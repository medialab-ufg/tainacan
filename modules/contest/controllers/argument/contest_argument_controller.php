<?php
$_GET['is_module_active'] = TRUE;
require_once(dirname(__FILE__).'../../../models/item/item_model.php');
include_once(dirname(__FILE__).'/../../../../controllers/general/general_controller.php');   
require_once(dirname(__FILE__) . '/../../../../models/object/object_model.php');
 class ContestArgumentController extends Controller{
	 public function operation($operation,$data){
                $model = new ItemModel;  
                $object_model = new ObjectModel;
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
                    case 'add_reply_positive':
                        $callback = json_decode($model->add($data['positive_argument'], $data['collection_id'], '', 'argument',$data['argument_parent'],'positive'));
                        if(isset($callback->socialdb_event_object_item_id)&&isset($callback->type)&&$callback->type=='success'){
                            $item = get_post($data['root_argument']);
                            //inserindo os valores das propriedades
                            $object_model->insert_properties_values($data, $callback->socialdb_event_object_item_id);
                            // propriedade de termos
                            $object_model->insert_properties_terms($data, $callback->socialdb_event_object_item_id);
                            //propriedades compostas
                            $object_model->insert_compounds($data,$callback->socialdb_event_object_item_id);
                            $data['item_id'] = $callback->socialdb_event_object_item_id;
                            $data['redirect'] = get_the_permalink($data['collection_id']).'?item='.$item->post_name;
                            return json_encode($data);
                        }
                    case 'add_reply_negative':
                        $callback = json_decode($model->add($data['negative_argument'], $data['collection_id'], '', 'argument',$data['argument_parent'],'negative'));
                        if(isset($callback->socialdb_event_object_item_id)&&isset($callback->type)&&$callback->type=='success'){
                            $item = get_post($data['root_argument']);
                            //inserindo os valores das propriedades
                            $object_model->insert_properties_values($data, $callback->socialdb_event_object_item_id);
                            // propriedade de termos
                            $object_model->insert_properties_terms($data, $callback->socialdb_event_object_item_id);
                            //propriedades compostas
                            $object_model->insert_compounds($data,$callback->socialdb_event_object_item_id);
                            $data['item_id'] = $callback->socialdb_event_object_item_id;
                            $data['redirect'] = get_the_permalink($data['collection_id']).'?item='.$item->post_name;
                            return json_encode($data);
                        }
                    case 'edit_comment_contest':
                        $data['comment'] = get_post($data['object_id']);
                        $data['type'] = get_post_meta($data['object_id'], 'socialdb_object_contest_position', true);
                        return json_encode($data);
                    case 'update_argument':  
                        //inserindo os valores das propriedades
                        $object_model->insert_properties_values($data, $data['argument_id']);
                        // propriedade de termos
                        $object_model->insert_properties_terms($data, $data['argument_id']);
                        //propriedades compostas
                        $object_model->insert_compounds($data,$data['argument_id']);
                        return json_encode($model->update_argument($data));
                        
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
