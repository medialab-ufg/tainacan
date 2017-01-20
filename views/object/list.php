<?php
include_once('./../../helpers/view_helper.php');
include_once('./../../helpers/object/object_helper.php');
include_once ('js/list_js.php');
include_once ('js/geolocation_js.php');

$viewHelper = new ViewHelper();
$objHelper = new ObjectHelper();

$countLine = 0;
$collection_list_mode = $collection_data['collection_metas']['socialdb_collection_list_mode'];

if( !$collection_list_mode ) {
    $collection_list_mode = "cards";
}

if( "geolocation" === $collection_list_mode && is_null($geo_coordinates) && $is_filtered_page ) {
    $geo_coordinates["lat"] = get_post_meta($col_id, "socialdb_collection_latitude_meta", true);
    $geo_coordinates["long"] = get_post_meta($col_id, "socialdb_collection_longitude_meta", true);

    echo '<input type="hidden" id="filtered_collection" value="true" />';
}
$_fxd_title = $viewHelper->terms_fixed['title']->name;
$numberItems = ceil($loop->found_posts / 10);

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
<input type="hidden" id="repo_fixed_title" value="<?php echo $_fxd_title; ?>">
    <input type="hidden" id="original_post_count" value="<?php echo $loop->post_count; ?>" />

<?php
$objHelper->renderCollectionPagination($loop->found_posts, $numberItems, $pagid, $show_string, 'top_pag');

if ( $loop->have_posts() ): ?>
    <div id="collection-view-mode">
        <div id='<?php echo $collection_list_mode; ?>-viewMode' class='col-md-12 no-padding list-mode-set'>
            <?php
            while ( $loop->have_posts() ) : $loop->the_post(); $countLine++;
                $curr_id = get_the_ID();
                $latitude = get_post_meta($curr_id, "socialdb_property_" . $geo_coordinates["lat"]);
                $longitude = get_post_meta($curr_id, "socialdb_property_" . $geo_coordinates["long"]);
                $location = get_post_meta($curr_id, "socialdb_property_" . $geo_loc);
                $curr_date = "<strong>" . __('Created at: ', 'tainacan') . "</strong>" . get_the_date('d/m/Y');
                $_object_description = get_the_content();
                
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
        
    </div>

<?php else: ?>

    <!-- TAINACAN: se a pesquisa nao encontrou nenhum item -->
    <div id="items_not_found" class="alert alert-danger">
        <span class="glyphicon glyphicon-warning-sign"></span> <?php _e('No objects found!', 'tainacan'); ?>
    </div>
    <!-- TAINACAN: se a colecao estiver vazia eh mostrado -->
    <div id="collection_empty" style="display:none" >
        <?php if (get_option('collection_root_id') != $collection_id): ?>
            <?php 
               if(has_action('empty_collection_message')):
                   do_action('empty_collection_message');
               else:
                    ?>
                    <div class="jumbotron">
                        <h2 style="text-align: center;"><?php _e('This collection is empty, create the first item!', 'tainacan') ?></h2>
                        <p style="text-align: center;">
                            <a onclick="showAddItemText()" class="btn btn-primary btn-lg" href="#" role="button">
                                <span class="glyphicon glyphicon-plus"></span> <?php _e('Click here to add a new item', 'tainacan') ?>
                            </a>
                        </p>
                    </div>
                  <?php 
               endif;
            ?>            
        <?php else: ?>
            <?php 
               if(has_action('empty_collection_message')):
                   do_action('empty_collection_message');
               else:
                    ?>
                    <div class="jumbotron">
                        <h2 style="text-align: center;"><?php _e('This repository is empty, create the first collection!', 'tainacan') ?></h2>
                        <p style="text-align: center;"><a onclick="showModalCreateCollection()" class="btn btn-primary btn-lg" href="#" role="button"><span class="glyphicon glyphicon-plus"></span>&nbsp;<?php _e('Click here to add a new collection', 'tainacan') ?></a>
                        </p>
                    </div>
                    <?php 
               endif;
            ?>
        <?php endif; ?>
    </div>
    
<?php
endif;

$objHelper->renderCollectionPagination($loop->found_posts, $numberItems, $pagid, $show_string, 'bottom_pag');
?>