<div class="droppableClassifications col-md-12 no-padding list-view-container top-div toggleSelect" 
    data-order="<?php echo $countLine; ?>"
    <?php if ($collection_list_mode != "list"): ?> style="display: none" <?php endif ?> >
    
    <input type="hidden" id="add_classification_allowed_<?php echo $curr_id ?>" 
            name="add_classification_allowed"
            value="<?php echo (string) verify_allowed_action($collection_id, 'socialdb_collection_permission_add_classification', $curr_id); ?>" />
    
    <div>
        <div class="col-md-1 item-thumb">
            <a href="<?php echo get_collection_item_href($collection_id); ?>"
               onclick="<?php get_item_click_event($collection_id, $curr_id )?>">
                <?php echo get_item_thumb_image($curr_id); ?>
            </a>
        </div>
        <div class="col-md-4 no-padding">
            <h4 class="item-display-title">
                <a href="<?php echo get_collection_item_href($collection_id); ?>" style="color: black; font-weight: bolder"
                   onclick="<?php get_item_click_event($collection_id, $curr_id )?>">
                    <?php the_title(); ?>
                </a>
            </h4>
        </div>

        <div class="col-md-3 author-created">
            <div class="item-author"><?php echo "<strong>" . __('Created by: ', 'tainacan') . "</strong>" . get_the_author(); ?></div>
            <div class="item-creation"><?php echo $curr_date ?></div>
        </div>

        <div class="col-md-2 no-padding">
            <?php if (get_option('collection_root_id') != $collection_id): ?>
                <!-- TAINACAN: container(AJAX) que mostra o html com os rankings do objeto-->
                <?php if(has_action('container_rankings_list')): ?>
                    <?php do_action('container_rankings_list',$curr_id) ?>
                <?php else: ?>
                        <div id="r_list_<?php echo $curr_id ?>" class="rankings-container"></div> 
                <?php endif; ?>  
            <?php endif; ?>
        </div>

        <div class="col-md-2">
            <ul class="item-funcs right">
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
