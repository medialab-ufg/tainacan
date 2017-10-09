<?php
include_once("js/actions_js.php");

if(!isset($collection_id))
    $collection_id = $post->post_parent;

if(!isset($curr_id))
    $curr_id = get_the_ID();

if(!isset($is_single_page))
    $is_single_page = is_single();

if(!isset($itemURL))
    $itemURL = get_the_permalink($curr_id);

if(!isset($collection_metas))
    $collection_metas = get_post_meta($collection_id, 'socialdb_collection_download_control', true);

if(!isset($is_moderator))
    $is_moderator = CollectionModel::is_moderator($collection_id, get_current_user_id());

$rdfURL = $itemURL . '.rdf';
$checkout = [ "out" => "do_checkout('". $curr_id ."')",
    "in" => "do_checkin('". $curr_id ."')",
    "discard" => "discard_checkout('". $curr_id ."')" ];

$itemDelete = [
    'id' => $curr_id,
    'title' =>  _t('Delete Object'),
    'time' => time(),
    'text' => _t('Are you sure to remove the object: ') . get_the_title() ];

if($is_single_page) {
    function set_single($func) {
        return "single_" . $func;
    }

    $checkout = array_map("set_single", $checkout);
}

$is_repo_admin = current_user_can('administrator');
$is_current_user_the_author = get_post($curr_id)->post_author == get_current_user_id();
?>

<?php if($is_single_page): ?>
<ul class="item-funcs right">
        <input type="hidden" class="post_id" name="post_id" value="<?= $curr_id ?>">
        <li>
            <a id="modal_network<?php echo $curr_id; ?>" onclick="open_share_modal(<?php echo $curr_id; ?>)">
                <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
            </a>
        </li>
    </ul>
<?php endif; ?>

