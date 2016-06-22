<?php

/**
 * Author: Eduardo hUMBERTO
 */
include_core_wp();
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../property/property_model.php');
require_once(dirname(__FILE__) . '../../category/category_model.php');

class OAIPMHModel extends Model {

    var $identifyResponse = array();
    var $deletedRecord = '';
    var $adminEmail = '';
    var $compression = '';
    var $expirationdatetime = '';
    var $delimiter = ':';
    var $show_identifier;
    var $SETS;
    var $METADATAFORMATS;
    var $supported_formats;
    var $MAXRECORDS;
    var $CONTENT_TYPE;
    var $charset;
    var $xmlescaped;
    var $text;   
    var $code;
    /**
     * @signature - config
     * @return Seta os valores iniciais da variaveis da classe
     * @author: Eduardo 
     */
    public function config() {
        $this->CONTENT_TYPE = 'Content-Type: text/xml';
        $this->identifyResponse["repositoryName"] = get_bloginfo( 'name');
        $this->identifyResponse['protocolVersion'] = '2.0';
        $this->identifyResponse['baseURL'] = get_bloginfo( 'url').'/oai/socialdb-oai/';
        $this->identifyResponse["earliestDatestamp"] = '2006-06-01';
        $this->identifyResponse["deletedRecord"] = 'no';
        $this->identifyResponse["granularity"] = 'YYYY-MM-DDThh:mm:ssZ';
        //$this->deletedRecord = $identifyResponse["deletedRecord"]; // a shorthand for checking the configuration of Deleted Records
// MAY (only one)
//granularity is days
//$granularity          = 'YYYY-MM-DD';
// granularity is seconds

// this is appended if your granularity is seconds.
// do not change
        if (strcmp($this->identifyResponse["granularity"], 'YYYY-MM-DDThh:mm:ssZ') == 0) {
            $this->identifyResponse["earliestDatestamp"] = $this->identifyResponse["earliestDatestamp"] . 'T00:00:00Z';
        }

// MUST (multiple)
// please adjust
        $this->adminEmail = get_bloginfo( 'admin_email');

        /** Compression methods supported. Optional (multiple). Default: null.
         * 
         * Currently only gzip is supported (you need output buffering turned on, 
         * and php compiled with libgz). 
         * The client MUST send "Accept-Encoding: gzip" to actually receive 
         */
// $compression		= array('gzip');
        $this->compression = null;

// MUST (only one)
// You may choose any name, but for repositories to comply with the oai 
// format it has to be unique identifiers for items records. 
// see: http://www.openarchives.org/OAI/2.0/guidelines-oai-identifier.htm
// Basically use domainname
// please adjust
        $url = array_reverse(explode('/', str_replace('http://', '', get_bloginfo( 'url'))));
        if(is_array($url)&& count($url)>1){
             $this->repositoryIdentifier = implode('.', $url);
        }else{
             $this->repositoryIdentifier = $url[0];
        }

// For RIF-CS, especially with ANDS, each registryObject much has a group for the ownership of data.
// For detail please see ANDS guide on its web site. Each data provider should have only one REG_OBJ_GROUP
// for this purpose.
        define('REG_OBJ_GROUP', 'Something agreed on');

// If Identifier needs to show NODE description. It is defined in identify.php 
// You may include details about your community and friends (other
// data-providers).
// Please check identify.php for other possible containers 
// in the Identify response
        $this->show_identifier = false;
// MUST (only one)



        /** Maximum mumber of the records to deliver
         * (verb is ListRecords)
         * If there are more records to deliver
         * a ResumptionToken will be generated.
         */
         $this->MAXRECORDS = 100;

        /** Maximum mumber of identifiers to deliver
         * (verb is ListIdentifiers)
         * If there are more identifiers to deliver
         * a ResumptionToken will be generated.
         */
        define('MAXIDS', 40);

        /** After 24 hours resumptionTokens become invalid. Unit is second. */
        define('TOKEN_VALID', 24 * 3600);
        define('MY_URI',  get_bloginfo( 'url' ));
        $this->expirationdatetime = gmstrftime('%Y-%m-%dT%TZ', time() + TOKEN_VALID);
        /** Where token is saved and path is included */
        define('TOKEN_PREFIX', dirname(__FILE__).'/socialdb_tokens/');

// define all supported sets in your repository
        $this->SETS = array(
            array('setSpec' => 'class:activity', 'setName' => 'Activities'),
            array('setSpec' => 'class:collection', 'setName' => 'Collections'),
            array('setSpec' => 'class:party', 'setName' => 'Parties')/* ,
                  array('setSpec'=>'phdthesis', 'setName'=>'PHD Thesis', 'setDescription'=>'<oai_dc:dc
                  xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"
                  xmlns:dc="http://purl.org/dc/elements/1.1/"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/
                  http://www.openarchives.org/OAI/2.0/oai_dc.xsd">
                  <dc:description>This set contains metadata describing electronic music recordings made during the 1950ies</dc:description>
                  </oai_dc:dc>') //,
                  // array('setSpec'=>'math', 'setName'=>'Mathematics') ,
                  // array('setSpec'=>'phys', 'setName'=>'Physics')
                 */);

// define all supported metadata formats, has to be an array
//
// myhandler is the name of the file that handles the request for the 
// specific metadata format.
// [record_prefix] describes an optional prefix for the metadata
// [record_namespace] describe the namespace for this prefix

        $this->METADATAFORMATS = array(
           // 'rif' => array('metadataPrefix' => 'rif',
             //   'schema' => 'http://services.ands.org.au/sandbox/orca/schemata/registryObjects.xsd',
             //   'metadataNamespace' => 'http://ands.org.au/standards/rif-cs/registryObjects/',
             //   'myhandler' => 'record_rif.php'
          //  ),
            'oai_dc' => array('metadataPrefix' => 'oai_dc',
                'schema' => 'http://www.openarchives.org/OAI/2.0/oai_dc.xsd',
                'metadataNamespace' => 'http://www.openarchives.org/OAI/2.0/oai_dc/',
                'myhandler' => 'record_dc.php',
                'record_prefix' => 'dc',
                'record_namespace' => 'http://purl.org/dc/elements/1.1/'
            )
        );
        
        $this->supported_formats = array('oai_dc');
        

        if (!is_array($this->METADATAFORMATS)) {
            exit("Configuration of METADATAFORMAT has been wrongly set. Correct your " . __FILE__);
        }

// The shorthand of xml schema namespace, no need to change this
        define('XMLSCHEMA', 'http://www.w3.org/2001/XMLSchema-instance');


// the charset you store your metadata in your database
// currently only utf-8 and iso8859-1 are supported
        $this->charset = "iso8859-1";

// if entities such as < > ' " in your metadata has already been escaped 
// then set this to true (e.g. you store < as &lt; in your DB)
        $this->xmlescaped = false;

// We store multiple entries for one element in a single row 
// in the database. SQL['split'] lists the delimiter for these entries.
// If you do not do this, do not define $SQL['split']
// $SQL['split'] = ';'; 
// the name of the table where your store your metadata's header
        $this->SQL['table'] = 'oai_headers';

// the name of the column where you store the unique identifiers
// pointing to your item.
// this is your internal identifier for the item
        $this->SQL['identifier'] = 'oai_identifier';

        $this->SQL['metadataPrefix'] = 'oai_metadataprefix';

// If you want to expand the internal identifier in some way
// use this (but not for OAI stuff, see next line)
        $this->idPrefix = '';

// this is your external (OAI) identifier for the item
// this will be expanded to
// oai:$repositoryIdentifier:$idPrefix$SQL['identifier']
// should not be changed
//
// Commented out 24/11/10 14:19:09 
// $oaiprefix = "oai".$delimiter.$repositoryIdentifier.$delimiter.$idPrefix; 
        $this->oaiprefix = "";

// adjust anIdentifier with sample contents an identifier
// $sampleIdentifier     = $oaiprefix.'anIdentifier';
// the name of the column where you store your datestamps
        $this->SQL['datestamp'] = 'datestamp';

// the name of the column where you store information whether
// a record has been deleted. Leave it as it is if you do not use
// this feature.
        $this->SQL['deleted'] = 'deleted';

// to be able to quickly retrieve the sets to which one item belongs,
// the setnames are stored for each item
// the name of the column where you store sets
        $this->SQL['set'] = 'oai_set';
    }

