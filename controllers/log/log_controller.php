<?php
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class LogController extends Controller {
    public function operation($op, $data) {
        switch($op):
            case "show_statistics":
                return $this->render(dirname(__FILE__) . '../../../views/statistics/list.php', $data);
            case "user_events":
                $log = new Log();
                $_evt = "user_" . $data['event'];
                // return json_encode($log->user_events($_evt));
                return $log->user_events($_evt, $data['event']);
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

$stat_controller = new LogController();
echo $stat_controller->operation($operation, $data);