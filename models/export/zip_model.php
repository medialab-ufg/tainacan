<?php

/**
 * Author: Eduardo Humberto
 */
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../property/property_model.php');
require_once(dirname(__FILE__) . '../../category/category_model.php');
require_once(dirname(__FILE__) . '../../object/object_model.php');
require_once(dirname(__FILE__) . '../../collection/collection_model.php');

class ZipModel extends Model {

     /**
     * @signature - generate_collection_template($dir,$collection_id)
     * @param string $dir O diretorio da colecao sera colocada como template
     * @param int $collection_id O id da colecao
     * @description - funcao que exporta toda a colecao para a pasta de templates
     * @author: Eduardo 
     */
    public function generate_collection_template($dir,$collection_id) {
        $this->recursiveRemoveDirectory($dir.'/package/taxonomies');
        $this->recursiveRemoveDirectory($dir.'/package/items');
        $this->recursiveRemoveDirectory($dir.'/package/metadata');
        mkdir($dir.'/package');
        mkdir($dir.'/package/taxonomies');
        mkdir($dir.'/package/items');
        mkdir($dir.'/package/metadata');
        //array_map('unlink', glob(dirname(__FILE__).'/package/items/*'));
        //array_map('unlink', glob(dirname(__FILE__).'/package/metadata/*'));
        $this->generate_taxonomy_files($collection_id,$dir);
        $this->get_collection_images($collection_id,$dir);
        $this->export_items($collection_id,$dir);
        if(is_file($dir.'/package/metadata/administrative_settings.xml')){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * metodo que remove o template da pasta
     * 
     * @param type $dir
     * @return boolean se o diretorio foi removido
     */
    public function remove_template($dir) {
         $this->recursiveRemoveDirectory($dir);
         if(!is_dir($dir)){
             return true;
         }else{
            return false; 
         }
    }
    
    
      /**
     * @signature - export_collection($collection_id)
     * @param int $collection_id O id da colecao
     * @return string O identifier do item a ser utilizado no form para o mapeamento
     * @description - funcao que exporta toda a colecao para um arquivo zip
     * @author: Eduardo 
     */
    public function export_collection($collection_id){
        //array_map('unlink', glob(dirname(__FILE__).'/package/taxonomies/*'));
        $this->recursiveRemoveDirectory(dirname(__FILE__).'/package/taxonomies');
        $this->recursiveRemoveDirectory(dirname(__FILE__).'/package/items');
        $this->recursiveRemoveDirectory(dirname(__FILE__).'/package/metadata');

        //Create directories
        if(!is_dir(dirname(__FILE__).'/package/')){
             mkdir(dirname(__FILE__).'/package');
        }
        mkdir(dirname(__FILE__).'/package/taxonomies');
        mkdir(dirname(__FILE__).'/package/items');
        mkdir(dirname(__FILE__).'/package/metadata');
        //array_map('unlink', glob(dirname(__FILE__).'/package/items/*'));
        //array_map('unlink', glob(dirname(__FILE__).'/package/metadata/*'));

	    //Generate files
        $this->generate_taxonomy_files($collection_id);//ok
        $this->get_collection_images($collection_id);
        $this->export_items($collection_id);

        //Zip file
        $this->create_zip_by_folder(dirname(__FILE__).'/');

        $this->download_send_headers(dirname(__FILE__).'/package.zip');
    }
     /**
     * @signature - get_collection_images($collection_id)
     * @param int $collection_id O id da colecao
     * @param string (Optional) O diretorio aonde sera criado
     * @return void copia o thumbnail e capa para as pastas qe serao zipadas
     * @author: Eduardo 
     */
    public function get_collection_images($collection_id,$dir = '') {
        if($dir==''){
           $dir =  dirname(__FILE__);
        }
        $thumbnail_id = get_post_thumbnail_id($collection_id);
        if($thumbnail_id){
          $fullsize_path = get_attached_file( $thumbnail_id ); // Full path
          $ext = pathinfo($fullsize_path, PATHINFO_EXTENSION);
          copy($fullsize_path, $dir.'/package/metadata/thumbnail.'.$ext);
        }
        if(get_post_meta($collection_id, 'socialdb_collection_cover_id',true)){
          $fullsize_path = get_attached_file(get_post_meta($collection_id, 'socialdb_collection_cover_id',true)); // Full path
          $ext = pathinfo($fullsize_path, PATHINFO_EXTENSION);
          copy($fullsize_path, $dir.'/package/metadata/cover.'.$ext);
        }

	    clean_post_cache($collection_id);
    }
    /**
     * @signature - export_collection($collection_id)
     * @param int $collection_id O id da colecao
     * @param string (Optional) O diretorio aonde sera criado
     * @return string O identifier do item a ser utilizado no form para o mapeamento
     * @description - funcao que exporta toda a colecao para um arquivo zip
     * @author: Eduardo 
     */
    public function generate_taxonomy_files($collection_id,$dir = '') {
        if($dir==''){
           $dir =  dirname(__FILE__);
        }
        $propertyModel = new PropertyModel;
        $categoryModel = new CategoryModel;
        $terms_id = [];
        $root_category = $this->get_category_root_of($collection_id);
        $all_properties_id = array_unique($this->get_parent_properties($root_category, [],$root_category));
        if ($all_properties_id) {
            foreach ($all_properties_id as $property_id) {
                $type = $propertyModel->get_property_type($property_id); // pego o tipo da propriedade
                if ($type == 'socialdb_property_term') {
                   $terms_id[] = get_term_meta($property_id, 'socialdb_property_term_root', true);
                }
            }
        }
        if(!in_array($root_category,$terms_id)){
            $terms_id[] = $root_category;
        }
        $categoryModel->export_zip_taxonomies(array_unique($terms_id),$dir,$collection_id);
        $xml_collection = $this->export_collection_settings($collection_id, $all_properties_id);
        $this->create_xml_file($dir.'/package/metadata/administrative_settings.xml', $xml_collection);

	    clean_post_cache($collection_id);
    }
    
    /**
     * @signature - download_send_headers($collection_id)
     * @param int 
     * @return 
     * @description - 
     * @author: Eduardo 
     */
    function download_send_headers($filename) {
        $file_name = basename($filename);
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Content-Length: " . filesize($filename));
        readfile($filename);
        unlink($filename);
        $this->recursiveRemoveDirectory(dirname(__FILE__).'/package/taxonomies');
        $this->recursiveRemoveDirectory(dirname(__FILE__).'/package/items');
        $this->recursiveRemoveDirectory(dirname(__FILE__).'/package/metadata');
        exit;
    }
    
    /**
     * @signature - download_send_headers($collection_id)
     * @param int $collection_id 
     * @return 
     * @description - 
     * @author: Eduardo 
     */
    function export_collection_settings($collection_id,$properties_id) {
        $collectionModel = new CollectionModel;
        $collection_post = get_post($collection_id);
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<collection>';
            $xml .= '<post_title>'.html_entity_decode($collection_post->post_title).'</post_title>';
            $xml .= '<post_content>'.html_entity_decode($collection_post->post_content).'</post_content>';
            $xml .= '<post_name>'.$collection_post->post_name.'</post_name>';
            $xml .= '<socialdb_collection_hide_tags>'. get_post_meta($collection_id, 'socialdb_collection_hide_tags', true).'</socialdb_collection_hide_tags>';
            $xml .= '<socialdb_collection_attachment>'. get_post_meta($collection_id, 'socialdb_collection_attachment', true).'</socialdb_collection_attachment>';
            $xml .= '<socialdb_collection_allow_hierarchy>'. get_post_meta($collection_id, 'socialdb_collection_allow_hierarchy', true).'</socialdb_collection_allow_hierarchy>';
            $xml .= '<parent>'.$this->get_privacity($collection_id).'</parent>';
            $xml .= '<socialdb_collection_object_type>'.get_post_meta($collection_id, 'socialdb_collection_object_type', true).'</socialdb_collection_object_type>';
            $xml .= '<socialdb_collection_default_ordering>'.get_post_meta($collection_id, 'socialdb_collection_default_ordering', true).'</socialdb_collection_default_ordering>';
            $xml .= '<socialdb_collection_ordenation_form>'.get_post_meta($collection_id, 'socialdb_collection_ordenation_form', true).'</socialdb_collection_ordenation_form>';
            $xml .= '<socialdb_collection_facet_widget_tree_orientation>'.get_post_meta($collection_id, 'socialdb_collection_facet_widget_tree_orientation', true).'</socialdb_collection_facet_widget_tree_orientation>';
            $xml .= '<socialdb_collection_mapping_exportation_active>'.get_post_meta($collection_id, 'socialdb_collection_mapping_exportation_active', true).'</socialdb_collection_mapping_exportation_active>';
                //design
            $xml .= '<socialdb_collection_board_background_color>' . get_post_meta($collection_id, 'socialdb_collection_board_background_color', true) . '</socialdb_collection_board_background_color>';
            $xml .= '<socialdb_collection_board_border_color>' . get_post_meta($collection_id, 'socialdb_collection_board_border_color', true) . '</socialdb_collection_board_border_color>';
            $xml .= '<socialdb_collection_board_font_color>' . get_post_meta($collection_id, 'socialdb_collection_board_font_color', true) . '</socialdb_collection_board_font_color>';
            $xml .= '<socialdb_collection_board_link_color>' . get_post_meta($collection_id, 'socialdb_collection_board_link_color', true) . '</socialdb_collection_board_link_color>';
            $xml .= '<socialdb_collection_board_skin_mode>' . get_post_meta($collection_id, 'socialdb_collection_board_skin_mode', true) . '</socialdb_collection_board_skin_mode>';
            $xml .= '<socialdb_collection_hide_title>' . get_post_meta($collection_id, 'socialdb_collection_hide_title', true) . '</socialdb_collection_hide_title>';
            $xml .= '<socialdb_collection_hide_description>' . get_post_meta($collection_id, 'socialdb_collection_hide_description', true) . '</socialdb_collection_hide_description>';
            $xml .= '<socialdb_collection_hide_thumbnail>' . get_post_meta($collection_id, 'socialdb_collection_hide_thumbnail', true) . '</socialdb_collection_hide_thumbnail>';
            $xml .= '<socialdb_collection_hide_menu>' . get_post_meta($collection_id, 'socialdb_collection_hide_menu', true) . '</socialdb_collection_hide_menu>';
            $xml .= '<socialdb_collection_hide_categories>' . get_post_meta($collection_id, 'socialdb_collection_hide_categories', true) . '</socialdb_collection_hide_categories>';
            $xml .= '<socialdb_collection_hide_rankings>' . get_post_meta($collection_id, 'socialdb_collection_hide_rankings', true) . '</socialdb_collection_hide_rankings>';
            $xml .= '<socialdb_collection_columns>' . get_post_meta($collection_id, 'socialdb_collection_columns', true) . '</socialdb_collection_columns>';
            $xml .= '<socialdb_collection_size_thumbnail>' . get_post_meta($collection_id, 'socialdb_collection_size_thumbnail', true) . '</socialdb_collection_size_thumbnail>';
            $xml .= '<socialdb_collection_submission_visualization>' . get_post_meta($collection_id, 'socialdb_collection_submission_visualization', true) . '</socialdb_collection_submission_visualization>';
            $xml .= '<socialdb_collection_default_tab>' . get_post_meta($collection_id, 'socialdb_collection_default_tab',true) . '</socialdb_collection_default_tab>';
            $xml .= '<socialdb_collection_update_tab_organization>' . get_post_meta($collection_id, 'socialdb_collection_update_tab_organization',true) . '</socialdb_collection_update_tab_organization>';
            
            $tabs = $this->sdb_get_post_meta_by_value($collection_id, 'socialdb_collection_tab');
            if($tabs &&  is_array($tabs)){
                $xml .= '<tabs>';
                foreach ($tabs as $tab) {
                   $xml .= '<tab>'; 
                   $xml .= '<id>'.$tab->meta_id.'</id>'; 
                   $xml .= '<name>'.$tab->meta_value.'</name>'; 
                   $xml .= '</tab>'; 
                }
                $xml .= '</tabs>';
            }
            
            $xml .= '<permissions>';
                $xml .= '<socialdb_collection_permission_create_category>'.get_post_meta($collection_id, 'socialdb_collection_permission_create_category', true).'</socialdb_collection_permission_create_category>';
                $xml .= '<socialdb_collection_permission_edit_category>'.get_post_meta($collection_id, 'socialdb_collection_permission_edit_category', true).'</socialdb_collection_permission_edit_category>';
                $xml .= '<socialdb_collection_permission_delete_category>'.get_post_meta($collection_id, 'socialdb_collection_permission_delete_category', true).'</socialdb_collection_permission_delete_category>';
                $xml .= '<socialdb_collection_permission_add_classification>'.get_post_meta($collection_id, 'socialdb_collection_permission_add_classification', true).'</socialdb_collection_permission_add_classification>';
                $xml .= '<socialdb_collection_permission_delete_classification>'.get_post_meta($collection_id, 'socialdb_collection_permission_delete_classification', true).'</socialdb_collection_permission_delete_classification>';
                $xml .= '<socialdb_collection_permission_create_object>'.get_post_meta($collection_id, 'socialdb_collection_permission_create_object', true).'</socialdb_collection_permission_create_object>';
                $xml .= '<socialdb_collection_permission_delete_object>'.get_post_meta($collection_id, 'socialdb_collection_permission_delete_object', true).'</socialdb_collection_permission_delete_object>';
                $xml .= '<socialdb_collection_permission_create_property_data>'.get_post_meta($collection_id, 'socialdb_collection_permission_create_property_data', true).'</socialdb_collection_permission_create_property_data>';
                $xml .= '<socialdb_collection_permission_edit_property_data>'.get_post_meta($collection_id, 'socialdb_collection_permission_edit_property_data', true).'</socialdb_collection_permission_edit_property_data>';
                $xml .= '<socialdb_collection_permission_delete_property_data>'.get_post_meta($collection_id, 'socialdb_collection_permission_delete_property_data', true).'</socialdb_collection_permission_delete_property_data>';
                $xml .= '<socialdb_collection_permission_edit_property_data_value>'.get_post_meta($collection_id, 'socialdb_collection_permission_edit_property_data_value', true).'</socialdb_collection_permission_edit_property_data_value>';
                $xml .= '<socialdb_collection_permission_create_property_object>'.get_post_meta($collection_id, 'socialdb_collection_permission_create_property_object', true).'</socialdb_collection_permission_create_property_object>';
                $xml .= '<socialdb_collection_permission_edit_property_object>'.get_post_meta($collection_id, 'socialdb_collection_permission_edit_property_object', true).'</socialdb_collection_permission_edit_property_object>';
                $xml .= '<socialdb_collection_permission_delete_property_object>'.get_post_meta($collection_id, 'socialdb_collection_permission_delete_property_object', true).'</socialdb_collection_permission_delete_property_object>';
                $xml .= '<socialdb_collection_permission_edit_property_object_value>'.get_post_meta($collection_id, 'socialdb_collection_permission_edit_property_object_value', true).'</socialdb_collection_permission_edit_property_object_value>';
                $xml .= '<socialdb_collection_permission_create_comment>'.get_post_meta($collection_id, 'socialdb_collection_permission_create_comment', true).'</socialdb_collection_permission_create_comment>';
                $xml .= '<socialdb_collection_permission_edit_comment>'.get_post_meta($collection_id, 'socialdb_collection_permission_edit_comment', true).'</socialdb_collection_permission_edit_comment>';
                $xml .= '<socialdb_collection_permission_delete_comment>'.get_post_meta($collection_id, 'socialdb_collection_permission_delete_comment', true).'</socialdb_collection_permission_delete_comment>';
                $xml .= '<socialdb_collection_permission_create_tags>'.get_post_meta($collection_id, 'socialdb_collection_permission_create_tags', true).'</socialdb_collection_permission_create_tags>';
                $xml .= '<socialdb_collection_permission_edit_tags>'.get_post_meta($collection_id, 'socialdb_collection_permission_edit_tags', true).'</socialdb_collection_permission_edit_tags>';
                $xml .= '<socialdb_collection_permission_delete_tags>'.get_post_meta($collection_id, 'socialdb_collection_permission_create_category', true).'</socialdb_collection_permission_delete_tags>';
                $xml .= '<socialdb_collection_permission_create_property_term>'.get_post_meta($collection_id, 'socialdb_collection_permission_create_property_term', true).'</socialdb_collection_permission_create_property_term>';
                $xml .= '<socialdb_collection_permission_edit_property_term>'.get_post_meta($collection_id, 'socialdb_collection_permission_edit_property_term', true).'</socialdb_collection_permission_edit_property_term>';
                $xml .= '<socialdb_collection_permission_delete_property_term>'.get_post_meta($collection_id, 'socialdb_collection_permission_delete_property_term', true).'</socialdb_collection_permission_delete_property_term>';
            $xml .= '</permissions>';
            $xml .= '<properties>';
            $xml = $this->generate_properties_xml($properties_id, $xml);
            $xml .= '</properties>';
            $xml .= '<facets>';
            $xml = $this->generate_facets_xml($collection_id, $xml);
            $xml .= '</facets>';
            $xml .= '<channels>';
            $xml = $this->generate_channels_xml($collection_id, $xml);
            $xml = $this->generate_youtube_xml($collection_id, $xml);
            $xml = $this->generate_instagram_xml($collection_id, $xml);
            $xml = $this->generate_flickr_xml($collection_id, $xml);
            $xml .= '</channels>';
        $xml .= '</collection>';
        return $xml;
    }
    
    
      /**
     * @signature - generate_facets_xml($property_id,$xml)
     * @param int collection_id
     * @param string $xml 
     * @return 
     * @description - 
     * @author: Eduardo 
     */
      public function generate_facets_xml($collection_id,$xml) {
        $default_tree_orientation = get_post_meta($collection_id, 'socialdb_collection_facet_widget_tree_orientation', true);
        $facets_id = array_filter(array_unique(get_post_meta($collection_id, 'socialdb_collection_facets')));
        foreach ($facets_id as $facet_id) {
            $facet['id'] = $facet_id;
            $facet['widget'] = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_widget', true);
            if ($facet['id'] == 'tag') {
                $facet['nome'] = 'Tag';
                $facet['widget'] = 'tree';
                $facet['orientation'] = $default_tree_orientation;
            }else{
                $property = get_term_by('id', $facet['id'], 'socialdb_property_type');
                if ($facet['widget'] == 'tree') {
                    $facet['orientation'] = $default_tree_orientation;
                    $facet['nome'] = $property->name;
                    $property = get_term_by('id', $facet['id'], 'socialdb_category_type');
                    if($property){
                         $facet['nome'] = $property->name;
                    }
                } else {
                    $facet['orientation'] = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet['id'] . '_orientation', true);
                    if ($property) {
                        $facet['nome'] = $property->name;
                    } else {
                        $property = get_term_by('id', $facet['id'], 'socialdb_category_type');
                        $facet['nome'] = $property->name;
                    }
                }
            }
            $facet['priority'] = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_priority', true);
            $xml .= '<facet>';
            $xml .= '<id>'.$facet['id'].'</id>';
            $xml .= '<name>'.$facet['nome'].'</name>';
            $xml .= '<orientation>'.$facet['orientation'].'</orientation>';
            $xml .= '<priority>'.$facet['priority'].'</priority>';
            $xml .= '<widget>'.$facet['widget'].'</widget>';
            $xml .= '<color>'.get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_color', true).'</color>';
            $xml .= '<range_option>'.get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_range_options', true).'</range_option>';
            $xml .= '</facet>';
        }        
        return $xml;
      }
      /**
     * @signature - generate_channels_oaipmh_xml($collection_id,$xml)
     * @param int collection_id
     * @param string $xml 
     * @return 
     * @description - 
     * @author: Eduardo 
     */
      public function generate_channels_xml($collection_id,$xml) {
        $channels = get_post_meta($collection_id, 'socialdb_collection_channel');
        if (is_array($channels)) {
            foreach ($channels as $ch) {
                $ch = get_post($ch);
                $oai_pmhdc = wp_get_object_terms($ch->ID, 'socialdb_channel_type');
                if (!empty($ch) && !empty($oai_pmhdc) && isset($oai_pmhdc[0]->name) && $oai_pmhdc[0]->name == 'socialdb_channel_oaipmhdc') {
                    $xml .= '<channel>';
                         $xml .= '<post_id>'.$ch->ID.'</post_id>';
                         $xml .= '<post_title>'.$ch->post_title.'</post_title>';
                         $xml .= '<type>socialdb_channel_oaipmhdc</type>';
                         $xml .= '<socialdb_channel_oaipmhdc_last_update>'.get_post_meta($ch->ID, 'socialdb_channel_oaipmhdc_last_update', true).'</socialdb_channel_oaipmhdc_last_update>';
                         $xml .= '<socialdb_channel_oaipmhdc_first_token>'.get_post_meta($ch->ID, 'socialdb_channel_oaipmhdc_first_token', true).'</socialdb_channel_oaipmhdc_first_token>';
                         $xml .= '<socialdb_channel_oaipmhdc_initial_size>'.get_post_meta($ch->ID, 'socialdb_channel_oaipmhdc_initial_size', true).'</socialdb_channel_oaipmhdc_initial_size>';
                         $xml .= '<socialdb_channel_oaipmhdc_sets>'.get_post_meta($ch->ID, 'socialdb_channel_oaipmhdc_sets', true).'</socialdb_channel_oaipmhdc_sets>';
                         $xml .= '<socialdb_channel_oaipmhdc_is_harvesting>'.get_post_meta($ch->ID, 'socialdb_channel_oaipmhdc_is_harvesting', true).'</socialdb_channel_oaipmhdc_is_harvesting>';
                         $xml .= '<socialdb_channel_oaipmhdc_mapping>'.get_post_meta($ch->ID, 'socialdb_channel_oaipmhdc_mapping', true).'</socialdb_channel_oaipmhdc_mapping>';
                         $xml .= '<socialdb_channel_oaipmhdc_import_object>'.get_post_meta($ch->ID, 'socialdb_channel_oaipmhdc_import_object', true).'</socialdb_channel_oaipmhdc_import_object>';
                   $xml .= '</channel>';
                }else if (!empty($ch) && !empty($oai_pmhdc) && isset($oai_pmhdc[0]->name) && $oai_pmhdc[0]->name == 'socialdb_channel_csv') {
                    $xml .= '<channel>';
                         $xml .= '<post_id>'.$ch->ID.'</post_id>';
                         $xml .= '<post_title>'.$ch->post_title.'</post_title>';
                         $xml .= '<type>socialdb_channel_csv</type>';
                         $xml .= '<socialdb_channel_csv_delimiter>'.get_post_meta($ch->ID, 'socialdb_channel_csv_delimiter', true).'</socialdb_channel_csv_delimiter>';
                         $xml .= '<socialdb_channel_csv_has_header>'.get_post_meta($ch->ID, 'socialdb_channel_csv_has_header', true).'</socialdb_channel_csv_has_header>';
                         $xml .= '<socialdb_channel_csv_mapping>'.get_post_meta($ch->ID, 'socialdb_channel_csv_mapping', true).'</socialdb_channel_csv_mapping>';
                   $xml .= '</channel>';
                }
                
            }
        } 
        return $xml;
      }
      /**
     * @signature - generate_youtube_xml($collection_id,$xml)
     * @param int collection_id
     * @param string $xml 
     * @return 
     * @description - 
     * @author: Eduardo 
     */
      public function generate_youtube_xml($collection_id,$xml) {
           //array de configuração dos parâmetros de get_posts()
            $args = array(
                'meta_key' => 'socialdb_channel_identificator',
                'meta_value' => $collection_id,
                'post_type' => 'socialdb_channel',
                'post_status' => 'publish',
                'suppress_filters' => true
            );
            $results = get_posts($args);
            if (is_array($results)) {
                $json = [];
                foreach ($results as $ch) {
                    if (!empty($ch)) {
                        $xml .= '<channel>';
                         $xml .= '<post_id>'.$ch->ID.'</post_id>';
                         $xml .= '<post_title>'.$ch->post_title.'</post_title>';
                         $xml .= '<type>socialdb_channel_youtube</type>';
                         $xml .= '<socialdb_channel_identificator>'.get_post_meta($ch->ID, 'socialdb_channel_identificator', true).'</socialdb_channel_identificator>';
                         $xml .= '<socialdb_channel_youtube_last_update>'.get_post_meta($ch->ID, 'socialdb_channel_youtube_last_update', true).'</socialdb_channel_youtube_last_update>';
                         $xml .= '<socialdb_channel_youtube_earlier_update>'.get_post_meta($ch->ID, 'socialdb_channel_youtube_earlier_update', true).'</socialdb_channel_youtube_earlier_update>';
                         $xml .= '<socialdb_channel_youtube_import_status>'.get_post_meta($ch->ID, 'socialdb_channel_youtube_import_status', true).'</socialdb_channel_youtube_import_status>';
                         $xml .= '<socialdb_channel_playlist_identificator>'.get_post_meta($ch->ID, 'socialdb_channel_playlist_identificator', true).'</socialdb_channel_playlist_identificator>';
                       $xml .= '</channel>';
                    }
                }
            }
            return $xml;
      }
      /**
     * @signature - generate_instagram_xml($collection_id,$xml)
     * @param int collection_id
     * @param string $xml 
     * @return 
     * @description - 
     * @author: Eduardo 
     */
      public function generate_instagram_xml($collection_id,$xml) {
           //array de configuração dos parâmetros de get_posts()
            $args = array(
                'meta_key' => 'socialdb_instagram_identificator',
                'meta_value' => $collection_id,
                'post_type' => 'socialdb_channel',
                'post_status' => 'publish',
                'suppress_filters' => true
            );
            $results = get_posts($args);
            if (is_array($results)) {
                foreach ($results as $ch) {
                    if (!empty($ch)) {
                        $xml .= '<channel>';
                         $xml .= '<post_id>'.$ch->ID.'</post_id>';
                         $xml .= '<post_title>'.$ch->post_title.'</post_title>';
                         $xml .= '<type>socialdb_channel_instagram</type>';
                         $xml .= '<socialdb_instagram_identificator>'.get_post_meta($ch->ID, 'socialdb_instagram_identificator', true).'</socialdb_instagram_identificator>';
                         $xml .= '<socialdb_instagram_identificator_last_update>'.get_post_meta($ch->ID, 'socialdb_instagram_identificator_last_update', true).'</socialdb_instagram_identificator_last_update>';
                         $xml .= '<socialdb_instagram_import_status>'.get_post_meta($ch->ID, 'socialdb_instagram_import_status', true).'</socialdb_instagram_import_status>';
                        $xml .= '</channel>';
                    }
                }
            }
            return $xml;
      }
    /**
     * @signature - generate_flickr_xml($collection_id,$xml)
     * @param int collection_id
     * @param string $xml 
     * @return 
     * @description - 
     * @author: Eduardo 
     */
      public function generate_flickr_xml($collection_id,$xml) {
           //array de configuração dos parâmetros de get_posts()
            $args = array(
                'meta_key' => 'socialdb_flickr_identificator',
                'meta_value' => $collection_id,
                'post_type' => 'socialdb_channel',
                'post_status' => 'publish',
                'suppress_filters' => true
            );
            $results = get_posts($args);
            if (is_array($results)) {
                foreach ($results as $ch) {
                    if (!empty($ch)) {
                        $xml .= '<channel>';
                         $xml .= '<post_id>'.$ch->ID.'</post_id>';
                         $xml .= '<post_title>'.$ch->post_title.'</post_title>';
                         $xml .= '<type>socialdb_channel_flickr</type>';
                         $xml .= '<socialdb_filckr_identificator>'.get_post_meta($ch->ID, 'socialdb_filckr_identificator', true).'</socialdb_filckr_identificator>';
                         $xml .= '<socialdb_filckr_identificator_last_update>'.get_post_meta($ch->ID, 'socialdb_filckr_identificator_last_update', true).'</socialdb_filckr_identificator_last_update>';
                         $xml .= '<socialdb_filckr_import_status>'.get_post_meta($ch->ID, 'socialdb_filckr_import_status', true).'</socialdb_filckr_import_status>';
                        $xml .= '</channel>';
                    }
                }
            }
            return $xml;
      }
     /**
     * @signature - export_items($collection_id)
     * @param int $collection_id
      * * @param string (Optional) O diretorio aonde sera criado
     * @return 
     * @description - 
     * @author: Eduardo 
     */  
     public function export_items($collection_id,$dir = '') {
        if($dir==''){
            $dir = dirname(__FILE__);
        } 
        $items = $this->get_collection_posts($collection_id);
        if($items && is_array($items)){
            foreach ($items as $index => $item) {
            	mkdir($dir.'/package/items/'.$index);

	            $this->generate_item_xml($item, $index, $dir);
                $this->export_item_thumbnail($item->ID, $index,$dir);
                $this->export_files($item->ID, $index,$dir);
                $this->export_content($item->ID, $index,$dir);

	            clean_post_cache($item->ID);
            }
        }
     }
    /**
     * 
     * @param type $item
     * @param type $index
     * @param type $dir o diretorio onde sera gerado o arquivo
     */ 
    public function generate_item_xml($item,$index,$dir = '') {
        if($dir==''){
            $dir = dirname(__FILE__);
        }
        $user_owner = get_user_by('id', $item->post_author);
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<item>';
        $xml .= "<post_title>{$item->post_title}</post_title>";
        $xml .= "<post_content>".htmlspecialchars($item->post_content)."</post_content>";
        $xml .= "<post_date>{$item->post_date}</post_date>";
        $xml .= "<post_author>{$user_owner->data->user_email}</post_author>";
        $xml .= "<socialdb_object_from>".get_post_meta( $item->ID, 'socialdb_object_from', TRUE)."</socialdb_object_from>";
        $xml .= "<socialdb_object_dc_source>".htmlspecialchars(get_post_meta( $item->ID, 'socialdb_object_dc_source', TRUE))."</socialdb_object_dc_source>";
        $xml .= "<socialdb_object_content>". htmlspecialchars(get_post_meta( $item->ID, 'socialdb_object_content', TRUE))."</socialdb_object_content>";
        $xml .= "<socialdb_object_dc_type>".get_post_meta( $item->ID, 'socialdb_object_dc_type', TRUE)."</socialdb_object_dc_type>";
        //categories
        $xml .= '<categories>';
        $xml .= implode('|', $this->generate_categories_item($item->ID));
        $xml .= '</categories>';
        //tags
        $xml .= '<tags>';
        $xml .= implode('|', $this->generate_tags_item($item->ID));
        $xml .= '</tags>';
        $xml .= '<properties>';
        $xml = $this->generate_properties_item($item->ID, $xml);
        $xml .= '</properties>';
        $xml .= '</item>';
        $this->create_xml_file($dir.'/package/items/'.$index.'/item.xml', $xml);

	    clean_post_cache($item->ID);
    }
     /**
     * @signature - generate_categories_item($item_id)
     * @param int collection_id
     * @return 
     * @description - 
     * @author: Eduardo 
     */  
     public function generate_categories_item($item_id) {
         $categories_id = [];
        $categories = wp_get_object_terms($item_id, 'socialdb_category_type');
        if (is_array($categories) && !empty($categories)) {
            foreach ($categories as $category) {
                $categories_id[] = $category->term_id;
            }
        }
        return $categories_id;
     }
      /**
     * @signature - generate_tags_item($item_id)
     * @param int collection_id
     * @return 
     * @description - 
     * @author: Eduardo 
     */  
     public function generate_tags_item($item_id) {
         $tags_name = array();
        $tags = wp_get_object_terms($item_id, 'socialdb_tag_type');
        if (is_array($tags) && !empty($tags)) {
            foreach ($tags as $tag) {
                    $tags_name[] = $tag->name;
            }
        }
        return $tags_name;
     }
     /**
      * 
      */
     public function generate_properties_item($item_id,$xml) {
         $properties = $this->get_properties_object($item_id);
         if(!empty($properties)){
             foreach ($properties as $id => $values) {
                if(strpos($id, "_")!==false){
                    $values =  explode(',', htmlspecialchars(implode('|', $values)));
                    $explode = explode('_', $id);
                    $id_compound = $explode[0];
                    $count = $explode[1];
                    $xml .= '<property>';
                    $xml .= '<id>'.$id_compound.'</id>';
                    $xml .= '<count>'.$count.'</count>';
                    
                    foreach ($values as $value) {
                        $xml .= '<compound>';
                        if(strpos($value, "_cat")!==false){
                            $xml .= '<cat>'.str_replace('_cat', "",$value).'</cat>';
                        }else{
                            $meta = $this->sdb_get_post_meta($value);
                            $xml .= '<property_id>'.  str_replace('socialdb_property_', '', $meta->meta_key).'</property_id>';
                            $xml .= '<value>'.$meta->meta_value.'</value>';
                        }
                        $xml .= '</compound>';
                    }
                    $xml .= '</property>';
                 }else{
                    $var_data = $this->get_all_property($id, true);
                    if(!isset($var_data['metas']['socialdb_property_is_compounds'])
                            ||!$var_data['metas']['socialdb_property_is_compounds']){ 
                        $xml .= '<property>';
                        $xml .= '<id>'.$id.'</id>';
                        $xml .= '<value>'.  htmlspecialchars(implode('|', $values)).'</value>';
                        $xml .= '</property>';
                    }
                 }
                 
             }
         }
         return $xml;
     }
     /**
      * 
      */
     public function export_files($item_id,$index,$dir = '') {
        if($dir == ''){
             $dir = dirname(__FILE__);
         } 
        $post = get_post($item_id);
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
                if(!file_exists (dirname(__FILE__).'/package/items/'.$index.'/files')){
                    mkdir(dirname(__FILE__).'/package/items/'.$index.'/files');
                }
                foreach ($attachments as $attachment) {
                    if (in_array($attachment->ID, $arquivos)) {
                        $object_content = get_post_meta($item_id,'socialdb_object_content',true);
                        if($object_content!=$attachment->ID){
                            $fullsize_path = get_attached_file($attachment->ID); // Full path
                            $filename_only = basename($fullsize_path); // Just the file name
                            $ext = pathinfo($fullsize_path, PATHINFO_EXTENSION);
                            copy($fullsize_path, dirname(__FILE__).'/package/items/'.$index.'/files/'.$filename_only.'.'.$ext);
                        }
                    }

	                clean_post_cache($item_id);
                }
            }

	        clean_post_cache($post->ID);
        }

