<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/list_js.php');
?>
<div class="fuelux">
    <div id="MyWizard" class="fuelux wizard">

        <ul class="steps fuelux step-content">
            <a onclick="showCollectionConfiguration('<?php echo get_template_directory_uri() ?>')SA;"><li ><span class="fuelux badge">1</span><?php echo __("Configuration") ?><span class="chevron"></span></li></a>
            <a onclick="showPropertiesConfiguration('<?php echo get_template_directory_uri() ?>');"><li><span class="fuelux badge">2</span><?php echo __("Metadata") ?><span class="fuelux chevron"></span></li></a>
            <a onclick="showRankingConfiguration('<?php echo get_template_directory_uri() ?>');"><li class="active" ><span class="badge badge-info">3</span><?= __("Rankings") ?><span class="fuelux chevron"></span></li></a>
            <a onclick="showSearchConfiguration('<?php echo get_template_directory_uri() ?>');"><li><span class="fuelux badge">4</span><?= __("Searching") ?><span class="fuelux chevron"></span></li></a>
            <a onclick="showDesignConfiguration('<?php echo get_template_directory_uri() ?>');"><li><span class="fuelux badge">5</span><?= __("Design") ?><span class="fuelux chevron"></span></li></a>
        </ul>
        <div class="fuelux actions">
            <a onclick="showPropertiesConfiguration('<?php echo get_template_directory_uri() ?>');" href="#" class="btn btn-mini btn-prev"> <span class="glyphicon glyphicon-chevron-left"></span></i><?php echo __("Previous") ?></a>
            <a onclick="showSearchConfiguration('<?php echo get_template_directory_uri() ?>');" href="#" class="btn btn-mini btn-next" data-last="Finish"><?php echo __("Next") ?><span class="glyphicon glyphicon-chevron-right"></span></i></a>
        </div>
    </div>
</div>
<div id="categories_title" class="row">
    <div class="col-md-1">
        <br>

    </div>
    <div class="col-md-10">
        <h3><?php _e('Ranking', 'tainacan') ?><button onclick="backToMainPage();" id="btn_back_collection" class="btn btn-default pull-right"><?php _e('Back to collection', 'tainacan') ?></button></h3>
        <hr>
        <div id="alert_success_properties" class="alert alert-success" style="display: none;">
            <button type="button" class="close" onclick="hide_alert();"><span aria-hidden="true">&times;</span></button>
            <?php _e('Operation was successful.', 'tainacan') ?>
        </div>
        <div id="alert_error_properties" class="alert alert-danger" style="display: none;">
            <button type="button" class="close" onclick="hide_alert();"><span aria-hidden="true">&times;</span></button>
            <?php _e('Error! Operation was unsuccessful.', 'tainacan') ?>&nbsp;<span id="message_category"></span>
        </div>
    </div>
</div>
<div class="row" id="ranking_create_opt">
    <div class="col-md-1">
        &nbsp;
    </div>
    <div class="col-md-10">
        <form method="POST" name="formRankingType" id="formRankingType">
            <input type="hidden" name="operation" id="operation_ranking_type" value="next_step" />
            <input type="hidden" id="collection_ranking_type_id" name="collection_id" value="">
            <div class="form-group">
                <label for="property_data_required"><?php _e('Create a voting for the items in the collection', 'tainacan'); ?>:</label><br>
                <input type="radio" name="property_data_required" id="property_data_required_true" checked="checked"  value="true">&nbsp;<?php _e('Use standard vote', 'tainacan'); ?><br>
                <input type="radio" name="property_data_required" id="property_data_required_false" value="false">&nbsp;<?php _e('Use personalized vote', 'tainacan'); ?>
            </div>
            <button type="submit" id="save_and_next" name="save_and_next" class="btn btn-primary" style="float: right;" value="next"><?php _e('Save & Next Step', 'tainacan'); ?></button> <!-- onclick="nextStep()" -->
        </form>
        <div class="form-group">
            <a href="#ranking_tabs" id="show_ranking_link" onclick="showPersonalizeRanking();"><?php _e('Personalize search interface', 'tainacan'); ?></a>
            <a href="#ranking_tabs" id="hide_ranking_link" onclick="hidePersonalizeRanking();" style="display: none;"><?php _e('Hide search interface', 'tainacan'); ?></a>
        </div>
    </div>
</div>
<input type="hidden" name="property_category_id" id="property_category_id" value="<?php echo $category->term_id; ?>"><br>
<div class="categories_menu" class="row" id="ranking_tabs">
    <div class="col-md-1">
        <input type="hidden" id="collection_list_ranking_id" name="collection_id" value="">
    </div>
    <div class="col-md-10">
        <div id="list_ranking">
            <table  class="table table-bordered" style="background-color: #d9edf7;">
                <th><?php _e('Ranking name', 'tainacan'); ?></th>
                <th><?php _e('Ranking type', 'tainacan'); ?></th>
                <th><?php _e('Edit', 'tainacan'); ?></th>
                <th><?php _e('Delete', 'tainacan'); ?></th>
                <tbody id="table_ranking" >
                </tbody>
            </table>
        </div>

        <button  onclick="add_new();" class="btn btn-default pull-left"><?php _e('Add New', 'tainacan') ?></button>
    </div>
</div>

<div class="modal fade" id="modal_remove_ranking" tabindex="-1" role="dialog" aria-labelledby="modal_remove_ranking" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form  id="submit_delete_ranking">
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