<?php
include_core_wp();
include_once ('js/mapping_attributes_js.php');
$fields_validate = [];
?>
<div class='panel panel-default'>
    <div id="importForm" class='panel-body'>
        <form id="form_import"> 
        <div class="form-group">
            <!--label for="library_type"><?php _e('Import OAI-PMH','tainacan'); ?></label><br-->
            <input type="radio" name="export_object"  value="true"><?php _e('Export files','tainacan'); ?><br>
            <input type="radio" name="export_object" checked="checked"  value="false"><?php _e('Not Export files','tainacan'); ?><br>
        </div>
        <?php foreach ($fields as $field) {
            $fields_validate[] = $field['socialdb_entity'];
            ?>
            <div class='form-group col-md-12 no-padding'>
                <label class='col-md-4 no-padding item-title'>
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

    <input type='hidden' value='<?php echo implode(',', $fields_validate); ?>' id='metadatas_export'>

    <div class="col-md-12 no-padding" style="margin-top: 20px">
        <button id="cancel_button_import" class="btn btn-default" onclick="cancel_export()"><?php echo __('Cancel','tainacan'); ?></button>
        <button id="submit_button_import" class="btn btn-primary pull-right tainacan-blue-btn-bg" onclick="save_mapping_export('<?php echo $url; ?>')"><?php echo __('Save','tainacan'); ?></button>
    </div>
