<?php
/**
 * Modulo de Ontologia do Tainacan
 * 
 * #-2 - LINK PARA O MANUAL
 * #-1 - REMOVENDO O COLLECTION CATEGORIES DO DYNATREE DAS CATEGORIAS
 * #0 - Alterando valores do total de nos e o nome da categoria root do repositorio
 * #1 - ADICIONANDO OS SCRIPTS DESTE MODULO 
 * #2 - ALTERACOES HOME DO ITEM  
 * #3 - ALTERACOES CRIACAO DA COLECAO
 * #4 - BOTAO DE ADICAO DE ITENS/DE FACETAS
 * #5 - ACTIONS E FILTERS PARA ADICAO DE FILTROS
 * #6 - ADCIONA UMA OPCAO NO MODAL DE CATEGORIA
 * #7 - INSERCAO DE AXIOMAS (Campos na edicao de categoria)
 * #8 - ADICAO DE METADADOS
 * #9 - ALTERANDO O FORM DE INSERCAO DE PROPRIEDADES
 * #10 - ALTERANDO O Controller e o model de eventos das propriedades
 * #11 - REMOCAO DE CAMPOS DESNECESSÁRIOS
 * #12 - FILTROS PARA A EXPORTACAO DE ONTOLOGIAS (RDF)
 * #13 - ALTERACAO DO FORMULARIO DE ADICAO DE PROPRIEDADE DE DADOS
 * #14 - ALTERACAO DO FORMULARIO DE ADICAO DE PROPRIEDADE DE OBJETO
 * #15 - ALTERACAO DA PAGINA DO ITEM PARA PROPRIEDADE DE DADOS
 * #16 - ADICIONA O BOTAO DE EDITAR PROPRIEDADE NA PAGINA DA PROPRIEDADE
 * #17 - ADICIONA NO MENU DA COLECAO A OPCAO DE FILTROS
 * #18 - ALTERA O THUMBNAIL DOS ITEMS/COLECAO
 * #19 - OPERACAO DE METADADOS DE CATEGORIA
 * #20 - IMPORTADOR OWL e FUNÇÕES AUXILIARES
 * #21 - IMPORTAÇÃO MAPAS CULTURAIS
 * @author: EDUARDO HUMBERTO
 */


define('MODULE_ONTOLOGY', 'tainacan-ontology');
define('ONTOLOGY_CONTROLLERS', get_template_directory_uri() . '/modules/' . MODULE_ONTOLOGY );
load_theme_textdomain("tainacan", dirname(__FILE__) . "/languages");

###### #-2 Link Para o manual ########
function ontology_alter_link_manual($name) {
    return  get_template_directory_uri() . '/modules/' . MODULE_ONTOLOGY . '/extras/manual/Manual_Tainancan_Ontology.pdf';
}
add_filter( 'alter_link_manual', 'ontology_alter_link_manual', 10, 3 );

###### #-1 REMOVENDO O COLLECTION CATEGORIES DO DYNATREE DAS CATEGORIAS ########

function ontology_remove_collection_categories($name) {
    return true;
}
add_filter( 'remove_collection_categories', 'ontology_remove_collection_categories', 10, 3 );

################ #0 Alterando valores do total de nos ,  nome da categoria root do repositorio, liberando todas as propriedades para a geolocalizacao ##########################

function ontology_alter_dynatree_number_of_items($name) {
    return 50;
}
add_filter( 'alter_dynatree_number_of_items', 'ontology_alter_dynatree_number_of_items', 10, 3 );

function ontology_alter_category_root_repository_name($name) {
    return 'socialdb_taxonomy';
}
add_filter( 'alter_category_root_repository_name', 'ontology_alter_category_root_repository_name', 10, 3 );

add_action('all_properties_to_alter_geolocation_select', 'ontology_all_properties_to_alter_geolocation_select');
function ontology_all_properties_to_alter_geolocation_select() {
    return true;
}
################ #1 ADICIONANDO OS SCRIPTS DESTE MODULO ###########################
add_action('wp_enqueue_scripts', 'tainacan_ontology_js');
function tainacan_ontology_js() {
    wp_register_script('tainacan-ontology', 
            get_template_directory_uri() . '/modules/' . MODULE_ONTOLOGY . '/libraries/js/tainacan-ontology.js', array('jquery'), '1.11');
   $js_files = ['tainacan-ontology'];
    foreach ($js_files as $js_file):
        wp_enqueue_script($js_file);
    endforeach;
}

add_action('wp_enqueue_scripts', 'tainacan_ontology_css');
function tainacan_ontology_css() {
    $registered_css = [
          'tainacan-ontology' => '/libraries/css/tainacan-ontology.css'
      ];
    foreach ($registered_css as $css_file => $css_path) {
         wp_register_style($css_file, get_template_directory_uri() . '/modules/' . MODULE_ONTOLOGY  . $css_path);
         wp_enqueue_style($css_file);
    }
}
################################################################################
######################### #2 ALTERACOES HOME DO ITEM ##############################
/**
 * Acao que cria o container que mostra as classes do individuo
 */
add_action( 'home_item_insert_container', 'ontology_home_item_insert_container', 10, 1 );
function ontology_home_item_insert_container( $post_id) {
    if($post_id){
        ?>
        <div class="row">
            <h4 class="title-pipe">
                       <?php _e('Categories','tainacan') ?>
            </h4>
            <div class="col-md-12" id="ontology_classificiations_<?php echo $post_id ?>"></div>
            <script>
                get_classes_individuo('#ontology_classificiations_<?php echo $post_id ?>',<?php echo $post_id ?>);
            </script>
        </div>
        <hr>
        <?php
    }
}
################################################################################
######################### #3 ALTERACOES CRIACAO DA COLECAO ########################
/**
 * Filtro que retorna o nome a ser usado pelo objeto da colecao
 */
function alter_collection_object($name) {
    return trim(ucfirst($name));
}
add_filter( 'collection_object', 'alter_collection_object', 10, 3 );
/**
 * Filtro que bloqueia a criacao de facetas por default E cria outras
 */
function ontology_category_root_as_facet($collection_id) {
    add_post_meta($collection_id, 'socialdb_collection_facets', 'tree');
    update_post_meta($collection_id, 'socialdb_collection_facet_tree_widget', 'tree');
    update_post_meta($collection_id, 'socialdb_collection_facet_tree_priority', '1');
    add_post_meta($collection_id, 'socialdb_collection_facets', 'notifications');
    update_post_meta($collection_id, 'socialdb_collection_facet_notifications_widget', 'notifications');
    update_post_meta($collection_id, 'socialdb_collection_facet_notifications_priority', '4');
    add_post_meta($collection_id, 'socialdb_collection_facets', 'tree_property');
    update_post_meta($collection_id, 'socialdb_collection_facet_tree_property_widget', 'tree_property');
    update_post_meta($collection_id, 'socialdb_collection_facet_tree_property_priority', '2');
    add_post_meta($collection_id, 'socialdb_collection_facets', 'ranking_colaborations');
    update_post_meta($collection_id, 'socialdb_collection_facet_ranking_colaborations_widget', 'ranking_colaborations');
    update_post_meta($collection_id, 'socialdb_collection_facet_ranking_colaborations_priority', '3');
    return false;
}
add_filter( 'category_root_as_facet', 'ontology_category_root_as_facet', 10, 3 );
/**
 * Filtro que bloqueia a criacao de facetas por default
 */
function ontology_show_checkbox_facet($no_use_variable) {
    return false;
}
add_filter( 'show_checkbox_facet', 'ontology_show_checkbox_facet', 10, 3 );
/**
 * Acao que cria o dynatree sem a necessidade de existir facetas 
 */
add_action( 'before_facets', 'ontology_before_facets', 10, 2 );
function ontology_before_facets(array $facets,$collection_id) {
    if(!isset($facets)||empty($facets)&&$collection_id!= get_option('collection_root_id')){
        ?>
        <?php do_action('before_tree') ?>  
        <div class="form-group tainacan-default-tags">
            <!-- TAINACAN: panel para adicao de categorias e tags -->
            <label class="title-pipe"> 
                <?php _e('Categories','tainacan'); ?>
            </label>
            <div>
                <!-- TAINACAN: arvore montado nesta div pela biblioteca dynatree, html e css neste local totamente gerado pela biblioteca -->
                <div id="dynatree"></div>
            </div>
        </div>
        <?php
    }else if($collection_id== get_option('collection_root_id')){
        ?>
        <script>
        $('#div_left').hide();
                $('#div_central').removeClass('col-md-9');
                $('#div_central').removeClass('col-md-10');
                $('#div_central').removeClass('col-md-12');
                $('#div_central').addClass('col-md-12');
                $('#div_central').show();
                $('#div_left').html('');
        </script>         
        <?php
    }
    ?>
    <script>
        $("#accordion").accordion({
        collapsible: true,
        active: 0,
        header: "label",
        animate: 200,
        heightStyle: "content",
        icons: false
    });
    </script>  
    <?php    
   
}

################################################################################
######################### #4 BOTAO DE ADICAO/EDICAO DE ITENS/DE FACETAS ###########################
/**
 * Acao que mostra o botao personalizado de adicao de individuo
 */
function alter_button_add_item_ontology($string) {
    ?>
    <button style="display: none;" 
            onclick="show_form_add_item_ontology()"
            type="button" 
            class="btn btn-primary has-selected-class" >
    <?php _e('Add Individual','tainacan') ?>
    </button>
    <a  style="cursor: pointer;color: white;"
        id="add_item_popover"
        class="btn btn-primary popover_item none-selected-class" 
         >
           <?php _e('Add Individual','tainacan') ?>
     </a>
    <script>
        $('html').on('click', function(e) {
            if (typeof $(e.target).data('original-title') == 'undefined') {
              $('#add_item_popover').popover('hide');
            }
        });
        $('#add_item_popover').popover({ 
           html : true,
           placement: 'left',
           title: '<?php echo _e('Add item in the collection','tainacan') ?>',
           content: function() {
             return $("#popover_content_add_item").html();
           }
        });
    </script>
    <div id="popover_content_add_item" class="hide">
        <form class="form-inline"  style="font-size: 12px;width: 300px;">
            <center>
             <span class="glyphicon glyphicon-arrow-left"></span>&nbsp;<?php _e('Select at the tree an individue class ','tainacan') ?>
             <br>
             <button type="button" 
                     onclick="show_form_add_item_ontology()"
                    class="btn btn-primary btn-xs">
                        <?php _e('Or create a class instance of owl:thing','tainacan') ?></button>
            </center>
        </form>
    </div> 
    
    <?php
}
add_action( 'show_custom_add_item_button', 'alter_button_add_item_ontology', 10, 3 );
/**
 * Filtro que mostra a view de edicao default
 */
function show_edit_default_ontology($collection_id) {
    return true;
}
add_filter( 'show_edit_default', 'show_edit_default_ontology', 10, 3 );
/**
 * Insere o botao para ser adicionado as facetas
 */
add_action('before_tree', 'insert_button_facets');
function insert_button_facets() {
   $link = "'" . get_template_directory_uri() . '/modules/' . MODULE_ONTOLOGY . "'";
    ?>
    <div class="btn-group" role="group" aria-label="...">
        <div class="btn-group tainacan-add-wrapper">
            <button style="margin-left:15px;margin-top:-30px;font-size: 14px;text-indent: 5%;font-weight: normal;" 
                    type="button" 
                    class="btn btn-default dropdown-toggle" 
                    data-toggle="dropdown" 
                    aria-haspopup="true" 
                    aria-expanded="false">
                <span class="glyphicon glyphicon-plus-sign"></span>&nbsp;
                <?php _e('Add', 'tainacan') ?> <span class="caret"></span>
            </button>
            <ul style="margin-left:15px;" class="dropdown-menu">
                <li><a onclick="showModalFilters('add_category','<?php echo __('Category','tainacan') ?>');" style="cursor: pointer;"><?php echo __('Category','tainacan')  ?></a></li>
                <li><a onclick="showPageCreateProperty(0,<?php echo $link; ?>)" style="cursor: pointer;" ><?php _e('Property', 'tainacan') ?></a></li>
           </ul>
        </div>
    </div>
    
    
    <?php
    
    
    
//    echo '<button style="margin-left:15px;margin-top:-30px;" class="btn btn-default btn-xs" onclick="';
//    echo "showModalFilters('add_category','".__('Category','tainacan')."');";
//    echo '">';
//    echo '<span class="glyphicon glyphicon-plus-sign"></span>&nbsp;<span style="font-size: 12px;text-indent: 5%;font-weight: normal;">';
//    echo __('Add Category','tainacan');
//    echo '</span></button><!--/center-->';                               
}

/**
 * Filtro que mostra no dymatree de edicao de categoria a categoria raiz
 */
function do_show_category_root_in_edit_category($is_showed) {
    return true;
}
add_filter( 'show_category_root_in_edit_category', 'do_show_category_root_in_edit_category', 10, 3 );
/**
 *  acao para alterar o label do titulo da adicao
 */
add_action('label_add_item', 'insert_individual_title_default');
function insert_individual_title_default() {
    echo __('Add Individual','tainacan');                              
}
/**
 *  acao para alterar o label do titulo da edicao
 */
add_action('label_edit_item', 'insert_edit_individual_title_default');
function insert_edit_individual_title_default() {
    echo __('Edit Individual','tainacan');                              
}
/**
 * Opcional mensagem
 */
add_action('optional_message', 'ontology_optional_message');
function ontology_optional_message() {
    echo '<span style="font-size:11px;">('. __('Optional','tainacan').')</span>';                              
}
######################### #5 ACTIONS E FILTERS PARA ADICAO DE FILTROS #############
/**
 * adiciona labels para o selectobox de facetas padroes
 */
add_action('add_standart_options_label_filters', 'ontology_add_standart_options_label_filters');
function ontology_add_standart_options_label_filters() {
    echo '<option value="tree_property">'. __('Properties','tainacan').'</option>';                              
}
/**
 * adiciona um optiongroup para as classes para o selectobox de facetas padroes
 */
add_action('add_optiongroup_label_filters', 'ontology_add_optiongroup_label_filters',10, 2);
function ontology_add_optiongroup_label_filters($collection_id,$array_data) {
    $facets = get_post_meta($collection_id,'socialdb_collection_facets');
    $ids_properties_term =[];
    if($array_data['property_term']){
        foreach ($array_data['property_term'] as $property) {
            $term = get_term_by('id', $property['metas']['socialdb_property_term_root'], 'socialdb_category_type');
            $ids_properties_term[] = $term->term_id;
        } 
    }
    echo '<optgroup id="classes_filters" label="'.__('Categories','tainacan').'">';  
    if($facets&&is_array($facets)){
        foreach ($facets as $facet) {
            $term = get_term_by('id', $facet,'socialdb_category_type');
            if($term&&!in_array($facet, $ids_properties_term)){
                 echo '<option value="'.$facet.'">'.$term->name.'</option>';   
            }
        }
    }
    echo '<optgroup>';                            
}

/**
 * adiciona o widget no menu left
 */
add_action( 'add_widget', 'ontology_add_widget', 10, 1 );
function ontology_add_widget(array $facet) {
    if ($facet['widget'] == 'tree_property'):     
        $link = "'" . get_template_directory_uri() . '/modules/' . MODULE_ONTOLOGY . "'";
        ?>
        <script type="text/javascript">
            initDynatreeFilterProperties(<?php echo $link ?>);
        </script>
        <div class="form-group">
            <label for="notifications" class="title-pipe"> <?php echo $facet['name']; ?></label>
            <div id="dynatree_properties_filter" style="max-height: 300px;overflow-y: scroll;">
            </div>
        </div>   
    <?php endif;                             
}
/**
 * este filtro recebe o id da propreidade/filtro para retornar as opções de 
 * visualização na edicao de filtros
 */
function ontology_add_widgets_filters($property_id) {
    if($property_id=='tree_property'){
       return ['tree_property'=>__('Tree Property','tainacan')];
    }else{
        $options = [];
        $options['0'] = __('Select...','tainacan');  
        $options['tree'] = __('Tree','tainacan');
//        $options['menu'] = __('Menu','tainacan');
//        $options['radio'] = __('Radio Button','tainacan');
//        $options['checkbox'] = __('Check Button','tainacan');
//        $options['selectbox'] = __('Select Box','tainacan');
//        $options['multipleselect'] = __('Multiple Select','tainacan');
      return $options;
    }
}
add_filter( 'add_widgets_filters', 'ontology_add_widgets_filters', 10, 3 );
/**
 * @uses visualization_model.php Diferentemente da anterior este filtro 
 * busca dados para serem mostrados na 
 * view menu left 
 */
function ontology_add_widgets_filters_in_view($property_id) {
    if($property_id=='tree_property'){
       return ['id'=>$property_id,'name'=>__('Properties','tainacan'),'widget'=>'tree_property','priority'=>2];
    }else{
        return false;
    }
}
add_filter( 'add_widgets_filters_in_view', 'ontology_add_widgets_filters_in_view', 10, 3 );
/**
 * @uses searc_model.php retorna o nome a ser usado na view edit.php dos filtros 
 * nome a ser mostrado na tabela de cadastrados
 */
function ontology_get_filter_name($property_id) {
    if($property_id=='tree_property'){
       return __('Properties','tainacan');
    }else{
        return false;
    }
}
add_filter( 'get_filter_name', 'ontology_get_filter_name', 10, 3 );

################################################################################

######################### #6 ADCIONA UMA OPCAO NO MODAL DE CATEGORIA ###########
add_action('add_option_in_add_category', 'ontology_add_option_in_add_category');
function ontology_add_option_in_add_category() {
    echo '<a style="margin-left: 45%;cursor:pointer" onclick="show_select_taxonomy();">
                                 '. __(' Or reuse a taxonomy','tainacan').'
                             </a>';                              
}
/**
 * @uses single.php permite adicionar um form extra na adicao de categorias
 */
add_action('show_option_in_add_category', 'ontology_show_option_in_add_category');
function ontology_show_option_in_add_category() {
    $link = "'" . get_template_directory_uri() . '/modules/' . MODULE_ONTOLOGY . "'";
    ?>
    <div  id="dynatree_select_taxonomies" style="height:250px;overflow-y: scroll;"></div>
    <div class="modal-footer">
                    <button type="button" class="btn btn-default" onclick="hide_select_taxonomy();"><?php echo __('Back', 'tainacan'); ?></button>
                    <button type="button" class="btn btn-primary" onclick="ontology_vinculate_taxonomy(<?php echo $link ?>,'<?php _e('Attention!','tainacan') ?>','<?php _e('Add, in this collection, the taxonomy: ','tainacan') ?>');">
                        <?php echo __('Add', 'tainacan'); ?>
                    </button>
    </div>   

    <script type="text/javascript">
                initDynatreeSelectTaxonomy(<?php echo $link ?>);
    </script>
    <?php    
}
################################################################################


######################## #7 INSERCAO DE AXIOMAS ###################################
/**
 *  Adicionando o dynatree personalizado no modal de edicao 
 * @uses single.php 
 */
add_action('insert_new_contextmenu_dynatree', 'ontology_insert_new_contextmenu_dynatree');
function ontology_insert_new_contextmenu_dynatree() {
    ?>
    <ul id="PropertyMenu" class="contextMenu" style="display:none;">
        <li class="add" >
            <a href="#add" style="background-position: 6px 50%;padding:1px 5px 1px 28px;background-repeat:no-repeat;background-image:url('<?php echo get_template_directory_uri() ?>/libraries/css/images/1462491942_page_white_add.png')">
                <?php echo __('Add', 'tainacan'); ?>
            </a>
        </li> 
        <li class="edit">
            <a href="#edit"><?php echo __('Edit', 'tainacan'); ?></a>
        </li>  
        <li class="delete">
            <input type="hidden" id="title_delete_property" value="<?php _e('Attention!','tainacan')?>">
            <input type="hidden" id="msg_delete_property" value="<?php _e('Delete the property:','tainacan')?>">
            <a href="#delete"><?php echo __('Delete', 'tainacan'); ?></a>
        </li>  
    </ul>
    <ul id="ontologyMenu" class="contextMenu" style="display:none;">
        <li class="equivalent">
            <a href="#equivalentclassAdd"><?php echo __('Add as Equivalent', 'tainacan'); ?></a>
        </li>  
        <li class="disjoint">
            <a href="#disjointwithAdd"><?php echo __('Add as Disjoint', 'tainacan'); ?></a>
        </li>
        <li class="unionof">
            <a href="#unionof"><?php echo __('Add as UnionOf', 'tainacan'); ?></a>
        </li>
        <li class="intersection">
            <a href="#intersectionof"><?php echo __('Add as Intersection Of', 'tainacan'); ?></a>
        </li>
        <li class="complementof">
            <a href="#complementof"><?php echo __('Add as Complement Of', 'tainacan'); ?></a>
        </li>
    </ul>
    <?php
}

/**
 * Adicionando o context menu para o dynatree criado
 * @uses single.php 
 */
add_action('insert_custom_dynatree_edit_category', 'ontology_insert_custom_dynatree_edit_category');
function ontology_insert_custom_dynatree_edit_category() {
    ?>
    <div id="ontology_dynatree_modal_edit">
    </div>
     <script type="text/javascript">
            reInitDynatree();
    </script>
    <?php
}
/**
 * Adicionando os campos no modal de EDICAO de categoria 
 * @uses single.php 
 */
