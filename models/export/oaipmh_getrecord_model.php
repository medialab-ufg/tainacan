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

class OAIPMHGetRecordModel extends OAIPMHModel {

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



    /**
     * @signature - list_records
     * @param  array $param Os argumentos vindos da url (verb,until,from,set,metadataprefix,resumptioToken)
     * @return mostra o xml do list record desejado
     * @description - Metodo responsavel em mostrar o xml do list records, o metodo executado no controller
     * ele chama os demais metodos que fazem as verificacoes de erros
     * @author: Eduardo 
     */
    public function get_record($data) {
        session_write_close();
        ini_set('max_execution_time', '0');
        $collection = '';
        $this->config();
        $object_id = str_replace('oai:'.$this->repositoryIdentifier.':','', $data['identifier']);
        $object = get_post($object_id);
        if(isset($object->ID)&&get_post_status($object->ID )=='publish'){
            $formats = $this->get_metadata_formats($object->ID);
            if(empty($formats)||!in_array($data['metadataPrefix'],$formats)){
                $this->errors[] = $this->oai_error('cannotDisseminateFormat');
                $this->oai_exit($data,$this->errors);
            }
        }else{
            $this->errors[] = $this->oai_error('idDoesNotExist');
            $this->oai_exit($data,$this->errors);
        }
        $this->initiate_variables($data);
        $this->xml_creater = new ANDS_Response_XML($data);
       // foreach ($objects as $object) {
        $collection = $this->get_collection_by_object($object->ID)[0];
        $identifier = 'oai:'.$this->repositoryIdentifier.':'. $object->ID;
        $datestamp = $this->formatDatestamp($object->post_date);
        $setspec = $collection->ID;
        $cur_record = $this->xml_creater->create_record();
        $cur_header = $this->xml_creater->create_header($identifier, $datestamp, $setspec,$cur_record);
        $this->working_node = $this->xml_creater->create_metadata($cur_record);
        $this->create_metadata_node($object,$collection,$cur_record);
            // $this->insert_xml($object);
        //}
        header($this->CONTENT_TYPE);
        if (isset($this->xml_creater)) {
            $this->xml_creater->display();
        } else {
            exit("There is a bug in codes");
        }
    }

    /**
     * @signature - get_mapping_value
     * @param  wp_post $object O objeto do tipo post
     * @param  wp_post $collection O objeto da colecao
     * @return array Com o mapeamento com seu valor respectivo
     * @description - Metodo responsavel em buscar o mapeamento especifico do objeto com seu valor
     * @author: Eduardo 
     */
    public function get_mapping_value($object,$collection) {
        $maps = [];
        $files = [];
        $mapping_id = $this->get_mapping($object->ID);
        if ($mapping_id) {
            $array_mapping = unserialize(get_post_meta($mapping_id, 'socialdb_channel_oaipmhdc_mapping', true));
            foreach ($array_mapping as $map) {
                if ($map['socialdb_entity'] == 'post_title'):
                    $map['value'] = $object->post_title;
                    $maps[] = $map;
                elseif ($map['socialdb_entity'] == 'post_content'):
                    $map['value'] = htmlspecialchars($object->post_content);
                    $maps[] = $map;
                elseif ($map['socialdb_entity'] == 'post_permalink'):
                    $map['value'] = get_post_meta($object->ID, 'socialdb_uri_imported', true);
                    $maps[] = $map;
                elseif ($map['socialdb_entity'] == 'socialdb_object_from'):
                    $map['value'] = get_post_meta($object->ID, 'socialdb_object_from', true);
                    $maps[] = $map;
                 elseif ($map['socialdb_entity'] == 'socialdb_object_dc_type'):
                    $map['value'] = get_post_meta($object->ID, 'socialdb_object_dc_type', true);
                    $maps[] = $map;
                elseif ($map['socialdb_entity'] == 'socialdb_object_dc_source'):
                    $map['value'] = get_post_meta($object->ID, 'socialdb_object_dc_source', true);
                    $maps[] = $map;
                elseif ($map['socialdb_entity'] == 'socialdb_object_content'):
                    $map['value'] = get_post_meta($object->ID, 'socialdb_object_content', true);
                    if($map['value']!=''&&  is_numeric($map['value'])){
                        $map['value'] = wp_get_attachment_url($map['value']);
                    }
                    $maps[] = $map;  
                elseif ($map['socialdb_entity'] == 'tag'):
                    $tags = wp_get_object_terms($object->ID, 'socialdb_tag_type');
                    if (is_array($tags)):
                        foreach ($tags as $tag) {
                            $map['value'] = $tag->name;
                            $maps[] = $map;
                        }
                    endif; 
                elseif (strpos($map['socialdb_entity'], "termproperty_") !== false):    
                    $hierarchy_names = [];
                    $category_model = new CategoryModel;
                    $trans = array("termproperty_" => "");
                    $property_id = strtr($map['socialdb_entity'], $trans);
                    $id = get_term_meta($property_id, 'socialdb_property_term_root', true);
                    $categories = wp_get_object_terms($object->ID, 'socialdb_category_type');
                    if (is_array($categories)):
                        foreach ($categories as $category) {
                            if($id==$category_model->get_category_facet_parent($category->term_id, $collection->ID)){
                                $map['value'] = $this->get_hierarchy_names($category->term_id,$id);
                                $maps[] = $map;
                            }
                        }
                    endif; 
                elseif (strpos($map['socialdb_entity'], "objectproperty_") !== false):
                    $trans = array("objectproperty_" => "");
                    $id = strtr($map['socialdb_entity'], $trans);
                    $object_properties = get_post_meta($object->ID,'socialdb_property_'.$id);
                    foreach ($object_properties as $object_property) {
                        $map['value'] = get_post($object_property);
                        $maps[] = $map;
                    }
                elseif (strpos($map['socialdb_entity'], "dataproperty_") !== false):
                    $trans = array("dataproperty_" => "");
                    $id = strtr($map['socialdb_entity'], $trans);
                    $data_properties = get_post_meta($object->ID,'socialdb_property_'.$id);
                    foreach ($data_properties as $data_property) {
                        $map['value'] = $data_property;
                        $maps[] = $map;
                    }
                endif;
            }
            $has_files = get_post_meta($mapping_id, 'socialdb_channel_oaipmhdc_import_object', true);
            if($has_files=='true'){
                $files = $this->list_files_to_export(array('object_id'=>$object->ID));
            }
        }
        $result['metadata'] = $maps;
        $result['files'] = $files;
        return $result;
    }

