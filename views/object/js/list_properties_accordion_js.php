<script>
    var checkbox_values = [];
    $(function () {
        var src = $('#src').val();
        var properties_autocomplete = get_val($("#properties_autocomplete").val());
        autocomplete_property_data(properties_autocomplete);
        $('#propertiesRootAdvancedSearch').html('');
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
        
        if($('.hide_rankings')&&$('.hide_rankings').val()==='true'){
            $('#list_ranking_items').hide();
        }
        
        if($('.hide_license')&&$('.hide_license').val()==='true'){
            $('#list_licenses_items').hide();
            $('#core_validation_license').val('true');
        }else{
            $('input:radio[name="object_license"]').change(function(){
                $('#core_validation_license').val('true');
                set_field_valid('license','core_validation_license');
            });
        }
        //# - inicializa os tooltips
        $('[data-toggle="tooltip"]').tooltip();
        //# - se o usuario desejar abrir todos os metadados
        $('.expand-all-item').toggle(function () {
            setMenuContainerHeight();
            $(this).find("div.action-text").text('<?php _e('Expand all', 'tainacan') ?>');
            $('#text_accordion .ui-accordion-content').fadeOut();
            $('.prepend-filter-label').switchClass('glyphicon-triangle-bottom', 'glyphicon-triangle-right');
            $(this).find('span').switchClass('glyphicon-triangle-bottom', 'glyphicon-triangle-right');
            $('.cloud_label').click();
        }, function () {
            $('#text_accordion .ui-accordion-content').fadeIn();
            $('.prepend-filter-label').switchClass('glyphicon-triangle-right', 'glyphicon-triangle-bottom');
            $(this).find('span').switchClass('glyphicon-triangle-right', 'glyphicon-triangle-bottom');
            $('.cloud_label').click();
            $(this).find("div.action-text").text('<?php _e('Collapse all', 'tainacan') ?>');
        });
        if($('#tabs_properties').length==0){
            $('.expand-all-item').trigger('click');
        }
        // # - inicializa o campos das propriedades de termo  
        list_properties_term_insert_objects();
        validate_all_fields();
    });


    function autocomplete_object_property_add(property_id, object_id) {
        $("#autocomplete_value_" + property_id + "_" + object_id).autocomplete({
            source: $('#src').val() + '/controllers/object/object_controller.php?operation=get_objects_by_property_json&property_id=' + property_id,
            messages: {
                noResults: '',
                results: function () {
                }
            },
            minLength: 2,
            select: function (event, ui) {
                console.log(event);
                $("#autocomplete_value_" + property_id + "_" + object_id).html('');
                $("#autocomplete_value_" + property_id + "_" + object_id).val('');
                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                var temp = $("#property_value_" + property_id + "_" + object_id + " [value='" + ui.item.value + "']").val();
                if (typeof temp == "undefined") {
                    var already_selected = false;
                    //validacao do campo
                    $('#core_validation_'+property_id).val('true');
                    set_field_valid(property_id,'core_validation_'+property_id);
                    //fim validacao do campo
                    $("#property_value_" + property_id + "_" + object_id + "_add option").each(function () {
                        if ($(this).val() == ui.item.value) {
                            already_selected = true;
                        }
                    });
                    if (!already_selected) {
                        if($('#cardinality_'+property_id + "_" + object_id).val()=='1'){
                             $("#property_value_" + property_id + "_" + object_id + "_add").html('');
                        }
                        $("#property_value_" + property_id + "_" + object_id + "_add").append("<option class='selected' value='" + ui.item.value + "' selected='selected' >" + ui.item.label + "</option>");
                        if (Hook.is_register('tainacan_validate_cardinality_onselect')) {
                            Hook.call('tainacan_validate_cardinality_onselect', ['select[name="socialdb_property_' + property_id + '[]"]', property_id]);
                        }
                    }
                }
                setTimeout(function () {
                    $("#autocomplete_value_" + property_id + "_" + object_id).val('');
                }, 100);
            }
        });
        
        //classe para formulario validae
    }
    /**
     * Autocomplete para os metadados de dados para insercao/edicao de item unico
     * @param {type} e
     * @returns {undefined}
     */
    function autocomplete_property_data(properties_autocomplete) {
        if (properties_autocomplete) {
            $.each(properties_autocomplete, function (idx, property_id) {
                if( $(".form_autocomplete_value_" + property_id).length==0){
                   // return false;
                }
                //validate
                $(".form_autocomplete_value_" + property_id).keyup(function(){
                    var cont = 0;
                    $(".form_autocomplete_value_" + property_id).each(function(index,value){
                       if( $(this).val().trim()!==''){
                            cont++;
                        }
                    });
                    
                    if( cont===0){
                        $('#core_validation_'+property_id).val('false');
                    }else{
                         $('#core_validation_'+property_id).val('true');
                    } 
                    
                    set_field_valid(property_id,'core_validation_'+property_id);
                });
                $(".form_autocomplete_value_" + property_id).change(function(){
                    var cont = 0;
                    $(".form_autocomplete_value_" + property_id).each(function(index,value){
                       if( $(this).val().trim()!==''){
                            cont++;
                        }
                    });
                    
                    if( cont===0){
                        $('#core_validation_'+property_id).val('false');
                    }else{
                         $('#core_validation_'+property_id).val('true');
                    }
                    set_field_valid(property_id,'core_validation_'+property_id);
                });
                // end validate
                $(".form_autocomplete_value_" + property_id).autocomplete({
                    source: $('#src').val() + '/controllers/collection/collection_controller.php?operation=list_items_search_autocomplete&property_id=' + property_id,
                    messages: {
                        noResults: '',
                        results: function () {
                        }
                    },
                    response: function( event, ui ) {
                        if(ui.content && ui.content.length>0 && $('.form_autocomplete_value_'+property_id+'_mask').val()!==''){
                           $.each(ui.content,function(index,value){
                               if($(event.target).val()==value.value || $(event.target).val().toLowerCase().trim()==value.value){
                                    toastr.error($(event.target).val()+' <?php _e(' is already inserted!', 'tainacan') ?>', '<?php _e('Attention!', 'tainacan') ?>', {positionClass: 'toast-bottom-right'});
                                    $(event.target).val('');
                               }
                               $(".form_autocomplete_value_" + property_id).autocomplete('close');
                           }); 
                        }
                        clear_repeated_values(event.target,".form_autocomplete_value_" + property_id);
                    },
                    minLength: 1,
                    select: function (event, ui) {
                        $("#form_autocomplete_value_" + property_id).val('');
                        if( $('.form_autocomplete_value_'+property_id+'_mask').val()!==''){
                            $(event.target).html(''); 
                            $(event.target).val('');
                            toastr.error(ui.item.value+' <?php _e(' is already inserted!', 'tainacan') ?>', '<?php _e('Attention!', 'tainacan') ?>', {positionClass: 'toast-bottom-right'});
                            return false;
                        }
                        //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                        var temp = $("#property_value_" + property_id).val();
                        if (typeof temp == "undefined") {
                            $("#form_autocomplete_value_" + property_id).val(ui.item.value);
                        }
                    }
                });
            });
        }
    }

    function clear_select_object_property(e, property_id, object_id) {
        $('option:selected', e).remove();
        $("#property_value_" + property_id + "_" + object_id + "_add option").each(function ()
        {
            $(this).attr('selected', 'selected');
        });
        //validacao do campo
        var cont = 0;
        $("#property_value_" + property_id + "_" + object_id + "_add option").each(function ()
        {
            cont++;
        });
        if(cont==0){
            $('#core_validation_'+property_id).val('false');
            set_field_valid(property_id,'core_validation_'+property_id);
        }            
        //fim validacao do campo
        if (Hook.is_register('tainacan_validate_cardinality_onselect')) {
            Hook.call('tainacan_validate_cardinality_onselect', ['select[name="socialdb_property_' + property_id + '[]"]', property_id]);
        }
        //$('.chosen-selected2 option').prop('selected', 'selected');
    }

