<?php
require_once(dirname(__FILE__) . '../../../models/license/license_model.php');
require_once(dirname(__FILE__) . '../../../controllers/general/general_controller.php');

class LicenseController extends Controller {

    public function operation($operation, $data) {
        $license_model = new LicenseModel();
        switch ($operation) {
            case "list_licenses":
                Log::addLog(['collection_id' => $data['collection_id'], 'event_type' => 'collection_admin', 'event' => 'licenses']);
                return $this->render(dirname(__FILE__) . '../../../views/license/list.php', $data);
                break;
            case "listStandartLicenses":
                $arrLicenses = $license_model->get_repository_licenses($data['collection_id']);
                return json_encode($arrLicenses);
                break;
            case "listCustomLicenses":
                $arrLicenses = $license_model->get_custom_licenses($data['collection_id']);
                return json_encode($arrLicenses);
                break;
            case "add_collection_license":
                if (trim($data['add_license_url']) == '' && trim($data['add_license_description']) == '' || trim($data['add_license_name']) == ''):
                    $result['title'] = __('Error','tainacan');
                    $result['msg'] = __('Please, fill the form correctly!','tainacan');
                    $result['type'] = 'error';
                else:
                    if (!$license_model->verify_equal_license_title($data['add_license_name'], $data['collection_id'])):
                        $result['title'] = __('Info','tainacan');
                        $result['msg'] = __('This license is already registered!','tainacan');
                        $result['type'] = 'info';
                    else:
                        $result = $license_model->insert_custom_license($data);
                    endif;
                endif;

                return json_encode($result);
                break;
            case "get_license_to_edit":
                $license = $license_model->get_license_to_edit($data['license_id']);
                return json_encode($license);
                break;
            case "edit_repository_license":
                if (trim($data['add_license_url']) == '' && trim($data['add_license_description']) == '' || trim($data['add_license_name']) == ''):
                    $result['title'] = __('Error','tainacan');
                    $result['msg'] = __('Please, fill the form correctly!','tainacan');
                    $result['type'] = 'error';
                else:
                    $result = $license_model->edit_repository_license($data);
                endif;
                return json_encode($result);
                break;
            case "delete_custom_license":
                $result = $license_model->delete_repository_license($data['license_id'], $data['collection_id']);
                return json_encode($result);
                break;
            case "change_pattern_license":
                $result = $license_model->change_pattern_license($data['license_id'], $data['collection_id']);
                return json_encode($result);
                break;
            case "change_enabled_license":
                $result = $license_model->change_enabled_license($data);
                return json_encode($result);
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

$license_controller = new LicenseController();
echo $license_controller->operation($operation, $data);
?>
