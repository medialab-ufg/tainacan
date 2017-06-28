<?php

if (isset($_GET['by_function'])) {
    include_once (WORDPRESS_PATH . '/wp-config.php');
    include_once (WORDPRESS_PATH . '/wp-load.php');
    include_once (WORDPRESS_PATH . '/wp-includes/wp-db.php');
} else {
    include_once (dirname(__FILE__) . '/../../../../../wp-config.php');
    include_once (dirname(__FILE__) . '/../../../../../wp-load.php');
    include_once (dirname(__FILE__) . '/../../../../../wp-includes/wp-db.php');
}

require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../property/property_model.php');
require_once(dirname(__FILE__) . '../../mapping/mapping_model.php');

class CollectionModel extends Model {

    public function __construct() {
        //  $this->propertymodel = new PropertyModel();
    }

    /**
     * function simple_add($data)
     * @param mix $data  O id do colecao
     * @return json  
     * 
     * Autor: Eduardo Humberto 
     */
    public function add($data) {
        $post = array(
            'post_title' => $data['collection_name'],
            'post_content' => $data['collection_content'],
            'post_status' => 'publish',
            'post_type' => 'ideas'
        );
        $data['ID'] = wp_insert_post($post);
        wp_set_collection_terms($data['ID'], 8, 'category-ideas');
        return json_encode($data);
    }

    /**
     * function simple_add($data)
     * @param mix $data O id do colecao
     * @param string $status O status inicial da coleção em questão
     * @return wp_post  
     * Funcao que insere a colecao apenas com o nome e o tipo de objeto
     * Autor: Eduardo Humberto 
     */
    public function simple_add($data, $status = 'draft') {
        error_reporting(0);
        if ($this->verify_collection($data['collection_name'])) {
            return false;
        }
        $collection = array(
            'post_type' => 'socialdb_collection',
            'post_title' => $data['collection_name'],
            'post_status' => $status,
            'post_author' => get_current_user_id(),
        );
        $collection_id = wp_insert_post($collection);
        $this->update_privacity($collection_id); // atualizando a privacidade da colecao
        $post = get_post($collection_id);
        $this->insert_permissions_default_values($collection_id);
        insert_taxonomy($post->ID, 'socialdb_collection', 'socialdb_collection_type', true); // (Criada em: functions.php) insere a categoria que identifica o tipo da colecao
        //pegando a licaenca padrao do repositorio
        if (get_option('socialdb_pattern_licenses')) {
            update_post_meta($collection_id, 'socialdb_collection_license_pattern', get_option('socialdb_pattern_licenses'));
        }
        //
        $this->createSocialMappingDefault($post->ID);
        // metadado para o nome do objeto da colecao
        update_post_meta($collection_id, 'socialdb_collection_object_name', $data['collection_object']);
        //filtro para o nome da colecao
        if (has_filter('collection_object')) {
            $name = (isset($data['collection_object']) && !empty($data['collection_object']) ? $data['collection_object'] : $data['collection_name']);
            $object_name = apply_filters('collection_object', $name);
        } else {
            $object_name = $data['collection_name'];
        }
        create_root_collection_category($post->ID, $object_name); //(Criada em: functions.php) cria a categoria inicial que identifica os objetos da colecao

        Log::addLog(['collection_id' => $collection_id, 'event_type' => 'user_collection', 'event' => 'add']);
        return $post->ID;
    }

    /**
     * 
     * @param type $collection_id
     */
    public function createSocialMappingDefault($collection_id) {
        /*         * *** MAPEAMENTO PADRAO DUBLIN CORE * */
        $mapping_model_dc = new MappingModel('socialdb_channel_oaipmhdc');
        $mapping_dc_default_id = $mapping_model_dc->create_mapping(__('Mapping Default', 'tainacan'), $collection_id);
        add_post_meta($mapping_dc_default_id, 'socialdb_channel_oaipmhdc_initial_size', '1');
        add_post_meta($mapping_dc_default_id, 'socialdb_channel_oaipmhdc_mapping', serialize([]));
        update_post_meta($collection_id, 'socialdb_collection_mapping_import_active', $mapping_dc_default_id);
        delete_post_meta($collection_id, 'socialdb_collection_channel');
        /** YOUTUBE * */
        $mapping_model_youtube = new MappingModel('socialdb_channel_youtube');
        $mapping_id_youtube = $mapping_model_youtube->create_mapping('socialdb_channel_youtube', $collection_id);

        $arr_youtube[] = array('tag' => 'title', 'socialdb_entity' => 'post_title');
        $arr_youtube[] = array('tag' => 'description', 'socialdb_entity' => 'post_content');
        $arr_youtube[] = array('tag' => 'url', 'socialdb_entity' => 'post_permalink');
        $arr_youtube[] = array('tag' => 'content', 'socialdb_entity' => 'socialdb_object_content');
        $arr_youtube[] = array('tag' => 'source', 'socialdb_entity' => 'socialdb_object_dc_source');
        $arr_youtube[] = array('tag' => 'type', 'socialdb_entity' => 'socialdb_object_dc_type');

        add_post_meta($mapping_id_youtube, 'socialdb_channel_youtube_mapping', serialize($arr_youtube));
        add_post_meta($mapping_id_youtube, 'socialdb_channel_youtube_collection_id', $collection_id);
        add_post_meta($mapping_id_youtube, 'socialdb_channel_youtube_inserted_ids', serialize(array()));

        /** FACEBOOK * */
        $mapping_model_facebook = new MappingModel('socialdb_channel_facebook');
        $mapping_id_fb = $mapping_model_facebook->create_mapping('socialdb_channel_facebook', $collection_id);

        $arr_fb[] = array('tag' => 'id', 'socialdb_entity' => 'post_title');
        $arr_fb[] = array('tag' => 'name', 'socialdb_entity' => 'post_content');
        $arr_fb[] = array('tag' => 'link', 'socialdb_entity' => 'post_permalink');
        $arr_fb[] = array('tag' => 'content', 'socialdb_entity' => 'socialdb_object_content');
        $arr_fb[] = array('tag' => 'source', 'socialdb_entity' => 'socialdb_object_dc_source');
        $arr_fb[] = array('tag' => 'type', 'socialdb_entity' => 'socialdb_object_dc_type');

        add_post_meta($mapping_id_fb, 'socialdb_channel_facebook_mapping', serialize($arr_fb));
        add_post_meta($mapping_id_fb, 'socialdb_channel_facebook_collection_id', $collection_id);

        /** INSTAGRAM * */
        $mapping_model_instagram = new MappingModel('socialdb_channel_instagram');
        $mapping_id_instagram = $mapping_model_instagram->create_mapping('socialdb_channel_instagram', $collection_id);

        $arr_instagram[] = array('tag' => 'id', 'socialdb_entity' => 'post_title');
        $arr_instagram[] = array('tag' => 'caption', 'socialdb_entity' => 'post_content');
        $arr_instagram[] = array('tag' => 'link', 'socialdb_entity' => 'post_permalink');
        $arr_instagram[] = array('tag' => 'content', 'socialdb_entity' => 'socialdb_object_content');
        $arr_instagram[] = array('tag' => 'source', 'socialdb_entity' => 'socialdb_object_dc_source');
        $arr_instagram[] = array('tag' => 'type', 'socialdb_entity' => 'socialdb_object_dc_type');

        add_post_meta($mapping_id_instagram, 'socialdb_channel_instagram_mapping', serialize($arr_instagram));
        add_post_meta($mapping_id_instagram, 'socialdb_channel_instagram_collection_id', $collection_id);

        /** FLICKR * */
        $mapping_model_flickr = new MappingModel('socialdb_channel_flickr');
        $mapping_id_flickr = $mapping_model_flickr->create_mapping('socialdb_channel_flickr', $collection_id);

        $arr_flickr[] = array('tag' => 'title', 'socialdb_entity' => 'post_title');
        $arr_flickr[] = array('tag' => 'description', 'socialdb_entity' => 'post_content');
        $arr_flickr[] = array('tag' => 'url', 'socialdb_entity' => 'post_permalink');
        $arr_flickr[] = array('tag' => 'content', 'socialdb_entity' => 'socialdb_object_content');
        $arr_flickr[] = array('tag' => 'source', 'socialdb_entity' => 'socialdb_object_dc_source');
        $arr_flickr[] = array('tag' => 'type', 'socialdb_entity' => 'socialdb_object_dc_type');

        add_post_meta($mapping_id_flickr, 'socialdb_channel_flickr_mapping', serialize($arr_flickr));
        add_post_meta($mapping_id_flickr, 'socialdb_channel_flickr_collection_id', $collection_id);

        /** VIMEO * */
        $mapping_model_vimeo = new MappingModel('socialdb_channel_vimeo');
        $mapping_id_vimeo = $mapping_model_vimeo->create_mapping('socialdb_channel_vimeo', $collection_id);

        $arr_vimeo[] = array('tag' => 'name', 'socialdb_entity' => 'post_title');
        $arr_vimeo[] = array('tag' => 'description', 'socialdb_entity' => 'post_content');
        $arr_vimeo[] = array('tag' => 'link', 'socialdb_entity' => 'post_permalink');
        $arr_vimeo[] = array('tag' => 'content', 'socialdb_entity' => 'socialdb_object_content');
        $arr_vimeo[] = array('tag' => 'source', 'socialdb_entity' => 'socialdb_object_dc_source');
        $arr_vimeo[] = array('tag' => 'type', 'socialdb_entity' => 'socialdb_object_dc_type');

        add_post_meta($mapping_id_vimeo, 'socialdb_channel_vimeo_mapping', serialize($arr_vimeo));
        add_post_meta($mapping_id_vimeo, 'socialdb_channel_vimeo_collection_id', $collection_id);
    }

