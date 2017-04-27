<script> 
//script que inicializa uma pagina gerada pelo cache
//#1 - SCRIPTS INICIAIS
//#2 - TABS
//#3 - RANKINGS
//
//
//
 //########################### #1 SCRIPTS INICIAIS #################################
$(function(){
    $('#object_id_add').val($('#temporary_id_item').val());
    //1 - inicializo as tabs
    $('.tabs').tab();
    //2 - ativo os tootips
    $('[data-toggle="tooltip"]').tooltip();
    //3 - ativo as tabs
    list_tabs();
    //4 - ckeditor para o conteudo do item
    showCKEditor('object_editor'); 
    //5 - O submit do form
    var src = $('#src').val();
    $( '#submit_form' ).submit( function( e ) {
       var verify =  $( this ).serializeArray();
        //hook para validacao do formulario
       $("#object_content").val(CKEDITOR.instances.object_editor.getData()); 
       var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function(node) {
                    return node.data.key;
       });
       $('#object_classifications').val(selKeys.join(", ")); 
       $('#modalImportMain').modal('show');//mostro o modal de carregamento
       $.ajax( {
              url: src+'/controllers/object/object_controller.php',
              type: 'POST',
              data: new FormData( this ),
              processData: false,
              contentType: false
            } ).done(function( result ) {
                    $('#modalImportMain').modal('hide');//mostro o modal de carregamento
                    elem_first =jQuery.parseJSON(result); 
                    if(!elem_first){
                         showAlertGeneral('Attention!', 'Invalid submission, file is too big!', 'error');
                    }
                    else if(elem_first.validation_error){
                        showAlertGeneral(elem_first.title, elem_first.msg, 'error');
                    }else{
                         $("#tainacan-breadcrumbs").hide();
                        $('#form').hide();
                         $('#main_part').show();
                        $('#collection_post').show();
                        $('#configuration').hide();
                        $('#configuration').html('');
                        //$("#dynatree").dynatree("getTree").reload();
                        //showList(src);
                        //wpquery_filter();
                        wpquery_clean();
                        set_containers_class($('#collection_id').val());
                        $("#container_socialdb").show('slow');
                        $('#create_button').show();
                        $('#menu_object').show();
                        showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                    }
                    
            }); 
            e.preventDefault();
    });
    
    
    var myDropzone = new Dropzone("div#dropzone_new", {
                accept: function(file, done) {
                      if (file.type === ".exe") {
                          done("Error! Files of this type are not accepted");
                      }
                      else { done(); }
                },
                init: function () {
                    thisDropzone = this;
                    this.on("removedfile", function (file) {
                        //    if (!file.serverId) { return; } // The file hasn't been uploaded
                        $.get($('#src').val() + '/controllers/object/object_controller.php?operation=delete_file&object_id=' + $("#object_id_add").val() + '&file_name=' + file.name, function (data) {
                            if (data.trim() === 'false') {
                                showAlertGeneral("Atention!", "An error ocurred, File already removed or corrupted!", 'error');
                            } else {
                                showAlertGeneral("Success", "File removed!", 'success');
                            }
                        }); // Send the file id along
                    });
                    $.get($('#src').val() + '/controllers/object/object_controller.php?operation=list_files&object_id=' + $("#object_id_add").val(), function (data) {
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
                url: $('#src').val() + '/controllers/object/object_controller.php?operation=save_file&object_id=' + $("#object_id_add").val(),
                addRemoveLinks: true

            });
            //upload file limit
            $("#object_file").on("change", function (e) {
                //check whether browser fully supports all File API
                if (window.File && window.FileReader && window.FileList && window.Blob)
                {
                    //get the file size and file type from file input field
                    var fsize = $('#object_file')[0].files[0].size;
                    var server_size = '50000000';
                    if (fsize > parseFloat(server_size)) //do something if file size more than 1 mb (1048576)
                    {
                        showAlertGeneral('Attention!', 'This file is too big, the file limit size of this server is ' + bytesToSize(server_size), 'error');
                        // alert(fsize +" bites\nToo big!");
                        $('#object_file').val('');
                    }
                }
            });      
    if($("#text_accordion")){
        $("#text_accordion").accordion({
            active: false,
            collapsible: true,
            header: "h2",
            heightStyle: "content"
        });
    }
});

//funcao responsavel em listar as abas nos selects
function list_tabs(){
    $.ajax({
        url: $('#src').val() + '/controllers/collection/collection_controller.php',
        type: 'POST',
        data: {
            collection_id: $("#collection_id").val(), 
            operation: 'get_tabs'}
    }).done(function (result) {
        hide_modal_main();
        initiate_accordeon('default');
        var json = jQuery.parseJSON(result);
        if(json.array.length>0){
            $.each(json.array,function(index,value){
                initiate_accordeon(value.meta_id);
            });
        }
        $('#tabs_item a').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
        });
    });
}
//inicia o accordeon de cada aba criada
function initiate_accordeon(id){
    $("#accordeon-"+id).accordion({
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
}


//BEGIN: funcao para mostrar votacoes
    function list_ranking_create(id) {
        var promisse;
        promisse =$.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'create_list_ranking_object', object_id: id}
        });
        promisse.done(function (result) {
            $('#create_list_ranking_' + id).html(result);
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab(); 
        });
        return promisse;
    }
//END

function show_object_properties(){
    var promisse;
    var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function(node) {
                            return node.data.key;
    });
    var selectedCategories = selKeys.join(",");
    promisse = $.ajax( {
        url: $('#src').val()+'/controllers/object/object_controller.php',
        type: 'POST',
        data: {operation: 'show_object_properties_accordion',object_id:$("#object_id_add").val(),collection_id:$("#create_object_collection_id").val(),categories:selectedCategories}
      } );
    promisse.done(function( result ) {
            $('#show_form_properties').html(result);
    });
    return promisse;
}

