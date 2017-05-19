<?php
    include_once ('js/tabs_item_js.php');
    include_once ('js/create_item_text_js.php');
    include_once ('js/create_draft_js.php');
    include_once ('js/validation_fixed_fields.php');
    include_once(dirname(__FILE__).'/../../helpers/view_helper.php');
    include_once(dirname(__FILE__).'/../../helpers/object/object_helper.php');

    $view_helper = new ObjectHelper($collection_id);
    $val = get_post_meta($collection_id, 'socialdb_collection_submission_visualization', true);
    $habilitateMedia = get_post_meta($collection_id, 'socialdb_collection_habilitate_media', true);
    $css = '';
    if($val&&$val=='one'){
        $view_helper->hide_main_container = true;
        $view_helper->mediaHabilitate = ($habilitateMedia =='true') ? true:false;
        $css = ($view_helper->mediaHabilitate) ? 'width: 72%; margin-left: 15px;margin-right: 10px;padding-left: 15px;' : 'margin-left:1%;width: 98%;padding-left:15px;';
        $mode = true;
    }else{
        $mode = false;
    }
    /**
     *
     * View utilizado para criar um item do tipo texto, utiliza os containers
     * list_properties-accordion, list_ranking_create e show_insert_object_licenses
     *
     *
     */
?>
<form  id="submit_form" onkeypress="return (event.keyCode == 13) ? false : true ;" style="margin-left: 15px;">
    <input type="hidden" id="object_id_add" name="object_id" value="<?php echo $object_id ?>">
    <input type="hidden" id="object_from" name="object_from" value="internal">
    <input type="hidden" id="object_type" name="object_type" value="text">
    <input type="hidden"
           id="object_classifications"
           name="object_classifications"
           value="<?php echo (isset($classifications)&&$classifications)? $classifications: "" ?>">
    <input type="hidden" id="object_content" name="object_content" value="">
    <input type="hidden" id="create_object_collection_id" name="collection_id" value="">
    <input type="hidden" id="operation" name="operation_priority" value="add">
    <div class="row" style="background-color: #f1f2f2">
        <div style="<?php echo ($view_helper->hide_main_container)?'margin-left:1%;padding-left:15px;min-height:500px;padding-top:80px;':'' ?>"
            class="<?php echo ($view_helper->hide_main_container)?'col-md-12':'col-md-2' ?> menu_left_loader">
             <center>
                    <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                    <h4><?php _e('Loading metadata...', 'tainacan') ?></h4>
             </center>
        </div>
        <div style="display: none; <?php echo $css?>"
             class="<?php echo ($view_helper->hide_main_container)? ($view_helper->mediaHabilitate)? 'col-md-9' : 'col-md-12':'col-md-3' ?> menu_left">
            <?php
                //se estiver apenas mostrando as propriedades
                if($view_helper->hide_main_container):
                ?>
                 <h4>
                    <?php if(has_action('label_add_item')): ?>
                           <?php do_action('label_add_item',$object_name) ?>
                    <?php else: ?>
                     <?php _e('Create new item - Write text','tainacan') ?></span>
                    <?php endif; ?>
                    <!--button type="button" onclick="back_main_list();"class="btn btn-default pull-right"-->
                    <a class="btn btn-default pull-right" href="<?php get_the_permalink($collection_id) ?>">
                        <b><?php _e('Back','tainacan') ?></b>
                    </a>
                    <br>
                    <small id="draft-text"></small>
                </h4>
                <hr>
                <!--------------------------- ABAS----------------------------->
                <?php $view_helper->add_tabs($collection_id) ?>
            <?php else: ?>
                <div    style="<?php echo ($view_helper->hide_main_container)?'margin-bottom:0%':'' ?>"
                    class="expand-all-item btn white tainacan-default-tags">
                <div class="action-text"
                     style="display: inline-block;">
                         <?php _e('Expand all', 'tainacan') ?></div>
                &nbsp;&nbsp;<span class="glyphicon-triangle-bottom white glyphicon"></span>
                </div>
            <?php endif; ?>
