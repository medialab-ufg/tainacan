<script>
    $(function () {
        event_single_list_properties_term_insert_objects();
        $('[data-toggle="tooltip"]').tooltip();
    });

    function edit_term_property(property_id, object_id) {
        $("#single_cancel_" + property_id + "_" + object_id).show();
        $("#single_edit_" + property_id + "_" + object_id).hide();
        $(".single_edit_" + property_id + "_" + object_id).hide();
        $("#labels_" + property_id + "_" + object_id).hide();
        $("#widget_" + property_id + "_" + object_id).fadeIn();
    }
    function cancel_term_property(property_id, object_id) {
        $(".single_edit_" + property_id + "_" + object_id).show();
        $("#single_edit_" + property_id + "_" + object_id).show();
        $("#single_cancel_" + property_id + "_" + object_id).hide();
        $("#widget_" + property_id + "_" + object_id).hide();
        $("#labels_" + property_id + "_" + object_id).fadeIn();
    }

    function edit_data_property(property_id, object_id) {
        $("#single_edit_" + property_id + "_" + object_id).hide();
        $("#single_cancel_" + property_id + "_" + object_id).show();
        $("#single_save_" + property_id + "_" + object_id).show();

        $(".single_socialdb_property_" + property_id ).prop({
            disabled: false
        });
        $("#value_" + property_id + "_" + object_id).hide();

        //Show edition box
        $("input[id ^= 'single_property_value_" + property_id + "_" + object_id + "']").prop({
            disabled: false
        });
        $("input[id ^= 'single_property_value_" + property_id + "_" + object_id + "']").show();
    }
    function cancel_data_property(property_id, object_id) {
//        $("#single_property_value_" + property_id + "_" + object_id).val($("#property_" + property_id + "_" + object_id + "_value_before").val());
//        $("#single_cancel_" + property_id + "_" + object_id).hide();
//        $("#single_edit_" + property_id + "_" + object_id).show();
//        $("#single_save_" + property_id + "_" + object_id).hide();
//        $("#single_property_value_" + property_id + "_" + object_id).prop({
//            disabled: true
//        });
//        
        swal({
            title: '<?php _e('Attention!', 'tainacan'); ?>',
            text: '<?php _e('You going to lose all changes unsaved!', 'tainacan'); ?>',
            type: "info",
            showCancelButton: true,
            confirmButtonClass: 'btn-primary',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $("#single_property_value_" + property_id + "_" + object_id).val($("#single_property_" + property_id + "_" + object_id + "_value_before").val());
                $("#single_cancel_" + property_id + "_" + object_id).hide();
                $("#single_edit_" + property_id + "_" + object_id).show();
                $("#single_save_" + property_id + "_" + object_id).hide();
                $("input[id ^= 'single_property_value_" + property_id + "_" + object_id + "']").prop({
                    disabled: true
                });
                $(".single_socialdb_property_" + property_id ).prop({
                    disabled: true
                });
                $("input[id ^= 'single_property_value_" + property_id + "_" + object_id + "']").hide();
                $("#value_" + property_id + "_" + object_id).show();
            }
        });
    }
    function save_data_property(property_id, object_id) {
        //hook para validacao do formulario
        if(Hook.is_register( 'tainacan_validate_single_save_data_property')){
            Hook.call( 'tainacan_validate_single_save_data_property', [ property_id,object_id] );
            if(!Hook.result.is_validated){
                showAlertGeneral('<?php _e('Attention','tainacan') ?>', Hook.result.message, 'info');
                return false;
            }
        }

        var index_val_list = [];
        $("input[id ^= 'single_property_value_" + property_id + "_" + object_id + "']").each(function(){
            index_val_list.push({
                val: $(this).val(),
                index: $(this).attr('data-index')
            });
        });

        //inserindo a propriedade
        if(Hook.is_register( 'tainacan_insert_single_save_data_property')){
             Hook.call( 'tainacan_insert_single_save_data_property', [ property_id,object_id,'<?php echo mktime(); ?>'] );
        }else{
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/event/event_controller.php",
                data: {
                    socialdb_event_collection_id: $('#collection_id').val(),
                    operation: 'add_event_property_data_edit_value',
                    socialdb_event_create_date: '<?php echo mktime(); ?>',
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_property_data_edit_value_object_id: object_id,
                    socialdb_event_property_data_edit_value_property_id: property_id,
                    socialdb_event_property_data_edit_value_attribute_value: index_val_list
                }
            }).done(function (result) {
                verifyPublishedItem(object_id);
                elem = jQuery.parseJSON(result);
                if(!elem)
                    return false;

                var dynatrees = $("#dynatree").length;
                if(dynatrees > 0)
                    $("#dynatree").dynatree("getTree").reload();

                $("#widget_" + property_id + "_" + object_id).hide();
                $("#labels_" + property_id + "_" + object_id).fadeIn();
                list_properties_single(object_id);
                showAlertGeneral(elem.title, elem.msg, elem.type);
                //limpando caches
                delete_all_cache_collection();
            });
        }
    }

    function edit_object_property(property_id, object_id) {
        $("#single_cancel_" + property_id + "_" + object_id).show();
        $("#single_edit_" + property_id + "_" + object_id).hide();
        $(".single_edit_" + property_id + "_" + object_id).hide();
        $("#single_save_" + property_id + "_" + object_id).show();
        $("#labels_" + property_id + "_" + object_id).hide();
        $("#widget_" + property_id + "_" + object_id).fadeIn();
    }
    function cancel_object_property(property_id, object_id) {
        swal({
            title: '<?php _e('Attention!', 'tainacan'); ?>',
            text: '<?php _e('You going to lose all changes unsaved!', 'tainacan'); ?>',
            type: "info",
            showCancelButton: true,
            confirmButtonClass: 'btn-primary',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/object/object_controller.php",
                    data: {property_id: property_id, object_id: object_id, operation: 'get_property_object_value', }
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    if (elem.values) {
                        $("#single_property_value_" + property_id + "_" + object_id).html('');
                        $.each(elem.values, function (idx, value) {
                            if (value && value !== false) {
                                $("#single_property_value_" + property_id + "_" + object_id).append("<option class='selected' value='" + value.id + "' selected='selected' >" + value.name + "</option>");
                            }
                        });
                    } else {
                        $("#single_property_value_" + property_id + "_" + object_id).html('');
                    }
                    $('.dropdown-toggle').dropdown();
                });
                $("#single_cancel_" + property_id + "_" + object_id).hide();
                $("#single_edit_" + property_id + "_" + object_id).show();
                $(".single_edit_" + property_id + "_" + object_id).show();
                $("#single_save_" + property_id + "_" + object_id).hide();
                $("#widget_" + property_id + "_" + object_id).hide();
                $("#labels_" + property_id + "_" + object_id).fadeIn();
            }
        });


        // metas

    }
    function save_object_property(property_id, object_id) {
        //hook para validacao do formulario
        if(Hook.is_register( 'tainacan_validate_single_save_object_property')){
            Hook.call( 'tainacan_validate_single_save_object_property', [ property_id,object_id] );
            if(!Hook.result.is_validated){
                showAlertGeneral('<?php _e('Attention','tainacan') ?>', Hook.result.message, 'info');
                return false;
            }
        }
        //insere a propriedade de objeto
        $('#modalImportMain').modal('show');//mostro o modal de carregamento
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/event/event_controller.php",
            data: {
                socialdb_event_collection_id: $('#collection_id').val(),
                operation: 'add_event_property_object_edit_value',
                socialdb_event_create_date: '<?php echo mktime(); ?>',
                socialdb_event_user_id: $('#current_user_id').val(),
                socialdb_event_property_object_edit_object_id: object_id,
                socialdb_event_property_object_edit_property_id: property_id,
                socialdb_event_property_object_edit_value_suggested_value: $("#single_property_value_" + property_id + "_" + object_id).val()}
        }).done(function (result) {
            $('#modalImportMain').modal('hide');//mostro o modal de carregamento
            verifyPublishedItem(object_id); 
            elem = jQuery.parseJSON(result);
            list_properties_single(object_id);
            showAlertGeneral(elem.title, elem.msg, elem.type);
            //limpando caches
            delete_all_cache_collection();
        });
        return;

    }
    function autocomplete_object_property(property_id, object_id) {
        $("#single_autocomplete_value_" + property_id + "_" + object_id).autocomplete({
            source: $('#src').val() + '/controllers/object/object_controller.php?operation=get_objects_by_property_json&property_id=' + property_id,
            messages: {
                noResults: '',
                results: function () {
                }
            },
            minLength: 2,
            select: function (event, ui) {
                $("#single_autocomplete_value_" + property_id + "_" + object_id).html('');
                $("#single_autocomplete_value_" + property_id + "_" + object_id).val('');
                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                var temp = $("#single_property_value_" + property_id + "_" + object_id + " [value='" + ui.item.value + "']").val();
                if (typeof temp == "undefined") {
                    var already_selected = false;
                    $("#single_property_value_" + property_id + "_" + object_id+" option").each(function(){
                        if($(this).val()==ui.item.value){
                            already_selected = true;
                        }
                    });
                    if(!already_selected){
                        $("#single_property_value_" + property_id + "_" + object_id).append("<option class='selected' value='" + ui.item.value + "' selected='selected' >" + ui.item.label + "</option>");
                        if(Hook.is_register( 'tainacan_validate_cardinality_onselect')){
                            Hook.call( 'tainacan_validate_cardinality_onselect', [ 'select[name="socialdb_property_'+property_id+'[]"]',property_id ] );
                        }
                    }
                }
                setTimeout(function () {
                    $("#single_autocomplete_value_" + property_id + "_" + object_id).val('');
                }, 100);
            }
        });
    }
    function clear_select_object_property(e,property_id,object_id) {
        $('option:selected', e).remove();
        $("#single_property_value_" + property_id + "_" + object_id+" option").each(function()
        {
           $(this).attr('selected','selected');
        });
        if(Hook.is_register( 'tainacan_validate_cardinality_onselect')){
            Hook.call( 'tainacan_validate_cardinality_onselect', [ 'select[name="socialdb_property_'+property_id+'[]"]',property_id ] );
        }
        //$('.chosen-selected2 option').prop('selected', 'selected');
    }
