<?php

class TextClass extends FormItem{

    /**
     *
     * @param type $compound
     * @param type $property_id
     * @param type $item_id
     * @param type $index_id
     */
    public function generate($compound, $property, $item_id, $index_id) {
        $compound_id = $compound['id'];
        $property_id = $property['id'];
        if ($property_id == 0) {
            $property = $compound;
        }
        //verifico se tem valor default
        $hasDefaultValue = (isset($property['metas']['socialdb_property_default_value']) && $property['metas']['socialdb_property_default_value']!='') ? $property['metas']['socialdb_property_default_value'] : false;
        $values = ($this->value && is_array($this->getValues($this->value[$index_id][$property_id])) && !empty($this->getValues($this->value[$index_id][$property_id]))) ? $this->getValues($this->value[$index_id][$property_id]) : false;
        //se nao possuir nem valor default verifico se ja existe
        $values = (!$values && $hasDefaultValue) ? [$hasDefaultValue] : $values;
        $autoValidate = ($values && isset($values[0]) && !empty($values[0])) ? true : false;
        $this->isRequired = ($property['metas'] && $property['metas']['socialdb_property_required'] && $property['metas']['socialdb_property_required'] != 'false') ? true : false;
        $isView = $this->viewValue($property,$values,'data');
        if($isView){
            return true;
        }
        ?>
        <?php if ($this->isRequired): ?>
        <div class="form-group"
             id="validation-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
             style="border-bottom:none;padding: 0px;">
                <input type="text"
                       value="<?php echo ($values && isset($values[0]) && !empty($values[0])) ? $values[0] : ''; ?>"
                       class="form-control"
                       id="text-field-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
                       aria-describedby="input2Status">
                <span style="display: none;" class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                <span style="display: none;" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
                <span id="input2Status" class="sr-only">(status)</span>
                <input type="hidden"
                       <?php if($property_id !== 0): ?>
                       compound="<?php echo $compound['id'] ?>"
                       <?php endif; ?>
                      property="<?php echo $property['id'] ?>"
                       class="validate-class validate-compound-<?php echo $compound['id'] ?>"
                       value="<?php echo ($autoValidate) ? 'true' : 'false' ?>">
         </div>
        <?php else: ?>
                    <?php if($property_id !== 0): ?>
                            <input  type="hidden"
                                    compound="<?php echo $compound['id'] ?>"
                                    property="<?php echo $property['id'] ?>"
                                    id="validation-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
                                    class="compound-one-field-should-be-filled-<?php echo $compound['id'] ?>"
                                    value="<?php echo ($autoValidate) ? 'true' : 'false' ?>">
                    <?php endif;  ?>
                    <input  type="text"
                            item="<?php echo $item_id ?>"
                            autocomplete="false"
                            value="<?php echo ($values && isset($values[0]) && !empty($values[0])) ? $values[0] : ''; ?>"
                            id="text-field-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
                            class="form-control text-field-<?php echo $compound['id'] ?>-<?php echo $property_id ?>"
                            name="socialdb_property_<?php echo $compound['id']; ?>[]" >
        <?php
        endif;
        $this->initScriptsTextClass($compound['id'], $property_id, $item_id, $index_id);
        if($hasDefaultValue): ?>
            <script>
                $('#text-field-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').trigger('blur');
            </script>
        <?php endif;
        //action para edicao dos widget de texto
        if(has_action('alter_widget_text_helper')){
            do_action('alter_widget_text_helper',$property,"text-field-". $compound['id'] ."-". $property_id ."-". $index_id);
        }
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
//             $('#text-field-<?php //echo $compound_id ?>//-<?php //echo $property_id ?>//-<?php //echo $index_id; ?>//').change(function(){
//                 $(this).trigger('blur');
//             });
             
            if('<?php echo $index_id; ?>' !=='0' && '<?php echo $property_id; ?>' ==='0'  && $('#text-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').val()==''){
                $('.js-append-property-<?php echo $compound_id ?>').hide();
            }
            $('#text-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').keyup(function(){
                if($(this).val()=='' && '<?php echo $property_id; ?>' === '0'){
                    $('.js-append-property-<?php echo $compound_id ?>').hide();
                }else if('<?php echo $property_id; ?>' === '0'){
                    $('.js-append-property-<?php echo $compound_id ?>').show();
                }
            });
            //enviando valores
            $('#text-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').blur(function(){
                //validando campos dentro do mesmo metadado
                if( Hook.is_register('validate_unique_fields')){
                    Hook.call('validate_unique_fields',['<?php echo $compound_id; ?>',$(this).val(),'text-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>','<?php echo $property_id ?>']);
                    if(Hook.result){
                        $('#text-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').val('');
                        toastr.error('<?php _e('this value is already inserted!', 'tainacan') ?>', '<?php _e('Attention!', 'tainacan') ?>', {positionClass: 'toast-bottom-right'});
                    }
                }

                <?php if($this->isRequired):  ?>
                    validateFieldsMetadataText($(this).val().trim(),'<?php echo $compound_id ?>','<?php echo $property_id ?>','<?php echo $index_id ?>')
                <?php endif; ?>
                    if($('#AllFieldsShouldBeFilled'+<?php echo $compound_id ?>).length === 0) {
                        $.ajax({
                            url: $('#src').val() + '/controllers/object/form_item_controller.php',
                            type: 'POST',
                            data: {
                                operation: 'saveValue',
                                type: 'data',
                                value: $(this).val().trim(),
                                item_id: '<?php echo $item_id ?>',
                                compound_id: '<?php echo $compound_id ?>',
                                property_children_id: '<?php echo $property_id ?>',
                                index: <?php echo $index_id ?>,
                                indexCoumpound: 0,
                                isKey: <?php echo ($this->isKey) ? 'true' : 'false' ?>
                            }
                        }).done(function (result) {
                            <?php if($this->isKey): ?>
                            var json = JSON.parse(result);
                            if (json.value) {
                                //$('#text-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').val('');
                                toastr.error(json.value + ' <?php _e(' is already inserted!', 'tainacan') ?>', '<?php _e('Attention!', 'tainacan') ?>', {positionClass: 'toast-bottom-right'});
                            }
                            <?php endif; ?>
                        });
                    }else{
                        Hook['<?php echo $compound_id.'_'.$index_id ?>'] = ( Hook['<?php echo $compound_id.'_'.$index_id ?>']) ?  Hook['<?php echo $compound_id.'_'.$index_id ?>'] : {};
                        Hook['<?php echo $compound_id.'_'.$index_id ?>']['<?php echo $property_id ?>'] = {
                            operation: 'saveValue',
                            type: 'data',
                            value: $(this).val().trim(),
                            item_id: '<?php echo $item_id ?>',
                            compound_id: '<?php echo $compound_id ?>',
                            property_children_id: '<?php echo $property_id ?>',
                            index: <?php echo $index_id ?>,
                            indexCoumpound: 0,
                            isKey: <?php echo ($this->isKey) ? 'true' : 'false' ?>
                        };
                    }

                    if($('#AllFieldsShouldBeFilled'+<?php echo $compound_id ?>).length > 0 && '<?php echo $property_id ?>' !== '0'){
                        Hook.call('blockCompounds',['<?php echo $compound_id ?>','<?php echo $property_id ?>','<?php echo $index_id ?>']);
                    }else{
                        Hook.result = false;
                    }
            });
        </script>
        <?php
    }
}
