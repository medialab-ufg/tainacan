<!--------------------------------------------------------------- Definição de janelas modais --------------------------------------------------------------->

<!-- TAINACAN: modal padrao bootstrap para adicao de categorias    -->
<div class="modal fade" id="modalAddCategoria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="submit_adicionar_category_single">
                <input type="hidden" id="category_single_add_id" name="category_single_add_id" value="">
                <input type="hidden" id="operation_event_create_category" name="operation" value="add_event_term_create">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-plus"></span>
                        <?php _e('Add Category', 'tainacan'); ?>
                        <?php do_action('add_option_in_add_category'); ?>
                    </h4>
                </div>
                <div id="form_add_category">
                    <div class="modal-body">

                        <div class="create_form-group">
                            <label for="category_single_name"><?php _e('Category name', 'tainacan'); ?></label>
                            <input type="text" class="form-control" id="category_single_name" name="socialdb_event_term_suggested_name" required="required" placeholder="<?php _e('Category name', 'tainacan'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="category_single_parent_name"><?php _e('Category parent', 'tainacan'); ?></label>
                            <input disabled="disabled" type="text" class="form-control" id="category_single_parent_name" placeholder="<?php _e('Right click on the tree and select the category as parent', 'tainacan'); ?>" name="category_single_parent_name">
                            <input type="hidden"  id="category_single_parent_id"  name="socialdb_event_term_parent" value="0" >
                        </div>
                        <div class="form-group">
                            <label for="category_add_description"><?php _e('Category description', 'tainacan'); ?>&nbsp;<span style="font-size: 10px;">(<?php _e('Optional', 'tainacan'); ?>)</span></label>
                            <textarea class="form-control" id="category_add_description" name="socialdb_event_term_description"
                                      placeholder="<?php _e('Describe your category', 'tainacan'); ?>"></textarea>
                        </div>
                        <input type="hidden" id="category_single_add_collection_id" name="socialdb_event_collection_id" value="<?php echo get_the_ID(); ?>">
                        <input type="hidden" id="category_single_add_create_time" name="socialdb_event_create_date" value="<?php echo mktime(); ?>">
                        <input type="hidden" id="category_single_add_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>">
                        <input type="hidden" id="category_single_add_dynatree_id" name="dynatree_id" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php _t('Close', 1); ?></button>
                        <button type="submit" class="btn btn-primary"><?php _t('Save', 1); ?></button>
                    </div>
                </div>
                <div id="another_option_category" style="display: none;">
                    <?php do_action('show_option_in_add_category'); ?>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- TAINACAN: modal padrao bootstrap para edicao de categorias    -->
