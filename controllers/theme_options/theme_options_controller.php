<?php
ini_set('max_input_vars', '10000');
require_once(dirname(__FILE__) . '../../../models/theme_options/theme_options_model.php');
require_once(dirname(__FILE__) . '../../../models/theme_options/populate_model.php');
require_once(dirname(__FILE__) . '../../../models/collection/collection_templates_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class ThemeOptionsController extends Controller {

    public function operation($operation, $data) {
        $theme_options_model = new ThemeOptionsModel();
        switch ($operation) {
            case "edit_configuration":
                $data = $theme_options_model->get_theme_options_data();
                if (is_array($data)) {
                    return $this->render(dirname(__FILE__) . '../../../views/theme_options/edit.php', $data);
                } else {
                    return $this->render(dirname(__FILE__) . '../../../views/theme_options/edit.php');
                }
                break;
            case "edit_general_configuration":
                $collectioModelTemplates = new CollectionTemplatesModel;
                $data = $theme_options_model->get_theme_general_options_data();
                $data['templates'] = $collectioModelTemplates->get_collections_templates();
                return $this->render(dirname(__FILE__) . '../../../views/theme_options/edit_configuration.php', $data);
                break;
            case "edit_welcome_email":
                $data['socialdb_welcome_email'] = get_option('socialdb_welcome_email');
                return $this->render(dirname(__FILE__) . '../../../views/theme_options/edit_email.php', $data);
                break;
            case "update_options":
                return $theme_options_model->update($data);
                break;
            case "update_configuration":
                return $theme_options_model->update_configuration($data);
                break;
            case "update_welcome_email":
                return $theme_options_model->update_welcome_email($data);
                break;
            case "edit_licenses":
                $data = $theme_options_model->get_theme_general_options_data();
                $data = $theme_options_model->get_theme_general_options_data();
                return $this->render(dirname(__FILE__) . '../../../views/theme_options/licenses/edit.php', $data);
                break;
            case "listStandartLicenses":
                $arrLicenses = $theme_options_model->get_licenses('standart');
                return json_encode($arrLicenses);
                break;
            case "listCustomLicenses":
                $arrLicenses = $theme_options_model->get_licenses('custom');
                return json_encode($arrLicenses);
                break;
            case "add_repository_license":
                if ($data['add_license_url'] == '' && $data['add_license_description'] == ''):
                    $result['title'] = __('Error', 'tainacan');
                    $result['msg'] = __('Please, fill the form correctly!', 'tainacan');
                    $result['type'] = 'error';
                else:
                    if (!$theme_options_model->verify_equal_license_title($data['add_license_name'])):
                        $result['title'] = __('Info', 'tainacan');
                        $result['msg'] = __('This license is already registered!', 'tainacan');
                        $result['type'] = 'info';
                    else:
                        $result = $theme_options_model->insert_custom_license($data);
                    endif;
                endif;

                return json_encode($result);
                break;
            case "get_license_to_edit":
                $license = $theme_options_model->get_license_to_edit($data['license_id']);
                return json_encode($license);
                break;
            case "edit_repository_license":
                $result = $theme_options_model->edit_repository_license($data);
                return json_encode($result);
                break;
            case "delete_custom_license":
                $result = $theme_options_model->delete_repository_license($data['license_id']);
                return json_encode($result);
                break;
            case "change_pattern_license":
                $result = $theme_options_model->change_pattern_license($data['license_id']);
                return json_encode($result);
                break;
            /*             * ************************* POPULAR COLECOES********************** */
            case "edit_tools":
                return $this->render(dirname(__FILE__) . '../../../views/theme_options/edit_tools.php', $data);
                break;
            case "import_full":
                return $this->render(dirname(__FILE__) . '../../../views/theme_options/import_full.php', $data);
                break;
            case "list_aip_files":
                $files = $theme_options_model->getAipFiles();
                if (is_array($files)) {
                    return json_encode($files);
                } else {
                    return false;
                }
                break;
            case "import_dspace_aip":
                ini_set('max_execution_time', '0');
                $file = $data['file'];
                $verify = $theme_options_model->verify_aip_file($file);
                if ($verify) {
                    $unzip_path = $theme_options_model->unzip_aip_file($file);
                    //site
                    if (file_exists($unzip_path . '/sitewide-aip.zip')) {
                        $unzip_site = $theme_options_model->unzip_aip_general($unzip_path . '/', 'sitewide-aip.zip');
                        $xml = (file_exists($unzip_site . '/mets.xml') ? simplexml_load_file($unzip_site . '/mets.xml') : null);
                        if ($xml != null) {
                            $theme_options_model->read_site_xml($xml);
                            $theme_options_model->recursiveRemoveDirectory($unzip_site);
                        }
                        //var_dump($xml);
                    } else {
                        return false;
                    }
                    //community
                    $community_files = scandir($unzip_path);
                    foreach ($community_files as $community_file) {
                        if (strpos($community_file, 'COMMUNITY') !== false) {
                            $unzip_com = $theme_options_model->unzip_aip_general($unzip_path . '/', $community_file);
                            $xml = (file_exists($unzip_com . '/mets.xml') ? simplexml_load_file($unzip_com . '/mets.xml') : null);
                            if ($xml != null) {
                                $theme_options_model->read_community_xml($xml);
                                $theme_options_model->recursiveRemoveDirectory($unzip_com);
                            }
                        }
                    }
                    //collection
                    $collection_files = scandir($unzip_path);
                    foreach ($collection_files as $collection_file) {
                        if (strpos($collection_file, 'COLLECTION') !== false) {
                            $unzip_col = $theme_options_model->unzip_aip_general($unzip_path . '/', $collection_file);
                            $xml = (file_exists($unzip_col . '/mets.xml') ? simplexml_load_file($unzip_col . '/mets.xml') : null);
                            if ($xml != null) {
                                $theme_options_model->read_collection_xml($xml, $unzip_col . '/');
                                $theme_options_model->recursiveRemoveDirectory($unzip_col);
                            }
                        }
                    }
                    //item
                    $item_files = scandir($unzip_path);
                    foreach ($item_files as $item_file) {
                        if (strpos($item_file, 'ITEM') !== false) {
                            $unzip_item = $theme_options_model->unzip_aip_general($unzip_path . '/', $item_file);
                            $xml = (file_exists($unzip_item . '/mets.xml') ? simplexml_load_file($unzip_item . '/mets.xml') : null);
                            if ($xml != null) {
                                $theme_options_model->read_item_xml($xml, $unzip_item . '/');
                                $theme_options_model->recursiveRemoveDirectory($unzip_item);
                            }
                        }
                    }
                    $theme_options_model->recursiveRemoveDirectory($unzip_path);
                    return true;
                } else {
                    return false;
                }
                break;
            case "delete_aip_file":
                $result = $theme_options_model->delete_aip_file($data['file']);
                return $result;
                break;
            case "upload_aip_zip":
                //var_dump($data, $_FILES);
                $file = (isset($_FILES['aip_pkg']) ? $_FILES['aip_pkg'] : null);
                $result = $theme_options_model->upload_aip_file($file, $data);
                return $result;
                break;
            case "export_full":
                return $this->render(dirname(__FILE__) . '../../../views/theme_options/export_full.php', $data);
                break;
            case 'populate_collection':
                $populateModel = new PopulateModel($data['items_category']);
                return $populateModel->populate_collection($data);
            case 'getProgress':
                $populateModel = new PopulateModel(0);
                return $populateModel->getProgress($data);
            case 'integrity_test':
                $result = array();
//                $collections = $theme_options_model->get_all_collections();
//                foreach ($collections as $collection) {
//                    $posts = $theme_options_model->get_collection_posts($collection->ID);
//                    foreach ($posts as $post) {
//                        $files = $theme_options_model->list_files_attachment($post->ID);
//                        foreach ($files as $file) {
//                            $md5_atual = ($theme_options_model->is_url_exist($file["guid"]) ? md5_file($file["guid"]) : 'Not Found!');
//                            $result_test = ($file["md5_inicial"] == $md5_atual ? 'OK' : 'NOK');
//                            add_post_meta($file['ID'], 'check_md5_' . time(), $md5_atual);
//                            $info_file['id'] = $file["ID"];
//                            $info_file['title'] = $file["name"];
//                            $info_file['md5_inicial'] = $file["md5_inicial"];
//                            $info_file['md5_atual'] = $md5_atual;
//                            $info_file['result'] = $result_test;
//
//                            $result[] = $info_file;
//                        }
//                    }
//                }


                $files = $theme_options_model->get_all_attachments();
                if (!empty($files)) {
                    foreach ($files as $file) {
                        $file["md5_inicial"] = get_post_meta($file["ID"], 'md5_inicial', true);
                        if (!$file["md5_inicial"] || $file["md5_inicial"] == '') {
                            $md5_inicial = ($theme_options_model->is_url_exist($file["guid"]) ? md5_file($file["guid"]) : 'Not Found!');
                            update_post_meta($file["ID"], 'md5_inicial', $md5_inicial);
                            $file["md5_inicial"] = $md5_inicial;
                        }
                        $md5_atual = ($theme_options_model->is_url_exist($file["guid"]) ? md5_file($file["guid"]) : 'Not Found!');
                        $result_test = ($file["md5_inicial"] == $md5_atual ? 'OK' : 'NOK');
                        add_post_meta($file['ID'], 'check_md5_' . time(), $md5_atual);
                        $info_file['id'] = $file["ID"];
                        $info_file['title'] = $file["post_title"];
                        $info_file['md5_inicial'] = $file["md5_inicial"];
                        $info_file['md5_atual'] = $md5_atual;
                        $info_file['result'] = $result_test;

                        $result[] = $info_file;
                    }
                }
                return json_encode($result);
        }
    }

}

/*
 * Controller execution
 */
if ($_POST['operation']) {
    $operation = $_POST['operation'];
    $data = $_POST;
} else {
    $operation = $_GET['operation'];
    $data = $_GET;
}

$theme_options_controller = new ThemeOptionsController();
echo $theme_options_controller->operation($operation, $data);
?>