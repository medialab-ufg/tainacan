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

        });
    });
    function cancel_export(){
         $('#maping_container_export').hide();
         $('#export_oaipmh_dc_container').show('slow');
    }
   
    
    function save_mapping_export(url){
       var return_value = validate_export_form($("#metadatas_export").val().split(','));
       if(return_value===1){
           showAlertGeneral('<?php echo __('Attention') ?>', '<?php echo __('Please, insert at least one mapping') ?>', 'error');
       }else{
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
                data: {
                    collection_id: $('#collection_id').val(), 
                    form: $("#form_import").serialize(),
                    url_base: url,
                    operation: 'saving_mapping_oaipmh_dc_export'}
            }).done(function (result) {
                 listTableOAIPMHDC();  
                 $('#maping_container_export').hide();
                 $('#export_oaipmh_dc_container').show('slow');
            });
        }
    }
    
    function validate_export_form(array_sdb_entities){
        var existence = [];
        var value;
        for(var i = 0; i<array_sdb_entities.length;i++){
            value =  $('[name='+array_sdb_entities[i]).val();
            if(value!==''){
                existence.push(value);
            }
        }
        if(existence.length>0){
            return 0;
        }else{
            return 1;
        }
        
    }
    
</script>