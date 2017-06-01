<?php

/**
 * Author: Eduardo Humberto
 */
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../property/property_model.php');
require_once(dirname(__FILE__) . '../../category/category_model.php');

class MappingModel extends Model {

    public function MappingModel($name) {
        $this->parent = get_term_by('name', $name, 'socialdb_channel_type');
    }

    /**
     * @signature - create_mapping($data)
     * @param string $name O nome do mapeamento
     * @param int $collection_id O id da colecao
     * @return int O id do mapeamento criado
     * @description - funcao que cria o mapeamnto e vincula com a colecao
     * @author: Eduardo 
     */
    public function create_mapping($name, $collection_id) {
        $post = array(
            'post_title' => $name,
            'post_status' => 'publish',
            'post_type' => 'socialdb_channel'
        );
        $object_id = wp_insert_post($post);
        add_post_meta($object_id, 'socialdb_channel_identificator', $name);
        add_post_meta($collection_id, 'socialdb_collection_channel', $object_id);
        if(isset($this->parent->term_id))
            wp_set_object_terms($object_id, array((int) $this->parent->term_id), 'socialdb_channel_type');
        return $object_id;
    }

    /**
     * @signature - delete_mapping($data)
     * @param int $mapping_id O id do mapeamento a ser excluido
     * @param int $collection_id O id da colecao
     * @return int O id do mapeamento criado
     * @description - funcao que cria o mapeamnto e vincula com a colecao
     * @author: Eduardo 
     */
    public function delete_mapping($mapping_id, $collection_id) {
        $return = [];
        $import_type = wp_get_object_terms($mapping_id, 'socialdb_channel_type');
        if (!empty($import_type) && isset($import_type[0]->name) && $import_type[0]->name == 'socialdb_channel_csv') {
            $csv_file = $this->show_files_csv($mapping_id);
            wp_delete_attachment($csv_file[0]->ID);
        }
        $post_meta = delete_post_meta($collection_id, 'socialdb_collection_channel', $mapping_id);
        $active_import = get_post_meta($collection_id, 'socialdb_collection_mapping_import_active', true);
        if ($active_import && $active_import == $mapping_id) {
            delete_post_meta($collection_id, 'socialdb_collection_mapping_import_active');
        }

        $delete_post = wp_delete_post($mapping_id);
        if ($post_meta && $delete_post) {
            $return['title'] = __('Success', 'tainacan');
            $return['msg'] = __('Mapping deleted successfully', 'tainacan');
            $return['type'] = __('success');
        } else {
            $return['title'] = __('Error', 'tainacan');
            $return['msg'] = __('Mapping not exists anymore!', 'tainacan');
            $return['type'] = __('error');
        }
        return $return;
    }

    /**
     * @signature - validate_url($data)
     * @param array $data Os dados vindos do formulario
     * @return array com os dados que serao utilizados para inserir a colecao via OAIPMH
     * @description - funcao que retorna todos os metadatas para realizar o mapeiamento das propriedades do repositorio escolhido
     * @author: Eduardo 
     */
    public function saving_mapping_dublin_core($data) {
        $dataInfo = array();
        $object_id = $this->create_mapping($data['url_base'], $data['collection_id']);
        parse_str($data['form'], $form); // parseio o formulario de mapeiamento de entidades
        //inserindo os valores do mapeamento
        $counter_oia_dc = $form['counter_oai_dc'];
        for ($i = 1; $i <= $counter_oia_dc; $i++) {
            if (isset($form['mapping_dublin_core_' . $i]) && $form['mapping_dublin_core_' . $i] !== '' && $form['mapping_socialdb_' . $i] !== '') {
                if ($form['qualifier_' . $i] !== '' && !empty($form['qualifier_' . $i])) {
                    $dataInfo[] = array('tag' => $form['mapping_dublin_core_' . $i], 'attribute_name' => 'qualifier', 'attribute_value' => $form['qualifier_' . $i], 'socialdb_entity' => $form['mapping_socialdb_' . $i]);
                } else {
                    $dataInfo[] = array('tag' => $form['mapping_dublin_core_' . $i], 'socialdb_entity' => $form['mapping_socialdb_' . $i]);
                }
            }
        }
        foreach ($form as $name => $value) {
            // if ($value !== '' && $this->verify_dublin_core($name)) {
            //     if (strpos($name, '_') !== false) {
            //         $tag = explode('_', $name);
            //         $dataInfo[] = array('tag' => $tag[0], 'attribute_name' => 'qualifier', 'attribute_value' => $tag[1], 'socialdb_entity' => $value);
            //     } else {
            //        $dataInfo[] = array('tag' => $name, 'socialdb_entity' => $value);
            //     }
            // } else
            if ($name == 'import_object') {
                add_post_meta($object_id, 'socialdb_channel_oaipmhdc_import_object', $value);
            } elseif ($name == 'tokenUrl') {
                add_post_meta($object_id, 'socialdb_channel_oaipmhdc_first_token', $value);
            } elseif ($name == 'all_size') {
                add_post_meta($object_id, 'socialdb_channel_oaipmhdc_initial_size', $value);
            } elseif ($name == 'sets') {
                add_post_meta($object_id, 'socialdb_channel_oaipmhdc_sets', $value);
            }
        }
        if (!empty($dataInfo)):
            add_post_meta($object_id, 'socialdb_channel_oaipmhdc_mapping', serialize($dataInfo));
        endif;
    }

