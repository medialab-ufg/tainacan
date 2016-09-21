<?php

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../property/property_model.php');
require_once(dirname(__FILE__) . '../../category/category_model.php');
require_once(dirname(__FILE__) . '../../mapping/mapping_model.php');
require_once(dirname(__FILE__) . '../../collection/collection_model.php');

class CsvModel extends Model {

    public function validate_csv(array $File, array $data, $Name = null) {

        if ($File["error"] == 4) {
            $data['msg'] = "Envie algum arquivo para importar!";
            $data['error'] = 1;
        } else {
            $Name = ((string) $Name ? $Name : substr($File['name'], 0, strrpos($File['name'], '.')));
            $FileType = substr($File['name'], strrpos($File['name'], '.'));

            $FileAccept = [
                'application/vnd.ms-excel',
                'application/csv',
                'text/csv'
            ];

            $FileAcceptType = [
                '.csv'
            ];

            $FileTypeUnd = [
                'application/download',
                'application/octet-stream'
            ];

            if (!in_array($File['type'], $FileAccept)):
                if (in_array($File['type'], $FileTypeUnd)):
                    if (!in_array($FileType, $FileAcceptType)):
                        $data['msg'] = "Tipo de arquivo não suportado. Envie .CSV!";
                        $data['error'] = 1;
                    else:
                        $data['error'] = 0;
                        $data['mapping_id'] = $this->save_file($data);
                    endif;
                else:
                    $data['msg'] = "Tipo de arquivo não suportado. Envie .CSV!";
                    $data['error'] = 1;
                endif;
            else:
                $data['error'] = 0;
                $data['mapping_id'] = $this->save_file($data);
            endif;
        }

        return $data;
    }
    
    /**
     * 
     * @param array $File
     * @param array $data
     * @param type $Name
     * @return type
     */
    public function validate_zip(array $File, array $data, $Name = null) {
        if ($File["error"] == 4) {
            $data['msg'] = "Envie algum arquivo para importar!";
            $data['error'] = 1;
        } else {
            $data['error'] = 0;
            $data['mapping_id'] = $this->save_file($data);
        }
        return $data;
    }

    public function save_file($data) {
        $_FILES = $data['file'];
        $mapping = new MappingModel('socialdb_channel_csv');
        $mapping_id = $mapping->create_mapping($data['file']['csv_file']['name'], $data['collection_id']);
        if ($_FILES) {
            foreach ($_FILES as $file => $array) {
                if (!empty($_FILES[$file]["name"])) {
                    $_FILES[$file]["name"] = $this->remove_accent_file($_FILES[$file]["name"]);
                    $newupload = $this->insert_attachment($file, $mapping_id);
                }
            }
        }
        return $mapping_id;
    }

    public function verify_mapping($data) {
        $result = false;
        foreach ($data as $map) {
            if ($map["value"] != "socialdb_csv_mapping_id") {
                if ($map["socialdb_entity"] != "") {
                    $result = true;
                }
            }
        }

        return $result;
    }

