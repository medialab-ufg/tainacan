<script>
    $(function () {
        // #1 - breadcrumbs para localizacao da pagina
        $("#tainacan-breadcrumbs").show();
        $("#tainacan-breadcrumbs .current-config").text('> <?php  echo ($is_beta_text) ?  __('Continue inserting','tainacan')  : __('Edit item','tainacan') ?>');
        //#2  -  ativo os tootips
         $('[data-toggle="tooltip"]').tooltip();
        changeContainerBgColor();
        $('#edit_object_collection_id').val($('#collection_id').val());
        // escondo o menu de adicionar objeto e ordenacao
        $("#menu_object").hide();
        // pegando os valores selecionados anteriormente
        var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function (node) {
            return node.data.key;
        });
        $('#selected_nodes_dynatree').val(selKeys.join(", "));
        //percorre 
        try {
            $("#dynatree").dynatree("getRoot").visit(function (node) {
                var nodes_selected = $('#object_classifications_edit').val().split(',');
                if (nodes_selected.indexOf(node.data.key) > -1) {
                    match = node;
                    node.select(node);
                }
            });
        }catch(err) {
           console.log('No dynatree found!');
        }
        //inicializando os containers da pagina
        initiate_tabs().done(function (result) {
            $.when( 
                list_ranking_update($("#object_id_edit").val()),//busca os rankings do item
                show_object_properties_edit(),//mostra as propriedades do item, com os formularios e seus widgets
                show_collection_licenses()//mostra as licencas disponiveis
            ).done(function ( v1, v2 ) {
                append_property_in_tabs();
                list_tabs();
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/collection/collection_controller.php",
                    data: { operation: 'get_ordenation_properties',collection_id:$('#collection_id').val() }
                }).done(function(result) {
                    var json = $.parseJSON(result);
                    if(json&&json.ordenation&&json.ordenation!==''){
                        for (var $property in json.ordenation) {
                            if (json.ordenation.hasOwnProperty($property)) {
                                reorder_properties_edit_item($property,json.ordenation[$property].split(','));
                                if($property==='default')
                                    reorder_properties_edit_item($property,json.ordenation[$property].split(','),"#text_accordion");
                            }
                        }
                    }
                    //autocomplete na edicao
                    var properties_autocomplete = edit_get_val($("#edit_properties_autocomplete").val());
                    autocomplete_edit_item_property_data(properties_autocomplete); 
                    //ckeditor
                    $("#text_accordion").accordion({
                        active: false,
                        collapsible: true,
                        header: "h2",
                        heightStyle: "content"
                    });
                    if($('#mediaHabilitateContainer').length>0){
                        $('#mediaHabilitateContainer').css('min-height','500px');
                        $('#mediaHabilitateContainer').show();
                    }
                    // esconde o carregamento do menu lateral
                    $('.menu_left_loader').hide();
                    $('.menu_left').show();
                    <?php if(!isset($is_view_mode)): ?>
                    showCKEditor('objectedit_editor');
                    createDraft();
                    set_content_valid();
                    validate_all_fields()
                     <?php else: ?>
                     $('#submit_form_edit_object h2 span').hide(); 
                     list_files_single( $("#object_id_edit").val());
                    <?php endif; ?>
                });
            });
        });     
        //submit do editar
        $('#submit_form_edit_object').submit(function (e) {
            e.preventDefault();
            var verify = $(this).serializeArray();
//            if (verify[0].value.trim() === '') {// verifica o nome do item
//                showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('Please set a valid item name', 'tainacan') ?>', 'info');
//                return false;
//            }
            $("#object_content_edit").val(CKEDITOR.instances.objectedit_editor.getData());
            //VERIFICACAO SE CATEGORIAS MAIS PROFUNDAS NÃO DEIXARAM DE SER SELECIONADAS
            var removed = [];
            var nodes_selected = $('#object_classifications_edit').val().split(',');
            var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function (node) {
                return node.data.key;
            });
            if( $("#dynatree").length>0){
                $("#dynatree").dynatree("getRoot").visit(function (node) {
                    if (nodes_selected.indexOf(node.data.key) > -1 && selKeys.indexOf(node.data.key) < 0) {
                        removed.push(node.data.key);
                    }
                });
            }
            
            $.each(nodes_selected, function (key, value) {
                if (removed.indexOf(value.trim()) < 0 && selKeys.indexOf(value.trim()) < 0) {
                    selKeys.push(value.trim());
                }
            });
            
              //hook para validacao do formulario
            if(Hook.is_register( 'tainacan_validate_create_item_form')){
                 e.preventDefault();
               Hook.call( 'tainacan_validate_create_item_form', [ $( this ).serializeArray() ] );
                if(!Hook.result.is_validated){
                    $('#modalImportMain').modal('hide');//mostro o modal de carregamento
                    console.log(Hook.result);
                    showAlertGeneral('<?php _e('Attention','tainacan') ?>', Hook.result.message, 'info');
                    return false;
                }
            }
            
            //FIM DA VERIFICACAO 
            $('#modalImportMain').modal('show');//mostro o modal de carregamento
            $('#object_classifications_edit').val(selKeys.join(", "));
            $.ajax({
                url: $('#src').val() + '/controllers/object/object_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//mostro o modal de carregamento
                elem_first = jQuery.parseJSON(result);
                 $("#tainacan-breadcrumbs").hide();
                $('#form').hide();
                 $('#main_part').show();
                $('#collection_post').show();
                $('#configuration').hide();
                $('#configuration').html('');
                $("#container_socialdb").show('slow');
                //wpquery_filter();
                wpquery_clean();
                set_containers_class($('#collection_id').val());
                //showList($('#src').val());
                $('#create_button').show();
                $('#menu_object').show();
                try {
                      $("#dynatree").dynatree("getTree").reload();
                }catch(err) {
                    console.log('No dynatree found!');
                }
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                $('.dropdown-toggle').dropdown();
                $('.nav-tabs').tab();
            });


            e.preventDefault();
        });
         <?php if(!isset($is_view_mode)): ?>
        var myDropzone = new Dropzone("div#dropzone_edit", {
            accept: function(file, done) {
                    if (file.type === ".exe") {
                        done("Error! Files of this type are not accepted");
                    }
                    else { 
                        done(); 
                        set_attachments_valid(myDropzone.getAcceptedFiles().length);
                    }
               },
            init: function () {
                thisDropzone = this;
                this.on("removedfile", function (file) {
                    //    if (!file.serverId) { return; } // The file hasn't been uploaded
                    $.get($('#src').val() + '/controllers/object/object_controller.php?operation=delete_file&object_id=' + $("#object_id_edit").val() + '&file_name=' + file.name, function (data) {
                        set_attachments_valid(thisDropzone.getAcceptedFiles().length);
                        if (data.trim() === 'false') {
                            showAlertGeneral('<?php _e("Atention!", 'tainacan') ?>', '<?php _e("An error ocurred, File already removed or corrupted!", 'tainacan') ?>', 'error');
                        } else {
                            showAlertGeneral('<?php _e("Success", 'tainacan') ?>', '<?php _e("File removed!", 'tainacan') ?>', 'success');
                        }
                    }); // Send the file id along
                });
                this.on("addedfile", function () {
                    $('.tainacan-image-legend').modal('show');
                });
                $.get($('#src').val() + '/controllers/object/object_controller.php?operation=list_files&object_id=' + $("#object_id_edit").val(), function (data) {
                    try {
                        var count = 0
                        $.each(data, function (key, value) {
                            if (value.name !== undefined && value.name !== 0) {
                                var mockFile = {name: value.name, size: value.size};
                                thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                                count++;
                            }
                        });
                        set_attachments_valid(count);
                    }
                    catch (e) {
                    }  // handle error
                });
            },
            url: $('#src').val() + '/controllers/object/object_controller.php?operation=save_file&object_id=' + $("#object_id_edit").val(),
            addRemoveLinks: true

        });
        <?php endif; ?>

        //upload file limit
        $("#object_file").on("change", function (e) {
            //check whether browser fully supports all File API
            console.log($('#object_file')[0].files);
            if (window.File && window.FileReader && window.FileList && window.Blob && $('#object_file')[0].files.length>0)
            {
                //get the file size and file type from file input field
                var fsize = $('#object_file')[0].files[0].size;
                var server_size = '<?php echo file_upload_max_size(); ?>';
                if (fsize > parseFloat(server_size)) //do something if file size more than 1 mb (1048576)
                {
                    showAlertGeneral('<?php _e('Attention!', 'tainacan') ?>', '<?php _e('This file is too big, the file limit size of this server is ', 'tainacan') ?>' + bytesToSize(server_size), 'error');
                    // alert(fsize +" bites\nToo big!");
                    $('#object_file').val('');
                }
            }
        });
        
         //autocomplete para o titulo no caso maskara
        if($('.title_mask').val()!==''){
            $("#object_name").autocomplete({
                source: $('#src').val() + '/controllers/object/object_controller.php?operation=search-items&collection_id='+$('#collection_id').val(),
                messages: {
                    noResults: '',
                    results: function () {
                    }
                },
                response: function( event, ui ) {
                    if(ui.content && ui.content.length>0){
                       $.each(ui.content,function(index,value){
                           if(value.item_id && value.item_id == $("#object_id_edit").val()){
                               $("#object_name").autocomplete('close');
                               return true;
                           }
                           
                           if(($(event.target).val().trim()==value.value || $(event.target).val().toLowerCase().trim()==value.value.toLowerCase().trim())){
                                toastr.error($(event.target).val()+' <?php _e(' is already inserted!', 'tainacan') ?>', '<?php _e('Attention!', 'tainacan') ?>', {positionClass: 'toast-bottom-right'});
                                $(event.target).val('');
                                $("#object_name").trigger('keyup');
                           }
                           $("#object_name").autocomplete('close');
                       }); 
                    }
                },
                minLength: 2,
                select: function (event, ui) {
                    $("#object_name").val('');
                    $(event.target).html(''); 
                    $(event.target).val('');
                    toastr.error(ui.item.value+' <?php _e(' is already inserted!', 'tainacan') ?>', '<?php _e('Attention!', 'tainacan') ?>', {positionClass: 'toast-bottom-right'});
                    return false;
                }
            });
            $("#object_name").hover(function(){
                $("#object_name").trigger('keyup');
            });
            $("#object_name").change(function(){
                $("#object_name").trigger('keyup');
            });
        }
    });
    // verifica se exite uma ordencao pre definida
    function reorder_properties_edit_item(tab_id,array_ids,seletor){
        if(!seletor){
            seletor = "#accordeon-"+tab_id;
        }
        var $ul = $(seletor),
        $items = $(seletor).children();
        $properties = $("#show_form_properties_edit").children();
        $rankings = $("#update_list_ranking_<?php echo $object->ID ?>").children();
      //  $("#text_accordion").html('');
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
             for(var j = 0; j<$properties.length;j++){
                 if($($properties.get(j)).attr('id')===array_ids[i]){
                      $( $properties.get(j) ).appendTo( $ul);
                 }
             }
             for(var j = 0; j<$items.length;j++){
                 if($($rankings.get(j)).attr('id')===array_ids[i]){
                     $( $rankings.get(j) ).appendTo( $ul);
                 }
             }
        }
        $($ul).accordion({
            active: false,
            collapsible: true,
            header: "h2",
            heightStyle: "content",
             beforeActivate: function(event, ui) {
                            // The accordion believes a panel is being opened
                           if (ui.newHeader[0]) {
                               var currHeader  = ui.newHeader;
                               var currContent = currHeader.next('.ui-accordion-content');
                            // The accordion believes a panel is being closed
                           } else {
                               var currHeader  = ui.oldHeader;
                               var currContent = currHeader.next('.ui-accordion-content');
                           }
                            // Since we've changed the default behavior, this detects the actual status
                           var isPanelSelected = currHeader.attr('aria-selected') == 'true';

                            // Toggle the panel's header
                           currHeader.toggleClass('ui-corner-all',isPanelSelected).toggleClass('accordion-header-active ui-state-active ui-corner-top',!isPanelSelected).attr('aria-selected',((!isPanelSelected).toString()));

                           // Toggle the panel's icon
                           currHeader.children('.ui-icon').toggleClass('ui-icon-triangle-1-e',isPanelSelected).toggleClass('ui-icon-triangle-1-s',!isPanelSelected);

                            // Toggle the panel's content
                           currContent.toggleClass('accordion-content-active',!isPanelSelected)    
                           if (isPanelSelected) { currContent.slideUp(); }  else { currContent.slideDown(); }

                           return false; // Cancels the default action
                       }
        });
      $('[data-toggle="tooltip"]').tooltip();
    }
    
     function show_message(){
        showAlertGeneral('<?php _e('Attention!','tainacan') ?>','<?php _e('There are required fields not filled!','tainacan') ?>','info');
    }
    
    function setImageLegend() {
        var image_legend = $('#image_legend').val();
        var post_parent = $('#object_id_edit').val();
        var params = 'operation=get_last_attachment&post_parent=' + post_parent + '&post_content=' + image_legend;
        $.get($('#src').val() + '/controllers/object/object_controller.php?' + params, function () {
        });
        $('.tainacan-image-legend').modal('toggle');
    }

    function show_object_properties_edit() {
        var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function (node) {
            return node.data.key;
        });
        //var selectedCategories = selKeys.join(",");
        var selectedCategories = '';
        var promisse;
        promisse = $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {<?php echo ($is_view_mode)?'is_view_mode:true,':'' ?>operation: 'list_properties_edit_accordeon', object_id: $("#object_id_edit").val(), collection_id: $("#collection_id").val(), categories: selectedCategories}
        });
        promisse.done(function (result) {
            // $('html, body').animate({
            //     scrollTop: parseInt($("#wpadminbar").offset().top)
            // }, 900);  
            $('#show_form_properties_edit').html(result);
        });
        return promisse;
    }

    function show_collection_licenses() {
        var promisse;
        promisse = $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {<?php echo ($is_view_mode)?'is_view_mode:true,':'' ?>operation: 'show_collection_licenses', object_id: $("#object_id_edit").val(), collection_id: $("#collection_id").val()}
        });
        promisse.done(function (result) {
            // $('html, body').animate({
            //     scrollTop: parseInt($("#wpadminbar").offset().top)
            //  }, 900);  
            $('#show_form_licenses').html(result);
        });
        return promisse;
    }

    function back_main_list() {
        $('#form').hide();
        $("#tainacan-breadcrumbs").hide();
        $('#configuration').hide();
        $('#main_part').show();
        $('#display_view_main_page').show();
        $("#container_three_columns").removeClass('white-background');
        $('#menu_object').show();
        window.history.pushState('forward', null, $('#route_blog').val()+$('#slug_collection').val()+'/');
        //remove o checkout in
        //if(!id){
            id = '';
        //}
//        $.ajax({
//            url: $('#src').val() + '/controllers/object/object_controller.php',
//            type: 'POST',
//            data: {operation: 'check-in', value: '', object_id: id}
//        })
    }
    function import_object_edit() {
        var url = $('#url_object_edit').val();
        var key = $('#socialdb_embed_api_id').val();
        var ajaxurl = 'http://api.embed.ly/1/oembed?key=:' + key + '&url=' + url;
        //div loader
        $('#loading').css({
            width: $(document).width(),
            height: $(document).height(),
            background: $('#src').val() + '/libraries/images/catalogo_loader_725.gif'
        });
        $('#loading').fadeIn(1000);
        $('#loading').fadeTo("slow", 0.8);
        $.getJSON(ajaxurl, {}, function (json) {
            console.log(json);
            var description = '', title = '';
            if (json.title !== undefined && json.title != null && json.title != false) {
                title = json.title;
            }
            else {
                $('#loading').hide();
                showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('This URL not contains availables items for importation', 'tainacan') ?>', 'error');
                return;
            }
            // se nao tiver descricao ele coloca o titulo na descricao
            if (json.description !== undefined && json.description != null && json.description != false) {
                description += json.description;
            }
            else {
                description = title;
            }
            //concatena o html na descricao
            if (json.html !== undefined && json.html != null && json.html != false) {
                description += json.html;
            }
            // limpando o formulario do modal de insercao
            $('#thumbnail_url_edit').html('');
            $('#object_name_edit').val('');
            //pegando a imagem
            var img = json.thumbnail_url;
            var html = '';
            $('#thumbnail_url_edit').val(img);
            // verifico se existe imagem para ser importada
            if (json.thumbnail_url !== undefined && json.thumbnail_url != null && json.thumbnail_url != false) {
                html += "<img id='thumbnail' src='" + img + "' style='cursor: pointer; max-width: 170px;' />&nbsp&nbsp";
                $('#image_side_edit_object').html(html);
                $('#existent_thumbnail').hide();
            }
            $('#object_name_edit').val(title);
            CKEDITOR.instances.objectedit_editor.setData(description);
            $('#loading').hide('slow');

        }).fail(function (result) {
            console.log('error', result, url);
            $('#loading').hide();
            showAlertGeneral('Atenção', 'URL inexistente ou indisponível', 'error');
        });
    }
