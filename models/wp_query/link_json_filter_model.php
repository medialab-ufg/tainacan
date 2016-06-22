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
class LinkJsonModel extends Model {

    /**
     * function get_link_json()
     * @param array $data Os dados vindo da requisicao ajax (collection_id,wp_query_args)
     * @return void 
     * Metodo reponsavel em determinar se deve listar as colecoes ou objetos
     * Autor: Eduardo Humberto 
     */
    public function get_link_json($data) {
        $recover_data = unserialize(stripslashes($data['wp_query_args']));
        unset($recover_data['properties_object_tree']);
        unset($recover_data['properties_data_tree']);
        unset($recover_data['properties_data_link']);
        unset($recover_data['license_tree']);
        unset($recover_data['type_tree']);
        unset($recover_data['format_tree']);
        unset($recover_data['source_tree']);
        unset($recover_data['advanced_search']);
        unset($recover_data['facets']);
        unset($recover_data['pagid']);
        unset($recover_data['properties_data_fromto_date']);
        unset($recover_data['properties_data_fromto_numeric']);
        unset($recover_data['properties_data_range_numeric']);
        unset($recover_data['properties_data_range_date']);
        unset($recover_data['properties_data_range_numeric']);
        unset($recover_data['properties_multipleselect']);
        unset($recover_data['keyword']);
        unset($recover_data['order_by']);
        unset($recover_data['order']);
        unset($recover_data['orderby']);
        unset($recover_data['ordenation_id']);
        unset($recover_data['tags']);
        return $recover_data;
    }
}
