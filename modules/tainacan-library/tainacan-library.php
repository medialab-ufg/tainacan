<?php
/**
 * Tainacan Biblioteca
 */

define('MODULE_LIBRARY', 'tainacan-library');
define('LIBRARY_CONTROLLERS', get_template_directory_uri() . '/modules/' . MODULE_LIBRARY );
load_theme_textdomain("tainacan", dirname(__FILE__) . "/languages");

/*
 * Adição de SCRIPTS
 */

//JavaScript
add_action('wp_enqueue_scripts', 'tainacan_libraries_js');
function tainacan_libraries_js() {
    wp_register_script('tainacan-library',
        get_template_directory_uri() . '/modules/' . MODULE_LIBRARY . '/libraries/js/tainacan-library.js', array('jquery'), '1.11');
    $js_files = ['tainacan-library'];
    foreach ($js_files as $js_file):
        wp_enqueue_script($js_file);
    endforeach;
}

//CSS
add_action('wp_enqueue_scripts', 'tainacan_libraries_css');
function tainacan_libraries_css() {
    $registered_css = [
        'tainacan-library' => '/libraries/css/tainacan-library.css'
    ];
    foreach ($registered_css as $css_file => $css_path) {
        wp_register_style($css_file, get_template_directory_uri() . '/modules/' . MODULE_LIBRARY  . $css_path);
        wp_enqueue_style($css_file);
    }
}

/*
 * FILTERS
 */
add_filter('addLibraryMenu', 'addLibraryMenu');
function addLibraryMenu()
{
    ?>
    <div class="btn-group" role="group" aria-label="...">
        <div class="btn-group tainacan-add-wrapper">
            <button type="button" class="btn btn-primary dropdown-toggle sec-color-bg" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php _e('Add', 'tainacan') ?> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a onclick="showAddItemText()"  style="cursor: pointer;"><?php _e('Item', 'tainacan') ?> </a></li>
                <li><a onclick="showModalImportMarc()" style="cursor: pointer;" id="addfrommarc" ><?php _e('Add from MARC', 'tainacan') ?>  </a></li>
            </ul>
        </div>
    </div>
    <?php
}

add_action('add_new_modals', 'add_new_modals_libraries');
function add_new_modals_libraries() {
    ?>
        <div class="modal fade" id="modalImportMarc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header"><!--Cabeçalho-->
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only"><?php _e('Close', 'tainacan'); ?></span>
                        </button>

                        <h4 class="modal-title"><?php _e('Import MARC', 'tainacan'); ?></h4>
                    </div><!--Fim cabeçalho-->

                    <div class="modal-body">
                        <div class="form-group" id="formmarc">
                            <form>
                                <label for="MARC">MARC:</label>
                                <textarea class="form-control" rows="8" id="textmarc" name="textmarc"></textarea>

                                <label for="sendfile"><?php _e('Send file', 'tainacan'); ?>: </label>
                                <input type="file" class="form-control" id="inputmarc">
                            </form>
                        </div>
                    </div>

                    <div class="modal-footer"><!--Rodapé-->
                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                            <?php _e('Cancel', 'tainacan'); ?>
                        </button>

                        <button type="button" class="btn btn-primary" id="btnAddMarc" onclick="createMarcItem();">
                            <?php _e('Add', 'tainacan'); ?>
                        </button>

                    </div><!--Fim rodapé-->
                </div>
            </div>
        </div>
    <?php
}

/*
 * Functions
 */

function import_marc()
{
    $marc = $_POST['marc'];
    $collection_id = $_POST['collection_id'];


    $lines = split_lines($marc);
    $treated_lines = treat_lines($lines);

    add_material($collection_id, $treated_lines);
}

function split_lines($marc)
{
    $array = [];
    $marc .= "\n";
    while(strlen($marc) - 1 > 0)
    {
        $line = strstr($marc, "\n", true);
        $marc = str_replace($line."\n", "", $marc);

        $array[] = $line;
    }

    return $array;
}

