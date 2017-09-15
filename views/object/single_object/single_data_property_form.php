<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/data_property_form_js.php'); ?>

<form id="single_submit_form_property_data">
    <input type="hidden" name="object_id"  value="<?php echo $category->term_id; ?>">
    <input type="hidden" name="property_category_id"  value="<?php echo $category->term_id; ?>">
    <div class="create_form-group">
        <label for="event_add_property_data_name"><?php _e('Property data name','tainacan'); ?></label>
        <input type="text" class="form-control" id="single_event_add_property_data_name" name="socialdb_event_property_data_create_name" required="required" placeholder="<?php _e('Property Data name'); ?>">
    </div>
    <div class="form-group">
        <label for="event_add_property_data_widget"><?php _e('Property data widget','tainacan'); ?></label>
        <select class="form-control" id="single_event_add_property_data_widget" name="socialdb_event_property_data_create_widget">
           <?php do_action('form_help_property_data_insert_types') ?>
        <option <?php do_action('form_help_property_data_type_text') ?> value="text"><?php _e('Text','tainacan'); ?></option>
        <option <?php do_action('form_help_property_data_type_textarea') ?> value="textarea"><?php _e('Textarea','tainacan'); ?></option>
        <option <?php do_action('form_help_property_data_type_date') ?> value="date"><?php _e('Date','tainacan'); ?></option>
        <option <?php do_action('form_help_property_data_type_numeric') ?> value="numeric"><?php _e('Numeric','tainacan'); ?></option>
        <option <?php do_action('form_help_property_data_type_autoincrement') ?> value="autoincrement"><?php _e('Auto-Increment','tainacan'); ?></option>
        </select>
    </div>
    <!--div class="form-group">
        <label for="event_add_property_data_column_ordenation"><?php _e('Property data column ordenation','tainacan'); ?></label>
        <input type="radio" name="socialdb_event_property_data_create_ordenation_column" id="single_event_add_property_data_column_ordenation_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
        <input type="radio" name="socialdb_event_property_data_create_ordenation_column" id="single_event_add_property_data_column_ordenation_false" checked="checked" value="false">&nbsp;<?php _e('No','tainacan'); ?>
    </div-->
    <div class="form-group">
        <label for="event_add_property_data_required"><?php _e('Property data required','tainacan'); ?></label>
        <input type="radio" name="socialdb_event_property_data_create_required" id="single_event_add_property_data_required_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
        <input type="radio" name="socialdb_event_property_data_create_required" id="single_event_add_property_data_required_false" checked="checked" value="false">&nbsp;<?php _e('No','tainacan'); ?>
    </div>
    <?php  do_action('form_modify_property_data') ?>
    <input type="hidden" id="single_event_add_property_data_collection_id" name="socialdb_event_collection_id" value="<?php echo $collection_id; ?>">
    <input type="hidden" id="single_event_add_property_data_object_id" name="property_data_object_id" value="<?php echo $object_id; ?>">
    <input type="hidden" id="single_event_add_property_data_create_time" name="socialdb_event_create_date" value="<?php echo time(); ?>">
    <input type="hidden" id="single_event_add_property_data_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>">
    <input type="hidden" id="operation_property_data" name="operation" value="add_event_property_data_create">

    <button type="submit" id="submit_property_data" class="btn btn-primary pull-right" style="margin-left: 5px;">
        <?php _e('Add','tainacan'); ?>
    </button>
    <button type="button" onclick="back_button('<?php echo $object_id; ?>')" class="btn btn-default pull-right" id="clear_categories">
        <?php _e('Cancel','tainacan'); ?>
    </button>
    <br>
</form>