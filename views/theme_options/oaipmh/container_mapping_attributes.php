<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/container_mapping_attributes_js.php');
?>
<div class='row form-group' id="tag_<?php echo $counter ?>">
    <label class='col-md-2'>
        <select name='mapping_dublin_core_<?php echo $counter ?>' class='data_dubin_core_<?php echo $counter ?> form-control' id='mapping_dublin_core_<?php echo $counter ?>'>

        </select>
    </label>    
    <label class='col-md-3'>
        <input type="text" class='form-control' placeholder="<?php _e('Set the qualifier (Optional)','tainacan') ?>" name="qualifier_<?php echo $counter ?>" value="">
    </label> 
    <div class='col-md-5'>
        <select name='mapping_socialdb_<?php echo $counter ?>' class='data_<?php echo $counter ?> form-control' >

        </select>
    </div>
    <label class='col-md-2'>
        <button type="button" onclick="remove_tag_oai_dc('<?php echo $counter ?>')"><span class="glyphicon glyphicon-remove"></span> <?php _e('Remove Tag','tainacan') ?> </button>
    </label> 
</div>		