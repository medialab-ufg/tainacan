<?php

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../property/property_model.php');

class DesignModel extends Model {

    public function create() {
        include(dirname(__FILE__) . '../../../views/design/create.php');
    }

    /**
     * function simple_add($data)
     * @param mix $data  O id do colecao
     * @return json  
     * 
     * Autor: Eduardo Humberto 
     */
    public function add($data) {
        $post = array(
            'post_title' => $data['collection_name'],
            'post_content' => $data['collection_content'],
            'post_status' => 'publish',
            'post_type' => 'ideas'
        );
        $data['ID'] = wp_insert_post($post);
        wp_set_collection_terms($data['ID'], 8, 'category-ideas');
        return json_encode($data);
    }

    /**
     * function simple_add($data)
     * @param mix $data  O id do colecao
     * @return wp_post  
     * Funcao que insere a colecao apenas com o nome e o tipo de objeto
     * Autor: Eduardo Humberto 
     */
    public function simple_add($data) {
        $collection = array(
            'post_type' => 'socialdb_collection',
            'post_title' => $data['collection_name'],
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
        );
        $collection_id = wp_insert_post($collection);
        $post = get_post($collection_id);
        insert_taxonomy($post->ID, 'socialdb_collection', 'socialdb_collection_type', true); // (Criada em: functions.php) insere a categoria que identifica o tipo da colecao
        create_root_collection_category($post->ID, __('Categories of ','tainacan') . $data['collection_object']); //(Criada em: functions.php) cria a categoria inicial que identifica os objetos da colecao
        return $post->ID;
    }

    public function edit($data) {
        $array_json = [];
        $post = get_post($post_id);
        $array_json['collection_name'] = $post->post_title;
        return json_encode($array_json);
    }

    /**
     * function update($data)
     * @param mix $data  Os dados que serao utilizados para atualizar a colecao
     * @return json com os dados atualizados 
     * metodo que atualiza os dados da colecao
     * @author Eduardo Humberto
     */
    public function update($data) {
        $CollectionModel = new CollectionModel;
        $facets = CollectionModel::get_facets($data['collection_id']);
        $property_facets = $CollectionModel->get_property_facets($data['collection_id']);
        $error = 0;
       // foreach ($facets as $facet) {
         //   update_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet . '_color', $data['color_' . $facet]);
        //}
       // if ($property_facets&&  is_array($property_facets)) {
        //    foreach ($property_facets as $property_facet) {
          //      update_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $property_facet['id'] . '_color', $data['color_' . $property_facet['id']]);
          //  }
        //}

        if(!update_post_meta($data['collection_id'], 'socialdb_collection_board_background_color', $data['background_color']))
                $error += 1;
        if(!update_post_meta($data['collection_id'], 'socialdb_collection_board_border_color', $data['border_color']))
                $error += 1;
        if(!update_post_meta($data['collection_id'], 'socialdb_collection_board_font_color', $data['font_color']))
                $error += 1;
        if(!update_post_meta($data['collection_id'], 'socialdb_collection_board_link_color', $data['link_color']))
                $error += 1;
        if(!update_post_meta($data['collection_id'], 'socialdb_collection_board_skin_mode', $data['BoardSkinOptions']))
                $error += 1;
        if(!update_post_meta($data['collection_id'], 'socialdb_collection_hide_title', $data['HideOptions_Title']))
                $error += 1;
        if(!update_post_meta($data['collection_id'], 'socialdb_collection_hide_description', $data['HideOptions_Descriprion']))
                $error += 1;
        if(!update_post_meta($data['collection_id'], 'socialdb_collection_hide_thumbnail', $data['HideOptions_Thumb']))
                $error += 1;
        if(!update_post_meta($data['collection_id'], 'socialdb_collection_hide_menu', $data['HideOptions_Menu']))
                $error += 1;
        if(!update_post_meta($data['collection_id'], 'socialdb_collection_hide_categories', $data['HideOptions_Category']))
                $error += 1;
        
        if(!update_post_meta($data['collection_id'], 'socialdb_collection_hide_rankings', $data['HideOptions_Rankings']))
                $error += 1;
        
        if(!update_post_meta($data['collection_id'], 'socialdb_collection_columns', $data['select_qtd_columns']))
                $error += 1;
        if(!update_post_meta($data['collection_id'], 'socialdb_collection_size_thumbnail', $data['thumb_size']))
                $error += 1;
        
        if($error > 0){
            $data['success'] = 'true';
        }
        else{
            $data['success'] = 'false';
        }

        return json_encode($data);
    }

