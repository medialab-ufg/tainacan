<?php
include_once (dirname(__FILE__) . '../../../models/collection/collection_model.php');
include_once (dirname(__FILE__) . '../../../models/license/license_model.php');
include_once (dirname(__FILE__) . '../../../models/property/property_model.php');
include_once (dirname(__FILE__) . '../../../models/category/category_model.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');

/**
 * The class ObjectModel
 * array exemplo helper = [
 *    [indice] 
 *          => [id-propriedade-filha-ou-zero]
 *                      => [type] => 'data',
 *                      => [values] => ['id-postmeta'] 
 * 
 * ]
 *
 */
class ObjectSaveValuesModel extends Model {
    
    public function removeValue($item_id,$compound_id,$property_children_id,$type,$index,$value) {
        $is_compound = ($property_children_id == 0) ? false : true;
        $property_children_id = ($property_children_id == 0) ? $compound_id : $property_children_id;
        $meta = get_post_meta($item_id, 'socialdb_property_helper_'.$compound_id, true);
        if($meta){
            $array = unserialize($meta);
            if(is_array($array) && isset($array[$index]) && isset($array[$index][$property_children_id]) && isset($array[$index][$property_children_id]['values'])){
                $values = $array[$index][$property_children_id]['values'];
                $updateValues = [];
                foreach ($values as $i => $meta_id) {
                    $meta = $this->sdb_get_post_meta($meta_id);
                    if($meta && $meta->meta_value != $value){
                        $updateValues[] = $meta_id;
                    }else if($meta && $meta->meta_value == $value){
                        // removo o valor do postmeta pelo meta_id
                        $this->sdb_delete_post_meta($meta->meta_id);
                        if($is_compound){
                            //removo o composto
                            $this->updateCompoundMeta($item_id, $compound_id, $property_children_id, $index, '');
                        }
                    }
                }
                $array[$index][$property_children_id]['values'] = $updateValues;
                update_post_meta($item_id, 'socialdb_property_helper_'.$compound_id, serialize($array));
            }
        }
        return json_encode(['date'=> date('d/m/y'),'hour'=> date('H:i:s')]);
    }
    
    /**
     * 
     * @param type $item_id
     * @param type $compound_id
     * @param type $property_children_id
     * @param type $type
     * @param type $index
     * @param type $value
     */
    public function removeIndexValue($item_id,$compound_id,$index) {
        $meta = get_post_meta($item_id, 'socialdb_property_helper_'.$compound_id, true);
        if($meta){
            $array = unserialize($meta);
            var_dump($array);
            if(is_array($array) && isset($array[(int)$index])){
                foreach ($array[$index] as $property_children_id => $type_and_values) {
                    $values = $array[$index][$property_children_id]['values'];
                    foreach ($values as $i => $meta_id) {
                        $meta = $this->sdb_get_post_meta($meta_id);
                        if($meta){
                            // removo o valor do postmeta pelo meta_id
                            $this->sdb_delete_post_meta($meta->meta_id);
                            delete_post_meta($item_id, 'socialdb_property_'.$compound_id.'_'.$index);
                        }
                    }
                }
                unset($array[intval(trim($index))]);
                update_post_meta($item_id, 'socialdb_property_helper_'.$compound_id, serialize($array));
            }
        }
        return json_encode(['date'=> date('d/m/y'),'hour'=> date('H:i:s')]);
    }
    /**
     * 
     * @param type $item_id
     * @param type $compound_id
     * @param type $property_children_id
     * @param type $type
     * @param type $index
     * @param type $value
     * @param type $indexCompound
     * @return boolean
     */
    public function saveValue($item_id,$compound_id,$property_children_id,$type,$index,$value,$indexCompound) {
        $meta = get_post_meta($item_id, 'socialdb_property_helper_'.$compound_id, true);
        if($meta){
            $array = unserialize($meta);
            // verifico se o metadato pai ja esta inserido , verifico se o indice tb ja esta inserido,
            // e se o metadado filho tb ja esta inserido naquele indice
            if(is_array($array) && isset($array[$index]) && isset($array[$index][$property_children_id])){
                // o tipo deste valor data,object ou term
                $type = $array[$index][$property_children_id]['type'];
                // array de valores (necessario se existir a necessidade de compostas com valores multivalorados)
                $values = $array[$index][$property_children_id]['values'];
                //busco o valor do postmeta bruto para ser atualizado
                $meta_value = (is_numeric($indexCompound) && isset($values[(int)$indexCompound])) ? $this->sdb_get_post_meta($values[(int)$indexCompound]) : false;
                //caso esse postmeta exista
                if($meta_value){
                    $this->updateValue($item_id, $meta_value, $compound_id, $property_children_id, $index, $value);
                }
                //caso nao exista esse, postmeta ele sera criado e salvo no helper
                else{
                    $meta_id = $this->createValue($item_id, $type, $compound_id, $property_children_id, $index, $value);
                    $array[$index][$property_children_id]['values'][] = $meta_id;
                }
            }else{
                $meta_id = $this->createValue($item_id, $type, $compound_id, $property_children_id, $index, $value);
                $new_children = [
                    'type' => $type,
                    'values' => [$meta_id]
                ];
                $array[$index][$property_children_id]= $new_children;
            }
        }else{
            $array = [];
            $meta_id = $this->createValue($item_id, $type, $compound_id, $property_children_id, $index, $value);
            $new_children = [
                'type' => $type,
                'values' => [$meta_id]
            ];
            $array[$index][$property_children_id]= $new_children;
        }
        update_post_meta($item_id, 'socialdb_property_helper_'.$compound_id, serialize($array));
        return json_encode(['date'=> date('d/m/y'),'hour'=> date('H:i:s')]);
    }
    
