<?php

class SelectboxClass extends FormItem{
    public function generate($compound,$property,$item_id,$index_id) {
        $compound_id = $compound['id'];
        $property_id = $property['id'];
        if ($property_id == 0) {
            $property = $compound;
        }
        ?>
        <select class="form-control auto-save" 
                id='selectbox-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>' >
            <option value=""><?php _e('Select','tainacan') ?>...</option>
            <?php if($property['has_children'] && is_array($property['has_children'])): ?>
                <?php foreach ($property['has_children'] as $child): ?>
                    <option value="<?php echo $child->term_id ?>"><?php echo $child->name ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <?php
        $this->initScriptsSelectBoxClass($compound_id, $property_id, $item_id, $index_id);
    }
    
    /**
     * 
     * @param type $property
     * @param type $item_id
     * @param type $index
     */
    public function initScriptsSelectBoxClass($compound_id,$property_id, $item_id, $index_id) {
        ?>
        <script>
            $('#selectbox-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').change(function(){
                $.ajax({
                    url: $('#src').val() + '/controllers/object/form_item_controller.php',
                    type: 'POST',
                    data: {
                        operation: 'saveValue',
                        type:'term',
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
