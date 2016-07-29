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
                var sortedIds = $( "#compounds_properties_ordenation" ).sortable("toArray");
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
                var selKeys = $.map($("#dynatree_properties_filter").dynatree("getSelectedNodes"), function (node) {
                    return node;
                });
                var keys = $.map($("#dynatree_properties_filter").dynatree("getSelectedNodes"), function (node) {
                    return node.data.key;
                });
                if(selKeys.length>0&&selKeys.length<5){
                    $.each(selKeys,function(index,node){
                        $( "#compounds_properties_ordenation" ).append('<li id="'+node.data.key+'"><span class="glyphicon glyphicon-sort sort-filter pull-right"></span>&nbsp;'+node.data.title+'</li>')
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
            list_collection_metadata();
            getRequestFeedback(elem.type, elem.msg);
        });
    });
    
</script>