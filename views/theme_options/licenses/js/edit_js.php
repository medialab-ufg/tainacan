<script>
    var src = $('#src').val();
    $(function () {
        var src = $('#src').val();
        listStandartLicenses();
        change_breadcrumbs_title('<?php _e('Repository Licenses','tainacan') ?>');

        $('#formAddLicense').submit(function (e) {
            if($('#add_license_name').val().trim()===''){
                showAlertGeneral('<?php _e('Attention','tainacan') ?>', '<?php _e('Name is empty','tainacan') ?>', 'info');
                return false;
            }
            e.preventDefault();
            show_modal_main();
            $.ajax({
                url: $("#src").val() + '/controllers/theme_options/theme_options_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                hide_modal_main();
                elem = jQuery.parseJSON(result);
                showAlertGeneral(elem.title, elem.msg, elem.type);
                listStandartLicenses();

                $("#addLicenseOperation").val('add_repository_license');
                $("#editLicenseId").val('');
                $("#add_license_name").val('');
                $("#add_license_url").val('');
                $("#add_license_description").val('');
            });
        });
    });

    function listStandartLicenses() {
        var src = $('#src').val();

        $.ajax({
            url: src + '/controllers/theme_options/theme_options_controller.php', type: 'POST',
            data: {operation: 'listStandartLicenses'},
            success: function (data) {
                if (data) {
                    var jsonObject = jQuery.parseJSON(data);
                    if (jsonObject && jsonObject != null) {
                        $("#list_licenses_content").html('');
                        $.each(jsonObject.licenses, function (id, object) {
                            $("#list_licenses_content").append("<tr><td>" + object.nome + "</td>" +
                                   // "<td><input type='radio' name='standartLicense' id='radio" + object.id + "' value=" + object.id + " onclick='changeStandartLicense(this," + object.id + ");'/></td>" +
                                    "<td><a href='javascript:void(0);' style='opacity:0.4'><span class='glyphicon glyphicon-trash'></span></a> " +
                                    "<a href='javascript:void(0);' style='opacity:0.4'><span class='glyphicon glyphicon-edit'></span></a></td></tr>");
                        });
                        listCustomLicenses();
                        $("#list_licenses_content").show();
                    }
                } // caso o controller retorne false
            }
        });// fim da inclusão
    }

    function listCustomLicenses() {
        var src = $('#src').val();

        $.ajax({
            url: src + '/controllers/theme_options/theme_options_controller.php', type: 'POST',
            data: {operation: 'listCustomLicenses'},
            success: function (data) {
                if (data) {
                    var jsonObject = jQuery.parseJSON(data);
                    if (jsonObject && jsonObject != null) {
                        if (jsonObject.licenses) {
                            $.each(jsonObject.licenses, function (id, object) {
                                $("#list_licenses_content").append("<tr><td>" + object.nome + "</td>" +
                                  //  "<td><input type='radio' name='standartLicense' id='radio" + object.id + "' value=" + object.id + " onclick='changeStandartLicense(this," + object.id + ");'/></td>" +
                                    "<td><a onclick='deleteCustomLicense(" + object.id + ")' href='javascript:void(0);'><span class='glyphicon glyphicon-trash'></span></a> " +
                                    "<a onclick='editCustomLicense(" + object.id + ")' href='javascript:void(0);'><span class='glyphicon glyphicon-edit'></span></a></td> </tr>");
                            });
                        }
                        $('#radio' + jsonObject.pattern).attr("checked", "checked");
                    }
                } // caso o controller retorne false
            }
        });// fim da inclusão
    }

    function changeStandartLicense(form, id) {
        $.ajax({
            url: src + '/controllers/theme_options/theme_options_controller.php',
            type: 'POST',
            data: {operation: 'change_pattern_license', license_id: id},
            success: function (data) {
                if (data) {
                    elem = jQuery.parseJSON(data);
                    showAlertGeneral(elem.title, elem.msg, elem.type);
                } // caso o controller retorne false
            }
        });// fim da inclusão
    }

    function editCustomLicense(id) {
        $('.edit-license').click().text('<?php _e("Edit license", "tainacan") ?>');
        $.ajax({
            url: src + '/controllers/theme_options/theme_options_controller.php',
            type: 'POST',
            data: {operation: 'get_license_to_edit', license_id: id},
            success: function (data) {
                if (data) {
                    var jsonObject = jQuery.parseJSON(data);

                    $("#addLicenseOperation").val('edit_repository_license');
                    $("#editLicenseId").val(jsonObject.id);
                    $("#add_license_name").val(jsonObject.nome);
                    $("#add_license_url").val(jsonObject.url);
                    $("#add_license_description").val(jsonObject.description);
                } // caso o controller retorne false
            }
        });// fim da inclusão
    }

    function deleteCustomLicense(id) {
        swal({
            title: '<?php _e('Attention!', 'tainacan'); ?>',
            text: '<?php _e('Are you sure?', 'tainacan'); ?>',
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
                    url: $('#src').val() + '/controllers/theme_options/theme_options_controller.php',
                    data: {
                        operation: 'delete_custom_license',
                        license_id: id
                    }
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    showAlertGeneral(elem.title, elem.msg, elem.type);
                    listStandartLicenses();
                });
            }
        });
    }
</script>
