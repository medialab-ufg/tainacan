<?php
class FormItemSource extends FormItemMultiple {

    public function widget($property, $item_id) {
        $this->isRequired = get_post_meta($this->collection_id, 'socialdb_collection_property_'.$property['id'].'_required', true);
        $source = get_post_meta($item_id, 'socialdb_object_dc_source', true);
        ?>
        <div class="form-group">
            <h2>
                <?php echo ($this->terms_fixed['source']) ? $this->terms_fixed['source']->name : _e('Source', 'tainacan') ?>
                <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>
            </h2>
            <div >
               <input type="hidden"
                     value="<?php echo get_post_meta($this->collection_id, 'socialdb_collection_property_'.$property['id'].'_mask_key', true) ?>">
                  <div class="form-group"
                       id="validation-<?php echo $property['id'] ?>-0-0"
                       style="border-bottom:none;padding: 0px;">
                    <input
                        type="text"
                        id="item_source"
                        class="form-control auto-save"
                        name="object_source"
                        value="<?php echo $source ?>"
                        placeholder="<?php _e('What\'s the item source', 'tainacan'); ?>"
                        value="" >
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
    $this->initScriptsSourceContainer($property,$item_id);
}


    /**
    *
    * @param type $property
    * @param type $item_id
    * @param type $index
    */
    public function initScriptsSourceContainer($property, $item_id) {
        ?>
              <script>
                      $('#item_source').keyup(function(){
                            $.ajax({
                                url: $('#src').val() + '/controllers/object/form_item_controller.php',
                                type: 'POST',
                                data: {
                                    operation: 'saveSource',
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
                                        operation: 'getSource',
                                        item_id:args[0]
                                    }
                                }).done(function (result) {
                                    var json = JSON.parse(result);
                                    if(json.value){
                                        $('#item_source').val(json.value);
                                    }
                                });
                     });
                     
                    Hook.register(
                        'get_multiple_item_value',
                        function ( args ) {
                            $('#item_source').val('');
                            $('#item_source').attr("placeholder", "<?php _e('Alter ', 'tainacan') ?>" + args.length + " <?php _e(' items', 'tainacan') ?>");
                    });
              </script>
        <?php
    }

}
