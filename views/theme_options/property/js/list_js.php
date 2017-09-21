<script>
    $(function () {
        var src = $('#src').val();
        showPropertyCategoryDynatree(src);
        showTermsDynatree(src);//mostra o dynatree
        list_property_data();
        list_property_object();
        list_property_terms();
        $('#property_term_collection_id').val('<?php echo get_option('collection_root_id') ?>');
        $('#property_data_collection_id').val('<?php echo get_option('collection_root_id') ?>');// setando o valor da colecao no formulario
        $('#property_object_collection_id').val('<?php echo get_option('collection_root_id') ?>');// setando o valor da colecao no formulario
<?php // Submissao do form de property data  ?>
        $('#submit_form_property_data_repository').submit(function (e) {
             show_modal_main();
            $("#properties_repository_menu").hide('slow');
            $("#loader_import_respository_properties").show('slow');
            e.preventDefault();
            $.ajax({
                url: src + '/controllers/property/property_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                 hide_modal_main();
                elem = jQuery.parseJSON(result);
                list_property_data();
                $('#property_data_name').val('');
                if (elem.type === 'success') {
                    get_categories_properties_ordenation();
                     $("#alert_success_properties").hide();
                    $("#alert_error_properties").hide();
                    $("#alert_success_properties").show();
                } else {
                     $("#alert_success_properties").hide();
                    $("#alert_error_properties").hide();
                    $("#alert_error_properties").show();
                }
                $("#loader_import_respository_properties").hide('slow');
                $("#properties_repository_menu").show('slow');
                $('.dropdown-toggle').dropdown();
            });
            e.preventDefault();
        });
<?php // Submissao do form de property object  ?>
        $('#submit_form_property_object').submit(function (e) {
             show_modal_main();
            $("#properties_repository_menu").hide('slow');
            $("#loader_import_respository_properties").show('slow');
            e.preventDefault();
            $.ajax({
                url: src + '/controllers/property/property_controller.php',
                type: 'POST',
                data: new FormData(this),
                async: false,
                processData: false,
                contentType: false
            }).done(function (result) {
                hide_modal_main();
                elem = jQuery.parseJSON(result);
                list_property_object();
                $('#property_object_name').val('');
                if (elem.type === 'success') {
                     $("#alert_success_properties").hide();
                    $("#alert_error_properties").hide();
                    $("#alert_success_properties").show();
                } else {
                     $("#alert_success_properties").hide();
                    $("#alert_error_properties").hide();
                    $("#alert_error_properties").show();
                }
                $("#loader_import_respository_properties").hide('slow');
                $("#properties_repository_menu").show('slow');
                $('.dropdown-toggle').dropdown();
            });
            e.preventDefault();
        });
<?php // Submissao do form de property term      ?>
        $('#submit_form_property_term').submit(function (e) {
             show_modal_main();
            e.preventDefault();
            $.ajax({
                url: src + '/controllers/property/property_controller.php',
                type: 'POST',
                data: new FormData(this),
                //async: false,
                processData: false,
                contentType: false
            }).done(function (result) {
                hide_modal_main();
                elem = jQuery.parseJSON(result);
                list_property_terms();
                clear_buttons();
                if (elem.type === 'success') {
                     $("#alert_success_properties").hide();
                    $("#alert_error_properties").hide();
                    $("#alert_success_properties").show();
                } else {
                     $("#alert_success_properties").hide();
                    $("#alert_error_properties").hide();
                    $("#alert_error_properties").show();
                }
                $('.dropdown-toggle').dropdown();
            });
            e.preventDefault();
        });
<?php // Submissao do form de para remocao  ?>
        $('#submit_delete_property').submit(function (e) {
            e.preventDefault();
             show_modal_main();
            $.ajax({
                url: src + '/controllers/property/property_controller.php',
                type: 'POST',
                data: new FormData(this),
                //async: false,
                processData: false,
                contentType: false
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                list_property_data();
                list_property_object();
                list_property_terms();
                hide_modal_main();
                if (elem.type === 'success') {
                     $("#alert_success_properties").hide();
                    $("#alert_error_properties").hide();
                    $("#alert_success_properties").show();
                } else {
                     $("#alert_success_properties").hide();
                    $("#alert_error_properties").hide();
                    $("#alert_error_properties").show();
                }
                $("#modal_remove_property").modal('hide');
                $('.dropdown-toggle').dropdown();
            });
            e.preventDefault();
        });


        $('#click_property_object_tab').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
            $('#property_category_dynatree').toggle();
        });
        $('#click_property_data_tab').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
            $('#property_category_dynatree').toggle();
        });
        $('#click_property_term_tab').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
        // reverse property
        $("#property_object_category_id").change(function (e) {
            $('#show_reverse_properties').hide();
            $('#property_object_is_reverse_false').prop('checked', true);
        });
        // reverse property    
        $('#property_object_is_reverse_true').click(function (e) {
            list_reverses();
            $('#show_reverse_properties').show();
        });
        //reverse property  
        $('#property_object_is_reverse_false').click(function (e) {
            $('#show_reverse_properties').hide();
        });
        // cardinality type 1  
        $('#socialdb_property_term_cardinality_1').click(function (e) {
            $('#socialdb_property_term_widget').html('');
            $('#socialdb_property_term_widget').append('<option value="tree"><?php _e('Tree','tainacan') ?></option>');
            $('#socialdb_property_term_widget').append('<option value="radio"><?php _e('Radio','tainacan') ?></option>');
            $('#socialdb_property_term_widget').append('<option value="selectbox"><?php _e('Selectbox','tainacan') ?></option>');
        });
        // cardinality type n  
        $('#socialdb_property_term_cardinality_n').click(function (e) {
            $('#socialdb_property_term_widget').html('');
            $('#socialdb_property_term_widget').append('<option value="checkbox"><?php _e('Checkbox','tainacan') ?></option>');
            $('#socialdb_property_term_widget').append('<option value="multipleselect"><?php _e('Multipleselect ','tainacan') ?></option>');
            $('#socialdb_property_term_widget').append('<option value="tree_checkbox"><?php _e('Tree - Checkbox','tainacan') ?></option>');
        });

        $('#socialdb_property_term_cardinality_1').trigger('click');

    });
