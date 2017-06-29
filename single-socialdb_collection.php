<?php
/*
 * Template Name: Index
 * Description: Pagina inicial da colecao
 */
require_once(dirname(__FILE__) . '/models/social_network/Facebook/autoload.php');
require_once(dirname(__FILE__) . '/controllers/helpers/helpers_controller.php');
require_once(dirname(__FILE__) . '/helpers/view_helper.php');
while (have_posts()) : the_post();
    if (get_post(get_the_ID())->post_status != 'publish') {
        wp_redirect(site_url());
    }
endwhile;
///****************************** EXECUTANDO SCRIPTS AVULSOS *********************/
if(!get_option('tainacan_update_items_helpers')){
    //HelpersController::execute_script('0003', ['collection_id' => 'all']);
}else if (isset($_GET['execute-script'])):
    error_reporting(E_ALL);
    if ($_GET['execute-script'] == '0003') {
      //  HelpersController::execute_script('0003', ['collection_id' => 'all']);
    } else if ($_GET['execute-script'] == '0002') {
        HelpersController::execute_script('0002', ['collection_id' => 'all']);
    } else if ($_GET['execute-script'] == '0001') {
        HelpersController::execute_script('0001', ['collection_id' => get_the_ID()]);
    }

    wp_redirect(get_the_permalink());
endif;
/* * **************************************************************************** */
session_start();
get_header();
global $config;
session_start();
$options = get_option('socialdb_theme_options');
$_currentID_ = get_the_ID();
$collection_default = get_option('disable_empty_collection');
$visualization_page_category = get_post_meta($_currentID_, 'socialdb_collection_visualization_page_category', true);
$_enable_header_ = get_post_meta($_currentID_, 'socialdb_collection_show_header', true);
$_color_scheme = ViewHelper::getCollectionColors($_currentID_);
$search_color = ($_color_scheme) ? $_color_scheme["primary"] : "#79a6ce";

if (!has_nav_menu('menu-ibram')):
    $_r_bg = repository_bg($col_root_id);
    ?>
    <div id="main_part_collection" class="collection_repo_config" 
         style="background: url(<?php echo $_r_bg; ?>); display: none; margin-top: 50px;">
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
else:
    echo '<input type="hidden" name="ibram_menu" value="ibram_menu_activated" />';
    ?>
    <style type="text/css"> .ibram-header {  margin-top: 50px; } </style>
<?php
endif;

