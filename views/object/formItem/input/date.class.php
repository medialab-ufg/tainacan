<?php

class DateClass extends FormItem {

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
                 <input 
                    style="margin-right: 5px;" 
                    size="13"
                    id="date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
                    class="input_date input auto-save form_autocomplete_value_<?php echo $property_id; ?>" 
                    aria-describedby="input2Status"
                    type="text" value="">
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
        <?php else: ?> 
            <?php if($property_id !== 0): ?> 
                    <input  type="hidden" 
                            compound="<?php echo $compound['id'] ?>"
                            property="<?php echo $property['id'] ?>"
                            id="validation-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
                            class="compound-one-field-should-be-filled-<?php echo $compound['id'] ?>"
                            value="false">
            <?php endif;  ?>
            <input 
                style="margin-right: 5px;" 
                size="13"
                id="date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
                class="input_date auto-save form_autocomplete_value_<?php echo $property_id; ?>" 
                type="text" value="">
        <?php
        endif;
                $this->initScriptsDate($property_id, $item_id, $compound_id, $index_id);
        }

        public function initScriptsDate($property_id, $item_id, $compound_id, $index_id) { ?>
        <script>
            init_metadata_date("#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>");

            $('#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').keyup(function () {
                <?php if($this->isRequired):  ?>
                    validateFieldsMetadataText($(this).val(),'<?php echo $compound_id ?>','<?php echo $property_id ?>','<?php echo $index_id ?>')
                <?php endif; ?>
                $.ajax({
                    url: $('#src').val() + '/controllers/object/form_item_controller.php',
                    type: 'POST',
                    data: {
                        operation: 'saveValue',
                        type: 'data',
                        value: $(this).val(),
                        item_id: '<?php echo $item_id ?>',
                        compound_id: '<?php echo $compound_id ?>',
                        property_children_id: '<?php echo $property_id ?>',
                        index: <?php echo $index_id ?>,
                        indexCoumpound: 0
                    }
                }).done(function (result) {

                });
            });
            
            $('#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').change(function () {
                <?php if($this->isRequired):  ?>
                    validateFieldsMetadataText($(this).val(),'<?php echo $compound_id ?>','<?php echo $property_id ?>','<?php echo $index_id ?>')
                <?php endif; ?>
                $.ajax({
                    url: $('#src').val() + '/controllers/object/form_item_controller.php',
                    type: 'POST',
                    data: {
                        operation: 'saveValue',
                        type: 'data',
                        value: $(this).val(),
                        item_id: '<?php echo $item_id ?>',
                        compound_id: '<?php echo $compound_id ?>',
                        property_children_id: '<?php echo $property_id ?>',
                        index: <?php echo $index_id ?>
                    }
                }).done(function (result) {

                });
            });

            function init_metadata_date(seletor) {
                $(seletor).datepicker({
                    dateFormat: 'dd/mm/yy',
                    dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
                    dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S', 'D'],
                    dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                    monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                    monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                    nextText: 'Próximo',
                    prevText: 'Anterior',
                    showOn: "button",
                    buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
                    buttonImageOnly: true
                });
            }
        </script> 
        <?php
    }
}
