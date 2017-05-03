<script>
    $(function () {
        var src = $('#src').val();
        var default_viewMode = $("#default-viewMode").val();

        $('.pagination_items').jqPagination({
            link_string: '/?page={page_number}',
            max_page: $('#number_pages').val(),
            paged: function (page) {
                $('html,body').animate({scrollTop: 0}, 'slow');
                wpquery_page(page, default_viewMode, true);
            }
        });

        if (default_viewMode == "slideshow") {
            getCollectionSlideshow();
        } else if(default_viewMode == "table") {
            setTimeout( function() {
                wpquery_page(1, default_viewMode, true);
            }, 300);
        }

        $('.viewMode-control li').removeClass('selected-viewMode');
        $('.viewMode-control li.' + default_viewMode).addClass('selected-viewMode');

        function get_colorScheme() {
            var coll_id = $('#collection_id').val();
            $.ajax({
                type: "POST",
                url: src + "/controllers/collection/collection_controller.php",
                data: {operation: 'get_default_color_scheme', collection_id: coll_id}
            }).done(function (r) {
                var color_scheme = $.parseJSON(r);
                if (color_scheme) {
                    $('#accordion .title-pipe').css('border-left-color', color_scheme.secondary);
                    $('.item-funcs li a').css('color', color_scheme.primary);

                    $('.prime-color-bg').css('background', color_scheme.primary);
                    $('.prime-color').css('color', color_scheme.secondary);
                    $('.sec-color-bg').css('background', color_scheme.secondary);
                    $('.sec-color').css('color', color_scheme.secondary);
                } else {
                    $('#div_left .expand-all').css('background', '#79a6ce');
                }
            });
        }
        get_colorScheme();

        $("#container_three_columns").removeClass('white-background');
        setMenuContainerHeight();
        
        $('a.move_eliminate').on('click', function() {
            var bulk_type = $('input.bulk_action_trash').val();
            var selected_total = 0;
            var bulkds = [];
            $('.selected-item').each(function(idx, el) {
                if($(el).hasClass('item-colecao')){
                    var item_id = $(el).parent().attr("id").replace("object_", "");
                    bulkds.push(item_id);
                    selected_total++;
                }
            });
            
            if( selected_total > 0 ) {
                var collection_id = $('#collection_id').val();
                var main_title = '<?php _e("Attention","tainacan"); ?>';
                var desc = '<?php _e("Are you sure in removing  ", "tainacan"); ?>' + selected_total + '<?php _e(" items permanently?", "tainacan"); ?>';
                eliminate_itens_collection(main_title, desc, bulkds, collection_id);
            } else {
                showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e("You did not select any items to delete!", "tainacan") ?>', 'info');
            }
        });
        
        $('.selectable-items-trash').on('click', '.selectors-trash a', function(ev) {
            var select = $(this).attr("class").split(" ")[0];
            console.log( select);
            $('input.bulk_action_trash').val( select );
            var its_highlighted = $( $(this).siblings()[0]).hasClass('highlight');
            var action =  $('input.bulk_action_trash').val();
            
            if( $(this).hasClass('highlight') ) {
                $(this).addClass('highlight');
            }else{
                $(this).removeClass('highlight')
            }
            
            if ( "select_all_trash" === select ) {
               // select_all_trash();
            }
            $( $(this).siblings()[0]).removeClass('highlight');
            $('.selectable-actions').fadeIn();
        });

        $('.toggleSelect').click(function() {
            if( $(this).hasClass('selected-item') ) {
                $(this).removeClass('selected-item')
            }else{
                $(this).addClass('selected-item');
            }
        });

    });

// funcao acionando no bolta voltar que mostra a listagem principal
    function back_button(object_id) {
        $('#data_property_form_' + object_id).hide();
        $('#object_property_form_' + object_id).hide();
        $('#edit_data_property_form_' + object_id).hide();
        $('#edit_object_property_form_' + object_id).hide();
        $('#list_all_properties_' + object_id).show();
    }
