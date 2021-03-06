<?php
/*
include_once (dirname(__FILE__) . '/../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-includes/wp-db.php');
*/
require_once(dirname(__FILE__) . '../../../event/event_model.php');
require_once(dirname(__FILE__) . '../../../property/property_model.php');

class EventPropertyTermEdit extends EventModel {

    public function __construct() {
        $this->parent = get_term_by('name', 'socialdb_event_property_term_edit', 'socialdb_event_type');
        $this->permission_name = 'socialdb_collection_permission_edit_property_term';
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
        $property_name = $data['socialdb_event_property_term_edit_name'];
        $property = get_term_by('id',$data['socialdb_event_property_term_edit_id'],'socialdb_property_type');
        //$title = __('Edit the term property ','tainacan').'('.$property_name.')'.__(' in the collection ','tainacan').'<b>'.$collection->post_title.'</b>';

        if(trim($property->name)==trim($property_name)){
            $text = '';
            $newcategory = (isset($data['socialdb_event_property_term_edit_new_taxonomy']) && !empty($data['socialdb_event_property_term_edit_new_taxonomy'])) ? $data['socialdb_event_property_term_edit_new_taxonomy'] : $data['socialdb_event_property_term_edit_root'];
            $category = get_term_meta($data['socialdb_event_property_term_edit_id'],'socialdb_property_term_root',true);
            $newrequired = $data['socialdb_event_property_term_edit_required'];
            $required = get_term_meta($data['socialdb_event_property_term_edit_id'],'socialdb_property_required',true);
            $newcardinality = $data['socialdb_event_property_term_edit_cardinality'];
            $cardinality = get_term_meta($data['socialdb_event_property_term_edit_id'],'socialdb_property_term_cardinality',true);
            $newwidget = $data['socialdb_event_property_term_edit_widget'];
            $widget = get_term_meta($data['socialdb_event_property_term_edit_id'],'socialdb_property_term_widget',true);


            if($newcategory !== $category){
                $newcategory = get_term_by('id',$newcategory,'socialdb_category_type');
                $name = ($newcategory) ? $newcategory->name : $data['socialdb_event_property_term_edit_new_category'];
                $category = get_term_by('id',$category,'socialdb_category_type');
                $val = ($category) ? $category->name : '(Vazio)';
                $text .=  __('Alter taxonomy from ', 'tainacan').' : <i>'.$val.'</i> '. __('to ', 'tainacan').'<i>'.$name.' </i>&nbsp;&nbsp;<br>';
            }

            if($newrequired !== $required){
                $newrequired = ($newrequired === 'true') ? __('True','tainacan') : __('False','tainacan');
                $required = ($required === 'true') ? __('True','tainacan') : __('False','tainacan');
                $text .=  __('Alter required field from ', 'tainacan').' : <i>'. $required .'</i> '. __('to ', 'tainacan').' <i>'.$newrequired.'</i>&nbsp;&nbsp;<br>';
            }

            if($newcardinality !== $cardinality){
                $newcardinality = ($newcardinality === 'n') ? __('Multiple values','tainacan') : __('One value','tainacan');
                $cardinality = ($cardinality === 'n') ? __('Multiple values','tainacan') : __('One value','tainacan');
                $text .=  __('Alter cardinality from ', 'tainacan').' : <i>'. $cardinality .'</i> '. __('to ', 'tainacan').' <i>'.$newcardinality.'</i>&nbsp;&nbsp;<br>';
            }

            if($newwidget !== $widget){
                $newwidget =  __($newwidget,'tainacan');
                $widget =  __($widget,'tainacan');
                $text .=  __('Alter widget field from ', 'tainacan').' : <i>'. $widget .'</i> '. __('to ', 'tainacan').' <i>'.$newwidget.'</i><br>';
            }

            $title = __('Alter configuration from term property ', 'tainacan').' : <i>'.$property->name.'</i>&nbsp;&nbsp;<br>'.$text.
                __(' in the collection ', 'tainacan') .' '.' <b><a target="_blank" href="'.  get_the_permalink($collection->ID).'">'.$collection->post_title.'</a></b> ';
        }else{
            $title = __('Edit the term property ', 'tainacan') .'<br>'.' '.
                __('From','tainacan').' : <i>'.$property->name.'</i><br>'.' '.
                __('To','tainacan').' : <i>'.$property_name.'</i><br>'.' '.
                __(' in the collection ', 'tainacan') .' '.' <b><a target="_blank" href="'.  get_the_permalink($collection->ID).'">'.$collection->post_title.'</a></b> ';
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
           $data = $this->update_property($data['event_id'],$data,$automatically_verified);    
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
    public function update_property($event_id,$data,$automatically_verified) {
        $propertyModel = new PropertyModel();
        // coloco os dados necessarios para criacao da propriedade
        $data['property_term_id'] = get_post_meta($event_id, 'socialdb_event_property_term_edit_id',true) ;
        $data['property_term_name'] = get_post_meta($event_id, 'socialdb_event_property_term_edit_name',true) ;
        $data['collection_id'] = get_post_meta($event_id, 'socialdb_event_collection_id',true) ;
        $data['socialdb_property_term_cardinality'] = get_post_meta($event_id, 'socialdb_event_property_term_edit_cardinality',true) ;
        $data['socialdb_property_term_widget'] = get_post_meta($event_id, 'socialdb_event_property_term_edit_widget',true) ;
        $data['socialdb_property_vinculate_category'] = get_post_meta($event_id, 'socialdb_event_property_term_edit_vinculate_category',true) ;   
        $data['socialdb_property_new_category'] = get_post_meta($event_id, 'socialdb_event_property_term_edit_new_category',true) ;   
        $data['socialdb_property_new_taxonomy'] = get_post_meta($event_id, 'socialdb_event_property_term_edit_new_taxonomy',true) ; 
        $data['property_term_required'] = get_post_meta($event_id, 'socialdb_event_property_term_edit_required',true) ;
        $data['socialdb_property_term_root'] = get_post_meta($event_id, 'socialdb_event_property_term_edit_root',true) ;
        $data['socialdb_property_help'] = get_post_meta($event_id, 'socialdb_event_property_term_edit_help',true) ;   
        $data['property_visualization'] = get_post_meta($event_id, 'socialdb_event_property_visualization',true) ;
        $data['property_category_id'] = get_term_meta($data['property_term_id'], 'socialdb_property_created_category',true) ;
        $data['property_locked'] = get_post_meta($event_id, 'socialdb_event_property_lock_field',true) ;
        $data['property_default_value'] = get_post_meta($event_id, 'socialdb_event_property_default_value',true) ;
        $data['property_habilitate_new_category'] = get_post_meta($event_id, 'socialdb_event_property_habilitate_new_category',true) ;
        // chamo a funcao do model de propriedade para fazer a insercao
         $result = json_decode($propertyModel->update_property_term($data));
        if(isset($result->property_term_id)){
                do_action('after_event_update_property_term',$result->property_term_id,$event_id);
        }
        // verifying if is everything all right
        if (get_term_by('id', $data['property_term_id'], 'socialdb_property_type')&&$result->success!='false') {
            $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
            $this->update_event_state('confirmed', $data['event_id']);
            $data['msg'] = __('The event was successful','tainacan');
            $data['type'] = 'success';
            $data['title'] = __('Success','tainacan');
        } else {
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
           if(isset($result->msg)):
             $data['msg'] = $result->msg;
            else:
              $data['msg'] = __('Please fill the fields correctly!','tainacan');  
            endif;
            $data['type'] = 'error';
            $data['title'] = 'Erro';
        }
        //$this->notificate_user_email( $data['collection_id'],  get_current_user_id(), $event_id);
        return $data;
    }

}
