<?php
require_once(dirname(__FILE__) . '../../../models/home/home_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class HomeController extends Controller {

    public function operation($operation, $data) {
        $home_model = new HomeModel();
        switch ($operation):
            case "display_view_main_page":
                $data = $home_model->display_view_main_page($data);
                return $this->render(dirname(__FILE__).'../../../views/home/home.php', $data);
                break;
            case "load_item_type":
                $items_array = $home_model->get_items_of_type($data['item_type']);
                $result = $home_model->format_item_data($items_array);
                return json_encode($result);
                break;
            case "total_collections":
                global $wpdb;
                $return = [];
                $wp_posts = $wpdb->prefix . "posts";
                $query = "SELECT p.* FROM $wp_posts p WHERE p.post_type LIKE 'socialdb_collection'
                          AND p.post_status LIKE 'publish' AND p.ID NOT IN (".get_option('collection_root_id').")
                        ";                 
                $results = $wpdb->get_results($query);
                if ($results&&is_array($results)&&count($results)>0) {
                    $return['size'] = count($results);
                }
                return json_encode($return);
            case "verifyAction":
                return $home_model->verifyAction($data);
        endswitch;
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

$collection_controller = new HomeController();
echo $collection_controller->operation($operation, $data);