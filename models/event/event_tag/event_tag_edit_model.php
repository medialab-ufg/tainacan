<?php
/*
include_once (dirname(__FILE__) . '/../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-includes/wp-db.php');
*/
require_once(dirname(__FILE__) . '../../../event/event_model.php');
require_once(dirname(__FILE__) . '../../../category/category_model.php');

class EventTagEdit extends EventModel {

    public function EventTagEdit() {
        $this->parent = get_term_by('name', 'socialdb_event_tag_edit', 'socialdb_event_type');
        $this->permission_name = 'socialdb_collection_permission_edit_tags';
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
        $new_name = $data['socialdb_event_tag_suggested_name'];
        $category = get_term_by('id',$data['socialdb_event_tag_id'],'socialdb_tag_type');
        $title = __('Edit the tag ','tainacan').'<br>'.
            __('From','tainacan').' : <i>'.$category->name.'</i><br>'.
            __('To','tainacan').' : <i>'.$new_name.'</i><br>'.
            __(' in the collection ','tainacan').'<b>'.$collection->post_title.'</b>';

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
           $data = $this->update_tag($data['event_id'],$data,$automatically_verified);    
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
    public function update_tag($event_id,$data,$automatically_verified) {
        $tagModel = new TagModel();
        $tag_name = get_post_meta($event_id,'socialdb_event_tag_suggested_name', true);
        $tag_description = get_post_meta($event_id, 'socialdb_event_tag_description',true) ;
        $tag_id = get_post_meta($event_id,'socialdb_event_tag_id',true);
        $collection_id = get_post_meta($event_id,'socialdb_event_collection_id',true);
        $data['synonyms'] = get_post_meta($event_id, 'socialdb_event_tag_synonyms',true) ;
        if(!$tagModel->verify_tag($tag_name, $collection_id)){
            $result = json_decode($tagModel->update($tag_id, $tag_name,$tag_description));
        }else{
            $result = false;
        }
        if ($result->success&&$result->success!='false') {
            $cat_id = $result->term_id;
            //se existir sinonimos para essa categoria
            $new_hash = md5(time());//crio o hash
            update_term_meta($cat_id, 'socialdb_term_synonyms', $new_hash); // salvo a categoria atual o novo hash exisitndo ou nao
            $synonyms = explode(',',$data['synonyms']); // pego as selecionadas
            if(is_array($synonyms)){ // se exitir
              $synonyms = str_replace("_tag","",array_filter($synonyms)); //pego todos indiferente de tags ou categorias
              foreach ($synonyms as $synonym) { // percorro
                  $hash = get_term_meta($synonym, 'socialdb_term_synonyms', true); // verifico se ja existe um hash neste termo
                  if($hash&&$hash!==''){//se alguma das selecionadas ja pertencer a outro grupo de sinonimos
                      $group_ids = $this->get_categories_hash($hash); // pego todos
                      foreach ($group_ids as $group_id) { // percorro o grupo
                          update_term_meta($group_id, 'socialdb_term_synonyms', $new_hash); // salvo o novo hash 
                      }
                  }else{ // se nao
                      update_term_meta($synonym, 'socialdb_term_synonyms', $new_hash); // salvo o novo hash 
                  }
              }
            }
            $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
            $this->update_event_state('confirmed', $data['event_id']);
            $data['msg'] = __('The event was successful','tainacan');
            $data['type'] = 'success';
            $data['title'] = __('Success','tainacan');
       }elseif(trim($tag_name)==''){
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
            $data['type'] = 'error';
            $data['title'] = 'Error';
            $data['msg'] = __('Tag name invalid','tainacan');
        }else {
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
            $data['msg'] = __('Tag name invalid or already exist','tainacan');
            $data['type'] = 'error';
            $data['title'] = 'Erro';
        }
        //$this->notificate_user_email($collection_id,  get_current_user_id(), $event_id);
        return $data;
    }

}
