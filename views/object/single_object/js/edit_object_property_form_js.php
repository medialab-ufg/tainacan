<script>
    $(function () {
        $('#single_submit_form_event_edit_property_object').submit(function (e) {
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
            single_list_reverses_edit_event();
            $('#single_event_edit_show_reverse_properties').show();
        });
        //reverse property  
        $('#single_event_edit_property_object_is_reverse_false').click(function (e) {
            $('#single_event_edit_show_reverse_properties').hide();
        });
        showPropertyCategoryDynatree($('#src').val());
    });
<?php // lista as propriedades da categoria que foi selecionada  ?>
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
                single_list_reverses_edit_event(node.data.key);
                concatenate_in_array(node.data.key,'#property_object_category_id');
                <?php if(has_action('javascript_onselect_relationship_dynatree_property_object')): ?>
                    <?php do_action('javascript_onselect_relationship_dynatree_property_object') ?>
                <?php endif; ?>
            }
        });
    }
</script>
