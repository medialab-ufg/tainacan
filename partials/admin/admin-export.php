<?php
include_once ( dirname(__FILE__) . '/../../views/theme_options/js/export_full_js.php');
include_once ( dirname(__FILE__) . '/../../helpers/view_helper.php');
?>
<div class="col-md-12">
    <div id="export_settings" class="col-md-12 config_default_style">
        <?php ViewHelper::render_config_title(__("Export", 'tainacan')); ?>
        <div class="col-md-12 no-padding">
            <div role="tabpanel">

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a id="click_zip" href="#zip" aria-controls="zip" role="tab" data-toggle="tab"><?php _e('AIP', 'tainacan') ?></a></li>
                    <li role="presentation"><a id="click_csv" href="#csv" aria-controls="csv" role="tab" data-toggle="tab"><?php _e('CSV', 'tainacan') ?></a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">

                    <div role="tabpanel" class="tab-pane active" id="zip">
                        <form id="form_export_zip" method="post" action="<?php echo get_template_directory_uri() ?>/controllers/theme_options/theme_options_controller.php">
                            <div class="export-container">
                                <input type="hidden" id="operation_import_aip" name="operation" value="export_full_aip" />
                                <select disabled="disabled" class="form-control">
                                    <option selected="selected"><?php _e('Dspace Format', 'tainacan') ?></option>
                                </select>
                            </div>

                            <button type="submit" onclick="start_loader_aip()" id="export_zip" class="btn btn-primary tainacan-blue-btn-bg"><?php _e('Export AIP', 'tainacan'); ?></button>
                        </form>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="csv">
                        <form id="form_export_csv" method="post" action="<?php echo get_template_directory_uri() ?>/controllers/export/export_controller.php">
                            <input type="hidden" id="operation_export_csv" name="operation" value="export_csv_file_full" />
                            <br>
                            <button type="submit" id="export_csv" class="btn btn-primary tainacan-blue-btn-bg"><?php _e('Export CSV', 'tainacan'); ?></button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExportAIP" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h4><?php _t('Download will start after the compression', true) ?></h4>
                <p><?php _t('Communities compressed', true) ?> : <span id="found-community"></span>/<span id="total-community">0</span></p>
                <p><?php _t('Collections compressed', true) ?> : <span id="found-collection"></span>/<span id="total-collection">0</span></p>
                <p><?php _t('Items compressed', true) ?> : <span id="found-item"></span>/<span id="total-item">0</span></p>
                <progress id="progressbar" value="0" max="100" style="width: 100%;"></progress><br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
            </div>
        </div>

    </div>
</div>