<?php
/*
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
*/
require_once(dirname(__FILE__) . '../../../event/event_model.php');
require_once(dirname(__FILE__) . '../../../comment/comment_model.php');

class EventCommentDelete extends EventModel {

    public function EventCommentDelete() {
        $this->parent = get_term_by('name', 'socialdb_event_comment_delete', 'socialdb_event_type');
        $this->permission_name = 'socialdb_collection_permission_delete_comment';
    }

    /**
     * function generate_title($data)
     * @param string $data  Os dados vindo do formulario
     * @return ara  
     * 
     * Autor: Eduardo Humberto 
     */
    public function generate_title($data) {
        $comment = get_comment($data['socialdb_event_comment_delete_id'] );
        $object = get_post($comment->comment_post_ID);
        $collection = get_post($data['socialdb_event_collection_id']);
        $title = __('Delete the Comment ','tainacan').'( <i>'.$comment->comment_content .'</i> ) '.__('in the item','tainacan').' <b><a href="'.  get_the_permalink($object->ID).'">'. $object->post_title.'</a></b> '.__('from collection','tainacan').' '.' <b><a href="'.  get_the_permalink($collection->ID).'">'.$collection->post_title.'</a></b>' ;
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
           $data = $this->delete_comment($data['event_id'],$data,$automatically_verified);    
       }elseif($actual_state!='confirmed'){
           $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
           $this->update_event_state('not_confirmed', $data['event_id']);
           $data['msg'] = __('The event was successful NOT confirmed','tainacan');
           $data['type'] = 'success';
           $data['title'] = __('Success','tainacan');
       }else{
           $data['msg'] = __('This event is already confirmed');
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
    public function delete_comment($event_id,$data,$automatically_verified) {
        $commentModel = new CommentModel();
        $collection_id = get_post_meta($event_id,'socialdb_event_collection_id',true);
        $comment_id = get_post_meta($event_id,'socialdb_event_comment_delete_id',true);
        $result = $commentModel->delete($comment_id);
       
        if (isset($result['success'])&&($result['success']=='true')) {
            $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
            $this->update_event_state('confirmed', $data['event_id']);
            $data['msg'] = __('The event was successful','tainacan');
            $data['type'] = 'success';
            $data['title'] = __('Success','tainacan');
        } else {
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
            if(isset($result['msg'])){
               $data['msg'] = $result['msg'];
            }else{
               $data['msg'] = __('Tag name invalid does not exist anymore','tainacan');
            }
            $data['type'] = 'error';
            $data['title'] = 'Erro';
        }
        //$this->notificate_user_email($collection_id,  get_current_user_id(), $event_id);
        return $data;
    }

}
