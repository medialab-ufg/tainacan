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
 * FILTERS and ACTIONS
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

add_action('add_tab_marc', 'add_new_tab_marc');
function add_new_tab_marc()
{
    ?>
    <li role="presentation"><a id="click_marc_tab" href="#marc_tab" aria-controls="marc_tab" role="tab" data-toggle="tab"><?php _e('MARC','tainacan') ?></a></li>
    <?php
}

add_action('add_show_all_meta', 'show_all_meta',10, 1);
function show_all_meta($collection_id)
{
    $all_properties_id = meta_ids($collection_id, false);
    $all_marc_fields = get_all_marc_fields();

    ?>
        <div role="tabpanel" class="tab-pane" id="marc_tab">
            <form action="" name="mapping" method="post">
            <?php foreach ($all_properties_id as $compound_name => $sub_properties) {?>
                <div class='form-group'>
                    <label class='col-md-12 col-sm-12 meta-title no-padding' name="<?= $compound_name?>" id="<?= $compound_name?>"> <?= $compound_name?> </label>
                <?php foreach ($sub_properties as $name => $id){?>
                        <label class='col-md-6 col-sm-12 meta-title no-padding'style="text-indent: 5%;"> <?= $name?> </label>
                        <div class='col-md-6 col-sm-12 meta-value'>
                                <select name="<?= $name ?>" class='data form-control' id="<?= $name ?>">
                                    <?php foreach ($all_marc_fields as $field) { ?>
                                        <option name="<?= $field ?>" id="<?= $field ?>" value="<?= $field ?>"><?= $field ?></option>
                                    <?php } ?>
                                </select>
                        </div>
                <?php } ?>
                </div>
            <?php } ?>
            <button type="button" class="btn btn-primary btn-lg tainacan-blue-btn-bg pull-right" id="mapping_save" onclick="save_mapping()"><?php _e("Save", "tainacan"); ?></button>
            </form>
        </div>
    <?php
}

/*
 * Functions
 */

function meta_ids($collection_id, $change_names_for_numbers)
{
    $return = [];

    $category_root_id = get_post_meta($collection_id, 'socialdb_collection_object_type', true);

    $root_category = get_post_meta($collection_id, 'socialdb_collection_object_type', true);
    $properties = get_term_meta($root_category,'socialdb_category_property_id');
    $properties = array_unique($properties);

    $father_root_category_id = get_term_by("id", $category_root_id, "socialdb_category_type")->parent;
    $father_properties = get_term_meta($father_root_category_id, 'socialdb_category_property_id');
    $father_properties = array_unique($father_properties);

    $all_properties = array_merge($properties, $father_properties);

    foreach ($all_properties as $property){
        $property_name = get_term_by('id',$property,'socialdb_property_type')->name;
        $sub_properties = get_term_meta($property, 'socialdb_property_compounds_properties_id', true);
        $sub_properties_name = [];
        if($sub_properties != null)
        {
            $sub_properties = explode(",", $sub_properties);
            //$return[just_numbers($property_name)] = $sub_properties;

            foreach ($sub_properties as $index => $value)
            {
                $pn = get_term_by('id',$value,'socialdb_property_type')->name;
                if(strlen($pn) > 0)
                {
                    $sub_properties_name[$pn] = $value;
                }

            }

            $properties_id[just_numbers($property_name)] = $property;
            if($change_names_for_numbers == true)
            {
                $return[just_numbers($property_name)] = $sub_properties_name;
            }else $return[$property_name] = $sub_properties_name;
        }
    }

    return $return;
}

