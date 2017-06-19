<?php

class FormItemTags extends FormItem {

    public function widget($property, $item_id) {
        $this->isRequired = get_post_meta($this->collection_id, 'socialdb_collection_property_'.$property['id'].'_required', true);
        $tags_name = [];
        $tags = wp_get_post_terms($item_id, 'socialdb_tag_type');
        if(isset($tags)){
            foreach ($tags as $tag) {
                $tags_name[] = $tag->name;
            }
        }
        ?>
        <div class="form-group">
            <h2>
                <?php echo ($this->terms_fixed['tags']) ? $this->terms_fixed['tags']->name :  _e('Tags','tainacan') ?>
                <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>
            </h2>
            <div >
               <input type="hidden"
                     value="<?php echo get_post_meta($this->collection_id, 'socialdb_collection_property_'.$property['id'].'_mask_key', true) ?>">
                  <div class="form-group"
                       id="validation-<?php echo $property['id'] ?>-0-0"
                       style="border-bottom:none;padding: 0px;">
                        <input type="text"
                               class="form-control auto-save"
                               id="object_tags"
                               value="<?php echo implode(',',$tags_name) ?>"
                               name="object_tags"
                               placeholder="<?php _e('The set of tags may be inserted by comma','tainacan') ?>">
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
   $this->initScriptsTagsContainer($property,$item_id);
}


   /**
   *
   * @param type $property
   * @param type $item_id
   * @param type $index
   */
   public function initScriptsTagsContainer($property, $item_id) {
       ?>
             <script>
                 $('#object_tags').blur(function(){
                       <?php if($this->isRequired === 'true'):  ?>
                           validateFieldsMetadataText($(this).val(),'<?php echo $property['id'] ?>','0','0')
                       <?php endif; ?>
                       $.ajax({
                           url: $('#src').val() + '/controllers/object/form_item_controller.php',
                           type: 'POST',
                           data: {
                               operation: 'saveTags',
                               value: $(this).val(),
                               collection_id:$('#collection_id').val(),
                               item_id:'<?php echo $item_id ?>'
                           }
                       }).done(function (result) {

                       });
                 });
             </script>
       <?php
   }

}
