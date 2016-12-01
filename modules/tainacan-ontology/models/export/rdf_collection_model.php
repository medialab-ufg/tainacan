<?php

/**
 * Author: Eduardo Humberto
 */
require_once(dirname(__FILE__) . '/rdf_model.php');
require_once(dirname(__FILE__) . '/../filters/filters_model.php');



class OntologyRDFCollectionModel extends OntologyRDFModel {
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
    public function get_rdf_collection_simple($show_ontology = true) {
        $content = '';
        $items = $this->get_collection_posts($this->collection->ID);
        if($items&&!empty($items)){
            $content .= '<!-- Indivíduos da ontologia - -->';
            $this->setNamespace('');
            foreach ($items as $item) {
                $this->setItemName($item->post_name);
                $content .= $this->get_rdf_item_simple($item);
            }
        }
        return $content;
    }
    /**
     * 
     * @param type $item_id
     * @return type
     */
     public function generate_rdf_properties_collection() {
         //$xml = '';
         $data_xml = '<!-- Ontology data properties -->';
         $objeto_xml = '<!-- Ontology object properties -->';
        $filters = new FiltersModel;
        $facets_id = array_filter(array_unique((get_post_meta($this->collection->ID, 'socialdb_collection_facets'))?get_post_meta($this->collection->ID, 'socialdb_collection_facets'):[]));
        $properties = [];
        $filters->get_collection_properties($properties,0,$facets_id);
        //$category_root_id = $this->get_category_root_of($this->collection->ID);
         $properties = array_unique($properties);
         if(!empty($properties)){
             foreach ($properties as $id) {
                 $type = $this->get_property_type_hierachy($id); // pego o tipo da propriedade
                 $all_data = $this->get_all_property($id,true); // pego todos os dados possiveis da propriedade
                 if(!$all_data['slug']){
                     continue;
                 }
                 if($type==='socialdb_property_data'){
                     $data_xml .= $this->add_lines_property_data_collection($all_data);
                 }elseif($type==='socialdb_property_object'){
                     $objeto_xml .= $this->add_lines_property_object_collection($all_data);
                 }elseif($type==='socialdb_property_term'){
                     $objeto_xml .= $this->add_lines_property_term_collection($all_data);
                 }elseif($type==='socialdb_property_ranking_stars'){
                     $data_xml .= $this->add_lines_property_ranking_collection($all_data,'socialdb_property_ranking_stars');
                 }elseif($type==='socialdb_property_ranking_like'){
                     $data_xml .= $this->add_lines_property_ranking_collection($all_data,'socialdb_property_ranking_like');
                 }elseif($type==='socialdb_property_ranking_binary'){
                     $data_xml .= $this->add_lines_property_ranking_collection($all_data,'socialdb_property_ranking_binary');
                 }
             }
         }
         return $data_xml.$objeto_xml;
     }
     /**
      * funcao que retorna as linhas para metadados de dados
      * @param type $data
      * @param type $values
      */
     public function add_lines_property_data_collection($data) {
         $xml = '';
         $filters_model = new FiltersModel;
         $domains = $filters_model->used_by_classes($data['id']);
         
         if(in_array($data['type'], ['numeric','number','auto-increment','int','integer']))
                $resource = 'http://www.w3.org/2001/XMLSchema#integer';
         elseif(in_array($data['type'], ['date']))
                $resource = 'http://www.w3.org/2001/XMLSchema#date'; 
         elseif(in_array($data['type'], ['text','textarea','string']))
                $resource = 'http://www.w3.org/2001/XMLSchema#string'; 
         elseif(in_array($data['type'], ['boolean']))
                $resource = 'http://www.w3.org/2001/XMLSchema#boolean'; 
         elseif(in_array($data['type'], ['datetime']))
                $resource = 'http://www.w3.org/2001/XMLSchema#datetime'; 
         elseif(in_array($data['type'], ['time']))
                $resource = 'http://www.w3.org/2001/XMLSchema#time'; 
         elseif(in_array($data['type'], ['float']))
                $resource = 'http://www.w3.org/2001/XMLSchema#float'; 
         
         $xml .= '<owl:DatatypeProperty rdf:about="'.get_permalink($this->collection->ID).'?property='.$data['slug'].'" >';
         $xml .= '<rdfs:range  rdf:resource="'.$resource.'" />';
         if($domains){
             foreach ($domains as $domain){
                 $xml .= '<rdfs:domain  rdf:resource="'.get_permalink($this->collection->ID).'?category='. get_term_by('id', $domain->term_id, 'socialdb_category_type')->slug.'" />';
             }
         }
         $xml .= '<rdfs:label>'.$data['name'].'</rdfs:label>';
         //description 
         if(isset($data['description'])&&!empty($data['description'])){
              $xml .= '<rdfs:comment>'.$data['description'].'</rdfs:comment>';
         }
         //parent 
         $parent = get_term_by('id', $data['parent'],'socialdb_property_type');
         if(isset($parent->term_id)&&$parent->name!='socialdb_property_data'){
              $xml .= '<rdfs:subPropertyOf rdf:resource="'.get_permalink($this->collection->ID).'?property='. get_term_by('id', $parent->term_id, 'socialdb_property_type')->slug.'"/>
';
         }
         //functional
         $required = get_term_meta($data['id'], 'socialdb_property_functional', true);
         if($required=='true'){
             $xml .= '<rdf:type rdf:resource="http://www.w3.org/2002/07/owl#FunctionalProperty" />';
         }
         //equivalent properties
         $equivalent = get_term_meta($data['id'], 'socialdb_property_equivalent');
         if($equivalent  &&  is_array($equivalent)){
             $equivalent = array_filter($equivalent);
             foreach ($equivalent as $equivalent_single) {
                 $xml .= '<owl:equivalentProperty rdf:resource="'.get_permalink($this->collection->ID).'?property='.get_term_by('id', $equivalent_single, 'socialdb_property_type')->slug.'" />';
             }
         }
         $xml .= '</owl:DatatypeProperty>';
         return $xml;
     }
     /**
      * funcao que retorna as linhas para metadados de objeto
      * @param type $data
      * @param type $values
      * @return string
      */
     public function add_lines_property_object_collection($data) {
         $xml_ranges = '';
         $filters_model = new FiltersModel;
         //domains
         $domains = $filters_model->used_by_classes($data['id']);
         //ranges
         if(is_array($data['metas']["socialdb_property_object_category_id"])){
             $ranges = array_filter(array_unique($data['metas']["socialdb_property_object_category_id"]));
             foreach ($ranges as $range) {
                 $xml_ranges .= '<rdfs:range  rdf:resource="'.get_permalink($this->collection->ID).'?category='.get_term_by('id',$range, 'socialdb_category_type')->slug.'" />';
             }
         }else{
             $range = get_term_by('id',$data['metas']["socialdb_property_object_category_id"],'socialdb_category_type') ;
              $xml_ranges .= '<rdfs:range  rdf:resource="'.get_permalink($this->collection->ID).'?category='.$range->slug.'" />';
         }
         //montando a proprieade
         $xml = '';
         $xml .= '<owl:ObjectProperty rdf:about="'.get_permalink($this->collection->ID).'?property='.$data['slug'].'" >';
         //montando os ranges
         $xml .= $xml_ranges;
         //montando os dominios
          if($domains){
             foreach ($domains as $domain){
                 $xml .= '<rdfs:domain  rdf:resource="'.get_permalink($this->collection->ID).'?category='. get_term_by('id', $domain->term_id, 'socialdb_category_type')->slug.'" />';
             }
         }
        // $xml .= '<rdfs:domain  rdf:resource="'.get_permalink($this->collection->ID).'?category='.$domain->slug.'" />';
         $xml .= '<rdfs:label>'.$data['name'].'</rdfs:label>';
         //parent 
         $parent = get_term_by('id', $data['parent'],'socialdb_property_type');
         if(isset($parent->term_id)&&$parent->name!='socialdb_property_object'){
              $xml .= '<rdfs:subPropertyOf rdf:resource="'.get_permalink($this->collection->ID).'?property='. get_term_by('id', $parent->term_id, 'socialdb_property_type')->slug.'"/>
';
         }
         
         //functional
          $required = get_term_meta($data['id'], 'socialdb_property_functional', true);
         if($required=='true'){
             $xml .= '<rdf:type rdf:resource="http://www.w3.org/2002/07/owl#FunctionalProperty" />';
         }
         //transitive
         $transitive = get_term_meta($data['id'], 'socialdb_property_transitive', true);
         if(($transitive && $transitive=='true') ){
                $xml .= '<rdf:type rdf:resource="http://www.w3.org/2002/07/owl#TransitiveProperty" />';
         }
         //inverseof
         $reverse = get_term_meta($data['id'], 'socialdb_property_object_is_reverse', true);
         if($reverse=='true'){
             $inverse =  get_term_by('id',$data['metas']["socialdb_property_object_reverse"],'socialdb_property_type') ;
             $xml .= '<owl:inverseOf rdf:resource="'.get_permalink($this->collection->ID).'?property='.$inverse->slug.'" />';
         }
         //simetric
         $simetric = get_term_meta($data['id'], 'socialdb_property_simetric', true);
         if(($simetric && $simetric=='true') ){
             $xml .= '<owl:SymmetricProperty rdf:resource="'.get_permalink($this->collection->ID).'?property='.$data['slug'].'" />';
         }
         //equivalent properties
         $equivalent = get_term_meta($data['id'], 'socialdb_property_equivalent');
         if($equivalent  &&  is_array($equivalent)){
             $equivalent = array_filter($equivalent);
             foreach ($equivalent as $equivalent_single) {
                 $xml .= '<owl:equivalentProperty rdf:resource="'.get_permalink($this->collection->ID).'?property='.get_term_by('id', $equivalent_single, 'socialdb_property_type')->slug.'" />';
             }
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
     * 
     * @param type $content
     * @return string
     */
    public function generate_rdf_complete_collection($content){
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
     
    public function init_export_complete_collection($show_ontology = true) {
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
        
         $xml .= '<!-- Ontology Classes -->';
        //category_root
//        $parent = get_term_by('id', $this->category_root->parent, 'socialdb_category_type');
//         $xml .= '<owl:Class rdf:about="'. $this->category_root_url.'"  >';
//        if(isset($this->category_root->name)&&!empty($this->category_root->name)):
//            $xml .= "<rdfs:label>{$this->category_root->name}</rdfs:label>";
//        endif;
//        if(isset($this->category_root->description)&&!empty($this->category_root->description)):
//            $xml .= "<rdfs:comment>".utf8_decode (htmlspecialchars($this->category_root->description))."</rdfs:comment>";
//        endif;
//         //$xml .= ''
//        $xml .= $this->get_restrictions_rdf($this->category_root->term_id);
//        $xml .= '<rdfs:subClassOf rdf:resource="'.get_permalink($this->collection->ID).'?category='.$parent->slug.'" />';
//        $xml .= '</owl:Class>';
        return $xml;
    }
     /**
     * funcao que gera o rdf de uma colecao completa com todos os seus itens
     * @return xml O rdf pronto a ser exibido
     */  
    public function export_all_collection() {
        $this->setCategoryRoot();
        $content = $this->init_export_complete_collection();
        $content .= $this->generate_rdf_taxonomies();     
        $content .= $this->generate_rdf_tags();
        $content .= $this->generate_rdf_properties_collection();
        $items = $this->get_collection_posts($this->collection->ID);
        if($items&&!empty($items)){
            $content .= '<!-- Individuals  -->';
            $this->setNamespace('');
            foreach ($items as $item) {
                $this->setItemName($item->post_name);
                $content .= $this->get_rdf_item_simple($item);
            }
        }
        return $this->generate_rdf_complete_collection($content);
    }
     /**
      * 
      * @return xml Com todas as taxonomias participantes desta colecao 
      */
     public function generate_rdf_taxonomies(){
        //$xml = '<!-- TAXONOMIES -->'; 
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
            $xml .= $this->get_restrictions_rdf($term->term_id);
            $xml .= $this->others_restrictions_classes($term->term_id);
            if($parent->slug!=='socialdb_category' && $parent->slug!=='socialdb_taxonomy')
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
     * Disjoint
     * Equivalent Classes
     * union Of
     * intersection Of
     * complement Of
     * 
     * Retorna todas as classes disjuntas e classes equivalentes
     * @param type $category_id
     */
    public function others_restrictions_classes($category_id) {
        $xml = '';
        $disjoint_classes = get_term_meta($category_id,'socialdb_category_disjointwith');
        if($disjoint_classes  &&  is_array($disjoint_classes)){
            foreach ($disjoint_classes as $disjoint_classe) {
                if($disjoint_classe!=''){
                   $xml .= '<owl:disjointWith rdf:resource="'.get_permalink($this->collection->ID).'?category='.  get_term_by('id', $disjoint_classe,'socialdb_category_type')->slug.'"/>';
                }
            }
        }
        $equivalent_classes = get_term_meta($category_id,'socialdb_category_equivalentclass');
        if($equivalent_classes  &&  is_array($equivalent_classes)){
            foreach ($equivalent_classes as $equivalent_classe) {
                if($equivalent_classe!=''){
                   $xml .= '<owl:equivalentClass  rdf:resource="'.get_permalink($this->collection->ID).'?category='.  get_term_by('id', $equivalent_classe,'socialdb_category_type')->slug.'"/>';
                }
            }
        }
        $unionof = get_term_meta($category_id,'socialdb_category_unionof');
        if($unionof  &&  is_array($unionof)&&!empty(array_filter($unionof))){
             $xml .='<owl:unionOf rdf:parseType="Collection">';
            foreach ($unionof as $union) {
                if($union!=''){
                   $xml .= '<owl:Class rdf:about="'.get_permalink($this->collection->ID).'?category='.  get_term_by('id', $union,'socialdb_category_type')->slug.'"/>';
                }
            }
            $xml .= '</owl:unionOf>';
        }
        $intersectionof = get_term_meta($category_id,'socialdb_category_intersectionof');
        if($intersectionof  &&  is_array($intersectionof)&&!empty(array_filter($intersectionof))){
             $xml .='<owl:intersectionOf rdf:parseType="Collection">';
            foreach ($intersectionof as $intersection) {
                if($intersection!=''){
                   $xml .= '<owl:Class rdf:about="'.get_permalink($this->collection->ID).'?category='.  get_term_by('id', $intersection,'socialdb_category_type')->slug.'"/>';
                }
            }
            $xml .= '</owl:intersectionOf>';
        }
        //complement
        $complementof = get_term_meta($category_id,'socialdb_category_complementof');
        if($complementof  &&  is_array($complementof)){
            foreach ($complementof as $complement) {
                if($complement!=''){
                   $xml .= '<owl:complementOf  rdf:resource="'.get_permalink($this->collection->ID).'?category='.  get_term_by('id', $complement,'socialdb_category_type')->slug.'"/>';
                }
            }
        }
        return $xml;
    }
    /**
     * retorna todas as restricoes de uma classe
     * @param int $category_id O id da categoria
     */
    public function get_restrictions_rdf($category_id) {
        $restriction = '';
        $collection_link = get_permalink($this->collection->ID);
        $category_root_id = $this->get_category_root_of($this->collection->ID);
        $properties = $this->get_parent_properties($category_id, [], $category_root_id);
        if(!empty($properties)){
            foreach ($properties as $id) {
                $all_data = $this->get_all_property($id,true); // pego todos os dados possiveis da propriedade
                if(!$all_data['slug']){
                    continue;
                }
                //restricoes
                if($all_data['metas']["socialdb_property_cardinalidality"]&&!empty($all_data['metas']["socialdb_property_cardinalidality"])){
                    $restriction .= $this->generate_restriction_tag($collection_link.'?property='.$all_data['slug'],$all_data['metas']["socialdb_property_cardinalidality"],'cardinality');
                }else{ 
                    if($all_data['metas']["socialdb_property_mincardinalidality"]&&!empty($all_data['metas']["socialdb_property_mincardinalidality"])){
                        if($all_data['metas']["socialdb_property_maxcardinalidality"]&&!empty($all_data['metas']["socialdb_property_maxcardinalidality"])){
                               $restriction .= $this->generate_restriction_tag_min_max(
                                $collection_link.'?property='.$all_data['slug'],
                                 $all_data['metas']["socialdb_property_mincardinalidality"],
                                $all_data['metas']["socialdb_property_maxcardinalidality"]);
                        }else{
                          $restriction .= $this->generate_restriction_tag($collection_link.'?property='.$all_data['slug'],$all_data['metas']["socialdb_property_mincardinalidality"],'minCardinality');
                        }
                    }
                    else if($all_data['metas']["socialdb_property_maxcardinalidality"]&&!empty($all_data['metas']["socialdb_property_maxcardinalidality"])){
                        $restriction .= $this->generate_restriction_tag(
                                $collection_link.'?property='.$all_data['slug'],
                                $all_data['metas']["socialdb_property_maxcardinalidality"],
                                'maxCardinality');
                    }
                }
                //allvaluesfrom
                if($all_data['metas']["socialdb_property_allvaluesfrom"]&&!empty($all_data['metas']["socialdb_property_allvaluesfrom"])){
                    $restriction .= $this->generate_restriction_tag($collection_link.'?property='.$all_data['slug'],$all_data['metas']["socialdb_property_allvaluesfrom"],'allValuesFrom',$collection_link.'?category=');
                }
                //somevaluesfrom
                if($all_data['metas']["socialdb_property_somevaluesfrom"]&&!empty($all_data['metas']["socialdb_property_somevaluesfrom"])){
                    $restriction .= $this->generate_restriction_tag($collection_link.'?property='.$all_data['slug'],$all_data['metas']["socialdb_property_somevaluesfrom"],'someValuesFrom',$collection_link.'?category=');
                }
                //somevaluesfrom
                if($all_data['metas']["socialdb_property_hasvalue"]&&!empty($all_data['metas']["socialdb_property_hasvalue"])){
                    $restriction .= $this->generate_restriction_tag($collection_link.'?property='.$all_data['slug'],$all_data['metas']["socialdb_property_hasvalue"],'hasValue',$collection_link.'?category=');
                }
                
            }
        }
        return $restriction;
    }
    
    
    /**
     * metodo que gera as tags da colecao
     * @return string
     */
    public function generate_rdf_tags() {
       // $xml = '<!-- TAGS -->'; 
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
    
    /**
     * 
     * @param type $resource 
     * @param type $value O valor da restricao
     * @param string $label O nome da tag a ser utlizado como restrição
     * @param string $datatype O tipo 
     * @return string
     */
    public function generate_restriction_tag($resource,$value,$label,$datatype='http://www.w3.org/2001/XMLSchema#nonNegativeInteger'){
        $restriction = '';
        $restriction .= '<rdfs:subClassOf>';
        $restriction .= '<owl:Restriction>';
        $restriction .= '<owl:onProperty rdf:resource="'.$resource.'"/>';
        if(!is_array($value)){
            $restriction .= '<owl:'.$label.' rdf:datatype="'.$datatype.'">';
            $restriction .= $value;
            $restriction .= '</owl:'.$label.'>';
        }else{
            $value = array_filter($value);
            if(empty($value)){
                return '';
            }
            foreach ($value as $val) {
                $restriction .= '<owl:'.$label.' rdf:resource="'.$datatype.''.  get_term_by('id', $val,'socialdb_category_type')->slug.'" />';
               // $restriction .= $val;
                //$restriction .= '</owl:'.$label.'>';
            }
        }
        $restriction .= '</owl:Restriction>';
        $restriction .= '</rdfs:subClassOf>';
        return $restriction;
     }
    /**
     * 
     * 
     * @param type $resource
     * @param type $value1
     * @param type $value2
     * @param type $datatype
     * @return string
     */
    public function generate_restriction_tag_min_max($resource,$value1,$value2,$datatype='http://www.w3.org/2001/XMLSchema#nonNegativeInteger'){
        $restriction = '';
        $restriction .= '<rdfs:subClassOf>';
        $restriction .= '<owl:Restriction>';
        $restriction .= '<owl:onProperty rdf:resource="'.$resource.'"/>';
        $restriction .= '<owl:minCardinality rdf:datatype="'.$datatype.'">';
        $restriction .= $value1;
        $restriction .= '</owl:minCardinality>';
        $restriction .= '<owl:maxCardinality rdf:datatype="'.$datatype.'">';
        $restriction .= $value2;
        $restriction .= '</owl:maxCardinality>';
        $restriction .= '</owl:Restriction>';
        $restriction .= '</rdfs:subClassOf>';
        return $restriction;
     }
    
}
