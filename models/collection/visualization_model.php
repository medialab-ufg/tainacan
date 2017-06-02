<?php
include_once (dirname(__FILE__) . '/../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../property/property_model.php');
require_once(dirname(__FILE__) . '../../license/license_model.php');
require_once(dirname(__FILE__) . '../../category/category_model.php');
require_once(dirname(__FILE__) . '../../collection/collection_model.php');

class VisualizationModel extends CollectionModel {
    
    public $dynatree_number_items;

    public function __construct(){
        if(has_filter('alter_dynatree_number_of_items')){
            $this->dynatree_number_items = apply_filters('alter_dynatree_number_of_items', '');
        }else{
            $this->dynatree_number_items = 9;
        }
    }

    /* function initJit() */
    /* receive ((array) data) */
    /* inite the div hypertree in the template index */
    /* Author: Eduardo */

    public function initJit($data) {
        global $wpdb;
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $wp_terms = $wpdb->prefix . "terms";
        $collection = get_post($data["collection_id"]);
        $jit = array('id' => "$collection->ID", 'name' => $collection->post_title, 'data' => [], 'children' => []);
        $facets_id = array_filter(array_unique(get_post_meta($data['collection_id'], 'socialdb_collection_facets')));
        foreach ($facets_id as &$facet_id) {
            $facet = get_term_by('id', $facet_id, 'socialdb_category_type');
            //$classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_color', true);
            if ($facet) {
                $jit['children'][] = array('name' => ucfirst($facet->name), 'id' => $facet->term_id, 'data' => ['relation' => 'member']);
                //$dynatree[] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id, 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
                //$dynatree[end(array_keys($dynatree))] = $this->getChildrenDynatree($facet->term_id, $dynatree[end(array_keys($dynatree))], $classCss);
                $query = "
                    SELECT * FROM $wp_terms t
                    INNER JOIN $wp_term_taxonomy tt ON t.term_id = tt.term_id
                    WHERE tt.parent = {$facet->term_id} ORDER BY t.name ASC
                    ";

                $results = $wpdb->get_results($query);

                if (count($results) > 0) {
                    $jit['children'][end(array_keys($jit['children']))]['children'] = $this->structureNode($results, $jit['children'][end(array_keys($jit['children']))]['children']);
                }
            }
        }
        return json_encode($jit);
    }

    function structureNode($descendentes, $jit2) {
        global $wpdb;
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $wp_terms = $wpdb->prefix . "terms";
        foreach ($descendentes as $descendente) {
            $query = "
			SELECT * FROM $wp_terms t
			INNER JOIN $wp_term_taxonomy tt ON t.term_id = tt.term_id
			WHERE tt.parent = {$descendente->term_id}";

            $subfilhos = $wpdb->get_results($query);
            if (count($subfilhos) > 0) {
                $jit2[] = array('name' => $descendente->name, 'id' => $descendente->term_id, 'data' => [], 'children' => []);
                $jit2[end(array_keys($jit2))]['children'] = $this->structureNode($subfilhos, $jit2[end(array_keys($jit2))]['children']);
            } else {
                $jit2[] = array('name' => $descendente->name, 'id' => $descendente->term_id, 'data' => [], 'children' => []);
            }
        }
        return $jit2;
    }

