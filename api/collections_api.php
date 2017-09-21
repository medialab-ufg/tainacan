<?php

abstract class CollectionsApi {

    public function get_collections($request) {
        $params = $request->get_params();
        
        $CollectionModel = new CollectionModel;
        $wpQueryModel = new WPQueryModel();
        $data = [];
        if(isset($params['filter'])){
            $params['filter']['post_type'] = 'socialdb_collection';
            $args = $wpQueryModel->queryAPI($params['filter']);
            $loop = new WP_Query($args);
            if ($loop->have_posts()) {
                while ( $loop->have_posts() ) : $loop->the_post();
                    $data[] = CollectionsApi::get_item( get_post()->ID );
                endwhile;
                return new WP_REST_Response( $data, 200 );
            }else{
                return new WP_Error('empty_search',  __( 'No items found with these arguments!', 'tainacan' ), array('status' => 404));
            }
        }else{
            $collections = $CollectionModel->get_all_collections();
            $collection_root_id = get_option('collection_root_id');
            foreach ($collections as $collection) {
                if($collection_root_id != $collection->ID)
                    $data[] = CollectionsApi::get_item( $collection->ID );
            }
            if(count($data)>0)
                return new WP_REST_Response( $data, 200 );
        }
       return new WP_Error('empty_search',  __( 'No items found with these arguments!', 'tainacan' ), array('status' => 404));
    }

    public function get_collection($request) {
        $params = $request->get_params();

        $CollectionModel = new CollectionModel;
        $data =  $CollectionModel->get_collection_data($params['id']);
        if($data)
            return new WP_REST_Response( CollectionsFiltersApi::getCollectionFilters($data), 200 );
        else
            return new WP_Error('collection_not_found',  __( 'No collections found!', 'tainacan' ), array('status' => 404));
        
    }

    public function get_collection_items($request) {
        $items = [];
        $params = $request->get_params();

        //se existir consultas
        $array['id'] = $params['id'];
        $array['includeMetadata'] = $params['includeMetadata'];
        $array['filter'] = ( isset($params['filter']) ) ? $params['filter'] : [];
        return CollectionsApi::filterByArgs($array);

        //caso for uma consulta simples
//        $CollectionModel = new CollectionModel;
//        $ObjectModel = new ObjectModel();
//        $properties = $ObjectModel->show_object_properties(['collection_id'=>$params['id']]);
//        $metadatas = CollectionsApi::structProperties($properties);
//        $data =  $CollectionModel->get_collection_posts($params['id']);
//        if ($data) {
//             foreach ($data as $value) {
//               $array['item'] = CollectionsApi::get_item($value->ID);
//               if($params['includeMetadata'] === '1')
//                    $array['metadata'] = CollectionsApi::getValuesItem($metadatas,$value->ID);
//
//               $items[] = $array;
//             }
//            return new WP_REST_Response( $items, 200 );
//        }else{
//            return new WP_Error('empty_collection',  __( 'No items inserted or found!', 'tainacan' ), array('status' => 404));
//        }
    }

    /**
     *
     *
     * @param $request
     * @return WP_Error
     */
    public function get_collection_item($request) {
        $ObjectModel = new ObjectModel();
        $params = $request->get_params();

        $Result['item'] =  CollectionsApi::get_item($params['post'],TRUE);
        $properties = $ObjectModel->show_object_properties(['collection_id'=>$params['id']]);
        $metadata = CollectionsApi::structProperties($properties);
        if (empty($Result['item'])) {
            return new WP_Error('invalid_item_id', 'Invalid Item ID', array('status' => 404));
        }
        $Result['metadata'] = CollectionsApi::getValuesItem($metadata,$params['post']);;
        return new WP_REST_Response( $Result, 200 );
    }
    /*******************************************************************************/
    // Metodos usados pela classe
    
