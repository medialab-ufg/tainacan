<script>
    $(function () {
        var src = $('#src').val();
        $('#collection_import_id').val($('#collection_id').val());
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/import/import_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'generate_selects'}
        }).done(function (result) {
            $('.data').html(result);
            set_values();
        });
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/export/export_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                operation: 'generate_selects'}
        }).done(function (result) {
            $('.data_dubin_core').html(result);

        });
    });
    function cancel_import() {
        $('#maping_container').hide();
        $('#validate_url_container').show('slow');
    }
    function editAppendMapping() {
        var count = $('#counter_oai_dc_edit').val();
        $('#counter_oai_dc_edit').val(parseInt(count) + 1);
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/import/import_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                counter: $('#counter_oai_dc_edit').val(),
                operation: 'generate_new_container'}
        }).done(function (result) {
            $('#edit_mapping_attributes_oai_dc').append(result);
        });
    }
    function set_values() {
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
                    $('[name=mapping_dublin_core_' + (id + 1) + ']').val(object.tag);
                    $('[name=mapping_socialdb_' + (id + 1) + ']').val(object.socialdb_entity)
                    if (object.attribute_value) {
                        $('[name=qualifier_' + (id + 1) + ']').val(object.attribute_value)
                        //$('[name='+object.tag+'_'+object.attribute_value+']').val(object.socialdb_entity);
                    }//else{
                    // $('[name='+object.tag+']').val(object.socialdb_entity);
                    //}
                });
                if (jsonObject.import_object === 'true') {
                    $("#edit_import_object_true").attr('checked', true);
                } else {
                    $("#edit_import_object_false").attr('checked', true);
                }
            }

        });
    }


    function update_mapping(url) {
         var validation = validation_form(parseInt($('#counter_oai_dc_edit').val()));
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
                    mapping_id: $("#mapping_id").val(),
                    url_base: url,
                    operation: 'updating_mapping_oaipmh_dc'}
            }).done(function (result) {
                listTableOAIPMHDC();
                $('#maping_container').hide();
                $('#validate_url_container').show('slow');
                showAlertGeneral('<?php _e('Success','tainacan'); ?>','<?php _e('Edited successfully.','tainacan'); ?>','success');
            });
        }
    }

    function remove_tag_oai_dc_edit(id) {
        $('#edit_tag_' + id).hide();
        $('[name=mapping_dublin_core_' + id).val('');
        $('[name=mapping_socialdb_' + id).val('');
        $('[name=qualifier_' + id).val('');
    }

    function validation_form_edit(counter) {
        var qualifier, sdb, dc;
        var all_values = [];
        for (var i = 1; i <= counter; i++) {
            dc = $('[name=mapping_dublin_core_' + i).val();
            sdb = $('[name=mapping_socialdb_' + i).val();
            qualifier = $('[name=qualifier_' + i).val();
            if (dc !== '' && sdb !== '') {
                if (qualifier !== '') {
                    if (all_values.indexOf(dc + '_' + qualifier) < 0) {
                        all_values.push(dc + '_' + qualifier)
                    } else {
                        return 1;
                    }
                } else {
                    if (all_values.indexOf(dc) < 0) {
                        all_values.push(dc);
                    } else {
                        return 1;
                    }
                }
            }
        }
        if (all_values.length === 0) {
            return 2;
        } else {
            return 0;
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