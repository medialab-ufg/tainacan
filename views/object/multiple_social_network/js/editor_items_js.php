<script>
    var checkbox_values = [];
    var myDropzone;
    var noCleaning = false;
    $(function () {
        $('#modalImportMain').modal('hide');
        var src = $('#src').val();
        var collection_id = $('#collection_id').val();
        // #1 - breadcrumbs para localizacao da pagina
        $("#tainacan-breadcrumbs").show();
        $("#tainacan-breadcrumbs .current-config").text('<?php _e('Add new item - Edit item(s) metadata','tainacan') ?>');
        // 2# - Dropzone dos arquivos
        $('.attachment-thumbnail').addClass('img-responsive');
        var src = $('#src').val();
         // 3# - se o usuario desejar abrir todos os metadados
        $('.expand-all-item').toggle(function () {
            setMenuContainerHeight();

            $(this).find("div.action-text").text('<?php _e('Expand all', 'tainacan') ?>');
            $('#multiple_accordion .ui-accordion-content').fadeOut();
            $('.prepend-filter-label').switchClass('glyphicon-triangle-bottom', 'glyphicon-triangle-right');
            $(this).find('span').switchClass('glyphicon-triangle-bottom', 'glyphicon-triangle-right');
            $('.cloud_label').click();
        }, function () {
            $('#multiple_accordion .ui-accordion-content').fadeIn();
            $('.prepend-filter-label').switchClass('glyphicon-triangle-right', 'glyphicon-triangle-bottom');
            $(this).find('span').switchClass('glyphicon-triangle-right', 'glyphicon-triangle-bottom');
            $('.cloud_label').click();
            $(this).find("div.action-text").text('<?php _e('Collapse all', 'tainacan') ?>');
        });
        $('.expand-all-item').trigger('click');
         // 4# - inicializa os metadados de termo
        list_properties_term_insert_objects();
        var properties_autocomplete = multiple_get_val($("#multiple_properties_data_id").val());
        multiple_autocomplete_property_data(properties_autocomplete);  
        show_license_item('multiple');// lista as licencas de um item
        // envia o formulario para o controllador
        $('#sumbit_multiple_items').submit(function (e) {
            e.preventDefault();
            var selected_items = [];
            $.each($("input:checkbox[name='selected_items']:checked"), function () {
                selected_items.push($(this).val());
            });
            var is_empty = false;
            $.each($("input:checkbox[name='selected_items']"), function () {
                if($('#title_' + $(this).val()).val().trim()==''){
                    is_empty = true;
                }
            });
            if(!is_empty){
                $('#selected_items_id').val(selected_items.join(','));
                $('#modalImportMain').modal('show');
                var formData = new FormData(this);
                
                $.ajax({
                    url: src + '/controllers/object/object_multiple_controller.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false
                }).done(function (result) {
                    set_containers_class(collection_id);
                    $('#modalImportMain').modal('hide');
                    elem_first = jQuery.parseJSON(result);
                    if (elem_first.type && elem_first.type == 'success') {
                         $('#form').hide();
                        $("#tainacan-breadcrumbs").hide();
                        $('#main_part').show();
                        $('#collection_post').show();
                        $('#configuration').hide();
                        //$("#dynatree").dynatree("getTree").reload();
                        //showList(src);
                        wpquery_clean();
                        $('#create_button').show();
                        $('#menu_object').show();
                        showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                    } else {
                        showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                    }
                });
            }else{
                showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('There are empty title, please fill before submit!', 'tainacan') ?>', 'info');
            }
            e.preventDefault();
        });
        $('.nav-tabs').tab();
        $('.dropdown-toggle').dropdown();
    });
     //votacoes do item
    //BEGIN: funcao para mostrar votacoes para um item
    function list_ranking_create(id) {
        $('#list_ranking_items').show();
        $('#create_list_ranking').html('');
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'create_list_ranking_object', object_id: id}
        }).done(function (result) {
            $('#create_list_ranking').html(result);
            //verifico se tem ordenacao para os metadados
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/collection/collection_controller.php",
                data: { operation: 'get_ordenation_properties',collection_id:$('#collection_id').val() }
            }).done(function(result) {
                var json = $.parseJSON(result);
                if(json&&json.ordenation&&json.ordenation!==''){
                    reorder_properties_multiple_item(json.ordenation.split(','));
                }
            });
            //inicializo o accordeon
            $("#accordion_socialnetwork").accordion("destroy");  
            $("#accordion_socialnetwork").accordion({
                collapsible: true,
                header: "h2",
                animate: 200,
                heightStyle: "content"
            });
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab(); 
        });
    }
    //END
    //BEGIN: funcao para mostrar votacoes para varios itens
    function list_ranking_multiple(ids) {
        $('#list_ranking_items').show();
        $('#create_list_ranking').html('');
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'multiple_list_ranking_object',ids:ids, object_id: 'multiple'}
        }).done(function (result) {
            $('#create_list_ranking').html(result);
            //verifico se tem ordenacao para os metadados
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/collection/collection_controller.php",
                data: { operation: 'get_ordenation_properties',collection_id:$('#collection_id').val() }
            }).done(function(result) {
                var json = $.parseJSON(result);
                if(json&&json.ordenation&&json.ordenation!==''){
                    reorder_properties_multiple_item(json.ordenation.split(','));
                }
            });
            //inicializo o accordeon
            $("#accordion_socialnetwork").accordion("destroy");  
            $("#accordion_socialnetwork").accordion({
                collapsible: true,
                header: "h2",
                animate: 200,
                heightStyle: "content"
            });
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab(); 
        });
    }
    //END
    
    //votacoes do item
    
    //BEGIN: funcao para mostrar as licencas para um item
    function show_license_item(id) {
        $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {operation: 'show_collection_licenses', object_id: id, collection_id: $("#collection_id").val()}
        }).done(function (result) {
            $('#show_form_licenses').html(result);
            
            //inicaliza as licensas se existir
            setLicenses();
        });
    }
    
    //anexos do item
    function init_dropzone_attachments(id){
        //dropzone para adicionar anexos
        destroy_dropzone();
        $('#dropzone_new').html('');
        $('#dropzone_new')
                .append('<div class="dz-message" data-dz-message><span style="text-align: center;vertical-align: middle;line-height: 90px;"><h2><span class="glyphicon glyphicon-upload"></span><b><?php _e('Drop Files','tainacan')  ?></b> <?php _e('to upload','tainacan')  ?></h2><h4>(<?php _e('or click','tainacan')  ?>)</h4>')
                ;
        $('#attachments_item_upload').show();
         myDropzone = new Dropzone("div#dropzone_new", {
                accept: function(file, done) {
                      if (file.type === ".exe") {
                          done("Error! Files of this type are not accepted");
                      }
                      else { done(); }
                },
                init: function () {
                    thisDropzone = this;
                    this.on("removedfile", function (file) {
                        if(!noCleaning){
                            //    if (!file.serverId) { return; } // The file hasn't been uploaded
                            $.get($('#src').val() + '/controllers/object/object_controller.php?operation=delete_file&object_id=' + id + '&file_name=' + file.name, function (data) {
                                if (data.trim() === 'false') {
                                    showAlertGeneral('<?php _e("Atention!", 'tainacan') ?>', '<?php _e("An error ocurred, File already removed or corrupted!", 'tainacan') ?>', 'error');
                                } else {
                                    showAlertGeneral('<?php _e("Success", 'tainacan') ?>', '<?php _e("File removed!", 'tainacan') ?>', 'success');
                                }
                            }); // Send the file id along
                        }
                    });
                    $.get($('#src').val() + '/controllers/object/object_controller.php?operation=list_files&object_id=' + id, function (data) {
                        try {
                            //var jsonObject = JSON.parse(data);
                            $.each(data, function (key, value) {
                                if (value.name !== undefined && value.name !== 0) {
                                    var mockFile = {name: value.name, size: value.size};
                                    thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                                }
                            });
                        }
                        catch (e)
                        {
                            // handle error 
                        }
                    });
                },                
                url: $('#src').val() + '/controllers/object/object_controller.php?operation=save_file&object_id=' + id,
                addRemoveLinks: true

            });
           
    }
    function destroy_dropzone(){
        $('#attachments_item_upload').hide();
        if(myDropzone){
           noCleaning = true;
           myDropzone.destroy();
           noCleaning = false;
        }
    }
    //funcao que ordena o accordeon
    function reorder_properties_multiple_item(array_ids){
        var $ul = $("#accordion_socialnetwork"),
        $items = $("#accordion_socialnetwork").children();
        $rankings = $("#create_list_ranking").children();
       for (var i = 0; i< array_ids.length; i++) {
           // index is zero-based to you have to remove one from the values in your array
             for(var j = 0; j<$items.length;j++){
                 if($($items.get(j)).attr('id')&&$($items.get(j)).attr('id')===array_ids[i]){
                     $( $items.get(j) ).appendTo( $ul);
                 }
                 if(array_ids[i]==='socialdb_license_id'&&$($items.get(j)).attr('id')&&$($items.get(j)).attr('id')==='list_licenses_items'){
                      $( $items.get(j) ).appendTo( $ul);
                 }
             }
            for(var j = 0; j<$items.length;j++){
                 if($($rankings.get(j)).attr('id')===array_ids[i]){
                     for(var k = 0; k<$items.length;k++){
                        if($($items.get(k)).attr('id')===$($rankings.get(j)).attr('id')){
                             $( $items.get(k) ).remove();
                        }
                     }
                     $( $rankings.get(j) ).appendTo( $ul);
                 }
             }
      }
      $('[data-toggle="tooltip"]').tooltip();
    }
    /******************************** Manipulação das cores ao selecionar itens setando se eh anexo o u nao******************************************/
    function focusItem(id) {
        if ($('#buttonBackItems').is(':visible')) { // eh pq esta selecionando anexos
            var selected_id = getOnlyItemID();
            if (selected_id != id && $('#attachment_option_' + id).is(':checked')) {
                $('#attachment_option_' + id).removeAttr('checked');
                $('#attachment_option_' + id).trigger('change');
                clean_item_selected_colour(id);
            } else if (selected_id != id) {
                $('#attachment_option_' + id).attr('checked', 'checked');
                $('#attachment_option_' + id).trigger('change');
                set_attachment_selected_colour(id);
            }
        }
        else {//se estiver selecionando itens
            if ($("#parent_" + id).val() !== '') {//se estiver setado como anexo
                var title = $("#title_" + $("#parent_" + id).val()).val();
                toastr.info('<?php _e('Attachment of ', 'tainacan') ?> ' + title, '<?php _e('Attention', 'tainacan') ?>', set_toastr_class());
            } else if ($('#item_option_' + id).is(':checked')) {
                $('#item_option_' + id).removeAttr('checked');
                $('#item_option_' + id).trigger('change');
                clean_item_selected_colour(id);
            } else {
                $('#item_option_' + id).attr('checked', 'checked');
                $('#item_option_' + id).trigger('change');
                set_item_selected_colour(id);
            }

        }
    }
    /***************************** Seleciona todos os items *********************************************/
    function selectAll() {
        $.each($("input:checkbox[name='selected_items']"), function () {
            if ($("#parent_" + $(this).val()).val() === '') {
                set_item_selected_colour($(this).val());
                $(this).attr('checked', 'checked');
            }
        });
        selectedItems();
    }
    /***************************** DESmarca todos os items *********************************************/
    function unselectAll() {
        $.each($("input:checkbox[name='selected_items']"), function () {
            $(this).removeAttr('checked');
            if ($("#parent_" + $(this).val()).val() === '') { // se nao for anexo
                clean_item_selected_colour($(this).val());
            }
            selectedItems();
        });
    }
    /******************************** Esconde os itens selecionados *******************************************/
    function removeSelected() {
        var deletedIds = [];
        var size = $("input:checkbox[name='selected_items']:checked").length;
        swal({
            title: '<?php _e('Attention', 'tainacan') ?>',
            text: '<?php _e('Are you sure to remove ', 'tainacan') ?>' + size + ' <?php _e('item(s) selected?', 'tainacan') ?>',
            type: "info",
            showCancelButton: true,
            confirmButtonClass: 'btn-primary',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                var counter = 0;
                var allItems = $("#items_id").val().split(',').filter(Boolean);
                var images = $("#items_images").val().split(',').filter(Boolean);
                var videos = $("#items_video").val().split(',').filter(Boolean);
                var audios = $("#items_audio").val().split(',').filter(Boolean);
                var pdfs = $("#items_pdf").val().split(',').filter(Boolean);
                var others = $("#items_other").val().split(',').filter(Boolean);
                $.each($("input:checkbox[name='selected_items']:checked"), function () {
                    //retiro dos checados
                    $(this).removeAttr('checked');
                    //escondo seu wrapper
                    $("#wrapper_" + $(this).val()).hide();
                    deletedIds.push($(this).val());
                    //retiro o item do array de items geral
                    if (allItems.length > 0 && allItems.indexOf($(this).val()) >= 0) {
                        allItems.splice(allItems.indexOf($(this).val()), 1);
                        $("#items_id").val(allItems.join(','));
                        if (allItems.length == 0) {
                            $("#selectOptions").attr('disabled', 'disabled');// retiro a opcao de retirar todos pois nao ha itens
                            $("#no_item_uploaded").show();
                        }
                        selectedItems();
                    }
                    //retiro do array e de seu container de tipo
                    if (images.length > 0 && images.indexOf($(this).val()) >= 0) {
                        images.splice(images.indexOf($(this).val()), 1);
                        $("#items_images").val(images.join(','));
                        if (images.length == 0) {
                            $("#container_images").hide();
                        }
                    } else if (videos.length > 0 && videos.indexOf($(this).val()) >= 0) {
                        videos.splice(videos.indexOf($(this).val()), 1);
                        $("#items_video").val(videos.join(','));
                        if (videos.length == 0) {
                            $("#container_videos").hide();
                        }
                    } else if (audios.length > 0 && audios.indexOf($(this).val()) >= 0) {
                        audios.splice(audios.indexOf($(this).val()), 1);
                        $("#items_audio").val(audios.join(','));
                        if (audios.length == 0) {
                            $("#container_audios").hide();
                        }
                    } else if (pdfs.length > 0 && pdfs.indexOf($(this).val()) >= 0) {
                        pdfs.splice(pdfs.indexOf($(this).val()), 1);
                        $("#items_pdf").val(pdfs.join(','));
                        if (pdfs.length == 0) {
                            $("#container_pdfs").hide();
                        }
                    } else if (others.length > 0 && others.indexOf($(this).val()) >= 0) {
                        others.splice(others.indexOf($(this).val()), 1);
                        $("#items_other").val(others.join(','));
                        if (others.length == 0) {
                            $("#container_others").hide();
                        }
                    }
                    counter++;
                });
                delete_items_socialnetwork(deletedIds);
                if (counter > 0) {
                    toastr.success(counter + '<?php _e(' items/item removed successfully!', 'tainacan') ?>', '<?php _e('Success', 'tainacan') ?>', set_toastr_class());
                }
            }
        });


    }

    function delete_items_socialnetwork(ids) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {
                operation: 'delete_items_socialnetwork',
                items_id: ids,
                collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem_first = jQuery.parseJSON(result);
            showList($('#src').val());
        });
    }
    /***************************** MARCA/DESMARCA items de um determinado tipo *********************************************/
    function clear_selected(array, id) {
        if (array.length > 0) {
            $.each($("input:checkbox[name='selected_items']"), function () {
                if (array.indexOf($(this).val()) >= 0 && $(id).is(':checked')) {
                    $(this).attr('checked', 'checked');
                    set_item_selected_colour($(this).val());
                    selectedItems();
                } else if (array.indexOf($(this).val()) >= 0 && !$(id).is(':checked')) {
                    $(this).removeAttr('checked');
                    clean_item_selected_colour($(this).val());
                    selectedItems();
                }
            });
        }
    }

    function selectImages() {
        var images = $("#items_images").val().split(',').filter(Boolean);
        clear_selected(images, '#selectAllImages');
    }
    function selectVideo() {
        var videos = $("#items_video").val().split(',').filter(Boolean);
        clear_selected(videos, '#selectAllVideo');
    }
    function selectAudio() {
        var audios = $("#items_audio").val().split(',').filter(Boolean);
        clear_selected(audios, '#selectAllAudio');
    }
    function selectPdf() {
        var pdfs = $("#items_pdf").val().split(',').filter(Boolean);
        clear_selected(pdfs, '#selectAllPdf');
    }
    function selectOther() {
        var others = $("#items_other").val().split(',').filter(Boolean);
        clear_selected(others, '#selectAllOther');
    }
    /******************************* AO SELECIONAR OS ITEMS VIA CHECKBOX *********************/
    function selectedItems() {
        var selected_items = [];
        $.each($("input:checkbox[name='selected_items']:checked"), function () {
            selected_items.push($(this).val());
        });
        if (selected_items.length == 1) {// quando selecionado um item seus valores vao para o form a esquerda
            item_id = selected_items[0];
            //$("#buttonSelectedAttachments").show();// mostra o botao de anexos
            list_ranking_create(item_id);// lista o ranking de um item
            if($("input[name='object_license']")){
                $("input[name='object_license']").removeAttr('checked');
                if($("#license_"+item_id).val()!==''&&$('#radio'+$("#license_"+item_id).val())){
                   $('#radio'+$("#license_"+item_id).val()).attr("checked", "checked");
                }
            }
            $("#form_properties_items").show(); // mostra o formulario para edicao
            $("#no_properties_items").hide(); // mostra a mensagem que solicita a selecao de itens
            $("#labels_items_selected").show(); // mostra a div q tem as quantidades de itens selecionados
            $("#number_of_items_selected").text(selected_items.length);// total de items selecionados
            //buscando os valores para o formulario
            $("#multiple_object_name").val($("#title_" + item_id).val());// seta o titulo do item
            $("#multiple_object_description").val($("#description_" + item_id).val());// descricao do item
            $("#multiple_object_source").val($("#source_" + item_id).val());// descricao do item
            var tags = $("#tags_" + item_id).val().split(',');//quebro as tags em um array
            tags = tags.filter(function (v) {
                return v !== ''
            });
            if (tags.length > 0) {//
                $("#multiple_object_tags").val(tags.join(','));
            }
            //BUSCANDO VALORES das propriedades de objeto
            var objectProperties = $("#multiple_properties_object_id").val().split(',').filter(Boolean);
            for (var i = 0; i < objectProperties.length; i++) {
                $("#multiple_property_value_" + objectProperties[i] + "_<?php echo $object_id ?>_add option:selected").each(function () {
                    $(this).remove(); //or whatever else
                }); // limpo os campos primeiramente
                var value_property = $("#socialdb_property_" + objectProperties[i] + "_" + item_id).val().split('||').filter(Boolean); // TORNA O VALOR DO hidden em array
                for (var j = 0; j < value_property.length; j++) {
                    $("#multiple_property_value_" + objectProperties[i] + "_<?php echo $object_id ?>_add").append("<option class='selected' value='" + value_property[j] + "' selected='selected' >" + value_property[j].split('_')[1] + "</option>");
                }
            }
            //BUSCANDO VALORES das **propriedades de data** para cada o item selecionado
            var dataProperties = $("#multiple_properties_data_id").val().split(',').filter(function (v) {
                return v !== ''
            });
            console.log($("#multiple_properties_data_id").val());
            for (var i = 0; i < dataProperties.length; i++) {
                $('.multiple_socialdb_property_' + dataProperties[i]).each(function(index,value){
                    $(this).val('');
                });
                var value_property = $("#socialdb_property_" + dataProperties[i] + "_" + item_id).val().split('||').filter(Boolean); // TORNA O VALOR DO hidden em array
                $('.multiple_socialdb_property_' + dataProperties[i]).each(function(index,value){
                    if(value_property[index]){
                        $(this).val(value_property[index]);
                    }
                });
                //$("#multiple_property_value_" + objectProperties[i] + "_<?php echo $object_id ?>_add").append("<option class='selected' value='" + value_property[j] + "' selected='selected' >" + value_property[j].split('_')[1] + "</option>");
                // $('#multiple_socialdb_property_' + dataProperties[i]).val($("#socialdb_property_" + dataProperties[i] + "_" + item_id).val());
            }
            //BUSCANDO VALORES das **propriedades de termos** para o item selecionado
            //checkbox
            var checkboxes = $("#multiple_properties_terms_checkbox").val().split(',').filter(function (v) {
                return v !== ''
            });
            for (var i = 0; i < checkboxes.length; i++) {
                $.each($("input:checkbox[name='multiple_socialdb_propertyterm_" + checkboxes[i] + "']:checked"), function () {
                    $(this).removeAttr('checked');
                });
                var categories = $("#socialdb_property_" + checkboxes[i] + "_" + item_id).val().split(',').filter(function (v) {
                    return v !== ''
                });
                $.each($("input:checkbox[name='multiple_socialdb_propertyterm_" + checkboxes[i] + "']"), function () {
                    if (categories.length > 0 && categories.indexOf($(this).val()) >= 0) {
                        $(this).attr('checked', 'checked');
                    }
                });
            }
            //multipleSelect
            var multipleSelects = $("#multiple_properties_terms_multipleselect").val().split(',').filter(function (v) {
                return v !== ''
            });
            for (var i = 0; i < multipleSelects.length; i++) {
                $("select[name='multiple_socialdb_propertyterm_" + multipleSelects[i] + "']").val([]);
                var categories = $("#socialdb_property_" + multipleSelects[i] + "_" + item_id).val().split(',').filter(function (v) {
                    return v !== ''
                });
                $.each($("select[name='multiple_socialdb_propertyterm_" + multipleSelects[i] + "'] option"), function () {
                    if (categories.length > 0 && categories.indexOf($(this).val()) >= 0) {
                        $(this).attr('selected', 'selected');
                    }
                });
            }
            //dynatree checkbox
            var multiple_properties_terms_treecheckbox = $("#multiple_properties_terms_treecheckbox").val().split(',').filter(function (v) {
                return v !== ''
            });
            for (var i = 0; i < multiple_properties_terms_treecheckbox.length; i++) {
                if( $("#socialdb_property_" + multiple_properties_terms_treecheckbox[i] + "_" + item_id)){
                    var categories = $("#socialdb_property_" + multiple_properties_terms_treecheckbox[i] + "_" + item_id).val().split(',').filter(function (v) {
                        return v !== ''
                    });
                }
                $("#multiple_field_property_term_" + multiple_properties_terms_treecheckbox[i]).dynatree("getRoot").visit(function (node) {
                    node.select(false);
                });
                $("#multiple_field_property_term_" + multiple_properties_terms_treecheckbox[i]).dynatree("getRoot").visit(function (node) {
                    if (categories.length > 0 && categories.indexOf(node.data.key) >= 0) {
                        node.select(true);
                    }
                });
            }
            //radio
            var radios = $("#multiple_properties_terms_radio").val().split(',').filter(function (v) {
                return v !== ''
            });
            for (var i = 0; i < radios.length; i++) {
                $.each($("input[name='multiple_socialdb_propertyterm_" + radios[i] + "']"), function () {
                    $(this).removeAttr('checked');
                });
                var category = $("#socialdb_property_" + radios[i] + "_" + item_id).val();
                $.each($("input[name='multiple_socialdb_propertyterm_" + radios[i] + "']"), function () {
                    if (category && category == $(this).val()) {
                        $(this).attr('checked', 'checked');
                    }
                });
            }
            //select
            var select = $("#multiple_properties_terms_selectbox").val().split(',').filter(function (v) {
                return v !== ''
            });
            for (var i = 0; i < select.length; i++) {
                $.each($("select[name='multiple_socialdb_propertyterm_" + select[i] + "'] option"), function () {
                    $(this).removeAttr('selected');
                });
                var category = $("#socialdb_property_" + select[i] + "_" + item_id).val();
                $.each($("select[name='multiple_socialdb_propertyterm_" + select[i] + "'] option"), function () {
                    if (category && category == $(this).val()) {
                        $(this).attr('selected', 'selected');
                    }
                });
            }
            //dynatree radio
            var multiple_properties_terms_tree = $("#multiple_properties_terms_tree").val().split(',').filter(function (v) {
                return v !== ''
            });
            for (var i = 0; i < multiple_properties_terms_tree.length; i++) {
                var categories = $("#socialdb_property_" + multiple_properties_terms_tree[i] + "_" + item_id).val().split(',').filter(function (v) {
                    return v !== ''
                });
                $("#multiple_field_property_term_" + multiple_properties_terms_tree[i]).dynatree("getRoot").visit(function (node) {
                    node.select(false);
                });
                $("#multiple_field_property_term_" + multiple_properties_terms_tree[i]).dynatree("getRoot").visit(function (node) {
                    if (categories.length > 0 && categories.indexOf(node.data.key) >= 0) {
                        node.select(true);
                    }
                });
            }
            //mostra os anexos
            init_dropzone_attachments(item_id);
        } else if (selected_items.length > 1) {
            $("#buttonSelectedAttachments").hide();// mostra o botao de anexos
            
            list_ranking_multiple(selected_items.join(','));// lista o ranking de um item
            if($("input[name='object_license']")&&$("input[name='object_license']:checked")){
                $("input[name='object_license']:checked").removeAttr('checked');
            }
            
            $("#form_properties_items").show(); // mostra o formulario para edicao
            $("#no_properties_items").hide(); // mostra a mensagem que solicita a selecao de itens
            $("#labels_items_selected").show(); // mostra a div q tem as quantidades de itens selecionados
            $("#number_of_items_selected").text(selected_items.length);// total de items selecionados
            //limpo o formulario
            $("#multiple_object_name").val(''); //limpo o campo do tiulo
            $("#multiple_object_name").attr("placeholder", "<?php _e('Replace ', 'tainacan') ?>" + selected_items.length + " <?php _e(' titles', 'tainacan') ?>");
            $("#multiple_object_description").attr("value", "");//limpo o campo de descricao
            clear_fields_multiple();
        } else {
            $("#buttonSelectedAttachments").hide();// mostra o botao de anexos
            $("#no_properties_items").show(); // mostra a mensagem que solicita a selecao de itens
            $("#form_properties_items").hide(); // esconde o formulario para edicao
            //limpo o formulario
            $("#multiple_object_name").val('');
            $("#multiple_object_description").attr("value", "");
            $("#multiple_object_tags").val('');
            //limpo VALORES das propriedades de objeto
            var objectProperties = $("#multiple_properties_object_id").val().split(',').filter(Boolean);
            for (var i = 0; i < objectProperties.length; i++) {
                $("#multiple_property_value_" + objectProperties[i] + "_<?php echo $object_id ?>_add option:selected").each(function () {
                    $(this).remove(); //or whatever else
                });

            }
            clear_fields_multiple();
        }
    }
    
    /**
    *  funcao que limpa todos os campos do menu esquerdo
    */
    function clear_fields_multiple(){
          //pego as tags comuns aos itens selecionados
          $("#multiple_object_tags").val('');
//            var all_tags = [];
//            var tags = [];
//            $.each($("input:checkbox[name='selected_items']:checked"), function () {
//                tags = $("#tags_" + $(this).val()).val().split(',');
//                tags = tags.filter(function (v) {
//                    return v !== ''
//                });
//                for (var i = 0; i < tags.length; i++) {
//                    all_tags.push(tags[i]);
//                }
//            });
//            if (all_tags.length > 0) {// se existir tags em qualquer um dos itens
//                all_tags = remove_duplicates_safe(all_tags);
//                $("#multiple_object_tags").val(all_tags.join(','));
//            }
            //BUSCANDO VALORES das **propriedades de objeto** para cada item selecionado
            var objectProperties = $("#multiple_properties_object_id").val().split(',').filter(function (v) {
                return v !== ''
            });
            for (var i = 0; i < objectProperties.length; i++) {
                var allValues = [];
                $("#multiple_property_value_" + objectProperties[i] + "_<?php echo $object_id ?>_add option:selected").each(function () {
                    setPropertyObject($(this).val(), objectProperties[i], true);
                    $(this).remove(); //or whatever else
                }); // limpo os campos primeiramente
                $.each($("input:checkbox[name='selected_items']:checked"), function () {  // varro todos os objetos     
                    var value_property = $("#socialdb_property_" + objectProperties[i] + "_" + $(this).val()).val().split('||').filter(Boolean); // TORNA O VALOR DO hidden desta propriedade em array
                    for (var j = 0; j < value_property.length; j++) {//loop que varre todos os valores de cada objeto para uma unica propriedade
                        allValues.push(value_property[j]);//coloco os valores para esta propriedade em array unico
                    }
                });
                if (allValues.length > 0) {// se existir objects
                    allValues = remove_duplicates_safe(allValues);// retiro os duplicados
                    for (var j = 0; j < allValues.length; j++) {
                        $("#multiple_property_value_" + objectProperties[i] + "_<?php echo $object_id ?>_add").append("<option class='selected' value='" + allValues[j] + "' selected='selected' >" + allValues[j].split('_')[1] + "</option>");
                    }
                }
            }
            //BUSCANDO VALORES das **propriedades de data** para cada item selecionado
            var dataProperties = $("#multiple_properties_data_id").val().split(',').filter(function (v) {
                return v !== ''
            });
            for (var i = 0; i < dataProperties.length; i++) {
               $('.multiple_socialdb_property_' + dataProperties[i]).each(function(index,value){
                     $(this).val('');
                });
            }
            //BUSCANDO VALORES das **propriedades de termos** para o item selecionado
            //checkbox
            var checkboxes = $("#multiple_properties_terms_checkbox").val().split(',').filter(function (v) {
                return v !== ''
            });
            for (var i = 0; i < checkboxes.length; i++) {
                $.each($("input:checkbox[name='multiple_socialdb_propertyterm_" + checkboxes[i] + "']:checked"), function () {
                    $(this).removeAttr('checked');
                });
//                $.each($("input:checkbox[name='selected_items']:checked"), function () {  // varro todos os objetos     
//                    var categories = $("#socialdb_property_" + checkboxes[i] + "_" + $(this).val()).val().split(',').filter(function (v) {
//                        return v !== ''
//                    });
//                    $.each($("input:checkbox[name='multiple_socialdb_propertyterm_" + checkboxes[i] + "']"), function () {
//                        if (categories.length > 0 && categories.indexOf($(this).val()) >= 0) {
//                            $(this).attr('checked', 'checked');
//                        }
//                    });
//                });
//                setCategoriesCheckbox(checkboxes[i], 'not');
            }
            //multipleSelect
            var multipleSelects = $("#multiple_properties_terms_multipleselect").val().split(',').filter(function (v) {
                return v !== ''
            });
            for (var i = 0; i < multipleSelects.length; i++) {
                $("select[name='multiple_socialdb_propertyterm_" + multipleSelects[i] + "']").val([]);
//                $.each($("input:checkbox[name='selected_items']:checked"), function () {  // varro todos os objetos     
//                    var categories = $("#socialdb_property_" + multipleSelects[i] + "_" + $(this).val()).val().split(',').filter(function (v) {
//                        return v !== ''
//                    });
//                    $.each($("select[name='multiple_socialdb_propertyterm_" + multipleSelects[i] + "'] option"), function () {
//                        if (categories.length > 0 && categories.indexOf($(this).val()) >= 0) {
//                            $(this).attr('selected', 'selected');
//                        }
//                    });
//                });
//                setCategoriesSelectMultiple(multipleSelects[i], "select[name='multiple_socialdb_propertyterm_" + multipleSelects[i] + "']");
            }
            //treecheckbox
            var multiple_properties_terms_treecheckbox = $("#multiple_properties_terms_treecheckbox").val().split(',').filter(function (v) {
                return v !== ''
            });
            for (var i = 0; i < multiple_properties_terms_treecheckbox.length; i++) {
                $("#multiple_field_property_term_" + multiple_properties_terms_treecheckbox[i]).dynatree("getTree").reload();
                //$("#multiple_field_property_term_"+multiple_properties_terms_treecheckbox[i]).dynatree("getRoot").visit(function(node){
                //   node.select(false);
                // });
//                $.each($("input:checkbox[name='selected_items']:checked"), function () {  // varro todos os objetos     
//                    var categories = $("#socialdb_property_" + multiple_properties_terms_treecheckbox[i] + "_" + $(this).val()).val().split(',').filter(function (v) {
//                        return v !== ''
//                    });
//                    $("#multiple_field_property_term_" + multiple_properties_terms_treecheckbox[i]).dynatree("getRoot").visit(function (node) {
//                        if (categories.length > 0 && categories.indexOf(node.data.key) >= 0) {
//                            node.select(true);
//                        }
//                    });
//                });
            }
            //radio
            var radios = $("#multiple_properties_terms_radio").val().split(',').filter(function (v) {
                return v !== ''
            });
            for (var i = 0; i < radios.length; i++) {
                $.each($("input[name='multiple_socialdb_propertyterm_" + radios[i] + "']"), function () {
                    $(this).removeAttr('checked');
                });
            }
            //selectbox
            var select = $("#multiple_properties_terms_selectbox").val().split(',').filter(function (v) {
                return v !== ''
            });
            for (var i = 0; i < select.length; i++) {
                $.each($("select[name='multiple_socialdb_propertyterm_" + select[i] + "'] option"), function () {
                    $(this).removeAttr('selected');
                });
            }
            //dynatree radio
            var multiple_properties_terms_tree = $("#multiple_properties_terms_tree").val().split(',').filter(function (v) {
                return v !== ''
            });
            for (var i = 0; i < multiple_properties_terms_tree.length; i++) {
                $("#multiple_field_property_term_" + multiple_properties_terms_tree[i]).dynatree("getTree").reload();
//                if($("#socialdb_property_" + multiple_properties_terms_tree[i] + "_" + item_id)){
//                    var categories = $("#socialdb_property_" + multiple_properties_terms_tree[i] + "_" + item_id).val().split(',').filter(function (v) {
//                        return v !== ''
//                    });
//                }
//                $("#multiple_field_property_term_" + multiple_properties_terms_tree[i]).dynatree("getRoot").visit(function (node) {
//                    node.select(false);
//                });
            }
            //esconde anexos
            destroy_dropzone();
    }
    // FIM: AO SELECIONAR ITEMS
    // coloca o titulo para todos os items selecionados
    function setTitle(title) {
        console.log($(title).val());
        $.each($("input:checkbox[name='selected_items']:checked"), function () {
            $("#title_" + $(this).val()).val($(title).val());
        });

    }
    // coloca a descricao para todos os items selecionados
    function setDescription(description) {
        var counter = 0;
        if ($(description).val() != '') {
            $.each($("input:checkbox[name='selected_items']:checked"), function () {
                counter++;
                $("#description_" + $(this).val()).val($(description).val());
            });
            //$("#multiple_object_description").attr("value", "");
            //$("#multiple_object_name").val(''); 
            toastr.success(counter + '<?php _e(' items/item updated successfully!', 'tainacan') ?>', '<?php _e('Success', 'tainacan') ?>', set_toastr_class());
        }
    }
    //coloca as tags para todos os itens
    function setTags(tags) {
        var counter = 0;
        if ($(tags).val() != '') {
            var tags_names = $(tags).val().split(',');
            if (tags_names.length > 0) {
                $.each($("input:checkbox[name='selected_items']:checked"), function () {
                    counter++;
                    $("#tags_" + $(this).val()).val(tags_names.join(','));
                });
            }
            toastr.success(counter + '<?php _e(' items/item updated successfully!', 'tainacan') ?>', '<?php _e('Success', 'tainacan') ?>', set_toastr_class());
        }
    }
    // coloca a fonte para todos os items selecionados
    function setSource(source) {
        var counter = 0;
        console.log(source);
        if ($(source).val() != '') {
            $.each($("input:checkbox[name='selected_items']:checked"), function () {
                counter++;
                $("#source_" + $(this).val()).val($(source).val());
            });
            //$("#multiple_object_description").attr("value", "");
            //$("#multiple_object_name").val(''); 
            toastr.success(counter + '<?php _e(' items/item updated successfully!', 'tainacan') ?>', '<?php _e('Success', 'tainacan') ?>', set_toastr_class());
        }
    }
    // atribui os metadados de dados para todos os itens selecionados
    function setPropertyData(data, property_id) {
        var counter = 0;
        if ($(data).val() != '') {
            $.each($("input:checkbox[name='selected_items']:checked"), function () {
                counter++;
                var values = [];
                $('.multiple_socialdb_property_'+ property_id).each(function(index,value){
                    if($(this).val()!=''){
                        values.push($(this).val());
                    }
                });
                $("#socialdb_property_" + property_id + "_" + $(this).val()).val(values.join('||'));
                //$("#socialdb_property_" + property_id + "_" + $(this).val()).val($(data).val());
            });
            toastr.success(counter + '<?php _e(' items/item updated successfully!', 'tainacan') ?>', '<?php _e('Success', 'tainacan') ?>', set_toastr_class());
        }
    }
    // atribui os metadados de dados para todos os itens selecionados
    function setPropertyObject(object, property_id, showMessage) {
        var counter = 0;
        if (object != '') {
            $.each($("input:checkbox[name='selected_items']:checked"), function () {
                var array = $("#socialdb_property_" + property_id + "_" + $(this).val()).val().split('||'); // TORNA O VALOR DO hidden em arrya para poder adicionar
                if (array.indexOf(object) < 0) {
                    array.push(object);
                }
                $("#socialdb_property_" + property_id + "_" + $(this).val()).val(array.join('||'));
                counter++;
            });
            if (!showMessage) {
                toastr.success(counter + '<?php _e(' items/item updated successfully!', 'tainacan') ?>', '<?php _e('Success', 'tainacan') ?>', set_toastr_class());
            }
        }
    }
    function removePropertyObject(object, property_id) {
        var counter = 0;
        if (object != '') {
            $.each($("input:checkbox[name='selected_items']:checked"), function () {
                var array = $("#socialdb_property_" + property_id + "_" + $(this).val()).val().split('||').filter(function (v) {
                    return v !== ''
                }); // TORNA O VALOR DO hidden em arrya para poder adicionar
                for (var i = 0; i < array.length; i++) {
                    if (array[i] == object) {
                        array.splice(i, 1);
                    }
                }
                $("#socialdb_property_" + property_id + "_" + $(this).val()).val(array.join('||'));
                counter++;
            });
            toastr.success(counter + '<?php _e(' items/item updated successfully!', 'tainacan') ?>', '<?php _e('Success', 'tainacan') ?>', set_toastr_class());
        }
    }
