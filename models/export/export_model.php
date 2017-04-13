<?php

/**
 * Author: Eduardo Humberto
 */
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../property/property_model.php');
require_once(dirname(__FILE__) . '../../category/category_model.php');
require_once(dirname(__FILE__) . '../../object/object_model.php');
require_once(dirname(__FILE__) . '../../collection/collection_model.php');

class ExportModel extends Model {

    /**
     * @signature - generate_selects($metadata)
     * @param array $data Os dados vindos do formulario
     * @return string O identifier do item a ser utilizado no form para o mapeiamento
     * @description - funcao que retorna todos os metadatas para realizar o mapeiamento das propriedades do repositorio escolhido
     * @author: Eduardo 
     */
    public function create_new_mapping($data) {
        $field = array();
        $propertyModel = new PropertyModel;
        $facets_id = CollectionModel::get_facets($data['collection_id']);
        $field['socialdb_entity'] = 'post_title';
        $field['name_socialdb_entity'] = __('Item Title', 'tainacan');
        $data['fields'][] = $field;
        $field['socialdb_entity'] = 'post_content';
        $field['name_socialdb_entity'] = __('Item Description', 'tainacan');
        $data['fields'][] = $field;
        $field['socialdb_entity'] = 'post_permalink';
        $field['name_socialdb_entity'] = __('Item URL', 'tainacan');
        $data['fields'][] = $field;
        // new fields
        $field['socialdb_entity'] = 'socialdb_object_from';
        $field['name_socialdb_entity'] = __('Item Format', 'tainacan');
        $data['fields'][] = $field;
        $field['socialdb_entity'] = 'socialdb_object_dc_source';
        $field['name_socialdb_entity'] = __('Item Source', 'tainacan');
        $data['fields'][] = $field;
        $field['socialdb_entity'] = 'socialdb_object_content';
        $field['name_socialdb_entity'] = __('Item Content', 'tainacan');
        $data['fields'][] = $field;
        $field['socialdb_entity'] = 'socialdb_object_dc_type';
        $field['name_socialdb_entity'] = __('Item Type', 'tainacan');
        $data['fields'][] = $field;
        //if ($facets_id) {
        // foreach ($facets_id as $facet_id) {
        //    $term_facet = get_term_by("id", $facet_id, "socialdb_category_type");
        //   $field['socialdb_entity'] = 'facet_' . $term_facet->term_id;
        //  $field['name_socialdb_entity'] = __('Facet') . ' ' . $term_facet->name;
        //  $data['fields'][] = $field;
        //}
        //}
        $root_category = $this->get_category_root_of($data['collection_id']);
        //$all_properties_id = get_term_meta($root_category, 'socialdb_category_property_id');
        $all_properties_id = array_unique($this->get_parent_properties($root_category, [], $root_category));
        if ($all_properties_id) {
            foreach ($all_properties_id as $property_id) {
                $property = get_term_by("id", $property_id, "socialdb_property_type");
                if (in_array($property->slug, $this->fixed_slugs)):
                    continue;
                endif;
                $type = $propertyModel->get_property_type($property_id); // pego o tipo da propriedade
                if ($type == 'socialdb_property_object') {
                    $field['socialdb_entity'] = 'objectproperty_' . $property_id;
                    $field['name_socialdb_entity'] = $property->name . ' (' . __('Object Property', 'tainacan') . ')';
                    $data['fields'][] = $field;
                } elseif ($type == 'socialdb_property_data') {
                    $field['socialdb_entity'] = 'dataproperty_' . $property_id;
                    $field['name_socialdb_entity'] = $property->name . ' (' . __('Data Property', 'tainacan') . ')';
                    $data['fields'][] = $field;
                } elseif ($type == 'socialdb_property_term') {
                    $field['socialdb_entity'] = 'termproperty_' . $property_id;
                    $field['name_socialdb_entity'] = $property->name . ' (' . __('Term Property', 'tainacan') . ')';
                    $data['fields'][] = $field;
                }
            }
        }
        $field['socialdb_entity'] = 'tag';
        $field['name_socialdb_entity'] = __('Tags', 'tainacan');
        $data['fields'][] = $field;
        return $data;
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
                return $metadata->getName() . '_' . (string) $b;
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
        $html = '<option value="">' . __('Select', 'tainacan') . '</option>';
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
        if ($tags_dc) {
            foreach ($tags_dc as $tag_dc) {
                $html .= "<option value='$tag_dc'>$tag_dc</option>";
            }
        }
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
    public function import_list_set($url_base, $collection_id) {
        session_write_close();
        ini_set('max_execution_time', '0');
        if ($this->get_category_root_of($collection_id)) {
            $xml_list_set = $this->read_list_set($url_base);
            if ($xml_list_set) {
                $array_list_set = $this->parse_xml_set_to_array($xml_list_set);
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
            $category_spec = $category_model->get_term_by_slug((string) $object_spec . '_' . $collection_id);
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
        if (isset($data['update_repository'])) {
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
            if ($record->metadata->Count() > 0) {
                $metadata = $dc->children('http://purl.org/dc/elements/1.1/');
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
        $object_id = socialdb_insert_object($record['title'], $record['date']);
        //mapping
        add_post_meta($object_id, 'socialdb_channel_id', $mapping_id);
        //
        if ($object_id != 0) {
            foreach ($record['metadata'] as $identifier => $metadata) {
                if ($form[$identifier] !== '') {
                    if ($form[$identifier] == 'post_title'):
                        $this->update_title($object_id, $metadata);
                    elseif ($form[$identifier] == 'post_content'):
                        $content .= $metadata . ",";
                    elseif ($form[$identifier] == 'post_permalink'):
                        update_post_meta($object_id, 'socialdb_uri_imported', $metadata);
                    elseif ($form[$identifier] == 'tag'):
                        $this->insert_tag($metadata, $object_id, $collection_id);
                    elseif (strpos($form[$identifier], "facet_") !== false):
                        $trans = array("facet_" => "");
                        $parent = strtr($form[$identifier], $trans);
                        $this->insert_category($metadata, $object_id, $collection_id, $parent);
                    elseif (strpos($form[$identifier], "objectproperty_") !== false):
                        $trans = array("objectproperty_" => "");
                        $id = strtr($form[$identifier], $trans);
                        add_post_meta($object_id, 'socialdb_property_' . $id . '', $metadata);
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

    public function insert_category($name, $object_id, $collection_id, $parent_id) {
        $array = socialdb_insert_term($name, 'socialdb_tag_type', $parent_id, sanitize_title(remove_accent($name)) . "_" . $collection_id);
        socialdb_add_tax_terms($collection_id, array($array['term_id']), 'socialdb_category_type');
        socialdb_add_tax_terms($object_id, array($array['term_id']), 'socialdb_category_type');
    }

    public function generate_csv_data($data) {
        $propertyModel = new PropertyModel;
        $facets_id = CollectionModel::get_facets($data['collection_id']);
        $objects = $this->get_collection_posts($data['collection_id']);
        foreach ($objects as $object) {
            if ($object->ID == $data['collection_id']) {
                continue;
            }
            
            /** ID * */
            if ($object->ID != "") {
                $csv_data['ID'] = $object->ID;
            }
            
            /** Title * */
            if ($object->post_title != "") {
                $value = $object->post_title;
                if(mb_detect_encoding($value)==='UTF-8'){
                    $value = utf8_decode($value);
                }
                $csv_data['title'] = $value;
            } else {
                $csv_data['title'] = '';
            }

            /** Description * */
            if ($object->post_content != "") {
                $value = $object->post_content;
                if(mb_detect_encoding($value)==='UTF-8'){
                    $value = utf8_decode($value);
                }
                $csv_data['description'] = $value;
            } else {
                $csv_data['description'] = '';
            }

            /** Content * */
            if (get_post_meta($object->ID, 'socialdb_object_content', true) != "") {
                $csv_data['content'] = utf8_decode(get_post_meta($object->ID, 'socialdb_object_content', true));
                if ($csv_data['content'] != '' && is_numeric($csv_data['content'])) {
                    $csv_data['content'] = wp_get_attachment_url($csv_data['content']);
                }
            } else {
                $csv_data['content'] = '';
            }

            /** Origin  * */
            if (get_post_meta($object->ID, 'socialdb_object_from')) {
                $csv_data['item_from'] = get_post_meta($object->ID, 'socialdb_object_from', true);
            }

            /** Type  * */
            if (get_post_meta($object->ID, 'socialdb_object_dc_type')) {
                $csv_data['item_type'] = get_post_meta($object->ID, 'socialdb_object_dc_type', true);
            }

            /** Source  * */
            if (get_post_meta($object->ID, 'socialdb_object_dc_source')) {
                $value = get_post_meta($object->ID, 'socialdb_object_dc_source', true);
                if(mb_detect_encoding($value)==='UTF-8'){
                    $value = utf8_decode($value);
                }
                $csv_data['item_source'] = $value;
            }

            /** URL * */
            if (get_post_meta($object->ID, 'socialdb_uri_imported')) {
                $csv_data['permalink'] = get_post_meta($object->ID, 'socialdb_uri_imported', true);
            } else {
                $csv_data['permalink'] = get_the_permalink($data['collection_id']) . '?object_id=' . $object->ID;
            }

            /** Tags * */
            $tags = wp_get_object_terms($object->ID, 'socialdb_tag_type', array('fields' => 'names'));
            if (!empty($tags)) {
                $csv_data['tags'] = utf8_decode(implode('||', $tags));
            } else {
                $csv_data['tags'] = '';
            }

            /** Categories * */
            $categories_of_facet = array();
            $category_model = new CategoryModel;
            $categories = wp_get_object_terms($object->ID, 'socialdb_category_type');
            $facets = CollectionModel::get_facets($data['collection_id']);
            if (is_array($categories)):
                foreach ($categories as $category) {
                    $facet_id = $category_model->get_category_facet_parent($category->term_id, $data['collection_id']);
                    if (!isset($facet_id) || $facet_id == $category->term_id) {
                        continue;
                    }
                    $categories_of_facet[$facet_id][] = $this->get_hierarchy_names($category->term_id, $facet_id);
                }
            endif;

            if ($facets) {
                foreach ($facets as $facet) {
                    $term = get_term_by('id', $facet, 'socialdb_category_type');
                    if (is_array($categories_of_facet[$facet])):
                        $csv_data[utf8_decode($term->name)] = utf8_decode(implode(', ', $categories_of_facet[$facet]));
                    else:
                        $csv_data[utf8_decode($term->name)] = '';
                    endif;
                }
            }

            $categories_of_facet = '';

            /** Propriedades de Atributos * */
            $root_category = $this->get_category_root_of($data['collection_id']);
            //$all_properties_id = get_term_meta($root_category, 'socialdb_category_property_id');
            $all_properties_id = array_unique($this->get_parent_properties($root_category, [], $root_category));
            if ($all_properties_id) {
                foreach ($all_properties_id as $property_id) {
                    $property = get_term_by("id", $property_id, "socialdb_property_type");
                    if (in_array($property->slug, $this->fixed_slugs)):
                        continue;
                    endif;
                    $type = $propertyModel->get_property_type($property_id); // pego o tipo da propriedade
                    if ($type == 'socialdb_property_data') {
                        $value = get_post_meta($object->ID, 'socialdb_property_' . $property_id, true);
                        if(mb_detect_encoding($value)==='UTF-8'){
                            $value = utf8_decode($value);
                        }
                        $csv_data[utf8_decode($property->name)] = get_post_meta($object->ID, 'socialdb_property_' . $property_id, true);
                    } elseif ($type == 'socialdb_property_object') {
                        $property_result_meta_value = get_post_meta($object->ID, 'socialdb_property_' . $property_id);
                        if (is_array($property_result_meta_value) && $property_result_meta_value[0] != '') {
                            foreach ($property_result_meta_value as $property_meta_value) {
                                $array_property_name[] = get_post($property_meta_value)->post_title;
                            }
                            $csv_data[utf8_decode($property->name)] = utf8_decode(implode(', ', $array_property_name));
                        } else {
                            $csv_data[utf8_decode($property->name)] = '';
                        }
                    }
                }
                $array_property_name = [];
            }

            /** Arquivos * */
            $array_files = $this->list_files_to_export($object->ID);
            if ($array_files) {
                $csv_data['Files'] = implode(', ', $array_files);
            } else {
                $csv_data['Files'] = '';
            }


            /**             * ************************** */
            $csv[] = $csv_data;
        }
        return $csv;
    }

    public function generate_csv_data_selected($data) {
        $propertyModel = new PropertyModel;
        $facets_id = CollectionModel::get_facets($data['collection_id']);
        $objects = $data['loop'];
        while ($objects->have_posts()) {
            $objects->the_post();
            $object = get_post(get_the_ID());
            if (get_the_ID() == $data['collection_id']) {
                continue;
            }
            /** Title * */
            if (get_the_title() != "") {
                $csv_data['title'] = utf8_decode(get_the_title());
            } else {
                $csv_data['title'] = '';
            }

            /** Description * */
            if (get_the_content() != "") {
                $description = utf8_decode(get_the_content());
//                if(mb_detect_encoding($description, 'auto')=='UTF-8'){
//                    $description = iconv('ISO-8859-1', 'UTF-8', $description);
//                }
                $csv_data['description'] = $description;
            } else {
                $csv_data['description'] = '';
            }

            /** Content * */
            if (get_post_meta(get_the_ID(), 'socialdb_object_content', true) != "") {
                $content = get_post_meta(get_the_ID(), 'socialdb_object_content', true);
//                if(mb_detect_encoding($content, 'auto')=='UTF-8'){
//                    $content = iconv('ISO-8859-1', 'UTF-8', $content);
//                }
                $csv_data['content'] = utf8_decode($content);
                if ($csv_data['content'] != '' && is_numeric($csv_data['content'])) {
                    $csv_data['content'] = wp_get_attachment_url($csv_data['content']);
                }
            } else {
                $csv_data['content'] = '';
            }

            /** Origin  * */
            if (get_post_meta($object->ID, 'socialdb_object_from')) {
                $csv_data['item_from'] = get_post_meta($object->ID, 'socialdb_object_from', true);
            }

            /** Type  * */
            if (get_post_meta($object->ID, 'socialdb_object_dc_type')) {
                $csv_data['item_type'] = get_post_meta($object->ID, 'socialdb_object_dc_type', true);
            }

            /** Source  * */
            if (get_post_meta($object->ID, 'socialdb_object_dc_source')) {
                $csv_data['item_source'] = utf8_decode(get_post_meta($object->ID, 'socialdb_object_dc_source', true));
            }

            /** URL * */
            if (get_post_meta(get_the_ID(), 'socialdb_uri_imported')) {
                $csv_data['permalink'] = get_post_meta(get_the_ID(), 'socialdb_uri_imported', true);
            } else {
                $csv_data['permalink'] = get_the_permalink($data['collection_id']) . '?item=' . $object->post_name;
            }

            /** Tags * */
            $tags = wp_get_object_terms(get_the_ID(), 'socialdb_tag_type', array('fields' => 'names'));
            if (!empty($tags)) {
                $csv_data['tags'] = utf8_decode(implode('||', $tags));
            } else {
                $csv_data['tags'] = '';
            }

            /** Categories * */
            $categories_of_facet = array();
            $category_model = new CategoryModel;
            $categories = wp_get_object_terms(get_the_ID(), 'socialdb_category_type');
            $facets = CollectionModel::get_facets($data['collection_id']);
            /*  if (is_array($categories)):
              foreach ($categories as $category) {
              $facet_id = $category_model->get_category_facet_parent($category->term_id, $data['collection_id']);
              if (!isset($facet_id) || $facet_id == $category->term_id) {
              continue;
              }
              $categories_of_facet[$facet_id][] = $this->get_hierarchy_names($category->term_id, $facet_id);
              }
              endif;

              if ($facets) {
              foreach ($facets as $facet) {
              $term = get_term_by('id', $facet, 'socialdb_category_type');
              if (is_array($categories_of_facet[$facet])):
              $csv_data[$term->name] = implode(', ', $categories_of_facet[$facet]);
              else:
              $csv_data[$term->name] = '';
              endif;
              }
              } */

            $categories_of_facet = [];

            /** Propriedades de Atributos * */
            $root_category = $this->get_category_root_of($data['collection_id']);
            //$all_properties_id = get_term_meta($root_category, 'socialdb_category_property_id');
            $all_properties_id = array_unique($this->get_parent_properties($root_category, [], $root_category));
            //buscando metadados de categoria
            if($data['classifications']){
                $values = explode(',', $data['classifications']);
                foreach ($values as $value) {
                    $properties = get_term_meta(trim($value), 'socialdb_category_property_id');
                    if(is_array($properties))
                        $all_properties_id = array_filter(array_unique(array_merge($all_properties_id, $properties)));
                }
            }
            // listando dados
            if ($all_properties_id) {
                foreach ($all_properties_id as $property_id) {
                    $property = get_term_by("id", $property_id, "socialdb_property_type");
                    if (in_array($property->slug, $this->fixed_slugs)):
                        continue;
                    endif;
                    $type = $propertyModel->get_property_type($property_id); // pego o tipo da propriedade
                    if ($type == 'socialdb_property_data') {
                        $csv_data[utf8_decode($property->name)] = get_post_meta(get_the_ID(), 'socialdb_property_' . $property_id, true);
                    } elseif ($type == 'socialdb_property_object') {
                        $property_result_meta_value = get_post_meta(get_the_ID(), 'socialdb_property_' . $property_id);
                        if (is_array($property_result_meta_value) && $property_result_meta_value[0] != '') {
                            foreach ($property_result_meta_value as $property_meta_value) {
                                $array_property_name[] = get_post($property_meta_value)->post_title;
                            }
                            $csv_data[utf8_decode($property->name)] = utf8_decode(implode(', ', array_unique($array_property_name)));
                        } else {
                            $csv_data[utf8_decode($property->name)] = '';
                        }
                    } elseif ($type == 'socialdb_property_term') {
                        if (is_array($categories)):
                            foreach ($categories as $category) {
                               // $facet_id = $category_model->get_category_facet_parent($category->term_id, $data['collection_id']);
                                $facet_id = $this->is_children_term($property_id,$category->term_id);
                                if (!$facet_id) {
                                    continue;
                                }
                                $categories_of_facet[$property_id][] = $this->get_hierarchy_names($category->term_id, $facet_id);
                            }
                        endif;
                    }
                }
                $array_property_name = [];
            }
            //property terms
            if ($categories_of_facet !== '' && is_array($categories_of_facet)) {
                foreach ($categories_of_facet as $property_id => $values) {
                    $term = get_term_by('id', $property_id, 'socialdb_property_type');
                    if (!$term) {
                        $term = get_term_by('id', $property_id, 'socialdb_category_type');
                    }
                    if (is_array($categories_of_facet[$property_id])):
                        $csv_data[utf8_decode($term->name)] = utf8_decode(implode(', ', array_unique($categories_of_facet[$property_id])));
                    else:
                        $csv_data[utf8_decode($term->name)] = '';
                    endif;
                }
            }

            /** Arquivos * */
            $array_files = $this->list_files_to_export(get_the_ID());
            if ($array_files) {
                $csv_data['Files'] = implode(', ', $array_files);
            } else {
                $csv_data['Files'] = '';
            }

            /**             * ************************** */
            //var_dump($csv_data);exit();
            $csv[] = $csv_data;
        }
        return $csv;
    }

    public function get_hierarchy_names($category_id, $facet_id) {
        $result = [];
        $flag = false;
        $parents = array_reverse(get_ancestors($category_id, 'socialdb_category_type'));
        if (is_array($parents) && !empty($parents)) {
            foreach ($parents as $parent) {
                if ($parent == $facet_id) {
                    $flag = true;
                }
                if ($flag)
                    $result[] = get_term_by('id', $parent, 'socialdb_category_type')->name;
            }
        }
        $result[] = get_term_by('id', $category_id, 'socialdb_category_type')->name;
        unset($result[0]);
        return implode('::', $result);
    }

    public function list_files_to_export($object_id) {
        $real_attachments = [];
        if ($object_id) {
            $post = get_post($object_id);
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
                            $url = wp_get_attachment_url($attachment->ID);
                            //$array_temp['size'] = filesize( get_attached_file( $attachment->ID ) );
                            $real_attachments[] = $url;
                        }
                    }
                }
            }
        }
        if (!empty($real_attachments)) {
            return $real_attachments;
        } else {
            return false;
        }
    }

    /**
     * function get_selected_objects()
     * @param array Array com os dados a serem utilizados para realizar os filtros
     * @return void 
     * Metodo reponsavel em  realizar a filtragem dos items que estao listados
     * @author Eduardo Humberto 
     */
    public function get_selected_objects($data) {
        // pego as classificacoes
        $object_model = new ObjectModel;
        $categories = $object_model->get_classification('category', $data['classifications']);
        $tags = $object_model->get_classification('tag', $data['classifications']);
        $properties = $object_model->get_classification('property', $data['classifications']);
        // inserindo as categorias e as tags na query
        $tax_query = $object_model->get_tax_query($categories, $data['collection_id'], $tags);
        //inserindo as propriedades
        $meta_query = $object_model->get_meta_query($properties);
        //tipo de ordenacao
        $orderby = $object_model->set_order_by($data);
        if ($orderby == 'meta_value_num') {
            $meta_key = 'socialdb_property_' . trim($data['ordenation_id']);
        }
        //a forma de ordenacao
        $order = $object_model->set_type_order($data);
        //all_data_inside
        $args = array(
            'post_type' => 'socialdb_object',
            'paged' => $page,
            'posts_per_page' => -1,
            'tax_query' => $tax_query,
            'orderby' => $meta_key,
            'order' => $order,
            //'no_found_rows' => true, // counts posts, remove if pagination required
            'update_post_term_cache' => false, // grabs terms, remove if terms required (category, tag...)
            'update_post_meta_cache' => false, // grabs post meta, remove if post meta required
        );
        if ($meta_query) {
            $args['meta_query'] = $meta_query;
        }
        if (isset($meta_key)) {
            $args['meta_key'] = $meta_key;
        }
        if (isset($data['keyword']) && $data['keyword'] != '') {
            $args['s'] = $data['keyword'];
        }
        $loop = new WP_Query($args);
        return $loop;
    }
    
    /**
     * 
     * @param type $property_id
     * @param type $category_id
     * @return boolean
     */
    public function is_children_term($property_id,$category_id) {
        $has_value = false;
        $term_root = get_term_meta($property_id, 'socialdb_property_term_root', true);
        if($term_root){
            $hierarchy = get_ancestors($category_id, 'socialdb_category_type');
            if( in_array((int)$term_root, $hierarchy)){
                return $term_root;
            }
            return false;
        }else{
            return false;
        }
    }

    function get_name_file() {
        list($usec, $sec) = explode(" ", microtime());
        return ((int) ($usec * 1000) + (int) ($sec * 1000));
    }

    public function array2csv(array &$array, $delimiter = ';') {

        if (count($array) == 0) {
            return null;
        }
        //$filename = $this->get_name_file();
        $filename = 'tainacan_csv';
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)), $delimiter);
        foreach ($array as $row) {
            fputcsv($df, $row, $delimiter);
        }
        fclose($df);
    }

    function download_send_headers($filename) {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2020 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download  
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }

    public function array2csv_full(array &$array, $filename, $delimiter = ';') {
        if (count($array) == 0) {
            return null;
        }
        if (!is_dir(dirname(__FILE__) . '/collections/')) {
            mkdir(dirname(__FILE__) . '/collections');
        }
        //$filename = $this->get_name_file();
        //$filename = 'socialdb_csv';
        $df = fopen(dirname(__FILE__) . '/collections/' . utf8_decode($filename) . '.csv', 'w');
        if ($df) {
            fputcsv($df, array_keys(reset($array)), $delimiter);
            foreach ($array as $row) {
                fputcsv($df, $row, $delimiter);
            }
            fclose($df);
        }
    }

    public function force_zip_download() {
        $file = dirname(__FILE__) . '/tainacan_full_csv.zip';
        if (headers_sent()) {
            echo 'HTTP header already sent';
        } else {
            if (!is_file($file)) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
                echo 'File not found';
            } else if (!is_readable($file)) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
                echo 'File not readable';
            } else {
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                header("Content-Type: application/zip");
                header("Content-Transfer-Encoding: Binary");
                header("Content-Length: " . filesize($file));
                header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
                readfile($file);
                unlink($file);
                $this->recursiveRemoveDirectory(dirname(__FILE__) . '/collections');
                exit;
            }
        }
    }

}
