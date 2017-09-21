<?php

require_once(dirname(__FILE__) . '../../../models/social_network/youtube_model.php');
require_once(dirname(__FILE__) . '../../../models/object/object_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class YoutubeController extends Controller {

    public function operation($operation, $data) {
        switch ($operation) {
            //BEGIN New Code
            case "import_video_url":
                // recupera a chave da api cadastrada
                $title = '';
                $config = get_option('socialdb_theme_options');
                if(!$config['socialdb_youtube_api_id']|| trim($config['socialdb_youtube_api_id'])=='' || trim($config['socialdb_youtube_api_id'])=='0'){
                    $object_model = new ObjectModel();
                    $extracted = $object_model->extract_metatags($data['video_url']);
                    if($extracted && is_array($extracted)){
                        foreach ($extracted as $array) {
                            if($array['name_field']=='title'){
                                $title = $array['value'];
                            }
                        }
                    }
                    $id = socialdb_insert_object($title,'','draft');
                    if($id):
                        update_post_meta($id, 'socialdb_object_content', $data['video_url']);
                        if (strpos($data['video_url'], 'youtube.com') !== false) {
                            parse_str(parse_url($data['video_url'], PHP_URL_QUERY), $vars);
                            $object_model->add_thumbnail_url('https://i.ytimg.com/vi/' . $vars['v'] . '/0.jpg', $id);
                            update_post_meta($id, 'socialdb_object_dc_type', 'video');
                        }
                        return json_encode([$id]);
                    else:
                        return false;
                    endif;
                }
                $urlVideoYoutube = explode('/', $data['video_url']);
                $videoID = explode('=', $urlVideoYoutube[3]);
                if ($videoID[1]&&$config) {
                    $youtube = new youtubeModel($videoID[1], $config, false, true);
                    $object_model = new ObjectModel();
                    $id = $youtube->insertVideoItem($data, $object_model,'draft');
                    if($id):
                        return json_encode([$id]);
                    else:
                        return json_encode(['error'=>'no id']);
                    endif;
                } else {
                     return json_encode(['error'=>'no parsed']);
                }
                break;
            case "import_video_channel":
                // recupera a chave da api cadastrada
                $config = get_option('socialdb_theme_options');
                if(!$config['socialdb_youtube_api_id']|| trim($config['socialdb_youtube_api_id'])=='' || trim($config['socialdb_youtube_api_id'])=='0'){
                    $object_model = new ObjectModel();
                    $extracted = $object_model->extract_metatags($data['url']);
                    $image = '';
                    if($extracted && is_array($extracted)){
                        foreach ($extracted as $array) {
                            if($array['name_field']=='title'){
                                $title = $array['value'];
                            }
                            else if($array['name_field']=='og:image'){
                                $image = (strpos($array['value'], 'http')===false) ? 'http:'.$array['value'] : $array['value'];
                            }
                        }
                    }
                    $id = socialdb_insert_object($title,'','draft');
                    if($id):
                        update_post_meta($id, 'socialdb_object_content', $data['url']);
                        if(!empty($image))
                            $object_model->add_thumbnail_url($image, $id);
                        update_post_meta($id, 'socialdb_object_dc_type', 'other');
                        return json_encode([$id]);
                    else:
                        return false;
                    endif;
                }
                
                if ($data['playlist'] == '') {
                    //verifica se o identificador do canal passado é uma URL
                    $urlBaseYoutube = explode('/', $data['identifier']);
                    if ($urlBaseYoutube[0] == 'https:' && $urlBaseYoutube[2] == 'www.youtube.com') {
                        $tipo = $urlBaseYoutube[3];
                        if ($tipo == 'user') {
                            $user = $urlBaseYoutube[4];
                            $youtube = new YoutubeModel($user, $config, true);
                        } elseif ($tipo == 'channel') {
                            $channel = $urlBaseYoutube[4];
                            $youtube = new youtubeModel($channel, $config, false);
                        } else {
                            return false;
                        }
                    } else {
                        //tenta instanciar objeto através do nome do usuário do canal
                        $youtube = new YoutubeModel($data['identifier'], $config, true);
                        //caso falhe, intancia objeto pelo id do canal
                        if (!$youtube) {
                            $youtube = new YoutubeModel($data['identifier'], $config, false);
                            if (!$youtube) {
                                return false;
                            }
                        }
                    } // termina o tratamento de urls
                }else{
                    $youtube = new YoutubeModel('', $config, false, false, true);
                }

                $object_model = new ObjectModel();
                $return = $youtube->insertVideoChannel($data, $object_model);
                if ($return) {
                    return json_encode($return);
                } else {
                    return json_encode([]);
                }
                break;
            //END New Code
            //*********************************************************************************************//
            case "getVideosYoutube":
                // recupera a chave da api cadastrada
                $config = get_option('socialdb_theme_options');
                //verifica se o identificador do canal passado é uma URL
                $urlBaseYoutube = explode('/', $data['identifier']);
                if ($urlBaseYoutube[0] == 'https:' && $urlBaseYoutube[2] == 'www.youtube.com') {
                    $tipo = $urlBaseYoutube[3];
                    if ($tipo == 'user') {
                        $user = $urlBaseYoutube[4];
                        $youtube = new YoutubeModel($user, $config, true);
                    } elseif ($tipo == 'channel') {
                        $channel = $urlBaseYoutube[4];
                        $youtube = new youtubeModel($channel, $config, false);
                    } else {
                        return false;
                    }
                } else {
                    //tenta instanciar objeto através do nome do usuário do canal
                    $youtube = new YoutubeModel($data['identifier'], $config, true);
                    //caso falhe, intancia objeto pelo id do canal
                    if (!$youtube) {
                        $youtube = new YoutubeModel($data['identifier'], $config, false);
                        if (!$youtube) {
                            return false;
                        }
                    }
                } // termina o tratamento de urls

                $object_model = new ObjectModel();
                $data['playlist'] = ($data['playlist'] == __('Get all videos, no playlist specified.', 'tainacan') ? '' : $data['playlist']);
                return $youtube->getAllVideos($data, $object_model);
            case "updateVideosYoutube":
                // recupera a chave da api cadastrada
                $config = get_option('socialdb_theme_options');
                //verifica se o identificador do canal passado é uma URL
                $urlBaseYoutube = explode('/', $data['identifier']);
                if ($urlBaseYoutube[0] == 'https:' && $urlBaseYoutube[2] == 'www.youtube.com') {
                    $tipo = $urlBaseYoutube[3];
                    if ($tipo == 'user') {
                        $user = $urlBaseYoutube[4];
                        $youtube = new YoutubeModel($user, $config, true);
                    } elseif ($tipo == 'channel') {
                        $channel = $urlBaseYoutube[4];
                        $youtube = new youtubeModel($channel, $config, false);
                    } else {
                        return false;
                    }
                } else {
                    //tenta instanciar objeto através do nome do usuário do canal
                    $youtube = new YoutubeModel($data['identifier'], $config, true);
                    //caso falhe, intancia objeto pelo id do canal
                    if (!$youtube) {
                        $youtube = new YoutubeModel($data['identifier'], $config, false);
                        if (!$youtube) {
                            return false;
                        }
                    }
                } // termina o tratamento de url

                $object_model = new ObjectModel();
                $data['playlist'] = ($data['playlist'] == __('Get all videos, no playlist specified.', 'tainacan') ? '' : $data['playlist']);
                return $youtube->updateVideosChannel($data, $object_model);
            case "listIdentifiersYoutube":
                return YoutubeModel::list_channels($data['collectionId']);
                break;

            case "deleteIdentifierYoutube":
                return YoutubeModel::delete_channel($data['identifier'], $data['collection_id']);
            case "editIdentifierYoutube":
                return YoutubeModel::edit_channel($data['identifier'], $data['new_identifier'], $data['new_playlist']);
            case "InsertIdentifierYoutube":
                return YoutubeModel::insert_channel($data['identifier'], $data['playlist'], $data['collectionId']);
            case "list":
                Log::addLog(['collection_id' => $data['collection_id'], 'event_type' => 'collection_admin', 'event' => 'social_media']);
                return $this->render(dirname(__FILE__) . '../../../views/social_network/list.php', $data);
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

$youtube_controller = new YoutubeController();
echo $youtube_controller->operation($operation, $data);
?>