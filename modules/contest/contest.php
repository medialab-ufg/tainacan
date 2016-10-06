<?php

/* 
 * Modo de debates do Tainacan
 * 
 * #1 - ADICIONANDO OS SCRIPTS DESTE MODULO 
 * #2 - BOTAO DE ADICAO DE ARGUMENTO
 * #3 - ALTERACOES CRIACAO DA COLECAO
 * #4 -  REMOCAO DE CAMPOS DESNECESSÁRIOS 
 * 
 * @author: EDUARDO HUMBERTO
 */

####################### #1 - ADICIONANDO OS SCRIPTS DESTE MODULO#######################
define('MODULE_CONTEST', 'contest');
define('CONTEST_CONTROLLERS', get_template_directory_uri() . '/modules/' . MODULE_CONTEST );
load_theme_textdomain("tainacan", dirname(__FILE__) . "/languages");
add_action('wp_enqueue_scripts', 'tainacan_contest_js');
function tainacan_contest_js() {
    wp_register_script('contest', 
            get_template_directory_uri() . '/modules/' . MODULE_CONTEST . '/libraries/js/contest.js', array('jquery'), '1.11');
    $js_files = ['contest'];
    foreach ($js_files as $js_file):
        wp_enqueue_script($js_file);
    endforeach;
}

add_action('wp_enqueue_scripts', 'tainacan_contest_css');
function tainacan_contest_css() {
    $registered_css = [
          'item-css' => '/libraries/css/item.css'
      ];
    foreach ($registered_css as $css_file => $css_path) {
         wp_register_style($css_file, get_template_directory_uri() . '/modules/' . MODULE_CONTEST  . $css_path);
         wp_enqueue_style($css_file);
    }
}
################################################################################

######################### #2 BOTAO DE ADICAO DE ARGUMENTO ###########################
/**
 * Filtro que mostra o botao personalizado de adicao de individuo
 */
function alter_button_add_item_contest($string) {
    $string .= '
        <div class="btn-group" role="group" aria-label="...">
            <div class="btn-group tainacan-add-wrapper">
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  '.__('Add','tainacan').'&nbsp;<span class="caret"></span> 
                  </button>
                    <ul class="dropdown-menu">
                        <li><a  onclick="contest_show_modal_create_argument()" style="cursor: pointer;">'. __('Item','tainacan').'</a></li>
                        <li><a onclick="contest_show_modal_create_question()" style="cursor: pointer;" >'. __('Question with multiple answers','tainacan').'</a></li>
                   </ul>
            </div>
        </div>';
    $string .= 
    '<div class="modal fade" id="modalCreateArgument" tabindex="-1" role="dialog" aria-labelledby="modalCreateArgument" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form onsubmit="show_modal_main();" action="'.CONTEST_CONTROLLERS.'/controllers/argument/contest_argument_controller.php" method="POST">
                    <input type="hidden" name="operation" value="simple_add">
                    <div class="modal-header">
                        <button type="button" style="color:black;" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">'.__('New Argument', 'tainacan').'</h4>
                    </div>
                    <div class="modal-body"  >
                        <div class="form-group">
                           <label for="exampleInputEmail1">'.__('Describe a conclusion or an afirmation below','tainacan').'</label>
                           <textarea name="conclusion" class="form-control" required="" placeholder="'.__('This field is obligate!','tainacan').'" ></textarea>
                        </div>
                        <div style="margin-left:25px;" class="form-group">
                          <label for="exampleInputPassword1"><span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;'.__('Describe a positive argument (Optional) ','tainacan').'</label>
                           <textarea name="positive_argument" class="form-control"  placeholder="'.__('This field is optional!','tainacan').'" ></textarea>
                        </div>
                        <div style="margin-left:25px;" class="form-group">
                          <label for="exampleInputFile"><span class="glyphicon glyphicon-thumbs-down"></span>&nbsp;'.__('Describe a negative argument (Optional) ','tainacan').'</label>
                           <textarea name="negative_argument" class="form-control" placeholder="'.__('This field is optional!','tainacan').'" ></textarea>
                        </div>
                        <input type="hidden" name="collection_id" value="'.get_the_ID().'">
                        <input type="hidden" name="classifications" value="">
                        <input type="hidden" name="operation" value="add">
                    </div>
                    <div class="modal-footer">
                        <button style="color:grey;" type="button" class="btn btn-default" data-dismiss="modal">'. __('Close', 'tainacan').'</button>
                        <button type="submit" class="btn btn-primary" >'. __('Save', 'tainacan').'</button>
                    </div>
                </form>
            </div>
        </div>
    </div>';
     $string .= 
    '<div class="modal fade" id="modalCreateQuestion" tabindex="-1" role="dialog" aria-labelledby="modalCreateArgument" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form onsubmit="show_modal_main();" action="'.CONTEST_CONTROLLERS.'/controllers/question/contest_question_controller.php" method="POST">
                    <input type="hidden" name="operation" value="simple_add">
                    <div class="modal-header">
                        <button type="button" style="color:black;" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">'.__('New Question', 'tainacan').'</h4>
                    </div>
                    <div class="modal-body"  >
                        <div class="form-group">
                           <label for="exampleInputEmail1">'.__('Insert a question below').'</label>
                           <textarea name="question" class="form-control" required="" placeholder="'.__('This field is obligate!','tainacan').'" ></textarea>
                        </div>
                        <div style="margin-left:25px;" class="form-group">
                          <label for="exampleInputPassword1"></span>&nbsp;'.__('Describe an answer (or conclusion) for the question (Optional) ','tainacan').'</label>
                           <textarea name="answer" class="form-control"  placeholder="'.__('This field is optional!','tainacan').'" ></textarea>
                        </div>
                        <input type="hidden" name="collection_id" value="'.get_the_ID().'">
                        <input type="hidden" name="classifications" value="">
                        <input type="hidden" name="operation" value="add">
                    </div>
                    <div class="modal-footer">
                        <button style="color:grey;" type="button" class="btn btn-default" data-dismiss="modal">'. __('Close', 'tainacan').'</button>
                        <button type="submit" class="btn btn-primary" >'. __('Save', 'tainacan').'</button>
                    </div>
                </form>
            </div>
        </div>
    </div>';
    return $string;
}
add_filter( 'show_custom_add_item_button', 'alter_button_add_item_contest', 10, 3 );