//******* atribui valores para as categorias dos items selecionados
    // checkbox
    function setCategoriesCheckbox(property_id, value_id) {
        var counter = 0;
        $.each($("input:checkbox[name='selected_items']:checked"), function () {
            counter++;
            $("#socialdb_property_" + property_id + "_" + $(this).val()).val('');
            //var array = $("#socialdb_property_"+property_id+"_"+$(this).val()).val().split(',').filter(function(v){return v!==''}); // busco todas categorias de cada objeto
            var array = [];
            $.each($("input:checkbox[name='multiple_socialdb_propertyterm_" + property_id + "']:checked"), function () { // percorro todas categorias da propriedade
                array.push($(this).val());
            });
            $("#socialdb_property_" + property_id + "_" + $(this).val()).val(array.join(','));
        });
        validate_checkbox(property_id);
        $('#sumbit_multiple_items .auto-save').trigger('change');
        if (value_id !== 'not') {
            toastr.success(counter + '<?php _e(' items/item updated successfully!', 'tainacan') ?>', '<?php _e('Success', 'tainacan') ?>', set_toastr_class());
        }
    }
    // radio button
    function setCategoriesRadio(property_id, value_id) {
        var counter = 0;
        $.each($("input:checkbox[name='selected_items']:checked"), function () {
            counter++;
            $("#socialdb_property_" + property_id + "_" + $(this).val()).val(value_id);
        });
        validate_radio(property_id);
        $('#sumbit_multiple_items .auto-save').trigger('change');
        toastr.success(counter + '<?php _e(' items/item updated successfully!', 'tainacan') ?>', '<?php _e('Success', 'tainacan') ?>', set_toastr_class());
    }
    //select box
    function setCategoriesSelect(property_id, field) {
        var counter = 0;
        $.each($("input:checkbox[name='selected_items']:checked"), function () {
            counter++;
            if ($(field).val() != '') {
                $("#socialdb_property_" + property_id + "_" + $(this).val()).val($(field).val());
            }
        });
        validate_selectbox(field,property_id);
        toastr.success(counter + '<?php _e(' items/item updated successfully!', 'tainacan') ?>', '<?php _e('Success', 'tainacan') ?>', set_toastr_class());
    }
    //tree radio
    function setCategoriesTree(property_id, value_id) {
        var counter = 0;
        $.each($("input:checkbox[name='selected_items']:checked"), function () {
            counter++;
            if (value_id != '') {
                $("#socialdb_property_" + property_id + "_" + $(this).val()).val(value_id);
            } else {
                $("#socialdb_property_" + property_id + "_" + $(this).val()).val('');
            }
        });
        $('#sumbit_multiple_items .auto-save').trigger('change');
        toastr.success(counter + '<?php _e(' items/item updated successfully!', 'tainacan') ?>', '<?php _e('Success', 'tainacan') ?>', set_toastr_class());
    }
    //select box multipple
    function setCategoriesSelectMultiple(property_id, field) {
        var counter = 0;
        if($(field).val()===null){
            return false;
        }
        var array = $(field).val().join(',');
        $.each($("input:checkbox[name='selected_items']:checked"), function () {
            counter++;
            if (array != '' && array != ',') {
                $("#socialdb_property_" + property_id + "_" + $(this).val()).val(array);
            }
        });
        validate_multipleselectbox(field,property_id);
        toastr.success(counter + '<?php _e(' items/item updated successfully!', 'tainacan') ?>', '<?php _e('Success', 'tainacan') ?>', set_toastr_class());
    }
    // coloca a descricao para todos os items selecionados
    function setLicenses() {
        if($("input[name='object_license']")){
            $("input[name='object_license']").change(function(){ 
                  var counter = 0;
                  $.each($("input:checkbox[name='selected_items']:checked"), function () {
                    counter++;
                    $("#license_" + $(this).val()).val($("input[name='object_license']:checked").val());
                  });
                //$("#multiple_object_description").attr("value", "");
                //$("#multiple_object_name").val(''); 
                toastr.success(counter + '<?php _e(' items/item updated successfully!', 'tainacan') ?>', '<?php _e('Success', 'tainacan') ?>', set_toastr_class());
            });
        }
    }
    /****************** Selecionar anexos para um item checado **********************************************/
    //mostra os itens para selecao de anexos
    function selectedAttachments() {
        var item_id;
        $.each($("input:checkbox[name='selected_items']:checked"), function () {
            item_id = $(this).val();// pego o item aonde sera inserido os attachaments
        });
        var title = $("#title_" + item_id).val();// pego o titulo do item a ser inserido os anexos
        if (title == '') {// caso  o titulo estiver vazio
            title = '<?php _e('No title especified!') ?>';
        }
        hide_group_checkboxes();
        $("#submit_button").hide();
        $("#nameItemAttachment").text(title);// coloco o titulo no bloco a esquerda dos itens
        $("#form_properties_items").hide();// escondo o formulario de edicao de metadados
        $("#selectOptions").hide();// escondo a opcao de selecionar todos
        $("#removeSelectedButton").hide();// escondo o botao de deletar itens
        $("#buttonSelectedAttachments").hide();// escondo o botao selecionar anexos
        $("#buttonBackItems").show();// mostro o botao para voltar para edicao de itens
        $("#selectingAttachment").show();// mostro o bloco com o titulo do item q sera inserido os anexos
        //$(".class_selected_items").hide();//escondo o checkbox dos items
        // $(".class_checkboxAttachments").show();//mostro o checkbox dos anexos
        var myAttachments = $("#attachments_" + item_id).val().split(',').filter(Boolean);
        $.each($("input:checkbox[name='checkboxAttachments']"), function () {// percorro todos os itens para retirar o checkbox do item qu3e foi selecionado
            if ($(this).val() == item_id) {
                $(this).css('display', 'none');//retiro o checkbutton
                set_item_selected_colour($(this).val());
                //$('#panel_'+$(this).val()).css('background-color','#c0c0c0');
            }
            if (myAttachments.lengh > 0 && myAttachments.indexOf($(this).val())) {// busco os attachments deste items
                $(this).attr('checked', 'checked');
                set_attachment_selected_colour($(this).val());
                // $('#panel_'+$(this).val()).css('background-color','#c9c9c9');
            }

        });
    }
    // retorna para edicao de itens
    function backItemsEditting() {
        show_group_checkboxes();
        $("#submit_button").show();
        $("#form_properties_items").show();// mostro o formulario de edicao de metadados
        $("#selectOptions").show();// mostra a opcao de selecionar todos
        $("#removeSelectedButton").show();// mostra o botao de deletar itens
        $("#buttonSelectedAttachments").show();// mostra o botao selecionar anexos
        $("#buttonBackItems").hide();// esconde o botao para voltar para edicao de itens
        $("#selectingAttachment").hide();// esconde o bloco com o titulo do item q sera inserido os anexos
        //$(".class_selected_items").show();//mostro o checkbox dos items
        //$(".class_checkboxAttachments").hide();//escondo o checkbox dos anexos
        $.each($("input:checkbox[name='selected_items']"), function () {
            if ($("#parent_" + $(this).val()).val() !== '') {//retiro os checkbox dos items q sao anexos
                $("#item_option_" + $(this).val()).hide();
            }
        });
    }
    //funcao que insere e retira o attachment
    function manipulateAttachaments(attachment_id) {
        var isInserting = false;
        var array_attachments = [];
        var item_id = getOnlyItemID();// pego o ID do item que sera inserido/removido o anexo
        var title = $("#title_" + attachment_id).val();// pego o titulo do anexo
        if (title == '') {// caso  o titulo estiver vazio
            title = '<?php _e('No title especified!') ?>';
        }
        $.each($("input:checkbox[name='checkboxAttachments']:checked"), function () {// percorro todos os itens para retirar o checkbox do item qu3e foi selecionado
            array_attachments.push($(this).val());
            if ($(this).val() == attachment_id) {
                isInserting = true;
            }
            $("#parent_" + $(this).val()).val(item_id);// coloco o id do item pai (item) no anexo
        });
        if (array_attachments.length > 0) {
            $("#attachments_" + item_id).val(array_attachments.join(','));
        }
        if (isInserting) {
            //$("#item_option_"+attachment_id).hide();
            toastr.success('<?php _e('Attachment added successfully!', 'tainacan') ?>', '<?php _e('Success', 'tainacan') ?>', set_toastr_class());
        } else {
            //$("#item_option_"+attachment_id).show();
            $("#parent_" + attachment_id).val('');// removo o item pai (item), retornando para edicao de item
            toastr.success('<?php _e('Attachment removed successfully!', 'tainacan') ?>', '<?php _e('Success', 'tainacan') ?>', set_toastr_class());
        }
    }
   /**
     * Autocomplete para os metadados de dados para insercao/edicao
     * @param {type} e
     * @returns {undefined}
     */
    function multiple_autocomplete_property_data(properties_autocomplete) {
        console.log(properties_autocomplete);
         if (properties_autocomplete) {
            $.each(properties_autocomplete, function (idx, property_id) {
                        $("#multiple_socialdb_property_" + property_id).autocomplete({
                            source: $('#src').val() + '/controllers/collection/collection_controller.php?operation=list_items_search_autocomplete&property_id=' + property_id,
                            messages: {
                                noResults: '',
                                results: function () {
                                }
                            },
                            minLength: 2,
                            select: function (event, ui) {
                                $("#multiple_socialdb_property_" + property_id).val('');
                                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                                var temp = $("#property_value_" + property_id).val();
                                if (typeof temp == "undefined") {
                                    $("#multiple_socialdb_property_" + property_id).val(ui.item.value);
                                }
                            }
                        });
                    });
                }
    }
    /***************************** metadado de objeto (GERA O AUTOCOMPLETE) ************************************/
    function multiple_autocomplete_object_property_add(property_id, object_id) {
        $("#multiple_autocomplete_value_" + property_id + "_" + object_id).autocomplete({
            source: $('#src').val() + '/controllers/object/object_controller.php?operation=get_objects_by_property_json&property_id=' + property_id,
            messages: {
                noResults: '',
                results: function () {
                }
            },
            minLength: 2,
            select: function (event, ui) {
                var already_selected = false;
                $("#multiple_property_value_" + property_id + "_" + object_id+"_add option").each(function(){
                        if($(this).val()==ui.item.value){
                            already_selected = true;
                        }
                });
                $("#multiple_autocomplete_value_" + property_id + "_" + object_id).html('');
                $("#multiple_autocomplete_value_" + property_id + "_" + object_id).val('');
                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                if(!already_selected){
                    if($('#cardinality_'+property_id + "_" + object_id).val()=='1'){
                         $("#property_value_" + property_id + "_" + object_id + "_edit").html('');
                    }
                    $("#multiple_property_value_" + property_id + "_" + object_id + "_add").append("<option class='selected' value='" + ui.item.value + '_' + ui.item.label + "' selected='selected' >" + ui.item.label + "</option>");
                    setPropertyObject(ui.item.value + '_' + ui.item.label, property_id);
                }
                setTimeout(function () {
                    $("#multiple_autocomplete_value_" + property_id + "_" + object_id).val('');
                }, 100);
            }
        });
    }

    function clear_select_object_property(e, property_id) {
        console.log($(e).val());
        removePropertyObject($(e).val(), property_id);
        $('option:selected', e).remove();
        //$('.chosen-selected2 option').prop('selected', 'selected');
    }

