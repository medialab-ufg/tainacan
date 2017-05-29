<?php

ini_set('max_input_vars', '10000');
include_once (dirname(__FILE__) . '/../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');

class HelpersAPIModel extends Model {
    
    
     public static function createCollection($post) {
         $collection = array(
            'post_type' => 'socialdb_collection',
            'post_title' => $post['title'],
            'post_status' => 'publish',
            'post_name' => $post['name'],
            'post_content' => $post['content'],
            'post_author' => get_current_user_id(),
        );
        $collection_id = wp_insert_post($collection);
        return $collection_id;
     }
    
    /**
     * 
     * @param type $post
     * @param type $metas
     * @param type $idBlog
     * @return type
     */
    public static function updateCollection($post,$metas,$idBlog = false) {
        $collection = array(
            'post_type' => 'socialdb_collection',
            'post_title' => (string) $xml->post_title,
            'post_status' => 'publish',
            'post_name' => (string) $xml->post_name,
            'post_content' => (string) $xml->post_content,
            'post_author' => get_current_user_id(),
        );
        $collection_id = wp_insert_post($collection);
        //
        $this->createSocialMappingDefault($collection_id);
        //categoria raiz da colecao
        $socialdb_collection_object_type = $this->get_term_imported_id((string) $xml->socialdb_collection_object_type); //init
        //privacidade
        $type = get_term_by('name', (string) $xml->parent, 'socialdb_collection_type');
        wp_set_post_terms($collection_id, array($type->term_id), 'socialdb_collection_type');
        //metas
        update_post_meta($collection_id, 'socialdb_collection_object_type', $socialdb_collection_object_type);
        update_post_meta($collection_id, 'socialdb_collection_hide_tags', (string) $xml->socialdb_collection_hide_tags);
        update_post_meta($collection_id, 'socialdb_collection_attachment', (string) $xml->socialdb_collection_attachment);
        update_post_meta($collection_id, 'socialdb_collection_allow_hierarchy', (string) $xml->socialdb_collection_allow_hierarchy);
        update_post_meta($collection_id, 'socialdb_collection_ordenation_form', (string) $xml->socialdb_collection_ordenation_form);
        update_post_meta($collection_id, 'socialdb_collection_facet_widget_tree_orientation', (string) $xml->socialdb_collection_facet_widget_tree_orientation);
        update_post_meta($collection_id, 'socialdb_collection_board_background_color', (string) $xml->socialdb_collection_board_background_color);
        update_post_meta($collection_id, 'socialdb_collection_board_border_color', (string) $xml->socialdb_collection_board_border_color);
        update_post_meta($collection_id, 'socialdb_collection_board_font_color', (string) $xml->socialdb_collection_board_font_color);
        update_post_meta($collection_id, 'socialdb_collection_board_link_color', (string) $xml->socialdb_collection_board_link_color);
        update_post_meta($collection_id, 'socialdb_collection_board_skin_mode', (string) $xml->socialdb_collection_board_skin_mode);
        update_post_meta($collection_id, 'socialdb_collection_hide_title', (string) $xml->socialdb_collection_hide_title);
        update_post_meta($collection_id, 'socialdb_collection_hide_description', (string) $xml->socialdb_collection_hide_description);
        update_post_meta($collection_id, 'socialdb_collection_hide_thumbnail', (string) $xml->socialdb_collection_hide_thumbnail);
        update_post_meta($collection_id, 'socialdb_collection_hide_menu', (string) $xml->socialdb_collection_hide_menu);
        update_post_meta($collection_id, 'socialdb_collection_hide_categories', (string) $xml->socialdb_collection_hide_categories);
        update_post_meta($collection_id, 'socialdb_collection_hide_rankings', (string) $xml->socialdb_collection_hide_rankings);
        update_post_meta($collection_id, 'socialdb_collection_columns', (string) $xml->socialdb_collection_columns);
        update_post_meta($collection_id, 'socialdb_collection_size_thumbnail', (string) $xml->socialdb_collection_size_thumbnail);
        update_post_meta($collection_id, 'socialdb_collection_submission_visualization', (string) $xml->socialdb_collection_submission_visualization);
        //permissions
        update_post_meta($collection_id, 'socialdb_collection_permission_create_category', (string) $xml->permissions->socialdb_collection_permission_create_category);
        update_post_meta($collection_id, 'socialdb_collection_permission_edit_category', (string) $xml->permissions->socialdb_collection_permission_edit_category);
        update_post_meta($collection_id, 'socialdb_collection_permission_delete_category', (string) $xml->permissions->socialdb_collection_permission_delete_category);
        update_post_meta($collection_id, 'socialdb_collection_permission_add_classification', (string) $xml->permissions->socialdb_collection_permission_add_classification);
        update_post_meta($collection_id, 'socialdb_collection_permission_delete_classification', (string) $xml->permissions->socialdb_collection_permission_delete_classification);
        update_post_meta($collection_id, 'socialdb_collection_permission_create_object', (string) $xml->permissions->socialdb_collection_permission_create_object);
        update_post_meta($collection_id, 'socialdb_collection_permission_delete_object', (string) $xml->permissions->socialdb_collection_permission_delete_object);
        update_post_meta($collection_id, 'socialdb_collection_permission_create_property_data', (string) $xml->permissions->socialdb_collection_permission_create_property_data);
        update_post_meta($collection_id, 'socialdb_collection_permission_edit_property_data', (string) $xml->permissions->socialdb_collection_permission_edit_property_data);
        update_post_meta($collection_id, 'socialdb_collection_permission_delete_property_data', (string) $xml->permissions->socialdb_collection_permission_delete_property_data);
        update_post_meta($collection_id, 'socialdb_collection_permission_edit_property_data_value', (string) $xml->permissions->socialdb_collection_permission_edit_property_data_value);
        update_post_meta($collection_id, 'socialdb_collection_permission_create_property_object', (string) $xml->permissions->socialdb_collection_permission_create_property_object);
        update_post_meta($collection_id, 'socialdb_collection_permission_edit_property_object', (string) $xml->permissions->socialdb_collection_permission_edit_property_object);
        update_post_meta($collection_id, 'socialdb_collection_permission_delete_property_object', (string) $xml->permissions->socialdb_collection_permission_delete_property_object);
        update_post_meta($collection_id, 'socialdb_collection_permission_edit_property_object_value', (string) $xml->permissions->socialdb_collection_permission_edit_property_object_value);
        update_post_meta($collection_id, 'socialdb_collection_permission_create_comment', (string) $xml->permissions->socialdb_collection_permission_create_comment);
        update_post_meta($collection_id, 'socialdb_collection_permission_edit_comment', (string) $xml->permissions->socialdb_collection_permission_edit_comment);
        update_post_meta($collection_id, 'socialdb_collection_permission_delete_comment', (string) $xml->permissions->socialdb_collection_permission_delete_comment);
        update_post_meta($collection_id, 'socialdb_collection_permission_create_tags', (string) $xml->permissions->socialdb_collection_permission_delete_comment);
        update_post_meta($collection_id, 'socialdb_collection_permission_edit_tags', (string) $xml->permissions->socialdb_collection_permission_edit_tags);
        update_post_meta($collection_id, 'socialdb_collection_permission_delete_tags', (string) $xml->permissions->socialdb_collection_permission_delete_tags);
        update_post_meta($collection_id, 'socialdb_collection_permission_create_property_term', (string) $xml->permissions->socialdb_collection_permission_create_property_term);
        update_post_meta($collection_id, 'socialdb_collection_permission_edit_property_term', (string) $xml->permissions->socialdb_collection_permission_edit_property_term);
        update_post_meta($collection_id, 'socialdb_collection_permission_delete_property_term', (string) $xml->permissions->socialdb_collection_permission_delete_property_term);
        //tab defaullt
        if ($xml->socialdb_collection_default_tab)
            update_post_meta($collection_id, 'socialdb_collection_default_tab', (string) $xml->socialdb_collection_default_tab);
        if ($xml->socialdb_collection_update_tab_organization)
            update_post_meta($collection_id, 'socialdb_collection_update_tab_organization', (string) $xml->socialdb_collection_update_tab_organization);
        // properties
        $properties = $this->insert_properties($xml, $socialdb_collection_object_type, $collection_id);
        // tabs
        $tabs = $this->insertTabs($xml, $collection_id);
        $this->updateTabOrganization($collection_id, $tabs);
        //facets
        $this->add_facets($xml, $collection_id);
        //channels
        $this->add_channels($xml, $collection_id);
        //ordenation
        $socialdb_collection_default_ordering = $this->get_term_imported_id((string) $xml->socialdb_collection_default_ordering); //after all
        update_post_meta($collection_id, 'socialdb_collection_default_ordering', $socialdb_collection_default_ordering);
        $socialdb_collection_mapping_exportation_active = $this->get_post_imported_id((string) $xml->socialdb_collection_mapping_exportation_active); //after all
        update_post_meta($collection_id, 'socialdb_collection_mapping_exportation_active', $socialdb_collection_mapping_exportation_active);
        //items            
        if (is_dir($dir_created . '/package/items')) {
            session_write_close();
            ini_set('max_execution_time', '0');
            $this->import_items($dir_created . '/package/items', $collection_id);
        }

        //retiro o id transitorio
        if ($properties) {
            foreach ($properties as $property) {
                delete_term_meta($property, 'socialdb_imported_id');
            }
        }
        return $collection_id;
    }
    
    /**
     * 
     * @param type $post
     * @return type
     */
    public static function createCategory($category,$metas,$parent_id = false) {
        $array = wp_insert_term($category['name'], 'socialdb_category_type', array('parent' => (!$parent) ? get_term_by('slug', 'socialdb_taxonomy', 'socialdb_category_type')->term_id : $parent_id,
            'slug' => $this->generate_slug(trim($category['name']), 0)));
        add_term_meta($array['term_id'], 'socialdb_category_owner', get_current_user_id());
        return $array['term_id'];
    }
    
    
    public static function updateCategory($id,$category,$metas) {
        $array = wp_update_term($id, 'socialdb_category_type', array(
            'name' => $category['name']));
        return $array['term_id'];
    }

}
