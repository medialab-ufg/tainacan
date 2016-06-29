<?php include_once("js/register_js.php"); ?>
<div class="col-md-12" style="background: #E8E8E8; padding-top: 50px; margin-top: -20px; padding-bottom: 50px;">
    <div class="col-md-7 center" style="background: white; ">
        <form id="formUserRegister" name="formUserRegister" >
            <input type="hidden" name="operation" value="add">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"><?php _e('Register', 'tainacan'); ?></h4>
            </div>

            <div class="col-md-12 no-padding" style="margin: 20px 0 20px 0;">
                <div class="col-md-6" style="padding-left: 0;">
                    <a href="#" class="btn btn-primary" style="width: 100%;">
                        <?php _e('Register with Facebook', 'tainacan'); ?>
                    </a>
                </div>
                <div class="col-md-6" style="padding-right: 0;">
                    <a href="#" class="btn btn-danger" style="width: 100%;">
                        <?php _e('Register with Google Plus', 'tainacan'); ?>
                    </a>
                </div>
            </div>

            <div class="col-md-12">
                <div class="col-md-5" style="border-bottom: 1px solid #e8e8e8"></div>
                <div class="col-md-2 cnter" style="text-align: center">
                    <?php _e('or', 'tainacan'); ?>
                </div>
                <div class="col-md-5" style="border-bottom: 1px solid #e8e8e8"></div>
            </div>

            <div>
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

            <a href="#" class="more-options-register"> <?php _e('More options', 'tainacan'); ?> </a>
            <br> <br>

            <div class="expanded-register" style="display: none">
                <div class="form-group">
                    <label for="about_you"> <?php _e('About you', 'tainacan'); ?> </label>
                    <input type="text" name="about_you" class="form-control about_you">
                </div>
                <div class="form-group">
                    <label for="current_work"> <?php _e('Current workplace', 'tainacan'); ?> </label>
                    <input type="text" name="current_work" class="form-control current_work">
                </div>
                <div class="form-group">
                    <label for="prof_resume"> <?php _e('Professional Resume', 'tainacan'); ?> </label>
                    <input type="text" name="prof_resume" class="form-control prof_resume">
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

 */ ?>
