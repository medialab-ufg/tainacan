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
                return $log->user_events($_evt, $data['event'], $data['from'], $data['to'], $data['collec_id'], $data['filter']);
            case "add_log":
                Log::addLog(['collection_id' => $data['collection_id'], 'event_type' => 'collection_search',
                    'event' => $data['event'], 'resource_id' => $data['resource_id'] ]);
                break;
        endswitch;
    }

    private function getEventType($parent_name, $_event_suffix) {
        switch ($parent_name) {
            case _t('Users'):
            case _t('Categories'):
                return "user_" . $_event_suffix;
            case _t('Comments'):
                return "comment";
            case _t('Items'):
                return $_event_suffix . "_items";
            case _t('Tags'):
                return "tags";
            case _t('Collections'):
            case _t('Import / Export'):
            case _t('Administration'):
                return $_event_suffix;
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