<?php

/**
 * Author: Eduardo Humberto
 */
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../property/property_model.php');
require_once(dirname(__FILE__) . '../../category/category_model.php');
require_once(dirname(__FILE__) . '../../import/oaipmh_model.php');

class ExtractMetadataModel extends Model {
    
    /**
     * Metodo que extrai os valores de um record oai-pmh retornando os valores em 
     * um array associativo
     * 
     * @param string $url
     * @return array
     */
    public function get_record_oaipmh($url) {
        $response_xml_data = file_get_contents($url); // pego os 100 primeiros
        try{
             $xml = new SimpleXMLElement($response_xml_data);
        } catch (Exception $ex) {
             return false;
        }
       
        if(!isset($xml->GetRecord)){
            return false;
        }
        $record = $xml->GetRecord->record;
        $dc = $record->metadata->children("http://www.openarchives.org/OAI/2.0/oai_dc/");
        if ($record->metadata->Count() > 0 ) {
            if(!$record->header->setSpec && isset($data['sets']) && !empty($data['sets'])){
                 $json_response['token'] = '';
            }
            $metadata = $dc->children('http://purl.org/dc/elements/1.1/');
            $record_response['identifier'] = (string)$record->header->identifier;
            $record_response['datestamp'] = (string)$record->header->datestamp;
            $record_response['title'] = $metadata->title;
            $record_response['date'] = $record->header->datestamp;
            $tam_metadata = count($metadata);
            for ($i = 0; $i < $tam_metadata; $i++) {
                $value = (string) $metadata[$i];
                $identifier = $this->get_identifier($metadata[$i]);
                $record_response['metadata'][$identifier][] = $value;
            }
            $json_response['records'][] = $record_response;
        }
        $record_response['files'] = [];
        $record_response = [];
        return $json_response;
    }
    
    /**
     * @signature - get_link_data_nadle($data)
     * @param string $data O array
     * @param boolean $is_ojs Bollean para verificacao do tipo de extracao
     * @return string O link
     * @description - funcao que busca o link da oai-pmh
     * @author: Eduardo 
     */
    public function get_link_data_handle($data,$is_ojs = false) {
        if($is_ojs){
            $complement = 'oai';
            $response_xml_data = file_get_contents('http://' . $data['url'] . 'oai?verb=Identify'); // pego os 100 primeiros
        }else{
            $remove_port = explode(':', $data['url'])[0];
            $complement = '/oai/request';
            $response_xml_data = file_get_contents('http://' . $data['url'] . '/oai/request?verb=Identify'); // pego os 100 primeiros
        }
        
        if ($response_xml_data):
            $xml = new SimpleXMLElement($response_xml_data);
            $property = 'oai-identifier';
            if($xml->Identify->description->$property->sampleIdentifier):
                $identifier = explode('/', (string) $xml->Identify->description->$property->sampleIdentifier)[0];
                $identifier = str_replace('prefix', $data['tag'] , $identifier);
                return 'http://' . $data['url'] . $complement .
                    '?verb=GetRecord&metadataPrefix=oai_dc&identifier='
                    . $identifier . '/' . $data['id'];
            else:    
                $identifier = 'oai:'.$data['url'].':'.$data['tag'].'/'.$data['id'];
                return 'http://' . $data['url'] . $complement .
                    '?verb=GetRecord&metadataPrefix=oai_dc&identifier='
                    . $identifier ;
            endif;
        else:
            return '';
        endif;
    }
    /**
     * @signature - create_mapping($data)
     * @param string $name O nome do mapeamento
     * @param int $collection_id O id da colecao
     * @return int O id do mapeamento criado
     * @description - funcao que cria o mapeamnto e vincula com a colecao
     * @author: Eduardo 
     */
    public function get_metadata_handle($data,$is_ojs = false) {
        if($is_ojs){
            $complement = 'oai';
            $response_xml_data = download_page('http://' . $data['url'] . 'oai?verb=Identify'); // pego os 100 primeiros
        }else{
            $complement = '/oai/request';
            $remove_port = explode(':', $data['url'])[0];
            $response_xml_data = download_page('http://' . $data['url'] . '/oai/request?verb=Identify'); // pego os 100 primeiros
        }
        
        if ($response_xml_data):
            $xml = new SimpleXMLElement($response_xml_data);
            $property = 'oai-identifier';
            $identifier = explode('/', (string) $xml->Identify->description->$property->sampleIdentifier)[0];

            $response_xml_data = download_page('http://' . $data['url'] . $complement .
                    '?verb=GetRecord&metadataPrefix=oai_dc&identifier='
                    . $identifier . '/' . $data['id']); // pego os 100 primeiros
            $xml = new SimpleXMLElement($response_xml_data);
            $record = $xml->GetRecord->record;
            $whole_metadatas = [];
            //verifico se existe metadados para a extracao
            if ($record->metadata) {// mas o primeiro que tiver metadodos
                $tags_dc = array('title',
                                'language',
                                'source',
                                'keywords',
                                'subject',
                                'relation',
                                'type',
                                'date',
                                'description',
                                'contributors',
                                'publisher',
                                'creator',
                                'rights',
                                'identifier',
                                'format');
                foreach ($tags_dc as $metadata) {// percorro todos os dados
                    $whole_metadatas[] = $metadata;
                    $array['name_tag'] = "dc:" . $metadata;
                    $array['name_field'] = $metadata;
                    $array['name_on_select'] = $metadata;
                    $array['has_attribute'] = false;
                    $data['metadatas'][] = $array;
                }
            }
        endif;
        return (isset($data['metadatas'])) ? $data['metadatas'] : false; 
    }
    
    
    /**
     * @signature - get_identifyier($metadata)
     * @param array $metadata Os dados vindos do formulario
     * @return string O identifier do item a ser utilizado no form para o mapeiamento
     * @description - funcao que retorna todos os metadatas para realizar o mapeiamento das propriedades do repositorio escolhido
     * @author: Eduardo 
     */
    public function get_identifier($metadata) {
        $attributes = $metadata->attributes(); // atributos
        if ($attributes) {
            foreach ($attributes as $a => $b) {
                return $metadata->getName().'_'.(string) $b;
            }
        } else {
            return $metadata->getName();
        }
    }
    
