<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/list_js.php');
?>
<?php if(isset($hide_wizard)&&$hide_wizard=='show'): ?>
<div class="fuelux">
    <div id="MyWizard" class="fuelux wizard">

        <ul class="steps fuelux step-content">
            <a onclick="showCollectionConfiguration('<?php echo get_template_directory_uri() ?>');"><li ><span class="fuelux badge">1</span><?php echo __("Configuration",'tainacan') ?><span class="chevron"></span></li></a>
            <a onclick="showPropertiesConfiguration('<?php echo get_template_directory_uri() ?>');"><li class="active" ><span class="badge badge-info">2</span><?php echo __("Metadata",'tainacan') ?><span class="fuelux chevron"></span></li></a>
            <a onclick="showRankingConfiguration('<?php echo get_template_directory_uri() ?>');"><li><span class="fuelux badge">3</span><?= __("Rankings",'tainacan') ?><span class="fuelux chevron"></span></li></a>
            <a onclick="showSearchConfiguration('<?php echo get_template_directory_uri() ?>');"><li><span class="fuelux badge">4</span><?= __("Searching",'tainacan') ?><span class="fuelux chevron"></span></li></a>
            <a onclick="showDesignConfiguration('<?php echo get_template_directory_uri() ?>');"><li><span class="fuelux badge">5</span><?= __("Design",'tainacan') ?><span class="fuelux chevron"></span></li></a>
        </ul>
        <div class="fuelux actions">
            <a onclick="showCollectionConfiguration('<?php echo get_template_directory_uri() ?>');" href="#" class="btn btn-mini btn-prev"> <span class="glyphicon glyphicon-chevron-left"></span></i><?php echo __("Previous",'tainacan') ?></a>
            <a onclick="showRankingConfiguration('<?php echo get_template_directory_uri() ?>');" href="#" class="btn btn-mini btn-next" data-last="Finish"><?php echo __("Next",'tainacan') ?><span class="glyphicon glyphicon-chevron-right"></span></i></a>
        </div>
    </div>
</div>
<?php endif; ?>
<div id="categories_title" class="row">
    <div class="col-md-1">
        <?php if ($is_root): ?>
            <!--br -->
        <?php endif; ?>
    </div>
    <div class="col-md-10">
        <?php if ($is_root): ?>
            <h3> <?php _e('Properties','tainacan') ?>
                <button onclick="backToMainPage();" id="btn_back_collection" class="btn btn-default pull-right"><?php _e('Back to collection','tainacan') ?></button>
            </h3>
            <hr>
        <?php else: ?>
            <h3> <?php _e('Properties','tainacan') ?><?php _e(' of ','tainacan') ?> <?php echo $category->name; ?>
                <button onclick="backToMainPage();" id="btn_back_collection" class="btn btn-default pull-right"><?php _e('Back to collection','tainacan') ?></button>
                <button style="display: none;"  id="btn_back_collection_hide_modal"  class="btn btn-default pull-right"  data-dismiss="modal" aria-label="Close"><?php _e('Close','tainacan') ?></button>
             <hr>
        <?php endif; ?>

        <div id="alert_success_properties" class="alert alert-success" style="display: none;">
            <button type="button" class="close" onclick="hide_alert('alert_success_properties');"><span aria-hidden="true">&times;</span></button>
            <?php _e('Operation was successful.','tainacan') ?>
        </div>
        <div id="alert_error_properties" class="alert alert-danger" style="display: none;">
            <button type="button" class="close" onclick="hide_alert('alert_error_properties');"><span aria-hidden="true">&times;</span></button>
             <span id="default_message_error">
                <?php _e('Error! Operation was unsuccessful.', 'tainacan') ?>
            </span>&nbsp;
            <span id="message_category"></span>
        </div>
    </div>
