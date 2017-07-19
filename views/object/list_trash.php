<?php
include_once('./../../helpers/view_helper.php');
include_once('./../../helpers/object/object_helper.php');
include_once ('js/list_trash_js.php');
include_once ('helper/loader.php');

$viewHelper = new ViewHelper();
$objHelper->renderCollectionPagination($loop->found_posts, $loop->post_count, $pagid, $show_string, 'top_pag');

if ( $loop->have_posts() ):
    // Determina # de colunas;
    if ($collection_data['collection_metas']['socialdb_collection_columns'] != '')
        $classColumn = 12 / $collection_data['collection_metas']['socialdb_collection_columns'];
    ?>
    <div id="collection-view-mode" class="trash-listing">
        <div id='<?php echo $collection_list_mode; ?>-viewMode' class='col-md-12 no-padding list-mode-set'>
            <?php while ( $loop->have_posts() ) : $loop->the_post(); $countLine++;
                $curr_id = get_the_ID();
                      
                include "list_modes/modals.php";
                include "list_modes/cards_trash.php";
                include "list_modes/list_trash.php";
                include "list_modes/gallery_trash.php";
            endwhile;

            include_once "list_modes/slideshow_trash.php";
            include_once "list_modes/table_trash.php";
            ?>
        </div>
    </div>
<?php else: ?>
    <!-- TAINACAN: se a pesquisa nao encontrou nenhum item -->
    <div id="items_not_found" class="alert alert-danger">
        <span class="glyphicon glyphicon-warning-sign"></span>&nbsp;<?php _e('No objects found!', 'tainacan'); ?>
    </div>
    <!-- TAINACAN: se a colecao estiver vazia eh mostrado -->
    <div id="collection_empty" style="display:none" >
        <?php if (get_option('collection_root_id') != $collection_id): ?>
            <div class="jumbotron">
                <h2 style="text-align: center;"><?php _e('This collection is empty, create the first item!', 'tainacan') ?></h2>
                <p style="text-align: center;"><a onclick="show_form_item()" class="btn btn-primary btn-lg" href="#" role="button"><span class="glyphicon glyphicon-plus"></span>&nbsp;<?php _e('Click here to add a new item', 'tainacan') ?></a>
                </p>
            </div>
        <?php else: ?>
            <div class="jumbotron">
                <h2 style="text-align: center;"><?php _e('This repository is empty, create the first collection!', 'tainacan') ?></h2>
                <p style="text-align: center;"><a onclick="showModalCreateCollection()" class="btn btn-primary btn-lg" href="#" role="button"><span class="glyphicon glyphicon-plus"></span>&nbsp;<?php _e('Click here to add a new collection', 'tainacan') ?></a>
                </p>
            </div>
        <?php endif; ?>
    </div>
<?php
endif;

$objHelper->renderCollectionPagination($loop->found_posts, $loop->post_count, $pagid, $show_string, 'bottom_pag');