<div class="modal fade" id="modalEditCategoria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="submit_edit_category_single">
                <input type="hidden" id="category_single_edit_id" name="socialdb_event_term_id" value="">
                <input type="hidden" id="operation_event_edit_category" name="operation" value="add_event_term_edit">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-pencil"></span>&nbsp;<?php echo __('Edit Category', 'tainacan'); ?></h4>
                </div>
                <div class="modal-body row">
                    <div class="col-md-4">
                        <div id="dynatree_modal_edit">
                        </div>
                        <?php do_action('insert_custom_dynatree_edit_category') ?>
                    </div>
                    <div class="col-md-8">
                        <div id="form_simple_eidt_category">
                            <div class="create_form-group">
                                <label for="category_single_edit_name"><?php _e('Category name', 'tainacan'); ?></label>
                                <input type="text" class="form-control" id="category_single_edit_name" name="socialdb_event_term_suggested_name" required="required" placeholder="<?php _e('Category name', 'tainacan'); ?>">
                                <input type="hidden"  id="socialdb_event_previous_name"  name="socialdb_event_term_previous_name" value="0" >
                            </div>
                            <div class="form-group">
                                <label for="category_single_parent_name_edit"><?php _e('Category parent', 'tainacan'); ?></label>
                                <input disabled="disabled" type="text" class="form-control" id="category_single_parent_name_edit" name="category_single_term_parent_name_edit" placeholder="<?php _e('Click on the tree and select the category as parent', 'tainacan'); ?>" >
                                <input type="hidden"  id="category_single_parent_id_edit"  name="socialdb_event_term_suggested_parent" value="0" >
                                <input type="hidden"  id="socialdb_event_previous_parent"  name="socialdb_event_term_previous_parent" value="0" >
                            </div>
                            <div class="form-group" <?php do_action('description_category_view') ?>>
                                <label for="category_parent_name"><?php _e('Category description', 'tainacan'); ?>&nbsp;<span style="font-size: 10px;">(<?php _e('Optional', 'tainacan'); ?>)</span></label>
                                <textarea class="form-control"
                                          id="category_edit_description"
                                          placeholder="<?php _e('Describe your category', 'tainacan'); ?>"
                                          name="socialdb_event_term_description" ></textarea>
                            </div>
                        </div>
                        <?php do_action('insert_fields_edit_modal_category') ?>
                        <button type="button" onclick="list_category_property_single()" id="show_category_property_single" class="btn btn-primary"><?php _e('Category Properties', 'tainacan'); ?></button>
                        <!-- Sinonimos -->
                        <br><br>
                        <a onclick="toggle_container_synonyms('#synonyms_container')" <?php do_action('synonyms_category_view') ?> style="cursor: pointer;">
                            <?php _e('Synonyms', 'tainacan') ?>
                            <span class="glyphicon glyphicon-triangle-bottom"></span>
                        </a>
                        <div style="display: none;" id="synonyms_container">
                            <div id="dynatree_synonyms" style="height: 200px;overflow-y: scroll;"></div>
                            <input type="hidden" id="category_synonyms" name="socialdb_event_term_synonyms">
                        </div>
                        <!-- Fim: Sinonimos -->
                        <input type="hidden" id="category_single_edit_collection_id" name="socialdb_event_collection_id" value="<?php echo get_the_ID(); ?>">
                        <input type="hidden" id="category_single_edit_time" name="socialdb_event_create_date" value="<?php echo mktime(); ?>">
                        <input type="hidden" id="category_single_edit_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>">
                        <input type="hidden" id="category_single_edit_dynatree_id" name="dynatree_id" value="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo __('Save', 'tainacan'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- modal propriedades -->
