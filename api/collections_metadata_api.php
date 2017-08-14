<?php

abstract class CollectionsMetadataApi {
    
    public function get_metadata($param) {
        
    }
    /**
     * 
     * @param type $request
     * @return \WP_REST_Response
     */
    public function list_metadatas($request) {
        $ObjectModel = new ObjectModel();
        $params = $request->get_params();
        $collection_id = $params['id'];
        $response = [];
        
        //labels fixos
        $params['labels_collection'] = unserialize(get_post_meta($collection_id, 'socialdb_collection_fixed_properties_labels', true));
        
        //visibilidade dos metadados fixos
        $params['visibility'] = explode(',',get_post_meta($collection_id, 'socialdb_collection_fixed_properties_visibility', true)) ;
        
        //busco todos metadados
        $properties = $ObjectModel->show_object_properties(['collection_id'=>$params['id']]);
        
        //busco valores para mostrar as ordenacoes e suas abas
        $tabs = unserialize(get_post_meta($collection_id, 'socialdb_collection_update_tab_organization', true));
        $ordenation = unserialize(get_post_meta($collection_id, 'socialdb_collection_properties_ordenation', true));
        $default_tab = get_post_meta($collection_id, 'socialdb_collection_default_tab', true);
        $allTabs = $ObjectModel->sdb_get_post_meta_by_value($collection_id, 'socialdb_collection_tab');
        $tabs_and_properties = CollectionsMetadataApi::getOrdenationProperties($ordenation, $tabs, $allTabs, $properties);
        
        //itero sobre as abas para trazer os dados
        foreach ($tabs_and_properties as $tab => $tab_properties) {
            $tab_data = ['tab-id'=>$tab];
            if($tab === 'default'){
                $tab_data['tab-name'] =  ($default_tab != '') ? $default_tab : __('Default', 'tainacan');
            }else if(is_array($allTabs)){
                foreach ($allTabs as $tabMeta) {
                    if($tabMeta->meta_id == $tab)
                        $tab_data['tab-name'] =  $tabMeta->meta_value;
                }
            }
            $tab_data['tab-properties'] =  CollectionsMetadataApi::get_details_properties($tab_properties,$params);
            $response[] = $tab_data;
        }
        
        return new WP_REST_Response( $response, 200 );
    }
    
    /**
     * Metodo que organiza os os metadados de acordo com sua aba
     * @param type $propertiesOrdenation
     * @param type $mappingTabsProperties
     * @param type $allTabs
     */
    public function getOrdenationProperties($propertiesOrdenation, $mappingTabsProperties, $allTabs, $properties) {
        $arrayIds = ['default' => []];
        //todas as abas
        if ($allTabs && is_array($allTabs)) {
            foreach ($allTabs as $tab) {
                $arrayIds[$tab->meta_id] = [];
            }
        }
        //olhando na ordenacao
        if ($propertiesOrdenation && is_array($propertiesOrdenation)) {
            foreach ($propertiesOrdenation as $tab => $ordenation) {
                $arrayIds[$tab] = explode(',', $ordenation);
            }
        }
        //olhando no mapeamento
        if ($mappingTabsProperties && isset($mappingTabsProperties[0])) {
            foreach ($mappingTabsProperties[0] as $property_id => $tab) {
                if ($property_id && $tab && !in_array($property_id, $arrayIds[$tab]) && !in_array('compounds-' . $property_id, $arrayIds[$tab])) {
                    $arrayIds[$tab][] = $property_id;
                }
            }
        }
        //possiveis metadados nao ordenados
        $arrayMapTabs =  CollectionsMetadataApi::verifyPropertiesWithoutTabs($arrayIds, $properties);
        return CollectionsMetadataApi::setMetadata($arrayMapTabs, $properties);
    }

    /**
     * veririca se um metadado nao esta no array que ordenam as abas
     * @param type $arrayMapTabs
     * @param type $properties
     * @return type
     */
    public function verifyPropertiesWithoutTabs($arrayMapTabs, $properties) {
        $types = ['property_data', 'property_object', 'property_term', 'property_compounds','fixeds'];
        foreach ($types as $type) {
            if ($properties[$type] && is_array($properties[$type])) {
                foreach ($properties[$type] as $data) {
                    $tab = false;
                    foreach ($arrayMapTabs as $tabs => $values) {
                        if (in_array($data['id'], $values) || in_array('compounds-' . $data['id'], $values)) {
                            $tab = $tabs;
                        }
                    }
                    if (!$tab) {
                        $arrayMapTabs['default'][] = $data['id'];
                    }
                }
            }
        }
        return $arrayMapTabs;
    }

