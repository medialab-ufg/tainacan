<?php

//use CollectionModel;

include_once (dirname(__FILE__) . '/../../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../../wp-includes/wp-db.php');
include_once(dirname(__FILE__) . '/../../../../models/general/general_model.php');
include_once(dirname(__FILE__) . '/../../../../models/object/object_model.php');

class ArchivalManagementModel extends Model {
    
    /**
     * @signature generate_classification_plan($category_id)
     * @param int $category_id O id da categoria que sera gerado o texto para o arquivo
     * @return string O conteudo do aquivo
     */
    public function generate_classification_plan($category_id,&$string = '',$space='') {
        $term = get_term_by('id', $category_id, 'socialdb_category_type');
        $code = get_term_meta($category_id, 'socialdb_category_classification_code',true);
        $children = $this->get_category_children($category_id);
        $string .= $space.$code.' - '.$term->name. PHP_EOL;
        if(!empty($children)){
            $space .= '  ';
            foreach ($children as $child) {
                $this->generate_classification_plan($child, $string, $space);
            }
        }
        return $string;
    }
    /**
     * @signature generate_classification_plan($category_id)
     * @param int $category_id O id da categoria que sera gerado o texto para o arquivo
     * @return string O conteudo do aquivo
     */
    public function generate_table_of_temporality($category_id,&$string = '') {
        $term = get_term_by('id', $category_id, 'socialdb_category_type');
        $code = get_term_meta($category_id, 'socialdb_category_classification_code',true);
        // destinacao
        $destination = get_term_meta($category_id, 'socialdb_category_destination',true);
        if($destination=='permanent_guard'){
            $destination = __('Permanent guard','tainacan');
        }elseif($destination=='elimination'){
            $destination = __('Elimination','tainacan');
        }
        //busca a observacao
        $observation = $term->description;
        //calculo o tempo da fase corrente
        $current_phase = '#';
        $current_months = get_term_meta($category_id, 'socialdb_category_current_phase',true);
        if($current_months){
             $current_phase = floor($current_months/12).' anos';
        }
        if($current_months%12>0){
            $months = $current_months%12;
            $current_phase .= ' e '.$months.' meses';
        }
        //calculo o tempo da fase intermediaria
        $intermediate_phase = '#';
        $intermediate_months = get_term_meta($category_id, 'socialdb_category_intermediate_phase',true);
        if($intermediate_months){
             $intermediate_phase = floor($intermediate_months/12).' anos';
        }
        if($intermediate_months%12>0){
            $months = $intermediate_months%12;
            $intermediate_phase .= ' e '.$months.' meses';
        }
        //busco os filhos
        $children = $this->get_category_children($category_id);
        // monto a linha do arquivo
        $name = '';
        if((mb_detect_encoding($term->name)=='UTF-8')||mb_detect_encoding($term->name)=='ASCII'):
            $name = $term->name;
        elseif($term->name):
            $name =  utf8_encode($term->name);
        endif;
        //verifico a co
        if((mb_detect_encoding($observation)=='UTF-8')||mb_detect_encoding($observation)=='ASCII'):
            $observation = $observation;
        elseif($term->name):
            $observation =  utf8_encode($observation);
        endif;
        $string .= $code.' - '.$name.';'.$current_phase.';'.$intermediate_phase.';'.$destination.';'.$observation. PHP_EOL;
        if(!empty($children)){
            foreach ($children as $child) {
                $this->generate_table_of_temporality($child, $string);
            }
        }
        return $string;
    }
    /**
     * @signature get_items_to_transfer($data)
     * @param array $data
     * @return string Os items que estao
     */
    public function get_items_to_transfer($data,$posts) {
        $object = new ObjectModel;
        $array = [];
        $items = [];
        if($posts){
            foreach ($posts as $post) {
                $categories = $object->get_object_categories_id($post->ID);
                $category_archive_id = $this->get_archive_management_category($data['category_id'], $categories);
               if($category_archive_id){
                    $array['name'] = $post->post_title;
                    $creation_date_repository_property = get_term_by('slug', 'creation_date_repository_property', 'socialdb_property_type');
                    $array['date'] = get_post_meta($post->ID,'socialdb_property_'.$creation_date_repository_property->term_id,true);
                    // o status atual do item para verificar se ha necessidade de alteracao
                    $status_repository_property = get_term_by('slug', 'status_repository_property', 'socialdb_property_type');
                    $status = get_post_meta($post->ID,'socialdb_property_'.$status_repository_property->term_id,true);
                    $current_phase_time = get_term_meta($category_archive_id, "socialdb_category_current_phase",true);
                    $normalize = floor($current_phase_time/12).' anos';
                    if($current_phase_time%12>0){
                        $normalize.= ' e '.($current_phase_time%12).' meses';
                    }
                    $array['current_phase_time'] = $normalize;
                    // verificando o tempo
                    $d1 = new DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $array['date']))));
                    $d2 = new DateTime();
                    $object_time = $d2->diff($d1);
                    $months = 0;
                    // a quantidade de anos que ja se passaram desde a data de criacao ate agora
                    if($object_time->y>0){
                        $months = $object_time->y * 12;
                    }
                    // os meses
                    if($object_time->m>0){
                        $months += $object_time->m;
                    }
                    //expiration
                    if($months>$current_phase_time&&$status=='current'){
                        $array['expiration'] = $months-$current_phase_time;
                        $array['id'] = $post->ID;
                        $items[] = $array;
                    }
                    
                }
            }
        }
        return json_encode($items);
    }
    /**
     * @signature get_items_to_eliminate($data)
     * @param array $data
     * @return string Os items que estao
     */
    public function get_items_to_eliminate($data,$posts) {
        $object = new ObjectModel;
        $array = [];
        $items = [];
        if($posts){
            foreach ($posts as $post) {
                $categories = $object->get_object_categories_id($post->ID);
                $category_archive_id = $this->get_archive_management_category($data['category_id'], $categories);
                if($category_archive_id){
                    $array['name'] = $post->post_title;
                    //busco a propriedade que e responsavel em determinar a data de criacao do item
                    $creation_date_repository_property = get_term_by('slug', 'creation_date_repository_property', 'socialdb_property_type');
                    //busco a propriedade que e responsavel em determinar o status do item
                    $status_repository_property = get_term_by('slug', 'status_repository_property', 'socialdb_property_type');
                      $status = get_post_meta($post->ID,'socialdb_property_'.$status_repository_property->term_id,true);
                    // data de criacao do item
                    $array['date'] = get_post_meta($post->ID,'socialdb_property_'.$creation_date_repository_property->term_id,true);
                    //tempo da fase intermediaria
                    $intermediate_phase_time = get_term_meta($category_archive_id, "socialdb_category_intermediate_phase",true);
                   //arredonda o tempo intermediaria para baixo
                    $normalize = floor($intermediate_phase_time/12).' anos';
                    if($intermediate_phase_time%12>0){
                        $normalize.= ' e '.($intermediate_phase_time%12).' meses';
                    }
                    $array['intermediate_phase_time'] = $normalize;
                    // verificando quanto tempo foi passado da criacao ate o presente momento
                    $d1 = new DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $array['date']))));
                    $d2 = new DateTime();
                    $object_time = $d2->diff($d1);
                    $months = 0;
                    // a quantidade de anos que ja se passaram desde a data de criacao ate agora
                    if($object_time->y>0){
                        $months = $object_time->y * 12;
                    }
                     
                    // os meses
                    if($object_time->m>0){
                        $months += $object_time->m;
                    }
                    // aumentando  o tempo corrente
                     $current_phase_time = get_term_meta($category_archive_id, "socialdb_category_current_phase",true);
                     $intermediate_phase_time +=$current_phase_time;
                    //expiration
                    if($months>$intermediate_phase_time&&$status=='intermediate'){
                        $array['expiration'] = $months-$intermediate_phase_time;
                        $array['id'] = $post->ID;
                        $items[] = $array;
                    }
                    
                }
            }
        }
          return json_encode($items);
    }
    /**
     * @signature get_archive_management_category($category_id,$categories)
     * @param int $category_id
     * @param array $categories 
     * @return string/false O id da categoria que classifica
     */
    public function get_archive_management_category($category_id,$categories){
        if(is_array($categories)&&!empty($categories)){
            foreach ($categories as $id) {
                $hierarchies = array_reverse(get_ancestors($id, 'socialdb_category_type'));
                if($id!=$category_id&&in_array($category_id, $hierarchies)){
                    return $id;
                }
            }
        }
        return false;
    }
}
