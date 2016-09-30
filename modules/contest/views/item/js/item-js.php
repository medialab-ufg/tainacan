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
                score: 1,
                property_id: property_id,
                object_id: object_id,
                collection_id: $("#collection_id").val()
            }
        }).done(function (result) {
            elem_first = jQuery.parseJSON(result);
            if (elem_first.is_user_logged_in && elem_first.results.length > 0) {
                $.each(elem_first.results, function (index, result) {
                    $(result.seletor).text(result.score.final_score);
                });
                showAlertGeneral('<?php _e('Vote successfully', 'tainacan') ?>', '<?php _e('Your like was computed', 'tainacan') ?>', '<?php _e('success') ?>');
            } else {
                showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('You must sign up first to vote', 'tainacan') ?>', '<?php _e('error') ?>');
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
                property_id: property_id,
                object_id: object_id,
                collection_id: $("#collection_id").val()
            }
        }).done(function (result) {
            elem_first = jQuery.parseJSON(result);
            if (elem_first.is_user_logged_in && elem_first.results.length > 0) {
                $.each(elem_first.results, function (index, result) {
                    $(result.seletor).text(result.score.final_score);
                });
                showAlertGeneral('<?php _e('Vote successfully', 'tainacan') ?>', '<?php _e('Your like was computed', 'tainacan') ?>', '<?php _e('success') ?>');
            } else {
                showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('You must sign up first to vote', 'tainacan') ?>', '<?php _e('error') ?>');
            }
        });
    }
</script>    