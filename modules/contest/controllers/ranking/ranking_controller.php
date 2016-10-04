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
                //coloco o voto em que foi realizado
                $result_data['seletor'] = '#constest_score_'.$data['object_id'] ;
                $result_data['is_new'] = $ranking_model->save_vote(
                                            ['object_id'=>$data['object_id'],'property_id'=>$data['property_id'],'score'=>$data['score']],true,true);
                $result_data['score'] = $ranking_model->calculate_vote_binary($data['property_id'], $data['object_id']);
                //adiciono no array
                $data['results'][] = $result_data;
                //busco a posicao inicial
                $position_origin =  get_post_meta($data['object_id'], 'socialdb_object_contest_position', true);
                $score_origin = $data['score'];
                $array_ancestors = get_ancestors($data['object_id'], 'socialdb_object');
                if (is_user_logged_in()) {
                    if(is_array($array_ancestors)){
                        foreach ($array_ancestors as $index => $ancest) {
                            $result_data['seletor'] = '#constest_score_'.$ancest ;
                            $position =  get_post_meta($ancest, 'socialdb_object_contest_position', true);
                            if($position):// se ele tiver uma posicao
                                if($position_origin == 'positive'):
                                    $score = ($data['score']=='1') ? '1' : '-1';
                                    $result_data['is_new'] = $ranking_model->save_vote(
                                            ['object_id'=>$ancest,'property_id'=>$data['property_id'],'score'=>$score],true,true);
                                else:
                                    $score = ($data['score']=='1') ? '-1' : '1';
                                    $result_data['is_new'] = $ranking_model->save_vote(['object_id'=>$ancest,'property_id'=>$data['property_id'],'score'=>$score],true,true);
                                endif;
                                $position_origin = $position;
                                $score_origin = $score;
                            else:// se nao tiver uma posicao
                                // verifco se o anterior a ele possui
                                if(isset($array_ancestors[$index-1])): 
                                    $position =  get_post_meta($array_ancestors[$index-1], 'socialdb_object_contest_position', true);
                                    //position
                                    if($position=='positive')
                                        $score = ($data['score']=='1') ? '1' : '-1';
                                    else if($position=='negative')
                                        $score = ($data['score']=='1') ? '-1' : '1';
                                    // is new
                                    $result_data['is_new'] = $ranking_model->save_vote(
                                            ['object_id'=>$ancest,'property_id'=>$data['property_id'],'score'=>$score],true,true);
                                //se nao busco o voto atual    
                                else:  
                                    $score = ($data['score']=='1') ? '1' : '-1';
                                    $result_data['is_new'] = $ranking_model->save_vote(
                                            ['object_id'=>$ancest,'property_id'=>$data['property_id'],'score'=>$score],true,true);
                                endif;
                                $position_origin = $position;
                                $score_origin = $score;
                            endif;   
                            $result_data['score'] = $ranking_model->calculate_vote_binary($data['property_id'], $ancest);
                            $data['results'][] = $result_data;
                        }
                    }
                    $data['is_user_logged_in'] = true;
                } else {
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