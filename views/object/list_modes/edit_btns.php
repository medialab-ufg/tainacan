<?php if (get_option('collection_root_id') != $collection_id): ?>
    <!--------------------------- DELETE AND EDIT OBJECT------------------------------------------------>
    <?php if ($is_moderator || get_post($curr_id)->post_author == get_current_user_id()): ?>
        <li>
            <a style="cursor: pointer;" onclick="edit_object_item('<?php echo $curr_id ?>')">
                <span class="glyphicon glyphicon-edit"></span>
            </a>
        </li>
        <li>
            <a onclick="delete_object('<?= __('Delete Object', 'tainacan') ?>', '<?= __('Are you sure to remove the object: ', 'tainacan') . get_the_title() ?>', '<?php echo $curr_id ?>', '<?= mktime() ?>')" style="cursor: pointer;" class="remove">
                <span class="glyphicon glyphicon-trash"></span>
            </a>
        </li>
    <?php
    else:
        // verifico se eh oferecido a possibilidade de remocao do objeto vindulado
        if (verify_allowed_action($collection_id, 'socialdb_collection_permission_delete_object')): ?>
            <li>
                <a onclick="show_report_abuse('<?php echo $curr_id ?>')" href="#" class="report_abuse">
                    <span class="glyphicon glyphicon-warning-sign"></span>
                </a>
            </li>
    <?php
        endif;
    endif; // if is not moderator
else: // if is not the root collection
    if ($is_moderator || get_post(get_the_ID())->post_author == get_current_user_id()): ?>
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
endif; ?>