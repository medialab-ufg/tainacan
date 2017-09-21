<?php

require_once(dirname(__FILE__) . '../../../models/social_network/vimeo_model.php');
require_once(dirname(__FILE__) . '../../../models/object/object_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');
require_once(dirname(__FILE__) . '../../../models/social_network/vimeo/autoload.php');

class VimeoController extends Controller {

    public function operation($operation, $data) {
        $vimeo_model = new VimeoModel();
        switch ($operation) {
            case "import_vimeo_items":
                $data['import_type'] = (empty($data['import_type']) ? 'users' : $data['import_type']);
                $result = $vimeo_model->getVimeoVideos($data);

                if ($result) {
                    return json_encode($result);
                } else {
                    return json_encode([]);
                }
                break;
        }
    }

}

if ($_POST['operation']) {
    $operation = $_POST['operation'];
    $data = $_POST;
} else {
    $operation = $_GET['operation'];
    $data = $_GET;
}

$vimeo_controller = new VimeoController();
echo $vimeo_controller->operation($operation, $data);
?>