    public function get_item($item_id,$attachments = false) {
        $CollectionModel = new CollectionModel;
        $item = get_post($item_id);
        unset($item->menu_order);
        unset($item->post_mime_type);
        unset($item->pinged);
        unset($item->post_password);
        unset($item->to_ping);
        
        $collection_id = get_post_meta($item_id, 'socialdb_object_collection_init', true);
        if(empty($collection_id)){
          $collection_id = $CollectionModel->get_collection_by_object($item_id)[0]->ID;
        }
        
        //busco a url certa caso for post type object
        if($item->post_type === 'socialdb_object'){
            $item->guid = get_the_permalink($collection_id).$item->post_name;
            //link do item
            $item->link = [ RepositoryApi::getLink('object',$item_id,'self',[ 'collection_id' => $collection_id ]) ];
        }
        
       //caso o post type collection verifico se possui a capap
        if($item->post_type === 'socialdb_collection'){
            $cover_id = get_post_meta($item_id, 'socialdb_collection_cover_id', true);
            if($cover_id && $cover_id !== '')
                $item->cover = get_attachment_link($cover_id);
            //referncia ao link da colecao na api
            $item->link = [ RepositoryApi::getLink('collection',$item_id) ];
        }
        
        unset($item->post_type);
        //thumbnail do item
        if(has_post_thumbnail($item_id)){
            $item->thumbnail = get_the_post_thumbnail_url($item_id);
            if(!$item->thumbnail){
                $id = get_post_meta($item_id,'_thumbnail_id',true);
                if($id){
                    $item->thumbnail = get_post($id)->guid;
                }
            }
        }
        
        //se for para mostrar anexos
        if($attachments){
            $objectFileClass = new ObjectFileModel();
            $item->attachments = $objectFileClass->get_files(['object_id'=>$item_id]);
        }
        
        return $item;
    }
    /**
     * 
     * @param type $params
     * @return \WP_Error|\WP_REST_Response
     */
    private function filterByArgs($params){
        $filters = $params['filter'];
        $wpQueryModel = new WPQueryModel();
        $ObjectModel = new ObjectModel();
        $properties = $ObjectModel->show_object_properties(['collection_id'=>$params['id']]);
        $metadatas = CollectionsApi::structProperties($properties);
        $args = $wpQueryModel->queryAPI($filters,$params['id']);
        $loop = new WP_Query($args);
        
        //itero sobre os itens
        if ($loop->have_posts()) {
            $data = [];
            //total de itens
            $data['found_items'] = $loop->found_posts;

            //limite
            $data['items_per_page'] = $loop->post_count;

            //page
            $data['page'] = (isset($args['paged'])) ? $args['paged'] : 1;
            while ( $loop->have_posts() ) : $loop->the_post();
                $array['item'] = CollectionsApi::get_item( get_post()->ID );
                if($params['includeMetadata'] === '1')
                    $array['metadata'] = CollectionsApi::getValuesItem($metadatas,get_the_ID());
                $data['items'][] = $array;
            endwhile;
            return new WP_REST_Response( $data, 200 );
        }else{
            return new WP_Error('empty_search',  __( 'No items found with these arguments!', 'tainacan' ), array('status' => 404));
        }
    }
    
    /**
     * 
     * @param type $property
     */
    public function getTypeProperty($property) {
        $wpQueryModel = new WPQueryModel();
        $is_object = (isset($property['metas']['socialdb_property_object_category_id']) && !empty($property['metas']['socialdb_property_object_category_id'])) ? true : false;
        if($is_object){
            return 'item';
        }else if(in_array($property['slug'], $wpQueryModel->fixed_slugs)){
            return 'property-default';
        }else if($property['type'] === __('Compounds', 'tainacan')){
            return 'compound';
        }else{
            return $property['type'];
        }         
    }

