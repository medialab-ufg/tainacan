<script>
    var intervalo;
    $(function () {
        var src = $('#src').val();
        $('#collection_import_id').val($('#collection_id').val());
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/export/export_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'generate_selects'}
        }).done(function (result) {
            $('.data').html(result);
            set_values(); 
        });
    });
   
    function set_values(){
         $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
            data: {
                collection_id: $('#collection_id').val(), 
                mapping_id: $("#mapping_id").val(),
                operation: 'get_mapping'}
        }).done(function (result) {
              var jsonObject = jQuery.parseJSON(result);
              if (jsonObject && jsonObject != null) {
                        $.each(jsonObject.mapping, function (id, object) {
                            $('[name='+object.socialdb_entity+']').val(object.tag);
                        });
                        if(jsonObject.import_object==='true'){
                            $("#edit_export_object_true").attr('checked',true);
                        }else{
                            $("#edit_export_object_false").attr('checked',true);
                        }
                }        
             
        });
    }
    
    
    function update_mapping_export(){
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
            data: {
                collection_id: $('#collection_id').val(), 
                form: $("#form_import").serialize(),
                mapping_id: $("#mapping_id").val(),
                operation: 'updating_mapping_oaipmh_dc_export'}
        }).done(function (result) {
             listTableOAIPMHDC();  
             $('#maping_container_export').hide();
             $('#export_oaipmh_dc_container').show('slow');
        });
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