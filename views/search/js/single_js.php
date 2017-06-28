<script>
    var search_items_query = $('#wp_query_args').val();
    var search_collections_query = $('#wp_query_args').val();
    $(function () {
        set_containers_class($('#collection_id').val());
        show_collection_properties_root($('#collection_id').val());
        // *************** Iframe Popover Collection ****************
        $('[data-toggle="popover"]').popover();
        $('[data-toggle="tooltip"]').tooltip();
        $("textarea").on("keydown",function(e) {
            var key = e.keyCode;
            // If the user has pressed enter
            if (key == 13) {
                $(this).val($(this).val()+"\n");
                return false;
            }
            else {
                return true;
            }
        });
        if ($('#is_filter').val() == '1') {
            $('#form').hide();
            $('#list').hide();
            $('#loader_objects').show();
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
                data: {operation: 'filter', wp_query_args: '<?php echo serialize($_GET) ?>', collection_id: $('#collection_id').val()}
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                $('#loader_objects').hide();
                $('#list').html(elem.page);
                $('#wp_query_args').val(elem.args);
                $('#list').show();
                set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
                show_filters($('#collection_id').val(), elem.args);
                if (elem.empty_collection) {
                    $('#collection_empty').show();
                    $('#items_not_found').hide();
                }
            });
        }else{
            set_popover_content($("#socialdb_permalink_collection").val());
        }
        //advanced search submit
        $('#advanced_search_collection_form').submit(function (e) {
            e.preventDefault();
            show_modal_main();
            $.ajax({
                url: $('#src').val() + '/controllers/advanced_search/advanced_search_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                console.log(elem);
                hide_modal_main();
                if (elem.args_collection) {
                    search_collections_query = elem.args_collection;
                    $('#wp_query_args').val(search_collections_query);
                    if (elem.args_item) {
                        search_items_query = elem.args_item;
                    }
                    console.log(elem.has_collection , elem.has_item);
                    if(elem.has_collection && elem.has_item){
                        $('#click_ad_search_collection').parent().show();
                        $('#click_ad_search_items').parent().show();
                        $('#click_ad_search_collection').trigger('click');
                    }else if(elem.has_collection && !elem.has_item){
                        $('#click_ad_search_collection').trigger('click');
                        $('#click_ad_search_collection').parent().show();
                        $('#click_ad_search_items').parent().hide();
                    }else if(!elem.has_collection && elem.has_item){
                        $('#click_ad_search_collection').parent().hide();
                        $('#click_ad_search_items').parent().show();
                        $('#click_ad_search_items').trigger('click');
                    }else if(!elem.has_collection && !elem.has_item){
                         $('#click_ad_search_items').trigger('click');
                        $('#click_ad_search_collection').parent().hide();
                        $('#click_ad_search_items').parent().hide();
                    }
                    
                }
                else if (elem.args_item) {
                    search_items_query = elem.args_item;
                    $('#wp_query_args').val(search_items_query);
                    if( $('#click_ad_search_items').length>0){
                        $('#click_ad_search_items').trigger('click');
                    }else{
                        wpquery_filter();
                    }
                }else{
                    wpquery_filter();
                }
            });
            e.preventDefault();
        });

        $(".sort_list").on('click', function() {
            var action = $(this).attr("id");
            $(".sort_list").css('background', 'white');
            $(this).css('background', 'buttonface');
            change_ordenation(action);
        });
    });
    
    $(document).ready(function() {
        document.title = '<?php echo html_entity_decode(get_the_title()) ?>';
    });
    //search repeated items
    function clear_repeated_values(main_value,classes){
       $.each($(classes),function(index,value){
           if($(value).attr('id') !== $(main_value).attr('id') && 
                   $(value).val() &&
                   $(main_value).val() &&
                   $(value).val().trim().toLowerCase() === $(main_value).val().trim().toLowerCase() ){
                 toastr.error($(main_value).val()+' <?php _e(' is already inserted!', 'tainacan') ?>', '<?php _e('Attention!', 'tainacan') ?>', {positionClass: 'toast-bottom-right'});
                $(main_value).val('');
           }
       });
    }
    //slideUp
    function slideFormAdvancedDown(){
        if($('#propertiesRootAdvancedSearch').is(':visible')){
            $('#propertiesRootAdvancedSearch').hide();
            $('#filters_collection').show();
            $('.text-left .clear-top-search').parent().show();
            $('#icon-search-bottom').show();
            $('#icon-search-top').hide();
            $('.search-colecao').show();
        }else{
            $('#icon-search-bottom').hide();
            $('#filters_collection').hide();
             $('.text-left .clear-top-search').parent().hide();
            $('#icon-search-top').show();
            $('#propertiesRootAdvancedSearch').show();
            $('.search-colecao').hide();
        }
    }
    //limpando do formulario de busca avancada
    function reboot_form(){ 
        if($('#propertiesRootAdvancedSearch').length>0){
            $('#propertiesRootAdvancedSearch').html('<center><img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>"><h3><?php _e('Please wait...', 'tainacan') ?></h3></center>');   
            show_collection_properties_root($('#collection_id').val());
            //wpquery_clean(); 
            search_collections_query = '';
            search_items_query = '';
            $('#click_ad_search_collection').parent().show();
            $('#click_ad_search_items').parent().show();
            $('#click_ad_search_collection').trigger('click');
        }
    }
    // atualiza o container com as propriedades da colecao que foi selecionada no selectbox
    function show_collection_properties_root(collection_id) {
        //mostro o loader para carregar os metadados
        if($('#collection_id').val()===$('#collection_root_id').val() && $('#search-advanced-text').val() != ''){
            show_modal_main();
        }
        //ajax properties
        $.ajax({
            url: $('#src').val() + '/controllers/advanced_search/advanced_search_controller.php',
            type: 'POST',
            data: {operation: 'show_object_properties_auto_load', collection_id: collection_id}
        }).done(function (result) {
            $('#propertiesRootAdvancedSearch').html(result);
            //$('#propertiesRootAdvancedSearch').show();
            revalidate_adv_autocomplete(collection_id);
            //se estiver buscando algo nos campos de busca externos e que esteja na home de colecoes
            if($('#collection_id').val()===$('#collection_root_id').val() && $('#search-advanced-text').val() != ''){
                if($('#search-advanced-text').val()!=='@')
                    $('#advanced_search_title').val($('#search-advanced-text').val());
                slideFormAdvancedDown();
                $('#advanced_search_collection_form').trigger('submit');
                $('#search-advanced-text').val('');
            }
        });
    }

    function revalidate_adv_autocomplete(collection_id) {
        $("#advanced_search_title").autocomplete({
            source: $('#src').val() + '/controllers/collection/collection_controller.php?operation=list_items_search_autocomplete_advanced_search&collection_id=' + collection_id,
            messages: {
                noResults: '',
                results: function () {
                }
            },
            minLength: 2,
            select: function (event, ui) {
                console.log(event);
                $("#advanced_search_title").val('');
                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                var temp = $("#property_value_").val();
                if (typeof temp == "undefined") {
                    $("#advanced_search_title").val(ui.item.value);
                }
            }
        });

    }

    /**************************** Comentarios **************************************************/
    function list_comments_general() {
        if ($('#socialdb_event_comment_term_id').val() == 'collection') {
            list_comments_term('comments_term', 'collection');
        } else if ($('#socialdb_event_comment_term_id').val() == '') {
            list_comments($('#single_object_id').val());
        } else {
            list_comments_term('comments_term', $('#socialdb_event_comment_term_id').val());
        }
    }


    function submit_comment(object_id) {
        if ($('#comment').val().trim() === '') {
            showAlertGeneral('<?php _e('Attention!', 'tainacan') ?>', '<?php _e('Fill your comment', 'tainacan') ?>', 'info');
        } else {
            show_modal_main();
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/event/event_controller.php",
                data: {
                    operation: 'add_event_comment_create',
                    socialdb_event_create_date: '<?php echo mktime() ?>',
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_comment_create_object_id: object_id,
                    socialdb_event_comment_create_content: $('#comment').val(),
                    socialdb_event_comment_author_name: $('#author').val(),
                    socialdb_event_comment_author_email: $('#email').val(),
                    socialdb_event_comment_author_website: $('#url').val(),
                    socialdb_event_comment_term_id: $('#socialdb_event_comment_term_id').val(),
                    socialdb_event_comment_parent: 0,
                    socialdb_event_collection_id: $('#collection_id').val()}
            }).done(function (result) {
                hide_modal_main();
                elem_first = jQuery.parseJSON(result);
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                list_comments_general();
                $("#comment_item"+object_id).modal('hide');
            });
        }
    }
    // submissao da resposta a um comentario
    function submit_comment_reply(object_id) {
        if ($('#comment_msg_reply').val().trim() === '') {
            showAlertGeneral('<?php _e('Attention!', 'tainacan') ?>', '<?php _e('Fill your comment', 'tainacan') ?>', 'info');
        } else {
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/event/event_controller.php",
                data: {
                    operation: 'add_event_comment_create',
                    socialdb_event_create_date: '<?php echo mktime() ?>',
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_comment_create_object_id: object_id,
                    socialdb_event_comment_create_content: $('#comment_msg_reply').val(),
                    socialdb_event_comment_author_name: $('#author_reply').val(),
                    socialdb_event_comment_author_email: $('#email_reply').val(),
                    socialdb_event_comment_author_website: $('#url_reply').val(),
                    socialdb_event_comment_term_id: $('#edit_socialdb_event_comment_term_id').val(),
                    socialdb_event_comment_parent: $('#comment_id').val(),
                    socialdb_event_collection_id: $('#collection_id').val()
                }
            }).done(function (result) {

                list_comments_general();
                $('.dropdown-toggle').dropdown();
                $('.nav-tabs').tab();
                $('#modalReplyComment').modal("hide");
                elem_first = jQuery.parseJSON(result);
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                $('html, body').animate({
                    scrollTop: $("#comments").offset().top
                }, 2000);
            });
        }
    }
    // mostra modal de resposta
    function showModalReply(comment_parent_id) {
        console.log($('#modalReplyComment'));
        $('#comment_id').val(comment_parent_id);
        $('#modalReplyComment').modal("show");
    }
    // mostrar modal de reportar abuso
    function showModalReportAbuseComment(comment_parent_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/comment/comment_controller.php",
            data: {
                operation: 'get_comment_json',
                comment_id: comment_parent_id
            }
        }).done(function (result) {
            var comment = jQuery.parseJSON(result);
            $('#comment_id_report').val(comment_parent_id);
            $('#description_comment_abusive').html(comment.comment.comment_content);
            $('#showModalReportAbuseComment').modal("show");
        });
    }
    // mostrar edicao
    function showEditComment(comment_id) {
        show_modal_main();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/comment/comment_controller.php",
            data: {
                operation: 'get_comment_json',
                comment_id: comment_id
            }
        }).done(function (result) {
            hide_modal_main();
            var comment = jQuery.parseJSON(result);
            $('#comment_text_' + comment_id).hide("slow");
            $('#edit_field_value_' + comment_id).val(comment.comment.comment_content);
            $('#comment_edit_field_' + comment_id).show("slow");
        });
    }
    // cancelar edicao
    function cancelEditComment(comment_id) {
        $('#comment_edit_field_' + comment_id).hide("slow");
        $('#comment_text_' + comment_id).show("slow");
    }
    // disparado quando eh dono ou admin   
    function showAlertDeleteComment(comment_id, title, text, time) {
        swal({
            title: title,
            text: text,
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
                    data: {operation: 'add_event_comment_delete', socialdb_event_create_date: time,
                        socialdb_event_user_id: $('#current_user_id').val(), socialdb_event_comment_delete_id: comment_id,
                        socialdb_event_comment_delete_object_id: $("#single_object_id").val(),
                        socialdb_event_collection_id: $('#collection_id').val()}
                }).done(function (result) {
                    hide_modal_main();
                    list_comments_general();
                    elem_first = jQuery.parseJSON(result);
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                });
            }
        });
    }
    // formulario de reportar abuso para demais usuarios
    function submit_report_abuse() {
        show_modal_main();
        if ($('#comment_msg_report').val().trim() === '') {
            showAlertGeneral('<?php _e('Attention!', 'tainacan') ?>', '<?php _e('Fill all fields', 'tainacan') ?>', 'info');
        } else {
            $('#showModalReportAbuseComment').modal("hide");
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/event/event_controller.php",
                data: {
                    operation: 'add_event_comment_delete',
                    socialdb_event_create_date: '<?php echo mktime() ?>',
                    socialdb_event_observation: $('#comment_msg_report').val(),
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_comment_delete_id: $('#comment_id_report').val(),
                    socialdb_event_collection_id: $('#collection_id').val()}
            }).done(function (result) {
                hide_modal_main();
                list_comments_general();
                elem_first = jQuery.parseJSON(result);
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
            });
        }
    }
    // submissao do formulario de edicao
    function submitEditComment(comment_id) {
        if ($('#edit_field_value_' + comment_id).val().trim() === '') {
            showAlertGeneral('<?php _e('Attention!', 'tainacan') ?>', '<?php _e('Fill your comment', 'tainacan') ?>', 'info');
        } else {
            show_modal_main();
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/event/event_controller.php",
                data: {operation: 'add_event_comment_edit', socialdb_event_create_date: '<?php echo mktime() ?>',
                    socialdb_event_user_id: $('#current_user_id').val(), socialdb_event_comment_edit_id: comment_id,
                    socialdb_event_comment_edit_object_id: $("#single_object_id").val(),
                    socialdb_event_comment_edit_content: $('#edit_field_value_' + comment_id).val(),
                    socialdb_event_collection_id: $('#collection_id').val()}
            }).done(function (result) {
                list_comments_general();
                hide_modal_main();
                $('.dropdown-toggle').dropdown();
                $('.nav-tabs').tab();
                elem_first = jQuery.parseJSON(result);
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                $('html, body').animate({
                    scrollTop: $("#comments").offset().top
                }, 2000);
            });
        }
    }

    /******************************************************************************/

    function set_popover_content(content) {
        //$('[data-toggle="popover"]').popover();
        //var myPopover = $('#iframebutton').data('popover');
        //$('#iframebutton').popover('hide');
        if ($('#iframebutton_dropdown').length>0) {
            $('#iframebutton_dropdown').html('<form style="margin:5px;">Search URL:&nbsp<input type="text" style="width:165px;" value="' + content + '" /><br><br>Iframe:&nbsp<input type="text" style="width:200px;" value="<iframe style=\'width:100%\' height=\'1000\' src=\'' + content + '\' frameborder=\'0\'></iframe>" /></form>'); 
        }
    }

    function set_popover_content_link(content) {
        $('[data-toggle="popover"]').popover();
        var myPopover = $('#linkbutton').data('popover');
        $('#linkbutton').popover('hide');
        if (myPopover) {
            myPopover.options.html = true;
            //<iframe width="560" height="315" src="https://www.youtube.com/embed/CGyEd0aKWZE" frameborder="0" allowfullscreen></iframe>
            myPopover.options.content = '<form><input type="text" style="width:200px;" value="' + content + '" /></form>';
        }
    }


    function set_containers_class(collection_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/collection/collection_controller.php",
            data: {operation: 'set_container_classes', collection_id: collection_id}
        }).done(function (result) {
            $('.dropdown-toggle').dropdown();
            elem = jQuery.parseJSON(result);
            if ($('#collection_root_id').val() == elem.collection_id) {
                $('#div_central').show();
                $('#div_central').removeClass('col-md-12');
                $('#div_central').addClass('col-md-9');
                $('#div_left').show();
                load_root_menu_left(collection_id);
            } else if (elem.has_left && elem.has_left == 'true' && (!elem.has_right || elem.has_right !== 'true')) {
                $('#div_central').show();
                $('#div_central').removeClass('col-md-12');
                $('#div_central').addClass('col-md-9');
                $('#div_left').show();
                load_menu_left(collection_id);
            } else {
<?php if (!has_filter('category_root_as_facet') || apply_filters('category_root_as_facet', true)): ?>
                    $('#div_left').hide();
                    $('#div_central').removeClass('col-md-9');
                    $('#div_central').removeClass('col-md-10');
                    $('#div_central').removeClass('col-md-12');
                    $('#div_central').addClass('col-md-12');
                    $('#div_central').show();
                    $('#div_left').html('');
<?php else: ?>
                    load_menu_left(collection_id);
<?php endif; ?>
                // load_menu_top(collection_id);
            }
        });
    }


    function load_menu_left(collection_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/collection/collection_controller.php",
            data: {operation: 'load_menu_left', collection_id: collection_id}
        }).done(function (result) {
            $('.dropdown-toggle').dropdown();
            $('#div_left').html(result);
        });
    }

    function load_root_menu_left(collection_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/collection/collection_controller.php",
            data: {operation: 'load_root_menu_left', collection_id: collection_id}
        }).done(function (result) {
            $('.dropdown-toggle').dropdown();
            $('#div_left').html(result);
        });
    }

    function list_category_property_single(category_id) {
        if (!category_id) {
            category_id = $("#category_single_edit_id").val();
        }
        var operation = '<?php echo (has_filter('tainacan_operation_metadata_category')) ? apply_filters('tainacan_operation_metadata_category', '') : 'list_metadata_category' ?>';
        $('#modalEditCategoria').modal('hide');
        $.ajax({
            url: $('#src').val() + '/controllers/property/property_controller.php',
            type: 'POST',
            data: {
                operation: operation,
                hide_wizard: 'true',
                category_id: category_id,
                collection_id: $("#collection_id").val()}
        }).done(function (result) {
            $("#menu_object").hide();
            $("#container_socialdb").hide('slow');
            $("#list").hide('slow');
            $("#loader_objects").hide();
            $("#form").html(result);
            $('#form').show('slow');
            //$('#single_category_property').html(result);
            //$('#single_modal_category_property').modal('show');
        });
    }

    /**
     * verificando se o item ainda esta publicado
     * @param {type} item_id
     * @returns {undefined}
     */
    function verifyPublishedItem(item_id) {
        $.ajax({
            url: $('#src').val() + '/controllers/object/objectsingle_controller.php',
            type: 'POST',
            data: {
                operation: 'verifyPublishedItem',
                collection_id: $("#collection_id").val(),
                item_id: item_id}
        }).done(function (result) {
            json = JSON.parse(result);
            console.log(json);
            if (json.is_removed) {
                showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('This item has been removed, redirecting to collection home page! ', 'tainacan') ?>', 'error');
                window.location = json.url;
            }
        });
    }

    /**
     *  Funcao que verifica se uma acao pode ser executada 
     * @param {type} value
     * @param {type} facet_id
     * @returns ajax promisse
     */
    function verifyAction(collection_id, action, object_id) {
        return $.ajax({
            url: $('#src').val() + '/controllers/home/home_controller.php',
            type: 'POST',
            data: {
                operation: 'verifyAction',
                action: action,
                collection_id: collection_id,
                object_id: object_id}
        });
    }


    function bindContextMenuSingle(span, dynatree_id) {
        // Add context menu to this node:
        var menu;
        if (dynatree_id) {
            menu = 'myMenuNoList';
        } else {
            menu = 'myMenuSingle';
        }
        $(span).contextMenu({menu: menu, trigger: 'hover'}, function (action, el, pos) {
            // The event was bound to the <span> tag, but the node object
            // is stored in the parent <li> tag
            var node = $.ui.dynatree.getNode(el);
            switch (action) {
                case "see":
                    var src = $('#src').val();
                    // Close menu on click
                    show_modal_main();
                    // Close menu on click
                    var promisse = get_url_category(node.data.key);
                    promisse.done(function (result) {
                        elem = jQuery.parseJSON(result);
                        var n = node.data.key.toString().indexOf("_");
                        if (node.data.key.indexOf('_tag') >= 0) {
                            showPageTags(elem.slug, src);
                            node.deactivate();
                        } else if (n < 0 || node.data.key.indexOf('_facet_category') >= 0) {
                            showPageCategories(elem.slug, src);
                            node.deactivate();
                        }
                    });
                    break;
                case "add":
                    var promisse = verifyAction($('#collection_id').val(), 'socialdb_collection_permission_create_category', 0);
                    promisse.done(function (result) {
                        json = JSON.parse(result);
                        if (!json.isAllowed) {
                            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('This action was configured as "NOT ALLOWED" by moderators!', 'tainacan') ?>', 'info');
                        } else {
                            $("#category_single_parent_name").val(node.data.title);
                            $("#category_single_parent_id").val(node.data.key);
                            $('#modalAddCategoria').modal('show');
                            $('.dropdown-toggle').dropdown();
                            //ativando para um dynatree especifico
                            if (dynatree_id) {
                                $("#category_single_add_dynatree_id").val(dynatree_id);
                            }
                        }
                    });
                    break;
                case "edit":
                    var promisse = verifyAction($('#collection_id').val(), 'socialdb_collection_permission_edit_category', 0);
                    promisse.done(function (result) {
                        json = JSON.parse(result);
                        if (!json.isAllowed) {
                            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('This action was configured as "NOT ALLOWED" by moderators!', 'tainacan') ?>', 'info');
                        } else {

                            //$("#category_single_parent_name_edit").val(node.data.title);
                            //$("#category_single_parent_id_edit").val(node.data.key);
                            $("#category_single_edit_name").val(node.data.title);
                            $("#socialdb_event_previous_name").val(node.data.title);
                            $("#category_edit_description").val('');
                            $("#category_single_edit_id").val(node.data.key);
                            //ativando para um dynatree especifico
                            if (dynatree_id) {
                                $("#category_single_edit_dynatree_id").val(dynatree_id);
                            }
                            $('#modalEditCategoria').modal('show');
                            //                $("#operation").val('update');
                            $('.dropdown-toggle').dropdown();
                            $.ajax({
                                type: "POST",
                                url: $('#src').val() + "/controllers/category/category_controller.php",
                                data: {category_id: node.data.key, operation: 'get_parent'}
                            }).done(function (result) {
                                elem = jQuery.parseJSON(result);
                                $("#category_single_edit_name").val(elem.child_name);
                                if (elem.name) {
                                    $("#category_single_parent_name_edit").val(elem.name);
                                    $("#category_single_parent_id_edit").val(elem.term_id);
                                    $("#socialdb_event_previous_parent").val(elem.term_id);
                                } else {
                                    $("#category_single_parent_name_edit").val('Categoria raiz');
                                }
                                //$("#show_category_property").show();
                                $('.dropdown-toggle').dropdown();
                            });
                            // metas
                            $.ajax({
                                type: "POST",
                                url: $('#src').val() + "/controllers/category/category_controller.php",
                                data: {category_id: node.data.key, operation: 'get_metas'}
                            }).done(function (result) {
                                elem = jQuery.parseJSON(result);
                                $('#category_synonyms').val('');
                                if (elem.term.description) {
                                    $("#category_edit_description").val(elem.term.description);
                                }
                                //sinonimos
                                clear_synonyms_tree();
                                if (elem.socialdb_term_synonyms && elem.socialdb_term_synonyms.length > 0) {
                                    $('#category_synonyms').val(elem.socialdb_term_synonyms.join(','));
                                    $("#dynatree_synonyms").dynatree("getRoot").visit(function (node) {
                                        var str = node.data.key.replace("_tag", "");
                                        if (elem.socialdb_term_synonyms.indexOf(str) >= 0) {
                                            node.select(true);
                                        }
                                    });
                                }
<?php do_action('javascript_metas_category') ?>
                                //if (elem.socialdb_category_permission) {
                                //  $("#category_permission").val(elem.socialdb_category_permission);
                                //}
//                                if (elem.socialdb_category_moderators) {
//                                    $("#chosen-selected2-user").html('');
//                                    $.each(elem.socialdb_category_moderators, function (idx, user) {
//                                        if (user && user !== false) {
//                                            $("#chosen-selected2-user").append("<option class='selected' value='" + user.id + "' selected='selected' >" + user.name + "</option>");
//                                        }
//                                    });
//                                }
                                //set_fields_archive_mode(elem);
                                $('.dropdown-toggle').dropdown();
                            });
                        }
                    });
                    break;
                case "delete":
                    var promisse = verifyAction($('#collection_id').val(), 'socialdb_collection_permission_delete_category', 0);
                    promisse.done(function (result) {
                        json = JSON.parse(result);
                        if (!json.isAllowed) {
                            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('This action was configured as "NOT ALLOWED" by moderators!', 'tainacan') ?>', 'info');
                        } else {
                            $("#category_single_delete_id").val(node.data.key);
                            $("#delete_category_single_name").text(node.data.title);
                            //ativando para um dynatree especifico
                            if (dynatree_id) {
                                $("#category_single_delete_dynatree_id").val(dynatree_id);
                            }
                            $('#modalExcluirCategoria').modal('show');
                            $('.dropdown-toggle').dropdown();
                        }
                    });
                    break;
                case 'metadata':
                    list_category_property_single(node.data.key);
                    break;
                default:
                    alert("Todo: appply action '" + action + "' to node " + node);
            }
        });
    }

    /**
     * 
     funcao que mostra o menu de acoes do dynatree para tags
     * @param {type} value
     * @param {type} facet_id
     * @returns {undefined}     */
    function bindContextMenuSingleTag(span) {
        // Add context menu to this node:
        $(span).contextMenu({menu: "myMenuSingleTag"}, function (action, el, pos) {
            // The event was bound to the <span> tag, but the node object
            // is stored in the parent <li> tag
            var node = $.ui.dynatree.getNode(el);
            console.log(node.data.key);
            switch (action) {
                case "see":
                    var src = $('#src').val();
                    // Close menu on click
                    show_modal_main();
                    // Close menu on click
                    var promisse = get_url_category(node.data.key);
                    promisse.done(function (result) {
                        elem = jQuery.parseJSON(result);
                        hide_modal_main();
                        var n = node.data.key.toString().indexOf("_");
                        if (node.data.key.indexOf('_tag') >= 0) {
                            showPageTags(elem.slug, src);
                            node.deactivate();
                        } else if (n < 0 || node.data.key.indexOf('_facet_category') >= 0) {
                            showPageCategories(elem.slug, src);
                            node.deactivate();
                        }
                    });
                    break;
                case "add":
                    var promisse = verifyAction($('#collection_id').val(), 'socialdb_collection_permission_create_tags', 0);
                    promisse.done(function (result) {
                        json = JSON.parse(result);
                        if (!json.isAllowed) {
                            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('This action was configured as "NOT ALLOWED" by moderators!', 'tainacan') ?>', 'info');
                        } else {
                            $('#modalAdicionarTag').modal('show');
                            $('.dropdown-toggle').dropdown();
                        }
                    });
                    break;
                case "edit":
                    var promisse = verifyAction($('#collection_id').val(), 'socialdb_collection_permission_edit_tags', 0);
                    promisse.done(function (result) {
                        json = JSON.parse(result);
                        if (!json.isAllowed) {
                            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('This action was configured as "NOT ALLOWED" by moderators!', 'tainacan') ?>', 'info');
                        } else {
                            $("#tag_single_edit_name").val(node.data.title);
                            $("#tag_single_edit_id").val(node.data.key);
                            $("#tag_edit_description").val('');
                            $('#modalEditTag').modal('show');
                            $("#operation").val('update');
                            $.ajax({
                                type: "POST",
                                url: $('#src').val() + "/controllers/tag/tag_controller.php",
                                data: {tag_id: node.data.key, operation: 'get_tag'}
                            }).done(function (result) {
                                elem = jQuery.parseJSON(result);
                                if (elem.term.description) {
                                    $("#tag_edit_description").val(elem.term.description);
                                }
                                //sinonimos
                                clear_synonyms_tree();
                                if (elem.socialdb_term_synonyms && elem.socialdb_term_synonyms.length > 0) {
                                    $('#tag_synonyms').val(elem.socialdb_term_synonyms.join(','));
                                    $("#dynatree_synonyms_tag").dynatree("getRoot").visit(function (node) {
                                        var str = node.data.key.replace("_tag", "");
                                        console.log(str, elem.socialdb_term_synonyms.indexOf(str) >= 0);
                                        if (elem.socialdb_term_synonyms.indexOf(str) >= 0) {
                                            node.select(true);
                                        }
                                    });
                                }
                                $('.dropdown-toggle').dropdown();
                            });
                            $('.dropdown-toggle').dropdown();
                        }
                    });
                    break;
                case "delete":
                    var promisse = verifyAction($('#collection_id').val(), 'socialdb_collection_permission_delete_tags', 0);
                    promisse.done(function (result) {
                        json = JSON.parse(result);
                        if (!json.isAllowed) {
                            showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('This action was configured as "NOT ALLOWED" by moderators!', 'tainacan') ?>', 'info');
                        } else {
                            $("#delete_tag_single_name").text(node.data.title);
                            $("#tag_single_delete_id").val(node.data.key);
                            $('#modalExcluirTag').modal('show');
                            $('.dropdown-toggle').dropdown();
                        }
                    });
                    break;
                default:
                    alert("Todo: appply action '" + action + "' to node " + node);
            }
        });
    }

    /*
     *
     * TODO: refactor code
     * */
    //wp query functions #######################################################
    // faz as filtragens de links externos e retorna para a pagina de listagem
    function wpquery_link_filter(value, facet_id) {
        $('#display_view_main_page').show();
        $('#collection_post').show();
        $('#configuration').hide().html('');
        $('#main_part').show('slow');
        var stateObj = {foo: "bar"};
        history.replaceState(stateObj, "page 2", '?');
        $('#list').hide();
        $('#loader_objects').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_link', facet_id: facet_id, wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            setMenuContainerHeight();
        });
    }
    // faz as filtragens de links externos e retorna para a pagina de listagem PARA termos
    function wpquery_term_filter(value, facet_id) {
        $('#display_view_main_page').show();
        $('#collection_post').show();
        $('#configuration').hide();
        $('#configuration').html('');
        $('#main_part').show('slow');
        var stateObj = {foo: "bar"};
        history.replaceState(stateObj, "page 2", '?');
        $('#list').hide();
        $('#loader_objects').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_radio', facet_id: facet_id, wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            var curr_viewMode = $("#collection_single_ordenation").attr('data-viewMode');
            if (curr_viewMode) {
                changeViewMode(curr_viewMode);
            }
            setMenuContainerHeight();
        });
    }

    function wpquery_filter_by_facet(value, facet_id, operation) {
        $("#list").hide();
        $('#loader_objects').show();
        var facet_id = facet_id || "";
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: operation, value: value, facet_id: facet_id, wp_query_args: $('#wp_query_args').val(), collection_id: $('#collection_id').val()}
        }).done(function (result) {
            var elem = $.parseJSON(result);
            $('#loader_objects').hide();
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            // $('.clear-top-search').fadeIn();
            $('#list').html(elem.page).show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            var curr_viewMode = $("#collection_single_ordenation").attr('data-viewMode');
            if (curr_viewMode) {
                changeViewMode(curr_viewMode);
            }

            setMenuContainerHeight();
        });
    }

    function wpquery_select(seletor, facet_id) {
        $('#list').hide();
        $('#loader_objects').show();
        var value = $(seletor).val();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_select', facet_id: facet_id, wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            setMenuContainerHeight();
             var curr_viewMode = $("#collection_single_ordenation").attr('data-viewMode');
            if (curr_viewMode) {
                changeViewMode(curr_viewMode);
            }
        });
    }

    function wpquery_checkbox(seletor, facet_id) {
        $('#list').hide();
        $('#loader_objects').show();
        var value = $('input:checkbox:checked#' + seletor).map(function () {
            return this.value;
        }).get().join(",");
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_checkbox', facet_id: facet_id, wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            setMenuContainerHeight();
             var curr_viewMode = $("#collection_single_ordenation").attr('data-viewMode');
            if (curr_viewMode) {
                changeViewMode(curr_viewMode);
            }
        });
    }

    function wpquery_multipleselect(facet_id, seletor) {
        $('#list').hide();
        var value = '';
        $('#loader_objects').show();
        if (!$('#' + seletor)) {
            value = '';
        } else {
            if ($('#' + seletor).val()) {
                value = $('#' + seletor).val().join(",");
            } else {
                value = '';
            }
        }
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_multipleselect', facet_id: facet_id, wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            setMenuContainerHeight();
        });
    }

    function wpquery_range(facet_id, facet_type, value1, value2) {
        $('#list').hide();
        $('#loader_objects').show();
        var value = value1 + ',' + value2;
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_range', facet_id: facet_id, facet_type: facet_type, wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            var curr_viewMode = $("#collection_single_ordenation").attr('data-viewMode');
            if (curr_viewMode) {
                changeViewMode(curr_viewMode);
            }
            setMenuContainerHeight();
        });
    }

    function wpquery_fromto(facet_id, facet_type) {

        if ($('#facet_' + facet_id + '_1').val() !== '' && $('#facet_' + facet_id + '_2').val() !== '') {
            $('#list').hide();
            $('#loader_objects').show();
            var value = $('#facet_' + facet_id + '_1').val() + ',' + $('#facet_' + facet_id + '_2').val();
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
                data: {operation: 'wpquery_fromto', facet_id: facet_id, facet_type: facet_type, wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                $('#loader_objects').hide();
                $('#list').html(elem.page);
                $('#wp_query_args').val(elem.args);
                set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
                show_filters($('#collection_id').val(), elem.args);
                $('#list').show();
                if (elem.empty_collection) {
                    $('#collection_empty').show();
                    $('#items_not_found').hide();
                }
                var curr_viewMode = $("#collection_single_ordenation").attr('data-viewMode');
                if (curr_viewMode) {
                    changeViewMode(curr_viewMode);
                }
                setMenuContainerHeight();
            });
        }
    }

    function wpquery_ordenation(value, temp_list_mode) {
        $('#list').hide();
        $('#loader_objects').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_ordenation', wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);

            $('#loader_objects').hide();
            $('#wp_query_args').val(elem.args);
            $('#list').html(elem.page).show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }

            if (temp_list_mode) {
                changeViewMode(temp_list_mode);
            }

            setMenuContainerHeight();
        });
    }

    function wpquery_orderBy(value, temp_list_mode) {
        $('#list').hide();
        $('#loader_objects').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_orderby', wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#wp_query_args').val(elem.args);
            $('#list').html(elem.page).show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }

            if (temp_list_mode) {
                changeViewMode(temp_list_mode);
            }

            setMenuContainerHeight();
        });
    }

    function wpquery_keyword(value) {
        $('#list').hide();
        $('#loader_objects').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_keyword', wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            $('#list').show();
            // $('.clear-top-search').fadeIn();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            if(!elem.has_post && $("#collection_id").val() ===  $("#collection_root_id").val()){
                search_items_query = $('#wp_query_args').val();
                search_collections_query = $('#wp_query_args').val();
               $('#click_ad_search_items').trigger('click');
            }else{
               search_collections_query = $('#wp_query_args').val();
            }
            setMenuContainerHeight();
            var curr_viewMode = $("#collection_single_ordenation").attr('data-viewMode');
            if (curr_viewMode) {
                changeViewMode(curr_viewMode);
            }
        });
    }

    function wpquery_page(value, collection_viewMode, is_trash) {
        $('#list').hide();
        $('#loader_objects').show();
        var src =  "'"+$('#src').val()+"'";
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: { operation: 'wpquery_page', wp_query_args: $('#wp_query_args').val(), value: value,
                posts_per_page: $('.col-items-per-page').val(), collection_id: $('#collection_id').val(), is_trash: is_trash}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            if (collection_viewMode) {
                changeViewMode(collection_viewMode);
            }

            if(is_trash) {
                <?php if(!apply_filters('tainacan_show_restore_options', get_the_ID())): ?>
                        $('#table-view tr').each(function(num, item) {
                            var curr_id = $(item).find('td').first().find('a').attr('data-id');
                            $(item).find('td').last().html('<li> <a onclick="showSingleObject(' + curr_id + ','+src+','+is_trash+')"> <span class="glyphicon glyphicon-eye-open"></span> </a></li>');     
                        });
                         $('li.item-redesocial').hide();
                         $('ul.item-menu-container').hide();
                        $('.item-colecao').each(function(num, item) {
                              var curr_id = $(this).find('.post_id').last().val();
                              $(this).find('.item-funcs').last().append('<li style="float: right; margin-left: 10px;"> <a onclick="showSingleObject(' + curr_id + ','+src+','+is_trash+')"> <span class="glyphicon glyphicon-eye-open"></span> </a></li>');
                        });
                 <?php else:  ?>     
                        $('li.item-redesocial').hide();
                        $('ul.item-menu-container').hide();
                        $('#table-view tr').each(function(num, item) {
                            var curr_id = $(item).find('td').first().find('a').attr('data-id');  
                             $(item).find('td').last().html('<li> <a onclick="delete_permanently_object(\'Deletar Item\', \'Deletar este item permanentemente?\', ' + curr_id + ')" class="remove"> <span class="glyphicon glyphicon-trash"></span> </a> </li><li> <a onclick="restore_object(' + curr_id + ')"> <span class="glyphicon glyphicon-retweet"></span> </a></li>');
                        });
                <?php endif; ?> 
            }

            setMenuContainerHeight();
        });
    }
    /**
     * filtra os itens pelo autor
     
     * @param {type} value
     * @param {type} collection_viewMode
     * @returns {undefined}     */
    function wpquery_author(value, collection_viewMode) {
        $('#list').hide();
        $('#loader_objects').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'wpquery_author', wp_query_args: $('#wp_query_args').val(), value: value, collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            show_filters($('#collection_id').val(), elem.args);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            if (collection_viewMode) {
                changeViewMode(collection_viewMode);
            }

            setMenuContainerHeight();
        });
    }
    
    function wpquery_menu_left(type){
        $('#list').hide();
        $('#loader_objects').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'filter', wp_query_args: $('#wp_query_args').val(), collection_id: $('#collection_id').val(), post_type: 'socialdb_object',author:type }
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            if (type && type == 'socialdb_collection') {
                search_collections_query = $('#wp_query_args').val();
            } else if (type && type == 'socialdb_object') {
                search_items_query = $('#wp_query_args').val();
            }
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            setMenuContainerHeight();
        });
    }

    function wpquery_filter(type) {
        if (!type) {
            type = '';
        } else if (type == 'socialdb_collection') {
            $('#wp_query_args').val(search_collections_query);
            $('#options-collections-search').show();
            $('#options-items-search').hide();
        } else if (type == 'socialdb_object') {
            $('#wp_query_args').val(search_items_query);
            $('#options-items-search').show();
            $('#options-collections-search').hide();
        }
        $('#list').hide();
        $('#loader_objects').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'filter', wp_query_args: $('#wp_query_args').val(), collection_id: $('#collection_id').val(), post_type: type}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            if (type && type == 'socialdb_collection') {
                if(!elem.has_post && $("#collection_id").val() ===  $("#collection_root_id").val()){
                    search_items_query = $('#wp_query_args').val();
                    search_collections_query = $('#wp_query_args').val();
                   $('#click_ad_search_items').trigger('click');
                }else{
                   search_collections_query = $('#wp_query_args').val();
                }
            } else if (type && type == 'socialdb_object') {
                search_items_query = $('#wp_query_args').val();
            }
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            $('#list').show();
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            setMenuContainerHeight();
        });
    }

    function wpquery_clean() {
        $('#list').hide();
        $('#loader_objects').show();
        search_collections_query = '';
        search_items_query = '';
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'clean', wp_query_args: $('#wp_query_args').val(), collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            //console.log(elem.listed_by_value);
            $('#collection_single_ordenation').val(elem.listed_by_value);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            $('#list').show();
            if ($("#dynatree")) {
                $("#dynatree").dynatree("getTree").reload();
            }
            setMenuContainerHeight();
        });
    }

    function wpquery_remove(index_array, type, value) {
        $('#list').hide();
        $('#loader_objects').show();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {
                index_array: index_array,
                type: type,
                value: value,
                operation: 'remove',
                wp_query_args: $('#wp_query_args').val(),
                collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            $('#loader_objects').hide();
            $('#list').html(elem.page);
            $('#wp_query_args').val(elem.args);
            set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1');
            show_filters($('#collection_id').val(), elem.args);
            if (elem.empty_collection) {
                $('#collection_empty').show();
                $('#items_not_found').hide();
            }
            var result_set = $('.search-resultset').find('a').length;
            if (result_set > 0) {
                $("button#clear").fadeIn();
            }
            $('#flag_dynatree_ajax').val('true');
            $('#list').show();
            setMenuContainerHeight();
        });
    }

    // funcao que captura a action on change no selectbox na pagina single.php
    function getOrder(value) {
        var curr_viewMode = $(value).attr('data-viewMode');
        var val = $(value).val();
        wpquery_ordenation(val, curr_viewMode);
        //list_all_objects(selKeys.join(", "), $("#collection_id").val(), $(value).val());
    }

    // funcao que captura a action on change no selectbox na pagina single.php
    function change_ordenation(order) {
        var curr_viewMode = $("#collection_single_ordenation").attr('data-viewMode');
        wpquery_orderBy(order, curr_viewMode);
    }

    function search_objects(e) {
        var search_for = $(e).val();
        wpquery_keyword(search_for);
    }

    function backToMainPage(reload_container, keep_search) {
        change_breadcrumbs_title('', ' ');

        if(keep_search && keep_search == true) {
        } else {
            wpquery_clean();
            if (!reload_container) {
                set_containers_class($('#collection_id').val());
            }
        }

        list_main_ordenation_filter();
        $('.modal-backdrop').hide();
        $('#main_part_collection').hide();
        $("#category_page").val('');
        $("#property_page").val('');
        $('#display_view_main_page').show();
        $('#collection_post').show();
        $('#configuration').hide().html('');
        $('#form').hide().html('');
        $('#create_button').show();
        $('#menu_object').show();
        $("#list").show();
        $("#container_socialdb").show('fast');
        $('#main_part').show('slow');
        reinit_synonyms_tree();
    }

    //apenas para a pagina de demonstracao do item
    function backToMainPageSingleItem() {
        change_breadcrumbs_title('', ' ');
        wpquery_filter();
        set_containers_class($('#collection_id').val());
        list_main_ordenation_filter();
        $('#display_view_main_page').show();
        $('#collection_post').show();
        $('#configuration').hide().html('');
        $('#main_part').show('slow');
        var stateObj = {foo: "bar"};
        backRoute($('#slug_collection').val());
        //set_containers_class($('#collection_id').val());
    }

    // volta a listagem e limpa as url
    function back_and_clean_url() {
        $("#category_page").val('');
        $("#property_page").val('');
        $('#form').hide();
        $('#create_button').show();
        $('#menu_object').show();
        $("#list").show();
        $("#container_socialdb").show('fast');
        var stateObj = {foo: "bar"};
        history.replaceState(stateObj, "page 2", '?');
    }

    function show_filters(collection_id, filters) {
        $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: {
                operation: 'show_filters',
                collection_id: collection_id,
                filters: filters
            }
        }).done(function (result) {
            $('#filters_collection').html(result);
            $('.remove-link-filters').show();
            var result_set = $('.search-resultset').find('a').length;
            if (result_set > 0 && !$('#propertiesRootAdvancedSearch').is(':visible')) {
                $("button#clear").fadeIn();
            } else {
                $("button#clear").fadeOut('fast');
            }
        });
    }

    //***************************************** BEGIN SOCIAL NETWORK IMPORT *********************************************//

