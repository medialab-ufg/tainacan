<?php
add_action("add_loan_mail", "loan_mail");
function loan_mail()
{
    $text = get_option('socialdb_devolution_email_alert');

    ?>
    <div class="col-md-12 config_default_style" id="dddd">
        <?php ViewHelper::render_config_title( __("Devolution Email Configuration", 'tainacan') ); ?>

        <form  id="devolution_email">
            <!------------------- Descricao-------------------------->
            <div class="form-group">
                <label for="collection_description"><?php _e('Email','tainacan'); ?></label>
                <textarea rows="4" id="devolution_email_alert" name="devolution_email_alert"   value="" placeholder='<?= __("Type here the email that will be sent when some material should be returned"); ?>'>
                    <?php echo $text; ?>
                </textarea>
                <input type="hidden" name="devolution_email_alert_content" id="devolution_email_alert_content" value="" >

            </div>

            <div class="alert alert-info" role="alert">
                <div><?php _e('If you want to put the name of the user just insert on the text of the email: __USER_NAME__ ','tainacan'); ?></div>
                <div><?php _e('If you want to put the login of the user just insert on the text of the email: __USER_LOGIN__ ','tainacan'); ?></div>
            </div>

            <input type="hidden" id="operation" name="operation" value="update_devolution_email_alert_content">
            <!--            <button type="submit" id="submit_configuration" class="btn btn-primary pull-right">--><?php //_e('Submit','tainacan'); ?><!--</button>-->

            <?php echo ViewHelper::render_default_submit_button(); ?>

        </form>
    </div>
    <?php
}
?>