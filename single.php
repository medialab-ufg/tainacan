<?php get_header(); ?>
    <div class="container-fluid">
        <div class="col-md-12">
            <?php while (have_posts()): the_post(); ?>
                <h1 class="entry-title"><?php the_title(); ?></h1> <hr />

                <div class="single-post-wrapper"> <?php echo the_content(); ?> </div>

                <?php if (comments_open() || get_comments_number()): ?>
                    <div class="comments-wrapper"> <?php comments_template(); ?> </div>
                <?php
                endif;

                edit_post_link();

            endwhile; ?>
        </div>
    </div>

<?php get_footer(); ?>