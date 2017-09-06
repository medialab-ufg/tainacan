<script>
    $(function () {
        var src = $('#src').val();
        // botao superior que relaizao submit
        $("#button_save_and_next").click(function () {
            $("#submit_taxonomy_zone").submit();
        });
        
        if ($('#open_wizard').val() == 'true') {
            $('#btn_back_collection').hide();
            $('#submit_configuration').hide();
            $('#save_and_next').val('true');
        } else {
            $('#collection-steps').hide();
            $('#save_and_next').val('false');
        }

        change_breadcrumbs_title('<?php _e('Categories', 'tainacan') ?>');
        
        $("#conclude_config").click(function() {
            goToCollectionHome();
        });  

        $('#submit_taxonomy_zone').submit(function (e) {
            e.preventDefault();
            $('#modalExcluirCategoriaUnique').modal('hide');
            show_modal_main();//mostra o modal de carregamento
            $.ajax({
                url: src + '/controllers/category/category_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                hide_modal_main();//esconde o modal de carregamento
                $('.dropdown-toggle').dropdown();
                elem = jQuery.parseJSON(result);

                swal({
                    title: '<?php _e('Success', 'tainacan') ?>',
                    text: '<?php _e('Categories saved successfully!', 'tainacan') ?>',
                    type: 'success',
                    timer: 1500,
                    showCancelButton: false,
                    showConfirmButton: false
                });


                if (elem.save_and_next && elem.save_and_next == 'true') {
                    showPropertiesAndFilters('<?php echo get_template_directory_uri() ?>');
                } else {
                    if (elem.is_moderator) {
                        showTaxonomyZone('<?php echo get_template_directory_uri() ?>');
                    } else {
                        backToMainPage();
                    }
                }
            });
            e.preventDefault();
        });

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
                elem_first = jQuery.parseJSON(result);
                if (elem_first) {
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                    showTaxonomyZone(src);
                } else {
                    showAlertGeneral('<?php _e('Error', 'tainacan') ?>', '<?php _e('Unformated xml', 'tainacan') ?>', 'error');
                }

            });
            e.preventDefault();
        });
    });
    /**
     ****************************************************************************
     ************************* Importar Taxonomia ************************
     ****************************************************************************
     **/
    function show_modal_import_taxonomy(id, name) {
        $("#import_taxonomy_root_category_id").val(id);
        $("#import_taxonomy_title").text(name);
        $("#modal_import_taxonomy").modal('show');
    } 
    /**
     ****************************************************************************
     ************************* FUNCOES PARA AREA DE CRIACAO DE TAXONOMIAS ************************
     ****************************************************************************
     **/
    var selected_element;
    var new_category_html =
            '<span onclick="click_event_taxonomy_create_zone($(this).parent())"  style="display: none;" class="li-default taxonomy-list-name taxonomy-category-new">' +
            '<span class="glyphicon glyphicon-pencil"></span><?php _e('Click here to edit the category name', 'tainacan') ?></span>' +
            '<input type="text" ' +
            'onblur="blur_event_taxonomy_create_zone($(this).parent())"' +
            'onkeyup="keypress_event_taxonomy_create_zone($(this).parent(),event)" class="input-taxonomy-create style-input">';
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
         save_taxonomy();
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
                            "</span><input type='text' style='display: none;' class='input-taxonomy-create style-input'" +
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
    //verifica se o container possui algum li, funcao apenas caso estiver vazio
    function verify_has_li() {
        if ($('#taxonomy_create_zone').has('ul').length == 0) {
            $('#taxonomy_create_zone').html('<ul class="root_ul"><li class="taxonomy-list-create">' +
                    new_category_html + '</li></ul>')
        }
    }
    //adicionando uma categoria na irma acima
    function add_hierarchy_taxonomy_create_zone() {
        var input = $(selected_element).find('.input-taxonomy-create').first();
        if ($(input).val() === '') {
            return false;
        }
        //se ja existe
        var term = ''
        if($(selected_element).attr('term')){
            term = 'term="'+$(selected_element).attr('term')+'"';
        }
        var sibling = $(selected_element).prev();
        var children = '';
        if ($(selected_element).find('ul').first().length > 0) {
            children = "<ul >" + $(selected_element).find('ul').first().html() + '</ul>';
        }
        if (sibling.length > 0) {
            if (sibling.find('ul').first().length > 0) {
                sibling.find('ul').first().append("<li "+term+" class='taxonomy-list-create' >" +
                        "<span onclick='click_event_taxonomy_create_zone($(this).parent())' class='li-default taxonomy-list-name taxonomy-category-finished'>" + $(input).val() +
                        "</span><input type='text' style='display: none;' class='input-taxonomy-create style-input'" +
                        " onblur='blur_event_taxonomy_create_zone($(this).parent())'  onkeyup='keypress_event_taxonomy_create_zone($(this).parent(),event)' >" + children + "</li>");
            } else {
                sibling.append("<ul><li "+term+" class='taxonomy-list-create'  >" +
                        "<span onclick='click_event_taxonomy_create_zone($(this).parent())' class='li-default taxonomy-list-name taxonomy-category-finished'>" + $(input).val() +
                        "</span><input type='text' style='display: none;' class='input-taxonomy-create style-input'" +
                        " onblur='blur_event_taxonomy_create_zone($(this).parent())'  onkeyup='keypress_event_taxonomy_create_zone($(this).parent(),event)' >" + children + "</li></ul>");
            }
            $(selected_element).remove();
        }
        save_taxonomy();
    }
    //volta uma 'casa' para a categoria, subindo na hierarquia
    function remove_hierarchy_taxonomy_create_zone() {
        //verifico se nao esta querndo subir de hierarquia
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
                "<span onclick='click_event_taxonomy_create_zone($(this).parent())' class='li-default taxonomy-list-name taxonomy-category-finished'>" + $(input).val() +
                "</span><input type='text' style='display: none;' class='input-taxonomy-create style-input'" +
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
</script>
