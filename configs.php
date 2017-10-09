<?php
$rq = explode("/", $_SERVER['REQUEST_URI']);
$config_request = end($rq);
$dynamic_admin_file = "admin-" . $config_request . ".php";
$has_template = locate_template( "partials/admin/" . $dynamic_admin_file);
$can_manage_categories = (is_user_logged_in() && ($config_request === "categories"));
global $wp_query;

if(!empty($has_template) && $has_template != "" && (current_user_can('manage_options') || $can_manage_categories)) {
    status_header(200);
    $wp_query->is_404 = false;

    get_header();
    get_template_part("partials/setup","header");
    get_template_part("partials/header/cover");
    echo "<section class='admin-configs'>";
        get_template_part( "partials/admin/admin", $config_request );
    echo "</section>";
    get_footer();
    exit();
} else {
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 );
    exit();
}