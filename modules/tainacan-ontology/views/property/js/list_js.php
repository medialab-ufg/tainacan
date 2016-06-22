<script>
    $(function () {
       
        var src = $('#src').val();
        showDynatreesDomains(src);
        showPropertyCategoryDynatree(src);
        showTermsDynatree(src);//mostra o dynatree
        //lots of code
        show_modal_main();
        setTimeout(function(){  //Beginning of code that should run AFTER the timeout  
            <?php if($type=='socialdb_property_data'){ ?>
                 edit_data(<?php echo $property->term_id; ?>);
            <?php }else if($type=='socialdb_property_object'){ ?>     
                  edit_object(<?php echo $property->term_id; ?>);
             <?php }else if($type=='socialdb_property_term'){ ?>      
                  edit_term(<?php echo $property->term_id; ?>); 
             <?php } ?>      
        },700);  // put the timeout here
        $('#property_term_collection_id').val($('#collection_id').val());
        $('#property_data_collection_id').val($('#collection_id').val());// setando o valor da colecao no formulario
        $('#property_object_collection_id').val($('#collection_id').val());// setando o valor da colecao no formulario
      
  <?php // Submissao do form de property data     ?>
        $('#submit_form_property_data').submit(function (e) {
            e.preventDefault();
            $('#modalImportMain').modal('show');//mostra o modal de carregamento
            $.ajax({
                url: src + '/controllers/property/property_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//esconde o modal de carregamento
                elem = jQuery.parseJSON(result);
                clear_buttons();
                edit_data(<?php echo $property->term_id; ?>);
                if (elem.type === 'success') {
                    get_categories_properties_ordenation();
                     $("#alert_success_properties").hide();
                    $("#alert_error_properties").hide();
                    $("#alert_success_properties").show();
                } else {
                     $("#alert_success_properties").hide();
                    $("#alert_error_properties").hide();
                    $("#alert_error_properties").show();
                    if(elem.msg){
                         $("#default_message_error").hide();
                         $("#message_category").html(elem.msg);
                         $("#message_category").show();
                    }
                }
                $('.dropdown-toggle').dropdown();
                $('html, body').animate({
                    scrollTop: 0
                }, 900);
                
                load_menu_left($('#collection_id').val());
            });
            e.preventDefault();
        });
<?php // Submissao do form de property object     ?>
        $('#submit_form_property_object').submit(function (e) {
            e.preventDefault();
            $('#modalImportMain').modal('show');//mostra o modal de carregamento
            $.ajax({
                url: src + '/controllers/property/property_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//esconde o modal de carregamento
                elem = jQuery.parseJSON(result);
                clear_buttons();
                edit_object(<?php echo $property->term_id; ?>);
                if (elem.type === 'success') {
                    $("#alert_success_properties").hide();
                    $("#alert_error_properties").hide();
                    $("#alert_success_properties").show();
                } else {
                    $("#alert_success_properties").hide();
                    $("#alert_error_properties").hide();
                    $("#alert_error_properties").show();
                    if(elem.msg){
                         $("#default_message_error").hide();
                         $("#message_category").html(elem.msg);
                         $("#message_category").show();
                    }
                }
                $('.dropdown-toggle').dropdown();
                $('html, body').animate({
                    scrollTop: 0
                }, 900);
                load_menu_left($('#collection_id').val());
            });
            e.preventDefault();
        });
<?php // Submissao do form de property term     ?>
        $('#submit_form_property_term').submit(function (e) {
            e.preventDefault();
            $('#modalImportMain').modal('show');//mostra o modal de carregamento
            $.ajax({
                url: src + '/controllers/property/property_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//esconde o modal de carregamento
                elem = jQuery.parseJSON(result);
                clear_buttons();
                edit_term(<?php echo $property->term_id; ?>);
                if (elem.type === 'success') {
                    $("#alert_success_properties").hide();
                    $("#alert_error_properties").hide();
                    $("#alert_success_properties").show();
                } else {
                    $("#alert_success_properties").hide();
                    $("#alert_error_properties").hide();
                    $("#alert_error_properties").show();
                    if(elem.msg){
                         $("#default_message_error").hide();
                         $("#message_category").html(elem.msg);
                         $("#message_category").show();
                    }
                }
                $('.dropdown-toggle').dropdown();
                $('html, body').animate({
                    scrollTop: 0
                }, 900);
            });
            load_menu_left($('#collection_id').val());
            e.preventDefault();
        });
<?php // Submissao do form de para remocao     ?>
        $('#submit_delete_property').submit(function (e) {
            e.preventDefault();
            $("#modal_remove_property").modal('hide');
            $('#modalImportMain').modal('show');//mostra o modal de carregamento
            $.ajax({
                url: src + '/controllers/property/property_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//esconde o modal de carregamento
                elem = jQuery.parseJSON(result);
                clear_buttons();
                list_property_data();
                list_property_object();
                list_property_terms();
               if (elem.type === 'success') {
                    $("#alert_success_properties").hide();
                    $("#alert_error_properties").hide();
                    $("#alert_success_properties").show();
                } else {
                    $("#alert_success_properties").hide();
                    $("#alert_error_properties").hide();
                    $("#alert_error_properties").show();
                    if(elem.msg){
                         $("#default_message_error").hide();
                         $("#message_category").html(elem.msg);
                         $("#message_category").show();
                    }
                }
                $('.dropdown-toggle').dropdown();
                $('html, body').animate({
                    scrollTop: 0
                }, 900);
            });
            e.preventDefault();
        });


        $('#click_property_object_tab').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
        });
        $('#click_property_data_tab').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
        });
        $('#click_property_term_tab').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
        // reverse property
        $("#property_object_category_id").change(function (e) {
            $('#show_reverse_properties').hide();
            $('#property_object_is_reverse_false').prop('checked', true);
        });
        // reverse property    
        $('#property_object_is_reverse_true').click(function (e) {
            list_reverses();
            $('#show_reverse_properties').show();
        });
        //reverse property  
        $('#property_object_is_reverse_false').click(function (e) {
            $('#show_reverse_properties').hide();
        });
        // cardinality type 1  
        $('#socialdb_property_term_cardinality_1').click(function (e) {
            $('#socialdb_property_term_widget').html('');
            $('#socialdb_property_term_widget').append('<option value="tree"><?php _e('Tree','tainacan') ?></option>');
            $('#socialdb_property_term_widget').append('<option value="radio"><?php _e('Radio','tainacan') ?></option>');
            $('#socialdb_property_term_widget').append('<option value="selectbox"><?php _e('Selectbox','tainacan') ?></option>');
        });
        // cardinality type n  
        $('#socialdb_property_term_cardinality_n').click(function (e) {
            $('#socialdb_property_term_widget').html('');
            $('#socialdb_property_term_widget').append('<option value="checkbox"><?php _e('Checkbox','tainacan') ?></option>');
            $('#socialdb_property_term_widget').append('<option value="multipleselect"><?php _e('Multipleselect ','tainacan') ?></option>');
            $('#socialdb_property_term_widget').append('<option value="tree_checkbox"><?php _e('Tree - Checkbox','tainacan') ?></option>');
        });

        $('#socialdb_property_term_cardinality_1').trigger('click');
        
         
    });

