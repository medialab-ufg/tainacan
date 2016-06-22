<script>
    var intervalo;
    $(function () {
        var src = $('#src').val();
        $('#collection_import_id').val($('#collection_id').val());
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/social_network/social_mapping_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'generate_selects', social_network: $('#social_network').val()}
        }).done(function (result) {
            $('.data').html(result);
            set_values();
        });
    });

    function set_values() {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                mapping_id: $("#mapping_id").val(),
                term: $("#social_network_term").val(),
                operation: 'get_mapping_social_network'}
        }).done(function (result) {
            var jsonObject = jQuery.parseJSON(result);
            if (jsonObject && jsonObject != null) {
                $.each(jsonObject.mapping, function (id, object) {
                    $('[name=' + object.socialdb_entity + ']').val(object.tag);
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
                form: $("#form_import_social").serialize(),
                mapping_id: $("#mapping_id").val(),
                social_network: $('#social_network').val(),
                term: $("#social_network_term").val(),
                operation: 'updating_social_mapping'}
        }).done(function (result) {
            showAlertGeneral('Sucesso','Mapeamento salvo.','success');
            $('#edit_mapping').hide();
            $('#list_social_network').show('slow');
        });
    }
    
    function cancel_export(){
         $('#edit_mapping').hide();
         $('#list_social_network').show('slow');
    }

</script>