<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/edit_maping_attributes_js.php');
?>
<div class='panel panel-default'>
    <div id="importForm" class='panel-body'>
        <form id="form_import_csv_edit_mapping"> 
            <div class="form-group">
                <input type="hidden" id="socialdb_csv_mapping_id" name="socialdb_csv_mapping_id" value="<?php echo $mapping_id; ?>">
            </div>
            <?php
                if(isset($csv_data) && !empty($csv_data))
                {
                    $csv_data = array_filter($csv_data, function($value) {
                        return !empty($value) || $value === 0;
                    });

                    asort($csv_data, SORT_STRING);
                    foreach ($csv_data as $key => $csv) {
                ?>
                    <div class='row form-group'>
                        <label class='col-md-4'>
                            <?php
                            if(mb_detect_encoding($csv) !== 'UTF-8')
                                echo utf8_encode($csv);
                            else echo $csv;
                            ?>
                        </label>
                        <div class='col-md-8'>
                            <select name='<?php echo 'csv_p' . $key; ?>' class='data form-control' id='<?php echo 'csv_p' . $key; ?>'>

                            </select>
                        </div>
                    </div>
            <?php }
                }?>

            <button id="cancel_button_import_csv" type="button" class="btn btn-default" onclick="cancel_import_csv()"><?php echo __('Cancel','tainacan'); ?></button>
            <button type="button" id="submit_button_import_csv_edit" class="btn btn-primary" onclick="update_mapping()"><?php echo __('Save','tainacan'); ?></button>
        </form> 
    </div>

