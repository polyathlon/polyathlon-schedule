<?php

/*
* Plugin Name: Polyathlon Competition Schedule
* Plugin URI: http://base.rsu.edu.ru
* Description: WordPress multipurpose plugin to showcase polyathlon competition schedule!
* Author: Vladislav Antoshkin
* Author URI: http://base.rsu.edu.ru
* License: GPLv2 or later
* Version: 1.0.0
*/


//Load configs
require_once( dirname(__FILE__).'/poly-config.php');
require_once( POLY_CLASSES_DIR_PATH.'/poly-ajax.php');
require_once( POLY_CLASSES_DIR_PATH.'/POLYHelper.php');
require_once( POLY_CLASSES_DIR_PATH.'/POLYDBInitializer.php');

//Register activation & deactivation hooks
register_activation_hook( __FILE__, 'poly_activation_hook');
register_uninstall_hook( __FILE__, 'poly_uninstall_hook');
register_deactivation_hook( __FILE__, 'poly_deactivation_hook');

//Register action hooks
add_action('init', 'poly_init_action');
add_action('admin_enqueue_scripts', 'poly_admin_enqueue_scripts_action' );
add_action('wp_enqueue_scripts', 'poly_wp_enqueue_scripts_action' );
add_action('admin_menu', 'poly_admin_menu_action');
add_action('admin_head', 'poly_admin_head_action');
add_action('admin_footer', 'poly_admin_footer_action');
add_action('upgrader_process_complete', 'poly_update_complete_action', 10, 2);
add_action('plugins_loaded', 'polyathlon_plugins_loaded_action');

//Register filter hooks

//Register poly shortcode handlers
add_shortcode('poly_schedule', 'poly_shortcode_handler');
add_shortcode('poly', 'poly_shortcode_handler');

//Register Ajax actions
add_action( 'wp_ajax_poly_get_schedules', 'wp_ajax_poly_get_schedules');
add_action( 'wp_ajax_poly_save_schedules', 'wp_ajax_poly_save_schedules');
add_action( 'wp_ajax_poly_get_options', 'wp_ajax_poly_get_options');
add_action( 'wp_ajax_poly_save_options', 'wp_ajax_poly_save_options');
add_action( 'wp_ajax_poly_get_competition_names', 'wp_ajax_poly_get_competition_names');
add_action( 'wp_ajax_poly_save_competition_names', 'wp_ajax_poly_save_competition_names');
add_action( 'wp_ajax_poly_get_sport_disciplines', 'wp_ajax_poly_get_sport_disciplines');
add_action( 'wp_ajax_poly_save_sport_disciplines', 'wp_ajax_poly_save_sport_disciplines');
add_action( 'wp_ajax_poly_get_sport_discipline_names', 'wp_ajax_poly_get_sport_discipline_names');
add_action( 'wp_ajax_poly_save_sport_discipline_names', 'wp_ajax_poly_save_sport_discipline_names');
add_action( 'wp_ajax_poly_get_schedule_items', 'wp_ajax_poly_get_schedule_items');
add_action( 'wp_ajax_poly_save_schedule_items', 'wp_ajax_poly_save_schedule_items');
add_action( 'wp_ajax_poly_get_competition_stages', 'wp_ajax_poly_get_competition_stages');
add_action( 'wp_ajax_poly_save_competition_stages', 'wp_ajax_poly_save_competition_stages');
add_action( 'wp_ajax_poly_get_competition_stage_names', 'wp_ajax_poly_get_competition_stage_names');
add_action( 'wp_ajax_poly_save_competition_stage_names', 'wp_ajax_poly_save_competition_stage_names');
add_action( 'wp_ajax_poly_get_age_groups', 'wp_ajax_poly_get_age_groups');
add_action( 'wp_ajax_poly_save_age_groups', 'wp_ajax_poly_save_age_groups');
add_action( 'wp_ajax_poly_get_cities', 'wp_ajax_poly_get_cities');
add_action( 'wp_ajax_poly_save_cities', 'wp_ajax_poly_save_cities');
add_action( 'wp_ajax_poly_get_countries', 'wp_ajax_poly_get_countries');
add_action( 'wp_ajax_poly_save_countries', 'wp_ajax_poly_save_countries');
add_action( 'wp_ajax_poly_get_age_group_names', 'wp_ajax_poly_get_age_group_names');
add_action( 'wp_ajax_poly_save_age_group_names', 'wp_ajax_poly_save_age_group_names');

