<?php

require_once(dirname(__FILE__) . '../../general/general_model.php');

class CacheModel extends Model {

    /**
     * 
     * @param type $html
     * @param type $operation
     * @param type $collection_id
     */
    public function save_cache($html,$operation,$collection_id) {
        error_reporting(E_ALL);
        $collection = get_post($collection_id);
        if(!is_file(dirname(__FILE__).'../../../cache/'.$collection->post_name.'/'.$operation.'.html')){
            if(!is_dir(dirname(__FILE__).'../../../cache/'.$collection->post_name))
                 mkdir(dirname(__FILE__).'../../../cache/'.$collection->post_name);
            ob_clean();
            $df = fopen(dirname(__FILE__).'../../../cache/'.$collection->post_name.'/'.$operation.'.html', 'w');
            var_dump($html);
            fwrite($df, $html);
            fclose($df);
        }
        
    }
    /**
     * 
     * @param type $operation
     * @param type $collection_id
     */
    public function delete_cache($operation,$collection_id) {
        $collection = get_post($collection_id);
        if(is_file(dirname(__FILE__).'../../../cache/'.$collection->post_name.'/'.$operation.'.html')){
            unlink(dirname(__FILE__).'../../../cache/'.$collection->post_name.'/'.$operation.'.html');
        }
    }
}
