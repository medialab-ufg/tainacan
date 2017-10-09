<?php
require_once(dirname(__FILE__) . '../../../models/social_network/Facebook/autoload.php');

global $wp_query;
$collection_id = $wp_query->post->ID;
$_redir_url = get_bloginfo(template_directory) . '/controllers/user/user_controller.php?collection_id=' . $collection_id . '&operation=return_login_fb';

$config = get_option('socialdb_theme_options');
$app['app_id'] = $config['socialdb_fb_api_id'];
$app['app_secret'] = $config['socialdb_fb_api_secret'];

if (!empty($app['app_id']) && !empty($app['app_secret'])) {
    try{
        $fb = new Facebook\Facebook([
            'app_id' => $app['app_id'],
            'app_secret' => $app['app_secret'],
            'default_graph_version' => 'v2.3',
        ]);

        $helper = $fb->getRedirectLoginHelper();
        $permissions = ['email', 'user_birthday']; // optional
        $loginUrl = $helper->getLoginUrl( $_redir_url, $permissions);

    }catch(Exception $e){
        $loginUrl = false;
    }
}
?>
<input type="hidden" id="src_login" name="src" value="<?php echo get_template_directory_uri() ?>">

<div id="login-out" class="col-md-12 login-outer-container">
    <div id="login-in" class="col-md-5 center container login-inner-container">
    
        <h4 style="font-weight: bolder; margin: 10px 0;"><?php _e('Login', 'tainacan'); ?></h4>
        <hr>

        <?php if($loginUrl || $authUrl): ?>
            <div class="col-md-12 no-padding" style="margin: 0px 0 20px 0;">
                <div class="col-md-6" style="padding-left: 0;">
                    <?php if ($facebook_option['api_id'] && $facebook_option['api_secret']) { ?>
                        <a href="<?php echo $loginUrl;?>" class="btn btn-primary" style="width: 100%;">
                            <?php _e('Login with Facebook', 'tainacan'); ?>
                        </a>
                    <?php } ?>
                </div>
                <div class="col-md-6" style="padding-right: 0;">
                    <?php if (isset($authUrl)) { ?>
                        <a href="<?php echo $authUrl; ?>"><img src="<?php echo get_template_directory_uri(); ?>/libraries/images/plus_login.png" style="max-width: 150px;" /></a>
                        <a href="#" class="btn btn-danger" style="width: 100%;">
                            <?php _e('Login with Google Plus', 'tainacan'); ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if($loginUrl || $authUrl): ?>
            <div class="col-md-12">
                <div class="col-md-5" style="border-bottom: 1px solid #e8e8e8"></div>
                <div class="col-md-2 center" style="text-align: center">
                    <?php _e('or', 'tainacan'); ?>
                </div>
                <div class="col-md-5" style="border-bottom: 1px solid #e8e8e8"></div>
            </div>
        <?php endif; ?>

        <form id="LoginForm" name="LoginForm" class="form-signin">
            <input type="hidden" id="operation_log" name="operation" value="login_regular">
            <input type="hidden" id="collection_id_login" name="collection_id" value="<?php echo $collection_id; ?>">

            <label for="inputEmail" class="sr-only-no"><?php _e("Username",'tainacan'); ?> </label>
            <input type="text" id="inputUsername" name="username" class="form-control" placeholder="<?php _e('Username','tainacan') ?>" required="" autofocus="">
            <br />
            <label for="inputPassword" class="sr-only-no"><?php _e("Password",'tainacan'); ?></label>
            <input type="password" id="inputPassword" name="password" class="form-control" placeholder="<?php _e('Password','tainacan') ?>" required="">

            <button class="btn btn-lg btn-primary pull-right send-login" type="submit"><?php _e("Login",'tainacan'); ?></button>
            <br><label><a style="cursor: pointer;" id="open_myModalForgotPasswordHeader"><?php _e("Forgot password?",'tainacan'); ?></a></label>
        </form>
    </div>
    <div id="forgot_password" class="hide_elem">
        <hr style="margin-top: 0px; margin-bottom: 8px">
        <form  id="formUserForgotPasswordHeader" name="formUserForgotPassword" >
            <input type="hidden" name="operation" value="forgot_password">
            <label for="user_login"><?php _e('Username or Email','tainacan'); ?><span style="color: #EE0000;"> *</span></label>
            <input type="text" required="required" class="form-control" name="user_login_forgot" id="user_login_forgot" placeholder="<?php _e('Username or e-mail that you use to login','tainacan'); ?>">

            <button type="submit" class="btn btn-default pull-right" style="background-color: #0c698b ;color: white;"><?php _e('Send','tainacan'); ?></button>
        </form>
    </div>
</div>
