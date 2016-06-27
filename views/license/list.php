<?php
include_once ('../../helpers/view_helper.php');
include_once('js/list_js.php');
?>
<div class="col-md-12">
    <div class="col-md-12 config_default_style" id="licenses_settings">
        <?php ViewHelper::render_config_title( __("Collection Licenses", 'tainacan') ); ?>
        <div id="list_licenses">
            <form name="formEnabledLicenses" id="formEnabledLicenses" method="POST">
                <table class="table table-bordered" id="licenses_display">
                    <thead>
                    <tr>
                        <th><?php _e('Title','tainacan'); ?></th>
                        <th><?php _e('Pattern','tainacan'); ?></th>
                        <th><?php _e('Enabled','tainacan'); ?></th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody id="list_licenses_content"></tbody>
                    
                </table>
            </form>
        </div>
        <div class="new-license">
            <h3> <?php _e('Add license', 'tainacan'); ?> </h3>
            <form name="formAddLicense" id="formAddLicense" method="POST">
                <input type="hidden" name="operation" id="addLicenseOperation" value="add_collection_license" />
                <input type="hidden" name="collection_id" id="collection_id_license" value="<?php echo $collection_id ?>" />
                <input type="hidden" name="editLicenseId" id="editLicenseId" value="" />

                <label for="add_license_name"><?php _e('License Title','tainacan'); ?></label>
                <input type="text"  name="add_license_name" id="add_license_name" style="width: 33.333%" placeholder="<?php _e('Type here','tainacan'); ?>" class="form-control" required/></br>

                <label for="add_license_url"><?php _e('License URL','tainacan'); ?></label>
                <input type="text" name="add_license_url" id="add_license_url" style="width: 33.333%"  class="form-control" placeholder="<?php _e('Type here','tainacan'); ?>"></br>

                <label for="add_license_description"><?php _e('License Description','tainacan'); ?></label>
            <textarea rows="10" name="add_license_description" id="add_license_description"
                      placeholder="<?php _e('Describe briefly','tainacan'); ?>" class="form-control"></textarea></br>

                <input type="submit" id="addLicenseBtn" name="addLicenseBtn" class="btn btn-default pull-right tainacan-blue-btn-bg" value="<?php _e('Save','tainacan'); ?>"  />
            </form>    
        </div>
        
    </div>
</div>