    /**
     * @signature - validate_url($data)
     * @param array $data Os dados vindos do formulario
     * @return array com os dados que serao utilizados para inserir a colecao via OAIPMH
     * @description - funcao que retorna todos os metadatas para realizar o mapeiamento das propriedades do repositorio escolhido
     * @author: Eduardo 
     */
    public function saving_mapping_dublin_core_export($data) {
        $dataInfo = array();
        $object_id = $this->create_mapping('mapping_temp', $data['collection_id']);
        $post = array(
            'ID' => $object_id,
            'post_title' => 'Export Mapping ' . $object_id,
        );
        $object_id = wp_update_post($post);
        parse_str($data['form'], $form); // parseio o formulario de mapeiamento de entidades
        foreach ($form as $name => $value) {
            if ($value !== '' && $name !== 'export_object' && !strpos($name, 'qualifier_socialdb_')) {
                if (isset($form['qualifier_socialdb_' . $name]) && $form['qualifier_socialdb_' . $name] !== '') {
                    $dataInfo[] = array('tag' => $value, 'attribute_name' => 'qualifier', 'attribute_value' => $form['qualifier_socialdb_' . $name], 'socialdb_entity' => $name);
                } else {
                    $dataInfo[] = array('tag' => $value, 'socialdb_entity' => $name);
                }
            } elseif ($name == 'export_object') {
                add_post_meta($object_id, 'socialdb_channel_oaipmhdc_import_object', $value);
            }
        }
        if (!empty($dataInfo)):
            add_post_meta($object_id, 'socialdb_channel_oaipmhdc_mapping', serialize($dataInfo));
        endif;
    }

    /**
     * @signature - updating_mapping_metatags($data)
     * @param array $data Os dados vindos do formulario
     * @description - atualiza o metadados de um mapeamento
     * @author: Eduardo 
     */
    public function save_mapping_metatags($data) {
        $dataInfo = array();
        parse_str($data['form'], $form); // parseio o formulario de mapeiamento de entidades
        //insiro o mapeamento
        $has_mapping = $this->get_mapping_metatags($form['url'], $data['collection_id']);
        if (!$has_mapping) {
            $url_parsed = parse_url($form['url']);
            $this->parent = get_term_by('name', 'socialdb_channel_metatag', 'socialdb_channel_type');
            $object_id = $this->create_mapping($url_parsed['host'], $data['collection_id']);
            $data['result'] = 1;
        } else {
            $url_parsed = parse_url($form['url']);
            $object_id = $has_mapping;
            $data['result'] = $url_parsed['host'];
        }
        //inserindo os valores do mapeamento
        $counter_oia_dc = $form['counter_oai_dc'];
        for ($i = 0; $i < $counter_oia_dc; $i++) {
            if ($form['mapping_metatags_' . $i] !== '' && $form['mapping_socialdb_' . $i] !== '') {
                $dataInfo[] = array('tag' => $form['mapping_metatags_' . $i], 'socialdb_entity' => $form['mapping_socialdb_' . $i]);
            }
        }
        if (!empty($dataInfo)):
            update_post_meta($object_id, 'socialdb_channel_oaipmhdc_mapping', serialize($dataInfo));
        endif;
        return $data;
    }