//    function import_youtube_video_url() {
//        var youtube_video_url = $('#youtube_video_url').val().trim();
//        var collectionId = $('#collection_id').val();
//
//        if (youtube_video_url) {
//            $('#modalImportMain').modal('show');
//            var src = $('#src').val();
//
//            $.ajax({
//                url: src + '/controllers/social_network/youtube_controller.php',
//                type: 'POST',
//                data: {operation: 'import_video_url',
//                    video_url: youtube_video_url,
//                    collectionId: collectionId},
//                success: function (response) {
//                    $('#modalImportMain').modal('hide');
//                    if (response) {
//                        showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('Video imported successfully', 'tainacan'); ?>', 'success');
//                        set_containers_class(collectionId);
//                        wpquery_clean();
//                    } else {
//                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid URL or Video already inserted.', 'tainacan'); ?>', 'error');
//                    }
//                }
//            });
//            $('#youtube_video_url').val('');
//            $('#modalshowModalImportSocialNetwork').modal('hide');
//        } else {
//            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Youtube video url', 'tainacan'); ?>', 'error');
//        }
//    }

//    function import_youtube_channel() {
//        var inputIdentifierYoutube = $('#youtube_identifier_input').val().trim();
//        var inputPlaylistYoutube = $('#youtube_playlist_identifier_input').val().trim();
//        var collectionId = $('#collection_id').val();
//
//        if (inputIdentifierYoutube) {
//            $('#modalImportMain').modal('show');
//            var src = $('#src').val();
//
//            //ajax
//            $.ajax({
//                url: src + '/controllers/social_network/youtube_controller.php',
//                type: 'POST',
//                data: {operation: 'import_video_channel',
//                    identifier: inputIdentifierYoutube,
//                    playlist: inputPlaylistYoutube,
//                    collectionId: collectionId},
//                success: function (response) {
//                    $('#modalImportMain').modal('hide');
//                    var json = JSON.parse(response);
//                    if (json.length > 0) {
//                        showViewMultipleItemsSocialNetwork(json);
//                        //showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('OK', 'tainacan'); ?>', 'success');
//                        //wpquery_clean();
//                    }
//                    else {
//                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid Channel/Playlist or no videos to be imported', 'tainacan'); ?>', 'error');
//                    }
//                }
//            });
//            //end ajax
//
//            $('#youtube_identifier_input').val('');
//            $('#youtube_playlist_identifier_input').val('');
//            $('#modalshowModalImportSocialNetwork').modal('hide');
//        } else {
//            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Youtube channel identifier', 'tainacan'); ?>', 'error');
//        }
//    }

