<?php

/**
 * Author: Eduardo HUmberto
 */
if(is_file(dirname(__FILE__) .'../../../../../wp-config.php')){
include_once (dirname(__FILE__) .'../../../../../wp-config.php');
include_once (dirname(__FILE__) .'../../../../../wp-load.php');
include_once (dirname(__FILE__) .'../../../../../wp-includes/wp-db.php');
}else{
    include_once (dirname(__FILE__) .'../../../../../../wp-config.php');
    include_once (dirname(__FILE__) .'../../../../../../wp-load.php');
    include_once (dirname(__FILE__) .'../../../../../../wp-includes/wp-db.php');
}
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../property/property_model.php');
require_once(dirname(__FILE__) . '../../category/category_model.php');

class HarvestingOAIPMHModel extends Model {

    /**
     *
     * Return an array of term ids from specs received of the object in the parameter
     *
     * @param string $object_specs O objeto com todos os specs para serem iterados
     * @param int $collection_id O id da colecao a qual sera vinculada
     * @return array Os IDs dos specs que estao no banco de dados
     */
    public function do_harvesting($data) {
        $json_response = array();
        $record_response = array();
        try{
            if (isset($data['sets']) && !empty($data['sets'])) {
                $sets = explode(',', $data['sets']);
                $url = $data['url'] . "?verb=ListRecords&metadataPrefix=oai_dc&from=" . $data['from'].'&until='.$data['until'];
                foreach ($sets as $set) {
                    $url .= '&set=' . $set;
                }
            } else {
                $url = $data['url'] . "?verb=ListRecords&metadataPrefix=oai_dc&from=" . $data['from'].'&until='.$data['until']; // pego os 100 primeiros
            }
            $response_xml_data = download_page($url); // pego o xml 
            //error_log( print_r(' URL  = '.$url, true ),0);
            //error_log( print_r($response_xml_data, true ),0);  
            $xml = new SimpleXMLElement($response_xml_data);
            if (isset($xml->error) && (string) $xml->error == 'The requested resumptionToken is invalid or has expired' && $data['lastpage'] <= 1) {
                $response_xml_data = download_page($data['url'] . '?verb=ListRecords&metadataPrefix=oai_dc'); // pego o xml 
                $xml = new SimpleXMLElement($response_xml_data);
            }
            $json_response['token'] = (string) $xml->ListRecords->resumptionToken; // pego o token da proxima list records
            $tam = count($xml->ListRecords->record); // verifico o tamanho dos list record para o for
            
                  //  error_log( print_r(' TAM = '.$tam, true ),0);
            for ($j = 0; $j < $tam; $j++) {
                $record = $record = $xml->ListRecords->record[$j];
                $dc = $record->metadata->children("http://www.openarchives.org/OAI/2.0/oai_dc/");
                if ($record->metadata->Count() > 0) {
                    if (!$record->header->setSpec && isset($data['sets']) && !empty($data['sets'])) {
                        $json_response['token'] = '';
                        continue;
                    }
                    $metadata = $dc->children('http://purl.org/dc/elements/1.1/');
                    //  error_log( print_r($metadata, true ),0);
                    $record_response['identifier'] = (string) $record->header->identifier;
                    $record_response['datestamp'] = (string) $record->header->datestamp;
                    $record_response['list_sets'] = $this->get_set_specs($record->header->setSpec, $data['collection_id']);
                    $record_response['title'] = $metadata->title;
                    $record_response['date'] = $record->header->datestamp;
                    $tam_metadata = count($metadata);
                    for ($i = 0; $i < $tam_metadata; $i++) {
                        $value = (string) $metadata[$i];
                        $identifier = $this->get_identifier($metadata[$i]);
                        $record_response['metadata'][$identifier] = $value;
                    }
                    if ($record->files) {
                        foreach ($record->files->url as $url):
                            $record_response['files'][] = (string) $url;
                        endforeach;
                    }
                    $json_response['records'][] = $record_response;
                }
                $record_response['files'] = [];
            }
            $json_response['count_records'] = $tam;
        }catch(Exception $e){
            $json_response['token'] = '';
        }
        return $json_response;
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
            $category_spec = $category_model->get_term_by_slug((string) $object_spec . '_' . $collection_id);
            if ($category_spec) {
                $array_categories[] = $category_spec[0]->term_id;
            }
        }
        return $array_categories;
    }

    /**
     *
     * Metodo que spercorre os dados para enviar para a funcao que realiza o salvamento dos dados
     *
     * @param array O com os dados que serao salvos na base de dados
     * @return void
     */
    public function saving_data($data) {
        //parse_str($data['form'], $form); // parseio o formulario de mapeiamento de entidades
        $form = $this->get_mapping_oaipmh_dc($data['mapping_id']);
        //$array_property = json_decode($propertyModel->create_property_data(__('Size'), $data['collection_id'])); // crio a propriedade Size
        
        foreach ($data['all_data'] as $inserts) {
            foreach ($inserts['records'] as $record) {
                if ($record) {
                    $this->saving_metadata($record, $form, $data['collection_id'], $data['mapping_id']);
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
    function saving_metadata($record, $form, $collection_id, $mapping_id) {
        $categories = $record['list_sets'];
        $categories[] = $this->get_category_root_of($collection_id);
        $content = '';
        $verify_object = $this->verify_object($record['identifier']);
        if (!$verify_object):
            $object_id = socialdb_insert_object($record['title'], $record['date']);
            add_post_meta($object_id, 'socialdb_channel_id', $mapping_id);
            if($record['identifier']):
                 add_post_meta($object_id, 'socialdb_object_identifier',$record['identifier']);
             endif;
            if($record['datestamp']):
                 add_post_meta($object_id, 'socialdb_object_datestamp',$record['datestamp']);
            endif;
        else:
            $object_id = $verify_object;
            $original_collection = get_post_meta($object_id, 'socialdb_object_original_collection', true);
            if($original_collection&&$original_collection!=$collection_id){
                $object_id = socialdb_insert_object($record['title'], $record['date']);
                add_post_meta($object_id, 'socialdb_channel_id', $mapping_id);
                if($record['identifier']):
                     add_post_meta($object_id, 'socialdb_object_identifier',$record['identifier']);
                 endif;
                if($record['datestamp']):
                     add_post_meta($object_id, 'socialdb_object_datestamp',$record['datestamp']);
                endif;
            }
        endif;
        //mapping
        if ($object_id != 0) {
            foreach ($record['metadata'] as $identifier => $metadata) {
                if ($form[$identifier] !== '') {
                    if ($form[$identifier] == 'post_title'):
                        $this->update_title($object_id, $metadata);
                    elseif ($form[$identifier] == 'post_content'):
                        $content .= $metadata . ",";
                    elseif ($form[$identifier] == 'post_permalink'):
                        update_post_meta($object_id, 'socialdb_object_dc_source', $metadata);
                    elseif ($form[$identifier] == 'socialdb_object_content'):
                        update_post_meta($object_id, 'socialdb_object_content', implode(',', $metadata));
                    elseif ($form[$identifier] == 'socialdb_object_dc_type'):
                        update_post_meta($object_id, 'socialdb_object_dc_type', implode(',', $metadata));
                    elseif ($form[$identifier] == 'tag'):
                        $this->insert_tag($metadata, $object_id, $collection_id);
                     //elseif (strpos($form[$identifier], "facet_")!==false):
                    elseif (strpos($form[$identifier], "termproperty_")!==false):
                        $trans = array("termproperty_" => "");
                        $property_id = strtr($form[$identifier], $trans);
                        $parent = get_term_meta($property_id, 'socialdb_property_term_root', true);
                        foreach ($metadata as $meta) {
                            if(trim($meta)!==''){
                                $this->insert_hierarchy($meta,$object_id,$collection_id,$parent);
                            }
                        }
                    // elseif(strpos($form[$identifier], "hierarchy")!==false):    
                    //  $this->insert_hierarchy($metadata,$object_id,$collection_id);
                    elseif (strpos($form[$identifier], "dataproperty_") !== false):
                        $trans = array("dataproperty_" => "");
                        $id = strtr($form[$identifier], $trans);
                        add_post_meta($object_id, 'socialdb_property_' . $id . '', $metadata);
                    endif;
                }
            }
            //files
            if ($form['import_object'] == 'true' && isset($record['files'])):
                foreach ($record['files'] as $file) {
                    $this->add_file_url($file, $object_id);
                } elseif ($form['import_object'] == 'false' && isset($record['files'])):
                foreach ($record['files'] as $file) {
                    add_post_meta($object_id, 'socialdb_files_url', $file);
                }
            endif;
            update_post_meta($object_id, 'socialdb_object_from', 'external');
            update_post_content($object_id, $content);
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
        $mappings = unserialize(get_post_meta($mapping_id, 'socialdb_channel_oaipmhdc_mapping', true));
        foreach ($mappings as $mapping) {
            if (isset($mapping['attribute_value'])) {
                $index = $mapping['tag'] . '_' . $mapping['attribute_value'];
            } else {
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
    public function insert_tag($name, $object_id, $collection_id) {
        $parent = get_term_by('name', 'socialdb_tag', 'socialdb_tag_type');
        $array = socialdb_insert_term($name, 'socialdb_tag_type', $parent->term_id, sanitize_title(remove_accent($name)) . "_" . $collection_id);
        socialdb_add_tax_terms($collection_id, array($array['term_id']), 'socialdb_tag_type');
        socialdb_add_tax_terms($object_id, array($array['term_id']), 'socialdb_tag_type');
    }

    public function insert_category($name, $collection_id, $parent_id) {
        $array = socialdb_insert_term($name, 'socialdb_category_type', $parent_id, sanitize_title(remove_accent($name)).'_'.  mktime());
       // $array = socialdb_insert_term($name, 'socialdb_category_type', $parent_id, sanitize_title(remove_accent($name)) . "_" . $collection_id);
        return $array;
    }

    public function insert_hierarchy($metadata, $object_id, $collection_id, $parent = 0) {
        $array = array();
        $categories = explode('::', $metadata);
        foreach ($categories as $category) {
            $array = $this->insert_category($category, $collection_id, $parent);
            $parent = $array['term_id'];
        }
        socialdb_add_tax_terms($object_id, array($array['term_id']), 'socialdb_category_type');
    }

    /**
     * function get_collection_by_category_root($user_id)
     * @param int a categoria raiz de uma colecao
     * @return array(wp_post) a colecao de onde pertence a categoria root
     * @ metodo responsavel em retornar as colecoes de um determinando usuario
     * @author: Eduardo Humberto 
     */
    public function verify_object($identifier) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "
                    SELECT p.* FROM $wp_posts p
                    INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id    
                    WHERE pm.meta_key LIKE 'socialdb_collection_object_type' and pm.meta_value like '$identifier'
            ";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result) && count($result) > 0) {
            return $result[0]->ID;
        } else {
            return FALSE;
        }
    }

    public function execute($data) {
        $token = 'init';
        while ($token != '') {
            $results = $this->do_harvesting($data);
            $data['all_data'][] = $results;
            $token = $results['token'];
            $tam = $results['count_records'];
        }
        if ($tam > 0) {
            $this->saving_data($data);
        }
    }

}
