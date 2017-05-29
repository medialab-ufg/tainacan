<?php
include_once("js/register_js.php");
require_once(dirname(__FILE__) . '../../../models/social_network/Facebook/autoload.php');
session_start();

$config = get_option('socialdb_theme_options');
$app['app_id'] = $config['socialdb_fb_api_id'];
$app['app_secret'] = $config['socialdb_fb_api_secret'];

if (!empty($app['app_id']) && !empty($app['app_secret'])) {
    $fb = new Facebook\Facebook([
        'app_id' => $app['app_id'],
        'app_secret' => $app['app_secret'],
        'default_graph_version' => 'v2.3',
    ]);

    $helper = $fb->getRedirectLoginHelper();
    $permissions = ['email', 'user_birthday']; // optional
    $loginUrl = $helper->getLoginUrl(get_bloginfo(template_directory) . '/controllers/user/user_controller.php?collection_id=' . $collection_id . '&operation=return_login_fb', $permissions);

}
?>
<div class="col-md-12" style="background: #E8E8E8; padding-top: 50px; margin-top: -20px; padding-bottom: 50px;">
    <div class="col-md-7 center" style="background: white; border: 2px solid #d8d6d6; padding: 25px">

        <form id="formUserRegister" name="formUserRegister" type="POST">
            <input type="hidden" name="operation" value="add">

            <h4 style="font-weight: bolder; margin: 0;"><?php _e('Register', 'tainacan'); ?></h4>
            <hr style="margin-top: 5px;">

            <div class="col-md-12 no-padding" style="margin: 0px 0 20px 0;">
                <div class="col-md-6" style="padding-left: 0;">
                    <?php if ($loginUrl) { ?>
                        <a href="<?php echo $loginUrl;?>" class="btn btn-primary" style="width: 100%;">
                            <?php _e('Register with Facebook', 'tainacan'); ?>
                        </a>
                    <?php } ?>
                </div>
                <div class="col-md-6" style="padding-right: 0;">
                    <?php if (isset($authUrl)) { ?>
                        <a href="<?php echo $authUrl; ?>"><img src="<?php echo get_template_directory_uri(); ?>/libraries/images/plus_login.png" style="max-width: 150px;" /></a>
                        <a href="#" class="btn btn-danger" style="width: 100%;">
                            <?php _e('Register with Google Plus', 'tainacan'); ?>
                        </a>
                    <?php } ?>
                </div>
            </div>

            <?php if($loginUrl || $authUrl): ?>
                <div class="col-md-12">
                    <div class="col-md-5" style="border-bottom: 1px solid #e8e8e8"></div>
                    <div class="col-md-2 cnter" style="text-align: center">
                        <?php _e('or', 'tainacan'); ?>
                    </div>
                    <div class="col-md-5" style="border-bottom: 1px solid #e8e8e8"></div>
                </div>
            <?php endif; ?>

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

                <?php
                if(current_user_can('administrator'))
                {
                    if(has_action('add_root_properties'))
                    {
                        do_action('add_root_properties');
                    }
                }
                ?>
            </div>

            <a href="#" class="more-options-register"> <?php _e('More options', 'tainacan'); ?> </a>
            <br> <br>

            <div class="expanded-register" style="display: none">

                <?php if(has_action('add_new_user_properties'))
                {
                    do_action('add_new_user_properties');
                }
                ?>

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
