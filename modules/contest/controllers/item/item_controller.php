<?php
$_GET['is_module_active'] = TRUE;
require_once(dirname(__FILE__).'../../../models/item/item_model.php');
include_once(dirname(__FILE__).'/../../../../controllers/general/general_controller.php');  
 class ItemController extends Controller{
	 public function operation($operation,$data){
		switch ($operation) {
                    //adicionar um novo argumento 
                    case 'show-item':
                        $data['object'] = get_post($data['object_id']);
                         return $this->render(dirname(__FILE__).'../../../views/item/item.php', $data);
                        
                }
	}
 }
/*
 * Controller execution
*/

 if($_POST['operation']){
	$operation = $_POST['operation'];
    $data = $_POST;
}else{
	$operation = $_GET['operation'];
	$data = $_GET;
}

$controller = new ItemController();
echo $controller->operation($operation,$data);
