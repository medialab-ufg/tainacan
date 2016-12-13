<?php

ini_set('max_input_vars', '10000');

require_once(dirname(__FILE__) . '../../../models/import/import_model.php');
require_once(dirname(__FILE__) . '../../../models/import/csv_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class CsvController extends Controller {

    public function operation($operation, $data) {
        $csv_model = new CsvModel();

        switch ($operation) {
            case "show_import_configuration":
                return $this->render(dirname(__FILE__) . '../../../views/import/import_configuration.php');
                break;

            case "generate_selects":
                return $oaipmh_model->generate_selects($data);
                break;

            case "validate_csv":
                if (isset($data['file']['csv_file']['name'])) {
                    $type = pathinfo($data['file']['csv_file']['name']);
                    if ($type['extension'] == 'zip') {
                        $data = $csv_model->validate_zip($data['file']['csv_file'], $data);
                        if ($data['error'] > 0) {
                            return json_encode($data);
                        } else {
                            unset($data['file']);
                            return $this->render(dirname(__FILE__) . '../../../views/import/csv/maping_attributes.php', $data);
                        }
                    } else if ($type['extension'] == 'csv') {
                        $data = $csv_model->validate_csv($data['file']['csv_file'], $data);
                        if ($data['error'] > 0) {
                            return json_encode($data);
                        } else {
                            unset($data['file']);
                            return $this->render(dirname(__FILE__) . '../../../views/import/csv/maping_attributes.php', $data);
                        }
                    }
                } else {
                    $data['msg'] = "Envie algum arquivo para importar!";
                    $data['error'] = 1;
                    return json_encode($data);
                }
                break;
            case "do_import_csv":
                $data = $csv_model->do_import_csv($data);

                return json_encode($data);
            // case 'saving_data':
            // return json_encode($oaipmh_model->saving_data($data));
            case 'import_list_set':
                $oaipmh_model->import_list_set($data['url'], $data['collection_id']);
                return true;
            case 'import_full_csv':
                $dir = $csv_model->unzip_csv_package($data);
                if ($dir) {
                    $csv_files = array_values(array_diff(scandir($dir), array('..', '.')));
                    $csv_model->import_csv_full($csv_files, $dir);
                    $arr['result'] = true;
                } else {
                    $arr['result'] = false;
                }
                return json_encode($arr);
        }
    }

}

/*
 * Controller execution
 */
if ($_POST['operation']) {
    $operation = $_POST['operation'];
    $data = $_POST;
    $data['file'] = $_FILES;
} else {
    $operation = $_GET['operation'];
    $data = $_GET;
    $data['file'] = $_FILES;
}

$csv_controller = new CsvController();
echo $csv_controller->operation($operation, $data);
?>