<?php // lista as propriedades da categoria que foi selecionada  ?>
    function list_reverses(selected) {
        $.ajax({
            url: $('#src').val() + '/controllers/property/property_controller.php',
            type: 'POST',
            data: {collection_id: $("#collection_id").val(), category_id: $("#property_object_category_id").val(), operation: 'show_reverses', property_id: $('#property_category_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#property_object_reverse').html('');
            if (elem.no_properties === false) {
                $.each(elem.property_object, function (idx, property) {
                    if (property.id == selected) {
                        $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                    } else {
                        $('#property_object_reverse').append('<option value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                    }
                });
            } else {
                $('#property_object_reverse').append('<option value="false"><?php _e('No properties added','tainacan'); ?></option>');
            }
        });
    }
<?php // edicao das propriedades de atributo  ?>
    function edit_data(element) {
        var id = $(element).closest('td').find('.property_data_id').val();
        $.ajax({
            url: $('#src').val() + '/controllers/property/property_controller.php',
            type: 'POST',
            data: {collection_id: $("#collection_id").val(), operation: 'edit_property_data', property_id: id}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $("#property_data_title").text('<?php _e('Edit property','tainacan') ?>');
            $("#property_data_id").val(elem.id);
            $("#property_data_name").val(elem.name);
            $("#property_data_widget").val(elem.metas.socialdb_property_data_widget);
             $("#socialdb_property_data_default_value").val(elem.metas.socialdb_property_default_value);
            if (elem.metas.socialdb_property_data_column_ordenation === 'false') {
                $("#property_data_column_ordenation_false").prop('checked', true);
            } else {
                $("#property_data_column_ordenation_true").prop('checked', true);
            }
            if (elem.metas.socialdb_property_required === 'false') {
                $("#property_data_required_false").prop('checked', true);
            } else {
                $("#property_data_required_true").prop('checked', true);
            }
            $("#operation_property_data").val('update_property_data');
        });
    }
<?php // edicao das propriedades de objeto  ?>
    function edit_object(element) {
        var id = $(element).closest('td').find('.property_object_id').val();
        $.ajax({
            url: $('#src').val() + '/controllers/property/property_controller.php',
            type: 'POST',
            data: {collection_id: $("#collection_id").val(), operation: 'edit_property_object', property_id: id}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $("#property_object_title").text('<?php _e('Edit property','tainacan') ?>');
            $("#property_object_id").val(elem.id);
            $("#property_object_name").val(elem.name);
            $("#property_object_category_id").val(elem.metas.socialdb_property_object_category_id);
            if (elem.metas.socialdb_property_object_is_facet === 'false') {
                $("#property_object_facet_false").prop('checked', true);
            } else {
                $("#property_object_facet_true").prop('checked', true);
            }
            if (elem.metas.socialdb_property_object_is_reverse === 'false') {
                $("#property_object_is_reverse_false").prop('checked', true);
                $('#show_reverse_properties').hide();
            } else {
                $("#property_object_is_reverse_true").prop('checked', true);
                list_reverses(elem.metas.socialdb_property_object_reverse);
                $('#show_reverse_properties').show();
            }
            if (elem.metas.socialdb_property_required === 'false') {
                $("#property_object_required_false").prop('checked', true);
            } else {
                $("#property_object_required_true").prop('checked', true);
            }
            $("#operation_property_object").val('update_property_object');
        });
    }
    function edit_term(element) {
        var id = $(element).closest('td').find('.property_term_id').val();
        $.ajax({
            url: $('#src').val() + '/controllers/property/property_controller.php',
            type: 'POST',
            data: {collection_id: $("#collection_id").val(), operation: 'edit_property_term', property_id: id}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $("#property_term_title").text('<?php _e('Edit property','tainacan') ?>');
            $("#property_term_id").val(elem.id);
            $("#property_term_name").val(elem.name);
            if (elem.metas.socialdb_property_term_cardinality === '1') {
                $('#socialdb_property_term_cardinality_1').trigger('click');
            } else {
                $("#socialdb_property_term_cardinality_n").trigger('click');
            }
            if (elem.metas.socialdb_property_required === 'false') {
                $("#property_term_required_false").prop('checked', true);
            } else {
                $("#property_term_required_true").prop('checked', true);
            }
            if (elem.metas.socialdb_property_term_root) {
                get_category_root_name(elem.metas.socialdb_property_term_root);
            }
            $("#socialdb_property_term_widget").val(elem.metas.socialdb_property_term_widget);
            $("#operation_property_term").val('update_property_term');
        });
    }
<?php // excluir propriedades      ?>
    function delete_property(element,type) {
        $("#property_delete_collection_id").val($("#collection_id").val());
        var id = $(element).closest('td').find('.property_id').val();
        var name = $(element).closest('td').find('.property_name').val();
        $("#property_delete_id").val(id);
        $("#deleted_property_name").text(name);
          $("#type_metadata_form").val(type);
        $("#modal_remove_property").modal('show');

    }
<?php //limpar apenas o campo de relacao        ?>
    function clear_relation() {
        $("#property_object_category_id").val('');
        $("#property_object_category_name").val('');
    }
<?php //fechar alerts apos form        ?>
    function hide_alert() {
        $(".alert").hide();
    }
<?php //limpar botoes formulario        ?>
    function clear_buttons() {
        $('#show_reverse_properties').hide();
        $("#property_data_title").text('<?php _e('Add new property','tainacan') ?>');
        $("#property_data_id").val('');
        $("#property_data_name").val('');
        $("#property_data_widget").val('');
        $("#property_data_column_ordenation_false").prop('checked', true);
        $("#property_data_required_false").prop('checked', true);
        $("#property_object_title").text('<?php _e('Add new property','tainacan') ?>');
        $("#property_object_id").val('');
        $("#property_object_name").val('');
        $("#property_object_category_id").val('');
        $("#property_object_facet_false").prop('checked', true);
        $("#property_object_is_reverse_false").prop('checked', true);
        $("#property_object_required_false").prop('checked', true);

        $("#property_term_title").text('<?php _e('Add new property','tainacan') ?>');
        $("#property_term_id").val('');
        $("#property_term_name").val('');
        
        $('#default_field').show();
         $('#required_field').show();

        $("#operation_property_data").val('add_property_data');
        $("#operation_property_object").val('add_property_object');
        $("#operation_property_term").val('add_property_term');
        $('#submit_form_property_term').parents('form').find('input[type=text],textarea,select').filter(':visible').val('');
    }
<?php //vincular categorias com a colecao (facetas)        ?>
    function add_facets() {
        var selKeys = $.map($("#categories_dynatree").dynatree("getSelectedNodes"), function (node) {
            return node.data.key;
        });
        var selectedCategories = selKeys.join(",");
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/category/category_controller.php",
            data: {collection_id: $('#category_collection_id').val(), operation: 'vinculate_facets', facets: selectedCategories}
        }).done(function (result) {
            $('.dropdown-toggle').dropdown();
            $("#categories_dynatree").dynatree("getTree").reload();
            elem = jQuery.parseJSON(result);
            if (elem.success === 'true') {
                $("#alert_success_categories").toggle();
            } else {
                $("#alert_error_categories").toggle();
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
                    operation: 'initPropertyDynatree'
                }
                , addActiveKey: true
            },
            onLazyRead: function (node) {
                node.appendAjax({
                    url: src + '/controllers/category/category_controller.php',
                    data: {
                        collection_id: $("#collection_id").val(),
                        category_id: node.data.key,
                        operation: 'findDynatreeChild'
                    }
                });
            },
            onClick: function (node, event) {
                // Close menu on click
                $("#property_object_category_id").val(node.data.key);
                $("#property_object_category_name").val(node.data.title);

            },
            onKeydown: function (node, event) {
                // Eat keyboard events, when a menu is open
                if ($(".contextMenu:visible").length > 0)
                    return false;

                switch (event.which) {

                    // Open context menu on [Space] key (simulate right click)
                    case 32: // [Space]
                        $(node.span).trigger("mousedown", {
                            preventDefault: true,
                            button: 2
                        })
                                .trigger("mouseup", {
                                    preventDefault: true,
                                    pageX: node.span.offsetLeft,
                                    pageY: node.span.offsetTop,
                                    button: 2
                                });
                        return false;

                        // Handle Ctrl-C, -X and -V
                    case 67:
                        if (event.ctrlKey) { // Ctrl-C
                            copyPaste("copy", node);
                            return false;
                        }
                        break;
                    case 86:
                        if (event.ctrlKey) { // Ctrl-V
                            copyPaste("paste", node);
                            return false;
                        }
                        break;
                    case 88:
                        if (event.ctrlKey) { // Ctrl-X
                            copyPaste("cut", node);
                            return false;
                        }
                        break;
                }
            },
            onCreate: function (node, span) {
            },
            onPostInit: function (isReloading, isError) {
                //$('#parentCat').val("Nenhum");
                //$( "#btnExpandAll" ).trigger( "click" );
            },
            onActivate: function (node, event) {
                // Close menu on click
                if ($(".contextMenu:visible").length > 0) {
                    $(".contextMenu").hide();
                    //          return false;
                }
            },
            onSelect: function (flag, node) {
            },
            dnd: {
                preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.     
                revert: false, // true: slide helper back to source if drop is rejected
                onDragStart: function (node) {
                    /** This function MUST be defined to enable dragging for the tree.*/

                    logMsg("tree.onDragStart(%o)", node);
                    if (node.data.isFolder) {
                        return false;
                    }
                    return true;
                },
                onDragStop: function (node) {
                    logMsg("tree.onDragStop(%o)", node);
                },
                onDragEnter: function (node, sourceNode) {
                    if (node.parent !== sourceNode.parent)
                        return false;
                    return ["before", "after"];
                },
                onDrop: function (node, sourceNode, hitMode, ui, draggable) {
                    sourceNode.move(node, hitMode);
                }
            }
        });
    }
