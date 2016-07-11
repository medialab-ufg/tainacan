<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/list_js.php');
global $config;
?>  

<div id="categories_title" class="row"> 
    <div class="col-md-12 tainacan-topo-categoria">
        <h3><?php _e('Categories', 'tainacan') ?><small>&nbsp;&nbsp;&nbsp;<!--a href="#MyWizard" onclick="show_modal_import_taxonomy()"><?php _e('Import', 'tainacan') ?></a--></small>
            <button onclick="backToMainPage();" class="btn btn-default pull-right"><?php _e('Back to collection', 'tainacan') ?></button>
        </h3> 
        <hr>
        <div id="alert_success_categories" class="alert alert-success" style="display: none;">
            <button type="button" class="close" onclick="hide_alert();"><span aria-hidden="true">&times;</span></button>
            <?php _e('Operation was successful.', 'tainacan') ?>
        </div>    
        <div id="alert_error_categories" class="alert alert-danger" style="display: none;">
            <button type="button" class="close" onclick="hide_alert();"><span aria-hidden="true">&times;</span></button>
            <span id="default_message_error">
                <?php _e('Error! Operation was unsuccessful.', 'tainacan') ?>
            </span>&nbsp;
            <span id="message_category"></span>
        </div>    
    </div>
</div>
<div class="categories_menu" class="row">
    <div class="col-md-4">
        <div id="categories_dynatree" style='height: 400px;overflow: scroll;' >
        </div>
        <!--center><button onclick="add_facets()" class="btn btn-primary"><?php _e('Add selected categories as facets', 'tainacan'); ?></button></center-->

    </div>
    <div class="col-md-6">
        <form  id="submit_form_category">
            <div class="create_form-group">
                <label for="category_name"><?php _e('Category name', 'tainacan'); ?></label>
                <input maxlength="50" type="text" class="form-control" id="category_name" name="category_name" required="required" placeholder="<?php _e('Category name', 'tainacan'); ?>">
            </div>
            <div class="form-group">
                <label for="category_parent_name"><?php _e('Category parent', 'tainacan'); ?></label>
                <input disabled="disabled" type="text" class="form-control" id="category_parent_name" placeholder="<?php _e('Right click on the tree and select the category as parent', 'tainacan'); ?>" name="category_parent_name" >
                <input type="hidden"  id="category_parent_id"  name="category_parent_id" value="0" >
            </div>
            <div class="form-group">
                 <label for="category_parent_name"><?php _e('Category description', 'tainacan'); ?>&nbsp;<span style="font-size: 10px;">(<?php _e('Optional', 'tainacan'); ?>)</span></label>
                <textarea class="form-control" 
                          id="category_description" 
                          placeholder="<?php _e('Describe your category', 'tainacan'); ?>" 
                          name="socialdb_event_term_description" ></textarea>    
            </div>
            <div class="form-group">
                <label for="category_permission"><?php _e('Category privacity', 'tainacan'); ?></label>
                <select class="form-control" id="category_permission"  style="width: 350px;height: auto;"  name="category_permission" >
                    <option selected="selected" value="private"><?php _e('Private', 'tainacan'); ?></option>
                    <option value="public"><?php _e('Public', 'tainacan'); ?></option>
                </select>
            </div>
            <div class="form-group">
                <label for="chosen-selected-user"><?php _e('Category moderators', 'tainacan'); ?></label>
                <input type="text"  id="chosen-selected-user" placeholder="<?php _e('Type the three user initial letters', 'tainacan'); ?>" style="width: 50%;" class="chosen-selected form-control" />
                <select class="chosen-selected2 form-control" style="width: 50%;height: auto;" multiple name="category_moderators[]" id="chosen-selected2-user"  >
                    <div id="visual"></div>
                </select>
            </div>
            <?php if (isset($config['mode']) && $config['mode'] == 1) { ?>
                <!------------ Modo GESTAO ARQUIVISTICA ----------------->
                <div class="form-inline form-group">
                    <label  for="current_phase"><?php _e('Current Phase', 'tainacan'); ?></label><br>
                    <input onchange="handleChange(this);" type="number" class="form-control input-sm"  id="current_phase_year" placeholder="<?php _e('Year(s)', 'tainacan'); ?>" name="current_phase_year" >
                    <input  onchange="handleChange(this);" type="number" class="form-control input-sm" id="current_phase_month" placeholder="<?php _e('Month(s)', 'tainacan'); ?>" name="current_phase_month" >
                </div>
                <div class="form-inline form-group">
                    <label for="intermediate_phase"><?php _e('Intermediate Phase', 'tainacan'); ?></label><br>
                    <input  onchange="handleChange(this);" class="form-control input-sm" type="number"  id="intermediate_phase_year" placeholder="<?php _e('Year(s)', 'tainacan'); ?>" name="intermediate_phase_year" >
                    <input  onchange="handleChange(this);" class="form-control input-sm" type="number"  id="intermediate_phase_month" placeholder="<?php _e('Month(s)', 'tainacan'); ?>" name="intermediate_phase_month" >
                </div>
                <div class="form-inline form-group">
                    <label for="destination"><?php _e('Destination', 'tainacan'); ?></label><br>
                    <input class="form-control" type="radio"  id="destination_permanent_guard" value="permanent_guard"  name="destination" ><?php _e('Permanent guard', 'tainacan') ?>
                    <input class="form-control" type="radio"  id="destination_elimination" value="elimination" name="destination" ><?php _e('Elimination', 'tainacan') ?>
                </div>
                <div class="form-group">
                    <label for="classification_code"><?php _e('Classification Code', 'tainacan'); ?></label>
                    <input  type="text" class="form-control" id="classification_code" placeholder="<?php _e('Type here the category classification code', 'tainacan'); ?>" name="classification_code" >
                </div>
                <div class="form-group">
                    <label for="classification_code"><?php _e('Observation', 'tainacan'); ?></label>
                    <textarea id="observation" class="form-control" name="observation"></textarea>
                </div>
            <?php } ?>
            <button type="button" onclick="list_category_property()" id="show_category_property" style="display: none;" class="btn btn-primary"><?php _e('Category Properties', 'tainacan'); ?></button>
            <br><br>
            <input type="hidden" id="category_collection_id" name="collection_id" value="">
            <input type="hidden" id="category_id" name="category_id" value="">
            <input type="hidden" id="operation_category_form" name="operation" value="add">
            <button type="submit" id="submit" class="btn btn-default"><?php _e('Submit', 'tainacan'); ?></button>
            <button type="button" onclick="clear_buttons()" class="btn btn-default" id="clear_categories"><?php _e('New', 'tainacan'); ?></button>
        </form>
    </div>    
