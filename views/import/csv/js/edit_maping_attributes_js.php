<script>
    $(function () {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/import/import_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'generate_selects'}
        }).done(function (result) {
            $('.data').html(result);
            set_values_csv();
        });
    });
    
    function cancel_import_csv() {
        $('#maping_container_csv').hide();
        $('#validate_url_csv_container').show('slow');
    }

    function set_values_csv() {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                mapping_id: $("#socialdb_csv_mapping_id").val(),
                operation: 'get_mapping_csv'}
        }).done(function (result) {
            var jsonObject = jQuery.parseJSON(result);
            if (jsonObject && jsonObject != null) {
                $.each(jsonObject.mapping, function (id, object) {
                    $('[name=' + object.value + ']').val(object.socialdb_entity);
                });
            }

        });
    }


    function update_mapping() {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                form: $("#form_import_csv_edit_mapping").serialize(),
                mapping_id: $("#socialdb_csv_mapping_id").val(),
                operation: 'updating_mapping_csv'}
        }).done(function (result) {
            listTableCSV();
            $('#maping_container_csv').hide();
            $('#validate_url_csv_container').show('slow');
            showAlertGeneral('<?php _e('Success','tainacan'); ?>','<?php _e('Edited successfully.','tainacan'); ?>','success');
        });
    }

</script>