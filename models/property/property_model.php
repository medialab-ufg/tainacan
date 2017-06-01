<?php

//use CollectionModel;
if (isset($_GET['by_function'])) {
    include_once (WORDPRESS_PATH . '/wp-config.php');
    include_once (WORDPRESS_PATH . '/wp-load.php');
    include_once (WORDPRESS_PATH . '/wp-includes/wp-db.php');
} else {
    include_once (dirname(__FILE__) .'/../../../../../wp-config.php');
    include_once (dirname(__FILE__) .'/../../../../../wp-load.php');
    include_once (dirname(__FILE__) .'/../../../../../wp-includes/wp-db.php');
}

include_once(dirname(__FILE__) . '../../general/general_model.php');
include_once (dirname(__FILE__) . '../../collection/collection_model.php');
include_once (dirname(__FILE__) . '../../category/category_model.php');
if(!function_exists('str_get_html')){
    include_once (dirname(__FILE__) . '../../../extras/SimpleHTMLDomParser/simple_html_dom.php');
}
class PropertyModel extends Model {

    var $collectionModel;
    var $categoryModel;

    public function __construct() {
        $this->collectionModel = new CollectionModel();
        $this->categoryModel = new CategoryModel();
    }
    
    /**
     * function add_property_term($data)
     * @param mix $data Os dados vindos via ajax para a insercao da propriedade de termo
     * @return json  para mostrar o resultdo insercao
     * 
     * @autor: Eduardo Humberto 
     */
    public function add_property_term($data) {
        if (isset($data['property_term_name']) && !empty($data['property_term_name'])) {
            $id_slug = $data['collection_id'];
            if (isset($data['property_category_id'])&&$this->get_category_root_of($data['collection_id']) != $data['property_category_id']) {// verifico se eh a categoria root onde sera inserido a propriedade
                $id_slug .= '_property' . $data['property_category_id'];
            }
            $is_new = $this->verify_property($data['property_term_name'],$id_slug);
            if(!$is_new){
                $new_property = wp_insert_term($data['property_term_name'], 'socialdb_property_type', array('parent' => $this->get_property_type_id('socialdb_property_term'),
                'slug' => $this->categoryModel->generate_slug($data['property_term_name'], $id_slug)));
            }
            
        }
        //apos a insercao
        if (!is_wp_error($new_property)&&isset($new_property['term_id'])) {// se a propriedade foi inserida com sucesso
            instantiate_metas($new_property['term_id'], 'socialdb_property_term', 'socialdb_property_type', true);
            $this->add_property_position_ordenation($data['collection_id'], $new_property['term_id'],$data['property_tab']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_collection_id', $data['collection_id']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_required', $data['property_term_required']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_term_cardinality', $data['socialdb_property_term_cardinality']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_term_widget', $data['socialdb_property_term_widget']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_visualization',$data['property_visualization']);
            update_term_meta($new_property['term_id'], 'socialdb_property_locked',$data['property_locked']);
            update_term_meta($new_property['term_id'], 'socialdb_property_habilitate_new_category',$data['property_habilitate_new_category']);
            //adicionando a categoria raiz
            if($data['socialdb_property_vinculate_category']=='create'&&$data['socialdb_property_new_category']){
                $category_id = $this->add_category_root_property_term($data['socialdb_property_new_category']);
                if($category_id):
                    $data['socialdb_property_term_root'] = $category_id;
                    $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_term_root',$category_id);
                    if($data['socialdb_property_new_taxonomy']):
                        $html = str_get_html($data['socialdb_property_new_taxonomy']);
                        foreach($html->find( '.root_ul', 0)->children() as $li){
                            $this->add_taxonomy_property_term($li,$category_id);
                        }
                    endif;
                endif;
            }else{
               $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_term_root', $data['socialdb_property_term_root']);
            }
            //adicionando a cor da faceta
            update_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $data['socialdb_property_term_root'] . '_color', 'color13');
            if($data['socialdb_property_default_value']){
                 $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_default_value', $data['socialdb_property_default_value']);
            }
            if($data['socialdb_property_help']){
                $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_help', $data['socialdb_property_help']);
            }
            
            if(!isset($data['property_category_id'])){
                $data['property_category_id'] = $this->get_category_root_of($data['collection_id']);
            }
            $result[] = $this->vinculate_property($data['property_category_id'], $new_property['term_id']); // vinculo com a colecao/categoria
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_created_category', $data['property_category_id']);// adiciono a categoria de onde partiu esta propriedade
            $data['property_id'] =$new_property['term_id'];
            //possivelmente um problema
            $this->vinculate_objects_with_property($new_property['term_id'], $data['collection_id'], $data['property_category_id']);
             // se for propriedades do repositorio
//            if($this->is_repository_property($data['property_category_id'])){
//                $this->insert_property_repository($data['property_id']);
//            }else{// se possuir colecoes filhas
//                $this->insert_properties_hierarchy($data['property_category_id'], $data['property_id']);
//            }
            if (!in_array(false, $result)) {
                $data['success'] = 'true';
            } else {
                $data['success'] = 'false';
            }
        } else {
            $data['success'] = 'false';
            if($is_new){
                $data['msg'] = __('There is another property with this name!','tainacan');
            }
        }
        return json_encode($data);
    }

    /**
     * function add_property_data($data)
     * @param mix $data  O id do colecao
     * @return json  
     * 
     * @autor: Eduardo Humberto 
     */
    public function add_property_data($data) {
        if (isset($data['property_data_name']) && !empty($data['property_data_name'])) {
            $id_slug = $data['collection_id'];
            // verifico se eh a categoria root onde sera inserido a propriedade
            if (isset($data['property_category_id'])&&$this->get_category_root_of($data['collection_id']) != $data['property_category_id']) {
                $id_slug .= '_property' . $data['property_category_id'];
            }
            $is_new = $this->verify_property($data['property_data_name'],$id_slug);
            if(!$is_new){
                $new_property = wp_insert_term($data['property_data_name'], 'socialdb_property_type', array('parent' => $this->get_property_type_id('socialdb_property_data'),
                'slug' => $this->categoryModel->generate_slug($data['property_data_name'], $id_slug)));
            }
            
        }
        //apos a insercao
        if (!is_wp_error($new_property)&&isset($new_property['term_id'])) { // se a propriedade foi inserida com sucesso
            instantiate_metas($new_property['term_id'], 'socialdb_property_data', 'socialdb_property_type', true);
            $this->add_property_position_ordenation($data['collection_id'], $new_property['term_id'],$data['property_tab']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_collection_id', $data['collection_id']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_required', $data['property_data_required']);
             update_term_meta($new_property['term_id'], 'socialdb_property_data_mask', $data['property_data_mask']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_data_widget', $data['property_data_widget']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_data_cardinality', $data['socialdb_property_data_cardinality']);
            update_term_meta($new_property['term_id'], 'socialdb_property_visualization',$data['property_visualization']);
            update_term_meta($new_property['term_id'], 'socialdb_property_locked',$data['property_locked']);
            if($data['socialdb_property_data_help']!=''):
                $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_help', $data['socialdb_property_data_help']);
            else:
                 $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_help', ' ');
            endif;
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_data_column_ordenation', 'false');
            if(!isset($data['property_category_id'])){
                $data['property_category_id'] = $this->get_category_root_of($data['collection_id']);
            }
            if($data['property_default_value']){
                 $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_default_value', $data['property_default_value']);
            }
            $result[] = $this->vinculate_property($data['property_category_id'], $new_property['term_id']); // vinculo com a colecao/categoria
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_created_category', $data['property_category_id']);
            
            $data['property_id'] =$new_property['term_id'];
            $data['new_property_id'] =$new_property['term_id'];
            //possivelmente um problema
            if($data['property_data_widget']=='autoincrement'){
                $this->vinculate_objects_with_property_autoincrement($new_property['term_id'], $data['collection_id'], $data['property_category_id']);
            }else{
                $this->vinculate_objects_with_property($new_property['term_id'], $data['collection_id'], $data['property_category_id']);
            }
            // se for propriedades do repositorio
//            if($this->is_repository_property($data['property_category_id'])){
//                //$this->insert_property_repository($data['property_id']);
//            }else{// se possuir colecoes filhas
//                //$this->insert_properties_hierarchy($data['property_category_id'], $data['property_id']);
//            }
            if (!in_array(false, $result)) {
                $data['success'] = 'true';
            } else {
                $data['success'] = 'false';
            }
        } else {
            $data['success'] = 'false';
            if($is_new){
                $data['msg'] = __('There is another property with this name!','tainacan');
            }
        }
        return json_encode($data);
    }
    