    public function do_import_csv($data) {
        // $time_start = microtime(true);
        session_write_close();
        ini_set('max_execution_time', '0');
        $count = 0;
        $content = '';
        $categories[] = $this->get_category_root_of($data['collection_id']);
        $mapping_model = new MappingModel('socialdb_channel_csv');
        $bd_csv_data = unserialize(get_post_meta($data['mapping_id'], 'socialdb_channel_csv_mapping', true));
        $verify_mapping = $this->verify_mapping($bd_csv_data);
        if ($verify_mapping) {
            $mapping_id = $data['mapping_id'];
            update_post_meta($mapping_id, 'socialdb_channel_csv_last_update', mktime());
            $delimiter = get_post_meta($data['mapping_id'], 'socialdb_channel_csv_delimiter', true);
            $csv_has_header = get_post_meta($data['mapping_id'], 'socialdb_channel_csv_has_header', true);

            $multi_values = get_post_meta($data['mapping_id'], 'socialdb_channel_csv_multi_values', true);
            $hierarchy = get_post_meta($data['mapping_id'], 'socialdb_channel_csv_hierarchy', true);
            $import_zip_csv = get_post_meta($data['mapping_id'], 'socialdb_channel_csv_import_zip', true);

            $files = $mapping_model->show_files_csv($mapping_id);
            foreach ($files as $file) {
                //$name_file =  wp_get_attachment_link($file->ID, 'thumbnail', false, true);
                $name_file = wp_get_attachment_url($file->ID);
                $objeto = fopen($name_file, 'r');
                // LEITURA DO ARQUIVO
                //$time_before_read = microtime() - $time_start;
                while (($csv_data = fgetcsv($objeto, 0, $delimiter)) !== false) {
                    $count++;
                    if ($csv_has_header == 1 && $count == 1) {
                        continue;
                    } else {
                        $lines[] = $csv_data;
                    }
                }
                // $time_after_read = microtime() - $time_start;
                // var_dump(' Fim Leitura dos dados',$time_before_read,$time_after_read);
                // insercao dos dados
                for ($i = 0; $i < count($lines); $i++):
                    $object_id = socialdb_insert_object_csv('Import CSV ' . $count);
                    foreach ($bd_csv_data as $metadata) {
                        if ($metadata['socialdb_entity'] !== '') {
                            $field_value = $lines[$i][str_replace('csv_p', '', $metadata['value'])];
                            if ($metadata['socialdb_entity'] == 'post_title'):
                                if (mb_detect_encoding($field_value, 'auto') == 'UTF-8') {
                                    $field_value = iconv('ISO-8859-1', 'UTF-8', $field_value);
                                }
                                $this->update_title($object_id, $field_value);
                                $this->set_common_field_values($object_id, 'title', $field_value);
                            elseif ($metadata['socialdb_entity'] == 'post_content'):
                                $authFormat = array(
                                    'jpg',
                                    'png',
                                    'jpeg',
                                    'gif',
                                    'bmp'
                                );
                                $formatFile = explode('.', $field_value);
                                if (in_array(end($formatFile), $authFormat)) {
                                    if ($import_zip_csv == 'url_local') {
                                        //Tem arquivo ZIP
                                        $import_zip_path = get_post_meta($data['mapping_id'], 'socialdb_channel_csv_zip_path', true);
                                        $this->add_thumbnail_item_zip($import_zip_path . DIRECTORY_SEPARATOR . $field_value, $object_id);
                                        //$this->insert_attachment_file($import_zip_path . DIRECTORY_SEPARATOR . $field_value, $object_id);
                                    } else {
                                        //E uma URL e nao o caminho ZIP
                                        $this->add_thumbnail_url($field_value, $object_id);
                                        //$this->add_file_url($field_value, $object_id);
                                    }
                                } 
                                $content .= $field_value . ",";
                            elseif ($metadata['socialdb_entity'] == 'attach'):
                                //attachment (Files)
                                $files = explode(', ', $field_value);
                                if (is_array($files)) {
                                    foreach ($files as $file) {

                                        if ($import_zip_csv == 'url_local') {
                                            //Tem arquivo ZIP
                                            $import_zip_path = get_post_meta($data['mapping_id'], 'socialdb_channel_csv_zip_path', true);
                                            $this->insert_attachment_file($import_zip_path . DIRECTORY_SEPARATOR . $file, $object_id);
                                        } else {
                                            //E uma URL e nao o caminho ZIP
                                            $this->add_file_url($file, $object_id);
                                        }
                                    }
                                } else {
                                    if ($import_zip_csv == 'url_local') {
                                        //Tem arquivo ZIP
                                        $import_zip_path = get_post_meta($data['mapping_id'], 'socialdb_channel_csv_zip_path', true);
                                        $this->insert_attachment_file($import_zip_path . DIRECTORY_SEPARATOR . $field_value, $object_id);
                                    } else {
                                        //E uma URL e nao o caminho ZIP
                                        $this->add_file_url($field_value, $object_id);
                                    }
                                }
//update_post_meta($object_id, 'socialdb_object_dc_source', $field_value);
                            elseif ($metadata['socialdb_entity'] == 'post_permalink'):
                                update_post_meta($object_id, 'socialdb_object_dc_source', $field_value);
                                $this->set_common_field_values($object_id, 'object_source', $field_value);
                            elseif ($metadata['socialdb_entity'] == 'socialdb_object_content') :
                                if (mb_detect_encoding($field_value, 'auto') == 'UTF-8') {
                                    $field_value = iconv('ISO-8859-1', 'UTF-8', $field_value);
                                }
                                update_post_meta($object_id, 'socialdb_object_content', $field_value);
                                $this->set_common_field_values($object_id, 'object_content', $field_value); elseif ($metadata['socialdb_entity'] == 'socialdb_object_dc_type'):
                                update_post_meta($object_id, 'socialdb_object_dc_type', $field_value);
                                $this->set_common_field_values($object_id, 'object_type', $field_value);
                            elseif ($metadata['socialdb_entity'] == 'tag' && $field_value != ''):
//$fields_value = explode('||', $field_value);
                                $fields_value = explode($multi_values, $field_value);
                                foreach ($fields_value as $field_value):
                                    //$fields[] = explode('::', $field_value);
                                    $fields[] = explode($hierarchy, $field_value);
                                endforeach;
                                foreach ($fields as $fields_value):
                                    foreach ($fields_value as $field_value):
                                        $this->insert_tag($field_value, $object_id, $data['collection_id']);
                                    endforeach;
                                endforeach;
// elseif (strpos($metadata['socialdb_entity'], "facet_") !== false):
                            elseif (strpos($metadata['socialdb_entity'], "termproperty_") !== false):
                                if (is_array($field_value) && count($field_value) == 1) {
                                    $fields_value = $field_value[0];
                                    if (trim($fields_value) == '') {
                                        continue;
                                    }
                                } else {
                                    $fields_value = $field_value;
                                }
                                if (!empty($fields_value)):
//if (strpos($fields_value, '||') !== false) {
//    $fields_value = explode('||', $fields_value);
                                    if (strpos($fields_value, $multi_values) !== false) {
                                        $fields_value = explode($multi_values, $fields_value);
                                    } else {
                                        $fields_value = explode(', ', $fields_value);
                                    }
                                    $trans = array("termproperty_" => "");
                                    $property_id = strtr($metadata['socialdb_entity'], $trans);
                                    $parent = get_term_meta($property_id, 'socialdb_property_term_root', true);
//$fields_value = explode(', ', $field_value);
//$trans = array("facet_" => "");
//$parent = strtr($metadata['socialdb_entity'], $trans);
                                    foreach ($fields_value as $field_value):
//$this->insert_category($field_value, $object_id, $data['collection_id'], $parent);
                                        $this->insert_hierarchy($field_value, $object_id, $data['collection_id'], $parent, $property_id, $hierarchy);
                                    endforeach;
                                endif;
                            elseif (strpos($metadata['socialdb_entity'], "objectproperty_") !== false):
                                $trans = array("objectproperty_" => "");
                                $id = strtr($metadata['socialdb_entity'], $trans);
                                add_post_meta($object_id, 'socialdb_property_' . $id . '', $field_value);
                            elseif (strpos($metadata['socialdb_entity'], "dataproperty_") !== false):
                                $trans = array("dataproperty_" => "");
                                $id = strtr($metadata['socialdb_entity'], $trans);
                                $has_inserted = add_post_meta($object_id, 'socialdb_property_' . $id, $field_value);
                                if (!$has_inserted) {
                                    $final_test = add_post_meta($object_id, 'socialdb_property_' . $id, utf8_encode($field_value));
                                }
                                $this->set_common_field_values($object_id, "socialdb_property_$id", $field_value);
                            endif;
                        }
                    }
                    if (mb_detect_encoding($content, 'auto') == 'UTF-8') {
                        $content = iconv('ISO-8859-1', 'UTF-8', $content);
                    }
                    update_post_meta($object_id, 'socialdb_object_from', 'external');
                    $this->set_common_field_values($object_id, 'object_from', 'external');
                    update_post_content($object_id, $content);
                    $this->set_common_field_values($object_id, 'description', $content);
                    socialdb_add_tax_terms($object_id, $categories, 'socialdb_category_type');
                    $content = '';
//$time_after_insert = microtime() - $time_start;
//var_dump(' Fim da insercao do item',$time_after_insert);
                endfor;

                break;
            }
            if ($import_zip_csv == 'url_local') {
                $import_zip_path = get_post_meta($data['mapping_id'], 'socialdb_channel_csv_zip_path', true);
                if($import_zip_path && is_dir($import_zip_path)){
                    unlink($import_zip_path);
                }
            }
            return true;
        } else {
            return false;
        }
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
        $array = socialdb_insert_term($name, 'socialdb_tag_type', $parent->term_id, sanitize_title(remove_accent($name)) . "_collection" . $collection_id);
        socialdb_add_tax_terms($collection_id, array($array['term_id']), 'socialdb_tag_type');
        socialdb_add_tax_terms($object_id, array($array['term_id']), 'socialdb_tag_type');
    }

