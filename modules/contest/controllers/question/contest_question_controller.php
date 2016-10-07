<?php
$_GET['is_module_active'] = TRUE;
require_once(dirname(__FILE__).'../../../models/item/item_model.php');
include_once(dirname(__FILE__).'/../../../../controllers/general/general_controller.php');  
 class ContestQuestionController extends Controller{
	 public function operation($operation,$data){
                $model = new ItemModel;   
		switch ($operation) {
                     //adicionar um novo argumento 
                    case 'add':
                        $callback = json_decode($model->add($data['question'], $data['collection_id'], $data['classification'], 'question'));
                        if(isset($callback->socialdb_event_object_item_id)&&isset($callback->type)&&$callback->type=='success'){
                            if(trim($data['answer'])!==''){
                                $model->add($data['answer'], $data['collection_id'], $data['classification'], 'question',$callback->socialdb_event_object_item_id,'positive');
                            }
                            $item = get_post($callback->socialdb_event_object_item_id);
                            wp_redirect(get_the_permalink($data['collection_id']).'?item='.$item->post_name);
                        }
                    case 'add_answer':
                        $callback = json_decode($model->add($data['answer_argument'], $data['collection_id'], '', 'question',$data['root_argument'],'positive'));
                        if(isset($callback->socialdb_event_object_item_id)&&isset($callback->type)&&$callback->type=='success'){
                            $item = get_post($data['root_argument']);
                            $data['redirect'] = get_the_permalink($data['collection_id']).'?item='.$item->post_name;
                            return json_encode($data);
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

$controller = new ContestQuestionController();
echo $controller->operation($operation,$data);