    /**
     * function verify_property($data)
     * @param mix $data  Os dados que serao utilizados para verificar a existencia da propriedade
     * metodo que verifica se a categoria realmente exise
     * Autor: Eduardo Humberto 
     */
    public function verify_property($name,$collection_id) {
        $array = socialdb_term_exists_by_slug($this->generate_slug($name,$collection_id), 'socialdb_property_type');
        if (!isset($array['term_id'])) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * function  create_property_data Cria uma propriedade de atributo somente pelo nome com valores padrao
     * @param string $name O nome da propriedade
      * @param int $collection_id  O id do colecao
     * @return json  
     * 
     * @autor: Eduardo Humberto 
     */
    public function create_property_data($name, $collection_id) {
        $data['name'] = $name;
        $data['collection_id'] = $collection_id;
        $data['property_category_id'] = $this->get_category_root_of($data['collection_id']);
        $data['property_data_required'] = 'false';
        $data['property_data_widget'] = 'text';
        $data['property_data_column_ordenation'] = 'true';
        return $this->add_property_data($data);
    }

    /**
     * function add_property_object($data)
     * @param mix $data  O id do colecao
     * @return json  
     * 
     * @autor: Eduardo Humberto 
     */
    public function add_property_object($data) {
        $data = $this->convert_data_object($data);
        if (isset($data['property_object_name']) && !empty($data['property_object_name']) ) {
            $id_slug = $data['collection_id'];
            if ($this->get_category_root_of($data['collection_id']) != $data['property_category_id']) {// verifico se eh a categoria root onde sera inserido a propriedade
                $id_slug .= '_property' . $data['property_category_id'];
            }
            $is_new = $this->verify_property($data['property_object_name'],$id_slug);
            if(!$is_new){
            $new_property = wp_insert_term($data['property_object_name'], 'socialdb_property_type', array('parent' => $this->get_property_type_id('socialdb_property_object'),
                'slug' => $this->categoryModel->generate_slug($data['property_object_name'], $id_slug)));
            }
        }
        //apos a insercao
        if (!is_wp_error($new_property)&&isset($new_property['term_id'])) {// se a propriedade foi inserida com sucesso
            instantiate_metas($new_property['term_id'], 'socialdb_property_object', 'socialdb_property_type', true);
             $this->add_property_position_ordenation($data['collection_id'], $new_property['term_id'],$data['property_tab']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_collection_id', $data['collection_id']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_required', $data['property_object_required']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_object_cardinality', $data['socialdb_property_object_cardinality']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_visualization',$data['property_visualization']);
            update_term_meta($new_property['term_id'], 'socialdb_property_locked',$data['property_locked']);
            update_term_meta($new_property['term_id'], 'socialdb_property_to_search_in',$data['property_to_search_in']);
            update_term_meta($new_property['term_id'], 'socialdb_property_avoid_items',$data['property_avoid_items']);
            update_term_meta($new_property['term_id'], 'socialdb_property_habilitate_new_item',$data['property_habilitate_new_item']);
            if($data['property_default_value']&&!empty($data['property_default_value'])){
                 $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_default_value', $data['property_default_value']);
            }
            //selecionando varios relacionamentos ao mesmo tempo
            if(strpos($data['property_object_category_id'], ',')!==false){
                $categories = array_filter(explode(',', $data['property_object_category_id']));
                foreach ($categories as $category) {
                     $result[] = add_term_meta($new_property['term_id'], 'socialdb_property_object_category_id', $category);
                }
            }else{
              $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_object_category_id', $data['property_object_category_id']);
            }
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_object_is_reverse', $data['property_object_is_reverse']);
            //$result[] = update_term_meta($new_property['term_id'], 'socialdb_property_object_is_facet', $data['property_object_facet']);
            // se faceta adiciona nas cores
            if ($data['property_object_facet'] == 'true') {
                add_post_meta($id_slug, 'socialdb_collection_facet_' . $new_property['term_id'] . '_color', 'color_property1');
            }
            //
            if(!isset($data['property_category_id'])){
                $data['property_category_id'] = $this->get_category_root_of($data['collection_id']);
            }
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_created_category', $data['property_category_id']);
            $result[] = $this->vinculate_property($data['property_category_id'], $new_property['term_id']); // vinculo com a colecao/categoria

            $data['new_property_id'] = $new_property['term_id'];

            //possivelmente um problema devido ao grande tempo de execucao
            $this->vinculate_objects_with_property($new_property['term_id'], $data['collection_id'], $data['property_category_id']);
            if ($data['property_object_is_reverse'] == 'true') {
                if ($data['property_object_is_reverse'] != 'false') {// se selecionou propriedade reversa e existir propriedades
                    $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_object_reverse', $data['property_object_reverse']);
                    // salvando na propriedade reversa
                    $this->update_reverse_metas($new_property['term_id'], $data['property_object_reverse'], $data['property_category_id']);
                } else {// se nao ele salva como nao existisse propriedade reversa
                    $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_object_is_reverse', 'false');
                }
            }
            // se for propriedades do repositorio ()
//            if($this->is_repository_property($data['property_category_id'])){
//                if($data['property_object_facet'] == 'true'){
//                    $this->insert_property_repository($new_property['term_id'],true);
//                }else{
//                     $this->insert_property_repository($new_property['term_id']);
//                }
//            }else{// se possuir colecoes filhas
//                if($data['property_object_facet'] == 'true'){
//                     $this->insert_properties_hierarchy($data['property_category_id'], $new_property['term_id'],true);
//                }else{
//                      $this->insert_properties_hierarchy($data['property_category_id'],$new_property['term_id']);
//                }
//               
//            }
            //validacao
            //if (!in_array(false, $result)) {
                $data['success'] = 'true';
            //} else {
              //  $data['success'] = 'false';
            //}
        } else {
            $data['success'] = 'false';
            if($is_new){
                $data['msg'] = __('There is another property with this name!','tainacan');
            }
        }
        return json_encode($data);
    }

    /**
     * function convert_data_object($data)
     * @param mix $data  O id do colecao
     * @return mix $data os   
     * @description funcao que convert os nomes semelhantes aos metadados no banco para ser inserido nas propriedades de objeto
     * @autor: Eduardo Humberto 
     */
    public function convert_data_object($data) {
        if (isset($data['socialdb_event_property_object_create_name']) && isset($data['socialdb_event_property_object_create_category_id'])) {
            $data['property_object_name'] = $data['socialdb_event_property_object_create_name'];
            $data['collection_id'] = $data['socialdb_event_collection_id'];
            $data['property_object_category_id'] = $data['socialdb_event_property_object_create_category_id'];
            $data['property_object_required'] = $data['socialdb_event_property_object_create_required'];
            $data['property_object_facet'] = $data['socialdb_event_property_object_create_is_facet'];
            $data['property_object_is_reverse'] = $data['socialdb_event_property_object_create_is_reverse'];
            $data['property_object_reverse'] = $data['socialdb_event_property_object_create_reverse'];
        }
        return $data;
    }

    /**
     * function update_property_data($data)
     * @param mix $data  Os dados que serao utilizados para atualizar nas proprieades
     * @return json com os dados atualizados 
     * metodo que atualiza os dados da propriedade
     * @autor: Eduardo Humberto 
     */
    public function update_property_data($data) {
        $category_created = get_term_meta($data['property_data_id'], 'socialdb_property_created_category', true);
        if($category_created&&$category_created!=$data['property_category_id']){ // verificando se a propriedade pertence a outra colecao
             $data['success'] = 'false';
             $data['msg'] = __('This property does not belong to this collection!','tainacan');
             return json_encode($data);
        }
        $id_slug = $data['collection_id'];
        if ($this->get_category_root_of($data['collection_id']) != $data['property_category_id']) {// verifico se eh a categoria root onde sera inserido a propriedade
            $id_slug .= '_property' . $data['property_category_id'];
        }
        $is_new = $this->verify_property($data['property_object_name'],$id_slug);
        //atualizando a propriedade
        if (!$is_new&&isset($data['property_data_name']) && !empty($data['property_data_name']) && !empty($data['property_data_id'])) {
            $new_property = wp_update_term($data['property_data_id'], 'socialdb_property_type', array(
                'name' => $data['property_data_name']
            ));
        }
        //apos a atualizacao
       if (!is_wp_error($new_property)&&isset($new_property['term_id'])) {// se a propriedade foi inserida com sucesso
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_collection_id', $data['collection_id']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_required', $data['property_data_required']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_data_widget', $data['property_data_widget']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_data_mask', $data['property_data_mask']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_help', $data['property_data_help']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_data_column_ordenation', $data['property_data_column_ordenation']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_data_cardinality', $data['socialdb_property_data_cardinality']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_visualization',$data['property_visualization']);
            update_term_meta($new_property['term_id'], 'socialdb_property_locked',$data['property_locked']);
            if($data['socialdb_property_default_value']){
                 $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_default_value', $data['socialdb_property_default_value']);
            }
            //possivelmente um problema
            $this->vinculate_objects_with_property($new_property['term_id'], $data['collection_id'], $data['property_category_id']);
            $data['success'] = 'true';
        } else {
            $data['success'] = 'false';
        }
        
        return json_encode($data);
    }

