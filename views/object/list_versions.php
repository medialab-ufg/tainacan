<?php
/*
 * View responsavel em mostrar um objeto especifico
 *
 */
include_once('./../../helpers/view_helper.php');
include_once ('js/list_versions_js.php');
?>
<input type="hidden" name="single_object_id" id="single_object_id" value="<?php echo $object->ID; ?>" >
<input type="hidden" id="single_name" name="item_single_name" value="<?php echo $object->post_name; ?>" />
<input type="hidden" id="socialdb_permalink_object" name="socialdb_permalink_object" value="<?php echo get_the_permalink($collection_id) . '?item=' . $object->post_name; ?>" />

<ol class="breadcrumb item-breadcrumbs">
    <li> <a href="<?php echo get_permalink(get_option('collection_root_id')); ?>"> <?php _e('Repository', 'tainacan') ?> </a> </li>
    <li> <a href="#" onclick="backToMainPageSingleItem()"> <?php echo get_post($collection_id)->post_title; ?> </a> </li>
    <li class="active"> <a href="#" onclick="showSingleObject('<?php echo $object_id; ?>', '<?php echo get_template_directory_uri(); ?>')"> <?php echo $object->post_title; ?> </a> </li>
</ol>

<div id="single_item_versions" class="col-md-12" style="background-color: #FFF;">
    <div class="item-main-data row" style="padding-right: 0; padding-left: 0;">
        <div class="col-md-12 content-title single-item-title tainacan-header-info" style="padding-bottom: 35px;">
            <div class="col-md-12">
                <h3 id="text_title"><?php echo $id_active . $object->post_title . ' - ' . __('Version History', 'tainacan'); ?></h3>
                <hr>
            </div>
            <div class="col-md-3">
                <strong><?php _e('Title', 'tainacan'); ?></strong>
            </div>
            <div class="col-md-2">
                <strong><?php _e('Version', 'tainacan'); ?></strong>
            </div>
            <div class="col-md-2">
                <strong><?php _e('Date', 'tainacan'); ?></strong>
            </div>
            <!--div class="col-md-2">
                <strong><?php _e('Editor', 'tainacan'); ?></strong>
            </div-->
            <div class="col-md-3">
                <strong><?php _e('Note', 'tainacan'); ?></strong>
            </div>
            <div class="col-md-2">
                <strong><?php _e('Action', 'tainacan'); ?></strong>
            </div>
            <?php foreach ($versions as $version) { ?>
                <div class="col-md-3">
                    <a href="<?php echo get_collection_item_href($collection_id); ?>"
                           onclick="<?php get_item_click_event($collection_id,$version['ID']) ?>">
                               <?php echo $version['title']; ?>
                        </a>
                    <!--a><?php echo $version['title']; ?></a-->
                </div>
                <div class="col-md-2">
                    <?php echo $version['version']; ?>
                </div>
                <div class="col-md-2">
                    <?php echo date('d/m/Y H:i', strtotime($version['data'])); ?>
                </div>
                <!--div class="col-md-2">
                    Eu
                </div-->
                <div class="col-md-3">
                    <?php echo $version['note']; ?>
                </div>
                <div class="col-md-2">
                    <?php if ($id_active != $version['ID']) { ?>
                        <?php if ((verify_collection_moderators($collection_id, get_current_user_id()) || current_user_can('manage_options')) && get_post_type($collection_id) == 'socialdb_collection'): ?>
                    <ul class="item-funcs" style="float: left !important;">
                                <li>
                                    <a onclick="delete_version('<?php echo $version['ID'] ?>', '<?php _e('Are you sure?','tainacan'); ?>', '<?php _e('This operation is not possible reverse. If you delete the original item, all versions are deleted.','tainacan'); ?>');" href="#">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </a>
                                </li>
                                <li>
                                    <a onclick="restore_version('<?php echo $id_active ?>','<?php echo $version['ID'] ?>', '<?php _e('Are you sure?','tainacan'); ?>', '<?php _e('Are you want to restore this item?','tainacan'); ?>');" href="#">
                                        <span class="glyphicon glyphicon-repeat"></span>
                                    </a>
                                </li>
                            </ul>
                        <?php else: ?>
                            <?php _e('Not active', 'tainacan'); ?>
                        <?php endif; ?>
                    <?php } else { ?>
                        <?php _e('Active', 'tainacan'); ?>
                    <?php } ?>
                </div>
            <?php } //var_dump ($version_active, $original, $version_numbers, $object); ?>
        </div>
    </div>
</div>
