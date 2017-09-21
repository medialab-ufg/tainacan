<script>
    $(function () {
        var src = $('#src').val();
        event_list_properties_term_insert_objects();

    });

    function edit_data_property(property_id, object_id) {
        $("#cancel_" + property_id + "_" + object_id).show();
        $("#edit_" + property_id + "_" + object_id).hide();
        $("#save_" + property_id + "_" + object_id).show();
        $("#property_value_" + property_id + "_" + object_id).prop({
            disabled: false
        });
    }
    function cancel_data_property(property_id, object_id) {
        swal({
                title: '<?php _e('Attention!'); ?>',
                text: '<?php _e('You going to lose all changes unsaved!'); ?>',
                type: "info",
                showCancelButton: true,
                confirmButtonClass: 'btn-primary',
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function (isConfirm) {
                if (isConfirm) {
                        $("#property_value_" + property_id + "_" + object_id).val($("#property_" + property_id + "_" + object_id + "_value_before").val());
                        $("#cancel_" + property_id + "_" + object_id).hide();
                        $("#edit_" + property_id + "_" + object_id).show();
                        $("#save_" + property_id + "_" + object_id).hide();
                        $("#property_value_" + property_id + "_" + object_id).prop({
                            disabled: true
                        });
                }
            });            
    }
    function save_data_property(property_id, object_id) {
        if($("#property_value_" + property_id + "_" + object_id).val().trim()!==''){
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/event/event_controller.php",
                data: {
                    socialdb_event_collection_id: $('#collection_id').val(),
                    operation: 'add_event_property_data_edit_value',
                    socialdb_event_create_date: '<?php echo mktime();  ?>',
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_property_data_edit_value_object_id: object_id,
                    socialdb_event_property_data_edit_value_property_id: property_id,
                    socialdb_event_property_data_edit_value_attribute_value: $("#property_value_" + property_id + "_" + object_id).val()}
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                list_properties(object_id);
                showAlertGeneral(elem.title, elem.msg, elem.type);
            });
            return;
        }else{
           showAlertGeneral('<?php _e('Attention') ?>', '<?php _e('Invalid value!') ?>', 'info');
        }
    }

    function edit_object_property(property_id, object_id) {
        $("#cancel_" + property_id + "_" + object_id).show();
        $("#edit_" + property_id + "_" + object_id).hide();
        $("#save_" + property_id + "_" + object_id).show();
        $("#autocomplete_value_" + property_id + "_" + object_id).prop({
            disabled: false
        });
        $("#property_value_" + property_id + "_" + object_id).prop({
            disabled: false
        });
    }
    function cancel_object_property(property_id, object_id) {
            // metas
            swal({
                title: '<?php _e('Attention!'); ?>',
                text: '<?php _e('You going to lose all changes unsaved!'); ?>',
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
                            $("#property_value_" + property_id + "_" + object_id).html('');
                            $.each(elem.values, function (idx, value) {
                                if (value && value !== false) {
                                    $("#property_value_" + property_id + "_" + object_id).append("<option class='selected' value='" + value.id + "' selected='selected' >" + value.name + "</option>");
                                }
                            });
                        } else {
                            $("#property_value_" + property_id + "_" + object_id).html('');
                        }
                        $('.dropdown-toggle').dropdown();
                    });
                    $("#cancel_" + property_id + "_" + object_id).hide();
                    $("#edit_" + property_id + "_" + object_id).show();
                    $("#save_" + property_id + "_" + object_id).hide();
                    $("#autocomplete_value_" + property_id + "_" + object_id).prop({
                        disabled: true
                    });
                    $("#property_value_" + property_id + "_" + object_id).prop({
                        disabled: true
                    });
                }
            });

        }
    function save_object_property(property_id, object_id) {
         var delete_all_values = false;
        if( $("#property_value_" + property_id + "_" + object_id).val()===''){
            delete_all_values = true;
        }
        
        $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/event/event_controller.php",
                data: {
                    socialdb_event_collection_id: $('#collection_id').val(),
                    delete_all_values: delete_all_values,
                    operation: 'add_event_property_object_edit_value',
                    socialdb_event_create_date: '<?php echo mktime();  ?>',
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_property_object_edit_object_id: object_id,
                    socialdb_event_property_object_edit_property_id: property_id,
                    socialdb_event_property_object_edit_value_suggested_value: $("#property_value_" + property_id + "_" + object_id).val()}
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                list_properties(object_id);
                showAlertGeneral(elem.title, elem.msg, elem.type);
                // if (elem.pre_approved) {
               // //    $("#property_" + property_id + "_" + object_id + "_value_before").val($("#property_value_" + property_id + "_" + object_id).val());
               // } else {
                //    cancel_data_property(property_id, object_id)
                //}
         });
        return;
        
    }
    function autocomplete_object_property(property_id, object_id) {
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
                    $("#property_value_" + property_id + "_" + object_id).append("<option class='selected' value='" + ui.item.value + "' selected='selected' >" + ui.item.label + "</option>");

                }
                setTimeout(function () {
                    $("#autocomplete_value_" + property_id + "_" + object_id).val('');
                }, 100);
            }
        });
    }
    function clear_select_object_property(e,property_id,object_id) {
        $('option', e).remove();
         $("#property_value_" + property_id + "_" + object_id+"_add option").each(function()
        {
           $(this).attr('selected','selected');
        });
    }