    /**
     * @signature - create_metadata_node
     * @param  wp_post $object O objeto do tipo post
     * @param  wp_post $collection O objeto da colecao
     * @return Adciona no  noh <metadata> os valores necessarios
     * @description - Metodo responsavel realizar o povoamento no noh metadata
     * @author: Eduardo 
     */
    protected function create_metadata_node($object,$collection,$record_node) {
        $this->working_node = $this->xml_creater->addChild($this->working_node, 'oai_dc:dc');
        $this->working_node->setAttribute('xmlns:oai_dc', "http://www.openarchives.org/OAI/2.0/oai_dc/");
        $this->working_node->setAttribute('xmlns:dc', "http://purl.org/dc/elements/1.1/");
        $this->working_node->setAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");
        $this->working_node->setAttribute('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd');
        $maps = $this->get_mapping_value($object,$collection);
        if ($maps['metadata']) {
            foreach ($maps['metadata'] as $map) {
                if (isset($map['attribute_value'])) {
                    $node = $this->xml_creater->addChild($this->working_node, 'dc:' . $map['tag'], $map['value']);
                    $node->setAttribute($map['attribute_name'], $map['attribute_value']);
                    //$this->add_value_metadata($map['tag'], $map['value'], $map['attribute_value'], $map['attribute_name']);
                } else {
                     $this->xml_creater->addChild($this->working_node, 'dc:' . $map['tag'], html_entity_decode($map['value']));
                }
            }
        }
        if($maps['files']&&  is_array($maps['files'])&&!empty($maps['files'])){
            $file_node = $this->xml_creater->addChild($record_node, 'files');
            foreach ($maps['files'] as $file) {
                $url_node = $this->xml_creater->addChild($file_node, 'url',$file['url']);
                $url_node->setAttribute('size', $file['size']);
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
                    list($this->deliveredrecords, $this->from, $this->until, $sets, $this->metadataPrefix) = $readings;
                    if($sets=='-'){
                        $this->sets = array();
                    }else{
                         $this->sets = explode(',', $sets);
                    }
                }
            }
            //
        } else {
            $this->deliveredrecords = 0;
            if (isset($data['set'])) {
                if (is_array($data['set'])) {
                    $this->sets = $data['set'];
                } else {
                    $this->sets = array($data['set']);
                }
            } else {
                $this->sets = array();
            }

            if (isset($data['from'])) {
                $this->from = $data['from'];
            } else {
                $this->from = '-';
            }
            if (isset($data['until'])) {
                $this->until = $data['until'];
            } else {
                $this->until = '-';
            }
            
            $this->metadataPrefix =  $data['metadataPrefix']; 
        }
        
        if(is_array($this->errors)&&count($this->errors)>0){
            $this->oai_exit($data,$this->errors);
        }
    }
    
    
      /**
     * @signature - save_file($data)
     * @param array $data Os dados vindos do formulario
     * @return json com os dados do resultado do evento criado
     * @description - Insere um objeto apenas com o titulo
     * @author: Eduardo 
     */
    public function list_files_to_export($data) {
        $real_attachments = [];
        if ($data['object_id']) {
            $post = get_post($data['object_id']);
            $result = '';
            if (!is_object(get_post_thumbnail_id())) {
                $args = array(
                    'post_type' => 'attachment',
                    'numberposts' => -1,
                    'post_status' => null,
                    'post_parent' => $post->ID,
                    'exclude' => get_post_thumbnail_id()
                );
                //  var_dump($args);
                $attachments = get_posts($args);
                $arquivos = get_post_meta($post->ID, '_file_id');
                if ($attachments) {
                    foreach ($attachments as $attachment) {
                        if (in_array($attachment->ID, $arquivos)) {
                            $array_temp['url'] = wp_get_attachment_url( $attachment->ID );
                            $array_temp['size'] = filesize( get_attached_file( $attachment->ID ) );
                            $real_attachments[] = $array_temp;
                        }
                    }
                } 
            }
        }
        if(!empty($real_attachments)){
            return $real_attachments;
        }else{
            return false;
        }
    }
    
    public function get_hierarchy_names($category_id,$facet_id){
         $result = [];
         $flag = false;
         $parents = array_reverse(get_ancestors($category_id, 'socialdb_category_type' ));
         if(is_array($parents)&&!empty($parents)){
             foreach ($parents as $parent) {
                 if($parent==$facet_id){
                     $flag = true;
                 }
                 if($flag)
                 $result[] = get_term_by('id', $parent,  'socialdb_category_type')->name;
                 
             }
         }
         return implode('::', $result);
    }

}
