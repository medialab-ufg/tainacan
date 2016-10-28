<?php
require_once(dirname(__FILE__) . '../../../models/collection/collection_model.php');
require_once(dirname(__FILE__) . '../../../models/collection/collection_parent_model.php');
require_once(dirname(__FILE__) . '../../../models/collection/collection_import_model.php');
require_once(dirname(__FILE__) . '../../../models/collection/collection_templates_model.php');
require_once(dirname(__FILE__) . '../../../models/collection/visualization_model.php');
require_once(dirname(__FILE__) . '../../../models/property/property_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');
include_once (dirname(__FILE__) . '../../../models/event/event_collection/event_collection_create_model.php');

class CollectionController extends Controller {

    public function operation($operation, $data) {
        $collection_model = new CollectionModel();
        $collection_parent_model = new CollectionParentModel();
        $visualization_model = new VisualizationModel();
        switch ($operation) {
            case "initDynatree":
                return $visualization_model->initDynatree($data);
            case "initDynatreeSynonyms":
                return $visualization_model->initDynatreeSynonyms($data);
            case "initDynatreeTags":
                return $visualization_model->initDynatreeTags($data);
            case "initDynatreeSingleEdit":
                return $visualization_model->initDynatreeSingleEdit($data);
            case "expand_dynatree":
                return json_encode($visualization_model->expandDynatree($data));
            case "create":
                return $collection_model->create();
            case 'simple_add':
                $data['collection_name'] = trim($data['collection_name']);
                $data['collection_object'] = trim($data['collection_object']);
                
                if(empty($data['collection_name'])||empty($data['collection_object'])):
                    header("location:" . get_permalink(get_option('collection_root_id')) . '?info_messages=' . __('Invalid collection name or object name!','tainacan') . '&info_title=' . __('Attention','tainacan'));
                elseif (is_user_logged_in()):
                    if($data['template']=='none'):
                        $new_collection_id = $collection_model->simple_add($data);
                        if ($new_collection_id) {
                            $result = json_decode($this->insert_collection_event($new_collection_id, $data));
                            if ($result->type == 'success') {
                                header("location:" . get_permalink($new_collection_id) . '?open_wizard=true');
                            } else {
                                header("location:" . get_permalink(get_option('collection_root_id')) . '?info_messages=' . __('Collection sent for approval','tainacan') . '&info_title=' . __('Attention','tainacan'));
                            }
                        } else {
                            header("location:" . get_permalink(get_option('collection_root_id')) . '?info_messages=' . __('Collection already exists','tainacan') . '&info_title=' . __('Attention','tainacan'));
                        }
                    else:    
                        $import_model = new CollectionImportModel;
                        $new_collection_id = $import_model->importCollectionTemplate($data);                       
                        
                        if($new_collection_id) {
                            // $result = json_decode($this->insert_collection_event($new_collection_id, $data));
                            return ( json_decode($this->insert_collection_event($new_collection_id, $data)) );                           
                            
                            if ($result->type == 'success') {
                                header("location:" . get_permalink($new_collection_id) . '?open_wizard=true');
                            } else {
                                header("location:" . get_permalink(get_option('collection_root_id')) . '?info_messages=' . __('Collection sent for approval','tainacan') . '&info_title=' . __('Attention','tainacan'));
                            }
                        } else {
                            return ['error' => __('Error creating template collection', 'tainacan') ];
                        }
                    endif;
                else:
                    header("location:" . get_permalink(get_option('collection_root_id')) . '?info_messages=' . __('You must be logged in to create collecions','tainacan') . '&info_title=' . __('Attention','tainacan'));
                endif;
                break;
            case "add":
                return $collection_model->add($data);
            case "edit":
                return $collection_model->edit($data);
            case "update":
                if (isset($data['save_and_next']) && $data['save_and_next'] == 'true') {
                    $data['next_step'] = true;
                } else {
                    $data['next_step'] = false;
                }
                
                $data['update'] = $collection_model->update($data);
                $data['is_moderator'] = CollectionModel::is_moderator($data['collection_id'], get_current_user_id());
                return json_encode($data);
            case "delete":
                return $collection_model->delete($data);
            case "list":
                return $collection_model->list_collection();
            case "show_header":
                $mycollections = $data['mycollections'];
                $sharedcollections = $data['sharedcollections'];
                $data = $collection_model->get_collection_data($data['collection_id']);
                $data['mycollections'] = $mycollections;
                $data['sharedcollections'] = $sharedcollections;
                $data['json_autocomplete'] = $collection_model->create_main_json_autocomplete($data['collection_post']->ID);
                return $this->render(dirname(__FILE__) . '../../../views/collection/header_collection.php', $data);
                // break;
            case "edit_configuration":
                if (is_user_logged_in()) {
                    $data = $collection_model->get_collection_data($data['collection_id']);
                    return $this->render(dirname(__FILE__) . '../../../views/collection/edit.php', $data);
                } else {
                    wp_redirect(get_the_permalink(get_option('collection_root_id')));
                }
                break;
            case "list_ordenation":
                $data = $collection_model->list_ordenation($data, $data['get_all_meta']);

                $data['names']['general_ordenation'] = __('General Ordenation','tainacan');
                $data['names']['data_property'] = __('Property Data','tainacan');
                $data['names']['ranking'] = __('Rankings','tainacan');
                return json_encode($data);
            case "show_form_data_property":
                return $collection_model->list_ordenation($data);
            case 'list_autocomplete' :
                return json_encode($collection_model->create_main_json_autocomplete($data['collection_id'], $data['term']));
            case "initGeneralJit":
                return $visualization_model->initJit($data);
            case "initTreemapJit":
                return $visualization_model->initTreemapJit($data);
            case "get_collections_json":// pega todos as colecoes e coloca em um array json
                return $this->get_collections_json($data);                
            case 'get_most_participatory_authors':
                $collection_id = $data['collection_id'];
                $data = $collection_model->get_collection_data($collection_id);
                if ($data['collection_metas']['socialdb_collection_most_participatory'] == 'yes') {
                    $data['authors'] = $collection_model->get_most_participatory_authors($collection_id);
                    return $this->render(dirname(__FILE__) . '../../../views/collection/most_participatory_authors.php', $data);
                }
                break;
            case 'get_most_colaborators_authors':
                $collection_id = $data['collection_id'];
                $data['authors'] = $collection_model->get_most_colaborators_authors($collection_id);
                return $this->render(dirname(__FILE__) . '../../../views/collection/most_participatory_authors.php', $data);
                break;
            case 'get_category_property':
                return $collection_model->get_order_category_properties($data);
                break;
            case 'check_privacity':
                return $collection_model->check_privacity($data);
                break;
            case 'verify_name_collection':
                return json_encode($collection_model->verify_name_collection($data));
            case 'delete_collection':
                return $collection_model->delete($data);
            case 'list_collections_parent':
                return json_encode($collection_parent_model->list_collection_parent($data['collection_id']));
            case "show_filters":
                $data = $collection_model->get_filters($data);
                return $this->render(dirname(__FILE__) . '../../../views/collection/filters.php', $data);
                break;
            //index search visualizations
            case "set_container_classes":
                return json_encode($visualization_model->set_container_classes($data));
                break;
             case 'load_menu_left':
                 $data['selected_menu_style_id'] = $this->get_selected_menu_style( $data['collection_id'] );
                 $data['selected_menu_style_json'] = $this->get_menu_style_json( $data['selected_menu_style_id'] );
                 $data['facets'] = $visualization_model->get_facets_visualization($data['collection_id']);
                 $data['has_tree'] = $visualization_model->has_tree($data['collection_id'],'left-column');
                 if($data['has_tree']){
                     $data['tree'] = $visualization_model->get_data_tree($data['collection_id']);
                 }
                 return $this->render(dirname(__FILE__) . '../../../views/search/menu_left.php', $data);
                 break;
            case 'set_collection_cover_img':
                $file_extension = wp_check_filetype($data['img_url']);
                $attachment = [
                    'guid' => $data['img_url'],
                    'post_mime_type' => 'image/' . $file_extension['ext'],
                    'post_title' => '', 'post_content' => '',
                ];
                $img_id = wp_insert_attachment($attachment);

                return update_post_meta($data['collection_id'],'socialdb_collection_cover_id', $img_id );
                break;
            case 'update_collection_thumbnail':
                $file_extension = wp_check_filetype($data['img_url']);
                $file = [ 'guid' => $data['img_url'],
                    'post_mime_type' => 'image/' . $file_extension['ext'],
                    'post_title' => '', 'post_content' => '',
                ];
                $img_id = wp_insert_attachment($file);
                $retorno['updated_thumb_id'] = update_post_meta($data['collection_id'], '_thumbnail_id', $img_id);
                
                return json_encode( $retorno );
            case 'list_items_search_autocomplete':
                $property_model = new PropertyModel;
                $property = get_term_by('id', $data['property_id'], 'socialdb_property_type');
                if ($property) {
                    if(in_array($property->slug, $property_model->fixed_slugs)){
                        if($property->slug==='socialdb_property_fixed_title'):
                            return $visualization_model->get_objects_by_property_json_advanced_search($data);
                        elseif($property->slug==='socialdb_property_fixed_source'):
                           return  $visualization_model->get_data_by_property_json($data,'socialdb_object_dc_source');
                        endif;
                    }else if($property_model->get_property_type($property->term_id)=='socialdb_property_object'){
                        return  $visualization_model->get_objects_by_property_json($data);
                    }else{
                        return  $visualization_model->get_data_by_property_json($data);
                    }
                }else{
                    return  $visualization_model->get_terms_by_property_json($data);
                }
            case 'list_items_search_autocomplete_advanced_search':
                return $visualization_model->get_objects_by_property_json_advanced_search($data);
            /********************* Visibilidade **********************/  
            case 'alter_visibility':
                $meta = get_post_meta($data['collection_id'], 'socialdb_collection_fixed_properties_visibility', true);
                if($meta&&$meta!=''):
                    $array = explode(',', $meta);
                    if(is_array($array)&& count($array)>0 &&  ($key = array_search($data['property_id'], $array)) !== false):
                        unset($array[$key]);
                    elseif(is_array($array)):  
                        $array[] = $data['property_id'];
                    endif;
                else:
                   $array = [];
                   $array[] = $data['property_id']; 
                endif;
                update_post_meta($data['collection_id'], 'socialdb_collection_fixed_properties_visibility', implode(',', $array));
                break;
            /********************* IMPORTACAO DE COLECAO **********************/
            case 'importCollection':
                $collectionImportation = new CollectionImportModel;
                return json_encode($collectionImportation->import($data));
            /*************************** TEMPLATES **********************/
            case 'list-collection-templates':
                $colectionTemplateModel = new CollectionTemplatesModel;
                $data['collectionTemplates'] = $colectionTemplateModel->get_collections_templates();
                if(!isset($data['is_json'])){
                     return $this->render(dirname(__FILE__) . '../../../views/collection/list-collection-templates.php', $data);
                }else{
                    return json_encode( $data['collectionTemplates']);
                }
                break;
            case 'add_collection_template' :
                $colectionTemplateModel = new CollectionTemplatesModel;
                return $colectionTemplateModel->add_collection_template($data);
            case 'delete_collection_template' :
                $colectionTemplateModel = new CollectionTemplatesModel;
                return $colectionTemplateModel->delete_collection_template($data);
            /************************* Tabs ***********************************/
            case 'alter_tab_name':
                 if($data['id']!='default'){
                     $collection_model->sdb_update_post_meta($data['id'], $data['name']);
                 }else{
                     update_post_meta($data['collection_id'], 'socialdb_collection_default_tab', $data['name']);
                 }
                break;
            case 'get_tabs':
                $default_tab = get_post_meta($data['collection_id'], 'socialdb_collection_default_tab', true);
                $tabs['default'] = (!$default_tab) ? __('Default', 'tainacan') : $default_tab ;
                $tabs['array'] = $collection_model->sdb_get_post_meta_by_value($data['collection_id'], 'socialdb_collection_tab');
                if($tabs &&  is_array($tabs)){
                    return json_encode($tabs);
                }else{
                   return  json_encode([]);
                }
            case 'insert_tab':
                $data['id'] = $collection_model->sdb_add_post_meta($data['collection_id'], 'socialdb_collection_tab', $data['tab_name']);
                return json_encode($data);
            case 'remove_tab':
                $collection_model->realocate_tabs_collection($data['id'], $data['collection_id']);
                $collection_model->sdb_delete_post_meta($data['id']);
                return json_encode($data);
            /********************** ordenacao dos metadados *******************/
            case 'update_ordenation_properties':
                $meta = unserialize(get_post_meta($data['collection_id'], 'socialdb_collection_properties_ordenation', true));
                if(isset($data['tab'])){
                     $index = ($data['tab']=='false')? 'default' : $data['tab'];
                     $array = (is_array($meta)) ? $meta : [];
                     $array[$index] = $data['ordenation'];
                     update_post_meta($data['collection_id'], 'socialdb_collection_properties_ordenation', serialize($array));
                }
                break;
            case 'get_ordenation_properties':
                $meta = unserialize(get_post_meta($data['collection_id'], 'socialdb_collection_properties_ordenation', true));
                if(!$meta||$meta==''||$data['tab']){
                     $data['ordenation'] = '';
                     return json_encode($data);
                }
                foreach ($meta as $tab_id => $string) {
                    $ids = explode(',',$string);
                    $new_ids = [];
                    foreach ($ids as $id) {
                       if(is_numeric($id)){
                           $new_ids[] = 'meta-item-'.$id;
                       }else{
                            $new_ids[] =$id;
                       }
                    }
                    $data['ordenation'][$tab_id] = implode(',', $new_ids);
                }
                return json_encode($data);
            /************************ Pagina de comentarios *******************/
            case 'comments':
                return json_encode(['html'=> $this->render(dirname(__FILE__) . '../../../views/collection/comments.php', $data)]);
            case 'update_color_schemes':
                return json_encode( $visualization_model->update_color_schemes($data) );
            case 'get_color_schemes':
                return json_encode( $visualization_model->get_color_schemes($data['collection_id']) );
            case 'get_default_color_scheme':
                return json_encode($visualization_model->get_default_color_scheme($data['collection_id']));
        }
    }

    /**
     * @signature - function insert_event($object_id, $data )
     * @param int $object_id O id do Objeto
     * @param array $data Os dados vindos do formulario
     * @return array os dados para o evento
     * @description - 
     * @author: Eduardo 
     */
    public function insert_collection_event($collection_id, $data) {
        $eventAddCollection = new EventCollectionCreateModel();
        $data['socialdb_event_create_collection_id'] = $collection_id;
        $data['socialdb_event_collection_id'] = get_option('collection_root_id');
        $data['socialdb_event_user_id'] = get_current_user_id();
        $data['socialdb_event_create_date'] = mktime();
        return $eventAddCollection->create_event($data);
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

$collection_controller = new CollectionController();
echo $collection_controller->operation($operation, $data);
?>