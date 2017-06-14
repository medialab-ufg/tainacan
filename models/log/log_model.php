<?php
require_once(dirname(__FILE__) . '../../general/general_model.php');

class Log extends Model {
    private $_log_version = 1.0;
    static $primary_key = 'id';

    const _TABLE_SUFFIX_ = "statistics";
    const _POSTS_TABLE = "posts";

    public static function _table() {
        return $GLOBALS['wpdb']->prefix . self::_TABLE_SUFFIX_;
    }

    public static function _posts_table() {
        return $GLOBALS['wpdb']->prefix . self::_POSTS_TABLE;
    }

    public static function addLog($logData) {
        global $wpdb;

        if( !array_key_exists('user_id', $logData) ) {
            $logData['user_id'] = get_current_user_id();
        }

        $final_data = array_merge($logData, self::getCommonFields() );
        return $wpdb->insert(self::_table(), $final_data);
    }
    
    private static function getCommonFields() {
        return ['ip' => $_SERVER['REMOTE_ADDR'], 'event_date' => date('Y-m-d H:i:s')];
    }

    public static function getUserEvents($event_type, $event, $encoded = true, $from = '', $to = '', $collection_id = NULL, $filter) {
        global $wpdb;

        if(($collection_id == 'null' || is_null($collection_id)) and $filter == 'months'){
            $fr = substr($from, 0, 7);
            $t = substr($to, 0, 7);

            if($event == 'publish' || $event == 'draft' || $event == 'trash'){
                $SQL_query = sprintf(
                    "SELECT * FROM (
                        (
                            SELECT * 
                                FROM (
                                    SELECT post_status AS event, substring(post_date, 1, 7) AS date, count(id) AS total
                                    FROM %s
                                        WHERE post_type = 'socialdb_object' AND post_status = '$event'
                                        GROUP BY (substring(post_date, 1, 7))
                                ) res1
                                WHERE date between '$fr' AND '$t'
                        ) res2 
                        NATURAL JOIN (
                            SELECT count(id) AS event_total 
                            FROM %s
                                WHERE post_status = '$event' AND post_type = 'socialdb_object' AND substring(post_date, 1, 7) between '$fr' AND '$t'
                        ) res3
                    )", self::_posts_table(), self::_posts_table());
            }
            else if($event == 'delete' and $event_type == 'general_status_items'){
                $SQL_query = sprintf(
                    "SELECT * FROM (
                        (
                            SELECT * 
                                FROM (
                                    SELECT event, substring(event_date, 1, 7) AS date, count(id) AS total 
                                    FROM %s
                                        WHERE event = '$event' AND event_type = 'user_items' 
                                        GROUP BY (substring(event_date, 1, 7))
                                ) res1  
                                WHERE date between '$from' AND '$to'
                        ) res2 
                        NATURAL JOIN (
                            SELECT count(id) AS event_total 
                            FROM %s 
                                WHERE event = '$event' AND event_type = 'user_items' AND substring(event_date, 1, 7) between '$fr' AND '$t'
                        ) res3
                    )", self::_table(), self::_table());
            }
            else{
                $SQL_query = sprintf(
                    "SELECT * FROM (
                        (
                            SELECT * 
                            FROM (
                                SELECT event, substring(event_date, 1, 7) AS date, count(id)  AS total 
                                FROM %s 
                                    WHERE event = '$event' AND event_type = '$event_type' 
                                    GROUP BY (substring(event_date, 1, 7))
                            ) res1  
                            WHERE date between '$fr' AND '$t'
                        ) res2 
                        NATURAL JOIN (
                            SELECT count(id) AS event_total 
                            FROM %s 
                                WHERE event = '$event' AND event_type = '$event_type' AND substring(event_date, 1, 7) between '$fr' AND '$t'
                        ) res3
                    )", self::_table(), self::_table());
            }
        }
        else if(($collection_id == 'null' || is_null($collection_id)) and $filter == 'weeks'){
            if($event == 'publish' || $event == 'draft' || $event == 'trash'){
                $SQL_query = sprintf(
                    "SELECT * FROM (
                        (
                            SELECT * 
                            FROM (
                                SELECT post_status AS event, (week(substring(post_date, 1, 10))) AS week_number, count(id) AS total 
                                FROM %s 
                                    WHERE post_status = '$event' AND post_type = '$event_type' AND substring(post_date, 1, 10) between '$from' AND '$to' 
                                    GROUP BY (week(substring(post_date, 1, 10)))
                            ) res1
                        ) res2 
                        NATURAL JOIN (
                            SELECT count(id) AS event_total 
                            FROM %s 
                                WHERE post_status = '$event' AND post_type = '$event_type' AND substring(post_date, 1, 10) between '$from' AND '$to'
                        ) res3
                    )", self::_posts_table(), self::_posts_table());
            }
            else if($event == 'delete' and $event_type == 'general_status_items'){
                $SQL_query = sprintf(
                    "SELECT * FROM (
                        (
                            SELECT * 
                            FROM (
                                SELECT event, (week(substring(event_date, 1, 10))) AS week_number, count(id) AS total 
                                FROM %s 
                                    WHERE event = '$event' AND event_type = 'user_items' AND substring(event_date, 1, 10) between '$from' AND '$to' 
                                    GROUP BY (week(substring(event_date, 1, 10)))
                            ) res1
                        ) res2 
                        NATURAL JOIN (
                            SELECT count(id) AS event_total 
                            FROM %s 
                                WHERE event = '$event' AND event_type = 'user_items' AND substring(event_date, 1, 10) between '$from' AND '$to'
                        ) res3
                    )", self::_table(), self::_table());
            }
            else{
                $SQL_query = sprintf(
                    "SELECT * FROM (
                        (
                            SELECT * 
                            FROM (
                                SELECT event, (week(substring(event_date, 1, 10))) AS week_number, count(id) AS total 
                                FROM %s 
                                    WHERE event = '$event' AND event_type = '$event_type' AND substring(event_date, 1, 10) between '$from' AND '$to' 
                                    GROUP BY (week(substring(event_date, 1, 10)))
                            ) res1
                        ) res2 
                        NATURAL JOIN (
                            SELECT count(id) AS event_total 
                            FROM %s 
                                WHERE event = '$event' AND event_type = '$event_type' AND substring(event_date, 1, 10) between '$from' AND '$to'
                        ) res3
                    )", self::_table(), self::_table());
            }
        }
        else if(($collection_id == 'null' || is_null($collection_id)) and $filter == 'days'){
            if($event == 'publish' || $event == 'draft' || $event == 'trash'){
                $SQL_query = sprintf(
                    "SELECT * FROM (
                        (
                            SELECT *
                            FROM (
                                SELECT post_status AS event, substring(post_date, 1, 10) AS date, count(id) AS total
                                FROM %s
                                    WHERE post_type = 'socialdb_object' AND post_status='$event'
                                    GROUP BY (substring(post_date, 1, 10))
                            ) res1
                            WHERE date between '$from' AND '$to'
                        ) res2 
                        NATURAL JOIN (
                            SELECT count(id) AS event_total 
                            FROM %s
                                WHERE post_status = '$event' AND post_type = 'socialdb_object' AND substring(post_date, 1, 10) between '$from' AND '$to'
                        ) res3
                    )", self::_posts_table(), self::_posts_table());
            }
            else if($event == 'delete' and $event_type == 'general_status_items'){
                $SQL_query = sprintf(
                    "SELECT * FROM (
                        (
                            SELECT * 
                            FROM (
                                SELECT event, substring(event_date, 1, 10) AS date, count(id) AS total 
                                FROM %s
                                    WHERE event = '$event' AND event_type = 'user_items' 
                                    GROUP BY (substring(event_date, 1, 10))
                            ) res1  
                            WHERE date between '$from' AND '$to'
                        ) res2 
                        NATURAL JOIN (
                            SELECT count(id) AS event_total 
                            FROM %s 
                                WHERE event = '$event' AND event_type = 'user_items' AND substring(event_date, 1, 10) between '$from' AND '$to'
                        ) res3
                    )", self::_table(), self::_table());
            }
            else{
                $SQL_query = sprintf(
                    "SELECT * FROM (
                        (
                            SELECT * 
                            FROM (
                                SELECT event, substring(event_date, 1, 10) AS date, count(id) AS total 
                                FROM %s
                                    WHERE event = '$event' AND event_type = '$event_type' 
                                    GROUP BY (substring(event_date, 1, 10))
                            ) res1  
                            WHERE date between '$from' AND '$to'
                        ) res2 
                        NATURAL JOIN (
                            SELECT count(id) AS event_total 
                            FROM %s 
                                WHERE event = '$event' AND event_type = '$event_type' AND substring(event_date, 1, 10) between '$from' AND '$to'
                        ) res3
                    )", self::_table(), self::_table());
            }
        }
        else {
            // Collection id isn't null

            if($filter == 'months'){
                $fr = substr($from, 0, 7);
                $t = substr($to, 0, 7);

                if($event == 'publish' || $event == 'draft' || $event == 'trash'){
                    $SQL_query = sprintf(
                    "SELECT * FROM (
                        (
                            SELECT * 
                                FROM (
                                    SELECT post_status AS event, substring(post_date, 1, 7) AS date, count(id) AS total
                                    FROM %s
                                        WHERE post_type = 'socialdb_object' AND post_status = '$event' AND collection_id = '$collection_id'
                                        GROUP BY (substring(post_date, 1, 7))
                                ) res1
                                WHERE date between '$fr' AND '$t'
                        ) res2 
                        NATURAL JOIN (
                            SELECT count(id) AS event_total 
                            FROM %s
                                WHERE post_status = '$event' AND post_type = 'socialdb_object' AND collection_id = '$collection_id' AND substring(post_date, 1, 7) between '$fr' AND '$t'
                        ) res3
                    )", self::_posts_table(), self::_posts_table());
                }
                else if($event == 'delete' and $event_type == 'general_status_items'){
                    $SQL_query = sprintf(
                    "SELECT * FROM (
                        (
                            SELECT * 
                                FROM (
                                    SELECT event, substring(event_date, 1, 7) AS date, count(id) AS total 
                                    FROM %s
                                        WHERE event = '$event' AND event_type = 'user_items' AND collection_id = '$collection_id' 
                                        GROUP BY (substring(event_date, 1, 7))
                                ) res1  
                                WHERE date between '$from' AND '$to'
                        ) res2 
                        NATURAL JOIN (
                            SELECT count(id) AS event_total 
                            FROM %s 
                                WHERE event = '$event' AND event_type = 'user_items' AND collection_id = '$collection_id' AND substring(event_date, 1, 7) between '$fr' AND '$t'
                        ) res3
                    )", self::_table(), self::_table());
                }
                else{
                    $SQL_query = sprintf(
                    "SELECT * FROM (
                        (
                            SELECT * 
                            FROM (
                                SELECT event, substring(event_date, 1, 7) AS date, count(id)  AS total 
                                FROM %s 
                                    WHERE event = '$event' AND event_type = '$event_type' AND collection_id = '$collection_id'
                                    GROUP BY (substring(event_date, 1, 7))
                            ) res1  
                            WHERE date between '$fr' AND '$t'
                        ) res2 
                        NATURAL JOIN (
                            SELECT count(id) AS event_total 
                            FROM %s 
                                WHERE event = '$event' AND event_type = '$event_type'  AND collection_id = '$collection_id' AND substring(event_date, 1, 7) between '$fr' AND '$t'
                        ) res3
                    )", self::_table(), self::_table());
                }
            }
            else if($filter == 'weeks'){
                if($event == 'publish' || $event == 'draft' || $event == 'trash'){
                    $SQL_query = sprintf(
                        "SELECT * FROM (
                            (
                                SELECT * 
                                FROM (
                                    SELECT post_status AS event, (week(substring(post_date, 1, 10))) AS week_number, count(id) AS total 
                                    FROM %s 
                                        WHERE post_status = '$event' AND post_type = '$event_type' AND collection_id = '$collection_id' AND substring(post_date, 1, 10) between '$from' AND '$to' 
                                        GROUP BY (week(substring(post_date, 1, 10)))
                                ) res1
                            ) res2 
                            NATURAL JOIN (
                                SELECT count(id) AS event_total 
                                FROM %s 
                                    WHERE post_status = '$event' AND post_type = '$event_type' AND collection_id = '$collection_id' AND substring(post_date, 1, 10) between '$from' AND '$to'
                            ) res3
                        )", self::_posts_table(), self::_posts_table());
                }
                else if($event == 'delete' and $event_type == 'general_status_items'){
                    $SQL_query = sprintf(
                        "SELECT * FROM (
                            (
                                SELECT * 
                                FROM (
                                    SELECT event, (week(substring(event_date, 1, 10))) AS week_number, count(id) AS total 
                                    FROM %s 
                                        WHERE event = '$event' AND event_type = 'user_items' AND collection_id = '$collection_id' AND substring(event_date, 1, 10) between '$from' AND '$to' 
                                        GROUP BY (week(substring(event_date, 1, 10)))
                                ) res1
                            ) res2 
                            NATURAL JOIN (
                                SELECT count(id) AS event_total 
                                FROM %s 
                                    WHERE event = '$event' AND event_type = 'user_items' AND collection_id = '$collection_id' AND substring(event_date, 1, 10) between '$from' AND '$to'
                            ) res3
                        )", self::_table(), self::_table());
                }
                else{
                    $SQL_query = sprintf(
                        "SELECT * FROM (
                            (
                                SELECT * 
                                FROM (
                                    SELECT event, (week(substring(event_date, 1, 10))) AS week_number, count(id) AS total 
                                    FROM %s 
                                        WHERE event = '$event' AND event_type = '$event_type' AND collection_id = '$collection_id' AND substring(event_date, 1, 10) between '$from' AND '$to' 
                                        GROUP BY (week(substring(event_date, 1, 10)))
                                ) res1
                            ) res2 
                            NATURAL JOIN (
                                SELECT count(id) AS event_total 
                                FROM %s 
                                    WHERE event = '$event' AND event_type = '$event_type' AND collection_id = '$collection_id' AND substring(event_date, 1, 10) between '$from' AND '$to'
                            ) res3
                        )", self::_table(), self::_table());
                }
            }
            else if($filter == 'days'){
                if($event == 'publish' || $event == 'draft' || $event == 'trash'){
                    $SQL_query = sprintf(
                        "SELECT * FROM (
                            (
                                SELECT *
                                FROM (
                                    SELECT post_status AS event, substring(post_date, 1, 10) AS date, count(id) AS total
                                    FROM %s
                                        WHERE post_type = 'socialdb_object' AND post_status='$event' AND collection_id = '$collection_id'
                                        GROUP BY (substring(post_date, 1, 10))
                                ) res1
                                WHERE date between '$from' AND '$to'
                            ) res2 
                            NATURAL JOIN (
                                SELECT count(id) AS event_total 
                                FROM %s
                                    WHERE post_status = '$event' AND post_type = 'socialdb_object' AND collection_id = '$collection_id' AND substring(post_date, 1, 10) between '$from' AND '$to'
                            ) res3
                        )", self::_posts_table(), self::_posts_table());
                }
                else if($event == 'delete' and $event_type == 'general_status_items'){
                    $SQL_query = sprintf(
                        "SELECT * FROM (
                            (
                                SELECT * 
                                FROM (
                                    SELECT event, substring(event_date, 1, 10) AS date, count(id) AS total 
                                    FROM %s
                                        WHERE event = '$event' AND event_type = 'user_items' AND collection_id = '$collection_id'
                                        GROUP BY (substring(event_date, 1, 10))
                                ) res1  
                                WHERE date between '$from' AND '$to'
                            ) res2 
                            NATURAL JOIN (
                                SELECT count(id) AS event_total 
                                FROM %s 
                                    WHERE event = '$event' AND event_type = 'user_items' AND collection_id = '$collection_id' AND substring(event_date, 1, 10) between '$from' AND '$to'
                            ) res3
                        )", self::_table(), self::_table());
                }
                else{
                    $SQL_query = sprintf(
                        "SELECT * FROM (
                            (
                                SELECT * 
                                FROM (
                                    SELECT event, substring(event_date, 1, 10) AS date, count(id) AS total 
                                    FROM %s
                                        WHERE event = '$event' AND event_type = '$event_type' AND collection_id = '$collection_id'
                                        GROUP BY (substring(event_date, 1, 10))
                                ) res1  
                                WHERE date between '$from' AND '$to'
                            ) res2 
                            NATURAL JOIN (
                                SELECT count(id) AS event_total 
                                FROM %s 
                                    WHERE event = '$event' AND event_type = '$event_type' AND collection_id = '$collection_id' AND substring(event_date, 1, 10) between '$from' AND '$to'
                            ) res3
                        )", self::_table(), self::_table());
                }
            }
            // $SQL_query = sprintf("SELECT COUNT(id) AS '$_alias' FROM %s WHERE event_type = '$event_type' AND event = '$event'
            //        AND collection_id = '$collection_id' AND event_date BETWEEN '$from' AND '$to'", self::_table() );
        }

        if($encoded){
            return json_encode($wpdb->get_results($SQL_query));
        }
        else{
            $ret = $wpdb->get_results($SQL_query);
            return $ret;
        }
    }
    