<!-- TAINACAN: INICIO ACCORDEON -->
            <div id="text_accordion" class="multiple-items-accordion">
            <?php
            //se for no modo de apenas um container
            if($mode):
            ?>
            <!-- TAINACAN: titulo do item -->
            <div id="<?php echo $view_helper->get_id_list_properties('title','title'); ?>"
                 class="form-group"
                <?php echo $view_helper->get_visibility($view_helper->terms_fixed['title']) ?>
                <?php do_action('item_title_attributes') ?>>
                <h2>
                    <?php echo ($view_helper->terms_fixed['title']) ? $view_helper->terms_fixed['title']->name :  _e('Title','tainacan') ?>
                    <a class="pull-right"
                       style="margin-right: 15px;margin-left: -25px;"
                        >
                        <span title="<?php _e('Type the item name','tainacan'); ?>"
                       data-toggle="tooltip" data-placement="bottom" class="glyphicon glyphicon-question-sign"></span>
                    </a>
                    <?php
                        echo $view_helper->setValidation($collection_id, $view_helper->terms_fixed['title']->term_id, 'title');
                    ?>
                </h2>
                 <div>
                    <input type="hidden" 
                           class="title_mask" 
                           value="<?php echo get_post_meta($collection_id, 'socialdb_collection_property_'.$view_helper->terms_fixed['title']->term_id.'_mask_key', true) ?>">
                    <input class="form-control auto-save"
                           type="text"
                           id="object_name"
                           name="object_name"
                           placeholder="<?php _e('Item name','tainacan'); ?>">
                  </div>
            </div>
            <!-- TAINACAN: Campo com o ckeditor para items do tipo texto -->
            <div id="<?php echo $view_helper->get_id_list_properties('content','object_content_text'); ?>"
                  <?php echo $view_helper->get_visibility($view_helper->terms_fixed['content']) ?>
                 class="form-group" <?php do_action('item_content_attributes') ?>>
                <h2>
                    <?php echo ($view_helper->terms_fixed['content']) ? $view_helper->terms_fixed['content']->name :  _e('Content','tainacan') ?>
                    <a class="pull-right"
                       style="margin-right: 15px;margin-left: -25px;"
                        >
                        <span title="<?php _e('Type the content of the item','tainacan'); ?>"
                       data-toggle="tooltip" data-placement="bottom" class="glyphicon glyphicon-question-sign"></span>
                    </a>
                    <?php
                        echo $view_helper->setValidation($collection_id, $view_helper->terms_fixed['content']->term_id, 'content');
                    ?>
                </h2>
                 <div >
                    <textarea class="form-control auto-save" id="object_editor" name="object_editor" placeholder="<?php _e('Object Content','tainacan'); ?>"></textarea>
                </div>
            </div>
            <!-- TAINACAN: UPLOAD DE ANEXOS DOS ITEMS -->
               <?php if(!$view_helper->mediaHabilitate): ?>
                    <div id="<?php echo $view_helper->get_id_list_properties('attachments','attachments'); ?>"
                        <?php echo $view_helper->get_visibility($view_helper->terms_fixed['attachments']) ?>
                        class="form-group" <?php do_action('item_attachments_attributes') ?> >
                        <h2>
                           <?php echo ($view_helper->terms_fixed['attachments']) ? $view_helper->terms_fixed['attachments']->name :  _e('Attachments','tainacan') ?>
                            <a class="pull-right"
                              style="margin-right: 15px;"
                                >
                                <span title="<?php _e('Upload attachments for your item','tainacan'); ?>"
                               data-toggle="tooltip" data-placement="bottom" class="glyphicon glyphicon-question-sign"></span>
                            </a>
                            <?php
                                echo $view_helper->setValidation($collection_id, $view_helper->terms_fixed['attachments']->term_id, 'attachments');
                            ?>
                        </h2>
                        <div >
                            <div id="dropzone_new" <?php ($socialdb_collection_attachment=='no') ? print_r('style="display:none"') : '' ?>
                                class="dropzone"
                                style="margin-bottom: 15px;min-height: 150px;padding-top: 0px;">
                                    <div class="dz-message" data-dz-message>
                                     <span style="text-align: center;vertical-align: middle;">
                                         <h3>
                                             <span class="glyphicon glyphicon-upload"></span>
                                             <b><?php _e('Drop Files','tainacan')  ?></b>
                                                 <?php _e('to upload','tainacan')  ?>
                                         </h3>
                                         <h4>(<?php _e('or click','tainacan')  ?>)</h4>
                                     </span>
                                 </div>
                            </div>
                        </div>
                    </div>
                 <?php endif; ?>
            <?php endif; ?>
            <!-- TAINACAN: thumbnail do item -->
            <?php if(!$view_helper->mediaHabilitate): ?>
            <div id="<?php echo $view_helper->get_id_list_properties('thumbnail','thumbnail_id'); ?>"
                 class="form-group"
                <?php echo $view_helper->get_visibility($view_helper->terms_fixed['thumbnail']) ?>
                <?php do_action('item_thumbnail_attributes') ?>>
                <h2>
                    <?php echo ($view_helper->terms_fixed['thumbnail']) ? $view_helper->terms_fixed['thumbnail']->name :  _e('Thumbnail','tainacan') ?>
                    <?php do_action('optional_message') ?>
                    <a class="pull-right"
                       style="margin-right: 15px;"
                       >
                        <span  title="<?php _e('Insert a thumbnail in your item!','tainacan'); ?>"
                       data-toggle="tooltip" data-placement="bottom" class="glyphicon glyphicon-question-sign"></span>
                    </a>
                    <?php
                        echo $view_helper->setValidation($collection_id, $view_helper->terms_fixed['thumbnail']->term_id, 'thumbnail');
                    ?>
                </h2>
                <div  >
                        <input type="hidden" name="thumbnail_url" id="thumbnail_url" value="">
                        <div id="image_side_create_object">
                        </div>
                        <input type="file"
                               id="object_thumbnail"

                               name="object_thumbnail"
                               class="form-control auto-save">
                </div>
            </div>
            <?php endif; ?>
            <!-- TAINACAN: a fonte do item -->
            <div id="<?php echo $view_helper->get_id_list_properties('source','socialdb_object_dc_source'); ?>"
                 class="form-group"
                <?php echo $view_helper->get_visibility($view_helper->terms_fixed['source']) ?>
                <?php do_action('item_source_attributes') ?>>
                <h2>
                    <?php echo ($view_helper->terms_fixed['source']) ? $view_helper->terms_fixed['source']->name :  _e('Source','tainacan') ?>
                    <a class="pull-right"
                       style="margin-right: 15px;margin-left: -25px;"
                        >
                        <span title="<?php _e('What\'s the item source','tainacan'); ?>"
                       data-toggle="tooltip" data-placement="bottom" class="glyphicon glyphicon-question-sign"></span>
                    </a>
                    <?php
                        echo $view_helper->setValidation($collection_id, $view_helper->terms_fixed['source']->term_id, 'source');
                    ?>
                </h2>
                 <div  >
                    <input
                           type="text"
                           id="object_source"
                           class="form-control auto-save"
                           name="object_source"
                           placeholder="<?php _e('What\'s the item source','tainacan'); ?>"
                           value="" >
                  </div>
            </div>
            <!-- TAINACAN: a descricao do item -->
            <div id="<?php echo $view_helper->get_id_list_properties('description','post_content'); ?>"
                 class="form-group"
                 <?php echo $view_helper->get_visibility($view_helper->terms_fixed['description']) ?>
                 >
                <h2>
                     <?php echo ($view_helper->terms_fixed['description']) ? $view_helper->terms_fixed['description']->name :  _e('Description','tainacan') ?>
                    <a class="pull-right"
                       style="margin-right: 15px;margin-left: -25px;"
                        >
                        <span title="<?php _e('Describe your item','tainacan'); ?>"
                       data-toggle="tooltip" data-placement="bottom" class="glyphicon glyphicon-question-sign"></span>
                    </a>
                    <?php
                        echo $view_helper->setValidation($collection_id, $view_helper->terms_fixed['description']->term_id, 'description');
                    ?>
                </h2>
                <div >
                    <textarea class="form-control auto-save"
                              rows="8"
                              id="object_description_example"
                              placeholder="<?php _e('Describe your item','tainacan'); ?>"
                              name="object_description" ></textarea>
                </div>
            </div>
            <!-- TAINACAN: tags do item -->
            <div id="<?php echo $view_helper->get_id_list_properties('tags','tag'); ?>"
                 class="form-group"
                <?php echo $view_helper->get_visibility($view_helper->terms_fixed['tags']) ?>
                <?php do_action('item_tags_attributes') ?>>
                <h2>
                    <?php echo ($view_helper->terms_fixed['tags']) ? $view_helper->terms_fixed['tags']->name :  _e('Tags','tainacan') ?>
                    <a class="pull-right"
                       style="margin-right: 15px;margin-left: -25px;"
                       >
                        <span  title="<?php _e('The set of tags may be inserted by comma','tainacan') ?>"
                       data-toggle="tooltip" data-placement="bottom" class="glyphicon glyphicon-question-sign"></span>
                    </a>
                    <?php
                        echo $view_helper->setValidation($collection_id, $view_helper->terms_fixed['tags']->term_id, 'tags');
                    ?>
                </h2>
                <div  >
                    <input type="text"
                           class="form-control auto-save"
                           id="object_tags"
                           name="object_tags"
                           placeholder="<?php _e('The set of tags may be inserted by comma','tainacan') ?>">
                 </div>
            </div>
            <!-- TAINACAN: a propriedades do item -->
            <div id="show_form_properties">
                <center>
                    <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                    <h4><?php _e('Loading Properties...', 'tainacan') ?></h4>
                </center>
            </div>
            <!-- TAINACAN: a licencas do item -->
            <div id="<?php echo $view_helper->get_id_list_properties('license','list_licenses_items'); ?>"
                 class="form-group"
                 <?php echo $view_helper->get_visibility($view_helper->terms_fixed['license']) ?>
                 >
                <h2>
                    <?php echo ($view_helper->terms_fixed['license']) ? $view_helper->terms_fixed['license']->name :  _e('Licenses','tainacan') ?>
                    <a class="pull-right"
                       style="margin-right: 15px;margin-left: -25px;"
                       >
                        <span  title="<?php _e('Licenses available for this item','tainacan') ?>"
                       data-toggle="tooltip" data-placement="bottom" class="glyphicon glyphicon-question-sign"></span>
                    </a>
                    <a id='required_field_license'  style="padding: 3px;" >
                                &nbsp; <span  title="<?php echo __('This metadata is required!','tainacan')?>"
                               data-toggle="tooltip" data-placement="top" >*</span>
                    </a>
                    <a id='ok_field_license'  style="display: none;padding: 3px;margin-left: -30px;" >
                            &nbsp; <span class="glyphicon glyphicon-ok-circle" title="<?php echo __('Field filled successfully!','tainacan')?>"
                           data-toggle="tooltip" data-placement="top" ></span>
                    </a>
                    <input type="hidden"
                                 id='core_validation_license'
                                 class='core_validation'
                                 value='false'>
                    <input type="hidden"
                                 id='core_validation_license_message'
                                 value='<?php echo sprintf(__('The field license is required','tainacan'),$property['name']); ?>'>
                </h2>
                <div id="show_form_licenses"></div>
                <input type="hidden" class="auto-save" id="property_license_id" value="<?php echo $view_helper->terms_fixed['license']->term_id ?>">
             </div>
             <!-- TAINACAN: votacoes do item -->
             <div id="create_list_ranking_<?php echo $object_id ?>"></div>
            </div>
