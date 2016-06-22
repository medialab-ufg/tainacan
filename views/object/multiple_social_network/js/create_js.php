<script>
    $(function () {
        // #1 - breadcrumbs para localizacao da pagina
        $("#tainacan-breadcrumbs").show();
        $("#tainacan-breadcrumbs .current-config").text('<?php _e('Add new item - Insert URL','tainacan') ?>');
    });
    
    function back_main_list() {
        $('#form').hide();
        $("#tainacan-breadcrumbs").hide();
        $('#configuration').hide();
        $('#main_part').show();
        $('#display_view_main_page').show();
        $("#container_socialdb").show('fast');
        $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {operation: 'delete_temporary_object', ID: '<?php echo $object_id ?>'}
        }).done(function (result) {
            $('#main_part').show();
            $('#collection_post').show();
            $('#configuration').slideDown();
            $('#configuration').hide();
        });
    }

    function edit_items_uploaded() {
        show_modal_main();
        $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {
                operation: 'editor_items',
                collection_id: $('#collection_id').val(),
                object_id: '<?php echo $object_id ?>'}
        }).done(function (data) {
            hide_modal_main();
            if (data != 0) {
                $("#upload_container").hide();
                $('#editor_items').html(data);
                $("#editor_items").css('display', 'block');
            } else {
                showAlertGeneral('<?php _e("Attention!", 'tainacan') ?>', '<?php _e("File is too big or Uploaded, however, not supported by wordpress, please select valid files!", 'tainacan') ?>', 'error');
            }
        });
    }

    function upload_more_files() {
        swal({
            title: '<?php _e("Attention!", 'tainacan') ?>',
            text: '<?php _e("You did not finish your action. Are you sure to leave this page?", 'tainacan') ?>',
            type: "warning",
            cancelButtonText: '<?php _e("Cancel", 'tainacan') ?>',
            showCancelButton: true,
            confirmButtonClass: 'btn-success',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $("#editor_items").slideDown();
                $("#editor_items").hide();
                $('#upload_container').show();
                $("#tainacan-breadcrumbs .current-config").text('<?php _e('Add new item - Send local file','tainacan') ?>');
            }
        });
    }
</script>
