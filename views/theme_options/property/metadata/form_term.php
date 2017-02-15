<div id="meta-category" class="modal fade" role="dialog" aria-labelledby="Category">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit"> <?php _e('Add metadata', 'tainacan') ?> </span> - <?php _e('Category', 'tainacan') ?>
                </h4>
            </div>

            <div class="modal-body">
                <form id="submit_form_property_term">
                    <div>
                        <div class="category-fit-column">
                            <div class="metadata-common-fields">
                                <div class="create_form-group form-group">
                                    <label for="property_term_name"><?php _e('Property term name','tainacan'); ?></label>
                                    <input type="text" class="form-control" id="property_term_name" name="property_term_name" required="required" placeholder="<?php _e('Property Term name','tainacan'); ?>">
                                </div>

                                <div class="create_form-group form-group">
                                    <label for="socialdb_property_help"><?php _e('Property term text helper','tainacan'); ?></label>
                                    <input type="text" class="form-control" id="socialdb_property_help" name="socialdb_property_help" />
                                </div>

                                <div class="form-group category-fit-column" style="display: inline-block; width: 59%">
                                    <label for="property_term_required" style="display: block"><?php _e('Elements Quantity:','tainacan'); ?></label>
                                    <input type="radio" name="socialdb_property_term_cardinality" id="socialdb_property_term_cardinality_1" checked="checked"  value="1">&nbsp;<?php _e('Unic value','tainacan') ?>
                                    <input type="radio" name="socialdb_property_term_cardinality" id="socialdb_property_term_cardinality_n" value="n">&nbsp;<?php _e('Multiple values','tainacan') ?>
                                </div>
                                <div class="form-group" style="display: inline-block; width: 39%">
                                    <label for="property_term_required" style="display: block"><?php _e('Required property','tainacan'); ?></label>
                                    <input type="radio" name="property_term_required" id="property_term_required_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
                                    <input type="radio" name="property_term_required" id="property_term_required_false" checked="checked" value="false">&nbsp;<?php _e('No','tainacan'); ?>
                                </div>

                                <div class="form-group">
                                    <label for="socialdb_property_term_widget"><?php _e('Property Term Widget','tainacan'); ?></label>
                                    <select class="form-control" id="socialdb_property_term_widget" name="socialdb_property_term_widget">
                                    </select>
                                </div>

                                <hr class="modal-hr-style">
                            </div>

                            <!--div class="form-group">
                                <label for="use-filter" style="display: inline-block"><?php _e('Use as a filter','tainacan'); ?></label>
                                <input type="checkbox" onchange="toggle_term_widget(this)" value="use_filter" name="property_data_use_filter" class="property_data_use_filter" />
                            </div-->

                            <div class="term-widget" style="display: none">

                                <div class="form-group">
                                    <label for="property_term_filter_widget"><?php _e('Property Widget','tainacan'); ?></label>
                                    <select class="form-control" onchange="term_widget_options(this)"
                                            id="property_term_filter_widget" name="property_term_filter_widget">
                                        <option value="select"><?php _e('Select', 'taincan'); ?></option>
                                    </select>

                                    <?php echo $view_helper->render_tree_colors(); ?>

                                </div>

                                <div class="form-group" id="select_menu_style" style="display: none">
                                    <label for="select_menu_style"> <?php _e('Select Menu Style', 'tainacan') ?> </label>
                                    <select class="form-control select2-menu" id="select_menu_style" name="select_menu_style">
                                        <optgroup label="<?php _e('Select the style for your facet', 'tainacan') ?>">
                                            <?php foreach ( $menu_style_ids as $menu_id): ?>
                                                <option value="menu_style_<?php echo $menu_id?>" id="menu_style_<?php echo $menu_id?>"> </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div style="float: right" class="category-fit-column right metadata-common-fields">
                            <div class="col-md-12">
                                <label for="socialdb_property_term_root_category"><?php _e('Property Term Root Category','tainacan'); ?></label>
                                <div style='height: 242px;' id="terms_dynatree">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label for="selected_category"><?php _e('Selected category','tainacan'); ?></label><br>
                                <select required="required" size='2' id="socialdb_property_term_root" class="form-control" name='socialdb_property_term_root'></select>
                            </div>

                        </div>
                    </div>

                    <input type="hidden" name="property_category_id" value="<?php echo $category->term_id; ?>">
                    <input type="hidden" id="property_term_collection_id" name="collection_id" value="">
                    <input type="hidden" id="property_term_id" name="property_term_id" value="">
                    <input type="hidden" id="operation_property_term" name="operation" value="add_property_term">
                    <input type="hidden" name="search_add_facet" id="search_add_facet" value="">
                </form>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left close-modal" data-dismiss="modal"><?php _e('Cancel','tainacan') ?></button>
                <button type="submit" class="btn btn-primary action-continue" form="submit_form_property_term">
                    <?php _e('Continue','tainacan') ?>
                </button>
<!--                <button type="button" onclick="clear_buttons()" class="btn btn-default" id="clear_categories">--><?php //_e('New','tainacan'); ?><!--</button>-->
            </div>
        </div>
    </div>
</div>