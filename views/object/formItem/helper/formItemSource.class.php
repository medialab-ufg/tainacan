<?php

class FormItemSource extends FormItem {

    public function widget($property, $item_id) {
        ?>
        <div class="form-group">
            <h2>
                <?php echo ($this->terms_fixed['source']) ? $this->terms_fixed['source']->name : _e('Source', 'tainacan') ?>
                <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>   
            </h2>
            <div  >
                <input
                    type="text"
                    id="object_source"
                    class="form-control auto-save"
                    name="object_source"
                    placeholder="<?php _e('What\'s the item source', 'tainacan'); ?>"
                    value="" >
            </div>
        </div>
        <?php
    }

}
