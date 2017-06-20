<style>
    #container_add_category ul{
        list-style: square;
    }
    .style-input {
        padding: 5px;border: 1px solid #ccc;border-radius: 4px; 
    }
    #meta-category .ui-autocomplete { position: absolute; cursor: default;z-index:30 !important;} 
</style> 
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
                            <div class="metadata-common-fields">
                                <div class="create_form-group form-group">
                                    <label for="property_term_name"><?php _e('Property term name', 'tainacan'); ?></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="property_term_name" 
                                           name="property_term_name" 
                                           required="required" 
                                           placeholder="<?php _e('Property Term name', 'tainacan'); ?>">
                                </div>
                                <div class="metadata-fixed-fields" style="display:none;">
                                    <div class="create_form-group">
                                        <label for="property_fixed_name"><?php _e('Property name', 'tainacan'); ?></label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="property_fixed_name_term" 
                                               name="property_fixed_name" 
                                               placeholder="<?php _e('Property name', 'tainacan'); ?>">
                                    </div> <br />
                                </div>
                                <!--------- A categoria raiz do metadado -------------->
                                <div class="right metadata-common-fields" style="margin-bottom: 15px;">
                                    <label for="socialdb_property_term_root_category">
                                        <?php _e('Vinculate Category', 'tainacan'); ?>
                                    </label>
                                    <br>
                                    <!-- se a categoria ja existe  -->
                                    <input type="radio"
                                           name="socialdb_property_vinculate_category" 
                                           id="socialdb_property_vinculate_category_exist" 
                                           checked="checked"  value="exist">&nbsp;<?php _e('Use existing: choose', 'tainacan') ?>
                                    <div style='height: 242px;margin-left: 15px;' id="terms_dynatree"></div>
                                    <br>
                                    <p><?php _e('Selected term', 'tainacan') ?></p>
                                    <div id="selected_categories_term"></div>
                                    <input type="hidden" 
                                           id="socialdb_property_term_root" 
                                           class="form-control" name='socialdb_property_term_root'>
                                    <!-- se deseja criar uma nova categoria  -->
                                    <br>
                                    <input  type="radio" 
                                            name="socialdb_property_vinculate_category" 
                                            id="socialdb_property_vinculate_category_create" 
                                            value="create">&nbsp;<?php _e('Create new', 'tainacan') ?>
                                    <div style='display: none;margin-left: 15px;' id="container_add_category">
                                        <input type="text" 
                                               class="form-control" 
                                               id="property_term_new_category" 
                                               name="socialdb_property_term_new_category"  
                                               placeholder="<?php _e('Category name', 'tainacan'); ?>">
                                        <div style="margin-top: 15px;padding: 15px;border: 1px solid #ccc;border-radius: 4px;min-height: 65px;"
                                             onclick="verify_has_li()"
                                             >
                                            <button type="button"
                                                    class="btn btn-default pull-right" 
                                                    onclick="add_hierarchy_taxonomy_create_zone()">
                                                <span class="glyphicon glyphicon-indent-left"></span>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-default pull-right" 
                                                    onclick="remove_hierarchy_taxonomy_create_zone()">
                                                <span class="glyphicon glyphicon-indent-right"></span>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-default pull-right" 
                                                    onclick="up_category_taxonomy()">
                                                <span class="glyphicon glyphicon-arrow-up"></span>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-default pull-right" 
                                                    onclick="down_category_taxonomy()">
                                                <span class="glyphicon glyphicon-arrow-down"></span>
                                            </button>
                                            <div id="taxonomy_create_zone" >
                                                <span style="opacity: 0.5;"><?php _e('Click here to create a sub-category', 'tainacan') ?></span>
                                            </div>
                                            <input type="hidden" value="" id="socialdb_property_term_new_taxonomy" name="socialdb_property_term_new_taxonomy">
                                        </div>
                                        <br>
                                        <button type="button" onclick="add_field_category()" class="btn btn-primary btn-xs">
                                            <span class="glyphicon glyphicon-plus"></span><?php _e('Add sub-category', 'tainacan') ?>
                                        </button>
                                    </div>
                                </div> 
                                <a style="cursor: pointer;" onclick="toggle_advanced_configuration('#data-advanced-configuration-term')">
                                    <?php _e('Advanced Configuration', 'tainacan') ?> <span class="glyphicon glyphicon-triangle-bottom"></span>
                                </a>
                                <!--------- FIM: A categoria raiz do metadado -------------->
                                <div id="data-advanced-configuration-term" style="display: none;">
                                    <?php $view_helper->commomFieldsProperties() ?>
                                    <div class="create_form-group form-group">
                                        <label for="socialdb_property_help"><?php _e('Property term text helper', 'tainacan'); ?></label>
                                        <input type="text" class="form-control" id="socialdb_property_help" name="socialdb_property_help" />
                                    </div>
                                    <div class="form-group">
                                        <label for="socialdb_property_term_widget"><?php _e('Property Term Widget', 'tainacan'); ?></label>
                                        <select class="form-control" id="socialdb_property_term_widget" name="socialdb_property_term_widget">
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="property_term_required" ><?php _e('Elements Quantity', 'tainacan'); ?> : </label>
                                        <input type="radio" 
                                               name="socialdb_property_term_cardinality" 
                                               id="socialdb_property_term_cardinality_1" 
                                               checked="checked"  value="1">&nbsp;<?php _e('Unic value', 'tainacan') ?>
                                        <input type="radio" 
                                               name="socialdb_property_term_cardinality" 
                                               id="socialdb_property_term_cardinality_n" 
                                               value="n">&nbsp;<?php _e('Multiple values', 'tainacan') ?>
                                    </div>
                                    <div class="form-group">
                                        <label for="property_term_required" style="margin-right: 10px;" ><?php _e('Visualization', 'tainacan'); ?> : </label>
                                        &nbsp;<input type="radio" name="socialdb_event_property_visualization" id="socialdb_property_term_visualization_public" checked="checked"  value="public">&nbsp;<?php _e('Public', 'tainacan') ?>
                                        &nbsp;<input type="radio" name="socialdb_event_property_visualization" id="socialdb_property_term_visualization_restrict" value="restrict">&nbsp;<?php _e('Restrict', 'tainacan') ?>
                                    </div>    
                                    <div class="form-group category-fit-column" style="display: inline-block; width: 59%">
                                        <label style="display: block"><?php _e('Habilitate add new category', 'tainacan'); ?></label>
                                        &nbsp;<input type="radio" name="socialdb_event_property_habilitate_new_category" id="new_item_true"   value="true">&nbsp;<?php _e('Yes', 'tainacan') ?>
                                        &nbsp;<input type="radio" name="socialdb_event_property_habilitate_new_category" id="new_item_false" checked="checked" value="false">&nbsp;<?php _e('No', 'tainacan') ?>
                                    </div>
                                    <div class="form-group" >
                                        <input type="checkbox" name="property_term_required" id="property_term_required_true" value="true">&nbsp;<b><?php _e('Required', 'tainacan'); ?></b>
                                    </div>
                                    <div class="form-group">
                                        <label for="socialdb_event_property_tab"><?php _e('Select the tab', 'tainacan'); ?></label>
                                        <select class="socialdb_event_property_tab form-control" name="socialdb_event_property_tab">
                                        </select>
                                    </div>
                                    <div  class="form-group">
                                        <label for="socialdb_property_default_value"><?php _e('Property data default value', 'tainacan'); ?></label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="default_value_text_term" 
                                               onkeyup="autocomplete_term_property_default_value($('#socialdb_property_term_root').val());"
                                               name="default_value_text" 
                                               placeholder="<?php _e('Type the name of the category', 'tainacan'); ?>"><br>
                                        <input type="hidden" 
                                               id="socialdb_property_term_default_value" name="socialdb_event_property_default_value"><br>
                                    </div>
                                </div>   
                            </div>

                            <!--div class="form-group">
                                <label for="use-filter" style="display: inline-block"><?php _e('Use as a filter', 'tainacan'); ?></label>
                                <input type="checkbox" onchange="toggle_term_widget(this)" value="use_filter" name="property_data_use_filter" class="property_data_use_filter" />
                            </div--

                            <div class="term-widget" style="display: none">

                                <div class="form-group">
                                    <label for="property_term_filter_widget"><?php _e('Property Widget', 'tainacan'); ?></label>
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
                            <?php foreach ($menu_style_ids as $menu_id): ?>
                                                        <option value="menu_style_<?php echo $menu_id ?>" id="menu_style_<?php echo $menu_id ?>"> </option>
                            <?php endforeach; ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>-->
                    </div>

                    <input type="hidden" name="property_category_id" value="<?php echo $category->term_id; ?>">
                    <input type="hidden" id="property_term_collection_id" name="collection_id" value="">
                    <input type="hidden" id="property_term_id" name="property_term_id" value="">
                    <input type="hidden" id="operation_property_term" name="operation" value="add_property_term">
                    <input type="hidden" name="search_add_facet" id="search_add_facet" value="">
                </form>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left close-modal" data-dismiss="modal"><?php _e('Cancel', 'tainacan') ?></button>
                <button type="submit" class="btn btn-primary action-continue" form="submit_form_property_term">
                    <?php _e('Continue', 'tainacan') ?>
                </button>
                <!--                <button type="button" onclick="clear_buttons()" class="btn btn-default" id="clear_categories">--><?php //_e('New','tainacan');   ?><!--</button>-->
            </div>
        </div>
    </div>
</div>