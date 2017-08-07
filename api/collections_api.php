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
        $params = $request->get_params();

        var_dump($params);

        $CollectionModel = new CollectionModel;
        return $CollectionModel->get_collection_posts($params['id']);
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

}
