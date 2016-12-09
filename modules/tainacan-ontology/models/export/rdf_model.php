<?php

/**
 * Author: Eduardo Humberto
 */
include_once(dirname(__FILE__) . '/../../../../models/general/general_model.php');
require_once(dirname(__FILE__) . '/../../../../models/property/property_model.php');
require_once(dirname(__FILE__) . '/../../../../models/category/category_model.php');
require_once(dirname(__FILE__) . '/../../../../models/object/object_model.php');
require_once(dirname(__FILE__) . '/../../../../models/collection/collection_model.php');

class OntologyRDFModel extends Model {
    
    var $collection;
    var $item_name;
    var $namespace;
    
    public function __construct($collection_id = 0) {
        if($collection_id>0){
            $this->collection = get_post($collection_id);
        }else{
            $this->collection = get_post();
        }
    }
    
    public function setItemName($item_name) {
        $this->item_name = $item_name;
    }
    
    public function setCollection($collection_id) {
         $this->collection = get_post($collection_id);
    }
    
    public function setNamespace($namespace) {
         $this->namespace = $namespace;
    }
################################# begin:item ###################################    
    /**
     * funcao que gera o rdf de um item simples
     * @return xml O rdf pronto a ser exibido
     */  
    public function export_simple_item() {
        $this->setItemName(str_replace('.rdf', '', $_GET['item']));
        //$this->setNamespace('collection:');
        $this->setNamespace('');
        $args = array(
                    'name' => str_replace('.rdf', '', $_GET['item']),
                    'post_type' => 'socialdb_object',
                    'post_status' => 'publish',
                    'numberposts' => 1
                );
        $result = get_posts($args);
        if(!isset($result[0]))      
                      return false;
        
        $item = $result[0];
        $content = $this->get_rdf_item_simple($item);
        return $this->generate_complete_rdf_simple_item($content);
    }
    
    
    /**
     * 
     * @param type $content
     * @return string
     */
    public function generate_complete_rdf_simple_item($content){
        header("Content-Type: application/xml; charset=UTF-8");
        $header = '<rdf:RDF
        xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
        xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#">';
        $header .=  $content;
        $header .=  '</rdf:RDF>';
        return $header;
    }
    
     /**
     * gera o xml do item para o rdf simples
     * @param type $item
     * @param type $index
     */ 
    public function get_rdf_item_simple($item) {
        $collection_term_root = get_term_by('id', $this->get_category_root_of($this->collection->ID), 'socialdb_category_type');
        if(!$item->post_title):
            return '';
        endif;
        $xml = '<rdf:Description rdf:about="'.  get_permalink($this->collection->ID).'?item='.$this->item_name.'"  >';
        $xml .= "<rdfs:label>".strip_tags(htmlspecialchars($item->post_title))."</rdfs:label>";
        if(isset($item->post_content)&&!empty($item->post_content)):
            $xml .= "<rdfs:comment>".strip_tags(htmlspecialchars($item->post_content))."</rdfs:comment>";
        endif;
        
        $classifications = wp_get_object_terms($item->ID, 'socialdb_category_type');
        if(is_array($classifications)){
            foreach ($classifications as $classification) {
                if($classification->term_id==$collection_term_root->term_id){
                   continue; 
                }else{
                    $xml .= '<rdf:type rdf:resource="'.  get_permalink($this->collection->ID).'?category='.$classification->slug.'" />';
                    break;
                }
            }
        }else{
            $xml .= '<rdf:type rdf:resource="'.  get_permalink($this->collection->ID).'?category='.$collection_term_root->slug.'" />';
        }
        
       $xml .= $this->generate_rdf_properties_item($item->ID);
//        if(get_post_meta( $item->ID, 'socialdb_object_dc_type', TRUE)=='text'&&get_post_meta( $item->ID, 'socialdb_object_content', TRUE)!='')
//               $xml .= '<'.$this->namespace.'content rdf:datatype="http://www.w3.org/2001/XMLSchema#string" >'
//                        .strip_tags(htmlspecialchars(get_post_meta( $item->ID, 'socialdb_object_content', TRUE)))
//                        .'</'.$this->namespace.'content>'; 
//        else if(get_permalink(get_post_meta( $item->ID, 'socialdb_object_dc_type', TRUE))&&get_post_meta( $item->ID, 'socialdb_object_content', TRUE)!='')
//                $xml .= '<'.$this->namespace.'content rdf:datatype="'.get_permalink(get_post_meta( $item->ID, 'socialdb_object_dc_type', TRUE)).'" />';
         
       // $xml .= $this->get_tags_item($item->ID);
       // $xml .= $this->get_attachments($item->ID);
        $xml .= '</rdf:Description>';
        return $xml;
    }
    
    /**
     * @signature - get_categories_item_rdf($item_id)
     * @param int collection_id
     * @return 
     * @description - funcao que retorna todos os ids de um item
     * @author: Eduardo 
     */  
     public function get_categories_item($item_id) {
        $categories_id = [];
        $categories = wp_get_object_terms($item_id, 'socialdb_category_type');
        if (is_array($categories) && !empty($categories)) {
            foreach ($categories as $category) {
                $categories_id[] = $category->term_id;
            }
        }
        return $categories_id;
     }
    
