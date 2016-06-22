<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/edit_js.php');
?>
<div class="fuelux">
    <div id="MyWizard" class="fuelux wizard">

        <ul class="steps fuelux step-content">
            <a onclick="showCollectionConfiguration('<?php echo get_template_directory_uri() ?>');"><li ><span class="fuelux badge">1</span><?php echo __("Configuration",'tainacan') ?><span class="chevron"></span></li></a>
            <a onclick="showPropertiesConfiguration('<?php echo get_template_directory_uri() ?>');"><li><span class="fuelux badge">2</span><?php echo __("Metadata",'tainacan') ?><span class="fuelux chevron"></span></li></a>
            <a onclick="showRankingConfiguration('<?php echo get_template_directory_uri() ?>');"><li><span class="fuelux badge">3</span><?= __("Rankings",'tainacan') ?><span class="fuelux chevron"></span></li></a>
            <a onclick="showSearchConfiguration('<?php echo get_template_directory_uri() ?>');"><li><span class="fuelux badge">4</span><?= __("Searching",'tainacan') ?><span class="fuelux chevron"></span></li></a>
            <a onclick="showDesignConfiguration('<?php echo get_template_directory_uri() ?>');"><li class="active"><span class="badge badge-info">5</span><?= __("Design",'tainacan') ?><span class="fuelux chevron"></span></li></a>
        </ul>
        <div class="fuelux actions">
            <a onclick="showSearchConfiguration('<?php echo get_template_directory_uri() ?>');" href="#" class="btn btn-mini btn-prev"> <span class="glyphicon glyphicon-chevron-left"></span></i><?php echo __("Previous",'tainacan') ?></a>
        </div>
    </div>
</div> 
<div class="row" id="design_create_opt">
    <div class="col-md-1">
        &nbsp;
    </div>
    <div class="col-md-10">
        <h3><?php _e('Design','tainacan') ?></h3><hr> 
        <form method="POST">
            <div class="form-group">
                <!--label for="property_data_required">< ?php _e('Create a voting for the items in the collection'); ?>:</label><br-->
                <input type="radio" name="property_data_required" id="property_data_required_true" checked="checked"  value="true">&nbsp;<?php _e('Use standard design','tainacan'); ?>
            </div>
            <button type="button" onclick="nextStep()" id="save_and_next" name="save_and_next" class="btn btn-primary" style="float: right;" value="next"><?php _e('Next Step','tainacan'); ?></button>
        </form>
        <div class="form-group">
            <a href="#ranking_tabs" id="show_design_link" onclick="showPersonalizeDesign();"><?php _e('Personalize Design','tainacan'); ?></a>
            <a href="#ranking_tabs" id="hide_design_link" onclick="hidePersonalizeDesign();" style="display: none;"><?php _e('Hide Design','tainacan'); ?></a>
        </div>
    </div>