<div class="modal fade bs-example-modal-lg" id="single_modal_category_property"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-lg">
        <div class="modal-content">
            <div id="single_category_property" style="max-height: 450px;overflow-x: scroll;">
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<!-- modal exluir -->
<div class="modal fade" id="modalExcluirCategoria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="submit_delete_category_single">
                <input type="hidden" id="category_single_delete_id" name="socialdb_event_term_id" value="">
                <input type="hidden" id="operation" name="operation" value="add_event_term_delete">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php echo __('Remove Category', 'tainacan'); ?></h4>
                </div>
                <div class="modal-body">
                    <?php echo __('Confirm the exclusion of ', 'tainacan'); ?>&nbsp;<b><span id="delete_category_single_name"></span></b>?
                </div>
                <input type="hidden" id="category_single_delete_collection_id" name="socialdb_event_collection_id" value="<?php echo get_the_ID(); ?>">
                <input type="hidden" id="category_single_delete_time" name="socialdb_event_create_date" value="<?php echo mktime(); ?>">
                <input type="hidden" id="category_single_delete_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>">
                <input type="hidden" id="category_single_delete_dynatree_id" name="dynatree_id" value="">

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo __('Delete', 'tainacan'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_send_files_items_zip" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="submit_files_item_zip">
                <input type="hidden" id="operation" name="operation" value="send_files_item_zip">
                <input type="hidden" name="collection_id" value="<?php echo get_the_ID() ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-plus"></span>&nbsp;<?php echo __('Import files from zip', 'tainacan'); ?></h4>
                </div>
                <div class="modal-body">
                    <input type="radio" onchange="changeFormZip(this.value)" id="sendFileItemZip" name="sendfile_zip" value="file" checked="checked"/> <?php echo __('Send File', 'tainacan'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" onchange="changeFormZip(this.value)" id="sendUrlItemZip" name="sendfile_zip" value="url"/> <?php echo __('In Server', 'tainacan'); ?>
                    <br>
                    <div id="div_send_file_zip">
                        <input type="file" accept=".zip" name="file_zip">
                    </div>
                    <div id="div_in_server_zip" style="display:none;">
                        <input type="text" name="file_path" placeholder="<?php echo __('Insert file path in this server', 'tainacan'); ?>" class="form-control">
                    </div>
                    <br><br>
                    <div>
                        <input type="checkbox" onclick="changeMetadataZipDiv()" id="zip_folder_hierarchy" name="zip_folder_hierarchy" value="1">&nbsp;<?php echo __('Import Folder Hierarchy', 'tainacan'); ?>
                    </div>
                    <div id="metadata_zip_div" style="display:none;">
                        <input type="radio" onchange="changeFormZipMetadata(this.value)" id="createMetaItemZip" name="meta_zip" value="create" checked="checked"/> <?php echo __('Create Metadata', 'tainacan'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" onchange="changeFormZipMetadata(this.value)" id="chooseMetaItemZip" name="meta_zip" value="choose"/> <?php echo __('Choose Metadata', 'tainacan'); ?>
                        <br>
                        <div id="div_create_metadata_zip">
                            <input type="text" name="meta_name" placeholder="<?php echo __('Insert value', 'tainacan'); ?>" class="form-control">
                        </div>
                        <div id="div_choose_metadata_zip" style="display:none;">
                            <select id="chosen_meta" name="chosen_meta" class="form-control">
                                <option>[<?php echo __('Select', 'tainacan'); ?>]</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo __('Import', 'tainacan'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- TAINACAN: modal padrao bootstrap para adicao de items sem url    -->
<!-- modal Adicionar Rapido -->
<div class="modal fade" id="modal_import_objet_url" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="form_url_import">
                <input type="hidden" id="socialdb_event_collection_id_tag" name="socialdb_event_collection_id" value="">
                <input type="hidden" id="operation" name="operation" value="add">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <input type="text" id="title_insert_object_url" class="form-control input-lg" value="">
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4" id="image_side"></div>
                        <div class="col-md-8">
                            <textarea rows="6" class="form-control" id="description_insert_object_url" ></textarea>
                        </div>
                        <input type="hidden" id="thumbnail_url" name="thumbnail_url" value="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                    <a href="#" id="save_object_url" class="btn btn-primary"><?php echo __('Save', 'tainacan'); ?></a>
                </div>
            </div>
            <div style="display: none;" id="loader_import_object">
                <center><img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>"><h3><?php _e('Importing Object...', 'tainacan') ?></h3></center>
            </div>
        </div>
    </div>
</div>

<!-- TAINACAN: modal padrao bootstrap para adicao de tags    -->
<!-- modal Adicionar Tag -->
<div class="modal fade" id="modalAdicionarTag" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="submit_adicionar_tag_single">
                <input type="hidden" id="operation_tag_add" name="operation" value="add_event_tag_create">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-plus"></span>&nbsp;<?php echo __('Add Tag', 'tainacan'); ?></h4>
                </div>
                <div class="modal-body">

                    <div class="create_form-group">
                        <label for="tag_single_name"><?php _e('Tag', 'tainacan'); ?></label>
                        <input type="text" class="form-control" id="tag_single_name" name="socialdb_event_tag_suggested_name" required="required" placeholder="<?php _e('Tag name', 'tainacan'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="category_parent_name"><?php _e('Tag description', 'tainacan'); ?>&nbsp;<span style="font-size: 10px;">(<?php _e('Optional', 'tainacan'); ?>)</span></label>
                        <textarea class="form-control"
                                  id="tag_add_description"
                                  placeholder="<?php _e('Describe your tag', 'tainacan'); ?>"
                                  name="socialdb_event_tag_description" ></textarea>
                    </div>
                    <input type="hidden" id="tag_single_add_collection_id" name="socialdb_event_collection_id" value="<?php echo get_the_ID(); ?>">
                    <input type="hidden" id="tag_single_add_create_time" name="socialdb_event_create_date" value="<?php echo mktime(); ?>">
                    <input type="hidden" id="tag_single_add_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo __('Save', 'tainacan'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- TAINACAN: modal padrao bootstrap para edicao de tags   -->
<div class="modal fade" id="modalEditTag" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="submit_edit_tag_single">
                <input type="hidden" id="tag_single_edit_id" name="socialdb_event_tag_id" value="">
                <input type="hidden" id="operation_tag_edit" name="operation" value="add_event_tag_edit">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-pencil"></span>&nbsp;<?php echo __('Edit Tag', 'tainacan'); ?></h4>
                </div>
                <div class="modal-body row">
                    <div class="col-md-12">
                        <div class="create_form-group">
                            <label for="tag_single_edit_name"><?php _e('Tag name', 'tainacan'); ?></label>
                            <input type="text" class="form-control" id="tag_single_edit_name" name="socialdb_event_tag_suggested_name" required="required" placeholder="<?php _e('Tag name', 'tainacan'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="category_parent_name"><?php _e('Tag description', 'tainacan'); ?>&nbsp;<span style="font-size: 10px;">(<?php _e('Optional', 'tainacan'); ?>)</span></label>
                            <textarea class="form-control"
                                      id="tag_edit_description"
                                      placeholder="<?php _e('Describe your tag', 'tainacan'); ?>"
                                      name="socialdb_event_tag_description" ></textarea>
                        </div>
                        <!-- Sinonimos -->
                        <a onclick="toggle_container_synonyms('#synonyms_container_tag')" style="cursor: pointer;">
                            <?php _e('Synonyms', 'tainacan') ?>
                            <span class="glyphicon glyphicon-triangle-bottom"></span>
                        </a>
                        <div style="display: none;" id="synonyms_container_tag">
                            <div id="dynatree_synonyms_tag" style="height: 200px;overflow-y: scroll;"></div>
                            <input type="hidden" id="tag_synonyms" name="socialdb_event_tag_synonyms">
                        </div>
                        <!-- Fim: Sinonimos -->
                        <input type="hidden" id="tag_single_edit_collection_id" name="socialdb_event_collection_id" value="<?php echo get_the_ID(); ?>">
                        <input type="hidden" id="tag_single_edit_time" name="socialdb_event_create_date" value="<?php echo mktime(); ?>">
                        <input type="hidden" id="tag_single_edit_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo __('Edit', 'tainacan'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- TAINACAN: modal padrao bootstrap para exclusao de tags   -->
<div class="modal fade" id="modalExcluirTag" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="submit_delete_tag_single">
                <input type="hidden" id="tag_single_delete_id" name="socialdb_event_tag_id" value="">
                <input type="hidden" id="operation_tag_delete" name="operation" value="add_event_tag_delete">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php echo __('Remove Tag', 'tainacan'); ?></h4>
                </div>
                <div class="modal-body">
                    <?php echo __('Confirm the exclusion of ', 'tainacan'); ?><span id="delete_tag_single_name"></span>?
                </div>
                <input type="hidden" id="tag_single_delete_collection_id" name="socialdb_event_collection_id" value="<?php echo get_the_ID(); ?>">
                <input type="hidden" id="tag_single_delete_time" name="socialdb_event_create_date" value="<?php echo mktime(); ?>">
                <input type="hidden" id="tag_single_delete_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>">

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo __('Delete', 'tainacan'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- TAINACAN: modal padrao bootstrap para exibição do loading de importação   -->
<div class="modal fade" id="modalImportLoading" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <center>
                <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                <h3><?php _e('Please wait...', 'tainacan') ?></h3>
                <div id="divprogress">
                    <progress id='progressbarmapas' value='0' max='100' style='width: 100%;'></progress><br>
                </div>
            </center>
        </div>
    </div>
</div>

<!-- TAINACAN: modal padrao bootstrap para confirmação de importação Mapas Culturais   -->
<div class="modal fade" id="modalImportConfirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content"> <!--Conteúdo da janela modal-->

            <div class="modal-header"><!--Cabeçalho-->
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only"><?php _e('Do you really want to close?', 'tainacan') ?></span>
                </button>

                <h4 class="modal-title text-center"><?php _e('Count of elements', 'tainacan') ?></h4>
            </div><!--Fim cabeçalho-->

            <div class="modal-body"><!--Conteúdo-->
                <div class="text-center">
                    <dl class="dl-horizontal">
                        <dt><?php _e('Agents', 'tainacan') ?>: </dt>
                        <dd id="agents">00</dd>

                        <dt><?php _e('Projects', 'tainacan') ?>: </dt>
                        <dd id="projects">00</dd>

                        <dt><?php _e('Events', 'tainacan') ?>: </dt>
                        <dd id="events">00</dd>

                        <dt><?php _e('Spaces', 'tainacan') ?></dt>
                        <dd id="spaces">00</dd>
                    </dl>
                </div>
            </div><!--Fim conteúdo-->

            <div class="modal-footer"><!--Rodapé-->
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    <?php _e('Cancel', 'tainacan'); ?>
                </button>

                <button type="button" class="btn btn-primary"
                        onclick="import_mapas_culturais($('#url_mapa_cultural').val().trim())"
                        id="submit_mapa_cultural_url"
                        class="btn btn-primary tainacan-blue-btn-bg pull-right">
                    <?php _e('Import', 'tainacan'); ?>
                </button>

            </div><!--Fim rodapé-->

        </div>
    </div>
</div>

<!-- TAINACAN: modal padrao bootstrap para exibição dos itens importados do Mapa Cultural   -->
<div class="modal fade" id="modalImportFinished" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content"> <!--Conteúdo da janela modal-->
            <div class="modal-header"><!--Cabeçalho-->
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only"><?php _e('Do you really want to close?', 'tainacan') ?></span>
                </button>

                <h4 class="modal-title text-center"><?php _e('Imported elements', 'tainacan') ?></h4>
            </div><!--Fim cabeçalho-->

            <div class="modal-body"><!--Conteúdo-->
                <div class="text-center">
                    <h4 id="ontology_name"></h4>
                    <dl class="dl-horizontal">
                        <dt><?php _e('Classes', 'tainacan') ?>: </dt>
                        <dd id="classes">00</dd>

                        <dt><?php _e('Datatype', 'tainacan') ?>: </dt>
                        <dd id="datatype">00</dd>

                        <dt><?php _e('Object Property', 'tainacan') ?>: </dt>
                        <dd id="object_property">00</dd>

                        <dt><?php _e('Individuals', 'tainacan') ?></dt>
                        <dd id="individuals">00</dd>
                    </dl>
                </div>
            </div><!--Fim conteúdo-->

            <div class="modal-footer"><!--Rodapé-->
                <button type="button" class="btn btn-primary"
                        onclick="go_to_ontology()"
                        id="go_to_ontology"
                        class="btn btn-primary tainacan-blue-btn-bg pull-right">
                    <?php _e('Go to ontology', 'tainacan'); ?>
                </button>

            </div><!--Fim rodapé-->
        </div>
    </div>
</div>

<!-- TAINACAN: modal padrao bootstrap para demonstracao de execucao de processos, utilizado em varias partes do socialdb   -->
<div class="modal fade" id="modalImportSocialnetworkClean" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <center>
                <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
                <h3><?php _e('Undoing actions...', 'tainacan') ?></h3>
            </center>
        </div>
    </div>
</div>

<!-- TAINACAN: modal padrao bootstrap para redefinicao de senha -->
<div class="modal fade" id="myModalPasswordReset" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="formUserPasswordReset" name="formUserPasswordReset" >
                <input type="hidden" name="operation" value="change_password">
                <input type="hidden" name="password_user_id" id="password_user_id" value=""/>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php _e('Change Password!', 'tainacan'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="old_password_reset"><?php _e('Old Password', 'tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                        <input type="password" required="required" class="form-control" name="old_password_reset" id="old_password_reset" placeholder="<?php _e('Type here the old password', 'tainacan'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="new_password_reset"><?php _e('New Password'); ?><span style="color: #EE0000;"> *</span></label>
                        <input type="password" required="required" class="form-control" name="new_password_reset" id="new_password_reset" placeholder="<?php _e('Type here the new password', 'tainacan'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="new_check_password_reset"><?php _e('Confirm new password'); ?><span style="color: #EE0000;"> *</span></label>
                        <input type="password" required="required" class="form-control" name="new_check_password_reset" id="new_check_password_reset" placeholder="<?php _e('Type here your new password again', 'tainacan'); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan'); ?></button>
                    <button type="submit" class="btn btn-primary" onclick="check_passwords();
                                return false;"><?php _e('Submit', 'tainacan'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalShowFullDescription" tabindex="-1" role="dialog" aria-labelledby="modalShowFullDescriptionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo get_template_directory_uri() ?>/controllers/collection/collection_controller.php" method="POST">
                <input type="hidden" name="operation" value="simple_add">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php _e('Description', 'tainacan'); ?></h4>
                </div>
                <div class="modal-body" style="overflow: scroll; max-height: 500px;" id="modalShowFullDescription_body">
                    <?php echo get_the_content(); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para importacao das redes sociais -->
<div class="modal fade" id="modalshowModalImportSocialNetwork" tabindex="-1" role="dialog" aria-labelledby="modalshowModalImportSocialNetworkLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="operation" value="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php _e('Import Social Media', 'tainacan'); ?></h4>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs">
                        <li role="presentation" class="active"><a href="#aba-youtube" aria-controls="property_data_tab" role="tab" data-toggle="tab"><?php _e('Youtube', 'tainacan') ?></a></li>
                        <li role="presentation"><a href="#aba-flickr" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Flickr', 'tainacan') ?></a></li>
                        <li role="presentation"><a href="#aba-faceboook" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Facebook', 'tainacan') ?></a></li>
                        <li role="presentation"><a href="#aba-instagram" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Instagram', 'tainacan') ?></a></li>
                        <li role="presentation"><a href="#aba-vimeo" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Vimeo', 'tainacan') ?></a></li>
                    </ul>
                    <br>
                    <div class="tab-content">
                        <!-- Aba do youtube-->
                        <div id="aba-youtube" class="tab-pane fade in active">
                            <!--h3><?php _e("Youtube Channels", 'tainacan'); ?></h3>
                                <div id="list_youtube_channels">
                                    <table  class="table table-bordered" style="background-color: #d9edf7;">
                                        <th><?php _e('Identifier', 'tainacan'); ?></th>
                                        <th><?php _e('Playlist', 'tainacan'); ?></th>
                                        <th><?php _e('Edit', 'tainacan'); ?></th>
                                        <th><?php _e('Delete', 'tainacan'); ?></th>
                                        <th><?php _e('Import', 'tainacan'); ?></th>
                                        <th><?php _e('Update', 'tainacan'); ?></th>
                                        <tbody id="table_youtube_identifiers" >
                                        </tbody>
                                    </table>
                                </div-->

                            <label for="channel_identifier"><?php _e('Entry youtube video url', 'tainacan'); ?></label>
                            <input type="text"  name="youtube_video_url" id="youtube_video_url" placeholder="<?php _e('Type here', 'tainacan'); ?>" class="form-control" /><br>
                            <input type="button" id="btn_youtube_video_url" onclick="import_youtube_video_url()" name="btn_youtube_video_url" class="btn btn-success pull-left" value="<?php _e('Import', 'tainacan'); ?>"  /><br><br>
                            <hr>
                            <label for="channel_identifier"><?php _e('Entry channel youtube identifeir', 'tainacan'); ?></label>
                            <input type="text"  name="channel_identifier" id="youtube_identifier_input" placeholder="<?php _e('Type here', 'tainacan'); ?>" class="form-control" required /><br>
                            <label for="youtube_playlist_identifier_input"><?php _e('Entry playlist youtube identifeir', 'tainacan'); ?></label>
                            <input type="text"  name="youtube_playlist_identifier_input" id="youtube_playlist_identifier_input" placeholder="<?php _e('Type here', 'tainacan'); ?>" class="form-control"/>
                            <span class="help-block"><b><?php _e('Help: ', 'tainacan'); ?></b><?php _e('Type here to get a specific playlist or leave blank to get all', 'tainacan'); ?></span><br>
                            <input type="button" id="btn_identifiers_youtube" onclick="import_youtube_channel()" name="addChannel" class="btn btn-success pull-left" value="<?php _e('Import', 'tainacan'); ?>"  />
                            <br><br>
                        </div>

                        <!-- Aba do flickr-->
                        <div id="aba-flickr" class="tab-pane fade">
                            <!--h3><?php _e("Flickr Profiles", 'tainacan'); ?></h3>
                                <div id="list_perfil_flickr">
                                    <table  class="table table-bordered" style="background-color: #d9edf7;">
                                        <th><?php _e('User Name', 'tainacan'); ?></th>
                                        <th><?php _e('Edit', 'tainacan'); ?></th>
                                        <th><?php _e('Delete', 'tainacan'); ?></th>
                                        <th><?php _e('Import', 'tainacan'); ?></th>
                                        <th><?php _e('Update', 'tainacan'); ?></th>
                                        <tbody id="table_flickr_identifiers" >
                                        </tbody>
                                    </table>
                                </div-->
                            <label for="flickr_identifiers"><?php _e('Entry an user name from a flickr profile', 'tainacan'); ?></label>
                            <input type="text"  name="flickr_identifiers" id="flickr_identifier_input" placeholder="Digite aqui" class="form-control"/><br/>
                            <input type="button" id="btn_identifiers_flickr" onclick="import_flickr()" name="addChannel" class="btn btn-success pull-left" value="<?php _e('Import', 'tainacan'); ?>"  />
                            <br><br>
                        </div>

                        <!-- Aba do facebook-->
                        <div id="aba-faceboook" class="tab-pane fade">
                            <!--h3><?php _e("Facebook Profiles", 'tainacan'); ?></h3-->
                            <?php
                            $config = get_option('socialdb_theme_options');
                            $app['app_id'] = $config['socialdb_fb_api_id'];
                            $app['app_secret'] = $config['socialdb_fb_api_secret'];
                            try {
                                $fb = new Facebook\Facebook([
                                    'app_id' => $app['app_id'],
                                    'app_secret' => $app['app_secret'],
                                    'default_graph_version' => 'v2.3',
                                ]);

                                $helper = $fb->getRedirectLoginHelper();
                                $permissions = ['user_photos', 'email', 'user_likes']; // optional
                                $collection_id = get_the_ID();
                                $loginUrl = $helper->getLoginUrl(get_bloginfo(template_directory) . '/controllers/social_network/facebook_controller.php?collection_id=' . $collection_id . '&operation=getAccessToken', $permissions);
                            } catch (Exception $e) {

                            }

                            //echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
                            ?>
                            <a href="<?php echo $loginUrl; ?>" class="btn btn-success"><?php _e('Import Facebook Photos', 'tainacan'); ?></a>

                        </div>
                        <!-- Aba do instagram-->
                        <div id="aba-instagram" class="tab-pane fade">
                            <!--h3><?php _e("Instagram Profiles", 'tainacan'); ?></h3>
                                <div id="list_perfil_instram">
                                    <table  class="table table-bordered" style="background-color: #d9edf7;">
                                        <th><?php _e('User Name', 'tainacan'); ?></th>
                                        <th><?php _e('Edit', 'tainacan'); ?></th>
                                        <th><?php _e('Delete', 'tainacan'); ?></th>
                                        <th><?php _e('Import', 'tainacan'); ?></th>
                                        <th><?php _e('Update', 'tainacan'); ?></th>
                                        <tbody id="table_instagram_identifiers" >
                                        </tbody>
                                    </table>
                                </div-->
                            <label for="instagram_identifiers"><?php _e('Entry an user name from a instagram profile', 'tainacan'); ?></label>
                            <input type="text"  name="instagram_identifiers" id="instagram_identifier_input" placeholder="<?php _e('Type here', 'tainacan'); ?>" class="form-control"/> <br/>
                            <input type="button" id="btn_identifiers_instagram" onclick="import_instagram()" name="addChannel" class="btn btn-success pull-left" value="<?php _e('Import', 'tainacan'); ?>"  />
                            <br><br>
                        </div>
                        <!-- Aba do vimeo-->
                        <div id="aba-vimeo" class="tab-pane fade">
                            <!--h3><?php _e("Vimeo Profiles", 'tainacan'); ?></h3>
                                <div id="list_perfil_instram">
                                    <table  class="table table-bordered" style="background-color: #d9edf7;">
                                        <th><?php _e('User Name', 'tainacan'); ?></th>
                                        <th><?php _e('Edit', 'tainacan'); ?></th>
                                        <th><?php _e('Delete', 'tainacan'); ?></th>
                                        <th><?php _e('Import', 'tainacan'); ?></th>
                                        <th><?php _e('Update', 'tainacan'); ?></th>
                                        <tbody id="table_instagram_identifiers" >
                                        </tbody>
                                    </table>
                                </div-->
                            <label for="vimeo_identifiers"><?php _e('Entry an user name from a vimeo profile', 'tainacan'); ?></label>
                            <input type="text"  name="vimeo_identifiers" id="vimeo_identifier_input" placeholder="<?php _e('Type here', 'tainacan'); ?>" class="form-control"/><br>
                            <div class="radio">
                                <label><input type="radio" name="optradio_vimeo" value="channels" required="required"><?php _e('Channel', 'tainacan'); ?></label>
                            </div>
                            <div class="radio">
                                <label><input type="radio" name="optradio_vimeo" value="users" required="required"><?php _e('User', 'tainacan'); ?></label>
                            </div><br>
                            <input type="button" id="btn_identifiers_vimeo" onclick="import_vimeo()" name="addChannel" class="btn btn-success pull-left" value="<?php _e('Import', 'tainacan'); ?>"  />
                            <br><br>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan'); ?></button>
                    <!--button type="submit" class="btn btn-primary"><?php _e('Save'); ?></button-->
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para importacao geral -->
<div class="modal fade" id="modalshowModalImportAll" tabindex="-1" role="dialog" aria-labelledby="modalshowModalImportAllLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="operation" value="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php _e('Web Resource', 'tainacan'); ?></h4>
                </div>
                <div class="modal-body">
                    <?php
                    if (!session_id()) {
                        session_start();
                    }

                    $config = get_option('socialdb_theme_options');
                    $app['app_id'] = $config['socialdb_fb_api_id'];
                    $app['app_secret'] = $config['socialdb_fb_api_secret'];
                    try {
                        $fb = new Facebook\Facebook([
                            'app_id' => $app['app_id'],
                            'app_secret' => $app['app_secret'],
                            'default_graph_version' => 'v2.3',
                        ]);

                        $helper = $fb->getRedirectLoginHelper();
                        $permissions = ['user_photos', 'email', 'user_likes']; // optional
                        $collection_id = get_the_ID();
                        $loginUrl = $helper->getLoginUrl(get_bloginfo(template_directory) . '/controllers/social_network/facebook_controller.php?collection_id=' . $collection_id . '&operation=getAccessToken', $permissions);
                    } catch (Exception $e) {

                    }

                    //echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
                    ?>
                    <label for="item_url_import_all"><?php _e('Add item through URL', 'tainacan'); ?></label>
                    <input type="text" onkeyup="verify_import_type()"  name="item_url_import_all" id="item_url_import_all" placeholder="<?php _e('Type here', 'tainacan'); ?>" class="form-control" /><br>
                    <p>
                        <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/facebook.png' ?>" id="facebook_import_icon"/>
                        <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/youtube.png' ?>" id="youtube_import_icon"/>
                        <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/flickr.png' ?>" id="flickr_import_icon"/>
                        <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/vimeo.png' ?>" id="vimeo_import_icon"/>
                        <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/instagram.png' ?>" id="instagram_import_icon"/>
                        <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/files.png' ?>" id="files_import_icon"/>
                        <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/sites.png' ?>" id="sites_import_icon"/>
                    </p>
                    <hr>
                    <div>
                        <p>
                            <?php _e('Through this feature you can enter:', 'tainacan'); ?>
                        </p>
                        <ul>
                            <li><?php _e('Files (ex. pdf, jpg, png, etc)', 'tainacan'); ?></li>
                            <li><?php _e('Sites', 'tainacan'); ?></li>
                            <li><?php _e('Video from Youtube or Vimeo', 'tainacan'); ?></li>
                            <li><?php _e('Multiple videos from a Youtube Channel or Vimeo Channel', 'tainacan'); ?></li>
                            <li><?php _e('Images from Flickr, Facebook or Instagram', 'tainacan'); ?></li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan'); ?></button>
                    <a href="<?php echo $loginUrl; ?>" id="btn_import_fb" style="display: none;" class="btn btn-success"><?php _e('Import', 'tainacan'); ?></a>
                    <button type="button" onclick="importAll_verify()" id="btn_import_allrest" class="btn btn-primary right"><?php _e('Import'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
if (has_action('add_new_modals'))
    do_action('add_new_modals', '');

if (has_filter('tainacan_show_reason_modal'))
    apply_filters('tainacan_show_reason_modal', "");