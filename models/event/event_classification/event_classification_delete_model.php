<?php
/*
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
*/
require_once(dirname(__FILE__) . '../../../event/event_model.php');

class EventClassificationDeleteModel extends EventModel {

    public function EventClassificationDeleteModel() {
        $this->parent = get_term_by('name', 'socialdb_event_classification_delete', 'socialdb_event_type');
        $this->permission_name = 'socialdb_collection_permission_delete_classification';
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
            $title = __('Delete the category : ','tainacan') . ' <i>'.$category->name.'</i>';
        } elseif ($data['socialdb_event_classification_type'] != 'tag' && $data['socialdb_event_classification_type'] != 'category') {
            $property = get_term_by('id', $data['socialdb_event_classification_type'], 'socialdb_property_type');
            $value = get_post($data['socialdb_event_classification_term_id']);
            $title = __('Delete the classification : ','tainacan') .' <i>'. $value->post_title.'</i> ' . _(' of the object property ') .' <b>'. $property->name.'</b>';
        } else {
            $category = get_term_by('id', $data['socialdb_event_classification_term_id'], 'socialdb_tag_type');
            $title = __('Delete the tag : ','tainacan') .' <i>'. $category->name.'</i>';
        }
        $object = get_post($data['socialdb_event_classification_object_id']);
        $title.= __(' in the object ','tainacan') .' '.'<b><a href="'.  get_the_permalink($object->ID).'">'. $object->post_title.'</a></b>';
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
              $data = $this->delete_event_category($object_id->ID, $data, $automatically_verified);
           }elseif($type=='tag'){
              $data = $this->delete_event_tag($object_id->ID, $data, $automatically_verified);
           }else{
              $data =  $this->delete_event_property($object_id->ID, $data, $automatically_verified); 
           }      
       }elseif($actual_state!='confirmed'){
           $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
           $this->update_event_state('not_confirmed', $data['event_id']);
           $data['msg'] = __('The event was successful NOT confirmed','tainacan');
           $data['type'] = 'success';
           $data['title'] = 'Success';
       }else{
           $data['msg'] = __('This event is already confirmed','tainacan');
           $data['type'] = 'info';
           $data['title'] = 'Atention';
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
    public function delete_event_category($object_id,$data,$automatically_verified = false){
        //pego a categoria
        $category = get_term_by('id',  get_post_meta($data['event_id'], 'socialdb_event_classification_term_id',true),'socialdb_category_type');
        $collection_id = get_post_meta($data['event_id'], 'socialdb_event_collection_id',true);
        $category_root_id = $this->get_category_root_of($collection_id);

        if($category&&$object_id&&($category->term_id!=$category_root_id)){// se a categoria ou objeto forem validos
            $result = wp_remove_object_terms( $object_id, $category->term_id,'socialdb_category_type');
            /************commom values*******************/
            //deleto o valor
             $property_id = $this->get_category_property($category->term_id, $collection_id);
             $this->delete_commom_field_value($object_id, "socialdb_propertyterm_$property_id", $category->term_id);
            //atualizo o array
            $categories = wp_get_object_terms($object_id, 'socialdb_category_type');
            if(is_array($categories)){
                foreach ($categories as $category):
                    $category_id = $category->term_id;
                    $property_id = $this->get_category_property($category_id, $collection_id);
                    $this->concatenate_commom_field_value($object_id, "socialdb_propertyterm_$property_id",$category_id);
                endforeach;
            }
            // end commom values
            if($result){
                $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
                $this->update_event_state('confirmed', $data['event_id']);
                $data['msg'] = __('The event was successful','tainacan');
                $data['type'] = 'success';
                $data['title'] = 'Success';
            }else{
                $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
                $data['msg'] = __('This classification does not exist anymore','tainacan');
                $data['type'] = 'error';
                $data['title'] = 'Error';
            }
        }elseif($category->term_id==$category_root_id){
            $data['msg'] = __('You may not delete a classification between this object and the collection category root','tainacan');
            $data['type'] = 'error';
            $data['title'] = 'Error';
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
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
    public function delete_event_tag($object_id,$data,$automatically_verified = false){
        //pego a tag
        $tag = get_term_by('id',  get_post_meta($data['event_id'], 'socialdb_event_classification_term_id',true),'socialdb_tag_type');
        if($tag&&$object_id){// se a categoria ou objeto forem validos
            $result = wp_remove_object_terms( $object_id, $tag->term_id,'socialdb_tag_type');
            //**********commom values
            //deleto o valor
             $this->delete_commom_field_value($object_id, "socialdb_propertyterm_tag", $tag->term_id);
            
             $tags_id = [];
            $tags = wp_get_object_terms($object_id, 'socialdb_tag_type');
            if(is_array($tags)){
                foreach ($tags as $tag) {
                    $tags_id[] = $tag->term_id;
                }
            }
            $this->set_common_field_values($object_id, "socialdb_propertyterm_tag", $tags_id,'term');
            if($result){
                $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
                $this->update_event_state('confirmed', $data['event_id']);
                $data['msg'] = __('The event was successful','tainacan');
                $data['type'] = 'success';
                $data['title'] = 'Success';
            }else{
                $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
                $data['msg'] = __('This classification does not exist anymore','tainacan');
                $data['type'] = 'error';
                $data['title'] = 'Error';
            }
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
    public function  delete_event_property($object_id,$data,$automatically_verified = false){
       //pego a propriedade de relacionamento
        $property = get_term_by('id',  get_post_meta($data['event_id'], 'socialdb_event_classification_type',true),'socialdb_property_type');
        $relationship_id = get_post(get_post_meta($data['event_id'], 'socialdb_event_classification_term_id',true));
        if($property&&$relationship_id&&$object_id){ // faco a validacao
            $metas = get_post_meta($object_id, 'socialdb_property_'.$property->term_id);//pego a propriedade
            if(!$metas||count($metas)==1){// se exisir ou nao exisir so um relacionamento para essa propriedade ele atualiza esse unico registro
                update_post_meta($object_id, 'socialdb_property_'.$property->term_id, '',$relationship_id->ID);
            }else{// se nao, exclui somente ele
                delete_post_meta($object_id, 'socialdb_property_'.$property->term_id, $relationship_id->ID);
            }
            $this->set_common_field_values($object_id, 'socialdb_property_'.$property->term_id, get_post_meta($object_id, 'socialdb_property_'.$property->term_id),'item');
            $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
            $this->update_event_state('confirmed', $data['event_id']);
            $data['msg'] = __('The event was successful','tainacan');
            $data['type'] = 'success';
            $data['title'] = 'Success';
        }else{ // se caso qualquer um dos itens for invalido
            $data['msg'] = __('Object, Property or relationship invalid','tainacan');
            $data['type'] = 'error';
            $data['title'] = 'Error';
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
        }
        return $data;
    }

}
