<?php
require_once(dirname(__FILE__) . '/models/social_network/Facebook/autoload.php');
require_once(dirname(__FILE__) . '/controllers/helpers/helpers_controller.php');
require_once(dirname(__FILE__) . '/helpers/view_helper.php');

///****************************** EXECUTANDO SCRIPTS AVULSOS *********************/
if(!get_option('tainacan_update_items_helpers')){
    HelpersController::execute_script('0003', ['collection_id' => 'all']);
    wp_redirect(get_the_permalink());
}else if (isset($_GET['execute-script'])):
    error_reporting(E_ALL);
    if ($_GET['execute-script'] == '0003') {
        HelpersController::execute_script('0003', ['collection_id' => 'all']);
    } else if ($_GET['execute-script'] == '0002') {
        HelpersController::execute_script('0002', ['collection_id' => 'all']);
    } else if ($_GET['execute-script'] == '0001') {
        HelpersController::execute_script('0001', ['collection_id' => get_the_ID()]);
    }

    wp_redirect(get_the_permalink());
endif;
/****************************************************************************** */

session_start();
get_header();
get_template_part("partials/setup","header");
global $config;
// session_start();
$_currentID_ = get_the_ID();
$visualization_page_category = get_post_meta($_currentID_, 'socialdb_collection_visualization_page_category', true);
$_enable_header_ = get_post_meta($_currentID_, 'socialdb_collection_show_header', true);
$_color_scheme = ViewHelper::getCollectionColors($_currentID_);
$search_color = ($_color_scheme) ? $_color_scheme["primary"] : "#79a6ce";

if (!has_nav_menu('menu-ibram')): ?>
    <div id="main_part_collection" class="collection_repo_config" style="background: url(<?php echo repository_bg(); ?>); display: none; margin-top: 0">
        <div class="row container-fluid">
            <div class="project-info">
                <center>
                    <h1> <?php bloginfo('name') ?> </h1>
                    <h3> <?php bloginfo('description') ?> </h3>
                </center>
            </div>
            <?php include_once "views/collection/collec_share.php"; ?>
        </div>
    </div>
    <?php
endif;

