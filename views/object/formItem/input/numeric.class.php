<?php

class NumericClass extends FormItem{
    public function generate($compound, $property_id, $item_id, $index_id) {
        $compound_id = $compound['id'];
        ?>
         <input type="text" 
                id="numeric-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>" 
                value="" 
                class="form-control auto-save form_autocomplete_value_<?php echo $property_id; ?>" 
                onkeypress='return onlyNumbers(event)'
                name="socialdb_property_<?php echo $property_id; ?>[]">
        <?php
        $this->initScriptsNumericClass($compound_id,$property_id, $item_id, $index_id);
    }
    
    /**
     * 
     * @param type $property
     * @param type $item_id
     * @param type $index
     */
    public function initScriptsNumericClass($compound_id,$property_id, $item_id, $index_id) {
        ?>
        <script>
            $('#numeric-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').keyup(function(){
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
