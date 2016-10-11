<script>
    $(function () {
        //url
        var stateObj = {foo: "bar"};
        history.replaceState(stateObj, "page 2", $('#socialdb_permalink_object').val());
        //submissao de formulario positivo
        $('#form_answer').submit(function (e) {
            $('.modal').modal('hide');
            show_modal_main();
            $.ajax({
                url: $('#src').val() + '/modules/<?php echo MODULE_CONTEST ?>/controllers/question/contest_question_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).success(function (result) {
                $('.nav-tabs').tab();
                $('.dropdown-toggle').dropdown();
                elem = jQuery.parseJSON(result);
                //show messages
                $('.modal').modal('hide');
                hide_modal_main();
                showItemObject($('#item_id').val(),$('#src').val());
                showAlertGeneral('<?php _e('Success', 'tainacan') ?>', '<?php _e('Operation was successfully!', 'tainacan') ?>', 'success');
                //if (elem.redirect)
                    //window.location = elem.redirect;
            }).error(function (error) {
            });
            e.preventDefault();
        });
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
               //show messages
                $('.modal').modal('hide');
                hide_modal_main();
                showItemObject($('#item_id').val(),$('#src').val());
                showAlertGeneral('<?php _e('Success', 'tainacan') ?>', '<?php _e('Operation was successfully!', 'tainacan') ?>', 'success');
                //if (elem.redirect)
                    //window.location = elem.redirect;
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
                //show messages
                $('.modal').modal('hide');
                hide_modal_main();
                showItemObject($('#item_id').val(),$('#src').val());
                showAlertGeneral('<?php _e('Success', 'tainacan') ?>', '<?php _e('Operation was successfully!', 'tainacan') ?>', 'success');
                //if (elem.redirect)
                    //window.location = elem.redirect;
            }).error(function (error) {
            });
            e.preventDefault();
        });
        //submissao de formulario de edicao
        $('#form_update_argument').submit(function (e) {
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
               //show messages
                $('.modal').modal('hide');
                hide_modal_main();
                showItemObject($('#item_id').val(),$('#src').val());
                showAlertGeneral('<?php _e('Success', 'tainacan') ?>', '<?php _e('Operation was successfully!', 'tainacan') ?>', 'success');
                //if (elem.redirect)
                    //window.location = elem.redirect;
            }).error(function (error) {
            });
            e.preventDefault();
        });
        //submissao de formulario de edicao
        $('#form_report_abuse').submit(function (e) {
            $.ajax({
                url: $('#src').val() + "/controllers/event/event_controller.php",
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).success(function (result) {
                $('#modalReportAbuse').modal('hide');
                $('.nav-tabs').tab();
                $('.dropdown-toggle').dropdown();
                elem_first = jQuery.parseJSON(result);
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
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
    
     function show_properties_argument_edit(type, object_id) {
        var promisse;
        promisse = $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {operation: 'show_object_properties_edit', object_id: object_id, collection_id: $("#collection_id").val()}
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
    function edit_comment(item_id,hide_position) {
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
                $('#edit_argument_id').val(elem_first.comment.ID);
                $('#text-edit-argument').val(elem_first.comment.post_title);
                show_properties_argument_edit('edit', item_id);
                if (item_id == rootComment||hide_position===true) {
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
    
    /**
    * 
    * @param {type} item_id
    * @returns {undefined}     */
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
    
    function report_abuse(item_id){
        $('#collection_report_argument_id').val($('#collection_id').val());
        $('#report_argument_id').val(item_id);
        $('#argument_report_text').text($('#text-comment-'+item_id).text());
        $('#modalReportAbuse').modal('show');
    }
    
    function submit_report_abuse(item_id){
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
        });
    }
    
    function share_comment(id,text, url){
        $('#modalShareComment').modal('show');
        var url_twitter = 'https://twitter.com/intent/tweet?text='+text+'&via=socialdb';
        $('#share-twitter-comment').attr('href',url_twitter);
        var url_facebook = 'http://www.facebook.com/sharer/sharer.php?u='+url+'&t='+text;
        $('#share-facebook-comment').attr('href',url_facebook);
        var url_gmail = 'https://plus.google.com/share?url='+url;
        $('#share-gmail-comment').attr('href',url_gmail);
    }
    
    function add_answer(object_id){
        $('#collection_answer_id').val($("#collection_id").val());
        $('#answer_text').text($('#text-comment-' + object_id).text());
        show_properties_argument('answer', object_id);
        $('#modalAddAnswer').modal('show');
        
    }
    
    function autocomplete_arguments(seletor,property_id){
        $(seletor).autocomplete({
            source: $('#src').val() + '/controllers/object/object_controller.php?operation=get_objects_by_property_json&property_id=' + property_id,
            messages: {
                noResults: '',
                results: function () {
                }
            },
            minLength: 2,
            select: function (event, ui) {
                console.log(event);
                event.preventDefault();
                var label = ui.item.label;
                $(seletor).val(label);
            }
        });    
    }
</script>    