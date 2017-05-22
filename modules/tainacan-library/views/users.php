<h1><?php _e("Signed up users"); ?></h1>
<?php
    $signed_up_users = get_users();
    foreach ($signed_up_users as $user)
    {
        print_r($user);
        print "<br><br>";
    }
?>
