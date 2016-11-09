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
        $_alias = "total_" . $event;
        $sql = sprintf("SELECT COUNT(id) as '$_alias' FROM %s WHERE event_type = '$event_type' AND event = '$event'", self::_table() );
        
        if( $encoded ) {
            return json_encode( $wpdb->get_results($sql) );
        } else {
            return $wpdb->get_results($sql, ARRAY_N);
        }
    }

    private function get_event_type($spec) {
        switch($spec) {
            case 'collection':
                return 'view';
        }
    }

    public static function user_events($event_type, $spec) {
        $_evt_type = self::get_event_type($spec);

        $_collection_events = ['add', 'view', 'edit', 'delete'];
        $_stats = [];
        foreach ($_collection_events as $ev) {
            $evt_count_ = self::getUserEvents($event_type, $ev, false);
            $l_data = array_pop($evt_count_);
            $_stats[] = $l_data[0];
        }

        $c = array_combine( $_collection_events, $_stats);
        $stat_data = [ "stat_title" => [ 'Coleções do Usuário', 'qtd' ], "stat_object" => $c ];

        return json_encode($stat_data);
    }

    public static function getUserStatus($event) {
        global $wpdb;
        $sql = sprintf("SELECT COUNT(id) as total_status FROM %s WHERE event_type = 'user_collection' AND event = '$event'", self::_table() );
        return json_encode( $wpdb->get_results($sql) );
    }

}