    /**
     * unifica o array de todos os metadados
     * @param array $properties
     * @return array com todos os metadados unificados
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
                $array[] = CollectionsApi::prettifyPropertyData($metadata,'item',$values);
            } else if (in_array($metadata['type'], $term) && !$object) {
                $array[] = CollectionsApi::prettifyPropertyTerm($metadata,'item',$values);
            } else if ($object) {
                $array[] = CollectionsApi::prettifyPropertyObject($metadata,'item',$values);
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

    /**
     * Metodo que prerpara o array de propriedade composto a ser mostrado
     * na resposta da
     * @param type $property
     * @param type $type
     * @param type $values
     * @return type
     */
    public function prettifyPropertyCompound($property,$type = 'item',$values = false){
        $return = [];
        $wpQueryModel = new WPQueryModel();
        
        //se estiver buscando os valores de um item especifico
        if($type === 'item'){
            $return = ['id' => $property['id'],'name'=>$property['name'],'type'=> CollectionsApi::getTypeProperty($property),'children'=>[]];
            $childrens =  $property['metas']['socialdb_property_compounds_properties_id'];
            $childrens = (is_array($childrens)) ? $childrens : explode(',', $childrens);
            
            // itero sobre os filhos para buscar os seus dados
            foreach ($childrens as $children) {
                $children = (is_array($children)) ?  $children : $wpQueryModel->get_all_property($children, true);
                $children_array[$children['id']] = $children;
            }
            
            //se existir o valor inserido
            if($values){
                $line = [];
                foreach ($values as $value) {
                    $column = [];
                    foreach ($children_array as $children) {
                        $column[] = CollectionsApi::get_values_atom($children,$value);
                    }
                    $line[] = $column;
                }
                $return['children'] = $line;
            }else{
                $column = [];
                //nao existe valor busco apenas os metadados
                foreach ($children_array as $children) {
                        $column[] = CollectionsApi::get_values_atom($children,[]);
                }
                 $return['children'][] = $column;
            }            
        }
        return $return;
    }

    /**
     *
     *
     * @param $property
     * @param $value A linha de um helper dos valores de um item
     * @return array Os valores reais de um metadado
     */
    private function get_values_atom($property,$value){
        $wpQueryModel = new WPQueryModel();
        $array = ['id' => $property['id'],'name'=>$property['name'],'type'=>CollectionsApi::getTypeProperty($property)];
        if(isset($value[$property['id']]) && is_array($value[$property['id']]['values']) && !empty($value[$property['id']]['values'])){
            foreach ($value[$property['id']]['values'] as $meta_id) {
                $array['values'][] = $wpQueryModel->sdb_get_post_meta($meta_id)->meta_value;
            }
        }
        if(!isset($array['values']))
            $array['empty'] = true;
        
        return $array;
    }
    
    /**
     * metodo que prerpara o array de metadado de propriedade de dados a ser mostrado
     * na resposta
     * 
     * @param array $property
     * @param string $type
     * @param array $values
     * @return array
     */
    public function prettifyPropertyData($property,$type = 'item',$values = false) {
        $return = [];
        $wpQueryModel = new WPQueryModel();
        if($type === 'item'){
            $return = ['id' => $property['id'],'name'=>$property['name'],'type'=> CollectionsApi::getTypeProperty($property)];
            if($values){
                foreach ($values as $value) {
                    if(isset($value[0]) && is_array($value[0]['values']) && !empty($value[0]['values'])){
                        foreach ($value[0]['values'] as $meta_id) {
                            $meta =  $wpQueryModel->sdb_get_post_meta($meta_id);
                            $item_id =  $meta->post_id;
                            $return['values'][] = $meta->meta_value;        
                        }
                        //se o plugin de data aproximada estiver ativado
                        if (isset($item_id) && is_plugin_active('data_aacr2/data_aacr2.php') && $property['type'] == 'date' && get_post_meta($item_id, "socialdb_property_{$property['id']}_0_date", true)):
                            unset($return['values']);
                            $return['values'][] =  get_post_meta($object_id, "socialdb_property_{$property['id']}_0_date", true);
                        endif;    
                    }
                }
            }else{
                $return['empty'] = true;
            }
        }else{
            $return = ['id' => $property['id'],'name'=>$property['name']]; 
        }
        return $return;
    }
    
