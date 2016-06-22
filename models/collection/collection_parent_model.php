<?php


require_once(dirname(__FILE__) . '/collection_model.php');

class CollectionParentModel extends CollectionModel {

    public function list_collection_by_user(){
        if(current_user_can('manage_options')){
            $my_collections = $this->get_all_collections();
        }else{
            $my_collections = $this->get_collection_by_user(get_current_user_id());
        }
        return $my_collections;
    }
    
    /**
     * function verify_name_collection()
     * @param array Os dados vindo do formulario
     * @return json com o id e o nome de cada colecao
     * @author Eduardo Humberto
     */
    public function list_collection_parent($actual_collection_id){
        $messed_array = array();
        $result = array();
        $not_allowed = [get_option('collection_root_id'),$actual_collection_id];
        $my_collections = $this->list_collection_by_user();// busco as colecoes do usuario/ se for admin sao todas
        if(!empty($my_collections)){// se existir
            foreach ($my_collections as $my_collection) {// percorro cada uma
                $allow_hierarchy = get_post_meta($my_collection->ID, 'socialdb_collection_allow_hierarchy',true);//
                if($allow_hierarchy!=='false'&&$my_collection->post_status=='publish'&&!in_array($my_collection->ID, $not_allowed)){
                      $result['collection_name'] = $my_collection->post_title;
                      $result['category_root_id'] =  get_post_meta($my_collection->ID, 'socialdb_collection_object_type',true);
                      $result['parent'] = get_term_by('id', $result['category_root_id'], 'socialdb_category_type')->parent;
                      $messed_array[] = $result;
                }
            }
        }
        
                
        $repository_category = get_term_by('slug', 'socialdb_category', 'socialdb_category_type');
        $final_array = array('name'=> 'socialdb_category','id'=>$repository_category->term_id,'children'=>array());
        $this->reorganize_array($messed_array,$final_array['children'],$repository_category->term_id);
        return $final_array;
    }
    
    public function reorganize_array(&$messed_array,&$final_array,$parent_id) {
        if(is_array($messed_array)&&count($messed_array)>0){
            foreach ($messed_array as $collection) {
                if($collection['parent']==$parent_id){
                    $final_array[] = array('name'=> $collection['collection_name'],'id'=>$collection['category_root_id'],'children'=>array());
                    $this->reorganize_array($messed_array,$final_array[end(array_keys($final_array))]['children'],$collection['category_root_id']);
                }
            }
        }else{
            return $final_array;
        }
    }
}