///////////////////////////// TERM PROPERTIES FUNCTIONS ////////////////////////
function event_list_properties_term_insert_objects() {
        var categories = event_get_val($("#event_object_categories_id_<?php echo $object_id; ?>").val());
        var radios = event_get_val($("#event_properties_terms_radio").val());
        var selectboxes = event_get_val($("#event_properties_terms_selectbox").val());
        var trees = event_get_val($("#event_properties_terms_tree").val());
        var checkboxes = event_get_val($("#event_properties_terms_checkbox").val());
        var multipleSelects = event_get_val($("#event_properties_terms_multipleselect").val());
        var treecheckboxes = event_get_val($("#event_properties_terms_treecheckbox").val());
        event_list_radios(radios,categories);
        event_list_tree(trees,categories);
        event_list_selectboxes(selectboxes,categories);
        event_list_multipleselectboxes(multipleSelects,categories);
        event_list_checkboxes(checkboxes,categories);
        event_list_treecheckboxes(treecheckboxes,categories);
    }
    // radios
    function event_list_radios(radios,categories) {
        if (radios) {
            $.each(radios, function (idx, radio) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: radio}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#field_event_property_term_' + radio).html('');
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
                            $('#value_radio_' + radio+'_<?php echo $object_id; ?>').val(children.term_id);
                        }
                         //delete_value(children.term_id);
                        $('#field_event_property_term_' + radio+'_<?php echo $object_id; ?>').append('<input '+checked+' onchange="get_event_radio(this,'+radio+',<?php echo $object_id; ?>)" type="radio" name="socialdb_propertyterm_'+radio+'" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                        //  }
                    });
                });
            });
        }
    }
    // checkboxes
    function event_list_checkboxes(checkboxes,categories) {
        if (checkboxes) {
            $.each(checkboxes, function (idx, checkbox) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: checkbox}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#field_event_property_term_' + checkbox).html('');
                    $.each(elem.children, function (idx, children) {
                        var required = '';
                        var checked = '';
                        // event_delete_value(children.term_id);
                        if(elem.metas.socialdb_property_required==='true'){
                            required = 'required="required"';
                        }
                        if(categories.indexOf(children.term_id)>-1){
                            checked = 'checked="checked"';
                        }
                        //  if (property.id == selected) {
                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        //  } else {
                        $('#field_event_property_term_' + checkbox+'_<?php echo $object_id; ?>').append('<input onchange="get_event_checkbox(this,<?php echo $object_id; ?>)" '+checked+' '+required+' type="checkbox" name="socialdb_propertyterm_'+checkbox+'[]" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                        //  }
                    });
                });
            });
        }
    }
    // selectboxes
    function event_list_selectboxes(selectboxes,categories) {
        if (selectboxes) {
            $.each(selectboxes, function (idx, selectbox) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: selectbox}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#field_event_property_term_' + selectbox+'_<?php echo $object_id; ?>').html('');
                    $('#field_event_property_term_' + selectbox+'_<?php echo $object_id; ?>').append('<option  value="">Selecione...</option>');
                    $.each(elem.children, function (idx, children) {
                        var checked = '';
                         //delete_value(children.term_id);
                        if(categories.indexOf(children.term_id)>-1){
                            checked = 'selected="selected"';
                            $('#value_select_' + selectbox+'_<?php echo $object_id; ?>').val(children.term_id);
                        }
                        $('#field_event_property_term_' + selectbox+'_<?php echo $object_id; ?>').append('<option '+checked+' value="' + children.term_id + '">' + children.name + '</option>');
                        //  }
                    });
                });
            });
        }
    }
     // multiple
    function event_list_multipleselectboxes(multipleSelects,categories) {
        if (multipleSelects) {
            $.each(multipleSelects, function (idx, multipleSelect) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: multipleSelect}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#field_event_property_term_' + multipleSelect+'_<?php echo $object_id; ?>').html('');
                    $.each(elem.children, function (idx, children) {
                        var checked = '';
                        //delete_value(children.term_id);
                        if(categories.indexOf(children.term_id)>-1){
                            checked = 'selected="selected"';
                        }
                        $('#field_event_property_term_' + multipleSelect+'_<?php echo $object_id; ?>').append('<option onclick="get_event_multiple(this,<?php echo $object_id; ?>)" '+checked+' value="' + children.term_id + '">' + children.name + '</option>');
                        //  }
                    });
                });
            });
        }
    }
    // treecheckboxes
    function event_list_treecheckboxes(treecheckboxes,categories) {
        if (treecheckboxes) {
            $.each(treecheckboxes, function (idx, treecheckbox) {
                $("#field_event_property_term_"+treecheckbox+'_<?php echo $object_id; ?>').dynatree({
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
                        if(categories.indexOf(node.data.key)<0){
                           add_classification(<?php echo $object_id; ?>,node.data.key);
                        }else{
                            remove_classication('<?php  _e('Remove classification') ?>','<?php _e('Are you sure to remove this classification') ?>',node.data.key,<?php echo $object_id; ?>,'<?php echo mktime(); ?>')
                        }

                    },
                    onKeydown: function (node, event) {
                    },
                    onCreate: function (node, span) {
                        $("#socialdb_propertyterm_"+treecheckbox+"_<?php echo $object_id; ?>").html('');
                        $("#field_event_property_term_"+treecheckbox+'_<?php echo $object_id; ?>').dynatree("getRoot").visit(function(node){
                          // delete_value(node.data.key); 
                           if(categories.indexOf(node.data.key)>-1){
                                 node.select();
                                 $("#socialdb_propertyterm_"+treecheckbox+"_<?php echo $object_id; ?>").append('<option selected="selected" value="'+node.data.key+'" >'+node.data.title+'</option>');
                            }
                        });
                    },
                    onPostInit: function (isReloading, isError) {
                    },
                    onActivate: function (node, event) {
                    },
                    onSelect: function (flag, node) {
                         //if(categories.indexOf(node.data.key)<0){
                          // add_classification(<?php echo $object_id; ?>,node.data.key);
                        // }
                    },
                    dnd: {
                    }
                });
            });
        }
    }
    
    // tree
    function event_list_tree(trees,categories) {
        if (trees) {
            $.each(trees, function (idx, tree) {
                $("#field_event_property_term_"+tree+'_<?php echo $object_id; ?>').dynatree({
                    selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
                    checkbox: true,
                    initAjax: {
                        url: $('#src').val() + '/controllers/category/category_controller.php',
                        data: {
                            collection_id: $("#collection_id").val(),
                            property_id: tree,
                            hide_checkbox: 'true',
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
                        //delete_value(node.data.key);
                        var key = node.data.key;
                        if(key.search('moreoptions')<0&&key.search('alphabet')<0){
                            $("#socialdb_propertyterm_"+tree+"_<?php echo $object_id; ?>").html('');
                            $("#socialdb_propertyterm_"+tree+"_<?php echo $object_id; ?>").append('<option selected="selected" value="'+node.data.key+'" >'+node.data.title+'</option>');
                            get_event_tree(node.data.key,$('#value_tree_' + tree+'_<?php echo $object_id; ?>').val(),tree,<?php echo $object_id; ?>);
                            $('#value_tree_' + tree+'_<?php echo $object_id; ?>').val(node.data.key);
                        }
                    },
                    onKeydown: function (node, event) {
                    },
                    onCreate: function (node, span) {
                        $("#field_event_property_term_"+tree+'_<?php echo $object_id; ?>').dynatree("getRoot").visit(function(node){
                          // delete_value(node.data.key); 
                           if(categories.indexOf(node.data.key)>-1){
                                 //node.select();
                                 $("#socialdb_propertyterm_"+tree+"_<?php echo $object_id; ?>").html('');
                                 $("#socialdb_propertyterm_"+tree+"_<?php echo $object_id; ?>").append('<option selected="selected" value="'+node.data.key+'" >'+node.data.title+'</option>');
                                 $('#value_tree_' + tree+'_<?php echo $object_id; ?>').val(node.data.key);
                            }
                        });
                    },
                    onPostInit: function (isReloading, isError) {
                    },
                    onActivate: function (node, event) {
                    },
                    onSelect: function (flag, node) {
                         add_classification(<?php echo $object_id; ?>,node.data.key);
                         //var selKeys = $.map(node.tree.getSelectedNodes(), function (node) {
                         //   return node;
                         //});
          ///               $("#socialdb_propertyterm_"+tree+"_<?php echo $object_id; ?>").html('');
                        //$.each(selKeys,function(index,key){
                        //    $("#socialdb_propertyterm_"+tree).append('<option selected="selected" value="'+key.data.key+'" >'+key.data.title+'</option>')
                       // });
                    },
                    dnd: {
                    }
                });
            });
        }
    }
    
    
    
    // get value of the property
    function event_get_val(value) {
        if (value === '') {
            return false;
        } else if (value.split(',')[0] === '' && value !== '') {
            return [value];
        } else {
            return value.split(',');
        }
    }
    // retira do array de categorias que sao do objeto
    function event_delete_value(category_id){
       var classifications =   $("#object_classifications_event_<?php echo $object_id; ?>").val().split(',');
       if(classifications.length>0&&category_id){
           var index = classifications.indexOf(category_id);
           if(index>-1){
               classifications.splice(index, 1);
               $("#object_classifications_event").val(classifications.join());
           }
       }
    }
    //add_classification
    function add_classification(object_id,term_id){
        swal({
            title: '<?php  _e('Add classification') ?>',
            text: '<?php _e('Are you sure to include this classification') ?>',
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
                    elem_first = jQuery.parseJSON(result);
                    show_classifications(object_id);
                    list_properties(object_id);
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);

                });
            }else{
                 list_properties(object_id);
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
                    elem_first = jQuery.parseJSON(result);
                    show_classifications(object_id);
                    list_properties(object_id);
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);

                });
            }else{
                 list_properties(object_id);
            }
        });
    }
    
    
    //get the event on checbox
    function get_event_checkbox(e,object_id){
        var is_checked = $(e).is(":checked");
        if(is_checked){
            add_classification(object_id,$(e).val());
        }else{
            remove_classication('<?php  _e('Remove classification') ?>','<?php _e('Are you sure to remove this classification') ?>',$(e).val(),object_id,'<?php echo mktime(); ?>');
        }
    }
    
    //get multipleselect values
    function get_event_multiple(e,object_id){
       var flag = false;
        var is_selected = $(e).is(":selected");
        var classifications =   $("#object_classifications_event_<?php echo $object_id; ?>").val().split(',');
        if(classifications.length>0&&$(e).val()){
            var index = classifications.indexOf($(e).val());
            if(index>-1){
                remove_classication('<?php  _e('Remove classification') ?>','<?php _e('Are you sure to remove this classification') ?>',$(e).val(),object_id,'<?php echo mktime(); ?>');
                flag = true;
            }
        }
        if(!flag&&is_selected){
               add_classification(object_id,$(e).val());
        }else if(!flag){
              remove_classication('<?php  _e('Remove classification') ?>','<?php _e('Are you sure to remove this classification') ?>',$(e).val(),object_id,'<?php echo mktime(); ?>');
        }
    }
    
    function get_event_radio(e,property_id,object_id){
       var before_category = $('#value_radio_'+property_id+'_'+object_id).val();
       $('#value_radio_'+property_id+'_'+object_id).val($(e).val());
        swal({
            title: '<?php  _e('Add classification') ?>',
            text: '<?php _e('Are you sure to include this classification? This action removes the previous selected category') ?>',
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
                    list_properties(object_id);
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
                        socialdb_event_classification_term_id: before_category,
                        socialdb_event_classification_type: 'category',
                        socialdb_event_collection_id: $('#collection_id').val()}
                }).done(function (result) {
                    elem_first = jQuery.parseJSON(result);
                });
               
            }else{
                 list_properties(object_id);
            }
        });       
    }
    
    function get_event_select(e,property_id,object_id){
       if($(e).val()!==''){
            var before_category = $('#value_select_'+property_id+'_'+object_id).val();
            $('#value_select_'+property_id+'_'+object_id).val($(e).val());
             swal({
                 title: '<?php  _e('Add classification') ?>',
                 text: '<?php _e('Are you sure to include this classification? This action removes the previous selected category') ?>',
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
                         list_properties(object_id);
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
                             socialdb_event_classification_term_id: before_category,
                             socialdb_event_classification_type: 'category',
                             socialdb_event_collection_id: $('#collection_id').val()}
                     }).done(function (result) {
                         elem_first = jQuery.parseJSON(result);
                     });

                 }else{
                      list_properties(object_id);
                 }
             });
        }
    }
    
    function get_event_tree(value_actual,value_before,property_id,object_id){
        swal({
                 title: '<?php  _e('Add classification') ?>',
                 text: '<?php _e('Are you sure to include this classification? This action removes the previous selected category') ?>',
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
                             socialdb_event_classification_term_id: value_actual,
                             socialdb_event_classification_type: 'category',
                             socialdb_event_collection_id: $('#collection_id').val()}
                     }).done(function (result) {
                         elem_first = jQuery.parseJSON(result);
                         show_classifications(object_id);
                         list_properties(object_id);
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
                             socialdb_event_classification_term_id: value_before,
                             socialdb_event_classification_type: 'category',
                             socialdb_event_collection_id: $('#collection_id').val()}
                     }).done(function (result) {
                         elem_first = jQuery.parseJSON(result);
                     });

                 }else{
                      list_properties(object_id);
                 }
             });
    }
    
    
</script>
