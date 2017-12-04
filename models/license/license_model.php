<?php

include_once (dirname(__FILE__) . '/../../../../../wp-config.php');
include_once (ABSPATH . '/wp-load.php');
include_once (ABSPATH . '/wp-includes/wp-db.php');

class LicenseModel {

    public function get_license_to_edit($license_id) {
        $object_post = get_post($license_id);
        $data_license['id'] = $object_post->ID;
        $data_license['nome'] = $object_post->post_title;
        $data_license['description'] = $object_post->post_content;
        $data_license['url'] = get_post_meta($object_post->ID, 'socialdb_custom_license_url', true);

        return $data_license;
    }

    public function edit_repository_license($data) {
        $post = array(
            'ID' => $data['editLicenseId'],
            'post_title' => $data['add_license_name'],
            'post_content' => $data['add_license_description'],
            'post_status' => 'publish',
            'post_type' => 'socialdb_license'
        );
        wp_update_post($post);
        update_post_meta($data['editLicenseId'], 'socialdb_custom_license_url', $data['add_license_url']);

        $result['title'] = __('Success', 'tainacan');
        $result['msg'] = __('Edit successfully', 'tainacan');
        $result['type'] = 'success';

        return $result;
    }

    public function delete_repository_license($id, $collection_id) {
        //DELETAR
        wp_delete_post($id);
        delete_post_meta($collection_id, 'socialdb_collection_license', $id);

        $result['title'] = __('Success', 'tainacan');
        $result['msg'] = __('Delete successfully', 'tainacan');
        $result['type'] = 'success';

        return $result;
    }

    public function change_pattern_license($id, $collection_id) {
        $old_license = get_post_meta($collection_id, 'socialdb_collection_license_pattern', true);
        if ($id == $old_license):
            update_post_meta($collection_id, 'socialdb_collection_license_pattern', '');
            $result['uncheck'] = true;
        else:
            update_post_meta($collection_id, 'socialdb_collection_license_pattern', $id);
            $result['uncheck'] = false;
        endif;

        $result['title'] = __('Success', 'tainacan');
        $result['msg'] = __('Change pattern successfully', 'tainacan');
        $result['type'] = 'success';

        return $result;
    }

    public function change_enabled_license($data) {
        parse_str($data['form_data'], $arr);
        $enabledLicenses = serialize($arr['enabledLicense']);
        update_post_meta($data['collection_id'], 'socialdb_collection_license_enabled', $enabledLicenses);

        $result['title'] = __('Success', 'tainacan');
        $result['msg'] = __('Change successfully saved', 'tainacan');
        $result['type'] = 'success';

        return $result;
    }

    public function get_repository_licenses($collection_id) {
        $arrLicenses = get_option('socialdb_standart_licenses');
        $pattern = get_post_meta($collection_id, 'socialdb_collection_license_pattern');
        if (isset($arrLicenses) && !empty($arrLicenses)) {
            foreach ($arrLicenses as $license) {
                $object_post = get_post($license);
                $data_license['id'] = $object_post->ID;
                $data_license['nome'] = $object_post->post_title;

                $data['licenses'][] = $data_license;
            }
        }
        $arrLicenses_custom = get_option('socialdb_custom_licenses');
        if (isset($arrLicenses_custom) && !empty($arrLicenses_custom)) {
            foreach ($arrLicenses_custom as $license) {
                $object_post = get_post($license);
                $data_license['id'] = $object_post->ID;
                $data_license['nome'] = $object_post->post_title;

                $data['licenses'][] = $data_license;
            }
        }
        $data['pattern'] = $pattern;

        return $data;
    }

    public function get_custom_licenses($collection_id) {
        $collection_meta = get_post_meta($collection_id, 'socialdb_collection_license');
        $pattern = get_post_meta($collection_id, 'socialdb_collection_license_pattern');
        $enabled = unserialize(get_post_meta($collection_id, 'socialdb_collection_license_enabled')[0]);
        if ($collection_meta):
            foreach ($collection_meta as $meta):
                if ($meta):
                    $object_post = get_post($meta);
                    $data_license['id'] = $object_post->ID;
                    $data_license['nome'] = $object_post->post_title;

                    $data['licenses'][] = $data_license;
                endif;
            endforeach;
        endif;
        $data['pattern'] = $pattern;

        if ($enabled) {
            foreach ($enabled as $en) {
                $data['enabled'][] = $en;
            }
        }

        return $data;
    }

    public function verify_equal_license_title($title, $collection_id) {
        $arrStandartLicenses = get_option('socialdb_standart_licenses');
        $arrCustomLicenses = get_option('socialdb_custom_licenses');
        foreach ($arrStandartLicenses as $license) {
            $object_post = get_post($license);
            if ($object_post->post_title == $title) {
                return false;
            }
        }
        if ($arrCustomLicenses) {
            foreach ($arrCustomLicenses as $license) {
                $object_post = get_post($license);
                if ($object_post->post_title == $title) {
                    return false;
                }
            }
        }

        $collection_meta = get_post_meta($collection_id, 'socialdb_collection_license');
        if ($collection_meta):
            foreach ($collection_meta as $meta):
                $object_post = get_post($meta);
                if ($object_post->post_title == $title) {
                    return false;
                }
            endforeach;
        endif;

        return true;
    }

    public function insert_custom_license($data) {
        $getLicenses = get_post_meta($data['collection_id'], 'socialdb_collection_license');

        $post = array(
            'post_title' => $data['add_license_name'],
            'post_content' => $data['add_license_description'],
            'post_status' => 'publish',
            'post_type' => 'socialdb_license'
        );
        $object_id = wp_insert_post($post);
        if ($data['add_license_url'] != '') {
            add_post_meta($object_id, 'socialdb_custom_license_url', $data['add_license_url']);
        }
        wp_set_object_terms($object_id, array((int) get_term_by('slug', 'socialdb_license_custom', 'socialdb_license_type')->term_id), 'socialdb_license_type');

        add_post_meta($data['collection_id'], 'socialdb_collection_license', $object_id);

        $result['title'] = __('Success', 'tainacan');
        $result['msg'] = __('Registered successfully', 'tainacan');
        $result['type'] = 'success';

        return $result;
    }

}

?>