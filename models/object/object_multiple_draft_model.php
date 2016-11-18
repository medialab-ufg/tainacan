<?php
require_once(dirname(__FILE__) . '/object_multiple_model.php');

/**
 * The class ObjectMultipleDraftModel 
 *
 */
class ObjectMultipleDraftModel extends ObjectMultipleModel {
    /**
   * @signature - insert_post($data)
   * @param array $data Os dados vindos do formulario
   * @param int $item_id O id do item do formulario
   * @return int O id do objeto criado
   * @description - Insere o post do item no banco de dados
   * @author: Eduardo
   */
  public function insert_post_multiple($data,$item_id,$is_multiple_editting) {
      if($data['parent_'.$item_id]==''){
          $post = array(
              'post_title' => $data['title_'.$item_id],
              'post_content' => $data['description_'.$item_id],
              'post_status' => 'betafile',
              'post_author' => get_current_user_id(),
              'post_type' => 'socialdb_object'
          );
          if(!get_post_meta($item_id, 'socialdb_item_id') && !$is_multiple_editting){
            $object_id = wp_insert_post($post);
            add_post_meta($item_id, 'socialdb_item_id', $object_id);
            add_user_meta(get_current_user_id(), 'socialdb_collection_'.$data['collection_id'].'_betafile', $object_id);
          }else{
              $post['ID'] = (!$is_multiple_editting) ? get_post_meta($item_id, 'socialdb_item_id',TRUE) : $item_id;
              wp_update_post($post);
              $object_id = $post['ID'];
              $this->delete_item_meta($post['ID']);
              wp_delete_object_term_relationships( $object_id, 'socialdb_category_type' );
          }
          $this->set_common_field_values($object_id, 'title', $post['post_title']);
          $this->set_common_field_values($object_id, 'description', $post['post_content']);
          return $object_id;
      }else{
          return false;
      }

  }
  
  /**
     * @signature - add($data)
     * @param array $data Os dados vindos do formulario
     * @return json com os dados do resultado do evento criado
     * @description - Insere os items na colecao
     * @author: Eduardo 
     */
  public function add($data,$is_multiple_editting = false) {
    $result = [];
    $items_id = explode(',', $data['items_id']); // id de todos os itens
    if($items_id&&is_array($items_id)){
      foreach ($items_id as $item_id) {
        $post_id = $this->insert_post_multiple($data,trim($item_id),$is_multiple_editting);
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
          $result['ids'][] = $post_id;

          if( isset($data['do_extract']) && $data['do_extract'] === "true" ):
            $_file_path_ = get_attached_file($item_id);
            if(isset($_file_path_)) {
              $_exif_data = exif_read_data($_file_path_, 0, true);
              unset($_exif_data['FILE']);
              unset($_exif_data['COMPUTED']);

              if($_exif_data && !empty($_exif_data)):

                $property_model = new PropertyModel();
                $_DATASET = [
                  'collection_id' => $data['collection_id'],
                  'category_id' => $this->get_category_root_of($data['collection_id']),
                  'property_category_id' => $this->get_category_root_of($data['collection_id'])
                ];
                $_col_metas = json_decode($property_model->list_property_data($_DATASET))->property_data;

                $_props_slugs = [];
                $_props_ids = [];
                foreach($_col_metas as $_prop_data) {
                  $_formtd = explode("_", $_prop_data->slug);
                  array_push($_props_slugs, $_formtd[0] );
                  array_push($_props_ids, $_prop_data->id);
                }

                foreach($_exif_data as $exif_tag):
                  if(is_array($exif_tag)):
                    foreach($exif_tag as $exif_key => $exif_val):
                      $_meta_exif_data_name = "Exif_" . $exif_key;

                      if( strpos($exif_key, "UndefinedTag") === false ):
                          $image_exif = [
                              'collection_id' =>  $data['collection_id'],
                            'property_category_id' => $this->get_category_root_of($data['collection_id']),
                            'property_data_name' => $_meta_exif_data_name,
                            'property_metadata_type' => 'text',
                            'socialdb_property_required' => false,
                            'socialdb_property_data_cardinality' => '1',
                            'is_repository_property' => 'false'
                          ];

                          $_exif_slug = strtolower( sanitize_file_name( str_replace("_", "-", $_meta_exif_data_name) ));
                          if( in_array($_exif_slug, $_props_slugs )) {
                            $prop_index = array_search($_exif_slug, $_props_slugs);
                            if($prop_index && $prop_index != false) {
                              $prop_id = $_props_ids[$prop_index];
                              if($prop_id) {
                                $_meta_field = "socialdb_property_" . (string) $prop_id;
                                update_post_meta($post_id, $_meta_field, $exif_val);
                              }
                            }
                          } else {
                            $inserted_data = $property_model->add_property_data($image_exif);
                            $new_prop_id = json_decode($inserted_data)->new_property_id;
                            if ($new_prop_id && is_int($new_prop_id)) {
                              update_term_meta($new_prop_id, 'socialdb_property_is_fixed', 'true');
                              update_term_meta($new_prop_id, 'socialdb_property_visibility', 'show');
                              $_meta_field = "socialdb_property_" . (string)$new_prop_id;
                              update_post_meta($post_id, $_meta_field, $exif_val);
                            }
                          }
                      endif;
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
}
