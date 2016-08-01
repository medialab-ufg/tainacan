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
                if(selKeys.length>0&&selKeys.length<5){
                    $.each(selKeys,function(index,node){
                        var type = $('#property_type_'+node.data.key).val();
                        $( "#compounds_properties_ordenation" ).append('<li id="compounds-'+node.data.key+'">'+
                                '<a onclick="edit_metadata(' + node.data.key + ')" class="edit_property_data" href="javascript:void(0)">' +
                                '<span style="margin-right:5px;" class="glyphicon glyphicon-edit pull-right"><span></a> ' +
                                '<a onclick="delete_property(' + node.data.key + ','+type+')" class="delete_property" href="#">' +
                                '<span style="margin-right:5px;" class="glyphicon glyphicon-trash pull-right"><span></a>' +
                                '<span style="margin-right:5px;" class="glyphicon glyphicon-sort sort-filter pull-right"></span>&nbsp;'+ add_filter_button(node.data.key) + node.data.title+'</li>')
                    })
                    $('#compounds_id').val(keys.join(','));
                }else if(selKeys.length>4){
                    node.select(false);
                }
                accordeon_ordenation_properties();
            }
       });
    }
    //SUBMISSAO DO METADADO COMPOSTA
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
    
</script>