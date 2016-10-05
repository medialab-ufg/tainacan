<li class="col-md-6 cards-view-container top-div"
    id="object_<?php echo $curr_id ?>" data-order="<?php echo $countLine; ?>"
    <?php if ($collection_list_mode != "cards"): ?> style="display: none;" <?php endif ?> >

    <input type="hidden" id="add_classification_allowed_<?php echo $curr_id ?>" name="add_classification_allowed" value="<?php echo (string) verify_allowed_action($collection_id, 'socialdb_collection_permission_add_classification', $curr_id); ?>" />
    <!-- TAINACAN: coloca a class row DO ITEM, sao cinco colunas possiveis todas elas podendo ser escondidas pelo o usuario, mas seu tamanho eh fixo col-md-2  -->
    <div class="item-colecao" <?php if (($countLine % 2) == 0) { echo "style='margin-right: 0'"; } ?>>
        <input type="hidden" class="latitude"  value="<?php echo $latitude[0]; ?>" />
        <input type="hidden" class="longitude" value="<?php echo $longitude[0]; ?>" />
        <input type="hidden" class="location" value="<?php echo $location[0]; ?>" />

        <div class="droppableClassifications">

            <div class="row item-info">
            <div class="col-md-4 colFoto">
                <a href="<?php echo get_collection_item_href($collection_id); ?>"
                   onclick="<?php get_item_click_event($collection_id,$curr_id) ?>">
                       <?php echo get_item_thumb_image( $curr_id ); ?>
                </a>
            </div>

                <?php
                // $termos = wp_get_object_terms($curr_id, "socialdb_category_type", ['fields' => 'ids']);
                // var_dump($collection_id);

                $_isso_tem = get_the_terms($collection_id, "socialdb_property_type");

                $plim = wp_get_object_terms($collection_id, 'socialdb_category_type', array('fields' => 'ids'));
                // var_dump($plim);

                $_item_terms_ = get_the_terms($curr_id, "socialdb_category_type");
                // var_dump($_item_terms_);

                $paps = get_term_meta(end($table_meta_ids));
                $_binas = (int) $paps['socialdb_property_term_root'][0];
                // echo " O term root relacionado eh " . $_binas;

                if(is_array($_item_terms_)) {
                    foreach($_item_terms_ as $_iTerm) {
                        // var_dump($_iTerm);
                        if($_binas === $_iTerm->parent) {
                            echo $_iTerm->name . " => " . $_iTerm->term_id . " Meu paps eh " . $_iTerm->parent . " <br />";
                        }

                    }
                }
                // echo "<h3> Check bellow all the table metas that came to us</h3>";
                //var_dump($table_meta_ids);
                // echo "<hr/>";
                // echo "<hr/>";
                ?>

            <div class="col-md-8 flex-box item-meta-box" style="flex-direction:column;">
                <div class="item-meta col-md-12 no-padding">
                    <?php
                    if( isset($table_meta_ids) && $table_meta_ids > 0):
                        foreach ($table_meta_ids as $meta_id) {
                            $__item_meta = get_post_meta($curr_id, "socialdb_property_$meta_id", true);
                            // echo "<h1>" . $__item_meta . "</h1>";
                            ?>
                            <input type="hidden" name="item_table_meta" value="<?php echo $__item_meta; ?>" />
                        <?php }
                    endif;
                    ?>
                    
                    <h4 class="item-display-title">
                        <a href="<?php echo get_collection_item_href($collection_id); ?>"
                           onclick="<?php get_item_click_event($collection_id,$curr_id) ?>">
                            <?php echo wp_trim_words( get_the_title(), 13 ); ?>
                        </a>
                    </h4>
                    <div class="item-description"> <?php echo wp_trim_words(get_the_content(), 16); ?> </div>
                    
                    <div class="row author-created">
                        <div class="col-md-6 author">
                            <div class="item-author"><?php echo "<strong>" . __('Created by: ', 'tainacan') . "</strong>" . wp_trim_words( get_the_author(), 2); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="item-creation"><?php echo $curr_date; ?></div>
                        </div>
                    </div>

                    <?php if (get_option('collection_root_id') != $collection_id): ?>
                    
                        <button id="show_rankings_<?php echo $curr_id ?>" class="cards-ranking"> </button>

                        <div class="editing-item">
                            
                            <!-- TAINACAN: container(AJAX) que mostra o html com os rankings do objeto-->
                            <div id="rankings_<?php echo $curr_id ?>" class="rankings-container"></div>

                            <ul class="item-funcs col-md-5 right">
                                <input type="hidden" class="post_id" name="post_id" value="<?= $curr_id ?>">
                                <li class="item-redesocial">
                                    <a id="modal_network<?php echo $curr_id; ?>" onclick="showModalShareNetwork(<?php echo $curr_id; ?>)">
                                        <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                                    </a>
                                </li>
                                <?php include "edit_btns.php"; ?>
                            </ul>

                        </div> <!--.editing-item -->

                    <?php endif; ?>

                    <!-- TAINACAN: container(AJAX) que mostra o html com as classificacoes do objeto -->
                    <div id="classifications_<?php echo $curr_id ?>" class="class-meta-box"></div>

                </div>

                <div class="show-item-metadata">
                    <!-- CATEGORIES AND TAGS -->
                    <input type="hidden" value="<?php echo $curr_id ?>" class="object_id">
                    <button id="show_classificiations_<?php echo $curr_id ?>" style="width:100%" class="btn btn-default"
                            onclick="show_classifications('<?php echo $curr_id ?>')">
                                <?php _e('Metadata', 'tainacan'); ?>
                    </button>
                </div>
            </div>

        </div>
        </div>
    </div>
</li>