    /**
     * metodo que prerpara o array de metadado de propriedade de relacionamento a ser mostrado
     * na resposta
     * 
     * @param array $property
     * @param string $type
     * @param array $values
     * @return array
     */
    public function prettifyPropertyObject($property,$type = 'item',$values = false) {
        $return = [];
        $empty = false;
        $wpQueryModel = new WPQueryModel();
        if($type === 'item'){
            $return = ['id' => $property['id'],'name'=>$property['name'],'type'=> CollectionsApi::getTypeProperty($property)];
            if($values){
                foreach ($values as $value) {
                    if(isset($value[0]) && is_array($value[0]['values']) && !empty($value[0]['values'])){
                        foreach ($value[0]['values'] as $meta_id) {
                            $return['values'][] = $wpQueryModel->sdb_get_post_meta($meta_id)->meta_value;
                        }
                    }
                }
                if(!isset($return['values'])){
                     $return['empty'] = true;
                }
            }else{
                $return['empty'] = true;
            }
        }else{
            $return = ['id' => $property['id'],'name'=>$property['name']]; 
        }
        return $return;
    }
    
    /**
     * metodo que prerpara o array de metadado de propriedade de relacionamento a ser mostrado
     * na resposta
     * 
     * @param array $property
     * @param string $type
     * @param array $values
     * @return array
     */
    public function prettifyPropertyTerm($property,$type = 'item',$values = false) {
        $return = [];
        $wpQueryModel = new WPQueryModel();
        if($type === 'item'){
            $return = ['id' => $property['id'],'name'=>$property['name'],'type'=> CollectionsApi::getTypeProperty($property)];
            if($values){
                foreach ($values as $value) {
                    if(isset($value[0]) && is_array($value[0]['values']) && !empty($value[0]['values'])){
                        foreach ($value[0]['values'] as $meta_id) {
                            $metadata = $wpQueryModel->sdb_get_post_meta($meta_id);
                            $return['values'][] = $metadata->meta_value;
                            $item_id = $metadata->post_id;
                        }
                        //verifico se o metadado tem propriedades filhas
                        if(isset($item_id)){
                            $properties_children = CollectionsApi::getCategoryProperties($return['values'],$item_id);
                            if($properties_children)
                                 $return['term-properties'] = $properties_children; 
                        }
                    }
                }
            }else{
                $return['empty'] = true;
            }
        }else{
            $return = ['id' => $property['id'],'name'=>$property['name']]; 
        }
        return $return;
    }

    /**
     * Metodo que busca os valores dos metadados extras de categoria
     *
     * @param $values A(s) categoria selecionada que sera buscado os metadados
     * @param $item_id O id do item para buscar os valores
     * @return array Os metadados extras de categoria
     */
    public function getCategoryProperties($values,$item_id) {
        $properties = [];
        $array = [];
        $wpQueryModel = new WPQueryModel();
        //busco a propriedades
        foreach ($values as $value) {
            $terms = get_term_meta($value, 'socialdb_category_property_id');
            if($terms)
                $properties = array_merge ($properties, $terms);
        }
        
        //retirando possiveis duplicacoes
        $properties = array_filter($properties);
        foreach ($properties as $metadata) {
            $metadata = $wpQueryModel->get_all_property($metadata,true);
            if(isset($metadata['metas']['socialdb_property_is_compounds']) && $metadata['metas']['socialdb_property_is_compounds'] != ''){
                continue;
            }
            $values = CollectionsApi::getValuePropertyHelper($item_id,$metadata['id']);
            $data = ['text', 'textarea', 'date', 'number', 'numeric', 'auto-increment', 'user'];
            $term = ['selectbox', 'radio', 'checkbox', 'tree', 'tree_checkbox', 'multipleselect'];
            $object = (isset($metadata['metas']['socialdb_property_object_category_id']) && !empty($metadata['metas']['socialdb_property_object_category_id'])) ? true : false;
            if (in_array($metadata['type'], $data) && !$object) {
                $array[] = CollectionsApi::prettifyPropertyData($metadata,'item',$values);
            } else if (in_array($metadata['type'], $term) && !$object) {
                $array[] = CollectionsApi::prettifyPropertyTerm($metadata,'item',$values);
            } else if ($object) {
                $array[] = CollectionsApi::prettifyPropertyObject($metadata,'item',$values);
            } else if ($metadata['type'] == __('Compounds', 'tainacan')) {
                $array[] = CollectionsApi::prettifyPropertyCompound($metadata,'item',$values);
            }
        }
        return $array;
    }
    
    
}