while (have_posts()) : the_post();
    if (get_post(get_the_ID())->post_status != 'publish') {
        wp_redirect(site_url());
    }
    ?>
    <!-- TAINACAN: div necessaria para procedimentos do facebook  -->
    <div id="fb-root"></div>

    <!-- TAINACAN: esta div (AJAX) mostra o painel da colecao e suas acoes, estilos inline para descer a div apenas pois estava sob o header  -->
    <div id="collection_post" style="margin-top: 50px;"> </div>

    <!-- TAINACAN - BEGIN: ITENS NECESSARIOS PARA EXECUCAO DE VARIAS PARTES DO SOCIALDB -->
    <input type="hidden" id="visualization_page_category" name="visualization_page_category" value="<?php echo (!$visualization_page_category || $visualization_page_category === 'right_button') ? 'right_button' : 'click'; ?>">
    <input type="hidden" id="show_collection_default" name="show_collection_default" value="<?php echo (!$collection_default || $collection_default === 'false') ? 'show' : 'hide'; ?>">
    <input type="hidden" id="socialdb_fb_api_id" name="socialdb_fb_api_id" value="<?php echo $options['socialdb_fb_api_id']; ?>">
    <input type="hidden" id="socialdb_embed_api_id" name="socialdb_embed_api_id" value="<?php echo $options['socialdb_embed_api_id']; ?>">
    <input type="hidden" id="current_user_id" name="current_user_id" value="<?php echo get_current_user_id(); ?>">
    <input type="hidden" id="src" name="src" value="<?php echo get_template_directory_uri() ?>">
    <input type="hidden" id="collection_id" name="collection_id" value="<?php echo get_the_ID() ?>">
    <input type="hidden" id="mode" name="mode" value="<?php echo $mode ?>">
    <input type="hidden" id="site_url" value="<?php echo site_url(); ?>" >
    <input type="hidden" id="collection_root_id" value="<?php echo get_option('collection_root_id'); ?>">
    <input type="hidden" id="collection_root_url" value="<?php echo get_the_permalink(get_option('collection_root_id')) ?>">
    <input type="hidden" id="socialdb_permalink_collection" name="socialdb_permalink_collection" value="<?php echo get_the_permalink(get_the_ID()); ?>" />
    <input type="hidden" id="slug_collection" name="slug_collection" value="<?php echo get_post(get_the_ID())->post_name; ?>"> <!-- utilizado na busca -->
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

    <!-- Minhas colecoes -->
    <input type="hidden" id="mycollections" name="mycollections" value="<?php
    if (isset($_GET['mycollections'])) {
        echo 'true';
    }
    ?>">

    <!-- Colecoes compartilhadas -->
    <input type="hidden" id="sharedcollections" name="sharedcollections" value="<?php
    if (isset($_GET['sharedcollections'])) {
        echo 'true';
    }
    ?>">

    <!-- PAGINA DO ITEM -->
    <input type="hidden" id="object_page" name="object_page" value="<?php
    if (get_query_var('item') && !get_query_var('edit-item')) {
        echo trim(get_query_var('item'));
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

    <!-- Se devera abrir o formulario de adicao item -->
    <input type="hidden" id="open_create_item_text" name="open_create_item_text" value="<?php
    if (isset($_GET['create-item'])) {
        echo $_GET['create-item'];
    }
    ?>">

    <input type="hidden" id="open_login" name="open_login" value="<?php
    if (isset($_GET['open_login'])) {
        echo $_GET['open_login'];
    }
    ?>">

    <input type="hidden" id="open_edit_item" name="open_edit_item" value="<?php
    if (isset($_GET['open_edit_item'])) {
        echo $_GET['open_edit_item'];
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
    <input type="hidden" id="global_tag_id" name="global_tag_id" value="<?php echo (get_term_by('slug', 'socialdb_property_fixed_tags', 'socialdb_property_type')->term_id) ? get_term_by('slug', 'socialdb_property_fixed_tags', 'socialdb_property_type')->term_id : 'tag' ?>"> <!-- utilizado na busca -->
    <input type="hidden" id="search-advanced-text" value="<?php echo (isset($_GET['search-advanced-text']) && !empty($_GET['search-advanced-text'])) ? $_GET['search-advanced-text'] : '' ?>">

    <!-- TAINACAN - END: ITENS NECESSARIOS PARA EXECUCAO DE VARIAS PARTES DO SOCIALDB -->

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
                                            <?php
                                            if ($_enable_header_ === "disabled") {
                                                include("views/collection/config_menu.php");
                                                ?>
                                                <h3 class="title"> <?php echo get_the_title(); ?> </h3>
                                                <hr>
                                            <?php } ?>
                                        </div>

                                        <div class="search-colecao">
                                            <div class="input-group" style="z-index: 1;">
                                                <input  style="font-size: 13px;z-index: 1;" class="form-control input-medium placeholder ui-autocomplete-input" id="search_objects"
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
                                        <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
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
                                             $collection_id = get_the_ID();
                                             do_action('addLibraryMenu', $collection_id);
                                         else:
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
                                                 //$add_item_str = '<a href="javascript:void(0)" style="color: white; width: 100%;" class="btn"';
                                                 //$add_item_str .= 'onclick="' . $_add_modes[$_add_opts[0]]['action'] . '">' . $temp . '</a>';
                                                 $add_item_str = '<a href="'. get_the_permalink($collection_id).'criar-item" style="color: white; width: 100%;" class="btn"';
                                                 $add_item_str .= '>' . $temp . '</a>';
                                             }
                                             ?>

                                            <div class="btn-group" role="group" aria-label="...">
                                                <div class="btn-group tainacan-add-wrapper">

                                                    <?php echo $add_item_str ?>

                                                    <ul class="dropdown-menu" <?php echo $hideStr; ?> >
                                                        <?php if (false === is_array($_add_opts)) { ?>
                                                            <!--li><a onclick="showAddItemText()"> <?php _e('Write text', 'tainacan') ?> </a> </li-->
                                                            <li><a href="<?php echo get_the_permalink($collection_id).'criar-item'; ?>"> <?php _e('Write text', 'tainacan') ?> </a> </li>
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
                                                                        }else{
                                                                            echo '<li><a class="add_'.$_mode .'"  href="'.get_the_permalink($collection_id).'criar-item">' . __('Write text', 'tainacan') . '</a></li>';
                                                                        }
                                                                    }
                                                                }
                                                            } else {
                                                                //echo '<li><a onclick="showAddItemText()">' . _e('Write text', 'tainacan') . '</a></li>';
                                                                echo '<li><a href="'.get_the_permalink($collection_id).'criar-item">' . __('Write text', 'tainacan') . '</a></li>';
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

                                <?php
                                $HideFromPlugin = (has_action('alter_home_page') ? 'hide' : '');
                                ?>
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
                                        <label for="collection_single_ordenation"><?php _t('Select: ', 1); ?></label>
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
                                        <label for="collection_single_ordenation"><?php _t('Select: ', 1); ?></label>
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
                                            $collection_id = get_the_ID();
                                            $change_name = apply_filters('tainacan_show_restore_options', $collection_id);
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
                    <div id="loader_objects" style="display:none"><center><img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>"><h3><?php _e('Loading objects...', 'tainacan') ?></h3></center></div>
                    <br>
                    <!--a id="home_button" href="#" class="btn btn-default"><span class="glyphicon glyphicon-th-list"></span></a-->

                </div>

            </div>
        </div>
    </div>
    <!-- Fim do conteudo principal da pagina (div main part) -->

    <!-- TAINACAN: esta div é mostrada quando é clicado com o botao direito sobre categorias e tags no dynatree  -->
    <?php do_action('insert_new_contextmenu_dynatree') ?>

    <ul id="myMenuSingle" class="contextMenu" style="display:none;">
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
        <div id="configuration" class="col-md-12 no-padding" style="margin-top: 0;"></div>
    </div>

    <div id='container-fluid-users' class="container-fluid no-padding" style="background-color: #f1f2f2">
        <div id="users_div"  class="col-md-12" style="margin-top: 0;"></div>
    </div>

    <!-- TAINACAN: scripts utilizados para criacao e montagem dos widgets de pesquisa  -->

    <!--------------------------------------------------------------- Definição de janelas modais --------------------------------------------------------------->

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
                                <div class="form-group" <?php do_action('description_category_view') ?>>
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
                            <a onclick="toggle_container_synonyms('#synonyms_container')" <?php do_action('synonyms_category_view') ?> style="cursor: pointer;">
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

    <div class="modal fade" id="modal_send_files_items_zip" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form  id="submit_files_item_zip">
                    <input type="hidden" id="operation" name="operation" value="send_files_item_zip">
                    <input type="hidden" name="collection_id" value="<?php echo get_the_ID() ?>">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-plus"></span>&nbsp;<?php echo __('Import files from zip', 'tainacan'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <input type="radio" onchange="changeFormZip(this.value)" id="sendFileItemZip" name="sendfile_zip" value="file" checked="checked"/> <?php echo __('Send File', 'tainacan'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" onchange="changeFormZip(this.value)" id="sendUrlItemZip" name="sendfile_zip" value="url"/> <?php echo __('In Server', 'tainacan'); ?>
                        <br>
                        <div id="div_send_file_zip">
                            <input type="file" accept=".zip" name="file_zip">
                        </div>
                        <div id="div_in_server_zip" style="display:none;">
                            <input type="text" name="file_path" placeholder="<?php echo __('Insert file path in this server', 'tainacan'); ?>" class="form-control">
                        </div>
                        <br><br>
                        <div>
                            <input type="checkbox" onclick="changeMetadataZipDiv()" id="zip_folder_hierarchy" name="zip_folder_hierarchy" value="1">&nbsp;<?php echo __('Import Folder Hierarchy', 'tainacan'); ?>
                        </div>
                        <div id="metadata_zip_div" style="display:none;">
                            <input type="radio" onchange="changeFormZipMetadata(this.value)" id="createMetaItemZip" name="meta_zip" value="create" checked="checked"/> <?php echo __('Create Metadata', 'tainacan'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" onchange="changeFormZipMetadata(this.value)" id="chooseMetaItemZip" name="meta_zip" value="choose"/> <?php echo __('Choose Metadata', 'tainacan'); ?>
                            <br>
                            <div id="div_create_metadata_zip">
                                <input type="text" name="meta_name" placeholder="<?php echo __('Insert value', 'tainacan'); ?>" class="form-control">
                            </div>
                            <div id="div_choose_metadata_zip" style="display:none;">
                                <select id="chosen_meta" name="chosen_meta" class="form-control">
                                    <option>[<?php echo __('Select', 'tainacan'); ?>]</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo __('Import', 'tainacan'); ?></button>
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
    <div class="modal fade" id="modalImportMain" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <center>
                    <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                    <h3><?php _e('Please wait...', 'tainacan') ?></h3>
                </center>
            </div>
        </div>
    </div>

    <!-- TAINACAN: modal padrao bootstrap para exibição do loading de importação   -->
    <div class="modal fade" id="modalImportLoading" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <center>
                    <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                    <h3><?php _e('Please wait...', 'tainacan') ?></h3>
                    <div id="divprogress">
                        <progress id='progressbarmapas' value='0' max='100' style='width: 100%;'></progress><br>
                    </div>
                </center>
            </div>
        </div>
    </div>

    <!-- TAINACAN: modal padrao bootstrap para confirmação de importação Mapas Culturais   -->
    <div class="modal fade" id="modalImportConfirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content"> <!--Conteúdo da janela modal-->

                <div class="modal-header"><!--Cabeçalho-->
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only"><?php _e('Do you really want to close?', 'tainacan') ?></span>
                    </button>

                    <h4 class="modal-title text-center"><?php _e('Count of elements', 'tainacan') ?></h4>
                </div><!--Fim cabeçalho-->

                <div class="modal-body"><!--Conteúdo-->
                    <div class="text-center">
                        <dl class="dl-horizontal">
                            <dt><?php _e('Agents', 'tainacan') ?>: </dt>
                            <dd id="agents">00</dd>

                            <dt><?php _e('Projects', 'tainacan') ?>: </dt>
                            <dd id="projects">00</dd>

                            <dt><?php _e('Events', 'tainacan') ?>: </dt>
                            <dd id="events">00</dd>

                            <dt><?php _e('Spaces', 'tainacan') ?></dt>
                            <dd id="spaces">00</dd>
                        </dl>
                    </div>
                </div><!--Fim conteúdo-->

                <div class="modal-footer"><!--Rodapé-->
                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                        <?php _e('Cancel', 'tainacan'); ?>
                    </button>

                    <button type="button" class="btn btn-primary"
                            onclick="import_mapas_culturais($('#url_mapa_cultural').val().trim())"
                            id="submit_mapa_cultural_url"
                            class="btn btn-primary tainacan-blue-btn-bg pull-right">
                                <?php _e('Import', 'tainacan'); ?>
                    </button>

                </div><!--Fim rodapé-->

            </div>
        </div>
    </div>

    <!-- TAINACAN: modal padrao bootstrap para exibição dos itens importados do Mapa Cultural   -->
    <div class="modal fade" id="modalImportFinished" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content"> <!--Conteúdo da janela modal-->
                <div class="modal-header"><!--Cabeçalho-->
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only"><?php _e('Do you really want to close?', 'tainacan') ?></span>
                    </button>

                    <h4 class="modal-title text-center"><?php _e('Imported elements', 'tainacan') ?></h4>
                </div><!--Fim cabeçalho-->

                <div class="modal-body"><!--Conteúdo-->
                    <div class="text-center">
                        <h4 id="ontology_name"></h4>
                        <dl class="dl-horizontal">
                            <dt><?php _e('Classes', 'tainacan') ?>: </dt>
                            <dd id="classes">00</dd>

                            <dt><?php _e('Datatype', 'tainacan') ?>: </dt>
                            <dd id="datatype">00</dd>

                            <dt><?php _e('Object Property', 'tainacan') ?>: </dt>
                            <dd id="object_property">00</dd>

                            <dt><?php _e('Individuals', 'tainacan') ?></dt>
                            <dd id="individuals">00</dd>
                        </dl>
                    </div>
                </div><!--Fim conteúdo-->

                <div class="modal-footer"><!--Rodapé-->
                    <button type="button" class="btn btn-primary"
                            onclick="go_to_ontology()"
                            id="go_to_ontology"
                            class="btn btn-primary tainacan-blue-btn-bg pull-right">
                                <?php _e('Go to ontology', 'tainacan'); ?>
                    </button>

                </div><!--Fim rodapé-->
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

    <!-- TAINACAN: modal padrao bootstrap para redefinicao de senha -->
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

    <!-- TAINACAN: modal padrao bootstrap para exibição de um unico usuario   -->
    <div class="modal fade" id="modalShowUser" tabindex="-1" role="dialog" aria-labelledby="ShowUser" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content"> <!--Conteúdo da janela modal-->
                <div class="modal-header"><!--Cabeçalho-->
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only"><?php _e('Do you really want to close?', 'tainacan') ?></span>
                    </button>

                    <h4 class="modal-title text-center"><?php _e('User information', 'tainacan') ?></h4>
                </div><!--Fim cabeçalho-->

                <div class="modal-body" style="margin-bottom: 30px;"><!--Conteúdo-->
                    <div class="col-md-12" id="user_info">

                    </div>
                </div><!--Fim conteúdo-->

                <div class="modal-footer">
                    <input type="hidden" id="elemenID" value="">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan'); ?></button>
                    <button type="button" onclick="update_user_info();" id="btn_update_user" class="btn btn-primary right"><?php _e('Save'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (has_action('add_new_modals')) {
        do_action('add_new_modals', '');
    }

    if (has_filter('tainacan_show_reason_modal')) {
        apply_filters('tainacan_show_reason_modal', "");
    }
    ?>
    <?php require_once (dirname(__FILE__) . '/views/search/js/single_js.php'); ?>
    <?php require_once (dirname(__FILE__) . '/extras/routes/routes.php'); ?>

    <?php
endwhile; // end of the loop.
get_footer();
