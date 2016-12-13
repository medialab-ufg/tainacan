<?php
/**
 * Template Name: Statistics
 */

if ( current_user_can('manage_options') ):
    get_header(); ?>
    <script type="text/javascript">
        $(function() {
            $.ajax({
                url: $('.stat_path').val() + '/controllers/log/log_controller.php',
                type: 'POST',
                data: { operation: 'show_statistics' }
            }).done(function(res) {
                $('#tainacan-stats').html(res);
            });
        });
    </script>

    <input type="hidden" class="stat_path" value="<?php echo get_template_directory_uri() ?>">
    <div id='tainacan-stats' class='col-md-12 no-padding'>
        <center>
            <img src="<?php echo get_template_directory_uri() . '/libraries/images/ajaxLoader.gif' ?>" width="64px" height="64px" />
        </center>
    </div>
    <?php
    get_footer();
else:
    $home = home_url("/");
    header("Location: " . $home);
endif;