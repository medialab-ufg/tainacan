<?php
/*
 * View responsavel em mostrar um objeto especifico
 *
 */
include_once('./../../helpers/view_helper.php');
include_once ('js/list_single_version_js.php');

$create_perm_object = verify_allowed_action($collection_id, 'socialdb_collection_permission_create_property_object');
$edit_perm_object = verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_object');
$delete_perm_object = verify_allowed_action($collection_id, 'socialbd_collection_permission_delete_property_object');
$create_perm_data = verify_allowed_action($collection_id, 'socialdb_collection_permission_create_property_data');
$edit_perm_data = verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_data');
$delete_perm_data = verify_allowed_action($collection_id, 'socialdb_collection_permission_delete_property_data');

$meta_type = ucwords($metas['socialdb_object_dc_type'][0]);
$meta_source = $metas['socialdb_object_dc_source'][0];
?>
<input type="hidden" name="single_object_id" id="single_object_id" value="<?php echo $object->ID; ?>" >
<input type="hidden" id="single_name" name="item_single_name" value="<?php echo $object->post_name; ?>" />
<input type="hidden" id="socialdb_permalink_object" name="socialdb_permalink_object" value="<?php echo get_the_permalink($collection_id) . '?item=' . $object->post_name; ?>" />

<ol class="breadcrumb item-breadcrumbs">
    <li> <a href="<?php echo site_url(); ?>"> Home </a> </li>
    <li> <a href="#" onclick="backToMainPageSingleItem()"> <?php echo get_post($collection_id)->post_title; ?> </a> </li>
    <li class="active"> <?php echo $object->post_title; ?> </li>

    <button data-title="<?php printf(__("URL of %s", "tainacan"), $object->post_title); ?>" id="iframebuttonObject" data-container="body"
            class="btn bt-default content-back pull-right" data-toggle="popoverObject" data-placement="left" data-content="">
        <span class="glyphicon glyphicon-link"></span>
    </button>