//************************* properties terms ******************************************//
    function list_properties_term_insert_objects() {
        var radios = get_val($("#properties_terms_radio").val());
        var selectboxes = get_val($("#properties_terms_selectbox").val());
        var trees = get_val($("#properties_terms_tree").val());
        var checkboxes = get_val($("#properties_terms_checkbox").val());
        var multipleSelects = get_val($("#properties_terms_multipleselect").val());
        var treecheckboxes = get_val($("#properties_terms_treecheckbox").val());
        list_radios(radios);
        list_tree(trees);
        list_selectboxes(selectboxes);
        list_multipleselectboxes(multipleSelects);
        list_checkboxes(checkboxes);
        list_treecheckboxes(treecheckboxes);
    }
    // radios
    function list_radios(radios) {
        if (radios) {
            $.each(radios, function (idx, radio) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: radio}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#field_property_term_' + radio).html('');
                    $.each(elem.children, function (idx, children) {
                        var required = '';
                        //if (elem.metas.socialdb_property_required === 'true') {
                            required = ' onclick="validate_radio(' + radio + ')"';
                        //}
                        if(is_selected_category(children.term_id,'#object_classifications')){
                            required  += ' checked="checked" '
                        }
                        
                        //  if (property.id == selected) {
                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        //  } else {
                        $('#field_property_term_' + radio).append('<input class="auto-save" ' + required + ' type="radio" name="socialdb_propertyterm_' + radio + '" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                        //  }
                    });
                });
            });
        }
    }
    // checkboxes
    function list_checkboxes(checkboxes) {
        if (checkboxes) {
            $.each(checkboxes, function (idx, checkbox) {
                //inicia a propriedade
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: checkbox}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#field_property_term_' + checkbox).html('');                    
                    $.each(elem.children, function (idx, children) {
                        var checked = '';
                        if(is_selected_category(children.term_id,'#object_classifications')){
                            checked  += ' checked="checked" '
                        }
                        $('#field_property_term_' + checkbox).append('<input class="auto-save"  type="checkbox" '+checked+' onchange="validate_checkbox(' + checkbox + ')" name="socialdb_propertyterm_' + checkbox + '[]" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                    });
                    var required = '';
                    if (elem.metas.socialdb_property_required === 'true') {
                       // required = 'required';
                    }
                    $('#field_property_term_' + checkbox).append('<input type="hidden" name="checkbox_required_' + checkbox + '" value="' + required + '" >');
                });
                //
                
            });
        }
    }

    // selectboxes
    function list_selectboxes(selectboxes) {
        if (selectboxes) {
            $.each(selectboxes, function (idx, selectbox) {
                //validation
                $('#field_property_term_' + selectbox).select(function(){
                    if( $("#field_property_term_" + selectbox).val()===''){
                        $('#core_validation_'+selectbox).val('false');
                    }else{
                         $('#core_validation_'+selectbox).val('true');
                    }
                    set_field_valid(selectbox,'core_validation_'+selectbox);
                });
                //
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: selectbox}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#field_property_term_' + selectbox).html('');
                    $('#field_property_term_' + selectbox).append('<option value=""><?php _e('Select','tainacan') ?>...</option>');
                    if(elem.children){    
                        $.each(elem.children, function (idx, children) {
                            var selected = '';
                            if(is_selected_category(children.term_id,'#object_classifications')){
                                selected  += ' selected="selected" '
                            }
                            //  if (property.id == selected) {
                            //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                            //  } else {
                            $('#field_property_term_' + selectbox).append('<option '+selected+' value="' + children.term_id + '">' + children.name + '</option>');
                            //  }
                        });
                    }    
                });
            });
        }
    }
    // multiple
    function list_multipleselectboxes(multipleSelects) {
        if (multipleSelects) {
            $.each(multipleSelects, function (idx, multipleSelect) {
                //validation
                $('#field_property_term_' + multipleSelect).select(function(){
                    if( $("#field_property_term_" + multipleSelects).val()===''){
                        $('#core_validation_'+multipleSelect).val('false');
                    }else{
                         append_category_properties($("#field_property_term_" + multipleSelects).val(),$("#field_property_term_" + multipleSelects).val(),multipleSelect);
                         $('#core_validation_'+multipleSelect).val('true');
                    }
                    set_field_valid(multipleSelect,'core_validation_'+multipleSelect);
                });
                //init
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: multipleSelect}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#field_property_term_' + multipleSelect).html('');
                    if(elem.children){  
                        $.each(elem.children, function (idx, children) {
                            var selected = '';
                            if(is_selected_category(children.term_id,'#object_classifications')){
                                selected  += ' selected="selected" '
                            }
                            $('#field_property_term_' + multipleSelect).append('<option '+selected+' value="' + children.term_id + '">' + children.name + '</option>');
                            //  }
                        });
                    }
                });
            });
        }
    }
    // treecheckboxes
    function list_treecheckboxes(treecheckboxes) {
        if (treecheckboxes) {
            $.each(treecheckboxes, function (idx, treecheckbox) {
                $("#field_property_term_" + treecheckbox).dynatree({
                    selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
                    checkbox: true,
                    initAjax: {
                        url: $('#src').val() + '/controllers/category/category_controller.php',
                        data: {
                            collection_id: $("#collection_id").val(),
                            property_id: treecheckbox,
                            operation: 'initDynatreeDynamic'
                        }
                        , addActiveKey: true
                    },
                    onLazyRead: function (node) {
                        node.appendAjax({
                            url: $('#src').val() + '/controllers/collection/collection_controller.php',
                            data: {
                                collection: $("#collection_id").val(),
                                key: node.data.key,
                                classCss: node.data.addClass,
                                //operation: 'findDynatreeChild'
                                operation: 'expand_dynatree'
                            }
                        });
                    },
                    onClick: function (node, event) {
                        // Close menu on click
                        $("#property_object_category_id").val(node.data.key);
                        $("#property_object_category_name").val(node.data.title);

                    },
                    onPostInit: function (isReloading, isError) {
                        select_items("#field_property_term_" + treecheckbox);
                    },
                    onCreate: function (node, span) {
                         bindContextMenuSingle(span,'field_property_term_' + treecheckbox);
                        $('.dropdown-toggle').dropdown();
                    },
                    onSelect: function (flag, node) {
                        var cont = 0;
                        var selKeys = $.map(node.tree.getSelectedNodes(), function (node) {
                            return node;
                        });
                        var categories = $.map(node.tree.getSelectedNodes(), function (node) {
                            return node.data.key;
                        });
                        if(categories.length>0&&categories.indexOf(node.data.key)>=0){
                            append_category_properties(node.data.key,node.data.key,treecheckbox);
                        }else{
                            append_category_properties(0,node.data.key, treecheckbox);
                        }
                        
                        
                        $("#socialdb_propertyterm_" + treecheckbox).html('');
                        $.each(selKeys, function (index, key) {
                            cont++;
                            $("#socialdb_propertyterm_" + treecheckbox).append('<input type="hidden" name="socialdb_propertyterm_'+treecheckbox+'[]" value="' + key.data.key + '" >');
                        });
                        if(cont===0){
                            $('#core_validation_'+treecheckbox).val('false');
                            set_field_valid(treecheckbox,'core_validation_'+treecheckbox);
                         }else{
                            $('#core_validation_'+treecheckbox).val('true');
                            set_field_valid(treecheckbox,'core_validation_'+treecheckbox); 
                             $('form .auto-save').trigger('change');
                         }
                    }
                });
            });
        }
    }
    
    function select_items(dynatree){
        $(dynatree).dynatree("getRoot").visit(function(node){
            if(is_selected_category(node.data.key,'#object_classifications')){
                node.select(true);
            }
        });
    }

    // tree
    function list_tree(trees) {
        if (trees) {
            $.each(trees, function (idx, tree) {
                $("#field_property_term_" + tree).dynatree({
                    checkbox: true,
                    // Override class name for checkbox icon:
                    classNames: {checkbox: "dynatree-radio"},
                    selectMode: 1,
                    selectionVisible: true, // Make sure, selected nodes are visible (expanded). 
                    checkbox: true,
                            initAjax: {
                                url: $('#src').val() + '/controllers/category/category_controller.php',
                                data: {
                                    collection_id: $("#collection_id").val(),
                                    property_id: tree,
                                    //hide_checkbox: 'true',
                                    operation: 'initDynatreeDynamic'
                                }
                                , addActiveKey: true
                            },
                    onLazyRead: function (node) {
                        node.appendAjax({
                            url: $('#src').val() + '/controllers/collection/collection_controller.php',
                            data: {
                                collection: $("#collection_id").val(),
                                key: node.data.key,
                                //hide_checkbox: 'true',
                                hide_count: 'true',
                                classCss: node.data.addClass,
                                //operation: 'findDynatreeChild'
                                operation: 'expand_dynatree'
                            }
                        });
                    },
                    onPostInit: function (isReloading, isError) {
                        select_items("#field_property_term_" + tree);
                    },
                    onCreate: function (node, span) {
                        bindContextMenuSingle(span,'field_property_term_' + tree);
                        $('.dropdown-toggle').dropdown();
                    },
                    onSelect: function (flag, node) {
                        console.log('unic dynatree',$("#socialdb_propertyterm_" + tree).val(), node.data.key);
                        if ($("#socialdb_propertyterm_" + tree).val() === node.data.key) {
                            append_category_properties(0,node.data.key,tree);
                            $("#socialdb_propertyterm_" + tree).val("");
                             $('#core_validation_'+tree).val('false');
                             set_field_valid(tree,'core_validation_'+tree);
                        } else {
                            append_category_properties(node.data.key,$("#socialdb_propertyterm_" + tree).val(),tree);
                            $("#socialdb_propertyterm_" + tree).val(node.data.key);
                            $('#core_validation_'+tree).val('true');
                             $('form .auto-save').trigger('change');
                             set_field_valid(tree,'core_validation_'+tree);
                        }
                    }
                });
            });
        }
    }
    // get value of the property
    function get_val(value) {
        if (value === '' || value === undefined) {
            return false;
        } else if (value.split(',')[0] === '' && value !== '') {
            return [value];
        } else {
            return value.split(',');
        }
    }
