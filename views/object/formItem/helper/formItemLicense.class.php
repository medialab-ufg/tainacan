<?php

class FormItemLicense extends FormItem {

    public function widget($property, $item_id) {
        ?>
        <!-- TAINACAN: a licencas do item -->
        <div class="form-group">
            <h2>
                <?php echo ($this->terms_fixed['license']) ? $this->terms_fixed['license']->name : _e('Licenses', 'tainacan') ?>
                <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>   
            </h2>
            <div>
                
            </div>
        </div>
        <?php
    }

}
