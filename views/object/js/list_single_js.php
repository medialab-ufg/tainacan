<script>
    $(function () {
        $("#main_part_collection").hide();
        var is_item_home = $("#configuration .item-breadcrumbs").siblings().first().is("#single_object_id");
        if( is_item_home ) {
            $("#configuration").css('margin-top', 50);
        }

        change_breadcrumbs_title('<?php _e('Import', 'tainacan') ?>');

        $('img').bind('contextmenu', function (e) {
            return false;
        });

        var is_col_header_visible = $(".collection_header").is(":visible");
        if (!is_col_header_visible) {
            //$('.header-navbar').css("margin-bottom", 0);
            $('body').css('background-color', '#f2f2f2');
        }

        //botao voltar do browser
        if (window.history && window.history.pushState) {
            previousRoute = window.location.pathname;
            window.history.pushState('forward', null, $('#route_blog').val()+$('#slug_collection').val()+'/'+$('#single_name').val());
            //
        }
        var stateObj = {foo: "bar"};
        $('#form').html('');
//        $('#object_page').val($('#single_name').val());
//        history.replaceState(stateObj, "page 2", $('#socialdb_permalink_object').val());

        if( ! $('body').hasClass('page-template-page-statistics') ) {
            //verifyPublishedItem($('#single_object_id').val());
            list_files_single($('#single_object_id').val());
            list_ranking_single($('#single_object_id').val());
            list_properties_single($('#single_object_id').val());
            list_properties_edit_remove_single($('#single_object_id').val());
            list_comments($('#single_object_id').val());
            $('[data-toggle="popoverObject"]').popover();
        }


        var myPopoverObject = $('#iframebuttonObject').data('popover');
        $('#iframebuttonObject').popover('hide');
        myPopoverObject.options.html = true;
        //<iframe width="560" height="315" src="https://www.youtube.com/embed/CGyEd0aKWZE" frameborder="0" allowfullscreen></iframe>
        myPopoverObject.options.content = $('#socialdb_permalink_object').val();
        // form thumbnail
        $('#formThumbnail').submit(function (e) {
            e.preventDefault();
            $('#single_modal_thumbnail').modal('hide');
            $('#modalImportMain').modal('show');//mostro o modal de carregamento

            $.ajax({
                url: $('#src').val() + "/controllers/object/objectsingle_controller.php",
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).success(function (result) {
                elem = jQuery.parseJSON(result);
                if (elem.attachment_id) {
                    insert_fixed_metadata($('#single_object_id').val(), 'thumbnail', elem.attachment_id);
                } else {
                    $('#modalImportMain').modal('hide');//mostro o modal de carregamento
                }
            });
        });
        //carrego as licensas ativas
        $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {operation: 'show_collection_licenses', object_id: $('#single_object_id').val(), collection_id: $("#collection_id").val()}
        }).done(function (result) {
            $('#event_license').html(result);
        });
    });


    /*
     * Increments item's collection view count
     * @author Rodrigo Guimarães
     * */
    function increment_collection_view_count(collection_id) {
        $.ajax({
            url: $('#src').val() + "/controllers/object/objectsingle_controller.php",
            data: {collection_id: collection_id, operation: 'increment_collection_count'}
        });
    }

//BEGIN: funcao para mostrar os arquivos
    function list_files_single(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'show_files', object_id: id}
        }).done(function (result) {
            $('#single_list_files_' + id).html(result);
        });
    }
//END
//BEGIN: funcao para mostrar votacoes
    function list_ranking_single(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'single_list_ranking_object', object_id: id}
        }).done(function (result) {
            $('#single_list_ranking_' + id).html(result);
        });
    }
//END
//BEGIN:as proximas funcoes sao para mostrar os eventos
// list_properties(id): funcao que mostra a primiera listagem de propriedades
    function list_properties_single(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/objectsingle_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'list_properties_renew', object_id: id}
        }).done(function (result) {
            $('#single_list_all_properties_' + id).html(result);
            var main_height = $('#single_item_tainacan .item-main-data').height();
            $("#single_item_tainacan .item-attachments").height(main_height);

            var meta_count = $('#single_list_all_properties_' + id + ' .col-md-6').length;
            if( meta_count > 0 && (meta_count % 2 != 0) ) {
                var last_meta = $('#single_list_all_properties_' + id + ' .col-md-6').last();
                $(last_meta).removeClass('col-md-6').addClass('col-md-12').css('border-top', '3px solid #E8E8E8');
            }
        });
    }
