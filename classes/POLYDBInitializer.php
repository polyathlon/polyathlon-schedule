<?php

class PolyDBInitializer {
    public $tableSchedules;
    public $tableSchedule_items;
    public $tableCompetition_names;
    public $tableSport_disciplines;
    public $tableSport_discipline_names;
    public $tableSport_disciplines_and_age_groups;
    public $tableCompetition_stages;
    public $tableCompetition_stage_names;
    public $tableAge_groups;
    public $tableCities;
    public $tableCountries;
    public $tableAge_group_names;

    public $tableOptions;

function __construct(){
    $this->tableSchedules = POLY_TABLE_SCHEDULES;
    $this->tableSchedule_items = POLY_TABLE_SCHEDULE_ITEMS;
    $this->tableCompetition_names = POLY_TABLE_COMPETITION_NAMES;
    $this->tableSport_disciplines = POLY_TABLE_SPORT_DISCIPLINES;
    $this->tableSport_discipline_names = POLY_TABLE_SPORT_DISCIPLINE_NAMES;
    $this->tableSport_disciplines_and_age_groups = POLY_TABLE_SPORT_DISCIPLINES_AND_AGE_GROUPS;
    $this->tableCompetition_stages = POLY_TABLE_COMPETITION_STAGES;
    $this->tableCompetition_stage_names = POLY_TABLE_COMPETITION_STAGE_NAMES;
    $this->tableAge_groups = POLY_TABLE_AGE_GROUPS;
    $this->tableCities = POLY_TABLE_CITIES;
    $this->tableCountries = POLY_TABLE_COUNTRIES;
    $this->tableAge_group_names = POLY_TABLE_AGE_GROUP_NAMES;

    $this->tableOptions = POLY_TABLE_OPTIONS;
}

public function configure(){
    //NOTE: before any configuration check what should we do later. Should we initialize with demo data or not, or something else.
    $needsConfiguration = $this->needsConfiguration();
    $needInitialization = $this->needsInitialization();

    if($needsConfiguration){
        $this->setupTables();
    }

    if($needInitialization){
        //$this->initializeTables();
    }
}

public function needsConfiguration(){
    global $wpdb;

    $sql = "SHOW TABLES FROM `{$wpdb->dbname}`  WHERE";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableSchedules}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableSchedule_items}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableCompetition_names}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableSport_disciplines}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableSport_discipline_names}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableSport_disciplines_and_age_groups}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableCompetition_stages}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableCompetition_stage_names}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableAge_groups}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableCities}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableCountries}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableAge_group_names}%'";

    $res = $wpdb->get_results($sql,ARRAY_A);

    //If any table is missing needs setup
    return count($res) < 12;
}

public function needsInitialization(){
    global $wpdb;

    $sql = "SHOW TABLES FROM `{$wpdb->dbname}`  WHERE";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableSchedules}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableSchedule_items}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableCompetition_names}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableSport_disciplines}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableSport_discipline_names}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableSport_disciplines_and_age_groups}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableCompetition_stages}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableCompetition_stage_names}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableAge_groups}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableCities}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableCountries}%' OR";
    $sql .=" `Tables_in_{$wpdb->dbname}` LIKE '%{$this->tableAge_group_names}%'";

    $res = $wpdb->get_results($sql,ARRAY_A);

    //If there is no tables yet, needs initialization
    return count($res) == 0;
}


public function checkForChanges() {
    // global $wpdb;
    // $table = $wpdb->get_results( $wpdb->prepare(
    //     "SELECT COUNT(1) FROM information_schema.tables WHERE table_schema=%s AND table_name=%s;",
    //     $wpdb->dbname, $this->tableProjects
    // ) );
    // if ( !empty( $table ) ) {
    //     $column = $wpdb->get_results($wpdb->prepare(
    //         "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ",
    //         $wpdb->dbname, $this->tableProjects, 'details'
    //     ));
    //     if (empty($column)) {
    //         $sql = "ALTER TABLE `{$this->tableProjects}` ADD `details` text";
    //         $wpdb->query($sql);
    //     }
    // }
}

