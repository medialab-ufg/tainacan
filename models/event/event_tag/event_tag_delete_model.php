<?php
/*
include_once (dirname(__FILE__) . '/../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-includes/wp-db.php');
*/
require_once(dirname(__FILE__) . '../../../event/event_model.php');
require_once(dirname(__FILE__) . '../../../category/category_model.php');

class EventTagDelete extends EventModel {

    public function EventTagDelete() {
        $this->parent = get_term_by('name', 'socialdb_event_tag_delete', 'socialdb_event_type');
        $this->permission_name = 'socialdb_collection_permission_delete_tags';
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
        $category = get_term_by('id',$data['socialdb_event_term_id'],'socialdb_tag_type');
        $title = __('Delete the tag ','tainacan').'( <b>'.$category->name.'</b> )'.__(' in the collection ','tainacan').' <b><a href="'.  get_the_permalink($collection->ID).'">'.$collection->post_title.'</a></b> ';
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
           $data = $this->delete_category($data['event_id'],$data,$automatically_verified);    
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
     * function delete_category($data)
     * @param string $event_id  O id do evento que vai pegar os metas
     * @param string $data  Os dados do evento a ser verificado
     * @param string $automatically_verified  Se o evento foi automaticamente verificado
     * @return array    
     * 
     * Autor: Eduardo Humberto 
     */
    public function delete_category($event_id,$data,$automatically_verified) {
                 
      $tagModel = new TagModel();        
        $tag_id = get_post_meta($event_id,'socialdb_event_tag_id',true);
        $collection_id = get_post_meta($event_id,'socialdb_event_collection_id',true);
        
        $result = $tagModel ->delete(array('tag_delete_id' => $tag_id));
        //// se o usuario removeu uma categoria de faceta para embaixo de outra categoria
        // verifying if is everything all right
        if ($result){
            $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
            $this->update_event_state('confirmed', $data['event_id']);
            $data['msg'] = __('The event was successful','tainacan');
            $data['type'] = 'success';
              $data['title'] = __('Success','tainacan');
        } else {
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
            $data['msg'] = __('This category does not exist anymore','tainacan');
            $data['type'] = 'error';
            $data['title'] = 'Erro';
        }
        //$this->notificate_user_email($collection_id,  get_current_user_id(), $event_id);
        return $data;
    }

}