</div>
<div class="categories_menu" class="row" id="personalize_design">
    <div class="col-md-1">
        <br>
        
    </div>	
    <div class="col-md-10">

        <h3><?php _e('Personalize Design','tainacan'); ?><button class="btn btn-default pull-right" onclick="backToMainPage()" id="btn_back_collection"><?php _e('Back to collection','tainacan'); ?></button></h3>
        <hr>
        <div id="alert_success_categories" class="alert alert-success" style="display: none;">
            <button type="button" class="close" onclick="hide_alert();"><span aria-hidden="true">&times;</span></button>
            <?php _e('Operation was successful.','tainacan') ?>
        </div>    
        <div id="alert_error_categories" class="alert alert-danger" style="display: none;">
            <button type="button" class="close" onclick="hide_alert();"><span aria-hidden="true">&times;</span></button>
            <?php _e('Error! Operation was unsuccessful.','tainacan') ?>&nbsp;<span id="message_category"></span>
        </div>
        <form  id="submit_form_edit_design_collection">
            <div class="form-group">
                <h5><strong><?php _e('Set some colors to your collection','tainacan'); ?></strong></h5>
                <div class="col-md-12">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="background_color"><?php _e('Board Background Color','tainacan'); ?></label>
                            <input type="text" class="form-control colorpicker_socialdb" id="background_color" name="background_color" placeholder="<?php _e('Board Background Color','tainacan'); ?>" value="<?php echo $collection_metas['socialdb_collection_board_background_color']; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="border_color"><?php _e('Board Border Color','tainacan'); ?></label>
                            <input type="text" class="form-control colorpicker_socialdb" id="border_color" name="border_color" placeholder="<?php _e('Board Border Color','tainacan'); ?>" value="<?php echo $collection_metas['socialdb_collection_board_border_color']; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="font_color"><?php _e('Board Font Color','tainacan'); ?></label>
                            <input type="text" class="form-control colorpicker_socialdb" id="font_color" name="font_color" placeholder="<?php _e('Board Font Color','tainacan'); ?>" value="<?php echo $collection_metas['socialdb_collection_board_font_color']; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="link_color"><?php _e('Board Link Color','tainacan'); ?></label>
                            <input type="text" class="form-control colorpicker_socialdb" id="link_color" name="link_color" placeholder="<?php _e('Board Link Color','tainacan'); ?>" value="<?php echo $collection_metas['socialdb_collection_board_link_color']; ?>">
                        </div>
                    </div>
                </div>
            </div>
            <h5><strong><?php _e('Sets a style for the header of your collection','tainacan'); ?></strong></h5>
            <div class="form-group">
                <label class="radio-inline">
                    <input type="radio" name="BoardSkinOptions" id="skin_thumbnail" value="skin_thumbnail" <?php
            if ($collection_metas['socialdb_collection_board_skin_mode'] == "skin_thumbnail") {
                echo "checked";
            }
            ?>> <?php _e('Thumbnail','tainacan'); ?>
                </label>
                <label class="radio-inline">
                    <input type="radio" name="BoardSkinOptions" id="skin_cover" value="skin_cover" <?php
                    if ($collection_metas['socialdb_collection_board_skin_mode'] == "skin_cover") {
                        echo "checked";
                    }
            ?>> <?php _e('Cover','tainacan'); ?>
                </label>
            </div>
            <h5><strong><?php _e('Hide details of your collection','tainacan'); ?></strong></h5>
            <div class="form-group">
                <label class="checkbox-inline">
                    <input type="checkbox" id="HideOptions_Title" name="HideOptions_Title" value="hide_title" <?php
                    if ($collection_metas['socialdb_collection_hide_title'] == "hide_title") {
                        echo "checked";
                    }
            ?>> <?php _e('Title','tainacan'); ?>
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" id="HideOptions_Descriprion" name="HideOptions_Descriprion" value="hide_description" <?php
                    if ($collection_metas['socialdb_collection_hide_description'] == "hide_description") {
                        echo "checked";
                    }
            ?>> <?php _e('Description','tainacan'); ?>
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" id="HideOptions_Thumb" name="HideOptions_Thumb" value="hide_thumb" <?php
                    if ($collection_metas['socialdb_collection_hide_thumbnail'] == "hide_thumb") {
                        echo "checked";
                    }
            ?>> <?php _e('Thumbnail','tainacan'); ?>
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" id="HideOptions_Menu" name="HideOptions_Menu" value="hide_menu" <?php
                    if ($collection_metas['socialdb_collection_hide_menu'] == "hide_menu") {
                        echo "checked";
                    }
            ?>> <?php _e('Menu','tainacan'); ?>
                </label>
                <label class="checkbox-inline">
                    <input type="checkbox" id="HideOptions_Category" name="HideOptions_Category" value="hide_category" <?php
                    if ($collection_metas['socialdb_collection_hide_categories'] == "hide_category") {
                        echo "checked";
                    }
            ?>> <?php _e('Categories','tainacan'); ?>
                </label>
                 <label class="checkbox-inline">
                    <input type="checkbox" id="HideOptions_Rankings" name="HideOptions_Rankings" value="hide_rankings" <?php
                    if ($collection_metas['socialdb_collection_hide_rankings'] == "hide_rankings") {
                        echo "checked";
                    }
            ?>> <?php _e('Rankings','tainacan'); ?>
                </label>
            </div>
            <div class="form-group">
                <label for="select_qtd_columns"><?php _e('Set the number of columns in the object list page','tainacan'); ?></label>
                <select id="select_qtd_columns" name="select_qtd_columns" class="form-control">
                    <option value="1" <?php
                    if ($collection_metas['socialdb_collection_columns'] == "1") {
                        echo 'selected="selected"';
                    }
            ?>>1</option>
                    <option value="2" <?php
                    if ($collection_metas['socialdb_collection_columns'] == "2") {
                        echo 'selected="selected"';
                    }
            ?>>2</option>
                    <option value="3" <?php
                    if ($collection_metas['socialdb_collection_columns'] == "3") {
                        echo 'selected="selected"';
                    }
            ?>>3</option>
                    <option value="4" <?php
                    if ($collection_metas['socialdb_collection_columns'] == "4") {
                        echo 'selected="selected"';
                    }
            ?>>4</option>
                </select>
            </div>
            <div class="form-group">
                <label for="thumb_size"><?php _e('Set the thumbnail size','tainacan'); ?></label>
                <!--input type="text" class="form-control" id="thumb_size" name="thumb_size" value="< ?php echo $collection_metas['socialdb_collection_size_thumbnail']; ?>"-->
                <select id="thumb_size" name="thumb_size" class="form-control">
                    <option value="thumbnail" <?php
                    if ($collection_metas['socialdb_collection_size_thumbnail'] == "thumbnail") {
                        echo 'selected="selected"';
                    }
            ?>><?php _e('Thumbnail','tainacan'); ?></option>
                    <option value="medium" <?php
                    if ($collection_metas['socialdb_collection_size_thumbnail'] == "medium") {
                        echo 'selected="selected"';
                    }
            ?>><?php _e('Medium','tainacan'); ?></option>
                    <option value="large" <?php
                    if ($collection_metas['socialdb_collection_size_thumbnail'] == "large") {
                        echo 'selected="selected"';
                    }
            ?>><?php _e('Large','tainacan'); ?></option>
                    <option value="full" <?php
                    if ($collection_metas['socialdb_collection_size_thumbnail'] == "full") {
                        echo 'selected="selected"';
                    }
            ?>><?php _e('Full','tainacan'); ?></option>
                </select>
            </div>

