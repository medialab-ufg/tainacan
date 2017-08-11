<?php get_header(); ?>

    <div id="primary" class="tainacan-page-area col-md-12">
        <main id="main" class="site-main" role="main">

            <?php
            while( have_posts() ) : the_post();
                get_template_part( 'partials/content/content', 'page' );
            endwhile;
            ?>

        </main>
    </div>

<?php get_footer(); ?>