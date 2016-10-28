<?php
ini_set('max_input_vars', '10000');

require_once(dirname(__FILE__) . '../../general/general_controller.php');

class PermissionController extends Controller {

    public function operation($operation, $data) {
        switch ($operation) {
            case "show-page":
                return $this->render(dirname(__FILE__) . '../../../views/permission/page.php');
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

$import_controller = new PermissionController();
echo $import_controller->operation($operation, $data);
?>