//************************* properties terms (MOSTRA OS DADOS DE METADADOS DE TERMO) ******************************************//
    function list_properties_term_insert_objects() {
        var radios = multiple_get_val($("#multiple_properties_terms_radio").val());
        var selectboxes = multiple_get_val($("#multiple_properties_terms_selectbox").val());
        var trees = multiple_get_val($("#multiple_properties_terms_tree").val());
        var checkboxes = multiple_get_val($("#multiple_properties_terms_checkbox").val());
        var multipleSelects = multiple_get_val($("#multiple_properties_terms_multipleselect").val());
        var treecheckboxes = multiple_get_val($("#multiple_properties_terms_treecheckbox").val());
        multiple_list_radios(radios);
        multiple_list_tree(trees);
        multiple_list_selectboxes(selectboxes);
        multiple_list_multipleselectboxes(multipleSelects);
        multiple_list_checkboxes(checkboxes);
        multiple_list_treecheckboxes(treecheckboxes);
    }
    // radios
    function multiple_list_radios(radios) {
        if (radios) {
            $.each(radios, function (idx, radio) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: radio}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#multiple_field_property_term_' + radio).html('');
                    $.each(elem.children, function (idx, children) {
                        //  if (property.id == selected) {
                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        //  } else {
                        $('#multiple_field_property_term_' + radio).append('<input value="' + children.term_id + '" type="radio" onchange="setCategoriesRadio(' + radio + ',' + children.term_id + ',0)"  name="multiple_socialdb_propertyterm_' + radio + '" >&nbsp;' + children.name + '<br>');
                        //  }
                    });
                });
            });
        }
    }
    // checkboxes
    function multiple_list_checkboxes(checkboxes) {
        if (checkboxes) {
            $.each(checkboxes, function (idx, checkbox) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: checkbox}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#multiple_field_property_term_' + checkbox).html('');
                    $.each(elem.children, function (idx, children) {
                        $('#multiple_field_property_term_' + checkbox).append('<input type="checkbox" onchange="setCategoriesCheckbox(' + checkbox + ',' + children.term_id + ')"  name="multiple_socialdb_propertyterm_' + checkbox + '" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                    });
                    // var required = '';
                    // if(elem.metas.socialdb_property_required==='true'){
                    //   required = 'required';
                    //}
                    //$('#multiple_field_property_term_' + checkbox).append('<input type="hidden" name="checkbox_required_'+checkbox+'" value="'+required+'" >');
                });
            });
        }
    }

    // selectboxes
    function multiple_list_selectboxes(selectboxes) {
        if (selectboxes) {
            $.each(selectboxes, function (idx, selectbox) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: selectbox}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#multiple_field_property_term_' + selectbox).html('');
                    $('#multiple_field_property_term_' + selectbox).html('<option value=""><?php _e('Select...', 'tainacan') ?></option>');
                    $.each(elem.children, function (idx, children) {
                        //  if (property.id == selected) {
                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        //  } else {
                        $('#multiple_field_property_term_' + selectbox).append('<option value="' + children.term_id + '">' + children.name + '</option>');
                        //  }
                    });
                });
            });
        }
    }
    // multiple
    function multiple_list_multipleselectboxes(multipleSelects) {
        if (multipleSelects) {
            $.each(multipleSelects, function (idx, multipleSelect) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: multipleSelect}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#multiple_field_property_term_' + multipleSelect).html('');
                    $.each(elem.children, function (idx, children) {
                        //  if (property.id == selected) {
                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        //  } else {
                        $('#multiple_field_property_term_' + multipleSelect).append('<option value="' + children.term_id + '">' + children.name + '</option>');
                        //  }
                    });
                });
            });
        }
    }
    // treecheckboxes
    function multiple_list_treecheckboxes(treecheckboxes) {
        if (treecheckboxes) {
            $.each(treecheckboxes, function (idx, treecheckbox) {
                $("#multiple_field_property_term_" + treecheckbox).dynatree({
                    selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
                    checkbox: true,
                    initAjax: {
                        url: $('#src').val() + '/controllers/category/category_controller.php',
                        data: {
                            collection_id: $("#collection_id").val(),
                            property_id: treecheckbox,
                            operation: 'initDynatreeDynamic'
                        }
                        , addActiveKey: true
                    },
                    onLazyRead: function (node) {
                        node.appendAjax({
                            url: $('#src').val() + '/controllers/collection/collection_controller.php',
                            data: {
                                collection: $("#collection_id").val(),
                                key: node.data.key,
                                classCss: node.data.addClass,
                                //operation: 'findDynatreeChild'
                                operation: 'expand_dynatree'
                            }
                        });
                    },
                    onClick: function (node, event) {
                        // Close menu on click
                        //$("#property_object_category_id").val(node.data.key);
                        // $("#property_object_category_name").val(node.data.title);

                    },
                    onKeydown: function (node, event) {
                    },
                    onCreate: function (node, span) {
                         bindContextMenuSingle(span,'multiple_field_property_term_' + treecheckbox);
                    },
                    onPostInit: function (isReloading, isError) {
                    },
                    onActivate: function (node, event) {
                    },
                    onSelect: function (flag, node) {
                        var selKeys = $.map(node.tree.getSelectedNodes(), function (node) {
                            return node.data.key;
                        });
                        setCategoriesTree(treecheckbox, selKeys.join(','));
                        //metadados
                        var categories = $.map(node.tree.getSelectedNodes(), function (node) {
                            return node.data.key;
                        });
                        if(categories.length>0&&categories.indexOf(node.data.key)>=0){
                            append_category_properties(node.data.key,node.data.key,treecheckbox);
                        }else{
                            append_category_properties(0,node.data.key,treecheckbox);
                        }
                    },
                    dnd: {
                    }
                });
            });
        }
    }

    // tree
    function multiple_list_tree(trees) {
        if (trees) {
            $.each(trees, function (idx, tree) {
                $("#multiple_field_property_term_" + tree).dynatree({
                    checkbox: true,
                    // Override class name for checkbox icon:
                    classNames: {checkbox: "dynatree-radio"},
                    selectMode: 1,
                    selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
                    checkbox: true,
                            initAjax: {
                                url: $('#src').val() + '/controllers/category/category_controller.php',
                                data: {
                                    collection_id: $("#collection_id").val(),
                                    property_id: tree,
                                    // hide_checkbox: 'true',
                                    operation: 'initDynatreeDynamic'
                                }
                                , addActiveKey: true
                            },
                    onLazyRead: function (node) {
                        node.appendAjax({
                            url: $('#src').val() + '/controllers/collection/collection_controller.php',
                            data: {
                                collection: $("#collection_id").val(),
                                key: node.data.key,
                                // hide_checkbox: 'true',
                                classCss: node.data.addClass,
                                //operation: 'findDynatreeChild'
                                operation: 'expand_dynatree'
                            }
                        });
                    },
                    onClick: function (node, event) {
                        // Close menu on click
                        var key = node.data.key;
                        if (key.search('moreoptions') < 0 && key.search('alphabet') < 0) {
                            $("#socialdb_propertyterm_" + tree).html('');
                            $("#socialdb_propertyterm_" + tree).append('<option selected="selected" value="' + node.data.key + '" >' + node.data.title + '</option>')
                        }
                    },
                    onKeydown: function (node, event) {
                    },
                    onCreate: function (node, span) {
                         bindContextMenuSingle(span,'multiple_field_property_term_' + tree);
                    },
                    onPostInit: function (isReloading, isError) {
                    },
                    onActivate: function (node, event) {
                    },
                    onSelect: function (flag, node) {
                        setCategoriesTree(tree, node.data.key);
                        //verificando se existe metadados
                        if ($("#socialdb_propertyterm_" + tree).val() === node.data.key) {
                            append_category_properties(0,node.data.key,tree);
                            $("#socialdb_propertyterm_" + tree).val("");
                        } else {
                            append_category_properties(node.data.key,$("#socialdb_propertyterm_" + tree).val(),tree);
                            $("#socialdb_propertyterm_" + tree).val(node.data.key);
                        }
                    },
                    dnd: {
                    }
                });
            });
        }
    }


    /*********************** FUNCOES DE SUPORTE ********************************/
    // funcao q retorna o id do item selecionado para edicao
    function getOnlyItemID() {
        var item_id;
        $.each($("input:checkbox[name='selected_items']:checked"), function () {
            item_id = $(this).val();// pego o item aonde sera inserido os attachaments
        });
        return item_id;
    }
    // get value of the property
    function multiple_get_val(value) {
        if (value === '') {
            return false;
        } else if (value.split(',')[0] === '' && value !== '') {
            return [value];
        } else {
            return value.split(',');
        }
    }
    //removendo arrays com itens duplicados
    function remove_duplicates_safe(arr) {
        var obj = {};
        var arr2 = [];
        for (var i = 0; i < arr.length; i++) {
            if (!(arr[i] in obj)) {
                arr2.push(arr[i]);
                obj[arr[i]] = true;
            }
        }
        return arr2;

    }
    //seta cor selecionado item
    function set_item_selected_colour(id) {
       // $('#panel_' + id).css('background-color', '#c0c0c0');
        $('#wrapper_' + id).addClass('selected-border');
    }
    //seta cor selecionado item
    function set_attachment_selected_colour(id) {
        //$('#panel_' + id).css('background-color', '#c7cadd');
    }
    //limpa cor selecionado
    function clean_item_selected_colour(id) {
       // $('#panel_' + id).css('background-color', '#e8e8e8');
        $('#wrapper_' + id).removeClass('selected-border');
    }
    //esconde checkboxes dos grupos por tipo
    function hide_group_checkboxes() {
        $('#selectAllImages').hide();
        $('#selectAllVideo').hide();
        $('#selectAllPdf').hide();
        $('#selectAllAudio').hide();
        $('#selectAllOther').hide();
    }
    //mostra checkboxes dos grupos por tipo
    function show_group_checkboxes() {
        $('#selectAllImages').show();
        $('#selectAllVideo').show();
        $('#selectAllPdf').show();
        $('#selectAllAudio').show();
        $('#selectAllOther').show();
    }
    //setar classe do toaster
    function set_toastr_class() {
        return {positionClass: 'toast-bottom-right', preventDuplicates: true};
    }
    //voltar para a listagem de itens
    function back_main_list_socialnetwork() {
        swal({
            title: '<?php _e('Attention!','tainacan') ?>',
            text: '<?php _e('You did not finish your action. Are you sure to leave this page?','tainacan') ?>',
            type: "error",
            showCancelButton: true,
            cancelButtonText: '<?php _e('Cancel','tainacan') ?>',
            confirmButtonClass: 'btn-success',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $('#modalImportSocialnetworkClean').modal('show');
                $('#form').hide();
                $("#tainacan-breadcrumbs").hide();
                $('#create_button').show();
                $('#menu_object').show();
                $("#list").show();
                $("#container_socialdb").show('fast');
                $('#main_part').show();
                $('#collection_post').show();
                $('#configuration').slideDown();
                $('#configuration').hide();
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/object/object_controller.php",
                    data: {
                        operation: 'remove_ids_socialnetwork',
                        items_id: $('#items_id').val(),
                        collection_id: $('#collection_id').val()}
                }).done(function (result) {
                    elem_first = jQuery.parseJSON(result);
                    showList($('#src').val());
                    $('#modalImportSocialnetworkClean').modal('hide');
                });
            }
        });
        
    }
    
    function back_main_list() {
        $('#form').hide();
        $('#configuration').hide();
        $('#main_part').show();
        $('#display_view_main_page').show();
        $("#container_socialdb").show('fast');
         showList($('#src').val());
    }
    //accordion para os campos dos metados dos itens
    $("#accordion_socialnetwork").accordion({
        collapsible: true,
        header: "h2",
        heightStyle: "content"
    });


    // selecionando items com o mouse
    $("#selectable").selectable({
        filter: ".item",
        stop: function () {
            $(".ui-selected", this).each(function () {
                var id = this.id;
                console.log(id.replace('panel_', ''));
                focusItem(id.replace('panel_', ''));
            });
        }
    });
  //######## INSERCAO DE UM ITEM AVULSO EM UMA COLECAO #########################//    
    function add_new_item_by_title(collection_id,seletor,property_id,object_id){
        var title = '';
        $( '.title_'+property_id+'_'+ object_id).each(function( index ) {
            if($( this ).val()&&$( this ).val().trim()!==''){
                title = $( this ).val().trim();
                return;
            }
        });
        if(title===''){
            showAlertGeneral('<?php _e('Attention!','tainacan') ?>','<?php _e('Item title is empty!','tainacan') ?>','info');
        }else{
            $(seletor).trigger('click');
            $('.title_'+ property_id + "_" + object_id ).val('');
            show_modal_main();
            $.ajax({
                url: $('#src').val() + '/controllers/object/object_controller.php',
                type: 'POST',
                data: { operation: 'insert_fast', collection_id: collection_id, title: title}
            }).done(function (result) {
                hide_modal_main();
                wpquery_filter();
                //list_all_objects(selKeys.join(", "), $("#collection_id").val());
                elem_first = jQuery.parseJSON(result);
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                if(elem_first.type==='success'){
                    $("#multiple_property_value_" + property_id + "_" + object_id + "_add").append("<option class='selected' value='" + elem_first.item.ID + "' selected='selected' >" + elem_first.item.post_title + "</option>");
                }
            });
        }
    }        
