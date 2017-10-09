<?php
/**
 * Template Name: Contato
 */

$response = "";
if( $_SERVER["REQUEST_METHOD"] === "POST") {
    $name = sanitize_text_field($_POST['message_name']);
    $email = sanitize_email( $_POST['message_email'] );
    $message = sanitize_text_field( $_POST['message_text'] );
    $subject = sanitize_text_field( $_POST['message_subject'] );
    
    $missing_content = _t("Please supply all information.");
    $_invalid_mail   = _t("Email Address Invalid.");
    $message_unsent  = _t("Message was not sent. Try Again.");
    $message_sent    = _t("Thanks! Your message has been sent.");

    $to = get_option('admin_email');
    $subject = "$subject - from ".get_bloginfo('name');
    $headers = 'From: '. $email . "\r\n" . 'Reply-To: ' . $email . "\r\n";

    if( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
        tainacan_contact_form("error", $_invalid_mail);
    } else if( strlen($name) < 3 && strlen($message) < 5 && (empty($name) || empty($message)) ) {
        tainacan_contact_form("error", $missing_content);
    } else {
        $sent = wp_mail($to, $subject, strip_tags($message), $headers);
        if($sent) tainacan_contact_form("success", $message_sent);
        else tainacan_contact_form("error", $message_unsent);
    }
}

get_header();
?>
    <div class="col-md-12 tainacan-page-area">

        <div class="col-md-12 no-padding center">

            <div id="primary" class="tainacan-content-area">
                <main id="main" class="col-md-8 center" role="main">

                    <h2 class="contact-title"><?php _t('Contact',1);?></h2>

                    <div class="col-md-12 top-container">

                        <?php if(is_active_sidebar('footer-b')):
                            echo '<div class="col-md-6">';
                                dynamic_sidebar('footer-b');
                            echo '</div>';
                        endif; ?>

                        <div class="col-md-6 form-container">
                            <h2> <?php _t('Send your message',1); ?> </h2>
                            <div id="respond">
                                <?php echo $response; ?>
                                <form action="<?php the_permalink(); ?>" method="post">
                                    <p> <input type="text" required name="message_name" placeholder="Nome" value="<?php echo esc_attr($_POST['message_name']); ?>" /> </p>
                                    <p> <input type="text" required placeholder="E-mail" name="message_email" value="<?php echo esc_attr($_POST['message_email']); ?>" /> </p>
                                    <p> <input type="text" placeholder="Assunto" name="message_subject" value="<?php echo esc_attr($_POST['message_subject']); ?>" /> </p>
                                    <p> <textarea type="text" required placeholder="Escreva sua mensagem aqui" name="message_text"><?php echo esc_textarea($_POST['message_text']); ?></textarea> </p>
                                    <input type="hidden" name="submitted" value="1">
                                    <p><button type="submit" class="btn btn-default"><?php _t('Send',1); ?> </button></p>
                                </form>
                            </div>
                        </div>
                    </div>

                    <?php
                    if( is_active_sidebar("contact-widgets") ) {
                        echo '<div class="col-md-12 top-container">';
                            dynamic_sidebar("contact-widgets");
                        echo '</div>';
                    }
                    ?>

                </main>
            </div>
        </div>
    </div>
<?php
get_footer();