<?php

class FormItemLicense extends FormItem {

    public function widget($property, $item_id) {
       $this->isRequired = get_post_meta($this->collection_id, 'socialdb_collection_property_'.$property['id'].'_required', true);
       $license_selected = get_post_meta($item_id, 'socialdb_license_id', true);
       $licenses = $this->getLicenses($item_id,$this->collection_id)
        ?>
        <!-- TAINACAN: a licencas do item -->
        <div class="form-group">
            <h2>
                <?php echo ($this->terms_fixed['license']) ? $this->terms_fixed['license']->name : _e('Licenses', 'tainacan') ?>
                <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>
            </h2>
            <div>
              <input type="hidden"
                    value="<?php echo get_post_meta($this->collection_id, 'socialdb_collection_property_'.$property['id'].'_mask_key', true) ?>">
                 <div class="form-group"
                      id="validation-<?php echo $property['id'] ?>-0-0"
                      style="border-bottom:none;padding: 0px;">
                  <?php
                  $has_cc = 0;
                  if(isset($licenses) && !empty($licenses)): ?>
                  <?php foreach ($licenses as $license) { ?>
                          <?php if(strpos($license['nome'], 'Creative Commons') !== false) $has_cc = 1;?>
                          <div class="radio">
                              <label><input type="radio"
                                            class="object_license"
                                            name="object_license"
                                            value="<?php echo $license['id']; ?>"
                                            id="radio<?php echo $license['id']; ?>"
                                            <?php
                                            if($license['id'] == $license_selected){
                                                $has_checked = true;
                                                echo "checked='checked'";
                                            }
                                            ?>
                                            ><?php echo $license['nome']; ?></label>
                          </div>
                    <?php  } ?>
                <?php else: ?>
                    <div class="alert alert-info"><?php _e('No licenses enabled in this collection')?></div>
                <?php endif; ?>
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
             <?php if($has_cc){ ?>
                 <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalHelpCC"><?php _e("Help Choosing",'tainacan'); ?></button><br><br>
             <?php } ?>

             <!-- modal ajuda a escolher CC -->
                 <div class="modal fade" id="modalHelpCC" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                     <div class="modal-dialog">
                         <div class="modal-content">
                             <form  id="submit_help_cc">
                                 <input type="hidden" name="operation" id="operationCC" id="" value="help_choosing_license">
                                 <div class="modal-header">
                                     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                     <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-ok"></span>&nbsp;<?php echo __('Help Choosing the License','tainacan'); ?></h4>
                                 </div>
                                 <div class="modal-body">
                                     <div class="create_form-group">
                                         <label for="commercial_use_license"><?php _e('Allow commercial uses of your work?','tainacan'); ?></label>
                                         <div class="radio">
                                             <label><input type="radio" name="commercial_use_license" id="" value="1"><?php _e('Yes','tainacan'); ?></label>
                                         </div>
                                         <div class="radio">
                                             <label><input type="radio" name="commercial_use_license" id="" value="0"><?php _e('No','tainacan'); ?></label>
                                         </div>
                                     </div>
                                     <div class="create_form-group">
                                         <label for="change_work_license"><?php _e('Allow modifications of your work?','tainacan'); ?></label>
                                         <div class="radio">
                                             <label><input type="radio" name="change_work_license" id="" value="1"><?php _e('Yes','tainacan'); ?></label>
                                         </div>
                                         <div class="radio">
                                             <label><input type="radio" name="change_work_license" id="" value="2"><?php _e('Yes, as long as others share alike','tainacan'); ?></label>
                                         </div>
                                         <div class="radio">
                                             <label><input type="radio" name="change_work_license" id="" value="0"><?php _e('No','tainacan'); ?></label>
                                         </div>
                                     </div>
                                 </div>

                                 <div class="modal-footer">
                                     <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close','tainacan'); ?></button>
                                     <button type="submit" class="btn btn-primary"><?php echo __('Choose a License','tainacan'); ?></button>
                                 </div>
                             </form>
                         </div>
                     </div>
                 </div>
            </div>
        </div>
        <?php
        $this->initScriptsLicenseContainer($property,$item_id);
    }

    /**
     * function show_collection_licenses()
     * @param array Array com os dados da colecao
     * @return As licenças habilitadas da colecao
     * @author Eduardo Humberto
     */
    public function getLicenses($item_id,$collection_id) {
        $licenses = [];
        $enabledLicenses = unserialize(get_post_meta($collection_id, 'socialdb_collection_license_enabled')[0]);
        if (isset($item_id)) {
            $data['pattern'] = get_post_meta($item_id, 'socialdb_license_id');
        } else {
            $data['pattern'] = get_post_meta($collection_id, 'socialdb_collection_license_pattern');
        }

        if ($enabledLicenses) {

            $arrLicenses = get_option('socialdb_standart_licenses');
            foreach ($arrLicenses as $license) {
                $object_post = get_post($license);
                if (in_array($object_post->ID, $enabledLicenses)) {
                    $data_license['id'] = $object_post->ID;
                    $data_license['nome'] = $object_post->post_title;

                    $licenses[] = $data_license;
                }
            }

            $arrLicenses_custom = get_option('socialdb_custom_licenses');
            if ($arrLicenses_custom) {
                foreach ($arrLicenses_custom as $license) {
                    $object_post = get_post($license);
                    if (in_array($object_post->ID, $enabledLicenses)) {
                        $data_license['id'] = $object_post->ID;
                        $data_license['nome'] = $object_post->post_title;

                        $licenses[] = $data_license;
                    }
                }
            }
            $collection_meta = get_post_meta($collection_id, 'socialdb_collection_license');
            if ($collection_meta):
                foreach ($collection_meta as $meta):
                    if ($meta):
                        $object_post = get_post($meta);
                        if (in_array($object_post->ID, $enabledLicenses)):
                            $data_license['id'] = $object_post->ID;
                            $data_license['nome'] = $object_post->post_title;

                            $licenses[] = $data_license;
                        endif;
                    endif;
                endforeach;
            endif;
        }

        return $licenses;
    }

    /**
    *
    * @param type $property
    * @param type $item_id
    * @param type $index
    */
    public function initScriptsLicenseContainer($property, $item_id) {
        ?>
              <script>
              $('#submit_help_cc').submit(function (e) {
                  e.preventDefault();
                  $.ajax({
                      url: $("#src").val() + '/controllers/object/object_controller.php',
                      type: 'POST',
                      data: new FormData(this),
                      processData: false,
                      contentType: false
                  }).done(function (result) {
                      $("#modalHelpCC").modal('hide');
                      elem = jQuery.parseJSON(result);
                      if(elem.id && elem.id != ''){
                          $('#radio' + elem.id).attr("checked", "checked");
                      }
                      showAlertGeneral(elem.title, elem.msg, elem.type);
                  });
              });


                  $('#object_license').change(function(){
                        <?php if($this->isRequired === 'true'):  ?>
                            validateFieldsMetadataText($(this).val(),'<?php echo $property['id'] ?>','0','0')
                        <?php endif; ?>
                        $.ajax({
                            url: $('#src').val() + '/controllers/object/form_item_controller.php',
                            type: 'POST',
                            data: {
                                operation: 'saveLicense',
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
