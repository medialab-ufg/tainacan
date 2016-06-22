<?php

require_once(dirname(__FILE__) . '../../../models/social_network/facebook_model.php');
require_once(dirname(__FILE__) . '../../../models/object/object_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');
//require_once(dirname(__FILE__) . '../../../models/user/facebook.php');
//require_once(dirname(__FILE__) . '../../../models/user/FacebookSocialDB.class.php');
require_once(dirname(__FILE__) . '../../../models/social_network/Facebook/autoload.php');

class FacebookController extends Controller {

    public function operation($operation, $data) {
        $facebook_model = new FacebookModel();
        switch ($operation) {

            case "getAccessToken":
                if (!session_id()) {
                    session_start();
                }
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
                    $facebook_model->getPhotos($accessToken, $data['collection_id']);
                    // Now you can redirect to another page and use the
                    // access token from $_SESSION['facebook_access_token']
                } else {
                    wp_redirect(get_the_permalink($data['collection_id']));
                }

                break;
            case "getPhotosFacebook":
                return $facebook_model->getPhotos($data);
                break;

            case "updatePhotosFacebook":
                return;
                break;

            case "insertIdentifierFacebook":
                return FacebookModel::insert_facebook_identifier($data['identifier'], $data['collectionId']);
                break;


            case "listIdentifiersFacebook":
                return FacebookModel::list_facebook_identifier($data['collectionId']);
                break;

            case "editIdentifierFacebook":
                return FacebookModel::edit_facebook_identifier($data['identifier'], $data['new_identifier']);
                break;

            case "deleteIdentifierFacebook":
                return FacebookModel::delete_facebook_identifier($data['identifier'], $data['collection_id']);
                break;
        }
    }

}

if ($_POST['operation']) {
    $operation = $_POST['operation'];
    $data = $_POST;
} else {
    $operation = $_GET['operation'];
    $data = $_GET;
}

$facebook_controller = new FacebookController();
echo $facebook_controller->operation($operation, $data);
?>