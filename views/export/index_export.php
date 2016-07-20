<?php
/**
 * Author: Eduardo
 */
include_once ('js/index_export_js.php');
include_once ('../../helpers/view_helper.php');
?>
<div class="col-md-12">
    <div id="export_settings" class="col-md-12 config_default_style">
        <?php ViewHelper::render_config_title( __("Export", 'tainacan') ); ?>
        <div class="col-md-12 no-padding">
            <div role="tabpanel">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a id="click_oaipmhtab" href="#oaipmhtab" aria-controls="oaipmhtab" role="tab" data-toggle="tab"><?php _e('OAI-PMH','tainacan') ?></a></li>
                    <li role="presentation"><a id="click_csvtab" href="#csvtab" aria-controls="csvtab" role="tab" data-toggle="tab"><?php _e('CSV','tainacan') ?></a></li>
                    <li role="presentation"><a id="click_zip" href="#zip" aria-controls="zip" role="tab" data-toggle="tab"><?php _e('Package','tainacan') ?></a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="oaipmhtab" >
                        <div id="export_oaipmh_dc_container">
                            <form id="form_default">
                                <div id="list_export_oaipmh_dc">
                                    <table class="table table-bordered">
                                        <th class="main"><?php _e('Identifier','tainacan'); ?></th>
                                        <th class="main"><?php _e('Active Mapping','tainacan'); ?></th>
                                        <th><?php // _e('Actions','tainacan'); ?></th>
                                        <tbody id="table_export_oaipmh_dc" >
                                        </tbody>
                                    </table>
                                </div>
                                <br>
                                <div id="oai_repository"></div>
                                <button type="submit" id="show_mapping_export_oaipmhdc" class="btn btn-primary pull-right tainacan-blue-btn-bg"><?php _e('Save active mapping','tainacan'); ?></button>
                                <button type="button" onclick="show_mapping_export()" id="show_mapping_export_oaipmhdc" class="btn btn-primary tainacan-blue-btn-bg"><?php _e('Create new OAIPMH DC','tainacan'); ?></button>
                            </form>
                        </div>
                        <div id="maping_container_export">
                        </div>
                    </div>
                    <!-- Tab panes -->
                    <div role="tabpanel" class="tab-pane" id="csvtab">
                        <form id="form_export_csv" method="post" action="<?php echo get_template_directory_uri() ?>/controllers/export/export_controller.php" onsubmit="return verify_delimiter();">
                            <div class="export-container">
                                <input type="hidden" id="collection_id_export_csv" name="collection_id" value="<?php echo $collection_id; ?>" />
                                <input type="hidden" id="operation_export_csv" name="operation" value="export_csv_file" />
                                <label for="socialdb_delimiter_csv"><?php _e('Set Delimiter','tainacan'); ?></label>
                                <input type="text" class="form-control" id="socialdb_delimiter_csv" name="socialdb_delimiter_csv" maxlength="1" required>
                            </div>
                            <button type="submit" id="export_csv" class="btn btn-primary tainacan-blue-btn-bg"><?php _e('Export CSV File','tainacan'); ?></button>
                            <!--button type="button" onclick="export_csv_file()" id="export_csv" class="btn btn-primary"><?php _e('Export CSV File','tainacan'); ?></button-->
                        </form>
                    </div>
                    <!-- Tab panes -->
                    <div role="tabpanel" class="tab-pane" id="zip">
                        <form id="form_export_zip" method="post" action="<?php echo get_template_directory_uri() ?>/controllers/export/zip_controller.php">
                            <div class="export-container">
                                <input type="hidden" id="collection_id_zip" name="collection_id" value="<?php echo $collection_id; ?>" />
                                <input type="hidden" id="operation_export_zip" name="operation" value="export_collection" />
                                <select disabled="disabled" class="form-control">
                                    <option selected="selected"><?php _e('Tainacan Format','tainacan') ?></option>
                                </select>
                            </div>

                            <button type="submit" id="export_zip" class="btn btn-primary tainacan-blue-btn-bg"><?php _e('Export package','tainacan'); ?></button>
                            <!--button type="button" onclick="export_csv_file()" id="export_csv" class="btn btn-primary"><?php _e('Export CSV File','tainacan'); ?></button-->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>