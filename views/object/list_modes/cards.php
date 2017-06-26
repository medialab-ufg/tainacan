<?php
$_current_collection = $_SERVER['HTTP_REFERER'];
$_last_char = $_current_collection[strlen($_current_collection) - 1];

if ($_last_char != "/") {
    $_current_collection .= '/';
}

$itemURL = $_current_collection . basename(get_permalink());
?>
<li class="col-md-6 cards-view-container top-div"
    id="object_<?php echo $curr_id ?>" data-order="<?php echo $countLine; ?>"
    <?php if ($collection_list_mode != "cards"): ?> style="display: none;" <?php endif ?> >

    <input type="hidden" id="add_classification_allowed_<?php echo $curr_id ?>" name="add_classification_allowed" value="<?php echo (string) verify_allowed_action($collection_id, 'socialdb_collection_permission_add_classification', $curr_id); ?>" />
    <!-- TAINACAN: coloca a class row DO ITEM, sao cinco colunas possiveis todas elas podendo ser escondidas pelo o usuario, mas seu tamanho eh fixo col-md-2  -->
    <div class="item-colecao toggleSelect" <?php
    if (($countLine % 2) == 0) {
        echo "style='margin-right: 0'";
    }
    ?>>
        <input type="hidden" class="latitude"  value="<?php echo $latitude[0]; ?>" />
        <input type="hidden" class="longitude" value="<?php echo $longitude[0]; ?>" />
        <input type="hidden" class="location" value="<?php echo $location[0]; ?>" />

        <div class="droppableClassifications">

            <div class="item-info">
                <div class="col-md-4 colFoto no-padding">
                    <a href="<?php echo get_collection_item_href($collection_id, $curr_id, $viewHelper); ?>"
                       onclick="<?php get_item_click_event($collection_id, $curr_id) ?>">
                        <?php echo get_item_thumb_image($curr_id); ?>
                    </a>
                </div>

                <div class="col-md-8 flex-box item-meta-box" style="flex-direction:column;">
                    <div class="item-meta col-md-12 no-padding">
                        <?php
                        $_item_title_ = get_the_title();
                        $_trim_desc = $_object_description;

                        if (is_array($table_meta_array) && count($table_meta_array) > 0):
                            $_DEFAULT_EMPTY_VALUE = "--";
                            //$_trim_desc = wp_trim_words($_object_description, 16);
                            foreach ($table_meta_array as $item_meta_info):
                                $fmt = str_replace("\\", "", $item_meta_info);
                                if (is_string($fmt)):
                                    $_meta_obj = json_decode($fmt);
                                    if (is_object($_meta_obj)):

                                        $_META = ['id' => $_meta_obj->id, 'tipo' => $_meta_obj->tipo];
                                        if ($loop->current_post === 0)
                                            echo '<input type="hidden" name="meta_id_table" value="' . $_META['id'] . '" data-mtype="' . $_META['tipo'] . '">';

                                        if ($_META['tipo'] === 'property_data') {
                                            $meta_type = get_term_meta($_META['id'], 'socialdb_property_data_widget', true);
                                            $check_fixed = get_term($_meta_obj->id);
                                            $_out_ = $_DEFAULT_EMPTY_VALUE;

                                            if (in_array($check_fixed->slug, $viewHelper::$fixed_slugs)) {
                                                $_slug = $check_fixed->slug;
                                                $base = str_replace("socialdb_property_fixed_", "", $_slug);
                                                switch ($base) {
                                                    case "title":
                                                        $_out_ = $_item_title_;
                                                        break;
                                                    case "description":
                                                        $_out_ = $_trim_desc;
                                                        break;
                                                    case "source":
                                                        $_out_ = get_post_meta($curr_id, "socialdb_object_dc_source")[0];
                                                        break;
                                                    case "type":
                                                        $_out_ = get_post_meta($curr_id, "socialdb_object_dc_type")[0];
                                                        break;
                                                    case "license":
                                                        $_out_ = get_post_meta($curr_id, "socialdb_license_id")[0];
                                                        break;
                                                }
                                            } else {
                                                $__item_meta = get_post_meta($curr_id, "socialdb_property_$_meta_obj->id", true) ?: $_DEFAULT_EMPTY_VALUE;
                                                if (!empty($__item_meta)) {
                                                    $_out_ = $__item_meta;
                                                }
                                            }

                                            if ($meta_type == 'date') {
                                                $date_temp = explode('-', $_out_);
                                                if (count($date_temp) > 1):
                                                    $_out_ = $date_temp[2] . '/' . $date_temp[1] . '/' . $date_temp[0];
                                                endif;
                                            }

                                            if($meta_type == 'user')
                                            {
                                                $user = get_user_by("id", $_out_);
                                                $_out_ = $user->data->display_name;
                                            }
                                            
                                            echo '<input type="hidden" name="item_table_meta" value="' . $_out_ . '" />';
                                        } else if ($_META['tipo'] === 'property_term') {
                                            $_current_object_terms_ = get_the_terms($curr_id, "socialdb_category_type");
                                            $_father_name = get_term($_META['id'])->name;
                                            $_father_category_id = (int) get_term_meta($_META['id'])['socialdb_property_term_root'][0];
                                            $_item_meta_val = $_DEFAULT_EMPTY_VALUE;

                                            foreach ($_current_object_terms_ as $curr_term) {
                                                if ($curr_term->parent == $_father_category_id) {
                                                    $_item_meta_val = $curr_term->name;
                                                }
                                            }
                                            ?>
                                            <input id="tableV-meta-<?= $_META['id']; ?>" type="hidden" name="item_table_meta"
                                                   data-parent="<?= $_father_name ?>" value="<?= $_item_meta_val; ?>" />
                                                   <?php
                                               } else if ($_META['tipo'] == 'property_object') {
                                                   $_prop_key = "socialdb_property_" . (string) $_META['id'];
                                                   $_related_obj_id =  get_post_meta($curr_id, $_prop_key);
                                                   $_father_name = get_term($_META['id'])->name;
                                                   $_item_meta_val = $_DEFAULT_EMPTY_VALUE;
                                                   $values = [];
                                                   if (count($_related_obj_id) > 0) {
                                                       foreach ($_related_obj_id as $value) {
                                                            $_obj_id = get_post($value)->ID;
                                                            if ($_obj_id != $curr_id) {
                                                                $values[] = get_post($_obj_id)->post_title;
                                                            }
                                                       }
                                                      
                                                   }
                                                   ?>
                                            <input id="tableV-meta-<?= $_META['id']; ?>" type="hidden" name="item_table_meta"
                                                   data-parent="<?= $_father_name ?>" value="<?= (count($values)>0) ? implode('<br>', $values) : $_item_meta_val; ?>" />
                                                   <?php
                                               }
                                           endif; //is_object
                                       endif; // is_string
                                   endforeach;
                               endif;
                               ?>

                        <h4 class="item-display-title">
                            <!--a href="<?php echo get_collection_item_href($collection_id, $curr_id); ?>"
                               onclick="<?php get_item_click_event($collection_id, $curr_id) ?>"-->
                            <a href="<?php echo get_collection_item_href($collection_id, $curr_id, $viewHelper); ?>"
                               onclick="<?php get_item_click_event($collection_id, $curr_id) ?>">
                                   <?php echo wp_trim_words($_item_title_, 13); ?>
                            </a>
                        </h4>

                        <div class="item-description"> <?php echo $_trim_desc; ?> </div>
                        <div class='item-desc-hidden-full' style="display: none"><?php echo $_object_description; ?></div>       

                        <div class="row author-created">
                            <div class="col-md-6 author">
                                <div class="item-author"><?php echo "<strong>" . __('Created by: ', 'tainacan') . "</strong>" . wp_trim_words(get_the_author(), 2); ?></div>
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
                                    <li class="item-redesocial tainacan-museum-clear">
                                        <a id="modal_network<?php echo $curr_id; ?>" onclick="showModalShareNetwork(<?php echo $curr_id; ?>)">
                                            <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                                        </a>
                                    </li>
                                    <?php // include "edit_btns.php";   ?>
                                </ul>

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
                                    <?php // include "edit_btns_table.php";   ?>
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