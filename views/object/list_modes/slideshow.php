<div class="col-md-12 no-padding slideshow-view-container top-div" <?php if ($collection_list_mode != "slideshow"): ?> style="display: none" <?php endif ?> >
    <div id="slideshow-viewMode" class="col-md-12 no-padding"></div>
</div>

<div class="modal fade slideShow-modal" tabindex="-1" id="collection-slideShow" role="dialog" aria-labelledby="Slideshow" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" style="color: white">
                    <?php _e('Collection', 'tainacan')?>
                    <span class="sS-collection-name"> </span>
                </h4>
            </div>
            <div class="modal-body" style="border: none">
                <div id="slideshow-viewMode" class="col-md-12 no-padding">
                    <div class="container col-md-11 center">
                        <div class="collection-slides">
                            <?php while ( $loop->have_posts() ) : $loop->the_post(); $countLine++; ?>
                                <div> <?php echo get_item_thumb_image(get_the_ID()); ?> </div>
                            <?php endwhile; ?>
                        </div>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>