//################################ Cardinalidade #################################//    
        function show_fields_metadata_cardinality(property_id,id){
        $('#button_property_'+property_id+'_'+id).hide();
        $('#container_field_'+property_id+'_'+(id+1)).show();         
    }
    
    
    function remove_container(property_id,id){
        var show_button = false;
        $('#container_field_'+property_id+'_'+(id)).hide();
        $('#core_validation_'+property_id).val('true');
        $('#form_autocomplete_value_'+property_id+'_'+(id)+"_origin").val('');
        validate_all_fields();
        //se o proximo container
        if(!$('#container_field_'+property_id+'_'+(id+1)).is(':visible')){
            show_button = true;
        }
        //busco o container que esta sendo mostrado
        while(!$('#container_field_'+property_id+'_'+(id)).is(':visible')){
            id--;
        }
        //se 
        if(show_button)
            $('#button_property_'+property_id+'_'+id).show();
        
    }
//################################ adicao de propriedades de categorias #################################//    
    function append_category_properties(id,remove_id,property_id){
        //buscando as categorias selecionadas nos metadados de termo
        var selected_categories = $('#selected_categories').val();
        if(selected_categories===''){
             selected_categories= [];
        }else{
            selected_categories = selected_categories.split(',');
        }
        //se estiver retirando alguma das categorias
        if(remove_id&&selected_categories.indexOf(remove_id)>=0){
            var index = selected_categories.indexOf(remove_id);
            selected_categories.splice(index, 1);
            $('#selected_categories').val(selected_categories.join(','));
            if($('.category-'+remove_id)){
                $.each($('.category-'+remove_id),function(index,value){
                    var id = $(this).attr('property');
                    var type = $(this).attr('type');
                    remove_property_general(id,type);
                });
                $('.category-'+remove_id).remove();
            }
            

        }
        //busco os metadados da categoria selecionada    
        if(id&&selected_categories.indexOf(id)>=0){
            //var index = selected_categories.indexOf(id);
           // selected_categories.splice(index, 1);
            //$('#selected_categories').val(selected_categories.join(','));
        }else if(id!==0){
            selected_categories.push(id);
            //adicionando metadados
            show_modal_main();
            $.ajax({
                url: $('#src').val() + '/controllers/object/object_controller.php',
                type: 'POST',
               data: { operation: 'list_properties_categories_accordeon_multiple',properties_to_avoid:$('#properties_id').val(),categories: id, object_id:$('#object_id_add').val()}
            }).done(function (result) {
                hide_modal_main();
                //list_all_objects(selKeys.join(", "), $("#collection_id").val());
                 $('#append_properties_categories_'+property_id).html(result);
                insert_html_property_category(id,property_id);

            });
            $('#selected_categories').val(selected_categories.join(','));
        }
    }
    
    function insert_html_property_category(category_id,property_id){
        var flag = false;
        $ul = $("#accordion_socialnetwork");
        $items = $("#accordion_socialnetwork").children();
        $('#append_properties_categories_'+property_id).css('margin-top','15px');
        $properties_append = $('#append_properties_categories_'+property_id).children().children();
        $properties_append.animate({borderWidth : '1px',borderColor: 'red',borderStyle: 'dotted'}, 'slow', 'linear');
        setTimeout(removeBorderCat(property_id),8000);
        for (var i = 0; i <$properties_append.length; i++) {
              // index is zero-based to you have to remove one from the values in your array
                for(var j = 0; j<$items.length;j++){
                    if($($items.get(j)).attr('id')&&$($items.get(j)).attr('id')===$($properties_append.get(i)).attr('id')){
                        flag = true;
                        if(!$($items.get(j)).hasClass('category-'+category_id)){
                            $($items.get(j)).addClass('category-'+category_id);
                        }
                    }
                }
                if(!flag){
                   //$( $properties_append.get(i) ).appendTo( $ul);
                   var id =  $( $properties_append.get(i) ).attr('property');
                   var type =  $( $properties_append.get(i) ).attr('type');
                   if(id&&type)
                        add_property_general(id,type);
                }
               flag = false;
         }
         $("#accordion_socialnetwork").accordion("destroy");  
         $("#accordion_socialnetwork").accordion({
                    active: false,
                    collapsible: true,
                    header: "h2",
                    heightStyle: "content"
                });
         $('[data-toggle="tooltip"]').tooltip();
    }
     //retira as bordas
    function removeBorderCat(property_id){
        $properties_append = $('#append_properties_categories_'+property_id).children().children();
        $properties_append.animate({borderWidth : '1px',borderColor: '#d3d3d3',borderStyle:"solid"}, 'slow', 'linear');
    }
    //adicionando as propriedades das categorias no array de propriedades gerais
    function add_property_general(id,type){
        if(type=='hidden'){
            return false;
        }
        var ids = $('#multiple_properties_'+type+'_id').val().split(','); 
        var flag = false;
        if(ids){
           ids.push(id);
           //adiciono
           $.each($("input:checkbox[name='selected_items']:checked"), function () {
                var item_id = $(this).val();
                $items = $("#wrapper_"+item_id).children();
                for (var i = 0; i <$items.length; i++) {
                    if($($items.get(i)).attr('id')=='socialdb_property_'+id+'_'+item_id){
                        flag = true;
                    }
                }
                if(!flag){
                    var html = '<input type="hidden" id="socialdb_property_'+id+'_'+item_id+'" name="socialdb_property_'+id+'_'+item_id+'" value="">'
                    $("#wrapper_"+item_id).append(html); 
                }
                flag = false;
           });
        }
         $('#multiple_properties_'+type+'_id').val(ids.join(','));
    }
    //removendo as propriedades das categorias no array de propriedades gerais
    function remove_property_general(id,type){
        if(type=='hidden'){
            return false;
        }
        var ids = $('#multiple_properties_'+type+'_id').val().split(','); 
        var index = ids.indexOf(id);
        ids.splice(index, 1);
        $('#multiple_properties_'+type+'_id').val(ids.join(','));
    }
