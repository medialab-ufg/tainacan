<?php
/**
 * Template Name: Statistics
 */

add_action('tainacan-header-end', function() {
    include('header-statistics.php');
});

$home_url = get_bloginfo('url');
$repository_title = get_bloginfo('name');
$repo_desc = get_bloginfo('description');
if ( current_user_can('manage_options') ):
    get_header(); ?>

    <div id='tainacan-stats' class='row'>
        <center style="margin: 40px 0 40px 0">
            <img src="<?php echo get_template_directory_uri() . '/libraries/images/ajaxLoader.gif' ?>" width="64px" height="64px" />
            <br> <br>
            <?php _t('Loading Statistics ...', 1); ?>
        </center>
    </div>
    <?php
    require_once (dirname(__FILE__) . '/extras/routes/routes.php');
    get_footer();
else:
    $home = home_url("/");
    header("Location: " . $home);
endif;
