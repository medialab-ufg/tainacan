<?php
$_GET['is_module_active'] = TRUE;
require_once(dirname(__FILE__).'../../../models/item/item_model.php');
include_once(dirname(__FILE__).'/../../../../controllers/general/general_controller.php');  
require_once(dirname(__FILE__) . '/../../../../models/object/object_model.php');
 class ContestQuestionController extends Controller{
	 public function operation($operation,$data){
                $model = new ItemModel;   
                $object_model = new ObjectModel;
		switch ($operation) {
                     //adicionar um novo argumento 
                    case 'add':
                        $callback = json_decode($model->add($data['question'], $data['collection_id'], $data['classification'], 'question'));
                        if(isset($callback->socialdb_event_object_item_id)&&isset($callback->type)&&$callback->type=='success'){
                            if(trim($data['answer'])!==''){
                                $model->add($data['answer'], $data['collection_id'], $data['classification'], 'argument',$callback->socialdb_event_object_item_id,'positive');
                            }
                            $ranking_id = get_post_meta($data['collection_id'], 'socialdb_collection_ranking_default_id', true);
                            add_post_meta($callback->socialdb_event_object_item_id, 'socialdb_property_'.$ranking_id, 0);
                            $item = get_post($callback->socialdb_event_object_item_id);
                            wp_redirect(get_the_permalink($data['collection_id']).'?item='.$item->post_name);
                        }
                    case 'add_answer':
                        $callback = json_decode($model->add($data['answer_argument'], $data['collection_id'], '', 'argument',$data['root_argument'],'positive'));
                        if(isset($callback->socialdb_event_object_item_id)&&isset($callback->type)&&$callback->type=='success'){
                            $item = get_post($data['root_argument']);
                             //inserindo os valores das propriedades
                            $object_model->insert_properties_values($data, $callback->socialdb_event_object_item_id);
                            // propriedade de termos
                            $object_model->insert_properties_terms($data, $callback->socialdb_event_object_item_id);
                            //propriedades compostas
                            $object_model->insert_compounds($data,$callback->socialdb_event_object_item_id);
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