<?php //listar propriedades de dados        ?>
    function list_property_data() {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/property/property_controller.php",
            data: {collection_id: $('#category_collection_id').val(), operation: 'list_property_data', category_id: $('#property_category_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if (elem.no_properties !== true) {
                $('#no_properties_data').hide();
                $('#table_property_data').html('');
                $('#table_property_data').append('<tr><td><?php _e('Title','tainacan') ?></td><td><?php _e('text','tainacan') ?></td><td></td><td></td></tr>');
                $('#table_property_data').append('<tr><td><?php _e('Type','tainacan') ?></td><td><?php _e('radio','tainacan') ?></td><td></td><td></td></tr>');
                $('#table_property_data').append('<tr><td><?php _e('Format','tainacan') ?></td><td><?php _e('radio','tainacan') ?></td><td></td><td></td></tr>');
                $('#table_property_data').append('<tr><td><?php _e('Content','tainacan') ?></td><td><?php _e('file/textarea','tainacan') ?></td><td></td><td></td></tr>');
                $('#table_property_data').append('<tr><td><?php _e('Source','tainacan') ?></td><td><?php _e('text','tainacan') ?></td><td></td><td></td></tr>');
                $('#table_property_data').append('<tr><td><?php _e('Description','tainacan') ?></td><td><?php _e('text','tainacan') ?></td><td></td><td></td></tr>');
                $('#table_property_data').append('<tr><td><?php _e('Tags','tainacan') ?></td><td><?php _e('text','tainacan') ?></td><td></td><td></td></tr>');
                $.each(elem.property_data, function (idx, property) {
                    if ((property.metas.is_repository_property && property.metas.is_repository_property === true&&$('#property_category_id').val() !== property.metas.socialdb_property_created_category) ||
                            (property.metas.socialdb_property_created_category && $('#property_category_id').val() !== property.metas.socialdb_property_created_category)) {
                        $('#table_property_data').append('<tr><td>' + property.name + '</td><td>' + property.type + '</td><td></td><td></td></tr>');
                    } else {
                        $('#table_property_data').append('<tr><td>' + property.name + '</td><td>' + property.type + '</td><td><input type="hidden" class="property_data_id" value="' + property.id + '"><a onclick="edit_data(this)" class="edit_property_data" href="#submit_form_property_data"><span class="glyphicon glyphicon-edit"><span></a></td><td><input type="hidden" class="property_id" value="' + property.id + '"><input type="hidden" class="property_name" value="' + property.name + '"><a onclick="delete_property(this,1)" class="delete_property" href="#"><span class="glyphicon glyphicon-remove"><span></a></td></tr>');
                    }

                });
                $('#list_properties_data').show();
            } else {
                $('#no_properties_data').hide();
                $('#table_property_data').html('');
                $('#table_property_data').append('<tr><td><?php _e('Title','tainacan') ?></td><td><?php _e('text','tainacan') ?></td><td></td><td></td></tr>');
                $('#table_property_data').append('<tr><td><?php _e('Type','tainacan') ?></td><td><?php _e('radio','tainacan') ?></td><td></td><td></td></tr>');
                $('#table_property_data').append('<tr><td><?php _e('Format','tainacan') ?></td><td><?php _e('radio','tainacan') ?></td><td></td><td></td></tr>');
                $('#table_property_data').append('<tr><td><?php _e('Content','tainacan') ?></td><td><?php _e('textarea','tainacan') ?></td><td></td><td></td></tr>');
                $('#table_property_data').append('<tr><td><?php _e('Source','tainacan') ?></td><td><?php _e('text','tainacan') ?></td><td></td><td></td></tr>');
                $('#table_property_data').append('<tr><td><?php _e('Description','tainacan') ?></td><td><?php _e('textarea','tainacan') ?></td><td></td><td></td></tr>');
                $('#table_property_data').append('<tr><td><?php _e('Licenses','tainacan') ?></td><td><?php _e('radio','tainacan') ?></td><td></td><td></td></tr>');
                $('#list_properties_data').show();     
            }
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
<?php //listar propriedades de objeto        ?>
    function list_property_object() {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/property/property_controller.php",
            data: {collection_id: $('#category_collection_id').val(), operation: 'list_property_object', category_id: $('#property_category_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if (elem.no_properties !== true) {
                $('#no_properties_object').hide();
                $('#table_property_object').html('');
                $.each(elem.property_object, function (idx, property) {
                    if ((property.metas.is_repository_property && property.metas.is_repository_property === true&&$('#property_category_id').val() !== property.metas.socialdb_property_created_category) ||
                            (property.metas.socialdb_property_created_category && $('#property_category_id').val() !== property.metas.socialdb_property_created_category)) {
                        $('#table_property_object').append('<tr><td>' + property.name + '</td><td>' + property.type + '</td><td></td></tr>');
                    } else {
                        $('#table_property_object').append('<tr><td>' + property.name + '</td><td>' + property.type + '</td><td><input type="hidden" class="property_object_id" value="' + property.id + '"><a onclick="edit_object(this)" class="edit_property_object" href="#submit_form_property_object"><span class="glyphicon glyphicon-edit"><span></a></td><td><input type="hidden" class="property_id" value="' + property.id + '"><input type="hidden" class="property_name" value="' + property.name + '"><a onclick="delete_property(this,2)" class="delete_property" href="#"><span class="glyphicon glyphicon-remove"><span></a></td></tr>');
                    }
                });
                $('#list_properties_object').show();
            } else {
                $('#list_properties_object').hide();
                $('#no_properties_object').show();
            }
            $('.dropdown-toggle').dropdown();
        });
    }
<?php //listar propriedades de objeto        ?>
    function list_property_terms() {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/property/property_controller.php",
            data: {collection_id: $('#category_collection_id').val(), operation: 'list_property_terms', category_id: $('#property_category_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if (elem && elem.no_properties !== true) {
                $('#no_properties_term').hide();
                $('#table_property_term').html('');
                $.each(elem.property_terms, function (idx, property) {
                    if ((property.metas.is_repository_property && property.metas.is_repository_property === true&&$('#property_category_id').val() !== property.metas.socialdb_property_created_category) ||
                            (property.metas.socialdb_property_created_category && $('#property_category_id').val() !== property.metas.socialdb_property_created_category)) {
                        $('#table_property_term').append('<tr><td>' + property.name + '</td><td>' + property.type + '</td><td></td></tr>');
                    } else {
                        $('#table_property_term').append('<tr><td>' + property.name + '</td><td>' + property.type + '</td><td><input type="hidden" class="property_term_id" value="' + property.id + '"><a onclick="edit_term(this)" class="edit_property_term" href="#submit_form_property_term"><span class="glyphicon glyphicon-edit"><span></a></td><td><input type="hidden" class="property_id" value="' + property.id + '"><input type="hidden" class="property_name" value="' + property.name + '"><a onclick="delete_property(this,3)" class="delete_property" href="#"><span class="glyphicon glyphicon-remove"><span></a></td></tr>');
                    }
                });
                $('#list_properties_term').show();
            } else {
                $('#list_properties_term').hide();
                $('#no_properties_term').show();
            }
            $('.dropdown-toggle').dropdown();
        });
    }
    function showTermsDynatree(src) {
        $("#terms_dynatree").dynatree({
            checkbox: true,
            // Override class name for checkbox icon:
            classNames: {checkbox: "dynatree-radio"},
            selectMode: 1,
            selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
            checkbox: true,
            initAjax: {
                url: src + '/controllers/category/category_controller.php',
                data: {
                    collection_id: $("#collection_id").val(),
                    operation: 'initDynatreeTerms'
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
                        //hide_checkbox: 'true',
                        operation: 'findDynatreeChild'
                    }
                });
                $('.dropdown-toggle').dropdown();
            },
            onClick: function (node, event) {
                // Close menu on click
//                $.ajax({
//                    type: "POST",
//                    url: $('#src').val() + "/controllers/category/category_controller.php",
//                    data: {collection_id: $('#collection_id').val(), operation: 'verify_has_children', category_id: node.data.key}
//                }).done(function (result) {
//                    $('.dropdown-toggle').dropdown();
//                    elem_first = jQuery.parseJSON(result);
//                    if (elem_first.type === 'error') {
//                        showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
//                    } else {
//                        showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
//                        $("#socialdb_property_term_root").html('');
//                        $("#socialdb_property_term_root").append('<option selected="selected" value="' + node.data.key + '">' + node.data.title + '</option>');
//
//                    }
//
//                });
            },
            onKeydown: function (node, event) {
                // Eat keyboard events, when a menu is open
                if ($(".contextMenu:visible").length > 0)
                    return false;

                switch (event.which) {

                    // Open context menu on [Space] key (simulate right click)
                    case 32: // [Space]
                        $(node.span).trigger("mousedown", {
                            preventDefault: true,
                            button: 2
                        })
                                .trigger("mouseup", {
                                    preventDefault: true,
                                    pageX: node.span.offsetLeft,
                                    pageY: node.span.offsetTop,
                                    button: 2
                                });
                        return false;

                        // Handle Ctrl-C, -X and -V
                    case 67:
                        if (event.ctrlKey) { // Ctrl-C
                            copyPaste("copy", node);
                            return false;
                        }
                        break;
                    case 86:
                        if (event.ctrlKey) { // Ctrl-V
                            copyPaste("paste", node);
                            return false;
                        }
                        break;
                    case 88:
                        if (event.ctrlKey) { // Ctrl-X
                            copyPaste("cut", node);
                            return false;
                        }
                        break;
                }
            },
            onCreate: function (node, span) {
                // bindContextMenu(span);
            },
            onPostInit: function (isReloading, isError) {
                //$('#parentCat').val("Nenhum");
                //$( "#btnExpandAll" ).trigger( "click" );
            },
            onActivate: function (node, event) {
                // Close menu on click
                if ($(".contextMenu:visible").length > 0) {
                    $(".contextMenu").hide();
                    //          return false;
                }
            },
            onSelect: function (flag, node) {
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/category/category_controller.php",
                    data: {collection_id: $('#collection_id').val(), operation: 'verify_has_children', category_id: node.data.key}
                }).done(function (result) {
                    $('.dropdown-toggle').dropdown();
                    elem_first = jQuery.parseJSON(result);
                    if (elem_first.type === 'error') {
                        showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                    } else {
                        showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                        $("#socialdb_property_term_root").html('');
                        $("#socialdb_property_term_root").append('<option selected="selected" value="' + node.data.key + '">' + node.data.title + '</option>');

                    }

                });
            },
            dnd: {
                preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.     
                revert: false, // true: slide helper back to source if drop is rejected
                onDragStart: function (node) {
                    /** This function MUST be defined to enable dragging for the tree.*/

                    logMsg("tree.onDragStart(%o)", node);
                    if (node.data.isFolder) {
                        return false;
                    }
                    return true;
                },
                onDragStop: function (node) {
                    logMsg("tree.onDragStop(%o)", node);
                },
                onDragEnter: function (node, sourceNode) {
                    if (node.parent !== sourceNode.parent)
                        return false;
                    return ["before", "after"];
                },
                onDrop: function (node, sourceNode, hitMode, ui, draggable) {
                    sourceNode.move(node, hitMode);
                }
            }
        });
    }

    function get_category_root_name(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/category/category_controller.php",
            data: {operation: 'get_category_root_name', category_id: id}
        }).done(function (result) {
            $('.dropdown-toggle').dropdown();
            elem_first = jQuery.parseJSON(result);
            $("#socialdb_property_term_root").html('').append('<option selected="selected" value="' + elem_first.key + '">' + elem_first.title + '</option>');
        });
    }
    
     function hide_fields(e){
        if($(e).val()==='autoincrement'){
            $('#default_field').hide();
            $('#required_field').hide();
        }else{
            $('#default_field').show();
            $('#required_field').show();
        }
    }

</script>

