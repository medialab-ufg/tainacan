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
            $('#tmp_div_all_metas').html(result);

        });
    });
    function cancel_import_csv() {
        $('#maping_container_csv').hide();
        $('#validate_url_csv_container').show('slow');
    }

    //function save_csv_delimiter(){
    $('#form_import_csv_delimit').submit(function (e) {
        //e.preventDefault();
        show_modal_main();
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
            hide_modal_main();
            if(result.trim()==='false'){
                listTableCSV();
                $('#maping_container_csv').hide();
                $('#validate_url_csv_container').show('slow');
            }else{
                $('#importForm_csv').hide();
                $('#add_mapping_csv').html(result);
            }
            
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
    
    function getHeaderNames(){
        if($('#create_metadata_column_name').is(':checked')){
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/import/csv_controller.php",
                data: {
                    mapping_id: $('#socialdb_csv_mapping_id').val(),
                    delimiter: $("#socialdb_delimiter_csv").val(),
                    operation: 'getHeaderCSV'}
            }).done(function (result) {
                var json = JSON.parse(result);
                if(json.length>0){
                    $('#select-title-box').show();
                    $('#map_title_metadata').html('');
                    $('#map_title_metadata').append('<option value=""><?php _e('Select','tainacan') ?></option>');
                    $.each(json,function(index,value){
                        if(value && value !== '')
                            $('#map_title_metadata').append('<option value="'+index+'">'+value+'</option>');
                    });
                }else{
                    $('#select-title-box').hide();
                    $('#map_title_metadata').html('');
                    showAlertGeneral('<?php _e('Attention!','tainacan') ?>','<?php _e('CSV not formatted or delimiter not matching !','tainacan') ?>','error');
                }
            });
        }else{
            $('#select-title-box').hide();
        }
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