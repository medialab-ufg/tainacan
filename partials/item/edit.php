<?php
include_once dirname(__FILE__ ) . '/../../controllers/object/object_controller.php';
$obj = new ObjectController();
$data = ['collection_id' => get_the_ID()];
$op = $obj->operation("edit-item", $data);

get_header();
get_template_part("partials/setup","header");;

echo $op;

get_footer();