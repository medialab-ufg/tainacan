<?php

class TextAreaClass extends FormItem{
    public function generate($property_id,$item_id,$compound_id,$index_id) {
        ?>
        <textarea   class="form-control auto-save form_autocomplete_value_<?php echo $property['id']; ?>" 
                    id="textarea-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>" 
                    rows='9'
                    name="socialdb_property_<?php echo $property['id']; ?>[]"
                    ></textarea>
        <?php
    }
    
    public function template() {
        return '
        <textarea  
                compound="##compound##"
                item="##item##"
                id="textarea-field-##compound##-##property##-##index##" 
                class="form-control auto-save textarea-field-##compound##-##property##" 
                rows="9"
                name="socialdb_property_##property##[]" ></textarea>
        ';
    }
}
