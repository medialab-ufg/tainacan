<?php

/**
 * Author: Eduardo Humberto
 */
require_once(dirname(__FILE__) . '../../../models/ranking/ranking_model.php');
include_once(dirname(__FILE__).'/../../../../controllers/general/general_controller.php');   

class RankingController extends Controller {

    public function operation($operation, $data) {
        $ranking_model = new RankingContestModel();
        switch ($operation) {
            case "save_vote_binary":
                $result_data = [];
                $array_ancestors[] = $data['object_id'];
                $position_origin =  get_post_meta($data['object_id'], 'socialdb_object_contest_position', true);
                $array_ancestors = array_merge($array_ancestors, get_ancestors($data['object_id'], 'socialdb_object'));
                if (is_user_logged_in()) {
                    if(is_array($array_ancestors)){
                        foreach ($array_ancestors as $ancest) {
                            $position =  get_post_meta($ancest, 'socialdb_object_contest_position', true);
                            if($position_origin == $position):
                                $score = '1';
                                $result_data['save_vote'][] = $ranking_model->save_vote(
                                        ['object_id'=>$ancest,'property_id'=>$data['property_id'],'score'=>$score],true,true);
                            else:
                                $score = '-1';
                                $result_data['save_vote'][] = $ranking_model->save_vote(['object_id'=>$ancest,'property_id'=>$data['property_id'],'score'=>$score],true,true);
                            endif;
                            $result_data['result'] = $ranking_model->calculate_vote_binary($data['property_id'], $data['object_id']);
                            $data['results'][] = $result_data;
                        }
                    }
                    $data['is_user_logged_in'] = true;
                } else {
                    $data['results'] = $ranking_model->calculate_vote_binary($data['property_id'], $data['object_id']);
                    $data['is_user_logged_in'] = false;
                }
                return json_encode($data);
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

$ranking_controller = new RankingController();
echo $ranking_controller->operation($operation, $data);
?>