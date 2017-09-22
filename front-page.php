<?php
$front = [ 'config' => get_option('show_on_front'), 'page' => get_option('page_on_front')];

get_header();
?>
    <header class="custom-header" style="<?php echo home_header_bg($socialdb_logo) ?>">
        <div class="menu-transp-cover"></div>
        <?php
        if (has_action('alter_home_page'))
            do_action('alter_home_page');
        else
            get_template_part("partials/header/main");
        ?>
    </header>
<?php
// Se usuário escolheu uma página para ser a Home
 if( "page" === $front['config'] ) {
     $_menu_ = ['container_class' => 'container', 'container' => false, 'walker' => new wp_bootstrap_navwalker(), 'menu_class' => 'navbar navbar-inverse menu-ibram'];
     $page_template = get_page_template_slug($front['page']);

     wp_nav_menu($_menu_);

     if('template-home.php' === $page_template) {
         load_template(dirname( __FILE__ ) . '/template-home.php');
     }

    // Ou se escolheu "Seus posts recentes" para ser a home (padrão da instalação WP)
 } else { ?>
     <!-- TAINACAN: esta div (AJAX) recebe html e está presente tanto na index quanto no single, pois algumas views da administracao sao carregadas aqui -->
     <div id="configuration" class="col-md-12 no-padding"></div>

     <?php if (has_nav_menu("menu-ibram")):
         get_template_part("partials/home", "ibram");
     else: ?>
         <div id="display_view_main_page" class="container-fluid"></div>
         <div id="loader_collections">
             <img src="<?php echo get_template_directory_uri() . '/libraries/images/new_loader.gif' ?>" width="64px" height="64px"
                  alt="<?php _t('Loading', 1); ?>" title="<?php _t('Loading', 1); ?>"/>
             <h3> <?php _e('Loading Collections...', 'tainacan') ?> </h3>
         </div>
     <?php endif; ?>

     <div id='container-fluid-configuration' class="container-fluid no-padding">
         <div id="users_div"></div> <!-- classe de users_div -->
     </div>

     <?php
 }

get_template_part("partials/setup","header");
get_footer();
?>