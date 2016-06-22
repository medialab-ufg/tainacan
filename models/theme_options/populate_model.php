<?php

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
/**
 * Model que realiza a insercao de automatica de itens e taxonomias
 */
class PopulateModel extends Model {
    var $numberItemsPerCategory;
    
    public function __construct($numberItemsPerCategory) {
        $this->numberItemsPerCategory = $numberItemsPerCategory;
    }

    /**
     * Metodo que inicia a execuacao do script para insercao dos dados
     * @param array $data
     */
    public function populate_collection($data) {
        // pego a categoria raiz da colecao
        $category_root_id = $this->get_category_root_of($data['socialdb_collection_id']);
        $subcategories_per_level = $data['subcategories_per_level'];
        $number_levels = $data['number_levels'];
        $this->populate_taxonomies($subcategories_per_level, $number_levels, $category_root_id,$category_root_id);
        $this->generate_classifications($category_root_id, $data['classification']);
    }
    /**
     * 
     * @param type $subcategories_per_level
     * @param type $number_levels
     * @param type $parent
     * @param type $root_of_all o pai de todas as categorias
     * @param type $name_parent o nome do pai para ser feito a concatenacao
     */
    public function populate_taxonomies($subcategories_per_level,$number_levels,$parent,$root_of_all,$name_parent = '') {
        //percorro a qtd por nivel
        for($i = 0;$i<$subcategories_per_level;$i++){
            //coloco seu nome concatenado no pai estilo 00001
            $length = strlen((string)$subcategories_per_level);
            $name = $name_parent.sprintf("%0".$length."d", $i);
           $array = socialdb_insert_term($name, 'socialdb_category_type', $parent, $name.'_'.  mktime(),$root_of_all);
           wp_cache_delete( $array['term_id'], 'terms' );
           wp_cache_delete('all_ids', 'socialdb_category_type');
            wp_cache_delete('get', 'socialdb_category_type');
            delete_option("socialdb_category_type_children");
           _get_term_hierarchy('socialdb_category_type');
//             $array = wp_insert_term($name, 'socialdb_category_type', array('parent' => $parent,
//                    'slug' => $name.'_'.  mktime(), 'description' => $root_of_all));
            
            if($number_levels>1){
                $this->populate_taxonomies($subcategories_per_level, $number_levels-1,$array['term_id'],$root_of_all,$name.'_');
            }elseif($number_levels<2&&$number_levels>0){
                add_term_meta($array['term_id'], 'socialdb_is_border', 'true');
                for($j = 0;$j<$this->numberItemsPerCategory;$j++){
                    $this->insertItem($root_of_all, $array['term_id'], $name, $j);
                }
            }
        }    
    }
    /**
     * 
     * @global type $wpdb
     * @param type $category_root_id
     * @param type $numberOfClassifications
     * @return boolean
     */
    
    public function generate_classifications($category_root_id,$numberOfClassifications) {
        global $wpdb;
        //array de items criado na colecao
        $items = $this->get_category_root_posts($category_root_id);
        //categorias criadas que podem ser usadas como classificacao (as mais profundas)
        $categories = $this->get_categories_border($category_root_id);
        // verifico se nao esta vazio se nao ja retorno falso pois nao ha como classificar
        // sem categorias
        if(!$categories||empty($categories)){
            return false;
        }
        //pego o limite de indices do array de categorias
        $length = count($categories)-1;
        //itero sobre todos os itens da colecao pertencente a categoria raiz
        for($i=0;$i<count($items);$i++){
            //itero sobre as categorias (-1 pois ja tem uma classificacao por
            // default)
            for($j=0;$j<($numberOfClassifications-1);$j++){
                // pego um index aleatorio para realizar a classficacao
                $index = rand ( 0 , $length); 
                //verifico se a classifcacao ja foi realizada
                $verify = socialdb_relation_exists($categories[$index]->term_id, $items[$i]->ID);
                // se nao ele adciona o item e repete a execucao do for ate
                // estourar o limite de classificacoes do item
                if (!isset($verify['object_id'])) {
                    $wpdb->insert($wpdb->term_relationships, array('object_id' => $items[$i]->ID, 'term_taxonomy_id' => $categories[$index]->term_id));
                }
                // se sim decremento o contador para que faca uma nova tentativa
                // para encontrar uma classificacao valida
                else{
                    $j--;
                }
            }
        }
    }
    
