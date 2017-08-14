<?php get_header(); ?>

<div class="col-md-12 tainacan-page-area">
    <?php if ( is_home() && ! is_front_page() ): ?>
        <header class="page-header">
            <h3 class="page-title"><?php single_post_title(); ?></h3>
        </header>
    <?php else: ?>
        <header class="page-header col-md-12">
            <h2 class="page-title"><?php _t( 'Posts', 1); ?></h2>
        </header>
    <?php endif; ?>

    <div id="primary" class="tainacan-content-area">
        <main id="main" class="col-md-8" role="main">
            <?php if (have_posts()):
                while ( have_posts() ) : the_post();
                    get_template_part( 'partials/content/content' );
                endwhile;
                the_posts_pagination( );
            else:
                get_template_part( 'partials/content/content', 'none' );
            endif;
            ?>
        </main>
    </div>

    <div class="col-md-4">
        <?php get_sidebar(); ?>
    </div>
</div><!-- .wrap -->

<?php get_footer(); ?>