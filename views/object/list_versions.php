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

            <div class="col-md-12">
                <table id="list_versions_table" class="table table-striped table-bordered" >
                    <thead>
                        <tr>
                            <th><?php _e('Title', 'tainacan'); ?></th>
                            <th><?php _e('Version', 'tainacan'); ?></th>
                            <th><?php _e('Date', 'tainacan'); ?></th>
                            <th><?php _e('User', 'tainacan'); ?></th>
                            <th><?php _e('Note', 'tainacan'); ?></th>
                            <th><?php _e('Action', 'tainacan'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="table_events_verified" >
                        <?php foreach ($versions as $version) { ?>
                            <tr>
                                <td>
                                    <?php if ($id_active != $version['ID']) { ?>
                                        <a href="#" onclick="showSingleObjectVersion('<?php echo $version['ID']; ?>', '<?php echo get_template_directory_uri(); ?>')">
                                        <?php } else { ?>
                                            <a href="#">
                                            <?php } ?>
                                            <?php echo $version['title']; ?>
                                        </a>
                                    <!--a><?php echo $version['title']; ?></a-->
                                </td>
                                <td>
                                    <?php echo $version['version']; ?>
                                </td>
                                <td>
                                    <?php echo date('d/m/Y H:i', strtotime($version['data'])); ?>
                                </td>
                                <td>
                                    <?php echo $version['user']; ?>
                                </td>
                                <td>
                                    <?php
                                    if (empty($version['note'])) {
                                        echo '-';
                                    } else {
                                        echo $version['note'];
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($id_active != $version['ID']) { ?>
                                        <?php if ((verify_collection_moderators($collection_id, get_current_user_id()) || current_user_can('manage_options')) && get_post_type($collection_id) == 'socialdb_collection'): ?>
                                            <ul class="item-funcs" style="float: left !important;">
                                                <?php if ($version['ID'] != $original) { ?>
                                                    <li>
                                                        <a onclick="delete_version('<?php echo $version['ID'] ?>', '<?php _e('Are you sure?', 'tainacan'); ?>', '<?php _e('This operation is not possible reverse.', 'tainacan'); ?>');" href="#">
                                                            <span class="glyphicon glyphicon-trash"></span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <li>
                                                    <a onclick="restore_version('<?php echo $id_active ?>', '<?php echo $version['ID'] ?>', '<?php _e('Are you sure?', 'tainacan'); ?>', '<?php _e('Are you want to restore this item?', 'tainacan'); ?>');" href="#">
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
                                </td>

                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
