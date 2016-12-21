<?php

include_once (dirname(__FILE__) .'/../../../../../wp-config.php');
    include_once (dirname(__FILE__) .'/../../../../../wp-load.php');
    include_once (dirname(__FILE__) .'/../../../../../wp-includes/wp-db.php');
include_once (dirname(__FILE__) . '../../collection/collection_model.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../property/property_model.php');
require_once(dirname(__FILE__) . '../../search/search_model.php');

/**
 * @clas-name	RankingModel 
 * @description	Classe repsonsável por criar rankings nas coleções.
 * @author     	
 * @version  1.0
 */
class RankingModel extends Model {

    public $property_model;

    /**
     * function RankingModel()
     * Metodo responsavel por instanciar o model property
     * @autor: Marco Tulio 
     */
    public function __construct() {
        $this->property_model = new PropertyModel();
    }

    /**
     * function add($data)
     * Responsável por adicionar um ranking
     * @autor: Marco Tulio 
     */
    public function add($data) {
        $type = $this->get_ranking_parent($data);
        $data['widget'] = $data['search_data_widget'];

        if (!empty($data['ranking_name'])) {
            $new_ranking = wp_insert_term($data['ranking_name'], 'socialdb_property_type', array('parent' => $type->term_id,
                'slug' => $this->generate_slug($data['ranking_name'], $data['collection_id'])));
            $ranking_id = $new_ranking['term_id'];
            $data['new_ranking_id'] = $ranking_id;
        }

        if (!is_wp_error($new_ranking) && $ranking_id) {
            $this->add_property_position_ordenation($data['collection_id'], $ranking_id);
            $result[] = update_term_meta($ranking_id, 'socialdb_property_created_category', $this->get_category_root_of($data['collection_id']));
            instantiate_metas($ranking_id, $type->name, 'socialdb_property_type', true);
            $result[] = $this->vinculate_property($this->get_category_root_of($data['collection_id']), $ranking_id);
            //possivelmente um problema
            $this->vinculate_objects_with_property($ranking_id, $data['collection_id'], $this->get_category_root_of($data['collection_id']));
            $this->insert_properties_hierarchy($this->get_category_root_of($data['collection_id']), $ranking_id);
            $data['success'] = 'true';
            $data['result'] = $result;
        } else {
            $data['success'] = 'false';
            if(trim($data['ranking_name'])==''){
                $data['msg'] = __('Ranking name is empty','tainacan');
            }else {
                $data['msg'] = __('This ranking already exists!','tainacan');
            }
        }

        if ( $data['ranking_use_filter'] === "use_filter" && isset($data['search_data_widget']) ) {
            $ranking_facet = [
                'search_add_facet' => $ranking_id,
                'search_data_widget' => $data['search_data_widget'],
                'collection_id' => $data['collection_id']
            ];
            $search_model = new SearchModel();
            $search_model->add( $ranking_facet );
        }

        return json_encode($data);
    }

    /**
     * function create_santard_vote($data)
     * Responsável por adicionar um ranking padrão
     * @autor: Marcus Bruno 
     */
    public function create_santard_vote($data) {
        $type = $this->get_type_by_name('socialdb_property_ranking_like');

        $new_ranking = wp_insert_term(__('Best Items','tainacan'), 'socialdb_property_type', array('parent' => $type->term_id,
            'slug' => $this->generate_slug(__('Best Items','tainacan'), $data['collection_id'])));
        
        $category_parent_root_id = get_term_by('id', $this->get_category_root_of($data['collection_id']), 'socialdb_category_type')->parent ;
        if (!is_wp_error($new_ranking) && $new_ranking['term_id']&&$this->is_repository_property($category_parent_root_id)) {// 
            $result[] = update_term_meta($new_ranking['term_id'], 'socialdb_property_created_category', $this->get_category_root_of($data['collection_id']));
            instantiate_metas($new_ranking['term_id'], $type->name, 'socialdb_property_type', true);
            $result[] = $this->vinculate_property($this->get_category_root_of($data['collection_id']), $new_ranking['term_id']);
            //possivelmente um problema
            $this->vinculate_objects_with_property($new_ranking['term_id'], $data['collection_id'], $this->get_category_root_of($data['collection_id']));
        }

        return true;
    }

