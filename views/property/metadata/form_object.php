<div id="meta-relationship" class="modal fade" role="dialog" aria-labelledby="Relationship">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> <?php _e('Add Property', 'tainacan') ?> - <?php _e('Relationship', 'tainacan') ?> </h4>
            </div>
            <div class="modal-body">
                <form id="submit_form_property_object">
                    <div class="metadata-common-fields">
                        <div class="create_form-group">
                            <label for="property_object_name"><?php _e('Property name','tainacan'); ?></label>
                            <input type="text" class="form-control" id="property_object_name" name="property_object_name" required="required" placeholder="<?php _e('Property Object name','tainacan'); ?>">
                        </div>
                        <div class="form-group">
                            <?php //if (isset($is_root) && $is_root): ?>
                                <!--label for="property_object_category_id"><?php _e('Property relationship','tainacan'); ?></label>
                                <select class="form-control" id="property_object_category_id" name="property_object_category_id">
                                    <?php foreach ($property_object as $object) { ?>
                                        <option value="<?php echo $object['category_id'] ?>"><?php echo $object['collection_name'] ?></option>
                                    <?php } ?>
                                </select-->
                            <?php // else: ?>
                                <label for="property_object_category_id"><?php _e('Property relationship','tainacan'); ?></label>
                               <div id="property_category_dynatree" style="height: 200px;overflow-y: scroll;" >
                               </div>
                                <br>
                                <p><?php _e('Selected relationship','tainacan') ?></p>
                                <div id="selected_categories_relationship"></div>
                               <input required="required" type="hidden"  id="property_object_category_id"  name="property_object_category_id" value="<?php //echo $category->term_id; ?>" >
                            <?php // endif; ?>
                        </div>
                        <a style="cursor: pointer;" onclick="toggle_advanced_configuration('#data-advanced-configuration-object')">
                            <?php _e('Advanced Configuration', 'tainacan') ?> <span class="glyphicon glyphicon-triangle-bottom"></span>
                        </a>
                    </div>
                    <div id="data-advanced-configuration-object" style="display: none;">
                        <?php $view_helper->commomFieldsProperties(true) ?>
                        <div class="form-group category-fit-column" style="display: inline-block; width: 59%">
                            <label style="display: block"><?php _e('Habilitate add new item','tainacan'); ?></label>
                            &nbsp;<input type="radio" name="socialdb_event_property_habilitate_new_item" id="new_item_true"   value="true">&nbsp;<?php _e('Yes','tainacan') ?>
                            &nbsp;<input type="radio" name="socialdb_event_property_habilitate_new_item" id="new_item_false" checked="checked" value="false">&nbsp;<?php _e('No','tainacan') ?>
                        </div>
                        <div class="form-group category-fit-column" style="display: inline-block; width: 59%">
                            <label for="property_term_required" style="display: block"><?php _e('Elements Quantity:','tainacan'); ?></label>
                            <input type="radio" name="socialdb_property_object_cardinality" id="socialdb_property_object_cardinality_1"  value="1">&nbsp;<?php _e('Unic value','tainacan') ?>
                            <input type="radio" name="socialdb_property_object_cardinality" id="socialdb_property_object_cardinality_n" checked="checked" value="n">&nbsp;<?php _e('Multiple values','tainacan') ?>
                        </div>
                        <div class="form-group">
                            <label for="property_object_required"><?php _e('Property object required','tainacan'); ?></label>
                            <input type="radio" name="property_object_required" id="property_object_required_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
                            <input type="radio" name="property_object_required" id="property_object_required_false" checked="checked" value="false">&nbsp;<?php _e('No','tainacan'); ?>
                        </div>
                        <!--div class="form-group">
                            <label for="property_object_is_reverse"><?php _e('Property object reverse','tainacan'); ?></label>
                            <input type="radio" name="property_object_is_reverse" id="property_object_is_reverse_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
                            <input type="radio" name="property_object_is_reverse" id="property_object_is_reverse_false" checked="checked" value="false">&nbsp;<?php _e('No','tainacan'); ?>
                        </div-->
                        <div class="form-group"  >
                            <label for="property_object_reverse"><?php _e('Select the reverse property','tainacan'); ?></label>
                            <select class="form-control" id="property_object_reverse" name="property_object_reverse" onchange="setValueReverse(this)">
                            </select>
                        </div>
                        <input type="hidden" value="false" id="property_object_is_reverse" name="property_object_is_reverse">

                        <?php /*
                        <div class="form-group">
                            <label for="socialdb_property_term_widget"><?php _e('Property Term Widget','tainacan'); ?></label>
                            <select class="form-control" id="socialdb_property_term_widget" name="socialdb_property_term_widget">
                            </select>
                        </div>
                        */ ?>

                        <hr class="hr-style">
                        <div class="form-group" style="display: inline-block;">
                            <label for="property_term_required" style="margin-right: 10px;" ><?php _e('Visualization','tainacan'); ?> : </label>
                            &nbsp;<input type="radio" name="socialdb_event_property_visualization" id="socialdb_property_object_visualization_public" checked="checked"  value="public">&nbsp;<?php _e('Public','tainacan') ?>
                            &nbsp;<input type="radio" name="socialdb_event_property_visualization" id="socialdb_property_object_visualization_restrict" value="restrict">&nbsp;<?php _e('Restrict','tainacan') ?>
                        </div>
                        <div class="form-group">
                            <label for="socialdb_event_property_tab"><?php _e('Select the tab','tainacan'); ?></label>
                            <select class="socialdb_event_property_tab form-control" name="socialdb_event_property_tab">
                            </select>
                        </div>
                        <div  class="create_form-group">
                            <label for="socialdb_property_default_value"><?php _e('Property data default value', 'tainacan'); ?></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="default_value_text" 
                                   onkeyup="autocomplete_object_property_default_value($('#property_object_category_id').val());"
                                   name="default_value_text" 
                                   placeholder="<?php _e('Type the name of the item', 'tainacan'); ?>"><br>
                            <input type="hidden" 
                                   id="socialdb_property_object_default_value" name="socialdb_event_property_default_value"><br>
                        </div>
                    </div>    
                    <div class="form-group" style="margin-top: 15px;">
                        <label for="use-filter"><?php _e('Use as a filter','tainacan'); ?></label>
                        <input type="checkbox" value="use_filter" name="property_data_use_filter" class="property_data_use_filter" />
                    </div>

                    <div class="form-group data-widget" style="display: none;">
                        <label for="search_data_widget"><?php _e('Filter type','tainacan'); ?></label>
                        <select name="search_data_widget" id="search_data_widget" class="form-control"  data-type="socialdb_property_object"
                                onchange="select_tree_color('#meta-relationship')" >
                            <option value="tree"><?php _e('Tree','tainacan') ?></option>
                        </select>

                        <?php echo $view_helper->render_tree_colors(); ?>
                        <div class="form-group" style="margin-top: 15px;margin-bottom: 15px;">
                            <label for="property_term_required" style="margin-right: 10px;" ><?php _e('Ordenation','tainacan'); ?> : </label>
                            &nbsp;<input type="radio" name="filter_ordenation" id="object_filter_ordenation_a" checked="checked"  value="alphabetic">&nbsp;<?php _e('Alphabetic','tainacan') ?>
                            &nbsp;<input type="radio" name="filter_ordenation" id="object_filter_ordenation_1" value="number">&nbsp;<?php _e('Items number','tainacan') ?>
                        </div>
                    </div>

                    <input type="hidden" id="property_object_collection_id" name="collection_id" value="">
                    <input type="hidden" id="property_object_id" name="property_object_id" value="">
                    <input type="hidden" id="operation_property_object" name="operation" value="add_property_object">
                    <input type="hidden" name="property_category_id"  value="<?php echo $category->term_id; ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left close-modal" data-dismiss="modal"><?php _e('Cancel','tainacan') ?></button>
                <button type="submit" class="btn btn-primary action-continue" form="submit_form_property_object">
                    <?php _e('Continue','tainacan') ?>
                </button>
<!--                <button type="button" onclick="clear_buttons()" class="btn btn-default" id="clear_categories">--><?php //_e('New','tainacan'); ?><!--</button>-->
            </div>
        </div>
    </div>
</div>