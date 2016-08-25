<?php
include_once ('../../helpers/view_helper.php');
include_once ('../../helpers/mapping/mapping_helper.php');
include_once ( dirname(__FILE__).'/js/container_mapping_js.php');
$helper = new MappingHelper;
?>
<form id="submit_mapping_handle" >
    <div class="col-md-12" style="margin: 15px;padding-right: 48px;">
         <button 
            type="button"
            class="btn btn-default pull-left" 
            data-dismiss="modal" 
            aria-label="Close"><?php _e('Close', 'tainacan') ?></button>
        <button 
            type="submit"
            class="btn btn-primary pull-right"><?php _e('Save', 'tainacan') ?></button>
    </div>    
    <div class="col-md-12">
        <!--br>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button-->
        <?php
        $helper->generate_mapping_table($tainacan_properties, $generic_properties);
        ?>
    </div>
    <div class="col-md-12" style="margin-bottom: 15px;">
        <input type="hidden" name="url" value="<?php echo $url; ?>">
        <input type="hidden" name="collection_id" value="<?php echo $collection_id; ?>">
        <input type="hidden" name="operation" value="submit_mapping_metatags">
    </div>
</form>    