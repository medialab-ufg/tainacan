<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/tags_js.php');
include_once(dirname(__FILE__).'/../../helpers/view_helper.php');
global $config;
?>  

<div id="categories_title" class="row"> 
    <div class="col-md-12 tainacan-topo-categoria">
        <h3><?php _e('Tags', 'tainacan') ?><small>&nbsp;&nbsp;&nbsp;</small>
            <?php ViewHelper::buttonVoltar() ?>
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
        <div id="tags_dynatree" style='height: 400px;overflow: scroll;' >
        </div>
        <!--center><button onclick="add_facets()" class="btn btn-primary"><?php _e('Add selected categories as facets', 'tainacan'); ?></button></center-->

    </div>
    <div class="col-md-6">
        <form id="submit_form_tag">
            <div class="create_form-group">
                <label for="tag_name"><?php _e('Tag name', 'tainacan'); ?></label>
                <input maxlength="50" type="text" class="form-control" id="tag_name" name="tag_name" required="required" placeholder="<?php _e('Tag name', 'tainacan'); ?>">
            </div>
            <div class="form-group">
                <label for="socialdb_event_term_description"><?php _e('Tag description', 'tainacan'); ?>&nbsp;<span style="font-size: 10px;">(<?php _e('Optional', 'tainacan'); ?>)</span></label>
                <textarea class="form-control" id="tag_description" placeholder="<?php _e('Describe your tag', 'tainacan'); ?>" 
                          name="socialdb_event_term_description" ></textarea>    
            </div>
            <br><br>
            <input type="hidden" id="tag_single_collection_id" name="socialdb_event_collection_id" value="<?php echo $collection_id; ?>">
                        <input type="hidden" id="tag_single_create_time" name="socialdb_event_create_date" value="<?php echo mktime(); ?>">
                        <input type="hidden" id="tag_single_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>">
            <input type="hidden" id="tag_collection_id" name="collection_id" value="<?php echo $collection_id; ?>">
            <input type="hidden" id="tag_id" name="tag_id" value="">
            <input type="hidden" id="operation_tag_form" name="operation" value="add">
            <button type="button" onclick="clear_buttons()" class="btn btn-default" id="clear_categories"><?php _e('Clear', 'tainacan'); ?></button>
            <button type="submit" id="submit" class="btn btn-default"><?php _e('Submit', 'tainacan'); ?></button>
        </form>
    </div>    
</div> 
<ul id="myMenu" class="contextMenu" style="display:none;margin-top: -24%;">
    <li class="edit"><a href="#edit"><?php echo __('Edit', 'tainacan'); ?></a></li>
    <li class="delete"><a href="#delete"><?php echo __('Remove', 'tainacan'); ?></a></li>
</ul> 
<!-- modal exluir -->
<div class="modal fade" id="modalExcluirTagUnique" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="submit_delete_tag">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php echo __('Remove Tag', 'tainacan'); ?></h4>
                </div>
                <div class="modal-body">
                    <?php echo __('Confirm the exclusion of ', 'tainacan'); ?> <span id="delete_tag_name"></span>?
                </div>
                <input type="hidden" id="tag_single_delete_id" name="socialdb_event_tag_id" value="">
                <input type="hidden" id="operation_tag_delete" name="operation" value="add_event_tag_delete">
                <input type="hidden" id="tag_single_delete_collection_id" name="socialdb_event_collection_id" value="<?php echo get_the_ID(); ?>">
                <input type="hidden" id="tag_single_delete_time" name="socialdb_event_create_date" value="<?php echo mktime(); ?>">
                <input type="hidden" id="tag_single_delete_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>">
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo __('Confirm', 'tainacan'); ?></button>
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
