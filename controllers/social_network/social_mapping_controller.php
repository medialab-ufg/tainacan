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
require_once(dirname(__FILE__) . '../../../models/social_network/social_mapping_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');
require_once(dirname(__FILE__) . '../../../models/export/export_model.php');

class SocialMappingController extends Controller {

    public function operation($operation, $data) {
        $social_mapping_model = new SocialMappingModel();
        switch ($operation) {
            case "show_mapping":
                $export_model = new ExportModel;
                $collection_id = $data['collection_id'];
                $social_network = $data['social_network'];
                $data = $export_model->create_new_mapping($data);
                $data['mapping_id'] = $social_mapping_model->get_post_by_title('socialdb_channel_'.$data['social_network'], $collection_id, $social_network);
                $data['term'] = 'socialdb_channel_'.$social_network.'_mapping';
                return $this->render(dirname(__FILE__) . '../../../views/social_network/edit_maping_attributes.php', $data);
                break;
            case "generate_selects":
                return $social_mapping_model->generate_selects($data);
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

$social_mapping_controller = new SocialMappingController();
echo $social_mapping_controller->operation($operation, $data);
?>