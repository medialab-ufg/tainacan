<?php
$property_data_types = $view_helper->get_property_data_types();
foreach( $view_helper->get_metadata_types() as $type => $label):
    if ( ! in_array($type, $view_helper->get_special_metadata()) ): ?>
        <div class="modal fade" id="meta-<?php echo $type ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel-<?php echo $type ?>">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom: none;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel" style="display: inline-block">
                            <?php echo __("Add Property", "tainacan")  . ' - ' . __( $label , 'tainacan') ?>
                        </h4>
                        <?php ?>
                        <select name="select-data-type" id="select-data-type" class="form-control" style="display: none" onchange="change_meta_type()">
                            <?php foreach($property_data_types as $tipo => $titulo ): ?>
                                <option value="<?php echo $tipo ?>"> <?php echo $titulo ?> </option>
                            <?php endforeach; ?>
                        </select>
                         <?php ?>
                    </div>

                    <div class="modal-body">
                        <form id="submit_form_property_data_<?php echo $type ?>" name="submit_form_property_data_<?php echo $type ?>" class="form_property_data">
                            <div class="metadata-common-fields">
                                <div class="create_form-group">
                                    <label for="property_data_name"><?php _e('Property name','tainacan'); ?></label>
                                    <input type="text" class="form-control" id="property_data_name" name="property_data_name" placeholder="<?php _e('Property name','tainacan'); ?>">
                                </div> <br />

                                <div id="default_field" class="create_form-group">
                                    <label for="socialdb_property_default_value"><?php _e('Property data default value','tainacan'); ?></label>
                                    <input type="text" class="form-control" id="socialdb_property_data_default_value" name="socialdb_property_default_value" placeholder="<?php _e('Property Data Default Value','tainacan'); ?>"><br>
                                </div>
                                
                                <div class="create_form-group">
                                    <label for="socialdb_property_help"><?php _e('Text helper','tainacan'); ?></label>
                                    <input type="text" class="form-control" id="socialdb_property_data_help" name="socialdb_property_data_help" />
                                </div>
                                <br>
                                
                                <div id="cardinality_field" class="form-group category-fit-column" style="display: inline-block; width: 59%">
                                    <label for="property_term_required" style="display: block"><?php _e('Elements Quantity:','tainacan'); ?></label>
                                    <input type="radio" name="socialdb_property_data_cardinality" id="socialdb_property_data_cardinality_1" checked="checked"  value="1">&nbsp;<?php _e('Unic value','tainacan') ?>
                                    <input type="radio" name="socialdb_property_data_cardinality" id="socialdb_property_data_cardinality_n" value="n">&nbsp;<?php _e('Multiple values','tainacan') ?>
                                </div>
                                
                                <div id="required_field" class="form-group" style="display: inline-block; width: 59%" >
                                    <label for="property_data_required" style="display: block"><?php _e('Required','tainacan'); ?></label>
                                    <input type="radio" name="property_data_required" id="property_data_required_true"  value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
                                    <input type="radio" name="property_data_required" id="property_data_required_false" value="false">&nbsp;<?php _e('No','tainacan'); ?>
                                </div>
                                <?php $view_helper->commomFieldsProperties() ?>
                                <div class="create_form-group">
                                    <a onclick="show_key_container()" href="javascript:void(0)"><?php _e('Mask', 'tainacan'); ?>&nbsp;&nbsp;<span class="mask_down glyphicon glyphicon-chevron-down"></span><span style="display:none;" class="mask_up glyphicon glyphicon-chevron-up"></span></a>
                                    <!--select name='socialdb_property_data_mask' id='socialdb_property_data_mask' class="form-control">
                                        <option value=""><?php _e('Select...', 'tainacan') ?></option>
                                        <option value="key">_<?php _e('key', 'tainacan') ?></option>
                                    </select-->
                                    <div class="row mask_container" style="display:none;margin-top: 5px;">
                                        <div class="col-md-6">
                                            <input type="radio" name="socialdb_property_data_mask[]" value="key">&nbsp;<?php _e('Collection key', 'tainacan') ?><br>
                                            <input type="radio" name="socialdb_property_data_mask[]" value="repository_key">&nbsp;<?php _e('Repository key', 'tainacan') ?><br>
                                        </div>
                                        <div class="col-md-6">

                                        </div>
                                    </div>
                                    <?php
                                    do_action('tainacan_date_aacr2', $type);
                                    ?>
                                </div>

                                <!-- Filter -->
                                <div class="form-group">
                                    <label for="use-filter"><?php _e('Use as a filter', 'tainacan'); ?></label>
                                    <input type="checkbox" value="use_filter" name="property_data_use_filter" class="property_data_use_filter" />
                                </div>

                                <div class="form-group data-widget" style="display: none;">

                                    <label for="search_data_widget"><?php _e('Filter type', 'tainacan'); ?></label>
                                    <select name="search_data_widget" id="search_data_widget" class="form-control" data-type="<?php echo $type ?>"
                                            onclick="select_tree_color('<?php echo "#meta-$type" ?>')"
                                            onchange="show_increase_btn('<?php echo $type ?>', this)">
                                        <?php echo $view_helper->render_widgets_options(); ?>
                                    </select>

                                    <?php if (in_array($type, ["date", "numeric"])) { ?>
                                        <br />
                                        <div id="data_range_submit" style="display: none">
                                            <div id="data_range_form"></div>
                                            <button type="button" class="range_increaser" data-type="<?php echo $type ?>"
                                                    onclick="increase_data_range()">
                                                <span class="glyphicon glyphicon-plus"></span><?php _e('Add', 'tainacan') ?>
                                            </button>
                                        </div>
                                        <input type='hidden' id='counter_data_range' name="counter_data_range" value='0'>
                                    <?php } ?>

                                    <div id="color_field_property_search" style="display: block">
                                        <h5 style="color: black"><strong><?php _e('Set the facet color', 'tainacan'); ?></strong></h5>
                                        <div class="form-group" style="padding-left: 5px">
                                            <?php
                                            for ($i = 1; $i < 14; $i++) {
                                                echo '<label class="radio-inline"> <input type="radio" class="color_property" name="color_facet" id="color_property' . $i . '" value="color_property' . $i . '" ';
                                                echo '><img src="' . get_template_directory_uri() . '/libraries/images/cor' . $i . '.png">  </label>';
                                            };
                                            ?>
                                        </div>
                                        <div class="form-group" style="margin-top: 15px;margin-bottom: 15px;">
                                            <label style="margin-right: 10px;" ><?php _e('More options','tainacan'); ?> : </label>
                                            <input type="checkbox" name="habilitate_more_options" id="habilitate_more_options" value="true">&nbsp;<?php _e('Habilitate more options','tainacan') ?>
                                        </div>
                                    </div>


                                    <div class="form-group" style="margin-top: 15px;margin-bottom: 15px;">
                                        <label for="property_term_required" style="margin-right: 10px;" ><?php _e('Ordenation', 'tainacan'); ?> : </label>
                                        &nbsp;<input type="radio" name="filter_ordenation" id="filter_ordenation_a" checked="checked"  value="alphabetic">&nbsp;<?php _e('Alphabetic', 'tainacan') ?>
                                        &nbsp;<input type="radio" name="filter_ordenation" id="filter_ordenation_1" value="number">&nbsp;<?php _e('Items number', 'tainacan') ?>
                                    </div>
                                </div>
                                
                                <input type="hidden" name="property_data_widget" value="<?php echo $type ?>" class="property_data_widget">
                                <input type="hidden" name="orientation" value="left-column">
                            </div>

                            <input type="hidden" name="property_category_id" value="<?php echo $category->term_id; ?>">
                            <input type="hidden" name="property_metadata_type" value="<?php echo $type ?>" id="property_metadata_type">
                            <input type="hidden" id="property_data_collection_id" name="collection_id" value="<?php echo $collection_id ?>">
                            <input type="hidden" id="property_data_id" name="property_data_id" value="">
                            <input type="hidden" id="operation_property_data" name="operation" value="add_property_data">
                            <input type="hidden" id="search_add_facet" name="search_add_facet" value="">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left close-modal" data-dismiss="modal"><?php _e('Cancel','tainacan') ?></button>
                        <button type="submit" class="btn btn-primary action-continue" form="submit_form_property_data_<?php echo $type ?>">
                            <?php _e('Save','tainacan') ?>
                        </button>
                    </div>
                    </div>
                </div>
            </div>
            <?php
        endif;
endforeach;

foreach( ['object', 'term', 'voting', 'filter', 'tag','compounds'] as $metadata ) {
    include_once (dirname(__FILE__) ."/metadata/form_$metadata.php");
}
?>

<div class="modal fade" id="modal_remove_property" tabindex="-1" role="dialog" aria-labelledby="modal_remove_property_data" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="submit_delete_property">
                <input type="hidden" id="property_delete_collection_id" name="collection_id" value="">
                <input type="hidden" id="property_delete_id" name="property_delete_id" value="">
                <input type="hidden" id="operation" name="operation" value="delete">
                <input type="hidden" name="type" id="type" value="1">

                <input type="hidden" name="property_category_id" value="" id="property_category_id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php echo __('Removing property','tainacan'); ?></h4>
                </div>

                <div class="modal-body">
                    <?php echo __('Confirm the exclusion of ','tainacan'); ?> <span id="deleted_property_name"></span> ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close','tainacan'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php _e('Salve','tainacan'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>