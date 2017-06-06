<?php

class TextClass extends FormItem{
    public function generate($property_id,$item_id,$compound_id,$index_id) {
        ?>
        <input  type="text" 
                item="<?php echo $item_id ?>"
                id="text-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>" 
                class="form-control form_autocomplete_value_<?php echo $property_id ?>" 
                value="<?php ?>"
                name="socialdb_property_<?php echo $property['id']; ?>[]" >
        <?php
    }
}
