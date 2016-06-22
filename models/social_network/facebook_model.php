<?php

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
//require_once 'facebook/autoload.php';

/**
 * @clas-name	FacebookModel 
 * @description	integrar uma conta do facebook a uma coleção do Tainacan
 * @version    	1.0
 */
class FacebookModel extends Model {

    public $images_ids;
    public $arrItem;
    public $numInsertedItems;

    public function getPhotos($accessToken, $collection_id) {
        $object_model = new ObjectModel();
        $config = get_option('socialdb_theme_options');
        $app['app_id'] = $config['socialdb_fb_api_id'];
        $app['app_secret'] = $config['socialdb_fb_api_secret'];

        $fbApp = new Facebook\FacebookApp($app['app_id'], $app['app_secret']);
        $request = new Facebook\FacebookRequest(
                $fbApp, $accessToken, 'GET', '/me/photos', array(
            'fields' => 'album,backdated_time,backdated_time_granularity,can_delete,can_tag,created_time,event,from,height,icon,id,images,link,name,name_tags,page_story_id,picture,place,updated_time,tags,likes,width,comments',
            'limit' => '100',
            'type' => 'uploaded'
                )
        );

        $request_me = new Facebook\FacebookRequest(
                $fbApp, $accessToken, 'GET', '/me'
        );

        $urlBase_me = 'https://graph.facebook.com' . $request_me->getUrl();
        $urlBase = 'https://graph.facebook.com' . $request->getUrl();

        $resposta_me = file_get_contents($urlBase_me);
        $resposta = file_get_contents($urlBase);
        $json_me = &json_decode($resposta_me, true);
        $json = &json_decode($resposta, true);

        $user_id = $json_me['id'];

        $json = $this->getMediaInfo($json);
        
        //Verifica se esse ID já foi importado alguma vez
//        $list_ids = self::list_facebook_identifier($collection_id);
//
//        if ($list_ids && !empty($list_ids)) {
//            $id_exists = 0;
//            foreach ($list_ids['identifier'] as $list_id) {
//                if ($list_id['name'] == $user_id) {
//                    $id_exists = 1;
//                    $imported_photos = $list_id['UpdateIds'];
//                    $fb_post_id = $list_id['id'];
//                    break;
//                }
//            }
//
//            if ($id_exists != 1) {
//                $fb_post_id = self::insert_facebook_identifier($user_id, $collection_id);
//            }
//        } else {
//            $fb_post_id = self::insert_facebook_identifier($user_id, $collection_id);
//        }
//
//        if (empty($imported_photos[0]) && !isset($imported_photos[0])) {
//            $imported_photos[0] = array();
//        }
//        echo "<hr>";
//        echo "<pre>";
//        var_dump($imported_photos);
//        echo "<hr>";
//        var_dump($json);
//        echo "</pre>";
//        echo "<hr>";
//        exit();

        $param['collection_id'] = $collection_id;
        $this->numInsertedItems = 0;
        $this->images_ids = [];

        if (!empty($json['data'])) {
            foreach ($json['resultdata'] as $media) {
                $this->arrItem = $media;
                $result = $this->insertFacebookItem($param, $object_model, 'draft');
                if ($result) {
                    $this->numInsertedItems++;
                    $this->images_ids[] = $result;
                }
            }
        }
        
//        $this->images_ids = [];
//        if (!empty($json['data'])) {
//            foreach ($json['data'] as $photo) {
//                if ($photo['from']['id'] == $user_id) {
//                    if (!in_array($photo['id'], $imported_photos[0])) {
//                        $img_tag = '<img src="' . $photo['images'][0]['source'] . '" style="max-width:200px; max-height:200px;" /><br>';
//                        $post_id = $object_model->add_photo($collection_id, $photo['name'], $img_tag . $photo['id']);
//                        if ($post_id) {
//                            $this->images_ids[] = $photo['id'];
//                            $object_model->add_thumbnail_url($photo['images'][0]['source'], $post_id);
//                            add_post_meta($post_id, 'socialdb_uri_imported', $photo['images'][0]['source']);
//                        }
//                    } else {
//                        $this->images_ids[] = $photo['id'];
//                    }
//                }
//            }
//        }

        if (isset($json['paging']['next']) && !empty($json['paging']['next'])) {
            $this->interaction_getPhotos($json['paging']['next'], $user_id, $param, $imported_photos, $fb_post_id);
        }

        if ($this->numInsertedItems>0) {
            $_SESSION['facebookInsertedIds'] = json_encode($this->images_ids);
        } else {
            $_SESSION['facebookInsertedIds'] = 'facebook_error';
        }
        wp_redirect(get_the_permalink($collection_id));
    }

    private function getMediaInfo($jsonResponse) {
        if (!empty($jsonResponse['data'])) {
            foreach ($jsonResponse['data'] as $media) {
                $jsonResponse['resultdata'][] = array(
                    'from_name' => $media["from"]['name'],
                    'from_id' => $media['from']['id'],
                    'created_time' => $media['created_time'],
                    'id' => $media['id'],
                    'link' => $media['link'],
                    'name' => $media['name'],
                    'place' => $media['place']["name"],
                    'type' => 'image',
                    'content' => $media['images'][0]['source'],
                    'source' => 'Facebook',
                    'thumbnail' => $media['images'][0]['source']
                );
            }
        }
        return $jsonResponse;
    }