    /**
     * @signature - updating_mapping_metatags($data)
     * @param array $data Os dados vindos do formulario
     * @description - atualiza o metadados de um mapeamento
     * @author: Eduardo 
     */
    public function updating_mapping_metatags($data) {
        $dataInfo = array();
        $object_id = $data['mapping_id'];
        parse_str($data['form'], $form); // parseio o formulario de mapeiamento de entidades
        //inserindo os valores do mapeamento
        $counter_oia_dc = $form['counter_oai_dc'];
        for ($i = 1; $i <= $counter_oia_dc; $i++) {
            if ($form['mapping_metatags_' . $i] !== '' && $form['mapping_socialdb_' . $i] !== '') {
                $dataInfo[] = array('tag' => $form['mapping_metatags_' . $i], 'socialdb_entity' => $form['mapping_socialdb_' . $i]);
            }
        }
        if (!empty($dataInfo)):
            update_post_meta($object_id, 'socialdb_channel_oaipmhdc_mapping', serialize($dataInfo));
        endif;
    }

    /**
     * @signature - updating_mapping_dublin_core($data)
     * @param array $data Os dados vindos do formulario
     * @return array com os dados que serao utilizados para inserir a colecao via OAIPMH
     * @description - funcao que retorna todos os metadatas para realizar o mapeiamento das propriedades do repositorio escolhido
     * @author: Eduardo 
     */
    public function updating_mapping_dublin_core($data) {
        $dataInfo = array();
        $object_id = $data['mapping_id'];
        parse_str($data['form'], $form); // parseio o formulario de mapeiamento de entidades
        //inserindo os valores do mapeamento
        $counter_oia_dc = $form['counter_oai_dc'];
        for ($i = 1; $i <= $counter_oia_dc; $i++) {
            if ($form['mapping_dublin_core_' . $i] !== '' && $form['mapping_socialdb_' . $i] !== '') {
                if ($form['qualifier_' . $i] !== '' && !empty($form['qualifier_' . $i])) {
                    $dataInfo[] = array('tag' => $form['mapping_dublin_core_' . $i], 'attribute_name' => 'qualifier', 'attribute_value' => $form['qualifier_' . $i], 'socialdb_entity' => $form['mapping_socialdb_' . $i]);
                } else {
                    $dataInfo[] = array('tag' => $form['mapping_dublin_core_' . $i], 'socialdb_entity' => $form['mapping_socialdb_' . $i]);
                }
            }
        }
        foreach ($form as $name => $value) {
            /* if ($value !== '' && $this->verify_dublin_core($name)) {
              if (strpos($name, '_') !== false) {
              $tag = explode('_', $name);
              $dataInfo[] = array('tag' => $tag[0], 'attribute_name' => 'qualifier', 'attribute_value' => $tag[1], 'socialdb_entity' => $value);
              } else {
              $dataInfo[] = array('tag' => $name, 'socialdb_entity' => $value);
              }
              } else */
            if ($name == 'import_object') {
                update_post_meta($object_id, 'socialdb_channel_oaipmhdc_import_object', $value);
            } elseif ($name == 'tokenUrl') {
                update_post_meta($object_id, 'socialdb_channel_oaipmhdc_first_token', $value);
            } elseif ($name == 'all_size') {
                update_post_meta($object_id, 'socialdb_channel_oaipmhdc_initial_size', $value);
            }
        }
        if (!empty($dataInfo)):
            update_post_meta($object_id, 'socialdb_channel_oaipmhdc_mapping', serialize($dataInfo));
        endif;
    }

    public function updating_mapping_csv($data) {
        $dataInfo = array();
        $object_id = $data['mapping_id'];
        parse_str($data['form'], $form); // parseio o formulario de mapeiamento de entidades
        foreach ($form as $name => $value) {
            if ($value !== '') {
                $dataInfo[] = array('value' => $name, 'socialdb_entity' => $value);
            }
        }
        if (!empty($dataInfo)):
            update_post_meta($object_id, 'socialdb_channel_csv_mapping', serialize($dataInfo));
        endif;
    }

    public function updating_social_mapping($data) {
        $dataInfo = array();
        $object_id = $data['mapping_id'];
        parse_str($data['form'], $form); // parseio o formulario de mapeiamento de entidades
        foreach ($form as $name => $value) {
            if ($value !== '' && $name != 'mapping_id' && $name != 'social_network_term' && $name != 'social_network') {
                $dataInfo[] = array('tag' => $value, 'socialdb_entity' => $name);
            }
        }
        if (!empty($dataInfo)):
            update_post_meta($object_id, $data['term'], serialize($dataInfo));
        endif;
    }

