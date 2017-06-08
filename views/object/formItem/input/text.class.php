<?php

class TextClass extends FormItem{
    
    /**
     * 
     * @param type $compound
     * @param type $property_id
     * @param type $item_id
     * @param type $index_id
     */
    public function generate($compound,$property_id,$item_id,$index_id) {
        ?>
        <input  type="text" 
                item="<?php echo $item_id ?>"
                id="text-field-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>" 
                class="form-control text-field-<?php echo $compound['id'] ?>-<?php echo $property_id ?>" 
                value="<?php ?>"
                name="socialdb_property_<?php echo $compound['id']; ?>[]" >
        <?php
        $this->initScriptsTextClass($compound['id'],$property_id, $item_id, $index_id);
    }
    
    
    /**
     * 
     * @param type $property
     * @param type $item_id
     * @param type $index
     */
    public function initScriptsTextClass($compound_id,$property_id, $item_id, $index_id) {
        ?>
        <script>
            $('#text-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').keyup(function(){
                $.ajax({
                    url: $('#src').val() + '/controllers/object/form_item_controller.php',
                    type: 'POST',
                    data: {
                        operation: 'saveValue',
                        type:'data',
                        value: $(this).val(),
                        item_id:'<?php echo $item_id ?>',
                        compound_id:'<?php echo $compound_id ?>',
                        property_children_id: '<?php echo $property_id ?>',
                        index: <?php echo $index_id ?>,
                        indexCoumpound: 0
                    }
                }).done(function (result) {
                
                });
            });
        </script> 
        <?php
    }
}
