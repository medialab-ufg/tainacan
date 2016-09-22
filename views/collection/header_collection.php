<?php
include_once ('js/header_js.php');
//$post = get_post($collection_id);
global $config;
$options = get_option('socialdb_theme_options');
$current_collection_id = $collection_post->ID;
$collection_thumb = get_post_meta($current_collection_id, "_thumbnail_id", true);
$collection_img_id = get_post_meta( $current_collection_id, 'socialdb_collection_cover_id', true);

$thumb_url = $collection_thumb ? wp_get_attachment_url($collection_thumb) : get_template_directory_uri() . "/libraries/images/colecao_thumb.svg";
?>
<!-- TAINACAN: panel da colecao, background-color definido pelo o usuario -->
<!--div class="panel-heading" style="max-width: 100%;border-color: <?= $collection_metas['socialdb_collection_board_border_color'] ?>;color:<?= $collection_metas['socialdb_collection_board_font_color'] ?>;background-color: <?= $collection_metas['socialdb_collection_board_background_color'] ?>;"-->
<?php $url_image = wp_get_attachment_url(get_post_meta( $current_collection_id, 'socialdb_collection_cover_id', true)); ?>
<div class="panel-heading collection_header container-fluid collection_header_img" style=" <?php if ($url_image) { ?> background-image: url(<?php echo $url_image; ?>); <?php } ?>">
    <div class="row">
        <!-- TAINACAN: container com o menu da colecao, link para eventos e a busca de items -->
        <div class="col-md-12">
            <div class="col-md-10">
                <div class="row same-height">
                    <div class="col-md-2">
                        <div class="relative">
                            <?php if ((verify_collection_moderators($current_collection_id, get_current_user_id()) || current_user_can('manage_options')) && get_post_type($current_collection_id) == 'socialdb_collection'): ?>
                                <div onclick="showCollectionConfiguration_editImages('<?php echo get_template_directory_uri() ?>', '1');" class="avatar-edit">
                                    <span class="glyphicon glyphicon-picture show-edit" ></span>
                                    <span><?php _e('Change image', 'tainacan'); ?></span>
                                </div>
                            <?php endif; ?>
                            <a href="<?php echo get_the_permalink($current_collection_id); ?>" class="collection-thumb">
                                <img src="<?php echo $thumb_url ?>" class="attachment-thumbnail wp-post-image img-responsive" />
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
                            <h3 class="white title"> <a href="<?php echo get_the_permalink($current_collection_id); ?>"> <?php echo $collection_post->post_title; ?> </a> </h3>
                            <div class="collection-description">
                                <?php echo Words(strip_tags($collection_post->post_content), 450, '... <a onclick="showFullDescription()" style="cursor:pointer;"> Ver mais! </a>'); ?>
                            </div>

                            <div class="collection-author">
                                <?php
                                if ( ! is_root_category($current_collection_id) ) {
                                    echo '<strong>' . __('Administrator: ', 'tainacan') . '</strong><i>' . get_the_author_meta( "display_name", $collection_post->post_author ) .'</i>';
                                }
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php include ("config_menu.php"); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-3">
                    <div class="col-md-12 bg-edit">
                        <?php if ((verify_collection_moderators($collection_post->ID, get_current_user_id()) || current_user_can('manage_options')) && get_post_type($collection_post->ID) == 'socialdb_collection'): ?>
                            <button onclick="showCollectionConfiguration_editImages('<?php echo get_template_directory_uri() ?>', '2');" class="btn btn-default">
                                <span class="glyphicon glyphicon-picture" ></span><span><?php _e('Change Cover', 'tainacan'); ?></span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6 pull-right" style="text-align:right;padding:10px 0;"> <!-- compartilhamentos -->
                    <!-- ******************** TAINACAN: compartilhar colecao (titutlo,imagem e descricao) no FACEBOOK ******************** -->
                    <a target="_blank" href="http://www.facebook.com/sharer/sharer.php?s=100&amp;p[url]=<?php echo get_the_permalink($collection_post->ID); ?>&amp;p[images][0]=<?php echo wp_get_attachment_url(get_post_thumbnail_id($collection_post->ID)); ?>&amp;p[title]=<?php echo htmlentities($collection_post->post_title); ?>&amp;p[summary]=<?php echo strip_tags($collection_post->post_content); ?>">
                        <div class="fab"><span data-icon="&#xe021;"></span></div>
                    </a>

                    <!-- ******************** TAINACAN: compartilhar colecao (titulo,imagem) no GOOGLE PLUS ******************** -->
                    <a target="_blank" href="https://plus.google.com/share?url=<?php echo get_the_permalink($collection_post->ID); ?>">
                        <div class="fab"><span data-icon="&#xe01b;"></span></div>
                    </a>

                    <!-- ************************ TAINACAN: compartilhar colecao  no TWITTER ******************** -->
                    <a target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo get_the_permalink($collection_post->ID); ?>&amp;text=<?php echo htmlentities($collection_post->post_title); ?>&amp;via=socialdb">
                        <div class="fab"><span data-icon="&#xe005;"></span></div>
                    </a>
                    <!-- ******************** TAINACAN: RSS da colecao com seus metadados ******************** -->
                    <?php if (get_option('collection_root_id') != $collection_post->ID): ?>
                        <a target="_blank" href="<?php echo site_url() . '/feed_collection/' . $collection_post->post_name ?>">
                            <div class="fab"><span data-icon="&#xe00c;"></span></div>
                        </a>
                    <?php endif; ?>
                    <!-- ******************** TAINACAN: exportar CSV os items da colecao que estao filtrados ******************** -->
                    <?php if (get_option('collection_root_id') != $collection_post->ID) { ?>
                        <!--a style="cursor: pointer;" onclick="export_selected_objects()">
                            <div class="fab"><small><h6><b>csv</b></h6></small></div>
                        </a-->
                    <?php } ?>
                    <!-- ******************** TAINACAN: IFRAME URL ******************** -->
                    <button id="iframebutton" data-container="body" data-toggle="popover" data-placement="left" data-title="URL Iframe" data-content="" data-original-title="" title="Emded URL">
                      <div class="fab"><small><h6><b><></b></h6></small></div>
                    </button>

                    <script>
                        set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
                    </script>

                    <!--button style="float:right;margin-left:5px;" id="iframebutton" type="button" class="btn btn-default btn-sm" data-container="body" data-toggle="popover" data-placement="left" data-title="URL Iframe" data-content="">
                        <span class="glyphicon glyphicon-link"></span>
                    </button-->
                    <!-- ******************** TAINACAN: se o plugin de restful estiver ativo ***-->
                    <?php if(is_restful_active()): ?>
                        <!--a target="_blank" href="<?php echo site_url() . '/wp-json/posts/' . $collection_post->ID.'/?type=socialdb_collection' ?>">
                           <div class="fab"><small><h6><b>json</b></h6></small></div>
                        </a>
                        <!--a style="cursor: pointer;" onclick="export_selected_objects_json()">
                            <div class="fab"><small><h6><b>items</b></h6></small></div>
                        </a-->
                    <?php endif; ?>
                        <a href="#" id="resources_collection_button" class="dropdown-toggle"  data-toggle="dropdown" role="button" aria-expanded="false" >
                          <div class="fab">
                              <div style="font-size:1em; cursor:pointer;" data-icon="&#xe00b;"></div>
                          </div>
                        </a>
                        <ul id="resources_collection_dropdown" class="dropdown-menu" role="menu">
                             <li>
                                 <a target="_blank" href="<?php echo get_the_permalink($collection_post->ID) ?>?all.rdf"  ><span class="glyphicon glyphicon-upload"></span> <?php _e('RDF', 'tainacan'); ?>&nbsp;
                                 </a>
                             </li>
                             <?php if(is_restful_active()): ?>
                             <li>
                                 <a href="<?php echo site_url() . '/wp-json/posts/' . $collection_post->ID.'/?type=socialdb_collection' ?>"  ><span class="glyphicon glyphicon-upload"></span> <?php _e('JSON', 'tainacan'); ?>&nbsp;
                                 </a>
                             </li>
                             <?php endif; ?>
                             <?php if (get_option('collection_root_id') != $collection_post->ID) { ?>
                             <li>
                                     <a style="cursor: pointer;" onclick="export_selected_objects()"  ><span class="glyphicon glyphicon-upload"></span> <?php _e('CSV', 'tainacan'); ?>&nbsp;
                                     </a>
                            </li>
                            <?php } ?>
                             <li>
                                 <a onclick="showGraph('<?php echo get_the_permalink($collection_post->ID) ?>?all.rdf')"  style="cursor: pointer;"   >
                                     <span class="glyphicon glyphicon-upload"></span> <?php _e('Graph', 'tainacan'); ?>&nbsp;
                                 </a>
                             </li>
                     </ul>
                     <!-- ******************** TAINACAN: Comentarios ******************** -->
                     <a style="cursor: pointer;" onclick="showPageCollectionPage()" >
                        <div class="fab"><span style="font-size: medium;" class="glyphicon glyphicon-comment"></span></div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- TAINACAN: div com o input para pesquisa de items na colecao -->
    <!--div class="col-md-10">
        <div class="input-group">
            <div class="input-group-btn">
                <button onclick="clear_list()"id="clear" class="btn-xs btn-primary btn" style="margin-right:10px;margin-bottom:5px"><?php _e('Clear', 'tainacan') ?></button>
            </div>
            <input onkeyup="set_value(this)" onkeydown="if (event.keyCode === 13)
                        document.getElementById('search_main').click();
                   " type="text" style="font-size: 13px; " class="form-control input-medium placeholder" id="search_objects" placeholder="<?php _e('Search Objects', 'tainacan') ?>">
            <span class="input-group-btn">
                <button id="search_main" type="button" onclick="search_objects('#search_objects')"  class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
            </span>
        </div>
    </div-->
    <!-- modal exluir -->
    <div class="modal fade" id="modal_delete_collection<?php echo $collection_post->ID; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Report Abuse', 'tainacan'); ?></h4>
                    </div>
                    <div class="modal-body"><?php echo __('Describe why the collection: ','tainacan') . $collection_post->post_title . __(' is abusive: ', 'tainacan'); ?>
                        <textarea id="observation_delete_collection<?php echo $collection_post->ID ?>" class="form-control"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                        <button onclick="report_abuse_collection('<?php _e('Delete Collection', 'tainacan') ?>', '<?php _e('Are you sure to remove the collection: ', 'tainacan') . $collection_post->post_title ?>', '<?php echo $collection_post->ID ?>', '<?php echo mktime() ?>', '<?php echo get_option('collection_root_id') ?>')" type="button" class="btn btn-primary"><?php echo __('Delete', 'tainacan'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$root_category_cover_id = get_option('socialdb_logo');
$cover_url = wp_get_attachment_url( get_post_meta( $root_category_cover_id , 'socialdb_respository_cover_id', true));

if ( has_nav_menu('menu-ibram') ) { ?>
    <div class="ibram-header" <?php if($root_category_cover_id != "") { ?> style="background-image: url(<?php echo $cover_url ?>)" <?php } ?> >
        <div class="col-md-12 no-padding">
            <div class="col-md-10 no-padding">
                <h3> <?php echo bloginfo('name'); ?> </h3>
                <h5> <?php echo bloginfo('description'); ?> </h5>
            </div>
            <?php include("config_menu.php"); ?>
        </div>

    </div>

    <?php
    wp_nav_menu( ['theme_location' => 'menu-ibram', 'container_class' => 'containewr', 'container' => false,
        'menu_class' => 'navbar navbar-inverse menu-ibram', 'walker'    => new wp_bootstrap_navwalker() ] );
}
?>

<div id="tainacan-breadcrumbs" class="config-steps">
    <a href="<?php echo esc_url( home_url('/')  ) ?>"> Home </a> >
    <a href="<?= get_the_permalink(get_option('collection_root_id')) . '?mycollections=true' ?>"><?php _e('My collections','tainacan'); ?></a> >
    <a href="javascript:void(0)" onclick="backToMainPage();"> <span class="collection-title"></span></a> <span class="last-arrow"> </span>
    <div class="current-config" style="display: inline-block;"> </div>
</div>
