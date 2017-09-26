<script>
function show_edit_data_property_form(object_id,property_id){
    $.ajax({
        type: "POST",
        url: $('#src').val()+"/controllers/object/objectsingle_controller.php",
        data: {collection_id:  $('#collection_id').val(),operation:'show_edit_data_property_form',property_id:property_id,object_id:object_id}
    }).done(function( result ) {
        $('#single_edit_data_property_form_'+object_id).html(result);
        $('#single_list_all_properties_'+object_id).hide();
        $('#single_data_property_form_'+object_id).hide();
        $('#single_object_property_form_'+object_id).hide();
        $('#single_edit_object_property_form_'+object_id).hide();
        $('#single_edit_data_property_form_'+object_id).show();
        $('.dropdown-toggle').dropdown();
        $('.nav-tabs').tab();
    });
}

function show_edit_term_property_form(object_id, property_id)
{
    var promise = show_form_term_property_single(object_id);
    promise.done(function(){
        $("#create_new").hide();

        $.ajax({
            url: $('#src').val() + '/controllers/property/property_controller.php',
            type: 'POST',
            data: {collection_id: $("#collection_id").val(), operation: 'edit_property_term', property_id: property_id}
        }).done(function (result) {
            $("#submit_term_form").text('<?php _e("Update", "tainacan"); ?>');
            elem = $.parseJSON(result);
            var visualization = (elem.metas.socialdb_property_visualization) ? elem.metas.socialdb_property_visualization : 'public';
            var locked = (elem.metas.socialdb_property_locked) ? elem.metas.socialdb_property_locked : false;
            var default_value = (elem.metas.socialdb_property_default_value) ? elem.metas.socialdb_property_default_value : false;
            var habilitate_new_category = (elem.metas.socialdb_property_habilitate_new_category) ? elem.metas.socialdb_property_habilitate_new_category : false;

            $('#socialdb_property_vinculate_category_exist').prop('checked', 'checked');
            $('#socialdb_property_vinculate_category_exist').trigger('click');

            if ($("#meta-item-" + property_id).hasClass('root_category')) {
                $("#meta-category .metadata-common-fields").hide();
            } else {
                $("#meta-category .metadata-common-fields").show();
            }

            //metadado fixo
            if (!$("#meta-item-" + property_id).hasClass('fixed-property')) {
                $("#meta-category .metadata-fixed-fields").hide();
                $('#is_property_fixed_term').val('false');
                $("#property_term_name").val(elem.name);
            } else if ('<?php echo get_user_by('email', get_option('admin_email'))->ID ?>' == $('#current_user_id').val()) {
                $("#meta-category .metadata-fixed-fields").show();
                $('#property_fixed_name_term').val(elem.name);
                $('#is_property_fixed_term').val('true');
            }

            $("#property_term_id").val(elem.id);

            $("#meta-category #property_term_name").val(elem.name);
            $("#meta-category #socialdb_property_help").val(elem.metas.socialdb_property_help);

            if (elem.metas.socialdb_property_term_cardinality === '1') {
                $('#meta-category #socialdb_property_term_cardinality_1').prop('checked', true);
            } else {
                $("#meta-category #socialdb_property_term_cardinality_n").prop('checked', true);
            }

            if (elem.metas.socialdb_property_required === 'true') {
                $("#property_term_required_true").prop('checked', true);
            } else {
                $("#property_term_required_true").prop('checked', false);
            }

            if (elem.metas.socialdb_property_help) {
                $("#socialdb_property_help").val(elem.metas.socialdb_property_help);
            }
            if (visualization == 'restrict') {
                $("#socialdb_property_term_visualization_restrict").prop('checked', true);
                $("#socialdb_property_term_visualization_public").removeAttr('checked');
            } else {
                $("#socialdb_property_term_visualization_public").prop('checked', true);
                $("#socialdb_property_term_visualization_restrict").removeAttr('checked');
            }

            //se o campo esta travado para edicao
            $("#meta-category .property_lock_field").removeAttr('checked');
            if (locked) {
                $("#meta-category .property_lock_field").prop('checked', true);
            }

            var term_root = elem.metas.socialdb_property_term_root;
            if (term_root) {
                setTimeout(function(){
                    get_category_root_name(term_root);
                }, 500);
            }

            //habilitar novo item
            $("#meta-category #new_item_true").removeAttr('checked');
            $("#meta-category #new_item_false").removeAttr('checked');
            if (habilitate_new_category == 'true') {
                $("#meta-category #new_item_true").prop('checked', true);
            }else{
                $("#meta-category #new_item_false").prop('checked', true);
            }

            //valor default da propriedade
            $('#default_value_text_term').val('');
            $('#socialdb_property_term_default_value').val('');
            if(default_value){
                $('#default_value_text_term').val(elem.metas.socialdb_property_default_value_text);
                $('#socialdb_property_term_default_value').val(default_value);
            }

            $("#operation_property_term").val('update_property_term');

            if (is_metadata_filter(term_root)) {
                $("#property_term_filter_widget").val(elem.metas.property_term_filter_widget);
                $("#meta-category .property_data_use_filter").prop('checked', true);
                $("#meta-category .term-widget").show();
            } else if (elem.search_widget && elem.search_widget != 'false') {
                $("#meta-category .property_data_use_filter").prop("checked", true);
                //ordenations
                var $radios = $("#meta-category input:radio[name=filter_ordenation]");
                $radios.prop('checked', false);
                if ($radios.is(':checked') === false) {
                    $radios.filter('[value=' + elem.ordenation_facet + ']').prop('checked', true);
                }
                //color
                if (elem.color_facet && elem.color_facet != 'false') {
                    var $radios = $("#meta-category input:radio[name=color_facet]");
                    $radios.prop('checked', false);
                    if ($radios.is(':checked') === false) {
                        $radios.filter('[value=' + elem.color_facet + ']').prop('checked', true);
                    }
                }
                $("#meta-category .data-widget").show();
            }

        });
    });
}

