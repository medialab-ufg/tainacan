<?php

class NumericClass extends FormItem{
    public function generate($property_id,$item_id,$compound_id,$index_id) {
        ?>
         <input type="text" 
                id="numeric-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>" 
                value="" 
                class="form-control auto-save form_autocomplete_value_<?php echo $property_id; ?>" 
                onkeypress='return onlyNumbers(event)'
                name="socialdb_property_<?php echo $property_id; ?>[]">
        <?php
    }
}
