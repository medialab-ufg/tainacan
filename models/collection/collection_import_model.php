<?php

ini_set('max_input_vars', '10000');
error_reporting(E_ALL);
require_once(dirname(__FILE__) . '/collection_model.php');

class CollectionImportModel extends CollectionModel {
    /*
     * @signature import($data)
     * @param array $data Os parametros e o file vindo do formulario
     * @return json A url da colecao criada para redirecionamento
     */
    public function import($data) {
        $return = [];
        session_write_close();
        ini_set('max_execution_time', '0');
        $return['result'] = false;
        $dir_created = $this->unzip_package();
        if(is_dir($dir_created.'/package/taxonomies' )){
           $this->import_xml_taxonomies($dir_created.'/package/taxonomies' );
        }
        
        if(is_dir($dir_created.'/package/metadata' )&&  is_file($dir_created.'/package/metadata/administrative_settings.xml')){
            $xml = simplexml_load_file($dir_created.'/package/metadata/administrative_settings.xml');
            $collection_id = $this->import_collection($xml,$dir_created);
            //thumbnail
            $this->add_thumbnail_collection($dir_created,$collection_id);
            //capa da colecao
            $this->add_cover_collection($dir_created,$collection_id);
            $return['result'] = true;
            $return['url'] = get_the_permalink($collection_id);
        }
        
        $this->recursiveRemoveDirectory($dir_created);
        return $return;
    }