################################################################################

######################### #3 ALTERACOES CRIACAO DA COLECAO ########################
/**
 * Filtro que retorna o nome a ser usado pela categoria raiz da colecao
 */
function alter_collection_object($name) {
    return __('Argument Type','tainacan');
}
add_filter( 'collection_object', 'alter_collection_object', 10, 3 );
/*
 * Adicionando os metadados default diretamente na categoria raiz 
 */
add_action( 'insert_default_properties_collection', 'contest_insert_default_properties_collection', 10, 2 );
function contest_insert_default_properties_collection($category_id,$collection_id) {
        $new_property = wp_insert_term(__('In favor / Against', 'tainacan'), 'socialdb_property_type', array('parent' => get_term_by('name', 'socialdb_property_ranking_binary', 'socialdb_property_type')->term_id,
            'slug' => "contest_in_favor_against_property". mktime()));
        update_term_meta($new_property['term_id'], 'socialdb_property_created_category', $category_id); // adiciono a categoria de onde partiu esta propriedade
        add_term_meta($category_id, 'socialdb_category_property_id', $new_property['term_id']);
        //Related
        $new_property = wp_insert_term(__('Related', 'tainacan'), 'socialdb_property_type', array('parent' => get_term_by('name', 'socialdb_property_object', 'socialdb_property_type')->term_id,
            'slug' => "contest_related_property". mktime()));
        update_term_meta($new_property['term_id'], 'socialdb_property_object_category_id', $category_id);
        update_term_meta($new_property['term_id'], 'socialdb_property_created_category', $category_id); // adiciono a categoria de onde partiu esta propriedade
        add_term_meta($category_id, 'socialdb_category_property_id', $new_property['term_id']);
        add_post_meta($collection_id, 'socialdb_collection_facets', $new_property['term_id']);
        add_post_meta($collection_id, 'socialdb_collection_facet_' . $new_property['term_id'] . '_color', 'color_property8');
        add_post_meta($collection_id, 'socialdb_collection_facet_' . $new_property['term_id'] . '_priority', 999);
        add_post_meta($collection_id, 'socialdb_collection_facet_' . $new_property['term_id'] . '_widget', 'tree');$parent_category_id = get_register_id('socialdb_category', 'socialdb_category_type');
        /* Criando a categoria raiz e adicionando seus metas */
        $facet_id = create_register(__('Subject','tainacan'), 'socialdb_category_type', array('parent' => $parent_category_id, 'slug' => "subject_" . mktime()));
         add_post_meta($collection_id, 'socialdb_collection_facets', $facet_id['term_id']);
         add_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id['term_id'] . '_color', 'color1');
         add_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id['term_id'] . '_widget', 'tree');
         add_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id['term_id'] . '_priority', 2);
}
################################################################################

##################### 11# REMOCAO DE CAMPOS DESNECESSÁRIOS #########################
/** ESCONDER CAMPOS DO FORMULARIO DE ADICAO E EDICAO DE PROPRIEDADE DE OBJETOS*/
add_action('collection_create_name_object', 'hide_field');
/******************************************************************************/
function hide_field() {
    echo 'style="display:none;"';                          
}

##################### 12# MOSTRA PAGINA DO ITEM DESTE MODO #########################
add_filter( 'alter_page_item', 'contest_alter_page_item', 10, 1 );
function contest_alter_page_item($data) {
    $html = '<script type="text/javascript">
            init_contest_item_page("'. CONTEST_CONTROLLERS.'",'.$data['collection_id'].','.$data['object']->ID.');</script>'.$html;
    return $html;
}
################################################################################
##################### 13# ADICIONANDO O TIPO DE DENUNCIA #######################
function contest_add_meta_delete_object_event(){
    $term = get_term_by('slug', 'socialdb_event_object_delete','socialdb_event_type');
    create_metas($term->term_id, 'socialdb_event_object_delete_metas', 'socialdb_event_object_delete_type', 'socialdb_event_object_delete_type');
}
contest_add_meta_delete_object_event();