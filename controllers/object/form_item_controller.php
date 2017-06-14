<?php

/**
 * #1 - ADICIONAR ITEMS TIPO TEXTO
 * #2 - ADICIONAR ITEMS DEFAULT
 */
require_once(dirname(__FILE__) . '../../../models/object/object_model.php');
require_once(dirname(__FILE__) . '../../../models/collection/collection_model.php');
require_once(dirname(__FILE__) . '../../../controllers/general/general_controller.php');
require_once(dirname(__FILE__) . '../../../models/user/user_model.php');
require_once(dirname(__FILE__) . '../../../models/object/object_save_values.php');

class FormItemController extends Controller {

    public function operation($operation, $data) {
        $object_model = new ObjectModel();
        switch ($operation) {
            case "appendContainerText":
                include_once dirname(__FILE__) . '../../../views/object/formItem/helper/formItem.class.php';
                $class = new FormItemText($data['collection_id']);
                return $class->appendContainerText(unserialize(stripslashes(html_entity_decode($data['property_details']))),$data['item_id'],$data['index']);  
             case "appendContainerCompounds":
                include_once dirname(__FILE__) . '../../../views/object/formItem/helper/formItem.class.php';
                $class = new FormItemCompound($data['collection_id']);
                return $class->appendContainerCompounds(unserialize(stripslashes(html_entity_decode($data['property_details']))),$data['item_id'],$data['index']);      
            case "saveValue":
                $class = new ObjectSaveValuesModel();
                return $class->saveValue($data['item_id'], 
                        $data['compound_id'], 
                        $data['property_children_id'], 
                        $data['type'], 
                        $data['index'], 
                        $data['value'],
                        (isset($data['indexCoumpound']) ? $data['indexCoumpound'] : false )
                        );
            case "removeValue":
                $class = new ObjectSaveValuesModel();
                return $class->removeValue($data['item_id'], $data['compound_id'], $data['property_children_id'], $data['type'], $data['index'], $data['value']);
            case "removeIndexValues":
                $class = new ObjectSaveValuesModel();
                return $class->removeIndexValue($data['item_id'], $data['compound_id'], $data['index']);    
            case 'updateItem':
                $category_root_id = $object_model->get_category_root_of($data['collection_id']);
                $post = array(
                    'ID' => $data['item_id'],
                    'post_status' => 'publish',
                    'post_parent' => $data['collection_id']
                );
                $data['ID'] = wp_update_post($post);
                //categoria raiz da colecao
                wp_set_object_terms($data['ID'], array((int) $category_root_id), 'socialdb_category_type',true);
                update_post_meta($data['ID'], 'socialdb_object_collection_init', $data['collection_id']);
                return $object_model->insert_object_event($data['ID'], $data);;
        }
    }
	
}

/*
 * Controller execution
 */
if ($_POST['operation']) {
    $operation = $_POST['operation'];
    $data = $_POST;
} else {
    $operation = $_GET['operation'];
    $data = $_GET;
}

$form_item_controller = new FormItemController();
echo $form_item_controller->operation($operation, $data);
?>