    /**
     * function update_property_object($data)
     * @param mix $data  Os dados que serao utilizados para atualizar nas proprieades
     * @return json com os dados atualizados 
     * metodo que atualiza os dados da propriedade
     * @autor: Eduardo Humberto 
     */
    public function update_property_object($data) {
        $category_created = get_term_meta($data['property_object_id'], 'socialdb_property_created_category', true);
        if($category_created&&$category_created!=$data['property_category_id']){ // verificando se a propriedade pertence a outra colecao
             $data['success'] = 'false';
             $data['msg'] = __('This property does not belong to this collection!','tainacan');
             return json_encode($data);
        }
        $id_slug = $data['collection_id'];
        if ($this->get_category_root_of($data['collection_id']) != $data['property_category_id']) {// verifico se eh a categoria root onde sera inserido a propriedade
            $id_slug .= '_property' . $data['property_category_id'];
        }
        $is_new = $this->verify_property($data['property_object_name'],$id_slug);
        //atualizando a propriedade
        if (!$is_new&&isset($data['property_object_name']) && !empty($data['property_object_name']) && !empty($data['property_object_id'])) {
            $new_property = wp_update_term($data['property_object_id'], 'socialdb_property_type', array(
                'name' => $data['property_object_name']
            ));
        }
        //apos a atualizacao
        if (!is_wp_error($new_property)&&isset($new_property['term_id'])) {// se a propriedade foi inserida com sucesso
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_collection_id', $data['collection_id']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_required', $data['property_object_required']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_object_is_reverse', $data['property_object_is_reverse']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_object_is_facet', $data['property_object_facet']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_object_cardinality', $data['socialdb_property_object_cardinality']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_visualization',$data['property_visualization']);
            update_term_meta($new_property['term_id'], 'socialdb_property_locked',$data['property_locked']);
            update_term_meta($new_property['term_id'], 'socialdb_property_to_search_in',$data['property_to_search_in']);
            update_term_meta($new_property['term_id'], 'socialdb_property_avoid_items',$data['property_avoid_items']);
            update_term_meta($new_property['term_id'], 'socialdb_property_habilitate_new_item',$data['property_habilitate_new_item']);
            if($data['property_default_value']&&!empty($data['property_default_value'])){
                 $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_default_value', $data['property_default_value']);
            }
            //selecionando varios relacionamentos ao mesmo tempo
            if(strpos($data['property_object_category_id'], ',')!==false){
                delete_term_meta($new_property['term_id'], 'socialdb_property_object_category_id');
                $categories = array_unique(array_filter(explode(',', $data['property_object_category_id'])));
                foreach ($categories as $category) {
                     $result[] = add_term_meta($new_property['term_id'], 'socialdb_property_object_category_id', $category);
                }
            }else{
              delete_term_meta($new_property['term_id'], 'socialdb_property_object_category_id');
              $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_object_category_id', $data['property_object_category_id']);
            }
            //atualizando a cor no dynatree
            if ($data['property_object_facet'] == 'true') {
                add_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $new_property['term_id'] . '_color', 'color_property1');
            } else {
                delete_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $new_property['term_id'] . '_color');
            }
            //verifico se eh reversa
            if ($data['property_object_is_reverse'] == 'true') {
                if ($data['property_object_is_reverse'] != 'false') {// se selecionou propriedade reversa e existir propriedades
                    $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_object_reverse', $data['property_object_reverse']);
                    $this->update_reverse_metas($new_property['term_id'], $data['property_object_reverse'],  $data['property_category_id']);
                } else {// se nao ele salva como nao existisse propriedade reversa
                    $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_object_is_reverse', 'false');
                }
            }
            //possivelmente um problema
            $this->vinculate_objects_with_property($new_property['term_id'], $data['collection_id'], $data['property_category_id']);
            $data['success'] = 'true';
        } else {
            $data['success'] = 'false';
        }
        return json_encode($data);
    }
    
    /**
     * function update_property_data($data)
     * @param mix $data  Os dados que serao utilizados para atualizar nas proprieades
     * @return json com os dados atualizados 
     * metodo que atualiza os dados da propriedade
     * @autor: Eduardo Humberto 
     */
    public function update_property_term($data) {
        $category_created = get_term_meta($data['property_term_id'], 'socialdb_property_created_category', true);
        if($category_created&&$category_created!=$data['property_category_id']){ // verificando se a propriedade pertence a outra colecao
             $data['success'] = 'false';
             $data['msg'] = __('This property does not belong to this collection!','tainacan');
             return json_encode($data);
        }
        //atualizando a propriedade
        if (isset($data['property_term_name']) && !empty($data['property_term_name']) && !empty($data['property_term_id'])) {
            $new_property = wp_update_term($data['property_term_id'], 'socialdb_property_type', array(
                'name' => $data['property_term_name']
            ));
        }
        //apos a atualizacao
       if (!is_wp_error($new_property)&&isset($new_property['term_id'])) {// se a propriedade foi inserida com sucesso
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_collection_id', $data['collection_id']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_required', $data['property_term_required']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_term_cardinality', $data['socialdb_property_term_cardinality']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_term_widget', $data['socialdb_property_term_widget']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_term_root', $data['socialdb_property_term_root']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_visualization',$data['property_visualization']);
            update_term_meta($new_property['term_id'], 'socialdb_property_locked',$data['property_locked']);
            update_term_meta($new_property['term_id'], 'socialdb_property_habilitate_new_category',$data['property_habilitate_new_category']);
            update_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $data['socialdb_property_term_root'] . '_color', 'color13');
            if($data['property_default_value']&&!empty($data['property_default_value'])){
                 $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_default_value', $data['property_default_value']);
            }
            if($data['socialdb_property_help']&&!empty($data['socialdb_property_help'])){
                $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_help', $data['socialdb_property_help']);
            }
            //possivelmente um problema
            $this->vinculate_objects_with_property($new_property['term_id'], $data['collection_id'], $data['property_category_id']);
            $data['success'] = 'true';
        } else {
            $data['success'] = 'false';
        }
        return json_encode($data);
    }

    /**
     * function update_reverse_metas($data)
     * @param int $property_id  O id da propriedade que foi inserido a propriedade reversa
     * @param int $property_reverse_id  O id da propriedade reversa
     * @return void metodo que atualiza os metas na propriedade reversa
     * @autor: Eduardo Humberto 
     */
    private function update_reverse_metas($property_id, $property_reverse_id,$category_root_id = 0) {
        $previously_reverse = get_term_meta($property_reverse_id, 'socialdb_property_object_reverse');
        if ($previously_reverse) {
            if ($previously_reverse[0] && $previously_reverse[0] != $property_id) {
                update_term_meta($previously_reverse[0], 'socialdb_property_object_is_reverse', 'false');
                update_term_meta($previously_reverse[0], 'socialdb_property_object_reverse', '');
            }
        }
        add_term_meta($property_reverse_id, 'socialdb_property_object_category_id', $category_root_id); 
        update_term_meta($property_reverse_id, 'socialdb_property_object_is_reverse', 'true');
        update_term_meta($property_reverse_id, 'socialdb_property_object_reverse', $property_id);
    }

    /**
     * function delete_reverse_metas($data)
     * @param int $property_id  O id da propriedade que foi inserido a propriedade reversa
     * @return void metodo que atualiza os metas na propriedade reversa
     * @autor: Eduardo Humberto 
     */
    private function delete_reverse_metas($property_id) {
        $previously_reverse = get_term_meta($property_id, 'socialdb_property_object_reverse');
        if ($previously_reverse[0]) {
            update_term_meta($previously_reverse[0], 'socialdb_property_object_is_reverse', 'false');
            update_term_meta($previously_reverse[0], 'socialdb_property_object_reverse', '');
        }
    }

    /**
     * function edit_property_data($data)
     * @param mix $data  Os dados que serao utilizados para buscar os dados da propriedade
     * @return json com os dados 
     * metodo que busca os todos os dados e metadados de uma propriedade
     * @autor: Eduardo Humberto 
     */
    public function edit_property($data) {
        $collection_id = ($data['collection_id'])? $data['collection_id'] : ''; 
        $data = $this->get_all_property($data['property_id'], true,$collection_id); // pego todos os dados possiveis da propriedade
        // $data['selected_menu_style_id'] = $this->get_selected_menu_style( $data['collection_id'] );
        return json_encode($data);
    }


    /** function delete() 
    * @param array $data Os dados vindos do evento
    * @return json com os dados da propriedade excluida.
    * exclui propriedade 
    * @author Eduardo */

    public function delete($data) {
        // busco a categoria de onde esta propriedade foi criada
        $category_created = get_term_meta($data['property_delete_id'], 'socialdb_property_created_category', true);
        $socialdb_property_term_root = get_term_meta($data['property_delete_id'], 'socialdb_property_term_root', true);
        //se esta categoria nao pertence a essa colecao ela pode ser excluida
        if($category_created&&$category_created!=$data['property_category_id']){ 
            delete_term_meta($data['property_category_id'], 'socialdb_category_property_id', $data['property_delete_id']);
            $this->delete_property_meta_data($data['property_delete_id']);
            wp_delete_term($data['property_delete_id'], 'socialdb_property_type');
            $data['success'] = 'false';
            $data['msg'] = __('This property does not belong to this collection!','tainacan');
            return json_encode($data);
        }
        // busco o objeto da propriedade
        $property = get_term_by('id', $data['property_delete_id'], 'socialdb_property_type');
        // se for propriedades do repositorio
//        $category_root_id = get_term_meta($property->term_id, 'socialdb_property_created_category', true);
//        if( $this->is_repository_property($category_root_id)){
//            //$this->delete_property_repository($data['property_delete_id']);
//        }else{
//             //$this->delete_properties_hierarchy($category_root_id, $data['property_delete_id']);
//        }
        if(strpos($property->slug, '_property')){
            $array = explode('_', $property->slug);
            $categogy_root_of_collection_id = str_replace('property', '', $array[2]);
        }else{
            $categogy_root_of_collection_id = $this->get_category_root_of($data['collection_id']);
            if('socialdb_property_term' == $this->get_property_type_hierachy($property->term_id)){
                delete_post_meta($data['collection_id'], 'socialdb_collection_facets',  get_term_meta($property->term_id, 'socialdb_property_term_root', true));
            }else{
                delete_post_meta($data['collection_id'], 'socialdb_collection_facets',$property->term_id);
            }
         }
        $this->delete_reverse_metas($data['property_delete_id']);
        //se estiver excluindo um metadado de uma colecao
        if (isset($data['property_delete_id']) && delete_term_meta($categogy_root_of_collection_id, 'socialdb_category_property_id', $data['property_delete_id']) && $this->delete_property_meta_data($data['property_delete_id']) && wp_delete_term($data['property_delete_id'], 'socialdb_property_type')) {
            $data['success'] = 'true';
            $this->remove_property_position_ordenation($data['collection_id'],$data['property_delete_id']);
            // se for ordenacao padrao do repositorio
            if($data['property_delete_id']==get_post_meta($data['collection_id'], 'socialdb_collection_default_ordering', true)){
                 $recent_property = get_term_by('slug', 'socialdb_ordenation_recent', 'socialdb_property_type');
                 update_post_meta($data['collection_id'], 'socialdb_collection_default_ordering', $recent_property->term_id);
                 update_post_meta($data['collection_id'], 'socialdb_collection_ordenation_form', 'desc');       
            }
        }
        //se estiver excluindo um metadado do repositorio
        else if(isset($data['property_delete_id'])&&get_term_by('id',$category_created, 'socialdb_category_type')&&get_term_by('id',$category_created, 'socialdb_category_type')->name=='socialdb_category'){
            delete_term_meta($category_created, 'socialdb_category_property_id', $data['property_delete_id']);
            $this->delete_property_meta_data($data['property_delete_id']);
            wp_delete_term($data['property_delete_id'], 'socialdb_property_type');
            if(get_term_by('id',$category_created, 'socialdb_category_type')->name=='socialdb_category'){
                $this->remove_facet_from_collections($data['property_delete_id'],$socialdb_property_term_root);
            }
        }
        //propriedade
        else if($category_created==$data['property_category_id']){
            delete_term_meta($category_created, 'socialdb_category_property_id', $data['property_delete_id']);
            $this->delete_property_meta_data($data['property_delete_id']);
            wp_delete_term($data['property_delete_id'], 'socialdb_property_type');
        }else {
            $data['success'] = 'false';
        }
        return json_encode($data);
    }

    /* function list_data() - metodo invocado quando a view list e chamada */
    /* @param array $data
      /* @return array com os dados das propriedades da colecao(categoria root) ou de suas subcategorias./
      /* @author Eduardo */
    public function list_data($data) {
        $category_property = $this->set_category($data); // seto a categoria de onde vira as propriedades a partir dos dados vindos da view
        $data['is_root'] = $this->is_category_root($data); // verifico se ela e a root da colecao
        $data['category'] = $category_property; // coloco no array que sera utilizado na view
        if ($data['is_root']||isset($data['is_configuration_repository'])) { // se for buscar as propriedades da categoria raiz
            $collections_user = $this->collectionModel->get_collection_by_user(get_current_user_id()); // busco todas as colecoes
            foreach ($collections_user as $collection_user) {// varro todas colecoes
                $collection_name = $collection_user->post_title; // pego o nome
                $category_id = $this->get_category_root_of($collection_user->ID);
                if (isset($category_id) && !empty($category_id)) {
                    $data['property_object'][] = array('collection_name' => $collection_name, 'category_id' => $category_id);
                }
            }
        }

        // Is repository metadata?
        if( is_null($data['collection_id']) ) {
            Log::addLog(['event_type' => 'admin', 'event' => 'metadata']);
        } else {
            Log::addLog(['collection_id' => $data['collection_id'], 'event_type' => 'collection_admin', 'event' => 'metadata']);
        }

        return $data;
    }

    /** function list_property_data()
     * @param array $data os dados vindo do formulario
     * @return json com os todos os dados (o termo mais o os metas) das propriedades de dados.
     * @author Eduardo
    **/

    public function list_property_data($data) {
        $collection_id = $data['collection_id'];
        $category_property = $this->set_category($data); // seto a categoria de onde vira as propriedades a partir dos dados vindos da view
        $data['tabs'] = $this->get_tabs_list($data['collection_id']);
        $data['is_root'] = $this->is_category_root($data); // verifico se ela é a root da colecao
        $data['category'] = $category_property; // coloco no array que sera utilizado na view
        //$properties_verification = $this->categoryModel->get_properties($collection_id, []);
       // if ($this->has_properties($category_property->term_id)||!empty($properties_verification)) {// verifico se existe propriedades
            //$all_properties_id = get_term_meta($category_property->term_id, 'socialdb_category_property_id');
            if($category_property->slug!='socialdb_category'&&$data['is_root']){
                $all_properties_id = $this->categoryModel->get_properties($collection_id, []);
            }else{
               $all_properties_id = array_unique($this->get_parent_properties($category_property->term_id, [],$category_property->term_id));
            }
            if(is_array($all_properties_id)){
                $all_properties_id = array_unique($all_properties_id);
                foreach ($all_properties_id as $property_id) {// varro todas propriedades
                    $type = $this->get_property_type_hierachy($property_id); // pego o tipo da propriedade
                    $all_data = $this->get_all_property($property_id,true, $collection_id); // pego todos os dados possiveis da propriedade;
                    if ($type === 'socialdb_property_data') {// pego o tipo
                        $data['property_data'][] = $all_data;
                        $data['no_properties'] = false;
                    }
                }
                if (!isset($data['no_properties'])) {
                    $data['no_properties'] = true;
                }
            }else{
                 $data['no_properties'] = true;
            }return json_encode($data);
    }

    /* function list_property_object() */
    /* @param array $data os dados vindo do formulario
      /* @return json com os todos os dados (o termo mais o os metas) das propriedades de objetos./
      /* @author Eduardo */

    public function list_property_object($data,$is_reverse = false) {

        $category_property = $this->set_category($data); // seto a categoria de onde vira as propriedades a partir dos dados vindos da view
        $data['tabs'] = $this->get_tabs_list($data['collection_id']);
        $data['is_root'] = $this->is_category_root($data); // verifico se ela e a root da colecao
        $data['category'] = $category_property; // coloco no array que sera utilizado na view
        //if ($this->has_properties($category_property->term_id)||!empty($this->categoryModel->get_properties($data['collection_id'], []))) {// verifico se existe propriedades
           
          //$all_properties_id = get_term_meta($category_property->term_id, 'socialdb_category_property_id');
            if($category_property->slug!='socialdb_category'&&!$is_reverse&&$data['is_root']){
                $all_properties_id = $this->categoryModel->get_properties($data['collection_id'], []);
            }
            // este ELSE eh necessario pois esta listagem pode ser utilizada
            // para visualizacao das propriedades de categorias, as quais nao
            // podem ser utilizadas com o id da colecao
            else{
                $all_properties_id = array_unique($this->get_parent_properties($category_property->term_id, [],$category_property->term_id));
            }
            
            foreach ($all_properties_id as $property_id) {// varro todas propriedades
                $type = $this->get_property_type_hierachy($property_id); // pego o tipo da propriedade
                $all_data = $this->get_all_property($property_id,true); // pego todos os dados possiveis da propriedade
                if ($type === 'socialdb_property_object') {// pego o tipo
                    $data['property_object'][] = $all_data;
                    $data['no_properties'] = false;
                }
            }
            if (!isset($data['no_properties'])) {
                $data['no_properties'] = true;
            }
        //} else {
        //    $data['no_properties'] = true;
        //}     
        return json_encode($data);
    }
    
   /**
     * function list_property_data($data)
     * @param array $data Os dados que fornecem informacoes para a montagem dos dados
     * @return array com os meta key das propriedades.
     * @author: Eduardo Humberto 
     */
    public function list_property_terms($data) {
        $category_property = $this->set_category($data); // seto a categoria de onde vira as propriedades a partir dos dados vindos da view
        $data['tabs'] = $this->get_tabs_list($data['collection_id']);
        $data['is_root'] = $this->is_category_root($data); // verifico se ela e a root da colecao
        $data['category'] = $category_property; // coloco no array que sera utilizado na view
        $collection_id = ($data['collection_id'])?$data['collection_id']:"";
       // if ($this->has_properties($category_property->term_id)||!empty($this->categoryModel->get_properties($data['collection_id'], []))) {// verifico se existe propriedades
            //$all_properties_id = get_term_meta($category_property->term_id, 'socialdb_category_property_id');
            if($category_property->slug!='socialdb_category'&&$data['is_root']){
                $all_properties_id = $this->categoryModel->get_properties($data['collection_id'], []);
            }else{
                $all_properties_id = array_unique($this->get_parent_properties($category_property->term_id, [],$category_property->term_id));
            }
            if(is_array($all_properties_id)){
                $all_properties_id = array_unique($all_properties_id);
                foreach ($all_properties_id as $property_id) {// varro todas propriedades
                    $type = $this->get_property_type_hierachy($property_id); // pego o tipo da propriedade
                    $all_data = $this->get_all_property($property_id,true,$collection_id); // pego todos os dados possiveis da propriedade
                    if ($type === 'socialdb_property_term') {// pego o tipo
                        $data['property_terms'][] = $all_data;
                        $data['no_properties'] = false;
                    }
                }
            }
            if (!isset($data['no_properties'])) {
                $data['no_properties'] = true;
            }
       // } else {
          //  $data['no_properties'] = true;
        //}
        return json_encode($data);
    }
    
    /** function list_property_compounds()
     * @param array $data os dados vindo do formulario
     * @return json com os todos os dados (o termo mais o os metas) das propriedades compostas.
     * @author Eduardo
     * */
    public function list_property_compounds($data) {
        $collection_id = $data['collection_id'];
        $category_property = $this->set_category($data); // seto a categoria de onde vira as propriedades a partir dos dados vindos da view
        $data['tabs'] = $this->get_tabs_list($data['collection_id']);
        $data['is_root'] = $this->is_category_root($data); // verifico se ela é a root da colecao
        $data['category'] = $category_property; // coloco no array que sera utilizado na view
        //$properties_verification = $this->categoryModel->get_properties($collection_id, []);
       // if ($this->has_properties($category_property->term_id)||!empty($properties_verification)) {// verifico se existe propriedades
            //$all_properties_id = get_term_meta($category_property->term_id, 'socialdb_category_property_id');
            if($category_property->slug!='socialdb_category'&&$data['is_root']){
                $all_properties_id = $this->categoryModel->get_properties($collection_id, []);
            }else{
               $all_properties_id = array_unique($this->get_parent_properties($category_property->term_id, [],$category_property->term_id));
            }
            if(is_array($all_properties_id)){
                $all_properties_id = array_unique($all_properties_id);
                foreach ($all_properties_id as $property_id) {// varro todas propriedades
                    $type = $this->get_property_type_hierachy($property_id); // pego o tipo da propriedade
                    $all_data = $this->get_all_property($property_id,true, $collection_id); // pego todos os dados possiveis da propriedade;
                    if ($type === 'socialdb_property_compounds') {// pego o tipo
                        $data['property_compounds'][] = $all_data;
                        $data['no_properties'] = false;
                    }
                }
                if (!isset($data['no_properties'])) {
                    $data['no_properties'] = true;
                }
            }else{
                 $data['no_properties'] = true;
            }
            return json_encode($data);
    }
    
    /**
     * 
     * @param type $param
     */
    public function get_tabs_list($collection_id) {
        $value = get_post_meta($collection_id, 'socialdb_collection_update_tab_organization',true);
        if($value){
           $array =  unserialize($value);
           if(is_array($array)){
               try{
                   // unset($array[0]['']);
               } catch (Exception $e){
                   
               }
               return json_encode($array);
           }else{
               return json_encode(false);  
           }
        }else{
          return json_encode(false);  
        }
    }

    /* function get_property_object_facets() */
    /* @param int $category_root_id id da categoria raiz
      /* @return array com os dados e metadados das propriedades que são facetas./
      /* @author Eduardo */

    public function get_property_object_facets($category_root_id) {
        $data['property_object'] = array();
        $all_properties_id = get_term_meta($category_root_id, 'socialdb_category_property_id');
        foreach ($all_properties_id as $property_id) {// varro todas propriedades
            $type = $this->get_property_type($property_id); // pego o tipo da propriedade
            $all_data = $this->get_all_property($property_id, true); // pego todos os dados possiveis da propriedade
            if ($type == 'socialdb_property_object') {// verifico o tipo e se e faceta
                $data['property_object'][] = $all_data;
                $data['no_properties'] = false;
            }
        }
        return $data['property_object'];
    }
    /* function get_property_object_facets() */
    /* @param int $category_root_id id da categoria raiz
      /* @return array com os dados e metadados das propriedades que são facetas./
      /* @author Eduardo */

    public function get_property_data_facets($category_root_id) {
        $data['property_data'] = array();
        $all_properties_id = get_term_meta($category_root_id, 'socialdb_category_property_id');
        foreach ($all_properties_id as $property_id) {// varro todas propriedades
            $type = $this->get_property_type($property_id); // pego o tipo da propriedade
            $all_data = $this->get_all_property($property_id, true); // pego todos os dados possiveis da propriedade
            if ($type == 'socialdb_property_data') {// verifico o tipo e se e faceta
                $data['property_data'][] = $all_data;
                $data['no_properties'] = false;
            }
        }
        return $data['property_data'];
    }

    /* function set_category($data) */
    /* @param array $data
      /* @return object retorna categoria dona das propriedades seja ela a root do catalogo ou uma especifica./
      /* @author Eduardo */

    public function set_category($data) {
        if (isset($data['category_id']) && !empty($data['category_id'])) {// se por acaso esta buscando propriedades de uma categoria especifica
            $category = get_term_by('id', $data['category_id'], 'socialdb_category_type');
        } else {//se for as propriedades da colecao(category root)
            $cat_id = $this->get_category_root_of($data['collection_id']);
            $category = get_term_by('id', $cat_id, 'socialdb_category_type');
        }
        return $category;
    }

    /* function has_properties($category_id) */
    /* @param int $category_id o id da categoria
      /* @return boolean se existir propriedades./
      /* @author Eduardo */

    public function has_properties($category_id) {
        $metas = get_term_meta($category_id, 'socialdb_category_property_id');
        if(is_array($metas)){
            $metas = array_filter($metas);
        }
        if ($metas && !empty($metas[0])) {// se por acaso esta buscando propriedades de uma categoria especifica
            return true;
        } else {
            return false;
        }
    }

    /* function is_category_root($data) */
    /* @param array $data
      /* @return boolean se a categoria e uma categoria root./
      /* @author Eduardo */

    public function is_category_root($data) {
        if (isset($data['category_id']) && !empty($data['category_id'])) {// se por acaso esta buscando propriedades de uma categoria especifica
            return false;
        } else {//se for propreidades da categoria root da colecao
            return true;
        }
    }

    /* function get_property_type($property_id) */
    /* @param int o id da propriedade 
      /* @return string retorna o tipo da propriedade./
      /* @author Eduardo */

    public static function get_property_type($property_id) {
        $parent_id = get_term_by('id', $property_id, 'socialdb_property_type')->parent;
        $parent = get_term_by('id', $parent_id, 'socialdb_property_type');
        return $parent->name;
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
     * function get_property_type_id($property_parent_name)
     * @param int $id O id do objeto
     * @return array com os meta key das propriedades.
     * @author: Eduardo Humberto 
     */
    public function get_properties_by_object_id($id) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "
                        SELECT pm.meta_key FROM $wp_posts p
                        INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id    
                        WHERE pm.post_id = $id
                        AND pm.meta_key like 'socialdb_property_%'
                ";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result) && count($result) > 0) {
            $properties_id = array();
            foreach ($result as $property) {
                $properties_id[] = str_replace('socialdb_property_', '', $property->meta_key);
            }
            return $properties_id;
        } else {
            return array();
        }
    }

    /**
     * function get_object_property_value($object_id,$property_id)
     * @param int $object_id O id do objeto
     * @param int $property_id O id da propriedade
     * @return array com os metas ou false se estiver vazio.
     * @author: Eduardo Humberto 
     */
    public function get_object_property_value($object_id, $property_id) {
        $value = get_post_meta($object_id, 'socialdb_property_' . $property_id);
        $return = $this->eliminate_invalid_values($value);
        if ($return) {
            return $return;
        } else {
            return false;
        }
    }
    /**
     * function eliminate_invalid_values($postmeta)
     * @param array $postmeta O id do objeto
     * @return array com os metas validos ou false se estiver vazio.
     * @author: Eduardo Humberto 
     */
    public function eliminate_invalid_values($postmeta) {
          $array = [];
         if($postmeta&&is_array($postmeta)){
             foreach ($postmeta as $meta) {
                 if($meta&&trim($meta)!=''){
                     $array[] = trim($meta);
                 }
             }
             //se estiver vazio, todos os valores sao invalidos
             if(empty($array)){
                 return false;
             }else{
                 return $array;
             }
         }else{
             return false;
         }
    }
    
    
    /**
     * function insert_property_repository($property_id,$is_facet = false)
     * @param int $property_id O id do objeto
     * @param boolean $is_facet 
     * @return void
     * @author: Eduardo Humberto 
     */
    public function insert_property_repository($property_id,$is_facet = false){
         ini_set('max_execution_time', '0');
        $all_collections = $this->get_all_collections();
        foreach ($all_collections as $collection) {
            $category_root_id = get_post_meta($collection->ID, 'socialdb_collection_object_type',true);
            $metas = get_term_meta($category_root_id, 'socialdb_category_property_id');
            //if(!$metas||$metas[0]==''){
                //delete_term_meta($category_root_id, 'socialdb_category_property_id');
            //}
            //if(is_array($metas)&&!in_array($property_id, $metas)){
              //  add_term_meta($category_root_id, 'socialdb_category_property_id', $property_id);
               // if($is_facet){
                //     add_post_meta($collection->ID, 'socialdb_collection_facet_' . $property_id . '_color', 'color_property1');
                //}
            //}
            $this->vinculate_objects_with_property($property_id,$collection->ID, $category_root_id);
        }
    }
    /**
     * function delete_property_repository($property_id)
     * @param int $object_id O id do objeto
     * @param int $property_id O id da propriedade
     * @return array com os metas ou false se estiver vazio.
     * @author: Eduardo Humberto 
     */
     public function delete_property_repository($property_id){
        $all_collections = $this->get_all_collections();
        foreach ($all_collections as $collection) {
            $category_root_id = get_post_meta($collection->ID, 'socialdb_collection_object_type',true);
            delete_term_meta($category_root_id, 'socialdb_category_property_id', $property_id);
        }
    }
    
    /**
     * function get_children_property_terms($property_id)
     * @param array $data 
     * @return array com os metas ou false se estiver vazio.
     * @author: Eduardo Humberto 
     */
     public function get_children_property_terms($data){
        if(!session_id()) {
            session_start();
        }
        $cache = (isset($_SESSION['tainacan-categories']['children-'.$data['property_id']])) ? $_SESSION['tainacan-categories']['children-'.$data['property_id']] : false; 
        if(!$cache){
            $all_data = $this->get_all_property($data['property_id'],true); // pego todos os dados possiveis da propriedade  
            if($all_data['metas']['socialdb_property_term_root']){
               $ordenation = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $all_data['metas']['socialdb_property_term_root'] . '_ordenation', true);
               if($ordenation && $ordenation=='alphabetic'){
                   $all_data['children'] = $this->getChildren($all_data['metas']['socialdb_property_term_root'],'t.name ASC');
               }else{
                   $all_data['children'] = $this->getChildren($all_data['metas']['socialdb_property_term_root']);
               }
            }
            $_SESSION['tainacan-categories']['children-'.$data['property_id']] = $all_data;
        }else{
            $all_data = $cache;
        }
         return $all_data;
     }   
     /**
      * @signature get_properties_autocomplete($category,$type_property)
      * @param int $category O id da categoria aonde esta sendo criado a propriedade
      * @param string $type_property  O tipo de propriedade que esta sendo criado
      * @param string $search O termo a ser pesquisado
      * @return array com os metadados com as mesmas iniciais
      * @author: Eduardo Humberto  
      */
     public function get_properties_autocomplete($collection_id,$category,$type_property,$search) {
        $all_properties = [];
        $term_actual = get_term_by('id', $category,'socialdb_category_type');
        $facets_id = array_filter(array_unique((get_post_meta($collection_id, 'socialdb_collection_facets'))?get_post_meta($collection_id, 'socialdb_collection_facets'):[]));
        $array = [];
        $this->get_collection_properties($array,$term_actual->term_id,$facets_id);
        //busco as propriedades sem categoria
        $properties_with_no_domain = $this->list_properties_by_collection($collection_id);
        if($properties_with_no_domain&&is_array($properties_with_no_domain)){
            foreach ($properties_with_no_domain as $property_with_no_domain) {
                if(!in_array($property_with_no_domain->term_id, $array)){
                    $array[] = $property_with_no_domain->term_id;
                }
            }
        }
         if(!empty($array)){
             foreach ($array as $id) {
                 $type = $this->get_property_type_hierachy($id); // pego o tipo da propriedade
                 $all_data = $this->get_all_property($id,true); // pego todos os dados possiveis da propriedade
                 if(!$all_data['slug']){
                     continue;
                 }
                 if($type_property=='data'&&$type=='socialdb_property_data'){
                     $all_properties[$id] = array('label'=>$all_data['name'],'value'=>$id,'permalink'=>'');
                 }elseif($type_property=='object'&&$type=='socialdb_property_object'){
                     $all_properties[$id] = array('value'=>$id,'label'=>$all_data['name'],'permalink'=>'');
                 }elseif($type_property=='term'&&$type=='socialdb_property_term'){
                      $all_properties[$id] = array('value'=>$id,'label'=>$all_data['name'],'permalink'=>'');
                 }
             }
         }
         return json_encode($this->search_in_array('label', $all_properties, $search));
     }
    /**
      * @signature get_id_facet_fixed_properties($data)
      * @param array $data o array que vem com todos os metadados de um propriedades
      * @return array com os metadados já trabalhado
      *  
      * Este metodo consiste em retornar o id correto das propriedades fixas
      * 
      * 
      * @author: Eduardo Humberto  
      */
     public function get_id_facet_fixed_properties($data){
         if(isset($data['slug'])&&  in_array($data['slug'], $this->fixed_slugs)){
             switch ($data['slug']){
                 case 'socialdb_property_fixed_title':
                     $data['id'] = 'item_name';
                     break;
                 case 'socialdb_property_fixed_source':
                     $data['id'] = 'socialdb_object_dc_source';
                     break;
                 case 'socialdb_property_fixed_license':
                     $data['id'] = 'socialdb_license_id';
                     break;
                 case 'socialdb_object_dc_type':
                     $data['id'] = 'socialdb_object_dc_type';
                     break;
             }
         }
         
         return $data;
     }
    /**
     * 
     * @param object $li
     */
    public function add_taxonomy_property_term($li,$parent_id = 0) {
        $name = $li->children(0)->plaintext;
        $array = wp_insert_term(trim($name), 'socialdb_category_type', array('parent' => $parent_id,
                    'slug' => sanitize_title(remove_accent(trim($li->plaintext))).'_'.  mktime()));
        $find = $li->find('ul',0);
        if($find){
            foreach($find->children() as $li_child){
                $this->add_taxonomy_property_term($li_child,$array['term_id']);
            }
        }
    } 
    /**
     * 
     * @param object $li
     * @param type $parent_id
     */
    public function add_category_root_property_term($name) {
        $new_root_category = wp_insert_term($name, 'socialdb_category_type', array('parent' => $this->get_category_taxonomy_root(),
                'slug' => $this->generate_slug($name, 0)));
         add_term_meta($new_root_category['term_id'], 'socialdb_category_owner', get_current_user_id());
        return (isset($new_root_category['term_id']))?$new_root_category['term_id']:false;
        
    } 
    /**
     * 
     *  metodo que atualiza as abas de uma propriedade
     * 
     * function update_tab_organization($data)
     * @param int $collection_id
     * @param int $tab_id
     * @param int $property_id
     * @autor: Eduardo Humberto 
     */
    public function update_tab_organization($collection_id,$tab_id,$property_id) {
        $array = unserialize(get_post_meta($collection_id, 'socialdb_collection_update_tab_organization',true));
        if($array && is_array($array)):
            $array[0][$property_id] = $tab_id;
        else:
           $array = [];
           $array[0][$property_id] = $tab_id;     
        endif;
        update_post_meta($collection_id, 'socialdb_collection_update_tab_organization',  serialize($array));
    }
    
    /**
     * function add_property_compounds($data)
     * @param string $name O nome da propriedade
     * @param int $collection_id O id da colecao
     * @param int $category_id O id da categoria que sera vinculada a propriedade
     * @param string $properties_id As prorpeidades que compõe a categoria raiz
     * @param string $cardinality a ajuda para este tipo de metadado
     * @param string $help (optional) a ajuda para este tipo de metadado
     * @param string $required (optional) se o metadado eh obrigatorio
     * @return json  para mostrar o resultdo insercao
     * 
     * <b> Metodo que adiciona o metadado compostos </b>
     * 
     * @autor: Eduardo Humberto 
     */
    public function add_property_compounds($name,$collection_id,$category_id,$properties_id,$cardinality,$help = '',$required = 'false',$visualization = 'public',$tab_id = 'default') {
        if (!empty($name)) {
            $id_slug = $collection_id;
            if (isset($category_id)&&$this->get_category_root_of($collection_id) != $category_id) {// verifico se eh a categoria root onde sera inserido a propriedade
                $id_slug .= '_property' . $category_id;
            }
            $is_new = $this->verify_property($name,$id_slug);
            if(!$is_new){
                $new_property = wp_insert_term($name, 'socialdb_property_type', array('parent' => $this->get_property_type_id('socialdb_property_compounds'),
                'slug' => $this->categoryModel->generate_slug($name, $id_slug)));
            }
        }
        //apos a insercao
        if (!is_wp_error($new_property)&&isset($new_property['term_id'])) {// se a propriedade foi inserida com sucesso
            instantiate_metas($new_property['term_id'], 'socialdb_property_compounds', 'socialdb_property_type', true);
            $this->add_property_position_ordenation($collection_id, $new_property['term_id'],$tab_id);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_collection_id',$collection_id);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_required',($required) ? $required : 'false' );
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_compounds_cardinality', $cardinality);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_compounds_properties_id', $properties_id);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_help',($help) ? $help : ' ');
            $result[] = $this->vinculate_property($category_id, $new_property['term_id']); // vinculo com a colecao/categoria
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_created_category',$category_id);// adiciono a categoria de onde partiu esta propriedade
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_visualization',$visualization);// adiciono a categoria de onde partiu esta propriedade
            $data['property_id'] = $new_property['term_id'];
            //adiciona os metas nas propriedades que a compoe
            $this->update_properties_compounded($data['property_id'], $properties_id, 'true');
            //possivelmente um problema
            $this->vinculate_objects_with_property($new_property['term_id'], $collection_id,$category_id);
            if (!in_array(false, $result)) {
                $data['success'] = 'true';
            } else {
                $data['success'] = 'false';
            }
        } else {
            $data['success'] = 'false';
            if($is_new){
                $data['msg'] = __('There is another property with this name!','tainacan');
            }
        }
        return json_encode($data);
    }
    
    /**
     * 
     * @param int $property_id
     * @param type $name
     * @param type $collection_id
     * @param type $category_id
     * @param type $properties_id
     * @param type $cardinality
     * @param type $help
     * @param type $required
     * 
     * <b> Metodo que adiciona o metadado compostos </b>
     * @return json
     */
    public function update_property_compounds($property_id,$name,$collection_id,$category_id,$properties_id,$cardinality,$help = false,$required = false,$visualization = 'public') {
        $category_created = get_term_meta($property_id, 'socialdb_property_created_category', true);
        if($category_created&&$category_created!=$category_id){ // verificando se a propriedade pertence a outra colecao
             $data['success'] = 'false';
             $data['msg'] = __('This property does not belong to this collection!','tainacan');
             return json_encode($data);
        }
        $id_slug = $collection_id;
        if ($this->get_category_root_of($collection_id) != $category_id) {// verifico se eh a categoria root onde sera inserido a propriedade
            $id_slug .= '_property' . $category_id;
        }
        $is_new = $this->verify_property($name,$id_slug);
        //atualizando a propriedade
        if (!$is_new&&isset($name) && !empty($name) && !empty($property_id)) {
            $new_property = wp_update_term($property_id, 'socialdb_property_type', array(
                'name' => $name
            ));
        }
        // removo os metas das propriedades que eram vinculadas
        $properties_olds = get_term_meta($new_property['term_id'], 'socialdb_property_compounds_properties_id', true);
        $this->update_properties_compounded($property_id, $properties_olds, 'false');
        // apos a atualizacao
       if (!is_wp_error($new_property)&&isset($new_property['term_id'])) {// se a propriedade foi inserida com sucesso
            $data['property_id'] = $new_property['term_id'];
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_collection_id',$collection_id);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_required',$required);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_compounds_cardinality', $cardinality);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_compounds_properties_id', $properties_id);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_help',$help);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_created_category',$category_id);// adiciono a categoria de onde partiu esta propriedade
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_visualization',$visualization);// adiciono a categoria de onde partiu esta propriedade
            //possivelmente um problema
            $this->vinculate_objects_with_property($new_property['term_id'],$collection_id,$category_id);
            //adiciona os meta das propriedades que a compoe
            $this->update_properties_compounded($new_property['term_id'], $properties_id, 'true');
            $data['success'] = 'true';
        } else {
            $data['success'] = 'false';
        }
        return json_encode($data);
    }
    /**
     * 
     * @param int $property_id O id da propriedade compostas
     * @param array $array_propriedades Os ids da propriedades
     * @param string $value
     * 
     * metodo que atualiza os valores das propriedades compostas
     */
    public function update_properties_compounded($property_id,$array_propriedades, $value) {
        $ids = explode(',', $array_propriedades);
        if (is_array($ids)) {
            foreach ($ids as $id) {
                $meta = unserialize(get_term_meta($id, 'socialdb_property_is_compounds', true));
                if (!$meta || !is_array($meta)):
                    $meta = [];
                endif;
                $meta[$property_id] = $value;
                update_term_meta($id, 'socialdb_property_is_compounds', serialize($meta));
            }
        }
    }
}