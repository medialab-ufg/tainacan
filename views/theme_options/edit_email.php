<?php
include_once ('js/edit_email_js.php');
include_once ('../../helpers/view_helper.php');
?>
<div class="col-md-12">
    <div class="col-md-12 config_default_style" id="licenses_settings">

        <?php ViewHelper::render_config_title( __("Welcome Email Configuration", 'tainacan') ); ?>

        <form  id="submit_form_edit_welcome_email">
            <!------------------- Descricao-------------------------->
            <div class="form-group">
                <label for="collection_description"><?php _e('Email','tainacan'); ?></label>
                <textarea rows="4" id="editor" name="editor"   value="" placeholder='<?= __("Type here the email that will be sent when someone register"); ?>'><?php echo $socialdb_welcome_email; ?></textarea>
                <input type="hidden" name="welcome_email_content" id="welcome_email_content" value="" />

            </div>

            <div class="alert alert-info" role="alert">
                <div><?php _e('If you want to put the name of the user just insert on the text of the email: __USER_NAME__ ','tainacan'); ?></div>
                <div><?php _e('If you want to put the login of the user just insert on the text of the email: __USER_LOGIN__ ','tainacan'); ?></div>
            </div>

            <input type="hidden" id="operation" name="operation" value="update_welcome_email">
<!--            <button type="submit" id="submit_configuration" class="btn btn-primary pull-right">--><?php //_e('Submit','tainacan'); ?><!--</button>-->

            <?php echo ViewHelper::render_default_submit_button(); ?>

        </form>
        <?php
        if(has_action('add_loan_mail'))
        {
            do_action('add_loan_mail');
        }
        ?>
    </div>

</div>