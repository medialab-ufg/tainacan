<?php
require_once(dirname(__FILE__) . '../../../models/rss/rss_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class RssController extends Controller {

    public function operation($operation, $data) {
        $rss_model = new RssModel();
        switch ($operation) {
            case "feed":
                header("Content-Type: application/xml; charset=ISO-8859-1");
                echo $rss_model->feed($data['collection_id']);
                break;
            
        }
    }

}

/*
 * Controller execution
 */

if (isset($_POST['operation'])) {
    $operation = $_POST['operation'];
    $data = $_POST;
} else {
    $operation = $_GET['operation'];
    $data = $_GET;
}

$rss_controller = new RssController();
echo $rss_controller->operation($operation, $data);