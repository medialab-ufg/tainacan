<script>
    /**
     * funcao que salva o rascunho do item
     * @returns {undefined}
     */
    function createDraft() {
    }

    function back_main_list_discard(id) {
        swal({
            title: 'Atenção ',
            text: 'Confirme sua ação:',
            type: "info",
            showCancelButton: true,
            confirmButtonClass: 'btn-primary',
            closeOnConfirm: true,
            closeOnCancel: true,
            confirmButtonText: "Voltar e descartar",
            cancelButtonText: "Apenas voltar",
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
                            url: $('#src').val() + '/controllers/object/object_controller.php',
                            type: 'POST',
                            data: {operation: 'delete_temporary_object', collection_id: $('#collection_id').val(), delete_draft: 'true', ID: id}
                        }).done(function (result) {
                            // $('html, body').animate({
                            //   scrollTop: parseInt($("#wpadminbar").offset().top)
                            // }, 900);  
                        });
                    }
                });
    }
</script>
