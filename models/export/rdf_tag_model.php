<?php

/**
 * Author: Eduardo Humberto
 */
require_once(dirname(__FILE__) . '/rdf_collection_model.php');

class RDFTagModel extends RDFCollectionModel {
################################# begin:tag ###################################    
    /**
     * funcao que gera o rdf de um item simples
     * @return xml O rdf pronto a ser exibido
     */  
    public function export_tag($slug) {
        $this->setCategoryRoot();
        $content = $this->get_rdf_tag($slug);
        return $this->generate_complete_tag_property($content);
    }
    
    
    /**
     * 
     * @param type $content
     * @return string
     */
    public function generate_complete_tag_property($content){
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
    public function get_rdf_tag($slug) {
        $xml = '';
        $tag = get_term_by('slug', $slug,'socialdb_tag_type');
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
       
        if($tag->name){
        $xml.= '<owl:Class rdf:about="'.$url.'?tag='.$tag->slug.'"  >';
                $xml .= "<rdfs:label>{$tag->name}</rdfs:label>";
                if($tag->slug!='socialdb_tag'){
                    $xml .= '<rdfs:subClassOf rdf:resource="'.$url.'?tag=socialdb_tag" />';
                }
                $xml .= '</owl:Class>';
        }
        return $xml;
    }
}
