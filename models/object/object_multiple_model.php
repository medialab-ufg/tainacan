<?php

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once (dirname(__FILE__) . '../../../models/collection/collection_model.php');
include_once (dirname(__FILE__) . '../../../models/property/property_model.php');
include_once (dirname(__FILE__) . '../../../models/ranking/ranking_model.php');
include_once (dirname(__FILE__) . '../../../models/category/category_model.php');
include_once (dirname(__FILE__) . '../../../models/event/event_object/event_object_create_model.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../user/user_model.php');
require_once(dirname(__FILE__) . '../../tag/tag_model.php');

/**
 * The class ObjectModel 
 *
 */
class ObjectMultipleModel extends Model {

    /**
     * @signature - add($data)
     * @param array $data Os dados vindos do formulario
     * @return json com os dados do resultado do evento criado
     * @description - Insere os items na colecao
     * @author: Eduardo 
     */
    public function add($data) {
        $result = [];
        $items_id = explode(',', $data['items_id']); // id de todos os itens
        if($items_id&&is_array($items_id)){
            foreach ($items_id as $item_id) {
                $post_id = $this->insert_post($data,trim($item_id));
                if($post_id){
                    $this->vinculate_collection($data, $post_id);
                    $this->item_resource($data, $item_id, $post_id);
                    $this->item_attachments($data, $item_id, $post_id);
                    $this->item_tags($data, $item_id, $post_id);
                    $this->item_property_data($data, $item_id, $post_id);
                    $this->item_property_object($data, $item_id, $post_id);
                    $this->item_property_term($data, $item_id, $post_id);
                    $this->insert_rankings($data,$post_id,$item_id);
                    $this->insert_license($data,$post_id,$item_id);
                    $result['ids'][] =$post_id; 

                    if( isset($data['do_extract']) && $data['do_extract'] === "true" ):
                        $_file_path_ = get_attached_file($item_id);
                        if(isset($_file_path_)) {
                            $_exif_data = exif_read_data($_file_path_, 0, true);
                            unset($_exif_data['FILE']);
                            unset($_exif_data['COMPUTED']);

                            if($_exif_data && !empty($_exif_data)):
                                $result['exif_metas'] = $_exif_data;
                                foreach($_exif_data as $exif_tag):
                                    if(is_array($exif_tag)):
                                        foreach($exif_tag as $exif_key => $exif_val):
                                            $_meta_exif_data_name = "Exif_" . $exif_key;
                                            $image_exif = [
                                                'collection_id' =>  $data['collection_id'],
                                                'property_data_name' => $_meta_exif_data_name,
                                                'property_metadata_type' => 'text',
                                                'socialdb_property_required' => false,
                                                'socialdb_property_data_cardinality' => '1',
                                                'is_repository_property' => 'true'
                                            ];

                                            $_term_exists = get_term_by('name', $_meta_exif_data_name, 'socialdb_property_type');
                                            // If term does not exists
                                            if ( $_term_exists === false) {
                                                $property_model = new PropertyModel();
                                                $inserted_data = $property_model->add_property_data($image_exif);
                                                $new_prop_id = json_decode($inserted_data)->new_property_id;

                                                if( $new_prop_id && is_int($new_prop_id) ) {
                                                    $_meta_field = "socialdb_property_" . (string) $new_prop_id;
                                                    update_post_meta($post_id, $_meta_field, $exif_val);
                                                }
                                            }

                                        endforeach;
                                    endif;
                                endforeach;
                            endif;
                        }
                    endif;

                }
            }
        }
        if(count($result['ids'])>0){
            $result['title'] = __('Success','tainacan');
            $result['title'] = count($result['ids']).' '.__('item/items inserted successfully','tainacan');
            $result['type'] = 'success';
        }else{
            $result['title'] = __('Error','tainacan');
            $result['title'] = __('No items inserted successfully!','tainacan');
            $result['type'] = 'error';
        }
        return json_encode($result);
    }
    /**
     * @signature - insert_post($data)
     * @param array $data Os dados vindos do formulario
     * @param int $item_id O id do item do formulario
     * @return int O id do objeto criado
     * @description - Insere o post do item no banco de dados 
     * @author: Eduardo 
     */
    public function insert_post($data,$item_id) {
        if($data['parent_'.$item_id]==''){
            $post = array(
                'post_title' => $data['title_'.$item_id],
                'post_content' => $data['description_'.$item_id],
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
                'post_type' => 'socialdb_object'
            );
            $object_id = wp_insert_post($post);
            $this->set_common_field_values($object_id, 'title', $post['post_title']);
            $this->set_common_field_values($object_id, 'description', $post['post_content']);
            return $object_id;
        }else{
            return false;
        }
        
    }
    /**
     * @signature - vinculate_collection($data)
     * @param array $data Os dados vindos do formulario
     * @param int $post_id O id do post criado
     * @return void
     * @description - Insere o post do item no banco de dados 
     * @author: Eduardo 
     */
    public function vinculate_collection($data,$post_id) {
        $collectionModel = new CollectionModel;
        $category_root_id = $collectionModel->get_category_root_of($data['collection_id']);
        //categoria raiz da colecao
        wp_set_object_terms($post_id, array((int) $category_root_id), 'socialdb_category_type',true);
    }
    /**
     * @signature - item_resource($data)
     * @param array $data Os dados vindos do formulario
     * @param int $post_id O id do post criado
     * @return void
     * @description - funcao que insere metadados essenciais do objeto como tipo,origem  
     * @author: Eduardo 
     */
    public function item_resource($data,$item_id,$post_id) {
        update_post_meta( $post_id, 'socialdb_object_from', 'internal');
        update_post_meta( $post_id, 'socialdb_object_dc_source', $data['source_'.$item_id]);
        update_post_meta( $post_id, 'socialdb_object_content', $item_id);
        update_post_meta( $post_id, 'socialdb_object_dc_type', $data['type_'.$item_id]);
        $this->set_common_field_values($post_id, 'object_from', 'internal');
        $this->set_common_field_values($post_id, 'object_source', $data['source_'.$item_id]);
        $this->set_common_field_values($post_id, 'object_type', $data['object_type']);
        $this->set_common_field_values($post_id, 'object_content', $data['type_'.$item_id]);
        if($data['type_'.$item_id]=='image'){
            set_post_thumbnail($post_id, $item_id);
        }
    }
    /**
     * @signature - item_attachments($data)
     * @param array $data Os dados vindos do formulario
     * @param int $item_id O id do item vindo do formulario
     * @param int $post_id O id do post criado
     * @return void
     * @description - funcao que insere os anexos no item
     * @author: Eduardo 
     */
    public function item_attachments($data,$item_id,$post_id) {
//        if($data['attachments_'.$item_id]!=''&&!empty($data['attachments_'.$item_id])){
//            $attachemnts = explode(',', $data['attachments_'.$item_id]);
//            if(is_array($attachemnts)){
//                $attachemnts = array_filter(array_unique($attachemnts));
//                foreach ($attachemnts as $attachemnt) {
//                    add_post_meta($post_id, '_file_id', $attachemnt);
//                    wp_update_post(['ID'=> trim($attachemnt),'post_parent'=>$post_id]);
//                }
//            }
//        }
        $attachemnts = get_post_meta($item_id, '_file_id');
        if($attachemnts&&  is_array($attachemnts)){
            foreach ($attachemnts as $attachemnt) {
                add_post_meta($post_id, '_file_id', $attachemnt);
                wp_update_post(['ID'=> trim($attachemnt),'post_parent'=>$post_id]);
            }
        }

    }
    /**
     * @signature - item_tags($data)
     * @param array $data Os dados vindos do formulario
     * @param int $item_id O id do item vindo do formulario
     * @param int $post_id O id do post criado
     * @return void
     * @description - funcao que insere os anexos no item
     * @author: Eduardo 
     */
    public function item_tags($data,$item_id,$post_id) {
        $tagModel = new TagModel();
        if($data['tags_'.$item_id]!=''&&!empty($data['tags_'.$item_id])){
            $tags = explode(',', $data['tags_'.$item_id]);
            if(is_array($tags)){
                $tags = array_filter(array_unique($tags));
                foreach ($tags as $tag) {
                    if ($tag !== ''):
                        $tag_array = $tagModel->add($tag, $data['collection_id']);
                        $tagModel->add_tag_object($tag_array['term_id'], $post_id);
                        $this->concatenate_commom_field_value( $post_id, "socialdb_propertyterm_tag", $tag_array['term_id']);
                    endif;
                }
            }
        }
    }
    /**
     * @signature - item_property_data($data)
     * @param array $data Os dados vindos do formulario
     * @param int $item_id O id do item vindo do formulario
     * @param int $post_id O id do post criado
     * @return void
     * @description - funcao que insere metadados de dados no item
     * @author: Eduardo 
     */
    public function item_property_data($data,$item_id,$post_id) {
        $properties_data = explode(',', $data['multiple_properties_data_id']); 
        if($properties_data&&$properties_data!=''&&  is_array($properties_data)){
              $properties_id = array_filter(array_unique($properties_data));
              foreach ($properties_id as $property_id) {
                  if(isset($data['socialdb_property_'.$property_id.'_'.$item_id])&&$this->getArray(trim($data['socialdb_property_'.$property_id.'_'.$item_id]))){
                       //update_post_meta($post_id, 'socialdb_property_' .$property_id,$data['socialdb_property_'.$property_id.'_'.$item_id]);
                        //$this->set_common_field_values($post_id, "socialdb_property_$property_id",$data['socialdb_property_'.$property_id.'_'.$item_id]);
                    $array =  $this->getArray(trim($data['socialdb_property_'.$property_id.'_'.$item_id]));
                    if(is_array($array)){
                        $array = array_filter(array_unique($array));
                        foreach ($array as $value) {
                            if(trim($value)!=''){
                               add_post_meta($post_id, 'socialdb_property_' .$property_id,  $value);
                               $this->set_common_field_values($post_id, "socialdb_property_$property_id",$value);
                            }
                        }
                    } 
                  }
              }
        }
    }
    /**
     * @signature - item_property_object($data)
     * @param array $data Os dados vindos do formulario
     * @param int $item_id O id do item vindo do formulario
     * @param int $post_id O id do post criado
     * @return void
     * @description - funcao que insere metadados de objeto no item
     * @author: Eduardo 
     */
    public function item_property_object($data,$item_id,$post_id) {
        $properties = explode(',', $data['multiple_properties_object_id']); 
        if($properties&&$properties!=''&&  is_array($properties)){
              $properties_id = array_filter(array_unique($properties));
              foreach ($properties_id as $property_id) {
                  if(isset($data['socialdb_property_'.$property_id.'_'.$item_id])
                          &&$this->getArray(trim($data['socialdb_property_'.$property_id.'_'.$item_id]))){
                      $this->insertMetasArray($this->getArray($data['socialdb_property_'.$property_id.'_'.$item_id]), $post_id, $property_id);
                  }
              }
        }
    }
    /**
     * @signature - item_property_term($data)
     * @param array $data Os dados vindos do formulario
     * @param int $item_id O id do item vindo do formulario
     * @param int $post_id O id do post criado
     * @return void
     * @description - funcao que insere metadados de termo no item
     * @author: Eduardo 
     */
    public function item_property_term($data,$item_id,$post_id) {
        $properties = explode(',', $data['multiple_properties_term_id']); 
        if($properties&&$properties!=''&&is_array($properties)){
              $properties_id = array_filter(array_unique($properties));
              foreach ($properties_id as $property_id) {
                  if(isset($data['socialdb_property_'.$property_id.'_'.$item_id])
                          &&$this->getArray(trim($data['socialdb_property_'.$property_id.'_'.$item_id]))){
                      $this->insertMetasCategory($this->getArray($data['socialdb_property_'.$property_id.'_'.$item_id]), $post_id, $property_id);
                  }
              }
        }
    }
     /**
     * @signature - getArray($data)
     * @param array $string O array concatenado
     * @return void
     * @description - funcao que verifica valida e retona um array a partir de um array concatenado por virgula 
     * @author: Eduardo 
     */
    private function getArray($string){
        $array = explode('||', $string);
        if(trim($string)!=''&& is_array($array)){
            return $array;
        }else{
             return false;
        }
    }
      /**
     * @signature - insertMetasArray($data)
     * @param array $array O array concatenado
     * @param int $post_id O array concatenado
     * @param int $property_id O array concatenado
     * @return void
     * @description - funcao que verifica valida e retona um array a partir de um array concatenado por virgula 
     * @author: Eduardo 
     */
    private function insertMetasArray($array,$post_id,$property_id){
        foreach ($array as $object_id) {
            if(trim($object_id)!=''){
               add_post_meta($post_id, 'socialdb_property_' .$property_id,  explode('_', $object_id)[0]);
               $this->concatenate_commom_field_value_object($post_id, "socialdb_property_$property_id",explode('_', $object_id)[0]);
            }
        }
    }
      /**
     * @signature - insertMetasArray($data)
     * @param array $array O array concatenado
     * @param int $post_id O array concatenado
     * @param int $property_id O array concatenado
     * @return void
     * @description - funcao que verifica valida e retona um array a partir de um array concatenado por virgula 
     * @author: Eduardo 
     */
    private function insertMetasCategory($array,$post_id,$property_id){
        foreach ($array as $category_id) {
            wp_set_object_terms($post_id, array((int) $category_id), 'socialdb_category_type',true);
             $this->concatenate_commom_field_value( $post_id, "socialdb_propertyterm_$property_id",$category_id);
            // add_post_meta($post_id, 'socialdb_property_' .$property_id,$object_id);
        }
    }
    
