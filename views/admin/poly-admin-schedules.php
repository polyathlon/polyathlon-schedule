<?php

$poly_pid = 0;

if(isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])){
    $poly_action = 'edit';
    $poly_pid = (int)$_GET['id'];
}else if(isset($_GET['action']) && $_GET['action'] === 'create'){
    $poly_action = 'create';
}

global $poly_theme;
?>

<div class="poly-schedule-header">

    <div class="poly-three-parts poly-fl">
        <a id="poly-button-secondary" class='button-secondary schedule-button poly-glazzed-btn poly-glazzed-btn-dark' href="<?php echo "?page={$poly_adminPage}"; ?>">
            <div class='poly-icon poly-schedule-button-icon'><i class="fa fa-long-arrow-left"></i></div>
        </a>
    </div>

    <div class="poly-three-parts poly-fl poly-title-part">
        <input id="poly-schedule-name" class="poly-schedule-title" name="schedule-title" maxlength="250" placeholder= "<?php echo __("Enter schedule name", "schedule")?>" type="text">
    </div>

    <div class="poly-three-parts poly-fr">
        <a id="poly-save-schedule-button" class='button-secondary schedule-button poly-glazzed-btn poly-glazzed-btn-green poly-fr' href="#">
            <div class='poly-icon poly-schedule-button-icon'><i class="fa fa-save fa-fw"></i></div>
        </a>
    </div>
</div>

<hr />

<div class="poly-empty-schedule-item-list-alert">
    <h3><?php echo __("You don't have item in this schedule yet!", "schedule")?></h3>
</div>

<div class="poly-gallery-wrapper">
    <div class="poly-add-item-boxes">
        <div class="poly-add-item-box"><a id="poly-add-schedule-item-button" class='button-secondary poly-add-project-button poly-glazzed-btn poly-glazzed-btn-green' href='#' title="<?php echo __("Add new schedule item", "schedule")?>"><?php echo __("+ Add schedule item", "schedule")?></a></div>
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

//Co$poly_pidnfigure javascript vars passed PHP
var poly_adminPage = "<?php echo $poly_adminPage ?>";
var poly_action = "<?php echo $poly_action ?>";

var poly_categoryAutocompleteDS = [];

var poly_attachmentTypePicture = 'pic';

//Configure schedule model
var poly_schedule = {};
poly_schedule.id = "<?php echo $poly_pid ?>";
poly_schedule.schedule_items = {};
poly_schedule.competition_names = {};
poly_schedule.deletions = [];
poly_schedule.isDraft = true;

const ageMenNames = [
    "Мальчики: 11-12 лет",
    "Мальчики: 12-13 лет",
    "Юноши: 14-15 лет",
    "Юноши: 16-17 лет",
    "Юниоры: 18-20 лет",
    "Юниоры: 21-23 года",
    "Юниоры: 17-25 лет",
    "Мужчины",
    "Ветераны: 40-80 лет"
];

const ageWomenNames = [
    "Девочки: 11-12 лет",
    "Девочки: 12-13 лет",
    "Девушки: 14-15 лет",
    "Девушки: 16-17 лет",
    "Юниорки: 18-20 лет",
    "Юниорки: 21-23 года",
    "Юниорки: 17-25 лет",
    "Женщины",
    "Ветераны: 40-80 лет"
];

const ageMenWomenNames = [
    "Мальчики, девочки: 11-12 лет",
    "Мальчики, девочки: 12-13 лет",
    "Юноши, девушки: 14-15 лет",
    "Юноши, девушки: 16-17 лет",
    "Юниоры, юниорки: 18-20 лет",
    "Юниоры, юниорки: 21-23 года",
    "Юниоры, юниорки: 17-25 лет",
    "Мужчины, женщины",
    "Ветераны: 40-80 лет"
];
jQuery(".poly-empty-schedule_item-list-alert").show();

