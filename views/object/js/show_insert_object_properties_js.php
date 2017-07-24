<script>
    var checkbox_values = [];
    $(function () {
        var src = $('#src').val();
        list_properties_term_insert_objects();
        var properties_autocomplete = get_val($("#properties_autocomplete").val());
        autocomplete_property_data(properties_autocomplete); 
    });


    function autocomplete_object_property_add(property_id, object_id) {
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
                    $("#property_value_" + property_id + "_" + object_id+"_add option").each(function(){
                        if($(this).val()==ui.item.value){
                            already_selected = true;
                        }
                    });
                    if(!already_selected){
                        $("#property_value_" + property_id + "_" + object_id + "_add").append("<option class='selected' value='" + ui.item.value + "' selected='selected' >" + ui.item.label + "</option>");
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
    function autocomplete_property_data(properties_autocomplete) {
         if (properties_autocomplete) {
            $.each(properties_autocomplete, function (idx, property_id) {
                        $("#form_autocomplete_value_" + property_id).autocomplete({
                            source: $('#src').val() + '/controllers/collection/collection_controller.php?operation=list_items_search_autocomplete&property_id=' + property_id,
                            messages: {
                                noResults: '',
                                results: function () {
                                }
                            },
                            minLength: 2,
                            select: function (event, ui) {
                                $("#form_autocomplete_value_" + property_id).val('');
                                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                                var temp = $("#property_value_" + property_id).val();
                                if (typeof temp == "undefined") {
                                    $("#form_autocomplete_value_" + property_id).val(ui.item.value);
                                }
                            }
                        });
                    });
                }
    }

    function clear_select_object_property(e,property_id,object_id) {
        $('option:selected', e).remove();
        $("#property_value_" + property_id + "_" + object_id+"_add option").each(function()
        {
           $(this).attr('selected','selected');
        });
        if(Hook.is_register( 'tainacan_validate_cardinality_onselect')){
            Hook.call( 'tainacan_validate_cardinality_onselect', [ 'select[name="socialdb_property_'+property_id+'[]"]',property_id ] );
        }
        //$('.chosen-selected2 option').prop('selected', 'selected');
    }

//************************* properties terms ******************************************//
    function list_properties_term_insert_objects() {
        var radios = get_val($("#properties_terms_radio").val());
        var selectboxes = get_val($("#properties_terms_selectbox").val());
        var trees = get_val($("#properties_terms_tree").val());
        var checkboxes = get_val($("#properties_terms_checkbox").val());
        var multipleSelects = get_val($("#properties_terms_multipleselect").val());
        var treecheckboxes = get_val($("#properties_terms_treecheckbox").val());
        list_radios(radios);
        list_tree(trees);
        list_selectboxes(selectboxes);
        list_multipleselectboxes(multipleSelects);
        list_checkboxes(checkboxes);
        list_treecheckboxes(treecheckboxes);
    }
    // radios
    function list_radios(radios) {
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
                        if(elem.metas.socialdb_property_required==='true'){
                            required = 'required="required"';
                        }
                        //  if (property.id == selected) {
                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        //  } else {
                        $('#field_property_term_' + radio).append('<input '+required+' type="radio" name="socialdb_propertyterm_'+radio+'" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                        //  }
                    });
                });
            });
        }
    }
    // checkboxes
    function list_checkboxes(checkboxes) {
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
                        $('#field_property_term_' + checkbox).append('<input type="checkbox" name="socialdb_propertyterm_'+checkbox+'[]" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                    });
                    var required = '';
                    if(elem.metas.socialdb_property_required==='true'){
                        required = 'required';
                    }
                    $('#field_property_term_' + checkbox).append('<input type="hidden" name="checkbox_required_'+checkbox+'" value="'+required+'" >');
                });
            });
        }
    }
    
    // selectboxes
    function list_selectboxes(selectboxes) {
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
                        //  if (property.id == selected) {
                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        //  } else {
                        $('#field_property_term_' + selectbox).append('<option value="' + children.term_id + '">' + children.name + '</option>');
                        //  }
                    });
                });
            });
        }
    }
     // multiple
    function list_multipleselectboxes(multipleSelects) {
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
                        //  if (property.id == selected) {
                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        //  } else {
                        $('#field_property_term_' + multipleSelect).append('<option value="' + children.term_id + '">' + children.name + '</option>');
                        //  }
                    });
                });
            });
        }
    }
    // treecheckboxes
    function list_treecheckboxes(treecheckboxes) {
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
                                //operation: 'findDynatreeChild'
                                operation: 'expand_dynatree'
                            }
                        });
                    },
                    onClick: function (node, event) {
                        // Close menu on click
                        $("#property_object_category_id").val(node.data.key);
                        $("#property_object_category_name").val(node.data.title);

                    },
                    onKeydown: function (node, event) {
                    },
                    onCreate: function (node, span) {
//                        var key = node.data.key;
//                        var n = key.toString().indexOf("_");
//                        if (n > 0) {// se for propriedade de objeto
//                            values = key.split("_");
//                            if (values[1] === 'tag' || (values[1] === 'facet' && values[2] === 'tag')) {
//                                bindContextMenuSingleTag(span);
//                            } else if (values[1] === 'facet' && values[2] === 'category') {
//                                bindContextMenuSingle(span);
//                            }
//                        } else {
//                            bindContextMenuSingle(span);
//                        }

                        $('.dropdown-toggle').dropdown();
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
    function list_tree(trees) {
        if (trees) {
            $.each(trees, function (idx, tree) {
                $("#field_property_term_"+tree).dynatree({
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
                            //hide_checkbox: 'true',
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
                                hide_count:'true',
                                classCss: node.data.addClass,
                                //operation: 'findDynatreeChild'
                                operation: 'expand_dynatree'
                            }
                        });
                    },
                    onClick: function (node, event) {
                        // Close menu on click
//                         var key = node.data.key;
//                        if(key.search('moreoptions')<0&&key.search('alphabet')<0){
//                            $("#socialdb_propertyterm_"+tree).html('');
//                            $("#socialdb_propertyterm_"+tree).append('<option selected="selected" value="'+node.data.key+'" >'+node.data.title+'</option>')
//                         }
                    },
                    onKeydown: function (node, event) {
                    },
                    onCreate: function (node, span) {
//                        var key = node.data.key;
//                        var n = key.toString().indexOf("_");
//                        if (n > 0) {// se for propriedade de objeto
//                            values = key.split("_");
//                            if (values[1] === 'tag' || (values[1] === 'facet' && values[2] === 'tag')) {
//                                bindContextMenuSingleTag(span);
//                            } else if (values[1] === 'facet' && values[2] === 'category') {
//                                bindContextMenuSingle(span);
//                            }
//                        } else {
//                            bindContextMenuSingle(span);
//                        }

                        $('.dropdown-toggle').dropdown();
                    },
                    onPostInit: function (isReloading, isError) {
                    },
                    onActivate: function (node, event) {
                    },
                    onSelect: function (flag, node) {
                        $("#socialdb_propertyterm_"+tree).html("");
                        $("#socialdb_propertyterm_"+tree).append('<option selected="selected" value="'+node.data.key+'" >'+node.data.title+'</option>')
                       
                    },
                    dnd: {
                    }
                });
            });
        }
    }
    
    
    
    // get value of the property
    function get_val(value) {
        if (value === ''||value===undefined) {
            return false;
        } else if (value.split(',')[0] === '' && value !== '') {
            return [value];
        } else {
            return value.split(',');
        }
    }

</script>
