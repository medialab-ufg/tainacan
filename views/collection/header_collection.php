<?php
include_once ('js/header_js.php');
//$post = get_post($collection_id);
global $config;
$options = get_option('socialdb_theme_options');
$current_collection_id = $collection_post->ID;
$collection_thumb = get_post_meta($current_collection_id, "_thumbnail_id", true);
$collection_img_id = get_post_meta($current_collection_id, 'socialdb_collection_cover_id', true);
$_enable_header_ = get_post_meta($current_collection_id, 'socialdb_collection_show_header', true);

$thumb_url = $collection_thumb ? wp_get_attachment_url($collection_thumb) : get_template_directory_uri() . "/libraries/images/colecao_thumb.svg";
?>

<div class='headers_container'>
	<?php
	if( empty($_enable_header_) || $_enable_header_ == "enabled") {
		$url_image = wp_get_attachment_url(get_post_meta($current_collection_id, 'socialdb_collection_cover_id', true));
		?>
        <div class="panel-heading collection_header container-fluid collection_header_img"
             style="<?php if ($url_image) { ?> background-image: url(<?php echo $url_image; ?>); <?php } ?>">
            <div class="row">
                <!-- TAINACAN: container com o menu da colecao, link para eventos e a busca de items -->
                <div class="col-md-12">
                    <div class="col-md-10">
                        <div class="row same-height">
                            <div class="col-md-2">
                                <div class="relative">
									<?php if ((verify_collection_moderators($current_collection_id, get_current_user_id()) || current_user_can('manage_options')) && get_post_type($current_collection_id) == 'socialdb_collection'): ?>
                                        <div class="avatar-edit"
                                             onclick="showCollectionConfiguration_editImages('<?php echo get_template_directory_uri() ?>', '1');">
                                            <span class="glyphicon glyphicon-picture show-edit"></span>
                                            <span><?php _e('Change image', 'tainacan'); ?></span>
                                        </div>
									<?php endif; ?>
                                    <a href="<?php echo get_the_permalink($current_collection_id); ?>" class="collection-thumb">
                                        <img src="<?php echo $thumb_url ?>" class="attachment-thumbnail wp-post-image img-responsive"/>
                                    </a>
                                </div>
                            </div>
                            <!-- TAINACAN: div com o titulo e a descricao -->
                            <div class="col-md-12 titulo-colecao">
								<?php if (isset($mycollections) && $mycollections == 'true') { ?>
                                    <span class="bottom"><b class="white"><?php _e('My Collections', 'tainacan'); ?></b><br></span>
								<?php } else if (isset($sharedcollections) && $sharedcollections == 'true') { ?>
                                    <h3 class="white title"><?php _e('Shared Collections', 'tainacan'); ?></h3>
								<?php } else { ?>
                                    <h3 class="white title"> <a href="<?php echo get_the_permalink($current_collection_id); ?>">
											<?php echo $collection_post->post_title; ?> </a>
                                    </h3>
                                    <div class="collection-description">
										<?php echo Words(strip_tags($collection_post->post_content), 450, '... <a onclick="showFullDescription()" style="cursor:pointer;">' . _t('View more') . '</a>'); ?>
                                    </div>

                                    <div class="collection-author">
										<?php if (!is_root_category($current_collection_id)) {
											echo '<strong>' . __('Administrator: ', 'tainacan') . '</strong><i>' . get_the_author_meta("display_name", $collection_post->post_author) . '</i>';
										}
										?>
                                    </div>
								<?php } ?>
                            </div>
                        </div>
                    </div>
					<?php include("config_menu.php"); ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="col-md-12 bg-edit">
								<?php if ((verify_collection_moderators($collection_post->ID, get_current_user_id()) || current_user_can('manage_options')) && get_post_type($collection_post->ID) == 'socialdb_collection'): ?>
                                    <button
                                            onclick="showCollectionConfiguration_editImages('<?php echo get_template_directory_uri() ?>', '2');"
                                            class="btn btn-default">
                                    <span
                                            class="glyphicon glyphicon-picture"></span><span><?php _e('Change Cover', 'tainacan'); ?></span>
                                    </button>
								<?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6 pull-right sharings-container no-padding">
                            <?php
                                require_once( "share_buttons.php" );
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- modal exluir -->
            <div class="modal fade" id="modal_delete_collection<?php echo $collection_post->ID; ?>" tabindex="-1"
                 role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form>
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Report Abuse', 'tainacan'); ?>
                                </h4>
                            </div>
                            <div
                                    class="modal-body"><?php echo __('Describe why the collection: ', 'tainacan') . $collection_post->post_title . __(' is abusive: ', 'tainacan'); ?>
                                <textarea id="observation_delete_collection<?php echo $collection_post->ID ?>"
                                          class="form-control"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">
									<?php _t('Close', 1); ?>
                                </button>
                                <button
                                        onclick="report_abuse_collection('<?php _e('Delete Collection', 'tainacan') ?>', '<?php _e('Are you sure to remove the collection: ', 'tainacan') . $collection_post->post_title ?>', '<?php echo $collection_post->ID ?>', '<?php echo time() ?>', '<?php echo get_option('collection_root_id') ?>')"
                                        type="button" class="btn btn-primary"><?php _t('Delete', 1); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}
	$root_category_cover_id = get_option('socialdb_logo');
	$cover_url = wp_get_attachment_url(get_post_meta($root_category_cover_id, 'socialdb_respository_cover_id', true));
	$_curr_user_ = ['is_logged' => is_user_logged_in(), 'is_subscriber' =>  current_user_can('subscriber'), 'is_admin' => current_user_can('manage_options') ];

	//    if( $_curr_user_['is_logged'] && $_curr_user_['is_admin'] ) {
	//        $_current_menu = "menu-ibram";
	//    } else if ( !$_curr_user_['is_logged'] || $_curr_user_['is_subscriber']) {
	//        $_current_menu = "menu-ibram-visitor";
	//    }

	if( $_curr_user_['is_logged']) {
		$_current_menu = "menu-ibram";
	} else if ( !$_curr_user_['is_logged']) {
		$_current_menu = "menu-ibram-visitor";
	}

	if (has_nav_menu($_current_menu)) {
		echo "<input type='hidden' name='ibram_menu_active' class='ibram_menu_active' value='true'>";
		if( empty($_enable_header_) || $_enable_header_ == "enabled") {
			?>
            <div class="ibram-header" <?php if ($root_category_cover_id != "") { ?> style="background-image: url(<?php echo $cover_url ?>)" <?php } ?> >
                <div class="col-md-12 no-padding">
                    <div class="col-md-10 no-padding">
                        <h3> <?php echo bloginfo('name'); ?> </h3>
                        <h5> <?php echo bloginfo('description'); ?> </h5>
                    </div>
					<?php include("config_menu.php"); ?>
                </div>
            </div>

			<?php
		}
		wp_nav_menu([ 'theme_location' => $_current_menu, 'container_class' => 'container', 'container' => false,
		              'depth'=> 3, 'menu_class' => 'navbar navbar-inverse menu-ibram', 'walker' => new wp_bootstrap_navwalker() ]);
	}
	?>
</div>

<div id="tainacan-breadcrumbs" class="config-steps">
    <a href="<?php echo esc_url(home_url('/')) ?>"> Home </a> >
    <a class="tainacan-museum-clear" href="<?= get_the_permalink(get_option('collection_root_id')) /*. '?mycollections=true'*/ ?>"><?php _e('All collections', 'tainacan'); ?></a>
    <span class="tainacan-museum-clear">></span>
    <!--a href="javascript:void(0)" onclick="backToMainPage();"> <span class="collection-title"></span></a> <span class="last-arrow"> </span-->
    <a href="javascript:void(0)" onclick="backRoute($('#slug_collection').val());"> <span class="collection-title"></span></a> <span class="last-arrow"> </span>
    <div class="current-config" style="display: inline-block;"> </div>
</div>

<input type="hidden" class="stat_path" value="<?php echo get_template_directory_uri() ?>">