<?php

ini_set('max_input_vars', '10000');
/**
 * Author: Eduardo Humberto
 */
require_once(dirname(__FILE__) . '../../../models/mapping/mapping_model.php');
require_once(dirname(__FILE__) . '../../../models/mapping/extract_metadata_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');
require_once(dirname(__FILE__) . '../../../models/import/oaipmh_model.php');
require_once(dirname(__FILE__) . '../../../models/export/export_model.php');

class MappingController extends Controller {

    public function operation($operation, $data) {
        $mapping_model = new MappingModel('socialdb_channel_oaipmhdc');
        $extract_model = new ExtractMetadataModel;
        $oaipmh_model = new OAIPMHModel();
        switch ($operation) {
            case "saving_mapping_oaipmh_dc_repository":
                parse_str($data['form'], $form); // parseio o formulario de mapeiamento de entidades
                $data['collection_id'] = $form['collection_id'];
                return $mapping_model->saving_mapping_dublin_core($data);
                break;
            case "saving_mapping_oaipmh_dc":
                return $mapping_model->saving_mapping_dublin_core($data);
                break;
            case "saving_mapping_oaipmh_dc_export":
                return $mapping_model->saving_mapping_dublin_core_export($data);
                break;
            case 'create_mapping_metatags':
                $metadata = $extract_model->extract_metatags($data['url']);
                $data['mapping_array'] = $metadata;
                return $this->render(dirname(__FILE__) . '../../../views/import/metatags/mapping_attributes.php', $data);
            case "save_mapping_metatags":
                return json_encode($mapping_model->save_mapping_metatags($data));
                break;
            case "updating_mapping_metatags":
                return $mapping_model->updating_mapping_metatags($data);
                break;
            case "updating_mapping_oaipmh_dc":
                return $mapping_model->updating_mapping_dublin_core($data);
                break;
            case "updating_mapping_oaipmh_dc_export":
                return $mapping_model->updating_mapping_dublin_core_export($data);
                break;
            case 'list_mapping_oaipmh_dc':
                return json_encode($mapping_model->list_mapping_dublin_core($data['collection_id']));
                break;
            case 'list_mapping_metatag':
                return json_encode($mapping_model->list_mapping_metatag($data['collection_id']));
                break;
            case "update_date":
                return update_post_meta($data['mapping_id'], 'socialdb_channel_oaipmhdc_last_update',tainacan_time());
                break;
            case "delete_mapping":
                return json_encode($mapping_model->delete_mapping($data['mapping_id'], $data['collection_id']));
                break;
            case "edit_mapping_metatags":
                $mapping_id = $data['mapping_id'];
                $data['mapping_id'] = $mapping_id;
                $data['mapping_array'] = $mapping_model->get_mapping_dublin_core($mapping_id);
                return $this->render(dirname(__FILE__) . '../../../views/import/metatags/edit_maping_attributes.php', $data);
            case "edit_mapping_oaipmh":
                $mapping_id = $data['mapping_id'];
                $set = get_post_meta($mapping_id, 'socialdb_channel_oaipmhdc_sets', true);
                if ($set !== '' && $set) {
                    $data['sets'] = $set;
                }
                $data = $oaipmh_model->validate_url($data);
                $data['mapping_id'] = $mapping_id;
                $data['mapping_array'] = $mapping_model->get_mapping_dublin_core($mapping_id);
                return $this->render(dirname(__FILE__) . '../../../views/import/oaipmh/edit_maping_attributes.php', $data);
            case "edit_mapping_oaipmh_repository":
                $mapping_id = $data['mapping_id'];
                $set = get_post_meta($mapping_id, 'socialdb_channel_oaipmhdc_sets', true);
                if ($set !== '' && $set) {
                    $data['sets'] = $set;
                }
                $data = $oaipmh_model->validate_url($data);
                $data['mapping_id'] = $mapping_id;
                $data['mapping_array'] = $mapping_model->get_mapping_dublin_core($mapping_id);
                return $this->render(dirname(__FILE__) . '../../../views/theme_options/oaipmh/edit_maping_attributes.php', $data);
            case "edit_mapping_oaipmh_default":
                //insiro o mapeamento
                $has_mapping = get_post_meta($data['collection_id'], 'socialdb_collection_mapping_import_active', true);
                if (!is_numeric($has_mapping)) {
                    $mapping_id = $this->create_mapping(__('Mapping Default', 'tainacan'), $data['collection_id']);
                    add_post_meta($mapping_id, 'socialdb_channel_oaipmhdc_initial_size', '1');
                    add_post_meta($mapping_id, 'socialdb_channel_oaipmhdc_mapping', serialize([]));
                    update_post_meta($data['collection_id'], 'socialdb_collection_mapping_import_active', $object_id);
                } else {
                    $mapping_id = $has_mapping;
                }
                //data
                $data['number_of_objects'] = 1;
                $data['mapping_id'] = $mapping_id;
                $data['mapping_array'] = $mapping_model->get_mapping_dublin_core($mapping_id);
                return $this->render(dirname(__FILE__) . '../../../views/import/oaipmh/edit_maping_attributes.php', $data);
                break;
            case "edit_mapping_oaipmh_export":
                $export_model = new ExportModel;
                $data = $export_model->create_new_mapping($data);
                return $this->render(dirname(__FILE__) . '../../../views/export/oaipmh/edit_maping_attributes.php', $data);
                break;
            case 'get_mapping':
                return json_encode($mapping_model->get_mapping_dublin_core($data['mapping_id']));
            case 'get_mapping_social_network':
                return json_encode($mapping_model->get_mapping_social_network($data['mapping_id'], $data['term']));
            case 'get_mapping_csv':
                return json_encode($mapping_model->get_mapping_csv($data['mapping_id']));
            case 'is_harvesting':
                return $mapping_model->is_harvesting($data);
            case 'saving_delimiter_header_csv':
                //parse_str($data['form'], $form);
                //var_dump($data, $data['file'], $_FILES);
                //exit();
                $mapping_id = $data['socialdb_csv_mapping_id'];
                $delimiter = ($data['socialdb_delimiter_csv'] == '' ? ';' : $data['socialdb_delimiter_csv']);
                $multi_values = ($data['socialdb_delimiter_multi_values_csv'] == '' ? '||' : $data['socialdb_delimiter_multi_values_csv']);
                $hierarchy = ($data['socialdb_delimiter_hierarchy_csv'] == '' ? '::' : $data['socialdb_delimiter_hierarchy_csv']);
                $import_url_external = ($data['import_url_external']&&$data['import_url_external']=='url_externa') ? $data['import_url_external'] : 'false';
                $csv_has_header = $data['socialdb_csv_has_header'];
                $code = $data['socialdb_delimiter_code_csv'];
                if($_FILES && isset($_FILES['csv_file'])){
                    foreach ($_FILES as $file => $array) {
                        if (!empty($_FILES[$file]["name"])) {
                            $_FILES[$file]["name"] = remove_accent($_FILES[$file]["name"]);
                            delete_post_meta($mapping_id, '_file_id');
                            $newupload = $mapping_model->insert_attachment($file, $mapping_id);
                        }
                    }
                }                
                $mapping_model->save_delimiter_csv($mapping_id, $delimiter, $multi_values, $hierarchy, $import_url_external, $csv_has_header,$code);
                $files = $mapping_model->show_files_csv($mapping_id);
                foreach ($files as $file) {
                    //$name_file =  wp_get_attachment_link($file->ID, 'thumbnail', false, true);
                    $name_file = wp_get_attachment_url($file->ID);
                    $type = pathinfo($name_file);
                    if($type['extension']=='zip'){
                       $data['csv_data'] = $mapping_model->get_csv_in_zip_file($name_file, $delimiter);
                    }else{
                        $objeto = fopen($name_file, 'r');
                        if(strpos($name_file, 'socialdb_csv')!==false && strpos($name_file, 'tainacan_csv')!==false)
                            $csv_data = fgetcsv($objeto, 0, $delimiter);
                        while(($csv_data = fgetcsv($objeto, 0, $delimiter)) !== false){
                             $data['csv_data'] = $csv_data;
                             break;
                        }
                    }
                    break;
                }
                $data['mapping_id'] = $mapping_id;
                unset($data['file']);
                //var_dump($data);
                if($data['create_metadata_column_name']&&$data['create_metadata_column_name']=='true'&&$type['extension']=='csv'&&$csv_has_header=='1'){
                    update_post_meta($mapping_id, 'socialdb_channel_csv_add_columns', 'true');
                    update_post_meta($mapping_id, 'socialdb_channel_csv_column_title', ($data['map_title_metadata']!='') ? $data['map_title_metadata']: '');
                    return "false";
                }
                if(isset($data['socialdb_csv_is_editting']))
                    return $this->render(dirname(__FILE__) . '../../../views/import/csv/edit_mapping.php', $data);
                else
                    return $this->render(dirname(__FILE__) . '../../../views/import/csv/add_mapping.php', $data);
                break;
            /* case 'saving_delimiter_header_csv':
              //parse_str($data['form'], $form);
              var_dump($data,$data['file'], $_FILES);
              exit();
              $mapping_id = $form['socialdb_csv_mapping_id'];
              $delimiter = ($form['socialdb_delimiter_csv'] == '' ? ';' : $form['socialdb_delimiter_csv']);
              $csv_has_header = $form['socialdb_csv_has_header'];
              $mapping_model->save_delimiter_csv($mapping_id, $delimiter, $csv_has_header);
              $files = $mapping_model->show_files_csv($mapping_id);
              foreach ($files as $file) {
              //$name_file =  wp_get_attachment_link($file->ID, 'thumbnail', false, true);
              $name_file = wp_get_attachment_url($file->ID);
              $objeto = fopen($name_file, 'r');
              $data['csv_data'] = fgetcsv($objeto, 0, $delimiter);
              break;
              }
              $data['mapping_id'] = $mapping_id;
              return $this->render(dirname(__FILE__) . '../../../views/import/csv/add_mapping.php', $data); */
            case 'saving_mapping_csv':
                return $mapping_model->saving_mapping_csv($data);
            case 'list_mapping_csv':
                return json_encode($mapping_model->list_mapping_csv($data['collection_id']));
                break;
            case "edit_headers_mapping_csv":
                $data['socialdb_channel_csv_delimiter'] = get_post_meta($data['mapping_id'], 'socialdb_channel_csv_delimiter', true);
                $data['socialdb_channel_csv_multi_values'] = get_post_meta($data['mapping_id'], 'socialdb_channel_csv_multi_values', true);
                $data['socialdb_channel_csv_hierarchy'] = get_post_meta($data['mapping_id'], 'socialdb_channel_csv_hierarchy', true);
                $data['socialdb_channel_csv_import_zip'] = get_post_meta($data['mapping_id'], 'socialdb_channel_csv_import_zip', true);
                $data['socialdb_channel_csv_has_header'] = get_post_meta($data['mapping_id'], 'socialdb_channel_csv_has_header', true);
                $data['socialdb_channel_csv_code'] = get_post_meta($data['mapping_id'], 'socialdb_channel_csv_code', true);
                 return $this->render(dirname(__FILE__) . '../../../views/import/csv/edit_maping_attributes.php', $data);
            case "edit_mapping_csv":
                $mapping_id = $data['mapping_id'];
                $delimiter = get_post_meta($mapping_id, 'socialdb_channel_csv_delimiter', true);
                $files = $mapping_model->show_files_csv($mapping_id);
                foreach ($files as $file) {
                    //$name_file =  wp_get_attachment_link($file->ID, 'thumbnail', false, true);
                    $name_file = wp_get_attachment_url($file->ID);
                    $objeto = fopen($name_file, 'r');
                    $csv_data = fgetcsv($objeto, 0, $delimiter);
                    while(($csv_data = fgetcsv($objeto, 0, $delimiter)) !== false){
                        break;
                    }
                    $data['csv_data'] = $csv_data;
                    break;
                }
                return $this->render(dirname(__FILE__) . '../../../views/import/csv/edit_mapping.php', $data);
                break;
            case "updating_mapping_csv":
                return $mapping_model->updating_mapping_csv($data);
                break;
            case "updating_social_mapping":
                return $mapping_model->updating_social_mapping($data);
                break;
            //oaipmh EXPORT
            case 'form_default_mapping':
                return json_encode($mapping_model->set_active_mapping($data));
            /*             * *************************************************************** */
            /*    EXTRACAO DE METADADOS HANDLE                                */
            /*             * *************************************************************** */
            case 'get_metadata_handle':
                $has_mapping = get_post_meta($data['collection_id'], 'socialdb_collection_mapping_import_active', true);
                $is_mapped = (is_numeric($has_mapping)) ? unserialize(get_post_meta($has_mapping, 'socialdb_channel_oaipmhdc_mapping', true)) : false;
                if (!$has_mapping || $has_mapping == '' || (!$is_mapped || count($is_mapped) === 0)):
                    $data['base'] = 'http://' . $data['url'] . '/oai/request';
                    $data['generic_properties'] = $extract_model->get_metadata_handle($data);
                    if (!$data['generic_properties']) {
                        $data['hasMapping'] = false;
                        return json_encode($data);
                    }
                    $data['tainacan_properties'] = $extract_model->get_tainacan_properties($data);
                    $data['oai_url'] = $extract_model->get_link_data_handle($data);
                    $data['html'] = $this->render(dirname(__FILE__) . '../../../views/mapping/container_mapping.php', $data);
                elseif (is_numeric($has_mapping) && count($is_mapped) > 0):
                    $url = $extract_model->get_link_data_handle($data);
                    $mapp_array = $oaipmh_model->get_mapping_oaipmh_dc($has_mapping);
                    $record_value = (isset($extract_model->get_record_oaipmh($url)['records'])) ? $extract_model->get_record_oaipmh($url)['records'][0] : [];
                    if (!$record_value) {
                        $data['hasMapping'] = false;
                        return json_encode($data);
                    }
                    $data['object_id'] = $extract_model->insert_item_handle($mapp_array, $data['collection_id'], $record_value);
                    $data['hasMapping'] = true;
                endif;
                return json_encode($data);
            case 'submit_mapping_handle':
                if ($data['url_oai']):
                    $mapping_id = $mapping_model->saving_mapping_handle($data);
                    $mapp_array = $oaipmh_model->get_mapping_oaipmh_dc($mapping_id);
                    $record_value = (isset($extract_model->get_record_oaipmh($data['url_oai'])['records'])) ? $extract_model->get_record_oaipmh($data['url_oai'])['records'][0] : [];
                    if (!$record_value) {
                        $data['result'] = false;
                        return json_encode($data);
                    }
                    $data['object_id'] = $extract_model->insert_item_handle($mapp_array, $data['collection_id'], $record_value);
                    $data['result'] = true;
                else:
                    $data['result'] = false;
                endif;
                return json_encode($data);
            /*             * *************************************************************** */
            /*    EXTRACAO DE METADADOS  OJS                                  */
            /*             * *************************************************************** */
            case 'get_metadata_ojs':
                $has_mapping = get_post_meta($data['collection_id'], 'socialdb_collection_mapping_import_active', true);
                $is_mapped = (is_numeric($has_mapping)) ? unserialize(get_post_meta($has_mapping, 'socialdb_channel_oaipmhdc_mapping', true)) : false;
                if (!$has_mapping || $has_mapping == '' || (!$is_mapped || count($is_mapped) === 0)):
                    $data['base'] = 'http://' . $data['url'] . 'oai';
                    $data['generic_properties'] = $extract_model->get_metadata_handle($data, true); // true pois diferencia a url
                    if (!$data['generic_properties']) {
                        $data['hasMapping'] = false;
                        return json_encode($data);
                    }
                    $data['tainacan_properties'] = $extract_model->get_tainacan_properties($data);
                    $data['oai_url'] = $extract_model->get_link_data_handle($data, true);
                    $data['html'] = $this->render(dirname(__FILE__) . '../../../views/mapping/container_mapping.php', $data);
                elseif (is_numeric($has_mapping) && count($is_mapped) > 0):
                    $url = $extract_model->get_link_data_handle($data, true);
                    $mapp_array = $oaipmh_model->get_mapping_oaipmh_dc($has_mapping);
                    $record_value = (isset($extract_model->get_record_oaipmh($url)['records'])) ? $extract_model->get_record_oaipmh($url)['records'][0] : [];
                    if (!$record_value) {
                        $data['hasMapping'] = false;
                        return json_encode($data);
                    }
                    $data['object_id'] = $extract_model->insert_item_handle($mapp_array, $data['collection_id'], $record_value);
                    $data['hasMapping'] = true;
                endif;
                return json_encode($data);
            /*             * *************************************************************** */
            /*    EXTRACAO DE METADADOS - METATAGS                            */
            /*             * *************************************************************** */
            case 'get_metadata_metatags':
                //verifico se ja existe o mapeamento para este dominiuo
                $has_mapping = $mapping_model->get_mapping_metatags($data['url'], $data['collection_id']);
                if (!$has_mapping || $has_mapping == ''):
                    //extraio todos os metatadados
                    $data['generic_properties'] = $extract_model->extract_metatags($data['url']); // true pois diferencia a url
                    if (!$data['generic_properties']) {
                        $data['hasMapping'] = false;
                        return json_encode($data);
                    }
                    //busco os metadados da colecao
                    $data['tainacan_properties'] = $extract_model->get_tainacan_properties($data);
                    $data['html'] = $this->render(dirname(__FILE__) . '../../../views/mapping/container_mapping_metatags.php', $data);
                elseif (is_numeric($has_mapping)):
                    $mapp_array = $oaipmh_model->get_mapping_oaipmh_dc($has_mapping);
                    $record = $extract_model->get_record_metatags($data['url'])['records'][0];
                    $record_value = (isset($record)) ? $record : [];
                    if (!$record_value) {
                        $data['hasMapping'] = false;
                        return json_encode($data);
                    }
                    $data['object_id'] = $extract_model->insert_item_handle($mapp_array, $data['collection_id'], $record_value);
                    $data['hasMapping'] = true;
                endif;
                return json_encode($data);
            case 'submit_mapping_metatags':
                if ($data['url']):
                    //crio o mapeamento
                    $mapping_id = $mapping_model->saving_mapping_metatags($data);
                    //busco os valores mapeados
                    $mapp_array = $oaipmh_model->get_mapping_oaipmh_dc($mapping_id);
                    //array com os valores a ser inserido
                    $record = $extract_model->get_record_metatags($data['url'])['records'][0];
                    $record_value = (isset($record)) ? $record : [];
                    if (!$record_value) {
                        $data['result'] = false;
                        return json_encode($data);
                    }
                    $data['object_id'] = $extract_model->insert_item_handle($mapp_array, $data['collection_id'], $record_value);
                    $data['result'] = true;
                else:
                    $data['result'] = false;
                endif;
                return json_encode($data);
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

$mapping_controller = new MappingController();
echo $mapping_controller->operation($operation, $data);
