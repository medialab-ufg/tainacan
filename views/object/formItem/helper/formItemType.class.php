<?php

class FormItemType extends FormItem {

    public function widget($property, $item_id) {
        ?>
        <div class="form-group">
            <h2>
                <?php echo ($this->terms_fixed['type']) ? $this->terms_fixed['type']->name :  _e('Type','tainacan') ?>
                 <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>
            </h2>
            <div>
                <select class="form-control">
                    <option value=""><?php _e('Select','tainacan') ?>...</option>
                    <option value="text"><?php _e('Text','tainacan') ?></option>
                    <option value="pdf"><?php _e('PDF','tainacan') ?></option>
                    <option value="audio"><?php _e('Audio','tainacan') ?></option>
                    <option value="audio"><?php _e('Video','tainacan') ?></option>
                </select>
            </div>
        </div>
        <?php
    }

}
