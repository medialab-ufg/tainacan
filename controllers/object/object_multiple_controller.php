<?php
require_once(dirname(__FILE__) . '../../../models/object/object_model.php');
require_once(dirname(__FILE__) . '../../../models/collection/collection_model.php');
require_once(dirname(__FILE__) . '../../../controllers/general/general_controller.php');
require_once(dirname(__FILE__) . '../../../models/user/user_model.php');

require_once(dirname(__FILE__) . '../../../models/object/object_multiple_model.php');

class ObjectMultipleController extends Controller {

    public function operation($operation, $data) {
        $objectmultiple_model = new ObjectMultipleModel;
        switch ($operation) {
            case "add_multiples":
                $data = $objectmultiple_model->add($data);
                return $data;
            case 'add_multiples_socialnetwork':     
                 $data = $objectmultiple_model->add_socialnetwork($data);
                return $data;
            case 'edit_multiple_items':
                return $objectmultiple_model->edit_multiple($data);
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

$object_controller = new ObjectMultipleController();
echo $object_controller->operation($operation, $data);
?>
