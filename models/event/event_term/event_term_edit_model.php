<?php
/*
include_once (dirname(__FILE__) . '/../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-includes/wp-db.php');
*/
require_once(dirname(__FILE__) . '../../../event/event_model.php');
require_once(dirname(__FILE__) . '../../../category/category_model.php');

class EventTermEdit extends EventModel {

    public function EventTermEdit() {
        $this->parent = get_term_by('name', 'socialdb_event_term_edit', 'socialdb_event_type');
        $this->permission_name = 'socialdb_collection_permission_edit_category';
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
        $category = get_term_by('id',$data['socialdb_event_term_id'],'socialdb_category_type');
        if(trim($data['socialdb_event_term_suggested_name'])!==trim($data['socialdb_event_term_previous_name'])){
           $title = __('Edit the category ','tainacan').' '.'(  <i>'.$category->name.'</i> ) '.__('to the name ','tainacan').' '.'( <i>'.$data['socialdb_event_term_suggested_name'].'</i> )';
        }else{
            $parent = get_term_by('id', $data['socialdb_event_term_suggested_parent'], 'socialdb_category_type');
          $title = __('Edit the category ','tainacan').' '.'( <i>'.$category->name.'</i>  ) '.__('to the parent ','tainacan').' '.' ( <i>'.$parent->name.'</i> )';
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
           $data = $this->update_category($data['event_id'],$data,$automatically_verified);    
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
    public function update_category($event_id,$data,$automatically_verified) {
        $categoryModel = new CategoryModel();
        // coloco os dados necessarios para criacao da categoria
        $data['category_id'] = get_post_meta($event_id, 'socialdb_event_term_id',true) ;
        if(strpos($data['category_id'], '_facet_category')!==false){
                 $data['category_id'] = str_replace('_facet_category', '', $data['category_id'] );
        }
        $data['category_name'] = get_post_meta($event_id, 'socialdb_event_term_suggested_name',true) ;
        $data['category_description'] = get_post_meta($event_id, 'socialdb_event_term_description',true) ;
        $data['collection_id'] = get_post_meta($event_id, 'socialdb_event_collection_id',true) ;
        $data['category_parent_id'] = get_post_meta($event_id, 'socialdb_event_term_suggested_parent',true) ;
        $data['synonyms'] = get_post_meta($event_id, 'socialdb_event_term_synonyms',true) ;
        if($categoryModel->verify_category($data)){
           // chamo a funcao do model de categoria para fazer a insercao
            $result = json_decode($categoryModel->update($data));
        }  else {
            $result = false;
        }
        //action para apos a edicao de um termo
        do_action('after_event_edit_term',$event_id);
        //filtro que per,ite a vinculacao direta como faceta
        $is_showed = apply_filters( 'show_category_root_in_edit_category', $is_showed );
        if($is_showed){
            $category = get_term_by('slug','socialdb_category','socialdb_category_type');
            if($data['category_parent_id']==$category->term_id&&!$categoryModel->is_facet($data['category_id'], $data['collection_id'])){
                $categoryModel->add_facet($data['category_id'], $data['collection_id']);
            }
        }
        
        // se na edicao o usuario setou a categoria como faceta e ela ainda nao estiver no array de facetas da colecao
        //if($data['category_parent_id']=='0'&&!$categoryModel->is_facet($data['category_id'], $data['collection_id'])){
           // $categoryModel->add_facet($data['category_id'], $data['collection_id']);
        //}// se o usuario removeu uma categoria de faceta para embaixo de outra categoria
        //elseif($data['category_parent_id']!='0'&&$categoryModel->is_facet($data['category_id'], $data['collection_id'])){
        //if($data['category_parent_id']!='0'&&$categoryModel->is_facet($data['category_id'], $data['collection_id'])){
           // $categoryModel->delete_facet($data['category_id'], $data['collection_id']);
        //}
        // verifying if is everything all right
        if ($result&&$result->success=='true'&&get_term_by('id', $data['category_id'], 'socialdb_category_type')) {
            $this->set_approval_metas($data['event_id'], $data['socialdb_event_observation'], $automatically_verified);
            $this->update_event_state('confirmed', $data['event_id']);
            $data['msg'] = __('The event was successful','tainacan');
            $data['type'] = 'success';
            $data['title'] = __('Success','tainacan');
        } else {
            $this->update_event_state('invalid', $data['event_id']); // seto a o evento como invalido
            $data['msg'] = __('Error on update this category','tainacan');
            $data['type'] = 'error';
            $data['title'] = 'Erro';
        }
        //$this->notificate_user_email($data['collection_id'],  get_current_user_id(), $event_id);
        return $data;
    }

}