add_action('insert_fields_edit_modal_category', 'ontology_insert_fields_edit_modal_category');
function ontology_insert_fields_edit_modal_category() {
    ?>
    <div  style="padding-bottom: 3px;">
        <a onclick="toggleSlide('ontology_categories_fields','form_simple_eidt_category');" style="cursor: pointer;">
                <span><?php _e('Advanced Options','tainacan')  ?></span> 
                <span class="glyphicon glyphicon-triangle-bottom"></span>
        </a>
    </div>
    <div id="ontology_categories_fields" style="display: none;">
        <div class="create_form-group">
            <label for="category_single_edit_name"><?php _e('Equivalent Categories', 'tainacan'); ?></label>
            <div id="equivalentclass_categories_droppable" class="form-control" style="overflow-y:scroll; height: 50px;  border: 2px solid #73AD21">

            </div>
            <input type="hidden" id="add_equivalentclass_ids" name="socialdb_event_term_equivalentclass" value="">
            <?php ?>
            <!--input type="text" class="form-control" id="category_single_edit_name" name="socialdb_event_term_suggested_name" required="required" placeholder="<?php _e('Category name', 'tainacan'); ?>">
            <input type="hidden"  id="socialdb_event_previous_name"  name="socialdb_event_term_previous_name" value="0" -->
        </div>    
        <div class="create_form-group">
            <label for="category_single_edit_name"><?php _e('Disjoint Categories', 'tainacan'); ?></label>
            <div id="disjointwith_categories_droppable" class="form-control" style="overflow-y:scroll; height: 50px;  border: 2px solid #73AD21">

            </div>
            <input type="hidden" id="add_disjointwith_ids" name="socialdb_event_term_disjointwith" value="">
        </div>
        <!-- Union Of -->
        <div class="create_form-group">
            <label for="category_single_edit_name"><?php _e('Union Of', 'tainacan'); ?></label>
            <div id="unionof_categories_droppable" class="form-control" style="overflow-y:scroll; height: 50px;  border: 2px solid #73AD21">

            </div>
            <input type="hidden" id="add_unionof_ids" name="socialdb_event_term_unionof" value="">
            <?php ?>
            <!--input type="text" class="form-control" id="category_single_edit_name" name="socialdb_event_term_suggested_name" required="required" placeholder="<?php _e('Category name', 'tainacan'); ?>">
            <input type="hidden"  id="socialdb_event_previous_name"  name="socialdb_event_term_previous_name" value="0" -->
        </div>    
        <!-- Intersection Of -->
        <div class="create_form-group">
            <label for="category_single_edit_name"><?php _e('Intersection Of', 'tainacan'); ?></label>
            <div id="intersectionof_categories_droppable" class="form-control" style="overflow-y:scroll; height: 50px;  border: 2px solid #73AD21">

            </div>
            <input type="hidden" id="add_intersectionof_ids" name="socialdb_event_term_intersectionof" value="">
        </div>
        <!-- Complement of -->
        <div class="create_form-group">
            <label for="category_single_edit_name"><?php _e('Complement Of', 'tainacan'); ?></label>
            <div id="complementof_categories_droppable" class="form-control" style="overflow-y:scroll; height: 50px;  border: 2px solid #73AD21">

            </div>
            <input type="hidden" id="add_complementof_ids" name="socialdb_event_term_complementof" value="">
        </div>
    </div>    
    <br>  
    
    <script type="text/javascript">
            reInitDynatree();
    </script>
    <?php    
}
/******************************************************************************/
/**** Adicionando o pedaco de script para retornar os dados ja cadastrados ****/
add_action('javascript_metas_category', 'ontology_javascript_metas_category');
function ontology_javascript_metas_category() {
    echo 
    '$("#ontology_dynatree_modal_edit").dynatree("getTree").reload();'
    .'clear_ontology_fields_category();'
    . 'set_fields_modal_categories(elem);';
}
/******************************************************************************
**
 * @uses searc_model.php retorna o nome a ser usado na view edit.php dos filtros 
 * nome a ser mostrado na tabela de cadastrados
 */
function ontology_modificate_returned_metas_categories($data) {
    if($data['all_metas']
            &&isset($data['config']['socialdb_category_disjointwith'])&&isset($data['config']['socialdb_category_equivalentclass'])){
        unset($data['config']['socialdb_category_disjointwith']);
        unset($data['config']['socialdb_category_equivalentclass']);
        unset($data['config']['socialdb_category_unionof']);
        unset($data['config']['socialdb_category_intersectionof']);
        unset($data['config']['socialdb_category_complementof']);
    }else{
        return $data['config'];
    }
    foreach ($data['all_metas'] as $category_data) {
            if (($category_data->meta_key == 'socialdb_category_equivalentclass') && $category_data->meta_value != '') {
                $data['config'][$category_data->meta_key][] = get_term_by('id', $category_data->meta_value, 'socialdb_category_type');
            } elseif ($category_data->meta_key == 'socialdb_category_disjointwith' && $category_data->meta_value != '') {
                $data['config'][$category_data->meta_key][] = get_term_by('id', $category_data->meta_value, 'socialdb_category_type');
            } elseif ($category_data->meta_key == 'socialdb_category_unionof' && $category_data->meta_value != '') {
                $data['config'][$category_data->meta_key][] = get_term_by('id', $category_data->meta_value, 'socialdb_category_type');
            } elseif ($category_data->meta_key == 'socialdb_category_intersectionof' && $category_data->meta_value != '') {
                $data['config'][$category_data->meta_key][] = get_term_by('id', $category_data->meta_value, 'socialdb_category_type');
            } elseif ($category_data->meta_key == 'socialdb_category_complementof' && $category_data->meta_value != '') {
                $data['config'][$category_data->meta_key][] = get_term_by('id', $category_data->meta_value, 'socialdb_category_type');
            } 
    }
    return $data['config'];
}
add_filter( 'modificate_returned_metas_categories', 'ontology_modificate_returned_metas_categories', 10, 3 );
/******************************************************************************/
/**** Adicionando o pedaco de script para retornar os dados ja cadastrados ****/
/**
 * @uses single.php 
 */
add_action('after_event_edit_term', 'ontology_after_event_edit_term', 10, 1);
function ontology_after_event_edit_term($event_id) {
    $category_id = get_post_meta($event_id, 'socialdb_event_term_id',true) ;
    if(strpos($category_id, '_facet_category')!==false){
             $category_id = str_replace('_facet_category', '', $category_id);
    }
    //classes equivalentes
    $equivalentclass = get_post_meta($event_id, 'socialdb_event_term_equivalentclass',true) ;
    if($equivalentclass&&!empty(trim($equivalentclass))){
        delete_term_meta($category_id, 'socialdb_category_equivalentclass');
        $array = explode(',', $equivalentclass);
        foreach ($array as $value) {
            if(trim($value)!=''){
                add_term_meta($category_id, 'socialdb_category_equivalentclass', trim($value));
            }
        }
    }
    // classes disjuntas
    $disjointwith = get_post_meta($event_id, 'socialdb_event_term_disjointwith',true) ;
    if($disjointwith&&!empty(trim($disjointwith))){
        delete_term_meta($category_id, 'socialdb_category_disjointwith');
        $array = explode(',', $disjointwith);
        foreach ($array as $value) {
            if(trim($value)!=''){
                add_term_meta($category_id, 'socialdb_category_disjointwith', trim($value));
            }
        }
    }
    // construtor de união
    $unionof = get_post_meta($event_id, 'socialdb_event_term_unionof',true) ;
    if($unionof&&!empty(trim($unionof))){
        delete_term_meta($category_id, 'socialdb_category_unionof');
        $array = explode(',', $unionof);
        foreach ($array as $value) {
            if(trim($value)!=''){
                add_term_meta($category_id, 'socialdb_category_unionof', trim($value));
            }
        }
    }
    // construtor de intersecção
    $intersectionof = get_post_meta($event_id, 'socialdb_event_term_intersectionof',true) ;
    if($intersectionof&&!empty(trim($intersectionof))){
        delete_term_meta($category_id, 'socialdb_category_intersectionof');
        $array = explode(',', $intersectionof);
        foreach ($array as $value) {
            if(trim($value)!=''){
                add_term_meta($category_id, 'socialdb_category_intersectionof', trim($value));
            }
        }
    }
    // construtor de complemento
    $complementof= get_post_meta($event_id, 'socialdb_event_term_complementof',true) ;
    if($complementof&&!empty(trim($complementof))){
        delete_term_meta($category_id, 'socialdb_category_complementof');
        $array = explode(',', $complementof);
        foreach ($array as $value) {
            if(trim($value)!=''){
                add_term_meta($category_id, 'socialdb_category_complementof', trim($value));
            }
        }
    }
    
}
######################## FIM:INSERCAO DE AXIOMAS ###############################
######################## #8 ADICAO DE METADADOS ###################################

/********** Adicionando os metadados default diretamente na categoria raiz ****/
add_action( 'add_new_metas_category', 'ontology_add_category_metas', 10, 1 );
function ontology_add_category_metas($category_root_term) {
    create_metas($category_root_term['term_id'], 'socialdb_category_metas', 'socialdb_category_disjointwith', 'socialdb_category_disjointwith');
    create_metas($category_root_term['term_id'], 'socialdb_category_metas', 'socialdb_category_equivalentclass', 'socialdb_category_equivalentclass');
    create_metas($category_root_term['term_id'], 'socialdb_category_metas', 'socialdb_category_unionof', 'socialdb_category_unionof');
    create_metas($category_root_term['term_id'], 'socialdb_category_metas', 'socialdb_category_intersectionof', 'socialdb_category_intersectionof');
    create_metas($category_root_term['term_id'], 'socialdb_category_metas', 'socialdb_category_complementof', 'socialdb_category_complementof');
}
/******************************************************************************/
/********** Adicionando os metadados nos eventos de CRIACAO DE TERMO ****/
add_action( 'add_new_metas_event_create_category', 'ontology_add_new_metas_event_create_category', 10, 1 );
function ontology_add_new_metas_event_create_category($event_create_term) {
    create_metas($event_create_term['term_id'], 'socialdb_event_term_create_metas', 'socialdb_event_term_disjointwith', 'socialdb_event_term_disjointwith');
    create_metas($event_create_term['term_id'], 'socialdb_event_term_create_metas', 'socialdb_event_term_equivalentclass', 'socialdb_event_term_equivalentclass');
    create_metas($event_create_term['term_id'], 'socialdb_event_term_create_metas', 'socialdb_event_term_unionof', 'socialdb_event_term_unionof');
    create_metas($event_create_term['term_id'], 'socialdb_event_term_create_metas', 'socialdb_event_term_intersectionof', 'socialdb_event_term_intersectionof');
    create_metas($event_create_term['term_id'], 'socialdb_event_term_create_metas', 'socialdb_event_term_complementof', 'socialdb_event_term_complementof');
}
/******************************************************************************/
/********** Adicionando os metadados nos eventos de EDICAO DE TERMO ****/
add_action( 'add_new_metas_event_edit_category', 'ontology_add_new_metas_event_edit_category', 10, 1 );
function ontology_add_new_metas_event_edit_category($event_edit_term) {
    create_metas($event_edit_term['term_id'], 'socialdb_event_term_edit_metas', 'socialdb_event_term_disjointwith', 'socialdb_event_term_disjointwith');
    create_metas($event_edit_term['term_id'], 'socialdb_event_term_edit_metas', 'socialdb_event_term_equivalentclass', 'socialdb_event_term_equivalentclass');
     create_metas($event_edit_term['term_id'], 'socialdb_event_term_edit_metas', 'socialdb_event_term_unionof', 'socialdb_event_term_unionof');
    create_metas($event_edit_term['term_id'], 'socialdb_event_term_edit_metas', 'socialdb_event_term_intersectionof', 'socialdb_event_term_intersectionof');
    create_metas($event_edit_term['term_id'], 'socialdb_event_term_edit_metas', 'socialdb_event_term_complementof', 'socialdb_event_term_complementof');
}
/******************************************************************************/
/********** Adicionando os metadados em property (RAIZ) ****/
add_action( 'add_new_metas_property', 'ontology_add_property_metas', 10, 1 );
function ontology_add_property_metas($property_root_term) {
    create_metas($property_root_term['term_id'], 'socialdb_property_metas', 'socialdb_property_mincardinalidality', 'socialdb_property_mincardinalidality');
    create_metas($property_root_term['term_id'], 'socialdb_property_metas', 'socialdb_property_maxcardinalidality', 'socialdb_property_maxcardinalidality');
    create_metas($property_root_term['term_id'], 'socialdb_property_metas', 'socialdb_property_cardinalidality', 'socialdb_property_cardinalidality');
    create_metas($property_root_term['term_id'], 'socialdb_property_metas', 'socialdb_property_equivalent', 'socialdb_property_equivalent');
    create_metas($property_root_term['term_id'], 'socialdb_property_metas', 'socialdb_property_allvaluesfrom', 'socialdb_property_allvaluesfrom');
    create_metas($property_root_term['term_id'], 'socialdb_property_metas', 'socialdb_property_somevaluesfrom', 'socialdb_property_somevaluesfrom');
    create_metas($property_root_term['term_id'], 'socialdb_property_metas', 'socialdb_property_hasvalue', 'socialdb_property_hasvalue');
    create_metas($property_root_term['term_id'], 'socialdb_property_metas', 'socialdb_property_functional', 'socialdb_property_functional');
}
/********** Adicionando os metadados em property data****/
add_action( 'add_new_metas_property_object', 'ontology_add_property_object_metas', 10, 1 );
function ontology_add_property_object_metas($property_object_root_term) {
    create_metas($property_object_root_term['term_id'], 'socialdb_property_object_metas', 'socialdb_property_object_transitive', 'socialdb_property_object_transitive');
    create_metas($property_object_root_term['term_id'], 'socialdb_property_object_metas', 'socialdb_property_object_simetric', 'socialdb_property_object_simetric');
}
/**
 * Adicionando os metadados <b>COMUNS</b> aos eventos de propriedade de dados e objeto 
 */
add_action( 'add_new_metas_event_property_data', 'ontology_add_new_metas_event_properties', 10, 2 );
add_action( 'add_new_metas_event_property_object', 'ontology_add_new_metas_event_properties', 10, 2 );
function ontology_add_new_metas_event_properties($event_property,$type) {
    create_metas($event_property['term_id'], $type, 'socialdb_event_property_description', 'socialdb_event_property_description');
    create_metas($event_property['term_id'], $type, 'socialdb_event_property_parent', 'socialdb_event_property_parent');
    create_metas($event_property['term_id'], $type, 'socialdb_event_property_mincardinalidality', 'socialdb_event_property_mincardinalidality');
    create_metas($event_property['term_id'], $type, 'socialdb_event_property_maxcardinalidality', 'socialdb_event_property_maxcardinalidality');
    create_metas($event_property['term_id'], $type, 'socialdb_event_property_cardinalidality', 'socialdb_event_property_cardinalidality');
    create_metas($event_property['term_id'], $type, 'socialdb_event_property_equivalent', 'socialdb_event_property_equivalent');
    create_metas($event_property['term_id'], $type, 'socialdb_event_property_allvaluesfrom', 'socialdb_event_property_allvaluesfrom');
    create_metas($event_property['term_id'], $type, 'socialdb_event_property_somevaluesfrom', 'socialdb_event_property_somevaluesfrom');
    create_metas($event_property['term_id'], $type, 'socialdb_event_property_hasvalue', 'socialdb_event_property_hasvalue');
    create_metas($event_property['term_id'], $type, 'socialdb_event_property_functional', 'socialdb_event_property_functional');
    if($type=='socialdb_event_property_object_metas'){
        create_metas($event_property['term_id'], $type, 'socialdb_event_property_transitive', 'socialdb_event_property_transitive');
        create_metas($event_property['term_id'], $type, 'socialdb_event_property_simetric', 'socialdb_event_property_simetric');
    }
}

######################## fim: ADICAO DE METADADOS ##############################
######### #9 ALTERANDO O FORM DE INSERCAO DE PROPRIEDADES #########################

/********** DADOS: Adicionando os campos no form de propriedades de dados *************/
add_action('form_help_property_data_insert_types', 'ontology_form_help_property_data_insert_types');
function ontology_form_help_property_data_insert_types() {
    ?>
    <option value="string"><?php _e('String','tainacan'); ?></option>
    <option value="boolean"><?php _e('Boolean','tainacan'); ?></option>
    <option value="date"><?php _e('Date','tainacan'); ?></option>
    <option value="datetime"><?php _e('Datetime','tainacan'); ?></option>
    <option value="float"><?php _e('Float','tainacan'); ?></option>
    <option value="int"><?php _e('Integer','tainacan'); ?></option>
    <option value="time"><?php _e('Time','tainacan'); ?></option>
    <?php
}
/********** Adicionando os campos no form de propriedades de dados *************/
/**
 * DATA
 * formulario de adicao e edicao de propriedade de dados para ontologia
 * 
 * @uses list.php 
 */
add_action('form_modify_property_data', 'ontology_form_modify_property_data');
function ontology_form_modify_property_data() {
    ?>
    <div  style="padding-bottom: 3px;margin-top: -10px;">
        <a onclick="toggleSlide('ontology_property_data_advanced_fields');" style="cursor: pointer;">
                <span><?php _e('Advanced Options','tainacan')  ?></span> 
                <span class="glyphicon glyphicon-triangle-bottom"></span>
        </a>
    </div>
    <br>
    <div id="ontology_property_data_advanced_fields" style="display: none;overflow-y: scroll;">
        <!-- Descricao -->
        <div class="create_form-group">
            <label for="socialdb_property_data_description"><?php _e('Description','tainacan'); ?></label>
            <textarea placeholder="<?php _e('Describe your property','tainacan') ?>" class="form-control" id="socialdb_property_data_description" name="socialdb_property_description"></textarea>
        </div>
        <br>
        <!-- Parent -->
        <div class="create_form-group">
            <label for="socialdb_property_data_description"><?php _e('Property Parent','tainacan'); ?></label><br>
            <input type="checkbox" checked="checked" id="data_no_parent" name="no_parent" value="true">&nbsp;<?php _e('No parent','tainacan'); ?><br>
            <div style="display: none;height: 200px;overflow-y: scroll;" id="dynatree_property_data_parent"></div>
            <input type="hidden" id="data_socialdb_property_parent" name="socialdb_property_parent">
        </div>
        <br>
        <!-- Cardinality -->
        <div class="create_form-group">
            <label for="socialdb_property_data_description"><?php _e('Cardinality','tainacan'); ?></label>
            <div class="form-inline form-group row col-md-12">
                <select style="margin-right: 20px;" name="select_1_cardinality" id="select_1_cardinality" class="form-control col-md-4">
                    <option value="" ><?php _e('Select...','tainacan') ?></option>
                    <option value="socialdb_property_cardinalidality" ><?php _e('Cardinality','tainacan') ?></option>
                    <option value="socialdb_property_mincardinalidality"><?php _e('Minimum Cardinality','tainacan') ?></option>
                    <option value="socialdb_property_maxcardinalidality"><?php _e('Maximum Cardinality','tainacan') ?></option>
                </select>
                <input type="text" class="form-control col-md-4" placeholder="<?php _e('value','tainacan') ?>" id="data_field_1_cardinality" name="field_1_cardinality">
                <div id="button_add_cardinality" style="display: none;" class="col-md-1">
                    <button id="click_add_cardinality" type="button" class=" btn btn-sm btn-primary">
                        <span class="glyphicon glyphicon-plus"></span>
                    </button>
                </div>    
            </div> 
            <div id="option_cardinality_field" class="form-inline form-group row col-md-12" style="display: none;">
                <select id="select_2_cardinality" disabled="disabled" style="margin-right: 20px;" name="select_2_cardinality" class="form-control col-md-4">
                    <option value="socialdb_property_mincardinalidality"><?php _e('Minimum Cardinality','tainacan') ?></option>
                    <option value="socialdb_property_maxcardinalidality"><?php _e('Maximum Cardinality','tainacan') ?></option>
                </select>
                <input type="text" class="form-control col-md-4"  placeholder="<?php _e('value','tainacan') ?>" id="data_field_2_cardinality" name="field_2_cardinality">
            </div>  
        </div>
        <br>
        <!-- Restrictions -->
        <div class="form-inline form-group row col-md-12">
            <label for="property_data_functional"><?php _e('Restrictions','tainacan'); ?></label><br>
            <input id="property_data_functional" type="checkbox" name="property_data_functional" value="true">&nbsp;<?php _e('Functional','tainacan'); ?>
            <br><br>
            <div id="restriction_1" class="form-inline form-group row col-md-12" >
                <select id="select_restriction_1"  style="margin-right: 20px;" name="select_restriction_1" class="form-control col-md-4">
                    <option value="" ><?php _e('Select...','tainacan') ?></option>
                    <option value="equivalentproperty"><?php _e('Equivalent Property','tainacan') ?></option>
                </select>
                <div style="display: none;height: 200px;overflow-y: scroll;" id="dynatree_data_restriction_1"></div>
                <input type="hidden" id="data_equivalentproperty_ids" name="equivalentproperty_ids">
            </div>  
        </div>
        
    </div>    
    <br>  
    
    <script type="text/javascript">
            form_property_data_init('<?php echo ONTOLOGY_CONTROLLERS; ?>');
    </script>
    <?php    
}
/**** Adicionando o pedaco de script para setar os dados na edicao propriedade de dados****/
add_action('javascript_set_new_fields_edit_property_data', 'ontology_javascript_set_new_fields_edit_property_data');
function ontology_javascript_set_new_fields_edit_property_data() {
    echo 'set_fields_edit_property_data(elem)';
}
/**** Limpando o formulario ***********/
add_action('javascript_clear_forms', 'ontology_javascript_clear_forms');
function ontology_javascript_clear_forms() {
    echo ' ontology_clear_forms();';
}
/****************************************/
/**
 * OBJECT
 * formulario de adicao e edicao de propriedade de objeto para ontologia
 * 
 * @uses list.php 
 */
