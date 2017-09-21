<?php

ini_set('max_input_vars', '10000');

require_once(dirname(__FILE__) . '../../../models/import/import_model.php');
require_once(dirname(__FILE__) . '../../../models/import/csv_model.php');
require_once(dirname(__FILE__) . '../../../models/mapping/mapping_model.php');
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
                $targetdir = $csv_model->unzip_csv_package($data);
                if ($targetdir) {
                    if (is_dir($targetdir . DIRECTORY_SEPARATOR . 'collections')) {
                        $dir = $targetdir . DIRECTORY_SEPARATOR . 'collections';
                        $csv_files = array_values(array_diff(scandir($dir), array('..', '.')));
                        $csv_model->import_csv_full($csv_files, $dir);
                        $csv_model->recursiveRemoveDirectory($targetdir);
                        $arr['result'] = true;
                    } else {
                        $csv_model->recursiveRemoveDirectory($targetdir);
                        $arr['result'] = false;
                    }
                } else {
                    $arr['result'] = false;
                }
                return json_encode($arr);
            case 'getHeaderCSV':
                $mapping_model =  new MappingModel('socialdb_channel_csv');
                $files = $mapping_model->show_files_csv($data['mapping_id']);
                foreach ($files as $file) {
                    return json_encode($this->get_header_csv_file($file->guid, $data['delimiter']));
                }
                return json_encode([]);
                break;
        }
    }
    
    /**
     * 
     * @param type $file_name
     */

    public function get_header_csv_file($file_name, $delimiter) {
        $objeto = fopen($file_name, 'r');
        $count = 0;
        $lines = array();
        $lines_final = array();
        // LEITURA DO ARQUIVO
        //$time_before_read = microtime() - $time_start;
        while (($csv_data = fgetcsv($objeto, 0, $delimiter)) !== false) {
            $count++;
            if(empty(array_filter($csv_data))){
                continue; 
            }
            $lines = $csv_data;
            break;
        }
        foreach ($lines as $value) {
            if(mb_detect_encoding($value, 'auto')=='UTF-8'){
                $lines_final[] = iconv("Windows-1252","UTF-8" , $value);
            }else{
                $lines_final[] = $value;
            }
        }
        return array_filter($lines_final);
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