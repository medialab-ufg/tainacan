<script>
    $(document).ready(function () {
        change_breadcrumbs_title('<?php _e('Events','tainacan') ?>');
        $('#configuration').show();
        notification_events_repository();
        $('.nav-tabs').tab();
        var dataTable_options = {
            "order": [[4,"desc"]],
            "aoColumnDefs": [ { "bVisible": false, "aTargets" : [4] } ],
            "initComplete": function() {
                $("#event_verified_table_wrapper .row").eq(2).addClass('datatable-result-sets');
                $("#event_not_verified_table_wrapper .row").eq(2).addClass('datatable-result-sets');
            },
            "language": {
                sInfo: "Exibindo de _START_ até _END_ de _TOTAL_ itens",
                sLengthMenu: "Mostrar _MENU_ itens por página",
                sInfoFiltered: "(filtrados de _MAX_ eventos)",
                search: "Pesquisar: ",
                paginate: {
                    first: "Primeira",
                    previous: "Anterior",
                    next: "Próxima",
                    last: "Última"
                }
            }
        };
        $('#event_not_verified_table').DataTable(dataTable_options);
        $('#event_verified_table').DataTable(dataTable_options);
<?php // Submissao do form de exclusao da categoria   ?>
        $('#submit_form_event_not_confirmed').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: $('#src').val() + '/controllers/event/event_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                $('#modal_verify_event_not_confirmed').modal('hide');
                showHeaderCollection($('#src').val());
                showEvents($('#src').val());
                notification_events_repository();
                elem_first = jQuery.parseJSON(result);
                if (elem_first.operation && elem_first.operation !== 'socialdb_event_collection_delete') {
                    showEvents($('#src').val());
                } else {
                    showEventsRepository($('#src').val(), '<?php echo get_option('collection_root_id'); ?>');
                }
                if (elem.success === 'true') {
                    showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('An error ocurred, this event does not exist anymore', 'tainacan') ?>', 'error');
                } else {
                    showAlertGeneral('<?php _e('Success', 'tainacan') ?>', '<?php _e('Event confirmed successfuly', 'tainacan') ?>', 'success');
                }
                $('.dropdown-toggle').dropdown();
            });
            e.preventDefault();
        });

        $('#click_events_not_verified').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
        $('#click_events_verified').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
            /*
            var show_string = $('.dataTables_info').text().replace("Showing", "Exibindo: ");
            show_string = show_string.replace("to", "a").replace("of", "de").replace("entries", "itens");
            $('.dataTables_info').text(show_string);
            */
        });

    });
