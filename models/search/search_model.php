<?php

require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../property/property_model.php');
require_once(dirname(__FILE__) . '../../event/event_model.php');

class SearchModel extends Model {

/**
 *
 * @param array $data
 * @return array Com a confirmacao dos dados da submissao
 */
public function add($data) {
    if(is_numeric($data['search_add_facet'])&&!$this->verify_term_publicated($data['search_add_facet'])){
        $result['title'] = __('Warning','tainacan');
        $result['msg'] = __('Facet removed!','tainacan');
        $result['type'] = 'warning';
        $result['result'] = 'false';
        return $result;
    }
    $facets_id = array_filter(array_unique(get_post_meta($data['collection_id'], 'socialdb_collection_facets')));
    if (in_array($data['search_add_facet'], $facets_id)) {
        $result['title'] = __('Warning','tainacan');
        $result['msg'] = __('Facet already registered.','tainacan');
        $result['type'] = 'warning';
        $result['result'] = 'false';
    } else {
        $collection_id = $data['collection_id'];
        add_post_meta($collection_id, 'socialdb_collection_facets', $data['search_add_facet']);
        update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['search_add_facet'] . '_widget', $data['search_data_widget']);
        update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['search_add_facet'] . '_ordenation', $data['ordenation']);
        $orientation = ($orientation == '' ? 'left-column' : $orientation);

