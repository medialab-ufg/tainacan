<?php
$rq = explode("/", $_SERVER['REQUEST_URI']);

get_header();

$dynamic_admin_file = "admin-" . end($rq) . ".php";
$has_template = locate_template( "partials/admin/" . $dynamic_admin_file );

if(!empty($has_template) && $has_template != "")
    get_template_part( "partials/admin/admin", end($rq) );

 get_footer(); ?>