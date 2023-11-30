<?php

function wp_ajax_poly_get_schedule(){
    global $wpdb;
    $response = new stdClass();

    if(!isset($_GET['id'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid schedule identifier!';
        poly_ajax_return($response);
    }

    $pid = (int)$_GET['id'];
    $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_SCHEDULES." WHERE id = %d", $pid);
    $res = $wpdb->get_results( $query , OBJECT );

    if(count($res)){
        $schedule = $res[0];

        $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_PROJECTS." WHERE pid = %d", $pid);
        $res = $wpdb->get_results( $query , OBJECT );

        $projects = array();
        foreach($res as $project) {
            if (!empty($project->categories)) {
                $project->categories = explode(',', $project->categories);
            } else {
                $project->categories = array();
            }

            if(!empty($project->details)) {
                $project->details = json_decode($project->details, true);
            }

            $projects[$project->id] = $project;

            $picJson = json_decode(base64_decode($project->cover));
            $picId = $picJson ? $picJson->id : '';
            $picInfo = $picId ? POLYHelper::getAttachementMeta($picId, "medium") : '';
            $pic = array(
                "id" => $picId,
                "src" => $picInfo ? $picInfo["src"] : '',
            );
            $project->cover = base64_encode(json_encode($pic));

            $pics = array();
            if ($project->pics && !empty($project->pics)) {
                $exp = explode(",", $project->pics);
                foreach ($exp as $item) {
                    $picJson = json_decode(base64_decode($item));
                    $picId = $picJson ? $picJson->id : '';
                    $picInfo = $picId ? POLYHelper::getAttachementMeta($picId, "medium") : '';
                    $pic = array(
                        "id" => $picId,
                        "src" => $picInfo ? $picInfo["src"] : '',
                    );

                    $pics[] = base64_encode(json_encode($pic));
                }
            }
            $project->pics = implode(",", $pics);
        }

        $schedule->projects = $projects;
        $schedule->corder = explode(',',$schedule->corder);
        $schedule->options = json_decode( str_replace('\"', '"', $schedule->options), true);

        $response->status = 'success';
        $response->schedule = $schedule;
    }else{
        $response->status = 'error';
        $response->errormsg = 'Unknown schedule identifier!';
    }

    poly_ajax_return($response);
}

// function wp_ajax_poly_get_schedule(){
//     global $wpdb;
//     $response = new stdClass();

//     if(!isset($_GET['id'])){
//         $response->status = 'error';
//         $response->errormsg = 'Invalid schedule identifier!';
//         poly_ajax_return($response);
//     }

//     $pid = (int)$_GET['id'];
//     $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_SCHEDULES." WHERE id = %d", $pid);
//     $res = $wpdb->get_results( $query , OBJECT );

//     if(count($res)){
//         $schedule = $res[0];

//         $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_PROJECTS." WHERE pid = %d", $pid);
//         $res = $wpdb->get_results( $query , OBJECT );

//         $projects = array();
//         foreach($res as $project) {
//             if (!empty($project->categories)) {
//                 $project->categories = explode(',', $project->categories);
//             } else {
//                 $project->categories = array();
//             }

//             if(!empty($project->details)) {
//                 $project->details = json_decode($project->details, true);
//             }

//             $projects[$project->id] = $project;

//             $picJson = json_decode(base64_decode($project->cover));
//             $picId = $picJson ? $picJson->id : '';
//             $picInfo = $picId ? POLYHelper::getAttachementMeta($picId, "medium") : '';
//             $pic = array(
//                 "id" => $picId,
//                 "src" => $picInfo ? $picInfo["src"] : '',
//             );
//             $project->cover = base64_encode(json_encode($pic));

//             $pics = array();
//             if ($project->pics && !empty($project->pics)) {
//                 $exp = explode(",", $project->pics);
//                 foreach ($exp as $item) {
//                     $picJson = json_decode(base64_decode($item));
//                     $picId = $picJson ? $picJson->id : '';
//                     $picInfo = $picId ? POLYHelper::getAttachementMeta($picId, "medium") : '';
//                     $pic = array(
//                         "id" => $picId,
//                         "src" => $picInfo ? $picInfo["src"] : '',
//                     );

//                     $pics[] = base64_encode(json_encode($pic));
//                 }
//             }
//             $project->pics = implode(",", $pics);
//         }

//         $schedule->projects = $projects;
//         $schedule->corder = explode(',',$schedule->corder);
//         $schedule->options = json_decode( str_replace('\"', '"', $schedule->options), true);

//         $response->status = 'success';
//         $response->schedule = $schedule;
//     }else{
//         $response->status = 'error';
//         $response->errormsg = 'Unknown schedule identifier!';
//     }

//     poly_ajax_return($response);
// }

