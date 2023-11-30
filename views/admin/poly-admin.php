<div class="poly-background">
</div>
<div id="poly-wrap" class="poly-wrap poly-glazzed-wrap">

<?php include_once( POLY_ADMIN_VIEWS_DIR_PATH.'/poly-header-banner.php'); ?>

<div class="poly-wrap-main">

    <script>
        POLY_AJAX_URL = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>';
        POLY_IMAGES_URL = '<?php echo POLY_IMAGES_URL ?>';
    </script>

    <?php

    abstract class POLYTabType{
        const Dashboard = 'dashboard';
        const Settings = 'settings';
        const Help = 'help';
        const Terms = 'terms';
    }

    $poly_tabs = array(
        POLYTabType::Dashboard => 'All Schedules',
        POLYTabType::Settings => 'General Settings',
        POLYTabType::Help => 'User Manual',
    );

    $poly_adminPage = isset( $_REQUEST['page']) ? filter_var($_REQUEST['page'], FILTER_SANITIZE_STRING) : null;
    $poly_currentTab = isset ( $_GET['tab'] ) ? filter_var($_GET['tab'], FILTER_SANITIZE_STRING) : POLYTabType::Dashboard;
    $poly_action = isset ( $_GET['action'] ) ? filter_var($_GET['action'], FILTER_SANITIZE_STRING) : null;
    $poly_gridType = isset ( $_GET['type'] ) ? filter_var($_GET['type'], FILTER_SANITIZE_STRING) : null;

    include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-modal-spinner.php");
    include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-header.php");

    if($poly_action == 'create' || $poly_action == 'edit'){
        if($poly_gridType == POLYGridType::GALLERY) {
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-gallery.php");
        } elseif($poly_gridType == POLYGridType::TEAM) {
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-team.php");
        } elseif($poly_gridType == POLYGridType::CLIENT_LOGOS) {
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-client_logos.php");
        } elseif($poly_gridType == POLYGridType::CATALOG) {
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-catalog.php");
        } elseif($poly_gridType == POLYGridType::SCHEDULES) {
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-schedules.php");
        } else if($poly_gridType == POLYGridType::SLIDER) {
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-slider.php");
        } else if($poly_gridType == POLYGridType::COMPETITION_NAMES) {
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-competition-names.php");
        } else if($poly_gridType == POLYGridType::COUNTRIES) {
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-countries.php");
        } else if($poly_gridType == POLYGridType::SCHEDULE_ITEMS) {
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-schedule-items.php");
        } else if($poly_gridType == POLYGridType::SPORT_DISCIPLINES) {
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-sport-disciplines.php");
        } else if($poly_gridType == POLYGridType::SPORT_DISCIPLINE_NAMES) {
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-sport-discipline-names.php");
        } else if($poly_gridType == POLYGridType::COMPETITION_STAGES) {
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-competition-stages.php");
        } else if($poly_gridType == POLYGridType::COMPETITION_STAGE_NAMES) {
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-competition-stage-names.php");
        } else if($poly_gridType == POLYGridType::CITIES) {
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-cities.php");
        } else if($poly_gridType == POLYGridType::AGE_GROUPS) {
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-age-groups.php");
        } else if($poly_gridType == POLYGridType::AGE_GROUP_NAMES) {
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-age-group-names.php");
        }
        else {
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-album.php");
        }
    }else if ($poly_action == 'options'){
        include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-schedule-options.php");
    }else{
        //Tabs are not fully developed yet, that's why we have disabled them in this version
        //poly_renderAdminTabs($poly_currentTab, $poly_adminPage, $poly_tabs);

        if($poly_currentTab == POLYTabType::Dashboard){
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-dashboard.php");            
        }else if($poly_currentTab == POLYTabType::Settings){
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-settings.php");
        }else if($poly_currentTab == POLYTabType::Help){
            include_once(POLY_ADMIN_VIEWS_DIR_PATH."/poly-admin-help.php");
        }
    }

    function poly_renderAdminTabs( $current, $page, $tabs = array()){
        //Hardcoded style for removing dynamically added bottom-border
        echo '<h2 class="nav-tab-wrapper poly-admin-nav-tab-wrapper" style="border: 0px">';

        foreach ($tabs as $tab => $name) {
            $class = ($tab == $current) ? 'nav-tab-active' : '';
            echo "<a class='nav-tab $class' href='?page=$page&tab=$tab'>$name</a>";
        }
        echo '</h2>';
    }

    ?>
    <div style="clear:both;"></div>
</div>
</div>
