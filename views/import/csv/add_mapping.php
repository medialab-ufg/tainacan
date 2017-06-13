<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/maping_attributes_js.php');
?>
<div class='panel panel-default'>
    <div id="importForm" class='panel-body'>
        <form id="form_import_csv_mapping"> 
            <div class="form-group">
                <input type="hidden" id="socialdb_csv_mapping_id" name="socialdb_csv_mapping_id" value="<?php echo $mapping_id; ?>">
            </div>
             <?php foreach ($csv_data as $key=>$csv) { ?>
            <div class='row form-group'>
                <label class='col-md-4'>
                    <?php echo utf8_encode($csv); ?>
                </label>    
                <div class='col-md-8'>
                    <select name='<?php echo 'csv_p'.$key; ?>' class='data form-control' id='<?php echo 'csv_p'.$key; ?>'>

                    </select>
                </div>	  
            </div>		
        <?php } ?>
            <button type="button" id="submit_button_import_csv" class="btn btn-primary" onclick="save_csv_mapping()"><?php echo __('Save','tainacan'); ?></button>
        </form> 
        <div id="tmp_div_all_metas"></div>
    </div>
    
