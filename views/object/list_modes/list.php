<div class="col-md-12 no-padding list-view-container top-div" <?php if ($collection_list_mode != "list"): ?> style="display: none" <?php endif ?> >
    <div class="col-md-1 item-thumb">
        <a href="<?php echo get_collection_item_href($collection_id); ?>"
           onclick="<?php get_item_click_event($collection_id, get_the_ID() )?>">
            <?php echo get_item_thumb_image(get_the_ID()); ?>
        </a>
    </div>
    <div class="col-md-3 no-padding">
        <h4 class="item-display-title">
            <a href="<?php echo get_collection_item_href($collection_id); ?>" style="color: black; font-weight: bolder"
               onclick="<?php get_item_click_event($collection_id, get_the_ID() )?>">
                <?php the_title(); ?>
            </a>
        </h4>
    </div>
    <div class="col-md-3">
        <div class="item-description"><?php echo wp_trim_words(get_the_content(), 15); ?></div>
    </div>

    <div class="col-md-3">
        <div class="item-author"><?php echo "<strong>" . __('Created by: ', 'tainacan') . "</strong>" . get_the_author(); ?></div>
        <div class="item-creation"><?php echo "<strong>" . __('Created at: ', 'tainacan') . "</strong>" . get_the_date('d/m/Y'); ?></div>
    </div>

    <div class="col-md-2">
        <?php if (get_option('collection_root_id') != $collection_id): ?>
            <button id="show_rankings_list_<?php echo get_the_ID() ?>" onclick="show_value_ordenation('<?php echo get_the_ID() ?>', '#rankings_list_', '#show_rankings_list_')"
                    class="btn btn-default"><?php _e('Show rankings', 'tainacan'); ?></button>

            <div class="editing-item">
                <!-- TAINACAN: container(AJAX) que mostra o html com os rankings do objeto-->
                <div id="rankings_list_<?php echo get_the_ID() ?>" class="rankings-container"></div>

                <ul class="item-funcs col-md-5 right">
                    <!-- TAINACAN: hidden com id do item -->
                    <input type="hidden" class="post_id" name="post_id" value="<?= get_the_ID() ?>">

                    <?php if (get_option('collection_root_id') != $collection_id): ?>
                    <?php else: ?>
                        <!-- TAINACAN: mostra o modal da biblioteca sweet alert para exclusao de uma colecao -->
                        <?php if ($is_moderator || get_post(get_the_ID())->post_author == get_current_user_id()): ?>
                        <?php else: ?>
                            <!-- TAINACAN:  modal padrao bootstrap para reportar abuso -->
                            <div class="modal fade" id="modal_delete_object<?php echo get_the_ID() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Report Abuse', 'tainacan'); ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php echo __('Describe why the collection: ', 'tainacan') . get_the_title() . __(' is abusive: ', 'tainacan'); ?>
                                            <textarea id="observation_delete_collection<?php echo get_the_ID() ?>" class="form-control"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                                            <button onclick="report_abuse_collection('<?php _e('Delete Collection', 'tainacan') ?>', '<?php _e('Are you sure to remove the collection: ', 'tainacan') . get_the_title() ?>', '<?php echo get_the_ID() ?>', '<?= mktime() ?>', '<?php echo get_option('collection_root_id') ?>')" type="button" class="btn btn-primary"><?php echo __('Delete', 'tainacan'); ?></button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>

            </div> <!-- .editing-item -->

                   <!-- TAINACAN: script para disparar o evento que mostra os rankings -->
            <script>
                $('#show_rankings_list_<?php echo get_the_ID() ?>').hide().trigger('click');
            </script>
        <?php endif; ?>
    </div>
</div>
