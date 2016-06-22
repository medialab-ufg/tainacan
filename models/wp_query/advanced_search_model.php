<?php

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once (dirname(__FILE__) . '../../../models/collection/collection_model.php');
include_once (dirname(__FILE__) . '../../../models/license/license_model.php');
include_once (dirname(__FILE__) . '../../../models/property/property_model.php');
include_once (dirname(__FILE__) . '../../../models/category/category_model.php');
include_once (dirname(__FILE__) . '../../../models/event/event_object/event_object_create_model.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../user/user_model.php');
require_once(dirname(__FILE__) . '../../tag/tag_model.php');

/**
 * The class ObjectModel
 *
 */
class AdvancedSearchModel extends Model {

    /**
     * function set_post_type()
     * @param int 
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function get_data_wpquery($loop) {
        $data = [];
        $data['total_items'] = $loop->found_posts;
        if ($loop->have_posts()) :
             while ($loop->have_posts()) : $loop->the_post(); 
                $array = [];
                $terms =  wp_get_object_terms(get_the_ID(), 'socialdb_category_type');
                if($terms&& is_array($terms)){
                    foreach ($terms as $term) {
                        if($this->verify_collection_category_root($term->term_id)){
                            $array['collection_id'] = $this->get_collection_category($term->term_id);
                            $array['collection_name'] = get_post($array['collection_id'])->post_title;
                            $array['link'] = get_the_permalink($array['collection_id']).'?item='.get_post(get_the_ID())->post_name;                            
                        }
                    }
                }
                $data[get_the_ID()] = $array;
             endwhile;
        endif;
        return $data;
    }

   
}
