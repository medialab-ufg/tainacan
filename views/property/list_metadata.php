<?php
include_once('../../helpers/view_helper.php');
include_once('js/tabs_js.php');
include_once('js/compounds_js.php');
include_once ('js/list_metadata_js.php');

$view_helper = new ViewHelper();
$meta = get_post_meta($collection_id, 'socialdb_collection_fixed_properties_visibility', true);
$tabs = unserialize(get_post_meta($collection_id, 'socialdb_collection_update_tab_organization',true));
$array_visibility = ($meta&&$meta!=='') ? $meta : '';
if(!session_id()) {
        session_start();
}

delete_post_meta($collection_id,'properties-cached');
delete_post_meta($collection_id,'rankings-cached');
unset($_SESSION['tainacan-categories']);
?>

<?php $view_helper->render_header_config_steps('metadata') ?>

<input type="hidden" name="tabs_properties" id="tabs_properties"
       value='<?php echo ($tabs&&is_array($tabs)) ? json_encode($tabs) :  ''; ?>'/>
<input type="hidden" name="visibility_collection_properties" id="visibility_collection_properties" value='<?php echo $array_visibility; ?>'/>
<input type="hidden" name="property_category_id" id="property_category_id" value="<?php echo $category->term_id; ?>"/>
<div class="categories_menu col-md-12 no-padding"  id="properties_tabs">

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

    <div class="col-md-9 ui-widget-content metadata-actions" style="padding-right: 0;">
        
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
            <button class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown" aria-expanded="false" aria-haspopup="true" onclick="resetAllForms()">
                <span style="color:white;"><?php _e('Add Property', 'tainacan'); ?> <span style="color:white;" class="caret"></span></span> 
            </button>

            <ul class="dropdown-menu add-property-dropdown">
                <?php foreach( $view_helper->get_metadata_types() as $type => $label):  ?>
                    <li>
                       <!--a  data-toggle="modal" data-target="#meta-<?php echo $type ?>"-->
                       <a onclick="$('#meta-<?php echo $type ?>').modal('show');clear_form('<?php echo $type ?>');$('#meta-<?php echo $type ?> input[type=text]').first().focus()" > 
                           <?php
                           if($type == 'user')
                           {
                               ?>
                               <span class="glyphicon glyphicon-user"></span>
                               <?php
                           }else{
                           ?>
                           <img
                               <?php if($type=='metadata_compound'): echo 'height="15" width="15"'; endif;?>
                               src="<?php $view_helper->get_metadata_icon($type); ?>" 
                               alt="<?php echo $type ?>" title="<?php echo $type ?>">

                           <?php }?>
                            <?php echo $label ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="col-md-2 right back-to-collection" style="padding: 0 2% 0 0;"> 
                <a class="btn btn-default pull-right white" href="<?php echo get_the_permalink($collection_id) ?>">
                <!--button onclick="backRoute($('#slug_collection').val());" id="btn_back_collection" class="btn btn-default pull-right white"-->
                    <?php _e('Back to collection','tainacan') ?>
                </a>
            </div>
        </div>
        <?php $default_tab = get_post_meta($collection_id, 'socialdb_collection_default_tab', true) ?>
        <?php $selected_menu_style_id = get_post_meta( $collection_id, 'socialdb_collection_facet_' . $f_id . '_menu_style', true); ?>
        <!-- Abas para a Listagem dos metadados -->
        <div style="background: white;">
            <ul class="nav nav-tabs" style="background: white;margin-left: 15px;margin-right: 15px;">
                <li  role="presentation" class="active">
                    <a id="click-tab-default" href="#tab_default" aria-controls="tab_default" role="tab" data-toggle="tab">
                        <span ondblclick="alter_tab_title('default')" id="default-tab-title"><?php echo (!$default_tab) ? _e('Default', 'tainacan') : $default_tab ?></span>
                        <input id="default-tab-title-input" 
                               class="style-input"
                               onblur="on_blur_input_title('default')"
                               onkeyup="on_key_input_title('default',event)"
                               style="display: none;" 
                               type="text" 
                               value="<?php echo (!$default_tab) ? _e('Default', 'tainacan') : $default_tab ?>">
                    </a>
                </li>
                <li id="plus_tab_button" role="presentation">
                    <a style="cursor: pointer;" onclick="add_tab(this)"  role="tab" data-toggle="tab">
                        <span class="glyphicon glyphicon-plus"></span>
                    </a>
                </li>
         </ul>  
        </div>    
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
        <button id="next_property_wizard" onclick="showLayout($('#src').val());" class="btn btn-success pull-right btn-lg" style="display:none;margin-top: 15px;margin-right: 15px;">
            <?php _e('Save & Next', 'tainacan'); ?>
        </button>  
        <?php include_once "metadata_forms.php"; ?>

        <input type="hidden" id="collection_list_ranking_id" name="collection_id" value="">
    </div>

</div> <!-- #properties-tabs -->