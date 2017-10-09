<div class="modal fade" id="meta-filter" tabindex="-1" role="dialog" aria-labelledby="Filter">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: none;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel" style="font-weight: bolder; color: black;">
                    <?php _e("Edit", "tainacan") ?> <span class="tainacan-filter-title"></span>
                </h4>
            </div>
            <div class="modal-body">
                <form id="submit_form_filter" name="submit_form_filter">

                    <div class="form-group">
                        <label for="use-filter"><?php _e('Use as a filter','tainacan'); ?></label>
                        <input type="checkbox" value="true" name="property_data_use_filter" class="property_data_use_filter" />
                    </div>

                    <div class="form-group data-widget" style="display: none;">
                        <label for="search_data_widget"><?php _e('Filter type','tainacan'); ?></label>
                        <select name="search_data_widget" id="search_data_widget" class="form-control">
                            <option value="tree"> <?php _e('Tree', 'tainacan') ?> </option>
                        </select>

                        <div id="color_field_property_search">
                            <h5><strong><?php _e('Set the facet color','tainacan'); ?></strong></h5>
                            <div class="form-group">
                                <?php for ($i = 1; $i < 14; $i++) {
                                    echo '<label class="radio-inline"> <input type="radio" class="color_property" name="color_facet" id="color_property' . $i . '" value="color_property' . $i . '" ';
                                    echo '><img src="' . get_template_directory_uri() . '/libraries/images/cor' . $i . '.png">  </label>';
                                }; ?>
                            </div>
                            <div class="form-group" style="margin-top: 15px;margin-bottom: 15px;">
                                <label style="margin-right: 10px;" ><?php _e('More options','tainacan'); ?> : </label>
                                <input type="checkbox" name="habilitate_more_options" id="habilitate_more_options" value="true">&nbsp;<?php _e('Habilitate more options','tainacan') ?>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="property_data_widget" value="tree">
                    <input type="hidden" name="search_add_facet" value="" id="search_add_facet">
                    <input type="hidden" name="operation" class="operation" value="">
                    <input type="hidden" id="property_data_collection_id" name="collection_id" value="<?php echo $collection_id ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left close-modal" data-dismiss="modal"><?php _e('Cancel','tainacan') ?></button>
                <button type="submit" class="btn btn-primary action-continue" form="submit_form_filter">
                    <?php _e('Continue','tainacan') ?>
                </button>
            </div>
        </div>
    </div>
</div>