<!--Here Goes HTML-->
<style>

.poly-wrapper {
    margin: 0;
    box-sizing: border-box;
}

.poly-wrapper .poly-items {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
}

.poly-items .poly-item {
    display: flex;
    flex-direction: column;
    flex: 1;
    margin: 5px;
    width: 100%;
    min-width: 480px;
}

@media screen and (max-width: 600px) {
    .poly-items .poly-item {
        min-width: 300px;
    }
}

.poly-item .poly-schedule {
    display: flex;
    flex: 1 0 auto;
    margin: 5px 0px;
}

.poly-schedule .poly-main {
    flex: 1;
    width: 70%;
    padding: 5px 0px 5px 5px;
    /* display: flex;
    flex-direction: column;
    align-content: flex-start;
    justify-content: flex-start; */
}

.poly-main .poly-schedule-title{
    /* flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    align-content: center; */
    width: 100%;
    background: darkgray;
    /* padding-top: 10px;
    padding-bottom: 10px;
    font-size: 16px;
    line-height: 1.5em; */
    text-align: center;
    font-weight: 600;
    color: white;
    border-radius: 5px;
    padding: 5px 0;
}

/* .poly-schedule-title span{
    padding-bottom: 3px;
} */

.poly-main .poly-schedule-email,
.poly-main .poly-schedule-phone {
    flex: 1;
    display: flex;
    align-items: center;
    width: 100%;
    background: white;
    padding-top: 5px;
    padding-bottom: 5px;
    font-size: 14px;
    line-height: 1.5em;
}

.poly-main .poly-age-groups {
    display: flex;
    justify-items: center;
    flex-direction: column;
    background: white;
    padding-top: 5px;
    padding-bottom: 5px;
    margin-left: 10px;
}

.poly-age-groups .poly-age-group {
    flex: 1;
    width: 100%;
    padding-top: 5px;
    padding-bottom: 5px;
    font-size: 16px;
    line-height: 1.5em;
    white-space: nowrap;
}

.poly-schedule .poly-image {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    max-width: 100%;
}

.poly-schedule .poly-left {
    /* flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center; */
    padding: 5px 5px 5px 0;
    max-width: 35%;
}

.poly-left .poly-dates {
    /* flex: 1;
    display: flex;
    align-items: center;
    justify-content: center; */
    background-color: #6001d2;
    width: 100%;
    color: white;
    font-weight: 600;
    text-align: center;
    padding: 5px 0;
}


.poly-item .poly-header[data-color="1"],
.poly-left .poly-dates[data-color="1"],
.poly-multi-select .poly-select-numbers[data-color="1"] {
    background-color: #6001d2;
}

.poly-item .poly-header[data-color="2"],
.poly-left .poly-dates[data-color="2"],
.poly-multi-select .poly-select-numbers[data-color="2"] {
    background-color: #0094d4;
}

.poly-item .poly-header[data-color="3"],
.poly-left .poly-dates[data-color="3"],
.poly-multi-select .poly-select-numbers[data-color="3"] {
    background-color: #d10031;
}

.poly-item .poly-header[data-color="4"],
.poly-left .poly-dates[data-color="4"],
.poly-multi-select .poly-select-numbers[data-color="4"] {
    background-color: #d100ae;
}

.poly-item .poly-header[data-color="5"],
.poly-left .poly-dates[data-color="5"],
.poly-multi-select .poly-select-numbers[data-color="5"] {
    background-color: #d19600;
}

.poly-item .poly-header[data-color="6"],
.poly-left .poly-dates[data-color="6"],
.poly-multi-select .poly-select-numbers[data-color="6"] {
    background-color: #d14d00;
}

.poly-item .poly-header[data-color="7"],
.poly-left .poly-dates[data-color="7"],
.poly-multi-select .poly-select-numbers[data-color="7"] {
    background-color: #1100d1;
}

.poly-item .poly-header[data-color="8"],
.poly-left .poly-dates[data-color="8"],
.poly-multi-select .poly-select-numbers[data-color="8"] {
    background-color: #00d17a;
}

.poly-item .poly-header[data-color="9"],
.poly-left .poly-dates[data-color="9"],
.poly-multi-select .poly-select-numbers[data-color="9"] {
    background-color: #F78079;
}

.poly-item .poly-header[data-color="10"],
.poly-left .poly-dates[data-color="10"],
.poly-multi-select .poly-select-numbers[data-color="10"] {
    background-color: #d10000;
}

.poly-left .poly-place {
    /* flex: 1;
    display: flex;
    align-items: center;
    justify-content: center; */
    text-align: center;
}

.poly-image img{
    max-width: 100%;
    max-height: 150px;
}

.poly-item .poly-image {
    padding: 10px 5px 5px;
}

.poly-item .poly-header,
.poly-item .poly-footer {
    padding: 5px;
}

.poly-item .poly-header {
    display: flex;
    justify-content: flex-start;
    flex-direction: row-reverse;
    flex: 0 0 auto;
    user-select: none;
    background-color: #6001d2;
    font-family: 'Rubik', sans-serif;
    font-size: 18px;
    color: #FFF;
    line-height: 25px;
    box-sizing: border-box;
    font-weight: 600;
    text-align: center;
}

