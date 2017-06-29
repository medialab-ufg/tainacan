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
            // #1 ADICIONAR e EDITAR ITEM
            case "edit-item":
            case "create-item":
                //classe que executa toda a logica
                include_once dirname(__FILE__) . '../../../views/object/formItem/helper/formItem.class.php';
                //sessao
                if(!session_id()) {
                        session_start();
                }
                //verificacoes para edicao ou criacao deitem
                if(isset($data['item_id'])){
                  $formClass = new FormItem($data['collection_id'],__('Edit item','tainacan'));
                  $checkout = get_post_meta($data['object_id'], 'socialdb_object_checkout', true);
                  if (is_numeric($checkout) && !isset($data['motive'])) {
                      $user = get_user_by('id', $checkout)->display_name;
                      $time = get_post_meta($data['object_id'], 'socialdb_object_checkout_time', true);
                      return 'checkout@' . $user . '@' . date('d/m/Y', $time);
                  }
                  $_SESSION['operation-form'] = 'edit';
                }else{
                  $beta_id = get_user_meta(get_current_user_id(), 'socialdb_collection_' . $data['collection_id'] . '_betatext', true);
                  if ($beta_id && is_numeric($beta_id)) {
                    $formClass = new FormItem($data['collection_id'],__('Continue editting...','tainacan'));
                    $data['item_id'] = $beta_id;
                  }else{
                    $formClass = new FormItem($data['collection_id']);
                  } 
                   $_SESSION['operation-form'] = 'add';
                }
                //se nao existir algum ID eu crio
                if(isset($data['item_id'])) { 
                    $data['ID'] = $data['item_id']; 
                }else{  
                    $data['ID'] = $object_model->create();
                    update_user_meta(get_current_user_id(), 'socialdb_collection_' . $data['collection_id'] . '_betatext', $data['ID']); 
                }
                //jogo a class no array que sera utlizado no formulario
                $data['formItem'] = $formClass;
                $data['modeView'] = get_post_meta($data['collection_id'], 'socialdb_collection_submission_visualization', true);
                //verifico se ja existe as propriedades no cache
                $cache = get_post_meta($data['collection_id'], 'properties-cached', true);
                if(!$cache || $cache === ''){
                   $data['properties'] = $object_model->show_object_properties($data);
                   update_post_meta($data['collection_id'], 'properties-cached', serialize($data['properties']));
                }else{
                   $data['properties'] = unserialize($cache);
                }
                //renderizo
                return $this->render(dirname(__FILE__) . '../../../views/object/formItem/formItem.php', $data);
            // propriedades de categoria
            case 'appendCategoryMetadata'://
                    //class
                    include_once dirname(__FILE__) . '../../../views/object/formItem/helper/formItem.class.php';
                    $formItem = new FormItem($data['collection_id']);
                    $data = $object_model->show_object_properties($data);
                    $properties_to_avoid = explode(',', $data['properties_to_avoid']);
                    return $formItem->startCategoryMetadata($properties_to_avoid, $data);
                    break;
################################################################################
            case "create_item_text":
                //verifico se existe rascunho para se mostrado
                $beta_id = get_user_meta(get_current_user_id(), 'socialdb_collection_' . $data['collection_id'] . '_betatext', true);
//                if ($beta_id && is_numeric($beta_id)) {
//                    $data['object_id'] = $beta_id;
//                    $data['is_beta_text'] = true;
//                    return $this->operation('edit', $data);
//                }
                //se nao ele busca o cache da pagina de adiconar item
                $has_cache = $this->has_cache($data['collection_id'], 'create-item-text');
                $option = get_option('tainacan_cache');
