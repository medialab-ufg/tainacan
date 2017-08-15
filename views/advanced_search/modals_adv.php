<?php
$curr_id = get_the_ID();
$curr_time = time();
$root_id = get_option('collection_root_id');
$collection_id = $viewHelper->helper_get_collection_by_object(get_the_ID())[0]->ID
/*
 * TAINACAN: modal para compartilhar o item
 *
 */
?>
<div class="modal fade modal-share-network" id="adv_modal_share_network<?php echo get_the_ID() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            
            <?php echo $viewHelper->render_modal_header('remove-sign', '<span class="glyphicon glyphicon-share"></span> ', __('Share', 'tainacan')); ?>

            <div class="modal-body">
                <form name="form_share_item<?php echo get_the_ID() ?>" id="form_share_item<?php echo get_the_ID() ?>" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo __('Post it on: ', 'tainacan'); ?><br>
                            <a target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo get_the_permalink($collection_id) . '?item=' . get_post(get_the_ID())->post_name; ?>&amp;text=<?php echo htmlentities(get_the_title()); ?>&amp;via=socialdb"><?php echo ViewHelper::render_icon('twitter-square', 'png', 'Twitter'); ?></a>&nbsp;
                            <a onclick="redirect_facebook('<?php echo get_the_ID() ?>');" href="#"><?php echo ViewHelper::render_icon('facebook-square', 'png', 'Facebook'); ?></a>&nbsp;
                            <a target="_blank" href="https://plus.google.com/share?url=<?php echo get_the_permalink($collection_id) . '?item=' . get_post(get_the_ID())->post_name; ?>"><?php echo ViewHelper::render_icon('googleplus-square', 'png', 'Google Plus'); ?></a>
                            <br><br>
                            <?php echo __('Link: ', 'tainacan'); ?>
                            <input type="text" id="link_object_share<?php echo get_the_ID() ?>" class="form-control" value="<?php echo get_the_permalink($collection_id) . '?item=' . get_post(get_the_ID())->post_name; ?>" />
                        </div>
                        <div class="col-md-6">
                            <?php echo __('Embed it: ', 'tainacan'); ?>
                            <textarea id="embed_object<?php echo get_the_ID() ?>" class="form-control" rows="5"><?php echo '<iframe width="1024" height="768" src="' . get_the_permalink($collection_id) . '?item=' . get_post(get_the_ID())->post_name . '" frameborder="0"></iframe>'; ?></textarea>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo __('Email: ', 'tainacan'); ?><br>
                            <input type="text" id="email_object_share<?php echo get_the_ID() ?>" class="form-control" /><br>
                            <?php echo __('Share in other collection: ', 'tainacan'); ?><br>
                            <input type="text" id="collections_object_share<?php echo get_the_ID() ?>" class="form-control autocomplete_share_item" >
                            <input type="hidden" name="collection_id" id="collections_object_share<?php echo get_the_ID() ?>_id"  >
                            <input type="hidden" name="collection_id" id="collections_object_share<?php echo get_the_ID() ?>_url"  >
                        </div>
                    </div>
                </form>
            </div>

            <?php echo $viewHelper->render_modal_footer("send_share_item(\"$curr_id\")", __('Send', 'tainacan')); ?>

        </div>
    </div>
</div>

<?php
if (get_option('collection_root_id') != $collection_id):
    if( ! $is_moderator || get_post(get_the_ID())->post_author != get_current_user_id() ): ?>

        <?php
        /*
         * TAINACAN: modal padrao bootstrap para reportar abuso
         */
        $abuse_title = __('Report Abuse', 'tainacan');
        $abuse_text = __('Are you sure to remove the object: ', 'tainacan') . get_the_title();
        $curr_time = mktime();
        ?>
        <div class="modal fade" id="modal_delete_object<?php echo get_the_ID() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <?php echo $viewHelper->render_modal_header('remove-sign', '<span class="glyphicon glyphicon-trash"></span> ', $abuse_title); ?>

                    <div class="modal-body">
                        <?php echo __('Describe why the object: ', 'tainacan') . get_the_title() . __(' is abusive: ', 'tainacan'); ?>
                        <textarea id="observation_delete_object<?php echo get_the_ID() ?>" class="form-control"></textarea>
                    </div>

                    <?php echo $viewHelper->render_modal_footer("report_abuse_object(\"$abuse_title\", \"$abuse_text\", \"$curr_id\", \"$curr_time\")", __('Delete', 'tainacan')); ?>

                </div>
            </div>
        </div>

    <?php endif;
else: ?>

    <?php
    /*
     * TAINACAN: modal padrao bootstrap para reportar abuso
     */
    $abuse_title = __('Delete Collection', 'tainacan');
    $abuse_text = __('Are you sure to remove the collection: ', 'tainacan') . get_the_title();
    ?>
    <div class="modal fade" id="modal_delete_object<?php echo get_the_ID() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                
                <?php echo $viewHelper->render_modal_header('remove-sign', '<span class="glyphicon glyphicon-trash"></span> ', $abuse_title); ?>
                
                <div class="modal-body">
                    <?php echo __('Describe why the collection: ', 'tainacan') . get_the_title() . __(' is abusive: ', 'tainacan'); ?>
                    <textarea id="observation_delete_collection<?php echo get_the_ID() ?>" class="form-control"></textarea>
                </div>

                <?php echo $viewHelper->render_modal_footer("report_abuse_collection(\"$abuse_title\", \"$abuse_text\", \"$curr_id\", \"$curr_time\", \"$root_id\")", __('Delete', 'tainacan')); ?>

            </div>
        </div>
    </div>
<?php endif; ?>