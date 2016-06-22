<?php

ini_set('max_input_vars', '10000');
error_reporting(0);
/**
 * Eduardo Humberto
 */
require_once(dirname(__FILE__) . '../../general/general_controller.php');
require_once(dirname(__FILE__) . '../../../models/export/rdf_model.php');
require_once(dirname(__FILE__) . '../../../models/export/rdf_collection_model.php');
require_once(dirname(__FILE__) . '../../../models/export/rdf_category_model.php');
require_once(dirname(__FILE__) . '../../../models/export/rdf_property_model.php');
require_once(dirname(__FILE__) . '../../../models/export/rdf_tag_model.php');
require_once(dirname(__FILE__) . '../../../models/export/rdf_repository_model.php');
class RDFController extends Controller {
    var $model;
    var $model_collection;
    var $model_category;
    var $model_property;
    var $model_tag;
    var $model_repository;
    
    public function operation($operation, $data) {
        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        //verifico qual operacao esta tentando fazer
        if(is_page_tainacan()&&get_page_tainacan()=='item'){
           $operation = 'item';
        }elseif(!is_page_tainacan()&&is_single()&&strpos($actual_link, 'all')===false){
           $operation = 'collection'; 
        }elseif (!is_page_tainacan()&&is_single()&&strpos($actual_link, 'all')!==false) {
           $operation = 'collection_all';
        }elseif (!is_page_tainacan()&&is_front_page()&&strpos($actual_link, 'all')!==false) {
           $operation = 'repository_all';
        }elseif (!is_page_tainacan()&&is_front_page()&&strpos($actual_link, 'all')===false) {
           $operation = 'repository';
        }else if(is_page_tainacan()&&get_page_tainacan()=='category'){
            $operation = 'category';
        }else if(is_page_tainacan()&&get_page_tainacan()=='property'){
            $operation = 'property';
        }else if(is_page_tainacan()&&get_page_tainacan()=='tag'){
            $operation = 'tag';
        }
        unset($data['by_function']);
        switch ($operation) {
            case 'category':
                $slug = str_replace('.rdf', '', $data['category']);
                if(has_filter('personal_export_category_rdf')){
                    return apply_filters('personal_export_category_rdf', $slug) ;
                }else{
                  return trim($this->model_category->export_category($slug)); 
                }
                break;
            case 'taxonomy':
                $slug = str_replace('.rdf', '', $data['tax']);
                return trim($this->model_category->export_taxonomy($slug));
                break;
             case 'property':
                $slug = str_replace('.rdf', '', $data['property']);
                 if(has_filter('personal_export_property_rdf')){
                    return apply_filters('personal_export_property_rdf', $slug) ;
                }else{
                  return trim($this->model_property->export_property($slug));
                }
                break;
            case 'tag':
                $slug = str_replace('.rdf', '', $data['tag']);
                return trim($this->model_tag->export_tag($slug));
                break;
            case 'item':
                if(has_filter('modificate_export_item_rdf')){
                    return apply_filters('modificate_export_item_rdf', '') ;
                }else{
                  return trim($this->model->export_simple_item());
                }
                break;
            case 'collection_all':
                //if(get_current_user_id()=='1'){
                    if(has_filter('modificate_export_complete_collection_rdf')){
                        return apply_filters('modificate_export_complete_collection_rdf', '') ;
                    }else{
                      return trim($this->model_collection->export_all_collection());
                    }
               // }else{
                //    wp_redirect(site_url());
                //}
                break;
            case 'collection':
                if(has_filter('modificate_export_collection_rdf')){
                    return apply_filters('modificate_export_collection_rdf', '') ;
                }else{
                   return trim($this->model_collection->export_simple_collection());
                }
                break;
            case 'repository':
                if(has_filter('modificate_export_simple_repository_rdf')){
                    return apply_filters('modificate_export_simple_repository_rdf', '') ;
                }else{
                    return trim($this->model_repository->export_simple_repository());
                }
                break;
            case 'repository_all':
                if(get_current_user_id()=='1'):
                    if(has_filter('modificate_export_complete_repository_rdf')){
                        return apply_filters('modificate_export_complete_repository_rdf', '') ;
                    }else{
                       return trim($this->model_repository->export_complete_repository());
                    }
                else:
                    wp_redirect(site_url());
                endif;
                break;
        }
    }
    
    public function __construct() {
       $this->model = new RDFModel();
       $this->model_collection = new RDFCollectionModel();
       $this->model_repository = new RDFRepositoryModel();
       $this->model_category = new RDFCategoryModel();
       $this->model_property = new RDFPropertyModel();
       $this->model_tag = new RDFTagModel();
    }
}

/*
 * Controller execution
 */
if ($_POST['operation']) {
    $operation = $_POST['operation'];
    $data = $_POST;
}elseif($_GET['verb']){
    $operation = $_GET['verb'];
    $data = $_GET;
}
else {
    $operation = $_GET['operation'];
    $data = $_GET;
}
$rdf_controller = new RDFController();
echo $rdf_controller->operation($operation, $data);
