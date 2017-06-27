<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once (dirname(__FILE__) . '../../../models/collection/collection_model.php');
include_once (dirname(__FILE__) . '../../../models/category/category_model.php');
include_once (dirname(__FILE__) . '../../../models/event/event_object/event_object_create_model.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../user/user_model.php');
require_once(dirname(__FILE__) . '../../tag/tag_model.php');

/**
 * The class ObjectModel
 *
 */
class ObjectFileModel extends Model {

    /**
     * @signature - fast_insert_url($data)
     * @param array $data Os dados vindos do formulario
     * @return json com os dados do resultado do evento criado
     * @description - Insere um objeto apenas com o titulo
     * @author: Eduardo 
     */
    public function list_files($data) {
        $post = get_post($data['object_id']);
        $result = array();
        if (!is_object(get_post_thumbnail_id())) {
            $args = array(
                'post_type' => 'attachment',
                'numberposts' => -1,
                'post_status' => null,
                'post_parent' => $post->ID,
                'exclude' => get_post_thumbnail_id()
            );

            $attachments = get_posts($args);
            $arquivos = get_post_meta($post->ID, '_file_id');
            if ($attachments) {
                foreach ($attachments as $attachment) {
                    if (is_array($arquivos)&&in_array($attachment->ID, $arquivos)) {
                        $object_content = get_post_meta($data['object_id'],'socialdb_object_content',true);
                        if($object_content!=$attachment->ID){
                            $metas = wp_get_attachment_metadata($attachment->ID);
                            $obj['name'] = $attachment->post_title;
                            $obj['ID'] = $attachment->ID;
                            $obj['size'] = filesize(get_attached_file($attachment->ID));
                            $result[] = $obj;
                        }
                    }
                }
            }
        }
        if (!empty($result)) {
            header('Content-type: text/json');              //3
            header('Content-type: application/json');
            echo json_encode($result);
        } else {
            echo '0';
        }
    }
    /**
     * @signature - save_file($data)
     * @param array $data Os dados vindos do formulario
     * @return json com os dados do resultado do evento criado
     * @description - Insere um objeto apenas com o titulo
     * @author: Eduardo 
     */
    public function save_file($data) {
        if ($_FILES) {
            foreach ($_FILES as $file => $array) {
                if (!empty($_FILES[$file]["name"])) {
                    //$_FILES[$file]["name"] = $this->remove_accent_file($_FILES[$file]["name"]);
                    $_FILES[$file]["name"] = remove_accents($_FILES[$file]["name"]);
                     $newupload = $this->insert_attachment($file, $data['object_id']);
                     echo json_encode($newupload);
                }
            }
        }
    }
    
    /**
     * @signature - save_file($data)
     * @param array $data Os dados vindos do formulario
     * @return json com os dados do resultado do evento criado
     * @description - Insere um objeto apenas com o titulo
     * @author: Eduardo 
     */
    public function delete_file($data) {
        if ($data['object_id'] && $data['file_name']) {
            $post = get_post($data['object_id']);
            $result = '';
            if (!is_object(get_post_thumbnail_id())) {
                $args = array(
                    'post_type' => 'attachment',
                    'numberposts' => -1,
                    'post_status' => null,
                    'post_parent' => $post->ID,
                    'exclude' => get_post_thumbnail_id()
                );
                //  var_dump($args);
                $attachments = get_posts($args);
                $arquivos = get_post_meta($post->ID, '_file_id');
                if ($attachments) {
                    foreach ($attachments as $attachment) {
                        if(is_numeric($data['file_name'])){
                            if (in_array($attachment->ID, $arquivos) && $data['file_name'] == $attachment->ID) {
                                $result = wp_delete_attachment($attachment->ID);
                            }
                        }else{
                            $filename = explode('.', $data['file_name'])[0];
                            if (in_array($attachment->ID, $arquivos) && str_replace(' ','-',urldecode($filename)) == urldecode($attachment->post_title)) {
                                $result = wp_delete_attachment($attachment->ID);
                            }
                        }
                    }
                }
            }
        }
        if (!$result) {
            echo 'false';
        } else {
            echo 'true';
        }
    }
    
    /**
     * @signature - save_file($data)
     * @param array $data Os dados vindos do formulario
     * @return json com os dados do resultado do evento criado
     * @description - Insere um objeto apenas com o titulo
     * @author: Eduardo 
     */
    public function show_files($data) {
        $real_attachments = [];
        if ($data['object_id']) {
            $post = get_post($data['object_id']);
            $result = '';
            if (!is_object(get_post_thumbnail_id())) {
                $args = array(
                    'post_type' => 'attachment',
                    'numberposts' => -1,
                    'post_status' => null,
                    'post_parent' => $post->ID,
                    'exclude' => get_post_thumbnail_id()
                );
                //  var_dump($args);
                $attachments = get_posts($args);
                $arquivos = get_post_meta($post->ID, '_file_id');
                $object_content = get_post_meta($data['object_id'],'socialdb_object_content',true);
                if ($attachments) {
                    foreach ($attachments as $attachment) {
                        if (in_array($attachment->ID, $arquivos)&&$object_content!=$attachment->ID) {
                             $metas = wp_get_attachment_metadata($attachment->ID);
                            $real_attachments['posts'][] = $attachment;
                            $extension = $attachment->guid;
                            $ext = pathinfo($extension, PATHINFO_EXTENSION);
                            if(in_array($ext, ['mp4','m4v','wmv','avi','mpg','ogv','3gp','3g2'])){
                                    $real_attachments['videos'][] = $attachment;     
                             }elseif (in_array($ext, ['jpg','jpeg','png','gif'])) {
                                     $obj['metas'] = $metas;
                                    $real_attachments['image'][] = $attachment;     
                             }elseif (in_array($ext, ['mp3','m4a','ogg','wav','wma'])) {
                                    $real_attachments['audio'][] = $attachment;     
                             }elseif(in_array($ext, ['pdf'])){
                                    $real_attachments['pdf'][] = $attachment;   
                             }else{
                                    $real_attachments['others'][] = $attachment;
                             }
                        }
                    }
                } 
            }
        }
        if(!empty($real_attachments)){
            return $real_attachments;
        }else{
            return false;
        }
    }
    