</div>
<div class="row" id="properties_create_opt">
    <div class="col-md-1">
        &nbsp;
    </div>
    <div class="col-md-10">
        <form method="POST">
            <div class="form-group">
                <label for="property_data_required"><?php _e('Using metadata schema','tainacan'); ?>:</label><br>
                <input type="radio" name="property_data_required" id="property_data_required_true" checked="checked"  value="true">&nbsp;<?php _e('Use the default schema repository (Dublin Core Simplified)','tainacan'); ?><br>
                <input type="radio" name="property_data_required" id="property_data_required_false" value="false" disabled="disabled">&nbsp;<?php _e('Request permission to use another metadata scheme','tainacan'); ?>
            </div>
            <button type="button" onclick="nextStep()" id="save_and_next" name="save_and_next" class="btn btn-primary" style="float: right;" value="next"><?php _e('Next Step','tainacan'); ?></button>
        </form>
        <div class="form-group">
            <a href="#properties_tabs" id="show_metadata_link" onclick="showMetadataSchema();"><?php _e('Extend metadata schema','tainacan'); ?></a>
            <a href="#properties_tabs" id="hide_metadata_link" onclick="hideMetadataSchema();" style="display: none;"><?php _e('Hide metadata schema','tainacan'); ?></a>
        </div>
    </div>
