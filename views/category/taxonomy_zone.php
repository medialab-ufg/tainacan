<?php
include_once ('js/taxonomy_zone_js.php');
include_once(dirname(__FILE__) . '/../../helpers/view_helper.php');
include_once(dirname(__FILE__) . '/../../helpers/category/category_helper.php');
$view_helper = new CategoryHelper;
?>
<style>
    .style-input{
        padding: 5px;border: 1px solid #ccc;border-radius: 4px; 
    }
    .margin-buttons{
        margin-left: 15px;
        margin-top: 15px;
    }
    .label-button{
        color: #0c698b;
    }
    #taxonomy_create_zone ul{
        list-style: square;
    }
</style>    
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