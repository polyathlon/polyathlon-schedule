<?php

$poly_pid = 0;

if(isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])){
    $poly_action = 'edit';
    $poly_pid = (int)$_GET['id'];
}else if(isset($_GET['action']) && $_GET['action'] === 'create'){
    $poly_action = 'create';
}

?>

<div class="poly-schedule-header">

    <div class="poly-three-parts poly-fl">
        <a class='button-secondary schedule-button poly-glazzed-btn poly-glazzed-btn-dark' href="<?php echo "?page={$poly_adminPage}"; ?>">
            <div class='poly-icon poly-schedule-button-icon'><i class="fa fa-long-arrow-left"></i></div>
        </a>
    </div>

    <div class="poly-three-parts poly-fl poly-title-part">
        <input id="poly-schedule-name" class="poly-schedule-title" name="schedule-title" maxlength="250" placeholder="<?php echo __("Enter age group name ", "schedule")?>" type="text">
    </div>

    <div class="poly-three-parts poly-fr">
        <a id="poly-save-schedule-button" class='button-secondary schedule-button poly-glazzed-btn poly-glazzed-btn-green poly-fr' href="#">
            <div class='poly-icon poly-schedule-button-icon'><i class="fa fa-save fa-fw"></i></div>
        </a>
    </div>
</div>

<hr />

<div class="poly-empty-age-group-list-alert">
    <h3><?php echo __("You don't have age group in this age group name yet!", "schedule")?></h3>
</div>

<div class="poly-gallery-wrapper">
    <div class="poly-add-item-boxes">
        <div class="poly-add-item-box"><a id="poly-add-age-group-button" class="button-secondary poly-add-project-button poly-glazzed-btn poly-glazzed-btn-green" href='#' title="<?php echo __("Add new age group", "schedule")?>"> <?php echo __("+ Add age group", "schedule")?></a></div>
    </div>

    <table id="poly-gallery-project-list">
    </table>
</div>

<script>

var _POLY_LAST_GENERATED_INT_ID = 100000;

function poly_generateIntId(){
    return ++_POLY_LAST_GENERATED_ID;
}

//Show loading while the page is being complete loaded
poly_showSpinner();

//Configure javascript vars passed PHP
var poly_adminPage = "<?php echo $poly_adminPage ?>";
var poly_action = "<?php echo $poly_action ?>";

//Configure schedule model
var poly_schedule = {};
poly_schedule.id = "<?php echo $poly_pid ?>";
poly_schedule.age_groups = {};
poly_schedule.deletions = [];
poly_schedule.isDraft = true;


jQuery(".poly-empty-age-group-list-alert").show();

