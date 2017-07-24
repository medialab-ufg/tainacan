<script>
    $(function () {
        var src = $('#src').val();
         $('#submit_form_property_data').submit(function (e) {
            $.ajax({
                url: $('#src').val() + '/controllers/event/event_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
               get_categories_properties_ordenation();
                list_properties_edit_remove($('#event_add_property_data_object_id').val());
                elem = jQuery.parseJSON(result);
                back_button($('#event_add_property_data_object_id').val());// o id do objeto
                list_properties($('#event_add_property_data_object_id').val());// o id do objeto
                showAlertGeneral(elem.title, elem.msg, elem.type);
            });
            e.preventDefault();
        });
        $('.edit').click(function (e) {
            var id = $(this).closest('td').find('.post_id').val();
            $.get(src + '/views/object/edit.php?id=' + id, function (data) {
                $("#form").html(data);
                $('#form').show();
                $("#list").hide();
                $('#create_button').hide();
                e.preventDefault();
            });
            e.preventDefault();
        });

        $('.remove').click(function (e) {
            var id = $(this).closest('td').find('.post_id').val();
            $.get(src + '/views/object/delete.php?id=' + id, function (data) {
                $("#remove").html(data);
                $("#remove").show();
                $("#form").hide(data);
                ;
                $("#list").hide();
                $('#create_button').hide();
            });
            e.preventDefault();
        });

    });

    function edit_data_property(property_id, object_id) {
        $("#cancel_" + property_id + "_" + object_id).show();
        $("#edit_" + property_id + "_" + object_id).hide();
        $("#save_" + property_id + "_" + object_id).show();
        $("#property_value_" + property_id + "_" + object_id).prop({
            disabled: false
        });
    }
    function cancel_data_property(property_id, object_id) {
        $("#property_value_" + property_id + "_" + object_id).val($("#property_" + property_id + "_" + object_id + "_value_before").val());
        $("#cancel_" + property_id + "_" + object_id).hide();
        $("#edit_" + property_id + "_" + object_id).show();
        $("#save_" + property_id + "_" + object_id).hide();
        $("#property_value_" + property_id + "_" + object_id).prop({
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
                property_data_value: $("#property_value_" + property_id + "_" + object_id).val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if (elem.pre_approved) {
                $("#property_" + property_id + "_" + object_id + "_value_before").val($("#property_value_" + property_id + "_" + object_id).val());
            } else {
                cancel_data_property(property_id, object_id)
            }
        });
    }

    function edit_object_property(property_id, object_id) {
        $("#cancel_" + property_id + "_" + object_id).show();
        $("#edit_" + property_id + "_" + object_id).hide();
        $("#save_" + property_id + "_" + object_id).show();
        $("#autocomplete_value_" + property_id + "_" + object_id).prop({
            disabled: false
        });
        $("#property_value_" + property_id + "_" + object_id).prop({
            disabled: false
        });
    }
    function cancel_object_property(property_id, object_id) {
        // metas
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {property_id: property_id,object_id:object_id , operation: 'get_property_object_value',}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if (elem.values) {
                $("#property_value_" + property_id + "_" + object_id).html();
                $.each(elem.values, function (idx, value) {
                    if (value && value !== false) {
                       $("#property_value_" + property_id + "_" + object_id).append("<option class='selected' value='" + value.id + "' selected='selected' >" + value.name + "</option>");
                    }
                });
            }else{
                $("#property_value_" + property_id + "_" + object_id).html('');
            }
            $('.dropdown-toggle').dropdown();
        });
        $("#cancel_" + property_id + "_" + object_id).hide();
        $("#edit_" + property_id + "_" + object_id).show();
        $("#save_" + property_id + "_" + object_id).hide();
        $("#autocomplete_value_" + property_id + "_" + object_id).prop({
            disabled: true
        });
        $("#property_value_" + property_id + "_" + object_id).prop({
            disabled: true
        });
    }
    function save_object_property(property_id, object_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/event/event_property_data_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                operation: 'update_value',
                object_id: object_id,
                property_id: property_id,
                property_data_value: $("#property_value_" + property_id + "_" + object_id).val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if (elem.pre_approved) {
                $("#property_" + property_id + "_" + object_id + "_value_before").val($("#property_value_" + property_id + "_" + object_id).val());
            } else {
                cancel_data_property(property_id, object_id)
            }
        });
    }
    function autocomplete_object_property(property_id, object_id) {
        $("#autocomplete_value_" + property_id + "_" + object_id).autocomplete({
            source: $('#src').val() + '/controllers/object/object_controller.php?operation=get_objects_by_property_json&property_id=' + property_id,
            messages: {
                noResults: '',
                results: function () {
                }
            },
            minLength: 2,
            select: function (event, ui) {
                $("#autocomplete_value_" + property_id + "_" + object_id).html('');
                $("#autocomplete_value_" + property_id + "_" + object_id).val('');
                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                var temp = $("#property_value_" + property_id + "_" + object_id + " [value='" + ui.item.value + "']").val();
                if (typeof temp == "undefined") {
                    $("#property_value_" + property_id + "_" + object_id).append("<option class='selected' value='" + ui.item.value + "' selected='selected' >" + ui.item.label + "</option>");

                }
                setTimeout(function () {
                    $("#autocomplete_value_" + property_id + "_" + object_id).val('');
                }, 100);
            }
        });
    }
    function clear_select_object_property(e) {
        $('option:selected', e).remove();
        //$('.chosen-selected2 option').prop('selected', 'selected');
    }
</script>
