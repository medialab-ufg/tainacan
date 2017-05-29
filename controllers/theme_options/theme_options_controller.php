<?php
ini_set('max_input_vars', '10000');
require_once(dirname(__FILE__) . '../../../models/theme_options/theme_options_model.php');
require_once(dirname(__FILE__) . '../../../models/theme_options/populate_model.php');
require_once(dirname(__FILE__) . '../../../models/collection/collection_templates_model.php');
require_once(dirname(__FILE__) . '../../../models/theme_options/export_aip_model.php');
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
                Log::addLog(['user_id' => get_current_user_id(), 'event_type' => 'admin', 'event' => 'config']);
                return $this->render(dirname(__FILE__) . '../../../views/theme_options/edit_configuration.php', $data);
                break;
            case "edit_welcome_email":
                $data['socialdb_welcome_email'] = get_option('socialdb_welcome_email');
                Log::addLog(['user_id' => get_current_user_id(), 'event_type' => 'admin', 'event' => 'welcome_mail']);
                return $this->render(dirname(__FILE__) . '../../../views/theme_options/edit_email.php', $data);
                break;
            case 'updates_page':
                return $this->render(dirname(__FILE__) . '../../../views/theme_options/updates_page.php', $data);
            case "update_options":
                return $theme_options_model->update($data);
                break;
            case "update_configuration":
                return $theme_options_model->update_configuration($data);
                break;
            case "update_welcome_email":
                return $theme_options_model->update_welcome_email($data);
                break;
            case "update_devolution_email_alert_content":
                return $theme_options_model->update_devolution_email_alert($data);
                break;
            case "edit_licenses":
                $data = $theme_options_model->get_theme_general_options_data();
                Log::addLog(['event_type' => 'admin', 'event' => 'licenses']);
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
                Log::addLog(['user_id' => get_current_user_id(), 'event_type' => 'admin', 'event' => 'tools']);
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
                     return json_encode([]);
                }
                break;
            case "import_dspace_aip":
                ini_set('max_execution_time', '0');
                error_reporting(E_ALL);
                $file = $data['file'];
                $verify = $theme_options_model->verify_aip_file($file);
                if ($verify) {
                    $unzip_path = $theme_options_model->unzip_aip_file($file);
                    $this->save_total_import_aip($unzip_path);
                    //site
                    if (file_exists($unzip_path . '/sitewide-aip.zip')) {
                        $unzip_site = $theme_options_model->unzip_aip_general($unzip_path . '/', 'sitewide-aip.zip');
                        $xml = (file_exists($unzip_site . '/mets.xml') ? simplexml_load_file($unzip_site . '/mets.xml') : null);
                        if ($xml != null) {
                            $theme_options_model->read_site_xml($xml);
                            $theme_options_model->recursiveRemoveDirectory($unzip_site);
                        }
                    } else {
                        return json_encode([ 'result'=>false ]);;
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
                    return json_encode([ 'result'=>true ]);
                } else {
                    return json_encode([ 'result'=>false ]);;
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
            /********************** Exportacao AIP ****************************/    
            case 'export_full_aip':
                $export_model = new ExportAIP;
                $export_model->export_aip_zip();
                break;
            case 'get_info_export_aip':
                $export_model = new ExportAIP;
                return $export_model->get_info_export_aip($data);
                break;
            case 'get_info_import_aip':
                return $theme_options_model->get_info_import_aip($data);
                break;
        }
    }
    
    public function save_total_import_aip($unzip_path){
        $array['folder'] = $unzip_path;
        $array['count_communities'] = 0;
        $array['count_collections'] = 0;
        $array['count_items'] = 0;
        $community_files = scandir($unzip_path);
        foreach ($community_files as $community_file) {
            if (strpos($community_file, 'COMMUNITY') !== false) {
                $array['count_communities']++;
            }
        }
        foreach ($community_files as $community_file) {
            if (strpos($community_file, 'COLLECTION') !== false) {
                $array['count_collections']++;
            }
        }
        foreach ($community_files as $community_file) {
            if (strpos($community_file, 'ITEM') !== false) {
                $array['count_items'] ++;
            }
        }
        update_option('socialdb_aip_importation', serialize($array));
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