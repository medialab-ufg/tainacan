<?php get_header(); ?>
<div class="page-not-found">
    <h3> <?php _e('Page not found!' , 'tainacan'); ?> </h3>
    <h5 class="btn btn-info">
        <a href="<?php echo home_url(); ?>"> <?php _t('Return to Home Page' , 1); ?> </a>
    </h5>
</div>
<?php get_footer(); ?>