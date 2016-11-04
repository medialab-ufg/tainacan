<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/mapping_attributes_js.php');
$counter = 0;
?>
<?php if(!$mapping_array): ?>
    <center><h3><?php _e('Invalid URL or no metatags found!','tainacan') ?></h3></center>
    <button id="cancel_button_import" class="btn btn-default btn-lg" onclick="cancel_import_metatags()"><?php echo __('Back','tainacan'); ?></button>
<?php else: ?>
<div class='panel panel-default'>
    <div id="importForm" class='panel-body'>
        <form id="form_import_metags"> 
            <input type='hidden' id='metatags_mapping_url' name="url" value='<?php echo $url; ?>'>
            <input type='hidden' id='metatags_counter_oai_dc_edit' name="counter_oai_dc" value='<?php echo count($mapping_array); ?>'>
            <?php  while($counter < count($mapping_array)):  ?>
                <div class='row form-group' id="edit_tag_<?php echo $counter ?>">
                    <label class='col-md-5'>
                        <input type="hidden" class='data_dubin_core' name="mapping_metatags_<?php echo $counter ?>" value="<?php echo $mapping_array[$counter]['name_field'] ?>">
                        <select disabled="disabled" class='data_dubin_core form-control' name='select_mapping_metatags_<?php echo $counter ?>'  id='mapping_dublin_core_<?php echo $counter ?>'>
                            <option value="<?php echo $mapping_array[$counter]['name_field']  ?>"><?php echo $mapping_array[$counter]['name_field']  ?></option>
                        </select>
                    </label>   
                    <div class='col-md-5'>
                        <select name='mapping_socialdb_<?php echo $counter ?>' class='data form-control' >

                        </select>
                    </div>
                    <label class='col-md-2'>
                        <button type="button" onclick="remove_tag_metatags('<?php echo $counter ?>')"><span class="glyphicon glyphicon-remove"></span> <?php _e('Remove Tag','tainacan') ?> </button>
                    </label> 
                </div>	
            <?php $counter++; ?>
            <?php endwhile; ?>
        </form> 
    </div>
    <input type='hidden' value='<?php echo $whole_metadatas; ?>' id='metadatas'>
    <button id="cancel_button_import" class="btn btn-default" onclick="cancel_import_metatags()"><?php echo __('Cancel','tainacan'); ?></button>
    <button id="submit_button_import" class="btn btn-primary" onclick="save_mapping_metatags()"><?php echo __('Save','tainacan'); ?></button>
<?php endif; ?>