    /**
     * 
     * @param type $param
     */
    public function get_tainacan_properties($data) {
        $data['metadatas'][] = array('name'=> __('Title','tainacan'),'value'=>'post_title');
        $data['metadatas'][] = array('name'=> __('Description','tainacan'),'value'=>'post_content');
        $data['metadatas'][] = array('name'=> __('Content','tainacan'),'value'=>'socialdb_object_content');
        $data['metadatas'][] = array('name'=> __('URL','tainacan'),'value'=>'post_permalink');
        $data['metadatas'][] = array('name'=> __('Type','tainacan'),'value'=>'socialdb_object_dc_type');
        $data['metadatas'][] = array('name'=> __('Source','tainacan'),'value'=>'socialdb_object_dc_source');
        $root_category = $this->get_category_root_of($data['collection_id']);
        $all_properties_id = $this->get_parent_properties($root_category, [],$root_category); 
        if ($all_properties_id) {
            foreach ($all_properties_id as $property_id) {
                $property = get_term_by("id", $property_id, "socialdb_property_type");
                if(in_array($property->slug, $this->fixed_slugs) ):
                    continue;
                endif;
                $type = $this->get_property_type_hierachy($property_id); // pego o tipo da propriedade
                if ($type == 'socialdb_property_object') {
                    $data['metadatas'][] = array('name'=> $property->name,'value'=>'objectproperty_' . $property_id);
                    //$html .= "<option value='objectproperty_" . $property_id . "'>" . $property->name . ' (' . __('Object Property','tainacan') . ')' . "</option>";
                } elseif ($type == 'socialdb_property_data') {
                    $data['metadatas'][] = array('name'=> $property->name,'value'=>'dataproperty_' . $property_id);
                    //$html .= "<option value='dataproperty_" . $property_id . "'>" . $property->name . ' (' . __('Data Property','tainacan') . ')' . "</option>";
                } elseif($type == 'socialdb_property_term'){
                    $data['metadatas'][] = array('name'=> $property->name,'value'=>'termproperty_' . $property_id);
                    //$html .= "<option value='termproperty_" . $property_id . "'>" . $property->name . ' (' . __('Term Property','tainacan') . ')' . "</option>";
                }
            }
        }
        $data['metadatas'][] = array('name'=> __('Source','tainacan'),'value'=>'socialdb_object_dc_source');
        return  $data['metadatas'];
    }
    
