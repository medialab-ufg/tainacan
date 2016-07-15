<script>
    $(function () {
        change_breadcrumbs_title('<?php _e('Import','tainacan') ?>');

        $('#validate_url_container').show('slow');
        
        var src = $('#src').val();
        $('#collection_import_id').val($('#collection_id').val());
        $('#collection_import_csv_id').val($('#collection_id').val());
        $('#collection_id_export_csv').val($('#collection_id').val());
        $('#collection_id_zip').val($('#collection_id').val());

        
        $('#click_zip').click(function (e) {
            e.preventDefault();
            $(this).tab('show')
        });

    });

    function get_icon(icon) {
        var path = '<?php echo get_template_directory_uri() ?>';
        return path + "/libraries/images/icons/icon-" + icon;
    }
    var edit_icon = get_icon("edit.png");
    var delete_icon = get_icon("delete_collection_redirect.png");


    $('#formCsv').submit(function (e) {
        show_modal_main();
        $.ajax({
            url: $('#src').val() + '/controllers/import/csv_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            hide_modal_main();
            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('All objects imported succesfully!', 'tainacan') ?>', 'success');
            /*try {
                elem = jQuery.parseJSON(result);
                if (elem.error) {
                    showAlertGeneral('<?php _e('Error!', 'tainacan'); ?>', elem.msg, 'error');
                }
            }
            catch (e)
            {
                $('#importForm_csv').show();
                $('#validate_url_csv_container').hide();
                $('#maping_container_csv').html(result);
            }*/
        });
        e.preventDefault();

    });

    function listTableCSV() {

        var src = $('#src').val();
        var collectionId = $('#collection_id').val();

        // $("#btn_identifiers_youtube_update").hide();
        //$("#btn_identifiers_youtube_cancel").hide();
        //$("#loader_videos").hide();
        $.ajax({
            url: src + "/controllers/mapping/mapping_controller.php",
            type: 'POST',
            data: {operation: 'list_mapping_csv',
                collection_id: collectionId
            },
            success: function (data) {
                if (data !== '[]') {
                    var jsonObject = jQuery.parseJSON(data);
                    if (jsonObject && jsonObject != null) {
                        $("#table_csv").html('');
                        $.each(jsonObject.identifier, function (id, object) {
                            if (object.lastUpdate === false || object.lastUpdate === '') {
                                $("#table_csv").append("<tr><td>" + object.name + "</td>" +
                                        "<td><a href='#' onclick=\"edit_mapping_csv('" + object.name + "'," + object.id + "," + collectionId + ")\"><span class='glyphicon glyphicon-pencil'></span></a></td>" +
                                        "<td><a href='#' onclick='delete_mapping(" + object.id + "," + collectionId + " )'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                        "<td><a href='#' onclick=\"do_import_csv('" + object.id + "')\"><span class='glyphicon glyphicon-arrow-down'></span></a></td>");
                            }
                            else {
                                $("#table_csv").append("<tr><td>" + object.name + "</td>" +
                                        "<td><a href='#' onclick=\"edit_mapping_csv('" + object.name + "'," + object.id + "," + collectionId + ")\"><span class='glyphicon glyphicon-pencil'></span></a></td>" +
                                        "<td><a href='#' onclick='delete_mapping(" + object.id + "," + collectionId + ")'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                        "<td><a href='#' ><span style='opacity:0.4' class='glyphicon glyphicon-arrow-down'>&nbsp;<?php _e('Imported in', 'tainacan') ?> " + object.lastUpdate + "</span></a></td>");
                            }
                        });
                        $("#table_csv").show();
                    }
                } // caso o controller retorne false
            }
        });// fim da inclusão de identificador youtube 
    }

    function do_import_csv(mapping_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/import/csv_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                mapping_id: mapping_id,
                operation: 'do_import_csv'}
        }).done(function (result) {
            imported += result.imported;
            update_progressbar(imported, size);
            if (((result.token === "NULL" || result.token === ""))) {
                window.clearInterval(intervalo);
                update_date(mapping_id);
                $("#progress").hide('slow');
                $('#maping_container').hide();
                $('#validate_url_container').show('slow');
                $("#cronometer").hide('slow');
                showHeaderCollection($('#src').val());
                showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('All objects imported succesfully!', 'tainacan') ?>', 'success');
                return;
            } else {
                //window.clearInterval(intervalo);
                //  saving_data(collection_id,all_data);
                do_import(mapping_id, url_base, result.token, imported, size);
            }
        });
    }

    function export_csv_file() {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/export/export_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                operation: 'export_csv_file'
            }
        }).done(function (result) {
            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('All objects imported succesfully!', 'tainacan') ?>', 'success');
        }).fail(function (jqXHR, textStatus, errorThrown) {

        });

    }

    function verify_delimiter() {
        if ($("#socialdb_delimiter_csv").val().trim() == '') {
            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('Please, fill the delimiter correctly!', 'tainacan') ?>', 'error');
            return false;
        } else {
            return true;
        }
    }

</script>
