<?php
/**
 * Author: Marco Túlio Bueno Veira
 */
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/edit_js.php');

?>

<div id="ranking_title" class="row">
    <div class="col-md-2">
       &nbsp;
    </div>    
    <div class="col-md-9">  
        <h3><?php _e('Ranking','tainacan'); ?></h3> 
        <div id="alert_success" class="alert alert-success" style="display: none;">
            <button type="button" class="close" onclick="hide_alert();"><span aria-hidden="true">&times;</span></button>
<?php _e('Operation was successful.','tainacan') ?>
        </div>    
        <div id="alert_error" class="alert alert-danger" style="display: none;">
            <button type="button" class="close" onclick="hide_alert();"><span aria-hidden="true">&times;</span></button>
<?php _e('Error! Operation was unsuccessful.','tainacan') ?>&nbsp;
        </div>    
    </div>
</div>


<div class="col-md-2">
</div>	
<div class="col-md-10">

    <form  id="submit_edit_ranking">

        <div class="form-group">
            <label for="ranking_name"><?php _e('Ranking Name','tainacan'); ?></label>
            <input type="text" class="form-control" id="ranking_name" name="ranking_name" required="required" value="<?php echo($ranking['name']); ?>">
        </div>

        <div class="form-group">
            <label for="ranking_type"><?php _e('Ranking Type','tainacan'); ?></label>
            <select name="ranking_type" class="form-control" disabled="disabled">
                <option <?php if($ranking['type'] == 'like') echo 'selected = "selected"'; ?> value="like"><?php _e('Like','tainacan'); ?></option>
                <option <?php if($ranking['type'] == 'binary') echo 'selected = "selected"'; ?> value="binary"><?php _e('Binary','tainacan'); ?></option>
                <option <?php if($ranking['type'] == 'stars') echo 'selected = "selected"'; ?> value="stars"><?php _e('Stars','tainacan'); ?></option>
            </select>
        </div>


        <input type="hidden" id="collection_ranking_id" name="collection_id" value="">
        <input type="hidden" id="ranking_id" name="ranking_id" value="<?php echo($ranking['id']); ?>">
        <input type="hidden" id="operation" name="operation" value="edit">
        <button type="submit" id="submit" class="btn btn-default"><?php _e('Submit','tainacan'); ?></button>
        <button type="button" id="back" onclick="backToListRanking()" class="btn btn-default"><?php _e('Back','tainacan'); ?></button>
    </form>
</div>
