<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
    </header>

    <?php get_the_post_thumbnail(); ?>

    <div class="entry-content">
        <?php the_content(); ?>
    </div>

</article><!-- #post-## -->