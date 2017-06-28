<?php
include_once(dirname(__FILE__) . '/../../helpers/view_helper.php');
include_once ('js/edit_tools_js.php');
?>
<div class="col-md-12">
    <div class="col-md-12 config_default_style">
        <?php ViewHelper::render_config_title( __("Tools", 'tainacan') ); ?>
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#aba-populate" aria-controls="property_data_tab" role="tab" data-toggle="tab"><?php _e('Populate', 'tainacan') ?></a></li>
            <li role="presentation"><a href="#aba-teste-integridade" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Integrity Test', 'tainacan') ?></a></li>

            <li role="presentation">
                <a href="#aba-reindexacao" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Reindexation', 'tainacan') ?></a>
            </li>
        </ul>
        <div class="tab-content">
            <!-- Aba Popular Coleção-->
            <div id="aba-populate" class="tab-pane fade in active">
                <h4 style="padding: 10px 0 0 15px"><?php _e('Populate collection', 'tainacan'); ?></h4>
                <hr>
                <form id="submit_form_edit_tools" style="padding-left: 15px">
                    <!------------------- Coleção-------------------------->
                    <div class="form-group">
                        <label for="socialdb_collection_id"><strong><?php _e('Collection', 'tainacan'); ?></strong></label>
                        <input type="text" style="width: 67%"  id="collection" placeholder="<?php _e('Type the collection name', 'tainacan'); ?>"  class="chosen-selected form-control" />
                        <input type="hidden" class="form-control" name="socialdb_collection_id" id="socialdb_collection_id" value="" />
                    </div>

                    <hr class="tools-internal-hr">
                    
                    <!------------------- Categorias e Subcategorias-------------------------->
                    <div class="form-group">
                        <label for="subcategories_per_level"><?php _e('Subcategories per level', 'tainacan'); ?></label>
                        <input type="text" class="form-control" style="width: 67%" name="subcategories_per_level" id="subcategories_per_level" value="" placeholder="<?php _e('Type here', 'tainacan'); ?>" onkeyup="somaCategorias();" />
                    </div>
                    <div class="form-group">
                        <label for="number_levels"><?php _e('Number of levels', 'tainacan'); ?></label>
                        <input type="text" class="form-control" style="width: 67%" name="number_levels" id="number_levels" value="" placeholder="<?php _e('Type here', 'tainacan'); ?>" onkeyup="somaCategorias();" />
                    </div>
                    <div class="form-group">
                        <label for="total_categories"><?php _e('Total of Categories', 'tainacan'); ?>:</label>
                        <input type="text" class="form-control" style="width: 67%" name="total_categories" id="total_categories" disabled="disabled" />
                    </div>
                    <div class="form-group">
                        <label for="items_category"><?php _e('Items per category', 'tainacan'); ?></label>
                        <input type="text" class="form-control" style="width: 67%" name="items_category" id="items_category" value="" placeholder="<?php _e('Type here', 'tainacan'); ?>" onkeyup="somaItens();" />
                    </div>
                    <div class="form-group">
                        <label for="total_items"><?php _e('Total of Items', 'tainacan'); ?>:</label>
                        <input type="text" class="form-control" style="width: 67%" name="total_items" id="total_items" disabled="disabled" />
                    </div>
                    <div class="form-group">
                        <label for="classification"><?php _e('Classification', 'tainacan'); ?></label>
                        <input type="text" class="form-control" style="width: 67%" name="classification" id="classification" value="" placeholder="<?php _e('Type here', 'tainacan'); ?>" onkeyup="somaClassificacao();" />
                    </div>
                    <div class="form-group">
                        <label for="total_classifications"><?php _e('Total of Classifications', 'tainacan'); ?>:</label>
                        <input type="text" class="form-control" style="width: 67%" name="total_classifications" id="total_classifications" disabled="disabled" />
                    </div>

                    <input type="hidden" id="operation" name="operation" value="populate_collection">
                    <button type="submit" id="submit_tools"  class="btn btn-primary pull-right"><?php _e('Populate', 'tainacan'); ?></button>
                </form>
            </div>

            <!-- Aba Teste de Integridade-->
            <div id="aba-teste-integridade" class="tab-pane fade">
                <br>
                <form  id="submit_form_integrity_test">
                    <input type="hidden" id="operation" name="operation" value="integrity_test">
                    <button type="submit" id="submit_integrity"  class="btn btn-primary pull-right"><?php _e('Start Test', 'tainacan'); ?></button>
                </form>
                <br>
                <div id="show_console_test" style="display: none; background-color: #dadada;">
                    <div style="padding: 50px !important;">
                        <div id="table_content_div">
                            <table id="dataTable_console" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th style="width: 10%;"><?php _e("ID", 'tainacan'); ?></th>
                                        <th style="width: 30%;"><?php _e("Title", 'tainacan'); ?></th>
                                        <th style="width: 25%;"><?php _e("MD5 Initial", 'tainacan'); ?></th>
                                        <th style="width: 25%;"><?php _e("MD5 Final", 'tainacan'); ?></th>
                                        <th style="width: 10%;"><?php _e("Result", 'tainacan'); ?></th>
                                    </tr>
                                </thead>
                                <!--tfoot>
                                    <tr>
                                        <th><?php _e("ID", 'tainacan'); ?></th>
                                        <th><?php _e("Title", 'tainacan'); ?></th>
                                        <th><?php _e("MD5 Initial", 'tainacan'); ?></th>
                                        <th><?php _e("MD5 Final", 'tainacan'); ?></th>
                                        <th><?php _e("Result", 'tainacan'); ?></th>
                                    </tr>
                                </tfoot-->
                                <tbody id="dataTable_console_content">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <br>
                <div id="show_generate_pdf" style="padding-left: 20px !important; padding-bottom: 20px !important; display: none;">
                    <button id="generate_pdf_test"  class="btn btn-primary" onclick="javascript:autoTablePDF();"><?php _e('Generate PDF', 'tainacan'); ?></button>
                </div>
            </div>

            <!-- Aba Reindexação -->
            <p id="aba-reindexacao" class="tab-pane fade">
                <h4 style="padding: 10px 0 0 15px"><?php _e('Reindexation', 'tainacan'); ?></h4>
                <hr>
                <?php _e('Choose what will be reindexed:');?>
                <br><br>

                <form id="reindexation_form">
                    <input type="checkbox" name="pdf_text"> <?php _e("Reindex text from PDF files", "tainacan"); ?><br>
                    <input type="checkbox" name="pdf_thumbnail"> <?php _e("Reindex thumbnails from PDF files", "tainacan"); ?><br>
                    <input type="checkbox" name="office_text"> <?php _e("Reindex text from Microsoft Office files", "tainacan"); ?><br>
                    <br>
                    <button class="btn btn-primary" type="submit"><?php _e("Reindex", "tainacan"); ?></button>
                </form>
            </div>
        </div>
        <br>
    </div>

    <!-- TAINACAN: modal padrao bootstrap para demonstracao de execucao do processo   -->
    <div class="modal fade" id="modalPopulate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <center>
                        <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                        <div id="cronometer" >
                            <center>
                                <h3><span id="hora">00h</span><span id="minuto">00m</span><span id="segundo">00s</span></h3>
                            </center>
                        </div>
                        <h3><?php _e('Number of documents inserted', 'tainacan') ?>&nbsp;:&nbsp;<span id="documents_inserted"></span></h3>
                        <h3><?php _e('Number of categories', 'tainacan') ?>&nbsp;:&nbsp;<span id="categories_inserted"></span></h3>
                    </center>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>