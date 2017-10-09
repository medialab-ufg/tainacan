<?php
/*
 * Template Name: HomePage
 * */
?>

<?php get_header(); ?>

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