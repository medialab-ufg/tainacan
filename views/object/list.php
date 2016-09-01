<?php
/*
 * View responsavel em mostrar o menu mais opcoes com as votacoes, propriedades e arquivos anexos
 *
 */
include_once('./../../helpers/view_helper.php');
include_once('./../../helpers/object/object_helper.php');
include_once ('js/list_js.php');
include_once ('js/geolocation_js.php');

$viewHelper = new ViewHelper();

$countLine = 0;
$collection_list_mode = $collection_data['collection_metas']['socialdb_collection_list_mode'];

if( !$collection_list_mode ) {
    $collection_list_mode = "cards";
}
?>

<!-- TAINACAN: hidden utilizados para execucao de processos desta view (list.php)  -->
<input type="hidden" id="keyword_pagination" name="keyword_pagination" value="<?php if (isset($keyword)) echo $keyword; ?>" />
<input type="hidden" id="sorted_form" name="sorted_form" value="<?php echo $sorted_by; ?>" />
<input type="hidden" id="default-viewMode" value="<?php echo $collection_list_mode; ?>">
<input type="hidden" id="temp-viewMode" value="<?php echo $collection_list_mode; ?>">
<input type="hidden" id="slideshow-time" value="<?php echo $_slideshow_time; ?>">
<input type="hidden" id="set-lat" value="<?php echo $geo_coordinates["lat"]; ?>">
<input type="hidden" id="set-long" value="<?php echo $geo_coordinates["long"]; ?>">
<input type="hidden" id="approx_mode" value="<?php echo $use_approx_mode; ?>">
<input type="hidden" id="approx_location" value="<?php echo $geo_loc; ?>">

<?php if ( $loop->have_posts() ): ?>

    <div id="collection-view-mode">
        <div id='<?php echo $collection_list_mode; ?>-viewMode' class='col-md-12 no-padding list-mode-set'>
            <?php while ( $loop->have_posts() ) : $loop->the_post(); $countLine++;
                $curr_id = get_the_ID();          
                $curr_date = "<strong>" . __('Created at: ', 'tainacan') . "</strong>" . get_the_date('d/m/Y');
                $latitude = get_post_meta($curr_id, "socialdb_property_" . $geo_coordinates["lat"]);
                $longitude = get_post_meta($curr_id, "socialdb_property_" . $geo_coordinates["long"]);
                $location = get_post_meta($curr_id, "socialdb_property_" . $geo_loc);            
                
                include "list_modes/modals.php";
                include "list_modes/cards.php";
                include "list_modes/list.php";
                include "list_modes/gallery.php";
            endwhile;

            include_once "list_modes/slideshow.php";
            include_once "list_modes/table.php";            
            include_once "list_modes/geolocation.php";
            ?>
        </div>
        
        <div id="temp-editor" style="display: none">
            <?php include_once "temp/edit-multiple.php"; ?>
        </div>
        
    </div>

<?php else: ?>

    <!-- TAINACAN: se a pesquisa nao encontrou nenhum item -->
    <div id="items_not_found" class="alert alert-danger">
        <span class="glyphicon glyphicon-warning-sign"></span> <?php _e('No objects found!', 'tainacan'); ?>
    </div>
    <!-- TAINACAN: se a colecao estiver vazia eh mostrado -->
    <div id="collection_empty" style="display:none" >
        <?php if (get_option('collection_root_id') != $collection_id): ?>
            <div class="jumbotron">
                <h2 style="text-align: center;"><?php _e('This collection is empty, create the first item!', 'tainacan') ?></h2>
                <p style="text-align: center;">
                    <a onclick="showAddItemText()" class="btn btn-primary btn-lg" href="#" role="button">
                        <span class="glyphicon glyphicon-plus"></span> 
                            <?php _e('Click here to add a new item', 'tainacan') ?>
                    </a>
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

$numberItems = ceil($loop->found_posts / 10);
if ($loop->found_posts > 10):
    ?>
    <!-- TAINACAN: div com a paginacao da listagem -->
    <div class="">
        <div id="center_pagination" class="col-md-12">

            <input type="hidden" id="number_pages" name="number_pages" value="<?= $numberItems; ?>">
            <div class="pagination_items col-md-4 pull-left">
                <a href="#" class="btn btn-default btn-sm first" data-action="first"><span class="glyphicon glyphicon-backward"></span><!--&laquo;--></a>
                <a href="#" class="btn btn-default btn-sm previous" data-action="previous"><span class="glyphicon glyphicon-step-backward"></span><!--&lsaquo;--></a>
                <input type="text"  style="width: 90px;" readonly="readonly" data-current-page="<?php if (isset($pagid)) echo $pagid; ?>" data-max-page="0" />
                <a href="#" class="btn btn-default btn-sm next" data-action="next"><span class="glyphicon glyphicon-step-forward"></span><!--&rsaquo;--></a>
                <a href="#" class="btn btn-default btn-sm last" data-action="last"><span class="glyphicon glyphicon-forward"></span><!--   &raquo; --></a>
            </div>

            <div class="col-md-3 center">                
                <?php
                // $_per_page = $loop->query['posts_per_page'];
                $_per_page = 10;
                
                echo $show_string . " 1 - <span class='per-page'> $_per_page </span>" . __(' of ', 'tainacan') . $loop->found_posts;
                ?>
            </div>

            <div class="col-md-3 pull-right">
                <?php _e('Items per page:', 'tainacan') ?>
                <select name="items-per-page" id="items-per-page">
                    <option>5</option>
                    <option>8</option>
                    <option value="<?php echo $_per_page; ?>" selected> <?php echo $_per_page; ?> </option>
                    <option>15</option>
                    <option>25</option>
                    <option>50</option>
                </select>
            </div>

        </div>
    </div>
<?php endif; ?>