    /**
     * function insert_meta_default_values()
     * @param int id  O id da categoria criada
     * Funcao que da os valores por default as categorias
     * Autor: Eduardo Humberto 
     */
    function insert_permissions_default_values($collection_id) {
        update_post_meta($collection_id, 'socialdb_collection_permission_create_category', 'members');
        update_post_meta($collection_id, 'socialdb_collection_permission_edit_category', 'approval');
        update_post_meta($collection_id, 'socialdb_collection_permission_delete_category', 'approval');
        update_post_meta($collection_id, 'socialdb_collection_permission_add_classification', 'members');
        update_post_meta($collection_id, 'socialdb_collection_permission_delete_classification', 'approval');
        update_post_meta($collection_id, 'socialdb_collection_permission_create_object', 'members');
        update_post_meta($collection_id, 'socialdb_collection_permission_delete_object', 'approval');
        update_post_meta($collection_id, 'socialdb_collection_permission_create_property_data', 'members');
        update_post_meta($collection_id, 'socialdb_collection_permission_edit_property_data', 'approval');
        update_post_meta($collection_id, 'socialdb_collection_permission_delete_property_data', 'approval');
        update_post_meta($collection_id, 'socialdb_collection_permission_edit_property_data_value', 'approval');
        update_post_meta($collection_id, 'socialdb_collection_permission_create_property_object', 'members');
        update_post_meta($collection_id, 'socialdb_collection_permission_edit_property_object', 'approval');
        update_post_meta($collection_id, 'socialdb_collection_permission_delete_property_object', 'approval');
        update_post_meta($collection_id, 'socialdb_collection_permission_edit_property_object_value', 'approval');
        update_post_meta($collection_id, 'socialdb_collection_permission_create_property_term', 'members');
        update_post_meta($collection_id, 'socialdb_collection_permission_edit_property_term', 'approval');
        update_post_meta($collection_id, 'socialdb_collection_permission_delete_property_term', 'approval');
        //Permissions Comments
        update_post_meta($collection_id, 'socialdb_collection_permission_create_comment', 'member');
        update_post_meta($collection_id, 'socialdb_collection_permission_edit_comment', 'approval');
        update_post_meta($collection_id, 'socialdb_collection_permission_delete_comment', 'approval');
        //Permissions Comments
        update_post_meta($collection_id, 'socialdb_collection_permission_create_tags', 'member');
        update_post_meta($collection_id, 'socialdb_collection_permission_edit_tags', 'approval');
        update_post_meta($collection_id, 'socialdb_collection_permission_delete_tags', 'approval');

        return true;
    }

    public function edit($data) {
        $array_json = [];
        $post = get_post($post_id);
        $array_json['collection_name'] = $post->post_title;
        return json_encode($array_json);
    }