    /**
     * function edit($data)
     * Responsável por editar um ranking
     * @autor: Marco Tulio 
     */
    public function edit($data) {
        $type = $this->get_ranking_parent($data);        
        if (!empty(trim($data['ranking_name']))) {         
            $is_new = $this->verify_property($data['ranking_name'], $data['collection_id']);
            
            if($is_new){
                $data['msg'] = __('There is another property with this name!','tainacan');
                $data['success'] = 'false';
                return json_encode($data);
            }
            $edit_ranking = wp_update_term($data['ranking_id'], 'socialdb_property_type', array('name' => $data['ranking_name']) );
            $data['edited'] = $edit_ranking;

            return json_encode($data);

            if ($edit_ranking['term_id']) {
                $data['success'] = 'true';
            } else {
                $data['success'] = 'false';
            }
            
        }
        return json_encode($data);
    }
    
     public function verify_property($name,$collection_id) {
        $array = socialdb_term_exists_by_slug($this->generate_slug($name,$collection_id), 'socialdb_property_type');
        if (!isset($array['term_id'])) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * function delete()
     * Responsável por remover um ranking
     * @autor: Marco Tulio 
     */
    public function delete($data) {
        $categogy_root_of_collection_id = $this->get_category_root_of($data['collection_id']);
       if (isset($data['ranking_delete_id']) && delete_term_meta($categogy_root_of_collection_id, 'socialdb_category_property_id', $data['ranking_delete_id']) && $this->delete_property_meta_data($data['ranking_delete_id']) && wp_delete_term($data['ranking_delete_id'], 'socialdb_property_type')) {
            $this->remove_property_position_ordenation($data['collection_id'],$data['ranking_delete_id']); 
           $data['success'] = 'true';
            delete_post_meta($data['collection_id'], 'socialdb_collection_facets',$data['ranking_delete_id']);
            // se for ordenacao padrao do repositorio
            if($data['ranking_delete_id']==get_post_meta($data['collection_id'], 'socialdb_collection_default_ordering', true)){
                 $recent_property = get_term_by('slug', 'socialdb_ordenation_recent', 'socialdb_property_type');
                 update_post_meta($data['collection_id'], 'socialdb_collection_default_ordering', $recent_property->term_id);
                 update_post_meta($data['collection_id'], 'socialdb_collection_ordenation_form', 'desc');  
            }
             $data['success'] = 'true';
        } else {
            $data['success'] = 'false';
        }
        return json_encode($data);
    }

    /**
     * function list_ranking($data)
     * Responsável por listar os rankings ja cadastrados
     * @autor: Marco Tulio 
     */
    public function list_ranking($data) {
        $category_root = $this->get_category_root_of($data['collection_id']);
        $category_root_id = $this->get_category_root_id();
        //$all_properties_id = get_term_meta($category_root, 'socialdb_category_property_id');
        $all_properties_id = $this->get_parent_properties($category_root, [], $category_root_id);
        $data['category_root'] = $category_root; // coloco no array que sera utilizado na view
        $all_properties_id = array_unique($all_properties_id);
        foreach ($all_properties_id as $property_id) {// varro todas propriedades
            $type = $this->property_model->get_property_type($property_id); // pego o tipo da propriedade
            $all_data = $this->property_model->get_all_property($property_id,true); // pego todos os dados possiveis da propriedade
            $all_data['range_options'] = unserialize(get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $all_data['id'] . '_range_options', true));

            if (($type == 'socialdb_property_ranking_like') || ($type == 'socialdb_property_ranking_binary') || ($type == 'socialdb_property_ranking_stars')) {// pego o tipo
                $data['rankings'][] = $all_data;
                $data['no_properties'] = false;
            }
        }
        if (!isset($data['no_properties'])) {
            $data['no_properties'] = true;
        }

        return json_encode($data);
    }