//Perform some actions when window is ready
jQuery(window).load(function () {
    //Setup sortable lists and grids
    jQuery('.sortable').sortable();
    jQuery('.handles').sortable({
//        handle: 'span'
    });

    jQuery("#poly-gallery-project-list").sortable({items: 'tr'});

    //In case of edit we should perform ajax call and retrieve the specified schedule for editing
    if(poly_action == 'edit'){
        poly_schedule = polyAjaxGetWithId(poly_schedule.id, 'poly_get_age_group_names');

        //NOTE: The validation and moderation is very important thing. Here could be not expected conversion
        //from PHP to Javascript JSON objects. So here we will validate, if needed we will do changes
        //to meet our needs
        poly_schedule = validatedSchedule(poly_schedule);
        //This schedule is already exists on server, so it's not draft item
        poly_schedule.isDraft = false;
    }

    jQuery('#poly-gallery-project-list').sortable().bind('sortupdate', function(e, ui) {
        //ui.item contains the current dragged element.
        //Triggered when the user stopped sorting and the DOM position has changed.
        poly_updateModel();
    });


    jQuery("#poly-save-schedule-button").on( 'click', function( evt ){
        evt.preventDefault();

        //Apply last changes to the model
        poly_updateModel();

        //Validate saving

        if(!poly_schedule.name){
            alert("<?php echo __("Oops! You're trying to save a age group name  without name.", "schedule")?>");
            return;
        }

        //Show spinner
        poly_showSpinner();

        //Perform Ajax calls
        poly_result = polyAjaxSave(poly_schedule, 'poly_save_age_group_names');

        //Get updated model from the server
        poly_schedule = polyAjaxGetWithId(poly_result['pid'], 'poly_get_age_group_names');
        poly_schedule = validatedSchedule(poly_schedule);
        poly_schedule.isDraft = false;

        poly_selectedProjectId = 0;

        //Update UI
        poly_updateUI();

        jQuery("#poly-gallery-project-list").scrollTop(0);

        //Hide spinner
        poly_hideSpinner();
    });

    jQuery("#poly-add-age-group-button").on( 'click', function( evt ){
        evt.preventDefault();
        poly_addProject();
    });

    jQuery(document).keypress(function(event) {
        //cmd+s or control+s
        if (event.which == 115 && (event.ctrlKey||event.metaKey)|| (event.which == 19)) {
            event.preventDefault();

            jQuery( "#poly-save-schedule-button" ).trigger( "click" );
            return false;
        }
        return true;
    });

    //Update UI based on retrieved/(just create) model
    poly_updateUI();

    //When the page is ready, hide loading spinner
    poly_hideSpinner();
});

function poly_addProject(){
    //Create new draft project
    var poly_age_group = {};
    poly_age_group.age_group_id = poly_generateIntId();
    poly_age_group.min_age = '';
    poly_age_group.max_age = '';
    poly_age_group.isDraft = true;

    poly_schedule.age_groups[poly_age_group.age_group_id] = poly_age_group;

    poly_addProjectItem(poly_age_group);

    jQuery(".poly-empty-age-group-list-alert").hide();
    jQuery("#poly-gallery-project-list").scrollTop(0);
}

function poly_addProjectItem(poly_age_group)
{
    var html = '';
    // '<div class="poly-content-box"><input type="text" placeholder="Enter min age value " name="age_group.name" value=""></div>' +
    html +=
        '<tr id="poly-gallery-project-' + poly_age_group.age_group_id + '" data-id="' + poly_age_group.age_group_id + '" class="poly-gallery-project">' +
            '<td class="poly-draggable"><i class="fa fa-reorder"></i></td>' +
            '<td class="poly-content">' +
                '<div class="poly-content-vertical">' +
                    '<div class="poly-content-vertical-box"><input type="text" placeholder="<?php echo __("Enter min age value", "schedule")?>" name="age_group.min_age" value=""></div>' +
                    '<div class="poly-content-vertical-box"><input type="text" placeholder="<?php echo __("Enter max age value", "schedule")?>" name="age_group.max_age" value=""></div>' +
                '</div>' +
            '</td>' +
            '<td class="poly-gallery-delete-proj"><i class="fa fa-trash-o" onclick="onDeleteProject(\'' + poly_age_group.age_group_id + '\')"></i></td>' +
        '</tr>';
    html = jQuery(html);

    jQuery("input[name='age_group.min_age']", html).val(poly_age_group.min_age);
    jQuery("input[name='age_group.max_age']", html).val(poly_age_group.max_age);

    jQuery("#poly-gallery-project-list").prepend(html);
}

function poly_updateUI(){
    if(poly_schedule.name){
        jQuery("#poly-schedule-name").val( poly_schedule.name );
    }

    jQuery("#poly-gallery-project-list").empty();
    if( poly_schedule.age_groups && Object.entries(poly_schedule.age_groups).length ){
        for (const [poly_age_groupIndex, age_group] of Object.entries(poly_schedule.age_groups)) {
            var cItem = poly_schedule.age_groups[poly_age_groupIndex];
            poly_addProjectItem(cItem);
        }
        jQuery(".poly-empty-age-group-list-alert").hide();
        // for(var poly_age_groupIndex = 0; poly_age_groupsIndex < poly_schedule.corder.length; poly_age_groupIndex++){

        //     var poly_projectId = poly_schedule.corder[poly_schedule.corder.length - poly_projectIndex-1];
        //     if(!poly_schedule.projects[poly_projectId]){
        //         continue;
        //     }
        //     var cItem = poly_schedule.projects[poly_projectId];
        //     cItem.name = PolyBase64.decode(cItem.name);
        //     poly_addProjectItem(cItem);

        //     jQuery(".poly-empty-age-group-list-alert").hide();
        // }
    }
}

function poly_updateModel(){
    //To make sure it's valid JS object
    poly_schedule = validatedSchedule(poly_schedule);

    poly_schedule.name = jQuery("#poly-schedule-name").val();

    jQuery(".poly-gallery-project").each(function(key, elem){
        elem = jQuery(elem);
        poly_selectedAgeGroupId = elem.attr('data-id');
        var poly_activeAgeGroup = poly_schedule.age_groups[poly_selectedAgeGroupId];
        poly_activeAgeGroup.min_age = jQuery("input[name='age_group.min_age']", elem).val();
        poly_activeAgeGroup.max_age = jQuery("input[name='age_group.max_age']", elem).val();
        poly_schedule.age_groups[poly_selectedAgeGroupId] = poly_activeAgeGroup;
    });
}

function validatedSchedule( schedule ){
    if (!schedule) {
      schedule = {};
    }

    if( !schedule.deletions || !(schedule.deletions && poly_isJSArray(schedule.deletions)) ){
            schedule.deletions = [];
    }

    return schedule;
}

function onDeleteProject( poly_childId ){
    if( !poly_childId ){
        return;
    }

    if( !confirm('Are you sure you want to delete?') ){
        return;
    }

    //Remove from projects assoc array and add in deletions list
    delete poly_schedule.age_groups[poly_childId];
    poly_schedule.deletions.push(poly_childId);

    // //Remove from ordered list
    // var crp_oi = poly_schedule.corder.indexOf(poly_childId);
    // if(crp_oi >= 0){
    //     poly_schedule.corder.splice(crp_oi,1);
    // }

    jQuery("#poly-gallery-project-"+poly_childId).remove();

    jQuery("#poly-save-schedule-button").click();
}

function htmlEntitiesEncode( str ){
    return jQuery('<div/>').text(str).html();
}

</script>
