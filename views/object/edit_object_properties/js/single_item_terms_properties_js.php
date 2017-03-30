<script>
    $(function () {
        //event_single_list_properties_term_insert_objects();
    });
    function event_single_list_properties_term_insert_objects() {
       console.log('list_single_propreties'); 
       var categories = edit_get_val($("#edit_object_categories_id").val());
        var radios = edit_get_val($("#properties_terms_radio").val());
        var selectboxes = edit_get_val($("#properties_terms_selectbox").val());
        var trees = edit_get_val($("#properties_terms_tree").val());
        var checkboxes = edit_get_val($("#properties_terms_checkbox").val());
        var multipleSelects = edit_get_val($("#properties_terms_multipleselect").val());
        var treecheckboxes = edit_get_val($("#properties_terms_treecheckbox").val());
        event_single_list_radios(radios, categories);
        event_single_list_tree(trees, categories);
        event_single_list_selectboxes(selectboxes, categories);
        event_single_list_multipleselectboxes(multipleSelects, categories);
        event_single_list_checkboxes(checkboxes, categories);
        event_single_list_treecheckboxes(treecheckboxes, categories);
    }
    // radios
    function event_single_list_radios(radios, categories) {
        if (radios) {
            $.each(radios, function (idx, radio) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: radio}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#field_event_single_property_term_' + radio).html('');
                    $.each(elem.children, function (idx, children) {
                        var required = '';
                        var checked = '';
                        if (elem.metas.socialdb_property_required === 'true') {
                            required = 'required="required"';
                        }
                        //  if (property.id == selected) {
                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        //  } else {
                        if (categories.indexOf(children.term_id) > -1) {
                            checked = 'checked="checked"';
                            $('#value_single_radio_' + radio + '_<?php echo $object_id; ?>').val(children.term_id);
                            $("#labels_" + radio + "_<?php echo $object_id; ?>").html('');
                            $("#labels_" + radio + "_<?php echo $object_id; ?>").append('<p><a style="cursor:pointer;" onclick="wpquery_term_filter(' + children.term_id + ',' + radio + ')">' + children.name + '</a></p><br>');//inserindo os termos escolhidos
                        }
                        //delete_value(children.term_id);
                        $('#field_event_single_property_term_' + radio + '_<?php echo $object_id; ?>').append('<input ' + checked + ' onchange="get_event_single_radio(this,' + radio + ',<?php echo $object_id; ?>)" type="radio" name="socialdb_propertyterm_' + radio + '" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                        //  }
                    });
                });
            });
        }
    }
    // checkboxes
    function event_single_list_checkboxes(checkboxes, categories) {
        if (checkboxes) {
            $.each(checkboxes, function (idx, checkbox) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: checkbox}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#field_event_single_property_term_' + checkbox).html('');
                    
                    $("#labels_" + checkbox + "_<?php echo $object_id; ?>").html('');
                    if(elem.children){
                        $.each(elem.children, function (idx, children) {
                            var required = '';
                            var checked = '';
                            // event_single_delete_value(children.term_id);
                            if (elem.metas.socialdb_property_required === 'true') {
                                required = 'required="required"';
                            }
                            if (categories.indexOf(children.term_id) > -1) {
                                checked = 'checked="checked"';
                                //$("#labels_" + checkbox + "_<?php echo $object_id; ?>").html('');//zero o html do container que recebera os
                                // insiro o html do link do valor atribuido
                                $("#labels_" + checkbox + "_<?php echo $object_id; ?>").append('<p><a style="cursor:pointer;" onclick="wpquery_term_filter(' + children.term_id + ',' + checkbox + ')">' + children.name + '</a></p><br>');//inserindo os termos escolhidos
                                append_category_properties(children.term_id,0,checkbox); 
                            }
                            //  if (property.id == selected) {
                            //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                            //  } else {
                            $('#field_event_single_property_term_' + checkbox + '_<?php echo $object_id; ?>').append('<input onchange="get_event_single_checkbox(this,<?php echo $object_id; ?>)" ' + checked + ' ' + required + ' type="checkbox" name="socialdb_propertyterm_' + checkbox + '[]" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                            //  }
                        });
                    }
                });
            });
        }
    }
    // selectboxes
    function event_single_list_selectboxes(selectboxes, categories) {
        if (selectboxes) {
            $.each(selectboxes, function (idx, selectbox) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: selectbox}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#field_event_single_property_term_' + selectbox + '_<?php echo $object_id; ?>').html('');
                    $('#field_event_single_property_term_' + selectbox + '_<?php echo $object_id; ?>').append('<option  value="">Selecione...</option>');
                    $.each(elem.children, function (idx, children) {
                        var checked = '';
                        //delete_value(children.term_id);
                        if (categories.indexOf(children.term_id) > -1) {
                            checked = 'selected="selected"';
                            $("#labels_" + selectbox + "_<?php echo $object_id; ?>").html('');
                            $("#labels_" + selectbox + "_<?php echo $object_id; ?>").append('<p><a style="cursor:pointer;" onclick="wpquery_term_filter(' + children.term_id + ',' + selectbox + ')">' + children.name + '</a></p><br>');//inserindo os termos escolhidos
                            $('#value_single_select_' + selectbox + '_<?php echo $object_id; ?>').val(children.term_id);
                            append_category_properties(children.term_id,0,selectbox); 
                        }
                        $('#field_event_single_property_term_' + selectbox + '_<?php echo $object_id; ?>').append('<option ' + checked + ' value="' + children.term_id + '">' + children.name + '</option>');
                        //  }
                    });
                });
            });
        }
    }
    // multiple
    function event_single_list_multipleselectboxes(multipleSelects, categories) {
        if (multipleSelects) {
            $.each(multipleSelects, function (idx, multipleSelect) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: multipleSelect}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $("#labels_" + multipleSelect + "_<?php echo $object_id; ?>").html('');
                    $('#field_event_single_property_term_' + multipleSelect + '_<?php echo $object_id; ?>').html('');
                    $.each(elem.children, function (idx, children) {
                        var checked = '';
                        //delete_value(children.term_id);
                        if (categories.indexOf(children.term_id) > -1) {
                            checked = 'selected="selected"';
                            $("#labels_" + multipleSelect + "_<?php echo $object_id; ?>").append('<p><a style="cursor:pointer;" onclick="wpquery_term_filter(' + children.term_id + ',' + multipleSelect + ')">' + children.name + '</a></p><br>');//inserindo os termos escolhidos
                            append_category_properties(children.term_id,0,multipleSelect); 
                        }
                        //  }
                    });
                });
            });
        }
    }
    // treecheckboxes
    function event_single_list_treecheckboxes(treecheckboxes, categories) {
        if (treecheckboxes) {
            $.each(treecheckboxes, function (idx, treecheckbox) {
                 $("#labels_" + treecheckbox + "_<?php echo $object_id; ?>").html('');
                //mostrando o valor adicionando no label do metadado para cada categoria selecionada
                $.each(categories, function (idx, category) {
                    //bsuca os dados da categoria
                    var promise = get_category_promise(category,treecheckbox);
                    promise.done(function (result) {
                        elem = JSON.parse(result);
                        if(elem.show){
                            //$("#labels_" + tree + "_<?php echo $object_id; ?>").html('');//zero o html do container que recebera os
                            // insiro o html do link do valor atribuido
                            $("#labels_" + treecheckbox + "_<?php echo $object_id; ?>").append('<p><a style="cursor:pointer;" onclick="wpquery_term_filter(' + elem.term.term_id + ',' + treecheckbox + ')">' + elem.term.name + '</a></p><br>');//zero o html do container que recebera os
                            // coloco no selectbox o valor selecionado
                            append_category_properties(elem.term.term_id,elem.term.term_id,treecheckbox);                        }
                    });
                });
                //
            });
        }
    }

    // tree
    function event_single_list_tree(trees, categories) {
        if (trees) {
            $.each(trees, function (idx, tree) {
                //mostrando o valor adicionando no label do metadado
                $.each(categories, function (idx, category) {
                    var promise = get_category_promise(category,tree);
                    promise.done(function (result) {
                        elem = JSON.parse(result);
                        if(elem.show){
                            $("#socialdb_propertyterm_" + tree + "_<?php echo $object_id; ?>").html('');
                            $("#labels_" + tree + "_<?php echo $object_id; ?>").html('');//zero o html do container que recebera os
                            // insiro o html do link do valor atribuido
                            $("#labels_" + tree + "_<?php echo $object_id; ?>").html('<p><a style="cursor:pointer;" onclick="wpquery_term_filter(' + elem.term.term_id + ',' + tree + ')">' + elem.term.name + '</a></p>');//zero o html do container que recebera os
                            append_category_properties(elem.term.term_id,elem.term.term_id,tree);
                        }
                    });
                });
            });
        }
    }



    // get value of the property
    function event_single_get_val(value) {
        if (value === '') {
            return false;
        } else if (value.split(',')[0] === '' && value !== '') {
            return [value];
        } else {
            return value.split(',');
        }
    }
    // retira do array de categorias que sao do objeto
    function event_single_delete_value(category_id) {
        var classifications = $("#object_classifications_event_single_<?php echo $object_id; ?>").val().split(',');
        if (classifications.length > 0 && category_id) {
            var index = classifications.indexOf(category_id);
            if (index > -1) {
                classifications.splice(index, 1);
                $("#object_classifications_event_single").val(classifications.join());
            }
        }
    }
</script>
