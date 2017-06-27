<?php
/*
include_once (dirname(__FILE__) . '/../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-includes/wp-db.php');
*/
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
    
    public static function create_helper_item(){
        $model = new HelpersModel;
        $items = $model->getAllItemsPublished();
        foreach ($items as $item) {
            $model->getAllPropertiesFromItem($item->ID) ;
        }
        update_option('tainacan_update_items_helpers', 'true');
    }
    
    /**
     * 
     * @global type $wpdb
     * @return boolean
     */
    public function getAllItemsPublished() {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $query = "
                        SELECT * FROM $wp_posts p  
                        WHERE p.post_type LIKE 'socialdb_object' and p.post_status LIKE 'publish'
                ";
        $result = $wpdb->get_results($query);
        if ($result && !empty($result)) {
            return $result;
        } else {
            return false;
        }
    }
    
    public function getAllPropertiesFromItem($item_id) {
        global $wpdb;
        $array_compounds = [];
        $array_singles = [];
        $array_value_singles = [];
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "
                        SELECT * FROM $wp_postmeta p  
                        WHERE p.post_id = '".$item_id."' 
                            and ( p.meta_key LIKE 'socialdb_property_%' OR p.meta_key LIKE 'socialdb_propertyterm_%') AND p.meta_key NOT LIKE 'socialdb_property_helper_%'
                ";
        $result = $wpdb->get_results($query);
        if ($result && !empty($result)) {
            foreach ($result as $item) {
                $count = explode('_', $item->meta_key);
                if(count($count) > 3){
                    if(is_numeric($count[2]) && $count[3] !== 'cat')
                       $array_compounds[$count[2]][$count[3]] = $item->meta_value;
                    else if(is_numeric($count[2]) && $count[3] === 'cat')
                       $array_compounds[$count[2]][$count[3]] =['value'=>$item->meta_value,'meta_id'=>$item->meta_id];
                }else{
                    $array_singles[$count[2]][$item->meta_id] = $item->meta_value;
                    $array_value_singles[$item->meta_id]['value'] = $item->meta_value;
                    $array_value_singles[$item->meta_id]['property_id'] = $count[2];
                }
            }
            $this->saveHelper($item_id,$array_compounds, $array_singles,$array_value_singles);
        } else {
            return false;
        }
    }
    
    
    public function saveHelper($item_id,$coumpounds,$singles,$values) {
        echo '<pre>';
        foreach ($coumpounds as $compound_id => $indexes) {
            $array = [];
            foreach ($indexes as $index => $metas) {
                if($index === 'cat'){
                    $array[0][0] = [
                            'type' => 'term',
                            'values' => [$metas['meta_id']]
                        ]; 
                }else{
                    $metas = explode(',', $metas);
                    foreach ($metas as $index_compounds => $meta_id){
                       if(strpos($meta_id,'_cat')!==false){ 
                           $properties = get_term_meta($compound_id, 'socialdb_property_compounds_properties_id', true);
                           $cat_id = str_replace('_cat', '', $meta_id);   
                           $property_id = explode(',', $properties)[$index_compounds];
                           $meta_id = $this->sdb_add_post_meta($item_id, 'socialdb_property_'.$property_id.'_cat', $cat_id);
                           $new_children = [
                                          'type' => 'term',
                                          'values' => [$meta_id]
                                      ];
                                      $array[$index][$property_id] = $new_children; 
                        }else{    
                            if($meta_id && is_numeric($meta_id)) { 
                                if(trim($values[$meta_id]['value']) !== ''){
                                      $new_children = [
                                          'type' => is_numeric($values[$meta_id]['value']) ? 'object' : 'data',
                                          'values' => [$meta_id]
                                      ];
                                      $array[$index][$values[$meta_id]['property_id']] = $new_children; 
                               }
                            }
                       }
                    }
                }
            }
            //var_dump($compound_id);
            print_r($array);
            update_post_meta($item_id, 'socialdb_property_helper_'.$compound_id, serialize($array));
        }
        
        foreach ($singles as $single_id => $indexes) {
            $array = [];
            foreach ($indexes as $meta_id  => $meta_value) {
                $index = 0;
                if(trim($meta_value) !== ''){
                        $new_children = [
                            'type' => 'data',
                            'values' => [$meta_id]
                        ];
                        $array[$index][0] = $new_children; 
                        $index++;
                }
            }
            print_r($array);
            update_post_meta($item_id, 'socialdb_property_helper_'.$single_id, serialize($array));
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
