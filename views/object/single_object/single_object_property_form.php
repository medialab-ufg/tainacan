<?php
/*
 * 
 * View utilizada para CRIACAO de propriedades de objeto
 * 
 */
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/object_property_form_js.php'); ?>

<form  id="single_submit_form_property_object">
    <input type="hidden" name="property_category_id"  value="<?php echo $category->term_id; ?>">
    <div class="create_form-group">
        <label for="event_add_property_object_name"><?php _e('Property object name','tainacan'); ?></label>
        <input type="text" class="form-control" id="single_event_add_property_object_name" name="socialdb_event_property_object_create_name" required="required" placeholder="<?php _e('Property Object name','tainacan'); ?>">
    </div>
    <div class="form-group"> 
            <label for="event_add_property_object_category_id"><?php _e('Property object relationship','tainacan'); ?></label>
            <div id="property_category_dynatree" style="height: 300px;overflow-y: scroll;" >
                                    </div>
             <input required="required" type="hidden"  id="property_object_category_id"  name="socialdb_event_property_object_create_category_id" value="<?php //echo $category->term_id; ?>" >
    </div>
    <!--div class="form-group">
        <label for="event_add_property_object_required"><?php _e('Property object facet','tainacan'); ?></label>
        <input type="radio" name="socialdb_event_property_object_create_is_facet" id="single_event_add_property_object_facet_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
        <input type="radio" name="socialdb_event_property_object_create_is_facet" id="single_event_add_property_object_facet_false" checked="checked" value="false">&nbsp;<?php _e('No','tainacan'); ?>
    </div-->
    <div class="form-group">
        <label for="event_add_property_object_required"><?php _e('Property object required','tainacan'); ?></label>
        <input type="radio" name="socialdb_event_property_object_create_required" id="single_event_add_property_object_required_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
        <input type="radio" name="socialdb_event_property_object_create_required" id="single_event_add_property_object_required_false" checked="checked" value="false">&nbsp;<?php _e('No','tainacan'); ?>
    </div>
    <div class="form-group">
        <label for="event_add_property_object_is_reverse"><?php _e('Property object reverse','tainacan'); ?></label>
        <input type="radio" name="socialdb_event_property_object_create_is_reverse" id="single_event_add_property_object_is_reverse_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
        <input type="radio" name="socialdb_event_property_object_create_is_reverse" id="single_event_add_property_object_is_reverse_false" checked="checked" value="false">&nbsp;<?php _e('No','tainacan'); ?>
    </div>
    <div id="single_event_add_show_reverse_properties" class="form-group" style="display: none;">
        <label for="event_add_property_object_reverse"><?php _e('Select the reverse property','tainacan'); ?></label>
        <select class="form-control" id="single_event_add_property_object_reverse" name="socialdb_event_property_object_create_reverse">
        </select>
    </div>
    <?php do_action('form_modify_property_object') ?>
    <input type="hidden" id="single_event_add_property_object_collection_id" name="socialdb_event_collection_id" value="<?php echo $collection_id; ?>">
    <input type="hidden"  name="socialdb_event_property_object_create_cardinality" value="n">
    <input type="hidden" id="single_event_add_property_object_id" name="property_object_id" value="<?php echo $object_id; ?>">
    <input type="hidden" id="single_event_add_property_object_create_time" name="socialdb_event_create_date" value="<?php echo mktime(); ?>">
    <input type="hidden" id="single_event_add_property_object_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>">
    <input type="hidden" id="single_operation_property_object" name="operation" value="add_event_property_object_create">
    <button type="submit" id="submit" class="btn btn-primary pull-right" style="margin-left: 5px;">
        <?php _e('Submit','tainacan'); ?>
    </button>

    <button type="button" onclick="back_button_single('<?php echo $object_id; ?>')" class="btn btn-default pull-right" id="clear_categories">
        <?php _e('Cancel','tainacan'); ?>
    </button>
    <br>
</form>