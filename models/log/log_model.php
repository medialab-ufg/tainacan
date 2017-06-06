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
        $_alias = "total_" . $event;

        if(($collection_id == 'null' || is_null($collection_id)) and $filter == 'months'){
            $fr = substr($from, 0, 7);
            $t = substr($to, 0, 7);

            if($event == 'publish' || $event == 'draft' || $event == 'trash'){
                $SQL_query = sprintf(
                    "SELECT * from((select *
                        from (select post_status as event, substring(post_date, 1, 7) as date, count(id) as total
                            from %s
                                where post_type = 'socialdb_object' and post_status='$event'
                                group by (substring(post_date, 1, 7)))
                            res1
                        where date between '$fr' and '$t')
                        res2 natural join (select count(id) as event_total from %s
                            where post_status = '$event' and post_type = 'socialdb_object' and substring(post_date, 1, 7) between '$fr' and '$t')
                        res3)", self::_posts_table(), self::_posts_table());
                //SELECT COUNT(id) as total_active from wp_posts WHERE post_type='socialdb_object' AND post_status='publish'
            }
            else if($event == 'delete' and $event_type == 'general_status_items'){
                $SQL_query = sprintf(
                "SELECT * from ((select * 
                            from (select event, substring(event_date, 1, 7) as date, count(id) as total 
                                from %s
                                where event = '$event' and event_type = 'user_items' 
                                    group by (substring(event_date, 1, 7))) 
                            res1  
                            where date between '$from' and '$to') 
                            res2 natural join (select count(id) as event_total from %s 
                                where event = '$event' and event_type = 'user_items' and substring(event_date, 1, 7) between '$fr' and '$t') 
                                res3)", self::_table(), self::_table());
            }
            else{
                $SQL_query = sprintf(
                    "SELECT * from ((select * 
                            from (select event, substring(event_date, 1, 7) as date, count(id)  as total 
                                from %s 
                                where event = '$event' and event_type = '$event_type' 
                                    group by (substring(event_date, 1, 7))) 
                            res1  
                            where date between '$fr' and '$t') 
                            res2 
                                natural join (select count(id) as event_total from %s 
                                where event = '$event' and event_type = '$event_type' and substring(event_date, 1, 7) between '$fr' and '$t') 
                                res3)", self::_table(), self::_table());
            }
        }
        else if(($collection_id == 'null' || is_null($collection_id)) and $filter == 'weeks'){
            if($event == 'publish' || $event == 'draft' || $event == 'trash'){
                $SQL_query = sprintf(
                    "SELECT * from ((select * 
                            from (select post_status as event, (week(substring(post_date, 1, 10))) as week_number, count(id) as total 
                                from %s 
                                where post_status = '$event' and post_type = '$event_type' and substring(post_date, 1, 10) between '$from' and '$to' 
                                    group by (week(substring(post_date, 1, 10)))) 
                            res1) 
                            res2 
                            natural join (select count(id) as event_total from %s 
                            where post_status = '$event' and post_type = '$event_type' and substring(post_date, 1, 10) between '$from' and '$to') 
                            res3)", self::_posts_table(), self::_posts_table());
            }
            else if($event == 'delete' and $event_type == 'general_status_items'){
                $SQL_query = sprintf(
                "SELECT * from ((select * 
                            from (select event, (week(substring(event_date, 1, 10))) as week_number, count(id) as total 
                                from %s 
                                where event = '$event' and event_type = 'user_items' and substring(event_date, 1, 10) between '$from' and '$to' 
                                    group by (week(substring(event_date, 1, 10)))) 
                            res1) 
                            res2 
                            natural join (select count(id) as event_total from %s 
                            where event = '$event' and event_type = 'user_items' and substring(event_date, 1, 10) between '$from' and '$to') 
                            res3)", self::_table(), self::_table());
            }
            else{
                $SQL_query = sprintf(
                    "SELECT * from ((select * 
                            from (select event, (week(substring(event_date, 1, 10))) as week_number, count(id) as total 
                                from %s 
                                where event = '$event' and event_type = '$event_type' and substring(event_date, 1, 10) between '$from' and '$to' 
                                    group by (week(substring(event_date, 1, 10)))) 
                            res1) 
                            res2 
                            natural join (select count(id) as event_total from %s 
                            where event = '$event' and event_type = '$event_type' and substring(event_date, 1, 10) between '$from' and '$to') 
                            res3)", self::_table(), self::_table());
            }
        }
        else if(($collection_id == 'null' || is_null($collection_id)) and $filter == 'days'){
            if($event == 'publish' || $event == 'draft' || $event == 'trash'){
                $SQL_query = sprintf(
                    "SELECT * from((select *
                        from (select post_status as event, substring(post_date, 1, 10) as date, count(id) as total
                        from %s
                        where post_type = 'socialdb_object' and post_status='$event'
                        group by (substring(post_date, 1, 10)))
                        res1
                        where date between '$from' and '$to')
                        res2 natural join (select count(id) as event_total from %s
                        where post_status = '$event' and post_type = 'socialdb_object' and substring(post_date, 1, 10) between '$from' and '$to')
                        res3)", self::_posts_table(), self::_posts_table());
            }
            else if($event == 'delete' and $event_type == 'general_status_items'){
                $SQL_query = sprintf(
                "SELECT * from ((select * 
                            from (select event, substring(event_date, 1, 10) as date, count(id) as total 
                                from %s
                                where event = '$event' and event_type = 'user_items' 
                                    group by (substring(event_date, 1, 10))) 
                            res1  
                            where date between '$from' and '$to') 
                            res2 natural join (select count(id) as event_total from %s 
                                where event = '$event' and event_type = 'user_items' and substring(event_date, 1, 10) between '$from' and '$to') 
                                res3)", self::_table(), self::_table());
            }
            else{
                $SQL_query = sprintf(
                    "SELECT * from ((select * 
                            from (select event, substring(event_date, 1, 10) as date, count(id) as total 
                                from %s
                                where event = '$event' and event_type = '$event_type' 
                                    group by (substring(event_date, 1, 10))) 
                            res1  
                            where date between '$from' and '$to') 
                            res2 natural join (select count(id) as event_total from %s 
                                where event = '$event' and event_type = '$event_type' and substring(event_date, 1, 10) between '$from' and '$to') 
                                res3)", self::_table(), self::_table());
            }
        }
        else {
            $SQL_query = sprintf("SELECT COUNT(id) as '$_alias' FROM %s WHERE event_type = '$event_type' AND event = '$event'
                   AND collection_id = '$collection_id' AND event_date BETWEEN '$from' AND '$to'", self::_table() );
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
        // else if("general_status_items" === $event_type) {
        //     return self::getItemsStatus($spec, $collection_id);
        // } 
        else if("repo_searches" == $event_type || $event_type == "collection_searches") {
            return self::getFrequentSearches($collection_id);
        } else {
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
                from ((select event, date, count(event) as total 
                from (select post_parent as event, substring(post_date, 1, 7) as date 
                from %s 
                where post_parent > 0 and post_type = 'socialdb_object' and substring(post_date, 1, 7) between '$fr' and '$t') res1  
                group by event, date) res2 
                join (select post_parent, count(id) as event_total 
                from %s
                where post_parent > 0 and post_type = 'socialdb_object' and substring(post_date, 1, 7) between '$fr' and '$t' 
                group by post_parent) res3 on res2.event = res3.post_parent)", self::_posts_table(), self::_posts_table());
        }
        else if($filter == 'weeks'){
            $sql = sprintf(
                "SELECT event, week_number, total, event_total 
                from ((select event, week_number, count(event) as total 
                from (select post_parent as event, week(substring(post_date, 1, 10)) as week_number 
                from %s 
                where post_parent > 0 and post_type = 'socialdb_object' and substring(post_date, 1, 10) between '$from' and '$to') res1 
                group by event, week_number) res2 
                join (select post_parent, count(id) as event_total 
                from %s 
                where post_parent > 0 and post_type = 'socialdb_object' and substring(post_date, 1, 10) between '$from' and '$to' 
                group by post_parent) res3 on res2.event = res3.post_parent);", self::_posts_table(), self::_posts_table());
        }
        else if($filter == 'days'){
            $sql = sprintf(
                "SELECT event, date, total, event_total 
                from ((select event, date, count(event) as total 
                from (select post_parent as event, substring(post_date, 1, 10) as date 
                from %s 
                where post_parent > 0 and post_type = 'socialdb_object' and substring(post_date, 1, 10) between '$from' and '$to') res1  
                group by event, date) res2 
                join (select post_parent, count(id) as event_total 
                from %s 
                where post_parent > 0 and post_type = 'socialdb_object' and substring(post_date, 1, 10) between '$from' and '$to' 
                group by post_parent) res3 on res2.event = res3.post_parent);", self::_posts_table(), self::_posts_table());
        }
        // $sql = sprintf("SELECT post_parent, COUNT(id) as total_collection FROM %s WHERE post_type='socialdb_object' AND post_parent > 0 GROUP BY post_parent ORDER BY COUNT(*) DESC;", self::_posts_table());
        $top_collections = $wpdb->get_results($sql);
        //$stat_data = ["quality_stat" => $cols_array, "color" => '#73880a'];
        //return json_encode( $stat_data );
        return $top_collections;
    }

    public function getFrequentSearches( $collection_id = NULL) {
        global $wpdb;

        if($collection_id == 'null' || is_null($collection_id) ) {
            $sql = sprintf(
            "SELECT event as term, COUNT(*) as t_count 
            FROM %s 
            WHERE event_type='advanced_search' 
            GROUP BY event ORDER BY COUNT(*) DESC", self::_table());
        } else {
            $sql = sprintf("SELECT event as term, COUNT(*) as t_count FROM %s WHERE event_type='collection_search' AND collection_id='$collection_id' GROUP BY event ORDER BY COUNT(*) DESC", self::_table());
        }

        $_searches = $wpdb->get_results($sql);

        $_s_arr = [];
        foreach($_searches as $_s) {
            array_push( $_s_arr, [ $_s->term => $_s->t_count]);
        }

        $stat_data = [ "stat_title" => [ 'Buscas Frequentes', 'Qtd' ], "quality_stat" => $_s_arr, "color" => 'NO_CHART'];
        return json_encode( $stat_data );
    }

    // public function getItemsStatus($spec, $collection_id = NULL) {
    //     global $wpdb;

    //     if( $collection_id == 'null' || is_null($collection_id) ) {
    //         $status['active'] = sprintf("SELECT COUNT(id) as total_active from wp_posts WHERE post_type='socialdb_object' AND post_status='publish'", self::_posts_table());
    //         $status['draft'] = sprintf("SELECT COUNT(id) as total_draft from wp_posts WHERE post_type='socialdb_object' AND post_status='draft'", self::_posts_table());
    //         $status['trash'] = sprintf("SELECT COUNT(id) as total_trash from wp_posts WHERE post_type='socialdb_object' AND post_status='trash'", self::_posts_table());
    //         $status['deleted'] = sprintf("SELECT count(id) as total_delete from wp_statistics WHERE event_type='user_items' AND event='delete'",self::_table());
    //     } else {
    //         $status['active'] = sprintf("SELECT COUNT(id) as total_active from wp_posts WHERE post_type='socialdb_object' AND post_status='publish' AND post_parent='$collection_id'", self::_posts_table());
    //         $status['draft'] = sprintf("SELECT COUNT(id) as total_draft from wp_posts WHERE post_type='socialdb_object' AND post_status='draft'  AND post_parent='$collection_id'", self::_posts_table());
    //         $status['trash'] = sprintf("SELECT COUNT(id) as total_trash from wp_posts WHERE post_type='socialdb_object' AND post_status='trash' AND post_parent='$collection_id'", self::_posts_table());
    //         $status['deleted'] = sprintf("SELECT count(id) as total_delete from wp_statistics WHERE event_type='user_items' AND event='delete' AND collection_id='$collection_id'",self::_table());
    //     }
    //     //post_date
    //     $_res = [];
    //     $_stat_obj = self::get_event_type($spec);
    //     foreach($status as $s) {
    //         $_obj = array_pop($wpdb->get_results($s));
    //         array_push( $_res, $_obj );
    //     }
    //     $stat_data = ["stat_title" => ['Status dos Itens', '#'], "stat_object" => $_res, "color" => $_stat_obj['color'], "item_status" => true];
    //     return json_encode($stat_data);
    // }

    public static function getUserStatus($event) {
        global $wpdb;
        $sql = sprintf("SELECT COUNT(id) as total_status FROM %s WHERE event_type = 'user_collection' AND event = '$event'", self::_table() );
        return json_encode( $wpdb->get_results($sql) );
    }
}