    /** Dump information of a varible for debugging,
     * only works when SHOW_QUERY_ERROR is true.
     * \param $var_name Type: string Name of variable is being debugded
     * \param $var Type: mix Any type of varibles used in PHP
     * \see SHOW_QUERY_ERROR in oaidp-config.php
     */
    function debug_var_dump($var_name, $var) {
        if (SHOW_QUERY_ERROR) {
            echo "Dumping \${$var_name}: \n";
            var_dump($var) . "\n";
        }
    }

    /** Prints human-readable information about a variable for debugging,
     * only works when SHOW_QUERY_ERROR is true.
     * \param $var_name Type: string Name of variable is being debugded
     * \param $var Type: mix Any type of varibles used in PHP
     * \see SHOW_QUERY_ERROR in oaidp-config.php
     */
    function debug_print_r($var_name, $var) {
        if (SHOW_QUERY_ERROR) {
            echo "Printing \${$var_name}: \n";
            print_r($var) . "\n";
        }
    }

    /** Prints a message for debugging,
     * only works when SHOW_QUERY_ERROR is true.
     * PHP function print_r can be used to construct message with <i>return</i> parameter sets to true.
     * \param $msg Type: string Message needs to be shown
     * \see SHOW_QUERY_ERROR in oaidp-config.php
     */
    function debug_message($msg) {
        if (!SHOW_QUERY_ERROR)
            return;
        echo $msg, "\n";
    }

