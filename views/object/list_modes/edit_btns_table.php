<?php include 'js/edit_btns_js.php'; ?>
<?php if (get_option('collection_root_id') != $collection_id): ?>
    <!--------------------------- DELETE AND EDIT OBJECT------------------------------------------------>
    <?php if ($is_moderator || get_post($curr_id)->post_author == get_current_user_id()): ?>
    <li class="dropdown" >
            <?php
            if (has_filter('show_edit_default') && apply_filters('show_edit_default', $data['collection_id'])) {
                ?>
               <a style="cursor: pointer;"   onclick="edit_object('<?php echo $curr_id ?>')">
                    <?php
                } else {
                    $has_checked_in = get_post_meta( $curr_id ,'socialdb_object_checkout', true);
                    ?>
                        <?php if(!hasHelper($curr_id)): ?>
                         <a id="edit_button_<?php echo $curr_id ?>" style="cursor: pointer;"
                           data-toggle="dropdown" role="button" aria-expanded="false"
                            onclick="edit_object_item('<?php echo $curr_id ?>')">
                         <?php else: ?>    
                         <a id="edit_button_<?php echo $curr_id ?>" style="cursor: pointer;"
                           data-toggle="dropdown" role="button" aria-expanded="false"
                            href="<?php echo get_the_permalink($collection_id).get_post($curr_id)->post_name.'/editar'; ?>">   
                         <?php endif; ?>    
                    <?php } ?>
                    <span class="glyphicon glyphicon-edit"></span>
                   </a>
                   <?php if(is_numeric($has_checked_in)): ?>
                    <ul class="dropdown-menu dropdown-hover-show" style="position: absolute;top:-140%;width:50%;left:-1030%;" role="menu" >
                             <li><button style="position: relative;right: 10%;" onclick="discard_checkout('<?php echo $curr_id ?>')" class="btn btn-primary">Discard Checkout</button></li>
                             <li><button style="position: relative;right: 50%;  margin-top: 3%;" onclick="do_checkin('<?php echo $curr_id ?>')" class="btn btn-primary">Checkin</button></li>
                    </ul>
                   <?php else: ?>
                    <ul class="dropdown-menu dropdown-hover-show" style="position: absolute;top:-10%;left:-1030%;" role="menu" >
                             <li ><button style="position: relative;right: -50%;"  onclick="do_checkout('<?php echo $curr_id ?>')" class="btn btn-primary pull-left">Checkout</button></li>
                    </ul>
                   <?php endif; ?>
                    
        </li>
        <li>
            <a onclick="delete_object('<?= __('Delete Object', 'tainacan') ?>', '<?= __('Are you sure to remove the object: ', 'tainacan') . get_the_title() ?>', '<?php echo $curr_id ?>', '<?= mktime() ?>')" style="cursor: pointer;" class="remove">
                <span class="glyphicon glyphicon-trash"></span>
            </a>
        </li>
        <?php
    else:
        // verifico se eh oferecido a possibilidade de remocao do objeto vindulado
        if (verify_allowed_action($collection_id, 'socialdb_collection_permission_delete_object')):
            ?>
            <li>
                <a onclick="show_report_abuse('<?php echo $curr_id ?>')" href="#" class="report_abuse">
                    <span class="glyphicon glyphicon-warning-sign"></span>
                </a>
            </li>
            <?php
        endif;
    endif; // if is not moderator
else: // if is not the root collection
    if ($is_moderator || get_post(get_the_ID())->post_author == get_current_user_id()):
        ?>
        <li>
            <!-- TAINACAN: mostra o modal da biblioteca sweet alert para exclusao de uma colecao -->
            <a onclick="delete_collection('<?= __('Delete Object', 'tainacan') ?>', '<?= __('Are you sure to remove the collection: ', 'tainacan') . get_the_title() ?>', '<?php echo $curr_id ?>', '<?= mktime() ?>', '<?php echo get_option('collection_root_id') ?>')" href="#" class="remove">
                <span class="glyphicon glyphicon-trash"></span>
            </a>
        </li>
    <?php else: ?>
        <li>
            <!-- TAINACAN: mostra o modal para reportar abusao em um item, gerando assim um evento -->
            <a onclick="show_report_abuse('<?php echo $curr_id ?>')" href="#" class="report_abuse">
                <span class="glyphicon glyphicon-warning-sign"></span>
            </a>
        </li>
    <?php endif;
endif;
?>
<li>
    <!-- TAINACAN: mostra o modal para reportar abusao em um item, gerando assim um evento -->
    <a onclick="show_duplicate_item('<?php echo $curr_id ?>')" href="#" class="duplicate_item">
        <span class="glyphicon glyphicon-copy"></span>
    </a>
</li>