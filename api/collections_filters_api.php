<?php


abstract class CollectionsFiltersApi {
    
    /**
     * metodo que traz os filtros e demais informacoes importantes de uma colecao
     * 
     */
    public function getCollectionFilters($collection_data) {
        $meta =  $collection_data['collection_metas'];
        $response['collection'] = CollectionsApi::get_item($collection_data['collection_post']->ID);
        $response['info']['category_root_id'] = $meta['socialdb_collection_object_type'];
        $response['info']['default_ordenation'] = $meta['socialdb_collection_default_ordering'];
        $response['info']['ordenation_form'] = $meta['socialdb_collection_ordenation_form'];
        $response['info']['address'] = $meta['socialdb_collection_address'];
        $response['info']['license'] = $meta['socialdb_collection_license'];
        $response['info']['columns'] = $meta['socialdb_collection_columns'];
        $response['info']['exportation_active'] = $meta['socialdb_collection_mapping_exportation_active'];
        $response['info']['allow_hierarchy'] = $meta['socialdb_collection_allow_hierarchy'];
        $response['info']['collection_parent'] = $meta['socialdb_collection_parent'];
        $response['info']['license_pattern'] = $meta['socialdb_collection_license_pattern'];
        $response['info']['license_enabled'] = $meta['socialdb_collection_vinculated_object'];
        $response['info']['collection_view'] = $meta['collection_view_count'];
        $response['info']['list_mode'] = $meta['socialdb_collection_list_mode'];
        $response['info']['add_watermark'] = $meta['socialdb_collection_add_watermark'];
        $response['info']['moderation_type'] = $meta['socialdb_collection_moderation_type'];
        $response['info']['item_name'] = $meta['socialdb_collection_object_name'];
        $response['info']['download_control'] = $meta['socialdb_collection_download_control'];
        $response['info']['submission_visualization'] = $meta['socialdb_collection_submission_visualization'];
        $response['info']['visualization_page_category'] = $meta['socialdb_collection_visualization_page_category'];
        $response['info']['habilitate_media'] = $meta['socialdb_collection_habilitate_media'];
        $response['info']['item_habilitate_media'] = $meta['socialdb_collection_vinculated_object'];
        $response['info']['item_visualization'] = $meta['socialdb_collection_item_visualization'];
        $response['info']['default_color_scheme'] = $meta['socialdb_default_color_scheme'];
        $response['info']['show_header'] = $meta['socialdb_collection_show_header'];
        $response['info']['add_item'] = $meta['socialdb_collection_add_item'];
        $response['info']['vinculated_items'] = $meta['socialdb_collection_vinculated_object'];
        $response['info']['moderators'] =  $meta['socialdb_collection_moderator'];
        $response['info']['channels'] = $meta['socialdb_collection_channel'];
        $response['info']['color_scheme'] = ( $meta['socialdb_collection_color_scheme'] != '') ? unserialize( $meta['socialdb_collection_color_scheme']) : false;
        $response['info']['slideshow_time'] =  $meta['socialdb_collection_slideshow_time'];
        $response['info']['use_prox_mode'] =  $meta['socialdb_collection_use_prox_mode'];
        $response['info']['latidude_meta'] = $meta['socialdb_collection_latitude_meta'];
        $response['info']['longitude_meta'] = $meta['socialdb_collection_longitude_meta'];
        $response['info']['table_metas'] = ($meta['socialdb_collection_table_metas']) ? unserialize(base64_decode($meta['socialdb_collection_table_metas'])) : false;
        $response['info']['privacity'] = $meta['sociadb_collection_privacity'];
        
        //permissoes
        $response['info']['permissions'] = CollectionsFiltersApi::getPermissions($collection_data);
        
        //filtros
        $response['info']['filters'] = CollectionsFiltersApi::getFilters($collection_data);
        
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