    /** Check if provided correct arguments for a request.
     *
     * Only number of parameters is checked.
     * metadataPrefix has to be checked before it is used.
     * set has to be checked before it is used.
     * resumptionToken has to be checked before it is used.
     * from and until can easily checked here because no extra information 
     * is needed.
     */
    function checkArgs($args, $checkList) {
//	global $errors, $TOKEN_VALID, $METADATAFORMATS;
        global $errors, $METADATAFORMATS;
//	$verb = $args['verb'];
        unset($args["verb"]);

        debug_print_r('checkList', $checkList);
        debug_print_r('args', $args);

        // "verb" has been checked before, no further check is needed
        if (isset($checkList['required'])) {
            for ($i = 0; $i < count($checkList["required"]); $i++) {
                debug_message("Checking: par$i: " . $checkList['required'][$i] . " in ");
                debug_var_dump("isset(\$args[\$checkList['required'][\$i]])", isset($args[$checkList['required'][$i]]));
                // echo "key exists". array_key_exists($checkList["required"][$i],$args)."\n";
                if (isset($args[$checkList['required'][$i]]) == false) {
                    // echo "caught\n";
                    $errors[] = oai_error('missingArgument', $checkList["required"][$i]);
                } else {
                    // if metadataPrefix is set, it is in required section
                    if (isset($args['metadataPrefix'])) {
                        $metadataPrefix = $args['metadataPrefix'];
                        // Check if the format is supported, it has enough infor (an array), last if a handle has been defined.
                        if (!array_key_exists($metadataPrefix, $METADATAFORMATS) || !(is_array($METADATAFORMATS[$metadataPrefix]) || !isset($METADATAFORMATS[$metadataPrefix]['myhandler']))) {
                            $errors[] = oai_error('cannotDisseminateFormat', 'metadataPrefix', $metadataPrefix);
                        }
                    }
                    unset($args[$checkList["required"][$i]]);
                }
            }
        }
        debug_message('Before return');
        debug_print_r('errors', $errors);
        if (!empty($errors))
            return;

        // check to see if there is unwanted	
        foreach ($args as $key => $val) {
            debug_message("checkArgs: $key");
            if (!in_array($key, $checkList["ops"])) {
                debug_message("Wrong\n" . print_r($checkList['ops'], true));
                $errors[] = oai_error('badArgument', $key, $val);
            }
            switch ($key) {
                case 'from':
                case 'until':
                    if (!checkDateFormat($val)) {
                        $errors[] = oai_error('badGranularity', $key, $val);
                    }
                    break;

                case 'resumptionToken':
                    // only check for expairation
                    if ((int) $val + TOKEN_VALID < time())
                        $errors[] = oai_error('badResumptionToken');
                    break;
            }
        }
    }

    /** Validates an identifier. The pattern is: '/^[-a-z\.0-9]+$/i' which means 
     * it accepts -, letters and numbers. 
     * Used only by function <B>oai_error</B> code idDoesNotExist. 
     * \param $url Type: string
     */
    function is_valid_uri($url) {
        return((bool) preg_match('/^[-a-z\.0-9]+$/i', $url));
    }

    /** Validates attributes come with the query.
     * It accepts letters, numbers, ':', '_', '.' and -. 
     * Here there are few more match patterns than is_valid_uri(): ':_'.
     * \param $attrb Type: string
     */
    function is_valid_attrb($attrb) {
        return preg_match("/^[_a-zA-Z0-9\-\:\.]+$/", $attrb);
    }

    /** All datestamps used in this system are GMT even
     * return value from database has no TZ information
     */
    function formatDatestamp($datestamp) {
        return date("Y-m-d\TH:i:s\Z", strtotime($datestamp));
    }

