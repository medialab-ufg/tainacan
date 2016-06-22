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
                list_main_ordenation();
                elem = jQuery.parseJSON(result);
                back_button_single($('#single_event_add_property_data_object_id').val());// o id do objeto
                list_properties_single($('#single_event_add_property_data_object_id').val());// o id do objeto
                showAlertGeneral(elem.title, elem.msg, elem.type);
            });
            e.preventDefault();
        });

    });

</script>
