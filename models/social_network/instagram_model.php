<?php

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');

class InstagramModel extends Model {

    /**
     * constantes contendo as chaves da api
     * e as urls bases para login, autenticação e requisição
     */
    const API_URL = 'https://api.instagram.com/v1/';
    const API_OAUTH_URL = 'https://api.instagram.com/oauth/authorize/';
    const API_OAUTH_TOKEN_URL = 'https://api.instagram.com/oauth/access_token';
    const CONTLROLLER_PATH = '/controllers/social_network/instagram_controller.php';

    /**
     * armazena a id de usuário. Necessário para requisitar dados
     */
    private $userId;

    /**
     * armazena configurações da API
     */
    private $clientId;
    private $clientSecret;

    /**
     * armazena info de um item
     */
    private $arrItem;

    /**
     * @__construct 
     * @description: constutor da classe seta a id do usuário
     * @param: $username - nome de usuário válido do instagram
     */
    function __construct($username, array $config) {
        $this->clientId = $config['socialdb_instagram_api_id'];
        $this->clientSecret = $config['socialdb_instagram_api_secret'];
        if ($username) {
            $requestUserId = self::API_URL . 'users/search?q=' . $username . '&count=1&client_id=' . $this->clientId;
            $result = file_get_contents($requestUserId);
            $jsonResponse = json_decode($result, true);
            $this->userId = $jsonResponse['data'][0]['id'];
        }
    }

    /**
     * @name: getUserId 
     * @description: retorna a id do usuário associado a um nome de usuário válido
     * instagram
     * @return:string
     */
    private function getUserId() {
        return $this->userId;
    }

    /**
     * @name: getUriRedirect() 
     * @description: retorna a uri de redirecionamento(url corrente)
     * instagram
     * @return:string
     */
    private function getUriRedirect() {
        return $this->uriRedirect;
    }

    /**
     * @name: requestInstagramApi 
     * @description: faz uma requisição autenticado 
     * @return: string
     */
    static function requestInstagramApi($url) {
        $response = file_get_contents($url);
        return $response;
    }

    /**
     * @name: loginInstagram 
     * @description: faz o login de um usuário 
     * @return: array
     */
    public function loginInstagram() {
        $urlLogin = self::API_OAUTH_URL . '?client_id=' . $this->clientId;
        $urlLogin .= '&redirect_uri=' . get_bloginfo('template_directory') . self::CONTLROLLER_PATH;
        $urlLogin .= '&response_type=code';
        header('location: ' . $urlLogin);
    }

// get_bloginfo('template_directory')

    /**
     * @name: getAccessToken 
     * @description: autentica um usuário logado
     * @param: $code, string enviada via URL pela API do instagram 
     * como resposta a uma solicitação de autenticação 
     * @return: array
     */
    private function getAccessToken($code) {
        $paramConfig = array(
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'authorization_code',
            'redirect_uri' => get_bloginfo('template_directory') . self::CONTLROLLER_PATH,
            'code' => $code
        );

        $ch = curl_init(self::API_OAUTH_TOKEN_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramConfig);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $jsonData = curl_exec($ch);
        curl_close($ch);
        $arrayJson = json_decode($jsonData, true);
        return $arrayJson['access_token']; //['access_token'];
    }

