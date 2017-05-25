<?php
require_once(dirname(__FILE__) . '../../../../controllers/general/general_controller.php');


class userController extends Controller
{
    public function operation ($operation)
    {
        switch($operation)
        {
            case 'get_user':
                $user_id = $_POST['user_id'];
                $user = get_user_by('id', $user_id);

                return json_encode($user);
                break;
            default:
                return false;
                break;
        }
    }
}

$operation = isset($_POST['operation']) ? $_POST['operation'] : false;

$userController = new userController();
echo $userController->operation($operation);