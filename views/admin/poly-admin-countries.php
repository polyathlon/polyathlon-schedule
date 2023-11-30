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
        <input id="poly-schedule-name" class="poly-schedule-title" name="schedule-title" maxlength="250" placeholder= "<?php echo __("Enter country name", "schedule")?>" type="text">
    </div>

    <div class="poly-three-parts poly-fr">
        <a id="poly-save-schedule-button" class='button-secondary schedule-button poly-glazzed-btn poly-glazzed-btn-green poly-fr' href="#">
            <div class='poly-icon poly-schedule-button-icon'><i class="fa fa-save fa-fw"></i></div>
        </a>
    </div>
</div>

<hr />

<div class="poly-empty-city-list-alert">
    <h3><?php echo __("You don't have city in this country yet!", "schedule")?></h3>
</div>

<div class="poly-gallery-wrapper">
    <div class="poly-add-item-boxes">
        <div class="poly-add-item-box"><a id="poly-add-city-button" class='button-secondary poly-add-project-button poly-glazzed-btn poly-glazzed-btn-green' href='#' title= "<?php echo __("Add new city", "schedule")?>"><?php echo __("+ Add city", "schedule")?></a></div>
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

var poly_categoryAutocompleteDS = [];

//Configure schedule model
var poly_schedule = {};
poly_schedule.id = "<?php echo $poly_pid ?>";
poly_schedule.cities = {};
poly_schedule.deletions = [];
poly_schedule.isDraft = true;


jQuery(".poly-empty-city-list-alert").show();

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
        poly_schedule = polyAjaxGetWithId(poly_schedule.id, 'poly_get_countries');

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
            alert("<?php echo __("Oops! You're trying to save a country name without name.", "schedule")?>");
            return;
        }

        //Show spinner
        poly_showSpinner();

        //Perform Ajax calls
        poly_result = polyAjaxSave(poly_schedule, 'poly_save_countries');

        //Get updated model from the server
        poly_schedule = polyAjaxGetWithId(poly_result['pid'], 'poly_get_countries');
        poly_schedule = validatedSchedule(poly_schedule);
        poly_schedule.isDraft = false;

        poly_selectedProjectId = 0;

        //Update UI
        poly_updateUI();

        jQuery("#poly-gallery-project-list").scrollTop(0);

        //Hide spinner
        poly_hideSpinner();
    });

    jQuery("#poly-add-city-button").on( 'click', function( evt ){
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
    var poly_city = {};
    poly_city.city_id = poly_generateIntId();
    poly_city.name = '';
    poly_city.isDraft = true;

    poly_schedule.cities[poly_city.city_id] = poly_city;

    poly_addProjectItem(poly_city);

    jQuery(".poly-empty-city-list-alert").hide();
    jQuery("#poly-gallery-project-list").scrollTop(0);
}

function poly_addProjectItem(poly_city)
{
    var html = '';
    html +=
        '<tr id="poly-gallery-project-' + poly_city.city_id + '" data-id="' + poly_city.city_id + '" class="poly-gallery-project">' +
            '<td class="poly-draggable"><i class="fa fa-reorder"></i></td>' +
            '<td class="poly-content">' +
                '<div class="poly-content-box"><input type="text" placeholder= <?php echo __("Enter city name", "schedule")?> name="city.name" value=""></div>' +
            '</td>' +
            '<td class="poly-gallery-delete-proj"><i class="fa fa-trash-o" onclick="onDeleteProject(\'' + poly_city.city_id + '\')"></i></td>' +
        '</tr>';
    html = jQuery(html);

    jQuery("input[name='city.name']", html).val(poly_city.name);

    jQuery("#poly-gallery-project-list").prepend(html);
}

function poly_updateUI(){
    if(poly_schedule.name){
        jQuery("#poly-schedule-name").val( poly_schedule.name );
    }

    jQuery("#poly-gallery-project-list").empty();
    if( poly_schedule.cities && Object.entries(poly_schedule.cities).length ){
        for (const [poly_cityIndex, city] of Object.entries(poly_schedule.cities)) {
            var cItem = poly_schedule.cities[poly_cityIndex];
            poly_addProjectItem(cItem);
        }
        jQuery(".poly-empty-city-list-alert").hide();
        // for(var poly_cityIndex = 0; poly_citiesIndex < poly_schedule.corder.length; poly_cityIndex++){

        //     var poly_projectId = poly_schedule.corder[poly_schedule.corder.length - poly_projectIndex-1];
        //     if(!poly_schedule.projects[poly_projectId]){
        //         continue;
        //     }
        //     var cItem = poly_schedule.projects[poly_projectId];
        //     cItem.name = PolyBase64.decode(cItem.name);
        //     poly_addProjectItem(cItem);

        //     jQuery(".poly-empty-city-list-alert").hide();
        // }
    }
}

function poly_updateModel(){
    //To make sure it's valid JS object
    poly_schedule = validatedSchedule(poly_schedule);

    poly_schedule.name = jQuery("#poly-schedule-name").val();

    jQuery(".poly-gallery-project").each(function(key, elem){
        elem = jQuery(elem);
        poly_selectedCityId = elem.attr('data-id');
        var poly_activeCity = poly_schedule.cities[poly_selectedCityId];
        poly_activeCity.name = jQuery("input[name='city.name']", elem).val();
        poly_schedule.cities[poly_selectedCityId] = poly_activeCity;
    });
}

function validatedSchedule( schedule ){
    if( !schedule ){
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

    if( !confirm('<?php echo __('Are you sure you want to delete?', "schedule")?>') ){
        return;
    }

    //Remove from projects assoc array and add in deletions list
    delete poly_schedule.cities[poly_childId];
    poly_schedule.deletions.push(poly_childId);

    // //Remove from ordered list
    // var crp_oi = poly_schedule.corder.indexOf(poly_childId);
    // if(crp_oi >= 0){
    //     poly_schedule.corder.splice(crp_oi,1);
    // }

    jQuery("#poly-gallery-project-"+poly_childId).remove();
}

function htmlEntitiesEncode( str ){
    return jQuery('<div/>').text(str).html();
}

</script>
