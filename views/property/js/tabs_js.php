<script type="text/javascript">
    /* Executed by script's start */
    $(function () {
        initiate_accordeon('default');
        list_tabs();
    });
    //inicializa as abas
    function initiate_tabs(){
        var xhr =  $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: {
                collection_id: $("#collection_id").val(), 
                operation: 'get_tabs'}
        });
        // quando o ajax for finalizado        
        xhr.done(function (result) {
            hide_modal_main();
            var json = jQuery.parseJSON(result);
            if(json.array.length>0){
                var li = $('#plus_tab_button');
                var content = $('#tab-content-metadata');
                $.each(json.array,function(index,value){
                   if($('#metadata-container-'+value.meta_id).length==0){
                       li.before(get_li_model(value.meta_id,value.meta_value));
                      content.append(get_tab_content(value.meta_id));
                   } 
                   initiate_accordeon(value.meta_id);
                });
            }
        });
        //retorno apenas a promess
        return xhr;
    }
    //funcao que gera um li modelo para ser incluido
    function get_li_model(id,name){
        if(!name){
            name = '<?php _e('New tab', 'tainacan') ?>';
        }
        return '<li role="presentation" onmouseover="show_close_tab('+id+')" onmouseout="hide_close_tab('+id+')">'+
                '<a id="click-tab-'+id+'" href="#tab-'+id+'" aria-controls="tab-'+id+'" role="tab" data-toggle="tab">'+
                    '<span class="tab-title"  ondblclick="alter_tab_title('+id+')" id="'+id+'-tab-title">'+name+'</span>'+
                    '<input id="'+id+'-tab-title-input"'+
                           'class="style-input"'+
                           'onblur="on_blur_input_title('+id+')"'+
                           'onkeyup="on_key_input_title('+id+',event)"'+
                           ' style="display: none;" '+
                           'type="text" '+
                           'value="'+name+'">&nbsp;&nbsp;'+
                           '<span id="remove-button-tab-'+id+'" style="display:none;cursor:pointer;position: absolute;top: 0px;right: 0px;height: 15px;padding-left:2px;padding-right:2px;background:#0c698b;color:white" onclick="remove_tab(this,'+id+')"><span style="position: relative;top: -3px">x</span></span>'
                '</a>'+
            '</li>';
    }
    
    function show_close_tab(id){
        $('#remove-button-tab-'+id).show();
    }
    function hide_close_tab(id){
        $('#remove-button-tab-'+id).hide();
    }
    // funcao que gera o conteudo da aba criada
    function get_tab_content(id){
        return '<div style="background:white;" id="tab-'+id+'" class="ui-widget ui-helper-clearfix col-md-12 tab-pane fade">'+
                '<ul style="background:white;" id="metadata-container-'+id+'" class="gallery ui-helper-reset ui-helper-clearfix connectedSortable metadata-container">'+
                '</ul>'+
                '</div>';
    }
    // ao clicar sobre o span que contenha o titulo da aba
    function alter_tab_title(id){
        $('#'+id+'-tab-title').hide();
        $('#'+id+'-tab-title-input').show();
        $('#'+id+'-tab-title-input').focus();
    }
    // ao clicar fora do input do titulo
    function on_blur_input_title(id){
        $('#'+id+'-tab-title-input').hide();
        $('#'+id+'-tab-title').show();
    }
    // ao apertar enter no input do titulo e alterar o titulo
    function on_key_input_title(id,e){
        if(e.keyCode == 13 && $('#'+id+'-tab-title-input').val()!==''){
            $('#'+id+'-tab-title-input').hide();
            $('#'+id+'-tab-title').show();
        }else if($('#'+id+'-tab-title-input').val()!==''){
            alter_tab_name(id,$('#'+id+'-tab-title-input').val());
            $('#'+id+'-tab-title').text($('#'+id+'-tab-title-input').val());
        }
    }
    // alterando o nome da aba
    function alter_tab_name(id,name){
        $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: {
                collection_id: $("#collection_id").val(), 
                name:name,
                id:id,
                operation: 'alter_tab_name'}
        });
    }
    // adicionando uma nova aba
    function add_tab(seletor){
        show_modal_main();
        $('.style-input').hide();
        $('.tab-title').show();
        var promisse = insert_tab('<?php _e('New tab', 'tainacan') ?>');
        var li = $(seletor).parent();
        var content = $('#tab-content-metadata');
        promisse.done(function (result) {
            hide_modal_main();
            var json = jQuery.parseJSON(result);
            li.before(get_li_model(json.id));
            content.append(get_tab_content(json.id));
            $('.nav-tabs').tab();
            $('#click-tab-'+json.id+'').trigger('click');
            initiate_accordeon(json.id);
        });
        
    }
    //insere o postmeta da aba
    function insert_tab(name){
        return $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: {
                collection_id: $("#collection_id").val(), 
                tab_name: name, 
                operation: 'insert_tab'}
        });
    }
    //remove a aba
    function remove_tab(seletor,id){
         swal({
            title: '<?php _e('Attention!','tainacan') ?>',
            text: '<?php _e('Are you sure removing this tab?','tainacan') ?>',
            type: "info",
            showCancelButton: true,
            cancelButtonText: '<?php _e('Cancel','tainacan') ?>',
            confirmButtonClass: 'btn-primary',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                var li = $(seletor).parent().parent();
                $('#click-tab-default').trigger('click');
                li.remove();
                $.ajax({
                    url: $('#src').val() + '/controllers/collection/collection_controller.php',
                    type: 'POST',
                    data: {
                        collection_id: $("#collection_id").val(), 
                        id: id, 
                        operation: 'remove_tab'}
                }).done(function (result) {
                    list_collection_metadata();
                });
            }
        });        
    }
    //adiciona o metadado na aba correta
    function get_property_tab_seletor(id){
        if(!id){
            return 'ul#metadata-container-default';
        }else{
            return 'ul#metadata-container-'+id;
        }
    }
    //funcao responsavel em listar as abas nos selects
    function list_tabs(){
        //console.log('list-tabs');
        $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: {
                collection_id: $("#collection_id").val(), 
                operation: 'get_tabs'}
        }).done(function (result) {
            hide_modal_main();
            var json = jQuery.parseJSON(result);
            $('.socialdb_event_property_tab').html('');
            $('.socialdb_event_property_tab').append('<option value="default">'+json.default+'</option>');
            if(json.array.length>0){
                $.each(json.array,function(index,value){
                     $('.socialdb_event_property_tab').append('<option value="'+value.meta_id+'">'+value.meta_value+'</option>');
                });
            }
        });
    }
    //inicia o accordeon de cada aba criada
    function initiate_accordeon(id){
        $("#metadata-container-"+id ).sortable({
            cursor: "n-resize",
            containment: $("#metadata-container-"+id ),
            revert: 250,
            start: function(event, ui) {
               $(ui.item).show();
            },
            receive: function(event, ui) {
                var $ui_container = ui.item.context.parentNode.id;
                var item_id =  ui.item.context.id;
                var item_search_widget = $("#"+item_id).attr("data-widget");
                var is_fixed_meta = $("#"+item_id).hasClass('fixed-meta');
                var $sorter_span = "<span class='glyphicon glyphicon-sort sort-filter'></span>";
                $(ui.item.context).addClass('hide');
            },
            remove: function(event, ui) {
               // var $ui_container = ui.item.context.parentNode.id;
               // removeFacet(ui.item.context.id);
            },
            stop: function(event, ui) {
                var $ui_container = ui.item.context.parentNode.id;
                var sortedIds = $("#filters-accordion").sortable("toArray");
                //$("#metadata-container-"+id ).removeClass("change-meta-container");
            },
            sort: function(event, ui) {
                $(ui.item).show();
                var filtros_atuais = get_current_filters();
                //$("#metadata-container-"+id ).addClass("change-meta-container");
            },
            update: function( event, ui ) { 
                var $ui_container = ui.item.context.parentNode.id;
                var data = []; 
                var tab = '';
                $("#metadata-container-"+id+" li").each(function(i, el){
                     var p = $(el).attr('id').replace("meta-item-", "");
                     tab = $(el).attr('tab');
                     data.push(p);
                });
                console.log(tab);
                $.ajax({
                     type: "POST",
                     url: $('#src').val() + "/controllers/collection/collection_controller.php",
                     data: {
                         category_id: $('#property_category_id').val(),
                         collection_id: $('#collection_id').val(), 
                         operation: 'update_ordenation_properties', 
                         tab : tab, 
                         ordenation: data.join(',')}
                 });
                 delete_all_cache_collection();
            }        

        }).disableSelection();
    }
    // funcao que retorna o id da aba ou entao retorna false se caso nao existir
    function get_tab_property_id(current_id){
        var tab_property_id = false;
        var json = jQuery.parseJSON($('#tabs_properties').val());
        if(json && json.length>0){
            $.each(json,function(index,object){
                if(object[current_id]){
                    tab_property_id = object[current_id];
                }
            });
        }else if(json){
            $.each(json,function(index,object){
                if(index == current_id){
                    tab_property_id = object;
                }
            });
        }
        if(!tab_property_id){
            return 'default';
        }else{
            return tab_property_id;
        }
        
    }
</script>