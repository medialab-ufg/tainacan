<?php
$_src_ = get_template_directory_uri();
$col_controller = $_src_ . "/controllers/collection/collection_controller.php";
?>
<!-- TAINACAN: Modal para criação de colecao -->
<div class="modal fade" id="newCollection" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content new-collection">

            <div class="modal-header">
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'> <span aria-hidden='true' class='glyphicon glyphicon-remove-sign'></span> </button>
                <h4 class="modal-title" id="modal-create"> <?php _t('Create Collection',1); ?></h4>
            </div>

            <form onsubmit="$('#newCollection').modal('hide'); show_modal_main();" action="<?php echo $col_controller ?>" method="POST" id="createCollection">
                <div id="form_new_collection">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="collection_name"><?php _t('Collection name', 1); ?></label>
                            <input type="text" required="required" class="form-control" name="collection_name" id="collection_name"
                                   placeholder="<?php _t('Type the name of your collection', 1); ?>">
                        </div>

                        <input type="hidden" name="operation" value="simple_add">
                        <input type="hidden" name="template" id='template_collection' value="none">
                        <input type="hidden" name="collection_object" id='collection_object' value="<?php _t('Item',1); ?>">
                    </div>

                    <div class="modal-footer" style="border-top: 0">
                        <button type="button" data-dismiss="modal" class="btn btn-default pull-left"> <?php _t('Cancel', 1); ?> </button>
                        <button type="submit" class="btn btn-success"><?php _t('Continue', 1); ?></button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="modalImportCollection" tabindex="-1" role="dialog" aria-labelledby="modalImportCollectionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="importCollection">
                <input type="hidden" name="operation" value="importCollection">
                <div class="modal-header">
                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'> <span aria-hidden='true' class='glyphicon glyphicon-remove-sign'></span> </button>
                    <h4 class="modal-title" id="modal-create"> <?php _t('Import Collection',1); ?></h4>
                </div>

                <div class="modal-body">
                    <?php
                    // Adição SELECT BOX
                    if (has_action('add_select_box'))
                        do_action("add_select_box");
                    ?>
                    <div class="form-group">
                        <label for="collection_file"><?php _t('Select the file', 1); ?></label>
                        <input type="file" required="required" class="form-control" name="collection_file" id="collection_file" >
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php _t('Cancel', 1); ?></button>
                    <button type="submit" class="btn btn-primary pull-right"><?php _t('Import', 1); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>