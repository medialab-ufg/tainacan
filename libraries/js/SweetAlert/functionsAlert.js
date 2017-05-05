
// Type = success, warning, info or error
function showAlertGeneral(title, msg, type) {
    swal(title, msg, type);
    $('.nav-tabs').tab();
}

//Alert Privacity
function redirect_privacity(title, text, url) {
    swal({
        title: title,
        text: text,
        type: "error",
        showCancelButton: false,
        confirmButtonClass: 'btn-primary',
        closeOnConfirm: false,
        closeOnCancel: true
    },
    function (isConfirm) {
        if (isConfirm) {
            window.location = url;
        }
    });
}

//Alert
function confirm_success_register_user(title, text, url) {
    swal({
        title: title,
        text: text,
        type: "success",
        showCancelButton: false,
        confirmButtonClass: 'btn-primary',
        closeOnConfirm: false,
        closeOnCancel: true
    },
    function (isConfirm) {
        if (isConfirm) {
            window.location = url;
        }
    });
}

//Events deletes (category) alert
function remove_event_category_classication(title, text, category_id, object_id, time) {
    swal({
        title: title,
        text: text,
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
                    operation: 'add_event_classification_delete',
                    socialdb_event_create_date: time,
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_classification_object_id: object_id,
                    socialdb_event_classification_term_id: category_id,
                    socialdb_event_classification_type: 'category',
                    socialdb_event_collection_id: $('#collection_id').val()}
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//mostro o modal de carregamento
                elem_first = jQuery.parseJSON(result);
                show_classifications(object_id);
                set_containers_class($('#collection_id').val());
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);

            });
        }
    });
}

function remove_event_property_classication(title, text, category_id, object_id, time, type) {
    swal({
        title: title,
        text: text,
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: 'btn-danger',
        closeOnConfirm: true,
        closeOnCancel: true
    },
    function (isConfirm) {
        $('#modalImportMain').modal('show');//mostro o modal de carregamento
        if (isConfirm) {
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/event/event_controller.php",
                data: {
                    operation: 'add_event_classification_delete',
                    socialdb_event_create_date: time,
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_classification_object_id: object_id,
                    socialdb_event_classification_term_id: category_id,
                    socialdb_event_classification_type: type,
                    socialdb_event_collection_id: $('#collection_id').val()}
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//escondo o modal de carregamento
                elem_first = jQuery.parseJSON(result);
                set_containers_class($('#collection_id').val());
                show_classifications(object_id);
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);

            });
        }
    });
}

// deletar objeto
function delete_object(title, text, object_id, time) {
    swal({
        title: title,
        text: text,
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
                    socialdb_event_create_date: time,
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_object_item_id: object_id,
                    socialdb_event_collection_id: $('#collection_id').val()}
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//escondo o modal de carregamento
                elem_first = jQuery.parseJSON(result);
                showList($('#src').val());
                 set_containers_class($('#collection_id').val());
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
            });
        }
    });
}

function delete_object_no_confirmation(object_id, time)
{
    $('#modalImportMain').modal('show');//mostro o modal de carregamento
    $.ajax({
        type: "POST",
        url: $('#src').val() + "/controllers/event/event_controller.php",
        data: {
            operation: 'add_event_object_delete',
            socialdb_event_create_date: time,
            socialdb_event_user_id: $('#current_user_id').val(),
            socialdb_event_object_item_id: object_id,
            socialdb_event_collection_id: $('#collection_id').val()}
    }).done(function (result) {
        $('#modalImportMain').modal('hide');//escondo o modal de carregamento
        elem_first = jQuery.parseJSON(result);
        showList($('#src').val());
        set_containers_class($('#collection_id').val());
        showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
    });
}

// deletar objeto
function delete_collection(title, text, collection_id, time, collection_root_id) {
    swal({
        title: title,
        text: text,
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
                    operation: 'add_event_collection_delete',
                    socialdb_event_create_date: time,
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_delete_collection_id: collection_id,
                    socialdb_event_collection_id: collection_root_id}
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//escondo o modal de carregamento
                elem_first = jQuery.parseJSON(result);
                showList($('#src').val());
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
            });
        }
    });
}

