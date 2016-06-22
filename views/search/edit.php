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
            <a onclick="showSearchConfiguration('<?php echo get_template_directory_uri() ?>');"><li class="active"><span class="badge badge-info">4</span><?= __("Searching",'tainacan') ?><span class="fuelux chevron"></span></li></a>
            <a onclick="showDesignConfiguration('<?php echo get_template_directory_uri() ?>');"><li><span class="fuelux badge">5</span><?= __("Design") ?><span class="fuelux chevron"></span></li></a>
        </ul>
        <div class="fuelux actions">
            <a onclick="showRankingConfiguration('<?php echo get_template_directory_uri() ?>');" href="#" class="btn btn-mini btn-prev"> <span class="glyphicon glyphicon-chevron-left"></span></i><?php echo __("Previous",'tainacan') ?></a>
            <a onclick="showDesignConfiguration('<?php echo get_template_directory_uri() ?>');" href="#" class="btn btn-mini btn-next" data-last="Finish"><?php echo __("Next",'tainacan') ?><span class="glyphicon glyphicon-chevron-right"></span></i></a>
        </div>
    </div>
</div>

<div class="row" id="search_create_opt">
    <div class="col-md-1">
        &nbsp;
    </div>
    <div class="col-md-10">
        <h3><?php _e('Search','tainacan') ?></h3>
        <hr>
        <form method="POST">
            <div class="form-group">
                <!--label for="property_data_required">< ?php _e('Create a voting for the items in the collection'); ?>:</label><br-->
                <input type="radio" name="property_data_required" id="property_data_required_true" checked="checked"  value="true">&nbsp;<?php _e('Use standard search interface','tainacan'); ?>
            </div>
            <button type="button" onclick="nextStep()" id="save_and_next" name="save_and_next" class="btn btn-primary" style="float: right;" value="next"><?php _e('Next Step','tainacan'); ?></button>
        </form>
        <div class="form-group">
            <a href="#" id="show_search_link" onclick="showPersonalizeSearch();"><?php _e('Personalize search interface','tainacan'); ?></a>
            <a href="#" id="hide_search_link" onclick="hidePersonalizeSearch();" style="display: none;"><?php _e('Hide search interface','tainacan'); ?></a>
        </div>
    </div>
</div>

