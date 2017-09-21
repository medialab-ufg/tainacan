<?php ?>
<script>
    $(function () {
        $("#table_search_data_id").rowSorter({
            handler: "span.sort-handler",
            onDrop: function (tbody, row, index, oldIndex) {
                //$(tbody).parent().find("tfoot > tr > td").html((oldIndex + 1) + ". foi movida para a posicao " + (index + 1));
                renumber_table_horizontal('#table_search_data_id');
            }
        });

        $("#table_search_data_left_column_id").rowSorter({
            handler: "span.sort-handler",
            onDrop: function (tbody, row, index, oldIndex) {
                //$(tbody).parent().find("tfoot > tr > td").html((oldIndex + 1) + ". foi movida para a posicao " + (index + 1));
                renumber_table_left('#table_search_data_left_column_id');
            }
        });

        $("#table_search_data_right_column_id").rowSorter({
            handler: "span.sort-handler",
            onDrop: function (tbody, row, index, oldIndex) {
                //$(tbody).parent().find("tfoot > tr > td").html((oldIndex + 1) + ". foi movida para a posicao " + (index + 1));
                renumber_table_right('#table_search_data_right_column_id');
            }
        });

        list_facets();
        list_ordenation();
        list_properties_data_ordenation();
        list_properties_data_selected_ordenation();
        get_widgets('#search_add_facet');
        if ($('#open_wizard').val() == 'true') {
            $('#btn_back_collection').hide();
            $('#submit_configuration').hide();
            $('#personalize_search').hide();
        }
        else {
            $('#MyWizard').hide();
            $('#search_create_opt').hide();
            $('#save_and_next').hide();
            $('#personalize_search').show();
        }
        var src = $('#src').val();

        //formulario de submissao
        $('#submit_form_search_data').submit(function (e) {
            var form_data = $(this).serialize();

            e.preventDefault();
            $.ajax({
                url: src + '/controllers/search/search_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                list_facets();
                elem = jQuery.parseJSON(result);
                showAlertGeneral(elem.title, elem.msg, elem.type);
                setTimeout(function () {
                    renumber_all();
                }, 500);
                if (elem.result == 'true') {
                    showSearchConfiguration($('#src').val());
                    set_containers_class($('#collection_id').val());
                }
                $('.dropdown-toggle').dropdown();
            });
            e.preventDefault();
        });

        //formulario de submissao
        $('#form_ordenation_search').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: src + '/controllers/search/search_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                showAlertGeneral(elem.title, elem.msg, elem.type);
                $('.dropdown-toggle').dropdown();
            });
            e.preventDefault();
        });
    });

    function renumber_all() {
        renumber_table_left('#table_search_data_left_column_id');
    }
    

    //Renumber table rows left
    function renumber_table_left(tableID) {
        var src = $('#src').val();
        var arrFacets = [];
        $(tableID + " tr").each(function () {
            count = $(this).parent().children().index($(this)) + 1;
            var input_id = $(this).find("input[class='find_facet']").attr('id') + '';
            if (input_id != 'undefined') {
                var facet_id = input_id.split('_')[1];
                arrFacets.push([facet_id, count]);
                var html_insert = count + "<input class='find_facet' type='hidden' id='position_" + facet_id + "' value='" + facet_id + "_" + count + "' />";
                $(this).find('.priority-left').html(html_insert);
            }
        });

        $.ajax({
            url: src + '/controllers/search/search_controller.php',
            type: 'POST',
            data: {arrFacets: arrFacets, operation: 'save_new_priority', collection_id: $('#collection_id').val()}
        }).done(function (result) {
            $('.dropdown-toggle').dropdown();
        });
    }
    

    function list_ordenation() {
        $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: {operation: 'list_ordenation', collection_id: $("#collection_id").val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if (elem.general_ordenation) {
                $("#collection_order").append("<optgroup label='<?php _e('General ordenation','tainacan') ?>'>");
                $.each(elem.general_ordenation, function (idx, general) {
                    if (general && general !== false) {
                        $("#collection_order").append("<option value='" + general.id + "' selected='selected' >" + general.name + "</option>");
                    }
                });
            }
            if (elem.property_data) {
                $("#collection_order").append("<optgroup label='<?php _e('Data properties','tainacan') ?>'>");
                $.each(elem.property_data, function (idx, data) {
                    if (data && data !== false) {
                        $("#collection_order").append("<option value='" + data.id + "' selected='selected' >" + data.name + " - ( <?php _e('Type','tainacan') ?>:"+data.type+" ) </option>");
                    }
                });
            }
            if (elem.rankings) {
                $("#collection_order").append("<optgroup label='<?php _e('Rankings','tainacan') ?>'>");
                $.each(elem.rankings, function (idx, ranking) {
                    if (ranking && ranking !== false) {
                        $("#collection_order").append("<option value='" + ranking.id + "' selected='selected' >" + ranking.name + "  - ( <?php _e('Type','tainacan') ?>:"+ranking.type+" ) </option>");
                    }
                });
            }
            if (elem.selected) {
                $("#collection_order").val(elem.selected);
            }
            $('.dropdown-toggle').dropdown();
        });
    }

    function list_facets() {
        var src = $('#src').val();

        $.ajax({
            url: src + '/controllers/search/search_controller.php',
            type: 'POST',
            data: {operation: 'list_facets', collection_id: $('#collection_id').val()},
            success: function (data) {
                if (data) {
                    var jsonObject = jQuery.parseJSON(data);
                    if (jsonObject && jsonObject != null) {
                        $("#table_search_data").html('');
                        $("#table_search_data_left_column").html('');
                        $("#table_search_data_right_column").html('');
                        $.each(jsonObject, function (id, object) {
                            //if(object.nome == null) object.nome = 'Tag';
                            if (object.orientation == 'horizontal') {
                                if (object.nome != null) {
                                    $("#table_search_data").append("<tr><td>" + object.nome + "</td>" +
                                            "<td>" + object.widget + "</td>" +
                                            "<td><a onclick='editFacet(\"" + object.id + "\")' href='#submit_form_search_data'><span class='glyphicon glyphicon-pencil'></span></a></td>" +
                                            "<td><a onclick='deleteFacet(\"" + object.id + "\")' href='#submit_form_search_data'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                            "<td><a href='#'><span class='glyphicon glyphicon-sort sort-handler'></span></a></td>" +
                                            "<td class='priority-horizontal'>" + object.priority + "<input class='find_facet' type='hidden' id='position_" + object.id + "' value='" + object.id + "_" + object.priority + "' /></td>" +
                                            "</tr>");
                                }
                            } else if (object.orientation == 'left-column') {
                                if (object.nome != null) {
                                    $("#table_search_data_left_column").append("<tr><td>" + object.nome + "</td>" +
                                            "<td>" + object.widget + "</td>" +
                                            "<td><a onclick='editFacet(\"" + object.id + "\")' href='#submit_form_search_data'><span class='glyphicon glyphicon-pencil'></span></a></td>" +
                                            "<td><a onclick='deleteFacet(\"" + object.id + "\")' href='#submit_form_search_data'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                            "<td><a href='#'><span class='glyphicon glyphicon-sort sort-handler'></span></a></td>" +
                                            "<td class='priority-left'>" + object.priority + "<input class='find_facet' type='hidden' id='position_" + object.id + "' value='" + object.id + "_" + object.priority + "' /></td>" +
                                            "</tr>");
                                }
                            } else if (object.orientation == 'right-column') {
                                if (object.nome != null) {
                                    $("#table_search_data_right_column").append("<tr><td>" + object.nome + "</td>" +
                                            "<td>" + object.widget + "</td>" +
                                            "<td><a onclick='editFacet(\"" + object.id + "\")' href='#submit_form_search_data'><span class='glyphicon glyphicon-pencil'></span></a></td>" +
                                            "<td><a onclick='deleteFacet(\"" + object.id + "\")' href='#submit_form_search_data'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                            "<td><a href='#'><span class='glyphicon glyphicon-sort sort-handler'></span></a></td>" +
                                            "<td class='priority-right'>" + object.priority + "<input class='find_facet' type='hidden' id='position_" + object.id + "' value='" + object.id + "_" + object.priority + "' /></td>" +
                                            "</tr>");
                                }
                            }
                        });
                        $("#table_search_data").show();
                        $("#table_search_data_left_column").show();
                        $("#table_search_data_right_column").show();
                    }
                } // caso o controller retorne false
            }
        });// fim da inclusão

    }

    function editFacet(id) {
        $('#operation_search_data').val('update');
        $('#property_id').val(id);
        $('#color_field_property_search').hide();
        $('#color_field_search').hide();
        $('#search_add_facet').val(id);
        $('#search_add_facet option:eq('+id+')').attr('selected', 'selected');
        $('#search_add_facet').attr("disabled", "disabled");
        $('#btn_add_new').show();
        get_widgets('#search_add_facet');

        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/search/search_controller.php",
            data: {operation: 'fill_edit_form', property_id: id, collection_id: $('#collection_id').val()}
        }).done(function (result) {

            $('.dropdown-toggle').dropdown();
            elem = jQuery.parseJSON(result);
            $('[value=' + elem.widget + ']').attr("selected", "selected");
            if (elem.widget === 'tree'&&elem.property_id!=='tag') {
                $('#orientation_field').hide();
                $('#color_field_search').show();
                $('#' + elem.class_color).attr("checked", "checked");
                $('[name=color_facet]').attr("required", "required");
            } else if (elem.widget === 'range') {
                $('#orientation_field').show();
                $('#color_field_search').hide();
                $('#search_data_orientation').val(elem.orientation);
                $('#range_form').html('');
                $('#range_submit').show();
                var counter = 0;
                $('#counter_range').val(counter);
                $.each(elem.range_options, function (key, value) {
                    append_range();
                    $('#range_' + $('#counter_range').val() + '_1').val(value.value_1);
                    $('#range_' + $('#counter_range').val() + '_2').val(value.value_2);
                });
            } else if( elem.widget === 'menu' ) {
                $("#select_menu_style").show();
                // Adds previously selected menu style to facet edit
                showOrientationStyles();
                $('.select2-menu').select2('data', { id: elem.chosen_menu_style_id, text: " (Estilo selecionado)" } );
            }else if( elem.property_id==='tag' ) {
                $('#orientation_field').hide();
                $('#color_field_search').hide();
                $("#select_menu_style").hide();
            } else {
                $('#orientation_field').show();
                $('#color_field_search').hide();
                $("#select_menu_style").hide();
                $('#search_data_orientation').val(elem.orientation);
            }
        });
    }

    function deleteFacet(id) {
        swal({
            title: '<?php _e('Attention!','tainacan'); ?>',
            text: '<?php _e('Are you sure?','tainacan'); ?>',
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
                    url: $('#src').val() + "/controllers/search/search_controller.php",
                    data: {
                        operation: 'delete_facet',
                        facet_id: id,
                        collection_id: $('#collection_id').val()
                    }
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    showAlertGeneral(elem.title, elem.msg, elem.type);
                    $('#operation_search_data').val('add');
                    list_facets();
                    setTimeout(function () {
                        renumber_all();
                    }, 500);
                    showSearchConfiguration($('#src').val());
                    set_containers_class($('#collection_id').val());
                });
            }
        });
    }

    function showPersonalizeSearch() {
        $("#show_search_link").hide('slow');
        $("#hide_search_link").show('slow');
        $(".categories_menu").show('slow');
    }

    function hidePersonalizeSearch() {
        $("#personalize_search").hide('slow');
        $("#hide_search_link").hide('slow');
        $("#show_search_link").show('slow');
    }

    function nextStep() {
        //$('#configuration').hide();
        //$('#configuration').html('');
        showDesignConfiguration('<?php echo get_template_directory_uri() ?>');
    }

    function get_widgets(selector) {
        $('#range_submit').hide();
        $('#search_data_widget').html('');
        $.ajax({
            type: "POST",
            async: false,
            url: $('#src').val() + "/controllers/search/search_controller.php",
            data: {operation: 'get_widgets', property_id: $(selector).val()}
        }).done(function (result) {
            $('.dropdown-toggle').dropdown();
            elem = jQuery.parseJSON(result);
            if (elem.select) {
                $.each(elem.select, function (key, value) {
                    if (key === '0') {
                        $('#search_data_widget').append('<option value="">' + value + '</option>');
                    } else {
                        $('#search_data_widget').append('<option value="' + key + '">' + value + '</option>');
                    }
                });
            }
        });
    }

    // Loads Selected Menu style
   function load_menu_assets(src, menu_style_id) {
       $.ajax({
           type: "POST",
           async: false,
           url: src + "/controllers/search/search_controller.php",
           data: { operation: 'get_menu_style', menu_style_id: menu_style_id }
       }).error( function(){
           $("#menu_style_container").html('Error loading styles. Try again later.');
       }).done(function(result){
           $("#menu_style_container").html( result );
       });
   }

    function load_menu_style_data(src) {
        $.ajax({
            type: "POST",
            async: false,
            url: src + "/controllers/search/search_controller.php",
            data: { operation: 'load_menu_style_data' , menu_property: "terms" }
        }).error(function() {
            alert('<?php _e("Something went wrong. Try again later.", "tainacan") ?>');
        }).done(function(result) {
            var menu_item = $.parseJSON(result);
            $(menu_item).each( function(index, item) {
                var terms = item.terms;
                $("select#select_menu_style").append('<option value="menu_style_'+ item.id + '" id="menu_style_'+ item.id +'"> #' + item.id + '</option>');
                terms.map( function(class_name) {
                    $("select#select_menu_style option#menu_style_" + item.id).addClass(class_name);
                });
            });
        });
    }

    // esconde a orientacao para os tree
    function hide_orientation(selector,property_id) {
         $.ajax({
            type: "POST",
            async: false,
            url: $('#src').val() + "/controllers/search/search_controller.php",
            data: {operation: 'get_widget_tree_type', property_id: property_id}
        }).done(function (result) {
            var res = result;
            $('.dropdown-toggle').dropdown();
            if ($(selector).val() === 'tree' && res.trim()=='property_term') {
                $('#orientation_field').hide();
                $('#color_field_property_search').hide();
                $('#color_field_search').show();
                $('#select_menu_style').hide();
                $('[name=color_facet]').attr("required", "required");
            }else if($(selector).val() === 'tree' && res.trim()=='property_object'){
                $('#orientation_field').hide();
               // $('#color_field_search').hide();
                $('#select_menu_style').hide();
                 $('#color_field_property_search').hide();
                $('#color_field_search').show();
                $('[name=color_facet]').attr("required", "required");
            }else if(res.trim()==='tag'){
                $('#orientation_field').hide();
                $('#color_field_search').hide();
                $('#color_field_property_search').hide();
                $('#select_menu_style').hide();
            } else if ( $(selector).val() === 'menu' ) {
                $('#select_menu_style').fadeIn();
                showOrientationStyles();
                $('#color_field_search').hide();
            } else {
                $('#color_field_search').hide();
                $('#orientation_field').show();
                $('#color_field_property_search').hide();
                $('#select_menu_style').hide();
                $('[name=color_facet]').removeAttr("required");
            }

            if ($(selector).val() === 'range') {
                $('#range_submit').show();
                var counter = 1;
                $('#counter_range').val(counter);
                append_range();
            } else {
                $('#range_form').html('');
                $('#range_submit').hide();
            }
        });
        
    }

    // da um apende no range
    function  append_range() {
        var count = $('#counter_range').val();
        $('#counter_range').val(parseInt(count) + 1);
        $.ajax({
            type: "POST",
            async: false,
            url: $('#src').val() + "/controllers/search/search_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                facet_id: $('#search_add_facet').val(),
                counter: $('#counter_range').val(),
                operation: 'append_range'}
        }).done(function (result) {
            $('#range_form').append(result);
        });
    }

    function save_widget_tree(tree_type) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/search/search_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                tree_type: $(tree_type).val(),
                operation: 'save_default_widget_tree'}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            showAlertGeneral(elem.title, elem.msg, elem.type);
        });
    }

    function save_widget_tree_orientation(orientation_type) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/search/search_controller.php",
            data: {
                collection_id: $('#collection_id').val(),
                orientation_type: $(orientation_type).val(),
                operation: 'save_default_widget_tree_orientation'}
        }).done(function (result) {
            list_facets();
            elem = jQuery.parseJSON(result);
            showAlertGeneral(elem.title, elem.msg, elem.type);
            set_containers_class($('#collection_id').val());
            setTimeout(function () {
                renumber_all();
            }, 500);
        });
    }
    // ordenation functions
    function list_properties_data_ordenation(){
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/property/property_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'list_property_data', category_id: $('#property_category_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if (elem.no_properties !== true) {
                $('#collection_order_properties').html('');
                $.each(elem.property_data, function (idx, property) {
                    //if(!property.metas.is_repository_property&&property.metas.socialdb_property_created_category==elem.category.term_id){ 
                    //if(property.metas.is_repository_property||property.metas.socialdb_property_created_category==elem.category.term_id){ 
                    //if(!property.metas.is_repository_property){ 
                    $('#collection_order_properties').append('<option value="'+property.id+'">' + property.name + ' (<?php _e('Type','tainacan') ?>:'+property.type+')</option>');
                    //}
                });

            } else {
                $('#collection_order_properties').html('');
                $('#collection_order_properties').append('<option value="">' + '<?php _e('No data properties inserted','tainacan') ?>' + '</option>');
            }
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
    
    function list_properties_data_selected_ordenation(){
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/property/property_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'list_property_data', category_id: $('#property_category_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if (elem.no_properties !== true) {
                $('#collection_order_selected_properties').html('');
                $.each(elem.property_data, function (idx, property) {
                    if(property.metas.socialdb_property_data_column_ordenation&&property.metas.socialdb_property_data_column_ordenation==='true'){
                       $('#collection_order_selected_properties').append('<option selected="selected" value="'+property.id+'">' + property.name + ' (<?php _e('Type','tainacan') ?>:'+property.type+')</option>');
                    }
                });
            } else {
                $('#collection_order_selected_properties').html('');
                $('#collection_order_selected_properties').append('<option value="">' + '<?php _e('No data properties inserted','tainacan') ?>' + '</option>');
            }
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
    
    function remove_property_ordenation(e){
          if($(e).val()){
             $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/search/search_controller.php",
                data: {
                    collection_id: $('#collection_id').val(),
                    property_id: $(e).val(),
                    operation: 'remove_property_ordenation'}
            }).done(function (result) {
                $('#collection_order').html('');
                list_ordenation();
                list_properties_data_ordenation();
                list_properties_data_selected_ordenation();
                elem = jQuery.parseJSON(result);
                showAlertGeneral(elem.title, elem.msg, elem.type);
            });
        }
    }
    
    function add_property_ordenation(){
        if($('#collection_order_properties').val()){
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/search/search_controller.php",
                data: {
                    collection_id: $('#collection_id').val(),
                    property_id: $('#collection_order_properties').val(),
                    operation: 'add_property_ordenation'}
            }).done(function (result) {
                $('#collection_order').html('');
                list_ordenation();
                list_properties_data_ordenation();
                list_properties_data_selected_ordenation();
                elem = jQuery.parseJSON(result);
                showAlertGeneral(elem.title, elem.msg, elem.type);
            });
        }
    }

    function get_menu_property(property) {
        var url = '<?php echo get_template_directory_uri() ?>' + "/controllers/search/search_controller.php";
        $.ajax({
            type: "POST",
            url: url,
            data: { operation: 'get_item_property', property: property }
        }).done( function (result) {
            var obj = $.parseJSON(result);
            $(obj).each( function(idx, el) {
                var m = $("option#menu_style_" + el.id);
                var item_classes = (el.terms).join(' ');
                $(m).addClass( item_classes );
            });
        });
    }
    get_menu_property('terms');

    function showOrientationStyles() {
        var orientation_class = $("#search_data_orientation option:selected").attr('class');
        $("#select_menu_style option").each(function(idx, el){
            var item_classes = $(el).attr('class');
            var filter = "";
            if ( null !== (item_classes.match(" ")) ) {
                filter = item_classes.split(" ")[0];
            } else {
                filter = item_classes;
            }

            if ( orientation_class.indexOf(filter) > -1 ) {
                $(el).removeClass('hide-el');
            } else {
                $(el).addClass('hide-el');
                $('.select2-menu').change();
            }
        });
    }

    // Seelct2 Helper function
    function addMenuThumb(item) {
        if ( !item.id ) return item.text;
        var item_id = item.id;
        var f = item_id.replace(/menu_style_/g, "");
        var thumb = '<?php echo get_stylesheet_directory_uri() ?>/extras/cssmenumaker/menus/' + f + '/thumbnail/css_menu_thumb.png';
        return "<span><img src='" + thumb + "' class='img-flag' />" + item.text + "</span>";
    }

    // Formats select menu options to show up it's thumbnail
    $('.select2-menu').select2({
        formatResult: addMenuThumb,
        formatSelection: addMenuThumb,
        escapeMarkup: function(m) { return m; }
    });

</script>
