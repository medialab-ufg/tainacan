<?php
include_once("js/actions_js.php");

if( is_null($curr_id) && is_null($itemURL) ) {
    $is_single_page = true;
    $curr_id = $object->ID;
    $itemURL = get_the_permalink($collection_id) . '?item=' . $object->post_name;
}

$checkout = [
    "out" => "do_checkout('". $curr_id ."')",
    "in" => "do_checkin('". $curr_id ."')",
    "discard" => "discard_checkout('". $curr_id ."')",
];

$rdfURL = $itemURL . '.rdf';
$itemDelete = [ 'id' => $curr_id, 'title' =>  _t('Delete Object'), 'time' => mktime(),
  'text' => _t('Are you sure to remove the object: ') . get_the_title() ];

if($is_single_page) {
    function set_single($func) {
        return "single_" . $func;
    }

    $checkout = array_map("set_single", $checkout);
}
?>

<?php if($is_single_page): ?>
    <ul class="item-funcs right">
        <input type="hidden" class="post_id" name="post_id" value="<?= $curr_id ?>">
        <li>
            <a id="modal_network<?php echo $curr_id; ?>" onclick="showModalShareNetwork(<?php echo $curr_id; ?>)">
                <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
            </a>
        </li>
    </ul>
<?php endif; ?>

<ul class="nav navbar-bar navbar-right item-menu-container">
    <li class="dropdown open_item_actions" id="action-<?php echo $curr_id; ?>">
        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
            <?php echo ViewHelper::render_icon("config", "png", _t('Item options')); ?>
        </a>
        <ul class="dropdown-menu pull-right dropdown-show new-item-menu" role="menu" id="item-menu-options">
            <?php if(!$is_single_page): ?>
                <li class="collec-only"> <a class="ac-view-item" href="<?php echo $itemURL; ?>"> <?php _t('View Item',1); ?> </a> </li>
            <?php
            endif;

            if($is_single_page) {
                if ($collection_metas == 'allowed' || ($collection_metas == 'moderate' && is_user_logged_in()) || ($collection_metas == 'controlled' && ($is_moderator || $object->post_author == get_current_user_id()))) {
                    if ($metas['socialdb_object_dc_type'][0] == 'image') {
                        $url_image = wp_get_attachment_url(get_post_thumbnail_id($object->ID, 'full'));
                        $thumbail_id = get_post_thumbnail_id($object->ID, 'full'); ?>
                        <li>
                            <a href="<?php echo $url_image; ?>" download="<?php echo $object->post_title; ?>.jpg" onclick="downloadItem('<?php echo $thumbail_id; ?>');">
                                <?php _t('Download item file', 1); ?>
                            </a>
                        </li>
                        <?php
                    }
                }
            }
            ?>

            <li> <a class="ac-open-file"> <?php _t('Print item',1); ?> </a> </li>

            <?php if ($is_moderator || get_post($curr_id)->post_author == get_current_user_id()): ?>
                <li>
                    <?php if( has_filter('show_edit_default') && apply_filters('show_edit_default', $collection_id) ) { ?>
                        <a onclick="edit_object('<?php echo $curr_id; ?>')"> <?php _t('Edit item',1); ?> </a>
                    <?php } else { ?>
                        <a id="edit_button_<?php echo $curr_id; ?>" onclick="edit_object_item('<?php echo $curr_id ?>')">
                            <?php _t('Edit item',1); ?>
                        </a>
                    <?php } ?>
                </li>
                <li> <a class="ac-duplicate-item" data-op="same"> <?php _t('Duplicate in this collection',1); ?> </a> </li>
                <li> <a class="ac-duplicate-item" data-op="other"> <?php _t('Duplicate in other collection',1); ?> </a> </li>
            <?php
            else:
                if (verify_allowed_action($collection_id, 'socialdb_collection_permission_delete_object')): ?>
                <li>
                    <a onclick="show_report_abuse('<?php echo $curr_id ?>')" href="javascript:void(0)" class="report_abuse">
                        <?php _t('Report Abuse',1); ?>
                    </a>
                </li>
                <?php
                endif;
            endif; ?>

            <?php if ($is_moderator || get_post($curr_id)->post_author == get_current_user_id()):
                $has_checked_in = get_post_meta($curr_id, 'socialdb_object_checkout', true);
                if(is_numeric($has_checked_in)) { ?>
                    <?php /*
                        <li> <a class="ac-checkin" onclick="do_checkin('<?php echo $curr_id ?>')"> <?php _t('Check-in',1); ?> </a> </li>
                        <li> <a class="ac-discard-checkout" onclick="discard_checkout('<?php echo $curr_id ?>')"> <?php _t('Discard Check-out',1); ?> </a> </li>
                    */ ?>
                    <li> <a class="ac-checkin" onclick="<?php echo $checkout['in'] ?>"> <?php _t('Check-in',1); ?> </a> </li>
                    <li> <a class="ac-discard-checkout" onclick="<?php echo $checkout['discard'] ?>"> <?php _t('Discard Check-out',1); ?> </a> </li>
                <?php } else { ?>
                    <li> <a class="ac-checkout" onclick="do_checkout(<?php echo $curr_id?>)"> <?php _t('Check-out old',1); ?> </a> </li>
                    <li> <a class="ac-checkout" onclick="<?php echo $checkout['out'] ?>"> <?php _t('Check-out',1); ?> </a> </li>
                <?php } ?>

                <li> <a class="ac-create-version"> <?php _t('Create new version',1); ?> </a> </li>
            <?php endif; ?>

            <li> <a class="ac-item-versions"> <?php _t('Item versions',1); ?> </a> </li>

            <li> <a class="ac-item-rdf" href="<?php echo $rdfURL; ?>" target="_blank"> <?php _t('Export RDF',1); ?> </a> </li>

            <?php if($is_single_page): ?>
                <li> <a class="ac-item-graph" onclick="showGraph('<?php echo $rdfURL; ?>')"> <?php _t('See graph',1); ?> </a> </li>
            <?php endif; ?>

            <?php if(!$is_single_page): ?>
                <li class="collec-only"> <a class="ac-comment-item"> <?php _t('Comment item',1); ?> </a> </li>
            <?php endif; ?>

            <?php if ($is_moderator || get_post($curr_id)->post_author == get_current_user_id()): ?>
                <li>
                    <a class="ac-exclude-item"
                       onclick="delete_object('<?php echo $itemDelete['title']; ?>','<?php echo $itemDelete['text']; ?>','<?php echo $itemDelete['id']; ?>','<?php echo $itemDelete['time']; ?>')">
                        <?php _t('Exclude item',1); ?>
                    </a>
                </li>
            <?php endif; ?>

        </ul>
    </li>
</ul>
