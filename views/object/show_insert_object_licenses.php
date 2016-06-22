<?php 
/*
 * View Responsavel em mostrar as licenças na hora de INSERCAO do objeto, NAO UTILIZADA NOS EVENTOS
 */
include_once ('js/show_insert_object_licenses_js.php'); 
$has_cc = 0;
if(isset($licenses) && !empty($licenses)): ?>
    <h4><?php _e('Object License','tainacan'); ?></h4>
    <?php foreach ($licenses as $license) { ?>
            <?php if(strpos($license['nome'], 'Creative Commons') !== false) $has_cc = 1;?>
            <div class="radio">
                <label><input type="radio" 
                              class="object_license" 
                              name="object_license" 
                              value="<?php echo $license['id']; ?>" 
                              id="radio<?php echo $license['id']; ?>" 
                              <?php 
                              if($license['id'] == $pattern[0]){ 
                                  $has_checked = true;
                                  echo "checked='checked'"; 
                              } 
                              ?> 
                              required="required"><?php echo $license['nome']; ?></label>
            </div>
    <?php  } ?>
<?php else: ?>    
    <input type="hidden" class='hide_license' value="true">
<?php endif; ?>

<?php if(isset($has_checked)): ?>    
    <input type="hidden" class='already_checked_license' value="true">
<?php endif; ?>    
    
    
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