    /**
     * function update($data)
     * @param mix $data  Os dados que serao utilizados para atualizar a colecao
     * @return json com os dados atualizados 
     * metodo que atualiza os dados da colecao
     * Autor: Eduardo Humberto 
     */
    public function update($data) {
        $post = array(
            'ID' => $data['collection_id'],
            'post_title' => $data['collection_name'],
            'post_content' => $data['collection_content'],
            'post_status' => 'publish',
            'post_type' => 'socialdb_collection',
            'post_name' => $data['socialdb_collection_address']
        );

        if (isset($data['collection_owner'])) {
            $post["post_author"] = $data['collection_owner'];
        }

        $post_id = wp_update_post($post);
        //verificando se existe aquivos para ser incluidos
        if ($data['remove_cover']) {
            $cover_id = get_post_meta($post_id, 'socialdb_collection_cover_id', true);
            wp_delete_attachment($cover_id);
            delete_post_meta($post_id, 'socialdb_collection_cover_id');
        }
        if ($data['remove_watermark']) {
            $watermark_id = get_post_meta($post_id, 'socialdb_collection_watermark_id', true);
            wp_delete_attachment($watermark_id);
            delete_post_meta($post_id, 'socialdb_collection_watermark_id');
        }
        if ($data['remove_thumbnail']) {
            delete_post_thumbnail($post_id);
        }

        if ($data['enable_header']) {
            update_post_meta($post_id, 'socialdb_collection_show_header', 'enabled');
        } else {
            update_post_meta($post_id, 'socialdb_collection_show_header', 'disabled');
        }

        Log::addLog(['collection_id' => $data['collection_id'], 'event_type' => 'user_collection', 'event' => 'edit']);

        if ($_FILES) {
            $this->add_thumbnail($post_id);
            $id_cover = $this->add_cover($post_id);
            $id_watermark = $this->add_watermark($post_id);

            if ($id_cover) {
                update_post_meta($post_id, 'socialdb_collection_cover_id', $id_cover);
            }
            if ($id_watermark) {
                update_post_meta($post_id, 'socialdb_collection_watermark_id', $id_watermark);
            }
        }
        if ($data['add_watermark']) {
            update_post_meta($post_id, 'socialdb_collection_add_watermark', true);
        } else {
            update_post_meta($post_id, 'socialdb_collection_add_watermark', false);
        }

        if ($data['collection_moderators'] && is_array($data['collection_moderators'])) {
            delete_post_meta($post_id, 'socialdb_collection_moderator');
            foreach ($data['collection_moderators'] as $moderator) {
                add_post_meta($post_id, 'socialdb_collection_moderator', $moderator);
            }
        } else {
            delete_post_meta($post_id, 'socialdb_collection_moderator');
        }
        if ($data['socialdb_collection_parent'] != '' && ($data['socialdb_collection_parent'] != get_post_meta($post_id, 'socialdb_collection_parent', true))) {
            $old_parent = get_post_meta($post_id, 'socialdb_collection_parent', true);
            if ($old_parent && $old_parent != '' && get_term_by('id', $old_parent, 'socialdb_category_type')) {
                //$this->exclude_data_parent_removed($old_parent, $data['collection_id']);
            }

            if ($data['socialdb_collection_parent'] == 'collection_root') {
                $data['socialdb_collection_parent'] = '0';
                $category_root_id = $this->get_category_root_of($data['collection_id']);
                $update_category = wp_update_term($category_root_id, 'socialdb_category_type', array(
                    'parent' => get_term_by('name', 'socialdb_category', 'socialdb_category_type')->term_id
                ));
                update_post_meta($post_id, 'socialdb_collection_parent', $data['socialdb_collection_parent']);
            } else {
                $category_root_id = $this->get_category_root_of($data['collection_id']);
                $move_to = get_term_by('id', $data['socialdb_collection_parent'], 'socialdb_category_type');
                if ($move_to && !is_wp_error($move_to)) {
                    $update_category = wp_update_term($category_root_id, 'socialdb_category_type', array(
                        'parent' => $move_to->term_id
                    ));
                    update_post_meta($post_id, 'socialdb_collection_parent', $data['socialdb_collection_parent']);
                    //$this->extend_collection($data['collection_id'], $move_to->term_id);
                }
            }
        }

        if ($data['socialdb_collection_moderation_type'] == 'democratico') {
            update_post_meta($post_id, 'socialdb_collection_moderation_days', $data['socialdb_collection_moderation_days']);
        }

        update_post_meta($post_id, 'socialdb_collection_moderation_type', $data['socialdb_collection_moderation_type']);
        update_post_meta($post_id, 'socialdb_collection_object_name', $data['socialdb_collection_object_name']);
        update_post_meta($post_id, 'socialdb_collection_hide_tags', $data['socialdb_collection_hide_tags']);
        update_post_meta($post_id, 'socialdb_collection_attachment', $data['collection_attachments']);
        update_post_meta($post_id, 'socialdb_collection_show_labels', $data['collection_show_labels']);
        update_post_meta($post_id, 'socialdb_collection_most_participatory', $data['collection_most_participatory']);
        update_post_meta($post_id, 'socialdb_collection_address', $data['socialdb_collection_address']);
        update_post_meta($post_id, 'socialdb_collection_allow_hierarchy', $data['socialdb_collection_allow_hierarchy']);
        update_post_meta($post_id, 'socialdb_collection_download_control', $data['socialdb_collection_download_control']);

        //Permissions
        update_post_meta($post_id, 'socialdb_collection_permission_create_category', $data['socialdb_collection_permission_create_category']);
        update_post_meta($post_id, 'socialdb_collection_permission_edit_category', $data['socialdb_collection_permission_edit_category']);
        update_post_meta($post_id, 'socialdb_collection_permission_delete_category', $data['socialdb_collection_permission_delete_category']);
        update_post_meta($post_id, 'socialdb_collection_permission_add_classification', $data['socialdb_collection_permission_add_classification']);
        update_post_meta($post_id, 'socialdb_collection_permission_delete_classification', $data['socialdb_collection_permission_delete_classification']);
        update_post_meta($post_id, 'socialdb_collection_permission_create_object', $data['socialdb_collection_permission_create_object']);
        update_post_meta($post_id, 'socialdb_collection_permission_delete_object', $data['socialdb_collection_permission_delete_object']);
        update_post_meta($post_id, 'socialdb_collection_permission_create_property_data', $data['socialdb_collection_permission_create_property_data']);
        update_post_meta($post_id, 'socialdb_collection_permission_edit_property_data', $data['socialdb_collection_permission_edit_property_data']);
        update_post_meta($post_id, 'socialdb_collection_permission_delete_property_data', $data['socialdb_collection_permission_delete_property_data']);
        update_post_meta($post_id, 'socialdb_collection_permission_edit_property_data_value', $data['socialdb_collection_permission_edit_property_data_value']);
        update_post_meta($post_id, 'socialdb_collection_permission_create_property_object', $data['socialdb_collection_permission_create_property_object']);
        update_post_meta($post_id, 'socialdb_collection_permission_edit_property_object', $data['socialdb_collection_permission_edit_property_object']);
        update_post_meta($post_id, 'socialdb_collection_permission_delete_property_object', $data['socialdb_collection_permission_delete_property_object']);
        update_post_meta($post_id, 'socialdb_collection_permission_edit_property_object_value', $data['socialdb_collection_permission_edit_property_object_value']);
        update_post_meta($post_id, 'socialdb_collection_permission_create_property_term', $data['socialdb_collection_permission_create_property_term']);
        update_post_meta($post_id, 'socialdb_collection_permission_edit_property_term', $data['socialdb_collection_permission_edit_property_term']);
        update_post_meta($post_id, 'socialdb_collection_permission_delete_property_term', $data['socialdb_collection_permission_delete_property_term']);
        //Permissions Comments
        update_post_meta($post_id, 'socialdb_collection_permission_create_comment', $data['socialdb_collection_permission_create_comment']);
        update_post_meta($post_id, 'socialdb_collection_permission_edit_comment', $data['socialdb_collection_permission_edit_comment']);
        update_post_meta($post_id, 'socialdb_collection_permission_delete_comment', $data['socialdb_collection_permission_delete_comment']);
        //Permissions Tags
        update_post_meta($post_id, 'socialdb_collection_permission_create_tags', $data['socialdb_collection_permission_create_tags']);
        update_post_meta($post_id, 'socialdb_collection_permission_edit_tags', $data['socialdb_collection_permission_edit_tags']);
        update_post_meta($post_id, 'socialdb_collection_permission_delete_tags', $data['socialdb_collection_permission_delete_tags']);
        $data['collection_id'] = $post_id;
        if (has_action('update_collection_configuration')) {
            do_action('update_collection_configuration', $data);
        }
        $this->update_privacity($post_id, $data['collection_privacy']);
        return json_encode($data);
    }

