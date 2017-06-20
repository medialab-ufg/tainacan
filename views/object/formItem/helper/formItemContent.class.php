<?php
class FormItemContent extends FormItem {

    public function widget($property, $item_id,$isFocusMedia = false) {
        $this->isRequired = get_post_meta($this->collection_id, 'socialdb_collection_property_'.$property['id'].'_required', true);
        $content = get_post_meta($item_id,'socialdb_object_content',true);
        ?>
        <div class="form-group" >
             <?php echo ($isFocusMedia) ? '<h5>' : '<h2>' ?>
                <?php echo ($this->terms_fixed['content']) ? $this->terms_fixed['content']->name : _e('Content', 'tainacan') ?>
                <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>
            <?php echo ($isFocusMedia) ? '</h5>' : '</h2>' ?>
            <div >
               <input type="hidden"
                     value="<?php echo get_post_meta($this->collection_id, 'socialdb_collection_property_'.$property['id'].'_mask_key', true) ?>">
                  <div class="form-group"
                       id="validation-<?php echo $property['id'] ?>-0-0"
                       style="border-bottom:none;padding: 0px;">
                        <textarea class="form-control auto-save"
                                  id="item_content"
                                  name="item_content"
                                  placeholder="<?php _e('Object Content', 'tainacan'); ?>"><?php echo $content; ?></textarea>
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
        $this->initScriptsContentContainer($property,$item_id);
    }


    /**
     *
     * @param type $property
     * @param type $item_id
     * @param type $index
     */
    public function initScriptsContentContainer($property, $item_id) {
        ?>
        <script>
            showCKEditor('item_content');
            CKEDITOR.instances.item_content.on('contentDom', function() {
                CKEDITOR.instances.item_content.document.on('keyup', function(event) {
                      <?php if($this->isRequired === 'true'):  ?>
                          validateFieldsMetadataText($(this).val(),'<?php echo $property['id'] ?>','0','0')
                      <?php endif; ?>
                      $.ajax({
                          url: $('#src').val() + '/controllers/object/form_item_controller.php',
                          type: 'POST',
                          data: {
                              operation: 'saveContent',
                              value: CKEDITOR.instances.item_content.getData(),
                              item_id:'<?php echo $item_id ?>'
                          }
                      }).done(function (result) {

                      });
              });
            });
        </script>
        <?php
    }

}