<?php // lista as propriedades da categoria que foi selecionada     ?>
    function list_reverses(selected) {
        if($("#property_object_category_id").val()!==''){
            $.ajax({
                url: $('#src').val() + '/controllers/property/property_controller.php',
                type: 'POST',
                data: {collection_id: $("#collection_id").val(), category_id: $("#property_object_category_id").val(), operation: 'show_reverses', property_id: $('#property_category_id').val()}
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                $('#property_object_reverse').html('');
                if (elem.no_properties === false) {
                    $('#property_object_reverse').html('<option value=""><?php _e('None','tainacan') ?></option>');
                    $.each(elem.property_object, function (idx, property) {
                        //console.log(property.id,selected);
                        if (property.id == selected) {
                            $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        } else {
                            $('#property_object_reverse').append('<option value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                        }
                    });
                } else {
                    $('#property_object_reverse').append('<option value="false"><?php _e('No properties added','tainacan'); ?></option>');
                }
            });
        }
    }
<?php // edicao das propriedades de atributo     ?>
    function edit_data(id) {
        $.ajax({
            url: $('#src').val() + '/controllers/property/property_controller.php',
            type: 'POST',
            data: {collection_id: $("#collection_id").val(), operation: 'edit_property_data', property_id: id}
        }).done(function (result) {
            
            hide_modal_main();
            elem = jQuery.parseJSON(result);
           // $("#property_data_title").html('<a onclick="toggleSlide(\'submit_form_property_data\',\'list_properties_data\');" style="cursor: pointer;">'+'<?php _e('Edit property','tainacan') ?>'+'<span class="glyphicon glyphicon-triangle-bottom"></span></a>');
            $("#property_data_title").html(''+'<?php _e('Edit property','tainacan') ?>'+'');
            $("#property_data_id").val(elem.id);
            $("#property_data_name").val(elem.name);
            //dominio da propriedade
            var created_categories = elem.metas.socialdb_property_created_category;
            var used_by_categories = elem.metas.socialdb_property_used_by_categories;
            if(created_categories||used_by_categories){
                if($("#property_category_dynatree_data_domain")){
                       $("#property_category_dynatree_data_domain").dynatree("getRoot").visit(function (node) {
                               node.select(false);
                       });
                       $('#property_data_domain_category_id').val('');
                       $("#property_category_dynatree_data_domain").dynatree("getRoot").visit(function (node) {
                           if((created_categories&&created_categories.constructor === Array&&created_categories.indexOf(node.data.key)>-1)||
                                   (created_categories&&created_categories==node.data.key)||
                                   (used_by_categories&&used_by_categories.constructor=== Array&&used_by_categories.indexOf(node.data.key)>-1)){
                                    node.select(true);
                                    ids = $('#property_data_domain_category_id').val().split(',');
                                    index = ids.indexOf(node.data.key);
                                    if(index<0){
                                        ids.push(node.data.key);
                                       $('#property_data_domain_category_id').val(ids.join(','));
                                    }
                               }
                       });
                }
            }
            $("#property_data_widget").val(elem.metas.socialdb_property_data_widget);
            $("#socialdb_property_data_help").val(elem.metas.socialdb_property_help);
            $("#socialdb_property_data_default_value").val(elem.metas.socialdb_property_default_value);
            if (elem.metas.socialdb_property_data_column_ordenation === 'false') {
                $("#property_data_column_ordenation_false").prop('checked', true);
            } else {
                $("#property_data_column_ordenation_true").prop('checked', true);
            }
            if (elem.metas.socialdb_property_required === 'false') {
                $("#property_data_required_false").prop('checked', true);
            } else {
                $("#property_data_required_true").prop('checked', true);
            }
               
            $("#operation_property_data").val('update_property_data');
            
            <?php if(has_action('javascript_set_new_fields_edit_property_data')): ?>
                <?php do_action('javascript_set_new_fields_edit_property_data') ?>
            <?php endif; ?>
             
        });
    }
<?php // edicao das propriedades de objeto     ?>
    function edit_object(id) {
        $.ajax({
            url: $('#src').val() + '/controllers/property/property_controller.php',
            type: 'POST',
            data: {collection_id: $("#collection_id").val(), operation: 'edit_property_object', property_id: id}
        }).done(function (result) {
            
            hide_modal_main();
            elem = jQuery.parseJSON(result);
            //$("#property_object_title").html('<a onclick="toggleSlide(\'submit_form_property_object\',\'list_properties_object\');" style="cursor: pointer;">'+'<?php _e('Edit property','tainacan') ?>'+'<span class="glyphicon glyphicon-triangle-bottom"></span></a>');
            $("#property_object_title").html(''+'<?php _e('Edit property','tainacan') ?>'+'');
            $("#property_object_id").val(elem.id);
            $("#property_object_name").val(elem.name);
            //dominio da propriedade
            var created_categories = elem.metas.socialdb_property_created_category;
            var used_by_categories = elem.metas.socialdb_property_used_by_categories;
            if(created_categories||used_by_categories){
                if($("#property_category_dynatree_object_domain")){
                       $("#property_category_dynatree_object_domain").dynatree("getRoot").visit(function (node) {
                               node.select(false);
                       });
                       $('#property_object_domain_category_id').val('');
                       $("#property_category_dynatree_object_domain").dynatree("getRoot").visit(function (node) {
                           if((created_categories&&created_categories.constructor === Array&&created_categories.indexOf(node.data.key)>-1)||
                                   (created_categories&&created_categories==node.data.key)||
                                   (used_by_categories&&used_by_categories.constructor=== Array&&used_by_categories.indexOf(node.data.key)>-1)){
                                    node.select(true);
                                    ids = $('#property_object_domain_category_id').val().split(',');
                                    index = ids.indexOf(node.data.key);
                                    if(index<0){
                                        ids.push(node.data.key);
                                       $('#property_object_domain_category_id').val(ids.join(','));
                                    }
                               }
                       });
                }
            }
           //relacionamento da propriedade de objeto
           // console.log(elem.metas.socialdb_property_object_category_id.constructor ===Array);
            if(elem.metas.socialdb_property_object_category_id.constructor === Array){
               //  console.log('first');
                if($("#property_category_dynatree")){
                       $("#property_category_dynatree").dynatree("getRoot").visit(function (node) {
                               node.select(false);
                       });
                       $("#property_category_dynatree").dynatree("getRoot").visit(function (node) {
                               if(elem.metas.socialdb_property_object_category_id.indexOf(node.data.key)>-1){
                                    node.select(true);
                                    ids = $('#property_object_category_id').val().split(',');
                                    index = ids.indexOf(node.data.key);
                                    if(index<0){
                                        ids.push(node.data.key);
                                       $('#property_object_category_id').val(ids.join(','));
                                    }
                               }
                       });
                }
            }else if(elem.metas.socialdb_property_object_category_id){
               //  console.log('second');
                 if($("#property_category_dynatree")){
                       $("#property_category_dynatree").dynatree("getRoot").visit(function (node) {
                               node.select(false);
                       });
                       $("#property_category_dynatree").dynatree("getRoot").visit(function (node) {
                               if(elem.metas.socialdb_property_object_category_id===node.data.key){
                                    node.select(true);
                                    ids = $('#property_object_category_id').val().split(',');
                                    index = ids.indexOf(node.data.key);
                                    if(index<0){
                                        ids.push(node.data.key);
                                       $('#property_object_category_id').val(ids.join(','));
                                    }
                               }
                       });
                }
            }
            //se for faceta
            if (elem.metas.socialdb_property_object_is_facet === 'false') {
                $("#property_object_facet_false").prop('checked', true);
            } else {
                $("#property_object_facet_true").prop('checked', true);
            }
            if (elem.metas.socialdb_property_object_is_reverse === 'false') {
                $("#property_object_is_reverse_false").prop('checked', true);
                $('#show_reverse_properties').hide();
            } else {
                $("#property_object_is_reverse_true").prop('checked', true);
                list_reverses(elem.metas.socialdb_property_object_reverse);
                $('#show_reverse_properties').show();
            }
            if (elem.metas.socialdb_property_required === 'false') {
                $("#property_object_required_false").prop('checked', true);
            } else {
                $("#property_object_required_true").prop('checked', true);
            }
            $("#operation_property_object").val('update_property_object');
            <?php if(has_action('javascript_set_new_fields_edit_property_object')): ?>
                <?php do_action('javascript_set_new_fields_edit_property_object') ?>
            <?php endif; ?>
        });
    }
    function edit_term(id) {
        $.ajax({
            url: $('#src').val() + '/controllers/property/property_controller.php',
            type: 'POST',
            data: {collection_id: $("#collection_id").val(), operation: 'edit_property_term', property_id: id}
        }).done(function (result) {
            hide_modal_main();
            elem = jQuery.parseJSON(result);
            $("#property_term_title").text('<?php _e('Edit property','tainacan') ?>');
            $("#property_term_id").val(elem.id);
            $("#property_term_name").val(elem.name);
             //dominio da propriedade
             console.log(elem);
            var created_categories = elem.metas.socialdb_property_created_category;
            var used_by_categories = elem.metas.socialdb_property_used_by_categories;
            if(created_categories||used_by_categories){
                if($("#property_category_dynatree_term_domain")){
                       $("#property_category_dynatree_term_domain").dynatree("getRoot").visit(function (node) {
                               node.select(false);
                       });
                       $('#property_term_domain_category_id').val('');
                       $("#property_category_dynatree_term_domain").dynatree("getRoot").visit(function (node) {
                           if((created_categories&&created_categories.constructor === Array&&created_categories.indexOf(node.data.key)>-1)||
                                   (created_categories&&created_categories==node.data.key)||
                                   (used_by_categories&&used_by_categories.constructor=== Array&&used_by_categories.indexOf(node.data.key)>-1)){
                                    node.select(true);
                                    ids = $('#property_term_domain_category_id').val().split(',');
                                    index = ids.indexOf(node.data.key);
                                    if(index<0){
                                        ids.push(node.data.key);
                                       $('#property_term_domain_category_id').val(ids.join(','));
                                    }
                               }
                       });
                }
            }
            //cardinalidade
            if (elem.metas.socialdb_property_term_cardinality === '1') {
                $('#socialdb_property_term_cardinality_1').trigger('click');
            } else {
                $("#socialdb_property_term_cardinality_n").trigger('click');
            }
            if (elem.metas.socialdb_property_required === 'false') {
                $("#property_term_required_false").prop('checked', true);
            } else {
                $("#property_term_required_true").prop('checked', true);
            }
            if(elem.metas.socialdb_property_help){
                $("#socialdb_property_help").val(elem.metas.socialdb_property_help);
            }
            
            if (elem.metas.socialdb_property_term_root) {
                get_category_root_name(elem.metas.socialdb_property_term_root);
            }
            $("#socialdb_property_term_widget").val(elem.metas.socialdb_property_term_widget);
            $("#operation_property_term").val('update_property_term');
        });
    }
<?php // excluir propriedades     ?>
    function delete_property(element,type) {
        $("#property_delete_collection_id").val($("#collection_id").val());
        var id = $(element).closest('td').find('.property_id').val();
        var name = $(element).closest('td').find('.property_name').val();
        $("#property_delete_id").val(id);
        $("#deleted_property_name").text(name);
        $("#type_metadata_form").val(type);
        $("#modal_remove_property").modal('show');

    }
<?php //limpar apenas o campo de relacao       ?>
    function clear_relation() {
        $("#property_object_category_id").val('');
        $("#property_object_category_name").val('');
    }
<?php //fechar alerts apos form       ?>
    function hide_alert(seletor) {
        $("#"+seletor).hide();
    }
<?php //limpar botoes formulario       ?>
    function clear_buttons() {
        $('#show_reverse_properties').hide();
        //$("#property_data_title").html('<a onclick="toggleSlide(\'submit_form_property_data\',\'list_properties_data\');" style="cursor: pointer;">'+'<?php _e('Add new property','tainacan') ?>'+'<span class="glyphicon glyphicon-triangle-bottom"></span></a>');
        $("#property_data_title").html(''+'<?php _e('Add new property','tainacan') ?>'+'');
        $("#property_data_id").val('');
        $("#property_data_name").val('');
        $("#property_data_widget").val('');
        $("#property_data_column_ordenation_false").prop('checked', true);
        $("#property_data_required_false").prop('checked', true);
        $('#submit_form_property_data').parents('form').find('input[type=text],textarea,select').filter(':visible').val('');
       // $('#submit_form_property_data').parents('form').find('input[type=checkbox],input[type=radio]').filter(':visible').val('');
        
       // $("#property_object_title").html('<a onclick="toggleSlide(\'submit_form_property_object\',\'list_properties_object\');" style="cursor: pointer;">'+'<?php _e('Add new property','tainacan') ?>'+'<span class="glyphicon glyphicon-triangle-bottom"></span></a>');;
        $("#property_object_title").html(''+'<?php _e('Add new property','tainacan') ?>'+'');;
        $("#property_object_id").val('');
        $("#property_object_name").val('');
        $("#property_object_category_id").val('');
        $("#property_object_facet_false").prop('checked', true);
        $("#property_object_is_reverse_false").prop('checked', true);
        $("#property_object_required_false").prop('checked', true);

       // $("#property_term_title").html('<a onclick="toggleSlide(\'submit_form_property_term\',\'list_properties_term\');" style="cursor: pointer;">'+'<?php _e('Add new property','tainacan') ?>'+'<span class="glyphicon glyphicon-triangle-bottom"></span></a>');;
        $("#property_term_title").html(''+'<?php _e('Add new property','tainacan') ?>'+'');;
       
        $("#property_term_id").val('');
        $("#property_term_name").val('');
        
       // $('#default_field').show();
        // $('#required_field').show();
        <?php do_action('javascript_clear_forms');  ?>
        $("#operation_property_data").val('add_property_data');
        $("#operation_property_object").val('add_property_object');
        $("#operation_property_term").val('add_property_term');
        $('#submit_form_property_term').parents('form').find('input[type=text],textarea,select').filter(':visible').val('');
    }
<?php //vincular categorias com a colecao (facetas)       ?>
    function add_facets() {
        var selKeys = $.map($("#categories_dynatree").dynatree("getSelectedNodes"), function (node) {
            return node.data.key;
        });
        var selectedCategories = selKeys.join(",");
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/category/category_controller.php",
            data: {collection_id: $('#category_collection_id').val(), operation: 'vinculate_facets', facets: selectedCategories}
        }).done(function (result) {
            $('.dropdown-toggle').dropdown();
            $("#categories_dynatree").dynatree("getTree").reload();
            elem = jQuery.parseJSON(result);
            if (elem.success === 'true') {
                $("#alert_success_categories").toggle();
            } else {
                $("#alert_error_categories").toggle();
            }
        });
    }

    function showPropertyCategoryDynatree(src) {
        $("#property_category_dynatree").dynatree({
            selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
            checkbox: true,
            initAjax: {
                 url: src + '/controllers/collection/collection_controller.php',
                data: {
                    collection_id: $("#collection_id").val(),
                    operation: 'initDynatreeSingleEdit',
                    hideCheckbox: 'false'
                }
                , addActiveKey: true
            },
            onLazyRead: function (node) {
                node.appendAjax({
                    url: src + '/controllers/category/category_controller.php',
                    data: {
                        collection_id: $("#collection_id").val(),
                        category_id: node.data.key,
                        operation: 'findDynatreeChild'
                    }
                });
            },
            onClick: function (node, event) {
                // Close menu on click
                //$("#property_object_category_id").val(node.data.key);
                //$("#property_object_category_name").val(node.data.title);

            },
            onSelect: function (flag, node) {
                concatenate_in_array(node.data.key,'#property_object_category_id');
                <?php if(has_action('javascript_onselect_relationship_dynatree_property_object')): ?>
                    <?php do_action('javascript_onselect_relationship_dynatree_property_object') ?>
                <?php endif; ?>
                    
                console.log($('#property_object_category_id').val());
            }
        });
    }


    function showTermsDynatree(src) {
        $("#terms_dynatree").dynatree({
            checkbox: true,
            // Override class name for checkbox icon:
            classNames: {checkbox: "dynatree-radio"},
            selectMode: 1,
            selectionVisible: true, // Make sure, selected nodes are visible (expanded). 
            initAjax: {
                url: src + '/controllers/category/category_controller.php',
                data: {
                    collection_id: $("#collection_id").val(),
                    operation: 'initDynatreeTerms'
                }
                , addActiveKey: true
            },
            onLazyRead: function (node) {
                node.appendAjax({
                    url: src + '/controllers/category/category_controller.php',
                    data: {
                        collection_id: $("#collection_id").val(),
                        category_id: node.data.key,
                        classCss: node.data.addClass,
                        //hide_checkbox: 'true',
                        operation: 'findDynatreeChild'
                    }
                });
                $('.dropdown-toggle').dropdown();
            },
            onClick: function (node, event) {
                // Close menu on click
//                $.ajax({
//                    type: "POST",
//                    url: $('#src').val() + "/controllers/category/category_controller.php",
//                    data: {collection_id: $('#collection_id').val(), operation: 'verify_has_children', category_id: node.data.key}
//                }).done(function (result) {
//                    $('.dropdown-toggle').dropdown();
//                    elem_first = jQuery.parseJSON(result);
//                    if (elem_first.type === 'error') {
//                        showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
//                    } else {
//                        showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
//                        $("#socialdb_property_term_root").html('');
//                        $("#socialdb_property_term_root").append('<option selected="selected" value="' + node.data.key + '">' + node.data.title + '</option>');
//
//                    }
//
//                });
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
                // bindContextMenu(span);
            },
            onPostInit: function (isReloading, isError) {
                //$('#parentCat').val("Nenhum");
                //$( "#btnExpandAll" ).trigger( "click" );
            },
            onActivate: function (node, event) {
                // Close menu on click
                if ($(".contextMenu:visible").length > 0) {
                    $(".contextMenu").hide();
                    //          return false;
                }
            },
            onSelect: function (flag, node) {
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/category/category_controller.php",
                    data: {collection_id: $('#collection_id').val(), operation: 'verify_has_children', category_id: node.data.key}
                }).done(function (result) {
                    console.log($("#socialdb_property_term_root").val());
                    $('.dropdown-toggle').dropdown();
                    elem_first = jQuery.parseJSON(result);
                    if (elem_first.type === 'error') {
                        showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                        node.select(false);
                    } else if($("#socialdb_property_term_root").val()=='null'||$("#socialdb_property_term_root").val()!=node.data.key) {
                        showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
                        $("#socialdb_property_term_root").html('');
                        $("#socialdb_property_term_root").append('<option selected="selected" value="' + node.data.key + '">' + node.data.title + '</option>');

                    }

                });
            },
            dnd: {
                preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.     
                revert: false, // true: slide helper back to source if drop is rejected
                onDragStart: function (node) {
                    /** This function MUST be defined to enable dragging for the tree.*/

                    logMsg("tree.onDragStart(%o)", node);
                    if (node.data.isFolder) {
                        return false;
                    }
                    return true;
                },
                onDragStop: function (node) {
                    logMsg("tree.onDragStop(%o)", node);
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

    function get_category_root_name(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/category/category_controller.php",
            data: {operation: 'get_category_root_name', category_id: id}
        }).done(function (result) {
            $('.dropdown-toggle').dropdown();
            elem_first = jQuery.parseJSON(result);
            $("#socialdb_property_term_root").html('');
            $("#socialdb_property_term_root").append('<option selected="selected" value="' + elem_first.key + '">' + elem_first.title + '</option>');
        });
    }

    function showMetadataSchema() {
        $("#show_metadata_link").hide('slow');
        $("#hide_metadata_link").show('slow');
        $(".categories_menu").show('slow');
    }

    function hideMetadataSchema() {
        $("#properties_tabs").hide('slow');
        $("#hide_metadata_link").hide('slow');
        $("#show_metadata_link").show('slow');
    }

    function nextStep() {
        //$('#configuration').hide();
        //$('#configuration').html('');
        showRankingConfiguration('<?php echo get_template_directory_uri() ?>');
    }
    
    function hide_fields(e){
        if($(e).val()==='autoincrement'){
            $('#default_field').hide();
            $('#required_field').hide();
        }else{
            if($('#default_field')&&$('#required_field')){
                $('#default_field').show();
                $('#required_field').show();
            }
        }
    }
    /**
     * funcao que gera o autocomplete de popriedades 
     * @param {type} category
     * @param {type} type
     * @returns {undefined}
     */
    function autocomplete_metadata(category,type,selector){
        $("#property_"+type+"_name").autocomplete({
           // source: $('#src').val() + '/controllers/property/property_controller.php?operation=list_properties_autocomplete&category=' + category +'&type='+type,
            source: function( request, response ) {
                $.ajax({
                  url:  $('#src').val() + '/controllers/property/property_controller.php',
                  dataType: "json",
                  data: {
                      collection_id:$('#collection_id').val(),
                    q: request.term,
                    operation:'list_properties_autocomplete',
                    category:category,
                    type:type
                  },
                  success: function( data ) {
                    console.log(data);
                    response( data );
                  },error: function (qXHR,textStatus,errorThrown){
                      console.log(qXHR,textStatus,errorThrown);
                  }
                });
            },
            appendTo: "#"+selector,
            messages: {
                noResults: '',
                results: function () {
                }
            },
            minLength: 2,
            select: function (event, ui) {
                event.preventDefault();
                $("#property_"+type+"_name").val('');
                swal({
                    title: '<?php _e('Attention!','tainacan') ?>',
                    text: '<?php _e('Add the property','tainacan') ?>'+' ( '+ui.item.label+' )',
                    type: "info",
                    showCancelButton: true,
                    confirmButtonClass: 'btn-info',
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $('#modalImportMain').modal('show');//mostro o modal de carregamento
                        if(type=='data'){
                            call_event_property_data(category,ui.item.value);
                        }else if(type=='object'){
                            call_event_property_object(category,ui.item.value);
                        }else if(type=='term'){
                            call_event_property_term(category,ui.item.value);
                        }
                    }
                });
            }
        }); 
   }
   
   /**
    * funcao que realiza insercao do evento do tipo DATA
    * @param {type} category_id
    * @param {type} property_id
    * @returns {undefined}    */
   function call_event_property_data(category_id,property_id){
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/event/event_controller.php",
            data: {
                operation: 'add_event_property_data_create',
                socialdb_event_create_date: '<?php echo time(); ?>',
                socialdb_event_user_id: $('#current_user_id').val(),
                socialdb_event_property_data_create_category_root_id: category_id,
                socialdb_event_property_data_create_id:property_id,
                socialdb_event_collection_id: $('#collection_id').val()
             }
        }).done(function (result) {
            $('#modalImportMain').modal('hide');//escondo o modal de carregamento
            elem_first = jQuery.parseJSON(result);
            list_property_data();
            showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
        });
   }
   /**
    * funcao que realiza insercao do evento do tipo object
    * @param {type} category_id
    * @param {type} property_id
    * @returns {undefined}    */
   function call_event_property_object(category_id,property_id){
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/event/event_controller.php",
            data: {
                operation: 'add_event_property_object_create',
                socialdb_event_create_date: '<?php echo time(); ?>',
                socialdb_event_user_id: $('#current_user_id').val(),
                socialdb_event_property_object_create_category_root_id: category_id,
                socialdb_event_property_object_create_id:property_id,
                socialdb_event_collection_id: $('#collection_id').val()
             }
        }).done(function (result) {
            $('#modalImportMain').modal('hide');//escondo o modal de carregamento
            elem_first = jQuery.parseJSON(result);
            list_property_object();
            showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
        });
   }
   /**
    * funcao que realiza insercao do evento do tipo term
    * @param {type} category_id
    * @param {type} property_id
    * @returns {undefined}    */
   function call_event_property_term(category_id,property_id){
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/event/event_controller.php",
            data: {
                operation: 'add_event_property_term_create',
                socialdb_event_create_date: '<?php echo time(); ?>',
                socialdb_event_user_id: $('#current_user_id').val(),
                socialdb_event_property_term_create_category_root_id: category_id,
                socialdb_event_property_term_create_id:property_id,
                socialdb_event_collection_id: $('#collection_id').val()
             }
        }).done(function (result) {
            $('#modalImportMain').modal('hide');//escondo o modal de carregamento
            elem_first = jQuery.parseJSON(result);
            list_property_terms();
            showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
        });
   }
   
 //HELPERS
