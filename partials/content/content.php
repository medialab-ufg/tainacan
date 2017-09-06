<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="tainacan-article-wrapper col-md-12">
        <?php /* if ( '' !== get_the_post_thumbnail() && ! is_single() ) : ?>
            <div class="post-thumbnail">
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail([80,80]); ?>
                </a>
            </div>
        <?php endif; */ ?>

        <header class="entry-header">
            <?php
            if ( is_single() ) {
                the_title( '<h1 class="entry-title">', '</h1>' );
            } else {
                the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
            }
            ?>
        </header>

        <div class="post-resume">
            <?php echo wp_trim_words( get_the_excerpt(), 25, '...'); ?>
        </div>

        <div class="read-more">
            <a href="<?php echo esc_url(get_permalink()); ?>"><?php _t('Read more...', 1); ?></a>
        </div>

    </div>


    <div class="entry-content">
        <?php

        wp_link_pages( array(
            'before'      => '<div class="page-links">' . _t( 'Pages:' ),
            'after'       => '</div>',
            'link_before' => '<span class="page-number">',
            'link_after'  => '</span>',
        ) );
        ?>
    </div>

</article><!-- #post-## -->
