<script>
    $(function () {
        //submissao de formulario positivo
        $('#form_positive_argument').submit(function (e) {
            $.ajax({
                url: $('#src').val() + '/modules/<?php echo MODULE_CONTEST ?>/controllers/argument/contest_argument_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).success(function (result) {
                $('.nav-tabs').tab();
                $('.dropdown-toggle').dropdown();
                elem = jQuery.parseJSON(result);
                if(elem.redirect)
                    window.location = elem.redirect;
            }).error(function (error) {
            });
            e.preventDefault();
        });
        //submissao de formulario negativo
        $('#form_negative_argument').submit(function (e) {
            $.ajax({
                url: $('#src').val() + '/modules/<?php echo MODULE_CONTEST ?>/controllers/argument/contest_argument_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).success(function (result) {
                $('.nav-tabs').tab();
                $('.dropdown-toggle').dropdown();
                elem = jQuery.parseJSON(result);
                if(elem.redirect)
                    window.location = elem.redirect;
            }).error(function (error) {
            });
            e.preventDefault();
        });
    });
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
                $('#collection_postive_argument_id').val($("#collection_id").val());
                $('[name="argument_parent"]').val(object_id);
                $('#argument_positive_text').text($('#text-comment-' + object_id).text());
                show_properties_argument('positive', object_id);
                $('#modalReplyPositiveArgument').modal('show');
                //showAlertGeneral('<?php _e('Vote successfully', 'tainacan') ?>', '<?php _e('Your like was computed', 'tainacan') ?>', '<?php _e('success') ?>');
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
                $('#collection_negative_argument_id').val($("#collection_id").val());
                $('[name="argument_parent"]').val(object_id);
                show_properties_argument('negative', object_id);
                $('#argument_negative_text').text($('#text-comment-' + object_id).text());
                $('#modalReplyNegativeArgument').modal('show');
            } else {
                // showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('You must sign up first to vote', 'tainacan') ?>', '<?php _e('error') ?>');
            }
        });
    }

    function show_properties_argument(type, object_id) {
        var promisse;
        promisse = $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {operation: 'show_object_properties', object_id: object_id, collection_id: $("#collection_id").val()}
        });
        promisse.done(function (result) {
            console.log('#properties_' + type);
            $('#properties_' + type).html(result);
        });
    }

    /**
     * 
     * @param {type} seletor
     * @returns {undefined}     */
    function toggle_additional_information(seletor) {
        if ($(seletor).is(':visible')) {
            $(seletor).slideUp();
        } else {
            $(seletor).slideDown();
        }

    }
</script>    