    public function insert_rankings($data,$post_id,$item_id) {
        $property_model = new PropertyModel;
        $ranking_model = new RankingModel;
        $category_model = new CategoryModel;
        if($data['properties_id']){
            $properties = $category_model->get_properties($data['collection_id'], []);
            if(is_array($properties)){
                foreach ($properties as $property) {
                    $dados = json_decode($property_model->edit_property(array('property_id' => $property)));
                    if ($dados->type && in_array($dados->type, ['stars', 'like', 'binary'])) {
                       $result = get_post_meta($item_id, 'socialdb_property_'.$dados->id, true);
                       if(!empty($result)){
                          $vote_id = $ranking_model->is_already_voted(get_current_user_id(), $dados->id, $item_id);
                          if($vote_id){
                            $row = $this->sdb_get_post_meta_by_value($vote_id, 'socialdb_property_ranking_object_id', $item_id);
                            $this->sdb_update_post_meta($row->meta_id, $post_id);
                          }
                          add_post_meta($post_id, 'socialdb_property_'.$dados->id, $result); 
                       }else{
                          add_post_meta($post_id, 'socialdb_property_'.$dados->id, 0);  
                       }
                    }
                }
            }
        }
    }
    
    public function insert_license($data,$post_id,$item_id) {
        if($data['license_'.$item_id]){
            update_post_meta($post_id, 'socialdb_license_id', $data['license_'.$item_id]);
        }
    }
    ############################################################################
    