</div>
<input type="hidden" name="property_category_id" id="property_category_id" value="<?php echo $category->term_id; ?>"/>
<div class="categories_menu" class="row" style="z-index:9999;"  id="properties_tabs">
    <div class="col-md-1">

    </div>
    <div style="margin-bottom: 20px;" class="col-md-10">
        <div role="tabpanel">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a id="click_property_data_tab" href="#property_data_tab" aria-controls="property_data_tab" role="tab" data-toggle="tab"><?php _e('Property data','tainacan') ?></a></li>
                <li role="presentation"><a id="click_property_object_tab" href="#property_object_tab" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Property object','tainacan') ?></a></li>
                <li role="presentation"><a id="click_property_term_tab" href="#property_term_tab" aria-controls="property_term_tab" role="tab" data-toggle="tab"><?php _e('Property term','tainacan') ?></a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="property_data_tab">
                    <div id="list_properties_data" style="display: none;">
                        <table  class="table table-bordered" style="background-color: #d9edf7;">
                            <th><?php _e('Property data name','tainacan'); ?></th>
                            <th><?php _e('Property data type','tainacan'); ?></th>
                            <th><?php _e('Edit','tainacan'); ?></th>
                            <th><?php _e('Delete','tainacan'); ?></th>
                            <tbody id="table_property_data">
                            </tbody>
                        </table>
                    </div>
                    <div id="loader_data">
                                 <center>
                                    <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                                    <h3><?php _e('Please wait...', 'tainacan') ?></h3>
                                </center>
                    </div>
                    <div id="no_properties_data" style="display: none;">
                        <div id="alert_properties_data" class="alert alert-info">
                            <center><?php _e('No Properties added','tainacan'); ?></center>
                        </div>
                    </div>
                    <h4 id="property_data_title">
                        <!--a onclick="toggleSlide('submit_form_property_data','list_properties_data');" style="cursor: pointer;">
                            <?php _e('Add new property','tainacan'); ?>
                           <span class="glyphicon glyphicon-triangle-bottom"></span>
                        </a-->
                         <?php _e('Add new property','tainacan'); ?>
                    </h4>
                    <form   id="submit_form_property_data">
                        <input type="hidden" name="property_category_id"  value="<?php echo $category->term_id; ?>">
                        <div class="create_form-group">
                            <label for="property_data_name"><?php _e('Property data name','tainacan'); ?></label>
                            <input type="text"
                                   class="form-control"
                                   id="property_data_name"
                                   name="property_data_name"
                                   required="required"
                                   placeholder="<?php _e('Property Data name','tainacan'); ?>">
                            <div id="autocomplete_property_data"></div>
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="property_data_widget"><?php _e('Property data widget','tainacan'); ?></label>
                            <select onchange="hide_fields(this)" class="form-control" id="property_data_widget" name="property_data_widget">
                                <?php do_action('form_help_property_data_insert_types') ?>
                                <option <?php do_action('form_help_property_data_type_text') ?> value="text"><?php _e('Text','tainacan'); ?></option>
                                <option <?php do_action('form_help_property_data_type_textarea') ?> value="textarea"><?php _e('Textarea','tainacan'); ?></option>
                                <option <?php do_action('form_help_property_data_type_date') ?> value="date"><?php _e('Date','tainacan'); ?></option>
                                <option <?php do_action('form_help_property_data_type_numeric') ?> value="numeric"><?php _e('Numeric','tainacan'); ?></option>
                                <option <?php do_action('form_help_property_data_type_autoincrement') ?> value="autoincrement"><?php _e('Auto-Increment','tainacan'); ?></option>

                            </select>
                        </div>
                        <div id="default_field" class="create_form-group" <?php do_action('form_default_value_property_data') ?>>
                            <label for="socialdb_property_default_value"><?php _e('Property data default value','tainacan'); ?></label>
                            <input type="text" class="form-control" id="socialdb_property_data_default_value" name="socialdb_property_default_value" placeholder="<?php _e('Property Data Default Value','tainacan'); ?>"><br>
                        </div>
                        <div class="create_form-group" <?php do_action('form_help_property_data') ?>>
                            <label for="socialdb_property_help"><?php _e('Property data text helper','tainacan'); ?></label>
                            <textarea class="form-control" id="socialdb_property_data_help" name="socialdb_property_data_help"></textarea>
                        </div>
                        <br>
                        <div id="required_field" class="form-group"  <?php do_action('form_required_property_data') ?>>
                            <label for="property_data_required"><?php _e('Property data required','tainacan'); ?></label>
                            <input type="radio" name="property_data_required" id="property_data_required_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
                            <input type="radio" name="property_data_required" id="property_data_required_false" checked="checked" value="false">&nbsp;<?php _e('No','tainacan'); ?>
                        </div>
                        <?php do_action('form_modify_property_data') ?>
                        <input type="hidden" id="property_data_collection_id" name="collection_id" value="">
                        <input type="hidden" id="property_data_id" name="property_data_id" value="">
                        <input type="hidden" id="operation_property_data" name="operation" value="add_property_data">
                        <button type="submit" id="submit_property_data" class="btn btn-primary"><?php _e('Submit','tainacan'); ?></button>
                        <button type="button" onclick="clear_buttons()" class="btn btn-default" id="clear_categories"><?php _e('Clear','tainacan'); ?></button>
                        <br>
                    </form>
                </div>
                <!-- FORMULARIO PROPRIEDADE DE OBJETO -->
                <div role="tabpanel" class="tab-pane" id="property_object_tab">
                    <div id="list_properties_object">
                        <table  class="table table-bordered" style="background-color: #d9edf7;">
                            <th><?php _e('Property object name','tainacan'); ?></th>
                            <th><?php _e('Property object type','tainacan'); ?></th>
                            <th><?php _e('Edit','tainacan'); ?></th>
                            <th><?php _e('Delete','tainacan'); ?></th>
                            <tbody id="table_property_object" style="background-color: #d9edf7;">
                            </tbody>
                        </table>
                    </div>
                     <div id="loader_object">
                                 <center>
                                    <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                                    <h3><?php _e('Please wait...', 'tainacan') ?></h3>
                                </center>
                    </div>
                    <div id="no_properties_object" style="display: none;">
                        <div id="alert_properties_object" class="alert alert-info">
                            <center><?php _e('No Properties added','tainacan'); ?></center>
                        </div>
                    </div>
                    <h4 id="property_object_title">
                        <!--a onclick="toggleSlide('submit_form_property_object','list_properties_object');" style="cursor: pointer;">
                           <?php _e('Add new property','tainacan'); ?>
                           <span class="glyphicon glyphicon-triangle-bottom"></span>
                        </a-->
                         <?php _e('Add new property','tainacan'); ?>
                    </h4>
                    <form  id="submit_form_property_object">
                        <input type="hidden" name="property_category_id"  value="<?php echo $category->term_id; ?>">
                        <div class="create_form-group">
                            <label for="property_object_name"><?php _e('Property object name','tainacan'); ?></label>
                            <input type="text" class="form-control" id="property_object_name" name="property_object_name" required="required" placeholder="<?php _e('Property Object name','tainacan'); ?>">
                             <div id="autocomplete_property_object"></div>
                        </div>
                        <div class="form-group">
                            <?php if (isset($is_root) && $is_root): ?>
                                <label for="property_object_category_id"><?php _e('Property object relationship','tainacan'); ?></label>
                                <select class="form-control" id="property_object_category_id" name="property_object_category_id">
                                    <?php foreach ($property_object as $object) { ?>
                                        <option value="<?php echo $object['category_id'] ?>"><?php echo $object['collection_name'] ?></option>
                                    <?php } ?>
                                </select>
                            <?php else: ?>
                                <label for="property_object_category_id"><?php _e('Property object relationship','tainacan'); ?></label>
                                <?php if (!$is_root): ?>
                                    <div id="property_category_dynatree" style="height: 300px;overflow-y: scroll;" >
                                    </div>
                                <?php endif; ?>
                                <!--input disabled="disabled" type="text" class="form-control" id="property_object_category_name" value="" placeholder="<?php _e('Click on the category in the tree','tainacan'); ?>" name="property_object_category_name" -->
                                <input required="required" type="hidden"  id="property_object_category_id"  name="property_object_category_id" value="<?php //echo $category->term_id; ?>" >
                            <?php endif; ?>
                        </div>
                        <!--div class="form-group">
                            <label for="property_object_required"><?php _e('Property object facet','tainacan'); ?></label>
                            <input type="radio" name="property_object_facet" id="property_object_facet_true" value="true">&nbsp;<?php _e('Yes'); ?>
                            <input type="radio" name="property_object_facet" id="property_object_facet_false" checked="checked" value="false">&nbsp;<?php _e('No'); ?>
                        </div-->
                        <div class="form-group" <?php do_action('form_required_property_object') ?>>
                            <label for="property_object_required"><?php _e('Property object required','tainacan'); ?></label>
                            <input type="radio" name="property_object_required" id="property_object_required_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
                            <input type="radio" name="property_object_required" id="property_object_required_false" checked="checked" value="false">&nbsp;<?php _e('No','tainacan'); ?>
                        </div>
                        <div class="form-group" <?php do_action('form_is_reverse_property_object') ?>>
                            <label for="property_object_is_reverse"><?php _e('Property object reverse','tainacan'); ?></label>
                            <input type="radio" name="property_object_is_reverse" id="property_object_is_reverse_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
                            <input type="radio" name="property_object_is_reverse" id="property_object_is_reverse_false" checked="checked" value="false">&nbsp;<?php _e('No','tainacan'); ?>
                        </div>
                        <div id="show_reverse_properties" class="form-group" style="display: none;">
                            <label for="property_object_reverse"><?php _e('Select the reverse property','tainacan'); ?></label>
                            <select class="form-control" id="property_object_reverse" name="property_object_reverse">
                            </select>
                        </div>
                        <br>
                        <?php do_action('form_modify_property_object') ?>
                        <input type="hidden" id="property_object_collection_id" name="collection_id" value="">
                        <input type="hidden" id="property_object_id" name="property_object_id" value="">
                        <input type="hidden" id="operation_property_object" name="operation" value="add_property_object">
                        <button type="submit" id="submit" class="btn btn-primary"><?php _e('Submit','tainacan'); ?></button>
                        <button type="button" onclick="clear_buttons()" class="btn btn-default" id="clear_categories"><?php _e('New','tainacan'); ?></button>
                        <br>
                    </form>
                </div>
                <!-- FORMULARIO PROPRIEDADE DE TERM -->
                <div role="tabpanel" class="tab-pane" id="property_term_tab">
                    <div id="list_properties_term">
                        <table  class="table table-bordered" style="background-color: #d9edf7;">
                            <th><?php _e('Property term name','tainacan'); ?></th>
                            <th><?php _e('Property term type','tainacan'); ?></th>
                            <th><?php _e('Edit','tainacan'); ?></th>
                            <th><?php _e('Delete','tainacan'); ?></th>
                            <tbody id="table_property_term" style="background-color: #d9edf7;" >
                            </tbody>
                        </table>
                    </div>
                     <div id="loader_term">
                                 <center>
                                    <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                                    <h3><?php _e('Please wait...', 'tainacan') ?></h3>
                                </center>
                    </div>
                    <div id="no_properties_term" style="display: none;">
                        <div class="alert alert-info">
                            <center><?php _e('No Properties added','tainacan'); ?></center>
                        </div>
                    </div>
                    <h3 id="property_term_title"><?php _e('Add new property','tainacan'); ?></h3>
                    <form  id="submit_form_property_term">
                        <input type="hidden" name="property_category_id"  value="<?php echo $category->term_id; ?>">
                        <div class="create_form-group">
                            <label for="property_term_name"><?php _e('Property term name','tainacan'); ?></label>
                            <input type="text" class="form-control" id="property_term_name" name="property_term_name" required="required" placeholder="<?php _e('Property Term name','tainacan'); ?>">
                            <div id="autocomplete_property_term"></div>
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="property_term_required"><?php _e('Property term cardinality','tainacan'); ?></label><br>
                            <input type="radio" name="socialdb_property_term_cardinality" id="socialdb_property_term_cardinality_1" checked="checked"   value="1">&nbsp;<?php _e('Associate with one category','tainacan') ?>
                            <input type="radio" name="socialdb_property_term_cardinality" id="socialdb_property_term_cardinality_n" value="n">&nbsp;<?php _e('Associate with multiple categories','tainacan') ?>
                        </div>
                        <div class="form-group">
                            <label for="socialdb_property_term_widget"><?php _e('Property Term Widget','tainacan'); ?></label>
                            <select class="form-control" id="socialdb_property_term_widget" name="socialdb_property_term_widget">
                            </select>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="socialdb_property_term_root_category"><?php _e('Property Term Root Category','tainacan'); ?></label>

                                <div style='height: 150px;overflow: scroll;' id="terms_dynatree" >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="selected_category"><?php _e('Selected category','tainacan'); ?></label><br>
                                <select required="required" size='2' id="socialdb_property_term_root" class="form-control" name='socialdb_property_term_root'></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="property_term_required"><?php _e('Property object required','tainacan'); ?></label><br>
                            <input type="radio" name="property_term_required" id="property_term_required_true" value="true">&nbsp;<?php _e('Yes','tainacan'); ?>
                            <input type="radio" name="property_term_required" id="property_term_required_false" checked="checked" value="false">&nbsp;<?php _e('No','tainacan'); ?>
                        </div>
                         <!--div class="create_form-group">
