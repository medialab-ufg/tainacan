<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/create_js.php');
?>
<h3>
    <?php if(has_action('label_add_item')): ?>
           <?php do_action('label_add_item',$object_name) ?>
    <?php else: ?>
          <?php _e('Add','tainacan'); ?><?php echo ' '.$object_name; ?>
    <?php endif; ?>
    <button onclick="back_main_list();"class="btn btn-default pull-right">
        <b><?php _e('Back','tainacan') ?></b>
    </button>
</h3>
<!--button onclick="back_main_list();"class="btn btn-default btn-sm pull-right"><span class="glyphicon glyphicon-backward"></span></button-->

<hr>
<form  id="submit_form">
    <input type="hidden" id="object_id_add" name="object_id" value="<?php echo $object_id ?>">
    <div class="form-group">
        <label for="object_name"><?php _e('Item name','tainacan'); ?></label>
        <input class="form-control" type="text" class="form-control" id="object_name" name="object_name" required="required" placeholder="<?php _e('Item name','tainacan'); ?>">
    </div>
    <div class="form-group" <?php do_action('item_type_attributes') ?> >
        <label for="object_name"><?php _e('Item type','tainacan'); ?></label><br>
        <input type="radio"
               onchange="show_other_type_field(this)"
               name="object_type"
               value="text"
               checked="checked" required>&nbsp;<?php _e('Text','tainacan'); ?><br>
        <input type="radio"
               name="object_type"
               id="video_type"
               onchange="show_other_type_field(this)"
               value="video" required>&nbsp;<?php _e('Video','tainacan'); ?><br>
        <input type="radio"
               onchange="show_other_type_field(this)"
               name="object_type"
               value="image" required>&nbsp;<?php _e('Image','tainacan'); ?><br>
        <input type="radio"
               onchange="show_other_type_field(this)"
               name="object_type"
               value="pdf" required>&nbsp;<?php _e('PDF','tainacan'); ?><br>
        <input type="radio"
               name="object_type"
               onchange="show_other_type_field(this)"
               value="audio" required>&nbsp;<?php _e('Audio','tainacan'); ?><br>
        <input type="radio"
               onchange="show_other_type_field(this)"
               name="object_type"
               value="other"  required>&nbsp;<?php _e('Other','tainacan'); ?>
        <!--  TAINACAN:  Field extra para outro formato -->
        <input style="display: none;"
               type="text"
               id="object_type_other"
               name="object_type_other"
               value="" >
        <br>
    </div>
    <div id="thumb-idea-form" <?php do_action('item_from_attributes') ?>>
        <label for="object_thumbnail">
            <?php _e('Internal or external','tainacan'); ?>
        </label><br>
        <input type="radio"
               name="object_from"
               id="external_option"
               onchange="toggle_from(this)"
               value="external" required>&nbsp;<?php _e('Web Address','tainacan'); ?>
            <!--  TAINACAN: Campo para importacao de noticias ou outros item VIA URL do tipo texto -->
            <div style="display:
                 none;padding-top: 10px;"
                 id="object_url_text"
                 class="input-group"
            >
                <input onkeyup="set_source(this)"
                       type="text"
                       id="url_object"
                       value="<?php echo (isset($has_url)?$has_url:'') ?>"
                       class="form-control input-medium placeholder"
                       placeholder="<?php _e('Type/paste the URL and click in the button import','tainacan'); ?>" name="object_url"  >
                <span class="input-group-btn">
                    <button onclick="import_object();" class="btn btn-primary" type="button"><?php _e('Import','tainacan'); ?></button>
                </span>
            </div>
            <!-- TAINACAN: Campo para importacao de outros arquivos via url -->
            <div id="object_url_others" style="display: none;padding-top: 10px;" >
                <!--span style="display: none;" id="badge_helper" class="label label-default"><?php _e('The URL must contain the format ( and only the format) of the item (Ex. .png,.pdf,.mp3), except videos.','tainacan') ?></span><br><br-->
                <input
                       type="text"
                       onkeyup="set_source(this)"
                       id="object_url_others_input"
                       placeholder="<?php _e('Type/paste the URL','tainacan'); ?>"
                       class="form-control"
                       name="object_url"
                       value="" >
            </div>

        <br>
        <input type="radio"
               id="internal_option"
               onchange="toggle_from(this)"
               checked="checked"
               name="object_from"
               value="internal"  required>&nbsp;<?php _e('Local','tainacan'); ?>
         <input style="display: none;padding-top: 10px;"
                type="file" size="50"
                id="object_file"
                name="object_file"
                class="btn btn-default btn-sm">
        <br>
        <br>
    </div>
   <!-- TAINACAN: Campo com o ckeditor para items do tipo texto -->
   <div id="object_content_text" class="form-group" <?php do_action('item_content_attributes') ?>>
        <label for="object_editor"><?php _e('Item Content','tainacan'); ?></label>
        <textarea class="form-control" id="object_editor" name="object_editor" placeholder="<?php _e('Object Content','tainacan'); ?>">
        </textarea>
    </div>
    <!-- TAINACAN: thumbnail do item -->
    <div id="thumbnail-idea-form"  <?php do_action('item_thumbnail_attributes') ?>>
        <label for="object_thumbnail">
            <?php _e('Item Thumbnail','tainacan'); ?>
             <?php do_action('optional_message') ?>
        </label>
        <input type="hidden" name="thumbnail_url" id="thumbnail_url" value="">
        <div id="image_side_create_object">
        </div>
        <input type="file"
               size="50"
               id="object_thumbnail"
               name="object_thumbnail"
               class="btn btn-default btn-sm">
        <br>
    </div>
    <!-- TAINACAN: a fonte do item -->
    <div class="form-group" <?php do_action('item_source_attributes') ?>>
        <label for="object_editor">
            <?php _e('Item Source','tainacan'); ?>
        </label>
        <input
               type="text"
               id="object_source"
               class="form-control"
               name="object_source"
               placeholder="<?php _e('What\'s the object source','tainacan'); ?>"
               value="" >
    </div>
    <!-- TAINACAN: a descricao do item -->
    <div id="object_description" class="form-group">
        <label for="object_description"><?php _e('Item Description','tainacan'); ?></label>
        <textarea class="form-control" id="object_description_example" name="object_description" ></textarea>
    </div>
    <!-- TAINACAN: UPLOAD DE ANEXOS DOS ITEMS -->
    <div <?php do_action('item_attachments_attributes') ?> id="dropzone_new" <?php ($socialdb_collection_attachment=='no') ? print_r('style="display:none"') : '' ?> class="dropzone">
    </div>
    <div class="form-group" <?php do_action('item_tags_attributes') ?>>
        <label for="object_tags"><?php _e('Object tags','tainacan'); ?></label>
        <input type="text" class="form-control" id="object_tags" name="object_tags"  placeholder="<?php _e('The set of tags may be inserted by comma','tainacan') ?>">
    </div>
    <div id="show_form_properties">
        <center><img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>"><h3><?php _e('Loading Properties...', 'tainacan') ?></h3></center>
    </div>
    <div id="show_form_licenses">
    </div>
     <div id="create_list_ranking_<?php echo $object_id ?>"></div>
    <input type="hidden" id="object_classifications" name="object_classifications" value="">
    <input type="hidden" id="object_content" name="object_content" value="">
    <input type="hidden" id="create_object_collection_id" name="collection_id" value="">
    <input type="hidden" id="operation" name="operation" value="add">
    <!--button onclick="back_main_list();" style="margin-bottom: 20px;"  class="btn btn-default btn-lg pull-left"><b><?php _e('Back','tainacan') ?></b></button-->
    <button type="submit" id="submit" style="margin-bottom: 20px;" class="btn btn-primary btn-lg pull-right send-button"><?php _e('Submit','tainacan'); ?></button>
</form>