    /** The database uses datastamp without time-zone information.
     * It needs to clean all time-zone informaion from time string and reformat it
     */
    function checkDateFormat($date) {
        $date = str_replace(array("T", "Z"), " ", $date);
        $time_val = strtotime($date);
        if (!$time_val)
            return false;
        if (strstr($date, ":")) {
            return date("Y-m-d H:i:s", $time_val);
        } else {
            return date("Y-m-d", $time_val);
        }
    }

    /** Retrieve all defined 'setSpec' from configuraiton of $SETS. 
     * It is used by ANDS_TPA::create_obj_node();
     */
    function prepare_set_names() {
        global $SETS;
        $n = count($SETS);
        $a = array_fill(0, $n, '');
        for ($i = 0; $i < $n; $i++) {
            $a[$i] = $SETS[$i]['setSpec'];
        }
        return $a;
    }

    /** Finish a request when there is an error: send back errors. */
    function oai_exit($args,$errors) {
//	global $CONTENT_TYPE;
        header($this->CONTENT_TYPE);
        $e = new ANDS_Error_XML($args, $errors);
        $e->display();
        exit();
    }

// ResumToken section
    /** Generate a string based on the current Unix timestamp in microseconds for creating resumToken file name. */
    function get_token() {
        list($usec, $sec) = explode(" ", microtime());
        return ((int) ($usec * 1000) + (int) ($sec * 1000));
    }

    /** Create a token file. 
     * It has three parts which is separated by '#': cursor, extension of query, metadataPrefix.
     * Called by listrecords.php.
     */
    function createResumToken($cursor, $from,$until,$sets, $metadataPrefix) {
        $token = $this->get_token();
        $fp = fopen(TOKEN_PREFIX . $token, 'w');
        if ($fp == false) {
            exit("Cannot write. Writer permission needs to be changed.");
        }
        fputs($fp, "$cursor#");
        fputs($fp, "$from#");
        fputs($fp, "$until#");
        fputs($fp, "$sets#");
        fputs($fp, "$metadataPrefix#");
        fclose($fp);
        return $token;
    }

    /** Read a saved ResumToken */
    function readResumToken($resumptionToken) {
        $rtVal = false;
        $fp = fopen($resumptionToken, 'r');
        if ($fp != false) {
            $filetext = fgets($fp, 255);
            $textparts = explode('#', $filetext);
            fclose($fp);
            unlink($resumptionToken);
            $rtVal = array((int) $textparts[0], $textparts[1], $textparts[2],$textparts[3],$textparts[4]);
        }
        return $rtVal;
    }

    ######################################################################
    

    /**
     * function has_mapping($collection_id)
     * @param int $collection_id
     * @return boolean 
     * @description metodo responsavel em verificar se existe um mapeamento na colecao
     * @author: Eduardo Humberto 
     */
    public function has_mapping($collection_id) {
        $channels = get_post_meta($collection_id, 'socialdb_collection_channel');
        if (is_array($channels)) {
            $json = [];
            foreach ($channels as $ch) {
                $ch = get_post($ch);
                if(isset($ch->ID)){
                    $oai_pmhdc = wp_get_object_terms($ch->ID, 'socialdb_channel_type');
                    if (!empty($ch) && !is_wp_error($oai_pmhdc) && isset($oai_pmhdc[0]->name) && $oai_pmhdc[0]->name == 'socialdb_channel_oaipmhdc') {
                        return true;
                    }
                }    
            }
            return false;
        } else {
            return false;
        }
    }
    
    /**
     * function get_mapping_harvested($collection_id)
     * @param int $collection_id
     * @return boolean 
     * @description metodo responsavel em retornar um array com as ids dos mapeamentos que possuem harvesting
     * @author: Eduardo Humberto 
     */
    public function get_mapping_harvested($collection_id) {
        $harvested_mappings = array();
        $channels = get_post_meta($collection_id, 'socialdb_collection_channel');
        if (is_array($channels)) {
            $json = [];
            foreach ($channels as $ch) {
                $ch = get_post($ch);
                if(isset($ch->ID)){
                    $harvest = get_post_meta($ch->ID, 'socialdb_channel_oaipmhdc_is_harvesting', true);
                    if($harvest&&$harvest=='enabled'){
                        $harvested_mappings[] = $ch->ID;
                    }
                }
            }
        } else {
            return false;
        }
        
        if(empty($harvested_mappings)){
            return false;
        }else{
            return $harvested_mappings;
        }
    }

    

