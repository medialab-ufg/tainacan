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