     /**
     * function get_categories_border()
     * @param int $root_id
     * @return json com o id e o nome de cada objeto
     */
    public function get_categories_border($root_id) {
        global $wpdb;
        $wp_terms = $wpdb->prefix . "terms";
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $wp_taxonomymeta = $wpdb->prefix . "termmeta";
        $query = "
			SELECT * FROM $wp_terms t
			INNER JOIN $wp_term_taxonomy tm ON t.term_id = tm.term_id
			INNER JOIN $wp_taxonomymeta tt ON t.term_id = tt.term_id
		        WHERE tm.description LIKE '$root_id' and
                        tt.meta_key LIKE 'socialdb_is_border' AND tt.meta_value LIKE 'true'
		";
        $result = $wpdb->get_results($query);
        if ($result) {
            return $result;
        }else{
            return array();
        }
    }
    /**
     * 
     * @param type $category_root_id a CATEGORIA RAIZ DA COLECAO
     * @param type $term_id O id da categoria que foi criada
     * @param type $name O nome da categoria que sera inserido o item
     * @param type $counter O contador de itens ja inseridos nesta categoria
     */
    public function insertItem($category_root_id,$term_id,$name,$counter) {
        global $wpdb;
        $post = array(
            'post_title' => $name.'-'.$counter,
            'post_status' => 'publish',
            'post_type' => 'socialdb_object'
        );
        $post_id = wp_insert_post($post);
        $wpdb->insert($wpdb->term_relationships, array('object_id' => $post_id, 'term_taxonomy_id' => $category_root_id));
       // wp_update_term_count(array($category_root_id), 'socialdb_category_type');
        $wpdb->insert($wpdb->term_relationships, array('object_id' => $post_id, 'term_taxonomy_id' => $term_id));
        //wp_update_term_count(array($term_id), 'socialdb_category_type');
        
    }
    
    
    public function getProgress($data){
        $category_root_id = $this->get_category_root_of($data['collection_id']);
        //array de items criado na colecao
        $items = $this->get_category_root_posts($category_root_id);
        //categorias criadas que podem ser usadas como classificacao (as mais profundas)
        $categories = $this->get_categories_border($category_root_id);
        return json_encode(['documents'=>$items,'categories'=>$categories]);
    }
    
    public function clean_term_cache2($ids, $taxonomy = '', $clean_taxonomy = true) {
	global $wpdb, $_wp_suspend_cache_invalidation;

	if ( ! empty( $_wp_suspend_cache_invalidation ) ) {
		return;
	}

	if ( !is_array($ids) )
		$ids = array($ids);

	$taxonomies = array();
	// If no taxonomy, assume tt_ids.
	if ( empty($taxonomy) ) {
		$tt_ids = array_map('intval', $ids);
		$tt_ids = implode(', ', $tt_ids);
		$terms = $wpdb->get_results("SELECT term_id, taxonomy FROM $wpdb->term_taxonomy WHERE term_taxonomy_id IN ($tt_ids)");
		$ids = array();
		foreach ( (array) $terms as $term ) {
			$taxonomies[] = $term->taxonomy;
			$ids[] = $term->term_id;
			wp_cache_delete( $term->term_id, 'terms' );
		}
		$taxonomies = array_unique($taxonomies);
	} else {
		$taxonomies = array($taxonomy);
		foreach ( $taxonomies as $taxonomy ) {
			foreach ( $ids as $id ) {
				wp_cache_delete( $id, 'terms' );
			}
		}
	}

	foreach ( $taxonomies as $taxonomy ) {
		if ( $clean_taxonomy ) {
			wp_cache_delete('all_ids', $taxonomy);
			wp_cache_delete('get', $taxonomy);
			delete_option("{$taxonomy}_children");
			// Regenerate {$taxonomy}_children
			_get_term_hierarchy($taxonomy);
		}

		/**
		 * Fires once after each taxonomy's term cache has been cleaned.
		 *
		 * @since 2.5.0
		 *
		 * @param array  $ids      An array of term IDs.
		 * @param string $taxonomy Taxonomy slug.
		 */
		do_action( 'clean_term_cache', $ids, $taxonomy );
	}

	wp_cache_set( 'last_changed', microtime(), 'terms' );
}

}
