<?php

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
//require_once 'facebook/autoload.php';

/**
 * @clas-name	VimeoModel 
 * @description	integrar uma conta do vimeo a uma coleção do Tainacan
 * @version    	1.0
 */
class VimeoModel extends Model {

    public $item_ids;
    public $arrItem;
    public $numInsertedItems;

    public function getVimeoVideos($data) {
        $object_model = new ObjectModel;
        $config = get_option('socialdb_theme_options');
        $app['app_id'] = $config['socialdb_vimeo_client_id'];
        $app['app_secret'] = $config['socialdb_vimeo_api_secret'];
        $lib = new \Vimeo\Vimeo($app['app_id'], $app['app_secret']);

        // scope is an array of permissions your token needs to access. You can read more at https://developer.vimeo.com/api/authentication#scopes
        $token = $lib->clientCredentials('public');

        // use the token
        $lib->setToken($token['body']['access_token']);

        $response = $lib->request('/' . $data['import_type'] . '/' . $data['identifier'] . '/videos/', array('per_page' => 50), 'GET');

        if ($response["body"]["total"] > 0) {
            $json = $this->getMediaInfo($response['body']['data']);

            $param['collection_id'] = $data['collectionId'];
            $this->numInsertedItems = 0;
            $this->item_ids = [];

            if (!empty($json)) {
                foreach ($json as $media) {
                    $this->arrItem = $media;
                    $result = $this->insertVimeoItem($param, $object_model, 'draft');
                    if ($result) {
                        $this->numInsertedItems++;
                        $this->item_ids[] = $result;
                    }
                }
            }

            if (isset($response["body"]["paging"]['next']) && !empty($response["body"]['paging']['next'])) {
                $this->interaction_getVimeoVideos($response["body"]['paging']['next'], $lib, $param);
            }

            if ($this->numInsertedItems > 0) {
                return $this->item_ids;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function interaction_getVimeoVideos($url, $lib, $param) {
        $object_model = new ObjectModel();
        ///users/nomedocanal/videos/?per_page=5&page=2
        $url = explode('?', $url);
        $url_opt = explode('=', $url[1]);
        $response = $lib->request($url[0], array('per_page' => 50, 'page' => $url_opt[2]), 'GET');
        $json = $this->getMediaInfo($response['body']['data']);

        if (!empty($json)) {
            foreach ($json as $media) {
                $this->arrItem = $media;
                $result = $this->insertVimeoItem($param, $object_model, 'draft');
                if ($result) {
                    $this->numInsertedItems++;
                    $this->item_ids[] = $result;
                }
            }
        }

        if (isset($response["body"]['paging']['next']) && !empty($response["body"]['paging']['next'])) {
            $this->interaction_getVimeoVideos($response["body"]['paging']['next'], $lib, $param);
        }
    }

    private function getMediaInfo($jsonResponse) {
        if (!empty($jsonResponse)) {
            foreach ($jsonResponse as $media) {
                $media_id = explode('/', $media['uri'])[2];
                $json[] = array(
                    'name' => $media['name'],
                    'description' => $media['description'],
                    'link' => $media['link'],
                    'id' => $media_id,
                    'created_time' => $media['created_time'],
                    'license' => $media['license'],
                    'tags' => implode(', ', $media['tags']),
                    'user' => $media['user']['name'],
                    'type' => 'video',
                    'content' => 'https://vimeo.com/' . $media_id,
                    'source' => 'Vimeo',
                    'thumbnail' => $media['pictures']['sizes'][5]['link']
                );
            }
        } else {
            $json = false;
        }
        return $json;
    }

    public function insertVimeoItem($data, $object_model, $status = 'publish') {
        $collection_id = $data['collection_id'];
        $mapping_id = $this->get_post_by_title('socialdb_channel_vimeo', $collection_id, 'vimeo');

        $getCurrentIds = unserialize(get_post_meta($mapping_id, 'socialdb_channel_vimeo_inserted_ids', true));
        $getCurrentIds = (is_array($getCurrentIds) ? $getCurrentIds : array());

        if (!in_array($this->arrItem['id'], $getCurrentIds)) {
            $mapping = unserialize(get_post_meta($mapping_id, 'socialdb_channel_vimeo_mapping', true));

            foreach ($mapping as $mp) {
                $form[$mp['tag']] = $mp['socialdb_entity'];
            }

            $object_id = socialdb_insert_object_socialnetwork($this->arrItem['name'], $status);

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

                $getCurrentIds[$object_id] = $this->arrItem['id'];
                update_post_meta($mapping_id, 'socialdb_channel_vimeo_inserted_ids', serialize($getCurrentIds));

                $user_id = current_user_id_or_anon();
                $logData = ['collection_id' => $collection_id, 'item_id' => $object_id,
                  'user_id' => $user_id, 'event_type' => 'user', 'event' => 'add_item' ];
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

}

?>