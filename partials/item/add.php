<?php
$is_moderator = CollectionModel::is_moderator($collection_id, get_current_user_id());
if( is_user_logged_in() && (current_user_can("manage_option") || $is_moderator) ) {
    include_once dirname(__FILE__ ) . '/../../controllers/object/object_controller.php';
    $obj = new ObjectController();

    $op = "";
    if(get_query_var("add") === 'true') {
        $set_op = "create-item";
        $data = ['collection_id' => get_the_ID()];

    } else if(get_query_var("edit") === 'true') {
        $item = get_post();
        $col_id = $item->post_parent;
        if(0 === $col_id || is_null($col_id)) {
            $col_id = get_post_meta($item->ID, "socialdb_object_collection_init", true);
            if(is_null($col_id) || empty($col_id)) {
                $col_id = $obj->get_collection_by_item($item->ID)[0]->ID;
            }
        }
        $set_op = "edit-item";
        $data = ['collection_id' => $col_id, 'item_id' => $item->ID];
    }

    $op = $obj->operation($set_op, $data);
    if(isset($set_op) && !empty($set_op)) {
        get_header();
        get_template_part("partials/setup","header");;
            echo $op;
        get_footer();
    } else {
        wp_redirect(site_url());
    }
} else {
    wp_redirect(site_url());
}