function treat_lines($lines)
{
    $array = [];
    foreach($lines as $line)
    {
        $field = strstr($line, " ", true);
        $line = remove_first_occurence($field." ", $line);

        $select_box_state = strstr($line, "|", true);

        if(strlen($field) > 0)
        {
            $array[$field]['1'] = $select_box_state[0];
            $array[$field]['2'] = $select_box_state[1];
        }

        $line = remove_first_occurence($select_box_state."|", $line);

        while(strlen($line) > 0)
        {
            $subFieldValue = strstr($line, "|", true);
            if($subFieldValue == null)
            {
                $subFieldValue = $line;
                $line = null;
            }
            else
            {
                $line = remove_first_occurence($subFieldValue."|", $line);
            }

            $subField = $subFieldValue[0];

            $subFieldValue = remove_first_occurence($subField, $subFieldValue);
            $array[$field][$subField] = $subFieldValue;
        }
    }

    return $array;
}

function remove_first_occurence($to_be_removed, $string)
{
    $length = strlen($to_be_removed);

    return substr($string, $length);
}

function just_numbers($str) {
    return preg_replace("/[^0-9]/", "", $str);
}

function add_material($collection_id, $property_list)
{
    $return = [];
    $properties_id = [];
    $data['object_name'] = 'Teste6';
    $collection_import_model = new CollectionImportModel();
    $user_id = get_current_user_id();
    $post = array(
        'post_title' => ($data['object_name']) ? $data['object_name'] : time(),
        'post_content' => $data['object_description'] ? $data['object_description'] : '',
        'post_status' => 'publish',
        'post_author' => $user_id,
        'post_type' => 'socialdb_object'
    );

    $data['ID'] = wp_insert_post($post);

    $category_root_id = get_post_meta($collection_id, 'socialdb_collection_object_type', true);

    wp_set_object_terms($data['ID'], array((int) $category_root_id), 'socialdb_category_type');
    wp_set_object_terms($data['ID'], array((int) $data['class_id']), 'socialdb_category_type',true);
    $collection_import_model->set_common_field_values($data['ID'], 'title',$data['object_name']);
    $collection_import_model->set_common_field_values($data['ID'], 'description', $data['object_description']);


    //Propriedades relacionadas a aquele individuo
    $root_category = get_post_meta($collection_id, 'socialdb_collection_object_type', true);
    $properties = get_term_meta($root_category,'socialdb_category_property_id');
    $properties = array_unique($properties);

    if($properties && is_array($properties)){

        foreach ($properties as $property){
            $property_name = get_term_by('id',$property,'socialdb_property_type')->name;
            $sub_properties = get_term_meta($property, 'socialdb_property_compounds_properties_id', true);

            if($sub_properties != null)
            {
                $sub_properties = explode(",", $sub_properties);
                //$return[just_numbers($property_name)] = $sub_properties;

                foreach ($sub_properties as $index => $value)
                {
                    $pn = get_term_by('id',$value,'socialdb_property_type')->name;
                    $sub_properties_name[$pn] = $value;
                }

                $return[just_numbers($property_name)] = $sub_properties_name;
                $properties_id[just_numbers($property_name)] = $property;
            }
        }
    }

    /* Pega metadados da coleção PAI*/
    $father_root_category_id = get_term_by("id", $category_root_id, "socialdb_category_type")->parent;
    $father_properties = get_term_meta($father_root_category_id, 'socialdb_category_property_id');
    $father_properties = array_unique($father_properties);

    if($father_properties && is_array($father_properties))
    {
        foreach($father_properties as $father_property)
        {
            $father_property_name = get_term_by('id',$father_property,'socialdb_property_type')->name;
            $father_sub_properties = get_term_meta($father_property, 'socialdb_property_compounds_properties_id', true);

            if($father_sub_properties != null)
            {
                $father_sub_properties = explode(",", $father_sub_properties);
                //$return[just_numbers($father_property_name)] = $father_sub_properties;

                foreach ($father_sub_properties as $index => $value)
                {
                    $pn = get_term_by('id',$value,'socialdb_property_type')->name;
                    $father_sub_properties_name[$pn] = $value;
                }

                $return[just_numbers($father_property_name)] = $father_sub_properties_name;

                $properties_id[just_numbers($father_property_name)] = $father_property;
            }
        }
    }

     /*!!!!!!Pega metadados da coleção PAI */

    foreach($property_list as $field => $sub_fields)
    {
        $inserted_ids = [];
        switch($field)
        {
            case '013':
                foreach($sub_fields as $sub_field => $value)
                {
                    switch ($sub_field)
                    {
                        case 'a':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Número'], $value);
                            break;
                        case 'b':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['País'], $value);
                            break;
                        case 'c':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Tipo'], $value);
                            break;
                        case 'd':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Data'], $value);
                            break;
                        case 'e':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Estado da patente'], $value);
                            break;
                        case 'f':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Parte de um documento'], $value);
                            break;

                    }
                }
                break;
            case '020':
                $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Número do ISBN'], $sub_fields['a']);
                break;
            case '022':
                $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Número do ISSN'], $sub_fields['a']);
                break;
            case '029':
                $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Número do ISNM'], $sub_fields['a']);
                break;
            case '040':
                foreach($sub_fields as $sub_field => $value)
                {
                    switch ($sub_field)
                    {
                        case 'a':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Código da agência catalogadora'], $value);
                            break;
                        case 'b':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Língua da catalogação'], $value);
                            break;
                    }
                }
                break;
            case '041':
                foreach($sub_fields as $sub_field => $value)
                {
                    switch ($sub_field)
                    {
                        case '1':
                            $term_meta = get_term_meta($return[$field]['Indicação de tradução'] ,'socialdb_property_term_root', true);
                            $term_children = get_term_children($term_meta, 'socialdb_category_type');
                            if($value == '0')
                            {
                                $category_root_id =  $term_children[0];
                            }else $category_root_id = $term_children[1];

                            wp_set_object_terms($data['ID'], array((int) $category_root_id), 'socialdb_category_type', true);
                            $inserted_ids[] = $category_root_id.'_cat';

                            break;
                        case 'a':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Código do idioma do texto'], $value);
                            break;
                        case 'b':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Código do idioma do sumário ou resumo'], $value);
                            break;
                        case 'h':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Código do idioma do documento original'], $value);
                            break;
                    }
                }
                break;
            case '043':
                $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Código de área geográfica'], $sub_fields['a']);
                break;
            case '045':
                foreach($sub_fields as $sub_field => $value)
                {
                    switch ($sub_field)
                    {
                        case '1':
                            $term_meta = get_term_meta($return[$field]['Tipo do período cronológico'] ,'socialdb_property_term_root', true);
                            $term_children = get_term_children($term_meta, 'socialdb_category_type');

                            switch ($value)
                            {
                                case '0':
                                    $category_root_id =  $term_children[3];
                                    break;
                                case '1':
                                    $category_root_id =  $term_children[2];
                                    break;
                                case '2':
                                    $category_root_id =  $term_children[2];
                                    break;
                            }

                            wp_set_object_terms($data['ID'], array((int) $category_root_id), 'socialdb_category_type', true);
                            $inserted_ids[] = $category_root_id.'_cat';

                            break;
                        case 'a':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Código do período de tempo'], $value);
                            break;
                        case 'b':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Período de tempo formatado de 9999 a.C em diante'], $value);
                            break;
                        case 'c':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Período de tempo formatado anterior a 9999 a.C.'], $value);
                            break;
                    }
                }
                break;
            case '080':
                foreach($sub_fields as $sub_field => $value)
                {
                    switch ($sub_field)
                    {
                        case '2':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Número de edição da CDU'], $value);
                            break;
                        case 'a':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Número de Classificação'], $value);
                            break;
                    }
                }
                break;
            case '082':
                foreach($sub_fields as $sub_field => $value)
                {
                    switch ($sub_field)
                    {
                        case '2':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Número de edição da CDD'], $value);
                            break;
                        case 'a':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Número de Classificação'], $value);
                            break;
                    }
                }
                break;
            case '090':
                foreach($sub_fields as $sub_field => $value)
                {
                    switch ($sub_field)
                    {
                        case 'a':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Classificação'], $value);
                            break;
                        case 'b':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Código do autor'], $value);
                            break;
                        case 'c':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Edição - volume'], $value);
                            break;
                    }
                }
                break;
            case '095':
                $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Área do conhecimento'], $sub_fields['a']);
                break;
        }

        update_post_meta($data['ID'], 'socialdb_property_' . $properties_id[$field] . '_0', implode(',', $inserted_ids));
    }
}

function parse_save($object_id, $compound_id, $property_id, $value)
{
    $object_model = new ObjectModel();
    return $object_model->add_value_compound($object_id,$compound_id, $property_id, 0, 0, $value);
}