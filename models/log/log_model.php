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
        $final_data = array_merge($logData, self::getCommonFields() );
        return $wpdb->insert(self::_table(), $final_data);
    }
    
    private function getCommonFields() {
        return ['ip' => $_SERVER['REMOTE_ADDR'], 'event_date' => date('Y-m-d H:i:s')];
    }

    public static function getUserEvents($event_type, $event, $encoded = true, $from = '', $to = '') {
        global $wpdb;
        $_alias = "total_" . $event;

        if(empty($from)) {
            $from = "2016-01-01";
        }
        if(empty($to)) {
            $to = date("Y-m-d");
        }

        $sql = sprintf("SELECT COUNT(id) as '$_alias' FROM %s WHERE event_type = '$event_type' AND event = '$event' AND event_date BETWEEN '$from' AND '$to' ", self::_table() );
        
        if( $encoded ) {
            return json_encode( $wpdb->get_results($sql) );
        } else {
            return $wpdb->get_results($sql, ARRAY_N);
        }
    }
/*
    public static function totalItemsStatus($event, $encoded = true, $from = '', $to = '') {
        global $wpdb;
        $_alias = "total_" . $event;

        if(empty($from)) { $from = "2016-01-01"; }
        if(empty($to)) { $to = date("Y-m-d"); }

        $sql = sprintf("SELECT COUNT(id) as '$_alias' FROM %s WHERE post_type='socialdb_object' AND  post_status='$event' AND event_date BETWEEN '$from' AND '$to' ", self::_posts_table() );

        if( $encoded ) {
            return json_encode( $wpdb->get_results($sql) );
        } else {
            return $wpdb->get_results($sql, ARRAY_N);
        }
    }
*/
    
    private function get_event_type($spec) {
        switch($spec) {
            case 'items':
                return ['color' => '#0EEAFF', 'events' => self::getDefaultFields('download')];
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
        }
    }

    private function getDefaultFields($extra_fields = "") {
        $base_defaults = ['add', 'view', 'edit', 'delete'];
        return ( is_array($extra_fields) && !empty($extra_fields) ) ? array_merge($base_defaults, $extra_fields) : $base_defaults;
    }

    public static function user_events($event_type, $spec, $from, $to) {
        $_events_ = self::get_event_type($spec);

        if( "top_collections_items" == $event_type ) {
            return self::getTopCollections();
        } else if("general_status_items" === $event_type) {
            return self::getItemsStatus($spec);
        } else {
            if($_events_) {
                $_stats = [];
                foreach ($_events_['events'] as $ev) {
                    $evt_count_ = self::getUserEvents($event_type, $ev, false, $from, $to);
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

    public function getItemsStatus($spec) {
        global $wpdb;
        $status['active'] = sprintf("SELECT COUNT(id) as total_active from wp_posts WHERE post_type='socialdb_object' AND post_status='publish'", self::_posts_table());
        $status['draft'] = sprintf("SELECT COUNT(id) as total_draft from wp_posts WHERE post_type='socialdb_object' AND post_status='draft'", self::_posts_table());
        $status['trash'] = sprintf("SELECT COUNT(id) as total_trash from wp_posts WHERE post_type='socialdb_object' AND post_status='trash'", self::_posts_table());
        $status['deleted'] = sprintf("SELECT count(id) as total_delete from wp_statistics WHERE event_type='user_items' AND event='delete'",self::_table());

        $_res = [];
        $_stat_obj = self::get_event_type($spec);
        foreach($status as $s) {
            $_obj = array_pop($wpdb->get_results($s));
            array_push( $_res, $_obj );
        }
        $stat_data = [ "stat_title" => [ 'Status dos Itens', '#' ], "stat_object" => $_res, "color" => $_stat_obj['color'], "item_status" => true ];
        return json_encode($stat_data);
    }

    public static function getUserStatus($event) {
        global $wpdb;
        $sql = sprintf("SELECT COUNT(id) as total_status FROM %s WHERE event_type = 'user_collection' AND event = '$event'", self::_table() );
        return json_encode( $wpdb->get_results($sql) );
    }
}