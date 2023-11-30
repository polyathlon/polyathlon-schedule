<?php

$poly_pid = 0;

if(isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])){
    $poly_action = 'edit';
    $poly_pid = $_GET['id'];
}else if(isset($_GET['action']) && $_GET['action'] === 'create'){
    $poly_action = 'create';
}

global $poly_theme;

?>

<div class="poly-schedule-header">

    <div class="poly-three-parts poly-fl">
        <a class='button-secondary schedule-button poly-glazzed-btn poly-glazzed-btn-dark' href="<?php echo "?page={$poly_adminPage}"; ?>">
            <div class='poly-icon poly-schedule-button-icon'><i class="fa fa-long-arrow-left"></i></div>
        </a>
    </div>

    <div class="poly-three-parts poly-fl poly-title-part"><input id="poly-schedule-title" class="poly-schedule-title" name="schedule-title" maxlength="250" placeholder="Enter team name" type="text"></div>

    <div class="poly-three-parts poly-fr">
        <a id="poly-save-schedule-button" class='button-secondary schedule-button poly-glazzed-btn poly-glazzed-btn-green poly-fr' href="#">
            <div class='poly-icon poly-schedule-button-icon'><i class="fa fa-save fa-fw"></i></div>
        </a>
        <a id="poly-schedule-options-button" class='button-secondary schedule-button poly-glazzed-btn poly-glazzed-btn-orange poly-fr' href="#" onclick="onScheduleOptions()">
            <div class='poly-icon poly-schedule-button-icon'><i class="fa fa-cog fa-fw"></i></div>
        </a>
    </div>
</div>

<hr />

<div class="poly-empty-project-list-alert">
    <h3>You don't have members in this team yet!</h3>
</div>