    public function interaction_getPhotos($url, $user_id, $param, $imported_photos, $fb_post_id) {
        $object_model = new ObjectModel();
        $resposta = file_get_contents($url);
        $json = &json_decode($resposta, true);
        $json = $this->getMediaInfo($json);

        if (!empty($json['data'])) {
            foreach ($json['resultdata'] as $media) {
                $this->arrItem = $media;
                $result = $this->insertFacebookItem($param, $object_model, 'draft');
                if ($result) {
                    $this->numInsertedItems++;
                    $this->images_ids[] = $result;
                }
            }
        }

        if (isset($json['paging']['next']) && !empty($json['paging']['next'])) {
            $this->interaction_getPhotos($json['paging']['next'], $user_id, $param, $imported_photos, $fb_post_id);
        }
    }

    public function insertFacebookItem($data, $object_model, $status = 'publish') {
        $collection_id = $data['collection_id'];
        $mapping_id = $this->get_post_by_title('socialdb_channel_facebook', $collection_id, 'facebook');

        $getCurrentIds = unserialize(get_post_meta($mapping_id, 'socialdb_channel_facebook_inserted_ids', true));

        $getCurrentIds = (is_array($getCurrentIds) ? $getCurrentIds : array());

        if (!in_array($this->arrItem['id'], $getCurrentIds)) {
            $mapping = unserialize(get_post_meta($mapping_id, 'socialdb_channel_facebook_mapping', true));

            foreach ($mapping as $mp) {
                $form[$mp['tag']] = $mp['socialdb_entity'];
            }

            $object_id = socialdb_insert_object_socialnetwork($this->arrItem['id'], $status);

            //mapping
            add_post_meta($object_id, 'socialdb_channel_id', $mapping_id);
            $categories[] = $this->get_category_root_of($collection_id);
            if ($object_id != 0) {
                $this->add_thumbnail_url_facebook($this->arrItem['thumbnail'], $object_id);
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
                update_post_meta($mapping_id, 'socialdb_channel_facebook_inserted_ids', serialize($getCurrentIds));

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

    /**
     * @description - function insert_facebook_identifier($identifier)
     * $identifier é o nome do usuário do perfil flickr 
     * Insere um identificador de canal no banco
     * 
     * @autor: Saymon 
     */
    public static function insert_facebook_identifier($identifier, $colectionId) {
        $postId = wp_insert_post(['post_title' => $identifier, 'post_status' => 'publish', 'post_type' => 'socialdb_channel']);
        if ($postId) {
            add_post_meta($postId, 'socialdb_facebook_identificator', $colectionId);
            add_post_meta($postId, 'socialdb_facebook_identificator_last_update', '');
            add_post_meta($postId, 'socialdb_facebook_import_status', 0);
            return $postId;
        } else {
            return false;
        }
    }

    /**
     * @description - function edit_facebook_identifier($identifier)
     * $identifier -  o nome do usuário do perfil flickr 
     * $newIdentifier - novo valor  
     * altera um identificador de um dado perfil flickr
     * 
     * @autor: Saymon 
     */
    public static function edit_facebook_identifier($identifier, $newIdentifier) {
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
     * @description - function delete_facebook_identifier($identifier)
     * $identifier -  o nome do usuário do perfil flickr 
     * $colectionId - coleção a que o identificador pertence  
     * exclui um identificador de um dado perfil flickr
     * 
     * @autor: Saymon 
     */
    public static function delete_facebook_identifier($identifierId, $colectionId) {
        $deletedIdentifier = wp_delete_post($identifierId);
        if ($deletedIdentifier) {
            delete_post_meta($identifierId, 'socialdb_facebook_identificator', $identifier);
            delete_post_meta($identifierId, 'socialdb_facebook_identificator', $colectionId);
            return true;
        } else {

            return false;
        }
    }

    public static function list_facebook_identifier($collectionId) {
        //array de configuração dos parâmetros de get_posts()
        $args = array(
            'meta_key' => 'socialdb_facebook_identificator',
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
                    $postMetaLastUpdate = get_post_meta($ch->ID, 'socialdb_facebook_ids_last_update');
                    $postMetaImportSatus = get_post_meta($ch->ID, 'socialdb_facebook_import_status');
                    $array = array('name' => $ch->post_title, 'id' => $ch->ID, 'UpdateIds' => $postMetaLastUpdate, 'importStatus' => $postMetaImportSatus);
                    $json['identifier'][] = $array;
                }
            }
            return $json;
        } else {
            return false;
        }
    }

    private static function setUpdateIdsFacebook($post_id, $ids) {
        update_post_meta($post_id, 'socialdb_facebook_ids_last_update', $ids);
    }

    private function setImportStatus($post_id, $status) {
        update_post_meta($post_id, 'socialdb_facebook_import_status', $status);
    }

}

?>