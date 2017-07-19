<?php
get_header();
?>

<!-- TAINACAN: esta div (AJAX) recebe html E esta presente tanto na index quanto no single, pois algumas views da administracao sao carregadas aqui -->
<div id="configuration"  class="col-md-12 no-padding"></div>
<input type="hidden" id="max_collection_showed" name="max_collection_showed" value="20">
<input type="hidden" id="total_collections" name="total_collections" value="">
<input type="hidden" id="last_index" name="last_index" value="0">

<?php if (has_nav_menu("menu-ibram")):
     include_once ("views/home/home_ibram.php");
    else: ?>
    <div id="display_view_main_page" class="container-fluid"></div>
<?php endif; ?>

<div id='container-fluid-configuration' class="container-fluid no-padding" style="background-color: #f1f2f2">
    <div id="users_div"> <!-- class="col-md-12" > é classe de users_div -->
    </div>
</div>


<?php if( ! has_nav_menu("menu-ibram") ): ?>
    <div id="loader_collections">
        <img src="<?php echo get_template_directory_uri() . '/libraries/images/new_loader.gif' ?>" width="64px" height="64px" />
        <h3> <?php _e('Loading Collections...', 'tainacan') ?> </h3>
    </div>
<?php endif; ?>

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
