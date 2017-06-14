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
 * Require de functions 
 */
require_once ('functions/marc_functions.php');
require_once ('functions/helpers_functions.php');

require_once ('mail/tainacan-library-mail.php');

/*
 * Adição de SCRIPTS
 */

//JavaScript
add_action('wp_enqueue_scripts', 'tainacan_libraries_js');
function tainacan_libraries_js() {
    wp_register_script('tainacan-library',
        get_template_directory_uri() . '/modules/' . MODULE_LIBRARY . '/libraries/js/tainacan-library.js', array('jquery'), '1.11');
    wp_register_script('barcode',
        get_template_directory_uri() . '/modules/' . MODULE_LIBRARY . '/libraries/js/barcode.js', array('jquery'), '1.11');
    $js_files[] = ['tainacan-library'];
    $js_files[] = ['barcode'];
    foreach ($js_files as $js_file):
        wp_enqueue_script($js_file);
    endforeach;
}

//CSS
add_action('wp_enqueue_scripts', 'tainacan_libraries_css');
function tainacan_libraries_css() {
    $registered_css = array(
        'tainacan-library' => '/libraries/css/tainacan-library.css'
    );
    
    foreach ($registered_css as $css_file => $css_path) {
        wp_register_style($css_file, get_template_directory_uri() . '/modules/' . MODULE_LIBRARY  . $css_path);
        wp_enqueue_style($css_file);
    }
}

/*
 * FILTERS and ACTIONS
 */

