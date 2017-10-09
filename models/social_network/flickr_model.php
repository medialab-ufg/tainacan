<?php

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../object/object_save_values.php');

class FlickrModel extends Model {

    /**
     * @clas-name	FlickrModel 
     * @description	Listar fotos públicas automaticamente de um dado usuário do flickr
     * @author     	Saymon de Oliveira Souza (alxsay@hotmail.com)
     * @version    	1.0
     */
    const endPoint = 'https://api.flickr.com/services/rest/?';
    const method = 'method=flickr.';
    const apiKey = '&api_key=';
    const perPage = '&per_page=500';
    const format = '&format=json&nojsoncallback=1';

    private $userId;
    private $userName;
    private $apiKeyValue;
    private $arrItem;
    private $albumId;

    //apenas um nome válido de usuário é necessário para instanciar a classe
    function __construct($uName, array $config) {
        //necessário tratar os espaços em branco
        $this->userName = str_replace(' ', '+', $uName);
        $this->apiKeyValue = $config['socialdb_flickr_api_id'];
        //$this->userId = $this->peopleFindByUsername()['user']['nsid'];
        $this->userId = $uName;
        if ($this->userId) {
            return true;
        } else {
            return false;
        }
    }

    /* @name: callFlickrAPI()
     * @description: método privado para fazer qualquer requisição para a API do flickr
     * @arfs: $req é uma string contendo a url de uma requisição REST
     * 
     * @return: a resposta da API do formato de uma array do php
     * */

    private function callFlickrAPI($req) {
        $response = file_get_contents($req);
        $jsonResponse = json_decode($response, true);
        return $jsonResponse;
    }

    /* @name: peopleFindByUsername()
     * @description: método público para recuperar a nsid do usuário
     * @return: a nsid de um dado usuário flickr necessário para realizar outras operações
     * como, por exemplo, listar as fotos públicas deste usuário
     * */

    private function peopleFindByUsername() {
        $request = self::endPoint . self::method . 'people.findByUsername' . self::apiKey . $this->apiKeyValue . '&username=' . $this->userName . self::format;
        return $this->callFlickrAPI($request);
    }

    /* @name: peopleGetPublicPhotos()
     * @description: método público para recuperar no máximo 500 fotos do usuário
     * @return: array php contendo os dados de no máximo 500 fotos de um usuário
     * */

