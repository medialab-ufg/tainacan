<?php
$_GET['is_module_active'] = TRUE;
require_once(dirname(__FILE__).'../../../models/item/item_model.php');
include_once(dirname(__FILE__).'/../../../../controllers/general/general_controller.php');  
 class ContestArgumentController extends Controller{
	 public function operation($operation,$data){
                $model = new ItemModel;  
                var_dump($data);exit();
		switch ($operation) {
                    //adicionar um novo argumento 
                    case 'add':
                        $callback = json_decode($model->add($data['conclusion'], $data['collection_id'], $data['classification'], 'argument'));
                        if(isset($callback->socialdb_event_object_item_id)&&isset($callback->type)&&$callback->type=='success'){
                            if(trim($data['positive_argument'])!==''){
                                $model->add($data['positive_argument'], $data['collection_id'], $data['classification'], 'argument',$callback->socialdb_event_object_item_id,'positive');
                            }
                            
                            if(trim($data['negative_argument'])!==''){
                                $model->add($data['negative_argument'], $data['collection_id'], $data['classification'], 'argument',$callback->socialdb_event_object_item_id,'negative');
                            }
                            $item = get_post($callback->socialdb_event_object_item_id);
                            wp_redirect(get_the_permalink($data['collection_id']).'?item='.$item->post_name);
                        }
                        
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

$controller = new ContestArgumentController();
echo $controller->operation($operation,$data);
