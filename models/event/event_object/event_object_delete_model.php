<?php
require_once(dirname(__FILE__) . '../../../event/event_model.php');

class EventObjectDeleteModel extends EventModel {

    public function __construct() {
        $this->parent = get_term_by('name', 'socialdb_event_object_delete', 'socialdb_event_type');
        $this->permission_name = 'socialdb_collection_permission_delete_object';
    }

    /**
     * function generate_title($data)
     * @param string $data  Os dados vindo do formulario
     * @return ara
     *
     * 
     * Autor: Eduardo Humberto 
     */
    public function generate_title($data) {
        $object = get_post($data['socialdb_event_object_item_id']);
        $title = __('Delete the object ','tainacan').' '. $object->post_title;
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

        if (has_filter('tainacan_alter_permission_actions')) {
            if(is_null($actual_state) && $data['socialdb_event_confirmed']=='true') {
                $this->update_event_state('confirmed', $data['event_id']);
                $actual_state = get_post_meta($data['event_id'], 'socialdb_event_confirmed',true);
            }
        }

       // se o evento foi confirmado automaticamente ou pelos moderadores
       if( $actual_state!='confirmed'&&$automatically_verified  ||(isset($data['socialdb_event_confirmed'])&&$data['socialdb_event_confirmed']=='true') ) {
           $data = $this->update_post_status(get_post_meta($data['event_id'], 'socialdb_event_object_item_id',true),$data,$automatically_verified);    
       } elseif($actual_state!='confirmed'){
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
     * function update_post_status($data)
     * @param string $object_id  O id do objeto a ser alterado o post status
     * @param string $data  Os dados do evento a ser verificado
     * @param string $automatically_verified  Se o evento foi automaticamente verificado
     * @return array    
     * 
     * Autor: Eduardo Humberto 
     */
    public function update_post_status($object_id,$data,$automatically_verified) {
        $collection_id = get_post_meta($data['event_id'],'socialdb_event_collection_id',true);
        // verifico se o item nao eh apenas vinculado
        $array =  get_post_meta($collection_id, 'socialdb_collection_vinculated_object');
        if($array && is_array($array) && in_array($object_id, $array)) {
            delete_post_meta($collection_id, 'socialdb_collection_vinculated_object',$object_id);
            $result = wp_remove_object_terms( $object_id,(int) $this->get_category_root_of($collection_id),'socialdb_category_type');
            $value = $object_id;
        } else {
            // Update the post
            $object = array(
                'ID' => $object_id,
                'post_status' => 'draft'
            );
            // Update the post into the database
            $value = wp_update_post($object);

            if(has_filter('tainacan_delete_item_perm')) {
                return apply_filters('tainacan_delete_item_perm', $value, $collection_id);
            }
        }
        //verificando se todo
        if ($value > 0) {
            if ( !array_key_exists("socialdb_event_observation", $data) ) {
                $_event_observation = "";
            } else {
                $_event_observation = $data['socialdb_event_observation'];
            }
            $this->set_approval_metas($data['event_id'], $_event_observation, $automatically_verified);
            $this->update_event_state('confirmed', $data['event_id']);
            $data['msg'] = __('The event was successful','tainacan');
            $data['type'] = 'success';
           $data['title'] = __('Success','tainacan');
           
           //***** BEGIN CHECK SOCIAL NETWORKS *****//
           // YOUTUBE
           $mapping_id_youtube = $this->get_post_by_title('socialdb_channel_youtube', $collection_id, 'youtube');
           $getCurrentIds_youtube = unserialize(get_post_meta($mapping_id_youtube, 'socialdb_channel_youtube_inserted_ids', true));
           if(isset($getCurrentIds_youtube[$object_id])){
               unset($getCurrentIds_youtube[$object_id]);
               update_post_meta($mapping_id_youtube, 'socialdb_channel_youtube_inserted_ids', serialize($getCurrentIds_youtube));
           }
           
           //FACEBOOK
           $mapping_id_facebook = $this->get_post_by_title('socialdb_channel_facebook', $collection_id, 'facebook');
           $getCurrentIds_facebook = unserialize(get_post_meta($mapping_id_facebook, 'socialdb_channel_facebook_inserted_ids', true));
           if(isset($getCurrentIds_facebook[$object_id])){
               unset($getCurrentIds_facebook[$object_id]);
               update_post_meta($mapping_id_facebook, 'socialdb_channel_facebook_inserted_ids', serialize($getCurrentIds_facebook));
           }
           
           //INSTAGRAM
           $mapping_id_instagram = $this->get_post_by_title('socialdb_channel_instagram', $collection_id, 'instagram');
           $getCurrentIds_instagram = unserialize(get_post_meta($mapping_id_instagram, 'socialdb_channel_instagram_inserted_ids', true));
           if(isset($getCurrentIds_instagram[$object_id])){
               unset($getCurrentIds_instagram[$object_id]);
               update_post_meta($mapping_id_instagram, 'socialdb_channel_instagram_inserted_ids', serialize($getCurrentIds_instagram));
           }
           
           //FLICKR
           $mapping_id_flickr = $this->get_post_by_title('socialdb_channel_flickr', $collection_id, 'flickr');
           $getCurrentIds_flickr = unserialize(get_post_meta($mapping_id_flickr, 'socialdb_channel_flickr_inserted_ids', true));
           if(isset($getCurrentIds_flickr[$object_id])){
               unset($getCurrentIds_flickr[$object_id]);
               update_post_meta($mapping_id_flickr, 'socialdb_channel_flickr_inserted_ids', serialize($getCurrentIds_flickr));
           }
           //***** END CHECK SOCIAL NETWORKS *****//
           
        } else {
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
            $data['msg'] = __('This object does not exist anymore','tainacan');
            $data['type'] = 'error';
            $data['title'] = 'Erro';
        }
        //$this->notificate_user_email( $collection_id,  get_current_user_id(), $data['event_id']);
        return $data;
    }

}