add_action('form_modify_property_object', 'ontology_form_modify_property_object');
function ontology_form_modify_property_object() {
    ?>
    <div  style="padding-bottom: 3px;margin-top: -10px;">
        <a onclick="toggleSlide('ontology_property_object_advanced_fields');" style="cursor: pointer;">
                <span><?php _e('Advanced Options','tainacan')  ?></span> 
                <span class="glyphicon glyphicon-triangle-bottom"></span>
        </a>
    </div>
    <br>
    <div id="ontology_property_object_advanced_fields" style="display: none;overflow-y: scroll;">
        <!-- Descricao -->
        <div class="create_form-group">
            <label for="socialdb_property_object_description"><?php _e('Description','tainacan'); ?></label>
            <textarea placeholder="<?php _e('Describe your property','tainacan') ?>" class="form-control" id="socialdb_property_object_description" name="socialdb_property_description"></textarea>
        </div>
        <br>
        <!-- Parent -->
        <div class="create_form-group">
            <label for="socialdb_property_object_parent"><?php _e('Property Parent','tainacan'); ?></label><br>
            <input type="checkbox" checked="checked" id="object_no_parent" name="no_parent" value="true">&nbsp;<?php _e('No parent','tainacan'); ?><br>
            <div style="display: none;height: 200px;overflow-y: scroll;" id="dynatree_property_object_parent"></div>
            <input type="hidden" id="object_socialdb_property_parent" name="socialdb_property_parent">
        </div>
        <br>
        <!-- Cardinality -->
        <div class="create_form-group">
            <label for="select_1_cardinality_object"><?php _e('Cardinality','tainacan'); ?></label>
            <div class="form-inline form-group row col-md-12">
                <select style="margin-right: 20px;" name="select_1_cardinality" id="select_1_cardinality_object" class="form-control col-md-4">
                    <option value="" ><?php _e('Select...','tainacan') ?></option>
                    <option value="socialdb_property_cardinalidality" ><?php _e('Cardinality','tainacan') ?></option>
                    <option value="socialdb_property_mincardinalidality"><?php _e('Minimum Cardinality','tainacan') ?></option>
                    <option value="socialdb_property_maxcardinalidality"><?php _e('Maximum Cardinality','tainacan') ?></option>
                </select>
                <input type="text" class="form-control col-md-4" placeholder="<?php _e('value','tainacan') ?>" id="object_field_1_cardinality" name="field_1_cardinality">
                <div id="button_add_cardinality_object" style="display: none;" class="col-md-1">
                    <button id="click_add_cardinality_object" type="button" class=" btn btn-sm btn-primary">
                        <span class="glyphicon glyphicon-plus"></span>
                    </button>
                </div>    
            </div> 
            <div id="option_cardinality_field_object" class="form-inline form-group row col-md-12" style="display: none;">
                <select id="select_2_cardinality_object" disabled="disabled" style="margin-right: 20px;" name="select_2_cardinality" class="form-control col-md-4">
                    <option value="socialdb_property_mincardinalidality"><?php _e('Minimum Cardinality','tainacan') ?></option>
                    <option value="socialdb_property_maxcardinalidality"><?php _e('Maximum Cardinality','tainacan') ?></option>
                </select>
                <input type="text" class="form-control col-md-4"  placeholder="<?php _e('value','tainacan') ?>" id="object_field_2_cardinality" name="field_2_cardinality">
            </div>  
        </div>
        <br>
        <div  class="form-group" >
            <label for="property_object_reverse_ontology"><?php _e('Reverse property','tainacan'); ?></label>
            <select class="form-control" id="property_object_reverse_ontology" name="property_object_reverse">
                <option value=""><?php _e('No properties found or no range selected','tainacan') ?></option>
            </select>
        </div>
        <br>
        <!-- Restrictions -->
        <div class="form-inline form-group row col-md-12">
            <label for="property_object_functional"><?php _e('Restrictions','tainacan'); ?></label><br>
            <input id="property_object_functional" type="checkbox" name="property_object_functional" value="true">&nbsp;<?php _e('Functional','tainacan'); ?>
            <input id="property_object_transitive" type="checkbox" name="property_object_transitive" value="true">&nbsp;<?php _e('Transitive','tainacan'); ?>
            <input id="property_object_simetric" type="checkbox" name="property_object_simetric" value="true">&nbsp;<?php _e('Simetric','tainacan'); ?>
            <br><br>
            <div id="restriction_1_object" class="form-inline form-group row col-md-12" >
                <select id="select_restriction_1_object"  style="margin-right: 20px;" name="select_restriction_1" class="form-control col-md-4">
                    <option value="" ><?php _e('Select...','tainacan') ?></option>
                    <option value="equivalentproperty"><?php _e('Equivalent Property','tainacan') ?></option>
                    <option value="allvaluesfrom"><?php _e('All Values From','tainacan') ?></option>
                    <option value="somevaluesfrom"><?php _e('Some Values From','tainacan') ?></option>
                    <option value="hasvalue"><?php _e('Has Value','tainacan') ?></option>
                </select>
                <div style="display: none;height: 200px;overflow-y: scroll;" id="dynatree_object_restriction_1"></div>
                <div style="display: none;height: 200px;overflow-y: scroll;" id="dynatree_object_restriction_2"></div>
                <div style="display: none;height: 200px;overflow-y: scroll;" id="dynatree_object_restriction_3"></div>
                <div style="display: none;height: 200px;overflow-y: scroll;" id="dynatree_object_restriction_4"></div>
                <input type="hidden" id="object_equivalentproperty_ids" name="equivalentproperty_ids">
                <input type="hidden" id="object_allvaluesfrom_ids" name="allvaluesfrom_ids">
                <input type="hidden" id="object_somevaluesfrom_ids" name="somevaluesfrom_ids">
                <input type="hidden" id="object_hasvalue_ids" name="hasvalue_ids">
            </div>  
        </div>
        
    </div>    
    <br>  
    
    <script type="text/javascript">
            form_property_object_init('<?php echo ONTOLOGY_CONTROLLERS; ?>');
    </script>
    <?php    
}
/************** AO realizar um select no dynatree de relacoes****************************************************************/
add_action('javascript_onselect_relationship_dynatree_property_object', 'ontology_javascript_onselect_relationship_dynatree_property_object');
function ontology_javascript_onselect_relationship_dynatree_property_object() {
    echo 'onselect_relationship(node.data.key)';
}
/**** Adicionando o pedaco de script para setar os dados na edicao propriedade de objeto ****/
add_action('javascript_set_new_fields_edit_property_object', 'ontology_javascript_set_new_fields_edit_property_object');
function ontology_javascript_set_new_fields_edit_property_object() {
    echo 'set_fields_edit_property_object(elem)';
}

################################################################################
####### 10# ALTERANDO O Controller e o model de eventos das propriedades  ##########
/**
 * DATA
 * @uses property_controller
 * 
 *  altera o controller para suportar os novos metadados de propriedade de dados
 * 
 */
function ontology_modificate_values_event_property_data_add($data) {
    //description
    if(isset($data['socialdb_property_description'])&&!empty(trim($data['socialdb_property_description']))){
        $data['socialdb_event_property_description'] = $data['socialdb_property_description'];
    }
    //parent
    if((!isset($data['no_parent'])||empty(trim($data['no_parent'])))
            &&isset($data['socialdb_property_parent'])&&!empty(trim($data['socialdb_property_parent']))){
        $data['socialdb_event_property_parent'] = $data['socialdb_property_parent'];
    }
    //cardinalidade
    if(isset($data['select_1_cardinality'])&&!empty(trim($data['select_1_cardinality']))){
        if($data['select_1_cardinality']=='socialdb_property_cardinalidality'){
            $data['socialdb_event_property_cardinalidality'] = $data['field_1_cardinality'];
        }else{
            if($data['select_1_cardinality']=='socialdb_property_mincardinalidality'){
                 $data['socialdb_event_property_mincardinalidality'] = $data['field_1_cardinality'];
                 if(isset($data['field_2_cardinality'])&&!empty(trim($data['field_2_cardinality']))){
                     $data['socialdb_event_property_maxcardinalidality'] = $data['field_2_cardinality'];
                 }
            }elseif($data['select_1_cardinality']=='socialdb_property_maxcardinalidality'){
                $data['socialdb_event_property_maxcardinalidality'] = $data['field_1_cardinality'];
                 if(isset($data['field_2_cardinality'])&&!empty(trim($data['field_2_cardinality']))){
                     $data['socialdb_event_property_mincardinalidality'] = $data['field_2_cardinality'];
                 }
            }
        }
    }
    //functional
    if(isset($data['property_data_functional'])&&!empty(trim($data['property_data_functional']))){
         $data['socialdb_event_property_functional'] = $data['property_data_functional'];
    }
    //restricoes
    if(isset($data['select_restriction_1'])&&!empty(trim($data['select_restriction_1']))){
        if($data['select_restriction_1']=='equivalentproperty'&&!empty(trim($data['equivalentproperty_ids']))){
            $data['socialdb_event_property_equivalent'] = $data['equivalentproperty_ids'];
        }
    }
    return $data;
}
add_filter( 'modificate_values_event_property_data_add', 'ontology_modificate_values_event_property_data_add', 10, 3 );
add_filter( 'modificate_values_event_property_data_update', 'ontology_modificate_values_event_property_data_add', 10, 3 );
/**
 * DATA
 * 
 * @uses event_property_data_create_model
 * 
 * acao disparada apos a insercao de uma propriedade de dados
 * 
 */
add_action( 'after_event_add_property_data', 'ontology_after_event_add_property_data', 10, 2 );
add_action( 'after_event_update_property_data', 'ontology_after_event_add_property_data', 10, 2 );
function ontology_after_event_add_property_data($property_id,$event_id) {
    $description = get_post_meta($event_id, 'socialdb_event_property_description',true) ;
    $parent = get_post_meta($event_id, 'socialdb_event_property_parent',true) ;
    wp_update_term($property_id,'socialdb_property_type', 
            array(
                'description' => ($description&&trim($description)!='')? $description :'',
                'parent'=> ($parent&&trim($parent)!=''&&  is_numeric($parent))? trim($parent) :  get_term_by('slug', 'socialdb_property_data','socialdb_property_type')->term_id,
            ));
    //DOMAINS
    $property_used_by_categories = get_post_meta($event_id, 'socialdb_event_property_used_by_categories',true) ;// os dados vindo do evento
    $categories = get_term_meta($property_id, 'socialdb_property_used_by_categories'); //as categorias atuais vinculadas a essa prorpiedade
    if($categories  &&  is_array($categories)): // se existir atuais ele vai tentar remover apenas as que nao estiverem setadas
        foreach ($categories as $category) :// percorro todas elas
             if($property_used_by_categories&&!empty(trim($property_used_by_categories))):// verifico se esta tentando inserir novos valores
                    $new_categories = explode(',', $property_used_by_categories);
                    if(in_array($category, $new_categories)): // se a categoria antiga estiver nas novas posicoes pula para a proxima execucao
                        continue;
                    endif; 
             endif; 
             // se nao continua o processo 
            $properties = get_term_meta($category, 'socialdb_category_property_id'); // busco os metas da categoria
            if($properties&&( is_array($properties)&&in_array($property_id, $properties))): // verifico se a propriedade ainda esta presente
                delete_term_meta($category, 'socialdb_category_property_id', $property_id); // removo de suas propriedades
            endif;
            delete_term_meta($property_id, 'socialdb_property_used_by_categories',$category); // e entao removo do array de classes que utilizam esta propriedade
         endforeach;
         
    endif; 
    if($property_used_by_categories&&!empty(trim($property_used_by_categories))){ // SE EXISTIR NOVOS VALORES
        $new_categories = explode(',', $property_used_by_categories);// coloco em um array
        foreach ($new_categories as $new_category) {
            if(is_array($categories)&&in_array($new_category, $categories)){
                continue;
            }
            add_term_meta($property_id, 'socialdb_property_used_by_categories', $new_category);
            $properties = get_term_meta($new_category, 'socialdb_category_property_id');
            if(!$properties||( is_array($properties)&&!in_array($property_id, $properties))):
                add_term_meta($new_category, 'socialdb_category_property_id', $property_id);
            endif; 
        }

    }
    //cardinalidality
    $cardinalidality = get_post_meta($event_id, 'socialdb_event_property_cardinalidality',true) ;
    if($cardinalidality&&trim($cardinalidality)!=''){
        update_term_meta($property_id, 'socialdb_property_cardinalidality', trim($cardinalidality));
    }else{
        update_term_meta($property_id, 'socialdb_property_cardinalidality', '');
    }
    //maxcardinalidality
    $cardinalidality = get_post_meta($event_id, 'socialdb_event_property_maxcardinalidality',true) ;
    if($cardinalidality&&trim($cardinalidality)!=''){
        update_term_meta($property_id, 'socialdb_property_maxcardinalidality', trim($cardinalidality));
    }else{
        update_term_meta($property_id, 'socialdb_property_maxcardinalidality', '');
    }
    //mincardinalidality
    $cardinalidality = get_post_meta($event_id, 'socialdb_event_property_mincardinalidality',true) ;
    if($cardinalidality&&trim($cardinalidality)!=''){
        update_term_meta($property_id, 'socialdb_property_mincardinalidality', trim($cardinalidality));
    }else{
        update_term_meta($property_id, 'socialdb_property_mincardinalidality', '');
    }
    //functional
    $functional = get_post_meta($event_id, 'socialdb_event_property_functional',true) ;
    if($functional&&trim($functional)!=''){
        update_term_meta($property_id, 'socialdb_property_functional', trim($functional));
    }else{
        update_term_meta($property_id, 'socialdb_property_functional', '');
    }
    //equivalent
    $equivalent = get_post_meta($event_id, 'socialdb_event_property_equivalent',true) ;
    if($equivalent&&trim($equivalent)!=''){
        delete_term_meta($property_id, 'socialdb_property_equivalent');
        $ids = explode(',', $equivalent);
        foreach ($ids as $id) {
            add_term_meta($property_id, 'socialdb_property_equivalent', $id);
        }
    }else{
        update_term_meta($property_id, 'socialdb_property_equivalent', '');
    }
}

add_action( 'after_event_delete_property_data', 'ontology_after_event_delete_property', 10, 2 );
add_action( 'after_event_delete_property_object', 'ontology_after_event_delete_property', 10, 2 );
add_action( 'after_event_delete_property_term', 'ontology_after_event_delete_property', 10, 2 );
function ontology_after_event_delete_property($property,$event_id) {
    global $wpdb;
    $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
    $wp_terms = $wpdb->prefix . "terms";
    $query = "
                    SELECT * FROM $wp_terms t
                    INNER JOIN $wp_term_taxonomy tt ON t.term_id = tt.term_id
                            WHERE tt.parent = {$property->term_id} 
            ";
    $children = $wpdb->get_results($query);
    if($children&&is_array($children)){
        foreach ($children as $child) {
            $query = "
                    UPDATE $wp_term_taxonomy tt SET tt.parent = {$property->parent} 
                            WHERE tt.term_id = {$child->term_id} 
            ";
            $wpdb->get_results($query);                
        }
    }
}
/**
 * OBJECT
 * @uses property_controller
 * 
 *  altera o controller para suportar os novos metadados de propriedade de dados
 * 
 */
function ontology_modificate_values_event_property_object_add($data) {
    //description
    if(isset($data['socialdb_property_description'])&&!empty(trim($data['socialdb_property_description']))){
        $data['socialdb_event_property_description'] = $data['socialdb_property_description'];
    }
    //parent
    if((!isset($data['no_parent'])||empty(trim($data['no_parent'])))
            &&isset($data['socialdb_property_parent'])&&!empty(trim($data['socialdb_property_parent']))){
        $data['socialdb_event_property_parent'] = $data['socialdb_property_parent'];
    }
    //cardinalidade
    if(isset($data['select_1_cardinality'])&&!empty(trim($data['select_1_cardinality']))){
        if($data['select_1_cardinality']=='socialdb_property_cardinalidality'){
            $data['socialdb_event_property_cardinalidality'] = $data['field_1_cardinality'];
        }else{
            if($data['select_1_cardinality']=='socialdb_property_mincardinalidality'){
                 $data['socialdb_event_property_mincardinalidality'] = $data['field_1_cardinality'];
                 if(isset($data['field_2_cardinality'])&&!empty(trim($data['field_2_cardinality']))){
                     $data['socialdb_event_property_maxcardinalidality'] = $data['field_2_cardinality'];
                 }
            }elseif($data['select_1_cardinality']=='socialdb_property_maxcardinalidality'){
                $data['socialdb_event_property_maxcardinalidality'] = $data['field_1_cardinality'];
                 if(isset($data['field_2_cardinality'])&&!empty(trim($data['field_2_cardinality']))){
                     $data['socialdb_event_property_mincardinalidality'] = $data['field_2_cardinality'];
                 }
            }
        }
    }
    //reverse
    if(isset($data['property_object_reverse'])&&!empty(trim($data['property_object_reverse']))&&$data['property_object_reverse']!='false'){
        $data['socialdb_event_property_object_create_is_reverse'] = 'true';
        $data['socialdb_event_property_object_edit_is_reverse'] = 'true';
        $data['socialdb_event_property_object_create_reverse'] = $data['property_object_reverse'];
        $data['socialdb_event_property_object_edit_reverse'] = $data['property_object_reverse'];
    }
    //functional
    if(isset($data['property_object_functional'])&&!empty(trim($data['property_object_functional']))){
         $data['socialdb_event_property_functional'] = $data['property_object_functional'];
    }
    //transitive
    if(isset($data['property_object_transitive'])&&!empty(trim($data['property_object_transitive']))){
         $data['socialdb_event_property_transitive'] = $data['property_object_transitive'];
    }
    //simetrico
    if(isset($data['property_object_simetric'])&&!empty(trim($data['property_object_simetric']))){
         $data['socialdb_event_property_simetric'] = $data['property_object_simetric'];
    }
    //restricoes
    if(!empty(trim($data['equivalentproperty_ids']))){
        $data['socialdb_event_property_equivalent'] = $data['equivalentproperty_ids'];
    }
    if(!empty(trim($data['allvaluesfrom_ids']))){
        $data['socialdb_event_property_allvaluesfrom'] = $data['allvaluesfrom_ids'];
    }
    if(!empty(trim($data['somevaluesfrom_ids']))){
        $data['socialdb_event_property_somevaluesfrom'] = $data['somevaluesfrom_ids'];
    }
    if(!empty(trim($data['hasvalue_ids']))){
        $data['socialdb_event_property_hasvalue'] = $data['hasvalue_ids'];
    }
    return $data;
}
add_filter( 'modificate_values_event_property_object_add', 'ontology_modificate_values_event_property_object_add', 10, 3 );
add_filter( 'modificate_values_event_property_object_update', 'ontology_modificate_values_event_property_object_add', 10, 3 );
/**
 * OBJECT
 * 
 * @uses event_property_data_create_model
 * 
 * acao disparada apos a insercao de uma propriedade de dados
 * 
 */
