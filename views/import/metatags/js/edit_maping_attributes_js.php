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
            set_values_metatags();
        });
    });
    
    function cancel_import_metatags() {
        $('#maping_container_metatags').hide();
        $('#url_container_metatags').show('slow');
    }
    
    
    function set_values_metatags() {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                mapping_id: $("#metatags_mapping_id").val(),
                operation: 'get_mapping'}
        }).done(function (result) {
            var jsonObject = jQuery.parseJSON(result);
            if (jsonObject && jsonObject != null) {
                $.each(jsonObject.mapping, function (id, object) {
                    console.log(object);
                    $('[name=mapping_metatags_' + (id + 1) + ']').val(object.tag);
                    $('[name=select_mapping_metatags_' + (id + 1) + ']').append('<option value="'+object.tag+'" >'+object.tag+'</option>')
                    //$('[name=mapping_dublin_core_' + (id + 1) + ']').val(object.tag);
                    $('[name=mapping_socialdb_' + (id + 1) + ']').val(object.socialdb_entity);
                });
            }
        });
    }


    function update_mapping_metatags(mapping_id) {
        var validation = validation_form_metatags(parseInt($('#metatags_counter_oai_dc_edit').val()));
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
                    form: $("#form_import_metags").serialize(),
                    mapping_id: mapping_id,
                    operation: 'updating_mapping_metatags'}
            }).done(function (result) {
                listTableMetaTag();
                $('#maping_container_metatags').hide();
                $('#url_container_metatags').show('slow');
                showAlertGeneral('<?php _e('Success','tainacan'); ?>','<?php _e('Edited successfully.','tainacan'); ?>','success');
            });
        }
    }

    function remove_tag_metatags(id) {
        $('#edit_tag_' + id).hide();
        $('[name=mapping_metatags_' + id).val('');
        $('[name=mapping_socialdb_' + id).val('');
    }

    function validation_form_metatags(counter) {
        var sdb, dc;
        var all_values = [];
        for (var i = 1; i <= counter; i++) {
            dc = $('[name=mapping_metatags_' + i).val();
            sdb = $('[name=mapping_socialdb_' + i).val();
            if (dc !== '' && sdb !== '') {
                if (all_values.indexOf(dc) < 0) {
                    all_values.push(dc);
                } else {
                    return 1;
                }
            }
        }
        if (all_values.length === 0) {
            return 2;
        } else {
            return 0;
        }
    }

</script>