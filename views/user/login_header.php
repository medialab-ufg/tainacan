<?php
include_once ('js/login_header_js.php');
require_once(dirname(__FILE__) . '../../../models/social_network/Facebook/autoload.php');

if(!isset($_SESSION)){
   //session_start(); 
}


$_redir_url = get_bloginfo(template_directory) . '/controllers/user/user_controller.php?collection_id=' . $collection_id . '&operation=return_login_fb';

/*    
    $loginUrl = $facebook->getLoginUrl( array( 'scope' => 'email,user_birthday','redirect_uri' =>  $_redir_url ));
    $logoutUrl = $facebook->getLogoutUrl();
*/

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
                <div class="col-md-2 cnter" style="text-align: center">
                    <?php _e('or', 'tainacan'); ?>
                </div>
                <div class="col-md-5" style="border-bottom: 1px solid #e8e8e8"></div>
            </div>
        <?php endif; ?>

        <form action="" id="LoginForm" name="LoginForm" class="form-signin">
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
</div>

<div class="modal fade" id="myModalForgotPasswordHeader" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="formUserForgotPasswordHeader" name="formUserForgotPassword" >  
                <input type="hidden" name="operation" value="forgot_password">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php _e('Forgot Password?','tainacan'); ?></h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="user_login"><?php _e('Username or Email','tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                        <input type="text" required="required" class="form-control" name="user_login_forgot" id="user_login_forgot" placeholder="<?php _e('Type here the username that you will use for login or your email','tainacan'); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close','tainacan'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php _e('Send','tainacan'); ?></button>
                </div>
            </form>    
        </div>
    </div>
</div>