<div class="poly-gallery-wrapper poly-team-wrapper">
    <div class="poly-add-item-boxes">
        <div class="poly-add-item-box"><a id="poly-add-picture-button" class='button-secondary poly-add-project-button poly-glazzed-btn poly-glazzed-btn-green' href='#' title='Add new member'>+ Add member</a></div>
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
    var poly_attachmentTypePicture = 'pic';

    //Configure schedule model
    var poly_schedule = {};
    poly_schedule.id = "<?php echo $poly_pid ?>";
    poly_schedule.projects = {};
    poly_schedule.corder = [];
    poly_schedule.deletions = [];
    poly_schedule.isDraft = true;
    poly_schedule.all_cats = [];

    jQuery(".poly-empty-project-list-alert").show();

    //Perform some actions when window is ready
    jQuery(window).load(function () {
        //Setup sortable lists and grids
        jQuery('.sortable').sortable();
        jQuery('.handles').sortable({
//        handle: 'span'
        });
        jQuery("#poly-gallery-project-list").sortable({items: 'tr'});


        //In case of edit we sould perform ajax call and retrieve the specified schedule for editing
        if(poly_action == 'edit'){
            poly_schedule = polyAjaxGetScheduleWithId(poly_schedule.id);
            //NOTE: The validation and moderation is very important thing. Here could be not expected conversion
            //from PHP to Javascript JSON objects. So here we will validate, if needed we will do changes
            //to meet our needs
            poly_schedule = validatedSchedule(poly_schedule);
            //This schedule is already exists on server, so it's not draft item
            poly_schedule.isDraft = false;
        }
        jQuery('#poly-project-list').sortable().bind('sortupdate', function(e, ui) {
            //ui.item contains the current dragged element.
            //Triggered when the user stopped sorting and the DOM position has changed.
            poly_updateModel();
        });

        jQuery("#poly-save-schedule-button").on( 'click', function( evt ){
            evt.preventDefault();

            //Apply last changes to the model
            poly_updateModel();

            //Validate saving
            if(!poly_schedule.title){
                alert("Oops! You're trying to save a team without name.");
                return;
            }

            //Show spinner
            poly_showSpinner();

            //Perform Ajax calls
            poly_result = polyAjaxSaveSchedule(poly_schedule);

            //Get updated model from the server
            poly_schedule = polyAjaxGetScheduleWithId(poly_result['pid']);
            poly_schedule = validatedSchedule(poly_schedule);
            poly_schedule.isDraft = false;

            poly_selectedProjectId = 0;

            //Update UI
            poly_updateUI();
            jQuery("#poly-project-list").scrollTop(0);

            //Hide spinner
            poly_hideSpinner();
        });


        jQuery("#poly-add-picture-button").on( 'click', function( evt ){
            evt.preventDefault();

            poly_openMediaUploader( function callback(picInfoArr){
                if(picInfoArr && picInfoArr.length > 0) {
                    for (var pi = 0; pi < picInfoArr.length; pi++) {
                        poly_addProject(picInfoArr[pi]);
                    }
                }
            }, true );
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

    function poly_addProject(picInfo){

        //Create new draft project
        var poly_project = {};
        poly_project.id = poly_generateIntId();
        poly_project.title = '';
        poly_project.description = '';
        poly_project.url = '';
        poly_project.isDraft = true;
        poly_project.categories = [];
        poly_project.cover = picInfo;

        poly_schedule.projects[poly_project.id] = poly_project;
        poly_schedule.corder.unshift(poly_project.id);

        poly_addProjectItem(poly_project);
        jQuery(".poly-empty-project-list-alert").hide();
        jQuery("#poly-gallery-project-list").scrollTop(0);
    }

    function poly_addProjectItem(poly_project )
    {
        var html = '';

        html +=
            '<tr id="poly-gallery-project-' + poly_project.id + '" data-id="' + poly_project.id + '" class="poly-gallery-project">' +
            '<td class="poly-draggable"><i class="fa fa-reorder"></i></td>' +
            '<td class="poly-attachment">' +
            '<div>' +
            '<div class="poly-attachment-img">' +
            '<div class="poly-attachment-img-overlay" onclick="poly_onProjectEdit(\'' + poly_project.id + '\')"><i class="fa fa-pencil"></i></div>' +
            '</div>' +
            '<input type="hidden" class="poly-project-cover-src" name="project.cover" value="" />' +
            '</div>' +
            '</td>' +
            '<td class="poly-content">' +
            '<div class="poly-content-box"><input type="text" placeholder="Enter full name" name="project.title" value=""></div>' +
            '<div class="poly-content-box"><textarea rows=3 placeholder="Enter info" name="project.description"></textarea></div>' +
            '<div class="poly-content-box"><input type="text" disabled="disabled" placeholder="Enter facebook link (PRO)" name="project.fb_link" value=""></div>' +
            '<div class="poly-content-box"><input type="text" disabled="disabled" placeholder="Enter linkedin link (PRO)" name="project.ln_link" value=""></div>' +
            '<div class="poly-content-box"><input type="text" placeholder="Enter custom link (http://example.com)" name="project.url" value=""></div>' +
            '</td>' +
            '<td class="poly-gallery-delete-proj"><i class="fa fa-trash-o" onclick="onDeleteProject(\'' + poly_project.id + '\')"></i></td>' +
            '</tr>';
        html = jQuery(html);
        jQuery("input[name='project.title']", html).val(poly_project.title);
        jQuery("textarea[name='project.description']", html).val(poly_project.description);
        jQuery("input[name='project.cover']", html).val(poly_project.cover);
        jQuery("input[name='project.url']", html).val(poly_project.url);
        jQuery("#poly-gallery-project-list").prepend(html);
        poly_changeProjectCover(poly_project.id, poly_project.cover);
    }

    function poly_changeProjectCover(projectId, picInfo) {
        var thumb_img = "<?php echo ($poly_theme == 'dark') ? '/general/glazzed-image-placeholder_dark.png' : '/general/glazzed-image-placeholder.png'; ?>";

        if(picInfo) {
            picInfo.type = poly_attachmentTypePicture;
        }
        var bgImage = (picInfo ? picInfo.src : POLY_IMAGES_URL + thumb_img);

        jQuery("#poly-gallery-project-"+projectId+" .poly-project-cover-src").val(JSON.stringify(picInfo));
        jQuery("#poly-gallery-project-"+projectId+" .poly-attachment-img").css('background', 'url('+bgImage+') center center / cover no-repeat');
    }

    function poly_onProjectEdit(projectId) {
        poly_openMediaUploader(function callback(picInfo) {
            poly_changeProjectCover(projectId, picInfo);
        }, false);
    }

    function poly_updateUI(){

        if(poly_schedule.title){
            jQuery("#poly-schedule-title").val( poly_schedule.title );
        }

        jQuery("#poly-gallery-project-list").empty();
        if(poly_schedule.projects && poly_schedule.corder){
            for(var poly_projectIndex = 0; poly_projectIndex < poly_schedule.corder.length; poly_projectIndex++){

                var poly_projectId = poly_schedule.corder[poly_schedule.corder.length - poly_projectIndex-1];
                if(!poly_schedule.projects[poly_projectId]){
                    continue;
                }
                var cItem = poly_schedule.projects[poly_projectId];
                cItem.title = PolyBase64.decode(cItem.title);
                cItem.description = PolyBase64.decode(cItem.description);
                cItem.cover = cItem.cover ? JSON.parse(PolyBase64.decode(cItem.cover)) : null;
                poly_addProjectItem(cItem);

                jQuery(".poly-empty-project-list-alert").hide();
            }
        }

    }

    function poly_updateModel(){
        //To make sure it's valid JS object
        poly_schedule = validatedSchedule(poly_schedule);

        poly_schedule.title = jQuery("#poly-schedule-title").val();
        poly_schedule.corder = jQuery("#poly-gallery-project-list").sortable("toArray", {attribute: 'data-id'});
        poly_schedule.extoptions = {
            all_cats: {},
            type: '<?php echo POLYGridType::TEAM; ?>'
        };

        jQuery(".poly-gallery-project").each(function(key, elem){
            elem = jQuery(elem);
            poly_selectedProjectId = elem.attr('data-id');
            var poly_activeProject = poly_schedule.projects[poly_selectedProjectId];

            poly_activeProject.title = PolyBase64.encode(jQuery("input[name='project.title']", elem).val());
            poly_activeProject.description = PolyBase64.encode(jQuery("textarea[name='project.description']", elem).val());
            poly_activeProject.cover = PolyBase64.encode(jQuery("input[name='project.cover']", elem).val());
            poly_activeProject.url = jQuery("input[name='project.url']", elem).val();
            poly_activeProject.pics = poly_activeProject.cover;

            poly_schedule.projects[poly_selectedProjectId] = poly_activeProject;
        });
    }

    function validatedSchedule(schedule){
        if (!schedule) {
            schedule = {};
        }

        //NOTE: We use assoc array for projects, so if it's null/undefined or Array,
        //then we should change it as an Object to treat it as an assoc array
        if(!schedule.projects || (schedule.projects && poly_isJSArray(schedule.projects))){
            schedule.projects = {};
        }

        if(!schedule.deletions || !(schedule.deletions && poly_isJSArray(schedule.deletions))){
            schedule.deletions = [];
        }

        return schedule;
    }

    function onDeleteProject(poly_projectId){
        if(!poly_projectId) return;

        if(!confirm('Are you sure you want to delete?')) {
            return;
        }

        //Remove from projects assoc array and add in deletions list
        delete poly_schedule.projects[poly_projectId];
        poly_schedule.deletions.push(poly_projectId);

        //Remove from ordered list
        var poly_oi = poly_schedule.corder.indexOf(poly_projectId);
        if(poly_oi >= 0){
            poly_schedule.corder.splice(poly_oi,1);
        }

        jQuery("#poly-gallery-project-"+poly_projectId).remove();

    }

    function onScheduleOptions() {
        if (poly_schedule.isDraft) {
            alert("Save the draft team before changing the view options");
        } else {
            var href = "?page=" + poly_adminPage + "&action=options&id=" + poly_schedule.id+'&type=<?php echo POLYGridType::TEAM; ?>';
            poly_loadHref(href);
        }
    }
</script>
