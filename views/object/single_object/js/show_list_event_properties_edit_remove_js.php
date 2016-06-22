<script>
function show_edit_data_property_form(object_id,property_id){
    $.ajax({
        type: "POST",
        url: $('#src').val()+"/controllers/object/objectsingle_controller.php",
        data: {collection_id:  $('#collection_id').val(),operation:'show_edit_data_property_form',property_id:property_id,object_id:object_id}
    }).done(function( result ) {
        console.log('#list_all_properties_'+object_id);
        $('#single_edit_data_property_form_'+object_id).html(result);
        $('#single_list_all_properties_'+object_id).hide();
        $('#single_data_property_form_'+object_id).hide();
        $('#single_object_property_form_'+object_id).hide();
        $('#single_edit_object_property_form_'+object_id).hide();
        $('#single_edit_data_property_form_'+object_id).show();
        $('.dropdown-toggle').dropdown();
        $('.nav-tabs').tab();
    });
}
function show_edit_object_property_form(object_id,property_id){
    $.ajax({
        type: "POST",
        url: $('#src').val()+"/controllers/object/objectsingle_controller.php",
        data: {collection_id:  $('#collection_id').val(),operation:'show_edit_object_property_form',property_id:property_id,object_id:object_id}
    }).done(function( result ) {
        console.log('#list_all_properties_'+object_id);
         $('#single_edit_object_property_form_'+object_id).html(result);
        $('#single_list_all_properties_'+object_id).hide();
        $('#single_data_property_form_'+object_id).hide();
        $('#single_object_property_form_'+object_id).hide();
        $('#single_edit_data_property_form_'+object_id).hide();
        $('#single_edit_object_property_form_'+object_id).show();
        $('.dropdown-toggle').dropdown();
        $('.nav-tabs').tab();
    });
}    


function show_confirmation_delete_property_object_event(object_id,property_id,property_name){
    swal({
        title: '<?php _e('Are you sure','tainacan') ?>',
        text: '<?php _e('Delete the object property ','tainacan') ?>'+'('+property_name+')',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: 'btn-danger',
        closeOnConfirm: false,
        closeOnCancel: true
    },
    function (isConfirm) {
        if (isConfirm) {
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/event/event_controller.php",
                data: {
                    operation: 'add_event_property_object_delete',
                    socialdb_event_create_date: <?php echo mktime(); ?>,
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_property_object_delete_id: property_id,
                    socialdb_event_collection_id: $('#collection_id').val()}
            }).done(function (result) {
                elem_first = jQuery.parseJSON(result);
                back_button(object_id);
                $("#dynatree").dynatree("getTree").reload();
                list_properties_single(object_id);
                list_properties_edit_remove_single(object_id);
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
            });
        }
    });
}

function show_confirmation_delete_property_data_event(object_id,property_id,property_name){
    swal({
        title: '<?php _e('Are you sure','tainacan') ?>',
        text: '<?php _e('Delete the data property ','tainacan') ?>'+'('+property_name+')',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: 'btn-danger',
        closeOnConfirm: false,
        closeOnCancel: true
    },
    function (isConfirm) {
        if (isConfirm) {
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/event/event_controller.php",
                data: {
                    operation: 'add_event_property_data_delete',
                    socialdb_event_create_date: <?php echo mktime(); ?>,
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_property_data_delete_id: property_id,
                    socialdb_event_collection_id: $('#collection_id').val()}
            }).done(function (result) {
                elem_first = jQuery.parseJSON(result);
                back_button_single(object_id);
                list_properties_single(object_id);
                list_properties_edit_remove_single(object_id);
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
            });
        }
    });
}
</script>
