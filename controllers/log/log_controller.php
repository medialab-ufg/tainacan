<?php
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class LogController extends Controller {
    public function operation($op, $data) {
        switch($op):
            case "show_statistics":
                return $this->render(dirname(__FILE__) . '../../../views/statistics/list.php', $data);
        endswitch;
        
    }
    
}

if ($_POST['operation']) {
    $operation = $_POST['operation'];
    $data = $_POST;
} else {
    $operation = $_GET['operation'];
    $data = $_GET;
}

$stat_controller = new StatisticsController();
echo $stat_controller->operation($operation, $data);