<ul class="nav navbar-bar navbar-right item-menu-container"  <?php if(has_action('hide_actions_item')) do_action('hide_actions_item') ?> >
    <li class="dropdown open_item_actions" id="action-<?php echo $curr_id; ?>">
        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
            <span class="dashicons dashicons-admin-generic main-color"></span>
        </a>
        <ul class="dropdown-menu pull-right dropdown-show new-item-menu" role="menu" id="item-menu-options">
            <?php if(!$is_single_page): ?>
                <li class="collec-only"> <a class="ac-view-item" href="<?php echo $itemURL; ?>"> <?php _t('View Item',1); ?> </a> </li>
            <?php
            endif;

            if($is_single_page) {
                if ($collection_metas == 'allowed' || ($collection_metas == 'moderate' && is_user_logged_in()) || ($collection_metas == 'controlled' && ($is_moderator || $object->post_author == get_current_user_id()))) {
                    $thumb_id = get_post_thumbnail_id($curr_id, 'full');
                    if ($metas['socialdb_object_dc_type'][0] == 'image') {
                        $url_image = wp_get_attachment_url(get_post_thumbnail_id($curr_id, 'full'));
                         ?>
                        <li>
                            <a href="<?php echo $url_image; ?>" download="<?php echo $object->post_title; ?>.jpg" onclick="downloadItem('<?php echo $thumb_id; ?>');">
                                <?php _t('Download item file', 1); ?>
                            </a>
                        </li>
                        <?php
                    } else if($metas['socialdb_object_dc_type'][0] == 'pdf') {
                        if ($metas['socialdb_object_from'][0] == 'internal' && wp_get_attachment_url($metas['socialdb_object_content'][0])) {
                            $_file_url_ = wp_get_attachment_url($metas['socialdb_object_content'][0]); ?>
                            <li><a href="<?php echo $_file_url_?>" download> <?php _t('Download item file', 1); ?> </a> </li>
                        <?php }
                    }
                }
            }
            ?>

            <li> <a class="ac-open-file"> <?php _t('Print item',1); ?> </a> </li>

            <?php if($is_repo_admin): ?>
                <li class="tainacan-museum-clear">
                    <a href="javascript:void(0)" class="change-owner" data-item="<?php echo $curr_id; ?>"><?php _t('Change item owner',1); ?></a>
                </li>
            <?php endif; ?>

            <?php
            if(has_filter('tainacan_show_restore_options'))
            {
                $show = apply_filters('tainacan_show_restore_options', $collection_id);
            }else $show = true;

            if (($is_moderator || get_post($curr_id)->post_author == get_current_user_id()) && $show): ?>
                <li>
                    <?php if( has_filter('show_edit_default') && apply_filters('show_edit_default', $collection_id) ) { ?>
                        <a onclick="edit_object('<?php echo $curr_id; ?>')"> <?php _t('Edit item',1); ?> </a>
                    <?php } else { ?>
                        <?php if(hasHelper($curr_id)):
                            // $edit_link =  get_the_permalink($collection_id).get_post($curr_id)->post_name.'/editar';
                            $edit_link =  site_url() . '/item/' . get_post($curr_id)->post_name.'/editar';
                            ?>
                        <a href="<?php echo $edit_link; ?>">
                            <?php _t('Edit item',1); ?>
                        </a>
                        <?php else: ?>
                        <a id="edit_button_<?php echo $curr_id; ?>" onclick="edit_object_item('<?php echo $curr_id ?>')">
                            <?php _t('Edit item',1); ?>
                        </a>
                        <?php endif; ?>
                    <?php } ?>
                </li>
                <li class="tainacan-museum-clear"> <a class="ac-duplicate-item" data-op="same"> <?php _t('Duplicate in this collection',1); ?> </a> </li>
                <li class="tainacan-museum-clear"> <a class="ac-duplicate-item" data-op="other"> <?php _t('Duplicate in other collection',1); ?> </a> </li>
            <?php
            else:
                if (verify_allowed_action($collection_id, 'socialdb_collection_permission_delete_object')): ?>
                <li class="tainacan-museum-clear">
                    <a onclick="show_report_abuse('<?php echo $curr_id ?>')" href="javascript:void(0)" class="report_abuse">
                        <?php _t('Report Abuse',1); ?>
                    </a>
                </li>
                <?php
                endif;
            endif; ?>

            <?php if ($is_moderator || $is_current_user_the_author):
                $has_checked_in = get_post_meta($curr_id, 'socialdb_object_checkout', true);
                if(is_numeric($has_checked_in)) { ?>
                    <li class="tainacan-museum-clear"> <a class="ac-checkin" onclick="<?php echo $checkout['in'] ?>"> <?php _t('Check-in',1); ?> </a> </li>
                    <li class="tainacan-museum-clear"> <a class="ac-discard-checkout" onclick="<?php echo $checkout['discard'] ?>"> <?php _t('Discard Check-out',1); ?> </a> </li>
                <?php } else { ?>
                    <li class="tainacan-museum-clear"> <a class="ac-checkout" onclick="<?php echo $checkout['out'] ?>"> <?php _t('Check-out',1); ?> </a> </li>
                <?php } ?>

                <li class="tainacan-museum-clear"> <a class="ac-create-version"> <?php _t('Create new version',1); ?> </a> </li>
            <?php endif; ?>

            <li class="tainacan-museum-clear"> <a class="ac-item-versions"> <?php _t('Item versions',1); ?> </a> </li>

            <li class="tainacan-museum-clear"> <a class="ac-item-rdf" href="<?php echo $rdfURL; ?>" target="_blank"> <?php _t('Export RDF',1); ?> </a> </li>

            <?php if($is_single_page): ?>
                <li class="tainacan-museum-clear"> <a class="ac-item-graph" onclick="showGraph('<?php echo $rdfURL; ?>')"> <?php _t('See graph',1); ?> </a> </li>
            <?php endif;

            if(!$is_single_page): ?>
                <li class="collec-only tainacan-museum-clear">
                    <a class="ac-comment-item"> <?php _t('Comment item',1); ?> </a>
                </li>
            <?php
            endif;

            if( has_filter('tainacan_show_reason_modal') && ! apply_filters("tainacan_show_restore_options", $collection_id)
                && ($is_repo_admin || $is_current_user_the_author) ) {
            ?>
                <li>
                    <a class="ac-exclude-item"
                       onclick="show_reason_modal(<?php echo $itemDelete['id']; ?>)">
                        <?php echo (has_filter('alter_label_exclude') && has_filter('tainacan_show_reason_modal')) ? apply_filters("alter_label_exclude", $collection_id) : _t('Excluded item',1);?>
                    </a>
                </li>

                <li style="display: none">
                    <a class="ac-exclude-item" id="<?php echo $itemDelete['id']; ?>"
                       onclick="delete_object_no_confirmation('<?php echo $itemDelete['id']; ?>','<?php echo $itemDelete['time']; ?>');">
                        <?php _t('Exclude item',1);?>
                    </a>
                </li>
                <?php
            }
            else if ($is_moderator || $is_current_user_the_author || verify_allowed_action($collection_id, 'socialdb_collection_permission_delete_object')): ?>
                <li>
                    <a class="ac-exclude-item"
                       onclick="delete_object('<?php echo $itemDelete['title']; ?>','<?php echo $itemDelete['text']; ?>','<?php echo $itemDelete['id']; ?>','<?php echo $itemDelete['time']; ?>')">
                        <?php _t('Exclude item',1);?>
                    </a>
                </li>
            <?php endif; ?>

        </ul>
    </li>
</ul>