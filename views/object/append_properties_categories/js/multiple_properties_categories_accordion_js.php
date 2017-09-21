<script>
    $(function () {
        pc_list_properties_term_insert_objects();
        $('.nav-tabs').tab();
        $('.dropdown-toggle').dropdown();
    });
    
//************************* properties terms (MOSTRA OS DADOS DE METADADOS DE TERMO) ******************************************//
    function pc_list_properties_term_insert_objects() {
        var radios = pc_multiple_get_val($("#pc_multiple_properties_terms_radio").val());
        var selectboxes = pc_multiple_get_val($("#pc_multiple_properties_terms_selectbox").val());
        var trees = pc_multiple_get_val($("#pc_multiple_properties_terms_tree").val());
        var checkboxes = pc_multiple_get_val($("#pc_multiple_properties_terms_checkbox").val());
        var multipleSelects = pc_multiple_get_val($("#pc_multiple_properties_terms_multipleselect").val());
        var treecheckboxes = pc_multiple_get_val($("#pc_multiple_properties_terms_treecheckbox").val());
        pc_multiple_list_radios(radios);
        pc_multiple_list_tree(trees);
        pc_multiple_list_selectboxes(selectboxes);
        pc_multiple_list_multipleselectboxes(multipleSelects);
        pc_multiple_list_checkboxes(checkboxes);
        pc_multiple_list_treecheckboxes(treecheckboxes);
    }
    // radios
    function pc_multiple_list_radios(radios) {
        if (radios) {
            $.each(radios, function (idx, radio) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: radio}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#multiple_field_property_term_' + radio).html('');
                    $.each(elem.children, function (idx, children) {
                        //  if (property.id == selected) {
                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        //  } else {
                        $('#multiple_field_property_term_' + radio).append('<input value="' + children.term_id + '" type="radio" onchange="setCategoriesRadio(' + radio + ',' + children.term_id + ',0)"  name="multiple_socialdb_propertyterm_' + radio + '" >&nbsp;' + children.name + '<br>');
                        //  }
                    });
                });
            });
        }
    }
    // checkboxes
    function pc_multiple_list_checkboxes(checkboxes) {
        if (checkboxes) {
            $.each(checkboxes, function (idx, checkbox) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: checkbox}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#multiple_field_property_term_' + checkbox).html('');
                    $.each(elem.children, function (idx, children) {
                        $('#multiple_field_property_term_' + checkbox).append('<input type="checkbox" onchange="setCategoriesCheckbox(' + checkbox + ',' + children.term_id + ')"  name="multiple_socialdb_propertyterm_' + checkbox + '" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                    });
                    // var required = '';
                    // if(elem.metas.socialdb_property_required==='true'){
                    //   required = 'required';
                    //}
                    //$('#multiple_field_property_term_' + checkbox).append('<input type="hidden" name="checkbox_required_'+checkbox+'" value="'+required+'" >');
                });
            });
        }
    }

    // selectboxes
    function pc_multiple_list_selectboxes(selectboxes) {
        if (selectboxes) {
            $.each(selectboxes, function (idx, selectbox) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: selectbox}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#multiple_field_property_term_' + selectbox).html('');
                    $('#multiple_field_property_term_' + selectbox).html('<option value=""><?php _e('Select...', 'tainacan') ?></option>');
                    $.each(elem.children, function (idx, children) {
                        //  if (property.id == selected) {
                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        //  } else {
                        $('#multiple_field_property_term_' + selectbox).append('<option value="' + children.term_id + '">' + children.name + '</option>');
                        //  }
                    });
                });
            });
        }
    }
    // multiple
    function pc_multiple_list_multipleselectboxes(multipleSelects) {
        if (multipleSelects) {
            $.each(multipleSelects, function (idx, multipleSelect) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: multipleSelect}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#multiple_field_property_term_' + multipleSelect).html('');
                    $.each(elem.children, function (idx, children) {
                        //  if (property.id == selected) {
                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        //  } else {
                        $('#multiple_field_property_term_' + multipleSelect).append('<option value="' + children.term_id + '">' + children.name + '</option>');
                        //  }
                    });
                });
            });
        }
    }
    // treecheckboxes
    function pc_multiple_list_treecheckboxes(treecheckboxes) {
        if (treecheckboxes) {
            $.each(treecheckboxes, function (idx, treecheckbox) {
                $("#multiple_field_property_term_" + treecheckbox).dynatree({
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
                        //$("#property_object_category_id").val(node.data.key);
                        // $("#property_object_category_name").val(node.data.title);

                    },
                    onKeydown: function (node, event) {
                    },
                    onCreate: function (node, span) {
                        bindContextMenuSingle(span,'multiple_field_property_term_' + treecheckbox);
                    },
                    onPostInit: function (isReloading, isError) {
                    },
                    onActivate: function (node, event) {
                    },
                    onSelect: function (flag, node) {
                        var selKeys = $.map(node.tree.getSelectedNodes(), function (node) {
                            return node.data.key;
                        });
                        setCategoriesTree(treecheckboxes, selKeys.join(','));
                        //
                        var categories = $.map(node.tree.getSelectedNodes(), function (node) {
                            return node.data.key;
                        });
                        if(categories.length>0&&categories.indexOf(node.data.key)>=0){
                            append_category_properties(node.data.key);
                        }else{
                            append_category_properties(0,node.data.key);
                        }
                    },
                    dnd: {
                    }
                });
            });
        }
    }

    // tree
    function pc_multiple_list_tree(trees) {
        if (trees) {
            $.each(trees, function (idx, tree) {
                $("#multiple_field_property_term_" + tree).dynatree({
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
                                // hide_checkbox: 'true',
                                classCss: node.data.addClass,
                                //operation: 'findDynatreeChild'
                                operation: 'expand_dynatree'
                            }
                        });
                    },
                    onClick: function (node, event) {
                        // Close menu on click
                        var key = node.data.key;
                        if (key.search('moreoptions') < 0 && key.search('alphabet') < 0) {
                            $("#socialdb_propertyterm_" + tree).html('');
                            $("#socialdb_propertyterm_" + tree).append('<option selected="selected" value="' + node.data.key + '" >' + node.data.title + '</option>')
                        }
                    },
                    onKeydown: function (node, event) {
                    },
                    onCreate: function (node, span) {
                        bindContextMenuSingle(span,'multiple_field_property_term_' + tree);
                    },
                    onPostInit: function (isReloading, isError) {
                    },
                    onActivate: function (node, event) {
                    },
                    onSelect: function (flag, node) {
                        setCategoriesTree(tree, node.data.key);
                        if ($("#socialdb_propertyterm_" + tree).val() === node.data.key) {
                            append_category_properties(0,node.data.key);
                            $("#socialdb_propertyterm_" + tree).val("");
                        } else {
                            append_category_properties(node.data.key,$("#socialdb_propertyterm_" + tree).val());
                            $("#socialdb_propertyterm_" + tree).val(node.data.key);
                        }
                    },
                    dnd: {
                    }
                });
            });
        }
    }


    /*********************** FUNCOES DE SUPORTE ********************************/
    
    // get value of the property
    function pc_multiple_get_val(value) {
        if (value === '') {
            return false;
        } else if (value.split(',')[0] === '' && value !== '') {
            return [value];
        } else {
            return value.split(',');
        }
    }
</script>