    /*
     * @signature unzip_package()
     * @return string $targetdir o diretorio para onde foi descompactado o arquivo
     */
    public function unzip_package(){
        if ($_FILES["collection_file"]["name"]) {
            $file = $_FILES["collection_file"];
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
            $targetdir = dirname(__FILE__)."/" . $ran;
            $targetzip = dirname(__FILE__)."/" . $ran . ".zip";

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
    /**
     * @signature add_thumbnail_collection($dir_created,$collection_id)
     * @param string $dir_created
     * @param string $collection_id
     */
    public function add_thumbnail_collection($dir_created,$collection_id) {
        if(is_file($dir_created.'/package/metadata/thumbnail.png')){
            copy($dir_created.'/package/metadata/thumbnail.png', $dir_created.'/package/metadata/thumbnail2.png');
            $thumbnail_id = $this->insert_attachment_file($dir_created.'/package/metadata/thumbnail2.png', $collection_id);
            set_post_thumbnail($collection_id, $thumbnail_id);
        }elseif(is_file($dir_created.'/package/metadata/thumbnail.jpg')){
            copy($dir_created.'/package/metadata/thumbnail.jpg', $dir_created.'/package/metadata/thumbnail2.jpg');
            $thumbnail_id = $this->insert_attachment_file($dir_created.'/package/metadata/thumbnail2.jpg', $collection_id);
             set_post_thumbnail($collection_id, $thumbnail_id);
        }elseif(is_file($dir_created.'/package/metadata/thumbnail.gif')){
            copy($dir_created.'/package/metadata/thumbnail.gif', $dir_created.'/package/metadata/thumbnail2.gif');
            $thumbnail_id = $this->insert_attachment_file($dir_created.'/package/metadata/thumbnail2.gif', $collection_id);
            set_post_thumbnail($collection_id, $thumbnail_id);
        }elseif(is_file($dir_created.'/package/metadata/thumbnail.jpeg')){
            copy($dir_created.'/package/metadata/thumbnail.jpeg', $dir_created.'/package/metadata/thumbnail2.jpeg');
            $thumbnail_id = $this->insert_attachment_file($dir_created.'/package/metadata/thumbnail2.jpeg', $collection_id);
            set_post_thumbnail($collection_id, $thumbnail_id);
        }
    }

    /**
     * @signature add_cover_collection($dir_created,$collection_id)
     * @param string $dir_created
     * @param string $collection_id
     */
    public function add_cover_collection($dir_created,$collection_id) {
        if(is_file($dir_created.'/package/metadata/cover.png')){
            copy($dir_created.'/package/metadata/cover.png', $dir_created.'/package/metadata/cover2.png');
            $cover_id = $this->insert_attachment_file($dir_created.'/package/metadata/cover2.png', $collection_id);
            update_post_meta($collection_id, 'socialdb_collection_cover_id', $cover_id);
        }elseif(is_file($dir_created.'/package/metadata/cover.jpg')){
             copy($dir_created.'/package/metadata/cover.jpg', $dir_created.'/package/metadata/cover2.jpg');
            $cover_id = $this->insert_attachment_file($dir_created.'/package/metadata/cover2.jpg', $collection_id);
              update_post_meta($collection_id, 'socialdb_collection_cover_id', $cover_id);
        }elseif(is_file($dir_created.'/package/metadata/cover.gif')){
             copy($dir_created.'/package/metadata/cover.gif', $dir_created.'/package/metadata/cover2.gif');
            $cover_id = $this->insert_attachment_file($dir_created.'/package/metadata/cover2.gif', $collection_id);
            update_post_meta($collection_id, 'socialdb_collection_cover_id', $cover_id);
        }elseif(is_file($dir_created.'/package/metadata/cover.jpeg')){
             copy($dir_created.'/package/metadata/cover.jpeg', $dir_created.'/package/metadata/cover2.jpeg');
            $cover_id = $this->insert_attachment_file($dir_created.'/package/metadata/cover2.jpeg', $collection_id);
             update_post_meta($collection_id, 'socialdb_collection_cover_id', $cover_id);
        }
    }
    ############################# IMPORTAR TAXONOMIAS ############################################
   /*
     * @signature import_xml_taxonomies($data)
     * @param string $dir
     */
   public function import_xml_taxonomies($dir) {
       //$categories_id = [];
        foreach (new DirectoryIterator($dir) as $fileInfo) {
                if($fileInfo->isDot()) 
                    continue;
                $xml = simplexml_load_file($fileInfo->getPath().'/'.$fileInfo->getFilename());
                $name = (has_filter('alter_category_root_repository_name')) ? apply_filters('alter_category_root_repository_name','') : 'socialdb_category';
                $data = $this->add_hierarchy_importing_collection($xml, 0, get_term_by('name', $name, 'socialdb_category_type')->term_id);
               //$categories_id[] = $data['ids'];
        }
        //return $categories_id;
   }
   
   /** function add_hierarchy_importing_collection($xml,$collection_id,$parent = 0) 
     * @param 
     * @param array 
     * @return array 
     * @author: Eduardo */
    public function add_hierarchy_importing_collection($xml, $collection_id, $parent = 0, &$all_ids = []) {
        if ($xml) {
            $attributes = $xml->attributes();
            if (isset($attributes['label']) && !empty($attributes['label'])) {
                $array = wp_insert_term(trim($attributes['label']), 'socialdb_category_type', array('parent' => $parent,
                    'slug' => $this->generate_slug(trim($attributes['label']), 0)));
                add_term_meta($array['term_id'], 'socialdb_imported_id', (string) $attributes['id']);
                add_term_meta($array['term_id'], 'socialdb_category_owner', get_current_user_id());
                $this->insert_properties($xml, $array['term_id'], false);
                //if($parent == $this->get_category_root()){
                //$this->add_facet($array['term_id'], $collection_id);
                //}
                $parent = $array['term_id'];
                //if(!in_array($array['term_id'], $all_ids)){
                //  $all_ids[] = $array['term_id'];
                //}
            }
            $has_children = $xml->count();
            if ($xml->isComposedBy->node) {
                foreach ($xml->isComposedBy->node as $value) {
                    $this->add_hierarchy_importing_collection($value, $collection_id, $parent, $all_ids);
                }
            }
            $data['title'] = __('Success', 'tainacan');
            $data['msg'] = __('All categories imported successfully', 'tainacan');
            $data['type'] = 'success';
            $data['ids'] = $all_ids;
        } else {
            $data = array();
            $data['title'] = __('Error', 'tainacan');
            $data['msg'] = __('Xml incompatible', 'tainacan');
            $data['type'] = 'error';
            $data['ids'] = $all_ids;
        }
        return $data;
    }
   ############################### IMPORTAR DADOS ADMINISTRATIVOS DA COLECAO #####################
   /*
     * @signature import_xml_taxonomies($data)
     * @param string $xml
     * @param string $dir_created
     */
   public function import_collection($xml,$dir_created) {
       $collection = array(
            'post_type' => 'socialdb_collection',
            'post_title' => (string) $xml->post_title,
            'post_status' => 'publish',
            'post_name' => (string) $xml->post_name,
            'post_content' => (string) $xml->post_content,
            'post_author' => get_current_user_id(),
        );
       $collection_id = wp_insert_post($collection);
       //
       $this->createSocialMappingDefault($collection_id);
       //categoria raiz da colecao
       $socialdb_collection_object_type = $this->get_term_imported_id((string) $xml->socialdb_collection_object_type);//init
      //privacidade
        $type = get_term_by('name', (string) $xml->parent, 'socialdb_collection_type');
        wp_set_post_terms($collection_id, array($type->term_id), 'socialdb_collection_type');
       //metas
       update_post_meta($collection_id, 'socialdb_collection_object_type', $socialdb_collection_object_type); 
       update_post_meta($collection_id, 'socialdb_collection_hide_tags', (string) $xml->socialdb_collection_hide_tags);
       update_post_meta($collection_id, 'socialdb_collection_attachment', (string) $xml->socialdb_collection_attachment);
       update_post_meta($collection_id, 'socialdb_collection_allow_hierarchy', (string) $xml->socialdb_collection_allow_hierarchy);
       update_post_meta($collection_id, 'socialdb_collection_ordenation_form', (string) $xml->socialdb_collection_ordenation_form);
       update_post_meta($collection_id, 'socialdb_collection_facet_widget_tree_orientation', (string) $xml->socialdb_collection_facet_widget_tree_orientation);
       update_post_meta($collection_id, 'socialdb_collection_board_background_color', (string) $xml->socialdb_collection_board_background_color);
       update_post_meta($collection_id, 'socialdb_collection_board_border_color', (string) $xml->socialdb_collection_board_border_color);
       update_post_meta($collection_id, 'socialdb_collection_board_font_color', (string) $xml->socialdb_collection_board_font_color);
       update_post_meta($collection_id, 'socialdb_collection_board_link_color', (string) $xml->socialdb_collection_board_link_color);
       update_post_meta($collection_id, 'socialdb_collection_board_skin_mode', (string) $xml->socialdb_collection_board_skin_mode);
       update_post_meta($collection_id, 'socialdb_collection_hide_title', (string) $xml->socialdb_collection_hide_title);
       update_post_meta($collection_id, 'socialdb_collection_hide_description', (string) $xml->socialdb_collection_hide_description);
       update_post_meta($collection_id, 'socialdb_collection_hide_thumbnail', (string) $xml->socialdb_collection_hide_thumbnail);
       update_post_meta($collection_id, 'socialdb_collection_hide_menu', (string) $xml->socialdb_collection_hide_menu);
       update_post_meta($collection_id, 'socialdb_collection_hide_categories', (string) $xml->socialdb_collection_hide_categories);
       update_post_meta($collection_id, 'socialdb_collection_hide_rankings', (string) $xml->socialdb_collection_hide_rankings);
       update_post_meta($collection_id, 'socialdb_collection_columns', (string) $xml->socialdb_collection_columns);
       update_post_meta($collection_id, 'socialdb_collection_size_thumbnail', (string) $xml->socialdb_collection_size_thumbnail);
       update_post_meta($collection_id, 'socialdb_collection_submission_visualization', (string) $xml->socialdb_collection_submission_visualization);
       //permissions
       update_post_meta($collection_id, 'socialdb_collection_permission_create_category', (string) $xml->permissions->socialdb_collection_permission_create_category);
       update_post_meta($collection_id, 'socialdb_collection_permission_edit_category', (string) $xml->permissions->socialdb_collection_permission_edit_category);
       update_post_meta($collection_id, 'socialdb_collection_permission_delete_category', (string) $xml->permissions->socialdb_collection_permission_delete_category);
       update_post_meta($collection_id, 'socialdb_collection_permission_add_classification', (string) $xml->permissions->socialdb_collection_permission_add_classification);
       update_post_meta($collection_id, 'socialdb_collection_permission_delete_classification', (string) $xml->permissions->socialdb_collection_permission_delete_classification);
       update_post_meta($collection_id, 'socialdb_collection_permission_create_object', (string) $xml->permissions->socialdb_collection_permission_create_object);
       update_post_meta($collection_id, 'socialdb_collection_permission_delete_object', (string) $xml->permissions->socialdb_collection_permission_delete_object);
       update_post_meta($collection_id, 'socialdb_collection_permission_create_property_data', (string) $xml->permissions->socialdb_collection_permission_create_property_data);
       update_post_meta($collection_id, 'socialdb_collection_permission_edit_property_data', (string) $xml->permissions->socialdb_collection_permission_edit_property_data);
       update_post_meta($collection_id, 'socialdb_collection_permission_delete_property_data', (string) $xml->permissions->socialdb_collection_permission_delete_property_data);
       update_post_meta($collection_id, 'socialdb_collection_permission_edit_property_data_value', (string) $xml->permissions->socialdb_collection_permission_edit_property_data_value);
       update_post_meta($collection_id, 'socialdb_collection_permission_create_property_object', (string) $xml->permissions->socialdb_collection_permission_create_property_object);
       update_post_meta($collection_id, 'socialdb_collection_permission_edit_property_object', (string) $xml->permissions->socialdb_collection_permission_edit_property_object);
       update_post_meta($collection_id, 'socialdb_collection_permission_delete_property_object', (string) $xml->permissions->socialdb_collection_permission_delete_property_object);
       update_post_meta($collection_id, 'socialdb_collection_permission_edit_property_object_value', (string) $xml->permissions->socialdb_collection_permission_edit_property_object_value);
       update_post_meta($collection_id, 'socialdb_collection_permission_create_comment', (string) $xml->permissions->socialdb_collection_permission_create_comment);
       update_post_meta($collection_id, 'socialdb_collection_permission_edit_comment', (string) $xml->permissions->socialdb_collection_permission_edit_comment);
       update_post_meta($collection_id, 'socialdb_collection_permission_delete_comment', (string) $xml->permissions->socialdb_collection_permission_delete_comment);
       update_post_meta($collection_id, 'socialdb_collection_permission_create_tags', (string) $xml->permissions->socialdb_collection_permission_delete_comment);
       update_post_meta($collection_id, 'socialdb_collection_permission_edit_tags', (string) $xml->permissions->socialdb_collection_permission_edit_tags);
       update_post_meta($collection_id, 'socialdb_collection_permission_delete_tags', (string) $xml->permissions->socialdb_collection_permission_delete_tags);
       update_post_meta($collection_id, 'socialdb_collection_permission_create_property_term', (string) $xml->permissions->socialdb_collection_permission_create_property_term);
       update_post_meta($collection_id, 'socialdb_collection_permission_edit_property_term', (string) $xml->permissions->socialdb_collection_permission_edit_property_term);
       update_post_meta($collection_id, 'socialdb_collection_permission_delete_property_term', (string) $xml->permissions->socialdb_collection_permission_delete_property_term);
       //tab defaullt
       if($xml->socialdb_collection_default_tab)
            update_post_meta($collection_id, 'socialdb_collection_default_tab', (string) $xml->socialdb_collection_default_tab);
       if($xml->socialdb_collection_update_tab_organization)
            update_post_meta($collection_id, 'socialdb_collection_update_tab_organization', (string) $xml->socialdb_collection_update_tab_organization);
       // properties
       $properties = $this->insert_properties($xml, $socialdb_collection_object_type, $collection_id);
       // tabs
       $tabs = $this->insertTabs($xml, $collection_id);
       $this->updateTabOrganization($collection_id,$tabs);
       //facets
       $this->add_facets($xml, $collection_id);
       //channels
       $this->add_channels($xml, $collection_id);
       //ordenation
       $socialdb_collection_default_ordering = $this->get_term_imported_id((string) $xml->socialdb_collection_default_ordering);//after all
       update_post_meta($collection_id, 'socialdb_collection_default_ordering', $socialdb_collection_default_ordering);
       $socialdb_collection_mapping_exportation_active = $this->get_post_imported_id((string) $xml->socialdb_collection_mapping_exportation_active);//after all
       update_post_meta($collection_id, 'socialdb_collection_mapping_exportation_active', $socialdb_collection_mapping_exportation_active);
       //items            
        if(is_dir($dir_created.'/package/items' )){
              session_write_close();
             ini_set('max_execution_time', '0');
            $this->import_items($dir_created.'/package/items',$collection_id);
        }

       //retiro o id transitorio
       if($properties){
           foreach ($properties as $property) {
               delete_term_meta($property, 'socialdb_imported_id');
           } 
       }
       return $collection_id;
   }
   
   /**
    * 
    * @param type $xml O xml completo
    * @param type $category_root_id O id da categoria raiz
    * @return array Os ids das propriedades 
    */
   public function insert_properties($xml,$category_root_id,$collection_id = 0){
       $properties_id = [];
       if(isset($xml->properties->property)){
           foreach ($xml->properties->property as $property) {
               if(isset($property->socialdb_property_term_root)){
                   $property_id = $this->add_property_term($property,$property->socialdb_property_created_category,$category_root_id);
                    add_term_meta($category_root_id, 'socialdb_category_property_id',$property_id);
                    $properties_id[] = $property_id;
               }elseif(isset($property->socialdb_property_data_widget)){
                   $property_id = $this->add_property_data($property,$property->socialdb_property_created_category,$category_root_id);
                    add_term_meta($category_root_id, 'socialdb_category_property_id',$property_id);
                    $properties_id[] = $property_id;
               }elseif(isset($property->socialdb_property_object_category_id)){
                   $property_id = $this->add_property_object($property,$property->socialdb_property_created_category,$category_root_id);
                    add_term_meta($category_root_id, 'socialdb_category_property_id',$property_id);
                    $properties_id[] = $property_id;
               }elseif(isset($property->ranking_type)){
                   $property_id = $this->add_property_ranking($property,$property->socialdb_property_created_category,$category_root_id);
                   add_term_meta($category_root_id, 'socialdb_category_property_id',$property_id);
                   $properties_id[] = $property_id;
               }
               
               if($collection_id && $property->id)
                    $this->updatePropertiesTabId($collection_id,(string) $property->id,$property_id);
           }
           foreach ($xml->properties->property as $property) {
               if(isset($property->socialdb_property_compounds_properties_id)){
                   $property_id = $this->add_property_compounds($property,$property->socialdb_property_created_category,$category_root_id);
                    add_term_meta($category_root_id, 'socialdb_category_property_id',$property_id);
                    $properties_id[] = $property_id;
               }
               if($collection_id && $property->id)
                    $this->updatePropertiesTabId($collection_id,(string) $property->id,$property_id);
           }
           
       }
       return $properties_id;
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
    /**
     * function add_property_term($property)
     * @param object $property
     * @return int O id da da propriedade criada.
     * @author: Eduardo Humberto 
     */
   public function add_property_term($property,$socialdb_collection_object_type,$category_root_id) {
        $new_property = wp_insert_term((string)$property->name, 'socialdb_property_type', array('parent' => $this->get_property_type_id('socialdb_property_term'),
                'slug' => $this->generate_slug((string)$property->name, 0)));
        if(is_wp_error($new_property)){
            $new_property = wp_insert_term(__('Imported Property','tainacan'), 'socialdb_property_type', array('parent' => $this->get_property_type_id('socialdb_property_term'),
                'slug' => $this->generate_slug(__('Imported Property','tainacan'), 0)));
        }
        update_term_meta($new_property['term_id'], 'socialdb_property_required', (string)$property->property_term_required);
        update_term_meta($new_property['term_id'], 'socialdb_property_term_cardinality', (string) $property->socialdb_property_term_cardinality);
        update_term_meta($new_property['term_id'], 'socialdb_property_term_widget',  (string)$property->socialdb_property_term_widget);
        update_term_meta($new_property['term_id'], 'socialdb_property_help',  (string)$property->socialdb_property_help);
        update_term_meta($new_property['term_id'], 'socialdb_property_term_root',$this->get_term_imported_id((string) $property->socialdb_property_term_root));  
        update_term_meta($new_property['term_id'], 'socialdb_property_created_category',$category_root_id);
        update_term_meta($new_property['term_id'], 'socialdb_imported_id',(string)$property->id);
        return $new_property['term_id'];
   }
   /**
     * function add_property_data($property)
     * @param object $property
     * @return int O id da da propriedade criada.
     * @author: Eduardo Humberto 
     */
   public function add_property_data($property,$socialdb_collection_object_type,$category_root_id) {
        $new_property = wp_insert_term((string)$property->name, 'socialdb_property_type', array('parent' => $this->get_property_type_id('socialdb_property_data'),
                'slug' => $this->generate_slug((string)$property->name, 0)));
        //Functional
        if(is_wp_error($new_property)){
            $new_property = wp_insert_term(__('Imported Property','tainacan'), 'socialdb_property_type', array('parent' => $this->get_property_type_id('socialdb_property_data'),
                'slug' => $this->generate_slug(__('Imported Property','tainacan'), 0)));
        }
        update_term_meta($new_property['term_id'], 'socialdb_property_required', (string)$property->property_term_required);
        update_term_meta($new_property['term_id'], 'socialdb_property_data_widget', (string) $property->socialdb_property_data_widget);
        update_term_meta($new_property['term_id'], 'socialdb_property_data_column_ordenation',  (string)$property->socialdb_property_data_column_ordenation);
        update_term_meta($new_property['term_id'], 'socialdb_property_default_value',  (string)$property->socialdb_property_default_value);
        update_term_meta($new_property['term_id'], 'socialdb_property_created_category',$category_root_id);
        update_term_meta($new_property['term_id'], 'socialdb_imported_id',(string)$property->id);
        return $new_property['term_id'];
   }
   /**
     * function add_property_objec($property)
     * @param object $property
     * @return int O id da da propriedade criada.
     * @author: Eduardo Humberto 
     */
   public function add_property_object($property,$socialdb_collection_object_type,$category_root_id) {
        $new_property = wp_insert_term((string)$property->name, 'socialdb_property_type', array('parent' => $this->get_property_type_id('socialdb_property_object'),
                'slug' => $this->generate_slug((string)$property->name, 0)));
        if(is_wp_error($new_property)){
            $new_property = wp_insert_term(__('Imported Property','tainacan'), 'socialdb_property_type', array('parent' => $this->get_property_type_id('socialdb_property_object'),
                'slug' => $this->generate_slug(__('Imported Property','tainacan'), 0)));
        }
        //Functional
        update_term_meta($new_property['term_id'], 'socialdb_property_required', (string)$property->property_term_required);
        update_term_meta($new_property['term_id'], 'socialdb_property_object_category_id', $this->get_term_imported_id((string) $property->socialdb_property_object_category_id));
        update_term_meta($new_property['term_id'], 'socialdb_property_object_is_reverse',  (string)$property->socialdb_property_object_is_reverse);
        update_term_meta($new_property['term_id'], 'socialdb_property_object_reverse',  (string)$property->socialdb_property_object_reverse);
       // update_term_meta($new_property['term_id'], 'socialdb_property_created_category',(string)$socialdb_collection_object_type);
        update_term_meta($new_property['term_id'], 'socialdb_property_created_category',$category_root_id);
        update_term_meta($new_property['term_id'], 'socialdb_imported_id',(string)$property->id);
        return $new_property['term_id'];
   }
   /**
     * function add_property_objec($property)
     * @param object $property
     * @return int O id da da propriedade criada.
     * @author: Eduardo Humberto 
     */
   public function add_property_compounds($property,$socialdb_collection_object_type,$category_root_id) {
        $new_property = wp_insert_term((string)$property->name, 'socialdb_property_type', array('parent' => $this->get_property_type_id('socialdb_property_compounds'),
                'slug' => $this->generate_slug((string)$property->name, 0)));
        update_term_meta($new_property['term_id'], 'socialdb_property_required', (string)$property->socialdb_property_required);
        $old_ids = explode(',', (string)$property->socialdb_property_compounds_properties_id);
        if($old_ids){
            $new_ids = [];
            foreach ($old_ids as $old_id) {
                $meta = array();
                if(is_numeric($old_id)){
                   $new_id = $this->get_term_imported_id($old_id);
                   $new_ids[] = $new_id;
                   $meta[$new_property['term_id']] = 'true';
                   update_term_meta($new_id, 'socialdb_property_is_compounds', serialize($meta));
                }
            }
            update_term_meta($new_property['term_id'], 'socialdb_property_compounds_properties_id',  implode(',', $new_ids));
        }
        update_term_meta($new_property['term_id'], 'socialdb_property_compounds_cardinality',  (string)$property->socialdb_property_compounds_cardinality);
        update_term_meta($new_property['term_id'], 'socialdb_property_help',  (string)$property->socialdb_property_help);
       // update_term_meta($new_property['term_id'], 'socialdb_property_created_category',(string)$socialdb_collection_object_type);
        update_term_meta($new_property['term_id'], 'socialdb_property_created_category',$category_root_id);
        update_term_meta($new_property['term_id'], 'socialdb_imported_id',(string)$property->id);
        return $new_property['term_id'];
   }
   /**
     * function add_property_objec($property)
     * @param object $property
     * @return int O id da da propriedade criada.
     * @author: Eduardo Humberto 
     */
   public function add_property_ranking($property,$socialdb_collection_object_type,$category_root_id) {
        $new_property = wp_insert_term((string)$property->name, 'socialdb_property_type', array('parent' => $this->get_property_type_id((string)$property->ranking_type),
                'slug' => $this->generate_slug((string)$property->name, 0)));
        update_term_meta($new_property['term_id'], 'socialdb_property_required', (string)$property->property_term_required);
        update_term_meta($new_property['term_id'], 'socialdb_property_created_category',$category_root_id);
        update_term_meta($new_property['term_id'], 'socialdb_imported_id',(string)$property->id);
        return $new_property['term_id'];
   }
   
   /**
    * 
    * 
    * @param xml $xml
    * @param int $collection_id O id colecao
    * 
    */
   public function insertTabs($xml,$collection_id) {
        $tabs = [];
        if(isset($xml->tabs->tab)){
            foreach ($xml->tabs->tab as $tab) {
                $new_id = $this->sdb_add_post_meta($collection_id, 'socialdb_collection_tab',(string)$tab->name);
                $tabs[$new_id] = $tab;
            }
        }
        return $tabs;
   }
   
   /**
    * metodo responsavel em atualizar a organizacao das abas importadas
    *
    * @param type $collection_id
    * @param type $properties_id
    * @param type $tabs
    * @return void
    */
   public function updateTabOrganization($collection_id,$tabs) {
        if(empty($tabs))
            return true;
        
        $array = unserialize(get_post_meta($collection_id, 'socialdb_collection_update_tab_organization',true));
        if($array && is_array($array[0])):
            foreach ($tabs as $tab_new_id => $tab) {
                foreach ($array[0] as $property_key => $tab_value) {
                    if($tab_value==(string)$tab->id){
                        $array[0][$property_key] = $tab_new_id;
                    }
                }
            }
        endif;
        update_post_meta($collection_id, 'socialdb_collection_update_tab_organization',  serialize($array));
   }
   
   /**
    * metodo responsavel em atualizar a organizacao das abas importadas
    *
    * @param type $collection_id
    * @param type $properties_id
    * @param type $tabs
    * @return void
    */
   public function updatePropertiesTabId($collection_id,$old_id,$new_id) {
        $array = unserialize(get_post_meta($collection_id, 'socialdb_collection_update_tab_organization',true));
        if($array && is_array($array[0])):
            foreach ($array[0] as $property_key => $tab_value) {
                if($old_id == $property_key){
                    $array[0][$new_id] = $tab_value;
                    unset($array[0][$property_key]);
                }
            }
        endif;
        update_post_meta($collection_id, 'socialdb_collection_update_tab_organization',  serialize($array));
   }
   
   
   
   /**
     * function add_facets($xml,$collection_id)
     * @param object $xml
     * @param int $collection_id
     * @return array O id da propriedades criadas.
     * @author: Eduardo Humberto 
     */
   public function add_facets($xml,$collection_id) {
       if(isset($xml->facets->facet)){
           foreach ($xml->facets->facet as $facet) {
               $facet_id = $this->get_term_imported_id((string) $facet->id);
               if(is_numeric(trim($facet_id))){
                    add_post_meta($collection_id, 'socialdb_collection_facets', (string)$facet_id);
                    update_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_orientation', (string)$facet->orientation);
                    update_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_widget', (string)$facet->widget);
                    update_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_priority', (string)$facet->priority);
                    update_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_color', (string)$facet->color);
                    update_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_range_options', (string)$facet->range_option);
                    
               }elseif((string) $facet->id=='tag'){
                    add_post_meta($collection_id, 'socialdb_collection_facets', 'tag');
                    update_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_orientation', (string)$facet->orientation);
                    update_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_widget', (string)$facet->widget);
                    update_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_priority', (string)$facet->priority);
                    update_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_color', (string)$facet->color);
                    update_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_range_options', (string)$facet->range_option);
               }else{
                    add_post_meta($collection_id, 'socialdb_collection_facets',(string) $facet->id);
                    update_post_meta($collection_id, 'socialdb_collection_facet_' . (string)$facet->id . '_orientation', (string)$facet->orientation);
                    update_post_meta($collection_id, 'socialdb_collection_facet_' . (string)$facet->id . '_widget', (string)$facet->widget);
                    update_post_meta($collection_id, 'socialdb_collection_facet_' .(string) $facet->id . '_priority', (string)$facet->priority);
                    update_post_meta($collection_id, 'socialdb_collection_facet_' .(string) $facet->id . '_color', (string)$facet->color);
                    update_post_meta($collection_id, 'socialdb_collection_facet_' .(string) $facet->id . '_range_options', (string)$facet->range_option);
               }
           }
       }
   }
   /**
     * function add_channels($xml,$collection_id)
     * @param object $xml
     * @param int $collection_id
     * @return array O id da propriedades criadas.
     * @author: Eduardo Humberto 
     */
   public function add_channels($xml,$collection_id) {
       if(isset($xml->channels->channel)){
           foreach ($xml->channels->channel as $channel) {
              $ch = array(
                    'post_type' => 'socialdb_channel',
                    'post_title' => (string) $channel->post_title,
                    'post_status' => 'publish',
                    'post_author' => get_current_user_id(),
                );
               $channel_id = wp_insert_post($ch);
               add_post_meta($collection_id, 'socialdb_collection_channel', $channel_id);
               add_post_meta($channel_id, 'socialdb_imported_id', (string) $channel->post_id);
               if((string) $channel->type=='socialdb_channel_youtube'){
                   //wp_set_object_terms($channel_id,  get_term_by('name', 'socialdb_channel_oaipmhdc', 'socialdb_channel_type')->term_id);
                   update_post_meta($channel_id, 'socialdb_channel_youtube_last_update',(string) $channel->socialdb_channel_youtube_last_update);
                   update_post_meta($channel_id, 'socialdb_channel_youtube_earlier_update',(string) $channel->socialdb_channel_youtube_earlier_update);
                   update_post_meta($channel_id, 'socialdb_channel_youtube_import_status',(string) $channel->socialdb_channel_youtube_import_status);
                   update_post_meta($channel_id, 'socialdb_channel_playlist_identificator',(string) $channel->socialdb_channel_playlist_identificator);
               }elseif((string) $channel->type=='socialdb_channel_csv'){
                   wp_set_object_terms($channel_id,  get_term_by('name', 'socialdb_channel_csv', 'socialdb_channel_type')->term_id,'socialdb_channel_type');
                   update_post_meta($channel_id, 'socialdb_channel_csv_delimiter',(string) $channel->socialdb_channel_csv_delimiter);
                   update_post_meta($channel_id, 'socialdb_channel_csv_has_header',(string) $channel->socialdb_channel_csv_has_header);
                   update_post_meta($channel_id, 'socialdb_channel_csv_mapping',(string) $channel->socialdb_channel_csv_mapping);
                  
               }elseif((string) $channel->type=='socialdb_channel_oaipmhdc'){
                   wp_set_object_terms($channel_id,  get_term_by('name', 'socialdb_channel_oaipmhdc', 'socialdb_channel_type')->term_id,'socialdb_channel_type');
                   update_post_meta($channel_id, 'socialdb_channel_oaipmhdc_last_update',(string) $channel->socialdb_channel_oaipmhdc_last_update);
                   update_post_meta($channel_id, 'socialdb_channel_oaipmhdc_first_token',(string) $channel->socialdb_channel_oaipmhdc_first_token);
                   update_post_meta($channel_id, 'socialdb_channel_oaipmhdc_initial_size',(string) $channel->socialdb_channel_oaipmhdc_initial_size);
                   update_post_meta($channel_id, 'socialdb_channel_oaipmhdc_sets',(string) $channel->socialdb_channel_oaipmhdc_sets);
                   update_post_meta($channel_id, 'socialdb_channel_oaipmhdc_is_harvesting',(string) $channel->socialdb_channel_oaipmhdc_is_harvesting);
                   update_post_meta($channel_id, 'socialdb_channel_oaipmhdc_mapping',(string) $channel->socialdb_channel_oaipmhdc_mapping);
                   update_post_meta($channel_id, 'socialdb_channel_oaipmhdc_import_object',(string) $channel->socialdb_channel_oaipmhdc_import_object);
               }elseif((string) $channel->type=='socialdb_channel_instagram'){
                   update_post_meta($channel_id, 'socialdb_instagram_identificator',(string) $channel->socialdb_instagram_identificator);
                   update_post_meta($channel_id, 'socialdb_instagram_identificator_last_update',(string) $channel->socialdb_instagram_identificator_last_update);
                   update_post_meta($channel_id, 'socialdb_instagram_import_status',(string) $channel->socialdb_instagram_import_status);
               }elseif((string) $channel->type=='socialdb_channel_flickr'){
                   update_post_meta($channel_id, 'socialdb_filckr_identificator',(string) $channel->socialdb_filckr_identificator);
                   update_post_meta($channel_id, 'socialdb_filckr_identificator_last_update',(string) $channel->socialdb_filckr_identificator_last_update);
                   update_post_meta($channel_id, 'socialdb_filckr_import_status',(string) $channel->socialdb_filckr_import_status);
               }
           }
       }
   }
   ##################################### IMPORTAR ITEMS DA COLECAO #################################
   /**
     * function import_itens($dir)
     * @args string $dir 
     * @return void Apenas insere os itens importados no banco de dados
     * @author: Eduardo Humberto 
     */
   public function import_items($dir,$collection_id) {
       $cont = 0;
       while (is_dir($dir.'/'.$cont)){
           $object_id = $this->open_item_xml($dir.'/'.$cont,$collection_id);
           $this->import_thumbnail_item($dir.'/'.$cont, $object_id);
           if(is_dir($dir.'/'.$cont.'/content')){
               $this->import_content_item($dir.'/'.$cont.'/content', $object_id);
           }
           if(is_dir($dir.'/'.$cont.'/files')){
               $this->import_files_item($dir.'/'.$cont.'/files', $object_id);
           }
           $cont++;
       }
   }
   /**
     * function open_item_xml($xml,$collection_id)
     * @return void Apenas insere os itens importados no banco de dados
     * @author: Eduardo Humberto 
     */
   public function open_item_xml($dir,$collection_id) {
       $xml = simplexml_load_file($dir.'/item.xml');
       
       $user = get_user_by('email', (string)$xml->post_author);
       if($user){
           $author = $user->ID;
       }else{
           $author = get_current_user_id();
       }
       
       $object = array(
            'post_type' => 'socialdb_object',
            'post_title' => (string) $xml->post_title,
            'post_status' => 'publish',
            'post_date' => (string) $xml->post_date,
            'post_name' => (string) $xml->post_name,
            'post_content' => (string) $xml->post_content,
            'post_author' => $author,
        );
       $object_id = wp_insert_post($object);
       $this->set_common_field_values($object_id, 'title', (string) $xml->post_title);
       $this->set_common_field_values($object_id, 'description', (string) $xml->post_content);
       //categoria raiz da colecao
       update_post_meta($object_id, 'socialdb_object_from',(string) $xml->socialdb_object_from);
       $this->set_common_field_values($object_id, 'object_from', 'external');
       update_post_meta($object_id, 'socialdb_object_dc_source', (string) $xml->socialdb_object_dc_source);
       $this->set_common_field_values($object_id, 'object_source', (string) $xml->socialdb_object_dc_source);
       update_post_meta($object_id, 'socialdb_object_dc_type', (string) $xml->socialdb_object_dc_type);
       $this->set_common_field_values($object_id, 'object_type',  (string) $xml->socialdb_object_dc_type);
       update_post_meta($object_id, 'socialdb_object_content', (string) $xml->socialdb_object_content);
       $this->set_common_field_values($object_id, 'object_content', (string) $xml->socialdb_object_content);
       $this->insert_categories_item($xml, $object_id);
       $this->insert_tags_item($xml, $object_id, $collection_id);
       $this->insert_properties_xml_items($xml, $object_id);
       return $object_id;
   }
   /**
     * function insert_categories_item($xml,$object_id)
     * @return void Insere as categorias no item
     * @author: Eduardo Humberto 
     */
   public function insert_categories_item($xml,$object_id) {
       $new_ids = [];
       $ids = explode('|', (string) $xml->categories);
       if(is_array($ids)){
           foreach ($ids as $id) {
               $new_ids[] = (int)$this->get_term_imported_id($id);
           }
       }
       wp_set_object_terms($object_id, $new_ids, 'socialdb_category_type');
   }
   /**
     * function insert_categories_tag($xml,$object_id)
     * @return void Insere as categorias no item
     * @author: Eduardo Humberto 
     */
   public function insert_tags_item($xml,$object_id,$collection_id) {
       $new_tags = [];
       $tags = wp_get_object_terms($collection_id,'socialdb_tag_type');// tags da colecao
       $tags_xml = explode('|', (string) $xml->tags);// tags do xml
       if((string) $xml->tags!=''&&is_array($tags_xml)){ // verifico se tem tags no xmml
           foreach ($tags_xml as $tag_xml) {// varro todas as tags do xml
               if($tags&&is_array($tags)){// verifico se ha tags na colecao
                   $exist = false;// flag q mostra q nao encontrou tag na colecao com o nome da tag do xml
                   foreach ($tags as $tag) { // percorro todas as tags da colecao 
                       if($tag->name==$tag_xml){ // verifico se o nome da tag do xml ja existe nas tags da colecao
                            wp_set_object_terms($object_id, $tag->term_id, 'socialdb_tag_type',true);
                            $exist = true; // apenas para mostrar q nao e necessario criar uma nova tag
                            break;
                       }
                   }
                   if(!$exist){// se nao bateu com nenhuma tag da colecao ele cria uma nova tag
                        $new_tag =wp_insert_term( trim($tag_xml),
                           'socialdb_tag_type',
                                 array('parent'=>  get_term_by('name','socialdb_tag','socialdb_tag_type')->term_id,
                                   'slug'=>  $this->generate_slug($tag_xml,$collection_id)));
                        wp_set_object_terms($object_id, $new_tag['term_id'], 'socialdb_tag_type',true); //adicono nno objeto
                        wp_set_object_terms($collection_id,$new_tag['term_id'], 'socialdb_tag_type',true); // adciono no calecao
                   }
               }  else {// se nao ha tags na colecao 
                     $new_tag =wp_insert_term( trim($tag_xml),
                           'socialdb_tag_type',
                                 array('parent'=>  get_term_by('name','socialdb_tag','socialdb_tag_type')->term_id,
                                   'slug'=>  $this->generate_slug($tag_xml,$collection_id)));
                        wp_set_object_terms($object_id, $new_tag['term_id'], 'socialdb_tag_type',true); //adicono nno objeto
                        wp_set_object_terms($collection_id,$new_tag['term_id'], 'socialdb_tag_type',true); // adciono no calecao
                   
               }               
           }
       }
   }
    /**
     * function insert_properties_xml($xml,$object_id)
     * @return void Insere as proriedades no item
     * @author: Eduardo Humberto 
     */
    public function insert_properties_xml_items($xml,$object_id) {
       $properties = $xml->properties->property; 
       if($properties){
           foreach ($properties as $property) {
               if(!isset($property->count)){
                    $id = $this->get_term_imported_id((string)$property->id);
                    update_post_meta($object_id, 'socialdb_property_'.$id, (string)$property->value);
                    $this->set_common_field_values($object_id, "socialdb_property_$id",(string)$property->value);
               }else if(isset($property->count)){
                    $id = $this->get_term_imported_id((string)$property->id);
                    $counter = (string) $property->count;
                    $compounds = $property->compound;
                    $ids = [];
                    if($compounds){
                        foreach ($compounds as $compound) {
                            if(isset($compound->cat)){
                                $cat_id = (int)$this->get_term_imported_id((string)$compound->cat);
                                $ids[] = $cat_id.'_cat';
                                wp_set_object_terms( $object_id,(int)$cat_id,'socialdb_category_type',true);
                            }else{
                                $property_id = $this->get_term_imported_id((string)$compound->property_id);
                                $ids[] = $this->sdb_add_post_meta($object_id, 'socialdb_property_'.$property_id, (string)$compound->value);
                                $this->set_common_field_values($object_id, "socialdb_property_$property_id",(string)$property->value);
                            }
                        }
                        update_post_meta($object_id, 'socialdb_property_'.$id.'_'.$counter, implode(',', $ids));
                    }
               }
               
           }
       }
    }
    
    /**
     * import_thumbnail_item($dir,$object_id)
     * @return void Insere o arquivo content do item
     * @author: Eduardo Humberto 
     */
     public function import_thumbnail_item($dir,$object_id) {
        if(is_file($dir.'/thumbnail.png')){
            $thumbnail_id = $this->insert_attachment_file($dir.'/thumbnail.png', $object_id);
            set_post_thumbnail($object_id, $thumbnail_id);
        }elseif(is_file($dir.'/thumbnail.jpg')){
            $thumbnail_id = $this->insert_attachment_file($dir.'/thumbnail.jpg', $object_id);
             set_post_thumbnail($object_id, $thumbnail_id);
        }elseif(is_file($dir.'/thumbnail.gif')){
            $thumbnail_id = $this->insert_attachment_file($dir.'/thumbnail.gif', $object_id);
            set_post_thumbnail($object_id, $thumbnail_id);
        }elseif(is_file($dir.'/thumbnail.jpeg')){
            $thumbnail_id = $this->insert_attachment_file($dir.'/thumbnail.jpeg', $object_id);
            set_post_thumbnail($object_id, $thumbnail_id);
        }
    }
    
     /**
     * import_content_item($dir,$object_id)
     * @return void Insere o arquivo content do item
     * @author: Eduardo Humberto 
     */
    public function import_content_item($dir,$object_id) {
        //$categories_id = [];
        foreach (new DirectoryIterator($dir) as $fileInfo) {
            if ($fileInfo->isDot()) continue;
            
            $content_id = $this->insert_attachment_file($fileInfo->getPath(). '/' .$fileInfo->getFilename(), $object_id);
            add_post_meta($object_id, '_file_id', $content_id);
            update_post_meta($object_id, 'socialdb_object_content', $content_id);
        }
    }
    /**
     * import_files_item($dir,$object_id)
     * @return void Insere as proriedades no item
     * @author: Eduardo Humberto 
     */
    public function import_files_item($dir,$object_id) {
        //$categories_id = [];
        foreach (new DirectoryIterator($dir) as $fileInfo) {
            if ($fileInfo->isDot())
                continue;
            $content_id = $this->insert_attachment_file($fileInfo->getPath(). '/' .$fileInfo->getFilename(), $object_id);
            add_post_meta($object_id, '_file_id', $content_id);
            //$categories_id[] = $data['ids'];
        }
        //return $categories_id;
    }
    /*
     * @signature importCollectionTemplate($data)
     * @param array $data Os parametros e o file vindo do formulario
     * @return json A url da colecao criada para redirecionamento
     */
    public function importCollectionTemplate($data) {
        $template = $data['template'];
        // VERIFICO SE O NOME DA COLECAO PODE SER USADO
        /*
        if ( $this->verify_collection($data['collection_name']) ) {
            return false;
        }
        */
        $dir_created = TAINACAN_UPLOAD_FOLDER . "/data/templates/".$template; 
        if(!is_dir($dir_created))
            $dir_created = dirname(__FILE__) . "/../../data/templates/".$template; 
            
        if(is_dir($dir_created.'/package/taxonomies' )){
           $this->import_xml_taxonomies($dir_created.'/package/taxonomies' );
        }
        if(is_dir($dir_created.'/package/metadata' )&&  is_file($dir_created.'/package/metadata/administrative_settings.xml')){
            $xml = simplexml_load_file($dir_created.'/package/metadata/administrative_settings.xml');
            $collection_id = $this->import_collection($xml,$dir_created);
            //thumbnail
            $this->add_thumbnail_collection($dir_created,$collection_id);
            //capa da colecao
            $this->add_cover_collection($dir_created,$collection_id);
            $this->update_collection_data($collection_id, $data);
            
            return $collection_id;
        } else {
            return false;
        }
        
    }
    /**
     * Metodo que atualiza o titulo e a categoria raiz de uma colecao  definidos pelo
     * o usuario no formulario de insercao do item
     * 
     * @param type $collection_id
     * @param type $data
     */
    public function update_collection_data($collection_id , $data){
        
        $collection = array(
            'ID'=> $collection_id,
            'post_title' => $data['collection_name'],
            'post_name' => sanitize_title(remove_accent($data['collection_name'])),
            'post_status' => 'draft',
            'post_author' => get_current_user_id(),
        );
        wp_update_post($collection);
        $category_root_id = $this->get_category_root_of($collection_id);
        wp_update_term($category_root_id, 'socialdb_category_type', array(
                'name' => $data['collection_object'] . __(' of ','tainacan') . $data['collection_name']
            ));
        
    }

}
