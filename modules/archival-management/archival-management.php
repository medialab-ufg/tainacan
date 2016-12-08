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

/****************************** Category edit *********************************/
add_action('description_category_view', 'hide_field');
/******************************************************************************/
function hide_field() {
    echo 'style="display:none;"';                          
}

/********** Adicionando os metadados nos eventos de CRIACAO DE TERMO ****/
add_action( 'add_new_metas_event_create_category', 'archival_add_new_metas_event_create_category', 10, 1 );
function archival_add_new_metas_event_create_category($event_create_term) {
    create_metas($event_create_term['term_id'], 'socialdb_event_term_create_metas', 'socialdb_event_term_current_phase_year', 'socialdb_event_term_current_phase_year');
    create_metas($event_create_term['term_id'], 'socialdb_event_term_create_metas', 'socialdb_event_term_current_phase_month', 'socialdb_event_term_current_phase_month');
    create_metas($event_create_term['term_id'], 'socialdb_event_term_create_metas', 'socialdb_event_term_current_phase_string', 'socialdb_event_term_current_phase_string');
    create_metas($event_create_term['term_id'], 'socialdb_event_term_create_metas', 'socialdb_event_term_intermediate_phase_year', 'socialdb_event_term_intermediate_phase_year');
    create_metas($event_create_term['term_id'], 'socialdb_event_term_create_metas', 'socialdb_event_term_intermediate_phase_month', 'socialdb_event_term_intermediate_phase_month');
    create_metas($event_create_term['term_id'], 'socialdb_event_term_create_metas', 'socialdb_event_term_destination', 'socialdb_event_term_destination');
    create_metas($event_create_term['term_id'], 'socialdb_event_term_create_metas', 'socialdb_event_term_classification_code', 'socialdb_event_term_classification_code');
    create_metas($event_create_term['term_id'], 'socialdb_event_term_create_metas', 'socialdb_event_term_observation', 'socialdb_event_term_observation');
}
/******************************************************************************/
/********** Adicionando os metadados nos eventos de EDICAO DE TERMO ****/
add_action( 'add_new_metas_event_edit_category', 'archival_add_new_metas_event_edit_category', 10, 1 );
function archival_add_new_metas_event_edit_category($event_edit_term) {
    create_metas($event_edit_term['term_id'], 'socialdb_event_term_edit_metas', 'socialdb_event_term_current_phase_year', 'socialdb_event_term_current_phase_year');
    create_metas($event_edit_term['term_id'], 'socialdb_event_term_edit_metas', 'socialdb_event_term_current_phase_month', 'socialdb_event_term_current_phase_month');
    create_metas($event_edit_term['term_id'], 'socialdb_event_term_edit_metas', 'socialdb_event_term_current_phase_string', 'socialdb_event_term_current_phase_string');
    create_metas($event_edit_term['term_id'], 'socialdb_event_term_edit_metas', 'socialdb_event_term_intermediate_phase_year', 'socialdb_event_term_intermediate_phase_year');
    create_metas($event_edit_term['term_id'], 'socialdb_event_term_edit_metas', 'socialdb_event_term_intermediate_phase_month', 'socialdb_event_term_intermediate_phase_month');
    create_metas($event_edit_term['term_id'], 'socialdb_event_term_edit_metas', 'socialdb_event_term_destination', 'socialdb_event_term_destination');
    create_metas($event_edit_term['term_id'], 'socialdb_event_term_edit_metas', 'socialdb_event_term_classification_code', 'socialdb_event_term_classification_code');
    create_metas($event_edit_term['term_id'], 'socialdb_event_term_edit_metas', 'socialdb_event_term_observation', 'socialdb_event_term_observation');
}
/******************************************************************************/
/**
 * Adicionando os campos no modal de EDICAO de categoria 
 * @uses single.php 
 */