function wp_ajax_poly_save_schedule() {
    global $wpdb;
    $response = new stdClass();

    if(!isset($_POST['data'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid schedule passed!';
        poly_ajax_return($response);
    }

    //Convert to stdClass object
    $schedule = json_decode( stripslashes( $_POST['data']), true);
    $pid = isset($schedule['id']) ? (int)$schedule['id'] : 0;

    $corder = "";
    if (isset($schedule['corder'])) {
      $corder = array_map('intval', $schedule['corder']);
      $corder = implode(',',$schedule['corder']);
    }

    $type = ((isset($schedule['extoptions']) && isset($schedule['extoptions']['type'])) ? $schedule['extoptions']['type'] : POLYGridType::ALBUM);
    $type =  filter_var($type, FILTER_SANITIZE_STRING);
    $extOptions = array(
        'type' => $type
    );

    //Insert if schedule is draft yet
    if(isset($schedule['isDraft']) && (int)$schedule['isDraft']){
        $title = isset($schedule['title']) ? filter_var($schedule['title'], FILTER_SANITIZE_STRING) : "";

        $wpdb->insert(
            POLY_TABLE_SCHEDULES,
            array(
                'title' => $title,
            ),
            array(
                '%s',
            )
        );

        //Get real identifier and use it instead of draft identifier for tmp usage
        $pid = $wpdb->insert_id;
    }

    $projects = isset($schedule['projects']) ? $schedule['projects'] : array();
    foreach($projects as $id => $project){
        $cover = isset($project['cover']) ? $project['cover'] : "";
        if (empty($cover)) {
            continue;
        }
        if (empty(POLYHelper::validatedBase64String($cover))) {
            continue;
        }

        //Any custom HTML content is permitted for title, description
        $title = isset($project['title']) ? $project['title'] : "";
        $description = isset($project['description']) ? $project['description'] : "";

        $url = isset($project['url']) ? filter_var($project['url'], FILTER_VALIDATE_URL) : "";
        $pics = isset($project['pics']) && POLYHelper::validatedBase64String($project['pics']) ? $project['pics'] : "";

        //Caretories are not supported in Free version
        $cats = "";

        $details = isset($project['details']) ? $project['details'] : '';
        $details = json_encode($details);

        if(isset($project['isDraft']) && $project['isDraft']){
            $wpdb->insert(
                POLY_TABLE_PROJECTS,
                array(
                    'title' => $title,
                    'pid' => $pid,
                    'cover' => $cover,
                    'description' => $description,
                    'url' => $url,
                    'pics' => $pics,
                    'categories' => $cats,
                    'details' => $details
                ),
                array(
                    '%s',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                )
            );

            $realProjId = $wpdb->insert_id;
            $corder = str_replace($id,$realProjId,$corder);
        }else{
            $wpdb->update(
                POLY_TABLE_PROJECTS,
                array(
                    'title' => $title,
                    'cover' => $cover,
                    'description' => $description,
                    'url' => $url,
                    'pics' => $pics,
                    'categories' => $cats,
                    'details' => $details
                ),
                array( 'id' => $id ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                ),
                array( '%d' )
            );
        }
    }


    $deletions = isset($schedule['deletions']) ? $schedule['deletions'] : array();
    $deletions = array_map('intval', $deletions);

    foreach($deletions as $deletedProjectId) {
        // Default usage.
        $wpdb->delete( POLY_TABLE_PROJECTS, array( 'id' => $deletedProjectId ) );
    }

    $title = isset($schedule['title']) ? filter_var($schedule['title'], FILTER_SANITIZE_STRING) : "";
    $extOptions = json_encode($extOptions);

    $wpdb->update(
        POLY_TABLE_SCHEDULES,
        array(
            'title' => $title,
            'corder' => $corder,
            'extoptions' => $extOptions
        ),
        array( 'id' => $pid ),
        array(
            '%s',
            '%s',
            '%s'
        ),
        array( '%d' )
    );

    $response->status = 'success';
    $response->pid = $pid;
    poly_ajax_return($response);
}


//Helper functions
function poly_ajax_return( $response ){
    echo  json_encode( $response );
    die();
}

function wp_ajax_poly_get_competition_names(){
    global $wpdb;
    $response = new stdClass();

    if(!isset($_GET['id'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid competition name identifier!';
        poly_ajax_return($response);
    }

    $pid = (int)$_GET['id'];
    $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_COMPETITION_NAMES." WHERE ".POLY_TABLE_COMPETITION_NAMES_ID." = %d", $pid);
    $res = $wpdb->get_results( $query , OBJECT );

    if(count($res)){
        $schedule = $res[0];
        $response->status = 'success';
        $response->schedule = $schedule;
    }else{
        $response->status = 'error';
        $response->errormsg = 'Unknown competition name identifier!';
    }

    poly_ajax_return($response);
}

function wp_ajax_poly_save_competition_names() {
    global $wpdb;
    $response = new stdClass();

    if(!isset($_POST['data'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid competition names passed!';
        poly_ajax_return( $response );
    }

    //Convert to stdClass object
    $schedule = json_decode( stripslashes( $_POST['data']), true );

    $pid = isset($schedule[POLY_TABLE_COMPETITION_NAMES_ID]) ? (int)$schedule[POLY_TABLE_COMPETITION_NAMES_ID] : 0;

    //Insert if schedule is draft yet
    if(isset($schedule['isDraft']) && (int)$schedule['isDraft']){
        $name = isset($schedule['name']) ? filter_var($schedule['name'], FILTER_SANITIZE_STRING) : "";

        $wpdb->insert(
            POLY_TABLE_COMPETITION_NAMES,
            array(
                'name' => $name,
            ),
            array(
                '%s',
            )
        );

        //Get real identifier and use it instead of draft identifier for tmp usage
        $pid = $wpdb->insert_id;
    }

    $name = isset($schedule['name']) ? filter_var($schedule['name'], FILTER_SANITIZE_STRING) : "";

    $wpdb->update(
        POLY_TABLE_COMPETITION_NAMES,
        array(
            'name' => $name,
        ),
        array( POLY_TABLE_COMPETITION_NAMES_ID => $pid ),
        array(
            '%s',
        ),
        array(
            '%d'
        )
    );

    $response->status = 'success';
    $response->pid = $pid;
    poly_ajax_return($response);
}

function wp_ajax_poly_get_competition_name_list(){
    global $wpdb;
    $response = new stdClass();

    $query = "SELECT * FROM ".COMPETITION_NAMES;
    $res = $wpdb->get_results( $query , OBJECT );

    $competition_names = array();

    foreach( $res as $competition_name ){
        $competition_names[$competition_name->competition_name_id] = $competition_name;
    }

    if( count($res) ){
        $response->status = 'success';
        $response->schedule = $competition_names;
    }else{
        $response->status = 'error';
        $response->errormsg = 'Competition name list is empty!';
    }

    presi_ajax_return($response);
}

function wp_ajax_poly_get_schedules(){
    global $wpdb;
    $response = new stdClass();

    if(!isset($_GET['id'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid schedule name identifier!';
        poly_ajax_return($response);
    }

    $pid = (int)$_GET['id'];
    $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_SCHEDULES." WHERE ".POLY_TABLE_SCHEDULES_ID." = %d", $pid);
    $res = $wpdb->get_results( $query , OBJECT );

    if( count($res) ){
        $schedule = $res[0];

        // $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_SCHEDULE_ITEMS." WHERE ".POLY_TABLE_SCHEDULES_ID." = %d ORDER BY ".POLY_TABLE_SCHEDULE_ITEMS_ID." DESC" , $pid);
        $query = $wpdb->prepare(
        "SELECT ".POLY_TABLE_SCHEDULE_ITEMS.".*
      FROM ".POLY_TABLE_SCHEDULE_ITEMS."
      WHERE ".POLY_TABLE_SCHEDULE_ITEMS.".".POLY_TABLE_SCHEDULES_ID." = %d ORDER BY ".POLY_TABLE_SCHEDULE_ITEMS.".".POLY_TABLE_SCHEDULE_ITEMS_ID." DESC"
      , $pid);

        $res = $wpdb->get_results( $query , OBJECT );

        $schedule_items = array();
        foreach($res as $schedule_item) {
            $schedule_items[$schedule_item->schedule_item_id] = $schedule_item;
        }

        $schedule->schedule_items = $schedule_items;

        $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_COMPETITION_NAMES);
        $res = $wpdb->get_results( $query , OBJECT );

        $competition_names = array();
        foreach($res as $competition_name) {
            $competition_names[$competition_name->competition_name_id] = $competition_name;
        }

        $schedule->competition_names = $competition_names;

        $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_COMPETITION_STAGE_NAMES);
        $res = $wpdb->get_results( $query , OBJECT );

        $competition_stage_names = array();
        foreach($res as $competition_stage_name) {
            $competition_stage_names[$competition_stage_name->competition_stage_name_id] = $competition_stage_name;
        }

        $schedule->competition_stage_names = $competition_stage_names;

        $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_SPORT_DISCIPLINE_NAMES);
        $res = $wpdb->get_results( $query , OBJECT );

        $sport_discipline_names = array();
        foreach($res as $sport_discipline_name) {
            $sport_discipline_names[$sport_discipline_name->sport_discipline_name_id] = $sport_discipline_name;
        }

        $schedule->discipline_names = $sport_discipline_names;

        $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_STAGES);
        $res = $wpdb->get_results( $query , OBJECT );

        $stages = array();
        foreach($res as $stage) {
            $stages[$stage->stage_id] = $stage;
        }

        $schedule->stages = $stages;

        $query = $wpdb->prepare(
            "SELECT "
                .POLY_TABLE_CITIES.".*,"
                .POLY_TABLE_COUNTRIES.".name AS country_name
            FROM ".POLY_TABLE_COUNTRIES."
                INNER JOIN ".POLY_TABLE_CITIES."
                    ON ".POLY_TABLE_COUNTRIES.".".POLY_TABLE_COUNTRIES_ID." = ".POLY_TABLE_CITIES.".".POLY_TABLE_COUNTRIES_ID
        );

        $res = $wpdb->get_results( $query , OBJECT );

        $cities = array();
        foreach( $res as $city ){
            $cities[$city->city_id] = $city;
        }

        $schedule->cities = $cities;

        $query = $wpdb->prepare(
            "SELECT "
                .POLY_TABLE_AGE_GROUPS.".*,"
                .POLY_TABLE_AGE_GROUP_NAMES.".name
            FROM ".POLY_TABLE_AGE_GROUP_NAMES."
                INNER JOIN ".POLY_TABLE_AGE_GROUPS."
                    ON ".POLY_TABLE_AGE_GROUPS.".".POLY_TABLE_AGE_GROUP_NAMES_ID." = ".POLY_TABLE_AGE_GROUP_NAMES.".".POLY_TABLE_AGE_GROUP_NAMES_ID
        );

        $res = $wpdb->get_results( $query , OBJECT );

        $age_groups = array();
        foreach( $res as $age_group ){
            $age_groups[$age_group->age_group_id] = $age_group;
        }

        $schedule->age_groups = $age_groups;

        $response->status = 'success';
        $response->schedule = $schedule;
    }else{
        $response->status = 'error';
        $response->errormsg = 'Unknown schedule identifier!';
    }

    poly_ajax_return($response);
}

function wp_ajax_poly_save_schedules() {
    global $wpdb;
    $response = new stdClass();

    if(!isset($_POST['data'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid schedule passed!';
        poly_ajax_return( $response );
    }

    //Convert to stdClass object
    $schedule = json_decode( stripslashes( $_POST['data']), true );

    $pid = isset($schedule[POLY_TABLE_SCHEDULES_ID]) ? (int)$schedule[POLY_TABLE_SCHEDULES_ID] : 0;

    //Insert if schedule is draft yet
    if(isset($schedule['isDraft']) && (int)$schedule['isDraft']){
        $name = isset($schedule['name']) ? filter_var($schedule['name'], FILTER_SANITIZE_STRING) : "";

        $wpdb->insert(
            POLY_TABLE_SCHEDULES,
            array(
                'name' => $name,
            ),
            array(
                '%s',
            )
        );

        //Get real identifier and use it instead of draft identifier for tmp usage
        $pid = $wpdb->insert_id;
    }

    $schedule_items = isset($schedule['schedule_items']) ? $schedule['schedule_items'] : array();

    foreach($schedule_items as $id => $schedule_item){

        //Any custom HTML content is permitted for title, description
        $number = isset($schedule_item['number']) ? $schedule_item['number'] : "";
        $number_ekp = isset($schedule_item['number_ekp']) ? $schedule_item['number_ekp'] : "";
        $competition_name_id = isset($schedule_item['competition_name_id']) ? $schedule_item['competition_name_id'] : "";
        $city_id = isset($schedule_item['city_id']) ? $schedule_item['city_id'] : "";
        $disciplines = isset($schedule_item['disciplines']) ? $schedule_item['disciplines'] : "";
        $stage_id = isset($schedule_item['stage_id']) ? $schedule_item['stage_id'] : "";
        $age_groups = isset($schedule_item['age_groups']) ? $schedule_item['age_groups'] : "";
        $start_date = isset($schedule_item['start_date']) ? $schedule_item['start_date'] : "";
        $end_date = isset($schedule_item['end_date']) ? $schedule_item['end_date'] : "";
        $image = isset($schedule_item['image']) ? $schedule_item['image'] : "";
        $regulations = isset($schedule_item['regulations']) ? $schedule_item['regulations'] : "";
        $protocol = isset($schedule_item['protocol']) ? $schedule_item['protocol'] : "";
        $details = isset($schedule_item['details']) ? $schedule_item['details'] : "";

        if( isset($schedule_item['isDraft']) && $schedule_item['isDraft'] ){
            if ($stage_id=="") {
            $wpdb->insert(
                POLY_TABLE_SCHEDULE_ITEMS,
                array(
                    'number' => $number,
                    'number_ekp' => $number_ekp,
                    'schedule_id' => $pid,
                    'competition_name_id' => $competition_name_id,
                    'city_id' => $city_id,
                    'disciplines' => $disciplines,
                    'age_groups' => $age_groups,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'image' => $image,
                    'regulations' => $regulations,
                    'protocol' => $protocol,
                    'details' => $details,
                ),
                array(
                    '%d',
                    '%s',
                    '%d',
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                )
            );
            }
            else {
                $wpdb->insert(
                    POLY_TABLE_SCHEDULE_ITEMS,
                    array(
                        'number' => $number,
                        'number_ekp' => $number_ekp,
                        'schedule_id' => $pid,
                        'competition_name_id' => $competition_name_id,
                        'city_id' => $city_id,
                        'disciplines' => $disciplines,
                        'stage_id' => $stage_id,
                        'age_groups' => $age_groups,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'image' => $image,
                        'regulations' => $regulations,
                        'protocol' => $protocol,
                        'details' => $details,
                    ),
                    array(
                        '%d',
                        '%s',
                        '%d',
                        '%d',
                        '%d',
                        '%s',
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                    )
                );

            }
            $realScheduleItemId = $wpdb->insert_id;
        } else {
            if ($stage_id=="") {
                $wpdb->update (
                    POLY_TABLE_SCHEDULE_ITEMS,
                    array(
                        'number' => $number,
                        'number_ekp' => $number_ekp,
                        'schedule_id' => $pid,
                        'competition_name_id' => $competition_name_id,
                        'city_id' => $city_id,
                        'disciplines' => $disciplines,
                        'age_groups' => $age_groups,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'image' => $image,
                        'regulations' => $regulations,
                        'protocol' => $protocol,
                        'details' => $details,
                    ),
                    array( POLY_TABLE_SCHEDULE_ITEMS_ID => $id ),
                    array(
                        '%d',
                        '%s',
                        '%d',
                        '%d',
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                    ),
                    array(
                        '%d',
                    )
                );
            }
            else {
                $wpdb->update(
                    POLY_TABLE_SCHEDULE_ITEMS,
                    array(
                        'number' => $number,
                        'number_ekp' => $number_ekp,
                        'schedule_id' => $pid,
                        'competition_name_id' => $competition_name_id,
                        'city_id' => $city_id,
                        'disciplines' => $disciplines,
                        'stage_id' => $stage_id,
                        'age_groups' => $age_groups,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'image' => $image,
                        'regulations' => $regulations,
                        'protocol' => $protocol,
                        'details' => $details,
                    ),
                    array( POLY_TABLE_SCHEDULE_ITEMS_ID => $id ),
                    array(
                        '%d',
                        '%s',
                        '%d',
                        '%d',
                        '%d',
                        '%s',
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                    ),
                    array(
                        '%d',
                    )
                );
            }
        }
    }


    $deletions = isset($schedule['deletions']) ? $schedule['deletions'] : array();
    $deletions = array_map('intval', $deletions);

    foreach($deletions as $deletedScheduleItemId) {
        // Default usage.
        $wpdb->delete(
            POLY_TABLE_SCHEDULE_ITEMS,
            array(
                POLY_TABLE_SCHEDULE_ITEMS_ID => $deletedScheduleItemId,
            )
        );
    }

    $name = isset($schedule['name']) ? filter_var($schedule['name'], FILTER_SANITIZE_STRING) : "";

    $wpdb->update(
        POLY_TABLE_SCHEDULES,
        array(
            'name' => $name,
        ),
        array( POLY_TABLE_SCHEDULES_ID => $pid ),
        array(
            '%s',
        ),
        array( '%d' )
    );

    $response->status = 'success';
    $response->pid = $pid;
    poly_ajax_return($response);
}

function wp_ajax_poly_get_schedule_items(){
    global $wpdb;
    $response = new stdClass();

    if(!isset($_GET['id'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid schedule items identifier!';
        poly_ajax_return($response);
    }

    $pid = (int)$_GET['id'];
    $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_SCHEDULE_ITEMS." WHERE ".POLY_TABLE_SCHEDULE_ITEMS_ID."= %d", $pid);
    $res = $wpdb->get_results( $query , OBJECT );

    if(count($res)){
        $schedule = $res[0];
        $response->status = 'success';
        $response->schedule = $schedule;
    }else{
        $response->status = 'error';
        $response->errormsg = 'Unknown schedule items identifier!';
    }

    poly_ajax_return($response);
}

function wp_ajax_poly_save_schedule_items() {
    global $wpdb;
    $response = new stdClass();

    if(!isset($_POST['schedule'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid schedule passed!';
        poly_ajax_return($response);
    }

    //Convert to stdClass object
    $schedule = json_decode( stripslashes( $_POST['schedule']), true);
    $pid = isset($schedule['id']) ? (int)$schedule['id'] : 0;

    $corder = "";
    if (isset($schedule['corder'])) {
      $corder = array_map('intval', $schedule['corder']);
      $corder = implode(',',$schedule['corder']);
    }

    $type = ((isset($schedule['extoptions']) && isset($schedule['extoptions']['type'])) ? $schedule['extoptions']['type'] : POLYGridType::SCHEDULE_ITEMS);
    $type =  filter_var($type, FILTER_SANITIZE_STRING);
    $extOptions = array(
        'type' => $type
    );

    //Insert if schedule is draft yet
    if(isset($schedule['isDraft']) && (int)$schedule['isDraft']){
        $title = isset($schedule['title']) ? filter_var($schedule['title'], FILTER_SANITIZE_STRING) : "";

        $wpdb->insert(
            POLY_TABLE_SCHEDULE_ITEMS,
            array(
                'title' => $title,
            ),
            array(
                '%s',
            )
        );

        //Get real identifier and use it instead of draft identifier for tmp usage
        $pid = $wpdb->insert_id;
    }

    $title = isset($schedule['title']) ? filter_var($schedule['title'], FILTER_SANITIZE_STRING) : "";
    $extOptions = json_encode($extOptions);

    $wpdb->update(
        POLY_TABLE_SCHEDULE_ITEMS,
        array(
            'title' => $title,
            'corder' => $corder,
            'extoptions' => $extOptions
        ),
        array( 'id' => $pid ),
        array(
            '%s',
            '%s',
            '%s'
        ),
        array( '%d' )
    );

    $response->status = 'success';
    $response->pid = $pid;
    poly_ajax_return($response);
}

function wp_ajax_poly_get_sport_disciplines(){
    global $wpdb;
    $response = new stdClass();

    if(!isset($_GET['id'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid sport disciplines identifier!';
        poly_ajax_return($response);
    }

    $pid = (int)$_GET['id'];
    $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_SPORT_DISCIPLINES." WHERE ".POLY_TABLE_SPORT_DISCIPLINES_ID."= %d", $pid);
    $res = $wpdb->get_results( $query , OBJECT );

    if(count($res)){
        $schedule = $res[0];
        $response->status = 'success';
        $response->schedule = $schedule;
    }else{
        $response->status = 'error';
        $response->errormsg = 'Unknown sport disciplines identifier!';
    }

    poly_ajax_return($response);
}

function wp_ajax_poly_save_sport_disciplines() {
    global $wpdb;
    $response = new stdClass();

    if(!isset($_POST['schedule'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid schedule passed!';
        poly_ajax_return($response);
    }

    //Convert to stdClass object
    $schedule = json_decode( stripslashes( $_POST['schedule']), true);
    $pid = isset($schedule['id']) ? (int)$schedule['id'] : 0;

    $corder = "";
    if (isset($schedule['corder'])) {
      $corder = array_map('intval', $schedule['corder']);
      $corder = implode(',',$schedule['corder']);
    }

    $type = ((isset($schedule['extoptions']) && isset($schedule['extoptions']['type'])) ? $schedule['extoptions']['type'] : POLYGridType::SPORT_DISCIPLINES);
    $type =  filter_var($type, FILTER_SANITIZE_STRING);
    $extOptions = array(
        'type' => $type
    );

    //Insert if schedule is draft yet
    if(isset($schedule['isDraft']) && (int)$schedule['isDraft']){
        $title = isset($schedule['title']) ? filter_var($schedule['title'], FILTER_SANITIZE_STRING) : "";

        $wpdb->insert(
            POLY_TABLE_SPORT_DISCIPLINES,
            array(
                'title' => $title,
            ),
            array(
                '%s',
            )
        );

        //Get real identifier and use it instead of draft identifier for tmp usage
        $pid = $wpdb->insert_id;
    }

    $title = isset($schedule['title']) ? filter_var($schedule['title'], FILTER_SANITIZE_STRING) : "";
    $extOptions = json_encode($extOptions);

    $wpdb->update(
        POLY_TABLE_SPORT_DISCIPLINES,
        array(
            'title' => $title,
            'corder' => $corder,
            'extoptions' => $extOptions
        ),
        array( 'id' => $pid ),
        array(
            '%s',
            '%s',
            '%s'
        ),
        array( '%d' )
    );

    $response->status = 'success';
    $response->pid = $pid;
    poly_ajax_return($response);
}

function wp_ajax_poly_get_sport_discipline_names(){
    global $wpdb;
    $response = new stdClass();

    if(!isset($_GET['id'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid sport discipline names identifier!';
        poly_ajax_return($response);
    }

    $pid = (int)$_GET['id'];
    $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_SPORT_DISCIPLINE_NAMES." WHERE ".POLY_TABLE_SPORT_DISCIPLINE_NAMES_ID."= %d", $pid);
    $res = $wpdb->get_results( $query , OBJECT );

    if(count($res)){
        $schedule = $res[0];
        $response->status = 'success';
        $response->schedule = $schedule;
    }else{
        $response->status = 'error';
        $response->errormsg = 'Unknown sport discipline names identifier!';
    }

    poly_ajax_return($response);
}

function wp_ajax_poly_save_sport_discipline_names() {
    global $wpdb;
    $response = new stdClass();

    if(!isset($_POST['data'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid schedule passed!';
        poly_ajax_return($response);
    }

    //Convert to stdClass object
    $schedule = json_decode( stripslashes( $_POST['data']), true);

    $pid = isset($schedule[POLY_TABLE_SPORT_DISCIPLINE_NAMES_ID]) ? (int)$schedule[POLY_TABLE_SPORT_DISCIPLINE_NAMES_ID] : 0;

    //Insert if schedule is draft yet
    if(isset($schedule['isDraft']) && (int)$schedule['isDraft']){
        $name = isset($schedule['name']) ? filter_var($schedule['name'], FILTER_SANITIZE_STRING) : "";
        $abbreviated_name = isset($schedule['abbreviated_name']) ? filter_var($schedule['abbreviated_name'], FILTER_SANITIZE_STRING) : "";

        $wpdb->insert(
            POLY_TABLE_SPORT_DISCIPLINE_NAMES,
            array(
                'name' => $name,
                'abbreviated_name' => $abbreviated_name,
            ),
            array(
                '%s',
                '%s',
            )
        );

        //Get real identifier and use it instead of draft identifier for tmp usage
        $pid = $wpdb->insert_id;
    }

    $name = isset($schedule['name']) ? filter_var($schedule['name'], FILTER_SANITIZE_STRING) : "";
    $abbreviated_name = isset($schedule['abbreviated_name']) ? filter_var($schedule['abbreviated_name'], FILTER_SANITIZE_STRING) : "";

    $wpdb->update(
        POLY_TABLE_SPORT_DISCIPLINE_NAMES,
        array(
            'name' => $name,
            'abbreviated_name' => $abbreviated_name,
        ),
        array( POLY_TABLE_SPORT_DISCIPLINE_NAMES_ID => $pid ),
        array(
            '%s',
            '%s',
        ),
        array( '%d' )
    );

    $response->status = 'success';
    $response->pid = $pid;
    poly_ajax_return($response);
}

function wp_ajax_poly_get_competition_stages(){
    global $wpdb;
    $response = new stdClass();

    if(!isset($_GET['id'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid competition stages identifier!';
        poly_ajax_return($response);
    }

    $pid = (int)$_GET['id'];
    $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_COMPETITION_STAGES." WHERE ".POLY_TABLE_COMPETITION_STAGES_ID."= %d", $pid);
    $res = $wpdb->get_results( $query , OBJECT );

    if( count($res) ){
        $schedule = $res[0];
        $response->status = 'success';
        $response->schedule = $schedule;
    }else{
        $response->status = 'error';
        $response->errormsg = 'Unknown competition stages identifier!';
    }

    poly_ajax_return($response);
}

function wp_ajax_poly_save_competition_stages() {
    global $wpdb;
    $response = new stdClass();

    if(!isset($_POST['schedule'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid schedule passed!';
        poly_ajax_return($response);
    }

    //Convert to stdClass object
    $schedule = json_decode( stripslashes( $_POST['schedule']), true);
    $pid = isset($schedule['id']) ? (int)$schedule['id'] : 0;

    $corder = "";
    if (isset($schedule['corder'])) {
      $corder = array_map('intval', $schedule['corder']);
      $corder = implode(',',$schedule['corder']);
    }

    $type = ((isset($schedule['extoptions']) && isset($schedule['extoptions']['type'])) ? $schedule['extoptions']['type'] : POLYGridType::COMPETITION_STAGES);
    $type =  filter_var($type, FILTER_SANITIZE_STRING);
    $extOptions = array(
        'type' => $type
    );

    //Insert if schedule is draft yet
    if(isset($schedule['isDraft']) && (int)$schedule['isDraft']){
        $title = isset($schedule['title']) ? filter_var($schedule['title'], FILTER_SANITIZE_STRING) : "";

        $wpdb->insert(
            POLY_TABLE_COMPETITION_STAGES,
            array(
                'title' => $title,
            ),
            array(
                '%s',
            )
        );

        //Get real identifier and use it instead of draft identifier for tmp usage
        $pid = $wpdb->insert_id;
    }

    $title = isset($schedule['title']) ? filter_var($schedule['title'], FILTER_SANITIZE_STRING) : "";
    $extOptions = json_encode($extOptions);

    $wpdb->update(
        POLY_TABLE_COMPETITION_STAGES,
        array(
            'title' => $title,
            'corder' => $corder,
            'extoptions' => $extOptions
        ),
        array( 'id' => $pid ),
        array(
            '%s',
            '%s',
            '%s'
        ),
        array( '%d' )
    );

    $response->status = 'success';
    $response->pid = $pid;
    poly_ajax_return($response);
}

function wp_ajax_poly_get_competition_stage_names(){
    global $wpdb;
    $response = new stdClass();

    if(!isset($_GET['id'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid competition stage names identifier!';
        poly_ajax_return($response);
    }

    $pid = (int)$_GET['id'];
    $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_COMPETITION_STAGE_NAMES." WHERE ".POLY_TABLE_COMPETITION_STAGE_NAMES_ID."= %d", $pid);
    $res = $wpdb->get_results( $query , OBJECT );

    if(count($res)){
        $schedule = $res[0];
        $response->status = 'success';
        $response->schedule = $schedule;
    }else{
        $response->status = 'error';
        $response->errormsg = 'Unknown competition stage names identifier!';
    }

    poly_ajax_return($response);
}

function wp_ajax_poly_save_competition_stage_names() {
    global $wpdb;
    $response = new stdClass();

    if(!isset($_POST['data'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid competition stage names passed!';
        poly_ajax_return( $response );
    }

    //Convert to stdClass object
    $schedule = json_decode( stripslashes( $_POST['data']), true );

    $pid = isset($schedule[POLY_TABLE_COMPETITION_STAGE_NAMES_ID]) ? (int)$schedule[POLY_TABLE_COMPETITION_STAGE_NAMES_ID] : 0;

    //Insert if schedule is draft yet
    if(isset($schedule['isDraft']) && (int)$schedule['isDraft']){
        $name = isset($schedule['name']) ? filter_var($schedule['name'], FILTER_SANITIZE_STRING) : "";

        $wpdb->insert(
            POLY_TABLE_COMPETITION_STAGE_NAMES,
            array(
                'name' => $name,
            ),
            array(
                '%s',
            )
        );

        //Get real identifier and use it instead of draft identifier for tmp usage
        $pid = $wpdb->insert_id;
    }

    $name = isset($schedule['name']) ? filter_var($schedule['name'], FILTER_SANITIZE_STRING) : "";

    $wpdb->update(
        POLY_TABLE_COMPETITION_STAGE_NAMES,
        array(
            'name' => $name,
        ),
        array( POLY_TABLE_COMPETITION_STAGE_NAMES_ID => $pid ),
        array(
            '%s',
        ),
        array(
            '%d'
        )
    );

    $response->status = 'success';
    $response->pid = $pid;
    poly_ajax_return($response);
}

function wp_ajax_poly_get_age_groups(){
    global $wpdb;
    $response = new stdClass();

    if(!isset($_GET['id'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid age groups identifier!';
        poly_ajax_return($response);
    }

    $pid = (int)$_GET['id'];
    $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_AGE_GROUPS." WHERE ".POLY_TABLE_AGE_GROUPS_ID."= %d", $pid);
    $res = $wpdb->get_results( $query , OBJECT );

    if(count($res)){
        $schedule = $res[0];
        $response->status = 'success';
        $response->schedule = $schedule;
    }else{
        $response->status = 'error';
        $response->errormsg = 'Unknown age groups identifier!';
    }

    poly_ajax_return($response);
}

function wp_ajax_poly_save_age_groups() {
    global $wpdb;
    $response = new stdClass();

    if(!isset($_POST['schedule'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid schedule passed!';
        poly_ajax_return($response);
    }

    //Convert to stdClass object
    $schedule = json_decode( stripslashes( $_POST['schedule']), true);
    $pid = isset($schedule['id']) ? (int)$schedule['id'] : 0;

    $corder = "";
    if (isset($schedule['corder'])) {
      $corder = array_map('intval', $schedule['corder']);
      $corder = implode(',',$schedule['corder']);
    }

    $type = ((isset($schedule['extoptions']) && isset($schedule['extoptions']['type'])) ? $schedule['extoptions']['type'] : POLYGridType::AGE_GROUPS);
    $type =  filter_var($type, FILTER_SANITIZE_STRING);
    $extOptions = array(
        'type' => $type
    );

    //Insert if schedule is draft yet
    if(isset($schedule['isDraft']) && (int)$schedule['isDraft']){
        $title = isset($schedule['title']) ? filter_var($schedule['title'], FILTER_SANITIZE_STRING) : "";

        $wpdb->insert(
            POLY_TABLE_AGE_GROUPS,
            array(
                'title' => $title,
            ),
            array(
                '%s',
            )
        );

        //Get real identifier and use it instead of draft identifier for tmp usage
        $pid = $wpdb->insert_id;
    }

    $title = isset($schedule['title']) ? filter_var($schedule['title'], FILTER_SANITIZE_STRING) : "";
    $extOptions = json_encode($extOptions);

    $wpdb->update(
        POLY_TABLE_AGE_GROUPS,
        array(
            'title' => $title,
            'corder' => $corder,
            'extoptions' => $extOptions
        ),
        array( 'id' => $pid ),
        array(
            '%s',
            '%s',
            '%s'
        ),
        array( '%d' )
    );

    $response->status = 'success';
    $response->pid = $pid;
    poly_ajax_return($response);
}

function wp_ajax_poly_get_cities(){
    global $wpdb;
    $response = new stdClass();

    if(!isset($_GET['id'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid cities identifier!';
        poly_ajax_return($response);
    }

    $pid = (int)$_GET['id'];
    $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_CITIES." WHERE ".POLY_TABLE_CITIES_ID."= %d", $pid);
    $res = $wpdb->get_results( $query , OBJECT );

    if(count($res)){
        $schedule = $res[0];
        $response->status = 'success';
        $response->schedule = $schedule;
    }else{
        $response->status = 'error';
        $response->errormsg = 'Unknown cities identifier!';
    }

    poly_ajax_return($response);
}

function wp_ajax_poly_save_cities() {
    global $wpdb;
    $response = new stdClass();

    if(!isset($_POST['schedule'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid schedule passed!';
        poly_ajax_return($response);
    }

    //Convert to stdClass object
    $schedule = json_decode( stripslashes( $_POST['schedule']), true);
    $pid = isset($schedule['id']) ? (int)$schedule['id'] : 0;

    $corder = "";
    if (isset($schedule['corder'])) {
      $corder = array_map('intval', $schedule['corder']);
      $corder = implode(',',$schedule['corder']);
    }

    $type = ((isset($schedule['extoptions']) && isset($schedule['extoptions']['type'])) ? $schedule['extoptions']['type'] : POLYGridType::CITIES);
    $type =  filter_var($type, FILTER_SANITIZE_STRING);
    $extOptions = array(
        'type' => $type
    );

    //Insert if schedule is draft yet
    if(isset($schedule['isDraft']) && (int)$schedule['isDraft']){
        $title = isset($schedule['title']) ? filter_var($schedule['title'], FILTER_SANITIZE_STRING) : "";

        $wpdb->insert(
            POLY_TABLE_CITIES,
            array(
                'title' => $title,
            ),
            array(
                '%s',
            )
        );

        //Get real identifier and use it instead of draft identifier for tmp usage
        $pid = $wpdb->insert_id;
    }

    $title = isset($schedule['title']) ? filter_var($schedule['title'], FILTER_SANITIZE_STRING) : "";
    $extOptions = json_encode($extOptions);

    $wpdb->update(
        POLY_TABLE_CITIES,
        array(
            'title' => $title,
            'corder' => $corder,
            'extoptions' => $extOptions
        ),
        array( 'id' => $pid ),
        array(
            '%s',
            '%s',
            '%s'
        ),
        array( '%d' )
    );

    $response->status = 'success';
    $response->pid = $pid;
    poly_ajax_return($response);
}

function wp_ajax_poly_get_countries(){
    global $wpdb;
    $response = new stdClass();

    if(!isset($_GET['id'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid country identifier!';
        poly_ajax_return($response);
    }

    $pid = (int)$_GET['id'];
    $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_COUNTRIES." WHERE ".POLY_TABLE_COUNTRIES_ID."= %d", $pid);
    $res = $wpdb->get_results( $query , OBJECT );


    if(count($res)){
        $schedule = $res[0];

        $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_CITIES." WHERE ".POLY_TABLE_COUNTRIES_ID." = %d", $pid);
        $res = $wpdb->get_results( $query , OBJECT );

        $cities = array();
        foreach($res as $city) {
            $cities[$city->city_id] = $city;
        }

        $schedule->cities = $cities;

        $response->status = 'success';
        $response->schedule = $schedule;
    }else{
        $response->status = 'error';
        $response->errormsg = 'Unknown countries identifier!';
    }

    poly_ajax_return($response);
}

function wp_ajax_poly_save_countries() {
    global $wpdb;
    $response = new stdClass();

    if(!isset($_POST['data'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid country passed!';
        poly_ajax_return($response);
    }

    //Convert to stdClass object
    $schedule = json_decode( stripslashes( $_POST['data']), true);

    $pid = isset($schedule[POLY_TABLE_COUNTRIES_ID]) ? (int)$schedule[POLY_TABLE_COUNTRIES_ID] : 0;

    //Insert if schedule is draft yet
    if(isset($schedule['isDraft']) && (int)$schedule['isDraft']){
        $name = isset($schedule['name']) ? filter_var($schedule['name'], FILTER_SANITIZE_STRING) : "";

        $wpdb->insert(
            POLY_TABLE_COUNTRIES,
            array(
                'name' => $name,
            ),
            array(
                '%s',
            )
        );

        //Get real identifier and use it instead of draft identifier for tmp usage
        $pid = $wpdb->insert_id;
    }

    $cities = isset($schedule['cities']) ? $schedule['cities'] : array();

    foreach($cities as $id => $city){

        //Any custom HTML content is permitted for title, description
        $name = isset($city['name']) ? $city['name'] : "";

        if(isset($city['isDraft']) && $city['isDraft']){
            $wpdb->insert(
                POLY_TABLE_CITIES,
                array(
                    'name' => $name,
                    'country_id' => $pid,
                ),
                array(
                    '%s',
                    '%d',
                )
            );

            $realCityId = $wpdb->insert_id;
        }else{
            $wpdb->update(
                POLY_TABLE_CITIES,
                array(
                    'name' => $name,
                ),
                array( POLY_TABLE_CITIES_ID => $id ),
                array(
                    '%s',
                ),
                array(
                    '%d',
                )
            );
        }
    }


    $deletions = isset($schedule['deletions']) ? $schedule['deletions'] : array();
    $deletions = array_map('intval', $deletions);

    foreach($deletions as $deletedCityId) {
        // Default usage.
        $wpdb->delete(
            POLY_TABLE_CITIES,
            array(
                POLY_TABLE_CITIES_ID => $deletedCityId,
            )
        );
    }

    $name = isset($schedule['name']) ? filter_var($schedule['name'], FILTER_SANITIZE_STRING) : "";

    $wpdb->update(
        POLY_TABLE_COUNTRIES,
        array(
            'name' => $name,
        ),
        array( POLY_TABLE_COUNTRIES_ID => $pid ),
        array(
            '%s',
        ),
        array(
            '%d',
        )
    );

    $response->status = 'success';
    $response->pid = $pid;
    poly_ajax_return($response);
}

function wp_ajax_poly_get_age_group_names(){
    global $wpdb;
    $response = new stdClass();

    if(!isset($_GET['id'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid age group name identifier!';
        poly_ajax_return($response);
    }

    $pid = (int)$_GET['id'];
    $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_AGE_GROUP_NAMES." WHERE ".POLY_TABLE_AGE_GROUP_NAMES_ID."= %d", $pid);
    $res = $wpdb->get_results( $query , OBJECT );


    if(count($res)){
        $schedule = $res[0];

        $query = $wpdb->prepare("SELECT * FROM ".POLY_TABLE_AGE_GROUPS." WHERE ".POLY_TABLE_AGE_GROUP_NAMES_ID." = %d", $pid);
        $res = $wpdb->get_results( $query , OBJECT );

        $age_groups = array();
        foreach($res as $age_group) {
            $age_groups[$age_group->age_group_id] = $age_group;
        }

        $schedule->age_groups = $age_groups;

        $response->status = 'success';
        $response->schedule = $schedule;
    }else{
        $response->status = 'error';
        $response->errormsg = 'Unknown age group name identifier!';
    }

    poly_ajax_return($response);
}

function wp_ajax_poly_save_age_group_names() {
    global $wpdb;
    $response = new stdClass();

    if(!isset($_POST['data'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid age group name passed!';
        poly_ajax_return($response);
    }

    //Convert to stdClass object
    $schedule = json_decode( stripslashes( $_POST['data']), true);

    $pid = isset($schedule[POLY_TABLE_AGE_GROUP_NAMES_ID]) ? (int)$schedule[POLY_TABLE_AGE_GROUP_NAMES_ID] : 0;

    //Insert if schedule is draft yet
    if(isset($schedule['isDraft']) && (int)$schedule['isDraft']){
        $name = isset($schedule['name']) ? filter_var($schedule['name'], FILTER_SANITIZE_STRING) : "";
        $abbreviated_name = isset($schedule['abbreviated_name']) ? filter_var($schedule['abbreviated_name'], FILTER_SANITIZE_STRING) : "";

        $wpdb->insert(
            POLY_TABLE_AGE_GROUP_NAMES,
            array(
                'name' => $name,
                'abbreviated_name' => $abbreviated_name,
            ),
            array(
                '%s',
                '%s',
            )
        );

        //Get real identifier and use it instead of draft identifier for tmp usage
        $pid = $wpdb->insert_id;
    }

    $age_groups = isset($schedule['age_groups']) ? $schedule['age_groups'] : array();

    foreach($age_groups as $id => $age_group){

        //Any custom HTML content is permitted for title, description
        $min_age = isset($age_group['min_age']) ? $age_group['min_age'] : "";
        $max_age = isset($age_group['max_age']) ? $age_group['max_age'] : "";

        if(isset($age_group['isDraft']) && $age_group['isDraft']){
            $wpdb->insert(
                POLY_TABLE_AGE_GROUPS,
                array(
                    'min_age' => $min_age,
                    'max_age' => $max_age,
                    'age_group_names_id' => $pid,
                ),
                array(
                    '%d',
                    '%d',
                    '%d',
                )
            );
            $realAgeGroupId = $wpdb->insert_id;
        }else{
            $wpdb->update(
                POLY_TABLE_AGE_GROUPS,
                array(
                    'min_age' => $min_age,
                    'max_age' => $max_age,
                    'age_group_names_id' => $pid,
                ),
                array( POLY_TABLE_AGE_GROUPS_ID => $id ),
                array(
                    '%d',
                    '%d',
                    '%d',
                ),
                array(
                    '%d',
                )
            );
        }
    }


    $deletions = isset($schedule['deletions']) ? $schedule['deletions'] : array();
    $deletions = array_map('intval', $deletions);

    foreach($deletions as $deletedAgeGroupId) {
        // Default usage.
        $wpdb->delete(
            POLY_TABLE_AGE_GROUPS,
            array(
                POLY_TABLE_AGE_GROUPS_ID => $deletedAgeGroupId,
            )
        );
    }

    $name = isset($schedule['name']) ? filter_var($schedule['name'], FILTER_SANITIZE_STRING) : "";
    $abbreviated_name = isset($schedule['abbreviated_name']) ? filter_var($schedule['abbreviated_name'], FILTER_SANITIZE_STRING) : "";


    $wpdb->update(
        POLY_TABLE_AGE_GROUP_NAMES,
        array(
            'name' => $name,
            'abbreviated_name' => $abbreviated_name,
        ),
        array( POLY_TABLE_AGE_GROUP_NAMES_ID => $pid ),
        array(
            '%s',
            '%s',
        ),
        array(
            '%d',
        )
    );

    $response->status = 'success';
    $response->pid = $pid;
    poly_ajax_return($response);
}


