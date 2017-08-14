<?php

abstract class RepositoryApi {


    public function get_repository_items($request) {
        $items = [];
        $params = $request->get_params();

        //se NAO existir consultas
        if(!isset($params['filter']))
            $params['filter'] = [];

        return RepositoryApi::filterByArgs($params);
    }

    // Metodos da classe
    private function filterByArgs($params){
        $filters = $params['filter'];
        $wpQueryModel = new WPQueryModel();
        $args = $wpQueryModel->queryAPI($filters);
        $loop = new WP_Query($args);
        if ($loop->have_posts()) {
            $data = [];
            while ( $loop->have_posts() ) : $loop->the_post();
                $array['item'] = CollectionsApi::get_item( get_post()->ID,$params['id'] );
                $data[] = $array;
            endwhile;
            return new WP_REST_Response( $data, 200 );
        }else{
            return new WP_Error('empty_search',  __( 'No items inserted in this repository or found with these arguments!', 'tainacan' ), array('status' => 404));
        }
    }
    
    /**
     * 
     * @param WP_REST_Request $request
     */
    public function get_repository_metadata($request) {
        $wpQueryModel = new WPQueryModel();
        $params = $request->get_params();
        $response = [];
        
        $properties = get_term_meta($wpQueryModel->get_category_root(), 'socialdb_category_property_id');
        if($properties && is_array($properties)){
            foreach ($properties as $property_id) {
                $property = $wpQueryModel->get_all_property($property_id, true );
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
}
