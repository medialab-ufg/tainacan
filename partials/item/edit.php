<?php
include_once dirname(__FILE__ ) . '/../../controllers/object/object_controller.php';
$obj = new ObjectController();

$item = get_post();
$data = ['collection_id' => $item->post_parent,'item_id'=>$item->ID];

$op = $obj->operation("edit-item", $data);

get_header();
get_template_part("partials/setup","header");;

echo $op;

get_footer();