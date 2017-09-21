<?php
include_once ('js/updates_page_js.php');
include_once ('../../helpers/view_helper.php');
?>
<div class="col-md-12 ui-widget-content metadata-actions">

    <div id="export_settings" class="col-md-12 config_default_style">
        <?php ViewHelper::render_config_title(__("Import", 'tainacan')); ?>
        <div class="col-md-12 no-padding">
            <div role="tabpanel">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a id="click_api" href="#api" aria-controls="api" role="tab" data-toggle="tab"><?php _e('Main repository', 'tainacan') ?></a></li>
                    <li role="presentation"><a id="click_updates" href="#updates" aria-controls="updates" role="tab" data-toggle="tab"><?php _e('Version', 'tainacan') ?></a></li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- Tab panes -->
                    <div role="tabpanel" class="tab-pane active" id="api"><br>
                        <form id="formCsv" name="formCsv" enctype="multipart/form-data" method="post">

                            <div class="form-group">
                                <label><?php _e('Main repository', 'tainacan'); ?></label>
                                <div class="input-group">
                                    <input type="text" id="url_api" class="form-control" placeholder="<?php _e('Example: http://tainaca.org', 'tainacan'); ?>">
                                    <span class="input-group-btn">
                                        <button onclick="testLinkAPI()"  class="btn btn-default" type="button"><?php _e('Test connection!', 'tainacan'); ?></button>
                                    </span>
                                </div><!-- /input-group -->
                                <br>
                                <div class="row col-md-12">
                                    <div class="col-md-4 no-padding">
                                        <div class="form-group">
                                            <input type="text" id="api_user"  placeholder="<?php _e('Main repository user', 'tainacan'); ?>" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" id="api_key"  placeholder="<?php _e('Main repository password', 'tainacan'); ?>" class="form-control">
                                        </div>    
                                    </div>    
                                </div>    
                            </div>
                            <div></div>
                            <input type="hidden" id="operation_csv" name="operation" value="import_full_csv">
                            <button type="button" onclick="confirmationAPI()" id="submit_csv" class="btn btn-success btn-lg pull-left"><?php _e('Import', 'tainacan'); ?></button>
                        </form>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="updates"><br>
                        <form id="formCsv" name="formCsv" enctype="multipart/form-data" method="post">

                            <div class="form-group">
                                <label><?php _e('Main repository', 'tainacan'); ?></label>
                                <div class="input-group">
                                    <input type="file" >
                                    <span class="input-group-btn">
                                        <button onclick="testLinkAPI()"  class="btn btn-default" type="button"><?php _e('Test connection!', 'tainacan'); ?></button>
                                    </span>
                                </div><!-- /input-group -->
                            </div>
                            <div></div>
                            <input type="hidden" id="operation_csv" name="operation" value="import_full_csv">
                            <button type="button" onclick="confirmationAPI()" id="submit_csv" class="btn btn-success btn-lg"><?php _e('Import', 'tainacan'); ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>