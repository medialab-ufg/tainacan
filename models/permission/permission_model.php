<?php
/*
 * # 1 - Collection profiles methods
 * # 2 - Repository profiles methods
 */


require_once(dirname(__FILE__) . '../../general/general_model.php');
/**
 * 
 * classe que salva as permissoes de uma colecao e de um reposiotrio
 * 
 */
class PermissionModel extends Model {

    
################## 1 - Collection profiles methods #############################
    /**
     * 
     * @param type $data
     */
    public function save_permission_collection($data) {
        var_dump($data);
        add_new_profile_collection($data);
    }
    
    /**
     * 
     * @param type $id O id do termo criado para o perfil
     * @param type $key A chave que o utilizada para montagem do index do array 
     * @param type $data os dados vindo do formulario
     * 
     */
    public function update_role_permission($id,$key,$data) {
        $array_entities = ['item','metadata','category','tag','comment','descritor'];
        foreach ($array_entities as $entity) {
            $indexes = $this->get_keys_permission($entity, $key);
            foreach ($indexes as $index) {
                if(isset($data[$index])){
                    update_term_meta($id, $index, $data[$index]);
                }else{
                    update_term_meta($id, $index, 'no');
                }
            }
        }
    }
    
    /**
     * 
     * @param type $entity
     * @param type $key
     */
    public function get_keys_permission($entity,$key){
        return [
            'socialdb_permission_crud_user_'. $entity .'_'. $key,
            'socialdb_permission_crud_other_'. $entity .'_'. $key,
            'socialdb_permission_suggest_user_'. $entity .'_'. $key,
            'socialdb_permission_suggest_other_'. $entity .'_'. $key,
            'socialdb_permission_review_other_'. $entity .'_'. $key,
            'socialdb_permission_download_other_'. $entity .'_'. $key,
            'socialdb_permission_view_other_'. $entity .'_'. $key,
        ];
    }
    
    
    /**
     * 
     * @param type $param
     */
    public function get_collection_profile_data($collection_id) {
        $values = [];
        $profiles = get_post_meta($collection_id, 'socialdb_collection_roles');
        if($profiles){
            foreach ($profiles as $profile) {
                $values[$profile] = $this->get_property_meta_data($profile);
            }
        }
        return $values;
    }
################################################################################
}
