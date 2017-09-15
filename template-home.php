<?php
/*
 * Template Name: HomePage
 * */
?>

<?php get_header();

    $_menu_args = [ 'container_class' => 'container', 'container' => false, 'walker' => new wp_bootstrap_navwalker(),
        'menu_class' => 'navbar navbar-inverse menu-ibram' ];
    ?>
    <header class="custom-header" style="<?php echo home_header_bg($socialdb_logo)?>">
        <div class="menu-transp-cover"></div>
        <?php get_template_part("partials/header/main"); ?>
    </header>

    <?php wp_nav_menu($_menu_args); ?>

    <div class="col-md-12 tainacan-page-area">

        <div class="col-md-12 no-padding center">
            <header class="page-header col-md-12 no-padding"> </header>

            <div id="primary" class="tainacan-content-area">
                <main id="main" class="col-md-8 center" role="main">
                    <?php
                    for($i = 1; $i < 5; $i++):
                        if( is_active_sidebar("part-$i") ) {
                            dynamic_sidebar("part-$i");
                        }
                    endfor;
                    ?>
                </main>
            </div>
        </div>
    </div>

<?php get_footer(); ?>