// mostra a listagem apos clique no botao para edicao e exclusao
    function list_properties_edit_remove_single(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/objectsingle_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'list_properties_edit_remove', object_id: id}
        }).done(function (result) {
            $('#single_list_properties_edit_remove').html(result);
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }

// mostra o formulario para criacao de propriedade de dados
    function show_form_data_property_single(object_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/objectsingle_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'show_form_data_property', object_id: object_id}
        }).done(function (result) {
            $('#single_list_all_properties_' + object_id).hide();
            $('#single_object_property_form_' + object_id).hide();
            $('#single_edit_data_property_form_' + object_id).hide();
            $('#single_edit_object_property_form_' + object_id).hide();
            $('#single_data_property_form_' + object_id).html(result).css('padding', 20).show();
            $('.dropdown-toggle').dropdown();
        });
    }
// mostra o formulario para criacao de propriedade de objeto
    function show_form_object_property_single(object_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/objectsingle_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'show_form_object_property', object_id: object_id}
        }).done(function (result) {
            $('#single_list_all_properties_' + object_id).hide();
            $('#single_data_property_form_' + object_id).hide();
            $('#single_edit_data_property_form_' + object_id).hide();
            $('#single_edit_object_property_form_' + object_id).hide();
            $('#single_object_property_form_' + object_id).html(result).css('padding', 20).show();
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
// funcao acionando no bolta voltar que mostra a listagem principal
    function back_button_single(object_id) {
        $('#single_data_property_form_' + object_id).hide();
        $('#single_object_property_form_' + object_id).hide();
        $('#single_edit_data_property_form_' + object_id).hide();
        $('#single_edit_object_property_form_' + object_id).hide();
        $('#single_list_all_properties_' + object_id).show();
    }
// END:fim das funcoes que mostram as propriedades
//funcao que mostra as classificacoes apos clique no botao show_classification
    function show_classifications_single(object_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/objectsingle_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'show_classifications', object_id: object_id}
        }).done(function (result) {
            $('#single_show_classificiations_' + object_id).hide();
            $('#single_classifications_' + object_id).html(result).show();
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }

    //mostrar modal de denuncia
    function single_show_report_abuse(object_id) {
        $('#single_modal_delete_object' + object_id).modal('show');
    }

    //Events deletes (category) alert
    function single_remove_event_category_classication(title, text, category_id, object_id, time) {
        swal({
            title: title,
            text: text,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/event/event_controller.php",
                    data: {
                        operation: 'add_event_classification_delete',
                        socialdb_event_create_date: time,
                        socialdb_event_user_id: $('#current_user_id').val(),
                        socialdb_event_classification_object_id: object_id,
                        socialdb_event_classification_term_id: category_id,
                        socialdb_event_classification_type: 'category',
                        socialdb_event_collection_id: $('#collection_id').val()}
                }).done(function (result) {
                    elem_first = jQuery.parseJSON(result);
                    show_classifications_single(object_id);
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);

                });
            }
        });
    }

    function single_remove_event_property_classication(title, text, category_id, object_id, time, type) {
        swal({
            title: title,
            text: text,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/event/event_controller.php",
                    data: {
                        operation: 'add_event_classification_delete',
                        socialdb_event_create_date: time,
                        socialdb_event_user_id: $('#current_user_id').val(),
                        socialdb_event_classification_object_id: object_id,
                        socialdb_event_classification_term_id: category_id,
                        socialdb_event_classification_type: type,
                        socialdb_event_collection_id: $('#collection_id').val()}
                }).done(function (result) {
                    elem_first = jQuery.parseJSON(result);
                    show_classifications_single(object_id);
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);

                });
            }
        });
    }
