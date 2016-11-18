<?php
ini_set('max_input_vars', '10000');

require_once(dirname(__FILE__) . '../../../models/permission/permission_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class PermissionController extends Controller {
            
    public function operation($operation, $data) {
        $model = new PermissionModel;
        switch ($operation) {
            case "show-page":
                insert_default_roles($data['collection_id']);
                $data['values'] = $model->get_collection_profile_data($data['collection_id']);
                return $this->render(dirname(__FILE__) . '../../../views/permission/page.php',$data);
                break;
            case "save-permission":
                return $model->save_permission_collection($data);
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
