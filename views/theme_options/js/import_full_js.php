<script>
    $(function () {
        listTableAIP();
        change_breadcrumbs_title('<?php _e('Import', 'tainacan') ?>');

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
            var json = JSON.parse(result);
            if (json.result == false) {
                showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('The file you are trying to upload is not a .zip file or is not Tainacan standart. Please try again!', 'tainacan') ?>', 'error');
            } else {
                showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('All objects imported succesfully!', 'tainacan') ?>', 'success');
            }
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

    $('#form_export_zip').submit(function (e) {
        show_modal_main();
        $.ajax({
            url: $('#src').val() + '/controllers/theme_options/theme_options_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            hide_modal_main();
            var jsonObject = jQuery.parseJSON(result);
            if (jsonObject.result) {
                listTableAIP();
            }
            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', jsonObject.error, 'info');
        });
        e.preventDefault();

    });

    function listTableAIP() {
        var src = $('#src').val();

        $.ajax({
            url: src + "/controllers/theme_options/theme_options_controller.php",
            type: 'POST',
            data: {operation: 'list_aip_files'},
            success: function (data) {
                console.log(data);
                if (data && data !== '[]' && data !== '') {
                    var jsonObject = jQuery.parseJSON(data);
                    if (jsonObject && jsonObject != null) {
                        $("#table_aip").html('');
                        $.each(jsonObject, function (index, file) {
                            $("#table_aip").append("<tr><td>" + file + "</td>" +
                                    "<td><a href='#' onclick=\"do_import_aip_zip('" + file + "')\"><span class='glyphicon glyphicon-arrow-down'></span></a>&nbsp;&nbsp;" +
                                    "<a href='#' onclick=\"delete_aip_zip('" + file + "')\"><span class='glyphicon glyphicon-trash'></span></a></td>");
                        });
                        $("#table_aip").show();
                    } else {
                        $("#table_aip").hide();
                    }
                } else {
                    $("#table_aip").html('');
                    $("#table_aip").append("<tr><td colspan='2'>" + '<?php __('No files found.', 'tainacan'); ?>' + "</td></tr>");

                    $("#table_aip").show();
                }

            }
        });// fim
    }

    function do_import_aip_zip(file) {
        swal({
            title: '<?php _e('Attention', 'tainacan') ?>',
            text: '<?php _e('Are you sure about this procedure?', 'tainacan') ?>',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                //show_modal_main();
                var src = $('#src').val();

                $.ajax({
                    url: src + "/controllers/theme_options/theme_options_controller.php",
                    type: 'POST',
                    data: {operation: 'import_dspace_aip', file: file},
                    success: function (data) {
                        var json = JSON.parse(data);
                        if (json.result) {
                           start_loader_aip();
                            //listTableAIP();
                        } else {
                             showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('Zip not compatible!', 'tainacan') ?>', 'error');
                        }
                    }
                });// fim
                
            }
        });
    }

    function delete_aip_zip(file) {
        swal({
            title: '<?php _e('Attention', 'tainacan') ?>',
            text: '<?php _e('Are you sure to delete this file?', 'tainacan') ?>',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                show_modal_main();
                var src = $('#src').val();

                $.ajax({
                    url: src + "/controllers/theme_options/theme_options_controller.php",
                    type: 'POST',
                    data: {operation: 'delete_aip_file', file: file},
                    success: function (data) {
                        hide_modal_main();
                        if (data) {
                            showAlertGeneral('<?php _e('Success', 'tainacan') ?>', '<?php _e('File deleted.', 'tainacan') ?>', 'success');
                            listTableAIP();
                        } else {
                            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('Some error ocurred, please click in REFRESH and try again!', 'tainacan') ?>', 'error');
                        }
                    }
                });// fim
            }
        });
    }

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
                            } else {
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
//********************** IMPORTACAO OAIPMH - REPOSITORY ******************************/  
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
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);

                });
            }
        });
    }
    /* @name: listTableOAIPMHDC()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/
    function edit_mapping_oaipmh(url, mapping_id, collection_id) {
        var url_base = url;
        if (url_base !== '') {
            $('#validate_url_container').hide('slow');
            $('#loader_validacao').show();
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
                data: {url: url_base,
                    collection_id: collection_id,
                    operation: 'edit_mapping_oaipmh_repository',
                    mapping_id: mapping_id
                }
            }).done(function (result) {
                console.log('success');
                $('#loader_validacao').hide('slow');
                $('#maping_container_repository').html(result);
                $('#maping_container_repository').show();
                $('#url_base_oai').attr("disable");

            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log('erro');
                $('#loader_validacao').hide();
                showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('Server not found or not available', 'tainacan') ?>', '<?php _e('error') ?>');
            });
        } else {
            showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('URL base is empty', 'tainacan') ?>', '<?php _e('error') ?>');
        }
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
                collection_id: $('#collection_id').val(),
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
    function import_list_set(url_base, sets) {
        if (url_base !== '') {
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/import/import_controller.php",
                data: {
                    url: url_base,
                    sets: sets,
                    operation: 'import_list_set_repository'
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

    function do_import(mapping_id, url_base, token, imported, size, sets) {
        var first;
        if (isNaN(imported)) {
            import_list_set(url_base, sets);
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
                collection_id: '<?php echo get_option('collection_root_id') ?>',
                url: url_base,
                mapping_id: mapping_id,
                token: token,
                first: first,
                sets: sets,
                operation: 'do_import'}
        }).done(function (result) {
            imported += result.imported;
            update_progressbar(imported, size);
            if (((result.token === "NULL" || result.token === ""))) {
                window.clearInterval(intervalo);
                update_date(mapping_id);
                $("#progress").hide('slow');
                $('#maping_container_repository').hide();
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
                collection_id: $('#collection_id').val(),
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

    // AIP
    function show_form_by_type() {
        if ($('#select_aip_type').val() == 'dspace') {
            $('#select_file_import_dspace').show();
            $('#select_file_import_tainacan').hide();
        } else if ($('#select_aip_type').val() == 'tainacan') {
            $('#select_file_import_dspace').hide();
            $('#select_file_import_tainacan').show();
        }
    }

    function refresh_list_aip() {
        $("#table_aip").html('Loading...');
        listTableAIP();
    }

    function upload_aip_zip() {
        console.log('entrou aqui');
        show_modal_main();
        var src = $('#src').val();

        $.ajax({
            url: src + "/controllers/theme_options/theme_options_controller.php",
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            hide_modal_main();
            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('All objects imported succesfully!', 'tainacan') ?>', 'success');

        });
        e.preventDefault();
    }

    /*******************  Mostra o contador de item,colecoes e comunidades criadas ***********************************************************/
    function start_loader_aip() {
        $('#modalImportAIP').modal('show');
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/theme_options/theme_options_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                operation: 'get_info_import_aip'
            }
        }).done(function (result) {
            var json = JSON.parse(result);
            if (json && (json.total_community === 0 || json.total_community === '0')) {
                $('#total-community').html('<?php _t('Calculating...', true) ?>');
                $('#total-collection').html('<?php _t('Calculating...', true) ?>');
                $('#total-item').html('<?php _t('Calculating...', true) ?>');
            } else {
                $('#total-community').html(json.total_community);
                $('#found-community').html(json.found_community);
                $('#total-collection').html(json.total_collection);
                $('#found-collection').html(json.found_collection);
                $('#total-item').html(json.total_item);
                $('#found-item').html(json.founf_item);

                $('#progressbar').val(json.percent);
            }
            callback_loader_aip(json);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('All objects imported succesfully!', 'tainacan') ?>', 'success');
        });
    }

    function callback_loader_aip(json) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/theme_options/theme_options_controller.php",
            data: {
                total_community: json.total_community,
                total_collection: json.total_collection,
                total_item: json.total_item,
                collection_id: $('#collection_id').val(),
                operation: 'get_info_import_aip'
            }
        }).done(function (result) {
            var json = JSON.parse(result);
            if (!json.close) {
                if (json && (json.total_community === 0 || json.total_community === '0')) {
                    $('#total-community').html('<?php _t('Calculating...', true) ?>');
                    $('#total-collection').html('<?php _t('Calculating...', true) ?>');
                    $('#total-item').html('<?php _t('Calculating...', true) ?>');
                    $('#found-community').html('<?php _t('Calculating...', true) ?>');
                    $('#found-collection').html('<?php _t('Calculating...', true) ?>');
                    $('#found-item').html('<?php _t('Calculating...', true) ?>');
                } else {
                    $('#total-community').html(json.total_community);
                    $('#found-community').html(json.found_community);
                    $('#total-collection').html(json.total_collection);
                    $('#found-collection').html(json.found_collection);
                    $('#total-item').html(json.total_item);
                    $('#found-item').html(json.found_item);
                    $('#progressbar').val(json.percent);
                }
                callback_loader_aip(json);
            } else {
                $('#modalImportAIP').modal('hide');
                showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('All objects imported succesfully!', 'tainacan') ?>', 'success');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('All objects imported succesfully!', 'tainacan') ?>', 'success');
        });
    }

</script>