//    function import_flickr() {
//        var inputIdentifierFlickr = $('#flickr_identifier_input').val().trim();
//        var collectionId = $('#collection_id').val();
//
//        if (inputIdentifierFlickr) {
//            $('#modalImportMain').modal('show');
//            var src = $('#src').val();
//
//            $.ajax({
//                url: src + '/controllers/social_network/flickr_controller.php',
//                type: 'POST',
//                data: {operation: 'import_flickr_items',
//                    identifier: inputIdentifierFlickr,
//                    collectionId: collectionId},
//                success: function (response) {
//                    //se a gravação no banco foi realizado, a tabela é incrementada
//                    $('#modalImportMain').modal('hide');
//                    var json = JSON.parse(response);
//                    if (json.length > 0) {
//                        showViewMultipleItemsSocialNetwork(json);
//                        //showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('OK', 'tainacan'); ?>', 'success');
//                        //wpquery_clean();
//                    }
//                    else {
//                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid Flickr identifier or no items to be imported', 'tainacan'); ?>', 'error');
//                    }
//                }
//            });
//            $('#flickr_identifier_input').val('');
//            $('#modalshowModalImportSocialNetwork').modal('hide');
//        }
//        else {
//            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Flickr identifier', 'tainacan'); ?>', 'error');
//            $('#flickr_identifier_input').val('');
//        }
//    }

