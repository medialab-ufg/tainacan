<div class="col-md-12 no-padding slideshow-view-container top-div" <?php if ($collection_list_mode != "slideshow"): ?> style="display: none" <?php endif ?> >
    <div id="slideshow-viewMode" class="col-md-12 no-padding"></div>
</div>

<div class="modal fade slideShow-modal" tabindex="-1" id="collection-slideShow" role="dialog" aria-labelledby="Slideshow" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php echo $viewHelper->render_modal_header('remove-sign', __("Collection", "tainacan"), "<span class='sS-collection-name'> </span>"); ?>

            <div class="modal-body" style="border: none">
                <div id="slideshow-viewMode" class="col-md-12 no-padding">
                    <div class="container col-md-12 center">
                        <div class="collection-slides">
                            <?php $cntr = 1; while ( $loop->have_posts() ): $loop->the_post(); ?>
                                <div class="sSitem-<?= $cntr; ?>"> <?php echo get_item_thumb_image(get_the_ID()); ?> </div>
                            <?php $cntr++; endwhile; ?>
                        </div>
                        <div class="main-slide col-md-12 center">
                            <?php while ( $loop->have_posts() ) : $loop->the_post(); /* $countLine++; */ $curr_id = get_the_ID(); ?>

                                <div class="wrapper">
                                    <h3>
                                        <?php if(empty($trash_list)): ?>
                                            <a href="<?php echo $itemURL; ?>">
                                                <?php the_title(); ?>
                                            </a>
                                        <?php elseif ($trash_list): the_title(); endif; ?>
                                    </h3>

                                    <div class="col-md-11 center center-block main-current-container" style="float: none; margin: 10px auto !important;">
                                        <?php $viewHelper->videoSlideItemHtml($curr_id); ?>
                                    </div>

                                    <div class="col-md-12 meta-configs" style="padding-top: 20px; min-height: 120px;">
                                        <?php if (get_option('collection_root_id') != $collection_id): ?>
                                            <div class="col-md-6 pull-left">
                                                <!-- TAINACAN: container(AJAX) que mostra o html com os rankings do objeto-->
                                                <div id="r_slideshow_<?php echo $curr_id ?>" class="rankings-container" style="text-align: right; margin-right: 0"></div>
                                            </div>

                                            <div class="col-md-6 left pull-right">

                                                <?php if(empty($trash_list)): include "actions/item_actions.php"; ?>
                                                    <ul class="item-funcs" style="text-align: left">
                                                        <input type="hidden" class="post_id" name="post_id" value="<?= $curr_id ?>">
                                                        <li class="tainacan-museum-clear">
                                                            <a id="modal_network<?php echo $curr_id; ?>" onclick="showModalShareNetwork(<?php echo $curr_id; ?>)">
                                                                <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                <?php elseif ($trash_list): ?>
                                                    <ul class="item-funcs col-md-6 right">
                                                        <input type="hidden" class="post_id" name="post_id" value="<?= $curr_id ?>">
                                                        <?php include "edit_btns_trash.php"; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </div>
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