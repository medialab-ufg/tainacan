<script>
    $(function () {
        var src = $('#src').val();
        search_list_properties_term_insert_objects();
        var search_properties_autocomplete = search_get_val($("#search_properties_autocomplete").val());
        autocomplete_object_property_add(search_properties_autocomplete);
        
        show_collection_licenses();
        //# - inicializa os tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
    
    function clear_general_field(){
        $('#advanced_search_general').val('');
    }
    
    function autocomplete_object_property_add(search_properties_autocomplete) {
         if (search_properties_autocomplete) {
            $.each(search_properties_autocomplete, function (idx, property_id) {
                        $("#autocomplete_value_" + property_id).autocomplete({
                            source: $('#src').val() + '/controllers/collection/collection_controller.php?operation=list_items_search_autocomplete&property_id=' + property_id,
                            messages: {
                                noResults: '',
                                results: function () {
                                }
                            },
                            minLength: 2,
                            select: function (event, ui) {
                                console.log(event);
                                $("#autocomplete_value_" + property_id).val('');
                                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                                var temp = $("#property_value_" + property_id).val();
                                if (typeof temp == "undefined") {
                                    $("#autocomplete_value_" + property_id).val(ui.item.value);
                                }
                            }
                        });
                    });
                }
    }

    function clear_select_object_property(e) {
        $('option:selected', e).remove();
        //$('.chosen-selected2 option').prop('selected', 'selected');
    }

//************************* properties terms ******************************************//
    function search_list_properties_term_insert_objects() {
        var radios = search_get_val($("#search_properties_terms_radio").val());
        var selectboxes = search_get_val($("#search_properties_terms_selectbox").val());
        var trees = search_get_val($("#search_properties_terms_tree").val());
        var checkboxes = search_get_val($("#search_properties_terms_checkbox").val());
        var multipleSelects = search_get_val($("#search_properties_terms_multipleselect").val());
        var treecheckboxes = search_get_val($("#search_properties_terms_treecheckbox").val());
        search_list_radios(radios);
        search_list_tree(trees);
        search_list_selectboxes(selectboxes);
        search_list_multipleselectboxes(multipleSelects);
        search_list_checkboxes(checkboxes);
        search_list_treecheckboxes(treecheckboxes);
    }
    // radios
    function search_list_radios(radios) {
        if (radios) {
            $.each(radios, function (idx, radio) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: radio}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#search_field_property_term_' + radio).html('');
                    $.each(elem.children, function (idx, children) {
                        var required = '';
                        if(elem.metas.socialdb_property_required==='true'){
                            required = 'required="required"';
                        }
                        //  if (property.id == selected) {
                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        //  } else {
                        var name = "'socialdb_propertyterm_"+radio+"'";
                        $('#search_field_property_term_' + radio)
                                .append('<input '+required+' onchange="onRadioChecked('+name+','+radio+')" type="radio" name="socialdb_propertyterm_'+radio+'" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                        //  }
                    });
                });
            });
        }
    }
    // checkboxes
    function search_list_checkboxes(checkboxes) {
        if (checkboxes) {
            $.each(checkboxes, function (idx, checkbox) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: checkbox}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#search_field_property_term_' + checkbox).html('');
                    var name =  "'socialdb_propertyterm_"+checkbox+"[]'";
                    $.each(elem.children, function (idx, children) {
                        $('#search_field_property_term_' + checkbox).append('<input onchange="onCheckboxValue('+name+','+checkbox+')" type="checkbox" name="socialdb_propertyterm_'+checkbox+'[]" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                    });
                    var required = '';
                    if(elem.metas.socialdb_property_required==='true'){
                        required = 'required';
                    }
                    //$('#search_field_property_term_' + checkbox).append('<input type="hidden" name="checkbox_required_'+checkbox+'" value="'+required+'" >');
                });
            });
        }
    }
    
    // selectboxes
    function search_list_selectboxes(selectboxes) {
        if (selectboxes) {
            $.each(selectboxes, function (idx, selectbox) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: selectbox}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#search_field_property_term_' + selectbox).html('');
                    $.each(elem.children, function (idx, children) {
                        //  if (property.id == selected) {
                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        //  } else {
                        $('#search_field_property_term_' + selectbox).append('<option value="' + children.term_id + '">' + children.name + '</option>');
                        //  }
                    });
                });
            });
        }
    }
    
    // multiple
    function search_list_multipleselectboxes(multipleSelects) {
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
                        $('#search_field_property_term_' + multipleSelect).append('<option value="' + children.term_id + '">' + children.name + '</option>');
                        //  }
                    });
                });
            });
        }
    }
    
    // treecheckboxes
    function search_list_treecheckboxes(treecheckboxes) {
        if (treecheckboxes) {
            $.each(treecheckboxes, function (idx, treecheckbox) {
                $("#search_field_property_term_"+treecheckbox).dynatree({
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
                    },
                    onPostInit: function (isReloading, isError) {
                    },
                    onActivate: function (node, event) {
                    },
                    onSelect: function (flag, node) {
                        var cont = 0;
                        var selKeys = $.map(node.tree.getSelectedNodes(), function (node) {
                            return node;
                        });
                        var selKeysValue = $.map(node.tree.getSelectedNodes(), function (node) {
                            return node.data.key;
                        });
                        $("#socialdb_propertyterm_" + treecheckbox).html('');
                        $.each(selKeys, function (index, key) {
                            cont++;
                            $("#socialdb_propertyterm_" + treecheckbox).append('<input type="hidden" name="socialdb_propertyterm_'+treecheckbox+'[]" value="' + key.data.key + '" >');
                        });
                        if(selKeysValue.length>0){
                            append_category_properties_adv(selKeysValue.join(','),treecheckbox );
                        }
                    },
                    dnd: {
                    }
                });
            });
        }
    }
    
    // tree
    function search_list_tree(trees) {
        if (trees) {
            //console.log(trees);
            $.each(trees, function (idx, tree) {
                $("#search_field_property_term_"+tree).dynatree({
                    checkbox: true,
                    // Override class name for checkbox icon:
                    classNames: {checkbox: "dynatree-radio"},
                    selectMode: 1,
                    selectionVisible: true, // Make sure, selected nodes are visible (expanded). 
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
                                hide_checkbox: 'true',
                                classCss: node.data.addClass,
                                //operation: 'findDynatreeChild'
                                operation: 'expand_dynatree'
                            }
                        });
                    },
                    onClick: function (node, event) {
                        // Close menu on click
                        var key = node.data.key;
                        if(key.search('moreoptions')<0&&key.search('alphabet')<0){
                            $("#search_socialdb_propertyterm_"+tree).html('');
                            $("#search_socialdb_propertyterm_"+tree).append('<option selected="selected" value="'+node.data.key+'" >'+node.data.title+'</option>')
                         }
                    },
                    onKeydown: function (node, event) {
                    },
                    onCreate: function (node, span) {
                    },
                    onPostInit: function (isReloading, isError) {
                    },
                    onActivate: function (node, event) {
                    },
                    onSelect: function (flag, node) {
                        if ($("#socialdb_propertyterm_" + tree).val() === node.data.key) {
                            $("#socialdb_propertyterm_" + tree).val("");
                             $('#core_validation_'+tree).val('false');
                        } else {
                            $("#socialdb_propertyterm_" + tree).val(node.data.key);
                             append_category_properties_adv(node.data.key,tree );
                            $('#core_validation_'+tree).val('true');
                        }
                    },
                    dnd: {
                    }
                });
            });
        }
    }
    
    // get value of the property
    function search_get_val(value) {
        if (!value || value === '') {
            return false;
        } else if (value.split(',')[0] === '' && value !== '') {
            return [value];
        } else {
            return value.split(',');
        }
    }
    
    function show_collection_licenses(){
        $.ajax( {
            url: $('#src').val()+'/controllers/object/object_controller.php',
            type: 'POST',
            data: {operation: 'show_collection_licenses_search',collection_id:$("#collection_id").val()}
          } ).done(function( result ) {
            //$('html, body').animate({
               ///  scrollTop: parseInt($("#wpadminbar").offset().top)
               // }, 900);       
            $('#show_form_licenses').html(result); 
        });
    }
