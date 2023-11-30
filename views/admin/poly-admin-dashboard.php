<?php

if( $poly_adminPageType == POLYGridType::COMPETITION_NAMES ){
    require_once( POLY_CLASSES_DIR_PATH.'/POLYCompetitionNamesListTable.php');

    //Create an instance of our package class...
    $listTable = new POLYCompetitionNamesListTable();

}
elseif($poly_adminPageType == POLYGridType::SCHEDULES){
    require_once( POLY_CLASSES_DIR_PATH.'/POLYSchedulesListTable.php');

    //Create an instance of our package class...
    $listTable = new POLYSchedulesListTable();
}
elseif($poly_adminPageType == POLYGridType::COUNTRIES){
    require_once( POLY_CLASSES_DIR_PATH.'/POLYCountriesListTable.php');

    //Create an instance of our package class...
    $listTable = new POLYCountriesListTable();
}
elseif($poly_adminPageType == POLYGridType::SCHEDULE_ITEMS){
    require_once( POLY_CLASSES_DIR_PATH.'/POLYScheduleItemsListTable.php');

    //Create an instance of our package class...
    $listTable = new POLYScheduleItemsListTable();
}
elseif($poly_adminPageType == POLYGridType::SPORT_DISCIPLINES){
    require_once( POLY_CLASSES_DIR_PATH.'/POLYSportDisciplinesListTable.php');

    //Create an instance of our package class...
    $listTable = new POLYSportDisciplinesListTable();
}
elseif($poly_adminPageType == POLYGridType::SPORT_DISCIPLINE_NAMES){
    require_once( POLY_CLASSES_DIR_PATH.'/POLYSportDisciplineNamesListTable.php');

    //Create an instance of our package class...
    $listTable = new POLYSportDisciplineNamesListTable();
}
elseif($poly_adminPageType == POLYGridType::COMPETITION_STAGES){
    require_once( POLY_CLASSES_DIR_PATH.'/POLYCompetitionStagesListTable.php');

    //Create an instance of our package class...
    $listTable = new POLYCompetitionStagesListTable();
}
elseif($poly_adminPageType == POLYGridType::COMPETITION_STAGE_NAMES){
    require_once( POLY_CLASSES_DIR_PATH.'/POLYCompetitionStageNamesListTable.php');

    //Create an instance of our package class...
    $listTable = new POLYCompetitionStageNamesListTable();
}
elseif($poly_adminPageType == POLYGridType::AGE_GROUPS){
    require_once( POLY_CLASSES_DIR_PATH.'/POLYAgeGroupsListTable.php');

    //Create an instance of our package class...
    $listTable = new POLYAgeGroupsListTable();
}
elseif($poly_adminPageType == POLYGridType::AGE_GROUP_NAMES){
    require_once( POLY_CLASSES_DIR_PATH.'/POLYAgeGroupNamesListTable.php');

    //Create an instance of our package class...
    $listTable = new POLYAgeGroupNamesListTable();
}
elseif($poly_adminPageType == POLYGridType::CITIES){
    require_once( POLY_CLASSES_DIR_PATH.'/POLYCitiesListTable.php');

    //Create an instance of our package class...
    $listTable = new POLYCitiesListTable();
}
else{
    require_once( POLY_CLASSES_DIR_PATH.'/POLYDashboardListTable.php');

    //Create an instance of our package class...
    $listTable = new POLYDashboardListTable();
}

//Prepare items of our package class...
$listTable->prepare_items();

function featuresListTooltip(){
    $tooltip = "";
    $tooltip .= "<div class=\"poly-tooltip-content\">";
    $tooltip .= "<ul>";
    $tooltip .= "<li>* Do Full Design Adjustments</li>";
    $tooltip .= "<li>* Put Multiple Grids On Pages</li>";
    $tooltip .= "<li>* Setup Masonry, Puzzle, Grid Layouts</li>";
    $tooltip .= "<li>* Embed YouTube, Vimeo & Native Videos</li>";
    $tooltip .= "<li>* Popup iFrame & Google Maps</li>";
    $tooltip .= "<li>* Open Light/Dark/Fixed/Fullscreen Popups</li>";
    $tooltip .= "<li>* 100+ Hover Styles & Animations</li>";
    $tooltip .= "<li>* Allow Category Filtration & Pagination</li>";
    $tooltip .= "<li>* Enable Social Sharing</li>";
    $tooltip .= "<li>* Perform Ajax/Lazy Loading</li>";
    $tooltip .= "<li>* Receive Product Enquiries</li>";
    $tooltip .= "</ul>";
    $tooltip .= "</div>";

    $tooltip = htmlentities($tooltip);
    return $tooltip;
}
?>

