<?php

require_once(dirname(__FILE__) . '../../general/general_model.php');
/**
 * 
 * classe que salva as permissoes de uma colecao e de um reposiotrio
 * 
 */
class PermissionModel extends Model {

    /**
     * 
     * @param type $data
     */
    public function save_permission_collection($data) {
        var_dump($data);
        $this->add_new_profile_collection($data);
    }
    
    /**
     * function add($data)
     * @param mix $data  O id do colecao
     * @return json  
     * 
     * Autor: Eduardo Humberto 
     */
    public function add_new_profile_collection($data) {
        $name = $data['name_new_profile'];
        if(trim($name)==''){
            return false;
        }
        $is_new = get_term_by($field, sanitize_title(remove_accent($name)) . "_" . $data['collection_id'], 'socialdb_role_type');
        if (!$is_new) {
            $name = $name.'-1';
        }
        $new_role = wp_insert_term($name, 'socialdb_role_type', array(
                'slug' => sanitize_title(remove_accent($name)) . "_" . $data['collection_id']));
        //apos a insercao
        if (!is_wp_error($new_role) && $new_role['term_id']) {// se a categoria foi inserida com sucesso
            add_post_meta($data['collection_id'], 'socialdb_collection_roles', $new_role['term_id']);
            return $new_role['term_id'];
        } else {
            return false;
        }
    }
    
    public function update_role_permission($id,$key,$data) {
        $array_entities = ['item','metadata','category','tag','comment','descritor'];
        foreach ($array_entities as $entity) {
            if($data['socialdb_permission_suggest_user_'. $entity .'_'. $key]){
                update_term_meta($id, $entity, $array_entities);
            }else{
                
            }
        }
    }
}
