<script>
      var n = 0;
       var key = '';
      var decodeEntities = (function() {
      // this prevents any overhead from creating the object each time
      var element = document.createElement('div');

      function decodeHTMLEntities (str) {
        if(str && typeof str === 'string') {
          // strip script/html tags
          str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
          str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
          element.innerHTML = str;
          str = element.textContent;
          element.textContent = '';
        }

        return str;
      }

      return decodeHTMLEntities;
      })();

    $(function () {
        showDynatreeLeft($('#src').val());
        //se existir filtro para eventos
        if($('#filters_has_event_notification').val()=='true'){
            list_events_filters();
        }

    });

    function showDynatreeLeft(src)
    {
        var select = 0;
        $("#dynatree").dynatree({
            selectionVisible: true, // Make sure, selected nodes are visible (expanded).
            checkbox: true,
            initAjax: {
                url: src + '/controllers/collection/collection_controller.php',
                data: {
                    collection_id: $("#collection_id").val(),
                    operation: 'initDynatree'
                },
                addActiveKey: true
            },
            onLazyRead: function (node) {
                node.appendAjax({
                    url: src + '/controllers/collection/collection_controller.php',
                    data: {
                        key: node.data.key,
                        collection: $("#collection_id").val(),
                        classCss: node.data.addClass,
                        operation: 'expand_dynatree'
                    },
                    success: function(node) {
                        $("#dynatree a").hover(function(){
                            var node = $.ui.dynatree.getNode(this);
                            var key = node.data.key;
                            $(node.span).attr('id',"ui-dynatree-id-" + node.data.key)
                            var n = key.toString().indexOf("_");
                            if($('#context_menu_'+node.data.key).length==0){
                                if (n > 0) {// se for propriedade de objeto
                                    values = key.split("_");
                                    if (values[1] === 'tag' || (values[1] === 'facet' && values[2] === 'tag')) {
                                        $(node.span).append('<a id="context_menu_'+node.data.key+'" onclick="triggerContextMenu('+"'#ui-dynatree-id-"+node.data.key+"'"+
                                               ',event,'+"'myMenuSingleTag'"+')" style="display:none;cursor:pointer;"><span class="glyphicon glyphicon-chevron-down"></span></a>');
                                    } else if (values[1] === 'facet' && values[2] === 'category') {
                                        $(node.span).append('<a id="context_menu_'+node.data.key+'" onclick="triggerContextMenu('+"'#ui-dynatree-id-"+node.data.key+"'"+
                                               ',event,'+"'myMenuSingle'"+')" style="display:none;cursor:pointer;"><span class="glyphicon glyphicon-chevron-down"></span></a>');
                                    }
                                } else {
                                    $(node.span).append('<a id="context_menu_'+node.data.key+'" onclick="triggerContextMenu('+"'#ui-dynatree-id-"+node.data.key+"'"+
                                          ',event,'+"'myMenuSingle'"+')" style="display:none;cursor:pointer;"><span class="glyphicon glyphicon-chevron-down"></span></a>');
                                }
                            }
                        }, function(){
                            var node = $.ui.dynatree.getNode(this);
                            if($('#context_menu_'+node.data.key).length==0){
                                if (n > 0) {// se for propriedade de objeto
                                    values = key.split("_");
                                    if (values[1] === 'tag' || (values[1] === 'facet' && values[2] === 'tag')) {
                                        $(node.span).append('<a id="context_menu_'+node.data.key+'" onclick="triggerContextMenu('+"'#ui-dynatree-id-"+node.data.key+"'"+
                                               ',event,'+"'myMenuSingleTag'"+')" style="display:none;cursor:pointer;"><span class="glyphicon glyphicon-chevron-down"></span></a>');
                                    } else if (values[1] === 'facet' && values[2] === 'category') {
                                        $(node.span).append('<a id="context_menu_'+node.data.key+'" onclick="triggerContextMenu('+"'#ui-dynatree-id-"+node.data.key+"'"+
                                               ',event,'+"'myMenuSingle'"+')" style="display:none;cursor:pointer;"><span class="glyphicon glyphicon-chevron-down"></span></a>');
                                    }
                                } else {
                                    $(node.span).append('<a id="context_menu_'+node.data.key+'" onclick="triggerContextMenu('+"'#ui-dynatree-id-"+node.data.key+"'"+
                                          ',event,'+"'myMenuSingle'"+')" style="display:none;cursor:pointer;"><span class="glyphicon glyphicon-chevron-down"></span></a>');
                                }
                            }
                        });
                    }
                });
            },
            onClick: function (node, event) {
                var item_title = node.data.title;
                var item_id = parseInt(node.data.key);
                logColAction(item_title, item_id);
                // Close menu on click
                if ($(".contextMenu:visible").length > 0) {
                    $(".contextMenu").hide();
                }
                //verifico aonde esta clicando
                if($('#visualization_page_category').val()==='click' &&
                        ((event.srcElement && event.srcElement.className==='dynatree-title') || (event.target && event.target.className==='dynatree-title'))){
                    // Close menu on click
                    $('#modalImportMain').modal('show');
                    // Close menu on click
                    var promisse = get_url_category(node.data.key);
                    promisse.done(function (result) {
                        elem = jQuery.parseJSON(result);
                        $('#modalImportMain').modal('hide');
                        var n = node.data.key.toString().indexOf("_");
                        if(node.data.key.indexOf('_tag')>=0){
                            showPageTags(elem.slug, src);
                            node.deactivate();
                        }else if(n<0||node.data.key.indexOf('_facet_category')>=0){
                            showPageCategories(elem.slug, src);
                            node.deactivate();
                        }
                    });
                }else if($('#visualization_page_category').val()!=='click' &&
                        ((event.srcElement && event.srcElement.className==='dynatree-title') || (event.target && event.target.className==='dynatree-title'))){
                    if(node.data.key.indexOf("_facet_")>=0){
                        return false;
                    }
                    //seleciono ou unselect
                     var selKeys = $.map(node.tree.getSelectedNodes(), function (tnode) {
                        return tnode.data.key;
                    });
                    //get_categories_properties_ordenation();s
                    if(selKeys.indexOf(node.data.key)<0 ||
                            ( (node.data.key.indexOf("_moreoptions")>=0 || node.data.key.indexOf("alphabet")>=0) ) && (select === false || select === 0 ) ){
                        select = true;
                        node.select(true);
                        if((node.data.key.indexOf("_moreoptions")>=0 || node.data.key.indexOf("alphabet")>=0)){
                            node.expand(true);
                        }
                    }else{
                        select = false;
                        node.select(false);
                        node.deactivate();
                        if((node.data.key.indexOf("_moreoptions")>=0 || node.data.key.indexOf("alphabet")>=0)){
                            node.expand(false);
                        }
                    }
                }
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
               //console.log(decodeEntities(node.data.title));
               $(node.span).find('.dynatree-title').html(decodeEntities(node.data.title));
                /*if(!$('body').hasClass('logged-in'))
                {
                    return false;
                } */
                var key = node.data.key;
                $(span).attr('id',"ui-dynatree-id-" + node.data.key)
                n = key.toString().indexOf("_");
                if (n > 0) {// se for propriedade de objeto
                    values = key.split("_");
                    if (values[1] === 'tag' || (values[1] === 'facet' && values[2] === 'tag')) {
                         $(span).attr('onmouseout',"hideContextMenu('#context_menu_"+node.data.key+"')");
                        $(span).attr('onmouseover',"showContextMenu('#context_menu_"+node.data.key+"')");
                        $(span).append('<a id="context_menu_'+node.data.key+'" onclick="triggerContextMenu('+"'#ui-dynatree-id-"+node.data.key+"'"+
                               ',event,'+"'myMenuSingleTag'"+')" style="display:none;cursor:pointer;"><span class="glyphicon glyphicon-chevron-down"></span></a>');
                        bindContextMenuSingleTag(span);
                    } else if (values[1] === 'facet' && values[2] === 'category') {
                        $(span).attr('onmouseout',"hideContextMenu('#context_menu_"+node.data.key+"')");
                        $(span).attr('onmouseover',"showContextMenu('#context_menu_"+node.data.key+"')");
                        $(span).append('<a id="context_menu_'+node.data.key+'" onclick="triggerContextMenu('+"'#ui-dynatree-id-"+node.data.key+"'"+
                               ',event,'+"'myMenuSingle'"+')" style="display:none;cursor:pointer;"><span class="glyphicon glyphicon-chevron-down"></span></a>');
                        bindContextMenuSingle(span);

                    }
                } else {
                    bindContextMenuSingle(span);
                    $(span).attr('onmouseout',"hideContextMenu('#context_menu_"+node.data.key+"')");
                    $(span).attr('onmouseover',"showContextMenu('#context_menu_"+node.data.key+"')");
                    $(span).append('<a id="context_menu_'+node.data.key+'" onclick="triggerContextMenu('+"'#ui-dynatree-id-"+node.data.key+"'"+
                          ',event,'+"'myMenuSingle'"+')" style="display:none;cursor:pointer;"><span class="glyphicon glyphicon-chevron-down"></span></a>');
                }
                Hook.call('tainacan_oncreate_main_dynatree',[node]);
                $('.dropdown-toggle').dropdown();
            },
            onPostInit: function (isReloading, isError) {
                //$('#parentCat').val("Nenhum");
                $('#parentId').val("");
                $("ul.dynatree-container").css('border', "none");
                //$( "#btnExpandAll" ).trigger( "click" );
            },
            onExpand: function (flag, node) {
                var key = node.data.key;
                $(node.span).attr('id',"ui-dynatree-id-" + node.data.key)
                var n = key.toString().indexOf("_");
                if($('#context_menu_'+node.data.key).length==0){
                    if (n > 0) {// se for propriedade de objeto
                        values = key.split("_");
                        if (values[1] === 'tag' || (values[1] === 'facet' && values[2] === 'tag')) {
                            $(node.span).append('<a id="context_menu_'+node.data.key+'" onclick="triggerContextMenu('+"'#ui-dynatree-id-"+node.data.key+"'"+
                                   ',event,'+"'myMenuSingleTag'"+')" style="display:none;cursor:pointer;"><span class="glyphicon glyphicon-chevron-down"></span></a>');
                        } else if (values[1] === 'facet' && values[2] === 'category') {
                            $(node.span).append('<a id="context_menu_'+node.data.key+'" onclick="triggerContextMenu('+"'#ui-dynatree-id-"+node.data.key+"'"+
                                   ',event,'+"'myMenuSingle'"+')" style="display:none;cursor:pointer;"><span class="glyphicon glyphicon-chevron-down"></span></a>');
                        }
                    } else {
                        $(node.span).append('<a id="context_menu_'+node.data.key+'" onclick="triggerContextMenu('+"'#ui-dynatree-id-"+node.data.key+"'"+
                              ',event,'+"'myMenuSingle'"+')" style="display:none;cursor:pointer;"><span class="glyphicon glyphicon-chevron-down"></span></a>');
                    }
                }
            },
            onSelect: function (flag, node) {
//                    if($('#visualization_page_category').val()!=='click'){
//                       if(select===true){
//                           node.select(true);
//                       }else if(select===false){
//                           node.select(false);
//                       }
//                    }
                    var selKeys = $.map(node.tree.getSelectedNodes(), function (node) {
                        if(node.data.key.indexOf("_facet_")>=0||node.data.key.indexOf("_moreoptions")>=0||node.data.key.indexOf("alphabet")>=0){
                            //continue
                        }else{
                            return node.data.key;
                        }
                    });
                   //
                    //get_categories_properties_ordenation();s
                    if($('#flag_dynatree_ajax').val()==='true'
                            &&node.data.key.indexOf("_moreoptions")<0&&node.data.key.indexOf("alphabet")<0) {
                        var node_values = selKeys.join(", ");
                        wpquery_filter_by_facet( node_values, "", "wpquery_dynatree");
                    }
                    //REMOVENDO AS SELECOES ABAIXO DO PAI
                    if(selKeys.indexOf(node.data.key)>=0){
                        unselect_children(node);
                    }
                    // lanco um hook para ser usada ao selecionar um item no dynatree
                    if (Hook.is_register('tainacan_onselect_dynatree')) {
                        Hook.call('tainacan_onselect_dynatree', [selKeys]);
                    }
                //list_all_objects(selKeys.join(", "), $("#collection_id").val(), $('#collection_single_ordenation').val(), '', $("#value_search").val())
            },
            dnd: {
                preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
                revert: false, // true: slide helper back to source if drop is rejected
                onDragStart: function (node) {
                    /** This function MUST be defined to enable dragging for the tree.*/

                    // logMsg("tree.onDragStart(%o)", node);
                    if (node.data.isFolder) {
                        return false;
                    }
                    return true;
                },
                onDragStop: function (node) {
                    //
                    // logMsg("tree.onDragStop(%o)", node);
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

    /**
     *
     * @returns {undefined}     */
    function showContextMenu(object){
         $(object).show();
    }
    /**
     *
     * @returns {undefined}     */
    function hideContextMenu(object){
         $(object).hide();
    }
    /**
    *
     * @param {type} span
     * @param {type} event
     * @returns {undefined}     */
    function triggerContextMenu(span,event,menu){
         $(span).triggerContextMenu(event,span,menu,function (action, el, pos) {
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
                        if(node.data.key.indexOf('_tag')>=0){
                            showPageTags(elem.slug, src);
                            node.deactivate();
                        }else if(n<0||node.data.key.indexOf('_facet_category')>=0){
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
                            //$("#category_single_add_dynatree_id").val(dynatree_id);
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
                            if (menu) {
                                $("#category_single_edit_dynatree_id").val(menu);
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
                            //$("#category_single_delete_dynatree_id").val(dynatree_id);
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
     * funcao que gera o context menu para o dynatree apos cinco segundos
     * @param {type} property_id
     * @returns {undefined}
     */
    function addHoverToNodes() {
        var root = $("#dynatree").dynatree("getRoot");
        root.visit(function (node, unused) {
            $("#ui-dynatree-id-" + node.data.key).hover(function () {

                $(this).data('hover-dynatree', window.setTimeout(function (){
                     $("#ui-dynatree-id-" + node.data.key).trigger("mousedown", {
                                    pageX: 50,
                                    pageY: 50,
                                    button: 3
                                });

                }, 2000));
            },
            function ()
            {
                clearTimeout($(this).data('hover-dynatree'));
            });
        }, 0, false);
     }

    function autocomplete_menu_left(property_id) {
        $("#autocomplete_multipleselect_" + property_id).autocomplete({
            source: $('#src').val() + '/controllers/collection/collection_controller.php?operation=list_items_search_autocomplete&is_search=true&property_id=' + property_id + '&collection_id='+$('#collection_id').val(),
            messages: {
                noResults: '',
                results: function () { }
            },
            minLength: 2,
            select: function (event, ui) {
                $("#autocomplete_multipleselect_" + property_id).html('');
                $("#autocomplete_multipleselect_" + property_id).val('');
                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                var temp = $("#multipleselect_value_" + property_id + " [value='" + ui.item.value + "']").val();
                if (typeof temp == "undefined") {
                    $("#multipleselect_value_" + property_id ).append("<option onclick='clear_autocomplete_menu_left(this,"+property_id+")' value='" + ui.item.value + "' id='option_"+property_id+"_"+ui.item.value.replace(/\s+/, "") +"' selected='selected' >" + ui.item.label + "</option>");
                     wpquery_multipleselect(property_id, "multipleselect_value_" + property_id);
                }
                setTimeout(function () {
                    $("#autocomplete_multipleselect_" + property_id).val('');
                }, 100);
            }
        });
    }

    function clear_autocomplete_menu_left(e,facet_id) {
         $(e).remove();
         wpquery_multipleselect(facet_id, "multipleselect_value_" + facet_id);
    }

    function list_events_filters(){
        $.ajax({
            url: $('#src').val() + '/controllers/search/search_controller.php',
            type: 'POST',
            data: {operation: 'get_events_data',collection_id: $("#collection_id").val()}
        }).done(function (result) {
            $('#notifications_filter').html(result);
        });
    }

    /*********************************************************************/
    function findCSSTags( css_source ) {
        var tagPattern = /\[\[.+?\]\]/gi;
        var tagsFound = {};

        while((n = tagPattern.exec( css_source ) ) != null) {
            var tag = n[0].match(/\[\[\s*(\w+)/i);
            var value = n[0].match(/:\s*([\w#]+)\s*\]\]/i);
            tag = tag[1];
            if(value) {
                value = value[1];
            }
            tagsFound[tag] = value;
        }
        if(css_source.match(/\.align-center/i)) {
            tagsFound['menu_align'] = "left";
            tagsFound['menu_align_center'] = "";
        }
        if(css_source.match(/\.align-right/i)) {
            tagsFound['menu_align'] = "left";
            tagsFound['menu_align_right'] = "";
        }
        return tagsFound;
    }

    function activeFacetAccordion() {
        return ( $("#accordion .form-group").length == 1 ) ? 0 : false;
    }

    $("#accordion").accordion({
        collapsible: true,
        header: "label",
        animate: 200,
        heightStyle: "content",
        icons: false
    });

    if(!isMobile())
    {
        $('#accordion .ui-accordion-content').show();
    }

    $('.expand-all').toggle(function() {
        setMenuContainerHeight();

        $(this).find("div.action-text").text( '<?php _e('Expand all', 'tainacan') ?>' );
        $('#accordion .ui-accordion-content').fadeOut();
        $('.prepend-filter-label').switchClass('glyphicon-triangle-bottom','glyphicon-triangle-right');
        $(this).find('span').switchClass('glyphicon-triangle-bottom','glyphicon-triangle-right');
        //$('.cloud_label').click();
    }, function() {
        $('#accordion .ui-accordion-content').fadeIn();
        $('.prepend-filter-label').switchClass('glyphicon-triangle-right', 'glyphicon-triangle-bottom');
        $(this).find('span').switchClass('glyphicon-triangle-right', 'glyphicon-triangle-bottom');
        //$('.cloud_label').click();
        $(this).find("div.action-text").text( '<?php _e('Collapse all', 'tainacan') ?>' );
    });

    var icon_html = "<span class='prepend-filter-label glyphicon-triangle-bottom blue glyphicon sec-color'></span>";
    $('label.title-pipe').each(function(idx, el) {
       $(el).prepend(icon_html);
    });
//####################### DEMARCANDO RECURSIVAMENTE ############################
function unselect_children(node){
    if(node.childList){
        $.each(node.childList,function(index,node2){
            node2.select(false);
            unselect_children(node2);
        });
    }
}

$.ui.plugin.add("resizable", "alsoResizeReverse", {
    start: function() {
        var res = $(this).resizable("instance"),
            opts = res.options;

        $(opts.alsoResizeReverse).each(function() {
            var el = $(this);
            el.data("ui-resizable-alsoresizeReverse", {
                width: parseInt(el.width(), 10), height: parseInt(el.height(), 10),
                left: parseInt(el.css("left"), 10), top: parseInt(el.css("top"), 10)
            });
        });
    },

    resize: function(event, ui) {
        var that = $(this).resizable( "instance" ),
            o = that.options,
            os = that.originalSize,
            op = that.originalPosition,
            delta = {
                height: (that.size.height - os.height) || 0,
                width: (that.size.width - os.width) || 0,
                top: (that.position.top - op.top) || 0,
                left: (that.position.left - op.left) || 0
            };

        $(o.alsoResizeReverse).each(function() {
            var el = $(this), start = $(this).data("ui-resizable-alsoresize-reverse"), style = {},
                css = el.parents(ui.originalElement[0]).length ? [ "width", "height" ] :
                    [ "width", "height", "top", "left" ];

            $.each(css, function(i, prop) {
                var sum = (start[prop] || 0) - (delta[prop] || 0);
                if (sum && sum >= 0) {
                    style[prop] = sum || null;
                }
            });

            el.css(style);
        });
    },

    stop: function() {
        $(this).removeData("resizable-alsoresize-reverse");
    }
});
$(function() {
    $("#div_left").resizable({
        maxWidth: 650,
        minWidth: 200,
        alsoResizeReverse: "#div_central"
    });

    if(isMobile())
    {
        $('.expand-all').click();
    }
});
</script>