//Global vars
$poly_schedules;

function poly_update_complete_action( $upgrader_object, $options ) {
    $our_plugin = plugin_basename( __FILE__ );
    if( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
        foreach( $options['plugins'] as $plugin ) {
            if( $plugin == $our_plugin ) {
                set_transient( 'polyathlon_updated', 1 );
            }
        }
    }
}

function polyathlon_plugins_loaded_action()
{
    if (get_transient('polyathlon_updated')) {
        $dbInitializer = new POLYDBInitializer();
        $dbInitializer->checkForChanges();

        delete_transient('polyathlon_updated');
    }
    load_plugin_textdomain( 'schedule' );
}

//Registered activation hook
function poly_activation_hook(){
    $dbInitializer = new POLYDBInitializer();
    if($dbInitializer->needsConfiguration()){
        $dbInitializer->configure();
    }
    $dbInitializer->checkForChanges();
}

function poly_uninstall_hook(){
    delete_option(POLY_BANNERS_CONTENT);
    delete_option(POLY_BANNERS_LAST_LOADED_AT);
}

function poly_deactivation_hook(){
}

//Registered hook actions
function poly_init_action() {
    global $wp_version;
    if ( version_compare( $wp_version, '5.0.0', '>=' ) ) {
        wp_register_script(
            'poly-shortcode-block-script',
            POLY_JS_URL . '/poly-shortcode-block.js',
            array('wp-blocks', 'wp-element')
        );

        wp_register_style(
            'poly-shortcode-block-style',
            POLY_CSS_URL . '/poly-admin-editor-block.css',
            array('wp-edit-blocks'),
            filemtime(plugin_dir_path(__FILE__) . 'css/poly-admin-editor-block.css')
        );

        register_block_type('polyathlon-premium/poly-shortcode-block', array(
            'editor_script' => 'poly-shortcode-block-script',
            'editor_style' => 'poly-shortcode-block-style',
        ));
    }
    ob_start();
}

function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
}

function poly_admin_enqueue_scripts_action($hook) {
    if (stripos($hook, POLY_PLUGIN_SLUG) !== false) {
        poly_enqueue_admin_scripts();
        poly_enqueue_admin_csss();
    }
}

function poly_wp_enqueue_scripts_action(){
    poly_enqueue_front_scripts();
    poly_enqueue_front_csss();
}

function poly_admin_menu_action() {
    poly_setup_admin_menu_buttons();
}

function poly_admin_head_action(){
    poly_include_inline_scripts();
    poly_setup_media_buttons();
}

function poly_admin_footer_action() {
    poly_include_inline_htmls();
}

//Registered hook filters
function poly_mce_external_plugins_filter($pluginsArray){
    return poly_register_tinymce_plugin($pluginsArray);
}

function poly_mce_buttons_filter($buttons){
    return poly_register_tc_buttons($buttons);
}

//Shortcode Hanlders
function poly_shortcode_handler($attributes){
	ob_start();

    //Prepare render data
    global $poly_schedules;
    // $poly_schedules = POLYHelper::getScheduleWithId($attributes['id']);
    $poly_schedules = POLYHelper::getScheduleItems($attributes['id']);
    require_once(POLY_FRONT_VIEWS_DIR_PATH."/poly-front.php");

    $result = ob_get_clean();
    return $result;
}

