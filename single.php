<?php
/*
 * Template Name: Index
 * Description: Paginca inicial da colecao
 */
require_once(dirname(__FILE__) . '/models/social_network/Facebook/autoload.php');
require_once(dirname(__FILE__) . '/controllers/helpers/helpers_controller.php');
///****************************** EXECUTANDO SCRIPTS  AVULSOS*********************/
if (isset($_GET['execute-script'])):
    error_reporting(E_ALL);
    if ($_GET['execute-script'] == '0002') {
        HelpersController::execute_script('0002', [ 'collection_id' => 'all']);
    } else if ($_GET['execute-script'] == '0001') {
        HelpersController::execute_script('0001', [ 'collection_id' => get_the_ID()]);
    }

    wp_redirect(get_the_permalink());
endif;
/* * **************************************************************************** */
session_start();
get_header();
global $config;
session_start();
$options = get_option('socialdb_theme_options');
?>

<?php while (have_posts()) : the_post(); ?>
    <!-- TAINACAN: div necessaria para procedimentos do facebook  -->
    <div id="fb-root"></div>
    <!-- TAINACAN: esta div (AJAX) mostra o painel da colecao e suas acoes, estilos inline para descer a div apenas pois estava sob o header  -->
    <div class="panel panel-default collection_header" id="collection_post" style="margin-top: -20px;margin-bottom: 0px;">
    </div>
    <!-- TAINACAN - BEGIN: ITENS NECESSARIOS PARA EXECUCAO DE VARIAS PARTES DO SOCIALDB -->
    <input type="hidden" id="socialdb_fb_api_id" name="socialdb_fb_api_id" value="<?php echo $options['socialdb_fb_api_id']; ?>">
    <input type="hidden" id="socialdb_embed_api_id" name="socialdb_embed_api_id" value="<?php echo $options['socialdb_embed_api_id']; ?>">
    <input type="hidden" id="current_user_id" name="current_user_id" value="<?php echo get_current_user_id(); ?>">
    <input type="hidden" id="src" name="src" value="<?php echo get_template_directory_uri() ?>">
    <input type="hidden" id="collection_id" name="collection_id" value="<?php echo get_the_ID() ?>">
    <input type="hidden" id="mode" name="mode" value="<?php echo $mode ?>">
    <input type="hidden" id="socialdb_permalink_collection" name="socialdb_permalink_collection" value="<?php echo get_the_permalink(get_the_ID()); ?>" />
    <input type="hidden" id="search_collection_field" name="search_collection_field" value="<?php
    if ($_GET['search']) {
        echo $_GET['search'];
    }
    ?>">
    <!-- Hidden para verificar se existe filtros via url -->
    <input type="hidden" id="is_filter" name="is_filter" value="<?php
    if (isset($_GET['is_filter'])) {
        echo $_GET['is_filter'];
    }
    ?>">
    <!-- Hidden para recuperacao de senha -->
    <input type="hidden" id="recovery_password" name="recovery_password" value="<?php
    if ($_GET['recovery_password']) {
        echo (int) base64_decode($_GET['recovery_password']);
    }
    ?>">
    <input type="hidden" id="mycollections" name="mycollections" value="<?php
    if (isset($_GET['mycollections'])) {
        echo 'true';
    }
    ?>">
    <!-- PAGINA DO ITEM -->
    <input type="hidden" id="object_page" name="object_page" value="<?php
    if (isset($_GET['item'])) {
        echo trim($_GET['item']);
    }
    ?>">
    <!-- PAGINA DA CATEGORIA -->
    <input type="hidden" id="category_page" name="category_page" value="<?php
    if (isset($_GET['category'])) {
        echo trim($_GET['category']);
    }
    ?>">
    <!-- PAGINA DA PROPRIEDADE -->
    <input type="hidden" id="property_page" name="property_page" value="<?php
    if (isset($_GET['property'])) {
        echo trim($_GET['property']);
    }
    ?>">
    <!-- PAGINA DA TAG -->
    <input type="hidden" id="tag_page" name="tag_page" value="<?php
    if (isset($_GET['tag'])) {
        echo trim($_GET['tag']);
    }
    ?>">
    <!-- PAGINA DA TAXONOMIA -->
    <input type="hidden" id="tax_page" name="object_page" value="<?php
    if (isset($_GET['tax'])) {
        echo trim($_GET['tax']);
    }
    ?>">
    <input type="hidden" id="info_messages" name="info_messages" value="<?php
    if (isset($_GET['info_messages'])) {
        echo $_GET['info_messages'];
    }
    ?>">
    <input type="hidden" id="info_title" name="info_title" value="<?php
    if (isset($_GET['info_title'])) {
        echo $_GET['info_title'];
    }
    ?>">
    <input type="hidden" id="open_wizard" name="open_wizard" value="<?php
    if (isset($_GET['open_wizard'])) {
        echo $_GET['open_wizard'];
    }
    ?>">
    <input type="hidden" id="open_login" name="open_login" value="<?php
    if (isset($_GET['open_login'])) {
        echo $_GET['open_login'];
    }
    ?>">
    <input type="hidden" id="instagramInsertedIds" name="instagramInsertedIds" value="<?php
    if (isset($_SESSION['instagramInsertedIds'])) {
        if ($_SESSION['instagramInsertedIds'] != 'instagram_error') {
            echo $_SESSION['instagramInsertedIds'];
        } else {
            echo 'instagram_error';
        }
        unset($_SESSION['instagramInsertedIds']);
    } else {
        echo 'false';
    }
    ?>">
    <input type="hidden" id="facebookInsertedIds" name="facebookInsertedIds" value="<?php
    if (isset($_SESSION['facebookInsertedIds'])) {
        if ($_SESSION['facebookInsertedIds'] != 'facebook_error') {
            echo $_SESSION['facebookInsertedIds'];
        } else {
            echo 'facebook_error';
        }
        unset($_SESSION['facebookInsertedIds']);
    } else {
        echo 'false';
    }
    ?>">
    <input type="hidden" id="wp_query_args" name="wp_query_args" value=""> <!-- utilizado na busca -->
    <input type="hidden" id="change_collection_images" name="change_collection_images" value="">
    <input type="hidden" id="value_search" name="value_search" value=""> <!-- utilizado na busca -->
    <input type="hidden" id="flag_dynatree_ajax" name="flag_dynatree_ajax" value="true"> <!-- utilizado na busca -->
    <!-- TAINACAN - END: ITENS NECESSARIOS PARA EXECUCAO DE VARIAS PARTES DO SOCIALDB -->

    <!-- TAINACAN: esta div central que agrupa todos os locais para widgets e a listagem de objeto -->
    <div id="main_part">
        <!-- TAINACAN: este container agrupa a coluna da esquerda dos widgets, a listagem de itens e coluna da direita dos widgets -->
        <div id="container_three_columns" class="container-fluid">

            <div class="row">

                <!-- TAINACAN: esta div (AJAX) mostra os widgets para pesquisa que estao setadas na esquerda  -->
                <div  id="div_left" class="col-md-3" style="height: 1300px;min-height: 500px;overflow-y:  auto;"></div>

                <!-- TAINACAN: esta div agrupa a listagem de itens ,submissao de novos itens e ordencao -->
                <div  id="div_central" class="col-md-9">

                    <!-- TAINACAN: esta div agrupa a submissao de novos itens e a ordenacao (estilo inline usado para afastar do painel da colecao) -->
                    <div id="menu_object" class="row col-md-12">
                        <div class="col-lg-12 no-padding">

                            <div class="col-md-12 no-padding">
                                <div class="row search-top-container">
                                    <div class="col-md-10 box-left">
                                        <div class="search-colecao">
                                            <div class="input-group">
                                                <input style="font-size: 13px;" class="form-control input-medium placeholder ui-autocomplete-input" id="search_objects"
                                                       onkeyup="set_value(this)" onkeydown="if (event.keyCode === 13)
                                                                       document.getElementById('search_main').click();"
                                                       type="text" placeholder="<?php _e('Find', 'tainacan') ?>" autocomplete="off">
                                                <span class="input-group-btn">
                                                    <button id="search_main" type="button" onclick="search_objects('#search_objects')" class="btn btn-default">
                                                        <span class="glyphicon glyphicon-search"></span>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 box-right">
                                        <button class="btn btn-default" onclick="showAdvancedSearch('<?php echo get_template_directory_uri() ?>');">
                                            <?php _e('Advanced Search', 'tainacan'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 no-padding">
                                <div class="col-md-10 no-padding">
                                    <div id="filters_collection"></div>
                                </div>
                                <div class="col-md-2 no-padding">
                                    <div class="text-left clear-top-search">
                                        <button onclick="clear_list()" id="clear" class="prime-color-bg"><?php _e('Clear search', 'tainacan') ?></button>
                                    </div>
                                </div>

                            </div>

                        </div>


                        <!-- TAINACAN: esta div estao localizados o campo para o titulo e botao com o icone para o adicionar rapido, colado ao input - col-md-6 (bootstrap) -->
                        <!--div class="col-md-6">
                            <div class="input-group">
                                <input onkeydown="if (event.keyCode === 13)
                                            document.getElementById('click_fast_insert').click()" type="text" placeholder="<?php _e('Type the title or the URI to you object!', 'tainacan'); ?>" id="fast_insert_object" class="form-control input-medium placeholder" style="font-size: 13px; "></textarea>
                                <span class="input-group-btn">
                                    <button class="btn btn-default" id="click_fast_insert" onclick="fast_insert()" type="button"><span class="glyphicon glyphicon-plus"></span></button>
                                </span>
                            </div><!-- /input-group -->
                        <!--/div-->
                        <!-- TAINACAN: esta div estao o botao que abre o formulario completo para submissao de itens, botao para ordenacao asc e desc, e o selectbox para selecionar a ordenacao  - col-md-6 (bootstrap) -->
                        <div class="col-md-12 header-colecao">
                            <div class="row">
                                <?php if (get_option('collection_root_id') != get_the_ID() && (is_user_logged_in() && verify_allowed_action(get_the_ID(), 'socialdb_collection_permission_create_object'))): ?>
                                    <div class="col-md-2 tainacan-add-item">
                                        <?php if (apply_filters('show_custom_add_item_button', '')): ?>
                                            <?php echo apply_filters('show_custom_add_item_button', ''); ?>
                                        <?php elseif (has_nav_menu('menu-ibram')): ?>
                                            <button type="button" class="btn btn-primary" onclick="showAddItemText()">
                                                <?php _e('Add', 'tainacan') ?> <span class="glyphicon glyphicon-plus"></span>
                                            </button>
                                        <?php else: ?>
                                            <div class="btn-group" role="group" aria-label="...">
                                                <div class="btn-group tainacan-add-wrapper">
                                                    <button type="button" class="btn btn-primary dropdown-toggle sec-color-bg" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <?php _e('Add', 'tainacan') ?> <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <!--li><a  id="create_button" style="cursor: pointer;"><?php echo (get_post_meta(get_the_ID(), 'socialdb_collection_object_name', true)) ? get_post_meta(get_the_ID(), 'socialdb_collection_object_name', true) : _e('Item', 'tainacan') //_e('Item', 'tainacan')                    ?></a></li>
                                                        <li><a onclick="showViewMultipleItems()" style="cursor: pointer;" ><?php _e('Multiple Files', 'tainacan') ?></a></li>
                                                        <!--li><a style="cursor: pointer;" onclick="showModalImportSocialNetwork();" ><?php _e('Social Media', 'tainacan') ?></a></li>
                                                        <li><a style="cursor: pointer;" onclick="showModalImportAll();" ><?php _e('Web Resource URL', 'tainacan') ?></a></li>
                                                        <li class="divider" -->
                                                        <li><a onclick="showAddItemText()"  style="cursor: pointer;"><?php _e('Write text', 'tainacan') ?> </a></li>
                                                        <li><a onclick="showViewMultipleItems()" style="cursor: pointer;" ><?php _e('Send file(s)', 'tainacan') ?>  </a></li>
                                                        <li><a onclick="showAddItemURL();" style="cursor: pointer;" ><?php _e('Insert URL', 'tainacan') ?>  </a></li>    
                                                    </ul>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="col-md-4 flex-box">
                                    <select onchange="getOrder(this)" class="form-control white"
                                            name="collection_single_ordenation" id="collection_single_ordenation">
                                        <option value=""><?php _e('Sorted by', 'tainacan') ?></option>
                                    </select>
                                    &nbsp;
                                    <button onclick="change_ordenation('asc')" type="button" id="sort_list" class="btn btn-default pull-right"><span class="glyphicon glyphicon-sort-by-attributes"></span></button>
                                    <button onclick="change_ordenation('desc')" type="button" id="sort_list" class="btn btn-default pull-right"><span class="glyphicon glyphicon-sort-by-attributes-alt"></span></button>
                                </div>

                                <div class="col-md-3 viewMode-control">
                                    <div class="sec-color"> <?php _e('Show:', 'tainacan') ?> </div>
                                    <ul>
                                        <?php
                                        $viewModes = [ 'cards', 'gallery', 'list', 'slideshow'];
                                        foreach ($viewModes as $mode):
                                            ?>
                                            <li class="<?php echo $mode ?>">
                                                <a href="javascript:void(0)" onclick="changeViewMode('<?php echo $mode ?>')">
                                                    <img alt="<?php echo ucfirst(__($mode, 'tainacan')); ?>"
                                                         src="<?php echo get_template_directory_uri() . '/libraries/images/icons/collection/icon-' . $mode . '.png' ?>" />
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="col-md-2">
                                    <?php
                                    if (is_user_logged_in()) {
                                        if (get_the_ID() != get_option('collection_root_id') && verify_collection_moderators(get_the_ID(), get_current_user_id())) {
                                            ?>
                                            <button onclick="showTrash('<?php echo get_template_directory_uri(); ?>');" class="btn btn-default"><?php _e('Trash', 'tainacan'); ?></button>
                                            
                                            <?php
                                        } else {
                                            $admin_email = get_option('admin_email');
                                            $user_data = get_user_by('ID', get_current_user_id())->user_email;
                                            if ($admin_email == $user_data) {
                                                ?>
                                                <button onclick="showTrash('<?php echo get_template_directory_uri(); ?>');" class="btn btn-default"><?php _e('Trash', 'tainacan'); ?></button>
                                                
                                                <?php
                                            }
                                        }
                                        ?>
                                        <button style="display: none;" id="hideTrash" onclick="showList('<?php echo get_template_directory_uri(); ?>');" class="btn btn-default"><?php _e('Exit', 'tainacan'); ?></button>
                                        <?php
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>

                    </div>

                    <!--div id="remove"> view removida </div> -->
                    <!-- TAINACAN: esta div (AJAX)recebe o formulario para criacao e edicao de itens  -->
                    <div id="form" >
                    </div>

                    <!-- TAINACAN: esta div apenas 'envelopa' a que recebe a listagem nenhum estilo e associado  -->
                    <div id="container_socialdb" class="row col-md-12">
                        <!-- TAINACAN: esta div (AJAX)recebe a listagem de itens  -->
                        <ul id="list" class="col-md-12 row">
                        </ul>
                    </div>
                    <!-- TAINACAN: div que esta o gif que eh mostrada ao filtrar itens e outras acoes que necessitam e carregamento -->
                    <div id="loader_objects" style="display:none"><center><img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>"><h3><?php _e('Loading objects...', 'tainacan') ?></h3></center></div>
                    <br>
                    <!--a id="home_button" href="#" class="btn btn-default"><span class="glyphicon glyphicon-th-list"></span></a-->

                </div>

                <!-- TAINACAN: esta div (AJAX) mostra os widgets para pesquisa que estao setadas na direita  -->
                <div id="div_right"></div>

            </div>
        </div>
    </div>
    <!-- Fim do conteudo principal da pagina (div main part) -->
    <!-- TAINACAN: esta div eh mostrada quando eh clicado com o botao direito sobre categorias e tags no dynatree  -->
    <?php do_action('insert_new_contextmenu_dynatree') ?>

    <ul id="myMenuSingle" class="contextMenu" style="display:none;">
        <?php if (verify_allowed_action(get_the_ID(), 'socialdb_collection_permission_create_category')): ?>
            <li class="add">
                <a href="#add" style="background-position: 6px 50%;padding:1px 5px 1px 28px;background-repeat:no-repeat;background-image:url('<?php echo get_template_directory_uri() ?>/libraries/css/images/1462491942_page_white_add.png')">
                    <?php echo __('Add', 'tainacan'); ?>
                </a>
            </li>
        <?php endif; ?>
        <?php if (verify_allowed_action(get_the_ID(), 'socialdb_collection_permission_edit_category')): ?>
            <li class="edit">
                <a href="#edit"><?php echo __('Edit', 'tainacan'); ?></a>
            </li>
        <?php endif; ?>
        <?php if (verify_allowed_action(get_the_ID(), 'socialdb_collection_permission_delete_category')): ?>
            <li class="delete">
                <a href="#delete"><?php echo __('Remove', 'tainacan'); ?></a>
            </li>
        <?php endif; ?>
        <?php //if (verify_collection_moderators(get_the_ID(), get_current_user_id())):   ?>
        <li class="list" id="list_meta_single">
            <a href="#metadata" style="background-position: 6px 50%;padding:1px 5px 1px 28px;background-repeat:no-repeat;background-image:url('<?php echo get_template_directory_uri() ?>/libraries/css/images/properties.png')">
                <?php echo __('Metadata', 'tainacan'); ?>
            </a>
        </li>
        <?php // endif;   ?>
    </ul>
    <ul id="myMenuNoList" class="contextMenu" style="display:none;">
        <?php if (verify_allowed_action(get_the_ID(), 'socialdb_collection_permission_create_category')): ?>
            <li class="add">
                <a href="#add" style="background-position: 6px 50%;padding:1px 5px 1px 28px;background-repeat:no-repeat;background-image:url('<?php echo get_template_directory_uri() ?>/libraries/css/images/1462491942_page_white_add.png')">
                    <?php echo __('Add', 'tainacan'); ?>
                </a>
            </li>
        <?php endif; ?>
        <?php if (verify_allowed_action(get_the_ID(), 'socialdb_collection_permission_edit_category')): ?>
            <li class="edit">
                <a href="#edit"><?php echo __('Edit', 'tainacan'); ?></a>
            </li>
        <?php endif; ?>
        <?php if (verify_allowed_action(get_the_ID(), 'socialdb_collection_permission_delete_category')): ?>
            <li class="delete">
                <a href="#delete"><?php echo __('Remove', 'tainacan'); ?></a>
            </li>
        <?php endif; ?>
        <?php //if (verify_collection_moderators(get_the_ID(), get_current_user_id())): ?>
        <?php // endif;   ?>
    </ul>
    <!-- TAINACAN: esta div eh mostrada quando eh clicado com o botao direito sobre categorias e tags no dynatree  -->
    <ul id="myMenuSingleTag" class="contextMenu" style="display:none;">
        <?php if (verify_allowed_action(get_the_ID(), 'socialdb_collection_permission_create_tags')): ?>
            <li class="add">
                <a href="#add"><?php echo __('Add', 'tainacan'); ?></a>
            </li>
        <?php endif; ?>
        <?php if (verify_allowed_action(get_the_ID(), 'socialdb_collection_permission_edit_tags')): ?>
            <li class="edit">
                <a href="#edit"><?php echo __('Edit', 'tainacan'); ?></a>
            </li>
        <?php endif; ?>
        <?php if (verify_allowed_action(get_the_ID(), 'socialdb_collection_permission_delete_tags')): ?>
            <li class="delete">
                <a href="#delete"><?php echo __('Remove', 'tainacan'); ?></a>
            </li>
        <?php endif; ?>
    </ul>
    <!-- TAINACAN: esta div (AJAX) mostra as configuracoes da colecao  -->
    <div id='container-fluid-configuration' class="container-fluid no-padding" style="background-color: #f1f2f2">

        <div id="configuration" class="col-md-12 no-padding"></div>

    </div>
    <!-- TAINACAN: scripts utilizados para criacao e monagem dos widgets de pesquisa  -->

    <!-- TAINACAN: modal padrao bootstrap para adicao de categorias    -->
    <div class="modal fade" id="modalAddCategoria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form  id="submit_adicionar_category_single">
                    <input type="hidden" id="category_single_add_id" name="category_single_add_id" value="">
                    <input type="hidden" id="operation_event_create_category" name="operation" value="add_event_term_create">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
                        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-plus"></span>
                            <?php _e('Add Category', 'tainacan'); ?>
                            <?php do_action('add_option_in_add_category'); ?>
                        </h4>
                    </div>
                    <div id="form_add_category">
                        <div class="modal-body">

                            <div class="create_form-group">
                                <label for="category_single_name"><?php _e('Category name', 'tainacan'); ?></label>
                                <input type="text" class="form-control" id="category_single_name" name="socialdb_event_term_suggested_name" required="required" placeholder="<?php _e('Category name', 'tainacan'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="category_single_parent_name"><?php _e('Category parent', 'tainacan'); ?></label>
                                <input disabled="disabled" type="text" class="form-control" id="category_single_parent_name" placeholder="<?php _e('Right click on the tree and select the category as parent', 'tainacan'); ?>" name="category_single_parent_name">
                                <input type="hidden"  id="category_single_parent_id"  name="socialdb_event_term_parent" value="0" >
                            </div>
                            <div class="form-group">
                                <label for="category_add_description"><?php _e('Category description', 'tainacan'); ?>&nbsp;<span style="font-size: 10px;">(<?php _e('Optional', 'tainacan'); ?>)</span></label>
                                <textarea class="form-control"
                                          id="category_add_description"
                                          placeholder="<?php _e('Describe your category', 'tainacan'); ?>"
                                          name="socialdb_event_term_description" ></textarea>
                            </div>
                            <input type="hidden" id="category_single_add_collection_id" name="socialdb_event_collection_id" value="<?php echo get_the_ID(); ?>">
                            <input type="hidden" id="category_single_add_create_time" name="socialdb_event_create_date" value="<?php echo mktime(); ?>">
                            <input type="hidden" id="category_single_add_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>">
                            <input type="hidden" id="category_single_add_dynatree_id" name="dynatree_id" value="">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                            <button type="submit" class="btn btn-primary"><?php echo __('Save', 'tainacan'); ?></button>
                        </div>
                    </div>
                    <div id="another_option_category" style="display: none;">
                        <?php do_action('show_option_in_add_category'); ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- TAINACAN: modal padrao bootstrap para edicao de categorias    -->
    <div class="modal fade" id="modalEditCategoria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form  id="submit_edit_category_single">
                    <input type="hidden" id="category_single_edit_id" name="socialdb_event_term_id" value="">
                    <input type="hidden" id="operation_event_edit_category" name="operation" value="add_event_term_edit">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-pencil"></span>&nbsp;<?php echo __('Edit Category', 'tainacan'); ?></h4>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-4">
                            <div id="dynatree_modal_edit">
                            </div>
                            <?php do_action('insert_custom_dynatree_edit_category') ?>
                        </div>
                        <div class="col-md-8">
                            <div id="form_simple_eidt_category">
                                <div class="create_form-group">
                                    <label for="category_single_edit_name"><?php _e('Category name', 'tainacan'); ?></label>
                                    <input type="text" class="form-control" id="category_single_edit_name" name="socialdb_event_term_suggested_name" required="required" placeholder="<?php _e('Category name', 'tainacan'); ?>">
                                    <input type="hidden"  id="socialdb_event_previous_name"  name="socialdb_event_term_previous_name" value="0" >
                                </div>
                                <div class="form-group">
                                    <label for="category_single_parent_name_edit"><?php _e('Category parent', 'tainacan'); ?></label>
                                    <input disabled="disabled" type="text" class="form-control" id="category_single_parent_name_edit" name="category_single_term_parent_name_edit" placeholder="<?php _e('Click on the tree and select the category as parent', 'tainacan'); ?>" >
                                    <input type="hidden"  id="category_single_parent_id_edit"  name="socialdb_event_term_suggested_parent" value="0" >
                                    <input type="hidden"  id="socialdb_event_previous_parent"  name="socialdb_event_term_previous_parent" value="0" >
                                </div>
                                <div class="form-group">
                                    <label for="category_parent_name"><?php _e('Category description', 'tainacan'); ?>&nbsp;<span style="font-size: 10px;">(<?php _e('Optional', 'tainacan'); ?>)</span></label>
                                    <textarea class="form-control"
                                              id="category_edit_description"
                                              placeholder="<?php _e('Describe your category', 'tainacan'); ?>"
                                              name="socialdb_event_term_description" ></textarea>
                                </div>
                            </div>
                            <?php do_action('insert_fields_edit_modal_category') ?>
                            <button type="button" onclick="list_category_property_single()" id="show_category_property_single" class="btn btn-primary"><?php _e('Category Properties', 'tainacan'); ?></button>
                            <!-- Sinonimos -->
                            <br><br>
                            <a onclick="toggle_container_synonyms('#synonyms_container')" style="cursor: pointer;">
                                <?php _e('Synonyms', 'tainacan') ?>
                                <span class="glyphicon glyphicon-triangle-bottom"></span>
                            </a>
                            <div style="display: none;" id="synonyms_container">
                                <div id="dynatree_synonyms" style="height: 200px;overflow-y: scroll;"></div>
                                <input type="hidden" id="category_synonyms" name="socialdb_event_term_synonyms">
                            </div>
                            <!-- Fim: Sinonimos -->     
                            <input type="hidden" id="category_single_edit_collection_id" name="socialdb_event_collection_id" value="<?php echo get_the_ID(); ?>">
                            <input type="hidden" id="category_single_edit_time" name="socialdb_event_create_date" value="<?php echo mktime(); ?>">
                            <input type="hidden" id="category_single_edit_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>">
                            <input type="hidden" id="category_single_edit_dynatree_id" name="dynatree_id" value="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo __('Save', 'tainacan'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- modal propriedades -->
    <div class="modal fade bs-example-modal-lg" id="single_modal_category_property"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-lg">
            <div class="modal-content">
                <div id="single_category_property" style="max-height: 450px;overflow-x: scroll;">
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    <!-- modal exluir -->
    <div class="modal fade" id="modalExcluirCategoria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form  id="submit_delete_category_single">
                    <input type="hidden" id="category_single_delete_id" name="socialdb_event_term_id" value="">
                    <input type="hidden" id="operation" name="operation" value="add_event_term_delete">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php echo __('Remove Category', 'tainacan'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <?php echo __('Confirm the exclusion of ', 'tainacan'); ?><span id="delete_category_single_name"></span>?
                    </div>
                    <input type="hidden" id="category_single_delete_collection_id" name="socialdb_event_collection_id" value="<?php echo get_the_ID(); ?>">
                    <input type="hidden" id="category_single_delete_time" name="socialdb_event_create_date" value="<?php echo mktime(); ?>">
                    <input type="hidden" id="category_single_delete_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>">
                    <input type="hidden" id="category_single_delete_dynatree_id" name="dynatree_id" value="">

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo __('Delete', 'tainacan'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- TAINACAN: modal padrao bootstrap para adicao de items sem url    -->
    <!-- modal Adicionar Rapido -->
    <div class="modal fade" id="modal_import_objet_url" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="form_url_import">
                    <input type="hidden" id="socialdb_event_collection_id_tag" name="socialdb_event_collection_id" value="">
                    <input type="hidden" id="operation" name="operation" value="add">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <input type="text" id="title_insert_object_url" class="form-control input-lg" value="">
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4" id="image_side"></div>
                            <div class="col-md-8">
                                <textarea rows="6" class="form-control" id="description_insert_object_url" ></textarea>
                            </div>
                            <input type="hidden" id="thumbnail_url" name="thumbnail_url" value="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                        <a href="#" id="save_object_url" class="btn btn-primary"><?php echo __('Save', 'tainacan'); ?></a>
                    </div>
                </div>
                <div style="display: none;" id="loader_import_object">
                    <center><img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>"><h3><?php _e('Importing Object...', 'tainacan') ?></h3></center>
                </div>
            </div>
        </div>
    </div>

    <!-- TAINACAN: modal padrao bootstrap para adicao de tags    -->
    <!-- modal Adicionar Tag -->
    <div class="modal fade" id="modalAdicionarTag" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form  id="submit_adicionar_tag_single">
                    <input type="hidden" id="operation_tag_add" name="operation" value="add_event_tag_create">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-plus"></span>&nbsp;<?php echo __('Add Tag', 'tainacan'); ?></h4>
                    </div>
                    <div class="modal-body">

                        <div class="create_form-group">
                            <label for="tag_single_name"><?php _e('Tag', 'tainacan'); ?></label>
                            <input type="text" class="form-control" id="tag_single_name" name="socialdb_event_tag_suggested_name" required="required" placeholder="<?php _e('Tag name', 'tainacan'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="category_parent_name"><?php _e('Tag description', 'tainacan'); ?>&nbsp;<span style="font-size: 10px;">(<?php _e('Optional', 'tainacan'); ?>)</span></label>
                            <textarea class="form-control"
                                      id="tag_add_description"
                                      placeholder="<?php _e('Describe your tag', 'tainacan'); ?>"
                                      name="socialdb_event_tag_description" ></textarea>
                        </div>
                        <input type="hidden" id="tag_single_add_collection_id" name="socialdb_event_collection_id" value="<?php echo get_the_ID(); ?>">
                        <input type="hidden" id="tag_single_add_create_time" name="socialdb_event_create_date" value="<?php echo mktime(); ?>">
                        <input type="hidden" id="tag_single_add_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo __('Save', 'tainacan'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- TAINACAN: modal padrao bootstrap para edicao de tags   -->
    <div class="modal fade" id="modalEditTag" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form  id="submit_edit_tag_single">
                    <input type="hidden" id="tag_single_edit_id" name="socialdb_event_tag_id" value="">
                    <input type="hidden" id="operation_tag_edit" name="operation" value="add_event_tag_edit">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-pencil"></span>&nbsp;<?php echo __('Edit Tag', 'tainacan'); ?></h4>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-12">
                            <div class="create_form-group">
                                <label for="tag_single_edit_name"><?php _e('Tag name', 'tainacan'); ?></label>
                                <input type="text" class="form-control" id="tag_single_edit_name" name="socialdb_event_tag_suggested_name" required="required" placeholder="<?php _e('Tag name', 'tainacan'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="category_parent_name"><?php _e('Tag description', 'tainacan'); ?>&nbsp;<span style="font-size: 10px;">(<?php _e('Optional', 'tainacan'); ?>)</span></label>
                                <textarea class="form-control"
                                          id="tag_edit_description"
                                          placeholder="<?php _e('Describe your tag', 'tainacan'); ?>"
                                          name="socialdb_event_tag_description" ></textarea>
                            </div>
                            <!-- Sinonimos -->
                            <a onclick="toggle_container_synonyms('#synonyms_container_tag')" style="cursor: pointer;">
                                <?php _e('Synonyms', 'tainacan') ?>
                                <span class="glyphicon glyphicon-triangle-bottom"></span>
                            </a>
                            <div style="display: none;" id="synonyms_container_tag">
                                <div id="dynatree_synonyms_tag" style="height: 200px;overflow-y: scroll;"></div>
                                <input type="hidden" id="tag_synonyms" name="socialdb_event_tag_synonyms">
                            </div>
                            <!-- Fim: Sinonimos -->    
                            <input type="hidden" id="tag_single_edit_collection_id" name="socialdb_event_collection_id" value="<?php echo get_the_ID(); ?>">
                            <input type="hidden" id="tag_single_edit_time" name="socialdb_event_create_date" value="<?php echo mktime(); ?>">
                            <input type="hidden" id="tag_single_edit_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo __('Edit', 'tainacan'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- TAINACAN: modal padrao bootstrap para exclusao de tags   -->
    <div class="modal fade" id="modalExcluirTag" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form  id="submit_delete_tag_single">
                    <input type="hidden" id="tag_single_delete_id" name="socialdb_event_tag_id" value="">
                    <input type="hidden" id="operation_tag_delete" name="operation" value="add_event_tag_delete">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php echo __('Remove Tag', 'tainacan'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <?php echo __('Confirm the exclusion of ', 'tainacan'); ?><span id="delete_tag_single_name"></span>?
                    </div>
                    <input type="hidden" id="tag_single_delete_collection_id" name="socialdb_event_collection_id" value="<?php echo get_the_ID(); ?>">
                    <input type="hidden" id="tag_single_delete_time" name="socialdb_event_create_date" value="<?php echo mktime(); ?>">
                    <input type="hidden" id="tag_single_delete_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>">

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo __('Delete', 'tainacan'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- TAINACAN: modal padrao bootstrap para demonstracao de execucao de processos, utilizado em varias partes do socialdb   -->
    <div class="modal fade" id="modalImportMain" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <center>
                    <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                    <h3><?php _e('Please wait...', 'tainacan') ?></h3>
                </center>
            </div>
        </div>
    </div>
    <!-- TAINACAN: modal padrao bootstrap para demonstracao de execucao de processos, utilizado em varias partes do socialdb   -->
    <div class="modal fade" id="modalImportSocialnetworkClean" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <center>
                    <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                    <h3><?php _e('Undoing actions...', 'tainacan') ?></h3>
                </center>
            </div>
        </div>
    </div>

    <!-- TAINACAN: modal padrao bootstrap para redefinicaode senha   -->
    <!-- Modal redefinir senha -->
    <div class="modal fade" id="myModalPasswordReset" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form  id="formUserPasswordReset" name="formUserPasswordReset" >
                    <input type="hidden" name="operation" value="change_password">
                    <input type="hidden" name="password_user_id" id="password_user_id" value=""/>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><?php _e('Change Password!', 'tainacan'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="old_password_reset"><?php _e('Old Password', 'tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                            <input type="password" required="required" class="form-control" name="old_password_reset" id="old_password_reset" placeholder="<?php _e('Type here the old password', 'tainacan'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="new_password_reset"><?php _e('New Password'); ?><span style="color: #EE0000;"> *</span></label>
                            <input type="password" required="required" class="form-control" name="new_password_reset" id="new_password_reset" placeholder="<?php _e('Type here the new password', 'tainacan'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="new_check_password_reset"><?php _e('Confirm new password'); ?><span style="color: #EE0000;"> *</span></label>
                            <input type="password" required="required" class="form-control" name="new_check_password_reset" id="new_check_password_reset" placeholder="<?php _e('Type here your new password again', 'tainacan'); ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan'); ?></button>
                        <button type="submit" class="btn btn-primary" onclick="check_passwords();
                                    return false;"><?php _e('Submit', 'tainacan'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalShowFullDescription" tabindex="-1" role="dialog" aria-labelledby="modalShowFullDescriptionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?php echo get_template_directory_uri() ?>/controllers/collection/collection_controller.php" method="POST">
                    <input type="hidden" name="operation" value="simple_add">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><?php _e('Description', 'tainacan'); ?></h4>
                    </div>
                    <div class="modal-body" style="overflow: scroll; max-height: 500px;" id="modalShowFullDescription_body">
                        <?php echo get_the_content(); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para importacao das redes sociais -->
    <div class="modal fade" id="modalshowModalImportSocialNetwork" tabindex="-1" role="dialog" aria-labelledby="modalshowModalImportSocialNetworkLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="operation" value="">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><?php _e('Import Social Media', 'tainacan'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs">
                            <li role="presentation" class="active"><a href="#aba-youtube" aria-controls="property_data_tab" role="tab" data-toggle="tab"><?php _e('Youtube', 'tainacan') ?></a></li>
                            <li role="presentation"><a href="#aba-flickr" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Flickr', 'tainacan') ?></a></li>
                            <li role="presentation"><a href="#aba-faceboook" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Facebook', 'tainacan') ?></a></li>
                            <li role="presentation"><a href="#aba-instagram" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Instagram', 'tainacan') ?></a></li>
                            <li role="presentation"><a href="#aba-vimeo" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Vimeo', 'tainacan') ?></a></li>
                        </ul>
                        <br>
                        <div class="tab-content">
                            <!-- Aba do youtube-->
                            <div id="aba-youtube" class="tab-pane fade in active">
                                <!--h3><?php _e("Youtube Channels", 'tainacan'); ?></h3>
                                <div id="list_youtube_channels">
                                    <table  class="table table-bordered" style="background-color: #d9edf7;">
                                        <th><?php _e('Identifier', 'tainacan'); ?></th>
                                        <th><?php _e('Playlist', 'tainacan'); ?></th>
                                        <th><?php _e('Edit', 'tainacan'); ?></th>
                                        <th><?php _e('Delete', 'tainacan'); ?></th>
                                        <th><?php _e('Import', 'tainacan'); ?></th>
                                        <th><?php _e('Update', 'tainacan'); ?></th>
                                        <tbody id="table_youtube_identifiers" >
                                        </tbody>
                                    </table>
                                </div-->

                                <label for="channel_identifier"><?php _e('Entry youtube video url', 'tainacan'); ?></label>
                                <input type="text"  name="youtube_video_url" id="youtube_video_url" placeholder="<?php _e('Type here', 'tainacan'); ?>" class="form-control" /><br>
                                <input type="button" id="btn_youtube_video_url" onclick="import_youtube_video_url()" name="btn_youtube_video_url" class="btn btn-success pull-left" value="<?php _e('Import', 'tainacan'); ?>"  /><br><br>
                                <hr>
                                <label for="channel_identifier"><?php _e('Entry channel youtube identifeir', 'tainacan'); ?></label>
                                <input type="text"  name="channel_identifier" id="youtube_identifier_input" placeholder="<?php _e('Type here', 'tainacan'); ?>" class="form-control" required /><br>
                                <label for="youtube_playlist_identifier_input"><?php _e('Entry playlist youtube identifeir', 'tainacan'); ?></label>
                                <input type="text"  name="youtube_playlist_identifier_input" id="youtube_playlist_identifier_input" placeholder="<?php _e('Type here', 'tainacan'); ?>" class="form-control"/>
                                <span class="help-block"><b><?php _e('Help: ', 'tainacan'); ?></b><?php _e('Type here to get a specific playlist or leave blank to get all', 'tainacan'); ?></span><br>
                                <input type="button" id="btn_identifiers_youtube" onclick="import_youtube_channel()" name="addChannel" class="btn btn-success pull-left" value="<?php _e('Import', 'tainacan'); ?>"  />
                                <br><br>
                            </div>

                            <!-- Aba do flickr-->
                            <div id="aba-flickr" class="tab-pane fade">
                                <!--h3><?php _e("Flickr Profiles", 'tainacan'); ?></h3>
                                <div id="list_perfil_flickr">
                                    <table  class="table table-bordered" style="background-color: #d9edf7;">
                                        <th><?php _e('User Name', 'tainacan'); ?></th>
                                        <th><?php _e('Edit', 'tainacan'); ?></th>
                                        <th><?php _e('Delete', 'tainacan'); ?></th>
                                        <th><?php _e('Import', 'tainacan'); ?></th>
                                        <th><?php _e('Update', 'tainacan'); ?></th>
                                        <tbody id="table_flickr_identifiers" >
                                        </tbody>
                                    </table>
                                </div-->
                                <label for="flickr_identifiers"><?php _e('Entry an user name from a flickr profile', 'tainacan'); ?></label>
                                <input type="text"  name="flickr_identifiers" id="flickr_identifier_input" placeholder="Digite aqui" class="form-control"/></br>
                                <input type="button" id="btn_identifiers_flickr" onclick="import_flickr()" name="addChannel" class="btn btn-success pull-left" value="<?php _e('Import', 'tainacan'); ?>"  />
                                <br><br>
                            </div>

                            <!-- Aba do facebook-->
                            <div id="aba-faceboook" class="tab-pane fade">
                                <!--h3><?php _e("Facebook Profiles", 'tainacan'); ?></h3-->
                                <?php
                                $config = get_option('socialdb_theme_options');
                                $app['app_id'] = $config['socialdb_fb_api_id'];
                                $app['app_secret'] = $config['socialdb_fb_api_secret'];
                                try {
                                    $fb = new Facebook\Facebook([
                                        'app_id' => $app['app_id'],
                                        'app_secret' => $app['app_secret'],
                                        'default_graph_version' => 'v2.3',
                                    ]);

                                    $helper = $fb->getRedirectLoginHelper();
                                    $permissions = ['user_photos', 'email', 'user_likes']; // optional
                                    $collection_id = get_the_ID();
                                    $loginUrl = $helper->getLoginUrl(get_bloginfo(template_directory) . '/controllers/social_network/facebook_controller.php?collection_id=' . $collection_id . '&operation=getAccessToken', $permissions);
                                } catch (Exception $e) {
                                    
                                }

                                //echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
                                ?>
                                <a href="<?php echo $loginUrl; ?>" class="btn btn-success"><?php _e('Import Facebook Photos', 'tainacan'); ?></a>

                            </div>
                            <!-- Aba do instagram-->
                            <div id="aba-instagram" class="tab-pane fade">
                                <!--h3><?php _e("Instagram Profiles", 'tainacan'); ?></h3>
                                <div id="list_perfil_instram">
                                    <table  class="table table-bordered" style="background-color: #d9edf7;">
                                        <th><?php _e('User Name', 'tainacan'); ?></th>
                                        <th><?php _e('Edit', 'tainacan'); ?></th>
                                        <th><?php _e('Delete', 'tainacan'); ?></th>
                                        <th><?php _e('Import', 'tainacan'); ?></th>
                                        <th><?php _e('Update', 'tainacan'); ?></th>
                                        <tbody id="table_instagram_identifiers" >
                                        </tbody>
                                    </table>
                                </div-->
                                <label for="instagram_identifiers"><?php _e('Entry an user name from a instagram profile', 'tainacan'); ?></label>
                                <input type="text"  name="instagram_identifiers" id="instagram_identifier_input" placeholder="<?php _e('Type here', 'tainacan'); ?>" class="form-control"/></br>
                                <input type="button" id="btn_identifiers_instagram" onclick="import_instagram()" name="addChannel" class="btn btn-success pull-left" value="<?php _e('Import', 'tainacan'); ?>"  />
                                <br><br>
                            </div>
                            <!-- Aba do vimeo-->
                            <div id="aba-vimeo" class="tab-pane fade">
                                <!--h3><?php _e("Vimeo Profiles", 'tainacan'); ?></h3>
                                <div id="list_perfil_instram">
                                    <table  class="table table-bordered" style="background-color: #d9edf7;">
                                        <th><?php _e('User Name', 'tainacan'); ?></th>
                                        <th><?php _e('Edit', 'tainacan'); ?></th>
                                        <th><?php _e('Delete', 'tainacan'); ?></th>
                                        <th><?php _e('Import', 'tainacan'); ?></th>
                                        <th><?php _e('Update', 'tainacan'); ?></th>
                                        <tbody id="table_instagram_identifiers" >
                                        </tbody>
                                    </table>
                                </div-->
                                <label for="vimeo_identifiers"><?php _e('Entry an user name from a vimeo profile', 'tainacan'); ?></label>
                                <input type="text"  name="vimeo_identifiers" id="vimeo_identifier_input" placeholder="<?php _e('Type here', 'tainacan'); ?>" class="form-control"/><br>
                                <div class="radio">
                                    <label><input type="radio" name="optradio_vimeo" value="channels" required="required"><?php _e('Channel', 'tainacan'); ?></label>
                                </div>
                                <div class="radio">
                                    <label><input type="radio" name="optradio_vimeo" value="users" required="required"><?php _e('User', 'tainacan'); ?></label>
                                </div><br>
                                <input type="button" id="btn_identifiers_vimeo" onclick="import_vimeo()" name="addChannel" class="btn btn-success pull-left" value="<?php _e('Import', 'tainacan'); ?>"  />
                                <br><br>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan'); ?></button>
                        <!--button type="submit" class="btn btn-primary"><?php _e('Save'); ?></button-->
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para importacao geral -->
    <div class="modal fade" id="modalshowModalImportAll" tabindex="-1" role="dialog" aria-labelledby="modalshowModalImportAllLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="operation" value="">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><?php _e('Web Resource', 'tainacan'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <?php
                        if (!session_id()) {
                            session_start();
                        }

                        $config = get_option('socialdb_theme_options');
                        $app['app_id'] = $config['socialdb_fb_api_id'];
                        $app['app_secret'] = $config['socialdb_fb_api_secret'];
                        try {
                            $fb = new Facebook\Facebook([
                                'app_id' => $app['app_id'],
                                'app_secret' => $app['app_secret'],
                                'default_graph_version' => 'v2.3',
                            ]);

                            $helper = $fb->getRedirectLoginHelper();
                            $permissions = ['user_photos', 'email', 'user_likes']; // optional
                            $collection_id = get_the_ID();
                            $loginUrl = $helper->getLoginUrl(get_bloginfo(template_directory) . '/controllers/social_network/facebook_controller.php?collection_id=' . $collection_id . '&operation=getAccessToken', $permissions);
                        } catch (Exception $e) {
                            
                        }

                        //echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
                        ?>
                        <label for="item_url_import_all"><?php _e('Add item through URL', 'tainacan'); ?></label>
                        <input type="text" onkeyup="verify_import_type()"  name="item_url_import_all" id="item_url_import_all" placeholder="<?php _e('Type here', 'tainacan'); ?>" class="form-control" /><br>
                        <p>
                            <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/facebook.png' ?>" id="facebook_import_icon"/>
                            <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/youtube.png' ?>" id="youtube_import_icon"/>
                            <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/flickr.png' ?>" id="flickr_import_icon"/>
                            <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/vimeo.png' ?>" id="vimeo_import_icon"/>
                            <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/instagram.png' ?>" id="instagram_import_icon"/>
                            <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/files.png' ?>" id="files_import_icon"/>
                            <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/sites.png' ?>" id="sites_import_icon"/>
                        </p>
                        <hr>
                        <div>
                            <p>
                                <?php _e('Through this feature you can enter:', 'tainacan'); ?>
                            </p>
                            <ul>
                                <li><?php _e('Files (ex. pdf, jpg, png, etc)', 'tainacan'); ?></li>
                                <li><?php _e('Sites', 'tainacan'); ?></li>
                                <li><?php _e('Video from Youtube or Vimeo', 'tainacan'); ?></li>
                                <li><?php _e('Multiple videos from a Youtube Channel or Vimeo Channel', 'tainacan'); ?></li>
                                <li><?php _e('Images from Flickr, Facebook or Instagram', 'tainacan'); ?></li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan'); ?></button>
                        <a href="<?php echo $loginUrl; ?>" id="btn_import_fb" style="display: none;" class="btn btn-success"><?php _e('Import', 'tainacan'); ?></a>
                        <button type="button" onclick="importAll_verify()" id="btn_import_allrest" class="btn btn-primary right"><?php _e('Import'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once (dirname(__FILE__) . '/views/search/js/single_js.php'); ?>


    <?php
endwhile; // end of the loop.
get_footer();