/******************************* Category Properties***************************/
     function append_category_properties_adv(id,property_id ){
        //busco os metadados da categoria selecionada    
        if(id!==''){
            //adicionando metadados
            $('#append_properties_categories_'+property_id+'_adv')
                     .html('<center><img width="100" heigth="100" src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>"><?php _e('Loading metadata for this field','tainacan') ?></center>');
            $.ajax({
                url: $('#src').val() + '/controllers/advanced_search/advanced_search_controller.php',
                type: 'POST',
                data: { operation: 'get_categories_properties',properties_to_avoid:$('#properties_id_avoid').val(),categories: id,property_searched_id:property_id}
            }).done(function (result) {
                console.log('carregando metadados da propriedade',property_id);
                hide_modal_main();
                //list_all_objects(selKeys.join(", "), $("#collection_id").val());
                $('#append_properties_categories_'+property_id+'_adv').html(result);
            });
        }
    }
    
    function onRadioChecked(name,property_id){
        append_category_properties_adv($('input[name='+name+']:checked').val(),property_id);
    }
    
    function onSelectValue(seletor,property_id){
         append_category_properties_adv($( seletor ).val(),property_id);
    }
    
    function onCheckboxValue(name,property_id){
        var values = [];
        $.each($('input[name='+name+']:checked'),function(index,value){
            values.push($(value).val());
        });
        append_category_properties_adv(values.join(','),property_id);
    }
</script>