<!-- TAINACAN: FIM ACCORDEON -->
            <?php if($view_helper->hide_main_container): ?>
                <br><br>
                 <!--button onclick="back_main_list();" style="margin-bottom: 20px;"  class="btn btn-default btn-lg pull-left"><b><?php _e('Back','tainacan') ?></b></button-->
                <button type="button" onclick="back_main_list();"
                        style="margin-bottom: 20px;color" class="btn btn-default btn-lg pull-left"><?php _e('Cancel','tainacan'); ?></button>
                <div id="submit_container">
                    <button type="submit" id="submit" style="margin-bottom: 20px;" class="btn btn-success btn-lg pull-right send-button"><?php _e('Submit','tainacan'); ?></button>
                </div>
                <div id="submit_container_message" style="display: none;">
                     <button type="button" onclick="show_message()" style="margin-bottom: 20px;" class="btn btn-success btn-lg pull-right send-button"><?php _e('Submit','tainacan'); ?></button>
                </div>
            <?php endif; ?>
        </div>
        <?php if($view_helper->mediaHabilitate): ?>
            <!-- Thumbnail e anexos -->
            <div class="col-md-3" id="mediaHabilitateContainer" style="display: none; background: white;border: 3px solid #E8E8E8;font: 11px Arial;;padding: 1% 2% 0px 15px;margin-top: 0px">
                <h4>
                   <?php echo ($view_helper->terms_fixed['thumbnail']) ? $view_helper->terms_fixed['thumbnail']->name :  _e('Thumbnail','tainacan') ?>
               </h4>
                <hr>
                <div id="thumnbail_place"  style="margin-top:15px;">
                        <input type="hidden" name="thumbnail_url" id="thumbnail_url" value="">
                        <div id="image_side_create_object">
                        </div>
                        <input type="file"
                               id="object_thumbnail"
                               name="object_thumbnail"
                               class="form-control auto-save">
                </div>
                <br><br>
                <h4>
                   <?php echo ($view_helper->terms_fixed['attachments']) ? $view_helper->terms_fixed['attachments']->name :  _e('Attachments','tainacan') ?>
               </h4>
                <hr>
                 <div >
                     <center>
                     <div id="dropzone_new" <?php ($socialdb_collection_attachment=='no') ? print_r('style="display:none"') : '' ?>
                         class="dropzone"
                         style="margin-bottom: 15px;min-height: 150px;padding-top: 0px;">
                                <div class="dz-message" data-dz-message>
                                    <span style="text-align: center;vertical-align: middle;">
                                        <h3>
                                            <span class="glyphicon glyphicon-upload"></span>
                                            <b><?php _e('Drop Files','tainacan')  ?></b>
                                                <?php _e('to upload','tainacan')  ?>
                                        </h3>
                                        <h4>(<?php _e('or click','tainacan')  ?>)</h4>
                                    </span>
                                </div>
                     </div>
                     </center>
                 </div>
            </div>
        <?php endif; ?>
        <div style="<?php echo ($view_helper->hide_main_container)?'display:none;':'' ?>" class="col-md-9 add-item-container">
            <h3>
                <?php if(has_action('label_add_item')): ?>
                       <?php do_action('label_add_item',$object_name) ?>
                <?php else: ?>
                      <?php _e('Create new item - Write text','tainacan') ?>
                <?php endif; ?>
                <button type="button" onclick="back_main_list();"class="btn btn-default pull-right">
                    <b><?php _e('Back','tainacan') ?></b>
                </button>
                <small id="draft-text"></small>
            </h3>
            <hr>
            <?php
            //se for no modo dois containeres
            if(!$mode):
            ?>
                <div <?php echo $view_helper->get_visibility($view_helper->terms_fixed['title']) ?>
                    class="form-group">
                    <label for="object_name">
                        <?php echo ($view_helper->terms_fixed['title']) ? $view_helper->terms_fixed['title']->name :  _e('Title','tainacan') ?>
                    </label>
                    <input class="form-control auto-save"
                        <?php echo ($view_helper->get_visibility($view_helper->terms_fixed['title'])=='')?'required="required"':'' ?>
                           type="text"
                           id="object_name"
                           name="object_name"
                           placeholder="<?php _e('Item name','tainacan'); ?>">
                </div>
                <!-- Tainacan: type do objeto -->
                <div class="form-group"
                    <?php echo $view_helper->get_visibility($view_helper->terms_fixed['type']) ?>
                    <?php do_action('item_from_attributes') ?>>
                    <label for="object_name">
                        <?php echo ($view_helper->terms_fixed['type']) ? $view_helper->terms_fixed['type']->name :  _e('Type','tainacan') ?>
                    </label><br>
                    <input type="radio"
                           disabled="disabled"
                           checked="checked"
                           value="text"
                           required>&nbsp;<?php _e('Text','tainacan'); ?>
                    <input type="radio"
                           disabled="disabled"
                           value="video" >&nbsp;<?php _e('Video','tainacan'); ?>
                    <input type="radio"
                           disabled="disabled"
                           value="image" required>&nbsp;<?php _e('Image','tainacan'); ?>
                    <input type="radio"
                           disabled="disabled"
                           value="pdf" required>&nbsp;<?php _e('PDF','tainacan'); ?>
                    <input type="radio"
                           disabled="disabled"
                           value="audio" required>&nbsp;<?php _e('Audio','tainacan'); ?>
                    <input type="radio"
                           disabled="disabled"
                           value="other"  required>&nbsp;<?php _e('Other','tainacan'); ?>
                    <br>
                </div>
               <!-- TAINACAN: Campo com o ckeditor para items do tipo texto -->
               <div id="object_content_text"
                     <?php echo $view_helper->get_visibility($view_helper->terms_fixed['content']) ?>
                    class="form-group" <?php do_action('item_content_attributes') ?>>
                    <label for="object_editor">
                        <?php echo ($view_helper->terms_fixed['content']) ? $view_helper->terms_fixed['content']->name :  _e('Content','tainacan') ?>
                    </label>
                    <textarea class="form-control auto-save" id="object_editor" name="object_editor" placeholder="<?php _e('Object Content','tainacan'); ?>">
                    </textarea>
                </div>
                <!-- TAINACAN: UPLOAD DE ANEXOS DOS ITEMS -->
                <div  <?php echo $view_helper->get_visibility($view_helper->terms_fixed['attachments']) ?>
                    class="form-group">
                    <label for="attachments">
                        <?php echo ($view_helper->terms_fixed['attachments']) ? $view_helper->terms_fixed['attachments']->name :  _e('Attachments','tainacan') ?>
                    </label>
                    <div <?php do_action('item_attachments_attributes') ?>
                        id="dropzone_new" <?php ($socialdb_collection_attachment=='no') ? print_r('style="display:none"') : '' ?>
                        class="dropzone"
                        style="margin-bottom: 15px;min-height: 150px;padding-top: 0px;">
                            <div class="dz-message" data-dz-message>
                             <span style="text-align: center;vertical-align: middle;">
                                 <h2>
                                     <span class="glyphicon glyphicon-upload"></span>
                                     <b><?php _e('Drop Files','tainacan')  ?></b>
                                         <?php _e('to upload','tainacan')  ?>
                                 </h2>
                                 <h4>(<?php _e('or click','tainacan')  ?>)</h4>
                             </span>
                         </div>
                    </div>
                </div>
            <?php endif; ?>
                <!--button onclick="back_main_list();" style="margin-bottom: 20px;"  class="btn btn-default btn-lg pull-left"><b><?php _e('Back','tainacan') ?></b></button-->
                <button type="button"
                        onclick="back_main_list(<?php echo $object_id ?>);"
                        style="margin-bottom: 20px;color"
                        class="btn btn-default btn-lg pull-left">
                            <?php _e('Discard','tainacan'); ?>
                </button>
                <div id="submit_container">
                    <button type="submit"
                            id="submit"
                            style="margin-bottom: 20px;"
                            class="btn btn-success btn-lg pull-right send-button">
                                <?php _e('Save','tainacan'); ?></button>
                </div>
                <div id="submit_container_message" style="display: none;">
                     <button type="button" onclick="show_message()" style="margin-bottom: 20px;" class="btn btn-success btn-lg pull-right send-button"><?php _e('Save','tainacan'); ?></button>
                </div>
        </div>
    </div>
</form>
