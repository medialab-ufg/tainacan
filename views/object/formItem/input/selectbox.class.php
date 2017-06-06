<?php

class SelectboxClass extends FormItem{
    public function generate($property,$item_id,$compound_id,$index_id) {
        ?>
        <select class="form-control auto-save" 
                id='selectbox-field-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>' >
            <option value=""><?php _e('Select','tainacan') ?>...</option>
            <?php if($property['has_children'] && is_array($property['has_children'])): ?>
                <?php foreach ($property['has_children'] as $child): ?>
            <option value="<?php echo $child->term_id ?>"><?php echo $child->name ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <?php
    }
}
