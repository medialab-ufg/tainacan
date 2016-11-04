<?php

include_once (dirname(__FILE__) . '/../../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../../wp-includes/wp-db.php');
include_once (dirname(__FILE__) . '/../../../../models/collection/collection_model.php');
include_once (dirname(__FILE__) . '/../../../../models/license/license_model.php');
include_once (dirname(__FILE__) . '/../../../../models/property/property_model.php');
include_once (dirname(__FILE__) . '/../../../../models/category/category_model.php');
include_once (dirname(__FILE__) . '/../../../../models/event/event_object/event_object_create_model.php');
require_once(dirname(__FILE__) . '/../../../../models/general/general_model.php');
require_once(dirname(__FILE__) . '/../../../../models/user/user_model.php');
require_once(dirname(__FILE__) . '/../../../../models/tag/tag_model.php');

/**
 * The class ObjectModel
 *
 */
class ItemModel extends Model {

    /**
     * 
     * @param type $title
     * @param type $collection_id
     * @param type $classification
     * @param type $parent
     * @param type $position
     * @return type
     */
    public function add($title,$collection_id,$classification,$type,$parent = 0,$position = false) {
        $category_root_id = $this->get_category_root_of($collection_id);
        $user_id = get_current_user_id();
        $post = array(
            'post_title' => $title,
            'post_status' => 'inherit',
            'post_author' => $user_id,
            'post_parent' => $parent,
            'post_type' => 'socialdb_object'
        );
        $post_id = wp_insert_post($post);
        //categoria raiz da colecao
        if($parent===0){
             wp_set_object_terms($post_id, array((int) $category_root_id), 'socialdb_category_type');
        }
        //inserindo as classificacoes
        $this->insert_classifications($classification, $post_id);
        //postmetas
        update_post_meta($post_id, 'socialdb_object_contest_type', $type);
        if($position){
           update_post_meta($post_id, 'socialdb_object_contest_position', $position);  
        }
        // inserindo o evento
        $data = $this->insert_item_event($post_id, $collection_id);

        return $data;
    }
    
    /**
     * @signature - function insert_event($object_id, $data )
     * @param int $object_id O id do Objeto
     * @param int $collection_id
     * @return array os dados para o evento
     * @description - 
     * @author: Eduardo 
     */
    public function insert_item_event($object_id, $collection_id) {
        $data = [];
        $eventAddObject = new EventObjectCreateModel();
        $data['socialdb_event_object_item_id'] = $object_id;
        $data['socialdb_event_collection_id'] = $collection_id;
        $data['socialdb_event_user_id'] = get_current_user_id();
        $data['socialdb_event_create_date'] = time();
        return $eventAddObject->create_event($data);
    }
    
    /**
     * @signature - function insert_classifications($classification_string, $object_id)
     * @param string $classification_string A string que esta concatenada com os valores do dynatree selecionadas
     * @param int $object_id O id do Objeto
     * @return void
     * @description - Insere os valores selecionados no dynatree no objeto criado
     * @author: Eduardo 
     */
    public function insert_classifications($classification_string, $object_id) {
        $classification_array = explode(',', $classification_string);
        foreach ($classification_array as $classification) {
            if (strpos($classification, '_') !== false) {
                $value_array = explode('_', $classification);
                if ($value_array[1] == 'tag') {
                    wp_set_object_terms($object_id, array((int) $value_array[0]), 'socialdb_tag_type', true);
                } else {
                    $metas = get_post_meta($object_id, 'socialdb_property_' . $value_array[1]);
                    if (!$metas || (count($metas) == 1 && $metas[0] == '')) {
                        update_post_meta($object_id, 'socialdb_property_' . $value_array[1], $value_array[0]);
                    } else {
                        add_post_meta($object_id, 'socialdb_property_' . $value_array[1], $value_array[0]);
                    }
                }
            } else {
                wp_set_object_terms($object_id, array((int) $classification), 'socialdb_category_type', true);
            }
        }
    }
    
    /**
     * @signature - function insert_tags($string_tags, $collection_id, $object_id)
     * @param string $string_tags A string que veio do formulario com todas as tags
     * @param int $collection_id O id da colecao
     * @param int $object_id O id do Objeto
     * @return void
     * @description - Insere os valores das tags colocadas pelo usuario
     * @author: Eduardo 
     */
    public function insert_tags($string_tags, $collection_id, $object_id) {
        $tagModel = new TagModel();
        $this->concatenate_commom_field_value( $object_id, "socialdb_propertyterm_tag", '');
        $tags = explode(',', $string_tags);
        if (is_array($tags) && !empty($tags)) {
            foreach ($tags as $tag) {
                if ($tag !== ''):
                    $tag_array = $tagModel->add($tag, $collection_id);
                    $tagModel->add_tag_object($tag_array['term_id'], $object_id);
                    $this->concatenate_commom_field_value( $object_id, "socialdb_propertyterm_tag", $tag_array['term_id']);
                endif;
            }
        }
    }
    
    /**
     * 
     * @param array $data
     * @return type
     */
    public function update_argument($data) {
          $post = array(
            'ID' => $data['argument_id'],
            'post_title' => $data['argument'],
            'post_type' => 'socialdb_object'
        );
        $data['ID'] = wp_update_post($post);
        update_post_meta($data['ID'], 'socialdb_object_contest_position', $data['argument-type']);  
        return $data;
    }

}
