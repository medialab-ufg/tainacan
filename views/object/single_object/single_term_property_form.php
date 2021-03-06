<?php
    include_once ('../../../../../../wp-config.php');
    include_once ('../../../../../../wp-load.php');
    include_once ('../../../../../../wp-includes/wp-db.php');
    require("js/single_term_property_js.php");
    require_once(dirname(__FILE__) . "../../../../helpers/view_helper.php");
    $view_helper = new ViewHelper();
?>

<hr>
<h4 id="edit-text-term" ><?php echo __('Alter the metadata: ','tainacan') ?> <span id="name-property-term"></span></h4>
<h4 id="create-text-term" ><?php echo __('Create term metadata','tainacan') ?></h4>
<form id="submit_form_property_term" onkeypress="return event.keyCode != 13;" >
    <div id="meta-category" >
        <div class="metadata-common-fields">
            <div class="create_form-group form-group">
                <label for="property_term_name"><?php _e('Name','tainacan'); ?></label>
                <input type="text"
                       class="form-control"
                       id="property_term_name"
                       name="property_term_name"
                       required="required"
                       placeholder="<?php _e('Property Term name','tainacan'); ?>">
            </div>
            <div class="metadata-fixed-fields" style="display:none;">
                <div class="create_form-group">
                    <label for="property_fixed_name"><?php _e('Property name','tainacan'); ?></label>
                    <input type="text"
                           class="form-control"
                           id="property_fixed_name_term"
                           name="property_fixed_name"
                           placeholder="<?php _e('Property name','tainacan'); ?>">
                </div> <br />
            </div>
            <!--------- A categoria raiz do metadado -------------->
            <div class="right metadata-common-fields" style="margin-bottom: 15px;">
                <label for="socialdb_property_term_root_category">
                    <?php _e('Vinculate Category','tainacan'); ?>
                </label>
                <br>
                <!-- se a categoria ja existe  -->
                <input type="radio"
                       name="socialdb_property_vinculate_category"
                       id="socialdb_property_vinculate_category_exist"
                       checked="checked"  value="exist">&nbsp;<?php _e('Use existing: choose','tainacan') ?>

                <!-- Dynatree area -->
                <div style='height: 242px;margin-left: 15px;' id="terms_dynatree"></div>

                <br>
                <!--p><?php _e('Selected term','tainacan') ?></p-->
                <div style="display: none;"  id="selected_categories_term"></div>
                <input type="hidden"
                       id="socialdb_property_term_root"
                       class="form-control" name='socialdb_property_term_root'>
                <!-- se deseja criar uma nova categoria  -->
                <br>
                <span id="create_new">
                    <input  type="radio"
                        name="socialdb_property_vinculate_category"
                        id="socialdb_property_vinculate_category_create"
                        value="create">&nbsp;<?php _e('Create new','tainacan') ?>
                </span>
                <div style='display: none;margin-left: 15px;' id="container_add_category">
                    <input type="text"
                           class="form-control"
                           id="property_term_new_category"
                           name="socialdb_property_term_new_category"
                           placeholder="<?php _e('Taxonomy name, this field is required!','tainacan'); ?>">
                    <div style="margin-top: 15px;padding: 15px;border: 1px solid #ccc;border-radius: 4px;min-height: 65px;" onclick="verify_has_li()">
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
                        <button type="button" onclick="add_field_category()" class="btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span><?php _e('Add sub-category','tainacan') ?>
                        </button>
                        <button type="button"
                                class="btn btn-danger"
                                data-toggle="popover"
                                title="<?php _e('Helper','tainacan') ?>"
                                data-content="<?php _e('The button Add sub-category creates the fields for new categories in the taxonomy,
                                 the buttons on the right allow moves the category in the heirarchy created  ( first click in the category) ','tainacan') ?>">
                                <span class="glyphicon glyphicon-question-sign"></span>
                        </button>
                        <div id="taxonomy_create_zone" style="min-height: 150px;margin-top: 5px;" >
                            <span style="opacity: 0.5;"><?php //_e('Click here to create a sub-category','tainacan') ?></span>
                        </div>
                        <input type="hidden" value="" id="socialdb_property_term_new_taxonomy" name="socialdb_property_term_new_taxonomy">
                    </div>
                    <br>

                </div>
            </div>
            <!--------- FIM: A categoria raiz do metadado -------------->
            <div id="data-advanced-configuration-term">
                <?php //$view_helper->commomFieldsProperties() ?>
                <!--div class="create_form-group form-group">
                    <label for="socialdb_property_help"><?php _e('Property term text helper','tainacan'); ?></label>
                    <input type="text" class="form-control" id="socialdb_property_help" name="socialdb_property_help" />
                </div-->
                <div class="form-group">
                    <label for="socialdb_property_term_widget"><?php _e('Widget','tainacan'); ?></label>
                    <select class="form-control" id="socialdb_property_term_widget" name="socialdb_property_term_widget">
                    </select>
                </div>
                <div class="form-group">
                    <label for="property_term_required" ><?php _e('Elements Quantity','tainacan'); ?> : </label>
                    <input type="radio"
                           name="socialdb_property_term_cardinality"
                           id="socialdb_property_term_cardinality_1"
                           checked="checked"  value="1">&nbsp;<?php _e('Unic value','tainacan') ?>
                    <input type="radio"
                           name="socialdb_property_term_cardinality"
                           id="socialdb_property_term_cardinality_n"
                           value="n">&nbsp;<?php _e('Multiple values','tainacan') ?>
                </div>
                <!--div class="form-group">
                    <label for="property_term_required" style="margin-right: 10px;" ><?php _e('Visualization','tainacan'); ?> : </label>
                    &nbsp;<input type="radio" name="socialdb_event_property_visualization" id="socialdb_property_term_visualization_public" checked="checked"  value="public">&nbsp;<?php _e('Public','tainacan') ?>
                    &nbsp;<input type="radio" name="socialdb_event_property_visualization" id="socialdb_property_term_visualization_restrict" value="restrict">&nbsp;<?php _e('Restrict','tainacan') ?>
                </div--
                <div-- class="form-group category-fit-column" style="display: inline-block; width: 59%">
                    <label style="display: block"><?php _e('Enable add new category','tainacan'); ?></label>
                    &nbsp;<input type="radio" name="socialdb_event_property_habilitate_new_category" id="new_item_true"   value="true">&nbsp;<?php _e('Yes','tainacan') ?>
                    &nbsp;<input type="radio" name="socialdb_event_property_habilitate_new_category" id="new_item_false" checked="checked" value="false">&nbsp;<?php _e('No','tainacan') ?>
                </div-->
                <!--div class="form-group" >
                    <input type="checkbox" name="property_term_required" id="property_term_required_true" value="true">&nbsp;<b><?php _e('Required','tainacan'); ?></b>
                </div-->
                <div class="form-group">
                    <label for="event_edit_property_term_required"><?php _e('Required','tainacan'); ?>:&nbsp;</label>
                    <input type="radio"  name="property_term_required" id="property_term_required_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
                    <input type="radio"  name="property_term_required" id="property_term_required_false"  value="false">&nbsp;<?php _e('No','tainacan'); ?>
                </div>
                <!--<div class="form-group">
                    <label for="socialdb_event_property_tab"><?php _e('Select the tab','tainacan'); ?></label>
                    <select class="socialdb_event_property_tab form-control" name="socialdb_event_property_tab">
                    </select>
                </div>-->
                <!--<div  class="form-group">
                    <label for="socialdb_property_default_value"><?php _e('Property data default value', 'tainacan'); ?></label>
                    <input type="text"
                           class="form-control"
                           id="default_value_text_term"
                           onkeyup="autocomplete_term_property_default_value($('#socialdb_property_term_root').val());"
                           name="default_value_text"
                           placeholder="<?php _e('Type the name of the category', 'tainacan'); ?>"><br>
                    <input type="hidden"
                           id="socialdb_property_term_default_value" name="socialdb_event_property_default_value"><br>
                </div>-->
            </div>

            <!--<hr class="metadata-common-fields">
            <div class="form-group">
                <label for="use-filter" style="display: inline-block"><?php _e('Use as a filter','tainacan'); ?></label>
                <input type="checkbox" onchange="toggle_term_widget(this)" value="use_filter" name="property_data_use_filter" class="property_data_use_filter" />
            </div>
            <div class="term-widget" style="display: none">

                <div class="form-group">
                    <label for="property_term_filter_widget"><?php _e('Property Widget','tainacan'); ?></label>
                    <select class="form-control" onchange="term_widget_options(this)"
                            id="property_term_filter_widget" name="property_term_filter_widget">
                        <option value="select"><?php _e('Select', 'taincan'); ?></option>
                    </select>

                    <?php echo $view_helper->render_tree_colors(); ?>

                </div>

                <div class="form-group" id="select_menu_style_container" style="display: none">
                    <select class="form-control select2-menu" id="select_menu_style" name="select_menu_style">
                        <optgroup label="<?php _e('Select the style for your facet', 'tainacan') ?>">
                            <?php foreach ( $menu_style_ids as $menu_id): ?>
                                <option value="menu_style_<?php echo $menu_id?>" id="menu_style_<?php echo $menu_id?>">
                                    <img src="<?php echo get_menu_thumb_path($menu_id); ?>">
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
                <div class="form-group" style="margin-top: 15px;margin-bottom: 15px;">
                    <label for="property_term_required" style="margin-right: 10px;" ><?php _e('Ordenation','tainacan'); ?> : </label>
                    &nbsp;<input type="radio" name="filter_ordenation" id="term_filter_ordenation_a" checked="checked"  value="alphabetic">&nbsp;<?php _e('Alphabetic','tainacan') ?>
                    &nbsp;<input type="radio" name="filter_ordenation" id="term_filter_ordenation_1" value="number">&nbsp;<?php _e('Items number','tainacan') ?>
                </div>
            </div>-->
        </div>
    </div>

    <input type="hidden" id="is_property_fixed_term" name="is_property_fixed" value="false">
    <input type="hidden" name="property_category_id" value="<?php echo $category->term_id; ?>">
    <input type="hidden" id="property_term_collection_id" name="collection_id" value="">
    <input type="hidden" id="property_term_id" name="property_term_id" value="">
    <input type="hidden" id="operation_property_term" name="operation" value="add_property_term">
    <input type="hidden" name="search_add_facet" id="search_add_facet" value="">
    <input type="hidden" name="select_menu_style" value="menu_style_116">

    <!-- -->
    <!--input type="hidden" name="socialdb_property_term_widget" value="tree"-->
    <input type="hidden" name="socialdb_event_property_tab" value="default">
    <input type="hidden" name="property_term_filter_widget" value="tree">
    <input type="hidden" name="filter_ordenation" value="alphabetic">


    <br>
    <button type="submit" id="submit_term_form" class="btn btn-primary pull-right action-continue" form="submit_form_property_term" style="margin-left: 5px;">
        <?php _e("Save", "tainacan")?>
    </button>

    <button type="button" onclick="back_button('<?php echo $object_id; ?>')" class="btn btn-default pull-right" id="clear_categories">
        <?php _e('Cancel','tainacan'); ?>
    </button>
    <br>
</form>