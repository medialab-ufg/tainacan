<?php
/*
include_once (dirname(__FILE__) . '/../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-includes/wp-db.php');
*/
require_once(dirname(__FILE__) . '../../../event/event_model.php');

class EventPropertyCompoundsEditValue extends EventModel {

    public function __construct() {
        $this->parent = get_term_by('name', 'socialdb_event_property_compounds_edit_value', 'socialdb_event_type');
        $this->permission_name = 'socialdb_collection_permission_edit_property_data_value';
    }

      /**
     * function generate_title($data)
     * @param string $data  Os dados vindo do formulario
     * @return ara  
     * 
     * Autor: Eduardo Humberto 
     */
    public function generate_title($data) {
        $object = get_post($data['socialdb_event_property_compounds_edit_value_object_id']);
        $property = get_term_by('id', $data['socialdb_event_property_compounds_edit_value_property_id'], 'socialdb_property_type');
        if($data['socialdb_event_property_compounds_edit_value_attribute_value']!=''){
            $text = $data['socialdb_event_property_data_edit_value_attribute_value'];
            $title = __('Set the values: ','tainacan').' ( <i>'.$text.'</i> ) '.__(' of the data property ','tainacan').' <b>'.$property->name.'</b> '
                . __(' in the the object ','tainacan') . '<b><a href="'.  get_the_permalink($object->ID).'">'. $object->post_title.'</a></b>';
            
        }else{
            $title = __('Delete all values of the compounds property ','tainacan').' <b>'.$property->name.'</b> '
                . __(' in the the object ','tainacan') . '<b><a href="'.  get_the_permalink($object->ID).'">'. $object->post_title.'</a></b>';
        }
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
           $data = $this->update_property_value($data['event_id'],$data,$automatically_verified);    
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
     * function update_post_status($data)
     * @param string $event_id  O id do evento
     * @param string $data  Os dados do evento a ser verificado
     * @param string $automatically_verified  Se o evento foi automaticamente verificado
     * @return array    
     * 
     * Autor: Eduardo Humberto 
     */
    public function update_property_value($event_id,$data,$automatically_verified) {
        $collection_id = get_post_meta($event_id,'socialdb_event_collection_id',true);
        $object_id = get_post_meta($event_id, 'socialdb_event_property_compounds_edit_value_object_id',true);
        $property = get_post_meta($event_id, 'socialdb_event_property_compounds_edit_value_property_id',true);
        $properties_compound = get_term_meta($property, 'socialdb_property_compounds_properties_id',true);
        $row = get_post_meta($event_id, 'socialdb_event_property_compounds_edit_value_row',true);
        //os novos valores a serem atribuidos
        $value = get_post_meta($event_id, 'socialdb_event_property_compounds_edit_value_attribute_value',true);
        //verifico se nao esta apenas limpando
        if($value && $value!= '' && $properties_compound){
            // os novos ids metas caso tenha uma categoria
            $new_ids_meta = [];
            // coloco os valores em um array
            $values = explode(',', $value);
            //busco os ids de estao localizados os metas
            $ids_metas = array_values(array_filter(explode(',', get_post_meta($object_id,'socialdb_property_'.$property.'_'.$row,true)))); 
            // o id das propriedades participantes
            $properties_compounds_id = array_values(array_filter(explode(',', $properties_compound)));
            //percorro as propriedades que compoe a composta
            foreach ($properties_compounds_id as $key => $property_compounds_id) {
                $result = true;
                //busco o tipo da propriedade
                $type = $this->get_property_type_hierachy($property_compounds_id);
                if($type =='socialdb_property_data' || $type =='socialdb_property_object'){
                    //array que sera usado atualizar o indice da propriedade composta
                    if(empty($ids_metas)||(isset($ids_metas[$key])&&$ids_metas[$key]=='')){
                        $new_ids_meta[] = $this->sdb_add_post_meta($object_id, "socialdb_property_$property_compounds_id", $values[$key]);
                        $this->set_common_field_values($object_id, "socialdb_property_$property_compounds_id",$values[$key]);
                    }else{
                        $new_ids_meta[] = $ids_metas[$key];
                        //atualizo o postmeta com o valor alterado
                        $this->sdb_update_post_meta($ids_metas[$key], $values[$key]);
                        $this->set_common_field_values($object_id, "socialdb_property_$property_compounds_id",$values[$key]);
                    }
                    
                }else{
                    //removo o antigo
                    if(empty($ids_metas))
                        wp_remove_object_terms( $object_id, get_term_by('id', str_replace('_cat', '', $ids_metas[$key]),'socialdb_category_type')->term_id,'socialdb_category_type');
                    //adiciono o novo
                    wp_set_object_terms( (int) $object_id,(int)$values[$key],'socialdb_category_type',true);
                    $this->set_common_field_values($object_id, "socialdb_propertyterm_$property_compounds_id",$values[$key]);
                    $new_ids_meta[] = $values[$key].'_cat';
                }
            }
            update_post_meta($object_id,'socialdb_property_'.$property.'_'.$row, implode(',', $new_ids_meta));
        }else if($properties_compound){
            //busco os ids de estao localizados os metas
            $ids_metas = explode(',', get_post_meta($object_id,'socialdb_property_'.$property.'_'.$row,true)); 
            // o id das propriedades participantes
            $properties_compounds_id = explode(',', $properties_compound);
            //percorro as propriedades que compoe a composta
            foreach ($properties_compounds_id as $key => $property_compounds_id) {
                $result = true;
                //busco o tipo da propriedade
                $type = $this->get_property_type_hierachy($property_compounds_id);
                if($type =='socialdb_property_data' || $type =='socialdb_property_object'){
                    //atualizo o postmeta com o valor alterado
                    update_post_meta($object_id, "socialdb_property_$property",'');
                    $this->set_common_field_values($object_id, "socialdb_property_$property",'');
                }else{
                    //$result = wp_remove_object_terms( (int) $object_id, get_term_by('id', str_replace('_cat', '', $ids_metas[$key]),'socialdb_category_type')->term_id,'socialdb_category_type');
                    $this->set_common_field_values($object_id, "socialdb_propertyterm_$property",'');
                }
            }
            delete_post_meta($object_id,'socialdb_property_'.$property.'_'.$row);
        }
        // verifying if is everything all right
        if(!$result){
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
            $data['msg'] = __('Please set a valid value for this property','tainacan');
            $data['type'] = 'error';
            $data['title'] = 'Erro';
        }elseif ($result) {
            $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
            $this->update_event_state('confirmed', $data['event_id']);
            $data['msg'] = __('The event was successful','tainacan');
            $data['type'] = 'success';
            $data['title'] = __('Success','tainacan');
        } else {
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
            $data['msg'] = __('This object does not exist anymore','tainacan');
            $data['type'] = 'error';
            $data['title'] = 'Erro';
        }
        return $data;
    }

}
