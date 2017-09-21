<?php

/**
 * Author: Eduardo Humberto
 */
require_once(dirname(__FILE__) . '/rdf_collection_model.php');

class RDFPropertyModel extends RDFCollectionModel {
    var $category_root;
    var $category_root_url;
    
    public function setCategoryRoot() {
        $this->category_root = get_term_by('id',  $this->get_category_root_of($this->collection->ID), 'socialdb_category_type');
        $this->category_root_url = get_permalink($this->collection->ID).'?category='.$this->category_root->slug;
        
    }
################################# begin:property ###################################    
    /**
     * funcao que gera o rdf de um item simples
     * @return xml O rdf pronto a ser exibido
     */  
    public function export_property($slug_category) {
        $this->setCategoryRoot();
        $content = $this->get_rdf_property($slug_category);
        return $this->generate_complete_rdf_property($content);
    }
    
    
    /**
     * 
     * @param type $content
     * @return string
     */
    public function generate_complete_rdf_property($content){
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
     * gera o xml da propriedade
     * @param type $item
     * @param type $index
     */ 
    public function get_rdf_property($slug) {
        $xml = '';
        $property = get_term_by('slug', $slug,'socialdb_property_type');
        if(isset($this->collection->ID)):
            $url = get_permalink($this->collection->ID);
            $xml = '<owl:Ontology rdf:about="'.  get_permalink($this->collection->ID).'"  >';
             if(isset($this->collection->post_title)&&!empty($this->collection->post_title)):
                $xml .= "<rdfs:label>{$this->collection->post_title}</rdfs:label>";
            endif;
            if(isset($this->collection->post_content)&&!empty($this->collection->post_content)):
                $xml .= "<rdfs:comment>".utf8_decode (htmlspecialchars($this->collection->post_content))."</rdfs:comment>";
            endif;
            $xml .= '</owl:Ontology>';
        else:
            $url = site_url().'/';
        endif;
       
        $xml .= $this->generate_rdf_property_type($property->term_id);
        return $xml;
    }
    /**
     * 
     * @param type $id O id da propriedade
     * @return type
     */
     public function generate_rdf_property_type($id) {
        $xml = '';
        $type = $this->get_property_type($id); // pego o tipo da propriedade
        $all_data = $this->get_all_property($id,true); // pego todos os dados possiveis da propriedade
        if(!$all_data['slug']){
                     return '';
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
        return $xml;
     }
}
