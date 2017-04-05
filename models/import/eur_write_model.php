<?php

ini_set('max_input_vars', '10000');
error_reporting(0);
session_write_close();
ini_set('max_execution_time', '0');
ini_set('memory_limit', '-1');
/**
 * Description of Europeana_model
 *
 * @author MB
 */
class EurWriteModel extends Model {

    public $collection_id;
    public $Url_base;
    public $Root_category;
    public $Item_type;
    public $Item_content;
    public $Item_from;
    public $User;
    public $Pass;
    public $Query;
    public $Result;
    public $totalResults;
    public $InsertedItems;

    function __construct($collection_id) {
        $this->collection_id = $collection_id;
        //$this->Root_category = 138;
        $this->Item_type = 'image';
        $this->Item_from = 'external';
        $this->InsertedItems = 0;
    }
    
    function getInsertedItems() {
        return $this->InsertedItems;
    }

    function insert_items_full(array $items, array $metadados) {
        $this->InsertedItems = 0;
        if (!empty($items)) {
            foreach ($items as $item) {
                $this->Item_type = (isset($item["type"]) ? $this->setType($item['type'], $item["guid"]) : false);
                $thumb = (isset($item["edmPreview"]) && is_array($item["edmPreview"]) ? $this->setImage($item['edmPreview'][0]) : false);

                if ($this->Item_type && $thumb) {
                    $this->DoInsert($item, $thumb, $metadados);
                }
            }
        }
    }

    function insert_items(array $items) {
        if (!empty($items)) {
            foreach ($items as $item) {
                $this->Item_type = (isset($item["type"]) ? $this->setType($item['type'], $item["guid"]) : false);
                $thumb = (isset($item["edmPreview"]) && is_array($item["edmPreview"]) ? $this->setImage($item['edmPreview'][0]) : false);

                if ($this->Item_type && $thumb) {
                    $this->DoInsert($item, $thumb);
                }
            }
        }
    }

    function DoInsert(array $item, $thumb, $post_meta_full = false) {
        $this->InsertedItems++;
        $object = array(
            'post_type' => 'socialdb_object',
            'post_title' => (isset($item["title"]) && is_array($item["title"]) ? $item["title"][0] : $item["title"]),
            'post_status' => 'publish',
            'post_content' => (isset($item["dcDescriptionLangAware"]['def']) && is_array($item["dcDescriptionLangAware"]['def']) ? implode(', ', $item["dcDescriptionLangAware"]['def']) : ''),
            'post_author' => $this->User
        );
       $object_id = wp_insert_post($object);
       update_post_meta($data['ID'], 'socialdb_object_content', (isset($item["edmPreview"]) && is_array($item["edmPreview"]) ? implode(', ', $item["edmPreview"]) : ''));
       $this->set_common_field_values($object_id, 'title', (isset($item["title"]) && is_array($item["title"]) ? $item["title"][0] : $item["title"]));
       $this->set_common_field_values($object_id, 'description', (isset($item["dcDescriptionLangAware"]['def']) && is_array($item["dcDescriptionLangAware"]['def']) ? implode(', ', $item["dcDescriptionLangAware"]['def']) : ''));
       //categoria raiz da colecao
       update_post_meta($object_id, 'socialdb_object_from',$this->Item_from);
       $this->set_common_field_values($object_id, 'object_from',$this->Item_from);
       update_post_meta($object_id, 'socialdb_object_dc_source', (string) $item["guid"]);
       $this->set_common_field_values($object_id, 'object_source', (string) $item["guid"]);
       update_post_meta($object_id, 'socialdb_object_dc_type', $this->Item_type);
       $this->set_common_field_values($object_id, 'object_type', $this->Item_type);
       update_post_meta($object_id, 'link_thumb', $thumb);
       $this->add_thumbnail_url($thumb, $object_id);
        //categoria raiz da colecao
        wp_set_object_terms($object_id, array((int) $this->Root_category), 'socialdb_category_type');
        if ($post_meta_full) {
            foreach ($post_meta_full as $meta) {
                $meta_value = '';
                if (isset($item[$meta['metadado']])) {
                    if (is_array($item[$meta['metadado']])) {
                        $first_key = key($item[$meta['metadado']]);
                        if(is_array($item[$meta['metadado']][$first_key])) {
                            foreach ($item[$meta['metadado']] as $meta_array){
                                $meta_value .= implode(', ', $meta_array);
                            }
                        }else{
                            $meta_value = implode(', ', $item[$meta['metadado']]);
                        }
                    }else{
                        $meta_value = $item[$meta['metadado']];
                    }
                    add_post_meta($object_id, 'socialdb_property_' . $meta['metadata_id'], (string) $meta_value);
                    $this->set_common_field_values($object_id,'socialdb_property_' . $meta['metadata_id'], (string) $meta_value);
                }
            }
        }
    }

    function setType($type, $content) {
        switch ($type):
            case 'TEXT':
                $new_type = 'other';
                $this->Item_content = $content;
                break;

            case 'IMAGE':
                $new_type = 'image';
                $this->Item_content = false;
                break;

            case 'SOUND':
                $new_type = 'other';
                $this->Item_content = $content;
                break;

            case 'VIDEO':
                $new_type = 'video';
                $this->Item_content = false;
                break;

            case '3D':
                $new_type = 'other';
                $this->Item_content = $content;
                break;

            default:
                $new_type = 'other';
                $this->Item_content = $content;
                break;
        endswitch;

        return strtolower($new_type);
    }

    function setImage($image) {
        //http://europeanastatic.eu/api/image?uri=http%3A%2F%2Fpurl.pt%2F13970%2Fcover.get&size=LARGE&type=TEXT

        //$new_image = explode('&', $image)[0];
        //$new_image = $this->removeQueryInImageUrl($new_image);

        return $image;
    }

    public function removeQueryInImageUrl($url) {
        $types = ['jpg', 'jpeg', 'png', 'gif'];
        foreach ($types as $type) {
            if (strpos($url, $type) !== false) {
                $uri = parse_url($url);
                $uri = explode('=', urldecode($uri['query']))[1];
                $url = parse_url($uri);
                return sprintf('%s://%s%s', $url['scheme'], $url['host'], $url['path']);
            }
        }
        return $url;
    }

    public function getCollection() {
       return $this->collection_id;
    }

    public function getRootCategory($collection_id) {
        $this->Root_category = $this->get_category_root_of($collection_id) ;
        return $this->Root_category;
    }
    
    /**
     * function add_property_data($property)
     * @param object $property
     * @return int O id da da propriedade criada.
     * @author: Eduardo Humberto 
     */
   public function createMetadata($metadado, $type = 'text') {
        $new_property = wp_insert_term((string)$metadado, 'socialdb_property_type', array('parent' => $this->get_property_type_id('socialdb_property_data'),
                'slug' => $this->generate_slug($metadado, 0)));
       
        update_term_meta($new_property['term_id'], 'socialdb_property_data_widget', $type);
        update_term_meta($new_property['term_id'], 'socialdb_property_created_category',$this->Root_category);
         add_term_meta($this->Root_category, 'socialdb_category_property_id',$new_property['term_id']);
        return $new_property['term_id'];
   }
   
   /**
     * function get_property_type_id($property_parent_name)
     * @param string $property_parent_name
     * @return int O id da categoria que determinara o tipo da propriedade.
     * @author: Eduardo Humberto 
     */
    public function get_property_type_id($property_parent_name) {
        $property_root = get_term_by('name', $property_parent_name, 'socialdb_property_type');
        return $property_root->term_id;
    }

}
