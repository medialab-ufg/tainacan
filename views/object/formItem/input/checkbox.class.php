<?php

class CheckboxClass extends FormItem{
    public function generate($property,$item_id,$compound_id,$index_id) {
        ?>
        <?php if($property['has_children'] && is_array($property['has_children'])): ?>
            <?php foreach ($property['has_children'] as $child): ?>
                <input type="checkbox"
                       name="checkbox-field-<?php echo $compound_id ?>-<?php echo $property['id'] ?>-<?php echo $index_id; ?>[]"
                       value="<?php echo $child->term_id ?>">&nbsp;<?php echo $child->name ?>
            <?php endforeach; ?>
        <?php endif;
        $this->initScriptsCheckboxBoxClass($compound_id, $property_id, $item_id, $index_id);
    }

    /**
     *
     * @param type $property
     * @param type $item_id
     * @param type $index
     */
    public function initScriptsCheckboxBoxClass($compound_id,$property_id, $item_id, $index_id) {
        ?>
        <script>
            $('input[name="checkbox-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"]').change(function(){
                 if($(this).is(':checked')){
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
                           index: <?php echo $index_id ?>
                       }
                   });
                 }else{
                    $.ajax({
                       url: $('#src').val() + '/controllers/object/form_item_controller.php',
                       type: 'POST',
                       data: {
                           operation: 'removeValue',
                           type:'term',
                           value: $(this).val(),
                           item_id:'<?php echo $item_id ?>',
                           compound_id:'<?php echo $compound_id ?>',
                           property_children_id: '<?php echo $property_id ?>',
                           index: <?php echo $contador ?>
                       }
                   });
                 }
            });
        </script>
        <?php
    }
}
