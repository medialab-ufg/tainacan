<?php

//ini_set('max_input_vars', '10000');

require_once(dirname(__FILE__) . '../../../models/export/zip_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');
class ZipController extends Controller {

    public function operation($operation, $data) {
        $zip_model = new ZipModel;
        switch ($operation) {
            case "export_collection":
                $zip_model->export_collection($data['collection_id']);
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

$zip_controller = new ZipController();
echo $zip_controller->operation($operation, $data);
