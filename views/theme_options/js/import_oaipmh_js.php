<script>
    $(function () {
        listTableOAIPMHDC();
    });
    /* @name: listTableOAIPMHDC()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais salvos no banco
     * 
     * @author: EDUARDO
     **/

    function listTableOAIPMHDC() {
        var src = $('#src').val();
        var collectionId = $('#collection_id').val();

        $.ajax({
            url: src + "/controllers/mapping/mapping_controller.php", type: 'POST',
            data: { operation: 'list_mapping_oaipmh_dc', collection_id: '<?php echo get_option('collection_root_id') ?>' },
            success: function (data) {
                $("#table_oaipmh_dc").html('');
                if (data !== '[]') {
                    var jsonObject = jQuery.parseJSON(data);
                    if (jsonObject && jsonObject != null && jsonObject.identifier) {
                        $.each(jsonObject.identifier, function (id, object) {
                            if (object.size) {
                                if (object.lastUpdate === false || object.lastUpdate === '') {
                                    $("#table_oaipmh_dc").append("<tr><td>" + object.name + "</td><td>--</td>" +
                                        "<td><a href='#' onclick='delete_mapping(" + object.id + "," + collectionId + " )'><span class='glyphicon glyphicon-trash'></span></a> &nbsp; " +
                                        "<a href='#' onclick=\"edit_mapping_oaipmh('" + object.name + "'," + object.id + "," + collectionId + ")\"><span class='glyphicon glyphicon-edit'></span></a> &nbsp; " +
                                        "<a href='#' onclick=\"do_import(" + object.id + ",'" + object.name + "','" + object.token + "','begin','" + object.size + "','" + object.sets + "')\"><span class='glyphicon glyphicon-arrow-down'></span></a></td></tr>");
                                } else {
                                    $("#table_oaipmh_dc").append("<tr><td>" + object.name + "</td>" +
                                        "<td><a href='#' onclick=\"is_harvesting(" + object.id + ",'" + object.is_harvesting + "')\">" + object.is_harvesting + "</a></td>" +
                                        "<td><a href='#' onclick='delete_mapping(" + object.id + "," + collectionId + ")'><span class='glyphicon glyphicon-trash'></span></a> &nbsp; " +
                                        "<td><a href='#' onclick=\"edit_mapping_oaipmh('" + object.name + "'," + object.id + "," + collectionId + ")\"><span class='glyphicon glyphicon-edit'></span></a> &nbsp; " +
                                        "<td><a href='#' ><span style='opacity:0.4' class='glyphicon glyphicon-arrow-down'>&nbsp;<?php _e('Imported in', 'tainacan') ?> " + object.lastUpdate + "</span></a></td></tr>");
                                }
                            }
                        });
                        $("#table_oaipmh_dc").show();
                    }
                } // caso o controller retorne false
            }
        });// fim da inclusão de identificador youtube 
    }
    
   function validate_url_repository() {
        var url_base = $('#url_base_oai').val();
        if (url_base !== '') {
            $('#validate_url_container').hide();
            $('#loader_validacao').show();
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/import/import_controller.php",
                data: {
                    url: $('#url_base_oai').val(),
                    sets: $('#sets_import_oaipmh').val(),
                    collection_id: $('#collection_id').val(),
                    operation: 'validate_url_repository'
                }
            }).done(function (result) {
                $('#loader_validacao').hide('slow');
                $('#maping_container_repository').html(result);

            }).fail(function (jqXHR, textStatus, errorThrown) {
                $('#loader_validacao').hide();
                $('#validate_url_container').show('slow');
                showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('Server not found or not available', 'tainacan') ?>', '<?php _e('error') ?>');
            });
        } else {
            showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('URL base is empty', 'tainacan') ?>', '<?php _e('error') ?>');
        }
    }
    
    
    /* @name: is_harvesting()
     * @description: permite o harvesting de um  mapeamento
     * 
     * @author: EDUARDO
     **/
    function is_harvesting(mapping_id, is_harvesting) {
        if (is_harvesting === 'disabled') {
            is_harvesting = 'enabled';
        } else {
            is_harvesting = 'disabled';
        }
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/mapping/mapping_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                operation: 'is_harvesting',
                mapping_id: mapping_id,
                is_harvesting: is_harvesting
            }
        }).done(function (result) {
            listTableOAIPMHDC();
            showAlertGeneral('<?php _e('Success', 'tainacan') ?>', '<?php _e('Mapping', 'tainacan') ?> ' + is_harvesting + ' successfuly', '<?php _e('success') ?>');

        }).fail(function (jqXHR, textStatus, errorThrown) {
            $('#loader_validacao').hide();
            showAlertGeneral('<?php _e('Atention', 'tainacan') ?>', '<?php _e('Server not found or not available', 'tainacan') ?>', '<?php _e('error') ?>');
        });

    }
</script>