function get_all_marc_fields()
{
    $marc_fiels = [];

    $marc_fiels[] = '013 $a';
    $marc_fiels[] = '013 $b';
    $marc_fiels[] = '013 $c';
    $marc_fiels[] = '013 $d';
    $marc_fiels[] = '013 $e';
    $marc_fiels[] = '013 $f';

    $marc_fiels[] = '020 $a';

    $marc_fiels[] = '022 $a';

    $marc_fiels[] = '029 $a';

    $marc_fiels[] = '040 $a';
    $marc_fiels[] = '040 $b';

    $marc_fiels[] = '041 #1';
    $marc_fiels[] = '041 $a';
    $marc_fiels[] = '041 $b';
    $marc_fiels[] = '041 $h';

    $marc_fiels[] = '043 $a';

    $marc_fiels[] = '045 #1';
    $marc_fiels[] = '045 $a';
    $marc_fiels[] = '045 $b';
    $marc_fiels[] = '045 $c';

    $marc_fiels[] = '080 $2';
    $marc_fiels[] = '080 $a';

    $marc_fiels[] = '082 $2';
    $marc_fiels[] = '082 $a';

    $marc_fiels[] = '090 $a';
    $marc_fiels[] = '090 $b';
    $marc_fiels[] = '090 $c';

    $marc_fiels[] = '095 $a';

    $marc_fiels[] = '100 #1';
    $marc_fiels[] = '100 $a';
    $marc_fiels[] = '100 $b';
    $marc_fiels[] = '100 $c';
    $marc_fiels[] = '100 $d';
    $marc_fiels[] = '100 $q';

    $marc_fiels[] = '110 #1';
    $marc_fiels[] = '110 $a';
    $marc_fiels[] = '110 $b';
    $marc_fiels[] = '110 $c';
    $marc_fiels[] = '110 $d';
    $marc_fiels[] = '110 $l';
    $marc_fiels[] = '110 $n';

    $marc_fiels[] = '111 #1';
    $marc_fiels[] = '111 $a';
    $marc_fiels[] = '111 $c';
    $marc_fiels[] = '111 $d';
    $marc_fiels[] = '111 $e';
    $marc_fiels[] = '111 $g';
    $marc_fiels[] = '111 $k';
    $marc_fiels[] = '111 $n';

    $marc_fiels[] = '130 #1';
    $marc_fiels[] = '130 $a';
    $marc_fiels[] = '130 $d';
    $marc_fiels[] = '130 $f';
    $marc_fiels[] = '130 $g';
    $marc_fiels[] = '130 $k';
    $marc_fiels[] = '130 $k';
    $marc_fiels[] = '130 $l';
    $marc_fiels[] = '130 $p';

    $marc_fiels[] = '210 #1';
    $marc_fiels[] = '210 #2';
    $marc_fiels[] = '210 $a';
    $marc_fiels[] = '210 $b';

    $marc_fiels[] = '240 #1';
    $marc_fiels[] = '240 #2';
    $marc_fiels[] = '240 $a';
    $marc_fiels[] = '240 $b';
    $marc_fiels[] = '240 $f';
    $marc_fiels[] = '240 $g';
    $marc_fiels[] = '240 $k';
    $marc_fiels[] = '240 $l';
    $marc_fiels[] = '240 $n';
    $marc_fiels[] = '240 $p';

    $marc_fiels[] = '243 #1';
    $marc_fiels[] = '243 #2';
    $marc_fiels[] = '243 $a';
    $marc_fiels[] = '243 $f';
    $marc_fiels[] = '243 $g';
    $marc_fiels[] = '243 $k';
    $marc_fiels[] = '243 $l';

    $marc_fiels[] = '245 #1';
    $marc_fiels[] = '245 #2';
    $marc_fiels[] = '245 $a';
    $marc_fiels[] = '245 $b';
    $marc_fiels[] = '245 $c';
    $marc_fiels[] = '245 $h';
    $marc_fiels[] = '245 $n';
    $marc_fiels[] = '245 $p';

    $marc_fiels[] = '246 #1';
    $marc_fiels[] = '246 #2';
    $marc_fiels[] = '246 $a';
    $marc_fiels[] = '246 $b';
    $marc_fiels[] = '246 $f';
    $marc_fiels[] = '246 $g';
    $marc_fiels[] = '246 $h';
    $marc_fiels[] = '246 $i';
    $marc_fiels[] = '246 $n';
    $marc_fiels[] = '246 $p';

    $marc_fiels[] = '250 $a';
    $marc_fiels[] = '250 $b';

    $marc_fiels[] = '255 $a';

    $marc_fiels[] = '256 $a';

    $marc_fiels[] = '257 $a';

    $marc_fiels[] = '258 $a';
    $marc_fiels[] = '258 $b';


    $marc_fiels[] = '260 $a';
    $marc_fiels[] = '260 $b';
    $marc_fiels[] = '260 $c';
    $marc_fiels[] = '260 $e';
    $marc_fiels[] = '260 $f';
    $marc_fiels[] = '260 $g';

    $marc_fiels[] = '300 $a';
    $marc_fiels[] = '300 $b';
    $marc_fiels[] = '300 $c';
    $marc_fiels[] = '300 $e';

    $marc_fiels[] = '306 $a';

    $marc_fiels[] = '310 $a';
    $marc_fiels[] = '310 $b';

    $marc_fiels[] = '321 $a';
    $marc_fiels[] = '321 $b';

    $marc_fiels[] = '340 $a';
    $marc_fiels[] = '340 $b';
    $marc_fiels[] = '340 $c';
    $marc_fiels[] = '340 $d';
    $marc_fiels[] = '340 $e';

    $marc_fiels[] = '342 #1';
    $marc_fiels[] = '342 #2';
    $marc_fiels[] = '342 $a';
    $marc_fiels[] = '342 $b';
    $marc_fiels[] = '342 $c';
    $marc_fiels[] = '342 $d';

    $marc_fiels[] = '343 $a';
    $marc_fiels[] = '343 $b';

    $marc_fiels[] = '362 #1';
    $marc_fiels[] = '362 $a';
    $marc_fiels[] = '362 $z';

    $marc_fiels[] = '490 #1';
    $marc_fiels[] = '490 $a';
    $marc_fiels[] = '490 $v';

    $marc_fiels[] = '500 $a';

    $marc_fiels[] = '501 $a';

    $marc_fiels[] = '502 $a';

    $marc_fiels[] = '504 $a';

    $marc_fiels[] = '505 $a';

    $marc_fiels[] = '515 $a';

    $marc_fiels[] = '520 $a';
    $marc_fiels[] = '520 $u';

    $marc_fiels[] = '521 $a';

    $marc_fiels[] = '525 $a';

    $marc_fiels[] = '530 $a';

    $marc_fiels[] = '534 $a';

    $marc_fiels[] = '550 $a';

    $marc_fiels[] = '555 #1';
    $marc_fiels[] = '555 $3';
    $marc_fiels[] = '555 $a';
    $marc_fiels[] = '555 $b';
    $marc_fiels[] = '555 $c';
    $marc_fiels[] = '555 $d';
    $marc_fiels[] = '555 $u';

    $marc_fiels[] = '580 $a';

    $marc_fiels[] = '590 $a';

    $marc_fiels[] = '595 $a';
    $marc_fiels[] = '595 $b';

    $marc_fiels[] = '600 #1';
    $marc_fiels[] = '600 $a';
    $marc_fiels[] = '600 $b';
    $marc_fiels[] = '600 $c';
    $marc_fiels[] = '600 $d';
    $marc_fiels[] = '600 $k';
    $marc_fiels[] = '600 $q';
    $marc_fiels[] = '600 $t';
    $marc_fiels[] = '600 $x';
    $marc_fiels[] = '600 $y';
    $marc_fiels[] = '600 $z';
    
    $marc_fiels[] = '610 #1';
    $marc_fiels[] = '610 $a';
    $marc_fiels[] = '610 $b';
    $marc_fiels[] = '610 $c';
    $marc_fiels[] = '610 $d';
    $marc_fiels[] = '610 $g';
    $marc_fiels[] = '610 $k';
    $marc_fiels[] = '610 $l';
    $marc_fiels[] = '610 $n';
    $marc_fiels[] = '610 $t';
    $marc_fiels[] = '610 $x';
    $marc_fiels[] = '610 $y';
    $marc_fiels[] = '610 $z';

    $marc_fiels[] = '611 #1';
    $marc_fiels[] = '611 $a';
    $marc_fiels[] = '611 $c';
    $marc_fiels[] = '611 $d';
    $marc_fiels[] = '611 $e';
    $marc_fiels[] = '611 $n';
    $marc_fiels[] = '611 $t';
    $marc_fiels[] = '611 $x';
    $marc_fiels[] = '611 $y';
    $marc_fiels[] = '611 $z';

    $marc_fiels[] = '630 #1';
    $marc_fiels[] = '630 $a';
    $marc_fiels[] = '630 $d';
    $marc_fiels[] = '630 $f';
    $marc_fiels[] = '630 $g';
    $marc_fiels[] = '630 $k';
    $marc_fiels[] = '630 $l';
    $marc_fiels[] = '630 $p';
    $marc_fiels[] = '630 $x';
    $marc_fiels[] = '630 $y';
    $marc_fiels[] = '630 $z';

    $marc_fiels[] = '650 $a';
    $marc_fiels[] = '650 $x';
    $marc_fiels[] = '650 $y';
    $marc_fiels[] = '650 $z';

    $marc_fiels[] = '651 $a';
    $marc_fiels[] = '651 $x';
    $marc_fiels[] = '651 $y';
    $marc_fiels[] = '651 $z';

    $marc_fiels[] = '700 #1';
    $marc_fiels[] = '700 #2';
    $marc_fiels[] = '700 $a';
    $marc_fiels[] = '700 $b';
    $marc_fiels[] = '700 $c';
    $marc_fiels[] = '700 $d';
    $marc_fiels[] = '700 $e';
    $marc_fiels[] = '700 $l';
    $marc_fiels[] = '700 $q';
    $marc_fiels[] = '700 $t';

    $marc_fiels[] = '710 #1';
    $marc_fiels[] = '710 #2';
    $marc_fiels[] = '710 $a';
    $marc_fiels[] = '710 $b';
    $marc_fiels[] = '710 $c';
    $marc_fiels[] = '710 $d';
    $marc_fiels[] = '710 $g';
    $marc_fiels[] = '710 $l';
    $marc_fiels[] = '710 $n';
    $marc_fiels[] = '710 $t';

    $marc_fiels[] = '730 #1';
    $marc_fiels[] = '730 #2';
    $marc_fiels[] = '730 $a';
    $marc_fiels[] = '730 $d';
    $marc_fiels[] = '730 $f';
    $marc_fiels[] = '730 $g';
    $marc_fiels[] = '730 $k';
    $marc_fiels[] = '730 $l';
    $marc_fiels[] = '730 $p';
    $marc_fiels[] = '730 $x';
    $marc_fiels[] = '730 $y';
    $marc_fiels[] = '730 $z';

    $marc_fiels[] = '740 #1';
    $marc_fiels[] = '740 #2';
    $marc_fiels[] = '740 $a';
    $marc_fiels[] = '740 $n';
    $marc_fiels[] = '740 $p';

    $marc_fiels[] = '830 #2';
    $marc_fiels[] = '830 $a';
    $marc_fiels[] = '830 $v';

    $marc_fiels[] = '856 $d';
    $marc_fiels[] = '856 $f';
    $marc_fiels[] = '856 $u';
    $marc_fiels[] = '856 $y';

    $marc_fiels[] = '947 $a';
    $marc_fiels[] = '947 $b';
    $marc_fiels[] = '947 $c';
    $marc_fiels[] = '947 $d';
    $marc_fiels[] = '947 $e';
    $marc_fiels[] = '947 $f';
    $marc_fiels[] = '947 $g';
    $marc_fiels[] = '947 $i';
    $marc_fiels[] = '947 $j';
    $marc_fiels[] = '947 $k';
    $marc_fiels[] = '947 $l';
    $marc_fiels[] = '947 $n';
    $marc_fiels[] = '947 $o';
    $marc_fiels[] = '947 $p';
    $marc_fiels[] = '947 $q';
    $marc_fiels[] = '947 $r';
    $marc_fiels[] = '947 $s';
    $marc_fiels[] = '947 $t';
    $marc_fiels[] = '947 $u';
    $marc_fiels[] = '947 $z';

    return $marc_fiels;
}

