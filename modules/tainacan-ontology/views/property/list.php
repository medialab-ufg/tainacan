<?php
include_once ('js/list_js.php');
?>

<div id="categories_title" class="row">
    <div class="col-md-1">
    </div>
    <div class="col-md-10">
            <h3><?php _e('Edit','tainacan') ?> <?php echo $property->name; ?>
                <button onclick="backToMainPage();" id="btn_back_collection" class="btn btn-default pull-right"><?php _e('Back to collection','tainacan') ?></button>
             <hr>
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
<input type="hidden" name="property_edit_id" id="property_edit_id" value="<?php echo $property->term_id; ?>"/>
<input type="hidden" name="property_category_id" id="property_category_id" value="<?php echo $category->term_id; ?>"/>
<div class="categories_menu" class="row" style="z-index:9999;"  id="properties_tabs">
    <div class="col-md-1">

    </div>
    <div style="margin-bottom: 20px;" class="col-md-10">
        <div <?php echo ($type!='socialdb_property_data')?'style="display:none;"':'' ?>  id="property_data_tab">
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
                        <!-- Domain -->
                        <div class="form-group">
                            <label for="property_category_dynatree_data_domain"><?php _e('Domain','tainacan'); ?></label>
                            <div id="property_category_dynatree_data_domain" style="height: 300px;overflow-y: scroll;" >
                            </div>
                              <input required="required" type="hidden"  id="property_data_domain_category_id"  name="socialdb_event_property_used_by_categories" value="<?php echo $category->term_id; ?>" >
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
                        <input type="hidden" id="operation_property_data" name="operation" value="update_property_data">
                        <button type="submit" id="submit_property_data" class="btn btn-lg btn-primary pull-right"><?php _e('Save','tainacan'); ?></button>
                        <br>
                    </form>
        </div>
        <!-- FORMULARIO PROPRIEDADE DE OBJETO -->
        <div <?php echo ($type!='socialdb_property_object')?'style="display:none;"':'' ?> id="property_object_tab">
                    <form  id="submit_form_property_object">
                        <input type="hidden" name="property_category_id"  value="<?php echo $category->term_id; ?>">
                        <div class="create_form-group">
                            <label for="property_object_name"><?php _e('Property object name','tainacan'); ?></label>
                            <input type="text" class="form-control" id="property_object_name" name="property_object_name" required="required" placeholder="<?php _e('Property Object name','tainacan'); ?>">
                             <div id="autocomplete_property_object"></div>
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="property_category_dynatree_object_domain"><?php _e('Domain','tainacan'); ?></label>
                            <?php if (!$is_root): ?>
                                <div id="property_category_dynatree_object_domain" style="height: 300px;overflow-y: scroll;" >
                                </div>
                            <?php endif; ?>
                              <input required="required" type="hidden"  id="property_object_domain_category_id"  name="socialdb_event_property_used_by_categories" value="<?php echo $category->term_id; ?>" >
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
                        <input type="hidden" id="operation_property_object" name="operation" value="update_property_object">
                        <button type="submit" id="submit" class="btn btn-lg btn-primary pull-right"><?php _e('Save','tainacan'); ?></button>
                       <br>
                    </form>
        </div>
        <!-- FORMULARIO PROPRIEDADE DE TERM -->
        <div <?php echo ($type!='socialdb_property_term')?'style="display:none;"':'' ?> id="property_term_tab">
                    <form  id="submit_form_property_term">
                        <input type="hidden" name="property_category_id"  value="<?php echo $category->term_id; ?>">
                        <div class="create_form-group">
                            <label for="property_term_name"><?php _e('Property term name','tainacan'); ?></label>
                            <input type="text" class="form-control" id="property_term_name" name="property_term_name" required="required" placeholder="<?php _e('Property Term name','tainacan'); ?>">
                            <div id="autocomplete_property_term"></div>
                        </div>
                        <br>
                        <!-- Domain -->
                        <div class="form-group">
                            <label for="property_category_dynatree_term_domain"><?php _e('Domain','tainacan'); ?></label>
                            <div id="property_category_dynatree_term_domain" style="height: 300px;overflow-y: scroll;" >
                            </div>
                              <input required="required" type="hidden"  id="property_term_domain_category_id"  name="socialdb_event_property_used_by_categories" value="<?php echo $category->term_id; ?>" >
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
                        <br>
                        <div class="create_form-group">
                            <label for="socialdb_property_help"><?php _e('Property term text helper','tainacan'); ?></label>
                            <textarea class="form-control" id="socialdb_property_help" name="socialdb_property_help"></textarea>
                        </div>
                        <br>
                        <input type="hidden" id="property_term_collection_id" name="collection_id" value="">
                        <input type="hidden" id="property_term_id" name="property_term_id" value="">
                        <input type="hidden" id="operation_property_term" name="operation" value="update_property_term">
                        <button type="submit" id="submit" class="btn btn-lg btn-primary pull-right"><?php _e('Save','tainacan'); ?></button>
                       <br>
                    </form>
                </div>
            </div>
    </div>