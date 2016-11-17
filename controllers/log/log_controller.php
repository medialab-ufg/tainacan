<?php
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class LogController extends Controller {
    public function operation($op, $data) {
        switch($op):
            case "show_statistics":
                return $this->render(dirname(__FILE__) . '../../../views/statistics/list.php', $data);
            case "user_events":
                $log = new Log();
                $_evt = $this->getEventType($data['parent'], $data['event']);
                return $log->user_events($_evt, $data['event'], $data['from'], $data['to']);
        endswitch;
    }

    private function getEventType($parent_name, $_event_suffix) {
        switch ($parent_name) {
            case i18n_str('Users'):
            case i18n_str('Collections'):
                return "user_" . $_event_suffix;
            case i18n_str('Comments'):
                return "comment";
            case i18n_str('Items'):
                return "user_";
            case i18n_str('Tags'):
                return "tags";
        }
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