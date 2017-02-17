<?php

/**
 * #1 - ADICIONAR ITEMS TIPO TEXTO
 * #2 - ADICIONAR ITEMS DEFAULT
 */
require_once(dirname(__FILE__) . '../../../models/object/object_model.php');
require_once(dirname(__FILE__) . '../../../models/collection/collection_model.php');
require_once(dirname(__FILE__) . '../../../controllers/general/general_controller.php');
require_once(dirname(__FILE__) . '../../../models/user/user_model.php');
require_once(dirname(__FILE__) . '../../../models/object/objectfile_model.php');

class ObjectController extends Controller {

    public function operation($operation, $data) {
        $object_model = new ObjectModel();
        $objectfile_model = new ObjectFileModel;
        switch ($operation) {
            // #1 ADICIONAR ITEMS TIPO TEXTO
            case "create_item_text":
                //verifico se existe rascunho para se mostrado
                $beta_id = get_user_meta(get_current_user_id(), 'socialdb_collection_' . $data['collection_id'] . '_betatext', true);
                if ($beta_id && is_numeric($beta_id)) {
                    $data['object_id'] = $beta_id;
                    $data['is_beta_text'] = true;
                    return $this->operation('edit', $data);
                }
                //se nao ele busca o cache da pagina de adiconar item
                $has_cache = $this->has_cache($data['collection_id'], 'create-item-text');
                $option = get_option('tainacan_cache');
                if ($has_cache && $option != 'false' && $data['classifications'] == '') {
                    $has_cache = htmlspecialchars_decode(stripslashes($has_cache)) .
                            '<input type="hidden" id="temporary_id_item" value="' . $object_model->create() . '">' .
                            file_get_contents(dirname(__FILE__) . '../../../views/object/js/create_item_text_cache_js.php') .
                            file_get_contents(dirname(__FILE__) . '../../../views/object/js/create_draft_js.php');
                    return $has_cache;
                } else {
                    $data['object_name'] = get_post_meta($data['collection_id'], 'socialdb_collection_object_name', true);
                    $data['socialdb_collection_attachment'] = get_post_meta($data['collection_id'], 'socialdb_collection_attachment', true);
                    $data['object_id'] = $object_model->create();
                    return $this->render(dirname(__FILE__) . '../../../views/object/create_item_text.php', $data);
                }
                break;
            // FIM : ADICIONAR ITEMS TIPO TEXTO
            // #1 ADICIONAR ITEMS TIPO URL
            case "create_item_url":
                $data['object_name'] = get_post_meta($data['collection_id'], 'socialdb_collection_object_name', true);
                $data['socialdb_collection_attachment'] = get_post_meta($data['collection_id'], 'socialdb_collection_attachment', true);
                $data['object_id'] = $object_model->create();
                return $this->render(dirname(__FILE__) . '../../../views/object/create_item_url.php', $data);
                break;
            // FIM : ADICIONAR ITEMS TIPO TEXTO 
            // #2 - ADICIONAR ITEMS DEFAULT
            case "create":
                $data['object_name'] = get_post_meta($data['collection_id'], 'socialdb_collection_object_name', true);
                $data['socialdb_collection_attachment'] = get_post_meta($data['collection_id'], 'socialdb_collection_attachment', true);
                $data['object_id'] = $object_model->create();
                return $this->render(dirname(__FILE__) . '../../../views/object/create.php', $data);
                break;
            // FIM : ADICIONAR ITEMS DEFAULT
            // adiciona um item simples
            case "add":
                return $object_model->add($data);
                break;
            case "add_item_not_published":
                return $object_model->add_item_not_published($data);
                break;
            //#4 EDITOR DE ITEMS MULTIPLOS
            case "showViewMultipleItems":
                $items = get_user_meta(get_current_user_id(), 'socialdb_collection_' . $data['collection_id'] . '_betafile');
                if (!$items || empty($items)):
                    $data['object_id'] = $object_model->create();
                    return $this->render(dirname(__FILE__) . '../../../views/object/multiple_items/create.php', $data);
                else:
                    return $this->operation('betafile', $data);
                endif;
                break;
            case "editor_items":
                $data['properties'] = $object_model->show_object_properties($data);
                $data['items'] = $objectfile_model->get_files($data);
                if ($data['items'] && empty(!$data['items'])) {
                    return $this->render(dirname(__FILE__) . '../../../views/object/multiple_items/editor_items.php', $data);
                } else {
                    return 0;
                }
                break;
            //END: EDITOR DE ITEMS MULTIPLOS
            //# EDITOR DE ITENS PARA REDES SOCIAIS
            case "showAddItemURL":
                return $this->render(dirname(__FILE__) . '../../../views/object/multiple_social_network/create.php', $data);
                break;
            case "showViewMultipleItemsSocialNetwork":
                $data['properties'] = $object_model->show_object_properties($data);
                $data['items'] = $objectfile_model->get_inserted_items_social_network($data);
                if ($data['items'] && empty(!$data['items'])) {
                    return $this->render(dirname(__FILE__) . '../../../views/object/multiple_social_network/editor_items.php', $data);
                } else {
                    return 0;
                }
                break;
            //END: EDITOR DE ITENS PARA REDES SOCIAIS
            //BEGIN: FILES AND SOCIAL MEDIA betafile
            case 'betafile':
                $data['properties'] = $object_model->show_object_properties($data);
                $data['is_beta_file'] = true;
                $data['items_id'] = get_user_meta(get_current_user_id(), 'socialdb_collection_' . $data['collection_id'] . '_betafile');
                $data['items'] = $objectfile_model->get_inserted_items_social_network($data);
                if ($data['items'] && empty(!$data['items'])) {
                    return $this->render(dirname(__FILE__) . '../../../views/object/multiple_social_network/editor_items.php', $data);
                } else {
                    return 0;
                }
                break;
            //END    
            case 'insert_fast':// apenas com o titulo
                return $object_model->fast_insert($data);
                break;
            case 'insert_fast_url':// atraves da url
                return $object_model->fast_insert_url($data);
            // # - PAGINA DE EDICAO DEFAULT
            case "edit_default":
                $object_name = get_post_meta($data['collection_id'], 'socialdb_collection_object_name', true);
                $socialdb_collection_attachment = get_post_meta($data['collection_id'], 'socialdb_collection_attachment', true);
                $data = $object_model->edit($data['object_id'], $data['collection_id']);
                $data['object_name'] = $object_name;
                $data['socialdb_collection_attachment'] = $socialdb_collection_attachment;
                $data['socialdb_object_from'] = get_post_meta($data['object']->ID, 'socialdb_object_from', true);
                $data['socialdb_object_dc_source'] = get_post_meta($data['object']->ID, 'socialdb_object_dc_source', true);
                $data['socialdb_object_content'] = get_post_meta($data['object']->ID, 'socialdb_object_content', true);
                $data['socialdb_object_dc_type'] = get_post_meta($data['object']->ID, 'socialdb_object_dc_type', true);
                return $this->render(dirname(__FILE__) . '../../../views/object/edit.php', $data);
                break;
            // # - PAGINA DE EDICAO PARA TEXTOS
            case "edit":
                $checkout = get_post_meta($data['object_id'], 'socialdb_object_checkout', true);
                if (is_numeric($checkout) && !isset($data['motive'])) {
                    $user = get_user_by('id', $checkout)->display_name;
                    $time = get_post_meta($data['object_id'], 'socialdb_object_checkout_time', true);
                    return 'checkout@' . $user . '@' . date('d/m/Y', $time);
                }
                $object_name = get_post_meta($data['collection_id'], 'socialdb_collection_object_name', true);
                $socialdb_collection_attachment = get_post_meta($data['collection_id'], 'socialdb_collection_attachment', true);
                $collection_id = $data['collection_id'];
                $beta_text = (isset($data['is_beta_text'])) ? $data['is_beta_text'] : false;
                $data = $object_model->edit($data['object_id'], $data['collection_id']);
                $data['object_name'] = $object_name;
                $data['collection_id'] = $collection_id;
                $data['socialdb_collection_attachment'] = $socialdb_collection_attachment;
                $data['socialdb_object_from'] = get_post_meta($data['object']->ID, 'socialdb_object_from', true);
                $data['socialdb_object_dc_source'] = get_post_meta($data['object']->ID, 'socialdb_object_dc_source', true);
                $data['socialdb_object_content'] = get_post_meta($data['object']->ID, 'socialdb_object_content', true);
                $data['socialdb_object_dc_type'] = get_post_meta($data['object']->ID, 'socialdb_object_dc_type', true);
                if ($beta_text)
                    $data['is_beta_text'] = true;
                return $this->render(dirname(__FILE__) . '../../../views/object/edit_item_text.php', $data);
                break;
            case "update":
                return $object_model->update($data);
                break;
            case "delete":
                return $object_model->delete($data);
                break;
            case "show_classifications":
                $data = $object_model->show_classifications($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/object_classifications.php', $data);
                break;
            case "list": // A listagem inicial dos objetos
                $return = array();
                $collection_model = new CollectionModel;
                $collection_id = $data['collection_id'];
                $recover_wpquery = $object_model->get_args($data);
                $args = $object_model->list_all($data);
                $data['loop'] = new WP_Query($args);
                $data['collection_data'] = $collection_model->get_collection_data($collection_id);
                $data["show_string"] = is_root_category($collection_id) ? __('Showing collections:', 'tainacan') : __('Showing Items:', 'tainacan');
                
                // View mode's vars
                $data["geo_coordinates"]["lat"] = get_post_meta($collection_id, "socialdb_collection_latitude_meta", true);
                $data["geo_coordinates"]["long"] = get_post_meta($collection_id, "socialdb_collection_longitude_meta", true);
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($collection_id, "socialdb_collection_table_metas", true)));

                $view_count = get_post_meta($collection_id, 'collection_view_count', true);
                if (empty($view_count)):
                    add_post_meta($collection_id, 'collection_view_count', 1, true);
                else:
                    $updated = $view_count + 1;
                    update_post_meta($collection_id, 'collection_view_count', $updated, $view_count);
                endif;

                if (!$data['sorted_by']) {
                    $data['sorted_by'] = 'desc';
                }
                $data['listed_by'] = $object_model->get_ordered_name($data['collection_id'], $data['ordenation_id'], $data['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($data['collection_id'], get_current_user_id());
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                $return['args'] = serialize($recover_wpquery);
                if (empty($object_model->get_collection_posts($data['collection_id']))) {
                    $return['empty_collection'] = true;
                } else {
                    $return['empty_collection'] = false;
                }
                $logData = ['collection_id' => $collection_id, 'event_type' => 'user_collection', 'event' => 'view'];
                Log::addLog($logData);
                if (mb_detect_encoding($return['page'], 'auto') == 'UTF-8') {
                    $return['page'] = iconv('ISO-8859-1', 'UTF-8', utf8_decode($return['page']));
                }
                return json_encode($return);
                break;
            case "list_trash": // A listagem dos objetos na lixeira
                $return = array();
                $collection_model = new CollectionModel;
                $collection_id = $data['collection_id'];
                $recover_wpquery = $object_model->get_args($data);
                //$post_status = ($collection_id == get_option('collection_root_id') ? 'draft' : 'trash');
                $post_status = 'draft';
                $data['mycollections'] = 'true';
                $args = $object_model->list_all($data, $post_status);
                $data['loop'] = new WP_Query($args);
                $data['collection_data'] = $collection_model->get_collection_data($collection_id);

                $view_count = get_post_meta($collection_id, 'collection_view_count', true);
                if (empty($view_count)):
                    add_post_meta($collection_id, 'collection_view_count', 1, true);
                else:
                    $updated = $view_count + 1;
                    update_post_meta($collection_id, 'collection_view_count', $updated, $view_count);
                endif;

                if (!$data['sorted_by']) {
                    $data['sorted_by'] = 'desc';
                }
                $data['listed_by'] = $object_model->get_ordered_name($data['collection_id'], $data['ordenation_id'], $data['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($data['collection_id'], get_current_user_id());
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list_trash.php', $data);
                $return['args'] = serialize($recover_wpquery);
                if (empty($object_model->get_collection_posts($data['collection_id']))) {
                    $return['empty_collection'] = true;
                } else {
                    $return['empty_collection'] = false;
                }
                if (mb_detect_encoding($return['page'], 'auto') == 'UTF-8') {
                    $return['page'] = iconv('ISO-8859-1', 'UTF-8', utf8_decode($return['page']));
                }
                return json_encode($return);
                break;
            case 'restore_object':
                if ($data['collection_id'] != get_option('collection_root_id')) {
                    //restore item
                    $result = $object_model->restoreItem($data['object_id']);
                } else {
                    //restore collection
                    $result = $object_model->restoreItem($data['object_id']);
                }
                return $result;
                break;
            case 'delete_permanently_object':
                if ($data['collection_id'] != get_option('collection_root_id')) {
                    //delete item
                    $result = $object_model->delete_permanently_item($data['object_id']);
                } else {
                    //delete collection
                    //$result = $object_model->delete_permanently_collection($data['object_id']);
                    $result = $object_model->delete_permanently_item($data['object_id']);
                }
                return $result;
                break;
            case 'filter': // a listagem com filtros
                $collection_model = new CollectionModel;
                $data['loop'] = $object_model->filter($data);
                $data['collection_data'] = $collection_model->get_collection_data($data['collection_id']);
                if ($data['order_by']) {
                    $data['listed_by'] = $object_model->get_ordered_name($data['collection_id'], $data['ordenation_id'], $data['order_by']);
                } else {
                    $data['listed_by'] = $object_model->get_ordered_name($data['collection_id'], $data['ordenation_id']);
                }
                //
                if (!$data['sorted_by']) {
                    $data['sorted_by'] = 'desc';
                }
                $data['is_moderator'] = CollectionModel::is_moderator($data['collection_id'], get_current_user_id());
                return $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);

            // propriedades na insercao do objeto
            case 'show_object_properties'://
                $data = $object_model->show_object_properties($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/show_insert_object_properties.php', $data);
            // propriedades na insercao do objeto com o ACCORDION
            case 'show_object_properties_accordion'://
                $data = $object_model->show_object_properties($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/list_properties_accordion.php', $data);
            // propriedades na EDICAO do objeto
            case 'show_object_properties_edit'://
                $data = $object_model->show_object_properties($data);
                $data['categories_id'] = wp_get_object_terms($data['object_id'], 'socialdb_category_type', array('fields' => 'ids'));
                return $this->render(dirname(__FILE__) . '../../../views/object/edit_object_properties/list_properties.php', $data);
                break;
            // propriedades na EDICAO do objeto ACCORDEON
            case 'list_properties_edit_accordeon'://
                $data = $object_model->show_object_properties($data);
                $data['categories_id'] = wp_get_object_terms($data['object_id'], 'socialdb_category_type', array('fields' => 'ids'));
                return $this->render(dirname(__FILE__) . '../../../views/object/edit_object_properties/edit_list_properties_accordion.php', $data);
                break;
            // propriedades de categoria
            case 'list_properties_categories_accordeon'://
                $data = $object_model->show_object_properties($data);
                $data['categories_id'] = wp_get_object_terms($data['object_id'], 'socialdb_category_type', array('fields' => 'ids'));
                return $this->render(dirname(__FILE__) . '../../../views/object/append_properties_categories/properties_categories_accordion.php', $data);
                break;
            case 'list_properties_categories_accordeon_multiple'://
                $data = $object_model->show_object_properties($data);
                $data['categories_id'] = wp_get_object_terms($data['object_id'], 'socialdb_category_type', array('fields' => 'ids'));
                return $this->render(dirname(__FILE__) . '../../../views/object/append_properties_categories/multiple_properties_categories_accordion.php', $data);
                break;
            // mostra propriedades preparando para um evento
            case 'list_properties':// mostra todas as propriedades com seus respectivos valores (aparece por default)
                $data = $object_model->list_properties($data);
                $data['categories_id'] = wp_get_object_terms($data['object_id'], 'socialdb_category_type', array('fields' => 'ids'));
                return $this->render(dirname(__FILE__) . '../../../views/object/show_list_event_properties.php', $data);
                break;
            case 'list_properties_edit_remove':// pega todas as propriedade para serem mostradas no formulario de edicao e remocao
                $data = $object_model->list_properties($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/show_list_event_properties_edit_remove.php', $data);
                break;
            case "get_objects_by_property_json":// pega todos os objetos relacionado de uma propriedade e coloca em um array json
                return $object_model->get_objects_by_property_json($data);
            case "get_property_object_value":// retorna os valores para uma propriedade de objeto especificao
                return $object_model->get_property_object_value($data);
            case 'show_form_data_property':// mostra o formulario para insercao de propriedade de dados
                $property_model = new PropertyModel();
                $data = $property_model->list_data($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/data_property_form.php', $data);
            case 'show_form_object_property':// mostra o formulario para insercao de propriedade de objecto
                $property_model = new PropertyModel();
                $data = $property_model->list_data($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/object_property_form.php', $data);
            case 'show_edit_data_property_form':// mostra o formulario para EDICAO de propriedade de dados
                $property_model = new PropertyModel();
                $data['value'] = json_decode($property_model->edit_property($data));
                $data = $property_model->list_data($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/edit_data_property_form.php', $data);
            case 'show_edit_object_property_form':// mostra o formulario para EDICAO de propriedade de OBJETOS
                $property_model = new PropertyModel();
                $data['value'] = json_decode($property_model->edit_property($data));
                $data = $property_model->list_data($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/edit_object_property_form.php', $data);
            case "list_single_object":
                $user_model = new UserModel();
                $object_id = $data['object_id'];
                $col_id = $data['collection_id'];
                $data['object'] = get_post($object_id);
                $data["username"] = $user_model->get_user($data['object']->post_author)['name'];
                $data['metas'] = get_post_meta($object_id);
                $data['collection_metas'] = get_post_meta($col_id, 'socialdb_collection_download_control', true);
                $data['collection_metas'] = ($data['collection_metas'] ? $data['collection_metas'] : 'allowed');
                $data['has_watermark'] = get_post_meta($col_id, 'socialdb_collection_add_watermark', true);
                $watermark_id = get_post_meta($col_id, 'socialdb_collection_watermark_id', true);
                if ($watermark_id) {
                    $data['url_watermark'] = wp_get_attachment_url($watermark_id);
                } else {
                    $data['url_watermark'] = get_template_directory_uri() . '/libraries/images/icone.png';
                }
                //busco a forma de visualizacao do item
                $mode = get_post_meta($col_id, 'socialdb_collection_item_visualization', true);
                //se existir a acao para alterar a home do item
                if (has_action('alter_page_item')) {
                    return apply_filters('alter_page_item', $data);
                }else if($mode=='one'){
                    //$data = $object_model->edit($data['object_id'], $data['collection_id']);
                    $data['collection_id'] = $col_id;
                    $data['object_name'] = $object_name;
                    $data['is_view_mode'] = true;
                    $data['socialdb_collection_attachment'] = $socialdb_collection_attachment;
                    $data['socialdb_object_from'] = get_post_meta($object_id, 'socialdb_object_from', true);
                    $data['socialdb_object_dc_source'] = get_post_meta($object_id, 'socialdb_object_dc_source', true);
                    $data['socialdb_object_content'] = get_post_meta($object_id, 'socialdb_object_content', true);
                    $data['socialdb_object_dc_type'] = get_post_meta($object_id, 'socialdb_object_dc_type', true);
                    return $this->render(dirname(__FILE__) . '../../../views/object/edit_item_text.php', $data);
                }else {
                    $logData = ['collection_id' => $col_id, 'item_id' => $object_id,
                        'event_type' => 'user_items', 'event' => 'view'];
                    Log::addLog($logData);
                    return $this->render(dirname(__FILE__) . '../../../views/object/list_single_object.php', $data);
                }
                break;
            case 'press_item':
                $user_model = new UserModel();
                $object_id = $data['object_id'];
                $col_id = $data['collection_id'];
                $press['object'] = get_post($object_id);
                $_object = get_post($object_id);
                $press["author"] = $user_model->get_user($_object->post_author)['name'];
                $press["title"]  = $_object->post_title;
                $press["desc"]   = $_object->post_content;
                $press["output"] = substr($_object->post_name, 0, 15) . mktime();
                $press["data_c"] = explode(" ", $_object->post_date)[0];

                $_item_meta = get_post_meta($object_id);
                foreach($_item_meta as $meta => $val) {
                    if( is_string($meta)) {
                        $pcs = explode("_", $meta);
                        if( ($pcs[0] . $pcs[1]) == "socialdbproperty" ) {
                            $col_meta = get_term($pcs[2]);
                            if( !is_null($col_meta) && is_object($col_meta) ) {
                                $_pair = ['meta' => $col_meta->name, 'value' => $val[0]];
                                $press['inf'][] = $_pair;
                            }
                        }
                    }

                }
                return json_encode($press);
                
            case "list_single_object_version":
                $user_model = new UserModel();
                $object_id = $data['object_id'];
                $data['object'] = get_post($object_id);
                $data["username"] = $user_model->get_user($data['object']->post_author)['name'];
                $data['metas'] = get_post_meta($object_id);
                $data['collection_metas'] = get_post_meta($data['collection_id'], 'socialdb_collection_download_control', true);
                $data['collection_metas'] = ($data['collection_metas'] ? $data['collection_metas'] : 'allowed');
                $data['has_watermark'] = get_post_meta($data['collection_id'], 'socialdb_collection_add_watermark', true);
                $watermark_id = get_post_meta($data['collection_id'], 'socialdb_collection_watermark_id', true);
                if ($watermark_id) {
                    $data['url_watermark'] = wp_get_attachment_url($watermark_id);
                } else {
                    $data['url_watermark'] = get_template_directory_uri() . '/libraries/images/icone.png';
                }

                if (has_action('alter_page_item')) {
                    $array_json['html'] = apply_filters('alter_page_item', $data);
                    return json_encode($array_json);
                } else {
                    return $this->render(dirname(__FILE__) . '../../../views/object/list_single_object_version.php', $data);
                }
                break;
            case "list_single_object_by_name":
                $user_model = new UserModel();
                $object_name = $data['object_name'];
                $args = array(
                    'name' => $object_name,
                    'post_type' => 'socialdb_object',
                    'post_status' => 'publish',
                    'numberposts' => 1
                );
                $result = get_posts($args);
                if (empty($result) || !isset($result)) {
                    $args = array(
                        'name' => $object_name,
                        'post_type' => 'socialdb_object',
                        'post_status' => 'inherit',
                        'numberposts' => 1
                    );
                    $result = get_posts($args);
                }
                if (count($result) > 0 && isset($result[0]) && in_array($result[0]->post_status, array('publish', 'inherit'))) {
                    $data['object'] = $result[0];
                    $data["username"] = $user_model->get_user($data['object']->post_author)['name'];
                    $data['metas'] = get_post_meta($data['object']->ID);
                    $data['collection_metas'] = get_post_meta($data['collection_id'], 'socialdb_collection_download_control', true);
                    $data['collection_metas'] = ($data['collection_metas'] ? $data['collection_metas'] : 'allowed');
                    $data['has_watermark'] = get_post_meta($data['collection_id'], 'socialdb_collection_add_watermark', true);
                    $watermark_id = get_post_meta($data['collection_id'], 'socialdb_collection_watermark_id', true);
                    if ($watermark_id) {
                        $data['url_watermark'] = wp_get_attachment_url($watermark_id);
                    } else {
                        $data['url_watermark'] = get_template_directory_uri() . '/libraries/images/icone.png';
                    }
                    //busco a forma de visualizacao do item
                    $mode = get_post_meta($data['collection_id'], 'socialdb_collection_item_visualization', true);
                    //$mode = '';
                    //se existir a acao para alterar a home do item
                    if (has_filter('alter_page_item')) {
                        $array_json['html'] = apply_filters('alter_page_item', $data);
                        return json_encode($array_json);
                    }else if($mode=='one'){
                        //$data = $object_model->edit($data['object_id'], $data['collection_id']);
                        $data['collection_id'] = $data['collection_id'];
                        $data['object_name'] = $object_name;
                        $data['is_view_mode'] = true;
                        $data['socialdb_collection_attachment'] = $socialdb_collection_attachment;
                        $data['socialdb_object_from'] = get_post_meta($object_id, 'socialdb_object_from', true);
                        $data['socialdb_object_dc_source'] = get_post_meta($object_id, 'socialdb_object_dc_source', true);
                        $data['socialdb_object_content'] = get_post_meta($object_id, 'socialdb_object_content', true);
                        $data['socialdb_object_dc_type'] = get_post_meta($object_id, 'socialdb_object_dc_type', true);
                        $array_json['html'] = $this->render(dirname(__FILE__) . '../../../views/object/edit_item_text.php', $data);
                        return json_encode($array_json);
                    } else {
                        $array_json['html'] = $this->render(dirname(__FILE__) . '../../../views/object/list_single_object.php', $data);
                        return json_encode($array_json);
                    }
                } else {
                    $array_json['redirect'] = get_the_permalink($data['collection_id']);
                    return json_encode($array_json);
                }
                break;
            case 'list_search':
                if ($data['collection_id'] == get_option('collection_root_id')) {
                    $array['is_json'] = false;
                    //
                    if (!$data['sorted_by']) {
                        $data['sorted_by'] = 'desc';
                    }
                    $data['loop'] = $object_model->list_collection($data);
                    $data['listed_by'] = $object_model->get_ordered_name($data['collection_id'], $data['ordenation_id']);
                    $array['html'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                    $logData = ['collection_id' => $data['collection_id'], 'user_id' => get_current_user_id(),
                        'event_type' => 'user_collection', 'event' => 'view'];
                    Log::addLog($logData);
                    return json_encode($array);
                } else {
                    $array['is_json'] = TRUE;
                    $array['link'] = get_the_permalink(get_option('collection_root_id')) . "?search=" . $data['keyword'];
                    return json_encode($array);
                }
                break;
            //temp file
            case 'delete_temporary_object':
                if (isset($data['delete_draft'])){
                    delete_user_meta(get_current_user_id(), 'socialdb_collection_' . $data['collection_id'] . '_betatext');
                    delete_user_meta(get_current_user_id(), 'socialdb_collection_' . $data['collection_id'] . '_betafile');
                }
                if ($data['ID'] && get_post($data['ID'])->post_status === 'betatext'):
                    $post = array(
                        'ID' => $data['ID'],
                        'post_status' => 'draft'
                    );
                    wp_update_post($post);
                    //deleto o rascunho assim que adiciono
                    return json_encode($data);
                    break;
                else:
                    delete_user_meta(get_current_user_id(), 'socialdb_collection_' . $data['collection_id'] . '_betatext');
                    delete_user_meta(get_current_user_id(), 'socialdb_collection_' . $data['collection_id'] . '_betafile');
                    return $object_model->delete($data);
            endif;
            //ACTION FILES
            case 'list_files':
                return $objectfile_model->list_files($data);
            case 'save_file':
                return $objectfile_model->save_file($data);
            case 'delete_file':
                return $objectfile_model->delete_file($data);
            case 'show_files':
                $data['attachments'] = $objectfile_model->show_files($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/file/list_file.php', $data);
            case 'redirect_facebook':
                return json_encode($object_model->redirect_facebook($data));
            // acoes para listagem de comentarios
            case 'list_comments':
                $data['permissions'] = $object_model->verify_comment_permissions($data['collection_id']);
                return $this->render(dirname(__FILE__) . '../../../views/object/comments/view_comments.php', $data);
            case 'list_comments_term':
                $data['permissions'] = $object_model->verify_comment_permissions($data['collection_id']);
                return $this->render(dirname(__FILE__) . '../../../views/comment/view_comments.php', $data);
            // licenças na insercao do objeto
            case 'show_collection_licenses':
                $data = $object_model->show_collection_licenses($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/show_insert_object_licenses.php', $data);
                break;
            case 'show_collection_licenses_search':
                $data = $object_model->show_collection_licenses($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/show_insert_object_licenses_search.php', $data);
                break;
            case 'help_choosing_license':
                $data = $object_model->help_choosing_license($data);
                return json_encode($data);
                break;
            case 'move_items_to_trash':
                $data = $object_model->move_to_trash($data['objects_ids'], $data['collection_id']);
                return json_encode($data);
            // limpando uma colecao
            case 'clean_collection_itens':
                $data = $object_model->clean_collection($data);
                return json_encode($data);
            // limpando alguns itens da colecao 
            case 'delete_items_socialnetwork':
                $data = $object_model->delete_items_socialnetwork($data);
                return json_encode($data);
            case 'remove_ids_socialnetwork':
                $data['items_id'] = explode(',', $data['items_id']);
                $data = $object_model->delete_items_socialnetwork($data);
                return json_encode($data);
            case 'get_last_attachment':
                $object_model->set_attachment_description($data['post_parent'], $data['post_content']);
                break;
            case 'update_attachment_legend':
                $object_model->update_attachment_legend($data['item_id'], $data['item_legend']);
                break;
            case 'insertUserDownload':
                if (is_user_logged_in()) {
                    $logData = ['collection_id' => $data['collection_id'], 'item_id' => $data['item_id'],
                        'event_type' => 'user_items', 'event' => 'download'];
                    Log::addLog($logData);
                    add_post_meta($data['thumb_id'], 'socialdb_user_download_' . time(), get_current_user_id());
                }
                return true;
                break;
            case 'edit_multiple_items':
                $set = [];
                if (!$data['items_data']) {
                    exit();
                }
                foreach ($data['items_data'] as $_previous) {
                    $data['items_id'] [] = $_previous['id'];
                    //array_push( $set, [ 'ID' => $_previous['id'], 'title' => $_previous['title'], 'desc' => $_previous['desc'] ] );
                }
                $data['items'] = $objectfile_model->get_inserted_items_social_network($data);
                $data['edit_multiple'] = true;
                if ($data['items'] && empty(!$data['items'])) {
                    return $this->render(dirname(__FILE__) . '../../../views/object/multiple_social_network/editor_items.php', $data);
                }
                //return $this->render( dirname(__FILE__) . '../../../views/object/temp/edit_multiple.php', [ 'edit_data' => $set ] );
                break;
            ################# PARSE URL ####################################
            case 'parse_url_alternative':
                $return = [];
                $extracted = $object_model->extract_metatags($data['url']);
                if ($extracted && is_array($extracted)) {
                    foreach ($extracted as $array) {
                        $return[$array['name_field']] = $array['value'];
                    }
                }
                return json_encode($return);
                break;
            ################# VERSIONAMENTO ####################################
            case 'duplicate_item_same_collection':
                $item = get_post($data['object_id']);
                $newItem = $object_model->copyItem($item, $data['collection_id']);
                $metas = get_post_meta($item->ID);
                $object_model->copyItemMetas($newItem, $metas);
                $object_model->copyItemCategories($newItem, $data['object_id']);
                $object_model->copyItemTags($newItem, $data['object_id']);

                $object_name = get_post_meta($data['collection_id'], 'socialdb_collection_object_name', true);
                $socialdb_collection_attachment = get_post_meta($data['collection_id'], 'socialdb_collection_attachment', true);
                $data = $object_model->edit($newItem, $data['collection_id']);
                $data['object_name'] = $object_name;
                $data['socialdb_collection_attachment'] = $socialdb_collection_attachment;
                $data['socialdb_object_from'] = get_post_meta($data['object']->ID, 'socialdb_object_from', true);
                $data['socialdb_object_dc_source'] = get_post_meta($data['object']->ID, 'socialdb_object_dc_source', true);
                $data['socialdb_object_content'] = get_post_meta($data['object']->ID, 'socialdb_object_content', true);
                $data['socialdb_object_dc_type'] = get_post_meta($data['object']->ID, 'socialdb_object_dc_type', true);
                return $this->render(dirname(__FILE__) . '../../../views/object/edit_item_text.php', $data);
                break;
            case 'duplicate_item_other_collection':

                $item = get_post($data['object_id']);
                $category_root_id = $object_model->get_category_root_of($data['collection_id']);
                $newItem = $object_model->copyItem($item, $data['new_collection_id']);
                $metas = get_post_meta($item->ID);
                $object_model->copyItemMetas($newItem, $metas, false);
                //$object_model->copyItemCategories($newItem, $data['object_id'], $category_root_id);
                $object_model->copyItemCategoriesOtherCol($newItem, $data['object_id'], $category_root_id);
                //$object_model->copyItemTags($newItem, $data['object_id']);
                $data['new_collection_url'] = $data['new_collection_url'] . '?open_edit_item=' . $newItem;
                return json_encode($data);
                break;
            case 'versioning':
                //var_dump($data);
                //exit();
                $item = get_post($data['object_id']);
                $metas = get_post_meta($item->ID);
                $version = $object_model->checkVersionNumber($item);
                $original = $object_model->checkOriginalItem($item->ID);
                $version_numbers = $object_model->checkVersions($original);
                //$version = $object_model->checkVersions($original);
                $new_version = count($version_numbers) + 2;
                //var_dump($version_numbers, $new_version);
                //exit();
                $newItem = $object_model->createVersionItem($item, $data['collection_id']);
                if ($newItem) {
                    $object_model->copyItemMetas($newItem, $metas);
                    $object_model->copyItemCategories($newItem, $data['object_id']);
                    $object_model->copyItemTags($newItem, $data['object_id']);
                    $object_model->createMetasVersion($newItem, $original, $new_version, $data['motive']);
                    return true;
                } else {
                    return false;
                }
                //var_dump($version_numbers, $new_version);
                /* $item = get_post($data['object_id']);
                  $newItem = $object_model->createVersionItem($item, $data['collection_id']); //inherit - revision
                  $metas = get_post_meta($item->ID);
                  $object_model->copyItemMetas($newItem, $metas);
                  $object_model->copyItemCategories($newItem, $data['object_id']);
                  $object_model->copyItemTags($newItem, $data['object_id']); */
                break;
            case 'show_item_versions':
                //var_dump($data); //collection_id, object_id
                $user_model = new UserModel();
                $object_id = $data['object_id'];
                $data['object'] = get_post($object_id);
                $data["username"] = $user_model->get_user($data['object']->post_author)['name'];
                $data['metas'] = get_post_meta($object_id);

                $data['version_active'] = $object_model->checkVersionNumber($data['object']);
                $data['original'] = $object_model->checkOriginalItem($data['object']->ID);
                $data['id_active'] = $object_model->checkVersionActive($data['original']);
                $data['version_numbers'] = $object_model->checkVersions($data['original']);

                $all_versions = $object_model->get_all_versions($data['original']);

                $arrFirst['ID'] = get_post($data['original'])->ID;
                $arrFirst['title'] = get_post($data['original'])->post_title;
                $arrFirst['version'] = 1;
                $arrFirst['data'] = get_post($data['original'])->post_date;
                $arrFirst['user'] = get_post_meta($data['original'], 'socialdb_version_user', true);
                $arrFirst['note'] = get_post_meta($data['original'], 'socialdb_version_comment', true);

                $data['versions'][] = $arrFirst;

                foreach ($all_versions as $each_version) {
                    $user = get_user_by('ID', get_post_meta($each_version->ID, 'socialdb_version_user', true));
                    $arrV['ID'] = $each_version->ID;
                    $arrV['title'] = $each_version->post_title;
                    $arrV['version'] = get_post_meta($each_version->ID, 'socialdb_version_number', true);
                    $arrV['data'] = get_post_meta($each_version->ID, 'socialdb_version_date', true);
                    $arrV['user'] = $user->display_name;
                    $arrV['note'] = get_post_meta($each_version->ID, 'socialdb_version_comment', true);
                    $data['versions'][] = $arrV;
                }

                return $this->render(dirname(__FILE__) . '../../../views/object/list_versions.php', $data);
                break;
            case 'delete_version':
                //var_dump($data);
                $original = get_post_meta($data['version_id'], 'socialdb_version_postid', true);
                if ($original) {
                    //E uma versao
                    $result = $object_model->send_version_to_trash($data['version_id']);
                } else {
                    //E o item original
                }
                break;
            case 'restore_version':
                //var_dump($data);
                $item = get_post($data['active_id']);
                $newItem = $data['version_id'];
                $object_model->revertItem($item, $newItem);
                return true;
                break;
            case 'check-out':
                update_post_meta($data['object_id'], 'socialdb_object_checkout', (isset($data['value'])) ? '' : get_current_user_id());
                update_post_meta($data['object_id'], 'socialdb_object_checkout_time', time());
                return true;
            case 'check-in':
                $item = get_post($data['object_id']);
                $metas = get_post_meta($item->ID);
                $version = $object_model->checkVersionNumber($item);
                $original = $object_model->checkOriginalItem($item->ID);
                $version_numbers = $object_model->checkVersions($original);
                //$version = $object_model->checkVersions($original);
                $new_version = count($version_numbers) + 2;
                //var_dump($version_numbers, $new_version);
                //exit();
                $newItem = $object_model->createVersionItem($item, $data['collection_id']);
                if ($newItem) {
                    $object_model->copyItemMetas($newItem, $metas);
                    $object_model->copyItemCategories($newItem, $data['object_id']);
                    $object_model->copyItemTags($newItem, $data['object_id']);
                    $object_model->createMetasVersion($newItem, $original, $new_version, $data['motive']);
                    $data['object_id'] = $newItem;
                    return $this->operation('edit', $data);
                } else {
                    return false;
                }

            case 'get_categories_for_import_items_zip':
                $categories = $object_model->get_facet_category_for_select($data['collection_id']);
                if (empty($categories)) {
                    $result = array(
                        array(
                            'id' => null,
                            'name' => __('No Metadata Found', 'tainacan')
                        )
                    );
                } else {
                    $result = array();
                    foreach ($categories as $category) {
                        $result[] = array(
                            'id' => $category->term_id,
                            'name' => $category->name
                        );
                    }
                }
                return json_encode($result);
                break;
        }
    }

    /**
     * function get_author_name($author_id)
     * @param int $author_id o id do author
     * @return string Retorna o nome do usuario
     * 
     * @author: Eduardo Humberto 
     */
    public function get_author_name($author_id) {
        $object_model = new ObjectModel();
        return $object_model->get_object_author($author_id, 'name');
    }

}

/*
 * Controller execution
 */
if ($_POST['operation']) {
    $operation = $_POST['operation'];
    $data = $_POST;
} else {
    $operation = $_GET['operation'];
    $data = $_GET;
}

$object_controller = new ObjectController();
echo $object_controller->operation($operation, $data);
?>
