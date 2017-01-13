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

    public static function getUserEvents($event_type, $event, $encoded = true, $from = '', $to = '', $collection_id = NULL) {
        global $wpdb;
        $_alias = "total_" . $event;

        if(empty($from)) {
            $from = "2016-01-01";
        }
        if(empty($to)) {
            $to = date("Y-m-d", strtotime("tomorrow"));
        }

        if( $collection_id == 'null' || is_null($collection_id) ) {
            $sql = sprintf("SELECT COUNT(id) as '$_alias' FROM %s WHERE event_type = '$event_type' AND event = '$event' AND event_date BETWEEN '$from' AND '$to'", self::_table() );
        } else {
            $sql = sprintf("SELECT COUNT(id) as '$_alias' FROM %s WHERE event_type = '$event_type' AND event = '$event' 
                   AND collection_id = '$collection_id' AND event_date BETWEEN '$from' AND '$to'", self::_table() );
        }

        if( $encoded ) {
            return json_encode( $wpdb->get_results($sql) );
        } else {
            return $wpdb->get_results($sql, ARRAY_N);
        }
    }
    
    private function get_event_type($spec) {
        switch($spec) {
            case 'items':
                return ['color' => '#0EEAFF', 'events' => self::getDefaultFields(['download', 'vote'])];
            case 'category':
                return ['color' => '#D6DF22', 'events' => self::getDefaultFields()];
            case 'collection':
                return ['color' => '#149271', 'events' => self::getDefaultFields()];
            case 'comments':
                return ['color' => '#8DC53E', 'events' => self::getDefaultFields()];
            case 'tags':
                return ['color' => 'orange', 'events' => self::getDefaultFields()];
            case 'user':
                return ['color' => '#E63333', 'events' => ['view', 'comment', 'vote'] ];
            case 'status':
                return ['color' => '#79A7CF', 'events' => ['login', 'register', 'delete_user'] ];
            case 'general_status':
                return ['color' => '#EF4C28', 'events' => ['active', 'draft', 'trash', 'delete'] ];
            case 'profile':
                return ['color' => '#F09302', 'events' => ['subscriber', 'administrator', 'editor', 'author', 'contributor'] ];
            case 'imports':
                return ['color' => '#43F7B1', 'events' => ['access_oai_pmh', 'import_csv', 'export_csv', 'import_tainacan', 'export_tainacan'] ];
            case 'collection_imports':
                return ['color' => '#CC181E', 'events' => ['access_oai_pmh', 'harvest_oai_pmh', 'import_csv', 'export_csv'] ];
            case 'admin':
                return ['color' => '#027758', 'events' => ['config', 'metadata', 'keys', 'licenses', 'welcome_mail', 'tools'] ];
            case 'collection_admin':
                return ['color' => 'goldenrod', 'events' => ['config', 'metadata', 'layout', 'social_media', 'licenses', 'import', 'export'] ];
        }
    }

    private function getDefaultFields($extra_fields = "") {
        $base_defaults = ['add', 'view', 'edit', 'delete'];
        return ( is_array($extra_fields) && !empty($extra_fields) ) ? array_merge($base_defaults, $extra_fields) : $base_defaults;
    }

    public static function user_events($event_type, $spec, $from, $to, $collection_id = NULL) {
        $_events_ = self::get_event_type($spec);

        if( "top_collections_items" == $event_type ) {
            return self::getTopCollections();
        } else if("general_status_items" === $event_type) {
            return self::getItemsStatus($spec, $collection_id);
        } else if("repo_searches" == $event_type || $event_type == "collection_searches") {
            return self::getFrequentSearches($collection_id);
        } else {
            if($_events_) {
                $_stats = [];
                foreach ($_events_['events'] as $ev) {
                    $evt_count_ = self::getUserEvents($event_type, $ev, false, $from, $to, $collection_id);
                    $l_data = array_pop($evt_count_);
                    $_stats[] = $l_data[0];
                }
                $prepared_struct = array_combine($_events_['events'], $_stats);
                $stat_data = [ "stat_title" => [ 'Coleções do Usuário', 'Qtd' ], "stat_object" => $prepared_struct, "color" => $_events_['color']  ];
            } else {
                $stat_data = [];
            }

            return json_encode($stat_data);
        }
    }

    public function getTopCollections() {
        global $wpdb;
        $sql = sprintf("SELECT post_parent, COUNT(id) as total_collection FROM %s WHERE post_type='socialdb_object' AND post_parent > 0 GROUP BY post_parent ORDER BY COUNT(*) DESC;", self::_posts_table());
        $top_collections = $wpdb->get_results($sql);
        $cols_array = [];
        foreach ($top_collections as $col) {
            $_title = get_post($col->post_parent)->post_title;
            array_push($cols_array, [$_title => $col->total_collection ]);
        }
        $stat_data = [ "stat_title" => [ 'Coleções do Usuário', 'Qtd' ], "quality_stat" => $cols_array, "color" => '#73880a'];
        return json_encode( $stat_data );
    }

    public function getFrequentSearches( $collection_id = NULL) {
        global $wpdb;

        if($collection_id == 'null' || is_null($collection_id) ) {
            $sql = sprintf("SELECT event as term, COUNT(*) as t_count FROM %s WHERE event_type='advanced_search' GROUP BY event ORDER BY COUNT(*) DESC", self::_table());
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

    public function getItemsStatus($spec, $collection_id = NULL) {
        global $wpdb;

        if( $collection_id == 'null' || is_null($collection_id) ) {
            $status['active'] = sprintf("SELECT COUNT(id) as total_active from wp_posts WHERE post_type='socialdb_object' AND post_status='publish'", self::_posts_table());
            $status['draft'] = sprintf("SELECT COUNT(id) as total_draft from wp_posts WHERE post_type='socialdb_object' AND post_status='draft'", self::_posts_table());
            $status['trash'] = sprintf("SELECT COUNT(id) as total_trash from wp_posts WHERE post_type='socialdb_object' AND post_status='trash'", self::_posts_table());
            $status['deleted'] = sprintf("SELECT count(id) as total_delete from wp_statistics WHERE event_type='user_items' AND event='delete'",self::_table());
        } else {
            $status['active'] = sprintf("SELECT COUNT(id) as total_active from wp_posts WHERE post_type='socialdb_object' AND post_status='publish' AND post_parent='$collection_id'", self::_posts_table());
            $status['draft'] = sprintf("SELECT COUNT(id) as total_draft from wp_posts WHERE post_type='socialdb_object' AND post_status='draft'  AND post_parent='$collection_id'", self::_posts_table());
            $status['trash'] = sprintf("SELECT COUNT(id) as total_trash from wp_posts WHERE post_type='socialdb_object' AND post_status='trash' AND post_parent='$collection_id'", self::_posts_table());
            $status['deleted'] = sprintf("SELECT count(id) as total_delete from wp_statistics WHERE event_type='user_items' AND event='delete' AND collection_id='$collection_id'",self::_table());
        }

        $_res = [];
        $_stat_obj = self::get_event_type($spec);
        foreach($status as $s) {
            $_obj = array_pop($wpdb->get_results($s));
            array_push( $_res, $_obj );
        }
        $stat_data = ["stat_title" => ['Status dos Itens', '#'], "stat_object" => $_res, "color" => $_stat_obj['color'], "item_status" => true];
        return json_encode($stat_data);
    }

    public static function getUserStatus($event) {
        global $wpdb;
        $sql = sprintf("SELECT COUNT(id) as total_status FROM %s WHERE event_type = 'user_collection' AND event = '$event'", self::_table() );
        return json_encode( $wpdb->get_results($sql) );
    }
}