//Internal functionality
function poly_setup_admin_menu_buttons(){
    add_menu_page(POLY_PLUGIN_NAME, POLY_PLUGIN_NAME, 'edit_posts', POLY_PLUGIN_SLUG, "poly_admin_schedules_page", 'dashicons-schedule', 76);
    add_submenu_page(POLY_PLUGIN_SLUG, POLY_SUBMENU_COMPETITION_NAMES_TITLE, POLY_SUBMENU_COMPETITION_NAMES_TITLE, 'edit_posts', POLY_SUBMENU_COMPETITION_NAMES_SLUG, 'poly_admin_competition_names_page');
    add_submenu_page(POLY_PLUGIN_SLUG, POLY_SUBMENU_SPORT_DISCIPLINE_NAMES_TITLE, POLY_SUBMENU_SPORT_DISCIPLINE_NAMES_TITLE, 'edit_posts', POLY_SUBMENU_SPORT_DISCIPLINE_NAMES_SLUG, 'poly_admin_sport_discipline_names_page');
    add_submenu_page(POLY_PLUGIN_SLUG, POLY_SUBMENU_COMPETITION_STAGE_NAMES_TITLE, POLY_SUBMENU_COMPETITION_STAGE_NAMES_TITLE, 'edit_posts', POLY_SUBMENU_COMPETITION_STAGE_NAMES_SLUG, 'poly_admin_competition_stage_names_page');
    add_submenu_page(POLY_PLUGIN_SLUG, POLY_SUBMENU_AGE_GROUP_NAMES_TITLE, POLY_SUBMENU_AGE_GROUP_NAMES_TITLE, 'edit_posts', POLY_SUBMENU_AGE_GROUP_NAMES_SLUG, 'poly_admin_age_group_names_page');
    add_submenu_page(POLY_PLUGIN_SLUG, POLY_SUBMENU_COUNTRIES_TITLE, POLY_SUBMENU_COUNTRIES_TITLE, 'edit_posts', POLY_SUBMENU_COUNTRIES_SLUG, 'poly_admin_countries_page');
}

function poly_admin_page() {
  require_once(POLY_ADMIN_VIEWS_DIR_PATH.'/poly-admin.php');
}

function poly_admin_albums_page(){
    global $poly_adminPageType;
    $poly_adminPageType = POLYGridType::ALBUM;
    require_once(POLY_ADMIN_VIEWS_DIR_PATH.'/poly-admin.php');
}
function poly_admin_schedules_page(){
    global $poly_adminPageType;
    $poly_adminPageType = POLYGridType::SCHEDULES;
    require_once(POLY_ADMIN_VIEWS_DIR_PATH.'/poly-admin.php');
}
function poly_admin_galleries_page(){
    global $poly_adminPageType;
    $poly_adminPageType = POLYGridType::GALLERY;
    require_once(POLY_ADMIN_VIEWS_DIR_PATH.'/poly-admin.php');
}
function poly_admin_client_logos_page(){
    global $poly_adminPageType;
    $poly_adminPageType = POLYGridType::CLIENT_LOGOS;
    require_once(POLY_ADMIN_VIEWS_DIR_PATH.'/poly-admin.php');
}
function poly_admin_teams_page(){
    global $poly_adminPageType;
    $poly_adminPageType = POLYGridType::TEAM;
    require_once(POLY_ADMIN_VIEWS_DIR_PATH.'/poly-admin.php');
}
function poly_admin_catalogs_page(){
    global $poly_adminPageType;
    $poly_adminPageType = POLYGridType::CATALOG;
    require_once(POLY_ADMIN_VIEWS_DIR_PATH.'/poly-admin.php');
}
function poly_admin_sliders_page(){
    global $poly_adminPageType;
    $poly_adminPageType = POLYGridType::SLIDER;
    require_once(POLY_ADMIN_VIEWS_DIR_PATH.'/poly-admin.php');
}
function poly_admin_competition_names_page(){
    global $poly_adminPageType;
    $poly_adminPageType = POLYGridType::COMPETITION_NAMES;
    require_once(POLY_ADMIN_VIEWS_DIR_PATH.'/poly-admin.php');
}
function poly_admin_countries_page(){
    global $poly_adminPageType;
    $poly_adminPageType = POLYGridType::COUNTRIES;
    require_once(POLY_ADMIN_VIEWS_DIR_PATH.'/poly-admin.php');
}
function poly_admin_schedule_items_page(){
    global $poly_adminPageType;
    $poly_adminPageType = POLYGridType::SCHEDULE_ITEMS;
    require_once(POLY_ADMIN_VIEWS_DIR_PATH.'/poly-admin.php');
}
function poly_admin_sport_disciplines_page(){
    global $poly_adminPageType;
    $poly_adminPageType = POLYGridType::SPORT_DISCIPLINES;
    require_once(POLY_ADMIN_VIEWS_DIR_PATH.'/poly-admin.php');
}
function poly_admin_sport_discipline_names_page(){
    global $poly_adminPageType;
    $poly_adminPageType = POLYGridType::SPORT_DISCIPLINE_NAMES;
    require_once(POLY_ADMIN_VIEWS_DIR_PATH.'/poly-admin.php');
}
function poly_admin_competition_stages_page(){
    global $poly_adminPageType;
    $poly_adminPageType = POLYGridType::COMPETITION_STAGES;
    require_once(POLY_ADMIN_VIEWS_DIR_PATH.'/poly-admin.php');
}
function poly_admin_competition_stage_names_page(){
    global $poly_adminPageType;
    $poly_adminPageType = POLYGridType::COMPETITION_STAGE_NAMES;
    require_once(POLY_ADMIN_VIEWS_DIR_PATH.'/poly-admin.php');
}
function poly_admin_cities_page(){
    global $poly_adminPageType;
    $poly_adminPageType = POLYGridType::CITIES;
    require_once(POLY_ADMIN_VIEWS_DIR_PATH.'/poly-admin.php');
}
function poly_admin_age_groups_page(){
    global $poly_adminPageType;
    $poly_adminPageType = POLYGridType::AGE_GROUPS;
    require_once(POLY_ADMIN_VIEWS_DIR_PATH.'/poly-admin.php');
}
function poly_admin_age_group_names_page(){
    global $poly_adminPageType;
    $poly_adminPageType = POLYGridType::AGE_GROUP_NAMES;
    require_once(POLY_ADMIN_VIEWS_DIR_PATH.'/poly-admin.php');
}


