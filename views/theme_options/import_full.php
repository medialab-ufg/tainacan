<?php
include_once ('js/import_full_js.php');
include_once ('js/import_oaipmh_js.php');
include_once ('../../helpers/view_helper.php');
?>
<div class="col-md-12">
    
    <div id="export_settings" class="col-md-12 config_default_style">
        <?php ViewHelper::render_config_title(__("Import", 'tainacan')); ?>
        <div class="col-md-12 no-padding">
            <div role="tabpanel">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a id="click_oaipmhtab" href="#oaipmhtab-" aria-controls="oaipmhtab-" role="tab" data-toggle="tab"><?php _e('OAI-PMH','tainacan') ?></a></li>
                    <li role="presentation"><a id="click_zip" href="#zip" aria-controls="zip" role="tab" data-toggle="tab"><?php _e('AIP', 'tainacan') ?></a></li>
                    <li role="presentation"><a id="click_csv" href="#csv" aria-controls="csv" role="tab" data-toggle="tab"><?php _e('CSV Package', 'tainacan') ?></a></li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <!--  OAIPMH -->
                    <div role="tabpanel" class="tab-pane active" id="oaipmhtab-" >
                        
                        <div id="validate_url_container" >
                            <div id="list_oaipmh_dc">
                                <table  class="table table-bordered">
                                    <th><?php _e('Identifier','tainacan'); ?></th>
                                    <th><?php _e('Harvesting','tainacan'); ?></th>
                                    <th> </th>
                                    <tbody id="table_oaipmh_dc" >
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            <div class="form-group">
                                <label><?php _e('Base URL','tainacan'); ?></label>
                                <input type="text" id="url_base_oai" class="form-control" placeholder="<?php _e('Insert the OAI-PMH respository URL','tainacan'); ?>">
                            </div>
                            <div class="form-group">
                                <label><?php _e('Set (Optional)','tainacan'); ?></label>
                                <input type="text" id="sets_import_oaipmh" name="sets_import_oaipmh" class="form-control" placeholder="<?php _e('Type a valid set','tainacan'); ?>">
                            </div>
                            <input type="hidden" id="collection_import_id" name="collection_id" value="">
                            <input type="hidden" id="operation" name="operation" value="validate">
                            <button type="button" onclick="validate_url_repository()" id="submit_oaipmh" class="btn btn-primary tainacan-blue-btn-bg"><?php _e('Validate','tainacan'); ?></button>
                        </div>
                        <div id="loader_validacao" style="display:none">
                            <center>
                                <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                                <h3><?php _e('Validating Repository...','tainacan') ?></h3>
                            </center>
                        </div>
                        <div id="maping_container_repository">
                        </div>
                       
                        <div id="progress" style="display: none;">
                            <center>
                                <h3 id="title_import"><?php echo __("Please wait, this process may take several minutes",'tainacan'); ?></h3>
                                <progress id="progressbar" value="0" max="100"></progress><br>
                                <center>
                                    <h3><span id="progressstatus"></span></h3>
                                    <br>

                                </center>
                            </center>
                        </div>
                        <div id="cronometer"  style="display: none;" >
                            <center>
                                <h3><span id="hora">00h</span><span id="minuto">00m</span><span id="segundo">00s</span></h3>
                            </center>
                        </div>
                    </div>
                    <!-- Tab panes -->
                    <div role="tabpanel" class="tab-pane" id="zip">
                        <form id="form_export_zip" method="post" action="<?php echo get_template_directory_uri() ?>/controllers/import/zip_controller.php">
                            <div class="export-container">
                                <input type="hidden" id="operation_import_aip" name="operation" value="import_full_aip" />
                                <select disabled="disabled" class="form-control">
                                    <option selected="selected"><?php _e('Dspace Format', 'tainacan') ?></option>
                                </select>
                            </div>

                            <button type="submit" id="export_zip" class="btn btn-primary tainacan-blue-btn-bg"><?php _e('Import AIP', 'tainacan'); ?></button>
                        </form>
                    </div>
                    <!-- Tab panes -->
                    <div role="tabpanel" class="tab-pane" id="csv"><br>
                        <form id="formCsv" name="formCsv" enctype="multipart/form-data" method="post">
                            <div class="form-group">
                                <input type="file" accept=".zip" id="csv_pkg" name="csv_pkg" placeholder="<?php _e('Insert the CSV file', 'tainacan'); ?>">
                            </div>
                            <input type="hidden" id="operation_csv" name="operation" value="import_full_csv">
                            <button type="submit" id="submit_csv" class="btn btn-primary tainacan-blue-btn-bg"><?php _e('Import', 'tainacan'); ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>