      /**
     * @signature - get_files($data)
     * @param array $data Os dados vindos do formulario
     * @return json com os dados do resultado do evento criado
     * @description - Insere um objeto apenas com o titulo
     * @author: Eduardo 
     */
    public function get_files($data) {
        $post = get_post($data['object_id']);
        $result = array();
        if (!is_object(get_post_thumbnail_id())) {
            $args = array(
                'post_type' => 'attachment',
                'numberposts' => -1,
                'post_status' => null,
                'post_parent' => $post->ID,
                'exclude' => get_post_thumbnail_id()
            );
            //  var_dump($args);
            $attachments = get_posts($args);
            $arquivos = get_post_meta($post->ID, '_file_id');
            if ($attachments) {
                foreach ($attachments as $attachment) {
                    if (in_array($attachment->ID, $arquivos)) {
                        $_file_path_ = get_attached_file($attachment->ID);
                        $metas = wp_get_attachment_metadata($attachment->ID);
                        $obj['ID'] = $attachment->ID;
                        $obj['name'] = $attachment->post_title;
                        $obj['size'] = filesize($_file_path_);
                        $extension = $attachment->guid;
                        $ext = pathinfo($extension, PATHINFO_EXTENSION);
                        if(in_array($ext, ['mp4','m4v','wmv','avi','mpg','ogv','3gp','3g2'])){
                            $result['videos'][] = $obj;     
                        }elseif (in_array($ext, ['jpg','jpeg','png','gif', 'tiff'])) {
                           $obj['metas'] = $metas;
                           $result['image'][] = $obj;
                           /*
                            * TODO: confirm if code below should be removed
                            * */
                           if( in_array($ext, ['jpg', 'jpeg', 'tiff']) ) {
                               /*
                               $property_model = new PropertyModel();
                               $_exif_data = exif_read_data($_file_path_, 0, true);
                               unset($_exif_data['FILE']);
                               unset($_exif_data['COMPUTED']);
                               */
                           }

                        }
                        elseif (in_array($ext, ['mp3','m4a','ogg','wav','wma']))
                        {
                           $result['audio'][] = $obj;
                        }
                        elseif(in_array($ext, ['pdf']))
                        {
                           $result['pdf'][] = $obj;
                        }
                        elseif (in_array($ext, ['doc', 'docx', 'pptx', 'xlsx']))
                        {
                            $result['office'][] = $obj;

                            $last_position = count($result['office']) - 1;
                            
                            $result['office'][$last_position]['ext'] = $ext;
                        }
                        else{
                            $result['others'][] = $obj;
                        }
                        
                    }
                }
            }
        }
        return $result;
    }
     /**
     * @signature - get_inserted_items_social_network($data)
     * @param array $data Os dados vindos do formulario
     * @return json com os dados do resultado do evento criado
     * @description - Insere um objeto apenas com o titulo
     * @author: Eduardo 
     */
    public function get_inserted_items_social_network($data) {
        $result = [];
        $items = $data['items_id'];
        if(is_array($items)){
            foreach ($items as $item_id) {
                $item = get_post($item_id);
                 $obj['ID'] = $item->ID;
                 $obj['name'] = $item->post_title;
                 $obj['content'] = $item->post_content;
                $tags_id = $this->get_object_tags_id($item->ID);
                if(isset($tags_id)){
                    $tags_name = [];
                    foreach ($tags_id as $tag) {
                        $tags_name[] = get_term_by('id',$tag,'socialdb_tag_type')->name; 
                    }
                    $obj['tags'] = implode(',', $tags_name);
                }
                $properties = $this->get_properties_object($item->ID); 
                if($properties && is_array($properties)){
                    $obj['properties'] = $properties;
                }
                $type = get_post_meta($item_id,'socialdb_object_dc_type',true);
                $obj['source'] = get_post_meta($item_id,'socialdb_object_dc_source',true);;
                if($type=='video'){
                    $result['videos'][] = $obj;   
                }elseif($type=='image'){
                    $result['image'][] = $obj;     
                }elseif($type=='text'){
                     $result['text'][] = $obj;   
                }elseif($type=='pdf'){
                     $result['text'][] = $obj;   
                }elseif($type=='other'||$type=='others'){
                     $result['text'][] = $obj;   
                }elseif($type=='audio'){
                     $result['text'][] = $obj;   
                }else{
                    $result['text'][] = $obj;  
                }
                $result['items'][] = $obj;
            }
        }
        return $result;
    }
    
    
    function remove_accent_file($str) {
        $a = array('�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '�', '�', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '�', '�', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '�', '?', '?', '?', '?', '�', '�', '?', '�', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        return str_replace($a, $b, $str);
    }

}
