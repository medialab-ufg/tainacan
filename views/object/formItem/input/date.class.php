<?php

class DateClass extends FormItem {

    public function generate($compound,$property,$item_id,$index_id)
    {
        $compound_id = $compound['id'];
        $property_id = $property['id'];
        if ($property_id == 0) {
            $property = $compound;
        }
       //verifico se tem valor default
        $hasDefaultValue = (isset($property['metas']['socialdb_property_default_value']) && $property['metas']['socialdb_property_default_value']!='') ? $property['metas']['socialdb_property_default_value'] : false;
        $values = ($this->value && is_array($this->getValues($this->value[$index_id][$property_id]))) ? $this->getValues($this->value[$index_id][$property_id]) : false;
        //se nao possuir nem valor default verifico se ja existe

        $mapping = get_option('socialdb_general_mapping_collection');
        $collection_id = $property['metas']['socialdb_property_collection_id'];
        if(has_action("add_material_loan_devolution") && $mapping['Emprestimo'] == $collection_id)
        {
            //Get variable from DB
            $loan_time = get_option('socialdb_loan_time');
            $devolution_days = get_option('socialdb_devolution_weekday');
            $devolution_day_problem_option = get_option('socialdb_devolution_day_problem');
            if($devolution_day_problem_option == 'after')
                $sum = 1;
            else $sum = -1;


            $today = intval(date('d'));
            $month = intval(date('m'));
            $year = intval (date('Y'));
            $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
            $actual_weekday = '';


            $day_to_return += $today + $loan_time;
            while ($day_to_return > $days_in_month)
            {
                $day_to_return -= $days_in_month;
                $next_month = $month + 1;

                if($next_month % 12 == 0)
                {
                    $month = 12;
                }else if($next_month % 12 > $month)
                {
                    $month = $next_month % 12;
                }

                if($next_month > 12)
                {
                    $year++;
                }

                $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
            }

            if($day_to_return < $days_in_month)
            {
                $actual_weekday = date("l", mktime(0, 0, 0, $month, $day_to_return, $year));

                while (!array_key_exists($actual_weekday, $devolution_days))
                {
                    $day_to_return += $sum;
                    $actual_weekday = date("l", mktime(0, 0, 0, $month, $day_to_return, $year));

                    if($day_to_return > $days_in_month)
                    {
                        $day_to_return -= $days_in_month;
                        $next_month = $month + 1;

                        if($next_month % 12 == 0)
                        {
                            $month = 12;
                        }else if($next_month % 12 > $month)
                        {
                            $month = $next_month % 12;
                        }

                        if($next_month > 12)
                        {
                            $year++;
                        }

                        $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
                    }
                }
            }

            $values[0] = date('d/m/Y', mktime(0, 0, 0, $month, $day_to_return, $year));
            $hasDefaultValue = true;
        }
        
        $values = (!$values && $hasDefaultValue) ? [$hasDefaultValue] : $values;
        $autoValidate = ($values && isset($values[0]) && !empty($values[0])) ? true : false;
        $this->isRequired = ($property['metas'] && $property['metas']['socialdb_property_required'] && $property['metas']['socialdb_property_required'] != 'false') ? true : false;
        $isView = $this->viewValue($property,$values,'term');
        if($isView){
            return true;
        }
        ?>
        <?php if ($this->isRequired): ?> 
        <div class="form-group" 
             id="validation-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
             style="border-bottom:none;padding: 0px;">
                <?php if(has_action('alter_input_date')): ?>
                    <?php do_action('alter_input_date',
                            ['value'=> $values,'item_id'=>$item_id,'compound'=>$compound,'property_id'=>$property_id,'property'=>$property,'index'=>$index_id,'autoValidate'=>$autoValidate]) ?>
                <?php else: ?> 
                 <input 
                    style="margin-right: 5px;" 
                    size="13"
                    value="<?php echo ($values && isset($values[0]) && !empty($values[0])) ? $values[0] : ''; ?>"
                    id="date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
                    class="input_date input auto-save form_autocomplete_value_<?php echo $property_id; ?>" 
                    aria-describedby="input2Status"
                    type="text" value="">
                <?php endif; ?> 
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
            <?php if(has_action('alter_input_date')): ?>
                <?php do_action('alter_input_date',
                            ['value'=> $values,'item_id'=>$item_id,'compound'=>$compound,'property_id'=>$property_id,'property'=>$property,'index'=>$index_id,'autoValidate'=>$autoValidate]) ?>
            <?php else: ?>         
                <input 
                    style="margin-right: 5px;" 
                    size="13"
                    value="<?php echo ($values && isset($values[0]) && !empty($values[0])) ? $values[0] : ''; ?>"
                    id="date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
                    class="input_date auto-save form_autocomplete_value_<?php echo $property_id; ?>" 
                    type="text" value="">
            <?php endif; ?> 
        <?php
        endif;
                $this->initScriptsDate($property_id, $item_id, $compound_id, $index_id);
        if($hasDefaultValue): ?>
            <script>
                $('#date-field-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').trigger('blur');
            </script>
        <?php endif;         
        }

