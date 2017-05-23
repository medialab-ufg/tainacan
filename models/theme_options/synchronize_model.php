<?php

ini_set('max_input_vars', '10000');
include_once (dirname(__FILE__) . '/../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');

class SynchronizeModel extends Model { 
    public  $User;
    public $Pass;
    public $url;
    /**
     * 
     * @param type $data
     */
    public function start($data) {
        $this->User = $data['api_user'];
        $this->Pass = $data['api_key'];
        $this->url = $data['api_url'];
        $qry_str = "/posts?type=socialdb_collection&filter[status]=publish&user=".$this->User.'&password='.$this->Pass;;
        $ch = curl_init();
        // Set query data here with the URL
        curl_setopt($ch, CURLOPT_URL, $this->url. $qry_str);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, '3');
        $content = trim(curl_exec($ch));
        curl_close($ch);
        $content = json_decode($content, true);
        if (empty($content)) {
            return false;
        } else {
            foreach ($content as $post) {
                if(!isset($post['ID']))
                    continue;
                $qry_str = "/posts/" . $post['ID'] . "/meta?user=".$this->User.'&password='.$this->Pass;
                $ch = curl_init();
                // Set query data here with the URL
                curl_setopt($ch, CURLOPT_URL,$this->url. $qry_str);
                //curl_setopt($ch, CURLOPT_USERPWD, "$this->User:$this->Pass"); //Your credentials goes here
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, '3');
                $metas = trim(curl_exec($ch));
                $metas_collection = json_decode($metas, true);
            }
        }  
    }
    
    public function getPropertiesFromCollection($metas) {
        if(is_array($metas)){
            foreach ($metas as $meta) {
                if($meta['meta_key'] === 'socialdb_collection_object_type'){
                    
                }
            }
        }
    }
    
}
