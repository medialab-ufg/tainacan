<script>
    $(function () {
        $('#single_submit_form_property_object').submit(function (e) {
            $.ajax({
                url: $('#src').val() + '/controllers/event/event_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                back_button_single($('#single_event_add_property_object_id').val());
                $("#dynatree").dynatree("getTree").reload();
                list_properties_single($('#single_event_add_property_object_id').val());
                showAlertGeneral(elem.title, elem.msg, elem.type);
            });
            e.preventDefault();
        });
        if ($('#single_event_add_property_object_is_reverse_value').val() == 'true') {
            var val = $('#event_add_property_object_reverse_value').val();
            single_list_reverses_event(val);
            $('#single_event_add_show_reverse_properties').show();
        }
        // reverse property
        $("#single_event_add_property_object_category_id").change(function (e) {
            $('#single_event_add_show_reverse_properties').hide();
            $('#single_event_add_property_object_is_reverse_false').prop('checked', true);
        });
        // reverse property    
        $('#single_event_add_property_object_is_reverse_true').click(function (e) {
            single_list_reverses_event();
            $('#single_event_add_show_reverse_properties').show();
        });
        //reverse property  
        $('#single_event_add_property_object_is_reverse_false').click(function (e) {
            $('#single_event_add_show_reverse_properties').hide();
        });
    });
<?php // lista as propriedades da categoria que foi selecionada   ?>
    function single_list_reverses_event(selected) {
        $.ajax({
            url: $('#src').val() + '/controllers/property/property_controller.php',
            type: 'POST',
            data: {collection_id: $("#collection_id").val(), category_id: $("#single_event_add_property_object_category_id").val(), operation: 'show_reverses', property_id: $('#single_event_add_property_category_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#single_event_add_property_object_reverse').html('');
            if (elem.no_properties === false) {
                $.each(elem.property_object, function (idx, property) {
                    if (property.id == selected) {
                        $('#single_event_add_property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                    } else {
                        $('#single_event_add_property_object_reverse').append('<option value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                    }
                });
            } else {
                $('#single_event_add_property_object_reverse').append('<option value="false"><?php _e('No properties added','tainacan'); ?></option>');
            }
        });
    }
</script>
