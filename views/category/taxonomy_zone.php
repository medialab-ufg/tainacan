<?php
include_once ('js/taxonomy_zone_js.php');
include_once(dirname(__FILE__) . '/../../helpers/view_helper.php');
include_once(dirname(__FILE__) . '/../../helpers/category/category_helper.php');
$view_helper = new CategoryHelper;
?>
<div class="col-md-12 config-temp-box">

    <?php $view_helper->render_header_config_steps('categories') ?>

    <div class="col-md-12 tainacan-config-container">
        <form id="submit_taxonomy_zone" onkeypress="return event.keyCode != 13;">
            <div class="form-group">
                <input type="text" 
                       class="col-md-6 style-input" 
                       placeholder="<?php _e('Type the category name', 'tainacan') ?>"
                       id="category_root_name" 
                       name="category_root_name" 
                       required="required" 
                       value="<?php echo $view_helper->get_category_root_name($collection_id) ?>">
                <div  class="col-md-4" >
                    <center>
                        <button type="button" onclick="add_field_category()" class="btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span><?php _e('Add sub-category','tainacan') ?>
                        </button>
                        <button type="button"
                                class="btn btn-default" 
                                onclick="up_category_taxonomy()">
                            <span class="glyphicon glyphicon-arrow-up"></span>
                        </button>
                        <button type="button"
                                class="btn btn-default" 
                                onclick="down_category_taxonomy()">
                            <span class="glyphicon glyphicon-arrow-down"></span>
                        </button>
                        <button type="button"
                                class="btn btn-default" 
                                onclick="remove_hierarchy_taxonomy_create_zone()">
                            <span class="glyphicon glyphicon-indent-right"></span>
                        </button>
                        <button type="button"
                                class="btn btn-default" 
                                onclick="add_hierarchy_taxonomy_create_zone()">
                            <span class="glyphicon glyphicon-indent-left"></span>
                        </button>
                    </center>    
                </div>
                <button type="button"
                        class="col-md-2 btn btn-default label-button"
                        onclick="show_modal_import_taxonomy('<?php echo get_post_meta($collection_id, 'socialdb_collection_object_type', true); ?>', '<?php echo $view_helper->get_category_root_name($collection_id) ?>')"
                        >
                    <?php _e('Import Taxonomy','tainacan') ?>
                </button>
            </div>
            <div class="col-md-12" 
                 id="taxonomy_create_zone"
                 onclick="verify_has_li()"
                 style="min-height:350px ;margin-top: 15px;padding: 15px;border: 1px solid #ccc;border-radius: 4px;">
                <?php echo $view_helper->inserted_children($collection_id)  ?>
            </div>
            <input type="hidden" value="<?php echo str_replace( '"',"'", $view_helper->inserted_children($collection_id)) ?>" id="socialdb_property_term_new_taxonomy" name="socialdb_property_term_new_taxonomy">
            <input type="hidden" id="verify_collection_name" name="verify_collection_name" value="allow">
            <input type="hidden" id="redirect_to_caegories" name="redirect_to_caegories" value="false">
            <input type="hidden" id="collection_id" name="collection_id" value="<?php echo $collection_id; ?>">
            <input type="hidden" id="operation" name="operation" value="taxonomy_zone_submit">
            <input type="hidden" id="save_and_next" name="save_and_next" value="false">
            <button type="submit" 
                    id="submit_configuration"
                    class="btn btn-success pull-right margin-buttons" >
                        <?php _e('Save', 'tainacan'); ?>
            </button>
            <button type="submit" 
                    class="btn btn-primary pull-right margin-buttons" >
                <?php _e('Save & Next', 'tainacan'); ?>
            </button>    
        </form>
    </div>
</div>
<!-- modal import taxonomy -->
<div class="modal fade" id="modal_import_taxonomy"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-lg">
        <form method="post" id="import_taxonomy_submit"
              enctype="multipart/form-data">

            <div class="modal-content">
                <div class="modal-header">
                    <h4> <?php _e('Import Taxonomy in ', 'tainacan') ?>&nbsp;<span id='import_taxonomy_title'></span></h4>
                </div>    
                <div class="row col-md-12" style="margin: 7px;">
                    <div class="form-group ">
                        <label for="input_file_import" ><?php _e('Send the xml file ', 'tainacan') ?></label>
                        <input required="required" id="input_file_import" class="btn btn-default" type="file" name="xml"/>
                    </div>
                </div> 
                <input name="operation" class="btn btn-default" type="hidden" value="insert_hierarchy"/>
                <input name="root_category_id" id='import_taxonomy_root_category_id' type="hidden" value=""/>
                <input name="collection_id" class="btn btn-default" type="hidden" value="" id="collection_id_hierarchy_import"/>
                <div class="modal-footer">
                    <input class="btn btn-primary pull-right" type="submit" value="<?php echo __('Send File', 'tainacan'); ?>"/>
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                </div> 
            </div>
        </form>
    </div>
</div>