    /**
     * @signature - updating_mapping_dublin_core($data)
     * @param array $data Os dados vindos do formulario
     * @return array com os dados que serao utilizados para inserir a colecao via OAIPMH
     * @description - funcao que retorna todos os metadatas para realizar o mapeiamento das propriedades do repositorio escolhido
     * @author: Eduardo 
     */
    public function updating_mapping_dublin_core_export($data) {
        $dataInfo = array();
        $object_id = $data['mapping_id'];
        parse_str($data['form'], $form); // parseio o formulario de mapeiamento de entidades
        foreach ($form as $name => $value) {
            if ($value !== '' && $name !== 'export_object' && !strpos($name, 'qualifier_socialdb_')) {
                if ($form['qualifier_socialdb_' . $name] !== '') {
                    $dataInfo[] = array('tag' => $value, 'attribute_name' => 'qualifier', 'attribute_value' => $form['qualifier_socialdb_' . $name], 'socialdb_entity' => $name);
                } else {
                    $dataInfo[] = array('tag' => $value, 'socialdb_entity' => $name);
                }
            } elseif ($name == 'export_object') {
                update_post_meta($object_id, 'socialdb_channel_oaipmhdc_import_object', $value);
            }
        }
        if (!empty($dataInfo)):
            update_post_meta($object_id, 'socialdb_channel_oaipmhdc_mapping', serialize($dataInfo));
        endif;
    }

    /**
     * @signature - is_harvesting($data)
     * @param array $data Os dados vindos do formulario
     * @return array com os dados que serao utilizados para inserir a colecao via OAIPMH
     * @description - funcao que retorna todos os metadatas para realizar o mapeiamento das propriedades do repositorio escolhido
     * @author: Eduardo 
     */
    public function is_harvesting($data) {
        $object_id = $data['mapping_id'];
        $is_harvesting = $data['is_harvesting'];
        if (!empty($is_harvesting)):
            return update_post_meta($object_id, 'socialdb_channel_oaipmhdc_is_harvesting', $is_harvesting);
        endif;
        return false;
    }

