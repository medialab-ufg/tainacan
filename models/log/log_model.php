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

    public static function getUserEvents($event) {
        global $wpdb;
        $sql = sprintf("SELECT COUNT(id) as total_login FROM %s WHERE event_type = 'user' AND event = '$event'", self::_table() );
        return json_encode( $wpdb->get_results($sql) );
    }

    public static function getStatEvents() {
        
    }

    /*
    public static function _fetch_sql($value) {
        global $wpdb;
        $sql = sprintf("SELECT * FROM %s WHERE %s = %%s", self::_table(), static::$primary_key);
        return $wpdb->prepare($sql, $value);
    }

    public static function get( $value ) {
        global $wpdb;
        return $wpdb->get_row( self::_fetch_sql( $value ) );
    }
    */
}