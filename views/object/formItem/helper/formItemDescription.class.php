<?php
class FormItemDescription extends FormItem {

    public function widget($property, $item_id) {
        ?>
        <div class="form-group" >
            <h2>
                <?php echo ($this->terms_fixed['description']) ? $this->terms_fixed['description']->name : _e('Description', 'tainacan') ?>
                <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>     
            </h2>
            <div >
                <textarea class="form-control auto-save"
                          rows="8"
                          id="object_description_example"
                          placeholder="<?php _e('Describe your item', 'tainacan'); ?>"
                          name="object_description" ></textarea>
            </div>
        </div>
        <?php
    }

}
