<?php

ini_set('max_input_vars', '10000');
error_reporting(0);
session_write_close();
ini_set('max_execution_time', '0');
ini_set('memory_limit', '-1');
include_once (dirname(__FILE__) . '/../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');

class HelpersAPIModel extends Model {

    public static function createCollection($post,$metas, SynchronizeModel $class) {
        $model = new Model();
        $collection = array(
            'post_type' => 'socialdb_collection',
            'post_title' => $post['title'],
            'post_status' => 'publish',
            'post_content' => $post['content'],
            'post_author' => get_current_user_id(),
        );
        $collection_id = wp_insert_post($collection);
        $model->createSocialMappingDefault($collection_id);
        foreach ($metas as $meta) {
            if($meta['key'] == 'socialdb_collection_object_type' 
                    || $meta['key'] == 'socialdb_collection_longitude_meta'
                    || $meta['key'] == 'socialdb_collection_latitude_meta'
                    || $meta['key'] == 'socialdb_collection_facets'
                    || $meta['key'] == 'socialdb_collection_channel'
                    || $meta['key'] == 'socialdb_collection_default_ordering'
                    || $meta['key'] == 'socialdb_collection_moderator'
                    || strpos($meta['key'], 'socialdb_collection_facet_') !== false
                    || strpos($meta['key'], 'socialdb_collection_property_') !== false ){
                continue;
            }else if($meta['key'] == 'socialdb_collection_tab'){
                 HelpersAPIModel::insertTabs($meta['ID'],$meta['value'],$collection_id,$class->url);
            }else{
                 update_post_meta($collection_id, $meta['key'], $meta['value']);
            }
        }
        if(isset($post['terms']['socialdb_collection_type'][0])){
            $type = get_term_by('id', $post['terms']['socialdb_collection_type'][0]['ID'], 'socialdb_collection_type');
            wp_set_post_terms($collection_id, array($type->term_id), 'socialdb_collection_type');
        }
        //atualizo com o token atual
        update_post_meta($collection_id, 'socialdb_token', $class->token);
        return $collection_id;
    }

    /**
     * 
     * @param type $post
     * @param type $metas
     * @param type $idBlog
     * @return type
     */
    public static function updateCollection($post, $metas, SynchronizeModel $class, $idBlog) {
        $collection = array(
            'ID'=> $idBlog,
            'post_title' => $post['title'],
            'post_status' => 'publish',
            'post_content' => $post['content'],
            'post_author' => get_current_user_id(),
        ); 
        $collection_id = wp_update_post($collection);
        foreach ($metas as $meta) {
            if($meta['key'] == 'socialdb_collection_object_type' 
                    || $meta['key'] == 'socialdb_collection_longitude_meta'
                    || $meta['key'] == 'socialdb_collection_latitude_meta'
                    || $meta['key'] == 'socialdb_collection_facets'
                    || $meta['key'] == 'socialdb_collection_channel'
                    || $meta['key'] == 'socialdb_collection_default_ordering'
                    || $meta['key'] == 'socialdb_collection_moderator'
                    || strpos($meta['key'], 'socialdb_collection_facet_') !== false
                    || strpos($meta['key'], 'socialdb_collection_property_') !== false ){
                continue;
            }else if($meta['key'] == 'socialdb_collection_tab'){
                HelpersAPIModel::insertTabs($meta['ID'],$meta['value'],$collection_id,$class->url);
            }else{
                 update_post_meta($collection_id, $meta['key'], $meta['value']);
            }
        }
        if(isset($post['terms']['socialdb_collection_type'][0])){
            $type = get_term_by('id', $post['terms']['socialdb_collection_type'][0]['ID'], 'socialdb_collection_type');
            wp_set_post_terms($collection_id, array($type->term_id), 'socialdb_collection_type');
        }
        //atualizo com o token atual
        update_post_meta($collection_id, 'socialdb_token', $class->token);
        return $collection_id;
    }
    
