<!-- Positive argument -->
    <div class="modal fade" 
         id="modalReplyPositiveArgument" 
         tabindex="-1" 
         role="dialog" 
         aria-labelledby="modalReplyPositiveArgument" 
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form_positive_argument">
                    <div class="modal-header">
                        <button type="button" 
                                style="color:black;" 
                                class="close" 
                                data-dismiss="modal" 
                                aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" 
                            id="myModalLabel">
                                <?php _e('New argument','tainacan') ?>
                        </h4>
                    </div>
                    <div class="modal-body"  >
                        <div class="form-group">
                            <i><b>"<span id="argument_positive_text"></span>"</b></i>
                        </div>
                        <hr>
                        <div  class="form-group">
                           <label for="exampleInputPassword1"><span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<?php _e('Describe a positive argument','tainacan') ?></label>
                           <textarea name="positive_argument" class="form-control"   ></textarea>
                        </div>
                        <div  class="form-group" style="margin-bottom: -10px;">
                            <center>
                                <a style="cursor: pointer;" onclick="toggle_additional_information('#properties_positive')">
                                    <?php _e('Additional informations','tainacan') ?><br>
                                    <span class="glyphicon glyphicon-chevron-down"></span>
                                </a>    
                            </center>
                            <div style="display: none;" id="properties_positive"></div>
                        </div>    
                        <input type="hidden" id="collection_postive_argument_id" name="collection_id" value="">
                        <input type="hidden" name="classifications" value="">
                        <input type="hidden" name="root_argument" value="<?php echo $object->ID; ?>">
                        <input type="hidden" name="argument_parent" value="">
                        <input type="hidden" name="operation" value="add_reply_positive">
                    </div>
                    <div class="modal-footer">
                        <button style="color:grey;" type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan') ?></button>
                        <button type="submit" class="btn btn-primary" ><?php _e('Save', 'tainacan') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- negative argument -->
    <div class="modal fade" 
         id="modalReplyNegativeArgument" 
         tabindex="-1" 
         role="dialog" 
         aria-labelledby="modalReplyNegativeArgument" 
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form_negative_argument">
                    <div class="modal-header">
                        <button type="button" 
                                style="color:black;" 
                                class="close" 
                                data-dismiss="modal" 
                                aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" 
                            id="myModalLabel">
                                <?php _e('New argument','tainacan') ?>
                        </h4>
                    </div>
                    <div class="modal-body"  >
                        <div class="form-group">
                            <i><b>"<span id="argument_negative_text"></span>"</b></i>
                        </div>
                        <hr>
                        <div  class="form-group">
                           <label for="exampleInputPassword1"><span class="glyphicon glyphicon-thumbs-down"></span>&nbsp;<?php _e('Describe a positive argument','tainacan') ?></label>
                           <textarea name="negative_argument" class="form-control"   ></textarea>
                        </div>
                        <div  class="form-group" style="margin-bottom: -10px;">
                            <center>
                                <a style="cursor: pointer;" onclick="toggle_additional_information('#properties_negative')">
                                    <?php _e('Additional informations','tainacan') ?><br>
                                    <span class="glyphicon glyphicon-chevron-down"></span>
                                </a>    
                            </center>
                            <div style="display: none;" id="properties_negative"></div>
                        </div>    
                        <input type="hidden" name="collection_id" value="">
                        <input type="hidden" name="classifications" value="">
                        <input type="hidden" name="root_argument" value="<?php echo $object->ID; ?>">
                        <input type="hidden" name="argument_parent" value="">
                        <input type="hidden" name="operation" value="add_reply_negative">
                    </div>
                    <div class="modal-footer">
                        <button style="color:grey;" type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan') ?></button>
                        <button type="submit" class="btn btn-primary" ><?php _e('Save', 'tainacan') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!--Editando comentario -->
 <div class="modal fade" 
         id="modalEditArgument" 
         tabindex="-1" 
         role="dialog" 
         aria-labelledby="modalEditArgument" 
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form_positive_argument">
                    <div class="modal-header">
                        <button type="button" 
                                style="color:black;" 
                                class="close" 
                                data-dismiss="modal" 
                                aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" 
                            id="myModalLabel">
                                <?php _e('Edit argument','tainacan') ?>
                        </h4>
                    </div>
                    <div class="modal-body"  >
                        <div  class="form-group">
                           <label for="exampleInputPassword1"><?php _e('Argument text','tainacan') ?></label>
                           <textarea name="argument" id="text-edit-argument" class="form-control"   ></textarea>
                        </div>
                        <div  class="form-group" id="edit-type-comment">
                           <label for="exampleInputPassword1"><?php _e('Argument type','tainacan') ?></label> : 
                           <input type="radio" id="edit-argument-positive" value="positive">&nbsp;<span class="glyphicon glyphicon-thumbs-up"></span>
                           <input type="radio" id="edit-argument-negative" value="negative">&nbsp;<span class="glyphicon glyphicon-thumbs-down"></span>
                        </div>
                        <div  class="form-group" style="margin-bottom: -10px;">
                            <center>
                                <a style="cursor: pointer;" onclick="toggle_additional_information('#properties_edit')">
                                    <?php _e('Additional informations','tainacan') ?><br>
                                    <span class="glyphicon glyphicon-chevron-down"></span>
                                </a>    
                            </center>
                            <div style="display: none;" id="properties_edit"></div>
                        </div>    
                        <input type="hidden" id="collection_edit_argument_id" name="collection_id" value="">
                        <input type="hidden" name="argument_position" value="">
                        <input type="hidden" name="argument_id" value="">
                        <input type="hidden" name="operation" value="update_argument">
                    </div>
                    <div class="modal-footer">
                        <button style="color:grey;" type="button" class="btn btn-default pull-left" ><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Send to trash', 'tainacan') ?></button>
                        <button style="color:grey;" type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan') ?></button>
                        <button type="submit" class="btn btn-success" ><?php _e('Save', 'tainacan') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>