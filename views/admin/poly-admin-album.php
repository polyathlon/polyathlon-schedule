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

    <div class="poly-three-parts poly-fl poly-title-part"><input id="poly-schedule-title" class="poly-schedule-title" name="schedule-title" maxlength="250" placeholder="Enter album title" type="text"></div>

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
    <h3>You don't have items in this album yet!</h3>
</div>

<div class="poly-project-wrapper">
    <aside class="poly-project-sidebar">
        <div>
            <a id="poly-add-project-button" class='button-secondary poly-add-project-button poly-glazzed-btn poly-glazzed-btn-green' href='#' title='Add new'>+ Add album</a>
        </div>

        <ul id="poly-project-list" class="poly-project-list handles list">
        </ul>
    </aside>
    <section class="poly-project-preview-wrapper">
        <div class="poly-project-details-wrapper">
            <aside class="poly-project-details-sidebar">
                <div id="poly-project-details-content">
                    <input id="poly-project-title" class="poly-project-title" name="project.title" value="" type="text" placeholder="Enter title">

                    <div id="poly-project-cover-img" class="poly-project-cover-img">
                        <div id="poly-project-cover-img-overlay">
                            <div id="poly-project-cover-img-overlay-content">
                                <div class='poly-icon poly-edit-icon poly-edit-project-cover-icon'> </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="poly-project-cover-src" name="project.cover" value="" />

                    <textarea id="poly-project-description" name="project.description" placeholder="Enter description..."></textarea>
                    <input id="poly-project-url" name="project.url" value="" type="text" placeholder="Enter URL">
                </div>
            </aside>
            <section class="poly-project-images-wrapper">
                <div class="poly-add-picture-button-wrapper">
                    <a id="poly-add-picture-button" class='button-secondary poly-add-picture-button poly-glazzed-btn poly-glazzed-btn-green' href='#' title='Add new'>+ Add picture</a>
                    <a id="poly-add-video-button" class='button-secondary poly-add-picture-button poly-glazzed-btn poly-glazzed-btn-green poly-tooltip' href='#' title='<?php echo htmlentities('<div class="poly-tooltip-content">Upgrade to Premium version for Local videos</div>'); ?>'>+ Video (PRO)</a>
                    <a id="poly-add-youtube-button" class='button-secondary poly-add-picture-button poly-glazzed-btn poly-glazzed-btn-green poly-tooltip' href='#' title='<?php echo htmlentities('<div class="poly-tooltip-content">Upgrade to Premium version for Youtube videos</div>'); ?>'>+ Youtube (PRO)</a>
                    <a id="poly-add-vimeo-button" class='button-secondary poly-add-picture-button poly-glazzed-btn poly-glazzed-btn-green poly-tooltip' href='#' title='<?php echo htmlentities('<div class="poly-tooltip-content">Upgrade to Premium version for Vimeo videos</div>'); ?>'>+ Vimeo (PRO)</a>
                    <a id="poly-add-iframe-button" class='button-secondary poly-add-picture-button poly-glazzed-btn poly-glazzed-btn-green poly-tooltip' href='#' title='<?php echo htmlentities('<div class="poly-tooltip-content">Upgrade to Premium version for iFrames</div>'); ?>'>+ Iframe (PRO)</a>
                    <a id="poly-add-map-button" class='button-secondary poly-add-picture-button poly-glazzed-btn poly-glazzed-btn-green poly-tooltip' href='#' title='<?php echo htmlentities('<div class="poly-tooltip-content">Upgrade to Premium version for Maps</div>'); ?>'>+ Map (PRO)</a>
                </div>

                <ul id="poly-project-images-grid" class="poly-project-images-grid sortable grid" style="overflow-y: auto">
                </ul>
            </section>
        </div>
    </section>
</div>

<script>

//Show loading while the page is being complete loaded
poly_showSpinner();

//Configure javascript vars passed PHP
var poly_adminPage = "<?php echo $poly_adminPage ?>";
var poly_action = "<?php echo $poly_action ?>";
var poly_selectedProjectId = 0;

var poly_categoryAutocompleteDS = [];

