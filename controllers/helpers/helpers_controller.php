<?php
require_once(dirname(__FILE__) . '../../../models/helpers/helpers_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class HelpersController extends Controller {
    
    public static function execute_script($code,$args = []) {
        switch ($code):
            case "0001":
                HelpersModel::update_commom_field_collection($args['collection_id']);
                break;
            case '0002':
                HelpersModel::update_all_collections();
                break;
            case '0003':
                HelpersModel::create_helper_item();
                break;
        endswitch;
    }
}
