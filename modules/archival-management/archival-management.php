<?php

/*
 * Modulo de gerenciamento arquivistico
 */
define('MODULE', 'archival-management');
// Adiciono o menu para ir para a view para o gerenciamento de categorias
add_action('add_configuration_menu_tainacan', 'add_archival_management_menu');
load_theme_textdomain("tainacan", dirname(__FILE__) . "/languages");

function add_archival_management_menu() {
    $link = "'" . get_template_directory_uri() . '/modules/' . MODULE . "'";
    echo '<li>
                           <a style="cursor: pointer;" 
                              onclick="showArchivalManagement(' . $link . ');" >
                               <span class="glyphicon glyphicon-folder-open"></span>&nbsp;
                                   ' . __('Archival Management', 'tainacan')
    . '</a>'
    . '</li>';
}

/*
 * Adicinando os metadados default diretamente na categoria raiz
 */
add_action( 'add_new_metas_category', 'add_metas_and_defaults_properties', 10, 1 );
function add_metas_and_defaults_properties($category_root_term) {
    create_metas($category_root_term['term_id'], 'socialdb_category_metas', 'socialdb_category_current_phase', 'socialdb_category_current_phase');
    create_metas($category_root_term['term_id'], 'socialdb_category_metas', 'socialdb_category_intermediate_phase', 'socialdb_category_intermediate_phase');
    create_metas($category_root_term['term_id'], 'socialdb_category_metas', 'socialdb_category_destination', 'socialdb_category_destination');
    create_metas($category_root_term['term_id'], 'socialdb_category_metas', 'socialdb_category_classification_code', 'socialdb_category_classification_code');
    if (!get_term_by('slug', 'creation_date_repository_property', 'socialdb_property_type') && !get_term_by('slug', 'status_repository_property', 'socialdb_property_type')) {
        //data de criacao
        $new_property = wp_insert_term(__('Creation Date', 'tainacan'), 'socialdb_property_type', array('parent' => get_term_by('name', 'socialdb_property_data', 'socialdb_property_type')->term_id,
            'slug' => "creation_date_repository_property"));
        $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_required', 'true');
        $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_data_widget', 'date');
        $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_data_column_ordenation', 'false');
        update_term_meta($new_property['term_id'], 'socialdb_property_created_category', $category_root_term['term_id']); // adiciono a categoria de onde partiu esta propriedade
        add_term_meta($category_root_term['term_id'], 'socialdb_category_property_id', $new_property['term_id']);
        //status
        $new_property = wp_insert_term(__('Status', 'tainacan'), 'socialdb_property_type', array('parent' => get_term_by('name', 'socialdb_property_data', 'socialdb_property_type')->term_id,
            'slug' => "status_repository_property"));
        $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_required', 'true');
        $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_data_widget', 'radio');
        $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_data_column_ordenation', 'false');
        update_term_meta($new_property['term_id'], 'socialdb_property_created_category', $category_root_term['term_id']); // adiciono a categoria de onde partiu esta propriedade
        add_term_meta($category_root_term['term_id'], 'socialdb_category_property_id', $new_property['term_id']);
    } else {
        add_mode_archive_properties($category_root_term);
    }
}

/**
 * 
 * @param type $category_root_term O id da categoria raiz do repositorio
 */
function add_mode_archive_properties($category_root_term) {
    $types = [
        'creation_date_repository_property',
        'status_repository_property'];
    $metas = get_term_meta($category_root_term['term_id'], 'socialdb_category_property_id');
    foreach ($types as $type) {
        $property = get_term_by('slug', $type, 'socialdb_property_type');
        if (!in_array($property->term_id, $metas)) {
            add_term_meta($category_root_term['term_id'], 'socialdb_category_property_id', $property->term_id);
        }
    }
}

/*
 * Adicionando scripts deste modulo
 */
add_action('wp_enqueue_scripts', 'archival_management_js');

function archival_management_js() {
    wp_register_script('archival_management_js', get_template_directory_uri() . '/modules/' . MODULE . '/libraries/js/archival_management_js.js', array('jquery'), '1.11');
    $js_files = ['archival_management_js'];
    foreach ($js_files as $js_file):
        wp_enqueue_script($js_file);
    endforeach;
}
