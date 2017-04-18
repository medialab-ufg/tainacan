<div>
    <footer id="footer"  role="contentinfo">
        <aside>
            <?php

            if ( is_active_sidebar( 'footer-a' ) ) { ?>
                <div class="widgetcolumn12 footer-widget-1">
                    <?php dynamic_sidebar( 'footer-a' ); ?>
                </div>
                <?php
            }

            if ( is_active_sidebar( 'footer-b' ) ) { ?>
                <div class="widgetcolumn12 footer-widget-2">
                    <?php dynamic_sidebar( 'footer-b' ); ?>
                </div>
                <?php
            }

            if ( is_active_sidebar( 'footer-c' ) ) { ?>
                <div class="footer-widget-3">
                    <?php dynamic_sidebar( 'footer-c' ); ?>
                </div>
                <?php
            }
            ?>
        </aside><!-- .widget-area -->

    </footer><!-- #colophon -->
    <div class="flogo-bar">
        <div class="logo-footer">
            <img src="<?php echo get_template_directory_uri().'/libraries/images/Tainacan_pb.svg' ?>" width="60">
            <div class="wordpress-powered">
                <?php _e('Tainacan is proudly powered by', 'tainacan'); ?>
                <a href="https://wordpress.org/" > WordPress </a>
            </div>
        </div>
    </div>
</div>
<?php wp_footer(); ?>
</body>
</html>