    /**
      * 
      */
     public function generate_rdf_properties_item($item_id) {
         $xml = '';
         $categories = $this->get_categories_item($item_id);
         $properties = $this->get_properties_object($item_id);
         if(!empty($properties)){
             foreach ($properties as $id => $values) {
                 $type = $this->get_property_type($id); // pego o tipo da propriedade
                 $all_data = $this->get_all_property($id,true); // pego todos os dados possiveis da propriedade
                 if(!$all_data['slug']){
                     continue;
                 }
                 if($type=='socialdb_property_data'){
                    $xml .= $this->add_lines_property_data($all_data, $values);
                 }elseif($type=='socialdb_property_object'){
                     $xml .= $this->add_lines_property_object($all_data, $values);
                 }elseif($type=='socialdb_property_term'){
                    $xml .= $this->add_lines_property_term($all_data, $categories);
                 }elseif($type=='socialdb_property_ranking'){
                    $xml .= $this->add_lines_property_ranking($all_data, $values);
                 }
             }
         }
         return $xml;
     }
     /**
      * funcao que retorna as linhas para metadados de dados
      * @param type $data
      * @param type $values
      */
     public function add_lines_property_data($data,$values) {
         $xml = '';
         if(in_array($data['type'], ['numeric','number','auto-increment','int']))
                $datatype = 'http://www.w3.org/2001/XMLSchema#integer';
         elseif(in_array($data['type'], ['date']))
                $datatype = 'http://www.w3.org/2001/XMLSchema#date'; 
         elseif(in_array($data['type'], ['text','textarea']))
                $datatype = 'http://www.w3.org/2001/XMLSchema#string'; 
         else
            $datatype = 'http://www.w3.org/2001/XMLSchema#'.$data['type']; 
         if(is_array($values)){
             foreach ($values as $value) {
                 if($value&&trim($value)!==''){
                    $xml .= '<'.$this->namespace.''.$data['slug'].' rdf:datatype="'.$datatype.'">'
                            . $value
                            . '</'.$this->namespace.''.$data['slug'].'>';
                 }
             }
         }
         return $xml;
     }
     /**
      * funcao que retorna as linhas para metadados de objeto
      * @param type $data
      * @param type $values
      * @return string
      */
     public function add_lines_property_object($data,$values) {
         $xml = '';
         if(is_array($values)){
            foreach ($values as $value) {
                $value = absint($value);
                 if($value&&get_permalink($value)){
                     $link = get_the_permalink($value);
                     $xml .= '<'.$this->namespace.''.$data['slug'].' rdf:resource="'.htmlspecialchars($link).'" />';
                    // var_dump('<'.$this->namespace.''.$data['slug'].' rdf:about="'.get_permalink($value).'" />');
                }
            }
         }
         return $xml;
     }
     /**
      * funcao que retorna as linhas para os metadados de termo
      * @param type $data
      * @param type $categories
      * @return string
      */
     public function add_lines_property_term($data,$categories) {
         $xml = '';
         if(is_array($categories)){
             foreach ($categories as $category) {
                 $term = get_term_by('id', $category,'socialdb_category_type');
                  $ancestors = get_ancestors($term->term_id,'socialdb_category_type');
                  $collection = $this->get_collection_by_category_root($data["metas"]["socialdb_property_term_root"]);
                  if(isset($collection[0])){
                      $xml .= '<'.$this->namespace.''.$data['slug'].' rdf:about="'.get_permalink($collection[0]->ID).'?category='.$term->slug.'" />';
                  }
             }
         }
         return $xml;
     }
     /**
      * funcao que retorna as linhas para os rankings
      * @param type $data
      * @param type $values
      * @return string
      */
     public function add_lines_property_ranking($data,$values) {
         $xml = '';
         $datatype = 'http://www.w3.org/2001/XMLSchema#string'; 
         if(is_array($values)){
             foreach ($values as $value) {
                  $xml .= '<'.$this->namespace.''.$data['slug'].' rdf:datatype="'.$datatype.'">'
                          . floatval($value)
                          . '</'.$this->namespace.''.$data['slug'].'>';
             }
         }
         return $xml;
     }
     /**
     * @signature - generate_tags_item($item_id)
     * @param int collection_id
     * @return 
     * @description - 
     * @author: Eduardo 
     */  
     public function get_tags_item($item_id) {
        $xml = '';         
        $tags = wp_get_object_terms($item_id, 'socialdb_tag_type');
        if (is_array($tags) && !empty($tags)) {
            foreach ($tags as $tag) {
                     $xml .= '<'.$this->namespace.'has-tag rdf:about="'.get_permalink($this->collection->ID).'?tag='.$tag->slug.'" />';
            }
        }
        return $xml;
     }
     
     /**
     * @signature - get_attachments($data)
     * @param array $item_id Os dados vindos do formulario
     * @return json com os dados do resultado do evento criado
     * @description - Insere um objeto apenas com o titulo
     * @author: Eduardo 
     */
    public function get_attachments($item_id) {
        $real_attachments = [];
        $xml = '';        
        if ($item_id) {
            $post = get_post($item_id);
            $result = '';
            if (!is_object(get_post_thumbnail_id())) {
                $args = array(
                    'post_type' => 'attachment',
                    'numberposts' => -1,
                    'post_status' => null,
                    'post_parent' => $post->ID,
                    'exclude' => get_post_thumbnail_id()
                );
                //  var_dump($args);
                $attachments = get_posts($args);
                $arquivos = get_post_meta($post->ID, '_file_id');
                $object_content = get_post_meta($item_id,'socialdb_object_content',true);
                if ($attachments) {
                    foreach ($attachments as $attachment) {
                        if (in_array($attachment->ID, $arquivos)&&$object_content!=$attachment->ID) {
                            $metas = wp_get_attachment_metadata($attachment->ID);
                            $real_attachments['posts'][] = $attachment;
                            $extension = $attachment->guid;
                            $xml .= '<'.$this->namespace.'attachment rdf:about="'.$attachment->guid.'" />';
                        }
                    }
                } 
            }
        }
        if(!empty($xml)){
            return $xml;
        }else{
            return false;
        }
    }
     
################################## end: item ###################################    
     
}