function show_collection_licenses(){
    var promisse;
    promisse = $.ajax( {
        url: $('#src').val()+'/controllers/object/object_controller.php',
        type: 'POST',
        data: {operation: 'show_collection_licenses',collection_id:$("#create_object_collection_id").val()}
      } );
      
    promisse.done(function( result ) {
        //$('html, body').animate({
           ///  scrollTop: parseInt($("#wpadminbar").offset().top)
           // }, 900);       
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
        $.ajax( {
            url: $('#src').val()+'/controllers/object/object_controller.php',
            type: 'POST',
            data: {operation: 'delete_temporary_object',ID:$("#object_id_add").val()}
        } ).done(function( result ) {
            // $('html, body').animate({
             //   scrollTop: parseInt($("#wpadminbar").offset().top)
           // }, 900);  
        });
    }
 
 function import_object(){
    show_modal_main(); 
    var url = String($('#url_object').val());
    var key = $('#socialdb_embed_api_id').val();
    if(url.search('youtube.com')>=0){
         $('#object_content_text').hide();// ckeditor apenas para texto
         $('#object_url_text').hide();// esconde o campo de url para textos
         $('#object_url_others').show('slow');// o campo para colocar a url do item sem ser texto
         $('#object_url_others_input').val(url);
         console.log($('#object_url_others').val());
         $('#video_type').attr('checked','checked');
         return false;
    }
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
        hide_modal_main();
        var description = '', title = '';
        if (json.title !== undefined && json.title != null && json.title != false) {
            title = json.title;
        }
        else {
            $('#loading').hide();
            showAlertGeneral('Attention', 'This URL not contains availables items for importation', 'error');
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
            json.html = json.html.replace('width="854"', 'width="200"');
            json.html = json.html.replace('height="480"', 'height="200"');
            description = json.html + description;
        }
        // limpando o formulario do modal de insercao
        $('#thumbnail_url').html('');
        $('#title_insert_object_url').val('');
        $('#description_insert_object_url').val('');
        //pegando a imagem
        var img = json.thumbnail_url;
        var html = '';
        $('#thumbnail_url').val(img);
        // verifico se existe imagem para ser importada
        if (json.thumbnail_url !== undefined && json.thumbnail_url != null && json.thumbnail_url != false) {
            html += "<img id='thumbnail' src='" + img + "' style='cursor: pointer; max-width: 170px;' />&nbsp&nbsp";
            $('#image_side_create_object').html(html);
        }
        $('#object_name').val(title);
        CKEDITOR.instances.object_editor.setData(description);
        $('#loading').hide('slow');

    }).fail(function (result) {
        console.log('error', result, url);
        $('#loading').hide();
        hide_modal_main();
        showAlertGeneral('Atenção', 'URL inexistente ou indisponível', 'error');
    });
 }
 //funcoes que mostram a visualizacao do item
 function show_other_type_field(field){
    if($(field).val()==='other'){
        $('#object_type_other').attr('required','required');
        $('#object_type_other').show('slow');
        $('#badge_helper').hide();
    }else{
        $('#object_type_other').removeAttr("required");
        $('#object_type_other').hide('slow');
        $('#badge_helper').show();
    }
    
    if($(field).val()!=='text'){
        $('#object_url').val('');
         $('#object_url_others').val('');
        $('#external_option').attr('checked','checked'); // se for externo o default e url
        $('#object_content_text').hide();// ckeditor apenas para texto
        $('#object_url_text').hide();// esconde o campo de url para textos
        $('#url_object').removeAttr("required");//retiro o campo de requirido deste input para urls que sejam do item do tipo texto
        $('#object_url_others').show('slow');// o campo para colocar a url do item sem ser texto
        $('#object_url_others').attr('required','required');
         $('#object_file').hide();// esconde a submissao de items tipo arquivo
         $('#object_file').removeAttr("required");// retiro o campo de requirido do arquivo
    }else{
        $('#badge_helper').hide();// esconde o helper
        $('#object_url_others').val('');
         $('#object_file').hide();// esconde a submissao de items tipo arquivo
         $('#object_file').removeAttr("required");// retiro o campo de requirido do arquivo
         $('#internal_option').attr('checked','checked');
         $('#object_url_text').hide();// escondo o campo de colocar url para textos
         $('#url_object').removeAttr("required");//retiro o campo de requirido deste input para urls que sejam do item do tipo texto
         $('#object_url_others').hide();// escondo o campo de colocar a url para tipos de arquivo que nao seja texto
         $('#object_url_others').removeAttr("required");//retiro o campo de requirido deste input para urls que nao seja do item do tipo texto
         $('#object_content_text').show();
    }
    //retirando o thumbnail
    if($(field).val()==='image'){
        $('#thumbnail-idea-form').hide();
    }else{
        $('#thumbnail-idea-form').show();
    }
 }
 // text functions
  function toggle_from(field){
    if($(field).val()==='external'){
        if($('input[name=object_type]:checked', '#submit_form').val()==='text'){
            $('#object_url_others').hide();// o campo url para outros tipos 
            $('#object_url_others').removeAttr("required");//retiro o campo de requirido deste input para urls que nao seja do item do tipo texto
            $('#object_url_text').show('slow');// o campo url para text
            $('#url_object').attr('required','required');// coloco o campo de url para arquivos que nao seja texto como obrigatorio
            $('#object_file').hide('slow'); // escondo o campo para pegar arquivos internos
            $('#object_file').removeAttr("required");// retiro o campo de requirido do arquivo
        }else{
            $('#object_file').hide(); 
            $('#object_file').removeAttr("required");
            $('#object_url_text').hide('slow');// escondo o campo  de url para textos ja que o conteudo sera escrito dentro do ckeditor
            $('#url_object').removeAttr("required");//retiro o campo de requirido deste input para urls que sejam do item do tipo texto
            $('#object_url_others').show('slow'); 
            $('#object_url_others').attr('required','required');// coloco o campo de url para arquivos que nao seja texto como obrigatorio
        }
    }else{
        if($('input[name=object_type]:checked', '#submit_form').val()==='text'){
            $('#object_file').hide(); // escondo o campo de upload de arquivos
            $('#object_file').removeAttr("required");// retiro o campo de requirido do arquivo
            $('#object_url_others').hide();//escondo o input para urls para tipos que nao seja texto
            $('#object_url_others').removeAttr("required");//retiro o campo de requirido deste input para urls que nao seja do item do tipo texto
            $('#object_url_text').hide('slow');// escondo o campo  de url para textos ja que o conteudo sera escrito dentro do ckeditor
            $('#url_object').removeAttr("required");//retiro o campo de requirido deste input para urls que sejam do item do tipo texto
        }else{
            $('#object_url_text').hide();// escondo o campo de colocar url para textos
            $('#object_url_text').removeAttr("required");//retiro o campo de requirido deste input para urls que sejam do item do tipo texto
            $('#object_url_others').hide();// escondo o campo de colocar a url para tipos de arquivo que nao seja texto
            $('#url_object').removeAttr("required");//retiro o campo de requirido deste input para urls que nao seja do item do tipo texto
            $('#object_file').show('slow'); // mostra o campo de submissao de arquivo
            $('#object_file').attr('required','required');// coloco o campo de upload de arquivo como obrigatorio
        }
    }
 }
 //se mudar a url
 function set_source(field){
      $('#object_source').val($(field).val()); 
 }
 //########################### #2 TABS #########################################
 //click toggle
    function open_accordeon(id){
        if( $('#tab-'+id+' .ui-accordion-content').is(':visible')){
            $('#tab-'+id).find("div.action-text").text('Expandir todos');
            $('#tab-'+id+' .ui-accordion-content').fadeOut();
            $('.cloud_label').click();
        }else{
            $('#tab-'+id+' .ui-accordion-content').fadeIn();
            $('.cloud_label').click();
            $('#tab-'+id).find("div.action-text").text('Retrair tudo');
        }
    }
  //########################### #3 RANKINGS ####################################
  $(function () {
        var src = $('#src').val();
        var object_id = $('#object_id_add').val();
        if ($('#create_stars_id_' + object_id).val()) {
            stars = $('#create_stars_id_' + object_id).val().split(',');
            $.each(stars, function (idx, elem) {
                $('#create_rating_' + object_id + '_' + elem).raty({
                    score: $('#create_star_' + object_id + '_' + elem).val(),
                    half: true,
                    starType: 'i',
                    click: function (score, evt) {
                        create_save_vote_stars(score, elem, object_id)
                        return false;
                    }
                });
            });
        }
       
       if($('.hide_rankings')&&$('.hide_rankings').val()==='true'){
            $('#list_ranking_items').hide();
        }

    });
    function create_save_vote_stars(score, property_id, object_id) {
        $.ajax({
            url: $('#src').val() + '/controllers/ranking/ranking_controller.php',
            type: 'POST',
            data: {
                operation: 'save_vote_stars',
                score:score*2,
                property_id:property_id,
                object_id:object_id, 
                collection_id: $("#collection_id").val()
            }
        }).done(function (result) {
             elem_first =jQuery.parseJSON(result); 
             console.log(elem_first);
             $('#create_rating_' + object_id + '_' + property_id).raty({
                    score: Math.ceil((elem_first.results.final_score*2))/2,
                    half: true,
                    starType: 'i',
                    click: function (score, evt) {
                        create_save_vote_stars(score, property_id, object_id)
                        return false;
                    }
                });   
            $('#create_counter_' + object_id + '_' + property_id).text(elem_first.results.count);
            if(elem_first.is_user_logged_in){
                score = Math.ceil((score*2))/2;
                elem_first.results.final_score = Math.ceil((elem_first.results.final_score*2))/2;
                if(elem_first.is_new){
                  showAlertGeneral('Voto realizado com sucesso!', elem_first.msg, 'success');
                }else{
                  showAlertGeneral('Voto atualizado com sucesso!',  elem_first.msg, 'info');
                }
            }else{
                 showAlertGeneral('Aten&ccedil;&atilde;o', 'Por favor autentique-se primeiro!', 'error');
            }
        });
    }
    function create_save_vote_like(property_id, object_id) {
        $.ajax({
            url: $('#src').val() + '/controllers/ranking/ranking_controller.php',
            type: 'POST',
            data: {
                operation: 'save_vote_like',
                score: 1,
                property_id:property_id,
                object_id:object_id, 
                collection_id: $("#collection_id").val()
            }
        }).done(function (result) {
            elem_first =jQuery.parseJSON(result); 
            $("#create_like_"+object_id+"_"+property_id).val(elem_first.results.final_score); 
            $("#create_counter_"+object_id+"_"+property_id).text(elem_first.results.final_score); 
            if(elem_first.is_user_logged_in){
                if(elem_first.is_new){
                  showAlertGeneral('Voto realizado com sucesso!', elem_first.msg, 'success');
                }else{
                  showAlertGeneral('Voto atualizado com sucesso!',  elem_first.msg, 'info');
                }
            }else{
                 showAlertGeneral('Aten&ccedil;&atilde;o', 'Por favor autentique-se primeiro!', 'error');
            }
        });
    }
    function create_save_vote_binary_up(property_id, object_id) {
        $.ajax({
            url: $('#src').val() + '/controllers/ranking/ranking_controller.php',
            type: 'POST',
            data: {
                operation: 'save_vote_binary',
                score:1,
                property_id:property_id,
                object_id:object_id, 
                collection_id: $("#collection_id").val()
            }
        }).done(function (result) {
            elem_first =jQuery.parseJSON(result); 
            $("#create_counter_up_"+object_id+"_"+property_id).text(elem_first.results.final_up);
            $("#create_counter_down_"+object_id+"_"+property_id).text(elem_first.results.final_down); 
            $("#create_score_"+object_id+"_"+property_id).text(elem_first.results.final_score);
            if(elem_first.is_user_logged_in){
                if(elem_first.is_new){
                  showAlertGeneral('Voto realizado com sucesso!', 'Seu voto foi computado!', 'success');
                }else{
                  showAlertGeneral('Aten&ccedil;&atilde;o', 'Voc&ecirc; j&aacute; curtiu esse item!', 'info');
                }
            }else{
                 showAlertGeneral('Aten&ccedil;&atilde;o', 'Por favor autentique-se primeiro!', 'error');
            }
        });
    }
    
    function create_save_vote_binary_down(property_id, object_id) {
        $.ajax({
            url: $('#src').val() + '/controllers/ranking/ranking_controller.php',
            type: 'POST',
            data: {
                operation: 'save_vote_binary',
                score: -1,
                property_id:property_id,
                object_id:object_id, 
                collection_id: $("#collection_id").val()
            }
        }).done(function (result) {
            elem_first =jQuery.parseJSON(result); 
            $("#create_counter_up_"+object_id+"_"+property_id).text(elem_first.results.final_up);
            $("#create_counter_down_"+object_id+"_"+property_id).text(elem_first.results.final_down); 
            $("#create_score_"+object_id+"_"+property_id).text(elem_first.results.final_score);
            if(elem_first.is_user_logged_in){
                if(elem_first.is_new){
                  showAlertGeneral('Voto realizado com sucesso!', 'Seu voto foi computado!', 'success');
                }else{
                  showAlertGeneral('Aten&ccedil;&atilde;o', 'Voc&ecirc; j&aacute; curtiu esse item!', 'info');
                }
            }else{
                 showAlertGeneral('Aten&ccedil;&atilde;o', 'Por favor autentique-se primeiro!', 'error');
            }
        });
    }
