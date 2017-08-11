<?php

abstract class CollectionsMetadataApi {
    
    public function list_metadatas($request) {
        $ObjectModel = new ObjectModel();
        $params = $request->get_params();
        $collection_id = $params['id'];
        $response = [];
        
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
                $tab_data['name'] =  ($default_tab != '') ? $default_tab : __('Default', 'tainacan');
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
                if($params['includeMetadata']==='1'){
                    
                }
                $response[] = $details;
            }
        }
        return $response;
    }
    
}