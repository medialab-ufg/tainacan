<?php
include_once(dirname(__FILE__) . '/../../helpers/view_helper.php');
include_once ( dirname(__FILE__) . '/../../views/theme_options/licenses/js/edit_js.php');
?>
<div class="col-md-12">
    <div class="col-md-12 config_default_style">
        <?php ViewHelper::render_config_title( __("Repository Licenses", 'tainacan') ); ?>
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#list_licenses" aria-controls="property_data_tab" role="tab" data-toggle="tab"><?php _e('Current Licenses', 'tainacan') ?></a></li>
            <li role="presentation"><a href="#new-license" class='edit-license' aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Add license', 'tainacan') ?></a></li>
        </ul>

        <div class="tab-content" style="padding: 10px;">
            <div id="list_licenses" class="tab-pane fade in active">
                <table class="table table-bordered" id="licenses_display">
                    <thead>
                    <tr>
                        <th> <?php _t('Title',1); ?> </th>
                        <th> <?php _t('Actions',1); ?> </th>
                    </tr>
                    </thead>

                    <tbody id="list_licenses_content"> </tbody>
                </table>
            </div>
            <div id="new-license" class="new-license tab-pane fade">
                <form name="formAddLicense" id="formAddLicense" method="POST">
                    <input type="hidden" name="operation" id="addLicenseOperation" value="add_repository_license" />
                    <input type="hidden" name="editLicenseId" id="editLicenseId" value="" />

                    <label for="add_license_name"><?php _e('License Title','tainacan'); ?></label>
                    <input type="text"  name="add_license_name" id="add_license_name" placeholder="<?php _e('Type here','tainacan'); ?>" class="form-control" required/><br/>

                    <label for="add_license_url"><?php _e('License URL','tainacan'); ?></label>
                    <input type="text" name="add_license_url" id="add_license_url" class="form-control" placeholder="<?php _e('Type here','tainacan'); ?>"><br/>

                    <label for="add_license_description"><?php _e('License Description','tainacan'); ?></label>
                    <textarea rows="10" name="add_license_description" id="add_license_description"
                              placeholder="<?php _e('Describe briefly','tainacan'); ?>" class="form-control"></textarea><br/>
                    <br>

                    <input type="submit" id="addLicenseBtn" name="addLicenseBtn" class="btn btn-primary pull-right" value="<?php _e('Save','tainacan'); ?>"  />
                </form>
            </div>
        </div>
    </div>
</div>

