<?php

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../../event/event_model.php');

class EventClassificationCreateModel extends EventModel {

    public function EventClassificationCreateModel() {
        $this->parent = get_term_by('name', 'socialdb_event_classification_create', 'socialdb_event_type');
        $this->permission_name = 'socialdb_collection_permission_add_classification';
    }

    /**
     * function generate_title($data)
     * @param string $data  Os dados vindo do formulario
     * @return ara  
     * 
     * Autor: Eduardo Humberto 
     */
    public function generate_title($data) {
        if ($data['socialdb_event_classification_type'] == 'category') {
            $category = get_term_by('id', $data['socialdb_event_classification_term_id'], 'socialdb_category_type');
            $title = __('Add the category : ','tainacan') . $category->name;
        } elseif ($data['socialdb_event_classification_type'] != 'tag' && $data['socialdb_event_classification_type'] != 'category') {
            $property = get_term_by('id', $data['socialdb_event_classification_type'], 'socialdb_property_type');
            $value = get_post($data['socialdb_event_classification_term_id']);
            $title = __('Add the classification : ','tainacan') . $value->post_title . _(' of the object property ') .' '. $property->name;
        } else {
            $category = get_term_by('id', $data['socialdb_event_classification_term_id'], 'socialdb_tag_type');
            $title = __('Add the tag : ','tainacan') .' '. $category->name;
        }
        $object = get_post($data['socialdb_event_classification_object_id']);
        $title.= __(' in the object ','tainacan') .' '.$object->post_title;
        return $title;
    }