add_action('insert_fields_edit_modal_category', 'archival_insert_fields_edit_modal_category');
function archival_insert_fields_edit_modal_category() {
    ?>
    <div  style="padding-bottom: 3px;">
        <a onclick="toggleSlide('archival_categories_fields','form_simple_eidt_category');" style="cursor: pointer;">
                <span><?php _e('Advanced Options','tainacan')  ?></span> 
                <span class="glyphicon glyphicon-triangle-bottom"></span>
        </a>
    </div>
    <div id="archival_categories_fields" style="display: none;">
         <!------------ Modo GESTAO ARQUIVISTICA ----------------->
                <div class="form-inline form-group">
                    <div id="current_phase_number">
                        <label for="current_phase"><?php _e('Current Phase', 'tainacan'); ?></label><br>
                        <input onchange="handleChange(this);" type="number" class="form-control input-sm" id="current_phase_year" placeholder="<?php _e('Year(s)', 'tainacan'); ?>" name="socialdb_event_term_current_phase_year" >
                        <input onchange="handleChange(this);" type="number" class="form-control input-sm" id="current_phase_month" placeholder="<?php _e('Month(s)', 'tainacan'); ?>" name="socialdb_event_term_current_phase_month" >
                    </div>
                    <div id="current_phase_text" class="form-inline form-group" style="display: none;">
                        <label for="current_phase"><?php _e('Current Phase', 'tainacan'); ?></label><br>
                        <input type="text" class="form-control" id="current_phase_string" name="socialdb_event_term_current_phase_string" >
                    </div>
                    <br>
                    <?php _t('Explicate note',true) ?>
                    <input type="checkbox" 
                           id="current_phase_checkbox" 
                           value="true" 
                           onchange="if($(this).is(':checked')){ toggleSlide('current_phase_text','current_phase_number');$('.form-control .input-sm').val('');}else{ toggleSlide('current_phase_number','current_phase_text'); $('#current_phase_string').val('');} ">
                </div>
                <div class="form-inline form-group">
                    <label for="intermediate_phase"><?php _e('Intermediate Phase', 'tainacan'); ?></label><br>
                    <input onchange="handleChange(this);" class="form-control input-sm" type="number" id="intermediate_phase_year" placeholder="<?php _e('Year(s)', 'tainacan'); ?>" name="socialdb_event_term_intermediate_phase_year" >
                    <input onchange="handleChange(this);" class="form-control input-sm" type="number" id="intermediate_phase_month" placeholder="<?php _e('Month(s)', 'tainacan'); ?>" name="socialdb_event_term_intermediate_phase_month" >
                </div>
                <div class="form-inline form-group">
                    <label for="destination"><?php _e('Destination', 'tainacan'); ?></label><br>
                    <input class="form-control" type="radio" id="destination_permanent_guard" value="permanent_guard" name="socialdb_event_term_destination" >&nbsp;<?php _e('Permanent guard', 'tainacan') ?>
                    <input class="form-control" type="radio" id="destination_elimination" value="elimination" name="socialdb_event_term_destination" >&nbsp;<?php _e('Elimination', 'tainacan') ?>
                </div>
                <div class="form-group">
                    <label for="classification_code"><?php _e('Classification Code', 'tainacan'); ?></label>
                    <input type="text" class="form-control" id="classification_code" placeholder="<?php _e('Type here the category classification code', 'tainacan'); ?>" name="socialdb_event_term_classification_code" >
                </div>
                <div class="form-group">
                    <label for="observation"><?php _e('Observation', 'tainacan'); ?></label>
                    <textarea id="observation" class="form-control" name="socialdb_event_term_observation"></textarea>
                </div>
    </div>    
    <br>  
    <?php    
}
/**** Adicionando o pedaco de script para retornar os dados ja cadastrados ****/
/**
 * @uses single.php 
 */
