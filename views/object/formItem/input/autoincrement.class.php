<?php

class AutoIncrementClass extends FormItem{
    public function generate($property_id,$item_id,$compound_id,$index_id) {
        ?>
        <input 
            disabled="disabled"  
            type="number" 
            id="autoincrement-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
            class="form-control" 
            value="">
        <?php
    } 

}
