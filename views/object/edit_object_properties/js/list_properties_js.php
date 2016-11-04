<script>
    $(function () {
        var src = $('#src').val();
        edit_list_properties_term_insert_objects();
        var properties_autocomplete = edit_get_val($("#edit_properties_autocomplete").val());
        //autocomplete_edit_item_property_data(properties_autocomplete); 
    });

   
    function autocomplete_object_property_edit(property_id, object_id) {
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
                    $("#property_value_" + property_id + "_" + object_id+"_edit option").each(function(){
                        if($(this).val()==ui.item.value){
                            already_selected = true;
                        }
                    });
                    if(!already_selected){
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
    
     /**
     * Autocomplete para os metadados de dados para insercao/edicao de item unico
     * @param {type} e
     * @returns {undefined}
     */
//    function autocomplete_edit_item_property_data(properties_autocomplete) {
//         if (properties_autocomplete) {
//            $.each(properties_autocomplete, function (idx, property_id) {
//                       
//                        $("#form_edit_autocomplete_value_" + property_id).autocomplete({
//                            source: $('#src').val() + '/controllers/collection/collection_controller.php?operation=list_items_search_autocomplete&property_id=' + property_id,
//                            messages: {
//                                noResults: '',
//                                results: function () {
//                                }
//                            },
//                            minLength: 2,
//                            select: function (event, ui) {
//                                $("#form_edit_autocomplete_value_" + property_id).val('');
//                                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
//                                var temp = $("#form_edit_autocomplete_value_" + property_id).val();
//                                if (typeof temp == "undefined") {
//                                    $("#form_edit_autocomplete_value_" + property_id).val(ui.item.value);
//                                }
//                            }
//                        });
//                    });
//                }
//    }
    
     function clear_select_object_property(e,property_id,object_id) {
        $('option:selected', e).remove();
         $("#property_value_" + property_id + "_" + object_id+"_edit option").each(function()
        {
           $(this).attr('selected','selected');
        });
        if(Hook.is_register( 'tainacan_validate_cardinality_onselect')){
            Hook.call( 'tainacan_validate_cardinality_onselect', [ 'select[name="socialdb_property_'+property_id+'[]"]',property_id ] );
        }
        //$('.chosen-selected2 option').prop('selected', 'selected');
    }
    
    //************************* properties terms ******************************************//
    function edit_list_properties_term_insert_objects() {
        var categories = edit_get_val($("#edit_object_categories_id").val());
        var radios = edit_get_val($("#properties_terms_radio").val());
        var selectboxes = edit_get_val($("#properties_terms_selectbox").val());
        var trees = edit_get_val($("#properties_terms_tree").val());
        var checkboxes = edit_get_val($("#properties_terms_checkbox").val());
        var multipleSelects = edit_get_val($("#properties_terms_multipleselect").val());
        var treecheckboxes = edit_get_val($("#properties_terms_treecheckbox").val());
        edit_list_radios(radios,categories);
        edit_list_tree(trees,categories);
        edit_list_selectboxes(selectboxes,categories);
        edit_list_multipleselectboxes(multipleSelects,categories);
        edit_list_checkboxes(checkboxes,categories);
        edit_list_treecheckboxes(treecheckboxes,categories);
    }
    // radios
    function edit_list_radios(radios,categories) {
        if (radios) {
            $.each(radios, function (idx, radio) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: radio}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#field_property_term_' + radio).html('');
                    $.each(elem.children, function (idx, children) {
                        var required = '';
                        var checked = '';
                        if(elem.metas.socialdb_property_required==='true'){
                            required = 'required="required"';
                        }
                        //  if (property.id == selected) {
                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        //  } else {
                        if(categories.indexOf(children.term_id)>-1){
                            checked = 'checked="checked"';
                        }
                         delete_value(children.term_id);//retiro
                        $('#field_property_term_' + radio).append('<input '+checked+' '+required+' type="radio" name="socialdb_propertyterm_'+radio+'" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                        //  }
                    });
                });
            });
        }
    }
    // checkboxes
    function edit_list_checkboxes(checkboxes,categories) {
        if (checkboxes) {
            $.each(checkboxes, function (idx, checkbox) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: checkbox}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#field_property_term_' + checkbox).html('');
                    $.each(elem.children, function (idx, children) {
                        var required = '';
                        var checked = '';
                         delete_value(children.term_id);
                        if(elem.metas.socialdb_property_required==='true'){
                            required = 'required="required"';
                        }
                        if(categories.indexOf(children.term_id)>-1){
                            checked = 'checked="checked"';
                        }
                        //  if (property.id == selected) {
                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        //  } else {
                        $('#field_property_term_' + checkbox).append('<input '+checked+' '+required+' type="checkbox" name="socialdb_propertyterm_'+checkbox+'[]" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                        //  }
                    });
                });
            });
        }
    }
    // selectboxes
    function edit_list_selectboxes(selectboxes,categories) {
        if (selectboxes) {
            $.each(selectboxes, function (idx, selectbox) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: selectbox}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#field_property_term_' + selectbox).html('');
                    $.each(elem.children, function (idx, children) {
                        var checked = '';
                         delete_value(children.term_id);
                        if(categories.indexOf(children.term_id)>-1){
                            checked = 'selected="selected"';
                        }
                        $('#field_property_term_' + selectbox).append('<option '+checked+' value="' + children.term_id + '">' + children.name + '</option>');
                        //  }
                    });
                });
            });
        }
    }
     // multiple
    function edit_list_multipleselectboxes(multipleSelects,categories) {
        if (multipleSelects) {
            $.each(multipleSelects, function (idx, multipleSelect) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: multipleSelect}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#field_property_term_' + multipleSelect).html('');
                    $.each(elem.children, function (idx, children) {
                        var checked = '';
                        delete_value(children.term_id);
                        if(categories.indexOf(children.term_id)>-1){
                            checked = 'selected="selected"';
                        }
                        $('#field_property_term_' + multipleSelect).append('<option '+checked+' value="' + children.term_id + '">' + children.name + '</option>');
                        //  }
                    });
                });
            });
        }
    }
    // treecheckboxes
    function edit_list_treecheckboxes(treecheckboxes,categories) {
        if (treecheckboxes) {
            $.each(treecheckboxes, function (idx, treecheckbox) {
                $("#field_property_term_"+treecheckbox).dynatree({
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
                    onKeydown: function (node, event) {
                    },
                    onCreate: function (node, span) {
                         $("#field_property_term_"+treecheckbox).dynatree("getRoot").visit(function(node){
                            delete_value(node.data.key);
                           if(categories.indexOf(node.data.key)>-1){
                                node.select();
                            }
                        });
                    },
                    onPostInit: function (isReloading, isError) {
                    },
                    onActivate: function (node, event) {
                    },
                    onSelect: function (flag, node) {
                        var selKeys = $.map(node.tree.getSelectedNodes(), function (node) {
                            return node;
                        });
                        $("#socialdb_propertyterm_"+treecheckbox).html('');
                        $.each(selKeys,function(index,key){
                            $("#socialdb_propertyterm_"+treecheckbox).append('<option selected="selected" value="'+key.data.key+'" >'+key.data.title+'</option>')
                        });
                    },
                    dnd: {
                    }
                });
            });
        }
    }
    
    // tree
    function edit_list_tree(trees,categories) {
        if (trees) {
            $.each(trees, function (idx, tree) {
                $("#field_property_term_"+tree).dynatree({
                    selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
                    checkbox: true,
                      initAjax: {
                        url: $('#src').val() + '/controllers/category/category_controller.php',
                        data: {
                            collection_id: $("#collection_id").val(),
                            property_id: tree,
                            hide_checkbox: 'true',
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
                                hide_checkbox: 'true',
                                classCss: node.data.addClass,
                                 order: 'name',
                                //operation: 'findDynatreeChild'
                                operation: 'expand_dynatree'
                            }
                        });
                    },
                    onClick: function (node, event) {
                        // Close menu on click
                         var key = node.data.key;
                        if(key.search('moreoptions')<0&&key.search('alphabet')<0){
                            delete_value(node.data.key);
                            $("#socialdb_propertyterm_"+tree).html('');
                            $("#socialdb_propertyterm_"+tree).append('<option selected="selected" value="'+node.data.key+'" >'+node.data.title+'</option>');
                        }
                    },
                    onKeydown: function (node, event) {
                    },
                    onCreate: function (node, span) {
                         $("#field_property_term_"+tree).dynatree("getRoot").visit(function(node){
                           delete_value(node.data.key); 
                           if(categories.indexOf(node.data.key)>-1){
                                node.select();
                            }
                        });
                    },
                    onPostInit: function (isReloading, isError) {
                    },
                    onActivate: function (node, event) {
                    },
                    onSelect: function (flag, node) {
                        var selKeys = $.map(node.tree.getSelectedNodes(), function (node) {
                            return node;
                        });
                        $("#socialdb_propertyterm_"+tree).html('');
                        $.each(selKeys,function(index,key){
                            $("#socialdb_propertyterm_"+tree).append('<option selected="selected" value="'+key.data.key+'" >'+key.data.title+'</option>')
                        });
                    },
                    dnd: {
                    }
                });
            });
        }
    }
    
    
    
    // get value of the property
    function edit_get_val(value) {
        if (value === '') {
            return false;
        } else if (value.split(',')[0] === '' && value !== '') {
            return [value];
        } else {
            return value.split(',');
        }
    }
    
    function delete_value(category_id){
       if($("#object_classifications").length==0 || !$("#object_classifications").val()){
           return;
       }
       var classifications =   $("#object_classifications").val().split(',');
       if(classifications.length>0&&category_id){
           var index = classifications.indexOf(category_id);
           if(index>-1){
               classifications.splice(index, 1);
               $("#object_classifications").val(classifications.join());
           }
       }
    }

</script>