    /**
     * @signature - validate_url($data)
     * @param array $data Os dados vindos do formulario
     * @return array com os dados que serao utilizados para inserir a colecao via OAIPMH
     * @description - funcao que retorna todos os metadatas para realizar o mapeiamento das propriedades do repositorio escolhido
     * @author: Eduardo 
     */
    public function verify_dublin_core($name) {
        $array = array('title',
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
        foreach ($array as $dc_tags):
            if (strpos($name, $dc_tags) !== false):
                return true;
            endif;
        endforeach;
        return false;
    }

    /**
     * @signature - list_mapping_dublin_core($collection_id)
     * @param int $collection_id Os dados vindos do formulario
     * @return array com os dados que serao utilizados para inserir a colecao via OAIPMH
     * @description - funcao que retorna todos os metadatas para realizar o mapeiamento das propriedades do repositorio escolhido
     * @author: Eduardo 
     */
    public function list_mapping_dublin_core($collection_id) {
//array de configuração dos parâmetros de get_posts()
        $channels = get_post_meta($collection_id, 'socialdb_collection_channel');

        if (is_array($channels)) {
            $json = [];
            $json['active_mapping'] = get_post_meta($collection_id, 'socialdb_collection_mapping_exportation_active', true);
            foreach ($channels as $ch) {
                $ch = get_post($ch);
                $oai_pmhdc = wp_get_object_terms($ch->ID, 'socialdb_channel_type');
                if (!empty($ch) && !empty($oai_pmhdc) && isset($oai_pmhdc[0]->name) && $oai_pmhdc[0]->name == 'socialdb_channel_oaipmhdc') {
                    $postMetaLastUpdate = get_post_meta($ch->ID, 'socialdb_channel_oaipmhdc_last_update', true);
                    if ($postMetaLastUpdate) {
                        $postMetaLastUpdate = date("d/m/Y", $postMetaLastUpdate);
                    }
                    $token = get_post_meta($ch->ID, 'socialdb_channel_oaipmhdc_first_token', true);
                    $size = get_post_meta($ch->ID, 'socialdb_channel_oaipmhdc_initial_size', true);
                    $sets = get_post_meta($ch->ID, 'socialdb_channel_oaipmhdc_sets', true);
                    $is_harvesting = get_post_meta($ch->ID, 'socialdb_channel_oaipmhdc_is_harvesting', true);
                    if ($is_harvesting == '' || !$is_harvesting) {
                        $is_harvesting = 'disabled';
                    }
                    $array = array('name' => $ch->post_title,
                        'id' => $ch->ID, 'lastUpdate' => $postMetaLastUpdate, 'token' => $token,
                        'size' => $size, 'sets' => $sets, 'is_harvesting' => $is_harvesting);
                    $json['identifier'][] = $array;
                }
            }
            return $json;
        } else {
            return false;
        }
    }

    /**
     * @signature - list_mapping_dublin_core($collection_id)
     * @param int $collection_id Os dados vindos do formulario
     * @return array com os dados que serao utilizados para inserir a colecao via OAIPMH
     * @description - funcao que retorna todos os metadatas para realizar o mapeiamento das propriedades do repositorio escolhido
     * @author: Eduardo 
     */
    public function list_mapping_metatag($collection_id) {
        //array de configuração dos parâmetros de get_posts()
        $channels = get_post_meta($collection_id, 'socialdb_collection_channel');

        if (is_array($channels)) {
            $json = [];
            foreach ($channels as $ch) {
                $ch = get_post($ch);
                $oai_pmhdc = wp_get_object_terms($ch->ID, 'socialdb_channel_type');
                if (!empty($ch) && !empty($oai_pmhdc) && isset($oai_pmhdc[0]->name) && $oai_pmhdc[0]->name == 'socialdb_channel_metatag') {
                    //$token = get_post_meta($ch->ID, 'socialdb_channel_oaipmhdc_first_token', true);
                    //$size = get_post_meta($ch->ID, 'socialdb_channel_oaipmhdc_initial_size', true);
                    //$sets = get_post_meta($ch->ID, 'socialdb_channel_oaipmhdc_sets', true);
                    $array = array('name' => $ch->post_title,
                        'id' => $ch->ID);
                    $json['identifier'][] = $array;
                }
            }
            return $json;
        } else {
            return false;
        }
    }

    /**
     * @signature - get_mapping_dublin_core($data)
     * @param array $mapping_id Os dados vindos do formulario
     * @return array com os dados que serao utilizados para inserir a colecao via OAIPMH
     * @description - funcao que retorna todos os metadatas para realizar o mapeiamento das propriedades do repositorio escolhido
     * @author: Eduardo 
     */
    public function get_mapping_dublin_core($mapping_id) {
        $data = array();
        $data['mapping'] = unserialize(get_post_meta($mapping_id, 'socialdb_channel_oaipmhdc_mapping', true));
        $data['import_object'] = get_post_meta($mapping_id, 'socialdb_channel_oaipmhdc_import_object', true);
        return $data;
    }

    public function get_mapping_social_network($mapping_id, $term) {
        $data = array();
        $data['mapping'] = unserialize(get_post_meta($mapping_id, $term, true));
        return $data;
    }

    public function get_mapping_csv($mapping_id) {
        $data = array();
        $data['mapping'] = unserialize(get_post_meta($mapping_id, 'socialdb_channel_csv_mapping', true));
        return $data;
    }

    public function save_delimiter_csv($mapping_id, $delimiter, $multi_values, $hierarchy, $import_zip_csv, $has_header = 0 ,$code = 'utf8') {
        update_post_meta($mapping_id, 'socialdb_channel_csv_delimiter', $delimiter);
        update_post_meta($mapping_id, 'socialdb_channel_csv_multi_values', $multi_values);
        update_post_meta($mapping_id, 'socialdb_channel_csv_hierarchy', $hierarchy);
        update_post_meta($mapping_id, 'socialdb_channel_csv_import_zip', $import_zip_csv);
        update_post_meta($mapping_id, 'socialdb_channel_csv_has_header', $has_header);
        update_post_meta($mapping_id, 'socialdb_channel_csv_code', $code);
    }

    public function validate_zip(array $File, array $data, $Name = null) {
        $result = array();
        if ($File["error"] == 4) {
            $result['msg'] = "Envie algum arquivo para importar!";
            $result['error'] = 1;
        } else {
            $Name = ((string) $Name ? $Name : substr($File['name'], 0, strrpos($File['name'], '.')));
            $FileType = substr($File['name'], strrpos($File['name'], '.'));

            $FileAccept = [
                'application/zip',
                'application/x-zip-compressed',
                'multipart/x-zip',
                'application/x-compressed'
            ];

            $FileAcceptType = [
                '.zip'
            ];

            $FileTypeUnd = [
                'application/download',
                'application/octet-stream'
            ];

            if (!in_array($File['type'], $FileAccept)):
                if (in_array($File['type'], $FileTypeUnd)):
                    if (!in_array($FileType, $FileAcceptType)):
                        $result['msg'] = "Tipo de arquivo não suportado. Envie .ZIP!";
                        $result['error'] = 1;
                    else:
                        $result['error'] = 0;
                        $result['path'] = $this->unzip($File, $data);
                    endif;
                else:
                    $result['msg'] = "Tipo de arquivo não suportado. Envie .ZIP!";
                    $result['error'] = 1;
                endif;
            else:
                $result['error'] = 0;
                $result['path'] = $this->unzip($File, $data);
            endif;
        }

        return $result;
    }

    public function unzip($file, $data) {
        error_reporting(0);
        //if ($_FILES["collection_file"]["name"]) {
        //$file = $_FILES["collection_file"];
        $filename = $file["name"];
        $tmp_name = $file["tmp_name"];
        $type = $file["type"];

        $name = explode(".", $filename);

        /* here it is really happening */
        $ran = $name[0] . "-" . time() . "-" . rand(1, time());
        $targetdir = dirname(__FILE__) . "/" . $ran;
        $targetzip = dirname(__FILE__) . "/" . $ran . ".zip";

        if (move_uploaded_file($tmp_name, $targetzip)) { //Uploading the Zip File

            /* Extracting Zip File */
            //var_dump($targetzip, $targetdir);
            $zip = new ZipArchive();
            $x = $zip->open($targetzip);  // open the zip file to extract
            if ($x === true) {
                $zip->extractTo($targetdir); // place in the directory with same name  
                $zip->close();
                unlink($targetzip); //Deleting the Zipped file
            }
        }
        //}
        
        $targetdir = str_replace("\\", "/", $targetdir);
        
        return $targetdir;
    }
    
    /**
     * extrao o zip e retorna o csv da colecao
     * @param type $path
     * @param type $delimiter
     */
    public function get_csv_in_zip_file($path,$delimiter) {
         /* here it is really happening */
         error_reporting(0);
        $time = time();
        $targetdir = dirname(__FILE__) . "/" .$time;
        $targetzip = dirname(__FILE__)."/" . $time . ".zip";
        mkdir($targetdir);
        /* Extracting Zip File */
        $zip = new ZipArchive();
        if (copy($path, $targetzip)) { //Uploading the Zip File
            $x = $zip->open($targetzip);  // open the zip file to extract
            $zip->extractTo($targetdir);
            if ($x === true) {
                $zip->extractTo($targetdir); // place in the directory with same name  
                if(is_file($targetdir.'/csv-package/administrative-settings.csv')){
                    $objeto = fopen($targetdir.'/csv-package/administrative-settings.csv', 'r');
                    $csv_data = fgetcsv($objeto, 0, $delimiter);
                    while(($csv_data = fgetcsv($objeto, 0, $delimiter)) !== false){
                        break;
                    }
                    fclose($objeto);
                    $zip->close();
                    unlink($targetzip); //Deleting the Zipped file
                    $this->recursiveRemoveDirectory($targetdir);
                    return array_filter($csv_data);
                }    
                $zip->close();
                unlink($targetzip); //Deleting the Zipped file
                $this->recursiveRemoveDirectory($targetdir);
            }
        }
        return [];
    }

    public function show_files_csv($mapping_id) {
        $real_attachments = [];
        if ($mapping_id) {
            $post = get_post($mapping_id);
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
                $arquivos = get_post_meta($post->ID, '_thumbnail_id');
                if ($attachments) {
                    foreach ($attachments as $attachment) {
                        if (in_array($attachment->ID, $arquivos)) {
                            $real_attachments[] = $attachment;
                        }
                    }
                }

                if (empty($real_attachments)) {
                    $arquivos = get_post_meta($post->ID, '_file_id');
                    if ($attachments) {
                        foreach ($attachments as $attachment) {
                            if (in_array($attachment->ID, $arquivos)) {
                                $real_attachments[] = $attachment;
                            }
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

    public function saving_mapping_csv($data) {
        $dataInfo = array();
        parse_str($data['form'], $form); // parseio o formulario de mapeiamento de entidades
        $object_id = $form['socialdb_csv_mapping_id'];
        foreach ($form as $name => $value) {
            if ($name != 'socialdb_csv_mapping_id') {
                $dataInfo[] = array('value' => $name, 'socialdb_entity' => $value);
            }
        }
        if (!empty($dataInfo)):
            add_post_meta($object_id, 'socialdb_channel_csv_mapping', serialize($dataInfo));
        endif;
    }

    public function list_mapping_csv($collection_id) {
//array de configuração dos parâmetros de get_posts()
        $channels = get_post_meta($collection_id, 'socialdb_collection_channel');
        if (is_array($channels)) {
            $json = [];
            foreach ($channels as $ch) {
                $ch = get_post($ch);
                $csv = wp_get_object_terms($ch->ID, 'socialdb_channel_type');
                if (!empty($ch) && !empty($csv) && isset($csv[0]->name) && $csv[0]->name == 'socialdb_channel_csv') {
                    $postMetaLastUpdate = get_post_meta($ch->ID, 'socialdb_channel_csv_last_update', true);
                    if ($postMetaLastUpdate) {
                        $postMetaLastUpdate = date("d/m/Y", $postMetaLastUpdate);
                    }

                    $array = array('name' => $ch->post_title,
                        'id' => $ch->ID, 'lastUpdate' => $postMetaLastUpdate);
                    $json['identifier'][] = $array;
                }
            }
            return $json;
        } else {
            return false;
        }
    }

    /*     * ****************************************************************************** */

    public function set_active_mapping($data) {
        $objects = $this->get_collection_posts($data['collection_id']);
        update_post_meta($data['collection_id'], 'socialdb_collection_mapping_exportation_active', $data['mapping_id']);
        foreach ($objects as $object) {
            $channels = get_post_meta($object->ID, 'socialdb_channel_id');
            if ($channels && is_array($channels)) {
                foreach ($channels as $channel) {
                    $terms = wp_get_object_terms($channel, 'socialdb_channel_type');
                    if ($terms && !empty($terms) && $terms[0]->name == 'socialdb_channel_oaipmhdc') {
                        delete_post_meta($object->ID, 'socialdb_channel_id', $channel);
                    }
                }
            }
            add_post_meta($object->ID, 'socialdb_channel_id', $data['mapping_id']);
        }
        $link = site_url() . '/oai/socialdb-oai/?verb=ListRecords&metadataPrefix=oai_dc&set=' . $data['collection_id'];
        $data['title'] = __('Success', 'tainacan');
        $data['msg'] = __('Collection available with this mapping in in the OAI-PMH repository', 'tainacan');
        $data['html'] = "<a class='btn btn-primary' href='$link'>" . __('See repository', 'tainacan') . '</a><br><br>';
        $data['type'] = 'success';
        $data['result'] = '1';
        return $data;
    }

    /*     * ************************************************************************
     *                         SALVANDO MAPEAMENTOS METATAGS ITEM
     * ************************************************************************ */

    /**
     * 
     * @param type $url
     * @param type $collection_id
     */
    public function get_mapping_metatags($url, $collection_id) {
        $parse = parse_url($url);
        $mappings = $this->list_mapping_metatag($collection_id);
        if ($mappings && isset($mappings['identifier'])) {
            foreach ($mappings['identifier'] as $mapp) {
                if ($mapp['name'] == $parse['host']) {
                    return $mapp['id'];
                }
            }
        }
        return false;
    }

    /**
     * 
     * @param type $param
     */
    public function saving_mapping_metatags($data) {
        $identifier = '';
        if ($data['mapped_generic_properties'] == '') {
            return false;
        }
        //insiro o mapeamento
        $has_mapping = $this->get_mapping_metatags($data['url'], $data['collection_id']);
        if (!$has_mapping) {
            $url_parsed = parse_url($data['url']);
            $this->parent = get_term_by('name', 'socialdb_channel_metatag', 'socialdb_channel_type');
            $object_id = $this->create_mapping($url_parsed['host'], $data['collection_id']);
        } else {
            $object_id = $has_mapping;
        }
        // mapeamento 
        $array_generic_mapped = explode(',', $data['mapped_generic_properties']);
        $array_tainacan_mapped = explode(',', $data['mapped_tainacan_properties']);
        foreach ($array_generic_mapped as $key => $generic) {
            if (strpos($array_tainacan_mapped[$key], 'new_') !== false) {
                $id = str_replace('new_', '', $array_tainacan_mapped[$key]);
                if ($data['create_property_' . $id]) {
                    $identifier = 'dataproperty_' . $this->add_property_data($data['name_property_' . $id], $data['widget_property_' . $id], $data['collection_id']);
                    $dataInfo[] = array('tag' => $array_generic_mapped[$key], 'socialdb_entity' => $identifier);
                }
            } else {
                $identifier = $array_tainacan_mapped[$key];
                $dataInfo[] = array('tag' => $array_generic_mapped[$key], 'socialdb_entity' => $identifier);
            }
        }
        update_post_meta($object_id, 'socialdb_channel_oaipmhdc_initial_size', '1');
        update_post_meta($object_id, 'socialdb_channel_oaipmhdc_mapping', serialize($dataInfo));
        return $object_id;
    }

    /*     * ************************************************************************
     *                         SALVANDO MAPEAMENTOS OAI-PMH ITEM
     * ************************************************************************ */

    /**
     * 
     * @param type $param
     */
    public function saving_mapping_handle($data) {
        $identifier = '';
        if ($data['mapped_generic_properties'] == '') {
            return false;
        }
        //insiro o mapeamento
        $has_mapping = get_post_meta($data['collection_id'], 'socialdb_collection_mapping_import_active', true);
        if (!is_numeric($has_mapping)) {
            $object_id = $this->create_mapping(__('Mapping Default', 'tainacan'), $data['collection_id']);
            update_post_meta($data['collection_id'], 'socialdb_collection_mapping_import_active', $object_id);
        } else {
            $object_id = $has_mapping;
        }
        // mapeamento 
        $array_generic_mapped = explode(',', $data['mapped_generic_properties']);
        $array_tainacan_mapped = explode(',', $data['mapped_tainacan_properties']);
        foreach ($array_generic_mapped as $key => $generic) {
            if (strpos($array_tainacan_mapped[$key], 'new_') !== false) {
                $id = str_replace('new_', '', $array_tainacan_mapped[$key]);
                if ($data['create_property_' . $id]) {
                    $identifier = 'dataproperty_' . $this->add_property_data($data['name_property_' . $id], $data['widget_property_' . $id], $data['collection_id']);
                    $dataInfo[] = array('tag' => $array_generic_mapped[$key], 'socialdb_entity' => $identifier);
                }
            } else {
                $identifier = $array_tainacan_mapped[$key];
                $dataInfo[] = array('tag' => $array_generic_mapped[$key], 'socialdb_entity' => $identifier);
            }
        }
        update_post_meta($object_id, 'socialdb_channel_oaipmhdc_initial_size', '1');
        update_post_meta($object_id, 'socialdb_channel_oaipmhdc_mapping', serialize($dataInfo));
        return $object_id;
    }

    /**
     * function add_property_data($property)
     * @param object $property
     * @return int O id da da propriedade criada.
     * @author: Eduardo Humberto 
     */
    public function add_property_data($name, $widget, $collection_id) {
        $category_root_id = $this->get_category_root_of($collection_id);
        $new_property = wp_insert_term($name, 'socialdb_property_type', array('parent' => $this->get_property_type_id('socialdb_property_data'),
            'slug' => $this->generate_slug((string) $name, $collection_id)));
        update_term_meta($new_property['term_id'], 'socialdb_property_required', 'false');
        update_term_meta($new_property['term_id'], 'socialdb_property_data_widget', $widget);
        update_term_meta($new_property['term_id'], 'socialdb_property_data_column_ordenation', '');
        update_term_meta($new_property['term_id'], 'socialdb_property_default_value', '');
        update_term_meta($new_property['term_id'], 'socialdb_property_created_category', $category_root_id);
        add_term_meta($category_root_id, 'socialdb_category_property_id', $new_property['term_id']);
        return $new_property['term_id'];
    }

    /**
     * function get_property_type_id($property_parent_name)
     * @param string $property_parent_name
     * @return int O id da categoria que determinara o tipo da propriedade.
     * @author: Eduardo Humberto 
     */
    public function get_property_type_id($property_parent_name) {
        $property_root = get_term_by('name', $property_parent_name, 'socialdb_property_type');
        return $property_root->term_id;
    }

}
