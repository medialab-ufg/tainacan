<?php

/**
 * Author: Eduardo Humberto Resende Guimaraes
 */
include_core_wp();
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../property/property_model.php');
require_once(dirname(__FILE__) . '../../category/category_model.php');
require_once(dirname(__FILE__) . '/oaipmh_model.php');
require_once(dirname(__FILE__) . '/xml_creater.php');

class OAIPMHListMetaDataFormatsModel extends OAIPMHModel {

   /** \var $working_node
     * O noh que esta sendo utilizado atualmente	
     */
    protected $working_node;
    public $errors;
    public $xml_creater;
    public $restoken = '-';
    public $expirationdatetime;
    public $num_rows;
    public $cursor;
    public $deliveredrecords;
    public $from;
    public $until;
    public $sets;
    public $metadataPrefix;

    /**
     * @signature - list_records
     * @param  array $param Os argumentos vindos da url (verb,until,from,set,metadataprefix,resumptioToken)
     * @return mostra o xml do list record desejado
     * @description - Metodo responsavel em mostrar o xml do list records, o metodo executado no controller
     * ele chama os demais metodos que fazem as verificacoes de erros
     * @author: Eduardo 
     */
    public function list_metadata_formats($data) {
        session_write_close();
        $formats = array();
        ini_set('max_execution_time', '0');
        $this->config();
        $this->xml_creater = new ANDS_Response_XML($data);
        if(isset($data['identifier'])){// se estiver olhando por metadados de um unico objeto
            $object_id = str_replace('oai:'.$this->repositoryIdentifier.':','', $data['identifier']);
            $object = get_post($object_id);
            if(isset($object->ID)&&get_post_status($object->ID )=='publish'){
                $formats = $this->get_metadata_formats($object->ID);
                if(empty($formats)){
                    $this->errors[] = $this->oai_error('noMetadataFormats');
                    $this->oai_exit($data,$this->errors);
                }
            }else{
                $this->errors[] = $this->oai_error('idDoesNotExist');
                $this->oai_exit($data,$this->errors);
            }
        }
        foreach ($this->METADATAFORMATS as $metadata_format) {
            if(!empty($formats)&&!in_array($metadata_format['metadataPrefix'], $formats)){
                continue;
            }
            $description_node = $this->xml_creater->add2_verbNode('metadataFormat');
            $this->xml_creater->addChild($description_node, 'metadataPrefix', $metadata_format['metadataPrefix']);
            $this->xml_creater->addChild($description_node, 'schema', $metadata_format['schema']);
            $this->xml_creater->addChild($description_node, 'metadataNamespace', $metadata_format['metadataNamespace']);
        }
        header($this->CONTENT_TYPE);
        if (isset($this->xml_creater)) {
            $this->xml_creater->display();
        } else {
            exit("There is a bug in codes");
        }
    }

    

}