    /**
     * function list_ranking_object($data)
     * Responsável por listar os rankings com seus valores para os objetos
     * @autor: Marco Tulio 
     */
    public function list_ranking_object($data) {
        $category_root = $this->get_category_root_of($data['collection_id']);
        //$all_properties_id = get_term_meta($category_root, 'socialdb_category_property_id');
        $all_properties_id = $this->get_parent_properties($category_root, [],$category_root);
        foreach ($all_properties_id as $property_id) {// varro todas propriedades
            $type = $this->property_model->get_property_type($property_id); // pego o tipo da propriedade
            $all_data = $this->property_model->get_all_property($property_id); // pego todos os dados possiveis da propriedade
            if (($type == 'socialdb_property_ranking_like')) {// pego o tipo
                $all_data['value'] = get_post_meta($data['object_id'], 'socialdb_property_' . $all_data['id'], true);
                $all_data['count'] = $this->count_votes($all_data['id'], $data['object_id']);
                $data['likes'][] = $all_data;
                $data['no_properties'] = false;
            } elseif (($type == 'socialdb_property_ranking_binary')) {// pego o tipo
                $count = $this->count_votes_binary($all_data['id'], $data['object_id']);
                $all_data['count_up'] = $count['count_up'];
                $all_data['count_down'] = $count['count_down'];
                $all_data['value'] = $count['count_up'] - $count['count_down'];
                $data['binaries'][] = $all_data;
                $data['no_properties'] = false;
            }
            if (($type == 'socialdb_property_ranking_stars')) {// pego o tipo
                $all_data['value'] = get_post_meta($data['object_id'], 'socialdb_property_' . $all_data['id'], true);
                $all_data['count'] = $this->count_votes($all_data['id'], $data['object_id']);
                $data['stars'][] = $all_data;
                $data['stars_id'][] = $all_data['id'];
                $data['no_properties'] = false;
            }
        }
        if (!isset($data['no_properties'])) {
            $data['no_properties'] = true;
        }
        if (isset($data['stars_id'])) {
            $data['stars_id'] = implode(',', $data['stars_id']);
        }

        return $data;
    }
     /**
     * @date 28/09/2015
     * function list_value_ordenation($data)
     * Responsável em retornar o ranking ou o valor da propriedade selecionada na ordenacao
     * @autor: Eduardo
     */
    public function list_value_ordenation($data) {
        //$all_properties_id = get_term_meta($category_root, 'socialdb_category_property_id');
        $type = $this->property_model->get_property_type($data['ordenation_id']); // pego o tipo da propriedade
        $all_data = $this->property_model->get_all_property($data['ordenation_id']); // pego todos os dados possiveis da propriedade
        if (($type == 'socialdb_property_ranking_like')) {// pego o tipo
            $all_data['value'] = get_post_meta($data['object_id'], 'socialdb_property_' . $all_data['id'], true);
            $all_data['count'] = $this->count_votes($all_data['id'], $data['object_id']);
            $data['likes'][] = $all_data;
            $data['no_properties'] = false;
        } elseif (($type == 'socialdb_property_ranking_binary')) {// pego o tipo
            $count = $this->count_votes_binary($all_data['id'], $data['object_id']);
            $all_data['count_up'] = $count['count_up'];
            $all_data['count_down'] = $count['count_down'];
            $all_data['value'] = $count['count_up'] - $count['count_down'];
            $data['binaries'][] = $all_data;
            $data['no_properties'] = false;
        }
        elseif (($type == 'socialdb_property_ranking_stars')) {// pego o tipo
            $all_data['value'] = get_post_meta($data['object_id'], 'socialdb_property_' . $all_data['id'], true);
            $all_data['count'] = $this->count_votes($all_data['id'], $data['object_id']);
            $data['stars'][] = $all_data;
            $data['stars_id'][] = $all_data['id'];
            $data['no_properties'] = false;
        }elseif($type == 'socialdb_property_data'&&  get_term_by('slug', 'socialdb_ordenation_recent', 'socialdb_property_type')->term_id!=$data['ordenation_id']){
            $all_data['value'] = get_post_meta($data['object_id'], 'socialdb_property_' . $all_data['id'], true);
            $data['property_data']= $all_data;
            $data['no_properties'] = false;
        }elseif($data['ordenation_id']=='socialdb_object_dc_type'){
            $type = get_post_meta($data['object_id'], 'socialdb_object_dc_type', true);
            if($type=='text'){
                $all_data['value'] = __('Text','tainacan');
            }elseif($type=='image'){
                $all_data['value'] = __('Image','tainacan');
            }elseif($type=='video'){
                $all_data['value'] = __('Video','tainacan');
            }elseif($type=='pdf'){
                $all_data['value'] = __('PDF','tainacan');
            }elseif($type=='audio'){
                $all_data['value'] = __('Audio','tainacan');
            }else{
                $all_data['value'] = __('Other','tainacan');
            }
            $data['type']= $all_data;
            $data['no_properties'] = false;
        }elseif($data['ordenation_id']=='socialdb_object_from'){
            $format = get_post_meta($data['object_id'], 'socialdb_object_from', true);
            if($format=='internal'){
                $all_data['value'] = __('Internal','tainacan');
            }else{
                $all_data['value'] = __('External','tainacan');
            }
            $data['format']= $all_data;
            $data['no_properties'] = false;
        }elseif($data['ordenation_id']=='comment_count'){
            $all_data['value'] = get_post($data['object_id'])->comment_count;
            $data['popular']= $all_data;
            $data['no_properties'] = false;
        }elseif($data['ordenation_id']=='socialdb_license_id'){
            $all_data['value'] = get_post(get_post_meta($data['object_id'], 'socialdb_license_id', true))->post_title;
            $data['license']= $all_data;
            $data['no_properties'] = false;
        }
        
        
        else{
           $all_data['value'] = __('Submitted in ','tainacan').get_the_date('d/m/Y',$data['object_id']);
           $data['recent']= $all_data;
           $data['no_properties'] = false;
        }
        
        if (!isset($data['no_properties'])) {
            $data['no_properties'] = true;
        }
        if (isset($data['stars_id'])) {
            $data['stars_id'] = implode(',', $data['stars_id']);
        }

        return $data;
    }
    
