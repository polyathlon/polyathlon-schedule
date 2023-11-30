<?php
    global $poly_schedules;

    //Validation goes here
    if($poly_schedules) {
        //Setup ordered projects array
        // $poly_schedules->schedule_items = getOrderedItems($poly_schedules);
        $poly_schedules = getOrderedItems($poly_schedules);

        require(POLY_FRONT_VIEWS_DIR_PATH . "/layouts/poly-front-schedule.php");
        // if ($poly_schedules->grid_type == POLYGridType::SLIDER) {
        //     require(POLY_FRONT_VIEWS_DIR_PATH . "/layouts/poly-front-slider.php");
        // } else {
        //     require_once(POLY_FRONT_VIEWS_DIR_PATH . "/layouts/poly-front-tiled-layout-lightgallery.php");
        // }

        //Render user specified custom css
        //echo "<style>". $poly_schedules->options[POLYOption::kCustomCSS]."</style>";

        //Finally render custom js
        //echo "<script> jQuery(window).load(function() {".$poly_schedules->options[POLYOption::kCustomJS]."});</script>";

    }else{
        echo "Ooooops!!! Short-code related grid wasn't found in your database!";
    }


// function getOrderedProjects($poly_schedules){
//     $orderedProjects = array();

//     if(isset($poly_schedules->projects) && isset($poly_schedules->corder)){
//         foreach($poly_schedules->corder as $pid){
//             $orderedProjects[] = $poly_schedules->projects[$pid];
//         }
//     }

//     return $orderedProjects;
// }

function cmpItems($a, $b)
{
    $now = date('Y-m-d');
    if ($a->end_date < $now and $b->end_date >= $now)
        return 1;

    if ($a->end_date >= $now and $b->end_date < $now)
        return -1;

    if ($a->start_date == $b->start_date) {
        if ($a->end_date == $b->end_date) {
            if ($a->competition_name_id == $b->competition_name_id)
                return 0;
            return ($a->competition_name_id < $b->competition_name_id) ? -1 : 1;;
        }
        return ($a->end_date < $b->end_date) ? -1 : 1;

    }
    return ($a->start_date < $b->start_date) ? -1 : 1;
}

function getOrderedItems($poly_schedules){
    usort($poly_schedules, "cmpItems");
    return $poly_schedules;
}