//############################### #4 - PROPRIEDADES ############################
    var checkbox_values = [];
    $(function () {
        var src = $('#src').val();
        var properties_autocomplete = get_val($("#properties_autocomplete").val());
        autocomplete_property_data(properties_autocomplete);
        if($('.hide_rankings')&&$('.hide_rankings').val()==='true'){
            $('#list_ranking_items').hide();
        }
        if($('.hide_license')&&$('.hide_license').val()==='true'){
            $('#list_licenses_items').hide();
            $('#core_validation_license').val('true');
        }else{
            $('input:radio[name="object_license"]').change(function(){
                $('#core_validation_license').val('true');
                set_field_valid('license','core_validation_license');
            });
        }
        if($('#tabs_properties').length==0){
            $('.expand-all-item').trigger('click');
        }
        // # - inicializa o campos das propriedades de termo
        init_metadata_date( ".input_date" );
        list_properties_term_insert_objects();
        validate_all_fields();
    });


    function autocomplete_object_property_add(property_id, object_id) {
        $("#autocomplete_value_" + property_id + "_" + object_id).autocomplete({
            source: $('#src').val() + '/controllers/object/object_controller.php?operation=get_objects_by_property_json&property_id=' + property_id,
            messages: {
                noResults: '',
                results: function () {
                }
            },
            minLength: 2,
            select: function (event, ui) {
                console.log(event);
                $("#autocomplete_value_" + property_id + "_" + object_id).html('');
                $("#autocomplete_value_" + property_id + "_" + object_id).val('');
                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                var temp = $("#property_value_" + property_id + "_" + object_id + " [value='" + ui.item.value + "']").val();
                if (typeof temp == "undefined") {
                    var already_selected = false;
                    //validacao do campo
                    $('#core_validation_'+property_id).val('true');
                    set_field_valid(property_id,'core_validation_'+property_id);
                    //fim validacao do campo
                    $("#property_value_" + property_id + "_" + object_id + "_add option").each(function () {
                        if ($(this).val() == ui.item.value) {
                            already_selected = true;
                        }
                    });
                    if (!already_selected) {
                        if($('#cardinality_'+property_id + "_" + object_id).val()=='1'){
                             $("#property_value_" + property_id + "_" + object_id + "_add").html('');
                        }
                        $("#property_value_" + property_id + "_" + object_id + "_add").append("<option class='selected' value='" + ui.item.value + "' selected='selected' >" + ui.item.label + "</option>");
                        if (Hook.is_register('tainacan_validate_cardinality_onselect')) {
                            Hook.call('tainacan_validate_cardinality_onselect', ['select[name="socialdb_property_' + property_id + '[]"]', property_id]);
                        }
                    }
                }
                setTimeout(function () {
                    $("#autocomplete_value_" + property_id + "_" + object_id).val('');
                }, 100);
            }
        });
        
        //classe para formulario validae
    }
    /**
     * Autocomplete para os metadados de dados para insercao/edicao de item unico
     * @param {type} e
     * @returns {undefined}
     */
    function autocomplete_property_data(properties_autocomplete) {
        if (properties_autocomplete) {
            $.each(properties_autocomplete, function (idx, property_id) {
                if( $(".form_autocomplete_value_" + property_id).length==0){
                    return false;
                }
                //validate
                $(".form_autocomplete_value_" + property_id).keyup(function(){
                    var cont = 0;
                    $(".form_autocomplete_value_" + property_id).each(function(index,value){
                       if( $(this).val().trim()!==''){
                            cont++;
                        }
                    });
                    
                    if( cont===0){
                        $('#core_validation_'+property_id).val('false');
                    }else{
                         $('#core_validation_'+property_id).val('true');
                    } 
                    
                    set_field_valid(property_id,'core_validation_'+property_id);
                });
                $(".form_autocomplete_value_" + property_id).change(function(){
                    var cont = 0;
                    $(".form_autocomplete_value_" + property_id).each(function(index,value){
                       if( $(this).val().trim()!==''){
                            cont++;
                        }
                    });
                    
                    if( cont===0){
                        $('#core_validation_'+property_id).val('false');
                    }else{
                         $('#core_validation_'+property_id).val('true');
                    }
                    set_field_valid(property_id,'core_validation_'+property_id);
                });
                // end validate
                $(".form_autocomplete_value_" + property_id).autocomplete({
                    source: $('#src').val() + '/controllers/collection/collection_controller.php?operation=list_items_search_autocomplete&property_id=' + property_id,
                    messages: {
                        noResults: '',
                        results: function () {
                        }
                    },
                    minLength: 2,
                    select: function (event, ui) {
                        $("#form_autocomplete_value_" + property_id).val('');
                        //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                        var temp = $("#property_value_" + property_id).val();
                        if (typeof temp == "undefined") {
                            $("#form_autocomplete_value_" + property_id).val(ui.item.value);
                        }
                    }
                });
            });
        }
    }

    function clear_select_object_property(e, property_id, object_id) {
        $('option:selected', e).remove();
        $("#property_value_" + property_id + "_" + object_id + "_add option").each(function ()
        {
            $(this).attr('selected', 'selected');
        });
        //validacao do campo
        var cont = 0;
        $("#property_value_" + property_id + "_" + object_id + "_add option").each(function ()
        {
            cont++;
        });
        if(cont==0){
            $('#core_validation_'+property_id).val('false');
            set_field_valid(property_id,'core_validation_'+property_id);
        }            
        //fim validacao do campo
        if (Hook.is_register('tainacan_validate_cardinality_onselect')) {
            Hook.call('tainacan_validate_cardinality_onselect', ['select[name="socialdb_property_' + property_id + '[]"]', property_id]);
        }
        //$('.chosen-selected2 option').prop('selected', 'selected');
    }

