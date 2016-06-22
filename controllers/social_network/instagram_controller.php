<?php

require_once(dirname(__FILE__) . '../../../models/social_network/instagram_model.php');
require_once(dirname(__FILE__) . '../../../models/object/object_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class InstagramController extends Controller {

    public function operation($operation, $data) {

        switch ($operation) {
            //BEGIN New Code
            case "getAllPhotosInstagram":
                $object_model = new ObjectModel();
                $config = get_option('socialdb_theme_options');
                $instagramUser = new InstagramModel($data['nameUserInstagram'], $config);
                $return = $instagramUser->getAllUserMediaRecent($data, $object_model);
                session_start();
                if($return){
                    $_SESSION['instagramInsertedIds'] = json_encode($return);
                }else{
                    $_SESSION['instagramInsertedIds'] = 'instagram_error';
                }
                break;
            //END New Code
            //*********************************************************************************************//
            case "getPhotosInstagram":
                $config = get_option('socialdb_theme_options');
                $instagramUser = new InstagramModel(null, $config);
                $instagramUser->loginInstagram();
                break;

//            case "getAllPhotosInstagram":
//                $object_model = new ObjectModel();
//                $config = get_option('socialdb_theme_options');
//                $instagramUser = new InstagramModel($data['nameUserInstagram'], $config);
//                $instagramUser->getAllUserMediaRecent($data, $object_model);
//                break;

            case "insertIdentifierInstagram":
                return InstagramModel::insert_instagram_identifier($data['identifier'], $data['collectionId']);
                break;

            case "listIdentifiersInstagram":
                return InstagramModel::list_instagram_identifier($data['collectionId']);
                break;

            case "editIdentifierInstagram":
                return InstagramModel::edit_instagram_identifier($data['identifier'], $data['new_identifier']);
                break;

            case "deleteIdentifierInstagram":
                return InstagramModel::delete_instagram_identifier($data['identifier'], $data['collection_id']);
                break;
        }
    }

}

/**
 * se a API do instagram respondeu com o CODE necessário para
 * trocar pelo token de acesso, chama a operação getAllPhotosInstagram
 */
if (isset($_GET['code'])) {
    session_start();
    //código de resposta da API
    $data['code'] = $_GET['code'];
    //identificador do usuário que veio via ajax
    $data['nameUserInstagram'] = $_SESSION['nameUser'];
    $data['postIdUserInstagram'] = $_SESSION['instagram_post_id'];
    //a id da coleção corrente
    $data['collection_id'] = $_SESSION['collection_id'];
//    if(isset($_SESSION['real_op'])){
//        $data['real_op'] = $_SESSION['real_op'];
//    }
//    if(isset($_SESSION['lastDate'])){
//        $data['lastDate'] = $_SESSION['lastDate'];
//    }
    $instagram_controller = new InstagramController();
    //limpa da memória os dados de sessão
    $_SESSION = array();
    session_destroy();
    //devolve a resposta para a requisição ajax
    $instagram_controller->operation('getAllPhotosInstagram', $data);
    //redireciona para a coleção corrente
    wp_redirect(get_the_permalink($data['collection_id']));
}

// nome da operação e os dados para realizá-la
if ($_POST['operation']) {
    $operation = $_POST['operation'];
    $data = $_POST;
} else {
    $operation = $_GET['operation'];
    $data = $_GET;
}

/**
 * se a operação getPhotosInstagram for invocada,
 * um redirecionamento para login e autenticação no instagram será realizado 
 */
if ($operation == 'getPhotosInstagram') {
    session_start();
    $_SESSION['nameUser'] = $data['identifier'];
//    $_SESSION['instagram_post_id'] = $data['post_id'];
    $_SESSION['collection_id'] = $data['collection_id'];
//    if(isset($data['real_op'])){
//        $_SESSION['real_op'] = $data['real_op'];
//    }
//    if(isset($data['lastDate'])){
//        $_SESSION['lastDate'] = $data['lastDate'];
//    }
    $instagram_controller = new InstagramController();
    //o redirecionamento para tela de login do instagram
    $instagram_controller->operation($operation, null);
} else {
    //qualquer outra operação que não seja getPhotosInstagram cairá aqui
    $instagram_controller = new InstagramController();
    echo $instagram_controller->operation($operation, $data);
}
?>