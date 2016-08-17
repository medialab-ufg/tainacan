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
            case "saving_mapping_oaipmh_dc":
                return $mapping_model->saving_mapping_dublin_core($data);
                break;
            case "saving_mapping_oaipmh_dc_export":
                return $mapping_model->saving_mapping_dublin_core_export($data);
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
            case "update_date":
                return update_post_meta($data['mapping_id'], 'socialdb_channel_oaipmhdc_last_update', mktime());
                break;
            case "delete_mapping":
                return json_encode($mapping_model->delete_mapping($data['mapping_id'], $data['collection_id']));
                break;
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
                parse_str($data['form'], $form);
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
                return $this->render(dirname(__FILE__) . '../../../views/import/csv/add_mapping.php', $data);
            case 'saving_mapping_csv':
                return $mapping_model->saving_mapping_csv($data);
            case 'list_mapping_csv':
                return json_encode($mapping_model->list_mapping_csv($data['collection_id']));
                break;
            case "edit_mapping_csv":
                $mapping_id = $data['mapping_id'];
                $delimiter = get_post_meta($mapping_id, 'socialdb_channel_csv_delimiter', true);
                $files = $mapping_model->show_files_csv($mapping_id);
                foreach ($files as $file) {
                    //$name_file =  wp_get_attachment_link($file->ID, 'thumbnail', false, true);
                    $name_file = wp_get_attachment_url($file->ID);
                    $objeto = fopen($name_file, 'r');
                    $data['csv_data'] = fgetcsv($objeto, 0, $delimiter);
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
            /******************************************************************/
            /*    EXTRACAO DE METADADOS                                       */ 
            /******************************************************************/
            case 'get_metadata_handle':
                $data['generic_properties'] = $extract_model->get_metadata_handle($data);
                $data['tainacan_properties'] = $extract_model->get_tainacan_properties($data);
               // var_dump($data);exit();
                $data['html'] = $this->render(dirname(__FILE__) . '../../../views/mapping/container_mapping.php',$data);
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