function get_category_root_name(id) {
    var cont = 0;
    $("#terms_dynatree").dynatree("getRoot").visit(function (node) {
        node.select(false);
    });
    $('#selected_categories_term').html('');
    $("#terms_dynatree").dynatree("getRoot").visit(function (node) {
        if (node.data.key == id) {
            cont++;
            node.select(true);
        }
    });

    if (cont === 0) {
        $('#selected_categories_term').html('');
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/category/category_controller.php",
            data: {category_id: id, operation: 'get_metas'}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            add_label_box_term(elem.term.term_id, elem.term.name, '#selected_categories_term');
            $("#socialdb_property_term_root").val(elem.term.term_id);
        });
    }

}


function is_metadata_filter(meta_id) {
    var current_filters = $("#filters-accordion li");
    var filters_ids = [];
    $(current_filters).each(function (idx, el) {
        filters_ids.push($(el).attr('id'));
    });

    var formatted_id = meta_id.toString();
    if ($.inArray(formatted_id, filters_ids) > -1) {
        return true;
    }
}

function show_edit_object_property_form(object_id,property_id){
    $.ajax({
        type: "POST",
        url: $('#src').val()+"/controllers/object/objectsingle_controller.php",
        data: {collection_id:  $('#collection_id').val(),operation:'show_edit_object_property_form',property_id:property_id,object_id:object_id}
    }).done(function( result ) {
         $('#single_edit_object_property_form_'+object_id).html(result);
        $('#single_list_all_properties_'+object_id).hide();
        $('#single_data_property_form_'+object_id).hide();
        $('#single_object_property_form_'+object_id).hide();
        $('#single_edit_data_property_form_'+object_id).hide();
        $('#single_edit_object_property_form_'+object_id).show();
        $('.dropdown-toggle').dropdown();
        $('.nav-tabs').tab();
    });
}    


function show_confirmation_delete_property_object_event(object_id,property_id,property_name,root_id){
    swal({
        title: '<?php _e('Are you sure','tainacan') ?>',
        text: '<?php _e('Delete the object property ','tainacan') ?>'+' ('+property_name+')',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: 'btn-danger',
        closeOnConfirm: true,
        closeOnCancel: true
    },
    function (isConfirm) {
        if (isConfirm) {
            show_modal_main();
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/event/event_controller.php",
                data: {
                    operation: 'add_event_property_object_delete',
                    socialdb_event_create_date: '<?php echo mktime(); ?>',
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_property_object_delete_id: property_id,
                     socialdb_event_property_object_delete_category_root_id: root_id,
                    socialdb_event_collection_id: $('#collection_id').val()}
            }).done(function (result) {
                hide_modal_main();
                elem_first = jQuery.parseJSON(result);
                back_button(object_id);
                //$("#dynatree").dynatree("getTree").reload();
                list_properties_single(object_id);
                list_properties_edit_remove_single(object_id);
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                //limpando caches
                delete_all_cache_collection();
            });
        }
    });
}

function show_confirmation_delete_property_data_event(object_id,property_id,property_name,root_id){
    swal({
        title: '<?php _e('Are you sure','tainacan') ?>',
        text: '<?php _e('Delete the data property ','tainacan') ?>'+'( '+property_name+')',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: 'btn-danger',
        closeOnConfirm: true,
        closeOnCancel: true
    },
    function (isConfirm) {
        if (isConfirm) {
            show_modal_main();
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/event/event_controller.php",
                data: {
                    operation: 'add_event_property_data_delete',
                    socialdb_event_create_date: <?php echo mktime(); ?>,
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_property_data_delete_id: property_id,
                    socialdb_event_property_data_delete_category_root_id: root_id,
                    socialdb_event_collection_id: $('#collection_id').val()}
            }).done(function (result) {
                hide_modal_main();
                elem_first = jQuery.parseJSON(result);
                back_button_single(object_id);
                list_properties_single(object_id);
                list_properties_edit_remove_single(object_id);
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                //limpando caches
                delete_all_cache_collection();
            });
        }
    });
}
</script>
