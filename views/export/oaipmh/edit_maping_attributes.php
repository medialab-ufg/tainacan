<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/edit_maping_attributes_js.php');
?>
<div class='panel panel-default'>
    <div id="importForm" class='panel-body'>
        <form id="form_import"> 
        <div class="form-group">
            <!--label for="library_type"><?php _e('Import OAI-PMH','tainacan'); ?></label><br-->
            <input type='hidden' id='mapping_id' name="mapping_id" value='<?php echo $mapping_id; ?>'>
            <input type="radio" id="edit_export_object_true" name="export_object"  checked="checked"   value="true"><?php _e('Export files','tainacan'); ?><br>
            <input type="radio" id="edit_export_object_false" name="export_object" value="false"><?php _e('Not Export files','tainacan'); ?><br>
        </div>
        <?php  foreach ($fields as $field) { ?>
            <div class='form-group col-md-12 no-padding'>
                <label class='col-md-4 no-padding'>
                    <?php echo $field['name_socialdb_entity'] ?>
                </label>    
                <div class='col-md-4'>
                   <select name='<?php echo $field['socialdb_entity'] ?>' class='data form-control' id='<?php echo $field['socialdb_entity'] ?>'>
                   </select>
                </div>	
                 <div class='col-md-4'>
                     <input type="text" class='form-control' placeholder="<?php _e('Set the qualifier (Optional)','tainacan') ?>" name="qualifier_socialdb_<?php echo $field['socialdb_entity'] ?>" value="">
                </div>
            </div>		
        <?php } ?>
        </form> 
    </div>
    <div class="col-md-12 no-padding" style="margin-top: 20px">
        <button id="cancel_button_import" class="btn btn-default" onclick="cancel_export()"><?php echo __('Cancel','tainacan'); ?></button>
        <button id="submit_button_import" class="btn btn-primary pull-right tainacan-blue-btn-bg" onclick="update_mapping_export()"><?php echo __('Update','tainacan'); ?></button>
    </div>
