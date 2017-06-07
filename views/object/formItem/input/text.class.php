<?php

class TextClass extends FormItem{
    public function generate($compound,$property_id,$item_id,$index_id) {
        ?>
        <input  type="text" 
                item="<?php echo $item_id ?>"
                id="text-field-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>" 
                class="form-control text-field-<?php echo $compound['id'] ?>-<?php echo $property['id'] ?>" 
                value="<?php ?>"
                name="socialdb_property_<?php echo $property['id']; ?>[]" >
        <?php
        $this->initScriptsTextClass($compound['id'], $item_id, $index_id);
    }
    
    
    /**
     * 
     * @param type $property
     * @param type $item_id
     * @param type $index
     */
    public function initScriptsTextClass($property_id, $item_id, $index) {
        ?>
        <script>
            var index = <?php echo $index; ?>;

            $('.js-append-property-<?php echo $property['id'] ?>').keyup(function(){
                console.log(<?php echo $item_id ?>,<?php echo $index ?>);
                $.ajax({
                    url: $('#src').val() + '/controllers/object/form_item_controller.php',
                    type: 'POST',
                    data: {
                        collection_id: $("#collection_id").val(),
                        operation: 'saveValue',
                        type:'data',
                        item_id:'<?php echo $item_id ?>',
                        property_children_id: '<?php echo $property_id ?>',
                        index: <?php echo $index ?>
                    }
                }).done(function (result) {
                    $('#meta-item-<?php echo $property['id']; ?> #appendTextContainer').append(result);
                });
            });
        </script> 
        <?php
    }
}
