<?php
$_GET['is_module_active'] = TRUE;
include_once(dirname(__FILE__) . '/../../../../models/category/category_model.php');
include_once(dirname(__FILE__) . '/../../../../models/property/property_model.php');
require_once(dirname(__FILE__).'/../../../../models/event/event_property_data/event_property_data_create_model.php');
require_once(dirname(__FILE__).'/../../../../models/event/event_property_data/event_property_data_edit_model.php');
require_once(dirname(__FILE__).'/../../../../models/event/event_property_data/event_property_data_delete_model.php');
require_once(dirname(__FILE__).'/../../../../models/event/event_property_object/event_property_object_create_model.php');
require_once(dirname(__FILE__).'/../../../../models/event/event_property_object/event_property_object_edit_model.php');
require_once(dirname(__FILE__).'/../../../../models/event/event_property_object/event_property_object_delete_model.php');
require_once(dirname(__FILE__).'/../../../../models/event/event_property_term/event_property_term_create_model.php');
require_once(dirname(__FILE__).'/../../../../models/event/event_property_term/event_property_term_edit_model.php');
require_once(dirname(__FILE__).'/../../../../models/event/event_property_term/event_property_term_delete_model.php');
include_once(dirname(__FILE__).'/../../../../controllers/general/general_controller.php');  
 class OntologyPropertyController extends Controller{
	 public function operation($operation,$data){
                $model = new PropertyModel;   
		switch ($operation) {
                    case 'create':
                        $data['property_parent'] = get_term_by('id', $data['property_parent_id'], 'socialdb_property_type');
                        return $this->render(dirname(__FILE__).'/../../views/property/create.php', $data);
                    case 'edit':
                        $array = $model->get_all_property($data['property_id'], true);
                        $data['data'] = $array;
                        $data['property'] = get_term_by('id', $data['property_id'],'socialdb_property_type');
                        $data['category'] = get_term_by('id', $array['metas']['socialdb_property_created_category'],'socialdb_category_type');   
                        $data['type'] = $model->get_property_type_hierachy($data['property_id']);
                        return $this->render(dirname(__FILE__).'/../../views/property/list.php', $data);
                    case 'delete':
                        $type =  $model->get_property_type_hierachy($data['property_id']);
                        $array = $model->get_all_property($data['property_id'], true);
                         if($type=='socialdb_property_data'):
                            $eventAddProperty = new EventPropertyDataDelete();
                            $data['socialdb_event_property_data_delete_id'] = $data['property_id'];
                            $data['socialdb_event_collection_id'] = $data['collection_id'];
                            $data['socialdb_event_property_data_delete_category_root_id'] = $array['metas']['socialdb_property_created_category'];
                            $data['socialdb_event_user_id'] = get_current_user_id();
                            $data['socialdb_event_create_date'] = mktime();
                            return $eventAddProperty->create_event($data);
                        elseif($type=='socialdb_property_object'):
                            $eventAddProperty = new EventPropertyObjectDelete();
                            $data['socialdb_event_property_object_delete_id'] = $data['property_id'];
                            $data['socialdb_event_property_object_delete_category_root_id'] =$array['metas']['socialdb_property_created_category'];
                            $data['socialdb_event_collection_id'] = $data['collection_id'];
                            $data['socialdb_event_user_id'] = get_current_user_id();
                            $data['socialdb_event_create_date'] = mktime();
                            return $eventAddProperty->create_event($data);
                        elseif($type=='socialdb_property_term'):
                            $eventAddProperty = new EventPropertyTermDelete();
                            $data['socialdb_event_property_term_delete_id'] = $data['property_id'];
                            $data['socialdb_event_collection_id'] = $data['collection_id'];
                            $data['socialdb_event_property_term_delete_category_root_id'] = $array['metas']['socialdb_property_created_category'];
                            $data['socialdb_event_user_id'] = get_current_user_id();
                            $data['socialdb_event_create_date'] = mktime();
                            return $eventAddProperty->create_event($data);
                        endif;
                        
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

$controller = new OntologyPropertyController();
echo $controller->operation($operation,$data);
