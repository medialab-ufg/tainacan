<?php if (! is_user_logged_in()) { ?>
    <style type="text/css"> #collection-slideShow .modal-dialog { margin-top: 0 !important; } </style>
<?php }
$title_prefix = __("Collection", "tainacan");
?>
<div class="col-md-12 no-padding slideshow-view-container top-div" <?php if ($collection_list_mode != "slideshow"): ?> style="display: none" <?php endif ?> >
    <div id="slideshow-viewMode" class="col-md-12 no-padding"></div>
</div>

<div class="modal fade slideShow-modal" tabindex="-1" id="collection-slideShow" role="dialog" aria-labelledby="Slideshow" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <?php echo $viewHelper->render_modal_header('remove-sign', $title_prefix, "<span class='sS-collection-name'> </span>"); ?>

            <div class="modal-body" style="border: none">
                <div id="slideshow-viewMode" class="col-md-12 no-padding">
                    <div class="container col-md-12 center">
                        <div class="collection-slides">
                            <?php while ( $loop->have_posts() ) : $loop->the_post(); $countLine++; ?>
                                <div> <?php echo get_item_thumb_image(get_the_ID()); ?> </div>
                            <?php endwhile; ?>
                        </div>
                        <div class="main-slide col-md-12 center">
                            <?php while ( $loop->have_posts() ) : $loop->the_post(); $countLine++; $curr_id = get_the_ID(); ?>
                                <div class="" style="text-align: center">
                                    <a href="<?php echo get_collection_item_href($collection_id); ?>"
                                       onclick="<?php get_item_click_event($collection_id, $curr_id )?>">
                                        <?php echo get_item_thumb_image($curr_id, "large"); ?>
                                    </a>

                                    <div class="col-md-12 meta-configs">
                                        <h5 style="color: white; font-weight: bolder"> <?php the_title(); ?> </h5>

                                    <?php if (get_option('collection_root_id') != $collection_id): ?>
                                            
                                        <div class="col-md-6 pull-left">
                                            <!-- TAINACAN: container(AJAX) que mostra o html com os rankings do objeto-->
                                            <div id="r_slideshow_<?php echo $curr_id ?>" class="rankings-container"></div>
                                        </div>


                                        <ul class="item-funcs col-md-6 right">
                                            <input type="hidden" class="post_id" name="post_id" value="<?= $curr_id ?>">

                                            <li>
                                                <a id="modal_network<?php echo $curr_id; ?>" onclick="showModalShareNetwork(<?php echo $curr_id; ?>)">
                                                    <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                                                </a>
                                            </li>

                                            <?php include "edit_btns.php"; ?>
                                        </ul>
                                     
                                    <?php endif; ?>
                                    </div>

                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>