        if ($data['search_data_widget'] == 'tree') {
            update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['search_add_facet'] . '_color', $data['color_facet']);
            update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['search_add_facet'] . '_more_options', $data['enable_more_options']);
            // $orientation = get_post_meta($collection_id, 'socialdb_collection_facet_widget_tree_orientation', true);
            
        } elseif ($data['search_data_widget'] == 'range') {
            $options_range = array();

            if ( $data['counter_range'] ) {
                $max_range = $data['counter_range'];
            } else if ( $data['counter_data_range'] ){
                $max_range = $data['counter_data_range'];
            }
            for ($i = 0; $i <= $max_range; $i++):
                if ((isset($data['range_' . $i . '_1']) && $data['range_' . $i . '_1']!='') && (isset($data['range_' . $i . '_2']) && $data['range_' . $i . '_1']!='')) {
                    $options_range[] = array('value_1' => $data['range_' . $i . '_1'], 'value_2' => $data['range_' . $i . '_2']);
                }
            endfor;
            update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['search_add_facet'] . '_range_options', serialize($options_range));
            update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['search_add_facet'] . '_orientation', $data['search_data_orientation']);
        } elseif( $data['search_data_widget'] == 'menu' ) {
            update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['search_add_facet'] . '_menu_style', $data['select_menu_style']);
            update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['search_add_facet'] . '_orientation', $data['search_data_orientation']);
        } else {
            update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['search_add_facet'] . '_orientation', $data['search_data_orientation']);
        }

        //Pega as facetas cadastradas de acordo com a orientação escolhida para cadastrar com a ordenação correta.
        $priority = $this->get_the_priorities_facets($facets_id, $orientation, $collection_id);
        update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['search_add_facet'] . '_priority', $priority);

        $result['title'] = __('Success','tainacan');
        $result['msg'] = __('Facet successfully saved.','tainacan');
        $result['type'] = 'success';
        $result['result'] = 'true';
        $result['pipa'] = $max_range;
        $result['ue'] = $options_range;
    }
    return $result;
}

    public function get_the_priorities_facets(array $facets_ids, $orientation, $collection_id) {
        $orientation_tree = get_post_meta($collection_id, 'socialdb_collection_facet_widget_tree_orientation', true);
        $orientation_tree = ($orientation_tree == '' ? 'left-column' : $orientation_tree);
        $priority = array();

        foreach ($facets_ids as $facet_id) {
            $is_tree = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_widget', true);

            if ($is_tree == 'tree' && $orientation_tree == $orientation) {
                $priority[] = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_priority', true);
            } else {
                $same_ordenation = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_orientation', true);

                if ($same_ordenation == $orientation) {
                    $priority[] = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_priority', true);
                }
            }
        }

        if (empty($priority)) {
            $result = 1;
        } else {
            asort($priority);
            $result = ((int) array_pop($priority)) + 1;
        }

        return $result;
    }

    public function update($data) {
        $collection_id = $data['collection_id'];
        if ($data['property_id'] != '')
        {
            $fixed_id = get_term_by('slug', 'socialdb_property_fixed_tags', 'socialdb_property_type')->term_id;
            if($data['property_id']==$fixed_id){
                $data['property_id'] = 'tag';
            }
            $facets = get_post_meta($collection_id, 'socialdb_collection_facets');
            if($facets  &&  is_array($facets) && $data['property_id'] == 'tag' && (in_array($data['property_id'], $facets) || in_array($fixed_id, $facets)) ){
                //continue;
            }else if($facets  &&  is_array($facets) && !in_array($data['property_id'], $facets)){
                add_post_meta($collection_id, 'socialdb_collection_facets', $data['property_id']);
            }
            
            update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['property_id'] . '_widget', $data['search_data_widget']);
            update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['property_id'] . '_ordenation', $data['ordenation']);

            delete_post_meta($collection_id, 'socialdb_collection_facet_' . $data['property_id'] . '_color');
            delete_post_meta($collection_id, 'socialdb_collection_facet_' . $data['property_id'] . '_range_options');
            delete_post_meta($collection_id, 'socialdb_collection_facet_' . $data['property_id'] . '_orientation');

            if ($data['search_data_widget'] == 'tree') {
                update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['property_id'] . '_color', $data['color_facet']);
                update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['property_id'] . '_more_options', $data['enable_more_options']);
            } elseif ($data['search_data_widget'] == 'range') {
                $options_range = array();
                $max_range = $data['counter_range'];

                for ($i = 0; $i <= $max_range; $i++):
                    if ((isset($data['range_' . $i . '_1']) && $data['range_' . $i . '_1']!='') && (isset($data['range_' . $i . '_2']) &&$data['range_' . $i . '_2']!='')) {
                        $options_range[] = array('value_1' => $data['range_' . $i . '_1'], 'value_2' => $data['range_' . $i . '_2']);
                    }
                endfor;
                update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['property_id'] . '_range_options', serialize($options_range));
                update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['property_id'] . '_orientation', $data['search_data_orientation']);
            } elseif( $data['search_data_widget'] == 'menu' ) {
                update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['property_id'] . '_menu_style', $data['select_menu_style']);
                update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['property_id'] . '_orientation', $data['search_data_orientation']);
            } else {
                update_post_meta($collection_id, 'socialdb_collection_facet_' . $data['property_id'] . '_orientation', $data['search_data_orientation']);
            }

            $result['title'] = __('Success','tainacan');
            $result['msg'] = __('Facet successfully updated.','tainacan');
            $result['type'] = 'success';
            $result['result'] = 'true';
        } else {
            $result['title'] = __('Error','tainacan');
            $result['msg'] = __('Something went wrong. Please try again.','tainacan');
            $result['type'] = 'error';
            $result['result'] = 'false';
        }

        return $result;
    }

    public function delete($data) {
        if($data['facet_id']==='tag'){
            $data['facet_id'] = get_term_by('slug', 'socialdb_property_fixed_tags', 'socialdb_property_type')->term_id;
            delete_post_meta($data['collection_id'], 'socialdb_collection_facets', 'tag');
        }
        if($data['facet_id'])
            delete_post_meta($data['collection_id'], 'socialdb_collection_facets', $data['facet_id']);

        $result['title'] = __('Success','tainacan');
        $result['msg'] = __('Facet successfully deleted.','tainacan');
        $result['type'] = 'success';

        return $result;
    }


    /**
     * metodo que retorna os tipos de widgets a partir da faceta/metadado escolhido
     * @param array $data
     * @return array O array com os dados a ser montado na formulario de submissao da faceta
     */
    public function get_widgets($data) {
        $options = array();
        $propertyModel = new PropertyModel;
        $rankings = ['socialdb_property_ranking_like','socialdb_property_ranking_binary','socialdb_property_ranking_stars'];
        $type = $propertyModel->get_property_type($data['property_id']);
        $defaults_array = ['socialdb_object_from','socialdb_object_dc_type','socialdb_object_dc_source','socialdb_license_id'];
        $options['select']['0'] = __('Select...','tainacan');
        if($data['property_id'] == 'ranking_colaborations'){
             $options['select']['ranking_colaborations'] = __('Ranking of colaborations','tainacan');
        }elseif ($data['property_id'] == 'notifications') {
             $options['select']['notifications'] = __('Notifications','tainacan');
        }else if ($data['property_id'] == 'tag') {
            $options['select']['tree'] = __('Tree','tainacan');
            $options['select']['cloud'] = __('Tag Cloud','tainacan');
        } elseif ($type == 'socialdb_property_object') {
            $options['select']['multipleselect'] = __('Multiple Select','tainacan');
            $options['select']['tree'] = __('Tree','tainacan');
        } elseif ($type == 'socialdb_property_data') {
            if ($this->get_widget($data['property_id']) == 'numeric' || $this->get_widget($data['property_id']) == 'date') { 
                $options['select']['range'] = __('Range','tainacan');
                $options['select']['from_to'] = __('From/To','tainacan');
            } else {
                $options['select']['searchbox'] = __('Search box with autocomplete','tainacan');
                $options['select']['tree'] = __('Tree','tainacan');
                $options['select']['cloud'] = __('Tag Cloud','tainacan');
            }
            //} elseif ($propertyModel->get_property_type($data['property_id']) == 'socialdb_property_term') {
        }elseif(in_array($data['property_id'], $defaults_array)){
            //$options['select']['searchbox'] = __('Search box with autocomplete','tainacan');
            $options['select']['tree'] = __('Tree','tainacan');
        }elseif(in_array($type, $rankings)){
            if($type=='socialdb_property_ranking_stars'){
                 $options['select']['stars'] = __('Stars','tainacan');
            }else{
               $options['select']['range'] = __('Range','tainacan');
               $options['select']['from_to'] = __('From/To','tainacan'); 
            }
        } 
        else {
            $not_found = true;
            $options['select']['tree'] = __('Tree','tainacan');
            $options['select']['menu'] = __('Menu','tainacan');
            $options['select']['radio'] = __('Radio Button','tainacan');
            $options['select']['checkbox'] = __('Check Button','tainacan');
            $options['select']['selectbox'] = __('Select Box','tainacan');
            $options['select']['multipleselect'] = __('Multiple Select','tainacan');
        }
        //caso algum modulo queira modificar os widgets de um filtro ja existente
        //ou adicionar para um novo filtro 
        if(has_filter('add_widgets_filters')&&isset($not_found))
            $options['select'] = apply_filters ('add_widgets_filters', $data['property_id']);
        
        return $options;
    }
 /**
     * 
     * @param array $data
     * @return array O array com os dados a ser montado na formulario de submissao da faceta
     */
    public function get_widget($property_id) {
        $propertyModel = new PropertyModel;
        $data = $this->get_all_property($property_id, true);
        if($data['metas']['socialdb_property_data_widget']){
             return $data['metas']['socialdb_property_data_widget'];
        }else{
            return 'number';
        }
    }
     /**
     * 
     * @param array $data
     * @return array O array com os dados a ser montado na formulario de submissao da faceta
     */
    public function get_widget_tree_type($property_id) {
        $avoid = ['notifications','ranking_colaborations'];
        if($property_id=='tag'){
            return 'tag';
        }elseif(get_term_by('id', $property_id, 'socialdb_property_type')){
            return 'property_object';
        }elseif(!in_array($property_id, $avoid)){
             return 'property_term';
        }
        return '';
    }
    public function save_default_widget_tree($data) {
        if (update_post_meta($data['collection_id'], 'socialdb_collection_facet_widget_tree', $data['tree_type'])) {
            $result['title'] = __('Success','tainacan');
            $result['msg'] = __('Default widget tree changed successfully','tainacan');
            $result['type'] = 'success';
        } else {
            $result['title'] = __('Error!','tainacan');
            $result['msg'] = __('Something went wrong...','tainacan');
            $result['type'] = 'error';
        }
        return $result;
    }

    public function save_default_widget_tree_orientation($data) {
        if (update_post_meta($data['collection_id'], 'socialdb_collection_facet_widget_tree_orientation', $data['orientation_type'])) {
            $result['title'] = __('Success','tainacan');
            $result['msg'] = __('Default widget tree changed successfully','tainacan');
            $result['type'] = 'success';
        } else {
            $result['title'] = __('Error!','tainacan');
            $result['msg'] = __('Something went wrong...','tainacan');
            $result['type'] = 'error';
        }
        return $result;
    }

    public function get_saved_facets($collection_id, $is_repository = false, $child_id = 0) {
        $default_tree_orientation = get_post_meta($collection_id, 'socialdb_collection_facet_widget_tree_orientation', true);
        $default_tree_orientation = ($default_tree_orientation != '' ? $default_tree_orientation : 'left-column');
        $facets_id = array_filter(array_unique(get_post_meta($collection_id, 'socialdb_collection_facets')));
        $arrFacets = array();
        $prop = new PropertyModel();

        foreach ($facets_id as $facet_id) {
            $facet['id'] = $facet_id;
            $facet['widget'] = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_widget', true);
            if(has_filter('get_filter_name')&&apply_filters('get_filter_name', $facet['id']) ){
                $facet['nome'] = apply_filters('get_filter_name', $facet['id']);
                $facet['orientation'] = $default_tree_orientation;
            }
            $facet_property = get_term_by('id', $facet['id'], 'socialdb_property_type');

            $facet['prop'] = $prop->get_property_type( $facet_property->term_id );
            //buscando os dados de cada tipo
            if ($facet['id'] == 'tag' || ($facet_property->slug && $facet_property->slug == 'socialdb_property_fixed_tags') ) {
                $facet['id'] = 'tag';
                $facet['nome'] = 'Tag';
                //$facet['widget'] = 'tree';
                $facet['orientation'] = $default_tree_orientation;
            }else if ($facet['id'] == 'ranking_colaborations') {
                $facet['nome'] = __('Colaboration Ranking','tainacan');
                $facet['orientation'] = $default_tree_orientation;
            }else if ($facet['id'] == 'notifications') {
                $facet['nome'] = __('Notifications','tainacan');
                $facet['orientation'] = $default_tree_orientation;
            }else if ($facet['id'] == 'socialdb_object_from') {
                $facet['nome'] = __('Format','tainacan');
                $facet['widget'] = 'tree';
                $facet['orientation'] = $default_tree_orientation;
            }else if ($facet['id'] == 'socialdb_object_dc_type') {
                $facet['nome'] = __('Type','tainacan');
                $facet['widget'] = 'tree';
                $facet['orientation'] = $default_tree_orientation;
            }else if ($facet['id'] == 'socialdb_object_dc_source') {
                $facet['nome'] = __('Source','tainacan');
                $facet['widget'] = 'tree';
                $facet['orientation'] = $default_tree_orientation;
            } else if ($facet['id'] == 'socialdb_license_id') {
                $facet['nome'] = __('License','tainacan');
                $facet['widget'] = 'tree';
                $facet['orientation'] = $default_tree_orientation;
            } else {
                $property = get_term_by('id', $facet['id'], 'socialdb_property_type');
                if ($facet['widget'] == 'tree') {
                    $facet['orientation'] = $default_tree_orientation;
                    $facet['more_options'] = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_more_options', true);
                    $facet['nome'] = $property->name;
                    $property = get_term_by('id', $facet['id'], 'socialdb_category_type');
                    if($property){
                        if(in_array($property->slug, $this->fixed_slugs)){
                            $labels_collection =  get_post_meta($collection_id, 'socialdb_collection_fixed_properties_labels', true);
                            if ($labels_collection):
                                $array = unserialize($labels_collection);
                                $property->name = (isset($array[$property->term_id])) ? $array[$property->term_id] : $property->name;
                            endif;
                        }
                        $facet['nome'] = $property->name;
                    }
                } else if( $facet['widget'] == 'menu' ) {
                    $property = get_term_by('id', $facet['id'], 'socialdb_category_type');
                    if($property){
                        $facet['nome'] = $property->name;
                    }
                    $facet['orientation'] = $default_tree_orientation;
                } else {
                    $facet['orientation'] = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet['id'] . '_orientation', true);
                    if ($property) {
                        $facet['nome'] = $property->name;
                    } elseif(is_numeric($facet['id'])) {
                        $category = get_term_by('id', $facet['id'], 'socialdb_category_type');
                        $facet['nome'] = $category->name;
                        $facet['more_options'] = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet['id'] . '_more_options', true);
                    }
                }
            }
            if($is_repository)
            {
                $facet['priority'] = get_post_meta($child_id, 'socialdb_collection_facet_' . $facet_id . '_priority', true);
            }

            if(!$is_repository || !$facet['priority'])
            {
                $facet['priority'] = get_post_meta($collection_id, 'socialdb_collection_facet_' . $facet_id . '_priority', true);
            }


            $arrFacets[] = $facet;
        }

        usort($arrFacets, 'compare_priority'); // sort by priority
        
        return $arrFacets;
    }

    function get_widget_edit($data) {
        $data['widget'] = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $data['property_id'] . '_widget', true);
        if ($data['widget'] == 'tree') {
            $data['class_color'] = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $data['property_id'] . '_color', true);
        } elseif ($data['widget'] == 'range') {
            $data['range_options'] = unserialize(get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $data['property_id'] . '_range_options', true));
            $data['orientation'] = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $data['property_id'] . '_orientation', true);
        } elseif( $data['widget'] == 'menu' ) {
            $data['chosen_menu_style_id'] = get_post_meta( $data['collection_id'], "socialdb_collection_facet_" . $data['property_id'] . '_menu_style', true );
        } else {
            $data['orientation'] = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $data['property_id'] . '_orientation', true);
        }
        return $data;
    }

    function get_range_options($data) {
        $data['range_options'] = unserialize(get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $data['property_id'] . '_range_options', true));
        return $data;
    }

    function update_ordenation($data) {
        $post_id = $data['collection_id'];
        update_post_meta($post_id, 'socialdb_collection_table_metas', base64_encode(serialize($data['table_meta'])) );
        update_post_meta($post_id, 'socialdb_collection_list_mode', $data['collection_list_mode']);
        update_post_meta($post_id, 'socialdb_collection_slideshow_time', $data['slideshow_time']);
        update_post_meta($post_id, 'socialdb_collection_ordenation_form', $data['socialdb_collection_ordenation_form']);
        update_post_meta($post_id, 'socialdb_collection_visualization_page_category', $data['socialdb_collection_visualization_page_category']);

        if (isset($data['prox_mode'])) {
            update_post_meta($post_id, 'socialdb_collection_use_prox_mode', $data['prox_mode']);
            update_post_meta($post_id, 'socialdb_collection_location_meta', $data['location']);
        } else {
            update_post_meta($post_id, 'socialdb_collection_use_prox_mode', 'false');
        }
        //habilitate
        if(isset($data['habilitateMedia']) && $data['habilitateMedia'] == 'true'){
             update_post_meta($post_id, 'socialdb_collection_habilitate_media', 'true');
        }else{
             update_post_meta($post_id, 'socialdb_collection_habilitate_media', 'false');
        }
        //habilitateItem
        if(isset($data['habilitateItemMedia']) && $data['habilitateItemMedia'] == 'true'){
             update_post_meta($post_id, 'socialdb_collection_item_habilitate_media', 'true');
        }else{
             update_post_meta($post_id, 'socialdb_collection_item_habilitate_media', 'false');
        }

        update_post_meta($post_id, 'socialdb_collection_latitude_meta', $data['latitude']);
        update_post_meta($post_id, 'socialdb_collection_longitude_meta', $data['longitude']);
        update_post_meta($post_id, 'socialdb_collection_default_ordering', $data['collection_order']);
        update_post_meta($post_id, 'socialdb_collection_submission_visualization', $data['socialdb_collection_submission_visualization']);
        update_post_meta($post_id, 'socialdb_collection_item_visualization', $data['socialdb_collection_item_visualization']);
        update_post_meta($post_id, 'socialdb_collection_add_item', serialize($data['col_add_item']));

        $colorScheme = $data['color_scheme'];
        $collection_id = $data['collection_id'];
        $data['cores'] = update_post_meta( $collection_id, 'socialdb_collection_color_scheme', serialize($colorScheme));
        $data['default_cs'] = update_post_meta($collection_id, 'socialdb_default_color_scheme', serialize($data['default_color']));

        $result['title'] = __('Success','tainacan');
        $result['msg'] = __('Ordenation changed successfully','tainacan');
        $result['type'] = 'success';

        return $result;
    }

    function save_new_priority($data) {
        if(isset($data['arrFacets'])) {
            foreach ($data['arrFacets'] as $facet)
            {
                if($facet[0] == get_term_by('slug', 'socialdb_property_fixed_tags', 'socialdb_property_type')->term_id)
                {
                    $facet[0] = 'tag';
                }

                update_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet[0] . '_priority', $facet[1]);
            }
        }
        return true;
    }
    // ordenation save ordination
    /**
     * function remove_property_ordenation($property_id)
     */
    public function remove_property_ordenation($data) {
        $is_repository_properties = false;
        if($data['property_id']&& is_array($data['property_id'])){
            foreach ($data['property_id'] as $property_id) {
                $created_category = get_term_meta($property_id, 'socialdb_property_created_category', true);
                if($created_category!=$this->get_category_root_of($data['collection_id'])){
                     $is_repository_properties = true;
                }else{
                     update_term_meta($property_id, 'socialdb_property_data_column_ordenation','false');
                }
             }
        }
        
        if(!$is_repository_properties){
            $data['title'] = __('Success','tainacan');
            $data['msg'] = __('The property was removed as property ordination','tainacan');
            $data['type'] = 'success';
        }
        else{
            $data['title'] = __('Attention','tainacan');
            $data['msg'] = __('You can not remove ordination owned by another collecion','tainacan');
            $data['type'] = 'info';
        }
        
        return json_encode($data);
    }
    
    public function add_property_ordenation($data) {
         if($data['property_id']&&  is_array($data['property_id'])){
            foreach ($data['property_id'] as $property_id) {
                update_term_meta($property_id, 'socialdb_property_data_column_ordenation','true');
            }
        }$data['title'] = __('Success','tainacan');
        $data['msg'] = __('The property was added as property ordination','tainacan');
        $data['type'] = 'success';
        return json_encode($data);
    }
    /**
     * 
     */
    public function verify_term_publicated($term_id) {
        if(get_term_by('id', $term_id, 'socialdb_property_type')){
            return true;
        }else if(get_term_by('id', $term_id, 'socialdb_category_type')){
            return true;
        }else if(get_term_by('id', $term_id, 'socialdb_tag_type')){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 
     */
    public function get_events_data($data) {
        $collection_events = EventModel::list_all_events_terms($data);
        $userModel = new UserModel();
        if (!empty($collection_events)) {
            foreach ($collection_events as $event) {
                $info['state'] = get_post_meta($event->ID, 'socialdb_event_confirmed', true);
                $info['name'] = $event->post_title;
                $info['date'] = get_post_meta($event->ID, 'socialdb_event_create_date', true);
                $info['type'] = EventModel::get_type($event);
                $author = get_post_meta($event->ID, 'socialdb_event_user_id', true);
                $info['author'] = $userModel->get_user($author)['name'];
                $info['id'] = $event->ID;
                $data['events'][] = $info;
            }
        }
        return $data;
    }
    
    public function get_slideshow_time($data) {
        return get_post_meta($data['collection_id'], 'socialdb_collection_slideshow_time', true);        
    }

}