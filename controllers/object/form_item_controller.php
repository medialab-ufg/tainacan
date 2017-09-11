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
            case "appendContainerTextMultiple":
                include_once dirname(__FILE__) . '../../../views/object/formItemMultiple/formItemMultiple.class.php';
                $class = new FormItemText($data['collection_id']);
                return $class->appendContainerText(unserialize(stripslashes(html_entity_decode($data['property_details']))),$data['item_id'],$data['index']);
            case "appendContainerCompoundsMultiple":
                include_once dirname(__FILE__) . '../../../views/object/formItemMultiple/formItemMultiple.class.php';
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
                    //se o inverso estiver em um metadado composto
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
                        ( false )
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
                        ],true," p.post_status in ('publish','inherit','draft')  AND "));
                    if($json && is_array($json) && count($json) > 0){
                        foreach ($json as $value) {
                            if(strtolower($value->label) === strtolower($data['value']) && $value->ID != $data['item_id'] ){
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
                $object_model->set_common_field_values($data['ID'], 'title', $post['post_title']);
                return json_encode([true]);
                break;
            case 'saveDescription':
                $post = array(
                    'ID' => $data['item_id'],
                    'post_content' => $data['value']
                );
                $object_model->set_common_field_values($data['item_id'], 'description', $post['post_content']);
                $data['ID'] = wp_update_post($post);
                break;
            case 'saveContent':
                    update_post_meta($data['item_id'],'socialdb_object_content',$data['value']);
                    $object_model->set_common_field_values($data['item_id'], 'object_content', $data['value']);
                    break;
            case 'saveType':
                    update_post_meta($data['item_id'],'socialdb_object_dc_type',$data['value']);
                    $object_model->set_common_field_values($data['item_id'], 'object_type', $data['value']);
                    break;
            case 'saveSource':
                update_post_meta($data['item_id'],'socialdb_object_dc_source',$data['value']);
                $object_model->set_common_field_values($data['item_id'], 'object_source', $data['value']);
                break;
            case 'saveLicense':
                 update_post_meta($data['item_id'],'socialdb_license_id',$data['value']);
                 break;
            case 'saveThumbnail':
                $result = [];
                if ($_FILES) {
                    $attachment_id = $object_model->add_thumbnail($data['item_id']);
                    $result['attachment'] = $attachment_id;
                    if (isset($_FILES['object_thumbnail']) && !empty($_FILES['object_thumbnail'])) {
                        //$result['set_post'] = set_post_thumbnail($data['item_id'], $attachment_id);
                        $result['file'] = $_FILES['object_thumbnail'];
                        $result['data'] = $data;
                    }
                }
                return json_encode($result);
                break;
            case 'removeThumbnail':
                delete_post_thumbnail( $data['item_id'] );
                return json_encode([]);
            case 'saveTags':
                 $object_model->insert_tags($data['value'], $data['collection_id'], $data['item_id']);
                 break;
            case 'updateItem':
                $item = get_post($data['item_id']);
                //Tainacan Biblioteca
                $mapping = get_option('socialdb_general_mapping_collection');
                if(has_filter("add_book_loan") && isset($mapping) && ($mapping['Emprestimo'] == $data['collection_id'] || $mapping['Devoluções'] == $data['collection_id']))    
                {
                    $result = apply_filters("add_book_loan", $data);

                    if(!$result['ok'])
                    {
                        $result['unavailable_item'] = true;

                        return json_encode($result);
                    }else $data['ok'] = true;
                }else $data['ok'] = true;

                $category_root_id = $object_model->get_category_root_of($data['collection_id']);
                
                if(strcmp($item->post_title, 'Temporary_post') == 0) {
                    $post = array(
                        'post_title' => time(),
                        'ID' => $data['item_id'],
                        'post_parent' => $data['collection_id']
                    );
                } else {
                    $post = array(
                        'ID' => $data['item_id'],
                        'post_parent' => $data['collection_id']
                    );
                }

                if($post['post_parent'] === 0)
                    unset($post['post_parent']);


                $data['ID'] = wp_update_post($post);
                //Tainacan IBRAM
                if (has_action('tainacan_delete_related_item')) {
                    $values = ['object_id'=> $data['ID'],'collection_id'=> $data['collection_id']];
                    do_action('tainacan_delete_related_item', $values, $data['collection_id']);
                 }

                
                //categoria raiz da colecao
                wp_set_object_terms($data['ID'], array((int) $category_root_id), 'socialdb_category_type',true);
                update_post_meta($data['ID'], 'socialdb_object_collection_init', $data['collection_id']);
                update_user_meta(get_current_user_id(), 'socialdb_collection_' . $data['collection_id'] . '_betatext', '');
                if($item->post_status === 'publish'):
                    $data['msg'] = __('The event was successful', 'tainacan');
                    $data['type'] = 'success';
                    $data['title'] = __('Success', 'tainacan');
                    return json_encode($data);
                else:    
                    return $object_model->insert_object_event($data['ID'], $data);
                endif;
            //Buscando valores 
            case 'getDescription':
                $data['value'] = get_post($data['item_id'])->post_content;
                return json_encode($data);
                break;
            case 'getSource':
                $data['value'] = get_post_meta($data['item_id'],'socialdb_object_dc_source',true);
                return json_encode($data);
                break;
            case 'getTags':
                $string = [];
                if(is_array($data['item_id'])){
                    foreach ($data['item_id'] as $item_id) {
                        $tags = wp_get_object_terms($item_id, 'socialdb_tag_type');
                        if($tags && is_array($tags)){
                            foreach ($tags as $tag) {
                                $string[] = $tag->name;
                            }
                        }
                    }
                }else{
                    $tags = wp_get_object_terms($data['item_id'], 'socialdb_tag_type');
                    if($tags && is_array($tags)){
                        foreach ($tags as $tag) {
                            $string[] = $tag->name;
                        }
                    }
                }
                $data['value'] = ($string) ? implode(',', $string) : '';
                return json_encode($data);
                break;
            case 'getDataValue':
                $class = new ObjectSaveValuesModel();
                $result = $class->getValuePropertyHelper($data['item_id'], $data['compound_id']);
                if($result && isset($result[$data['index']][$data['property_children_id']])){
                     $data['value'] = $class->getValues($result[$data['index']][$data['property_children_id']]);
                     $this->array_set_pointer($result, $data['index']);
                     $next = next($result);
                     $data['nextIndex'] =  ($next) ? key($result) : false;
                }
                return json_encode($data);
            case 'getObjectValue':
                $values = [];
                $class = new ObjectSaveValuesModel();
                $result = $class->getValuePropertyHelper($data['item_id'], $data['compound_id']);
                if($result && isset($result[$data['index']][$data['property_children_id']])){
                    $ids = $class->getValues($result[$data['index']][$data['property_children_id']]);
                    if($ids && is_array($ids)){
                        foreach ($ids as $id) {
                            $values[$id] = get_post($id)->post_title;
                        }
                    }
                    $data['value'] = $values;
                }
                return json_encode($data);   
            case 'publishItems':
                delete_user_meta(get_current_user_id(), 'socialdb_collection_' . $data['collection_id'] . '_betafile');
                $class = new ObjectSaveValuesModel();
                if(is_array($data['items']))
                {
                    foreach ($data['items'] as $item) {
                        $post = array(
                        'ID' => $item,
                        'post_parent' => $data['collection_id'],
                        'post_status' => 'publish');
                        $data['ID'][] = wp_update_post($post);
                        $category_root_id = $class->get_category_root_of($data['collection_id']);
                        //categoria raiz da colecao
                        wp_set_object_terms($item, array((int) $category_root_id), 'socialdb_category_type',true);
                    }

                    $data['there_are_pdfFiles'] = get_documents_text($data['items']);
                }

                return json_encode($data);  
            case 'unpublish_item':
                $post = array(
                        'ID' => $data['id'],
                        'post_status' => 'draft');
                wp_update_post($post);
                return json_encode(true);          
        }
    }
    
    public function array_set_pointer(&$array, $value){
        reset($array);
        while($val=current($array))
        {
            if(key($val)==$value) 
                break;

            next($array);
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
if(isset($data['item_id']) && !is_array($data['item_id']) && strpos($data['item_id'], ',') !== false ){
    $ids = explode(',', $data['item_id']);
    foreach ($ids as $id) {
        $data['item_id'] = $id;
        $form_item_controller->operation($operation, $data);
    }
}else if($operation == 'saveValue' && is_array( $data['value'])){
    $ids = $data['value'];
    foreach ($ids as $id) {
        $data['value'] = $id;
        $form_item_controller->operation($operation, $data);
    }
    echo '[]';
}else{
    echo $form_item_controller->operation($operation, $data);
}
