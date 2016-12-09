<script>
    function remove_search_word() {
        var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function (node) {
            return node.data.key;
        });
        $("#value_search").val('');
        $("#search_objects").val('');
        $("#search_collections").val('');
        $("#search_collection_field").val('');
       // list_main_ordenation();
        wpquery_remove('keyword','_','_');
        //list_all_objects(selKeys.join(", "), $("#collection_id").val(), $('#collection_single_ordenation').val());
    }
    function remove_search_author() {
        $("#value_search").val('');
        $("#search_objects").val('');
        $("#search_collections").val('');
        $("#search_collection_field").val('');
       // list_main_ordenation();
        wpquery_remove('author','_','_');
        //list_all_objects(selKeys.join(", "), $("#collection_id").val(), $('#collection_single_ordenation').val());
    }
    function remove_filter_category(facet,key) {
        var nod;
        $('.remove-link-filters').hide(); 
         //wpquery_remove('facets',facet,key);
        $("#dynatree").dynatree("getRoot").visit(function (node) {
            if(node.data.key===key.trim()){
              $('#flag_dynatree_ajax').val('false'); 
              node.select(false);
              $(node.span).removeClass('dynatree-selected');
            }
        });
        var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function (node) {
            return node.data.key;
        });
        //multipleselect box
        $('#option_'+facet+'_'+key).remove();
        //select box
        $('#facet_'+facet).val('');
        //radio
        $('input[name=facet_'+facet+']').prop('checked', false);
        $('#checkbox_'+facet+'_'+key).prop('checked', false);
         wpquery_remove('facets',facet,key);
   }
    
    function remove_filter_tag(key) {
        $('.remove-link-filters').hide(); 
        $("#dynatree").dynatree("getRoot").visit(function (node) {
            if(node.data.key===key.trim()+'_tag'){
               $('#flag_dynatree_ajax').val('false'); 
               node.select(false);
            }
        });
        var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function (node) {
            return node.data.key;
        });
        wpquery_remove('tags','_',key);
    }
    
    function remove_filter_property_object_tree(property,key) {
        $('.remove-link-filters').hide(); 
        $("#dynatree").dynatree("getRoot").visit(function (node) {
            if(node.data.key===key.trim()+'_'+property.trim()){
               $('#flag_dynatree_ajax').val('false'); 
               node.select(false);
            }
        });
        var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function (node) {
            return node.data.key;
        });
        wpquery_remove('properties_object_tree',property,key);
    }
    function remove_filter_property_data_tree(property,key) {
        $('.remove-link-filters').hide(); 
        $("#dynatree").dynatree("getRoot").visit(function (node) {
            if(node.data.key===key.trim()+'_'+property.trim()+'_datatext'){
               $('#flag_dynatree_ajax').val('false'); 
               node.select(false);
            }
        });
        var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function (node) {
            return node.data.key;
        });
        wpquery_remove('properties_data_tree',property,key);
    }
    //remover filtro de propriedade de dados apenas com seu valor vindo de nenhum widget (LINKS avulsos)
    function remove_filter_property_data_link(property,key) {
        $('.remove-link-filters').hide(); 
        wpquery_remove('properties_data_link',property,key);
    }
    //remover as licensas do dynatree
     function remove_licenses_tree(key) {
        $('.remove-link-filters').hide(); 
        $("#dynatree").dynatree("getRoot").visit(function (node) {
            if(node.data.key===key.trim()+'_license'){
               $('#flag_dynatree_ajax').val('false'); 
               node.select(false);
            }
        });
        var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function (node) {
            return node.data.key;
        });
        wpquery_remove('license_tree','||',key);
    }
    //remover os tipos do dynatree
     function remove_type_tree(key) {
        $('.remove-link-filters').hide(); 
        $("#dynatree").dynatree("getRoot").visit(function (node) {
            if(node.data.key===key.trim()+'_type'){
               $('#flag_dynatree_ajax').val('false'); 
               node.select(false);
            }
        });
        var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function (node) {
            return node.data.key;
        });
        wpquery_remove('type_tree','||',key);
    }
    //remover os formatos do dynatree
     function remove_format_tree(key) {
        $('.remove-link-filters').hide(); 
        $("#dynatree").dynatree("getRoot").visit(function (node) {
            if(node.data.key===key.trim()+'_format'){
               $('#flag_dynatree_ajax').val('false'); 
               node.select(false);
            }
        });
        var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function (node) {
            return node.data.key;
        });
        wpquery_remove('format_tree','||',key);
    }
    //remover a fonte
     function remove_source_tree(key) {
        $('.remove-link-filters').hide(); 
        $("#dynatree").dynatree("getRoot").visit(function (node) {
            if(node.data.key===key.trim()+'_source'){
               $('#flag_dynatree_ajax').val('false'); 
               node.select(false);
            }
        });
        var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function (node) {
            return node.data.key;
        });
        wpquery_remove('source_tree','_',key);
    }
    
    function remove_filter_property_data_fromto_number(facet,key){
         $('#facet_'+facet+'_1').val('');
         $('#facet_'+facet+'_2').val('');
         wpquery_remove('properties_data_fromto_numeric',facet,key);
    }
    
    function remove_filter_property_data_fromto_date(facet,key){
         $('#facet_'+facet+'_1').val('');
         $('#facet_'+facet+'_2').val('');
         wpquery_remove('properties_data_fromto_date',facet,key);
    }
    
    function remove_filter_property_data_range_date(facet,key){
         wpquery_remove('properties_data_range_date',facet,key);
    }
    
    function remove_filter_property_data_range_numeric(facet,key){
         wpquery_remove('properties_data_range_numeric',facet,key);
    }
    
    function remove_filter_property_multipleselect(facet,key){
         $('#option_'+facet+'_'+key.replace(/\s+/, "")).remove();
         wpquery_remove('properties_multipleselect',facet,key);
    }
</script>
