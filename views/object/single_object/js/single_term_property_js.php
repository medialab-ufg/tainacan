<script>
$(function(){
    var src = $('#src').val();
    var collection_id = $("#collection_id").val();

    $("#property_term_collection_id").val(collection_id);
    showTermsDynatree(src);
});
var src = $('#src').val();
var selected_element;
var new_category_html =
    '<span onclick="click_event_taxonomy_create_zone($(this).parent())"  style="display: none;" class="li-default taxonomy-list-name taxonomy-category-new">' +
    '<span class="glyphicon glyphicon-pencil"></span><?php _e('Click here to edit the category name', 'tainacan') ?></span>' +
    '<input maxlength="255" type="text" ' +
    'onblur="blur_event_taxonomy_create_zone($(this).parent())"' +
    'onkeyup="keypress_event_taxonomy_create_zone($(this).parent(),event)" class="input-taxonomy-create style-input">';

function showTermsDynatree(src) {
    $("#terms_dynatree").dynatree({
        checkbox: true,
        classNames: {checkbox: "dynatree-radio"}, // Override class name for checkbox icon:
        selectMode: 1,
        selectionVisible: true, // Make sure, selected nodes are visible (expanded).
        initAjax: {
            url: src + '/controllers/category/category_controller.php',
            data: {
                collection_id: $("#collection_id").val(),
                operation: 'initDynatreeTerms'

            },
            addActiveKey: true
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
            if ($("#socialdb_property_term_root").val() !== node.data.key) {
                if ($("#socialdb_property_term_root").val() != '')
                    remove_label_box_term($("#socialdb_property_term_root").val(), "#terms_dynatree");
                $("#socialdb_property_term_root").val(node.data.key);
                $('#selected_categories_term').html('');
                add_label_box_term(node.data.key, node.data.title, '#selected_categories_term');
            } else {
                $("#socialdb_property_term_root").val('');
                remove_label_box_term(node.data.key, "#terms_dynatree");
                $('#selected_categories_term').html('');
            }

        },
        dnd: {
            preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
            revert: false, // true: slide helper back to source if drop is rejected
            onDragStart: function (node) {
                /** This function MUST be defined to enable dragging for the tree.*/

//                    logMsg("tree.onDragStart(%o)", node);
                if (node.data.isFolder) {
                    return false;
                }
                return true;
            },
            onDragStop: function (node) {
//                    logMsg("tree.onDragStop(%o)", node);
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

function toggle_advanced_configuration(seletor) {
    if ($(seletor).is(':visible')) {
        $(seletor).slideUp();
    } else {
        $(seletor).slideDown();
    }
}

function add_label_box_term(id, name, seletor) {
    $(seletor).append('<span id="label-box-' + id + '" class="label label-primary">'
        + name + ' <a style="color:white;cursor:pointer;" onclick="remove_label_box_term(' + id + ')">x</a></span>&nbsp;');
}

function remove_label_box_term(id, dynatree) {
    $('#terms_dynatree').dynatree("getRoot").visit(function (node) {
        if (node.data.key == id) {
            node.select(false);
        }
    });
    $('#label-box-' + id).remove();
}

$('#socialdb_property_vinculate_category_create').click(function (e) {
    if ($('#socialdb_property_vinculate_category_create').is(':checked')) {
        $('#terms_dynatree').fadeOut();
        $('#container_add_category').fadeIn();
    } else {
        $('#terms_dynatree').fadeIn();
        $('#container_add_category').fadeOut();
    }
});

function back_button(object_id) {
    $('#single_data_property_form_' + object_id).hide();
    $('#single_object_property_form_' + object_id).hide();
    $('#single_edit_data_property_form_' + object_id).hide();
    $('#single_edit_object_property_form_' + object_id).hide();
    $('#single_list_all_properties_' + object_id).show();
}

//verifica se o container possui algum li, funcao apenas caso estiver vazio
function verify_has_li() {
    if ($('#taxonomy_create_zone').has('ul').length == 0) {
        $('#taxonomy_create_zone').html('<ul class="root_ul"><li class="taxonomy-list-create">' +
            new_category_html + '</li></ul>')
    }
}

// quando algo eh escrito no container de cada categoria
function keypress_event_taxonomy_create_zone(object, e) {
    // pego o span com o texto
    var seletor = $(object).find('.taxonomy-list-name').first();
    // pego o primeiro input com o valor descartando os possiveis filhos
    var input = $(object).find('.input-taxonomy-create').first();
    //se estiver finalizando a edicao, isto eh, se apertar enter
    if (e.keyCode == 13 && seletor.hasClass('taxonomy-category-modified') && $(input).val() !== '') {
        //pego o valor do input
        var val = $(input).val();
        //mostro o texto
        $(seletor).show();
        //escondoo input
        $(input).hide();
        //coloco o valor do input no span do texto
        $(seletor).text($(input).val());
        //se exisitir filhos
        var children = '';
        if ($(object).find('ul').first().length > 0) {
            children = "<ul >" + $(object).find('ul').first().html() + '</ul>';
        }
        //atraves do seletor do li ou ul
        $(object)
        // create a new li item
            .before("<li class='taxonomy-list-create'   >" +
                "<span onclick='click_event_taxonomy_create_zone($(this).parent())'   class='li-default taxonomy-list-name taxonomy-category-finished'>" + val +
                "</span><input type='text' maxlength='255' style='display: none;' class='input-taxonomy-create style-input'" +
                " onblur='blur_event_taxonomy_create_zone($(this).parent())'  onkeyup='keypress_event_taxonomy_create_zone($(this).parent(),event)' >" +
                children + "</li>")
            // set plus sign again
            .html(new_category_html);
        $('#taxonomy_create_zone').find('.input-taxonomy-create').focus().is(':visible');
        e.preventDefault();
    }// se estiver deletando toda a linha
    else if ((e.keyCode == 8 || e.keyCode == 46) && $(input).val() === '') {
        $(object).remove();
        e.preventDefault();
    } else if ($(seletor).text() !== '') {
        seletor.removeClass('taxonomy-category-new');
        seletor.addClass('taxonomy-category-modified');
        $(seletor).text($(input).val());
        e.preventDefault();
    }
    save_taxonomy();
}

//subir categoria entre as suas irmas na hieraquia
function up_category_taxonomy() {
    if ($(selected_element).is(':visible') && selected_element.length > 0) {
        var prev = $(selected_element).prev();
        $(selected_element).insertBefore(prev);
        click_event_taxonomy_create_zone(selected_element);
    }
}

//descer categoria entre as suas irmas na hieraquia
function down_category_taxonomy() {
    if ($(selected_element).is(':visible') && selected_element.length > 0) {
        var prev = $(selected_element).next();
        $(selected_element).insertAfter(prev);
        click_event_taxonomy_create_zone(selected_element);
    }
}

//salva a taxonomia craida
function save_taxonomy() {
    var string = $('#taxonomy_create_zone').html();
    $('#socialdb_property_term_new_taxonomy').val(string.trim());
}

//volta uma 'casa' para a categoria, subindo na hierarquia
function remove_hierarchy_taxonomy_create_zone() {
    //verifico se nao esta querendo subir de hierarquia
    var input = $(selected_element).find('.input-taxonomy-create').first();
    if ($(input).val() === '') {
        return false;
    }
    //pego o pai direto e verifico se ja nao eh a raiz
    var parent_direct = $(selected_element).parent();
    if (parent_direct.is('div') || parent_direct.hasClass('root_ul')) {
        return false;
    }
    // guardo os filhos diretos da categoria movida
    var children = '';
    if ($(selected_element).find('ul').first().length > 0) {
        children = "<ul >" + $(selected_element).find('ul').first().html() + '</ul>';
    }
    var parent_li = parent_direct.parent();
    var parent_to_insert = parent_li.parent();
    parent_to_insert.append("<li class='taxonomy-list-create' >" +
        "<span style='display: none;' onclick='click_event_taxonomy_create_zone($(this).parent())' class='li-default taxonomy-list-name taxonomy-category-finished'>" + $(input).val() +
        "</span><input maxlength='255' type='text' value='" + $(input).val() + "'  class='input-taxonomy-create style-input'" +
        " onblur='blur_event_taxonomy_create_zone($(this).parent())'  onkeyup='keypress_event_taxonomy_create_zone($(this).parent(),event)' >" + children + "</li>");
    $(selected_element).remove();
    save_taxonomy();
}

//insere o input para adicao da categoria
function add_field_category() {
    $('.input-taxonomy-create').hide();
    $('.taxonomy-list-name').show();
    if ($(selected_element).is(':visible') && selected_element.length > 0) {
        if ($(selected_element).find('ul').first().length > 0) {
            $(selected_element).find('ul').first().append('<li class="taxonomy-list-create">' +
                new_category_html + '</li>');
        } else {
            $(selected_element).append('<ul><li class="taxonomy-list-create">' +
                new_category_html + '</li></ul>');
        }
    } else {
        if ($('#taxonomy_create_zone').has('ul').length == 0) {
            $('#taxonomy_create_zone').append('<ul class="root_ul"><li class="taxonomy-list-create">' +
                new_category_html + '</li></ul>');
        } else {
            $('#taxonomy_create_zone .root_ul').append('<li class="taxonomy-list-create">' +
                new_category_html + '</li>');
        }
    }

    $('#taxonomy_create_zone').find('.input-taxonomy-create').focus().is(':visible');
}

//quando uma categoria tem o foco perdido
function blur_event_taxonomy_create_zone(object) {
    var seletor = $(object).find('.taxonomy-list-name').first();
    var input = $(object).find('.input-taxonomy-create').first();
    if ($(input).val() === '') {
        $(object).remove();
    }
    $(seletor).show();
    $(input).hide();
    $(seletor).text($(input).val());
}

// quando se clica sobre a categoria
function click_event_taxonomy_create_zone(object) {
    $('.input-taxonomy-create').hide();
    $('.taxonomy-list-name').show();
    var seletor = $(object).find('.taxonomy-list-name').first();
    var input = $(object).find('.input-taxonomy-create').first();
    if (seletor.hasClass('taxonomy-category-finished') || seletor.hasClass('taxonomy-category-modified')) {
        $(input).val($(seletor).text());
        $(seletor).hide();
        $(input).show();
        $(input).focus();
    } else if (seletor.hasClass('taxonomy-category-new')) {
        $(seletor).hide();
        $(input).show();
        $(input).focus();
    }
    selected_element = object;
}

//adicionando uma categoria na irma acima
function add_hierarchy_taxonomy_create_zone() {
    var input = $(selected_element).find('.input-taxonomy-create').first();
    if ($(input).val() === '') {
        return false;
    }
    var sibling = $(selected_element).prev();
    var children = '';
    if ($(selected_element).find('ul').first().length > 0) {
        children = "<ul >" + $(selected_element).find('ul').first().html() + '</ul>';
    }
    if (sibling.length > 0) {
        if (sibling.find('ul').first().length > 0) {
            sibling.find('ul').first().append("<li class='taxonomy-list-create' >" +
                "<span style='display: none;'  onclick='click_event_taxonomy_create_zone($(this).parent())' class='li-default taxonomy-list-name taxonomy-category-finished'>" + $(input).val() +
                "</span><input type='text' maxlength='255' value='" + $(input).val() + "' class='input-taxonomy-create style-input'" +
                " onblur='blur_event_taxonomy_create_zone($(this).parent())'  onkeyup='keypress_event_taxonomy_create_zone($(this).parent(),event)' >" + children + "</li>");
        } else {
            sibling.append("<ul><li class='taxonomy-list-create'  >" +
                "<span style='display: none;' onclick='click_event_taxonomy_create_zone($(this).parent())' class='li-default taxonomy-list-name taxonomy-category-finished'>" + $(input).val() +
                "</span><input type='text' maxlength='255' value='" + $(input).val() + "'  class='input-taxonomy-create style-input'" +
                " onblur='blur_event_taxonomy_create_zone($(this).parent())'  onkeyup='keypress_event_taxonomy_create_zone($(this).parent(),event)' >" + children + "</li></ul>");
        }
        $(selected_element).remove();
    }
    save_taxonomy();
}

function toggle_term_widget(el) {
    if (el.checked) {
        $("#meta-category .term-widget").show();
    } else {
        $("#meta-category .term-widget").hide();
    }
}

//vinculacao de categorias
$('#socialdb_property_vinculate_category_exist').click(function (e) {
    if ($('#socialdb_property_vinculate_category_exist').is(':checked')) {
        $('#terms_dynatree').fadeIn();
        $('#container_add_category').fadeOut();
    } else {
        $('#terms_dynatree').fadeOut();
        $('#container_add_category').fadeIn();
    }
});

$('#submit_form_property_term').submit(function (e) {
    e.preventDefault();
    $('.modal').modal('hide');
    $('#modalImportMain').modal('show');
    $.ajax({
        url: src + '/controllers/property/property_controller.php',
        type: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false
    }).done(function (result) {
        elem = jQuery.parseJSON(result);
        $("#terms_dynatree").dynatree("getTree").reload();
        $('#modalImportMain').modal('hide');
        $('#socialdb_property_vinculate_category_exist').prop('checked', 'checked');
        $('#socialdb_property_vinculate_category_exist').trigger('click');
        $('#property_term_new_category').val('');
        $('#taxonomy_create_zone').html('');

        var item_was_dragged = $("#meta-category .term-widget").hasClass('select-meta-filter');
        var current_operation = elem.operation;
        var menu_style_id = elem.select_menu_style;
        var term_root_id = elem.socialdb_property_term_root;
        var ordenation = $('#meta-category input[name=filter_ordenation]:checked').val();

        if (elem.property_data_use_filter == "use_filter") {
            if (current_operation == "add_property_term") {
                setCollectionFacet("add", term_root_id, elem.property_term_filter_widget, ordenation, elem.color_facet, "", menu_style_id);
            } else if (current_operation == "update_property_term") {
                if (item_was_dragged) {
                    setCollectionFacet("add", term_root_id, elem.property_term_filter_widget, ordenation, elem.color_facet, "", menu_style_id);
                    $("#meta-category .term-widget").removeClass('select-meta-filter');
                } else {
                    setCollectionFacet("update", term_root_id, elem.property_term_filter_widget, ordenation, elem.color_facet, "", menu_style_id);
                }
            }
        }

        var object_id = $("#single_object_id").val();
        back_button(object_id);
        list_properties_single(object_id);

        showAlertGeneral(elem.title, elem.msg, elem.type);
        //$("#dynatree_properties_filter").dynatree("getTree").reload();
        //limpando caches
        delete_all_cache_collection();
    });
});
</script>