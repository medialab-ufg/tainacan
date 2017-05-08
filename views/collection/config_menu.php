<?php
    if(!isset($collection_post)){
        $collection_post = get_post();
        $current_collection_id = $collection_post->ID;
    } 
?>
<ul class="nav navbar-bar navbar-right">
    <li class="dropdown collec_menu_opnr">
    <?php if ((verify_collection_moderators($current_collection_id, get_current_user_id()) || current_user_can('manage_options')) && get_post_type($current_collection_id) == 'socialdb_collection'): ?>
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
            <span class="notification_events"></span>
            <div class="fab">
                <?php if("disabled" == $_enable_header_):
                    echo ViewHelper::render_icon("config", "png", _t('Collection configuration'));
                else: ?>
                    <img src="<?php echo get_template_directory_uri() ?>/libraries/images/configuracao.svg" alt="<?php _t('Configuration', 1); ?>" class="img-responsive">
                <?php endif; ?>
            </div>
        </a>

        <ul class="dropdown-menu pull-right dropdown-show" role="menu">
            <li><a style="cursor: pointer;" onclick="showCollectionConfiguration('<?php echo get_template_directory_uri() ?>');updateStateCollection('configuration');" ><span class="glyphicon glyphicon-wrench"></span>&nbsp;<?php _t('Configuration', 1); ?></a></li>
            <li <?php do_action('menu_collection_property_and_filters_configuration') ?>>
                <a style="cursor: pointer;" onclick="showPropertiesAndFilters('<?php echo get_template_directory_uri() ?>');updateStateCollection('metadata');" >
                    <span class="glyphicon glyphicon-list-alt"></span> &nbsp; <?php _e('Metadata and Filters', 'tainacan'); ?>
                </a>
            </li>
            <li>
                <a style="cursor: pointer;" onclick="showLayout('<?php echo get_template_directory_uri() ?>');updateStateCollection('layout');" >
                    <span class="glyphicon glyphicon-tint"></span> &nbsp; <?php _e('Layout', 'tainacan'); ?>
                </a>
            </li>
            <li <?php do_action('menu_collection_tags') ?> class="tainacan-museum-clear">
                <a style="cursor: pointer;" onclick="showCollectionTags('<?php echo get_template_directory_uri() ?>');updateStateCollection('tags');" >
                    <span class="glyphicon glyphicon-tag"></span> &nbsp; <?php _e('Tags', 'tainacan'); ?>
                </a>
            </li>

            <?php
            // esta acao permite a insercao de itens neste menu
            do_action('add_configuration_menu_tainacan');
            if (get_option('collection_root_id') != $current_collection_id) { ?>

                <li class="divider"></li>

                <li <?php do_action('menu_collection_social_configuration') ?> class="tainacan-museum-clear">
                    <a style="cursor: pointer;" onclick=" showSocialConfiguration('<?php echo get_template_directory_uri() ?>');updateStateCollection('social');" >
                        <span class="glyphicon glyphicon-globe"></span> &nbsp;<?php _e('Social', 'tainacan'); ?>
                    </a>
                </li>
                <li <?php do_action('menu_collection_license') ?> class="tainacan-museum-clear">
                    <a style="cursor: pointer;" onclick="showLicensesConfiguration('<?php echo get_template_directory_uri() ?>');updateStateCollection('licenses');" >
                        <span class="glyphicon glyphicon-duplicate"></span> &nbsp;<?php _e('Licenses', 'tainacan'); ?>
                    </a>
                </li>
                <li  <?php do_action('menu_collection_import') ?>>
                    <a style="cursor: pointer;" onclick="showImport('<?php echo get_template_directory_uri() ?>');updateStateCollection('import');" >
                        <span class="glyphicon glyphicon-open"></span> &nbsp;<?php _e('Import', 'tainacan'); ?>
                    </a>
                </li>
                <li <?php do_action('menu_collection_export') ?>>
                    <a style="cursor: pointer;" onclick="showExport('<?php echo get_template_directory_uri() ?>');updateStateCollection('export');" >
                        <span class="glyphicon glyphicon-save"></span> &nbsp;<?php _e('Export', 'tainacan'); ?>
                    </a>
                </li>
                <li <?php do_action('menu_collection_export') ?>>
                    <a style="cursor: pointer;" onclick="showStatistics('<?php echo get_template_directory_uri() ?>');updateStateCollection('statistics');" >
                        <span class="glyphicon glyphicon-stats"></span> &nbsp;<?php _e('Statistics', 'tainacan'); ?>
                    </a>
                </li>

                <li class="divider tainacan-museum-clear"></li>

                <li class="tainacan-museum-clear" style="cursor: pointer;">
                    <a onclick="delete_collection_redirect('<?php _e('Delete Collection', 'tainacan') ?>', '<?php echo __('Are you sure to remove the collection: ', 'tainacan') . $collection_post->post_title ?>', '<?php echo $collection_post->ID ?>', '<?= mktime() ?>', '<?php echo get_option('collection_root_id') ?>')" href="javascript:void(0)"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Delete', 'tainacan'); ?></a>
                </li>
                <li class="tainacan-museum-clear" style="cursor: pointer;">
                    <a onclick="clean_collection('<?php _e('Clean Collection', 'tainacan') ?>', '<?php echo __('Are you sure to remove all items', 'tainacan') ?>', '<?php echo $collection_post->ID ?>')" style="cursor: pointer;"><span class="glyphicon glyphicon-unchecked"></span>&nbsp;<?php _e('Clean Collection', 'tainacan'); ?></a>
                </li>

                <li class="divider"></li>

                <li>
                    <a class="events-link" onclick="showEvents('<?php echo get_template_directory_uri() ?>');updateStateCollection('events');" style="cursor:pointer;color:<?php echo $collection_metas['socialdb_collection_board_link_color']; ?>" >
                        <span class="glyphicon glyphicon-flash"></span> <?php _e('Events', 'tainacan'); ?> &nbsp;
                    </a>
                    <span class="notification_events"></span>
                </li>

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
        <ul class="dropdown-menu dropdown-show" role="menu">
            <li><a onclick="showEvents('<?php echo get_template_directory_uri() ?>');" style="color:<?php echo $collection_metas['socialdb_collection_board_link_color']; ?>" href="#"><span class="glyphicon glyphicon-flash"></span> <?php _e('Events', 'tainacan'); ?>&nbsp;<span id="notification_events" style="background-color:red;color:white;font-size:13px;"></span></a></li>
            <?php if (!verify_collection_moderators($collection_post->ID, get_current_user_id()) && !current_user_can('manage_options')): ?>
                <li><a onclick="show_report_abuse_collection('<?php echo $collection_post->ID; ?>');"  style="cursor: pointer;"><span class="glyphicon glyphicon-warning-sign"></span> <?php _e('Report Abuse', 'tainacan'); ?>&nbsp;</a></li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
</li>

</ul>