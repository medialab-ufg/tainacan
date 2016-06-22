<script>
    $(function () {
        var src = $('#src').val();
        var object_id = '<?php echo $object_id; ?>';
        if ($('#stars_id_' + object_id).val() !== '') {
            stars = $('#stars_id_' + object_id).val().split(',');
            $.each(stars, function (idx, elem) {
                $('#rating_' + object_id + '_' + elem).raty({
                    score: $('#star_' + object_id + '_' + elem).val(),
                    half: true,
                    starType: 'i',
                    click: function (score, evt) {
                        save_vote_stars(score, elem, object_id)
                        return false;
                    }
                });
            });
        }


    });
    function save_vote_stars(score, property_id, object_id) {
        $.ajax({
            url: $('#src').val() + '/controllers/ranking/ranking_controller.php',
            type: 'POST',
            data: {
                operation: 'save_vote_stars',
                score:score*2,
                property_id:property_id,
                object_id:object_id, 
                collection_id: $("#collection_id").val()
            }
        }).done(function (result) {
             elem_first =jQuery.parseJSON(result); 
             $('#rating_' + object_id + '_' + property_id).raty({
                    score: elem_first.results.final_score,
                    half: true,
                    starType: 'i',
                    click: function (score, evt) {
                        save_vote_stars(score, property_id, object_id)
                        return false;
                    }
                });   
            $('#counter_' + object_id + '_' + property_id).text(elem_first.results.count)   
            if(elem_first.is_user_logged_in){
                if(elem_first.is_new){
                  showAlertGeneral('<?php _e('Vote successfully added','tainacan') ?>', '<?php _e('Your vote was computed successfully','tainacan') ?>', '<?php _e('success') ?>');
                }else{
                  showAlertGeneral('<?php _e('Vote successfully updated','tainacan') ?>', '<?php _e('Your vote was updated successfully','tainacan') ?>', '<?php _e('success') ?>');
                }
            }else{
                 showAlertGeneral('<?php _e('Atention','tainacan') ?>', '<?php _e('You must sign up first to vote','tainacan') ?>', '<?php _e('error') ?>');
            }
        });
    }
    function save_vote_like(property_id, object_id) {
        $.ajax({
            url: $('#src').val() + '/controllers/ranking/ranking_controller.php',
            type: 'POST',
            data: {
                operation: 'save_vote_like',
                score: 1,
                property_id:property_id,
                object_id:object_id, 
                collection_id: $("#collection_id").val()
            }
        }).done(function (result) {
            elem_first =jQuery.parseJSON(result); 
            $("#like_"+object_id+"_"+property_id).val(elem_first.results.final_score); 
            $("#counter_"+object_id+"_"+property_id).text(elem_first.results.final_score); 
            if(elem_first.is_user_logged_in){
                if(elem_first.is_new){
                  showAlertGeneral('<?php _e('Vote successfully added','tainacan') ?>', '<?php _e('Your vote was computed successfully','tainacan') ?>', '<?php _e('success') ?>');
                }else{
                  showAlertGeneral('<?php _e('Vote successfully updated','tainacan') ?>', '<?php _e('Your vote was updated successfully','tainacan') ?>', '<?php _e('success') ?>');
                }
            }else{
                 showAlertGeneral('<?php _e('Atention','tainacan') ?>', '<?php _e('You must sign up first to vote','tainacan') ?>', '<?php _e('error') ?>');
            }
        });
    }
    function save_vote_binary_up(property_id, object_id) {
        $.ajax({
            url: $('#src').val() + '/controllers/ranking/ranking_controller.php',
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
            $("#counter_up_"+object_id+"_"+property_id).text(elem_first.results.final_up);
            $("#counter_down_"+object_id+"_"+property_id).text(elem_first.results.final_down); 
            $("#score_"+object_id+"_"+property_id).text(elem_first.results.final_score);
            if(elem_first.is_user_logged_in){
                if(elem_first.is_new){
                  showAlertGeneral('<?php _e('Vote successfully added','tainacan') ?>', '<?php _e('Your vote was computed successfully','tainacan') ?>', '<?php _e('success') ?>');
                }else{
                  showAlertGeneral('<?php _e('Vote successfully updated','tainacan') ?>', '<?php _e('Your vote was updated successfully','tainacan') ?>', '<?php _e('success') ?>');
                }
            }else{
                 showAlertGeneral('<?php _e('Atention','tainacan') ?>', '<?php _e('You must sign up first to vote','tainacan') ?>', '<?php _e('error') ?>');
            }
        });
    }
        function save_vote_binary_down(property_id, object_id) {
        $.ajax({
            url: $('#src').val() + '/controllers/ranking/ranking_controller.php',
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
            $("#counter_up_"+object_id+"_"+property_id).text(elem_first.results.final_up);
            $("#counter_down_"+object_id+"_"+property_id).text(elem_first.results.final_down); 
            $("#score_"+object_id+"_"+property_id).text(elem_first.results.final_score);
            if(elem_first.is_user_logged_in){
                if(elem_first.is_new){
                  showAlertGeneral('<?php _e('Vote successfully added','tainacan') ?>', '<?php _e('Your vote was computed successfully','tainacan') ?>', '<?php _e('success') ?>');
                }else{
                  showAlertGeneral('<?php _e('Vote successfully updated','tainacan') ?>', '<?php _e('Your vote was updated successfully','tainacan') ?>', '<?php _e('success') ?>');
                }
            }else{
                 showAlertGeneral('<?php _e('Atention','tainacan') ?>', '<?php _e('You must sign up first to vote','tainacan') ?>', '<?php _e('error') ?>');
            }
        });
    }
</script>
