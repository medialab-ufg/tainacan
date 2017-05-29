<?php
ini_set('max_input_vars', '10000');
require_once(dirname(__FILE__) . '../../../models/theme_options/synchronize_model.php');
require_once(dirname(__FILE__) . '../../../models/theme_options/populate_model.php');
require_once(dirname(__FILE__) . '../../../models/collection/collection_templates_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class SynchronizeController extends Controller {

    public function operation($operation, $data) {
        $synchronize_model = new SynchronizeModel();
        switch ($operation) {
            case "start":
                return $synchronize_model->start($data);
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

$theme_options_controller = new SynchronizeController();
echo $theme_options_controller->operation($operation, $data);
?>