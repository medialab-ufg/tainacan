<?php

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../../event/event_model.php');

class EventCollectionDeleteModel extends EventModel {

    public function EventCollectionDeleteModel() {
        $this->parent = get_term_by('name', 'socialdb_event_collection_delete', 'socialdb_event_type');
        $this->permission_name = 'socialdb_collection_permission_delete_collection';
    }

    /**
     * function generate_title($data)
     * @param string $data  Os dados vindo do formulario
     * @return ara  
     * 
     * Autor: Eduardo Humberto 
     */
    public function generate_title($data) {
        $object = get_post($data['socialdb_event_delete_collection_id']);
        $title = __('Delete the Collection ','tainacan') .' '. $object->post_title;
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
           $data = $this->update_post_status(get_post_meta($data['event_id'], 'socialdb_event_delete_collection_id',true),$data,$automatically_verified);    
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
     * function update_post_status($data)
     * @param string $object_id  O id do objeto a ser alterado o post status
     * @param string $data  Os dados do evento a ser verificado
     * @param string $automatically_verified  Se o evento foi automaticamente verificado
     * @return array    
     * 
     * Autor: Eduardo Humberto 
     */
    public function update_post_status($collection_delete_id,$data,$automatically_verified) {
         // $collection_id = get_post_meta($data['event_id'],'socialdb_event_collection_id',true);
        // Update the post
        $object = array(
            'ID' => $collection_delete_id,
            'post_status' => 'draft'
        );

        /*$collection_items = $this->get_collection_posts($collection_delete_id, "ID");
        foreach ( $collection_items as $item) {
            wp_update_post( ['ID' => $item->ID, 'post_status' => 'draft'] );
        }*/

        // Update the post into the database
        $value = wp_update_post($object);
        if ($value>0) {
            $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
            $this->update_event_state('confirmed', $data['event_id']);
            $data['msg'] = __('The event was successful','tainacan');
            $data['type'] = 'success';
            $data['title'] = 'Success';
            $data['url'] = get_the_permalink(get_option('collection_root_id'));
        } else {
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
            $data['msg'] = __('This object does not exist anymore','tainacan');
            $data['type'] = 'error';
            $data['title'] = 'Error';
        }
        //$this->notificate_user_email( $collection_id,  get_current_user_id(), $data['event_id']);
        return $data;
    }

}
