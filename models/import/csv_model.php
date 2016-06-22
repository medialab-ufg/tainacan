<?php

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../property/property_model.php');
require_once(dirname(__FILE__) . '../../category/category_model.php');
require_once(dirname(__FILE__) . '../../mapping/mapping_model.php');

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
                                $content .= $field_value . ",";
                            elseif ($metadata['socialdb_entity'] == 'post_permalink'):
                                update_post_meta($object_id, 'socialdb_object_dc_source', $field_value);
                                $this->set_common_field_values($object_id, 'object_source', $field_value);
                            elseif ($metadata['socialdb_entity'] == 'socialdb_object_content'):
                                if (mb_detect_encoding($field_value, 'auto') == 'UTF-8') {
                                    $field_value = iconv('ISO-8859-1', 'UTF-8', $field_value);
                                }
                                update_post_meta($object_id, 'socialdb_object_content', $field_value);
                                $this->set_common_field_values($object_id, 'object_content', $field_value);
                            elseif ($metadata['socialdb_entity'] == 'socialdb_object_dc_type'):
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
                                    if (strpos($fields_value, '||') !== false) {
                                        $fields_value = explode('||', $fields_value);
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
                                        $this->insert_hierarchy($field_value, $object_id, $data['collection_id'], $parent, $property_id);
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

    public function insert_hierarchy($metadata, $object_id, $collection_id, $parent = 0, $property_id = null) {
        $array = array();
        $categories = explode('::', $metadata);
        foreach ($categories as $category) {
            $array = $this->insert_category($category, $object_id, $collection_id, $parent);
            $this->concatenate_commom_field_value($object_id, "socialdb_propertyterm_$property_id", $array['term_id']);
            $parent = $array['term_id'];
        }
        socialdb_add_tax_terms($object_id, array($array['term_id']), 'socialdb_category_type');
    }

}
