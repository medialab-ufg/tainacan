<div class="col-md-12 table-view-container top-div table-responsive" >

    <h1><?php _e("Signed up users"); ?></h1>

    <table id = "table-users" class="table table-striped table-bordered table-hover dataTable" >
        <thead >
            <tr>
                <th> <?php _e("Name", "tainacan"); ?> </th>
                <th> <?php _e("User", "tainacan"); ?> </th>
                <th> <?php _e("E-mail", "tainacan"); ?> </th>
            </tr >
        </thead >

        <tbody id = "table-view-elementsll" >
            <?php
            $signed_up_users = get_users();
            foreach ($signed_up_users as $user) {
                ?>
                <tr onclick="showUser(<?php echo $user->id ?>);" class="user-row">
                    <td> <?php echo $user->display_name; ?> </td>
                    <td> <?php echo $user->user_login; ?> </td>
                    <td> <?php echo $user->user_email; ?> </td>
                </tr>
                <?php
            }
            ?>
        </tbody >
    </table >
</div >
