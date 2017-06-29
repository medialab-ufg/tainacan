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
date_default_timezone_set('America/Sao_Paulo');

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
                $data['value'] = trim($data['value']);
                $class = new ObjectSaveValuesModel();
                // action para salvar dados extras
                if(has_action('action_save_item')){
                    do_action('action_save_item', $data);
                }
                //SE FOR METADADO CHAVE FACO VALIDACAO
                if(isset($data['isKey']) && $data['isKey'] === 'true'){
                    $json =$object_model->get_data_by_property_json(
                        [
                        'property_id'=>($data['property_children_id']==='0') ? $data['compound_id'] : $data['property_children_id'],
                        'term'=>$data['value']
                        ]);
                    $json_decode = json_decode($json);
                    if($json_decode && is_array($json_decode) && count($json_decode) > 0){
                        foreach ($json_decode as $value) {
                            if($value->value === $data['value'] && $value->item_id != $data['item_id'] )
                                 return json_encode($data);
                        }
                    }
                }
                //SE EXISITIR UM METADADOREVERSO ELE INSERE O VALOR NO REVERSO
                if(isset($data['reverse']) && $data['reverse'] !== 'true'){
                    $meta = unserialize(get_term_meta($data['reverse'], 'socialdb_property_is_compounds', true));
                    if (!$meta || !is_array($meta)):
                        $compound_id = $data['reverse'];
                        $property_children_id = 0;
                    else:
                        foreach ($meta as $key => $value) {
                           if($value === 'true'){
                                $compound_id = $key;
                                $property_children_id = $data['reverse'];
                           }
                        }
                    endif;

                    $class->saveValue($data['value'],
                        $compound_id,
                        $property_children_id,
                        $data['type'],
                        $data['index'],
                        $data['item_id'],
                        (isset($data['indexCoumpound']) ? $data['indexCoumpound'] : false )
                        );
                }
                //INSERE O VALOR DE FATO
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
                if(isset($data['reverse']) && $data['reverse'] !== 'true'){
                    $meta = unserialize(get_term_meta($data['reverse'], 'socialdb_property_is_compounds', true));
                    if (!$meta || !is_array($meta)):
                        $compound_id = $data['reverse'];
                        $property_children_id = 0;
                    else:
                        foreach ($variable as $key => $value) {
                           if($value === 'true'){
                                $compound_id = $key;
                                $property_children_id = $data['reverse'];
                           }
                        }
                    endif;

                    $class->removeValue($data['value'],
                        $compound_id,
                        $property_children_id,
                        $data['type'],
                        $data['index'],
                        $data['item_id']
                        );
                }
                return $class->removeValue($data['item_id'], $data['compound_id'], $data['property_children_id'], $data['type'], $data['index'], $data['value']);
            case "removeIndexValues":
                $class = new ObjectSaveValuesModel();
                return $class->removeIndexValue($data['item_id'], $data['compound_id'], $data['index']);
            case 'saveTitle':
                $data['value'] = trim($data['value']);
                //SE FOR METADADO CHAVE FACO VALIDACAO
                if(isset($data['hasKey']) && $data['hasKey'] === 'true'){
                    $json = json_decode($object_model->get_objects_by_property_json_advanced_search(
                        [
                        'collection_id'=> $data['collection_id'],
                        'term'=>$data['value']
                        ]));
                    if($json && is_array($json) && count($json) > 0){
                        foreach ($json as $value) {
                            if($value->label === $data['value'] && $value->ID != $data['item_id'] ){
                                return json_encode($data);
                            }
                        }
                    }
                }
                $slug = wp_unique_post_slug(sanitize_title_with_dashes($data['value']), $data['item_id'], 'inherit', 'socialdb_object', 0);
                $post = array(
                    'ID' => $data['item_id'],
                    'post_title' => ($data['value']) ? $data['value'] : __('Empty Title','tainacan'),
                    'post_name' => $slug
                );
                $data['ID'] = wp_update_post($post);
                break;
            case 'saveDescription':
                      $post = array(
                          'ID' => $data['item_id'],
                          'post_content' => $data['value']
                      );
                      $data['ID'] = wp_update_post($post);
                      break;
            case 'saveContent':
                    update_post_meta($data['item_id'],'socialdb_object_content',$data['value']);
                    break;
            case 'saveType':
                    update_post_meta($data['item_id'],'socialdb_object_dc_type',$data['value']);
                    break;
            case 'saveSource':
                  update_post_meta($data['item_id'],'socialdb_object_dc_source',$data['value']);
                  break;
            case 'saveLicense':
                 update_post_meta($data['item_id'],'socialdb_license_id',$data['value']);
                 break;
            case 'saveThumbnail':
                if ($_FILES) {
                    $attachment_id = $object_model->add_thumbnail($data['item_id']);
                    if (isset($_FILES['object_thumbnail']) && !empty($_FILES['object_thumbnail'])) {
                        set_post_thumbnail($data['item_id'], $attachment_id);
                    }
                }
                break;
            case 'saveTags':
                 $object_model->insert_tags($data['value'], $data['collection_id'], $data['item_id']);
                 break;
            case 'updateItem':
                $category_root_id = $object_model->get_category_root_of($data['collection_id']);
                $post = array(
                    'ID' => $data['item_id'],
                    'post_parent' => $data['collection_id']
                );
                $data['ID'] = wp_update_post($post);
                //Tainacan IBRAM
                if (has_action('tainacan_delete_related_item')) {
                    $values = ['object_id'=> $data['ID']];
                    do_action('tainacan_delete_related_item', $values, $data['collection_id']);
                 }
                //categoria raiz da colecao
                wp_set_object_terms($data['ID'], array((int) $category_root_id), 'socialdb_category_type',true);
                update_post_meta($data['ID'], 'socialdb_object_collection_init', $data['collection_id']);
                update_user_meta(get_current_user_id(), 'socialdb_collection_' . $data['collection_id'] . '_betatext', '');
                return $object_model->insert_object_event($data['ID'], $data);
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