//Perform some actions when window is ready
jQuery(window).load(function () {
   //Setup sortable lists and grids
    // jQuery('.sortable').sortable();
    // jQuery('.handles').sortable({
    // // handle: 'span'
    // });

    // jQuery("#poly-gallery-project-list").sortable({items: 'tr'});


    //In case of edit we should perform ajax call and retrieve the specified schedule for editing
    if(poly_action == 'edit'){
        poly_schedule = polyAjaxGetWithId(poly_schedule.id, 'poly_get_schedules');

        //NOTE: The validation and moderation is very important thing. Here could be not expected conversion
        //from PHP to Javascript JSON objects. So here we will validate, if needed we will do changes
        //to meet our needs
        poly_schedule = validatedSchedule(poly_schedule);
        //This schedule is already exists on server, so it's not draft item
        poly_schedule.isDraft = false;
    }

 //   poly_competition_names = presiAjaxGet('poly_get_competition_name_list');
    //poly_createCompetitionNameList()
    // presi_createPositionList();

    // jQuery('#poly-gallery-project-list').sortable().bind('sortupdate', function(e, ui) {
    //     //ui.item contains the current dragged element.
    //     //Triggered when the user stopped sorting and the DOM position has changed.
    //     poly_updateModel();
    // });

    jQuery("#poly-save-schedule-button").on( 'click', function( evt ){
        evt.preventDefault();

        //Apply last changes to the model
        poly_updateModel();

        //Validate saving

        if(!poly_schedule.name){
            alert("<?php echo __("Oops! You're trying to save a schedule name without name.", "schedule")?>");
            return;
        }

        //Show spinner
        poly_showSpinner();

        //Perform Ajax calls
        poly_result = polyAjaxSave(poly_schedule, 'poly_save_schedules');

        //Get updated model from the server
        poly_schedule = polyAjaxGetWithId(poly_result['pid'], 'poly_get_schedules');
        poly_schedule = validatedSchedule(poly_schedule);
        poly_schedule.isDraft = false;

        poly_selectedProjectId = 0;

        //Update UI
        poly_updateUI();

        jQuery("#poly-gallery-project-list").scrollTop(0);

        //Hide spinner
        poly_hideSpinner();

        // //Redirect to previous page
        // jQuery( "#poly-button-secondary" )[0].click();
    });

    jQuery("#poly-add-schedule-item-button").on( 'click', function( evt ){
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
    var poly_schedule_item = {};
    poly_schedule_item.schedule_item_id = poly_generateIntId();
    poly_schedule_item.number = '';
    poly_schedule_item.number_ekp = '';
    poly_schedule_item.competition_name_id = 0;
    poly_schedule_item.disciplines = '';

    poly_schedule_item.start_date = '';
    poly_schedule_item.end_date = '';

    poly_schedule_item.isDraft = true;

    poly_schedule.schedule_items[poly_schedule_item.schedule_item_id] = poly_schedule_item;

    poly_addProjectItem(poly_schedule_item);

    jQuery(".poly-empty-schedule-item-list-alert").hide();
    jQuery("#poly-gallery-project-list").scrollTop(0);
}

function checkAgeGroups(ageGroup) {
    const ageMenCheck = Array(ageMenNames.length).fill(false);
    const ageWomenCheck = Array(ageWomenNames.length).fill(false);
    if (ageGroup) {
        for (let index = 0; index < ageMenNames.length; index++) {
            if (ageGroup.includes(ageMenWomenNames[index])) {
                ageMenCheck[index] = ageWomenCheck[index] = true
            }
            else if (ageGroup.includes(ageMenNames[index])) {
                ageMenCheck[index] = true
            }
            else if (ageGroup.includes(ageWomenNames[index])) {

                ageWomenCheck[index] = true
            }
            else {
                ageMenCheck[index] = false
                ageWomenCheck[index] = false
            }
        }
    }
    return {ageMenCheck, ageWomenCheck}
}


function AgeGroupChange(itemId) {
    const ageGroups = [];
    ageMenNames.forEach((ageGroup, index) => {
        const menCheckBox = window[`men-age-group-${itemId}-${index}`]
        const womenCheckBox = window[`women-age-group-${itemId}-${index}`]
        if (menCheckBox.checked && womenCheckBox.checked) {
            ageGroups.push(ageMenWomenNames[index])
        }
        else if (menCheckBox.checked)
            ageGroups.push(ageMenNames[index])
        else if (womenCheckBox.checked)
        ageGroups.push(ageWomenNames[index])
    })
    const ageGroupsInput = window[`age-groups-${itemId}`];
    ageGroupsInput.value = JSON.stringify(ageGroups);
}

function checkDisciplineNames(disciplineNames) {
    const disciplineNameCheck = [];
    if (disciplineNames) {
        disciplineNames = disciplineNames.split(', ');
        for( const [disciplineIndex, disciplineName] of Object.entries(poly_schedule.discipline_names) ){
            disciplineNameCheck[disciplineIndex] = disciplineNames.some( item =>
                item.includes(disciplineName.name.toLowerCase())
            )
        }
    }
    return disciplineNameCheck;
}

function disciplineNameChange(itemId) {
    const disciplineNames = [];
    for( const [disciplineIndex, disciplineName] of Object.entries(poly_schedule.discipline_names) ){
        const disciplineNameCheckBox = window[`discipline-name-${itemId}-${disciplineIndex}`];
        if (disciplineNameCheckBox.checked) {
            disciplineNames.push(disciplineName.name.toLowerCase())
        }
    }
    const disciplineInput = window[`disciplines-${itemId}`];
    disciplineInput.value = disciplineNames.join(', ');
}

function poly_addProjectItem(poly_schedule_item)
{
    var html = '';
    html +=
        `<tr id="poly-gallery-project-${poly_schedule_item.schedule_item_id}" data-id="${poly_schedule_item.schedule_item_id}" class="poly-gallery-project">
            <td class="poly-draggable"><i class="fa fa-reorder"></i></td>
            <td class="poly-attachment">
                <div>
                    <div class="poly-attachment-img">
                        <div class="poly-attachment-img-overlay" onclick="poly_onImageEdit(${poly_schedule_item.schedule_item_id})"><i class="fa fa-pencil"></i>
                        </div>
                    </div>
                    <input type="hidden" class="poly-project-cover-src" name="poly-schedule-item.image" value="" />
                </div>
            </td>
            <td class="poly-content">
                <div id="poly-competition-name-${poly_schedule_item.schedule_item_id}" class="poly-content-box select-box">
                    <div class="select-box-current" tabindex="1">
                    <div class="select-box-value">
                        <input class="select-box-input" type="radio" id="competition-name-${poly_schedule_item.schedule_item_id }-0" value="0" name="competition-name-${poly_schedule_item.schedule_item_id}" checked="checked">
                        <p class="select-box-input-text" placeholder>Enter competition name</p>
                    </div>
                    <img class="select-box-icon" src="https://cdn.onlinewebfonts.com/svg/img_295694.svg" alt="Arrow Icon" aria-hidden="true">`;
                    if( poly_schedule.competition_names && Object.entries(poly_schedule.competition_names).length ){
                        for( const [competitionNameIndex, competitionName] of Object.entries(poly_schedule.competition_names) ){
                            html += `<div class="select-box-value">
                                        <input class="select-box-input" type="radio" id="competition-name-${poly_schedule_item.schedule_item_id}-${competitionName.competition_name_id}" value="${competitionName.competition_name_id}" name="competition-name-${poly_schedule_item.schedule_item_id}">
                                        <p class="select-box-input-text">${competitionName.name}</p>
                                    </div>`;
                        }
                    }
                html+=`</div>
                <ul class="select-box-list">`;
                    if( poly_schedule.competition_names && Object.entries(poly_schedule.competition_names).length ){
                        for( const [competitionNameIndex, competitionName] of Object.entries(poly_schedule.competition_names) ){
                            html += `<li>
                                        <label class="select-box-option" for="competition-name-${poly_schedule_item.schedule_item_id}-${competitionName.competition_name_id}" aria-hidden="aria-hidden">${competitionName.name}</label>
                                    </li>`
                        }
                    }
                html += `</ui>
                </div>
                <div class="poly-content-box"><input type="text" placeholder="Enter number schedule item" name="poly-schedule-item.number" value=""></div>
                <div class="poly-content-box"><input type="text" placeholder="Enter number ekp schedule item" name="poly-schedule-item.number_ekp" value=""></div>
                `;
                if( poly_schedule.discipline_names && Object.entries(poly_schedule.discipline_names).length ){
                    html += `
                        <fieldset class="discipline-names-fieldset">
                            <legend>Спортивные дисциплины:</legend>
                    `;
                    const checkDisciplineName = checkDisciplineNames(poly_schedule_item.disciplines);
                    for( const [disciplineIndex, disciplineName] of Object.entries(poly_schedule.discipline_names) ){
                        html += `
                            <div class="poly-content-box discipline-name">
                                <input type="checkbox" id="discipline-name-${poly_schedule_item.schedule_item_id}-${disciplineIndex}" name="discipline-name-${poly_schedule_item.schedule_item_id}-${disciplineIndex}"${checkDisciplineName[disciplineIndex] ? " checked" : ""} onclick="disciplineNameChange(${poly_schedule_item.schedule_item_id})">
                                <label for="discipline-name-${poly_schedule_item.schedule_item_id}-${disciplineIndex}">${disciplineName.name}</label>
                            </div>
                        `;
                    }
                    html += `
                        </fieldset>
                        <div class="poly-content-box"><input type="text" placeholder="Enter disciplines schedule item" name="poly-schedule-item.disciplines" id="disciplines-${poly_schedule_item.schedule_item_id}" value=""></div>
                    `;
                }
                html += `

                <div id="poly-stage-${poly_schedule_item.schedule_item_id}" class="poly-content-box select-box">
                    <div class="select-box-current" tabindex="1">
                        <div class="select-box-value">
                            <input class="select-box-input" type="radio" id="stage-${poly_schedule_item.schedule_item_id }-0" value name="stage-${poly_schedule_item.schedule_item_id}" checked="checked">
                            <p class="select-box-input-text" placeholder>Enter stage</p>
                        </div>
                        <img class="select-box-icon" src="https://cdn.onlinewebfonts.com/svg/img_295694.svg" alt="Arrow Icon" aria-hidden="true">`;
                        if( poly_schedule.stages && Object.entries(poly_schedule.stages).length ){
                            for( const [stageIndex, stageName] of Object.entries(poly_schedule.stages) ){
                                html += `<div class="select-box-value">
                                            <input class="select-box-input" type="radio" id="stage-${poly_schedule_item.schedule_item_id}-${stageName.stage_id}" value="${stageName.stage_id}" name="stage-${poly_schedule_item.schedule_item_id}">
                                            <p class="select-box-input-text">${stageName.name}</p>
                                        </div>`;
                            }
                        }
                    html+=`</div>
                        <ul class="select-box-list">`;
                        if( poly_schedule.stages && Object.entries(poly_schedule.stages).length ){
                            for( const [stageIndex, stageName] of Object.entries(poly_schedule.stages) ){
                                html += `<li>
                                            <label class="select-box-option" for="stage-${poly_schedule_item.schedule_item_id}-${stageName.stage_id}" aria-hidden="aria-hidden">${stageName.name}</label>
                                        </li>`;
                            }
                        }
                    html += `</ui>
                </div>
                <fieldset class="age-groups-fieldset">
                    <legend>Возрастные группы:</legend>`;
                    const checkAgeGroup = checkAgeGroups(poly_schedule_item.age_groups);
                    for( const i in ageMenNames ){
                        html += `
                            <div class="poly-content-box age-group-men">
                            <input type="checkbox" id="men-age-group-${poly_schedule_item.schedule_item_id}-${i}" name="men-age-group-${poly_schedule_item.schedule_item_id}-${i}"${checkAgeGroup.ageMenCheck[i] ? " checked" : ""} onclick="AgeGroupChange(${poly_schedule_item.schedule_item_id})">
                                <label for="men-age-group-${poly_schedule_item.schedule_item_id}-${i}">${ageMenNames[i]}</label>
                            </div>
                            <div class="poly-content-box age-group-women">
                            <input type="checkbox" id="women-age-group-${poly_schedule_item.schedule_item_id}-${i}" name="women-age-group-${poly_schedule_item.schedule_item_id}-${i}"${checkAgeGroup.ageWomenCheck[i] ? " checked" : ""} onclick="AgeGroupChange(${poly_schedule_item.schedule_item_id})">
                            <label for="women-age-group-${poly_schedule_item.schedule_item_id}-${i}">${ageWomenNames[i]}</label>
                            </div>
                        `;
                    }
                html += `</div>
                </fieldset>
                <div class="poly-content-box"><input type="text" placeholder="Enter age groups schedule item" name="poly-schedule-item.age-groups" id="age-groups-${poly_schedule_item.schedule_item_id}" value=""></div>
                <div class="poly-content-box"><input type="date" min="2023-01-01" name="poly-schedule-item.start-date" value=""></div>
                <div class="poly-content-box"><input type="date" min="2023-01-01" name="poly-schedule-item.end-date" value=""></div>
                <div id="poly-city-${poly_schedule_item.schedule_item_id}" class="poly-content-box select-box">
                    <div class="select-box-current" tabindex="1">
                        <div class="select-box-value">
                            <input class="select-box-input" type="radio" id="city-${poly_schedule_item.schedule_item_id }-0" value="0" name="city-${poly_schedule_item.schedule_item_id}" checked="checked">
                            <p class="select-box-input-text" placeholder>Enter city name</p>
                        </div>
                        <img class="select-box-icon" src="https://cdn.onlinewebfonts.com/svg/img_295694.svg" alt="Arrow Icon" aria-hidden="true">`;
                        if( poly_schedule.cities && Object.entries(poly_schedule.cities).length ){
                            for( const [cityIndex, cityName] of Object.entries(poly_schedule.cities) ){
                                html += `<div class="select-box-value">
                                            <input class="select-box-input" type="radio" id="city-${poly_schedule_item.schedule_item_id}-${cityName.city_id}" value="${cityName.city_id}" name="city-${poly_schedule_item.schedule_item_id}">
                                            <p class="select-box-input-text">${cityName.name}</p>
                                        </div>`;
                            }
                        }
                    html += `</div>
                    <ul class="select-box-list">`;
                    if( poly_schedule.cities && Object.entries(poly_schedule.cities).length ){
                        for( const [cityIndex, cityName] of Object.entries(poly_schedule.cities) ){
                            html += `<li>
                                        <label class="select-box-option" for="city-${poly_schedule_item.schedule_item_id}-${cityName.city_id}" aria-hidden="aria-hidden">${cityName.name}, ${cityName.country_name}</label>
                                    </li>`;
                        }
                    }
                    html += `</ui>
                </div>
                <div class="poly-content-box"><input type="url" placeholder="Enter link to regulations" name="poly-schedule-item.regulations" value=""></div>
                <div class="poly-content-box"><input type="url" placeholder="Enter link to protocol" name="poly-schedule-item.protocol" value=""></div>
                <div class="poly-content-box"><input type="url" placeholder="Enter link to details" name="poly-schedule-item.details" value=""></div>
            </td>
            <td class="poly-gallery-delete-proj"><i class="fa fa-trash-o" onclick="onDeleteProject(${poly_schedule_item.schedule_item_id})"></i></td>
        </tr>`;
    html = jQuery(html);

    // jQuery("input[name='poly-schedule-item.competition-name-id']", html).val(poly_schedule_item.competition_name_id);
    jQuery("input[name='poly-schedule-item.number']", html).val(poly_schedule_item.number);
    jQuery("input[name='poly-schedule-item.number_ekp']", html).val(poly_schedule_item.number_ekp);
    jQuery("input[name='poly-schedule-item.disciplines']", html).val(poly_schedule_item.disciplines);
    jQuery("input[name='poly-schedule-item.stage']", html).val(poly_schedule_item.stage);
    jQuery("input[name='poly-schedule-item.age-groups']", html).val(poly_schedule_item.age_groups);
    jQuery("input[name='poly-schedule-item.start-date']", html).val(poly_schedule_item.start_date);
    jQuery("input[name='poly-schedule-item.end-date']", html).val(poly_schedule_item.end_date);
    jQuery("input[name='poly-schedule-item.regulations']", html).val(poly_schedule_item.regulations);
    jQuery("input[name='poly-schedule-item.protocol']", html).val(poly_schedule_item.protocol);
    jQuery("input[name='poly-schedule-item.details']", html).val(poly_schedule_item.details);
    jQuery("#poly-gallery-project-list").prepend(html);
}

function poly_onImageEdit(imageId) {
    poly_openMediaUploader(function callback(picInfo) {
        poly_changeImageCover(imageId, picInfo);
    }, false);
}

function poly_changeImageCover(imageId, picInfo) {
    var thumb_img = "<?php echo ($poly_theme == 'dark') ? '/general/glazzed-image-placeholder_dark.png' : '/general/glazzed-image-placeholder.png'; ?>";

    if(picInfo) {
        picInfo.type = poly_attachmentTypePicture;
    }
    var bgImage = (picInfo ? picInfo.src : POLY_IMAGES_URL + thumb_img);
    jQuery("#poly-gallery-project-"+imageId+" .poly-project-cover-src").val(JSON.stringify(picInfo));
    jQuery("#poly-gallery-project-"+imageId+" .poly-attachment-img").css('background', 'url('+bgImage+') center center / cover no-repeat');
}

function poly_updateUI(){
    if ( poly_schedule.name ){
        jQuery("#poly-schedule-name").val( poly_schedule.name );
    }

    jQuery("#poly-gallery-project-list").empty();
    if( poly_schedule.schedule_items && Object.entries(poly_schedule.schedule_items).length ){
        for (const [schedule_itemIndex, schedule_item] of Object.entries(poly_schedule.schedule_items)) {
            console.log(schedule_item);
            var scheduleItem = poly_schedule.schedule_items[schedule_itemIndex];
            poly_addProjectItem(scheduleItem);
            if ( scheduleItem.competition_name_id ){
                // jQuery(`#competition-name-${cItem.schedule_item_id}-${cItem.competition_name_id} .select-box-input[value="${cItem.competition_name_id}"]`).click();
                jQuery(`#competition-name-${scheduleItem.schedule_item_id}-${scheduleItem.competition_name_id}`).click();
            }

            if ( scheduleItem.city_id ){
                jQuery(`#city-${scheduleItem.schedule_item_id}-${scheduleItem.city_id}`).click();
            }

            if ( scheduleItem.stage_id ){
                jQuery(`#stage-${scheduleItem.schedule_item_id}-${scheduleItem.stage_id}`).click();
            }

            var image = scheduleItem.image ? JSON.parse(PolyBase64.decode(scheduleItem.image)) : null;
            poly_changeImageCover(scheduleItem.schedule_item_id, image);
        }

        jQuery(".poly-empty-schedule-item-list-alert").hide();
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
        poly_selectedScheduleItemId = elem.attr('data-id');
        var poly_activeScheduleItem = poly_schedule.schedule_items[poly_selectedScheduleItemId];
        //poly_activeScheduleItem.competition_name_id = jQuery("input[name='poly-schedule-item.competition-name-id']", elem).val();
        poly_activeScheduleItem.number = jQuery("input[name='poly-schedule-item.number']", elem).val();
        poly_activeScheduleItem.number_ekp = jQuery("input[name='poly-schedule-item.number_ekp']", elem).val();
        poly_activeScheduleItem.competition_name_id = Number(jQuery(`#poly-competition-name-${poly_selectedScheduleItemId} .select-box-input:checked`).val());
        poly_activeScheduleItem.disciplines = jQuery("input[name='poly-schedule-item.disciplines']", elem).val();
        poly_activeScheduleItem.age_groups = jQuery("input[name='poly-schedule-item.age-groups']", elem).val();
        // poly_activeScheduleItem.image = jQuery("input[name='poly-schedule-item.image']", elem).val();
        poly_activeScheduleItem.image = PolyBase64.encode(jQuery("input[name='poly-schedule-item.image']", elem).val());
        // jQuery("#poly-gallery-project-".poly_selectedScheduleItemId." .poly-attachment-img");
        poly_activeScheduleItem.city_id = Number(jQuery(`#poly-city-${poly_selectedScheduleItemId} .select-box-input:checked`).val());
        const stage_id = jQuery(`#poly-stage-${poly_selectedScheduleItemId} .select-box-input:checked`).val();
        poly_activeScheduleItem.stage_id = stage_id ? Number(stage_id) : null;
        poly_activeScheduleItem.start_date = jQuery("input[name='poly-schedule-item.start-date']", elem).val();
        poly_activeScheduleItem.end_date = jQuery("input[name='poly-schedule-item.end-date']", elem).val();

        poly_activeScheduleItem.regulations = jQuery("input[name='poly-schedule-item.regulations']", elem).val();
        poly_activeScheduleItem.protocol = jQuery("input[name='poly-schedule-item.protocol']", elem).val();
        poly_activeScheduleItem.details = jQuery("input[name='poly-schedule-item.details']", elem).val();
        poly_schedule.schedule_items[poly_selectedScheduleItemId] = poly_activeScheduleItem;
    });
}

function validatedSchedule(schedule){
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
    delete poly_schedule.schedule_items[poly_childId];
    poly_schedule.deletions.push(poly_childId);

    // //Remove from ordered list
    // var crp_oi = poly_schedule.corder.indexOf(poly_childId);
    // if(crp_oi >= 0){
    //     poly_schedule.corder.splice(crp_oi,1);
    // }

    jQuery("#poly-gallery-project-"+poly_childId).remove();
}

function htmlEntitiesEncode(str){
    return jQuery('<div/>').text(str).html();
}

</script>
