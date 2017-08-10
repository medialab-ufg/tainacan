<?php

abstract class CollectionsApi {

    public function get_collections() {
        $CollectionModel = new CollectionModel;
        return $CollectionModel->get_all_collections();
    }

    public function get_collection($request) {
        $params = $request->get_params();

        $CollectionModel = new CollectionModel;
        return $CollectionModel->get_collection_data($params['id']);
    }

    public function get_collection_items($request) {
        $items = [];
        $params = $request->get_params();

        //se existir consultas
        if(isset($params['filter']))
            return CollectionsApi::filterByArgs($params);

        //caso for uma consulta simples
        $CollectionModel = new CollectionModel;
        $ObjectModel = new ObjectModel();
        $properties = $ObjectModel->show_object_properties(['collection_id'=>$params['id']]);
        $metadatas = CollectionsApi::structProperties($properties);
        $data =  $CollectionModel->get_collection_posts($params['id']);
        if ($data) {
             foreach ($data as $value) {
               $array['item'] = $value;
               $array['metadata'] = CollectionsApi::getValuesItem($metadatas,$value->ID);
               $items[] = $array;
             }
            return new WP_REST_Response( $items, 200 );
        }else{
            return new WP_Error('empty_collection',  __( 'No items inserted or found!', 'tainacan' ), array('status' => 404));
        }
    }

    public function get_collection_item($request) {
        $params = $request->get_params();

        $Result['item'] = get_post($params['post']);
        if (empty($Result['item'])) {
            return new WP_Error('invalid_item_id', 'Invalid Item ID', array('status' => 404));
        }
        $Result['metas'] = get_post_meta($params['post']);
        return $Result;
    }
/*******************************************************************************/
// Metodos da classe
    private function filterByArgs($params){
        $filters = $params['filter'];
        $wpQueryModel = new WPQueryModel();
        $ObjectModel = new ObjectModel();
        $properties = $ObjectModel->show_object_properties(['collection_id'=>$params['id']]);
        $metadatas = CollectionsApi::structProperties($properties);
        $args = $wpQueryModel->queryAPI($filters,$params['id']);
        $loop = new WP_Query($args);
        if ($loop->have_posts()) {
            $data = [];
            while ( $loop->have_posts() ) : $loop->the_post();
                $array['item'] = get_post();
                $array['metadata'] = CollectionsApi::getValuesItem($metadatas,get_the_ID());
                $data[] = $array;
            endwhile;
            return new WP_REST_Response( $data, 200 );
        }else{
            return new WP_Error('empty_search',  __( 'No items found with these arguments!', 'tainacan' ), array('status' => 404));
        }
    }

    /**
     * unifica o array de todos os metadados
     * @param type $properties
     * @return type
     */
    private function structProperties($properties) {
        $structedProperties = [];
        //$types = ['property_data', 'property_object', 'property_term', 'property_compounds','fixeds'];
        $types = ['property_data', 'property_object', 'property_term', 'property_compounds'];
        foreach ($types as $type) {
            if ($properties[$type] && is_array($properties[$type])) {
                foreach ($properties[$type] as $data) {
                    $structedProperties[] = $data;
                }
            }
        }
        return $structedProperties;
    }

    /**
    * metodo que busca o valordo item a partir do metadado
    */
    public function getValuesItem($metadatas,$item_id){
        $array = [];

        foreach ($metadatas as $metadata) {
            $values = CollectionsApi::getValuePropertyHelper($item_id,$metadata['id']);
            $data = ['text', 'textarea', 'date', 'number', 'numeric', 'auto-increment', 'user'];
            $term = ['selectbox', 'radio', 'checkbox', 'tree', 'tree_checkbox', 'multipleselect'];
            $object = (isset($metadata['metas']['socialdb_property_object_category_id']) && !empty($metadata['metas']['socialdb_property_object_category_id'])) ? true : false;
            if (in_array($metadata['type'], $data) && !$object) {

            } else if (in_array($metadata['type'], $term) && !$object) {
              #code
            } else if ($object) {
              #code
            } else if ($metadata['type'] == __('Compounds', 'tainacan')) {
                $array[] = CollectionsApi::prettifyPropertyCompound($metadata,'item',$values);
            }
        }
        return $array;
    }

    /**
     *
     * @param type $item_id
     * @param type $property_id
     * @return boolean
     */
    public function getValuePropertyHelper($item_id, $property_id) {
        $meta = get_post_meta($item_id, 'socialdb_property_helper_' . $property_id, true);
        if ($meta && $meta != '') {
            $array = unserialize($meta);
            return $array;
        } else {
            return false;
        }
    }


    public function prettifyPropertyCompound($property,$type = 'item',$values = false){
        $return = [];
        $wpQueryModel = new WPQueryModel();
        if($type === 'item'){
            $return = ['id' => $property['id'],'name'=>$property['name'],'children'=>[]];
            foreach ($property['metas']['socialdb_property_compounds_properties_id'] as $children) {
                $children_array = ['id' => $children['id'],'name'=>$children['name']];
                if($values){
                    foreach ($values as $value) {
                        if(isset($value[$children['id']]) && is_array($value[$children['id']]['values']) && !empty($value[$children['id']]['values'])){
                            foreach ($value[$children['id']]['values'] as $meta_id) {
                                $children_array['values'][] = $wpQueryModel->sdb_get_post_meta($meta_id)->meta_value;
                            }
                        }
                    }
                }else{
                    $children_array['empty'] = true;
                }
                $return['children'][] = $children_array;
            }
        }else{
            $return = ['id' => $property['id'],'name'=>$property['name'],'children'=>[]];
            foreach ($property['socialdb_property_compounds_properties_id'] as $children) {
                $children_array = ['id' => $children['id'],'name'=>$children['name']];
            }
            $return['children'][] = $children_array;
        }
        return $return;
    }



}
