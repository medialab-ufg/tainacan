<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/edit_email_js.php');
include_once "common/redirect_button.php";
?>
<div class="col-md-10">
  
    <h3><?php _e('Welcome Email Configuration','tainacan'); ?></h3>
    <form  id="submit_form_edit_welcome_email">
        <!------------------- Descricao-------------------------->
        <div class="form-group">
            <label for="collection_description"><?php _e('Email','tainacan'); ?></label>           
            <textarea rows="4" id="editor" name="editor"   value="" placeholder='<?= __("Type here the email that will be sent when someone register"); ?>'><?php echo $socialdb_welcome_email; ?></textarea>
            <input type="hidden" name="welcome_email_content" id="welcome_email_content" value="" />

        </div>   
        
        <div class="alert alert-info" role="alert">
            <h3><?php _e('Tips:','tainacan'); ?></h3>
            <p><?php _e('If you want to put the name of the user just insert on the text of the email: __USER_NAME__ ','tainacan'); ?></p>
            <p><?php _e('If you want to put the login of the user just insert on the text of the email: __USER_LOGIN__ ','tainacan'); ?></p>
        </div>
        
        <input type="hidden" id="operation" name="operation" value="update_welcome_email">
        <button type="submit" id="submit_configuration" class="btn btn-default"><?php _e('Submit','tainacan'); ?></button>
    </form>
</div>	