function poly_setup_media_buttons(){
    global $typenow;
    // check user permissions
    if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
        return;
    }

    // verify the post type
    if( ! in_array( $typenow, array( 'post', 'page' ) ) )
        return;

    // check if WYSIWYG is enabled
    if ( get_user_option('rich_editing') == 'true') {
        add_filter("mce_external_plugins", "poly_mce_external_plugins_filter");
        add_filter('mce_buttons', 'poly_mce_buttons_filter');
    }
}

function poly_register_tinymce_plugin($pluginsArray) {
    $pluginsArray['poly_tc_buttons'] = POLY_JS_URL."/poly-tc-buttons.js";
    return $pluginsArray;
}

function poly_register_tc_buttons($buttons) {
    array_push($buttons, "poly_insert_tc_button");
    return $buttons;
}

function poly_include_inline_scripts(){
?>
    <script type="text/javascript">

        jQuery(document).ready(function() {
        });
    </script>
<?php
}

function poly_include_inline_htmls(){
?>

<?php
}

function poly_enqueue_admin_scripts(){
    wp_enqueue_script("jquery");
    wp_enqueue_script("jquery-ui-core");
    wp_enqueue_script("jquery-ui-sortable");
    wp_enqueue_script("jquery-ui-autocomplete");

    //Enqueue JS files
    wp_enqueue_script( 'poly-helper-js', POLY_JS_URL.'/poly-helper.js', array('jquery'), "", false );
    wp_enqueue_script( 'poly-main-admin-js', POLY_JS_URL.'/poly-main-admin.js', array('jquery'), "", true );
    wp_enqueue_script( 'poly-ajax-admin-js', POLY_JS_URL.'/poly-ajax-admin.js', array('jquery'), "", true );

    wp_register_script('poly-tooltipster', POLY_JS_URL."/jquery/jquery.tooltipster.js", array('jquery'), "", true );
    wp_enqueue_script('poly-tooltipster');

    wp_register_script('poly-caret', POLY_JS_URL."/jquery/jquery.caret.js", array('jquery'), "", true );
    wp_enqueue_script('poly-caret');

    wp_register_script('poly-tageditor', POLY_JS_URL."/jquery/jquery.tageditor.js", array('jquery'), "", true );
    wp_enqueue_script('poly-tageditor');

    wp_enqueue_media();
    wp_enqueue_script('wp-color-picker');
}

