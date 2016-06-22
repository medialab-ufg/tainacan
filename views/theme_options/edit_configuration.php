<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/edit_configuration_js.php');
?>
<!--div class="fuelux">
    <div id="MyWizard" class="fuelux wizard">

        <ul class="steps fuelux step-content">
            <a href="#"><li class="active"><span class="badge badge-info">1</span><?php echo  __("Configuration") ?><span class="chevron"></span></li></a>
            <a href="#"><li><span class="fuelux badge">2</span><?php echo  __("Categories") ?><span class="fuelux chevron"></span></li></a>
            <a href="#"><li><span class="fuelux badge">3</span><?php echo  __("Properties") ?><span class="fuelux chevron"></span></li></a>
            <a href="#"><li><span class="fuelux badge">4</span><?php echo  __("Rankings") ?><span class="fuelux chevron"></span></li></a>
            <a onclick="showAPIConfiguration('<?php echo get_template_directory_uri() ?>');" href="#"><li><span class="fuelux badge">5</span><?php echo  __("Social / API Keys") ?><span class="fuelux chevron"></span></li></a>
            <a href="#"><li><span class="fuelux badge">6</span><?php echo  __("Design") ?><span class="fuelux chevron"></span></li></a>
        </ul>
        <div class="fuelux actions">
             <a href="#" class="btn btn-mini btn-next" data-last="Finish"><?php echo  __("Next") ?><span class="glyphicon glyphicon-chevron-right"></span></i></a>
        </div>
    </div>
</div-->
<?php include_once "common/redirect_button.php" ?>

<div class="col-md-10">
    <h3><?php _e('Repository Configuration','tainacan'); ?></h3>
    <hr>
    <form  id="submit_form_edit_repository_configuration">
        <div class="form-group">
            <label for="repository_title"><?php _e('Repository Title','tainacan'); ?></label>
            <input type="text" class="form-control" id="repository_title" name="repository_title" required="required" value="<?php echo $blog_name; ?>">
        </div>
        <div id="thumb-idea-form">
            <label for="repository_logo"><?php _e('Logo','tainacan'); ?></label>
            <br>
            <?php
            if(get_the_post_thumbnail($socialdb_logo, 'thumbnail')){
              echo get_the_post_thumbnail($socialdb_logo, 'thumbnail'); ?>
            <br><br>
            <label for="remove_thumbnail"><?php _e('Remove Thumbnail','tainacan'); ?></label>
            <input type="checkbox"  id="remove_thumbnail" name="remove_thumbnail" value="true">
            <br><br>
            <?php }else{
                
            } 
            ?>
            <input type="file" size="50" id="repository_logo" name="repository_logo" class="btn btn-default btn-sm">
            
            <br>
        </div>
        <!------------------- Capa do repositorio ----------------------------->
        <div id="cover-idea-form">
            <label for="repository_logo"><?php _e('Cover','tainacan'); ?></label>
            <br>
            <?php
            $cover_id = get_post_meta($socialdb_logo, 'socialdb_respository_cover_id', true);
            if($cover_id){
              echo '<img src="'.  wp_get_attachment_thumb_url($cover_id).'">'; ?>
            <br><br>
            <label for="remove_thumbnail"><?php _e('Remove Cover','tainacan'); ?></label>
            <input type="checkbox"  id="remove_cover" name="remove_thumbnail" value="true">
            <br><br>
            <?php } ?>
            <input type="file" size="50" id="socialdb_collection_cover" name="socialdb_collection_cover" class="btn btn-default btn-sm">
            <br>
        </div>
        <!------------------- Descricao---------------------------------------->
        <div class="form-group">
            <label for="collection_description"><?php _e('Repository description','tainacan'); ?></label>           
            <textarea rows="4" id="editor" name="editor"   value="" placeholder='<?= __("Describe your collection in few words"); ?>'><?php echo $blog_description; ?></textarea>
            <input type="hidden" name="repository_content" id="repository_content" value="" />
        </div>            
        <!---------------- Colecoes templates --------------------------------->
        <div class="form-group">
            <fieldset class="scheduler-border">
                <legend class="scheduler-border"><strong><?php _e('Collection templates','tainacan'); ?></strong></legend>
                <div class="col-md-12">
                    <div class="form-group row">
                            <input type="text"  id="collection_template" placeholder="<?php _e('Type the collection name', 'tainacan'); ?>"  class="chosen-selected form-control" />
                            <select onclick="clear_collection_template(this)" class="chosen-selected2 form-control" style="height: auto;" multiple name="category_moderators[]" id="collection_templates"  >
                           </select>
                    </div>
                    <div class="form-group"  id="show_collection_empty"   style="display: none;">
                        <input type="checkbox" 
                               <?php echo (get_option('disable_empty_collection')=='true')?'checked="checked"':''; ?>
                               value="disabled" 
                               name="disable_empty_collection"/>
                        &nbsp<?php _e('Disable Empty Collection', 'tainacan'); ?>
                    </div>
                </div>
            </fieldset>
        </div>
        
        <div class="form-group">
            <fieldset class="scheduler-border">
                <legend class="scheduler-border"><strong><?php _e('Permissions - Choose permissions for each of the following actions','tainacan'); ?></strong></legend>
                <div class="col-md-12">
                    <div class="form-group row">
                        <div class="col-md-5">
                            <label for="socialdb_collection_permission_create_collection"><?php _e('Create Collection','tainacan'); ?></label>
                            <select name="socialdb_collection_permission_create_collection" id="socialdb_repository_permission_create_collection" class="form-control">
                                <option value="approval" <?php if ($socialdb_repository_permissions['socialdb_collection_permission_create_collection'] == 'approval') { echo 'selected = "selected"'; } ?>><?php _e('Approval','tainacan'); ?></option>
                                <option value="members" <?php if ($socialdb_repository_permissions['socialdb_collection_permission_create_collection'] == 'members') { echo 'selected = "selected"'; } ?>><?php _e('Members','tainacan'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="socialdb_collection_permission_delete_collection"><?php _e('Delete Collection','tainacan'); ?></label>
                            <select name="socialdb_collection_permission_delete_collection" id="socialdb_repository_permission_delete_collection" class="form-control">
                                <option value="approval" <?php if ($socialdb_repository_permissions['socialdb_collection_permission_delete_collection'] == 'approval') { echo 'selected = "selected"'; } ?>><?php _e('Approval','tainacan'); ?></option>
                                <option value="members" <?php if ($socialdb_repository_permissions['socialdb_collection_permission_delete_collection'] == 'members') { echo 'selected = "selected"'; } ?>><?php _e('Members','tainacan'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
        <input type="hidden" id="operation" name="operation" value="update_configuration">
        <button type="submit" id="submit_configuration" class="btn btn-primary btn-lg pull-right"><?php _e('Submit','tainacan'); ?></button>
    </form>
</div>	