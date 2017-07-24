<?php
$_show_edit_buttons = true;
$item_context = "collection";

if( has_filter('tainacan_show_restore_options') ) {
    $_show_edit_buttons = apply_filters('tainacan_show_restore_options', $collection_id);
}
if($_show_edit_buttons) {
     if (get_option('collection_root_id') != $collection_id):
         if ($is_moderator || get_post($curr_id)->post_author == get_current_user_id())
             $item_context = "object";
    endif;

    $confirm_text = _t(sprintf("Are you sure to remove the %s permanently: ", $item_context));
    $del = ['title' => _t('Delete Object'), 'text' => $confirm_text . get_the_title() ];
    ?>
    <li class="remove-permanent">
        <a onclick="delete_permanently_object('<?= $del['title'] ?>', '<?= $del['text'] ?>', '<?php echo $curr_id ?>')" href="javascript:void(0)" class="remove">
            <span class="glyphicon glyphicon-trash"></span>
        </a>
    </li>
    <li class="restore-item">
        <a onclick="restore_object('<?php echo $curr_id ?>')"> <span class="glyphicon glyphicon-retweet"></span> </a>
    </li>
<?php } ?>