        clean_post_cache($item_id);
     }
     /**
      * 
      */
     public function export_item_thumbnail($item_id,$index,$dir = '') {
         if($dir == ''){
             $dir = dirname(__FILE__);
         }
         $thumbnail_id = get_post_thumbnail_id($item_id);
        if($thumbnail_id){
          $fullsize_path = get_attached_file( $thumbnail_id ); // Full path
          $ext = pathinfo($fullsize_path, PATHINFO_EXTENSION);
          $filename_only = basename($fullsize_path); // Just the file name
          copy($fullsize_path,$dir.'/package/items/'.$index.'/thumbnail.'.$ext);
        }

	     clean_post_cache($item_id);
     }
     /**
      * 
      */
     public function export_content($item_id,$index,$dir = '') {
        if($dir == ''){
             $dir = dirname(__FILE__);
         } 
        $object_content = get_post_meta($item_id,'socialdb_object_content',true);
        $fullsize_path = get_attached_file($object_content);
        if($fullsize_path){
             mkdir(dirname(__FILE__).'/package/items/'.$index.'/content');
             $ext = pathinfo($fullsize_path, PATHINFO_EXTENSION);
             $filename_only = basename($fullsize_path); // Just the file name
             copy($fullsize_path, dirname(__FILE__).'/package/items/'.$index.'/content/'.$filename_only.'.'.$ext);
        }

        clean_post_cache($object_content);
	    clean_post_cache($item_id);
     }
    
}
