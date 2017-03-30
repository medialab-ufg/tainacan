
<?php
$_show_edit_buttons = true;

if( has_filter('tainacan_show_restore_options') ) {
    $_show_edit_buttons = apply_filters('tainacan_show_restore_options', $collection_id);
}

if($_show_edit_buttons) { ?>

    <?php if (get_option('collection_root_id') != $collection_id): ?>
        <!--------------------------- DELETE AND RESTORE OBJECT------------------------------------------------>
        <?php if ($is_moderator || get_post($curr_id)->post_author == get_current_user_id()): ?>
            <li>
                <a onclick="delete_permanently_object('<?= __('Delete Object', 'tainacan') ?>', '<?= __('Are you sure to remove the object permanently: ', 'tainacan') . get_the_title() ?>', '<?php echo $curr_id ?>')"
                   style="cursor: pointer;" class="remove">
                    <span class="glyphicon glyphicon-trash"></span>
                </a>
            </li>
            <li>
                <a style="cursor: pointer;" onclick="restore_object('<?php echo $curr_id ?>')">
                    <span class="glyphicon glyphicon-retweet"></span>
                </a>
            </li>
            <?php
        endif; // if is not moderator
    else: // if is not the root collection
        if ($is_moderator || get_post(get_the_ID())->post_author == get_current_user_id()): ?>
            <li>
                <!-- TAINACAN: mostra o modal da biblioteca sweet alert para exclusao de uma colecao -->
                <a onclick="delete_permanently_object('<?= __('Delete Object', 'tainacan') ?>', '<?= __('Are you sure to remove the collection permanently: ', 'tainacan') . get_the_title() ?>', '<?php echo $curr_id ?>')"
                   href="#" class="remove">
                    <span class="glyphicon glyphicon-trash"></span>
                </a>
            </li>
            <li>
                <a style="cursor: pointer;" onclick="restore_object('<?php echo $curr_id ?>')">
                    <span class="glyphicon glyphicon-retweet"></span>
                </a>
            </li>
        <?php endif;
    endif;

}
?>