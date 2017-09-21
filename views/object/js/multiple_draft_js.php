<script>
    /**
     * funcao que salva os rascunhos do item
     * @returns {undefined}
     */
    function createMultipleDraft() {
        $('#form_properties_items .auto-save').change(function () {
            var verify = $('#sumbit_multiple_items').serialize();
            $.ajax({
                url: $('#src').val() + '/controllers/object/object_draft_controller.php',
                type: 'POST',
                data: verify
            }).done(function (result) {
                $('.is_first_time').val('false')
                elem_first = jQuery.parseJSON(result);
                var string = '<?php _e('Automatically saved in', 'tainacan') ?> ' + elem_first.date
                        + ' of ' + elem_first.hour;
                $('#draft-text').text(string);
            });
        });
    }
    setTimeout(function () {
        createMultipleDraft();
    }, 6000);

    function back_main_list_discard() {
        swal({
            title: '<?php _e('Attention','tainacan') ?>',
            text: '<?php _e('Confirm your action','tainacan') ?>',
            type: "info",
            showCancelButton: true,
            confirmButtonClass: 'btn-primary',
            closeOnConfirm: true,
            closeOnCancel: true,
            confirmButtonText: "<?php _e('Back and discard','tainacan') ?>",
            cancelButtonText: "<?php _e('Just back', 'tainacan') ?>",
        },
                function (isConfirm) {
                    $('#form').hide();
                    $("#tainacan-breadcrumbs").hide();
                    $('#configuration').hide();
                    $('#main_part').show();
                    $('#display_view_main_page').show();
                    $("#container_three_columns").removeClass('white-background');
                    $('#menu_object').show();
                    if (isConfirm) {
                        $.ajax({
                            url: $('#src').val() + '/controllers/object/object_draft_controller.php',
                            type: 'POST',
                            data: {operation: 'clear_betafiles', collection_id: $('#collection_id').val()}
                        }).done(function (result) {
                            // $('html, body').animate({
                            //   scrollTop: parseInt($("#wpadminbar").offset().top)
                            // }, 900);  
                        });
                    }
                });
    }
</script>