    /**
     * metodo que olha no array de propriedades e retorna as informacoes
     * necessarias
     *
     * @param type $id
     * @param type $properties
     */
    public function getPropertyDetail($id, $properties) {
        $ObjectModel = new ObjectModel();
        $types = ['property_data', 'property_object', 'property_term', 'property_compounds'];
        foreach ($types as $type) {
            if ($properties[$type] && is_array($properties[$type])) {
                foreach ($properties[$type] as $data) {
                    if ($data['id'] == $id) {
                        return $data;
                    }
                }
            }
        }
        $term = get_term_by('id', $id, 'socialdb_property_type');
        if (in_array($term->slug, $ObjectModel->fixed_slugs)) {
            return ['id' => $term->term_id, 'name' => $term->name, 'slug' => $term->slug];
        }
        return false;
    }

    /**
     * setando a variavel da classe com os dados para serem listados na
     * @param type $arrayMapTabs
     * @param type $properties
     */
    public function setMetadata($arrayMapTabs, $properties) {
        $return = [];
        foreach ($arrayMapTabs as $tab => $values) {
            foreach ($values as $id) {
                $values =  CollectionsMetadataApi::getPropertyDetail($id, $properties);
                if ($values)
                    $return[$tab][] = $values;
            }
        }
        return $return;
    }
    
    
    public function get_details_properties($properties,$params){
        $response = [];
        if(is_array($properties)){ //includeMetadata
            foreach ($properties as $property) {
                $details = [
                    'id' => $property['id'],
                    'name'=> $property['name'],
                    'slug'=> $property['slug'],
                    'type'=> CollectionsApi::getTypeProperty($property),
                ];
                if( $params['includeMetadata'] === '1' && $details['type'] != 'property-default'){
                    $details['metadata'] = CollectionsMetadataApi::includeMetadata($property);
                    $details['visibility'] = ( in_array($property['id'], $params['visibility'])) ? 'off' : 'on';
                }elseif ($details['type'] === 'property-default' ) {
                    $visibility = (get_term_meta($property['id'],'socialdb_property_visibility',true));
                    $details['real-name'] = $details['name'];
                    $details['name'] = (isset($params['labels_collection'][$property['id']])) ? $params['labels_collection'][$property['id']] :  $details['name'];
                    if($params['includeMetadata'] === '1'){
                        $details['visibility'] = ($visibility === 'hide' || in_array($property['id'], $params['visibility'])) ? 'off' : 'on';
                        $required = get_post_meta($params['id'], 'socialdb_collection_property_'.$property['id'].'_required', true);
                        $details['required'] = ($required != '') ? true : false;
                        $is_mask = get_post_meta($params['id'], 'socialdb_collection_property_'.$property['id'].'_mask_key', true);
                        $details['is_mask'] = ($is_mask != '') ? $is_mask : false;
                    }
                }
                
                $response[] = $details;
            }
        }
        return $response;
    }
    
    /**
     * 
     * @param type $property
     */
    public function includeMetadata($property) {
        $data = ['text', 'textarea', 'date', 'number', 'numeric', 'auto-increment', 'user'];
        $term = ['selectbox', 'radio', 'checkbox', 'tree', 'tree_checkbox', 'multipleselect'];
        $object = (isset($property['metas']['socialdb_property_object_category_id']) && !empty($property['metas']['socialdb_property_object_category_id'])) ? true : false;
        if (in_array($property['type'], $data) && !$object) {
            return CollectionsMetadataApi::metadataText($property);
        } else if (in_array($property['type'], $term) && !$object) {
            return CollectionsMetadataApi::metadataTerm($property);
        } else if ($object) {
            return CollectionsMetadataApi::metadataObject($property);
        } else if ($property['type'] == __('Compounds', 'tainacan')) {
            return CollectionsMetadataApi::metadataCompound($property);
        }
    }
    
