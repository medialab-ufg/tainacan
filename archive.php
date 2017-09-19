<?php get_header(); ?>

    <div class="col-md-12 tainacan-page-area">

        <div class="col-md-8 no-padding center">
            <header class="page-header col-md-12 no-padding">
                <h1 class="page-title"> <?php _t( 'News', 1); ?> </h1>
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