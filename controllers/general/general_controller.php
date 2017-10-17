<?php
include_once (dirname(__FILE__) . '/../../../../../wp-config.php');
include_once (ABSPATH . '/wp-load.php');
include_once (ABSPATH . '/wp-includes/wp-db.php');

 class Controller {
    public function render($file, $variables = array()) {
        extract($variables);
        ob_start();
        include $file;
        $renderedView = ob_get_clean();

        return $renderedView;
    }

     function load_menu_styles() {
         $json_array = [];
         foreach( $this->get_menu_styles_ids() as $menu_id ):
             $json_array[] = $this->get_menu_style_json($menu_id);
         endforeach;
         return $json_array;
     }

     function load_menu_style_property($property) {
         $menu_data = [];
         foreach( $this->get_menu_styles_ids() as $menu_id ):
             $item_data = [ "id" => $menu_id, "terms" => $this->get_menu_style_property($menu_id, $property) ];
             $menu_data[] = $item_data;
         endforeach;

         return json_encode( $menu_data );
     }

     /**
      * @signature - get_menu_style_property
      * @param $menu_id - The id of the searched menu's json
      * @param $property - The searched property
      * @return string - property's json data, or false if not found
      * @description  Get the selected property from json file
      * @author: Rodrigo de Oliveira
      */
     protected function get_menu_style_property( $menu_id, $property ) {
         $menu_json = $this->get_menu_style_json( $menu_id );

         if( false != file_get_contents( $menu_json ) ):
             $parsed_json = json_decode( utf8_encode( file_get_contents( $menu_json )) );
             return $parsed_json->${property};
         else:
             return false;
         endif;
     }

     /**
      * @signature - get_menu_style_json
      * @param $menu_id - The id of the searched menu's json
      * @return string - path to menu's json file
      * @description  return the path to the chosen menu's json config file
      * @author: Rodrigo de Oliveira
      */
     protected function get_menu_style_json($menu_id) {
         return get_template_directory_uri() . "/extras/cssmenumaker/menus/" . $menu_id  ."/menu.json";
     }

     /**
      * @signature - get_menu_styles_ids
      * @return array of ids that will be used to select menu filter's style
      * @description  Get ids of pre-defined CssMenuMaker styles to use in Filter / Search Menu
      * @author: Rodrigo de Oliveira
      */
     protected function get_menu_styles_ids() {
         $styles_ids = [];
         if ($handle = opendir('../../extras/cssmenumaker/menus')) {
             while (false !== ($menu_file = readdir($handle))) {
                 if( is_numeric($menu_file) ):
                     $styles_ids[] = $menu_file;
                 endif;
             }
             closedir($handle);
         }
         return $styles_ids;
     }

     /**
      * @signature - get_selected_menu_style
      * @param int $collection_id
      * @return int $return
      * @description  selected menu style id for the current facet
      * @author: Rodrigo de Oliveira
      */
     protected function get_selected_menu_style( $collection_id ) {
         $facets_ids = array_filter( array_unique(get_post_meta( $collection_id, 'socialdb_collection_facets' ) ) );
         foreach( $facets_ids as $f_id ):
             $facet['id'] = $f_id;
             $facet['widget'] = get_post_meta($collection_id, 'socialdb_collection_facet_' . $f_id . '_widget', true);
             if( $facet['widget'] === 'menu' ):
                 $selected_menu_style_id = get_post_meta( $collection_id, 'socialdb_collection_facet_' . $f_id . '_menu_style', true);
             endif;
         endforeach;
         $return = str_replace( "menu_style_", "", $selected_menu_style_id);

         return $return;
     }
     
     
    /**
     * function get_collections_json()
     * @param array Os dados vindo do formulario
     * @return json com o id e o nome de cada colecao
     * @author Eduardo Humberto
     */
    public function get_collections_json($data) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $query = "
                        SELECT p.* FROM $wp_posts p
                        WHERE p.post_type like 'socialdb_collection' and p.post_status like 'publish' and p.post_title LIKE '%{$data['term']}%'
                ";
        $result = $wpdb->get_results($query);
        if ($result) {
            foreach ($result as $collection) {
                $json[] = array('value' => $collection->ID, 'label' => $collection->post_title, 'permalink' => get_permalink($collection->ID));
            }
        }
        return json_encode($json);
    }
    
    
    /**
     * metodo que busca se a pagina ja possui o cache e retorna seu html
     * 
     * @param int $collection_id
     * @param string $operation A operacao que se deseja buscar no cache
     * @return boolean
     */
    public function has_cache($collection_id,$operation){
        $collection = get_post($collection_id);
        if(is_file(TAINACAN_UPLOAD_FOLDER.'/cache/'.$collection->post_name.'/'.$operation.'.html')){
            return file_get_contents(TAINACAN_UPLOAD_FOLDER.'/cache/'.$collection->post_name.'/'.$operation.'.html');
        }else{
            return false;
        }
    }
    
}