<?php
$latitude = get_post_meta($curr_id, "socialdb_property_" . $geo_coordinates["lat"]);
$longitude = get_post_meta($curr_id, "socialdb_property_" . $geo_coordinates["long"]);
$location = get_post_meta($curr_id, "socialdb_property_" . $geo_loc);
$_object_description = get_the_content();
$item_title = wp_trim_words(get_the_title(), 13);
$_trim_desc = $_object_description;
?>

<div class="col-md-6 cards-view-container top-div no-padding" id="object_<?php echo $curr_id ?>" data-order="<?php echo $countLine; ?>"
    <?php if ($collection_list_mode != "cards"): ?> style="display: none;" <?php endif ?> >

    <input type="hidden" id="add_classification_allowed_<?php echo $curr_id ?>" name="add_classification_allowed" value="<?php echo (string) verify_allowed_action($collection_id, 'socialdb_collection_permission_add_classification', $curr_id); ?>" />
    <input type="hidden" value="<?php echo $curr_id ?>" class="object_id">

    <?php ObjectHelper::getTableViewData($table_meta_array, $loop->current_post, $curr_id, $viewHelper::$fixed_slugs); ?>

    <div class="item-colecao toggleSelect" <?php if (($countLine % 2) == 0) { echo "style='margin-right: 0'"; } ?>>

        <input type="hidden" class="latitude"  value="<?php echo $latitude[0]; ?>" />
        <input type="hidden" class="longitude" value="<?php echo $longitude[0]; ?>" />
        <input type="hidden" class="location" value="<?php echo $location[0]; ?>" />

        <div class="droppableClassifications">

            <div class="item-info">

                <div class="colFoto no-padding img-thumbnail">
                    <?php if(empty($trash_list)): ?>
                        <a href="<?php echo $itemURL; ?>">
                            <?php echo get_item_thumb_image($curr_id); ?>
                        </a>
                    <?php elseif ($trash_list): echo get_item_thumb_image($curr_id); endif; ?>
                </div>

                <div class="col-md-9 flex-box item-meta-box no-padding">
                    <div class="item-meta col-md-12 no-padding">

                        <h4 class="item-display-title">
                            <?php if(empty($trash_list)): ?>
                                <?php /* <a href="<?php echo get_collection_item_href($collection_id, $curr_id, $viewHelper); ?>" onclick="<?php get_item_click_event($collection_id, $curr_id) ?>">  */ ?>
                                <a href="<?php echo $itemURL; ?>">
                                    <?php echo $item_title; ?>
                                </a>
                            <?php elseif ($trash_list): echo $item_title; endif; ?>
                        </h4>

                        <div class="item-description"> <?php echo $_trim_desc; ?> </div>
                        <div class='item-desc-hidden-full' style="display: none"><?php echo $_object_description; ?></div>

                        <div class="row author-created">
                            <div class="col-md-6 author">
                                <div class="item-author"><?php echo "<strong>" . __('Created by: ', 'tainacan') . "</strong>" . wp_trim_words(get_the_author(), 2); ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="item-creation">
                                    <strong> <?php _t('Created at: ',1) ?> </strong>
                                    <span> <?php echo get_the_date('d/m/Y'); ?> </span>
                                </div>
                            </div>
                        </div>
                        <?php if (get_option('collection_root_id') != $collection_id): ?>
                            <button id="show_rankings_<?php echo $curr_id ?>" class="cards-ranking"> </button>

                            <div class="editing-item">
                                <!-- TAINACAN: container(AJAX) que mostra o html co m os rankings do objeto-->
                                <div id="rankings_<?php echo $curr_id ?>" class="rankings-container"></div>

                                <ul class="item-funcs col-md-5 right">
                                    <input type="hidden" class="post_id" name="post_id" value="<?= $curr_id ?>">
                                    <?php if (empty($trash_list)) { ?>
                                        <li class="item-redesocial tainacan-museum-clear">
                                            <a id="modal_network<?php echo $curr_id; ?>" onclick="showModalShareNetwork(<?php echo $curr_id; ?>)">
                                                <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                                            </a>
                                        </li>
                                    <?php } elseif($trash_list) { include "edit_btns_trash.php"; } ?>
                                </ul>

                                <?php if(empty($trash_list)): ?>

                                    <div class="new-item-actions">
                                        <?php include "actions/item_actions.php"; ?>
                                    </div>

                                    <ul class="item-funcs-table col-md-5 right" style="display:none;">
                                        <input type="hidden" class="post_id" name="post_id" value="<?= $curr_id ?>">
                                        <li class="item-redesocial tainacan-museum-clear" style="float: right; margin-left: 10px">
                                            <a id="modal_network<?php echo $curr_id; ?>" onclick="showModalShareNetwork(<?php echo $curr_id; ?>)">
                                                <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                                            </a>
                                        </li>
                                    </ul>

                                <?php endif; ?>

                            </div> <!--.editing-item -->

                        <?php endif; ?>

                        <!-- TAINACAN: container(AJAX) que mostra o html com as classificacoes do objeto -->
                        <div id="classifications_<?php echo $curr_id ?>" class="class-meta-box"></div>

                    </div>

                    <?php if(empty($trash_list)): ?>
                        <div class="show-item-metadata">
                            <!-- CATEGORIES AND TAGS -->
                            <button id="show_classificiations_<?php echo $curr_id ?>" style="width:100%" class="btn btn-default cards-voting"
                                    onclick="show_classifications('<?php echo $curr_id ?>')">
                                <?php // _e('Metadata', 'tainacan'); ?>
                                <?php _e('Categories', 'tainacan'); ?>
                            </button>
                        </div>
                    <?php endif; ?>

                </div> <!-- .item-meta-box -->

            </div>
        </div>
    </div>
</div>