add_action('after_event_edit_term', 'archival_after_event_edit_term', 10, 1);
function archival_after_event_edit_term($event_id) {
    $category_id = get_post_meta($event_id, 'socialdb_event_term_id',true) ;
    if(strpos($category_id, '_facet_category')!==false){
             $category_id = str_replace('_facet_category', '', $category_id);
             
    }
    $description = get_post_meta($event_id, 'socialdb_event_term_observation',true) ;
    $update_category = wp_update_term($category_id, 'socialdb_category_type', array(
       'description' => $description));
    $current_phase = 0;
    $intermediate_phase = 0;
    //current_phase_year
    $current_phase_string = get_post_meta($event_id, 'socialdb_event_term_current_phase_string',true) ;
    if($current_phase_string){
        $current_phase = $current_phase_string;
    }else{
        $current_phase_year = get_post_meta($event_id, 'socialdb_event_term_current_phase_year',true) ;
        if ($current_phase_year) {
            $current_phase = intval(trim($current_phase_year)) * 12;
        }
        //month
        $current_phase_month = get_post_meta($event_id, 'socialdb_event_term_current_phase_month',true) ;
        if ($current_phase_month) {
            $current_phase += intval(trim($current_phase_month));
        }
    }
    update_term_meta($category_id, "socialdb_category_current_phase", $current_phase);
    //year
    $intermediate_phase_year = get_post_meta($event_id, 'socialdb_event_term_intermediate_phase_year',true) ;
    if ($intermediate_phase_year) {
        $intermediate_phase = intval(trim($intermediate_phase_year)) * 12;
    }
    //month
    $intermediate_phase_month = get_post_meta($event_id, 'socialdb_event_term_intermediate_phase_month',true) ;
    if ($intermediate_phase_month) {
        $intermediate_phase += intval(trim($intermediate_phase_month));
    }
    $destination = get_post_meta($event_id, 'socialdb_event_term_destination',true) ;
    $code = get_post_meta($event_id, 'socialdb_event_term_classification_code',true) ;
    update_term_meta($category_id, "socialdb_category_intermediate_phase", $intermediate_phase);
    update_term_meta($category_id, "socialdb_category_destination", $destination);
    update_term_meta($category_id, "socialdb_category_classification_code", $code);    
}
######################## FIM:INSERCAO DE AXIOMAS ###############################
/**** Adicionando o pedaco de script para retornar os dados ja cadastrados ****/
add_action('javascript_metas_category', 'ontology_javascript_metas_category');
function ontology_javascript_metas_category() {
    echo 
    'clear_archival_fields_category();'
    . 'set_fields_modal_categories(elem);';
}
/******************************************************************************/
############################# #2 CRIANDO UMA COLECAO ###########################
function archival_category_root($collection_id, WP_Term $category_root) {
        $parent_taxonomy_category_id = get_register_id('socialdb_taxonomy', 'socialdb_category_type');
        $category_subject_root_id = create_register(__('Classification plan of', 'tainacan').' '.get_post($collection_id)->post_title, 'socialdb_category_type', array('parent' => $parent_taxonomy_category_id, 'slug' => 'subject_category_collection_'.$collection_id));
        $category_subject_root_id = get_term_by('id', $category_subject_root_id['term_id'], 'socialdb_category_type');
        add_term_meta($category_subject_root_id->term_id, 'socialdb_category_owner', get_current_user_id());
        //adiciono a categoria root como faceta da colecao
        update_post_meta($collection_id, 'socialdb_collection_facets', $category_subject_root_id->term_id);
        update_post_meta($collection_id, 'socialdb_collection_facet_' . $category_subject_root_id->term_id . '_color', 'color1');
        update_post_meta($collection_id, 'socialdb_collection_facet_' . $category_subject_root_id->term_id . '_widget', 'tree');
        update_post_meta($collection_id, 'socialdb_collection_facet_' . $category_subject_root_id->term_id . '_priority', '1');
        create_initial_property($category_subject_root_id->term_id, $collection_id,$category_root);
}
add_filter( 'create_root_category', 'archival_category_root', 10, 3 );