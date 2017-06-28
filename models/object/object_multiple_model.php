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

/*Extrator de texto PDF*/
require_once (dirname(__FILE__) . '../../../libraries/php/PDFParser/vendor/autoload.php');

/*Extrator de texto Office Documents*/
require_once (dirname(__FILE__) . '../../../libraries/php/OfficeToPlainText/OfficeDocumentToPlainText.php');

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

      if($items_id&&is_array($items_id)) {
          $_exif_compounds_array = [];
          $_exif_compound_parent = ['title' => 'Imagem EXIF', 'id' => -1];

      foreach ($items_id as $item_id) {
        $post_id = $this->insert_post($data,trim($item_id));
        if($post_id)
        {
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
          $col_id = $data['collection_id'];
          $user_id = get_current_user_id();

          if ($user_id == 0)
          {
            $user_id = get_option('anonimous_user');
          }

          $logData = ['collection_id' => $col_id, 'item_id' => $item_id,
            'user_id' => $user_id, 'event_type' => 'user_items', 'event' => 'add' ];
          Log::addLog($logData);

          if( isset($data['do_extract']) && $data['do_extract'] === "true" ):
              $_file_path_ = get_attached_file($item_id);
              $_file_type = wp_check_filetype($_file_path_);
              $_allowed_exif_exts = ['jpg', 'jpeg', 'JPG', 'JPEG', 'TIFF', 'tiff'];

              if(isset($_file_path_) && in_array($_file_type['ext'], $_allowed_exif_exts) ) {

                try {
                    $_exif_data = exif_read_data($_file_path_, 0, true);
                } catch (Exception $ex) {
                    $_exif_data = false;
                }

                if($_exif_data && !empty($_exif_data) ) {
                    unset($_exif_data['FILE']);
                    unset($_exif_data['COMPUTED']);

                    $property_model = new PropertyModel();
                    $rootId = $this->get_category_root_of($col_id);
                    $_DATASET = ['collection_id' => $col_id, 'category_id' => $rootId, 'property_category_id' => $rootId];
                    $_col_metas = json_decode($property_model->list_property_data($_DATASET))->property_data;

                    $_props_slugs = [];
                    $_props_ids = [];
                    foreach($_col_metas as $_prop_data) {
                        $_formtd = explode("_", $_prop_data->slug);
                        array_push($_props_slugs, $_formtd[0] );
                        array_push($_props_ids, $_prop_data->id);

                        if( ("imagem-exif" == $_formtd[0]) && ($_exif_compound_parent['title'] == $_prop_data->name) ) {
                            $_exif_compound_parent['id'] = $_prop_data->id;
                        }
                    }

                    foreach($_exif_data as $exif_tag):
                        if(is_array($exif_tag)):
                            foreach($exif_tag as $exif_key => $exif_val):
                                $_meta_exif_data_name = "Exif_" . $exif_key;

                                if( strpos($exif_key, "UndefinedTag") === false ):
                                    $image_exif = [
                                        'collection_id' =>  $col_id,
                                        'property_category_id' => $this->get_category_root_of($col_id),
                                        'property_data_name' => $_meta_exif_data_name,
                                        'property_metadata_type' => 'text',
                                        'socialdb_property_required' => false,
                                        'socialdb_property_data_cardinality' => '1',
                                        'is_repository_property' => 'false'
                                    ];

                                    $_exif_slug = strtolower( sanitize_file_name( str_replace("_", "-", $_meta_exif_data_name) ));
                                    // If exif metadata already exists
                                    if( in_array($_exif_slug, $_props_slugs )) {
                                        $prop_index = array_search($_exif_slug, $_props_slugs);
                                        if($prop_index && $prop_index != false) {
                                            $prop_id = $_props_ids[$prop_index];
                                            if($prop_id) {
                                                $_meta_field = "socialdb_property_" . (string) $prop_id;
                                                update_post_meta($post_id, $_meta_field, $exif_val);
                                                array_push($_exif_compounds_array, $prop_id);
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

                                            array_push($_exif_compounds_array, $new_prop_id);
                                        }
                                    }
                                endif;
                            endforeach;
                        endif;
                    endforeach;
                }
            } // if file exists and is an image
          endif;
        }
      }
          $uniq_exifs = implode(",", array_unique($_exif_compounds_array));
          // if it has been set already
          if($_exif_compound_parent['id'] > 0) {
              $property_model = new PropertyModel;
              $property_model->update_property_compounds($_exif_compound_parent['id'], $_exif_compound_parent['title'], $data['collection_id'], $_DATASET['category_id'], $uniq_exifs, "1");
          } else {
              $property_model = new PropertyModel;
              $property_model->add_property_compounds($_exif_compound_parent['title'], $data['collection_id'], $_DATASET['category_id'], $uniq_exifs, "1");
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
              'post_type' => 'socialdb_object',
              'post_parent' => $data['collection_id']
          );
          if(!get_post_meta($item_id, 'socialdb_item_id')){
            $object_id = wp_insert_post($post);
            add_post_meta($item_id, 'socialdb_item_id', $object_id);
          }else{
              $post['ID'] = get_post_meta($item_id, 'socialdb_item_id',TRUE);
              wp_update_post($post);
              $object_id = $post['ID'];
              $this->delete_item_meta($post['ID']);
              wp_delete_object_term_relationships( $object_id, 'socialdb_category_type' );
          }
          delete_user_meta(get_current_user_id(), 'socialdb_collection_'.$data['collection_id'].'_betafile', $object_id);
          $this->set_common_field_values($object_id, 'title', $post['post_title']);
          $this->set_common_field_values($object_id, 'description', $post['post_content']);
          return $object_id;
      }else{
          return false;
      }

  }

    public function collectionHasItems($_col_id_) {
        $children = get_posts(['post_parent' => $_col_id_, 'post_type'=>'socialdb_object']);

        if( empty($children) ) {
            return 0;
        } else {
            return $children;
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
    public function item_resource($data,$item_id,$post_id) 
    {
        update_post_meta( $post_id, 'socialdb_object_from', 'internal');
        update_post_meta( $post_id, 'socialdb_object_dc_source', $data['source_'.$item_id]);
        update_post_meta( $post_id, 'socialdb_object_content', $item_id);
        update_post_meta( $post_id, 'socialdb_object_dc_type', $data['type_'.$item_id]);

        if(strcmp($data['type_'.$item_id],'pdf') == 0)
        {
            $url_file = wp_get_attachment_url($item_id);

            try
            {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($url_file);
                $pdf_text = $pdf->getText();
                error_reporting(1);
                
                $this->set_common_field_values($post_id, "socialdb_property_$item_id", $pdf_text);
            }catch (Exception $e)
            {
                //Can't read PDF file, just move on.
            }
        }else if(strcmp($data['type_'.$item_id], 'office') == 0)
        {
            $file_path = get_attached_file($item_id);
            
            $reader = new OfficeDocumentToPlainText($file_path);
            $document_text = $reader->getDocumentText();
            if($document_text)
            {
                $this->set_common_field_values($post_id, "socialdb_property_$item_id", $document_text);
            }
        }
        
        $this->set_common_field_values($post_id, 'object_from', 'internal');
        $this->set_common_field_values($post_id, 'object_source', $data['source_'.$item_id]);
        $this->set_common_field_values($post_id, 'object_type', $data['object_type']);
        $this->set_common_field_values($post_id, 'object_content', $data['type_'.$item_id]);
        if($data['type_'.$item_id]=='image' && get_post($item_id) && get_post($item_id)->post_type=='attachment'){
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

        //PDF Thumbnail
        if(strcmp($data['type_'.$item_id], 'pdf') == 0)
        {
            $upload_dir = wp_upload_dir();

            $upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data['pdf_thumbnail_'.$item_id]));
            $filename = 'pdf_thumb_'.$item_id.'.png';

            $hashed_filename = md5( $filename . microtime() ) . '_' . $filename;

            // @new
            $image_upload = file_put_contents( $upload_path . $hashed_filename, $image );

            //HANDLE UPLOADED FILE
            if( !function_exists( 'wp_handle_sideload' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }

            // Without that I'm getting a debug error!?
            if( !function_exists( 'wp_get_current_user' ) ) {
                require_once( ABSPATH . 'wp-includes/pluggable.php' );
            }

            $file             = array();
            $file['error']    = '';
            $file['tmp_name'] = $upload_path . $hashed_filename;
            $file['name']     = $hashed_filename;
            $file['type']     = 'image/png';
            $file['size']     = filesize( $upload_path . $hashed_filename );

            // upload file to server
            // @new use $file instead of $image_upload
            $file_return = wp_handle_sideload( $file, array( 'test_form' => false ) );

            $filename = $file_return['file'];
            $attachment = array(
                'post_mime_type' => $file_return['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                'post_content' => '',
                'post_status' => 'inherit',
                'guid' => $upload_dir['url'] . '/' . basename($filename)
            );
            $attach_id = wp_insert_attachment( $attachment, $filename );

            set_post_thumbnail($post_id, $attach_id);

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
            $col_id = $data['collection_id'];
            foreach ($items_id as $item_id) {
                //if((empty($selected_items_id)||!$selected_items_id||!is_array($selected_items_id)||empty($selected_items_id[0]))||  in_array($item_id, $selected_items_id)){
                    $post_id = absint($item_id);
                     $object = array(
                         'ID' => $post_id,
                         'post_title' => $data['title_'.$item_id],
                         'post_status' => (isset($data['edit_multiple'])) ? 'publish' :'inherit',
                         'post_content' => $data['description_'.$item_id],
                         'post_parent' => $col_id
                    );
                    delete_user_meta(get_current_user_id(), 'socialdb_collection_'.$col_id.'_betafile', $post_id);
                    wp_update_post($object);

                    if(!isset($data['edit_multiple']))
                    {
                        $this->insert_object_event($post_id, ['collection_id' => $col_id ]);
                    }

                    /* Getting PDF text */
                    $extension = end(explode(".", $data["source_$item_id"]));
                    if(strcmp($extension,'pdf') == 0)
                    {
                        $url_file = $data["source_$item_id"];

                        try
                        {
                            $parser = new \Smalot\PdfParser\Parser();
                            $pdf = $parser->parseFile($url_file);
                            $pdf_text = $pdf->getText();

                            $this->set_common_field_values($post_id, "socialdb_property_$item_id", $pdf_text);
                        }catch (Exception $e)
                        {
                            //Can't read PDF file, just move on.
                        }
                    }
                    /* Getting PDF text */

                    $this->set_common_field_values($post_id, 'title', $object['post_title']);
                    $this->set_common_field_values($post_id, 'description', $object['post_content']);

                    if($post_id){
                        if(!isset($data['edit_multiple'])){
                            $this->vinculate_collection($data, $post_id);
                        }
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
    
    /**
     * @signature - function insert_event($object_id, $data )
     * @param int $object_id O id do Objeto
     * @param array $data Os dados vindos do formulario
     * @return array os dados para o evento
     * @description - 
     * @author: Eduardo 
     */
    public function insert_object_event($object_id, $data) {
        $eventAddObject = new EventObjectCreateModel();
        $data['socialdb_event_object_item_id'] = $object_id;
        $data['socialdb_event_collection_id'] = $data['collection_id'];
        $data['socialdb_event_user_id'] = get_current_user_id();
        $data['socialdb_event_create_date'] = time();
        return $eventAddObject->create_event($data);
    }
################################################################################    
######################### importando multiplos arquivos ########################  
################################################################################     
    /**
     * 
     */
    public function insert_items_zip($data){
        if($data['files'] && !empty($data['files']) && $data['sendfile_zip'] !== 'url'){
            if($data['meta_zip'] == 'choose' && $data['chosen_meta'] ){
                $category_id = $data['chosen_meta'];
            }elseif($data['meta_name']){
                $array = wp_insert_term(trim($data['meta_name']), 'socialdb_category_type', 
                        array('parent' =>$this->get_category_root_id(),'slug' => $this->generate_slug(trim($data['meta_name']), 0)));
                add_term_meta($array['term_id'], 'socialdb_category_owner', get_current_user_id());
                $category_id = $array['term_id'];
                add_post_meta($data['collection_id'], 'socialdb_collection_facets', $category_id);
                update_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $category_id . '_widget', 'tree');
                update_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $category_id . '_color', 'color1');
                $this->add_property_term($this->get_category_root_of($data['collection_id']), $data['meta_name'], $category_id);
            }
            
            $dir = $this->unzip_items($data['files']);
            foreach (new DirectoryIterator($dir) as $fileInfo) {
                    if($fileInfo->isDot()) 
                        continue;
                    
                   $this->recursiveFolder($fileInfo,$data['collection_id'],$category_id, ($data['zip_folder_hierarchy'] == '1') ? true : false);
            }
        }else if($data['file_path'] && !empty($data['file_path'])){
             if($data['meta_zip'] == 'choose' && $data['chosen_meta'] ){
                $category_id = $data['chosen_meta'];
            }elseif($data['meta_name']){
                $array = wp_insert_term(trim($data['meta_name']), 'socialdb_category_type', 
                        array('parent' =>$this->get_category_root_id(),'slug' => $this->generate_slug(trim($data['meta_name']), 0)));
                add_term_meta($array['term_id'], 'socialdb_category_owner', get_current_user_id());
                $category_id = $array['term_id'];
                add_post_meta($data['collection_id'], 'socialdb_collection_facets', $category_id);
                update_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $category_id . '_widget', 'tree');
                update_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $category_id . '_color', 'color1');
                $this->add_property_term($this->get_category_root_of($data['collection_id']), $data['meta_name'], $category_id);
            }
            
            $dir = $this->unzip_items_by_name($data['file_path']);
            if($dir){
                foreach (new DirectoryIterator($dir) as $fileInfo) {
                        if($fileInfo->isDot()) 
                            continue;

                       $this->recursiveFolder($fileInfo,$data['collection_id'],$category_id, ($data['zip_folder_hierarchy'] == '1') ? true : false);
                }
            }else{
                return false;
            }
        }
        return json_encode(['url'=> get_the_permalink($data['collection_id'])]);
    }
    /*
     * @signature unzip_package()
     * @return string $targetdir o diretorio para onde foi descompactado o arquivo
     */
    public function unzip_items_by_name($path){
        if ($path) {
            $filename = basename($path);
            $tmp_name = $path;

            $name = explode(".", $filename);
            $continue = strtolower($name[1]) == 'zip' ? true : false; //Checking the file Extension

            if (!$continue) {
                $message = "The file you are trying to upload is not a .zip file. Please try again.";
            }


            /* here it is really happening */
            $ran = $name[0] . "-" . time() . "-" . rand(1, time());
            if(!is_dir(TAINACAN_UPLOAD_FOLDER . "/data/zip-items")){
                mkdir(TAINACAN_UPLOAD_FOLDER . "/data/zip-items");
            }
            $targetdir = TAINACAN_UPLOAD_FOLDER . "/data/zip-items/" . $ran;
            mkdir($targetdir);
            $targetzip = TAINACAN_UPLOAD_FOLDER . "/data/zip-items/" . $ran . ".zip";

            if (file_put_contents($targetzip,file_get_contents($tmp_name))) { //Uploading the Zip File

                /* Extracting Zip File */

                $zip = new ZipArchive();
                $x = $zip->open($targetzip);  // open the zip file to extract
                if ($x === true) {
                    $zip->extractTo($targetdir); // place in the directory with same name  
                    $zip->close();
                    unlink($targetzip); //Deleting the Zipped file
                }
            } 
        }
        return $targetdir;
    }
    
     /*
     * @signature unzip_package()
     * @return string $targetdir o diretorio para onde foi descompactado o arquivo
     */
    public function unzip_items($files){
        if ($files["file_zip"]["name"]) {
            $file = $files["file_zip"];
            $filename = $file["name"];
            $tmp_name = $file["tmp_name"];
            $type = $file["type"];

            $name = explode(".", $filename);
            $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');

            if (in_array($type, $accepted_types)) { //If it is Zipped/compressed File
                $okay = true;
            }

            $continue = strtolower($name[1]) == 'zip' ? true : false; //Checking the file Extension

            if (!$continue) {
                $message = "The file you are trying to upload is not a .zip file. Please try again.";
            }


            /* here it is really happening */
            $ran = $name[0] . "-" . time() . "-" . rand(1, time());
            if(!is_dir(TAINACAN_UPLOAD_FOLDER . "/data/zip-items")){
                mkdir(TAINACAN_UPLOAD_FOLDER . "/data/zip-items");
            }
            $targetdir = TAINACAN_UPLOAD_FOLDER . "/data/zip-items/" . $ran;
            $targetzip = TAINACAN_UPLOAD_FOLDER . "/data/zip-items/" . $ran . ".zip";

            if (move_uploaded_file($tmp_name, $targetzip)) { //Uploading the Zip File

                /* Extracting Zip File */

                $zip = new ZipArchive();
                $x = $zip->open($targetzip);  // open the zip file to extract
                if ($x === true) {
                    $zip->extractTo($targetdir); // place in the directory with same name  
                    $zip->close();
                    unlink($targetzip); //Deleting the Zipped file
                }
            } 
        }
        return $targetdir;
    }
    
    public function recursiveFolder($fileInfo,$collection_id,$parent,$is_hierarchy = false) {
        if(is_file($fileInfo->getPath(). '/' .$fileInfo->getFilename())){ 
            $this->insertFileItem($fileInfo, $collection_id, $parent);
        }elseif(is_dir($fileInfo->getPath(). '/' .$fileInfo->getFilename())){
            if($is_hierarchy && $fileInfo->getFilename() !== '__MACOSX'):
                $array = wp_insert_term(trim($fileInfo->getFilename()), 'socialdb_category_type', array('parent' => ($parent!=0) ? $parent : $this->get_category_root_id() ,
                'slug' => $this->generate_slug(trim($fileInfo->getFilename()), 0)));
                add_term_meta($array['term_id'], 'socialdb_category_owner', get_current_user_id());
            else:
                $array['term_id'] = 0;
            endif;
            foreach (new DirectoryIterator($fileInfo->getPath(). '/' .$fileInfo->getFilename()) as $fileInfoRecursive) {
                    if($fileInfoRecursive->isDot()) 
                        continue;
                    
                     if ($fileInfoRecursive->getFilename()[0] === '.') {
                        continue;
                     }
                    
                    $this->recursiveFolder($fileInfoRecursive,$collection_id,$array['term_id'],$is_hierarchy);
            }
        }  
    }
    
    /**
     * 
     * @param type $fileInfo
     * @param type $collection_id
     */
    public function insertFileItem($fileInfo,$collection_id,$parent = 0) {
        $object = array(
            'post_type' => 'socialdb_object',
            'post_title' => $fileInfo->getFilename(),
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
        );
        $object_id = wp_insert_post($object);
        $content_id = $this->insert_attachment_file($fileInfo->getPath(). '/' .$fileInfo->getFilename(), $object_id);
        add_post_meta($object_id, '_file_id', $content_id);
        update_post_meta($object_id, 'socialdb_object_content', $content_id);
         update_post_meta($object_id, 'socialdb_object_from', 'internal');
        $ext = strtolower(pathinfo($fileInfo->getPath(). '/' .$fileInfo->getFilename(), PATHINFO_EXTENSION));
        if (in_array($ext, ['mp4', 'm4v', 'wmv', 'avi', 'mpg', 'ogv', '3gp', '3g2'])) {
             update_post_meta($object_id, 'socialdb_object_dc_type',  'video');
        } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            set_post_thumbnail($object_id, $content_id);
            update_post_meta($object_id, 'socialdb_object_dc_type',  'image');
        } elseif (in_array($ext, ['mp3', 'm4a', 'ogg', 'wav', 'wma'])) {
             update_post_meta($object_id, 'socialdb_object_dc_type', 'audio');
        } elseif (in_array($ext, ['pdf'])) {
             update_post_meta($object_id, 'socialdb_object_dc_type',  'pdf');
        }else{
             update_post_meta($object_id, 'socialdb_object_dc_type', 'other');
        }
        $category_root_id = $this->get_category_root_of($collection_id);
        //categoria raiz da colecao
        wp_set_object_terms($object_id, array((int) $category_root_id), 'socialdb_category_type');
        if($parent != 0){
            wp_set_object_terms($object_id, array((int) $parent), 'socialdb_category_type',true);
        }
        
    }
    
      /**
     * function add_property_term($property)
     * @param object $property
     * @return int O id da da propriedade criada.
     * @author: Eduardo Humberto 
     */
   public function add_property_term($category_root_id,$name,$term_root) {
        $new_property = wp_insert_term($name, 'socialdb_property_type', array('parent' => $this->get_property_type_id('socialdb_property_term'),
                'slug' => $this->generate_slug($name, 0)));
        update_term_meta($new_property['term_id'], 'socialdb_property_term_cardinality', '1');
        update_term_meta($new_property['term_id'], 'socialdb_property_term_widget', 'tree');
        update_term_meta($new_property['term_id'], 'socialdb_property_term_root',$term_root);  
        update_term_meta($new_property['term_id'], 'socialdb_property_created_category',$category_root_id);
        add_term_meta($category_root_id, 'socialdb_category_property_id', $new_property['term_id']);
        return $new_property['term_id'];
   }
}
