<?php

class RadioClass extends FormItem{
    public function generate($property,$item_id,$compound_id,$index_id) {
        ?>
        <?php if($property['has_children'] && is_array($property['has_children'])): ?>
            <?php foreach ($property['has_children'] as $child): ?>
                <input type="radio" 
                       name="radio-field-<?php echo $compound_id ?>-<?php echo $property['id'] ?>-<?php echo $index_id; ?>[]" 
                       value="<?php echo $child->term_id ?>">&nbsp;<?php echo $child->name ?>
            <?php endforeach; ?>
        <?php endif; 
    }
}