    /**
     * function verify_event($data)
     * @param string $object_id  O id do objeto
     * @param string $data  Os dados do evento a ser verificado
     * @param string $automatically_verified  Se o evento foi automaticamente verificado
     * @return array  
     * 
     * Autor: Eduardo Humberto 
     */
    public function verify_event($data,$automatically_verified = false) {
       $collection_id = get_post_meta($data['event_id'],'socialdb_event_collection_id',true); 
       $actual_state = get_post_meta($data['event_id'], 'socialdb_event_confirmed',true);
       if($actual_state!='confirmed'&&$automatically_verified||(isset($data['socialdb_event_confirmed'])&&$data['socialdb_event_confirmed']=='true')){// se o evento foi confirmado automaticamente ou pelos moderadores
           $object_id = get_post(get_post_meta($data['event_id'], 'socialdb_event_classification_object_id',true)); // pego o objeto
           $type = get_post_meta($data['event_id'], 'socialdb_event_classification_type',true);// pego o tipo
           if($type=='category'){// se for categoria
              $data = $this->insert_event_category($object_id->ID, $data,$collection_id, $automatically_verified);
           }elseif($type=='tag'){
              $data = $this->insert_event_tag($object_id->ID, $data, $automatically_verified);
           }else{
              $data =  $this->insert_event_property($object_id->ID, $data, $automatically_verified); 
           }      
       }elseif($actual_state!='confirmed'){
           $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
           $this->update_event_state('not_confirmed', $data['event_id']);
           $data['success'] = true;
       }else{
           $data['success'] = false;
           $data['message'] = __('This event is already confirmed','tainacan');
       }
       $this->notificate_user_email( $collection_id,  get_current_user_id(), $data['event_id']);
       return json_encode($data);
    }
      /**
     * function insert_event_category($data)
      * @param string $object_id  O id do objeto
     * @param string $data  Os dados do evento a ser verificado
     * @param string $automatically_verified  Se o evento foi automaticamente verificado
     * @return array    
     * 
     * Autor: Eduardo Humberto 
     */
    public function insert_event_category($object_id,$data,$collection_id,$automatically_verified = false){
        //pego a categoria
        $category = get_term_by('id',  get_post_meta($data['event_id'], 'socialdb_event_classification_term_id',true),'socialdb_category_type');
        if($category&&$object_id){// se a categoria ou objeto forem validos
            wp_set_object_terms( $object_id, $category->term_id,'socialdb_category_type',true);
            $this->concatenate_commom_field_value( $object_id, "socialdb_propertyterm_".$this->get_category_property($category->term_id, $collection_id), $category->term_id);
            $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
            $this->update_event_state('confirmed', $data['event_id']);
            $data['msg'] = __('The event was successful','tainacan');
            $data['type'] = 'success';
            $data['title'] = 'Success';
        }else{ // se caso qualquer um dos itens for invalido
            $data['msg'] = __('Object or category invalid','tainacan');
            $data['type'] = 'error';
            $data['title'] = 'Error';
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
        }
        
       return $data;
    }
      /**
     * function insert_event_tag($object_id,$data,$automatically_verified = false)
     * @param string $object_id  O id do objeto
     * @param string $data  Os dados do evento a ser verificado
     * @param string $automatically_verified  Se o evento foi automaticamente verificado
     * @return array    
     * 
     * Autor: Eduardo Humberto 
     */
    public function insert_event_tag($object_id,$data,$automatically_verified = false){
        //pego a tag
        $tag = get_term_by('id',  get_post_meta($data['event_id'], 'socialdb_event_classification_term_id',true),'socialdb_tag_type');
        if($tag&&$object_id){// se a categoria ou objeto forem validos
            wp_set_object_terms( $object_id, $tag->term_id,'socialdb_tag_type',true);
            $this->concatenate_commom_field_value( $object_id, "socialdb_propertyterm_tag", $tag->term_id);
            $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
            $this->update_event_state('confirmed', $data['event_id']);
            $data['msg'] = __('The event was successful','tainacan');
            $data['type'] = 'success';
            $data['title'] = 'Success';
        }else{ // se caso qualquer um dos itens for invalido
            $data['msg'] = __('Object or tag invalid','tainacan');
            $data['type'] = 'error';
            $data['title'] = 'Error';
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
        }
        return $data;
    }
    /**
     * function insert_event_property($object_id,$data,$automatically_verified = false)
     * @param string $object_id  O id do objeto
     * @param string $data  Os dados do evento a ser verificado
     * @param string $automatically_verified  Se o evento foi automaticamente verificado
     * @return array  
     * 
     * Autor: Eduardo Humberto 
     */
    public function insert_event_property($object_id,$data,$automatically_verified = false) {
       //pego a propriedade de relacionamento
        $property = get_term_by('id', get_post_meta($data['event_id'], 'socialdb_event_classification_type',true),'socialdb_property_type');
        if(!isset($property)){
            $property = get_term_by('id',  get_post_meta($data['event_id'], 'socialdb_event_classification_type',true),'socialdb_category_type');
            $relationship_id = get_term_by('id',get_post_meta($data['event_id'], 'socialdb_event_classification_term_id',true),'socialdb_category_type')->term_id;
        } else {
            $type = $this->get_property_type_hierachy($property_id);
            if($type=='socialdb_property_data'):
                $relationship_id = $this->sdb_get_post_meta(get_post_meta($data['event_id'], 'socialdb_event_classification_term_id',true))->meta_value;     
            else:
                $relationship_id = get_post(get_post_meta($data['event_id'], 'socialdb_event_classification_term_id',true))->ID;
            endif;                   
        }       
                  
       if($property&&$relationship_id&&$object_id) { // faco a validacao
            $metas = get_post_meta($object_id, 'socialdb_property_'.$property->term_id);
            if($metas&&$metas[0]!=''&&is_array($metas)){
                if(!in_array($relationship_id, $metas)):
                    add_post_meta($object_id, 'socialdb_property_'.$property->term_id, $relationship_id);
                    if(is_numeric($relationship_id)) {
                        $this->concatenate_commom_field_value_object($object_id, "socialdb_property_" . $property->term_id, $relationship_id);    
                    } else {
                        $this->concatenate_commom_field_value($object_id, "socialdb_property_" . $property->term_id_, $relationship_id);
                    }                    
                else:
                    $data['msg'] = __('This classification is already confirmed','tainacan');
                    $data['type'] = 'info';
                    $data['title'] = 'Attention!';
                    $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
                    return $data;
                endif;
            }else{
                update_post_meta($object_id, 'socialdb_property_'.$property->term_id, $relationship_id);
                $this->concatenate_commom_field_value_object($object_id, "socialdb_property_" . $property->term_id, $relationship_id);
            }
            $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
            $this->update_event_state('confirmed', $data['event_id']);
            $data['msg'] = __('The event was successful','tainacan');
            $data['type'] = 'success';
            $data['title'] = 'Success';
        } else { // se caso qualquer um dos itens for invalido
            $property = get_post_meta($data['event_id'], 'socialdb_event_classification_type',true);
            $data['msg']  = __('Object, Property or relationship invalid','tainacan');
            $data['type'] = 'error';
            $data['title'] = 'Error';
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
        }
        return $data;
    }

}
