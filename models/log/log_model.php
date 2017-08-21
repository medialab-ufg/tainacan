<?php
require_once(dirname(__FILE__) . '../../general/general_model.php');

class Log extends Model {
    private $_log_version = 1.0;
    static $primary_key = 'id';

    const _TABLE_SUFFIX_ = "statistics";
    const _POSTS_TABLE = "posts";
    const _USERS_TABLE = "users";

    public static function _table() {
        return $GLOBALS['wpdb']->prefix . self::_TABLE_SUFFIX_;
    }

    public static function _posts_table() {
        return $GLOBALS['wpdb']->prefix . self::_POSTS_TABLE;
    }

    public static function _users_table(){
        return $GLOBALS['wpdb']->prefix . self::_USERS_TABLE;
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
                                SELECT post_status AS event, INSERT((yearweek(substring(post_date, 1, 10))), 5, 0, '/ week-') AS week_number, count(id) AS total 
                                FROM %s 
                                    WHERE post_status = '$event' AND post_type = '$event_type' AND substring(post_date, 1, 10) between '$from' AND '$to' 
                                    GROUP BY INSERT((yearweek(substring(post_date, 1, 10))), 5, 0, '/ week-')
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
                                SELECT event, INSERT((yearweek(substring(event_date, 1, 10))), 5, 0, '/ week-') AS week_number, count(id) AS total 
                                FROM %s 
                                    WHERE event = '$event' AND event_type = 'user_items' AND substring(event_date, 1, 10) between '$from' AND '$to' 
                                    GROUP BY INSERT((yearweek(substring(event_date, 1, 10))), 5, 0, '/ week-')
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
                                SELECT event, INSERT((yearweek(substring(event_date, 1, 10))), 5, 0, '/ week-') AS week_number, count(id) AS total 
                                FROM %s 
                                    WHERE event = '$event' AND event_type = '$event_type' AND substring(event_date, 1, 10) between '$from' AND '$to' 
                                    GROUP BY INSERT((yearweek(substring(event_date, 1, 10))), 5, 0, '/ week-')
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
        else if(($collection_id == 'null' || is_null($collection_id)) and $filter == 'nofilter'){
            $SQL_query = sprintf(
                "SELECT event, count(id) AS total
                    FROM %s
                    WHERE event_type = '$event_type' AND event = '$event'
                ", self::_table() );
        }
        else {
            // Has collection id

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
                                    SELECT post_status AS event, INSERT((yearweek(substring(post_date, 1, 10))), 5, 0, '/ week-') AS week_number, count(id) AS total 
                                    FROM %s 
                                        WHERE post_status = '$event' AND post_type = '$event_type' AND collection_id = '$collection_id' AND substring(post_date, 1, 10) between '$from' AND '$to' 
                                        GROUP BY INSERT((yearweek(substring(post_date, 1, 10))), 5, 0, '/ week-')
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
                                    SELECT event, INSERT((yearweek(substring(event_date, 1, 10))), 5, 0, '/ week-') AS week_number, count(id) AS total 
                                    FROM %s 
                                        WHERE event = '$event' AND event_type = 'user_items' AND collection_id = '$collection_id' AND substring(event_date, 1, 10) between '$from' AND '$to' 
                                        GROUP BY INSERT((yearweek(substring(event_date, 1, 10))), 5, 0, '/ week-')
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
                                    SELECT event, INSERT((yearweek(substring(event_date, 1, 10))), 5, 0, '/ week-') AS week_number, count(id) AS total 
                                    FROM %s 
                                        WHERE event = '$event' AND event_type = '$event_type' AND collection_id = '$collection_id' AND substring(event_date, 1, 10) between '$from' AND '$to' 
                                        GROUP BY INSERT((yearweek(substring(event_date, 1, 10))), 5, 0, '/ week-')
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
            else if($filter == 'nofilter'){
                $SQL_query = sprintf(
                    "SELECT event, count(id) AS total
                        FROM %s
                        WHERE event_type = '$event_type' AND event = '$event' AND  collection_id = '$collection_id'
                    ", self::_table() );
            }
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
            case 'category':
                return ['events' => self::getDefaultFields()];
            case 'user_collection':
                return ['events' => self::getDefaultFields()];
            case 'comments':
                return ['events' => ['add', 'edit', 'delete']];
            case 'tags':
                return ['events' => self::getDefaultFields()];
            case 'user':
                return ['events' => ['view', 'vote'] ];
            case 'status':
                return ['events' => ['login', 'register', 'delete_user'] ];
            case 'general_status':
                return ['events' => self::getDefaultFields(['download', 'vote', 'publish', 'draft', 'trash'])];
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

    public static function user_events($event_type, $spec, $from, $to, $collection_id = NULL, $filter, $detail) {
        $_events_ = self::get_event_type($spec);

        if($event_type == 'general_status_items'){
            $event_type = 'user_items';
        }

        if($detail == 'detail'){
            $value_detail = self::getValuesDetail($event_type, $spec, $from, $to, $collection_id);
            $vdtl_data = array();
            
            foreach ($value_detail as $valdt => $data) {
                if($event_type == 'users' || $event_type == 'administration' || $event_type == 'busca' || $event_type == 'importexport'){
                    array_push($vdtl_data, [$data->user, $data->date]);
                }
                else if($event_type == 'c_items'){
                    array_push($vdtl_data, [$data->user, $data->item, $data->collection, $data->date]);
                }
                else if($event_type == 'items' || $event_type == 'collections' || $event_type == 'comments' || $event_type = 'categories'){
                    array_push($vdtl_data, [$data->user, $data->item, $data->date]);
                }
            }

            $stat_data = ["stat_object" => $vdtl_data];
            $return_data = json_encode($stat_data, JSON_NUMERIC_CHECK, JSON_FORCE_OBJECT);

            return $return_data;
        }
        else if( "top_collections" == $event_type ) {
            $top_collections = self::getTopCollections($from, $to, $filter);
            $cols_array = array();
            $events = array();

            foreach ($top_collections as $col => $data) {
                $_title = get_post($data->event)->post_title; // Point to position of collection in database table.
                //array_push($cols_array, [$_title => $col->total_collection ]);
                if($filter == 'nofilter'){
                    array_push($cols_array, [ $_title, $data->total ]);
                    array_push($events, $_title);
                }
                else if($filter != 'weeks'){
                    array_push($cols_array, [ $_title, $data->date, $data->total, $data->event_total ]);
                }
                else{
                    array_push($cols_array, [ $_title, $data->week_number, $data->total, $data->event_total ]);
                }
                array_push($events, $_title);
            }

            $stat_data = [ "stat_object" => $cols_array, "columns" => array_values(array_unique($events)) ];
            $return_data = json_encode($stat_data, JSON_NUMERIC_CHECK, JSON_FORCE_OBJECT);

            return $return_data;
        }
        else if("repo_searches" == $event_type || $event_type == "collection_searches") {
            $searches = self::getFrequentSearches($collection_id, $from, $to, $filter);

            $sarr = array();
            $events = array();
            foreach($searches as $key => $data) {
                if($filter == 'nofilter-dash'){
                    array_push($sarr, [ $data->event, $data->total ]);
                }
                else if($filter == 'nofilter'){
                    array_push($sarr, [ $data->event, $data->total ]);
                    array_push($events, $data->event);
                }
                else if($filter != 'weeks'){
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
                    else if($filter == 'nofilter'){
                        foreach ($results as $key => $data){
                            $_stats[] = [ $data->event, $data->total ];
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

            $SQL_query = sprintf(
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
            $SQL_query = sprintf(
                "SELECT event, week_number, total, event_total 
                    FROM (
                        (
                            SELECT event, week_number, count(event) AS total 
                            FROM (
                                SELECT post_parent AS event, yearweek(substring(post_date, 1, 10)) AS week_number 
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
            $SQL_query = sprintf(
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
        else if($filter == 'nofilter'){
            $SQL_query = sprintf(
                "SELECT post_parent AS event, count(*) AS total 
                    FROM %s 
                    WHERE post_parent > 0 AND post_type = 'socialdb_object'
                    GROUP BY post_parent 
                    ORDER BY count(*) DESC
                ", self::_posts_table());
        }

        $top_collections = $wpdb->get_results($SQL_query);

        return $top_collections;
    }

    public function getFrequentSearches( $collection_id = NULL, $from, $to, $filter) {
        global $wpdb;

        if(($collection_id == 'null' || is_null($collection_id))){
            $event_type = '%%search%%'; //this don't work
           
            if($filter == 'days' ) {
                $SQL_query = sprintf(
                    "SELECT event, date, total, event_total 
                    FROM (
                            (
                            SELECT event, substring(event_date, 1, 10) AS date, count(event) AS total 
                            FROM %s 
                                WHERE event_type like '". $event_type ."' AND (substring(event_date, 1, 10)) between '$from' AND '$to'
                                GROUP BY event, (substring(event_date, 1, 10))
                            ) res1 
                            JOIN (
                                SELECT event AS event2, count(event) AS event_total
                                FROM %s
                                    WHERE event_type like '". $event_type ."' AND (substring(event_date, 1, 10)) between '$from' AND '$to'
                                    GROUP BY event
                            ) res2
                            ON res1.event = res2.event2
                    ) ORDER BY event_total DESC ", self::_table(), self::_table());
            } 
            else if($filter == 'weeks' ){

                $SQL_query = sprintf(
                    "SELECT event, week_number, total, event_total
                    FROM (
                        (
                            SELECT event, yearweek(substring(event_date, 1, 10)) as week_number, count(event) AS total
                            FROM %s
                                WHERE event_type like '". $event_type ."'  AND (substring(event_date, 1, 10)) between '$from' AND '$to'
                                GROUP BY event, (yearweek(substring(event_date, 1, 10)))
                        ) res1
                        JOIN (
                            SELECT event AS event2, count(event) AS event_total
                            FROM %s
                                WHERE event_type like '". $event_type ."'  AND (substring(event_date, 1, 10)) between '$from' AND '$to'
                                GROUP BY event
                        ) res2
                        ON res1.event = res2.event2
                    ) ORDER BY event_total DESC ", self::_table(), self::_table());
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
                                WHERE event_type like '". $event_type ."'  AND (substring(event_date, 1, 7)) between '$fr' AND '$t'
                                GROUP BY event, (substring(event_date, 1, 7))
                            ) res1 
                            JOIN (
                                SELECT event AS event2, count(event) AS event_total
                                FROM %s
                                    WHERE event_type like '". $event_type ."'  AND (substring(event_date, 1, 7)) between '$fr' AND '$t'
                                    GROUP BY event
                            ) res2
                            ON res1.event = res2.event2
                    ) ORDER BY event_total DESC ", self::_table(), self::_table());
            }
            else if($filter == 'nofilter-dash'){
                $SQL_query = sprintf(
                    "SELECT event, count(*) AS total 
                        FROM %s 
                        WHERE event_type like '". $event_type ."'
                        GROUP BY event 
                        ORDER BY count(*) DESC LIMIT 10
                    ", self::_table());
            }
            else if($filter == 'nofilter'){
                $SQL_query = sprintf(
                    "SELECT event, count(*) AS total 
                        FROM %s 
                        WHERE event_type like '". $event_type ."'
                        GROUP BY event 
                        ORDER BY count(*) DESC
                    ", self::_table());
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
                            SELECT event, yearweek(substring(event_date, 1, 10)) as week_number, count(event) AS total
                            FROM %s
                                WHERE event_type = '$event_type' AND collection_id = '$collection_id' AND (substring(event_date, 1, 10)) between '$from' AND '$to'
                                GROUP BY event, (yearweek(substring(event_date, 1, 10)))
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
            else if($filter == 'nofilter-dash'){
                $SQL_query = sprintf(
                    "SELECT event, count(*) AS total 
                        FROM %s 
                        WHERE event_type like '". $event_type ."'
                        GROUP BY event 
                        ORDER BY count(*) DESC LIMIT 10
                    ", self::_table());
            }
            else if($filter == 'nofilter'){
                $SQL_query = sprintf(
                    "SELECT event, count(*) AS total 
                        FROM %s 
                        WHERE event_type = '$event_type' AND collection_id = '$collection_id' 
                        GROUP BY event 
                        ORDER BY count(*) DESC
                    ", self::_table());
            }
        }

        $searches = $wpdb->get_results($SQL_query);

        return $searches;
    }

    public function getValuesDetail($report, $event, $from, $to, $collection_id){
        global $wpdb;
        
        if(is_null($collection_id) || $collection_id == 'null'){
            if($report == 'users'){
                $SQL_query = sprintf(
                    "SELECT user_login AS user, event_date AS date
                        FROM %s JOIN %s ON %s.ID = user_id
                        WHERE event = '$event' AND (substring(event_date, 1, 10)) BETWEEN '$from' AND '$to'
                    ", self::_table(), self::_users_table(), self::_users_table());
            }
            else if($report == 'items'){
                if($event == 'publish' || $event == 'draft' || $event == 'trash'){
                    $SQL_query = sprintf(
                        "SELECT user_login AS user, post_title AS item, post_date AS date
                            FROM %s, %s
                            WHERE post_author = %s.ID AND post_type = 'socialdb_object' AND post_status = '$event' AND (substring(post_date, 1, 10)) BETWEEN '$from' AND '$to'
                        ", self::_users_table(), self::_posts_table(), self::_users_table());
                        
                }
                else{
                    $SQL_query = sprintf(
                        "SELECT user_login AS user, post_title AS item, event_date AS date
                            FROM %s, %s, %s
                            WHERE %s.ID = item_id AND user_id = %s.ID AND event = '$event' AND (substring(event_date, 1, 10)) BETWEEN '$from' AND '$to'
                        ", self::_table(), self::_users_table(), self::_posts_table(), self::_posts_table(), self::_users_table());
                } 
            }
            else if($report == 'collections' || $report == 'c_items'){
                if($event == 'edit' || $event == 'view'){
                    $SQL_query = sprintf(
                        "SELECT user_login AS user, post_title AS item, post_date AS date 
                            FROM %s, %s, %s 
                            WHERE collection_id = %s.ID AND user_id = post_author AND post_author = %s.ID AND event_type = 'user_collection' 
                                AND event = '$event' AND substring(event_date, 1, 10) BETWEEN '$from' AND '$to'
                            ", self::_table(), self::_users_table(), self::_posts_table(), self::_posts_table(), self::_users_table());
                }
                else if($event == 'add' || $event == 'delete'){
                    $event = ($event == 'add') ? 'Create' : 'Delete';
                    $title_event = $event .' the Collection%%';

                    $SQL_query = sprintf(
                        "SELECT user_login AS user, substring(post_title, 23) as item, post_date AS date 
                            FROM %s, %s where post_author = %s.ID and post_title like '". $title_event ."' AND substring(post_date, 1, 10) BETWEEN '$from' AND '$to'
                        ", self::_posts_table(), self::_users_table(), self::_users_table());
                }
                else{
                    // Para os itens
                    $SQL_query = sprintf(
                        "SELECT user, item, collection, date 
                            FROM (
                                (
                                    SELECT post_title AS collection, ID 
                                        FROM %s 
                                        WHERE post_type = 'socialdb_collection' AND post_status = 'publish' AND post_title = '$event'
                                ) A 
                                JOIN (
                                    SELECT post_title AS item, post_date AS date, post_parent, user_login AS user 
                                        FROM %s, %s
                                        WHERE post_author = %s.ID AND post_type = 'socialdb_object' AND substring(post_date, 1, 10) BETWEEN '$from' AND '$to'
                                    ) B 
                                ON A.ID = B.post_parent)
                        ", self::_posts_table(), self::_posts_table(), self::_users_table(), self::_users_table());
                }
            }
            else if($report == 'comments'){
                $SQL_query = sprintf(
                    "SELECT user_login AS user, post_title AS item, event_date AS date 
                        FROM %s, %s, %s 
                        WHERE item_id = %s.ID AND %s.ID = post_author AND user_id = post_author AND event='$event' AND event_type = 'comment' AND substring(event_date, 1, 10) BETWEEN '$from' AND '$to'
                    ", self::_table(), self::_posts_table(), self::_users_table(), self::_posts_table(), self::_users_table());
            }
            else if($report == 'categories' || $report == 'tags'){
                if($event == 'add' || $event == 'edit' || $event == 'delete'){
                    $event = ($event == 'add') ? 'Create' : (($event == 'edit') ? 'Edit' : 'Delete');
                    $title_event = ($report == 'categories') ? $event .' the category%%' : $event .' the tag%%';

                    $SQL_query = sprintf(
                        "SELECT user_login AS user, substring(post_title, locate('(', post_title)) AS item, post_date AS date 
                            FROM %s, %s 
                            WHERE post_author = %s.ID AND post_title LIKE '". $title_event ."' AND substring(post_date, 1, 10) BETWEEN '$from' AND '$to'
                        ", self::_users_table(), self::_posts_table(), self::_users_table());
                }
                else if($event == 'view'){
                    $evtype = ($report == 'categories') ? 'user_category' : 'tags';
                    
                    $SQL_query = sprintf(
                        "SELECT user_login AS user, substring(post_title, locate('(', post_title)) AS item, post_date AS date 
                            FROM %s, %s, %s 
                            WHERE post_author = %s.ID AND post_author = user_id AND resource_id = %s.ID AND event = '$event' AND event_type = '". $evtype ."' AND substring(post_date, 1, 10) BETWEEN '$from' AND '$to'
                        ", self::_users_table(), self::_table(), self::_posts_table(), self::_users_table(), self::_posts_table());
                }
            }
            else if($report == 'administration' || $report == 'busca' || $report == 'importexport'){
                $evtype = ($report == 'administration') ? 'event_type = \'collection_admin\'' : ($report == 'busca') ? 'event_type like \'collection_search\'' : 'event_type = \'imports\'';

                $SQL_query = sprintf(
                    "SELECT user_login AS user, event_date AS date 
                        FROM %s, %s 
                        WHERE user_id = %s.ID AND ". $evtype ." AND event = '$event' AND substring(event_date, 1, 10) BETWEEN '$from' AND '$to'
                    ", self::_users_table(), self::_table(), self::_users_table());
            }
        }
        else{
            if($report == 'items'){
                if($event == 'publish' || $event == 'draft' || $event == 'trash'){
                    $SQL_query = sprintf(
                        "SELECT user_login AS user, post_title AS item, post_date AS date
                            FROM %s, %s
                            WHERE post_author = %s.ID AND post_type = 'socialdb_object' AND post_status = '$event' 
                                AND (substring(post_date, 1, 10)) BETWEEN '$from' AND '$to' AND %s.id = '$collection_id'
                        ", self::_users_table(), self::_posts_table(), self::_users_table(), self::_posts_table());
                        
                }
                else{
                    $SQL_query = sprintf(
                        "SELECT user_login AS user, post_title AS item, event_date AS date
                            FROM %s, %s, %s
                            WHERE %s.ID = item_id AND user_id = %s.ID AND event = '$event' AND (substring(event_date, 1, 10)) BETWEEN '$from' AND '$to' 
                                AND %s.collection_id = '$collection_id'
                        ", self::_table(), self::_users_table(), self::_posts_table(), self::_posts_table(), self::_users_table(), self::_table());
                } 
            }
            else if($report == 'collections' || $report == 'c_items'){
                    $SQL_query = sprintf(
                        "SELECT user, item, collection, date 
                            FROM (
                                (
                                    SELECT post_title AS collection, ID 
                                        FROM %s 
                                        WHERE post_type = 'socialdb_collection' AND post_status = 'publish' AND post_title = '$event' AND id = '$collection_id'
                                ) A 
                                JOIN (
                                    SELECT post_title AS item, post_date AS date, post_parent, user_login AS user 
                                        FROM %s, %s
                                        WHERE post_author = %s.ID AND post_type = 'socialdb_object' AND substring(post_date, 1, 10) BETWEEN '$from' AND '$to' 
                                            AND %s.id = '$collection_id'
                                    ) B 
                                ON A.ID = B.post_parent)
                        ", self::_posts_table(), self::_posts_table(), self::_users_table(), self::_users_table(), self::_posts_table());
            }
            else if($report == 'comments'){
                $SQL_query = sprintf(
                    "SELECT user_login AS user, post_title AS item, event_date AS date 
                        FROM %s, %s, %s 
                        WHERE item_id = %s.ID AND %s.ID = post_author AND user_id = post_author AND event='$event' AND event_type = 'comment' 
                            AND substring(event_date, 1, 10) BETWEEN '$from' AND '$to' AND %s.collection_id = '$collection_id'
                    ", self::_table(), self::_posts_table(), self::_users_table(), self::_posts_table(), self::_users_table(), self::_table());
            }
            else if($report == 'categories' || $report == 'tags'){
                if($event == 'add' || $event == 'edit' || $event == 'delete'){
                    $SQL_query = sprintf(
                        "SELECT user_login AS user, substring(post_title, locate('(', post_title)) AS item, post_date AS date 
                            FROM %s, %s, %s 
                            WHERE post_author = %s.ID AND event = '$event' AND substring(post_date, 1, 10) BETWEEN '$from' AND '$to'
                                AND collection_id = '$collection_id' AND resource_id = %s.id
                        ", self::_users_table(), self::_posts_table(), self::_table(), self::_users_table(), self::_posts_table());
                }
                else if($event == 'view'){
                    $evtype = ($report == 'categories') ? 'user_category' : 'tags';
                    
                    $SQL_query = sprintf(
                        "SELECT user_login AS user, substring(post_title, locate('(', post_title)) AS item, post_date AS date 
                            FROM %s, %s, %s 
                            WHERE post_author = %s.ID AND post_author = user_id AND resource_id = %s.ID AND event = '$event' AND event_type = '". $evtype ."' 
                                AND substring(post_date, 1, 10) BETWEEN '$from' AND '$to' AND %s.collection_id = '$collection_id'
                        ", self::_users_table(), self::_table(), self::_posts_table(), self::_users_table(), self::_posts_table(), self::_table());
                }
            }
            else if($report == 'administration' || $report == 'busca' || $report == 'importexport'){
                $evtype = ($report == 'administration') ? 'event_type = \'collection_admin\'' : ($report == 'busca') ? 'event_type like \'collection_search\'' : 'event_type = \'collection_imports\'';

                $SQL_query = sprintf(
                    "SELECT user_login AS user, event_date AS date 
                        FROM %s, %s 
                        WHERE user_id = %s.ID AND ". $evtype ." AND event = '$event' AND substring(event_date, 1, 10) BETWEEN '$from' AND '$to'
                            AND collection_id = '$collection_id'
                    ", self::_users_table(), self::_table(), self::_users_table());
            }
        }

        $value_detail = $wpdb->get_results($SQL_query);

        return $value_detail;
    }
}