    /**
     * 
     * @param type $property
     * @return type
     */
    public function metadataText($property) {
        $return = [];
        $return['column_ordenation'] = isset($property['metas']['socialdb_property_data_column_ordenation']) ? $property['metas']['socialdb_property_data_column_ordenation'] : false;
        $return['widget'] = $property['metas']['socialdb_property_data_widget'];
        $return['cardinality'] = isset($property['metas']['socialdb_property_data_cardinality']) ? $property['metas']['socialdb_property_data_cardinality'] : '1';
        $return['is_mask'] = empty($property['metas']['socialdb_property_data_mask']) ? false : $property['metas']['socialdb_property_data_mask'] ;
        $return['required'] = ($property['metas']['socialdb_property_required'] === 'true') ? true : false;
        $return['default_value'] = ( $property['metas']['socialdb_property_default_value'] === '') ? '' : $property['metas']['socialdb_property_default_value'];
        $return['text_help'] = ( trim($property['metas']['socialdb_property_help']) === '') ? '' : $property['metas']['socialdb_property_help'];
        $return['created_category'] = $property['metas']['socialdb_property_created_category'];
        $return['collection_id'] = $property['metas']['socialdb_property_collection_id'];
        $return['used_by_categories'] =  (is_array($property['metas']['socialdb_property_used_by_categories'])) ? array_filter($property['metas']['socialdb_property_used_by_categories']) : [];
        $return['visualization'] = $property['metas']['socialdb_property_visualization'];
        $return['locked'] = ( isset($property['metas']['socialdb_property_locked'])) ? true : false;
        $return['is_aproximate_date'] = ( $property['metas']['socialdb_property_is_aproximate_date'] === '1') ? true : false;
        $return['is_repository_property'] = $property['metas']['is_repository_property'];
        return $return;
    }
    
    /**
     * 
     * @param type $property
     * @return type
     */
    public function metadataObject($property) {
        $return = [];
        $return['search_in_properties'] =  empty($property['metas']['socialdb_property_to_search_in']) ? false : $property['metas']['socialdb_property_to_search_in'] ;
        $return['avoid_items'] = ( isset($property['metas']['socialdb_property_avoid_items'])) ? true : false;
        $return['habilitate_new_item'] = ( isset($property['metas']['socialdb_property_habilitate_new_item']) && $property['metas']['socialdb_property_habilitate_new_item'] === 'true' ) ? true : false;
        $return['object_category_id'] = empty($property['metas']['socialdb_property_to_search_in']) ? false : $property['metas']['socialdb_property_to_search_in'] ;
        $return['is_filter'] = empty($property['metas']['socialdb_property_object_is_facet']) ? false : $property['metas']['socialdb_property_object_is_facet'] ;
        $return['reverse'] = empty($property['metas']['socialdb_property_object_reverse']) ? false : $property['metas']['socialdb_property_object_is_facet'] ;
        //used always
        $return['collection_id'] = $property['metas']['socialdb_property_collection_id'];
        $return['cardinality'] = isset($property['metas']['socialdb_property_object_cardinality']) ? $property['metas']['socialdb_property_object_cardinality'] : '1';
        $return['required'] = ($property['metas']['socialdb_property_required'] === 'true') ? true : false;
        $return['created_category'] = $property['metas']['socialdb_property_created_category'];
        $return['used_by_categories'] =  (is_array($property['metas']['socialdb_property_used_by_categories'])) ? array_filter($property['metas']['socialdb_property_used_by_categories']) : [];
        $return['visualization'] = $property['metas']['socialdb_property_visualization'];
        $return['locked'] = ( isset($property['metas']['socialdb_property_locked'])) ? true : false;
        $return['is_repository_property'] = ( $property['metas']['is_repository_property'] === 'true') ? true : false;
        
        return $return;
    }
    
