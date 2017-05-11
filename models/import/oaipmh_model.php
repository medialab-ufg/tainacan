<?php

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../property/property_model.php');
require_once(dirname(__FILE__) . '../../category/category_model.php');

class OAIPMHModel extends Model {

    /**
     * @signature - validate_url($data)
     * @param array $data Os dados vindos do formulario
     * @return array com os dados que serao utilizados para inserir a colecao via OAIPMH
     * @description - funcao que retorna todos os metadatas para realizar o mapeiamento das propriedades do repositorio escolhido
     * @author: Eduardo 
     */
    public function validate_url($data) {
        session_write_close();
        ini_set('max_execution_time', '0');
        $whole_metadatas = array();
        if(strpos($data['url'],'http://')===false && strpos($data['url'],'https://')===false){
            $data['url'] = 'http://'.$data['url'];
        }
        //sets
        if(isset($data['sets'])&&!empty($data['sets'])){
            $sets = explode(',',$data['sets']);
            $url = $data['url'] . "?verb=ListRecords&metadataPrefix=oai_dc";
            foreach ($sets as $set) {
                $url .= '&set='.$set;
            }
        }else{
           $url = $data['url'] . "?verb=ListRecords&metadataPrefix=oai_dc"; // pego os 100 primeiros
         }
        $response_xml_data =  file_get_contents($url); // pego os 100 primeiros
        try {
            $xml = new SimpleXMLElement($response_xml_data);
            $data['number_of_objects'] = $this->count_items($xml, $data);
            $data['token'] = $xml->ListRecords->resumptionToken;
            //import datas
            foreach ($xml->ListRecords->record as $record) {// vou atras de todos os records
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
                break;
                }
                $data['whole_metadatas'] = implode(",", $whole_metadatas);
            }
           $data['error'] = 'false';
        } catch (Exception $e) {
             $data['error'] = 'true';
        }
        return $data;
    }
    
  

    /**
     * @signature - count_items($xml,$data)
     * @param array $xml O listRecords da biblioteca a ser verifcada
     * @param array $data Os dados vindos do formulario
     * @return string O total de items presentes na colecao
     * @description - O total de items presentes na colecao
     * @author: Eduardo 
     */
    public function count_items($xml, $data) {
        if($xml->ListRecords){
            if ($xml->ListRecords->resumptionToken && null !== $xml->ListRecords->resumptionToken->attributes()) {
                $resumptionToken_attributes = $xml->ListRecords->resumptionToken->attributes();
                foreach ($resumptionToken_attributes as $tag => $attribute) {
                    if ($tag == 'completeListSize') {
                        return (string) $attribute;
                    }
                }
            }else{
                return count($xml->ListRecords->record);
            }
        }
        if(isset($data['sets'])&&!empty($data['sets'])){
            $sets = explode(',',$data['sets']);
            $url = $data['url'] . "?verb=ListIdentifiers&metadataPrefix=oai_dc";
            foreach ($sets as $set) {
                $url .= '&set='.$set;
            }
        }else{
           $url = $data['url'] . "?verb=ListIdentifiers&metadataPrefix=oai_dc"; // pego os 100 primeiros
         }
        $response_xml_data = download_page($url);
        $data = simplexml_load_string($response_xml_data);
        $json = json_encode($data);
        $array = json_decode($json, TRUE);
        return (string) count($array['ListIdentifiers']['header']);
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
     * @signature - generate_selects($metadata)
     * @param array $data Os dados vindos do formulario
     * @return string O identifier do item a ser utilizado no form para o mapeiamento
     * @description - funcao que retorna todos os metadatas para realizar o mapeiamento das propriedades do repositorio escolhido
     * @author: Eduardo 
     */
    public function generate_selects($data) {
        $html = '';
        $propertyModel = new PropertyModel;
        //$facets_id = CollectionModel::get_facets($data['collection_id']);
        $html .= "<option value=''>" . __('Select...','tainacan') . "</option>";
        $html .= "<option value='post_title'>" . __('Item Title','tainacan') . "</option>";
        $html .= "<option value='post_content'>" . __('Item Description','tainacan') . "</option>";
        $html .= "<option value='socialdb_object_content'>" . __('Item Content','tainacan') . "</option>";
        $html .= "<option value='post_permalink'>" . __('Item URL','tainacan') . "</option>";
        $html .= "<option value='socialdb_object_dc_type'>" . __('Item Type','tainacan') . "</option>";
        //$html .= "<option value='socialdb_object_from'>" . __('Item Format') . "</option>";
        $html .= "<option value='socialdb_object_dc_source'>" . __('Item Source','tainacan') . "</option>";
       // $html .= "<option value='hierarchy'>" . __('Hierarchy') . "</option>";
       // if ($facets_id) {
          //  foreach ($facets_id as $facet_id) {
           //     $term_facet = get_term_by("id", $facet_id, "socialdb_category_type");
          //      $html .= "<option value='facet_" . $term_facet->term_id . "'>Faceta " . $term_facet->name . "</option>";
           // }
        //}
        $root_category = $this->get_category_root_of($data['collection_id']);
        //$all_properties_id = get_term_meta($root_category, 'socialdb_category_property_id');
        $all_properties_id = $this->get_parent_properties($root_category, [],$root_category); 
        //busco as propriedades sem domain
        $properties_with_no_domain = $this->list_properties_by_collection($data['collection_id']);
        if($properties_with_no_domain&&is_array($properties_with_no_domain)){
            foreach ($properties_with_no_domain as $property_with_no_domain) {
                if(!in_array($property_with_no_domain->term_id, $all_properties_id)){
                    $all_properties_id[] = $property_with_no_domain->term_id;
                }
            }
        }
        if ($all_properties_id) {
            foreach ($all_properties_id as $property_id) {
                $parent = '';
                $property = get_term_by("id", $property_id, "socialdb_property_type");
                $property_root = get_term_meta($property->term_id, 'socialdb_property_created_category', true);
                $term = socialdb_term_exists($property_root);
                if($term){
                    $parent = ' - '.$term['name'];
                }else{
                     $parent = ' - '.__('Removed category','tainacan');
                }
                if(in_array($property->slug, $this->fixed_slugs) ):
                    continue;
                endif;
                $type = $propertyModel->get_property_type($property_id); // pego o tipo da propriedade
                if ($type == 'socialdb_property_object') {
                    $html .= "<option value='objectproperty_" . $property_id . "'>" . $property->name . ' (' . __('Object Property','tainacan') . ')' .$parent. "</option>";
                } elseif ($type == 'socialdb_property_data') {
                    $html .= "<option value='dataproperty_" . $property_id . "'>" . $property->name . ' (' . __('Data Property','tainacan') . ')' .$parent. "</option>";
                } elseif($type == 'socialdb_property_term'){
                       $html .= "<option value='termproperty_" . $property_id . "'>" . $property->name . ' (' . __('Term Property','tainacan') . ')' .$parent. "</option>";
                }
            }
        }
        $html .= "<option value='tag'>" . __('Tag','tainacan') . "</option>";
        $html .= "<option value='attach'>" . __('Attachments','tainacan') . "</option>";
        return $html;
    }

    /**
     *
     * Funcao que retorna o xml com os listsets de um repositorio
     *
     * @param string $url_base A url base do repositorio a ser importado
     * @return array com o id do termo criado.
     */
    public function read_list_set($url_base) {
        try {
            $response_xml_data = file_get_contents($url_base . '?verb=ListSets'); // pego o xml 
            $xml = new SimpleXMLElement($response_xml_data);
            return $xml;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     *
     * Funcao que verifica se o list set nao esta vazio
     *
     * @param array $sets O array de listset do xml
     * @return boolean TRUE se existir e FALSE para caso estiver vazio
     */
    public function is_listset($sets) {
        if (!isset($sets) || !$sets || empty($sets)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     * Funcao que converte o xml DOS LISTSET para tipo array php
     *
     * @param xml $xml O xml a ser convertido
     * @return array com o slug do spec com indice e seu nome como valor
     */
    public function parse_xml_set_to_array($xml) {
        $array_sets = [];
        $sets = $xml->ListSets;
        if ($this->is_listset($sets)) {
            foreach ($sets->set as $set) {
                $array_sets[(string) $set->setSpec] = (string) $set->setName;
            }
            return $array_sets;
        } else {
            return false;
        }
    }

    /**
     *
     * Metodo que percorre todos os listsets para realizar a criacao de todos os listsets
     *
     * @param array $array_list_set O array com os listspecs com o indice como slug e o name como o valor
     * @param int $collection_id O id da colecao 
     * @return void
     */
    public function save_list_set($array_list_set, $collection_id) {
        session_write_close();
        ini_set('max_execution_time', '0');
        foreach ($array_list_set as $set_slug => $set_name) {
            $result = $this->create_term_set($set_slug . '_' . $collection_id, $set_name, $collection_id);
            if (isset($result['already_exists'])) {
                break;
            }
        }
    }

    /**
     *
     * Metodo que cria o termo SET 
     *
     * @param string $set_slug O slug do set a ser criado
     * @param string $set_name O name do set a ser criado
     * @param int $collection_id O id da colecao
     * @return array Com ID do termo criado
     */
    public function create_term_set($set_slug, $set_name, $collection_id) {
        $root_category = $this->get_category_root_of($collection_id);
        return socialdb_insert_term($set_name, 'socialdb_category_type', $root_category, $set_slug);
    }

    /**
     *
     * Metodo que importa todos os SETSPECS para o banco de dados
     *
     * @param string $url_base A url base do repositorio de onde serao importadas os set specs
     * @param int $collection_id O id da colecao a qual sera vinculada
     * @return void
     */
    public function import_list_set($url_base, $collection_id,$sets) {
        session_write_close();
        ini_set('max_execution_time', '0');
        if ($this->get_category_root_of($collection_id)) {
            $xml_list_set = $this->read_list_set($url_base);
            if ($xml_list_set) {
                $array_list_set = $this->parse_xml_set_to_array($xml_list_set);
                if(is_string($sets)&&trim($sets)!=''){
                    $sets_selected = explode(',',$sets);
                    foreach ($array_list_set as $index => $value) {
                        if(!in_array($index, $sets_selected)){
                            unset($array_list_set[$index]);
                        }
                    }
                }
                $this->save_list_set($array_list_set, $collection_id);
            }
        }
    }

    /**
     *
     * Return an array of term ids from specs received of the object in the parameter
     *
     * @param string $object_specs O objeto com todos os specs para serem iterados
     * @param int $collection_id O id da colecao a qual sera vinculada
     * @return array Os IDs dos specs que estao no banco de dados
     */
    public function get_set_specs($object_specs, $collection_id) {
        $array_categories = [];
        $category_model = new CategoryModel;
        foreach ($object_specs as $object_spec) {
            if($collection_id == get_option('collection_root_id')){
                $category_spec = $category_model->get_term_by_slug((string) $object_spec);
            }else{
               $category_spec = $category_model->get_term_by_slug((string) $object_spec . '_' . $collection_id);  
            }
           
            if ($category_spec) {
                $array_categories[] = $category_spec[0]->term_id;
            }
        }
        return $array_categories;
    }

    /**
     *
     * Return an array of term ids from specs received of the object in the parameter
     *
     * @param string $object_specs O objeto com todos os specs para serem iterados
     * @param int $collection_id O id da colecao a qual sera vinculada
     * @return array Os IDs dos specs que estao no banco de dados
     */
    public function do_import($data) {
        $json_response = array();
        $record_response = array();
        if(isset($data['update_repository'])) {
            if (!$data['first'] || $data['first'] == 'false') {
                $response_xml_data = download_page($data['url'] . '?verb=ListRecords&resumptionToken=' . $data['token']); // pego o xml 
            } else {
                $response_xml_data = download_page($data['url'] . '?verb=ListRecords&metadataPrefix=oai_dc'); // pego o xml 
            }
        } else {
            if (!$data['first'] || $data['first'] == 'false') {
                $response_xml_data = download_page($data['url'] . '?verb=ListRecords&resumptionToken=' . $data['token']); // pego o xml 
            } else {
                if (isset($data['sets']) && !empty($data['sets'])) {
                    $sets = explode(',', $data['sets']);
                    $url = $data['url'] . "?verb=ListRecords&metadataPrefix=oai_dc";
                    foreach ($sets as $set) {
                        $url .= '&set=' . $set;
                    }
                } else {
                    $url = $data['url'] . "?verb=ListRecords&metadataPrefix=oai_dc"; // pego os 100 primeiros
                }
                $response_xml_data = download_page($url); // pego o xml 
            }
        }
        try{
            $xml = new SimpleXMLElement($response_xml_data);
            if (isset($xml->error) && (string) $xml->error == 'The requested resumptionToken is invalid or has expired' && $data['lastpage'] <= 1) {
                $response_xml_data = download_page($data['url'] . '?verb=ListRecords&metadataPrefix=oai_dc'); // pego o xml 
                $xml = new SimpleXMLElement($response_xml_data);
            }
            $json_response['token'] = (string) $xml->ListRecords->resumptionToken; // pego o token da proxima list records
            $tam = count($xml->ListRecords->record); // verifico o tamanho dos list record para o for
            for ($j = 0; $j < $tam; $j++) {
                $record = $record = $xml->ListRecords->record[$j];
                $dc = $record->metadata->children("http://www.openarchives.org/OAI/2.0/oai_dc/");
                if ($record->metadata->Count() > 0 ) {
                    if(!$record->header->setSpec && isset($data['sets']) && !empty($data['sets'])){
                         $json_response['token'] = '';
                        continue;
                    }
                    $metadata = $dc->children('http://purl.org/dc/elements/1.1/');
                    $record_response['identifier'] = (string)$record->header->identifier;
                    $record_response['datestamp'] = (string)$record->header->datestamp;
                    $record_response['list_sets'] = $this->get_set_specs($record->header->setSpec, $data['collection_id']);
                    $record_response['title'] = $metadata->title;
                    $record_response['date'] = $record->header->datestamp;
                    $tam_metadata = count($metadata);
                    for ($i = 0; $i < $tam_metadata; $i++) {
                        $value = (string) $metadata[$i];
                        $identifier = $this->get_identifier($metadata[$i]);
                        $record_response['metadata'][$identifier][] = $value;
                    }
                    if($record->files){
                        foreach ($record->files->url as $url):
                             $record_response['files'][] = (string)$url;
                        endforeach;
                    }
                    $json_response['records'][] = $record_response;
                }
                $record_response['files'] = [];
                $record_response = [];
            }
            Log::addLog(['collection_id' => $data['collection_id'],'event_type' => 'import', 'event' => 'access_oai_pmh']);
        }  catch (Exception $e){ }
        return $json_response;
    }
    

    /**
     *
     * Metodo que spercorre os dados para enviar para a funcao que realiza o salvamento dos dados
     *
     * @param array O com os dados que serao salvos na base de dados
     * @return void
     */
    public function saving_data($data) {
        $propertyModel = new PropertyModel;
        //parse_str($data['form'], $form); // parseio o formulario de mapeiamento de entidades
        $form = $this->get_mapping_oaipmh_dc($data['mapping_id']);
        update_post_meta($data['collection_id'], 'socialdb_collection_mapping_exportation_active', $data['mapping_id']);
        //$array_property = json_decode($propertyModel->create_property_data(__('Size'), $data['collection_id'])); // crio a propriedade Size
        foreach ($data['all_data'] as $inserts) {
            foreach ($inserts['records'] as $record) {
                if ($record) {
                    $this->saving_metadata($record, $form, $data['collection_id'],$data['mapping_id']);
                }
            }
        }
    }

    /**
     *
     * Metodo que salva os dados vindos do repostiorio importado
     *
     * @param array $record O com os dados que serao salvos na base de dados
     * @param array $form O com os dados que serao salvos na base de dados
     * @param int $collection_id O com os dados que serao salvos na base de dados
     * @param int $mapping_id O com os dados que serao salvos na base de dados
     * @return void
     */
    function saving_metadata($record, $form, $collection_id,$mapping_id) {
        $categories = $record['list_sets'];
        $categories[] = $this->get_category_root_of($collection_id);
        $content = '';
        $object_id = socialdb_insert_object($record['title'], $record['date']);
        //mapping
        add_post_meta($object_id, 'socialdb_channel_id',$mapping_id);
        update_post_meta($object_id, 'socialdb_object_dc_type', 'other');  
        $this->set_common_field_values($object_id, 'object_type', 'other');
        //
        if ($object_id != 0) {
            foreach ($record['metadata'] as $identifier => $metadata) {
                if ($form[$identifier] !== '') {
                    if ($form[$identifier] == 'post_title'):
                        $this->update_title($object_id, implode(',', $metadata));
                        $this->set_common_field_values($object_id, 'title', implode(',', $metadata));
                     elseif ($form[$identifier] == 'post_content'):
                        $content .=  implode(',', $metadata) . ",";
                    elseif ($form[$identifier] == 'post_permalink'):
                        update_post_meta($object_id, 'socialdb_object_dc_source', implode(',', $metadata));
                        $this->set_common_field_values($object_id, 'object_source', implode(',', $metadata));
                    elseif ($form[$identifier] == 'socialdb_object_content'):
                        update_post_meta($object_id, 'socialdb_object_content', implode(',', $metadata));
                        $this->set_common_field_values($object_id, 'object_content', implode(',', $metadata));
                   elseif ($form[$identifier] == 'socialdb_object_dc_type'):
                        $metadata = implode(',', $metadata);
                        if(in_array($metadata, ['pdf','text','video','image','audio'])){
                            update_post_meta($object_id, 'socialdb_object_dc_type', $metadata);  
                        }
                        $this->set_common_field_values($object_id, 'object_type', implode(',', $metadata));
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
                            //$this->insert_tag($meta,$object_id,$collection_id);
                           }
                        }
                    //elseif (strpos($form[$identifier], "facet_")!==false):
                    elseif (strpos($form[$identifier], "termproperty_")!==false):
                        $trans = array("termproperty_" => "");
                        $property_id = strtr($form[$identifier], $trans);
                        $parent = get_term_meta($property_id, 'socialdb_property_term_root', true);
                        foreach ($metadata as $meta) {
                            if(trim($meta)!==''){
                                $this->insert_hierarchy($meta,$object_id,$collection_id,$parent,$property_id);
                            }
                        }
                        // elseif(strpos($form[$identifier], "hierarchy")!==false):    
                        //  $this->insert_hierarchy($metadata,$object_id,$collection_id);
                    elseif (strpos($form[$identifier], "dataproperty_")!==false):
                        $trans = array("dataproperty_" => "");
                        $id = strtr($form[$identifier], $trans);
                        foreach ($metadata as $meta) {
                             add_post_meta($object_id, 'socialdb_property_'.$id.'',$meta);
                             $this->set_common_field_values($object_id, "socialdb_property_$id",$meta);
                        }
                    endif;
                }
                if($form['import_object']=='true'&&$identifier=='identifier'){
                     foreach ($metadata as $meta) {
                         if(filter_var($meta, FILTER_VALIDATE_URL)&&  strpos($meta, 'handle')!==false){
                            $tags = $this->extract_metatags($meta);
                            if(is_array($tags)){
                                for($i = 0;$i < count($tags); $i++ ){
                                    if($tags[$i]['name_field'] === 'citation_pdf_url'){
                                      $has_uploaded = true;
                                       $attachment_id = $this->add_file_url($tags[$i]['value'], $object_id);
                                       update_post_meta($object_id, 'socialdb_object_content', $attachment_id);
                                       update_post_meta($object_id, 'socialdb_object_dc_type', 'pdf');
                                       update_post_meta($object_id, 'socialdb_object_from', 'internal');
                                    }
                                }
                            }
                        }
                     }
                }
            }
            $metadata = '';
            //files
            if($record['identifier']){
                add_post_meta($object_id, 'socialdb_object_identifier',$record['identifier']);  
            }
            if($record['datestamp']){
                 add_post_meta($object_id, 'socialdb_object_datestamp',$record['datestamp']);
            }
            if(!isset($has_uploaded))
                update_post_meta($object_id, 'socialdb_object_from', 'external');
            $this->set_common_field_values($object_id, 'object_from', 'external');
            add_post_meta($object_id, 'socialdb_object_original_collection',$collection_id);
            update_post_content($object_id, $content);
            $this->set_common_field_values($object_id, 'description', $content);
            socialdb_add_tax_terms($object_id, $categories, 'socialdb_category_type');
        }
    }
    /**
     *
     * Metodo que busca o mapeamento para realizar a insercao dos itens
     *
     * @param int O id do mapeamento
     * @param string O titutlo desejado para o objeto
     * @return void
     */
    public function get_mapping_oaipmh_dc($mapping_id) {
         $data = [];
         $mappings = unserialize(get_post_meta($mapping_id,'socialdb_channel_oaipmhdc_mapping',true));
         foreach ($mappings as $mapping) {
             if(isset($mapping['attribute_value'])){
                 $index = $mapping['tag'].'_'.$mapping['attribute_value'];
             }else{
                 $index = $mapping['tag'];
             }
             $data[$index] = $mapping['socialdb_entity'];
         }
         $data['import_object'] = get_post_meta($mapping_id, 'socialdb_channel_oaipmhdc_import_object', true);
         return $data;
    }
    /**
     *
     * Metodo que atualiza o titulo de um objeto
     *
     * @param int O id do objeto
     * @param string O titutlo desejado para o objeto
     * @return void
     */
    public function update_title($ID, $title) {
        $object = array(
            'ID' => $ID,
            'post_title' => $title
        );
        wp_update_post($object);
    }
    /**
     *
     * Metodo que cria e vincula uma tag com um objeto
     *
     * @param string
     * @param string O titutlo desejado para o objeto
     * @return void
     */
    public function insert_tag($name,$object_id,$collection_id) {
        $parent = get_term_by('name','socialdb_tag','socialdb_tag_type');
        $array = socialdb_insert_term($name, 'socialdb_tag_type', $parent->term_id, sanitize_title(remove_accent($name)) . "_" . $collection_id);
        $this->concatenate_commom_field_value( $object_id, "socialdb_propertyterm_tag", $array['term_id']);
        socialdb_add_tax_terms($collection_id, array($array['term_id']), 'socialdb_tag_type');
        socialdb_add_tax_terms($object_id, array($array['term_id']), 'socialdb_tag_type');
    }
    
    public function insert_category($name,$collection_id,$parent_id) {
       $array = socialdb_insert_term($name, 'socialdb_category_type', $parent_id, sanitize_title(remove_accent($name)).'_'.  mktime());
       return $array;
    }
    
    public function insert_hierarchy($metadata,$object_id,$collection_id,$parent = 0,$property_id = null) {
        $array = array();
        $categories = explode('::', $metadata);
        foreach ($categories as $category) {
            $array = $this->insert_category($category,$collection_id,$parent);
            $this->concatenate_commom_field_value( $object_id, "socialdb_propertyterm_$property_id", $array['term_id']);
            $parent = $array['term_id'];
        }
        socialdb_add_tax_terms($object_id, array($array['term_id']), 'socialdb_category_type');
    }

}
