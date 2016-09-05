<?php

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');

class ThemeOptionsModel extends Model {

    public function change_pattern_license($id) {

        update_option('socialdb_pattern_licenses', $id);

        $result['title'] = __('Success', 'tainacan');
        $result['msg'] = __('Change pattern successfully', 'tainacan');
        $result['type'] = 'success';

        return $result;
    }

    public function delete_repository_license($id) {

        //DELETAR
        wp_delete_post($id);
        $custom_licenses = get_option('socialdb_custom_licenses');

        foreach ($custom_licenses as $key => $value) {
            if ($id == $value):
                unset($custom_licenses[$key]);
            endif;
        }

        update_option('socialdb_custom_licenses', $custom_licenses);

        $result['title'] = __('Success', 'tainacan');
        $result['msg'] = __('Delete successfully', 'tainacan');
        $result['type'] = 'success';

        return $result;
    }

    public function edit_repository_license($data) {
        $post = array(
            'ID' => $data['editLicenseId'],
            'post_title' => $data['add_license_name'],
            'post_content' => $data['add_license_description'],
            'post_status' => 'publish',
            'post_type' => 'socialdb_license'
        );
        wp_update_post($post);
        update_post_meta($data['editLicenseId'], 'socialdb_custom_license_url', $data['add_license_url']);

        $result['title'] = __('Success', 'tainacan');
        $result['msg'] = __('Edit successfully', 'tainacan');
        $result['type'] = 'success';

        return $result;
    }

    public function get_license_to_edit($id) {
        $object_post = get_post($id);
        $data_license['id'] = $object_post->ID;
        $data_license['nome'] = $object_post->post_title;
        $data_license['description'] = $object_post->post_content;
        $data_license['url'] = get_post_meta($object_post->ID, 'socialdb_custom_license_url', true);

        return $data_license;
    }

    public function get_licenses($type) {
        $option = ($type == 'standart' ? 'socialdb_standart_licenses' : 'socialdb_custom_licenses');
        $pattern = get_option('socialdb_pattern_licenses');
        $arrLicenses = get_option($option);
        foreach ($arrLicenses as $license) {
            $object_post = get_post($license);
            $data_license['id'] = $object_post->ID;
            $data_license['nome'] = $object_post->post_title;

            $data['licenses'][] = $data_license;
        }
        $data['pattern'] = $pattern;
        return $data;
    }

    public function verify_equal_license_title($title) {
        $arrStandartLicenses = get_option('socialdb_standart_licenses');
        $arrCustomLicenses = get_option('socialdb_custom_licenses');
        foreach ($arrStandartLicenses as $license) {
            $object_post = get_post($license);
            if ($object_post->post_title == $title) {
                return false;
            }
        }
        if ($arrCustomLicenses) {
            foreach ($arrCustomLicenses as $license) {
                $object_post = get_post($license);
                if ($object_post->post_title == $title) {
                    return false;
                }
            }
        }
        return true;
    }

    public function insert_custom_license($data) {
        $getLicenses = get_option('socialdb_custom_licenses');

        $post = array(
            'post_title' => $data['add_license_name'],
            'post_content' => $data['add_license_description'],
            'post_status' => 'publish',
            'post_type' => 'socialdb_license'
        );
        $object_id = wp_insert_post($post);
        if ($data['add_license_url'] != '')
            add_post_meta($object_id, 'socialdb_custom_license_url', $data['add_license_url']);
        wp_set_object_terms($object_id, array((int) get_term_by('slug', 'socialdb_license_custom', 'socialdb_license_type')->term_id), 'socialdb_license_type');

        if ($getLicenses) {
            $getLicenses[] = $object_id;
        } else {
            $getLicenses = array();
            $getLicenses[] = $object_id;
        }

        update_option('socialdb_custom_licenses', $getLicenses);

        $result['title'] = __('Success', 'tainacan');
        $result['msg'] = __('Registered successfully', 'tainacan');
        $result['type'] = 'success';

        return $result;
    }

    public function get_theme_options_data() {
        $socialdb_theme_options = get_option('socialdb_theme_options');
        return $socialdb_theme_options;
    }

    public function get_theme_general_options_data() {
        $data['blog_name'] = get_option('blogname');
        $data['blog_description'] = get_option('blogdescription');
        $data['socialdb_logo'] = get_option('socialdb_logo');
        $data['socialdb_repository_permissions'] = get_option('socialdb_repository_permissions');
        return $data;
    }

    /**
     * function update($data)
     * @param mix $data  Os dados que serao utilizados para atualizar os options do tema
     * @return json com os dados atualizados 
     * metodo que atualiza os dados da colecao
     * @author Marcus Bruno
     */
    public function update($data) {
        $fields = [
            'socialdb_youtube_api_id',
            'socialdb_flickr_api_id',
            'socialdb_fb_api_id',
            'socialdb_fb_api_secret',
            'socialdb_instagram_api_id',
            'socialdb_instagram_api_secret',
            'socialdb_vimeo_client_id',
            'socialdb_vimeo_api_secret',
            'socialdb_embed_api_id',
            'socialdb_google_client_id',
            'socialdb_google_secret_key',
            //'socialdb_google_redirect_uri',
            'socialdb_google_api_key'
        ];
        $options = get_option('socialdb_theme_options');

        foreach ($fields as $field) {
            if (!isset($data[$field])) {
                $new_options[$field] = (isset($options[$field])) ? $options[$field] : "";
            } else {
                $new_options[$field] = $data[$field];
            }
        }

        if (update_option('socialdb_theme_options', $new_options, 'yes')) {
            $data['title'] = __("Sucess", 'tainacan');
            $data['msg'] = __("Options successfully updated!", 'tainacan');
            $data['type'] = "success";
        } else {
            $data['title'] = __("Attention", 'tainacan');
            $data['msg'] = __("Options not updated!", 'tainacan');
            $data['type'] = "info";
        }

        return json_encode($data);
    }

