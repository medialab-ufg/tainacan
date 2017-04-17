<style>
    .modal {
        z-index: 1049;
    }
</style>
<div id="meta-metadata_compound" class="modal fade" role="dialog" aria-labelledby="Compounds" style="z-index: 1041;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> <span class="compounds-action"><?php _e('Add', 'tainacan'); ?></span> <?php _e('Compounds Metadata', 'tainacan'); ?> </h4>
            </div>
            <div class="modal-body">
                <form id="submit_form_compounds" name="submit_compounds">
                    <div class="form-group">
                        <label for="compounds_name"><?php _e('Name','tainacan'); ?></label>
                        <input type="text" class="form-control" id="compounds_name" name="compounds_name" required="required" value="">
                    </div>
                    <div class="form-group">
                        <label for="socialdb_property_help"><?php _e('Text helper','tainacan'); ?></label>
                        <input type="text" class="form-control" id="socialdb_property_help" name="socialdb_property_help" />
                    </div>
                    <div class="form-group col-md-12 no-padding">
                        <label class="col-md-6 no-padding" for="socialdb_property_help"><?php _e('Ordenation','tainacan'); ?></label>
                        <label class="col-md-6 " for="socialdb_property_help"><?php _e('Select the metadata below','tainacan'); ?></label>
                        <div  class="col-md-6 no-padding" style="height: auto;">
                             <ul id="compounds_properties_ordenation" class="metadata-container no-padding" ></ul>
                         </div>
                        <div class="col-md-6 no-padding">
                            <div style='height: 242px;' id="dynatree_properties_filter"></div>
                            <input type="hidden" id="compounds_id" name="compounds_id" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="socialdb_event_property_tab"><?php _e('Select the tab','tainacan'); ?></label>
                        <select class="socialdb_event_property_tab form-control" name="socialdb_event_property_tab">
                        </select>
                    </div>
                    <div class="form-group" >
                        <label for="property_term_required" ><?php _e('Elements Quantity','tainacan'); ?></label> :
                        <input type="radio" 
                               name="cardinality" 
                               id="socialdb_property_compounds_cardinality_1" 
                               checked="checked"  value="1">&nbsp;<?php _e('Unic value','tainacan') ?>
                        <input type="radio" 
                               name="cardinality" 
                               id="socialdb_property_compounds_cardinality_n" 
                               value="n">&nbsp;<?php _e('Multiple values','tainacan') ?>
                    </div>
                    <div class="form-group" >
                        <label for="property_term_required" style="margin-right: 10px;" ><?php _e('Visualization','tainacan'); ?> : </label>
                        &nbsp;<input type="radio" name="socialdb_event_property_visualization" id="socialdb_property_compounds_visualization_public" checked="checked"  value="public">&nbsp;<?php _e('Public','tainacan') ?>
                        &nbsp;<input type="radio" name="socialdb_event_property_visualization" id="socialdb_property_compounds_visualization_restrict" value="restrict">&nbsp;<?php _e('Restrict','tainacan') ?>
                    </div>
                    <div class="form-group" >
                        <input type="radio" 
                               name="required" 
                               id="property_compounds_required_false" 
                               checked="checked"
                               value="false">&nbsp;
                        <b><?php _e('Not Required','tainacan'); ?></b>
                        <input type="radio" 
                               name="required" 
                               id="property_compounds_required_true" 
                               value="true">&nbsp;
                        <b><?php _e('Required to all fields','tainacan'); ?></b>
                        <input type="radio" 
                               name="required" 
                               id="property_compounds_required_true_field" 
                               value="true_one_field">&nbsp;
                        <b><?php _e('Required to at least one field','tainacan'); ?></b>
                    </div>
                    <input type="hidden" id="compound_id" name="compound_id" value="">
                    <input type="hidden" id="compounds_collection_id" name="collection_id" value="<?php echo $collection_id; ?>">
                    <input type="hidden" id="operation_property_compounds" name="operation" value="add_property_compounds">
                    <input type="hidden" name="property_category_id" id="property_category_id" value="<?php echo $category->term_id; ?>"><br>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left close-modal" data-dismiss="modal"><?php _e('Cancel','tainacan') ?></button>
                <button type="submit" class="btn btn-primary action-continue" form="submit_form_compounds">
                    <?php _e('Continue','tainacan') ?>
                </button>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_remove_ranking" tabindex="-1" role="dialog" aria-labelledby="modal_remove_ranking" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="submit_delete_ranking">
                <input type="hidden" id="ranking_delete_collection_id" name="collection_id" value="">
                <input type="hidden" id="ranking_delete_id" name="ranking_delete_id" value="">
                <input type="hidden" id="operation" name="operation" value="delete_ranking">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php echo __('Removing ranking', 'tainacan'); ?></h4>
                </div>
                <div class="modal-body">
                    <?php echo __('Confirm the exclusion of ', 'tainacan'); ?>&nbsp;<span id="deleted_ranking_name"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo __('Save', 'tainacan'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>