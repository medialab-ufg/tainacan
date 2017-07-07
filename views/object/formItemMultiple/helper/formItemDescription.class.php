<?php
class FormItemDescription extends FormItemMultiple {

    public function widget($property, $item_id) {
      $this->isRequired = get_post_meta($this->collection_id, 'socialdb_collection_property_'.$property['id'].'_required', true);
      $content = get_post($item_id)->post_content;
        ?>
        <div class="form-group" >
            <h2>
                <?php echo ($this->terms_fixed['description']) ? $this->terms_fixed['description']->name : _e('Description', 'tainacan') ?>
                <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>
            </h2>
            <div >
               <input type="hidden"
                     value="<?php echo get_post_meta($this->collection_id, 'socialdb_collection_property_'.$property['id'].'_mask_key', true) ?>">
                  <div class="form-group"
                       id="validation-<?php echo $property['id'] ?>-0-0"
                       style="border-bottom:none;padding: 0px;">
                        <textarea class="form-control auto-save"
                                  rows="8"
                                  id="item-description"
                                  placeholder="<?php _e('Describe your item', 'tainacan'); ?>"
                                  name="object_description" ><?php echo $content; ?></textarea>
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
        $this->initScriptsDescriptionContainer($property,$item_id);
    }


    /**
     *
     * @param type $property
     * @param type $item_id
     * @param type $index
     */
    public function initScriptsDescriptionContainer($property, $item_id) {
        ?>
        <script>
                $('#item-description').keyup(function(){
                    $.ajax({
                        url: $('#src').val() + '/controllers/object/form_item_controller.php',
                        type: 'POST',
                        data: {
                            operation: 'saveDescription',
                            value: $(this).val(),
                            item_id: $('#item-multiple-selected').val().trim()
                        }
                    }).done(function (result) {

                    });
                });
                
                Hook.register(
                    'get_single_item_value',
                    function ( args ) {
                        $.ajax({
                            url: $('#src').val() + '/controllers/object/form_item_controller.php',
                            type: 'POST',
                            data: {
                                operation: 'getDescription',
                                item_id:args[0]
                            }
                        }).done(function (result) {
                            var json = JSON.parse(result);
                            if(json.value){
                                $('#item-description').val(json.value);
                            }
                    });
                });
                
                Hook.register(
                    'get_multiple_item_value',
                    function ( args ) {
                        $('#item-description').val('');
                        $('#item-description').attr("placeholder", "<?php _e('Alter ', 'tainacan') ?>" + args.length + " <?php _e(' items', 'tainacan') ?>");
                });
        </script>
        <?php
    }

}