    public function rand_color() {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    public function initTreemapJit($data) {
        global $wpdb;
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $wp_terms = $wpdb->prefix . "terms";
        $collection = get_post($data["collection_id"]);
        $jit = array('id' => "$collection->ID", 'name' => $collection->post_title, 'data' => [], 'children' => []);
        $facets_id = array_filter(array_unique(get_post_meta($data['collection_id'], 'socialdb_collection_facets')));
        foreach ($facets_id as &$facet_id) {
            $facet = get_term_by('id', $facet_id, 'socialdb_category_type');
            //$classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_color', true);
            if ($facet) {
                $jit['children'][] = array('name' => ucfirst($facet->name), 'id' => $facet->term_id, 'data' => ['$color' => '#626262', '$area' => $facet->count]);
                $color = $this->rand_color();
                //$dynatree[] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id, 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
                //$dynatree[end(array_keys($dynatree))] = $this->getChildrenDynatree($facet->term_id, $dynatree[end(array_keys($dynatree))], $classCss);
                $query = "
                    SELECT * FROM $wp_terms t
                    INNER JOIN $wp_term_taxonomy tt ON t.term_id = tt.term_id
                    WHERE tt.parent = {$facet->term_id} ORDER BY t.name ASC
                    ";

                $results = $wpdb->get_results($query);

                if (count($results) > 0) {
                    $jit['children'][end(array_keys($jit['children']))]['children'] = $this->structureNodeTreemap($results, $jit['children'][end(array_keys($jit['children']))]['children'], $color);
                }
            }
        }
        return json_encode($jit);
    }

    function structureNodeTreemap($descendentes, $jit2, $color) {
        global $wpdb;
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $wp_terms = $wpdb->prefix . "terms";
        foreach ($descendentes as $descendente) {
            $query = "
			SELECT * FROM $wp_terms t
			INNER JOIN $wp_term_taxonomy tt ON t.term_id = tt.term_id
			WHERE tt.parent = {$descendente->term_id}";

            $subfilhos = $wpdb->get_results($query);
            if (count($subfilhos) > 0) {
                $jit2[] = array('name' => $descendente->name, 'id' => $descendente->term_id, 'data' => ['$color' => $color, '$area' => $descendente->count], 'children' => []);
                $jit2[end(array_keys($jit2))]['children'] = $this->structureNodeTreemap($subfilhos, $jit2[end(array_keys($jit2))]['children'], $color);
            } else {
                $jit2[] = array('name' => $descendente->name, 'id' => $descendente->term_id, 'data' => ['$color' => $color, '$area' => $descendente->count], 'children' => []);
            }
        }
        return $jit2;
    }

    /* function initDynatree() */
    /* receive ((array) data) */
    /* inite the div dynatree in the template index */
    /* Author: Eduardo */

    public function initDynatreeOld($data) {
        $propertyModel = new PropertyModel;
        // $facets_id = CollectionModel::get_facets($data['collection_id']);
        $facets_id = array_filter(array_unique(get_post_meta($data['collection_id'], 'socialdb_collection_facets')));
        foreach ($facets_id as &$facet_id) {
            $facet = get_term_by('id', $facet_id, 'socialdb_category_type');
            $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_color', true);
            if ($facet && get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_widget', true) == 'tree') {
                $dynatree[] = array('title' => ucfirst(Words($facet->name, 30)), 'key' => $facet->term_id . '_facet_category', 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
                $dynatree[end(array_keys($dynatree))] = $this->getChildrenDynatree($facet->term_id, $dynatree[end(array_keys($dynatree))], $classCss);
            }
        }
        $root_category = $this->get_category_root_of($data['collection_id']);
        $properties = $propertyModel->get_property_object_facets($root_category);
        if ($properties) {
            foreach ($properties as $property) {
                $facet = get_term_by('id', $property['id'], 'socialdb_property_type');
                $widget = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $property['id'] . '_widget', true);
                $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $property['id'] . '_color', true);
                if ($facet && $widget == 'tree'&&  in_array( $property['id'], $facets_id)) {
                    $dynatree[] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id . "_facet_property" . $property['id'], 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
                    $dynatree[end(array_keys($dynatree))] = $this->getPropertyRelDynatree($property, $dynatree[end(array_keys($dynatree))], $classCss);
                }
            }
        }
        $properties_data = $propertyModel->get_property_data_facets($root_category);
        if ($properties_data) {
            foreach ($properties_data as $property) {
                $facet = get_term_by('id', $property['id'], 'socialdb_property_type');
                $widget = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $property['id'] . '_widget', true);
                $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $property['id'] . '_color', true);
                if ($facet && $widget == 'tree'&&  in_array( $property['id'], $facets_id)) {
                    $dynatree[] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id . "_facet_property" . $property['id'], 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
                    $dynatree[end(array_keys($dynatree))] = $this->getPropertyDataDynatree($property, $dynatree[end(array_keys($dynatree))], $classCss);
                }
            }
        }
        //tags
        if (in_array('tag', $facets_id)) {
            if ((get_post_meta($data['collection_id'], 'socialdb_collection_hide_tags', true)) != 'yes' && get_post_meta($data['collection_id'], 'socialdb_collection_facet_tag_widget', true) == 'tree') {
                //tags
                $dynatree[] = array('title' => __('Tags', 'tainacan'), 'key' => 'tag_facet_tag', 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => 'tag_img');
                $dynatree[end(array_keys($dynatree))] = $this->getTagRelDynatree($data['collection_id'], $dynatree[end(array_keys($dynatree))], 'tag_img');
            }
        }
        // para tipos se estiver setado
        if (in_array('socialdb_object_dc_type', $facets_id)) {
            $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_socialdb_object_dc_type_color', true);
            $dynatree[] = array('title' => __('Type', 'tainacan'), 'key' => 'socialdb_object_dc_type_facet', 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
            $dynatree[end(array_keys($dynatree))] = $this->getTypeDynatree($data['collection_id'], $dynatree[end(array_keys($dynatree))], $classCss);
        }
        // para o formato
        if (in_array('socialdb_object_from', $facets_id)) {
            $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_socialdb_object_from_color', true);
            $dynatree[] = array('title' => __('Format', 'tainacan'), 'key' => 'socialdb_object_from_facet', 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
            $dynatree[end(array_keys($dynatree))] = $this->getFormatDynatree($data['collection_id'], $dynatree[end(array_keys($dynatree))], $classCss);
        }
        //fonte dos dados
        if (in_array('socialdb_object_dc_source', $facets_id)) {
            $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_socialdb_object_dc_source_color', true);
            $dynatree[] = array('title' => __('Source', 'tainacan'), 'key' => 'socialdb_object_from_facet', 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
            $dynatree[end(array_keys($dynatree))] = $this->getSourceDynatree($data['collection_id'], $dynatree[end(array_keys($dynatree))], $classCss);
        }
        //licencas dos itens
        if (in_array('socialdb_license_id', $facets_id)) {
            $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_socialdb_license_id_color', true);
            $dynatree[] = array('title' => __('License', 'tainacan'), 'key' => 'socialdb_license_id_facet_tag', 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
            $dynatree[end(array_keys($dynatree))] = $this->getLicensesDynatree($data['collection_id'], $dynatree[end(array_keys($dynatree))], $classCss);
        }

        return json_encode($dynatree);
    }
    
    /* function initDynatree() */
    /* receive ((array) data) */
    /* inite the div dynatree in the template index */
    /* Author: Eduardo */

    public function initDynatree($data) {
        $propertyModel = new PropertyModel;
        $labels_collection = ($data['collection_id']!='') ? get_post_meta($data['collection_id'], 'socialdb_collection_fixed_properties_labels', true) : false;
        // $facets_id = CollectionModel::get_facets($data['collection_id']);
        $facets_id = array_filter(array_unique(get_post_meta($data['collection_id'], 'socialdb_collection_facets')));
        $facets = array();
        if(is_array($facets_id)){
            foreach ($facets_id as  $facet_id) {
                $index = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_priority', true);
                if(isset($facets[$index])){
                    $facets[] =$facet_id;
                }  else {
                    $facets[$index] = $facet_id;
                }
            }
        }else{
            return false;
        }
        ksort($facets);
        foreach ($facets as &$facet_id) {
            $facet = get_term_by('id', $facet_id, 'socialdb_category_type');
            $ancestors = get_ancestors($facet->term_id,'socialdb_category_type');
            $skip = false;
            foreach ($ancestors as $value) {
               if(in_array($value, $facets)){
                   $skip = true;
               } 
            }
            if($skip)
                continue;
            $ordenation = $this->get_ordenation_facet($data['collection_id'],$facet_id);
            if($facet){
                $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_color', true);
                if ($facet && get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_widget', true) == 'tree') {
                    $hide_checkbox = (!has_filter('show_checkbox_facet'))? true : apply_filters('show_checkbox_facet','');
                    if($hide_checkbox){
                        $key = $facet->term_id . '_facet_category';
                    }else{
                          $key = $facet->term_id ;
                    }

                    if(mb_detect_encoding($facet->name)=='UTF-8'||mb_detect_encoding($facet->name)=='ASCII'){
                        $dynatree[] = array('title' => ucfirst(Words($facet->name, 30)), 'key' => $key, 'isLazy' => true, 'data' => '', 'expand' => true, 'hideCheckbox' => $hide_checkbox, 'addClass' => $classCss);
                    }else{
                        $dynatree[] = array('title' => ucfirst(Words(utf8_decode(utf8_encode($facet->name)), 30)), 'key' => $key, 'isLazy' => true, 'data' => '', 'expand' => true, 'hideCheckbox' => $hide_checkbox, 'addClass' => $classCss);
                    }

                    $ordenation = ($ordenation =='alphabetic') ? 't.name ASC' : 'tt.count DESC,t.name ASC';
                    $dynatree[end(array_keys($dynatree))] = $this->getChildrenDynatree($facet->term_id, $dynatree[end(array_keys($dynatree))], $classCss,$ordenation);
                }
            }//tags
            elseif ('tag'==$facet_id) {
                if ((get_post_meta($data['collection_id'], 'socialdb_collection_hide_tags', true)) != 'yes' && get_post_meta($data['collection_id'], 'socialdb_collection_facet_tag_widget', true) == 'tree') {
                    //tags
                    $dynatree[] = array('title' => __('Tags', 'tainacan'), 'key' => 'tag_facet_tag', 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => 'tag_img');
                    $dynatree[end(array_keys($dynatree))] = $this->getTagRelDynatree($data['collection_id'], $dynatree[end(array_keys($dynatree))], 'tag_img');
                }
            }elseif(get_term_by('id', $facet_id, 'socialdb_property_type')){   
                $facet = get_term_by('id', $facet_id, 'socialdb_property_type');
                $widget = get_post_meta($data['collection_id'], 'socialdb_collection_facet_'.$facet_id.'_widget', true);
                $type = $propertyModel->get_property_type($facet_id); // pego o tipo da propriedade;
                //METADADOS FIXOS
                if ('socialdb_property_fixed_type'==$facet->slug) {
                    $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_color', true);
                    $dynatree[] = array('title' => $facet->name, 'key' => 'socialdb_object_dc_type_facet', 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
                    $dynatree[end(array_keys($dynatree))] = $this->getTypeDynatree($data['collection_id'], $dynatree[end(array_keys($dynatree))], $classCss);
                }elseif ('socialdb_property_fixed_title'==$facet->slug&&$widget=='tree') {
                   $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_color', true);
                    $dynatree[] = array('title' =>$facet->name, 'key' => 'socialdb_object_dc_type_facet', 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
                    $property['metas']['socialdb_property_object_category_id'] = $this->get_category_root_of($data['collection_id']);
                    $property['id']= 'title';
                    $dynatree[end(array_keys($dynatree))] = $this->getPropertyRelDynatree($property, $dynatree[end(array_keys($dynatree))], $classCss);
                }
                // para o formato
                elseif ('socialdb_property_fixed_from'==$facet->slug) {
                    $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_color', true);
                    $dynatree[] = array('title' =>$facet->name, 'key' => 'socialdb_object_from_facet', 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
                    $dynatree[end(array_keys($dynatree))] = $this->getFormatDynatree($data['collection_id'], $dynatree[end(array_keys($dynatree))], $classCss);
                }
                //fonte dos dados
                elseif ('socialdb_property_fixed_source'==$facet->slug) {
                    $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_color', true);
                    $dynatree[] = array('title' => $facet->name, 'key' => 'socialdb_object_from_facet', 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
                    $dynatree[end(array_keys($dynatree))] = $this->getSourceDynatree($data['collection_id'], $dynatree[end(array_keys($dynatree))], $classCss);
                }
                //tags
                elseif ('socialdb_property_fixed_tags'==$facet->slug && $type=='tree') {
                    //tags
                    if($labels_collection){
                        $array = unserialize($labels_collection);
                        $name = (isset($array[$facet->term_id])) ? $array[$facet->term_id] : $facet->name;
                    }else{        
                        $name = $facet->name;
                    }
                    $dynatree[] = array('title' => $name, 'key' => 'tag_facet_tag', 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => 'tag_img');
                    $dynatree[end(array_keys($dynatree))] = $this->getTagRelDynatree($data['collection_id'], $dynatree[end(array_keys($dynatree))], 'tag_img');
                }
                //licencas dos itens
                elseif ('socialdb_property_fixed_license'==$facet->slug) {
                    $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_socialdb_license_id_color', true);
                    $dynatree[] = array('title' => $facet->name, 'key' => 'socialdb_license_id_facet_tag', 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
                    $dynatree[end(array_keys($dynatree))] = $this->getLicensesDynatree($data['collection_id'], $dynatree[end(array_keys($dynatree))], $classCss);
                }
                // FIM: METADADOS FIXOS
                elseif($type == 'socialdb_property_data'){
                    $widget = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_widget', true);
                    $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_color', true);
                    if ($facet && $widget == 'tree') {
                        $property = $propertyModel->get_all_property($facet_id, true);
                        $dynatree[] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id . "_facet_property" . $facet_id, 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
                        $ordenation = ($ordenation =='alphabetic') ? 'pm.meta_value' : 'count DESC';
                        $dynatree[end(array_keys($dynatree))] = $this->getPropertyDataDynatree($property, $dynatree[end(array_keys($dynatree))], $classCss,$ordenation);
                    }
                }elseif($type == 'socialdb_property_term'){
                    $property = $propertyModel->get_all_property($facet_id, true);
                    $new_id = $property['metas']['socialdb_property_term_root'];
                    $widget = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_widget', true);
                    $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_color', true);
                    delete_post_meta($data['collection_id'], 'socialdb_collection_facets',$facet_id);
                    add_post_meta($data['collection_id'], 'socialdb_collection_facets',$new_id);
                    add_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $new_id . '_widget', $widget);
                    add_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $new_id . '_color', $classCss);
                    if ($facet && $widget == 'tree'&&  in_array( $facet_id, $facets_id)) {
                        $property = $propertyModel->get_all_property($facet_id, true);
                        $dynatree[] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id . "_facet_property" . $facet_id, 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
                        $dynatree[end(array_keys($dynatree))] = $this->getChildrenDynatree($new_id, $dynatree[end(array_keys($dynatree))], $classCss);
                     }  
                }else{
                    $widget = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_widget', true);
                    $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_color', true);
                    if ($facet && $widget == 'tree'&&  in_array( $facet_id, $facets_id)) {
                        $property = $propertyModel->get_all_property($facet_id, true);
                        $dynatree[] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id . "_facet_property" . $facet_id, 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
                        $dynatree[end(array_keys($dynatree))] = $this->getPropertyRelDynatree($property, $dynatree[end(array_keys($dynatree))], $classCss);
                    }  
                }
            }
            
            
        }
        //json_encode($dynatree,JSON_UNESCAPED_UNICODE);
        //var_dump(json_last_error());
        return json_encode($dynatree,JSON_UNESCAPED_UNICODE);
    }
    /**
     * 
     * @param array $data os
     * @param boolean(optional) $hide_checkbox se sera mostrado o checkbox
     */
    public function initDynatreeSynonyms($data,$hide_checkbox = false) {
        $property_model = new PropertyModel;
        $categories = [];$dynatree = [];
        $list_object_terms = json_decode( $property_model->list_property_terms($data));
        if($list_object_terms->property_terms&&  is_array($list_object_terms->property_terms)){
            foreach ($list_object_terms->property_terms as $property) {
                if(isset($property->metas->socialdb_property_term_root)
                        &&$property->metas->socialdb_property_term_root!=''
                        &&!in_array($property->metas->socialdb_property_term_root, $categories)){
                    $categories[] = $property->metas->socialdb_property_term_root;
                }
            }
        }
        //categorias
        foreach ($categories as &$category) {
            $facet = get_term_by('id', $category, 'socialdb_category_type');
            $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $category . '_color', true);
            $classCss = ($classCss)?$classCss:'color4';
            if ($facet) {
                $dynatree[] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id, 'isLazy' => true,  'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
                $dynatree[end(array_keys($dynatree))] = $this->getChildrenDynatreeSingleEdit($facet->term_id, $dynatree[end(array_keys($dynatree))], $classCss,$hide_checkbox);
            }
        }
        //tags
        $dynatree[] = array('title' => __('Tags', 'tainacan'), 'key' => 'tag_facet_tag', 'isLazy' => true,  'expand' => true, 'hideCheckbox' => true, 'addClass' => 'tag_img');
        $dynatree[end(array_keys($dynatree))] = $this->getTagRelDynatree($data['collection_id'], $dynatree[end(array_keys($dynatree))], 'tag_img');
        return json_encode($dynatree);
    }
    
    /**
     * 
     * @param array $data os
     * @param boolean(optional) $hide_checkbox se sera mostrado o checkbox
     */
    public function initDynatreeTags($data,$hide_checkbox = false) {
        $property_model = new PropertyModel;
        $categories = [];
        $dynatree = [];
        /*$list_object_terms = json_decode( $property_model->list_property_terms($data));
        if($list_object_terms->property_terms&&  is_array($list_object_terms->property_terms)){
            foreach ($list_object_terms->property_terms as $property) {
                if(isset($property->metas->socialdb_property_term_root)
                        &&$property->metas->socialdb_property_term_root!=''
                        &&!in_array($property->metas->socialdb_property_term_root, $categories)){
                    $categories[] = $property->metas->socialdb_property_term_root;
                }
            }
        }
        //categorias
        foreach ($categories as &$category) {
            $facet = get_term_by('id', $category, 'socialdb_category_type');
            $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $category . '_color', true);
            $classCss = ($classCss)?$classCss:'color4';
            if ($facet) {
                $dynatree[] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id, 'isLazy' => true,  'expand' => true, 'hideCheckbox' => true, 'addClass' => $classCss);
                $dynatree[end(array_keys($dynatree))] = $this->getChildrenDynatreeSingleEdit($facet->term_id, $dynatree[end(array_keys($dynatree))], $classCss,$hide_checkbox);
            }
        }*/
        //tags
        $dynatree[] = array('title' => __('Tags', 'tainacan'), 'key' => 'tag_facet_tag', 'isLazy' => true,  'expand' => true, 'hideCheckbox' => true, 'addClass' => 'tag_img');
        $dynatree[end(array_keys($dynatree))] = $this->getTagRelDynatree($data['collection_id'], $dynatree[end(array_keys($dynatree))], 'tag_img');
        return json_encode($dynatree);
    }
    /** function initDynatreeSingleEdit() 
    * receive ((array) data) 
    * inite the div dynatree in the template index 
    * Author: Eduardo */
    public function initDynatreeSingleEdit($data,$hide_checkbox = true) {
        $dynatree_complete = [];
       // $facets_id = CollectionModel::get_facets($data['collection_id']);
        $facets_id = array_filter(array_unique(get_post_meta($data['collection_id'], 'socialdb_collection_facets')));
        if($data['hideCheckbox']&&$data['hideCheckbox']=='false'){
            $hide_checkbox = false;
             $is_showed = false ;
        }else{
             // se permitir a visualizacao da categoria raiz
             $is_showed = apply_filters( 'show_category_root_in_edit_category', $is_showed );
        }
        foreach ($facets_id as &$facet_id) {
            $facet = get_term_by('id', $facet_id, 'socialdb_category_type');
            $ancestors = get_ancestors($facet->term_id,'socialdb_category_type');
            $skip = false;
            foreach ($ancestors as $value) {
               if(in_array($value, $facets_id)){
                   $skip = true;
               } 
            }
            if($skip)
                continue;
            $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_color', true);
            $classCss = ($classCss)?$classCss:'color4';
            if ($facet) {
                $dynatree[] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id, 'isLazy' => true, 'data' => $url, 'expand' => true, 'hideCheckbox' => $hide_checkbox, 'addClass' => $classCss);
                $dynatree[end(array_keys($dynatree))] = $this->getChildrenDynatreeSingleEdit($facet->term_id, $dynatree[end(array_keys($dynatree))], $classCss,$hide_checkbox);
            }
        }
       
        if($is_showed){
            $category = get_term_by('slug','socialdb_category','socialdb_category_type');
            $dynatree = array(
                'title' => __('Category','tainacan'),
                'key' => $category->term_id,
                'expand' => true, 
                'hideCheckbox' => true, 
                'addClass' => 'color4',
                'children' => $dynatree
            );
        }
        return json_encode($dynatree);
    }

    /** function getChildrenDynatree() 
      /* @param ((int,string) id,(array) dynatree)
      /* @return the children of the facets and insert in the array of the dynatree
      /* Author: Eduardo */
    public function getPropertyRelDynatree($properties, $dynatree, $classCss = 'color1') {
        $objects = $this->get_category_root_posts($properties['metas']['socialdb_property_object_category_id']);
        $counter = 0;
        if (count($objects) > 0) {
            foreach ($objects as $child) {
                //$dynatree['children'][] = array('title' => $child->post_title.' ('.$this->count_metadata_by_value('socialdb_property_'.$properties['id'], $child->ID).')', 'key' => $child->ID . "_" . $properties['id'], 'addClass' => $classCss);
                $dynatree['children'][] = array('title' => $child->post_title . '', 'key' => $child->ID . "_" . $properties['id'], 'addClass' => $classCss);
                $counter++;
                if ($counter > 29) {
                    $dynatree['children'][] = array('title' => __('See more', 'tainacan'), 'hideCheckbox' => true, 'key' => $properties['metas']['socialdb_property_object_category_id'] . '_moreoptionsproperty' . $properties['id'], 'isLazy' => true, 'addClass' => 'more');
                    break;
                }
            }
        }
        return $dynatree;
    }

    /**
     * @signature getPropertyDataDynatree($properties, $dynatree, $classCss = 'color1')
     * @param array $properties Os dados da propriedade
     * @param array $dynatree O array do dynatree que esta sendo montado
     * @param string $classCss A classe Css do icone do dynatree
     */
    public function getPropertyDataDynatree($properties, $dynatree, $classCss = 'color4',$ordenation = 'pm.meta_value') {
        $metas = $this->get_property_data_values($properties['id'],$ordenation);
        $counter = 0;
        if (count($metas) > 0) {
            foreach ($metas as $meta_id => $meta_value) {
                //$dynatree['children'][] = array('title' => $meta_value . ' (' . $this->count_metadata_by_value('socialdb_property_' . $properties['id'], $meta_value) . ')', 'key' => $meta_id . "_" . $properties['id'] . '_datatext', 'addClass' => $classCss);
                $dynatree['children'][] = array('title' => $meta_value , 'key' => $meta_id . "_" . $properties['id'] . '_datatext', 'addClass' => $classCss);
                $counter++;
                if ($counter > $this->dynatree_number_items) {
                    $dynatree['children'][] = array('title' => __('See more', 'tainacan'), 'hideCheckbox' => true, 'key' => $properties['id'] . '_moreoptionsdataproperty' . $properties['id'], 'isLazy' => true, 'addClass' => 'more');
                    break;
                }
            }
        }
        return $dynatree;
    }

    /* function getChildrenDynatree() */
    /* receive ((int,string) id,(array) dynatree) */
    /* Return the children of the facets and insert in the array of the dynatree */
    /* Author: Eduardo */

    public function getTagRelDynatree($collection_id, $dynatree, $classCss) {
        $counter = 0;
        $get_tags = wp_get_object_terms($collection_id, 'socialdb_tag_type');

        if ($get_tags) {
            foreach ($get_tags as $tag) {
                $dynatree['children'][] = array('title' => $tag->name, 'key' => $tag->term_id . "_tag", 'addClass' => $classCss);
                //$dynatree['children'][] = array('title' => $tag->name.' ('. $this->count_items_related($tag->term_id).')', 'key' => $tag->term_id . "_tag", 'addClass' => $classCss);
                $counter++;
                if ($counter > $this->dynatree_number_items) {
                    $dynatree['children'][] = array('title' => __('See more', 'tainacan'), 'hideCheckbox' => true, 'key' => '_moreoptionstag', 'isLazy' => true, 'addClass' => 'more');
                    break;
                }
            }
        }
        return $dynatree;
    }

    /* function getLicensesDynatree() */
    /* receive ((int,string) id,(array) dynatree) */
    /* Return the children of the facets and insert in the array of the dynatree */
    /* Author: Eduardo */

    public function getLicensesDynatree($collection_id, $dynatree, $classCss) {
        $licenseModel = new LicenseModel;
        $repository_licenses = $licenseModel->get_repository_licenses($collection_id);
        $custom_licenses = $licenseModel->get_custom_licenses($collection_id);
        if (is_array($repository_licenses) && is_array($repository_licenses['licenses'])) {
            foreach ($repository_licenses['licenses'] as $license) {
                $dynatree['children'][] = array('title' => $license['nome'] . ' (' . $this->count_metadata_defaults_by_value('socialdb_license_id', $license['id'], $this->get_collection_category_root($collection_id)) . ')',
                            'key' => $license['id'] . "_license",
                            'addClass' => $classCss);
            }
        }
        if (is_array($custom_licenses) && is_array($custom_licenses['licenses'])) {
            foreach ($custom_licenses['licenses'] as $license) {
                $dynatree['children'][] = array('title' => $license['nome'] . ' (' . $this->count_metadata_defaults_by_value('socialdb_license_id', $license['id'], $this->get_collection_category_root($collection_id)) . ')'
                    , 'key' => $license['id'] . "_license",
                    'addClass' => $classCss);
            }
        }
        return $dynatree;
    }

    /* function getTypeDynatree() */
    /* receive ((int,string) id,(array) dynatree) */
    /* retorna os tipos de itens a ser filtrados no dynatree */
    /* Author: Eduardo */

    public function getTypeDynatree($collection_id, $dynatree, $classCss) {
//         $dynatree['children'][] = array('title' => __('Text', 'tainacan') . ' (' . $this->count_metadata_defaults_by_value('socialdb_object_dc_type', 'text', $this->get_collection_category_root($collection_id)) . ')',
//            'key' => "text_type",
//            'addClass' => $classCss);
        $dynatree['children'][] = array('title' => __('Text', 'tainacan'),
            'key' => "text_type",
            'addClass' => $classCss);
        $dynatree['children'][] = array('title' => __('Image', 'tainacan') ,
            'key' => "image_type",
            'addClass' => $classCss);
        $dynatree['children'][] = array('title' => __('Video', 'tainacan') ,
            'key' => "video_type",
            'addClass' => $classCss);
        $dynatree['children'][] = array('title' => __('PDF', 'tainacan') ,
            'key' => "pdf_type",
            'addClass' => $classCss);
        $dynatree['children'][] = array('title' => __('Audio', 'tainacan'),
            'key' => "audio_type",
            'addClass' => $classCss);
        $dynatree['children'][] = array('title' => __('Other', 'tainacan'),
            'key' => "other_type",
            'addClass' => $classCss);

        return $dynatree;
    }

    /* function getFormatDynatree() */
    /* receive ((int,string) id,(array) dynatree) */
    /* Return the children of the facets and insert in the array of the dynatree */
    /* Author: Eduardo */

    public function getFormatDynatree($collection_id, $dynatree, $classCss) {
//        $dynatree['children'][] = 
//           array('title' => __('Internal', 'tainacan') . ' (' . $this->count_metadata_defaults_by_value('socialdb_object_from', 'internal', $this->get_collection_category_root($collection_id)) . ')',
//            'key' => "internal_format",
//            'addClass' => $classCss);
//        $dynatree['children'][] = array('title' => __('External', 'tainacan') . ' (' . $this->count_metadata_defaults_by_value('socialdb_object_from', 'external', $this->get_collection_category_root($collection_id)) . ')',
//            'key' => "external_format",
//            'addClass' => $classCss);
        $dynatree['children'][] = 
           array('title' => __('Internal', 'tainacan'),
            'key' => "internal_format",
            'addClass' => $classCss);
        $dynatree['children'][] = array('title' => __('External', 'tainacan'),
            'key' => "external_format",
            'addClass' => $classCss);
        return $dynatree;
    }

    /* function getSourceDynatree() */
    /* receive ((int,string) id,(array) dynatree) */
    /* Return the children of the facets and insert in the array of the dynatree */
    /* Author: Eduardo */

    public function getSourceDynatree($collection_id, $dynatree, $classCss) {
        $metas = $this->get_source_values($collection_id);
        $counter = 0;
        if (count($metas) > 0) {
            foreach ($metas as $post_id => $meta_value) {
                $dynatree['children'][] = array('title' => $meta_value ,
                    'key' => $post_id . '_source',
                    'addClass' => $classCss);
                $counter++;
                if ($counter > 15) {
                    break;
                }
            }
        }
        return $dynatree;
    }

    /* function getChildrenDynatree() */
    /* receive ((int,string) id,(array) dynatree) */
    /* Return the children of the facets and insert in the array of the dynatree */
    /* Author: Eduardo */

    public function getChildrenDynatree($facet_id, $dynatree, $classCss = 'color4',$ordenation = 't.name ASC') {
        $counter = 0;
        $children = $this->getChildren($facet_id,$ordenation);
        if (count($children) > 0) {
            foreach ($children as $child) {
                $children_of_child = $this->getChildren($child->term_id,$ordenation);
                if (count($children_of_child) > 0 || (!empty($children_of_child) && $children_of_child)) {
                    $dynatree['children'][] = array('title' => $child->name, 'key' => $child->term_id, 'isLazy' => true, 'addClass' => $classCss);
                    //$dynatree['children'][] = array('title' => $child->name.' ('. $this->count_items_related($child->term_id).')', 'key' => $child->term_id, 'isLazy' => true, 'addClass' => $classCss);
                } else {
                    $dynatree['children'][] = array('title' => $child->name, 'key' => $child->term_id, 'addClass' => $classCss);
                    //$dynatree['children'][] = array('title' => $child->name.' ('. $this->count_items_related($child->term_id).')', 'key' => $child->term_id, 'addClass' => $classCss);
                }
                $counter++;
                if ($counter == $this->dynatree_number_items) {
                    $dynatree['children'][] = array('title' => __('See more', 'tainacan'), 'hideCheckbox' => true, 'key' => $facet_id . '_moreoptions', 'isLazy' => true, 'addClass' => 'more');
                    break;
                }
            }
        }
        $dynatree = $this->get_category_properties($dynatree, $facet_id, $classCss);
        return $dynatree;
    }

    /* function get_category_properties() */
    /* receive ((array) $dynatree,(int) $facet_id,(string)$classCss) */
    /* Retorna as propriedades de categoria de uma  categoria qualquer */
    /* Author: Eduardo */

    public function get_category_properties($dynatree, $facet_id, $classCss) {
        $propertyModel = new PropertyModel;
        if (!isset($facet_id)) {
            $properties = $propertyModel->get_property_object_facets($facet_id);
        }
        if ($properties) {
            foreach ($properties as $property) {
                $facet = get_term_by('id', $property['id'], 'socialdb_property_type');
                $classCss = 'category_property_img';
                if ($facet) {
                    $dynatree['children'][] = array('title' => ucfirst($facet->name), 'key' => "_facet_property_category" . $property['id'], 'isLazy' => true, 'data' => $url, 'expand' => false, 'hideCheckbox' => true, 'addClass' => $classCss);
                    //$dynatree[end(array_keys($dynatree))] = $this->getPropertyRelDynatree($property, $dynatree[end(array_keys($dynatree))], $classCss);
                }
            }
        }
        return $dynatree;
    }

    /* function getChildrenDynatree() */
    /* receive ((int,string) id,(array) dynatree) */
    /* Return the children of the facets and insert in the array of the dynatree */
    /* Author: Eduardo */

    public function getChildrenDynatreeExpanded($facet_id, $dynatree, $classCss = 'color4', $data = null) {
        $children = $this->getChildren($facet_id);
        if (count($children) > 0 && count($children) < 100) {
            foreach ($children as $child) {
                //nome da categoria
                if (isset($data['hide_count'])) {
                    $title = $child->name;
                } else {
                    $title = $child->name;
                    //$title = $child->name.' ('. $this->count_items_related($child->term_id).')';
                }
                $children_of_child = $this->getChildren($child->term_id);
                if (count($children_of_child) > 0 || (!empty($children_of_child) && $children_of_child)) {
                    $dynatree['children'][] = array('title' => $title, 'key' => $child->term_id, 'isLazy' => true, 'addClass' => $classCss);
                } else {
                    $dynatree['children'][] = array('title' => $title, 'key' => $child->term_id, 'addClass' => $classCss);
                }
            }
        } elseif (count($children) > 99) {
            $dynatree = insert_alphabet_dynatree($dynatree, $facet_id, $classCss);
        }
        return $dynatree;
    }

    /* function getChildrenDynatree() */
    /* receive ((int,string) id,(array) dynatree) */
    /* Return the children of the facets and insert in the array of the dynatree */
    /* Author: Eduardo */

    public function getChildrenDynatreeSingleEdit($facet_id, $dynatree, $classCss = 'color4',$hide_checkbox = true) {
        $counter = 0;
        $children = $this->getChildren($facet_id);
        if (count($children) > 0) {
            foreach ($children as $child) {
                $children_of_child = $this->getChildren($child->term_id);
                if (count($children_of_child) > 0 || (!empty($children_of_child) && $children_of_child)) {
                    $dynatree['children'][] = array('hideCheckbox' => $hide_checkbox, 'title' => $child->name, 'key' => $child->term_id, 'isLazy' => true, 'addClass' => $classCss);
                } else {
                    $dynatree['children'][] = array('hideCheckbox' => $hide_checkbox, 'title' => $child->name, 'key' => $child->term_id, 'addClass' => $classCss);
                }
                $counter++;
                if ($counter == 9) {
                    $dynatree['children'][] = array('title' => __('See more', 'tainacan'), 'hideCheckbox' => true, 'key' => $facet_id . '_moreoptions', 'isLazy' => true, 'addClass' => 'more');
                    break;
                }
            }
        }
        return $dynatree;
    }

    /**
     * @signature expandDynatree($data)
     * @param array $data Os dados vindos do formulario
     * @return array Com os dados do dynatree 
     * Metodo reponsavel em gerar a listagem do dynatree
     * Autor: Eduardo Humberto 
     */
    public function expandDynatree($data) {
        if (strpos($data['key'], "_moreoptions") !== false && strpos($data['key'], "_moreoptionstag") === false && strpos($data['key'], "_moreoptionsproperty") === false && strpos($data['key'], "_moreoptionsdataproperty") === false) {
            return $this->expand_categories_moreoptions($data['collection'], $data['key'], $data);
        } elseif (strpos($data['key'], "?alphabet=") !== false) {
            return $this->expand_alphabet_dynatree_category($data['collection'], $data['key'], $data);
        } elseif (strpos($data['key'], "_moreoptionstag") !== false) {
            return $this->expand_tag_moreoptions($data['collection']);
        } elseif (strpos($data['key'], "?alphabettag=") !== false) {
            return $this->expand_alphabet_dynatree_tag($data['key']);
        } elseif (strpos($data['key'], "_moreoptionsproperty") !== false) {
            return $this->expand_property_moreoptions($data['collection'], $data['key']);
        } elseif (strpos($data['key'], "?alphabetproperty=") !== false) {
            return $this->expand_alphabet_dynatree_property($data['key']);
        }
        //more option e alphabet prorperty data
        elseif (strpos($data['key'], "_moreoptionsdataproperty") !== false) {
            return $this->expand_dataproperty_moreoptions($data['collection'], $data['key']);
        } elseif (strpos($data['key'], "?alphabetdataproperty=") !== false) {
            return $this->expand_alphabet_dynatree_dataproperty($data['key']);
        }
        //end
        elseif (strpos($data['key'], "?alphabetcategoryproperty=") !== false) {
            return $this->expand_alphabet_dynatree_categoryproperty($data['key']);
        } elseif (strpos($data['key'], "_facet_property_category") !== false) {
            return $this->expand_property_categories($data['key'], $data['classCss']);
        } else {
            return $this->expand_categories($data['key'], $data['classCss'], $data);
        }
    }

    /**
     * @signature expand_categories($key, $classCss)
     * @param int $key O id da categoria a ser expandida
     * @param string $classCss A classe css que sera usada para mostrar o icone adequado
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    public function expand_categories($key, $classCss, $data) {
        $dynatree = array();
        if (isset($data['hide_checkbox'])) {
            $hide_checkbox = true;
        } else {
            $hide_checkbox = false;
        }
        $children = $this->getChildren($key);
        if (count($children) > 0 && count($children) < 50) {
            foreach ($children as $child) {
                if (isset($data['order'])) {
                    $children_of_child = $this->getChildren($child->term_id, 't.name ASC');
                } else {
                    $children_of_child = $this->getChildren($child->term_id);
                }
                //nome da categoria
                if (isset($data['hide_count'])) {
                    $title = $child->name;
                } else {
                    $title = $child->name;
                    //$title = $child->name.' ('. $this->count_items_related($child->term_id).')';
                }
                //monta o array do dynatree 
                if (count($children_of_child) > 0 || (!empty($children_of_child) && $children_of_child)) {
                    $dynatree[] = array('title' => $title, 'hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'isLazy' => true, 'addClass' => $classCss);
                    $dynatree[end(array_keys($dynatree))] = $this->getChildrenDynatreeExpanded($child->term_id, $dynatree[end(array_keys($dynatree))], $classCss, $data);
                } else {
                    $dynatree[] = array('title' => $title, 'hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'addClass' => $classCss);
                }
            }
        } elseif (count($children) > 49) {
            $dynatree = $this->insert_alphabet_dynatree($dynatree, $key, $classCss);
        }
        return $dynatree;
    }

    /**
     * @signature expand_categories($key, $classCss)
     * @param int $key O id da propriedade de categoria a ser expandida
     * @param string $classCss A classe css que sera usada para mostrar o icone adequado
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista da propriedade de categoria
     * Autor: Eduardo Humberto 
     */
    public function expand_property_categories($key, $classCss) {
        $dynatree = array();
        $id = str_replace('_facet_property_category', '', $key);
        $property = $this->get_all_property($id, true);
        $children = $this->getChildren($property['metas']['socialdb_property_object_category_id']);
        if (count($children) > 0 && count($children) < 20) {
            foreach ($children as $child) {
                $children_of_child = $this->getChildren($child->term_id);
                if (count($children_of_child) > 0 || (!empty($children_of_child) && $children_of_child)) {
                    $dynatree[] = array('title' => $child->name, 'key' => $child->term_id . "_" . $id, 'isLazy' => true, 'addClass' => $classCss);
                    //$dynatree[end(array_keys($dynatree))] = $this->getChildrenDynatreeExpanded($child->term_id, $dynatree[end(array_keys($dynatree))], $classCss);
                } else {
                    $dynatree[] = array('title' => $child->name, 'key' => $child->term_id . "_" . $id, 'addClass' => $classCss);
                }
            }
        } elseif (count($children) > 19) {
            $dynatree = $this->insert_alphabet_dynatree_categoryproperty($dynatree, $property['metas']['socialdb_property_object_category_id'], $classCss);
        }
        return $dynatree;
    }

    /**
     * @signature expand_categories_moreoptions($key, $classCss)
     * @param int $collection_id O id da colecao
     * @param string $key A key do dynatree
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    public function expand_categories_moreoptions($collection_id, $key, $data) {
        $counter = 0;
        $dynatree = array();
        if (isset($data['hide_checkbox'])) {
            $hide_checkbox = true;
        } else {
            $hide_checkbox = false;
        }
        $id = str_replace('_moreoptions', '', $key);
        $classCss = get_post_meta($collection_id, 'socialdb_collection_facet_' . $id . '_color', true);
        if (isset($data['order'])) {
            $children = $this->getChildren($id, 't.name ASC');
        } else {
            $children = $this->getChildren($id);
        }
        if (count($children) > 0 && count($children) < 50) {
            foreach ($children as $child) {
                if ($counter >= 9) {
                    $children_of_child = $this->getChildren($child->term_id);
                    if (count($children_of_child) > 0 || (!empty($children_of_child) && $children_of_child)) {
                        $dynatree[] = array('title' => $child->name, 'hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'isLazy' => true, 'addClass' => $classCss);
                        //$dynatree[] = array('title' => $child->name.' ('. $this->count_items_related($child->term_id).')','hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'isLazy' => true, 'addClass' => $classCss);
                        $dynatree[end(array_keys($dynatree))] = $this->getChildrenDynatreeExpanded($child->term_id, $dynatree[end(array_keys($dynatree))], $classCss);
                    } else {
                        $dynatree[] = array('title' => $child->name, 'hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'addClass' => $classCss);
                        //$dynatree[] = array('title' => $child->name.' ('. $this->count_items_related($child->term_id).')','hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'addClass' => $classCss);
                    }
                }
                $counter++;
            }
        } elseif (count($children) > 49) {
            $dynatree = $this->insert_alphabet_dynatree($dynatree, $id, $classCss);
        }
        return $dynatree;
    }

    /**
     * @signature expand_tag_moreoptions($collection_id)
     * @param int $collection_id O id da colecao onde estao as tags
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    public function expand_tag_moreoptions($collection_id) {
        $counter = 0;
        $dynatree = array();
        $get_tags = wp_get_object_terms($collection_id, 'socialdb_tag_type');

        if (count($get_tags) > 0 && count($get_tags) < 50) {
            foreach ($get_tags as $tag) {
                if ($counter > 9) {
                    $dynatree[] = array('title' => $tag->name, 'key' => $tag->term_id . "_tag", 'addClass' => 'tag_img');
                }
                $counter++;
            }
        } elseif (count($get_tags) > 49) {
            $dynatree = $this->insert_alphabet_dynatree_tag($dynatree, $collection_id);
        }
        return $dynatree;
    }

    /**
     * @signature expand_tag_moreoptions($collection_id)
     * @param int $collection_id O id da colecao onde estao as tags
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    public function expand_property_moreoptions($collection_id, $key) {
        $counter = 0;
        $dynatree = array();
        $array = explode(',', str_replace('_moreoptionsproperty', ',', $key));
        $objects = $this->get_category_root_posts($array[0]);
        $class = get_post_meta($collection_id, 'socialdb_collection_facet_' . $array[1] . '_color', true);
        if (count($objects) > 0 && count($objects) < 50) {
            foreach ($objects as $object) {
                if ($counter > 19) {
                    $dynatree[] = array('title' => $object->post_title, 'key' => $object->ID . "_" . $array[1], 'addClass' => ($class)?($class):'default');
                }
                $counter++;
            }
        } elseif (count($objects) > 49) {
            $dynatree = $this->insert_alphabet_dynatree_property($dynatree, $collection_id, $array[0], $array[1]);
        }
        return $dynatree;
    }

    /**
     * @signature expand_tag_moreoptions($collection_id)
     * @param int $collection_id O id da colecao onde estao as tags
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    public function expand_dataproperty_moreoptions($collection_id, $key) {
        $counter = 0;
        $dynatree = array();
        $array = explode(',', str_replace('_moreoptionsdataproperty', ',', $key));
        $metas = $this->get_property_data_values(trim($array[0]));
        $class = get_post_meta($collection_id, 'socialdb_collection_facet_' . $array[1] . '_color', true);
        if (count($metas) > 0 && count($metas) < 50) {
            foreach ($metas as $meta_id => $meta_value) {
                if ($counter > 9) {
                    $dynatree[] = array('title' => $meta_value, 'key' => $meta_id . "_" . $array[1] . '_datatext', 'addClass' => $class);
                }
                $counter++;
            }
        } elseif (count($metas) > 49) {
            $dynatree = $this->insert_alphabet_dynatree_dataproperty($dynatree, $collection_id, $array[0], $array[1]);
        }
        return $dynatree;
    }

    /**
     * @signature expand_alphabet_dynatree($key, $classCss)
     * @param int $collection_id O id da colecao do dynatree
     * @param string $key o key vindo do node do dynatree
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    function expand_alphabet_dynatree_category($collection_id, $key, $data) {
        $category_model = new CategoryModel;
        if (isset($data['hide_checkbox'])) {
            $hide_checkbox = true;
        } else {
            $hide_checkbox = false;
        }
        $string = str_replace('?alphabet=', ',', $key);
        $array = explode(',', $string);
        $children = $this->get_term_children_by_first_letter($array[0], $array[1]);
        $facet_id = $category_model->get_category_facet_parent($array[0], $collection_id);
        $classCss = $category_model->get_facet_class($facet_id, $collection_id);
        if (count($children) > 0) {
            foreach ($children as $child) {
                $children_of_child = $this->getChildren($child->term_id);
                if (count($children_of_child) > 0 || (!empty($children_of_child) && $children_of_child)) {
                    $dynatree[] = array('title' => $child->name, 'hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'isLazy' => true, 'addClass' => $classCss);
                    //$dynatree[] = array('title' => $child->name.' ('. $this->count_items_related($child->term_id).')','hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'isLazy' => true, 'addClass' => $classCss);
                } else {
                    $dynatree[] = array('title' => $child->name, 'hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'addClass' => $classCss);
                    //$dynatree[] = array('title' => $child->name.' ('. $this->count_items_related($child->term_id).')','hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'addClass' => $classCss);
                }
            }
        }
        return $dynatree;
    }

    /**
     * @signature expand_alphabet_dynatree($key, $classCss)
     * @param int $collection_id O id da colecao do dynatree
     * @param string $key o key vindo do node do dynatree
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    function expand_alphabet_dynatree_tag($key) {
        $string = str_replace('?alphabettag=', ',', $key);
        $array = explode(',', $string);
        $tags = $this->get_tags_by_first_letter($array[0], $array[1]);
        if (count($tags) > 0) {
            foreach ($tags as $tag) {
                $dynatree[] = array('title' => $tag->name, 'key' => $tag->term_id . "_tag", 'addClass' => 'tag_img');
            }
        }
        return $dynatree;
    }

    /**
     * @signature expand_alphabet_dynatree($key, $classCss)
     * @param int $collection_id O id da colecao do dynatree
     * @param string $key o key vindo do node do dynatree
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    function expand_alphabet_dynatree_property($key) {
        $string = str_replace('?alphabetproperty=', ',', $key);
        $array = explode(',', $string);
        $data = unserialize(str_replace('\\', '', $array[1]));
        $objects = $this->get_object_by_first_letter($data['category_root_id'], $data['letter']);
        $class = get_post_meta($array[0], 'socialdb_collection_facet_' . $data['property_id'] . '_color', true);
        if (count($objects) > 0) {
            foreach ($objects as $object) {
                $dynatree[] = array('title' => $object->post_title, 'key' => $object->ID . "_" . $data['property_id'], 'addClass' => ($class)?$class:'default');
            }
        }
        return $dynatree;
    }

    /**
     * @signature expand_alphabet_dynatree_dataproperty($key, $classCss)
     * @param int $collection_id O id da colecao do dynatree
     * @param string $key o key vindo do node do dynatree
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    function expand_alphabet_dynatree_dataproperty($key) {
        $string = str_replace('?alphabetdataproperty=', ',', $key);
        $array = explode(',', $string);
        $data = unserialize(str_replace('\\', '', $array[1]));
        $metas = $this->get_metas_by_first_letter($data['property_id'], $data['letter']);
        $class = get_post_meta($array[0], 'socialdb_collection_facet_' . $data['property_id'] . '_color', true);
        if (count($metas) > 0) {
            foreach ($metas as $meta_id => $meta_value) {
                $dynatree[] = array('title' => $meta_value, 'key' => $meta_id . "_" . $data['property_id'] . '_datatext', 'addClass' => $class);
            }
        }
        return $dynatree;
    }

    /**
     * @signature expand_alphabet_dynatree_categoryproperty($key, $classCss)
     * @param int $collection_id O id da colecao do dynatree
     * @param string $key o key vindo do node do dynatree
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expansao de um modelo alphabetico
     * Autor: Eduardo Humberto 
     */
    function expand_alphabet_dynatree_categoryproperty($key) {
        $string = str_replace('?alphabetcategoryproperty=', ',', $key);
        $array = explode(',', $string);
        $data = unserialize(str_replace('\\', '', $array[1]));
        $children = $this->get_term_children_by_first_letter($data['category_root_id'], $data['letter']);
        $classCss = $data['classCss'];
        if (count($children) > 0) {
            foreach ($children as $child) {
                $children_of_child = $this->getChildren($child->term_id);
                if (count($children_of_child) > 0 || (!empty($children_of_child) && $children_of_child)) {
                    $dynatree[] = array('title' => $child->name, 'key' => $child->term_id . '_' . $data['category_root_id'], 'isLazy' => true, 'addClass' => $classCss);
                } else {
                    $dynatree[] = array('title' => $child->name, 'key' => $child->term_id . '_' . $data['category_root_id'], 'addClass' => $classCss);
                }
            }
        }
        return $dynatree;
    }

    /**
     * @signature expand_categories_moreoptions($key, $classCss)
     * @param int $key O id da categoria a ser expandida
     * @param string $classCss A classe css que sera usada para mostrar o icone adequado
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    public function return_alphabet_array() {
        $alphas = range('a', 'z');
        $numbers = range('0', '9');
        $chars = array_merge($alphas, $numbers);
        $chars[] = '*';
        sort($chars);
        return $chars;
    }

    /**
     * @signature expand_categories_moreoptions($key, $classCss)
     * @param int $key O id da categoria a ser expandida
     * @param string $classCss A classe css que sera usada para mostrar o icone adequado
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    function insert_alphabet_dynatree($dynatree, $term_id, $classCss) {
        $arrayalphabet = $this->return_alphabet_array();
        foreach ($arrayalphabet as $letter) {
            if (count($this->get_term_children_by_first_letter($term_id, $letter)) > 0) {
                $data = array('title' => strtoupper($letter), 'key' => $term_id . '?alphabet=' . $letter, 'isFolder' => false, 'hideCheckbox' => true, 'expand' => false, 'isLazy' => true, 'data' => '', 'addClass' => 'more');
                $dynatree[] = $data;
            }
            //  var_dump($value,$valor,$value!==null||$value!=='NULL');
            // $dynatree[end(array_keys($dynatree))] = isLazyAlpha($term_id, $valor, $dynatree[end(array_keys($dynatree))]);
        }
        return $dynatree;
    }

    /**
     * @signature expand_categories_moreoptions($key, $classCss)
     * @param int $key O id da categoria a ser expandida
     * @param string $classCss A classe css que sera usada para mostrar o icone adequado
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    function insert_alphabet_dynatree_tag($dynatree, $collection_id) {
        $arrayalphabet = $this->return_alphabet_array();
        foreach ($arrayalphabet as $letter) {
            if (count($this->get_tags_by_first_letter($collection_id, $letter)) > 0) {
                $data = array('title' => strtoupper($letter), 'key' => $collection_id . '?alphabettag=' . $letter, 'isFolder' => false, 'hideCheckbox' => true, 'expand' => false, 'isLazy' => true, 'data' => '', 'addClass' => 'more');
                $dynatree[] = $data;
            }
            //  var_dump($value,$valor,$value!==null||$value!=='NULL');
            // $dynatree[end(array_keys($dynatree))] = isLazyAlpha($term_id, $valor, $dynatree[end(array_keys($dynatree))]);
        }
        return $dynatree;
    }

    /**
     * @signature expand_categories_moreoptions($key, $classCss)
     * @param int $key O id da categoria a ser expandida
     * @param string $classCss A classe css que sera usada para mostrar o icone adequado
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    function insert_alphabet_dynatree_property($dynatree, $collection_id, $category_root_id, $property_id) {
        $arrayalphabet = $this->return_alphabet_array();
        foreach ($arrayalphabet as $letter) {
            if (count($this->get_object_by_first_letter($category_root_id, $letter)) > 0) {
                $info = array('category_root_id' => $category_root_id, 'property_id' => $property_id, 'letter' => $letter);
                $data = array('title' => strtoupper($letter), 'key' => $collection_id . '?alphabetproperty=' . serialize($info), 'isFolder' => false, 'hideCheckbox' => true, 'expand' => false, 'isLazy' => true, 'data' => '', 'addClass' => 'more');
                $dynatree[] = $data;
            }
            //  var_dump($value,$valor,$value!==null||$value!=='NULL');
            // $dynatree[end(array_keys($dynatree))] = isLazyAlpha($term_id, $valor, $dynatree[end(array_keys($dynatree))]);
        }
        return $dynatree;
    }

    /**
     * @signature insert_alphabet_dynatree_dataproperty($key, $classCss)
     * @param int $key O id da categoria a ser expandida
     * @param string $classCss A classe css que sera usada para mostrar o icone adequado
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    function insert_alphabet_dynatree_dataproperty($dynatree, $collection_id, $category_root_id, $property_id) {
        $arrayalphabet = $this->return_alphabet_array();
        foreach ($arrayalphabet as $letter) {
            if (count($this->get_metas_by_first_letter($property_id, $letter)) > 0) {
                $info = array('property_id' => $property_id, 'letter' => $letter);
                $data = array('title' => strtoupper($letter), 'key' => $collection_id . '?alphabetdataproperty=' . serialize($info), 'isFolder' => false, 'hideCheckbox' => true, 'expand' => false, 'isLazy' => true, 'data' => '', 'addClass' => 'more');
                $dynatree[] = $data;
            }
            //  var_dump($value,$valor,$value!==null||$value!=='NULL');
            // $dynatree[end(array_keys($dynatree))] = isLazyAlpha($term_id, $valor, $dynatree[end(array_keys($dynatree))]);
        }
        return $dynatree;
    }

    /**
     * @signature expand_categories_moreoptions($key, $classCss)
     * @param int $key O id da categoria a ser expandida
     * @param string $classCss A classe css que sera usada para mostrar o icone adequado
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    function insert_alphabet_dynatree_categoryproperty($dynatree, $category_root_id, $classCss) {
        $arrayalphabet = $this->return_alphabet_array();
        foreach ($arrayalphabet as $letter) {
            if (count($this->get_term_children_by_first_letter($category_root_id, $letter)) > 0) {
                $info = array('category_root_id' => $category_root_id, 'classCss' => $classCss, 'letter' => $letter);
                $data = array('title' => strtoupper($letter), 'key' => $category_root_id . '?alphabetcategoryproperty=' . serialize($info), 'isFolder' => false, 'hideCheckbox' => true, 'expand' => false, 'isLazy' => true, 'data' => '', 'addClass' => 'more');
                $dynatree[] = $data;
            }
            //  var_dump($value,$valor,$value!==null||$value!=='NULL');
            // $dynatree[end(array_keys($dynatree))] = isLazyAlpha($term_id, $valor, $dynatree[end(array_keys($dynatree))]);
        }
        return $dynatree;
    }

    /**
     * @signature expand_categories_moreoptions($key, $classCss)
     * @param int $key O id da categoria a ser expandida
     * @param string $classCss A classe css que sera usada para mostrar o icone adequado
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    public function get_term_children_by_first_letter($term_id, $letter) {
        $results = array();
        $children = $this->getChildren($term_id);
        foreach ($children as $child) {
            if ($letter !== '*' && (substr($child->name, 0, 1) === strtoupper($letter) || substr($child->name, 0, 1) === strtolower($letter))) {
                $results[] = $child;
            }// $dynatree[end(array_keys($dynatree))] = isLazyAlpha($term_id, $valor, $dynatree[end(array_keys($dynatree))]);
        }
        return $results;
    }

    /**
     * @signature get_tags_by_first_letter($key, $classCss)
     * @param int $collection_id O id da colecao que ira buscar as tags
     * @param string $letter A letra que vai buscar os items
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    public function get_tags_by_first_letter($collection_id, $letter) {
        $results = array();
        $get_tags = wp_get_object_terms($collection_id, 'socialdb_tag_type');
        foreach ($get_tags as $tag) {
            if ($letter !== '*' && (substr($tag->name, 0, 1) === strtoupper($letter) || substr($tag->name, 0, 1) === strtolower($letter))) {
                $results[] = $tag;
            }// $dynatree[end(array_keys($dynatree))] = isLazyAlpha($term_id, $valor, $dynatree[end(array_keys($dynatree))]);
        }
        return $results;
    }

    /**
     * @signature get_tags_by_first_letter($key, $classCss)
     * @param int $collection_id O id da colecao que ira buscar as tags
     * @param string $letter A letra que vai buscar os items
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    public function get_object_by_first_letter($category_root_id, $letter) {
        $results = array();
        $objects = $this->get_category_root_posts($category_root_id);
        foreach ($objects as $object) {
            if ($letter !== '*' && (substr($object->post_title, 0, 1) === strtoupper($letter) || substr($object->post_title, 0, 1) === strtolower($letter))) {
                $results[] = $object;
            }// $dynatree[end(array_keys($dynatree))] = isLazyAlpha($term_id, $valor, $dynatree[end(array_keys($dynatree))]);
        }
        return $results;
    }

    /**
     * @signature get_metas_by_first_letter($key, $classCss)
     * @param int $property_id O id da colecao que ira buscar as tags
     * @param string $letter A letra que vai buscar os items
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    public function get_metas_by_first_letter($property_id, $letter) {
        $results = array();
        $metas = $this->get_property_data_values($property_id);
        foreach ($metas as $meta_id => $meta_value) {
            if ($letter !== '*' && (substr($meta_value, 0, 1) === strtoupper($letter) || substr($meta_value, 0, 1) === strtolower($letter))) {
                $results[$meta_id] = $metas[$meta_id];
            }// $dynatree[end(array_keys($dynatree))] = isLazyAlpha($term_id, $valor, $dynatree[end(array_keys($dynatree))]);
        }
        return $results;
    }

    ############################################################################
    /**
     * @signature set_container_classes($data)
     * @param array $data Os dados vindo da requisicao ajax
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */

    public function set_container_classes($data) {
        $facets_id = array_filter(array_unique(get_post_meta($data['collection_id'], 'socialdb_collection_facets')));
        foreach ($facets_id as $facet_id) {
            $widget = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_widget', true);
            $orientation = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet_id . '_orientation', true);
            $data['has_left']  = 'true';
            if ($widget == 'tree') {
                $tree_orientation = get_post_meta($data['collection_id'], 'socialdb_collection_facet_widget_tree_orientation', true);
                if ($tree_orientation == 'left-column' || $tree_orientation == '') {
                    $data['has_left'] = 'true';
                } else {
                    $data['has_right'] = 'true';
                }
            } else if ($orientation && !empty($orientation)) {
                if ($orientation == 'left-column') {
                    $data['has_left'] = 'true';
                } elseif ($orientation == 'right-column') {
                    $data['has_right'] = 'true';
                }
            }
        }
        return $data;
    }

    /**
     * @signature get_facets($data)
     * @param int $collection_id O id da colecao que ira buscar as tags
     * @param string De qual posicao sao as facetas
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    public function get_facets_visualization($collection_id) {
        ini_set('max_execution_time', '0');
        $facets = array();
        $facet = array();
        $facets_id = array_filter(array_unique(get_post_meta($collection_id, 'socialdb_collection_facets')));
        foreach ($facets_id as $facet_id) {
            $widget = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_widget', true);
            // $orientation = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_orientation', true);
            $orientation_tree = get_post_meta($collection_id, 'socialdb_collection_facet_widget_tree_orientation', true);
            //adicionar para um novo filtro para modulos
            if(has_filter('add_widgets_filters_in_view')){
                 $facet = apply_filters('add_widgets_filters_in_view',$facet_id);
                 if($facet){
                    $priority = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_priority', true);
                    $facets[(int) $facet['priority']] = $facet;
                    continue;
                 }
            }
            // se o widget for do tipo de nuvem
            // if ($widget == 'cloud' && $orientation && $orientation == $position) {
            if ( $widget == 'cloud' ) {
                $priority = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_priority', true);
                $facet['id'] = $facet_id;
                $facet['widget'] = $widget;
                $facet['json'] = str_replace("'", '', $this->get_json_tagcloud($facet_id, $collection_id));
                if ($facet_id == 'tag') {
                    $facet['name'] = 'Tag';
                } else {
                    $facet['name'] = get_term_by('id', $facet_id, 'socialdb_property_type')->name;
                }
                $facets[(int) $priority] = $facet;
            }
            //se for diferente de arvore e estiver nesta orientacao    
            // elseif ($widget != 'tree' && $orientation && $orientation == $position) {
            elseif ($widget != 'tree') {
                $facet['id'] = $facet_id;
                $facet['widget'] = $widget;
                $priority = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_priority', true);
                $property = get_term_by('id', $facet['id'], 'socialdb_property_type');
                //filtros padroes 
                if($facet['id'] == 'ranking_colaborations'){
                     $facet['name'] = __('Ranking of colaborations','tainacan');
                }elseif ($facet['id'] == 'notifications') {
                     $facet['name'] = __('Notifications','tainacan');
                }
                //se for um metadado como filtro
                else if ($property) {
                    $facet['name'] = $property->name;
                    if ($widget == 'range') {
                        $facet['options'] = unserialize(get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_range_options', true));
                        $property_model = new PropertyModel;
                        $all_data = $property_model->get_all_property($facet_id, true);
                        $facet['type'] = $all_data['type'];
                    } elseif ($widget == 'from_to' || $widget == 'stars') {
                        $property_model = new PropertyModel;
                        $all_data = $property_model->get_all_property($facet_id, true);
                        $facet['type'] = $all_data['type'];
                    }
                } else {
                    $property = get_term_by('id', $facet['id'], 'socialdb_category_type');
                    $facet['name'] = $property->name;

                    if ($widget == 'menu') {
                        $facet['html'] = $this->generate_menu_html_left($facet_id, $facet_id);
                        /*
                        if ($position == 'horizontal') {
                            $facet['html'] = $this->generate_menu_html($facet_id, $facet_id);
                        } elseif ($position == 'left-column') {
                            $facet['html'] = $this->generate_menu_html_left($facet_id, $facet_id);
                        } else {
                            $facet['html'] = $this->generate_menu_html_right($facet_id, $facet_id);
                        } */
                    } 
                    else {
                        $facet['categories'] = $this->getChildren($facet_id);
                    }
                }
                $facets[(int) $priority] = $facet;
            // } elseif ($widget == 'tree' && (($orientation_tree && $orientation_tree == $position ) || ($position == 'left-column' && $orientation_tree == ''))) {
            } elseif ($widget == 'tree' && (($orientation_tree ) || ( $orientation_tree == ''))) {
                $facet['id'] = $facet_id;
                $facet['widget'] = $widget;
                $priority = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_priority', true);
                if(isset($facets[(int) $priority])){
                    $cont = (int) $priority +1000;
                    while(isset($facets[$cont])){
                        $cont++;
                    }
                    $facets[$cont] = $facet;
                }else{
                    $facets[(int) $priority] = $facet; 
                }
                
               
            }
        }
  
        ksort($facets);
        return $facets;
    }

    /**
     * @signature get_facets($data)
     * @param int $collection_id O id da colecao que ira buscar as tags
     * @param string De qual posicao sao as facetas
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    public function has_tree($collection_id, $position) {
        $facets_id = array_filter(array_unique(get_post_meta($collection_id, 'socialdb_collection_facets')));
        $orientation = get_post_meta($collection_id, 'socialdb_collection_facet_widget_tree_orientation', true);
        foreach ($facets_id as $facet_id) {
            $widget = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_widget', true);
            if ($widget == 'tree' && ($orientation == $position || ($orientation == '' && $position == 'left-column'))) {
                return true;
            }
        }
        return false;
    }

    /** @signature get_facets($data)
     * @param int $collection_id O id da colecao que ira buscar as tags
     * @param string De qual posicao sao as facetas
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    public function get_data_tree($collection_id) {
        $tree = array();
        $widget = get_post_meta($collection_id, 'socialdb_collection_facet_widget_tree', true);
        if ($widget == '' || !$widget) {
            $tree['socialdb_collection_facet_widget_tree'] = 'dynatree';
        } else {
            $tree['socialdb_collection_facet_widget_tree'] = $widget;
        }
        return $tree;
    }

    /** @signature generate_menu_html($data)
     * @param int $facet_id O id da faceta que sera gerado o menu horizontal
     * @param string De qual posicao sao as facetas
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    public function generate_menu_html($facet_id, $root_facet) {
        ini_set('max_execution_time', '0');
        $html = '';
        $children = $this->getChildren($facet_id);
        foreach ($children as $child) {
            $subChildren = $this->getChildren($child->term_id);
            if ($subChildren && count($subChildren) > 0) {
                $html .= '<li class="dropdown-submenu">'
                        . '<a onclick="wpquery_filter_by_facet('. $child->term_id . ',' . $root_facet . ', \'wpquery_menu\')" class="dropdown-toggle"  data-toggle="dropdown" href="#">' . $child->name . '</a>'
                        . '<ul class="dropdown-menu">' . $this->generate_menu_html($child->term_id, $root_facet) . '</ul>'
                        . '</li>';
            } else {
                $html .= '<li >'
                        . '<a onclick="wpquery_filter_by_facet('. $child->term_id . ',' . $root_facet . ', \'wpquery_menu\')"  href="#">' . $child->name . '</a></li>';
            }
        }
        return $html;
    }

    /** @signature generate_menu_html($data)
     * @param int $facet_id O id da faceta que sera gerado o menu horizontal
     * @param string De qual posicao sao as facetas
     * @return array Com os dados do dynatree 
     * Metodo reponsavel gerar a lista apos expan
     * Autor: Eduardo Humberto 
     */
    public function generate_menu_html_left($facet_id, $root_facet) {
        $html = '';
        ini_set('max_execution_time', '0');
        $children = $this->getChildren($facet_id);
        foreach ($children as $child) {
            $subChildren = $this->getChildren($child->term_id);
            if ($subChildren && count($subChildren) > 0) {
                $html .= '<li class="dropdown-submenu">'
                    . '<a onclick="wpquery_filter_by_facet('. $child->term_id . ',' . $root_facet . ', \'wpquery_menu\')"  class="dropdown-toggle" data-toggle="dropdown" href="#">' . $child->name . '</a>'
                    . '<ul class="dropdown-menu">' . $this->generate_menu_html($child->term_id, $root_facet) . '</ul>'
                    . '</li>';
            } else {
                $html .= '<li ><a onclick="wpquery_filter_by_facet('. $child->term_id . ',' . $root_facet . ', \'wpquery_menu\')" href="#">' . $child->name . '</a></li>';
            }
        }
        return $html;
    }

    /**
     * function get_property_data_values()
     * @param int O id da propriedade
     * @return json com o id e o nome de cada objeto
     * @author Eduardo Humberto
     */
    public function get_property_data_values($id,$order = 'pm.meta_value') {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "
                        SELECT COUNT(pm.meta_value) AS count,pm.* FROM $wp_posts p
                        INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id    
                        WHERE pm.meta_key like 'socialdb_property_{$id}'
                        GROUP BY pm.meta_value
                        ORDER BY $order
                ";
        $result = $wpdb->get_results($query);
        if ($result) {
            foreach ($result as $object) {
                $json[$object->meta_id] = trim($object->meta_value);
            }
        }
        $json = array_filter(array_unique($json));
        return $json;
    }
    
    /**
     * function get_property_data_values()
     * @param int O id da propriedade
     * @return json com o id e o nome de cada objeto
     * @author Eduardo Humberto
     */
    public function get_property_object_values($id,$order = 'pm.meta_value') {
        global $wpdb;
        $json = [];
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "
                        SELECT COUNT(pm.meta_value) AS count,pm.* FROM $wp_posts p
                        INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id    
                        WHERE pm.meta_key like 'socialdb_property_{$id}'
                        GROUP BY pm.meta_value
                        ORDER BY $order
                ";
        $result = $wpdb->get_results($query);
        if ($result) {
            foreach ($result as $object) {
                $post = get_post(trim($object->meta_value));
                if($post)
                    $json[$object->meta_id] = $post;
            }
        }
        $json = array_filter(array_unique($json));
        return $json;
    }

    /**
     * function get_property_data_values_count()
     * @param int O id da propriedade
     * @return json com os metas e o total de vezes que TODOS os valores deste metadado eh utilizado pelos itens de uma colecao
     * @author Eduardo Humberto
     */
    public function get_property_data_values_count($id) {
        global $wpdb;
        $json = [];
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "
                        SELECT pm.meta_id,pm.meta_value,count(pm.meta_value) as counter FROM $wp_posts p
                        INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id    
                        WHERE pm.meta_key like 'socialdb_property_{$id}' 
                        GROUP BY pm.meta_value
                        ORDER BY counter
                ";
        $result = $wpdb->get_results($query);
        if ($result) {
            foreach ($result as $object) {
                $json[] = $object;
            }
        }
        return $json;
    }

    /**
     * function get_property_data_values_count()
     * @param int O id da propriedade
     * @return json com os metas
     * @author Eduardo Humberto
     */
    public function get_count_tag_relantioships($collection_id) {
        $json = [];
        $result = wp_get_object_terms($collection_id, 'socialdb_tag_type');
        if ($result && is_array($result)) {
            foreach ($result as $object) {
                $json[] = ['name' => $object->name, 'id' => $object->term_id, 'count' => $this->count_items_related($object->term_id)];
            }
        }
        return $json;
    }

    /**
     * function count_items_related()
     * @param int O id da propriedade
     * @return int O total de itens que este termo eh relacionado menos a categoria raiz da colecao
     * @author Eduardo Humberto
     */
    public function count_items_related($term_id) {
        global $wpdb;
        $wp_term_relationships = $wpdb->prefix . "term_relationships";
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $wp_posts = $wpdb->prefix . "posts";
        $query = "
                        SELECT *
                        FROM $wp_term_relationships tr 
                        INNER JOIN $wp_term_taxonomy t ON tr.term_taxonomy_id = t.term_taxonomy_id
                        INNER JOIN $wp_posts p ON tr.object_id = p.ID
                        WHERE t.term_id = $term_id AND p.post_status LIKE 'publish' AND p.post_type LIKE 'socialdb_object'
                ";
        $result = $wpdb->get_results($query);
        if ($result) {
            return count($result);
        } else {
            return 0;
        }
    }

    /**
     * function get_source_values()
     * @param int O id da colecao que sera buscado os valores das fonte
     * @return json com o id e o nome de cada objeto
     * @author Eduardo Humberto
     */
    public function get_source_values($collection_id) {
        $values = [];
        $posts = $this->get_collection_posts($collection_id);
        if (is_array($posts)) {
            foreach ($posts as $post) {
                $value = trim(get_post_meta($post->ID, 'socialdb_object_dc_source', true));
                if ($value && !empty($value) && !in_array($value, $values)) {
                    $values[$post->ID] = $value;
                }
            }
        }
        return $values;
    }

    /**
     * function get_json_tagcloud()
     * @param int $facet_id O id da faceta a ser montado o json do item
     * @param int $collection_id O id da colecao que sera buscado os itens
     * @return json com o id e o nome de cada objeto
     * @author Eduardo Humberto
     */
    public function get_json_tagcloud($facet_id, $collection_id) {
        $json = [];
        if ($facet_id == 'tag') {
            $tags = $this->get_count_tag_relantioships($collection_id);
            if (is_array($tags) && !empty($tags)) {
                foreach ($tags as $tag) {
                    if ($this->setWeight($tag['count']) > 0) {
                        $json[] = array('value' => $tag['id'], 'facet_id' => $facet_id, 'text' => $tag['name'], 'weight' => $this->setWeight($tag['count']));
                    }
                }
            }
        } else {
            $metas = $this->get_property_data_values_count($facet_id);
            if (is_array($metas) && !empty($metas)) {
                foreach ($metas as $meta) {
                    if ($this->setWeight($meta->counter)) {
                        $json[] = array('value' => $meta->meta_id, 'facet_id' => $facet_id, 'text' => $meta->meta_value, 'weight' => $this->setWeight($meta->counter));
                    }
                }
            }
        }
        return json_encode($json);
    }

    /**
     * function setWeight($count)
     * @param int $count O total de itens que o termo/metadado esta vinculado
     * @return int o tamanho da fonte que utilizado no tagcloud
     * @author Eduardo Humberto
     */
    public function setWeight($count) {
        if ($count >= 12) {
            return 12;
        } else {
            return absint($count);
        }
    }

    /**
     * function get_property_data_count_by_value()
     * @param int O id da propriedade
     * @param int O valor do metadado
     * @return int O total de itens que utiliza o valor do metadado
     * @author Eduardo Humberto
     */
    public function count_metadata_by_value($id, $value) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        if ($id == 'socialdb_object_dc_type' && $value == 'other') {
            $query = "
                        SELECT pm.meta_id,pm.meta_value FROM $wp_posts p
                        INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id    
                        WHERE pm.meta_key like '{$id}'  and pm.meta_value  NOT IN ('text','image','video','audio','pdf')
                ";
        } else {
            $query = "
                        SELECT pm.meta_id,pm.meta_value FROM $wp_posts p
                        INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id    
                        WHERE pm.meta_key like '{$id}'  and pm.meta_value LIKE '{$value}'
                ";
        }

        $result = $wpdb->get_results($query);
        if ($result) {
            return count($result);
        } else {
            return 0;
        }
    }

    /**
     * function count_metadata_defaults_by_value()
     * @param int O id da propriedade
     * @param int O valor do metadado
     * @return int O total de itens que utiliza o valor do metadado
     * @author Eduardo Humberto
     */
    public function count_metadata_defaults_by_value($id, $value, $collection_root_id) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $wp_term_relationships = $wpdb->prefix . "term_relationships";
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $query = "
                        SELECT distinct p.ID
                        FROM $wp_term_relationships tr 
                        INNER JOIN $wp_term_taxonomy t ON tr.term_taxonomy_id = t.term_taxonomy_id
                        INNER JOIN $wp_posts p ON tr.object_id = p.ID
                        INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id        
                        WHERE t.term_id = $collection_root_id AND p.post_status LIKE 'publish' AND p.post_type LIKE 'socialdb_object'
                ";
        if ($id == 'socialdb_object_dc_type' && $value == 'other') {
            $query .= " AND pm.meta_key like '{$id}'  and pm.meta_value  NOT IN ('text','image','video','audio','pdf')
                ";
        } else {
            $query .= " AND pm.meta_key like '{$id}'  and pm.meta_value LIKE '{$value}'
                ";
        }
        //var_dump($query);
        $result = $wpdb->get_results($query);
        if ($result) {
            return count($result);
        } else {
            return 0;
        }
    }
    
    /**
     * 
     * @param type $collection_id
     * @param type $facet_id
     * @return string
     */
    public function get_ordenation_facet($collection_id,$facet_id){
        $ordenation = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_ordenation', true);
        if($ordenation){
            return $ordenation;
        }else{
            return 'alphabetic';
        }
    }


    public function set_default_color_scheme($data) {
        $colorScheme = $data['color_scheme'];
        $data['cores'] = update_post_meta( $data['collection_id'], 'socialdb_collection_color_scheme', serialize($colorScheme) );
        $data['color_schemes'] = $colorScheme;

        return $data;
    }

    /**
     * function update_color_schemes
     * @param  array - with form's color scheme data
     * @return array - with updated  color scheme
     * @author Rodrigo Guimarães
     */
    public function update_color_schemes($data) {
        $colorScheme = $data['color_scheme'];
        $collection_id = $data['collection_id'];
        $data['cores'] = update_post_meta( $collection_id, 'socialdb_collection_color_scheme', serialize($colorScheme));
        $data['default_cs'] = update_post_meta($collection_id, 'socialdb_default_color_scheme', serialize($data['default_color']));

        return $data;
    }

    /**
     * function get_color_schemes
     * @param  array - collection id to fetch color scheme from
     * @return array - with collection's color schemes chosen by user
     * @author Rodrigo Guimarães
     */
    public function get_color_schemes($post_id) {
        return unserialize( get_post_meta($post_id, 'socialdb_collection_color_scheme', true) );
    }

    /**
     * function get_default_color_scheme
     * @param  array - collection id to fetch color scheme from
     * @return array - with collection's color scheme
     * @author Rodrigo Guimarães
     */
    public function get_default_color_scheme($post_id) {
        return unserialize(get_post_meta($post_id,'socialdb_default_color_scheme', true) );
    }

}
