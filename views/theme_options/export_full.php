<?php
include_once ('js/export_full_js.php');
include_once ('../../helpers/view_helper.php');
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
                    <!-- Tab panes -->
                    <div role="tabpanel" class="tab-pane active" id="zip">
                        <form id="form_export_zip" method="post" action="<?php echo get_template_directory_uri() ?>/controllers/export/zip_controller.php">
                            <div class="export-container">
                                <input type="hidden" id="operation_import_aip" name="operation" value="export_full_aip" />
                                <select disabled="disabled" class="form-control">
                                    <option selected="selected"><?php _e('Dspace Format', 'tainacan') ?></option>
                                </select>
                            </div>

                            <button type="submit" id="export_zip" class="btn btn-primary tainacan-blue-btn-bg"><?php _e('Export AIP', 'tainacan'); ?></button>
                        </form>
                    </div>
                    <!-- Tab panes -->
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