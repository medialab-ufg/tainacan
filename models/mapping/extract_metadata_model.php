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
        $remove_port  = explode(':', $data['url'])[0];        
        $response_xml_data =  download_page('http://'.$data['url'].'/oai/request?verb=Identify'); // pego os 100 primeiros
        $xml = new SimpleXMLElement($response_xml_data);
        $property = 'oai-identifier';
        $identifier = explode('/', (string) $xml->Identify->description->$property->sampleIdentifier)[0];
        
        $response_xml_data =  download_page('http://'.$data['url'].
                '/oai/request?verb=GetRecord&metadataPrefix=oai_dc&identifier='
                .$identifier.'/'.$data['id']); // pego os 100 primeiros
        
         var_dump($response_xml_data);
        
       
    }

}
