<div id="slideshow-viewMode" class="col-md-12 no-padding">
    <div class="container col-md-11 center">
        <div class="main-slide">
            <?php while ( $loop->have_posts() ) : $loop->the_post(); $countLine++; ?>
                <div style="text-align: center">
                    <a href="<?php echo get_collection_item_href($collection_id); ?>"
                       onclick="<?php get_item_click_event($collection_id, get_the_ID() )?>">
                        <?php echo get_item_thumb_image(get_the_ID(), "large"); ?>
                    </a>
                    <h4 style="color: black; font-weight: bolder"> <?php the_title(); ?> </h4>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="collection-slides">
            <?php while ( $loop->have_posts() ) : $loop->the_post(); $countLine++; ?>
                <div> <?php echo get_item_thumb_image(get_the_ID()); ?> </div>
            <?php endwhile; ?>
        </div>

    </div>

</div>