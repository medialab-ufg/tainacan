<?php
global $current_user;
$_src_ = get_template_directory_uri();
$col_root_id = get_option('collection_root_id');
$email_name = (has_action('addLibraryMenu')) ? _t("E-mails configuration") : _t('Welcome Email');
$stat_page = get_page_by_title(__('Statistics', 'tainacan'))->ID;
?>

<!-- TAINACAN: Opções de ações do administrador, Cadastro, Login, Perfil, Manual do Usuário, e Coleções -->
<div class="user-actions collapse navbar-collapse" id="to-collapse">

    <ul class="nav navbar-nav navbar-right admin-configs">
        <?php if (is_user_logged_in()): ?>
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                    <?php echo ViewHelper::render_icon('user', 'png'); ?>
                    <?php echo $current_user->display_name; ?> <span class="caret"></span>
                </a>

                <ul class="dropdown-menu" role="menu" id="tainacan-menu">
                    <li> <a href="javascript:void(0)" onclick="showProfileScreen('<?php echo $_src_ ?>');"> <?php _e('Profile', 'tainacan'); ?></a> </li>

                    <li>
                        <?php /* <a onclick="showCategoriesConfiguration('<?php echo $_src_; ?>', '<?php echo is_front_page(); ?>'); updateStateRepositorio('categories'); "> */ ?>
                        <a href="<?php getAdmin('categories'); ?>"> <?php _t('My Categories', 1); ?> </a>
                    </li>

                    <li> <a href="<?php echo wp_logout_url(get_permalink()); ?>"> <?php _t('Logout', 1); ?> </a> </li>

                    <!-- TAINACAN: mostra ações do repositorio dentro da div#configuration localizada no arquivo single.php -->
                    <?php if (current_user_can('manage_options')): ?>
                        <li role="separator" class="divider"></li>

                        <li class="dropdown-header"> <?php _t('Repository Control Panel', 1); ?> </li>

                        <li>
                            <?php /* <a onclick="repoConfig('<?php echo $_src_ ?>', 'edit_general_configuration'); updateStateRepositorio('configuration'); "> */ ?>
                            <a href="<?php getAdmin('config'); ?>">
                                <span class="glyphicon glyphicon-wrench"></span> <?php _t('Configuration', 1); ?>
                            </a>
                        </li>
                        <li>
                            <?php /* <a onclick="repoConfig('<?php echo $_src_ ?>', 'list_repository', 'property'); updateStateRepositorio('metadata'); "> */ ?>
                            <a href="<?php getAdmin('metas'); ?>">
                                <span class="glyphicon glyphicon-list-alt"></span> <?php _t('Metadata', 1); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo get_bloginfo('url') ?>/wp-admin/users.php">
                                <span class="glyphicon glyphicon-user"></span> <?php _t('Users', 1); ?>
                            </a>
                        </li>
                        <li class="tainacan-museum-clear" <?php do_action('menu_repository_social_api') ?>>
                            <?php /* <a onclick="repoConfig('<?php echo $_src_ ?>', 'edit_configuration'); updateStateRepositorio('social'); "> */ ?>
                            <a href="<?php getAdmin('keys'); ?>">
                                <span class="glyphicon glyphicon-globe"></span>  <?php _t('Social / API Keys', 1); ?>
                            </a>
                        </li>
                        <li class="tainacan-museum-clear" <?php do_action('menu_repository_license') ?>>
                            <?php /* <a onclick="repoConfig('<?php echo $_src_ ?>', 'edit_licenses'); updateStateRepositorio('licenses'); "> */ ?>
                            <a href="<?php getAdmin('licenses'); ?>">
                                <span class="glyphicon glyphicon-duplicate"></span> <?php _t('Licenses', 1); ?>
                            </a>
                        </li>
                        <li class="tainacan-museum-clear">
                            <?php /* <a onclick="repoConfig('<?php echo $_src_ ?>', 'edit_welcome_email'); updateStateRepositorio('email'); "> */ ?>
                            <a href="<?php getAdmin('email'); ?>">
                                <span  class="glyphicon glyphicon-envelope"></span> <?php echo $email_name ?>
                            </a>
                        </li>

                        <li>
                            <?php /* <a onclick="repoConfig('<?php echo $_src_ ?>', 'edit_tools'); updateStateRepositorio('tools'); "> */ ?>
                            <a href="<?php getAdmin('tools'); ?>">
                                <span  class="glyphicon glyphicon-tasks"></span> <?php _t('Tools', 1); ?>
                            </a>
                        </li>
                        <li>
                            <?php /* <a onclick="repoConfig('<?php echo $_src_ ?>', 'import_full'); updateStateRepositorio('import'); "> */ ?>
                            <a href="<?php getAdmin('import'); ?>">
                                <span class="glyphicon glyphicon-import"></span> <?php _t('Import', 1); ?>
                            </a>
                        </li>
                        <li>
                            <?php /* <a onclick="repoConfig('<?php echo $_src_ ?>', 'export_full'); updateStateRepositorio('export'); "> */ ?>
                            <a href="<?php getAdmin('export'); ?>">
                                <span class="glyphicon glyphicon-export"></span> <?php _t('Export', 1); ?>
                            </a>
                        </li>
                        <li>
                            <?php /* <a onclick="repoConfig('<?php echo $_src_ ?>', 'updates_page'); "> */ ?>
                            <a href="<?php getAdmin('update'); ?>">
                                <span class="glyphicon glyphicon-refresh"></span> <?php _t('Updates', 1); ?>
                            </a>
                        </li>
                        <li>
                            <a class="repository-statistics" href="<?php echo get_the_permalink($stat_page); ?>">
                                <span class="glyphicon glyphicon-globe"></span> <?php _t('Statistics', 1); ?>
                            </a>
                        </li>

                        <li role="separator" class="divider" style="padding: 0"></li>

                        <li class="repository-events">
                            <?php /* <a onclick="repoConfig('<?php echo $_src_ ?>', 'list_events_repository', 'event', '<?php echo $col_root_id ?>'); updateStateRepositorio('events');"> */ ?>
                            <a href="<?php getAdmin('events'); ?>">
                                <span class="glyphicon glyphicon-flash"></span> <?php _e('Events', 'tainacan'); ?>&nbsp;&nbsp;
                                <span id="notification_events_repository"></span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul> <!-- End dropdown menu -->
            </li> <!-- End dropdown -->

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
        <?php else : ?> <!-- is not logged in -->
            <li>
                <div id="login-box" class="login-dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php _e('Login', 'tainacan') ?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <?php include_once dirname(__FILE__) . "./../views/user/login_header.php"; ?>
                    </div>
                </div>
            </li>

            <li>
                <button class="btn btn-default" onclick="registerUser('<?php echo $_src_; ?>');" href="javascript:void(0)">
                    <?php _e('Register', 'tainacan') ?>
                </button>
            </li>
        <?php endif; ?>
    </ul>

    <!--Exibe menu "Coleções"-->
    <?php if (!has_action('tainacan_show_reason_modal')) { ?>
        <ul class="nav navbar-nav navbar-right repository-settings clk">
            <li class="dropdown">
                <a class='dropdown-toggle' data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <?php echo ViewHelper::render_icon('collection', 'png'); ?>
                    <div style="display:inline-block; margin-left: 5px;">
                        <?php _e('Collections', 'tainacan'); ?>
                        <span class="caret" style=""></span>
                    </div>
                </a>

                <ul class="dropdown-menu">
                    <li>
                        <a href="<?php echo get_permalink($col_root_id); ?>"><?php _t('Show collections', 1); ?></a>
                    </li>

                    <!-- If is logged in, show others options -->
                    <?php if (is_user_logged_in()) : ?>
                        <li role="separator" class="divider"></li>

                        <li class="dropdown-submenu">
                            <a class="create-collection"> <?php _t('Create collection', 1); ?> </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a style="cursor: pointer;" id="click_new_collection"> <?php _e('General', 'tainacan'); ?> </a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a onclick="showModalImportCollection();" href="javascript:void(0)">
                                <?php _t('Import collection', 1); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
        <?php
    }

    if (has_action("add_users_button"))
        do_action("add_users_button");

    if (!is_front_page() && !is_plugin_active( 'ibram-tainacan/ibram-tainacan.php' )) : // !is_page($stat_page) ?>
        <form id="formSearchCollections" class="navbar-form navbar-right search-tainacan-collection" role="search">
            <div class="input-group search-collection search-home">
                <input style="display: none" type="text" class="form-control" name="search_collections" id="search_collections" placeholder="<?php _e('Find', 'tainacan') ?>"/>
                <button onclick="showTopSearch();" id="expand-top-search" class="btn btn-default" type="button">
                    <?php echo ViewHelper::render_icon('search-white', 'png', __('Click to expand', 'tainacan')); ?>
                </button>
            </div>
        </form>
    <?php elseif (has_action('alter_home_page')): ?>
        <form id="formSearchCollectionsTopSearch" class="navbar-form navbar-right search-tainacan-collection" role="search">
            <div class="input-group search-collection search-home">
                <input style="display: none" type="text" class="form-control" name="search_collections" id="search_collections" placeholder="<?php _e('Find', 'tainacan') ?>"/>
                <button onclick="showTopSearch();" id="expand-top-search" class="btn btn-default" type="button">
                    <?php echo ViewHelper::render_icon('search-white', 'png', __('Click to expand', 'tainacan')); ?>
                </button>
            </div>
        </form>
    <?php endif; ?>

</div><!-- /.navbar-collapse -->