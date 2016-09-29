<script>
    /**
     * 
     * @param {type} property_id
     * @param {type} object_id
     * @returns {undefined}
     */
    function contest_save_vote_binary_up(property_id, object_id) {
        $.ajax({
            url: $('#src').val() + '/modules/<?php echo MODULE_CONTEST ?>/controllers/ranking/ranking_controller.php',
            type: 'POST',
            data: {
                operation: 'save_vote_binary',
                score:1,
                property_id:property_id,
                object_id:object_id, 
                collection_id: $("#collection_id").val()
            }
        }).done(function (result) {
            elem_first =jQuery.parseJSON(result); 
            $("#create_counter_up_"+object_id+"_"+property_id).text(elem_first.results.final_up);
            $("#create_counter_down_"+object_id+"_"+property_id).text(elem_first.results.final_down); 
            $("#create_score_"+object_id+"_"+property_id).text(elem_first.results.final_score);
            if(elem_first.is_user_logged_in){
                if(elem_first.is_new){
                  showAlertGeneral('<?php _e('Vote successfully added','tainacan') ?>', '<?php _e('Your like was computed','tainacan') ?>', '<?php _e('success') ?>');
                }else{
                  showAlertGeneral('<?php _e('Attention','tainacan') ?>', '<?php _e('You already liked this item','tainacan') ?>', '<?php _e('info') ?>');
                }
            }else{
                 showAlertGeneral('<?php _e('Atention','tainacan') ?>', '<?php _e('You must sign up first to vote','tainacan') ?>', '<?php _e('error') ?>');
            }
        });
    }
    
    /**
    * 

     * @param {type} property_id
     * @param {type} object_id
     * @returns {undefined}     */
    function contest_save_vote_binary_down(property_id, object_id) {
        $.ajax({
            url: $('#src').val() + '/modules/<?php echo MODULE_CONTEST ?>/controllers/ranking/ranking_controller.php',
            type: 'POST',
            data: {
                operation: 'save_vote_binary',
                score: -1,
                property_id:property_id,
                object_id:object_id, 
                collection_id: $("#collection_id").val()
            }
        }).done(function (result) {
            elem_first =jQuery.parseJSON(result); 
            $("#create_counter_up_"+object_id+"_"+property_id).text(elem_first.results.final_up);
            $("#create_counter_down_"+object_id+"_"+property_id).text(elem_first.results.final_down); 
            $("#create_score_"+object_id+"_"+property_id).text(elem_first.results.final_score);
            if(elem_first.is_user_logged_in){
                if(elem_first.is_new){
                  showAlertGeneral('<?php _e('Vote successfully added','tainacan') ?>', '<?php _e('Your not like was computed','tainacan') ?>', '<?php _e('success') ?>');
                }else{
                  showAlertGeneral('<?php _e('Attention','tainacan') ?>', '<?php _e('You already not liked this item','tainacan') ?>', '<?php _e('info') ?>');
                }
            }else{
                 showAlertGeneral('<?php _e('Atention','tainacan') ?>', '<?php _e('You must sign up first to vote','tainacan') ?>', '<?php _e('error') ?>');
            }
        });
    }
</script>    