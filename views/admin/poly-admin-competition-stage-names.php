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
        <a id="poly-button-secondary" class='button-secondary schedule-button poly-glazzed-btn poly-glazzed-btn-dark' href="<?php echo "?page={$poly_adminPage}"; ?>">
            <div class='poly-icon poly-schedule-button-icon'><i class="fa fa-long-arrow-left"></i></div>
        </a>
    </div>

    <div class="poly-three-parts poly-fl poly-title-part">
    <input id="poly-schedule-name" class="poly-schedule-title" name="schedule-title" maxlength="250" placeholder= "<?php echo __("Enter competition name", "schedule")?>" type="text"></div>

    <div class="poly-three-parts poly-fr">
        <a id="poly-save-schedule-button" class='button-secondary schedule-button poly-glazzed-btn poly-glazzed-btn-green poly-fr' href="#">
            <div class='poly-icon poly-schedule-button-icon'><i class="fa fa-save fa-fw"></i></div>
        </a>
    </div>
</div>

<hr />

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
poly_schedule.isDraft = true;


//Perform some actions when window is ready
jQuery(window).load(function () {


    //In case of edit we should perform ajax call and retrieve the specified schedule for editing
    if(poly_action == 'edit'){
        poly_schedule = polyAjaxGetWithId(poly_schedule.id, 'poly_get_competition_stage_names');

        //NOTE: The validation and moderation is very important thing. Here could be not expected conversion
        //from PHP to Javascript JSON objects. So here we will validate, if needed we will do changes
        //to meet our needs
        poly_schedule = validatedSchedule(poly_schedule);
        //This schedule is already exists on server, so it's not draft item
        poly_schedule.isDraft = false;
    }


    jQuery("#poly-save-schedule-button").on( 'click', function( evt ){
        evt.preventDefault();

        //Apply last changes to the model
        poly_updateModel();

        //Validate saving

        if(!poly_schedule.name){
            alert("<?php echo __("Oops! You're trying to save a competition stage name without name.", "schedule")?>");
            return;
        }

        //Show spinner
        poly_showSpinner();

        //Perform Ajax calls
        poly_result = polyAjaxSave(poly_schedule, 'poly_save_competition_stage_names');

        //Get updated model from the server
        poly_schedule = polyAjaxGetWithId(poly_result['pid'], 'poly_get_competition_stage_names');
        poly_schedule = validatedSchedule(poly_schedule);
        poly_schedule.isDraft = false;

        //Update UI
        poly_updateUI();

        //Hide spinner
        poly_hideSpinner();

        //Redirect to previous page
        jQuery( "#poly-button-secondary" )[0].click();
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
    if(poly_schedule.name){
        jQuery("#poly-schedule-name").val( poly_schedule.name );
    }

}

function poly_updateModel(){
    //To make sure it's valid JS object
    poly_schedule = validatedSchedule(poly_schedule);

    poly_schedule.name = jQuery("#poly-schedule-name").val();
}

function validatedSchedule(schedule){
    if (!schedule) {
      schedule = {};
    }
    return schedule;
}

function htmlEntitiesEncode(str){
    return jQuery('<div/>').text(str).html();
}

</script>