add_action( 'after_event_add_property_object', 'ontology_after_event_add_property_object', 10, 2 );
add_action( 'after_event_update_property_object', 'ontology_after_event_add_property_object', 10, 2 );
function ontology_after_event_add_property_object($property_id,$event_id) {
    $description = get_post_meta($event_id, 'socialdb_event_property_description',true) ;
    $parent = get_post_meta($event_id, 'socialdb_event_property_parent',true) ;
    wp_update_term($property_id,'socialdb_property_type', 
            array(
                'description' => ($description&&trim($description)!='')? $description :'',
                'parent'=> ($parent&&trim($parent)!=''&&  is_numeric($parent))? trim($parent) :  get_term_by('slug', 'socialdb_property_object','socialdb_property_type')->term_id,
            ));
    //DOMAINS
    $property_used_by_categories = get_post_meta($event_id, 'socialdb_event_property_used_by_categories',true) ;// os dados vindo do evento
    $categories = get_term_meta($property_id, 'socialdb_property_used_by_categories'); //as categorias atuais vinculadas a essa prorpiedade
    if($categories  &&  is_array($categories)): // se existir atuais ele vai tentar remover apenas as que nao estiverem setadas
        foreach ($categories as $category) :// percorro todas elas
             if($property_used_by_categories&&!empty(trim($property_used_by_categories))):// verifico se esta tentando inserir novos valores
                    $new_categories = explode(',', $property_used_by_categories);
                    if(in_array($category, $new_categories)): // se a categoria antiga estiver nas novas posicoes pula para a proxima execucao
                        continue;
                    endif; 
             endif; 
             // se nao continua o processo 
            $properties = get_term_meta($category, 'socialdb_category_property_id'); // busco os metas da categoria
            if($properties&&( is_array($properties)&&in_array($property_id, $properties))): // verifico se a propriedade ainda esta presente
                delete_term_meta($category, 'socialdb_category_property_id', $property_id); // removo de suas propriedades
            endif;
            delete_term_meta($property_id, 'socialdb_property_used_by_categories',$category); // e entao removo do array de classes que utilizam esta propriedade
         endforeach;
         
    endif; 
    if($property_used_by_categories&&!empty(trim($property_used_by_categories))){ // SE EXISTIR NOVOS VALORES
        $new_categories = explode(',', $property_used_by_categories);// coloco em um array
        foreach ($new_categories as $new_category) {
            if(is_array($categories)&&in_array($new_category, $categories)){
                continue;
            }
            add_term_meta($property_id, 'socialdb_property_used_by_categories', $new_category);
            $properties = get_term_meta($new_category, 'socialdb_category_property_id');
            if(!$properties||( is_array($properties)&&!in_array($property_id, $properties))):
                add_term_meta($new_category, 'socialdb_category_property_id', $property_id);
            endif; 
        }

    }
    //cardinalidality
    $cardinalidality = get_post_meta($event_id, 'socialdb_event_property_cardinalidality',true) ;
    if($cardinalidality&&trim($cardinalidality)!=''){
        update_term_meta($property_id, 'socialdb_property_cardinalidality', trim($cardinalidality));
    }else{
        update_term_meta($property_id, 'socialdb_property_cardinalidality', '');
    }
    //maxcardinalidality
    $cardinalidality = get_post_meta($event_id, 'socialdb_event_property_maxcardinalidality',true) ;
    if($cardinalidality&&trim($cardinalidality)!=''){
        update_term_meta($property_id, 'socialdb_property_maxcardinalidality', trim($cardinalidality));
    }else{
        update_term_meta($property_id, 'socialdb_property_maxcardinalidality', '');
    }
    //mincardinalidality
    $cardinalidality = get_post_meta($event_id, 'socialdb_event_property_mincardinalidality',true) ;
    if($cardinalidality&&trim($cardinalidality)!=''){
        update_term_meta($property_id, 'socialdb_property_mincardinalidality', trim($cardinalidality));
    }else{
        update_term_meta($property_id, 'socialdb_property_mincardinalidality', '');
    }
    //functional
    $functional = get_post_meta($event_id, 'socialdb_event_property_functional',true) ;
    if($functional&&trim($functional)!=''){
        update_term_meta($property_id, 'socialdb_property_functional', trim($functional));
    }else{
        update_term_meta($property_id, 'socialdb_property_functional', '');
    }
    //equivalent
    $equivalent = get_post_meta($event_id, 'socialdb_event_property_equivalent',true) ;
    if($equivalent&&trim($equivalent)!=''){
        delete_term_meta($property_id, 'socialdb_property_equivalent');
        $ids = explode(',', $equivalent);
        foreach ($ids as $id) {
            add_term_meta($property_id, 'socialdb_property_equivalent', $id);
        }
    }else{
        update_term_meta($property_id, 'socialdb_property_equivalent', '');
    }
    //transitive
    $transitive = get_post_meta($event_id, 'socialdb_event_property_transitive',true) ;
    if($transitive&&trim($transitive)!=''){
//        delete_term_meta($property_id, 'socialdb_property_transitive');
//        $ids = explode(',', $transitive);
//        foreach ($ids as $id) {
//            add_term_meta($property_id, 'socialdb_property_transitive', $id);
//        }
        update_term_meta($property_id, 'socialdb_property_transitive', trim($transitive));
    }else{
        update_term_meta($property_id, 'socialdb_property_transitive', '');
    }
    //simetric
    $simetric = get_post_meta($event_id, 'socialdb_event_property_simetric',true) ;
    if($simetric&&trim($simetric)!=''){
//        delete_term_meta($property_id, 'socialdb_property_simetric');
//        $ids = explode(',', $simetric);
//        foreach ($ids as $id) {
//            add_term_meta($property_id, 'socialdb_property_simetric', $id);
//        }
        update_term_meta($property_id, 'socialdb_property_simetric', trim($simetric)); 
    }else{
        update_term_meta($property_id, 'socialdb_property_simetric', '');
    }
    //allvaluesfrom
    $allvaluesfrom = get_post_meta($event_id, 'socialdb_event_property_allvaluesfrom',true) ;
    if($allvaluesfrom&&trim($allvaluesfrom)!=''){
        delete_term_meta($property_id, 'socialdb_property_allvaluesfrom');
        $ids = explode(',', $allvaluesfrom);
        foreach ($ids as $id) {
            add_term_meta($property_id, 'socialdb_property_allvaluesfrom', $id);
        }
    }else{
        update_term_meta($property_id, 'socialdb_property_allvaluesfrom', '');
    }
    //somevaluesfrom
    $somevaluesfrom = get_post_meta($event_id, 'socialdb_event_property_somevaluesfrom',true) ;
    if($somevaluesfrom&&trim($somevaluesfrom)!=''){
        delete_term_meta($property_id, 'socialdb_property_somevaluesfrom');
        $ids = explode(',', $somevaluesfrom);
        foreach ($ids as $id) {
            add_term_meta($property_id, 'socialdb_property_somevaluesfrom', $id);
        }
    }else{
        update_term_meta($property_id, 'socialdb_property_somevaluesfrom', '');
    }
    //hasvalue
    $hasvalue = get_post_meta($event_id, 'socialdb_event_property_hasvalue',true) ;
    if($hasvalue&&trim($hasvalue)!=''){
        delete_term_meta($property_id, 'socialdb_property_hasvalue');
        $ids = explode(',', $hasvalue);
        foreach ($ids as $id) {
            add_term_meta($property_id, 'socialdb_property_hasvalue', $id);
        }
    }else{
        update_term_meta($property_id, 'socialdb_property_hasvalue', '');
    }
}
/**
 * TERM
 * 
 * @uses event_property_term_create_model
 * 
 * acao disparada apos a insercao de uma propriedade de termo
 * 
 */
add_action( 'after_event_add_property_term', 'ontology_after_event_add_property_term', 10, 2 );
add_action( 'after_event_update_property_term', 'ontology_after_event_add_property_term', 10, 2 );
function ontology_after_event_add_property_term($property_id,$event_id) {
    //DOMAINS
    $property_used_by_categories = get_post_meta($event_id, 'socialdb_event_property_used_by_categories',true) ;// os dados vindo do evento
    $categories = get_term_meta($property_id, 'socialdb_property_used_by_categories'); //as categorias atuais vinculadas a essa prorpiedade
    if($categories  &&  is_array($categories)): // se existir atuais ele vai tentar remover apenas as que nao estiverem setadas
        foreach ($categories as $category) :// percorro todas elas
             if($property_used_by_categories&&!empty(trim($property_used_by_categories))):// verifico se esta tentando inserir novos valores
                    $new_categories = explode(',', $property_used_by_categories);
                    if(in_array($category, $new_categories)): // se a categoria antiga estiver nas novas posicoes pula para a proxima execucao
                        continue;
                    endif; 
             endif; 
             // se nao continua o processo 
            $properties = get_term_meta($category, 'socialdb_category_property_id'); // busco os metas da categoria
            if($properties&&( is_array($properties)&&in_array($property_id, $properties))): // verifico se a propriedade ainda esta presente
                delete_term_meta($category, 'socialdb_category_property_id', $property_id); // removo de suas propriedades
            endif;
            delete_term_meta($property_id, 'socialdb_property_used_by_categories',$category); // e entao removo do array de classes que utilizam esta propriedade
         endforeach;
         
    endif; 
    if($property_used_by_categories&&!empty(trim($property_used_by_categories))){ // SE EXISTIR NOVOS VALORES
        $new_categories = explode(',', $property_used_by_categories);// coloco em um array
        foreach ($new_categories as $new_category) {
            if(is_array($categories)&&in_array($new_category, $categories)){
                continue;
            }
            add_term_meta($property_id, 'socialdb_property_used_by_categories', $new_category);
            $properties = get_term_meta($new_category, 'socialdb_category_property_id');
            if(!$properties||( is_array($properties)&&!in_array($property_id, $properties))):
                add_term_meta($new_category, 'socialdb_category_property_id', $property_id);
            endif; 
        }

    }
}
/** ALL TYPES
 * @uses property_model
 * 
 * altera o model de propriedades, este filtro eh utilizado para trabalhar os dados na chamada
 * do metodo get_all_property desta forma pode se alterar o array com os metadados
 * 
 */
function ontology_modificate_values_get_all_property($data) {
    global $wpdb;
    $config = [];
    $wp_taxonomymeta = $wpdb->prefix . "termmeta";
    if(!$data['id']){
        return $data;
    }
    //dados principais da propriedade
    $roots_parents = [
        get_term_by('name','socialdb_property_data','socialdb_property_type')->term_id,
        get_term_by('name','socialdb_property_object','socialdb_property_type')->term_id,
        get_term_by('name','socialdb_property_term','socialdb_property_type')->term_id ];
    $property = get_term_by('id', $data['id'], 'socialdb_property_type');
    $data['description'] = $property->description;
    $data['parent'] = ((in_array($property->parent, $roots_parents)||$property->parent==0)? 0 :$property->parent);
    // zero as restricoes
    $data['metas']['socialdb_property_equivalent'] = [];
    $data['metas']['socialdb_property_allvaluesfrom'] = [];
    $data['metas']['socialdb_property_hasvalue'] = [];
    $data['metas']['socialdb_property_somevaluesfrom'] = [];
    //faco a query pelos metas
    $query = "
                    SELECT * FROM $wp_taxonomymeta t
                            WHERE t.term_id = {$data['id']}
            ";
    $property_datas = $wpdb->get_results($query);
    if ($property_datas && is_array($property_datas)) {
        foreach ($property_datas as $property_data) {
            if ($property_data->meta_key == 'socialdb_property_equivalent') {
                $data['metas'][$property_data->meta_key][] = $property_data->meta_value;
            }else if ($property_data->meta_key == 'socialdb_property_allvaluesfrom') {
                $data['metas'][$property_data->meta_key][] = $property_data->meta_value;
            }else if ($property_data->meta_key == 'socialdb_property_hasvalue') {
                $data['metas'][$property_data->meta_key][] = $property_data->meta_value;
            }else if ($property_data->meta_key == 'socialdb_property_somevaluesfrom') {
                $data['metas'][$property_data->meta_key][] = $property_data->meta_value;
            } 
        }
    }
    return $data;
}
add_filter( 'modificate_values_get_all_property', 'ontology_modificate_values_get_all_property', 10, 3 );
################################################################################
##################### 11# REMOCAO DE CAMPOS DESNECESSÁRIOS #########################

/**************** ESCONDER CAMPOS DO FORMULARIO DE ADICAO E EXCLUSAO***********/
add_action('item_type_attributes', 'hide_field');
add_action('item_from_attributes', 'hide_field');
add_action('item_content_attributes', 'hide_field');
add_action('item_tags_attributes', 'hide_field');
//add_action('item_property_term_attributes', 'hide_field');
add_action('item_source_attributes', 'hide_field');
add_action('item_attachments_attributes', 'hide_field');
/******************************************************************************/
/**************** ESCONDER CAMPOS DO MENU DA COLECAO ***********/
//add_action('menu_collection_search_configuration', 'hide_field');
add_action('menu_collection_property_and_filters_configuration', 'hide_field');
add_action('menu_collection_property_configuration', 'hide_field');
add_action('menu_collection_social_configuration', 'hide_field');
add_action('menu_collection_license', 'hide_field');
//add_action('menu_collection_import', 'hide_field');
//add_action('menu_collection_export', 'hide_field');
/******************************************************************************/
/**************** ESCONDER CAMPOS DO MENU DO REPOSITORIO ***********/
add_action('menu_repository_social_api', 'hide_field');
add_action('menu_repository_license', 'hide_field');
/******************************************************************************/
/** ESCONDER CAMPOS DO FORMULARIO DE ADICAO E EDICAO DE PROPRIEDADE DE DADOS***/
add_action('form_default_value_property_data', 'hide_field');
add_action('form_help_property_data', 'hide_field');
add_action('form_required_property_data', 'hide_field');
add_action('form_help_property_data_type_text', 'hide_field');
add_action('form_help_property_data_type_textarea', 'hide_field');
add_action('form_help_property_data_type_date', 'hide_field');
add_action('form_help_property_data_type_numeric', 'hide_field');
add_action('form_help_property_data_type_autoincrement', 'hide_field');
/******************************************************************************/
/** ESCONDER CAMPOS DO FORMULARIO DE ADICAO E EDICAO DE PROPRIEDADE DE OBJETOS*/
add_action('form_is_reverse_property_object', 'hide_field');
add_action('form_required_property_object', 'hide_field');
/******************************************************************************/
/** ESCONDER CAMPOS DO FORMULARIO DE ADICAO E EDICAO DE PROPRIEDADE DE OBJETOS*/
add_action('collection_create_name_object', 'hide_field');
/******************************************************************************/
/** ESCONDER NA HOME DO ITEM */
add_action('home_item_add_property', 'hide_field');
add_action('home_item_delete_property', 'hide_field');
add_action('home_item_source_div', 'hide_field');
add_action('home_item_type_div', 'hide_field');
add_action('home_item_license_div', 'hide_field');
add_action('home_item_tag_div', 'hide_field');
add_action('home_item_attachments_div', 'hide_field');
add_action('home_item_content_div', 'hide_field');
/******************************************************************************/
/****************************** Category edit *********************************/
add_action('synonyms_category_view', 'hide_field');
/******************************************************************************/
function hide_field() {
    echo 'style="display:none;"';                          
}

/******************************************************************************/

################ FIM: REMOCAO DE CAMPOS DESNECESSÁRIOS #########################

##################### #12 FILTROS PARA A EXPORTACAO DE ONTOLOGIAS ##################
//exportacao simples do repositorio
function ontology_modificate_export_simple_repository_rdf($slug) {
    require_once(dirname(__FILE__) . '/models/export/rdf_repository_model.php');
    $object = new OntologyRDFRepositoryModel();
    return trim($object->export_simple_repository());
}
add_filter( 'modificate_export_simple_repository_rdf', 'ontology_modificate_export_simple_repository_rdf', 10, 3 );
//exportacao completa do repositorio
function ontology_modificate_export_simple_complete_rdf($slug) {
    require_once(dirname(__FILE__) . '/models/export/rdf_collection_model.php');
    $object = new OntologyRDFCollectionModel();
    return trim($object->export_all_collection());
}
add_filter( 'modificate_export_simple_complete_rdf', 'ontology_modificate_export_simple_complete_rdf', 10, 3 );
//exportacao simples de uma colecao
function ontology_modificate_export_collection_rdf($slug) {
    require_once(dirname(__FILE__) . '/models/export/rdf_collection_model.php');
    $object = new OntologyRDFCollectionModel();
    return trim($object->export_simple_collection());
}
add_filter( 'modificate_export_collection_rdf', 'ontology_modificate_export_collection_rdf', 10, 3 );
//exportacao completa de uma colecao
function ontology_modificate_export_collection_complete_rdf($slug) {
    require_once(dirname(__FILE__) . '/models/export/rdf_collection_model.php');
    $object = new OntologyRDFCollectionModel();
    return trim($object->export_all_collection());
}
add_filter( 'modificate_export_complete_collection_rdf', 'ontology_modificate_export_collection_complete_rdf', 10, 3 );
//exportacao de um item
function ontology_modificate_export_item_rdf($slug) {
    require_once(dirname(__FILE__) . '/models/export/rdf_model.php');
    $object = new OntologyRDFModel();
    return trim($object->export_simple_item());
}
add_filter( 'modificate_export_item_rdf', 'ontology_modificate_export_item_rdf', 10, 3 );
//exportacao de uma classe
function ontology_personal_export_category_rdf($slug) {
    require_once(dirname(__FILE__) . '/models/export/rdf_category_model.php');
    $object = new RDFOntologyCategoryModel();
    return trim($object->export_category($slug));
}
add_filter( 'personal_export_category_rdf', 'ontology_personal_export_category_rdf', 10, 3 );
//exportacao de uma propriedade
function ontology_personal_export_property_rdf($slug) {
    require_once(dirname(__FILE__) . '/models/export/rdf_property_model.php');
    $object = new OntologyRDFPropertyModel();
    return trim($object->export_property($slug));
}
add_filter( 'personal_export_property_rdf', 'ontology_personal_export_property_rdf', 10, 3 );

################ FIM: FILTROS PARA A EXPORTACAO DE ONTOLOGIAS ##################

#### #13 -  ALTERACAO DO FORMULARIO DE ADICAO/EDICAO DE PROPRIEDADE DE DADOS ###
/**
 * 
 * @param type $property
 * @return type
 * 
 */
function get_property_cardinality($property){
    $ontology_max_cardinality_fields = 40;
    $ontology_min_cardinality_fields = 0;
    if($property['metas']['socialdb_property_cardinalidality']&&!empty($property['metas']['socialdb_property_cardinalidality'])){
        $fixed = true;
        $ontology_min_cardinality_fields = $property['metas']['socialdb_property_cardinalidality'];
        $ontology_max_cardinality_fields = $property['metas']['socialdb_property_cardinalidality'];
    }else{
        if($property['metas']['socialdb_property_maxcardinalidality']&&!empty($property['metas']['socialdb_property_maxcardinalidality'])){
            $fixed = false;
            $ontology_max_cardinality_fields = $property['metas']['socialdb_property_maxcardinalidality'];
            if($property['metas']['socialdb_property_mincardinalidality']&&!empty($property['metas']['socialdb_property_mincardinalidality'])){
                $ontology_min_cardinality_fields = $property['metas']['socialdb_property_mincardinalidality'];
            }else{
                $ontology_min_cardinality_fields = 0;
            }
        }
        if($property['metas']['socialdb_property_mincardinalidality']&&!empty($property['metas']['socialdb_property_mincardinalidality'])){
             $fixed = false;
            $ontology_min_cardinality_fields = $property['metas']['socialdb_property_mincardinalidality'];
            if($property['metas']['socialdb_property_maxcardinalidality']&&!empty($property['metas']['socialdb_property_maxcardinalidality'])){
                $ontology_max_cardinality_fields = $property['metas']['socialdb_property_maxcardinalidality'];
            }
        }
    }
    return ['max'=>$ontology_max_cardinality_fields,'min'=>$ontology_min_cardinality_fields,'fixed'=>$fixed];
}

//altera o label para todas as propriedades 
add_action( 'modificate_label_insert_item_properties', 'ontology_modificate_label_insert_item_properties', 10, 1 );
add_action( 'modificate_label_edit_item_properties', 'ontology_modificate_label_insert_item_properties', 10, 1 );
function ontology_modificate_label_insert_item_properties($property) {
    $array_cardinality = get_property_cardinality($property);
    $ontology_max_cardinality_fields = $array_cardinality['max'];
    $ontology_min_cardinality_fields = $array_cardinality['min'];
    $fixed = $array_cardinality['fixed'];
    //html
    $path_image = get_template_directory_uri() . '/libraries/images/danger-sing.png';
    ?>
    <script type="text/javascript">
    $(document).ready(function(){
        $("#cardinality-obrigation-<?php echo $property['id'] ?>").tooltip({
            title: '<?php echo sprintf( __('Minimum of items neccessary: %s Maximun of items neccessary: %s','tainacan'), $ontology_min_cardinality_fields,($ontology_max_cardinality_fields==40)?'*':$ontology_max_cardinality_fields) ?>',
            delay: 100
        });
        $("#correct-<?php echo $property['id'] ?>").tooltip({
            title: '<?php echo __('Field filled correctly!','tainacan') ?>',
            delay: 100
        });
        $("#error-<?php echo $property['id'] ?>").tooltip({
            title: '<?php echo __('This field does not match its cardinality!','tainacan') ?>',
            delay: 100
        });
    });
    </script>
    <?php if($ontology_min_cardinality_fields>0): ?> 
         <a id="cardinality-obrigation-<?php echo $property['id'] ?>" data-toggle="tooltip" data-placement="right" ><img src="<?php echo $path_image ?>"></a>
    <?php else: ?> 
          <a style="display:none" id="cardinality-obrigation-<?php echo $property['id'] ?>" data-toggle="tooltip" data-placement="right" ><img src="<?php echo $path_image ?>"></a>
    <?php endif; ?> 
    <a style="display:none" id="correct-<?php echo $property['id'] ?>" data-toggle="tooltip" data-placement="right" >
        <span class="glyphicon glyphicon-ok-sign text-success"></span>
    </a> 
    <a style="display:none" id="error-<?php echo $property['id'] ?>" data-toggle="tooltip" data-placement="right" >
        <span class="glyphicon glyphicon-remove-sign text-danger"></span>
    </a>     
    <?php if($property['metas']['socialdb_property_help']&&!empty(trim($property['metas']['socialdb_property_help']))): ?> 
          <script type="text/javascript">
                $(document).ready(function(){
                    $("#help-<?php echo $property['id'] ?>").tooltip({
                        title: '<?php echo $property['metas']['socialdb_property_help'] ?>',
                        delay: 100
                    });
                });
         </script>
         <a id="help-<?php echo $property['id'] ?>" data-toggle="tooltip" data-placement="right" >
             <span class="glyphicon glyphicon-question-sign"></span>
         </a>
    <?php endif; ?>      
    <?php
}



