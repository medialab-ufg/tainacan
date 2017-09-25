<?php
/*
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
*/
require_once(dirname(__FILE__) . '../../../event/event_model.php');
require_once(dirname(__FILE__) . '../../../comment/comment_model.php');

class EventCommentCreate extends EventModel {

    public function EventCommentCreate() {
        $this->parent = get_term_by('name', 'socialdb_event_comment_create', 'socialdb_event_type');
        $this->permission_name = 'socialdb_collection_permission_create_comment';
    }

    /**
     * function generate_title($data)
     * @param string $data  Os dados vindo do formulario
     * @return ara  
     * 
     * Autor: Eduardo Humberto 
     */
    public function generate_title($data) {
        $object = get_post($data['socialdb_event_comment_create_object_id']);
        $content = $data['socialdb_event_comment_create_content'];
        $collection = get_post($data['socialdb_event_collection_id']);
        $title = __('Create the Comment  ','tainacan').'('.$content.')'.__(' in the object ','tainacan').'<b><a href="'.  get_the_permalink($object->ID).'">'. $object->post_title.'</a></b> '.__('from collection','tainacan').' '.' <b><a href="'.  get_the_permalink($collection->ID).'">'.$collection->post_title.'</a></b>';
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
           $data = $this->add_comment($data['event_id'],$data,$automatically_verified);    
       }elseif($actual_state!='confirmed'){
           $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
           $this->update_event_state('not_confirmed', $data['event_id']);
           $data['msg'] = __('The event was successful NOT confirmed','tainacan');
           $data['type'] = 'success';
           $data['title'] = __('Success','tainacan');
       }else{
           $data['msg'] = __('This event is already confirmed','tainacan');
           $data['type'] = 'info';
           $data['title'] = __('Attention','tainacan');
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
    public function add_comment($event_id,$data,$automatically_verified) {
        $commentModel = new CommentModel();
        $comment_content = get_post_meta($event_id,'socialdb_event_comment_create_content', true);
        $comment_author_name = get_post_meta($event_id,'socialdb_event_comment_author_name',true);
        $comment_author_email = get_post_meta($event_id,'socialdb_event_comment_author_email', true);
        $comment_author_website = get_post_meta($event_id,'socialdb_event_comment_author_website',true);
        $comment_term_id = get_post_meta($event_id,'socialdb_event_comment_term_id',true);
        $object_id = get_post_meta($event_id,'socialdb_event_comment_create_object_id', true);
        $parent_id = get_post_meta($event_id,'socialdb_event_comment_parent',true);
        $collection_id = get_post_meta($event_id,'socialdb_event_collection_id',true);
        $user_id = get_post_meta($event_id,'socialdb_event_user_id',true);
        
        if($user_id&&$user_id!=0){
           $result = $commentModel->add($object_id, $comment_content, $parent_id, '', '', '', $user_id,$comment_term_id);
           $commentarr['comment_ID'] = $result['comment_id'];
           $commentarr['comment_approved'] = 1;
           wp_update_comment( $commentarr );
        }elseif(trim($comment_author_name)!==''&&$comment_author_email!==''&&$comment_content!==''){
           $result = $commentModel->add($object_id, $comment_content, $parent_id, $comment_author_name, $comment_author_email, $comment_author_website, $user_id,$comment_term_id);
           $commentarr['comment_ID'] = $result['comment_id'];
           $commentarr['comment_approved'] = 1;
           wp_update_comment( $commentarr );
        }
        
        if (isset($result['comment_id'])) {
            $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
            $this->update_event_state('confirmed', $data['event_id']);

            $logData = ['collection_id' => $collection_id, 'item_id' => $object_id, 'resource_id' => $result['comment_id'],
                'user_id' => $user_id, 'event_type' => 'comment', 'event' => 'add' ];
            Log::addLog($logData);
            
            $data['msg'] = __('The event was successful','tainacan');
            $data['type'] = 'success';
            $data['title'] =  __('Success','tainacan');
        } else {
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
            if(isset($result['msg'])){
               $data['msg'] = $result['msg'];
            }else{
               $data['msg'] = __('Your comment is invalid','tainacan');
            }
            $data['type'] = 'error';
            $data['title'] = 'Erro';
        }
       // $this->notificate_user_email($collection_id,  get_current_user_id(), $event_id);
        return $data;
    }

}