function toggleSlideProperties(target,reverse){
    if(!reverse){
        reverse = false;
    }
    if($("#"+target).is(":visible") == true){
        $("#"+target).slideUp();
        if(reverse!==false){
            $("#"+reverse).slideDown();
        }
    }else{
        $("#"+target).slideDown();
        if(reverse!==false){
            $("#"+reverse).slideUp();
        }
    }
}  
//dynatree para os domains
 function showDynatreesDomains(src) {
        $.ajax({
            type: "POST",
            url: src + '/controllers/collection/collection_controller.php',
            data: {
                   collection_id: $("#collection_id").val(),
                    operation: 'initDynatreeSingleEdit',
                    hideCheckbox: 'false'
                }
        }).done(function (result) {
            var json_propriedades = jQuery.parseJSON(result);
            //domain propriedade de objeto
            $("#property_category_dynatree_object_domain").empty();
            $("#property_category_dynatree_object_domain").dynatree({
                checkbox: true,
                // Override class name for checkbox icon:
                children: json_propriedades,
                onLazyRead: function (node) {
                    node.appendAjax({
                        url: src + '/controllers/category/category_controller.php',
                        data: {
                            collection_id: $("#collection_id").val(),
                            category_id: node.data.key,
                            operation: 'findDynatreeChild'
                        }
                    });
                },
                onSelect: function (flag, node) {
                    concatenate_in_array(node.data.key,'#property_object_domain_category_id');
                }
            });
            //domain propriedade de dados
            $("#property_category_dynatree_data_domain").empty();
            $("#property_category_dynatree_data_domain").dynatree({
                checkbox: true,
                // Override class name for checkbox icon:
                children: json_propriedades,
                onLazyRead: function (node) {
                    node.appendAjax({
                        url: src + '/controllers/category/category_controller.php',
                        data: {
                            collection_id: $("#collection_id").val(),
                            category_id: node.data.key,
                            operation: 'findDynatreeChild'
                        }
                    });
                },
                onSelect: function (flag, node) {
                    concatenate_in_array(node.data.key,'#property_data_domain_category_id');
                }
            });
            //domain propriedade de dados
            $("#property_category_dynatree_term_domain").empty();
            $("#property_category_dynatree_term_domain").dynatree({
                checkbox: true,
                // Override class name for checkbox icon:
                children: json_propriedades,
                onLazyRead: function (node) {
                    node.appendAjax({
                        url: src + '/controllers/category/category_controller.php',
                        data: {
                            collection_id: $("#collection_id").val(),
                            category_id: node.data.key,
                            operation: 'findDynatreeChild'
                        }
                    });
                },
                onSelect: function (flag, node) {
                    concatenate_in_array(node.data.key,'#property_term_domain_category_id');
                }
            });
        });
    }



</script>