<!--            <h5><strong><?php _e('Set the color of each facet','tainacan'); ?></strong></h5>-->
           <!-- //<?php
//            foreach ($collection_metas['socialdb_collection_facets'] as $facet) {
//                echo $facet->name;
//
//                echo '<div class="form-group">';
//                for ($i = 1; $i < 14; $i++) {
//
//                    echo '<label class="radio-inline">
//                        <input type="radio" name="color_' . $facet->term_id . '" id="color' . $i . '" value="color' . $i . '" ';
//                    if ($collection_metas['socialdb_collection_facet_' . $facet->term_id . '_color'] == "color" . $i) {
//                        echo 'checked="checked"';
//                    }
//                    echo '><img src="' . get_template_directory_uri() . '/libraries/images/cor' . $i . '.png">
//                      </label>';
//                }
//                echo '</div>';
//
//                echo "<hr>";
//            }
//            if ($collection_metas['socialdb_collection_property_object_facets'] && is_array($collection_metas['socialdb_collection_property_object_facets'])) {
//                foreach ($collection_metas['socialdb_collection_property_object_facets'] as $facet_property) {
//                    echo $facet_property['name'];
//
//                    echo '<div class="form-group">';
//                    for ($i = 1; $i < 14; $i++) {
//
//                        echo '<label class="radio-inline">
//                        <input type="radio" name="color_' . $facet_property['id'] . '" id="color_property' . $i . '" value="color_property' . $i . '" ';
//                        if ($collection_metas['socialdb_collection_facet_' . $facet_property['id'] . '_color'] == "color_property" . $i) {
//                            echo 'checked="checked"';
//                        }
//                        echo '><img src="' . get_template_directory_uri() . '/libraries/images/cor_propriedade' . $i . '.png">
//                      </label>';
//                    }
//                    echo '</div>';
//
//                    echo "<hr>";
//                }
//            }
//            ?>-->
            <input type="hidden" id="collection_id" name="collection_id" value="<?php echo $collection_post->ID; ?>">
            <input type="hidden" id="operation" name="operation" value="update">
            <button type="submit" id="submit" class="btn btn-success"><?php _e('Save','tainacan'); ?></button>
        </form>
    </div>	
</div>