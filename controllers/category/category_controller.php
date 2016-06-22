<?php

require_once(dirname(__FILE__).'../../../models/category/category_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_term/event_term_create_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_term/event_term_edit_model.php');
require_once(dirname(__FILE__).'../../../models/event/event_term/event_term_delete_model.php');
require_once(dirname(__FILE__).'../../general/general_controller.php');  
require_once(dirname(__FILE__) . '../../../models/user/user_model.php');

 class CategoryController extends Controller{
	 public function operation($operation,$data){
		$category_model = new CategoryModel();
		switch ($operation) {
                    //pagina da categoria
                    case 'page':
                        $user_model = new UserModel();
                        $term = get_term_by('slug', $data['slug_category'], 'socialdb_category_type') ;
                        if ($term) {
                            $data['term'] = $term;
                            $data['parent'] = get_term_by('id', $term->parent, 'socialdb_category_type') ;
                            $properties = $category_model->get_parent_properties($term->term_id, [], $category_model->get_category_root_of($data['collection_id']));
                           // $properties = array_filter(get_term_meta($term->term_id, 'socialdb_category_property_id'));
                            if(!empty($properties)){
                                foreach ($properties as $id) {
                                    $data['metadata'][$id]['type'] = $category_model->get_property_type_hierachy($id); // pego o tipo da propriedade
                                    $data['metadata'][$id]['data'] = $category_model->get_all_property($id,true); // pego todos os dados possiveis da propriedade
                                }
                            }    
                            $data["username"] = $user_model->get_user(get_term_meta($term->term_id, 'socialdb_category_owner', true))['name'];
                            $array_json['html'] = $this->render(dirname(__FILE__) . '../../../views/category/page.php', $data);
                            return json_encode($array_json);
                        }else{
                            $array_json['title'] = __('Attention!','tainacan');
                            $array_json['error'] = __('Category removed','tainacan');
                            return json_encode($array_json);
                        }
                        break;
                    case 'page_tax':
                         $user_model = new UserModel();
                        $term = get_term_by('slug', $data['slug_category'], 'socialdb_category_type') ;
                        if ($term) {
                            $data['term'] = $term;
                             $data["username"] = $user_model->get_user(get_term_meta($term->term_id, 'socialdb_category_owner', true))['name'];
                            $array_json['html'] = $this->render(dirname(__FILE__) . '../../../views/category/page_tax.php', $data);
                            return json_encode($array_json);
                        }else{
                            $array_json['title'] = __('Attention!','tainacan');
                            $array_json['error'] = __('Category removed','tainacan');
                            return json_encode($array_json);
                        }
                        break;    
                    case "add":
                        //return json_encode($category_model->add($data));
                        return $this->insert_event_add($data);
                        break;
                    case "update":
                        //return json_encode($category_model->update($data));
                        return $this->insert_event_update($data);
                        break;    
                    case "delete":
                        //return $category_model->delete($data);
                        return $this->insert_event_delete($data);
                        break;    
                    case 'vinculate_facets':
                        return $category_model->vinculate_facets($data);
                        break;
                    case "get_parent":
                        $callback = $category_model->get_category_array($category_model->get_parent($data));
                        $callback['child_name'] = $category_model->get_category($data['category_id'])->name;
                        return json_encode($callback);
                        break;
                    case "initDynatree":
                        return $category_model->initCategoriesDynatree($data);
                        break;  
                    case 'initDynatreeTerms':
                         return $category_model->initCategoriesDynatreeTerms($data);
                        break;
                    case 'findDynatreeChild':
                        return $category_model->find_dynatree_children($data);
                        break;
                    case "initPropertyDynatree":
                        return $category_model->initPropertyCategoriesDynatree($data);
                        break;
                    case "get_metas":
                        return json_encode($category_model->get_metas($data));
                        break;
                    case "list":
                        return $this->render(dirname(__FILE__).'../../../views/category/list.php', $data);
                        break;  
                    case "insert_hierarchy":
                        return json_encode($category_model->insert_hierarchy($data));
                        break;
                     case "export_hierarchy":
                        $category_model->export_hierarchy($data);
                        break;
                    case 'verify_has_children':
                        return json_encode($category_model->verify_has_children($data));
                        break;
                    case 'get_category_root_name':
                        return json_encode(['key'=> $data['category_id'],'title'=> get_term_by('id', $data['category_id'], 'socialdb_category_type')->name]);
                    case 'initDynatreeDynamic':
                         return $category_model->initCategoriesDynatreeDynamic($data);
                        break;   
                    case 'verify_name_in_taxonomy':
                        return $category_model->verify_name_in_taxonomy($data);
                    case 'get_category':
                        $callback['term'] = $category_model->get_category($data['category_id']);
                        $ancestors = get_ancestors($data['category_id'], 'socialdb_category_type');
                        $value  = get_term_meta($data['property_id'], 'socialdb_property_term_root',true);
                        if(is_array($ancestors)&&in_array($value, $ancestors)){
                            $callback['show'] = true; 
                        }else{
                           $callback['show'] = false; 
                        }
                        return json_encode($callback);
                    case 'get_link_individuals':
                        return $category_model->get_link_individuals($data['term_id'],$data['collection_id']);
                    case 'get_url_category':
                        $category = get_term_by('id', $data['term_id'], 'socialdb_category_type');
                        if($category){
                            return json_encode(['slug'=>$category->slug]);
                        }else{
                            $id = ($data['term_id']=='tag_facet_tag')? get_term_by('slug','socialdb_tag','socialdb_tag_type')->term_id:$data['term_id'];
                            $tag = get_term_by('id', $id, 'socialdb_tag_type');
                            return json_encode(['slug'=>$tag->slug]);
                        }
                        
		}
	}
    /**
     * @signature - function insert_event_add($object_id, $data )
     * @param int $object_id O id do Objeto
     * @param array $data Os dados vindos do formulario
     * @return array os dados para o evento
     * @description - 
     * @author: Eduardo 
     */
    public function insert_event_add($data) {
        $eventAddTerm = new EventTermCreate();
        $data['socialdb_event_term_suggested_name'] = $data['category_name'];
        $data['socialdb_event_term_parent'] = $data['category_parent_id'];
        $data['socialdb_event_collection_id'] = $data['collection_id'];
        $data['socialdb_event_user_id'] = get_current_user_id();
        $data['socialdb_event_create_date'] = mktime();
        return $eventAddTerm->create_event($data);
    }
    /**
     * @signature - function insert_event_update( $data )
     * @param array $data Os dados vindos do formulario
     * @return array os dados para o evento
     * @description - 
     * @author: Eduardo 
     */
    
     public function insert_event_update($data) {
         $eventEditTerm = new EventTermEdit();
        $data['socialdb_event_term_id'] = $data['category_id'];
        $data['socialdb_event_term_suggested_name'] = $data['category_name'];
        $data['socialdb_event_term_suggested_parent'] = $data['category_parent_id'];
        $data['socialdb_event_term_previous_parent'] = 'Not informed';
        $data['socialdb_event_collection_id'] = $data['collection_id'];
        $data['socialdb_event_user_id'] = get_current_user_id();
        $data['socialdb_event_create_date'] = mktime();
        return $eventEditTerm->create_event($data);
    }
    /**
     * @signature - function insert_event_update( $data )
     * @param array $data Os dados vindos do formulario
     * @return array os dados para o evento
     * @description - 
     * @author: Eduardo 
     */
    
     public function insert_event_delete($data) {
        $eventDeleteTerm = new EventTermDelete();
        $data['socialdb_event_term_id'] = $data['category_delete_id'];
        $data['socialdb_event_collection_id'] = $data['collection_id'];
        $data['socialdb_event_user_id'] = get_current_user_id();
        $data['socialdb_event_create_date'] = mktime();
        return $eventDeleteTerm->create_event($data);
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

$category_controller = new CategoryController();
echo $category_controller->operation($operation,$data);