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

    var _col_id_ = $("#collection_id").val();
    var _root_repo_id_ = $("#collection_root_id").val();    
    setAdminHeader(_root_repo_id_, _col_id_);

    var curr_col_title = $('.titulo-colecao h3 a').text() || $('.titulo-colecao h3').text();
    var collection_data = { name: curr_col_title, url: $('.titulo-colecao h3 a').attr('href') };
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
        get_user_notifications();
        /*
        $('.root-notifications a').mouseenter(function() {
            get_user_notifications();
        });
        */

    });

    function clear_list() {
        $("#value_search").val('');
        $("#search_objects").val('');
        $("#search_collections").val('');
        $("#search_collection_field").val('');

        //list_main_ordenation();
        wpquery_clean();
        //reboot_form();
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
            type: "POST", url: $('#src').val() + "/controllers/event/event_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'notification_events'}
        }).done(function (result) {
            if(result.length > 6 && result != undefined) {
                $('.notification_events').html(result).css('padding', '0 4px 2px 1px');
            } else {
                $('.notification_events').hide();
            }
        });
    }

    function get_user_notifications() {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/event/event_controller.php",
            data: { collection_id: $('#collection_id').val(), operation: 'user_notification' }
        }).done(function(r) {
            var _ev_ = $.parseJSON(r);
            if(_ev_ && _ev_.total_evts > 0) {
                var _item_html = "";
                $(_ev_.evts).each(function(id, el) {
                    var events_path = '<?php _e("events", "tainacan")?>';
                    var extra_class = '';
                    if(el.is_root && el.is_root == true) {
                        var URL = $("#site_url").val() + '/admin/'+ events_path;
                        extra_class = 'trigger-events';
                    } else {
                        var URL = $("#site_url").val() + '/' + el.path + '/admin/'+ events_path +'/';
                    }

                    var content = "<span class='evt_col'> " + el.colecao + "</span> <span class='evts_cnt'>" + el.counting + "</span>";
                    _item_html += "<li class='col-md-12 no-padding'> <a class='evt_container evt-"+ id + ' ' + extra_class + "' href='" + URL + "'> ";
                    _item_html += content;
                    _item_html += "</a></li>";                                    
                });
                $(_item_html).appendTo('li.root-notifications ul');
                $('li.root-notifications').removeClass('hide');
                $('li.root-notifications .notification_events_repository').text(_ev_.total_evts);
            }
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
