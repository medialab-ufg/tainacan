<?php

class AutoIncrementClass extends FormItemMultiple{
     public function generate($compound,$property,$item_id,$index_id) {
        $compound_id = $compound['id'];
        $property_id = $property['id'];
        if ($property_id == 0) {
            $property = $compound;
        }
        ?>
        <input 
            disabled="disabled"  
            type="number" 
            id="autoincrement-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
            class="form-control" 
            value="">
        <?php
        $this->initScriptsIncrementClass($compound_id,$property_id, $item_id, $index_id);
    }
    
    /**
     * 
     * @param type $property
     * @param type $item_id
     * @param type $index
     */
    public function initScriptsIncrementClass($compound_id,$property_id, $item_id, $index_id) {
        ?>
        <script>
            $('#autoincrement-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').keyup(function(){
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
            
            Hook.register(
            'get_single_item_value',
            function ( args ) {
                $.ajax({
                    url: $('#src').val() + '/controllers/object/form_item_controller.php',
                    type: 'POST',
                    data: {
                        operation: 'getDataValue',
                        compound_id:'<?php echo $compound_id ?>',
                        property_children_id: '<?php echo $property_id ?>',
                        index: <?php echo $index_id ?>,
                        item_id:args[0]
                    }
                }).done(function (result) {
                    var json = JSON.parse(result);
                    if(json.value){
                        $('#item_source').val(json.value.join(','));
                    }
                });
            });
        </script> 
        <?php
    }
}
