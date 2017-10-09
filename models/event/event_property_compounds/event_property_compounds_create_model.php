<?php
/*
include_once (dirname(__FILE__) . '/../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-includes/wp-db.php');
*/
require_once(dirname(__FILE__) . '../../../event/event_model.php');
require_once(dirname(__FILE__) . '../../../property/property_model.php');

class EventPropertyCompoundsCreate extends EventModel {

    public function __construct() {
        $this->parent = get_term_by('name', 'socialdb_event_property_compounds_create', 'socialdb_event_type');
        $this->permission_name = 'socialdb_collection_permission_create_property_data';
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
        $property_name = $data['socialdb_event_property_compounds_create_name'];
        $title = __('Create the compounds property ','tainacan').' ( <i>'.$property_name.'</i> )'.__(' in the collection','tainacan').' '.' <b><a href="'.  get_the_permalink($collection->ID).'">'.$collection->post_title.'</a></b> ';
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
           $data = $this->add_property($data['event_id'],$data,$automatically_verified);    
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
     * @param string $event_id  O id do evento que vai pegar os metas
     * @param string $data  Os dados do evento a ser verificado
     * @param string $automatically_verified  Se o evento foi automaticamente verificado
     * @return array    
     * 
     * Autor: Eduardo Humberto 
     */
    public function add_property($event_id,$data,$automatically_verified) {
        $propertyModel = new PropertyModel();
        // coloco os dados necessarios para criacao da propriedade
        $name = get_post_meta($event_id, 'socialdb_event_property_compounds_create_name',true) ;
        $collection_id = get_post_meta($event_id, 'socialdb_event_collection_id',true) ;
        $cardinality = get_post_meta($event_id, 'socialdb_event_property_compounds_create_cardinality',true) ;
        $properties_id = get_post_meta($event_id, 'socialdb_event_property_compounds_create_properties_id',true) ;
        $required = get_post_meta($event_id, 'socialdb_event_property_compounds_create_required',true) ;
        $help = get_post_meta($event_id, 'socialdb_event_property_compounds_create_help',true) ;   
        $property_id = get_post_meta($event_id, 'socialdb_event_property_compounds_create_id',true) ;
        $tab_id = get_post_meta($event_id, 'socialdb_event_property_tab',true) ;
        $visualization = get_post_meta($event_id, 'socialdb_event_property_visualization',true) ;
        //inserindo o metadado
        $property_category_id = get_post_meta($event_id, 'socialdb_event_property_compounds_create_category_root_id',true) ;
        // chamo a funcao do model de propriedade para fazer a insercao
        if(!$property_id||empty($property_id)||!is_numeric($property_id)){
            $result = json_decode($propertyModel->add_property_compounds($name, $collection_id, $property_category_id, $properties_id,$cardinality, $help, $required,$visualization,$tab_id));
            if(isset($result->property_id)){
                do_action('after_event_add_property_compounds',$result->property_id,$event_id);
                $propertyModel->update_tab_organization($collection_id,$tab_id, $result->property_id);
            }
        }else{
            add_term_meta($property_category_id, 'socialdb_category_property_id',$property_id);
            //metadado que mostra que a 
            add_term_meta($property_id, 'socialdb_property_used_by_categories', $property_category_id);
            $this->set_approval_metas($event_id, $data['socialdb_event_observation'], $automatically_verified);
            $this->update_event_state('confirmed', $event_id);
            $data['msg'] = __('The event was successful','tainacan');
            $data['type'] = 'success';
            $data['title'] = __('Success','tainacan');
            return $data;
        }
        //$data['results'] = $result;
        $data['new_property_id'] = $result->property_id;
        // verifying if is everything all right
        if ($result->success!=='false') {
            $this->set_approval_metas($event_id, $data['socialdb_event_observation'], $automatically_verified);
            $this->update_event_state('confirmed',$event_id);
            $data['msg'] = __('The event was successful','tainacan');
            $data['type'] = 'success';
            $data['title'] = __('Success','tainacan');
        } else {
            $this->update_event_state('invalid', $event_id); // seto a o evento como invalido
            if(isset($result->msg)):
             $data['msg'] = $result->msg;
            else:
              $data['msg'] = __('Please fill the fields correctly!','tainacan');  
            endif;
            $data['type'] = 'error';
            $data['title'] = 'Erro';
        }
        // $this->notificate_user_email( $data['collection_id'],  get_current_user_id(), $event_id);
        return $data;
    }

}
