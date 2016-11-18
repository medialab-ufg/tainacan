<?php
$_GET['is_module_active'] = TRUE;
include_once(dirname(__FILE__).'../../../models/filters/filters_model.php');
include_once(dirname(__FILE__).'/../../../../controllers/general/general_controller.php');  
 class FiltersController extends Controller{
	 public function operation($operation,$data){
                $model = new FiltersModel;   
		switch ($operation) {
                    case 'initDynatreePropertiesFilter':
                        return $model->initDynatreePropertiesFilter($data['collection_id']);
                    case 'restrictionsDynatreeProperties':
                        return $model->initDynatreePropertiesFilter($data['collection_id'],false); 
                    case 'parentDataDynatreeProperties':
                        return $model->initDynatreeTypePropertiesFilter($data['collection_id'],false); 
                    case 'parentObjectDynatreeProperties':
                        return $model->initDynatreeTypePropertiesFilter($data['collection_id'],false,'socialdb_property_object'); 
                    case 'restrictionsDynatreeIndividues':
                        return $model->initDynatreeIndividues($data['collection_id']); 
                        
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

$controller = new FiltersController();
echo $controller->operation($operation,$data);
