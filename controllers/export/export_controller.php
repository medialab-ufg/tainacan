<?php

ini_set('max_input_vars', '10000');

require_once(dirname(__FILE__) . '../../../models/export/export_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class ExportController extends Controller {

    public function operation($operation, $data) {
        $export_model = new ExportModel;

        switch ($operation) {
            case "index_export":
                return $this->render(dirname(__FILE__) . '../../../views/export/index_export.php');
                break;
            case 'create_new_mapping':
                $data = $export_model->create_new_mapping($data);
                return $this->render(dirname(__FILE__) . '../../../views/export/oaipmh/mapping_attributes.php', $data);
                break;
            case "generate_selects":
                return $export_model->generate_selects($data);
                break;
            case "export_csv_file":
                $data['socialdb_delimiter_csv'] = trim($data['socialdb_delimiter_csv']);
                if ($data['socialdb_delimiter_csv'] != '') {
                    $csv_data = $export_model->generate_csv_data($data);

                    $export_model->download_send_headers('socialdb_csv.csv');
                    echo $export_model->array2csv($csv_data, $data['socialdb_delimiter_csv']);
                } else {
                    wp_redirect(get_the_permalink($data['collection_id']) . '?info_title=Attention&info_messages=' . urlencode(__('Please, fill the delimiter correctly!','tainacan')));
                }
                break;
            case "export_selected_objects":
                $data['loop'] = $export_model->get_selected_objects($data);
                $csv_data = $export_model->generate_csv_data_selected($data);
                $export_model->download_send_headers('socialdb_csv.csv');
                echo $export_model->array2csv($csv_data);
                break;
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

$export_controller = new ExportController();
echo $export_controller->operation($operation, $data);