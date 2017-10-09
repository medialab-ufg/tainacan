<!DOCTYPE html>
<!--[if IEMobile 7 ]> <html <?php language_attributes(); ?>class="no-js iem7"> <![endif]-->
<!--[if lt IE 7 ]> <html <?php language_attributes(); ?> class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html <?php language_attributes(); ?> class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html <?php language_attributes(); ?> class="no-js ie8"> <![endif]-->
<!--[if (gte IE 9)|(gt IEMobile 7)|!(IEMobile)|!(IE)]><!-->
<?php
include_once('helpers/view_helper.php');
$socialdb_logo = get_option('socialdb_logo');
$socialdb_title = get_option('blogname');
$col_root_id = get_option('collection_root_id');
$stat_page = get_page_by_title(__('Statistics', 'tainacan'))->ID;
$viewHelper = new ViewHelper();
$_src_ = get_template_directory_uri();
global $wp_query;

if (is_object($wp_query->post)) {
    $collection_id = $wp_query->post->ID;
    $collection_owner = $wp_query->post->post_author;
} else {
    $collection_id = 0;
    $collection_owner = "";
}
$_header_enabled = get_post_meta($collection_id, 'socialdb_collection_show_header', true);
?>
<html <?php language_attributes(); ?> xmlns:fb="http://www.facebook.com/2008/fbml" class="no-js"><!--<![endif]-->
<head>
    <meta charset="<?php bloginfo('charset'); ?>"> <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="google-site-verification" content="29Uww0bx9McdeJom1CDiXyGUZwK5mtoSuF5tA_i59F4" />
    <link rel="icon" type="image/png" href="<?php echo $_src_ . '/libraries/images/icone.png' ?>">

    <title> <?php echo repository_page_title() ?> </title>
    <?php if (is_front_page()) { ?>
        <link rel="alternate" type="application/rdf+xml" href="<?php echo site_url(); ?>/?.rdf">
        <?php if (is_restful_active()) { ?>
            <link rel="alternate" type="application/json" href="<?php echo site_url(); ?>/wp-json/">
        <?php
        }
    } elseif (is_page_tainacan()) { ?>
        <link rel="alternate" type="application/rdf+xml" href="<?php echo get_the_permalink(); ?>?<?php echo get_page_tainacan() ?>=<?php echo trim($_GET[get_page_tainacan()]) ?>.rdf">
        <?php if (is_restful_active()) { ?>
          <link rel="alternate" type="application/json" href="<?php echo site_url() . '/wp-json/posts/' . get_post_by_name($_GET[get_page_tainacan()], OBJECT, 'socialdb_object')->ID . '/?type=socialdb_object' ?>">
            <?php
        }
    } elseif (is_single()) { ?>
            <meta name="thumbnail_url" content="<?php echo get_the_post_thumbnail_url(get_the_ID()) ?>" />
            <meta name="description" content="<?php echo get_the_content() ?>" />
            <link rel="alternate" type="application/rdf+xml" href="<?php echo get_the_permalink(); ?>?.rdf">
            <?php $_GOOGLE_API_KEY = "AIzaSyBZXPZcDMGeT-CDugrsYWn6D0PQSnq_odg"; ?>
            <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $_GOOGLE_API_KEY; ?>" async></script>
            
            <?php if (is_restful_active()) { ?>
                <link rel="alternate" type="application/json" href="<?php echo site_url() . '/wp-json/posts/' . get_the_ID() . '/?type=socialdb_collection' ?>">
            <?php }
    } ?>

    <?php echo set_config_return_button(is_front_page()); ?>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<!-- TAINACAN: tag body adaptado para o gplus -->
<body <?php body_class(); ?> itemscope>

    <!-- TAINACAN: tag nav, utilizando classes do bootstrap nao modificadas, onde estao localizados os links que chamam paginas da administracao do repositorio -->
    <nav <?php echo set_navbar_bg_color('black', $stat_page); ?> class="navbar navbar-default header-navbar">
        <div class="container-fluid">

            <div class="navbar-header logo-container">
                <button type="button" class="navbar-toggle collapsed" id="btn-toggle" data-toggle="collapse" data-target="#to-collapse">
                    <span class="sr-only"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
                </button>

                <?php
                echo $viewHelper->renderRepositoryLogo($socialdb_logo, $socialdb_title); // Render Logo

                if ("disabled" == $_header_enabled) { ?>
                    <!-- Render site name -->
                    <div class="col-md-8 left repository no-padding">
                      <h5> <?php echo bloginfo('name'); ?> </h5>
                    </div>

                <?php } ?>

            </div> <!-- /.navbar header -->
            
            <?php get_template_part("partials/actions", "header"); ?>

        </div> <!-- /.container-fluid -->
    </nav>

    <?php

    get_template_part("partials/modals","header");

    // Renders custom header only for new template pages
    if ( is_archive() || is_page_template() || is_page() || is_singular('post') || is_home() ) {
        if (!is_page($stat_page)) {
            $_menu_ = ['container_class' => 'container', 'container' => false, 'walker' => new wp_bootstrap_navwalker(), 'menu_class' => 'navbar navbar-inverse menu-ibram'];
            if (!is_front_page()) {
                echo "<header class='custom-header' style='" . home_header_bg($socialdb_logo) . "'>";
                echo "<div class='menu-transp-cover'></div>" . get_template_part("partials/header/main");
                echo "</header>";
                wp_nav_menu($_menu_);
            }
        }
    }