<?php //vincular categorias com a colecao (facetas)     ?>
    function show_verify_event_not_confirmed(event_id, collection_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/event/event_controller.php",
            data: {collection_id: collection_id, operation: 'get_event_info', event_id: event_id}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if (!elem.author || elem.author === null || elem.author == '<?php get_option('anonimous_user') ?>') {
                elem.author = '<?php _e('Anonimous', 'tainacan') ?>';
            }
            if (elem.name) {
                $('#event_date_create').text(elem.date);
                $('#event_author').text(elem.author);
                $('#event_description').html(elem.name);
                if (elem.observation && elem.observation.indexOf('<a targ') >= 0) {
                    $('#link_new_item_not_observed').html(elem.observation);
                }else if(elem.link){
                     $('#link_new_item_not_observed').html(elem.link);
                }else {
                    $('#event_observation').html(elem.observation);
                }
                $('#event_operation').val(elem.operation);
                $('#event_id').val(elem.id);
                if (elem.democratic_vote_id) {
                    $('#unconfirmed_democratic_vote_id').val(elem.democratic_vote_id);
                    $('#unconfirmed_counter_up').text(elem.count_up);
                    $('#unconfirmed_counter_down').text(elem.count_down);
                }
            }
            $('.dropdown-toggle').dropdown();
            $('#modal_verify_event_not_confirmed').modal('show');
        });
    }
    function show_unconfirmed_users_events(event_id, collection_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/event/event_controller.php",
            data: {collection_id: collection_id, operation: 'get_event_info', event_id: event_id}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if (!elem.author || elem.author === null || elem.author == '<?php get_option('anonimous_user') ?>') {
                elem.author = '<?php _e('Anonimous', 'tainacan') ?>';
            }
            if (elem.name) {
                $('#unconfirmed_users_event_date_create').text(elem.date);
                $('#unconfirmed_users_event_author').text(elem.author);
                $('#unconfirmed_users_event_description').html(elem.name);
                $('#unconfirmed_users_event_id').val(elem.id);
                if (elem.author_id == '<?php echo get_current_user_id() ?>') {
                    $('#vote_not_allowed').show();
                    $('#vote_allowed').hide();
                }
                if (elem.democratic_vote_id) {
                    $('#unconfirmed_users_democratic_vote_id').val(elem.democratic_vote_id);
                    $('#unconfirmed_users_counter_up').text(elem.count_up);
                    $('#unconfirmed_users_counter_down').text(elem.count_down);
                }
            }
            $('.dropdown-toggle').dropdown();
            $('#modal_verify_event_not_confirmed_democratic').modal('show');
        });
    }

    function show_verify_event_confirmed(event_id, collection_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/event/event_controller.php",
            data: {collection_id: collection_id, operation: 'get_event_info', event_id: event_id}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if (!elem.author || elem.author === null || elem.author == '<?php get_option('anonimous_user') ?>') {
                elem.author = '<?php _e('Anonimous', 'tainacan') ?>';
            }
            if (elem.name) {
                $('#event_date_create').text(elem.date);
                $('#event_author').text(elem.author);
                $('#event_description').text(elem.name);
                $('#event_operation').val(elem.operation);
                $('#event_id').val(elem.id);
            }
            $('.dropdown-toggle').dropdown();
            $('#modal_verify_event_not_confirmed').modal('show');
        });
    }

    function event_save_vote_binary_up(property_id, object_id, on_event) {
        $.ajax({
            url: $('#src').val() + '/controllers/ranking/ranking_controller.php',
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
            $("#single_counter_up_" + object_id + "_" + property_id).text(elem_first.results.final_up);
            $("#single_counter_down_" + object_id + "_" + property_id).text(elem_first.results.final_down);
            $("#single_score_" + object_id + "_" + property_id).text(elem_first.results.final_score);
            if (elem_first.is_user_logged_in) {
                if (elem_first.is_new) {
                    showAlertGeneral('<?php _e('Vote successfully', 'tainacan') ?>', '<?php _e('Your like was computed', 'tainacan') ?>', '<?php _e('success') ?>');
                } else {
                    showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('You already liked this item', 'tainacan') ?>', '<?php _e('info') ?>');
                }
            } else {
                showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('You must sign up first to vote', 'tainacan') ?>', '<?php _e('error') ?>');
            }
            if (on_event && on_event == 'unconfirmed') {
                $('#unconfirmed_counter_up').text(elem_first.results.final_up);
                $('#unconfirmed_counter_down').text(elem_first.results.final_down);
            } else if (on_event && on_event == 'confirmed') {
                $('#confirmed_counter_up').text(elem_first.results.final_up);
                $('#confirmed_counter_down').text(elem_first.results.final_down);
            } else if (on_event && on_event == 'unconfirmed_users') {
                $('#unconfirmed_users_counter_up').text(elem_first.results.final_up);
                $('#unconfirmed_users_counter_down').text(elem_first.results.final_down);
            }
        });
    }

    function event_save_vote_binary_down(property_id, object_id, on_event) {
        $.ajax({
            url: $('#src').val() + '/controllers/ranking/ranking_controller.php',
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
            $("#single_counter_up_" + object_id + "_" + property_id).text(elem_first.results.final_up);
            $("#single_counter_down_" + object_id + "_" + property_id).text(elem_first.results.final_down);
            $("#single_score_" + object_id + "_" + property_id).text(elem_first.results.final_score);
            if (elem_first.is_user_logged_in) {
                if (elem_first.is_new) {
                    showAlertGeneral('<?php _e('Vote successfully', 'tainacan') ?>', '<?php _e('Your not like was computed', 'tainacan') ?>', '<?php _e('success') ?>');
                } else {
                    showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('You already not liked this item', 'tainacan') ?>', '<?php _e('info') ?>');
                }
            } else {
                showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('You must sign up first to vote', 'tainacan') ?>', '<?php _e('error') ?>');
            }
            if (on_event && on_event == 'unconfirmed') {
                $('#unconfirmed_counter_up').text(elem_first.results.final_up);
                $('#unconfirmed_counter_down').text(elem_first.results.final_down);
            } else if (on_event && on_event == 'confirmed') {
                $('#confirmed_counter_up').text(elem_first.results.final_up);
                $('#confirmed_counter_down').text(elem_first.results.final_down);
            } else if (on_event && on_event == 'unconfirmed_users') {
                $('#unconfirmed_users_counter_up').text(elem_first.results.final_up);
                $('#unconfirmed_users_counter_down').text(elem_first.results.final_down);
            }
        });
    }
    /**
     * funcao que seleciona
     
     * @returns {undefined}     */
    function democratic_check_events() {
        $('input:checkbox[name="process_democratic_vote"]').prop('checked', 'checked');
    }
    /**
     * funcao que seleciona
     
     * @returns {undefined}     */
    function democratic_uncheck_events() {
        $('input:checkbox[name="process_democratic_vote"]').prop('checked', false);
    }
    /**
     * 
     
     * @param {type} title
     * @param {type} text
     * @param {type} url
     * @returns {undefined}     */
    function process_events_democratic() {
        swal({
            title: '<?php _e('Attention!', 'tainacan') ?>',
            text: '<?php _e('Proccess all events selected?', 'tainacan') ?>',
            type: "info",
            showCancelButton: true,
            confirmButtonClass: 'btn-primary',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                var values = []
                $('input:checkbox[name="process_democratic_vote"]:checked').each(function () {
                    values.push($(this).val());
                });
                $('#modalImportMain').modal('show');//mostro o modal de carregamento
                $.ajax({
                    url: $('#src').val() + '/controllers/event/event_controller.php',
                    type: 'POST',
                    data: {
                        operation: 'process_events_selected',
                        events: values.join(','),
                        collection_id: $("#collection_id").val()
                    }
                }).done(function (result) {
                    $('#modalImportMain').modal('hide');//mostro o modal de carregamento
                    //showHeaderCollection($('#src').val());
                    showEvents($('#src').val());
                    notification_events_repository();
                });
            }
        });
    }
</script>
