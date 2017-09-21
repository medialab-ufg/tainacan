<?php
include_once ('js/mapping_attributes_js.php');
?>
<div class='panel panel-default'>
    <div id="importForm" class='panel-body'>
        <form id="form_import"> 
        <center><h3><?php _e('Objects Found: ','tainacan') ?> <?php echo $number_of_objects; ?></h3></center>
        <input type='hidden' id='all_size' name="all_size" value='<?php echo $number_of_objects; ?>'>
        <input type='hidden' id='tokenUrl' name="tokenUrl" value='<?php echo $token; ?>'>
        <input type='hidden' id='sets' name="sets" value='<?php if(isset($sets)) echo $sets; ?>'>
        <input type='hidden' name="collection_id" value='<?php echo get_option('collection_root_id'); ?>'>
        <input type='hidden' id='counter_oai_dc' name="counter_oai_dc" value=''>
        <br>
        <input type="checkbox" id="import_object_repository" name="import_object" value="true" ><?php _e('Import object','tainacan'); ?><br>
        <!--label for="library_type"><?php _e('Import OAI-PMH','tainacan'); ?></label><br-->
        
        <div id="mapping_attributes_oai_dc"></div>
        <button type="button" onclick="appendMapping()"><span class="glyphicon glyphicon-plus"></span><?php _e('Add more tags','tainacan') ?></button>
        
        </form>
        <input type="hidden" name="error" id="validate_oai_pmh_dc_error" value="<?php echo $error ?>">
    </div>
    <input type='hidden' value='<?php echo $whole_metadatas; ?>' id='metadatas'>
    <button id="cancel_button_import" class="btn btn-default" onclick="cancel_import()"><?php echo __('Cancel','tainacan'); ?></button>
    <button id="submit_button_import" class="btn btn-primary" onclick="save_mapping('<?php echo $url; ?>')"><?php echo __('Save','tainacan'); ?></button>
  