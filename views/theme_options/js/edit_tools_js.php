<script>
    var integrity_test = "";
    $(function () {
        var src = $('#src').val();
        autocomplete_collection();

        $('#submit_form_integrity_test').submit(function (e) {
            $('#show_generate_pdf').hide();
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
                if (elem && elem != null) {
                    integrity_test = elem;
                    $("#dataTable_console_content").html('');
                    $.each(elem, function (id, object) {
                        var color_result = (object.result == 'OK' ? '006400' : 'FF0000');
                        $("#dataTable_console_content").append("<tr><td>" + object.id + "</td>" +
                                "<td>" + object.title + "</td>" +
                                "<td>" + object.md5_inicial + "</td>" +
                                "<td>" + object.md5_atual + "</td>" +
                                "<td style='color: #" + color_result + ";'>" + object.result + "</td></tr>");
                    });
                    $('#show_console_test').show();
                    $('#show_generate_pdf').show();
                    //$('#dataTable_console').DataTable().api().ajax.reload();
                }
            }).error(function (result) {
                hide_modal_main();
                $('#show_console_test').hide();
                $('#show_generate_pdf').hide();
                showAlertGeneral('<?php _e('Error', 'tainacan') ?>', '<?php _e('Please, try again!', 'tainacan') ?>', 'error');
            });
        });

        $('#submit_form_edit_tools').submit(function (e) {
            e.preventDefault();
            getProgress();
            tempo_tools();
            $("#documents_inserted").text(0 + '/' + $("#total_items").val());
            $("#categories_inserted").text(0 + '/' + $("#total_categories").val());
            $('#modalPopulate').modal('show');
            $.ajax({
                url: $("#src").val() + '/controllers/theme_options/theme_options_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                window.clearInterval(intervalPopulate);
                window.clearInterval(intervalo_tools);
                showAlertGeneral('<?php _e('Success', 'tainacan') ?>', '<?php _e('Collection populated successfully', 'tainacan') ?>', 'success');
            }).error(function (result) {
                elem = jQuery.parseJSON(result);
                window.clearInterval(intervalPopulate);
                window.clearInterval(intervalo_tools);
                showAlertGeneral('<?php _e('Success', 'tainacan') ?>', '<?php _e('Collection populated successfully', 'tainacan') ?>', 'success');
            });
        });
    });

    function autoTablePDF() {
        var columns = [
            {title: "ID", dataKey: "id"},
            {title: "Title", dataKey: "title"},
            {title: "MD5 Initial", dataKey: "md5_initial"},
            {title: "MD5 Final", dataKey: "md5_final"},
            {title: "Result", dataKey: "resultado"}

        ];
        var rows = [];
        $.each(integrity_test, function (id, object) {
            rows.push({id: object.id, title: object.title, md5_initial: object.md5_inicial, md5_final: object.md5_atual, resultado: object.result});
        });

        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();

        if (dd < 10) {
            dd = '0' + dd
        }

        if (mm < 10) {
            mm = '0' + mm
        }

        today = dd + '/' + mm + '/' + yyyy;

        // Only pt supported (not mm or in)
        var doc = new jsPDF('l', 'pt');
        doc.autoTable(columns, rows, {
            theme: 'striped',
            styles: {},
            columnStyles: {},
            margin: {top: 60},
            beforePageContent: function (data) {
                doc.text("Integrity Test - "+today, 40, 30);
            }
        });
        doc.save('integiry_test.pdf');
    }

    function autocomplete_collection() {
        $("#collection").autocomplete({
            source: $('#src').val() + '/controllers/collection/collection_controller.php?operation=get_collections_json',
            messages: {
                noResults: '',
                results: function () {
                }
            },
            minLength: 2,
            select: function (event, ui) {
                event.preventDefault();
                $("#collection").val(ui.item.label);
                $("#socialdb_collection_id").val(ui.item.value);
            }
        });
    }

    function somaCategorias() {
        if ($.isNumeric($("#subcategories_per_level").val()) && $.isNumeric($("#number_levels").val())) {
            var total = Math.pow($("#subcategories_per_level").val(), $("#number_levels").val());
            $("#total_categories").val(total);
        } else {
            $("#total_categories").val('');
        }
    }

    function somaItens() {
        if ($.isNumeric($("#total_categories").val()) && $.isNumeric($("#items_category").val())) {
            var total = $("#total_categories").val() * $("#items_category").val();
            $("#total_items").val(total);
        } else {
            $("#total_items").val('');
        }
    }

    function somaClassificacao() {
        if ($.isNumeric($("#total_items").val()) && $.isNumeric($("#classification").val())) {
            var total = $("#total_items").val() * $("#classification").val();
            $("#total_classifications").val(total);
        } else {
            $("#total_classifications").val('');
        }
    }

    function getProgress() {
        intervalPopulate = window.setInterval(function () {
            $.ajax({
                url: $("#src").val() + '/controllers/theme_options/theme_options_controller.php',
                type: 'POST',
                data: {
                    collection_id: $("#socialdb_collection_id").val(),
                    operation: 'getProgress'}
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                $("#documents_inserted").text(elem.documents.length + '/' + $("#total_items").val());
                $("#categories_inserted").text(elem.categories.length + '/' + $("#total_categories").val());
            });
        }, 5000);
    }

    function tempo_tools() {
        var s = 1;
        var m = 0;
        var h = 0;
        intervalo_tools = window.setInterval(function () {
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

</script>
