<?php


abstract class CollectionsFiltersApi {
    
    /**
     * metodo que traz os filtros e demais metadatarmacoes importantes de uma colecao
     * 
     */
    public function getCollectionFilters($collection_data) {
        $meta =  $collection_data['collection_metas'];
        $response['collection'] = CollectionsApi::get_item($collection_data['collection_post']->ID);
        $response['metadata']['category_root_id'] = $meta['socialdb_collection_object_type'];
        $response['metadata']['default_ordenation'] = $meta['socialdb_collection_default_ordering'];
        $response['metadata']['ordenation_form'] = $meta['socialdb_collection_ordenation_form'];
        $response['metadata']['address'] = $meta['socialdb_collection_address'];
        $response['metadata']['license'] = $meta['socialdb_collection_license'];
        $response['metadata']['columns'] = $meta['socialdb_collection_columns'];
        $response['metadata']['exportation_active'] = $meta['socialdb_collection_mapping_exportation_active'];
        $response['metadata']['allow_hierarchy'] = $meta['socialdb_collection_allow_hierarchy'];
        $response['metadata']['collection_parent'] = $meta['socialdb_collection_parent'];
        $response['metadata']['license_pattern'] = $meta['socialdb_collection_license_pattern'];
        $response['metadata']['license_enabled'] = $meta['socialdb_collection_license_enabled'];
        $response['metadata']['collection_view'] = $meta['collection_view_count'];
        $response['metadata']['list_mode'] = $meta['socialdb_collection_list_mode'];
        $response['metadata']['add_watermark'] = $meta['socialdb_collection_add_watermark'];
        $response['metadata']['moderation_type'] = $meta['socialdb_collection_moderation_type'];
        $response['metadata']['item_name'] = $meta['socialdb_collection_object_name'];
        $response['metadata']['download_control'] = $meta['socialdb_collection_download_control'];
        $response['metadata']['submission_visualization'] = $meta['socialdb_collection_submission_visualization'];
        $response['metadata']['visualization_page_category'] = $meta['socialdb_collection_visualization_page_category'];
        $response['metadata']['habilitate_media'] = $meta['socialdb_collection_habilitate_media'];
        $response['metadata']['item_habilitate_media'] = $meta['socialdb_collection_habilitate_media'];
        $response['metadata']['item_visualization'] = $meta['socialdb_collection_item_visualization'];
        $response['metadata']['default_color_scheme'] = $meta['socialdb_default_color_scheme'];
        $response['metadata']['show_header'] = $meta['socialdb_collection_show_header'];
        $response['metadata']['add_item'] = $meta['socialdb_collection_add_item'];
        $response['metadata']['vinculated_items'] = $meta['socialdb_collection_vinculated_object'];
        $response['metadata']['moderators'] =  $meta['socialdb_collection_moderator'];
        $response['metadata']['channels'] = $meta['socialdb_collection_channel'];
        $response['metadata']['color_scheme'] = ( $meta['socialdb_collection_color_scheme'] != '') ? unserialize( $meta['socialdb_collection_color_scheme']) : false;
        $response['metadata']['slideshow_time'] =  $meta['socialdb_collection_slideshow_time'];
        $response['metadata']['use_prox_mode'] =  $meta['socialdb_collection_use_prox_mode'];
        $response['metadata']['latidude_meta'] = $meta['socialdb_collection_latitude_meta'];
        $response['metadata']['longitude_meta'] = $meta['socialdb_collection_longitude_meta'];
        $response['metadata']['table_metas'] = ($meta['socialdb_collection_table_metas']) ? unserialize(base64_decode($meta['socialdb_collection_table_metas'])) : false;
        $response['metadata']['privacity'] = $meta['sociadb_collection_privacity'];
        
        //permissoes
        $response['metadata']['permissions'] = CollectionsFiltersApi::getPermissions($collection_data);
        
        //filtros
        $response['metadata']['filters'] = CollectionsFiltersApi::getFilters($collection_data);
        
        return $response;
    } 
    
    /**
     * trabalhando no retorno dos valores das permissoes
     */
    public function getPermissions($collection_data) {
        $return = [];
        foreach ($collection_data['collection_metas'] as $index => $value) {
            if(strpos($index, 'socialdb_collection_permission_')!==false){
                $return[str_replace('socialdb_collection_permission_', '', $index)] = $value;
            }
        }
        return $return;
    }
    
    /**
     * 
     * @param type $param
     */
    public function getFilters($collection_data) {
        $metas = $collection_data['collection_metas'];
        $response = [];
        if(isset($metas['socialdb_collection_facets']) && is_array($metas['socialdb_collection_facets'])){
            foreach ($metas['socialdb_collection_facets'] as $filter) {
                $return = [];
                $return['id'] = $filter->term_id;
                $return['name'] = $filter->name;
                $return['type'] = $filter->taxonomy === 'socialdb_property_type' ? 'metadata' : 'category';
                foreach ($metas as $index => $value) {
                    if(strpos($index, 'socialdb_collection_facet_'.$filter->term_id.'_')!==false){
                        $key = str_replace('socialdb_collection_facet_'.$filter->term_id.'_', '', $index);
                        $return[$key] = $value;
                    }
                }
                $response[] = $return;
            }
        }
        return $response;
    }
}