private function setupTables(){
    global $wpdb;

    $charset_collate = '';

    if ( ! empty( $wpdb->charset ) ) {
        $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
    }

    if ( ! empty( $wpdb->collate ) ) {
        $charset_collate .= " COLLATE {$wpdb->collate}";
    }
                  //`id` int NOT NULL AUTO_INCREMENT,
                  //`title` varchar(255) DEFAULT NULL,
                  //`corder` text DEFAULT '',
                  //`options` text DEFAULT '',
                  //`extoptions` text DEFAULT '',
                  //PRIMARY KEY (`id`)


    $sql = "SET FOREIGN_KEY_CHECKS=0";
    $wpdb->query( $sql );

    //Create Schedules table
    $sql = "CREATE TABLE IF NOT EXISTS {$this->tableSchedules} (
                  `schedule_id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(50) NOT NULL,
                  PRIMARY KEY(`schedule_id`)
                )ENGINE=InnoDB $charset_collate;
        ";
    $wpdb->query( $sql );

    //Create Schedule_items table
    $sql = "CREATE TABLE IF NOT EXISTS {$this->tableSchedule_items} (
                `schedule_item_id` int(11) NOT NULL AUTO_INCREMENT,
                `schedule_id` int(11) NOT NULL DEFAULT '0',
                `competition_name_id` int(11) NOT NULL DEFAULT '0',
                `number` int(11) NOT NULL,
                PRIMARY KEY(`schedule_item_id`),
                CONSTRAINT `Ref_76` FOREIGN KEY (`schedule_id`)
                  REFERENCES {$this->tableSchedules}(`schedule_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION,
                CONSTRAINT `Ref_77` FOREIGN KEY (`competition_name_id`)
                  REFERENCES {$this->tableCompetition_names}(`competition_name_id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION
              )ENGINE=InnoDB $charset_collate;
        ";
    $wpdb->query( $sql );

    //Create Competition_names table
    $sql = "CREATE TABLE IF NOT EXISTS {$this->tableCompetition_names} (
                `competition_name_id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(250) NOT NULL,
                PRIMARY KEY(`competition_name_id`)
              )ENGINE=INNODB $charset_collate;
       ";
    $wpdb->query( $sql );

    //Create Sport_disciplines table
    $sql = "CREATE TABLE IF NOT EXISTS {$this->tableSport_disciplines} (
              `sport_discipline_id` int(11) NOT NULL AUTO_INCREMENT,
              `schedule_item_id` int(11) NOT NULL DEFAULT '0',
              `sport_discipline_name_id` int(11) NOT NULL DEFAULT '0',
            PRIMARY KEY(`sport_discipline_id`),
            CONSTRAINT `Ref_81` FOREIGN KEY (`sport_discipline_name_id`)
              REFERENCES {$this->tableSport_discipline_names}(`sport_discipline_name_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            CONSTRAINT `Ref_82` FOREIGN KEY (`schedule_item_id`)
              REFERENCES {$this->tableSchedule_items}(`schedule_item_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION
            )ENGINE=INNODB $charset_collate;
       ";
    $wpdb->query( $sql );

    //Create Sport_discipline_names table
    $sql = "CREATE TABLE IF NOT EXISTS {$this->tableSport_discipline_names} (
             `sport_discipline_name_id` int(11) NOT NULL AUTO_INCREMENT,
             `name` varchar(250) NOT NULL,
             `abbreviated_name` varchar(50),
            PRIMARY KEY(`sport_discipline_name_id`)
            )ENGINE=INNODB $charset_collate;
       ";
    $wpdb->query( $sql );

    //Create Sport_disciplines_and_age_groups table
    $sql = "CREATE TABLE IF NOT EXISTS {$this->tableSport_disciplines_and_age_groups} (
             `sport_discipline_id` int(11) NOT NULL DEFAULT '0',
             `age_group_id` int(11) NOT NULL DEFAULT '0',
            PRIMARY KEY(`sport_discipline_id`, `age_group_id`),
            CONSTRAINT `Ref_86` FOREIGN KEY (`age_group_id`)
              REFERENCES {$this->tableAge_groups}(`age_group_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            CONSTRAINT `Ref_87` FOREIGN KEY (`sport_discipline_id`)
              REFERENCES {$this->tableSport_disciplines}(`sport_discipline_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION
            )ENGINE=INNODB $charset_collate;
       ";
    $wpdb->query( $sql );

    //Create Competition_stages table
    $sql = "CREATE TABLE IF NOT EXISTS {$this->tableCompetition_stages} (
             `competition_stage_id` int(11) NOT NULL AUTO_INCREMENT,
             `sport_discipline_id` int(11) NOT NULL DEFAULT '0',
             `competition_stage_name_id` int(11) NOT NULL DEFAULT '0',
             `start_date` date,
             `end_date` date,
             `city_id` int(11) NOT NULL DEFAULT '0',
            PRIMARY KEY(`competition_stage_id`),
            CONSTRAINT `Ref_83` FOREIGN KEY (`sport_discipline_id`)
              REFERENCES {$this->tableSport_disciplines}(`sport_discipline_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            CONSTRAINT `Ref_84` FOREIGN KEY (`competition_stage_name_id`)
              REFERENCES {$this->tableCompetition_stage_names}(`competition_stage_name_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            CONSTRAINT `Ref_85` FOREIGN KEY (`city_id`)
              REFERENCES {$this->tableCities}(`city_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION
            )ENGINE=INNODB $charset_collate;
       ";
    $wpdb->query( $sql );

    //Create Competition_stage_names table
    $sql = "CREATE TABLE IF NOT EXISTS {$this->tableCompetition_stage_names} (
             `competition_stage_name_id` int(11) NOT NULL AUTO_INCREMENT,
             `name` varchar(250) NOT NULL,
            PRIMARY KEY(`competition_stage_name_id`)
            )ENGINE=INNODB $charset_collate;
       ";
    $wpdb->query( $sql );

    //Create Age_groups table
    $sql = "CREATE TABLE IF NOT EXISTS {$this->tableAge_groups} (
             `age_group_id` int(11) NOT NULL AUTO_INCREMENT,
             `age_group_names_id` int(11) NOT NULL DEFAULT '0',
             `min_age` int(11),
             `max_age` int(11),
            PRIMARY KEY(`age_group_id`),
            CONSTRAINT `Ref_80` FOREIGN KEY (`age_group_names_id`)
              REFERENCES {$this->tableAge_group_names}(`age_group_names_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION
            )ENGINE=INNODB $charset_collate;
       ";
    $wpdb->query( $sql );

     //Create Cities table
     $sql = "CREATE TABLE IF NOT EXISTS {$this->tableCities} (
             `city_id` int(11) NOT NULL AUTO_INCREMENT,
             `country_id` int(11) NOT NULL DEFAULT '0',
             `name` varchar(50) NOT NULL,
            PRIMARY KEY(`city_id`),
            CONSTRAINT `Ref_79` FOREIGN KEY (`country_id`)
              REFERENCES {$this->tableCountries}(`country_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION
            )ENGINE=INNODB $charset_collate;
       ";
    $wpdb->query( $sql );

     //Create Countries table
        $sql = "CREATE TABLE IF NOT EXISTS {$this->tableCountries} (
                 `country_id` int(11) NOT NULL AUTO_INCREMENT,
                 `name` varchar(50) NOT NULL,
                PRIMARY KEY(`country_id`)
                )ENGINE=INNODB $charset_collate;
       ";
    $wpdb->query( $sql );

    //Create Age_group_names table
         $sql = "CREATE TABLE IF NOT EXISTS {$this->tableAge_group_names} (
                 `age_group_names_id` int(11) NOT NULL AUTO_INCREMENT,
                 `name` varchar(250) NOT NULL,
                 `abbreviated_name` varchar(50),
                PRIMARY KEY(`age_group_names_id`)
                )ENGINE=INNODB $charset_collate;
       ";
    $wpdb->query( $sql );

       //Add cascade FK. Relation between ( project & schedule )
    // $sql = "ALTER TABLE `{$this->tableProjects}` ADD PRIMARY KEY (`id`), ADD KEY `pid_index` (`pid`)";
    // $wpdb->query( $sql );

//    $sql = "ALTER TABLE `{$this->tableProjects}` ADD CONSTRAINT `poly_pid_fk` FOREIGN KEY (`pid`) REFERENCES `{$this->tableSchedules}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE";
//    $wpdb->query( $sql );

    $sql = "SET FOREIGN_KEY_CHECKS=1";
    $wpdb->query( $sql );
}

private function initializeTables(){
    // global $wpdb;

    // //Insert demo schedule
    // $wpdb->insert(
    //     $this->tableSchedules,
    //     array(
    //         'title' => '',
    //         'corder' => '',
    //         'options' => POLYHelper::getScheduleDefaultOptions()
    //     )
    // );
    // $pid = $wpdb->insert_id;

    // //Add demo project
    // $wpdb->insert(
    //     $this->tableProjects,
    //     array(
    //         'pid' => $pid,
    //         'title' => '',
    //         'description' => "",
    //     )
    // );
}
}