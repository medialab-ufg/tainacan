<?php

class DateClass extends FormItem {

    public function generate($property_id, $item_id, $compound_id, $index_id) {
        ?>
        <input 
            style="margin-right: 5px;" 
            size="13"
            id="date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
            class="input_date auto-save form_autocomplete_value_<?php echo $property_id; ?>" 
            type="text" value="">
            <?php
            $this->initScriptsDate($property_id, $item_id, $compound_id, $index_id);
        }

        public function initScriptsDate($property_id, $item_id, $compound_id, $index_id) {
            ?>
        <script>
            init_metadata_date("#date-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>");
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
