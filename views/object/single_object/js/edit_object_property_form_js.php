<script>
    $(function () {
        $('#single_submit_form_event_edit_property_object').submit(function (e) {
            if($('#property_object_category_id').val()===''){
                hide_modal_main();
                showAlertGeneral('<?php _e('Attention!','') ?>','<?php _e('Object metadata requires a category to search items!','') ?>', 'info');
            }else {

                show_modal_main();
                $.ajax({
                    url: $('#src').val() + '/controllers/event/event_controller.php',
                    type: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false
                }).done(function (result) {
                    hide_modal_main();
                    elem = jQuery.parseJSON(result);
                    back_button_single($('#single_event_edit_property_object_post_id').val());
                    //$("#dynatree").dynatree("getTree").reload();
                    list_properties_single($('#single_event_edit_property_object_post_id').val());
                    showAlertGeneral(elem.title, elem.msg, elem.type);
                    //limpando caches
                    delete_all_cache_collection();
                });
            }
            e.preventDefault();
        });
        if($('#single_event_edit_property_object_is_reverse_value').val()=='true'){
           var val = $('#single_event_edit_property_object_reverse_value').val();
            single_list_reverses_edit_event(val);
            $('#single_event_edit_show_reverse_properties').show();
        }
        // reverse property
        $("#single_event_edit_property_object_category_id").change(function (e) {
            $('#single_event_edit_show_reverse_properties').hide();
            $('#single_event_edit_property_object_is_reverse_false').prop('checked', true);
        });
        // reverse property    
        $('#single_event_edit_property_object_is_reverse_true').click(function (e) {
            $('#single_event_edit_show_reverse_properties').show();
        });
        //reverse property  
        $('#single_event_edit_property_object_is_reverse_false').click(function (e) {
            $('#single_event_edit_show_reverse_properties').hide();
        });
        showPropertyCategoryDynatreeEdit($('#src').val());
    });
     // lista as propriedades da categoria que foi selecionada
    function single_list_reverses_edit_event(selected) {
        $.ajax({
            url: $('#src').val() + '/controllers/property/property_controller.php',
            type: 'POST',
            data: {collection_id: $("#collection_id").val(), category_id: $("#property_object_category_id").val(), operation: 'show_reverses', property_id: $('#single_event_edit_property_object_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#single_event_edit_property_object_reverse').html('');
            if (elem.no_properties === false) {
                $.each(elem.property_object, function (idx, property) {
                    if (property.id == selected) {
                        $('#single_event_edit_property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                    } else {
                        $('#single_event_edit_property_object_reverse').append('<option value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                    }
                });
            } else {
                $('#single_event_edit_property_object_reverse').append('<option value="false"><?php _e('No properties added','tainacan'); ?></option>');
            }
        });
    }
    
     function showPropertyCategoryDynatreeEdit(src) {
        $("#property_category_dynatree_edit").dynatree({
            selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
            checkbox: true,
            initAjax: {
                  url: src + '/controllers/category/category_controller.php',
                data: {
                    collection_id: $("#collection_id").val(),
                    operation: 'initDynatreeTerms',
                    hideCheckbox: 'false'
                }
                , addActiveKey: true
            },
            onLazyRead: function (node) {
                $.when(
                    node.appendAjax({
                        url: src + '/controllers/category/category_controller.php',
                        data: {
                            collection_id: $("#collection_id").val(),
                            category_id: node.data.key,
                            classCss: node.data.addClass,
                            operation: 'findDynatreeChild',
                            selectedCategories:$('#helper_object_category_id').val(),
                        }
                    })
                ).then(function(){
//                    if(node.bExpanded === true)
//                    {
//                        let ids = $('#property_object_category_id').val().split(',');
//                        node.childList.forEach(function (item, indice){
//                            if(item.bSelected === true)
//                            {
//                                ids.push(item.data.key);
//                            }
//                        });
//                        $('#property_object_category_id').val(ids.filter( onlyUnique ).join(','));
//                    }
                });
            },
            onCreate: function (node, span) {
                var selectedValues = $('#helper_object_category_id').val().split(',');
                if(selectedValues.indexOf(node.data.key)>=0){
                    node.select(true);
                }
            },
            onClick: function (node, event) {
                // Close menu on click
                //$("#property_object_category_id").val(node.data.key);
                //$("#property_object_category_name").val(node.data.title);

            },
            onSelect: function (flag, node) {
                var selKeys = $.map($("#property_category_dynatree_edit").dynatree("getSelectedNodes"), function(node) {
                    return node.data.key;
                });
                if(selKeys.length>0){
                    $('#property_object_category_id').val(selKeys.join(','));
                }else{
                    $('#property_object_category_id').val('');
                }
                list_reverses_event_edit();
                <?php if(has_action('javascript_onselect_relationship_dynatree_property_object')): ?>
                    <?php do_action('javascript_onselect_relationship_dynatree_property_object') ?>
                <?php endif; ?>
            }
        });
    }

    function list_reverses_event_edit(selected) {
        if($("#property_object_category_id").val().trim()!='') {
            $.ajax({
                url: $('#src').val() + '/controllers/property/property_controller.php',
                type: 'POST',
                data: {
                    collection_id: $("#collection_id").val(),
                    category_id: $("#property_object_category_id").val(),
                    operation: 'show_reverses',
                    property_id: $('#property_category_id').val()
                }
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                $('#single_event_edit_property_object_reverse').html('');
                $('#single_event_edit_property_object_reverse').append('<option value="false"><?php _e('None', 'tainacan'); ?></option>');
                if (elem.no_properties === false) {
                    $.each(elem.property_object, function (idx, property) {
                        if (property.id == selected) {
                            $('#single_event_edit_property_object_is_reverse_true').prop('checked',true);
                            $('#single_event_edit_property_object_is_reverse_false').prop('checked',false);
                            $('#single_event_edit_property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        } else {
                            $('#single_event_editproperty_object_reverse').append('<option value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        }
                    });
                }else{
                    $('#single_event_edit_property_object_is_reverse_true').prop('checked',false);
                    $('#single_event_edit_property_object_is_reverse_false').prop('checked',true);
                }
            });
        }
    }
</script>
