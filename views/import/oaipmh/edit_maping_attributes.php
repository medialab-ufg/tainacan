<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/edit_maping_attributes_js.php');
$counter = 0;
?>
<div class='panel panel-default'>
    <div id="importForm" class='panel-body'>
        <form id="form_import"> 
            <center><h3><?php _e('Objects Found: ','tainacan') ?> 
                    <?php if ($sets): ?>
                        <?php echo __('( with the set ','tainacan') . $sets . ') '; ?> 
                    <?php endif; ?>    

                    <b><?php echo $number_of_objects; ?></b></h3></center>
            <input type='hidden' id='mapping_id' name="mapping_id" value='<?php echo $mapping_id; ?>'>
            <input type='hidden' id='all_size' name="all_size" value='<?php echo $number_of_objects; ?>'>
            <input type='hidden' id='tokenUrl' name="tokenUrl" value='<?php echo $token; ?>'>
            <input type='hidden' id='counter_oai_dc_edit' name="counter_oai_dc" value='<?php echo count($mapping_array['mapping']); ?>'>
            <div class="form-group">
                <!--label for="library_type"><?php _e('Import OAI-PMH','tainacan'); ?></label><br-->
                <input type="radio" name="import_object" id="edit_import_object_true" onclick="show_message_size()"  value="true"><?php _e('Import object','tainacan'); ?><br>
                <input type="radio" name="import_object" id="edit_import_object_false" checked="checked"  value="false"><?php _e('Import only metadata','tainacan'); ?><br>
            </div>
            <?php  while($counter < count($mapping_array['mapping'])): $counter++ ?>
                <div class='row form-group' id="edit_tag_<?php echo $counter ?>">
                    <label class='col-md-2'>
                        <select name='mapping_dublin_core_<?php echo $counter ?>' class='data_dubin_core form-control' id='mapping_dublin_core_<?php echo $counter ?>'>

                        </select>
                    </label>    
                    <label class='col-md-3'>
                        <input type="text" class='form-control' placeholder="<?php _e('Set the qualifier (Optional)','tainacan') ?>" name="qualifier_<?php echo $counter ?>" value="">
                    </label> 
                    <div class='col-md-5'>
                        <select name='mapping_socialdb_<?php echo $counter ?>' class='data form-control' >

                        </select>
                    </div>
                    <label class='col-md-2'>
                        <button type="button" onclick="remove_tag_oai_dc_edit('<?php echo $counter ?>')"><span class="glyphicon glyphicon-remove"></span> <?php _e('Remove Tag','tainacan') ?> </button>
                    </label> 
                </div>	
            <?php endwhile; ?>
            <div id="edit_mapping_attributes_oai_dc"></div>
            <button type="button" onclick="editAppendMapping()"><span class="glyphicon glyphicon-plus"></span><?php _e('Add more tags','tainacan') ?></button>
            <!--
            <?php foreach ($metadatas as $metadata) { ?>
                        <div class='row form-group'>
                            <label class='col-md-4'>
                                &lt;<?php echo $metadata['name_tag'] ?>
                <?php if ($metadata['has_attribute']) { ?>
                                            &nbsp;&nbsp;<?php echo $metadata['attributes']['name'] ?>="<?php echo $metadata['attributes']['value'] ?>"&nbsp;&nbsp;
                <?php } ?>
                                &gt&nbsp;&nbsp;<?php echo $metadata['name_field'] ?>&nbsp;&nbsp;&lt;dc:<?php echo $metadata['name_inside_tag'] ?>&gt
                            </label>    
                            <div class='col-md-8'>
                                <select name='<?php echo $metadata['name_on_select'] ?>' class='data form-control' id='<?php echo $metadata['name_inside_tag'] ?>'>
            
                                </select>
                            </div>	  
                        </div>		
            <?php } ?>
            -->
        </form> 
    </div>
    <input type='hidden' value='<?php echo $whole_metadatas; ?>' id='metadatas'>
    <button id="cancel_button_import" class="btn btn-default" onclick="cancel_import()"><?php echo __('Cancel','tainacan'); ?></button>
    <button id="submit_button_import" class="btn btn-primary" onclick="update_mapping('<?php echo $url; ?>')"><?php echo __('Update','tainacan'); ?></button>
