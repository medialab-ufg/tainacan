<?php
/**
 * Tainacan Biblioteca
 */

define('MODULE_LIBRARY', 'tainacan-library');
define('LIBRARY_CONTROLLERS', get_template_directory_uri() . '/modules/' . MODULE_LIBRARY );

define("COLLECTION_MAPPING_MARC_FATHER", "mappingMarcFather");
define("COLLECTION_MAPPING_MARC_SON", "mappingMarcSon");

define("MAPPING_MARC_ID_FATHER", "socialdb_mapping_marc_id_father");
define("MAPPING_MARC_ID_SON", "socialdb_mapping_marc_id_son");

define("MAPPING_MARC_TABLE", "socialdb_channel_marc_mapping_table");


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
    $marc_mapping = get_marc_mapping($collection_id);
    //print_r($marc_mapping);
    $marc_mapping_inverse = [];
    if($marc_mapping['father'] != false)
    {
        foreach($marc_mapping['father'] as $compound_num => $subfields)
        {
            foreach ($subfields as $subfield => $prop_id)
            {
                if($subfield != 'compound_id')
                {
                    if($subfield == '1')
                    {
                        $simbol = '#';
                    }else $simbol = '$';

                    $marc_mapping_inverse[$prop_id] = $compound_num." $simbol".$subfield;
                }else $marc_mapping_inverse[$prop_id] = $compound_num;
            }
        }

        if($marc_mapping['son'] != false)
        {
            foreach($marc_mapping['son'] as $compound_num => $subfields)
            {
                foreach ($subfields as $subfield => $prop_id)
                {
                    if($subfield != 'compound_id')
                    {
                        if($subfield == '1')
                        {
                            $simbol = '#';
                        }else $simbol = '$';

                        $marc_mapping_inverse[$prop_id] = $compound_num." $simbol".$subfield;
                    }else $marc_mapping_inverse[$prop_id] = $compound_num;
                }
            }
        }
    }else $marc_mapping_inverse = false;

    ?>
        <div role="tabpanel" class="tab-pane" id="marc_tab">
            <form name="mapping_marc" method="post" id="mapping_marc">
                <input type="hidden" name="collection_id" value="<?php echo $collection_id ?>">
                <input type="hidden" name="operation" value="save_mapping_marc">
            <?php
            foreach($all_properties_id as $setProperties){
            foreach ($setProperties as $compound_name => $sub_properties) {?>
                <div class='form-group'>
                    <label class='col-md-6 col-sm-12 meta-title no-padding' name="<?= $compound_name?>" id="<?= $compound_name?>"> <?= $compound_name?> </label>

                    <div class='col-md-6 col-sm-12 meta-value'>
                        <select name="<?= $sub_properties['compound_id'] ?>" class='data form-control' id="<?= $compound_name ?>">
                            <?php
                                foreach ($all_marc_fields as $field)
                                {
                                    if($marc_mapping_inverse != false && strcmp($marc_mapping_inverse[$sub_properties['compound_id']], $field) == 0)
                                    {
                                        echo "<option name='".$field."' value='".$field."' selected>". $field ."</option>";
                                    }else
                                    {
                                        echo "<option name='".$field."' value='".$field."'>". $field ."</option>";
                                    }

                                }
                            ?>
                        </select>
                    </div>
                <?php foreach ($sub_properties as $name => $id){
                    if($name != 'compound_id'){ ?>
                        <div class="col-md-12 no-padding" style="margin: 10px 0 10px 0">
                            <label class='col-md-6 col-sm-12 meta-title no-padding' style="text-indent: 5%; padding-bottom: 15px; border-bottom: 1px solid #e8e8e8"> <?= $name?> </label>
                            <div class='col-md-6 col-sm-12 meta-value'>
                                <select name="<?= $id ?>" class='data form-control' id="<?= $name ?>">
                                    <?php
                                    foreach ($all_marc_fields as $field) {
                                        if($marc_mapping_inverse != false && strcmp($marc_mapping_inverse[$id], $field) == 0)
                                        {
                                            echo "<option name='".$field."' value='".$field."' selected>". $field ."</option>";
                                        } else {
                                            echo "<option name='".$field."' value='".$field."'>". $field ."</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                <?php
                    }
                } }?>
                </div>
            <?php } ?>
            <button type="submit" class="btn btn-primary btn-lg tainacan-blue-btn-bg pull-right" id="mapping_save" onclick="save_mapping_marc()"><?php _e("Save", "tainacan"); ?></button>
            </form>
        </div>
    <?php
}

/*
 * Functions
 */

function meta_ids($collection_id, $change_names_for_numbers)
{
    $category_root_id = get_post_meta($collection_id, 'socialdb_collection_object_type', true);

    $root_category = get_post_meta($collection_id, 'socialdb_collection_object_type', true);
    $properties = get_term_meta($root_category,'socialdb_category_property_id');
    $properties = array_unique($properties);

    $father_root_category_id = get_term_by("id", $category_root_id, "socialdb_category_type")->parent;
    $father_properties = get_term_meta($father_root_category_id, 'socialdb_category_property_id');
    $father_properties = array_unique($father_properties);

    $return['son'] = get_ids($properties, $change_names_for_numbers);
    $return['father'] =  get_ids($father_properties, $change_names_for_numbers);

    return $return;
}

function get_ids($all_properties, $change_names_for_numbers)
{
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
            $sub_properties_name['compound_id'] = $property;
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

    $marc_fiels[] = '013';
    $marc_fiels[] = '013 $a';
    $marc_fiels[] = '013 $b';
    $marc_fiels[] = '013 $c';
    $marc_fiels[] = '013 $d';
    $marc_fiels[] = '013 $e';
    $marc_fiels[] = '013 $f';

    $marc_fiels[] = '020';
    $marc_fiels[] = '020 $a';

    $marc_fiels[] = '022';
    $marc_fiels[] = '022 $a';

    $marc_fiels[] = '029';
    $marc_fiels[] = '029 $a';

    $marc_fiels[] = '040';
    $marc_fiels[] = '040 $a';
    $marc_fiels[] = '040 $b';

    $marc_fiels[] = '041';
    $marc_fiels[] = '041 #1';
    $marc_fiels[] = '041 $a';
    $marc_fiels[] = '041 $b';
    $marc_fiels[] = '041 $h';

    $marc_fiels[] = '043';
    $marc_fiels[] = '043 $a';

    $marc_fiels[] = '045';
    $marc_fiels[] = '045 #1';
    $marc_fiels[] = '045 $a';
    $marc_fiels[] = '045 $b';
    $marc_fiels[] = '045 $c';

    $marc_fiels[] = '080';
    $marc_fiels[] = '080 $2';
    $marc_fiels[] = '080 $a';

    $marc_fiels[] = '082';
    $marc_fiels[] = '082 $2';
    $marc_fiels[] = '082 $a';

    $marc_fiels[] = '090';
    $marc_fiels[] = '090 $a';
    $marc_fiels[] = '090 $b';
    $marc_fiels[] = '090 $c';

    $marc_fiels[] = '095';
    $marc_fiels[] = '095 $a';

    $marc_fiels[] = '100';
    $marc_fiels[] = '100 #1';
    $marc_fiels[] = '100 $a';
    $marc_fiels[] = '100 $b';
    $marc_fiels[] = '100 $c';
    $marc_fiels[] = '100 $d';
    $marc_fiels[] = '100 $q';

    $marc_fiels[] = '110';
    $marc_fiels[] = '110 #1';
    $marc_fiels[] = '110 $a';
    $marc_fiels[] = '110 $b';
    $marc_fiels[] = '110 $c';
    $marc_fiels[] = '110 $d';
    $marc_fiels[] = '110 $l';
    $marc_fiels[] = '110 $n';

    $marc_fiels[] = '111';
    $marc_fiels[] = '111 #1';
    $marc_fiels[] = '111 $a';
    $marc_fiels[] = '111 $c';
    $marc_fiels[] = '111 $d';
    $marc_fiels[] = '111 $e';
    $marc_fiels[] = '111 $g';
    $marc_fiels[] = '111 $k';
    $marc_fiels[] = '111 $n';

    $marc_fiels[] = '130';
    $marc_fiels[] = '130 #1';
    $marc_fiels[] = '130 $a';
    $marc_fiels[] = '130 $d';
    $marc_fiels[] = '130 $f';
    $marc_fiels[] = '130 $g';
    $marc_fiels[] = '130 $k';
    $marc_fiels[] = '130 $k';
    $marc_fiels[] = '130 $l';
    $marc_fiels[] = '130 $p';

    $marc_fiels[] = '210';
    $marc_fiels[] = '210 #1';
    $marc_fiels[] = '210 #2';
    $marc_fiels[] = '210 $a';
    $marc_fiels[] = '210 $b';

    $marc_fiels[] = '240';
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

    $marc_fiels[] = '243';
    $marc_fiels[] = '243 #1';
    $marc_fiels[] = '243 #2';
    $marc_fiels[] = '243 $a';
    $marc_fiels[] = '243 $f';
    $marc_fiels[] = '243 $g';
    $marc_fiels[] = '243 $k';
    $marc_fiels[] = '243 $l';

    $marc_fiels[] = '245';
    $marc_fiels[] = '245 #1';
    $marc_fiels[] = '245 #2';
    $marc_fiels[] = '245 $a';
    $marc_fiels[] = '245 $b';
    $marc_fiels[] = '245 $c';
    $marc_fiels[] = '245 $h';
    $marc_fiels[] = '245 $n';
    $marc_fiels[] = '245 $p';

    $marc_fiels[] = '246';
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

    $marc_fiels[] = '250';
    $marc_fiels[] = '250 $a';
    $marc_fiels[] = '250 $b';

    $marc_fiels[] = '255';
    $marc_fiels[] = '255 $a';

    $marc_fiels[] = '256';
    $marc_fiels[] = '256 $a';

    $marc_fiels[] = '257';
    $marc_fiels[] = '257 $a';

    $marc_fiels[] = '258';
    $marc_fiels[] = '258 $a';
    $marc_fiels[] = '258 $b';

    $marc_fiels[] = '260';
    $marc_fiels[] = '260 $a';
    $marc_fiels[] = '260 $b';
    $marc_fiels[] = '260 $c';
    $marc_fiels[] = '260 $e';
    $marc_fiels[] = '260 $f';
    $marc_fiels[] = '260 $g';

    $marc_fiels[] = '300';
    $marc_fiels[] = '300 $a';
    $marc_fiels[] = '300 $b';
    $marc_fiels[] = '300 $c';
    $marc_fiels[] = '300 $e';

    $marc_fiels[] = '306';
    $marc_fiels[] = '306 $a';

    $marc_fiels[] = '310';
    $marc_fiels[] = '310 $a';
    $marc_fiels[] = '310 $b';

    $marc_fiels[] = '321';
    $marc_fiels[] = '321';
    $marc_fiels[] = '321 $a';
    $marc_fiels[] = '321 $b';

    $marc_fiels[] = '340';
    $marc_fiels[] = '340 $a';
    $marc_fiels[] = '340 $b';
    $marc_fiels[] = '340 $c';
    $marc_fiels[] = '340 $d';
    $marc_fiels[] = '340 $e';

    $marc_fiels[] = '342';
    $marc_fiels[] = '342 #1';
    $marc_fiels[] = '342 #2';
    $marc_fiels[] = '342 $a';
    $marc_fiels[] = '342 $b';
    $marc_fiels[] = '342 $c';
    $marc_fiels[] = '342 $d';

    $marc_fiels[] = '343';
    $marc_fiels[] = '343 $a';
    $marc_fiels[] = '343 $b';

    $marc_fiels[] = '362';
    $marc_fiels[] = '362 #1';
    $marc_fiels[] = '362 $a';
    $marc_fiels[] = '362 $z';

    $marc_fiels[] = '490';
    $marc_fiels[] = '490 #1';
    $marc_fiels[] = '490 $a';
    $marc_fiels[] = '490 $v';

    $marc_fiels[] = '500';
    $marc_fiels[] = '500 $a';

    $marc_fiels[] = '501';
    $marc_fiels[] = '501 $a';

    $marc_fiels[] = '502';
    $marc_fiels[] = '502 $a';

    $marc_fiels[] = '504';
    $marc_fiels[] = '504 $a';

    $marc_fiels[] = '505';
    $marc_fiels[] = '505 $a';

    $marc_fiels[] = '515';
    $marc_fiels[] = '515 $a';

    $marc_fiels[] = '520';
    $marc_fiels[] = '520 $a';
    $marc_fiels[] = '520 $u';

    $marc_fiels[] = '521';
    $marc_fiels[] = '521 $a';

    $marc_fiels[] = '525';
    $marc_fiels[] = '525 $a';

    $marc_fiels[] = '530';
    $marc_fiels[] = '530 $a';

    $marc_fiels[] = '534';
    $marc_fiels[] = '534 $a';

    $marc_fiels[] = '550';
    $marc_fiels[] = '550 $a';

    $marc_fiels[] = '555';
    $marc_fiels[] = '555 #1';
    $marc_fiels[] = '555 $3';
    $marc_fiels[] = '555 $a';
    $marc_fiels[] = '555 $b';
    $marc_fiels[] = '555 $c';
    $marc_fiels[] = '555 $d';
    $marc_fiels[] = '555 $u';

    $marc_fiels[] = '580';
    $marc_fiels[] = '580 $a';

    $marc_fiels[] = '590';
    $marc_fiels[] = '590 $a';

    $marc_fiels[] = '595';
    $marc_fiels[] = '595 $a';
    $marc_fiels[] = '595 $b';

    $marc_fiels[] = '600';
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

    $marc_fiels[] = '610';
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

    $marc_fiels[] = '611';
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

    $marc_fiels[] = '630';
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

    $marc_fiels[] = '650';
    $marc_fiels[] = '650 $a';
    $marc_fiels[] = '650 $x';
    $marc_fiels[] = '650 $y';
    $marc_fiels[] = '650 $z';

    $marc_fiels[] = '651';
    $marc_fiels[] = '651 $a';
    $marc_fiels[] = '651 $x';
    $marc_fiels[] = '651 $y';
    $marc_fiels[] = '651 $z';

    $marc_fiels[] = '700';
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

    $marc_fiels[] = '710';
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

    $marc_fiels[] = '730';
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

    $marc_fiels[] = '740';
    $marc_fiels[] = '740 #1';
    $marc_fiels[] = '740 #2';
    $marc_fiels[] = '740 $a';
    $marc_fiels[] = '740 $n';
    $marc_fiels[] = '740 $p';

    $marc_fiels[] = '830';
    $marc_fiels[] = '830 #2';
    $marc_fiels[] = '830 $a';
    $marc_fiels[] = '830 $v';

    $marc_fiels[] = '856';
    $marc_fiels[] = '856 $d';
    $marc_fiels[] = '856 $f';
    $marc_fiels[] = '856 $u';
    $marc_fiels[] = '856 $y';

    $marc_fiels[] = '947';
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

function save_mapping_marc($data)
{
    $collection_id = $data['collection_id'];
    $mappingModel = new MappingModel();

    $meta_ids = meta_ids($collection_id, false);
    $ids_from_father = [];
    $ids_from_son = [];
    //print_r($data);
    foreach ($meta_ids as $index =>$setIds)
    {
        foreach ($setIds as $field)
        {
            foreach ($field as $subfield_id)
            {
                if($index == "father")
                {
                    $ids_from_father[] = $subfield_id;
                }
                else $ids_from_son[] = $subfield_id;
            }
        }
    }

    $father_data_info = [];
    $son_data_info = [];
    foreach ($data as $property_id => $value)
    {
        $subfield = just_letters($value);
        if($subfield == null)
        {
            if(strstr($value, "#1"))
            {
                $subfield = "1";
                $value = str_replace("#1", "", $value);
            }else if (strstr($value, "$2"))
            {
                $subfield = "2";
                $value = str_replace("$2", "", $value);
            }
            else $subfield = 'compound_id';
        }

        if(in_array($property_id, $ids_from_father))
        {
            $father_data_info[just_numbers($value)][$subfield] = $property_id;
        }else $son_data_info[just_numbers($value)][$subfield] = $property_id;
    }
    //print_r($son_data_info);
    if(get_post_by_name(COLLECTION_MAPPING_MARC_FATHER, OBJECT, "socialdb_channel") == null)
    {
        //Cria pai
        //$father_id = get_post_meta($collection_id, 'socialdb_collection_parent', true);//Pega id da coleção pai
        $mapping_id = $mappingModel->create_mapping(COLLECTION_MAPPING_MARC_FATHER, $collection_id);
        add_post_meta($collection_id, MAPPING_MARC_ID_FATHER, $mapping_id);
        add_post_meta($mapping_id, MAPPING_MARC_TABLE, serialize($father_data_info));

        //Adicionar pai para todas as coleções

        //Cria filho
        $mapping_id = $mappingModel->create_mapping(COLLECTION_MAPPING_MARC_SON.$collection_id, $collection_id);
        add_post_meta($collection_id, MAPPING_MARC_ID_SON, $mapping_id);
        add_post_meta($mapping_id, MAPPING_MARC_TABLE, serialize($son_data_info));
    }else
    {
        //Atualiza Pai
        $postMappingId = get_post_by_name(COLLECTION_MAPPING_MARC_FATHER, OBJECT ,'socialdb_channel')->ID;
        update_post_meta($postMappingId, MAPPING_MARC_TABLE, serialize($father_data_info));

        //Verifica se o filho existe
        if(get_post_by_name(COLLECTION_MAPPING_MARC_SON.$collection_id, OBJECT, "socialdb_channel") == null)//Não existe, criar filho
        {
            //print "filho não existe";
            $mapping_id = $mappingModel->create_mapping(COLLECTION_MAPPING_MARC_SON.$collection_id, $collection_id);
            add_post_meta($collection_id, MAPPING_MARC_ID_SON, $mapping_id);
            add_post_meta($mapping_id, MAPPING_MARC_TABLE, serialize($son_data_info));
        }else//Filho já existe, só atualizar filho
        {
            //print "filho já exite";
            $postMappingId = get_post_by_name(COLLECTION_MAPPING_MARC_SON.$collection_id, OBJECT ,'socialdb_channel')->ID;
            update_post_meta($postMappingId, MAPPING_MARC_TABLE, serialize($son_data_info));
        }
    }

    $return['result'] = true;
    if($return['result'])
    {
        $return['url'] = get_the_permalink($collection_id);;
    }

    return $return;
}

function import_marc()
{
    $elem  = [];
    $marc = $_POST['marc'];
    $collection_id = $_POST['collection_id'];


    $lines = split_lines($marc);
    $treated_lines = treat_lines($lines);

    $elem['result'] = add_material($collection_id, $treated_lines);
    if($elem['result'])
    {
        $elem['url'] = get_the_permalink($collection_id);
    }

    return $elem;
}

function get_marc_mapping($collection_id)
{
    $father_mapping_id = get_post_meta($collection_id, MAPPING_MARC_ID_FATHER, true);
    if(!$father_mapping_id)
    {
        $father_mapping_id = get_post_by_name(COLLECTION_MAPPING_MARC_FATHER, OBJECT, "socialdb_channel")->ID;
    }

    $son_mapping_id = get_post_meta($collection_id, MAPPING_MARC_ID_SON, true);
    $return = [];
    
    if($father_mapping_id)
    {
        $father_mapping = get_post_meta($father_mapping_id, MAPPING_MARC_TABLE, true);
        $return['father'] = unserialize($father_mapping);
        if($son_mapping_id)
        {
            $son_mapping = get_post_meta($son_mapping_id, MAPPING_MARC_TABLE, true);
            $return['son'] = unserialize($son_mapping);
        }
        else
        {
            $return['son'] = false;
        }
        
        return $return;
    }else
    {
        $return['father'] = false;
        $return['son'] = false;
    }
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

    return true;
}

function parse_save($object_id, $compound_id, $property_id, $value)
{
    $object_model = new ObjectModel();
    return $object_model->add_value_compound($object_id,$compound_id, $property_id, 0, 0, $value);
}

function just_numbers($str) {
    return preg_replace("/[^0-9]/", "", $str);
}

function just_letters($str) {
    return preg_replace("/[^a-z]/", "", $str);
}