<script>
    $(function () {
        $('#submit_form_event_edit_property_object').submit(function (e) {
            $.ajax({
                url: $('#src').val() + '/controllers/event/event_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                list_properties_edit_remove($('#event_edit_property_object_post_id').val());
                back_button($('#event_edit_property_object_post_id').val());
                list_properties($('#event_edit_property_object_post_id').val());
                //$("#dynatree").dynatree("getTree").reload();
                showAlertGeneral(elem.title, elem.msg, elem.type);
            });
            e.preventDefault();
        });
        if($('#event_edit_property_object_is_reverse_value').val()=='true'){
           var val = $('#event_edit_property_object_reverse_value').val();
            list_reverses_edit_event(val);
            $('#event_edit_show_reverse_properties').show();
        }
        // reverse property
        $("#event_edit_property_object_category_id").change(function (e) {
            $('#event_edit_show_reverse_properties').hide();
            $('#event_edit_property_object_is_reverse_false').prop('checked', true);
        });
        // reverse property    
        $('#event_edit_property_object_is_reverse_true').click(function (e) {
            list_reverses_edit_event();
            $('#event_edit_show_reverse_properties').show();
        });
        //reverse property  
        $('#event_edit_property_object_is_reverse_false').click(function (e) {
            $('#event_edit_show_reverse_properties').hide();
        });
    });
<?php // lista as propriedades da categoria que foi selecionada  ?>
    function list_reverses_edit_event(selected) {
        $.ajax({
            url: $('#src').val() + '/controllers/property/property_controller.php',
            type: 'POST',
            data: {collection_id: $("#collection_id").val(), category_id: $("#event_edit_property_object_category_id").val(), operation: 'show_reverses', property_id: $('#event_edit_property_category_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#event_edit_property_object_reverse').html('');
            if (elem.no_properties === false) {
                $.each(elem.property_object, function (idx, property) {
                    if (property.id == selected) {
                        $('#event_edit_property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                    } else {
                        $('#event_edit_property_object_reverse').append('<option value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                    }
                });
            } else {
                $('#event_edit_property_object_reverse').append('<option value="false"><?php _e('No properties added','tainacan'); ?></option>');
            }
        });
    }
</script>