.poly-item .poly-footer {
    display: flex;
    justify-content: space-evenly;
    flex: 0 0 auto;
    font-family: 'Rubik', sans-serif;
    font-size: 16px;
    color: white;
    line-height: 25px;
    box-sizing: border-box;
    font-weight: 400;
    text-align: center;
    user-select: none;
}

.poly-item .poly-button {
    background-color: #121026;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    margin: 0 3px;
}

.poly-button a {
    color: white;
    display: flex;
    justify-content: center;
}

.poly-header .poly-competition-name {
    width: 100%;
    text-align: center;
    user-select: text;
}

.poly-header .poly-competition-number {
    white-space: nowrap;
    user-select: text;
}

.poly-multi-select {
    width: 100%;
    display: flex;
    height: 20px;
}

.poly-select-numbers {
    display: flex;
}

.poly-select-numbers a {
    font-weight: 500;
}

.poly-filter {
    display: flex;
}
</style>

<body>
    <div class="poly-wrapper">
        <!-- <div class="poly-multi-select">
            <?php foreach($poly_schedules as $poly_schedule): ?>
                <div class="poly-select-numbers" data-color="<?php echo $poly_schedule->competition_name_id?>">
                    <a href="<?php echo '#'.$poly_schedule->number;?>"><?php echo $poly_schedule->number;?></a>
                </div>
            <?php endforeach; ?>
        </div> -->

        <div class="poly-filter">
            <input type="date" id="competition-time" min="2023-01-01" class="form-control" value="<?php echo date('Y-m-d') ?>" onchange="competitionTimeFilter(event)">
            <!-- <input type="date" id="from" name="from" data-provide="datepicker" placeholder="Дата начало" class="form-control"> -->
            <select name="select" id="competition-status" class="form-control" onchange="competitionStatusFilter(event)">
                <option value="--Статус--">--Статус--</option>
                <option value="Текущее">Текущее</option>
                <option value="Прошедшее">Прошедшее</option>
                <option value="Ближайшее">Ближайшее</option>
            </select>
            <select name="select" id="competition-name" class="form-control" onchange="competitionNameFilter(event)">
                <option value="--Соревнование--">--Соревнование--</option>
                <?php foreach(POLYHelper::uniqueCompetitionNames($poly_schedules) as $competition_name): ?>
                    <option value="<?php echo $competition_name?>"><?php echo $competition_name?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="poly-items">
            <?php foreach($poly_schedules as $poly_schedule): ?>
                <div class="poly-item" id="<?php echo $poly_schedule->number?>">
                    <div class="poly-header" data-color="<?php echo $poly_schedule->competition_name_id?>">
                        <span class="poly-competition-number"><?php echo empty($poly_schedule->number_ekp) ? $poly_schedule->number : $poly_schedule->number_ekp?></span>
                        <span class="poly-competition-name">
                            <?php if (!empty($poly_schedule->stage_name)): ?>
                                <?php echo $poly_schedule->competition_name.' '.$poly_schedule->stage_name?>
                            <?php else: ?>
                                <?php echo $poly_schedule->competition_name?>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="poly-schedule">
                        <div class="poly-left">
                            <div class="poly-dates" data-color="<?php echo $poly_schedule->competition_name_id?>">
                                <span data-start="<?php echo $poly_schedule->start_date?>" data-end="<?php echo $poly_schedule->end_date?>"><?php echo $poly_schedule->date?></span>
                            </div>
                            <div class="poly-image">
                                <img src="<?php echo $poly_schedule->pic->src?>">
                            </div>
                            <div class="poly-place">
                                <span><?php echo $poly_schedule->city_name.', '.$poly_schedule->country_name?></span>
                            </div>
                        </div>

                        <div class="poly-main">
                            <div class="poly-schedule-title"><span><?php echo $poly_schedule->disciplines?></span></div>
                            <div class="poly-age-groups">
                                <?php foreach($poly_schedule->ageGroups as $ageGroup): ?>
                                    <div class="poly-age-group">
                                        <span>
                                          <?php echo $ageGroup?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="poly-footer">
                        <?php if (!empty($poly_schedule->regulations)): ?>
                            <div class="poly-button">
                                <a href="<?php echo $poly_schedule->regulations;?>" target="_blank">Регламент</a>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($poly_schedule->protocol)): ?>
                            <div class="poly-button">
                                <a href="<?php echo $poly_schedule->protocol;?>" target="_blank">Протоколы</a>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($poly_schedule->details)): ?>
                            <div class="poly-button">
                                <a href="<?php echo $poly_schedule->details;?>">Подробнее</a>
                            </div>
                        <?php endif; ?>
                        <?php if (empty($poly_schedule->details) && empty($poly_schedule->regulations) && empty($poly_schedule->protocol)): ?>
                            <div class="poly-button">
                                <a">Подготавливается</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>