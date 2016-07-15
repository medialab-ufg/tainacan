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
                                                <button id="show_rankings_ss_<?php echo $curr_id ?>" onclick="show_value_ordenation('<?php echo $curr_id ?>', '#rankings_ss_', '#show_rankings_ss_')"
                                                        class="btn btn-default"><?php _e('Show rankings', 'tainacan'); ?></button>

                                                <!-- TAINACAN: container(AJAX) que mostra o html com os rankings do objeto-->
                                                <div id="rankings_ss_<?php echo $curr_id ?>" class="rankings-container"></div>
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

                                        <?php /*  ?>
                                        <div class="col-md-6 pull-right">
                                                <div class="editing-item">

                                                    <div id="popover_content_wrapper<?php echo $curr_id; ?>" class="hide flex-box">
                                                        <a target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo get_the_permalink($collection_id) . '?item=' . get_post($curr_id)->post_name; ?>&amp;text=<?php echo htmlentities(get_the_title()); ?>&amp;via=socialdb"><div data-icon="&#xe005;"></div></a>
                                                        <a onclick="redirect_facebook('<?php echo $curr_id ?>');" href="#"><div data-icon="&#xe021;"></div></a>
                                                        <a target="_blank" href="https://plus.google.com/share?url=<?php echo get_the_permalink($collection_id) . '?item=' . get_post($curr_id)->post_name; ?>"><div data-icon="&#xe01b;"></div></a>
                                                    </div>

                                                    <ul class="item-funcs pull-right">
                                                        <!-- TAINACAN: hidden com id do item -->
                                                        <input type="hidden" class="post_id" name="post_id" value="<?= $curr_id ?>">

                                                        <!-- TAINACAN:  modal para compartilhar o item -->
                                                        <div class="modal fade" id="modal_share_network<?php echo $curr_id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <form name="form_share_item<?php echo $curr_id ?>" id="form_share_item<?php echo $curr_id ?>" method="post">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                            <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-share"></span>&nbsp;<?php _e('Share', 'tainacan'); ?></h4>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <?php echo __('Post it on: ', 'tainacan'); ?><br>
                                                                                    <a target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo get_the_permalink($collection_id) . '?item=' . get_post($curr_id)->post_name; ?>&amp;text=<?php echo htmlentities(get_the_title()); ?>&amp;via=socialdb"><?php echo ViewHelper::render_icon('twitter-square', 'png', 'Twitter'); ?></a>&nbsp;
                                                                                    <a onclick="redirect_facebook('<?php echo $curr_id ?>');" href="#"><?php echo ViewHelper::render_icon('facebook-square', 'png', 'Facebook'); ?></a>&nbsp;
                                                                                    <a target="_blank" href="https://plus.google.com/share?url=<?php echo get_the_permalink($collection_id) . '?item=' . get_post($curr_id)->post_name; ?>"><?php echo ViewHelper::render_icon('googleplus-square', 'png', 'Google Plus'); ?></a>
                                                                                    <br><br>
                                                                                    <?php echo __('Link: ', 'tainacan'); ?>
                                                                                    <input type="text" id="link_object_share<?php echo $curr_id ?>" class="form-control" value="<?php echo get_the_permalink($collection_id) . '?item=' . get_post($curr_id)->post_name; ?>" />
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <?php echo __('Embed it: ', 'tainacan'); ?>
                                                                                    <textarea id="embed_object<?php echo $curr_id ?>" class="form-control" rows="5"><?php echo '<iframe width="1024" height="768" src="' . get_the_permalink($collection_id) . '?item=' . get_post($curr_id)->post_name . '" frameborder="0"></iframe>'; ?></textarea>
                                                                                </div>
                                                                            </div>
                                                                            <hr>
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <?php echo __('Email: ', 'tainacan'); ?><br>
                                                                                    <input type="text" id="email_object_share<?php echo $curr_id ?>" class="form-control" /><br>
                                                                                    <?php echo __('Share in other collection: ', 'tainacan'); ?><br>
                                                                                    <input type="text" id="collections_object_share<?php echo $curr_id ?>" class="form-control autocomplete_share_item" >
                                                                                    <input type="hidden" name="collection_id" id="collections_object_share<?php echo $curr_id ?>_id"  >
                                                                                    <input type="hidden" name="collection_id" id="collections_object_share<?php echo $curr_id ?>_url"  >
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan'); ?></button>
                                                                            <button onclick="send_share_item(<?php echo $curr_id ?>);" type="button" class="btn btn-primary"><?php echo __('Send', 'tainacan'); ?></button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <?php
                                                        <li>
                                                            <div class="item-redesocial">
                                                                <a id="modal_network<?php echo $curr_id; ?>" onclick="showModalShareNetwork(<?php echo $curr_id; ?>)">
                                                                    <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                                                                </a>
                                                            </div>
                                                        </li>
                                                         ?>

                                                        <?php if (get_option('collection_root_id') != $collection_id): ?>
                                                            <!--------------------------- DELETE AND EDIT OBJECT------------------------------------------------>
                                                            <?php if ($is_moderator || get_post($curr_id)->post_author == get_current_user_id()): ?>
                                                                <li>
                                                                    <a onclick="delete_object('<?= __('Delete Object', 'tainacan') ?>', '<?= __('Are you sure to remove the object: ', 'tainacan') . get_the_title() ?>', '<?php echo $curr_id ?>', '<?= mktime() ?>')" style="cursor: pointer;" class="remove">
                                                                        <span class="glyphicon glyphicon-trash"></span>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <!-- onclick="edit_object">
                                                                    </a-->
                                                                    <a style="cursor: pointer;" onclick="edit_object_item('<?php echo $curr_id ?>')">
                                                                        <span class="glyphicon glyphicon-edit"></span>
                                                                    </a>
                                                                </li>
                                                            <?php else: ?>
                                                                <?php
                                                                // verifico se eh oferecido a possibilidade de remocao do objeto vindulado
                                                                if (verify_allowed_action($collection_id, 'socialdb_collection_permission_delete_object')): ?>
                                                                    <li>
                                                                        <a onclick="show_report_abuse('<?php echo $curr_id ?>')" href="#" class="report_abuse">
                                                                            <span class="glyphicon glyphicon-warning-sign"></span>
                                                                        </a>
                                                                    </li>
                                                                <?php endif; ?>
                                                                <!-- TAINACAN:  modal padrao bootstrap para reportar abuso -->
                                                                <div class="modal fade" id="modal_delete_object<?php echo $curr_id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                                <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Report Abuse', 'tainacan'); ?></h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <?php echo __('Describe why the object: ', 'tainacan') . get_the_title() . __(' is abusive: ', 'tainacan'); ?>
                                                                                <textarea id="observation_delete_object<?php echo $curr_id ?>" class="form-control"></textarea>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan'); ?></button>
                                                                                <button onclick="report_abuse_object('<?= __('Delete Object') ?>', '<?= __('Are you sure to remove the object: ', 'tainacan') . get_the_title() ?>', '<?php echo $curr_id ?>', '<?= mktime() ?>')" type="button" class="btn btn-primary"><?php echo __('Delete', 'tainacan'); ?></button>
                                                                            </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                            <!--li><a href=""><span class="glyphicon glyphicon-comment"></span></a></li-->
                                                        <?php else: ?>
                                                            <!-- TAINACAN: mostra o modal da biblioteca sweet alert para exclusao de uma colecao -->
                                                            <?php if ($is_moderator || get_post($curr_id)->post_author == get_current_user_id()): ?>
                                                                <li>
                                                                    <a onclick="delete_collection('<?= __('Delete Object', 'tainacan') ?>', '<?= __('Are you sure to remove the collection: ', 'tainacan') . get_the_title() ?>', '<?php echo $curr_id ?>', '<?= mktime() ?>', '<?php echo get_option('collection_root_id') ?>')" href="#" class="remove">
                                                                        <span class="glyphicon glyphicon-trash"></span>
                                                                    </a>
                                                                </li>
                                                            <?php else: ?>
                                                                <!-- TAINACAN: mostra o modal para reportar abusao em um item, gerando assim um evento -->
                                                                <li>
                                                                    <a onclick="show_report_abuse('<?php echo $curr_id ?>')" href="#" class="report_abuse">
                                                                        <span class="glyphicon glyphicon-warning-sign"></span>
                                                                    </a>
                                                                </li>
                                                                <!-- TAINACAN:  modal padrao bootstrap para reportar abuso -->
                                                                <div class="modal fade" id="modal_delete_object<?php echo $curr_id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                                <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Report Abuse', 'tainacan'); ?></h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <?php echo __('Describe why the collection: ', 'tainacan') . get_the_title() . __(' is abusive: ', 'tainacan'); ?>
                                                                                <textarea id="observation_delete_collection<?php echo $curr_id ?>" class="form-control"></textarea>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                                                                                <button onclick="report_abuse_collection('<?php _e('Delete Collection', 'tainacan') ?>', '<?php _e('Are you sure to remove the collection: ', 'tainacan') . get_the_title() ?>', '<?php echo $curr_id ?>', '<?= mktime() ?>', '<?php echo get_option('collection_root_id') ?>')" type="button" class="btn btn-primary"><?php echo __('Delete', 'tainacan'); ?></button>
                                                                            </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php endif; ?>

                                                        <li style="display: inline-block">
                                                            <div class="item-redesocial">
                                                                <a id="popover_network<?php echo $curr_id; ?>" rel="popover" data-placement="left"
                                                                   onclick="showPopover(<?php echo $curr_id; ?>)">
                                                                    <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                                                                </a>
                                                            </div>
                                                        </li>
                                                    </ul>

                                                </div> <!-- .editing-item -->
                                            </div>
                                             */ ?>

                                        <!-- TAINACAN: script para disparar o evento que mostra os rankings -->
                                        <script>
                                            $('#show_rankings_ss_<?php echo $curr_id ?>').hide().trigger('click');
                                        </script>
                                    <?php endif; ?>
                                    </div>

                                    <!-- TAINACAN: container(AJAX) que mostra o html com as classificacoes do objeto -->
                                    <div id="classifications_<?php echo $curr_id ?>" class="class-meta-box"></div>

                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>