function save_mapping_marc($name, $collection_id )
{
    $data_info = [];
    $object_id = create_mapping($name, $collection_id);
    
}

function create_mapping($name, $collection_id) {
    $post = array(
        'post_title' => $name,
        'post_status' => 'publish',
        'post_type' => 'socialdb_channel'
    );
    $object_id = wp_insert_post($post);
    add_post_meta($object_id, 'socialdb_channel_identificator', $name);
    add_post_meta($collection_id, 'socialdb_collection_channel', $object_id);
    wp_set_object_terms($object_id, array((int) $this->parent->term_id), 'socialdb_channel_type');
    return $object_id;
}

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
            case '100':
                foreach($sub_fields as $sub_field => $value)
                {
                    switch ($sub_field)
                    {
                        case '1':
                            $term_meta = get_term_meta($return[$field]['Forma de entrada'] ,'socialdb_property_term_root', true);
                            $term_children = get_term_children($term_meta, 'socialdb_category_type');

                            switch ($value)
                            {
                                case '0':
                                    $category_root_id =  $term_children[1];
                                    break;
                                case '1':
                                    $category_root_id =  $term_children[3];
                                    break;
                                case '2':
                                    $category_root_id =  $term_children[2];
                                    break;
                                case '3':
                                    $category_root_id =  $term_children[0];
                                    break;
                            }

                            wp_set_object_terms($data['ID'], array((int) $category_root_id), 'socialdb_category_type', true);
                            $inserted_ids[] = $category_root_id.'_cat';

                            break;
                        case 'a':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Sobrenome e/ou prenome do autor'], $value);
                            break;
                        case 'b':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Numeração que segue o prenome'], $value);
                            break;
                        case 'c':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Título e outras palavras associadas ao nome'], $value);
                            break;
                        case 'd':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Datas associadas ao nome'], $value);
                            break;
                        case 'q':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Forma completa do nome'], $value);
                            break;
                    }
                }
                break;
            case '110':
                foreach($sub_fields as $sub_field => $value)
                {
                    switch ($sub_field)
                    {
                        case '1':
                            $term_meta = get_term_meta($return[$field]['Forma de entrada'] ,'socialdb_property_term_root', true);
                            $term_children = get_term_children($term_meta, 'socialdb_category_type');

                            switch ($value)
                            {
                                case '0':
                                    $category_root_id =  $term_children[1];
                                    break;
                                case '1':
                                    $category_root_id =  $term_children[0];
                                    break;
                                case '2':
                                    $category_root_id =  $term_children[2];
                                    break;
                            }

                            wp_set_object_terms($data['ID'], array((int) $category_root_id), 'socialdb_category_type', true);
                            $inserted_ids[] = $category_root_id.'_cat';

                            break;
                        case 'a':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Nome da entidade ou do lugar'], $value);
                            break;
                        case 'b':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Unidades subordinadas'], $value);
                            break;
                        case 'c':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Local de realização do evento'], $value);
                            break;
                        case 'd':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Data da realização do evento'], $value);
                            break;
                        case 'l':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Língua do texto'], $value);
                            break;
                        case 'n':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Número da parte - seção da obra - ordem do evento'], $value);
                            break;
                    }
                }
                break;
            case '111':
                foreach($sub_fields as $sub_field => $value)
                {
                    switch ($sub_field)
                    {
                        case '1':
                            $term_meta = get_term_meta($return[$field]['Forma de entrada*'] ,'socialdb_property_term_root', true);
                            $term_children = get_term_children($term_meta, 'socialdb_category_type');

                            switch ($value)
                            {
                                case '0':
                                    $category_root_id =  $term_children[1];
                                    break;
                                case '1':
                                    $category_root_id =  $term_children[0];
                                    break;
                                case '2':
                                    $category_root_id =  $term_children[2];
                                    break;
                            }

                            wp_set_object_terms($data['ID'], array((int) $category_root_id), 'socialdb_category_type', true);
                            $inserted_ids[] = $category_root_id.'_cat';

                            break;
                        case 'a':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Nome do evento'], $value);
                            break;
                        case 'c':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Local de realização do evento'], $value);
                            break;
                        case 'd':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Data da realização do evento'], $value);
                            break;
                        case 'e':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Nome de subunidades do evento'], $value);
                            break;
                        case 'g':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Informações adicionais'], $value);
                            break;
                        case 'k':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Subcabeçalhos'], $value);
                            break;
                        case 'n':
                            $inserted_ids[] = parse_save($data['ID'], $properties_id[$field], $return[$field]['Número de ordem do evento'], $value);
                            break;
                    }
                }
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