<?php

ini_set('max_input_vars', '10000');

require_once(dirname(__FILE__) . '../../../models/import/import_model.php');
require_once(dirname(__FILE__) . '../../../models/import/eur_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class EurController extends Controller {

    public function operation($operation, $data) {
        $eur_model = new EurModel();

        switch ($operation) {
            case "import_eur":
                var_dump($data);
                break;

            case "search_eur":
                var_dump($data);
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
    $data['file'] = $_FILES;
} else {
    $operation = $_GET['operation'];
    $data = $_GET;
    $data['file'] = $_FILES;
}

$eur_controller = new EurController();
echo $eur_controller->operation($operation, $data);
?>