<?php
require_once(dirname(__FILE__) . '../../general/general_model.php');

class Log extends Model {
    private $_log_version = 1.0;
    static $primary_key = 'id';

    const _TABLE_SUFFIX_ = "statistics";

    public static function _table() {
        return $GLOBALS['wpdb']->prefix . self::_TABLE_SUFFIX_;
    }

    public static function add_log($logData) {
        global $wpdb;
        return $wpdb->insert( self::_table(), $logData);
    }

    public static function get_user_events() {
        global $wpdb;
        // $sql = sprintf("SELECT * FROM %s WHERE %s = %%s", self::_table(), static::$primary_key);
        // $sql = sprintf("SELECT * FROM %s WHERE user_event = 'user_login'", self::_table() );
        $sql = sprintf("SELECT COUNT(id) FROM %s WHERE user_event = 'user_login'", self::_table() );
        return $wpdb->get_results($sql);
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