add_filter('addLibraryMenu', 'addLibraryMenu', 10, 1);
function addLibraryMenu($collection_id)
{

    ?>
    <div class="btn-group" role="group" aria-label="...">
        <div class="btn-group tainacan-add-wrapper">
            <?php
                if($son_mapping_id = get_post_meta($collection_id, MAPPING_MARC_ID_SON, true)){
            ?>
                <button type="button" class="btn btn-primary dropdown-toggle sec-color-bg" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php _e('Add', 'tainacan') ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a onclick="showAddItemText()"  style="cursor: pointer;"><?php _e('Item', 'tainacan') ?> </a></li>
                    <li><a onclick="showModalImportMarc()" style="cursor: pointer;" id="addfrommarc" ><?php _e('Add from MARC', 'tainacan') ?>  </a></li>
                </ul>
            <?php } else { ?>
                <button type="button" class="btn btn-primary dropdown-toggle sec-color-bg" aria-haspopup="true" onclick="showAddItemText()">
                    <?php _e('Add', 'tainacan') ?> </span>
                </button>
            <?php } ?>
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

    $marc_mapping_inverse = [];
    if(is_array($marc_mapping['father']))
    {
        foreach($marc_mapping['father'] as $compound_num => $subfields)
        {
            foreach ($subfields as $subfield => $prop_id)
            {
                if($subfield != 'compound_id')
                {
                    if(just_letters($subfield))
                    {
                        $simbol = '$';
                    }
                    else $simbol = '';

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
                        if(just_letters($subfield))
                        {
                            $simbol = '$';
                        }
                        else $simbol = '';

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
                    <!--SelectBox Compound-->
                    <div class='col-md-6 col-sm-12 meta-value'>
                        <select name="<?php echo $sub_properties['compound_id'].' '.$sub_properties['compound_id'] ?>" class='data form-control' id="<?= $compound_name ?>">
                            <?php
                                foreach ($all_marc_fields as $field)
                                {
                                    if($marc_mapping_inverse != false && strcmp($marc_mapping_inverse[$sub_properties['compound_id'].'_'.$sub_properties['compound_id']], $field) == 0)
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
                    if($name != 'compound_id'){
                        ?>
                        <div class="col-md-12 no-padding" style="margin: 10px 0 10px 0">
                            <label class='col-md-6 col-sm-12 meta-title no-padding' style="text-indent: 5%; padding-bottom: 15px; border-bottom: 1px solid #e8e8e8"> <?= $name?> </label>
                            <!--SelectBox SubField-->
                            <div class='col-md-6 col-sm-12 meta-value'>
                                <select name="<?php echo $id.' '.$sub_properties['compound_id'] ?>" class='data form-control' id="<?= $name ?>">
                                    <?php
                                    foreach ($all_marc_fields as $field) {
                                        if($marc_mapping_inverse != false && strcmp($marc_mapping_inverse[$id."_".$sub_properties['compound_id']], $field) == 0)
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
                        $category_parent = get_term_by("id", $id, 'socialdb_property_type')->parent;
                        if(strcmp(get_term_by("id", $category_parent, 'socialdb_property_type')->name, "socialdb_property_term") == 0)
                        {
                            $term_meta = get_term_meta($id ,'socialdb_property_term_root', true);
                            $term_children = get_term_children($term_meta, 'socialdb_category_type');
                            foreach ($term_children as $id_select_item)
                            {
                                $prop_name = get_term_by("id", $id_select_item,"socialdb_category_type")->name;
                                ?>
                                <div class="col-md-12 no-padding">
                                    <label class='col-md-6 col-sm-12 meta-title no-padding' style="text-indent: 8%; padding-bottom: 15px; border-bottom: 1px solid #e8e8e8"> <?php echo $prop_name ?> </label>
                                    <!--SelectBox SubField-->
                                    <div class='col-md-6 col-sm-12 meta-value'>
                                        <select name="<?php echo $id_select_item.' '.$sub_properties['compound_id'] ?>" class='data form-control' id="<?= $name ?>">
                                            <?php
                                            foreach ($all_marc_fields as $field) {
                                                if($marc_mapping_inverse != false && strcmp($marc_mapping_inverse[$id_select_item."_".$sub_properties['compound_id']], $field) == 0)
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

                        }
                        ?>
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

add_action('add_mapping_library_collections', 'mapping_library_collections');
function mapping_library_collections()
{
    $args = array(
        'posts_per_page' => 100,
        'post_type' => 'socialdb_collection',
    );
    $posts = get_posts($args);

    $posts_name_id = [];
    foreach($posts as $post)
    {
        $posts_name_id[$post->post_title] = $post->ID;
    }

    $should_be_mapped = array(
        'Cadastro de usuario',
        'Emprestimo',
        'Devoluções',
        'Reservas',
        'Livro',
        'Panfleto',
        'Manuscrito',
        'Tese',
        'Periodico',
        'Artigo',
        'Arquivo de computador',
        'Mapa',
        'Foto',
        'Filme',
        'Partitura',
        'Música',
        'Som não musical',
        'Objecto 3D',
        'Exemplares',
        'Autoridades',
        'Vocabulário',
        'Fornecedores',
        'Requisições',
        'Cotações',
        'Pedidos'
        );
    $mapping = get_option('socialdb_general_mapping_collection');
    ?>
    <h5 style="font-weight: bolder; margin-bottom: 2px;"> Mapeamento de coleções </h5>
    <?php
    foreach ($should_be_mapped as $name)
    {
        ?>
        <div class="col-md-12">
            <div class="col-md-6 no-padding" style="margin: 10px 0 10px 0; border-bottom: 1px solid #e8e8e8">
                <label class='meta-title no-padding' style=""><?php echo $name ?></label>
            </div>
            <div class="col-md-6">
                <select name="collections[<?php echo $name ?>]" id="<?php echo $name ?>" class='data form-control'>

                    <?php
                        foreach ($posts_name_id as $post_name => $id)
                        {
                            if( $mapping[$name] == $id)
                            {
                                $selected = 'selected';
                            }else $selected = '';

                            ?>
                                <option name='<?php echo $post_name ?>' value='<?php echo $id ?>' <?php echo $selected ?>><?php echo $post_name ?></option>
                            <?php
                        }
                    ?>
                </select>
            </div>
        </div>
        <?php
    }

    ?>
    <?php
    daily_situation_update();
}

add_filter('add_book_loan', 'book_loan', 10, 1);
function book_loan($data)
{
    $collection_id = $data['collection_id'];
    $mapping = get_option('socialdb_general_mapping_collection');
    $title = "Item indisponível";
    if($mapping['Emprestimo'] == $collection_id)
    {
        $type = "Indisponível";
        $msg = "Exemplar já emprestado.";
    }else if ($mapping['Devoluções'] == $collection_id)
    {
        $type = "Disponível";
        $msg = "Exemplar já devolvido.";
    }else $type = false;


    if($type)
    {
        $related_ids = get_related_id($data);
        foreach ($related_ids as $id)
        {
            $id = $id[0];
            $category_id = get_category_id(get_post($id)->post_parent, "Disponibilidade");
            if($category_id)
            {
                $disp_children = get_tainacan_category_children($category_id);
                $option_id = get_term_by('id', $disp_children[$type], 'socialdb_category_type')->term_id;
                $last_saved_option_id = last_option_saved($id, $category_id);

                if($last_saved_option_id != $disp_children[$type])
                {
                    remove_last_option($disp_children, $id);
                    wp_set_object_terms($id, $option_id, 'socialdb_category_type', true);
                }
                else
                {
                    $result['ok'] = false;
                    $result['title'] = $title;
                    $result['msg'] = $msg;
                    
                    return $result;
                }
            }
        }
    }
    $result['ok'] = true;
    return $result;
}

add_action('add_material_loan_devolution', 'material_loan_devolution');
function material_loan_devolution()
{
    $loantime = get_option('socialdb_loan_time');
    ?>
    <h5 style="font-weight: bolder; margin-bottom: 2px;"> <?php _e('Material loan and devolution', 'tainacan') ?> </h5>

    <div class="col-md-12">
        <div class="col-md-6 no-padding" style="margin: 10px 0 10px 0; border-bottom: 1px solid #e8e8e8">
            <label class='meta-title no-padding' style=""><?php _e('Default loan time (days)') ?></label>
        </div>

        <div class="col-md-6">
            <input class="data form-control" type="number" min='1' list="sugestions" name="default_time" value="<?php echo $loantime ?>" required>
            <datalist id="sugestions">
                <option value="7">
                <option value="8">
                <option value="9">
                <option value="10">
            </datalist>
        </div>
    </div>

    <div class="col-md-12">
        <div class="col-md-6 no-padding" style="margin: 10px 0 10px 0; border-bottom: 1px solid #e8e8e8">
            <label class='meta-title no-padding' style=""><?php _e('Devolution days') ?></label>
        </div>

        <?php
            $devolution_week_day = get_option('socialdb_devolution_weekday');
            $weekdays = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
        ?>
        <div class="col-md-6">
            <?php
                foreach ($weekdays as $weekday)
                {
                    if($devolution_week_day && key_exists($weekday, $devolution_week_day))
                    {
                        $checked = "checked";
                    }else $checked = "";

                    ?>
                    <label class="checkbox-inline"><input type="checkbox" value="true" name="weekday[<?php echo $weekday ?>]" <?php echo $checked ?>><?php _e($weekday, "tainacan"); ?></label>
                    <?php
                }
            ?>
        </div>
    </div>

    <div class="col-md-12">
        <div class="col-md-6 no-padding" style="margin: 10px 0 10px 0; border-bottom: 1px solid #e8e8e8">
            <label class='meta-title no-padding' style=""><?php _e('If devolution in a not devolution day') ?></label>
        </div>

        <?php
            $devolution_day_option = get_option('socialdb_devolution_day_problem');
        ?>

        <div class="col-md-6">
            <input type="radio" class="radio-inline" name="devolutionDayProblem" value="before" <?php if($devolution_day_option == 'before') echo "checked";?>> <?php _e("Before", "tainacan") ?>
            <input type="radio" class="radio-inline" name="devolutionDayProblem" value="after" <?php if($devolution_day_option == 'after') echo "checked"; ?>> <?php _e("After", "tainacan") ?>
        </div>
    </div>
    <?php
}

add_action('add_barcode', 'gen_barcode', 10, 2);
function gen_barcode($collection_id, $object_id)
{
    global $wpdb;
    $bar_number = barcode_number($object_id, 13);
    
    $tombo_id = get_collection_property_id($collection_id, "Tombo Patrimonial");
    $tombo_val = get_post_meta($object_id, 'socialdb_property_'.$tombo_id)[1];

    $classificacao_id = get_collection_property_id($collection_id, "Classificação");
    $classificacao_val = get_post_meta($object_id,'socialdb_property_'.$classificacao_id)[0];

    $codigo_autor_id = get_collection_property_id($collection_id, "Código do autor");
    $codigo_autor_val = get_post_meta($object_id,'socialdb_property_'.$codigo_autor_id)[0];

    $ed_vol_id = get_collection_property_id($collection_id, 'Edição / volume');
    $ed_vol_val = get_post_meta($object_id,'socialdb_property_'.$ed_vol_id)[0];

    $n_exemplar_id = get_collection_property_id($collection_id, 'Número do exemplar');
    $n_exemplar_val = get_post_meta($object_id,'socialdb_property_'.$n_exemplar_id)[0];

    ?>
    <div class="box-item-paddings box-item-right"></div> <!--Gera linha-->
    <input type="hidden" id="tombo_val" value="<?php echo $tombo_val; ?>">
    <div class="col-md-12">
        <div id="barcode-box">
            <h4 class="title-pipe single-title"> <?php _e('Barcode', 'tainacan'); ?></h4>
            <button type="button" onclick="print_barcode('barcode-img', $('#tombo_val').val());" class="btn btn-default btn-xs pull-right" style="margin-top: 5px;"><span class="glyphicon glyphicon-print"></span></button>
            <div id="barcode-img" class="barcode-img">
                <svg id="barcode"
                     jsbarcode-textmargin="0"
                     jsbarcode-fontoptions="bold"
                     style="width: 30%; height: 30%;">
                </svg>
                
                <script>
                    JsBarcode("#barcode", "<?php echo $bar_number; ?>", {
                        format: "ean13",
                        width: 3
                    });
                </script>
            </div>
            <button type="button" onclick="print_spine_label();" class="btn btn-default pull-right" style="position: relative; bottom: 30px; ">
                <span class="glyphicon glyphicon-print"></span>
                <?php _e("Print spine label", 'tainacan'); ?>
            </button>
        </div>
    </div>
    <?php
    if($classificacao_val)
    {
        ?>
        <input type="hidden" id="classificação" value="<?php echo $classificacao_val; ?>">
        <?php
    }

    if($codigo_autor_val)
    {
        ?>
        <input type="hidden" id="codigo_autor" value="<?php echo $codigo_autor_val; ?>">
        <?php
    }

    if($ed_vol_val)
    {
        ?>
        <input type="hidden" id="edi_vol" value="<?php echo $ed_vol_val; ?>">
        <?php
    }

    if($n_exemplar_val)
    {
        ?>
        <input type="hidden" id="exemplar" value="<?php echo $n_exemplar_val; ?>">
        <?php
    }
    ?>
    <?php
}

function barcode_number($number, $length_out)
{
    return sprintf("%0".$length_out."s",   $number);
}

add_action("add_users_button", "users_button");
function users_button()
{
    if(current_user_can('administrator'))
    {
        ?>
        <div class="nav navbar-nav navbar-right">
            <div class="users-button"
                 onclick="get_users_page('http://localhost/wordpress/biblioteca/wp-content/themes/tainacan', 'show_all_users')">
                <i class="fa fa-users" aria-hidden="true"></i>
                <?php _e("Users", "tainacan"); ?>
            </div>
        </div>
        <?php
    }
}

add_action("add_root_properties", "root_properties");
function root_properties()
{
    ?>
    <div class="form-group">
        <label for="user_type"><?php _e('User type', 'tainacan'); ?><span style="color: #EE0000;"> *</span></label>
        <select class="form-control" name="user_type" id="user_type">
            <option value="employee"><?php _e("Employee", "tainacan"); ?></option>
            <option value="reader"><?php _e("Reader", "tainacan"); ?></option>
        </select>
    </div>

    <div class="form-group">
        <label for="user_situation"><?php _e('User situation', 'tainacan'); ?><span style="color: #EE0000;"> *</span></label>
        <select class="form-control" name="user_situation" id="user_situation">
            <option value="active"><?php _e("Active", "tainacan"); ?></option>
            <option value="blocked"><?php _e("Blocked", "tainacan"); ?></option>
            <option value="pendencies"><?php _e("Has pendencies", "tainacan"); ?></option>
            <option value="inactive"><?php _e("Inactive", "tainacan"); ?></option>
        </select>
    </div>
    <?php
}

add_action("add_new_user_properties", "new_user_properties");
function new_user_properties()
{
    ?>
    <!-- Sexo -->
    <div class="form-group">
        <label for="gender"><?php _e('Gender', 'tainacan'); ?><!--span style="color: #EE0000;"> *</span--></label>
        <select class="form-control" name="gender" id="user_gender">
            <option value="m"><?php _e("Male", "tainacan"); ?></option>
            <option value="f"><?php _e("Female", "tainacan"); ?></option>
        </select>
    </div>

    <!-- Telefone celular    -->
    <div class="form-group">
        <label for="mobile_phone"><?php _e('Mobile phone', 'tainacan'); ?></label>
        <input class="form-control" type="tel" placeholder="<?php _e("Mobile phone", "tainacan"); ?>" name="mobile_phone" id="mobile_phone">
    </div>

    <!-- Telefone celular    -->
    <div class="form-group">
        <label for="land_line"><?php _e('Land line', 'tainacan'); ?></label>
        <input class="form-control" type="tel" placeholder="<?php _e("Land line", "tainacan"); ?>" name="land_line" id="land_line">
    </div>

    <!-- RG -->
    <div class="form-group">
        <label for="rg"><?php _e('RG', 'tainacan'); ?></label>
        <input class="form-control" type="" placeholder="<?php _e("RG", "tainacan"); ?>" name="rg" id="rg">
    </div>

    <!-- CPF -->
    <div class="form-group">
        <label for="cpf"><?php _e('CPF', 'tainacan'); ?></label>
        <input class="form-control" placeholder="<?php _e("CPF", "tainacan"); ?>" name="CPF" id="CPF">
    </div>

    <!-- CEP -->
    <div class="form-group">
        <label for="CEP"><?php _e('CEP', 'tainacan'); ?></label>
        <input class="form-control" type="" placeholder="<?php _e("CEP", "tainacan"); ?>" name="CEP" id="CEP">
    </div>


    <!-- Endereço -->
    <div class="form-group">
        <label for="address"><?php _e('Address', 'tainacan'); ?></label>
        <input class="form-control" type="text" placeholder="<?php _e("Address", "tainacan"); ?>" name="address" id="address">
    </div>

    <div class="form-group">
        <label for="number"><?php _e('Number', 'tainacan'); ?></label>
        <input class="form-control" type="number" placeholder="<?php _e("Number", "tainacan"); ?>" name="number" id="number">
    </div>

    <div class="form-group">
        <label for="additional_address"><?php _e('Additional address', 'tainacan'); ?></label>
        <input class="form-control" type="text" placeholder="<?php _e("Additional address", "tainacan"); ?>" name="additional_address" id="additional_address">
    </div>

    <!-- Data de nascimento -->
    <div class="form-group">
        <label for="birthday"><?php _e('Birthday', 'tainacan'); ?></label>
        <input class="form-control" type="date" placeholder="<?php _e("Birthday", "tainacan"); ?>" name="birthday" id="birthday">
    </div>

    <!-- Input das mascaras -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.min.js"></script>
    <script>
        $("#mobile_phone").mask('(00) 0-0000-0000');
        $("#land_line").mask('(00) 0000-0000');
        $("#CPF").mask( '000.000.000-00', {reverse: true} );
        $("#CEP").mask('00000-000');
        $("#rg").mask('00.000.000-0')
    </script>
    <?php
}

/*
 * Functions
 */

function daily_situation_update()
{
    global $wpdb;
    $mapping = get_option('socialdb_general_mapping_collection');
    $collection_id = $mapping['Emprestimo'];
    $event = "
        CREATE 
        EVENT IF NOT EXISTS
         e_daily_situation_update
        ON SCHEDULE
          EVERY 1 DAY_HOUR 
        COMMENT 'Altera o Status dos livros'
        DO
          BEGIN 
            SET SQL_SAFE_UPDATES = 0;
            UPDATE
                $wpdb->term_relationships
            SET
                term_taxonomy_id = 
                (
                    SELECT term_id FROM
                        (
                            SELECT t.term_id, t.name 
                            FROM 
                                $wpdb->terms t INNER JOIN $wpdb->term_taxonomy tt 
                            ON 
                                t.term_id = tt.term_id
                            WHERE 
                                tt.parent = (
                                    SELECT meta_value 
                                    FROM
                                        $wpdb->termmeta
                                    WHERE 
                                        term_id = 
                                            (SELECT tbterms.term_id
                                            FROM 
                                                $wpdb->term_taxonomy as tbtaxonomy, $wpdb->terms as tbterms 
                                            WHERE 
                                                tbtaxonomy.term_id in 
                                                (SELECT meta_value 
                                                 from 
                                                    $wpdb->termmeta 
                                                 WHERE 
                                                    term_id = (
                                                        SELECT 
                                                            meta_value 
                                                        from 
                                                            $wpdb->postmeta 
                                                        WHERE 
                                                            post_id = $collection_id AND meta_key LIKE 'socialdb_collection_object_type') 
                                                        AND 
                                                            meta_key LIKE 'socialdb_category_property_id') 
                                            AND 
                                                tbtaxonomy.taxonomy LIKE 'socialdb_property_type' 
                                            AND tbterms.name LIKE 'Situação da devolução'
                                            LIMIT 1)
                                        AND
                                            meta_key LIKE 'socialdb_property_term_root'
                                )  
                            ORDER BY 
                                tt.count DESC,
                                t.name ASC
                    
                    ) AS options
                    WHERE
                        options.name LIKE 'Atrasado'
                )
            WHERE
                object_id IN 
                (
                    SELECT post_id from ( 
                    SELECT post_id, meta_value AS DevolutionDay, curdate() as Today
                    FROM 
                        $wpdb->postmeta 
                    WHERE 
                        meta_key 
                    LIKE 
                        CONCAT('socialdb_property_', 
                        (SELECT tbterms.term_id AS data_dev
                        FROM 
                            $wpdb->term_taxonomy as tbtaxonomy, $wpdb->terms as tbterms 
                        WHERE 
                            tbtaxonomy.term_id in 
                            (SELECT meta_value 
                             from 
                                $wpdb->termmeta 
                             WHERE 
                                term_id = (
                                        SELECT 
                                            meta_value 
                                        from 
                                            $wpdb->postmeta 
                                        WHERE 
                                            post_id = $collection_id AND meta_key LIKE 'socialdb_collection_object_type'
                                        ) 
                                    AND 
                                        meta_key LIKE 'socialdb_category_property_id'
                            ) 
                        AND 
                            tbtaxonomy.taxonomy LIKE 'socialdb_property_type' 
                        AND tbterms.name LIKE 'Data de devolução'
                        LIMIT 1)
                    )
                    AND
                        meta_value != '') 
                    as Devolution where Devolution.Today > Devolution.DevolutionDay
                )
                AND
                term_taxonomy_id = 
                (
                    SELECT term_id FROM
                        (
                            SELECT t.term_id, t.name 
                            FROM 
                                $wpdb->terms t INNER JOIN $wpdb->term_taxonomy tt 
                            ON 
                                t.term_id = tt.term_id
                            WHERE 
                                tt.parent = (
                                    SELECT meta_value 
                                    FROM
                                        $wpdb->termmeta
                                    WHERE 
                                        term_id = 
                                            (SELECT tbterms.term_id
                                            FROM 
                                                $wpdb->term_taxonomy as tbtaxonomy, $wpdb->terms as tbterms 
                                            WHERE 
                                                tbtaxonomy.term_id in 
                                                (SELECT meta_value 
                                                 from 
                                                    $wpdb->termmeta 
                                                 WHERE 
                                                    term_id = (
                                                        SELECT 
                                                            meta_value 
                                                        from 
                                                            $wpdb->postmeta 
                                                        WHERE 
                                                            post_id = $collection_id AND meta_key LIKE 'socialdb_collection_object_type') 
                                                        AND 
                                                            meta_key LIKE 'socialdb_category_property_id') 
                                            AND 
                                                tbtaxonomy.taxonomy LIKE 'socialdb_property_type' 
                                            AND tbterms.name LIKE 'Situação da devolução'
                                            LIMIT 1)
                                        AND
                                            meta_key LIKE 'socialdb_property_term_root'
                                )  
                            ORDER BY 
                                tt.count DESC,
                                t.name ASC
                    
                    ) AS options
                    WHERE
                        options.name LIKE 'A tempo'
                )
            ;
            SET SQL_SAFE_UPDATES = 1;
          END;
    ";

    return $wpdb->query($event);
}
daily_situation_update();

function get_collection_property_id($collection_id, $property_name)
{
    $root_category = get_post_meta($collection_id, 'socialdb_collection_object_type', true);
    $properties = get_term_meta($root_category,'socialdb_category_property_id');
    $properties = array_unique($properties);
    foreach ($properties as $id)
    {
        $name = get_term_by('id', $id,'socialdb_property_type')->name;
        if(strcmp($property_name, $name) === 0)
        {
            return $id;
        }
    }


}

function search_for_user($user_name)
{
    $users = get_users();
    $users_found = [];
    $user_name = strtolower($user_name);

    foreach ($users as $user)
    {
        //print_r($user);break;
        $display_name = strtolower($user->data->display_name);
        $user_login = strtolower($user->data->user_login);
        
        if(strpos($display_name, $user_name) !== false || strpos($user_login, $user_name) !== false)
        {
            $cpf = get_user_meta($user->data->ID, 'CPF');
            $users_found[] = ['display_name' => $user->data->display_name,
                'user_login' => $user->data->user_login,
                'id' => $user->data->ID,
                'user_email' => $user->data->user_email,
                'cpf' => $cpf[0]
            ];
        }
    }
    
    /* Montando estrutura para exibição */
    ?>
    <ul class="list-inline">
        <?php
        foreach ($users_found as $user)
        {
            ?>
            <li class="col-md-2 user_result" style="cursor: pointer;" onclick="select_user(this);"
                data-id="<?php echo $user['id']; ?>"
                data-login="<?php echo $user['user_login']; ?>"
                data-email="<?php echo $user['user_email']; ?>"
                data-cpf="<?php echo $user['cpf'] ?>"
            >
                <?php echo $user['display_name']; ?>
            </li>
            <?php
        }
        ?>
    </ul>
    <?php
}

function last_option_saved($post_id, $option_id)
{
    $terms = wp_get_post_terms( $post_id, 'socialdb_category_type' );
    if($terms && is_array($terms)){
        foreach ($terms as $term) {
            $hierarchy = get_ancestors($term->term_id, 'socialdb_category_type');
            if(is_array($hierarchy) && in_array($option_id, $hierarchy)){
                return $term->term_id;
            }
        }
    }

    return false;
}

function remove_last_option($sub_options_children_ids, $post_id)
{
    $terms = wp_get_post_terms($post_id, 'socialdb_category_type');

    $_item_terms = [];
    foreach ($terms as $tm) {
        array_push($_item_terms, $tm->term_id);
    }

    $previous_set_ids = [];
    $available_children = [];
    foreach ($sub_options_children_ids as $ch)
    {
        $_int_id_ = intval($ch);
        if (in_array($_int_id_, $_item_terms))
        {
            $previous_set_ids[] = $_int_id_;
        }
        else
        {
            array_push($available_children, $_int_id_);
        }
    }

    foreach ($previous_set_ids as $previous_set_id)
    {
        wp_remove_object_terms($post_id, get_term_by('id', $previous_set_id, 'socialdb_category_type')->term_id, 'socialdb_category_type');
    }
}

function get_tainacan_category_children($parent_id) {
    global $wpdb;
    $data = [];
    $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
    $query = "SELECT * FROM $wpdb->terms t INNER JOIN $wp_term_taxonomy tt ON t.term_id = tt.term_id
				WHERE tt.parent = {$parent_id}  ORDER BY tt.count DESC,t.name ASC";
    $result = $wpdb->get_results($query);
    if ($result && !empty($result)) {
        foreach ($result as $term) {
            $data[$term->name] = $term->term_id;
        }
    }
    return $data;
}

function get_category_id($collection_id, $metaname, $is_root = true)
{
    $term_id = false;
    if($is_root)
    {
        $category_root_id = get_post_meta($collection_id, 'socialdb_collection_object_type', true);
        $ids = get_term_meta($category_root_id, "socialdb_category_property_id");
    }else
    {
        $ids = get_term_meta($collection_id, "socialdb_category_property_id");
    }
    foreach ($ids as $id)
    {
        if($id)
        {
            $name = get_term_by("id", $id, "socialdb_property_type")->name;
            if(strcmp($name,$metaname) == 0)
            {
                $term_id = get_term_meta($id, "socialdb_property_term_root", true);
                break;
            }
        }
    }

    return $term_id;
}

function get_related_id($data)
{
    $post_ids = [];
    $elem_ids = $data['properties_object_ids'];
    $elem_ids = explode(",", $elem_ids);

    foreach ($elem_ids as $id)
    {
        if(key_exists("socialdb_property_".$id, $data))
        {
            $post_ids[] = $data["socialdb_property_".$id];
        }
    }

    return $post_ids;
}

function meta_ids($collection_id, $change_names_for_numbers)
{
    $root_category = get_post_meta($collection_id, 'socialdb_collection_object_type', true);
    $properties = get_term_meta($root_category,'socialdb_category_property_id');
    $properties = array_unique($properties);

    $category_root_id = get_post_meta($collection_id, 'socialdb_collection_object_type', true);

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

function update_user_properties($data)
{
    $userID = $data['elemenID'];
    foreach($data as $index => $value)
    {
        update_user_meta($userID, $index, $value);
    }
    return true;
}

/*
 * Entrada: ID da coleção que se deseja obter os filhos
 * Retorno: ID de todos os filhos de uma determinada coleção
 */
function get_objects_children_from_collection($collection_id)
{
//    $args
//    get_posts()
}