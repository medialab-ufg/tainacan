<?php
/*
include_once (dirname(__FILE__) . '/../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-includes/wp-db.php');
*/
require_once(dirname(__FILE__) . '../../../event/event_model.php');
require_once(dirname(__FILE__) . '../../../category/category_model.php');

class EventTermCreate extends EventModel {

    public function EventTermCreate() {
        $this->parent = get_term_by('name', 'socialdb_event_term_create', 'socialdb_event_type');
        $this->permission_name = 'socialdb_collection_permission_create_category';
    }

    /**
     * function generate_title($data)
     * @param string $data  Os dados vindo do formulario
     * @return ara  
     * 
     * Autor: Eduardo Humberto 
     */
    public function generate_title($data) {
        $collection = get_post($data['socialdb_event_collection_id']);
        $category_name = $data['socialdb_event_term_suggested_name'];
        $title = __('Create the category ','tainacan').' '.'( <i>'.$category_name.'</i> )'.' '.__(' in the collection ','tainacan').' '.' <b><a href="'.  get_the_permalink($collection->ID).'">'.$collection->post_title.'</a></b> ';
        return $title;
    }

    /**
     * function verify_event($data)
     * @param string $data  Os dados do evento a ser verificado
     * @param string $automatically_verified  Se o evento foi automaticamente verificado
     * @return array  
     * 
     * Autor: Eduardo Humberto 
     */
    public function verify_event($data,$automatically_verified = false) {
       $actual_state = get_post_meta($data['event_id'], 'socialdb_event_confirmed',true);
       if($actual_state!='confirmed'&&$automatically_verified||(isset($data['socialdb_event_confirmed'])&&$data['socialdb_event_confirmed']=='true')){// se o evento foi confirmado automaticamente ou pelos moderadores
           $data = $this->add_category($data['event_id'],$data,$automatically_verified);    
       }elseif($actual_state!='confirmed'){
           $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
           $this->update_event_state('not_confirmed', $data['event_id']);
           $data['msg'] = __('The event was successful NOT confirmed','tainacan');
           $data['type'] = 'success';
           $data['title'] = __('Success','tainacan');
       }else{
           $data['msg'] = __('This event is already confirmed','tainacan');
           $data['type'] = 'info';
           $data['title'] = __('Atention','tainacan');
       }
        $this->notificate_user_email(get_post_meta($data['event_id'], 'socialdb_event_collection_id',true),  get_post_meta($data['event_id'], 'socialdb_event_user_id',true), $data['event_id']);
       return json_encode($data);
    }
      /**
     * function add_category($data)
     * @param string $event_id  O id do evento que vai pegar os metas
     * @param string $data  Os dados do evento a ser verificado
     * @param string $automatically_verified  Se o evento foi automaticamente verificado
     * @return array    
     * 
     * Autor: Eduardo Humberto 
     */
    public function add_category($event_id,$data,$automatically_verified) {
        $categoryModel = new CategoryModel();
        // coloco os dados necessarios para criacao da categoria
        $data['category_name'] = get_post_meta($event_id, 'socialdb_event_term_suggested_name',true) ;
        $data['category_description'] = get_post_meta($event_id, 'socialdb_event_term_description',true) ;
        $data['category_des'] = get_post_meta($event_id, 'socialdb_event_term_suggested_name',true) ;
        $data['collection_id'] = get_post_meta($event_id, 'socialdb_event_collection_id',true) ;
        $data['category_parent_id'] = get_post_meta($event_id, 'socialdb_event_term_parent',true) ;
       // chamo a funcao do model de categoria para fazer a insercao e/ou vincular como faceta
        $result = json_decode($categoryModel->add($data));
        if($data['category_parent_id']=='socialdb_category' || $data['category_parent_id']=='socialdb_taxonomy'){
            $categoryModel->add_facet($result->term_id, $data['collection_id']);
        }
        // verifying if is everything all right
        if ($result->success=='true') {
            $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
            $this->update_event_state('confirmed', $data['event_id']);
            $data['msg'] = __('The event was successful','tainacan');
            $data['type'] = 'success';
            $data['title'] = __('Success','tainacan');
        } else {
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
            $data['msg'] = __('Category already exists!','tainacan');
            $data['type'] = 'error';
            $data['title'] = 'Erro';
        }
        //$this->notificate_user_email($data['collection_id'],  get_current_user_id(), $event_id);
        return $data;
    }

}
