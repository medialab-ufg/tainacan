<?php ?>
<script>
    $(function () {

        //#1 backgorund
        var is_col_header_visible = $(".collection_header").is(":visible");
        if (!is_col_header_visible) {
            //$('.header-navbar').css("margin-bottom", 0);
            $('body').css('background-color', '#f2f2f2');
        }
        $('#advanced_search_wp_query_args').val($('#wp_query_args').val());// pega os parametros de pesquisa da colecao atual
        show_collection_properties($('#collection_id').val());//pega as propriedades da colecao atual
        select_collection();// execucao
        // quando o formulario de pesquisa e submetido   
        $('#advanced_search_submit').submit(function (e) {
            e.preventDefault();
            show_modal_main();
            $.ajax({
                url: $('#src').val() + '/controllers/advanced_search/advanced_search_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                hide_modal_main();
                if (elem.not_found) {
                    swal({
                        title: '<?php _e("Attention!", 'tainacan') ?>',
                        text: '<?php _e("No results found!", 'tainacan') ?>',
                        type: "warning",
                        cancelButtonText: '<?php _e("Cancel", 'tainacan') ?>',
                        showCancelButton: true,
                        confirmButtonClass: 'btn-success',
                        closeOnConfirm: true,
                        closeOnCancel: true
                    });
                } else {
                    $('#container_filtros').hide();
                    $('#resultados_advanced_search').show();
                    $('#container_resultados_advanced_search').html(elem.page);
                    $('.modal').modal('hide');
                }

//                $('html, body').animate({
//                    scrollTop: parseInt($("#show-results-advanced-search").offset().top)
//                }, 1000);
                if (elem.args_collection) {
                    $('#advanced_search_wp_query_args_collection').val(elem.args_collection);
                }
                if (elem.args_item) {
                    $('#advanced_search_wp_query_args_item').val(elem.args_item);
                }

                setTimeout(function () {
                    $('.modal').modal('hide');
                    $('.modal-backdrop').modal('hide');
                    $('.modal-backdrop').hide();
                }, 2000);

            });
            e.preventDefault();
        });
    });


    function revalidate_autocomplete(collection_id) {
        $("#advanced_search_title").autocomplete({
            source: $('#src').val() + '/controllers/collection/collection_controller.php?operation=list_items_search_autocomplete_advanced_search&collection_id=' + collection_id,
            messages: {
                noResults: '',
                results: function () {
                }
            },
            minLength: 2,
            select: function (event, ui) {
                console.log(event);
                $("#advanced_search_title").val('');
                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                var temp = $("#property_value_").val();
                if (typeof temp == "undefined") {
                    $("#advanced_search_title").val(ui.item.value);
                }
            }
        });

    }

    // atualiza o container com as propriedades da colecao que foi selecionada no selectbox
    function show_collection_properties(collection_id) {
        $("#advanced_search_collection_id").val(collection_id);
        revalidate_autocomplete(collection_id);
        $.ajax({
            url: $('#src').val() + '/controllers/advanced_search/advanced_search_controller.php',
            type: 'POST',
            data: {operation: 'show_object_properties_auto_load', collection_id: collection_id}
        }).done(function (result) {
            $('#propertiesAdvancedSearch').html(result);
            $('#propertiesAdvancedSearch').show();
        });
    }

    //funcao que redireciona para a colecao
    function redirect_collection(collection_id) {
        $.ajax({
            url: $('#src').val() + '/controllers/advanced_search/advanced_search_controller.php',
            type: 'POST',
            data: {operation: 'redirect_collection', collection_id: collection_id}
        }).done(function (result) {
            elem = JSON.parse(result);
            window.location = elem.url;
        });
    }

    // monta o select com todas as colecoes do repositorio
    function select_collection() {
        $("#advanced_search_collection").attr('disabled', 'disabled');
        $("#advanced_search_collection").addClass('ui-autocomplete-loading');
        $("#advanced_search_collection").html("<option  value='' ><?php _e('Loading Collections', 'tainacan') ?>");
        $.ajax({
            url: $('#src').val() + '/controllers/advanced_search/advanced_search_controller.php',
            type: 'POST',
            data: {operation: 'select_collection'}
        }).done(function (result) {
            elem = JSON.parse(result);
<?php if (!empty($home_search_term)) { ?>
                $('#advanced_search_collection').val('<?php echo get_option('collection_root_id'); ?>');
                $('#advanced_search_submit').trigger('submit');
<?php } ?>
            $("#advanced_search_collection").removeAttr('disabled');
            $("#advanced_search_collection").removeClass('ui-autocomplete-loading');
            $("#advanced_search_collection").html('');
            var collection_root_id = '<?php echo get_option('collection_root_id'); ?>';
            $("#advanced_search_collection").append("<option  value='" + collection_root_id + "' ><?php _e('All Colections', 'tainacan') ?></option>");
            $.each(elem, function (idx, collection) {
                if (collection && collection !== false && collection.value != collection_root_id) {
                    $("#advanced_search_collection").append("<option  value='" + collection.value + "' >" + collection.name + "</option>");
                }
            });
            $("#advanced_search_collection").val($('#collection_id').val());
        });
    }

    function reboot_form() {
        showAdvancedSearch($('#src').val());
    }

    function slide_down_metadata_form() {
        $('#propertiesAdvancedSearch').removeClass('hide');
        $('#slide_up_button').hide();
        $('#slide_down_button').show();

    }
    function slide_up_metadata_form() {
        $('#propertiesAdvancedSearch').addClass('hide');
        $('#slide_down_button').hide();
        $('#slide_up_button').show();
    }
</script>