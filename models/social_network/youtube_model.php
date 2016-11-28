<?php

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');

/**
 * @clas-name	YoutubeModel 
 * @description	Listar video(s) e playlist(s) automaticamente de um dado canal do youtube
 * @author     	Saymon de Oliveira Souza (alxsay@hotmail.com)
 * @version    	1.0
 */
//mudar para chave de API do socialDB
//define('API_KEY', 'AIzaSyBhd8JzMmSJkfkcWooIN2QHp8Cgntvz25o');

class YoutubeModel extends Model {

    //nome, descrição, id, data de criação do canal 
    private $chTitle;
    private $description;
    private $chId;
    private $data;
    //estatísticas do canal
    private $numVideos;
    private $numViews;
    private $numSubscribers;
    private $numComments;
    //importante: a id de uploads do canal
    private $idUploads;
    // api secret
    private $apiKey;
    //Array com as info de um vídeo;
    private $arrVideo;

    function __construct($identifier, array $config, $flag = null, $single_video = false, $playlist = false) {

        $this->apiKey = $config['socialdb_youtube_api_id'];

        if ($single_video == false && $playlist == false) {

            $chInfo;

            //flag true, pesquisa por nome de usuário, false, pela id do canal
            if ($flag)
                $chInfo = &$this->getInfoFromCh('https://www.googleapis.com/youtube/v3/channels?part=statistics,snippet,contentDetails&forUsername=%s&key=' . $config['socialdb_youtube_api_id'], $identifier);
            else
                $chInfo = &$this->getInfoFromCh('https://www.googleapis.com/youtube/v3/channels?part=statistics,snippet,contentDetails&id=%s&key=' . $config['socialdb_youtube_api_id'], $identifier);

            // se existir o canal, seta as propriedades
            if ($chInfo != null) {
                $this->chTitle = &$chInfo['title'];
                $this->chId = &$chInfo['chId'];
                $this->description = &$chInfo['description'];
                $this->data = &$chInfo['data'];
                $this->numVideos = &$chInfo['numVideos'];
                $this->numViews = &$chInfo['numViews'];
                $this->numSubscribers = &$chInfo['numSubscribers'];
                $this->numComments = &$chInfo['numComments'];
                $this->idUploads = &$chInfo['idUploads'];
                $this->apiKey = $config['socialdb_youtube_api_id'];
            } else {
                return false;
            }
        } elseif ($single_video == true && $playlist == false) {
            $videoInfo = &$this->getInfoFromVideo("https://www.googleapis.com/youtube/v3/videos?part=snippet&id=%s&key=" . $config['socialdb_youtube_api_id'], $identifier);
            $this->arrVideo = &$videoInfo;
        }
//        elseif($playlist == true){
//            $this->numVideos = 1;
//        }
    }

    //retorna a id do canal
    public function getIdCh() {
        return $this->chId;
    }

    //retorna a id de uploads do canal
    public function getIdUploads() {
        return $this->idUploads;
    }

    //retorna o número de vídeos publicados por um canal
    public function getNumVideos() {
        return $this->numVideos;
    }

    //retorna o título de um canal
    public function getTitle() {
        return $this->chTitle;
    }

    //retorna a descrição de um canal feita por seu autor
    public function getDescription() {
        return $this->description;
    }

