<script>
    $(function () {
        var src = $('#src').val();
        //# 3 - esconde, se necessario os campos de ranking e licencas
        if($('.hide_rankings')&&$('.hide_rankings').val()==='true'){
            $('#list_ranking_items').hide();
        }
        
        if($('.hide_license')&&$('.hide_license').val()==='true'){
            $('#list_licenses_items').hide();    
            $('#core_validation_license').val('true');
        }else{
            if($("input[type='radio'][name='object_license']")){
                $("input[type='radio'][name='object_license']").change(function(){
                    $('#core_validation_license').val('true');
                    set_field_valid('license','core_validation_license');
                });
                if($('.already_checked_license')&&$('.already_checked_license').val()==='true'){
                   $('#core_validation_license').val('true');
                   set_field_valid('license','core_validation_license');
                }
            }
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
        edit_list_properties_term_insert_objects();
        if($('#properties_autocomplete')&&$('#properties_autocomplete').val()){
            var values = $('#properties_autocomplete').val().split(',');
            autocomplete_edit_item_property_data(values);
        }
    });

   
    function autocomplete_object_property_edit(property_id, object_id) {
        $("#autocomplete_value_" + property_id + "_" + object_id).autocomplete({
            source: $('#src').val() + '/controllers/object/object_controller.php?operation=get_objects_by_property_json&property_id=' + property_id,
            messages: {
                noResults: '',
                results: function () {
                }
            },
            minLength: 2,
            select: function (event, ui) {
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
                    $("#property_value_" + property_id + "_" + object_id+"_edit option").each(function(){
                        if($(this).val()==ui.item.value){
                            already_selected = true;
                        }
                    });
                    if(!already_selected){
                        if($('#cardinality_'+property_id + "_" + object_id).val()=='1'){
                             $("#property_value_" + property_id + "_" + object_id + "_edit").html('');
                        }
                        $("#property_value_" + property_id + "_" + object_id+"_edit").append("<option class='selected' value='" + ui.item.value + "' selected='selected' >" + ui.item.label + "</option>");
                        //hook para validacao do campo ao selecionar
                        if(Hook.is_register( 'tainacan_validate_cardinality_onselect')){
                            Hook.call( 'tainacan_validate_cardinality_onselect', [ 'select[name="socialdb_property_'+property_id+'[]"]',property_id ] );
                        }
                    }
                }
                setTimeout(function () {
                    $("#autocomplete_value_" + property_id + "_" + object_id).val('');
                }, 100);
            }
        });
    }
    
     /**
     * Autocomplete para os metadados de dados para insercao/edicao de item unico
     * @param {type} e
     * @returns {undefined}
     */
    function autocomplete_edit_item_property_data(properties_autocomplete) {
         if (properties_autocomplete) {
            $.each(properties_autocomplete, function (idx, property_id) {
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
                        if($(".form_autocomplete_value_" + property_id).length>0){
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
                        }
                        // end validate
                        if($(".form_autocomplete_value_" + property_id).length>0){
                            $(".form_autocomplete_value_" + property_id).autocomplete({
                                source: $('#src').val() + '/controllers/collection/collection_controller.php?operation=list_items_search_autocomplete&property_id=' + property_id,
                                messages: {
                                    noResults: '',
                                    results: function () {
                                    }
                                },
                                minLength: 2,
                                response: function( event, ui ) {
                                    var myself = false;
                                    var contador = false;
                                    if(ui.content && ui.content.length>0 && $('.form_autocomplete_value_'+property_id+'_mask').val()!==''){
                                       $.each(ui.content,function(index,value){
                                           console.log( value.item_id , $('#object_id_edit').val())
                                           if(($(event.target).val()==value.value || $(event.target).val().toLowerCase().trim()==value.value.toLowerCase().trim()) && value.item_id != $('#object_id_edit').val()){
                                               contador++;
                                           }else if(($(event.target).val()==value.value || $(event.target).val().toLowerCase().trim()==value.value.toLowerCase().trim()) && value.item_id == $('#object_id_edit').val()){
                                               myself = true;
                                           }
                                           $(".form_autocomplete_value_" + property_id).autocomplete('close');
                                       }); 

                                       if(contador>0 && myself === false){
                                            toastr.error($(event.target).val()+' <?php _e(' is already inserted!', 'tainacan') ?>', '<?php _e('Attention!', 'tainacan') ?>', {positionClass: 'toast-bottom-right'});
                                            $(event.target).val('');
                                            $(".form_autocomplete_value_" + property_id).autocomplete('close');
                                       }
                                    }
                                },
                                select: function (event, ui) {
                                    $("#form_autocomplete_value_" + property_id).val('');
                                    if( $('.form_autocomplete_value_'+property_id+'_mask').val()!=='' && $(event.target).val().indexOf('key')){
                                        $(event.target).html(''); 
                                        $(event.target).val('');
                                        toastr.error(ui.item.value+'<?php _e(' is already inserted!', 'tainacan') ?>', '<?php _e('Attention!', 'tainacan') ?>', {positionClass: 'toast-bottom-right'});
                                        return false;
                                    }else{
                                        var temp = $(".form_edit_autocomplete_value_" + property_id).val();
                                        if (typeof temp == "undefined") {
                                            $(".form_autocomplete_value_" + property_id).val(ui.item.value);
                                        }
                                    }
                                }
                            });
                        }
                    });
                }
    }
    
     function clear_select_object_property(e,property_id,object_id) {
        $('option:selected', e).remove();
         $("#property_value_" + property_id + "_" + object_id+"_edit option").each(function()
        {
           $(this).attr('selected','selected');
        });
        //validacao do campo
        var cont = 0;
        $("#property_value_" + property_id + "_" + object_id + "_edit option").each(function ()
        {
            cont++;
        });
        if(cont==0){
            $('#core_validation_'+property_id).val('false');
            set_field_valid(property_id,'core_validation_'+property_id);
        }            
        //fim validacao do campo
        if(Hook.is_register( 'tainacan_validate_cardinality_onselect')){
            Hook.call( 'tainacan_validate_cardinality_onselect', [ 'select[name="socialdb_property_'+property_id+'[]"]',property_id ] );
        }
        //$('.chosen-selected2 option').prop('selected', 'selected');
    }
    
    //************************* properties terms ******************************************//
    function edit_list_properties_term_insert_objects() {
        var categories = edit_get_val($("#edit_object_categories_id").val());
        var radios = edit_get_val($("#properties_terms_radio").val());
        var selectboxes = edit_get_val($("#properties_terms_selectbox").val());
        var trees = edit_get_val($("#properties_terms_tree").val());
        var checkboxes = edit_get_val($("#properties_terms_checkbox").val());
        var multipleSelects = edit_get_val($("#properties_terms_multipleselect").val());
        var treecheckboxes = edit_get_val($("#properties_terms_treecheckbox").val());
        edit_list_radios(radios,categories);
        edit_list_tree(trees,categories);
        edit_list_selectboxes(selectboxes,categories);
        edit_list_multipleselectboxes(multipleSelects,categories);
        edit_list_checkboxes(checkboxes,categories);
        edit_list_treecheckboxes(treecheckboxes,categories);
    }
    // radios
    function edit_list_radios(radios,categories) {
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
                        var checked = '';
                        //if(elem.metas.socialdb_property_required==='true'){
                            required = ' onchange="validate_radio(' + radio + ')"';
                        //}
                        //  if (property.id == selected) {
                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        //  } else {
                        if(categories&&categories.indexOf(children.term_id)>-1){
                            checked = 'checked="checked"';
                            //append_category_properties(children.term_id, 0,radio);
                        }
                         delete_value(children.term_id);//retiro
                        $('#field_property_term_' + radio).append('<input class="auto-save" '+checked+' '+required+' type="radio" name="socialdb_propertyterm_'+radio+'" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                        //  }
                    });
                });
            });
        }
    }
    // checkboxes
    function edit_list_checkboxes(checkboxes,categories) {
        if (checkboxes) {
            $.each(checkboxes, function (idx, checkbox) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: checkbox}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#field_property_term_' + checkbox).html('');
                    $.each(elem.children, function (idx, children) {
                        var required = '';
                        var checked = '';
                         delete_value(children.term_id);
                        if(elem.metas.socialdb_property_required==='true'){
                            required = 'onchange="validate_checkbox(' + checkbox + ')"';
                        }
                        if(categories.indexOf(children.term_id)>-1){
                            checked = 'checked="checked"';
                        }
                        //  if (property.id == selected) {
                        //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        //  } else {
                        $('#field_property_term_' + checkbox).append('<input class="auto-save" '+checked+' '+required+'  type="checkbox" name="socialdb_propertyterm_'+checkbox+'[]" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                        //  }
                    });
                });
            });
        }
    }
    // selectboxes
    function edit_list_selectboxes(selectboxes,categories) {
        if (selectboxes) {
            $.each(selectboxes, function (idx, selectbox) {
                $.ajax({
                    url: $('#src').val() + '/controllers/property/property_controller.php',
                    type: 'POST',
                    data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: selectbox}
                }).done(function (result) {
                    elem = jQuery.parseJSON(result);
                    $('#field_property_term_' + selectbox).html('');
                    $('#field_property_term_' + selectbox).append('<option value=""><?php _e('Select','tainacan') ?>...</option>');
                    $.each(elem.children, function (idx, children) {
                        var checked = '';
                         delete_value(children.term_id);
                        if(categories.indexOf(children.term_id)>-1){
                            checked = 'selected="selected"';
                            $('#core_validation_'+selectbox).val('true');
                            set_field_valid(selectbox,'core_validation_'+selectbox);
                        }
                        $('#field_property_term_' + selectbox).append('<option '+checked+' value="' + children.term_id + '">' + children.name + '</option>');
                        if(checked!==''){
                           $('#field_property_term_' + selectbox).trigger('change');
                        }
                        //  }
                    });
                });
            });
        }
    }
     // multiple
    function edit_list_multipleselectboxes(multipleSelects,categories) {
        if (multipleSelects) {
            $.each(multipleSelects, function (idx, multipleSelect) {
                //validation
                $('#field_property_term_' + multipleSelect).select(function(){
                    if( $("#field_property_term_" + multipleSelects).val()===''){
                        $('#core_validation_'+multipleSelect).val('false');
                    }else{
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
                    $.each(elem.children, function (idx, children) {
                        var checked = '';
                        delete_value(children.term_id);
                        if(categories.indexOf(children.term_id)>-1){
                            checked = 'selected="selected"';
                        }
                        $('#field_property_term_' + multipleSelect).append('<option '+checked+' value="' + children.term_id + '">' + children.name + '</option>');
                        //  }
                    });
                });
            });
        }
    }
    // treecheckboxes
    function edit_list_treecheckboxes(treecheckboxes,categories) {
        if (treecheckboxes) {
            $.each(treecheckboxes, function (idx, treecheckbox) {
                $("#field_property_term_"+treecheckbox).dynatree({
                    selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
                    checkbox: true,
                    initAjax: {
                        url: $('#src').val() + '/controllers/category/category_controller.php',
                        data: {
                            collection_id: $("#collection_id").val(),
                            property_id: treecheckbox,
                            order: 'name',
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
                                order: 'name',
                                //operation: 'findDynatreeChild'
                                operation: 'expand_dynatree'
                            }
                        });
                    },
                    onClick: function (node, event) {
                        // Close menu on click
                        delete_value(node.data.key);
                        $("#property_object_category_id").val(node.data.key);
                        $("#property_object_category_name").val(node.data.title);

                    },
                    onCreate: function (node, span) {
                        $("#field_property_term_"+treecheckbox).dynatree("getRoot").visit(function(node){
                            delete_value(node.data.key);
                           if(categories&&categories.indexOf(node.data.key)>-1){
                                node.select();
                            }
                        });
                         bindContextMenuSingle(span,'field_property_term_' + treecheckbox);
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
                            append_category_properties(0,node.data.key,treecheckbox);
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
                             $('form .auto-save').trigger('change');
                            set_field_valid(treecheckbox,'core_validation_'+treecheckbox); 
                         }
                    }
                });
            });
        }
    }
    
    // tree
    function edit_list_tree(trees,categories) {
        if (trees) {
            $.each(trees, function (idx, tree) {
                $("#field_property_term_"+tree).dynatree({
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
                           // hide_checkbox: 'true',
                            order: 'name',
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
                                classCss: node.data.addClass,
                                 order: 'name',
                                //operation: 'findDynatreeChild'
                                operation: 'expand_dynatree'
                            }
                        });
                    },
                    onCreate: function (node, span) {
                        var selKeys = $.map($("#field_property_term_"+tree).dynatree("getSelectedNodes"), function(node) {
                            return node.data.key;
                        });
                        // $("#field_property_term_"+tree).dynatree("getRoot").visit(function(node){
                        delete_value(node.data.key); 
                        if(categories&&categories.indexOf(node.data.key)>-1&&selKeys.length==0){
                             node.select();
                             return true;
                         }
                        //});
                        bindContextMenuSingle(span,'field_property_term_' + tree);
                    },
                    onSelect: function (flag, node) {
                        if ($("#socialdb_propertyterm_" + tree).val() === node.data.key) {
                            append_category_properties(0,node.data.key, tree);
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
    function edit_get_val(value) {
        if (!value||value === '' ) {
            return false;
        } else if (value.split(',')[0] === '' && value !== '') {
            return [value];
        } else {
            return value.split(',');
        }
    }
    
    function delete_value(category_id){
        if(!$("#object_classifications_edit")||!$("#object_classifications_edit").val()){
            return false;
        }
       var classifications =   $("#object_classifications_edit").val().split(',');
       if(classifications.length>0&&category_id){
           var index = classifications.indexOf(category_id);
           if(index>-1){
               classifications.splice(index, 1);
               $("#object_classifications_edit").val(classifications.join());
           }
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
    function append_category_properties(id,remove_id,property_id){
        //buscando as categorias selecionadas nos metadados de termo
        if($('#selected_categories').length == 0){
            return false;
        }
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
        }else if($('select[name="socialdb_propertyterm_'+property_id+'"]').is('select')){
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
            //show_modal_main();
            $('#append_properties_categories_'+property_id).html('');
             $('#append_properties_categories_'+property_id)
                     .html('<center><img width="100" heigth="100" src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>"><?php _e('Loading metadata for this field','tainacan') ?></center>');
            $.ajax({
                url: $('#src').val() + '/controllers/object/object_controller.php',
                type: 'POST',
                data: { <?php echo ($is_view_mode) ? 'is_view_mode:true,' : '' ?>operation: 'list_properties_categories_accordeon',properties_to_avoid:$('#properties_id').val(),categories: id, object_id:$('#object_id_edit').val(),isEdit:true}
            }).done(function (result) {
                console.log('568');
                //hide_modal_main();
                //list_all_objects(selKeys.join(", "), $("#collection_id").val());
               $('#append_properties_categories_'+property_id).html(result);
               insert_html_property_category(id,property_id);
            });
            $('#selected_categories').val(selected_categories.join(','));
        }
    }
    function insert_html_property_category(category_id,property_id){
        var flag = false;
        $ul = $("#text_accordion");
        $items = $("#text_accordion").children();
        $('#append_properties_categories_'+property_id).css('margin-top','15px');
        $properties_append = $('#append_properties_categories_'+property_id).children().children();
        //$properties_append.animate({borderWidth : '1px',borderColor: 'red',borderStyle: 'dotted'}, 'slow', 'linear');
        setTimeout(removeBorderCat(property_id),8000);
        for (var i = 0; i <$properties_append.length; i++) {
              // index is zero-based to you have to remove one from the values in your array
                for(var j = 0; j<$items.length;j++){
                    if($($items.get(j)).attr('id')&&$($items.get(j)).attr('id')===$($properties_append.get(i)).attr('id')){
                        flag = true;
                        if(!$($items.get(j)).hasClass('category-'+category_id)){
                            $($items.get(j)).addClass('category-'+category_id);
                        }
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
         if($(".multiple-items-accordion")){
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
        }
         $('[data-toggle="tooltip"]').tooltip();
    }
    //retira as bordas
    function removeBorderCat(property_id){
        $properties_append = $('#append_properties_categories_'+property_id).children().children();
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
       
//################################ Cardinalidade #################################//    
    function show_fields_metadata_cardinality(property_id,id){
        $('#button_property_'+property_id+'_'+id).hide();
        $('#button_cancel_property_'+property_id+'_'+id).hide();
        $('#container_field_'+property_id+'_'+(id+1)).show();     
        $('#button_property_'+property_id+'_'+(id+1)).show();
        $('#button_cancel_property_'+property_id+'_'+(id+1)).show();
    }
     function hide_fields_metadata_cardinality(property_id,id){
        if(id>0){
             $('#button_property_'+property_id+'_'+(id-1)).show();
             $('#button_cancel_property_'+property_id+'_'+(id-1)).show();
            $('#button_property_'+property_id+'_'+id).hide();
            $('#button_cancel_property_'+property_id+'_'+id).hide();  
            $('#container_field_'+property_id+'_'+(id-1)).show();     
            $('#container_field_'+property_id+'_'+(id)).hide();     
        }
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
//################################ VALIDACOES##############################################//
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
    function edit_validate_selectbox(seletor,property_id){
        if($(seletor).val()===''){
            $('#core_validation_'+property_id).val('false');
            set_field_valid(property_id,'core_validation_'+property_id);
        }else{
            append_category_properties($(seletor).val(), $('#socialdb_propertyterm_'+property_id+'_value').val(), property_id);
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
    
    function validate_selectbox(seletor,property_id){
        if($(seletor).val()===''){
            $('#core_validation_'+property_id).val('false');
            set_field_valid(property_id,'core_validation_'+property_id);
        }else{
            $('#core_validation_'+property_id).val('true');
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
                    <?php if(!$is_view_mode): ?>
                    var id = $( this ).attr('id').replace('core_validation_','');
                    if(!$.isNumeric(id) && $('#fixed_id_'+id).length > 0){
                         var id =  $('#fixed_id_'+id).val();
                    }
                    $('#meta-item-'+id+' h2').css('background-color','#ffcccc');
                    $.each($( "#submit_form_edit_object .tab-pane" ),function(index,seletor){
                        if($(seletor).find('#meta-item-'+id).length > 0){
                            var id_tab = $(seletor ).attr('id').replace('tab-','');
                            $('#click-tab-'+id_tab).css('background-color','#ffcccc');
                        }
                    });
                    <?php endif; ?>
                }
            }
        });
        <?php if(!$is_view_mode): ?>
        $.each($( "#submit_form_edit_object .tab-pane" ),function(index,seletor){
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
        <?php endif; ?>
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
<?php 
if($is_view_mode): 
    include_once 'single_item_terms_properties_js.php';
 endif; 