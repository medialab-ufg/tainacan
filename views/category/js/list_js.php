<script>
    $(function () {
        var src = $('#src').val();
        //$('#my-wizard').wizard(); //wizard para navegacao
        $('#category_collection_id').val($('#collection_id').val());
        //// setando o valor da colecao no formulario
        $('#collection_id_hierarchy_import').val($('#collection_id').val());
        showCategoryDynatree(src);//mostra o dynatree
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
        $('#submit_form_category').submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            verify_category_privacity(formData);// esta funcao chama a que insere o formularioo de fato
        });// end submit
<?php // Submissao do form de exclusao da categoria   ?>
        $('#submit_delete_category').submit(function (e) {
            e.preventDefault();
            $('#modalExcluirCategoriaUnique').modal('hide');
            $('#modalImportMain').modal('show');//mostra o modal de carregamento
            $.ajax({
                url: src + '/controllers/category/category_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//esconde o modal de carregamento
                $('.dropdown-toggle').dropdown();
                $("#categories_dynatree").dynatree("getTree").reload();
                elem = jQuery.parseJSON(result);
                if (elem.type === 'success') {
                    $("#alert_error_categories").hide();
                    $("#alert_success_categories").show();
                } else {
                    $("#alert_error_categories").show();
                    $("#alert_success_categories").hide();
                    if (elem.message) {
                        console.log(elem.message);
                        $("#message_category").text(elem.message);
                    }
                }

            });
            e.preventDefault();
        });

