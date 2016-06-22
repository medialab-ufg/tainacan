<?php

include_once (dirname(__FILE__) . '/../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../wp-includes/wp-db.php');
include_once (dirname(__FILE__) . '../../../models/collection/collection_model.php');
include_once (dirname(__FILE__) . '../../../models/property/property_model.php');
include_once (dirname(__FILE__) . '../../../models/category/category_model.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');

/**
 * The class HelpersModel centraliza metodos que utilizam os diferentes
 * models do tainacan para realizar uma tarefa especifica, como realizar
 * uma atualizacao em colecoes, ou ate mesmo funcoes de manipulacao de strings
 *
 */
class HelpersModel extends Model {
    
      /**
     * metodo que atualiza o metadado comum que centraliza os valores para 
     * facilitar uma busca completa do item e suas classificacoes EM TODAS
     * AS COLECOES
       * 
     * @param int $collection_id O id da colecao a ser atualizada
     */
     public static function update_all_collections(){
        session_write_close();
         $model = new HelpersModel;
        ini_set('max_execution_time', '0');
         $collections = $model->get_all_collections();
         if($collections  &&  is_array($collections)){
             foreach ($collections as $collection) {
                 self::update_commom_field_collection($collection->ID);
             }
         }
     }

     /**
     * metodo que atualiza o metadado comum que centraliza os valores para 
     * facilitar uma busca completa do item e suas classificacoes
     * 
     * @param int $collection_id O id da colecao a ser atualizada
     */
    public static function update_commom_field_collection($collection_id) {
        session_write_close();
        ini_set('max_execution_time', '0');
        $model = new HelpersModel;
        $items = $model->get_collection_posts($collection_id);
        if(is_array($items)){
            foreach ($items as $item) {
                $indexes = get_post_meta($item->ID, 'socialdb_object_commom_index', true);
                $values = get_post_meta($item->ID, 'socialdb_object_commom_values');
                if ($indexes && $values) {
                    continue;
                } else {
                    $model->add_item_values($item, $collection_id);
                }
            }
        }
    }

    /**
     * metodo que insere os valores de cada item 
     * @param WP_POST $item O post do item
     * @param int $collection_id O id da colecao a qual pertence o item
     */
    private function add_item_values($item, $collection_id) {
        $propertyModel = new PropertyModel;
        $category_root_id = $this->get_category_root_of($collection_id);
        $all_properties_id = array_unique($propertyModel->get_parent_properties($category_root_id, [], $category_root_id));
        $this->set_common_field_values($item->ID, 'title', $item->post_title);
        $this->set_common_field_values($item->ID, 'description', $item->post_content);
        $this->set_common_field_values($item->ID, 'object_from', get_post_meta($item->ID, 'socialdb_object_from', true));
        $this->set_common_field_values($item->ID, 'object_source', get_post_meta($item->ID, 'socialdb_object_dc_source', true));
        $this->set_common_field_values($item->ID, 'object_type', get_post_meta($item->ID, 'socialdb_object_dc_type', true));
        $this->set_common_field_values($item->ID, 'object_content', get_post_meta($item->ID, 'socialdb_object_content', true));
        if (is_array($all_properties_id)) {
            $this->add_item_properties($item->ID, $all_properties_id);
        }
        // buscando as categorias do item
        $categories = wp_get_object_terms($item->ID, 'socialdb_category_type');
        if(is_array($categories)){
            foreach ($categories as $category):
                $category_id = $category->term_id;
                $property_id = $this->get_category_property($category_id, $collection_id);
                $this->concatenate_commom_field_value($item->ID, "socialdb_propertyterm_$property_id",$category_id);
            endforeach;
        }
//        buscando as tags do item
        $tags = wp_get_object_terms($item->ID, 'socialdb_tag_type');
        if(is_array($tags)){
            foreach ($tags as $tag):
                $tag_id = $tag->term_id;
                $this->concatenate_commom_field_value($item->ID, "socialdb_propertyterm_tag",$tag_id);
            endforeach;
        }
    }

    /**
     * 
     * @param int $item_id
     * @param array $properties
     */
    private function add_item_properties($item_id, $properties) {
        $propertyModel = new PropertyModel;
        foreach ($properties as $property_id) {
            $type = $propertyModel->get_property_type($property_id);
            if ($type == 'socialdb_property_data') {// verifico o tipo
                $value = get_post_meta($item_id,"socialdb_property_$property_id",true);
                if($value)
                        $this->set_common_field_values($item_id,  "socialdb_property_$property_id",$value);
            }elseif($type='socialdb_property_object'){
               $values = get_post_meta($item_id,"socialdb_property_$property_id");
               if($values)
                        $this->set_common_field_values($item_id,  "socialdb_property_$property_id",$values,'item');
            }
        }
    }

}
