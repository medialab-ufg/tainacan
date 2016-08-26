<?php
include_once ('js/import_configuration_js.php');
include_once ('../../helpers/view_helper.php');
?>
<div class="col-md-12">
    <div id="import_settings" class="col-md-12 config_default_style">
		<?php ViewHelper::render_config_title( __("Import", 'tainacan') ); ?>
        <div class="col-md-12 no-padding">
            <div role="tabpanel">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a id="click_oaipmhtab" href="#oaipmhtab" aria-controls="oaipmhtab" role="tab" data-toggle="tab"><?php _e('OAI-PMH','tainacan') ?></a></li>
                    <li role="presentation"><a id="click_csvtab" href="#csvtab" aria-controls="csvtab" role="tab" data-toggle="tab"><?php _e('CSV','tainacan') ?></a></li>
                    <li role="presentation"><a id="click_metatag_tab" href="#metatag_tab" aria-controls="metatag_tab" role="tab" data-toggle="tab"><?php _e('Metatags','tainacan') ?></a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="oaipmhtab" >
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
                            <button type="button" onclick="validate_url()" id="submit_oaipmh" class="btn btn-primary tainacan-blue-btn-bg"><?php _e('Validate','tainacan'); ?></button>
                        </div>
                        <div id="loader_validacao" style="display:none">
                            <center>
                                <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                                <h3><?php _e('Validating Repository...','tainacan') ?></h3>
                            </center>
                        </div>
                        <div id="maping_container">
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
                    <div role="tabpanel" class="tab-pane" id="csvtab">
                        <div id="validate_url_csv_container" >
                            <div id="list_csv_dc">
                                <table  class="table table-bordered">
                                    <th><?php _e('Identifier','tainacan'); ?></th>
                                    <th></th>
                                    <tbody id="table_csv" >
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            <form id="formCsv" name="formCsv" enctype="multipart/form-data" method="post">
                                <div class="form-group">
                                    <input type="file" accept=".csv" id="csv_file" name="csv_file" placeholder="<?php _e('Insert the CSV file','tainacan'); ?>">
                                </div>
                                <input type="hidden" id="collection_import_csv_id" name="collection_id" value="">
                                <input type="hidden" id="operation_csv" name="operation" value="validate_csv">
                                <button type="submit" id="submit_csv" class="btn btn-primary tainacan-blue-btn-bg"><?php _e('Save','tainacan'); ?></button>
                            </form>
                        </div>
                        <div id="maping_container_csv">
                        </div>
                    </div>
                    <!-- Painel para metatags -->
                    <div role="tabpanel" class="tab-pane" id="metatag_tab">
                        <div id="validate_metatag_tab" >
                            <div id="list_oaipmh_dc">
                                <table  class="table table-bordered">
                                    <th><?php _e('Identifier','tainacan'); ?></th>
                                    <th><?php _e('Edit/Remove','tainacan'); ?></th>
                                    <tbody id="table_metatag_tab" >
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            
                            <div id="url_container_metatags" class="form-group col-md-12 no-padding">
                                <div class="col-md-12"><label><?php _e('URL','tainacan'); ?></label></div>
                                <div class="col-md-10">
                                    <input type="text" 
                                        id="url_metatag" 
                                        class="form-control" 
                                        placeholder="<?php _e('Insert the URL to extract metatags','tainacan'); ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" 
                                            onclick="validate_url_metatag()" 
                                            id="submit_metatag" 
                                            class="btn btn-primary tainacan-blue-btn-bg pull-left">
                                                <?php _e('Validate','tainacan'); ?>
                                    </button>
                                </div>
                            </div>
                            <div id="loader_validacao_metatags" style="display:none">
                                <center>
                                    <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                                    <h3><?php _e('Validating URL...','tainacan') ?></h3>
                                </center>
                            </div>
                            <div style="display: none;" id="maping_container_metatags">
                            </div>
                            <input type="hidden" id="collection_import_id" name="collection_id" value="">
                            <input type="hidden" id="operation" name="operation" value="validate_metatag">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>