    public function initScriptsDate($property_id, $item_id, $compound_id, $index_id) { ?>
    <script>
        init_metadata_date("#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>");

        $('#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').blur(function () {
            let field_value = $(this).val().split("/");
            let day = field_value[0], month = field_value[1], year = field_value[2];
            if(day_exist(day, month, year))
            {
                <?php if($this->isRequired):  ?>
                Hook.call('validateFieldsMetadataText',[$(this).val().trim(),'<?php echo $compound_id ?>','<?php echo $property_id ?>','<?php echo $index_id ?>']);
                //validateFieldsMetadataText($(this).val(),'<?php echo $compound_id ?>','<?php echo $property_id ?>','<?php echo $index_id ?>')
                <?php endif; ?>

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
                        isKey: <?php echo ($this->isKey) ? 'true':'false' ?>
                    }
                }).done(function (result) {
                    <?php if($this->isKey): ?>
                    let json =JSON.parse(result);
                    if(json.value){
                        $('#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').val('');
                        toastr.error(json.value+' <?php _e(' is already inserted!', 'tainacan') ?>', '<?php _e('Attention!', 'tainacan') ?>', {positionClass: 'toast-bottom-right'});
                    }
                    <?php endif; ?>
                });
            }
        });

        $('#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').change(function ()
        {
            let field_value = $(this).val().split("/");
            let day = field_value[0], month = field_value[1], year = field_value[2];
            if(day_exist(day, month, year))
            {
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
                        indexCoumpound: 0,
                        isKey: <?php echo ($this->isKey) ? 'true':'false' ?>
                    }
                }).done(function (result) {
                    <?php if($this->isKey): ?>
                    let json = JSON.parse(result);
                    if(json.value){
                        //$('#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>').val('');
                        toastr.error(json.value+' <?php _e(' is already inserted!', 'tainacan') ?>', '<?php _e('Attention!', 'tainacan') ?>', {positionClass: 'toast-bottom-right'});
                    }
                    <?php endif; ?>
                });
            }
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
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true
            });
        }

        function day_exist(day, month_number, year)
        {
            month_number--;
            let days_in_month = [/*January*/31,/*Fabruary*/ 28,/*March*/ 31,/*April*/ 30,/*May*/ 31,/*June*/ 30, /*July*/31,
                               /*August*/31, /*September*/30, /*October*/31,/*November*/ 30, /*December*/ 31];
            if(is_leap(year))
            {
                /*February*/
                days_in_month[1] = 29;
            }

            if(day > days_in_month[month_number] || day < 1 || month_number > 11 || month_number < 0)
            {
                return false;
            }
            else return true;
        }

        function is_leap(year)
        {
            if(year % 400 === 0 || (year % 4 === 0 && year % 100 !== 0))//Is a leap year
            {
                return true;
            }else return false;
        }
    </script>
    <?php
}
}