//                if ($has_cache && $option != 'false' && $data['classifications'] == '') {
//                    $has_cache = htmlspecialchars_decode(stripslashes($has_cache)) .
//                            '<input type="hidden" id="temporary_id_item" value="' . $object_model->create() . '">' .
//                            file_get_contents(dirname(__FILE__) . '../../../views/object/js/create_item_text_cache_js.php') .
//                            file_get_contents(dirname(__FILE__) . '../../../views/object/js/create_draft_js.php');
//                    return $has_cache;
//                } else {
                    $data['object_name'] = get_post_meta($data['collection_id'], 'socialdb_collection_object_name', true);
                    $data['socialdb_collection_attachment'] = get_post_meta($data['collection_id'], 'socialdb_collection_attachment', true);
                    $data['object_id'] = $object_model->create();
                    return $this->render(dirname(__FILE__) . '../../../views/object/create_item_text.php', $data);
                //}
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
                $return['preset_order'] = $recover_wpquery['order'];

                if (empty($object_model->get_collection_posts($data['collection_id']))) {
                    $return['empty_collection'] = true;
                } else {
                    $return['empty_collection'] = false;
                }
                $logData = ['collection_id' => $collection_id, 'event_type' => 'user_collection', 'event' => 'view'];
                Log::addLog($logData);
                /* if (mb_detect_encoding($return['page'], 'auto') == 'UTF-8') {  $return['page'] = iconv('ISO-8859-1', 'UTF-8', utf8_decode($return['page'])); } */
                return json_encode($return);
                break;
            case "list_trash": // A listagem dos objetos na lixeira
                $return = array();
                $collection_model = new CollectionModel;
                $collection_id = $data['collection_id'];
                $recover_wpquery = $object_model->get_args($data);
                //$post_status = ($collection_id == get_option('collection_root_id') ? 'draft' : 'trash');
                $post_status = 'draft';
                $recover_wpquery['post_status'] = $post_status;
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
                if(!session_id()) {
                        session_start();
                }
 //               $cache = $_SESSION['collection_'.$data['collection_id'].'_properties'];
