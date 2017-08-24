<?php
include_once('../../helpers/view_helper.php');
include_once('js/tabs_js.php');
include_once('js/compounds_js.php');
include_once ('js/list_category_metadata_js.php');

$view_helper = new ViewHelper();
$meta = get_post_meta($collection_id, 'socialdb_collection_fixed_properties_visibility', true);
$tabs = unserialize(get_post_meta($collection_id, 'socialdb_collection_update_tab_organization',true));
$array_visibility = ($meta&&$meta!=='') ? $meta : '';
?>
<style>
    .ui-widget-content {
         border: none;
        color: #222222;
    }
    .title-pipe .glyphicon-arrow-left{
        display: none;
    }
    .glyphicon-eye-open{
        color: #ddd;
    }
</style>
<style>
            #properties_tabs ul.metadata-container li{
                border: 1px solid #e3e3e3 !important;
            }
            .gallery li {
                padding: 0.4em;
                margin: 0;
            }
            .list-compounded{
                margin-left: 15px;
                margin-top: 15px;
            }
            .list-compounded li{
                cursor: pointer;
            }
</style>
<?php $view_helper->render_header_config_steps('metadata') ?>
<input type="hidden" name="tabs_properties" id="tabs_properties"
       value='<?php echo ($tabs&&is_array($tabs)) ? json_encode($tabs) :  ''; ?>'/>
<input type="hidden" name="visibility_collection_properties" id="visibility_collection_properties" value='<?php echo $array_visibility; ?>'/>
<input type="hidden" name="property_category_id" id="property_category_id" value="<?php echo $category->term_id; ?>"/>
<div class="categories_menu col-md-12 no-padding"  id="properties_tabs" style="min-height: 600px;">

    <div id="preset-filters" class="preset-filters ui-widget-header no-padding hide">
        <ul id="filters-accordion" class="connectedSortable"></ul>
    </div>

    <div class="col-md-12 ui-widget-content metadata-actions" style="padding-right: 0;">

        <div class="col-md-12 no-padding action-messages">
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
            <button class="btn btn-primary btn dropdown-toggle" data-toggle="dropdown" aria-expanded="false" aria-haspopup="true" onclick="resetAllForms()">
                <span style="color:white;"><?php _e('Add Property', 'tainacan'); ?> <span style="color:white;" class="caret"></span></span>
            </button>

            <?php /*
            <div class="alert alert-info" style="float: left; margin-left: 20px; padding: 13px 20px 13px 20px; font-size: 12px;">
                <i> * Arraste um metadado para o lado esquerdo para utiliza-lo como filtro </i>
            </div>
            */ ?>

            <ul class="dropdown-menu add-property-dropdown">
                <?php foreach( $view_helper->get_metadata_types() as $type => $label):  ?>
                    <li>
                       <!--a  data-toggle="modal" data-target="#meta-<?php echo $type ?>"-->
                       <a onclick="$('#meta-<?php echo $type ?>').modal('show');clear_form('<?php echo $type ?>');" >
                            <img
                                <?php if($type=='metadata_compound'): echo 'height="15" width="15"'; endif;?>
                                src="<?php $view_helper->get_metadata_icon($type); ?>"
                                alt="<?php echo $type ?>" title="<?php echo $type ?>">
                            <?php echo $label ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="col-md-2 right back-to-collection" style="padding: 0 2% 0 0;">
                <button onclick="backToMainPage();" id="btn_back_collection" class="btn btn-default pull-right white"><?php _e('Back to collection','tainacan') ?></button>
            </div>
        </div>
        <?php $default_tab = get_post_meta($collection_id, 'socialdb_collection_default_tab', true) ?>
        <?php $selected_menu_style_id = get_post_meta( $collection_id, 'socialdb_collection_facet_' . $f_id . '_menu_style', true); ?>
        <!-- Abas para a Listagem dos metadados -->
        <div id="tab-content-metadata" class="tab-content" style="background: white">
            <div id="tab_default" class="ui-widget ui-helper-clearfix col-md-12 tab-pane fade in active" style="background: white">
                <ul id="metadata-container-default" class="gallery ui-helper-reset ui-helper-clearfix connectedSortable metadata-container">
                    <?php
                    foreach($view_helper->get_default_metadata() as $meta_id => $metadata):
                        $title = __($metadata, 'tainacan'); ?>
                        <li id='<?php echo $meta_id ?>' data-widget='tree' class='ui-widget-content ui-corner-tr fixed-meta'>
                            <label class='title-pipe'> <?php echo $title ?></label>
                            <div class='action-icons default-metadata'>
                                <a onclick='edit_filter(this)' class='<?php echo $meta_id ?>' data-title='<?php echo $title ?>'>
                                    <span class='glyphicon glyphicon-edit'> </span>
                                </a>
                                <span class='glyphicon glyphicon-trash no-edit'> </span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div id="loader_metadados_page" style="display: none;font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 11px; line-height: normal; font-family: Arial;">
                    <center>
                        <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                        <h4><?php _e('Loading metadata...', 'tainacan') ?></h4>
                   </center>
                </div>
            </div>
        </div>
        <?php include_once "metadata_forms.php"; ?>

        <input type="hidden" id="collection_list_ranking_id" name="collection_id" value="">
    </div>

</div>
