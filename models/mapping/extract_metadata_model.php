<?php

/**
 * Author: Eduardo Humberto
 */
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../property/property_model.php');
require_once(dirname(__FILE__) . '../../category/category_model.php');

class ExtractMetadataModel extends Model {

    /**
     * @signature - create_mapping($data)
     * @param string $name O nome do mapeamento
     * @param int $collection_id O id da colecao
     * @return int O id do mapeamento criado
     * @description - funcao que cria o mapeamnto e vincula com a colecao
     * @author: Eduardo 
     */
    public function get_metadata_handle($data) {
        $remove_port = explode(':', $data['url'])[0];
        $response_xml_data = download_page('http://' . $data['url'] . '/oai/request?verb=Identify'); // pego os 100 primeiros
        if ($response_xml_data):
            $xml = new SimpleXMLElement($response_xml_data);
            $property = 'oai-identifier';
            $identifier = explode('/', (string) $xml->Identify->description->$property->sampleIdentifier)[0];

            $response_xml_data = download_page('http://' . $data['url'] .
                    '/oai/request?verb=GetRecord&metadataPrefix=oai_dc&identifier='
                    . $identifier . '/' . $data['id']); // pego os 100 primeiros
             $xml = new SimpleXMLElement($response_xml_data);
            $record = $xml->GetRecord->record;
            $whole_metadatas = [];
                    
            if ($record->metadata) {// mas o primeiro que tiver metadodos
                $dc = $record->metadata->children("http://www.openarchives.org/OAI/2.0/oai_dc/"); //filhos da tag metadata
                $metadatas = $dc->children('http://purl.org/dc/elements/1.1/'); //filhos do oai_dc:dc//loop para correr todos os filhos
                foreach ($metadatas as $metadata) {// percorro todos os dados
                    if (!in_array($this->get_identifier($metadata), $whole_metadatas) && (string) $metadata) { // se ele ainda nao estiver no aray 
                        $whole_metadatas[] = $this->get_identifier($metadata);
                        $array['name_tag'] = "dc:" . $metadata->getName();
                        $array['name_field'] = $metadata;
                        $array['name_on_select'] = $this->get_identifier($metadata);
                        $array['name_inside_tag'] = $metadata->getName();
                        $attributes = $metadata->attributes(); // atributos
                        if ($attributes) {
                            $array['has_attribute'] = true;
                            foreach ($attributes as $a => $b) {
                                $array['attributes'] = array('index' => (string) $a, 'value' => (string) $b);
                            }
                        } else {
                            $array['has_attribute'] = false;
                        }
                        $data['metadatas'][] = $array;
                    }
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

}
