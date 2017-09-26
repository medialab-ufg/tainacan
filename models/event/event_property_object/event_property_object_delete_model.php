<?php
/*
include_once (dirname(__FILE__) . '/../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-includes/wp-db.php');
*/
require_once(dirname(__FILE__) . '../../../event/event_model.php');
require_once(dirname(__FILE__) . '../../../property/property_model.php');

class EventPropertyObjectDelete extends EventModel {

    public function EventPropertyObjectDelete() {
        $this->parent = get_term_by('name', 'socialdb_event_property_object_delete', 'socialdb_event_type');
        $this->permission_name = 'socialdb_collection_permission_delete_property_object';
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
        $property = get_term_by('id',$data['socialdb_event_property_object_delete_id'],'socialdb_property_type');
        $title = __('Delete the object property ','tainacan').' ( <i>'.$property->name.'</i> ) '.__(' in the collection ','tainacan').' '.' <b><a href="'.  get_the_permalink($collection->ID).'">'.$collection->post_title.'</a></b> ';;
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
           $data = $this->delete_property($data['event_id'],$data,$automatically_verified);    
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
     * function delete_property($data)
     * @param string $event_id  O id do evento que vai pegar os metas
     * @param string $data  Os dados do evento a ser verificado
     * @param string $automatically_verified  Se o evento foi automaticamente verificado
     * @return array    
     * 
     * Autor: Eduardo Humberto 
     */
    public function delete_property($event_id,$data,$automatically_verified) {
        $propertyModel = new PropertyModel();
        // coloco os dados necessarios para criacao da propriedade
        $data['collection_id'] = get_post_meta($event_id, 'socialdb_event_collection_id',true) ;
        $data['property_delete_id'] = get_post_meta($event_id, 'socialdb_event_property_object_delete_id',true) ;
        $data['property_category_id'] = get_post_meta($event_id, 'socialdb_event_property_object_delete_category_root_id',true) ;
         $categories_used = get_term_meta($data['property_delete_id'], 'socialdb_property_used_by_categories') ;
        // chamo a funcao do model de propriedade para fazer a exclusao
        $verify = get_term_by('id', $data['property_delete_id'], 'socialdb_property_type');
        // verifico se a propriedade nao esteja apenas referenciando outra de outra
        // categoria desta forma ele exclui definitivamente
        if(!$categories_used||
                (is_array($categories_used)&&empty(array_filter($categories_used)))||
                    (is_array($categories_used)&&!in_array($data['property_category_id'], $categories_used))
                ){
            $propertyModel->delete($data);
            if(isset($verify->term_id)){
                do_action('after_event_delete_property_object',$verify->term_id,$event_id);
            }
        }else{
             delete_term_meta($data['property_category_id'], 'socialdb_category_property_id', $data['property_delete_id']);
             delete_term_meta($data['property_delete_id'], 'socialdb_property_used_by_categories',$data['property_category_id']); // e entao removo do array de categorias que utilizam esta propriedade
        }
        // verifying if is everything all right
        if ($verify){
            $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
            $this->update_event_state('confirmed', $data['event_id']);
            $data['msg'] = __('The event was successful','tainacan');
            $data['type'] = 'success';
            $data['title'] = __('Success','tainacan');
        } else {
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
            $data['msg'] = __('This property relationship does not exist anymore','tainacan');
            $data['type'] = 'error';
            $data['title'] = 'Erro';
        }
         $this->notificate_user_email( $data['collection_id'],  get_current_user_id(), $event_id);
        return $data;
    }

}
