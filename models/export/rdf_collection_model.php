<?php

/**
 * Author: Eduardo Humberto
 */
require_once(dirname(__FILE__) . '/rdf_model.php');



class RDFCollectionModel extends RDFModel {
    var $category_root;
    var $category_root_url;
    
    public function setCategoryRoot() {
        $this->category_root = get_term_by('id',  $this->get_category_root_of($this->collection->ID), 'socialdb_category_type');
        $this->category_root_url = get_permalink($this->collection->ID).'?category='.$this->category_root->slug;
        
    }
################################# begin:collection ###################################    
    /**
     * funcao que gera o rdf de um item simples
     * @return xml O rdf pronto a ser exibido
     */  
    public function export_simple_collection() {
        $this->setCategoryRoot();
        $content = $this->get_rdf_collection_simple();
        return $this->generate_complete_rdf_simple_collection($content);
    }
    
    
    /**
     * 
     * @param type $content
     * @return string
     */
    public function generate_complete_rdf_simple_collection($content){
        header("Content-Type: application/xml; charset=UTF-8");
        $header = '<rdf:RDF
        xmlns:skos="http://www.w3.org/2004/02/skos/core#"
        xmlns:dc="http://purl.org/dc/elements/1.1/"   
        xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
        xmlns="'.get_the_permalink().'"
        xmlns:repository="'.site_url().'"
        xmlns:owl="http://www.w3.org/2002/07/owl#"    
        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
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
    public function get_rdf_collection_simple($show_ontology = true) {
        
        //$user_owner = get_user_by('id', $item->post_author);
        if($show_ontology):
            $xml = '<owl:Ontology rdf:about="'.  get_permalink($this->collection->ID).'"  >';
            if(isset($this->collection->post_title)&&!empty($this->collection->post_title)):
                 $xml .= "<rdfs:label>{$this->collection->post_title}</rdfs:label>";
            endif;
            if(isset($this->collection->post_content)&&!empty($this->collection->post_content)):
                 $xml .= "<rdfs:comment>".utf8_decode (htmlspecialchars($this->collection->post_content))."</rdfs:comment>";
            endif;
            $xml .= '</owl:Ontology>';
        endif;
        //category_root
        $parent = get_term_by('id', $this->category_root->parent, 'socialdb_category_type');
         $xml .= '<owl:Class rdf:about="'. $this->category_root_url.'"  >';
        if(isset($this->category_root->name)&&!empty($this->category_root->name)):
            $xml .= "<rdfs:label>{$this->category_root->name}</rdfs:label>";
        endif;
        if(isset($this->category_root->description)&&!empty($this->category_root->description)):
            $xml .= "<rdfs:comment>".utf8_decode (htmlspecialchars($this->category_root->description))."</rdfs:comment>";
        endif;
        $xml .= '<rdfs:subClassOf rdf:resource="'.get_permalink($this->collection->ID).'?category='.$parent->slug.'" />';
        $xml .= '</owl:Class>';
        $xml .= $this->generate_rdf_properties_collection();
        return $xml;
    }
    /**
     * 
     * @param type $item_id
     * @return type
     */
     public function generate_rdf_properties_collection() {
         $xml = '';
         $category_root_id = $this->get_category_root_of($this->collection->ID);
         $properties = $this->get_parent_properties($category_root_id, [], $category_root_id);
         if(!empty($properties)){
             foreach ($properties as $id) {
                 $type = $this->get_property_type($id); // pego o tipo da propriedade
                 $all_data = $this->get_all_property($id,true); // pego todos os dados possiveis da propriedade
                 if(!$all_data['slug']){
                     continue;
                 }
                 if($type=='socialdb_property_data'){
                     $xml .= $this->add_lines_property_data_collection($all_data);
                 }elseif($type=='socialdb_property_object'){
                     $xml .= $this->add_lines_property_object_collection($all_data);
                 }elseif($type=='socialdb_property_term'){
                     $xml .= $this->add_lines_property_term_collection($all_data);
                 }elseif($type=='socialdb_property_ranking_stars'){
                     $xml .= $this->add_lines_property_ranking_collection($all_data,'socialdb_property_ranking_stars');
                 }elseif($type=='socialdb_property_ranking_like'){
                     $xml .= $this->add_lines_property_ranking_collection($all_data,'socialdb_property_ranking_like');
                 }elseif($type=='socialdb_property_ranking_binary'){
                     $xml .= $this->add_lines_property_ranking_collection($all_data,'socialdb_property_ranking_binary');
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
     public function add_lines_property_data_collection($data) {
         $xml = '';
         if(in_array($data['type'], ['numeric','number','auto-increment']))
                $resource = 'http://www.w3.org/2001/XMLSchema#integer';
         elseif(in_array($data['type'], ['date']))
                $resource = 'http://www.w3.org/2001/XMLSchema#date'; 
         elseif(in_array($data['type'], ['text','textarea']))
                $resource = 'http://www.w3.org/2001/XMLSchema#string'; 
         
         $cardinality = get_term_meta($data['id'], 'socialdb_property_data_cardinality', true);
         $required = get_term_meta($data['id'], 'socialdb_property_required', true);
         $created_category = $data['metas']["socialdb_property_created_category"];
         $link = get_permalink($this->collection->ID).'?category='.get_term_by('id',$created_category,'socialdb_category_type')->slug;
         
         $xml .= '<owl:DataTypeProperty rdf:about="'.get_permalink($this->collection->ID).'?property='.$data['slug'].'" >';
         $xml .= '<rdfs:range  rdf:resource="'.$resource.'" />';
         $xml .= '<rdfs:domain  rdf:resource="'.$link.'" />';
         $xml .= '<rdfs:label>'.$data['name'].'</rdfs:label>';
         if($required=='true'){
             $xml .= '<rdf:type rdf:resource="http://www.w3.org/2002/07/owl#FunctionalProperty" />';
         }
         if(!$cardinality||$cardinality=='1'){
              $xml .= '<owl:maxCardinality rdf:datatype="xsd:nonNegativeInteger" >1</owl:maxCardinality>';
         }
         $xml .= '</owl:DataTypeProperty>';
         return $xml;
     }
     /**
      * funcao que retorna as linhas para metadados de objeto
      * @param type $data
      * @param type $values
      * @return string
      */
     public function add_lines_property_object_collection($data) {
         $domain = get_term_by('id',$data['metas']["socialdb_property_created_category"],'socialdb_category_type') ;
         $range = get_term_by('id',$data['metas']["socialdb_property_object_category_id"],'socialdb_category_type') ;
         $xml = '';
         $xml .= '<owl:ObjectProperty rdf:about="'.get_permalink($this->collection->ID).'?property='.$data['slug'].'" >';
         $xml .= '<rdfs:range  rdf:resource="'.get_permalink($this->collection->ID).'?category='.$range->slug.'" />';
         $xml .= '<rdfs:domain  rdf:resource="'.get_permalink($this->collection->ID).'?category='.$domain->slug.'" />';
         $xml .= '<rdfs:label>'.$data['name'].'</rdfs:label>';
         
         $cardinality = get_term_meta($data['id'], 'socialdb_property_object_cardinality', true);
         $required = get_term_meta($data['id'], 'socialdb_property_required', true);
         $reverse = get_term_meta($data['id'], 'socialdb_property_object_is_reverse', true);
         
         if($required=='true'){
             $xml .= '<rdf:type rdf:resource="http://www.w3.org/2002/07/owl#FunctionalProperty" />';
         }
         if(!$cardinality||$cardinality=='1'){
              $xml .= '<owl:maxCardinality rdf:datatype="xsd:nonNegativeInteger" >1</owl:maxCardinality>';
         }
         
         if($reverse=='true'){
             $inverse =  get_term_by('id',$data['metas']["socialdb_property_object_reverse"],'socialdb_property_type') ;
             $xml .= '<owl:inverseOf rdf:about="'.get_permalink($this->collection->ID).'?property='.$inverse->slug.'" />';
         }else if($domain->term_id==$range->term_id){
             $xml .= '<owl:SymmetricProperty rdf:about="'.get_permalink($this->collection->ID).'?property='.$data['slug'].'" />';
         }
          $xml .= '</owl:ObjectProperty>';
         return $xml;
     }
     /**
      * funcao que retorna as linhas para os metadados de termo
      * @param type $data
      * @param type $categories
      * @return string
      */
     public function add_lines_property_term_collection($data) {
         $domain = get_term_by('id',$data['metas']["socialdb_property_created_category"],'socialdb_category_type') ;
         $range = get_term_by('id',$data['metas']["socialdb_property_term_root"],'socialdb_category_type') ;
         $xml = '';
         $xml .= '<owl:ObjectProperty rdf:about="'.get_permalink($this->collection->ID).'?property='.$data['slug'].'" >';
         $xml .= '<rdfs:range  rdf:resource="'.get_permalink($this->collection->ID).'?category='.$range->slug.'" />';
         $xml .= '<rdfs:domain  rdf:resource="'.get_permalink($this->collection->ID).'?category='.$domain->slug.'" />';
         $xml .= '<rdfs:label>'.$data['name'].'</rdfs:label>';
         
         $cardinality = get_term_meta($data['id'], 'socialdb_property_object_cardinality', true);
         $required = get_term_meta($data['id'], 'socialdb_property_required', true);
         if($required=='true'){
             $xml .= '<rdf:type rdf:resource="http://www.w3.org/2002/07/owl#FunctionalProperty" />';
         }
         if(!$cardinality||$cardinality=='1'){
              //$xml .= '<owl:maxCardinality rdf:datatype="xsd:nonNegativeInteger" >1</owl:maxCardinality>';
         }
         $xml .= '</owl:ObjectProperty>';
         return $xml;
     }
     /**
      * funcao que retorna as linhas para os rankings
      * @param type $data
      * @param type $type
      * @return string
      */
     public function add_lines_property_ranking_collection($data,$type) {
         $xml = '';
         $resource = 'http://www.w3.org/2001/XMLSchema#string';          
         $xml .= '<owl:DataTypeProperty rdf:about="'.get_permalink($this->collection->ID).'?property='.$data['slug'].'" >';
         $xml .= '<rdfs:range  rdf:resource="'.$resource.'" />';
         $xml .= '<rdfs:domain  rdf:resource="'.get_permalink($this->collection->ID).'?category='.$type.'" />';
         $xml .= '</owl:DataTypeProperty>';
         return $xml;
     }
     
################################## Colecao completa ############################
     /**
     * funcao que gera o rdf de uma colecao completa com todos os seus itens
     * @return xml O rdf pronto a ser exibido
     */  
    public function export_all_collection() {
        $this->setCategoryRoot();
        $content = $this->get_rdf_collection_simple();
        $content .= $this->generate_rdf_taxonomies();
        $content .= $this->generate_rdf_tags();
        $items = $this->get_collection_posts($this->collection->ID);
        if($items&&!empty($items)){
            $content .= '<!-- INDIVIDUOS -->';
            $this->setNamespace('');
            foreach ($items as $item) {
                $this->setItemName($item->post_name);
                $content .= $this->get_rdf_item_simple($item);
            }
        }
        return $this->generate_complete_rdf_simple_collection($content);
    }
     /**
      * 
      * @return xml Com todas as taxonomias participantes desta colecao 
      */
     public function generate_rdf_taxonomies(){
        $xml = '<!-- TAXONOMIAS -->'; 
        $terms_id = [];
        $root_category = $this->get_category_root_of($this->collection->ID);
        //$all_properties_id = array_unique($this->get_parent_properties($root_category, [],$root_category));
        $all_facets = CollectionModel::get_facets($this->collection->ID);
        if ($all_facets) {
            foreach ($all_facets as $term_id) {
                $term = get_term_by('id', $term_id,'socialdb_category_type'); // pega categoria
                if ($term) {
                   $terms_id[] = $term->term_id;
                }
            }
            $terms_id = array_unique($terms_id);
        }
        if(!in_array($root_category,$terms_id)){
            $terms_id[] = $root_category;
        }
       // var_dump($terms_id);exit();
        if (!empty($terms_id)) {
            $terms_id = array_unique($terms_id);
            foreach ($terms_id as $term_id) {
                $this->get_taxonomies_rdf($term_id, $xml);
            }
        }
        return $xml;
     }
     
     /**
     * get_taxonomies_rdf($parent_id,&$xml)
     * @param  int $parent_id O id do termo
     * @param string $xml O xml passado como referencia para criacao do arquivo
     * @author Eduardo Humberto 
     */
    public function get_taxonomies_rdf($parent_id, &$xml) {
        $term = get_term_by('id', $parent_id, 'socialdb_category_type');
        $parent = get_term_by('id', $term->parent, 'socialdb_category_type');
        if($this->get_category_root_of($this->collection->ID)!=$parent_id){
            $xml.= '<owl:Class rdf:about="'.get_permalink($this->collection->ID).'?category='.$term->slug.'"  >';
            $xml .= "<rdfs:label>{$term->name}</rdfs:label>";
            $xml .= '<rdfs:subClassOf rdf:resource="'.get_permalink($this->collection->ID).'?category='.$parent->slug.'" />';
            $xml .= '</owl:Class>';
        }
        $children = $this->get_category_children($parent_id);
        if (!empty($children) && is_array($children)) {
            foreach ($children as $child) {
                $this->get_taxonomies_rdf($child, $xml);
            }
        }
    }
    /**
     * metodo que gera as tags da colecao
     * @return string
     */
    public function generate_rdf_tags() {
        $xml = '<!-- TAGS -->'; 
        $get_tags = wp_get_object_terms($this->collection->ID, 'socialdb_tag_type');
        if ($get_tags) {
            foreach ($get_tags as $tag) {
                $xml.= '<owl:Class rdf:about="'.get_permalink($this->collection->ID).'?tag='.$tag->slug.'"  >';
                $xml .= "<rdfs:label>{$tag->name}</rdfs:label>";
                $xml .= '<rdfs:subClassOf rdf:resource="'.get_permalink($this->collection->ID).'?tag=socialdb_tag" />';
                $xml .= '</owl:Class>';
            }
        }
        return $xml;
    }
    
}
