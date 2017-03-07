<?php
$_curr_user_ = ['is_logged' => is_user_logged_in(), 'is_subscriber' =>  current_user_can('subscriber'), 'is_admin' => current_user_can('manage_options') ];
if( $_curr_user_['is_logged'] && $_curr_user_['is_admin'] ) {
    $_current_menu = "menu-ibram";
} else if ( !$_curr_user_['is_logged'] || $_curr_user_['is_subscriber']) {
    $_current_menu = "menu-ibram-visitor";
}

wp_nav_menu( ['theme_location' => $_current_menu, 'container_class' => 'container', 'container' => false,
    'menu_class' => 'navbar navbar-inverse menu-ibram', 'walker' => new wp_bootstrap_navwalker() ] );

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
                        <p> <?php echo rand(10, 50); ?> em atraso </p>
                        <p> <?php echo rand(1, 70); ?> para vencer </p>
                        <p> <?php echo rand(60, 150); ?> em andamento </p>
                        <p> <?php echo rand(40, 120); ?> devolvidos esta semana </p>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
</div>