    /**
     * atualiza os relacionmanetos que a colecao faz
     * @param type $collection_id
     * @param type $metas
     * @param SynchronizeModel $class
     */
    public static function updateCollectionsRelations($id_api,$metas, SynchronizeModel $class) {
        $collection_id =  MappingAPI::hasMapping($class->url, 'collections', $id_api);
        foreach ($metas as $meta) {
            if($meta['key'] == 'socialdb_collection_object_type'){
                update_post_meta($collection_id, 'socialdb_collection_object_type', MappingAPI::hasMapping($class->url, 'categories', $meta['value']));
            }else if($meta['key'] == 'socialdb_collection_facets'){
                $id_blog = ( MappingAPI::hasMapping($class->url, 'properties', $meta['value']) ) ? MappingAPI::hasMapping($class->url, 'properties', $meta['value']) : MappingAPI::hasMapping($class->url, 'categories', $meta['value']);
                HelpersAPIModel::updateFacets($collection_id, $id_blog);
                HelpersAPIModel::updateFacetsDetails($meta['value'],$id_blog,$collection_id,$metas);
            }else if(strpos($meta['key'], 'socialdb_collection_property_')!==false){
                if(strpos($meta['key'], '_required')!==false){
                    $id_api = str_replace('socialdb_collection_property_', '', str_replace('_required', '', $meta['key']));
                    $id_blog = ( MappingAPI::hasMapping($class->url, 'properties', $id_api) ) ? MappingAPI::hasMapping($class->url, 'properties', $id_api) : MappingAPI::hasMapping($class->url, 'categories', $id_api);
                    update_post_meta($collection_id,'socialdb_collection_property_'.$id_blog.'_required', $meta['value']);
                }else{
                    $id_api = str_replace('socialdb_collection_property_', '', str_replace('_mask_key', '', $meta['key']));
                    $id_blog = ( MappingAPI::hasMapping($class->url, 'properties',$id_api) ) ? MappingAPI::hasMapping($class->url, 'properties', $id_api) : MappingAPI::hasMapping($class->url, 'categories', $id_api);
                    update_post_meta($collection_id,'socialdb_collection_property_'.$id_blog.'_mask_key', $meta['value']);
                }
            }else if($meta['key'] == 'socialdb_collection_default_ordering'){
                update_post_meta($collection_id,'socialdb_collection_default_ordering', MappingAPI::hasMapping($class->url, 'properties', $id_api));
            }else if($meta['key'] == 'socialdb_collection_longitude_meta'){
                update_post_meta($collection_id,'socialdb_collection_longitude_meta', MappingAPI::hasMapping($class->url, 'properties', $id_api));
            }else if($meta['key'] == 'socialdb_collection_latitude_meta'){
                update_post_meta($collection_id,'socialdb_collection_latitude_meta', MappingAPI::hasMapping($class->url, 'properties', $id_api));
            }else if($meta['key'] == 'socialdb_collection_properties_ordenation'){
                $new_array = [];
                try{
                    $array = unserialize(base64_decode($meta['value']));
                    if(!is_array($array))
                        $array = unserialize($array);
                } catch (Exception $e){
                    $array = unserialize(utf8_decode(base64_decode($meta['value'])));
                    if(!is_array($array))
                        $array = unserialize($array);
                }
                if(is_array($array)){
                    foreach ($array as $key => $value) {
                        $properties = explode(',', $value);
                        $values = [];
                        $new_index = ($key == 'default') ? 'default' : MappingAPI::hasMapping($class->url, 'tabs', $key);
                        foreach ($properties as $property) {
                            $id =  MappingAPI::hasMapping($class->url, 'properties', str_replace('compounds-', '', $property));
                            $values[] = (strpos($property, 'compounds-') !== false) ? 'compounds-'.$id : $id;
                        }
                        $new_array[$new_index] = implode(',', $values);
                    }
                }
                update_post_meta($collection_id,'socialdb_collection_properties_ordenation', serialize($new_array));
            }else if($meta['key'] == 'socialdb_collection_update_tab_organization'){
                $new_array = [];
                $array = unserialize(unserialize(base64_decode($meta['value'])));
                if(is_array($array) && is_array($array[0])){
                    foreach ($array[0] as $key => $value) {
                        $new_array[MappingAPI::hasMapping($class->url, 'properties', $key)] = 
                                ($value == 'default') ? 'default' : MappingAPI::hasMapping($class->url, 'tabs', $value);
                    }
                }
                update_post_meta($collection_id,'socialdb_collection_update_tab_organization', serialize([$new_array]));
            }else if($meta['key'] == 'socialdb_collection_fixed_properties_visibility'){
                $new_array = [];
                $array =explode(',',$meta['value']);
                if(is_array($array)){
                    foreach ($array as $value) {
                        $new_array[] = MappingAPI::hasMapping($class->url, 'properties', $value);
                    }
                }
                update_post_meta($collection_id,'socialdb_collection_fixed_properties_visibility', implode(',',$new_array));
            }else if($meta['key'] == 'socialdb_collection_fixed_properties_labels'){
                $new_array = [];
                $array = unserialize(unserialize(base64_decode($meta['value'])));
                if(is_array($array)){
                    foreach ($array as $key => $value) {
                        $new_array[MappingAPI::hasMapping($class->url, 'properties', $key)] = $value;
                    }
                }
                update_post_meta($collection_id,'socialdb_collection_fixed_properties_labels', serialize($new_array));
            }
            
            
        }
    }
    
