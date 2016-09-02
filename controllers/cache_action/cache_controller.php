<?php 
require_once(dirname(__FILE__) . '../../../models/cache_action/cache_model.php');
require_once(dirname(__FILE__).'../../general/general_controller.php'); 
class CacheController extends Controller{
    public function operation($operation,$data){
            $cache_model = new CacheModel();
            switch ($operation) {
                //pagina da categoria
                case 'save_cache':
                    $cache_model->save_cache($data['html'], $data['route'], $data['collection_id']);
                    break;
                case 'delete_cache':
                    $cache_model->delete_cache($data['route'], $data['collection_id']);
                    break;


            }
    }
}

/*
 * Controller execution
*/

 if($_POST['operation']){
	$operation = $_POST['operation'];
    $data = $_POST;
}else{
	$operation = $_GET['operation'];
	$data = $_GET;
}

$controller = new CacheController();
echo $controller->operation($operation,$data);