    function update_welcome_email($data) {
        update_option('socialdb_welcome_email', $data['welcome_email_content']);

        $data['title'] = __("Sucess", 'tainacan');
        $data['msg'] = __("Options successfully updated!", 'tainacan');
        $data['type'] = "success";

        return json_encode($data);
    }

    function update_configuration($data) {
        $reload = false;
        $data['socialdb_repository_permissions'] = ['socialdb_collection_permission_create_collection' => $data['socialdb_collection_permission_create_collection'], 'socialdb_collection_permission_delete_collection' => $data['socialdb_collection_permission_delete_collection']];
        $data['repository_content'] = strip_tags($data['repository_content']);
   
        /*         * ***** */

        update_option('blogname', $data['repository_title']);
        update_option('blogdescription', $data['repository_content']);
        update_option('socialdb_repository_permissions', $data['socialdb_repository_permissions']);
        if($data['tainacan_module_activate']&&$data['tainacan_module_activate']!='default'){
             update_option('tainacan_module_activate', $data['tainacan_module_activate']);
             $reload = true;
        }else{
            update_option('tainacan_module_activate', '');
             $reload = false;
        }

        /*         * ***** */

        $socialdb_logo = get_option('socialdb_logo');

        if (isset($data['remove_thumbnail']) && $data['remove_thumbnail']) {
            delete_post_thumbnail($socialdb_logo);
        }

        if (isset($data['disable_empty_collection']) && $data['disable_empty_collection'] == 'disabled') {
            update_option('disable_empty_collection', 'true');
        } else {
            update_option('disable_empty_collection', 'false');
        }

        //var_dump($_FILES); exit();
        if ($_FILES) {
            if ($socialdb_logo) {
                $this->add_thumbnail($socialdb_logo);
            } else {
                $post = array(
                    'post_title' => 'socialdb-repository-logo',
                    'post_status' => 'publish'
                );
                $object_id = wp_insert_post($post);
                update_option('socialdb_logo', $object_id);
                $this->add_thumbnail($object_id);
                $socialdb_logo = $object_id;
            }

            if (isset($_FILES['socialdb_collection_cover']) && !empty($_FILES['socialdb_collection_cover'])) {
                $cover_id = $this->add_cover($socialdb_logo);
                update_post_meta($socialdb_logo, 'socialdb_respository_cover_id', $cover_id);
            }
        }
        
        if(isset($data['tainacan_cache']) && $data['tainacan_cache'] == 'true'){
             update_option('tainacan_cache', 'false');
        }else{
             update_option('tainacan_cache', 'true');
        }

        $data['title'] = __("Sucess", 'tainacan');
        $data['msg'] = __("Options successfully updated!", 'tainacan');
        $data['type'] = "success";
        $data['reload'] = $reload;


        return json_encode($data);
    }

    /**
     * @signature - fast_insert_url($data)
     * @param array $data Os dados vindos do formulario
     * @return json com os dados do resultado do evento criado
     * @description - Insere um objeto apenas com o titulo
     * @author: Eduardo 
     */
    public function list_files_attachment($object_id) {
        $post = get_post($object_id);
        $result = array();
        if (!is_object(get_post_thumbnail_id())) {
            $args = array(
                'post_type' => 'attachment',
                'numberposts' => -1,
                'post_status' => null,
                'post_parent' => $post->ID
            );

            $attachments = get_posts($args);
            $arquivos = get_post_meta($post->ID, '_file_id');
            if ($attachments) {
                foreach ($attachments as $attachment) {
                    if (in_array($attachment->ID, $arquivos)) {
                        $object_content = get_post_meta($object_id, 'socialdb_object_content', true);
                        if ($object_content != $attachment->ID) {
                            $obj['ID'] = $attachment->ID;
                            $obj['name'] = $attachment->post_title;
                            $obj['guid'] = $attachment->guid;
                            $obj['md5_inicial'] = get_post_meta($attachment->ID, 'md5_inicial', true);
                            $obj['size'] = filesize(get_attached_file($attachment->ID));
                            $result[] = $obj;
                        }
                    }
                }
            }
        }
        return $result;
    }

    public function is_url_exist($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($code == 200) {
            $status = true;
        } else {
            $status = false;
        } curl_close($ch);
        return $status;
    }

    public function get_all_attachments() {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $query = "
                SELECT p.* FROM $wp_posts p 
                WHERE p.post_type LIKE 'attachment'
            ";
        $result = $wpdb->get_results($query, ARRAY_A);
        if ($result && is_array($result) && count($result) > 0) {
            return $result;
        } else {
            return array();
        }
    }

}
