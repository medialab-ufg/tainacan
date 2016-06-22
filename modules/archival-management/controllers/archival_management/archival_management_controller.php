<?php
$_GET['is_module_active'] = TRUE;
include_once(dirname(__FILE__).'../../../models/archival_management/archival_management_model.php');
include_once(dirname(__FILE__).'/../../../../controllers/general/general_controller.php');  
 class ArchivalManagementController extends Controller{
	 public function operation($operation,$data){
                $archival_management_model = new ArchivalManagementModel;   
		switch ($operation) {
                    case "list":
                        $posts = $archival_management_model->get_collection_posts($data['collection_id']);
                        $data['category_root_id'] = $archival_management_model->get_category_root_of($data['collection_id']);
                        return $this->render(dirname(__FILE__).'../../../views/archival_management/list.php', $data);
                    case 'export_classification_plan':
                        $string = $archival_management_model->generate_classification_plan($data['category_id']);
                        header('Content-disposition: attachment; filename=Plano de Classificação - '.get_term_by('id',$data['category_id'],'socialdb_category_type')->name.'.txt');
                        header('Content-type: text/plain');
                        echo $string;
                        break; 
                    case 'export_table_of_temporality':
                        $string =  utf8_decode('Assunto;Fase Corrente;Fase Intermediária;Destinação Final;Observação'. PHP_EOL);
                        $string = $archival_management_model->generate_table_of_temporality($data['category_id'],$string);
                        header('Content-disposition: attachment; filename=Tabela de Temporariedade - '.get_term_by('id',$data['category_id'],'socialdb_category_type')->name.'.txt');
                        header('Content-type: text/plain');
                        echo $string;
                        break;
                    case 'list_items_to_export':
                        $posts = $archival_management_model->get_collection_posts($data['collection_id']);
                        return $archival_management_model->get_items_to_transfer($data, $posts);
                    case 'list_items_to_eliminate':
                        $posts = $archival_management_model->get_collection_posts($data['collection_id']);
                        return $archival_management_model->get_items_to_eliminate($data, $posts);
                        
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

$controller = new ArchivalManagementController();
echo $controller->operation($operation,$data);