    private function get_event_type($spec) {
        switch($spec) {
            case 'items':
                return ['events' => self::getDefaultFields(['download', 'vote'])];
            case 'category':
                return ['events' => self::getDefaultFields()];
            case 'collection':
                return ['events' => self::getDefaultFields()];
            case 'comments':
                return ['events' => self::getDefaultFields()];
            case 'tags':
                return ['events' => self::getDefaultFields()];
            case 'user':
                return ['events' => ['view', 'comment', 'vote'] ];
            case 'status':
                return ['events' => ['login', 'register', 'delete_user'] ];
            case 'general_status':
                return ['events' => ['publish', 'draft', 'trash', 'delete'] ];
            case 'profile':
                return ['events' => ['subscriber', 'administrator', 'editor', 'author', 'contributor'] ];
            case 'imports':
                return ['events' => ['access_oai_pmh', 'import_csv', 'export_csv', 'import_tainacan', 'export_tainacan'] ];
            case 'collection_imports':
                return ['events' => ['access_oai_pmh', 'harvest_oai_pmh', 'import_csv', 'export_csv'] ];
            case 'admin':
                return ['events' => ['config', 'metadata', 'keys', 'licenses', 'welcome_mail', 'tools'] ];
            case 'collection_admin':
                return ['events' => ['config', 'metadata', 'layout', 'social_media', 'licenses', 'import', 'export'] ];
        }
    }

