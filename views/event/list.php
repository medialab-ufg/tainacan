<?php
include_once ('../../helpers/view_helper.php');
include_once ('js/list_js.php');
if($collection_id == get_option('collection_root_id')){
    $url = false;
}else{
    $url = get_the_permalink($collection_id);
}
?>
<div id="events_title" class="col-md-12">
    <div class="col-md-12 config_default_style" id="events_settings">
        <?php ViewHelper::render_config_title( __("Events", 'tainacan'), $url ); ?>

        <div id="alert_success_categories" class="alert alert-success" style="display: none;">
            <button type="button" class="close" onclick="hide_alert();"><span aria-hidden="true">&times;</span></button>
            <?php _e('Operation was successful.', 'tainacan') ?>
        </div>
        <div id="alert_error_categories" class="alert alert-danger" style="display: none;">
            <button type="button" class="close" onclick="hide_alert();"><span aria-hidden="true">&times;</span></button>
            <?php _e('Error! Operation was unsuccessful.', 'tainacan') ?>&nbsp;<span id="message_category"></span>
        </div>

        <div class="events_menu">
            <div class="col-md-12">
                <div role="tabpanel">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a id="click_events_not_verified" href="#events_not_verified_tab" aria-controls="property_data_tab" role="tab" data-toggle="tab">
                                <?php _e('Events not verified', 'tainacan') ?><span>(<?php
                                    if (is_array($events_not_observed)): echo count($events_not_observed);
                                    else: echo '0';
                                    endif;
                                    ?>)</span>
                            </a>
                        </li>
                        <li role="presentation">
                            <a id="click_events_verified" href="#events_verified_tab" aria-controls="property_object_tab" role="tab" data-toggle="tab">
                                <?php _e('Events verified', 'tainacan') ?><span>(<?php
                                    if (is_array($events_observed)): echo count($events_observed);
                                    else: echo '0';
                                    endif;
                                    ?>)</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="events_not_verified_tab">
                        <div id="list_events_not_verified">
                            <?php if (isset($events_not_observed)) {     // se existir eventos a serem verificaos   ?>
                                <table id="event_not_verified_table" class="table table-striped table-bordered" cellspacing="0" width="100%">  <!--class="table table-bordered" style="background-color: #d9edf7;"-->
                                    <thead>
                                    <tr>
                                        <th><?php _e('Date', 'tainacan', 'tainacan'); ?></th>
                                        <th><?php _e('Event Type', 'tainacan'); ?></th>
                                        <th><?php _e('Event Description', 'tainacan'); ?></th>
                                        <th><?php _e('State', 'tainacan'); ?></th>
                                        <th><?php _e('Real Date', 'tainacan'); ?></th>
                                        <?php if ($moderation_type == 'democratico' && (current_user_can('manage_options') || verify_collection_moderators($collection_id, get_current_user_id()))): ?>
                                            <th>
                                                <a onclick="democratic_check_events()"><?php _e('Select all', 'tainacan'); ?></a>/
                                                <a onclick="democratic_uncheck_events()"><?php _e('Unselect all', 'tainacan'); ?></a>
                                                <button type="button" onclick="process_events_democratic()" class="btn btn-primary btn-xs pull-right"><?php _e('Process', 'tainacan'); ?></button>
                                            </th>
                                        <?php endif; ?>
                                    </tr>
                                    </thead>
                                    <tbody id="table_events_not_verified" >
                                    <?php foreach ($events_not_observed as $event) { ?>
                                        <tr>
                                            <td>
                                                <?php echo date("d/m/Y", $event['date']); ?>
                                            </td>
                                            <td>
                                                <?php echo $event['type']; ?>
                                            </td>
                                            <td>
                                                <?php if ((current_user_can('manage_options') || verify_collection_moderators($collection_id, get_current_user_id()))): ?>
                                                    <a style="cursor:pointer;" onclick="show_verify_event_not_confirmed('<?= $event['id'] ?>', '<?= $collection_id ?>')"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;<?php echo $event['name']; ?></a>
                                                <?php else: ?>
                                                    <a style="cursor:pointer;" onclick="show_unconfirmed_users_events('<?= $event['id'] ?>', '<?= $collection_id ?>')"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;<?php echo $event['name']; ?></a>
                                                <?php endif; ?>
                                            </td>
                                            <?php if ($moderation_type == 'moderador' || !isset($moderation_type) || empty($moderation_type)) { ?>
                                                <td>
                                                    <a style="cursor:pointer;" onclick="show_verify_event_not_confirmed('<?= $event['id'] ?>', '<?= $collection_id ?>')"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;<?php _e('Not verified', 'tainacan') ?></a>
                                                </td>
                                            <?php } else { ?>
                                                <td>
                                                    <div id="event_likes_<?php echo $event['democratic_vote_id']; ?>">
                                                        <?php
                                                        $author_id = get_post_field('post_author', $event['id']);
                                                        if ($author_id == get_current_user_id()) {
                                                            ?>
                                                            <span style="text-decoration: none;font-size: 20px;" class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
                                                            <span id="single_counter_up_<?php echo $event['id']; ?>_<?php echo $event['democratic_vote_id']; ?>"><?php echo $event['count_up'] ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                            <span style="text-decoration: none;font-size: 20px;" class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span>
                                                            <span id="single_counter_down_<?php echo $event['id']; ?>_<?php echo $event['democratic_vote_id']; ?>"><?php echo $event['count_down'] ?></span>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <a style="text-decoration: none;font-size: 20px;" onclick="event_save_vote_binary_up('<?php echo $event['democratic_vote_id']; ?>', '<?php echo $event['id']; ?>')" href="#">
                                                                <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
                                                            </a>
                                                            <span id="single_counter_up_<?php echo $event['id']; ?>_<?php echo $event['democratic_vote_id']; ?>"><?php echo $event['count_up'] ?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <a style="text-decoration: none;font-size: 20px;" onclick="event_save_vote_binary_down('<?php echo $event['democratic_vote_id']; ?>', '<?php echo $event['id']; ?>')" href="#counter_<?php echo $event['id']; ?>_<?php echo $event['democratic_vote_id']; ?>">
                                                                <span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span>
                                                            </a>
                                                            <span id="single_counter_down_<?php echo $event['id']; ?>_<?php echo $event['democratic_vote_id']; ?>"><?php echo $event['count_down'] ?></span>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                                <?php if ((current_user_can('manage_options') || verify_collection_moderators($collection_id, get_current_user_id()))): ?>
                                                    <td>
                                                        <center>
                                                            <input type="checkbox" name="process_democratic_vote" value="<?php echo $event['id']; ?>">
                                                        </center>
                                                    </td>
                                                <?php endif; ?>
                                            <?php } ?>
                                            <td> <?php echo date($event['date']); ?> </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                                <?php
                            } else { // se caso nao existir eventos a serem observados
                                ?>
                                <div id="post-0">
                                    <br>
                                    <h4 class="page-title"><strong><?php echo _e('No events in this section', 'tainacan'); ?></strong></h4>
                                    <br>
                                </div><!-- #post-0 -->
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="events_verified_tab">
                        <div id="list_events_verified">
                            <?php if (isset($events_observed)) { // se existir eventos a serem verificaos   ?>
                                <table id="event_verified_table"  class="table table-striped table-bordered" >
                                    <thead>
                                    <tr>
                                        <th><?php _e('Date', 'tainacan'); ?></th>
                                        <th><?php _e('Event Type', 'tainacan'); ?></th>
                                        <th><?php _e('Event Description', 'tainacan'); ?></th>
                                        <th><?php _e('State', 'tainacan'); ?></th>
                                        <th><?php _e('Real Date', 'tainacan'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody id="table_events_verified" >
                                    <?php foreach ($events_observed as $event) { ?>
                                        <tr>
                                            <td>
                                                <?php echo date("d/m/Y", $event['date']); ?>
                                            </td>
                                            <td>
                                                <?php echo $event['type']; ?>
                                            </td>
                                            <td>
                                                <?php echo $event['name']; ?>
                                            </td>
                                            <?php if ($moderation_type == 'moderador' || !isset($moderation_type) || empty($moderation_type)) { ?>
                                                <td>
                                                    <a style="cursor:pointer;" onclick="show_verify_event_confirmed('<?= $event['id'] ?>', '<?= $collection_id ?>')"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;
                                                        <?php
                                                        if ($event['state'] == 'confirmed'): _e('Confirmed', 'tainacan');
                                                        elseif ($event['state'] == 'not_confirmed'): _e('Not Confirmed', 'tainacan');
                                                        elseif ($event['state'] == 'invalid'): _e('Invalid', 'tainacan');
                                                        endif;
                                                        ?>
                                                    </a>
                                                </td>
                                            <?php }else { ?>
                                                <td>
                                                    <span class="glyphicon glyphicon-eye-open"></span>&nbsp;
                                                    <?php
                                                    if ($event['state'] == 'confirmed'): _e('Confirmed', 'tainacan');
                                                    elseif ($event['state'] == 'not_confirmed'): _e('Not Confirmed', 'tainacan');
                                                    elseif ($event['state'] == 'invalid'): _e('Invalid', 'tainacan');
                                                    endif;
                                                    ?>
                                                </td>
                                            <?php } ?>
                                            <td> <?php echo date($event['date']); ?> </td>
                                            <?php /* ?>
                                            <td>
                                                <?php if ($event['state'] != 'invalid'): ?>
                                                    <?php echo date("d/m/Y", get_post_meta($event['id'], 'socialdb_event_approval_date', true)); ?>
                                                <?php else: ?>
                                                    <?php _e('Invalid', 'tainacan') ?>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?php if ($event['state'] != 'invalid'): ?>
                                                    <?php $user_name_apr = get_user_by('id', get_post_meta($event['id'], 'socialdb_event_approval_by', true))->data->user_nicename; ?>
                                                    <?php if (empty($user_name_apr)) { ?>
                                                        <?php _e('Vote', 'tainacan'); ?>
                                                    <?php } else { ?>
                                                        <?php echo $user_name_apr; ?>
                                                    <?php } ?>
                                                <?php else: ?>
                                                    <?php _e('Invalid', 'tainacan') ?>
                                                <?php endif; ?>
                                            </td>
                                            <?php */ ?>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                                <?php
                            } else { // se caso nao existir eventos a serem observados
                                ?>
                                <div id="post-0">
                                    <br>
                                    <h4 class="page-title"><strong><?php echo _e('No events in this section', 'tainacan'); ?></strong></h4>
                                    <br>
                                </div><!-- #post-0 -->
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- modal eventos nao confirmados -->
<div class="modal fade" id="modal_verify_event_not_confirmed" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="submit_form_event_not_confirmed">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4><?php _e('Verify Event', 'tainacan') ?></h4>
                </div>
                <div class="modal-body">
                   <!-- <span><b><?php _e('Collection', 'tainacan') ?></b>: <span id="event_collection_name"></span></span><br>-->
                    <span><b><?php _e('Event created date', 'tainacan') ?></b>: <span id="event_date_create"></span></span><br>
                    <span><b><?php _e('Event author', 'tainacan') ?></b>: <span id="event_author"></span></span><br>
                    <hr>
                    <span><b><?php _e('Event description', 'tainacan') ?></b>:<br> <span id="event_description"></span></span><br>
                    <span id="link_new_item_not_observed"></span>
                    <hr>
                    <span><b><?php _e('Event confirmation', 'tainacan') ?></b>: </span><br>
                    <div>
                        <input type="radio" name="socialdb_event_confirmed" id="event_confirmed_true" value="true"> <?php _e('Confirmed', 'tainacan') ?>
                        <input type="radio" name="socialdb_event_confirmed" checked="checked" id="event_confirmed_false" value="false"> <?php _e('Not confirmed', 'tainacan') ?>

                    </div><br>
                    <?php if ($moderation_type == 'democratico' && (current_user_can('manage_options') || verify_collection_moderators($collection_id, get_current_user_id()))): ?>
                        <div id="admin_likes">
                            <span><b><?php _e('Votes', 'tainacan') ?></b>: </span><br>
                            <input type="hidden" id="unconfirmed_democratic_vote_id" name="single_democratic_vote_id" value="">
                            <a style="text-decoration: none;cursor: pointer;font-size: 20px;"
                               onclick="event_save_vote_binary_up($('#unconfirmed_democratic_vote_id').val(), $('#event_id').val(), 'unconfirmed')" >
                                <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
                            </a>
                            <span id="unconfirmed_counter_up"></span>&nbsp;&nbsp;&nbsp;
                            <a style="text-decoration: none;cursor: pointer;font-size: 20px;"
                               onclick="event_save_vote_binary_down($('#unconfirmed_democratic_vote_id').val(), $('#event_id').val(), 'unconfirmed')" >
                                <span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span>
                            </a>
                            <span id="unconfirmed_counter_down"></span>
                        </div>
                    <?php endif; ?>
                    <span><b><?php _e('Event observation', 'tainacan') ?></b>: </span><br>
                    <textarea class="form-control" name="socialdb_event_observation" id="event_observation"></textarea>
                    <input type="hidden" id="event_operation" name="operation" value="">
                    <input type="hidden" id="event_id" name="event_id" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan') ?></button>
                    <button type="submit" class="btn btn-primary"><?php _e('Save', 'tainacan') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- modal exluir -->
<div class="modal fade" id="modal_verify_event_confirmed" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="submit_form_event_not_confirmed">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4><?php _e('Verify Event', 'tainacan') ?></h4>
                </div>
                <div class="modal-body">
                   <!-- <span><b><?php _e('Collection', 'tainacan') ?></b>: <span id="event_collection_name"></span></span><br>-->
                    <span><b><?php _e('Event created date', 'tainacan') ?></b>: <span id="event_date_create"></span></span><br>
                    <span><b><?php _e('Event author', 'tainacan') ?></b>: <span id="event_author"></span></span><br>
                    <hr>
                    <span><b><?php _e('Event description', 'tainacan') ?></b>:<br> <span id="event_description"></span></span><br>
                    <span id="link_new_item_observed"></span>
                    <hr>
                    <span><b><?php _e('Event confirmation', 'tainacan') ?></b>: </span><br>
                    <div>
                        <input  type="radio" name="socialdb_event_confirmed" id="event_confirmed_true" value="true"> <?php _e('Confirmed', 'tainacan') ?>
                        <input type="radio" name="socialdb_event_confirmed" checked="checked" id="event_confirmed_false" value="false"> <?php _e('Not confirmed', 'tainacan') ?>
                    </div><br>
                    <span><b><?php _e('Event observation', 'tainacan') ?></b>: </span><br>
                    <textarea class="form-control" name="socialdb_event_observation" id="event_observation"></textarea>
                    <input type="hidden" id="event_operation" name="operation" value="">
                    <input type="hidden" id="event_id" name="event_id" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan') ?></button>
                    <button type="submit" class="btn btn-primary"><?php _e('Save', 'tainacan') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- modal eventos nao confirmados democraticos usuarios nao administradores -->
<div class="modal fade" id="modal_verify_event_not_confirmed_democratic" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="submit_form_event_not_confirmed">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4><?php _e('Event Details', 'tainacan') ?></h4>
                </div>
                <div class="modal-body">
                   <!-- <span><b><?php _e('Collection', 'tainacan') ?></b>: <span id="event_collection_name"></span></span><br>-->
                    <span><b><?php _e('Event created date', 'tainacan') ?></b>: <span id="unconfirmed_users_event_date_create"></span></span><br>
                    <span><b><?php _e('Event author', 'tainacan') ?></b>: <span id="unconfirmed_users_event_author"></span></span><br>
                    <hr>
                    <span><b><?php _e('Event description', 'tainacan') ?></b>:<br> <span id="unconfirmed_users_event_description"></span></span><br>
                    <span id="link_new_item_not_observed"></span>
                    <hr>
                    <span><b><?php _e('Votes', 'tainacan') ?></b>: </span><br>
                    <div id="vote_not_allowed" style="display:none;">
                        <input type="hidden" id="unconfirmed_users_democratic_vote_id" name="single_democratic_vote_id" value="">
                        <span style="text-decoration: none;font-size: 20px;" class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
                        <span id="unconfirmed_users_counter_up"></span>&nbsp;&nbsp;&nbsp;
                        <span style="text-decoration: none;font-size: 20px;" class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span>
                        <span id="unconfirmed_users_counter_down"></span><br>
                        <input type="hidden" id="unconfirmed_users_event_id" name="event_id" value="">
                    </div>
                    <div id="vote_allowed">
                        <input type="hidden" id="unconfirmed_users_democratic_vote_id" name="single_democratic_vote_id" value="">
                        <a style="text-decoration: none;cursor: pointer;font-size: 20px;"
                           onclick="event_save_vote_binary_up($('#unconfirmed_users_democratic_vote_id').val(), $('#unconfirmed_users_event_id').val(), 'unconfirmed_users')" >
                            <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
                        </a>
                        <span id="unconfirmed_users_counter_up"></span>&nbsp;&nbsp;&nbsp;
                        <a style="text-decoration: none;cursor: pointer;font-size: 20px;"
                           onclick="event_save_vote_binary_down($('#unconfirmed_users_democratic_vote_id').val(), $('#unconfirmed_users_event_id').val(), 'unconfirmed_users')" >
                            <span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span>
                        </a>
                        <span id="unconfirmed_users_counter_down"></span><br>
                        <input type="hidden" id="unconfirmed_users_event_id" name="event_id" value="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>