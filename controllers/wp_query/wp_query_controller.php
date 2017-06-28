<?php
require_once(dirname(__FILE__) . '../../../models/collection/collection_model.php');
require_once(dirname(__FILE__) . '../../../models/wp_query/wp_query_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class WPQueryController extends Controller {

    public function operation($operation, $data) {
        $wpquery_model = new WPQueryModel();
         switch ($operation) {
            case "wpquery_select":
                $return = array();
                if(empty($wpquery_model->get_collection_posts($data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                }
                $collection_model = new CollectionModel;
                $args = $wpquery_model->select_filter($data);
                $collection_id = $args['collection_id'];
                $paramters = $wpquery_model->do_filter($args);
                $data['loop'] =  new WP_Query($paramters);
                $data['collection_data'] = $collection_model->get_collection_data($collection_id);
                $data['listed_by'] = $wpquery_model->get_ordered_name($collection_id, $args['ordenation_id'], $args['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($collection_id, get_current_user_id());
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($collection_id, "socialdb_collection_table_metas", true)));
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                $return['args'] = serialize($args);
//                if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
//                     $return['page'] = iconv('ISO-8859-1', 'UTF-8',  utf8_decode($return['page']));
//                }
                return json_encode($return);
            case "wpquery_radio":
               $return = array();
                if(empty($wpquery_model->get_collection_posts($data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                }
                $collection_model = new CollectionModel;
                $args = $wpquery_model->radio_filter($data);
                $paramters = $wpquery_model->do_filter($args);
                $collection_id = $args['collection_id'];
                $data['loop'] =  new WP_Query($paramters);
                $data['collection_data'] = $collection_model->get_collection_data($collection_id);
                $data['listed_by'] = $wpquery_model->get_ordered_name($collection_id, $args['ordenation_id'], $args['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($collection_id, get_current_user_id());
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($collection_id, "socialdb_collection_table_metas", true)));
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                $return['args'] = serialize($args);
//                if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
//                    $return['page'] = iconv('ISO-8859-1', 'UTF-8',  utf8_decode($return['page']));
//                }
                return json_encode($return);
            case "wpquery_checkbox":
               $return = array();
                if(empty($wpquery_model->get_collection_posts($data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                }
                $collection_model = new CollectionModel;
                $args = $wpquery_model->checkbox_filter($data);
                $paramters = $wpquery_model->do_filter($args);
                $collection_id = $args['collection_id'];
                
                $data['loop'] =  new WP_Query($paramters);
                $data['collection_data'] = $collection_model->get_collection_data($collection_id);               
                $data['listed_by'] = $wpquery_model->get_ordered_name($collection_id, $args['ordenation_id'], $args['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($collection_id, get_current_user_id());               
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($collection_id, "socialdb_collection_table_metas", true)));
                
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                $return['args'] = serialize($args);
//                if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
//                    $return['page'] = iconv('ISO-8859-1', 'UTF-8',  utf8_decode($return['page']));
//                }
                return json_encode($return);
            case "wpquery_multipleselect":
                $return = array();
                if(empty($wpquery_model->get_collection_posts($data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                }
                $collection_model = new CollectionModel;
                $args = $wpquery_model->multipleselect_filter($data);
                $paramters = $wpquery_model->do_filter($args);
                $data['loop'] =  new WP_Query($paramters);
                $data['collection_data'] = $collection_model->get_collection_data($args['collection_id']);
                $data['listed_by'] = $wpquery_model->get_ordered_name($args['collection_id'], $args['ordenation_id'], $args['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($args['collection_id'], get_current_user_id());
                
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($args['collection_id'], "socialdb_collection_table_metas", true)));
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                $return['args'] = serialize($args);
//                if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
//                     $return['page'] = iconv('ISO-8859-1', 'UTF-8',  utf8_decode($return['page']));
//                }
                return json_encode($return);
            case "wpquery_range":
                $return = array();
                if(empty($wpquery_model->get_collection_posts($data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                }
                $collection_model = new CollectionModel;
                $args = $wpquery_model->range_filter($data);
                $paramters = $wpquery_model->do_filter($args);
                $data['loop'] =  new WP_Query($paramters);
                $data['collection_data'] = $collection_model->get_collection_data($args['collection_id']);
                $data['listed_by'] = $wpquery_model->get_ordered_name($args['collection_id'], $args['ordenation_id'], $args['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($args['collection_id'], get_current_user_id());
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($args['collection_id'], "socialdb_collection_table_metas", true)));
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                $return['args'] = serialize($args);
                if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
                    $return['page'] = iconv('ISO-8859-1', 'UTF-8',  utf8_decode($return['page']));
                }
                return json_encode($return);
            case "wpquery_fromto":
                $return = array();
                if(empty($wpquery_model->get_collection_posts($data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                }
                $collection_model = new CollectionModel;
                $args = $wpquery_model->fromto_filter($data);
                $paramters = $wpquery_model->do_filter($args);
                $data['loop'] =  new WP_Query($paramters);
                $data['collection_data'] = $collection_model->get_collection_data($args['collection_id']);
                $data['listed_by'] = $wpquery_model->get_ordered_name($args['collection_id'], $args['ordenation_id'], $args['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($args['collection_id'], get_current_user_id());
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($args['collection_id'], "socialdb_collection_table_metas", true)));
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                $return['args'] = serialize($args);
//                if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
//                    $return['page'] = utf8_decode(iconv('ISO-8859-1', 'UTF-8', $return['page']));
//                }
                return json_encode($return);
            case "wpquery_dynatree":
                $return = array();
                if(empty($wpquery_model->get_collection_posts($data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                }
                $collection_model = new CollectionModel;
                $args = $wpquery_model->dynatree_filter($data);
                $paramters = $wpquery_model->do_filter($args);
                $data['loop'] =  new WP_Query($paramters);
                $data['col_id'] = $args['collection_id'];
                $data['collection_data'] = $collection_model->get_collection_data($args['collection_id']);
                $data['listed_by'] = $wpquery_model->get_ordered_name($args['collection_id'], $args['ordenation_id'], $args['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($args['collection_id'], get_current_user_id());
                $data['is_filtered_page'] = true;
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($args['collection_id'], "socialdb_collection_table_metas", true)));
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                $return['args'] = serialize($args);
//                if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
//                     $return['page'] = iconv('ISO-8859-1', 'UTF-8',  utf8_decode($return['page']));
//                }
                return json_encode($return);
            case "wpquery_cloud":
                $return = array();
                if(empty($wpquery_model->get_collection_posts($data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                }
                $collection_model = new CollectionModel;
                $args = $wpquery_model->cloud_filter($data);
                $paramters = $wpquery_model->do_filter($args);
                $data['loop'] =  new WP_Query($paramters);
                $data['collection_data'] = $collection_model->get_collection_data($args['collection_id']);
                $data['listed_by'] = $wpquery_model->get_ordered_name($args['collection_id'], $args['ordenation_id'], $args['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($args['collection_id'], get_current_user_id());
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($args['collection_id'], "socialdb_collection_table_metas", true)));
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                $return['args'] = serialize($args);
//                if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
//                     $return['page'] = iconv('ISO-8859-1', 'UTF-8',  utf8_decode($return['page']));
//                }
                return json_encode($return);
            case "wpquery_link":
                $return = array();
                if(empty($wpquery_model->get_collection_posts($data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                }
                $collection_model = new CollectionModel;
                $args = $wpquery_model->link_metadata_filter($data);
                $paramters = $wpquery_model->do_filter($args);
                $data['loop'] =  new WP_Query($paramters);
                $data['collection_data'] = $collection_model->get_collection_data($args['collection_id']);
                $data['listed_by'] = $wpquery_model->get_ordered_name($args['collection_id'], $args['ordenation_id'], $args['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($args['collection_id'], get_current_user_id());
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($args['collection_id'], "socialdb_collection_table_metas", true)));
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                $return['args'] = serialize($args);
//                if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
//                     $return['page'] = iconv('ISO-8859-1', 'UTF-8',  utf8_decode($return['page']));
//                }
                return json_encode($return);
            case "wpquery_menu":
                $return = array();
                $collection_model = new CollectionModel;
                $args = $wpquery_model->dynatree_filter($data);
                $paramters = $wpquery_model->do_filter($args);
                $data['loop'] =  new WP_Query($paramters);
                $data['collection_data'] = $collection_model->get_collection_data($args['collection_id']);
                $data['listed_by'] = $wpquery_model->get_ordered_name($args['collection_id'], $args['ordenation_id'], $args['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($args['collection_id'], get_current_user_id());
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($args['collection_id'], "socialdb_collection_table_metas", true)));
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                $return['args'] = serialize($args);
//                if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
//                     $return['page'] = iconv('ISO-8859-1', 'UTF-8',  utf8_decode($return['page']));
//                }
                return json_encode($return);
            case "wpquery_ordenation":
                $return = array();
                if(empty($wpquery_model->get_collection_posts($data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                }
                $collection_model = new CollectionModel;
                $args = $wpquery_model->ordenation_filter($data);
                $paramters = $wpquery_model->do_filter($args);
                $data['loop'] =  new WP_Query($paramters);
                $data['collection_data'] = $collection_model->get_collection_data($args['collection_id']);
                $data['listed_by'] = $wpquery_model->get_ordered_name($args['collection_id'], $args['ordenation_id'], $args['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($args['collection_id'], get_current_user_id());
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($args['collection_id'], "socialdb_collection_table_metas", true)));
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
//                if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
//                     $return['page'] = iconv('ISO-8859-1', 'UTF-8',  utf8_decode($return['page']));
//                }
                $return['args'] = serialize($args);
                return json_encode($return);
            case "wpquery_orderby":
                $return = array();
                if(empty($wpquery_model->get_collection_posts($data['collection_id']))) {
                    $return['empty_collection'] = true;
                } else {
                    $return['empty_collection'] = false;
                }
                $collection_model = new CollectionModel;
                $args = $wpquery_model->orderby_filter($data);
                $params = $wpquery_model->do_filter($args);

                $data['loop'] =  new WP_Query($params);
                $data['collection_data'] = $collection_model->get_collection_data($args['collection_id']);
                $data['listed_by'] = $wpquery_model->get_ordered_name($args['collection_id'], $args['ordenation_id'], $args['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($args['collection_id'], get_current_user_id());
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($args['collection_id'], "socialdb_collection_table_metas", true)));
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                $return['args'] = serialize($args);
//                if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
//                    $return['page'] = iconv('ISO-8859-1', 'UTF-8',  utf8_decode($return['page']));
//                }
                return json_encode($return);
            case "wpquery_page":
                $return = array();
                if(empty($wpquery_model->get_collection_posts($data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                }
                $collection_model = new CollectionModel;
                $args = $wpquery_model->page_filter($data);
                $data['pagid'] = $data['value'];
                $data['col_id'] = $args['collection_id'];
                $paramters = $wpquery_model->do_filter($args);
                $data['loop'] =  new WP_Query($paramters);
                $data['collection_data'] = $collection_model->get_collection_data($args['collection_id']);
                $data["show_string"] = is_root_category($data['col_id']) ? _t('Showing collections:') : _t('Showing Items:');
                $data['listed_by'] = $wpquery_model->get_ordered_name($args['collection_id'], $args['ordenation_id'], $args['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($args['collection_id'], get_current_user_id());
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($args['collection_id'], "socialdb_collection_table_metas", true)));


                if(isset($data['is_trash']) && $data['is_trash'] === true) {
                    $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list_trash.php', $data);
                } else {
                    $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                }

                $return['args'] = serialize($args);
//                if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
//                    $return['page'] = iconv('ISO-8859-1', 'UTF-8',  utf8_decode($return['page']));
//                }
                return json_encode($return);
            case "wpquery_author":
                $return = array();
                if(empty($wpquery_model->get_collection_posts($data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                }
                $collection_model = new CollectionModel;
                $args = $wpquery_model->author_filter($data);
                $data['author'] = $data['value'];
                $paramters = $wpquery_model->do_filter($args);
                $data['loop'] =  new WP_Query($paramters);
                $data['collection_data'] = $collection_model->get_collection_data($args['collection_id']);
                $data['listed_by'] = $wpquery_model->get_ordered_name($args['collection_id'], $args['ordenation_id'], $args['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($args['collection_id'], get_current_user_id());
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($args['collection_id'], "socialdb_collection_table_metas", true)));
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                $return['args'] = serialize($args);
//                if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
//                    $return['page'] = iconv('ISO-8859-1', 'UTF-8',  utf8_decode($return['page']));
//                }
                return json_encode($return);
            case "wpquery_keyword":
                set_time_limit(0);
                $return = array();
                if(empty($wpquery_model->get_collection_posts($data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                }
                $collection_model = new CollectionModel;
                $args = $wpquery_model->keyword_filter($data);
                $paramters = $wpquery_model->do_filter($args);
                $data['loop'] =  new WP_Query($paramters);
                $return['has_post'] = $data['loop']->have_posts();
                $data['collection_data'] = $collection_model->get_collection_data($args['collection_id']);
                $data['listed_by'] = $wpquery_model->get_ordered_name($args['collection_id'], $args['ordenation_id'], $args['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($args['collection_id'], get_current_user_id());
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($args['collection_id'], "socialdb_collection_table_metas", true)));
                $return['page'] =   $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                $return['args'] = serialize($args);
                /* if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
                    $return['page'] = iconv('ISO-8859-1', 'UTF-8',  utf8_decode($return['page']));
                } */
                Log::addLog(['collection_id' => $data['collection_id'], 'event_type' => 'collection_search', 'event' => $data['value'] ]);

                return json_encode($return);
            case "filter":
                $return = array();
                $collection_model = new CollectionModel;
                $args = $wpquery_model->filter($data);
                $paramters = $wpquery_model->do_filter($args);
                $data['loop'] =  new WP_Query($paramters);
                $data['has_post'] = $data['loop']->have_posts();
                $data['collection_data'] = $collection_model->get_collection_data($args['collection_id']);
                $data['listed_by'] = $wpquery_model->get_ordered_name($args['collection_id'], $args['ordenation_id'], $args['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($args['collection_id'], get_current_user_id());
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($args['collection_id'], "socialdb_collection_table_metas", true)));
                $data['pagid'] = $args['pagid'];
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                $return['args'] = serialize($args);
                if(empty($wpquery_model->get_collection_posts($data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                }
//                if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
//                    $return['page'] = iconv('ISO-8859-1', 'UTF-8',  utf8_decode($return['page']));
//                }
                return json_encode($return);
            case "clean":
                $return = array();
                $collection_model = new CollectionModel;
                $args = $wpquery_model->clean($data);
                $paramters = $wpquery_model->do_filter($args);
                $data['loop'] =  new WP_Query($paramters);
                $data['collection_data'] = $collection_model->get_collection_data($args['collection_id']);
                $term = get_term_by('slug', 'socialdb_ordenation_recent', 'socialdb_property_type');
                $data['listed_by'] = $wpquery_model->get_ordered_name($args['collection_id'], $term->term_id, $args['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($args['collection_id'], get_current_user_id());
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($args['collection_id'], "socialdb_collection_table_metas", true)));
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                $return['args'] = serialize($args);
                $return['listed_by_value'] = $term->term_id;
                if(empty($wpquery_model->get_collection_posts($data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                }
//                if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
//                    $return['page'] = iconv('ISO-8859-1', 'UTF-8',  utf8_decode($return['page']));
//                }
                return json_encode($return);
            case "remove":
                $return = array();
                if(empty($wpquery_model->get_collection_posts($data['collection_id']))){
                    $return['empty_collection'] = true;
                }else{
                    $return['empty_collection'] = false;
                }
                $collection_model = new CollectionModel;
                $args = $wpquery_model->remove_filter($data);
                $paramters = $wpquery_model->do_filter($args);
                $data['loop'] =  new WP_Query($paramters);
                $data['collection_data'] = $collection_model->get_collection_data($args['collection_id']);
                $data['listed_by'] = $wpquery_model->get_ordered_name($args['collection_id'], $args['ordenation_id'], $args['order_by']);
                $data['is_moderator'] = CollectionModel::is_moderator($args['collection_id'], get_current_user_id());
                $data["table_meta_array"] = unserialize(base64_decode(get_post_meta($args['collection_id'], "socialdb_collection_table_metas", true)));
                $return['page'] = $this->render(dirname(__FILE__) . '../../../views/object/list.php', $data);
                $return['args'] = serialize($args);
//                 if(mb_detect_encoding($return['page'], 'auto')=='UTF-8'){
//                     $return['page'] = iconv('ISO-8859-1', 'UTF-8',  utf8_decode($return['page']));
//                }
                return json_encode($return);         
         }
        
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

$wpquery_controller = new WPQueryController();
$json = json_decode($wpquery_controller->operation($operation, $data));
if(isset($json->args)){
    $json->is_filter = true;
    $json->url = http_build_query(unserialize($json->args));
}
echo json_encode($json);
