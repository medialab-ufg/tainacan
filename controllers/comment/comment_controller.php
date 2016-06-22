<?php

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * e.g., it puts together the home page when no home.php file exists.
 *
 * Learn more: {@link https://codex.wordpress.org/Template_Hierarchy}
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
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