    public function edit_multiple($data) {
        $_editable_ids = explode(",", $data['items_id']);
        foreach($_editable_ids as $_c_id):
            $title_field = "title_" . $_c_id;
            $desc_field = "description_" . $_c_id;
            $_new_text = $data[$title_field];
            $_new_content = $data[$desc_field];

            $data['edited_items'] = wp_update_post([
                'ID' => $_c_id,
                'post_title'   => $_new_text,
                'post_content' => $_new_content
            ]);
        endforeach;

       return json_encode($data);
    }
    
    /**
     * @signature - add($data)
     * @param array $data Os dados vindos do formulario
     * @return json com os dados do resultado do evento criado
     * @description - Insere os items na colecao
     * @author: Eduardo 
     */
    public function add_socialnetwork($data) {
        $result = [];
        $items_id = explode(',', $data['items_id']); // id de todos os itens
        $selected_items_id = explode(',', $data['selected_items_id']); // id de todos os itens selecionados
        if($items_id&&is_array($items_id)){
            foreach ($items_id as $item_id) {
                //if((empty($selected_items_id)||!$selected_items_id||!is_array($selected_items_id)||empty($selected_items_id[0]))||  in_array($item_id, $selected_items_id)){
                    $post_id = absint($item_id);
                     $object = array(
                        'ID' => $post_id,
                        'post_title' => $data['title_'.$item_id],
                        'post_status' => 'publish',
                        'post_content' => $data['description_'.$item_id]
                    );
                    wp_update_post($object);
                    $this->set_common_field_values($post_id, 'title', $object['post_title']);
                    $this->set_common_field_values($post_id, 'description', $object['post_content']);
                    if($post_id){
                        $this->vinculate_collection($data, $post_id);
                        //$this->item_resource($data, $item_id, $post_id);
                        //$this->item_attachments($data, $item_id, $post_id);
                        $this->item_tags($data, $item_id, $post_id);
                        $this->item_property_data($data, $item_id, $post_id);
                        $this->item_property_object($data, $item_id, $post_id);
                        $this->item_property_term($data, $item_id, $post_id);
                       // $this->insert_rankings($data,$post_id);
                        $this->insert_license($data,$post_id,$item_id);
                        $result['ids'][] =$post_id;
                    }
//                }else{
//                    $object_id =  $item_id;
//                    $collection_id = $data['collection_id'];
//                    //***** BEGIN CHECK SOCIAL NETWORKS *****//
//                    // YOUTUBE
//                    $mapping_id_youtube = $this->get_post_by_title('socialdb_channel_youtube', $collection_id, 'youtube');
//                    $getCurrentIds_youtube = unserialize(get_post_meta($mapping_id_youtube, 'socialdb_channel_youtube_inserted_ids', true));
//                    if(isset($getCurrentIds_youtube[$object_id])){
//                        unset($getCurrentIds_youtube[$object_id]);
//                        update_post_meta($mapping_id_youtube, 'socialdb_channel_youtube_inserted_ids', serialize($getCurrentIds_youtube));
//                    }
//
//                    //FACEBOOK
//                    $mapping_id_facebook = $this->get_post_by_title('socialdb_channel_facebook', $collection_id, 'facebook');
//                    $getCurrentIds_facebook = unserialize(get_post_meta($mapping_id_facebook, 'socialdb_channel_facebook_inserted_ids', true));
//                    if(isset($getCurrentIds_facebook[$object_id])){
//                        unset($getCurrentIds_facebook[$object_id]);
//                        update_post_meta($mapping_id_facebook, 'socialdb_channel_facebook_inserted_ids', serialize($getCurrentIds_facebook));
//                    }
//
//                    //INSTAGRAM
//                    $mapping_id_instagram = $this->get_post_by_title('socialdb_channel_instagram', $collection_id, 'instagram');
//                    $getCurrentIds_instagram = unserialize(get_post_meta($mapping_id_instagram, 'socialdb_channel_instagram_inserted_ids', true));
//                    if(isset($getCurrentIds_instagram[$object_id])){
//                        unset($getCurrentIds_instagram[$object_id]);
//                        update_post_meta($mapping_id_instagram, 'socialdb_channel_instagram_inserted_ids', serialize($getCurrentIds_instagram));
//                    }
//
//                    //FLICKR
//                    $mapping_id_flickr = $this->get_post_by_title('socialdb_channel_flickr', $collection_id, 'flickr');
//                    $getCurrentIds_flickr = unserialize(get_post_meta($mapping_id_flickr, 'socialdb_channel_flickr_inserted_ids', true));
//                    if(isset($getCurrentIds_flickr[$object_id])){
//                        unset($getCurrentIds_flickr[$object_id]);
//                        update_post_meta($mapping_id_flickr, 'socialdb_channel_flickr_inserted_ids', serialize($getCurrentIds_flickr));
//                    }
//                    //***** END CHECK SOCIAL NETWORKS *****//
//                }
            }
        }
        if(count($result['ids'])>0){
            $result['title'] = __('Success','tainacan');
            $result['title'] = count($result['ids']).' '.__('item/items inserted successfully','tainacan');
            $result['type'] = 'success';
        }else{
            $result['title'] = __('Error','tainacan');
            $result['title'] = __('No items inserted successfully!','tainacan');
            $result['type'] = 'error';
        }
        return json_encode($result);
    }
}