<?php // Autocomplete dos usuarios moderadores de categoria   ?>
        $(".chosen-selected").keyup(function (event) {
            $("#chosen-selected-user").autocomplete({
                source: src + '/controllers/user/user_controller.php?operation=list_user',
                messages: {
                    noResults: '',
                    results: function () {
                    }
                },
                minLength: 2,
                select: function (event, ui) {
                    console.log(event);
                    var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
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
    //FUNCAO QUE REALIZA A INSERCAO OU EDICAO DE UMA CATEGORIA
    function submit_form(formData){
                $('#modalImportMain').modal('show');//mostra o modal de carregamento
                // primeira requisicao para verificar se existe uma categoria 
                // com este nome com o mesmo pai ou apenas notificar que existe
                // um no abaixo que possui esse mesmo pai
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/category/category_controller.php",
                    data: {
                        category_id: $('#category_id').val(),
                        suggested_name: $('#category_name').val(),
                        parent_id: $("#category_parent_id").val(),
                        operation_form: $("#operation_category_form").val(),
                        operation: 'verify_name_in_taxonomy'
                    }
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $("#category_description").val('');
                    $("#category_parent_name").val('');
                    $("#category_parent_id").val('');
                    $("#chosen-selected2-user").html('');
                    //se aconteceu algum erro na operacao ou existir uma categoria 
                    //com este pai e com este nome
                    if (elem.type == 'error') {
                        $('#modalImportMain').modal('hide');//esconde o modal de carregamento
                        showAlertGeneral(elem.title, elem.msg, elem.type);
                        $("#alert_error_categories").hide();
                        $("#alert_success_categories").hide();
                    }
                    // se existe algum no abaixo deste pai que possui um filho 
                    // com esse nome
                    else if (elem.type == 'info') {
                        $('#modalImportMain').modal('hide');//esconde o modal de carregamento
                        swal({
                            title: elem.title,
                            text: elem.msg,
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: 'btn-danger',
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function (isConfirm) {
                            //se o usuario optar por criar uma categoria com este nome
                            if (isConfirm) {
                                $('#modalImportMain').modal('show');//mostra o modal de carregamento
                                $.ajax({
                                    url: $('#src').val() + '/controllers/category/category_controller.php',
                                    type: 'POST',
                                    data: formData,
                                    processData: false,
                                    contentType: false
                                }).done(function (result) {
                                    $('#modalImportMain').modal('hide');//esconde o modal de carregamento
                                    $('.dropdown-toggle').dropdown();
                                    $("#categories_dynatree").dynatree("getTree").reload();
                                    //elem_first =jQuery.parseJSON(result); 
                                    elem = jQuery.parseJSON(result);
                                    if (elem.type.trim() === 'success') {
                                        $("#alert_error_categories").hide();
                                        $("#alert_success_categories").show();
                                        clean_archive_mode();
                                    } else {
                                        $("#alert_error_categories").show();
                                        $("#message_category").html(elem.msg);
                                        $("#alert_success_categories").hide();
                                    }
                                    $('#category_name').val('');
                                    $("#category_description").val('');

                                });
                               //e.preventDefault();
                            }
                            // se nao ele quiser criar a categoria sera mostrado
                            // para edicao a categoria para edicao
                            else {
                                $('#modalImportMain').modal('show');//mostra o modal de carregamento
                                $("#category_name").val($('#category_name').val());
                                $("#category_id").val(elem.id);
                                $("#operation_category_form").val('update');
                                $.ajax({
                                    type: "POST",
                                    url: $('#src').val() + "/controllers/category/category_controller.php",
                                    data: {category_id: elem.id, operation: 'get_parent'}
                                }).done(function (result) {
                                    $('#modalImportMain').modal('hide');//esconde o modal de carregamento
                                    elem = jQuery.parseJSON(result);
                                    if (elem.name) {
                                        $("#category_parent_name").val(elem.name);
                                        $("#category_parent_id").val(elem.term_id);
                                    } else {
                                        $("#category_parent_name").val('<?php _e('Category root', 'tainacan'); ?>');
                                        $("#category_parent_id").val('0');
                                    }
                                    $("#show_category_property").show();
                                    $('.dropdown-toggle').dropdown();
                                });
                                // metas desta categoria
                                $.ajax({
                                    type: "POST",
                                    url: $('#src').val() + "/controllers/category/category_controller.php",
                                    data: {category_id: elem.id, operation: 'get_metas'}
                                }).done(function (result) {
                                    elem = jQuery.parseJSON(result);
                                    console.log(elem);
                                    if (elem.socialdb_category_permission) {
                                        $("#category_permission").val(elem.socialdb_category_permission);
                                    }
                                    if (elem.socialdb_category_moderators) {
                                        $("#chosen-selected2-user").html();
                                        $.each(elem.socialdb_category_moderators, function (idx, user) {
                                            if (user && user !== false) {
                                                $("#chosen-selected2-user").append("<option class='selected' value='" + user.id + "' selected='selected' >" + user.name + "</option>");
                                            }
                                        });
                                    }
                                    $('.dropdown-toggle').dropdown();
                                });
                            }
                        });
                    }
                    // se a categoria passou por todas validacoes ela entao sera criada
                    // e todos os campos deverao ser resetados mostrando a mensagem
                    // de sucesso caso ela for criada corretamente
                    else {
                        $('#modalImportMain').modal('show');//mostra o modal de carregamento
                        $.ajax({
                            url: $('#src').val() + '/controllers/category/category_controller.php',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false
                        }).done(function (result) {
                            $('#modalImportMain').modal('hide');//esconde o modal de carregamento
                            $('.dropdown-toggle').dropdown();
                            $("#categories_dynatree").dynatree("getTree").reload();
                            $("#category_parent_name").val('<?php _e('Category root', 'tainacan'); ?>');
                            $("#category_parent_id").val('0');
                            //elem_first =jQuery.parseJSON(result); 
                            elem = jQuery.parseJSON(result);
                            if (elem.type.trim() === 'success') {
                                $("#alert_error_categories").hide();
                                $("#alert_success_categories").show();
                                clean_archive_mode();
                            } else {
                                $("#alert_error_categories").show();
                                $("#alert_success_categories").hide();
                                $("#message_category").html(elem.msg);
                            }
                            $('#category_name').val('');

                        });
                       // e.preventDefault();
                    }
                });
    }
    
    // FUNCAO QUE MOSTRA MENSAGEM CASO O USUARIO ESTEJA INSERINDO UMA CATEGORIA PRIVADA
    //  EMBAIXO DE UMA PUBLICA
    function verify_category_privacity(formData){
        if($('#category_parent_name').val()=='<?php _e('Public Categories', 'tainacan') ?>'&&$('#category_permission').val()=='private'){
             showAlertGeneral('<?php _e('Attention!', 'tainacan') ?>', '<?php _e('Please, select the category as public!', 'tainacan') ?>', "warning");   
        }else{
            submit_form(formData);
        }
    }
<?php //category properties    ?>
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
        clean_archive_mode();
        $("#show_category_property").hide();
        $("#chosen-selected2-user").html('');
        $("#category_parent_name").val('');
        $("#category_parent_id").val('0');
        $("#category_name").val('');
        $("#category_description").val('');
        $("#category_id").val('');
        $("#operation_category_form").val('add');
    }
    function showCategoryDynatree(src) {
        $("#categories_dynatree").dynatree({
            selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
            checkbox: true,
            initAjax: {
                url: src + '/controllers/category/category_controller.php',
                data: {
                    collection_id: $("#collection_id").val(),
                    operation: 'initDynatree'
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
                        hide_checkbox: true,
                        operation: 'findDynatreeChild'
                    }
                });
                $('.dropdown-toggle').dropdown();
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

<?php // --- Contextmenu helper: REALIZA AS ACOES DO CONTEXT MENU --------------------------------------------------   ?>
    function bindContextMenu(span) {
        // Add context menu to this node:
        $(span).contextMenu({menu: "myMenu"}, function (action, el, pos) {
            // The event was bound to the <span> tag, but the node object
            // is stored in the parent <li> tag
            var node = $.ui.dynatree.getNode(el);
            console.log(node.data.key);
            switch (action) {
                case "add":
                    $("#category_name").val('');
                    $("#chosen-selected2-user").html('');
                    $("#category_parent_name").val(node.data.title);
                    $("#category_parent_id").val(node.data.key);
                    if(node.data.key=='public_categories'){
                        $("#category_permission").val('public');
                    }else{
                        $("#category_permission").val('private');
                    }
                    $("#operation_category_form").val('add');
                    $("#category_id").val('');
                    clean_archive_mode();
                    break;
                case "edit":
                    $("#category_name").val(node.data.title);
                    $("#category_id").val(node.data.key);
                    $("#operation_category_form").val('update');
                    $.ajax({
                        type: "POST",
                        url: $('#src').val() + "/controllers/category/category_controller.php",
                        data: {category_id: node.data.key, operation: 'get_parent'}
                    }).done(function (result) {
                        elem = jQuery.parseJSON(result);
                        if (elem.name) {
                            $("#category_parent_name").val(elem.name);
                            $("#category_parent_id").val(elem.term_id);
                        } else {
                            $("#category_parent_name").val('<?php _e('Category root', 'tainacan'); ?>');
                            $("#category_parent_id").val('0');
                        }
                        $("#show_category_property").show();
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
                        if(elem.term.description){
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
                    $("#category_delete_id").val(node.data.key);
                    $("#delete_category_name").text(node.data.title);
                    $('#modalExcluirCategoriaUnique').modal('show');
                    $('.dropdown-toggle').dropdown();
                    break;
                case "set_parent":
                    if ($("#category_id").val() !== node.data.key && $("#category_id").val() != node.data.key) {
                        $("#category_parent_name").val(node.data.title);
                        $("#category_parent_id").val(node.data.key);
                    } else {
                        showAlertGeneral('<?php _e('Error', 'tainacan') ?>', '<?php _e("Invalid parent", 'tainacan') ?>', 'error');
                    }
                    $('.dropdown-toggle').dropdown();
                    break;
                case "import_taxonomy":
                    show_modal_import_taxonomy(node.data.key, node.data.title)
                    $('.dropdown-toggle').dropdown();
                    break;
                case "export_taxonomy":
                    show_modal_export_taxonomy(node.data.key, node.data.title)
                    $('.dropdown-toggle').dropdown();
                    break;
                default:
                    alert("Todo: appply action '" + action + "' to node " + node);
            }
        });
    }
<?php //vincular categorias com a colecao (facetas)     ?>
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

    function show_modal_import_taxonomy(id, name) {
        $("#import_taxonomy_root_category_id").val(id);
        $("#import_taxonomy_title").text(name);
        $("#modal_import_taxonomy").modal('show');
    }

    function show_modal_export_taxonomy(id, name) {
        $("#export_taxonomy_root_category_id").val(id);
        $("#export_taxonomy_title").text(name);
        $("#export_taxonomy_content").text(name);
        $("#modal_export_taxonomy").modal('show');
    }
    /**
     * funcao que esconde o modal de exportacao de taxonomia
     */
    function hideModalExportTaxonomy(){
        $('#modal_export_taxonomy').modal('hide');
        showAlertGeneral('<?php _e('Attention!','tainacan') ?>', '<?php _e('The download will start in few seconds','tainacan') ?>', 'info');
    }


// se estiver setado para o modo de gestao arquivista

    /**
     * funcao que limpa os campos inseridos no modo de gestao arquivista
     * @returns void
     */
    function clean_archive_mode() {
        if ($("#mode").val() == '1') {
            $("#current_phase_year").val('');
            $("#current_phase_month").val('');
            $("#intermediate_phase_year").val('');
            $("#intermediate_phase_month").val('');
            $("input[name='destination']:checked").attr('checked', false);
            $("#classification_code").val('');
            $("#observation").val('');
        }
    }
    /**
     * funcao que seta os valores nos campos inseridos na gestao arquivista pelo os valores do objeto json
     * @argument {json} object O obejto json com os metadados da categoria a ser editada
     * @returns void
     */
    function set_fields_archive_mode(object) {
        if (object.socialdb_category_current_phase && object.socialdb_category_current_phase.trim() !== '') {
            var months = parseInt(object.socialdb_category_current_phase.trim());
            $("#current_phase_year").val(Math.floor(months / 12));
            $("#current_phase_month").val(months % 12);
        }
        if (object.socialdb_category_intermediate_phase && object.socialdb_category_intermediate_phase.trim() !== '') {
            var months = parseInt(object.socialdb_category_intermediate_phase.trim());
            $("#intermediate_phase_year").val(Math.floor(months / 12));
            $("#intermediate_phase_month").val(months % 12);
        }
        if (object.socialdb_category_destination && object.socialdb_category_destination.trim() !== '') {
            switch (object.socialdb_category_destination.trim()) {
                case 'elimination':
                    $("#destination_elimination").attr('checked', 'checked');
                    break;
                case 'permanent_guard':
                    $("#destination_permanent_guard").attr('checked', 'checked');
                    break;
            }
        }
        if (object.term.description && object.term.description !== '') {
            $("#observation").val(object.term.description);
        }
        if (object.socialdb_category_classification_code && object.socialdb_category_classification_code.trim() !== '') {
            $("#classification_code").val(object.socialdb_category_classification_code.trim());
        }
    }
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
