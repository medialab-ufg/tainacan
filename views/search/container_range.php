<?php include_once ('js/container_range_js.php'); 
if($type=='textarea'){
    //$type = 'text';
}  
if($type=='date'){
    //$type = 'text';
}  

if($type=='numeric'){
   $type = 'number' ;
}
?>
<div class='row form-group' id="range_<?php echo $counter ?>">
    <label class='col-md-5'>
        <input maxlength="18" onkeypress="isNumber(this.id, event)"
               type="<?php echo ($form_type ) ? $form_type : $type; ?>"
               class='form-control' placeholder="<?php _e('Range FROM value', 'tainacan') ?>"
               id="range_<?php echo $counter ?>_1" name="range_<?php echo $counter ?>_1" value="">
    </label>    
    <label class='col-md-1'> <p> <?php _e('to','tainacan') ?> </p> </label>
    <label class='col-md-5'>
        <input maxlength="18"  onkeypress="isNumber(this.id, event)"
               type="<?php echo ($form_type) ? $form_type : $type; ?>"
               class='form-control' placeholder="<?php _e('Range TO value', 'tainacan') ?>"
               id="range_<?php echo $counter ?>_2" name="range_<?php echo $counter ?>_2" value="">
    </label> 
    <label class='col-md-1'>
        <button type="button" onclick="remove_range('<?php echo $counter ?>')"><span class="glyphicon glyphicon-remove"></span></button>
    </label> 
</div>		