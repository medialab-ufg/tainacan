<?php
require_once(dirname(__FILE__) . '../../../models/user/user_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');
include_once (dirname(__FILE__) . '../../../models/event/event_object/event_object_create_model.php');
//require_once(dirname(__FILE__) . '../../../models/user/facebook.php');
//require_once(dirname(__FILE__) . '../../../models/user/FacebookSocialDB.class.php');
require_once(dirname(__FILE__) . '../../../models/social_network/Facebook/autoload.php');
try{
    require_once(dirname(__FILE__) . '../../../models/user/google-api-php-client/src/apiClient.php');
    require_once(dirname(__FILE__) . '../../../models/user/google-api-php-client/src/contrib/apiPlusService.php');
} catch (Exception $e){
    
}

class UserController extends Controller {

    public function operation($operation, $data) {
        $user_model = new UserModel();
        switch ($operation) {
            case 'show_username':
                return $user_model->show_username($data);
            case "list_user":
                return $user_model->list_user($data);
                break;
            case "show_all_users":
                return $this->render(dirname(__FILE__) . '../../../modules/tainacan-library/views/users.php');
                break;
            case "add":
                return $user_model->register_user($data);
                break;
            case "show_login_screen":
                $options = get_option('socialdb_theme_options');
//                $data['facebook'] = new Facebook(array(
//                    'appId' => $options['socialdb_fb_api_id'],
//                    'secret' => $options['socialdb_fb_api_secret'],
//                    'cookie' => true,
//                ));
                $data['facebook_option'] = ['api_id' => $options['socialdb_fb_api_id'], 'api_secret' => $options['socialdb_fb_api_secret']];
                $data['gplus_option'] = ['client_id' => $options['socialdb_google_client_id'], 'secret_key' => $options['socialdb_google_secret_key'], 'api_key' => $options['socialdb_google_api_key']];

                /*
                if (!in_array('', $data['gplus_option'])):
                    try{
                        $data['gplus_client'] = new apiClient();
                        $data['gplus_client']->setApplicationName("Tainacan");

                        //*********** Replace with Your API Credentials **************
                        $data['gplus_client']->setClientId($options['socialdb_google_client_id']);
                        $data['gplus_client']->setClientSecret($options['socialdb_google_secret_key']);
                        $data['gplus_client']->setRedirectUri(site_url() . '/wp-content/themes/theme_socialdb/controllers/user/user_controller.php?operation=return_login_gplus');
                        $data['gplus_client']->setDeveloperKey($options['socialdb_google_api_key']);
                        //************************************************************

                        $data['gplus_client']->setScopes(array('https://www.googleapis.com/auth/plus.me', 'https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile'));
                        $plus = new apiPlusService($data['gplus_client']);

                        $data['authUrl'] = $data['gplus_client']->createAuthUrl();
                    }catch(Exception $e){
                        $data['error'] = $e->getMessage();
                    }
                endif;
                */

                return $this->render(dirname(__FILE__) . '../../../views/user/login.php', $data);
                break;
            case "return_login_fb":
                session_start();
                $config = get_option('socialdb_theme_options');
                $app['app_id'] = $config['socialdb_fb_api_id'];
                $app['app_secret'] = $config['socialdb_fb_api_secret'];

                $fb = new Facebook\Facebook([
                    'app_id' => $app['app_id'],
                    'app_secret' => $app['app_secret'],
                    'default_graph_version' => 'v2.4',
                ]);

                $helper = $fb->getRedirectLoginHelper();
                try {
                    $accessToken = $helper->getAccessToken();
                } catch (Facebook\Exceptions\FacebookResponseException $e) {
                    // When Graph returns an error
                    echo 'Graph returned an error: ' . $e->getMessage();
                    exit;
                } catch (Facebook\Exceptions\FacebookSDKException $e) {
                    // When validation fails or other local issues
                    echo 'Facebook SDK returned an error: ' . $e->getMessage();
                    exit;
                }
                if (isset($accessToken)) {
                    // Logged in!
                    //$_SESSION['facebook_access_token'] = (string) $accessToken;
                    // Now you can redirect to another page and use the
                    // access token from $_SESSION['facebook_access_token']
                    $login = $user_model->fb_register_login($accessToken, $data['collection_id'], $app);
                    wp_redirect(get_the_permalink($data['collection_id']));
//                    if($login){
//                        wp_redirect(get_the_permalink($data['collection_id']));
//                    }else{
//                        wp_redirect(get_the_permalink($data['collection_id']));
//                    }
                } else {
                    return $this->render(dirname(__FILE__) . '../../../views/user/login.php', $data);
                }

                //Old Version
//                $fb = new FacebookSocialDB;
//
//                $user = $fb->create_user();
//
//                if ($user) {
//                    wp_redirect(get_the_permalink($data['collection_id']));
//                } else {
//                    return $this->render(dirname(__FILE__) . '../../../views/user/login.php', $data);
//                }
                break;

            case "return_login_gplus":
                $options = get_option('socialdb_theme_options');
                $data['gplus_client'] = new apiClient();
                $data['gplus_client']->setApplicationName("Tainacan");

                //*********** Replace with Your API Credentials **************
                $data['gplus_client']->setClientId($options['socialdb_google_client_id']);
                $data['gplus_client']->setClientSecret($options['socialdb_google_secret_key']);
                $data['gplus_client']->setRedirectUri(site_url() . '/wp-content/themes/theme_socialdb/controllers/user/user_controller.php?operation=return_login_gplus');
                $data['gplus_client']->setDeveloperKey($options['socialdb_google_api_key']);
                //************************************************************

                $data['gplus_client']->setScopes(array('https://www.googleapis.com/auth/plus.me', 'https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile'));
                $plus = new apiPlusService($data['gplus_client']);

                if (isset($_GET['code'])) {
                    $data['gplus_client']->authenticate();
                    $access_token = $data['gplus_client']->getAccessToken();
                    $data['gplus_client']->setAccessToken($access_token);

                    $me = $plus->people->get('me');

                    $optParams = array('maxResults' => 100);
                    $activities = $plus->activities->listActivities('me', 'public', $optParams);

                    $_SESSION['access_token'] = $data['gplus_client']->getAccessToken();
                } else {
                    return $this->render(dirname(__FILE__) . '../../../views/user/login.php', $data);
                }

                $user = $user_model->create_user_gplus($me, $_SESSION['access_token']);
                unset($_SESSION['access_token']);

                if ($user) {
                    wp_redirect(get_the_permalink(get_option('collection_root_id')));
                } else {
                    return $this->render(dirname(__FILE__) . '../../../views/user/login.php', $data);
                }

                break;
            case "login_regular":
                $data['username'] = strip_tags(trim($data['username']));
                $user = $user_model->do_login($data['username'], $data['password']);
                if ($user) {
                    $data['login'] = 1;
                    if($data['collection_id'] && $data['collection_id'] !='0' && get_post($data['collection_id'])->post_type == 'socialdb_collection'){
                        $data['url'] = get_the_permalink($data['collection_id']);
                    }else{
                         $data['url'] = site_url();
                    }
                    $_log_data = [ 'collection_id' => $data['collection_id'], 'user_id' => $user->ID,
                        'event_type' => 'user_status', 'event' => 'login'];
                    Log::addLog($_log_data);
                } else {
                    $data['login'] = 0;
                    $data['title'] = __('Failed to Login','tainacan');
                    $data['msg'] = __('Your username and/or password are wrong. User not Found!','tainacan');
                    $data['type'] = 'error';
                }
                return json_encode($data);
                break;
            case "forgot_password":
                return json_encode($user_model->forgot_password($data['user_login_forgot']));
                break;
            case "show_profile_screen":
                return $this->render(dirname(__FILE__) . '../../../views/user/profile.php', $data);
                break;
            case "change_password":
                return json_encode($user_model->reset_password($data));
                break;
            case "register_user":
                return $this->render(dirname(__FILE__) . '../../../views/user/register.php');
                break;
            case "share_item_email_or_collection":
                $data['email'] = (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false ? $data['email'] : null);
                if(!empty($data['email'])){
                    //envia email ao usuario
                    $result = $user_model->send_share_email($data);
                }
                if(!empty($data['new_collection'])){
                    //relaciona o item a outra coleção
                    $eventAddObject = new EventObjectCreateModel();
                    $data['socialdb_event_object_item_id'] = $data['object_id'];
                    $data['socialdb_event_collection_id'] = trim($data['new_collection']);
                    $data['socialdb_event_user_id'] = get_current_user_id();
                    $data['socialdb_event_create_date'] = time();
                    return  $eventAddObject->create_event($data);
                }
                return json_encode($result);
                break;
            case 'search-colaborators':
                $colaborators = [];
                $result = $user_model->search_participatory_authors($data['collection_id'], $data['search']);
                if(is_array($result)){
                    foreach ($result as $value) {
                        $value->avatar = get_avatar($value,64);
                        $colaborators[] = $value;
                    }
                }
                return json_encode($colaborators);
        }
    }

}

/*
 * Controller execution
 */

if (isset($_POST['operation'])) {
    $operation = $_POST['operation'];
    $data = $_POST;
} else {
    $operation = $_GET['operation'];
    $data = $_GET;
}

$user_controller = new UserController();
echo $user_controller->operation($operation, $data);
?>