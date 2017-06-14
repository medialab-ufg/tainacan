<?php
class FormItemContent extends FormItem {

    public function widget($property, $item_id) {
        ?>
        <div class="form-group" >
            <h2>
                <?php echo ($this->terms_fixed['content']) ? $this->terms_fixed['content']->name : _e('Content', 'tainacan') ?>
                <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>    
            </h2>
            <div >
                <textarea class="form-control auto-save" 
                          id="object_editor" 
                          name="object_editor" 
                          placeholder="<?php _e('Object Content', 'tainacan'); ?>"></textarea>
            </div>
        </div>
        <?php
    }

}
