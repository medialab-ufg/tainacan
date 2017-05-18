<?php

ini_set('max_input_vars', '10000');
include_once (dirname(__FILE__) . '/../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');

class SynchronizeModel extends Model { 
    /**
     * 
     * @param type $data
     */
    public function start($data) {
        $qry_str = "/posts?type=socialdb_collection&filter[status]=publish";
        $ch = curl_init();
        // Set query data here with the URL
        curl_setopt($ch, CURLOPT_URL, $data['api_url']. $qry_str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, '3');
        $content = trim(curl_exec($ch));
        curl_close($ch);
        $content = json_decode($content, true);
        if (empty($content)) {
            return false;
        } else {
            foreach ($content as $post) {
                var_dump(count($content),$post['status']);
            }
        }    
        
    }
}
