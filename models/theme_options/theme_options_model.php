<?php

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../collection/collection_model.php');

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
        if ($data['tainacan_module_activate'] && $data['tainacan_module_activate'] != 'default') {
            update_option('tainacan_module_activate', $data['tainacan_module_activate']);
            $reload = true;
        } else {
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

        if (isset($data['tainacan_cache']) && $data['tainacan_cache'] == 'true') {
            update_option('tainacan_cache', 'false');
        } else {
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

    public function getAipFiles() {
        if (is_dir(dirname(__FILE__) . '../../../data/aip')) {
            $dir = dirname(__FILE__) . '../../../data/aip';
            $files = scandir($dir);
            $result = array();

            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && !is_dir($dir . '/' . $file)) {
                    $arrName = explode('.', $file);
                    $format = end($arrName);
                    $permit = array(
                        'zip'
                    );
                    if (in_array($format, $permit)) {
                        $result[] = $file;
                    }
                }
            }

            return $result;
        } else {
            return false;
        }
    }

    public function delete_aip_file($file) {
        if (is_file(dirname(__FILE__) . '../../../data/aip/' . $file)) {
            unlink(dirname(__FILE__) . '../../../data/aip/' . $file);
            return true;
        } else {
            return false;
        }
    }

    public function upload_aip_file($file, $data) {
        $MaxFileSize = 2048;
        $return = array();
        if ($data['select_aip_type'] == 'dspace') {

            $FileAccept = [
                'application/zip',
                'application/octet-stream'
                    //'application/x-zip-compressed', 
                    //'multipart/x-zip', 
                    //'application/x-compressed'
            ];

            if ($file === null):
                $result = false;
                $error = __("Envie um arquivo zip.", 'tainacan');
            elseif ($file['size'] > ($MaxFileSize * (1024 * 1024))):
                $result = false;
                $error = __("File is too big, max size is {$MaxFileSize}MB.", 'tainacan');
            elseif (!in_array($file['type'], $FileAccept)):
                $result = false;
                $error = __("File not supported. Send .ZIP!", 'tainacan');
            else:
                $name = time() . '_' . $file['name'];
                if (move_uploaded_file($file['tmp_name'], dirname(__FILE__) . '/../../data/aip/' . $name)):
                    $result = true;
                    $error = __("Arquivo enviado com sucesso!", 'tainacan');
                else:
                    $result = false;
                    $error = __("Erro ao mover o arquivo. Favor tente mais tarde!", 'tainacan');
                endif;
            endif;
        }else {
            $result = false;
            $error = __("Not implemented yet!", 'tainacan');
        }
        $return['result'] = $result;
        $return['error'] = $error;
        return json_encode($return);
    }

    public function verify_aip_file($file) {
        $filepath = dirname(__FILE__) . '/../../data/aip/' . $file;
        return (is_file($filepath) && file_exists($filepath) ? true : false);
    }

    public function unzip_aip_file($file) {
        /* here it is really happening */
        $filename = str_replace('.zip', '', $file);
        $targetdir = dirname(__FILE__) . '/../../data/aip/' . $filename;
        $targetzip = dirname(__FILE__) . '/../../data/aip/' . $file;

        //Se a pasta ja existir, ela é deletada
        if (is_dir($targetdir)) {
            $this->recursiveRemoveDirectory($targetdir);
        }

        /* Extracting Zip File */
        $zip = new ZipArchive();
        $x = $zip->open($targetzip);  // open the zip file to extract
        if ($x === true) {
            $zip->extractTo($targetdir); // place in the directory with same name  
            $zip->close();
        }

        return $targetdir;
    }

    public function unzip_aip_general($unzip_path, $file) {
        $filename = str_replace('.zip', '', $file);
        $targetdir = $unzip_path . $filename;
        $targetzip = $unzip_path . $file;

        /* Extracting Zip File */

        $zip = new ZipArchive();
        $x = $zip->open($targetzip);  // open the zip file to extract
        if ($x === true) {
            $zip->extractTo($targetdir); // place in the directory with same name  
            $zip->close();
            unlink($targetzip); //Deleting the Zipped file
        }

        return $targetdir;
    }

    public function read_site_xml($xml) {
        $title = (string) $xml->dmdSec[0]->mdWrap->xmlData->children('http://www.loc.gov/mods/v3')->mods->titleInfo->title;
        $groups = $xml->amdSec->techMD->mdWrap->xmlData->DSpaceRoles->Groups;
        $persons = $xml->amdSec->techMD->mdWrap->xmlData->DSpaceRoles->People;

        /*         * ************************ */
        update_option('blogname', $title);
        foreach ($persons->Person as $person) {
            $info = array();
            if (!email_exists($person->Email) && !username_exists($person->Email)) {
                $info['user_login'] = $person->Email;
                $info['user_email'] = $person->Email;
                $info['user_pass'] = md5(time());
                $info['first_name'] = $person->FirstName;
                $info['last_name'] = $person->LastName;
                $this->register_xml_user($info);
            }
        }
        foreach ($groups->Group as $group) {
            $attributes = $group->attributes();
            if ($attributes->Name == 'Administrator') {
                if (isset($group->Members->Member)) {
                    foreach ($group->Members->Member as $member) {
                        $user = get_user_by('email', $member['Name']);
                        if ($user) {
                            $userdata = array(
                                'ID' => $user->ID,
                                'role' => 'administrator'
                            );
                            $user_id = wp_update_user($userdata);
                        }
                    }
                }
            }
        }
    }

    public function register_xml_user($data) {
        global $wpdb;

        $login = strip_tags(trim($data['user_login']));
        $login = str_replace(' ', '-', $login);
        $login = str_replace(array('-----', '----', '---', '--'), '-', $login);
        $userdata = array(
            'user_login' => $login,
            'user_email' => $data['user_email'],
            'user_url' => '',
            'user_pass' => $data['user_pass'],
            //user meta
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'rich_editing' => 'true',
            'comment_shortcuts' => false,
            'show_admin_bar_front' => false,
            'wp_user_level' => 0,
            'wp_capabilities' => 'a:1:{s:10:"subscriber";b:1;}'
        );

        $user_id = wp_insert_user($userdata);

        if (isset($data['about_you'])) {
            $about_you = sanitize_text_field($data['about_you']);
            update_user_meta($user_id, 'about_you', $about_you);
        }
        if (isset($data['current_work'])) {
            $current_work = sanitize_text_field($data['current_work']);
            update_user_meta($user_id, 'current_work', $current_work);
        }
        if (isset($data['prof_resume'])) {
            $p_resume = sanitize_text_field($data['prof_resume']);
            update_user_meta($user_id, 'prof_resume', $p_resume);
        }
    }

    public function read_community_xml($xml) {
        $collection_model = new CollectionModel;
        $objid = (string) $xml->attributes()->OBJID;
        $id = explode(':', $objid)[1];
        $dim = $xml->dmdSec[1]->mdWrap->xmlData->children('http://www.dspace.org/xmlns/dspace/dim')->dim->field;
        $description = (string) $dim[0];
        $abstract = (string) $dim[1];
        $tableofcontents = (string) $dim[2];
        $uri = (string) $dim[3];
        $rights = (string) $dim[4];
        $title = (string) $dim[5];

        if (!$this->checkForAipID($id)) {
            $data_collection['collection_name'] = $title;
            $collection_id = $collection_model->simple_add($data_collection, 'publish');
            //create_root_collection_category($collection_id, $data_collection['collection_object']);
            $category_root_id = get_post_meta($collection_id, 'socialdb_collection_object_type', true);

            $collection = array(
                'ID' => $collection_id,
                'post_content' => $description
            );
            wp_update_post($collection);

            update_post_meta($collection_id, 'socialdb_dspace_aip_import_id', $id);
        }
    }

    public function checkForAipID($id) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "
                        SELECT pm.* FROM $wp_postmeta pm
                            INNER JOIN $wp_posts p ON p.ID = pm.post_id AND p.post_status LIKE 'publish'
                        WHERE pm.meta_key LIKE 'socialdb_dspace_aip_import_id' and pm.meta_value LIKE '{$id}'
                ";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result) && count($result) > 0) {
            return $result[0]->post_id;
        } else {
            return false;
        }
    }

    public function read_collection_xml($xml, $dir) {
        $collection_model = new CollectionModel;
        $objid = (string) $xml->attributes()->OBJID;
        $id = explode(':', $objid)[1];
        $struct = $xml->structMap;
        $logo = (isset($xml->fileSec->fileGrp->file->FLocat) ? (string) $xml->fileSec->fileGrp->file->FLocat->attributes('http://www.w3.org/1999/xlink')->href : null);
        $dim = $xml->dmdSec[1]->mdWrap->xmlData->children('http://www.dspace.org/xmlns/dspace/dim')->dim->field;
        $description = (string) $dim[0];
        $abstract = (string) $dim[1];
        $tableofcontents = (string) $dim[2];
        $uri = (string) $dim[3];
        $provenance = (string) $dim[4];
        $rights = (string) $dim[5];
        $license = (string) $dim[6];
        $title = (string) $dim[7];
        //var_dump($dim, $logo, $title);
        foreach ($struct as $parent) {
            if ($parent->attributes()->LABEL == 'Parent' && $parent->attributes()->TYPE == 'LOGICAL') {
                $parent_id = $parent->div->mptr->attributes('http://www.w3.org/1999/xlink')->href;
            }
        }

        if (!$this->checkForAipID($id)) {
            $data_collection['collection_name'] = $title;
            $collection_id = $collection_model->simple_add($data_collection, 'publish');
            //create_root_collection_category($collection_id, $data_collection['collection_object']);
            $category_root_id = get_post_meta($collection_id, 'socialdb_collection_object_type', true);

            $collection = array(
                'ID' => $collection_id,
                'post_content' => $description
            );
            wp_update_post($collection);

            update_post_meta($collection_id, 'socialdb_dspace_aip_import_id', $id);

            if ($logo) {
                $logo_id = $this->insert_attachment_file($dir . $logo, $collection_id);
                set_post_thumbnail($collection_id, $logo_id);
            }

            $parent_collection_id = $this->checkForAipID($parent_id);
            if (!empty($parent_id) && $parent_collection_id) {
                //eh uma subcoleção
                $category_root_id_parent = $this->get_category_root_of($parent_collection_id);
                $move_to = get_term_by('id', $category_root_id_parent, 'socialdb_category_type');
                if ($move_to && !is_wp_error($move_to)) {
                    $update_category = wp_update_term($category_root_id, 'socialdb_category_type', array(
                        'parent' => $move_to->term_id
                    ));
                    update_post_meta($collection_id, 'socialdb_collection_parent', $category_root_id_parent);
                }
                //var_dump($category_root_id);
            }
        }
        //else {
        //é uma coleção
        //}
        //var_dump($xml);
        //exit();
        /* $category_root_id = $this->get_category_root_of($data['collection_id']);
          $move_to = get_term_by('id', $data['socialdb_collection_parent'], 'socialdb_category_type');
          if ($move_to && !is_wp_error($move_to)) {
          $update_category = wp_update_term($category_root_id, 'socialdb_category_type', array(
          'parent' => $move_to->term_id
          ));
          update_post_meta($post_id, 'socialdb_collection_parent', $data['socialdb_collection_parent']);
          } */
    }

    function read_item_xml($xml, $dir) {
        $objid = (string) $xml->attributes()->OBJID;
        $id = explode(':', $objid)[1];
        $struct = $xml->structMap;
        
        foreach ($struct as $parent) {
            if ($parent->attributes()->LABEL == 'Parent' && $parent->attributes()->TYPE == 'LOGICAL') {
                $col_id = $parent->div->mptr->attributes('http://www.w3.org/1999/xlink')->href;
            }
        }
        
        $parent_collection_id = $this->checkForAipID($col_id);
        
        if($parent_collection_id){
            //Realiza a importacao
            
        }
        
        var_dump($parent_collection_id, $col_id);
        exit();
    }

}