    private function peopleGetPublicPhotos($page = 1) {
        $numPage = $page;
        $request = self::endPoint . self::method . 'people.getPublicPhotos' . self::apiKey . $this->apiKeyValue . '&user_id=' . $this->userId . self::perPage . '&page=' . $numPage . self::format;
        $reponse = $this->callFlickrAPI($request);
        $arrayResponse = array(array('pages' => $reponse['photos']['pages'], 'total' => $reponse['photos']['total']));
        foreach ($reponse['photos']['photo'] as &$photo) {
            $arrayResponse[] = array(
                'title' => $photo['title'],
                'url' => 'https://farm' . $photo['farm'] . '.staticflickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'] . '_b.jpg',
                //'date' => $photo['dateupload'],
                'embed' => '<embed width="200" height="200" src="' . 'https://farm' . $photo['farm'] . '.staticflickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'] . '_b.jpg" frameborder="0" allowfullscreen></iframe>',
                'thumbnail' => 'https://farm' . $photo['farm'] . '.staticflickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'] . '_b.jpg'
            );
        }
        return $arrayResponse;
    }

    /* @name: peopleGetAllPublicPhotos()
     * @description: método público para todas as fotos públicas de um usuário
     * @return: array php contendo os dados de no máximo 500 fotos de um usuário
     * 
     */

    public function peopleGetAllPublicPhotos(array $data, ObjectModel $object_model) {
        $firstRequest = $this->peopleGetPublicPhotos();
        $numPages = (int) $firstRequest[0]['pages'];
        $totalPhotos = (int) $firstRequest[0]['total'];
        if (($numPages >= 1) && ($totalPhotos >= 1)) {
            set_time_limit(0);
            // numero de fotos inseridas no banco
            $numRecortedPhotos = 0;
            //insere data de importação
            $dateUpdate = date('d/m/y');
            FlickrModel::setLastDateFlickr($data['identifierId'], $dateUpdate);
            FlickrModel::setImportStatus($data['identifierId'], 1);
            foreach ($firstRequest as &$fPhoto) {
                $post_id = $object_model->add_photo($data['collection_id'], $fPhoto['title'], $fPhoto['embed']);
                if ($post_id) {
                    $object_model->add_thumbnail_url($fPhoto['thumbnail'], $post_id);
                    add_post_meta($post_id, 'socialdb_uri_imported', $fPhoto['thumbnail']);
                    $numRecortedPhotos++;
                }
            }
            unset($firstRequest);
            if ($numPages > 1) {
                $page = 2;
                for ($i = 2; $i <= $numPages; $i++) {
                    $photos = &$this->peopleGetPublicPhotos($page);
                    foreach ($photos as &$photo) {
                        $post_id = $object_model->add_photo($data['collection_id'], $photo['title'], $photo['embed']);
                        if ($post_id) {
                            $object_model->add_thumbnail_url($photo['thumbnail'], $post_id);
                            add_post_meta($post_id, 'socialdb_uri_imported', $photo['thumbnail']);
                            $numRecortedPhotos++;
                        }
                    }
                    unset($photos);
                    $page++;
                }
            }
            return ($numRecortedPhotos > 0) ? true : false;
        } else {
            return false;
        }
    }

    /* @name: getPhotosFromUser()
     * @description: método público para recuperar no máximo 500 fotos do usuário
     * @return: array php contendo os dados de no máximo 500 fotos de um usuário
     * url de requisição - https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=ac2e2ea27cd77719248aab0439ea2bab&user_id=45950435%40N05&sort=date-posted-asc&content_type=1&extras=views,media,date_upload,license,date_taken&per_page=500&page=1&format=json
     */

    private function getPhotosFromUser($page) {
        $request = self::endPoint . self::method . 'photos.search' . self::apiKey . $this->apiKeyValue . '&user_id=' . $this->userId . '&sort=date-posted-asc&content_type=1&extras=views,date_upload,license,date_taken' . self::perPage . '&page=' . $page . self::format;
        $reponse = $this->callFlickrAPI($request);
        $arrayResponse = array(array('pages' => $reponse['photos']['pages'], 'total' => $reponse['photos']['total']));
        foreach ($reponse['photos']['photo'] as &$photo) {
            $arrayResponse[] = array(
                'title' => $photo['title'],
                'url' => 'https://farm' . $photo['farm'] . '.staticflickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'] . '_b.jpg',
                'date' => $photo['dateupload'],
                'datetaken' => $photo['datetaken'],
                'views' => $photo['views'],
                'embed' => 'https://farm' . $photo['farm'] . '.staticflickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'] . '_b.jpg',
                'embed_html' => '<embed width="200" height="200" src="' . 'https://farm' . $photo['farm'] . '.staticflickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'] . '_b.jpg" frameborder="0" allowfullscreen></iframe>',
                'thumbnail' => 'https://farm' . $photo['farm'] . '.staticflickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'] . '_b.jpg'
            );
        }
        return $arrayResponse;
    }

    /* @name: getPhotosFromUser()
     * @description: método público para recuperar no máximo 500 fotos do usuário
     * @return: array php contendo os dados de no máximo 500 fotos de um usuário
     * url de requisição - https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=ac2e2ea27cd77719248aab0439ea2bab&user_id=45950435%40N05&sort=date-posted-asc&content_type=1&extras=views,media,date_upload,license,date_taken&per_page=500&page=1&format=json
     */

    public function getAllPhotosFromUser(array $data, ObjectModel $object_model) {
        // índice da primeira página de resposta
        $currentPage = 1;
        // photos inclusas no banco
        $numRecortedPhotos = 0;
        // primeira requisição
        $response = $this->getPhotosFromUser($currentPage);
        // número de photos na requisição
        $numPhotos = count($response) - 1;
        // paginação
        $numPages = $response[0]['pages'];

        if (!empty($response)) {
            //altera status da importação
            self::setImportStatus($data['identifierId'], 1);
            // trata a primeira resposta: máximo de 500 photos
            for ($i = 1; $i <= $numPhotos; $i++) {
                $post_id = $object_model->add_photo($data['collection_id'], $response[$i]['title'], $response[$i]['embed']);
                if ($post_id) {
                    self::setLastDateFlickr($data['identifierId'], $response[$i]['date']);
                    $object_model->add_thumbnail_url($response[$i]['thumbnail'], $post_id);
                    add_post_meta($post_id, 'socialdb_uri_imported', $response[$i]['thumbnail']);
                    $numRecortedPhotos++;
                }
            }
            unset($response);
            // verifica se há mais de 500 photos
            if ($numPages > 1) {
                // esse loop só se repetirá caso exista mais de 1000 photos
                for ($i = 1; $i < $numPages; $i++) {
                    $currentPage++;
                    $response = $this->getPhotosFromUser($currentPage);
                    $numPhotos = count($response) - 1;
                    // inserindo as photos no banco
                    for ($i = 1; $i <= $numPhotos; $i++) {
                        $post_id = $object_model->add_photo($data['collection_id'], $response[$i]['title'], $response[$i]['embed']);
                        if ($post_id) {
                            self::setLastDateFlickr($data['identifierId'], $response[$i]['date']);
                            $object_model->add_thumbnail_url($response[$i]['thumbnail'], $post_id);
                            add_post_meta($post_id, 'socialdb_uri_imported', $response[$i]['thumbnail']);
                            $numRecortedPhotos++;
                        };
                    }
                    unset($response);
                }
                return ($numRecortedPhotos > 0) ? true : false;
            }
        }
        return ($numRecortedPhotos > 0) ? true : false;
    }

    private function getInfoFromItem($page) {
        //$request = self::endPoint . self::method . 'photos.search' . self::apiKey . $this->apiKeyValue . '&user_id=' . $this->userId . '&sort=date-posted-asc&content_type=1&extras=views,date_upload,license,date_taken' . self::perPage . '&page=' . $page . self::format;
        $request = self::endPoint . self::method . 'photos.search' . self::apiKey . $this->apiKeyValue . '&user_id=' . $this->userId . '&sort=date-posted-asc&content_type=1&extras=description,license,date_upload,date_taken,owner_name,icon_server,original_format,last_update,geo,tags,machine_tags,o_dims,views,media,path_alias,url_sq,url_t,url_s,url_q,url_m,url_n,url_z,url_c,url_l,url_o' . self::perPage . '&page=' . $page . self::format;
        $reponse = $this->callFlickrAPI($request);
        $arrayResponse = array(array('pages' => $reponse['photos']['pages'], 'total' => $reponse['photos']['total']));
        if (is_array($reponse['photos']['photo']) && !empty($reponse['photos']['photo'])) {
            foreach ($reponse['photos']['photo'] as &$photo) {
                if ($photo['media'] == 'photo') {
                    $imageUrl = (isset($photo['url_l']) ? $photo['url_l'] : $photo['url_m']);
                    $arrayResponse[] = array(
                        'secret' => $photo['secret'],
                        'server' => $photo['server'],
                        'farm' => $photo['farm'],
                        'ispublic' => $photo['ispublic'],
                        'title' => $photo['title'],
                        'ownername' => $photo['ownername'],
                        'tags' => str_replace(' ', ', ', $photo['tags']),
                        'description' => $photo['description']["_content"],
                        'owner' => $photo['owner'],
                        'date_upload' => $photo['dateupload'],
                        'url' => $imageUrl,
                        'id' => $photo['id'],
                        'content' => $imageUrl,
                        'type' => ($photo['media'] == 'photo' ? 'image' : $photo['media']),
                        'license' => $photo['license'],
                        'latitude' => $photo['latitude'],
                        'longitude' => $photo['longitude'],
                        'source' => 'Flickr',
                        'thumbnail' => $imageUrl
                            //'url' => 'https://farm' . $photo['farm'] . '.staticflickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'] . '_b.jpg',
                    );
                }
            }
            return $arrayResponse;
        } else {
            return false;
        }
    }

    private function getInfoFromItemAlbum($page) {
        $request = self::endPoint . self::method . 'photosets.getPhotos' . self::apiKey . $this->apiKeyValue . '&photoset_id=' . $this->albumId . '&extras=license,date_upload,date_taken,owner_name,icon_server,original_format,last_update,geo,tags,machine_tags,o_dims,views,media,path_alias,url_o' . self::perPage . '&page=' . $page . self::format;
        $reponse = $this->callFlickrAPI($request);
        $arrayResponse = array(array('pages' => $reponse['photoset']['pages'], 'total' => $reponse['photoset']['total']));
        if (is_array($reponse['photoset']['photo']) && !empty($reponse['photoset']['photo'])) {
            foreach ($reponse['photoset']['photo'] as &$photo) {
                if ($photo['media'] == 'photo') {
                    $imageUrl = (isset($photo['url_o']) ? $photo['url_o'] : '');
                    $itemTitle = (($photo['title'] != '') ? $photo['title'] : 'untitled');
                    $arrayResponse[] = array(
                        'secret' => $photo['secret'],
                        'server' => $photo['server'],
                        'farm' => $photo['farm'],
                        'ispublic' => $photo['ispublic'],
                        'title' => $itemTitle,
                        'ownername' => $photo['ownername'],
                        'tags' => str_replace(' ', ', ', $photo['tags']),
                        'description' => $photo['description']["_content"],
                        'owner' => $reponse['photoset']['owner'],
                        'date_upload' => $photo['dateupload'],
                        'url' => $imageUrl,
                        'id' => $photo['id'],
                        'content' => $imageUrl,
                        'type' => ($photo['media'] == 'photo' ? 'image' : $photo['media']),
                        'license' => $photo['license'],
                        'latitude' => $photo['latitude'],
                        'longitude' => $photo['longitude'],
                        'source' => 'Flickr',
                        'thumbnail' => $imageUrl
                    );
                }
            }
            return $arrayResponse;
        } else {
            return false;
        }
    }

    public function insertImageItem($data, $object_model, $status = 'publish') {
        $collection_id = $data['collectionId'];
        $class = new ObjectSaveValuesModel();
        $mapping_id = $this->get_post_by_title('socialdb_channel_flickr', $data['collectionId'], 'flickr');

        $getCurrentIds = unserialize(get_post_meta($mapping_id, 'socialdb_channel_flickr_inserted_ids', true));

        $getCurrentIds = (is_array($getCurrentIds) ? $getCurrentIds : array());

        if (!in_array($this->arrItem['id'], $getCurrentIds)) {
            $mapping = unserialize(get_post_meta($mapping_id, 'socialdb_channel_flickr_mapping', true));

            foreach ($mapping as $mp) {
                $form[$mp['tag']] = $mp['socialdb_entity'];
            }
            $object_id = socialdb_insert_object_socialnetwork_flickr($this->arrItem['title'], $status);
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
                            if ($metadata != ''):
                                $arrTags = explode(', ', $metadata);
                                foreach ($arrTags as $tags_value) {
                                    //var_dump($tags_value);
                                    $this->insert_tag($tags_value, $object_id, $collection_id);
                                }
                            endif;
                        //$this->insert_tag($metadata, $object_id, $collection_id);
                        elseif (strpos($form[$identifier], "termproperty_") !== false):
                            $trans = array("termproperty_" => "");
                            $property_id = strtr($form[$identifier], $trans);
                            $parent = get_term_meta($property_id, 'socialdb_property_term_root', true);
                            $term_id = $this->insert_hierarchy($metadata, $object_id, $collection_id, $parent);
                            $class->saveValue($object_id,
                                $property_id,
                                0,
                                'term',
                                0,
                                $term_id,
                                false
                            );
                        elseif (strpos($form[$identifier], "dataproperty_") !== false):
                            $trans = array("dataproperty_" => "");
                            $id = strtr($form[$identifier], $trans);
                            //add_post_meta($object_id, 'socialdb_property_' . $id . '', $metadata);
                            $class->saveValue($object_id,
                                $id,
                                0,
                                'data',
                                0,
                                $metadata,
                                false
                            );
                        endif;
                    }
                }
                $metadata = '';
                update_post_meta($object_id, 'socialdb_object_from', 'external');
                add_post_meta($object_id, 'socialdb_object_original_collection', $collection_id);
                update_post_content($object_id, $content);
                socialdb_add_tax_terms($object_id, $categories, 'socialdb_category_type');

                $getCurrentIds[$object_id] = $this->arrItem['id'];
                update_post_meta($mapping_id, 'socialdb_channel_flickr_inserted_ids', serialize($getCurrentIds));

                $user_id = current_user_id_or_anon();
                $logData = ['collection_id' => $collection_id, 'item_id' => $object_id,
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
        //socialdb_add_tax_terms($object_id, array($array['term_id']), 'socialdb_category_type');
        return $array['term_id'];
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

    public function insertFlickrItems(array $data, ObjectModel $object_model) {
        // índice da primeira página de resposta
        $currentPage = 1;
        // photos inclusas no banco
        $numRecortedPhotos = 0;
        // primeira requisição
        $response = $this->getInfoFromItem($currentPage);
        if ($response) {
            self::insert_flickr_identifier($data['identifier'], $data['collectionId']);
        }
        // número de photos na requisição
        $numPhotos = count($response) - 1;
        // paginação
        $numPages = $response[0]['pages'];

        if (!empty($response)) {
            // trata a primeira resposta: máximo de 500 photos
            for ($i = 1; $i <= $numPhotos; $i++) {
                $this->arrItem = $response[$i];
                $result = $this->insertImageItem($data, $object_model, 'draft');
                if ($result) {
                    $numRecortedPhotos++;
                    $arrSavedIds[] = $result;
                }
            }
            unset($response);
            // verifica se há mais de 500 photos
            if ($numPages > 1) {
                // esse loop só se repetirá caso exista mais de 1000 photos
                for ($i = 1; $i < $numPages; $i++) {
                    $currentPage++;
                    $response = $this->getInfoFromItem($currentPage);
                    $numPhotos = count($response) - 1;
                    // inserindo as photos no banco
                    for ($i = 1; $i <= $numPhotos; $i++) {
                        $this->arrItem = $response[$i];
                        $result = $this->insertImageItem($data, $object_model, 'draft');
                        if ($result) {
                            $numRecortedPhotos++;
                            $arrSavedIds[] = $result;
                        }
                    }
                    unset($response);
                }
            }
            return ($numRecortedPhotos > 0) ? $arrSavedIds : false;
        }
        return ($numRecortedPhotos > 0) ? true : false;
    }

    public function insertFlickrItemsFromAlbums(array $data, array $profile, ObjectModel $object_model) {
        $this->albumId = $profile[2];
        // índice da primeira página de resposta
        $currentPage = 1;
        // photos inclusas no banco
        $numRecortedPhotos = 0;
        // primeira requisição
        $response = $this->getInfoFromItemAlbum($currentPage);
        
        // número de photos na requisição
        $numPhotos = count($response) - 1;
        // paginação
        $numPages = $response[0]['pages'];

        if (!empty($response)) {
            // trata a primeira resposta: máximo de 500 photos
            for ($i = 1; $i <= $numPhotos; $i++) {
                $this->arrItem = $response[$i];
                $result = $this->insertImageItem($data, $object_model, 'draft');
                if ($result) {
                    $numRecortedPhotos++;
                    $arrSavedIds[] = $result;
                }
            }
            unset($response);
            // verifica se há mais de 500 photos
            if ($numPages > 1) {
                // esse loop só se repetirá caso exista mais de 1000 photos
                for ($i = 1; $i < $numPages; $i++) {
                    $currentPage++;
                    $response = $this->getInfoFromItemAlbum($currentPage);
                    $numPhotos = count($response) - 1;
                    // inserindo as photos no banco
                    for ($i = 1; $i <= $numPhotos; $i++) {
                        $this->arrItem = $response[$i];
                        $result = $this->insertImageItem($data, $object_model, 'draft');
                        if ($result) {
                            $numRecortedPhotos++;
                            $arrSavedIds[] = $result;
                        }
                    }
                    unset($response);
                }
            }
            return ($numRecortedPhotos > 0) ? $arrSavedIds : false;
        }
        return ($numRecortedPhotos > 0) ? true : false;
    }

    public function insertFlickrItems_albums(array $data, ObjectModel $object_model) {
        // índice da primeira página de resposta
        $currentPage = 1;
        // photos inclusas no banco
        $numRecortedPhotos = 0;
        // primeira requisição
        $response = $this->getInfoFromItem($currentPage);
        if ($response) {
            self::insert_flickr_identifier($data['identifier'], $data['collectionId']);
        }
        // número de photos na requisição
        $numPhotos = count($response) - 1;
        // paginação
        $numPages = $response[0]['pages'];

        if (!empty($response)) {
            // trata a primeira resposta: máximo de 500 photos
            for ($i = 1; $i <= $numPhotos; $i++) {
                $this->arrItem = $response[$i];
                $result = $this->insertImageItem($data, $object_model, 'draft');
                if ($result) {
                    $numRecortedPhotos++;
                    $arrSavedIds[] = $result;
                }
            }
            unset($response);
            // verifica se há mais de 500 photos
            if ($numPages > 1) {
                // esse loop só se repetirá caso exista mais de 1000 photos
                for ($i = 1; $i < $numPages; $i++) {
                    $currentPage++;
                    $response = $this->getInfoFromItem($currentPage);
                    $numPhotos = count($response) - 1;
                    // inserindo as photos no banco
                    for ($i = 1; $i <= $numPhotos; $i++) {
                        $this->arrItem = $response[$i];
                        $result = $this->insertImageItem($data, $object_model, 'draft');
                        if ($result) {
                            $numRecortedPhotos++;
                            $arrSavedIds[] = $result;
                        }
                    }
                    unset($response);
                }
            }
            return ($numRecortedPhotos > 0) ? $arrSavedIds : false;
        }
        return ($numRecortedPhotos > 0) ? true : false;
    }

    private function getOriginalSize($photo_id) {
        $request = self::endPoint . self::method . 'photos.getSizes' . self::apiKey . $this->apiKeyValue . '&photo_id=' . $photo_id . self::format;
        $reponse = $this->callFlickrAPI($request);
        return end($reponse['sizes']['size'])['source'];
    }

    private function getInfoFromSingleItem($profile) {
        $request = self::endPoint . self::method . 'photos.getInfo' . self::apiKey . $this->apiKeyValue . '&photo_id=' . $profile[2] . self::format;
        $reponse = $this->callFlickrAPI($request);
        $arrayResponse = array();
        if (is_array($reponse['photo']) && !empty($reponse['photo'])) {
            $photo = $reponse['photo'];
            if ($photo['media'] == 'photo') {
                $imageUrl = $this->getOriginalSize($profile[2]);
                if (isset($photo['tags']['tag']) && is_array($photo['tags']['tag'])) {
                    foreach ($photo['tags']['tag'] as $tag) {
                        $tags .= $tag['_content'] . ', ';
                    }
                }
                $arrayResponse[] = array(
                    'secret' => $photo['secret'],
                    'server' => $photo['server'],
                    'farm' => $photo['farm'],
                    'ispublic' => $photo['visibility']['ispublic'],
                    'title' => $photo['title']['_content'],
                    'ownername' => $photo['owner']['username'],
                    'tags' => $tags,
                    'description' => $photo['description']["_content"],
                    'owner' => $photo['owner']['path_alias'],
                    'date_upload' => $photo['dateuploaded'],
                    'url' => $imageUrl,
                    'id' => $photo['id'],
                    'content' => $imageUrl,
                    'type' => ($photo['media'] == 'photo' ? 'image' : $photo['media']),
                    'license' => $photo['license'],
                    'latitude' => $photo['latitude'],
                    'longitude' => $photo['longitude'],
                    'source' => 'Flickr',
                    'thumbnail' => $imageUrl
                        //'url' => 'https://farm' . $photo['farm'] . '.staticflickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'] . '_b.jpg',
                );
            }

            return $arrayResponse;
        } else {
            return false;
        }
    }

    public function insertFlickrSingleItem(array $data, array $profile, ObjectModel $object_model) {
        // única requisição
        $response = $this->getInfoFromSingleItem($profile);
        $numRecortedPhotos = 0;
        if (!empty($response)) {
            // trata a resposta
            $this->arrItem = $response[0];
            $result = $this->insertImageItem($data, $object_model, 'draft');
            if ($result) {
                $numRecortedPhotos++;
                $arrSavedIds[] = $result;
            }

            unset($response);
            return ($numRecortedPhotos > 0) ? $arrSavedIds : false;
        }
        return ($numRecortedPhotos > 0) ? true : false;
    }

    /* @name: peopleGetPublicPhotosUpdated()
     * @description: método público para todas as fotos públicas de um usuário
     * @return: array php contendo os dados de no máximo 500 fotos de um usuário
     * 
     */

    private function peopleGetPublicPhotosUpdated($date, $page) {
        $request = self::endPoint . self::method . 'photos.search' . self::apiKey . $this->apiKeyValue . '&user_id=' . $this->userId . '&min_upload_date=' . $date . '&sort=date-posted-asc&content_type=1&extras=views,date_upload,license,date_taken' . self::perPage . '&page=' . $page . self::format;
        $reponse = $this->callFlickrAPI($request);
        $arrayResponse = array(array('pages' => $reponse['photos']['pages'], 'total' => $reponse['photos']['total']));
        foreach ($reponse['photos']['photo'] as &$photo) {
            $arrayResponse[] = array(
                'title' => $photo['title'],
                'url' => 'https://farm' . $photo['farm'] . '.staticflickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'] . '_b.jpg',
                'date' => $photo['dateupload'],
                'datetaken' => $photo['datetaken'],
                'views' => $photo['views'],
                'embed_html' => '<embed width="200" height="200" src="' . 'https://farm' . $photo['farm'] . '.staticflickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'] . '_b.jpg" frameborder="0" allowfullscreen></iframe>',
                'embed' => 'https://farm' . $photo['farm'] . '.staticflickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'] . '_b.jpg',
                'thumbnail' => 'https://farm' . $photo['farm'] . '.staticflickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'] . '_b.jpg'
            );
        }
        return $arrayResponse;
    }

    /* @name: peopleGetAllPublicPhotosUpdated()
     * @description: método público para todas as fotos públicas de um usuário
     * @return: array php contendo os dados de no máximo 500 fotos de um usuário
     * 
     */

    public function peopleGetAllPublicPhotosUpdated(array $data, ObjectModel $object_model) {
        // data da última foto importada
        $dataLastPhotoImported = trim($data['data']);
        $curr_time = date("Y-m-d H:i:s", $dataLastPhotoImported + 60);
        $dataLastPhotoImported = strtotime($curr_time);

        // índice da primeira página de resposta
        $currentPage = 1;
        // photos inclusas no banco
        $numRecortedPhotos = 0;
        // primeira requisição
        $response = $this->peopleGetPublicPhotosUpdated($dataLastPhotoImported, $currentPage);

        // número de photos na requisição
        $numPhotos = count($response) - 1;
        // paginação
        $numPages = $response[0]['pages'];

        if (!empty($response)) {
            if ($numPhotos > 0) {
                //altera status da importação
                self::setImportStatus($data['identifierId'], 1);
                // trata a primeira resposta: máximo de 500 photos
                for ($i = 1; $i <= $numPhotos; $i++) {
                    $post_id = $object_model->add_photo($data['collection_id'], $response[$i]['title'], $response[$i]['embed']);
                    if ($post_id) {
                        self::setLastDateFlickr($data['identifierId'], $response[$i]['date']);
                        $object_model->add_thumbnail_url($response[$i]['thumbnail'], $post_id);
                        add_post_meta($post_id, 'socialdb_uri_imported', $response[$i]['thumbnail']);
                        $numRecortedPhotos++;
                    }
                }
                unset($response);
                // verifica se há mais de 500 photos
                if ($numPages > 1) {
                    // esse loop só se repetirá caso exista mais de 1000 photos
                    for ($i = 1; $i < $numPages; $i++) {
                        $currentPage++;
                        $response = $this->peopleGetPublicPhotosUpdated($dataLastPhotoImported, $currentPage);
                        $numPhotos = count($response) - 1;
                        // inserindo as photos no banco
                        for ($i = 1; $i <= $numPhotos; $i++) {
                            $post_id = $object_model->add_photo($data['collection_id'], $response[$i]['title'], $response[$i]['embed']);
                            if ($post_id) {
                                self::setLastDateFlickr($data['identifierId'], $response[$i]['date']);
                                $object_model->add_thumbnail_url($response[$i]['thumbnail'], $post_id);
                                add_post_meta($post_id, 'socialdb_uri_imported', $response[$i]['thumbnail']);
                                $numRecortedPhotos++;
                            };
                        }
                        unset($response);
                    }
                    return ($numRecortedPhotos > 0) ? true : false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
        return ($numRecortedPhotos > 0) ? true : false;
    }

    /**
     * @description - function insert_flickr_identifier($identifier)
     * $identifier é o nome do usuário do perfil flickr 
     * Insere um identificador de canal no banco
     * 
     * @autor: Saymon 
     */
//    public static function insert_flickr_identifier($identifier, $colectionId) {
//        $postId = wp_insert_post(['post_title' => $identifier, 'post_status' => 'publish', 'post_type' => 'socialdb_channel']);
//        if ($postId) {
//            add_post_meta($postId, 'socialdb_flickr_identificator', $colectionId);
//            add_post_meta($postId, 'socialdb_flickr_identificator_last_update', '');
//            add_post_meta($postId, 'socialdb_flickr_import_status', 0);
//            return true;
//        } else {
//            return false;
//        }
//    }

    static function insert_flickr_identifier($identifier, $colectionId) {
        $validation = self::verify_channel($identifier, $colectionId);
        if ($validation == false) {
            $postId = wp_insert_post(['post_title' => $identifier, 'post_status' => 'publish', 'post_type' => 'socialdb_channel']);
            if ($postId) {
                add_post_meta($postId, 'socialdb_flickr_identificator', $colectionId);
//            add_post_meta($postId, 'socialdb_channel_playlist_identificator', $playlist);
                add_post_meta($postId, 'socialdb_flickr_last_update', date("Y-m-d H:i:s"));
                add_post_meta($postId, 'socialdb_flickr_harvesting', 'disabled');
//            add_post_meta($postId, 'socialdb_channel_youtube_earlier_update', '');
//            add_post_meta($postId, 'socialdb_channel_youtube_import_status', 0);
                return true;
            } else {
                return false;
            }
        } else {
            update_post_meta($validation, 'socialdb_flickr_last_update', date("Y-m-d H:i:s"));
        }
    }

    static function verify_channel($identifier, $collectionId) {
        //array de configuração dos parâmetros de get_posts()
        $args = array(
            'meta_key' => 'socialdb_flickr_identificator',
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

    /**
     * @description - function edit_flickr_identifier($identifier)
     * $identifier -  o nome do usuário do perfil flickr 
     * $newIdentifier - novo valor  
     * altera um identificador de um dado perfil flickr
     * 
     * @autor: Saymon 
     */
    public static function edit_flickr_identifier($identifier, $newIdentifier) {
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
     * @description - function delete_flickr_identifier($identifier)
     * $identifier -  o nome do usuário do perfil flickr 
     * $colectionId - coleção a que o identificador pertence  
     * exclui um identificador de um dado perfil flickr
     * 
     * @autor: Saymon 
     */
    public static function delete_flickr_identifier($identifierId, $colectionId) {
        $deletedIdentifier = wp_delete_post($identifierId);
        if ($deletedIdentifier) {
            delete_post_meta($identifierId, 'socialdb_flickr_identificator', $identifier);
            delete_post_meta($identifierId, 'socialdb_flickr_identificator', $colectionId);
            return true;
        } else {

            return false;
        }
    }

    public static function list_flickr_identifier($collectionId) {
        //array de configuração dos parâmetros de get_posts()
        $args = array(
            'meta_key' => 'socialdb_flickr_identificator',
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
                    $postMetaLastUpdate = get_post_meta($ch->ID, 'socialdb_flickr_identificator_last_update');
                    $postMetaImportSatus = get_post_meta($ch->ID, 'socialdb_flickr_import_status');
                    $array = array('name' => $ch->post_title, 'id' => $ch->ID, 'lastUpdate' => $postMetaLastUpdate, 'importStatus' => $postMetaImportSatus);
                    $json['identifier'][] = $array;
                }
            }
            echo json_encode($json);
        } else {
            return false;
        }
    }

    static function setLastDateFlickr($post_id, $date) {
        update_post_meta($post_id, 'socialdb_flickr_identificator_last_update', $date);
    }

    static function setImportStatus($post_id, $date) {
        update_post_meta($post_id, 'socialdb_flickr_import_status', $date);
    }

}

?>