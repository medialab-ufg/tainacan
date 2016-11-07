<?php

require_once(dirname(__FILE__) . '../../general/general_model.php');
/**
 * 
 * o metodo <i>has_cache</i> esta disponibilizado no general_controller para acesso 
 * dos demais controladores
 * 
 */
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
        if(!is_file(TAINACAN_UPLOAD_FOLDER.'/cache/'.$collection->post_name.'/'.$operation.'.html')){
            if(!is_dir(TAINACAN_UPLOAD_FOLDER.'/cache/'.$collection->post_name))
                 mkdir(TAINACAN_UPLOAD_FOLDER.'/cache/'.$collection->post_name);
            ob_clean();
            $df = fopen(TAINACAN_UPLOAD_FOLDER.'/cache/'.$collection->post_name.'/'.$operation.'.html', 'w');
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
        if(is_file(TAINACAN_UPLOAD_FOLDER.'/cache/'.$collection->post_name.'/'.$operation.'.html')){
            unlink(TAINACAN_UPLOAD_FOLDER.'/cache/'.$collection->post_name.'/'.$operation.'.html');
        }
    }
}
