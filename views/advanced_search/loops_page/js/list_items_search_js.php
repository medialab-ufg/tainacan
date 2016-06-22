<script>
    $(function () {
        var src = $('#src').val();
    $('.pagination_items').jqPagination({
            link_string: '/?page={page_number}',
            max_page: $('#number_pages_items').val(),
            paged: function (page) {
                 $('html,body').animate({scrollTop: 0}, 'slow');
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/advanced_search/advanced_search_controller.php",
                    data: {operation: 'wpquery_page_advanced_item', posts_per_page:$('#items-per-page-items').val(), wp_query_args: $('#advanced_search_wp_query_args_item').val(), value: page}
                }).done(function (result) {
                     elem = jQuery.parseJSON(result);
                    $('#ad_search_items').html(elem.page);
                    $('#advanced_search_wp_query_args_item').val(elem.args);
                });
            }
        });
        $('.nav-tabs').tab();
     });
    //BEGIN: funcao que altera a qtd de items na visualizacao
     function change_qtd_elements_items(seletor){
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/advanced_search/advanced_search_controller.php",
            data: {operation: 'wpquery_page_advanced_item', 
                wp_query_args: $('#advanced_search_wp_query_args_item').val(),
                posts_per_page:$(seletor).val(),
                value: $('#actual_page_items').val()}
        }).done(function (result) {
             elem = jQuery.parseJSON(result);
            $('#ad_search_items').html(elem.page);
            $('#advanced_search_wp_query_args_item').val(elem.args);
        });
     } 
    function show_info(id) {
        check_privacity_info(id);
        list_ranking(id);
        list_files(id);
        list_properties(id);
        list_properties_edit_remove(id)
        $("#more_info_show_" + id).toggle();
        $("#less_info_show_" + id).toggle();
        $("#all_info_" + id).toggle('slow');
    }
//BEGIN: funcao para mostrar os arquivos
    function list_files(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'show_files', object_id: id}
        }).done(function (result) {
            $('#list_files_' + id).html(result);
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
//END
//BEGIN: funcao para mostrar votacoes
    function list_ranking(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'list_ranking_object', object_id: id}
        }).done(function (result) {
            $('#list_ranking_' + id).html(result);
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }

    function list_ranking_auto_load(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'list_ranking_object', object_id: id}
        }).done(function (result) {
//        $('#list_ranking_auto_load_'+id).html(result);
//        $('#list_ranking_auto_load_'+id).show();
            $('#ranking_auto_load').html(result);
            $('#ranking_auto_load').shshow_classificiations_ow();
        });
    }
//END
//BEGIN:as proximas funcoes sao para mostrar os eventos
// list_properties(id): funcao que mostra a primiera listagem de propriedades
    function list_properties(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'list_properties', object_id: id}
        }).done(function (result) {
            $('#list_all_properties_' + id).html(result);
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
// mostra a listagem apos clique no botao para edicao e exclusao
    function list_properties_edit_remove(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'list_properties_edit_remove', object_id: id}
        }).done(function (result) {
            $('#list_properties_edit_remove').html(result);
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
// mostra o formulario para criacao de propriedade de dados
    function show_form_data_property(object_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'show_form_data_property', object_id: object_id}
        }).done(function (result) {
            $('#data_property_form_' + object_id).html(result);
            $('#list_all_properties_' + object_id).hide();
            $('#object_property_form_' + object_id).hide();
            $('#edit_data_property_form_' + object_id).hide();
            $('#edit_object_property_form_' + object_id).hide();
            $('#data_property_form_' + object_id).show();
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
// mostra o formulario para criacao de propriedade de objeto
    function show_form_object_property(object_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'show_form_object_property', object_id: object_id}
        }).done(function (result) {
            $('#object_property_form_' + object_id).html(result);
            $('#list_all_properties_' + object_id).hide();
            $('#data_property_form_' + object_id).hide();
            $('#edit_data_property_form_' + object_id).hide();
            $('#edit_object_property_form_' + object_id).hide();
            $('#object_property_form_' + object_id).show();
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
// funcao acionando no bolta voltar que mostra a listagem principal
    function back_button(object_id) {
        $('#data_property_form_' + object_id).hide();
        $('#object_property_form_' + object_id).hide();
        $('#edit_data_property_form_' + object_id).hide();
        $('#edit_object_property_form_' + object_id).hide();
        $('#list_all_properties_' + object_id).show();
    }
// END:fim das funcoes que mostram as propriedades
//funcao que mostra as classificacoes apos clique no botao show_classification
   function show_classifications_search(object_id) {
        var close_box = "<a href='javascript:void(0)' class='close-metadata-box' onclick='toggle_item_box_elements_search("+object_id+")'>" +
            "<span class='glyphicon glyphicon-remove-circle'></span></a>";

        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'show_classifications', object_id: object_id}
        }).done(function (result) {
            toggle_item_box_elements_search(object_id);
            $('#classifications_' + object_id+ '_search').html( close_box + result).fadeIn();
            $('#show_classificiations_' + object_id+ '_search').fadeOut();
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }

    function toggle_item_box_elements_search(object_id) {
        var elements = [ '.item-display-title', '.item-description', '.item-author', '.item-creation'];
        $.each(elements, function(index, element) {
            $('#classifications_' + object_id+ '_search').parent('.item-meta').find(element).toggle();
        });

        $('#classifications_' + object_id + '_search .close-metadata-box').toggle();
        $('#classifications_' + object_id + '_search').toggleClass('shown-classifications').toggle();
        $('#show_classificiations_' + object_id+ '_search').fadeIn();
    }

//mostrar modal de denuncia
    function show_report_abuse(object_id) {
        $('#modal_delete_object' + object_id).modal('show');
    }
// editando objeto
    function edit_object(object_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'edit', object_id: object_id}
        }).done(function (result) {
            $("#container_socialdb").hide('slow');
            $("#form").hide();
            $("#form").html(result);
            $('#form').show('slow');
            $('#create_button').hide();
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }

    function redirect_facebook(object_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {fb_id:$('#socialdb_fb_api_id').val(),collection_id: $('#collection_id').val(), operation: 'redirect_facebook', object_id: object_id}
        }).done(function (result) {
            json = jQuery.parseJSON(result);
            //console.log(json);
            window.open(json.redirect, '_blank');
            // window.location = json.redirect;
        });
    }

    function show_rankings(object_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'list_ranking_object', object_id: object_id}
        }).done(function (result) {
            $('#rankings_' + object_id).html(result);
            $('#show_rankings_' + object_id).hide();
            $('#rankings_' + object_id).show();
        });
    }

    function check_privacity_info(id)
    {
        $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: {operation: 'check_privacity', collection_id: id}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if (elem.privacity == false)
            {
                redirect_privacity(elem.title, elem.msg, elem.url);
            }
        });
    }
     function showPopoverSearch(id) {
        // pop up #example1, #example2, #example3 with same content
        $('#popover_network' + id+'_search').popover({
            html: true,
            content: function () {
                return $('#popover_content_wrapper' + id+'_search').html();
            }
        });
    }
    //retorno para as vusca
    function back_to_search_form(){
        $('#container_filtros').show();
        $('#resultados_advanced_search').hide();
        $('#container_resultados_advanced_search').html(''); 
    }

</script>