    /**
     * 
     * @param type $property
     * @param type $is_compound
     * @return type
     */
    public function metadataTerm($property,$is_compound = false) {
        $ObjectModel = new ObjectModel();
        $return = [];
        
        $return['taxonomy'] = isset($property['metas']['socialdb_property_term_root']) ? $property['metas']['socialdb_property_term_root'] : '1';
        //used always
        $return['collection_id'] = $property['metas']['socialdb_property_collection_id'];
        $return['cardinality'] = isset($property['metas']['socialdb_property_term_cardinality']) ? $property['metas']['socialdb_property_term_cardinality'] : '1';
        $return['required'] = ($property['metas']['socialdb_property_required'] === 'true') ? true : false;
        $return['created_category'] = $property['metas']['socialdb_property_created_category'];
        $return['used_by_categories'] =  (is_array($property['metas']['socialdb_property_used_by_categories'])) ? array_filter($property['metas']['socialdb_property_used_by_categories']) : [];
        $return['visualization'] = $property['metas']['socialdb_property_visualization'];
        $return['locked'] = ( isset($property['metas']['socialdb_property_locked'])) ? true : false;
        $return['is_repository_property'] = ( $property['metas']['is_repository_property'] === 'true') ? true : false;
        
        //verifico se não tem filhos
        if(!isset($property['has_children']) && $return['taxonomy'])
            $property['has_children'] = $ObjectModel->getChildren($return['taxonomy']);
        
        //se caso for compostos
        if(!$is_compound && is_array($property['has_children'])){
            $return['categories'] = [];
            foreach ($property['has_children'] as $term) {
                $properties_children = get_term_meta($term->term_id, 'socialdb_category_property_id');
                $array['term'] = $term;
                $array['properties-children'] = [];
                if($properties_children && is_array($properties_children)){
                    $properties_children = array_filter($properties_children);
                    foreach ($properties_children as $property_id_children) {
                        $property_children = $ObjectModel->get_all_property($property_id_children,true);
                        //verifico se eh composto
                        if(isset($property_children['metas']['socialdb_property_is_compounds']) && $property_children['metas']['socialdb_property_is_compounds'] != ''){
                            continue;
                        }
                        
                        $details = [
                            'id' => $property_children['id'],
                            'name'=> $property_children['name'],
                            'slug'=> $property_children['slug'],
                            'type'=> CollectionsApi::getTypeProperty($property_children),
                        ];
                        $details['metadata'] = CollectionsMetadataApi::includeMetadata($property_children);
                        $visibility = (get_term_meta($property_children['id'],'socialdb_property_visibility',true));
                        $details['visibility'] = ($visibility === 'hide') ? 'off' : 'on';
                        if(empty($details['metadata']))
                            unset($details['metadata']);
                        $array['properties-children'][] = $details;
                    }
                }
                
                //categorias
                $return['categories'][] = $array;
            }
        }
        
        return $return;
    }
    
    /**
     * 
     * @param type $param
     */
    public function metadataCompound($property) {
        $ObjectModel = new ObjectModel();
        //used always
        $return['collection_id'] = $property['metas']['socialdb_property_collection_id'];
        $return['cardinality'] = isset($property['metas']['socialdb_property_compounds_cardinality']) ? $property['metas']['socialdb_property_compounds_cardinality'] : '1';
        $return['required'] = ($property['metas']['socialdb_property_required'] === 'true') ? true : false;
        $return['created_category'] = $property['metas']['socialdb_property_created_category'];
        $return['used_by_categories'] =  (is_array($property['metas']['socialdb_property_used_by_categories'])) ? array_filter($property['metas']['socialdb_property_used_by_categories']) : [];
        $return['visualization'] = $property['metas']['socialdb_property_visualization'];
        $return['locked'] = ( isset($property['metas']['socialdb_property_locked'])) ? true : false;
        $return['is_repository_property'] = ( $property['metas']['is_repository_property'] === 'true') ? true : false;
        
        $return['children'] = [];
        //se as filhas estiverem concatenadas
        $childrens =  $property['metas']['socialdb_property_compounds_properties_id'];
        $childrens = (is_array($childrens)) ? $childrens : explode(',', $childrens);

        // itero sobre os filhos para buscar os seus dados
        foreach ($childrens as $children) {
            $children = (is_array($children)) ?  $children : $ObjectModel->get_all_property($children, true);
            $children_array[$children['id']] = $children;
        }
        
        //iterando sobre as propriedades
        if(is_array($children_array)){
            foreach ($children_array as $property) {
                $details = [
                    'id' => $property['id'],
                    'name'=> $property['name'],
                    'slug'=> $property['slug'],
                    'type'=> CollectionsApi::getTypeProperty($property),
                ];
                $data = ['text', 'textarea', 'date', 'number', 'numeric', 'auto-increment', 'user'];
                $term = ['selectbox', 'radio', 'checkbox', 'tree', 'tree_checkbox', 'multipleselect'];
                $object = (isset($property['metas']['socialdb_property_object_category_id']) && !empty($property['metas']['socialdb_property_object_category_id'])) ? true : false;
                if (in_array($property['type'], $data) && !$object) {
                    $details['metadata'] = CollectionsMetadataApi::metadataText($property);
                } else if (in_array($property['type'], $term) && !$object) {
                    $details['metadata'] = CollectionsMetadataApi::metadataTerm($property);
                } else if ($object) {
                    $details['metadata'] = CollectionsMetadataApi::metadataObject($property);
                }
                
                $return['children'][] = $details;
            }
        }
        return $return;
    }
    
    
}