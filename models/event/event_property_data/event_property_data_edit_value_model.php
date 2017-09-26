<?php
/*
include_once (dirname(__FILE__) . '/../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-includes/wp-db.php');
*/
require_once(dirname(__FILE__) . '../../../../models/object/object_save_values.php');
require_once(dirname(__FILE__) . '../../../event/event_model.php');

class EventPropertyDataEditValue extends EventModel {

    public function EventPropertyDataEditValue() {
        $this->parent = get_term_by('name', 'socialdb_event_property_data_edit_value', 'socialdb_event_type');
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
        $object = get_post($data['socialdb_event_property_data_edit_value_object_id']);
        $property = get_term_by('id', $data['socialdb_event_property_data_edit_value_property_id'], 'socialdb_property_type');
        $values_before = get_post_meta($object->ID,'socialdb_property_'.$property->term_id);
        if($data['socialdb_event_property_data_edit_value_attribute_value']!=''){
            if($values_before && count(array_filter($values_before)) > 0){
                $valuesBefore = implode(',',array_filter($values_before));
                if(is_array($data['socialdb_event_property_data_edit_value_attribute_value'])){
                    $names = [];
                    foreach ($data['socialdb_event_property_data_edit_value_attribute_value'] as $value) {
                        if(isset($value['val']))
                            $names[] = $value['val'];
                        else
                            $names[] = $value;
                    }
                    $title = __('Alter the actual value of metadata ','tainacan').' <b>'.$property->name.'</b> '.__('from ').' ( <i>'.$valuesBefore.'</i> ) '.__(' to ','tainacan').' ( <i>'.implode(',',$names).'</i> ) '
                        . __(' in the object ','tainacan') .'<b><a href="'.  get_the_permalink($object->ID).'">'. $object->post_title.'</a></b>';
                }else{
                    $text = $data['socialdb_event_property_data_edit_value_attribute_value'];
                    $title = __('Alter the actual value of  metadata','tainacan').' <b>'.$property->name.'</b> '.__('from ').' ( <i>'.$valuesBefore.'</i> ) '.__(' to ','tainacan').' ( <i>'.$text.'</i> ) '
                        . __(' in the object ','tainacan') .'<b><a href="'.  get_the_permalink($object->ID).'">'. $object->post_title.'</a></b>';
                }
            }else{
                if(is_array($data['socialdb_event_property_data_edit_value_attribute_value'])){
                    $names = [];
                    foreach ($data['socialdb_event_property_data_edit_value_attribute_value'] as $value) {
                        if(isset($value['val']))
                            $names[] = $value['val'];
                        else
                            $names[] = $value;
                    }
                    $title = __('Set the value(s): ','tainacan').' ( <i>'.implode(',',$names).'</i> ) '.__(' of the data property ','tainacan').' <b>'.$property->name.'</b>'
                        . __(' in the the object ','tainacan') . '<b><a href="'.  get_the_permalink($object->ID).'">'. $object->post_title.'</a></b>';
                }else{
                    $text = $data['socialdb_event_property_data_edit_value_attribute_value'];
                    $title = __('Set the value: ','tainacan').' ( <i>'.$text.'</i> ) '.__(' of the data property ','tainacan').'<b>'.$property->name.'</b>'
                        . __(' in the the object ','tainacan') . '<b><a href="'.  get_the_permalink($object->ID).'">'. $object->post_title.'</a></b>';
                }
            }

        }else{
            $title = __('Delete all values of the data property ','tainacan').' <b>'.$property->name.'</b>'
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
    public function verify_event($data,$automatically_verified = false)
    {
       $actual_state = get_post_meta($data['event_id'], 'socialdb_event_confirmed',true);
       if($actual_state != 'confirmed' && $automatically_verified || (isset($data['socialdb_event_confirmed']) && $data['socialdb_event_confirmed'] == 'true'))// se o evento foi confirmado automaticamente ou pelos moderadores
       {
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
        $object_id = get_post_meta($event_id, 'socialdb_event_property_data_edit_value_object_id',true);
        $property = get_post_meta($event_id, 'socialdb_event_property_data_edit_value_property_id',true);
        $value = get_post_meta($event_id, 'socialdb_event_property_data_edit_value_attribute_value',true);

        //alterando o valor de fato das propriedades fixas ou das demais
        if($property == 'title'){
            $post = array(
                'ID' => $object_id,
                'post_title' => $value
            );
            $result = wp_update_post($post);
            $this->set_common_field_values($object_id, 'title', $value);
        }else if($property == 'description'){
            $post = array(
                'ID' => $object_id,
                'post_content' => $value
            );
            $result = wp_update_post($post);
            $this->set_common_field_values($object_id, 'description', $value);
        }else if($property == 'source'){
            $result =  update_post_meta($object_id, 'socialdb_object_dc_source', $value);
            $this->set_common_field_values($object_id, 'object_source', $value);
        }else if($property == 'type'){
            $result =  update_post_meta($object_id, 'socialdb_object_dc_type', $value);
            $this->set_common_field_values($object_id, 'object_type', $value);
        }else if($property == 'thumbnail'){
            $result = set_post_thumbnail($object_id, $value);
        }else if($property == 'license'){
            $result = update_post_meta($object_id, 'socialdb_license_id', $value);
        }else if(is_array($value) || is_array(unserialize($value))){
            foreach ($value as $meta) {
                if($meta['index'] == '0')
                {
                    $class = new ObjectSaveValuesModel();
                    $class->saveValue($object_id,
                        $property,
                        0,
                        'data',
                        0,
                        $meta['val'],
                        false
                    );
                }else
                {
                    $this->sdb_update_post_meta($meta['index'], $meta['val']);
                    $this->set_common_field_values($object_id, "socialdb_property_$property",$meta['val']);
                }
            }

            $result = true;
        }else{
            $meta = array($value);
            $clean_array = [];
            delete_post_meta($object_id,'socialdb_property_'.$property);
            foreach ($meta as $value) {
                if($value!=''){
                    add_post_meta($object_id,'socialdb_property_'.$property, $value);
                    $clean_array[] = $value;
                }
            }
            $result = true;
            $this->set_common_field_values($object_id, "socialdb_property_$property",$clean_array);
        }
        // verifying if is everything all right
        if(!$result&&$meta===''){
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
