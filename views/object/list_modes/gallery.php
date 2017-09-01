<?php
    $additional_curr_class = "";
    if ( ($countLine % 4) == 1 ) { 
        $additional_curr_class = "first-el";
    } else if( ($countLine % 4) == 0 ) {
        $additional_curr_class = "last-el";
    }    
?>

<div class="col-md-3 gallery-view-container top-div <?php echo $additional_curr_class ?>" data-order="<?php echo $countLine; ?>"
     <?php if ($collection_list_mode != "gallery"): ?> style="display: none" <?php endif ?> >
    
    <input type="hidden" id="add_classification_allowed_<?php echo get_the_ID() ?>" name="add_classification_allowed" value="<?php echo (string) verify_allowed_action($collection_id, 'socialdb_collection_permission_add_classification', get_the_ID()); ?>" />

    <div class="gallery-wrapper droppableClassifications toggleSelect">

        <div class="item-thumb">
            <?php if(empty($trash_list)): ?>
                <a href="<?php echo $itemURL; ?>">
                    <?php echo get_item_thumb_image($curr_id); ?>
                </a>
            <?php elseif ($trash_list): echo get_item_thumb_image($curr_id); endif; ?>
        </div>

        <div class=" title-container">
            <h5 class="item-display-title">
                <?php if(empty($trash_list)): ?>
                    <a href="<?php echo $itemURL; ?>">
                        <?php  echo wp_trim_words( get_the_title(), 4 ); ?>
                    </a>
                <?php elseif ($trash_list): echo wp_trim_words( get_the_title(), 4 ); endif; ?>
            </h5>

            <div class="gallery-metadata">
                <div class="col-md-5 no-padding">
                    <?php if (get_option('collection_root_id') != $collection_id): ?>
                        <?php if(has_action('container_rankings_gallery')): ?>
                            <?php do_action('container_rankings_gallery',$curr_id) ?>
                        <?php else: ?>
                            <div id="r_gallery_<?php echo $curr_id ?>" class="rankings-container"></div>
                        <?php endif; ?>                    
                    <?php endif; ?>
                </div>

                <?php if(empty($trash_list)): ?>
                    <ul class="item-funcs col-md-6 right">
                        <input type="hidden" class="post_id" name="post_id" value="<?= $curr_id ?>">
                        <li class="tainacan-museum-clear">
                            <a id="modal_network<?php echo $curr_id; ?>" onclick="showModalShareNetwork(<?php echo $curr_id; ?>)">
                                <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                            </a>
                        </li>
                    </ul>
                    <?php
                    include "actions/item_actions.php";

                elseif ($trash_list): ?>
                    <ul class="item-funcs col-md-6 right">
                        <input type="hidden" class="post_id" name="post_id" value="<?= $curr_id ?>">
                        <?php include "edit_btns_trash.php"; ?>
                    </ul>
                <?php endif; ?>
            </div>

        </div>
    </div>
 </div>