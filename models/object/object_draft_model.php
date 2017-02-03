<?php
require_once(dirname(__FILE__) . '/object_model.php');

/**
 * The class ObjectModel 
 *
 */
class ObjectDraftModel extends ObjectModel {
     
    public function addItemDraft($data) {
        $category_root_id = $this->collection_model->get_category_root_of($data['collection_id']);
        $user_id = get_current_user_id();
        if ($user_id == 0) {
            $user_id = get_option('anonimous_user');
        }
        $status = get_post($data['object_id']);
        $post = array(
            'ID' => $data['object_id'],
            'post_title' => ($data['object_name']) ? $data['object_name'] : time(),
            'post_content' => $data['object_description'],
            'post_status' => ($status->post_status==='publish') ? 'publish'  : 'betatext',
            'post_author' => $user_id,
            'post_type' => 'socialdb_object'
        );
        $data['ID'] = wp_update_post($post);
        $this->insert_rankings_value($data['ID'],$data['collection_id']);
        $slug = wp_unique_post_slug(sanitize_title_with_dashes($data['object_name']), $data['ID'], 'inherit', 'socialdb_object', 0);
        $post = array(
            'ID' => $data['object_id'],
            'post_name' => $slug
        );
        $data['ID'] = wp_update_post($post);
        //inserindo o objecto do item e o seu tipo
        $this->insert_item_resource($data);
        //categoria raiz da colecao
        wp_set_object_terms($data['ID'], array((int) $category_root_id), 'socialdb_category_type');
        //inserindo as classificacoes
        $this->insert_classifications($data['object_classifications'], $data['ID']);
        //inserindo tags
        $this->insert_tags($data['object_tags'], $data['collection_id'], $data['ID']);
        //inserindo os valores das propriedades
        $this->insert_properties_values($data, $data['ID']);
        //verificando se existe aquivos para ser incluidos
        if ($_FILES) {
            $attachment_id = $this->add_thumbnail($data['ID']);
            if (isset($_FILES['object_thumbnail']) && !empty($_FILES['object_thumbnail'])) {
                set_post_thumbnail($data['ID'], $attachment_id);
            }
        }
        //inserido via img via url
        if (isset($data['thumbnail_url']) && $data['thumbnail_url']) {
            $this->add_thumbnail_url($data['thumbnail_url'], $data['ID']);
        }
        //inserindo a url fonte dos dados
        if (isset($data['object_url']) && $data['object_url']) {
            update_post_meta($data['ID'], 'socialdb_uri_imported', $data['object_url']);
        }
        //verificando se existe mapeamento ativo
        if (get_post_meta($data['collection_id'], 'socialdb_collection_mapping_exportation_active')) {
            add_post_meta($data['ID'], 'socialdb_channel_id', get_post_meta($data['collection_id'], 'socialdb_collection_mapping_exportation_active', true));
        }
        // propriedade de termos
        $this->insert_properties_terms($data, $data['ID']);

        //object_license
        if ($data['object_license']) {
            update_post_meta($data['ID'], 'socialdb_license_id', $data['object_license']);
        }
        //propriedades compostas
        $this->insert_compounds($data, $data['ID']);
        // timezone e salvo o id do rascunho
        date_default_timezone_set('America/Sao_Paulo');
        $result = ['date'=> date('d/m/y'),'hour'=> date('H:i:s')];
        if($status->post_status != 'publish')
            update_user_meta(get_current_user_id(), 'socialdb_collection_'.$data['collection_id'].'_betatext', $data['ID']);
        return json_encode($result);
    }
   
}