    /**
     * function list_ranking($data)
     * Responsável por listar os rankings ja cadastrados
     * @autor: Marco Tulio 
     */
    public function edit_ranking($data) {
        $data['ranking'] = $this->property_model->get_all_property($data['ranking_id']); // pego todos os dados possiveis da propriedade
        return $data;
    }

    /**
     * function list_ranking($data)
     * @param arra $user_id O id do usuario a verificar
     * @param int $property_id O ranking a verificar
     * @autor: Eduardo 
     */
    public function save_vote($data, $update_value = true,$is_binary = false) {

        $property = get_term_by('id', $data['property_id'], 'socialdb_property_type');
        $is_voted = $this->is_already_voted(get_current_user_id(), $property->term_taxonomy_id, $data['object_id']);
        if (!$is_voted) {
            $post = array(
                'post_title' => 'Vote in the ranking ' . $data['property_id'] . '(stars)',
                'post_status' => 'publish',
                'post_type' => 'socialdb_vote'
            );
            $data['ID'] = wp_insert_post($post);
            add_post_meta($data['ID'], 'socialdb_property_ranking_vote', $data['score']);
            add_post_meta($data['ID'], 'socialdb_property_ranking_object_id', $data['object_id']);
            wp_set_object_terms($data['ID'], array((int) $property->term_id), 'socialdb_property_type', true);
            return true;
        } else {
            $is_changing = get_post_meta($is_voted, 'socialdb_property_ranking_vote', $data['score']);
            if($is_binary&&$is_changing!=$data['score']){
                update_post_meta($is_voted, 'socialdb_property_ranking_vote', $data['score']);
                return true;
            }else if ($update_value) {
                update_post_meta($is_voted, 'socialdb_property_ranking_vote', $data['score']);
            }
            return false; 
        }
    }