add_action( 'modificate_insert_item_properties_data', 'ontology_modificate_insert_item_properties_data', 10, 1 );
add_action( 'modificate_edit_item_properties_data', 'ontology_modificate_insert_item_properties_data', 10, 1 );
function ontology_modificate_insert_item_properties_data($property) {
    //cardinalidade
    $cont = 0;
    $array_cardinality = get_property_cardinality($property);
    $ontology_max_cardinality_fields = $array_cardinality['max'];
    $ontology_min_cardinality_fields = $array_cardinality['min'];
    $fixed = $array_cardinality['fixed'];
    //html
    ?>  
           <input type="hidden" id="property_<?php echo $property['id']; ?>_max" name="socialdb_property_<?php echo $property['id']; ?>_total_fields" value="<?php echo $ontology_max_cardinality_fields ?>" >
           <input type="hidden" id="property_<?php echo $property['id']; ?>_min" value="<?php echo $ontology_min_cardinality_fields ?>" >
           <input type="hidden" id="property_validation_<?php echo $property['id']; ?>" class="validation_properties_cardinality" value="" >
           <input type="hidden" class="obrigation-message-<?php echo $property['id']; ?>" value="<?php echo __('This field is required!','tainacan') ?>" >
           <input type="hidden" class="optional-message-<?php echo $property['id']; ?>" value="<?php echo sprintf(__('At least, fill %s field in this property!','tainacan'),$ontology_min_cardinality_fields); ?>" >
           <?php for($i=0;$i<$ontology_max_cardinality_fields;$i++):  ?>
            <div id="field_property_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                 class="row" 
                <?php echo ($i>=1&&!$fixed&&$i>=$ontology_min_cardinality_fields
                        &&(!isset($property['metas']['value'])||!is_array($property['metas']['value'])||count($property['metas']['value'])-1<$i))
                            ? 'style="display:none;"':'style="margin-bottom:15px;"' ?> >
                <div id="field_container_<?php echo $property['id']; ?>_<?php echo $i; ?>" class="col-md-11">
                    <div id="form_group_<?php echo $property['id']; ?>_<?php echo $i; ?>"  
                         class="form-group has-feedback"> 
                        <?php get_html_property_data_types($property,$i,$fixed); ?>
                        <span id="icon_<?php echo $property['id']; ?>_<?php echo $i; ?>" class="glyphicon" aria-hidden="true"></span>
                        <span id="status_field_<?php echo $property['id']; ?>_<?php echo $i; ?>" class="sr-only"></span>
                        <input type="hidden" value="false" id="is_valid_<?php echo $property['id']; ?>_<?php echo $i; ?>" class="is_valid_<?php echo $property['id']; ?>">
                    </div>  
                </div>    
                <div class="col-md-1">    
                    <?php 
                    //se nao for a cardinalidade fixa, se o maximo de campos for maior que um , se nao for o ultimo campo e se ja e maior que o minimo de campos
                    if(!$fixed&&$ontology_max_cardinality_fields>1&&$ontology_max_cardinality_fields-1!=$i&&$i>=$ontology_min_cardinality_fields-1
                            ):  $cont++; ?>
                    <button type="button" 
                            <?php echo (($cont==1&&!$property['metas']['value'])||count($property['metas']['value'])-1==$i)? '':'style="display:none"' ?> 
                            id="button_property_<?php echo $property['id']; ?>_<?php echo $i; ?>"
                            onclick="show_field_properties(<?php echo $property['id']; ?>,<?php echo $i+1; ?>)"
                            class="btn btn-primary">
                                <span class="glyphicon glyphicon-plus"></span>
                        </button>
                    <?php endif; ?>
                </div>    
            </div>  
            <?php endfor; 
       
}

//funcao que gera o input dependendo do tipo da propriedade
function get_html_property_data_types($property,$i,$fixed){
     if ($property['type'] == 'string') {  
       $seletor = 'form_autocomplete_value_'.$property['id'].'_'.$i;
         ?> 
                        <input     type="text" 
                                   aria-describedby="inputStatus"
                                   id="form_autocomplete_value_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                                   class="form-control form_autocomplete_value_<?php echo $property['id']; ?>"
                                   value="<?php echo (isset($property['metas']['value'][$i]))? $property['metas']['value'][$i]:'' ?>"
                                   <?php echo ($fixed)? 'required="required"':'' ?>
                                   name="socialdb_property_<?php echo $property['id']; ?>[]" 
                                   autocomplete="off"
                                   onkeyup="validation_cardinality_property_data('<?php echo $seletor ?>','<?php echo $property['id'] ?>','<?php echo $i ?>');"
                                   > 
                 <?php
    }else if ($property['type'] == 'boolean') { 
        $seletor = 'form_autocomplete_value_'.$property['id'].'_'.$i;
        ?>     
                    <select id="form_autocomplete_value_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                            class="form-control form_autocomplete_value_<?php echo $property['id']; ?>"
                            onchange="validation_cardinality_property_data('<?php echo $seletor ?>','<?php echo $property['id'] ?>','<?php echo $i ?>');"
                             <?php echo ($fixed)? 'required="required"':'' ?>
                            name="socialdb_property_<?php echo $property['id']; ?>[]">
                        <option value=""><?php _e('Select','tainacan') ?></option>
                        <option <?php echo (isset($property['metas']['value'][$i])&&$property['metas']['value'][$i]=='true')? 'selected="selected"':'' ?> value="true"><?php _e('True','tainacan') ?></option>
                        <option <?php echo (isset($property['metas']['value'][$i])&&$property['metas']['value'][$i]=='false')? 'selected="selected"':'' ?> value="false"><?php _e('False','tainacan') ?></option>
                    </select>  
        <?php
    }else if ($property['type'] == 'float') {
        
        $seletor = 'form_autocomplete_value_'.$property['id'].'_'.$i;
        ?>    
            <input type="text" 
                    id="form_autocomplete_value_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                   class="form-control form_autocomplete_value_<?php echo $property['id']; ?>"
                   value="<?php echo (isset($property['metas']['value'][$i]))? $property['metas']['value'][$i]:'' ?>"
                   onkeypress='return  isFloat(event)'
                   autocomplete="off"
                   onkeyup="validation_cardinality_property_data('<?php echo $seletor ?>','<?php echo $property['id'] ?>','<?php echo $i ?>');"
                    <?php echo ($fixed)? 'required="required"':'' ?>
                   name="socialdb_property_<?php echo $property['id']; ?>[]"  >
        <?php
    }else if ($property['type'] == 'int') { 
        $seletor = 'form_autocomplete_value_'.$property['id'].'_'.$i;
        ?>     
                <input type="text" 
                    id="form_autocomplete_value_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                    class="form-control form_autocomplete_value_<?php echo $property['id']; ?>"
                   value="<?php echo (isset($property['metas']['value'][$i]))? $property['metas']['value'][$i]:'' ?>"
                   onkeypress='return onlyNumbers(event)'
                   autocomplete="off"
                   onkeyup="validation_cardinality_property_data('<?php echo $seletor ?>','<?php echo $property['id'] ?>','<?php echo $i ?>');"
                    <?php echo ($fixed)? 'required="required"':'' ?>
                   name="socialdb_property_<?php echo $property['id']; ?>[]"  >
        <?php
    }else if ($property['type'] == 'date') { 
        $seletor = 'socialdb_property_'.$property['id'].'_field_'.$i;
        ?>     
                 <script>
                    $(function() {
                        $('#field_container_<?php echo $property['id']; ?>_<?php echo $i; ?>').attr('class','col-md-4');
                        $( "#socialdb_property_<?php echo $property['id']; ?>_field_<?php echo $i; ?>" ).datepicker({
                            dateFormat: 'dd/mm/yy',
                            dayNames: ['<?php _e('Sunday','tainacan') ?>','<?php _e('Monday','tainacan') ?>','<?php _e('Tuesday','tainacan') ?>','<?php _e('Wednesday','tainacan') ?>','<?php _e('Thursday','tainacan') ?>','<?php _e('Friday','tainacan') ?>','<?php _e('Saturday','tainacan') ?>'],
                            dayNamesMin: ['<?php _e('S','tainacan') ?>','<?php _e('M','tainacan') ?>','<?php _e('T','tainacan') ?>','<?php _e('W','tainacan') ?>','<?php _e('T','tainacan') ?>','<?php _e('F','tainacan') ?>','<?php _e('S','tainacan') ?>','<?php _e('S','tainacan') ?>'],
                            dayNamesShort: ['<?php _e('Sun','tainacan') ?>','<?php _e('Monday','tainacan') ?>','<?php _e('Tue','tainacan') ?>','<?php _e('Wed','tainacan') ?>','<?php _e('Thu','tainacan') ?>','<?php _e('Fri','tainacan') ?>','<?php _e('Sat','tainacan') ?>'],
                            monthNames: ['<?php _e('January','tainacan') ?>','<?php _e('February','tainacan') ?>','<?php _e('March','tainacan') ?>','<?php _e('April','tainacan') ?>','<?php _e('May','tainacan') ?>','<?php _e('June','tainacan') ?>','<?php _e('July','tainacan') ?>','<?php _e('August','tainacan') ?>','<?php _e('September','tainacan') ?>','<?php _e('October','tainacan') ?>','<?php _e('November','tainacan') ?>','<?php _e('December','tainacan') ?>'],
                            monthNamesShort: ['<?php _e('Jan','tainacan') ?>','<?php _e('Feb','tainacan') ?>','<?php _e('Mar','tainacan') ?>','<?php _e('Apr','tainacan') ?>','<?php _e('May','tainacan') ?>','<?php _e('Jun','tainacan') ?>','<?php _e('Jul','tainacan') ?>','<?php _e('Aug','tainacan') ?>','<?php _e('Sep','tainacan') ?>','<?php _e('Oct','tainacan') ?>','<?php _e('Nov','tainacan') ?>','<?php _e('Dec','tainacan') ?>'],
                            nextText: '<?php _e('Next','tainacan') ?>',
                            prevText: '<?php _e('Previous','tainacan') ?>',
                            showOn: "button",
                            buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
                            buttonImageOnly: true
                        });
                    });
                </script>    
                  <input style="margin-right: 5px;" 
                        aria-describedby="inputSuccess2Status"
                        value="<?php echo (isset($property['metas']['value'][$i]))? $property['metas']['value'][$i]:'' ?>"
                       class="form-control input_date " 
                       autocomplete="off"
                       onchange="validation_cardinality_property_data('<?php echo $seletor ?>','<?php echo $property['id'] ?>','<?php echo $i ?>');"
                       onkeyup="validation_cardinality_property_data('<?php echo $seletor ?>','<?php echo $property['id'] ?>','<?php echo $i ?>');"
                        <?php echo ($fixed)? 'required="required"':'' ?>
                       id="socialdb_property_<?php echo $property['id']; ?>_field_<?php echo $i; ?>" 
                       name="socialdb_property_<?php echo $property['id']; ?>[]" 
                       type="text" >
        <?php
    }else if ($property['type'] == 'datetime') { 
        $seletor = 'socialdb_property_'.$property['id'].'_field_'.$i;
        ?>     
                 <script>
                    $(function() {
                        $('#field_container_<?php echo $property['id']; ?>_<?php echo $i; ?>').attr('class','col-md-3');
                        $( "#socialdb_property_<?php echo $property['id']; ?>_field_<?php echo $i; ?>" ).datetimepicker({
                            dateFormat: 'dd-mm-yyT',
                            dayNames: ['<?php _e('Sunday','tainacan') ?>','<?php _e('Monday','tainacan') ?>','<?php _e('Tuesday','tainacan') ?>','<?php _e('Wednesday','tainacan') ?>','<?php _e('Thursday','tainacan') ?>','<?php _e('Friday','tainacan') ?>','<?php _e('Saturday','tainacan') ?>'],
                            dayNamesMin: ['<?php _e('S','tainacan') ?>','<?php _e('M','tainacan') ?>','<?php _e('T','tainacan') ?>','<?php _e('W','tainacan') ?>','<?php _e('T','tainacan') ?>','<?php _e('F','tainacan') ?>','<?php _e('S','tainacan') ?>','<?php _e('S','tainacan') ?>'],
                            dayNamesShort: ['<?php _e('Sun','tainacan') ?>','<?php _e('Monday','tainacan') ?>','<?php _e('Tue','tainacan') ?>','<?php _e('Wed','tainacan') ?>','<?php _e('Thu','tainacan') ?>','<?php _e('Fri','tainacan') ?>','<?php _e('Sat','tainacan') ?>'],
                            monthNames: ['<?php _e('January','tainacan') ?>','<?php _e('February','tainacan') ?>','<?php _e('March','tainacan') ?>','<?php _e('April','tainacan') ?>','<?php _e('May','tainacan') ?>','<?php _e('June','tainacan') ?>','<?php _e('July','tainacan') ?>','<?php _e('August','tainacan') ?>','<?php _e('September','tainacan') ?>','<?php _e('October','tainacan') ?>','<?php _e('November','tainacan') ?>','<?php _e('December','tainacan') ?>'],
                            monthNamesShort: ['<?php _e('Jan','tainacan') ?>','<?php _e('Feb','tainacan') ?>','<?php _e('Mar','tainacan') ?>','<?php _e('Apr','tainacan') ?>','<?php _e('May','tainacan') ?>','<?php _e('Jun','tainacan') ?>','<?php _e('Jul','tainacan') ?>','<?php _e('Aug','tainacan') ?>','<?php _e('Sep','tainacan') ?>','<?php _e('Oct','tainacan') ?>','<?php _e('Nov','tainacan') ?>','<?php _e('Dec','tainacan') ?>'],
                            nextText: '<?php _e('Next','tainacan') ?>',
                            prevText: '<?php _e('Previous','tainacan') ?>',
                            showOn: "button",
                            buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
                            buttonImageOnly: true,
                            timeFormat: "HH:mm:ss",
                            timeText: '<?php _e('Time','tainacan') ?>',
                            hourText: '<?php _e('Hour','tainacan') ?>',
                            minuteText: '<?php _e('Minute','tainacan') ?>',
                            secondText: '<?php _e('Second','tainacan') ?>',
                            currentText:'<?php _e('Now','tainacan') ?>',
                            closeText: '<?php _e('Close','tainacan') ?>'
                        });
                    });
                </script>    
                <input style="margin-right: 5px;" 
                       size="20" 
                       autocomplete="off"
                       onchange="validation_cardinality_property_data('<?php echo $seletor ?>','<?php echo $property['id'] ?>','<?php echo $i ?>');"
                        value="<?php echo (isset($property['metas']['value'][$i]))? $property['metas']['value'][$i]:'' ?>"
                        <?php echo ($fixed)? 'required="required"':'' ?>
                       id="socialdb_property_<?php echo $property['id']; ?>_field_<?php echo $i; ?>" 
                       name="socialdb_property_<?php echo $property['id']; ?>[]" 
                       type="text" >
        <?php
    }else if ($property['type'] == 'time') { 
        $seletor = 'socialdb_property_'.$property['id'].'_field_'.$i;
        ?>     
                 <script>
                    $(function() {
                        $('#field_container_<?php echo $property['id']; ?>_<?php echo $i; ?>').attr('class','col-md-3');
                        $( "#socialdb_property_<?php echo $property['id']; ?>_field_<?php echo $i; ?>" ).timepicker({
                            showOn: "button",
                            buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
                            buttonImageOnly: true,
                            timeFormat: "HH:mm:ss",
                            timeOnlyTitle: '<?php _e('Select Time','tainacan') ?>',
                             timeText: '<?php _e('Time','tainacan') ?>',
                            hourText: '<?php _e('Hour','tainacan') ?>',
                            minuteText: '<?php _e('Minute','tainacan') ?>',
                            secondText: '<?php _e('Second','tainacan') ?>',
                            currentText:'<?php _e('Now','tainacan') ?>',
                            closeText: '<?php _e('Close','tainacan') ?>'
                        });
                    });
                </script>    
                <input style="margin-right: 5px;" 
                       size="13" 
                       autocomplete="off"
                        onchange="validation_cardinality_property_data('<?php echo $seletor ?>','<?php echo $property['id'] ?>','<?php echo $i ?>');"
                        value="<?php echo (isset($property['metas']['value'][$i]))? $property['metas']['value'][$i]:'' ?>"
                        <?php echo ($fixed)? 'required="required"':'' ?>
                      id="socialdb_property_<?php echo $property['id']; ?>_field_<?php echo $i; ?>" 
                       name="socialdb_property_<?php echo $property['id']; ?>[]" 
                       type="text" >
        <?php
    } 
    // valida a cardinalidade dos campos
    ?>
    <script> 
         $(function() {
             validation_cardinality_property_data('<?php echo $seletor ?>','<?php echo $property['id'] ?>','<?php echo $i ?>');
         });
    </script>
    <?php
 }





### FIM: ALTERACAO DO FORMULARIO DE ADICAO DE PROPRIEDADE DE DADOS #############

#### #14 -  ALTERACAO DO FORMULARIO DE ADICAO/EDICAO DE PROPRIEDADE DE OBJETO ##############
add_action( 'modificate_insert_item_properties_object', 'ontology_modificate_insert_item_properties_object', 10,1 );
add_action( 'modificate_edit_item_properties_object', 'ontology_modificate_insert_item_properties_object', 10,1 );
add_action( 'modificate_single_item_properties_object', 'ontology_modificate_insert_item_properties_object', 10,1 );

function ontology_modificate_insert_item_properties_object($property) {
     $ontology_max_cardinality_fields = 40;
    $ontology_min_cardinality_fields = 0;
    if($property['metas']['socialdb_property_cardinalidality']&&!empty($property['metas']['socialdb_property_cardinalidality'])){
        $fixed = true;
        $ontology_max_cardinality_fields = $property['metas']['socialdb_property_cardinalidality'];
        $ontology_min_cardinality_fields = $property['metas']['socialdb_property_cardinalidality'];
    }else{
         if($property['metas']['socialdb_property_maxcardinalidality']&&!empty($property['metas']['socialdb_property_maxcardinalidality'])){
            $fixed = false;
            $ontology_max_cardinality_fields = $property['metas']['socialdb_property_maxcardinalidality'];
            if($property['metas']['socialdb_property_mincardinalidality']&&!empty($property['metas']['socialdb_property_mincardinalidality'])){
                $ontology_min_cardinality_fields = $property['metas']['socialdb_property_mincardinalidality'];
            }else{
                $ontology_min_cardinality_fields = 0;
            }
        }
        if($property['metas']['socialdb_property_mincardinalidality']&&!empty($property['metas']['socialdb_property_mincardinalidality'])){
             $fixed = false;
            $ontology_min_cardinality_fields = $property['metas']['socialdb_property_mincardinalidality'];
            if($property['metas']['socialdb_property_maxcardinalidality']&&!empty($property['metas']['socialdb_property_maxcardinalidality'])){
                $ontology_max_cardinality_fields = $property['metas']['socialdb_property_maxcardinalidality'];
            }
        }
        
        if(empty($property['metas']['socialdb_property_mincardinalidality'])&&empty($property['metas']['socialdb_property_maxcardinalidality'])){
            $ontology_max_cardinality_fields = '*';
            $ontology_min_cardinality_fields = 0;
        }
        
    }
    ?>
        <!--div class="alert alert-info" style="font-size: 12px;">        
            <span><strong style="color: grey;"><?php _e('Required Field','tainacan') ?></strong> &#8594; <?php echo ($fixed)? _e('True','tainacan'):_e('False','tainacan')  ?></span><br>   
            <span><strong style="color: grey;"><?php _e('Minimum of items neccessary','tainacan') ?></strong> &#8594; <?php echo $ontology_min_cardinality_fields  ?></span><br>    
            <span><strong style="color: grey;"><?php _e('Maximun of items nececssary','tainacan') ?></strong> &#8594;<?php echo ($ontology_max_cardinality_fields===999999)? '*':$ontology_max_cardinality_fields  ?></span><br>    
        </div-->  
        <input type="hidden" id="property_<?php echo $property['id']  ?>_error_cardinality_message"  
                       value="<?php echo sprintf(__('The field ( %s ) is not matching its cardinality!','tainacan'), $property['name'])   ?>">        
        <input type="hidden" id="property_<?php echo $property['id']  ?>_is_fixed" name="property_<?php echo $property['id']  ?>_is_fixed" value="<?php echo ($fixed)? 'true':'false'  ?>">
        <input type="hidden" id="property_<?php echo $property['id']  ?>_min_cardinality" name="property_<?php echo $property['id']  ?>_min_cardinality" value="<?php echo $ontology_min_cardinality_fields  ?>">
        <input type="hidden" id="property_<?php echo $property['id']  ?>_max" name="property_<?php echo $property['id']  ?>_max" value="<?php echo ($ontology_max_cardinality_fields==='*') ? 60 : $ontology_max_cardinality_fields  ?>">
        <input type="hidden" id="property_<?php echo $property['id']  ?>_max_cardinality" name="property_<?php echo $property['id']  ?>_max_cardinality" value="<?php echo $ontology_max_cardinality_fields  ?>">
        <input type="hidden" id="property_validation_<?php echo $property['id']; ?>" class="validation_properties_cardinality" value="" >
        <input type="hidden" class="obrigation-message-<?php echo $property['id']; ?>" value="<?php echo __('This field is required!','tainacan') ?>" >
        <input type="hidden" class="optional-message-<?php echo $property['id']; ?>" value="<?php echo sprintf(__('At least, fill %s field in this property!','tainacan'),$ontology_min_cardinality_fields); ?>" >
        <script> 
         $(function() {
             verify_cardinality_property_object_field('<?php echo 'select[name="socialdb_property_'.$property['id'].'[]"]' ?>','<?php echo $property['id'] ?>');
         });
        </script>
    <?php         
}


### FIM: ALTERACAO DO FORMULARIO DE ADICAO DE PROPRIEDADE DE DADOS ############# 
 
