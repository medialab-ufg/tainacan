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

            <div class="col-md-8 flex-box item-meta-box" style="flex-direction:column;">
                <div class="item-meta col-md-12 no-padding">
                    <?php
                    if( is_array($table_meta_array) && count($table_meta_array) > 0):
                        foreach ($table_meta_array as $item_meta_info):
                            $fmt = str_replace("\\","", $item_meta_info);
                            if(is_string($fmt)):
                                $_meta_obj = json_decode($fmt);
                                if(is_object($_meta_obj)):
                                    if($_meta_obj->tipo === 'property_data') {
                                        $__item_meta = get_post_meta($curr_id, "socialdb_property_$_meta_obj->id", true);
                                        if( !empty($__item_meta)) {
                                            echo '<input type="hidden" name="item_table_meta" value="'. $__item_meta .'" />';
                                        }
                                    } else if($_meta_obj->tipo === 'property_term') {
                                        $_current_object_terms_ = get_the_terms($curr_id, "socialdb_category_type");
                                        $_father_category_id = (int) get_term_meta($_meta_obj->id)['socialdb_property_term_root'][0];
                                        foreach ($_current_object_terms_ as $curr_term) {
                                            if($curr_term->parent == $_father_category_id) {
                                                echo '<input type="hidden" name="item_table_meta" value="'. $curr_term .'" />';
                                            }
                                        }
                                    }
                                endif; //is_object
                            endif; // is_string
                        endforeach;
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