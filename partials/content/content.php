<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="tainacan-article-wrapper">
        <?php if ( '' !== get_the_post_thumbnail() && ! is_single() ) : ?>
            <div class="post-thumbnail">
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail([80,80]); ?>
                </a>
            </div>
        <?php endif; ?>

        <header class="entry-header">
            <?php
            if ( is_single() ) {
                the_title( '<h1 class="entry-title">', '</h1>' );
            } else {
                the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
            }
            ?>
        </header>
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
