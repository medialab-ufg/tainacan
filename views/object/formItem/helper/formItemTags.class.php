<?php

class FormItemTags extends FormItem {

    public function widget($property, $item_id) {
        ?>
        <div class="form-group">
            <h2>
                <?php echo ($this->terms_fixed['tags']) ? $this->terms_fixed['tags']->name :  _e('Tags','tainacan') ?>
                <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>
            </h2>
            <div>
                <input type="text"
                       class="form-control auto-save"
                       id="object_tags"
                       name="object_tags"
                       placeholder="<?php _e('The set of tags may be inserted by comma','tainacan') ?>">
            </div>
        </div>
        <?php
    }

}