// END:fim das funcoes que mostram as propriedades

    function example(object_id) {
        $('#modalImportMain').modal('show');//mostro o modal de carregamento
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'edit', object_id: object_id}
        }).done(function (result) {
            hide_modal_main();
            $("#form").html('');
            $('#main_part').hide();
            $('#display_view_main_page').hide();
            $('#loader_collections').hide();
            $('#configuration').html(result).show();
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }

    function restore_object(object_id) {
        show_modal_main();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'restore_object', object_id: object_id}
        }).done(function (result) {
            if (result) {
                showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('The restoration of the item was successful!', 'tainacan'); ?>', 'success');
                showTrash($('#src').val());
            } else {
                hide_modal_main();
                showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Error restoring the item.', 'tainacan'); ?>', 'error');
            }
        });
    }

    function delete_permanently_object(title, text, object_id) {
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
                    url: $('#src').val() + "/controllers/object/object_controller.php",
                    data: {collection_id: $('#collection_id').val(), operation: 'delete_permanently_object', object_id: object_id}
                }).done(function (result) {
                    if (result) {
                        showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('Item successfully deleted!', 'tainacan'); ?>', 'success');
                        showTrash($('#src').val());
                    } else {
                        hide_modal_main();
                        showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Oops! Failed attempt to delete.', 'tainacan'); ?>', 'error');
                    }
                });
            }
        });
    }

    function delete_object2(object_id) {
        show_modal_main();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'delete_permanently_object', object_id: object_id}
        }).done(function (result) {
            if (result) {
                showAlertGeneral('<?php _e('Success', 'tainacan'); ?>', '<?php _e('Item successfully deleted!', 'tainacan'); ?>', 'success');
                showTrash($('#src').val());
            } else {
                hide_modal_main();
                showAlertGeneral('<?php _e('Error', 'tainacan'); ?>', '<?php _e('Oops! Failed attempt to delete.', 'tainacan'); ?>', 'error');
            }
        });
    }

    function show_value_ordenation(object_id, div_base, btn_base) {
        if (!div_base) {
            div_base = "#rankings_";
            btn_base = "#show_rankings_";
        }
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/ranking/ranking_controller.php",
            data: {collection_id: $('#collection_id').val(), ordenation_id: $('#collection_single_ordenation').val(), operation: 'list_value_ordenation', object_id: object_id}
        }).done(function (result) {
            $(btn_base + object_id).hide();
            $(div_base + object_id).html(result).show();
        });
    }

    function check_privacity_info(id) {
        $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: {operation: 'check_privacity', collection_id: id}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if (elem.privacity == false)
            {
                redirect_privacity(elem.title, elem.msg, elem.url);
            }
        });
    }

    function showModalCreateCollection() {
        $('#myModal').modal('show');
    }

    var col_title = $('.titulo-colecao h3.title').text();
    $("#collection-slideShow .sS-collection-name").text(col_title);

    /*
     * Slideshow view Mode slider
     * */
    $('.main-slide').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        fade: true,
        asNavFor: '.collection-slides',
        adaptiveHeight: true
    });

    $('.collection-slides').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        asNavFor: '.main-slide',
        variableWidth: true,
        dots: true,
        centerMode: true,
        // arrows: true,
        arrows: false,
        adaptiveHeight: true,
        autoplay: true,
        focusOnSelect: true
    });
/*******************************************************************************/
    function select_some_trash() {
         if( ! $('.toggleSelect').hasClass('selecting-item') ) {
             toastr.info('<?php _e('Select items below to edit or exclude!', 'tainacan') ?>', '', set_toastr_class());
         }

         $('.object_id').each(function(idx, el) {
            var item = $("#object_" + $(el).val() );
            $(item).find('.toggleSelect').addClass('selecting-item');
         });
    }

    function select_all_trash() {
        console.log($('.toggleSelect'));
        $(".toggleSelect").removeClass('selected-item');
        toastr.info('<?php _e('All items have been selected!', 'tainacan') ?>', '', set_toastr_class());
       $(".toggleSelect").each(function(idx, el) {
           if($(el).is(':visible')){
                $(".toggleSelect").addClass('selected-item');
                console.log('inside_',$(el).val());
            }
        });
    }
</script>
