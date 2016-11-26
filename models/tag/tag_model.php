<?php

//use CollectionModel;

include_once (dirname(__FILE__) . '/../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__).'../../general/general_model.php');  
include_once (dirname(__FILE__).'../../collection/collection_model.php'); 
include_once (dirname(__FILE__).'../../property/property_model.php'); 
include_once (dirname(__FILE__).'../../user/user_model.php'); 

class TagModel extends Model{
        var $parent;
        public function __construct(){
            $this->parent = get_term_by('name','socialdb_tag','socialdb_tag_type');
        }
	/**
        * function add($data)
        * @param mix $data  O id do colecao
        * @return json  
        * 
        * Autor: Eduardo Humberto 
        */
	public function add($tag_name,$collection_id,$description = ''){
            $is_new = $this->verify_tag($tag_name,$collection_id);
            if (!$is_new) {
                $new_tag = wp_insert_term( trim($tag_name), 'socialdb_tag_type',
                    array('parent' =>  $this->parent->term_id, 'description'=>$description,
                        'slug' =>  sanitize_title(remove_accent($tag_name)).'_'.$collection_id) );
            }else {
               $new_tag = socialdb_term_exists_by_slug(sanitize_title(remove_accent($tag_name)).'_'.$collection_id, 'socialdb_tag_type');
            }
            //apos a insercao
           if(!is_wp_error($new_tag)&&$new_tag['term_id']){// se a tag foi inserida com sucesso
                wp_set_object_terms($collection_id, array((int)$new_tag['term_id']), 'socialdb_tag_type',true);
                $data['success'] = 'true';
                $data['term_id'] = $new_tag['term_id'];
           }else{
                $data['success'] = 'false';
                if($is_new){
                    $data['msg'] = __('There is a tag with this name','tainacan');
                }else{
                    $data['term_id'] = get_term_by('slug',sanitize_title(remove_accent($tag_name)).'_'.$collection_id,'socialdb_tag_type')->term_id;
                    wp_set_object_terms($collection_id, array((int)$data['term_id']), 'socialdb_tag_type',true);
                }
                
           }
           return $data;
	}
        
        
         /**
     * function verify_category($data)
     * @param mix $data  Os dados que serao utilizados para verificar a existencia da categoria
     * metodo que verifica se a categoria realmente exise
     * Autor: Eduardo Humberto 
     */
    public function verify_tag($tag_name,$collection_id) {
        $array = socialdb_term_exists_by_slug(sanitize_title(remove_accent($tag_name)).'_'.$collection_id, 'socialdb_tag_type');
        if (!isset($array['term_id'])) {
            return false;
        } else {
            return true;
        }
    }

    /**
    * function add($data)
    * @param mix $data  O id do colecao
    * @return json
    *
    * Autor: Eduardo Humberto
    */
	public function add_tag_object($tag_id,$collection_id) {
        $result = wp_set_object_terms($collection_id, array((int)$tag_id), 'socialdb_tag_type',true);
        if($result){
            $data['success'] = 'true';
            // Log::addLog( ['user_id' => get_current_user_id(), 'event_type' => 'tags', 'event' => 'add', 'resource_id' => $tag_id] );
        } else {
            $data['success'] = 'false';
        }
        return $data;
	}
	/**
        * function update($data)
        * @param mix $data  Os dados que serao utilizados para atualizar a colecao
        * @return json com os dados atualizados 
        * metodo que atualiza os dados da colecao
        * Autor: Eduardo Humberto 
        */
	public function update($tag_id,$tag_name,$description = '') {
        if(strpos($tag_id, '_tag') !== false) { $tag_id = str_replace('_tag', '', $tag_id); }
        $update_tag = wp_update_term($tag_id, 'socialdb_tag_type', array('description'=>$description, 'name'=>$tag_name));
        if(!is_wp_error($update_tag)&&$update_tag['term_id']){// se a tag foi atualizada com sucesso
            $data['success'] = 'true';
            $data['term_id'] = $update_tag['term_id'];
            // Log::addLog( ['user_id' => get_current_user_id(), 'event_type' => 'tags', 'event' => 'edit', 'resource_id' => $update_tag['term_id']] );
        } else {
            $data['success'] = 'false';
        }
        return json_encode($data);
	}

	/* @param array $data
    /* @return json com os dados da tag excluida.
	/* exclui a tag */
	/* @author Eduardo */
	public function delete($data){;
        if(wp_delete_term( $data['tag_delete_id'], 'socialdb_tag_type')){
            $data['success'] = 'true';
            // Log::addLog( ['user_id' => get_current_user_id(), 'event_type' => 'tags', 'event' => 'delete', 'resource_id' => $data['tag_delete_id']] );
        }else{
            $data['success'] = 'false';
        }
        return json_encode($data);
	}
	
        
}