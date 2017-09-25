<?php
include_once(dirname(__FILE__).'/../../helpers/view_helper.php');
include_once('./../../helpers/object/object_helper.php');
include_once ('js/list_js.php');
include_once ('js/geolocation_js.php');
include_once ('helper/loader.php');
$objHelper->renderCollectionPagination($loop->found_posts, (isset($posts_per_page)) ? $posts_per_page : $loop->post_count, $pagid, $show_string, 'top_pag',$loop);

if ( $loop->have_posts() ) { ?>

    <div id="collection-view-mode">
        <div id='<?php echo $collection_list_mode; ?>-viewMode' class='col-md-12 no-padding list-mode-set'>
            <?php
            while ( $loop->have_posts() ) : $loop->the_post(); $countLine++;
                $curr_id = get_the_ID();
                $itemURL = get_the_permalink($curr_id);
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

    </div> <br />

<?php } else {
    if(get_option('collection_root_id') != $collection_id): ?>
    <div id="items_not_found_" class="alert alert-danger" style="margin-top: 20px;text-align: center;">
        <span class="glyphicon glyphicon-warning-sign"></span> <?php _t('No objects found!', 1); ?>
    </div>
    <?php elseif(get_option('collection_root_id') === $collection_id):
        echo '<center><div class="jumbotron"><h2>' . __('No collections found!','tainacan') . '</h2></div></center>';
    else: ?>
    <div id="collection_empty" style="display:none">
        <?php
        if (get_option('collection_root_id') != $collection_id):
            if (has_action('empty_collection_message')):
                do_action('empty_collection_message');
            else:
                echo '<div class="jumbotron"><h2>' . _t('No collection or item found!') . '</h2></div>';
            endif;
        endif; ?>
    </div>
<?php
    endif;

}

$objHelper->renderCollectionPagination($loop->found_posts, (isset($posts_per_page)) ? $posts_per_page : $loop->post_count, $pagid, $show_string, 'bottom_pag',$loop);