//************************* properties terms ******************************************//
    function list_properties_term_insert_objects() {
        var trees = get_val($("#properties_terms_tree").val());
        var treecheckboxes = get_val($("#properties_terms_treecheckbox").val());
        list_tree(trees);
        list_treecheckboxes(treecheckboxes);
    }
    
    // treecheckboxes
    function list_treecheckboxes(treecheckboxes) {
        if (treecheckboxes) {
            $.each(treecheckboxes, function (idx, treecheckbox) {
                $("#field_property_term_" + treecheckbox).dynatree({
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
                    onPostInit: function (isReloading, isError) {
                        select_items("#field_property_term_" + tree);
                    },
                    onClick: function (node, event) {
                        // Close menu on click
                        $("#property_object_category_id").val(node.data.key);
                        $("#property_object_category_name").val(node.data.title);

                    },
                    onCreate: function (node, span) {
                         bindContextMenuSingle(span,'field_property_term_' + treecheckbox);
                        $('.dropdown-toggle').dropdown();
                    },
                    onSelect: function (flag, node) {
                        var cont = 0;
                        var selKeys = $.map(node.tree.getSelectedNodes(), function (node) {
                            return node;
                        });
                        var categories = $.map(node.tree.getSelectedNodes(), function (node) {
                            return node.data.key;
                        });
                        if(categories.length>0&&categories.indexOf(node.data.key)>=0){
                            append_category_properties(node.data.key);
                        }else{
                            append_category_properties(0,node.data.key);
                        }
                        
                        
                        $("#socialdb_propertyterm_" + treecheckbox).html('');
                        $.each(selKeys, function (index, key) {
                            cont++;
                            $("#socialdb_propertyterm_" + treecheckbox).append('<input type="hidden" name="socialdb_propertyterm_'+treecheckbox+'[]" value="' + key.data.key + '" >');
                        });
                        if(cont===0){
                            $('#core_validation_'+treecheckbox).val('false');
                            set_field_valid(treecheckbox,'core_validation_'+treecheckbox);
                         }else{
                            $('#core_validation_'+treecheckbox).val('true');
                            set_field_valid(treecheckbox,'core_validation_'+treecheckbox); 
                         }
                    }
                });
            });
        }
    }


    function select_items(dynatree){
        $(dynatree).dynatree("getRoot").visit(function(node){
            if(is_selected_category(node.data.key,'#object_classifications')){
                node.select(true);
            }
        });
    }
    // tree
    function list_tree(trees) {
        if (trees) {
            $.each(trees, function (idx, tree) {
                $("#field_property_term_" + tree).dynatree({
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
                                    //hide_checkbox: 'true',
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
                                //hide_checkbox: 'true',
                                hide_count: 'true',
                                classCss: node.data.addClass,
                                //operation: 'findDynatreeChild'
                                operation: 'expand_dynatree'
                            }
                        });
                    },
                    onCreate: function (node, span) {
                        bindContextMenuSingle(span,'field_property_term_' + tree);
                        $('.dropdown-toggle').dropdown();
                        if(is_selected_category(node.data.key,'#object_classifications')){
                            node.select(true);
                        }
                    },
                    onSelect: function (flag, node) {
                        if ($("#socialdb_propertyterm_" + tree).val() === node.data.key) {
                            append_category_properties(0,node.data.key);
                            $("#socialdb_propertyterm_" + tree).val("");
                             $('#core_validation_'+tree).val('false');
                             set_field_valid(tree,'core_validation_'+tree);
                        } else {
                            append_category_properties(node.data.key,$("#socialdb_propertyterm_" + tree).val());
                            $("#socialdb_propertyterm_" + tree).val(node.data.key);
                            $('#core_validation_'+tree).val('true');
                             set_field_valid(tree,'core_validation_'+tree);
                        }
                    }
                });
            });
        }
    }
    // get value of the property
    function get_val(value) {
        if (value === '' || value === undefined) {
            return false;
        } else if (value.split(',')[0] === '' && value !== '') {
            return [value];
        } else {
            return value.split(',');
        }
    }
//######## INSERCAO DE UM ITEM AVULSO EM UMA COLECAO #########################//    
    function add_new_item_by_title(collection_id,title,seletor,property_id,object_id){
        if(title.trim()===''){
            showAlertGeneral('Aten&ccedil;&atilde;o','T&iacute;tulo est&aacute; vazio!','info');
        }else{
            $(seletor).trigger('click');
            $('#title_'+ property_id + "_" + object_id ).val('');
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
                    $("#property_value_" + property_id + "_" + object_id + "_add").append("<option class='selected' value='" + elem_first.item.ID + "' selected='selected' >" + elem_first.item.post_title + "</option>");
                }
            });
        }
    }
//################################ adicao de propriedades de categorias #################################//    
    function append_category_properties(id,remove_id,property_id ){
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
                    remove_property_general(id);
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
                data: { operation: 'list_properties_categories_accordeon',properties_to_avoid:$('#properties_id').val(),categories: id, object_id:$('#object_id_add').val()}
            }).done(function (result) {
                hide_modal_main();
                //list_all_objects(selKeys.join(", "), $("#collection_id").val());
                $('#append_properties_categories').html(result);
                insert_html_property_category();
                
            });
            $('#selected_categories').val(selected_categories.join(','));
        }
    }
    function insert_html_property_category(){
        var flag = false;
        $ul = $("#text_accordion");
        $items = $("#text_accordion").children();
        $properties_append = $("#append_properties_categories").children();
        for (var i = 0; i <$properties_append.length; i++) {
              // index is zero-based to you have to remove one from the values in your array
                for(var j = 0; j<$items.length;j++){
                    if($($items.get(j)).attr('id')&&$($items.get(j)).attr('id')===$($properties_append.get(i)).attr('id')){
                        flag = true;
                    }
                }
                if(!flag){
                   $( $properties_append.get(i) ).appendTo( $ul);
                   var id =  $( $properties_append.get(i) ).attr('property');
                   add_property_general(id);
                }
               flag = false;
         }
         $("#text_accordion").accordion("destroy");  
         $("#text_accordion").accordion({
                    active: false,
                    collapsible: true,
                    header: "h2",
                    heightStyle: "content"
                });
         $('[data-toggle="tooltip"]').tooltip();
    }
    //adicionando as propriedades das categorias no array de propriedades gerais
    function add_property_general(id){
        var ids = $('#properties_id').val().split(','); 
        if(ids){
           ids.push(id);
        }
         $('#properties_id').val(ids.join(','));
    }
    //removendo as propriedades das categorias no array de propriedades gerais
    function remove_property_general(id){
        var ids = $('#properties_id').val().split(','); 
        var index = ids.indexOf(id);
        ids.splice(index, 1);
        $('#properties_id').val(ids.join(','));
    }
   
