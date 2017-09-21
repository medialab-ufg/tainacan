<?php

require_once(dirname(__FILE__) . '../../../models/comment/comment_model.php');
require_once(dirname(__FILE__) . '../../../controllers/general/general_controller.php');

class CommentController extends Controller {

    public function operation($operation, $data) {
        $comment_model = new CommentModel();
        switch ($operation) {
            case "get_comment_json":
                return json_encode($comment_model->get_comment_json($data['comment_id']));
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

$comment_controller = new CommentController();
echo $comment_controller->operation($operation, $data);