    /**
     * function extend_collection($data)
     * @param int $collection_id  O id da colecao que sera inserido as propriedades
     * @param int $parent_collection_root_id  O id da colecao que sera extendida
     * @return json com os dados atualizados 
     * @description metodo que insere as propriedades de um colecao extendida
     * Autor: Eduardo Humberto 
     */
    public function extend_collection($collection_id, $parent_collection_root_id) {
        $properties_to_add = get_term_meta($parent_collection_root_id, 'socialdb_category_property_id');
        $category_root_id = $this->get_category_root_of($collection_id);
        $collection_properties = get_term_meta($collection_id, 'socialdb_category_property_id');
        if (!$collection_properties) {
            delete_term_meta($category_root_id, 'socialdb_category_property_id');
        }
        if ($properties_to_add && is_array($collection_properties)) {
            foreach ($properties_to_add as $property_to_add) {
                if (!in_array($property_to_add, $collection_properties)) {
                    $this->vinculate_property($category_root_id, $property_to_add);
                    $is_facet = get_term_meta($property_to_add, 'socialdb_property_object_is_facet', true);
                    if ($is_facet && $is_facet == 'true') {
                        add_post_meta($collection_id, 'socialdb_collection_facet_' . $property_to_add . '_color', 'color_property1');
                    }
                    //possivelmente um problema
                    $this->vinculate_objects_with_property($property_to_add, $collection_id, $category_root_id);
                }
            }
        }
    }

    /**
     * function exclude_data_parent_removed($parent_removed_category_id,$collection_id)
     * @param mix $parent_removed_category_id O id da categoria raiz da colecao pai
     * @param mix $collection_id  O id da colecao
     * @return void
     * metodo que retira as propriedades da colecao que foram inseridos por ela ter extendido um dada colecao
     * Autor: Eduardo Humberto 
     */
    public function exclude_data_parent_removed($parent_removed_category_id, $collection_id) {
        $properties_to_remove = get_term_meta($parent_removed_category_id, 'socialdb_category_property_id');
        $category_root_id = $this->get_category_root_of($collection_id);
        $collection_properties = get_term_meta($category_root_id, 'socialdb_category_property_id');
        if ($properties_to_remove && is_array($properties_to_remove)) {
            foreach ($properties_to_remove as $property_to_remove) {
                $category_property_id = get_term_meta($property_to_remove, 'socialdb_property_created_category', true);
                if (!$this->is_repository_property($category_property_id) && in_array($property_to_remove, $collection_properties) // se nao for propriedade do repositorio, se estiver no array de propriedades do pai anterior e a categoria root de onde foi criada nao for a atual
                        && $category_property_id != $category_root_id) {
                    delete_term_meta($category_root_id, 'socialdb_category_property_id', $property_to_remove);
                }
            }
        }
    }

    public function update_privacity($collection_id, $privacity = 'public') {
        $private_collections = get_option('socialdb_private_collections');
        $private_collections = ($private_collections) ?  unserialize($private_collections) : [];
        if ($privacity == 'public') {
            $type = get_term_by('name', 'socialdb_collection_public', 'socialdb_collection_type');
            wp_set_post_terms($collection_id, array($type->term_id), 'socialdb_collection_type');
            if($private_collections && is_array($private_collections) && isset($private_collections[$collection_id])){
                unset($private_collections[$collection_id]);
            }
        } else {
            if(!isset($private_collections[$collection_id])){
                $private_collections[$collection_id] = $this->get_category_root_of($collection_id);
            }
            $type = get_term_by('name', 'socialdb_collection_private', 'socialdb_collection_type');
            wp_set_post_terms($collection_id, array($type->term_id), 'socialdb_collection_type');
        }
        update_option('socialdb_private_collections', serialize($private_collections));
    }

    public function delete($data) {
        if (wp_delete_post($data['collection_id'])) {
            $data['title'] = __('Success', 'tainacan');
            $data['msg'] = __('Collection removed successfully', 'tainacan');
            $data['type'] = 'success';
        } else {
            $data['title'] = __('Attention', 'tainacan');
            $data['msg'] = __('Collection not exists anymore', 'tainacan');
            $data['type'] = 'error';
        }
        $data['url'] = get_the_permalink(get_option('collection_root_id'));
        return json_encode($data);
    }

    /**
     * function get_facets($collection_id)
     * @param int $collection_id
     * @return array int 
     * 
     * metodo responsavel em retornar as facetas da colecao
     * Autor: Eduardo Humberto 
     */
    public static function get_facets($collection_id) {
        $array = get_post_meta($collection_id, 'socialdb_collection_facets');
        if (is_array($array)) {
            return array_filter(array_unique($array));
        } else {
            return false;
        }
    }

    /**
     * function get_facets($collection_id)
     * @param int $collection_id
     * @return array int 
     * 
     * metodo responsavel em retornar as facetas da colecao
     * Autor: Eduardo Humberto 
     */
    public function get_property_facets($collection_id) {
        $category_root_id = $this->get_category_root_of($collection_id);
        $facets = $this->get_property_object_facets($category_root_id);
        return $facets;
    }