// deletar objeto
    function single_delete_object(title, text, object_id, time) {
        swal({
            title: title,
            text: text,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/event/event_controller.php",
                    data: {
                        operation: 'add_event_object_delete',
                        socialdb_event_create_date: time,
                        socialdb_event_user_id: $('#current_user_id').val(),
                        socialdb_event_object_item_id: object_id,
                        socialdb_event_collection_id: $('#collection_id').val()}
                }).done(function (result) {
                    elem_first = jQuery.parseJSON(result);
                    backToMainPage();
                    showList($('#src').val());
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                });
            }
        });
    }

    function single_report_abuse_object(title, text, object_id, time) {
        $('#modal_delete_object' + object_id).modal('hide');
        swal({
            title: title,
            text: text,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/event/event_controller.php",
                    data: {
                        operation: 'add_event_object_delete',
                        socialdb_event_create_date: time,
                        socialdb_event_observation: $('#observation_delete_object' + object_id).val(),
                        socialdb_event_user_id: $('#current_user_id').val(),
                        socialdb_event_object_item_id: object_id,
                        socialdb_event_collection_id: $('#collection_id').val()}
                }).done(function (result) {
                    elem_first = jQuery.parseJSON(result);
                    backToMainPage();
                    showList($('#src').val());
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                });
            }
        });
    }


    function single_remove_event_tag_classication(title, text, tag_id, object_id, time) {
        swal({
            title: title,
            text: text,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/event/event_controller.php",
                    data: {
                        operation: 'add_event_classification_delete',
                        socialdb_event_create_date: time,
                        socialdb_event_user_id: $('#current_user_id').val(),
                        socialdb_event_classification_object_id: object_id,
                        socialdb_event_classification_term_id: tag_id,
                        socialdb_event_classification_type: 'tag',
                        socialdb_event_collection_id: $('#collection_id').val()}
                }).done(function (result) {
                    elem_first = jQuery.parseJSON(result);
                    show_classifications_single(object_id);
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);

                });
            }
        });
    }

    function single_show_item_versions(object_id) {
        show_modal_main();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                operation: 'show_item_versions',
                object_id: object_id
            }
        }).done(function (result) {
            hide_modal_main();
            $('#main_part').hide();
            $('#display_view_main_page').hide();
            $('#loader_collections').hide();
            $('#collection_post').hide();
            $('#configuration').html(result).show(); 
        });
    }

    function downloadItem(thumb_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {
                item_id: $("#single_object_id").val(),
                collection_id: $('#collection_id').val(),
                operation: 'insertUserDownload',
                thumb_id: thumb_id
            }
        }).done(function (result) {
            //No result
        });
    }

//mostrar modal de denuncia
    function show_edit_object(object_id) {
        backToMainPage();
        edit_object_item(object_id);
    }
// editando objeto
    function edit_object_item(object_id) {
        var stateObj = {foo: "bar"};
        history.replaceState(stateObj, "page 2", '?');
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'edit', object_id: object_id}
        }).done(function (result) {
            hide_modal_main();
            if(result.trim().indexOf('checkout@')>=0){
                var arrayN = result.trim().split('@');
                showAlertGeneral('<?php _e('Attention!','tainacan') ?>','<?php _e('Item blocked by user ','tainacan') ?>','info');
            }else{
                $("#form").html('');
                $('#main_part').hide();
                $('#display_view_main_page').hide();
                $('#loader_collections').hide();
                $('#configuration').html(result).slideDown();
                $('.dropdown-toggle').dropdown();
                $('.nav-tabs').tab();
            }
        });
    }
