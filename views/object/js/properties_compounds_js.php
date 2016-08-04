<script>
    $(function () {
        // # - inicializa o campos das propriedades de termo compostas 
        compounds_list_properties_term_insert_objects();
    });
    function autocomplete_object_property_compound(property_id, object_id) {
        $("#autocomplete_value_" + property_id + "_" + object_id).autocomplete({
            source: $('#src').val() + '/controllers/object/object_controller.php?operation=get_objects_by_property_json&property_id=' + property_id,
            messages: {
                noResults: '',
                results: function () {
                }
            },
            minLength: 2,
            select: function (event, ui) {
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
                    $("#property_value_" + property_id + "_" + object_id+"_edit option").each(function(){
                        if($(this).val()==ui.item.value){
                            already_selected = true;
                        }
                    });
                    if(!already_selected){
                        if($('#cardinality_'+property_id + "_" + object_id).val()=='1'){
                             $("#property_value_" + property_id + "_" + object_id + "_edit").html('');
                        }
                        $("#property_value_" + property_id + "_" + object_id+"_edit").append("<option class='selected' value='" + ui.item.value + "' selected='selected' >" + ui.item.label + "</option>");
                        //hook para validacao do campo ao selecionar
                        if(Hook.is_register( 'tainacan_validate_cardinality_onselect')){
                            Hook.call( 'tainacan_validate_cardinality_onselect', [ 'select[name="socialdb_property_'+property_id+'[]"]',property_id ] );
                        }
                    }
                }
                setTimeout(function () {
                    $("#autocomplete_value_" + property_id + "_" + object_id).val('');
                }, 100);
            }
        });
    }
    
    
    
     function clear_select_object_property_compound(e,property_id,object_id) {
        $('option:selected', e).remove();
         $("#property_value_" + property_id + "_" + object_id+"_edit option").each(function()
        {
           $(this).attr('selected','selected');
        });
        //validacao do campo
        var cont = 0;
        $("#property_value_" + property_id + "_" + object_id + "_edit option").each(function ()
        {
            cont++;
        });
        if(cont==0){
            $('#core_validation_'+property_id).val('false');
            set_field_valid(property_id,'core_validation_'+property_id);
        }            
        //fim validacao do campo
        if(Hook.is_register( 'tainacan_validate_cardinality_onselect')){
            Hook.call( 'tainacan_validate_cardinality_onselect', [ 'select[name="socialdb_property_'+property_id+'[]"]',property_id ] );
        }
        //$('.chosen-selected2 option').prop('selected', 'selected');
    }
    //************************* properties terms ******************************************//
    function compounds_list_properties_term_insert_objects() {
        var categories = compounds_get_val($("#edit_object_categories_id").val());
        var radios = compounds_get_val($("#properties_terms_radio").val());
        var selectboxes = compounds_get_val($("#properties_terms_selectbox").val());
        var trees = compounds_get_val($("#properties_terms_tree").val());
        var checkboxes = compounds_get_val($("#properties_terms_checkbox").val());
        var multipleSelects = compounds_get_val($("#properties_terms_multipleselect").val());
        var treecheckboxes = compounds_get_val($("#properties_terms_treecheckbox").val());
        compounds_list_radios(radios,categories);
        compounds_list_tree(trees,categories);
        compounds_list_selectboxes(selectboxes,categories);
        compounds_list_multipleselectboxes(multipleSelects,categories);
        compounds_list_checkboxes(checkboxes,categories);
        compounds_list_treecheckboxes(treecheckboxes,categories);
    }
    // radios
    function compounds_list_radios(radios,categories) {
        if (radios) {
            $.each(radios, function (idx, radio) {
                if($('#cardinality_compound_'+radio).length>0){
                    var cardinality = $('#cardinality_compound_'+radio).val();
                    var xhr = $.ajax({
                            url: $('#src').val() + '/controllers/property/property_controller.php',
                            type: 'POST',
                            data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: radio}
                    });
                    xhr.done(function (result) {
                         for(var i = 0;i<cardinality;i++){
                                elem = jQuery.parseJSON(result);
                                $('#field_property_term_' + radio).html('');
                                $.each(elem.children, function (idx, children) {
                                    var required = '';
                                    var checked = '';
                                    if(elem.metas.socialdb_property_required==='true'){
                                        required = ' onchange="validate_radio(' + radio + ')"';
                                    }
                                    //  if (property.id == selected) {
                                    //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                                    //  } else {
                                    if(categories&&categories.indexOf(children.term_id)>-1){
                                        checked = 'checked="checked"';
                                    }
                                    if (typeof delete_value === "function") {
                                        delete_value(children.term_id);
                                    }
                                    $('#field_property_term_' + radio + '_' + i).append('<input '+checked+' '+required+' type="radio" name="socialdb_property_'+ radio +'_'+ i +'[]" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                                    //  }
                                });
                        }
                    });
                }
            });
        }
    }
    // checkboxes
    function compounds_list_checkboxes(checkboxes,categories) {
        if (checkboxes) {
            $.each(checkboxes, function (idx, checkbox) {
                if($('#cardinality_compound_'+checkbox).length>0){
                    var cardinality = $('#cardinality_compound_'+checkbox).val();
                    var xhr = $.ajax({
                            url: $('#src').val() + '/controllers/property/property_controller.php',
                            type: 'POST',
                            data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: checkbox}
                    });
                    
                    xhr.done(function (result) {
                        for(var i = 0;i<cardinality;i++){
                            elem = jQuery.parseJSON(result);
                            $('#field_property_term_' + checkbox).html('');
                            $.each(elem.children, function (idx, children) {
                                var required = '';
                                var checked = '';
                                if (typeof delete_value === "function") {
                                    delete_value(children.term_id);
                                }
                                if(elem.metas.socialdb_property_required==='true'){
                                    required = 'onchange="validate_checkbox(' + checkbox + ')"';
                                }
                                if(categories&&categories.indexOf(children.term_id)>-1){
                                    checked = 'checked="checked"';
                                }
                                //  if (property.id == selected) {
                                //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                                //  } else {
                                $('#field_property_term_' + checkbox + '_' + i).append('<input '+checked+' '+required+'  type="checkbox" name="socialdb_property_'+checkbox+'_' + i + '[]" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                                //  }
                            });
                        }
                    });
                    
                }
            });
        }
    }
    // selectboxes
    function compounds_list_selectboxes(selectboxes,categories) {
        if (selectboxes) {
            $.each(selectboxes, function (idx, selectbox) {
                if($('#cardinality_compound_'+selectbox).length>0){
                    var cardinality = $('#cardinality_compound_'+selectbox).val();
                    var xhr = $.ajax({
                            url: $('#src').val() + '/controllers/property/property_controller.php',
                            type: 'POST',
                            data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: selectbox}
                        })
                    xhr.done(function (result) {
                        for(var i = 0;i<cardinality;i++){
                            elem = jQuery.parseJSON(result);
                            $('#field_property_term_' + selectbox + '_' + i).html('');
                            $('#field_property_term_' + selectbox + '_' + i).append('<option value=""><?php _e('Select','tainacan') ?>...</option>');
                            $.each(elem.children, function (idx, children) {
                                var checked = '';
                                if (typeof delete_value === "function") {
                                    delete_value(children.term_id);
                                }
                                 
                                if(categories&&categories.indexOf(children.term_id)>-1){
                                    checked = 'selected="selected"';
                                    $('#core_validation_'+selectbox).val('true');
                                    set_field_valid(selectbox,'core_validation_'+selectbox);
                                }
                                $('#field_property_term_' + selectbox + '_' + i).append('<option '+checked+' value="' + children.term_id + '">' + children.name + '</option>');
                                if(checked!==''){
                                   $('#field_property_term_' + selectbox + '_' + i).trigger('change');
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
    function compounds_list_multipleselectboxes(multipleSelects,categories) {
        if (multipleSelects) {
            $.each(multipleSelects, function (idx, multipleSelect) {
                if($('#cardinality_compound_'+multipleSelect).length>0){
                    var cardinality = $('#cardinality_compound_'+multipleSelect).val();
                    var xhr = $.ajax({
                            url: $('#src').val() + '/controllers/property/property_controller.php',
                            type: 'POST',
                            data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: multipleSelect}
                        });
                     xhr.done(function (result) {    
                        for(var i = 0;i<cardinality;i++){
                            //validation
                            $('#field_property_term_' + multipleSelect + '_' + i).select(function(){
                                if( $("#field_property_term_" + multipleSelects).val()===''){
                                    $('#core_validation_'+multipleSelect).val('false');
                                }else{
                                     $('#core_validation_'+multipleSelect).val('true');
                                }
                                set_field_valid(multipleSelect,'core_validation_'+multipleSelect);
                            });
                            //init
                            elem = jQuery.parseJSON(result);
                            $('#field_property_term_' + multipleSelect+ '_' + i).html('');
                            $.each(elem.children, function (idx, children) {
                                var checked = '';
                                if (typeof delete_value === "function") {
                                    delete_value(children.term_id);
                                }
                                console.log(categories);
                                if(categories.indexOf(children.term_id)>-1){
                                    checked = 'selected="selected"';
                                }
                                $('#field_property_term_' + multipleSelect + '_' + i).append('<option '+checked+' value="' + children.term_id + '">' + children.name + '</option>');
                                //  }
                            });
                        }
                    });
                }    
            });
        }
    }
    // treecheckboxes
    function compounds_list_treecheckboxes(treecheckboxes,categories) {
        if (treecheckboxes) {;
            $.each(treecheckboxes, function (idx, treecheckbox) {
                 if($('#cardinality_compound_'+treecheckbox).length>0){
                    var cardinality = $('#cardinality_compound_'+treecheckbox).val();
                    for(var i = 0;i<cardinality;i++){
                        $("#field_property_term_"+ treecheckbox  + '_' + i ).dynatree({
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
                                // Close menu on click
                                delete_value(node.data.key);
                                $("#property_object_category_id").val(node.data.key);
                                $("#property_object_category_name").val(node.data.title);

                            },
                            onCreate: function (node, span) {
//                                $("#field_property_term_"+treecheckbox  + '_' + i).dynatree("getRoot").visit(function(node){
//                                    if (typeof delete_value === "function") {
//                                        delete_value(node.data.key);
//                                    }
//                                    if(categories&&categories.indexOf(node.data.key)>-1){
//                                        node.select();
//                                    }
//                                });
                                 bindContextMenuSingle(span,'field_property_term_' + treecheckbox  + '_' + i);
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


                                $("#socialdb_propertyterm_" + treecheckbox + '_' + i).html('');
                                $.each(selKeys, function (index, key) {
                                    cont++;
                                    $("#socialdb_propertyterm_" + treecheckbox + '_' + i).append('<input type="hidden" name="socialdb_property_'+treecheckbox+'_' + i + '[]" value="' + key.data.key + '" >');
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
                    }
                }    
            });
        }
    }
    
    // tree
    function compounds_list_tree(trees,categories) {
        if (trees) {
            $.each(trees, function (idx, tree) {
                if($('#cardinality_compound_'+tree).length>0){
                    var cardinality = $('#cardinality_compound_'+tree).val();
                    for(var i = 0;i<cardinality;i++){
                         $("#field_property_term_" + tree + '_' + i).dynatree({
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
//                                       if(categories&&categories.indexOf(node.data.key)>-1){
//                                            node.select();
//                                        }
//                                    });
                                    bindContextMenuSingle(span,'field_property_term_' + tree + '_');
                                },
                                onSelect: function (flag, node) {
                                    if ($("#socialdb_property_" + tree + '_' + i).val() === node.data.key) {
                                        append_category_properties(0,node.data.key);
                                        $("#socialdb_property_" + tree + '_' + i).val("");
                                         $('#core_validation_' + tree + '_' + i).val('false');
                                         set_field_valid(tree,'core_validation_' + tree + '_' + i);
                                    } else {
                                        append_category_properties(node.data.key,$("#socialdb_propertyterm_" + tree).val());
                                        $("#socialdb_property_" + tree + '_' + i).val(node.data.key);
                                        $('#core_validation_'+tree + '_' + i).val('true');
                                         set_field_valid(tree,'core_validation_' + tree + '_' + i);
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

</script>
