<script type="text/javascript">
    /**
     ****************************************************************************
     ************************* PROPRIEDADES COMPOSTAS ************************
     ****************************************************************************
     **/ 
    /*
     *  accordeon para ordenacao dos metadados
     */
    function accordeon_ordenation_properties(){
        $( "#compounds_properties_ordenation" ).sortable({
            cursor: "n-resize",
            containment: $('#meta-compounds'),
            revert: 250,
            receive: function(event, ui) {
            },
            remove: function(event, ui) {
            },
            stop: function(event, ui) {
            },
            sort: function(event, ui) {
            },
            update: function( event, ui ) { 
                var sortedIds = [];
                $("#compounds_properties_ordenation li").each(function(i, el){
                     var p = $(el).attr('id').replace("compounds-", "");
                     sortedIds.push(p);
                });
                $('#compounds_id').val(sortedIds.join(','));
            }  
        }).disableSelection();
    }
    
    /*
     *  funcao que abre o dynatree para as propriedades de uma colecao
     */
    function initDynatreeFilterProperties(src) {
       $("#dynatree_properties_filter").dynatree({
           selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
           checkbox: true,
             initAjax: {
               url: src + '/controllers/property/property_controller.php',
               data: {
                   collection_id: $("#collection_id").val(),
                   order: 'name',
                   operation: 'initDynatreePropertiesFilter'
               }
               , addActiveKey: true
           },
           onLazyRead: function (node) {
               node.appendAjax({
                   url: src + '/controllers/filters/filters_controller.php',
                   data: {
                       collection: $("#collection_id").val(),
                       key: node.data.key,
                       hide_checkbox: 'true',
                       //operation: 'findDynatreeChild'
                       operation: 'childrenDynatreePropertiesFilter'
                   }
               });
           },onSelect: function (flag, node) {
                $( "#compounds_properties_ordenation" ).html('');
                //busco os nos selecionados
                var selKeys = $.map($("#dynatree_properties_filter").dynatree("getSelectedNodes"), function (node) {
                    return node;
                });
                var keys = $.map($("#dynatree_properties_filter").dynatree("getSelectedNodes"), function (node) {
                    return node.data.key;
                });
                //limitacao da quantidade de propriedades selecionados
                if(selKeys.length>0&&selKeys.length<=6){
                    $.each(selKeys,function(index,node){
                        var type = types_compounds[node.data.key];
                        var string = '';
                        if(type=='1'){
                            string = 'edit_metadata';
                        }else if(type=='2'){
                            string = 'edit_object';
                        }else if(type=='3'){
                            string = 'edit_term';
                        }
                        $( "#compounds_properties_ordenation" ).append('<li id="compounds-'+node.data.key+'">'+
                                '<a onclick="'+string+'(' + node.data.key + ')" class="edit_property_data" href="javascript:void(0)">' +
                                '<span style="margin-right:5px;" class="glyphicon glyphicon-edit pull-right"><span></a> ' +
                                '<a onclick="delete_property(' + node.data.key + ','+type+')" class="delete_property" href="#">' +
                                '<span style="margin-right:5px;" class="glyphicon glyphicon-trash pull-right"><span></a>' +
                                '<span style="margin-right:5px;" class="glyphicon glyphicon-sort sort-filter pull-right"></span>&nbsp;'+ add_filter_button(node.data.key) + node.data.title+'</li>')
                    })
                    $('#compounds_id').val(keys.join(','));
                }else if(selKeys.length>6){
                    node.select(false);
                }
                accordeon_ordenation_properties();
            }
       });
    }
    //SUBMISSAO DO METADADO COMPOSTO
    $('#submit_form_compounds').submit(function (e) {
        e.preventDefault();
        $('.modal').modal('hide');
        $('#modalImportMain').modal('show');
        $.ajax({
            url: src + '/controllers/property/property_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            $('#modalImportMain').modal('hide');
            elem = jQuery.parseJSON(result);
            if ( elem != null ) {
                list_collection_metadata();
                getRequestFeedback(elem.type, elem.msg);
            }
        });
    });
    
    //verifica se faz parte de uma propriedade composta
    function is_compounded(item){
        if(item){
            for (var property in item) {
                if (item.hasOwnProperty(property)) {
                    if(item[property]=='true'){
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    //mostra icone na listagem
    function add_compounds_button(){
        return '<a  title="<?php _e('Compounds Metadata','tainacan') ?>" style="cursor:pointer;margin-right:9px;">'+
                ' <img style="height:16px;width:16px;" src="<?php echo get_template_directory_uri() . "/libraries/images/icons/icon-compounds.png"; ?>" ></a> ';
    }
    
    //LISTANDO AS PROPRIEDADES COMPOSTAS
    function list_property_compounds() {
        var xhr = $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/property/property_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'list_property_compounds', category_id: $('#property_category_id').val()}
        });
        
        xhr.done(function (result) {
            elem = jQuery.parseJSON(result);
            if(elem.tabs){
                $('#tabs_properties').val(elem.tabs);
            }
            if (elem.no_properties !== true) {
                $.each(elem.property_compounds, function (idx, property) {
                    var current_id = property.id;
                    var current_search_widget = property.search_widget;
                    //buscando a aba da propriedade
                    var tab_property_id = get_tab_property_id(current_id)
                    //visibilidade do metadado
                    if(property.metas.socialdb_property_visibility&&property.metas.socialdb_property_visibility==='hide'){
                        return true;
                    }
                    if ( property.metas.is_repository_property && property.metas.is_repository_property === true ||
                        (property.metas.socialdb_property_created_category && $('#property_category_id').val() !== property.metas.socialdb_property_created_category) ) {
                        //se o metadado do repositorio for fixo
                        var button = '';
                        var style = '';
                        var class_var = '';
                        button = '<span class="glyphicon glyphicon-trash no-edit"></span>';
                        //adiciona na listagem
                        $(get_property_tab_seletor(tab_property_id)).append(
                            '<li tab="'+tab_property_id+'" id="meta-item-' + current_id + '" data-widget="' + property.search_widget + '" class="root_category '+class_var+' ui-widget-content ui-corner-tr '+is_allowed_facet(property.slug)+'">' +
                            '<label '+style+'   class="title-pipe">'+ add_compounds_button() + property.name + add_text_type('compound') + '</label>' +
                            '<a onclick="edit_metadata(' + current_id + ')" class="edit_property_data" href="javascript:void(0)">' +
                            '<div class="action-icons">'+
                            '<a class="edit-filter"><span class="glyphicon glyphicon-sort sort-filter"></span></a>&nbsp;'+
                            '<span class="glyphicon glyphicon-edit"></span></a> ' +
                            button + '</div></li>');
                    } else {
                        if ( $.inArray(property.type, ranking_types) == -1 ) {;
                            $(get_property_tab_seletor(tab_property_id)).append(
                                '<li tab="'+tab_property_id+'" id="meta-item-' + current_id + '" data-widget="' + current_search_widget + '" class="' + property.type + ' ui-widget-content ui-corner-tr">' +
                                '<label class="title-pipe">'+ add_compounds_button() + property.name + add_text_type('compound') + '</label><div class="action-icons">' +
                                '<a class="edit-filter"><span class="glyphicon glyphicon-sort sort-filter"></span></a>&nbsp;'+
                                '<a onclick="edit_compounds(' + current_id + ')" class="edit_property_data" href="javascript:void(0)">' +
                                '<span class="glyphicon glyphicon-edit"><span></a> ' +
                                '<input type="hidden" class="property_id" value="' + property.id + '">' +
                                '<input type="hidden" class="property_name" value="' + property.name + '">' +
                                '<input type="hidden" id="property_type_' + property.id + '" value="4">' +
                                '<a onclick="delete_property(' + current_id + ',' + 4 + ')" class="delete_property" href="#">' +
                                '<span class="glyphicon glyphicon-trash"><span></a></div><ul class="list-compounded" id="list-compounded-' + property.id + '"></ul></li>');
                        }
                    }
                    get_children_compounds(current_id,property.metas.socialdb_property_compounds_properties_id);
                });
            }
        });
        return xhr;
    }
    //editando metadado composto
    function edit_compounds(id) {
        list_tabs();
        $.ajax({
            url: $('#src').val() + '/controllers/property/property_controller.php',
            type: 'POST',
            data: { collection_id: $("#collection_id").val(), operation: 'edit_property_compounds', property_id: id }
        }).done(function (result) {
            elem = $.parseJSON(result);
            var visualization =elem.metas.socialdb_property_visualization;
            $("#compound_id").val(elem.id);
            $("#operation_property_compounds").val('update_property_compounds');
            // abrir o modal
            $("#meta-compounds").modal('show');
            $("#meta-compounds .modal-title .compounds-action").text('<?php _e('Edit','tainacan') ?>');
            $("#meta-compounds #compounds_name").val( elem.name );
            $("#meta-compounds #socialdb_property_help").val( elem.metas.socialdb_property_help );
            $("#meta-compounds .socialdb_event_property_tab option[value='" + get_tab_property_id(elem.id) +"']").attr('selected','selected');
            //cardinalidade
            if (elem.metas.socialdb_property_compounds_cardinality === '1') {
                $('#meta-compounds #socialdb_property_compounds_cardinality_1').prop('checked', true);
            } else {
                $("#meta-compounds #socialdb_property_compounds_cardinality_n").prop('checked', true);
            }
            // se for obrigatorio
            if (elem.metas.socialdb_property_required === 'true') {
                $("#property_compounds_required_true").prop('checked', true);
            } else {
                $("#property_compounds_required_true").prop('checked', false);
            }
            conso
            if(visualization=='restrict'){
                $( "#socialdb_property_compounds_visualization_restrict").prop('checked', true);
                $(  "#socialdb_property_compounds_visualization_public").removeAttr('checked');
            }else{
                $( "#socialdb_property_compounds_visualization_public").prop('checked', true);
                $( "#socialdb_property_compounds_visualization_restrict").removeAttr('checked');
            }
            //seleciono as propriedades
            var properties = elem.metas.socialdb_property_compounds_properties_id.split(',');
            $("#dynatree_properties_filter").dynatree("getRoot").visit(function(node){
                if(properties.length>0&&properties.indexOf(node.data.key)>=0){
                     node.select(true);
                }
            });
            $('#compounds_id').val( elem.metas.socialdb_property_compounds_properties_id );
        });
    }
    
    
    /***************************************************************************
    *                           LISTA AS PROPRIEDADES COMPOSTAS
    ***************************************************************************/
    function get_children_compounds(property_id,compounds_id){
        $.ajax({
            url: $('#src').val() + '/controllers/property/property_controller.php',
            type: 'POST',
            data: { 
                collection_id: $("#collection_id").val(), 
                operation: 'get_property_compounds',
                property_id: property_id,
                compounds_id:compounds_id }
        }).done(function (result) {
            $('#modalImportMain').modal('hide');
            elem = jQuery.parseJSON(result);
            $.each(elem.compounds,function(index,property){
                var current_id = property.id;
                var current_search_widget = property.type;
                var type = types_compounds[current_id];
                var string = '';
                if(type=='1'){
                    string = 'edit_metadata';
                }else if(type=='2'){
                    string = 'edit_object';
                }else if(type=='3'){
                    string = 'edit_term';
                }
                $( "#list-compounded-"+property_id ).append('<li id="compounds-'+current_id+'">'+
                                '<a onclick="'+string+'(' + current_id + ')" class="edit_property_data" href="javascript:void(0)">' +
                                '<span style="margin-right:5px;" class="glyphicon glyphicon-edit pull-right"><span></a> ' +
                                '<a onclick="delete_property(' + current_id + ','+type+')" class="delete_property" href="#">' +
                                '<span style="margin-right:5px;" class="glyphicon glyphicon-trash pull-right"><span></a>' +
                                '<span style="margin-right:5px;" class="glyphicon glyphicon-sort sort-filter pull-right"></span>&nbsp;'+ add_filter_button(current_id) + property.name + add_text_type(current_search_widget) +'</li>')
            });
        });
    }
    
</script>