#### #15 -  ALTERACAO DA PAGINA DO ITEM PARA PROPRIEDADE DE DADOS ##############


add_action( 'modificate_single_item_properties_data', 'ontology_modificate_single_item_properties_data', 10, 2 );
function ontology_modificate_single_item_properties_data($property,$object_id) {
     //cardinalidade
    $cont = 0;
    $array_cardinality = get_property_cardinality($property);
    $ontology_max_cardinality_fields = $array_cardinality['max'];
    $ontology_min_cardinality_fields = $array_cardinality['min'];
    $fixed = $array_cardinality['fixed'];
    ?>  
    <div style="display: none;" id="single_property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
        <?php 
            if(has_action('modificate_label_insert_item_properties')):
                do_action('modificate_label_insert_item_properties', $property);
            endif;
            ?>
        <input type="hidden" id="property_<?php echo $property['id']  ?>_error_cardinality_message"  
                       value="<?php echo sprintf(__('The field ( %s ) is not matching its cardinality!','tainacan'), $property['name'])   ?>">        
        <input type="hidden" id="property_<?php echo $property['id']  ?>_is_fixed" name="property_<?php echo $property['id']  ?>_is_fixed" value="<?php echo ($fixed)? 'true':'false'  ?>">
        <input type="hidden" id="property_<?php echo $property['id']  ?>_min_cardinality" name="property_<?php echo $property['id']  ?>_min_cardinality" value="<?php echo $ontology_min_cardinality_fields  ?>">
        <input type="hidden" id="property_<?php echo $property['id']  ?>_max_cardinality" name="property_<?php echo $property['id']  ?>_max_cardinality" value="<?php echo $ontology_max_cardinality_fields  ?>">
       <!-- Hiddens para validacao inline -->
       <input type="hidden" id="property_<?php echo $property['id']; ?>_max" name="socialdb_property_<?php echo $property['id']; ?>_total_fields" value="<?php echo $ontology_max_cardinality_fields ?>" >
        <input type="hidden" id="property_<?php echo $property['id']; ?>_min" value="<?php echo $ontology_min_cardinality_fields ?>" >
        <input type="hidden" id="property_validation_<?php echo $property['id']; ?>" class="validation_properties_cardinality" value="" >
        <input type="hidden" class="obrigation-message-<?php echo $property['id']; ?>" value="<?php echo __('This field is required!','tainacan') ?>" >
        <input type="hidden" class="optional-message-<?php echo $property['id']; ?>" value="<?php echo sprintf(__('At least, fill %s field in this property!','tainacan'),$ontology_min_cardinality_fields); ?>" >
       <?php for($i=0;$i<$ontology_max_cardinality_fields;$i++): ?>
        <div id="field_property_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
             class="row" <?php echo ($i>=1&&!$fixed&&$i>=$ontology_min_cardinality_fields
                        &&(!isset($property['metas']['value'])||!is_array($property['metas']['value'])||count($property['metas']['value'])-1<$i))
             ? 'style="display:none;"':'style="margin-bottom:15px;"' ?> >
            <div id="field_container_<?php echo $property['id']; ?>_<?php echo $i; ?>" class="col-md-11">
                    <div id="form_group_<?php echo $property['id']; ?>_<?php echo $i; ?>"  
                         class="form-group has-feedback"> 
                        <?php get_html_property_data_types($property,$i,$fixed); ?>
                        <span id="icon_<?php echo $property['id']; ?>_<?php echo $i; ?>" class="glyphicon" aria-hidden="true"></span>
                        <span id="status_field_<?php echo $property['id']; ?>_<?php echo $i; ?>" class="sr-only"></span>
                        <input type="hidden" value="false" id="is_valid_<?php echo $property['id']; ?>_<?php echo $i; ?>" class="is_valid_<?php echo $property['id']; ?>">
                    </div>  
            </div>      
            <div class="col-md-1">    
            <?php  if(!$fixed&&$ontology_max_cardinality_fields>1&&$ontology_max_cardinality_fields-1!=$i&&$i>=$ontology_min_cardinality_fields-1
                            ):  $cont++; ?>
                    <button type="button" 
                        <?php echo (($cont==1&&!$property['metas']['value'])||count($property['metas']['value'])-1==$i)? '':'style="display:none"' ?> 
                        id="button_property_<?php echo $property['id']; ?>_<?php echo $i; ?>"
                        onclick="show_field_properties(<?php echo $property['id']; ?>,<?php echo $i+1; ?>)"
                        class="btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span>
                    </button>
                <?php endif; ?>
            </div>    
        </div>  
        <?php endfor; ?>
    </div>   
    <?php  
}

#### FIM  -  ALTERACAO DA PAGINA DO ITEM PARA PROPRIEDADE DE DADOS ##############

#### #16 -  ADICIONA O BOTAO DE EDITAR PROPRIEDADE NA PAGINA DA PROPRIEDADE ##############
add_action( 'add_button_edit_property', 'ontology_add_button_edit_property',10,1);
function ontology_add_button_edit_property($property_id){
    $link = "'" . get_template_directory_uri() . '/modules/' . MODULE_ONTOLOGY . "'";
    ?>
    <button type="button" onclick="showPageEditProperty('<?php echo $property_id ?>',<?php echo $link ?>);" class="btn btn-default btn-xs" >
               <span class="glyphicon glyphicon-edit"></span>
    </button>    
    <?php    
}
#### FIM -  ALTERACAO DA PAGINA DO ITEM PARA PROPRIEDADE DE DADOS ##############

#### #17 -  ADICIONA NO MENU DA COLECAO A OPCAO DE FILTROS #####################
add_action('add_configuration_menu_tainacan', 'add_filter_ontology_menu');
function add_filter_ontology_menu() {
    $link = "'" . get_template_directory_uri(). "'";
    echo '<li>
                           <a style="cursor: pointer;" 
                              onclick="showSearchConfiguration(' . $link . ');" >
                               <span class="glyphicon glyphicon-folder-open"></span>&nbsp;
                                   ' . __('Filters', 'tainacan')
    . '</a>'
    . '</li>';
    echo '<li>
                           <a style="cursor: pointer;" 
                              onclick="showRankingConfiguration(' . $link . ');" >
                               <span class="glyphicon glyphicon-star"></span>&nbsp;
                                   ' . __('Rankings', 'tainacan')
    . '</a>'
    . '</li>';
}
#### FIM -  ADICIONA NO MENU DA COLECAO A OPCAO DE FILTROS #####################

############ #18 - ALTERA O THUMBNAIL DOS ITEMS/COLEÇÕES #######################
function ontology_alter_thumbnail_items($type) {
     $link =  get_template_directory_uri() . '/modules/' . MODULE_ONTOLOGY . "/libraries/images/thumbnail.png"; 
    return $link;
}
add_filter( 'alter_thumbnail_items', 'ontology_alter_thumbnail_items', 10, 3 );

function ontology_alter_thumbnail_collection($type) {
     $link =  get_template_directory_uri() . '/modules/' . MODULE_ONTOLOGY . "/libraries/images/ontology.png"; 
    return $link;
}
add_filter( 'alter_thumbnail_collections', 'ontology_alter_thumbnail_collection', 10, 3 );
################################################################################

############ #19 - OPERCAO DE METADADOS DE CATEGORIA ###########################
function ontology_tainacan_operation_metadata_category() {
    return 'list';
}
add_filter( 'tainacan_operation_metadata_category', 'ontology_tainacan_operation_metadata_category');

###############################################################################
############### #20 Importador OWL e FUNÇÕES AUXILIARES #######################

add_action('add_select_box', 'add_select_box_import_ontology');
function add_select_box_import_ontology()
{
    ?>
    <label for='file type'>Tipo de arquivo</label>
    <select class='form-control' name='file_type'>
        <option value='rdf/owl'>RDF/OWL</option>
        <option value='tainacan-zip'>Tainacan (Zip)</option>
    </select>
    <?php
}

function parse_owl1()
{

    $return = [];
    session_write_close();
    ini_set('max_execution_time', '0');
    $return['result'] = false;
    if($_FILES['collection_file']['name']) {
        $file = $_FILES["collection_file"];
        $file_name = $file["name"];
        $tmp_name = $file["tmp_name"];
        $file_type = strtolower(end(explode('.', $file_name)));

        $name = explode(".", $file_name);

        if (($file_type == 'rdf' || $file_type == 'owl') && ($received_file = simplexml_load_file($tmp_name)) !== false) {

            $namespace = $received_file->getNamespaces(true);//Pega todos os namespaces do documento

            //Recupera todos os elementos de namespace OWL
            //Ontology, Class, ObjectProperty, DatatypeProperty
            $owl_tags = $received_file->children($namespace['owl']);

            //Elementos do documento
            $owl_ontology = $owl_tags->Ontology;
            $owl_classes = $owl_tags->Class;
            $owl_object_properties = $owl_tags->ObjectProperty;
            $owl_data_type_properties = $owl_tags->DatatypeProperty;
            $owl_functional_properties = $owl_tags->FunctionalProperty;
            $owl_transitive_properties = $owl_tags->TransitiveProperty;
            $owl_symmetric_properties = $owl_tags->SymmetricProperty;
            $owl_individuals = $owl_tags->NamedIndividual;

            $owl_declarations = $owl_tags->Declaration;
            //Preparando outros elementos
            if(count($owl_declarations) > 0)
            {
                //Capturando entrada
                $owl_subclassOf = $owl_tags->SubClassOf;
                $owl_class_assertions = $owl_tags->ClassAssertion;
                $owl_data_properties_domain_tags = $owl_tags->DataPropertyDomain;
                $owl_data_properties_range_tags = $owl_tags->DataPropertyRange;
                $owl_subdata_property_of_tags = $owl_tags->SubDataPropertyOf;
                $owl_functional_data_property_tags = $owl_tags->FunctionalDataProperty;

                //Declarações de retorno
                $ret_class_tags = []; $ret_named_individuals_tags = []; $ret_data_properties_tags = []; $ret_subclass_structure = [];
                $ret_data_properties_domain_tags = []; $ret_data_properties_range_tags = []; $ret_subdata_property_of = [];
                $ret_functional_data_property = []; $ret_object_property = [];

                $true = prepare_elements($owl_declarations, $ret_class_tags, $ret_named_individuals_tags, $ret_data_properties_tags,
                    $ret_object_property,
                    $owl_subclassOf, $ret_subclass_structure,
                    $owl_data_properties_domain_tags, $ret_data_properties_domain_tags,
                    $owl_data_properties_range_tags, $ret_data_properties_range_tags,
                    $owl_subdata_property_of_tags, $ret_subdata_property_of,
                    $owl_functional_data_property_tags, $ret_functional_data_property
                );

                if($true)
                {
                    $owl_classes = $ret_class_tags;
                    $owl_data_type_properties = $ret_data_properties_tags;
                    $owl_object_properties = $ret_object_property;
                }
                else
                {
                    throw new Exception("Houve um erro durante a importação");
                }
            }

            //Individuos
            $description_ind = $received_file->children($namespace['rdf'])->Description;
            
            try
            {
                //Tratando ontologias
                $collection_id = treats_ontology($owl_ontology, $namespace);

                //Tratando Classes
                $created_classes = treats_classes($owl_classes, $collection_id, $namespace, $ret_subclass_structure);

                //Tratando ObjectProperty
                $created_object_properties = treats_object_properties($owl_object_properties, $namespace, $owl_classes, $created_classes,
                    $owl_functional_properties, $collection_id);

                //Tratando DatatypeProperty
                $created_data_type_properties = treats_data_type_properties($owl_data_type_properties, $owl_classes, $created_classes,
                    $owl_functional_properties, $namespace, $collection_id,
                    $ret_data_properties_domain_tags, $ret_data_properties_range_tags, $ret_subdata_property_of,
                    $ret_functional_data_property
                    );
                $return['result'] = true;
                $return['url'] = get_the_permalink($collection_id);
                return $return;
                //Tratando FunctionalProperty
                treats_functional_property($owl_functional_properties, $created_data_type_properties, $created_object_properties,
                    $owl_classes, $created_classes, $namespace, $collection_id);
                
                //Tratando Class Restrictions
                treats_class_restriction($owl_classes, $created_classes, $created_object_properties, $created_data_type_properties,
                    $namespace);
                
                //Tratando transitive properties
                treats_transitive_properties($owl_transitive_properties, $created_object_properties, $owl_classes, $created_classes,
                    $namespace, $collection_id);
                
                //Tratando symmetric properties
                treats_symmetric_properties($owl_symmetric_properties,$created_object_properties, $owl_classes, $created_classes,
                    $namespace, $collection_id);

                //Trata individuos
                treats_individual($owl_individuals, $namespace, $collection_id, $created_classes);

                
            }catch (Exception $e)
            {
                $return['message'] = $e->getMessage();
                return $return;
            }

            $return['result'] = true;
            $return['url'] = get_the_permalink($collection_id);
        }
    }
    return $return;
}

/*
 * Trata Ontologia
 */
function treats_ontology(&$ontology_tags, &$namespace)
{
    //Tratamento Ontologia
    $data['collection_name'] = $ontology_tags->children($namespace['rdfs'])->label;
    if(!$data['collection_name'])
    {
        $data['collection_name'] = end(explode("/", $ontology_tags->attributes($namespace['rdf'])['about']));

        if(!$data['collection_name'])
        {
            $data['collection_name'] = strval(time());
        }
    }

    $retorno = simple_add($data);

    if($retorno != false)
    {
        return $retorno['collection_id'];
    }
    else
    {
        throw new Exception("Ontologia com esse nome já foi criada");
    }
}

/*
 * Trata Classes
 */
function treats_classes(&$class_tags, &$collection_id, &$namespace, &$owl_subclassOf)
{
    //Tratamento para não criar classes repetidas
    $created_classes = [];

    $count = count($class_tags);

    for($i = 0; $i < $count; $i++)
    {
        create_class($class_tags, $created_classes, $class_tags[$i], $collection_id, $namespace, $owl_subclassOf);
    }
    
    return $created_classes;
}

function create_class(&$class_tags, &$created_classes, &$classe, &$collection_id , &$namespace, &$subclassOf_tags)
{
    $category_model = new CategoryModel();

    //Definindo o nome da Classe
    if(($nome = $classe->attributes($namespace['rdf'])['ID']))
        $data['category_name'] = strval($nome);
    else
    {
        $data['category_name'] = strval($classe->children($namespace['rdfs'])->label);
        if(!$data['category_name'])
        {
            $data['category_name'] = end(explode("/", strval($classe->attributes($namespace['rdf'])['about'])));
            if(!$data['category_name'])
            {
                $data['category_name'] = str_replace("#", "", $classe->attributes()['IRI']);
                if(!$data['category_name'])
                {
                    throw new Exception("Impossivel criar classe, nome não encontrado");
                }
                else $with_declarations = true;
            }

        }
    }

    if($with_declarations != true)
    {
        //Descrição da classe
        $data['category_description'] = strval($classe->children($namespace['rdfs'])->comment);

        //Variaveis que guardam o nome da Classe Pai
        foreach($classe->children($namespace['rdfs'])->subClassOf as $subClasse)
        {
            if($subClasse->children($namespace['owl'])->Class->attributes($namespace['rdf'])['about'])
                $aboutClassSubClassOf = $subClasse->children($namespace['owl'])->Class->attributes($namespace['rdf'])['about'];
        }

        //Define o da classe pai, caso a classe seja raiz então $root_name receberá o nome da propria classe
        $resourceSubClassOf = $classe->children($namespace['rdfs'])->subClassOf->attributes($namespace['rdf'])['resource'];
        if($aboutClassSubClassOf != null)
            $root_name = str_replace("#", '',$aboutClassSubClassOf);
        else if($resourceSubClassOf != null)
        {
            $root_name = str_replace("#", '',$resourceSubClassOf);
        }
        else
            $root_name = $data['category_name'];

        if(end(explode ("/", strval($resourceSubClassOf))) == "?category=socialdb_taxonomy")
        {
            $resourceSubClassOf = null;
        }

        $id_about = strval($classe->attributes($namespace['rdf'])['ID']);
        if($id_about == null)
            $id_about = strval($classe->attributes($namespace['rdf'])['about']);

        $data['idAbout'] = $id_about;
    }
    else
    {
        $aboutClassSubClassOf = $subclassOf_tags[$data['category_name']];
        if(!$aboutClassSubClassOf)
            $root_name = $data['category_name'];
        else $root_name = $aboutClassSubClassOf;

        $data['idAbout'] = str_replace("#", "", $classe->attributes()['IRI']);
    }


    
    //Caso a classe ainda não tenha sido criada
    if($created_classes[$root_name]['created'] !== true)
    {
        //Verifica se a classe é subclasse de alguma classe
        if($aboutClassSubClassOf || $resourceSubClassOf)
        {
            $not_created_class = class_search($root_name, $class_tags, $namespace);

            /*
             *  Caso uma classe seja subClasse de uma classe não declarada no documento da importação
             * então ela é criada como classe raiz
             */

            if(gettype($not_created_class) == "object")
            {
                create_class($class_tags, $created_classes, $not_created_class, $collection_id, $namespace, $subclassOf_tags);//Chamada recursiva para criar pai
            }
            else
            {
                $new_data = $data;
                $new_data['category_name'] = $root_name;
                record_class($created_classes, $root_name, $category_model, $new_data, $collection_id, true);
            }
            record_class($created_classes, $root_name, $category_model, $data, $collection_id, false);
        }
        else/*Criação de classe raiz*/
        {
            record_class($created_classes, $root_name, $category_model, $data, $collection_id, true);
        }
    }//Caso a classe já tenha sido criada então é criada uma relção entre entre a classe e sua classe pai
    else if($root_name != $data['category_name'] && $root_name != $data['idAbout'])
    {
        record_class($created_classes, $root_name, $category_model, $data, $collection_id, false);
    }
}

function record_class(&$created_classes, &$root_name, &$category_model, &$data, &$collection_id, $is_root)
{
    //Define o pai da classe a ser criada
    if($data['dont_create'] != true)
    {
        if($is_root) $parent = $category_model->get_category_taxonomy_root();
        else $parent = $created_classes[$root_name]['creation_id'];
    }

    $new_class = wp_insert_term($data['category_name'], 'socialdb_category_type', array('parent' => $parent,
        'slug' => $category_model->generate_slug($data['category_name'], $collection_id), 'description' => $category_model->set_description($data['category_description'])));
    
    if (!is_wp_error($new_class) && $new_class['term_id']) {// se a classe foi inserida com sucesso
        instantiate_metas($new_class['term_id'], 'socialdb_taxonomy', 'socialdb_category_type', true);
        insert_meta_default_values($new_class['term_id']);
        $category_model->update_metas($new_class['term_id'], $data);
        $data['success'] = 'true';
        $data['term_id'] = $new_class['term_id'];
        $log_data = ['user_id' => get_current_user_id(),'resource_id' => $data['term_id'], 'event_type' => 'user_category', 'event' => 'add' ];
        Log::addLog($log_data);
    } else {
        $data['success'] = 'false';
    }

    //Marcar classe como criada
    if($data['dont_create'] != true)
    {
        $created_classes[$data['idAbout']]['created'] = true;
        $created_classes[$data['idAbout']]['creation_id'] = $new_class['term_id'];
    }

    //Define ligação entre ontologia e a classe raiz, ligação criada apenas para classes raiz
    if($is_root)
        $category_model->add_facet($new_class['term_id'], $collection_id);

    return $new_class['term_id'];
}

function class_search(&$class_to_search, &$class_tags, &$namespace)
{
    foreach($class_tags as $class)
    {
        if(strcmp($class_to_search, $class->attributes($namespace['rdf'])['about']) == 0 ||
            strcmp($class_to_search, $class->attributes($namespace['rdf'])['ID']) == 0 ||
            strcmp($class_to_search, strval($class->children($namespace['rdfs'])->label)) == 0 ||
            strcmp($class_to_search, strval($class->attributes()['IRI'])) == 0
        )
        {
            return $class;
        }
    }
    return false;
}
//Adiciona uma Classe
function simple_add(&$data) {
    error_reporting(0);
    $collection_model = new CollectionModel();
    if ($collection_model->verify_collection($data['collection_name'])) {
        return false;
    }

    $collection = array(
        'post_type' => 'socialdb_collection',
        'post_title' => $data['collection_name'],
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
    );

    $collection_id = wp_insert_post($collection);
    $collection_model->update_privacity($collection_id); // atualizando a privacidade da colecao
    $post = get_post($collection_id);
    $collection_model->insert_permissions_default_values($collection_id);
    insert_taxonomy($post->ID, 'socialdb_collection', 'socialdb_collection_type', true); // (Criada em: functions.php) insere a categoria que identifica o tipo da colecao
    $collection_model->createSocialMappingDefault($post->ID);
    // metadado para o nome do objeto da colecao
    update_post_meta($collection_id, 'socialdb_collection_object_name', $data['collection_object']);
    //filtro para o nome da colecao
    $data['collection_object'] = $data['collection_name'];
    if (has_filter('collection_object')) {
        $name = (isset( $data['collection_object'])&&!empty($data['collection_object'])?$data['collection_object']:$data['collection_name']);
        $object_name = apply_filters('collection_object', $name);
    } else {
        $object_name = __('Categories of ', 'tainacan') . $data['collection_name'];
    }
    create_root_collection_category($post->ID, $object_name); //(Criada em: functions.php) cria a categoria inicial que identifica os objetos da colecao

    return array('post_id' => $post->ID, 'collection_id' => $collection_id);
}