//################################ Inicializao de data #################################//    
    function init_metadata_date(seletor){
        $('.ui-datepicker-trigger').remove();
        $('.ui-helper-hidden-accessible').remove();
        $.each($(seletor),function(index,value){
            var id = '#'+$(value).attr('id');
            $(id).removeClass('ui-autocomplete-input');
            $(id).removeClass('hasDatepicker');
            $(id).datepicker({
                dateFormat: 'dd/mm/yy',
                dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
                dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
                dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
                monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                nextText: 'Próximo',
                prevText: 'Anterior',
                showOn: "button",
                buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
                buttonImageOnly: true
            });
        })
        
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
        if($('#socialdb_property_'+property_id+'_'+(id)).length>0)
            $('#socialdb_property_'+property_id+'_'+(id)).val('');
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
//################################ VALIDACOES#################################//
    /**
     * funcao que valida os campos radios, e realiza a insercao das propriedades de categorias
     * @param {type} property_id
     * @returns {undefined}     */
    function validate_radio(property_id){
        var selected = $("input[type='radio'][name='socialdb_propertyterm_"+property_id+"']:checked");
        if($(selected[0]).val()===$('#socialdb_propertyterm_'+property_id+'_value').val()){
            $(selected[0]).removeAttr('checked');
        }
        if (selected.length > 0) {
            append_category_properties(selected.val(), $('#socialdb_propertyterm_'+property_id+'_value').val());
            $('#socialdb_propertyterm_'+property_id+'_value').val(selected.val()); 
            $('#core_validation_'+property_id).val('true');
            set_field_valid(property_id,'core_validation_'+property_id);
        }else{
            $('#core_validation_'+property_id).val('false');
            set_field_valid(property_id,'core_validation_'+property_id);
        }
    }
    /**
     * funcao que valida o campo checkbox, e realiza a insercao das propriedades de categorias
     * @param {type} property_id
     * @returns {undefined}     */
    function validate_checkbox(property_id){
        var selected = $("input[type='checkbox'][name='socialdb_propertyterm_"+property_id+"[]']:checked");
        if (selected.length > 0) {
            $('#core_validation_'+property_id).val('true');
            set_field_valid(property_id,'core_validation_'+property_id);
        }else{
            $('#core_validation_'+property_id).val('false');
            set_field_valid(property_id,'core_validation_'+property_id);
        }
        //verificando se existe propriedades para serem  adicionadas
        $.each($("input[type='checkbox'][name='socialdb_propertyterm_"+property_id+"[]']"),function(index,value){
            if($(this).is(':checked')){
                append_category_properties($(this).val());
            }else{
                append_category_properties(0,$(this).val());
            }
        });
    }
    /**
     * funcao que valida os campos de selecao unica
     * @param {type} seletor
     * @param {type} property_id
     * @returns {undefined}     */
    function list_validate_selectbox(seletor,property_id){
        if($(seletor).val()===''){
            $('#core_validation_'+property_id).val('false');
            set_field_valid(property_id,'core_validation_'+property_id);
        }else{
            append_category_properties($(seletor).val(), $('#socialdb_propertyterm_'+property_id+'_value').val());
           $('#socialdb_propertyterm_'+property_id+'_value').val($(seletor).val()); 
            $('#core_validation_'+property_id).val('true');
            set_field_valid(property_id,'core_validation_'+property_id);
        }
        
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
            append_category_properties($(seletor).val(), $('#socialdb_propertyterm_'+property_id+'_value').val());
           $('#socialdb_propertyterm_'+property_id+'_value').val($(seletor).val()); 
            $('#core_validation_'+property_id).val('true');
            set_field_valid(property_id,'core_validation_'+property_id);
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
            $('#core_validation_'+property_id).val('true');
            set_field_valid(property_id,'core_validation_'+property_id);
            //verificando se existe propriedades para serem  adicionadas
            $.each($("#field_property_term_"+property_id+" option"),function(index,value){
                if($(this).is(':selected')){
                    append_category_properties($(this).val());
                }else{
                    append_category_properties(0,$(this).val());
                }
            });
        }else{
            $('#core_validation_'+property_id).val('false');
            set_field_valid(property_id,'core_validation_'+property_id);
        }
    }
    
    function set_field_valid(id,seletor){
        if($('#'+seletor).val()==='false'){
            $('#core_validation_'+id).val('false');
            $('#ok_field_'+id).hide();
            $('#required_field_'+id).show();
        }else{
            $('#core_validation_'+id).val('true');
            $('#ok_field_'+id).show();
            $('#required_field_'+id).hide();
        }
        validate_all_fields();
    }
    
    function validate_all_fields(){
        var cont = 0;
        $( ".core_validation").each(function( index ) {
            if($( this ).val()==='false'){
                cont++;
            }
        });
        if(cont===0){
            $('#submit_container').show();
            $('#submit_container_message').hide();
        }else{
            $('#submit_container').hide();
            $('#submit_container_message').show();
        }
    }