function poly_enqueue_admin_csss(){
    //Enqueue CSS files

    wp_register_style('poly-main-admin-style', POLY_CSS_URL.'/poly-main-admin.css');
    wp_enqueue_style('poly-main-admin-style');

    wp_register_style('poly-tc-buttons', POLY_CSS_URL.'/poly-tc-buttons.css');
    wp_enqueue_style('poly-tc-buttons');

    wp_register_style('poly-tooltipster', POLY_CSS_URL.'/tooltipster/tooltipster.css');
    wp_enqueue_style('poly-tooltipster');
    wp_register_style('poly-tooltipster-theme', POLY_CSS_URL.'/tooltipster/themes/tooltipster-shadow.css');
    wp_enqueue_style('poly-tooltipster-theme');

    wp_register_style('poly-accordion', POLY_CSS_URL.'/accordion/accordion.css');
    wp_enqueue_style('poly-accordion');

    wp_register_style('poly-tageditor', POLY_CSS_URL.'/tageditor/tageditor.css');
    wp_enqueue_style('poly-tageditor');

    wp_enqueue_style( 'wp-color-picker' );

    wp_register_style('poly-font-awesome', POLY_CSS_URL.'/fontawesome/font-awesome.css');
    wp_enqueue_style('poly-font-awesome');
}

function poly_enqueue_front_scripts(){
    //Enqueue JS files
    wp_enqueue_script( 'poly-main-front-js', POLY_JS_URL.'/poly-main-front.js', array('jquery') );
    wp_enqueue_script( 'poly-helper-js', POLY_JS_URL.'/poly-helper.js', array('jquery') );
    wp_enqueue_script( 'poly-competition-js', POLY_JS_URL.'/poly-competition.js', array('jquery') );
    wp_enqueue_script( 'poly-modernizr', POLY_JS_URL."/jquery/jquery.modernizr.js", array('jquery') );
    wp_enqueue_script( 'poly-tiled-layer', POLY_JS_URL."/poly-tiled-layer.js", array('jquery') );
    wp_enqueue_script( 'poly-fs-viewer', POLY_JS_URL.'/poly-fs-viewer.js', array('jquery') );
    wp_enqueue_script( 'poly-lg-viewer', POLY_JS_URL.'/jquery/jquery.lightgallery.js', array('jquery') );
    wp_enqueue_script( 'poly-owl', POLY_JS_URL.'/owl-carousel/owl.carousel.js', array('jquery') );
}

function poly_enqueue_front_csss(){
    //Enqueue CSS files
    wp_register_style('poly-main-front-style', POLY_CSS_URL.'/poly-main-front.css');
    wp_enqueue_style('poly-main-front-style');

    wp_register_style('poly-tc-buttons', POLY_CSS_URL.'/poly-tc-buttons.css');
    wp_enqueue_style('poly-tc-buttons');

    wp_register_style('poly-tiled-layer', POLY_CSS_URL.'/poly-tiled-layer.css');
    wp_enqueue_style('poly-tiled-layer');

    wp_register_style('poly-fs-viewer', POLY_CSS_URL.'/fsviewer/poly-fs-viewer.css');
    wp_enqueue_style('poly-fs-viewer');

    wp_register_style('poly-font-awesome', POLY_CSS_URL.'/fontawesome/font-awesome.css');
    wp_enqueue_style('poly-font-awesome');

    wp_register_style('poly-lg-viewer', POLY_CSS_URL.'/lightgallery/lightgallery.css');
    wp_enqueue_style('poly-lg-viewer');

    wp_register_style('poly-captions', POLY_CSS_URL.'/poly-captions.css');
    wp_enqueue_style('poly-captions');

    wp_register_style('poly-captions', POLY_CSS_URL.'/poly-captions.css');
    wp_enqueue_style('poly-captions');

    wp_register_style('poly-owl', POLY_CSS_URL.'/owl-carousel/assets/owl.carousel.css');
    wp_enqueue_style('poly-owl');

    wp_register_style('poly-layout', POLY_CSS_URL.'/owl-carousel/layout.css');
    wp_enqueue_style('poly-layout');
}