    /**
     * 
     * @param type $collection_id
     * @param type $id
     */
     public static function  updateFacets($collection_id,$id) {
        $metas = get_post_meta($collection_id, 'socialdb_collection_facets');
        if(!$metas || (is_array($metas) && !in_array($id, $metas))){
            add_post_meta($collection_id, 'socialdb_collection_facets',$id);
        }
    }
    
    /**
     * 
     * @param type $id_api
     * @param type $id_blog
     * @param type $collection_id
     * @param type $metas
     */
    public static function updateFacetsDetails($id_api,$id_blog,$collection_id,$metas) {
        foreach ($metas as $meta) {
             if($meta['key'] == 'socialdb_collection_facet_'.$id_api.'_widget'){
                 update_post_meta($collection_id, 'socialdb_collection_facet_'.$id_blog.'_widget', $meta['value']);
             }else if($meta['key'] == 'socialdb_collection_facet_'.$id_api.'_color'){
                 update_post_meta($collection_id, 'socialdb_collection_facet_'.$id_blog.'_color', $meta['value']);
             }else if($meta['key'] == 'socialdb_collection_facet_'.$id_api.'_priority'){
                 update_post_meta($collection_id, 'socialdb_collection_facet_'.$id_blog.'_priority', $meta['value']);
             }
        }
    }

    /**
     * 
     * @param type $post
     * @return type
     */
    public static function createCategory($category, $metas, SynchronizeModel $class, $parent_id = false) {
        $model = new Model();
        $array = wp_insert_term($category['name'], 'socialdb_category_type', array('parent' => (!$parent_id) ? get_term_by('slug', 'socialdb_taxonomy', 'socialdb_category_type')->term_id : $parent_id,
            'slug' => $model->generate_slug(trim($category['name']), 0)));
        add_term_meta($array['term_id'], 'socialdb_category_owner', get_current_user_id());
        update_term_meta($array['term_id'], 'socialdb_token', $class->token);
        return $array['term_id'];
    }

    public static function updateCategory($id, $category, $metas, SynchronizeModel $class) {
         $array = socialdb_update_term_name($id,$category['name']);
//        $array = wp_update_term($id, 'socialdb_category_type', array(
//            'name' => $category['name']));
        update_term_meta($array['term_id'], 'socialdb_token', $class->token);
        return $array['term_id'];
    }