//################  FUNCOES PARA OS COMENTARIOS ################################# 
//listando os comentarios
    function list_comments(object_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'list_comments', object_id: object_id}
        }).done(function (result) {
            verifyPublishedItem(object_id);
            $("#comments_object").html(result);
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
//
    function submit_comment_old() {
        $.ajax({
            type: "POST",
            url: '<?php echo get_option('siteurl'); ?>/wp-comments-post.php',
            data: {
                comment_post_ID: $('#single_object_id').val(),
                comment: $('#comment').val(),
                author: $('#author').val(),
                email: $('#email').val(),
                url: $('#url').val(),
                redirect_to: $('#redirect_to').val()}
        }).done(function (result) {
            console.log(result);
            list_comments($('#single_object_id').val());
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }


    function submit_comment_reply_old() {
        $.ajax({
            type: "POST",
            url: '<?php echo get_option('siteurl'); ?>/wp-comments-post.php',
            data: {
                socialdb_event_user_id: $('#current_user_id').val(),
                comment_post_ID: $('#single_object_id').val(),
                comment: $('#comment_msg_reply').val(),
                author: $('#author_reply').val(),
                email: $('#email_reply').val(),
                url: $('#url_reply').val(),
                redirect_to: $('#redirect_to').val(),
                comment_parent: $('#comment_id').val()
            }
        }).done(function (result) {
            list_comments($('#single_object_id').val());
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
            $('#modalReplyComment').modal("hide");
            showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('Reply successfully sent.', 'tainacan'); ?>', 'success');
            $('html, body').animate({
                scrollTop: $("#comments").offset().top
            }, 2000);
        });
    }


    /******************************* Metadados Fixos *******************************/
    // back title
    function cancel_license() {
        $('#cancel_license').hide();
        $('#save_license').hide();
        $('#event_license').hide();
        $('#edit_license').show();
        $('#text_license').show();
    }
    //save title
    function save_license(object_id) {
        insert_fixed_metadata(object_id, 'license', $('input[name="object_license"]:checked').val());
    }
    function edit_license() {
        $('#edit_license').hide();
        $('#text_license').hide();
        $('#cancel_license').show();
        $('#save_license').show();
        $('#event_license').show();
    }
    // THUMBNAIL
    // edit Thumbnail
    function edit_thumbnail() {
        $('#single_modal_thumbnail').modal('show');
    }
    //TITLE
    // edit title
    function edit_title() {
        $('#edit_title').hide();
        $('#text_title').hide();
        $('#cancel_title').show();
        $('#save_title').show();
        $('#event_title').show();
    }
    // back title
    function cancel_title() {
        $('#cancel_title').hide();
        $('#save_title').hide();
        $('#event_title').hide();
        $('#edit_title').show();
        $('#text_title').show();
    }
    //save title
    function save_title(object_id) {
        insert_fixed_metadata(object_id, 'title', $('#title_field').val());
    }
    //Type  
    // edit type
    function edit_type() {
        $('#edit_type').hide();
        $('#text_type').hide();
        $('#cancel_type').show();
        $('#save_type').show();
        $('#event_type').show();
    }
    // back type
    function cancel_type() {
        $('#cancel_type').hide();
        $('#save_type').hide();
        $('#event_type').hide();
        $('#edit_type').show();
        $('#text_type').show();
    }
    //save type
    function save_type(object_id) {
        insert_fixed_metadata(object_id, 'type', $('input[name="type_field"]:checked').val());
    }
    //SOURCE   
    // edit source
    function edit_source() {
        $('#edit_source').hide();
        $('#text_source').hide();
        $('#cancel_source').show();
        $('#save_source').show();
        $('#event_source').show();
    }
    // back Description(
    function cancel_source() {
        $('#cancel_source').hide();
        $('#save_source').hide();
        $('#event_source').hide();
        $('#edit_source').show();
        $('#text_source').show();
    }
    //save Description
    function save_source(object_id) {
        insert_fixed_metadata(object_id, 'source', $('#source_field').val());
    }
    //DESCRIPTION    
    // edit Description
    function edit_description() {
        $('#edit_description').hide();
        $('#text_description').hide();
        $('#cancel_description').show();
        $('#save_description').show();
        $('#event_description').show();
    }
    // back Description(
    function cancel_description() {
        $('#cancel_description').hide();
        $('#save_description').hide();
        $('#event_description').hide();
        $('#edit_description').show();
        $('#text_description').show();
    }
    //save Description
    function save_description(object_id) {
        insert_fixed_metadata(object_id, 'description', $('#description_field').val());
    }
    //TAG
    // edit tag
    function edit_tag() {
        $('button.edit-tag').hide();
        $('#edit_tag').hide();
        $('#cancel_tag').show();
        $('#save_tag').show();
        $('#event_tag').show();
    }
// back Description(
    function cancel_tag() {
        $('button.edit-tag').show();
        $('#cancel_tag').hide();
        $('#save_tag').hide();
        $('#event_tag').hide();
        $('#edit_tag').show();
    }
    //save Description
    function save_tag(object_id) {
        $('#modalImportMain').modal('show');//mostro o modal de carregamento
        $.ajax({
            url: $('#src').val() + '/controllers/event/event_controller.php',
            type: 'POST',
            data: {
                operation: 'add_event_tag_create',
                socialdb_event_create_date: '<?php echo mktime() ?>',
                socialdb_event_user_id: $('#current_user_id').val(),
                socialdb_event_tag_suggested_name: $('#event_tag_field').val(),
                socialdb_event_collection_id: $('#collection_id').val()
            }
        }).done(function (result) {
            $('#cancel_tag').hide();
            $('#save_tag').hide();
            $('#event_tag').hide();
            $('#edit_tag').show();
            $('#event_tag_field').val('');
            elem = jQuery.parseJSON(result);
            if (elem.term_id.length> 0) {
                add_tag_item(object_id, elem.term_id);
            } else {
                $('#modalImportMain').modal('hide');//mostro o modal de carregamento
                console.log(elem);
                showAlertGeneral(elem.title, '<?php _e('This tag was sent for approval, the classification will be able after this operation!', 'tainacan') ?>', elem.type);
            }

        });
    }

    //adiciona a tag no item
    function add_tag_item(object_id, value_id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/event/event_controller.php",
            data: {
                operation: 'add_event_classification_create',
                socialdb_event_create_date: '<?php echo mktime(); ?>',
                socialdb_event_user_id: $('#current_user_id').val(),
                socialdb_event_classification_object_id: object_id,
                socialdb_event_classification_term_id: value_id.join(','),
                socialdb_event_classification_type: 'tag',
                socialdb_event_collection_id: $('#collection_id').val()}
        }).done(function (result) {
            $('#modalImportMain').modal('hide');//mostro o modal de carregamento
            elem_first = jQuery.parseJSON(result);
            show_classifications_single(object_id);
            showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);

        });
    }

    //altera a classificacao do metadado e carrega novamente a tela do item
    function insert_fixed_metadata(object_id, type, value) {
        $('#modalImportMain').modal('show');//mostro o modal de carregamento
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/event/event_controller.php",
            data: {
                socialdb_event_collection_id: $('#collection_id').val(),
                operation: 'add_event_property_data_edit_value',
                socialdb_event_create_date: '<?php echo mktime(); ?>',
                socialdb_event_user_id: $('#current_user_id').val(),
                socialdb_event_property_data_edit_value_object_id: object_id,
                socialdb_event_property_data_edit_value_property_id: type,
                socialdb_event_property_data_edit_value_attribute_value: value}
        }).done(function (result) {
            $('#modalImportMain').modal('hide');//mostro o modal de carregamento
            elem = jQuery.parseJSON(result);
            showAlertGeneral(elem.title, elem.msg, elem.type);
            showSingleObjectByName($('#object_page').val(), $('#src').val());
        });
    }


     function single_do_checkout(id){
        $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {operation: 'check-out', collection_id: $('#collection_id').val(), object_id: id}
        }).done(function (result) {
            showAlertGeneral('<?php _e('Success!','tainacan') ?>','<?php _e('Checkout enabled!') ?>','success');
            location.reload();
        });
    }
    
    function single_discard_checkout(id){
        $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {operation: 'check-out', collection_id: $('#collection_id').val(), object_id: id,value:''}
        }).done(function (result) {
            showAlertGeneral('<?php _e('Success!','tainacan') ?>','<?php _e('Checkout disabled!') ?>','success');
           location.reload();
        });
    }
    
    function single_do_checkin(id){
         $('.dropdown-menu .dropdown-hover-show').trigger('mouseout');
        swal({
            title: "<?php _e('Checkin') ?>",
            text: "<?php _e('Checkin motive:') ?>",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: true,
            inputPlaceholder: "<?php _e('Type check in motive') ?>"
        },
        function(inputValue){
          if (inputValue === false) return false;

          if (inputValue === "") {
            swal.showInputError("<?php _e('You need to write something!') ?>");
            return false
          }
          show_modal_main();
            $.ajax({
                url: $('#src').val() + '/controllers/object/object_controller.php',
                type: 'POST',
                data: {operation: 'check-in', collection_id: $('#collection_id').val(), object_id: id,motive:inputValue}
            }).done(function (result) {
                 wpquery_filter();
                 hide_modal_main();
                showAlertGeneral('<?php _e('Success!','tainacan') ?>','<?php _e('Checkin done!') ?>','success');
                $("#form").html('');
                $('#main_part').hide();
                $('#display_view_main_page').hide();
                $('#loader_collections').hide();
                $('#configuration').html(result).show();
                $('.dropdown-toggle').dropdown();
                $('.nav-tabs').tab();
            });
        });
    }   
</script>
