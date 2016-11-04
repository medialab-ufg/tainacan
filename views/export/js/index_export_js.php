<script>
    var _col_id = $("#collection_id").val();
    $(function () {

        change_breadcrumbs_title('<?php _e('Export','tainacan') ?>');
        var src = $('#src').val();

        $('#validate_url_container').show('slow');
        listTableOAIPMHDC();
        $('#collection_import_id').val(_col_id);
        $('#collection_import_csv_id').val(_col_id);
        $('#collection_id_export_csv').val(_col_id);
        $('#collection_id_zip').val(_col_id);

        $('#click_oaipmhtab').click(function (e) {
            e.preventDefault();
            $(this).tab('show')
        });
        $('#click_zip').click(function (e) {
            e.preventDefault();
            $(this).tab('show')
        });
        $('#click_csvtab').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
            listTableCSV();
        });

        $('#form_default').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: $("#src").val() + '/controllers/mapping/mapping_controller.php',
                type: 'POST',
                data: {operation: 'form_default_mapping', mapping_id: $('input[name=socialdb_default_mapping]:checked').val(), collection_id: $("#collection_id").val()}
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                console.log(elem);
                if (elem.result === '1') {
                    listTableOAIPMHDC();
                    $("#oai_repository").html(elem.html);
                    showAlertGeneral(elem.title, elem.msg, elem.type);
                } else {
                    showAlertGeneral(elem.title, elem.msg, elem.type);
                }
            });

        });

    });

    function show_mapping_export() {
        $('#export_oaipmh_dc_container').hide('slow');
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/export/export_controller.php",
            data: {
                collection_id: _col_id,
                operation: 'create_new_mapping'
            }
        }).done(function (result) {
            $('#maping_container_export').html(result).show();

        }).fail(function (jqXHR, textStatus, errorThrown) {
            showAlertGeneral('<?php _e('Atention') ?>', '<?php _e('Server not found or not available') ?>', '<?php _e('error') ?>');
        });
    }

    function get_icon(icon) {
        var path = '<?php echo get_template_directory_uri() ?>';
        return path + "/libraries/images/icons/icon-" + icon;
    }
    // var edit_icon = get_icon("edit.png");
    // var delete_icon = get_icon("delete_collection_redirect.png");

    /* @name: listTableOAIPMHDC()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/
    function listTableOAIPMHDC() {
        var flag_has_active = false;
        var src = $('#src').val();
        var collectionId = $('#collection_id').val();
        // $("#btn_identifiers_youtube_update").hide();
        //$("#btn_identifiers_youtube_cancel").hide();
        //$("#loader_videos").hide();
        $.ajax({
            url: src + "/controllers/mapping/mapping_controller.php",
            type: 'POST',
            data: {operation: 'list_mapping_oaipmh_dc',
                collection_id: collectionId
            },
            success: function (data) {
                if (data !== '[]') {
                    var jsonObject = jQuery.parseJSON(data);
                    $("#table_export_oaipmh_dc").html('');
                    if (jsonObject && jsonObject != null && jsonObject.identifier) {
                        $("#show_mapping_export_oaipmhdc").show();
                        $.each(jsonObject.identifier, function (id, object) {
                            $("#table_export_oaipmh_dc").append("<tr>");

                            $("#table_export_oaipmh_dc").append("<td>" + object.name + "</td>");

                            if (object.id == jsonObject.active_mapping) {
                                flag_has_active = true;
                                $("#table_export_oaipmh_dc").append("<td><input  type='radio' checked='checked' name='socialdb_default_mapping' value='" + object.id + "'></td></tr>");
                            } else {
                                $("#table_export_oaipmh_dc").append("<td><input onchange='show_button_active_mapping()' type='radio' name='socialdb_default_mapping' value='" + object.id + "'></td></tr>");
                            }

                            if (object.size === false || object.size === '') {
                                $("#table_export_oaipmh_dc").append( "<td><a href='#' onclick='delete_mapping(" + object.id + "," + collectionId + " )'><span class='glyphicon glyphicon-trash'></span></a> "+
                                        "<a href='#' onclick=\"edit_mapping_oaipmh_export(" + object.id + "," + collectionId + ")\"><span class='glyphicon glyphicon-edit'></span></a></td>");
                            }
                            else {
                                $("#table_export_oaipmh_dc").append("<td><a href='#' ><?php _e('(Imported mapping)', 'tainacan') ?>&nbsp;<span style='opacity:0.4' class='glyphicon glyphicon-edit'></span></a></td>" +
                                        "<td><a href='#' onclick='delete_mapping(" + object.id + "," + collectionId + ")'><span class='glyphicon glyphicon-trash'></span></a></td></tr>" );
                            }

                        });

                        if (flag_has_active) {
                            $("#show_mapping_export_oaipmhdc").show();
                        } else {
                            $("#show_mapping_export_oaipmhdc").hide();
                        }
                        $("#table_export_oaipmh_dc").show();
                    } else {
                        $("#show_mapping_export_oaipmhdc").hide();
                    }
                } else { // caso o controller retorne false
                    $("#show_mapping_export_oaipmhdc").hide();
                }
            }
        });// fim da inclusão de identificador youtube 
    }
    //
    function show_button_active_mapping() {
        $("#show_mapping_export_oaipmhdc").show();
    }

    /* @name: listTableOAIPMHDC()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/
    function delete_mapping(mapping_id, collection_id) {

        swal({
            title: '<?php _e('Attention', 'tainacan') ?>',
            text: '<?php _e('Are you sure to delete this mapping?', 'tainacan') ?>',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
                    data: {
                        operation: 'delete_mapping',
                        mapping_id: mapping_id,
                        collection_id: collection_id
                    }
                }).done(function (result) {
                    elem_first = jQuery.parseJSON(result);
                    listTableOAIPMHDC();
                    listTableCSV();
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);

                });
            }
        });
    }

    function cancel_export() {
        $('#maping_container_export').hide();
        $('#export_oaipmh_dc_container').show('slow');
    }

    /* @name: listTableOAIPMHDC()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/
    function edit_mapping_oaipmh_export(mapping_id, collection_id) {
        $('#export_oaipmh_dc_container').hide('slow');
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
            data: {
                collection_id: collection_id,
                operation: 'edit_mapping_oaipmh_export',
                mapping_id: mapping_id
            }
        }).done(function (result) {
            console.log('success');
            $('#maping_container_export').html(result).show();

        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log('erro');
            $('#loader_validacao').hide();
            showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('Server not found or not available', 'tainacan') ?>', '<?php _e('error') ?>');
        });
    }

    /* @name: listTableOAIPMHDC()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/
    function update_date(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
            data: {
                mapping_id: id,
                collection_id: _col_id,
                operation: 'update_date'
            }
        }).done(function (result) {
            listTableOAIPMHDC();
        }).fail(function (jqXHR, textStatus, errorThrown) {

        });

    }

    /* @name: listTableOAIPMHDC()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/
    function import_list_set(url_base) {
        if (url_base !== '') {
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/import/import_controller.php",
                data: {
                    url: url_base,
                    collection_id: _col_id,
                    operation: 'import_list_set'
                }
            }).done(function (result) {

            }).fail(function (jqXHR, textStatus, errorThrown) {
                showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('Server not found or not available', 'tainacan') ?>', '<?php _e('error') ?>');
                return;
            });
        } else {
            showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('URL base is empty', 'tainacan') ?>', '<?php _e('error') ?>');
        }
    }
    /* @name: do_import()
     * @description: Funcao que chama via ajax a acao que salva os dados mapeados no vanco
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/

    function do_import(mapping_id, url_base, token, imported, size) {
        var first;
        if (isNaN(imported)) {
            import_list_set(url_base);
            tempo();
            $("#validate_url_container").hide('slow');
            $("#cronometer").show('slow');
            $("#progress").show('slow');
            imported = 0;
            first = true;
        } else {
            first = false;
        }
        $.ajax({
            dataType: "json",
            type: "GET",
            url: $('#src').val() + "/controllers/import/import_controller.php",
            data: {
                objects_found: size,
                collection_id: _col_id,
                url: url_base,
                mapping_id: mapping_id,
                token: token,
                first: first,
                operation: 'do_import'}
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

    /* @name: do_import()
     * @description: Funcao que chama via ajax a acao que salva os dados mapeados no vanco
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/

    function update_repository(mapping_id, url_base, token, imported, size) {
        var first;
        if (isNaN(imported)) {
            tempo();
            $("#validate_url_container").hide('slow');
            $("#cronometer").show('slow');
            $("#progress").show('slow');
            imported = 0;
            first = true;
        } else {
            first = false;
        }
        $.ajax({
            dataType: "json",
            type: "GET",
            url: $('#src').val() + "/controllers/import/import_controller.php",
            data: {
                objects_found: size,
                collection_id: _col_id,
                url: url_base,
                mapping_id: mapping_id,
                token: token,
                first: first,
                operation: 'do_import'}
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
                update_repository(mapping_id, url_base, result.token, imported, size);
            }
        });
    }

    /* @name: listTableOAIPMHDC()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/
    function show_message_size() {
        showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('Importing the object becomes the importation very slowly!', 'tainacan') ?>', 'info');
    }
    /* @name: listTableOAIPMHDC()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/
    function tempo() {
        var s = 1;
        var m = 0;
        var h = 0;
        intervalo = window.setInterval(function () {
            if (s == 60) {
                m++;
                s = 0;
            }
            if (m == 60) {
                h++;
                s = 0;
                m = 0;
            }
            if (h < 10)
                document.getElementById("hora").innerHTML = "0" + h + "h";
            else
                document.getElementById("hora").innerHTML = h + "h";
            if (s < 10)
                document.getElementById("segundo").innerHTML = "0" + s + "s";
            else
                document.getElementById("segundo").innerHTML = s + "s";
            if (m < 10)
                document.getElementById("minuto").innerHTML = "0" + m + "m";
            else
                document.getElementById("minuto").innerHTML = m + "m";
            s++;
        }, 1000);
    }




    function update_progressbar(imported, total) {
        var percent = (imported / total) * 100;
        $("#progressbar").val(percent);
    }

    $('#formCsv').submit(function (e) {
        $.ajax({
            url: $('#src').val() + '/controllers/import/csv_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            try {
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
            }
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
                                        "<td><a href='#' onclick=\"edit_mapping_csv('" + object.name + "'," + object.id + "," + collectionId + ")\"><span class='glyphicon glyphicon-edit'></span></a></td>" +
                                        "<td><a href='#' onclick='delete_mapping(" + object.id + "," + collectionId + " )'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                        "<td><a href='#' onclick=\"do_import_csv('" + object.id + "')\"><span class='glyphicon glyphicon-arrow-down'></span></a></td>");
                            }
                            else {
                                $("#table_csv").append("<tr><td>" + object.name + "</td>" +
                                        "<td><a href='#' onclick=\"edit_mapping_csv('" + object.name + "'," + object.id + "," + collectionId + ")\"><span class='glyphicon glyphicon-edit'></span></a></td>" +
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
                collection_id: _col_id,
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
                collection_id: _col_id,
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
