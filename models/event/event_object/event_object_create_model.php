<?php
/*
include_once (dirname(__FILE__) . '/../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-includes/wp-db.php');
*/
require_once(dirname(__FILE__) . '../../../event/event_model.php');
require_once(dirname(__FILE__) . '../../../category/category_model.php');
require_once(dirname(__FILE__) . '../../../property/property_model.php');
class EventObjectCreateModel extends EventModel {

    public function __construct() {
        $this->parent = get_term_by('name', 'socialdb_event_object_create', 'socialdb_event_type');
        $this->permission_name = 'socialdb_collection_permission_create_object';
    }

    /**
     * function generate_title($data)
     * @param string $data  Os dados vindo do formulario
     * @return ara  
     * 
     * Autor: Eduardo Humberto 
     */
    public function generate_title($data) {
        $object = get_post($data['socialdb_event_object_item_id']);
        if($object->post_status == 'publish'):
            $title = __('Add object ','tainacan') .' '. $object->post_title;
        else:    
            $title = __('Create the object ','tainacan') .' '. $object->post_title;
        endif;
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
           $data = $this->update_post_status(get_post_meta($data['event_id'], 'socialdb_event_object_item_id',true),$data,$automatically_verified);    
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
        // Update the post
        if(get_post($object_id)->post_status!='publish'){
            $object = array(
                'ID' => $object_id,
                'post_status' => 'publish'
            );
            // Update the post into the database
            $value = wp_update_post($object);
            delete_user_meta(get_current_user_id(), 'socialdb_collection_' . $collection_id . '_betatext');
            delete_user_meta(get_current_user_id(), 'socialdb_collection_' . $collection_id . '_betafile');
        }else{
            wp_set_object_terms($object_id, array((int) $this->get_category_root_of($collection_id)), 'socialdb_category_type',true);
            add_post_meta($collection_id, 'socialdb_collection_vinculated_object', $object_id);
            $value = $object_id;
        }
        $category_model = new CategoryModel();
        $all_properties = $category_model->get_properties($data['collection_id'], []);
        $this->insert_autoincrement($all_properties,$object_id);
        if ($value>0) {
            $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
            $this->update_event_state('confirmed', $data['event_id']);
            $data['msg'] = __('The event was successful','tainacan');
            $data['type'] = 'success';
            $data['title'] = __('Success','tainacan');
        } else {
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
            $data['msg'] = __('This object does not exist anymore','tainacan');
            $data['type'] = 'error';
            $data['title'] = __('Error','tainacan');
        }
        //$this->notificate_user_email( $collection_id,  get_current_user_id(), $data['event_id']);
        return $data;
    }
    
     /**
     * function insert_autoincrement($properties,$object_id)
     * @param string $properties  O id do objeto a ser alterado o post status
     * @param string $object_id  Os dados do evento a ser verificado
     * @return void    
     * 
     * Autor: Eduardo Humberto 
     */
    public function insert_autoincrement($properties,$object_id) {
        $property_model = new PropertyModel();
        if (is_array($properties)) {
            foreach ($properties as $property_id) {
                $type = $property_model->get_property_type($property_id); // pego o tipo da propriedade
                $all_data = $property_model->get_all_property($property_id, true); // pego todos os dados possiveis da propriedade
               if ($type == 'socialdb_property_data') {
                    //se caso for autoincrement
                    if($all_data['metas']['socialdb_property_data_widget']=='autoincrement'){
                        update_post_meta($object_id, 'socialdb_property_' . $property_id,$this->get_last_counter($property_id)+1);
                    }
                }
            }
        }
    }

}