/*
 * Trata objectProperty
 */
function treats_object_properties(&$object_property_transitive_property_tags, &$namespace, &$class_tags, &$created_classes,
                                  &$functional_property_tags, &$collection_id)
{
    //Tratamento para não criar objectPropertys repetidas
    $created_object_transitive_property = [];//create_associative_table($object_property_transitive_property_tags, $namespace);

    $count = count($object_property_transitive_property_tags);

    for($i = 0; $i < $count; $i++)
    {
        create_object_properties($object_property_transitive_property_tags, $created_object_transitive_property,
            $object_property_transitive_property_tags[$i], $class_tags, $created_classes, $functional_property_tags,
            $namespace, $collection_id);
    }

    return $created_object_transitive_property;
}

//Adiciona um Object Property

function create_object_properties(&$object_property_tags, &$created_object_property, &$object_property, &$class_tags, &$created_classes,
                                  &$functional_property_tags, &$namespace, &$collection_id)
{
    //Caso no nome da propriedade seja guardada em uma label e o que referencia a classe seja o about
    if(($about = $object_property->attributes($namespace['rdf'])['about']))
    {
        $data['idAbout'] = strval($about);
        $data['name'] = strval($object_property->children($namespace['rdfs'])->label);
        if($data['name'] == null)
        {
            $data['name'] = $data['idAbout'];
        }
    }
    else//Caso o nome da propriedade seja guardada em ID e sirva tanto para nomea-la quanto para identifica-la
    {
        $data['idAbout'] = strval($object_property->attributes($namespace['rdf'])['ID']);
        $data['name'] = strval($object_property->attributes($namespace['rdf'])['ID']);
        if(!$data['idAbout'])
        {
            $data['idAbout'] = strval($object_property->attributes()['IRI']);
            $data['name'] = strval($object_property->attributes()['IRI']);
        }
    }

    $data['transitive'] = false;
    $data['functional'] = false;

    $type_tags = $object_property->children($namespace['rdf'])->type;
    if($type_tags)
    {
        foreach($type_tags as $type)
        {
            $type_resource_1 = end(explode(";", $type->attributes($namespace['rdf'])['resource']));

            $type_resource_2 = end(explode("#", $type->attributes($namespace['rdf'])['resource']));
            if(strcmp($type_resource_1, "FunctionalProperty") == 0 || strcmp($type_resource_2, "FunctionalProperty") == 0)
                $data['functional'] = true;

            if(strcmp($type_resource_1, "TransitiveProperty") == 0 || strcmp($type_resource_2, "TransitiveProperty") == 0)
                $data['transitive'] = true;
        }
    }
    else
    {
        $new_name = "#" . $data['idAbout'];
        $new_functional = class_search($new_name, $functional_property_tags, $namespace);

        if (gettype($new_functional) == "object") {
            $data['functional'] = true;
        }
    }

    $data['hasFatherResource'] = $object_property->children($namespace['rdfs'])->subPropertyOf->attributes($namespace['rdf'])['resource'];

    if($data['hasFatherResource'])
    {
        if($created_object_property[($data['hasFatherResource'] = str_replace('#', '', $data['hasFatherResource']))]['created'] != true)
        {
            $object_property_to_create = class_search($data['hasFatherResource'], $object_property_tags, $namespace);

            if(gettype($object_property_to_create) == "object")
            {
                create_object_properties($object_property_tags, $created_object_property, $object_property_to_create, $class_tags,
                    $created_classes, $functional_property_tags, $namespace, $collection_id);
            }
            else
            {
                $new_data = $data;
                $new_data['name'] = $data['hasFatherResource'];
                add_object_property($created_object_property, $object_property, $class_tags, $created_classes, $new_data, $namespace, true, $collection_id);
            }

            add_object_property($created_object_property, $object_property, $class_tags, $created_classes, $data, $namespace, false, $collection_id);

        }
        else if($created_object_property[$data['idAbout']]['created'] == true)
        {
            add_object_property($created_object_property, $object_property, $class_tags, $created_classes, $data, $namespace, false, $collection_id);
        }
    }
    else if($created_object_property[$data['idAbout']]['creation_id'] != true)
    {
        add_object_property($created_object_property, $object_property, $class_tags, $created_classes, $data, $namespace, true, $collection_id);
    }
}

function add_object_property(&$created_objectProperty, &$object_property, &$class_tags, &$created_classes, &$data, &$namespace, $is_root, &$collection_id)
{
    $range_resource = $object_property->children($namespace['rdfs'])->range->attributes($namespace['rdf'])['resource'];
    $domain_resource = $object_property->children($namespace['rdfs'])->domain->attributes($namespace['rdf'])['resource'];

    $range_class = class_search($range_resource, $class_tags, $namespace);
    $domain_class = class_search($domain_resource, $class_tags, $namespace);


    //Recupera o ID da classe referenciada em DOMAIN
    if(gettype($domain_class) == 'object')
    {
        if(($name = $domain_class->attributes($namespace['rdf'])['ID']))
            $domain_class_name = strval($name);
        else
            $domain_class_name = $domain_class->attributes($namespace['rdf'])['about'];

        $data['id_domain_class'] = $created_classes[strval($domain_class_name)]['creation_id'];
    }

    if($is_root) {

        $new_property = wp_insert_term($data['name'], 'socialdb_property_type', array('parent' => get_property_type_id('socialdb_property_object'),
            'slug' => generate_slug($data['name'], 0)));
    }
    else {
        $new_property = wp_insert_term($data['name'], 'socialdb_property_type', array('parent' => $created_objectProperty[$data['hasFatherResource']]['creation_id'],
            'slug' => generate_slug($data['name'], 0)));
    }
    if($data['functional'] == true)
    {
        update_term_meta($new_property['term_id'], 'socialdb_property_required', "true");
        update_term_meta($new_property['term_id'], 'socialdb_property_functional', "true");
    }

    //Caso RANGE não seja Thing então é recuperado o ID da classe referenciada
    if(gettype($range_class) == 'object')
    {
        if(($name = $range_class->attributes($namespace['rdf'])['ID']))
        {
            $range_class_name = strval($name);
        }
        else
        {
            $range_class_name = $range_class->attributes($namespace['rdf'])['about'];
        }
        $data['id_range_class'] = $created_classes[strval($range_class_name)]['creation_id'];
        update_term_meta($new_property['term_id'], 'socialdb_property_object_category_id', $data['id_range_class']);
    }
    else if($object_property->children($namespace['rdfs'])->range)
    {
        $thing = get_term_by('name','socialdb_taxonomy','socialdb_category_type')->term_id;
        update_term_meta($new_property['term_id'], 'socialdb_property_object_category_id', $thing);
    }

    if($data['transitive'] == true)
    {
        update_term_meta($new_property['term_id'], "socialdb_property_transitive", "true");
    }

    if($data['symmetric'] == true)
    {
        update_term_meta($new_property['term_id'], "socialdb_property_simetric", "true");
    }

    // update_term_meta($new_property['term_id'], 'socialdb_property_created_category',(string)$socialdb_collection_object_type);
    update_term_meta($new_property['term_id'], 'socialdb_property_created_category',$data['id_domain_class']);
    add_term_meta($data['id_domain_class'],'socialdb_category_property_id',$new_property['term_id']);
    update_term_meta($new_property['term_id'], 'socialdb_property_collection_id', $collection_id);

    $created_objectProperty[$data['idAbout']]['creation_id'] = $new_property['term_id'];
    $created_objectProperty[$data['idAbout']]['created'] = true;

    return $new_property['term_id'];

}

function get_property_type_id($property_parent_name) {
    $property_root = get_term_by('name', $property_parent_name, 'socialdb_property_type');
    return $property_root->term_id;
}

function get_term_imported_id(&$imported_id) {
    global $wpdb;
    $wp_taxonomymeta = $wpdb->prefix . "termmeta";
    $query = "
            SELECT * FROM $wp_taxonomymeta t
                    WHERE t.meta_key like 'socialdb_imported_id' AND t.meta_value = {$imported_id}
                ";
    $term = $wpdb->get_results($query);
    if ($term && is_array($term)) {
        $last_index = count($term);
        return $term[$last_index - 1]->term_id;
    } elseif ($term && is_object($term)) {
        return $term->term_id;
    }
    return false;
}

function generate_slug($string, $collection_id) {
    return sanitize_title(remove_accent($string)) . "_" . mktime() . rand(0, 100);
}

/*
 * Trata DataType Property
 */
function treats_data_type_properties(&$data_type_propety_tags, &$class_tags, &$created_classes, &$functional_property_tags, &$namespace, &$collection_id,
    &$data_properties_domain_tags, &$data_properties_range_tags, &$subdata_property_of, &$functional_data_property_tags)
{
    //$created_data_type_properties = create_associative_table($data_type_propety_tags, $namespace);
    $created_data_type_properties = [];
    $count = count($data_type_propety_tags);
    for($i = 0; $i < $count; $i++)
    {
        create_data_type_properties($data_type_propety_tags, $created_data_type_properties, $class_tags, $created_classes,
            $functional_property_tags, $data_type_propety_tags[$i], $namespace, $collection_id, $data_properties_domain_tags,
            $data_properties_range_tags, $subdata_property_of, $functional_data_property_tags);
    }

    return $created_data_type_properties;
}

function create_data_type_properties(&$data_type_propety_tags, &$created_dataType_property, &$class_tags, &$created_classes, &$functional_property_tags,
    &$datatype_property, &$namespace, &$collection_id, &$data_properties_domain_tags, &$data_properties_range_tags,
    &$subdata_property_of, &$functional_data_property_tags)
{
    if(($about = $datatype_property->attributes($namespace['rdf'])['about']))
    {
        $data['idAbout'] = strval($about);
        $data['name'] = strval($datatype_property->children($namespace['rdfs'])->label);
        if(!$data['name'])
        {
            $data['name'] = $data['idAbout'];
        }
    }
    else if(($id = $datatype_property->attributes($namespace['rdf'])['ID']))//Caso o nome da propriedade seja guardada em ID e sirva tanto para nomea-la quanto para identifica-la
    {
        $data['idAbout'] = strval($id);
        $data['name'] = $data['idAbout'];
    }
    else
    {
        $data['idAbout'] = str_replace("#", "", $datatype_property->attributes()['IRI']);
        $data['name'] = $data['idAbout'];
        $with_declaration = true;
    }

    if($with_declaration != true)
    {
        $data['hasFatherResource'] = $datatype_property->children($namespace['rdfs'])->subPropertyOf->attributes($namespace['rdf'])['resource'];

        $data['functional'] = end(explode(";", $datatype_property->children($namespace['rdf'])->type->attributes($namespace['rdf'])['resource']));
        if(strcmp($data['functional'], "FunctionalProperty") == 0)
            $data['functional'] = true;
        else
        {
            $new_name = "#".$data['idAbout'];
            $new_functional = class_search($new_name, $functional_property_tags, $namespace);

            if(gettype($new_functional) == "object")
                $data['functional'] = true;
            else $data['functional'] = false;
        }
    }
    else
    {
        $data['hasFatherResource'] = $subdata_property_of[$data['name']];

        if($functional_data_property_tags[$data['name']] == true) $data['functional'] = true;
        else $data['functional'] = false;
    }

    if($data['hasFatherResource'])
    {
        if($created_dataType_property[($data['hasFatherResource'] = str_replace('#', '', $data['hasFatherResource']))]['created'] != true)
        {
            $datatype_property_to_create = class_search($data['hasFatherResource'], $data_type_propety_tags, $namespace);

            if(gettype($datatype_property_to_create) == "object")
            {
                create_data_type_properties($data_type_propety_tags, $created_dataType_property, $class_tags, $created_classes,
                    $functional_property_tags,$datatype_property_to_create, $namespace, $collection_id, $data_properties_domain_tags,
                    $data_properties_range_tags, $subdata_property_of, $functional_data_property_tags);
            }else
            {
                $new_data = $data;
                $new_data['name'] = $data['hasFatherResource'];
                add_data_type_property($created_dataType_property, $datatype_property, $class_tags, $created_classes, $new_data, $namespace,
                    true, $collection_id, $data_properties_range_tags, $data_properties_domain_tags);
            }

            add_data_type_property($created_dataType_property, $datatype_property, $class_tags, $created_classes, $data, $namespace,
                false, $collection_id, $data_properties_range_tags, $data_properties_domain_tags);
        }
        else if($created_dataType_property[$data['idAbout']]['created'] == true)//Criando filho de pai ja criado
        {
            add_data_type_property($created_dataType_property, $datatype_property, $class_tags, $created_classes, $data, $namespace,
                false, $collection_id, $data_properties_range_tags, $data_properties_domain_tags);
        }
    }
    else if($created_dataType_property[$data['idAbout']]['creation_id'] != true)
    {
        add_data_type_property($created_dataType_property, $datatype_property, $class_tags, $created_classes, $data, $namespace,
            true, $collection_id, $data_properties_range_tags, $data_properties_domain_tags);
    }
}

function add_data_type_property(&$created_data_type_property, &$datatype_property, &$class_tags, &$created_classes, &$data, &$namespace,
    $is_root, &$collection_id, &$data_properties_range_tags, &$data_properties_domain_tags)
{
    if(gettype($datatype_property) == "object")
    {
        $range = strval(end(explode("#", $datatype_property->children($namespace['rdfs'])->range->attributes($namespace['rdf'])['resource'])));//Tipo de dado
        if(!$range)
        {
            $range = $data_properties_range_tags[$datatype_property->attributes()['IRI']];
            if(!$range)
            {
                $range = "string";
            }
        }

        $domain = strval($datatype_property->children($namespace['rdfs'])->domain->attributes($namespace['rdf'])['resource']);
        if(!$domain)
        {
            $domain = $data_properties_domain_tags[$datatype_property->attributes()['IRI']];
        }
        $domain_class = class_search($domain,$class_tags,$namespace);

        if(gettype($domain_class) != "object")
        {
            $domain = str_replace("#", "", $domain);
            $domain_class = class_search($domain, $class_tags, $namespace);
        }

        //ID da classe de dominio
        if(gettype($domain_class) == "object")
        {
            if(($name = $domain_class->attributes($namespace['rdf'])['ID']))
                $domain_class_name = strval($name);
            else
                $domain_class_name = strval($domain_class->attributes($namespace['rdf'])['about']);

            $data['id_domain_class'] = $created_classes[strval($domain_class_name)]['creation_id'];
        }else
        {
            throw new Exception("Classe referenciada não foi encontrada: ".$domain);
        }
    }
    else
    {
        $range = $datatype_property['data_type'];//Seleciona tipo de dado
        $data['id_domain_class'] = $datatype_property['id_domain_class'];
    }

    if($is_root)
        $new_property = wp_insert_term($data['name'], 'socialdb_property_type', array('parent' => get_property_type_id('socialdb_property_data'),
            'slug' => generate_slug($data['name'], 0)));
    else
        $new_property = wp_insert_term($data['name'], 'socialdb_property_type', array('parent' => $created_data_type_property[$data['hasFatherResource']]['creation_id'],
            'slug' => generate_slug($data['name'], 0)));

    if($data['functional'] == true)
    {
        update_term_meta($new_property['term_id'], 'socialdb_property_required', "true");
        update_term_meta($new_property['term_id'], 'socialdb_property_functional', "true");
    }

    update_term_meta($new_property['term_id'], 'socialdb_property_data_widget', (string) $range);
    update_term_meta($new_property['term_id'], 'socialdb_property_created_category',$data['id_domain_class']);
    add_term_meta($data['id_domain_class'],'socialdb_category_property_id',$new_property['term_id']);
    update_term_meta($new_property['term_id'], 'socialdb_property_collection_id', $collection_id);


    $created_data_type_property[$data['idAbout']]['creation_id'] = $new_property['term_id'];
    $created_data_type_property[$data['idAbout']]['created'] = true;

    return $new_property['term_id'];
}

function create_associative_table(&$tags, &$namespace)
{
    $table = [];
    foreach($tags as $tag) {
        $idAbout = $tag->attributes($namespace['rdf'])['about'];
        if ($idAbout == null)
            $idAbout = $tag->attributes($namespace['rdf'])['ID'];

        $table[$idAbout] = array('created' => false, 'creation_id' => null);
    }
    
    return $table;
}

/*
 * Trata Functional Property
 */
function treats_functional_property(&$functionalProperty_tags, &$created_dataType_property, &$created_objectProperty, &$class_tags, &$created_classes , &$namespace)
{
    $created_functional_property = [];//create_associative_table($functionalProperty_tags, $namespace);

    $count = count($functionalProperty_tags);
    for($i = 0; $i < $count; $i++)
    {
        create_functional_property($functionalProperty_tags, $created_functional_property, $created_dataType_property, $created_objectProperty, $functionalProperty_tags[$i], $class_tags, $created_classes, $namespace);
    }
}

function create_functional_property(&$functional_property_tags, &$created_functional_property, &$created_dataType_property, &$created_objectProperty, &$functional_property, &$class_tags, &$created_classes, &$namespace, &$collection_id)
{
    $null = null;
    if(($about = $functional_property->attributes($namespace['rdf'])['about']))
    {
        $data['idAbout'] = strval($about);
        $data['name'] = strval($functional_property->children($namespace['rdfs'])->label);
    }
    else//Caso o nome da propriedade seja guardada em ID e sirva tanto para nomea-la quanto para identifica-la
    {
        $data['idAbout'] = strval($functional_property->attributes($namespace['rdf'])['ID']);
        $data['name'] = strval($functional_property->attributes($namespace['rdf'])['ID']);
    }

    $data['hasFatherResource'] = $functional_property->children($namespace['rdfs'])->subPropertyOf->attributes($namespace['rdf'])['resource'];

    $data['functional'] = true;

    $type = end(explode("#", $functional_property->children($namespace['rdf'])->type->attributes($namespace['rdf'])['resource']));

    if($data['hasFatherResource'])
    {
        //Pai ainda não foi criado
        if($created_functional_property[($data['hasFatherResource'] = str_replace('#', '', $data['hasFatherResource']))]['created'] != true &&
            $created_functional_property[($data['hasFatherResource'] = str_replace('#', '', $data['hasFatherResource']))]['created'] != null
        )
        {
            $functional_property_to_create = class_search($data['hasFatherResource'], $functional_property_tags, $namespace);
            create_functional_property($functional_property_tags, $created_functional_property, $created_dataType_property, $created_objectProperty, $functional_property_to_create, $class_tags, $created_classes, $namespace, $collection_id);
            if(strcmp($type, "DatatypeProperty") == 0)
                $id = add_data_type_property($created_dataType_property, $functional_property, $class_tags, $created_classes, $data,
                    $namespace, false, $collection_id, $null, $null);
            else if(strcmp($type, "ObjectProperty") == 0)
                $id = add_object_property($created_objectProperty, $functional_property, $class_tags, $created_classes, $data, $namespace, false, $collection_id);

            $created_functional_property[$data['idAbout']]['creation_id'] = $id;
            $created_functional_property[$data['idAbout']]['created'] = true;
        }
        else //if($created_functional_property[$data['idAbout']]['created'] == true)//Criando filho de pai ja criado
        {
            if(strcmp($type, "DatatypeProperty") == 0)
                $id = add_data_type_property($created_dataType_property, $functional_property, $class_tags, $created_classes, $data,
                    $namespace, false, $collection_id, $null, $null);
            else if(strcmp($type, "ObjectProperty") == 0)
                $id = add_object_property($created_objectProperty, $functional_property, $class_tags, $created_classes, $data, $namespace, false, $collection_id);

            $created_functional_property[$data['idAbout']]['creation_id'] = $id;
            $created_functional_property[$data['idAbout']]['created'] = true;
        }
    }
    else if(!$functional_property->attributes($namespace['rdf'])['about'] && $created_functional_property[$data['idAbout']]['creation_id'] != true)
    {
        //Condição about é para que não crie um determinado tipo de functional property pois este já é tratado nas funções
        //Que tratam DataTypePropertys e ObjectPropertys
        if(strcmp($type, "DatatypeProperty") == 0)
            $id = add_data_type_property($created_dataType_property, $functional_property, $class_tags, $created_classes, $data,
                $namespace, true, $collection_id, $null, $null);
        else if(strcmp($type, "ObjectProperty") == 0)
            $id = add_object_property($created_objectProperty, $functional_property, $class_tags, $created_classes, $data, $namespace, true, $collection_id);

        $created_functional_property[$data['idAbout']]['creation_id'] = $id;
        $created_functional_property[$data['idAbout']]['created'] = true;
    }
}

/*
 * Trata restrições de classe
 */