    public function insertVideoItem($data, $object_model, $status = 'publish') {
        $collection_id = $data['collectionId'];
        $mapping_id = $this->get_post_by_title('socialdb_channel_youtube', $data['collectionId'], 'youtube');
        $getCurrentIds = unserialize(get_post_meta($mapping_id, 'socialdb_channel_youtube_inserted_ids', true));

        $getCurrentIds = (is_array($getCurrentIds) ? $getCurrentIds : array());

        if (!in_array($this->arrVideo['idvideo'], $getCurrentIds)) {
            $mapping = unserialize(get_post_meta($mapping_id, 'socialdb_channel_youtube_mapping', true));
            if (is_array($mapping)) {
                foreach ($mapping as $mp) {
                    $form[$mp['tag']] = $mp['socialdb_entity'];
                }
            }
            $object_id = socialdb_insert_object_socialnetwork($this->arrVideo['title'], $status);

            //mapping
            add_post_meta($object_id, 'socialdb_channel_id', $mapping_id);
            $categories[] = $this->get_category_root_of($collection_id);
            if ($object_id != 0) {
                $this->add_thumbnail_url($this->arrVideo['thumbnail'], $object_id);
                foreach ($this->arrVideo as $identifier => $metadata) {
                    if ($form[$identifier] !== '') {
                        if ($form[$identifier] == 'post_title'):
                            if (mb_detect_encoding($metadata, 'auto') == 'UTF-8'):
                                $metadata = utf8_decode(iconv('ISO-8859-1', 'UTF-8', $metadata));
                            endif;
                            $this->update_title($object_id, $metadata);
                        elseif ($form[$identifier] == 'post_content'):
                            if (mb_detect_encoding($metadata, 'auto') == 'UTF-8'):
                                $metadata = utf8_decode(iconv('ISO-8859-1', 'UTF-8', $metadata));
                            endif;
                            // $content .= $metadata;
                            $content = $metadata;
                        elseif ($form[$identifier] == 'post_permalink'):
                            update_post_meta($object_id, 'socialdb_object_dc_source', $metadata);
                        elseif ($form[$identifier] == 'socialdb_object_content'):
                            update_post_meta($object_id, 'socialdb_object_content', $metadata);
                        elseif ($form[$identifier] == 'socialdb_object_dc_type'):
                            update_post_meta($object_id, 'socialdb_object_dc_type', $metadata);
                        elseif ($form[$identifier] == 'tag'):
                            $this->insert_tag($metadata, $object_id, $collection_id);
                        elseif (strpos($form[$identifier], "termproperty_") !== false):
                            $trans = array("termproperty_" => "");
                            $property_id = strtr($form[$identifier], $trans);
                            $parent = get_term_meta($property_id, 'socialdb_property_term_root', true);
                            $this->insert_hierarchy($metadata, $object_id, $collection_id, $parent);
                        elseif (strpos($form[$identifier], "dataproperty_") !== false):
                            $trans = array("dataproperty_" => "");
                            $id = strtr($form[$identifier], $trans);
                            add_post_meta($object_id, 'socialdb_property_' . $id . '', $metadata);
                        endif;
                    }
                }
                $metadata = '';
                update_post_meta($object_id, 'socialdb_object_from', 'external');
                add_post_meta($object_id, 'socialdb_object_original_collection', $collection_id);
                update_post_content($object_id, $content);
                socialdb_add_tax_terms($object_id, $categories, 'socialdb_category_type');

                $getCurrentIds[$object_id] = $this->arrVideo['idvideo'];
                update_post_meta($mapping_id, 'socialdb_channel_youtube_inserted_ids', serialize($getCurrentIds));

                $user_id = current_user_id_or_anon();
                $logData = ['collection_id' => $data['collectionId'], 'item_id' => $object_id,
                  'user_id' => $user_id, 'event_type' => 'user_items', 'event' => 'add' ];
                Log::addLog($logData);

                return $object_id;
            }
            else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     *
     * Metodo que atualiza o titulo de um objeto
     *
     * @param int O id do objeto
     * @param string O titutlo desejado para o objeto
     * @return void
     */
    public function update_title($ID, $title) {
        $object = array(
            'ID' => $ID,
            'post_title' => $title
        );
        wp_update_post($object);
    }

    public function insert_hierarchy($metadata, $object_id, $collection_id, $parent = 0) {
        $array = array();
        $categories = explode('::', $metadata);
        foreach ($categories as $category) {
            $array = $this->insert_category($category, $collection_id, $parent);
            $parent = $array['term_id'];
        }
        socialdb_add_tax_terms($object_id, array($array['term_id']), 'socialdb_category_type');
    }

    public function insert_tag($name, $object_id, $collection_id) {
        $parent = get_term_by('name', 'socialdb_tag', 'socialdb_tag_type');
        $array = socialdb_insert_term($name, 'socialdb_tag_type', $parent->term_id, sanitize_title(remove_accent($name)) . "_" . $collection_id);
        socialdb_add_tax_terms($collection_id, array($array['term_id']), 'socialdb_tag_type');
        socialdb_add_tax_terms($object_id, array($array['term_id']), 'socialdb_tag_type');
    }

    public function insert_category($name, $collection_id, $parent_id) {
        $array = socialdb_insert_term($name, 'socialdb_category_type', $parent_id, sanitize_title(remove_accent($name)) . '_' . mktime());
        return $array;
    }

    private function getInfoFromVideo($urlBase, $identifier) {
        $url = sprintf($urlBase, $identifier);
        session_write_close();
        ini_set('max_execution_time', '0');
        $resposta = download_page($url);

        $json = &json_decode($resposta, true);
        if (is_array($json)) {
            $qtd = (int) $json['pageInfo']['totalResults'];
            if ($qtd > 0) {
                $infoFromVideo = array(
                    'title' => &$json['items'][0]['snippet']['title'],
                    'date' => &$json['items'][0]['snippet']['publishedAt'],
                    'description' => &$json['items'][0]['snippet']['description'],
                    'channel' => &$json['items'][0]['snippet']['channelTitle'],
                    'idchannel' => &$json['items'][0]['snippet']['channelId'],
                    'idvideo' => &$json['items'][0]['id'],
                    'url' => 'https://www.youtube.com/watch?v=' . $identifier,
                    'type' => 'video',
                    'content' => 'https://www.youtube.com/watch?v=' . $identifier,
                    'source' => 'Youtube',
                    'thumbnail' => &$json['items'][0]['snippet']['thumbnails']['high']['url']
                );

                return $infoFromVideo;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //retorna informações completas de no máximo 50 videos de um canal
    private function getInfoFromVideoChannel($pageToken = '', $numVideos = 50) {

        $arrayIds = array();

        $urlBase = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=%s&pageToken=%s&playlistId=%s&fields=items/snippet,nextPageToken&key=' . $this->apiKey;
        $idChannelUploads = &$this->idUploads;
        $url = sprintf($urlBase, $numVideos, $pageToken, $idChannelUploads);

        $resposta = file_get_contents($url);
        $json = &json_decode($resposta, true);

        if (is_array($json['items']) && !empty($json['items'])) {
            foreach ($json['items'] as &$ids)
                $arrayIds[] = array(
                    'nextPageToken' => &$json['nextPageToken'],
                    'title' => $ids['snippet']['title'],
                    'date' => $ids['snippet']['publishedAt'],
                    'description' => $ids['snippet']['description'],
                    'channel' => $ids['snippet']['channelTitle'],
                    'idchannel' => $ids['snippet']['channelId'],
                    'idvideo' => $ids['snippet']['resourceId']['videoId'],
                    'url' => 'https://www.youtube.com/watch?v=' . $ids['snippet']['resourceId']['videoId'],
                    'type' => 'video',
                    'content' => 'https://www.youtube.com/watch?v=' . $ids['snippet']['resourceId']['videoId'],
                    'source' => 'Youtube',
                    'thumbnail' => $ids['snippet']['thumbnails']['high']['url']
                );

            return $arrayIds;
        } else {
            return false;
        }
    }

    //trabalhando com playlistis: pega videos de uma playlist específica
    public function getInfoFromVideoPlaylist($idPlaylist = '', $pageToken = '', $numVideos = 50) {
        $arrayIds = array();

        $urlBase = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=%s&pageToken=%s&playlistId=%s&fields=items/snippet,nextPageToken,pageInfo&key=' . $this->apiKey;
        $idChannelUploads = &$this->idUploads;
        $url = sprintf($urlBase, $numVideos, $pageToken, $idPlaylist);

        $resposta = file_get_contents($url);
        $json = &json_decode($resposta, true);

        foreach ($json['items'] as &$ids)
            $arrayIds[] = array(
                'totalVideos' => &$json['pageInfo']['totalResults'],
                'nextPageToken' => &$json['nextPageToken'],
                'title' => $ids['snippet']['title'],
                'date' => $ids['snippet']['publishedAt'],
                'description' => $ids['snippet']['description'],
                'channel' => $ids['snippet']['channelTitle'],
                'idchannel' => $ids['snippet']['channelId'],
                'idvideo' => $ids['snippet']['resourceId']['videoId'],
                'url' => 'https://www.youtube.com/watch?v=' . $ids['snippet']['resourceId']['videoId'],
                'type' => 'video',
                'content' => 'https://www.youtube.com/watch?v=' . $ids['snippet']['resourceId']['videoId'],
                'source' => 'Youtube',
                'thumbnail' => $ids['snippet']['thumbnails']['high']['url']
            );

        return $arrayIds;
    }

    public function getInfoPlaylist($idPlaylist = '', $pageToken = '', $numVideos = 50) {
        $arrayIds = array();

        $urlBase = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=%s&pageToken=%s&playlistId=%s&fields=items/snippet,nextPageToken,pageInfo&key=' . $this->apiKey;
        $url = sprintf($urlBase, $numVideos, $pageToken, $idPlaylist);

        $resposta = file_get_contents($url);
        $json = &json_decode($resposta, true);

        $this->numVideos = $json['pageInfo']['totalResults'];
    }

    /**
     * Importa e insere no banco todos os videos de um dado canal
     */
    public function insertVideoChannel(array $data, ObjectModel $object_model) {
        set_time_limit(0);

        if ($data['playlist'] != '') {
            $this->getInfoPlaylist($data['playlist']);
        }

        $numvideos = (int) $this->getNumVideos();
        if ($numvideos > 0) {
            $iterations = floor($numvideos / 50);
            $token = '';
            $videos;
            $numVideosInserted = 0;

            if ($data['playlist'] == '') {
                $videos = $this->getInfoFromVideoChannel($token);
                if ($videos) {
                    self::insert_channel($data['identifier'], '', $data['collectionId']);
                }
            } else {
                $videos = $this->getInfoFromVideoPlaylist($data['playlist'], $token);

//                if ($this->chId != $videos[0]['idchannel']) {
//                    return false;
//                }

                if ((int) $videos[0]['totalVideos'] > 0) {
                    $numvideos = (int) $videos[0]['totalVideos'];
                } else {
                    return false;
                }
            }

            foreach ($videos as &$video) {
                $this->arrVideo = $video;
                $result = $this->insertVideoItem($data, $object_model, 'draft');
                if ($result) {
                    $numVideosInserted++;
                    $arrSavedIds[] = $result;
                }
            }

            //muda o token de paginação
            $token = $videos[0]['nextPageToken'];
            unset($videos);
            // se houver mais de 50 videos, insere na coleção todos os videos de um dado canal
            if ($numvideos > 50) {
                for ($i = 0; $i < $iterations; ++$i) {

                    if ($data['playlist'] == '') {
                        $videos = $this->getInfoFromVideoChannel($token);
                    } else {
                        $videos = $this->getInfoFromVideoPlaylist($data['playlist'], $token);
                    }

                    // trata no máximo 50 videos por iteração
                    foreach ($videos as $video) {
                        $this->arrVideo = $video;
                        $result = $this->insertVideoItem($data, $object_model, 'draft');
                        if ($result) {
                            $numVideosInserted++;
                            $arrSavedIds[] = $result;
                        }
                    }
                    $token = $video['nextPageToken'];
                    unset($videos);
                }
            }
            if ($numVideosInserted > 0) {
                return $arrSavedIds;
            } else {
                return false;
            }
        } else {
            //se não houver videos publicos no canal
            return false;
        }
    }

    //retorna array com informações de um canal para setar as proprietadas do objeto
    private function getInfoFromCh($urlBase, $identifier) {

        $infoFromCh;

        $url = sprintf($urlBase, $identifier);
        $resposta = file_get_contents($url);

        $json = &json_decode($resposta, true);
        if (is_array($json)) {
            $ch = (int) $json['pageInfo']['totalResults'];
            if ($ch > 0) {

                $infoFromCh = array(
                    'chId' => &$json['items'][0]['id'],
                    'title' => &$json['items'][0]['snippet']['title'],
                    'description' => &$json['items'][0]['snippet']['description'],
                    'data' => &$json['items'][0]['snippet']['publishedAt'],
                    'idUploads' => &$json['items'][0]['contentDetails']['relatedPlaylists']['uploads'],
                    'numViews' => &$json['items'][0]['statistics']['viewCount'],
                    'numComments' => &$json['items'][0]['statistics']['commentCount'],
                    'numSubscribers' => &$json['items'][0]['statistics']['subscriberCount'],
                    'numVideos' => &$json['items'][0]['statistics']['videoCount']
                );

                return $infoFromCh;
            } else {
                return false;
            }
            //return false;
        } else {
            return false;
        }
    }

    //pega os dados estatísticos de um canal
    public function getStatisticsCh() {
        $info;

        $info = array(
            'numVideos' => &$this->numVideos,
            'numViews' => &$this->numViews,
            'numSubscribers' => &$this->numSubscribers,
            'numComments' => &$this->numComments,
        );

        return $info;
    }

    //pega todas as informações de um canal
    public function getAllChInfo() {
        $info;

        $info = array(
            'title' => &$this->chTitle,
            'chId' => &$this->chId,
            'data' => &$this->data,
            'description' => &$this->description,
            'numVideos' => &$this->numVideos,
            'numViews' => &$this->numViews,
            'numSubscribers' => &$this->numSubscribers,
            'numComments' => &$this->numComments,
            'idUploads' => &$this->idUploads
        );

        return $info;
    }

    //pega o pagetoken do próximo conjunto de videos cuja quantidade máxima é 50
    public function getNextPageToken($pageToken = '') {

        $nextPageToken;

        $urlBase = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=%s&pageToken=%s&fields=%s&key=' . $this->apiKey;
        $idChannelUploads = &$this->idUploads;
        $urlToPageToken = sprintf($urlBase, $idChannelUploads, $pageToken, "nextPageToken");
        $resposta = file_get_contents($urlToPageToken);
        $json = &json_decode($resposta, true);

        $nextPageToken = &$json['nextPageToken'];

        return $nextPageToken;
    }

    //se a flag for true, retorna a url completa de no maximo 50 videos; false, retorna apenas as ids
    public function getVideos($flag = true, $pageToken = '', $numVideos = 50) {
        $arrayIds = array();

        $urlBase = 'https://www.googleapis.com/youtube/v3/playlistItems?part=contentDetails&maxResults=%s&pageToken=%s&playlistId=%s&fields=items/contentDetails,nextPageToken&key=' . $this->apiKey;
        $idChannelUploads = &$this->idUploads;
        $url = sprintf($urlBase, $numVideos, $pageToken, $idChannelUploads);

        $resposta = file_get_contents($url);
        $json = &json_decode($resposta, true);

        if ($flag) {
            foreach ($json['items'] as &$ids)
                $arrayIds[] = array(
                    'url' => 'https://www.youtube.com/watch?v=' . $ids['contentDetails']['videoId'],
                    'nextPageToken' => &$json['nextPageToken']
                );
        } else {
            foreach ($json['items'] as &$ids)
                $arrayIds[] = array(
                    'idVideo' => &$ids['contentDetails']['videoId'],
                    'nextPageToken' => &$json['nextPageToken']
                );
        }

        return $arrayIds;
    }

    //retorna informações completas de no máximo 50 videos de um canal
    private function getVideosInfo($pageToken = '', $numVideos = 50) {

        $arrayIds = array();

        $urlBase = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=%s&pageToken=%s&playlistId=%s&fields=items/snippet,nextPageToken&key=' . $this->apiKey;
        $idChannelUploads = &$this->idUploads;
        $url = sprintf($urlBase, $numVideos, $pageToken, $idChannelUploads);

        $resposta = file_get_contents($url);
        $json = &json_decode($resposta, true);

        foreach ($json['items'] as &$ids)
            $arrayIds[] = array(
                'nextPageToken' => &$json['nextPageToken'],
                'date' => $ids['snippet']['publishedAt'],
                'title' => $ids['snippet']['title'],
                'description' => $ids['snippet']['description'],
                'chTitle' => $ids['snippet']['channelTitle'],
                'idChannel' => $ids['snippet']['playlistId'],
                'idVideo' => $ids['snippet']['resourceId']['videoId'],
                'url' => 'https://www.youtube.com/watch?v=' . $ids['snippet']['resourceId']['videoId'],
                'embed' => '<iframe width="200" height="200" src="http://www.youtube.com/embed/' . $ids['snippet']['resourceId']['videoId'] . '" frameborder="0" allowfullscreen></iframe><br>',
                'url' => 'https://www.youtube.com/watch?v=' . $ids['snippet']['resourceId']['videoId'],
                'thumbnail' => 'https://i.ytimg.com/vi/' . $ids['snippet']['resourceId']['videoId'] . '/0.jpg'
            );
        return $arrayIds;
    }

    /**
     * Importa e insere no banco todos os videos de um dado canal
     */
    public function getAllVideos(array $data, ObjectModel $object_model) {
        set_time_limit(0);
        $numvideos = (int) $this->getNumVideos();
        if ($numvideos > 0) {
            $iterations = floor($numvideos / 50);
            $token = '';
            $videos;
            $numVideosInserted = 0;

            if ($data['playlist'] == '') {
                //insere a primeira coleção de 50 videos
                $videos = $this->getVideosInfo($token);
            } else {
                $videos = $this->getVideosFromPlaylist($data['playlist'], $token);

                if ($this->chId != $videos[0]['channelId']) {
                    return false;
                }

                if ((int) $videos[0]['totalVideos'] > 0) {
                    $numvideos = (int) $videos[0]['totalVideos'];
                } else {
                    return false;
                }
            }

            //seta a data do vídeo mais recente
            $dateUpdate = $videos[0]['date'];
            $idUpdate = $videos[0]['idVideo'];
            if (isset($dateUpdate) && isset($idUpdate)) {
                self::setLastDate($data['identifierId'], $dateUpdate);
                self::setLastID($data['identifierId'], $idUpdate);
            }
            // altera o status do identificador do canal como importado
            self::setImportStatus($data['identifierId'], 1);
            foreach ($videos as &$video) {
                $post_id = $object_model->add_photo($data['collection_id'], $video['title'], $video['embed_url'], 'video', $video['url']);
                if ($post_id) {
                    self::setEarlierDate($data['identifierId'], $video['date']);
                    $object_model->add_thumbnail_url($video['thumbnail'], $post_id);
                    add_post_meta($post_id, 'socialdb_uri_imported', $video['thumbnail']);
                    //contador do número de vídeos inseridos no banco
                    $numVideosInserted++;
                }
            }
            //muda o token de paginação
            $token = $videos[0]['nextPageToken'];
            unset($videos);
            // se houver mais de 50 videos, insere na coleção todos os videos de um dado canal
            if ($numvideos > 50) {
                for ($i = 0; $i < $iterations; ++$i) {

                    if ($data['playlist'] == '') {
                        $videos = $this->getVideosInfo($token);
                    } else {
                        $videos = $this->getVideosFromPlaylist($data['playlist'], $token);
                    }

                    // trata no máximo 50 videos por iteração
                    foreach ($videos as $video) {
                        $post_id = $object_model->add_photo($data['collection_id'], $video['title'], $video['embed_url'], 'video', $video['url']);
                        if ($post_id) {
                            self::setEarlierDate($data['identifierId'], $video['date']);
                            $object_model->add_thumbnail_url($video['thumbnail'], $post_id);
                            add_post_meta($post_id, 'socialdb_uri_imported', $video['thumbnail']);
                            //contador do número de vídeos inseridos no banco
                            $numVideosInserted++;
                        }
                    }
                    $token = $video['nextPageToken'];
                    unset($videos);
                }
            }
            if ($numVideosInserted > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            //se não houver videos publicos no canal
            return false;
        }
    }

// fim da método getAllVideos()

    /**
     * @name: getVideosUploads()
     * @description: pega os videos de um canal a partir de uma data
     * @param: $data(string respresentando uma data), $pageToken(string respresentando token de paginação)
     */
    private function getVideosUploads($data, $pageToken = '') {
        $arrayIds = array();
        if (isset($data)) {
            $urlBase = 'https://www.googleapis.com/youtube/v3/search?publishedAfter=%s&order=date&part=snippet&pageToken=%s&fields=items(id,snippet),nextPageToken,pageInfo&channelId=%s&maxResults=50&key=' . $this->apiKey;
            $idChannel = &$this->chId;
            $url = sprintf($urlBase, $data, $pageToken, $idChannel);

            $resposta = file_get_contents($url);
            $json = &json_decode($resposta, true);

            foreach ($json['items'] as &$ids) {
                $arrayIds[] = array(
                    'nextPageToken' => &$json['nextPageToken'],
                    'total' => &$json['pageInfo']['totalResults'],
                    'date' => $ids['snippet']['publishedAt'],
                    'title' => $ids['snippet']['title'],
                    'description' => $ids['snippet']['description'],
                    'idVideo' => $ids['id']['videoId'],
                    'url' => 'https://www.youtube.com/watch?v=' . $ids['id']['videoId'],
                    'embed' => '<iframe width="200" height="200" src="http://www.youtube.com/embed/' . $ids['id']['videoId'] . '" frameborder="0" allowfullscreen></iframe>',
                    'embed_url' => 'https://www.youtube.com/watch?v=' . $ids['id']['videoId'],
                    'thumbnail' => 'https://i.ytimg.com/vi/' . $ids['id']['videoId'] . '/0.jpg'
                );
            }
            return $arrayIds;
        } else {
            return false;
        }
    }

    /**
     * @name: getAllVideosUploaded()
     * @description: pega todos os videos de um canal a partir de uma
     * certa data
     * @param: $data(string respresentando uma data), $pageToken(string respresentando token de paginação)
     */
    public function getAllVideosUploaded(array $data, ObjectModel $object_model) {
        set_time_limit(0);
        $token = '';
        //$dataBase = trim($data['data']);
        $dataBase = trim($data['lastDate']);
        $videos = $this->getVideosUploads($dataBase, $token);

        if (!empty($videos)) {
            $numvideos = (int) $videos[0]['total'];
            $iterations = floor($numvideos / 50);
            $numVideosInserted = 0;
            // atualiza data do video mais recente
            $dateUpdate = $videos[0]['date'];
            if ($dateUpdate != $dataBase) {
                self::setLastDate($data['identifierId'], $dateUpdate);
            }

            // insere a primeira página de resultados (máximo de 50 videos)
            foreach ($videos as $video) {
                if ($video['idVideo'] != $data['lastId']) {
                    if ($video['date'] != $dataBase) {
                        $post_id = $object_model->add_photo($data['collection_id'], $video['title'], $video['embed_url'], 'video', $video['url']);
                        if ($post_id) {
                            self::setLastID($data['identifierId'], $video['idVideo']);
                            $object_model->add_thumbnail_url($video['thumbnail'], $post_id);
                            add_post_meta($post_id, 'socialdb_uri_imported', $video['thumbnail']);
                            //contador do número de vídeos inseridos no banco
                            $numVideosInserted++;
                        }
                    }
                }
            }
            // altera o token de paginação
            $token = $videos[0]['nextPageToken'];
            unset($videos);
            // se houver mais de 50 videos, realiza iterações
            if (!empty($token)) {
                // insere na coleção todos os videos de um dado canal
                for ($i = 0; $i < $iterations; ++$i) {
                    $videos = $this->getVideosUploads($dataBase, $token);
                    // trata no máximo 50 videos por iteração
                    foreach ($videos as $video) {
                        if ($video['idVideo'] != $data['lastId']) {
                            if ($video['date'] != $dataBase) {
                                $post_id = $object_model->add_photo($data['collection_id'], $video['title'], $video['embed_url'], 'video', $video['url']);
                                if ($post_id) {
                                    self::setLastID($data['identifierId'], $video['idVideo']);
                                    $object_model->add_thumbnail_url($video['thumbnail'], $post_id);
                                    add_post_meta($post_id, 'socialdb_uri_imported', $video['thumbnail']);
                                    //contador do número de vídeos inseridos no banco
                                    $numVideosInserted++;
                                }
                            }
                        }
                    }
                    $token = $video['nextPageToken'];
                    unset($videos);
                }
                return ($numVideosInserted > 0) ? true : false;
            }
            return ($numVideosInserted > 0) ? true : false;
        } else {
            return false;
        }
    }

    /**
     * @name: getVideosUploadsEarlier()
     * @description: pega os videos de um canal a partir de uma data
     * @param: $data(string respresentando uma data), $pageToken(string respresentando token de paginação)
     */
    private function getVideosUploadsEarlier($data, $pageToken = '') {
        $arrayIds = array();
        if (isset($data)) {
            $idChannel = &$this->chId;
            $urlBase = 'https://www.googleapis.com/youtube/v3/search?publishedBefore=%s&order=date&part=snippet&pageToken=%s&fields=items(id,snippet),nextPageToken,pageInfo&channelId=%s&maxResults=50&key=' . $this->apiKey;
            $url = sprintf($urlBase, $data, $pageToken, $idChannel);

            $resposta = file_get_contents($url);
            $json = &json_decode($resposta, true);

            foreach ($json['items'] as &$ids) {
                if (!empty($ids['id']['videoId'])) {
                    $arrayIds[] = array(
                        'nextPageToken' => &$json['nextPageToken'],
                        'total' => &$json['pageInfo']['totalResults'],
                        'date' => $ids['snippet']['publishedAt'],
                        'title' => $ids['snippet']['title'],
                        'description' => $ids['snippet']['description'],
                        'idVideo' => $ids['id']['videoId'],
                        'url' => 'https://www.youtube.com/watch?v=' . $ids['id']['videoId'],
                        'embed' => '<iframe width="200" height="200" src="http://www.youtube.com/embed/' . $ids['id']['videoId'] . '" frameborder="0" allowfullscreen></iframe>',
                        'embed_url' => 'https://www.youtube.com/watch?v=' . $ids['id']['videoId'],
                        'thumbnail' => 'https://i.ytimg.com/vi/' . $ids['id']['videoId'] . '/default.jpg'
                    );
                }
            }
            return $arrayIds;
        } else {
            return false;
        }
    }

    /**
     * @name: getAllVideosUploaded()
     * @description: pega todos os videos de um canal a partir de uma
     * certa data
     * @param: $data(string respresentando uma data), $pageToken(string respresentando token de paginação)
     */
    public function getAllVideosUploadsEarlier(array $data, ObjectModel $object_model) {
        set_time_limit(0);
        $token = '';
        // $dataBase = trim($data['data']);
        $dataBase = trim($data['earlierDate']);
        $videos = $this->getVideosUploadsEarlier($dataBase, $token);
        if (!empty($videos)) {
            $numvideos = (int) $videos[0]['total'];
            $iterations = floor($numvideos / 50);
            $numVideosInserted = 0;
            foreach ($videos as $video) {
                if ($video['date'] != $dataBase) {
                    $post_id = $object_model->add_photo($data['collection_id'], $video['title'], $video['embed_url'], 'video', $video['url']);
                    if ($post_id) {
                        self::setEarlierDate($data['identifierId'], $video['date']);
                        $object_model->add_thumbnail_url($video['thumbnail'], $post_id);
                        add_post_meta($post_id, 'socialdb_uri_imported', $video['thumbnail']);
                        //contador do número de vídeos inseridos no banco
                        $numVideosInserted++;
                    }
                }
            }
            // altera o token de paginação
            $token = $videos[0]['nextPageToken'];
            unset($videos);
            // se houver mais de 50 videos, realiza iterações
            if (!empty($token)) {
                // insere na coleção todos os videos de um dado canal
                for ($i = 0; $i < $iterations; ++$i) {
                    $videos = $this->getVideosUploadsEarlier($dataBase, $token);
                    // trata no máximo 50 videos por iteração
                    foreach ($videos as $video) {
                        if ($video['date'] != $dataBase) {
                            $post_id = $object_model->add_photo($data['collection_id'], $video['title'], $video['embed_url'], 'video', $video['url']);
                            if ($post_id) {
                                self::setEarlierDate($data['identifierId'], $video['date']);
                                $object_model->add_thumbnail_url($video['thumbnail'], $post_id);
                                add_post_meta($post_id, 'socialdb_uri_imported', $video['thumbnail']);
                                //contador do número de vídeos inseridos no banco
                                $numVideosInserted++;
                            }
                        }
                    }
                    $token = $video['nextPageToken'];
                    unset($videos);
                }
                return ($numVideosInserted > 0) ? true : false;
            }
            return ($numVideosInserted > 0) ? true : false;
        } else {
            return false;
        }
    }

    /* name - updateVideosChannel()
     * description - atualiza a coleção de videos importadas de uma canal do youtube.
     * importa videos novos ou mais antigos anteriormente não importados.
     * 
     * * */

    public function updateVideosChannel(array $data, ObjectModel $object_model) {
        // tratar a data
        $datas = trim($data['data']);
        $arrayDates = explode('/', $datas);
        $lastDate = $arrayDates[0];
        $earlierDate = $arrayDates[1];

//        $curr_time = date("Y-m-d H:i:s", strtotime($lastDate) + 60);
//        $lastDate = date(DATE_RFC3339, strtotime($curr_time));

        $datas = ['lastDate' => $lastDate, 'earlierDate' => $earlierDate, 'identifierId' => $data['identifierId'], 'collection_id' => $data['collection_id'], 'playlist' => $data['playlist'], 'lastId' => $data['lastId']];
        // pega videos após uma data especificada
        $recentVideos = $this->getAllVideosUploaded($datas, $object_model);
        // pega videos anteriores a uma data especificada
        $ancientVideos = $this->getAllVideosUploadsEarlier($datas, $object_model);

        return ($recentVideos || $ancientVideos) ? true : false;
    }

    //trabalhando com playlistis: pega videos de uma playlist específica
    public function getVideosFromPlaylist($idPlaylist = '', $pageToken = '', $numVideos = 50) {
        $arrayIds = array();

        $urlBase = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=%s&pageToken=%s&playlistId=%s&fields=items/snippet,nextPageToken,pageInfo&key=' . $this->apiKey;
        $idChannelUploads = &$this->idUploads;
        $url = sprintf($urlBase, $numVideos, $pageToken, $idPlaylist);

        $resposta = file_get_contents($url);
        $json = &json_decode($resposta, true);

        foreach ($json['items'] as &$ids)
            $arrayIds[] = array(
                'totalVideos' => &$json['pageInfo']['totalResults'],
                'nextPageToken' => &$json['nextPageToken'],
                'date' => $ids['snippet']['publishedAt'],
                'title' => $ids['snippet']['title'],
                'description' => $ids['snippet']['description'],
                'channelId' => $ids['snippet']['channelId'],
                'chTitle' => $ids['snippet']['channelTitle'],
                'idChannel' => $ids['snippet']['playlistId'],
                'idVideo' => $ids['snippet']['resourceId']['videoId'],
                'url' => 'https://www.youtube.com/watch?v=' . $ids['snippet']['resourceId']['videoId'],
                'embed' => '<iframe width="200" height="200" src="http://www.youtube.com/embed/' . $ids['snippet']['resourceId']['videoId'] . '" frameborder="0" allowfullscreen></iframe><br>',
                'embed_url' => 'https://www.youtube.com/watch?v=' . $ids['snippet']['resourceId']['videoId'],
                'thumbnail' => 'https://i.ytimg.com/vi/' . $ids['snippet']['resourceId']['videoId'] . '/0.jpg'
            );

        return $arrayIds;
    }

    /**
     * @description - function insert_channel($identifier)
     * $identifier é o identificador do canal
     * Insere um identificador de canal no banco
     * 
     * @autor: Saymon 
     */
    static function insert_channel($identifier, $playlist, $colectionId) {
        $validation = self::verify_channel($identifier, $colectionId);
        if ($validation == false) {
            $postId = wp_insert_post(['post_title' => $identifier, 'post_status' => 'publish', 'post_type' => 'socialdb_channel']);
            if ($postId) {
                add_post_meta($postId, 'socialdb_channel_identificator', $colectionId);
//            add_post_meta($postId, 'socialdb_channel_playlist_identificator', $playlist);
                add_post_meta($postId, 'socialdb_channel_youtube_last_update', date("Y-m-d H:i:s"));
                add_post_meta($postId, 'socialdb_channel_youtube_harvesting', 'disabled');
//            add_post_meta($postId, 'socialdb_channel_youtube_earlier_update', '');
//            add_post_meta($postId, 'socialdb_channel_youtube_import_status', 0);
                return true;
            } else {
                return false;
            }
        } else {
            update_post_meta($validation, 'socialdb_channel_youtube_last_update', date("Y-m-d H:i:s"));
        }
    }

    static function verify_channel($identifier, $collectionId) {
        //array de configuração dos parâmetros de get_posts()
        $args = array(
            'meta_key' => 'socialdb_channel_identificator',
            'meta_value' => $collectionId,
            'post_type' => 'socialdb_channel',
            'post_status' => 'publish',
            'suppress_filters' => true
        );
        $results = get_posts($args);
        $retorno = false;
        if (is_array($results)) {
            foreach ($results as $ch) {
                if (!empty($ch)) {
                    if ($ch->post_title == $identifier) {
                        $retorno = $ch->ID;
                    }
                }
            }
        }
        return $retorno;
    }

    static function edit_channel($identifier, $newIdentifier, $newPlaylist) {
        if (!empty($newIdentifier)) {
            $my_post = array(
                'ID' => $identifier,
                'post_title' => $newIdentifier,
            );
            $postEdted = wp_update_post($my_post);

            update_post_meta($identifier, 'socialdb_channel_playlist_identificator', $newPlaylist);
            return ($postEdted) ? true : false;
        } else {
            return false;
        }
    }

    static function delete_channel($identifierId, $colectionId) {
        $deletedIdentifier = wp_delete_post($identifierId);
        if ($deletedIdentifier) {
            delete_post_meta($identifierId, 'socialdb_channel_identificator', $identifier);
            delete_post_meta($identifierId, 'socialdb_channel_identificator', $colectionId);
            return true;
        } else {

            return false;
        }
    }

    static function list_channels($collectionId) {
        //array de configuração dos parâmetros de get_posts()
        $args = array(
            'meta_key' => 'socialdb_channel_identificator',
            'meta_value' => $collectionId,
            'post_type' => 'socialdb_channel',
            'post_status' => 'publish',
            'suppress_filters' => true
        );
        $results = get_posts($args);
        if (is_array($results)) {
            $json = [];
            foreach ($results as $ch) {
                if (!empty($ch)) {
                    $postMetaLastUpdate = get_post_meta($ch->ID, 'socialdb_channel_youtube_last_update');
//                    $postMetaEarlierUpdate = get_post_meta($ch->ID, 'socialdb_channel_youtube_earlier_update');
//                    $postMetaImportSatus = get_post_meta($ch->ID, 'socialdb_channel_youtube_import_status');
//                    $postMetaPlaylist = get_post_meta($ch->ID, 'socialdb_channel_playlist_identificator');
//                    $postMetaLastIdUpdate = get_post_meta($ch->ID, 'socialdb_channel_youtube_last_id_update');
                    $array = array('name' => $ch->post_title, 'id' => $ch->ID, 'lastUpdate' => $postMetaLastUpdate, 'earlierUpdate' => $postMetaEarlierUpdate, 'importStatus' => $postMetaImportSatus, 'playlist' => $postMetaPlaylist, 'lastId' => $postMetaLastIdUpdate);
                    $json['identifier'][] = $array;
                }
            }
            echo json_encode($json);
        } else {
            return false;
        }
    }

    private static function setLastDate($post_id, $date) {
        update_post_meta($post_id, 'socialdb_channel_youtube_last_update', $date);
    }

    private static function setLastID($post_id, $last_id) {
        update_post_meta($post_id, 'socialdb_channel_youtube_last_id_update', $last_id);
    }

    private static function setEarlierDate($post_id, $date) {
        update_post_meta($post_id, 'socialdb_channel_youtube_earlier_update', $date);
    }

    private static function setImportStatus($post_id, $date) {
        update_post_meta($post_id, 'socialdb_channel_youtube_import_status', $date);
    }

}

?>