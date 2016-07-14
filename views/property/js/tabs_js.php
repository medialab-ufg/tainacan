<script type="text/javascript">
    /* Executed by script's start */
    $(function () {
        initiate_accordeon('default');
        initiate_tabs();
        list_tabs();
    });
    //inicializa as abas
    function initiate_tabs(){
        $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: {
                collection_id: $("#collection_id").val(), 
                operation: 'get_tabs'}
        }).done(function (result) {
            hide_modal_main();
            var json = jQuery.parseJSON(result);
            if(json.array.length>0){
                var li = $('#plus_tab_button');
                var content = $('#tab-content-metadata');
                $.each(json.array,function(index,value){
                   li.before(get_li_model(value.meta_id,value.meta_value));
                   content.append(get_tab_content(value.meta_id));
                });
            }
        });
    }
    //funcao que gera um li modelo para ser incluido
    function get_li_model(id,name){
        if(!name){
            name = '<?php _e('New tab', 'tainacan') ?>';
        }
        return '<li role="presentation">'+
                '<a id="click-tab-'+id+'" href="#tab-'+id+'" aria-controls="tab-'+id+'" role="tab" data-toggle="tab">'+
                    '<span class="tab-title"  ondblclick="alter_tab_title('+id+')" id="'+id+'-tab-title">'+name+'</span>'+
                    '<input id="'+id+'-tab-title-input"'+
                           'class="style-input"'+
                           'onblur="on_blur_input_title('+id+')"'+
                           'onkeyup="on_key_input_title('+id+',event)"'+
                           ' style="display: none;" '+
                           'type="text" '+
                           'value="'+name+'">&nbsp;&nbsp;'+
                           '<span class="close" style="cursor:pointer;" onclick="remove_tab(this,'+id+')">&times;</span>'
                '</a>'+
            '</li>';
    }
    // funcao que gera o conteudo da aba criada
    function get_tab_content(id){
        return '<div id="tab-'+id+'" class="ui-widget ui-helper-clearfix col-md-12 tab-pane fade">'+
                '<ul id="metadata-container-'+id+'" class="gallery ui-helper-reset ui-helper-clearfix connectedSortable metadata-container">'+
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
            connectWith: ".connectedSortable",
            revert: 250,
            helper: "clone",
            receive: function(event, ui) {
                var $ui_container = ui.item.context.parentNode.id;
                var item_id =  ui.item.context.id;
                var item_search_widget = $("#"+item_id).attr("data-widget");
                var is_fixed_meta = $("#"+item_id).hasClass('fixed-meta');
                var $sorter_span = "<span class='glyphicon glyphicon-sort sort-filter'></span>";
                $(ui.item.context).addClass('hide');
            },
            remove: function(event, ui) {
                var $ui_container = ui.item.context.parentNode.id;
                removeFacet(ui.item.context.id);
            },
            stop: function(event, ui) {
                var $ui_container = ui.item.context.parentNode.id;
                var sortedIds = $("#filters-accordion").sortable("toArray");
                $("#filters-accordion").removeClass("adding-meta");
                $("#metadata-container-"+id ).removeClass("change-meta-container");
            },
            sort: function(event, ui) {
                $("#filters-accordion").addClass("adding-meta");
                var filtros_atuais = get_current_filters();
                $("#metadata-container-"+id ).addClass("change-meta-container");
            },
            update: function( event, ui ) { 
                var $ui_container = ui.item.context.parentNode.id;
                var data = [];
                $("#metadata-container="+id+" li").each(function(i, el){
                     var p = $(el).attr('id').replace("meta-item-", "");
                     data.push(p);
                });
                $.ajax({
                     type: "POST",
                     url: $('#src').val() + "/controllers/collection/collection_controller.php",
                     data: {
                         collection_id: $('#collection_id').val(), 
                         operation: 'update_ordenation_properties', 
                         ordenation: data.join(',')}
                 });

            }        

        }).disableSelection();
    }

</script>