//funcoes que mostram a visualizacao do item
    function edit_show_other_type_field(field) {
        if ($(field).val() === 'other') {
            //$('#object_type_other').attr('required', 'required');
            $('#object_type_other').show('slow');
        } else {
           // $('#object_type_other').removeAttr("required");
            $('#object_type_other').hide('slow');
        }
        if ($(field).val() !== 'text') {
            $('#url_object_edit').val('');
            $('#object_url_others_input').val('');
            $('#external_option').attr('checked', 'checked'); // se for externo o default e url
            $('#object_content_text_edit').hide();// ckeditor apenas para texto
            $('#object_url_text').hide();// esconde o campo de url para textos
           // $('#url_object').removeAttr("required");//retiro o campo de requirido deste input para urls que sejam do item do tipo texto
            $('#object_url_others').show('slow');// o campo para colocar a url do item sem ser texto
            //$('#object_url_others').attr('required', 'required');
            $('#object_file').hide();// esconde a submissao de items tipo arquivo
           // $('#object_file').removeAttr("required");// retiro o campo de requirido do arquivo
        } else {
            CKEDITOR.instances.objectedit_editor.setData('');
            $('#object_url_others_input').val('');
            $('#url_object_edit').val('');
            $('#object_file').hide();// esconde a submissao de items tipo arquivo
            //$('#object_file').removeAttr("required");// retiro o campo de requirido do arquivo
            $('#internal_option').attr('checked', 'checked');
            $('#object_url_text').hide();// escondo o campo de colocar url para textos
            //$('#url_object').removeAttr("required");//retiro o campo de requirido deste input para urls que sejam do item do tipo texto
            $('#object_url_others').hide();// escondo o campo de colocar a url para tipos de arquivo que nao seja texto
           // $('#object_url_others').removeAttr("required");//retiro o campo de requirido deste input para urls que nao seja do item do tipo texto
            $('#object_content_text_edit').show();
        }
        //retirando o thumbnail
//    if($(field).val()==='image'){
//        $('#thumbnail-idea-form-edit').hide();
//    }else{
//        $('#thumbnail-idea-form-edit').show();
//    }
    }
    // text functions
    function edit_toggle_from(field) {
        if ($(field).val() === 'external') {
            if ($('input[name=object_type]:checked', '#submit_form_edit_object').val() === 'text') {
                $('#object_url_others').hide();// o campo url para outros tipos 
               // $('#object_url_others').removeAttr("required");//retiro o campo de requirido deste input para urls que nao seja do item do tipo texto
                $('#object_url_text').show('slow');// o campo url para text
             //   $('#url_object').attr('required', 'required');// coloco o campo de url para arquivos que nao seja texto como obrigatorio
                $('#object_file').hide('slow'); // escondo o campo para pegar arquivos internos
               // $('#object_file').removeAttr("required");// retiro o campo de requirido do arquivo
            } else {
                $('#object_file').hide();
               // $('#object_file').removeAttr("required");
                $('#object_url_text').hide('slow');// escondo o campo  de url para textos ja que o conteudo sera escrito dentro do ckeditor
                $('#url_object').removeAttr("required");//retiro o campo de requirido deste input para urls que sejam do item do tipo texto
                $('#object_url_others').show('slow');
               // $('#object_url_others').attr('required', 'required');// coloco o campo de url para arquivos que nao seja texto como obrigatorio
            }
        } else {
            if ($('input[name=object_type]:checked', '#submit_form_edit_object').val() === 'text') {
                $('#object_file').hide(); // escondo o campo de upload de arquivos
              //  $('#object_file').removeAttr("required");// retiro o campo de requirido do arquivo
                $('#object_url_others').hide();//escondo o input para urls para tipos que nao seja texto
                $('#object_url_others').removeAttr("required");//retiro o campo de requirido deste input para urls que nao seja do item do tipo texto
                $('#object_url_text').hide('slow');// escondo o campo  de url para textos ja que o conteudo sera escrito dentro do ckeditor
               // $('#url_object').removeAttr("required");//retiro o campo de requirido deste input para urls que sejam do item do tipo texto
            } else {
                $('#object_url_text').hide();// escondo o campo de colocar url para textos
                $('#object_url_text').removeAttr("required");//retiro o campo de requirido deste input para urls que sejam do item do tipo texto
                $('#object_url_others').hide();// escondo o campo de colocar a url para tipos de arquivo que nao seja texto
                $('#url_object').removeAttr("required");//retiro o campo de requirido deste input para urls que nao seja do item do tipo texto
                $('#object_file').show('slow'); // mostra o campo de submissao de arquivo
                //$('#object_file').attr('required', 'required');// coloco o campo de upload de arquivo como obrigatorio
            }
        }
    }


    //se mudar a url
    function edit_set_source(field) {
        $('#object_source').val($(field).val());
    }

    //BEGIN: funcao para mostrar votacoes
    function list_ranking_update(id) {
        var promisse;
        promisse = $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'update_list_ranking_object', object_id: id}
        });
        promisse.done(function (result) {
            $('#update_list_ranking_' + id).html(result);
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
        return promisse;
    }
//END

    function update_items_legends() {
        $('.image-legend').each(function(idx, el) {
            var item_legend = $(el).val();
            var item_id = $(el).attr('id').replace("legend-", "");
            $.ajax({
                type: "POST",
                url: $('#src').val() + '/controllers/object/object_controller.php',
                data: {operation: 'update_attachment_legend', item_id: item_id, item_legend: item_legend }
            }).success(function() {
                $('.ok-legend').show();
            }).error(function() {
                $('.error-legend').show();
            });
        });
    }

    function show_legends_box() {
        $('.legends-box').fadeIn();
    }

</script>
