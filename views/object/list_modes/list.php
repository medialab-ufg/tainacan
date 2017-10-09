<div class="droppableClassifications col-md-12 no-padding list-view-container top-div toggleSelect"
    data-order="<?php echo $countLine; ?>"
    <?php if ($collection_list_mode != "list"): ?> style="display: none" <?php endif ?> >
    
    <input type="hidden" id="add_classification_allowed_<?php echo $curr_id ?>" name="add_classification_allowed"
            value="<?php echo (string) verify_allowed_action($collection_id, 'socialdb_collection_permission_add_classification', $curr_id); ?>" />
    
    <div>
        <div class="col-md-1 item-thumb">
            <?php if(empty($trash_list)): ?>
                <a href="<?php echo $itemURL; ?>">
                    <?php echo get_item_thumb_image($curr_id); ?>
                </a>
            <?php elseif ($trash_list): echo get_item_thumb_image($curr_id); endif; ?>
        </div>

        <div class="col-md-4 no-padding">
            <h4 class="item-display-title">
                <?php if(empty($trash_list)): ?>
                    <a href="<?php echo $itemURL; ?>">
                        <?php the_title(); ?>
                    </a>
                <?php elseif ($trash_list): the_title(); endif; ?>
            </h4>
        </div>

        <div class="col-md-3 author-created">
            <div class="item-author"><?php echo "<strong>" . __('Created by: ', 'tainacan') . "</strong>" . get_the_author(); ?></div>
            <div class="item-creation">
                <strong> <?php _t('Created at: ',1) ?> </strong> <?php echo get_the_date('d/m/Y'); ?>
            </div>
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
            <?php if(empty($trash_list)): ?>
                <ul class="item-funcs right">
                    <input type="hidden" class="post_id" name="post_id" value="<?= $curr_id ?>">
                    <li class="tainacan-museum-clear">
                        <a id="modal_network<?php echo $curr_id; ?>" onclick="showModalShareNetwork(<?php echo $curr_id; ?>)">
                            <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                        </a>
                    </li>
                </ul>
                <?php
                include "actions/item_actions.php";
            elseif ($trash_list):
                include "edit_btns_trash.php";
            endif; ?>
        </div>

    </div>
</div>
