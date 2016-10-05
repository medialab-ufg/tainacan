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
                if (elem.redirect)
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
                if (elem.redirect)
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

    /**
     * 
     * @param {type} item_id
     * @returns {undefined}     */
    function edit_comment(item_id) {
        show_modal_main();
        $.ajax({
            url: $('#src').val() + '/modules/<?php echo MODULE_CONTEST ?>/controllers/argument/contest_argument_controller.php',
            type: 'POST',
            data: {
                operation: 'edit_comment_contest',
                object_id: item_id,
                collection_id: $("#collection_id").val()
            }
        }).done(function (result) {
            elem_first = jQuery.parseJSON(result);
            if (elem_first.comment && elem_first.comment.post_author == '<?php echo get_current_user_id(); ?>') {
                $('#collection_edit_argument_id').val($("#collection_id").val());
                $('#text-edit-argument').val(elem_first.comment.post_title);
                show_properties_argument('edit', item_id);
                if (item_id == rootComment) {
                    $('#edit-type-comment').hide();
                } else {
                    $('#edit-type-comment').show();
                    if (elem_first.type == 'positive') {
                        $('#edit-argument-positive').attr('checked', 'checked');
                        $('#edit-argument-negative').removeAttr('checked');
                    } else {
                        $('#edit-argument-negative').attr('checked', 'checked');
                        $('#edit-argument-positive').removeAttr('checked');
                    }
                }
                hide_modal_main();
                $('#modalEditArgument').modal('show');
            } else {
                // showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('You must sign up first to vote', 'tainacan') ?>', '<?php _e('error') ?>');
            }
        });
    }

    function delete_comment(item_id) {
        swal({
            title: '<?php _e('Attention!') ?>',
            text: '<?php _e('Are you sure to remove this comment?') ?>',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $('#modalImportMain').modal('show');//mostro o modal de carregamento
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/event/event_controller.php",
                    data: {
                        operation: 'add_event_object_delete',
                        socialdb_event_create_date: <?php echo time() ?>,
                        socialdb_event_user_id: $('#current_user_id').val(),
                        socialdb_event_object_item_id: item_id,
                        socialdb_event_collection_id: $('#collection_id').val()}
                }).done(function (result) {
                    $('#modalImportMain').modal('hide');//escondo o modal de carregamento
                    elem_first = jQuery.parseJSON(result);
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                    location.reload();
                });
            }
        });
    }
</script>    