<?php
include_once ('js/edit_configuration_js.php');
include_once(dirname(__FILE__) . '/../../helpers/view_helper.php');
include_once(dirname(__FILE__) . '/../../helpers/repository/repository_helper.php');
$view_helper = new RepositoryHelper();
$post_thumb = get_the_post_thumbnail($socialdb_logo, 'thumbnail');
?>
<div class="col-md-12 ui-widget-content metadata-actions">

    <div class="col-md-12 config_default_style">
        <?php ViewHelper::render_config_title( __("Repository Configuration", 'tainacan') ); ?>
        <form id="submit_form_edit_repository_configuration">
            <div class="form-group">
                <label for="repository_title"><?php _e('Repository Title','tainacan'); ?></label>
                <input type="text" class="form-control" id="repository_title" name="repository_title" required="required" value="<?php echo $blog_name; ?>">
            </div>
            <div id="thumb-idea-form">
                <label for="repository_logo"><?php _e('Logo','tainacan'); ?></label> <br>
                <?php  if($post_thumb): echo $post_thumb; ?> <br><br>
                    <label for="remove_thumbnail"> <?php _e('Remove Thumbnail','tainacan');?> </label>
                    <input type="checkbox" id="remove_thumbnail" name="remove_thumbnail" value="true"> <br><br>
                <?php endif; ?>
                <input type="file" size="50" id="repository_logo" name="repository_logo" class="btn btn-default btn-sm">
                <br>
            </div>
            <!------------------- Capa do repositorio ----------------------------->
            <div id="cover-idea-form">
                <label for="repository_logo"><?php _e('Cover','tainacan'); ?></label>
                <br>
                <?php
                $cover_id = get_option('socialdb_repository_cover_id');
                if($cover_id){
                  echo '<img src="'.  wp_get_attachment_thumb_url($cover_id).'">'; ?>
                <br><br>
                <label for="remove_thumbnail"><?php _e('Remove Cover','tainacan'); ?></label>
                <input type="checkbox"  id="remove_cover" name="remove_cover" value="true">
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
            <!---------------- Modo de operacao --------------------------------->
            <div class="form-group">
                <label for="repository_operation"><?php _e('Repository operation','tainacan'); ?></label>
                <br>
                <input <?php echo (!$view_helper->operation||$view_helper->operation=='')? 'checked="checked"':'' ?>
                        type="radio"
                        name="tainacan_module_activate"
                        value="default">&nbsp;<?php _e('Repository','tainacan') ?> <br>
                <?php foreach ($view_helper->get_available_modules() as $module): ?>
                <input <?php echo ($view_helper->operation==$module)? 'checked="checked"':'' ?>
                        type="radio"
                        name="tainacan_module_activate"
                        value="<?php echo $module ?>">&nbsp;<?php _e($module,'tainacan') ?> <br>
                <?php endforeach; ?>
            </div>
            
            <!---------------- Colecoes templates --------------------------------->
            <div class="form-group collection-templates">
                
                <h5 style="font-weight: bolder"> <?php _e('Collection templates','tainacan'); ?> </h5>
                <div class="col-md-12">
                    <div class="form-group row">
                        <input type="text"  id="collection_template" placeholder="<?php _e('Type the collection name', 'tainacan'); ?>"  class="chosen-selected form-control" />
                        <select onclick="clear_collection_template(this)" class="chosen-selected2 form-control" style="height: auto;display: none;" multiple name="category_moderators[]" id="collection_templates"  >
                        </select>
                          <h5 style="font-weight: bolder"> <?php _e('Habilitate Collection template', 'tainacan'); ?></h5>
                        <div id="dynatree-collection-templates"></div>
                    </div>
                    <!--div class="form-group row"  id="show_collection_empty" style="display: none;">
                        <input type="checkbox" value="disabled" name="disable_empty_collection"
                            <?php echo (get_option('disable_empty_collection')=='true')?'checked="checked"':''; ?> />
                        &nbsp<?php _e('Disable Empty Collection', 'tainacan'); ?>
                    </div -->
                </div>
            </div>
            <!---------------- Cache --------------------------------->
            <div class="form-group" style="margin-bottom: 50px;" >
                <h5 style="font-weight: bolder"> <?php _e('Tainacan Cache','tainacan'); ?> </h5>
                <div class="col-md-12 no-padding">
                    <input <?php echo (get_option('tainacan_cache')==='false')? 'checked="checked"':'' ?>
                        type="checkbox"
                        name="tainacan_cache"
                        value="true">&nbsp;<?php _e('Disable Tainacan cache','tainacan') ?> <br>
                </div>
            </div>
            <div class="form-group">
                <h5 style="font-weight: bolder; margin-bottom: 2px;"> <?php _e('Permissions','tainacan'); ?> </h5>
                <p><?php _e('Choose the permissions for each one of the actions below','tainacan'); ?></p>
                <div class="col-md-12 no-padding">
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
            </div>

            <!-- Mapeamento coleção -->
            <?php
            if(has_action("add_mapping_library_collections"))
            {
                do_action("add_mapping_library_collections");
            }

            if(has_action('add_material_loan_devolution'))
            {
                do_action('add_material_loan_devolution');
            }
            ?>

            <input type="hidden" id="operation" name="operation" value="update_configuration">
            <button type="submit" class="btn btn-primary pull-right"><?php _e('Submit','tainacan'); ?></button>
        </form>
    </div>
</div>