    /**
     * @name: getUserMediaRecent 
     * @description: recupera as imagens de um dado usuário
     * @param: $param, string; $flag, boolean, se true $param é um access_token,
     * false, indica que se trata de uma url completa para paginação de pedidos
     * da API do instagram
     * @return: array contendo dados de 20 imagens mais a url para
     * próxima página de resultados
     */
    private function getUserMediaRecent($param, $flag = true) {
        if ($flag) {
            $user_id = $this->getUserId();
            $url = self::API_URL . 'users/' . $user_id . '/media/recent';
            $url .= '?access_token=' . $param;
            $response = self::requestInstagramApi($url);
        } else {
            //flag falsa faz a requisição através da url da resposta seguinte
            $response = self::requestInstagramApi($param);
        }

        $jsonResponse = json_decode($response, true);

        foreach ($jsonResponse['data'] as $media) {
            if ($media['type'] == 'image') {
                $content_url = $media['images']['standard_resolution']['url'];
            } elseif ($media['type'] == 'video') {
                $content_url = $media['videos']['standard_resolution']['url'];
            }

            $users_in_photo = '';

            if (is_array($media['users_in_photo'])) {
                foreach ($media['users_in_photo'] as $user_in_photo) {
                    $users_in_photo[] = $user_in_photo['user']['username'];
                }
                if (is_array($users_in_photo)) {
                    $users_in_photo = implode(', ', $users_in_photo);
                }
            }
            
            $thumbnail_url = strpos($media['images']['standard_resolution']['url'], '?');
            
            if($thumbnail_url){
                $thumbnail_url = substr($media['images']['standard_resolution']['url'], 0, $thumbnail_url);
//                var_dump($thumbnail_url);
//                exit();
            }
//            
//            echo "<pre>";
//            var_dump($media);
//            echo "</pre>";
//            exit();

            $jsonResponse['resultdata'][] = array(
                'id' => $media['id'],
                'username' => $media['user']['username'],
                'tags' => implode(', ', $media['tags']),
                'caption' => $media['caption']['text'],
                'userid' => $media['user']['id'],
                'users_in_photo' => $users_in_photo,
                'created_time' => $media['created_time'],
                'type' => $media['type'],
                'link' => $media['link'],
                'location' => $media['location']['name'],
                'content' => $content_url,
                'source' => 'Instagram',
                'thumbnail' => $thumbnail_url
                //'thumbnail' => $media['images']['thumbnail']['url']
            );
        }

        return $jsonResponse;
    }

    //fim do método getUserMediaRecent()

    /**
     * @name: getUserMediaRecentUpdate 
     * @description: recupera as imagens de um dado usuário para update apartir do ultimo item
     * @param: $param, string; $lastId, string; $flag, boolean, se true $param é um access_token,
     * false, indica que se trata de uma url completa para paginação de pedidos
     * da API do instagram
     * @return: array contendo dados de 20 imagens mais a url para
     * próxima página de resultados
     */
    private function getUserMediaRecentUpdate($param, $lastDate, $flag = true) {
        $curr_time = date("Y-m-d H:i:s", strtotime($lastDate) + 60);

        $lastDate = strtotime($curr_time);

        if ($flag) {
            $user_id = $this->getUserId();
            $url = self::API_URL . 'users/' . $user_id . '/media/recent';
            $url .= '?access_token=' . $param;
            $url .= '&min_timestamp=' . $lastDate;
            $response = self::requestInstagramApi($url);
        } else {
            //flag falsa faz a requisição através da url da resposta seguinte
            $response = self::requestInstagramApi($param);
        }

        $jsonResponse = json_decode($response, true);

        return $jsonResponse;
    }

