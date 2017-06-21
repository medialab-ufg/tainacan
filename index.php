<?php
get_header();
$options = get_option('socialdb_theme_options');
$collection_default = get_option('disable_empty_collection');
?>
<!-- TAINACAN: hiddeNs responsaveis em realizar acoes do repositorio -->
<input type="hidden" id="show_collection_default" name="show_collection_default" value="<?php echo (!$collection_default || $collection_default === 'false') ? 'show' : 'hide'; ?>">
<input type="hidden" id="src" name="src" value="<?php echo get_template_directory_uri() ?>">
<input type="hidden" id="repository_main_page" name="repository_main_page" value="true">
<input type="hidden" id="collection_root_url" value="<?php echo get_the_permalink(get_option('collection_root_id')) ?>">
<input type="hidden" id="info_messages" name="info_messages" value="<?php
if (isset($_GET['info_messages'])) {
    echo $_GET['info_messages'];
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
    if (isset($_GET['category'])) {
        echo trim($_GET['category']);
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
<input type="hidden" id="socialdb_fb_api_id" name="socialdb_fb_api_id" value="<?php echo $options['socialdb_fb_api_id']; ?>">
<input type="hidden" id="socialdb_embed_api_id" name="socialdb_embed_api_id" value="<?php echo $options['socialdb_embed_api_id']; ?>">
<input type="hidden" id="collection_id" name="collection_id" value="<?php echo get_option('collection_root_id'); ?>">
<div id="main_part" class="home">
    <?php if(has_action('alter_home_page')): ?>
        <?php do_action('alter_home_page') ?>
    <?php else: ?>
        <div class="row container-fluid">
            <div class="project-info">
                <center>
                    <h1> <?php bloginfo('name') ?> </h1>
                    <h3> <?php bloginfo('description') ?> </h3>
                </center>
            </div>
            <div id="searchBoxIndex" class="col-md-3 col-sm-12 center">
                <form id="formSearchCollections" role="search">
                    <div class="input-group search-collection search-home">
                        <input style="color:white;" type="text" class="form-control" name="search_collections" id="search_collections" onfocus="changeBoxWidth(this)" placeholder="<?php _e('Find', 'tainacan') ?>"/>
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button"  onclick="redirectAdvancedSearch('#search_collections');"><span class="glyphicon glyphicon-search"></span></button>
                        </span>
                    </div>
                </form>
                <a onclick="redirectAdvancedSearch(false);" href="javascript:void(0)" class="col-md-12 adv_search">
                    <span class="white"><?php _e('Advanced search', 'tainacan') ?></span>
                </a>
             </div>
        </div>

        <?php include_once "views/collection/collec_share.php"; ?>
    <?php endif; ?>
</div>
</header>

<!-- TAINACAN: esta div (AJAX) recebe html E esta presente tanto na index quanto no single, pois algumas views da administracao sao carregadas aqui -->
<div id="configuration"  class="col-md-12 no-padding"></div>
<input type="hidden" id="max_collection_showed" name="max_collection_showed" value="20">
<input type="hidden" id="total_collections" name="total_collections" value="">
<input type="hidden" id="last_index" name="last_index" value="0">

<?php if ( has_nav_menu("menu-ibram") ):
     include_once ("views/home/home_ibram.php");
    else: ?>
    <div id="display_view_main_page" class="container-fluid"></div>
<?php endif; ?>

<div id='container-fluid-configuration' class="container-fluid no-padding" style="background-color: #f1f2f2">
    <div id="users_div"> <!-- class="col-md-12" > é classe de users_div -->

    </div>
    <?php /* <link href="http://localhost/wordpress/biblioteca/wp-content/themes/tainacan/libraries/css/bootstrap_data_table/data_table.css?ver=4.7.5" rel="stylesheet" type="text/css"> */ ?>
</div>

<!-- TAINACAN: esta div possui um gif que e colocada como none quando a listagem de recents e populares  -->
<div id="loader_collections">
    <img src="<?php echo get_template_directory_uri() . '/libraries/images/new_loader.gif' ?>" width="64px" height="64px" />
    <h3> <?php _e('Loading Collections...', 'tainacan') ?> </h3>
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
</body>

 <?php require_once (dirname(__FILE__) . '/extras/routes/routes.php'); ?>
<?php get_footer(); ?>