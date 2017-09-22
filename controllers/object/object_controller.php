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
                    update_post_meta( $data['ID'], 'socialdb_object_dc_type', 'text');
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

                //sessao
                if(!session_id()) {
                        session_start();
                }

                $_SESSION['operation-form'] = $data['operationForm'];
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
//                $data['properties'] = $object_model->show_object_properties($data);
//                $data['items'] = $objectfile_model->get_files($data);
//                if ($data['items'] && empty(!$data['items'])) {
//                    return $this->render(dirname(__FILE__) . '../../../views/object/multiple_items/editor_items.php', $data);
//                } else {
//                    return 0;
//                }
                include_once dirname(__FILE__) . '../../../views/object/formItemMultiple/formItemMultiple.class.php';
                $class = new FormItemMultiple($data['collection_id'],__('Add new item - Send local file', 'tainacan'),'add-files');
                $data['properties'] = $object_model->show_object_properties($data);
                $data['items'] = $objectfile_model->create_item_by_files($data);
                $class->start($data['items'], $data['properties']);
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
                    //return $this->render(dirname(__FILE__) . '../../../views/object/multiple_social_network/editor_items.php', $data);
                    include_once dirname(__FILE__) . '../../../views/object/formItemMultiple/formItemMultiple.class.php';
                    $class = new FormItemMultiple($data['collection_id'],__('Add new item - Insert URL', 'tainacan'),'add-social-network');
                    $class->start($data['items'], $data['properties']);
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
                    //return $this->render(dirname(__FILE__) . '../../../views/object/multiple_social_network/editor_items.php', $data);
                    include_once dirname(__FILE__) . '../../../views/object/formItemMultiple/formItemMultiple.class.php';
                    $class = new FormItemMultiple($data['collection_id'],__('Continue editting...  Insert URL', 'tainacan'),'add-social-network-beta');
                    $class->start($data['items'], $data['properties']);
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
                $recover_wpquery['posts_per_page'] = $args['posts_per_page'];
                $start = microtime(true);
                $data['loop'] = new WP_Query($args);
                $return['wpquerytime'] = microtime(true) - $start;
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
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data ) ;
                $return['args'] = serialize($recover_wpquery);
                $return['preset_order'] = $recover_wpquery['order'];
                $return['items_per_page'] = $args['posts_per_page'];

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
                $data['trash_list'] = true;
                $data['collection_data'] = $collection_model->get_collection_data($collection_id);
                $data["show_string"] = is_root_category($collection_id) ? __('Showing collections:', 'tainacan') : __('Showing Items:', 'tainacan');
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
                /* if (mb_detect_encoding($return['page'], 'auto') == 'UTF-8') { $return['page'] = iconv('ISO-8859-1', 'UTF-8', utf8_decode($return['page'])); } */
                return json_encode($return);
            case 'restore_object':
                return $object_model->restoreItem($data['object_id']);
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
                $object_id = $data['object_id'];
                $press = $object_model->getPDFBase($object_id);
                $press["breaks"] = $this->get_item_line_breaks($press["desc"]); // Pegar # de brs

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

                $tabs = $object_model->getItemTabs($data['collection_id']);
                $press['meta_ids_ord'] = explode(",",unserialize($tabs['order'][0])['default']);
                if($tabs['organize']) {
                    foreach($tabs["organize"] as $id => $tb) {
                        $mt = "socialdb_property_helper_${id}";
                        if( !array_key_exists($mt, $_item_meta)) {
                            $_item_meta[$mt] = ["--"];
                        }
                    }
                }

                $ord_list = [];
                foreach ($_item_meta as $meta => $val) {
                    $check_typeof_meta = explode( "_", $meta);

                    if ( is_string($meta) && ($check_typeof_meta[0] . $check_typeof_meta[1]) == "socialdbproperty" ) {
                        $prop = unserialize( get_post_meta($object_id, $meta)[0] );
                        $_meta_header_ = get_term($check_typeof_meta[3]);

                        if( is_array($prop) && count($prop) > 0 ) {
                            for ($i = 0; $i < count($prop); $i++) {
                                if(array_key_exists('0', $prop[$i])) {
                                    $ff_ID = $prop[$i][0]['values']['0'];
                                    $_meta_ = $this->sdb_get_post_meta($ff_ID);
                                    $final_val = $_meta_->meta_value;

                                    $_fmt_ID = str_replace("socialdb_property_", "", $_meta_->meta_key);
                                    if( strpos($_fmt_ID,"_cat") )
                                        $_fmt_ID = explode("_", $_fmt_ID)[0];

                                    $previous_term_id = 0;
                                    if($prop[$i][0]['type'] === "term") {
                                        $previous_term_id = $final_val;
                                        $final_val = get_term($final_val)->name;
                                    } else if($prop[$i][0]['type'] === "object") {
                                        $final_val = get_post($final_val)->post_title;
                                    }

                                    $_pair = [ 'meta' => $_meta_header_->name, 'value' => $final_val,
                                        'meta_id' => $_fmt_ID, 'meta_breaks' => $this->format_to_type($_fmt_ID, $final_val)];

                                    $_compound_check = get_term_meta($_fmt_ID, "socialdb_property_compounds_properties_id", true);
                                    if( empty($_compound_check) ) {
                                        $chk_compound_child = unserialize(get_term_meta($_fmt_ID, "socialdb_property_is_compounds", true));
                                        if(is_array($chk_compound_child)) {
                                            if( isset( $tabs['organize'][key($chk_compound_child)] ) && ($tabs['organize'][key($chk_compound_child)] != "default") ) {
                                                $_pair['submeta_tab_parent'] = key($chk_compound_child);
                                                $_pair['is_submeta'] = true;
                                            }
                                        } else {
                                            $_pair['is_submeta'] = false;
                                        }
                                    }

                                    $_meta_category_metas_ = get_term_meta( (int) $previous_term_id,'socialdb_category_property_id');
                                    if(is_array($_meta_category_metas_) && count($_meta_category_metas_) > 0) {
                                        foreach ($_meta_category_metas_ as $id) {
                                            $extra_helper = "socialdb_property_helper_${id}";
                                            $nome = get_term_by("id",$id,"socialdb_property_type");

                                            $helper = get_post_meta($object_id, $extra_helper);
                                            if(is_array($helper)) {
                                                $extra = unserialize(($helper[0]));
                                                if(is_array($extra) && !empty($extra)) {
                                                    $extra_id = $extra[0][0]["values"][0];

                                                    if( is_null($extra_id) && is_array($extra[0]) && !empty($extra[0]) ) {
                                                        $_compound_check = get_term_meta($id, "socialdb_property_compounds_properties_id", true);
                                                        $is_compound = !empty($_compound_check);
                                                        if($is_compound) {
                                                            $_pair['extras'][] = ['meta' => $nome->name, 'value' => '_____________________________', 'meta_id' => $id];

                                                            $count = 1;
                                                            foreach ($extra[0] as $child_id => $extra_children) {
                                                                $child_title = get_term($child_id)->name;
                                                                $_val_ = ".";
                                                                $set_val = $this->sdb_get_post_meta($extra_children["values"][0]);

                                                                if($extra_children["type"] === "object") {
                                                                    $_val_ = get_post($set_val->meta_value)->post_title;
                                                                } else if($extra_children["type"] === "term") {
                                                                    $_val_ = get_term($set_val->meta_value)->name;
                                                                }

                                                                $_pair['extras'][] = ['meta' => $child_title, 'value' => $_val_, 'meta_id' => $child_id, 'extra_submeta' => true, 'extra_padding' => ($count*20)];
                                                                $count++;
                                                            }
                                                        }
                                                    } else {
                                                        $extra_res = $this->sdb_get_post_meta($extra_id);
                                                        $_pair['extras'][] = ['meta' => $nome->name, 'value' => $extra_res->meta_value, 'meta_id' => $id];
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    $ord_list[$_pair['meta_id']] = $_pair;
                                    $press['set'][] = $_pair;

                                } else {
                                    $_pair = ['meta' => $_meta_header_->name, 'meta_id' => $_meta_header_->term_id,
                                        'value' => '_____________________________', 'submeta_header' => true ];

                                    $ord_list[$_pair['meta_id']] = $_pair;
                                    $press['set'][] = $_pair;

                                    foreach ($prop[$i] as $child_id => $child_data) {
                                        $child_term = get_term($child_id);
                                        $main_val = $child_data["values"][0];
                                        $m_val = $this->sdb_get_post_meta($main_val);
                                        $final_val = $m_val->meta_value;

                                        if( "term" == $child_data["type"] ) {
                                            $m_val = get_term($final_val);
                                            $final_val = $m_val->name;
                                        }

                                        $_pair = [ 'meta' => $child_term->name, 'value' => $final_val, 'meta_id' => $child_id,
                                            'is_submeta' => true, 'meta_breaks' => $this->format_to_type($child_id, $final_val) ];
                                                
                                        $ord_list[$_pair['meta_id']] = $_pair;
                                        $press['set'][] = $_pair;
                                    }
                                }
                            }
                                
                            // Apenas se o valor dos metadados estiverem vazios
                        } else if( !is_null($check_typeof_meta[3]) && ctype_digit($check_typeof_meta[3]) ) {
                            $_compound_check = get_term_meta($_meta_header_->term_id, "socialdb_property_compounds_properties_id", true);
                            $is_compound = !empty($_compound_check);

                            $_pair = ['meta' => $_meta_header_->name, 'value' => '--', 'meta_id' => $_meta_header_->term_id];

                            if($is_compound) {
                                $_pair['value']          = '_____________________________';
                                $_pair['submeta_header'] = true;
                                $_pair['children']       = $_compound_check;
                            } else {
                                $chk_compound_child = unserialize(get_term_meta($_meta_header_->term_id, "socialdb_property_is_compounds", true));
                                if(is_array($chk_compound_child)) {
                                            if( isset( $tabs['organize'][key($chk_compound_child)] ) && ($tabs['organize'][key($chk_compound_child)] != "default") ) {
                                                $_pair['submeta_tab_parent'] = key($chk_compound_child);
                                                $_parent_set_values = get_post_meta($object_id,"socialdb_property_helper_" . $_pair['submeta_tab_parent'],true);
                                                if(is_string($_parent_set_values) && !empty($_parent_set_values) ) {
                                                    $_parent_values_arr = unserialize($_parent_set_values);

                                                    if(is_array($_parent_values_arr[0])) {
                                                        foreach ($_parent_values_arr[0] as $_val_arr) {
                                                            if($_val_arr["type"] === "object") {
                                                               $fn =  $this->sdb_get_post_meta($_val_arr["values"][0]);
                                                               $prev = get_post($fn->meta_value);
                                                               $_pair['value'] = $prev->post_title;
                                                            }
                                                        }
                                                    }
                                                }
                                            } else {
                                                $check = ($tabs['organize'][key($chk_compound_child)] == "default");
                                                $_meta_parent_ = key($chk_compound_child);

                                                if($check) {
                                                    $par_vals = get_post_meta($object_id,"socialdb_property_helper_" . $_meta_parent_,true);
                                                    if(!empty($par_vals)) {
                                                        $par_arr = unserialize($par_vals);
                                                        $_current = $par_arr[0][$_pair['meta_id']];
                                                        if( is_array($_current) && !empty($_current) ) {
                                                            $children_vals = $this->sdb_get_post_meta($_current["values"][0]);
                                                            if(is_object($children_vals)) {
                                                                if($_current["type"] === "data") {
                                                                    $_pair['value'] = $children_vals->meta_value;
                                                                } else if ($_current["type"] === "term") {
                                                                    $_pair['value'] =  get_term($children_vals->meta_value)->name;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            $_pair['is_submeta'] = true;
                                        }
                            }

                            $ord_list[$_pair['meta_id']] = $_pair;
                            $press['set'][] = $_pair;
                        }
                    }
                }

                if(!is_null($press['meta_ids']))
                    $press['meta_ids'] = array_unique($press['meta_ids']);

                $tabs_unodr = [];
                if($press['set']) {
                    foreach ($press['set'] as $set_info) {
                        $mID = $set_info['meta_id'];
                        if( isset( $tabs['organize'][$mID]) && ($tabs['organize'][$mID] != "default") && ctype_digit($tabs['organize'][$mID]) ) {
                            if( !isset($set_info['submeta_header']) ) {
                                array_push($tabs_unodr, $set_info);
                            }
                        } else {
                            if( isset($set_info['is_submeta']) && $set_info['is_submeta'] && isset($set_info['submeta_tab_parent'])) {
                                if( isset( $tabs['organize'][$set_info['submeta_tab_parent']] ) ) {
                                    if(!in_array( $ord_list[$set_info['submeta_tab_parent']], $tabs_unodr))
                                        array_push($tabs_unodr, $ord_list[$set_info['submeta_tab_parent']]);

                                    array_push( $tabs_unodr, $set_info);
                                }
                            }
                        }
                    }
                }

                $final_ordered = [];
                foreach ($press['meta_ids_ord'] as $mio) {
                    $format_id = str_replace("compounds-", "", $mio);
                    if(array_key_exists($format_id, $ord_list)) {
                        array_push($final_ordered, $ord_list[$format_id]);
                    }
                }

                if(!empty($tabs_unodr))
                    $press['set'] = array_merge($final_ordered, $tabs_unodr);

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
                    $args['post_status'] = 'inherit';
                    $result = get_posts($args);
                }
                if (empty($result) || !isset($result)) {
                    $args['post_status'] = 'draft';
                    $result = get_posts($args);
                }

                if (count($result) > 0 && isset($result[0]) && in_array($result[0]->post_status, array('publish', 'inherit','draft'))) {
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
                    $data['ID'] = get_user_meta(get_current_user_id(), 'socialdb_collection_' . $data['collection_id'] . '_betatext',true);
                    delete_user_meta(get_current_user_id(), 'socialdb_collection_' . $data['collection_id'] . '_betatext');
                    delete_user_meta(get_current_user_id(), 'socialdb_collection_' . $data['collection_id'] . '_betafile');
                    if(is_numeric($data['ID']))
                        return $object_model->delete($data);
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
                $data['properties'] = $object_model->show_object_properties($data);
                $data['items'] = $objectfile_model->get_inserted_items_social_network($data);
                $data['edit_multiple'] = true;
                if ($data['items'] && empty(!$data['items'])) {
                    //return $this->render(dirname(__FILE__) . '../../../views/object/multiple_social_network/editor_items.php', $data);
                    include_once dirname(__FILE__) . '../../../views/object/formItemMultiple/formItemMultiple.class.php';
                    $class = new FormItemMultiple($data['collection_id'],__('Edit items', 'tainacan'),'edit-items');
                    $class->start($data['items'], $data['properties']);
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
                    return $newItem;
                } else {
                    return false;
                }
                break;
            case 'show_item_versions':
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

    private function format_to_type($meta_id, $meta_value) {
        $_meta_type = get_term_meta($meta_id, 'socialdb_property_data_widget', true);

        if( !empty($_meta_type) ) {
            if("date" === $_meta_type) {
                return date('d/m/Y', strtotime($meta_value) );
            } else if ("textarea" === $_meta_type) {
                return $this->get_item_line_breaks($meta_value);
            }
        }

        return 0;
    }

    private function get_tab_name($tab_id) {
        global $wpdb;

        if( intval($tab_id) <= 0)
            return false;

        $meta = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE meta_id=%d", $tab_id));

        if( is_null($meta) || empty($meta) )
            return false;

        return $meta;
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

    private function sdb_get_post_meta($meta_id) {
        global $wpdb;
        $query = "SELECT * FROM $wpdb->postmeta WHERE meta_id = $meta_id";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result)) {
            return $result[0];
        } elseif ($result && isset($result->ID)) {
            return $result;
        } else {
            return false;
        }
    }

    public function get_collection_by_item($object_id) {
        $categories = wp_get_object_terms($object_id, 'socialdb_category_type');
        foreach ($categories as $category) {
            $result = $this->get_collection_by_category_root($category->term_id);
            if (!empty($result)) {
                return $result;
            }
        }
    }

    public function get_collection_by_category_root($category_root_id) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "SELECT p.* FROM $wp_posts p INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id    
                  WHERE pm.meta_key LIKE 'socialdb_collection_object_type' and pm.meta_value like '$category_root_id'";
        $result = $wpdb->get_results($query);

        if ($result && is_array($result) && count($result) > 0) {
            return $result;
        } else {
            return array();
        }
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