</ol>
<div class="col-md-12" style="background-color: #ffff99;"><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;<?php _e('This is not the active version.','tainacan');?></div>
<div id="single_item_tainacan" class="col-md-12">
    <div class="col-md-9 item-main-data row" style="padding-right: 0;">

        <div class="col-md-12 content-title single-item-title tainacan-header-info">
            <div class="col-md-10">
                <h3 id="text_title"><?php echo $object->post_title; ?></h3>
                <span id="event_title" style="display:none;">
                    <input type="text" value="<?php echo $object->post_title; ?>" id="title_field" class="form-control">
                </span>
                <small>
                    <?php if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_data_value', $object_id)): ?>
                        <button type="button" alt="<?php _e('Cancel modification', 'tainacan') ?>" onclick="cancel_title()" id="cancel_title" class="btn btn-default btn-xs" style="display: none;" >
                            <span class="glyphicon glyphicon-arrow-left" ></span>
                        </button>
                        <button type="button" onclick="edit_title()" id="edit_title" class="btn btn-default btn-xs">
                            <span class="glyphicon glyphicon-edit"></span>
                            <?php // viewHelper::render_icon("edit_object"); ?>
                        </button>
                        <button type="button" onclick="save_title('<?php echo $object->ID ?>')" id="save_title" class="btn btn-default btn-xs" style="display: none;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                    <?php endif; ?>
                </small>

            </div>

            <div class="col-md-2 right no-padding">
                <ul class="item-funcs">
                    <?php
                    if ($collection_metas == 'allowed' || ($collection_metas == 'moderate' && is_user_logged_in()) || ($collection_metas == 'controlled' && ($is_moderator || $object->post_author == get_current_user_id()))) {
                        if ($metas['socialdb_object_dc_type'][0] == 'image') {
                            $url_image = wp_get_attachment_url(get_post_thumbnail_id($object->ID, 'full'));
                            $thumbail_id = get_post_thumbnail_id($object->ID, 'full');
                            ?>
                            <li>
                                <a href="<?php echo $url_image; ?>" download="<?php echo $object->post_title; ?>.jpg" onclick="downloadItem('<?php echo $thumbail_id; ?>');">
                                    <span class="glyphicon glyphicon-download"></span>
                                </a>
                            </li>
                            <?php
                        }
                    }
                    ?>
                    <?php if ($is_moderator || $object->post_author == get_current_user_id()): ?>
                        <!--li>
                            <a onclick="single_delete_object('<?= __('Delete Object', 'tainacan') ?>', '<?= __('Are you sure to remove the object: ', 'tainacan') . $object->post_title ?>', '<?php echo $object->ID ?>', '<?= mktime() ?>')" href="#" class="remove">
                                <span class="glyphicon glyphicon-trash"></span>
                            </a>
                        </li>
                        <li>
                            <a href="#" onclick="show_edit_object('<?php echo $object->ID ?>')" class="edit">
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>
                        </li-->
                        <?php
                    else:
                        // verifico se eh oferecido a possibilidade de remocao do objeto vindulado
                        if (verify_allowed_action($collection_id, 'socialdb_collection_permission_delete_object')):
                            ?>
                            <!--li>
                                <a onclick="single_show_report_abuse('<?php echo $object->ID ?>')" href="#" class="report_abuse">
                                    <span class="glyphicon glyphicon-warning-sign"></span>
                                </a>
                            </li-->
                        <?php endif; ?>
                        <!-- modal exluir -->
                        <div class="modal fade" id="single_modal_delete_object<?php echo $object->ID ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Report Abuse', 'tainacan'); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <?php echo __('Describe why the object: ', 'tainacan') . get_the_title() . __(' is abusive: ', 'tainacan'); ?>
                                        <textarea id="observation_delete_object<?php echo $object->ID ?>" class="form-control"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                                        <button onclick="single_report_abuse_object('<?= __('Delete Object') ?>', '<?= __('Are you sure to remove the object: ', 'tainacan') . get_the_title() ?>', '<?php echo $object->ID ?>', '<?= mktime() ?>')" type="button" class="btn btn-primary"><?php echo __('Delete', 'tainacan'); ?></button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <li>
                        <a onclick="single_show_item_versions('<?php echo $object->ID ?>')" href="#">
                            <span class="glyphicon glyphicon-folder-open"></span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-md-12">
                <hr class="single-item-divider" />
            </div>

            <div class="col-md-6"> <b> <?php echo __('Sent by: ', 'tainacan'); ?> </b> <?php echo get_the_author_meta("display_name", $object->post_author); ?> </div>
            <div class="col-md-6" style="text-align: right"> <span> <b> <?php _e('Sent date: ', 'tainacan'); ?> </b>  <?php echo get_the_date('d/m/y', $object->ID); ?> </span> </div>

            <div class="col-md-12" style="padding-bottom: 20px;">
                <div class="content-wrapper" <?php if (has_action('home_item_content_div')) do_action('home_item_content_div') ?> style="padding: 0; margin-top: 10px;">
                    <div>
                        <?php
                        if ($metas['socialdb_object_dc_type'][0] == 'text') {
                            echo $metas['socialdb_object_content'][0];
                        } else {
                            if ($metas['socialdb_object_from'][0] == 'internal') {
                                $url = wp_get_attachment_url($metas['socialdb_object_content'][0]);
                                switch ($metas['socialdb_object_dc_type'][0]) {
                                    case 'audio':
                                        $content = '<audio controls><source src="' . $url . '">' . __('Your browser does not support the audio element.', 'tainacan') . '</audio>';
                                        break;
                                    case 'image':
                                        //echo '<div id="watermark_div" style="background:url('.$url_watermark.') no-repeat; z-index:99;"></div>';
                                        if (get_the_post_thumbnail($object->ID, 'thumbnail')) {
                                            $url_image = wp_get_attachment_url(get_post_thumbnail_id($object->ID));
                                            //$content = '<center><a href="#" onclick="$.prettyPhoto.open([\'' . $url_image . '\'], [\'\'], [\'\']);return false">
                                            //            <img style="max-width:480px;" src="' . $url_image . '" class="img-responsive" />
                                            //        </a></center>';
                                            $style_watermark = ($has_watermark ? 'style="background:url(' . $url_watermark . ') no-repeat center; background-size: contain;"' : '');
                                            $opacity_watermark = ($has_watermark ? 'opacity: 0.80;' : '');
                                            $content = '<center ' . $style_watermark . '>'
                                                    . '<img style="max-width:480px; ' . $opacity_watermark . '" src="' . $url_image . '" class="img-responsive" />'
                                                    . '</center>';
                                        }
                                        break;
                                    case 'video':
                                        $content = '<video width="400" controls><source src="' . $url . '">' . __('Your browser does not support HTML5 video.', 'tainacan') . '</video>';
                                        break;
                                    case 'pdf':
                                        $content = '<embed src="' . $url . '" width="600" height="500" alt="pdf" pluginspage="http://www.adobe.com/products/acrobat/readstep2.html">';
                                        break;
                                    default:
                                        $content = '<p style="text-align:center;">' . __('File link:') . ' <a target="_blank" href="' . $url . '">' . __('Click here!', 'tainacan') . '</a></p>';
                                        break;
                                }
                            } else {
                                switch ($metas['socialdb_object_dc_type'][0]) {
                                    case 'audio':
                                        $content = '<audio controls><source src="' . $metas['socialdb_object_content'][0] . '">' . __('Your browser does not support the audio element.', 'tainacan') . '</audio>';
                                        break;
                                    case 'image':
                                        $style_watermark = ($has_watermark ? 'style="background:url(' . $url_watermark . ') no-repeat center; background-size: contain;"' : '');
                                        $opacity_watermark = ($has_watermark ? 'opacity: 0.80;' : '');
                                        if (get_the_post_thumbnail($object->ID, 'thumbnail')) {
                                            $url_image = wp_get_attachment_url(get_post_thumbnail_id($object->ID, 'large'));
                                            $content = '<center ' . $style_watermark . '><img style="max-width:480px; ' . $opacity_watermark . '"  src="' . $url_image . '" class="img-responsive" /></center>';
//                                            $content = '<center><a href="#" onclick="$.prettyPhoto.open([\'' . $url_image . '\'], [\'\'], [\'\']);return false">
//                                                        <img style="max-width:880px;"  src="' . $url_image . '" class="img-responsive" />
//                                                    </a></center>';
                                        } else {
                                            $content = "<img src='" . $metas['socialdb_object_content'][0] . "' class='img-responsive' />";
                                        }
                                        break;
                                    case 'video':
                                        if (strpos($metas['socialdb_object_content'][0], 'youtube') !== false) {
                                            $step1 = explode('v=', $metas['socialdb_object_content'][0]);
                                            $step2 = explode('&', $step1[1]);
                                            $video_id = $step2[0];
                                            $content = "<div style='height:600px; display: flex !important;'  ><iframe  class='embed-responsive-item' src='http://www.youtube.com/embed/" . $video_id . "?html5=1' allowfullscreen frameborder='0'></iframe></div>";
                                        } elseif (strpos($metas['socialdb_object_content'][0], 'vimeo') !== false) {
                                            $step1 = explode('/', rtrim($metas['socialdb_object_content'][0], '/'));
                                            $video_id = end($step1);
                                            $content = "<div class=\"embed-responsive embed-responsive-16by9\"><iframe class='embed-responsive-item' src='https://player.vimeo.com/video/" . $video_id . "' frameborder='0'></iframe></div>";
                                        } else {
                                            $content = "<div class=\"embed-responsive embed-responsive-16by9\"><iframe class='embed-responsive-item' src='" . $metas['socialdb_object_content'][0] . "' frameborder='0'></iframe></div>";
                                        }
                                        break;
                                    case 'pdf':
                                        $content = '<embed src="' . $metas['socialdb_object_content'][0] . '" width="600" height="500" alt="pdf" pluginspage="http://www.adobe.com/products/acrobat/readstep2.html">';
                                        break;
                                    default:
                                        $content = '<p style="text-align:center;">' . __('File link:', 'tainacan') . ' <a target="_blank" href="' . $metas['socialdb_object_content'][0] . '">' . __('Click here!', 'tainacan') . '</a></p>';
                                        break;
                                }
                            }
                            echo $content;
                        }
                        ?>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-12 content-title single-item-title tainacan-fixed-metas">

            <div class="col-md-12 item-fixed-data no-padding">
                <div class="col-md-6 left-container no-padding">
                    <div class="item-source box-item-paddings" style="border-top:none">
                        <div class="row" <?php if (has_action('home_item_source_div')) do_action('home_item_source_div') ?> style="padding-left: 30px;">
                            <div class="col-md-12 no-padding">
                                <h4 class="title-pipe single-title"> <?php _e('Source', 'tainacan'); ?></h4>
                                <div class="edit-field-btn">
                                    <?php
                                    // verifico se o metadado pode ser alterado
                                    if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_data_value', $object_id)):
                                        ?>
                                        <small>
                                            <button type="button" onclick="cancel_source()" id="cancel_source" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
                                            <button type="button" onclick="edit_source()" id="edit_source" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
                                            <button type="button" onclick="save_source('<?php echo $object->ID ?>')" id="save_source"class="btn btn-default btn-xs" style="display: none;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <div id="text_source">
                                    <?php echo format_item_text_source($meta_source); ?>
                                </div>
                                <div id="event_source" style="display:none;" >
                                    <input type="text" class="form-control" id="source_field" value="<?php echo $meta_source; ?>"
                                           name="source_field" placeholder="<?php _e('Type the source and click save!') ?>" >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item-type box-item-paddings" style="border-top: none">
                        <div class="row" <?php if (has_action('home_item_type_div')) do_action('home_item_type_div') ?> style="padding-left: 30px;">
                            <div class="col-md-12 no-padding">
                                <h4 class="title-pipe single-title"> <?php _e('Type', 'tainacan'); ?></h4>
                                <div class="edit-field-btn">
                                    <?php
                                    // verifico se o metadado pode ser alterado
                                    if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_data_value', $object_id)):
                                        ?>
                                        <small>
                                            <button type="button" onclick="cancel_type()" id="cancel_type" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
                                            <button type="button" onclick="edit_type()" id="edit_type" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
                                            <button type="button" onclick="save_type('<?php echo $object->ID ?>')" id="save_type"class="btn btn-default btn-xs" style="display: none;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <div id="text_type">
                                    <?php _e($meta_type, 'tainacan') ?>
                                </div>
                                <div id="event_type" style="display:none;" >
                                    <input type="radio" value="text"
                                           <?php echo ($meta_type == 'Text') ? 'checked="checked"' : '' ?> name="type_field" ><?php _e('Text', 'tainacan') ?><br>
                                    <input type="radio" value="image"
                                           <?php echo ($meta_type == 'Image') ? 'checked="checked"' : '' ?> name="type_field" ><?php _e('Image', 'tainacan') ?><br>
                                    <input type="radio" value="audio"
                                           <?php echo ($meta_type == 'Audio') ? 'checked="checked"' : '' ?> name="type_field" ><?php _e('Audio', 'tainacan') ?><br>
                                    <input type="radio" value="video"
                                           <?php echo ($meta_type == 'Video') ? 'checked="checked"' : '' ?> name="type_field" ><?php _e('Video', 'tainacan') ?><br>
                                    <input type="radio" value="pdf"
                                           <?php echo ($meta_type == 'Pdf') ? 'checked="checked"' : '' ?> name="type_field" ><?php _e('PDF', 'tainacan') ?><br>
                                    <input type="radio" value="other"
                                           <?php echo ($meta_type == 'Other') ? 'checked="checked"' : '' ?> name="type_field" ><?php _e('Other', 'tainacan') ?><br>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item-thumb box-item-paddings" style="border-top: none; border-bottom: none">
                        <div class="content-thumb" style="padding-left: 15px; ">
                            <h4 class="title-pipe single-title"> <?php _e('Thumbnail', 'tainacan'); ?></h4>
                            <div class="edit-field-btn">
                                <?php
                                // Evento para alteracao do thumbnail de um item
                                // verifico se o metadado pode ser alterado
                                if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_data_value', $object_id)):
                                    ?>
                                    <div style="margin-top: 5px;">
                                        <button type="button" onclick="edit_thumbnail()" id="edit_thumbnail" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?php
                                if (get_the_post_thumbnail($object->ID, 'thumbnail')) {
                                    $url_image = wp_get_attachment_url(get_post_thumbnail_id($object->ID));
                                    ?>
                                                                <!--a href="#" onclick="$.prettyPhoto.open(['<?php echo $url_image; ?>'], [''], ['']);
                                                                            return false">
                                                                    <!-- <img src="< ?php echo $url_image; ?>" class="img-responsive" /> -->
                                    <?php echo get_the_post_thumbnail($object->ID, 'thumbnail'); ?>
                                    <!--/a-->
                                <?php } else { ?>
                                    <img class="img-responsive" src="<?php echo get_item_thumbnail_default($object->ID); ?>" width="45%" />
                                <?php } ?>
                            </div>


                        </div>
                    </div>
                </div>
                <div class="col-md-6 right-container" style="margin-left: 0px;">
                    <div class="item-ranking box-item-paddings box-item-right" style="border-top:none">
                        <h4 class="title-pipe single-title"> <?php _e('Ranking', 'tainacan'); ?></h4>
                        <div id="single_list_ranking_<?php echo $object->ID; ?>" class="row"></div>
                    </div>
                    <div class="item-share box-item-paddings box-item-right">
                        <h4 class="title-pipe single-title"> <?php _e('Sharing', 'tainacan'); ?></h4>
                        <div class="content-redesocial-NO" style="width: 100%">
                            <a class="fb" target="_blank" href="http://www.facebook.com/sharer/sharer.php?s=100&amp;p[url]=<?php echo get_the_permalink($collection_id) . '?item=' . $object->post_name; ?>&amp;p[images][0]=<?php echo wp_get_attachment_url(get_post_thumbnail_id($object->ID)); ?>&amp;p[title]=<?php echo htmlentities($object->post_title); ?>&amp;p[summary]=<?php echo strip_tags($object->post_content); ?>">
                                <img src="<?php echo get_template_directory_uri() . '/libraries/images/icons/icon-facebook.png'; ?>" />
                            </a>
                            <a class="twitter" target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo get_the_permalink($collection_id) . '?item=' . $object->post_name; ?>&amp;text=<?php echo htmlentities($object->post_title); ?>&amp;via=socialdb">
                                <img src="<?php echo get_template_directory_uri() . '/libraries/images/icons/icon-twitter.png'; ?>" />
                            </a>
                            <a class="gplus" target="_blank" href="https://plus.google.com/share?url=<?php echo get_the_permalink($collection_id) . '?item=' . $object->post_name; ?>">
                                <img src="<?php echo get_template_directory_uri() . '/libraries/images/icons/icon-googleplus.png'; ?>" />
                            </a>
                            <a href="#" class="data-share dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" >
                                <!-- <div style="font-size:1em; cursor:pointer; color: black; display: inline-block;" data-icon="&#xe00b;"></div> -->
                                <img src="<?php echo get_template_directory_uri() . '/libraries/images/icons/icon-share.png'; ?>" />
                            </a>
                            <ul style=" z-index: 9999;" class="dropdown-menu" role="menu">
                                <li>
                                    <a target="_blank" href="<?php echo get_the_permalink($collection_id) . '?item=' . $object->post_name; ?>.rdf"  ><span class="glyphicon glyphicon-upload"></span> <?php _e('RDF', 'tainacan'); ?>&nbsp;
                                    </a>
                                </li>
                                <?php if (is_restful_active()): ?>
                                    <li>
                                        <a href="<?php echo site_url() . '/wp-json/posts/' . $object->ID . '/?type=socialdb_object' ?>"  ><span class="glyphicon glyphicon-upload"></span> <?php _e('JSON', 'tainacan'); ?>&nbsp;
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <a onclick="showGraph('<?php echo get_the_permalink($collection_id) . '?item=' . $object->post_name; ?>.rdf')"  style="cursor: pointer;"   >
                                        <span class="glyphicon glyphicon-upload"></span> <?php _e('Graph', 'tainacan'); ?>&nbsp;
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-12 item-metadata no-padding">

            <div class="item-description box-item-paddings">
                <h4 class="title-pipe single-title"> <?php _e('Description', 'tainacan'); ?></h4>
                <div class="edit-field-btn">
                    <?php
                    // verifico se o metadado pode ser alterado
                    if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_data_value', $object_id)):
                        ?>
                        <small>
                            <button type="button" onclick="cancel_description()" id="cancel_description" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
                            <button type="button" onclick="edit_description()" id="edit_description" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
                            <button type="button" onclick="save_description('<?php echo $object->ID ?>')" id="save_description"class="btn btn-default btn-xs" style="display: none;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                        </small>
                    <?php endif; ?>
                </div>

                <div id="text_description">
                    <div style="white-space: pre-wrap;"><?php echo $object->post_content; ?></div>
                </div>
                <div id="event_description" style="display:none; min-height: 150px;">
                    <textarea class="col-md-12 form-control" id="description_field" style="width:100%; min-height: 150px;"><?php echo $object->post_content; ?></textarea>
                </div>
            </div>

            <div class="col-md-6 left-container" style="border-right: 3px solid #e8e8e8">
                <!-- Licencas do item -->
                <div class="box-item-paddings item-license" <?php if (has_action('home_item_license_div')) do_action('home_item_license_div') ?> style="border: none;">
                    <h4 class="title-pipe single-title"> <?php _e('License', 'tainacan'); ?></h4>
                    <div class="edit-field-btn">
                        <?php
                        // verifico se o metadado pode ser alterado
                        if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_property_data_value', $object_id)):
                            ?>
                            <small>
                                <button type="button" onclick="cancel_license()" id="cancel_license" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
                                <button type="button" onclick="edit_license()" id="edit_license" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
                                <button type="button" onclick="save_license('<?php echo $object->ID ?>')" id="save_license"class="btn btn-default btn-xs" style="display: none;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                            </small>
                        <?php endif; ?>
                    </DIV>
                    <div id="text_license">
                        <p><?php
                            if (isset(get_post($metas['socialdb_license_id'][0])->post_title))
                                echo get_post($metas['socialdb_license_id'][0])->post_title;
                            else
                                echo __('No license registered for this item', 'tainacan');
                            ?></p>
                    </div>
                    <div id="event_license" style="display: none;">
                    </div>
                </div>
            </div>

            <div class="col-md-6 right-container no-padding">
                <!-- Tags -->
                <div class="box-item-paddings item-tags" style="" <?php if (has_action('home_item_tag_div')) do_action('home_item_tag_div') ?>>
                    <h4 class="title-pipe single-title"> <?php _e('Tags', 'tainacan'); ?></h4>
                    <div class="edit-field-btn">
                        <?php
                        // verifico se o metadado pode ser alterado
                        if (verify_allowed_action($collection_id, 'socialdb_collection_permission_edit_tag', $object->ID)):
                            ?>
                            <button type="button" onclick="cancel_tag()" id="cancel_tag" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
                            <button type="button" onclick="edit_tag()" id="edit_tag" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
                            <button type="button" onclick="save_tag('<?php echo $object->ID ?>')" id="save_tag" class="btn btn-default btn-xs" style="display: none;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                        <?php endif; ?>
                    </div>

                    </h4>
                    <div>
                        <input type="hidden" value="<?php echo $object->ID ?>" class="object_id">
                        <center><button id="single_show_classificiations_<?php echo $object->ID; ?>" onclick="show_classifications_single('<?php echo $object->ID; ?>')" class="btn btn-default btn-lg"><?php _e('Show classifications', 'tainacan'); ?></button></center>
                        <div id="single_classifications_<?php echo $object->ID ?>">
                        </div>
                        <div id="event_tag" style="display:none;">
                            <input type="text" style="width:50%;" class="form-control col-md-6" id="event_tag_field"  placeholder="<?php _e('Type the tag name', 'tainacan') ?>">
                        </div>
                        <script>
                            $('#single_show_classificiations_<?php echo $object->ID ?>').hide();
                            $('#single_show_classificiations_<?php echo $object->ID ?>').trigger('click');
                        </script>
                    </div>
                </div>
            </div>

            <div class="col-md-12 all-metadata no-padding">
                <!-- Metadados do item -->
                <div>
                    <div class="meta-header" style="padding: 10px 20px 10px 20px">
                        <h4 class="title-pipe single-title"> <?php _e('Properties', 'tainacan') ?></h4>
                        <div <?php do_action('home_item_add_property') ?> class="btn-group edit-field-btn">
                            <?php if ($create_perm_object || $create_perm_data): ?>
                                <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" id="btnGroupVerticalDrop1" style="font-size:11px;">
                                    <span class="glyphicon glyphicon-plus grayleft" ></span> <span class="caret"></span>
                                </button>
                                <ul aria-labelledby="btnGroupVerticalDrop1" role="menu" class="dropdown-menu add-metadata">
                                    <?php if ($create_perm_data): ?>
                                        <li>&nbsp;<span class="glyphicon glyphicon-th-list graydrop"></span>&nbsp;<span><a class="add_property_data" onclick="show_form_data_property_single('<?php echo $object->ID ?>')" href="#property_form_<?php echo $object->ID ?>"><?php _e('Add new data property', 'tainacan'); ?></a></span></li>
                                    <?php endif; ?>
                                    <?php if ($create_perm_object): ?>
                                        <li>&nbsp;<span class="glyphicon glyphicon-th-list graydrop"></span>&nbsp;<span><a class="add_property_object" onclick="show_form_object_property_single('<?php echo $object->ID ?>')" href="#property_form_<?php echo $object->ID ?>"><?php _e('Add new object property', 'tainacan'); ?></a></span></li>
                                    <?php endif; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                        <div <?php do_action('home_item_delete_property') ?> class="btn-group edit-field-btn">
                            <?php if ($edit_perm_object || $delete_perm_object || $edit_perm_data || $delete_perm_data): ?>
                                <button onclick="list_properties_edit_remove_single($('#single_object_id').val())"  data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" id="btnGroupVerticalDrop2" style="font-size:11px;">
                                    <span class="glyphicon glyphicon-pencil grayleft"></span>
                                    <span class="caret"></span>
                                </button>
                                <ul id="single_list_properties_edit_remove" aria-labelledby="btnGroupVerticalDrop1" role="menu" class="dropdown-menu">
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <div id="single_list_all_properties_<?php echo $object->ID ?>" class="single_list_properties"> </div>
                        <div id="single_data_property_form_<?php echo $object->ID ?>"></div>
                        <div id="single_object_property_form_<?php echo $object->ID ?>"></div>
                        <div id="single_edit_data_property_form_<?php echo $object->ID ?>"></div>
                        <div id="single_edit_object_property_form_<?php echo $object->ID ?>"></div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-12 item-comments no-padding">
            <div>
                <div id="comments_object"></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 item-attachments">
        <div <?php if (has_action('home_item_attachments_div')) do_action('home_item_attachments_div') ?> >
            <div id="single_list_files_<?php echo $object->ID ?>"></div>
        </div>
    </div>
</div>

<div class="container-fluid">
</div>

<div id="container_three_columns" class="container-fluid white-background">
    <div class="row">
        <div class="col-md-2">
            <div class="row">
            </div>
            <div class="row same-height">
                <input type="hidden" class="post_id" name="post_id" value="<?= $object->ID ?>">
            </div>

        </div>

        <!-- TAINACAN: esta div agrupa a listagem de itens ,submissao de novos itens e ordencao -->
        <div id="div_central" <?php
        if (has_action('home_item_attachments_div'))
            echo 'class="col-md-10"';
        else
            echo 'class="col-md-8"'
            ?> >
                 <?php
//Acao que permite que os modulos insiram novas divs para o item
                 do_action('home_item_insert_container', $object->ID)
                 ?>
        </div>
    </div>
</div>

<!-- Modal para upload de thumbnail -->
<div class="modal fade" id="single_modal_thumbnail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formThumbnail">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Select a image', 'tainacan'); ?></h4>
                </div>
                <div class="modal-body">
                    <input type="file" class="form-control" id="thumbnail_field" name="attachment" >
                    <input type="hidden" name="operation" value="insert_attachment">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo __('Alter Image', 'tainacan'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>