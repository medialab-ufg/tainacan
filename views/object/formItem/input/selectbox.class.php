<?php

class SelectboxClass extends FormItem{
    public function generate($compound,$property,$item_id,$index_id) {
        $compound_id = $compound['id'];
        $property_id = $property['id'];
        if ($property_id == 0) {
            $property = $compound;
        }
        $this->isRequired = ($property['metas'] && $property['metas']['socialdb_property_required'] && $property['metas']['socialdb_property_required'] != 'false') ? true : false;
        ?>
        <?php if ($this->isRequired): ?> 
        <div class="form-group" 
             id="validation-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
             style="border-bottom:none;padding: 0px;"> 
               <?php endif; ?>
                <select class="form-control auto-save" 
                        id='selectbox-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>' >
                    <option value=""><?php _e('Select','tainacan') ?>...</option>
                    <?php if($property['has_children'] && is_array($property['has_children'])): ?>
                        <?php foreach ($property['has_children'] as $child): ?>
                            <option value="<?php echo $child->term_id ?>"><?php echo $child->name ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if ($this->isRequired): ?> 
                <span style="display: none;" class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                <span style="display: none;" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
                <span id="input2Status" class="sr-only">(status)</span>
                <input type="hidden" 
                       <?php if($property_id !== 0): ?>
                       compound="<?php echo $compound['id'] ?>"
                       <?php endif; ?>
                       property="<?php echo $property['id'] ?>"
                       class="validate-class validate-compound-<?php echo $compound['id'] ?>"
                       value="false">
        </div> 
        <?php elseif($property_id !== 0): ?> 
        <input  type="hidden" 
                compound="<?php echo $compound['id'] ?>"
                property="<?php echo $property['id'] ?>"
                id="validation-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
                class="compound-one-field-should-be-filled-<?php echo $compound['id'] ?>"
                value="false">
        <?php endif;  
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
                appendCategoryMetadata($(this).val(), <?php echo $compound_id ?>, '#appendCategoryMetadata_<?php echo $compound_id; ?>_0_0')
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
                });
                <?php if($this->isRequired): ?>
                    validateFieldsMetadataText($(this).val(),'<?php echo $compound_id ?>','<?php echo $property_id ?>','<?php echo $index_id ?>');
                <?php endif; ?>
            });
        </script> 
        <?php
    }
}
