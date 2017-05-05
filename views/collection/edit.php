<?php
include_once ('js/edit_js.php');
include_once(dirname(__FILE__).'/../../helpers/view_helper.php');
$view_helper = new ViewHelper();
$collection_thumb_id = get_post_meta($collection_post->ID, '_thumbnail_id', true);
$thumb_url = wp_get_attachment_url($collection_thumb_id);
$image_cover_url = wp_get_attachment_url(get_post_meta($collection_post->ID, 'socialdb_collection_cover_id', true));
$_en_header = get_post_meta($collection_post->ID, 'socialdb_collection_show_header', 1);
$_showH = ("disabled" === $_en_header) ? false : true ;
?>
<div class="col-md-12 config-temp-box">

    <?php $view_helper->render_header_config_steps('config') ?>

    <div class="col-md-12 tainacan-config-container">
        <h3>
            <?php _e('Collection Configuration', 'tainacan'); ?>
            <!--button onclick="backToMainPage();" id="btn_back_collection" class="btn btn-default pull-right"><?php _e('Back to collection', 'tainacan') ?></button-->
            <?php ViewHelper::buttonVoltar() ?>
        </h3>
        <hr>

        <form id="submit_form_edit_collection">
            <div class="form-group">
                <label for="collection_name"><?php _e('Collection name', 'tainacan'); ?></label>
                <input type="text" class="form-control" id="collection_name" name="collection_name" required="required" value="<?php echo $collection_post->post_title; ?>">
            </div>

            <!------------------- Descricao-------------------------->
            <div class="form-group">
                <label for="collection_description"> <?php _t('Collection description', 1); ?> </label>
                <textarea class="form-control" rows="4" id="collection_content" name="collection_content" placeholder='<?php _t("Describe your collection in few words", 1); ?>'><?php echo $collection_post->post_content; ?></textarea>
            </div>

            <hr class='tainacanRow' />

            <div class="form-group" style="margin-bottom: 40px">
                <label for="enable_header"> <?php _t('Collection header',1); ?> </label>
                <div class="col-md-12">
                    <input type="checkbox" id="enable_header" name="enable_header"
                        <?php echo ($_showH) ? 'checked="checked"' : '' ?> >
                    <?php _t('Enable',1); ?>

                    <div id="thumb-idea-form" class="form-group enablelize"
                         style="margin-top: 10px; <?php echo ($_showH) ? '' : 'display:none'; ?>">
                        <div class="">
                            <label for="collection_thumbnail" style="display: block;"><?php _e('Collection thumbnail', 'tainacan'); ?></label>
                            <?php if($collection_thumb_id): ?>
                                <img src="<?php echo $thumb_url ?>" alt="" style="display: block; margin-bottom: 5px;" />
                                <label for="remove_thumbnail"><?php _e('Remove Thumbnail', 'tainacan'); ?></label> &nbsp;
                                <input type="checkbox"  id="remove_thumbnail" name="remove_thumbnail" value="true">
                                <?php /* <input type="button" class="btn btn-default edit-collection-tumb" value='<?php _e("Edit thumb","tainacan"); ?>' style="display: block"/> */?>
                            <?php endif; ?>
                        </div>
                        <div style="margin-top: 10px;">
                            <div id="collection_crop_thumb"></div>
                        </div>

                        <?php /* <!--<button onclick="remove_thumbnail('<?php echo $collection_post->ID; ?>')" class="btn btn-default" ><?php _e('Remove thumbnail') ?></button>--><br><br>
                            <label for="remove_thumbnail"><?php _e("Change collection's thumbnail", "tainacan"); ?></label><input type="file" size="50" id="collection_thumbnail" name="collection_thumbnail" class="btn btn-default btn-sm"> */ ?>
                    </div>

                    <div id="socialdb_cover" class="form-group enablelize" style="<?php echo ($_showH) ? '':'display:none'?>">
                        <?php if ($image_cover_url) { ?>
                            <hr />
                            <label for="socialdb_collection_cover"><?php _e('Collection cover', 'tainacan'); ?></label> <br />
                            <img src="<?= $image_cover_url ?>" style='max-height:190px;' />
                            <br /><br />
                            <label for="remove_cover"><?php _e('Remove Cover', 'tainacan'); ?></label>
                            <input type="checkbox"  id="remove_cover" name="remove_cover" value="true">
                            &nbsp;&nbsp;&nbsp;
                            <a href="javascript:void(0)" onclick="show_edit_cover()" class="btn btn-default"> <?php _e('Edit Cover', 'tainacan'); ?>  </a>
                            <br /><br />
                        <?php } ?>

                        <div id="edit_cover_container" <?php echo ($image_cover_url) ? 'class="hideCropBox"' : ''; ?>>
                            <label for="collection_cover_img_id"> <?php _e('Select Collection Cover', 'tainacan'); ?> </label>
                            <a href="javascript:void(0)" data-toggle="tooltip" title="<?php _e('After positioning the image cover as wished, click the green button to crop it.', 'tainacan'); ?>
                        (<?php _t('Minimum width recommended: 1920px',1) ?>)">
                                <span class="glyphicon glyphicon-question-sign"></span>
                            </a>
                            <div id="collection_cover_image"></div>
                            <input type="hidden" id="collection_cover_img_id" name="collection_cover_img_id" value=""/>
                        </div>

                    </div>

                </div>
            </div>

            <hr class='tainacanRow' />

            <div class="form-group">
                <a id="show_adv_config_link" onclick="showAdvancedConfig();" style="cursor:pointer;">
                    <span class="caret"></span>&nbsp;<?php _e('Advanced Configuration', 'tainacan'); ?>                    
                </a>
                <a id="hide_adv_config_link" onclick="hideAdvancedConfig();" style="display: none;cursor:pointer;">
                    <span class="caret-right"></span>&nbsp; <?php _e('Hide Advanced Configuration', 'tainacan'); ?></a>
            </div>

            <!------------------- DIV ADVANCED -------------------------->
            <div id="advanced_config" style="display: none;">
                <?php do_action('insert_form_edit_collection',$collection_post,$collection_metas); ?>
                <!------------------- Endereco da colecao -------------------------->
                <div class="form-group">
                    <label for="collection_description"><?php _e('Collection Address', 'tainacan'); ?></label>
                    <a href="javascript:void(0)" data-toggle="tooltip" title="<?php _e('The address must not contain spaces or special characters. If it contains will be removed by the system. Limit of 200 characters.', 'tainacan'); ?>">
                        <span class="glyphicon glyphicon-question-sign"></span>
                    </a>
                </div>
                <div class="form-inline form-group">
                    <div class="alert alert-success" style="display: none;width: 30%;" id="collection_name_success"><span class="glyphicon glyphicon-ok" ></span>&nbsp;&nbsp;<?php _e('Valid name!', 'tainacan') ?></div>
                    <div class="alert alert-danger" style="display: none;width: 30%;" id="collection_name_error"><span class="glyphicon glyphicon-warning-sign" >&nbsp;&nbsp;</span><?php _e('Invalid name!', 'tainacan') ?></div>
                    <label class="control-label" ><?php echo site_url() . '/'; ?></label>
                    <input onkeyup="verify_name_collection();" id="suggested_collection_name" required="required" type="text" class="form-control" name="socialdb_collection_address"  value="<?php echo $collection_post->post_name; ?>" maxlength="200" >
                    <input type="hidden" id="initial_address"  name="initial_address"  value="<?php echo $collection_post->post_name; ?>" >
                </div>
                <!----------------------- Objeto da colecao ----------------->
                <div class="form-group">
                    <label for="socialdb_collection_object_name">
                        <?php _e('Collection Object', 'tainacan'); ?>
                    </label><br>
                    <input id="socialdb_collection_object_name" 
                           type="text" 
                           class="form-control" 
                           name="socialdb_collection_object_name"  
                           value="<?php echo $collection_metas['socialdb_collection_object_name']; ?>" >
                </div>
                <!------------------- Esconder tags-------------------------->
                <!--div class="form-group">
                    <label for="socialdb_collection_hide_tags"><?php _e('Hide Tags', 'tainacan'); ?></label> 
                    <select name="socialdb_collection_hide_tags" class="form-control">
                        <option value="no"  <?php
                if ($collection_metas['socialdb_collection_hide_tags'] == 'no' || $collection_metas['socialdb_collection_hide_tags'] == '') {
                    echo 'selected = "selected"';
                }
                ?>>
                <?php _e('No', 'tainacan'); ?>
                        </option>
                        <option value="yes" <?php
                if ($collection_metas['socialdb_collection_hide_tags'] == 'yes') {
                    echo 'selected = "selected"';
                }
                ?>>
                <?php _e('Yes', 'tainacan'); ?>
                        </option>
                    </select>
                </div>
                <!------------------- Privacidade-------------------------->
                <div class="form-group">
                    <label for="collection_privacy"><?php _e('Collection privacy', 'tainacan'); ?></label> 
                    <select name="collection_privacy" class="form-control">
                        <option value="public" <?php
                        if ($collection_metas['sociadb_collection_privacity'][0]->name == 'socialdb_collection_public' || empty($collection_metas['sociadb_collection_privacity'])) {
                            echo 'selected = "selected"';
                        }
                        ?>>
                            <?php _e('Public', 'tainacan'); ?>
                        </option>
                        <option value="private" <?php
                        if ($collection_metas['sociadb_collection_privacity'][0]->name == 'socialdb_collection_private') {
                            echo 'selected = "selected"';
                        }
                        ?>>
                                    <?php _e('Private', 'tainacan'); ?>
                        </option>
                    </select>
                </div>
                <!------------------- Parent-------------------------->
                <input type="hidden" id="selected_parent_collection" value="<?php
                if ($collection_metas['socialdb_collection_parent'] && $collection_metas['socialdb_collection_parent'] != ''): echo $collection_metas['socialdb_collection_parent'];
                endif;
                ?>">

                <div class="form-group">
                    <label for="socialdb_collection_parent"><?php _e('Collection Parent', 'tainacan'); ?></label> 
                    <select name="socialdb_collection_parent" class="combobox form-control" id="socialdb_collection_parent">
                    </select>
                </div>
                <!------------------- Hierarquia-------------------------->
                <div class="form-group">
                    <label for="socialdb_collection_allow_hierarchy"><?php _e('Collection Hierarchy', 'tainacan'); ?></label>
                    <a href="#" data-toggle="tooltip" title="<?php _e('Changing the collection parent allows this collection to extend all metadata and rankings from another collection', 'tainacan'); ?>">
                        <span class="glyphicon glyphicon-question-sign"></span>
                    </a>
                    <select name="socialdb_collection_allow_hierarchy" class="form-control">
                        <option value="true" <?php
                        if ($collection_metas['socialdb_collection_allow_hierarchy'] == 'true' || empty($collection_metas['socialdb_collection_allow_hierarchy'])) {
                            echo 'selected = "selected"';
                        }
                        ?>>
                                    <?php _e('Yes', 'tainacan'); ?>
                        </option>
                        <option value="false" <?php
                        if ($collection_metas['socialdb_collection_allow_hierarchy'] == 'false') {
                            echo 'selected = "selected"';
                        }
                        ?>>
                                    <?php _e('No', 'tainacan'); ?>
                        </option>
                    </select>
                </div>

                <!-- Downloads Control -->
                <div class="form-group">
                    <label for="socialdb_collection_download_control"><?php _e('Downloads Control', 'tainacan'); ?></label> 
                    <select name="socialdb_collection_download_control" class="form-control">
                        <option value="allowed" <?php
                        if ($collection_metas['socialdb_collection_download_control'] == 'allowed' || empty($collection_metas['socialdb_collection_download_control'])) {
                            echo 'selected = "selected"';
                        }
                        ?>>
                                    <?php _e('Allowed - Everyone can download the original images', 'tainacan'); ?>
                        </option>
                        <option value="moderate" <?php
                        if ($collection_metas['socialdb_collection_download_control'] == 'moderate') {
                            echo 'selected = "selected"';
                        }
                        ?>>
                                    <?php _e('Moderate - Allowed by login', 'tainacan'); ?>
                        </option>
                        <option value="controlled" <?php
                        if ($collection_metas['socialdb_collection_download_control'] == 'controlled') {
                            echo 'selected = "selected"';
                        }
                        ?>>
                            <?php _e('Controlled - Only admins of the collection and the owners of the items can make downloads. Thumbnails of images are displayed.', 'tainacan'); ?>
                        </option>
                    </select>
                    <input type="checkbox" id="add_watermark" name="add_watermark" value="true" <?php if($collection_metas['socialdb_collection_add_watermark']){ echo 'checked="checked"';}?>> <?php _e('Generate thumbnail with watermark', 'tainacan'); ?>
                </div>

                <div id="uploadWatermark" style="<?php if($collection_metas['socialdb_collection_add_watermark']){ echo 'display:block;';}else{echo 'display:none;';}?>">
                    <div id="socialdb_watermark" class="form-group">
                        <?php
                        $image_watermark_url = wp_get_attachment_url(get_post_meta($collection_post->ID, 'socialdb_collection_watermark_id', true));
                        if ($image_watermark_url) {
                            ?>
                            <label for="socialdb_collection_watermark"><?php _e('Watermark', 'tainacan'); ?></label> <br />
                            <img src="<?= $image_watermark_url ?>" style='max-height:190px;' />
                            <br /><br />
                            <label for="remove_watermark"><?php _e('Remove Watermark', 'tainacan'); ?></label>
                            <input type="checkbox"  id="remove_watermark" name="remove_watermark" value="true">
                            <br /><br />
                        <?php } ?>
                    </div>
                    <input type="file" size="50" id="socialdb_collection_watermark" name="socialdb_collection_watermark" class="btn btn-default btn-sm">
                </div>

                <!--div class="form-group">
                    <label for="collection_attachments"><?php _e('You allow attachments to objects from the collection?', 'tainacan'); ?></label> 
                    <select name="collection_attachments" class="form-control">
                        <option value="yes" <?php
                if ($collection_metas['socialdb_collection_attachment'] == 'yes' || empty($collection_metas['socialdb_collection_attachment'])) {
                    echo 'selected = "selected"';
                }
                ?>>
                <?php _e('Yes', 'tainacan'); ?>
                        </option>
                        <option value="no" <?php
                if ($collection_metas['socialdb_collection_attachment'] == 'no') {
                    echo 'selected = "selected"';
                }
                ?>>
                <?php _e('No', 'tainacan'); ?>
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="collection_most_participatory"><?php _e('You want to show the ranking of the most participatory authors?', 'tainacan'); ?></label> 
                    <select name="collection_most_participatory" class="form-control">
                        <option value="yes"  <?php
                if ($collection_metas['socialdb_collection_most_participatory'] == 'yes' || empty($collection_metas['socialdb_collection_most_participatory'])) {
                    echo 'selected = "selected"';
                }
                ?>>
                <?php _e('Yes', 'tainacan'); ?>
                        </option>
                        <option value="no" <?php
                if ($collection_metas['socialdb_collection_most_participatory'] == 'no') {
                    echo 'selected = "selected"';
                }
                ?>>
                <?php _e('No', 'tainacan'); ?>
                        </option>
                    </select>
                </div-->

                <div class="form-group">
                    <label for=""><?php _t('Change collection owner',1); ?></label>
                    <?php /* <div class="form-control"> <div><?php echo _t('Current owner: ',1) .  get_the_author_meta("display_name", $collection_post->post_author); ?></div></div>*/ ?>
                    <input type="text" id="get_users_auto" class="chosen-selected form-control ui-autocomplete-input"
                           onkeyup="autocomplete_moderators('<?php echo $collection_post->ID; ?>', '#get_users_auto');"
                           placeholder="<?php _t('Type the three first letters of the user name ', 1);?>"
                           autocomplete="off">

                    <div class="col-md-12 no-padding new_own_cont">
                        <div class="new_owner_of_<?php echo $collection_post->ID; ?>">
                            <?php _t('New owner: ',1); ?>
                        </div>
                        <input type="hidden" class="new_owner_of_<?php echo $collection_post->ID?>"
                               name="collection_owner" id="collection_owner" value="<?php echo $collection_post->post_author; ?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="collection_moderation_type"><?php _e('Type of Moderation', 'tainacan'); ?></label> 
                    <select name="socialdb_collection_moderation_type" id="socialdb_collection_moderation_type" class="form-control" onchange="showModerationDays();">
                        <option value="moderador"  <?php
                        if ($collection_metas['socialdb_collection_moderation_type'] == 'moderador' || empty($collection_metas['socialdb_collection_moderation_type'])) {
                            echo 'selected = "selected"';
                        }
                        ?>>
                                    <?php _e('Approval by a moderator', 'tainacan'); ?>
                        </option>
                        <option value="democratico" <?php
                        if ($collection_metas['socialdb_collection_moderation_type'] == 'democratico') {
                            echo 'selected = "selected"';
                        }
                        ?>>
                                    <?php _e('Democratic approval (majority election)', 'tainacan'); ?>
                        </option>
                    </select>
                    <div id="div_moderation_days" style="display: none;">
                        <br>
                        <label for="socialdb_collection_moderation_days"><?php _e('Time to vote (days):', 'tainacan'); ?></label> 
                        <?php
                        $socialdb_collection_moderation_days = (empty($collection_metas['socialdb_collection_moderation_days']) ? '2' : $collection_metas['socialdb_collection_moderation_days'])
                        ?>
                        <input type="text" class="form-control" onkeypress="return onlyNumbers(this);" id="socialdb_collection_moderation_days" name="socialdb_collection_moderation_days" value="<?php echo $socialdb_collection_moderation_days; ?>">
                        <!--select name="socialdb_collection_moderation_days" class="form-control">
                            <option value="2"  <?php
                        if ($collection_metas['socialdb_collection_moderation_days'] == '2' || empty($collection_metas['socialdb_collection_moderation_days'])) {
                            echo 'selected = "selected"';
                        }
                        ?>>2</option>
                            <option value="5" <?php
                        if ($collection_metas['socialdb_collection_moderation_days'] == '5') {
                            echo 'selected = "selected"';
                        }
                        ?>>5</option>
                            <option value="7" <?php
                        if ($collection_metas['socialdb_collection_moderation_days'] == '7') {
                            echo 'selected = "selected"';
                        }
                        ?>>7</option>
                            <option value="10" <?php
                        if ($collection_metas['socialdb_collection_moderation_days'] == '10') {
                            echo 'selected = "selected"';
                        }
                        ?>>10</option>
                            <option value="14" <?php
                        if ($collection_metas['socialdb_collection_moderation_days'] == '14') {
                            echo 'selected = "selected"';
                        }
                        ?>>14</option>
                        </select-->
                    </div>
                </div>

                <div class="form-group">
                    <label for=""><?php _e('Collection Moderators', 'tainacan'); ?></label> 
                    <input type="text" onkeyup="autocomplete_moderators('<?php echo $collection_post->ID; ?>');" id="autocomplete_moderator" placeholder="<?php _e('Type the three first letters of the user name ', 'tainacan'); ?>"  class="chosen-selected form-control"  />
                    <select onclick="clear_select_moderators(this);" id="moderators_<?php echo $collection_post->ID; ?>" multiple class="chosen-selected2 form-control" style="height: auto;" multiple name="collection_moderators[]" id="chosen-selected2-user"  >
                        <?php if ($collection_metas['socialdb_collection_moderator']) { ?>
                            <?php foreach ($collection_metas['socialdb_collection_moderator'] as $moderator) {  // percoro todos os objetos     ?>
                                <option selected='selected' value="<?php echo $moderator['id'] ?>"><?php echo $moderator['name'] ?></option>
                            <?php } ?> 
                        <?php } ?>            
                    </select>
                </div>    

                <div class="form-group row">
                    <div class="col-md-12">
                        <label for=""><?php _e('Permissions - Choose permissions for each of the following actions', 'tainacan'); ?></label> 
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <div class="col-md-6" id="entity"><strong><?php _e('Entity', 'tainacan'); ?></strong></div>
                            <div class="col-md-2"><strong><?php _e('Create', 'tainacan'); ?></strong></div>
                            <div class="col-md-2"><strong><?php _e('Edit', 'tainacan'); ?></strong></div>
                            <div class="col-md-2"><strong><?php _e('Delete', 'tainacan'); ?></strong></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <div class="col-md-6"><?php _e('Category', 'tainacan'); ?></div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_create_category" id="socialdb_collection_permission_create_category" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_category'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_category'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_category'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_category'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_edit_category" id="socialdb_collection_permission_edit_category" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_category'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_category'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_category'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_category'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_delete_category" id="socialdb_collection_permission_delete_category" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_category'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_category'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_category'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_category'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <div class="col-md-6"><?php _e('Classification', 'tainacan'); ?></div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_add_classification" id="socialdb_collection_permission_add_classification" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_add_classification'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_add_classification'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_add_classification'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_add_classification'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_delete_classification" id="socialdb_collection_permission_delete_classification" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_classification'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_classification'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_classification'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_classification'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <div class="col-md-6"><?php _e('Item', 'tainacan'); ?></div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_create_object" id="socialdb_collection_permission_create_object" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_object'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_object'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_object'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_object'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_delete_object" id="socialdb_collection_permission_delete_object" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_object'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_object'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_object'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_object'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <div class="col-md-6"><?php _e('Comments', 'tainacan'); ?></div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_create_comment" id="socialdb_collection_permission_create_comment" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_comment'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_comment'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_comment'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_comment'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_edit_comment" id="socialdb_collection_permission_edit_comment" class="form-control">
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_comment'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_comment'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_comment'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_delete_comment" id="socialdb_collection_permission_delete_comment" class="form-control">
                                    <!--option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_comment'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option-->
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_comment'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_comment'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_comment'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <div class="col-md-6"><?php _e('Tags', 'tainacan'); ?></div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_create_tags" id="socialdb_collection_permission_create_tags" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_tags'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_tags'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_tags'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_tags'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_edit_tags" id="socialdb_collection_permission_edit_tags" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_tags'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_tags'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_tags'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_tags'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_delete_tags" id="socialdb_collection_permission_delete_tags" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_tags'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_tags'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_tags'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_tags'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <div class="col-md-6"><?php _e('Property Data', 'tainacan'); ?></div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_create_property_data" id="socialdb_collection_permission_create_property_data" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_property_data'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_property_data'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_property_data'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_property_data'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_edit_property_data" id="socialdb_collection_permission_edit_property_data" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_data'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_data'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_data'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_data'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_delete_property_data" id="socialdb_collection_permission_delete_property_data" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_property_data'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_property_data'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_property_data'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_property_data'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <div class="col-md-6"><?php _e('Property Data Value', 'tainacan'); ?></div>
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_edit_property_data_value" id="socialdb_collection_permission_edit_property_data_value" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_data_value'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_data_value'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_data_value'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_data_value'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">&nbsp;</div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <div class="col-md-6"><?php _e('Property Object', 'tainacan'); ?></div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_create_property_object" id="socialdb_collection_permission_create_property_object" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_property_object'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_property_object'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_property_object'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_property_object'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_edit_property_object" id="socialdb_collection_permission_edit_property_object" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_object'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_object'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_object'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_object'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_delete_property_object" id="socialdb_collection_permission_delete_property_object" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_property_object'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_property_object'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_property_object'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_property_object'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <div class="col-md-6"><?php _e('Property Object Value', 'tainacan'); ?></div>
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_edit_property_object_value" id="socialdb_collection_permission_edit_property_object_value" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_object_value'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_object_value'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_object_value'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_object_value'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">&nbsp;</div>
                        </div>
                    </div>
                    <!-- Property Terms -->
                    <div class="col-md-12">
                        <div class="form-group row">
                            <div class="col-md-6"><?php _e('Property Term', 'tainacan'); ?></div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_create_property_term" id="socialdb_collection_permission_create_property_term" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_property_term'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_property_term'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_property_term'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_create_property_term'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_edit_property_term" id="socialdb_collection_permission_edit_property_term" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_term'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_term'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_term'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_edit_property_term'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="socialdb_collection_permission_delete_property_term" id="socialdb_collection_permission_delete_property_term" class="form-control">
                                    <option value="anonymous" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_property_term'] == 'anonymous') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                    <option value="approval" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_property_term'] == 'approval') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Approval', 'tainacan'); ?></option>
                                    <option value="members" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_property_term'] == 'members') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Members', 'tainacan'); ?></option>
                                    <option value="unallowed" <?php
                                    if ($collection_metas['socialdb_collection_permission_delete_property_term'] == 'unallowed') {
                                        echo 'selected = "selected"';
                                    }
                                    ?>><?php _e('Not Allowed', 'tainacan'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <div class="col-md-6" id="entity"><strong><?php _e('Entity', 'tainacan'); ?></strong></div>
                            <div class="col-md-2"><strong><?php _e('Create', 'tainacan'); ?></strong></div>
                            <div class="col-md-2"><strong><?php _e('Edit', 'tainacan'); ?></strong></div>
                            <div class="col-md-2"><strong><?php _e('Delete', 'tainacan'); ?></strong></div>
                        </div>
                    </div>
                </div>

                <!--div class="form-group">
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><strong><?php _e('Permissions - Choose permissions for each of the following actions', 'tainacan'); ?></strong></legend>
                <!--div class="col-md-12">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="socialdb_collection_permission_create_category"><?php _e('Create Category', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_create_category" id="socialdb_collection_permission_create_category" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_create_category'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_create_category'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_create_category'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="socialdb_collection_permission_edit_category"><?php _e('Edit Category', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_edit_category" id="socialdb_collection_permission_edit_category" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_edit_category'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_edit_category'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_edit_category'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="socialdb_collection_permission_delete_category"><?php _e('Delete Category', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_delete_category" id="socialdb_collection_permission_delete_category" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_delete_category'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_delete_category'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_delete_category'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="socialdb_collection_permission_add_classification"><?php _e('Add Classification', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_add_classification" id="socialdb_collection_permission_add_classification" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_add_classification'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_add_classification'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_add_classification'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="socialdb_collection_permission_delete_classification"><?php _e('Delete Classification', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_delete_classification" id="socialdb_collection_permission_delete_classification" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_delete_classification'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_delete_classification'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_delete_classification'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="socialdb_collection_permission_create_object"><?php _e('Create Object', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_create_object" id="socialdb_collection_permission_create_object" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_create_object'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_create_object'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_create_object'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="socialdb_collection_permission_delete_object"><?php _e('Delete Object', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_delete_object" id="socialdb_collection_permission_delete_object" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_delete_object'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_delete_object'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_delete_object'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="socialdb_collection_permission_create_property_data"><?php _e('Create Property Data', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_create_property_data" id="socialdb_collection_permission_create_property_data" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_create_property_data'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_create_property_data'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_create_property_data'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="socialdb_collection_permission_edit_property_data"><?php _e('Edit Property Data', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_edit_property_data" id="socialdb_collection_permission_edit_property_data" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_edit_property_data'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_edit_property_data'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_edit_property_data'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="socialdb_collection_permission_delete_property_data"><?php _e('Delete Property Data', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_delete_property_data" id="socialdb_collection_permission_delete_property_data" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_delete_property_data'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_delete_property_data'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_delete_property_data'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="socialdb_collection_permission_edit_property_data_value"><?php _e('Edit Property Data Value', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_edit_property_data_value" id="socialdb_collection_permission_edit_property_data_value" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_edit_property_data_value'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_edit_property_data_value'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_edit_property_data_value'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="socialdb_collection_permission_create_property_object"><?php _e('Create Property Object', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_create_property_object" id="socialdb_collection_permission_create_property_object" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_create_property_object'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_create_property_object'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_create_property_object'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="socialdb_collection_permission_edit_property_object"><?php _e('Edit Property Object', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_edit_property_object" id="socialdb_collection_permission_edit_property_object" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_edit_property_object'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_edit_property_object'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_edit_property_object'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="socialdb_collection_permission_delete_property_object"><?php _e('Delete Property Object', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_delete_property_object" id="socialdb_collection_permission_delete_property_object" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_delete_property_object'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_delete_property_object'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_delete_property_object'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="socialdb_collection_permission_edit_property_object_value"><?php _e('Edit Property Object Value', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_edit_property_object_value" id="socialdb_collection_permission_edit_property_object_value" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_edit_property_object_value'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_edit_property_object_value'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_edit_property_object_value'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <!--div class="col-md-12">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="socialdb_collection_permission_create_comment"><?php _e('Create Comment', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_create_comment" id="socialdb_collection_permission_create_comment" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_create_comment'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_create_comment'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_create_comment'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="socialdb_collection_permission_edit_comment"><?php _e('Edit Comment', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_edit_comment" id="socialdb_collection_permission_edit_comment" class="form-control">
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_edit_comment'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_edit_comment'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="socialdb_collection_permission_delete_comment"><?php _e('Delete Comment', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_delete_comment" id="socialdb_collection_permission_delete_comment" class="form-control">
                <!--option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_delete_comment'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option-->
                <!--option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_delete_comment'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_delete_comment'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
            </select>
        </div>
    </div>
</div-->
                <!--div class="col-md-12">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="socialdb_collection_permission_create_tags"><?php _e('Create Tags', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_create_tags" id="socialdb_collection_permission_create_tags" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_create_tags'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_create_tags'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_create_tags'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="socialdb_collection_permission_edit_tags"><?php _e('Edit Tags', 'tainacan'); ?></label>
                            <select name="socialdb_collection_permission_edit_tags" id="socialdb_collection_permission_edit_tags" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_edit_tags'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_edit_tags'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_edit_tags'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="socialdb_collection_permission_delete_tags"><?php _e('Delete Tags'); ?></label>
                            <select name="socialdb_collection_permission_delete_tags" id="socialdb_collection_permission_delete_tags" class="form-control">
                                <option value="anonymous" <?php
                if ($collection_metas['socialdb_collection_permission_delete_tags'] == 'anonymous') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Anonymous', 'tainacan'); ?></option>
                                <option value="approval" <?php
                if ($collection_metas['socialdb_collection_permission_delete_tags'] == 'approval') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Approval', 'tainacan'); ?></option>
                                <option value="members" <?php
                if ($collection_metas['socialdb_collection_permission_delete_tags'] == 'members') {
                    echo 'selected = "selected"';
                }
                ?>><?php _e('Members', 'tainacan'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div-->
            </div>
            <input type="hidden" id="verify_collection_name" name="verify_collection_name" value="allow">
            <input type="hidden" id="redirect_to_caegories" name="redirect_to_caegories" value="false">
            <input type="hidden" id="collection_id" name="collection_id" value="<?php echo $collection_post->ID; ?>">
            <input type="hidden" id="operation" name="operation" value="update">
            <input type="hidden" id="save_and_next" name="save_and_next" value="false">
            
            <button type="button" class="btn btn-default pull-left btn-lg" onclick="backToMainPage()"><?php _e('Cancel', 'tainacan'); ?></button>
            
            <button type="submit" id="button_save_and_next" class="btn btn-success pull-right btn-lg"> <?php _e('Continue', 'tainacan'); ?> </button>
            <?php /*
            <button type="submit" id="button_save_and_next"  class="btn btn-primary" style="float: right;" ><?php _e('Save & Next', 'tainacan'); ?></button>
             * */ ?>
        </form>
    </div>
</div>