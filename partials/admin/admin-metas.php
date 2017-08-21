<?php
include_once ( dirname(__FILE__) . '/../../helpers/view_helper.php');
include_once ( dirname(__FILE__) . '/../../views/theme_options/property/js/compounds_js.php');
include_once ( dirname(__FILE__) . '/../../views/theme_options/property/js/list_metadata_js.php');
$view_helper = new ViewHelper();

$category = get_term_by("slug", "socialdb_category", "socialdb_category_type");
?>

<?php // $view_helper->render_header_config_steps('metadata') ?>
<input type="hidden" name="property_category_id" id="property_category_id" value="<?php echo $category->term_id ?>"/>
<input type="hidden" id="src" value="<?php echo site_url(); ?>"/>
<div class="categories_menu col-md-12 no-padding"  id="properties_tabs">

    <div class="categories_menu col-md-12 no-padding"  id="properties_tabs" style="margin-top: 10px;">
        <!-- Fitros -->
        <div id="preset-filters" class="col-md-3 preset-filters ui-widget-header no-padding">
            <div class="btn-group">
                <button
                        style="margin: 20px 0px 0px 20px;width: 250px;"
                        class="btn btn-primary btn-block btn-lg dropdown-toggle"
                        data-toggle="dropdown"
                        aria-expanded="false" aria-haspopup="true" >
                    <span style="color:white;"><?php _e('Add Filter', 'tainacan'); ?> <span style="color:white;" class="caret"></span></span>
                </button>
                <ul style="margin-left: 20px;" class="dropdown-menu" id="dropdown-filters">
                    <li>
                        <!--a  data-toggle="modal" data-target="#meta-<?php echo $type ?>"-->
                        <a onclick="add_colaboration_ranking()"  >
                            <?php _e('Colaboration Ranking','tainacan'); ?>
                        </a>
                    </li>
                </ul>
            </div>
            <ul style="margin-top: 20px;" id="filters-accordion" class="connectedSortable"></ul>
        </div>

        <!-- METADADOS -->
        <div class="col-md-9 metadata-actions" >

            <div class="col-md-12 no-padding action-messages" >
                <div id="alert_success_properties" class="alert alert-success" style="display: none; margin-top: 20px;">
                    <button type="button" class="close" onclick="hide_alert();"><span aria-hidden="true">&times;</span></button>
                    <?php _e('Operation was successful.','tainacan') ?>
                </div>
                <div id="alert_error_properties" class="alert alert-danger" style="display: none; margin-top: 20px;">
                    <button type="button" class="close" onclick="hide_alert();"><span aria-hidden="true">&times;</span></button>
                    <span id="default_message_error">
                <?php _e('Error! Operation was unsuccessful.', 'tainacan') ?>
            </span>&nbsp;
                    <span id="message_category"></span>
                </div>
            </div>

            <div class="add-property-btn btn-group col-md-12">
                <button class="btn btn-default btn-lg dropdown-toggle" data-toggle="dropdown" aria-expanded="false" aria-haspopup="true" onclick="resetAllForms()">
                    <?php _e('Add Property', 'tainacan'); ?> <span class="caret"></span>
                </button>

                <ul class="dropdown-menu add-property-dropdown">
                    <?php foreach( $view_helper->get_metadata_types() as $type => $label):  ?>
                        <li>
                            <!--a  data-toggle="modal" data-target="#meta-<?php echo $type ?>"-->
                            <a onclick="$('#meta-<?php echo $type ?>').modal('show')" >
                                <img src="<?php $view_helper->get_metadata_icon($type); ?>"
                                    <?php if($type=='metadata_compound'): echo 'height="15" width="15"'; endif;?>
                                     alt="<?php echo $type ?>"
                                     title="<?php echo $type ?>">
                                <?php echo $label ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="col-md-2 right back-to-collection" style="padding: 0 2% 0 0;">
                    <button onclick='window.history.back()' class='btn btn-default pull-right'> <?php _t("Back",1); ?> </button>
                </div>
            </div>

            <div id="tab-content-metadata" class="tab-content ui-widget ui-helper-clearfix col-md-12" style="background: white; padding-bottom: 20px">
                <ul id="metadata-container" class="gallery ui-helper-reset ui-helper-clearfix connectedSortable  metadata-container">
                    <?php
                    foreach($view_helper->get_default_metadata() as $meta_id => $metadata):
                        $title = __($metadata, 'tainacan'); ?>

                        <li id='<?php echo $meta_id ?>' data-widget='tree' class='ui-widget-content ui-corner-tr fixed-meta'>
                            <label class='title-pipe'>
                                <?php echo $title ?>
                            </label>
                            <div class='action-icons default-metadata'>
                                <a onclick='edit_filter(this)' class='<?php echo $meta_id ?>' data-title='<?php echo $title ?>'>
                                    <span class='glyphicon glyphicon-edit'> </span>
                                </a>
                                <span class='glyphicon glyphicon-trash no-edit'> </span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div id="loader_metadados_page" style="display: none;">
                    <center>
                        <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                        <h4><?php _e('Loading metadata...', 'tainacan') ?></h4>
                    </center>
                </div>
            </div>

            <?php include_once ( get_stylesheet_directory() . "/views/theme_options/property/metadata_forms.php"); ?>

            <input type="hidden" id="collection_list_ranking_id" name="collection_id" value="">
        </div>
    </div>
</div> <!-- #properties-tabs -->