=======
                        <div class="create_form-group">
>>>>>>> .r626
                            <label for="socialdb_property_default_value"><?php _e('Property term default value'); ?></label>
                            <input type="text" class="form-control" id="socialdb_property_default_value" name="socialdb_property_default_value" placeholder="<?php _e('Property Term Default Value'); ?>">
                        </div-->
                        <br>
                        <div class="create_form-group">
                            <label for="socialdb_property_help"><?php _e('Property term text helper','tainacan'); ?></label>
                            <textarea class="form-control" id="socialdb_property_help" name="socialdb_property_help"></textarea>
                        </div>
                        <br>
                        <input type="hidden" id="property_term_collection_id" name="collection_id" value="">
                        <input type="hidden" id="property_term_id" name="property_term_id" value="">
                        <input type="hidden" id="operation_property_term" name="operation" value="add_property_term">
                        <button type="submit" id="submit" class="btn btn-primary"><?php _e('Submit','tainacan'); ?></button>
                        <button type="reset" onclick="clear_buttons()" class="btn btn-default" id="clear_categories"><?php _e('New','tainacan'); ?></button>
                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_remove_property" tabindex="-1" role="dialog" aria-labelledby="modal_remove_property_data" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="submit_delete_property">
                <input type="hidden" id="property_delete_collection_id" name="collection_id" value="">
                <input type="hidden" id="property_delete_id" name="property_delete_id" value="">
                <input type="hidden" id="operation" name="operation" value="delete">
                <input type="hidden" id="type_metadata_form" name="type" value="">
                <input type="hidden" name="property_category_id"  value="<?php echo $category->term_id; ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php echo __('Removing property','tainacan'); ?></h4>
                </div>
                <div class="modal-body">
                    <?php echo __('Confirm the exclusion of ','tainacan'); ?><span id="deleted_property_name"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close','tainacan'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo __('Salve','tainacan'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
