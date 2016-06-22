<script>
    $(function () {
        var src = $('#src').val();
        var object_id = '<?php echo $object_id; ?>';
        if ($('#create_stars_id_' + object_id).val() !== '') {
            stars = $('#create_stars_id_' + object_id).val().split(',');
            $.each(stars, function (idx, elem) {
                $('#create_rating_' + object_id + '_' + elem).raty({
                    score: $('#create_star_' + object_id + '_' + elem).val(),
                    half: true,
                    starType: 'i',
                    click: function (score, evt) {
                        multiple_save_vote_stars(score, elem, $('#ids_multiple').val());
                        return false;
                    }
                });
            });
        }
         
       if($('.hide_rankings')&&$('.hide_rankings').val()==='true'){
            $('#list_ranking_items').hide();
        }  

    });
    function multiple_save_vote_stars(score, property_id, object_id) {
        $.ajax({
            url: $('#src').val() + '/controllers/ranking/ranking_controller.php',
            type: 'POST',
            data: {
                operation: 'save_vote_stars_multiple',
                score:score*2,
                property_id:property_id,
                object_id:object_id, 
                collection_id: $("#collection_id").val()
            }
        }).done(function (result) {
            elem_first =jQuery.parseJSON(result); 
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
    function multiple_save_vote_like(property_id, object_id) {
        $.ajax({
            url: $('#src').val() + '/controllers/ranking/ranking_controller.php',
            type: 'POST',
            data: {
                operation: 'save_vote_like_multiple',
                score: 1,
                property_id:property_id,
                object_id:object_id, 
                collection_id: $("#collection_id").val()
            }
        }).done(function (result) {
            elem_first =jQuery.parseJSON(result); 
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
    function multiple_save_vote_binary_up(property_id, object_id) {
        $.ajax({
            url: $('#src').val() + '/controllers/ranking/ranking_controller.php',
            type: 'POST',
            data: {
                operation: 'save_vote_binary_multiple',
                score:1,
                property_id:property_id,
                object_id:object_id, 
                collection_id: $("#collection_id").val()
            }
        }).done(function (result) {
            elem_first =jQuery.parseJSON(result); 
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
        function multiple_save_vote_binary_down(property_id, object_id) {
        $.ajax({
            url: $('#src').val() + '/controllers/ranking/ranking_controller.php',
            type: 'POST',
            data: {
                operation: 'save_vote_binary_multiple',
                score: -1,
                property_id:property_id,
                object_id:object_id, 
                collection_id: $("#collection_id").val()
            }
        }).done(function (result) {
            elem_first =jQuery.parseJSON(result); 
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
