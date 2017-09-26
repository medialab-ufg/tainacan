<?php
/*
include_once (dirname(__FILE__) . '/../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-includes/wp-db.php');
*/
require_once(dirname(__FILE__) . '../../../event/event_model.php');
require_once(dirname(__FILE__) . '../../../property/property_model.php');

class EventPropertyObjectEditValue extends EventModel {

    public function EventPropertyObjectEditValue() {
        $this->parent = get_term_by('name', 'socialdb_event_property_object_edit_value', 'socialdb_event_type');
        $this->permission_name = 'socialdb_collection_permission_edit_property_object_value';
    }

    /**
     * function generate_title($data)
     * @param string $data  Os dados vindo do formulario
     * @return ara  
     * 
     * Autor: Eduardo Humberto 
     */
    public function generate_title($data) {
        $object = get_post($data['socialdb_event_property_object_edit_object_id']);
        $property = get_term_by('id', $data['socialdb_event_property_object_edit_property_id'], 'socialdb_property_type');
        $values_before = get_post_meta($object->ID,'socialdb_property_'.$property->term_id);

        if($data['socialdb_event_property_object_edit_value_suggested_value']!=''){
            if($values_before && count(array_filter($values_before)) > 0){
                $new_values = [];
                foreach ($values_before as $item) {
                    $new_values[] = get_post($item)->post_title;
                }
                $valuesBefore = implode(',',array_filter($new_values));
                if(is_array($data['socialdb_event_property_object_edit_value_suggested_value'])){
                    $names = [];
                    foreach ($data['socialdb_event_property_object_edit_value_suggested_value'] as $value) {
                        if (isset($value['val']))
                            $names[] = get_post($value['val'])->post_title;
                        else
                            $names[] = get_post($value)->post_title;

                    }
                    $title = __('Alter the actual classification of metadata','tainacan').' <b>'.$property->name.'</b> '.__('from ','tainacan').' ( <i>'.$valuesBefore.'</i> ) '.__(' to ','tainacan').' ( <i>'.implode(',',$names).'</i> ) '
                        . __(' in the object ','tainacan') .'<b><a href="'.  get_the_permalink($object->ID).'">'. $object->post_title.'</a></b>';
                }else{
                    $text = get_post($data['socialdb_event_property_object_edit_value_suggested_value'])->post_title;
                    $title = __('Alter the actual classification of metadata','tainacan').' <b>'.$property->name.'</b> '.__('from ','tainacan').' ( <i>'.$valuesBefore.'</i> ) '.__(' to ','tainacan').' ( <i>'.$text.'</i> ) '
                        . __(' in the object ','tainacan') .'<b><a href="'.  get_the_permalink($object->ID).'">'. $object->post_title.'</a></b>';
                }
            }else {
                if (is_array($data['socialdb_event_property_object_edit_value_suggested_value'])) {
                    $names = [];
                    foreach ($data['socialdb_event_property_object_edit_value_suggested_value'] as $value) {
                        if (isset($value['val']))
                            $names[] = get_post($value['val'])->post_title;
                        else
                            $names[] = get_post($value)->post_title;

                    }
                    $title = __('Set the value: ', 'tainacan') . '( <i>' . implode(',', $names) . '</i> )' . __(' of the object property ', 'tainacan') . '  <b>' . $property->name . '</b> '
                        . __(' in the the object ', 'tainacan') . $object->post_title;
                } else {
                    $text = get_post($data['socialdb_event_property_object_edit_value_suggested_value'])->post_title;
                    $title = __('Set the value: ', 'tainacan') . '( <i>' . $text . '</i> )' . __(' of the object property ', 'tainacan') . ' ' . '<b>' . $property->name . '</b>'
                        . __(' in the the object ', 'tainacan') . '<b><a href="'.  get_the_permalink($object->ID).'">'. $object->post_title.'</a></b>';
                }
            }
        }else{
            $title = __('Delete all values of the object property ','tainacan').'<b>'.$property->name.'</b>'
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
    public function update_property_value($event_id, $data, $automatically_verified) {
        $property_model = new PropertyModel;
        $collection_id = get_post_meta($event_id, 'socialdb_event_collection_id', true);
        $object_id = get_post_meta($event_id, 'socialdb_event_property_object_edit_object_id', true);
        $property = get_post_meta($event_id, 'socialdb_event_property_object_edit_property_id', true);
        $dados = json_decode($property_model->edit_property(array('property_id' => $property)));
        $relations = get_post_meta($event_id, 'socialdb_event_property_object_edit_value_suggested_value', true);
        $all_metas = get_post_meta($object_id,'socialdb_property_'.$property);
        $result = delete_post_meta($object_id, 'socialdb_property_' . $property);
        $this->set_common_field_values($object_id, "socialdb_property_$property", '');
        //verificando se atualiza ou adiciona o meta da propriedade no objeto
        if (!isset($data['delete_all_values']) || ($data['delete_all_values'] != 'true')) {
            if ($relations && is_array($relations) && $relations[0] != '') {
                foreach ($relations as $relation_id) {
                    $result = add_post_meta($object_id, 'socialdb_property_' . $property, $relation_id);
                    $this->concatenate_commom_field_value_object($object_id, 'socialdb_property_' . $property, $relation_id);
                    if (isset($dados->metas) && ($dados->metas->socialdb_property_object_is_reverse == 'true')) {
                        add_post_meta($relation_id, "socialdb_property_" . $dados->metas->socialdb_property_object_reverse, $object_id);
                        $this->concatenate_commom_field_value_object($relation_id, "socialdb_property_" . $dados->metas->socialdb_property_object_reverse, $object_id);
                    }
                }
                $this->set_common_field_values($object_id, "socialdb_property_$property", $relations,'item');
            }
        }else{// se estiver apagando tudo
            if ($all_metas && is_array($all_metas)) {
                foreach ($all_metas as $all_meta) {
                    if (isset($dados->metas) && ($dados->metas->socialdb_property_object_is_reverse == 'true')) {
                        delete_post_meta($all_meta, "socialdb_property_" . $dados->metas->socialdb_property_object_reverse, $object_id);
                    }
                }
             }
        }
        // verifying if is everything all right
        if ($result) {
            $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
            $this->update_event_state('confirmed', $data['event_id']);
            $data['msg'] = __('The event was successful', 'tainacan');
            $data['type'] = 'success';
            $data['title'] = __('Success', 'tainacan');
        } else {
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
            $data['msg'] = __('This object does not exist anymore', 'tainacan');
            $data['type'] = 'error';
            $data['title'] = 'Erro';
        }
        //$this->notificate_user_email($collection_id,  get_current_user_id(), $event_id);
        return $data;
    }

}
