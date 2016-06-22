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

class OAIPMHListSetsModel extends OAIPMHModel {

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
     * function list_object_mapped()
     * @return array As colecoes que possuem algum tipo de mapeamento
     * @description metodo em retornar apenas as colecoes mapeadas
     * @author: Eduardo Humberto 
     */
    public function list_collections_mapped() {
        $mapped_collections = array();
        $all_collections = $this->get_all_collections();
        if ($all_collections) {
            foreach ($all_collections as $collection) {
                if ($this->has_mapping($collection->ID)) {
                    $mapped_collections[] = $collection;
                }
            }
        }
        return $mapped_collections;
    }

   
    
    public function limit_data($objects){
        $conter = 0;
        $result_objects = array();
        foreach ($objects as $object) {
            if($conter>=$this->deliveredrecords&&($this->deliveredrecords+$this->MAXRECORDS)>$conter){
                $result_objects[] = $object;
            }
            $conter++;
        }

        return $result_objects;
    }

    /**
     * @signature - list_records
     * @param  array $param Os argumentos vindos da url (verb,until,from,set,metadataprefix,resumptioToken)
     * @return mostra o xml do list record desejado
     * @description - Metodo responsavel em mostrar o xml do list records, o metodo executado no controller
     * ele chama os demais metodos que fazem as verificacoes de erros
     * @author: Eduardo 
     */
    public function list_sets($data) {
        session_write_close();
        ini_set('max_execution_time', '0');
        $collection = '';
        $this->config();
        $this->initiate_variables($data);
        $collections = $this->list_collections_mapped();
        $numRows = count($collections);
        if($numRows==0){
            $this->errors[] = $this->oai_error('noSetHierarchy');
            $this->oai_exit($data,$this->errors);
        }
        $collections = $this->limit_data($collections);
        $this->verify_resumption_token($numRows);
        $this->xml_creater = new ANDS_Response_XML($data);
        foreach ($collections as $collection) {
            $setNode =  $this->xml_creater->add2_verbNode("set");
            $this->xml_creater->addChild($setNode,'setSpec',$collection->ID);
            $this->xml_creater->addChild($setNode,'setName',$collection->post_title);
            $description_node = $this->xml_creater->addChild($setNode,'setDescription');
            $this->working_node = $this->xml_creater->addChild($description_node, 'oai_dc:dc');
            $this->working_node->setAttribute('xmlns:oai_dc', "http://www.openarchives.org/OAI/2.0/oai_dc/");
            $this->working_node->setAttribute('xmlns:dc', "http://purl.org/dc/elements/1.1/");
            $this->working_node->setAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");
            $this->working_node->setAttribute('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd');
            $this->xml_creater->addChild($this->working_node, 'dc:description',htmlspecialchars($collection->post_content));
            // $this->insert_xml($object);
        }
        //resumptionToken
        $this->add_resumption_token_xml($numRows);
        header($this->CONTENT_TYPE);
        if (isset($this->xml_creater)) {
            $this->xml_creater->display();
        } else {
            exit("There is a bug in codes");
        }
    }

   

    /**
     * @signature - create_metadata_node
     * @param  wp_post $object O objeto do tipo post
     * @param  wp_post $collection O objeto da colecao
     * @return Adciona no  noh <metadata> os valores necessarios
     * @description - Metodo responsavel realizar o povoamento no noh metadata
     * @author: Eduardo 
     */
    protected function create_metadata_node($object,$collection) {
        $this->working_node = $this->xml_creater->addChild($this->working_node, 'oai_dc:dc');
        $this->working_node->setAttribute('xmlns:oai_dc', "http://www.openarchives.org/OAI/2.0/oai_dc/");
        $this->working_node->setAttribute('xmlns:dc', "http://purl.org/dc/elements/1.1/");
        $this->working_node->setAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");
        $this->working_node->setAttribute('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd');
        $maps = $this->get_mapping_value($object,$collection);
        if ($maps) {
            foreach ($maps as $map) {
                if (isset($map['attribute_value'])) {
                    //$this->add_value_metadata($map['tag'], $map['value'], $map['attribute_value'], $map['attribute_name']);
                } else {
                     $this->xml_creater->addChild($this->working_node, 'dc:' . $map['tag'], $map['value']);
                }
            }
        }
    }
    
    public function initiate_variables($data) {
        $query = '';
        if (isset($data['resumptionToken'])) {
            if (!file_exists(TOKEN_PREFIX . $data['resumptionToken'])) {
                $this->errors[] = $this->oai_error('badResumptionToken', '', $data['resumptionToken']);
            } else {
                $readings = $this->readResumToken(TOKEN_PREFIX . $data['resumptionToken']);
                if ($readings == false) {
                    $this->errors[] = $this->oai_error('badResumptionToken', '', $data['resumptionToken']);
                } else {
                    list($this->deliveredrecords, $this->from, $this->until, $this->sets, $this->metadataPrefix) = $readings;
                }
            }
            //
        } else {
            $this->deliveredrecords = 0;
            $this->sets = '-';
            $this->from = '-';
            $this->until = '-';
            $this->metadataPrefix =  '-'; 
        }
        
        if(is_array($this->errors)&&count($this->errors)>0){
            $this->oai_exit($data,$this->errors);
        }
    }
    
    public function verify_resumption_token($numRows) {
        if ($numRows - $this->deliveredrecords > $this->MAXRECORDS) {
            if(implode(',',$this->sets)==''){
                 $this->sets = '-';
            }else{
                $this->sets = implode(',',$this->sets);
            }
            $this->cursor = (int) $this->deliveredrecords + $this->MAXRECORDS;
            $this->restoken = $this->createResumToken($this->cursor, $this->from,$this->until,$this->sets, $this->metadataPrefix);
           // var_dump(time() + TOKEN_VALID,date("Y-m-d\TH:i:s\Z", time() + TOKEN_VALID));
            $this->expirationdatetime = date("Y-m-d\TH:i:s\Z", time() + TOKEN_VALID);
             //$this->expirationdatetime = gmstrftime('%Y-%m-%d\T%T\Z', time() + TOKEN_VALID);
        }
    }
    
    public function add_resumption_token_xml($numRows) {
        // ResumptionToken
        if ($this->restoken!='-') {
            if ($this->expirationdatetime) {
                $this->xml_creater->create_resumpToken($this->restoken, $this->expirationdatetime, $numRows, $this->cursor);
            } else {
                $this->xml_creater->create_resumpToken('', null, $numRows, $this->deliveredrecords);
            }
        }
    }

}
