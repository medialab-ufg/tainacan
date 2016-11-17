<script>
    $(function () {
        $("#list-profiles").accordion({
            active: false,
            collapsible: true,
            header: "h2",
            heightStyle: "content"
        });

        $('#form-permission').submit(function (e) {
            e.preventDefault();
            $('#modalImportMain').modal('show');
            $.ajax({
                url: $('#src').val() + '/controllers/permission/permission_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                $('#modalImportMain').modal('hide');
                showPagePermission($('#src').val(),$('#collection_id').val())
                elem_first = jQuery.parseJSON(result);
                if (elem_first) {
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                } else {
                    showAlertGeneral('<?php _e('Error', 'tainacan') ?>', '<?php _e('Unformated xml', 'tainacan') ?>', 'error');
                }

            });
            e.preventDefault();
        });
    });

    function back_main_list() {
        $('#form').hide();
        $("#tainacan-breadcrumbs").hide();
        $('#configuration').hide();
        $('#main_part').show();
        $('#display_view_main_page').show();
    }
</script>    
