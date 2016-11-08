<?php
require_once(dirname(__FILE__) . '../../general/general_model.php');

class Log extends Model {
    private $_log_version = 1.0;
    static $primary_key = 'id';

    const _TABLE_SUFFIX_ = "statistics";

    public static function _table() {
        return $GLOBALS['wpdb']->prefix . self::_TABLE_SUFFIX_;
    }

    public static function addLog($logData) {
        global $wpdb;
        $final_data = array_merge($logData, self::getCommonFields() );
        return $wpdb->insert( self::_table(), $final_data);
    }
    
    private function getCommonFields() {
        return ['ip' => $_SERVER['REMOTE_ADDR'], 'event_date' => date('Y-m-d H:i:s')];
    }

    public static function getUserEvents($event_type, $event, $encoded = true) {
        global $wpdb;
        $sql = sprintf("SELECT COUNT(id) as total_login FROM %s WHERE event_type = '$event_type' AND event = '$event'", self::_table() );
        
        if( $encoded ) {
            return json_encode( $wpdb->get_results($sql) );
        } else {
            return $wpdb->get_results($sql);
        }
    }

    public static function user_events($event_type) {
        $log = [
            "stat_title" => [ 'Status de Usuários', 'qtd', ],
            "stat_object" => [
                "user_status" => self::getUserEvents($event_type, 'login', false),
                "user_register" => self::getUserEvents($event_type, 'register', false),
                "user_delete" => self::getUserEvents($event_type, 'delete_user', false),
            ]
        ];

        return json_encode( $log );
    }

    public static function getUserStatus($event) {
        global $wpdb;
        $sql = sprintf("SELECT COUNT(id) as total_status FROM %s WHERE event_type = 'user_collection' AND event = '$event'", self::_table() );
        return json_encode( $wpdb->get_results($sql) );
    }

}