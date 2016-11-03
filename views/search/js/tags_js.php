<script>
    $(function () {
        var src = $('#src').val();
        //$('#my-wizard').wizard(); //wizard para navegacao
        $('#category_collection_id').val($('#collection_id').val());
        //// setando o valor da colecao no formulario
        $('#collection_id_hierarchy_import').val($('#collection_id').val());
        showTagDynatree(src);//mostra o dynatree
        // Submissao do form de importacao   
        $('#import_taxonomy_submit').submit(function (e) {
            e.preventDefault();
            $("#modal_import_taxonomy").modal('hide');
            $('#modalImportMain').modal('show');
            $.ajax({
                url: src + '/controllers/category/category_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                $('#modalImportMain').modal('hide');
                $('.dropdown-toggle').dropdown();
                $("#categories_dynatree").dynatree("getTree").reload();
                elem_first = jQuery.parseJSON(result);
                if (elem_first) {
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                } else {
                    showAlertGeneral('<?php _e('Error', 'tainacan') ?>', '<?php _e('Unformated xml', 'tainacan') ?>', 'error');
                }

            });
            e.preventDefault();
        });

<?php
// SUBMISSAO DO FORMULARIO
?>
        $('#submit_form_tag').submit(function (e) {
            e.preventDefault();
            if ($('#operation_tag_form').val() == 'add') {
                var data = { socialdb_event_tag_suggested_name: $('#tag_name').val(),
                    socialdb_event_tag_description: $('#tag_description').val(),
                    socialdb_event_collection_id: $('#tag_single_collection_id').val(),
                    socialdb_event_create_date: $('#tag_single_create_time').val(),
                    socialdb_event_user_id: $('#tag_single_user_id').val(),
                    operation: 'add_event_tag_create'};
            } else {
                var data = { socialdb_event_tag_id: $('#tag_id').val(),
                    socialdb_event_tag_suggested_name: $('#tag_name').val(),
                    socialdb_event_tag_description: $('#tag_description').val(),
                    socialdb_event_collection_id: $('#tag_single_collection_id').val(),
                    socialdb_event_create_date: $('#tag_single_create_time').val(),
                    socialdb_event_user_id: $('#tag_single_user_id').val(),
                    operation: 'add_event_tag_edit'};
            }
            $('#modalImportMain').modal('show');//mostra o modal de carregamento
            $.ajax({
                url: $('#src').val() + '/controllers/event/event_controller.php',
                type: 'POST',
                data: data
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//esconde o modal de carregamento
                $('#tag_name').val('');
                $('#tag_description').val('');
                elem = jQuery.parseJSON(result);
                $("#tags_dynatree").dynatree("getTree").reload();
                reinit_synonyms_tree();
                reinit_tag_tree();
                showAlertGeneral(elem.title, elem.msg, elem.type);
                showHeaderCollection($('#src').val());
                wpquery_clean();
                $('.nav-tabs').tab();
            });
        });// end submit
<?php // Submissao do form de exclusao da categoria            ?>
        $('#submit_delete_tag').submit(function (e) {
            e.preventDefault();
            $('#modalExcluirTagUnique').modal('hide');
            $('#modalImportMain').modal('show');//mostra o modal de carregamento
            $.ajax({
                url: $('#src').val() + '/controllers/event/event_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//esconde o modal de carregamento
                $('.dropdown-toggle').dropdown();
                $("#tags_dynatree").dynatree("getTree").reload();
                elem = jQuery.parseJSON(result);
                showAlertGeneral(elem.title, elem.msg, elem.type);
            });
            e.preventDefault();
        });
        
<?php // Autocomplete dos usuarios moderadores de categoria ?>
        $(".chosen-selected").keyup(function (event) {
            $("#chosen-selected-user").autocomplete({
                source: src + '/controllers/user/user_controller.php?operation=list_user',
                messages: { noResults: '', results: function () { } },
                minLength: 2,
                select: function (event, ui) {
                    // console.log(event);
                    // var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                    var temp = $("#chosen-selected2-user [value='" + ui.item.value + "']").val();
                    if (typeof temp == "undefined") {
                        $("#chosen-selected2-user").append("<option class='selected' value='" + ui.item.value + "' selected='selected' >" + ui.item.label + "</option>");
                    }
                    setTimeout(function () {
                        $("#chosen-selected-user").val('');
                    }, 100);
                }
            });
        });
        $(".chosen-selected2").click(function () {
            $('option:selected', this).remove();
            //$('.chosen-selected2 option').prop('selected', 'selected');
        });

    });


