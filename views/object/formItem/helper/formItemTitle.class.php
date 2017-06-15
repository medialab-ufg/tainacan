<?php

class FormItemTitle extends FormItem{

    public function widget($property,$item_id) {
        $this->isRequired = get_post_meta($this->collection_id, 'socialdb_collection_property_'.$this->terms_fixed['title']->term_id.'_required', true);
        $title = get_post($item_id)->post_title;
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
                   <div class="form-group"
                        id="validation-<?php echo $property['id'] ?>-0-0"
                        style="border-bottom:none;padding: 0px;">
                        <input class="form-control auto-save"
                               type="text"
                               id="item-title"
                               value="<?php echo ($title !== 'Temporary_post') ? $title : '' ?>"
                               name="object_name"
                               placeholder="<?php _e('Item name','tainacan'); ?>">
                               <span style="display: none;" class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                               <span style="display: none;" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
                               <span id="input2Status" class="sr-only">(status)</span>
                               <?php if($this->isRequired === 'true'): ?>
                               <input type="hidden"
                                      property="<?php echo $property['id'] ?>"
                                      class="validate-class validate-compound-<?php echo $property['id'] ?>"
                                      value="false">
                              <?php endif; ?>
                  </div>
            </div>
        </div>
        <?php
        $this->initScriptsTitleContainer($property,$item_id);
    }


    /**
     *
     * @param type $property
     * @param type $item_id
     * @param type $index
     */
    public function initScriptsTitleContainer($property, $item_id) {
        ?>
        <script>
            $('#item-title').keyup(function(){
                <?php if($this->isRequired === 'true'):  ?>
                    validateFieldsMetadataText($(this).val(),'<?php echo $property['id'] ?>','0','0')
                <?php endif; ?>
                $.ajax({
                    url: $('#src').val() + '/controllers/object/form_item_controller.php',
                    type: 'POST',
                    data: {
                        operation: 'saveTitle',
                        value: $(this).val(),
                        item_id:'<?php echo $item_id ?>'
                    }
                }).done(function (result) {

                });
            });
        </script>
        <?php
    }
}