    public function insertInstagramItem($data, $object_model, $status = 'publish') {
        $collection_id = $data['collection_id'];
        $mapping_id = $this->get_post_by_title('socialdb_channel_instagram', $collection_id, 'instagram');

        $getCurrentIds = unserialize(get_post_meta($mapping_id, 'socialdb_channel_instagram_inserted_ids', true));

        $getCurrentIds = (is_array($getCurrentIds) ? $getCurrentIds : array());

        if (!in_array($this->arrItem['id'], $getCurrentIds)) {
            $mapping = unserialize(get_post_meta($mapping_id, 'socialdb_channel_instagram_mapping', true));

            foreach ($mapping as $mp) {
                $form[$mp['tag']] = $mp['socialdb_entity'];
            }

            $object_id = socialdb_insert_object_socialnetwork($this->arrItem['caption'], $status);

            //mapping
            add_post_meta($object_id, 'socialdb_channel_id', $mapping_id);
            $categories[] = $this->get_category_root_of($collection_id);
            if ($object_id != 0) {
                $this->add_thumbnail_url($this->arrItem['thumbnail'], $object_id);
                foreach ($this->arrItem as $identifier => $metadata) {
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
                            $content .= $metadata;
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

                $getCurrentIds[$object_id] = $this->arrItem['id'];
                update_post_meta($mapping_id, 'socialdb_channel_instagram_inserted_ids', serialize($getCurrentIds));

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
    
    public function insert_hierarchy($metadata,$object_id,$collection_id,$parent = 0) {
        $array = array();
        $categories = explode('::', $metadata);
        foreach ($categories as $category) {
            $array = $this->insert_category($category,$collection_id,$parent);
            $parent = $array['term_id'];
        }
        socialdb_add_tax_terms($object_id, array($array['term_id']), 'socialdb_category_type');
    }
    
    public function insert_tag($name,$object_id,$collection_id) {
        $parent = get_term_by('name','socialdb_tag','socialdb_tag_type');
        $array = socialdb_insert_term($name, 'socialdb_tag_type', $parent->term_id, sanitize_title(remove_accent($name)) . "_" . $collection_id);
        socialdb_add_tax_terms($collection_id, array($array['term_id']), 'socialdb_tag_type');
        socialdb_add_tax_terms($object_id, array($array['term_id']), 'socialdb_tag_type');
    }
    
    public function insert_category($name,$collection_id,$parent_id) {
       $array = socialdb_insert_term($name, 'socialdb_category_type', $parent_id, sanitize_title(remove_accent($name)).'_'.  mktime());
        return $array;
    }

    //fim do método getUserMediaRecentUpdate()

    /**
     * @name: getAllUserMediaRecent 
     */
    public function getAllUserMediaRecent(array $param, ObjectModel $object_model) {
        set_time_limit(0);
        $access_token = $this->getAccessToken($param['code']);
        // itens inclusos no banco
        $numInsertedItems = 0;

        if (isset($access_token)) {
            //requisita as midias mais recentes postadas pelo usuário intanciado
            $response = $this->getUserMediaRecent($access_token);

            foreach ($response['resultdata'] as &$media) {
                $this->arrItem = $media;
                $result = $this->insertInstagramItem($param, $object_model, 'draft');
                if ($result) {
                    $numInsertedItems++;
                    $arrSavedIds[] = $result;
                }
            }

            $nextUrl;

            // se houver mais de 20 imagens, realiza iterações
            do {
                if (isset($response['pagination']['next_url'])) {
                    $nextUrl = $response['pagination']['next_url'];
                    $response = $this->getUserMediaRecent($nextUrl, false);
                    foreach ($response['resultdata'] as &$media) {
                        $this->arrItem = $media;
                        $result = $this->insertInstagramItem($param, $object_model, 'draft');
                        if ($result) {
                            $numInsertedItems++;
                            $arrSavedIds[] = $result;
                        }
                    }
                } else {
                    unset($nextUrl);
                }
            } while (isset($nextUrl));

            if ($numInsertedItems > 0) {
                return $arrSavedIds;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @name: getAllUserMediaRecent 
     */
    public function getAllUserMediaRecent_old(array $param, ObjectModel $object_model) {
        set_time_limit(0);
        $access_token = $this->getAccessToken($param['code']);
        if (isset($access_token)) {
            //requisita as midias mais recentes postadas pelo usuário intanciado
            if (isset($param['real_op']) && $param['real_op'] == 'updatePhotosInstagram' && isset($param['lastDate'])) {
                $response = $this->getUserMediaRecentUpdate($access_token, $param['lastDate']);
            } else {
                $response = $this->getUserMediaRecent($access_token);
            }

            //seta a data do post (imagem ou video) mais recente
            $dateUpdate = $response['data'][0]['created_time'];
            $lastId = $response['data'][0]['id'];
            if (isset($dateUpdate) && isset($lastId)) {
                self::setLastDateInstagram($param['postIdUserInstagram'], $dateUpdate);
                self::setLastIdInstagram($param['postIdUserInstagram'], $lastId);
                // altera o status do identificador do canal como importado
                self::setImportStatus($param['postIdUserInstagram'], 1);
            } else {
                return false;
            }

            foreach ($response['data'] as &$media) {
                if ($media['type'] == 'image') {
                    $media_content = '<img src="' . $media['images']['standard_resolution']['url'] . '" width="200" height="200" />';
                    $media_content = $media['images']['standard_resolution']['url'];
                } elseif ($media['type'] == 'video') {
                    $media_content = '<iframe src="' . $media['videos']['standard_resolution']['url'] . '" width="200" height="200" frameborder="0" scrolling="no" allowfullscreen />';
                    $media_content = $media['videos']['standard_resolution']['url'];
                }

                //$post_id = $object_model->add_photo($param['collection_id'], 'inserir um título', $media_content);
                $post_id = $object_model->add_photo($param['collection_id'], 'inserir um título', $media_content);
                if ($post_id) {
                    $object_model->add_thumbnail_url($media['images']['standard_resolution']['url'], $post_id);
                    add_post_meta($post_id, 'socialdb_uri_imported', $media['images']['standard_resolution']['url']);
                }
            }

            $nextUrl;

            // se houver mais de 20 imagens, realiza iterações
            do {
                if (isset($response['pagination']['next_url'])) {
                    $nextUrl = $response['pagination']['next_url'];
                    $response = $this->getUserMediaRecent($nextUrl, false);
                    foreach ($response['data'] as &$media) {
                        //$post_id = $object_model->add_photo($param['collection_id'], 'inserir um título', '<embed width=400 height=350 src=\'' . $media['images']['standard_resolution']['url'] . '\'>');
                        $post_id = $object_model->add_photo($param['collection_id'], 'inserir um título', $media['images']['standard_resolution']['url']);
                        if ($post_id) {
                            $object_model->add_thumbnail_url($media['images']['thumbnail']['url'], $post_id);
                            add_post_meta($post_id, 'socialdb_uri_imported', $media['images']['thumbnail']['url']);
                        }
                    }
                } else {
                    unset($nextUrl);
                }
            } while (isset($nextUrl));

            return true;
        } else {
            return false;
        }
    }

//fim do método getAllUserMediaRecent()

    /**
     * @description - function insert_instagram_identifier($identifier)
     * $identifier é o nome do usuário do perfil flickr 
     * Insere um identificador de canal no banco
     * 
     * @autor: Saymon 
     */
    public static function insert_instagram_identifier($identifier, $colectionId) {
        $postId = wp_insert_post(['post_title' => $identifier, 'post_status' => 'publish', 'post_type' => 'socialdb_channel']);
        if ($postId) {
            add_post_meta($postId, 'socialdb_instagram_identificator', $colectionId);
            add_post_meta($postId, 'socialdb_instagram_identificator_last_update', '');
            add_post_meta($postId, 'socialdb_instagram_import_status', 0);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @description - function edit_vimeo_identifier($identifier)
     * $identifier -  o nome do usuário do perfil instagram 
     * $newIdentifier - novo valor  
     * altera um identificador de um dado perfil instagram
     * 
     * @autor: Saymon 
     */
    public static function edit_instagram_identifier($identifier, $newIdentifier) {
        if (!empty($newIdentifier)) {
            $my_post = array(
                'ID' => $identifier,
                'post_title' => $newIdentifier,
            );
            $postEdted = wp_update_post($my_post);
            return ($postEdted) ? true : false;
        } else {
            return false;
        }
    }

    /**
     * @name : delete_instagram_identifier()
     * @description : exclui um identificador de um dado perfil instagram
     * @param: identifier, $colectionId
     * $identifier -  o nome do usuário do perfil instagram 
     * $colectionId - coleção a que o identificador pertence  
     * $return - boolean (confimando a exlusão)
     * @autor: Saymon 
     */
    public static function delete_instagram_identifier($identifierId, $colectionId) {
        $deletedIdentifier = wp_delete_post($identifierId);
        if ($deletedIdentifier) {
            delete_post_meta($identifierId, 'socialdb_instagram_identificator', $identifier);
            delete_post_meta($identifierId, 'socialdb_instagram_identificator', $colectionId);
            return true;
        } else {
            return false;
        }
    }

    public static function list_instagram_identifier($collectionId) {
        //array de configuração dos parâmetros de get_posts()
        $args = array(
            'meta_key' => 'socialdb_instagram_identificator',
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
                    $postMetaLastUpdate = get_post_meta($ch->ID, 'socialdb_instagram_identificator_last_update', true);
                    $postMetaLastUpdate = ($postMetaLastUpdate == '' ? '' : date("Y-m-d H:i:s", $postMetaLastUpdate));

                    $postMetaLastIdUpdate = get_post_meta($ch->ID, 'socialdb_instagram_identificator_last_update_id');
                    $postMetaImportStatus = get_post_meta($ch->ID, 'socialdb_instagram_import_status');
                    $array = array('name' => $ch->post_title, 'id' => $ch->ID, 'lastUpdate' => $postMetaLastUpdate, 'importStatus' => $postMetaImportStatus, 'lastId' => $postMetaLastIdUpdate);
                    $json['identifier'][] = $array;
                }
            }
            echo json_encode($json);
        } else {
            return false;
        }
    }

    private static function setLastDateInstagram($post_id, $date) {
        update_post_meta($post_id, 'socialdb_instagram_identificator_last_update', $date);
    }

    private static function setLastIdInstagram($post_id, $last_id) {
        update_post_meta($post_id, 'socialdb_instagram_identificator_last_update_id', $last_id);
    }

    private static function setImportStatus($post_id, $status) {
        update_post_meta($post_id, 'socialdb_instagram_import_status', $status);
    }

}

?>