    /**
     * function list_collections_mapped()
     * @return array As colecoes que possuem algum tipo de mapeamento
     * @description metodo em retornar apenas as colecoes mapeadas
     * @author: Eduardo Humberto 
     */
    public function get_harvesting_mappings() {
        $array_temp = array();
        $harvesting_mappings = array();
        $all_collections = $this->get_all_collections();  
        if ($all_collections) {
            foreach ($all_collections as $collection) {
                if ($this->has_mapping($collection->ID)) {
                    $array_temp['mappings'] = $this->get_mapping_harvested($collection->ID);
                    $array_temp['collection_id'] = $collection->ID;
                    if($array_temp['mappings']){
                       $harvesting_mappings[] = $array_temp;
                    }
                }
            }
        } 
        return $harvesting_mappings;
    }
    
     /**
     * function list_collections_mapped()
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
   ##### helpers
   /** filter for until, appends to the end of SQL query */
   function untilQuery($until) {
        $until = $this->checkDateFormat($until);
        return " AND p.post_date <= '$until'";
    }

    /** filter for from , appends to the end of SQL query */
    function fromQuery($from) {
        $from = $this->checkDateFormat($from);
        return " AND p.post_date >= '$from'";
    }

    /** filter for sets,  appends to the end of SQL query */
    function setQuery($set) {
        return 't.term_taxonomy_id IN (' . $set . ")";
    }

    /** utility funciton to mapping error codes to readable messages */
    function oai_error($code, $argument = '', $value = '') {
        switch ($code) {
            case 'badArgument' :
                $this->text = "The argument '$argument' (value='$value') included in the request is not valid.";
                break;

            case 'badGranularity' :
                $this->text = "The value '$value' of the argument '$argument' is not valid.";
                $this->code = 'badArgument';
                break;

            case 'badResumptionToken' :
                $this->text = "The resumptionToken '$value' does not exist or has already expired.";
                break;

            case 'badRequestMethod' :
                $this->text = "The request method '$argument' is unknown.";
                $this->code = 'badVerb';
                break;

            case 'badVerb' :
                $this->text = "The verb '$argument' provided in the request is illegal.";
                break;

            case 'cannotDisseminateFormat' :
                $this->text = "The metadata format '$value' given by $argument is not supported by this repository.";
                break;

            case 'exclusiveArgument' :
                $this->text = 'The usage of resumptionToken as an argument allows no other arguments.';
                $this->code = 'badArgument';
                break;

            case 'idDoesNotExist' :
                $this->text = "The value '$value' of the identifier does not exist in this repository.";
                if (!is_valid_uri($value)) {
                    $this->code = 'badArgument';
                    $this->text .= ' Invalidated URI has been detected.';
                }
                break;

            case 'missingArgument' :
                $this->text = "The required argument '$argument' is missing in the request.";
                $this->code = 'badArgument';
                break;

            case 'noRecordsMatch' :
                $this->text = 'The combination of the given values results in an empty list.';
                break;

            case 'noMetadataFormats' :
                $this->text = 'There are no metadata formats available for the specified item.';
                break;

            case 'noVerb' :
                $this->text = 'The request does not provide any verb.';
                $this->code = 'badVerb';
                break;

            case 'noSetHierarchy' :
                $this->text = 'This repository does not support sets.';
                break;

            case 'sameArgument' :
                $this->text = 'Do not use the same argument more than once.';
                $this->code = 'badArgument';
                break;

            case 'sameVerb' :
                $this->text = 'Do not use verb more than once.';
                $this->code = 'badVerb';
                break;

            case 'notImp' :
                $this->text = 'Not yet implemented.';
                $this->code = 'debug';
                break;

            default:
                $this->text = "Unknown error: $this->code: '$this->code', argument: '$argument', value: '$value'";
                $this->code = 'badArgument';
        }
        return $this->code . "|" . $this->text;
    }
     /**
     * function get_metadata_formats($collection_id)
     * @param int $object_id
     * @return boolean 
     * @description metodo responsavel em verificar os tipos de metadados
     * @author: Eduardo Humberto 
     */
    public function get_metadata_formats($object_id) {
        $type = array();
        $channels = get_post_meta($object_id, 'socialdb_channel_id');
        if (is_array($channels)) {
            foreach ($channels as $ch) {
                $ch = get_post($ch);
                $oai_pmhdc = wp_get_object_terms($ch->ID, 'socialdb_channel_type');
                if (!empty($ch) && !empty($oai_pmhdc) && isset($oai_pmhdc[0]->name) && $oai_pmhdc[0]->name == 'socialdb_channel_oaipmhdc') {
                    $type[] = 'oai_dc';
                }elseif(!empty($ch) && !empty($oai_pmhdc) && isset($oai_pmhdc[0]->name) && $oai_pmhdc[0]->name == 'socialdb_channel_rdf'){
                    $type[] = 'rdf';
                }
            }
            return $type;
        } else {
            return $type;
        }
    }

}
