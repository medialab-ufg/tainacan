<?php

/**
 * #1 - ADICIONAR ITEMS TIPO TEXTO
 * #2 - ADICIONAR ITEMS DEFAULT
 */
require_once(dirname(__FILE__) . '../../../models/object/object_draft_model.php');
require_once(dirname(__FILE__) . '../../../models/collection/collection_model.php');
require_once(dirname(__FILE__) . '../../../controllers/general/general_controller.php');
require_once(dirname(__FILE__) . '../../../models/user/user_model.php');
require_once(dirname(__FILE__) . '../../../models/object/objectfile_model.php');
require_once(dirname(__FILE__) . '../../../models/object/object_multiple_draft_model.php');

class ObjectDraftController extends Controller {

    public function operation($operation, $data) {
        $object_model = new ObjectDraftModel;
        $objectfile_model = new ObjectFileModel;
        $objectmultiple_model = new ObjectMultipleDraftModel;
        switch ($operation) {
            case "clear_betafiles":
                delete_user_meta(get_current_user_id(), 'socialdb_collection_'.$data['collection_id'].'_betafile');
                break;
            case "add_multiples":
                $data = $objectmultiple_model->add($data);
                return $data;
            case "add_multiples_socialnetwork":
                $data = $objectmultiple_model->add($data,true);
                return $data;
            case "add":
            case "update":
                return $object_model->addItemDraft($data);
                break;
            // FIM : ADICIONAR ITEMS TIPO TEXTO
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

$object_controller = new ObjectDraftController();
echo $object_controller->operation($operation, $data);
?>