///////////////////////////// TERM PROPERTIES FUNCTIONS ////////////////////////
    function event_single_list_properties_term_insert_objects() {
        var categories = event_single_get_val($("#event_single_object_categories_id_<?php echo $object_id; ?>").val());
        var radios = event_single_get_val($("#event_single_properties_terms_radio").val());
        var selectboxes = event_single_get_val($("#event_single_properties_terms_selectbox").val());
        var trees = event_single_get_val($("#event_single_properties_terms_tree").val());
        var checkboxes = event_single_get_val($("#event_single_properties_terms_checkbox").val());
        var multipleSelects = event_single_get_val($("#event_single_properties_terms_multipleselect").val());
        var treecheckboxes = event_single_get_val($("#event_single_properties_terms_treecheckbox").val());
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
                            var title = '<?php _e('Remove classification', 'tainacan') ?>';
                            var text = '<?php _e('Are you sure to remove this classification', 'tainacan') ?>';
                            var object_id = <?php echo $object_id; ?>;
                            var time = '<?php echo mktime(); ?>';

                            /*var remove_button = '';
                            if(!elem.metas.socialdb_property_required)
                            {
                                remove_button =
                                    ' <a type="button" onclick="remove_classication(\'' + title + '\', \'' + text + '\',' + children.term_id + ',' + object_id + ',\'' + time + '\')" style="cursor: pointer;">'
                                    + "<span class='glyphicon glyphicon-remove-circle'></span>"
                                    +"</a>";
                            }*/

                            checked = 'checked="checked"';
                            $('#value_single_radio_' + radio + '_<?php echo $object_id; ?>').val(children.term_id);
                            $("#labels_" + radio + "_<?php echo $object_id; ?>").html('');
                            $("#labels_" + radio + "_<?php echo $object_id; ?>").append('<b><a style="cursor:pointer;" onclick="wpquery_term_filter(' + children.term_id + ',' + radio + ')">'
                                + children.name
                                + '</a>' /*+ remove_button*/
                                + '</b><br>');//inserindo os termos escolhidos
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
                                $("#labels_" + checkbox + "_<?php echo $object_id; ?>").append('<b><a style="cursor:pointer;" onclick="wpquery_term_filter(' + children.term_id + ',' + checkbox + ')">' + children.name + '</a></b><br>');//inserindo os termos escolhidos
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
                            $("#labels_" + selectbox + "_<?php echo $object_id; ?>").append('<b><a style="cursor:pointer;" onclick="wpquery_term_filter(' + children.term_id + ',' + selectbox + ')">' + children.name + '</a></b><br>');//inserindo os termos escolhidos
                            $('#value_single_select_' + selectbox + '_<?php echo $object_id; ?>').val(children.term_id);
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
                            $("#labels_" + multipleSelect + "_<?php echo $object_id; ?>").append('<b><a style="cursor:pointer;" onclick="wpquery_term_filter(' + children.term_id + ',' + multipleSelect + ')">' + children.name + '</a></b><br>');//inserindo os termos escolhidos
                        
                        }
                        $('#field_event_single_property_term_' + multipleSelect + '_<?php echo $object_id; ?>').append('<option onclick="get_event_single_multiple(this,<?php echo $object_id; ?>)" ' + checked + ' value="' + children.term_id + '">' + children.name + '</option>');
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
                            $("#labels_" + treecheckbox + "_<?php echo $object_id; ?>").append('<b><a style="cursor:pointer;" onclick="wpquery_term_filter(' + elem.term.term_id + ',' + treecheckbox + ')">' + elem.term.name + '</a></b><br>');//zero o html do container que recebera os
                            // coloco no selectbox o valor selecionado
                            $("#socialdb_propertyterm_" + treecheckbox + "_<?php echo $object_id; ?>").append('<option selected="selected" value="' + elem.term.term_id + '" >' + elem.term.name + '</option>');
                        }
                    });
                });
                //
                $("#field_event_single_property_term_" + treecheckbox + '_<?php echo $object_id; ?>').dynatree({
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
                                collection_id: $("#collection_id").val(),
                                //category_id: node.data.key,
                                key:node.data.key,
                                classCss: node.data.addClass,
                                 hide_count:'true',
                                operation: 'expand_dynatree'
                            }
                        });
                    },
                    onClick: function (node, event) {


                    },
                    onKeydown: function (node, event) {
                    },
                    onCreate: function (node, span) {
                       // $("#socialdb_propertyterm_" + treecheckbox + "_<?php echo $object_id; ?>").html('');
                        $("#field_event_single_property_term_" + treecheckbox + '_<?php echo $object_id; ?>').dynatree("getRoot").visit(function (node) {
                            // delete_value(node.data.key); 
                            if (categories.indexOf(node.data.key) > -1) {
                                node.select(false);
                                //$("#socialdb_propertyterm_" + treecheckbox + "_<?php echo $object_id; ?>").append('<option selected="selected" value="' + node.data.key + '" >' + node.data.title + '</option>');
                            }
                        });
                    },
                    onPostInit: function (isReloading, isError) {
                    },
                    onActivate: function (node, event_single) {
                    },
                    onSelect: function (flag, node) {
                        if (categories.indexOf(node.data.key) < 0) {
                            add_classification(<?php echo $object_id; ?>, node.data.key);
                        } else {
                            remove_classication('<?php _e('Remove classification', 'tainacan') ?>', '<?php _e('Are you sure to remove this classification', 'tainacan') ?>', node.data.key,<?php echo $object_id; ?>, '<?php echo mktime(); ?>')
                        }
                    },
                    dnd: {
                    }
                });
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

                            $("#labels_" + tree + "_<?php echo $object_id; ?>").html('<b><a style="cursor:pointer;" onclick="wpquery_term_filter(' + elem.term.term_id + ',' + tree + ')">'
                                + elem.term.name + '</a></b>');//zero o html do container que recebera os
                            // coloco no selectbox o valor selecionado
                            $("#socialdb_propertyterm_" + tree + "_<?php echo $object_id; ?>").append('<option selected="selected" value="' + elem.term.term_id + '" >' + elem.term.name + '</option>');
                            //coloco o valor atual no hidden para poder remove-lo caso necessario 
                            $('#value_single_tree_' + tree + '_<?php echo $object_id; ?>').val(elem.term.term_id);
                        }
                    });
                });
                //monta o dynatree do metadado
                $("#field_event_single_property_term_" + tree + '_<?php echo $object_id; ?>').dynatree({
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
                                key:node.data.key,
                                classCss: node.data.addClass,
                                operation: 'expand_dynatree'
                            }
                        });
                    },
                    onSelect: function (flag, node) {
                        //Tree = ID da propriedade
                        $("#socialdb_propertyterm_" + tree + "_<?php echo $object_id; ?>").html('');
                        $("#socialdb_propertyterm_" + tree + "_<?php echo $object_id; ?>").append('<option selected="selected" value="' + node.data.key + '" >' + node.data.title + '</option>');
                        get_event_single_tree(node.data.key, $('#value_single_tree_' + tree + '_<?php echo $object_id; ?>').val(), tree, <?php echo $object_id; ?>);
                        $('#value_single_tree_' + tree + '_<?php echo $object_id; ?>').val(node.data.key);
                    }
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
    //add_classification
    function add_classification(object_id, term_id) {
        swal({
            title: '<?php _e('Add classification', 'tainacan') ?>',
            text: '<?php _e('Are you sure to include this classification', 'tainacan') ?>',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/event/event_controller.php",
                    data: {
                        operation: 'add_event_classification_create',
                        socialdb_event_create_date: '<?php echo mktime(); ?>',
                        socialdb_event_user_id: $('#current_user_id').val(),
                        socialdb_event_classification_object_id: object_id,
                        socialdb_event_classification_term_id: term_id,
                        socialdb_event_classification_type: 'category',
                        socialdb_event_collection_id: $('#collection_id').val()}
                }).done(function (result) {
                    verifyPublishedItem(object_id);
                    elem_first = jQuery.parseJSON(result);
                    show_classifications_single(object_id);
                    list_properties_single(object_id);
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                    //limpando caches
                    delete_all_cache_collection();
                });
            } else {
                list_properties_single(object_id);
            }
        });

    }

    function remove_classication(title, text, category_id, object_id, time) {
        swal({
            title: title,
            text: text,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/event/event_controller.php",
                    data: {
                        operation: 'add_event_classification_delete',
                        socialdb_event_create_date: time,
                        socialdb_event_user_id: $('#current_user_id').val(),
                        socialdb_event_classification_object_id: object_id,
                        socialdb_event_classification_term_id: category_id,
                        socialdb_event_classification_type: 'category',
                        socialdb_event_collection_id: $('#collection_id').val()}
                }).done(function (result) {
                    verifyPublishedItem(object_id);
                    elem_first = jQuery.parseJSON(result);
                    show_classifications_single(object_id);
                    list_properties_single(object_id);
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                    //limpando caches
                    delete_all_cache_collection();
                });
            } /*else {
                list_properties_single(object_id);
            }*/
        });
    }

    //get the event on checbox
    function get_event_single_checkbox(e, object_id) {
        var is_checked = $(e).is(":checked");
        verifyPublishedItem(object_id);
        if (is_checked) {
            add_classification(object_id, $(e).val());
        } else {
            remove_classication('<?php _e('Remove classification', 'tainacan') ?>', '<?php _e('Are you sure to remove this classification', 'tainacan') ?>', $(e).val(), object_id, '<?php echo mktime(); ?>');
        }
    }

    //get multipleselect values
    function get_event_single_multiple(e, object_id) {
        verifyPublishedItem(object_id);
        var flag = false;
        var is_selected = $(e).is(":selected");
        var classifications = $("#object_classifications_event_single_<?php echo $object_id; ?>").val().split(',');
        if (classifications.length > 0 && $(e).val()) {
            var index = classifications.indexOf($(e).val());
            if (index > -1) {
                remove_classication('<?php _e('Remove classification', 'tainacan') ?>', '<?php _e('Are you sure to remove this classification', 'tainacan') ?>', $(e).val(), object_id, '<?php echo mktime(); ?>');
                flag = true;
            }
        }
        if (!flag && is_selected) {
            add_classification(object_id, $(e).val());
        } else if (!flag) {
            remove_classication('<?php _e('Remove classification', 'tainacan') ?>', '<?php _e('Are you sure to remove this classification', 'tainacan') ?>', $(e).val(), object_id, '<?php echo mktime(); ?>');
        }
    }

    function get_event_single_radio(e, property_id, object_id) {
        verifyPublishedItem(object_id);
        var before_category = $('#value_single_radio_' + property_id + '_' + object_id).val();
        $('#value_single_radio_' + property_id + '_' + object_id).val($(e).val());

        swal({
            title: '<?php _e('Add classification', 'tainacan') ?>',
            text: '<?php _e('Are you sure to include this classification? This action removes the previous selected category', 'tainacan') ?>',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                //adiciona a escolhida 
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/event/event_controller.php",
                    data: {
                        operation: 'add_event_classification_create',
                        socialdb_event_create_date: '<?php echo mktime(); ?>',
                        socialdb_event_user_id: $('#current_user_id').val(),
                        socialdb_event_classification_object_id: object_id,
                        socialdb_event_classification_property_id: property_id,
                        socialdb_event_classification_term_id: $(e).val(),
                        socialdb_event_classification_type: 'category',
                        socialdb_event_collection_id: $('#collection_id').val()
                    }
                }).done(function (result) {
                    elem_first = jQuery.parseJSON(result);
                    show_classifications(object_id);
                    list_properties_single(object_id);
                    //limpando caches
                    delete_all_cache_collection();

                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                });

                //retira a anterior
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/event/event_controller.php",
                    data: {
                        operation: 'add_event_classification_delete',
                        socialdb_event_create_date: '<?php echo mktime(); ?>',
                        socialdb_event_user_id: $('#current_user_id').val(),
                        socialdb_event_classification_object_id: object_id,
                        socialdb_event_classification_property_id: property_id,
                        socialdb_event_classification_term_id: before_category,
                        socialdb_event_classification_type: 'category',
                        socialdb_event_collection_id: $('#collection_id').val()}
                })

            } else {
                list_properties_single(object_id);
            }
        });
    }

    function get_event_single_select(e, property_id, object_id) {
        if ($(e).val() !== '') {
            verifyPublishedItem(object_id);
            var before_category = $('#value_single_select_' + property_id + '_' + object_id).val();
            $('#value_single_select_' + property_id + '_' + object_id).val($(e).val());
            swal({
                title: '<?php _e('Add classification', 'tainacan') ?>',
                text: '<?php _e('Are you sure to include this classification? This action removes the previous selected category', 'tainacan') ?>',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: 'btn-danger',
                closeOnConfirm: false,
                closeOnCancel: true
            },
            function (isConfirm) {
                if (isConfirm) {
                    //adiciona a escolhida 
                    $.ajax({
                        type: "POST",
                        url: $('#src').val() + "/controllers/event/event_controller.php",
                        data: {
                            operation: 'add_event_classification_create',
                            socialdb_event_create_date: '<?php echo mktime(); ?>',
                            socialdb_event_user_id: $('#current_user_id').val(),
                            socialdb_event_classification_object_id: object_id,
                            socialdb_event_classification_term_id: $(e).val(),
                            socialdb_event_classification_type: 'category',
                            socialdb_event_collection_id: $('#collection_id').val()}
                    }).done(function (result) {
                        elem_first = jQuery.parseJSON(result);
                        show_classifications(object_id);
                        list_properties_single(object_id);
                        showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                        //limpando caches
                        delete_all_cache_collection();
                    });
                    //retira a anterior
                    $.ajax({
                        type: "POST",
                        url: $('#src').val() + "/controllers/event/event_controller.php",
                        data: {
                            operation: 'add_event_classification_delete',
                            socialdb_event_create_date: '<?php echo mktime(); ?>',
                            socialdb_event_user_id: $('#current_user_id').val(),
                            socialdb_event_classification_object_id: object_id,
                            socialdb_event_classification_term_id: before_category,
                            socialdb_event_classification_type: 'category',
                            socialdb_event_collection_id: $('#collection_id').val()}
                    }).done(function (result) {
                        elem_first = jQuery.parseJSON(result);
                    });

                } else {
                    list_properties_single(object_id);
                }
            });
        }
    }

    function get_event_single_tree(value_actual, value_before, property_id, object_id) {
       verifyPublishedItem(object_id);
        swal({
            title: '<?php _e('Add classification', 'tainacan') ?>',
            text: '<?php _e('Are you sure to include this classification? This action removes the previous selected category', 'tainacan') ?>',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {

                //adiciona a escolhida
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/event/event_controller.php",
                    data: {
                        operation: 'add_event_classification_create',
                        socialdb_event_create_date: '<?php echo mktime(); ?>',
                        socialdb_event_user_id: $('#current_user_id').val(),
                        socialdb_event_classification_object_id: object_id,
                        socialdb_event_classification_property_id: property_id,
                        socialdb_event_classification_term_id: value_actual,
                        socialdb_event_classification_type: 'category',
                        socialdb_event_collection_id: $('#collection_id').val()}
                }).done(function (result) {
                    elem_first = jQuery.parseJSON(result);
                    // show_classifications(object_id);
                    list_properties_single(object_id);
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);

                    //limpando caches
                    delete_all_cache_collection();
                });

                //retira a anterior
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/event/event_controller.php",
                    data: {
                        operation: 'add_event_classification_delete',
                        socialdb_event_create_date: '<?php echo mktime(); ?>',
                        socialdb_event_user_id: $('#current_user_id').val(),
                        socialdb_event_classification_object_id: object_id,
                        socialdb_event_classification_property_id: property_id,
                        socialdb_event_classification_term_id: value_before,
                        socialdb_event_classification_type: 'category',
                        socialdb_event_collection_id: $('#collection_id').val()}
                }).done(function (result) {
                    elem_first = jQuery.parseJSON(result);
                    show_classifications_single(object_id);
                });
            } else {
                list_properties_single(object_id);
            }
        });
    }
</script>
