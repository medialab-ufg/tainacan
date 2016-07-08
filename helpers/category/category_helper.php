<?php
/*
 * Category Controller's view helper 
 * */
class CategoryHelper extends ViewHelper {
    
    /**
     * Metodo responsavel em retornar o nome da categoria raiz
     * @param int $collection_id O id da colecao
     * @return string O nome da categoria
     */
    public function get_category_root_name($collection_id) {
        $id =  get_post_meta($collection_id, 'socialdb_collection_object_type', true);
        return ($id)?get_term_by('id', $id, 'socialdb_category_type')->name:'';
    }
    
    public function inserted_children($collection_id,$term_id = 0,$string = '') {
        if($term_id===0){
            $root = true;
            $term_id =  get_post_meta($collection_id, 'socialdb_collection_object_type', true);
        }else{
             $root = false;
        }
        //$termchildren = get_term_children( (int)$term_id, 'socialdb_category_type' );
        $termchildren = get_term_children( (int)$term_id, 'socialdb_category_type' );
        if($termchildren&&!empty($termchildren)):
            $class_root = ($root)? 'class="root_ul"' : '';
            $string .= '<ul '.$class_root.' >';
            foreach ( $termchildren as $child ) {
                    $term = get_term_by( 'id', $child, 'socialdb_category_type' );
                    if($term->parent!=$term_id){
                        continue;
                    }
                    $string .= '<li term="'.$term->term_id.'" class="taxonomy-list-create">';
                    $string .= "<span onclick='click_event_taxonomy_create_zone($(this).parent())' class='li-default taxonomy-list-name taxonomy-category-finished'>";
                    $string .= $term->name ."</span><input type='text' style='display: none;' class='input-taxonomy-create style-input'";
                    $string .= " onblur='blur_event_taxonomy_create_zone($(this).parent())'  onkeyup='keypress_event_taxonomy_create_zone($(this).parent(),event)' >";
                    $string .= $this->inserted_children($collection_id,$term->term_id);
                    $string .=  '</li>';
            }
            $string .= '</ul>';
        endif;
        return $string;
    }
    
}