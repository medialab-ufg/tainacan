<script>
    var intervalo;
    $(function () {
        var src = $('#src').val();
        var counter = 1;
        $('#collection_import_id').val($('#collection_id').val());
        $('#counter_oai_dc').val(counter);
        if ($('#validate_oai_pmh_dc_error').val() === 'true') {
            $('#maping_container').hide();
            showAlertGeneral('<?php echo __('Attention') ?>', '<?php echo __('URL invalid or not available!') ?>', 'error');
            $('#validate_url_container').show();
        } else {
            $('#maping_container').show();
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/import/import_controller.php",
                data: {
                    collection_id: $('#collection_id').val(),
                    counter: counter,
                    operation: 'generate_new_container'}
            }).done(function (result) {
                $('#mapping_attributes_oai_dc').html(result);

            });
        }

    });

    function appendMapping() {
        var count = $('#counter_oai_dc').val();
        $('#counter_oai_dc').val(parseInt(count) + 1);
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/import/import_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                counter: $('#counter_oai_dc').val(),
                operation: 'generate_new_container'}
        }).done(function (result) {
            $('#mapping_attributes_oai_dc').append(result);

        });
    }

    function cancel_import() {
        $('#maping_container').hide();
        $('#validate_url_container').show('slow');
    }


    function save_mapping(url) {
        var validation = validation_form(parseInt($('#counter_oai_dc').val()));
        if(validation===1){
             showAlertGeneral('<?php echo __('Attention','tainacan') ?>', '<?php echo __('There is duplicate mappings','tainacan') ?>', 'error');
        }else if(validation===2){
            showAlertGeneral('<?php echo __('Attention','tainacan') ?>', '<?php echo __('Please, insert at least one mapping','tainacan') ?>', 'error');
        }else{
            $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                form: $("#form_import").serialize(),
                url_base: url,
                operation: 'saving_mapping_oaipmh_dc'}
        }).done(function (result) {
            listTableOAIPMHDC();
            $('#maping_container').hide();
            $('#validate_url_container').show('slow');
        });
        }
        
    }
    
    
    function validation_form(counter){
        var qualifier,sdb,dc;
        var all_values = [];
        for(var i = 1;i<=counter;i++){
           dc = $('[name=mapping_dublin_core_'+i).val();
           sdb =   $('[name=mapping_socialdb_'+i).val();
           qualifier = $('[name=qualifier_'+i).val();
           if(dc!==''&&sdb!==''){
               if(qualifier!==''){
                   if(all_values.indexOf(dc+'_'+qualifier)<0){
                     all_values.push(dc+'_'+qualifier)
                   }else{
                     return 1;
                   }
               }else{
                   if(all_values.indexOf(dc)<0){
                     all_values.push(dc);
                   }else{
                     return 1;
                   }
               }
           }        
        }
        if(all_values.length===0){
            return 2;
        }else{
            return 0;
        }
    }

</script>