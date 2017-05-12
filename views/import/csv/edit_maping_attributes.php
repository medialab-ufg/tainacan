<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/maping_attributes_js.php');
?>
<div class='panel panel-default'>
    <div id="importForm_csv" class='panel-body'>
        <form id="form_import_csv_delimit" name="form_import_csv_delimit" enctype="multipart/form-data" method="post"> 
            <div class="form-group">
                <input type="hidden" id="socialdb_csv_mapping_id" name="socialdb_csv_mapping_id" value="<?php echo $mapping_id; ?>">
                <label for="socialdb_delimiter_csv"><?php _e('Fields delimiter','tainacan'); ?></label><br>
                <input type="text" id="socialdb_delimiter_csv" name="socialdb_delimiter_csv" value=";" required="required" class="form-control">
            </div>
            <div class="form-group">
                <label for="socialdb_delimiter_multi_values_csv"><?php _e('Multi-values delimiter','tainacan'); ?></label><br>
                <input type="text" id="socialdb_delimiter_multi_values_csv" name="socialdb_delimiter_multi_values_csv" 
                       value="<?php echo ($socialdb_channel_csv_multi_values) ? $socialdb_channel_csv_multi_values : '' ?>" 
                       required="required" class="form-control">
            </div>
            <div class="form-group">
                <label for="socialdb_delimiter_hierarchy_csv"><?php _e('Hierarchy delimiter','tainacan'); ?></label><br>
                <input type="text" 
                       id="socialdb_delimiter_hierarchy_csv" 
                       name="socialdb_delimiter_hierarchy_csv" 
                       value="<?php echo ($socialdb_channel_csv_hierarchy) ? $socialdb_channel_csv_hierarchy : '' ?>" 
                       required="required" 
                       class="form-control">
            </div>
            <div class="form-group">
                <label for="socialdb_delimiter_code_csv"><?php _e('File Encoding','tainacan'); ?></label><br>
                <select id="socialdb_delimiter_code_csv" name="socialdb_delimiter_code_csv"  class="form-control">
                    <option <?php echo (!isset($socialdb_channel_csv_code) || !$socialdb_channel_csv_code || $socialdb_channel_csv_code == 'utf8') ? 'selected="selected"' : '' ?> value="utf8">UTF8</option>  
                    <option  <?php echo ($socialdb_channel_csv_code && $socialdb_channel_csv_code == 'iso8859-1') ? 'selected="selected"' : '' ?> value="iso8859-1">ISO 8859-1</option>   
                </select>
            </div>
            <div class="form-group">
                <input type="checkbox" 
                       name="import_url_external" 
                       value="url_externa" 
                       <?php echo ($socialdb_channel_csv_import_zip=='url_externa') ? 'checked="checked"' : '' ?>
                       onchange="hide_zip_input()"> <?php _e('Import external URL file (must map the content)', 'tainacan'); ?><br><br>
            </div>
            <h5><strong><?php _e('CSV Header','tainacan'); ?></strong></h5>
            <div class="form-group">
                <label class="radio-inline">
                    <input type="radio" 
                           name="socialdb_csv_has_header" 
                           <?php echo ($socialdb_channel_csv_has_header=='1') ? 'checked="checked"' : '' ?>
                           id="socialdb_csv_has_header_yes" 
                           value="1"> <?php _e('Yes','tainacan'); ?>
                </label>
                <label class="radio-inline">
                    <input type="radio" 
                           name="socialdb_csv_has_header" 
                           <?php echo ($socialdb_channel_csv_has_header=='0') ? 'checked="checked"' : '' ?>
                           id="socialdb_csv_has_header_no" 
                           value="0"> <?php _e('No','tainacan'); ?>
                </label>
            </div>
            <h5><strong><?php _e('Alter File','tainacan') ?></strong>&nbsp;&nbsp;<small><?php  _e('This action will replace the previous file','tainacan') ?></small></h5>
            <div class="form-group">
                <input type="file" accept=".csv,.zip" id="csv_file" name="csv_file" placeholder="<?php _e('Insert the CSV file', 'tainacan'); ?>">
            </div>
            <input type="hidden" id="socialdb_csv_mapping_operation" name="operation" value="saving_delimiter_header_csv">
            <input type="hidden" id="socialdb_csv_mapping_collection_id" name="collection_id" value="">
            <input type="hidden" id="socialdb_csv_is_editting" name="socialdb_csv_is_editting" value="true">
            <button id="cancel_button_import_csv" type="button" class="btn btn-default" onclick="cancel_import_csv()"><?php echo __('Cancel','tainacan'); ?></button>
            <button type="submit" id="submit_button_import_csv" class="btn btn-primary" ><?php echo __('Save','tainacan'); ?></button> <!--onclick="save_csv_delimiter()"-->
        </form> 
    </div>
    <div id="add_mapping_csv"></div>
    
