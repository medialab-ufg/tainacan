<script>
    $(function () {
        var src = $('#src').val();
        var object_id = '<?php echo $object_id; ?>';
        if ($('#single_stars_id_' + object_id).val() !== '') {
            stars = $('#single_stars_id_' + object_id).val().split(',');
            $.each(stars, function (idx, elem) {
                $('#single_rating_' + object_id + '_' + elem).raty({
                    score: $('#single_star_' + object_id + '_' + elem).val(),
                    half: true,
                    starType: 'i',
                    click: function (score, evt) {
                        single_save_vote_stars(score, elem, object_id)
                        return false;
                    }
                });
            });
        }


    });
    function single_save_vote_stars(score, property_id, object_id) {
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
             // console.log(elem_first);
             $('#single_rating_' + object_id + '_' + property_id).raty({
                 score: Math.ceil((elem_first.results.final_score*2))/2,
                 half: true,
                 starType: 'i',
                 click: function (score, evt) {
                     single_save_vote_stars(score, property_id, object_id);
                     return false;
                 }
                });   
            $('#single_counter_' + object_id + '_' + property_id).text(elem_first.results.count);
            if(elem_first.is_user_logged_in){
                score = Math.ceil((score*2))/2;
                elem_first.results.final_score = Math.ceil((elem_first.results.final_score*2))/2;
                if(elem_first.is_new){
                  showAlertGeneral('<?php _e('Vote successfully added','tainacan') ?>', elem_first.msg, '<?php _e('success') ?>');
                }else{
                  showAlertGeneral('<?php _e('Vote successfully updated','tainacan') ?>',  elem_first.msg, '<?php _e('success') ?>');
                }
            }else{
                 showAlertGeneral('<?php _e('Atention','tainacan') ?>', '<?php _e('You must sign up first to vote','tainacan') ?>', '<?php _e('error') ?>');
            }
        });
    }
    function single_save_vote_like(property_id, object_id) {
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
            $("#single_like_"+object_id+"_"+property_id).val(elem_first.results.final_score); 
            $("#single_counter_"+object_id+"_"+property_id).text(elem_first.results.final_score); 
            if(elem_first.is_user_logged_in){
                if(elem_first.is_new){
                  showAlertGeneral('<?php _e('Vote successfully added','tainacan') ?>', '<?php _e('Your vote was computed successfully','tainacan') ?>', '<?php _e('success') ?>');
                }else{
                  showAlertGeneral('<?php _e('Attention','tainacan') ?>', '<?php _e('You already liked this item','tainacan') ?>', '<?php _e('info') ?>');
                }
            }else{
                 showAlertGeneral('<?php _e('Atention','tainacan') ?>', '<?php _e('You must sign up first to vote','tainacan') ?>', '<?php _e('error') ?>');
            }
        });
    }
    function single_save_vote_binary_up(property_id, object_id) {
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
            $("#single_counter_up_"+object_id+"_"+property_id).text(elem_first.results.final_up);
            $("#single_counter_down_"+object_id+"_"+property_id).text(elem_first.results.final_down); 
            $("#single_score_"+object_id+"_"+property_id).text(elem_first.results.final_score);
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
        function single_save_vote_binary_down(property_id, object_id) {
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
            $("#single_counter_up_"+object_id+"_"+property_id).text(elem_first.results.final_up);
            $("#single_counter_down_"+object_id+"_"+property_id).text(elem_first.results.final_down); 
            $("#single_score_"+object_id+"_"+property_id).text(elem_first.results.final_score);
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