    private function getDefaultFields($extra_fields = "") {
        $base_defaults = ['add', 'view', 'edit', 'delete'];
        return ( is_array($extra_fields) && !empty($extra_fields) ) ? array_merge($base_defaults, $extra_fields) : $base_defaults;
    }

    public static function user_events($event_type, $spec, $from, $to, $collection_id = NULL, $filter) {
        $_events_ = self::get_event_type($spec);

        // event_type = 'collection' isn't work

        if( "top_collections_items" == $event_type ) {
            $top_collections = self::getTopCollections($from, $to, $filter);
            $cols_array = array();
            $class_names = array();

            foreach ($top_collections as $col => $data) {
                $_title = get_post($data->event)->post_title; //Point to position of collection in database table.
                //array_push($cols_array, [$_title => $col->total_collection ]);
                if($filter != 'weeks'){
                    array_push($cols_array, [ $_title, $data->date, $data->total, $data->event_total ]);
                }
                else{
                    array_push($cols_array, [ $_title, $data->week_number, $data->total, $data->event_total ]);
                }
                array_push($class_names, $_title);
            }

            $stat_data = [ "stat_object" => $cols_array, "columns" => array_values(array_unique($class_names)) ];
            $return_data = json_encode($stat_data, JSON_NUMERIC_CHECK, JSON_FORCE_OBJECT);

            return $return_data;
        }
        else if("repo_searches" == $event_type || $event_type == "collection_searches") {
            $searches = self::getFrequentSearches($collection_id, $from, $to, $filter);

            $sarr = array();
            $events = array();
            foreach($searches as $key => $data) {
                if($filter != 'weeks'){
                    array_push($sarr, [ $data->event, $data->date, $data->total, $data->event_total ]);
                    array_push($events, $data->event);
                }
                else{
                    array_push($sarr, [ $data->event, $data->week_number, $data->total, $data->event_total ]);
                    array_push($events, $data->event);
                }
            }

            $stat_data = ["stat_object" => $sarr, "columns" => array_values(array_unique($events))];

            $return_data = json_encode($stat_data, JSON_NUMERIC_CHECK, JSON_FORCE_OBJECT);

            return $return_data;
        } 
        else {
            if($_events_) {
                $_stats = array();

                foreach ($_events_['events'] as $ev) {
                    $results = self::getUserEvents($event_type, $ev, false, $from, $to, $collection_id, $filter);

                    $i = 0;
                    if($filter == 'months' || $filter == 'days') {
                        foreach ($results as $key => $data) {
                            $_stats[] = [ $data->event, $data->date, $data->total, $data->event_total ];
                            $i += 1;
                        }
                    }
                    else if($filter == 'weeks'){
                        foreach ($results as $key => $data){
                            $_stats[] = [ $data->event, $data->week_number, $data->total, $data->event_total ];
                        }
                    }
                }

                $stat_data = [ "stat_object" => $_stats, "columns" => $_events_ ];
            } else {
                $stat_data = [];
            }

            $return_data = json_encode($stat_data, JSON_NUMERIC_CHECK, JSON_FORCE_OBJECT);

            return $return_data;
        }
    }

