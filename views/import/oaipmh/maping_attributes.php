<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/maping_attributes_js.php');
?>
<div class='panel panel-default'>
    <div id="importForm" class='panel-body'>
        <form id="form_import"> 
        <center><h3><?php _e('Objects Found: ','tainacan') ?> <?php echo $number_of_objects; ?></h3></center>
        <input type='hidden' id='all_size' name="all_size" value='<?php echo $number_of_objects; ?>'>
        <input type='hidden' id='tokenUrl' name="tokenUrl" value='<?php echo $token; ?>'>
        <input type='hidden' id='sets' name="sets" value='<?php if(isset($sets)) echo $sets; ?>'>
        <input type='hidden' id='counter_oai_dc' name="counter_oai_dc" value=''>
        <div class="form-group">
            <!--label for="library_type"><?php _e('Import OAI-PMH','tainacan'); ?></label><br-->
            <input type="radio" name="import_object" onclick="show_message_size()"  value="true"><?php _e('Import object','tainacan'); ?><br>
            <input type="radio" name="import_object" checked="checked"  value="false"><?php _e('Import only metadata','tainacan'); ?><br>
        </div>
        <div id="mapping_attributes_oai_dc"></div>
        <button type="button" onclick="appendMapping()"><span class="glyphicon glyphicon-plus"></span><?php _e('Add more tags','tainacan') ?></button>
            
        
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
        </div-->
        </form>
        <input type="hidden" name="error" id="validate_oai_pmh_dc_error" value="<?php echo $error ?>">
    </div>
    <input type='hidden' value='<?php echo $whole_metadatas; ?>' id='metadatas'>
    <button id="cancel_button_import" class="btn btn-default" onclick="cancel_import()"><?php echo __('Cancel','tainacan'); ?></button>
    <button id="submit_button_import" class="btn btn-primary" onclick="save_mapping('<?php echo $url; ?>')"><?php echo __('Save','tainacan'); ?></button>
  