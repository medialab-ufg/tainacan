<?php
$_GET['is_module_active'] = TRUE;
include_once(dirname(__FILE__) . '/../../../../models/category/category_model.php');
include_once(dirname(__FILE__).'/../../../../controllers/general/general_controller.php');  
 class OntologyController extends Controller{
	 public function operation($operation,$data){
                $model = new CategoryModel;   
		switch ($operation) {
                    //dynatree 
                    case 'initDynatreeSelectTaxonomy':
                        $dynatree = [];
                        return json_encode($model->generate_user_categories_dynatree($data, $dynatree,false,false));
                    case 'childrenDynatreeSelectTaxonomy':
                        $data['classCss'] = 'user_img';
                        $data['category_id'] =  $data['key'];
                        unset($data['hide_checkbox']);
                        return $model->find_dynatree_children($data);
                    //vincular facetas de outras ontologias
                    case 'add_facet':
                        if(add_post_meta($data['collection_id'], 'socialdb_collection_facets', $data['taxonomy_id'])){
                             update_post_meta($data['collection_id'], 'socialdb_collection_facet_' .$data['taxonomy_id'] . '_widget', 'tree');
                             update_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $data['taxonomy_id'] . '_color', 'color4');
                             return json_encode(['title'=>__('Success','tainacan'),'msg'=>__('Taxonomy vinculated!','tainacan'),'type'=>'success']);
                        }else{
                            return json_encode(['title'=>__('Error','tainacan'),'msg'=>'Category removed or collection removed!','type'=>'error']);
                        }
                        
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

$controller = new OntologyController();
echo $controller->operation($operation,$data);