<div id="poly-dashboard-wrapper">
    <div id="poly-dashboard-add-new-wrapper">
        <div>
            <?php if ($poly_adminPageType == POLYGridType::SCHEDULES) { ?><a id="add-schedule-button" class='button-secondary add-schedule-button poly-glazzed-btn poly-glazzed-btn-green' href="<?php echo "?page={$poly_adminPage}&action=create&type=".POLYGridType::SCHEDULES; ?>" title= "<?php echo __("Add new schedule", "schedule")?>"><?php echo __("+ Schedule", "schedule")?></a><?php }
            elseif ($poly_adminPageType == POLYGridType::GALLERY) { ?><a id="add-gallery-button" class='button-secondary add-schedule-button poly-glazzed-btn poly-glazzed-btn-green' href="<?php echo "?page={$poly_adminPage}&action=create&type=".POLYGridType::GALLERY; ?>" title='Add new gallery'>+ Gallery</a><?php }
            elseif ($poly_adminPageType == POLYGridType::CLIENT_LOGOS) { ?><a id="add-client-logos-button" class='button-secondary add-schedule-button poly-glazzed-btn poly-glazzed-btn-green' href="<?php echo "?page={$poly_adminPage}&action=create&type=".POLYGridType::CLIENT_LOGOS; ?>" title='Add new gallery'>+ Client Logos</a><?php }
            elseif ($poly_adminPageType == POLYGridType::TEAM) { ?><a id="add-team-button" class='button-secondary add-schedule-button poly-glazzed-btn poly-glazzed-btn-green' href="<?php echo "?page={$poly_adminPage}&action=create&type=".POLYGridType::TEAM; ?>" title='Add new gallery'>+ Team</a><?php }
            elseif ($poly_adminPageType == POLYGridType::CATALOG) { ?><a id="add-catalog-button" class='button-secondary add-schedule-button poly-glazzed-btn poly-glazzed-btn-green' href="<?php echo "?page={$poly_adminPage}&action=create&type=".POLYGridType::CATALOG; ?>" title='Add new product catalog'>+ Product Catalog</a><?php }
            elseif ($poly_adminPageType == POLYGridType::SLIDER) { ?><a id="add-team-button" class='button-secondary add-schedule-button poly-glazzed-btn poly-glazzed-btn-green' href="<?php echo "?page={$poly_adminPage}&action=create&type=".POLYGridType::SLIDER; ?>" title='Add new slider'>+ Slider</a><?php }
            elseif ($poly_adminPageType == POLYGridType::COMPETITION_NAMES) { ?><a id="add-team-button" class='button-secondary add-schedule-button poly-glazzed-btn poly-glazzed-btn-green' href="<?php echo "?page={$poly_adminPage}&action=create&type=".POLYGridType::COMPETITION_NAMES; ?>" title= "<?php echo __("Add new competition name", "schedule")?>"><?php echo __("+ Competition name", "schedule")?> </a><?php }
            elseif ($poly_adminPageType == POLYGridType::COUNTRIES) { ?><a id="add-team-button" class='button-secondary add-schedule-button poly-glazzed-btn poly-glazzed-btn-green' href="<?php echo "?page={$poly_adminPage}&action=create&type=".POLYGridType::COUNTRIES; ?>" title= "<?php echo __("Add new countries", "schedule")?>"> <?php echo __("+ Countries", "schedule")?></a><?php }
            elseif ($poly_adminPageType == POLYGridType::SCHEDULE_ITEMS) { ?><a id="add-team-button" class='button-secondary add-schedule-button poly-glazzed-btn poly-glazzed-btn-green' href="<?php echo "?page={$poly_adminPage}&action=create&type=".POLYGridType::SCHEDULE_ITEMS; ?>" title='Add new schedule items'>+ Schedule items</a><?php }
            elseif ($poly_adminPageType == POLYGridType::SPORT_DISCIPLINES) { ?><a id="add-team-button" class='button-secondary add-schedule-button poly-glazzed-btn poly-glazzed-btn-green' href="<?php echo "?page={$poly_adminPage}&action=create&type=".POLYGridType::SPORT_DISCIPLINES; ?>" title='Add new sport disciplines'>+ Sport disciplines</a><?php }
            elseif ($poly_adminPageType == POLYGridType::SPORT_DISCIPLINE_NAMES) { ?><a id="add-team-button" class='button-secondary add-schedule-button poly-glazzed-btn poly-glazzed-btn-green' href="<?php echo "?page={$poly_adminPage}&action=create&type=".POLYGridType::SPORT_DISCIPLINE_NAMES; ?>" title= "<?php echo __("Add new sport discipline names", "schedule")?>"><?php echo __("+ Sport discipline names", "schedule")?></a><?php }
            elseif ($poly_adminPageType == POLYGridType::COMPETITION_STAGES) { ?><a id="add-team-button" class='button-secondary add-schedule-button poly-glazzed-btn poly-glazzed-btn-green' href="<?php echo "?page={$poly_adminPage}&action=create&type=".POLYGridType::COMPETITION_STAGES; ?>" title='Add new competition stages'>+ Competition stages</a><?php }
            elseif ($poly_adminPageType == POLYGridType::COMPETITION_STAGE_NAMES) { ?><a id="add-team-button" class='button-secondary add-schedule-button poly-glazzed-btn poly-glazzed-btn-green' href="<?php echo "?page={$poly_adminPage}&action=create&type=".POLYGridType::COMPETITION_STAGE_NAMES; ?>" title= "<?php echo __("Add new competition stage names", "schedule")?>"><?php echo __("+ Competition stage names", "schedule")?></a><?php }
            elseif ($poly_adminPageType == POLYGridType::CITIES) { ?><a id="add-team-button" class='button-secondary add-schedule-button poly-glazzed-btn poly-glazzed-btn-green' href="<?php echo "?page={$poly_adminPage}&action=create&type=".POLYGridType::CITIES; ?>" title='Add new cities'>+ Cities</a><?php }
            elseif ($poly_adminPageType == POLYGridType::AGE_GROUPS) { ?><a id="add-team-button" class='button-secondary add-schedule-button poly-glazzed-btn poly-glazzed-btn-green' href="<?php echo "?page={$poly_adminPage}&action=create&type=".POLYGridType::AGE_GROUPS; ?>" title='Add new age groups'>+ Age groups</a><?php }
            elseif ($poly_adminPageType == POLYGridType::AGE_GROUP_NAMES) { ?><a id="add-team-button" class='button-secondary add-schedule-button poly-glazzed-btn poly-glazzed-btn-green' href="<?php echo "?page={$poly_adminPage}&action=create&type=".POLYGridType::AGE_GROUP_NAMES; ?>" title= "<?php echo __("Add new age group names", "schedule")?>"><?php echo __("+ Age group names", "schedule")?></a><?php }
            else { ?><a id="add-album-button" class='button-secondary add-schedule-button poly-glazzed-btn poly-glazzed-btn-green' href="<?php echo "?page={$poly_adminPage}&action=create" ?>" title='Add new album'>+ Album</a><?php } ?>
        </div>
    </div>
<!--    <div><a class='button-secondary upgrade-button poly-tooltip poly-glazzed-btn poly-glazzed-btn-orange' href='--><?php //echo POLY_PRO_URL ?><!--' title='--><?php //echo featuresListTooltip(); ?><!--'>* UNLOCK ALL FEATURES *</a></div>-->

    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="" method="get">
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $poly_adminPage ?>" />
        <!-- Now we can render the completed list table -->
        <?php $listTable->display() ?>
    </form>

</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery(".tablenav.top", jQuery(".wp-list-table .no-items").closest("#poly-dashboard-wrapper")).hide();
    });
</script>