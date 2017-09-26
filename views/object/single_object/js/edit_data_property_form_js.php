<script>
    $(function () {
        var src = $('#src').val();
        $('#single_submit_form_edit_property_data').submit(function (e) {
            show_modal_main();
            $.ajax({
                url: $('#src').val() + '/controllers/event/event_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                hide_modal_main();
                elem = jQuery.parseJSON(result);
                back_button_single($('#single_event_edit_property_data_object_id').val());// o id do objeto
                list_properties_single($('#single_event_edit_property_data_object_id').val());// o id do objeto
                showAlertGeneral(elem.title, elem.msg, elem.type);
                //limpando caches
                delete_all_cache_collection();
            });
            e.preventDefault();
        });

    });

    function edit_data_property(property_id, object_id) {
        $("#single_cancel_" + property_id + "_" + object_id).show();
        $("#single_edit_" + property_id + "_" + object_id).hide();
        $("#single_save_" + property_id + "_" + object_id).show();
        $("#single_property_value_" + property_id + "_" + object_id).prop({
            disabled: false
        });
    }
    function cancel_data_property(property_id, object_id) {
        $("#single_property_value_" + property_id + "_" + object_id).val($("#property_" + property_id + "_" + object_id + "_value_before").val());
        $("#single_cancel_" + property_id + "_" + object_id).hide();
        $("#single_edit_" + property_id + "_" + object_id).show();
        $("#single_save_" + property_id + "_" + object_id).hide();
        $("#single_property_value_" + property_id + "_" + object_id).prop({
            disabled: true
        });
    }
    function save_data_property(property_id, object_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/event/event_property_data_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                operation: 'update_value',
                object_id: object_id,
                property_id: property_id,
                property_data_value: $("#single_property_value_" + property_id + "_" + object_id).val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            //limpando caches
            delete_all_cache_collection();
            if (elem.pre_approved) {
                $("#single_property_" + property_id + "_" + object_id + "_value_before").val($("#single_property_value_" + property_id + "_" + object_id).val());
            } else {
                cancel_data_property_single(property_id, object_id)
            }
        });
    }

   
</script>
