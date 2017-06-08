<?php

ini_set('max_input_vars', '10000');
include_once (dirname(__FILE__) . '/../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../wp-includes/wp-db.php');
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
        Log::addLog(['event_type' => 'admin', 'event' => 'keys']);
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
            'socialdb_google_api_key',
            'socialdb_eur_api_key',
            'socialdb_eur_private_key'
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

    function update_devolution_email_alert($data)
    {
        update_option('socialdb_devolution_email_alert', $data['devolution_email_alert_content']);

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
        
        if (isset($data['remove_cover']) && $data['remove_cover']) {
            $cover_id = get_option('socialdb_repository_cover_id');
            wp_delete_attachment($cover_id);
            delete_option('socialdb_repository_cover_id');
            $reload = true;
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

            if (isset($_FILES['socialdb_collection_cover']) && !empty($_FILES['socialdb_collection_cover']) && !empty($_FILES['socialdb_collection_cover']['name'])) {
                $cover_id = $this->add_cover(get_option('collection_root_id'));
                update_option('socialdb_repository_cover_id', $cover_id);
                $reload = true;
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

        //Salva mapeamento de Coleções do Tainacan Biblioteca

        ini_set('display_errors', '0');     # don't show any errors...
        error_reporting(E_ALL | E_STRICT);  # ...but do log them

        if(!empty($data['collections']))
        {
            update_option('socialdb_general_mapping_collection', $data['collections']);
        }

        //Loan time
        $loan_time = $data['default_time'];
        update_option('socialdb_loan_time', $loan_time);
        
        //Days of devolution
        if(!empty($data['weekday']))
        {
            update_option('socialdb_devolution_weekday', $data['weekday']);
        }
        
        //Devolution day problem
        if(!empty($data['devolutionDayProblem']))
        {
            update_option('socialdb_devolution_day_problem', $data['devolutionDayProblem']);
        }

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
        if (is_dir(TAINACAN_UPLOAD_FOLDER . '/data/aip')) {
            $dir = TAINACAN_UPLOAD_FOLDER . '/data/aip';
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
        if (is_file(TAINACAN_UPLOAD_FOLDER . '/data/aip/' . $file)) {
            unlink(TAINACAN_UPLOAD_FOLDER . '/data/aip/' . $file);
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
                'application/octet-stream',
                'application/x-zip-compressed',
                'multipart/x-zip',
                'application/x-compressed'
            ];

            if ($file === null):
                $result = false;
                $error = __("Send file zip.", 'tainacan');
            elseif ($file['size'] > ($MaxFileSize * (1024 * 1024))):
                $result = false;
                $error = __("File is too big, max size is {$MaxFileSize}MB.", 'tainacan');
            elseif (!in_array($file['type'], $FileAccept)):
                $result = false;
                $error = __("File not supported. Send .ZIP!", 'tainacan');
            else:
                $name = time() . '_' . $file['name'];
                if (!is_dir(TAINACAN_UPLOAD_FOLDER . '/data/aip')) {
                    mkdir(TAINACAN_UPLOAD_FOLDER . '/data/aip');
                }
                if (move_uploaded_file($file['tmp_name'], TAINACAN_UPLOAD_FOLDER . '/data/aip/' . $name)):
                    $result = true;
                    $error = __("File sent successfully!", 'tainacan');
                else:
                    $result = false;
                    $error = __("Fail sending file. Please try again!", 'tainacan');
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
        $filepath = TAINACAN_UPLOAD_FOLDER . '/data/aip/' . $file;
        return (is_file($filepath) && file_exists($filepath) ? true : false);
    }

    public function unzip_aip_file($file) {
        /* here it is really happening */
        $filename = str_replace('.zip', '', $file);
        $targetdir = TAINACAN_UPLOAD_FOLDER . '/data/aip/' . $filename;
        $targetzip = TAINACAN_UPLOAD_FOLDER . '/data/aip/' . $file;

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
            $collection_id = $collection_model->simple_add($data_collection, 'published');
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
        ini_set('max_execution_time', '0');
        $objid = (string) $xml->attributes()->OBJID;
        $id = explode(':', $objid)[1];
        $struct = $xml->structMap;

        foreach ($struct as $parent) {
            if ($parent->attributes()->LABEL == 'Parent' && $parent->attributes()->TYPE == 'LOGICAL') {
                $col_id = $parent->div->mptr->attributes('http://www.w3.org/1999/xlink')->href;
            }
        }

        $parent_collection_id = $this->checkForAipID($col_id);

        if ($parent_collection_id && !$this->checkForAipID($id)) {
            //Realiza a importacao
            $mods = $xml->dmdSec[0]->mdWrap->xmlData->children('http://www.loc.gov/mods/v3')->mods;
            $dim = $xml->dmdSec[1]->mdWrap->xmlData->children('http://www.dspace.org/xmlns/dspace/dim')->dim->field;

            $meta_dim = array();
            for ($i = 0; $i < count($dim); $i++) {
                $dim_attr = $xml->dmdSec[1]->mdWrap->xmlData->children('http://www.dspace.org/xmlns/dspace/dim')->dim->field[$i]->attributes();
                //var_dump($dim_attr->element);
                $qualifier = (isset($dim_attr->qualifier) && !empty($dim_attr->qualifier) ? (string) $dim_attr->qualifier : null);
                if ($qualifier) {
                    $meta_dim[(string) $dim_attr->element][$qualifier][] = (string) $dim[$i];
                } else {
                    $meta_dim[(string) $dim_attr->element][] = (string) $dim[$i];
                }
            }

            $metadados = array();
            $contributor = $this->arrange_meta_array($meta_dim['contributor']);
            $metadados['contributor'] = ['id' => null, 'value' => implode('; ', $contributor)];
            $metadados['creator'] = ['id' => null, 'value' => $meta_dim['creator'][0]];
            $date_accessioned = $this->arrange_meta_array($meta_dim['date']['accessioned'], false);
            $metadados['date_accessioned'] = ['id' => null, 'value' => implode('; ', $date_accessioned)];
            $metadados['date_available'] = ['id' => null, 'value' => $meta_dim['date']['available'][0]];
            $metadados['date_issued'] = ['id' => null, 'value' => $meta_dim['date']['issued'][0]];
            $metadados['identifier_citation'] = ['id' => null, 'value' => $meta_dim['identifier']['citation'][0]];
            $identifier_uri = $this->arrange_meta_array($meta_dim['identifier']['uri'], false);
            $metadados['identifier_uri'] = ['id' => null, 'value' => implode('; ', $identifier_uri)];
            $metadados['abstract'] = ['id' => null, 'value' => $meta_dim['description']['abstract'][0]];
            $metadados['provenance'] = ['id' => null, 'value' => implode('; ', $meta_dim['description']['provenance'])];
            $metadados['description'] = ['id' => null, 'value' => $meta_dim['description']['resumo'][0]];
            $metadados['format'] = ['id' => null, 'value' => $meta_dim['format'][0]];
            $metadados['language'] = ['id' => null, 'value' => $meta_dim['language'][0]];
            $metadados['publisher'] = ['id' => null, 'value' => $meta_dim['publisher'][0]];
            $metadados['publisher_country'] = ['id' => null, 'value' => $meta_dim['publisher']['country'][0]];
            $metadados['publisher_initials'] = ['id' => null, 'value' => $meta_dim['publisher']['initials'][0]];
            $metadados['publisher_program'] = ['id' => null, 'value' => $meta_dim['publisher']['program'][0]];
            $metadados['publisher_department'] = ['id' => null, 'value' => $meta_dim['publisher']['department'][0]];
            $metadados['rights'] = ['id' => null, 'value' => $meta_dim['rights'][0]];
            $metadados['rights_uri'] = ['id' => null, 'value' => $meta_dim['rights']['uri'][0]];
            $metadados['subject_cnpq'] = ['id' => null, 'value' => $meta_dim['subject']['cnpq'][0]];
            unset($meta_dim['subject']['cnpq']);
            $metadados['subject'] = ['id' => null, 'value' => implode('; ', $meta_dim['subject'])];
            $metadados['title'] = ['id' => null, 'value' => $meta_dim['title'][0]];
            $metadados['title_alternative'] = ['id' => null, 'value' => $meta_dim['title']['alternative'][0]];
            $metadados['dspace_type'] = ['id' => null, 'value' => $meta_dim['type'][0]];
            $metadados['thumbnail'] = ['id' => null, 'value' => $meta_dim['thumbnail']['url'][0]];

            $fileSec = (isset($xml->fileSec) ? $xml->fileSec->fileGrp : null);

            if ($metadados['title']['value'] != null) {
                //Insere o Item
                $inserted_item_id = $this->create_simple_item($metadados['title']['value'], $id, $parent_collection_id);
                if ($inserted_item_id) {
                    //Insere Metadados
                    foreach ($metadados as $key => $value) {
                        if($key=='subject'){
                            $subjects = explode(';', $value);
                            $term = $this->checkIfMetadataExists($key, $parent_collection_id);
                            if ($term) {
                                //insere o valor no metadado encontrado
                                $meta_id = $term->term_id;
                                $parent_id = get_term_meta($meta_id, 'socialdb_property_term_root', true);
                            } else {
                                //cria o metadado e insere o valor
                                $category_root_id = $this->get_category_root_of($parent_collection_id);
                                $parent_id = $this->add_property_term( $parent_collection_id, $category_root_id);
                            }
                            
                            foreach($subjects as $sub){
                                $nick = str_replace(' ', '', $sub);
                                $subject_root = get_term_by('slug', strtolower($nick).'_category_' . $collection_id, 'socialdb_category_type');
                                if(!$subject_root){
                                    $array = wp_insert_term($sub, 'socialdb_category_type', array('parent' => $parent_id,
                                                'slug' => strtolower($nick).'_category_' . $collection_id));
                                    add_term_meta($array['term_id'], 'socialdb_category_owner', get_current_user_id());
                                }else{
                                    $array['term_id'] = $subject_root->term_id;
                                }
                                 wp_set_object_terms($inserted_item_id, array((int)  $array['term_id']), 'socialdb_category_type',true);
                            }
                        }else if ($value['value'] != null) {
                            $term = $this->checkIfMetadataExists($key, $parent_collection_id);
                            if ($term) {
                                //insere o valor no metadado encontrado
                                $meta_id = $term->term_id;
                            } else {
                                //cria o metadado e insere o valor
                                $category_root_id = $this->get_category_root_of($parent_collection_id);
                                $meta_id = $this->add_property_data($key, $parent_collection_id, $category_root_id);
                            }
                            add_post_meta($inserted_item_id, 'socialdb_property_' . $meta_id, $value['value']);
                            $this->set_common_field_values($inserted_item_id, "socialdb_property_$meta_id", $value['value']);
                        }
                    }

                    //Insere Files
                    if ($fileSec != null) {
                        foreach ($fileSec as $file) {
                            $file_use = $file->attributes()->USE;
                            $file = $file->file->FLocat->attributes('http://www.w3.org/1999/xlink')->href;
                            if ($file_use != 'THUMBNAIL') {
                                //Insere como anexo do item
                                if (is_file($dir . $file)) {
                                    $logo_id = $this->insert_attachment_file($dir . $file, $inserted_item_id);
                                    add_post_meta($inserted_item_id, '_file_id', $logo_id);
                                    update_post_meta($inserted_item_id, 'socialdb_object_dc_type', 'other');
                                    update_post_meta($inserted_item_id, 'socialdb_object_content', get_attachment_link($id));
                                    $this->set_common_field_values($object_id, 'object_type','other');
                                }
                            } else if($file_use == 'THUMBNAIL'){
                                //E a Thumb do item
                                if (is_file($dir . $file)) {
                                    $logo_id = $this->insert_attachment_file($dir . $file, $inserted_item_id);
                                    set_post_thumbnail($inserted_item_id, $logo_id);
                                    update_post_meta($inserted_item_id, 'socialdb_object_dc_type', 'image');
                                    update_post_meta($inserted_item_id, 'socialdb_object_content', $logo_id);
                                    update_post_meta($object_id, 'socialdb_object_from','internal');
                                    $this->set_common_field_values($object_id, 'object_from','internal');
                                    $this->set_common_field_values($object_id, 'object_type','other');
                                }
                            }
                        }
                    }
                }
            }


            /* $metadados['name'] = ['id' => null, 'value' => (string) $mods->name->namePart];
              $metadados['dateAccessioned'] = ['id' => null, 'value' => (string) $mods->extension[0]->dateAccessioned];
              $metadados['dateAvailable'] = ['id' => null, 'value' => (string) $mods->extension[2]->dateAvailable];
              $metadados['dateIssued'] = ['id' => null, 'value' => (string) $mods->originInfo[0]->dateIssued];
              $metadados['publisher'] = ['id' => null, 'value' => (string) $mods->originInfo[1]->publisher];
              $metadados['relatedItem'] = ['id' => null, 'value' => (string) $mods->relatedItem->part->text];

              $identifier = array();
              foreach ($mods->identifier as $row) {
              $identifier[] = (string) $row;
              }

              $metadados['identifier'] = ['id' => null, 'value' => serialize($identifier)];
              $metadados['abstract'] = ['id' => null, 'value' => (string) $mods->abstract];
              $metadados['physicalDescription'] = ['id' => null, 'value' => (string) $mods->physicalDescription->form];
              $metadados['language'] = ['id' => null, 'value' => (string) $mods->language->languageTerm];
              $metadados['accessCondition'] = ['id' => null, 'value' => (string) $mods->accessCondition[0]];
              $metadados['license'] = ['id' => null, 'value' => (string) $mods->accessCondition[1]];

              $subject = array();
              foreach ($mods->subject as $sub) {
              $subject[] = (string) $sub->topic;
              }

              $metadados['subject'] = ['id' => null, 'value' => serialize($subject)];
              $metadados['title'] = ['id' => null, 'value' => (string) $mods->titleInfo[0]->title];
              $metadados['genre'] = ['id' => null, 'value' => (string) $mods->genre];
              $metadados['note'] = ['id' => null, 'value' => (string) $mods->note]; */

            //var_dump($metadados);
            //exit();
        }

        //var_dump($parent_collection_id, $col_id);
    }

    public function create_simple_item($title, $id, $collection_id) {
        $user_id = get_current_user_id();
        if ($user_id == 0 || is_wp_error($user_id)) {
            $user_id = get_option('anonimous_user');
        }
        $post = array(
            'post_title' => $title,
            'post_status' => 'publish',
            'post_author' => $user_id,
            'post_type' => 'socialdb_object'
        );
        $object_id = wp_insert_post($post);
        $this->set_common_field_values($object_id, 'title', $title);
        if ($object_id) {
            update_post_meta($object_id, 'socialdb_dspace_aip_import_id', $id);

            $category_root_id = $this->get_category_root_of($collection_id);
            wp_set_object_terms($object_id, array((int) $category_root_id), 'socialdb_category_type');

            return $object_id;
        } else {
            return false;
        }
    }

    function checkIfMetadataExists($metadado, $collection_id) {
        $term = get_term_by('slug', $metadado . '_' . $collection_id, 'socialdb_property_type');
        if ($term) {
            return $term;
        } else {
            return false;
        }
    }

    function arrange_meta_array($arr, $sub_level = true) {
        if (is_array($arr)) {
            $result = array();
            foreach ($arr as $row) {
                if ($sub_level) {
                    foreach ($row as $value) {
                        $result[] = $value;
                    }
                } else {
                    $result[] = $row;
                }
            }
            return $result;
        } else {
            return array();
        }
    }

    /**
     * function add_property_data($property)
     * @param object $property
     * @return int O id da da propriedade criada.
     * @author: Eduardo Humberto 
     */
    public function add_property_data($name, $collection_id, $category_root_id) {
        $new_property = wp_insert_term((string) $name, 'socialdb_property_type', array('parent' => $this->get_property_type_id('socialdb_property_data'),
            'slug' => $name . '_' . $collection_id));
        update_term_meta($new_property['term_id'], 'socialdb_property_required', false);
        update_term_meta($new_property['term_id'], 'socialdb_property_data_widget', 'text');
        update_term_meta($new_property['term_id'], 'socialdb_property_default_value', '');
        update_term_meta($new_property['term_id'], 'socialdb_property_created_category', $category_root_id);
        add_term_meta($category_root_id, 'socialdb_category_property_id', $new_property['term_id']);
        return $new_property['term_id'];
    }
    
     /**
     * function add_property_term($property)
     * @param object $property
     * @return int O id da da propriedade criada.
     * @author: Eduardo Humberto 
     */
   public function add_property_term($collection_id,$category_root_id) {
        $subject_root = get_term_by('slug', 'subject_category_' . $collection_id, 'socialdb_category_type');
        if(!$subject_root){
            $array = wp_insert_term(__('Subject','tainacan'), 'socialdb_category_type', array('parent' => $this->get_category_root_id(),
                        'slug' => 'subject_category_' . $collection_id));
            add_term_meta($array['term_id'], 'socialdb_category_owner', get_current_user_id());
        }else{
            $array['term_id'] = $subject_root->term_id;
        }
        $new_property = wp_insert_term(__('Subject','tainacan'), 'socialdb_property_type', array('parent' => $this->get_property_type_id('socialdb_property_term'),
                'slug' =>'subject_' . $collection_id));
        
        update_term_meta($new_property['term_id'], 'socialdb_property_term_cardinality', '1');
        update_term_meta($new_property['term_id'], 'socialdb_property_term_widget',  'tree');
        update_term_meta($new_property['term_id'], 'socialdb_property_term_root',$array['term_id']);  
        update_term_meta($new_property['term_id'], 'socialdb_property_created_category',$category_root_id);
        add_term_meta($category_root_id, 'socialdb_category_property_id', $new_property['term_id']);
        return $array['term_id'];
   }

    /**
     * function get_property_type_id($property_parent_name)
     * @param string $property_parent_name
     * @return int O id da categoria que determinara o tipo da propriedade.
     * @author: Eduardo Humberto 
     */
    public function get_property_type_id($property_parent_name) {
        $property_root = get_term_by('name', $property_parent_name, 'socialdb_property_type');
        return $property_root->term_id;
    }

    /**
     * 
     * @return type
     */
    public function get_info_import_aip($data) {
        $save_data = unserialize(get_option('socialdb_aip_importation'));
        if (!$save_data || !is_array($save_data)) {
            $save_data['count_communities'] = 0;
            $save_data['count_collections'] = 0;
            $save_data['count_items'] = 0;
            $save_data['folder'] = '';
        } else {
            $data['total_community'] = $save_data['count_communities'];
            $data['total_collection'] = $save_data['count_collections'];
            $data['total_item'] = $save_data['count_items'];
        }
        $return['total_community'] = (!isset($data['total_community'])) ? $save_data['count_communities'] : $data['total_community'];
        $return['total_collection'] = (!isset($data['total_collection'])) ? $save_data['count_collections'] : $data['total_collection'];
        $return['total_item'] = (!isset($data['total_item'])) ? $save_data['count_items'] : $data['total_item'];
        $return['total'] = $return['total_community'] + $return['total_collection'] + $return['total_item'];
        $return['found_community'] = $return['total_community'] - ($this->search_files_name_import('COMMUNITY@', $save_data));
        $return['found_collection'] = $return['total_collection'] - ($this->search_files_name_import('COLLECTION@', $save_data));
        $return['found_item'] = $return['total_item'] - ($this->search_files_name_import('ITEM@', $save_data));
        $return['exported'] = $return['found_community'] + $return['found_collection'] + $return['found_item'];
        $return['percent'] = ($return['total'] > 0) ? ($return['exported'] / $return['total']) * 100 : 0;
        if ($return['exported'] >= $return['total'] && $return['exported'] != 0 && $return['total'] != 0) {
            $return['close'] = true;
        }
        return json_encode($return);
    }

    /**
     * 
     * @param type $name
     * @return int
     */
    public function search_files_name_import($name, $save_data) {
        $index = 0;
        $dir = $save_data['folder'];
        if (is_dir($dir) && ($save_data['count_communities'] > 0 || $save_data['count_collections'] > 0 || $save_data['count_items'] > 0)) {
            foreach (glob("{$dir}/*.zip") as $file) {
                if (strpos($file, $name) !== false) {
                    $index++;
                }
            }
        }
        return $index;
    }

}