//######## INSERCAO DE UM ITEM AVULSO EM UMA COLECAO #########################//    
    function add_new_item_by_title(collection_id,title,seletor,property_id,object_id){
        if(title.trim()===''){
            showAlertGeneral('<?php _e('Attention!','tainacan') ?>','<?php _e('Item title is empty!','tainacan') ?>','info');
        }else{
            $(seletor).trigger('click');
            $('#title_'+ property_id + "_" + object_id ).val('');
            show_modal_main();
            $.ajax({
                url: $('#src').val() + '/controllers/object/object_controller.php',
                type: 'POST',
                data: { operation: 'insert_fast', collection_id: collection_id, title: title}
            }).done(function (result) {
                hide_modal_main();
                wpquery_filter();
                //list_all_objects(selKeys.join(", "), $("#collection_id").val());
                elem_first = jQuery.parseJSON(result);
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                if(elem_first.type==='success'){
                    $("#property_value_" + property_id + "_" + object_id + "_add").append("<option class='selected' value='" + elem_first.item.ID + "' selected='selected' >" + elem_first.item.post_title + "</option>");
                }
            });
        }
    }
//################################ adicao de propriedades de categorias #################################//    
    function append_category_properties(id,remove_id,property_id ){
        //buscando as categorias selecionadas nos metadados de termo
        var selected_categories = $('#selected_categories').val();
        if(selected_categories===''){
             selected_categories= [];
        }else{
            selected_categories = selected_categories.split(',');
        }
        //se estiver retirando alguma das categorias
        if(remove_id&&selected_categories.indexOf(remove_id)>=0){
            var index = selected_categories.indexOf(remove_id);
            selected_categories.splice(index, 1);
            $('#selected_categories').val(selected_categories.join(','));
            if($('.category-'+remove_id)){
                $.each($('.category-'+remove_id),function(index,value){
                    var id = $(this).attr('property');
                    remove_property_general(id);
                });
                $('.category-'+remove_id).remove();
            }
        }else if($('select[name="socialdb_propertyterm_'+property_id+'"]').is('select')){ // removo todos os ids desta propriedade
            $.each($('select[name="socialdb_propertyterm_'+property_id+'"] option'),function(index,val){
                var i = selected_categories.indexOf($(this).val());
                if(i>=0){
                    selected_categories.splice(i, 1);
                    $('#selected_categories').val(selected_categories.join(','));
                    $.each($('.category-'+$(this).val()),function(index,value){
                        var id = $(this).attr('property');
                        remove_property_general(id);
                    });
                    $('.category-'+$(this).val()).remove();
                }
            });
        }
        //busco os metadados da categoria selecionada    
        if(id&&selected_categories.indexOf(id)>=0){
            //var index = selected_categories.indexOf(id);
           // selected_categories.splice(index, 1);
            //$('#selected_categories').val(selected_categories.join(','));
        }else if(id!==0){
            selected_categories.push(id);
            //adicionando metadados
            $('#append_properties_categories_'+property_id)
                     .html('<center><img width="100" heigth="100" src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>"><?php _e('Loading metadata for this field','tainacan') ?></center>');
            $.ajax({
                url: $('#src').val() + '/controllers/object/object_controller.php',
                type: 'POST',
                data: { operation: 'list_properties_categories_accordeon',properties_to_avoid:$('#properties_id').val(),categories: id, object_id:$('#object_id_add').val(),not_block:'true'}
            }).done(function (result) {
                console.log('append_properties_categories_');
                hide_modal_main();
                //list_all_objects(selKeys.join(", "), $("#collection_id").val());
                $('#append_properties_categories_'+property_id).html(result);
                insert_html_property_category(property_id);

            });
            $('#selected_categories').val(selected_categories.join(','));
        }
    }
    function insert_html_property_category(property_id){
        console.log('insert_html_property_category');
        var flag = false;
        $ul = $("#text_accordion");
        $items = $("#text_accordion").children();
        $('#append_properties_categories_'+property_id).css('margin-top','15px');
        $properties_append = $('#append_properties_categories_'+property_id).children().children();
        $properties_append.animate({borderWidth : '1px',borderColor: 'red',borderStyle: 'dotted'}, 'slow', 'linear');
        setTimeout(removeBorderCat(property_id),8000);
        for (var i = 0; i <$properties_append.length; i++) {
              // index is zero-based to you have to remove one from the values in your array
                for(var j = 0; j<$items.length;j++){
                    if($($items.get(j)).attr('id')&&$($items.get(j)).attr('id')===$($properties_append.get(i)).attr('id')){
                        flag = true;
                    }
                }
                if(!flag){
                    // $( $properties_append.get(i) ).appendTo( $ul);
                    var id =  $( $properties_append.get(i) ).attr('property');
                    if(id){
                        add_property_general(id);
                    }
                }
               flag = false;
         }
         $(".multiple-items-accordion").accordion("destroy");  
         $(".multiple-items-accordion").accordion({
                    active: false,
                    collapsible: true,
                    header: "h2",
                    heightStyle: "content",
                    beforeActivate: function(event, ui) {
                            // The accordion believes a panel is being opened
                           if (ui.newHeader[0]) {
                               var currHeader  = ui.newHeader;
                               var currContent = currHeader.next('.ui-accordion-content');
                            // The accordion believes a panel is being closed
                           } else {
                               var currHeader  = ui.oldHeader;
                               var currContent = currHeader.next('.ui-accordion-content');
                           }
                            // Since we've changed the default behavior, this detects the actual status
                           var isPanelSelected = currHeader.attr('aria-selected') == 'true';

                            // Toggle the panel's header
                           currHeader.toggleClass('ui-corner-all',isPanelSelected).toggleClass('accordion-header-active ui-state-active ui-corner-top',!isPanelSelected).attr('aria-selected',((!isPanelSelected).toString()));

                           // Toggle the panel's icon
                           currHeader.children('.ui-icon').toggleClass('ui-icon-triangle-1-e',isPanelSelected).toggleClass('ui-icon-triangle-1-s',!isPanelSelected);

                            // Toggle the panel's content
                           currContent.toggleClass('accordion-content-active',!isPanelSelected)    
                           if (isPanelSelected) { currContent.slideUp(); }  else { currContent.slideDown(); }

                           return false; // Cancels the default action
                       }
                });
          console.log($('#meta-item-'+property_id+' h2'),'#meta-item-'+property_id+' h2')
         $('#meta-item-'+property_id+' h2').trigger('click');
         $('[data-toggle="tooltip"]').tooltip();
    }
    //retira as bordas
    function removeBorderCat(property_id){
        $properties_append = $('#append_properties_categories_'+property_id).children().children();
        console.log($properties_append);
        $properties_append.animate({borderWidth : '1px',borderColor: '#d3d3d3',borderStyle:"solid"}, 'slow', 'linear');
    }
    
    //adicionando as propriedades das categorias no array de propriedades gerais
    function add_property_general(id){
        var ids = $('#properties_id').val().split(','); 
        if(ids){
           ids.push(id);
        }
         $('#properties_id').val(ids.join(','));
    }
    //removendo as propriedades das categorias no array de propriedades gerais
    function remove_property_general(id){
        var ids = $('#properties_id').val().split(','); 
        var index = ids.indexOf(id);
        ids.splice(index, 1);
        $('#properties_id').val(ids.join(','));
    }
   
