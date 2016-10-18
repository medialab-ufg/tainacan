<?php
require_once(dirname(__FILE__) . '../../general/general_model.php');

class Log extends Model {
    private $_log_version = 1.0;
    const _TABLE_SUFFIX_ = "statistics";

    public static function _table() {
        return $GLOBALS['wpdb']->prefix . self::_TABLE_SUFFIX_;
    }

    public static function add_log($logData) {
        global $wpdb;
        return $wpdb->insert( self::_table(), $logData);
    }
}