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

class ObjectDraftController extends Controller {

    public function operation($operation, $data) {
        $object_model = new ObjectDraftModel;
        $objectfile_model = new ObjectFileModel;
        switch ($operation) {
            // #1 ADICIONAR ITEMS TIPO TEXTO
            case "add":
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
