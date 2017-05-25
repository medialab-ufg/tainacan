<script>
    var dynatree_object_index = [];
    $(function () {
        $("textarea").on("keydown",function(e) {
            var key = e.keyCode;
            // If the user has pressed enter
            if (key == 13) {
                $(this).val($(this).val()+"\n");
                return false;
            }
            else {
                return true;
            }
        });
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
                    var cardinality = $('#cardinality_'+ compound_id ).val();
                    for(var i = 0;i<cardinality;i++){
                        dynatree_object_index["compounds_"+compound_id+"_"+ property_id  + '_' + i] = i;
                        if( $(".form_autocomplete_compounds_" + property_id + '_'+i).length==0){
                            return false;
                        }
                        //validate
                        $(".form_autocomplete_compounds_" + property_id + '_'+i).keyup(function(){
                            var cont = 0;
                            var i =  $(this).attr('id').split('_')[3];
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
                            var i =  $(this).attr('id').split('_')[3];
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
                            minLength: 1,
                            response: function( event, ui ) {
                                if(ui.content && ui.content.length>0 && $('.form_autocomplete_value_'+property_id+'_mask').val()!==''){
                                   $.each(ui.content,function(index,value){
                                       if($(event.target).val()==value.value || $(event.target).val().toLowerCase().trim()==value.value){
                                            toastr.error($(event.target).val()+' <?php _e(' is already inserted!', 'tainacan') ?>', '<?php _e('Attention!', 'tainacan') ?>', {positionClass: 'toast-bottom-right'});
                                            $(event.target).val('');
                                       }
                                       $(event.target).autocomplete('close');
                                   }); 
                                }
                                clear_repeated_values(event.target,".form_autocomplete_compounds_" + property_id + '_'+i);
                            },
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
                    $("#property_value_"+compound_id+"_" + property_id + "_" + object_id+"_edit option").each(function(){
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
                    //var cardinality = $('#cardinality_compound_'+compound_id+'_'+radio).val();
                    var cardinality = $('#cardinality_'+compound_id).val();
                    var xhr = $.ajax({
                            url: $('#src').val() + '/controllers/property/property_controller.php',
                            type: 'POST',
                            data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: radio}
                    });
                    xhr.done(function (result) {
                         for(var i = 0;i<cardinality;i++){
                                elem = jQuery.parseJSON(result);
                                $('#field_property_term_'+compound_id+'_' + radio + '_' + i).html('');
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
                    //var cardinality = $('#cardinality_compound_'+ compound_id +'_'+checkbox).val();
                    var cardinality = $('#cardinality_'+ compound_id ).val();
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
                    //var cardinality = $('#cardinality_compound_'+compound_id+'_'+selectbox).val();
                    var cardinality = $('#cardinality_'+compound_id).val();
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
                            $('#field_property_term_'+compound_id+'_' + selectbox + '_' + i).append('<option value=""><?php _e('Select','tainacan') ?>...</option>');
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
                    //var cardinality = $('#cardinality_compound_'+compound_id+'_'+multipleSelect).val();
                    var cardinality = $('#cardinality_'+compound_id).val();
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
                    //var cardinality = $('#cardinality_compound_'+compound_id+'_'+treecheckbox).val();
                    var cardinality = $('#cardinality_'+compound_id).val();
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
                                var i =  this.$tree[0].id.split('_')[5];
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
                
                    //console.log('#cardinality_compound_'+compound_id+'_'+tree);
                if($('#cardinality_compound_'+compound_id+'_'+tree).length>0){
                    //var cardinality = $('#cardinality_compound_'+compound_id+'_'+tree).val();
                    var cardinality = $('#cardinality_'+compound_id).val();
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
                                    if(value&&value==node.data.key){
                                        node.select();
                                    }
                                    if(categories.length>0&&categories.indexOf(node.data.key)>=0){
                                        node.select();
                                    }
                                    bindContextMenuSingle(span,'field_property_term_' + tree + '_');
                                },
                                onSelect: function (flag, node) {
                                    var i =  this.$tree[0].id.split('_')[5];
                                    if ($("#field_property_term_"+compound_id+"_" + tree + '_' + i).val() === node.data.key) {
                                        append_category_properties(0,node.data.key);
                                         $('[name="socialdb_property_'+compound_id+'_'+tree+'_'+i+'[]"]').val("");
                                         $('#core_validation_'+compound_id+'_' + tree + '_' + i).val('false');
                                         set_field_valid_compounds(tree,'core_validation_'+compound_id+'_' + tree + '_' + i,compound_id);
                                    } else {
                                        append_category_properties(node.data.key,$("#field_property_term_"+compound_id+"_" + tree).val());
                                          $('[name="socialdb_property_'+compound_id+'_'+tree+'_'+i+'[]"]').val(node.data.key);
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
                if(!$('#core_validation_'+property_id+'_'+properties[i]+'_'+((id+1))).attr('validate_compound')) {
                    $('#ok_field_'+property_id+'_'+properties[i]+'_'+((id+1))).hide();
                    $('#required_field_'+property_id+'_'+properties[i]+'_'+((id+1))).show();
                    $('#core_validation_'+property_id+'_'+properties[i]+'_'+((id+1))).val('false');
                }
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
        <?php if($references['is_edit']): ?>
        if($('#button_property_'+property_id+'_'+(id-1)).length == 0){
            $('#container_field_'+property_id+'_'+(id-1)).append("<button type='button' id='button_property_"+property_id+"_"+(id-1)+"' onclick='show_fields_metadata_cardinality_compounds("+property_id+","+(id-1)+")'"+
                    "style='margin-top: 5px;' class='btn btn-primary btn-lg btn-xs btn-block'>" +
                    "<span class='glyphicon glyphicon-plus'></span>Adicionar campo</button>");
        }else{
            $('#button_property_'+property_id+'_'+(id-1)).show();
        }
        <?php else: ?>
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
        <?php endif; ?>
        
    }
    //################################ VALIDACOES##############################################//
    /**
     * funcao que valida os campos radios, e realiza a insercao das propriedades de categorias
     * @param {type} property_id
     * @returns {undefined}     */
    function compounds_validate_radio(property_id,i,compound_id){
        var selected = $("input[type='radio'][name='socialdb_property_"+compound_id+"_"+property_id+"_"+i+"[]']:checked");
        if (selected.length > 0) {
            //append_category_properties($(selected[0]).val(), $('#actual_field_term_'+compound_id+"_"+property_id+"_"+i).val());
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
             //   append_category_properties($(this).val());
            }else{
              //  append_category_properties(0,$(this).val());
            }
        });
    }
    /**
     * funcao que valida os campos de selecao unica
     * @param {type} seletor
     * @param {type} property_id
     * @returns {undefined}     */
    function compounds_validate_selectbox(seletor,property_id,compound_id,i){
        var selected = $("#field_property_term_"+compound_id+"_"+property_id+"_"+i).find(":selected");
        //console.log(selected);
        if (selected.length > 0) {
            $('#core_validation_'+compound_id+'_'+property_id+'_'+i).val('true');
            set_field_valid_compounds(property_id,'core_validation_'+compound_id+'_'+property_id+'_'+i,compound_id);
            //verificando se existe propriedades para serem  adicionadas
            $.each($("#field_property_term_"+compound_id+"_"+property_id+"_"+i+" option"),function(index,value){
                if($(this).is(':selected')){
                   // append_category_properties($(this).val(),0,compound_id);
                }else{
                   // append_category_properties(0,$(this).val(),compound_id);
                }
            });
        }else{
            $('#core_validation_'+compound_id+'_'+property_id+'_'+i).val('false');
            set_field_valid_compounds(property_id,'core_validation_'+compound_id+'_'+property_id+'_'+i,compound_id);
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
                  //  append_category_properties($(this).val());
                }else{
                  //  append_category_properties(0,$(this).val());
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
        //console.log(seletor,$('#'+seletor).val(),seletor.split('_'));
        var index = (seletor.split('_')[4]) ? seletor.split('_')[4] : 0
        if($('#'+seletor).val()==='false'){
            var slug = seletor.replace('core_validation_','');
            $('#ok_field_'+slug).hide();
            $('#required_field_'+slug).show();
        }else{
            var slug = seletor.replace('core_validation_','');
            $('#ok_field_'+slug).show();
            $('#required_field_'+slug).hide();
            if($('#only_field_'+slug+' #core_validation_'+compound_id).length >0){
                $('#only_field_'+slug+' #core_validation_'+compound_id).val('true');
            }
        }
        validate_all_fields_compounds(compound_id,index);
    }
    
    function validate_all_fields_compounds(compound_id,index){
        var cont = 0;
        var seletor = ($('.core_validation_'+compound_id+'_'+index).length > 0) ? '.core_validation_'+compound_id+'_'+index : '#core_validation_'+compound_id;
        console.log(seletor,index);
        //if(!index){
            for(var i = 0;i<parseInt($("#cardinality_"+compound_id).val());i++){
                //if($('#container_field_'+compound_id+'_'+i).is(':visible')){
                    $( ".core_validation_compounds_"+compound_id+"_"+i).each(function( index ) {
                        if($( this ).val()==='false'){
                            cont++;
                        }
                    });
                //}
            }
//        }else{
//            $( ".core_validation_compounds_"+compound_id+"_"+index).each(function( index ) {
//                if($( this ).val()==='false'){
//                    cont++;
//                }
//            });
//        }
       //se for do tipo de um field apenas
        if($('#type_required_'+compound_id).length > 0){
            var total_fields = $('#count_fields_'+compound_id).val();
            if(cont!=total_fields){
                $('input[name="core_validation_'+compound_id+'"]').val('true');
                $(seletor).val('true');
                set_field_valid(compound_id,'core_validation_'+compound_id);
            }else{
                $('input[name="core_validation_'+compound_id+'"]').val('false');
                $(seletor).val('false');
                set_field_valid(compound_id,'core_validation_'+compound_id);
            }
            
        }else{
            if(cont===0){
                $(seletor).val('true');
                $('input[name="core_validation_'+compound_id+'"]').val('true');
                set_field_valid(compound_id,'core_validation_'+compound_id);
            }else{
                $('input[name="core_validation_'+compound_id+'"]').val('false');
                $(seletor).val('false');
                set_field_valid(compound_id,'core_validation_'+compound_id);
            }
        }
        console.log(cont,'compounds',seletor,'#core_validation_'+compound_id,$('#core_validation_'+compound_id).val());
    }

</script>
