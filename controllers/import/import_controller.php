<?php
ini_set('max_input_vars', '10000');

require_once(dirname(__FILE__) . '../../../models/import/import_model.php');
require_once(dirname(__FILE__) . '../../../models/import/oaipmh_model.php');
require_once(dirname(__FILE__) . '../../../models/import/oaipmh_repository_model.php');
require_once(dirname(__FILE__) . '../../../models/import/harvesting_oaipmh_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');
require_once(dirname(__FILE__) . '../../../models/export/export_model.php');

class ImportController extends Controller {

    public function operation($operation, $data) {
        $oaipmh_model = new OAIPMHModel();

        switch ($operation) {
            case "show_import_configuration":
                return $this->render(dirname(__FILE__) . '../../../views/import/import_configuration.php');
                break;
            
            case "generate_selects":
                return $oaipmh_model->generate_selects($data);
                break;
            
            case "validate_url":
               // $oaipmh_model->import_list_set($data['url'], $data['collection_id']);
                $data = $oaipmh_model->validate_url($data);
                return $this->render(dirname(__FILE__) . '../../../views/import/oaipmh/maping_attributes.php',$data);
                break;
            case "validate_url_repository":
               // $oaipmh_model->import_list_set($data['url'], $data['collection_id']);
                $data = $oaipmh_model->validate_url($data);
                return $this->render(dirname(__FILE__) . '../../../views/theme_options/oaipmh/mapping_attributes.php',$data);
                break;
            case "do_import":
                $data['all_data'][] = $oaipmh_model->do_import($data);
                $oaipmh_model->saving_data($data);
                $data['all_data'][0]['imported'] = count($data['all_data'][0]['records']);
                return json_encode($data['all_data'][0]);
           // case 'saving_data':
               // return json_encode($oaipmh_model->saving_data($data));
            case 'import_list_set':
                $sets = false;
                if(isset($data['sets'])){
                    $sets = $data['sets'];
                }
                $oaipmh_model->import_list_set($data['url'], $data['collection_id'],$sets);
                return true;
            case 'import_list_set_repository':
                $oaipmh_model_repository = new OAIPMHRepositoryModel();
                $oaipmh_model_repository->import_list_set_repository($data);
                return true;
            case "generate_new_container":
                return $this->render(dirname(__FILE__) . '../../../views/import/oaipmh/container_mapping_attributes.php',$data);
                break;
            case "map_exif":
                $export_model = new ExportModel;
                $data = $export_model->create_new_mapping($data['fields']);
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

$import_controller = new ImportController();
echo $import_controller->operation($operation, $data);
?>