<script>
    $(function () {
        $('#single_submit_form_property_object').submit(function (e) {
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
                back_button_single($('#single_event_add_property_object_id').val());
                list_properties_single($('#single_event_add_property_object_id').val());
                showAlertGeneral(elem.title, elem.msg, elem.type);
            });
            e.preventDefault();
        });
        if ($('#single_event_add_property_object_is_reverse_value').val() == 'true') {
            var val = $('#event_add_property_object_reverse_value').val();
            single_list_reverses_event(val);
            $('#single_event_add_show_reverse_properties').show();
        }
        // reverse property
        $("#single_event_add_property_object_category_id").change(function (e) {
            $('#single_event_add_show_reverse_properties').hide();
            $('#single_event_add_property_object_is_reverse_false').prop('checked', true);
        });
        // reverse property    
        $('#single_event_add_property_object_is_reverse_true').click(function (e) {
            //single_list_reverses_event();
            $('#single_event_add_show_reverse_properties').show();
        });
        //reverse property  
        $('#single_event_add_property_object_is_reverse_false').click(function (e) {
            $('#single_event_add_show_reverse_properties').hide();
        });
        showPropertyCategoryDynatree($('#src').val());
    });
    
    function single_list_reverses_event(selected) {
        $.ajax({
            url: $('#src').val() + '/controllers/property/property_controller.php',
            type: 'POST',
            data: {collection_id: $("#collection_id").val(), category_id: selected, operation: 'show_reverses', property_id: $('#single_event_add_property_object_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#single_event_add_property_object_reverse').html('');
            if (elem.no_properties === false) {
                $.each(elem.property_object, function (idx, property) {
                    if (property.id == selected) {
                        $('#single_event_add_property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                    } else {
                        $('#single_event_add_property_object_reverse').append('<option value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                    }
                });
            } else {
                $('#single_event_add_property_object_reverse').append('<option value="false"><?php _e('No properties added','tainacan'); ?></option>');
            }
        });
    }
    
    
     function showPropertyCategoryDynatree(src) {
        $("#property_category_dynatree").dynatree({
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
                node.appendAjax({
                    url: src + '/controllers/category/category_controller.php',
                    data: {
                        collection_id: $("#collection_id").val(),
                        category_id: node.data.key,
                        classCss: node.data.addClass,
                        operation: 'findDynatreeChild'
                    }
                });
            },
            onClick: function (node, event) {
                // Close menu on click
                //$("#property_object_category_id").val(node.data.key);
                //$("#property_object_category_name").val(node.data.title);

            },
            onSelect: function (flag, node) {
                var selKeys = $.map($("#property_category_dynatree").dynatree("getSelectedNodes"), function(node) {
                    return node.data.key;
                });
                if(selKeys.length>0){
                    $('#property_object_category_id').val(selKeys.join(','));
                }else{
                    $('#property_object_category_id').val('');
                }
                list_reverses_event();
                <?php if(has_action('javascript_onselect_relationship_dynatree_property_object')): ?>
                    <?php do_action('javascript_onselect_relationship_dynatree_property_object') ?>
                <?php endif; ?>
            }
        });
    }

    function list_reverses_event(selected) {
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
                $('#property_object_reverse').html('');
                $('#single_event_add_property_object_reverse').append('<option value="false"><?php _e('None', 'tainacan'); ?></option>');
                if (elem.no_properties === false) {
                    $.each(elem.property_object, function (idx, property) {
                        if (property.id == selected) {
                            $('#single_event_add_property_object_is_reverse_true').prop('checked',true);
                            $('#single_event_add_property_object_is_reverse_false').prop('checked',false);
                            $('#single_event_add_property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        } else {
                            $('#single_event_add_property_object_reverse').append('<option value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        }
                    });
                }else{
                    $('#single_event_add_property_object_is_reverse_true').prop('checked',false);
                    $('#single_event_add_property_object_is_reverse_false').prop('checked',true);
                }
            });
        }
    }
</script>
