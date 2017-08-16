<?php

abstract class RepositoryApi {

    /**
     * Metodo que retonra os dados do repositorio
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function get_repository($request) {
        $response = [];
        $params = $request->get_params();
        //informacoes gerais
        $response['title'] = get_bloginfo('blogname');
        $response['description'] = get_bloginfo('blogname');
        $response['admin_email'] = get_bloginfo('admin_email');

        // logo do repositorio
        $logo = wp_get_attachment_url(get_option('socialdb_logo'));
        if ($logo) {
            $response['logo'] = $logo;
        }

        // capa repositorio
        $cover = wp_get_attachment_url(get_option('socialdb_repository_cover_id'));
        if ($cover) {
            $response['cover'] = $cover;
        }

        //informacoes completas
        if (isset($params['includeMetadata']) && $params['includeMetadata'] === '1') {
            $response['disable_empty_collection'] = get_option('disable_empty_collection');
            $response['repository_permissions'] = get_option('socialdb_repository_permissions');
            //caso tenha algum plugin que queira alterar esta resposta
            if (has_filter('alter_repository_api_response')) {
                $response = apply_filters('alter_repository_api_response', $response);
            }
        }
        //retorno ja com a classe especifica
        return new WP_REST_Response($response, 200);
    }

    /**
     * 
     * @param /WP_REST_Request $request
     */
    public function get_repository_items($request) {
        $params = $request->get_params();
        $array['filter'] = ( isset($params['filter']) ) ? $params['filter'] : [];
        return RepositoryApi::filterByArgs($array);
    }

    /**
     * 
     * @param /WP_REST_Request $request
     */
    public function get_repository_metadata($request) {
        $wpQueryModel = new WPQueryModel();
        $params = $request->get_params();
        $response = [];

        $properties = get_term_meta($wpQueryModel->get_category_root(), 'socialdb_category_property_id');
        if ($properties && is_array($properties)) {
            foreach ($properties as $property_id) {
                $property = $wpQueryModel->get_all_property($property_id, true);
                $details = [
                    'id' => $property['id'],
                    'name' => $property['name'],
                    'slug' => $property['slug'],
                    'type' => CollectionsApi::getTypeProperty($property),
                ];
                if ($params['includeMetadata'] === '1' && $details['type'] != 'property-default') {
                    $details['metadata'] = CollectionsMetadataApi::includeMetadata($property);
                    $details['visibility'] = ( in_array($property['id'], $params['visibility'])) ? 'off' : 'on';
                } elseif ($details['type'] === 'property-default') {
                    $visibility = (get_term_meta($property['id'], 'socialdb_property_visibility', true));
                    $details['real-name'] = $details['name'];
                    $details['name'] = (isset($params['labels_collection'][$property['id']])) ? $params['labels_collection'][$property['id']] : $details['name'];
                    if ($params['includeMetadata'] === '1') {
                        $details['visibility'] = ($visibility === 'hide' || in_array($property['id'], $params['visibility'])) ? 'off' : 'on';
                        $required = get_post_meta($params['id'], 'socialdb_collection_property_' . $property['id'] . '_required', true);
                        $details['required'] = ($required != '') ? true : false;
                        $is_mask = get_post_meta($params['id'], 'socialdb_collection_property_' . $property['id'] . '_mask_key', true);
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
     * @param string $type O tipo da entidade
     * @param int $id O id da entidade
     * @param string (Optional) $rel O tipo de relacionamento do link
     * @param array (Optional) $args Outros argumentos que podem ser utilizados
     * @return array  O array com o link
     */
    public function getLink($type, $id, $rel = 'self', $args = []) {
        if ($type == 'collection') {
            $href = get_bloginfo('url') . '/wp-json/' . TainacanApi::$namespace_tainacan . TainacanApi::$version . '/collections/' . $id;
        } else if ($type == 'object') {
            $href = get_bloginfo('url') . '/wp-json/' . TainacanApi::$namespace_tainacan . TainacanApi::$version . '/collections/' . $args['collection_id'] . '/items/' . $id;
        }
        return ['rel' => $rel, 'href' => $href];
    }

    // Metodo da classe
    private function filterByArgs($params) {
        $filters = $params['filter'];
        $wpQueryModel = new WPQueryModel();
        $args = $wpQueryModel->queryAPI($filters);
        $loop = new WP_Query($args);
        if ($loop->have_posts()) {
            $data = [];
            //total de itens
            $data['found_items'] = $loop->found_posts;

            //limite
            $data['items_per_page'] = $loop->post_count;

            //page
            $data['page'] = (isset($args['paged'])) ? $args['paged'] : 1;

            while ($loop->have_posts()) : $loop->the_post();
                $array['item'] = CollectionsApi::get_item(get_post()->ID, $params['id']);
                $data['items'][] = $array;
            endwhile;

            return new WP_REST_Response($data, 200);
        }else {
            return new WP_Error('empty_search', __('No items inserted in this repository or found with these arguments!', 'tainacan'), array('status' => 404));
        }
    }

}