    /**
     * function get_term_facets($collection_id)
     * @param array Array de facetas
     * @return array int 
     * metodo responsavel em retornar as facetas em forma de termos
     * Autor: Eduardo Humberto 
     */
    public static function get_term_facets($array_facets) {
        $facets = [];
        if (is_array($array_facets)) {
            foreach ($array_facets as $array_facet) {
                $facets[] = get_term_by('id', $array_facet, 'socialdb_category_type');
            }
        } else {
            $facets[] = get_term_by('id', $array_facets, 'socialdb_category_type');
        }
        return $facets;
    }

    /**
     * function get_collection_by_user($user_id)
     * @param int user
     * @return array(wp_post) as colecoes do usuario
     * @ metodo responsavel em retornar as colecoes de um determinando usuario
     * @author: Eduardo Humberto 
     */
    public function get_collection_by_user($user_id) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
//        $query_old = "
//                    SELECT * FROM $wp_posts p
//                    WHERE p.post_author = {$user_id} and p.post_type like 'socialdb_collection'
//                    order by p.post_title
//            ";
        $query = "
                    SELECT * FROM $wp_posts p
                    WHERE p.post_type like 'socialdb_collection' AND p.post_status like 'publish' 
                   order by p.post_title
            ";
        $all_collections = $wpdb->get_results($query);
        if ($all_collections && is_array($all_collections) && count($all_collections) > 0) {
            foreach ($all_collections as $collection) {
                if (self::is_moderator($collection->ID, $user_id)) {
                    $result[] = $collection;
                }
            }
        } else {
            $result = array();
        }



