<script>
    $(function () {
        var src = $('#src').val();
        if ($('#open_wizard').val() == 'true') {
            $('#btn_back_collection').hide();
            $('#submit_configuration').hide();
            $('#ranking_tabs').hide();
        }
        else {
            $('#MyWizard').hide();
            $('#ranking_create_opt').hide();
            $('#save_and_next').hide();
            $('#ranking_tabs').show();
        }
        $('#collection_list_ranking_id').val($('#collection_id').val());
        $('#collection_ranking_type_id').val($('#collection_id').val());
        $('.edit').click(function (e) {
            var id = $(this).closest('td').find('.post_id').val();
            $.get(src + '/views/ranking/edit.php?id=' + id, function (data) {
                $("#form").html(data);
                $('#form').show();
                $("#list").hide();
                $('#create_button').hide();
                e.preventDefault();
            });
            e.preventDefault();
        });


        $('.remove').click(function (e) {
            var id = $(this).closest('td').find('.post_id').val();
            $.get(src + '/views/ranking/delete.php?id=' + id, function (data) {
                $("#remove").html(data);
                $("#remove").show();
                $("#form").hide(data);
                ;
                $("#list").hide();
                $('#create_button').hide();
            });
            e.preventDefault();
        });


<?php // Submissao do form de para remocao  ?>
        $('#submit_delete_ranking').submit(function (e) {
            e.preventDefault();
            $("#modal_remove_ranking").modal('hide');
             $('#modalImportMain').modal('show');//mostra o modal de carregamento
            $.ajax({
                url: src + '/controllers/ranking/ranking_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                 $('#modalImportMain').modal('hide');//esconde o modal de carregamento
                elem = jQuery.parseJSON(result);
                list_ranking();
                if (elem.success === 'true') {
                     $("#alert_success_properties").hide();
                    $("#alert_error_properties").hide();
                    $("#alert_success_properties").show();
                } else {
                     $("#alert_success_properties").hide();
                    $("#alert_error_properties").hide();
                    $("#alert_error_properties").show();
                }
                $('.dropdown-toggle').dropdown();
            });
            e.preventDefault();
        });
        
         $('#formRankingType').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: src + '/controllers/ranking/ranking_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                //elem = jQuery.parseJSON(result);
                nextStep();
                $('.dropdown-toggle').dropdown();
            });
            e.preventDefault();
        });


        list_ranking();
    });


    function list_ranking() {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {collection_id: $('#collection_list_ranking_id').val(), operation: 'list_ranking'}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if (elem.no_properties !== true) {
                $('#no_properties_object').hide();
                $('#table_ranking').html('');
                $.each(elem.rankings, function (idx, ranking) {
                    if(ranking.metas.socialdb_property_created_category==elem.category_root){
                    $('#table_ranking').append('<tr><td>' + ranking.name + '</td><td>' + ranking.type + '\
        </td><td><input type="hidden" class="ranking_id" value="' + ranking.id + '">\n\
<a onclick="edit_ranking(this)" class="edit_ranking" href="#table_ranking">\n\
<span class="glyphicon glyphicon-edit"><span></a></td><td><input type="hidden" class="ranking_id" value="' + ranking.id + '">\n\
<input type="hidden" class="ranking_name" value="' + ranking.name + '">\n\
<a onclick="delete_ranking(this)" class="delete_ranking" href="#table_ranking"><span class="glyphicon glyphicon-remove">\n\
<span></a></td></tr>');
                     }else{
                          $('#table_ranking').append('<tr><td>' + ranking.name + '</td><td>' + ranking.type + '\
        </td><td><input type="hidden" class="ranking_id" value="' + ranking.id + '">\n\
</td><td><input type="hidden" class="ranking_id" value="' + ranking.id + '">\n\
<input type="hidden" class="ranking_name" value="' + ranking.name + '">\n\
</td></tr>');
                     }
                });
                $('#list_ranking').show();
            } else {
                $('#list_ranking').hide();
                $('#no_properties_object').show();
            }
            $('.dropdown-toggle').dropdown();
        });
    }

    function delete_ranking(element) {
        $("#ranking_delete_collection_id").val($("#collection_id").val());
        var id = $(element).closest('td').find('.ranking_id').val();
        var name = $(element).closest('td').find('.ranking_name').val();
        $("#ranking_delete_id").val(id);
        $("#deleted_ranking_name").text(name);
        $("#modal_remove_ranking").modal('show');
    }


    function add_new() {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {collection_id: $('#collection_list_ranking_id').val(), operation: 'add_new'}
        }).done(function (result) {
            $('#main_part').hide();
            $('#configuration').html(result);
            $('#configuration').show();
        });
        $('.dropdown-toggle').dropdown();
    }

    function edit_ranking(element) {
        var id = $(element).closest('td').find('.ranking_id').val();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {collection_id: $('#collection_list_ranking_id').val(), ranking_id: id, operation: 'edit_ranking'}
        }).done(function (result) {
            $('#main_part').hide();
            $('#configuration').html(result);
            $('#configuration').show();
        });
        $('.dropdown-toggle').dropdown();
    }

    function showPersonalizeRanking() {
        $("#show_ranking_link").hide('slow');
        $("#hide_ranking_link").show('slow');
        $(".categories_menu").show('slow');
    }

    function hidePersonalizeRanking() {
        $("#ranking_tabs").hide('slow');
        $("#hide_ranking_link").hide('slow');
        $("#show_ranking_link").show('slow');
    }

    function nextStep() {
       // $('#configuration').hide();
       // $('#configuration').html('');
        showSearchConfiguration('<?php echo get_template_directory_uri() ?>');
    }

</script>
