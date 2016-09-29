<?php
require_once(dirname(__FILE__) . '/../../../../models/general/general_model.php');
/**
 * @clas-name	RankingModel 
 * @description	Classe repsonsável por criar rankings nas coleções.
 * @author     	
 * @version  1.0
 */
class RankingContestModel extends Model {

    public $property_model;

    
     public function verify_property($name,$collection_id) {
        $array = socialdb_term_exists_by_slug($this->generate_slug($name,$collection_id), 'socialdb_property_type');
        if (!isset($array['term_id'])) {
            return false;
        } else {
            return true;
        }
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

    

}
