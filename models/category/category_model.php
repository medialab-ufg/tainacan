<?php

//use CollectionModel;

if (isset($_GET['by_function'])) {
    include_once (WORDPRESS_PATH . '/wp-config.php');
    include_once (WORDPRESS_PATH . '/wp-load.php');
    include_once (WORDPRESS_PATH . '/wp-includes/wp-db.php');
} else {
    include_once (dirname(__FILE__) . '/../../../../../wp-config.php');
    include_once (dirname(__FILE__) . '/../../../../../wp-load.php');
    include_once (dirname(__FILE__) . '/../../../../../wp-includes/wp-db.php');
}
require_once(dirname(__FILE__) . '../../general/general_model.php');
include_once (dirname(__FILE__) . '../../collection/collection_model.php');
include_once (dirname(__FILE__) . '../../property/property_model.php');
include_once (dirname(__FILE__) . '../../user/user_model.php');

class CategoryModel extends Model {

    var $usermodel;

    public function CategoryModel() {
        $this->usermodel = new UserModel();
    }

    /**
     * function add($data)
     * @param mix $data  O id do colecao
     * @return json  
     * 
     * Autor: Eduardo Humberto 
     */
    public function  add($data) {
        $is_new = $this->verify_category($data);

        if (!$is_new) {
            if ($data['category_parent_id'] == '0' ||
                    $data['category_parent_id'] == 'public_categories' || 
                    $data['category_parent_id'] == 'shared_categories'||$data['category_parent_id'] == 'socialdb_category'||$data['category_parent_id'] == 'socialdb_taxonomy') {// se nao o usuario nao setou o parent
                $new_category = wp_insert_term($data['category_name'], 'socialdb_category_type', array('parent' => $this->get_category_taxonomy_root(),
                    'slug' => $this->generate_slug($data['category_name'], $data['collection_id']), 'description' => $this->set_description($data)));
            } else {
                $data['category_parent_id'] = (($data['category_parent_id'] == 'user_categories' || $data['category_parent_id'] == 'socialdb_taxonomy' ) ? $this->get_category_taxonomy_root() : $data['category_parent_id']);
                $new_category = create_register($data['category_name'], 'socialdb_category_type', array('parent' => $data['category_parent_id'],
                    'slug' => $this->generate_slug($data['category_name'], $data['collection_id']), 'description' => $this->set_description($data)));
            }
        }
        //apos a insercao
        if (!is_wp_error($new_category) && $new_category['term_id']) {// se a categoria foi inserida com sucesso
            instantiate_metas($new_category['term_id'], 'socialdb_taxonomy', 'socialdb_category_type', true);
            insert_meta_default_values($new_category['term_id']);
            $this->update_metas($new_category['term_id'], $data);
            $data['success'] = 'true';
            $data['term_id'] = $new_category['term_id'];
            $log_data = ['collection_id' => $data['collection_id'], 'user_id' => get_current_user_id(),
                'resource_id' => $data['term_id'], 'event_type' => 'user_category', 'event' => 'add' ];
            Log::addLog($log_data);
        } else {
            $data['success'] = 'false';
            if ($is_new) {
                $data['msg'] = __('This category already exists!', 'tainacan');
            }
        }
        return json_encode($data);
    }

    /**
     * function set_description($data)
     * @param mix $data  Os dados que serao utilizados para verificar a existencia da categoria
     * metodo que verifica se a categoria realmente exise
     * Autor: Eduardo Humberto 
     */
    public function set_description($data) {
        global $config;
        if (isset($config['mode']) && $config['mode'] == 1 && $data['observation']) {
            $description = $data['observation'];
        }else if($data['category_description']){
            $description = $data['category_description'];
        } else {
            $description = '';
        }
        return $description;
    }