//                if(!$cache){
//                   $data = $object_model->show_object_properties($data);
//                   $_SESSION['collection_'.$data['collection_id'].'_properties'] = $data;
//                }else{
//                   $cache['object_id'] =  $data['object_id'];
//                   $data = $cache;
//                }
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
            case "get_objects_default_value":// pega todos os objetos relacionado de uma propriedade e coloca em um array json
                return $object_model->get_objects_by_selected_categories($data['categories'],$data['term']);
            case "get_terms_default_value":// pega todos os objetos relacionado de uma propriedade e coloca em um array json
                return $object_model->search_term_by_parent($data['parent'],$data['term']);
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
                } else if ($mode == 'one') {
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
                } else {
                    $logData = ['collection_id' => $col_id, 'item_id' => $object_id,
                        'event_type' => 'user_items', 'event' => 'view'];
                    Log::addLog($logData);
                    return $this->render(dirname(__FILE__) . '../../../views/object/list_single_object.php', $data);
                }
                break;
            case 'press_item':
                $user_model = new UserModel();
                $object_id = $data['object_id'];
                $_object = get_post($object_id);

                $press = [
                    "author" => $user_model->get_user($_object->post_author)['name'],
                    "title" => $_object->post_title,
                    "desc" => $_object->post_content,
                    "output" => substr($_object->post_name, 0, 15) . mktime(),
                    "data_c" => explode(" ", $_object->post_date)[0],
                    "object" => get_post($object_id),
                    "breaks" => $this->get_item_line_breaks($_object->post_content) // Pegar # de brs
                ];

                $_item_meta = get_post_meta($object_id);
                if ($_item_meta['_thumbnail_id']) {
                    $press['tbn'] = $this->format_item_thumb($_item_meta['_thumbnail_id']);
                }

                $item_attachs = $objectfile_model->show_files(['collection_id'=> $data['collection_id'], 'object_id' => $object_id]);
                if($item_attachs) {
                    foreach($item_attachs['image'] as $attach_obj) {
                        $press['attach'][] = [ 'title' => $attach_obj->post_title, 'url' => $attach_obj->guid ];
                    }
                }

                $tabs = [
                    'order' => get_post_meta($data['collection_id'], 'socialdb_collection_properties_ordenation'),
                    'organize' => unserialize( get_post_meta($data['collection_id'], 'socialdb_collection_update_tab_organization', true))[0],
                    'names' => get_post_meta($data['collection_id'], 'socialdb_collection_tab')
                ];

                $press['meta_ids_ord'] = explode(",",unserialize($tabs['order'][0])['default']);
                $ord_list = [];

                $total_index = 0;
                $_to_be_removed = [];
                foreach ($_item_meta as $meta => $val) {
                    $check_typeof_meta = explode( "_", $meta);
                    $is_compound_meta = false;

                    if( count($check_typeof_meta) == 4 && ctype_digit($check_typeof_meta[3]) ) {
                        $last_meta_id = intval($check_typeof_meta[3]);
                        if( $last_meta_id >= 0 && $last_meta_id <= 24 ) {
                            $is_compound_meta = true;
                        }
                    }

                    if (is_string($meta)) {
                        $pcs = explode("_", $meta);
                        if (($pcs[0] . $pcs[1]) == "socialdbproperty") {
                            $col_meta = get_term($pcs[2]);
                            if (!is_null($col_meta) && is_object($col_meta)) {
                                if( 4 === count($pcs) && is_string($_item_meta[$meta][0]) ) {
                                    $_sub_metas = explode(",", $_item_meta[$meta][0]);
                                    $_current_term_id = $col_meta->term_id;
                                    $curr_term_metas = explode(",",get_term_meta($_current_term_id,'socialdb_property_compounds_properties_id', true));

                                    if(is_array($_sub_metas)) {
                                        $final_title = $col_meta->name;
                                        // $press['meta_ids'][] = $_current_term_id;
                                        $_pair = ['meta' => $final_title, 'value'=> '_____________________________', 'submeta_header' => true, 'header_idx' => $total_index, 'meta_id' => $_current_term_id, 'meta_tab' => $tabs['organize'][$_current_term_id]];
                                        $press['inf'][] = $_pair;

                                        $ord_list[$_pair['meta_id']] = $_pair;

                                        $current_submeta_vals = [];
                                        $curr_meta = 0;
                                        foreach($_sub_metas as $s_meta) {
                                            $_meta_ = get_metadata_by_mid('post', $s_meta);
                                            if(ctype_digit($s_meta)) {
                                                if(is_object($_meta_)) {
                                                    $_title_id = explode("_", $_meta_->meta_key);
                                                    $_title = get_term($_title_id[2]);
                                                    $v = $_meta_->meta_value;
                                                    $_pair = ['meta' => $_title->name , 'value' => $v, 'is_submeta' => true, 'meta_id' => $_title->term_id];

                                                    $_meta_type = get_term_meta($_title->term_id, 'socialdb_property_data_widget', true);

                                                    if(is_numeric($v) && $_meta_type !== "numeric" ) {
                                                        $relation_meta_post = get_post($v);
                                                        if( !is_null($relation_meta_post) ) {
                                                            $_pair['value'] = $relation_meta_post->post_title;
                                                        }
                                                    }

                                                    if( $_pair['value'] != "" && ! empty($_pair['value']) ) {
                                                        $press['inf'][] = $_pair;
                                                        $aux_arr[] = $_title->name . "__" . $v;
                                                        $ord_list[$_pair['meta_id']] = $_pair;
                                                    }

                                                    if( !empty($_pair['value']) && !is_null($_pair['value'])) {
                                                        array_push($current_submeta_vals, $_pair['value']);
                                                    }

                                                    if( $is_compound_meta && empty($current_submeta_vals) && $last_meta_id > 0) {
                                                        unset($press['inf'][$total_index]);
                                                    }

                                                    $press['meta_ids'][] = $_title->term_id;

                                                } else {
                                                    $_curr_term = get_term($curr_term_metas[$curr_meta]);
                                                    $_pair = ['meta' => $_curr_term->name , 'value' => "--", 'is_submeta' => true, 'meta_id' => $_curr_term->term_id];

                                                    $post_val = $this->get_tab_name(intval($s_meta));
                                                    if( !is_null($post_val) && $post_val ) {
                                                        $_pair['value'] = $post_val;
                                                    }

                                                    $press['meta_ids'][] = $_curr_term->term_id;
                                                    $press['inf'][] = $_pair;
                                                    $ord_list[$_pair['meta_id']] = $_pair;
                                                }
                                            } else {
                                                $_title_id = explode("_", $_meta_->meta_key);
                                                $_title = get_term($_title_id[2])->name;

                                                $cat_check = explode("_", $s_meta);
                                                if(count($cat_check) == 2 && $cat_check[1] === "cat") {
                                                    $compounds_metas_titles = get_term_meta($col_meta->term_id, 'socialdb_property_compounds_properties_id', true);
                                                    $titles_ids_arr = explode(",", $compounds_metas_titles);
                                                    $string_title = get_term($titles_ids_arr[$curr_meta])->name;
                                                    $_term_name_ = get_term(intval($cat_check[0]))->name;
                                                    $_pair = ['meta' => $string_title, 'value' => $_term_name_, 'is_submeta' => true, 'meta_id' => get_term($titles_ids_arr[$curr_meta])->term_id];

                                                    $press['inf'][] = $_pair;
                                                    $ord_list[$_pair['meta_id']] = $_pair;
                                                    $press['meta_ids'][] = get_term($titles_ids_arr[$curr_meta])->term_id;
                                                    $aux_arr[] = $_title . "__" . $_term_name_;
                                                }
                                            }

                                            $curr_meta++;
                                        } // submetas loop
                                    }
                                } else {
                                    $_pair = ['meta' => $col_meta->name, 'value' => $val[0], 'meta_id' => $col_meta->term_id];

                                    $_meta_type = get_term_meta($col_meta->term_id, 'socialdb_property_data_widget', true);
                                    if("date" === $_meta_type) {
                                        $_pair['value'] = date('d/m/Y', strtotime($_pair['value']));
                                    } else if ("textarea" === $_meta_type) {
                                        $_pair["meta_breaks"] = $this->get_item_line_breaks($val[0]);
                                    }

                                    if(is_numeric($val[0]) && "numeric" !== $_meta_type) {
                                        $_check_text = get_post($val[0]);
                                        if( !is_null($_check_text)) {
                                            if( in_array($_check_text->post_title, $_item_meta['socialdb_object_commom_values']) ) {
                                                $_pair['value'] = $_check_text->post_title;
                                            }
                                        }
                                    }

                                    // $press['ctn'][] = [$_pair, 'tipo' => $_meta_type];
                                    $press['meta_ids'][] = $col_meta->term_id;
                                    $press['inf'][] = $_pair;
                                    $ord_list[$_pair['meta_id']] = $_pair;
                                }
                            } else {
                                $press['set'][] = $col_meta;
                            }
                        } /* else { $press['excluded'][] = $meta; } */
                    }

                    if($is_compound_meta && empty($current_submeta_vals) && $last_meta_id > 0) {
                        array_push($_to_be_removed, $total_index);
                    }
                    $total_index++;
                }

                if( ! is_null($press['meta_ids']) ) {
                    $press['meta_ids'] = array_unique($press['meta_ids']);
                }


                $s = [];
                $aux_ids = [];
                if( isset($press['inf']) ) {
                    $init = 0;
                    foreach ($press['inf'] as $_m_arr) {
                        $_item_pair = $_m_arr['meta'] . "__" . $_m_arr['value'];

                        if (!is_null($aux_arr)) {
                            if( in_array($_item_pair, $aux_arr) ) {
                                if( is_null($_m_arr['is_submeta'])) {
                                    unset( $press['inf'][$init] );
                                } else {
                                    $aux_ids[] = $init;
                                }
                            }
                        }

                        $_curr_header_idx = $_m_arr['header_idx'];
                        if( isset($_curr_header_idx) && is_int($_curr_header_idx) ) {
                            if( in_array($_curr_header_idx, $_to_be_removed) ) {
                                unset( $press['inf'][$init] );
                            } else {
                                $aux_ids[] = $init;
                            }
                        }

                        if( !is_null($press['inf'][$init]['meta_id']) )
                            array_push($s, $press['inf'][$init]['meta_id']);

                        $init++;
                    }
                }

                $info_ordenada = [];
                $info_desord = [];
                $add_ordered = [];
                foreach($press['meta_ids_ord'] as $oid) {
                    $curr_id = explode("compounds-", $oid);
                    $id = $curr_id[0];

                    if(count($curr_id) == 2)
                        $id = $curr_id[1];

                    if( array_key_exists($id, $ord_list) ) {
                        $info_ordenada[] = $ord_list[$id];
                        array_push($add_ordered, $id);
                    } else {
                        $info_desord[] = $ord_list[$id];
                    }

                }

                $orders_id_keys = array_keys($ord_list);
                foreach ($orders_id_keys as $desorder_ids ) {
                    if(! in_array($desorder_ids, $add_ordered) ) {
                        $info_desord[] = $ord_list[$desorder_ids];
                    }
                }

                $press['inf'] = array_merge($info_ordenada, $info_desord);

                return json_encode($press);

            case 'change_item_author':
                $update_data = ['ID'=> intval($data["item_id"]), 'post_author' => intval($data['new_author']) ];
                $ret['v'] = wp_update_post($update_data);
                return json_encode($ret);

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
                    } else if ($mode == 'one') {
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
                if (isset($data['delete_draft'])) {
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
                        'event_type' => 'user_items', 'event' => 'download', 'resource_id' => $data['thumb_id']];
                    Log::addLog($logData);
                    add_post_meta($data['thumb_id'], 'socialdb_user_download_' . time(), get_current_user_id());
                }
                return true;
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
                $collection_id = $data['collection_id'];
                $socialdb_collection_attachment = get_post_meta($data['collection_id'], 'socialdb_collection_attachment', true);
                $data = $object_model->edit($newItem, $data['collection_id']);
                $data['object_name'] = $object_name;
                $data['collection_id'] = $collection_id;
                $data['socialdb_collection_attachment'] = $socialdb_collection_attachment;
                $data['socialdb_object_from'] = get_post_meta($data['object']->ID, 'socialdb_object_from', true);
                $data['socialdb_object_dc_source'] = get_post_meta($data['object']->ID, 'socialdb_object_dc_source', true);
                $data['socialdb_object_content'] = get_post_meta($data['object']->ID, 'socialdb_object_content', true);
                $data['socialdb_object_dc_type'] = get_post_meta($data['object']->ID, 'socialdb_object_dc_type', true);
                return $this->render(dirname(__FILE__) . '../../../views/object/edit_item_text.php', $data);
                break;
            case 'duplicate_item_other_collection':
                $collection_id = $data['collection_id'];
                $item = get_post($data['object_id']);
                $category_root_id = $object_model->get_category_root_of($data['collection_id']);
                $newItem = $object_model->copyItem($item, $data['new_collection_id']);
                $metas = get_post_meta($item->ID);
                $object_model->copyItemMetas($newItem, $metas, false);
                //$object_model->copyItemCategories($newItem, $data['object_id'], $category_root_id);
                $object_model->copyItemCategoriesOtherCol($newItem, $data['object_id'], $category_root_id);
                //$object_model->copyItemTags($newItem, $data['object_id']);
                $data['collection_id'] = $collection_id;
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
                    return $newItem;
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
                $original = get_post_meta($data['version_id'], 'socialdb_version_postid', true);
                if ($original) {
                    //E uma versao
                    $result = $object_model->send_version_to_trash($data['version_id']);
                } else {
                    //E o item original
                }
                break;
            case 'restore_version':
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
            case 'default_img':
                $curr_id = $data['curr_id'];
                return(get_item_thumb_image($curr_id));
                break;
            case 'eliminate_itens':
                if(isset($data['ids']) && is_array($data['ids']) && is_user_logged_in()){
                    foreach ($data['ids'] as $id) {
                        wp_delete_post($id);
                    }
                }
                $data['title'] = __('Success','tainacan');
                $data['msg'] = __('Operation is successfully','tainacan');
                $data['type'] = 'success';
                return json_encode($data);
            case 'search-items':
                $result = [];
                $items = $object_model->searchItemCollection($data['collection_id'],trim($data['term']));
                foreach ($items as $item) {
                   $result[] = ['value'=>$item->post_title,'label'=>$item->post_title,'item_id'=>$item->ID] ;
                }
                return json_encode($result);
        }
    }

	private function get_item_line_breaks($text) {
		$total_br = 0;
		if( strlen($text) > 0 ) {
            $_desc_pieces = str_replace(PHP_EOL, "-----------", $text);  // ↵
            $_desc_pieces = explode("-----------", $_desc_pieces);

            $total_br = count($_desc_pieces);
		}

		return $total_br;
	}

	private function format_item_thumb($_thumb_id) {
        $img_URL = false;

        if( is_array($_thumb_id) ) {
            $img_URL = get_post($_thumb_id[0])->guid;
        } else if( is_string($_thumb_id) ) {
            $img_URL = get_post($_thumb_id)->guid;
        }

        if($img_URL) {
            $img_check = wp_check_filetype($img_URL);
            $file_archive = @file_get_contents($img_URL);

            if($file_archive) {
                $b64_img = base64_encode($file_archive);
                return [
                    'url' => "data:" . $img_check['type'] . ";base64," . $b64_img,
                    'ext' => $img_check['ext']
                ];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function get_tab_name($tab_id) {
        global $wpdb;

        if( intval($tab_id) <= 0) {
            return false;
        }

        $meta = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE meta_id=%d", $tab_id));

        if( is_null($meta) || empty($meta)) {
            return false;
        }

        return $meta;
    }

    private function get_tab_id($collection, $tab_name) {
        global $wpdb;

        if( ctype_digit($collection) && !empty($tab_name) ) {
            $meta = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE post_id=%d AND meta_key='socialdb_collection_tab' AND meta_value=%s", $collection, $tab_name));

            if( !is_null($meta) ) {
                return $meta;
            }
        }

        return false;
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
if($_POST['operation_priority']){
    $operation = $_POST['operation_priority'];
    $data = $_POST;
}else if ($_POST['operation']) {
    $operation = $_POST['operation'];
    $data = $_POST;
} else {
    $operation = $_GET['operation'];
    $data = $_GET;
}

$object_controller = new ObjectController();
echo $object_controller->operation($operation, $data);
?>
