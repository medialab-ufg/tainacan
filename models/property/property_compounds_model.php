<?php

include_once (dirname(__FILE__) . '/property_model.php');

class PropertyCompoundsModel extends PropertyModel {

    /**
     * function add_property_term($data)
     * @param mix $data Os dados vindos via ajax para a insercao da propriedade de termo
     * @return json  para mostrar o resultdo insercao
     * 
     * @autor: Eduardo Humberto 
     */
    public function add_property_compounds($data) {
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
            $this->add_property_position_ordenation($data['collection_id'], $new_property['term_id']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_collection_id', $data['collection_id']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_required', $data['property_term_required']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_term_cardinality', $data['socialdb_property_term_cardinality']);
            $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_term_widget', $data['socialdb_property_term_widget']);
            //adicionando a categoria raiz
            if($data['socialdb_property_vinculate_category']=='create'&&$data['socialdb_property_new_category']){
                $category_id = $this->add_category_root_property_term($data['socialdb_property_new_category']);
                if($category_id):
                    $data['socialdb_property_term_root'] = $category_id;
                    $result[] = update_term_meta($new_property['term_id'], 'socialdb_property_term_root',$category_id);
                    $html = str_get_html($data['socialdb_property_new_taxonomy']);
                    foreach($html->find( '.root_ul', 0)->children() as $li){
                        $this->add_taxonomy_property_term($li,$category_id);
                    }
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


}
