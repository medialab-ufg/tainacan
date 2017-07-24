<script>
    $(function () {
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/import/import_controller.php",
                data: {collection_id: $('#collection_id').val(), operation: 'generate_selects'}
            }).done(function (result) {
                $('.data_<?php echo $counter ?>').html(result);

            });
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/export/export_controller.php",
                data: {
                    collection_id: $('#collection_id').val(),
                    operation: 'generate_selects'}
            }).done(function (result) {
                $('.data_dubin_core_<?php echo $counter ?>').html(result);

            });
        });
    
     function remove_tag_oai_dc(id) {
        $('#tag_'+id).hide();
        $('input[name=mapping_dublin_core_'+id).val('');
        $('input[name=mapping_socialdb_'+id).val('');
        $('input[name=qualifier_'+id).val('');
        
     }
    
</script>