    /**
     * 
     * @param type $category
     * @param type $metas
     * @return type
     */
    public static function createProperty($property, $metas, SynchronizeModel $class) {
        $model = new Model();
        $array = ['socialdb_property_data', 'socialdb_property_object', 'socialdb_property_term','socialdb_property_compounds'];
        if (isset($property['parent']) && in_array($property['parent']['slug'], $array)) {
            
            if(in_array($property['slug'], $model->fixed_slugs)){
                $array['term_id'] = get_term_by('slug', $property['slug'],'socialdb_property_type')->term_id;
            }else{
                $array = wp_insert_term($property['name'], 'socialdb_property_type', array('parent' => get_term_by('slug', $property['parent']['slug'], 'socialdb_property_type')->term_id,
                    'slug' => $model->generate_slug(trim($property['name']), 0)));
            }
            //metas
            if (!is_wp_error($array) && is_array($metas) && isset($array['term_id'])) {
                MappingAPI::saveMapping($class->url, 'properties', $property['ID'], $array['term_id']);
                update_term_meta($array['term_id'], 'socialdb_token', $class->token);
                foreach ($metas as $meta) {
                    $ids_category = ['socialdb_property_object_category_id', 'socialdb_property_term_root'];
                    if (in_array($meta['key'], $ids_category) && trim($meta['value']) != '') {
                        $ids = explode(',', $meta['value']);
                        $new_ids = [];
                        foreach ($ids as $value) {
                            $new_ids[] = MappingAPI::hasMapping($class->url, 'categories', $value);
                        }
                        update_term_meta($array['term_id'], $meta['key'], implode(',', $new_ids));
                    } else if ($meta['key'] == 'socialdb_property_object_reverse' && trim($meta['value']) != '') {
                        if (!MappingAPI::hasMapping($class->url, 'properties', $meta['value'])) {
                            $term = $class->readProperty($meta['value']);
                            $metas_reverse = $class->readPropertyMetas($meta['value']);
                            HelpersAPIModel::createProperty($term, $metas_reverse, $class);
                        } else {
                            update_term_meta($array['term_id'], $meta['key'], MappingAPI::hasMapping($class->url, 'properties', $meta['value']));
                        }
                    }else if($meta['key'] == 'socialdb_property_collection_id' && trim($meta['value']) != ''){
                          update_term_meta($array['term_id'], $meta['key'], MappingAPI::hasMapping($class->url, 'collections', $meta['value']));
                    }else if($meta['key'] == 'socialdb_property_created_category' && trim($meta['value']) != ''){
                          update_term_meta($array['term_id'], $meta['key'], MappingAPI::hasMapping($class->url, 'categories', $meta['value']));
                    }else if ($meta['key'] == 'socialdb_property_compounds_properties_id' && trim($meta['value']) != '') {
                        $ids = explode(',', $meta['value']);
                        $new_ids = [];
                        foreach ($ids as $value) {
                            $new_ids[] = MappingAPI::hasMapping($class->url, 'properties', $value);
                        }
                        update_term_meta($array['term_id'], $meta['key'], implode(',', $new_ids));
                    }else if ($meta['key'] == 'socialdb_property_is_compounds' && trim($meta['value']) != ''){
                        $array_serializado = unserialize(unserialize(base64_decode($meta['value'])));
                        $new_ids = [];
                        foreach ($array_serializado as $index => $value) {
                            $new_ids[MappingAPI::hasMapping($class->url, 'properties', $index)] = $value;
                            if(MappingAPI::hasMapping($class->url, 'properties', $index) !== false){
                                $id = MappingAPI::hasMapping($class->url, 'properties', $index);
                                $terms = get_term_meta($id, 'socialdb_property_compounds_properties_id', true);
                                $ids = array_filter(explode(',', $terms));
                                if(is_array($ids) && !in_array($array['term_id'], $ids)){
                                    $ids[] = $array['term_id'];
                                    update_term_meta($id,'socialdb_property_compounds_properties_id', implode(',', $ids));
                                }
                            }
                        }
                        update_term_meta($array['term_id'], $meta['key'], serialize($new_ids));
                    }else {
                        update_term_meta($array['term_id'], $meta['key'], $meta['value']);
                    }
                }
            }
            return $array['term_id'];
        }
    }
    
    public static function updateProperty($property, $metas, SynchronizeModel $class) {
        $array = ['socialdb_property_data', 'socialdb_property_object', 'socialdb_property_term'];
        if (in_array($property['parent']['slug'], $array)) {
            $array = wp_update_term(MappingAPI::hasMapping($class->url, 'properties', $property['ID']), 'socialdb_property_type', 
                    array('name'=> $property['name']));
            //metas
            if (is_array($metas) && isset($array['term_id'])) {
                update_term_meta($array['term_id'], 'socialdb_token', $class->token);
                foreach ($metas as $meta) {
                    $ids_category = ['socialdb_property_object_category_id', 'socialdb_property_term_root'];
                    if (in_array($meta['key'], $ids_category) && trim($meta['value']) != '') {
                        $ids = explode(',', $meta['value']);
                        $new_ids = [];
                        foreach ($ids as $value) {
                            $new_ids[] = MappingAPI::hasMapping($class->url, 'categories', $value);
                        }
                        update_term_meta($array['term_id'], $meta['key'], implode(',', $new_ids));
                    } else if ($meta['key'] == 'socialdb_property_object_reverse' && trim($meta['value']) != '') {
                        if (!MappingAPI::hasMapping($class->url, 'properties', $meta['value'])) {
                            $term = $class->readProperty($meta['value']);
                            $metas_reverse = $class->readPropertyMetas($meta['value']);
                            HelpersAPIModel::createProperty($term, $metas_reverse, $class);
                        } else {
                            update_term_meta($array['term_id'], $meta['key'], MappingAPI::hasMapping($class->url, 'properties', $meta['value']));
                        }
                    }else if($meta['key'] == 'socialdb_property_collection_id' && trim($meta['value']) != ''){
                          update_term_meta($array['term_id'], $meta['key'], MappingAPI::hasMapping($class->url, 'collections', $meta['value']));
                    }else if($meta['key'] == 'socialdb_property_created_category' && trim($meta['value']) != ''){
                          update_term_meta($array['term_id'], $meta['key'], MappingAPI::hasMapping($class->url, 'categories', $meta['value']));
                    }else if ($meta['key'] == 'socialdb_property_compounds_properties_id' && trim($meta['value']) != '') {
                        $ids = explode(',', $meta['value']);
                        $new_ids = [];
                        foreach ($ids as $value) {
                            $new_ids[] = MappingAPI::hasMapping($class->url, 'properties', $value);
                        }
                        update_term_meta($array['term_id'], $meta['key'], implode(',', $new_ids));
                    }else if ($meta['key'] == 'socialdb_property_is_compounds' && trim($meta['value']) != ''){
                        error_reporting(0);
                        $array_serializado = unserialize(unserialize(base64_decode($meta['value'])));
                        $new_ids = [];
                        foreach ($array_serializado as $index => $value) {
                            $new_ids[MappingAPI::hasMapping($class->url, 'properties', $index)] = $value;
                            if(MappingAPI::hasMapping($class->url, 'properties', $index) !== false){
                                $id = MappingAPI::hasMapping($class->url, 'properties', $index);
                                $terms = get_term_meta($id, 'socialdb_property_compounds_properties_id', true);
                                $ids = array_filter(explode(',', $terms));
                                if(is_array($ids) && !in_array($array['term_id'], $ids)){
                                    $ids[] = $array['term_id'];
                                    update_term_meta($id,'socialdb_property_compounds_properties_id', implode(',', $ids));
                                }
                            }
                        }
                        update_term_meta($array['term_id'], $meta['key'], serialize($new_ids));
                    }else {
                        update_term_meta($array['term_id'], $meta['key'], $meta['value']);
                    }
                }
            }
            return $array['term_id'];
        }
    }
    
