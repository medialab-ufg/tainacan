<?php

class FormItemTitle extends FormItem{

    public function widget($property,$item_id) {
        ?>
        <!-- TAINACAN: titulo do item -->
        <div class="form-group">
            <h2>
                <?php echo ($this->terms_fixed['title']) ? $this->terms_fixed['title']->name :  _e('Title','tainacan') ?>
                <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>
            </h2>
             <div>
                <input type="hidden"  
                       value="<?php echo get_post_meta($this->collection_id, 'socialdb_collection_property_'.$this->terms_fixed['title']->term_id.'_mask_key', true) ?>">
                <input class="form-control auto-save"
                       type="text"
                       id="object_name"
                       name="object_name"
                       placeholder="<?php _e('Item name','tainacan'); ?>">
              </div>
        </div>
        <?php
    }
}