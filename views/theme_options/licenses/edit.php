<?php
include_once('../../../../../wp-config.php');
include_once('../../../../../wp-load.php');
include_once('../../../../../wp-includes/wp-db.php');
include_once('js/edit_js.php');
?>

<div class="row">
    <?php include_once dirname(__DIR__)."../common/redirect_button.php" ?>
    <div class="col-md-9">
        <div class="row">
            <h3><?php _e("Repository Licenses",'tainacan'); ?></h3>
            <div id="list_licenses">
                <table  class="table table-bordered" style="background-color: #d9edf7;">
                    <th><?php _e('Title','tainacan'); ?></th>
                    <th><?php _e('Pattern','tainacan'); ?></th>
                    <th><?php _e('Edit','tainacan'); ?></th>
                    <th><?php _e('Delete','tainacan'); ?></th>
                    <tbody id="list_licenses_content" >
                    </tbody>
                </table>
            </div>

            <form name="formAddLicense" id="formAddLicense" method="POST">
                <input type="hidden" name="operation" id="addLicenseOperation" value="add_repository_license" />
                <input type="hidden" name="editLicenseId" id="editLicenseId" value="" />
                
                <label for="add_license_name"><?php _e('Type a License Title','tainacan'); ?></label>
                <input type="text"  name="add_license_name" id="add_license_name" style="width: 33.333%" placeholder="<?php _e('Type here','tainacan'); ?>" class="form-control" required/></br>

                <label for="add_license_url"><?php _e('Type a License URL','tainacan'); ?></label>
                <input type="text" name="add_license_url" id="add_license_url" style="width: 33.333%"  class="form-control" placeholder="<?php _e('Type here','tainacan'); ?>"></br>

                <label for="add_license_description"><?php _e('Type a License Description','tainacan'); ?></label>
                <textarea rows="10" name="add_license_description" id="add_license_description" placeholder="<?php _e('Type here','tainacan'); ?>" class="form-control"></textarea></br>

                <!--div class="input-group">
                    <input type="text" name="add_license_url" id="add_license_url" style="width: 33.333%"  class="form-control" placeholder="<?php _e('Type here'); ?>">
                    <span class="input-group-btn">
                        <button class="btn btn-default" id="importLicense" name="importLicense" type="button"><?php _e('Import'); ?></button>
                    </span>
                </div--><!-- /input-group -->
                <br>

                <input type="submit" id="addLicenseBtn" name="addLicenseBtn" class="btn btn-default pull-left" value="<?php _e('Save','tainacan'); ?>"  />
            </form>
        </div>
    </div>
</div> 

