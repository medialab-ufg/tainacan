<?php get_header();
$obj = get_queried_object();

$extra_title = "";
if( is_object($obj) ) {
    if( isset($obj->term_id) && isset($obj->name) ) {
        if ($obj->term_id > 1) {
           $extra_title = " <small> <i> / $obj->name </i> </small>";
        }
    }
}
?>
    <div class="col-md-12 tainacan-page-area">
        <div class="col-md-8 no-padding center">
            <header class="page-header col-md-12 no-padding">
                <h1 class="page-title"> <?php echo _t( 'News') . $extra_title; ?> </h1>
            </header>

            <div id="primary" class="tainacan-content-area">
                <main id="main" class="col-md-12" role="main">
                    <?php if (have_posts()):
                        while ( have_posts() ) : the_post(); ?>
                            <div class="col-md-6 tainacan-new-wrapper">
                                <?php get_template_part( 'partials/content/content' ); ?>
                            </div>
                            <?php
                        endwhile;
                        echo "<div class='col-md-12 text-center'>" . get_the_posts_pagination(['screen_reader_text' => _t('More news')]) . "</div>";
                    else:
                        get_template_part( 'partials/content/content', 'none' );
                    endif;
                    ?>
                </main>
            </div>
        </div>

    </div><!-- .wrap -->

<?php get_footer(); ?>