//Configure schedule model
var poly_schedule = {};
poly_schedule.id = "<?php echo $poly_pid ?>";
poly_schedule.projects = {};
poly_schedule.corder = [];
poly_schedule.deletions = [];
poly_schedule.isDraft = true;

jQuery(".poly-project-preview-wrapper").hide();
jQuery(".poly-empty-project-list-alert").show();

//Perform some actions when window is ready
jQuery(window).load(function () {
    //Setup sortable lists and grids
    jQuery('.sortable').sortable();
    jQuery('.handles').sortable({
//        handle: 'span'
    });
    jQuery('#poly-project-list').sortable().bind('sortupdate', function(e, ui) {
        //ui.item contains the current dragged element.
        //Triggered when the user stopped sorting and the DOM position has changed.
        poly_updateModel();
    });


    jQuery('#poly-project-categories').tagEditor({
        placeholder: "Enter comma separated categories",
    });

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

    jQuery( '#poly-project-cover-img' ).on( 'click', function( evt ) {
        // Stop the anchor's default behavior
        evt.preventDefault();

        // Display the media uploader
        poly_openMediaUploader( function callback(picInfo){
             changeProjectCover(picInfo);
        }, false );
    });

    jQuery("#poly-add-project-button").on( 'click', function( evt ){
        evt.preventDefault();

        //Keep all the changes
        poly_updateModel();

        //Create new draft project
        var poly_project = {};
        poly_project.id = poly_generateId();
        poly_project.isDraft = true;
        poly_project.categories = [];

        poly_schedule.projects[poly_project.id] = poly_project;
        poly_schedule.corder.unshift(poly_project.id);

        //Set it as selected
        poly_selectedProjectId = poly_project.id;

        //Update UI
        poly_updateUI();
        jQuery("#poly-project-list").scrollTop(0);
    });

    jQuery( "#poly-project-list" ).bind('click', function(event) {

        var poly_targetElement = null;
        if(jQuery(event.target).hasClass('poly-project-li')){
            poly_targetElement = event.target;
        }else if (jQuery(event.target).hasClass('poly-project-title-label')){
            poly_targetElement = jQuery(event.target).parent();
        }else{
            return;
        }

        var poly_projId = jQuery(poly_targetElement).attr('id');
        if(poly_projId != poly_selectedProjectId){
            poly_updateModel();

            poly_selectedProjectId = poly_projId;
            var _curScrollPos = jQuery("#poly-project-list").scrollTop();

            poly_updateUI();
            jQuery("#poly-project-list").scrollTop(_curScrollPos);
        }
    });


    jQuery("#poly-save-schedule-button").on( 'click', function( evt ){
        evt.preventDefault();

        //Apply last changes to the model
        poly_updateModel();

        //Validate saving
        var poly_activeProject = poly_schedule.projects[poly_selectedProjectId];
        if(!poly_schedule.title){
            alert("Oops! You're trying to save an album without title.");
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
            if(picInfoArr && picInfoArr.length > 0)

            for(var pi = 0; pi < picInfoArr.length; pi++){
                var picInfo = picInfoArr[pi];
                var poly_picId = poly_generateId();

                var innerHTML = "";
                innerHTML +=    "<li id='" + poly_picId + "' class = 'poly-pic-li'>";
                innerHTML +=        "<div id='poly-project-pic-" + poly_picId + "' class='poly-project-pic'>";
                innerHTML +=            "<div class='poly-project-pic-overlay'>";
                innerHTML +=                "<div class='poly-project-pic-overlay-content'>";
                innerHTML +=                    "<div class='poly-icon poly-trash-icon poly-trash-project-pic-icon' onClick='onDeleteProjectPic(\"" + poly_picId + "\")'> </div>";
                innerHTML +=                    "<div class='poly-icon poly-edit-icon poly-edit-project-pic-icon' onClick='onEditProjectPic(\"" + poly_picId + "\")'> </div>";
                innerHTML +=                "</div>";
                innerHTML +=            "</div>"
                innerHTML +=         "</div>"
                innerHTML +=         "<input type='hidden' id='poly-project-pic-src-" + poly_picId + "' value='' />";
                innerHTML +=    "</li>";

                jQuery("#poly-project-images-grid").append( innerHTML );
                changeProjectPic(poly_picId, picInfo);
            }
        }, true );

        jQuery("#poly-project-images-grid").scrollTop(0);
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

function poly_updateUI(){

    if(poly_schedule.title){
        jQuery("#poly-schedule-title").val( poly_schedule.title );
    }


    jQuery(".poly-project-preview-wrapper").hide();
    jQuery(".poly-empty-project-list-alert").show();

    jQuery("#poly-project-list").empty();
    if(poly_schedule.projects && poly_schedule.corder){
        for(var poly_projectIndex = 0; poly_projectIndex < poly_schedule.corder.length; poly_projectIndex++){

            var poly_projectId = poly_schedule.corder[poly_projectIndex];
            if(!poly_schedule.projects[poly_projectId]){
                continue;
            }

            var poly_project = poly_schedule.projects[poly_projectId];

            var proj_thumb = poly_project.cover ? JSON.parse(PolyBase64.decode(poly_project.cover)) : null;
            var emptyProjThumb = proj_thumb ? '' : 'height: 30px;';
            var thumb_img = '/general/glazzed-image-placeholder-thumb.png';
            proj_thumb = proj_thumb ? proj_thumb.src : POLY_IMAGES_URL + thumb_img;

            var innerHTML = "";
            innerHTML += "<li id='" + poly_project.id +"' class = 'poly-project-li'>";
            innerHTML +=    "<span class = 'draggable'>:: </span>";
            innerHTML +=    '<div class="poly-proj-thumb" style="background-image: url('+proj_thumb+');'+emptyProjThumb+'"></div>';
            innerHTML +=    "<span class = 'poly-project-title-label'>" + poly_truncateIfNeeded( poly_project.title ? PolyBase64.decode(poly_project.title) : 'Untitled' , 18) + "</span>";
            innerHTML +=    "<div class='poly-icon poly-trash-icon poly-trash-project-icon' onClick='onDeleteProject(\"" + poly_project.id + "\")'> </div>";
            innerHTML += "</li>";
            jQuery("#poly-project-list").append( innerHTML );

            if(!poly_selectedProjectId){
                poly_selectedProjectId = poly_project.id;
            }

            if(poly_project.id == poly_selectedProjectId){
                jQuery("#" + poly_project.id + ".poly-project-li").addClass('active-project-li');

                //Update current project details view
                jQuery("#poly-project-title").val( (poly_project.title ? PolyBase64.decode(poly_project.title) : '') );
                jQuery("#poly-project-description").val( PolyBase64.decode(poly_project.description) );
                jQuery("#poly-project-url").val( (poly_project.url ? poly_project.url : '') );

                changeProjectCover(poly_project.cover ? JSON.parse(PolyBase64.decode(poly_project.cover)) : null);

                jQuery("#poly-project-images-grid").empty();
                if(poly_project.pics){
                    poly_picInfoList = poly_project.pics.split(",");
                    for(var poly_picIndex=0; poly_picIndex<poly_picInfoList.length; poly_picIndex++){
                        if(!poly_picInfoList[poly_picIndex]) continue;

                        var poly_picId = poly_generateId();

                        var innerHTML = "";
                        innerHTML +=    "<li id='" + poly_picId + "' class = 'poly-pic-li'>";
                        innerHTML +=        "<div id='poly-project-pic-" + poly_picId + "' class='poly-project-pic'>";
                        innerHTML +=            "<div class='poly-project-pic-overlay'>";
                        innerHTML +=                "<div class='poly-project-pic-overlay-content'>";
                        innerHTML +=                    "<div class='poly-icon poly-trash-icon poly-trash-project-pic-icon' onClick='onDeleteProjectPic(\"" + poly_picId + "\")'> </div>";
                        innerHTML +=                    "<div class='poly-icon poly-edit-icon poly-edit-project-pic-icon' onClick='onEditProjectPic(\"" + poly_picId + "\")'> </div>";
                        innerHTML +=                "</div>";
                        innerHTML +=            "</div>"
                        innerHTML +=         "</div>"
                        innerHTML +=         "<input type='hidden' id='poly-project-pic-src-" + poly_picId + "' value='' />";
                        innerHTML +=    "</li>";

                        jQuery("#poly-project-images-grid").append( innerHTML );
                        changeProjectPic(poly_picId, JSON.parse(PolyBase64.decode(poly_picInfoList[poly_picIndex])));
                    }
                }

                jQuery("#poly-project-images-grid").scrollTop(0);

                jQuery(".poly-project-preview-wrapper").show();
                jQuery(".poly-empty-project-list-alert").hide();
            }
        }
    }
}

function poly_updateModel(){
  //To make sure it's valid JS object
    poly_schedule = validatedSchedule(poly_schedule);

    poly_schedule.title = jQuery("#poly-schedule-title").val();
    poly_schedule.corder = jQuery("#poly-project-list").sortable("toArray");

    if(poly_selectedProjectId){
        var poly_activeProject = poly_schedule.projects[poly_selectedProjectId];

        poly_activeProject.title = PolyBase64.encode(jQuery("#poly-project-title").val());
        poly_activeProject.cover = PolyBase64.encode(jQuery("#poly-project-cover-src").val());
        poly_activeProject.description = PolyBase64.encode(jQuery("#poly-project-description").val());
        poly_activeProject.url = jQuery("#poly-project-url").val();

        var poly_projectPics = "";
        var poly_picIDsList = jQuery("#poly-project-images-grid").sortable("toArray");
        for(var poly_picIndex = 0; poly_picIndex < poly_picIDsList.length; poly_picIndex++){
            var picInfo = jQuery("#poly-project-pic-src-" + poly_picIDsList[poly_picIndex]).val();
            if(picInfo){
                poly_projectPics += PolyBase64.encode(picInfo) + ",";
            }
        }
        if(poly_projectPics.length > 0){
            poly_projectPics = poly_projectPics.substr(0,poly_projectPics.length-1); //Remove last ','
        }
        poly_activeProject.pics = poly_projectPics;

        poly_schedule.projects[poly_selectedProjectId] = poly_activeProject;
    }
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

function onEditProjectPic(poly_picId){
    poly_openMediaUploader( function callback(picInfo){
         changeProjectPic(poly_picId, picInfo);
    }, false );
}

function onDeleteProjectPic(poly_picId){
    jQuery("#"+ poly_picId + ".poly-pic-li").remove();
}

function onDeleteProject(poly_projectId){
    if(!poly_projectId) return;

    if(!confirm('Are you sure you want to delete the item?')) {
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

    var _curScrollPos = jQuery("#poly-project-list").scrollTop() - 40;

    //Set it as selected
    if(poly_selectedProjectId == poly_projectId){
        poly_selectedProjectId = 0;
        _curScrollPos = 0;
    }

    poly_updateUI();
    jQuery("#poly-project-list").scrollTop(_curScrollPos);
}

function onScheduleOptions(){
    if(poly_schedule.isDraft){
        alert("Save the draft album before changing the view options");
    }else{
        var href = "?page=" + poly_adminPage + "&action=options&id=" + poly_schedule.id;
        poly_loadHref(href);
    }
}

function changeProjectCover(picInfo){
    var thumb_img = '/general/glazzed-image-placeholder.png';
    // After that, set the properties of the image and display it
    jQuery( '#poly-project-cover-img' )
            .css( 'background', 'url(' + (picInfo ? picInfo.src : POLY_IMAGES_URL + thumb_img) + ') center no-repeat' )
            .css( 'background-size', 'cover');

    // Store the image's information into the meta data fields
    jQuery( '#poly-project-cover-src' ).val( JSON.stringify(picInfo) );
}

function changeProjectPic(poly_picId, picInfo){
    var thumb_img = '/general/glazzed-image-placeholder.png';
    // After that, set the properties of the image and display it
    jQuery( '#poly-project-pic-' + poly_picId )
            .css( 'background', 'url(' + (picInfo ? picInfo.src : POLY_IMAGES_URL + thumb_img) + ') center no-repeat' )
            .css( 'background-size', 'cover');

    // Store the image's information into the meta data fields
    jQuery( '#poly-project-pic-src-' + poly_picId ).val( JSON.stringify(picInfo) );
}

function htmlEntitiesEncode(str){
    return jQuery('<div/>').text(str).html();
}

</script>
