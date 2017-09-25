<?php
/*
include_once (dirname(__FILE__) . '/../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-includes/wp-db.php');
*/
require_once(dirname(__FILE__) . '../../../event/event_model.php');
require_once(dirname(__FILE__) . '../../../tag/tag_model.php');

class EventTagCreate extends EventModel {

    public function EventTagCreate() {
        $this->parent = get_term_by('name', 'socialdb_event_tag_create', 'socialdb_event_type');
        $this->permission_name = 'socialdb_collection_permission_create_tags';
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
        $tag_name = $data['socialdb_event_tag_suggested_name'];
        $title = __('Create the tag ','tainacan').'( <b>'.$tag_name.'</b> )'.__(' in the collection ','tainacan').' <b><a href="'.  get_the_permalink($collection->ID).'">'.$collection->post_title.'</a></b> ';
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
    public function verify_event($data,$automatically_verified = false)
    {
       $actual_state = get_post_meta($data['event_id'], 'socialdb_event_confirmed',true);
       if($actual_state != 'confirmed' && $automatically_verified || (isset($data['socialdb_event_confirmed']) && $data['socialdb_event_confirmed']=='true'))// se o evento foi confirmado automaticamente ou pelos moderadores
       {
           $data = $this->add_tag($data['event_id'],$data,$automatically_verified);    
       }
       elseif($actual_state!='confirmed')
       {
           $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
           $this->update_event_state('not_confirmed', $data['event_id']);
           $data['msg'] = __('The event was successful NOT confirmed','tainacan');
           $data['type'] = 'success';
           $data['title'] = __('Success','tainacan');
       }
       else
       {
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
    public function add_tag($event_id,$data,$automatically_verified)
    {
        $tagModel = new TagModel();
        $tag_name = get_post_meta($event_id,'socialdb_event_tag_suggested_name', true);
        $tag_description = get_post_meta($event_id, 'socialdb_event_tag_description',true) ;
        $collection_id = get_post_meta($event_id,'socialdb_event_collection_id',true);
        $tags = explode(',', $tag_name);
        foreach ($tags as $tag_name)
        {
            $result = $tagModel->add($tag_name, $collection_id,$tag_description);
            if (isset($result['term_id']))
            {
                $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
                $this->update_event_state('confirmed', $data['event_id']);
                $logData = ['collection_id' => $data['socialdb_event_collection_id'], 'resource_id' => $result['term_id'],
                    'user_id' => $data['socialdb_event_user_id'], 'event_type' => 'tags', 'event' => 'add' ];
                Log::addLog($logData);

                $data['msg'] = __('The event was successful','tainacan');
                $data['type'] = 'success';
                $data['title'] = __('Success','tainacan');
                $data['collection_id'] = $data['socialdb_event_collection_id'];
                $data['user_id'] = $data['socialdb_event_user_id'];
                $data['term_id'][] = $result['term_id'];
            }elseif(trim($tag_name)==''){
                $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
                $data['type'] = 'error';
                $data['title'] = 'Erro';
                $data['msg'] = __('Tag name invalid','tainacan');
            }else {
                $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
                if(isset($result['msg'])){
                   $data['msg'] = $result['msg'];
                }else{
                   $data['msg'] = __('Tag name invalid does not exist anymore','tainacan');
                }
                $data['type'] = 'error';
                $data['title'] = 'Erro';
            }
            
        }    
       // $this->notificate_user_email($collection_id,  get_current_user_id(), $event_id);
        return $data;
    }

}
