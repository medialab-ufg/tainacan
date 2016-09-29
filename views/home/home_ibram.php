<?php
wp_nav_menu( ['theme_location' => 'menu-ibram', 'container_class' => 'container', 'container' => false,
    'menu_class' => 'navbar navbar-inverse menu-ibram', 'walker'    => new wp_bootstrap_navwalker() ] );

$fixed_home_collections = ["Empréstimos", "Conservação", "Restaurações", "Exposição"];
?>
<div class="col-md-12 ibram-home-container">
    <div class="row">
        <?php foreach( $fixed_home_collections as $ibram_collection): ?>
            <div class="col-md-3">
                <div class="ibram-box">
                    <h4 class="title">
                        <a href="javascript:void(0)">
                            <?php echo $ibram_collection ?>
                        </a>
                    </h4>
                    <div class="ibram-info-container">
                        <p> 5 em atraso </p>
                        <p> 3 para vencer </p>
                        <p> 100 em andamento </p>
                        <p> 100 devolvidos esta semana </p>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
</div>
