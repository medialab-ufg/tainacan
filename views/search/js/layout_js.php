<script type="text/javascript">
    var src = $('#src').val();

    $('.custom_color_schemes').on('click', 'a.remove-cs', function() {
        $(this).parents('.color-container').fadeOut(300, function() {
          $(this).remove();
        });
    });

    $(".custom_color_schemes").on('click', '.color-container', function (e) {
        $('.color-container').removeClass('selected');
        $(this).addClass('selected');
        var c1 = $(this).find('.color1').val();
        var c2 = $(this).find('.color2').val();
        colorize("", c1, c2);
    });

    $('form[name="custom_colors"]').submit(function(event) {
        var form = $(this).serialize();
        event.preventDefault();
        $.ajax({ url: src + "/controllers/collection/collection_controller.php", type: 'POST', data: form })
          .done(function(result) {
            location.reload();
              var el = $.parseJSON(result);
          });
    });

    $('#collection_list_mode').change(function() {
        var v_mode = $(this).val();
        var togglable_divs = ['.coordinate', '.prox-container'];

        if( v_mode === 'slideshow') {
            $('.sl-time').fadeIn();
            $(togglable_divs).each(function(ix, el){ $( el).fadeOut(); } );
        } else if( v_mode === 'geolocation') {
            $('.sl-time').fadeOut();
            $('.table-meta-config').fadeOut();
            $($(togglable_divs)).each(function(ix, el){ $( el).fadeIn(); } );
        } else if (v_mode === "table") {
            $('.sl-time').fadeOut();
            $(togglable_divs).each(function(ix, el){ $( el).fadeOut(); } );
            $('.table-meta-config').fadeIn();
        } else {
            togglable_divs.push('.sl-time');
            togglable_divs.push('.table-meta-config');
            $(togglable_divs).each( function(idx, div) {
                $(div).fadeOut();
            });
        }
    });

    $(".prox_mode").change(function() {
        if(this.checked) {
            $('div.location').show();
            $('.coordinate').hide();
        } else {
            $('div.location').hide();
            $('.coordinate').show();
        }
    });

    $(function () {
        change_breadcrumbs_title('<?php _e('Layout','tainacan') ?>');
        get_defaultCS();
        get_colorSchemes();

        var layoutOptions = {
            change: function(event, ui) {
                var curr_id = event.target.id;
                var pickedColor = $("#" + curr_id).val();
                if (curr_id == "primary-custom-color") {
                    $('#tainacan-mini .color1').css('background', pickedColor);
                } else if(curr_id == "second-custom-color") {
                    $('#tainacan-mini .color2').css('background', pickedColor);
                }
            }
        };
        $('#primary-custom-color').wpColorPicker(layoutOptions);
        $('#second-custom-color').wpColorPicker(layoutOptions);

        $("#conclude_config").click(function() {
            goToCollectionHome();
        });  

        if( window.location.search.indexOf('open_wizard') > -1 ) {
            $("#collection-steps").show();
            var stateObj = {clear: "ok"};
            history.replaceState(stateObj, "tainacan", '?');
        } else {
            $("#collection-steps").hide();
        }

        $("#button_save_and_next").click(function(){
            $("#submit_ordenation_form").submit();
            $("#tainacan-breadcrumbs .collection-title").click();
        });

        var selected_view_mode = $('.selected_view_mode').val();
        $("#collection_list_mode").val(selected_view_mode);

        if(selected_view_mode != "geolocation") {
            $('.prox-container').hide();
        }

        if( selected_view_mode == "slideshow") {
            var s_time = $("#slideshow-time").val();
            $('.sl-time select').val(s_time);
            $('.sl-time').show();
        } else if ( selected_view_mode == "geolocation" ) {
            var use_approx_mode = $("#approx_mode").val();
            if(use_approx_mode && use_approx_mode === "use_approx_mode") {
                $('.prox_mode').prop('checked', true);
                $('.coordinate').hide();
                $('.location').show();
            } else if(use_approx_mode === 'false') {
                $('.coordinate').show();
                $('.location').hide();
            }
        } else if ( selected_view_mode == "table") {
            $('.table-meta-config').fadeIn();
        }

        list_ordenation();

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
                showAlertGeneral(elem.title, '<?php _t('The page will be loaded to show the new design!', 1); ?>', 'info');
                setTimeout(function(){
                    window.location = $('#socialdb_permalink_collection').val();
                }, 3000);
            });
        });
    });

    $( function() {
        $( "#sortable" ).sortable();
        $( "#sortable" ).disableSelection();
       // showHabilitateMedia('input[name="socialdb_collection_submission_visualization"]:checked');
      //  showHabilitateItemMedia('input[name="socialdb_collection_item_visualization"]:checked');
    } );

    $("#layout-accordion").accordion({
        collapsible: true,
        header: "h3",
        animate: 200,
        heightStyle: "content"
        // icons: false
    });

    $('#layout-accordion .ui-accordion-content').first().show();

    function appendColorScheme(color1, color2) {
        var c1 = color1 || $("#primary-custom-color").val();
        var c2 = color2 || $("#second-custom-color").val();
        var items_count = $('.custom_color_schemes .color-container').length;

        $('.custom_color_schemes').fadeIn().find(' .color-container').removeClass('selected');

        $('.custom_color_schemes .here').append(
            '<div class="color-container selected"><div class="remove-cS"><a href="javascript:void(0)" class="remove-cs">x</a></div>' +
            '<input type="text" class="color-input color1" style="background:'+c1+'" value="'+c1+'" name="color_scheme['+items_count+'][primary_color]"/> ' +
            '<input type="text" class="color-input color2" style="background:'+c2+'" value="'+c2+'" name="color_scheme['+items_count+'][secondary_color]"/> ' +
            '</div>');
    }

    function get_colorSchemes() {
        var coll_id = $('#collection_id').val();
        $.ajax({
            type: "POST",
            url: src + "/controllers/collection/collection_controller.php",
            data: {operation: 'get_color_schemes', collection_id: coll_id }
        }).done(function(r) {
            var el = $.parseJSON(r);
            $(el).each(function(idx, val) {
                appendColorScheme(val.primary_color, val.secondary_color);
            });
        });
    }

    function get_defaultCS() {
        $.ajax({
            type: "POST",
            url: src + "/controllers/collection/collection_controller.php",
            data: {operation: 'get_default_color_scheme', collection_id: $('#collection_id').val() }
        }).done(function(r) {
            var el = $.parseJSON(r);
            if (el) {
                colorize("", el.primary, el.secondary);
                $("input[type='text'][value='"+el.primary+"']").first().parent().addClass('selected');
            } else {
                colorize("", '#7AA7CF', '#0C698B');
            }
        });
    }

    function colorize(color_name, c1, c2) {
        if (color_name) {
            $('.project-color-schemes').removeClass('selected');
            $('.'+color_name).addClass('selected');
            var cor1 = $('.' + color_name + ' .color1').val();
            var cor2 = $('.' + color_name + ' .color2').val();
        } else {
            var cor1 = c1;
            var cor2 = c2;
        }

        $('#tainacan-mini .color1').css('background', cor1);
        $('#tainacan-mini .color2').css('background', cor2);
        $('a.wp-color-result').first().css('background', cor1);
        $('a.wp-color-result').last().css('background', cor2);
        $("#primary-custom-color").val(cor1);
        $("#second-custom-color").val(cor2);
        $('.default-c1').val(cor1);
        $('.default-c2').val(cor2);

        var dcs = $('.custom_color_schemes .defaults input').serialize();
    }

    function list_properties_data_ordenation() {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/property/property_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'list_property_data', category_id: $('#property_category_id').val()}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            if (elem.no_properties !== true) {
                $('#collection_order_properties').html('');
                $.each(elem.property_data, function (idx, property) {
                    $('#collection_order_properties').append('<option value="'+property.id+'">' + property.name + ' (<?php _e('Type','tainacan') ?>:'+property.type+')</option>');
                });
            }
        });
    }

    function list_ordenation() {
        $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: {operation: 'list_ordenation', collection_id: $("#collection_id").val(), get_all_meta: true}
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            var table_meta_ids = [];
            $('input[name="meta_id_table"]').each(function(idx, el) {
                var valor = $(el).val();
                table_meta_ids.push(valor);
            });

            if (elem.general_ordenation) {
                $("#collection_order").append("<optgroup label='<?php _e('General ordenation','tainacan') ?>'>");
                $.each(elem.general_ordenation, function (idx, general) {
                    if (general && general !== false) {
                        $("#collection_order").append("<option value='" + general.id + "' selected='selected' >" + general.name + "</option>");
                    }
                });
            }
            
            var default_span_html = '<li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>';
            var correct_order = [], default_order = [];
            
            if (elem.property_data)
            {
                $("#collection_order").append("<optgroup label='<?php _e('Data properties','tainacan') ?>'>");
                var iOrder = 0;
                <?php 
                if(has_action('all_properties_to_alter_geolocation_select')):
                ?>
                    $.ajax({
                        type: "POST",
                        url: $('#src').val() + "/controllers/property/property_controller.php",
                        data: {collection_id: $('#collection_id').val(), operation: 'get_properties_categories_dynatree'}
                    }).done(function (result) {
                        elem = JSON.parse(result);
                        $.each(elem, function (idx, data) {
                            if (data && data !== false) {
                                var numeric_id = data.key;
                                var string_id = numeric_id.toString();
                                $("#collection_order").append("<option value='" + data.key + "' selected='selected' >" + data.title + " - ( <?php _e('Type','tainacan') ?>:"+data.type+" ) </option>");                        

                                var curr_meta_index = table_meta_ids.indexOf(string_id);
                                if( curr_meta_index > -1 ) {
                                    var ck = "checked";                                   
                                }

                                var item_info = JSON.stringify({ 'id': data.key, 'order': iOrder, 'tipo': 'property_data'});
                                var sort_meta = default_span_html + "<input type='checkbox' id='table_meta' " + ck + " name='table_meta[]' value='" + item_info + "'> " + data.title + "<br /></li>";

                                if(ck && ck === "checked") {
                                    correct_order[curr_meta_index] = sort_meta;
                                } else {
                                   default_order[iOrder] = sort_meta;
                                }                                               

                                if(data.type === "socialdb_property_data") {
                                    var coords = ["select[name='latitude']","select[name='longitude']","select[name='location']"];
                                    $(coords).each(function(index, e){
                                       $(e).append("<option value='"+ data.key +"'>"+ data.title +"</option>");
                                    });
                                }
                            }
                            iOrder++;
                        });
                        var set_lat  = $("#set-lat").val();
                        var set_long = $("#set-long").val();
                        var location = $("#approx_location").val();
                        if(set_lat && set_long) {
                            $('#latitude_select option[value="'+set_lat+'"]').attr('selected','selected');
                            $('#longitude_select option[value="'+set_long+'"]').attr('selected','selected');
                        }
                    });            
                <?php                
                else:
                ?>
                    $.each(elem.property_data, function (idx, data) {
                        if (data && data !== false) {
                            var numeric_id = data.id;
                            var string_id = numeric_id.toString();
                            $("#collection_order").append("<option value='" + data.id + "' selected='selected' >" + data.name + " - ( <?php _e('Type','tainacan') ?>:"+data.type+" ) </option>");                        

                            var curr_meta_index = table_meta_ids.indexOf(string_id);
                            if( curr_meta_index > -1 ) {
                                var ck = "checked";   
                            }    
                            var item_info = JSON.stringify({ 'id': data.id, 'order': iOrder, 'tipo': 'property_data'});
                            var sort_meta = default_span_html + "<input type='checkbox' id='table_meta' " + ck + " name='table_meta[]' value='" + item_info + "'> " + data.name + "<br /></li>";

                            if(ck && ck === "checked") {
                                correct_order[curr_meta_index] = sort_meta;
                            } else {
                               default_order[iOrder] = sort_meta;
                            }    
                            if(data.type === "text") {
                                var coords = ["select[name='latitude']","select[name='longitude']","select[name='location']"];
                                $(coords).each(function(index, e){
                                   $(e).append("<option value='"+ data.id +"'>"+ data.name +"</option>");
                                });
                            }
                        }
                        iOrder++;
                    });
                    var set_lat  = $("#set-lat").val();
                    var set_long = $("#set-long").val();
                    var location = $("#approx_location").val();
                    if(set_lat && set_long) {
                        $('#latitude_select option[value="'+set_lat+'"]').attr('selected','selected');
                        $('#longitude_select option[value="'+set_long+'"]').attr('selected','selected');
                    }
                <?php                
                endif;
                ?>
            }

            if (elem.property_object) {
                $.each(elem.property_object, function (idx, data) {
                    if (data && data !== false) {
                        var numeric_id = data.id; 
                        
                        var string_id = numeric_id.toString();
                        
                        var curr_meta_index = table_meta_ids.indexOf(string_id);
                        if( curr_meta_index > -1 ) {
                            var ck = "checked";                                   
                        }
                        var item_info = JSON.stringify({ 'id': data.id, 'order': iOrder, 'tipo': 'property_object'});
                        var sort_meta = default_span_html + "<input type='checkbox' id='table_meta' " + ck + " name='table_meta[]' value='" + item_info + "'> " + data.name + "<br /></li>";
                        
                        if(ck && ck === "checked") {
                            correct_order[curr_meta_index] = sort_meta;
                        } else {
                           default_order[iOrder] = sort_meta;
                        }                        
                    }
                    iOrder++;
                });
            }

            if (elem.property_term) {
                $.each(elem.property_term, function (idx, data) {
                    if (data && data !== false) {
                        var numeric_id = data.id; 
                        var string_id = numeric_id.toString();
                        
                        var curr_meta_index = table_meta_ids.indexOf(string_id);
                        if( curr_meta_index > -1 ) {
                            var ck = "checked";                                   
                        }

                        // var sort_meta = '<li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>';
                        var item_info = JSON.stringify({ 'id': data.id, 'order': iOrder, 'tipo': 'property_term'});
                        // sort_meta += "<input type='checkbox' id='table_meta' " + ck + " name='table_meta[]' value='" + item_info + "'> " + data.name + "<br /></li>";
                        
                        var sort_meta = default_span_html + "<input type='checkbox' id='table_meta' " + ck + " name='table_meta[]' value='" + item_info + "'> " + data.name + "<br /></li>";
                        
                        if(ck && ck === "checked") {
                            correct_order[curr_meta_index] = sort_meta;
                        } else {
                           default_order[iOrder] = sort_meta;
                        } 
                    }
                    iOrder++;
                });
            }

            $(correct_order).each(function(idx, el) {
                $(".table-meta-config #sort-metas").append(el);
            });
            $(default_order).each(function(idx, el) {
                $(".table-meta-config #sort-metas").append(el);
            });

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

            $(".table-meta-config #sort-metas").sortable();

//            var set_lat  = $("#set-lat").val();
//            var set_long = $("#set-long").val();
//            var location = $("#approx_location").val();
//            if(set_lat && set_long) {
//                $('#latitude_select option[value="'+set_lat+'"]').attr('selected','selected');
//                $('#longitude_select option[value="'+set_long+'"]').attr('selected','selected');
//            }

            if (location) {
                $(".location select[name='location']").val(location);
            }
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
                    $('#collection_order').append('<option selected="selected" value="'+property.id+'">' + property.name + ' (<?php _e('Type','tainacan') ?>:'+property.type+')</option>');
                });
            } else {
                $('#collection_order_selected_properties')
                  .html('').append('<option value="">' + '<?php _e('No data properties inserted','tainacan') ?>' + '</option>');
            }

        });
    }

    function renumber_all() {
        renumber_table_left('#table_search_data_left_column_id');
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
    
    /**
    * 
    */
    function showHabilitateMedia(seletor,value){
        if($(seletor).is(':checked') && value){
            $('#habilitateMedia').show();
        }else{
           $('#habilitateMedia').hide();
        }
    }
    /**
    * 
    */
    function showHabilitateItemMedia(seletor,value){
        if($(seletor).is(':checked') && value){
            $('#habilitateItemMedia').show();
        }else{
           $('#habilitateItemMedia').hide();
        }
    }
</script>
