<script>!function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
        if (!d.getElementById(id)) {
            js = d.createElement(s);
            js.id = id;
            js.src = p + '://platform.twitter.com/widgets.js';
            fjs.parentNode.insertBefore(js, fjs);
        }
    }(document, 'script', 'twitter-wjs');
</script>
<script>
    $.widget("custom.catcomplete", $.ui.autocomplete, {
        _create: function () {
            this._super();
            this.widget().menu("option", "items", "> :not(.ui-autocomplete-category)");
        },
        _renderMenu: function (ul, items) {
            var that = this,
                    currentCategory = "";
            $.each(items, function (index, item) {
                var li;
                if (item.category != currentCategory) {
                    ul.append("<li class='ui-autocomplete-category'><b>" + item.category + "</b></li>");
                    currentCategory = item.category;
                }
                li = that._renderItemData(ul, item);
                if (item.category) {
                    li.attr("aria-label", item.category + " : " + item.label);
                }
            });
        }
    });

    var collection_data = { name: $('.titulo-colecao h3 a').text(), url: $('.titulo-colecao h3 a').attr('href') };
    $("#tainacan-breadcrumbs span.collection-title").text(collection_data.name);

    $(function () {

        if ( $(".ibram-header").is(":visible") ) {
            $(".collection_header_img").hide();
        }

        $('.dropdown-toggle').dropdown();
        $("#search_objects").catcomplete({
            delay: 0,
            minLength: 2,
            source: <?php echo json_encode($json_autocomplete); ?>,
            select: function (event, ui) {
                var str = '' + ui.item.id+'';
                $("#search_objects").val('');
                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
//                $("#dynatree").dynatree("getRoot").visit(function (node) {
//                    //console.log(node.data.key, ui.item.id, '' + node.data.key + '' === '' + ui.item.id + '');
//                    if ('' + node.data.key + '' === '' + ui.item.id + '') {
//                        match = node;
//                        node.toggleExpand();
//                        node.select(node);
//                        return true; // stop traversal (if we are only interested in first match)
//                    }
//                });
                if (str.indexOf("_keyword") >= 0) {
                    wpquery_keyword("'" + str.replace('_keyword', '')+ "'");
                }else{
                    wpquery_keyword("'" + ui.item.value+ "'");
                }
                setTimeout(function () {
                    $("#search_objects").val(ui.item.label);
                }, 100);
            }
        });
        
        $('#resources_collection_button').click(function (e) {
            var posX = e.target.offsetLeft;
            var posY = e.target.offsetTop ;
            $('#resources_collection_dropdown').css('left', posX);
             $('#resources_collection_dropdown').css('top', posY+32);
             e.preventDefault();
        });

        notification_events();

        //popover
        //$('[data-toggle="popover"]').popover();

//        // *************** Iframe Popover Collection ****************
//        //$('#iframebutton').attr('data-content', 'Teste').data('bs.popover').setContent();
//        var myPopover = $('#iframebutton').data('popover');
//        $('#iframebutton').popover('hide');
//        myPopover.options.html = true;
//        //<iframe width="560" height="315" src="https://www.youtube.com/embed/CGyEd0aKWZE" frameborder="0" allowfullscreen></iframe>
//        myPopover.options.content = '<form><input type="text" style="width:200px;" value="<iframe width=\'800\' height=\'600\' src=\'' + $("#socialdb_permalink_collection").val() + '\' frameborder=\'0\'></iframe>" /></form>';

    });


    function clear_list() {
        $("#value_search").val('');
        $("#search_objects").val('');
        $("#search_collections").val('');
        $("#search_collection_field").val('');

        list_main_ordenation();
        wpquery_clean();

        $("button#clear").fadeOut();
    }

    function set_value(e) {
        var search_for = $(e).val();
        $("#value_search").val(search_for);
    }
    /**
     * funcao que mostra o total de eventos novos no menu da colecao
     * @returns {html}
     */
    function notification_events() {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/event/event_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'notification_events'}
        }).done(function (result) {
            $('#notification_events').html(result);
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
   
    /**
     * 
     * @param {type} collection_id
     * @returns {html}
     */
    function export_selected_objects_json() {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/wp_query/wp_query_controller.php",
            data: {operation: 'link_json', wp_query_args: $('#wp_query_args').val(), collection_id: $('#collection_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            window.location = elem.url;
        });
    }
    /**
     * 
     * @param {type} collection_id
     * @returns {html}
     */
    function deleteCollection(collection_id) {
        swal({
            title: '<?php _e('Are you sure', 'tainacan') ?>',
            text: '<?php _e('Delete this collection?', 'tainacan') ?>',
            type: "warning",
            showCancelButton: true,
            cancelButtonText: '<?php _e('Cancel', 'tainacan') ?>',
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: false,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/collection/collection_controller.php",
                    data: {
                        operation: 'delete_collection',
                        collection_id: collection_id
                    }
                }).done(function (result) {
                    elem_first = jQuery.parseJSON(result);
                    showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);

                    window.location = elem_first.url;
                });
            }
        });
    }

//mostrar modal de denuncia
    function show_report_abuse_collection(collection_id) {
        $('#modal_delete_collection' + collection_id).modal('show');
        console.log($('#modal_delete_collection' + collection_id));
    }
</script>
