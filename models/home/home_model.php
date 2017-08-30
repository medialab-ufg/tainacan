<?php
include_once(dirname(__FILE__).'/../../helpers/view_helper.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');

class HomeModel extends Model {
    public function display_view_main_page($data) {
        $data['first_items'] = [ ['title' => __("Featured Collections", "tainacan"), 'data' => $this->get_popular() ],
                                 ['title' => __("Recent Collections", "tainacan"), 'data' => $this->get_recent() ]  ];
        return $data;
    }

    public function get_popular() {
        $root_id = (int) get_option('collection_root_id');
        return get_posts( [ 'post_type' => 'socialdb_collection', 'posts_per_page' => 20,
            'meta_key' => 'collection_view_count', 'orderby' => 'meta_value_num', 'exclude' => $root_id ] );
    }

    public function get_recent() {
        return get_posts( [ 'post_type' => 'socialdb_collection', 'posts_per_page' => 20 ] );
    }

    /**
     * @signature - get_items_of_type
     * @param $type - string containing the name of item type to be fetched
     * @return array - array of posts with that type
     * @description  Get 20 posts from certain data type
     * @author: Rodrigo de Oliveira
     */
    public function get_items_of_type($type) {
        $public_values = [];
        $viewHelper = new ViewHelper();
        $array_posts = get_posts( [ 'post_type' => 'socialdb_object','post_status' => 'publish', 'meta_key' => 'socialdb_object_dc_type',
                'meta_value' => $type, 'posts_per_page' => 20 ] );
        foreach ($array_posts as $post) {
            $collection = $viewHelper->helper_get_collection_by_object($post->ID)[0];
            if(isset($collection->ID) && $viewHelper->check_privacity_collection($collection->ID))
               $public_values[] =  $post;
        }
        return $public_values;
    }
    
    /**
     * @signature - format_item_data
     * @param $items_array - array of items to be formatted
     * @return array - array of posts formatted to be presented as needed at Home Page
     * @description  Formats item array to separete it's type, thumbnail and collection name, as long as its own data
     * @author: Rodrigo de Oliveira
     */
   public function format_item_data( $items_array ) {
       $formatted_items = [];
       foreach($items_array as $object):
           $post_meta = get_post_meta($object->ID, 'socialdb_object_dc_type')[0];
           $collection_name = $this->get_collection_by_object($object->ID)[0]->post_name;
           $thumbnail = $this->get_item_thumbnail($object->ID, $object->post_title);
           $item_terms = [ "type" => $post_meta, "collection_name" => $collection_name, "object" => $object, "thumbnail" => $thumbnail ];
           $formatted_items[] = $item_terms;
       endforeach;

       return $formatted_items;
   }

    /**
     * @signature - get_item_thumbnail
     * @param $item_id - id of item to be fetched
     * @param $title - item title
     * @return string - html of item thumb or it's formatted name
     * @description  Returns item thumbnail or, if it's empty, item name properly formatted
     * @author: Rodrigo de Oliveira
     */
    private function get_item_thumbnail($item_id, $title) {
        $no_thumb = '<div class="tainacan-thumbless">'.ucwords(htmlentities($title[0]{0})) . ucwords(htmlentities($title[1]{0})).'</div>';
        $return = has_post_thumbnail($item_id) ? get_the_post_thumbnail($item_id, 'thumbnail') : $no_thumb;

        $html_image = wp_get_attachment_image(get_post_thumbnail_id($item_id), 'thumbnail', false, array('class' => 'img-responsive'));
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($item_id), "thumbnail", false);

        if(preg_match("/pdf_thumb_/", basename($image[0])))
        {
            $DOM = simplexml_load_string($html_image);
            $DOM->attributes()->height = "150";
            $DOM->attributes()->width = "150";

            $html_image = $DOM->asXML();

            return $html_image;
        }

        return $return;
    }

    /**
     * @signature - get_all_item_types
     * @param $types - array of type names to be fetched
     * @return array - posts separated by it's type name
     * @description  Returns array of posts according to it's collection name and type
     * @author: Rodrigo de Oliveira
     */
    public function get_all_item_types( $types ) {
        $type_items_array = [];
        if ( is_array( $types ) ):
            foreach( $types as $type ):
                $type_items = $this->get_items_of_type($type);
                $collection_name = $this->get_collection_by_object( $type_items[0]->ID )[0]->post_name;
                $type_items_array[] = [ "type" => $type, "collection_name" => $collection_name, "object" => $type_items ];
            endforeach;
        endif;

        return $type_items_array;
    }
    /*
     * verificando se a acao eh permitida
     */
    public function verifyAction($data) {
      $json['isAllowed'] =  verify_allowed_action($data['collection_id'], $data['action'],$data['object_id']);
      return json_encode($json);
    }

}