    public function list_collection($args = null) {
        global $wp_query;
        $tax_query = array('relation' => 'IN');
        $tax_query[] = array(
            'taxonomy' => 'category-ideas',
            'field' => 'id',
            'terms' => array('8')
        );
        $args = array(
            'post_type' => 'ideas',
            'paged' => 1,
            'tax_query' => $tax_query,
            'orderby' => 'date',
            'order' => 'DESC',
        );
        query_posts($args);

        include(dirname(__FILE__) . '../../../views/design/list.php');
    }

    public function delete($data) {
        wp_delete_post($data['ID']);
        return json_encode($data);
    }

    /* function initDynatree() */
    /* receive ((array) data) */
    /* inite the div dynatree in the template index */
    /* Author: Eduardo */

    public function initDynatree($data) {
        $facets_id = array_filter(array_unique(get_post_meta($data['collection_id'], 'socialdb_collection_facets')));
        foreach ($facets_id as &$facet_id) {
            $facet = get_term_by('id', $facet_id, 'socialdb_category_type');
            if ($facet) {
                $dynatree[] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id . '#category', 'isLazy' => true, 'data' => $url, 'isFolder' => true, 'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
                $dynatree[end(array_keys($dynatree))] = $this->getChildrenDynatree($facet->term_id, $dynatree[end(array_keys($dynatree))]);
            }
        }
        return json_encode($dynatree);
    }

    /* function getChildrenDynatree() */
    /* receive ((int,string) id,(array) dynatree) */
    /* Return the children of the facets and insert in the array of the dynatree */
    /* Author: Eduardo */

    public function getChildrenDynatree($facet_id, $dynatree) {
        $children = $this->getChildren($facet_id);
        if (count($children) > 0) {
            foreach ($children as $child) {
                $children_of_child = $this->getChildren($child->term_id);
                ;
                if (count($children_of_child) > 0 || (!empty($children_of_child) && $children_of_child)) {
                    $dynatree['children'][] = array('title' => $child->name, 'key' => $child->term_id . "#category", 'isLazy' => true);
                } else {
                    $dynatree['children'][] = array('title' => $child->name, 'key' => $child->term_id . "#category");
                }
            }
        }
        return $dynatree;
    }

    /* function getChildren() */
    /* receive ((int,string) parent) */
    /* Return the children of the especif parent */
    /* Author: Eduardo */

    public function getChildren($parent) {
        global $wpdb;
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $wp_terms = $wpdb->prefix . "terms";
        $wp_taxonomymeta = $wpdb->prefix . "termmeta";
        $query = "
			SELECT * FROM $wp_terms t
			INNER JOIN $wp_term_taxonomy tt ON t.term_id = tt.term_id
				WHERE tt.parent = {$parent} ORDER BY tt.count DESC,t.name ASC  
		";
        return $wpdb->get_results($query);
    }

    /**
     * function get_category_root($collection_id)
     * @param int $collection_id
     * @return int With O term_id da categoria root da colecao.
     * 
     * metodo responsavel em retornar a categoria root da colecao
     * Autor: Eduardo Humberto 
     */
    public function get_category_root($collection_id) {
        return get_post_meta($collection_id, 'socialdb_collection_object_type', true);
    }

}
