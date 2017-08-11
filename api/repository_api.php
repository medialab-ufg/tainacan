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


}