//################################ Inicializao de data #################################//    
    function init_metadata_date(seletor){
        $(seletor).datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            showOn: "button",
            buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
            buttonImageOnly: true
        });
    }
//################################ Cardinalidade #################################//    
    function show_fields_metadata_cardinality(property_id,id){
        $('#button_property_'+property_id+'_'+id).hide();
        $('#container_field_'+property_id+'_'+(id+1)).show();         
    }
    
    
    function remove_container(property_id,id){
        var show_button = false;
        $('#container_field_'+property_id+'_'+(id)).hide();
        $('#core_validation_'+property_id).val('true');
        $('#form_autocomplete_value_'+property_id+'_'+(id)+"_origin").val('');
        if($('#socialdb_property_'+property_id+'_'+(id)).length>0)
            $('#socialdb_property_'+property_id+'_'+(id)).val('');
        validate_all_fields();
        //se o proximo container
        if(!$('#container_field_'+property_id+'_'+(id+1)).is(':visible')){
            show_button = true;
        }
        //busco o container que esta sendo mostrado
        while(!$('#container_field_'+property_id+'_'+(id)).is(':visible')){
            id--;
        }
        //se 
        if(show_button)
            $('#button_property_'+property_id+'_'+id).show();
        
    }
//################################ VALIDACOES#################################//
    /**
     * funcao que valida os campos radios, e realiza a insercao das propriedades de categorias
     * @param {type} property_id
     * @returns {undefined}     */
    function validate_status(property_id){
        var selected = $("input[type='radio'][name='socialdb_property_"+property_id+"']:checked");
        if($(selected[0]).val()===$('#socialdb_property_'+property_id+'_value').val()){
            $(selected[0]).removeAttr('checked');
        }
        if (selected.length > 0) {
            $('#socialdb_propertyterm_'+property_id+'_value').val(selected.val()); 
            $('#core_validation_'+property_id).val('true');
            set_field_valid(property_id,'core_validation_'+property_id);
        }else{
            $('#core_validation_'+property_id).val('false');
            set_field_valid(property_id,'core_validation_'+property_id);
        }
    }
    /**
     * funcao que valida os campos radios, e realiza a insercao das propriedades de categorias
     * @param {type} property_id
     * @returns {undefined}     */
    function validate_radio(property_id){
        var selected = $("input[type='radio'][name='socialdb_propertyterm_"+property_id+"']:checked");
        if($(selected[0]).val()===$('#socialdb_propertyterm_'+property_id+'_value').val()){
            $(selected[0]).removeAttr('checked');
        }
        if (selected.length > 0) {
            append_category_properties(selected.val(), $('#socialdb_propertyterm_'+property_id+'_value').val(),property_id);
            $('#socialdb_propertyterm_'+property_id+'_value').val(selected.val()); 
            $('#core_validation_'+property_id).val('true');
            set_field_valid(property_id,'core_validation_'+property_id);
        }else{
            $('#core_validation_'+property_id).val('false');
            set_field_valid(property_id,'core_validation_'+property_id);
        }
    }
    /**
     * funcao que valida o campo checkbox, e realiza a insercao das propriedades de categorias
     * @param {type} property_id
     * @returns {undefined}     */
    function validate_checkbox(property_id){
        var selected = $("input[type='checkbox'][name='socialdb_propertyterm_"+property_id+"[]']:checked");
        if (selected.length > 0) {
            $('#core_validation_'+property_id).val('true');
            set_field_valid(property_id,'core_validation_'+property_id);
        }else{
            $('#core_validation_'+property_id).val('false');
            set_field_valid(property_id,'core_validation_'+property_id);
        }
        //verificando se existe propriedades para serem  adicionadas
        $.each($("input[type='checkbox'][name='socialdb_propertyterm_"+property_id+"[]']"),function(index,value){
            if($(this).is(':checked')){
                append_category_properties($(this).val(),$(this).val(),property_id);
            }else{
                append_category_properties(0,$(this).val(),property_id);
            }
        });
    }
    /**
     * funcao que valida os campos de selecao unica
     * @param {type} seletor
     * @param {type} property_id
     * @returns {undefined}     */
    function list_validate_selectbox(seletor,property_id){
        if($(seletor).val()===''){
            $('#core_validation_'+property_id).val('false');
            set_field_valid(property_id,'core_validation_'+property_id);
        }else{
            append_category_properties($(seletor).val(), $('#socialdb_propertyterm_'+property_id+'_value').val(),property_id);
           $('#socialdb_propertyterm_'+property_id+'_value').val($(seletor).val()); 
            $('#core_validation_'+property_id).val('true');
            set_field_valid(property_id,'core_validation_'+property_id);
        }
        
    }
    /**
     * funcao que valida os campos de multipla selecao
     * @param {type} id
     * @param {type} seletor
     * @returns {undefined}     */
    function validate_multipleselectbox(seletor,property_id){
        var selected = $("#field_property_term_"+property_id+"").find(":selected");
        if (selected.length > 0) {
            $('#core_validation_'+property_id).val('true');
            set_field_valid(property_id,'core_validation_'+property_id);
            //verificando se existe propriedades para serem  adicionadas
            $.each($("#field_property_term_"+property_id+" option"),function(index,value){
                if($(this).is(':selected')){
                    append_category_properties($(this).val(),$(this).val(),property_id);
                }else{
                    append_category_properties(0,$(this).val(),property_id);
                }
            });
        }else{
            $('#core_validation_'+property_id).val('false');
            set_field_valid(property_id,'core_validation_'+property_id);
        }
    }
    
    function set_field_valid(id,seletor){
        if($('#'+seletor).val()==='false'){
            $('#core_validation_'+id).val('false');
            $('#ok_field_'+id).hide();
            $('#required_field_'+id).show();
        }else{
            $('#core_validation_'+id).val('true');
            $('#ok_field_'+id).show();
            $('#required_field_'+id).hide();
            if(!$.isNumeric(id) && $('#fixed_id_'+id).length > 0){
                var id =  $('#fixed_id_'+id).val();
            }
            $('#meta-item-'+id+' h2').css('background-color','#fffff');
        }
        validate_all_fields();
        if($('#'+seletor).val()!=='false'){
             $('#meta-item-'+id+' h2').css('background-color','#fffff');
        }
    }
    
    function validate_all_fields(){
        var cont = 0;
        var cont_pane = 0;
        var deny_repeated_ids = [];
        $( ".core_validation").each(function( index ) {
            if(deny_repeated_ids.indexOf($( this ).attr('id'))<0){
                deny_repeated_ids.push($( this ).attr('id'));
                if($( this ).val()==='false'){
                    cont++;
                    var id = $( this ).attr('id').replace('core_validation_','');
                    if(!$.isNumeric(id) && $('#fixed_id_'+id).length > 0){
                         var id =  $('#fixed_id_'+id).val();
                    }
                    $('#meta-item-'+id+' h2').css('background-color','#ffcccc');
                    $.each($( "#submit_form .tab-pane" ),function(index,seletor){
                        if($(seletor).find('#meta-item-'+id).length > 0){
                            var id_tab = $(seletor ).attr('id').replace('tab-','');
                            $('#click-tab-'+id_tab).css('background-color','#ffcccc');
                        }
                    });
                }
            }
        });
        $.each($( "#submit_form .tab-pane" ),function(index,seletor){
                var id_tab = $(seletor ).attr('id').replace('tab-','');
                deny_repeated_ids = [];
                $( seletor).find(".core_validation").each(function( index ) {
                    if(deny_repeated_ids.indexOf($( this ).attr('id'))<0){
                        deny_repeated_ids.push($( this ).attr('id'));
                        if($( this ).val()==='false'){
                            cont_pane++;
                        }
                    }
                    
                });
                if(cont_pane===0){
                     $('#click-tab-'+id_tab).css('background-color','white');
                }
                cont_pane= 0;
        });        
        
        if(cont===0){
            $('#tabs_item li a').css('background-color','white');
            $('#submit_container').show();
            $('#submit_container_message').hide();
        }else{
            $('#submit_container').hide();
            $('#submit_container_message').show();
        }
    }
</script>
