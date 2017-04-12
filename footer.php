<footer id="footer"  role="contentinfo">
    <!-- If you'd like to support WordPress, having the "powered by" link somewhere on your blog is the best way; it's our only promotion or advertising. -->

    <div class="col-md-4">
		<?php _e('Tainacan is proudly powered by', 'tainacan'); ?>
		<a href="https://wordpress.org/" > WordPress </a>
    </div>

    <aside class="widget-area" role="complementary">
        <?php

        if ( is_active_sidebar( 'footer-a' ) ) { ?>
<!--            <div class="widget-column footer-widget-1">-->
                <?php dynamic_sidebar( 'footer-a' ); ?>
<!--            </div>-->
        <?php }

        if ( is_active_sidebar( 'footer-b' ) ) { ?>
<!--            <div class="widget-column footer-widget-2">-->
                <?php dynamic_sidebar( 'footer-b' ); ?>
<!--            </div>-->
        <?php }

        if ( is_active_sidebar( 'footer-c' ) ) { ?>
<!--        <div class="widget-column footer-widget-3">-->
            <?php dynamic_sidebar( 'footer-c' ); ?>
<!--        </div>-->
        <?php } ?>
    </aside><!-- .widget-area -->
</footer><!-- #colophon -->
<div class="col-md-12 logo-footer" >
    <img src="<?php echo get_template_directory_uri().'/libraries/images/Tainacan_pb.svg' ?>" width="60">
</div>
<?php wp_footer(); ?>
</body>
</html>