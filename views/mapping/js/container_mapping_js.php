<!--
    js para o submit do modal do tipo handle
-->
<script>
    $(function () {
       $('#submit_mapping_handle').submit(function (e) {
            e.preventDefault();
            $(".modal").modal('hide');
            var mapped_extracted =  $( "#tainacan-mapped-ul").sortable("toArray");
            update_position_mapped(mapped_extracted);
            show_modal_main();
            $.ajax({
                url: $('#src').val() + '/controllers/mapping/mapping_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                hide_modal_main();
                $('.dropdown-toggle').dropdown();
                elem_first = jQuery.parseJSON(result);
                if (elem_first.result==true) {
                    $.ajax({
                        type: "POST",
                        url: $('#src').val() + "/controllers/object/object_controller.php",
                        data: {collection_id: $('#collection_id').val(), operation: 'edit', object_id: elem_first.object_id}
                    }).done(function (result) {
                        hide_modal_main();
                        $("#form").html('');
                        $('#main_part').hide();
                        $('#display_view_main_page').hide();
                        $('#loader_collections').hide();
                        $('#configuration').html(result).show();
                        $('.dropdown-toggle').dropdown();
                        $('.nav-tabs').tab();
                    });
                } else {
                    showAlertGeneral('<?php _e('Error', 'tainacan') ?>', '<?php _e('Unformated xml', 'tainacan') ?>', 'error');
                }

            });
        });
    });
    //
    
    
</script>