while (have_posts()) : the_post();
    if ($post->post_status != 'publish')
        wp_redirect(site_url());
    ?>
    <!-- TAINACAN: div necessaria para procedimentos do facebook  -->
    <div id="fb-root"></div>

    <!-- TAINACAN: esta div (AJAX) mostra o painel da colecao e suas acoes, estilos inline para descer a div apenas pois estava sob o header  -->
    <div id="collection_post"> </div>

    <!-- TAINACAN: esta div central que agrupa todos os locais para widgets e a listagem de objeto -->
    <div id="main_part">
        <!-- TAINACAN: este container agrupa a coluna da esquerda dos widgets, a listagem de itens e coluna da direita dos widgets -->
        <div id="container_three_columns" class="container-fluid">
            <div class="row">
                <!-- TAINACAN: esta div (AJAX) mostra os widgets para pesquisa que estao setadas na esquerda  -->
                <div id="div_left" class="col-md-3"></div>

                <!-- TAINACAN: esta div agrupa a listagem de itens ,submissao de novos itens e ordencao -->
                <div id="div_central" class="col-md-9">

                    <!-- TAINACAN: esta div agrupa a submissao de novos itens e a ordenacao (estilo inline usado para afastar do painel da colecao) -->
                    <div id="menu_object" class="row col-md-12">
                        <div class="col-lg-12 no-padding">

                            <div class="col-md-12 no-padding">
                                <div class="row search-top-container">
                                    <div class="col-md-12">
                                        <div class="titulo-colecao">
                                            <?php if ($_enable_header_ === "disabled") {
                                                 include("views/collection/config_menu.php"); ?>
                                                <h3 class="title"> <?php echo get_the_title(); ?> </h3>
                                                <hr>
                                            <?php } ?>
                                        </div>

                                        <div class="search-colecao">
                                            <div class="input-group">
                                                <input class="form-control input-medium placeholder ui-autocomplete-input" id="search_objects"
                                                       onkeyup="set_value(this)"
                                                       onkeydown="if (event.keyCode === 13)
                                                                    document.getElementById('search_main').click();"
                                                       onmouseover="$('#search_main').css('border-left', 'solid #AAA');"
                                                       onmouseout="$('#search_main').css('border-left', 'none');"
                                                       type="text" placeholder="<?php _e('Find', 'tainacan') ?>" autocomplete="off">
                                                <span class="input-group-btn">
                                                    <button id="search_main" type="button" onclick="search_objects('#search_objects')" class="btn btn-default">
                                                        <span class="glyphicon glyphicon-search"></span>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 no-padding">
                                    <!--button style="margin-top:5px;" class="btn btn-default btn-xs pull-right" onclick="showAdvancedSearch('<?php echo get_template_directory_uri() ?>');">
                                    <?php _e('Advanced Search', 'tainacan'); ?>
                                    </button-->
                                    <a style="margin-top:5px;cursor:pointer;" class="pull-right" onclick="slideFormAdvancedDown()">
                                        <?php _e('Advanced Search', 'tainacan'); ?>
                                        <span id="icon-search-bottom" class="glyphicon glyphicon-triangle-bottom" style="font-size: 14px;"></span>
                                        <span id="icon-search-top" class="glyphicon glyphicon-triangle-top" style="font-size: 14px;display:none"></span>
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-12 no-padding">
                                <div class="col-md-10 no-padding">
                                    <div id="filters_collection" style="background-color: <?php echo $search_color; ?>"></div>
                                </div>
                                <div class="col-md-2 no-padding">
                                    <div class="text-left clear-top-search">
                                        <button onclick="clear_list()" id="clear" class="prime-color-bg"><?php _e('Clear search', 'tainacan') ?></button>
                                    </div>
                                </div>
                            </div>
                            <form id="advanced_search_collection_form" style="" >
                                <input type="hidden" id="advanced_search_operation_collection" name="operation" value="do_advanced_search_collection">
                                <input type="hidden" id="advanced_search_collection" name="advanced_search_collection" value="<?php echo $_currentID_ ?>">
                                <input type="hidden" id="advanced_search_collection_id" name="collection_id" value="<?php echo $_currentID_; ?>">
                                <div style="margin-top: 10px;display:none;" class="" id="propertiesRootAdvancedSearch">
                                    <center>
                                        <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>"
                                             alt="<?php _t('Loading',1);?>" title="<?php _t('Loading',1);?>" />
                                        <h3><?php _e('Please wait...', 'tainacan') ?></h3>
                                    </center>
                                </div>
                            </form>
                        </div>

                        <!-- TAINACAN: esta div estao o botao que abre o formulario completo para submissao de itens, botao para ordenacao asc e desc, e o selectbox para selecionar a ordenacao  - col-md-6 (bootstrap) -->
                        <div class="col-md-12 header-colecao">
                            <div class="row">
                                <?php $_add_opts = unserialize(get_post_meta($_currentID_, 'socialdb_collection_add_item', true)); ?>
                                <?php if (get_option('collection_root_id') != get_the_ID() && (is_user_logged_in() && verify_allowed_action(get_the_ID(), 'socialdb_collection_permission_create_object'))): ?>
                                    <div class="tainacan-add-item col-md-1 no-padding"
                                        <?php if (has_filter('show_custom_add_item_button')): ?> style="margin-right:50px;" <?php endif; ?>
                                        <?php if (is_null($_add_opts) && count($_add_opts) == 0): ?> style="display: none" <?php endif; ?> >
                                        <?php
                                        if (has_filter('show_custom_add_item_button')):
                                            echo apply_filters('show_custom_add_item_button', '');
                                        elseif (has_action('addLibraryMenu')):
                                            do_action('addLibraryMenu', $_currentID_);
                                        else:
                                            $new_add_url = get_the_permalink($_currentID_). 'add';
                                            $_add_modes = [
                                                'write_text' => ['label' => _t('Write text'), 'action' => "showAddItemText()"],
                                                'send_file' => ['label' => _t('Send file(s)'), 'action' => "showViewMultipleItems()"],
                                                'send_file_zip' => ['label' => _t('Send file(s) via zip'), 'action' => "showSendFilesZip()"],
                                                'insert_url' => ['label' => _t('Insert URL'), 'action' => "showAddItemURL()"]
                                            ];
                                            $add_item_str = '<button type="button" class="btn btn-primary dropdown-toggle sec-color-bg" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                            $add_item_str .= __('Add', 'tainacan') . ' <span class="caret"></span>';
                                            $add_item_str .= ' </button>';
                                            $hideStr = "";
                                            if (is_array($_add_opts) && (count($_add_opts) === 1)) {
                                                $hideStr = "style='display:none'";
                                                $temp = _t('Add');
                                                $add_item_str = '<a href="'. $new_add_url .'" style="color: white; width: 100%;" class="btn"';
                                                $add_item_str .= '>' . $temp . '</a>';
                                            }
                                            ?>

                                            <div class="btn-group" role="group" aria-label="...">
                                                <div class="btn-group tainacan-add-wrapper">

                                                    <?php echo $add_item_str ?>

                                                    <ul class="dropdown-menu" <?php echo $hideStr; ?> >
                                                        <?php if (false === is_array($_add_opts)) { ?>
                                                            <!--li><a onclick="showAddItemText()"> <?php _e('Write text', 'tainacan') ?> </a> </li-->
                                                            <li><a href="<?php echo $new_add_url; ?>"> <?php _e('Write text', 'tainacan') ?> </a> </li>
                                                            <li><a onclick="showViewMultipleItems()"> <?php _e('Send file(s)', 'tainacan') ?>  </a> </li>
                                                            <li><a onclick="showSendFilesZip()"> <?php _e('Send file(s) via zip', 'tainacan') ?>  </a> </li>
                                                            <li><a onclick="showAddItemURL();"> <?php _e('Insert URL', 'tainacan') ?> </a> </li>
                                                            <?php
                                                        } else if (is_array($_add_opts)) {
                                                            if (count($_add_opts) > 0) {
                                                                foreach ($_add_modes as $_mode => $_item) {
                                                                    if (in_array($_mode, $_add_opts)) {
                                                                        if($_item['action'] !== 'showAddItemText()'){
                                                                            ?>
                                                                            <li>
                                                                                <a href="javascript:void(0)" onclick="<?php echo $_item['action']; ?>"
                                                                                   class="add_<?php echo $_mode ?>"> <?php echo $_item['label'] ?> </a>
                                                                            </li>
                                                                            <?php
                                                                        } else {
                                                                            echo '<li><a class="add_'.$_mode .'" href="'.$new_add_url.'">' . __('Write text', 'tainacan') . '</a></li>';
                                                                        }
                                                                    }
                                                                }
                                                            } else {
                                                                echo '<li><a href="'.$new_add_url.'">' . __('Write text', 'tainacan') . '</a></li>';
                                                            }
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="col-md-5 flex-box no-padding">
                                    <label for="collection_single_ordenation" class="order-by-label"><?php _t('Order by: ', 1); ?></label>
                                    <select onchange="getOrder(this)" class="form-control white"
                                            name="collection_single_ordenation" id="collection_single_ordenation">
                                        <option value=""><?php _e('Sorted by', 'tainacan') ?></option>
                                    </select>

                                    <button type="button" id="asc" class="btn btn-default pull-right sort_list"><span class="glyphicon glyphicon-sort-by-attributes"></span></button>
                                    <button type="button" id="desc" class="btn btn-default pull-right sort_list"><span class="glyphicon glyphicon-sort-by-attributes-alt"></span></button>
                                </div>

                                <?php $HideFromPlugin = (has_action('alter_home_page') ? 'hide' : ''); ?>
                                <div class="col-md-2 no-padding viewMode-control <?= $HideFromPlugin; ?>">
                                    <label class="sec-color"> <?php _e('Show:', 'tainacan') ?> </label>
                                    <button id="collectionViewMode" data-toggle="dropdown" type="button" class="btn btn-default"></button>

                                    <ul class="dropdown-menu" aria-labelledby="collectionViewMode">
                                        <?php foreach (ViewHelper::collection_view_modes() as $mode => $title): ?>
                                            <li class="<?php echo $mode ?>">
                                                <a href="javascript:void(0)" onclick="changeViewMode('<?php echo $mode ?>')">
                                                    <div class="pull-left"> <?php echo $title; ?> </div>
                                                    <div class="pull-right">
                                                        <img alt="<?php echo ucfirst(__($mode, 'tainacan')); ?>"
                                                             src="<?php echo get_template_directory_uri() . '/libraries/images/icons/collection/icon-' . $mode . '.png' ?>" />
                                                    </div>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                        <li class="geolocation hide">
                                            <a href="javascript:void(0)" onclick="changeViewMode('geolocation')">
                                                <div class="pull-left"> <?php _e('Map', 'tainacan'); ?> </div>
                                                <div class="pull-right"> <span class="glyphicon glyphicon-map-marker"></span> </div>
                                            </a>
                                        </li>
                                        <li class="table">
                                            <a href="javascript:void(0)" onclick="changeViewMode('table')">
                                                <div class="pull-left"> <?php _e('Table', 'tainacan'); ?> </div>
                                                <div class="pull-right"> <span class="glyphicon glyphicon-align-justify"></span> </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="col-md-2 selectable-items <?= $HideFromPlugin; ?>" id="normal-selectable">
                                    <?php
                                    if (is_user_logged_in() && get_the_ID() != get_option('collection_root_id') &&
                                        verify_collection_moderators(get_the_ID(), get_current_user_id())):
                                        ?>
                                        <label><?php _t('Select: ', 1); ?></label>
                                        <div class="selectors">
                                            <a onclick="select_some()" class="select_some">
                                                <?php echo ViewHelper::render_icon("selection", "png", __("Select some items", "tainacan")); ?>
                                            </a>
                                            <a onclick="select_all()" class="select_all">
                                                <?php echo ViewHelper::render_icon("select-all", "png", __("Select all items", "tainacan")); ?>
                                            </a>
                                            <input type="hidden" value="" class="bulk_action" name="bulk_action">
                                        </div>
                                        <div class="selectable-actions" style="display: none;">
                                            <a class="move_trash">
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </a>
                                            <a class="move_edition">
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-2 selectable-items selectable-items-trash <?= $HideFromPlugin; ?>" id="trash-selectable" style="display: none;">
                                    <?php
                                    if (is_user_logged_in() && get_the_ID() != get_option('collection_root_id') &&
                                        verify_collection_moderators(get_the_ID(), get_current_user_id())):
                                        ?>
                                        <label><?php _t('Select: ', 1); ?></label>
                                        <div class="selectors selectors-trash">
                                            <a onclick="select_some_trash()" class="select_some_trash">
                                                <?php echo ViewHelper::render_icon("selection", "png", __("Select some items", "tainacan")); ?>
                                            </a>
                                            <a onclick="select_all_trash()" comment class="select_all_trash">
                                                <?php echo ViewHelper::render_icon("select-all", "png", __("Select all items", "tainacan")); ?>
                                            </a>
                                            <input type="hidden" value="" class="bulk_action_trash" name="bulk_action">
                                        </div>
                                        <div class="selectable-actions" style="display: none;">
                                            <a class="move_eliminate">
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-1 no-padding trash-div pull-right">
                                    <?php
                                    if (is_user_logged_in()) {
                                        if (has_filter('tainacan_show_restore_options')) {
                                            $change_name = apply_filters('tainacan_show_restore_options', $_currentID_);
                                        } else
                                            $change_name = true;

                                        if (!$change_name) {
                                            $trash_name = "Registros cancelados";
                                        } else
                                            $trash_name = __('Trash', 'tainacan');

                                        if (get_the_ID() != get_option('collection_root_id') && verify_collection_moderators(get_the_ID(), get_current_user_id())) {
                                            ?>
                                            <button onclick="show_trash_page();" class="btn btn-default pull-right button-trash collection-trash">
                                                <?php echo $trash_name ?>
                                            </button>
                                            <?php
                                        } else {
                                            $admin_email = get_option('admin_email');
                                            $blog_email = get_bloginfo('admin_email');
                                            $user_data = get_user_by('ID', get_current_user_id())->user_email;
                                            //if ($admin_email == $user_data || $blog_email == $user_data) {
                                            ?>
                                            <button onclick="show_trash_page();" class="btn btn-default button-trash pull-right">
                                                <?php echo $trash_name; ?>
                                            </button>
                                            <?php
                                            //}
                                        }

                                        if (!$change_name) {
                                            $exit_trash_name = "Sair registros cancelados";
                                        } else
                                            $exit_trash_name = __('Exit trash', 'tainacan');
                                        ?>
                                        <button style="display: none;" id="hideTrash" onclick="hide_trash_page()" class="btn btn-default pull-right"><?php echo $exit_trash_name ?></button>
                                        <?php
                                    }
                                    ?>

                                    <button onclick="export_selected_objects()" type="button" class="btn btn-default pull-right export-btn" data-toggle="tooltip" data-placement="top" title="<?php _e('Download Results', 'tainacan') ?>">
                                        <span class="glyphicon glyphicon-download-alt">
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php
                        if (get_option('collection_root_id') == get_the_ID()):
                            ?>
                            <div role="tabpanel">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist" id="ul_menu_search">
                                    <li class="active">
                                        <a id="click_ad_search_collection"
                                           href="#ad_search_collection"
                                           onclick="wpquery_filter('socialdb_collection')"
                                           aria-controls="ad_search_collection"
                                           role="tab"
                                           data-toggle="tab">
                                            <span style="font-size: 18px"><?php _e('Collections', 'tainacan') ?></span>
                                        </a>

                                    </li>
                                    <li>
                                        <a id="click_ad_search_items"
                                           href="#ad_search_items"
                                           onclick="wpquery_filter('socialdb_object')"
                                           aria-controls="ad_search_items" role="tab" data-toggle="tab">
                                            <span style="font-size: 18px"><?php _e('Items', 'tainacan') ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <?php
                        endif;
                        ?>
                    </div>

                    <!--div id="remove"> view removida </div> -->
                    <!-- TAINACAN: esta div (AJAX) recebe o formulario para criacao e edicao de itens  -->
                    <div id="form" >
                    </div>

                    <!-- TAINACAN: esta div apenas 'envelopa' a que recebe a listagem nenhum estilo e associado  -->
                    <div id="container_socialdb" class="row col-md-12">
                        <!-- TAINACAN: esta div (AJAX)recebe a listagem de itens  -->
                        <ul id="list" class="col-md-12 row">
                        </ul>
                    </div>
                    <!-- TAINACAN: div que esta o gif que eh mostrada ao filtrar itens e outras acoes que necessitam e carregamento -->
                    <div id="loader_objects" style="display:none">
                        <center>
                            <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>"
                                 title="<?php _t('Loading objects...', 1) ?>" alt="<?php _t('Loading objects...', 1) ?>" />
                            <h3><?php _t('Loading objects...', 1) ?></h3>
                        </center>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Fim do conteudo principal da pagina (div main part) -->

    <!-- TAINACAN: esta div é mostrada quando é clicado com o botao direito sobre categorias e tags no dynatree  -->
    <?php do_action('insert_new_contextmenu_dynatree') ?>

    <ul id="myMenuSingle" class="contextMenu" style="display:none; position: fixed">
        <?php if (!$visualization_page_category || $visualization_page_category === 'right_button'): ?>
            <li class="see">
                <a href="#see" style="background-position: 6px 40%;padding:1px 5px 1px 28px;background-repeat:no-repeat;background-image:url('<?php echo get_template_directory_uri() ?>/libraries/css/images/see.png')">
                    <?php _e('View', 'tainacan'); ?>
                </a>
            </li>
        <?php endif; ?>
        <?php if (verify_allowed_action(get_the_ID(), 'socialdb_collection_permission_create_category')): ?>
            <li class="add">
                <a href="#add" style="background-position: 6px 50%;padding:1px 5px 1px 28px;background-repeat:no-repeat;background-image:url('<?php echo get_template_directory_uri() ?>/libraries/css/images/1462491942_page_white_add.png')">
                    <?php _e('Add', 'tainacan'); ?>
                </a>
            </li>
        <?php endif; ?>
        <?php if (verify_allowed_action(get_the_ID(), 'socialdb_collection_permission_edit_category')): ?>
            <li class="edit">
                <a href="#edit"><?php _e('Edit', 'tainacan'); ?></a>
            </li>
        <?php endif; ?>
        <?php if (verify_allowed_action(get_the_ID(), 'socialdb_collection_permission_delete_category')): ?>
            <li class="delete">
                <a href="#delete"><?php _e('Remove', 'tainacan'); ?></a>
            </li>
        <?php endif; ?>
        <?php //if (verify_collection_moderators(get_the_ID(), get_current_user_id())):      ?>
        <li class="list" id="list_meta_single">
            <a href="#metadata" style="background-position: 6px 50%;padding:1px 5px 1px 28px;background-repeat:no-repeat;background-image:url('<?php echo get_template_directory_uri() ?>/libraries/css/images/properties.png')">
                <?php _e('Metadata', 'tainacan'); ?>
            </a>
        </li>
        <?php // endif;      ?>
    </ul>

    <ul id="myMenuNoList" class="contextMenu" style="display:none;">
        <?php if (!$visualization_page_category || $visualization_page_category === 'right_button'): ?>
            <li class="see">
                <a href="#see" style="background-position: 6px 40%;padding:1px 5px 1px 28px;background-repeat:no-repeat;background-image:url('<?php echo get_template_directory_uri() ?>/libraries/css/images/see.png')">
                    <?php _e('See', 'tainacan'); ?>
                </a>
            </li>
        <?php endif; ?>
        <?php if (verify_allowed_action(get_the_ID(), 'socialdb_collection_permission_create_category')): ?>
            <li class="add">
                <a href="#add" style="background-position: 6px 50%;padding:1px 5px 1px 28px;background-repeat:no-repeat;background-image:url('<?php echo get_template_directory_uri() ?>/libraries/css/images/1462491942_page_white_add.png')">
                    <?php _e('Add', 'tainacan'); ?>
                </a>
            </li>
        <?php endif; ?>
        <?php if (verify_allowed_action(get_the_ID(), 'socialdb_collection_permission_edit_category')): ?>
            <li class="edit">
                <a href="#edit"><?php _e('Edit', 'tainacan'); ?></a>
            </li>
        <?php endif; ?>
        <?php if (verify_allowed_action(get_the_ID(), 'socialdb_collection_permission_delete_category')): ?>
            <li class="delete">
                <a href="#delete"><?php _e('Remove', 'tainacan'); ?></a>
            </li>
        <?php endif; ?>
        <?php //if (verify_collection_moderators(get_the_ID(), get_current_user_id())):  ?>
        <?php // endif;       ?>
    </ul>

    <!-- TAINACAN: esta div é mostrada quando eh clicado com o botao direito sobre categorias e tags no dynatree  -->
    <ul id="myMenuSingleTag" class="contextMenu" style="display:none;">
        <li class="see">
            <?php if (!$visualization_page_category || $visualization_page_category === 'right_button'): ?>
            <a href="#see" style="background-position: 6px 40%;padding:1px 5px 1px 28px;background-repeat:no-repeat;background-image:url('<?php echo get_template_directory_uri() ?>/libraries/css/images/see.png')">
                <?php _e('See', 'tainacan'); ?>
            </a>
        </li>
        <?php endif; ?>
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
        <div id="configuration" class="col-md-12 no-padding" style="margin-top: 0;">
        </div>
    </div>

    <div id='container-fluid-users' class="container-fluid no-padding" style="background-color: #f1f2f2">
        <div id="users_div"  class="col-md-12" style="margin-top: 0;">

        </div>
    </div>

    <?php

    include_once dirname(__FILE__) . "/views/collection/modals.php";
    require_once (dirname(__FILE__) . '/views/search/js/single_js.php');
    require_once (dirname(__FILE__) . '/extras/routes/routes.php');
endwhile; // end of the loop.

get_footer();