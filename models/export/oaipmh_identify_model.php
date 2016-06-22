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

class OAIPMHIdentifyModel extends OAIPMHModel {

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
     * @signature CONSTRUTOR
     * @return seta a variavel com xml_creater com a classe xml responsavel em gerar oarquivo
     * @author: Eduardo 
     */
    function __construct() {
        //$this->xml_creater = new ANDS_Response_XML(array('verb' => 'ListRecords'));
    }
    

    /**
     * @signature - list_records
     * @param  array $param Os argumentos vindos da url (verb,until,from,set,metadataprefix,resumptioToken)
     * @return mostra o xml do list record desejado
     * @description - Metodo responsavel em mostrar o xml do list records, o metodo executado no controller
     * ele chama os demais metodos que fazem as verificacoes de erros
     * @author: Eduardo 
     */
    public function identify($data) {
        session_write_close();
        ini_set('max_execution_time', '0');
        $collection = '';
        $this->config();
        $this->xml_creater = new ANDS_Response_XML($data);
        $this->xml_creater->add2_verbNode('repositoryName',$this->identifyResponse["repositoryName"]);
        $this->xml_creater->add2_verbNode('baseURL',$this->identifyResponse["baseURL"]); 
        $this->xml_creater->add2_verbNode('protocolVersion',$this->identifyResponse["protocolVersion"]); 
        $this->xml_creater->add2_verbNode('earliestDatestamp',$this->identifyResponse["earliestDatestamp"]);
        $this->xml_creater->add2_verbNode('deletedRecord',$this->identifyResponse["deletedRecord"]); 
        $this->xml_creater->add2_verbNode('granularity',$this->identifyResponse["granularity"]); 
        $this->xml_creater->add2_verbNode('adminEmail',$this->adminEmail); 
        $description_node = $this->xml_creater->add2_verbNode('description');
        $this->working_node = $this->xml_creater->addChild($description_node, 'oai-identifier');
        $this->working_node->setAttribute('xmlns', "http://www.openarchives.org/OAI/2.0/oai-identifier");
        $this->working_node->setAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");
        $this->working_node->setAttribute('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/oai-identifier http://www.openarchives.org/OAI/2.0/oai-identifier.xsd');
        $this->xml_creater->addChild($this->working_node, 'scheme',  'oai');
        $this->xml_creater->addChild($this->working_node, 'repositoryIdentifier',  $this->repositoryIdentifier);
        $this->xml_creater->addChild($this->working_node, 'delimiter',  ':');
        $this->xml_creater->addChild($this->working_node, 'sampleIdentifier',  'oai:'.$this->repositoryIdentifier.':1');
       /* 
        $this->xml_creater->addChild($setNode,'setName',$collection->post_title);
            $description_node = $this->xml_creater->addChild($setNode,'setDescription');
            $this->working_node = $this->xml_creater->addChild($description_node, 'oai_dc:dc');
            $this->working_node->setAttribute('xmlns:oai_dc', "http://www.openarchives.org/OAI/2.0/oai_dc/");
            $this->working_node->setAttribute('xmlns:dc', "http://purl.org/dc/elements/1.1/");
            $this->working_node->setAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");
            $this->working_node->setAttribute('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd');
            $this->xml_creater->addChild($this->working_node, 'dc:description',$collection->post_content);
        
        foreach ($objects as $object) {
            $collection = $this->get_collection_by_category_root($object->term_id)[0];
            $object = get_post($object->ID);
            $identifier = remove_accent(site_url()) . '_' . $object->ID;
            $datestamp = $this->formatDatestamp($object->post_date);
            $setspec = $collection->ID;
            $cur_header = $this->xml_creater->create_header($identifier, $datestamp, $setspec);
            //$cur_record = $this->xml_creater->create_record();
            //$this->working_node = $this->xml_creater->create_metadata($cur_record);
            //$this->create_metadata_node($object,$collection);
            // $this->insert_xml($object);
        }*/
        //resumptionToken
        header($this->CONTENT_TYPE);
        if (isset($this->xml_creater)) {
            $this->xml_creater->display();
        } else {
            exit("There is a bug in codes");
        }
    }

    

}