//################################ VALIDACOES#################################//
    /**
     * funcao que valida os campos radios, e realiza a insercao das propriedades de categorias
     * @param {type} property_id
     * @returns {undefined}     */
    function validate_radio(property_id){
        var selected = $("input[type='radio'][name='multiple_socialdb_propertyterm_"+property_id+"']:checked");
        if($(selected[0]).val()===$('#socialdb_propertyterm_'+property_id+'_value').val()){
            $(selected[0]).removeAttr('checked');
        }
        if (selected.length > 0) {
            append_category_properties(selected.val(), $('#socialdb_propertyterm_'+property_id+'_value').val(),property_id);
            $('#socialdb_propertyterm_'+property_id+'_value').val(selected.val()); 
        }
    }
    /**
     * funcao que valida o campo checkbox, e realiza a insercao das propriedades de categorias
     * @param {type} property_id
     * @returns {undefined}     */
    function validate_checkbox(property_id){
        //verificando se existe propriedades para serem  adicionadas
        $.each($("input[type='checkbox'][name='socialdb_propertyterm_"+property_id+"[]']"),function(index,value){
            if($(this).is(':checked')){
                append_category_properties($(this).val(),$(this).val(),property_id);
            }else{
                append_category_properties(0,$(this).val(),property_id);
            }
        });
    }
    /**
     * funcao que valida os campos de selecao unica
     * @param {type} seletor
     * @param {type} property_id
     * @returns {undefined}     */
    function validate_selectbox(seletor,property_id){
        if($(seletor).val()===''){
            $('#core_validation_'+property_id).val('false');
            set_field_valid(property_id,'core_validation_'+property_id);
        }else{
            append_category_properties($(seletor).val(), $('#socialdb_propertyterm_'+property_id+'_value').val(),property_id);
           $('#socialdb_propertyterm_'+property_id+'_value').val($(seletor).val()); 
        }
        
    }
    /**
     * funcao que valida os campos de multipla selecao
     * @param {type} id
     * @param {type} seletor
     * @returns {undefined}     */
    function validate_multipleselectbox(seletor,property_id){
        var selected = $("#field_property_term_"+property_id+"").find(":selected");
        if (selected.length > 0) {
            //verificando se existe propriedades para serem  adicionadas
            $.each($("#multiple_field_property_term_"+property_id+" option"),function(index,value){
                if($(this).is(':selected')){
                    append_category_properties($(this).val(),$(this).val(),property_id);
                }else{
                    append_category_properties(0,$(this).val(),property_id);
                }
            });
        }
    }     
</script>