    /**
     * @signature - count_votes($property_id,$object_id)
     * @param int $property_id O ranking a verificar
     * @param int $object_id O ranking a verificar
     * @return int O total de votos na votacao
     * @description - Conta os votos em um determinando objeto
     * @author: Eduardo 
     */
    public function count_votes($property_id, $object_id) {
        global $wpdb;
        $values = array();
        $property = get_term_by('id', $property_id, 'socialdb_property_type');
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $wp_term_relationships = $wpdb->prefix . "term_relationships";
        $query = "
                    SELECT p.ID FROM $wp_posts p
                    INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id
                    INNER JOIN $wp_term_relationships tr ON p.ID = tr.object_id    
                    WHERE tr.term_taxonomy_id = {$property->term_taxonomy_id} 
                    AND pm.meta_key LIKE 'socialdb_property_ranking_object_id'
                    AND pm.meta_value LIKE '$object_id'
            ";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result) && count($result) > 0) {
            return count($result);
        } else {
            return 0;
        }
        return $values;
    }

    /**
     * @signature - count_votes_up($property_id,$object_id)
     * @param int $property_id O ranking a verificar
     * @param int $object_id O ranking a verificar
     * @return array Com o total de votos positivos e negativos
     * @description - Conta os votos pos
     * @author: Eduardo 
     */
    public function count_votes_binary($property_id, $object_id) {
        global $wpdb;
        $array = array();
        $property = get_term_by('id', $property_id, 'socialdb_property_type');
        $up = 0;
        $down = 0;
        $values = array();
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $wp_term_relationships = $wpdb->prefix . "term_relationships";
        $query = "
                    SELECT pm.* FROM $wp_posts p
                    INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id
                    INNER JOIN $wp_term_relationships tr ON p.ID = tr.object_id    
                    WHERE tr.term_taxonomy_id = {$property->term_taxonomy_id} 
                    AND pm.meta_key LIKE 'socialdb_property_ranking_object_id'
                    AND pm.meta_value LIKE '$object_id'
            ";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result) && count($result) > 0) {
            foreach ($result as $postmeta) {
                $value = get_post_meta($postmeta->post_id, 'socialdb_property_ranking_vote', true);
                if ($value == 1) {
                    $up++;
                } else {
                    $down++;
                }
            }
        }
        $array['count_up'] = $up;
        $array['count_down'] = $down;
        return $array;
    }

    /**
     * @signature - calculate_vote_stars($data)
     * @param arra $property_id O id do ranking
     * @param int $object_id O id do objeto
     * @return boolean
     * @description - Calcula e atualiza a votação total do tipo STARS
     * @author: Eduardo 
     */
    public function calculate_vote_stars($property_id, $object_id) {
        $result = $this->get_votes($property_id, $object_id);
        if ($result && is_array($result) && count($result) > 0) {
            $values['count'] = count($result);
            $sum = 0;
            foreach ($result as $postmeta) {
                $sum +=get_post_meta($postmeta->post_id, 'socialdb_property_ranking_vote', true);
            }
            $values['final_score'] = round(($sum / count($result)) / 2, 1,PHP_ROUND_HALF_UP);
        } else {
            $values['count'] = 0;
            $values['final_score'] = 0;
        }
        update_post_meta($object_id, 'socialdb_property_' . $property_id, ceil(($values['final_score'])*2)/2);
        return $values;
    }

    /**
     * @signature - calculate_vote_like($data)
     * @param arra $property_id O id do ranking
     * @param int $object_id O id do objeto
     * @return boolean
     * @description - Calcula e atualiza a votação total do tipo LIKE
     * @author: Eduardo 
     */
    public function calculate_vote_like($property_id, $object_id) {
        $result = $this->get_votes($property_id, $object_id);
        if ($result && is_array($result) && count($result) > 0) {
            $values['count'] = count($result);
            $values['final_score'] = count($result);
        } else {
            $values['count'] = 0;
            $values['final_score'] = 0;
        }
        update_post_meta($object_id, 'socialdb_property_' . $property_id, $values['final_score']);
        return $values;
    }

    /**
     * @signature - calculate_vote_like($data)
     * @param arra $property_id O id do ranking
     * @param int $object_id O id do objeto
     * @return boolean
     * @description - Calcula e atualiza a votação total do tipo LIKE
     * @author: Eduardo 
     */
    public function calculate_vote_binary($property_id, $object_id) {
        $up = 0;
        $down = 0;
        $result = $this->get_votes($property_id, $object_id);
        if ($result && is_array($result) && count($result) > 0) {
            foreach ($result as $postmeta) {
                $value = get_post_meta($postmeta->post_id, 'socialdb_property_ranking_vote', true);
                if ($value == 1) {
                    $up++;
                } else {
                    $down++;
                }
            }
        }
        $values['final_up'] = $up;
        $values['final_down'] = $down;
        $values['final_score'] = $up - $down;
        update_post_meta($object_id, 'socialdb_property_' . $property_id, $values['final_score']);
        return $values;
    }

    /**
     * @signature - is_already_voted($user_id,$property_id)
     * @param int $user_id O id do usuario a verificar
     * @param int $property_id O ranking a verificar
     * @return boolean
     * @description - Verifca se o usuario ja votou no ranking, se votou retorna o ID do voto, se nao retorna false
     * @author: Eduardo 
     */
    public function is_already_voted($user_id, $property_id, $object_id) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $wp_term_relationships = $wpdb->prefix . "term_relationships";
        $query = "
                    SELECT p.* FROM $wp_posts p
                    INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id
                    INNER JOIN $wp_term_relationships tr ON p.ID = tr.object_id    
                    WHERE tr.term_taxonomy_id = $property_id AND p.post_author = $user_id
                    AND pm.meta_key LIKE 'socialdb_property_ranking_object_id'
                    AND pm.meta_value LIKE '$object_id'
            ";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result) && count($result) > 0) {
            return $result[0]->ID;
        } else {
            return false;
        }
    }

    /**
     * @signature - get_votes($property_id,$object_id)
     * @param int $property_id O ranking a verificar
     * @param int $object_id O id do objeto que deseja verificar os votos
     * @return boolean
     * @description - reorna todos as votacoes de um objeto
     * @author: Eduardo 
     */
    public function get_votes($property_id, $object_id) {
        global $wpdb;
        $property = get_term_by('id', $property_id, 'socialdb_property_type');
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $wp_term_relationships = $wpdb->prefix . "term_relationships";
        $query = "
                    SELECT pm.* FROM $wp_posts p
                    INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id
                    INNER JOIN $wp_term_relationships tr ON p.ID = tr.object_id    
                    WHERE tr.term_taxonomy_id = {$property->term_taxonomy_id} 
                    AND pm.meta_key LIKE 'socialdb_property_ranking_object_id'
                    AND pm.meta_value LIKE '$object_id'
            ";
        return $wpdb->get_results($query);
    }

    /**
     * funcao que retorna a string que sera colocada no titulo dos objetos sem ser realizado qualquer pesquisa
     * @param int $collection_id O id da colecao
     * @return array com os dados a serem utilizados
     * @author Eduardo Humberto
     */
    public function redirect_facebook($data) {
        $content = $this->get_content_facebook($data);
        $url = get_the_permalink($data['collection_id']) . '?item=' . get_post($data['object_id'])->post_name;
        $thumbnail = wp_get_attachment_url(get_post_thumbnail_id($data['object_id']));
        $title = get_post($data['object_id'])->post_title;
        $data['redirect'] = "https://www.facebook.com/dialog/feed?%20app_id=".$data['fb_id']."&display=popup&caption=Tainacan&link=$url&redirect_uri=$url&picture=$thumbnail&description=$content";
       // $data['redirect'] = "http://www.facebook.com/sharer/sharer.php?s=100&p[url]=$url&amp;p[images][0]=$thumbnail&p[title]=$title&p[summary]=$content";
        return $data;
    }

    public function get_content_facebook($data) {
        $content = strip_tags(get_the_content($data['object_id']));
        $data_ranking = $this->list_ranking_object($data);
        if ($data_ranking['stars']) {
            foreach ($data_ranking['stars'] as $star) {
                $content.= $star['name'] . ' ' . __('Average:','tainacan') . ' ' . $star['value'] . '\n';
            }
        }
        if ($data_ranking['likes']) {
            foreach ($data_ranking['likes'] as $like) {
                $content.= $like['name'] . ' ' . __('Total:') . ' ' . $like['value'] . '\n';
            }
        }
        if ($data_ranking['binaries']) {
            foreach ($data_ranking['binaries'] as $binary) {
                $content.= $binary['name'] . ' ' . __('Average','tainacan') . ' ' . $binary['value'] . '\n';
            }
        }
        return $content;
    }

}