function treats_class_restriction(&$class_tags, &$created_classes, &$created_object_property, &$created_data_type_property, &$namespace)
{
    foreach($class_tags as $class)
    {
        $current_class_id = $class->attributes($namespace['rdf'])['ID'];
        if(!$current_class_id)
            $current_class_id = $class->attributes($namespace['rdf'])['about'];

        $current_class_id = $created_classes[strval($current_class_id)]['creation_id'];

        //Trata restriction
        foreach($class->children($namespace['rdfs'])->subClassOf as $subclass)
        {
            if(($restriction = $subclass->children($namespace['owl'])->Restriction))
            {
                $on_property_class_name = $restriction->children($namespace['owl'])->onProperty->attributes($namespace['rdf'])['resource'];

                if(!$on_property_class_name)
                {
                    $on_property_class_name = $restriction->children($namespace['owl'])->onProperty->children($namespace['owl'])->ObjectProperty->attributes($namespace['rdf'])['about'];
                    if(!$on_property_class_name)
                    {
                        $on_property_class_name = $restriction->children($namespace['owl'])->onProperty->children($namespace['owl'])->FunctionalProperty->attributes($namespace['rdf'])['about'];
                        if(!$on_property_class_name)
                        {
                            $on_property_class_name = $on_property_class_name = $restriction->children($namespace['owl'])->onProperty->children($namespace['owl'])->DatatypeProperty->attributes($namespace['rdf'])['about'];
                            if($on_property_class_name)
                            {
                                $on_property_class_name = str_replace("#", '', $on_property_class_name);
                                $on_property_id = $created_data_type_property[strval($on_property_class_name)]['creation_id'];
                            }
                            else throw new Exception("Erro ao encontrar OnProperty!");
                        }
                        else
                        {
                            $on_property_class_name = str_replace("#", '', $on_property_class_name);
                            $on_property_id = $created_object_property[strval($on_property_class_name)]['creation_id'];
                            if($on_property_id == null)
                                $on_property_id = $created_data_type_property[strval($on_property_class_name)]['creation_id'];
                        }
                    }
                    else
                    {
                        $on_property_class_name = str_replace("#", '', $on_property_class_name);
                        $on_property_id = $created_object_property[strval($on_property_class_name)]['creation_id'];
                    }
                }
                else
                {
                    $on_property_class_name = str_replace("#", '', $on_property_class_name);
                    $on_property_id = $created_object_property[strval($on_property_class_name)]['creation_id'];
                    if($on_property_id == null)
                        $on_property_id = $created_data_type_property[strval($on_property_class_name)]['creation_id'];
                }

                //!Pega ID da classe indicada no OnProperty

                //All Values from
                if(($allvaluesfrom = $restriction->children($namespace['owl'])->allValuesFrom))
                {
                    $resource = $allvaluesfrom->attributes($namespace['rdf'])['resource'];
                    if(!$resource)
                    {
                        $resource = $allvaluesfrom->children($namespace['owl'])->attributes($namespace['rdf'])['about'];
                    }

                    $resource = str_replace("#", "", $resource);
                    add_term_meta($on_property_id, "socialdb_property_allvaluesfrom", $created_classes[strval($resource)]['creation_id']);
                } else
                //!All values from

                //Some values from
                if(($somevaluesfrom = $restriction->children($namespace['owl'])->someValuesFrom))
                {
                    $resource = $somevaluesfrom->attributes($namespace['rdf'])['resource'];
                    if(!$resource)
                    {
                        $resource = $somevaluesfrom->children($namespace['owl'])->attributes($namespace['rdf'])['about'];
                    }

                    $resource = str_replace("#", "", $resource);
                    add_term_meta($on_property_id, "socialdb_property_somevaluesfrom", $created_classes[strval($resource)]['creation_id']);
                }else
                //!Some values from


                //Has value
                if(($hasvalue = $restriction->children($namespace['owl'])->hasValue))
                {
                    $resource = $hasvalue->attributes($namespace['rdf'])['resource'];
                    if(!$resource)
                    {
                        $resource = $hasvalue->children($namespace['owl'])->attributes($namespace['rdf'])['about'];
                        if(!$resource)
                        {
                            $type = end(explode("#", $hasvalue->attributes($namespace['rdf'])['datatype']));
                            //Caso seja algum valor
                            //Ainda não criado no Tainacan
                        }
                        else
                        {
                            $resource = str_replace("#", "", $resource);
                            add_term_meta($on_property_id, "socialdb_property_hasvalue", $created_classes[strval($resource)]['creation_id']);
                        }
                    }
                }else
                //!Has value

                //cardinality
                if(($cardinality = $restriction->children($namespace['owl'])->cardinality))
                {
                    add_term_meta($on_property_id, "socialdb_property_cardinalidality", (int) $cardinality);
                }else
                //!cardinality

                //mincardinalidality
                if(($mincardinality = $restriction->children($namespace['owl'])->minCardinality))
                {
                    add_term_meta($on_property_id, "socialdb_property_mincardinalidality", (int) $mincardinality);
                }else
                //!mincardinality

                //Maxcardinality
                if(($maxcardinality = $restriction->children($namespace['owl'])->maxCardinality))
                {
                    add_term_meta($on_property_id, "socialdb_property_mincardinalidality", (int) $maxcardinality);
                }
                //!Maxcardinality
            }
        }
        //!Trata restriction

        //Trata DisjointWith
        foreach($class->children($namespace['owl'])->disjointWith as $disjointWith)
        {
            $disjoint_class_id = $disjointWith->attributes($namespace['rdf'])['resource'];
            $disjoint_class_id = str_replace("#", "", $disjoint_class_id);
            $disjoint_class_id = $created_classes[strval($disjoint_class_id)]['creation_id'];

            add_term_meta($current_class_id, "socialdb_category_disjointwith", $disjoint_class_id);
        }
        //!Trata DisjointWith

        //Trata ComplementOf
        if(($complementOfClass = $class->children($namespace['owl'])->complementOf->children($namespace['owl'])->Class))
        {
            $about = $complementOfClass->attributes($namespace['rdf'])['about'];
            $complementOfClassId = $created_classes[strval($about)]['creation_id'];
            add_term_meta($current_class_id, "socialdb_category_complementof", $complementOfClassId);
        }
        //!Trata ComplementOf
    }
}

/*
 * Trata transitive properties
 */

function treats_transitive_properties(&$transitive_properties, &$created_object_property, &$class_tags, &$created_classes, &$namespace)
{
    $created_transitive_properties = [];//create_associative_table($transitive_properties, $namespace);

    $count = count($transitive_properties);
    for($i = 0; $i < $count; $i++)
    {
        create_transitive_property($transitive_properties, $created_transitive_properties, $created_object_property, $class_tags, $created_classes, $transitive_properties[$i], $namespace);
    }
}

function create_transitive_property(&$transitive_properties_tags, &$created_transitive_properties, &$created_object_property, &$class_tags, &$created_classes, &$transitive_property, &$namespace, &$collection_id)
{
    if(($about = $transitive_property->attributes($namespace['rdf'])['about']))
    {
        $data['idAbout'] = strval($about);
        $data['name'] = strval($transitive_property->children($namespace['rdfs'])->label);
    }
    else//Caso o nome da propriedade seja guardada em ID e sirva tanto para nomea-la quanto para identifica-la
    {
        $data['idAbout'] = strval($transitive_property->attributes($namespace['rdf'])['ID']);
        $data['name'] = strval($transitive_property->attributes($namespace['rdf'])['ID']);
    }

    $data['functional'] = end(explode(";", $transitive_property->children($namespace['rdf'])->type->attributes($namespace['rdf'])['resource']));
    if(strcmp($data['functional'], "FunctionalProperty") == 0)
        $data['functional'] = true;
    else
    {
        $new_name = "#".$data['idAbout'];
        $new_functional = class_search($new_name, $functional_property_tags, $namespace);

        if(gettype($new_functional) == "object")
            $data['functional'] = true;
        else $data['functional'] = false;
    }

    $data['transitive'] = true;

    $data['hasFatherResource'] = $transitive_property->children($namespace['rdfs'])->subPropertyOf->attributes($namespace['rdf'])['resource'];

    if($data['hasFatherResource'])
    {
        if($created_transitive_properties[($data['hasFatherResource'] = str_replace('#', '', $data['hasFatherResource']))]['created'] != true)
        {
            $transitive_property_to_create = class_search($data['hasFatherResource'], $transitive_properties_tags, $namespace);
            create_transitive_property($transitive_properties_tags, $created_transitive_properties, $created_object_property, $class_tags, $created_classes,$transitive_property_to_create, $namespace, $collection_id);
            $id = add_object_property($created_object_property, $transitive_property, $class_tags, $created_classes, $data, $namespace, false, $collection_id);

            $created_transitive_properties[$data['idAbout']]['creation_id'] = $id;
            $created_transitive_properties[$data['idAbout']]['created'] = true;
        }
        else if($created_transitive_properties[$data['idAbout']]['created'] == true)
        {
            $id = add_object_property($created_object_property, $transitive_property, $class_tags, $created_classes, $data, $namespace, false, $collection_id);

            $created_transitive_properties[$data['idAbout']]['creation_id'] = $id;
            $created_transitive_properties[$data['idAbout']]['created'] = true;
        }
    }
    else if($created_transitive_properties[$data['idAbout']]['creation_id'] != true)
    {
        $id = add_object_property($created_object_property, $transitive_property, $class_tags, $created_classes, $data, $namespace, true, $collection_id);

        $created_transitive_properties[$data['idAbout']]['creation_id'] = $id;
        $created_transitive_properties[$data['idAbout']]['created'] = true;
    }
}

function treats_symmetric_properties(&$symmetric_properties, &$created_object_property, &$class_tags, &$created_classes, &$namespace, &$collection_id)
{
    foreach($symmetric_properties as $symmetric_property)
    {
        if(($about = $symmetric_property->attributes($namespace['rdf'])['about']))
        {
            $data['idAbout'] = strval($about);
            $data['name'] = strval($symmetric_property->children($namespace['rdfs'])->label);
        }
        else//Caso o nome da propriedade seja guardada em ID e sirva tanto para nomea-la quanto para identifica-la
        {
            $data['idAbout'] = strval($symmetric_property->attributes($namespace['rdf'])['ID']);
            $data['name'] = strval($symmetric_property->attributes($namespace['rdf'])['ID']);
        }

        $data['symmetric'] = true;

        add_object_property($created_object_property, $symmetric_property, $class_tags, $created_classes, $data, $namespace, true, $collection_id);
    }
}

/*
 * Trata individuos
 */
function treats_individuals(&$description_ind)
{
    foreach($description_ind as $description)
    {

    }
}

###############################################################################
#################### #20 Importador MAPAS CULTURAIS ###########################
add_action("add_tab_mapas_culturais", "add_tab_mapas_culturais_ontology");
function add_tab_mapas_culturais_ontology()
{
    ?>
    <li role="presentation"><a id="click_mapas_culturais_tab" href="#mapas_culturais_tab" aria-controls="mapas_culturais_tab" role="tab" data-toggle="tab"><?php _e('Mapas culturais','tainacan') ?></a></li>
    <?php
}

add_action("add_options_mapas_culturais", "add_options_mapas_culturais_ontology");
function add_options_mapas_culturais_ontology()
{
    ?>
    <div role="tabpanel" class="tab-pane" id="mapas_culturais_tab">
        <div id="validate_metatag_tab">
            <div id="list_oaipmh_dc">
                <table  class="table table-bordered">
                    <th><?php _e('Identifier','tainacan'); ?></th>
                    <th><?php _e('Edit/Remove','tainacan'); ?></th>
                    <tbody id="table_metatag_tab" >
                    </tbody>
                </table>
            </div>
            <br>

            <form id="mapa_cultural_form" class="form-group col-md-12 no-padding">
                <div class="col-md-12"><label><?php _e('URL','tainacan'); ?></label></div>
                <div class="col-md-10">
                    <input type="text"
                           id="url_mapa_cultural"
                           class="form-control"
                           placeholder="<?php _e('Insert the URL to extract metatags','tainacan'); ?>">
                </div>
                <div class="col-md-2">
                    <button type="button"
                            onclick="import_mapa_cultural($('#url_mapa_cultural').val().trim())"
                            id="submit_mapa_cultural_url"
                            class="btn btn-primary tainacan-blue-btn-bg pull-left">
                        <?php _e('Validate','tainacan'); ?>
                    </button>
                </div>
            </form>
            <div id="loader_validacao_metatags" style="display:none">
                <div class="text-center">
                    <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                    <h3><?php _e('Validating URL...','tainacan') ?></h3>
                </div>
            </div>
            <div style="display: none;" id="maping_container_metatags">
            </div>
        </div>
    </div>
    <?php
}

function mapa_cultural()
{
    $category_model = new CategoryModel();
    $collection_id = $_GET['collection_id'];
    //$type = $_GET['type'];

    $created_data_type_property = [];
    $result = $_POST['result'][0];

    $return['result'] = true;

    $null = null;
    $data['functional'] = false;

    /*if ($type == 'space')
        $name = "Espaços";
    elseif ($type == 'event')
        $name = "Eventos";
    elseif ($type == 'agent')
        $name = "Agentes";
    else
        $name = "Projetos";*/

    //$data['category_name'] = $name;

    $data['dont_create'] = true;
    $data['category_name'] = "Espaços";
    $created_classes['spaces'] = record_class($null, $name, $category_model, $data, $collection_id, true);

    $data['category_name'] = "Agentes";
    $created_classes['agents'] = record_class($null, $name, $category_model, $data, $collection_id, true);

    $data['category_name'] = "Projetos";
    $created_classes['projects'] = record_class($null, $name, $category_model, $data, $collection_id, true);

    $data['category_name'] = "Eventos";
    $created_classes['events'] = record_class($null, $name, $category_model, $data, $collection_id, true);

    try
    {
        treats_object_properties_unknown($result, $created_data_type_property, $data, $collection_id, $created_classes);
    }
    catch(Exception $e)
    {
        $return['result'] = false;
    }

    $return['url'] = get_the_permalink($collection_id);

    return $return;
}

function treats_object_properties_unknown(&$result, &$created_data_type_properties, &$data, &$collection_id, &$created_classes)
{
    foreach ($result as $index_class => $class_itens) {
        foreach ($class_itens as $attributes_list) {
            foreach ($attributes_list as $datatype_name => $value) {
                if($datatype_name =  'location')
                {
                    $created_data_type_properties['latitude']['associated_classes'] = [];
                    $created_data_type_properties['longitude']['associated_classes'] = [];
                }else
                    $created_data_type_properties[$datatype_name]['associated_classes'] = [];
            }
        }
    }

    foreach ($result as $index_class => $class_itens)
    {
        foreach ($class_itens as $attributes_list){
            //Criação de todas a propriedades relacionada a um determinado individuo
            foreach ($attributes_list as $datatype_name => $value)
            {
                if($datatype_name != 'location')
                {
                    create_property($created_data_type_properties, $datatype_name, $created_classes, $index_class, $collection_id);
                }
                else if($datatype_name == 'location')
                {
                    $datatype_name = 'latitude';
                    create_property($created_data_type_properties, $datatype_name, $created_classes, $index_class, $collection_id);

                    $datatype_name = 'longitude';
                    create_property($created_data_type_properties, $datatype_name, $created_classes, $index_class, $collection_id);

                    update_post_meta($collection_id, 'socialdb_collection_list_mode', 'geolocation');
                    update_post_meta($collection_id, 'socialdb_collection_latitude_meta', $created_data_type_properties['latitude']['creation_id']);
                    update_post_meta($collection_id, 'socialdb_collection_longitude_meta', $created_data_type_properties['longitude']['creation_id']);
                }
            }

            //Criação do individuo
            $data['object_name'] = end(explode("#", $attributes_list['name']));
            $data['class_id'] = $created_classes[$index_class];
            add_individual($data, $collection_id, $attributes_list);
        }
    }
}
function create_property(&$created_data_type_properties, &$datatype_name, &$created_classes, &$index_class, &$collection_id)
{
    $null = null;
    if($created_data_type_properties[$datatype_name]['created'] != null)
    {
        if(!in_array($created_classes[$index_class], $created_data_type_properties[$datatype_name]['associated_classes']))
        {
            add_term_meta($created_classes[$index_class],'socialdb_category_property_id',$created_data_type_properties[$datatype_name]['creation_id']);
            array_push($created_data_type_properties[$datatype_name]['associated_classes'], $created_classes[$index_class]);
        }
    }
    else if($datatype_name != 'name')
    {
        //Cria data type property
        $data_type_property = array('data_type' => 'string', 'id_domain_class' => $created_classes[$index_class]);
        $data['name'] = $datatype_name;
        $created_data_type_properties[$datatype_name]['creation_id'] =
            add_data_type_property($null, $data_type_property, $null, $null, $data, $null, true, $collection_id, $null, $null);

        $created_data_type_properties[$datatype_name]['created'] = true;

        array_push($created_data_type_properties[$datatype_name]['associated_classes'], $created_classes[$index_class]);
    }
}

function add_individual(&$data, &$collection_id, &$properties_list)
{
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

    //Propriedades relacionadas a aquele individuo
    $properties = get_term_meta($data['class_id'],'socialdb_category_property_id');
    $properties = array_unique($properties);
    if($properties && is_array($properties)){

        foreach ($properties as $property){
            $property_name = get_term_by('id',$property,'socialdb_property_type')->name;

            if($property_name != "name")
            {
                if($property_name == 'latitude')
                {
                    $value = $properties_list['location'][$property_name];
                    add_post_meta($data['ID'], 'socialdb_property_' . $property, $value);
                }
                else if($property_name == 'longitude')
                {
                    $value = $properties_list['location'][$property_name];
                    add_post_meta( $data['ID'],'socialdb_property_'.$property, $value);
                }
                else
                {
                    $value = $properties_list[$property_name];
                    add_post_meta( $data['ID'],'socialdb_property_'.$property, $value);
                }

            }
        }
    }
}

function treats_individual(&$individuals_tags, &$namespace, &$collection_id, &$created_classes)
{
    $null = null;
    foreach($individuals_tags as $individual)
    {
        $data['object_name'] = strval($individual->attributes($namespace['rdf'])['about']);
        $resource = strval($individual->children($namespace['rdf'])->type->attributes($namespace['rdf'])['resource']);
        $data['class_id'] = $created_classes[$resource]['creation_id'];

        add_individual($data, $collection_id, $null);
    }
}

function prepare_elements(&$owl_declarations,
                          &$ret_class_tags, &$ret_named_individuals_tags, &$ret_data_properties_tags,
                          &$ret_object_properties,
                          &$owl_subclassof, &$ret_subclass_structure,
                          &$data_properties_domain_tags, &$ret_data_properties_domain_tags,
                          &$data_properties_range_tags, &$ret_data_properties_range_tags,
                          &$subdata_property_of, &$ret_subdata_property_of,
                          &$functional_property_tags, &$ret_functional_property_tags
                        )
{
    if(count($owl_declarations))
    {
        for($i = 0, $class_pos = 0, $named_individuals_pos = 0, $data_properties_pos = 0, $object_properties_pos = 0;
            $i < count($owl_declarations); $i++)
        {
            if($owl_declarations[$i]->Class)
            {
                $ret_class_tags[$class_pos] = $owl_declarations[$i]->Class;
                $ret_class_tags[$class_pos]->attributes()['IRI'] = str_replace("#", "", $ret_class_tags[$class_pos]->attributes()['IRI']);
                $class_pos++;
            }
            else if($owl_declarations[$i]->NamedIndividual)
            {
                $ret_named_individuals_tags[$named_individuals_pos] = $owl_declarations[$i]->NamedIndividual;
                $named_individuals_pos++;
            }
            else if($owl_declarations[$i]->DataProperty)
            {
                $ret_data_properties_tags[$data_properties_pos] = $owl_declarations[$i]->DataProperty;

                $ret_data_properties_tags[$data_properties_pos]->attributes()['IRI'] =
                    str_replace("#", "", $ret_data_properties_tags[$data_properties_pos]->attributes()['IRI']);

                $data_properties_pos++;
            }
            else if($owl_declarations[$i]->ObjectProperty)
            {

                $ret_object_properties[$object_properties_pos] = $owl_declarations[$i]->ObjectProperty;

                $ret_object_properties[$object_properties_pos]->attributes()['IRI'] =
                    str_replace("#", "", $ret_object_properties[$data_properties_pos]->attributes()['IRI']);

                $object_properties_pos++;
            }
        }


        for($i = 0; $i < count($owl_subclassof); $i++)
        {
            $index = str_replace("#", "", $owl_subclassof[$i]->children()->Class[0]->attributes()['IRI']);
            $father_class = str_replace("#", "", $owl_subclassof[$i]->children()->Class[1]->attributes()['IRI']);

            $ret_subclass_structure[$index] = $father_class;
        }

        for($i = 0; $i < count($data_properties_domain_tags); $i++)
        {
            $index = str_replace("#", "", $data_properties_domain_tags[$i]->children()->DataProperty->attributes()['IRI']);
            $father_class =str_replace("#", "", $data_properties_domain_tags[$i]->children()->Class->attributes()['IRI']);

            $ret_data_properties_domain_tags[$index] = $father_class;

        }

        for($i = 0; $i < count($data_properties_range_tags); $i++)
        {
            $index = str_replace("#", "", $data_properties_range_tags[$i]->children()->DataProperty->attributes()['IRI']);
            $data_type =str_replace("xsd:", "", $data_properties_range_tags[$i]->children()->Datatype->attributes()['abbreviatedIRI']);

            $ret_data_properties_range_tags[$index] = $data_type;
        }

        for($i = 0; $i < count($subdata_property_of); $i++)
        {
            $index = str_replace("#", "", $subdata_property_of[$i]->children()->DataProperty[0]->attributes()['IRI']);
            $father_class = str_replace("#", "", $subdata_property_of[$i]->children()->DataProperty[1]->attributes()['IRI']);
            $ret_subdata_property_of[$index] = $father_class;
        }

        for($i = 0; $i < count($functional_property_tags); $i++)
        {
            $index = str_replace("#", "", $functional_property_tags[$i]->children()->DataProperty->attributes()['IRI']);
            $ret_functional_property_tags[$index] = true;
        }

        return true;
    }
}