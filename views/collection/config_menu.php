<div class="col-md-2" style="padding:10px 0;text-align:right; z-index: 1">
    <?php if ((verify_collection_moderators($current_collection_id, get_current_user_id()) || current_user_can('manage_options')) && get_post_type($current_collection_id) == 'socialdb_collection'): ?>
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" >
            <div class="fab"><img src="<?php echo get_template_directory_uri() ?>/libraries/images/configuracao.svg" alt="" class="img-responsive"></div>
        </a>
        <ul style="z-index: 9999;" class="dropdown-menu pull-right" role="menu">
            <li><a style="cursor: pointer;" onclick="showCollectionConfiguration('<?php echo get_template_directory_uri() ?>');" ><span class="glyphicon glyphicon-wrench"></span>&nbsp;<?php _e('Configuration', 'tainacan'); ?></a></li>

            <?php /* ?>
                        <li <?php do_action('menu_collection_property_configuration') ?>>
                            <a style="cursor: pointer;" onclick="showPropertiesConfiguration('<?php echo get_template_directory_uri() ?>');" >
                                <span class="glyphicon glyphicon-list-alt"></span>
                                &nbsp;<?php _e('Metadata', 'tainacan'); ?>
                            </a>
                        </li>
                        */?>

            <li <?php do_action('menu_collection_property_and_filters_configuration') ?>>
                <a style="cursor: pointer;" onclick="showPropertiesAndFilters('<?php echo get_template_directory_uri() ?>');" >
                    <span class="glyphicon glyphicon-list-alt"></span>
                    &nbsp;<?php _e('Metadata and Filters', 'tainacan'); ?>
                </a>
            </li>
            <li>
                <a style="cursor: pointer;" onclick="showLayout('<?php echo get_template_directory_uri() ?>');" >
                    <span class="glyphicon glyphicon-tint"></span>
                    &nbsp;<?php _e('Layout', 'tainacan'); ?>
                </a>
            </li>
            <?php
            /*  <li <?php do_action('menu_collection_search_configuration') ?>><a style="cursor: pointer;" onclick="showSearchConfiguration('<?php echo get_template_directory_uri() ?>');" ><span class="glyphicon glyphicon-list-alt"></span>&nbsp;<?php _e('Search', 'tainacan'); ?></a></li>
                <li><a style="cursor: pointer;" onclick="showRankingConfiguration('<?php echo get_template_directory_uri() ?>');" ><span class="glyphicon glyphicon-star"></span>&nbsp;<?php _e('Rankings', 'tainacan'); ?></a></li>
                <li><a style="cursor: pointer;" onclick="showDesignConfiguration('<?php echo get_template_directory_uri() ?>');" ><span class="glyphicon glyphicon-picture"></span>&nbsp;<?php _e('Design', 'tainacan'); ?></a></li>
             */ ?>
            <li class="divider"></li>
            <?php /*
            <li><a onclick="showUsersConfiguration('<?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-user"></span>&nbsp;<?php _e('Users', 'tainacan'); ?></a></li>
            <li><a style="cursor: pointer;" onclick="showCategoriesConfiguration('<?php echo get_template_directory_uri() ?>');" ><span class="glyphicon glyphicon-filter"></span>&nbsp;<?php _e('Categories', 'tainacan'); ?></a></li>
            */ ?>
            <?php
            // esta acao permite a insercao de itens neste menu
            do_action('add_configuration_menu_tainacan'); ?>
            <?php
            if (get_option('collection_root_id') == $current_collection_id) {
                ?>
                <!--li class="divider"></li>
                <li><a onclick="showAPIConfiguration('< ?php echo get_template_directory_uri() ?>');" href="#"><span class="glyphicon glyphicon-lock"></span>&nbsp;< ?php _e('API Keys Configuration'); ?></a></li-->
                <?php
            } else {
                ?>
                <li <?php do_action('menu_collection_social_configuration') ?>>
                    <a style="cursor: pointer;" onclick="showSocialConfiguration('<?php echo get_template_directory_uri() ?>');" >
                        <span class="glyphicon glyphicon-user"></span>&nbsp;<?php _e('Social', 'tainacan'); ?>
                    </a>
                </li>
                <li  <?php do_action('menu_collection_license') ?>>
                    <a style="cursor: pointer;" onclick="showLicensesConfiguration('<?php echo get_template_directory_uri() ?>');" >
                        <span class="glyphicon glyphicon-user"></span>&nbsp;<?php _e('Licenses', 'tainacan'); ?>
                    </a>
                </li>
                <li  <?php do_action('menu_collection_import') ?>>
                    <a style="cursor: pointer;" onclick="showImport('<?php echo get_template_directory_uri() ?>');" >
                        <span class="glyphicon glyphicon-open"></span>&nbsp;<?php _e('Import', 'tainacan'); ?>
                    </a>
                </li>
                <li  <?php do_action('menu_collection_export') ?>>
                    <a style="cursor: pointer;" onclick="showExport('<?php echo get_template_directory_uri() ?>');" >
                        <span class="glyphicon glyphicon-save"></span>&nbsp;<?php _e('Export', 'tainacan'); ?>
                    </a>
                </li>

                <li class="divider"></li>
                <li style="//background-color: #e4b9b9;"><a onclick="delete_collection_redirect('<?php _e('Delete Collection', 'tainacan') ?>', '<?php echo __('Are you sure to remove the collection: ', 'tainacan') . $collection_post->post_title ?>', '<?php echo $current_collection_id ?>', '<?= mktime() ?>', '<?php echo get_option('collection_root_id') ?>')" href="#"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Delete', 'tainacan'); ?></a></li>
                <li style="//background-color: #e4b9b9;"><a onclick="clean_collection('<?php _e('Clean Collection', 'tainacan') ?>', '<?php echo __('Are you sure to remove all items', 'tainacan') ?>', '<?php echo $collection_post->ID ?>')" style="cursor: pointer;"><span class="glyphicon glyphicon-unchecked"></span>&nbsp;<?php _e('Clean Collection', 'tainacan'); ?></a></li>
                <?php
            }
            if ( get_option('collection_root_id') != $current_collection_id) {
                ?>
                <li class="divider"></li>
                <li><a onclick="showEvents('<?php echo get_template_directory_uri() ?>');" style="color:<?php echo $collection_metas['socialdb_collection_board_link_color']; ?>" href="#"><span class="glyphicon glyphicon-flash"></span> <?php _e('Events', 'tainacan'); ?>&nbsp;<span id="notification_events" style="background-color:red;color:white;font-size:13px;"></span></a></li>
                <?php if (!verify_collection_moderators($current_collection_id, get_current_user_id()) && !current_user_can('manage_options')): ?>
                    <li><a onclick="show_report_abuse_collection('<?php echo $current_collection_id; ?>');" style="color:<?php echo $collection_metas['socialdb_collection_board_link_color']; ?>" href="#"><span class="glyphicon glyphicon-warning-sign"></span> <?php _e('Report Abuse', 'tainacan'); ?>&nbsp;</a></li>
                    <!-- modal exluir -->
                    <div class="modal fade" id="modal_delete_collection<?php echo $current_collection_id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form>
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Report Abuse', 'tainacan'); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <?php echo __('Describe why the collection: ') . $collection_post->post_title . __(' is abusive: ', 'tainacan'); ?>
                                        <textarea id="observation_delete_collection<?php echo $current_collection_id ?>" class="form-control"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                                        <button onclick="report_abuse_collection('<?php _e('Delete Collection', 'tainacan') ?>', '<?php _e('Are you sure to remove the collection: ', 'tainacan') . $collection_post->post_title ?>', '<?php echo $current_collection_id ?>', '<?php echo mktime() ?>', '<?php echo get_option('collection_root_id') ?>')" type="button" class="btn btn-primary"><?php echo __('Delete', 'tainacan'); ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php
            }
            ?>
        </ul>
    <?php elseif(is_user_logged_in()): ?>
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" >
            <div class="fab">
                <img src="<?php echo get_template_directory_uri() ?>/libraries/images/configuracao.svg" alt="" class="img-responsive">
            </div>
        </a>
        <ul class="dropdown-menu pull-right" role="menu">
            <li><a onclick="showEvents('<?php echo get_template_directory_uri() ?>');" style="color:<?php echo $collection_metas['socialdb_collection_board_link_color']; ?>" href="#"><span class="glyphicon glyphicon-flash"></span> <?php _e('Events', 'tainacan'); ?>&nbsp;<span id="notification_events" style="background-color:red;color:white;font-size:13px;"></span></a></li>
            <?php if (!verify_collection_moderators($collection_post->ID, get_current_user_id()) && !current_user_can('manage_options')): ?>
                <li><a onclick="show_report_abuse_collection('<?php echo $collection_post->ID; ?>');"  style="cursor: pointer;"><span class="glyphicon glyphicon-warning-sign"></span> <?php _e('Report Abuse', 'tainacan'); ?>&nbsp;</a></li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
</div>