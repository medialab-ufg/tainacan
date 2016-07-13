<?php $curr_id = get_the_ID();  ?>
<div class="col-md-12 no-padding list-view-container top-div" <?php if ($collection_list_mode != "list"): ?> style="display: none" <?php endif ?> >
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
        <div class="item-creation"><?php echo "<strong>" . __('Created at: ', 'tainacan') . "</strong>" . get_the_date('d/m/Y'); ?></div>
    </div>

    <div class="col-md-2 no-padding">
        <?php if (get_option('collection_root_id') != $collection_id): ?>
            <button id="show_rankings_list_<?php echo $curr_id ?>" onclick="show_value_ordenation('<?php echo $curr_id ?>', '#rankings_list_', '#show_rankings_list_')"
                    class="btn btn-default"><?php _e('Show rankings', 'tainacan'); ?></button>
            
            <!-- TAINACAN: container(AJAX) que mostra o html com os rankings do objeto-->
            <div id="rankings_list_<?php echo $curr_id ?>" class="rankings-container"></div>
        <?php endif; ?>
    </div>

    <div class="col-md-2">

        <ul class="item-funcs right">
            <input type="hidden" class="post_id" name="post_id" value="<?= $curr_id ?>">
            <li>
                <a id="modal_network<?php echo $curr_id; ?>" onclick="showModalShareNetwork(<?php echo $curr_id; ?>)">
                    <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                </a>
            </li>

            <?php include "edit_btns.php"; ?>
            
        </ul>
        
        <script>
            $('#show_rankings_list_<?php echo $curr_id ?>').hide().trigger('click');
        </script>
        
    </div>
</div>