//    function import_instagram() {
//        var inputIdentifierInstagram = $('#instagram_identifier_input').val().trim();
//        var collection_id = $('#collection_id').val();
//
//        if (inputIdentifierInstagram) {
//            $('#modalImportMain').modal('show');
//            var src = $('#src').val();
//
//            window.location = src + "/controllers/social_network/instagram_controller.php?collection_id=" + collection_id + "&operation=getPhotosInstagram&identifier=" + inputIdentifierInstagram;
//
//            $('#instagram_identifier_input').val('');
//            $('#modalshowModalImportSocialNetwork').modal('hide');
//        }
//        else {
//            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Instagram identifier', 'tainacan'); ?>', 'error');
//        }
//    }

//    function import_vimeo() {
//        var inputIdentifierVimeo = $('#vimeo_identifier_input').val().trim();
//        var collectionId = $('#collection_id').val();
//
//        if (inputIdentifierVimeo) {
//            $('#modalImportMain').modal('show');
//            var src = $('#src').val();
//
//            $.ajax({
//                url: src + '/controllers/social_network/vimeo_controller.php',
//                type: 'POST',
//                data: {operation: 'import_vimeo_items',
//                    identifier: inputIdentifierVimeo,
//                    import_type: $('input[name="optradio_vimeo"]:checked').val(),
//                    collectionId: collectionId},
//                success: function (response) {
//                    //se a gravação no banco foi realizado, a tabela é incrementada
//                    $('#modalImportMain').modal('hide');
//                    var json = JSON.parse(response);
//                    if (json.length > 0) {
//                        showViewMultipleItemsSocialNetwork(json);
//                        //showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('OK', 'tainacan'); ?>', 'success');
//                        //wpquery_clean();
//                    }
//                    else {
//                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid Vimeo identifier or no items to be imported', 'tainacan'); ?>', 'error');
//                    }
//                }
//            });
//            $('#vimeo_identifier_input').val('');
//            $('#modalshowModalImportSocialNetwork').modal('hide');
//        }
//        else {
//            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Vimeo identifier', 'tainacan'); ?>', 'error');
//            $('#vimeo_identifier_input').val('');
//        }
//    }

    //--------------------------- TELA DE IMPORTACAO DE MULTIPLO ARQUIVOS --------------------------
    function showViewMultipleItemsSocialNetwork(imported_ids) {
        $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {
                operation: 'showViewMultipleItemsSocialNetwork',
                items_id: imported_ids,
                collection_id: $("#collection_id").val()}
        }).done(function (result) {
            $('#main_part').hide();
            $('#display_view_main_page').hide();
            $('#loader_collections').hide();
            //$('#collection_post').hide();
            $('#configuration').html(result);
            $('#configuration').slideDown();
        });
    }

    //***************************************** END SOCIAL NETWORK IMPORT *********************************************//


    //***************************************** BEGIN IMPORT ALL *********************************************//

    function importAll_verify() {
        var inputImportAll = $('#item_url_import_all').val().trim();

        if (inputImportAll) {
            var youtube_url = validateYouTubeUrl();
            if (youtube_url) {
                // É uma URL de um vídeo do youtube. Executar a importação do vídeo.
                // console.log(youtube_url);
                import_youtube_video_url();
            } else {
                var youtube_channel_url = validateYouTubeChannelUrl();
                if (youtube_channel_url) {
                    // É uma URL de um canal do youtube. Executar a importação dos vídeos de canal.
                    var res = inputImportAll.split(youtube_channel_url[4]);
                    // console.log(res[1]);
                    import_youtube_channel(res[1]);
                }
                else {
                    var youtube_playlist_url = validateYouTubePlaylistUrl();
                    if (youtube_playlist_url) {
                        // É uma URL de uma playlist do youtube. Executar a importação dos vídeos da playlist.
                        // console.log(youtube_playlist_url);
                        import_youtube_playlist(youtube_playlist_url);
                    }
                    else {
                        var instagram_url = validateInstagramUrl();
                        if (instagram_url) {
                            // É uma URL do instagram. Executar a importação dos imagens e vídeos do usuario.
                            // console.log(instagram_url);
                            import_instagram(instagram_url);
                        } else {
                            var vimeo_url = validateVimeoUrl();
                            if (vimeo_url) {
                                // É uma URL do vimeo. Executar a importação dos vídeos.
                                vimeo_url = vimeo_url.split("/");
                                if (vimeo_url[3].localeCompare('channels') === 0) {
                                    // console.log('Canal: ' + vimeo_url[4]);
                                    import_vimeo('channels', vimeo_url[4]);
                                } else {
                                    // console.log('Usuario: ' + vimeo_url[3]);
                                    import_vimeo('users', vimeo_url[3]);
                                }
                            }
                            else {
                                var flickr_url = validateFlickrUrl();
                                if (flickr_url) {
                                    // É uma URL do Flickr. Executar a importação dos itens do usuário.
                                    // console.log(flickr_url);
                                    import_flickr(flickr_url);
                                }
                                else {
                                    var facebook_url = validateFacebookUrl();
                                    if (facebook_url) {
                                        // É uma URL do Facebook. Executar a importação dos itens do usuário.
                                        // console.log(facebook_url);
                                    }
                                    else {
                                        var any_file_type = validateAnyFile();
                                        if (any_file_type) {
                                            
                                            // É uma URL de um arquivo. Executar a importação deste arquivo.
                                            show_modal_main();
                                            //showFormCreateURLFile($('#item_url_import_all').val(), any_file_type);
                                            import_files_url($('#item_url_import_all').val(), any_file_type);
                                            $('#item_url_import_all').val('');
                                            $("#files_import_icon").addClass("grayscale");
                                            $('#modalshowModalImportAll').modal('hide');
                                        } else {
                                            var any_url = validateAnyUrl();
                                            if (any_url) {
                                                var split_url = $('#item_url_import_all').val().replace('http://', '').replace('https://', '').split('/');
                                                var index = split_url.indexOf('handle');
                                                var article = split_url.indexOf('article');
                                                var view = split_url.indexOf('view');
                                                if ($('#extract_metadata').is(':checked') && index >= 0) {
                                                    extract_metadata($('#item_url_import_all').val());
                                                } else if ($('#extract_metadata').is(':checked') && article >= 0 && view >= 0) {
                                                    extract_metadata($('#item_url_import_all').val());
                                                } else if ($('#extract_metadata').is(':checked')) {
                                                    extract_metatags($('#item_url_import_all').val());
                                                } else {
                                                    // É uma URL regular. Executar a importação através do Embed.ly.
                                                    show_modal_main();
                                                    // showFormCreateURL($('#item_url_import_all').val());
                                                    import_text($('#item_url_import_all').val());
                                                    $('#item_url_import_all').val('');
                                                    $("#sites_import_icon").addClass("grayscale");
                                                    $('#modalshowModalImportAll').modal('hide');
                                                    // console.log('URL Regular. Enviar pro Embed.ly.');
                                                }
                                            } else {
                                                showAlertGeneral("<?php _e('Alert', 'tainacan'); ?>", "<?php _e('Please, insert a valid URL', 'tainacan'); ?>", "error");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }


        } else {
            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform something', 'tainacan'); ?>', 'error');
            $('#item_url_import_all').val('');
        }
    }

    function validateYouTubeUrl()
    {
        var url = $('#item_url_import_all').val();
        if (url != undefined || url != '') {
            var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
            var match = url.match(regExp);
            if (match && match[2].length == 11) {
                // Do anything for being valid
                // if need to change the url to embed url then use below line
                //$('#ytplayerSide').attr('src', 'https://www.youtube.com/embed/' + match[2] + '?autoplay=0');
                return match;
            }
            else {
                // Do anything for not being valid
                return false;
            }
        }
    }

    function validateYouTubeChannelUrl()
    {
        var url = $('#item_url_import_all').val();
        if (url != undefined || url != '') {
            var regExp = /((http|https):\/\/|)(www\.)?youtube\.com\/(channel\/|user\/)[a-zA-Z0-9]{1,}/;
            var match = url.match(regExp);
            if (match) {
                return match;
            }
            else {
                // Do anything for not being valid
                return false;
            }
        }
    }

    function validateYouTubePlaylistUrl()
    {
        var url = $('#item_url_import_all').val();
        if (url != undefined || url != '') {
            var regExp = /(?:(?:http|https):\/\/)?(?:www.)?(?:youtube.com|youtu.be)\/([A-Za-z0-9-_]+)/im;
            var match_youtube = url.match(regExp);
            if (match_youtube) {
                var reg = new RegExp("[&?]list=([a-z0-9_-]+)", "i");
                var match = reg.exec(url);
                
                if (match) {
                    return match[1];
                }
                else {
                    // Do anything for not being valid
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    function validateInstagramUrl()
    {
        var url = $('#item_url_import_all').val();
        if (url != undefined || url != '') {
            var regExp = /(?:(?:http|https):\/\/)?(?:www.)?(?:instagram.com|instagr.am)\/([A-Za-z0-9-_.]+)/im;
            var match = url.match(regExp);
            if (match) {
                return match[1];
            }
            else {
                // Do anything for not being valid
                return false;
            }
        }
    }

    function validateVimeoUrl()
    {
        var url = $('#item_url_import_all').val();
        if (url != undefined || url != '') {
            var regExp = /(?:(?:http|https):\/\/)?(?:www.)?(?:vimeo.com)\/([A-Za-z0-9-_]+)/im;
            var match = url.match(regExp);
            if (match) {
                return url;
            }
            else {
                // Do anything for not being valid
                return false;
            }
        }
    }

    function validateFlickrUrl()
    {
        var url = $('#item_url_import_all').val();
        if (url != undefined || url != '') {
            var regExp = /((http|https):\/\/|)(www\.)?flickr\.com\/(photos\/)[a-zA-Z0-9]{1,}/;
            var match = url.match(regExp);
            if (match) {
                var result = url.split('/');
                if (typeof result[6] != 'undefined' && result[6] != '' && (result[5] == 'albums' || result[5] == 'sets')) {
                    //console.log(result[6]);
                    return 'albums/' + result[4] + '/' + result[6];
                } else if (typeof result[5] != 'undefined' && result[5] != '') {
                    //console.log(result[5]);
                    return 'singleitem/' + result[4] + '/' + result[5];
                } else {
                    return 'all/' + result[4];
                }
            }
            else {
                // Do anything for not being valid
                return false;
            }
        }
    }

    function validateFacebookUrl()
    {
        var url = $('#item_url_import_all').val();
        if (url != undefined || url != '') {
            var regExp = /(?:(?:http|https):\/\/)?(?:www.)?(?:facebook.com)/im;
            var match = url.match(regExp);
            if (match) {
                return match;
            }
            else {
                // Do anything for not being valid
                return false;
            }
        }
    }

    function validateAnyFile() {
        var url = $('#item_url_import_all').val();
        if (url != undefined || url != '') {
            var regExp = /(?:([^:/?#]+):)?(?:\/\/([^/?#]*))?([^?#]*\.(?:jpg|jpeg|bmp|tiff|gif|png|pdf|mp4|avi|flv|mkv|mp3))(?:\?([^#]*))?(?:#(.*))?/i;
            var match = url.match(regExp);
            if (match && validateAnyUrl()) {
                var regExp_image = /(?:([^:/?#]+):)?(?:\/\/([^/?#]*))?([^?#]*\.(?:jpg|jpeg|bmp|tiff|gif|png))(?:\?([^#]*))?(?:#(.*))?/i;
                var match_image = url.match(regExp_image);
                if (match_image) {
                    return 'image';
                } else {
                    var regExp_video = /(?:([^:/?#]+):)?(?:\/\/([^/?#]*))?([^?#]*\.(?:mp4|avi|flv|mkv))(?:\?([^#]*))?(?:#(.*))?/i;
                    var match_video = url.match(regExp_video);
                    if (match_video) {
                        return 'video';
                    } else {
                        var regExp_pdf = /(?:([^:/?#]+):)?(?:\/\/([^/?#]*))?([^?#]*\.(?:pdf))(?:\?([^#]*))?(?:#(.*))?/i;
                        var match_pdf = url.match(regExp_pdf);
                        if (match_pdf) {
                            return 'pdf';
                        } else {
                            var regExp_audio = /(?:([^:/?#]+):)?(?:\/\/([^/?#]*))?([^?#]*\.(?:mp3))(?:\?([^#]*))?(?:#(.*))?/i;
                            var match_audio = url.match(regExp_audio);
                            if (match_audio) {
                                return 'audio';
                            } else {
                                return 'other';
                            }
                        }
                    }
                }
            }
            else {
                // Do anything for not being valid
                return false;
            }
        }
    }

    function validateAnyUrl()
    {
        var url = $('#item_url_import_all').val();
        if (url != undefined || url != '') {
            var regExp = /(^|\s)((https?:\/\/)?[\w-]+(\.[\w-]+)+\.?(:\d+)?(\/\S*)?)/gi;
            var match = url.match(regExp);
            if (match) {
                return match;
            }
            else {
                // Do anything for not being valid
                return false;
            }
        }
    }

    function verify_import_type() {
        var url = $('#item_url_import_all').val().trim();
        if (url != undefined && url != '') {
            var youtube_url = validateYouTubeUrl();
            if (youtube_url) {
                // É uma URL de um vídeo do youtube.
                $("#btn_import_fb").css('display', 'none');
                $("#btn_import_allrest").css('display', 'block');
                $("#facebook_import_icon").addClass("grayscale");
                $("#flickr_import_icon").addClass("grayscale");
                $("#youtube_import_icon").removeClass("grayscale");
                $("#vimeo_import_icon").addClass("grayscale");
                $("#instagram_import_icon").addClass("grayscale");
                $("#files_import_icon").addClass("grayscale");
                $("#sites_import_icon").addClass("grayscale");
            } else {
                var youtube_channel_url = validateYouTubeChannelUrl();
                if (youtube_channel_url) {
                    // É uma URL de um canal do youtube.
                    $("#btn_import_fb").css('display', 'none');
                    $("#btn_import_allrest").css('display', 'block');
                    $("#facebook_import_icon").addClass("grayscale");
                    $("#flickr_import_icon").addClass("grayscale");
                    $("#youtube_import_icon").removeClass("grayscale");
                    $("#vimeo_import_icon").addClass("grayscale");
                    $("#instagram_import_icon").addClass("grayscale");
                    $("#files_import_icon").addClass("grayscale");
                    $("#sites_import_icon").addClass("grayscale");
                }
                else {
                    var youtube_playlist_url = validateYouTubePlaylistUrl();
                    if (youtube_playlist_url) {
                        // É uma URL de uma playlist do youtube.
                        $("#btn_import_fb").css('display', 'none');
                        $("#btn_import_allrest").css('display', 'block');
                        $("#facebook_import_icon").addClass("grayscale");
                        $("#flickr_import_icon").addClass("grayscale");
                        $("#youtube_import_icon").removeClass("grayscale");
                        $("#vimeo_import_icon").addClass("grayscale");
                        $("#instagram_import_icon").addClass("grayscale");
                        $("#files_import_icon").addClass("grayscale");
                        $("#sites_import_icon").addClass("grayscale");
                    }
                    else {
                        var instagram_url = validateInstagramUrl();
                        if (instagram_url) {
                            // É uma URL do instagram.
                            $("#btn_import_fb").css('display', 'none');
                            $("#btn_import_allrest").css('display', 'block');
                            $("#facebook_import_icon").addClass("grayscale");
                            $("#flickr_import_icon").addClass("grayscale");
                            $("#youtube_import_icon").addClass("grayscale");
                            $("#vimeo_import_icon").addClass("grayscale");
                            $("#instagram_import_icon").removeClass("grayscale");
                            $("#files_import_icon").addClass("grayscale");
                            $("#sites_import_icon").addClass("grayscale");
                        } else {
                            var vimeo_url = validateVimeoUrl();
                            if (vimeo_url) {
                                // É uma URL do vimeo.
                                $("#btn_import_fb").css('display', 'none');
                                $("#btn_import_allrest").css('display', 'block');
                                $("#facebook_import_icon").addClass("grayscale");
                                $("#flickr_import_icon").addClass("grayscale");
                                $("#youtube_import_icon").addClass("grayscale");
                                $("#vimeo_import_icon").removeClass("grayscale");
                                $("#instagram_import_icon").addClass("grayscale");
                                $("#files_import_icon").addClass("grayscale");
                                $("#sites_import_icon").addClass("grayscale");
                            }
                            else {
                                var flickr_url = validateFlickrUrl();
                                if (flickr_url) {
                                    // É uma URL do Flickr.
                                    $("#btn_import_fb").css('display', 'none');
                                    $("#btn_import_allrest").css('display', 'block');
                                    $("#facebook_import_icon").addClass("grayscale");
                                    $("#flickr_import_icon").removeClass("grayscale");
                                    $("#youtube_import_icon").addClass("grayscale");
                                    $("#vimeo_import_icon").addClass("grayscale");
                                    $("#instagram_import_icon").addClass("grayscale");
                                    $("#files_import_icon").addClass("grayscale");
                                    $("#sites_import_icon").addClass("grayscale");
                                }
                                else {
                                    var facebook_url = validateFacebookUrl();
                                    if (facebook_url) {
                                        $("#btn_import_fb").css('display', 'block');
                                        $("#btn_import_allrest").css('display', 'none');
                                        $("#facebook_import_icon").removeClass("grayscale");
                                        $("#flickr_import_icon").addClass("grayscale");
                                        $("#youtube_import_icon").addClass("grayscale");
                                        $("#vimeo_import_icon").addClass("grayscale");
                                        $("#instagram_import_icon").addClass("grayscale");
                                        $("#files_import_icon").addClass("grayscale");
                                        $("#sites_import_icon").addClass("grayscale");
                                    } else {
                                        var any_file_url = validateAnyFile();
                                        if (any_file_url) {
                                            $("#btn_import_fb").css('display', 'none');
                                            $("#btn_import_allrest").css('display', 'block');
                                            $("#facebook_import_icon").addClass("grayscale");
                                            $("#flickr_import_icon").addClass("grayscale");
                                            $("#youtube_import_icon").addClass("grayscale");
                                            $("#vimeo_import_icon").addClass("grayscale");
                                            $("#instagram_import_icon").addClass("grayscale");
                                            $("#files_import_icon").removeClass("grayscale");
                                            $("#sites_import_icon").addClass("grayscale");
                                        } else {
                                            var any_url = validateAnyUrl();
                                            if (any_url) {
                                                $("#btn_import_fb").css('display', 'none');
                                                $("#btn_import_allrest").css('display', 'block');
                                                $("#facebook_import_icon").addClass("grayscale");
                                                $("#flickr_import_icon").addClass("grayscale");
                                                $("#youtube_import_icon").addClass("grayscale");
                                                $("#vimeo_import_icon").addClass("grayscale");
                                                $("#instagram_import_icon").addClass("grayscale");
                                                $("#files_import_icon").addClass("grayscale");
                                                $("#sites_import_icon").removeClass("grayscale");
                                            } else {
                                                $("#btn_import_fb").css('display', 'none');
                                                $("#btn_import_allrest").css('display', 'block');
                                                $("#facebook_import_icon").addClass("grayscale");
                                                $("#flickr_import_icon").addClass("grayscale");
                                                $("#youtube_import_icon").addClass("grayscale");
                                                $("#vimeo_import_icon").addClass("grayscale");
                                                $("#instagram_import_icon").addClass("grayscale");
                                                $("#files_import_icon").addClass("grayscale");
                                                $("#sites_import_icon").addClass("grayscale");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $("#btn_import_fb").css('display', 'none');
            $("#btn_import_allrest").css('display', 'block');
            $("#facebook_import_icon").addClass("grayscale");
            $("#flickr_import_icon").addClass("grayscale");
            $("#youtube_import_icon").addClass("grayscale");
            $("#vimeo_import_icon").addClass("grayscale");
            $("#instagram_import_icon").addClass("grayscale");
            $("#files_import_icon").addClass("grayscale");
            $("#sites_import_icon").addClass("grayscale");
        }
    }

    function import_youtube_video_url() {
        var youtube_video_url = $('#item_url_import_all').val().trim();
        var collectionId = $('#collection_id').val();

        if (youtube_video_url) {
            $('#modalImportMain').modal('show');
            var src = $('#src').val();

            $.ajax({
                url: src + '/controllers/social_network/youtube_controller.php',
                type: 'POST',
                data: {operation: 'import_video_url',
                    video_url: youtube_video_url,
                    collectionId: collectionId},
                success: function (result) {
                    var json = JSON.parse(result);
                    if (json.length > 0) {
                        showViewMultipleItemsSocialNetwork(json);
                    }
                    else {
                        hide_modal_main();
                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid Channel/Playlist or no videos to be imported', 'tainacan'); ?>', 'error');
                    }
                }
            });

        } else {
            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Youtube video url', 'tainacan'); ?>', 'error');
        }
    }
    // faz a importacao do tipo texto e joga para a tela de multiplos
    function import_text(url) {
        var key = $('#socialdb_embed_api_id').val();
        var ajaxurl = 'http://api.embed.ly/1/oembed?key=:' + key + '&url=' + url;
        //div loader
        if (key == '') {
            import_text_alternative(url);
        } else {
            $.getJSON(ajaxurl, {}, function (json) {
                console.log(json);
                var description = '', title = '';
                if (json.title !== undefined && json.title != null && json.title != false) {
                    title = json.title;
                }
                else {
                    hide_modal_main();
                    showAlertGeneral('Atenção', 'Esta URL não possui items disponíveis para importação', 'error');
                    return;
                }
                // se nao tiver descricao ele coloca o titulo na descricao
                if (json.description !== undefined && json.description != null && json.description != false) {
                    description += json.description;
                }
                else {
                    description = title;
                }
                //concatena o html na descricao
                if (json.html !== undefined && json.html != null && json.html != false) {
                    json.html = json.html.replace('width="854"', 'width="200"');
                    json.html = json.html.replace('height="480"', 'height="200"');
                    description = json.html + description;
                }
                //pegando a imagem
                var img = '';
                if (json.thumbnail_url !== undefined && json.thumbnail_url != null && json.thumbnail_url != false) {
                    img = json.thumbnail_url;
                }
                // verifico se existe imagem para ser importada
                $.ajax({
                    url: $('#src').val() + '/controllers/object/object_controller.php',
                    type: 'POST',
                    data: {
                        operation: 'add_item_not_published',
                        collection_id: $("#collection_id").val(),
                        description: description,
                        thumbnail_url: img,
                        type: 'text',
                        url: url,
                        title: title}
                }).done(function (result) {
                    var json = JSON.parse(result);
                    if (json.length > 0) {
                        showViewMultipleItemsSocialNetwork(json);
                    }
                    else {
                        hide_modal_main();
                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid Channel/Playlist or no videos to be imported', 'tainacan'); ?>', 'error');
                    }
                });
            }).fail(function (result) {
                // console.log('error', result, url);
                hide_modal_main();
                showAlertGeneral('Atenção', 'URL inexistente ou indisponível', 'error');
            });
        }
    }

    function import_text_alternative(url) {
        var ajaxurl = $('#src').val() + '/controllers/object/object_controller.php?operation=parse_url_alternative&url=' + url;
        $.getJSON(ajaxurl, {}, function (json) {
            console.log(json);
            var description = '', title = '';
            if (json.title !== undefined && json.title != null && json.title != false) {
                title = json.title;
            }
            else {
                hide_modal_main();
                showAlertGeneral('Atenção', 'Esta URL não possui items disponíveis para importação', 'error');
                return;
            }
            // se nao tiver descricao ele coloca o titulo na descricao
            if (json.description !== undefined && json.description != null && json.description != false) {
                description += json.description;
            }
            else {
                description = title;
            }
            //concatena o html na descricao
            if (json.html !== undefined && json.html != null && json.html != false) {
                json.html = json.html.replace('width="854"', 'width="200"');
                json.html = json.html.replace('height="480"', 'height="200"');
                description = json.html + description;
            }
            //pegando a imagem
            var img = '';
            if (json.thumbnail_url !== undefined && json.thumbnail_url != null && json.thumbnail_url != false) {
                img = json.thumbnail_url;
            }
            // verifico se existe imagem para ser importada
            $.ajax({
                url: $('#src').val() + '/controllers/object/object_controller.php',
                type: 'POST',
                data: {
                    operation: 'add_item_not_published',
                    collection_id: $("#collection_id").val(),
                    description: description,
                    thumbnail_url: img,
                    type: 'text',
                    url: url,
                    title: title}
            }).done(function (result) {
                var json = JSON.parse(result);
                if (json.length > 0) {
                    showViewMultipleItemsSocialNetwork(json);
                }
                else {
                    hide_modal_main();
                    showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid Channel/Playlist or no videos to be imported', 'tainacan'); ?>', 'error');
                }
            });
        });
    }

    function import_files_url(url, type) {
        var title = '';
        if (type == 'image') {
            title = '<?php _e('Image', 'tainacan') ?>';
        } else if (type == 'video') {
            title = '<?php _e('Video', 'tainacan') ?>';
        } else if (type == 'audio') {
            title = '<?php _e('Audio', 'tainacan') ?>';
        } else if (type == 'other') {
            title = '<?php _e('Other', 'tainacan') ?>';
        } else if (type == 'pdf') {
            title = '<?php _e('PDF', 'tainacan') ?>';
        }
        $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {
                operation: 'add_item_not_published',
                collection_id: $("#collection_id").val(),
                content: url,
                description: '',
                type: type,
                url: url,
                title: title}
        }).done(function (result) {
            var json = JSON.parse(result);
            if (json.length > 0) {
                showViewMultipleItemsSocialNetwork(json);
            }
            else {
                hide_modal_main();
                showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid Channel/Playlist or no videos to be imported', 'tainacan'); ?>', 'error');
            }
        });
    }


    function import_youtube_channel(inputIdentifierYoutube) {
        var collectionId = $('#collection_id').val();

        if (inputIdentifierYoutube) {
            $('#modalImportMain').modal('show');
            var src = $('#src').val();

            //ajax
            $.ajax({
                url: src + '/controllers/social_network/youtube_controller.php',
                type: 'POST',
                data: {operation: 'import_video_channel',
                    identifier: inputIdentifierYoutube,
                    url: $('#item_url_import_all').val(),
                    playlist: '',
                    collectionId: collectionId},
                success: function (response) {
                    var json = JSON.parse(response);
                    if (json.length > 0) {
                        showViewMultipleItemsSocialNetwork(json);
                        //showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('OK', 'tainacan'); ?>', 'success');
                        //wpquery_clean();
                    }
                    else {
                        hide_modal_main();
                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid Channel/Playlist or no videos to be imported', 'tainacan'); ?>', 'error');
                    }
                }
            });
            //end ajax

            $('#item_url_import_all').val('');
            $("#youtube_import_icon").addClass("grayscale");
            $('#modalshowModalImportAll').modal('hide');
        } else {
            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Youtube channel identifier', 'tainacan'); ?>', 'error');
        }
    }

    function import_youtube_playlist(inputIdentifierYoutube) {
        var collectionId = $('#collection_id').val();

        if (inputIdentifierYoutube) {
            $('#modalImportMain').modal('show');
            var src = $('#src').val();

            //ajax
            $.ajax({
                url: src + '/controllers/social_network/youtube_controller.php',
                type: 'POST',
                data: {operation: 'import_video_channel',
                    //identifier: inputIdentifierYoutube,
                    //playlist: inputPlaylistYoutube,
                    url: $('#item_url_import_all').val(),
                    playlist: inputIdentifierYoutube,
                    collectionId: collectionId},
                success: function (response) {
                    var json = JSON.parse(response);
                    if (json.length > 0) {
                        showViewMultipleItemsSocialNetwork(json);
                        //showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('OK', 'tainacan'); ?>', 'success');
                        //wpquery_clean();
                    }
                    else {
                        hide_modal_main();
                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid Channel/Playlist or no videos to be imported', 'tainacan'); ?>', 'error');
                    }
                }
            });
            //end ajax

            $('#item_url_import_all').val('');
            $("#youtube_import_icon").addClass("grayscale");
            $('#modalshowModalImportAll').modal('hide');
        } else {
            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Youtube channel identifier', 'tainacan'); ?>', 'error');
        }
    }

    function import_instagram(instagram_url) {
        var inputIdentifierInstagram = instagram_url.trim();
        var collection_id = $('#collection_id').val();

        if (inputIdentifierInstagram) {
            $('#modalImportMain').modal('show');
            var src = $('#src').val();

            window.location = src + "/controllers/social_network/instagram_controller.php?collection_id=" + collection_id + "&operation=getPhotosInstagram&identifier=" + inputIdentifierInstagram;

            $('#item_url_import_all').val('');
            $("#instagram_import_icon").addClass("grayscale");
            $('#modalshowModalImportAll').modal('hide');
        }
        else {
            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Instagram URL with user identifier', 'tainacan'); ?>', 'error');
        }
    }

    function import_flickr(flickr_url) {
        var inputIdentifierFlickr = flickr_url.trim();
        var collectionId = $('#collection_id').val();

        if (inputIdentifierFlickr) {
            $('#modalImportMain').modal('show');
            var src = $('#src').val();

            $.ajax({
                url: src + '/controllers/social_network/flickr_controller.php',
                type: 'POST',
                data: {operation: 'import_flickr_items',
                    identifier: inputIdentifierFlickr,
                    collectionId: collectionId},
                success: function (response) {
                    //se a gravação no banco foi realizado, a tabela é incrementada
                    var json = JSON.parse(response);
                    if (json.length > 0) {
                        showViewMultipleItemsSocialNetwork(json);
                        //showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('OK', 'tainacan'); ?>', 'success');
                        //wpquery_clean();
                    }
                    else {
                        hide_modal_main();
                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid Flickr identifier or no items to be imported', 'tainacan'); ?>', 'error');
                    }
                }
            });
            $('#item_url_import_all').val('');
            $("#flickr_import_icon").addClass("grayscale");
            $('#modalshowModalImportAll').modal('hide');
        }
        else {
            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Flickr identifier', 'tainacan'); ?>', 'error');
            $('#item_url_import_all').val('');
        }
    }

    function import_vimeo(type, identifier) {
        var inputIdentifierVimeo = identifier.trim();
        var collectionId = $('#collection_id').val();

        if (inputIdentifierVimeo) {
            $('#modalImportMain').modal('show');
            var src = $('#src').val();

            $.ajax({
                url: src + '/controllers/social_network/vimeo_controller.php',
                type: 'POST',
                data: {operation: 'import_vimeo_items',
                    identifier: inputIdentifierVimeo,
                    import_type: type,
                    collectionId: collectionId},
                success: function (response) {
                    //se a gravação no banco foi realizado, a tabela é incrementada
                    var json = JSON.parse(response);
                    if (json.length > 0) {
                        showViewMultipleItemsSocialNetwork(json);
                        //showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('OK', 'tainacan'); ?>', 'success');
                        //wpquery_clean();
                    }
                    else {
                        hide_modal_main();
                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Invalid Vimeo identifier or no items to be imported', 'tainacan'); ?>', 'error');
                    }
                }
            });
            $('#item_url_import_all').val('');
            $("#vimeo_import_icon").addClass("grayscale");
            $('#modalshowModalImportAll').modal('hide');
        }
        else {
            showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Necessary to inform Vimeo identifier', 'tainacan'); ?>', 'error');
            $('#item_url_import_all').val('');
        }
    }
    //*****************************************  END IMPORT ALL  *********************************************//
    /**
     * funcao que concatena um array em um input, separado por virgulas
     * @param {int} o ID do item que sera inserido no array
     * @param {string} O id do input que esta sendo concatenado
     * @returns {void}     */
    function concatenate_in_array(key, seletor) {
        var ids = [];
        var result;
        if ($(seletor).val() !== '') {
            ids = $(seletor).val().split(',');
            index = ids.indexOf(key);
            if (index >= 0) {
                ids.splice(index, 1);
                result = false;
            } else {
                ids.push(key);
                result = true;
            }
            $(seletor).val(ids.join(','));
        } else {
            ids.push(key);
            $(seletor).val(ids.join(','));
            result = true;
        }
        return result;
    }

    function add_label_box(id, name, seletor) {
        $(seletor).append('<span id="label-box-' + id + '" class="label label-primary">'
                + name + ' <a style="color:white;cursor:pointer;" onclick="remove_label_box(' + id + ')">x</a></span>&nbsp;');
    }

    function remove_label_box(id, dynatree) {
        var cont = 0;
        if (!dynatree)
            dynatree = "#property_category_dynatree";
        $(dynatree).dynatree("getRoot").visit(function (node) {
            if (node.data.key == id) {
                cont++;
                node.select(false);
            }
        });
        if (cont === 0) {
            var ids = $('#property_object_category_id').val().split(',');
            var index = ids.indexOf(id);
            if (index >= 0) {
                ids.splice(index, 1);
                $('#property_object_category_id').val(ids.join(','));
            }
        }
        $('#label-box-' + id).remove();
    }
    /**
     * 
     
     * @param {type} url
     * @returns {String} */
    function get_type_url(url) {
        var fileExtension = url.replace(/^.*\./, '');     // USING JAVASCRIPT REGULAR EXPRESSIONS.
        switch (fileExtension) {
            case 'png':
            case 'jpeg':
            case 'jpg':
            case 'gif':
                return 'image';
                break;
            case 'mp4':
            case 'wmv':
            case 'ogv':
            case 'mpg':
                return 'video';
            case 'pdf':
                return 'pdf';
                break;
            case 'mp3':
            case 'wav':
            case 'm4a':
            case 'ogg':
                return 'audio';
            default:
                return 'other';
        }
    }
    /************************************************ HELPERS **********************************************************/
    function setValueReverse(seletor) {
        if ($(seletor).val() !== 'false') {
            $('#property_object_is_reverse').val('true');
        } else {
            $('#property_object_is_reverse').val('false');
        }
    }
/************************************************ LIXEIRA **********************************************************/    
function show_trash_page(){
    $('#icon-search-bottom').parent().hide();
    $('#normal-selectable').hide();
    $('.button-trash').hide();
    $('#trash-selectable').show();
    showTrash('<?php echo get_template_directory_uri(); ?>');
    if($('#ul_menu_search').length>0){
        $('#ul_menu_search').hide();
    }
}

function hide_trash_page(){
    $('#icon-search-bottom').parent().show(); 
    $('#normal-selectable').show();
    $('.button-trash').show();
    $('#trash-selectable').hide();
    showList('<?php echo get_template_directory_uri(); ?>');
    if($('#ul_menu_search').length>0){
        $('#ul_menu_search').show();
    }
}
    
    
</script>
