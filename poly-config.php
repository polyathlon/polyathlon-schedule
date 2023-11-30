<?php

/* Enable debugging */
/*
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);
*/


//***************** Immutable configurations ********************//
define( 'POLY_ROOT_DIR_NAME', 'polyathlon-schedule');
define( 'POLY_ROOT_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'POLY_CLASSES_DIR_PATH' , POLY_ROOT_DIR_PATH.'classes' );
define( 'POLY_IMAGES_DIR_PATH', POLY_ROOT_DIR_PATH.'images' );
define( 'POLY_VIEWS_DIR_PATH', POLY_ROOT_DIR_PATH.'views' );
define( 'POLY_ADMIN_VIEWS_DIR_PATH', POLY_VIEWS_DIR_PATH.'/admin' );
define( 'POLY_FRONT_VIEWS_DIR_PATH', POLY_VIEWS_DIR_PATH.'/front' );
define( 'POLY_PLUGIN_URL'   , plugins_url( POLY_ROOT_DIR_NAME ) );
define( 'POLY_CSS_URL'      , POLY_PLUGIN_URL.'/css' );
define( 'POLY_JS_URL'       , POLY_PLUGIN_URL.'/js' );
define( 'POLY_IMAGES_URL', POLY_PLUGIN_URL.'/images' );
define( 'POLY_API_URL', 'https://polyathlon.com/deliver/api/v1/api.php' );

define( 'POLY_LICENSE_TYPE', 'free' );
define( 'POLY_BANNERS_LAST_LOADED_AT', 'poly_banners_last_loaded_at' );
define( 'POLY_BANNERS_CONTENT', 'poly_banners_content' );

global $wpdb;

define( 'POLY_PLUGIN_PREFIX', 'poly');
define( 'POLY_DB_PREFIX'     , $wpdb->prefix.POLY_PLUGIN_PREFIX.'_' );

define("POLY_PLUGIN_NAME", __("Schedule", "schedule"));
define("POLY_PLUGIN_SLUG","polyathlon");

define("POLY_SUBMENU_SCHEDULES_TITLE", __("Schedules", "schedule"));
define("POLY_SUBMENU_SCHEDULES_SLUG","polyathlon-schedule");

// define("POLY_SUBMENU_GALLERIES_TITLE","Galleries");
// define("POLY_SUBMENU_GALLERIES_SLUG","polyathlon-galleries");
// define("POLY_SUBMENU_CLIENT_LOGOS_TITLE","Client logos");
// define("POLY_SUBMENU_CLIENT_LOGOS_SLUG","polyathlon-client-logos");
// define("POLY_SUBMENU_TEAMS_TITLE","Teams");
// define("POLY_SUBMENU_TEAMS_SLUG","polyathlon-teams");
// define("POLY_SUBMENU_PRODUCT_CATALOGS_TITLE","Product catalogs");
// define("POLY_SUBMENU_PRODUCT_CATALOGS_SLUG","polyathlon-catalogs");
// define("POLY_SUBMENU_SLIDER_TITLE","Sliders");
// define("POLY_SUBMENU_SLIDER_SLUG","polyathlon-sliders");
 define("POLY_SUBMENU_COMPETITION_NAMES_TITLE", __("Competition names", "schedule"));
define("POLY_SUBMENU_COMPETITION_NAMES_SLUG","polyathlon-competition-names");
define("POLY_SUBMENU_COUNTRIES_TITLE", __("Countries and cities", "schedule"));
define("POLY_SUBMENU_COUNTRIES_SLUG","polyathlon-countries");
define("POLY_SUBMENU_SCHEDULE_ITEMS_TITLE",__("Schedule items", "schedule"));
define("POLY_SUBMENU_SCHEDULE_ITEMS_SLUG","polyathlon-schedule-items");
//define("POLY_SUBMENU_SPORT_DISCIPLINES_TITLE","Sport disciplines");
//define("POLY_SUBMENU_SPORT_DISCIPLINES_SLUG","polyathlon-sport-disciplines");
define("POLY_SUBMENU_SPORT_DISCIPLINE_NAMES_TITLE", __("Sport discipline names", "schedule"));
define("POLY_SUBMENU_SPORT_DISCIPLINE_NAMES_SLUG","polyathlon-sport-discipline-names");
//define("POLY_SUBMENU_COMPETITION_STAGES_TITLE","Competition stages");
//define("POLY_SUBMENU_COMPETITION_STAGES_SLUG","polyathlon-competition-stages");
define("POLY_SUBMENU_COMPETITION_STAGE_NAMES_TITLE", __("Competition stages", "schedule"));
define("POLY_SUBMENU_COMPETITION_STAGE_NAMES_SLUG","polyathlon-competition-stage-names");
//define("POLY_SUBMENU_CITIES_TITLE","Cities");
//define("POLY_SUBMENU_CITIES_SLUG","polyathlon-cities");
//define("POLY_SUBMENU_AGE_GROUPS_TITLE","Age groups");
//define("POLY_SUBMENU_AGE_GROUPS_SLUG","polyathlon-age-groups");
define("POLY_SUBMENU_AGE_GROUP_NAMES_TITLE", __("Age groups", "schedule"));
define("POLY_SUBMENU_AGE_GROUP_NAMES_SLUG","polyathlon-age-group-names");



//**************** Configurable configurations *******************//
define( 'POLY_PRO_URL' , 'http://www.polyathlon.ru/schedule' );

//Define table names
define( 'POLY_TABLE_SCHEDULES' , POLY_DB_PREFIX.'schedules' );
define( 'POLY_TABLE_SCHEDULES_ID' , 'schedule_id' );
define( 'POLY_TABLE_SCHEDULE_ITEMS' , POLY_DB_PREFIX.'schedule_items' );
define( 'POLY_TABLE_SCHEDULE_ITEMS_ID' , 'schedule_item_id' );
define( 'POLY_TABLE_COMPETITION_NAMES' , POLY_DB_PREFIX.'competition_names' );
define( 'POLY_TABLE_COMPETITION_NAMES_ID' , 'competition_name_id' );
define( 'POLY_TABLE_SPORT_DISCIPLINES' , POLY_DB_PREFIX.'sport_disciplines' );
define( 'POLY_TABLE_SPORT_DISCIPLINES_ID' , 'sport_discipline_id' );
define( 'POLY_TABLE_SPORT_DISCIPLINE_NAMES' , POLY_DB_PREFIX.'sport_discipline_names' );
define( 'POLY_TABLE_SPORT_DISCIPLINE_NAMES_ID' , 'sport_discipline_name_id' );
define( 'POLY_TABLE_SPORT_DISCIPLINES_AND_AGE_GROUPS' , POLY_DB_PREFIX.'sport_disciplines_and_age_groups' );
define( 'POLY_TABLE_COMPETITION_STAGES' , POLY_DB_PREFIX.'competition_stages' );
define( 'POLY_TABLE_COMPETITION_STAGES_ID' , 'competition_stage_id' );
define( 'POLY_TABLE_STAGES' , POLY_DB_PREFIX.'stages' );
define( 'POLY_TABLE_STAGES_ID' , 'stage_id' );
//define( 'POLY_TABLE_COMPETITION_STAGE_NAMES' , POLY_DB_PREFIX.'competition_stage_names' );
//define( 'POLY_TABLE_COMPETITION_STAGE_NAMES_ID' , 'competition_stage_name_id' );
define( 'POLY_TABLE_COMPETITION_STAGE_NAMES' , POLY_DB_PREFIX.'stages' );
define( 'POLY_TABLE_COMPETITION_STAGE_NAMES_ID' , 'stage_id' );
define( 'POLY_TABLE_AGE_GROUPS' , POLY_DB_PREFIX.'age_groups' );
define( 'POLY_TABLE_AGE_GROUPS_ID' , 'age_group_id' );
define( 'POLY_TABLE_CITIES' , POLY_DB_PREFIX.'cities' );
define( 'POLY_TABLE_CITIES_ID' , 'city_id' );
define( 'POLY_TABLE_COUNTRIES' , POLY_DB_PREFIX.'countries' );
define( 'POLY_TABLE_COUNTRIES_ID' , 'country_id' );
define( 'POLY_TABLE_AGE_GROUP_NAMES' , POLY_DB_PREFIX.'age_group_names' );
define( 'POLY_TABLE_AGE_GROUP_NAMES_ID' , 'age_group_names_id' );

define( 'POLY_TABLE_PROJECTS' , POLY_DB_PREFIX.'projects' );
define( 'POLY_TABLE_OPTIONS' , POLY_DB_PREFIX.'options' );

//Enum simulated classes
abstract class POLYGridType{
    const ALBUM = 'album_gallery';
    const SCHEDULES = 'schedules';
    const GALLERY = 'gallery';
    const TEAM = 'team';
    const CLIENT_LOGOS = 'client_logos';
    const CATALOG = 'catalog';
    const SLIDER = 'slider';
    const COMPETITION_NAMES = 'competition_names';
    const COUNTRIES = 'countries';
    const SCHEDULE_ITEMS = 'schedule_items';
    const SPORT_DISCIPLINES = 'sport_disciplines';
    const SPORT_DISCIPLINE_NAMES = 'sport_discipline_names';
    const COMPETITION_STAGE_NAMES = 'competition_stage_names';
    const COMPETITION_STAGES = 'competition_stages';
    const CITIES = 'cities';
    const AGE_GROUPS = 'age_groups';
    const AGE_GROUP_NAMES = 'age_group_names';
}

abstract class POLYViewType{
    const Unknown = 0;
    const Puzzle = 1;
    const Masonry = 2;
    const Square = 3;
    const WaterfallList = 4;
    const Slider = 5;
    const TestLayout = 6;
}

abstract class POLYPjViewerType{
    const Unknown = 0;
    const LightGallery = 1;
    const LightGalleryLight = 2;
}


abstract class POLYProductStatus {
    const Visible = 'Visible';
    const Invisible = 'Invisible';
}


abstract class POLYDetailsDisplayStyle{
    const none = 'details-none';
    const style01 = 'details01';
    const style02 = 'details02';
    const style03 = 'details03';
    const style04 = 'details04';
    const style05 = 'details05';
    const style06 = 'details06';
    const style07 = 'details07';
    const style08 = 'details08';
    const style09 = 'details09';
    const style10 = 'details10';
    const style11 = 'details11';
    const style12 = 'details12';
    const style13 = 'details13';
    const style14 = 'details14';
    const style15 = 'details15';
    const style16 = 'details16';
    const style17 = 'details17';
    const style18 = 'details18';
    const style19 = 'details19';
    const style20 = 'details20';
    const style21 = 'details21';
    const style22 = 'details22';
    const style23 = 'details23';
    const style24 = 'details24';
    const style25 = 'details25';
    const style26 = 'details26';
    const style27 = 'details27';
    const style28 = 'details28';
    const style29 = 'details29';
    const style30 = 'details30';
    const style31 = 'details31 poly-details-bg';
    const style32 = 'details32 poly-details-bg';
    const style33 = 'details33 poly-details-bg';
    const style34 = 'details34 poly-details-bg';
    const style35 = 'details35 poly-details-bg';
    const style36 = 'details36 poly-details-bg';
    const style37 = 'details37 poly-details-bg';
    const style38 = 'details38 poly-details-bg';
    const style39 = 'details39 poly-details-bg';
    const style40 = 'details40 poly-details-bg';
    const style41 = 'details41 poly-details-bg';
    const style42 = 'details42 poly-details-bg';
    const style43 = 'details43 poly-details-bg';
    const style44 = 'details44 poly-details-bg';

    const dflt = 'details-none';
}

abstract class POLYPictureHoverStyle{
    const none = 'image-none';
    const style01 = 'image01';
    const style02 = 'image02';
    const style03 = 'image03';
    const style04 = 'image04';
    const style05 = 'image05';
    const style06 = 'image06';
    const style07 = 'image07';

    const dflt = 'image-none';
}

abstract class POLYOverlayDisplayStyle{
    const none = 'overlay-none';
    const style00 = 'overlay00';
    const style01 = 'overlay01';
    const style02 = 'overlay02';
    const style03 = 'overlay03';
    const style04 = 'overlay04';
    const style05 = 'overlay05';
    const style06 = 'overlay06';
    const style07 = 'overlay07';
    const style08 = 'overlay08';
    const style09 = 'overlay09';
    const style10 = 'overlay10';
    const style11 = 'overlay11';
    const style12 = 'overlay12';
    const style13 = 'overlay13';
    const style14 = 'overlay14';
    const style15 = 'overlay15';
    const style16 = 'overlay16';
    const style17 = 'overlay17';
    const style18 = 'overlay18';
    const style19 = 'overlay19';
    const style20 = 'overlay20';
    const style21 = 'overlay21';
    const style22 = 'overlay22';
    const style23 = 'overlay23';
    const style24 = 'overlay24';
    const style25 = 'overlay25';
    const style26 = 'overlay26';
    const style27 = 'overlay27';

    const dflt = 'overlay-none';
}

abstract class POLYOverlayButtonsDisplayStyle{
    const none =    'button-none';
    const style01 = 'button01';
    const style02 = 'button02';
    const style03 = 'button03';
    const style04 = 'button04';
    const style05 = 'button05';
    const style06 = 'button06';
    const style07 = 'button07';
    const style08 = 'button08';
    const style09 = 'button09';
    const style10 = 'button10';
    const style11 = 'button11';
    const style12 = 'button12';
    const style13 = 'button13';
    const style14 = 'button14';
    const style15 = 'button15';
    const style16 = 'button16';
    const style17 = 'button17';
    const style18 = 'button18';
    const style19 = 'button19';
    const style20 = 'button20';
    const style21 = 'button21';
    const style22 = 'button22';

    const dflt = 'button-none';
}

abstract class POLYShareButtonsDisplayStyle{
    const none =    'share-none';
    const style01 = 'share01';
    const style02 = 'share02';
    const style03 = 'share03';
    const style04 = 'share04';
    const style05 = 'share05';
    const style06 = 'share06';
    const style07 = 'share07';
    const style08 = 'share08';
    const style09 = 'share09';
    const style10 = 'share10';
    const style11 = 'share11';
    const style12 = 'share12';
    const style13 = 'share13';
    const style14 = 'share14';
    const style15 = 'share15';
    const style16 = 'share16';
    const style17 = 'share17';
    const style18 = 'share18';
    const style19 = 'share19';
    const style20 = 'share20';
    const style21 = 'share21';
    const style22 = 'share22';
    const style23 = 'share23';
    const style24 = 'share24';

    const dflt = 'share-none';
}

abstract class POLYOverlayButtonsHoverEffect{
    const none =    '';

    //2D Transitions
    const style01 = 'poly-hvr-grow';
    const style02 = 'poly-hvr-shrink';
    const style03 = 'poly-hvr-pulse';
    const style04 = 'poly-hvr-pulse-grow';
    const style05 = 'poly-hvr-pulse-shrink';
    const style06 = 'poly-hvr-push';
    const style07 = 'poly-hvr-pop';
    const style08 = 'poly-hvr-bounce-in';
    const style09 = 'poly-hvr-bounce-out';
    const style10 = 'poly-hvr-rotate';
    const style11 = 'poly-hvr-grow-rotate';
    const style12 = 'poly-hvr-float';
    const style13 = 'poly-hvr-sink';
    const style14 = 'poly-hvr-bob';
    const style15 = 'poly-hvr-hang';
    const style16 = 'poly-hvr-skew';
    const style17 = 'poly-hvr-skew-forward';
    const style18 = 'poly-hvr-skew-backward';
    const style19 = 'poly-hvr-wobble-horizontal';
    const style20 = 'poly-hvr-wobble-vertical';
    const style21 = 'poly-hvr-wobble-to-bottom-right';
    const style22 = 'poly-hvr-wobble-to-top-right';
    const style23 = 'poly-hvr-wobble-top';
    const style24 = 'poly-hvr-wobble-bottom';
    const style25 = 'poly-hvr-wobble-skew';
    const style26 = 'poly-hvr-wobble-skew';
    const style27 = 'poly-hvr-buzz';
    const style28 = 'poly-hvr-buzz-out';

    //Background Transitions
    const style29 = 'poly-hvr-fade';
    const style30 = 'poly-hvr-sweep-to-right';
    const style31 = 'poly-hvr-sweep-to-left';
    const style32 = 'poly-hvr-sweep-to-bottom';
    const style33 = 'poly-hvr-sweep-to-top';
    const style34 = 'poly-hvr-bounce-to-right';
    const style35 = 'poly-hvr-bounce-to-left';
    const style36 = 'poly-hvr-bounce-to-bottom';
    const style37 = 'poly-hvr-bounce-to-top';
    const style38 = 'poly-hvr-radial-out';
    const style39 = 'poly-hvr-radial-in';
    const style40 = 'poly-hvr-rectangle-in';
    const style41 = 'poly-hvr-rectangle-out';
    const style42 = 'poly-hvr-shutter-in-horizontal';
    const style43 = 'poly-hvr-shutter-out-horizontal';
    const style44 = 'poly-hvr-shutter-in-vertical';
    const style45 = 'poly-hvr-shutter-out-vertical';

    //Underline & Overline Transitions
    const style46 = 'poly-hvr-underline-from-left';
    const style47 = 'poly-hvr-underline-from-center';
    const style48 = 'poly-hvr-underline-from-right';
    const style49 = 'poly-hvr-underline-reveal';
    const style50 = 'poly-hvr-overline-reveal';
    const style51 = 'poly-hvr-overline-from-left';
    const style52 = 'poly-hvr-overline-from-center';
    const style53 = 'poly-hvr-overline-from-right';

    const dflt = '';
}

abstract class POLYFilterStyle{
    const style1 = 'poly-filter-style-1';
    const style2 = 'poly-filter-style-2';
    const style3 = 'poly-filter-style-3';
    const style4 = 'poly-filter-style-4';
    const style5 = 'poly-filter-style-5';
    const style6 = 'poly-filter-style-6';
    const style7 = 'poly-filter-style-7';
}

abstract class POLYPaginationStyle{
    const style1 = 'poly-pagination-style-1';
    const style2 = 'poly-pagination-style-2';
    const style3 = 'poly-pagination-style-3';
    const style4 = 'poly-pagination-style-4';
    const style5 = 'poly-pagination-style-5';
    const style6 = 'poly-pagination-style-6';
    const style7 = 'poly-pagination-style-7';
}

//Enum simulated classes
abstract class POLYOption{

    //Styles & Effects
    const kLayoutType = "kLayoutType";
    const kViewerType = "kViewerType";

    const kDetailsDisplayStyle = "kDetailsDisplayStyle";
    const kPictureHoverEffect = "kPictureHoverEffect";
    const kOverlayDisplayStyle = "kOverlayDisplayStyle";
    const kOverlayButtonsDisplayStyle = "kOverlayButtonsDisplayStyle";
    const kOverlayButtonsHoverEffect = "kOverlayButtonsHoverEffect";
    const kShareButtonsDisplayStyle = "kShareButtonsDisplayStyle";

    //Quality
    const kThumbnailQuality = "kThumbnailQuality";

    //Category filtration
    const kShowCategoryFilters = "kShowCategoryFilters";
    const kFilterStyle = "kFilterStyle";

    //Overlay items
    const kShowTitle = "kShowTitle";
    const kShowDesc = "kShowDesc";
    const kShowOverlay = "kShowOverlay";
    const kShowLinkButton = "kShowLinkButton";
    const kShowExploreButton = "kShowExploreButton";
    const kShowFacebookButton = "kShowFacebookButton";
    const kShowTwitterButton = "kShowTwitterButton";
    const kShowGooglePlusButton = "kShowGooglePlusButton";
    const kShowPinterestButton = "kShowPinterestButton";

    const kLinkIcon = "kLinkIcon";
    const kZoomIcon = "kZoomIcon";
    const kGoIcon = "kGoIcon";

    //Dimensions
    const kLayoutWidth = "kLayoutWidth";
    const kLayoutWidthUnit = "kLayoutWidthUnit";
    const kTileApproxWidth = "kTileApproxWidth";
    const kTileApproxHeight = "kTileApproxHeight";
    const kTileMinWidth = "kTileMinWidth";
    const kTileMargins = "kTileMargins";
    //Alignments
    const kLayoutAlignment = "kLayoutAlignment";
    //Colorization
    const kProgressColor = "kProgressColor";
    const kFiltersColor = "kFiltersColor";
    const kFiltersHoverColor = "kFiltersHoverColor";
    const kTileTitleColor = "kTileTitleColor";
    const kTileDescColor = "kTileDescColor";
    const kTileOverlayColor = "kTileOverlayColor";
    const kTileOverlayOpacity = "kTileOverlayOpacity";
    const kTileIconsColor = "kTileIconsColor";
    const kTileIconsBgColor = "kTileIconsBgColor";

    //Fonts
    const kTileTitleFontSize = "kTileTitleFontSize";
    const kTileDescFontSize = "kTileDescFontSize";
    const kTileTitleAlignment = "kTileTitleAlignment";
    const kTileDescAlignment = "kTileDescAlignment";

    //Other
    const kDirectLinking = "kDirectLinking";
    const kMouseType = "kMouseType";
    const kDescMaxLength = "kDescMaxLength";
    const kLinkTarget = "kLinkTarget";
    const kDisableAlbumStylePresentation = "kDisableAlbumStylePresentation";
    const kEnablePictureCaptions = "kEnablePictureCaptions";
    const kExcludeCoverPicture = "kExcludeCoverPicture";
    const kEnableGridLazyLoad = "kEnableGridLazyLoad";
    const kHideAllCategoryFilter = "kHideAllCategoryFilter";
    const kAllCategoryAlias = "kAllCategoryAlias";
    const kLoadUrlBlank = "kLoadUrlBlank";

    //Pagination
    const kItemsPerPage = "kItemsPerPage";
    const kMaxVisiblePageNumbers = "kMaxVisiblePageNumbers";
    const kEnablePagination = "kEnablePagination";
    const kPaginationAlignment = "kPaginationAlignment";
    const kPaginationStyle = "kPaginationStyle";
    const kPaginationColor = "kPaginationColor";
    const kPaginationHoverColor = "kPaginationHoverColor";
    const kItemsPerPageDefault = 15;
    const kMaxVisiblePageNumbersDefault = 15;

    //Customize CSS & JS
    const kCustomCSS = "kCustomCSS";
    const kCustomJS = "kCustomJS";

    //Extanded options
}

?>
