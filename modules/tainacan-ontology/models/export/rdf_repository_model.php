<?php

/**
 * Author: Eduardo Humberto
 */
require_once(dirname(__FILE__) . '/rdf_collection_model.php');



class OntologyRDFRepositoryModel extends OntologyRDFCollectionModel {
    var $root;
    var $root_url;
    
    public function setRootData(){
        $this->root = get_term_by('id', $this->get_category_root_id(),'socialdb_category_type') ;
        $this->root_url = site_url().'/?category='.$this->root->slug;
    }
################################# begin:repository ###################################    
    /**
     * funcao que gera o rdf de um item simples
     * @return xml O rdf pronto a ser exibido
     */  
    public function export_simple_repository() {
        $this->setRootData();
        $content = $this->get_rdf_repository_simple();
        return $this->generate_complete_rdf_simple_repository($content);
    }
    
    
    /**
     * 
     * @param type $content
     * @return string
     */
    public function generate_complete_rdf_simple_repository($content){
        header("Content-Type: application/xml; charset=UTF-8");
        $header = '<rdf:RDF
        xmlns:skos="http://www.w3.org/2004/02/skos/core#"
        xmlns:dc="http://purl.org/dc/elements/1.1/"   
        xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
        xmlns="'.site_url().'"
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
    public function get_rdf_repository_simple() {
        
        //$user_owner = get_user_by('id', $item->post_author);
        $xml = '<owl:Ontology rdf:about="'.site_url().'"  >';
        if(!empty(get_option('blogname'))):
             $xml .= "<rdfs:label>".htmlspecialchars(get_option('blogname'))."</rdfs:label>";
        endif;
        if(!empty(get_option('blogdescription'))):
         $xml .= "<rdfs:comment>". htmlspecialchars(get_option('blogdescription'))."</rdfs:comment>";
        endif;
        $xml .= '</owl:Ontology>';
        //category_root
        $xml .= '<owl:Class rdf:about="'. $this->root_url.'"  >';
        $xml .= "<rdfs:label>{$this->category_root->name}</rdfs:label>";
        $xml .= '<rdfs:subClassOf rdf:resource="http://www.w3.org/2004/02/skos/core#Concept" />';
        $xml .= '</owl:Class>';
        //content-text
        $xml .= '<owl:DataTypeProperty rdf:about="'.site_url().'?property=content-text" >';
        $xml .= '<rdfs:range  rdf:resource="http://www.w3.org/2001/XMLSchema#string" />';
        $xml .= '<rdfs:domain  rdf:resource="'.$this->root_url.'" />';
        $xml .= '<rdfs:label>'.__('Content','tainacan').'</rdfs:label>';
       // $xml .= '<owl:maxCardinality rdf:datatype="xsd:nonNegativeInteger" >1</owl:maxCardinality>';
        $xml .= '</owl:DataTypeProperty>';
        //content
        $xml .= '<owl:ObjectProperty rdf:about="'.site_url().'?property=content" >';
        $xml .= '<rdfs:range  rdf:resource="'.site_url().'?category=media" />';
        $xml .= '<rdfs:domain  rdf:resource="'.$this->root_url.'" />';
        $xml .= '<rdfs:label>'.__('Content','tainacan').'</rdfs:label>';
       // $xml .= '<owl:maxCardinality rdf:datatype="xsd:nonNegativeInteger" >1</owl:maxCardinality>';
        $xml .= '</owl:ObjectProperty>';
        //attachment
        $xml .= '<owl:ObjectProperty rdf:about="'.site_url().'?property=attachment" >';
        $xml .= '<rdfs:range  rdf:resource="'.site_url().'?category=media" />';
        $xml .= '<rdfs:domain  rdf:resource="'.$this->root_url.'" />';
        $xml .= '<rdfs:label>'.__('Attachment','tainacan').'</rdfs:label>';
      //  $xml .= '<owl:maxCardinality rdf:datatype="xsd:nonNegativeInteger" >1</owl:maxCardinality>';
        $xml .= '</owl:ObjectProperty>';
        //ranking
        $xml .= '<owl:DataTypeProperty rdf:about="'.site_url().'?property=socialdb_property_ranking" >';
        $xml .= '<rdfs:range  rdf:resource="http://www.w3.org/2001/XMLSchema#string" />';
        $xml .= '<rdfs:domain  rdf:resource="'.$this->root_url.'" />';
        $xml .= '<rdfs:label>'.__('Ranking','tainacan').'</rdfs:label>';
        $xml .= '</owl:DataTypeProperty>';
        //ranking-binary
        $xml .= '<owl:DataTypeProperty rdf:about="'.site_url().'?property=socialdb_property_ranking_binary" >';
        $xml .= '<rdfs:subClassOf  rdf:resource="'.site_url().'?property=socialdb_property_ranking" />';
        $xml .= '<rdfs:label>'.__('Ranking Binary','tainacan').'</rdfs:label>';
        $xml .= '</owl:DataTypeProperty>';
        //ranking-stars
        $xml .= '<owl:DataTypeProperty rdf:about="'.site_url().'?property=socialdb_property_ranking_stars" >';
        $xml .= '<rdfs:subClassOf  rdf:resource="'.site_url().'?property=socialdb_property_ranking" />';
        $xml .= '<rdfs:label>'.__('Ranking Stars','tainacan').'</rdfs:label>';
        $xml .= '</owl:DataTypeProperty>';
        //ranking-like
        $xml .= '<owl:DataTypeProperty rdf:about="'.site_url().'?property=socialdb_property_ranking_like" >';
        $xml .= '<rdfs:subClassOf  rdf:resource="'.site_url().'?property=socialdb_property_ranking" />';
        $xml .= '<rdfs:label>'.__('Ranking Binary','tainacan').'</rdfs:label>';
        $xml .= '</owl:DataTypeProperty>';
        //tag
        $xml .= '<owl:Class rdf:about="'.site_url().'?category=tag"  >';
        $xml .= "<rdfs:label>".__('Tag','tainacan')."</rdfs:label>";
        $xml .= '<rdfs:subClassOf rdf:resource="http://www.w3.org/2004/02/skos/core#Concept" />';
        $xml .= '</owl:Class>';
        //propriedade de objeto da categoria com a tag
        $xml .= '<owl:ObjectProperty rdf:about="'.site_url().'?property=has-tag" >';
        $xml .= '<rdfs:range  rdf:resource="'.site_url().'?category=tag" />';
        $xml .= '<rdfs:domain  rdf:resource="'.$this->root_url.'" />';
        $xml .= '<rdfs:label>'.__('Classified as tag','tainacan').'</rdfs:label>';
        $xml .= '</owl:ObjectProperty>';
        //license
        $xml .= '<owl:Class rdf:about="'.site_url().'?category=license"  >';
        $xml .= "<rdfs:label>".__('License','tainacan')."</rdfs:label>";
        $xml .= '<rdfs:subClassOf rdf:resource="http://www.w3.org/2004/02/skos/core#Concept" />';
        $xml .= '</owl:Class>';
        //media
        $xml .= '<owl:Class rdf:about="'.site_url().'?category=media"  >';
        $xml .= "<rdfs:label>".__('Media','tainacan')."</rdfs:label>";
        $xml .= '<rdfs:subClassOf rdf:resource="http://www.w3.org/2004/02/skos/core#Concept" />';
        $xml .= '</owl:Class>';
        $xml .= $this->generate_rdf_properties_repository();
        return $xml;
    }
    /**
     * 
     * @param type $item_id
     * @return type
     */
     public function generate_rdf_properties_repository() {
         $xml = '';
         $properties = array_unique($this->get_parent_properties($this->root->term_id, [],$this->root->term_id));
         if(!empty($properties)){
             foreach ($properties as $id) {
                 $type = $this->get_property_type($id); // pego o tipo da propriedade
                 $all_data = $this->get_all_property($id,true); // pego todos os dados possiveis da propriedade
                 if(!$all_data['slug']){
                     continue;
                 }
                 if($type=='socialdb_property_data'){
                     $xml .= $this->add_lines_property_data_repository($all_data);
                 }elseif($type=='socialdb_property_object'){
                     $xml .= $this->add_lines_property_object_repository($all_data);
                 }elseif($type=='socialdb_property_term'){
                     $xml .= $this->add_lines_property_term_repository($all_data);
                 }elseif($type=='socialdb_property_ranking_stars'){
                     $xml .= $this->add_lines_property_ranking_repository($all_data,'socialdb_property_ranking_stars');
                 }elseif($type=='socialdb_property_ranking_like'){
                     $xml .= $this->add_lines_property_ranking_repository($all_data,'socialdb_property_ranking_like');
                 }elseif($type=='socialdb_property_ranking_binary'){
                     $xml .= $this->add_lines_property_ranking_repository($all_data,'socialdb_property_ranking_binary');
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
     public function add_lines_property_data_repository($data) {
         $xml = '';
         if(in_array($data['type'], ['numeric','number','auto-increment']))
                $resource = 'http://www.w3.org/2001/XMLSchema#integer';
         elseif(in_array($data['type'], ['date']))
                $resource = 'http://www.w3.org/2001/XMLSchema#date'; 
         elseif(in_array($data['type'], ['text','textarea']))
                $resource = 'http://www.w3.org/2001/XMLSchema#string'; 
         
         $cardinality = get_term_meta($data['id'], 'socialdb_property_data_cardinality', true);
         $required = get_term_meta($data['id'], 'socialdb_property_required', true);
         
         $xml .= '<owl:DataTypeProperty rdf:about="'.site_url().'?property='.$data['slug'].'" >';
         $xml .= '<rdfs:range  rdf:resource="'.$resource.'" />';
         $xml .= '<rdfs:domain  rdf:resource="'.$this->root_url.'" />';
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
     public function add_lines_property_object_repository($data) {
         $domain = get_term_by('id',$data['metas']["socialdb_property_created_category"],'socialdb_category_type') ;
         $range = get_term_by('id',$data['metas']["socialdb_property_object_category_id"],'socialdb_category_type') ;
         $xml = '';
         $xml .= '<owl:ObjectProperty rdf:about="'.site_url().'?property='.$data['slug'].'" >';
         $xml .= '<rdfs:range  rdf:resource="'.site_url().'?category='.$range->slug.'" />';
         $xml .= '<rdfs:domain  rdf:resource="'.site_url().'?category='.$domain->slug.'" />';
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
             $xml .= '<owl:inverseOf rdf:about="'.site_url().'?property='.$inverse->slug.'" />';
         }else if($domain->term_id==$range->term_id){
             $xml .= '<owl:SymmetricProperty rdf:about="'.site_url().'?property='.$data['slug'].'" />';
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
     public function add_lines_property_term_repository($data) {
         $domain = get_term_by('id',$data['metas']["socialdb_property_created_category"],'socialdb_category_type') ;
         $range = get_term_by('id',$data['metas']["socialdb_property_term_root"],'socialdb_category_type') ;
         $xml = '';
         $xml .= '<owl:ObjectProperty rdf:about="'.site_url().'?property='.$data['slug'].'" >';
         $xml .= '<rdfs:range  rdf:resource="'.site_url().'?category='.$range->slug.'" />';
         $xml .= '<rdfs:domain  rdf:resource="'.site_url().'?category='.$domain->slug.'" />';
         $xml .= '<rdfs:label>'.$data['name'].'</rdfs:label>';
         
         $cardinality = get_term_meta($data['id'], 'socialdb_property_object_cardinality', true);
         $required = get_term_meta($data['id'], 'socialdb_property_required', true);
         if($required=='true'){
             $xml .= '<rdf:type rdf:resource="http://www.w3.org/2002/07/owl#FunctionalProperty" />';
         }
         if(!$cardinality||$cardinality=='1'){
              $xml .= '<owl:maxCardinality rdf:datatype="xsd:nonNegativeInteger" >1</owl:maxCardinality>';
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
     public function add_lines_property_ranking_repository($data,$type) {
         $xml = '';
         $resource = 'http://www.w3.org/2001/XMLSchema#string';          
         $xml .= '<owl:DataTypeProperty rdf:about="'.site_url().'?property='.$data['slug'].'" >';
         $xml .= '<rdfs:range  rdf:resource="'.$resource.'" />';
         $xml .= '<rdfs:domain  rdf:resource="'.site_url().'?category='.$type.'" />';
         $xml .= '</owl:DataTypeProperty>';
         return $xml;
     }
################################# exportacao completa repositorio ##############     
    /**
     * funcao que gera o rdf de um repositorio completo
     * @return xml O rdf pronto a ser exibido
     */  
    public function export_complete_repository() {
        $this->setRootData();
        $content = $this->get_rdf_repository_simple();
        $collections = $this->get_collections_published();
        $content .= '<!-- COLLECTIONS -->';
        if(is_array($collections)){
            foreach ($collections as $collection){
                if($collection->ID!=get_option('collection_root_id')){
                    $content .= '<!-- COLLECTION: '.$collection->post_title.'  -->';
                    $this->setCollection($collection->ID);
                    $this->setCategoryRoot();
                    $content .= $this->get_rdf_collection_simple(false);
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
                }
            }           
        }
        return $this->generate_complete_rdf_simple_repository($content);
    }
     
    
}