<?php // category properties  ?>
    function list_category_property() {
        $.ajax({
            url: $('#src').val() + '/controllers/property/property_controller.php',
            type: 'POST',
            data: {operation: 'list', category_id: $("#category_id").val(), collection_id: $("#collection_id").val()}
        }).done(function (result) {
            $('#category_property').html(result);
            $('#modal_category_property').modal('show');
            $('#btn_back_collection').hide();
            $('#btn_back_collection_hide_modal').show();
        });
    }

    function clear_buttons() {
        $("#tag_name").val('');
        $("#tag_description").val('');
        $("#tag_id").val('');
        $("#operation_tag_form").val('add');
    }

    function showTagDynatree(src) {
        $("#tags_dynatree").dynatree({
            selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
            checkbox: false,
            initAjax: {
                url: src + '/controllers/collection/collection_controller.php',
                data: {
                    collection_id: $("#collection_id").val(),
                    operation: 'initDynatreeTags'
                }
                , addActiveKey: true
            },
            onClick: function (node, event) {
                // Close menu on click
                if ($(".contextMenu:visible").length > 0) {
                    $(".contextMenu").hide();
                    //          return false;
                }
            }, onRender: function (isReloading, isError) {
                // var selNodes = node.tree.getSelectedNodes();
                //console.log(selNodes);

            },
            onKeydown: function (node, event) {

            },
            onCreate: function (node, span) {
                bindContextMenu(span);
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

<?php // --- Contextmenu helper: REALIZA AS ACOES DO CONTEXT MENU --------------------------------------------------            ?>
    function bindContextMenu(span) {
        // Add context menu to this node:
        $(span).contextMenu({menu: "myMenu"}, function (action, el, pos) {
            // The event was bound to the <span> tag, but the node object
            // is stored in the parent <li> tag
            var node = $.ui.dynatree.getNode(el);
            console.log(node.data.key);
            switch (action) {
                case "edit":
                    $("#tag_name").val(node.data.title);
                    $("#tag_id").val(node.data.key);
                    $("#operation_tag_form").val('update');
                    $.ajax({
                        type: "POST",
                        url: $('#src').val() + "/controllers/tag/tag_controller.php",
                        data: {tag_id: node.data.key, operation: 'get_tag'}
                    }).done(function (result) {
                        elem = jQuery.parseJSON(result);
                        if (elem.term.description) {
                            $("#tag_description").val(elem.term.description);
                        } else {
                            $("#tag_description").val('');
                        }
                        $('.dropdown-toggle').dropdown();
                    });
                    // metas
                    $.ajax({
                        type: "POST",
                        url: $('#src').val() + "/controllers/category/category_controller.php",
                        data: {category_id: node.data.key, operation: 'get_metas'}
                    }).done(function (result) {
                        elem = jQuery.parseJSON(result);
                        // console.log(elem);
                        if (elem.term.description) {
                            $("#category_description").val(elem.term.description);
                        }
                        if (elem.socialdb_category_permission) {
                            $("#category_permission").val(elem.socialdb_category_permission);
                        }
                        if (elem.socialdb_category_moderators) {
                            $("#chosen-selected2-user").html('');
                            $.each(elem.socialdb_category_moderators, function (idx, user) {
                                if (user && user !== false) {
                                    $("#chosen-selected2-user").append("<option class='selected' value='" + user.id + "' selected='selected' >" + user.name + "</option>");
                                }
                            });
                        }
                        set_fields_archive_mode(elem);
                        $('.dropdown-toggle').dropdown();
                    });
                    break;
                case "delete":
                    // console.log(node.data);
                    $("#tag_single_delete_id").val(node.data.key);
                    $("#delete_tag_name").text(node.data.title);
                    $('#modalExcluirTagUnique').modal('show');
                    $('.dropdown-toggle').dropdown();
                    break;
                default:
                    alert("Todo: appply action '" + action + "' to node " + node);
            }
        });
    }
<?php //vincular categorias com a colecao (facetas)              ?>
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


// se estiver setado para o modo de gestao arquivista

    /**
     * funcao que nao permite que o numer oem anos seja menor do que 0
     * @argument {object DOM} input O valor colocado no input
     * @returns void
     */
    function handleChange(input) {
        if (input.value <= 0)
            input.value = '';
    }
</script>
