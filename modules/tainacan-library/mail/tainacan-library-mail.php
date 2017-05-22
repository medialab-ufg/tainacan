<?php
add_action("add_loan_mail", "loan_mail");
function loan_mail()
{
    ?>
    <div class="col-md-12 config_default_style" id="dddd">
        <?php
            print_r(get_users());

        ?>
        <?php ViewHelper::render_config_title( __("Welcome Email Configuration", 'tainacan') ); ?>

        <form  id="dddsssd">
            <!------------------- Descricao-------------------------->
            <div class="form-group">
                <label for="collection_description"><?php _e('Email','tainacan'); ?></label>
                <textarea rows="4" id="editor1" name="editor"   value="" placeholder='<?= __("Type here the email that will be sent when someone register"); ?>'><?php echo ''; ?></textarea>
                <input type="hidden" name="welcome_email_content" id="welcome_email_content" value="" />

            </div>

            <div class="alert alert-info" role="alert">
                <div><?php _e('If you want to put the name of the user just insert on the text of the email: __USER_NAME__ ','tainacan'); ?></div>
                <div><?php _e('If you want to put the login of the user just insert on the text of the email: __USER_LOGIN__ ','tainacan'); ?></div>
            </div>

            <input type="hidden" id="dfadfafd" name="operation" value="update_welcome_email">
            <!--            <button type="submit" id="submit_configuration" class="btn btn-primary pull-right">--><?php //_e('Submit','tainacan'); ?><!--</button>-->

            <?php echo ViewHelper::render_default_submit_button(); ?>

        </form>
    </div>
    <?php
}
?>