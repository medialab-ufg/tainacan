<script>
    $(function () {
        var src = $('#src').val();
         $('#single_submit_form_property_data').submit(function (e) {
            $.ajax({
                url: $('#src').val() + '/controllers/event/event_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                //list_main_ordenation();
                var elem = jQuery.parseJSON(result);
                // o id do objeto
                var obj_id = $('#single_event_add_property_data_object_id').val();
                back_button_single(obj_id);
                list_properties_single(obj_id);
                showAlertGeneral(elem.title, elem.msg, elem.type);
                //limpando caches
                delete_all_cache_collection();
            });
            e.preventDefault();
        });

    });

    function back_button(object_id) {
        $('#single_data_property_form_' + object_id).hide();
        $('#single_object_property_form_' + object_id).hide();
        $('#single_edit_data_property_form_' + object_id).hide();
        $('#single_edit_object_property_form_' + object_id).hide();
        $('#single_list_all_properties_' + object_id).show();
    }

</script>
