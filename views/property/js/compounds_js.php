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
            //containment: $('#meta-compounds'),
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
       if($("#dynatree_properties_filter").has('li').length>0){
           $("#dynatree_properties_filter").dynatree("getTree").reload(); 
       }else{
            $("#dynatree_properties_filter").dynatree({
                selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
                checkbox: true,
                  initAjax: {
                    url: src + '/controllers/property/property_controller.php',
                    data: {
                        category_id: $('#property_category_id').val(),
                        order: 'name',
                        operation: 'initDynatreePropertiesFilterCategory'
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
                     $( "#compounds_properties_ordenation" ).css('height','auto');
                     //busco os nos selecionados
                     var selKeys = $.map($("#dynatree_properties_filter").dynatree("getSelectedNodes"), function (node) {
                         return node;
                     });
                     var keys = $.map($("#dynatree_properties_filter").dynatree("getSelectedNodes"), function (node) {
                         return node.data.key;
                     });
                     keys = ordenateCompundedKeys(keys);
                     selKeys = ordenateCompundedNodes(selKeys);
                     //limitacao da quantidade de propriedades selecionados
                     if(selKeys.length>0&&selKeys.length<=50){
                         console.log(selKeys);
                         $.each(selKeys,function(index,node){
                             var type = types_compounds[node.data.key];
                             var string = '';
                             if(type=='1'){
                                 string = 'edit_metadata';
                             }else if(type=='2'){
                                 string = 'edit_property_object';
                             }else if(type=='3'){
                                 string = 'edit_term';
                             }
                             $( "#compounds_properties_ordenation" ).append('<li id="compounds-'+node.data.key+'">'+
                                     '<a onclick="'+string+'(' + node.data.key + ')" class="edit_property_data" href="javascript:void(0)">' +
                                     '<span style="margin-right:5px;color: #88A6CC;" class="glyphicon glyphicon-edit pull-right"><span></a> ' +
                                     '<a onclick="delete_property(' + node.data.key + ','+type+')" class="delete_property" href="javascript:void(0)">' +
                                     '<span style="margin-right:5px;color: #88A6CC;" class="glyphicon glyphicon-trash pull-right"><span></a>' +
                                     '<a><span style="margin-right:5px;color: #88A6CC;" class="glyphicon glyphicon-sort sort-filter pull-right"></span></a>&nbsp;'+ add_filter_button(node.data.key) + node.data.title+'</li>')
                         })
                     }else if(selKeys.length>11){
                         node.select(false);
                     }
                     $('#compounds_id').val(keys.join(','));
                     accordeon_ordenation_properties();
                 }
            });
       }
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
            $('#compounds_id').val('');
            elem = jQuery.parseJSON(result);
            if ( elem != null ) {
                if(elem.operation != 'update_property_compounds'){     
                    list_collection_metadata();
                }else{
                    // console.log($('#meta-item-'+elem.compound_id+' .property-name').first());
                    $('#meta-item-'+elem.compound_id+' .property-name').first().text(elem.compounds_name);
                    $( "#list-compounded-"+elem.compound_id ).html('');
                    get_children_compounds(elem.compound_id,elem.compounds_id);
                }
                getRequestFeedback(elem.type, elem.msg);
            }
        });
    });
    
    function ordenateCompundedKeys(keys){
        var selected = $('#compounds_id').val();
        console.log(selected);
        if(selected.trim()!=""){
            var keys_ordenate = selected.split(',');
            if(keys_ordenate.length>keys.length){
                return keys;
            } 
            $.each(keys,function(index,value){
                if(keys_ordenate.indexOf(value)<0){
                    keys_ordenate.push(value);
                }
            });
            return keys_ordenate;
        }else{
            return keys;
        }
    }
    
    function ordenateCompundedNodes(nodes){
        var selected = $('#compounds_id').val();
        var nodes_ordenated = [];
        var last_added = '';
        if(selected.trim()!=""){
            var keys_ordenate = selected.split(',');
            if(keys_ordenate.length>nodes.length){
                return nodes;
            } 
             $.each(keys_ordenate,function(index,id){
                $.each(nodes,function(index,value){
                    if(id === value.data.key){
                        nodes_ordenated.push(value);
                    }else if(keys_ordenate.indexOf(value.data.key)<0){
                        last_added = value;
                    }
                });
            });    
            if(last_added!='')
                nodes_ordenated.push(last_added);
            return nodes_ordenated;
        }else{
            return nodes;
        }
    }
    
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
                ' <img style="height:16px;width:16px;" src="<?php echo get_template_directory_uri() . "/libraries/images/icons/icon-metadata_compound.png"; ?>" ></a> ';
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
                    var tab_property_id = get_tab_property_id(current_id);
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
                            '<label '+style+'   class="title-pipe">'+ add_compounds_button() + '<span class="property-name">' + property.name + '</span>' + add_text_type('compound') + '</label>' +
                            '<a onclick="edit_metadata(' + current_id + ')" class="edit_property_data" href="javascript:void(0)">' +
                            '<div class="action-icons">'+
                            '<a class="edit-filter"><span class="glyphicon glyphicon-sort sort-filter"></span></a>&nbsp;'+
                            '<span class="glyphicon glyphicon-edit"></span></a> ' +
                            button + '</div></li>');
                    } else {
                        if ( $.inArray(property.type, ranking_types) == -1 ) {
                            $(get_property_tab_seletor(tab_property_id)).append(
                                '<li tab="'+tab_property_id+'" id="meta-item-' + current_id + '" data-widget="' + current_search_widget + '" class="' + property.type + ' ui-widget-content ui-corner-tr">' +
                                '<label class="title-pipe">'+ add_compounds_button() + '<span class="property-name">' + property.name + '</span>' + add_text_type('compound') + '</label><div class="action-icons">' +
                                '<a class="edit-filter"><span class="glyphicon glyphicon-sort sort-filter"></span></a>&nbsp;'+
                                '<a onclick="edit_compounds(' + current_id + ')" class="edit_property_data" href="javascript:void(0)">' +
                                '<span class="glyphicon glyphicon-edit"><span></a> ' +
                                '<input type="hidden" class="property_id" value="' + property.id + '">' +
                                '<input type="hidden" class="property_name" value="' + property.name + '">' +
                                '<input type="hidden" id="property_type_' + property.id + '" value="4">' +
                                '<a onclick="delete_property(' + current_id + ',' + 4 + ')" class="delete_property" href="javascript:void(0)">' +
                                '<span class="glyphicon glyphicon-trash"><span></a></div><ul class="list-compounded" id="list-compounded-' + property.id + '" style="margin-top:8px;"></ul></li>');
                        }
                    }
                    get_children_compounds(current_id,property.metas.socialdb_property_compounds_properties_id);
                });
                list_collection_facets();
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
             $('#compounds_id').val('');
            var visualization = elem.metas.socialdb_property_visualization;
            $("#compound_id").val(elem.id);
            $("#operation_property_compounds").val('update_property_compounds');
            // abrir o modal
            $("#meta-metadata_compound").modal('show');
            $("#meta-metadata_compound .modal-title .compounds-action").text('<?php _e('Edit','tainacan') ?>');
            $("#meta-metadata_compound #compounds_name").val( elem.name );
            $("#meta-metadata_compound #socialdb_property_help").val( elem.metas.socialdb_property_help );
            $("#meta-metadata_compound .socialdb_event_property_tab option[value='" + get_tab_property_id(elem.id) +"']").attr('selected','selected');
            //cardinalidade
            if (elem.metas.socialdb_property_compounds_cardinality === '1') {
                $('#meta-metadata_compound #socialdb_property_compounds_cardinality_1').prop('checked', true);
            } else {
                $("#meta-metadata_compound #socialdb_property_compounds_cardinality_n").prop('checked', true);
            }
            // se for obrigatorio
            if (elem.metas.socialdb_property_required === 'true') {
                $("#property_compounds_required_true").prop('checked', true);
                $("#property_compounds_required_true_field").prop('checked', false);
            }else if(elem.metas.socialdb_property_required === 'true_one_field'){
                 $("#property_compounds_required_true").prop('checked', false);
                $("#property_compounds_required_true_field").prop('checked', true);
            } else {
                $("#property_compounds_required_true").prop('checked', false);
                $("#property_compounds_required_true_field").prop('checked', false);
            }
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
                node.select(false);
            });
            if(properties && properties.length > 0){
                $.each(properties,function(index,value){
                    $("#dynatree_properties_filter").dynatree("getRoot").visit(function(node){
                        if(node.data.key===value){
                             node.select(true);
                        }
                    });
                });
            }
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
                if(!property.name){
                    return true;
                }
                var current_id = property.id;
                var current_search_widget = property.type;
                var type = types_compounds[current_id];
                if(!type){
                    if(property.metas.socialdb_property_term_cardinality){
                        type = '3';
                    }
                }
                var string = '';
                var class_string = '';
                var cat_id = ''
                if(type=='1' || property.metas.socialdb_property_data_widget){
                    string = 'edit_metadata';
                }else if(type=='2' || property.metas.socialdb_property_object_category_id){
                    string = 'edit_property_object';
                }else if(type=='3'){
                    string = 'edit_term';
                    cat_id = '<input type="hidden" class="coumpound_id_'+property.metas.socialdb_property_term_root+'" id="'+ current_id +'">';
                }
                $( "#list-compounded-"+property_id ).append('<li id="compounds-'+current_id+'" class="'+class_string+'" style="width:102%;">'+
                                cat_id +
                                '<a onclick="delete_property(' + current_id + ','+type+')" class="delete_property" href="javascript:void(0)">' +
                                '<span style="margin-right:5px;color: #88A6CC;" class="glyphicon glyphicon-trash pull-right"><span></a>' + 
                                '<a onclick="'+string+'(' + current_id + ')" class="edit_property_data" href="javascript:void(0)">' +
                                '<span style="margin-right:5px;color: #88A6CC;" class="glyphicon glyphicon-edit pull-right"><span></a> ' +
                                
                                '<span style="margin-right:5px;color: #88A6CC;" class="glyphicon glyphicon-sort sort-filter pull-right"></span>&nbsp;'+ add_filter_button(current_id)
                                + '<span class="property-name">' + property.name + '</span>' + add_text_type(current_search_widget) +'</li>')
            });
        });
    }
    
</script>