<?php
include_once("js/actions_js.php");
$itemDelete = [
    'id' => $curr_id, 'title' =>  _t('Delete Object'), 'time' => mktime(),
    'text' => _t('Are you sure to remove the object: ') . get_the_title()
];
?>
<ul class="nav navbar-bar navbar-right item-menu-container">
    <li class="dropdown open_item_actions" id="action-<?php echo $curr_id; ?>">
        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
            <?php echo ViewHelper::render_icon("config", "png", _t('Item options')); ?>
        </a>
        <ul class="dropdown-menu pull-right dropdown-show new-item-menu" role="menu" id="item-menu-options">
            <li> <a class="ac-view-item" href="<?php echo $itemURL; ?>"> <?php _t('View Item',1); ?> </a> </li>
            <?php /* <li> <a class="ac-open-file"> <?php _t('Open item file',1); ?> </a> </li> */ ?>

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
            <?php endif; ?>
            
            <?php if ($is_moderator || get_post($curr_id)->post_author == get_current_user_id()):
                $has_checked_in = get_post_meta($curr_id, 'socialdb_object_checkout', true);
                if(is_numeric($has_checked_in)){ ?>
                    <li> <a class="ac-checkin" onclick="do_checkin('<?php echo $curr_id ?>')"> <?php _t('Check-in',1); ?> </a> </li>
                    <li> <a class="ac-discard-checkout" onclick="discard_checkout('<?php echo $curr_id ?>')"> <?php _t('Discard Check-out',1); ?> </a> </li>
                <?php } else { ?>
                    <li> <a class="ac-checkout" onclick="do_checkout('<?php echo $curr_id ?>')"> <?php _t('Check-out',1); ?> </a> </li>
                <?php } ?>
                <li> <a class="ac-create-version"> <?php _t('Create new version',1); ?> </a> </li>
                <li> <a class="ac-item-versions"> <?php _t('Item versions',1); ?> </a> </li>
            <?php endif; ?>
            
            <li> <a class="ac-item-rdf" href="<?php echo $itemURL; ?>.rdf" target="_blank"> <?php _t('Export RDF',1); ?> </a> </li>
            <li> <a class="ac-item-graph"> <?php _t('See graph',1); ?> </a> </li>            
            <li> <a class="ac-comment-item"> <?php _t('Comment item',1); ?> </a> </li>

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