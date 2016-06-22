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
require_once(dirname(__FILE__) . '../../../models/design/design_model.php');
require_once(dirname(__FILE__) . '../../../models/collection/collection_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class DesignController extends Controller {

    public function operation($operation, $data) {
        $design_model = new DesignModel();
        switch ($operation) {
            case "initDynatree":
                return $design_model->initDynatree($data);
                break;
            case "create":
                return $design_model->create();
                break;
            case 'simple_add':
                $new_collection_id = $design_model->simple_add($data);
                header("location:" . get_permalink($new_collection_id));
                break;
            case "add":
                return $design_model->add($data);
                break;
            case "edit":
                return $design_model->edit($data);
                break;
            case "update":
                return $design_model->update($data);
                break;
            case "delete":
                return $design_model->delete($data);
                break;
            case "list":
                return $design_model->list_collection();
                break;
            case "show_header":
                return $this->render(dirname(__FILE__) . '../../../views/design/header_collection.php', $data);
                break;
            case "edit_configuration":
                $collection_id = $data['collection_id'];
                $data = $design_model->get_collection_data($collection_id);

                return $this->render(dirname(__FILE__) . '../../../views/design/edit.php', $data);
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

$design_controller = new DesignController();
echo $design_controller->operation($operation, $data);
?>