</div> 
<ul id="myMenu" class="contextMenu" style="display:none;margin-top: -20%;">
    <li class="add"><a href="#add"><?php echo __('Add', 'tainacan'); ?></a></li>  
    <li class="edit"><a href="#edit"><?php echo __('Edit', 'tainacan'); ?></a></li>
    <!--   <li class="cut separator"><a href="#cut">Cut</a></li>
    <li class="copy"><a href="#copy">Copiar</a></li>
    <li class="paste"><a href="#paste">Paste</a></li> -->
    <li class="delete"><a href="#delete"><?php echo __('Remove', 'tainacan'); ?></a></li>
    <li class="set_parent"><a href="#set_parent"><?php echo _e('Set as parent', 'tainacan'); ?></a></li>
    <li class="import_taxonomy"><a href="#import_taxonomy"><?php _e('Import taxonomy', 'tainacan'); ?></a></li>
    <li class="export_taxonomy"><a href="#export_taxonomy"><?php _e('Export taxonomy', 'tainacan'); ?></a></li>
    <!--  <li class="quit separator"><a href="#quit">Quit</a></li> -->
</ul> 
<!-- modal exluir -->
<div class="modal fade" id="modalExcluirCategoriaUnique" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="submit_delete_category">   
                <input type="hidden" id="category_delete_id" name="category_delete_id" value="">
                <input type="hidden" id="operation_category_delete" name="operation" value="delete">
                <input type="hidden" id="delete_collection_id" name="collection_id" value="<?php echo $collection_id; ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php echo __('Remove Category', 'tainacan'); ?></h4>
                </div>
                <div class="modal-body">
                    <?php echo __('Confirm the exclusion of ', 'tainacan'); ?><span id="delete_category_name"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo __('Save', 'tainacan'); ?></button>
                </div>
            </form>  
        </div>
    </div>
</div>
<!-- modal propriedades -->
<div class="modal fade bs-example-modal-lg" id="modal_category_property"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-lg">
        <div class="modal-content"> 
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
            <div id="category_property">
            </div>
            <div class="modal-footer">
            </div> 
        </div>
    </div>