//############ #5 - PROPRIEDADES COMPOSTAS #####################################
var dynatree_object_index = [];
    $(function () {
        // # - autocomplete para as propriedades de dados
        var properties_autocomplete = compounds_get_val($("#properties_autocomplete").val());
        var compounds = compounds_get_val($("#properties_compounds").val()); 
        if(compounds&&compounds.length!=0){
             $.each(compounds, function (idx, compound) {
                 autocomplete_property_data_compounds(properties_autocomplete,compound)
             });
             // # - inicializa o campos das propriedades de termo compostas 
            compounds_list_properties_term_insert_objects();
        }
    });
    
    /**
     * Autocomplete para os metadados de dados para insercao/edicao de item unico
     * @param {type} e
     * @returns {undefined}
     */
    function autocomplete_property_data_compounds(properties_autocomplete,compound_id) {
        if (properties_autocomplete) {
            $.each(properties_autocomplete, function (idx, property_id) {
                if($('#cardinality_compound_'+compound_id+'_'+property_id).length>0){
                    var cardinality = $('#cardinality_compound_'+compound_id+'_'+property_id).val();
                    for(var i = 0;i<cardinality;i++){
                        dynatree_object_index["compounds_"+compound_id+"_"+ property_id  + '_' + i] = i;
                        if( $(".form_autocomplete_compounds_" + property_id + '_'+i).length==0){
                            return false;
                        }
                        //validate
                        $(".form_autocomplete_compounds_" + property_id + '_'+i).keyup(function(){
                            var cont = 0;
                            var i =  dynatree_object_index[$(this).attr('id')];
                            if( $(this).val().trim()!==''){
                                    cont++;
                            }
                            //contador
                            if( cont===0){
                                $('#core_validation_'+compound_id+'_'+property_id+'_'+i).val('false');
                                set_field_valid_compounds(property_id,'core_validation_'+compound_id+'_'+property_id+'_'+i,compound_id);
                            }else{
                                $('#core_validation_'+compound_id+'_'+property_id+'_'+i).val('true');
                                set_field_valid_compounds(property_id,'core_validation_'+compound_id+'_'+property_id+'_'+i,compound_id);
                            }
                        });
                        $(".form_autocomplete_compounds_" + property_id + '_'+i).change(function(){
                            var cont = 0;
                            var i =  dynatree_object_index[$(this).attr('id')];
                            if( $(this).val().trim()!==''){
                                cont++;
                            }

                            if( cont===0){
                                $('#core_validation_'+compound_id+'_'+property_id+'_'+i).val('false');
                                set_field_valid_compounds(property_id,'core_validation_'+compound_id+'_'+property_id+'_'+i,compound_id);
                            }else{
                                $('#core_validation_'+compound_id+'_'+property_id+'_'+i).val('true');
                                set_field_valid_compounds(property_id,'core_validation_'+compound_id+'_'+property_id+'_'+i,compound_id);
                            }
                        });
                        // end validate
                        $(".form_autocomplete_compounds_" + property_id + '_'+i).autocomplete({
                            source: $('#src').val() + '/controllers/collection/collection_controller.php?operation=list_items_search_autocomplete&property_id=' + property_id,
                            messages: {
                                noResults: '',
                                results: function () {
                                }
                            },
                            minLength: 2,
                            select: function (event, ui) {
                                var i =  dynatree_object_index[$(this).attr('id')];
                                $(".form_autocomplete_compounds_" + property_id + '_'+i).val('');
                                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                                var temp = $(".form_autocomplete_compounds_" + property_id + '_'+i).val();
                                if (typeof temp == "undefined") {
                                    $(".form_autocomplete_compounds_" + property_id + '_'+i).val(ui.item.value);
                                }
                            }
                        });
                    }
                }    
            });
        }
    }
    
    function autocomplete_object_property_compound(compound_id, property_id, object_id) {
        $("#autocomplete_value_"+compound_id+"_" + property_id + "_" + object_id).autocomplete({
            source: $('#src').val() + '/controllers/object/object_controller.php?operation=get_objects_by_property_json&property_id=' + property_id,
            messages: {
                noResults: '',
                results: function () {
                }
            },
            minLength: 2,
            select: function (event, ui) {
                $("#autocomplete_value_"+compound_id+"_" + property_id + "_" + object_id).html('');
                $("#autocomplete_value_"+compound_id+"_" + property_id + "_" + object_id).val('');
                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                var temp = $("#property_value_"+compound_id+"_" + property_id + "_" + object_id + " [value='" + ui.item.value + "']").val();
                if (typeof temp == "undefined") {
                     var already_selected = false;
                     //validacao do campo
                    $('#core_validation_'+compound_id+'_'+property_id+'_'+object_id).val('true');
                    set_field_valid_compounds(property_id,'core_validation_'+compound_id+'_'+property_id+'_'+object_id,compound_id);
                    //fim validacao do campo
                    $("#property_value_"+compound_id+"__" + property_id + "_" + object_id+"_edit option").each(function(){
                        if($(this).val()==ui.item.value){
                            already_selected = true;
                        }
                    });
                    if(!already_selected){
                        if($('#cardinality_'+compound_id+'_'+property_id + "_" + object_id).val()=='1'){
                             $("#property_value_"+compound_id+"_" + property_id + "_" + object_id + "_edit").html('');
                        }
                        $("#property_value_"+compound_id+"_" + property_id + "_" + object_id+"_edit").append("<option class='selected' value='" + ui.item.value + "' selected='selected' >" + ui.item.label + "</option>");
                        //hook para validacao do campo ao selecionar
                        if(Hook.is_register( 'tainacan_validate_cardinality_onselect')){
                            Hook.call( 'tainacan_validate_cardinality_onselect', [ 'select[name="socialdb_property_'+property_id+'[]"]',property_id ] );
                        }
                    }
                }
                setTimeout(function () {
                    $("#autocomplete_value_"+compound_id+"_" + property_id + "_" + object_id).val('');
                }, 100);
            }
        });
    }
    
    
    
     function clear_select_object_property_compound(e,compound_id,property_id,object_id) {
        $('option:selected', e).remove();
         $("#property_value_"+compound_id+"_" + property_id + "_" + object_id+"_edit option").each(function()
        {
           $(this).attr('selected','selected');
        });
        //validacao do campo
        var cont = 0;
        $("#property_value_"+compound_id+"_" + property_id + "_" + object_id + "_edit option").each(function ()
        {
            cont++;
        });
        if(cont==0){
            $('#core_validation_'+ compound_id + "_"+property_id+"_"+object_id).val('false');
            set_field_valid_compounds(property_id,'core_validation_'+compound_id+'_'+property_id+'_'+object_id,compound_id);
        }            
        //fim validacao do campo
        if(Hook.is_register( 'tainacan_validate_cardinality_onselect')){
            Hook.call( 'tainacan_validate_cardinality_onselect', [ 'select[name="socialdb_property_'+property_id+'[]"]',property_id ] );
        }
        //$('.chosen-selected2 option').prop('selected', 'selected');
    }
    //************************* properties terms ******************************************//
    function compounds_list_properties_term_insert_objects() {
        var radio_cats = ($('#properties_terms_radio').length>0) ? $("#properties_terms_radio").val() : $("#multiple_properties_terms_radio").val();
        var check_cats = ($('#properties_terms_checkbox').length>0) ? $("#properties_terms_checkbox").val() : $("#multiple_properties_terms_checkbox").val();
        var select_cats = ($('#properties_terms_selectbox').length>0) ? $("#properties_terms_selectbox").val() : $("#multiple_properties_terms_selectbox").val();
        var tree_cats = ($('#properties_terms_tree').length>0) ? $("#properties_terms_tree").val() : $("#multiple_properties_terms_tree").val();
        var treecheck_cats = ($('#properties_terms_treecheckbox').length>0) ? $("#properties_terms_treecheckbox").val() : $("#multiple_properties_terms_treecheckbox").val();
        var multiple_cats = ($('#properties_terms_multipleselect').length>0) ? $("#properties_terms_multipleselect").val() : $("#multiple_properties_terms_multipleselect").val();
        //
        var all_compounds_id = $('#properties_compounds').val().split(',');
        var categories = compounds_get_val($("#edit_object_categories_id").val());
        var radios = compounds_get_val(radio_cats);
        var selectboxes = compounds_get_val(select_cats);
        var trees = compounds_get_val(tree_cats);
        var checkboxes = compounds_get_val(check_cats);
        var multipleSelects = compounds_get_val(multiple_cats);
        var treecheckboxes = compounds_get_val(treecheck_cats);
        if (all_compounds_id&&all_compounds_id.length>0) {
            $.each(all_compounds_id, function (idx, compound_id) {
                compounds_list_radios(radios,categories,compound_id);
                compounds_list_tree(trees,categories,compound_id);
                compounds_list_selectboxes(selectboxes,categories,compound_id);
                compounds_list_multipleselectboxes(multipleSelects,categories,compound_id);
                compounds_list_checkboxes(checkboxes,categories,compound_id);
                compounds_list_treecheckboxes(treecheckboxes,categories,compound_id);
            });
        }
    }
    // radios
    function compounds_list_radios(radios,categories,compound_id) {
        if (radios) {
            $.each(radios, function (idx, radio) {
                if($('#cardinality_compound_'+compound_id+'_'+radio).length>0){
                    var cardinality = $('#cardinality_compound_'+compound_id+'_'+radio).val();
                    var xhr = $.ajax({
                            url: $('#src').val() + '/controllers/property/property_controller.php',
                            type: 'POST',
                            data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: radio}
                    });
                    xhr.done(function (result) {
                         for(var i = 0;i<cardinality;i++){
                                elem = jQuery.parseJSON(result);
                                $('#field_property_term_'+compound_id+'_' + radio + '_' + i).html('');
                                if(elem.children){
                                    $.each(elem.children, function (idx, children) {
                                        var required = '';
                                        var checked = '';
                                        var value = $('#actual_value_'+compound_id+'_' + radio + '_' + i).val();
                                        //if(elem.metas.socialdb_property_required==='true'){
                                            required = ' onchange="compounds_validate_radio(' + radio + ','+i+','+compound_id+')"';
                                        //}
                                        //  if (property.id == selected) {
                                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                                        //  } else {
                                        if(value!=''&&value==children.term_id){
                                            checked = 'checked="checked"';
                                        }
                                        if (typeof delete_value === "function") {
                                            delete_value(children.term_id);
                                        }
                                        $('#field_property_term_'+compound_id+'_' + radio + '_' + i).append('<input '+checked+' '+required+' type="radio" name="socialdb_property_'+compound_id+'_'+ radio +'_'+ i +'[]" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                                        //  }
                                    });
                                }
                        }
                    });
                }
            });
        }
    }
    // checkboxes
    function compounds_list_checkboxes(checkboxes,categories,compound_id) {
        if (checkboxes) {
            $.each(checkboxes, function (idx, checkbox) {
                if($('#cardinality_compound_'+compound_id+'_'+checkbox).length>0){
                    var cardinality = $('#cardinality_compound_'+ compound_id +'_'+checkbox).val();
                    var xhr = $.ajax({
                            url: $('#src').val() + '/controllers/property/property_controller.php',
                            type: 'POST',
                            data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: checkbox}
                    });
                    
                    xhr.done(function (result) {
                        for(var i = 0;i<cardinality;i++){
                            elem = jQuery.parseJSON(result);
                            $('#field_property_term_'+compound_id+'_' + checkbox + '_'+i).html('');
                            $.each(elem.children, function (idx, children) {
                                var required = '';
                                var checked = '';
                                var value = $('#actual_value_'+compound_id+'_' + checkbox + '_' + i).val();
                                if (typeof delete_value === "function") {
                                    delete_value(children.term_id);
                                }
                                required = ' onchange="compounds_validate_checkbox(' + checkbox + ','+ i +','+ compound_id +')"';
                                if(value&&value==children.term_id){
                                    checked = 'checked="checked"';
                                }
                                //  if (property.id == selected) {
                                //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                                //  } else {
                                $('#field_property_term_'+compound_id+'_' + checkbox + '_' + i).append('<input '+checked+' '+required+'  type="checkbox" name="socialdb_property_'+ compound_id +'_'+checkbox+'_' + i + '[]" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                                //  }
                            });
                        }
                    });
                    
                }
            });
        }
    }
    // selectboxes
    function compounds_list_selectboxes(selectboxes,categories,compound_id) {
        if (selectboxes) {
            $.each(selectboxes, function (idx, selectbox) {
                if($('#cardinality_compound_'+compound_id+'_'+selectbox).length>0){
                    var cardinality = $('#cardinality_compound_'+compound_id+'_'+selectbox).val();
                    var xhr = $.ajax({
                            url: $('#src').val() + '/controllers/property/property_controller.php',
                            type: 'POST',
                            data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: selectbox}
                        })
                    xhr.done(function (result) {
                        for(var i = 0;i<cardinality;i++){
                            dynatree_object_index['field_property_term_'+compound_id+'_' + selectbox + '_' + i] = i;
                            elem = jQuery.parseJSON(result);
                            $('#field_property_term_'+compound_id+'_' + selectbox + '_' + i).html('');
                            $('#field_property_term_'+compound_id+'_' + selectbox + '_' + i).change(function(){
                                var cont = 0;
                                var i =  dynatree_object_index[$(this).attr('id')];
                                if( $(this).val().trim()!==''){
                                    cont++;
                                }
                                if( cont===0){
                                    $('#core_validation_'+compound_id+'_'+selectbox+'_'+i).val('false');
                                    set_field_valid_compounds(selectbox,'core_validation_'+compound_id+'_'+selectbox+'_'+i,compound_id);
                                }else{
                                    $('#core_validation_'+compound_id+'_'+selectbox+'_'+i).val('true');
                                    set_field_valid_compounds(selectbox,'core_validation_'+compound_id+'_'+selectbox+'_'+i,compound_id);
                                }
                            });
                            $('#field_property_term_'+compound_id+'_' + selectbox + '_' + i).append('<option value="">Selecione...</option>');
                            $.each(elem.children, function (idx, children) {
                                var checked = '';
                                var value = $('#actual_value_'+compound_id+'_' + selectbox + '_' + i).val();
                                if (typeof delete_value === "function") {
                                    delete_value(children.term_id);
                                }
                                 
                                if(value!=''&&value==children.term_id){
                                    checked = 'selected="selected"';
                                    $('#core_validation_'+selectbox).val('true');
                                    set_field_valid(selectbox,'core_validation_'+selectbox);
                                }
                                $('#field_property_term_'+compound_id+'_' + selectbox + '_' + i).append('<option '+checked+' value="' + children.term_id + '">' + children.name + '</option>');
                                if(checked!==''){
                                   $('#field_property_term_'+compound_id+'_' + selectbox + '_' + i).trigger('change');
                                }
                                //  }
                            });
                        }    
                    });
                }    
            });
        }
    }
     // multiple
    function compounds_list_multipleselectboxes(multipleSelects,categories,compound_id) {
        if (multipleSelects) {
            $.each(multipleSelects, function (idx, multipleSelect) {
                if($('#cardinality_compound_'+compound_id+'_'+multipleSelect).length>0){
                    var cardinality = $('#cardinality_compound_'+compound_id+'_'+multipleSelect).val();
                    var xhr = $.ajax({
                            url: $('#src').val() + '/controllers/property/property_controller.php',
                            type: 'POST',
                            data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: multipleSelect}
                        });
                     xhr.done(function (result) {    
                        for(var i = 0;i<cardinality;i++){
                            var value = $('#actual_value_'+compound_id+'_' + multipleSelect + '_' + i).val();
                            //validation
                            $('#field_property_term_'+compound_id+'_' + multipleSelect + '_' + i).select(function(){
                                if( $("#field_property_term_"+compound_id+'_' + multipleSelect + '_' + i).val()===''){
                                    $('#core_validation_'+compound_id+'_' + multipleSelect + '_' + i).val('false');
                                }else{
                                     $('#core_validation_'+compound_id+'_' + multipleSelect + '_' + i).val('true');
                                }
                                set_field_valid_compounds(multipleSelect,'core_validation_'+compound_id+'_' + multipleSelect + '_' + i,compound_id);
                            });
                            //validation
                            $('#field_property_term_'+compound_id+'_' + multipleSelect + '_' + i).change(function(){
                                if( $("#field_property_term_"+compound_id+'_' + multipleSelect + '_' + i).val()===''){
                                    $('#core_validation_'+compound_id+'_' + multipleSelect + '_' + i).val('false');
                                }else{
                                     $('#core_validation_'+compound_id+'_' + multipleSelect + '_' + i).val('true');
                                }
                                set_field_valid_compounds(multipleSelect,'core_validation_'+compound_id+'_' + multipleSelect + '_' + i,compound_id);
                            });
                            //init
                            elem = jQuery.parseJSON(result);
                            $('#field_property_term_'+compound_id+'_' + multipleSelect+ '_' + i).html('');
                            $.each(elem.children, function (idx, children) {
                                var checked = '';
                                if (typeof delete_value === "function") {
                                    delete_value(children.term_id);
                                }
                                if(value&&value==children.term_id){
                                    checked = 'selected="selected"';
                                }
                                $('#field_property_term_'+compound_id+'_' + multipleSelect + '_' + i).append('<option '+checked+' value="' + children.term_id + '">' + children.name + '</option>');
                                //  }
                            });
                        }
                    });
                }    
            });
        }
    }
    // treecheckboxes
    function compounds_list_treecheckboxes(treecheckboxes,categories,compound_id) {
        if (treecheckboxes) {;
            $.each(treecheckboxes, function (idx, treecheckbox) {
                 if($('#cardinality_compound_'+compound_id+'_'+treecheckbox).length>0){
                    var cardinality = $('#cardinality_compound_'+compound_id+'_'+treecheckbox).val();
                    for(var i = 0;i<cardinality;i++){
                        var value = $('#actual_value_'+compound_id+'_' + treecheckbox + '_' + i).val();
                        dynatree_object_index["field_property_term_"+compound_id+"_"+ treecheckbox  + '_' + i] = i;
                        $("#field_property_term_"+compound_id+"_"+ treecheckbox  + '_' + i ).dynatree({
                            selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
                            checkbox: true,
                            initAjax: {
                                url: $('#src').val() + '/controllers/category/category_controller.php',
                                data: {
                                    collection_id: $("#collection_id").val(),
                                    property_id: treecheckbox,
                                    order: 'name',
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
                                        order: 'name',
                                        //operation: 'findDynatreeChild'
                                        operation: 'expand_dynatree'
                                    }
                                });
                            },
                            onClick: function (node, event) {
                                // Close menu on clickdelete_value
//                                if (typeof delete_value === "function") {
//                                        delete_value(node.data.key);
//                                }
                                
                            },
                            onCreate: function (node, span) {
//                                $("#field_property_term_"+treecheckbox  + '_' + i).dynatree("getRoot").visit(function(node){
//                                    if (typeof delete_value === "function") {
//                                        delete_value(node.data.key);
//                                    }
                                if(value&&value==node.data.key){
                                    node.select();
                                }
//                                });
                                bindContextMenuSingle(span,'field_property_term_'+compound_id+'_' + treecheckbox  + '_' + i);
                            },
                            onSelect: function (flag, node) {
                                var cont = 0;
                                var i = dynatree_object_index[this.$tree[0].id];
                                var selKeys = $.map(node.tree.getSelectedNodes(), function (node) {
                                    return node;
                                });
                                var categories = $.map(node.tree.getSelectedNodes(), function (node) {
                                    return node.data.key;
                                });
                                if(categories.length>0&&categories.indexOf(node.data.key)>=0){
                                    append_category_properties(node.data.key);
                                }else{
                                    append_category_properties(0,node.data.key);
                                }
                                $("#socialdb_propertyterm_"+compound_id+"_" + treecheckbox + '_' + i).html('');
                                $.each(selKeys, function (index, key) {
                                    cont++;
                                    $("#socialdb_propertyterm_"+compound_id+"_" + treecheckbox + '_' + i).append('<input type="hidden" name="socialdb_property_'+treecheckbox+'_' + i + '[]" value="' + key.data.key + '" >');
                                });
                                if(cont===0){
                                    $('#core_validation_'+compound_id+'_'+treecheckbox+ '_' + i).val('false');
                                    set_field_valid_compounds(treecheckbox,'core_validation_'+compound_id+'_'+treecheckbox+ '_' + i,compound_id);
                                 }else{
                                    $('#core_validation_'+compound_id+'_'+treecheckbox+ '_' + i).val('true');
                                    set_field_valid_compounds(treecheckbox,'core_validation_'+compound_id+'_'+treecheckbox+ '_' + i,compound_id); 
                                 }
                            }
                        });
                    }
                }    
            });
        }
    }
    
    // tree
    function compounds_list_tree(trees,categories,compound_id) {
        if (trees) {
            $.each(trees, function (idx, tree) {
                if($('#cardinality_compound_'+compound_id+'_'+tree).length>0){
                    var cardinality = $('#cardinality_compound_'+compound_id+'_'+tree).val();
                    for(var i = 0;i<cardinality;i++){
                        var value = $('#actual_value_'+compound_id+'_' + tree + '_' + i).val();
                        dynatree_object_index["field_property_term_"+compound_id+"_"+ tree  + '_' + i] = i;
                        $("#field_property_term_"+compound_id+"_" + tree + '_' + i).dynatree({
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
                                        order: 'name',
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
                                            //hide_checkbox: 'true',
                                            classCss: node.data.addClass,
                                             order: 'name',
                                            //operation: 'findDynatreeChild'
                                            operation: 'expand_dynatree'
                                        }
                                    });
                                },
                                onCreate: function (node, span) {
//                                    $("#field_property_term_"+tree + '_' + i).dynatree("getRoot").visit(function(node){
//                                        if (typeof delete_value === "function") {
//                                              delete_value(node.data.key);
//                                        }
                                    if(value&&value==node.data.key){
                                        node.select();
                                    }
//                                    });
                                    bindContextMenuSingle(span,'field_property_term_' + tree + '_');
                                },
                                onSelect: function (flag, node) {
                                    var i = dynatree_object_index[this.$tree[0].id];
                                    if ($("#field_property_term_"+compound_id+"_" + tree + '_' + i).val() === node.data.key) {
                                        append_category_properties(0,node.data.key);
                                        $("#field_property_term_"+compound_id+"_" + tree + '_' + i).val("");
                                         $('#core_validation_'+compound_id+'_' + tree + '_' + i).val('false');
                                         set_field_valid_compounds(tree,'core_validation_'+compound_id+'_' + tree + '_' + i,compound_id);
                                    } else {
                                        append_category_properties(node.data.key,$("#field_property_term_"+compound_id+"_" + tree).val());
                                        $("#field_property_term_"+compound_id+"_" + tree + '_' + i).val(node.data.key);
                                        $('#core_validation_'+compound_id+'_'+ tree + '_' + i).val('true');
                                        set_field_valid_compounds(tree,'core_validation_'+compound_id+'_' + tree + '_' + i,compound_id);
                                    }
                                }
                            });
                    }
                }    
            });
        }
    }
    // get value of the property
    function compounds_get_val(value) {
        if (!value||value === '' ) {
            return false;
        } else if (value.split(',')[0] === '' && value !== '') {
            return [value];
        } else {
            return value.split(',');
        }
    }
    //################################ Cardinalidade #################################//    
    function show_fields_metadata_cardinality_compounds(property_id,id){
        $('#button_property_'+property_id+'_'+id).hide();
        $('#container_field_'+property_id+'_'+(id+1)).show();   
        properties = $('#compounds_'+property_id).val().split(',');
        if(properties&&properties.length>0){
            for(var i = 0; i<properties.length; i++){
                  $('#core_validation_'+property_id+'_'+properties[i]+'_'+((id+1))).val('false');
            }
             validate_all_fields_compounds(property_id);
        }
    }
    //removendo o container
    function remove_container_compounds(property_id,id){
        var show_button = false;
        properties = $('#compounds_'+property_id).val().split(',');
        $('#container_field_'+property_id+'_'+(id)).hide();
        if(properties&&properties.length>0){
            for(var i = 0; i<properties.length; i++){
                  $('#core_validation_'+property_id+'_'+properties[i]+'_'+(id)).val('true');
                  $('input[name="socialdb_property_'+property_id+'_'+properties[i]+'_'+(id)+'[]"]').val('');
            }
            validate_all_fields_compounds(property_id);
        }
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
    //################################ VALIDACOES##############################################//
    /**
     * funcao que valida os campos radios, e realiza a insercao das propriedades de categorias
     * @param {type} property_id
     * @returns {undefined}     */
    function compounds_validate_radio(property_id,i,compound_id){
        var selected = $("input[type='radio'][name='socialdb_property_"+compound_id+"_"+property_id+"_"+i+"[]']:checked");
        if (selected.length > 0) {
            append_category_properties($(selected[0]).val(), $('#actual_field_term_'+compound_id+"_"+property_id+"_"+i).val());
            $('#actual_field_term_'+compound_id+"_"+property_id+"_"+i).val($(selected[0]).val()); 
            $('#core_validation_'+compound_id+'_'+property_id+"_"+i).val('true');
            set_field_valid_compounds(property_id,'core_validation_'+compound_id+'_'+property_id+"_"+i,compound_id);
        }else{
            $('#core_validation_'+property_id+"_"+i).val('false');
            set_field_valid_compounds(property_id,'core_validation_'+compound_id+'__'+property_id+"_"+i,compound_id);
        }
    }
    /**
     * funcao que valida o campo checkbox, e realiza a insercao das propriedades de categorias
     * @param {type} property_id
     * @returns {undefined}     */
    function compounds_validate_checkbox(property_id,i,compound_id){
        var selected = $("input[type='checkbox'][name='socialdb_property_" + compound_id + "_"+property_id+"_"+i+"[]']:checked");
        if (selected.length > 0) {
            $('#core_validation_'+ compound_id + "_"+property_id+"_"+i).val('true');
            set_field_valid_compounds(property_id,'core_validation_'+ compound_id + "_"+property_id+"_"+i,compound_id);
        }else{
            $('#core_validation_'+ compound_id + "_"+property_id+"_"+i).val('false');
            set_field_valid_compounds(property_id,'core_validation_'+ compound_id + "_"+property_id+"_"+i,compound_id);
        }
        //verificando se existe propriedades para serem  adicionadas
        $.each($("input[type='checkbox'][name='socialdb_property_" + compound_id + "_"+property_id+"_"+i+"[]']"),function(index,value){
            if($(this).is(':checked')){
                append_category_properties($(this).val());
            }else{
                append_category_properties(0,$(this).val());
            }
        });
    }
    /**
     * funcao que valida os campos de selecao unica
     * @param {type} seletor
     * @param {type} property_id
     * @returns {undefined}     */
    function edit_validate_selectbox(seletor,property_id,compound_id){
        console.log(seletor);
        if($(seletor).val()===''){
            $('#core_validation_'+property_id).val('false');
            set_field_valid(property_id,'core_validation_'+property_id);
        }else{
            append_category_properties($(seletor).val(), $('#socialdb_propertyterm_'+property_id+'_value').val());
           $('#socialdb_propertyterm_'+property_id+'_value').val($(seletor).val()); 
            $('#core_validation_'+property_id).val('true');
            set_field_valid(property_id,'core_validation_'+property_id);
        }
        
    }
    /**
     * funcao que valida os campos de multipla selecao
     * @param {type} id
     * @param {type} seletor
     * @returns {undefined}     */
    function compounds_validate_multipleselectbox(seletor,property_id,compound_id,i){
        var selected = $("#field_property_term_"+compound_id+"_"+property_id+"_"+i).find(":selected");
        console.log(selected);
        if (selected.length > 0) {
            $('#core_validation_'+compound_id+'_'+property_id+'_'+i).val('true');
            set_field_valid(property_id,'core_validation_'+compound_id+'_'+property_id+'_'+i,compound_id);
            //verificando se existe propriedades para serem  adicionadas
            $.each($("#field_property_term_"+compound_id+"_"+property_id+"_"+i+" option"),function(index,value){
                if($(this).is(':selected')){
                    append_category_properties($(this).val());
                }else{
                    append_category_properties(0,$(this).val());
                }
            });
        }else{
            $('#core_validation_'+compound_id+'_'+property_id+'_'+i).val('false');
            set_field_valid(property_id,'core_validation_'+compound_id+'_'+property_id+'_'+i,compound_id);
        }
    }
    
    function validate_selectbox(seletor,property_id,compound_id){
        if($(seletor).val()===''){
            $('#core_validation_'+property_id).val('false');
            set_field_valid(property_id,'core_validation_'+property_id);
        }else{
            $('#core_validation_'+property_id).val('true');
            set_field_valid(property_id,'core_validation_'+property_id);
        }
    }
    /**
    * 

     * @type Arguments     */
    function set_field_valid_compounds(id,seletor,compound_id){
        if($('#'+seletor).val()==='false'){
            $('#'+seletor).val('false');
        }else{
            $('#'+seletor).val('true');
        }
        validate_all_fields_compounds(compound_id);
    }
    
    function validate_all_fields_compounds(compound_id){
        var cont = 0;
        $( ".core_validation_compounds_"+compound_id).each(function( index ) {
            if($( this ).val()==='false'){
                cont++;
            }
        });
        if(cont===0){
            $('#core_validation_'+compound_id).val('true');
            set_field_valid(compound_id,'core_validation_'+compound_id);
        }else{
            $('#core_validation_'+compound_id).val('false');
            set_field_valid(compound_id,'core_validation_'+compound_id);
        }
    }
//####################### #6 - licencas ########################################  
    $(function () {
        var src = $('#src').val();

        $('#submit_help_cc').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: $("#src").val() + '/controllers/object/object_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                $("#modalHelpCC").modal('hide');
                elem = jQuery.parseJSON(result);
                if(elem.id && elem.id != ''){
                    $('#radio' + elem.id).attr("checked", "checked");
                }
                showAlertGeneral(elem.title, elem.msg, elem.type);
            });
        });
        
        if($('.hide_license')&&$('.hide_license').val()==='true'){
            $('#list_licenses_items').hide();
            $('#core_validation_license').val('true');
        }
        
        $('input:radio[name="object_license"]').change(function() {
            $('#core_validation_license').val('true');
            validate_all_fields();
            set_field_valid('license','core_validation_license')
        });
    });
  
</script>
            