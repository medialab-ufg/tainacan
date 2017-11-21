<div id="meta-voting" class="modal fade" role="dialog" aria-labelledby="Voting">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> <span class="ranking-action"><?php _e('Add', 'tainacan'); ?></span> <?php _e('Ranking', 'tainacan'); ?> </h4>
            </div>
            <div class="modal-body">
                <form id="submit_form_ranking" name="submit_ranking">
                    <div class="form-group">
                        <label for="ranking_name"><?php _e('Ranking Name','tainacan'); ?></label>
                        <input type="text" class="form-control" id="ranking_name" name="ranking_name" required="required" value="">
                    </div>

                    <div class="form-group">
                        <label for="ranking_type"> <?php _e('Ranking Type','tainacan'); ?></label>
                        <select name="ranking_type" class="form-control ranking-type" onchange="set_voting_widget(this)">
                            <option value="like"><?php _e('Like','tainacan'); ?></option>
                            <option value="binary"><?php _e('Binary','tainacan'); ?></option>
                            <option value="stars"><?php _e('Stars','tainacan'); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="socialdb_event_property_tab"><?php _e('Select the tab','tainacan'); ?></label>
                        <select class="socialdb_event_property_tab form-control" name="socialdb_event_property_tab">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="use-filter"><?php _e('Use as a filter','tainacan'); ?></label>
                        <input type="checkbox" value="use_filter" name="property_data_use_filter" class="property_data_use_filter" onchange="toggle_widget(this)"/>
                    </div>

                   <!-- <div class="data-widget">
                        <div class="use-voting-filter" style="display: none">
                        <div class="form-group">
                            <label for="search_data_widget"><?php /*_e('Widget','tainacan'); */?></label>
                            <select class="form-control" id="search_data_widget" name="search_data_widget" required="" onchange="toggle_range_submit(this)">
                                <option value="from_to"><?php /*_e('From/To','tainacan'); */?></option>
                                <option value="range"><?php /*_e('Range','tainacan'); */?></option>
                            </select>
                        </div>
                    </div>
                        <div id="range_submit" style="display: none;">
                            <div id="range_form"></div>
                            <button type="button" onclick="increase_range()"><span class="glyphicon glyphicon-plus"></span><?php /*_e('Add','tainacan') */?></button>
                        </div>
                    </div>-->

                    <input type="hidden" name="search_data_orientation" value="left-column">
                    <input type="hidden" id="search_data_widget" name="search_data_widget" value="">
                    <input type="hidden" id="ranking_id" name="ranking_id" value="">
                    <input type="hidden" id="operation" name="operation" value="add">
                    <input type="hidden" id="search_collection_id" name="collection_id" value="<?php echo $collection_id; ?>">
                    <input type='hidden' id='counter_range' name="counter_range" value='0'>
                    <input type="hidden" name="property_category_id" id="property_category_id" value="<?php echo $category->term_id; ?>"><br>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left close-modal" data-dismiss="modal"><?php _e('Cancel','tainacan') ?></button>
                <button type="submit" class="btn btn-primary action-continue" form="submit_form_ranking">
                    <?php _e('Continue','tainacan') ?>
                </button>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_remove_ranking" tabindex="-1" role="dialog" aria-labelledby="modal_remove_ranking" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="submit_delete_ranking">
                <input type="hidden" id="ranking_delete_collection_id" name="collection_id" value="">
                <input type="hidden" id="ranking_delete_id" name="ranking_delete_id" value="">
                <input type="hidden" id="operation" name="operation" value="delete_ranking">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php echo __('Removing ranking', 'tainacan'); ?></h4>
                </div>
                <div class="modal-body">
                    <?php echo __('Confirm the exclusion of ', 'tainacan'); ?>&nbsp;<span id="deleted_ranking_name"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo __('Save', 'tainacan'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>