<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/maping_attributes_js.php');
?>
<div class='panel panel-default'>
    <div id="importForm_csv" class='panel-body'>
        <form id="form_import_csv_delimit"> 
            <div class="form-group">
                <label for="library_type"><?php _e('Set Delimiter','tainacan'); ?></label><br>
                <input type="hidden" id="socialdb_csv_mapping_id" name="socialdb_csv_mapping_id" value="<?php echo $mapping_id; ?>">
                <input type="text" id="socialdb_delimiter_csv" name="socialdb_delimiter_csv" value=";" required="required">
            </div>
            <h5><strong><?php _e('CSV Header','tainacan'); ?></strong></h5>
            <div class="form-group">
                <label class="radio-inline">
                    <input type="radio" name="socialdb_csv_has_header" id="socialdb_csv_has_header_yes" value="1" checked> <?php _e('Yes','tainacan'); ?>
                </label>
                <label class="radio-inline">
                    <input type="radio" name="socialdb_csv_has_header" id="socialdb_csv_has_header_no" value="0"> <?php _e('No','tainacan'); ?>
                </label>
            </div>
            <button type="button" id="submit_button_import_csv" class="btn btn-primary" onclick="save_csv_delimiter()"><?php echo __('Save','tainacan'); ?></button>
        </form> 
    </div>
    <div id="add_mapping_csv"></div>
    