function delete_collection_redirect(title, text, collection_id, time, collection_root_id) {
    swal({
        title: title,
        text: text,
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
                    operation: 'add_event_collection_delete',
                    socialdb_event_create_date: time,
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_delete_collection_id: collection_id,
                    socialdb_event_collection_id: collection_root_id}
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//escondo o modal de carregamento
                elem_first = jQuery.parseJSON(result);
                showList($('#src').val());
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                window.location = elem_first.url;
            });
        }
    });
}

function clean_collection(title, text, collection_id) {
    swal({
        title: title,
        text: text,
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
                url: $('#src').val() + "/controllers/object/object_controller.php",
                data: {
                    operation: 'clean_collection_itens',
                    collection_id: collection_id}
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//escondo o modal de carregamento
                elem_first = jQuery.parseJSON(result);
                showList($('#src').val());
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
            });
        }
    });
}

function eliminate_itens_collection(main_title, desc, bulkds, collection_id) {
    swal({
        title: main_title,
        text: desc,
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
                url: $('#src').val() + "/controllers/object/object_controller.php",
                data: {
                    operation: 'eliminate_itens',
                    ids: bulkds}
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//escondo o modal de carregamento
                elem_first = jQuery.parseJSON(result);
                wpquery_filter();
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
            });
        }
    });
}

function move_items_to_trash(title, text, obj_ids, collection_id) {
    swal({
       title: title,
       text: text,
       showCancelButton: true,
       closeOnCancel: true,
       cancelButtonText: "Cancelar"
    }, function(isConfirm){
        if (isConfirm) {
            $("#modalImportMain").modal('show');
            $.ajax({
                type: "POST",
                url: $("#src").val() + "/controllers/object/object_controller.php",
                data: { operation: 'move_items_to_trash', objects_ids: obj_ids, collection_id: collection_id }
            }).done(function(r){
                $("#modalImportMain").modal('hide');
                var res = $.parseJSON(r);              
                showAlertGeneral(res.deleted[0].title, res.deleted[0].msg, res.deleted[0].type);
                showList($('#src').val());
            });
        }
    });
}

function report_abuse_object(title, text, object_id, time) {
    $('#modal_delete_object' + object_id).modal('hide');
    swal({
        title: title,
        text: text,
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
                    socialdb_event_create_date: time,
                    socialdb_event_observation: $('#observation_delete_object' + object_id).val(),
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_object_item_id: object_id,
                    socialdb_event_collection_id: $('#collection_id').val()}
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//escondo o modal de carregamento
                elem_first = jQuery.parseJSON(result);
                showList($('#src').val());
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
            });
        }
    });
}

function report_abuse_collection(title, text, collection_id, time, collection_root_id) {
    $('#modal_delete_object' + collection_id).modal('hide');
    $('#modal_delete_collection' + collection_id).modal('hide');
    swal({
        title: title,
        text: text,
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
                    operation: 'add_event_collection_delete',
                    socialdb_event_create_date: time,
                    socialdb_event_observation: $('#observation_delete_collection' + collection_id).val(),
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_delete_collection_id: collection_id,
                    socialdb_event_collection_id: collection_root_id}
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//escondo o modal de carregamento
                elem_first = jQuery.parseJSON(result);
                showList($('#src').val());
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
            });
        }
    });
}


function remove_event_tag_classication(title, text, tag_id, object_id, time) {
    swal({
        title: title,
        text: text,
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
                    operation: 'add_event_classification_delete',
                    socialdb_event_create_date: time,
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_classification_object_id: object_id,
                    socialdb_event_classification_term_id: tag_id,
                    socialdb_event_classification_type: 'tag',
                    socialdb_event_collection_id: $('#collection_id').val()}
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//escondo o modal de carregamento
                set_containers_class($('#collection_id').val());
                elem_first = jQuery.parseJSON(result);
                show_classifications(object_id);
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);

            });
        }
    });
}