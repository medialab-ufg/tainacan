<?php

class SimpleTreeClass extends FormItem{
    public function generate($property,$item_id,$compound_id,$index_id) {
        ?>
        <div class="row">
            <div style='height: 150px;' 
                 class='col-lg-12'  
                 id='field_property_term_<?php echo $property['id']; ?>'>
            </div>
        </div>
        <?php
    }
}