    public function insert_category($name, $object_id, $collection_id, $parent_id) {
        $array = socialdb_insert_term($name, 'socialdb_category_type', $parent_id, sanitize_title(remove_accent($name)) . '_' . mktime());
        socialdb_add_tax_terms($collection_id, array($array['term_id']), 'socialdb_category_type');
        socialdb_add_tax_terms($object_id, array($array['term_id']), 'socialdb_category_type');
        return $array;
    }

    function remove_accent_file($str) {
        $a = array('�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '�', '�', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '�', '�', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '�', '?', '?', '?', '?', '�', '�', '?', '�', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        return str_replace($a, $b, $str);
    }

    public function insert_hierarchy($metadata, $object_id, $collection_id, $parent = 0, $property_id = null, $hierarchy = '::') {
        $array = array();
        $categories = explode($hierarchy, $metadata);
        foreach ($categories as $category) {
            $array = $this->insert_category($category, $object_id, $collection_id, $parent);
            $this->concatenate_commom_field_value($object_id, "socialdb_propertyterm_$property_id", $array['term_id']);
            $parent = $array['term_id'];
        }
        socialdb_add_tax_terms($object_id, array($array['term_id']), 'socialdb_category_type');
    }

    /*
     * @signature unzip_csv_package()
     * @return string $targetdir o diretorio para onde foi descompactado o arquivo
     */

    public function unzip_csv_package($data) {
//var_dump($data['file']['csv_pkg']);
        if ($data['file']['csv_pkg']["name"]) {
            $file = $data['file']['csv_pkg'];
            $filename = $file["name"];
            $tmp_name = $file["tmp_name"];
            $type = $file["type"];

            $name = explode(".", $filename);
            $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');

            if (in_array($type, $accepted_types)) { //If it is Zipped/compressed File
                $okay = true;
            }

            $continue = strtolower($name[1]) == 'zip' ? true : false; //Checking the file Extension

            if (!$continue) {
                $message = "The file you are trying to upload is not a .zip file. Please try again.";
            }

            /* here it is really happening */
            $ran = $name[0] . "-" . time() . "-" . rand(1, time());
            $targetdir = dirname(__FILE__) . DIRECTORY_SEPARATOR . $ran;
            $targetzip = dirname(__FILE__) . DIRECTORY_SEPARATOR . $ran . ".zip";

            if (move_uploaded_file($tmp_name, $targetzip)) { //Uploading the Zip File

                /* Extracting Zip File */

                $zip = new ZipArchive();
                $x = $zip->open($targetzip);  // open the zip file to extract
                if ($x === true) {
                    $zip->extractTo($targetdir); // place in the directory with same name  
                    $zip->close();
                    unlink($targetzip); //Deleting the Zipped file
                }
            }
        }
        return $targetdir . DIRECTORY_SEPARATOR . 'collections';
    }

    public function import_csv_full(array $csv_files, $dir) {
        $collection_model = new CollectionModel;
//var_dump($csv_files, $dir);
        ini_set('max_execution_time', '0');
        $count = 0;
        foreach ($csv_files as $csv) {
            $name = explode(".", $csv);
            if (strtolower($name[1]) == 'csv') {
//cria a coleção com o nome do arquivo ($name[0])
                $data_collection['collection_name'] = html_entity_decode($name[0]);
                $data_collection['collection_object'] = __('item', 'tainacan');
                $collection_id = $collection_model->simple_add($data_collection, 'publish');
                create_root_collection_category($collection_id, $data_collection['collection_object']);
                $category_root_id = get_post_meta($collection_id, 'socialdb_collection_object_type', true);
//Coleção criada!
                $objeto = fopen($dir . DIRECTORY_SEPARATOR . $csv, 'r');
// LEITURA DO ARQUIVO
                $standart_metas = array(
                    'title',
                    'description',
                    'content',
                    'item_from',
                    'item_type',
                    'item_source',
                    'permalink',
                    'tags'
                );
                $mapping = [
                    ['socialdb_entity' => 'post_title', 'value' => 'title'],
                    [ 'socialdb_entity' => 'socialdb_object_content', 'value' => 'content'],
                    [ 'socialdb_entity' => 'post_content', 'value' => 'description'],
                    [ 'socialdb_entity' => 'socialdb_object_dc_type', 'value' => 'item_type'],
                    [ 'socialdb_entity' => 'post_permalink', 'value' => 'permalink'],
                    [ 'socialdb_entity' => 'tag', 'value' => 'tags']
                ];
                while (($csv_data = fgetcsv($objeto, 0, ';')) !== false) {
                    $count++;
                    if ($count == 1) {
                        $lines['header'] = $csv_data;
//cria os metadados da coleção
                        $arr_metas = array();
                        foreach ($csv_data as $key => $value) {
                            if (!in_array($value, $standart_metas) && trim($value) != '') {
                                $property_id = $this->add_property_data(utf8_encode($value), $category_root_id);
                                add_term_meta($category_root_id, 'socialdb_category_property_id', $property_id);
                                $arr_metas[] = array($key, $value, $property_id);
                                $mapping[] = ['socialdb_entity' => 'dataproperty_' . $property_id, 'value' => $value];
                            }
                        }
//Metadados Criados!
                    } else {
                        foreach ($csv_data as $key => $value) {
                            $new_csv_data[$lines['header'][$key]] = $value;
                        }
                        $lines[] = $new_csv_data;
                    }
                }
//insere os itens da coleção
                $this->insert_csv_itens($lines, $collection_id, $mapping, $category_root_id);
//Itens iseridos!
//var_dump($lines);
            }
        }
    }

    /**
     * function add_property_data($property)
     * @param object $property
     * @return int O id da da propriedade criada.
     * @author: Eduardo Humberto 
     */
    public function add_property_data($name, $category_root_id) {
        $new_property = wp_insert_term((string) $name, 'socialdb_property_type', array('parent' => $this->get_property_type_id('socialdb_property_data'), 'slug' => $this->generate_slug((string) $name, 0)));
        update_term_meta($new_property['term_id'], 'socialdb_property_required', false);
        update_term_meta($new_property['term_id'], 'socialdb_property_data_widget', 'text');
        update_term_meta($new_property['term_id'], 'socialdb_property_data_column_ordenation', false);
        update_term_meta($new_property['term_id'], 'socialdb_property_default_value', '');
        update_term_meta($new_property['term_id'], 'socialdb_property_created_category', $category_root_id);
        return $new_property['term_id'];
    }

    public function get_property_type_id($property_parent_name) {
        $property_root = get_term_by('name', $property_parent_name, 'socialdb_property_type');
        return $property_root->term_id;
    }

    public function insert_csv_itens($lines, $collection_id, $bd_csv_data, $category_root_id) {
        unset($lines['header']);
        $categories = array($category_root_id);
        for ($i = 0; $i < count($lines); $i++):
            $object_id = socialdb_insert_object_csv('Import CSV ' . $count);
            foreach ($bd_csv_data as $metadata) {
                if ($metadata['socialdb_entity'] !== '') {
                    $field_value = $lines[$i][str_replace('csv_p', '', $metadata['value'])];
                    if ($metadata['socialdb_entity'] == 'post_title'):
                        if (mb_detect_encoding($field_value, 'auto') == 'UTF-8') {
                            $field_value = iconv('ISO-8859-1', 'UTF-8', $field_value);
                        }
                        $this->update_title($object_id, $field_value);
                        $this->set_common_field_values($object_id, 'title', $field_value); elseif ($metadata ['socialdb_entity'] == 'post_content'):
                        $content .= $field_value . ",";
                    elseif ($metadata ['socialdb_entity'] == 'post_permalink'):
                        update_post_meta($object_id, 'socialdb_object_dc_source', $field_value);
                        $this->set_common_field_values($object_id, 'object_source', $field_value);
                    elseif ($metadata ['socialdb_entity'] == 'socialdb_object_content'):
                        if (mb_detect_encoding($field_value, 'auto') == 'UTF-8') {
                            $field_value = iconv('ISO-8859-1', 'UTF-8', $field_value);
                        }
                        update_post_meta($object_id, 'socialdb_object_content', $field_value);
                        $this->set_common_field_values($object_id, 'object_content', $field_value); elseif ($metadata['socialdb_entity'] == 'socialdb_object_dc_type'):
                        update_post_meta($object_id, 'socialdb_object_dc_type', $field_value);
                        $this->set_common_field_values($object_id, 'object_type', $field_value);
                    elseif ($metadata['socialdb_entity'] == 'tag' && $field_value != ''):
                        $fields_value = explode('||', $field_value);
                        foreach ($fields_value as $field_value):
                            $fields[] = explode('::', $field_value);
                        endforeach;
                        foreach ($fields as $fields_value):

                            foreach ($fields_value as $field_value):
                                $this->insert_tag($field_value, $object_id, $data['collection_id']);
                            endforeach;
                        endforeach;
                    elseif (strpos($metadata ['socialdb_entity'], "dataproperty_") !== false):
                        $trans = array("dataproperty_" => "");
                        $id = strtr($metadata['socialdb_entity'], $trans);
                        $has_inserted = add_post_meta($object_id, 'socialdb_property_' . $id, $field_value);
                        if (!$has_inserted) {
                            $final_test = add_post_meta($object_id, 'socialdb_property_' . $id, utf8_encode($field_value));
                        }
                        $this->set_common_field_values($object_id, "socialdb_property_$id", $field_value);
                    endif;
                }
            } if (mb_detect_encoding($content, 'auto') == 'UTF-8') {
                $content = iconv('ISO-8859-1', 'UTF-8', $content);
            } update_post_meta($object_id, 'socialdb_object_from', 'external');
            $this->set_common_field_values($object_id, 'object_from', 'external');
            update_post_content($object_id, $content);
            $this->set_common_field_values($object_id, 'description', $content);
            socialdb_add_tax_terms($object_id, $categories, 'socialdb_category_type');
            $content = '';
//$time_after_insert = microtime() - $time_start;
//var_dump(' Fim da insercao do item',$time_after_insert);
        endfor;
    }
    
    public function add_thumbnail_item_zip($dir_created,$object_id) {
        if(is_file($dir_created)){
            $thumbnail_id = $this->insert_attachment_file($dir_created, $object_id);
            set_post_thumbnail($object_id, $thumbnail_id);
        }
    }

}