        if ($result && is_array($result) && count($result) > 0) {
            return $result;
        } else {
            return array();
        }
    }

    /**
     * function list_ordenation($user_id)
     * @param $data Um array com o id da colecao
     * @return json as propriedades da colecao
     * @ metodo responsavel em retornar as colecoes de um determinando usuario
     * @author: Eduardo Humberto 
     */
    public function list_ordenation($data, $_get_all_meta = false) {
        $data['selected'] = $this->set_default_ordenation($data['collection_id']);
        $category_root = $this->get_category_root_of($data['collection_id']);
        //$all_properties_id = get_term_meta($category_root, 'socialdb_category_property_id');
        $all_properties_id = $this->get_parent_properties($category_root, [], $category_root);
        $recent_property = get_term_by('slug', 'socialdb_ordenation_recent', 'socialdb_property_type');
        $data['general_ordenation'][] = array('id' => $recent_property->term_id, 'name' => $recent_property->name);
        $data['general_ordenation'][] = array('id' => 'comment_count', 'name' => __('Populars', 'tainacan'));
        $data['general_ordenation'][] = array('id' => 'title', 'name' => __('Title', 'tainacan'));
        /*
        if ($data['collection_id'] != get_option('collection_root_id')):
            $data['general_ordenation'][] = array('id' => 'socialdb_object_dc_type', 'name' => __('Type', 'tainacan'));
            $data['general_ordenation'][] = array('id' => 'socialdb_object_from', 'name' => __('Format', 'tainacan'));
            $data['general_ordenation'][] = array('id' => 'socialdb_license_id', 'name' => __('Licenses', 'tainacan'));
        endif;
        */
        if ($all_properties_id && is_array($all_properties_id) && $all_properties_id[0]) {
            foreach ($all_properties_id as $property_id) {
                $property_object = get_term_by('id', $property_id, 'socialdb_property_type');
                $parent_name = PropertyModel::get_property_type($property_id);
                $all_data = $this->get_all_property($property_id, true);
                if (in_array($property_object->slug, $this->fixed_slugs)) {
                    $labels_collection = ($data['collection_id'] != '') ? get_post_meta($data['collection_id'], 'socialdb_collection_fixed_properties_labels', true) : false;
                    if ($labels_collection):
                        $array = unserialize($labels_collection);
                        $property_object->name = (isset($array[$property_object->term_id])) ? $array[$property_object->term_id] : $property_object->name;
                    endif;
                }
                
                if( $this->filter_ordenation($property_object->name, $all_data["type"]) ) {
                    $array = array('id' => $property_object->term_id, 'name' => $property_object->name, 'type' => $all_data['type']);
                    if ($parent_name == 'socialdb_property_data') {
                        // $is_ordenation = get_term_meta($property_object->term_id, 'socialdb_property_data_column_ordenation')[0];
                        // if ($is_ordenation == 'true')
                        $data['property_data'][] = $array;
                    } elseif ($parent_name != 'socialdb_property_term' && isset($parent_name) && $parent_name != 'socialdb_property_object') {
                        $data['rankings'][] = $array;
                    } else if ($_get_all_meta === "true") {
                        if ($parent_name == 'socialdb_property_term') {
                            $data['property_term'][] = $array;
                        } else if ($parent_name == 'socialdb_property_object') {
                            $data['property_object'][] = $array;
                        }
                    }
                }
            }
        }
        return $data;
    }

    private function filter_ordenation($str, $type) {
        $unused_filters = [_t("Source"),_t("Description"), _t("Content"),_t("Thumbnail"),_t("Attachments"),_t("Type"),_t("License")];
        $filter = true;
        if( ("radio" === $type || "textarea" === $type || "file" === $type ) && in_array(_t($str), $unused_filters)) {
            $filter = false;
        }
        return $filter;
    }

    /**
     * function set_default_ordenation($user_id)
     * @param int O id da colecao 
     * @return int O id da propriedade escolhida como ordenacao padrao
     * @author: Eduardo Humberto 
     */
    private function set_default_ordenation($collection_id) {
        $selected = get_post_meta($collection_id, 'socialdb_collection_default_ordering');
        if (isset($selected) && $selected && $selected[0] != '') {
            return $selected[0];
        } else {
            $recent_property = get_term_by('slug', 'socialdb_ordenation_recent', 'socialdb_property_type');
            return $recent_property->term_id;
        }
    }

    /**
     * funcao estatica que verifica se o usuario dado eh dono ou moderador da colecao
     * @param int O id da colecao 
     * @return boolean se pertence aos moderadores da colecao
     * @author: Eduardo Humberto 
     */
    public static function is_moderator($collection_id, $user_id) {
        $owner = get_post($collection_id)->post_author;
        $moderators = get_post_meta($collection_id, 'socialdb_collection_moderator');
        if ($user_id != 0 && ($user_id == $owner || (is_array($moderators) && in_array($user_id, $moderators)))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * funcao estatica que busca moderadores e o dono da colecao
     * @param int O id da colecao 
     * @return array Os ids dos usuarios
     * @author: Eduardo Humberto 
     */
    public static function get_moderators($collection_id) {
        $owners[] = get_post($collection_id)->post_author;
        $moderators = get_post_meta($collection_id, 'socialdb_collection_moderator');
        if ($moderators) {
            foreach ($moderators as $moderator) {
                $owners[] = $moderator;
            }
        }
        return $owners;
    }

    /**
     * funcao que cria o array que sera utilzada para montar o autocomplete
     * @param int O id da colecao 
     * @author: Eduardo Humberto 
     */
    public function create_main_json_autocomplete($collection_id) {
        session_write_close();
        ini_set('max_execution_time', '0');
        $autocomplete_array = array();
        $facets_id = array_filter(array_unique(get_post_meta($collection_id, 'socialdb_collection_facets')));
        foreach ($facets_id as &$facet_id) {
            $term = get_term_by("id", $facet_id, "socialdb_category_type");
            /* $autocomplete_array[] = array(
              'value' => $term->name,
              'link' => '',
              'label' => $term->name,
              'category' => $term->name,
              'id' => $term->term_id ); */
            // $children = $this->get_categories($term->term_id, ARRAY_N);
            $children = $this->getChildren($term->term_id);

            if ((is_array($children)) && count($children) > 0) {
                $this->get_categories_recursive($children, $autocomplete_array, $finding);
            }
        }
        $this->get_properties_autocomplete($collection_id, $autocomplete_array);
        $this->get_tags_autocomplete($collection_id, $autocomplete_array);
        $this->get_collection_objects_autocomplete($this->get_category_root_of($collection_id), $autocomplete_array);


        return $autocomplete_array;
    }

    /**
     * funcao recursiva que busca todas as categorias ate achar a ultima folha
     * @param array $children os filhos de uma categoria categorias
     * @param array $array_autocomplete o array autocomplete onde sera inserido os termos relacionados com a pesquisa
     * @param string $finding a string que esta propcurando ate o momento
     * @author: Eduardo Humberto 
     */
    public function get_categories_recursive($children, &$array_autocomplete) {
        $counter = 0;
        foreach ($children as $child) {
            if (is_array($child)) {
                $child_id = $child[0]->term_id;
            } else {
                $child_id = $child->term_id;
            }
            $term_child = get_term_by('id', $child_id, "socialdb_category_type");
            $parent = get_term_by('id', $term_child->parent, "socialdb_category_type");
            //$parent->name = str_replace('-', ' ', $parent->name);
            $array_autocomplete[] = array(
                'value' => $term_child->name,
                'link' => $parent->name,
                'label' => $parent->name . " > " . $term_child->name,
                'category' => $parent->name,
                'id' => $term_child->term_id);
            $children_of_child = $this->get_categories($term_child->term_id, ARRAY_N);
            if (count($children_of_child) > 0) {
                $this->get_categories_recursive($children_of_child, $array_autocomplete);
            }
            $counter++;
//            if ($counter == 9) {
//                break;
//            }
        }
    }

    /**
     * funcao que busca todas as propriedades para ser montada no autocomplete
     * @param int $collection_id O id da colecao que esa sendo montado o autocomplete
     * @param array $array_autocomplete o array autocomplete onde sera inserido os termos relacionados com a pesquisa
     * @author: Eduardo Humberto 
     */
    public function get_properties_autocomplete($collection_id, &$array_autocomplete) {
        //PROPERTIES
        $propertyModel = new PropertyModel;
        $root_category = $this->get_category_root_of($collection_id);
        $properties = $propertyModel->get_property_object_facets($root_category);
        if ($properties) {
            foreach ($properties as $property) {
                $facet = get_term_by('id', $property['id'], 'socialdb_property_type');
                $array_autocomplete[] = array(
                    'value' => $facet->name,
                    'link' => $facet->name,
                    'label' => $facet->name,
                    'category' => $facet->name,
                    'id' => $facet->term_id);
                if ($property['metas']['socialdb_property_object_category_id'])
                    $this->get_objects_autocomplete($property['id'], $property['metas']['socialdb_property_object_category_id'], $array_autocomplete);
            }
        }
    }

    /**
     * funcao que busca os objeto pertencentes a colecao
     * @param int $property_id o id da propriedade que sera montada no
     * @param array $category_root_collection_related O id da categoria raiz da colecao de onde vai buscar os objetos
     * @param array $array_autocomplete o array autocomplete onde sera inserido os termos relacionados com a pesquisa
     * @author: Eduardo Humberto 
     */
    public function get_objects_autocomplete($property_id, $category_root_collection_related, &$array_autocomplete) {
        $objects = $this->get_category_root_posts($category_root_collection_related);
        $facet = get_term_by('id', $property_id, 'socialdb_property_type');
        if (count($objects) > 0) {
            foreach ($objects as $child) {
                $array_autocomplete[] = array(
                    'value' => $child->post_title,
                    'link' => $child->post_title,
                    'label' => $child->post_title,
                    'category' => $facet->name . ' (' . __('Object Property', 'tainacan') . ')',
                    'id' => $child->ID . '_' . $property_id);
            }
        }
    }

    /**
     * funcao que busca os objeto pertencentes a colecao
     * @param int $property_id o id da propriedade que sera montada no
     * @param array $category_root_id O id da categoria raiz da colecao de onde vai buscar os objetos
     * @param array $array_autocomplete o array autocomplete onde sera inserido os termos relacionados com a pesquisa
     * @author: Eduardo Humberto 
     */
    public function get_collection_objects_autocomplete($category_root_id, &$array_autocomplete) {
        $objects = $this->get_category_root_posts($category_root_id);
        if (count($objects) > 0) {
            foreach ($objects as $child) {
                $array_autocomplete[] = array(
                    'value' => $child->post_title,
                    'link' => $child->post_title,
                    'label' => $child->post_title,
                    'category' => __('Items', 'tainacan'),
                    'id' => $child->post_title . '_keyword');
            }
        }
    }

    /**
     * funcao que busca as tags de uma colecao
     * @param int $collection_id O id da colecao que esa sendo montado o autocomplete
     * @param array $array_autocomplete o array autocomplete onde sera inserido os termos relacionados com a pesquisa
     * @author: Eduardo Humberto 
     */
    public function get_tags_autocomplete($collection_id, &$array_autocomplete) {
        $get_tags = wp_get_object_terms($collection_id, 'socialdb_tag_type');
        if ($get_tags) {
            foreach ($get_tags as $tag) {
                $array_autocomplete[] = array(
                    'value' => $tag->name,
                    'link' => $tag->name,
                    'label' => $tag->name,
                    'category' => __('Tags', 'tainacan'),
                    'id' => $tag->term_id . '_tag');
            }
        }
    }

    /**
     * funcao que busca os autores mais participativos de uma colecao a partir
     * da quantidade de items inseridos
     * @param int $collection_id O id da colecao que esa sendo montado o autocomplete
     * @author: Eduardo Humberto 
     */
    public function get_most_participatory_authors($collection_id) {
        global $wpdb;
        $category_root_id = $this->get_category_root_of($collection_id);
        $wp_term_relationships = $wpdb->prefix . "term_relationships";
        $wp_posts = $wpdb->prefix . "posts";
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $wp_users = $wpdb->base_prefix . "users";
        $query = "
	SELECT p.post_author, u.display_name, u.user_nicename, count(*) AS num_posts
	FROM $wp_posts p
		INNER JOIN $wp_term_relationships tr ON p.ID = tr.object_id
		INNER JOIN $wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
		INNER JOIN $wp_users u ON p.post_author = u.ID
	WHERE tt.term_id = $category_root_id
	GROUP BY p.post_author
	ORDER BY num_posts DESC
	LIMIT 10";

        $authors = $wpdb->get_results($query);
        return $authors;
    }

    /**
     * funcao que busca os autores mais participativos de uma colecao a partir
     * da quantidade de eventos criados na colecao
     * @param int $collection_id O id da colecao que esa sendo montado o autocomplete
     * @author: Eduardo Humberto 
     */
    public function get_most_colaborators_authors($collection_id) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $wp_users = $wpdb->base_prefix . "users";
        $query = "
	SELECT Distinct p.ID,p.post_author, u.display_name, u.user_nicename, count(*) AS num_posts
	FROM $wp_posts p
		INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id
		INNER JOIN $wp_users u ON p.post_author = u.ID
	WHERE p.post_type LIKE 'socialdb_event'
        and pm.meta_key LIKE 'socialdb_event_collection_id' and  pm.meta_value LIKE '$collection_id'
	GROUP BY p.post_author
	ORDER BY num_posts DESC
	LIMIT 10";
        $authors = $wpdb->get_results($query);
        return $authors;
    }

    /**
     * funcao que busca as propriedades de acategoria para montar na ordenacao
     * @param array $data Os dados vindos do formulario
     * @author: Eduardo Humberto 
     */
    public function get_order_category_properties($data) {
        $categoryModel = new CategoryModel;
        $all_properties_id = array();
        $already_id = array();
        $filters = explode(',', $data['categories']);
        $facets = CollectionModel::get_facets($data['collection_id']);
        $filters = array_merge($filters, $facets);
        foreach ($filters as $filter) {
            if (strpos($filter, '_') === false && $filter != $this->get_category_root_of($data['collection_id'])) {
                $all_properties_id = $categoryModel->get_parent_properties($filter, $all_properties_id);
            }
        }
        if ($all_properties_id && is_array($all_properties_id) && $all_properties_id[0]) {
            foreach ($all_properties_id as $property_id) {
                $property_object = get_term_by('id', $property_id, 'socialdb_property_type');
                $parent_name = PropertyModel::get_property_type($property_id);
                $array = array('id' => $property_object->term_id, 'name' => $property_object->name);
                if ($parent_name == 'socialdb_property_data') {
                    $is_ordenation = get_term_meta($property_object->term_id, 'socialdb_property_data_column_ordenation')[0];
                    if ($is_ordenation == 'true' && !in_array($property_object->term_id, $already_id)) {
                        $already_id[] = $property_object->term_id;
                        $data['property_data'][] = $array;
                    }
                }
            }
        }
        $data['names']['data_property'] = __('Property Data Categories', 'tainacan');
        return json_encode($data);
    }

    // Verifica a privacidade da coleção para aprovação de acesso
    public function check_privacity($data) {
        $result = array();
        $get_privacity = wp_get_object_terms($data['collection_id'], 'socialdb_collection_type');
        if ($get_privacity) {
            foreach ($get_privacity as $privacity) {
                $privacity_name = $privacity->name;
            }
        }

        $moderator = CollectionModel::is_moderator($data['collection_id'], get_current_user_id());

        if ($privacity_name == 'socialdb_collection_public' || current_user_can('manage_options')) {
            $result['privacity'] = true;
        } elseif ($privacity_name == 'socialdb_collection_private') {
            if ($moderator) {
                $result['privacity'] = true;
            } else {
                $result['privacity'] = false;
                $result['title'] = __('Attention', 'tainacan');
                $result['msg'] = __('You are not allowed to access this collection!', 'tainacan');
                $result['url'] = get_the_permalink(get_option('collection_root_id')); // . '?mycollections=true'
            }
        }

        return json_encode($result);
    }

    /**
     * function verify_collection()
     * @param array Os dados vindo do formulario
     * @return json com o id e o nome de cada colecao
     * @author Eduardo Humberto
     */
    public function verify_collection($name) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $query = "
                        SELECT p.* FROM $wp_posts p
                        WHERE p.post_type like 'socialdb_collection' and p.post_status like 'publish' and p.post_title LIKE '{$name}'
                ";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result) && count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * function verify_collection()
     * @param array Os dados vindo do formulario
     * @return json com o id e o nome de cada colecao
     * @author Eduardo Humberto
     */
    public function verify_all_collection($name) {
        $name = trim($name);
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $query = "
                        SELECT p.* FROM $wp_posts p
                        WHERE p.post_type like 'socialdb_collection' and p.post_name LIKE '{$name}'
                ";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result) && count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * function verify_name_collection()
     * @param array Os dados vindo do formulario
     * @return json com o id e o nome de cada colecao
     * @author Eduardo Humberto
     */
    public function verify_name_collection($data) {
        $data['exists'] = $this->verify_all_collection($data['suggested_collection_name']);
        return $data;
    }

    /* function get_filters($data) */
    /* receive ((int,string) parent) */
    /* Retorna os filtros do dynatree */
    /* Author: Eduardo */

    public function get_filters($data) {
        $recover_data = unserialize(stripslashes($data['filters']));
        //author
        if (isset($recover_data['author'])) {
            $data['author'] = get_user_by('id', $recover_data['author'])->nickname;
        }
        //keyword
        if (isset($recover_data['keyword'])) {
            $data['keyword'] = $recover_data['keyword'];
        }
        //categories
        if (isset($recover_data['facets'])) {
            foreach ($recover_data['facets'] as $facet => $categories_id) {
                //$filter['node_key'] = $category;
                foreach ($categories_id as $category_id) {
                    $category['facet'] = $facet;
                    $category['id'] = $category_id;
                    $category['name'] = get_term_by('id', $category_id, 'socialdb_category_type')->name;
                    $data['categories'][] = $category;
                }
            }
        }
        //PROPERTIES OBJECT TREE
        if (isset($recover_data['properties_object_tree'])) {
            foreach ($recover_data['properties_object_tree'] as $property_id => $objects) {
                //$filter['node_key'] = $category;
                foreach ($objects as $object_id) {
                    $property['property_id'] = $property_id;
                    $property['id'] = $object_id;
                    $property['name'] = get_post($object_id)->post_title;
                    $data['properties_object_tree'][] = $property;
                }
            }
        }
        //PROPERTIES DATA TREE
        if (isset($recover_data['properties_data_tree'])) {
            foreach ($recover_data['properties_data_tree'] as $property_id => $metas) {
                //$filter['node_key'] = $category;
                foreach ($metas as $meta) {
                    $property['property_id'] = $property_id;
                    $property['id'] = $meta;
                    $property['name'] = $this->get_meta_by_id($meta);
                    $data['properties_data_tree'][] = $property;
                }
            }
        }
        //PROPERTIES DATA LINK
        if (isset($recover_data['properties_data_link'])) {
            foreach ($recover_data['properties_data_link'] as $property_id => $metas) {
                //$filter['node_key'] = $category;
                foreach ($metas as $meta) {
                    $property['property_id'] = $property_id;
                    $property['id'] = $meta;
                    $property['name'] = $meta;
                    $data['properties_data_link'][] = $property;
                }
            }
        }
        // lICENCAS
        if (isset($recover_data['license_tree'])) {
            foreach ($recover_data['license_tree'] as $license) {
                //$filter['node_key'] = $category;
                $property['license_id'] = $license;
                $property['id'] = $license;
                $property['name'] = get_post($license)->post_title;
                $data['license_tree'][] = $property;
            }
        }
        // Tipos do items
        if (isset($recover_data['type_tree'])) {
            foreach ($recover_data['type_tree'] as $type) {
                //$filter['node_key'] = $category;
                $property['type_id'] = $type;
                $property['id'] = $type;
                switch ($type) {
                    case 'text':
                        $property['name'] = __('Text', 'tainacan');
                        break;
                    case 'image':
                        $property['name'] = __('Image', 'tainacan');
                        break;
                    case 'video':
                        $property['name'] = __('Video', 'tainacan');
                        break;
                    case 'audio':
                        $property['name'] = __('Audio', 'tainacan');
                        break;
                    case 'pdf':
                        $property['name'] = __('PDF', 'tainacan');
                        break;
                    case 'other':
                        $property['name'] = __('Other', 'tainacan');
                        break;
                }
                $data['type_tree'][] = $property;
            }
        }
        // Formato dos items
        if (isset($recover_data['format_tree'])) {
            foreach ($recover_data['format_tree'] as $format) {
                //$filter['node_key'] = $category;
                $property['type_id'] = $format;
                $property['id'] = $format;
                $property['name'] = ucfirst($format);
                $data['format_tree'][] = $property;
            }
        }
        // Fonte dos items
        if (isset($recover_data['source_tree'])) {
            foreach ($recover_data['source_tree'] as $id => $source) {
                //$filter['node_key'] = $category;
                $property['source_id'] = trim($id);
                $property['id'] = trim($id);
                $property['name'] = get_post_meta(trim($id), 'socialdb_object_dc_source', true);
                $data['source_tree'][] = $property;
            }
        }
        //tags
        if (isset($recover_data['tags'])) {
            foreach ($recover_data['tags'] as $tag_id) {
                $tag['id'] = $tag_id;
                $tag['name'] = get_term_by('id', $tag_id, 'socialdb_tag_type')->name;
                $data['tags'][] = $tag;
            }
        }
        //properties_multipleselect
        if (isset($recover_data['properties_multipleselect'])) {
            $property = array();
            foreach ($recover_data['properties_multipleselect'] as $property => $values) {
                //$filter['node_key'] = $category;
                $type = PropertyModel::get_property_type($property);
                foreach ($values as $value) {
                    $property_autocomplete['property_id'] = $property;
                    $property_autocomplete['value'] = $value;
                    if ($type == 'socialdb_property_object') {
                        $property_autocomplete['name'] = get_post($value)->post_title;
                        ;
                    } else {
                        $property_autocomplete['name'] = $value;
                    }
                    $data['properties_multipleselect'][] = $property_autocomplete;
                }
            }
        }
        //PROPERTIES data range numeric 
        if (isset($recover_data['properties_data_range_numeric'])) {
            $property = array();
            foreach ($recover_data['properties_data_range_numeric'] as $property_id => $value) {
                //$filter['node_key'] = $category;
                $property['property_id'] = $property_id;
                $property['value'] = $value;
                $property['name'] = str_replace(',', ' ' . __('until', 'tainacan') . ' ', $value);
                $data['properties_data_range_numeric'][] = $property;
            }
        }
        //PROPERTIES fromto numeric
        if (isset($recover_data['properties_data_fromto_numeric'])) {
            $property = array();
            foreach ($recover_data['properties_data_fromto_numeric'] as $property_id => $value) {
                //$filter['node_key'] = $category;
                $property['property_id'] = $property_id;
                $property['value'] = $value;
                $property['name'] = str_replace(',', ' ' . __('until', 'tainacan') . ' ', $value);
                $data['properties_data_fromto_numeric'][] = $property;
            }
        }
        //PROPERTIES range date 
        if (isset($recover_data['properties_data_range_date'])) {
            $property = array();
            foreach ($recover_data['properties_data_range_date'] as $property_id => $value) {
                //$filter['node_key'] = $category;
                $property['property_id'] = $property_id;
                $property['value'] = $value;
                $property['name'] = str_replace(',', ' ' . __('until', 'tainacan') . ' ', $value);
                $data['properties_data_range_date'][] = $property;
            }
        }
        //PROPERTIES fromto date
        if (isset($recover_data['properties_data_fromto_date'])) {
            $property = array();
            foreach ($recover_data['properties_data_fromto_date'] as $property_id => $value) {
                //$filter['node_key'] = $category;
                $property['property_id'] = $property_id;
                $property['value'] = implode(',', $value);
                $property['name'] = str_replace(',', ' ' . __('until', 'tainacan') . ' ', implode(',', $value));
                $data['properties_data_fromto_date'][] = $property;
            }
        }
        return $data;
    }

    /**
     * metodo que realoca as propriedades de uma tab excluida
     * 
     * @param int $tab_id O id da aba
     * @param int $collection_id O id da colecao
     */
    public function realocate_tabs_collection($tab_id, $collection_id) {
        $array = unserialize(get_post_meta($collection_id, 'socialdb_collection_update_tab_organization', true));
        if ($array && is_array($array) && $array[0]):
            foreach ($array[0] as $index => $value) {
                if ($tab_id == $value) {
                    $array[0][$index] = 'default';
                }
            }
        endif;
        update_post_meta($collection_id, 'socialdb_collection_update_tab_organization', serialize($array));
    }

}
