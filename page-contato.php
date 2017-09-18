<?php
/**
 * Template Name: Contato
 */

?>

<?php
$response = "";
function tainacan_contact_form($type, $message){
    global $response;

    if($type == "success") $response = "<div class='success'>{$message}</div>";
    else $response = "<div class='error'>{$message}</div>";
}

if( $_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['message_name'];
    $email = $_POST['message_email'];
    $message = $_POST['message_text'];
    $subject = $_POST['message_subject'];
    $missing_content = "Please supply all information.";
    $email_invalid   = "Email Address Invalid.";
    $message_unsent  = "Message was not sent. Try Again.";
    $message_sent    = "Thanks! Your message has been sent.";


    $to = get_option('admin_email');
    $subject = "$subject - from ".get_bloginfo('name');
    $headers = 'From: '. $email . "\r\n" . 'Reply-To: ' . $email . "\r\n";

    if( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
        tainacan_contact_form("error", $email_invalid);
    } else if( strlen($name) < 3 && strlen($message) < 5 && (empty($name) || empty($message)) ) {
        tainacan_contact_form("error", $missing_content);
    } else {
        $sent = wp_mail($to, $subject, strip_tags($message), $headers);
        if($sent) tainacan_contact_form("success", $message_sent); //message sent!
        else tainacan_contact_form("error", $message_unsent); //message wasn't sent
    }
}

get_header();
?>
    <div class="col-md-12 tainacan-page-area">

        <div class="col-md-12 no-padding center">

            <div id="primary" class="tainacan-content-area">
                <main id="main" class="col-md-8 center" role="main">

                    <h3>Contato</h3>
                    <hr>
                    <div class="col-md-12" style="background: white">
                        <div class="col-md-6">
                            <?php

                             if(is_active_sidebar('footer-b')):
                            dynamic_sidebar('footer-b');
                            endif; ?>
                        </div>
                        <div class="col-md-6">
                            <style type="text/css">
                                .error{
                                    padding: 5px 9px;
                                    border: 1px solid red;
                                    color: red;
                                    border-radius: 3px;
                                }

                                .success{
                                    padding: 5px 9px;
                                    border: 1px solid green;
                                    color: green;
                                    border-radius: 3px;
                                }

                                #respond form span{
                                    color: red;
                                }

                                #respond input, #respond p , #respond textarea {
                                    width: 100%;
                                    background: #F1F2F2;
                                    border: none;
                                    padding: 5px;
                                }
                            </style>
                            <h2 style="border: none">Mande sua mensagem</h2>
                            <div id="respond">
                                <?php echo $response; ?>
                                <form action="<?php the_permalink(); ?>" method="post">
                                    <p> <input type="text" required name="message_name" placeholder="Nome" value="<?php echo esc_attr($_POST['message_name']); ?>" /></p>
                                    <p> <input type="text" required placeholder="E-mail" name="message_email" value="<?php echo esc_attr($_POST['message_email']); ?>" /></p>
                                    <p> <input type="text" placeholder="Assunto" name="message_subject" value="<?php echo esc_attr($_POST['message_subject']); ?>" /></p>
                                    <p> <textarea type="text" required placeholder="Escreva sua mensagem aqui" name="message_text"><?php echo esc_textarea($_POST['message_text']); ?></textarea> </p>
                                    <input type="hidden" name="submitted" value="1">
                                    <p><submit type="submit"></p>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <?php
                        if( is_active_sidebar("contact-wids") ) {
                            dynamic_sidebar("contact-wids");
                        } else {
                            echo 'nothing to display';
                        }
                        ?>
                    </div>
                </main>
            </div>
        </div>
    </div>
<?php get_footer();