    /**
     * Metodo que extrai os valores de um record oai-pmh retornando os valores em 
     * um array associativo
     * 
     * @param string $url
     * @return array
     */
    public function get_record_metatags($url) {
        $response = $this->extract_metatags($url); // pego os 100 primeiros
        //busca as metatags
        if(!$response){
            return false;
        }
        $record_response['title'] = __('extracted item','tainacan');
        foreach ($response as $value) {
            if($value['name_field']=='title'){
               $record_response['title'] = trim($value['value']);
            }
            $record_response['metadata'][$value['name_field']][] = trim($value['value']);
        }
        $json_response['records'][] = $record_response;
        return $json_response;
    }
    
    /**
     * Metodo que insere o item atraves de seu mapeamento
     * 
     * 
     * @param type $form
     * @param type $collection_id
     * @param type $record
     */
    public function insert_item_handle($form,$collection_id,$record) {
        $oaipmh_class = new OAIPMHModel;
        $categories[] = $this->get_category_root_of($collection_id);
        $content = '';
        $object_id = socialdb_insert_object($record['title'], false);
        //mapping
        //add_post_meta($object_id, 'socialdb_channel_id',$mapping_id);
        if ($object_id != 0) {
            foreach ($record['metadata'] as $identifier => $metadata) {
                if ($form[$identifier] !== '') {
                    if ($form[$identifier] == 'post_title'):
                        $oaipmh_class->update_title($object_id, implode(',', $metadata));
                        $oaipmh_class->set_common_field_values($object_id, 'title', implode(',', $metadata));
                     elseif ($form[$identifier] == 'post_content'):
                        $content .=  implode(',', $metadata) . ",";
                    elseif ($form[$identifier] == 'post_permalink'):
                        update_post_meta($object_id, 'socialdb_object_dc_source', implode(',', $metadata));
                        $oaipmh_class->set_common_field_values($object_id, 'object_source', implode(',', $metadata));
                    elseif ($form[$identifier] == 'socialdb_object_content'):
                        update_post_meta($object_id, 'socialdb_object_content', implode(',', $metadata));
                        $oaipmh_class->set_common_field_values($object_id, 'object_content', implode(',', $metadata));
                   elseif ($form[$identifier] == 'socialdb_object_dc_type'):
                        update_post_meta($object_id, 'socialdb_object_dc_type', implode(',', $metadata));                       
                        $oaipmh_class->set_common_field_values($object_id, 'object_type', implode(',', $metadata));
                    elseif ($form[$identifier] == 'tag'):
                        foreach ($metadata as $meta) {
                           if(trim($meta)!==''){
                            $fields_value = explode('||', $meta);
                            foreach ($fields_value as $field_value):
                                $fields[] = explode('::', $field_value);
                            endforeach;
                            foreach ($fields as $fields_value):
                                foreach ($fields_value as $field_value):
                                    $this->insert_tag($field_value,$object_id,$collection_id);
                                endforeach;
                            endforeach;
                           }
                        }
                    elseif (strpos($form[$identifier], "termproperty_")!==false):
                        $trans = array("termproperty_" => "");
                        $property_id = strtr($form[$identifier], $trans);
                        $parent = get_term_meta($property_id, 'socialdb_property_term_root', true);
                        foreach ($metadata as $meta) {
                            if(trim($meta)!==''){
                                $oaipmh_class->insert_hierarchy($meta,$object_id,$collection_id,$parent,$property_id);
                            }
                        }
                    elseif (strpos($form[$identifier], "dataproperty_")!==false):
                        $trans = array("dataproperty_" => "");
                        $id = strtr($form[$identifier], $trans);
                        foreach ($metadata as $meta) {
                             add_post_meta($object_id, 'socialdb_property_'.$id.'',$meta);
                             $this->set_common_field_values($object_id, "socialdb_property_$id",$meta);
                        }
                    endif;
                }
            }
            $metadata = '';
            if($record['identifier']){
                 add_post_meta($object_id, 'socialdb_object_identifier',$record['identifier']);
            }
            if($record['datestamp']){
                 add_post_meta($object_id, 'socialdb_object_datestamp',$record['datestamp']);
            }
            update_post_meta($object_id, 'socialdb_object_from', 'external');
            $this->set_common_field_values($object_id, 'object_from', 'external');
            add_post_meta($object_id, 'socialdb_object_original_collection',$collection_id);
            update_post_content($object_id, $content);
            $this->set_common_field_values($object_id, 'description', $content);
            socialdb_add_tax_terms($object_id, $categories, 'socialdb_category_type');
            return $object_id;
        }
    }

}
