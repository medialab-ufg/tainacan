<div class="modal fade" id="meta-tag" tabindex="-1" role="dialog" aria-labelledby="Filter">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: none;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel" style="font-weight: bolder; color: black;">
                    <?php _e("Edit", "tainacan") ?> <span class="tainacan-filter-title"></span>
                </h4>
            </div>
            <div class="modal-body">
                <form id="submit_form_tag" name="submit_form_tag">
                    <input type="hidden" name="property_data_widget" value="<?php echo get_term_by('slug', 'socialdb_property_fixed_tags', 'socialdb_property_type')->term_id ?>">
                    <div class="form-group">
                        <label for="use-filter"><?php _e('Use as a filter','tainacan'); ?></label>
                        <input type="checkbox" value="true" name="property_data_use_filter" class="property_data_use_filter" />
                    </div>
                    <input type="hidden" name="search_add_facet" value="<?php echo get_term_by('slug', 'socialdb_property_fixed_tags', 'socialdb_property_type')->term_id ?>" >

                    <div class="form-group data-widget" style="display: none;">
                        <label for="search_data_widget"><?php _e('Filter type','tainacan'); ?></label>
                        <select name="search_data_widget" id="search_data_widget" class="form-control">
                            <option value="tree"> <?php _e('Tree', 'tainacan') ?> </option>
                            <option value="cloud"> <?php _e('Tag Cloud', 'tainacan') ?> </option>
                        </select>
                    </div>

                    <input type="hidden" id="property_data_collection_id" name="collection_id" value="<?php echo $collection_id ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left close-modal" data-dismiss="modal"><?php _e('Cancel','tainacan') ?></button>
                <button type="submit" class="btn btn-primary action-continue" form="submit_form_tag">
                    <?php _e('Continue','tainacan') ?>
                </button>
            </div>
        </div>
    </div>
</div>