<div class="categories_menu" class="row" id="personalize_search">
    <div class="row">
        <div class="col-md-1">
            <!--br>
            <button class="btn btn-default pull-right" onclick="backToMainPage()" id="btn_back_collection"><?php _e('Back to collection','tainacan'); ?></button-->
        </div>
        <div class="col-md-10">
            <h3><?php _e('Ordination','tainacan'); ?><button class="btn btn-default pull-right" onclick="backToMainPage()" id="btn_back_collection"><?php _e('Back to collection','tainacan'); ?></button></h3>
            <hr>
            <form method="POST" name="form_ordenation_search" id="form_ordenation_search">
                <input type="hidden" name="property_category_id"  value="<?php echo $category_root_id; ?>">
                <!------------------- Seleciona as propriedades de atributo para ser coluna de orden -->
                 <div class="form-group">
                    <label for="collection_order"><?php _e('Select the properties for ordenation','tainacan'); ?></label>
                      <div class="row">
                          <div class="col-md-5">
                              <select  id="collection_order_properties" multiple size="5" class='form-control' name="collection_order_properties[]" >
                              </select>
                         </div>
                          <div class="col-md-1"><br><button onclick="add_property_ordenation()" ><span class="glyphicon glyphicon-forward"></span></button></div>
                          <div class="col-md-6">
                              <select onclick="remove_property_ordenation(this)" id="collection_order_selected_properties" multiple size="5"  class='form-control' name="collection_order_selected_properties" >
                              </select>
                          </div>
                     </div>

                </div>
                <!------------------- Ordenacao-------------------------->
                <div class="form-group">
                    <label for="collection_order"><?php _e('Select the default ordination','tainacan'); ?></label>
                    <select id="collection_order" name="collection_order" class="form-control">
                    </select>
                </div>

                <!------------------- Forma de ordenacao-------------------------->
                <div class="form-group">
                    <label for="collection_ordenation_form"><?php _e('Select the ordination form','tainacan'); ?></label>
                    <select name="socialdb_collection_ordenation_form" class="form-control">
                        <option value="desc" <?php
                        if ($ordenation['collection_metas']['socialdb_collection_ordenation_form'] == 'desc' || empty($ordenation['collection_metas']['socialdb_collection_ordenation_form'])) {
                            echo 'selected = "selected"';
                        }
                        ?>>
                                    <?php _e('DESC','tainacan'); ?>
                        </option>
                        <option value="asc" <?php
                        if ($ordenation['collection_metas']['socialdb_collection_ordenation_form'] == 'asc') {
                            echo 'selected = "selected"';
                        }
                        ?>>
                                    <?php _e('ASC','tainacan'); ?>
                        </option>
                    </select>
                </div>
                <input type="hidden" id="collection_id_order_form" name="collection_id" value="<?php echo $collection_id; ?>">
                <input type="hidden" id="operation" name="operation" value="update_ordenation">
                <button type="submit" id="submit_ordenation_form" class="btn btn-success"><?php _e('Save','tainacan'); ?></button>
            </form><br>
            <hr>
            <h3><?php _e('Facets','tainacan'); ?></h3><br>
            <h4><?php _e('Horizontal','tainacan'); ?></h4>
            <div id="list_search_data">
                <table id="table_search_data_id" class="table table-bordered" style="background-color: #d9edf7;">
                    <thead>
                        <tr>
                            <th><?php _e('Name','tainacan'); ?></th>
                            <th ><?php _e('Type','tainacan'); ?></th>
                            <th style="width: 10%"><?php _e('Edit','tainacan'); ?></th>
                            <th style="width: 10%"><?php _e('Delete','tainacan'); ?></th>
                            <th style="width: 5%"><?php _e('Order','tainacan'); ?></th>
                            <th style="width: 5%"><?php _e('Priority','tainacan'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="table_search_data">
                    </tbody>
                    <!--tfoot>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                    </tfoot-->
                </table>
            </div>
            <h4><?php _e('Left Column','tainacan'); ?></h4>
            <div id="list_search_data_left_column">
                <table id="table_search_data_left_column_id" class="table table-bordered" style="background-color: #d9edf7;">
                    <thead>
                        <tr>
                            <th><?php _e('Name','tainacan'); ?></th>
                            <th ><?php _e('Type','tainacan'); ?></th>
                            <th style="width: 10%"><?php _e('Edit','tainacan'); ?></th>
                            <th style="width: 10%"><?php _e('Delete','tainacan'); ?></th>
                            <th style="width: 5%"><?php _e('Order','tainacan'); ?></th>
                            <th style="width: 5%"><?php _e('Priority','tainacan'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="table_search_data_left_column">
                    </tbody>
                    <!--tfoot>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                    </tfoot-->
                </table>

            </div>
            <h4><?php _e('Right Column','tainacan'); ?></h4>
            <div id="list_search_data_right_column">
                <table id="table_search_data_right_column_id" class="table table-bordered" style="background-color: #d9edf7;">
                    <thead>
                        <tr>
                            <th><?php _e('Name','tainacan'); ?></th>
                            <th ><?php _e('Type','tainacan'); ?></th>
                            <th style="width: 10%"><?php _e('Edit','tainacan'); ?></th>
                            <th style="width: 10%"><?php _e('Delete','tainacan'); ?></th>
                            <th style="width: 5%"><?php _e('Order','tainacan'); ?></th>
                            <th style="width: 5%"><?php _e('Priority','tainacan'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="table_search_data_right_column">
                    </tbody>
                    <!--tfoot>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                    </tfoot-->
                </table>
            </div>
            <div id="no_search_data" style="display: none;">
                <div class="alert alert-info">
                    <center><?php _e('No facet added','tainacan'); ?></center>
                </div>
            </div>
        </div>
    </div><br>
    <div class="col-md-1">
        &nbsp;
    </div>
    <div class="col-md-4">
        <h3><?php _e('Personalize Search Interface','tainacan'); ?></h3>
        <div id="alert_success_search" class="alert alert-success" style="display: none;">
            <button type="button" class="close" onclick="hide_alert();"><span aria-hidden="true">&times;</span></button>
            <?php _e('Operation was successful.','tainacan') ?>
        </div>
        <div id="alert_error_search" class="alert alert-danger" style="display: none;">
            <button type="button" class="close" onclick="hide_alert();"><span aria-hidden="true">&times;</span></button>
            <?php _e('Error! Operation was unsuccessful.','tainacan') ?>&nbsp;<span id="message_category"></span>
        </div>
        <div>
            <form id="submit_form_search_data">
                <input type='hidden' id='counter_range' name="counter_range" value=''>
                <div class="form-group">
                    <label for="search_add_facet"><?php _e('Add Facet','tainacan'); ?></label>
                    <select class="form-control" id="search_add_facet" name="search_add_facet" onchange="get_widgets(this);">
                        <option>Selecione...</option>
                        <!-- alterar -->
                         <optgroup label="<?php _e('Standart Options','tainacan'); ?>">
                              <option value="ranking_colaborations"><?php _e('Colaboration Ranking','tainacan'); ?></option>
                              <option value="notifications"><?php _e('Notifications','tainacan'); ?></option>
                              <?php do_action('add_standart_options_label_filters') ?>
                            <!--option value=""><?php _e('Hierarchy of collections','tainacan'); ?></option>-->
                            <!--option value="<?php echo $category_root_id; ?>"><?php echo __('Category root: ') . $category_root_name; ?></option-->
                        </optgroup>
                         <!-- alterar -->
                        <optgroup label="<?php _e('Property Data','tainacan'); ?>">
                            <option value="socialdb_object_from"><?php _e('Format','tainacan'); ?></option>
                            <option value="socialdb_object_dc_type"><?php _e('Type','tainacan'); ?></option>
                            <option value="socialdb_object_dc_source"><?php _e('Source','tainacan'); ?></option>
                            <option value="socialdb_license_id"><?php _e('License Type','tainacan'); ?></option>
                        <?php if (isset($property_data)): ?>
                                <?php foreach ($property_data as $property) { ?>
                                    <option value="<?php echo $property['id']; ?>"><?php echo $property['name']; ?></option>
                                <?php } ?>
                        <?php endif; ?>
                          </optgroup>
                        <?php if (isset($property_object)): ?>
                            <optgroup label="<?php _e('Object Properties','tainacan'); ?>">
                                <?php foreach ($property_object as $property) { ?>
                                    <option value="<?php echo $property['id']; ?>"><?php echo $property['name']; ?></option>
                                <?php } ?>
                            </optgroup>
                        <?php endif; ?>
                        <?php 
                        if (isset($property_term)): ?>
                            <optgroup label="<?php _e('Term Properties','tainacan'); ?>">
                                 <option
                            <?php
                                if ($ordenation['collection_metas']['socialdb_collection_hide_tags'] == 'yes') {
                                    echo 'disabled = "disabled"';
                                }
                            ?> value="tag"><?php _e('Tags','tainacan'); ?></option>
                                <?php
                                foreach ($property_term as $property) {
                                    $term = get_term_by('id', $property['metas']['socialdb_property_term_root'], 'socialdb_category_type');
                                    $ids_properties_term[] = $term->term_id;
                                    ?>
                                    <option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?> - <?php echo __('Property Term: ','tainacan').$property['name']; ?> </option>
                                <?php } ?>
                            </optgroup>
                        <?php endif; ?>
                        <?php if (isset($rankings)): ?>
                            <optgroup label="<?php _e('Rankings','tainacan'); ?>">
                                <?php foreach ($rankings as $ranking) { ?>
                                    <option value="<?php echo $ranking['id']; ?>"><?php echo $ranking['name'].' - '.__('Type','tainacan').':'.$ranking['type']; ?></option>
                                <?php } ?>
                             </optgroup>
                        <?php endif; ?>
                        <?php do_action('add_optiongroup_label_filters',$collection_id,['property_data'=>'','property_object'=>'','property_term'=>($property_term)?$property_term:[]]) ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="search_data_widget"><?php _e('Widget','tainacan'); ?></label>
                    <select class="form-control" id="search_data_widget" name="search_data_widget" onchange="hide_orientation(this,$('#search_add_facet').val());" required="">
                    </select>
                </div>
                <div id="color_field_search" style="display: none;">
                    <h5><strong><?php _e('Set the facet color','tainacan'); ?></strong></h5>
                    <?php
                    echo '<div class="form-group">';
                    for ($i = 1; $i < 14; $i++) {
                        echo '<label class="radio-inline">
                                            <input type="radio" name="color_facet" id="color' . $i . '" value="color' . $i . '" ';
                        echo '><img src="' . get_template_directory_uri() . '/libraries/images/cor' . $i . '.png">
                                        </label>';
                    }
                    echo '</div>';
                    ?>
                </div>
                <div id="color_field_property_search" style="display: none;">
                    <h5><strong><?php _e('Set the facet color','tainacan'); ?></strong></h5>
                    <?php
                    echo '<div class="form-group">';
                    for ($i = 1; $i < 14; $i++) {
                        echo '<label class="radio-inline"> <input type="radio" name="color_facet" id="color_property' . $i . '" value="color_property' . $i . '" ';
                        echo '><img src="' . get_template_directory_uri() . '/libraries/images/cor' . $i . '.png">  </label>';
                    }
                    echo '</div>';
                    ?>
                </div>

                <div id="range_submit" style="display: none;">
                    <div id="range_form">
                    </div>
                    <button type="button" onclick="append_range()"><span class="glyphicon glyphicon-plus"></span><?php _e('Add','tainacan') ?></button>
                </div>

                <div id="orientation_field" class="form-group">
                    <label for="search_data_orientation"><?php _e('Orientation','tainacan'); ?></label>
                    <select disabled="disabled" class="form-control" id="search_data_orientation" name="search_data_orientation" onchange="showOrientationStyles()">
                        <option selected="selected" value="left-column"  class="flyout vertical"> <?php _e('Left Column','tainacan'); ?></option>
                        <!--option value="right-column" class="vertical"> <?php _e('Right Column','tainacan'); ?></option>
                        <option value="horizontal"   class="tabbed drop-down horizontal"> <?php _e('Horizontal','tainacan'); ?></option-->
                    </select>
                </div>

                <div class="form-group" id="select_menu_style" style="display: none">
                    <label for="select_menu_style"> <?php _e('Select Menu Style', 'tainacan') ?> </label>
                    <select class="form-control select2-menu" id="select_menu_style" name="select_menu_style">
                        <optgroup label="<?php _e('Select the style for your facet', 'tainacan') ?>">
                        <?php foreach ( $menu_style_ids as $menu_id): ?>
                                <option value="menu_style_<?php echo $menu_id?>" id="menu_style_<?php echo $menu_id?>"> </option>
                        <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>

                <input type="hidden" name="search_data_orientation" value="left-column">
                <input type="hidden" id="operation_search_data" name="operation" value="add">
                <input type="hidden" id="property_id" name="property_id" value="">
                <input type="hidden" id="search_collection_id" name="collection_id" value="<?php echo $collection_id; ?>">
                <button type="submit" id="submit_search_data" class="btn btn-success"><?php _e('Save','tainacan'); ?></button>
                <button type="button" id="btn_add_new" style="display: none;" onclick="showSearchConfiguration('<?php echo get_template_directory_uri() ?>');" class="btn btn-default"><?php _e('Add New','tainacan'); ?></button>
            </form>
        </div>
    </div>
    <div class="col-md-1">
        &nbsp;
    </div>
    <div class="col-md-4" style="background-color: #f5f5f5; border: 1px solid;">
        <h3><?php _e('Default Tree Widget Configuration','tainacan'); ?></h3><br>
        <div class="row">
            <div class="col-md-5">
                <div class="form-group">
                    <label for="socialdb_collection_widget_tree"><?php _e('Default Tree Widget','tainacan'); ?></label><br>
                    <input type="radio" id="widget_tree_dynatree" name="socialdb_collection_widget_tree" onclick="save_widget_tree(this);" value="dynatree" <?php
                    if ($default_widget_tree == 'dynatree' || $default_widget_tree == '') {
                        echo 'checked="checked"';
                    }
                    ?>> <?php _e('Dynatree','tainacan'); ?><br>
                    <input disabled="disabled" type="radio" id="widget_tree_hypertree" name="socialdb_collection_widget_tree" onclick="save_widget_tree(this);" value="hypertree" <?php
                    if ($default_widget_tree == 'hypertree') {
                        echo 'checked="checked"';
                    }
                    ?>> <?php _e('Hypertree','tainacan'); ?><br>
                    <input disabled="disabled" type="radio" id="widget_tree_spacetree" name="socialdb_collection_widget_tree" onclick="save_widget_tree(this);" value="spacetree" <?php
                    if ($default_widget_tree == 'spacetree') {
                        echo 'checked="checked"';
                    }
                    ?>> <?php _e('Spacetree','tainacan'); ?><br>
                    <input disabled="disabled" type="radio" id="widget_tree_treemap" name="socialdb_collection_widget_tree" onclick="save_widget_tree(this);" value="treemap" <?php
                    if ($default_widget_tree == 'treemap') {
                        echo 'checked="checked"';
                    }
                    ?>> <?php _e('Treemap','tainacan'); ?><br>
                    <input disabled="disabled" type="radio" id="widget_tree_rgraph" name="socialdb_collection_widget_tree" onclick="save_widget_tree(this);" value="rgraph" <?php
                    if ($default_widget_tree == 'rgraph') {
                        echo 'checked="checked"';
                    }
                    ?>> <?php _e('RGraph','tainacan'); ?><br>

                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label for="socialdb_collection_widget_tree_orientation"><?php _e('Default Tree Widget Orientation','tainacan'); ?></label><br>
                    <input disabled="disabled" type="radio" id="widget_tree_orientation_left" name="socialdb_collection_widget_tree_orientation" onclick="save_widget_tree_orientation(this);" value="left-column" <?php
                    if ($default_widget_tree_orientation == 'left-column' || $default_widget_tree_orientation == '') {
                        echo 'checked="checked"';
                    }
                    ?>> <?php _e('Left Column','tainacan'); ?><br>
                    <!--input type="radio" id="widget_tree_orientation_right" name="socialdb_collection_widget_tree_orientation" onclick="save_widget_tree_orientation(this);" value="right-column" <?php
                    if ($default_widget_tree_orientation == 'right-column') {
                        echo 'checked="checked"';
                    }
                    ?>> <?php _e('Right Column','tainacan'); ?><br-->

                </div>
            </div>
        </div>
    </div>
    <div class="col-md-1">
        &nbsp;
    </div>
</div>