    public function getTopCollections($from, $to, $filter) {
        global $wpdb;

        if($filter == 'months'){
            $fr = substr($from, 0, 7);
            $t = substr($to, 0, 7);

            $sql = sprintf(
                "SELECT event, date, total, event_total 
                    FROM (
                        (
                            SELECT event, date, count(event) AS total 
                            FROM (
                                SELECT post_parent AS event, substring(post_date, 1, 7) AS date 
                                FROM %s 
                                    WHERE post_parent > 0 AND post_type = 'socialdb_object' AND substring(post_date, 1, 7) between '$fr' AND '$t'
                            ) res1  
                            GROUP BY event, date
                        ) res2 
                        JOIN (
                            SELECT post_parent, count(id) AS event_total 
                            FROM %s
                                WHERE post_parent > 0 AND post_type = 'socialdb_object' AND substring(post_date, 1, 7) between '$fr' AND '$t' 
                                GROUP BY post_parent
                        ) res3 
                        ON res2.event = res3.post_parent
                    )", self::_posts_table(), self::_posts_table());
        }
        else if($filter == 'weeks'){
            $sql = sprintf(
                "SELECT event, week_number, total, event_total 
                    FROM (
                        (
                            SELECT event, week_number, count(event) AS total 
                            FROM (
                                SELECT post_parent AS event, week(substring(post_date, 1, 10)) AS week_number 
                                FROM %s 
                                    WHERE post_parent > 0 AND post_type = 'socialdb_object' AND substring(post_date, 1, 10) between '$from' AND '$to'
                            ) res1 
                            GROUP BY event, week_number
                        ) res2 
                        JOIN (
                            SELECT post_parent, count(id) AS event_total 
                            FROM %s 
                                WHERE post_parent > 0 AND post_type = 'socialdb_object' AND substring(post_date, 1, 10) between '$from' AND '$to' 
                                GROUP BY post_parent
                        ) res3 
                        ON res2.event = res3.post_parent
                    )", self::_posts_table(), self::_posts_table());
        }
        else if($filter == 'days'){
            $sql = sprintf(
                "SELECT event, date, total, event_total 
                    FROM (
                        (
                            SELECT event, date, count(event) AS total 
                            FROM (
                                SELECT post_parent AS event, substring(post_date, 1, 10) AS date 
                                FROM %s 
                                    WHERE post_parent > 0 AND post_type = 'socialdb_object' AND substring(post_date, 1, 10) between '$from' AND '$to'
                            ) res1  
                            GROUP BY event, date
                        ) res2 
                        JOIN (
                            SELECT post_parent, count(id) AS event_total 
                            FROM %s 
                                WHERE post_parent > 0 AND post_type = 'socialdb_object' AND substring(post_date, 1, 10) between '$from' AND '$to' 
                                GROUP BY post_parent
                        ) res3 
                        ON res2.event = res3.post_parent
                    )", self::_posts_table(), self::_posts_table());
        }

        $top_collections = $wpdb->get_results($sql);

        return $top_collections;
    }

    public function getFrequentSearches( $collection_id = NULL, $from, $to, $filter) {
        global $wpdb;

        if(($collection_id == 'null' || is_null($collection_id))){
            $event_type = 'advanced_search'; //this don't work
           
            if($filter == 'days' ) {
                $SQL_query = sprintf(
                    "SELECT event, date, total, event_total 
                    FROM (
                            (
                            SELECT event, substring(event_date, 1, 10) AS date, count(event) AS total 
                            FROM %s 
                                WHERE event_type = '$event_type' AND (substring(event_date, 1, 10)) between '$from' AND '$to'
                                GROUP BY event, (substring(event_date, 1, 10))
                            ) res1 
                            JOIN (
                                SELECT event AS event2, count(event) AS event_total
                                FROM %s
                                    WHERE event_type = '$event_type' AND (substring(event_date, 1, 10)) between '$from' AND '$to'
                                    GROUP BY event
                            ) res2
                            ON res1.event = res2.event2
                    )", self::_table(), self::_table());
                //$sql = sprintf("SELECT event AS term, COUNT(*) AS t_count FROM %s WHERE event_type = 'advanced_search' GROUP BY event ORDER BY COUNT(*) DESC", self::_table());
            } 
            else if($filter == 'weeks' ){

                $SQL_query = sprintf(
                    "SELECT event, week_number, total, event_total
                    FROM (
                        (
                            SELECT event, week(substring(event_date, 1, 10)) as week_number, count(event) AS total
                            FROM %s
                                WHERE event_type = '$event_type' AND (substring(event_date, 1, 10)) between '$from' AND '$to'
                                GROUP BY event, (week(substring(event_date, 1, 10)))
                        ) res1
                        JOIN (
                            SELECT event AS event2, count(event) AS event_total
                            FROM %s
                                WHERE event_type = '$event_type' AND (substring(event_date, 1, 10)) between '$from' AND '$to'
                                GROUP BY event
                        ) res2
                        ON res1.event = res2.event2
                    )", self::_table(), self::_table());
            }
            else if($filter == 'months' ){
                $fr = substr($from, 0, 7);
                $t = substr($to, 0, 7);

                $SQL_query = sprintf(
                    "SELECT event, date, total, event_total 
                    FROM (
                            (
                            SELECT event, substring(event_date, 1, 7) AS date, count(event) AS total 
                            FROM %s 
                                WHERE event_type = '$event_type' AND (substring(event_date, 1, 7)) between '$fr' AND '$t'
                                GROUP BY event, (substring(event_date, 1, 7))
                            ) res1 
                            JOIN (
                                SELECT event AS event2, count(event) AS event_total
                                FROM %s
                                    WHERE event_type = '$event_type' AND (substring(event_date, 1, 7)) between '$fr' AND '$t'
                                    GROUP BY event
                            ) res2
                            ON res1.event = res2.event2
                    )", self::_table(), self::_table());
            }
        }
        else {
            $event_type = 'collection_search';

            if($filter == 'days' ) {
                $SQL_query = sprintf(
                    "SELECT event, date, total, event_total 
                    FROM (
                            (
                            SELECT event, substring(event_date, 1, 10) AS date, count(event) AS total 
                            FROM %s 
                                WHERE event_type = '$event_type' AND collection_id = '$collection_id' AND (substring(event_date, 1, 10)) between '$from' AND '$to'
                                GROUP BY event, (substring(event_date, 1, 10))
                            ) res1 
                            JOIN (
                                SELECT event AS event2, count(event) AS event_total
                                FROM %s
                                    WHERE event_type = '$event_type' AND collection_id = '$collection_id' AND (substring(event_date, 1, 10)) between '$from' AND '$to'
                                    GROUP BY event
                            ) res2
                            ON res1.event = res2.event2
                    )", self::_table(), self::_table());
                //$sql = sprintf("SELECT event AS term, COUNT(*) AS t_count FROM %s WHERE event_type = 'advanced_search' GROUP BY event ORDER BY COUNT(*) DESC", self::_table());
            } 
            else if($filter == 'weeks' ){

                $SQL_query = sprintf(
                    "SELECT event, week_number, total, event_total
                    FROM (
                        (
                            SELECT event, week(substring(event_date, 1, 10)) as week_number, count(event) AS total
                            FROM %s
                                WHERE event_type = '$event_type' AND collection_id = '$collection_id' AND (substring(event_date, 1, 10)) between '$from' AND '$to'
                                GROUP BY event, (week(substring(event_date, 1, 10)))
                        ) res1
                        JOIN (
                            SELECT event AS event2, count(event) AS event_total
                            FROM %s
                                WHERE event_type = '$event_type' AND collection_id = '$collection_id' AND (substring(event_date, 1, 10)) between '$from' AND '$to'
                                GROUP BY event
                        ) res2
                        ON res1.event = res2.event2
                    )", self::_table(), self::_table());
            }
            else if($filter == 'months' ){
                $fr = substr($from, 0, 7);
                $t = substr($to, 0, 7);

                $SQL_query = sprintf(
                    "SELECT event, date, total, event_total 
                    FROM (
                            (
                            SELECT event, substring(event_date, 1, 7) AS date, count(event) AS total 
                            FROM %s 
                                WHERE event_type = '$event_type' AND collection_id = '$collection_id' AND (substring(event_date, 1, 7)) between '$fr' AND '$t'
                                GROUP BY event, (substring(event_date, 1, 7))
                            ) res1 
                            JOIN (
                                SELECT event AS event2, count(event) AS event_total
                                FROM %s
                                    WHERE event_type = '$event_type' AND collection_id = '$collection_id' AND (substring(event_date, 1, 7)) between '$fr' AND '$t'
                                    GROUP BY event
                            ) res2
                            ON res1.event = res2.event2
                    )", self::_table(), self::_table());
            }
        }

        $searches = $wpdb->get_results($SQL_query);

        return $searches;
    }
}