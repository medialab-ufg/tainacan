<script>
    var intervalo;
    $(function () {
        $('#socialdb_csv_mapping_collection_id').val($('#collection_id').val());
        var src = $('#src').val();
        $('#collection_import_id').val($('#collection_id').val());
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/import/import_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'generate_selects'}
        }).done(function (result) {
            $('.data').html(result);

        });
    });
    function cancel_import_csv() {
        $('#maping_container_csv').hide();
        $('#validate_url_csv_container').show('slow');
    }

    //function save_csv_delimiter(){
    $('#form_import_csv_delimit').submit(function (e) {
        //e.preventDefault();
        $.ajax({
            url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
            type: "POST",
            data: new FormData(this),
            processData: false,
            contentType: false
                    /*data: {
                     collection_id: $('#collection_id').val(), 
                     form: $("#form_import_csv_delimit").serialize(),
                     operation: 'saving_delimiter_header_csv'}*/
        }).done(function (result) {
            $('#importForm_csv').hide();
            $('#add_mapping_csv').html(result);
        });
        e.preventDefault();
    });

    function save_csv_mapping() {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                form: $("#form_import_csv_mapping").serialize(),
                operation: 'saving_mapping_csv'}
        }).done(function (result) {
            listTableCSV();
            $('#maping_container_csv').hide();
            $('#validate_url_csv_container').show('slow');
        });
    }

    function hide_zip_input() {
        $("#zip_csv_file").hide();
    }

    function show_zip_input() {
        $("#zip_csv_file").show();
    }

    // function saving_data(collection_id,data){
    //   $.ajax({
    //      dataType: "json",
    //       type: "POST",
    //       url: $('#src').val() + "/controllers/import/import_controller.php",
    //       data: {
    //           collection_id: collection_id,
    //         all_data:data,
    //          form: $("#form_import").serialize(),
    //          operation: 'saving_data'}
    //  }).done(function (result) {
    //      console.log(result);
    //  });    
    // }

</script>