</div>
<!-- modal import taxonomy -->
<div class="modal fade" id="modal_import_taxonomy"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-lg">
        <form method="post" id="import_taxonomy_submit"
              enctype="multipart/form-data">

            <div class="modal-content">
                <div class="modal-header">
                    <h4> <?php _e('Import Taxonomy in ', 'tainacan') ?><span id='import_taxonomy_title'></span></h4>
                </div>    
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <div class="form-group">
                            <label for="input_file_import" ><?php _e('Send the xml file ', 'tainacan') ?></label>
                            <input required="required" id="input_file_import" class="btn btn-default" type="file" name="xml"/>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div> 
                <input name="operation" class="btn btn-default" type="hidden" value="insert_hierarchy"/>
                <input name="root_category_id" id='import_taxonomy_root_category_id' type="hidden" value=""/>
                <input name="collection_id" class="btn btn-default" type="hidden" value="" id="collection_id_hierarchy_import"/>
                <div class="modal-footer">
                    <input class="btn btn-primary pull-right" type="submit" value="Send File"/>
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                </div> 
            </div>
        </form>
    </div>
</div>
<!-- modal export taxonomy -->
<div class="modal fade" id="modal_export_taxonomy"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-lg">
        <form onsubmit="hideModalExportTaxonomy()" method="post" id="export_taxonomy_submit" action="<?php echo get_template_directory_uri() ?>/controllers/category/category_controller.php">

            <div class="modal-content">
                <div class="modal-header">
                    <h4> <?php _e('Export Taxonomy ', 'tainacan') ?>&nbsp;<span id='export_taxonomy_title'></span></h4>
                </div>    
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <div class="form-group">
                            <center><h4><strong><?php _e('Do you confirm the download of this taxonomy: ', 'tainacan') ?><span id='export_taxonomy_content'></span></strong></h4></center>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div> 
                <input name="operation" class="btn btn-default" type="hidden" value="export_hierarchy"/>
                <input name="root_category_id" id='export_taxonomy_root_category_id' type="hidden" value=""/>
                <input name="collection_id" class="btn btn-default" type="hidden" value="" id="collection_id_hierarchy_export"/>
                <div class="modal-footer">
                    <input class="btn btn-default" type="submit" value="<?php echo __('Confirm', 'tainacan'); ?>"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                </div> 
            </div>
        </form>
    </div>
</div>
<!--div class="fuelux">
    <div id="MyWizard" class="fuelux wizard">

        <ul class="steps fuelux step-content">
            <a onclick="showCollectionConfiguration('<?php echo get_template_directory_uri() ?>');" href="#submit_form_edit_collection"><li ><span class="fuelux badge">1</span><?php echo __("Configuration",'tainacan') ?><span class="chevron"></span></li><a>
                    <a onclick="showCategoriesConfiguration('<?php echo get_template_directory_uri() ?>');" href="#"><li class="active" ><span class="badge badge-info">2</span><?php echo __("Categories",'tainacan') ?><span class="fuelux chevron"></span></li></a>
                    <a onclick="showPropertiesConfiguration('<?php echo get_template_directory_uri() ?>');"><li ><span class="fuelux badge">3</span><?php echo __("Properties",'tainacan') ?><span class="fuelux chevron"></span></li>
                        <a onclick="showRankingConfiguration('<?php echo get_template_directory_uri() ?>');" href="#submit_form_edit_collection"><li><span class="fuelux badge">4</span><?php echo __("Rankings",'tainacan') ?><span class="fuelux chevron"></span></li>
                            <a onclick="showSocialConfiguration('<?php echo get_template_directory_uri() ?>');" href="#"><li><span class="fuelux badge">5</span><?php echo __("Social",'tainacan') ?><span class="fuelux chevron"></span></li>
                                <a onclick="showDesignConfiguration('<?php echo get_template_directory_uri() ?>');" href="#"><li><span class="fuelux badge">6</span><?php echo __("Design",'tainacan') ?><span class="fuelux chevron"></span></li>
                                    </ul>
                                    <div class="fuelux actions">
                                        <a onclick="showCollectionConfiguration('<?php echo get_template_directory_uri() ?>');" href="#" class="btn btn-mini btn-prev"> <span class="glyphicon glyphicon-chevron-left"></span></i><?php echo __("Previous",'tainacan') ?></a>
                                        <a onclick="showPropertiesConfiguration('<?php echo get_template_directory_uri() ?>');" href="#" class="btn btn-mini btn-next" data-last="Finish"><?php echo __("Next",'tainacan') ?><span class="glyphicon glyphicon-chevron-right"></span></i></a>
                                    </div>
                                    </div>
                                    </div--> 

