<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/edit_object_property_form_js.php');
?>
<h4><?php echo __('Editing the property: ','tainacan').$value->name; ?></h4>
<form  id="single_submit_form_event_edit_property_object">
    <input type="hidden" name="property_category_id"  value="<?php echo $category->term_id; ?>">
    <div class="create_form-group">
        <label for="event_edit_property_object_name"><?php _e('Property object name','tainacan'); ?></label>
        <input type="text" class="form-control" id="single_event_edit_property_object_name" name="socialdb_event_property_object_edit_name" value="<?php echo $value->name; ?>" required="required" placeholder="<?php _e('Property Object name','tainacan'); ?>">
    </div>
    <div class="form-group">
         <div class="form-group"> 
            <label for="event_add_property_object_category_id"><?php _e('Property object relationship','tainacan'); ?></label>
            <div id="property_category_dynatree" style="height: 300px;overflow-y: scroll;" >
                                    </div>
             <input required="required" type="hidden"  id="property_object_category_id"  name="property_object_category_id" value="<?php echo $category->term_id; ?>" >
    </div>
    <!--div class="form-group">
        <label for="event_edit_property_object_required"><?php _e('Property object facet','tainacan'); ?></label>
        <input type="radio"  <?php if($value->metas->socialdb_property_object_is_facet=='true') echo 'checked="checked";' ?> name="socialdb_event_property_object_edit_is_facet" id="single_event_edit_property_object_facet_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
        <input type="radio" <?php if($value->metas->socialdb_property_object_is_facet=='false') echo 'checked="checked";' ?> name="socialdb_event_property_object_edit_is_facet" id="single_event_edit_property_object_facet_false"  value="false">&nbsp;<?php _e('No','tainacan'); ?>
    </div-->
    <div class="form-group">
        <label for="event_edit_property_object_required"><?php _e('Property object required','tainacan'); ?></label>
        <input type="radio" <?php if($value->metas->socialdb_property_required=='true') echo 'checked="checked";' ?> name="socialdb_event_property_object_edit_required" id="single_event_edit_property_object_required_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
        <input type="radio" <?php if($value->metas->socialdb_property_required=='false') echo 'checked="checked";' ?> name="socialdb_event_property_object_edit_required" id="single_event_edit_property_object_required_false"  value="false">&nbsp;<?php _e('No','tainacan'); ?>
    </div>
    <div class="form-group">
        <label for="event_edit_property_object_is_reverse"><?php _e('Property object reverse','tainacan'); ?></label>
        <input type="radio" <?php if($value->metas->socialdb_property_object_is_reverse=='true') echo 'checked="checked";' ?> name="socialdb_event_property_object_edit_is_reverse" id="single_event_edit_property_object_is_reverse_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
        <input type="radio" <?php if($value->metas->socialdb_property_object_is_reverse=='false') echo 'checked="checked";' ?> name="socialdb_event_property_object_edit_is_reverse" id="single_event_edit_property_object_is_reverse_false"  value="false">&nbsp;<?php _e('No','tainacan'); ?>
    </div>
    <div id="single_event_edit_show_reverse_properties" class="form-group" style="display: none;">
        <label for="event_edit_property_object_reverse"><?php _e('Select the reverse property','tainacan'); ?></label>
        <select class="form-control" id="single_event_edit_property_object_reverse" name="socialdb_event_property_object_edit_reverse">
        </select>
    </div>
    <?php do_action('form_modify_property_object') ?>
    <!-- valor do checkbox do reverse para montar o html --> 
   <input type="hidden" id="single_event_edit_property_object_is_reverse_value" name="event_edit_property_object_is_reverse_value" value="<?php echo $value->metas->socialdb_property_object_is_reverse ?>">
   <!-- valor do reverse para montar o html --> 
    <input type="hidden" id="single_event_edit_property_object_create_time" name="socialdb_event_create_date" value="<?php echo mktime(); ?>"><!-- O momento de criacao do evento -->
    <input type="hidden" id="single_event_edit_property_object_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>"><!-- o id do usuario -->
    <input type="hidden" id="single_event_edit_property_object_reverse_value" name="event_edit_property_object_reverse_value" value="<?php echo $value->metas->socialdb_property_object_reverse ?>"><!-- VALOR ANTERIOR DA PROPRIEDADE REVERSA -->
    <input type="hidden" id="single_event_edit_property_object_collection_id" name="socialdb_event_collection_id" value="<?php echo $collection_id; ?>"><!-- ID DA COLECAO -->
    <input type="hidden" id="single_event_edit_property_object_id" name="socialdb_event_property_object_edit_id" value="<?php  echo $value->id; ?>"><!-- ID DA PROPRIEDADE -->
    <input type="hidden" id="single_event_edit_property_object_post_id" name="event_edit_property_object_post_id" value="<?php  echo $object_id; ?>"><!-- ID DO OBJETO EM QUESTAO -->
    <input type="hidden" id="operation_event_edit_property_object" name="operation" value="add_event_property_object_edit"><!-- OPERACAO -->
    <button type="submit" id="submit" class="btn btn-default"><?php _e('Submit','tainacan'); ?></button>
    <button type="button" onclick="back_button_sinlge('<?php echo $object_id; ?>')" cla ss="btn btn-default" id="clear_categories"><?php _e('Back','tainacan'); ?></button>
</form>