<?php

abstract class CollectionsApi {

    public function get_collections() {
        $CollectionModel = new CollectionModel;
        return $CollectionModel->get_all_collections();
    }

}