    /**
    * 
    * 
    * @param xml $xml
    * @param int $collection_id O id colecao
    * 
    */
   public static function insertTabs($ID,$name,$collection_id,$url) {
        $model = new Model();
        if(!MappingAPI::hasMapping($url, 'tabs', $ID)){
            $new_id = $model->sdb_add_post_meta($collection_id, 'socialdb_collection_tab',$name);
            MappingAPI::saveMapping($url, 'tabs', $ID, $new_id);
        }else{
            $model->sdb_update_post_meta(MappingAPI::hasMapping($url, 'tabs', $ID), $name);
        }
   }

}

################################################################################

class MappingAPI extends Model {

    public static function hasMapping($url, $type, $id_api) {
        $option = get_option('mapping-api-tainacan');
        if ($option) {
            $array = unserialize($option);
            foreach ($array as $index => $map) {
                if ($map['url'] == $url && isset($map[$type][$id_api])) {
                    return $map[$type][$id_api];
                }
            }
            //se nao foi mapeado
            return false;
        } else {
            return false;
        }
    }

    /**
     * 
     * @param type $url
     * @param type $type
     * @param type $id_api
     * @param type $id_blog
     */
    public static function saveMapping($url, $type, $id_api, $id_blog) {
        $block = false;
        $option = get_option('mapping-api-tainacan');
        if ($option) {
            $array = unserialize($option);
            foreach ($array as $index => $map) {
                if ($map['url'] == $url) {
                    $block = TRUE;
                    $map[$type][$id_api] = $id_blog;
                }
                $array[$index] = $map;
            }
            //se nao foi mapeado
            if (!$block) {
                $var = array('url' => $url, 'collections' => [], 'properties' => [], 'categories' => [],'tabs'=>[]);
                $var[$type][$id_api] = $id_blog;
                $array[] = $var;
            }
            update_option('mapping-api-tainacan', serialize($array));
        } else {
            $var = array('url' => $url, 'collections' => [], 'properties' => [], 'categories' => [],'tabs'=>[]);
            $var[$type][$id_api] = $id_blog;
            update_option('mapping-api-tainacan', serialize([$var]));
        }
    }
    
    /**
     * 
     * @param string $url
     * @param string $token
     */
    public static function garbageCollector($url,$token) {
        $option = get_option('mapping-api-tainacan');
        if ($option) {
            $arrays = unserialize($option);
            foreach ($arrays as $index => $array) {
                if($index == 'collections'){
                    foreach ($array as $id_blog) {
                        $has_token = get_post_meta($id_blog, 'socialdb_token', true);
                        if(!$has_token || $has_token != $token){
                            $collection = array(
                                'ID'=> $id_blog,
                                'post_status' => 'draft'
                            ); 
                            $collection_id = wp_update_post($collection);
                        }
                    }
                }else if($index == 'categories' || $index == 'properties'){
                    foreach ($array as $id_blog) {
                        $has_token = get_term_meta($id_blog, 'socialdb_token', true);
                        if(!$has_token || $has_token != $token){
                            wp_delete_term($id_blog,($index == 'categories') ? 'socialdb_category_type': 'socialdb_property_type');
                        }
                    }
                }
            }
        }
    }

}
