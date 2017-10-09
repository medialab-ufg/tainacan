<?php get_header(); ?>

    <div class="col-md-12 tainacan-page-area">

        <div class="col-md-8 no-padding center">
            <header class="page-header col-md-12 no-padding">
                <h1 class="page-title"> <?php the_title();  ?> </h1>
            </header>

            <div id="primary" class="tainacan-content-area">
                <main id="main" class="col-md-12" role="main">
                    <?php if (have_posts()):
                        while ( have_posts() ) : the_post(); ?>

                            <div class="single-post-wrapper"> <?php echo the_content(); ?> </div>

                            <?php /* if (comments_open() || get_comments_number()): ?>
                                <div class="comments-wrapper"> <?php comments_template(); ?> </div>
                                <?php
                            endif; */

                            edit_post_link();
                        endwhile;
                        the_posts_pagination( );
                    else:
                        get_template_part( 'partials/content/content', 'none' );
                    endif;
                    ?>
                </main>
            </div>
        </div>
    </div>

<?php get_footer(); ?>