    /**
     * 
     * @param type $item_id
     * @param type $type
     * @param type $compound_id
     * @param type $property_children_id
     * @param type $index
     * @param type $value
     * @return int O meta_id criado com o valor inserido ou atualizado
     */
    public function createValue($item_id,$type,$compound_id,$property_children_id,$index,$value) {
        // caso seja um metadado simples/ se nao 
        $is_compound = ($property_children_id == 0) ? false : true;
        $property_children_id = ($property_children_id == 0) ? $compound_id : $property_children_id;
        if($type == 'term'){
            $meta_id = $this->sdb_add_post_meta($item_id, 'socialdb_property_'.$property_children_id.'_cat', $value);
            // adiciono no relacionamento do item
            wp_set_object_terms($item_id, array((int) $value), 'socialdb_category_type', true);
            //adciono no array comum de busca
            $this->set_common_field_values($item_id, "socialdb_propertyterm_$property_children_id", [(int) $value], 'term');
            if($is_compound){
                //quando o metadao e termo seu id sera colocado no  array
                $this->updateCompoundMeta($item_id, $compound_id, $property_children_id, $index, $value.'_cat');
            }
        }else{
            $meta_id = $this->sdb_add_post_meta($item_id, 'socialdb_property_'.$property_children_id, $value);
            if($is_compound){
                //neste caso sera o meta_id
                $this->updateCompoundMeta($item_id, $compound_id, $property_children_id, $index, $meta_id);
            }
        }
        return $meta_id;
    }


    /**
     * 
     * Metodo que atualiza o postmeta que possui o valor do campo que esta sendo editado
     * 
     * 
     * 
     * @param type $item_id O id do item a ser atualizado
     * @param (WP_POSTMETA) $meta_value O objeto postmeta encontrado
     * @param type $compound_id O id do metadado pai
     * @param type $property_children_id O id do metadado filho
     * @param type $index O indice atual que esta sendo usado
     * @param string $value O valor bruto vindo do formulario
     */
    public function updateValue($item_id,$meta_value,$compound_id,$property_children_id,$index,$value) {
        // caso seja um metadado simples/ se nao 
        $is_compound = ($property_children_id === 0) ? false : true;
        $property_children_id = ($property_children_id === 0) ? $compound_id : $property_children_id;
        // caso o postmeta esteja apontado para uma categoria seu meta_key sera socialdb-property_#_cat
        if(strpos($meta_value->meta_key, '_cat')!==false){
            //pego seu id
            $categories[] = (int) $meta_value->meta_value;
            //removo do relacionamento com o item
            wp_remove_object_terms($item_id, $categories, 'socialdb_category_type');
            // se o usuario estiver apenas alterando o valor
            if($value !== ''){
                // atualizo o valor do postmeta pelo meta_id
                $this->sdb_update_post_meta($meta_value->meta_id, $value);
                // adiciono no relacionamento do item
                wp_set_object_terms($item_id, array((int) $value), 'socialdb_category_type', true);
                //adciono no array comum de busca
                $this->set_common_field_values($item_id, "socialdb_propertyterm_$property_children_id", [(int) $value], 'term');
                //se for composto
                if($is_compound){
                    $this->updateCompoundMeta($item_id, $compound_id, $property_children_id, $index, $value.'_cat');
                }
            }else{
                $this->sdb_update_post_meta($meta_value->meta_id, $value);
            }
        }else if(strpos($meta_value->meta_key, 'socialdb_property_')!==false){
            $this->sdb_update_post_meta($meta_value->meta_id, $value);
            if($is_compound){
                $this->updateCompoundMeta($item_id, $compound_id, $property_children_id, $index, $meta_value->meta_id);
            }
        }
    }
    
    /**
     * 
     * Metodo responsavel em atualizar o meta que faz o ponteiro do metadado
     * composto
     * 
     * @param type $item O id do item a ser atualizado
     * @param type $compound_id O id do metadado composto
     * @param type $children_id O id do metadado filho da composta a ser atualizado
     * @param type $index O indice do metadado composto
     * @param type $newValue O meta_id ou id_da categoria concatenado com _cat
     */
    public function updateCompoundMeta($item,$compound_id,$children_id,$index,$newValue) {
        //busco as propriedades que compoe a composta
        $childrens = explode(',',get_term_meta($compound_id, 'socialdb_property_compounds_properties_id', true));
        //busco os valores no array concatenado dos metas
        $childrens_value = get_post_meta($item, 'socialdb_property_'.$compound_id.'_'.$index, true);
        // se ele ja existir devo atualizar o id em caso de ser categoria
        if($childrens_value){
            $childrens_value = explode(',', $childrens_value);
            foreach ($childrens as $index_value  => $child) {
                if($child == $children_id)
                    $childrens_value[$index_value] = $newValue;
            }
            update_post_meta($item, 'socialdb_property_' . $compound_id . '_' . $index, implode(',', $childrens_value));
        }else{
            $new_array = [];
            foreach ($childrens as $child) {
                if($child == $children_id){
                    $new_array[] = $newValue;
                }else{
                    $new_array[] = '';
                }
            }
            update_post_meta($item, 'socialdb_property_' . $compound_id . '_' . $index, implode(',', $new_array));
        }
        
    }
    
    
    

}
