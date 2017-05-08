<?php
    $additional_curr_class = "";
    if ( ($countLine % 4) == 1 ) { 
        $additional_curr_class = "first-el";
    } else if( ($countLine % 4) == 0 ) {
        $additional_curr_class = "last-el";
    }    
?>

<div class="col-md-3 gallery-view-container top-div <?php echo $additional_curr_class ?>"   
     data-order="<?php echo $countLine; ?>"  
     <?php if ($collection_list_mode != "gallery"): ?> style="display: none" <?php endif ?> >
    
    <input type="hidden" id="add_classification_allowed_<?php echo get_the_ID() ?>" name="add_classification_allowed" value="<?php echo (string) verify_allowed_action($collection_id, 'socialdb_collection_permission_add_classification', get_the_ID()); ?>" />
    <div class="gallery-wrapper droppableClassifications toggleSelect">
        <div class="item-thumb">
            <a href="<?php echo get_collection_item_href($collection_id); ?>"
               onclick="<?php get_item_click_event($collection_id, $curr_id )?>">
                <?php echo get_item_thumb_image($curr_id); ?>
            </a>
        </div>

        <div class=" title-container">
            <h5 class="item-display-title">
                <a href="<?php echo get_collection_item_href($collection_id); ?>"
                   onclick="<?php get_item_click_event($collection_id, $curr_id )?>">
                    <?php echo wp_trim_words( get_the_title(), 4 ); ?>
                </a>
            </h5>

            <div class="gallery-metadata">
                <div class="col-md-5 no-padding">
                    <?php if (get_option('collection_root_id') != $collection_id): ?>                
                        <!-- TAINACAN: container(AJAX) que mostra o html com os rankings do objeto-->
                        <?php if(has_action('container_rankings_gallery')): ?>
                            <?php do_action('container_rankings_gallery',$curr_id) ?>
                        <?php else: ?>
                            <div id="r_gallery_<?php echo $curr_id ?>" class="rankings-container"></div>
                        <?php endif; ?>                    
                    <?php endif; ?>
                </div>
                <ul class="item-funcs col-md-6 right">
                    <input type="hidden" class="post_id" name="post_id" value="<?= $curr_id ?>">
                    <li class="tainacan-museum-clear">
                        <a id="modal_network<?php echo $curr_id; ?>" onclick="showModalShareNetwork(<?php echo $curr_id; ?>)">
                            <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                        </a>
                    </li>

                    <?php // include "edit_btns.php"; ?>                    
                </ul>
                
                <?php include "actions/item_actions.php"; ?>

            </div>         
        </div>
        
    </div>
 </div>