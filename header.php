<!DOCTYPE html>

<!--[if IEMobile 7 ]> <html <?php language_attributes(); ?>class="no-js iem7"> <![endif]-->
<!--[if lt IE 7 ]> <html <?php language_attributes(); ?> class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html <?php language_attributes(); ?> class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html <?php language_attributes(); ?> class="no-js ie8"> <![endif]-->
<!--[if (gte IE 9)|(gt IEMobile 7)|!(IEMobile)|!(IE)]><!-->
<?php
include_once('helpers/view_helper.php');

global $current_user;
get_currentuserinfo();
$socialdb_logo = get_option('socialdb_logo');
$socialdb_title = get_option('blogname');
$col_root_id = get_option('collection_root_id');

$stat_page = get_page_by_title(__('Statistics', 'tainacan'))->ID;
$viewHelper = new ViewHelper();
$_src_ = get_template_directory_uri();
?>
<html <?php language_attributes(); ?> xmlns:fb="http://www.facebook.com/2008/fbml" class="no-js"><!--<![endif]-->
<head>
  <meta charset="<?php bloginfo('charset'); ?>"><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="google-site-verification" content="29Uww0bx9McdeJom1CDiXyGUZwK5mtoSuF5tA_i59F4" />
  <link rel="icon" type="image/png" href="<?php echo $_src_ . '/libraries/images/icone.png' ?>">
  <title> <?php echo repository_page_title() ?> </title>
  <?php if (is_front_page()) { ?>
    <link rel="alternate" type="application/rdf+xml" href="<?php echo site_url(); ?>/?.rdf">
    <?php if (is_restful_active()) { ?>
      <link rel="alternate" type="application/json" href="<?php echo site_url(); ?>/wp-json/">
      <?php } ?>
      <?php } else if (is_page_tainacan()) { ?>
        <link rel="alternate" type="application/rdf+xml" href="<?php echo get_the_permalink(); ?>?<?php echo get_page_tainacan() ?>=<?php echo trim($_GET[get_page_tainacan()]) ?>.rdf">
        <?php if (is_restful_active()) { ?>
          <link rel="alternate" type="application/json" href="<?php echo site_url() . '/wp-json/posts/' . get_post_by_name($_GET[get_page_tainacan()], OBJECT, 'socialdb_object')->ID . '/?type=socialdb_object' ?>">
          <?php } ?>
          <?php } else if (is_single()) { ?>
            <meta name="thumbnail_url" content="<?php echo get_the_post_thumbnail_url(get_the_ID()) ?>" />
            <meta name="description" content="<?php echo get_the_content() ?>" />
            <link rel="alternate" type="application/rdf+xml" href="<?php echo get_the_permalink(); ?>?.rdf">
            <?php $_GOOGLE_API_KEY = "AIzaSyBZXPZcDMGeT-CDugrsYWn6D0PQSnq_odg"; ?>
            <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $_GOOGLE_API_KEY; ?>"></script>

            <?php if (is_restful_active()) { ?>
              <link rel="alternate" type="application/json" href="<?php echo site_url() . '/wp-json/posts/' . get_the_ID() . '/?type=socialdb_collection' ?>">
              <?php } ?>
              <?php } ?>

              <?php echo set_config_return_button(is_front_page()); ?>

              <meta name="viewport" content="width=device-width, initial-scale=1.0">
              <?php wp_head(); ?>
            </head>
            <!-- TAINACAN: tag body adaptado para o gplus -->
            <body <?php body_class(); ?> itemscope>
              <?php
              if (is_front_page() || is_page($stat_page)) {
                echo home_header_bg($socialdb_logo);
              }

              global $wp_query;
              $collection_id = $wp_query->post->ID;
              $collection_owner = $wp_query->post->post_author;
              $user_owner = get_user_by('id', $collection_owner)->display_name;
              $_header_enabled = get_post_meta($collection_id, 'socialdb_collection_show_header', true);
              ?>
              <!-- TAINACAN: tag nav, utilizando classes do bootstrap nao modificadas, onde estao localizados os links que chamam paginas da administracao do repositorio -->
              <nav <?php echo set_navbar_bg_color('black', $stat_page); ?> class="navbar navbar-default header-navbar">
                <?php //wp_nav_menu( array( 'theme_location' => 'header-menu' ) ); ?>

                <div class="container-fluid">

                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
                  </button>

                  <div class="navbar-header logo-container">
                    <?php
                      echo $viewHelper->renderRepositoryLogo($socialdb_logo, $socialdb_title);

                      if("disabled" == $_header_enabled) {
                    ?>
                        <div class="col-md-8 left repository no-padding">
                          <h5> <?php echo bloginfo('name'); ?> </h5>
                        </div>

                      <?php } ?>

                  </div>

                  <!-- TAINACAN: container responsavel em listar os links para as acoes no repositorio -->
                  <div class="user-actions collapse navbar-collapse" id="bs-example-navbar-collapse-1">

                    <div class="right-menu-container">
                      <!-- TAINACAN: mostra acoes do usuario, cadastro, login, edital perfil suas colecoes -->
                      <ul class="nav navbar-nav navbar-right admin-configs">
                        <?php if (is_user_logged_in()): ?>
                          <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                              <?php echo ViewHelper::render_icon('user', 'png'); ?>
                              <?php echo $current_user->display_name; ?> <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu dropdown-show" role="menu" id="tainacan-menu">
                              <li>
                                <a href="#" onclick="showProfileScreen('<?php echo $_src_ ?>');"> <?php _e('Profile', 'tainacan'); ?></a>
                              </li>
                              <!--li>
                                <a href="<?= get_the_permalink($col_root_id) . '?mycollections=true' ?>"><?php _e('My collections', 'tainacan'); ?></a>
                              </li>
                              <li>
                                <a href="<?= get_the_permalink($col_root_id) . '?sharedcollections=true' ?>"><?php _e('Shared Collections', 'tainacan'); ?></a>
                              </li-->
                              <li>
                                <a style="cursor: pointer;" onclick="showCategoriesConfiguration('<?php echo $_src_; ?>', '<?php echo is_front_page(); ?>');updateStateRepositorio('categories');" >
                                  <?php _e('My Categories', 'tainacan'); ?>
                                </a>
                              </li>
                              <li>
                                <a href="<?php echo wp_logout_url(get_permalink()); ?>"> <?php _e('Logout', 'tainacan'); ?> </a>
                              </li>

                              <?php if (current_user_can('manage_options')): ?>
                                <li class="divider"></li>
                                <!-- TAINACAN: mostra acoes do repositorio dentro da tag <div id="configuration"> localizado no arquivo single.php -->
                                <li class="admin-config-menu">
                                  <a class="config" href="javascript:void(0)"> <?php _e('Repository Control Panel', 'tainacan'); ?> <span class="caret"></span> </a>
                                  <ul class="admin-config-submenu" aria-expanded="false">
                                      <li><a onclick="repoConfig('<?php echo $_src_ ?>', 'edit_general_configuration');updateStateRepositorio('configuration');" style="cursor: pointer;" ><span class="glyphicon glyphicon-wrench"></span> <?php _e('Configuration', 'tainacan'); ?></a></li>
                                      <li><a onclick="repoConfig('<?php echo $_src_ ?>', 'list_repository', 'property');updateStateRepositorio('metadata');" style="cursor: pointer;" ><span class="glyphicon glyphicon-list-alt"></span> <?php _e('Metadata', 'tainacan'); ?></a></li>
                                      <li><a href="<?php echo get_bloginfo('url') ?>/wp-admin/users.php"> <span class="glyphicon glyphicon-user"></span> <?php _e('Users', 'tainacan'); ?> </a></li>
                                      <li class="tainacan-museum-clear" <?php do_action('menu_repository_social_api') ?>><a onclick="repoConfig('<?php echo $_src_ ?>', 'edit_configuration');updateStateRepositorio('social');" style="cursor: pointer;"><span class="glyphicon glyphicon-globe"></span>  <?php _e('Social / API Keys', 'tainacan'); ?></a></li>
                                      <li class="tainacan-museum-clear" <?php do_action('menu_repository_license') ?>><a onclick="repoConfig('<?php echo $_src_ ?>', 'edit_licenses');updateStateRepositorio('licenses');" style="cursor: pointer;"><span class="glyphicon glyphicon-duplicate"></span> <?php _e('Licenses', 'tainacan'); ?></a></li>
                                      <?php
                                        if(has_action('addLibraryMenu'))
                                        {
                                            $email_name = __("E-mails configuration", "tainacan");
                                        }else $email_name = __('Welcome Email', 'tainacan');
                                      ?>
                                      <li class="tainacan-museum-clear">
                                          <a onclick="repoConfig('<?php echo $_src_ ?>', 'edit_welcome_email'); updateStateRepositorio('email');" style="cursor: pointer;">
                                              <span  class="glyphicon glyphicon-envelope"></span>
                                              <?php echo $email_name ?>
                                          </a>
                                      </li>
                                      <?php
                                        if(has_action("addLibraryMenu"))
                                        {
                                          ?>
                                        <li><a style="cursor: pointer;" onclick="get_impressions_page('http://localhost/wordpress/biblioteca/wp-content/themes/tainacan');"><span  class="glyphicon glyphicon-print"></span> <?php _e('Impressions', 'tainacan'); ?></a></li>
                                          <?php
                                        }
                                      ?>
                                      <li><a onclick="repoConfig('<?php echo $_src_ ?>', 'edit_tools'); updateStateRepositorio('tools');" style="cursor: pointer;"><span  class="glyphicon glyphicon-tasks"></span> <?php _e('Tools', 'tainacan'); ?></a></li>
                                      <li><a onclick="repoConfig('<?php echo $_src_ ?>', 'import_full'); updateStateRepositorio('import');" style="cursor: pointer;"><span class="glyphicon glyphicon-import"></span> <?php _e('Import', 'tainacan'); ?></a></li>
                                      <li><a onclick="repoConfig('<?php echo $_src_ ?>', 'export_full'); updateStateRepositorio('export');" style="cursor: pointer;"><span class="glyphicon glyphicon-export"></span> <?php _e('Export', 'tainacan'); ?></a></li>
                                      <li><a onclick="repoConfig('<?php echo $_src_ ?>', 'updates_page'); " style="cursor: pointer;"><span class="glyphicon glyphicon-refresh"></span> <?php _e('Updates', 'tainacan'); ?></a></li>
                                      <li><a class="repository-statistics" href="<?php echo get_the_permalink($stat_page); ?>"> <span class="glyphicon glyphicon-globe"></span> <?php _e('Statistics', 'tainacan'); ?> </a></li>
                                      <li class="divider" style="padding: 0"></li>
                                      <li class="repository-events">
                                          <a onclick="repoConfig('<?php echo $_src_ ?>', 'list_events_repository', 'event', '<?php echo $col_root_id ?>');
                                                  updateStateRepositorio('events');" style="cursor: pointer;">
                                              <span class="glyphicon glyphicon-flash"></span> <?php _e('Events', 'tainacan'); ?>&nbsp;&nbsp;
                                              <span id="notification_events_repository"></span></a>
                                      </li>

                                    </ul>
                                  </li>
                                <?php endif; ?>
                              </ul>
                            </li>
                            <li class="root-notifications hide">
                              <a href="javascript:void(0)" class="dropdown-toggle" id="collectionEvents" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php echo ViewHelper::render_icon("sino", "png", _t('Collection Events')); ?>
                                <span class="notification_events_repository"></span>
                              </a>
                              <ul class="dropdown-menu" aria-labelledby="collectionEvents"></ul>
                            </li>
                            <li class="manual">
                              <a href="<?php echo MANUAL_TAINACAN_URL ?>">
                                <?php echo ViewHelper::render_icon("help", "png", __('Click to download the manual', 'tainacan')); ?>
                              </a>
                            </li>
                            <li class="wp-admin">
                              <a href="<?php echo get_admin_url(); ?>" target="_blank">
                                <span class="dashicons dashicons-wordpress"></span>
                              </a>
                            </li>
                          <?php else: ?>
                            <li>
                              <div id="login-box" class="login-dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  &nbsp;<?php _e('Login', 'tainacan') ?>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                  <?php include_once "views/user/login_header.php"; ?>
                                </div>
                              </div>
                            </li>
                            <li>
                              <button class="btn btn-default pull-right" onclick="registerUser('<?php echo $_src_; ?>');" href="javascript:void(0)">
                                &nbsp;<?php _e('Register', 'tainacan') ?>
                              </button>
                            </li>
                          <?php endif; ?>
                        </ul>

                        <ul <?php echo (has_nav_menu('menu-ibram')) ? 'style="display:none"' : '' ?> class="nav navbar-nav navbar-right repository-settings">
                          <!-- TAINACAN: mostra a busca avancada dentro da tag <div id="configuration"> localizado no arquivo single.php -->
                          <!--li><a onclick="showAdvancedSearch('<?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-search"></span>&nbsp;<?php _e('Advanced Search', 'tainacan'); ?></a></li -->
                          <!--button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-chevron-down"></span></button>
                          <!--a class="dropdown-toggle" href="#" data-toggle="dropdown">Sign In <strong class="caret"></strong></a-->
                        </ul>

                        <div class="nav navbar-nav navbar-right repository-settings clk">

                          <!--Exibe menus  "Coleções"-->
                          <?php
                            if (!has_action('tainacan_show_reason_modal'))
                            {
                          ?>
                            <ul id="collections-menu">
                            <li class="collections">
                              <a href="<?php echo get_permalink($col_root_id); ?>" class='collecs'>
                                <?php echo ViewHelper::render_icon('collection', 'png'); ?>
                                <div style="display:inline-block; margin-left: 5px;">
                                  <?php _e('Collections', 'tainacan'); ?>
                                  <span class="caret" style="font-size: 14px"></span>
                                </div>
                              </a>
                              <ul style="display:none; margin-top: 0;" class="sub-menu">
                                <li>
                                  <a href="<?php echo get_permalink($col_root_id); ?>">
                                    <?php _e('Show collections', 'tainacan'); ?>
                                  </a>
                                </li>
                                <?php if (is_user_logged_in()): ?>
                                  <li class="divider"></li>
                                  <li> <a class="create-collection">
                                    <?php _e('Create collection', 'tainacan') ?>
                                    <span class="glyphicon glyphicon-chevron-right"></span>
                                  </a>
                                  <ul class="sub-menu templates">
                                    <li class="click_new_collection">
                                      <a  id="click_new_collection" onclick="showModalCreateCollection()">
                                        <?php _e('General', 'tainacan'); ?>
                                      </a>
                                    </li>
                                  </ul>
                                </li>
                                <li>
                                  <a onclick="showModalImportCollection();" href="javascript:void(0)">
                                    <?php _e('Import collection', 'tainacan') ?>
                                  </a>
                                </li>

                              <?php endif; ?>
                            </ul>
                          </li>
                        </ul>
                          <?php } ?>
                      </div>

                      <?php
                      if(has_action("add_users_button"))
                      {
                        do_action("add_users_button");
                      }
                      ?>
                      <?php if (!is_front_page() && !is_plugin_active( 'ibram-tainacan/ibram-tainacan.php' )): // !is_page($stat_page) ?>
                        <form id="formSearchCollections" class="navbar-form navbar-right search-tainacan-collection" role="search">
                          <div class="input-group search-collection search-home">
                            <input style="display: none" type="text" class="form-control" name="search_collections" id="search_collections" placeholder="<?php _e('Find', 'tainacan') ?>"/>
                            <button onclick="showTopSearch();" id="expand-top-search" class="btn btn-default" type="button">
                              <?php echo ViewHelper::render_icon('search-white', 'png', __('Click to expand', 'tainacan')); ?>
                            </button>
                          </div>
                        </form>
                      <?php elseif(has_action('alter_home_page')):  ?>
                       <form id="formSearchCollectionsTopSearch" class="navbar-form navbar-right search-tainacan-collection" role="search">
                            <div class="input-group search-collection search-home">
                              <input style="display: none" type="text" class="form-control" name="search_collections" id="search_collections" placeholder="<?php _e('Find', 'tainacan') ?>"/>
                              <button onclick="showTopSearch();" id="expand-top-search" class="btn btn-default" type="button">
                                <?php echo ViewHelper::render_icon('search-white', 'png', __('Click to expand', 'tainacan')); ?>
                              </button>
                            </div>
                          </form>
                      <?php endif; ?>
                    </div>
                  </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
              </nav>

              <!-- TAINACAN: modal padrao bootstrap aberto via javascript pelo seu id, formulario inicial para criacao de colecao -->
              <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content new-collection">

                    <?php $col_controller = $_src_ . "/controllers/collection/collection_controller.php"; ?>
                    <form onsubmit="$('#myModal').modal('hide'); show_modal_main();" action="<?php echo $col_controller ?>" method="POST">
                      <?php echo $viewHelper->render_modal_header('remove-sign', __('Create Collection', 'tainacan')); ?>
                      <div id="form_new_collection">
                        <div class="modal-body" style="padding: 0 15px 0 15px;">

                          <div class="form-group">
                            <label for="collection_name"><?php _e('Collection name', 'tainacan'); ?></label>
                            <input type="text" required="required" class="form-control" name="collection_name" id="collection_name" placeholder="<?php _e('Type the name of your collection', 'tainacan'); ?>">
                          </div>

                          <input type="hidden" name="operation" value="simple_add">
                          <input type="hidden" name="template" id='template_collection' value="none">
                          <input type="hidden" name="collection_object" id='collection_object' value="<?php _e('Item'); ?>">
                        </div>

                        <div class="modal-footer" style="border-top: 0">
                          <button type="button" data-dismiss="modal" class="btn btn-default pull-left"> <?php _e('Cancel', 'tainacan'); ?> </button>
                          <button type="submit" class="btn btn-success"><?php _e('Continue', 'tainacan'); ?></button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <!-- TAINACAN: modal padrao bootstrap aberto via javascript pelo seu id, formulario inicial para criacao de colecao -->
              <div class="modal fade" id="modalImportCollection" tabindex="-1" role="dialog" aria-labelledby="modalImportCollectionLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form id="importCollection">
                      <input type="hidden" name="operation" value="importCollection">

                      <?php echo $viewHelper->render_modal_header('remove-sign', __('Import Collection', 'tainacan')); ?>

                      <div class="modal-body">
                        <!---Adição SELECT BOX-->
                        <?php
                        if (has_action('add_select_box')) {
                          do_action("add_select_box");
                        }
                        ?>
                        <div class="form-group">
                          <label for="collection_file"><?php _e('Select the file', 'tainacan'); ?></label>
                          <input type="file" required="required" class="form-control" name="collection_file" id="collection_file" >
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php _e('Cancel', 'tainacan'); ?></button>
                        <button type="submit" class="btn btn-primary pull-right"><?php _e('Import', 'tainacan'); ?></button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
