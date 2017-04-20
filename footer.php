<div>
    <footer id="footer"  role="contentinfo">
        <div class="row">
            <div class="col-md-5">
                <?php

                if ( is_active_sidebar( 'footer-a' ) ) { ?>
                    <div class="widgetcolumn12">
                        <?php dynamic_sidebar( 'footer-a' ); ?>
                    </div>
                    <?php
                }
                    ?>
            </div>
            <div class="col-md-4">
                <?php

                if ( is_active_sidebar( 'footer-b' ) ) { ?>
                    <div class="widgetcolumn12">
                        <?php dynamic_sidebar( 'footer-b' ); ?>
                    </div>
                    <?php
                }
                    ?>
            </div>

            <div class="col-md-3">
                <?php
                if ( is_active_sidebar( 'footer-c' ) ) { ?>
                    <div>
                        <?php dynamic_sidebar( 'footer-c' ); ?>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div><!-- .widget-area -->

    </footer><!-- #colophon -->
    <section class="logo-footer flogo-bar">
        <img class="logofooter"src="<?php echo get_template_directory_uri().'/libraries/images/Tainacan_pb.svg' ?>" width="60">
        <section class="wordpress-powered">
            <?php _e('Tainacan is proudly powered by', 'tainacan'); ?>
            <a href="https://wordpress.org/" > WordPress </a>
        </section>
    </section>
</div>
<?php wp_footer(); ?>
</body>
</html>