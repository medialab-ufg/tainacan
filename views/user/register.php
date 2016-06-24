<div class="col-md-12" style="background: #E8E8E8; padding-top: 50px; margin-top: -20px;">
    <div class="col-md-7 center" style="background: white; ">
        <form id="formUserRegister" name="formUserRegister" >
            <input type="hidden" name="operation" value="add">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"><?php _e('Register', 'tainacan'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="first_name"><?php _e('First Name', 'tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                    <input type="text" required="required" class="form-control" name="first_name" id="first_name" placeholder="<?php _e('Type here your first name', 'tainacan'); ?>">
                </div>
                <div class="form-group">
                    <label for="last_name"><?php _e('Last Name', 'tainacan'); ?><!--span style="color: #EE0000;"> *</span--></label>
                    <input type="text" class="form-control" name="last_name" id="last_name" placeholder="<?php _e('Type here your last name', 'tainacan'); ?>">
                </div>
                <div class="form-group">
                    <label for="user_email"><?php _e('Email', 'tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                    <input type="email" required="required" class="form-control" name="user_email" id="user_email" placeholder="<?php _e('Type here your e-mail', 'tainacan'); ?>">
                </div>
                <div class="form-group">
                    <label for="user_login"><?php _e('Username', 'tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                    <p class="help-block"><?php _e('Help: Limit of 25 characters', 'tainacan'); ?></p>
                    <input onkeyup="showUserName(this)" maxlength="25" type="text" required="required" class="form-control" name="user_login" id="user_login" placeholder="<?php _e('Type here the username that you will use for login', 'tainacan'); ?>">
                    <span id="result_username"></span>
                </div>
                <div class="form-group">
                    <label for="user_pass"><?php _e('Password', 'tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                    <input  type="password" required="required" class="form-control" name="user_pass" id="user_pass" placeholder="<?php _e('Type here your password', 'tainacan'); ?>">
                </div>
                <div class="form-group">
                    <label for="user_pass"><?php _e('Confirm Password', 'tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                    <input type="password" required="required" class="form-control" name="user_conf_pass" id="user_conf_pass" placeholder="<?php _e('Confirm your password', 'tainacan'); ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" onclick="check_register_fields(); return false;"><?php _e('Register', 'tainacan'); ?></button>
            </div>
        </form>
    </div>
</div>

<?php /*
<!-- TAINACAN: modal padrao bootstrap, aberto pelo id, responsavel pelo o cadastro de usuario -->
<div class="modal fade" id="myModalRegister" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            
        </div>
    </div>
</div>
<!-- TAINACAN: modal padrao bootstrap aberto via javascript pelo seu id, formulario inicial para criacao de colecao -->
<div class="modal fade" id="modalImportCollection" tabindex="-1" role="dialog" aria-labelledby="modalImportCollectionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="importCollection">
                <input type="hidden" name="operation" value="importCollection">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php _e('Import Collection', 'tainacan'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="collection_file"><?php _e('Select the file', 'tainacan'); ?></label>
                        <input type="file" required="required" class="form-control" name="collection_file" id="collection_file" >
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php _e('Import', 'tainacan'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
 */ ?>