    /**
     * function verify_category($data)
     * @param mix $data  Os dados que serao utilizados para verificar a existencia da categoria
     * metodo que verifica se a categoria realmente exise
     * Autor: Eduardo Humberto 
     */
    public function verify_category($data) {
        if (isset($data['category_id']) && !empty($data['category_id'])) {
            $term = get_term_by('id', $data['category_id'], 'socialdb_category_type');
            if (isset($term->term_id)) {
                return true;
            } else {
                return false;
            }
        } else {
            if (isset($data['category_parent_id']) && ($data['category_parent_id'] != '0' && $data['category_parent_id'] != 'user_categories' && $data['category_parent_id'] != $this->get_category_root())) {
                $array = socialdb_term_exists_by_slug(sanitize_title(remove_accent($data['category_name'])), 'socialdb_category_type', $data['category_parent_id']);
            } else {
                $array = socialdb_term_exists_by_slug(sanitize_title(remove_accent($data['category_name'])), 'socialdb_category_type');
            }
            if (!isset($array['term_id'])) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * function update($data)
     * @param mix $data  Os dados que serao utilizados para atualizar a colecao
     * @return json com os dados atualizados 
     * metodo que atualiza os dados da colecao
     * Autor: Eduardo Humberto 
     */
    public function update($data) {
        $data['category_parent_id'] = ($data['category_parent_id'] == 'user_categories' ? '0' : $data['category_parent_id']);
        if (($data['category_parent_id'] == '0' || $data['category_parent_id'] == $this->get_category_taxonomy_root()) && trim($data['category_name'])) {
            $update_category = wp_update_term($data['category_id'], 'socialdb_category_type', array(
                'name' => $data['category_name'], 'parent' => $this->get_category_taxonomy_root(),
                'description' => $this->set_description($data) ));
        } elseif (trim($data['category_name'])!='') {
            $update_category = wp_update_term($data['category_id'], 'socialdb_category_type', array(
                'name' => $data['category_name'],
               // 'slug' => $this->generate_slug($data['category_name'], $data['collection_id']),
                'parent' => $data['category_parent_id'],
                'description' => $this->set_description($data)
            ));
        }
        if ($update_category && !is_wp_error($update_category) && $update_category['term_id']) {// se a categoria foi atualizada com sucesso
            $has_property = get_term_meta($update_category['term_id'], 'socialdb_category_property_change_label', true);
            if($has_property && is_numeric($has_property)){
                // mudo o nome da propriedade que contem a categoria ruaz
                 $update = wp_update_term($has_property, 'socialdb_property_type', array(
                'name' => $data['category_name']));
            }
            $this->update_metas($update_category['term_id'], $data);
            $this->insert_synonyms($update_category['term_id'], $data);
            $log_data = ['collection_id' => $data['collection_id'], 'resource_id' => $update_category['term_id'],
                'user_id' => get_current_user_id(), 'event_type' => 'user_category', 'event' => 'edit' ];
            Log::addLog($log_data);
            $data['success'] = 'true';
        } else {
            $data['success'] = 'false';
        }
        return json_encode($data);
    }
    /**
     * salva os sinonimos se houver
     * @param int $cat_id
     * @param array $data
     */
    public function insert_synonyms($cat_id, $data){
        $new_hash = md5(time());//crio o hash
        update_term_meta($cat_id, 'socialdb_term_synonyms', $new_hash); // salvo a categoria atual o novo hash exisitndo ou nao
        $synonyms = explode(',',$data['synonyms']); // pego as selecionadas
        if(is_array($synonyms)){ // se exitir
          $synonyms = str_replace("_tag","",array_filter($synonyms)); //pego todos indiferente de tags ou categorias
          foreach ($synonyms as $synonym) { // percorro
              $hash = get_term_meta($synonym, 'socialdb_term_synonyms', true); // verifico se ja existe um hash neste termo
              if($hash&&$hash!==''){//se alguma das selecionadas ja pertencer a outro grupo de sinonimos
                  $group_ids = $this->get_categories_hash($hash); // pego todos
                  foreach ($group_ids as $group_id) { // percorro o grupo
                      update_term_meta($group_id, 'socialdb_term_synonyms', $new_hash); // salvo o novo hash 
                  }
              }else{ // se nao
                  update_term_meta($synonym, 'socialdb_term_synonyms', $new_hash); // salvo o novo hash 
              }
          }
        }
    }
    /**
     * function update_metas($data)
     * @param int $category_id  O id da categoria que tera os metas atualizados
     * @param mix $data  Os dados que serao utilizados para atualizar a categoria
     * @return boolean Se atualizar Verdadeiro, ERRO falso
     * metodo que atualiza os dados da colecao
     * Autor: Eduardo Humberto 
     */
    public function update_metas($category_id, $data) {
        if (isset($data['category_permission'])) {
            update_term_meta($category_id, 'socialdb_category_permission', $data['category_permission']);
            if (isset($data["category_moderators"]) && is_array($data["category_moderators"])) {
                delete_term_meta($category_id, "socialdb_category_moderators");
                foreach ($data["category_moderators"] as $moderator) {
                    add_term_meta($category_id, "socialdb_category_moderators", $moderator);
                }
            } else {
                delete_term_meta($category_id, "socialdb_category_moderators");
                update_term_meta($category_id, "socialdb_category_moderators", '');
            }
        }
        $this->update_archive($category_id, $data);
        return true;
    }

    /**
     * function update_archive
     * @param int $category_id  O id da categoria que tera os metas atualizados
     * @param mix $data  Os dados que serao utilizados para atualizar a categoria
     * metodo que atualiza os metadados das categorias quando o repositorio for do tipo 1
     * Autor: Eduardo Humberto 
     */
    public function update_archive($category_id, $data) {
        global $config;
        if (isset($config['mode']) && $config['mode'] == 1) {
            $current_phase = 0;
            $intermediate_phase = 0;
            $current_phase_string = $data['current_phase_string'];
            if($current_phase_string){
                $current_phase = $current_phase_string;
            }else{
                    if ($data['current_phase_year']) {
                        $current_phase = intval(trim($data['current_phase_year'])) * 12;
                    }
                    if ($data['current_phase_month']) {
                        $current_phase += intval(trim($data['current_phase_month']));
                    }
            }
            update_term_meta($category_id, "socialdb_category_current_phase", $current_phase);
            if ($data['intermediate_phase_year']) {
                $intermediate_phase = intval(trim($data['intermediate_phase_year'])) * 12;
            }
            if ($data['intermediate_phase_month']) {
                $intermediate_phase += intval(trim($data['intermediate_phase_month']));
            }
            update_term_meta($category_id, "socialdb_category_intermediate_phase", $intermediate_phase);
            update_term_meta($category_id, "socialdb_category_destination", $data['destination']);
            update_term_meta($category_id, "socialdb_category_classification_code", $data['classification_code']);
        }
    }

    /* function delete() */
    /* @param array $data
      /* @return json com os dados da categoria excluida.
      /* exclui a categoria */
    /* @author Eduardo */

    public function delete($data) {
        if (!$this->verify_collection_category_root($data['category_delete_id'])) {
            if ($data['category_delete_id'] != $this->get_category_root() && wp_delete_term($data['category_delete_id'], 'socialdb_category_type')) {
                $data['success'] = 'true';
                $log_data = ['collection_id' => $data['collection_id'], 'resource_id' => $data['category_delete_id'], 'user_id' => get_current_user_id(), 'event_type' => 'user_category', 'event' => 'delete' ];
                Log::addLog($log_data);
            } else {
                $data['success'] = 'false';
            }
        } else {
            $data['success'] = 'false';
            $data['message'] = __('Collection root category cannot be deleted', 'tainacan');
        }
        return json_encode($data);
    }

    /* function initCategoriesDynatreeDynamic() */
    /* receive ((array) data) */
    /* O dynatree dinamico do formulario de submissao */
    /* Author: Eduardo */

    public function initCategoriesDynatreeDynamic($data) {
        $counter = 0;
        if (isset($data['hide_checkbox'])) {
            $hide_checkbox = true;
        } else {
            $hide_checkbox = false;
        }
        $all_data = $this->get_all_property($data['property_id'], true); // pego todos os dados possiveis da propriedade
        $initial_term = get_term_by('id', $all_data['metas']['socialdb_property_term_root'], 'socialdb_category_type');
        $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $initial_term->term_id . '_color', true);
        $classCss = ($classCss)?$classCss:'color4';
//        $dynatree = array('title' => $initial_term->name, 'isLazy' => false,
//            'key' => $initial_term->term_id, 'activate' => false, 'expand' => true,
//            'hideCheckbox' => true, 'children' => array(), 'addClass' => $classCss);

        if (isset($data['order'])) {
            $children = $this->getChildren($all_data['metas']['socialdb_property_term_root'], 't.name ASC');
        } else {
            $children = $this->getChildren($all_data['metas']['socialdb_property_term_root']);
        }
        if (count($children) > 0) {
            foreach ($children as $child) {
                $children_of_child = $this->getChildren($child->term_id);
                if (count($children_of_child) > 0 || (!empty($children_of_child) && $children_of_child)) {// se tiver descendentes
                    $dynatree[] = array('title' => $child->name,'select'=>$this->isSelected($child->term_id,$data,'categories'), 'hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'isLazy' => true, 'addClass' => $classCss);
                } else {// se nao tiver filhos
                    $dynatree[] = array('title' => $child->name, 'select'=>$this->isSelected($child->term_id,$data,'categories'), 'hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'addClass' => $classCss);
                }
                $counter++;
                if ($counter == 25) {
                    $dynatree[] = array('title' => __('See more', 'tainacan'), 'hideCheckbox' => true, 'key' => $all_data['metas']['socialdb_property_term_root'] . '_moreoptions', 'isLazy' => true, 'addClass' => 'more');
                    break;
                }
            }
        }
        return json_encode($dynatree);
    }

    /**
     * @param $category_id
     * @param $array
     * @param bool $field
     */
    public function isSelected($category_id,$array,$field = false){
        if($field && isset($array[$field])){
            $search = (is_array($array[$field])) ? $array[$field] : explode(',',$array[$field]);
            if(in_array($category_id,$search)){
                return true;
            }
        }
        return false;
    }

    /* function initDynatree() */
    /* receive ((array) data) */
    /* inite the div dynatree in the template index */
    /* Author: Eduardo */

    public function initCategoriesDynatree($data) {
        $dynatree = [];
        $dynatree = $this->generate_user_categories_dynatree($data, $dynatree, true, true);
        //if(has_nav_menu('menu-ibram')){
            $dynatree = $this->generate_collection_categories_dynatree($data, $dynatree, true, false);  
        //}
        $dynatree = $this->generate_shared_categories_dynatree($data, $dynatree, true);
        $dynatree = $this->generate_public_categories_dynatree($data, $dynatree, true);
        return json_encode($dynatree);
    }

    /* function initCategoriesDynatreeTerms() */
    /* receive ((array) data) */
    /* Inicializa o dynatree nas propriedades de termo */
    /* Author: Eduardo */

    public function initCategoriesDynatreeTerms($data) {
        $dynatree = [];

        $hide_checkbox = true;
        if (!isset($data['hide_checkbox']) || $data['hide_checkbox'] == 'false') {
            $hide_checkbox = false;
        }
        $dynatree = $this->generate_user_categories_dynatree($data, $dynatree, $hide_checkbox, false);
        $dynatree = $this->generate_collection_categories_dynatree($data, $dynatree, $hide_checkbox, false);
        $dynatree = $this->generate_shared_categories_dynatree($data, $dynatree, $hide_checkbox, false);
        $dynatree = $this->generate_public_categories_dynatree($data, $dynatree, $hide_checkbox, false);
        return json_encode($dynatree);
    }

    /* function getChildrenDynatree() */
    /* @param array $data Os dados vindo do formulario
      /* @param array $dynatree O dynatree a ser populado
      /* @return array O dynatree com os categorias do usuario
      /* Retorna os filhos para as categorias no dynatree */
    /* @author Eduardo */

    public function generate_collection_categories_dynatree($data, $dynatree, $hide_checkbox = false,$show_select = true) {
        if(has_filter('remove_collection_categories') && apply_filters('remove_collection_categories', '')){
            return $dynatree;
        }
        $classCss = 'category_property_img';
        $dynatree[] = array('title' => __('Collection Categories', 'tainacan'), 'isLazy' => false,
            'key' => $this->get_category_root(), 'activate' => false, 'expand' => true,
            'hideCheckbox' => true, 'children' => array(), 'addClass' => $classCss);
        $facets_id = $this->get_categories_by_owner(get_current_user_id(), $this->get_category_root());
        foreach ($facets_id as &$facet_id) {
            $facet = get_term_by('id', $facet_id->term_id, 'socialdb_category_type');
            //pegando os indices do array
            $dynatree_index_parent = end(array_keys($dynatree)); //pega o index do parent maior, no caso seria o noh (User categories)
            // inserir os dados no dynatree
            if ($facet) {
                if (in_array($facet_id->term_id, CollectionModel::get_facets($data['collection_id']))) { // verifico se e uma faceta
                    $dynatree[$dynatree_index_parent]['children'][] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id, 'isLazy' => true, 'data' => $url,
                        'expand' => true, 'hideCheckbox' => $hide_checkbox, 'addClass' => $classCss, 'select' => $show_select, 'activate' => false, 'expand' => false);
                } else {
                    $dynatree[$dynatree_index_parent]['children'][] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id, 'isLazy' => true, 'data' => $url, 'expand' => true,
                        'hideCheckbox' => $hide_checkbox, 'addClass' => $classCss, 'activate' => false, 'expand' => false);
                }
            }
        }
        return $dynatree;
    }
    /* function getChildrenDynatree() */
    /* @param array $data Os dados vindo do formulario
      /* @param array $dynatree O dynatree a ser populado
      /* @return array O dynatree com os categorias do usuario
      /* Retorna os filhos para as categorias no dynatree */
    /* @author Eduardo */

    public function generate_user_categories_dynatree($data, $dynatree, $hide_checkbox = false,$show_select = true) {
        $classCss = 'user_img';
        $dynatree[] = array('title' => __('User Categories', 'tainacan'), 'isLazy' => false,
            'key' => 'user_categories', 'activate' => false, 'expand' => true,
            'hideCheckbox' => true, 'children' => array(), 'addClass' => $classCss);
        $facets_id = $this->get_categories_by_owner(get_current_user_id(), $this->get_category_taxonomy_root());
        foreach ($facets_id as &$facet_id) {
            $facet = get_term_by('id', $facet_id->term_id, 'socialdb_category_type');
            //pegando os indices do array
            $dynatree_index_parent = end(array_keys($dynatree)); //pega o index do parent maior, no caso seria o noh (User categories)
            // inserir os dados no dynatree
            if ($facet) {
                if (in_array($facet_id->term_id, CollectionModel::get_facets($data['collection_id']))) { // verifico se e uma faceta
                    $dynatree[$dynatree_index_parent]['children'][] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id, 'isLazy' => true, 'data' => $url,
                        'expand' => true, 'hideCheckbox' => $hide_checkbox, 'addClass' => $classCss, 'select' => $show_select, 'activate' => false, 'expand' => false);
                } else {
                    $dynatree[$dynatree_index_parent]['children'][] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id, 'isLazy' => true, 'data' => $url, 'expand' => true,
                        'hideCheckbox' => $hide_checkbox, 'addClass' => $classCss, 'activate' => false, 'expand' => false);
                }
            }
        }
        return $dynatree;
    }

    /* function getChildrenDynatree() */
    /* @param array $data Os dados vindo do formulario
      /* @param array $dynatree O dynatree a ser populado
      /* @return array O dynatree com os categorias do usuario
      /* Retorna os filhos para as categorias no dynatree */
    /* @author Eduardo */

    public function generate_shared_categories_dynatree($data, $dynatree, $hide_checkbox = false) {
        $classCss = 'shared_img';
        $dynatree[] = array('title' => __('Shared Categories', 'tainacan'), 'isLazy' => false,
            'key' => 'shared_categories', 'activate' => false, 'expand' => true,
            'hideCheckbox' => true, 'children' => array(), 'addClass' => $classCss);

        $facets_id = $this->get_categories_shared_by_owner(get_current_user_id());
        foreach ($facets_id as &$facet_id) {
            $facet = get_term_by('id', $facet_id->term_id, 'socialdb_category_type');
            //pegando os indices do array
            $dynatree_index_parent = end(array_keys($dynatree)); //pega o index do parent maior, no caso seria o noh (User categories)
            // inserir os dados no dynatree
            if ($facet) {
                if (in_array($facet_id->term_id, CollectionModel::get_facets($data['collection_id']))) { // verifico se e uma faceta
                    $dynatree[$dynatree_index_parent]['children'][] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id, 'isLazy' => true, 'data' => $url,
                        'expand' => true, 'hideCheckbox' => $hide_checkbox, 'addClass' => $classCss, 'select' => true, 'activate' => false, 'expand' => false);
                } else {
                    $dynatree[$dynatree_index_parent]['children'][] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id, 'isLazy' => true, 'data' => $url, 'expand' => true,
                        'hideCheckbox' => $hide_checkbox, 'addClass' => $classCss, 'activate' => false, 'expand' => false);
                }
            }
        }
        return $dynatree;
    }

    /* function getChildrenDynatree() */
    /* @param array $data Os dados vindo do formulario
      /* @param array $dynatree O dynatree a ser populado
      /* @return array O dynatree com os categorias do usuario
      /* Retorna os filhos para as categorias no dynatree */
    /* @author Eduardo */

    public function generate_public_categories_dynatree($data, $dynatree, $hide_checkbox = false) {
        $classCss = 'public_img';
        $dynatree[] = array('title' => __('Public Categories', 'tainacan'), 'isLazy' => false,
            'key' => 'public_categories', 'activate' => false, 'expand' => true,
            'hideCheckbox' => true, 'children' => array(), 'addClass' => $classCss);
        $facets_id = $this->get_public_categories();
        foreach ($facets_id as &$facet_id) {
            $facet = get_term_by('id', $facet_id->term_id, 'socialdb_category_type');
            //pegando os indices do array
            $dynatree_index_parent = end(array_keys($dynatree)); //pega o index do parent maior, no caso seria o noh (User categories)
            // inserir os dados no dynatree
            if ($facet) {
                if (in_array($facet_id->term_id, CollectionModel::get_facets($data['collection_id']))) { // verifico se e uma faceta
                    $dynatree[$dynatree_index_parent]['children'][] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id, 'isLazy' => true, 'data' => $url,
                        'expand' => true, 'hideCheckbox' => $hide_checkbox, 'addClass' => $classCss, 'select' => true, 'activate' => false, 'expand' => false);
                } else {
                    $dynatree[$dynatree_index_parent]['children'][] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id, 'isLazy' => true, 'data' => $url, 'expand' => true,
                        'hideCheckbox' => $hide_checkbox, 'addClass' => $classCss, 'activate' => false, 'expand' => false);
                }
            }
        }
        return $dynatree;
    }

    /* function initPropertyCategoriesDynatree() */
    /* receive ((array) data) */
    /* inite the div dynatree  in the property index */
    /* Author: Eduardo */

    public function initPropertyCategoriesDynatree($data) {
        $facets_id = $this->get_categories_by_owner(get_current_user_id(), $this->get_category_root());
        if(isset($data['hideCheckbox'])&&$data['hideCheckbox']=='false'){
            $hide_checkbox = false;
        }
        foreach ($facets_id as &$facet_id) {
            $facet = get_term_by('id', $facet_id->term_id, 'socialdb_category_type');
            if ($facet) {
                $dynatree[] = array('title' => ucfirst($facet->name), 'key' => $facet->term_id, 'isLazy' => true, 'expand' => true, 'hideCheckbox' => $hide_checkbox, 'addClass' => 'color4');
                $dynatree[end(array_keys($dynatree))] = $this->getPropertyChildrenDynatree($facet->term_id, $dynatree[end(array_keys($dynatree))], $data['collection_id'],$hide_checkbox);
            }
        }
        return json_encode($dynatree);
    }

    /* function getChildrenDynatree() */
    /* @param int $facet_id
      /* @param array $dynatree
      /* @param int $collection_id
      /* @return arra With O term_id da categoria root da colecao.
      /* Retorna os filhos para as categorias no dynatree */
    /* @author Eduardo */

    public function getChildrenDynatree($facet_id, $dynatree, $collection_id) {
        $children = $this->getChildren($facet_id);
        if (count($children) > 0) {
            foreach ($children as $child) {
                $children_of_child = $this->getChildren($child->term_id);
                if (count($children_of_child) > 0 || (!empty($children_of_child) && $children_of_child)) {// se tiver descendentes
                    if (in_array($child->term_id, CollectionModel::get_facets($collection_id))) { // verifico se e uma faceta
                        $dynatree['children'][] = array('title' => $child->name, 'key' => $child->term_id, 'isLazy' => true, 'select' => true,'addClass' => 'color4');
                    } else {// se nao
                        $dynatree['children'][] = array('title' => $child->name, 'key' => $child->term_id, 'isLazy' => true,'addClass' => 'color4');
                    }
                } else {// se nao tiver filhos
                    if (in_array($child->term_id, CollectionModel::get_facets($collection_id))) {// se for faceta
                        $dynatree['children'][] = array('title' => $child->name, 'key' => $child->term_id, 'select' => true,'addClass' => 'color4');
                    } else {
                        $dynatree['children'][] = array('title' => $child->name, 'key' => $child->term_id,'addClass' => 'color4');
                    }
                }
            }
        }
        return $dynatree;
    }

    /* function getPropertyChildrenDynatree() */
    /* @param int $facet_id
      /* @param array $dynatree
      /* @param int $collection_id
      /* @return arra With O term_id da categoria root da colecao.
      /* Retorna os filhos para as categorias no dynatree das propriedades */
    /* @author Eduardo */

    public function getPropertyChildrenDynatree($facet_id, $dynatree, $collection_id,$hide_checkbox = true) {
        $children = $this->getChildren($facet_id);
        if (count($children) > 0) {
            foreach ($children as $child) {
                $children_of_child = $this->getChildren($child->term_id);
                if (count($children_of_child) > 0 || (!empty($children_of_child) && $children_of_child)) {// se tiver descendentes
                    $dynatree['children'][] = array('title' => $child->name, 'key' => $child->term_id, 'hideCheckbox' => $hide_checkbox, 'isLazy' => true,'addClass' => 'color4');
                } else {// se nao tiver filhos
                    $dynatree['children'][] = array('title' => $child->name, 'key' => $child->term_id, 'hideCheckbox' => $hide_checkbox,'addClass' => 'color4');
                }
            }
        }
        return $dynatree;
    }

    /* function find_dynatree_children() */
    /* @param array $data  os dados do formulario
      /* @return json */
    /* @author: Eduardo */

    public function find_dynatree_children($data) {
        //print_r($data);
        $property_model = new PropertyModel();
        $collection_id = ($data['collection_id'])? $data['collection_id'] : '';
        $info = $property_model->get_all_property($data['property_id'], true,$collection_id); // pego todos os dados possiveis da propriedade
        $selected_ids = (!isset($data['selectedCategories'])) ? $info['metas']['socialdb_property_object_category_id'] : array_filter(explode(',', $data['selectedCategories']));
        $dynatree = [];

        if (isset($data['hide_checkbox'])) {
            $hide_checkbox = true;
        } else {
            $hide_checkbox = false;
        }

        $data['classCss'] = ($data['classCss'])?$data['classCss']:'color4';
        $children = $this->get_categories($data['category_id']);
        if (is_array($children) && count($children) > 0) {
            foreach ($children as $child)
            {
                $selected = false;
                if(is_array($selected_ids) && in_array($child->term_id, $selected_ids) )
                {
                    $selected = true;
                }

                //verifica se o proximo nivel possui mais descendentes
                $sub_childrens = $this->get_categories($child->term_id);
                if (is_array($sub_childrens) && count($sub_childrens) > 0) {
                    if (is_array(CollectionModel::get_facets($data['collection_id']))&&in_array($child->term_id, CollectionModel::get_facets($data['collection_id']))) {
                        $dynatree[] = array('title' => $child->name, 'hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'addClass' => $data['classCss'], 'isLazy' => true, 'select' => true);
                    } else {
                        $dynatree[] = array('title' => $child->name, 'hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'addClass' => $data['classCss'], 'isLazy' => true, 'select' => $selected);
                    }
                } else {
                    if (is_array(CollectionModel::get_facets($data['collection_id']))&&in_array($child->term_id,CollectionModel::get_facets($data['collection_id']))) {
                        $dynatree[] = array('title' => $child->name, 'hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'addClass' => $data['classCss'], 'select' => true);
                    } else {
                        $dynatree[] = array('title' => $child->name, 'hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'addClass' => $data['classCss'], 'select' => $selected);
                    }
                }
            }
        }
        return json_encode($dynatree);
    }

    /* function get_categories_by_owner() */
    /* @param int $owner_id o dono das categorias
      /* @param $parent(optional) a categoria pai que sera utilizada como base na pesquisa
      /* @return array */
    /* Author: Eduardo */

    public function get_categories_by_owner($owner_id, $parent = 0) {
        global $wpdb;
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $wp_terms = $wpdb->prefix . "terms";
        $wp_taxonomymeta = $wpdb->prefix . "termmeta";
        $query = "
			SELECT * FROM $wp_terms t
			INNER JOIN $wp_term_taxonomy tt ON t.term_id = tt.term_id
                        INNER JOIN $wp_taxonomymeta tx on tx.term_id = tt.term_id
			WHERE tt.parent = {$parent} AND (tx.meta_key LIKE 'socialdb_category_owner' AND
                        tx.meta_value LIKE '$owner_id' )   
                        ORDER BY t.name ASC  
		";
        return $wpdb->get_results($query);
    }

    /* function get_categories_shared_by_owner() */
    /* @param int $owner_id O id do usuario que buscara as categorias compartilhadas
      /* @return array com as categorias compartilhadas */
    /* @author: Eduardo */

    public function get_categories_shared_by_owner($owner_id) {
        global $wpdb;
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $wp_terms = $wpdb->prefix . "terms";
        $wp_taxonomymeta = $wpdb->prefix . "termmeta";
        $query = "
                    SELECT * FROM $wp_terms t
                    INNER JOIN $wp_term_taxonomy tt ON t.term_id = tt.term_id 
                    INNER JOIN $wp_taxonomymeta tx ON t.term_id = tx.term_id
                    WHERE tx.meta_key = 'socialdb_category_moderators' and tx.meta_value LIKE '$owner_id'   
                    ORDER BY t.name
                    ";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result) && count($result) > 0) {
            foreach ($result as $category) {
                $categories[$category->term_id] = $category->term_id;
            }
        }
        if ($categories && is_array($categories) && count($categories) > 0) {
            foreach ($categories as $category) {
                $hierarchies = array_reverse(get_ancestors($category, 'socialdb_category_type'));
                if (is_array($hierarchies)) {
                    $hierarchies[] = $category;
                } else {
                    $hierarchies = [];
                    $hierarchies[] = $category;
                }
                foreach ($hierarchies as $hierarchy) {
                    if (in_array($hierarchy, $categories)) {
                        if ($flag_eliminate) {
                            unset($categories[$hierarchy]);
                        }
                        $flag_eliminate = true;
                    }
                }
                $flag_eliminate = false;
            }
        }
        //
        $result = [];
        if ($categories && is_array($categories) && count($categories) > 0) {
            foreach ($categories as $category) {
                $result[] = get_term_by('id', $category, 'socialdb_category_type');
            }
        }
        return $result;
    }

    /* function get_categories_shared_by_owner() */
    /* @param int $owner_id O id do usuario que buscara as categorias compartilhadas
      /* @return array com as categorias compartilhadas */
    /* @author: Eduardo */

    public function get_public_categories() {
        global $wpdb;
        $flag_eliminate = false;
        $categories = [];
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $wp_terms = $wpdb->prefix . "terms";
        $wp_taxonomymeta = $wpdb->prefix . "termmeta";
        $query = "
                    SELECT * FROM $wp_terms t
                    INNER JOIN $wp_term_taxonomy tt ON t.term_id = tt.term_id 
                    INNER JOIN $wp_taxonomymeta tx ON t.term_id = tx.term_id
                    WHERE tx.meta_key LIKE 'socialdb_category_permission' and tx.meta_value LIKE 'public'  
                    ORDER BY t.name
                    ";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result) && count($result) > 0) {
            foreach ($result as $category) {
                $categories[$category->term_id] = $category->term_id;
            }
        }
        if ($categories && is_array($categories) && count($categories) > 0) {
            foreach ($categories as $category) {
                $hierarchies = array_reverse(get_ancestors($category, 'socialdb_category_type'));
                if (is_array($hierarchies)) {
                    $hierarchies[] = $category;
                } else {
                    $hierarchies = [];
                    $hierarchies[] = $category;
                }
                foreach ($hierarchies as $hierarchy) {
                    if (in_array($hierarchy, $categories)) {
                        if ($flag_eliminate) {
                            unset($categories[$hierarchy]);
                        }
                        $flag_eliminate = true;
                    }
                }
                $flag_eliminate = false;
            }
        }
        //
        $result = [];
        if ($categories && is_array($categories) && count($categories) > 0) {
            foreach ($categories as $category) {
                $result[] = get_term_by('id', $category, 'socialdb_category_type');
            }
        }
        return $result;
    }

    

    /**
     * function get_collection_category_root($collection_id)
     * @param int $collection_id
     * @return int With O term_id da categoria root da colecao.
     * 
     * metodo responsavel em retornar a categoria root da colecao
     * Autor: Eduardo Humberto 
     */
    public function get_collection_category_root($collection_id) {
        return get_post_meta($collection_id, 'socialdb_collection_object_type', true);
    }

    /**
     * function get_category_array($data)
     * @param object $data
     * @return array Retorna um array com o nome e o id do parent
     * metodo responsavel em retornar um termo em array, se o parent for a categoria root retorna vazio;
     * @author Eduardo Humberto 
     */
    public function get_category_array($data) {
        $array = [];
        if ($data->name == 'socialdb_category' || $data->name == 'socialdb_taxonomy') {
            $array['term_id'] = $data->term_id;
            $array['name'] = $data->name;
            $array['parent'] = $data->parent;
            return $array;
        } else {
            $array['term_id'] = $data->term_id;
            $array['name'] = $data->name;
            $array['parent'] = $data->parent;
            return $array;
        }
    }

    /**
     * function get_parent($data)
     * @param array $data
     * @return object
     * metodo responsavel em retornar o termo pai de uma categoria
     * @author Eduardo Humberto 
     */
    public function get_parent($data) {
        $term = get_term_by('id', $data['category_id'], 'socialdb_category_type');
        $parent = get_term_by('id', $term->parent, 'socialdb_category_type');
        return $parent;
    }

    /* function vinculate_facets() */
    /* @param array $data os dados vindo da viw
      /* @return json */
    /* Funcao que vincula as categorias com a colecao
      /* @author: Eduardo */

    public function vinculate_facets($data) {
        $facets = explode(',', $data['facets']); // os ids das categorias a serem vinculadas
        $this->clean_facets($data['collection_id']);
        foreach ($facets as $facet) {
            add_post_meta($data['collection_id'], 'socialdb_collection_facets', $facet);
            if (!get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet . '_color')) {
                update_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet . '_color', 'color1');
            }
        }
        if (is_array($facets)) {
            $data['success'] = 'true';
        }
        return json_encode($data);
    }

    /* function add_facet($category_id,$collection_id) */
    /* @param int $category_id a id da categoria a ser adicionada como faceta
      /* @param int $collection_id o id da colecao que recebera a faceta
      /* @return json */
    /* Funcao que vincula as categorias com a colecao COMO FACETAS
      /* @author: Eduardo */

    public function add_facet($category_id, $collection_id) {
        $facets = get_post_meta($collection_id, 'socialdb_collection_facets');
        if (!$facets || (is_array($facets) && !in_array($category_id, $facets))) {
            add_post_meta($collection_id, 'socialdb_collection_facets', $category_id);
            add_post_meta($collection_id,'socialdb_collection_facet_' . $category_id . '_widget', 'tree');
            if (!get_post_meta($collection_id, 'socialdb_collection_facet_' . $category_id . '_color')) {
                add_post_meta($collection_id, 'socialdb_collection_facet_' . $category_id . '_color', 'color1');
            }
        }
    }

    /* function is_facet($category_id,$collection_id) */
    /* @param int $category_id a id da categoria a ser adicionada como faceta
      /* @param int $collection_id o id da colecao que recebera a faceta
      /* @return boolean */
    /* Funcao que vincula as categorias com a colecao COMO FACETAS
      /* @author: Eduardo */

    public function is_facet($category_id, $collection_id) {
        $facets = get_post_meta($collection_id, 'socialdb_collection_facets');
        if ($facets && is_array($facets) && in_array($category_id, $facets)) {
            return true;
        } else {
            return false;
        }
    }

    /* function delete_facet($category_id,$collection_id) */
    /* @param int $category_id a id da categoria a ser adicionada como faceta
      /* @param int $collection_id o id da colecao que recebera a faceta
      /* @return boolean */
    /* Funcao que vincula as categorias com a colecao COMO FACETAS
      /* @author: Eduardo */

    public function delete_facet($category_id, $collection_id) {
        if ($this->is_facet($category_id, $collection_id)) {
            delete_post_meta($collection_id, 'socialdb_collection_facets', $category_id);
            if (!get_post_meta($collection_id, 'socialdb_collection_facet_' . $category_id . '_color')) {
                delete_post_meta($collection_id, 'socialdb_collection_facet_' . $category_id . '_color');
            }
            return true;
        } else {
            return false;
        }
    }

    /* function clean_facets() */
    /* @param int $collection_id o id da colecao
      /* @return void */
    /* Funcao que apaga todas as facetas
      /* @author: Eduardo */

    public function clean_facets($collection_id) {
        delete_post_meta($collection_id, 'socialdb_collection_facets');
    }

    /* function get_metas($data) */
    /* @param array $collection_id o id da colecao
      /* @return json Retorna o json com os meta dados das categorias */
    /* Funcao que retorna todos os metas das categorias
      /* @author: Eduardo */

    public function get_metas($data) {
        global $wpdb;
        $data['category_id'] = str_replace('_facet_category', '',  $data['category_id']);
        $wp_taxonomymeta = $wpdb->prefix . "termmeta";
        $query = "SELECT * FROM $wp_taxonomymeta WHERE term_id = {$data['category_id']}";
        $category_datas = $wpdb->get_results($query);
        foreach ($category_datas as $category_data) {
            if (($category_data->meta_key == 'socialdb_category_property_id') && $category_data->meta_value != '') {
                if(!is_array($config[$category_data->meta_key])){
                    $config[$category_data->meta_key] = array();
                }
                $config[$category_data->meta_key][] = $category_data->meta_value;
            } elseif ($category_data->meta_key == 'socialdb_category_moderators' && $category_data->meta_value != '') {
                $user = $this->usermodel->get_user($category_data->meta_value);
                $config[$category_data->meta_key][] = $user;
            } elseif($category_data->meta_key == 'socialdb_term_synonyms' && $category_data->meta_value != ''){
                 $config[$category_data->meta_key] = $this->get_categories_hash($category_data->meta_value);
            }else {
                $config[$category_data->meta_key] = $category_data->meta_value;
            }
        }
        $config['term'] = get_term_by('id', $data['category_id'], 'socialdb_category_type');
        if(has_filter('modificate_returned_metas_categories')){
            $config = apply_filters('modificate_returned_metas_categories', ['config'=>$config,'all_metas'=>$category_datas]);
        }

        return $config;
    }

    /** function get_terms_in_array_by_taxonomy($data) 
     * @param array $array O array com ids de termos de diferentes taxonomia
     * @param string $taxonomy A taxonomia
     * @return array(object) Um array com os objetos term da txonomia especificado 
     * Funcao que retorna os termos de um array com diferentes taxonomias
     * @author: Eduardo 
     * */
    public function get_terms_object_in_array_by_taxonomy($array, $taxonomy = 'socialdb_category_type') {
        $terms = [];
        if (is_array($array) && !empty($array)) {
            foreach ($array as $term_id) {
                $term = get_term_by('id', $term_id, $taxonomy);
                if ($term) {
                    $terms[] = $term;
                }
            }
        }
        return $terms;
    }

    /** function get_properties($collection_id,$categories) 
     * @param int  $collection_id O id da colecao
     * @param array $categories O array de objetos de categoria
     * @return array  Com as propriedades disponiveis para estas categorias e para a colecao 
     * FUNCAO QUE INSTANCIA AS PROPRIEDADES
     * @author: Eduardo */
    public function get_properties($collection_id, $categories) {
        $all_properties_id = [];
        // pego as propriedades desde a categoria root ate chegar na categoria raiz
        // da colecao, desta forma
        $all_properties_id = $this->get_parent_properties($this->get_collection_category_root($collection_id), $all_properties_id, $this->get_collection_category_root($collection_id));
        if (!empty($categories)) {
            foreach ($categories as $category) {
                if (is_object($category)):
                    // busco as propriedades hierquicamente
                    $all_properties_id = $this->get_parent_properties($category->term_id, $all_properties_id, $this->get_collection_category_root($collection_id));
                endif;
            }
        }
        //$all_properties_id = $this->get_facets_properties($collection_id, $all_properties_id);
        //$all_properties_id = $this->get_collection_properties($collection_id, $all_properties_id);
        return array_unique($all_properties_id);
    }

    /** function get_parent_properties($collection_id,$all_properties_id) 
     * @param int  $term_id O id da categoria raiz da colecao
     * @param array $all_properties_id O array com ids das propriedades que ja foram encontradas ate o meomento
     * @param int $category_root_id O id da categoria raiz da colecao atual
     * @return array  Com os ids das propriedades encontradas ou chama recursivamente a funcao ate encontrar a categoria root 
     * @author: Eduardo */
    public function get_parent_properties($term_id, $all_properties_id, $category_root_id) {
        $term = get_term_by('id', $term_id, 'socialdb_category_type');
        // se nao for a categoria raiz
        if ($term_id != 0 && $term_id) {
            $properties = [];
            $properties_raw = get_term_meta($term->term_id, 'socialdb_category_property_id');
            if (is_array($properties_raw)) {
                foreach ($properties_raw as $property) {
                    if ($property && $property != '' && !$this->verify_default_property_term($property, $category_root_id)) {
                        $properties[] = $property;
                    }
                }
            }
            if ($properties && isset($properties[0]) && $properties[0] != '') {
                $all_properties_id = array_merge($all_properties_id, $properties);
            }
            return $this->get_parent_properties($term->parent, $all_properties_id, $category_root_id);
        } else {
            return $all_properties_id;
        }
    }

   

    /* function get_facets_properties($collection_id,$all_properties_id) */
    /* @param int  $collection_id O id da colecao
      /* @param array $categories O array com ids das propriedades que ja foram encontradas ate o meomento
      /* @return array  Os ids pas propriedades encontradas apenas nas facetas */
    /* @author: Eduardo */

    public function get_facets_properties($collection_id, $all_properties_id) {
        $facets = CollectionModel::get_facets($collection_id);
        if ($facets && $facets[0] != '') {
            foreach ($facets as $facet) {
                $properties = get_term_meta($facet, 'socialdb_category_property_id');
                if ($properties && isset($properties[0]) && $properties[0] != '') {
                    $all_properties_id = array_merge($all_properties_id, $properties);
                }
            }
        }
        return $all_properties_id;
    }

    /* funcao que busca um termo pelo seu slug */
    /* Receive ($slug) */
    /* Return a term from database */
    /* Autor: Eduardo */

    function get_term_by_slug($slug) {
        global $wpdb;
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $wp_terms = $wpdb->prefix . "terms";
        $query = "
            SELECT * FROM $wp_terms t
            INNER JOIN $wp_term_taxonomy tt ON t.term_id = tt.term_id
            WHERE t.slug like '{$slug}'";
        $termo = $wpdb->get_results($query);
        return $termo;
    }

    /** function get_category($terms_id) 
     * @param int  $term_id o id da categoria
     * @return object  o objeto da categoria
      @author: Eduardo */
    public function get_category($term_id) {
        return get_term_by('id', $term_id, 'socialdb_category_type');
    }

    /** function export_zip_taxonomies($terms_id) 
     * @param array  terms_id com as categorias serem exportadas
     * @param string O dir aonde sera gerado os   arquivos
     * @param string O id da colecao
     * @return array  Gera os arquivos xml dentro da pasta a ser zipada para exportacao
      @author: Eduardo */
    public function export_zip_taxonomies($terms_id,$dir = '',$collection_id = 0) {
        if($dir==''){
           $dir =  dirname(__FILE__) . '/../export';
        }
        if (!empty($terms_id)) {
            foreach ($terms_id as $term_id) {
                if(!is_numeric($term_id))
                    continue;
                ob_clean();
                $df = fopen($dir. '/package/taxonomies/' . $term_id . '.xml', 'w');
                $xml = '<?xml version="1.0" encoding="UTF-8"?>';
                $this->get_hierarchy_categories_xml($term_id, $xml, '',$collection_id);
                fwrite($df, $xml);
                fclose($df);
            }
        }
    }

    /* function get_facets_properties($collection_id,$all_properties_id) */
    /* @param int  $collection_id O id da colecao
      /* @param array $categories O array com ids das propriedades que ja foram encontradas ate o meomento
      /* @return array  Os ids pas propriedades encontradas apenas nas facetas */
    /* @author: Eduardo */

    public function export_hierarchy($data) {
        $roots = ['0', $this->get_category_root(), 'user_categories'];
        $this->download_send_headers('hierarchy.xml');
        ob_clean();
        $df = fopen("php://output", 'a+');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        if (!in_array($data['root_category_id'], $roots)) {
            $this->get_hierarchy_categories_xml($data['root_category_id'], $xml, 'user');
        } elseif ($data['root_category_id'] == 'public_categories') {
            $this->get_hierarchy_categories_xml($this->get_category_root(), $xml, 'public');
        } elseif ($data['root_category_id'] == 'shared_categories') {
            $this->get_hierarchy_categories_xml($this->get_category_root(), $xml, 'shared');
        } else {
            $this->get_hierarchy_categories_xml($this->get_category_root(), $xml, 'user');
        }
        fwrite($df, $xml);
        fclose($df);
    }

    /* function get_facets_properties($collection_id,$all_properties_id) */
    /* @param int  $collection_id O id da colecao
      /* @param array $categories O array com ids das propriedades que ja foram encontradas ate o meomento
      /* @return array  Os ids pas propriedades encontradas apenas nas facetas */
    /* @author: Eduardo */

    public function insert_hierarchy($data) {
        session_write_close();
        ini_set('max_execution_time', '0');
        $roots = ['0', $this->get_category_root(), 'user_categories'];
        if (isset($_FILES['xml']) && ($_FILES['xml']['error'] == UPLOAD_ERR_OK)) {
            try {
                $xml = simplexml_load_file($_FILES['xml']['tmp_name']);
                if (!in_array($data['root_category_id'], $roots)) {
                    return $this->add_hierarchy($xml, $data['collection_id'], $data['root_category_id']);
                } else {
                    return $this->add_hierarchy($xml, $data['collection_id'], $this->get_category_root());
                }
            } catch (Exception $e) {
                $data['title'] = __('Error', 'tainacan');
                $data['msg'] = __('Xml unformated', 'tainacan');
                $data['type'] = 'error';
                return $data;
            }
        } else {
            $data['title'] = __('Error', 'tainacan');
            $data['msg'] = __('File corrupted', 'tainacan');
            $data['type'] = 'error';
            return $data;
        }
    }

    /**
     * function verify_has_children($data)
     * @param mix $data  Os dados vindos do formulario
     * @return array Com os dados que mostram se a categoria possui filhos  
     * 
     * Autor: Eduardo Humberto 
     */
    public function verify_has_children($data) {
        $children_of_child = $this->getChildren($data['category_id']);
        if (count($children_of_child) > 0 || (!empty($children_of_child) && $children_of_child)) {// se tiver descendentes
            $data['title'] = __('Success', 'tainacan');
            $data['msg'] = get_term_by('id', $data['category_id'], 'socialdb_category_type')->name . ' ' . __(' successfully selected!', 'tainacan');
            $data['type'] = 'success';
        } else {// se nao tiver filhos
            $data['title'] = __('Attention', 'tainacan');
            $data['msg'] = __('This category has no category children', 'tainacan');
            $data['type'] = 'error';
        }
        return $data;
    }

    /**
     * get_hierarchy_categories($parent_id,$field = 'term_id',&$result)
     * @param  int $parent_id $data  Os dados vindos do formulario
     * @param array Com os dados os filhos ja encontrados ate o momento
     * 
     * @author Eduardo Humberto 
     */
    public function get_hierarchy_categories($parent_id, &$result) {
        $children = $this->get_category_children($parent_id);
        if (!empty($children) && is_array($children)) {
            foreach ($children as $child) {
                $children_of_child = $this->get_category_children($child);
                if (!empty($children_of_child) && is_array($children_of_child)) {
                    $this->get_hierarchy_categories($child, $result);
                    $result[] = $child;
                } else {
                    $result[] = $child;
                }
            }
        }
    }

    /**
     * get_hierarchy_categories_xml($parent_id,$field = 'term_id',&$result)
     * @param  int $parent_id $data  Os dados vindos do formulario
     * @param array Com os dados os filhos ja encontrados ate o momento
     * @return void eh atribuido dinamicamente no xml a ser printado
     * @author Eduardo Humberto 
     */
    public function get_hierarchy_categories_xml($parent_id, &$xml, $type,$collection_id = 0) {
        $term = get_term_by('id', $parent_id, 'socialdb_category_type');
        $xml .= '<node id="' . $term->term_id . '" label="' . $term->name . '">';
        $xml = $this->insert_properties_category($term->term_id,$xml,$collection_id);
        $children = $this->get_category_children($parent_id);
        if (!empty($children) && is_array($children)) {
            $xml .= '<isComposedBy>';
            foreach ($children as $child) {
                //verificando o tipo de categoria que esta seedo exportado
                if ($type == 'user' && $this->get_category_root() == $parent_id && get_current_user_id() != get_term_meta($child, 'socialdb_category_owner', true)) {
                    continue;
                } elseif ($type == 'shared' && $this->get_category_root() == $parent_id && get_term_meta($child, 'socialdb_category_moderators') && !in_array($child, get_term_meta($child, 'socialdb_category_moderators'))) {
                    continue;
                } elseif ($type == 'public' && $this->get_category_root() == $parent_id && get_term_meta($child, 'socialdb_category_permission', true) != 'public') {
                    continue;
                }
                $child_term = get_term_by('id', $child, 'socialdb_category_type');
                // $xml .= '<node id="'.$child_term->term_id.'" label="'.$child_term->name.'">';
                $children_of_child = $this->get_category_children($child);
                //$xml .= '<isComposedBy>';
                if (!empty($children_of_child) && is_array($children_of_child)) {
                    $this->get_hierarchy_categories_xml($child, $xml, $type);
                } else {
                    $xml .= '<node id="' . $child_term->term_id . '" label="' . $child_term->name . '">';
                    $xml = $this->insert_properties_category($child,$xml,$collection_id);
                    $xml .= '</node>';
                }
                //$xml .= '</isComposedBy>';
                //$xml .= '</node>';
            }
            $xml .= '</isComposedBy>';
        }
        $xml .= '</node>';
    }
    
    /**
     * metodo que adiciona os metadados de uma categoria se existir
     * 
     * @param type $term_id
     * @param type $xml
     * @return string
     */
    public function insert_properties_category($term_id,$xml,$collection_id) {
        $properties = [];
        $properties_raw = get_term_meta($term_id, 'socialdb_category_property_id');
        if($properties_raw  &&  is_array($properties_raw) && $term_id != $this->get_category_root_of($collection_id)){
            $properties_raw = array_unique(array_filter($properties_raw));
            foreach ($properties_raw as $property_id) {
                $properties[] = $property_id;
            }
        }
        if(count($properties)>0){
            $xml .= '<properties>';
            $xml = $this->generate_properties_xml($properties, $xml);
            $xml .= '</properties>';
        }
        return $xml;
    }

    /**
     * verify_name_in_taxonomy($data)
     * @param array $data Com os dados da categoria a ser verificada
     * @return json com a informacao se a categoria podera ser criada, 
     * se ja possui uma categoria com este nome nesta hierarquia ou se ja existe
     * uma categoria com este pai e com este nome 
     * @author Eduardo Humberto 
     */
    public function verify_name_in_taxonomy($data) {
        // apenas verificando se o nome da categoria nao esta vaizo
        if (trim($data['suggested_name']) == '') {
            $result['msg'] = __('Category name is empty.', 'tainacan');
            $result['title'] = __('Error', 'tainacan');
            $result['type'] = 'error';
            return json_encode($result);
        }
        $result = [];
        // se estiver editando alguma categoria, ou seja existir algum id
        // para esta categoria
        if ($data['category_id'] != '') {
            $term = get_term_by('id', $data['category_id'], 'socialdb_category_type');
            if ($term->name == $data['suggested_name'] && $term->parent == $data['parent_id']) {
                $is_new = false;
                $result['type'] = 'success';
                return json_encode($result);
            } else {
                $is_new = !$this->verify_category(['category_id' => $data['category_id'], 'category_parent_id' => $data['parent_id'], 'category_name' => $data['suggested_name']]);
            }
        }
        //se eh uma nova categoria
        else {
            $is_new = $this->verify_category(['category_id' => $data['category_id'], 'category_parent_id' => $data['parent_id'], 'category_name' => $data['suggested_name']]);
        }

        if ($is_new) {
            $result['msg'] = __('There is a category with this name and this parent', 'tainacan');
            $result['title'] = __('Error', 'tainacan');
            $result['type'] = 'error';
            return json_encode($result);
        }
        // se a execucao do metodo chegar a este ponto, a categoria nao possui o
        // mesmo nome de nenhum filho direto deste pai
        // se o parent for o a categoria root isto eh, a de pimeiro nivel da arvore,
        // eh permitido criar categorias com o mesmo nome
        if ($data['parent_id'] != 'user_categories' && $data['parent_id'] != '0' && $data['parent_id'] != $this->get_category_root()) {
            $this->verify_name_in_hierarchy($data);
        }
        $result['type'] = 'success';
        return json_encode($result);
    }

    /**
     * verify_name_in_hierarchy(array('category_id','parent_id'))
     * @param array $data Com os dados da categoria a ser verificada
     * @return false ou um json caso exista este nome na hierarquia
     * @author Eduardo Humberto 
     */
    public function verify_name_in_hierarchy($data) {
        $hierarchy_ids = [];
        $this->get_hierarchy_categories($data['parent_id'], $hierarchy_ids);
        if (!empty($hierarchy_ids)) {
            foreach ($hierarchy_ids as $term_id) {
                $term = get_term_by('id', $term_id, 'socialdb_category_type');
                if ($term && (sanitize_title(remove_accent($term->name)) == sanitize_title(remove_accent($data['suggested_name'])))) {
                    $result['msg'] = __('There is a category with this name in this hierarchy, are you sure to create another? Cancel edit the category, ok creates the new one.', 'tainacan');
                    $result['title'] = __('Attention', 'tainacan');
                    $result['type'] = 'info';
                    $result['id'] = $term_id;
                    if (!isset($data['category_id']) || $data['category_id'] != $term->term_id) {
                        return json_encode($result);
                    }
                }
            }
        }
        return false;
    }
    /**
     * Metodo que executa as atividades da tela de criacao de taxonomia
     * @param array $data
     */
    public function taxonomy_zone($data) {
        if(!function_exists('str_get_html')){
            include_once (dirname(__FILE__) . '../../../extras/SimpleHTMLDomParser/simple_html_dom.php');        
        }
        //$category_root_id = $this->get_category_root_of($data['collection_id']);
        $category_meta = get_post_meta($data['collection_id'], 'socialdb_collection_subject_category', true);
        if($category_meta){
           $category_root_id = get_term_by('id', $category_meta,'socialdb_category_type')->term_id;
        }else{
            $category_root_id = $this->get_category_root_of($data['collection_id']);
        }
        //alterar o nome da categoria raiz
        if($data['category_root_name']&&trim($data['category_root_name'])!=''){
            wp_update_term($category_root_id, 'socialdb_category_type', array(
                'name' => $data['category_root_name']
            ));
            $has_property = get_term_meta($category_root_id, 'socialdb_category_property_change_label', true);
            if($has_property && is_numeric($has_property)){
                 $update_category = wp_update_term($has_property, 'socialdb_property_type', array(
                'name' => $data['category_name']));
            }
        }
        //cria a taxonomia
        if($data['socialdb_property_term_new_taxonomy']&&trim($data['socialdb_property_term_new_taxonomy'])!=''){
            $html = str_get_html((stripslashes ( $data['socialdb_property_term_new_taxonomy'])));
            if($html->find( '.root_ul', 0)){
                foreach($html->find( '.root_ul', 0)->children() as $li){
                    $this->add_nodes_taxonomy($li,$category_root_id);
                }
            }
        }
        return json_encode($data);
    }
    
    /**
     * 
     * @param object $li
     */
    public function add_nodes_taxonomy($li,$parent_id = 0) {
        $name = $li->children(0)->plaintext;
        if($li->getAttribute('term')&&is_numeric($li->getAttribute('term'))):
           $array =   wp_update_term((int)$li->getAttribute('term'), 'socialdb_category_type', array(
                'name' => $name,
               'parent' => $parent_id
            ));
        else:
            $array = wp_insert_term(trim($name), 'socialdb_category_type', array('parent' => $parent_id,
                    'slug' => sanitize_title(remove_accent(trim($li->plaintext))).'_'.  mktime()));
        endif;
        $find = $li->find('ul',0);
        if($find){
            foreach($find->children() as $li_child){
                $this->add_nodes_taxonomy($li_child,$array['term_id']);
            }
        }
    } 
    

}
