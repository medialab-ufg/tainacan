$(window).load(function () {
    setTimeout(function () {  //Beginning of code that should run AFTER the timeout  
        if ($('#open_wizard').val() === 'true') {
            $('.expand-all').trigger('click');
        }
    }, 3000);  // put the timeout here
});

/******************************* HOME DO ITEM **************************/
function get_classes_individuo(selector,item_id){
     $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'show_classifications', object_id: item_id}
        }).done(function (result) {
            $(selector).html(result).fadeIn();
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
}
/******************************************************************************/
/*
 *  funcao que abre o dynatree para as propriedades de uma colecao
 */
function initDynatreeFilterProperties(src) {
    $("#dynatree_properties_filter").dynatree({
        selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
        checkbox: false,
          initAjax: {
            url: src + '/controllers/filters/filters_controller.php',
            data: {
                collection_id: $("#collection_id").val(),
                hide_checkbox: 'true',
                order: 'name',
                operation: 'initDynatreePropertiesFilter'
            }
            , addActiveKey: true
        },
        onLazyRead: function (node) {
            node.appendAjax({
                url: src + '/controllers/filters/filters_controller.php',
                data: {
                    collection: $("#collection_id").val(),
                    key: node.data.key,
                    hide_checkbox: 'true',
                    //operation: 'findDynatreeChild'
                    operation: 'childrenDynatreePropertiesFilter'
                }
            });
        },
        onActivate: function (node, event) {
            $('#modalImportMain').modal('show');
                // Close menu on click
                var promisse = get_slug_property(node.data.key);
                promisse.done(function (result) {
                    elem = jQuery.parseJSON(result);                    
                    $('#modalImportMain').modal('hide');
                    showPageProperties(elem.slug, $('#src').val());
                    node.deactivate();
                });
        }, 
        onCreate: function (node, span) {
               bindContextProperty(span,src);
        }
    });
}
/** Context menu para as propriedades
 * 
 * @param {type} src
 * @returns {undefined}
 */
  function bindContextProperty(span,src) {
        // Add context menu to this node:
        $(span).contextMenu({menu: "PropertyMenu"}, function (action, el, pos) {
            var node = $.ui.dynatree.getNode(el);
            switch (action) {
                case 'add':
                    showPageCreateProperty(node.data.key,src);
                     break;
                case "edit":
                    showPageEditProperty(node.data.key,src);
                    break;
                 case "delete":
                     showDeleteProperty(node.data.key,node.data.title,src);
                    break;    
                default:
                    alert("Todo: appply action '" + action + "' to node " + node);
            }
        });
    }

/** Show page EDIT property **/
function showPageEditProperty(id,src){
    show_modal_main();
    $.ajax({
                        url: src + '/controllers/property/property_controller.php',
                        type: 'POST',
                        data: {operation: 'edit', property_id: id, collection_id: $("#collection_id").val()}
                    }).done(function (result) {
                        $("#menu_object").hide();
                        $("#container_socialdb").hide('slow');
                        $("#list").hide('slow');
                        $("#loader_objects").hide();            
                        $("#form").html(result);
                        $('#form').css('background','white');
                        $('#form').css('border','3px solid #E8E8E8');
                        $('#form').css('margin-left','-3px');
                        $('#form').css('height','2000px');
                        $('#form').css('border-top','none');
                        $('#form').show('slow');
                        //$('#single_category_property').html(result);
                        //$('#single_modal_category_property').modal('show');
                    });
}

/** Show page CREATE property **/
function showPageCreateProperty(parent,src){
    show_modal_main();
    $.ajax({
                        url: src + '/controllers/property/property_controller.php',
                        type: 'POST',
                        data: {operation: 'create', property_parent_id: parent, collection_id: $("#collection_id").val()}
                    }).done(function (result) {
                        $("#menu_object").hide();
                        $("#container_socialdb").hide('slow');
                        $("#list").hide('slow');
                        $("#loader_objects").hide();            
                        $("#form").html(result);
                        $('#form').css('background','white');
                        $('#form').css('border','3px solid #E8E8E8');
                        $('#form').css('margin-left','-3px');
                        $('#form').css('height','2000px');
                        $('#form').css('border-top','none');
                        $('#form').show('slow');
                        //$('#single_category_property').html(result);
                        //$('#single_modal_category_property').modal('show');
                    });
}

function showDeleteProperty(property_id,title,src) {
    swal({
        title:  $('#title_delete_property').val(),
        text: $('#msg_delete_property').val()+' '+title,
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: 'btn-danger',
        closeOnConfirm: true,
        closeOnCancel: true
    },
    function (isConfirm) {
        if (isConfirm) {
            $('#modalImportMain').modal('show');//mostro o modal de carregamento
            $.ajax({
                type: "POST",
                url: src + "/controllers/property/property_controller.php",
                data: {
                    operation: 'delete',
                    property_id: property_id,
                    collection_id: $('#collection_id').val()}
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//escondo o modal de carregamento
                elem_first = jQuery.parseJSON(result);
                backToMainPage();
                set_containers_class($('#collection_id').val());
                showAlertGeneral(elem_first.title, elem_first.msg, elem_first.type);
            });
        }
    });
}

function ontology_get_property_type(src,property_id){
    return $.ajax({
                type: "POST",
                url: src + "/controllers/property/event_controller.php",
                data: {
                    operation: 'get_property_type',
                    property_id: property_id}
            });
}

/*
 *  funcao que abre o dynatree para importacao de taxonomia
 */
function initDynatreeSelectTaxonomy(src) {
    $("#dynatree_select_taxonomies").dynatree({
        checkbox: true,
        // Override class name for checkbox icon:
        classNames: {checkbox: "dynatree-radio"},
        selectMode: 1,
          initAjax: {
            url: src + '/controllers/category/ontology_category_controller.php',
            data: {
                collection_id: $("#collection_id").val(),
                hide_checkbox: 'true',
                operation: 'initDynatreeSelectTaxonomy'
            }
            , addActiveKey: true
        },
        onLazyRead: function (node) {
            node.appendAjax({
                url: src + '/controllers/category/ontology_category_controller.php',
                data: {
                    collection: $("#collection_id").val(),
                    key: node.data.key,
                    hide_checkbox: 'true',
                    //operation: 'findDynatreeChild'
                    operation: 'childrenDynatreeSelectTaxonomy'
                }
            });
        }
    });
    
    $("#dynatree_select_taxonomies").dynatree("getRoot").visit(function(node){
        node.select(false);
    });
}
/**
 * 
 * @returns {undefined}
 */
function show_select_taxonomy(){
   $('#another_option_category').show();
   $('#form_add_category').hide();
}
/**
 * 
 * @returns {undefined}
 */
function hide_select_taxonomy(){
   $('#another_option_category').hide();
   $('#form_add_category').show();
}
/*
 * funcao que vincula uma taxonomia com colecao atual
 */
function ontology_vinculate_taxonomy(src,title,msg) {
    var selKeys = $.map($("#dynatree_select_taxonomies").dynatree("getSelectedNodes"), function (node) {
        return node.data;
    });
    if (selKeys[0]) {
         $('#modalAddCategoria').modal('hide');
         swal({
            title: title,
            text: msg+' '+selKeys[0].title,
            type: "info",
            showCancelButton: true,
            confirmButtonClass: 'btn-primary',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: src + '/controllers/category/ontology_category_controller.php',
                    type: 'POST',
                    data: {
                        operation: 'add_facet',
                        taxonomy_id: selKeys[0].key,
                        collection_id: $("#collection_id").val()}
                }).done(function (result) {
                    json = JSON.parse(result);
                    showAlertGeneral(json.title, json.msg, 'info');
                    //funcao do core que da relaod no menu lateral
                     set_containers_class($('#collection_id').val());
                });
            }else{
                $('#modalAddCategoria').modal('show');
            }
        });        
    }
}

/*********************************** AXIOMAS ********************************************/

function reInitDynatree() {
    var src = $('#src').val();
    $('#dynatree_modal_edit').hide();
    $("#ontology_dynatree_modal_edit").dynatree({
        selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
        checkbox: true,
        initAjax: {
            url: src + '/controllers/collection/collection_controller.php',
            data: {
                collection_id: $("#collection_id").val(),
                operation: 'initDynatreeSingleEdit',
                hide_tag: 'true',
                hide_checkbox: 'true'
            }
            , addActiveKey: true
        },
        onLazyRead: function (node) {
            node.appendAjax({
                url: src + '/controllers/category/category_controller.php',
                data: {
                    category_id: node.data.key,
                    collection_id: $("#collection_id").val(),
                    classCss: node.data.addClass,
                    hide_checkbox: 'true',
                    operation: 'findDynatreeChild'
                }
            });
        },
        onClick: function (node, event) {
            if (node.data.key != $("#category_single_edit_id").val()) {
                $("#category_single_parent_id_edit").val(node.data.key);
                $("#category_single_parent_name_edit").val(node.data.title);
            }
        },
        onCreate: function (node, span) {
                ontologyBindContextMenu(span);
            },
        onActivate: function (node, event) {
            // Close menu on click
            if ($(".contextMenu:visible").length > 0) {
                $(".contextMenu").hide();
                //          return false;
            }
        },
        onSelect: function (flag, node) {
        }
    });
}

function ontologyBindContextMenu(span) {
        // Add context menu to this node:
        $(span).contextMenu({menu: "ontologyMenu"}, function (action, el, pos) {
            // The event was bound to the <span> tag, but the node object
            // is stored in the parent <li> tag
            var node = $.ui.dynatree.getNode(el);
            console.log(node.data.key);
            switch (action) {
                case "equivalentclassAdd":
                    ontology_insert_category(node.data.key,node.data.title,'equivalentclass');
                    break;
                case "disjointwithAdd":
                    ontology_insert_category(node.data.key,node.data.title,'disjointwith');
                    break;    
                case "unionof":
                    ontology_insert_category(node.data.key,node.data.title,'unionof');
                    break;    
                case "intersectionof":
                    ontology_insert_category(node.data.key,node.data.title,'intersectionof');
                    break;    
                case "complementof":
                    ontology_insert_category(node.data.key,node.data.title,'complementof');
                    break;    
            }
        });
}

function ontology_insert_category(id,name,type){
    var html = '<span id="'+type+'_'+id+'" style="margin:4px;padding:2px;background:#73AD21;color:white;">'+name+'<a style="color:white;" onclick="ontology_remove_category('+"'"+id+"'"+','+"'"+name+"'"+','+"'"+type+"'"+')"><span class="glyphicon glyphicon-remove"></span></a></div>';
    var values =  $('#add_'+type+'_ids').val().split(',');
    console.log(id,name,type);
    if(!values||values.indexOf(id)<0){
        if(!values){
           values = []; 
        }
        values.push(id);
        $('#'+type+'_categories_droppable').append(html);
        $('#add_'+type+'_ids').val(values.join(','));
    }
}

function ontology_remove_category(id,name,type){
    $('#'+type+'_'+id).remove();
     var values =  $('#add_'+type+'_ids').val().split(',');
     var index = values.indexOf(id);
    if(values&&index>-1){
         values.splice(index, 1);
         $('#add_'+type+'_ids').val(values.join(','));
    }
}

function clear_ontology_fields_category(){
    $('#equivalentclass_categories_droppable').html('');
    $('#disjointwith_categories_droppable').html('');
    $('#unionof_categories_droppable').html('');
    $('#intersectionof_categories_droppable').html('');
    $('#complementof_categories_droppable').html('');
    $('#edit_disjointwith_ids').val('');
    $('#edit_equivalentclass_ids').val('');
}

function set_fields_modal_categories(elem){
    if(elem.socialdb_category_disjointwith&&elem.socialdb_category_disjointwith.length>0){
        $.each(elem.socialdb_category_disjointwith, function (idx, category) {
              ontology_insert_category(category.term_id,category.name,'disjointwith');
        });
    }
    if(elem.socialdb_category_equivalentclass&&elem.socialdb_category_equivalentclass.length>0){
        $.each(elem.socialdb_category_equivalentclass, function (idx, category) {
              ontology_insert_category(category.term_id,category.name,'equivalentclass');
        });
    }
    if(elem.socialdb_category_unionof&&elem.socialdb_category_unionof.length>0){
        $.each(elem.socialdb_category_unionof, function (idx, category) {
              ontology_insert_category(category.term_id,category.name,'unionof');
        });
    }
    if(elem.socialdb_category_intersectionof&&elem.socialdb_category_intersectionof.length>0){
        $.each(elem.socialdb_category_intersectionof, function (idx, category) {
              ontology_insert_category(category.term_id,category.name,'intersectionof');
        });
    }
    if(elem.socialdb_category_complementof&&elem.socialdb_category_complementof.length>0){
        $.each(elem.socialdb_category_complementof, function (idx, category) {
              ontology_insert_category(category.term_id,category.name,'complementof');
        });
    }
}
/*********************************** FIM AXIOMAS ******************************/
/***********************Property data form ************************************/
function form_property_data_init(src){
    //iniciando as propriedades
    initDynatrees(src);
    $('#default_field').remove();
    $('#required_field').remove();
    //cardinalidade
    $('#select_1_cardinality').change(function (e) {
        if($('#select_1_cardinality').val()!=='socialdb_property_cardinalidality'&&$('#select_1_cardinality').val()!==''){
            $('#button_add_cardinality').show();
            if($('#select_1_cardinality').val()==='socialdb_property_mincardinalidality'){
                $("#select_2_cardinality option[value=socialdb_property_maxcardinalidality]").attr("selected","selected");
            }else{
                $("#select_2_cardinality option[value=socialdb_property_mincardinalidality]").attr("selected","selected");
            }
        }else{
            $('#button_add_cardinality').hide();
            $('#option_cardinality_field').hide();
            if($('#select_1_cardinality').val()===''){
                $("#data_field_1_cardinality").val('');
            }
        }
        e.preventDefault();
    });
    //adicionar nova cardinalidade
    $('#click_add_cardinality').click(function (e) {
        $('#option_cardinality_field').show();
        e.preventDefault();
    });
    
    //adicionar nova cardinalidade
    $('#data_no_parent').change(function (e) {
        $('#dynatree_property_data_parent').toggle();
    });
    
    //adicionar nova cardinalidade
    $('#select_restriction_1').change(function (e) {
        if($('#select_restriction_1').val()==''){
           $('#dynatree_data_restriction_1').fadeOut();
        }else{
            if($('#select_restriction_1').val()=='equivalentproperty'){
                $('#dynatree_data_restriction_1').fadeOut(); 
                $('#dynatree_data_restriction_1').fadeIn();
                $("#dynatree_data_restriction_1").dynatree("getRoot").visit(function (node) {
                        node.select(false);
                });
                $("#dynatree_data_restriction_1").dynatree("getRoot").visit(function (node) {
                        if($("#data_equivalentproperty_ids").val().split(',').indexOf(node.data.key)>-1){
                             node.select(true);
                        }
                });
            }
            
        }
    });
}

function initDynatrees(src){
    $.ajax({
        type: "POST",
        url: src + '/controllers/filters/filters_controller.php',
        data: {
                collection_id: $("#collection_id").val(),
                order: 'name',
                operation: 'restrictionsDynatreeProperties'
            }
    }).done(function (result) {
        var json_propriedades = jQuery.parseJSON(result);
        //dynatree_restriction_1 
        $("#dynatree_data_restriction_1").empty();
        $("#dynatree_data_restriction_1").dynatree({
            selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
            checkbox: true,
            children: json_propriedades,
            onSelect: function (flag, node) {
                     //equivalent properties
                if($('#select_restriction_1').val()=='equivalentproperty'){
                    set_values_restrictions(node.data.key,'#data_equivalentproperty_ids');
                }
                
            }
        });
    });
    $.ajax({
        type: "POST",
        url: src + '/controllers/filters/filters_controller.php',
        data: {
                collection_id: $("#collection_id").val(),
                order: 'name',
                operation: 'parentDataDynatreeProperties'
            }
    }).done(function (result) {
        var json_propriedades = jQuery.parseJSON(result);
        //parent
        $("#dynatree_property_data_parent").empty();
        $("#dynatree_property_data_parent").dynatree({
            checkbox: true,
            // Override class name for checkbox icon:
            classNames: {checkbox: "dynatree-radio"},
            selectMode: 1,
            children: json_propriedades,
            onSelect: function (flag, node) {
               
                //equivalent properties
                console.log(node);
                    $('#data_socialdb_property_parent').val(node.data.key);
            }
        });
    });
}

function set_fields_edit_property_data(elem){
    console.log(elem);
    //toggleSlide('submit_form_property_data','list_properties_data');
    $('#socialdb_property_data_description').val(elem.description);
    if(elem.parent!==0){
        $('#data_no_parent').removeAttr('checked');
        $("#dynatree_property_data_parent").show();
         $("#dynatree_property_data_parent").dynatree("getRoot").visit(function (node) {
                if (node.data.key==elem.parent) {
                    match = node;
                    node.select(true);
                }
        });
    }else{
        $('#data_no_parent').attr('checked','checked');
        $("#dynatree_property_data_parent").hide();
        $("#dynatree_property_data_parent").dynatree("getRoot").visit(function (node) {
                    node.select(false);
                
        });
    }
    //cardinality 
     $("#data_field_1_cardinality").val('');
    if(elem.metas.socialdb_property_cardinalidality&&elem.metas.socialdb_property_cardinalidality!==""){
        $("#select_1_cardinality option[value='socialdb_property_cardinalidality']").attr("selected","selected");
        $("#data_field_1_cardinality").val(elem.metas.socialdb_property_cardinalidality);
        $("#button_add_cardinality").hide();
        $("#option_cardinality_field").hide();
    }else{
        if(elem.metas.socialdb_property_mincardinalidality&&elem.metas.socialdb_property_mincardinalidality!==""){
            $("#select_1_cardinality option[value='socialdb_property_mincardinalidality']").attr("selected","selected");
            $("#data_field_1_cardinality").val(elem.metas.socialdb_property_mincardinalidality);
            if(elem.metas.socialdb_property_maxcardinalidality&&elem.metas.socialdb_property_maxcardinalidality!==""){
                    $("#button_add_cardinality").show();
                    $("#option_cardinality_field").show();
                    $("#select_2_cardinality option[value='socialdb_property_maxcardinalidality']").attr("selected","selected");
                    $("#data_field_2_cardinality").val(elem.metas.socialdb_property_maxcardinalidality);
            }
        }else if(elem.metas.socialdb_property_maxcardinalidality&&elem.metas.socialdb_property_maxcardinalidality!==""){
             $("#select_1_cardinality option[value='socialdb_property_maxcardinalidality']").attr("selected","selected");
               $("#data_field_1_cardinality").val(elem.metas.socialdb_property_maxcardinalidality);
        }
    }
    //functional
    if(elem.metas.socialdb_property_functional&&elem.metas.socialdb_property_functional==='true'){
         $("#property_data_functional").attr('checked','checked');
    }
    //restrictions (equivalent)
    if(elem.metas.socialdb_property_equivalent&&elem.metas.socialdb_property_equivalent.length>0){
        $("#select_restriction_1 option[value='equivalentproperty']").attr("selected","selected");
        $("#dynatree_data_restriction_1").show();
        $("#data_equivalentproperty_ids").val(elem.metas.socialdb_property_equivalent.join(','));
        $("#dynatree_data_restriction_1").dynatree("getRoot").visit(function (node) {
                node.select(false);
        });
        $("#dynatree_data_restriction_1").dynatree("getRoot").visit(function (node) {
                if(elem.metas.socialdb_property_equivalent.indexOf(node.data.key)>-1){
                     node.select(true);
                }
        });
    }
}
/****************************************************************************/
/***********************Property Object form ************************************/
function form_property_object_init(src){
    //iniciando as propriedades
    initDynatreesObject(src);
    initDynatreesObjectRestriction(src);
    $('#property_object_reverse').remove();
    $('#show_reverse_properties').remove();
    $('#default_field').remove();
    $('#required_field').remove();
    $('#property_object_reverse_ontology').attr('id','property_object_reverse');
    //cardinalidade
    $('#select_1_cardinality_object').change(function (e) {
        if($('#select_1_cardinality_object').val()!=='socialdb_property_cardinalidality'&&$('#select_1_cardinality_object').val()!==''){
            $('#button_add_cardinality_object').show();
            if($('#select_1_cardinality_object').val()==='socialdb_property_mincardinalidality'){
                $("#select_2_cardinality_object option[value=socialdb_property_maxcardinalidality]").attr("selected","selected");
            }else{
                $("#select_2_cardinality_object option[value=socialdb_property_mincardinalidality]").attr("selected","selected");
            }
        }else{
            $('#button_add_cardinality_object').hide();
            $('#option_cardinality_field_object').hide();
            if($('#select_1_cardinality_object').val()===''){
                $("#object_field_1_cardinality").val('');
            }
        }
        e.preventDefault();
    });
    //adicionar nova cardinalidade
    $('#click_add_cardinality_object').click(function (e) {
        $('#option_cardinality_field_object').show();
        e.preventDefault();
    });
    
    //adicionar nova cardinalidade
    $('#object_no_parent').change(function (e) {
        $('#dynatree_property_object_parent').toggle();
    });
    
    //adicionar nova restricao
    $('#select_restriction_1_object').change(function (e) {
        $('#dynatree_object_restriction_1').fadeOut(); 
        $('#dynatree_object_restriction_2').fadeOut(); 
        $('#dynatree_object_restriction_3').fadeOut(); 
        $('#dynatree_object_restriction_4').fadeOut(); 
        if($('#select_restriction_1_object').val()==''){
           
        }else{
            if($('#select_restriction_1_object').val()=='equivalentproperty'){
                $('#dynatree_object_restriction_1').fadeIn();
            } else if($('#select_restriction_1_object').val()=='allvaluesfrom'){
                $('#dynatree_object_restriction_2').fadeIn();
            }else if($('#select_restriction_1_object').val()=='somevaluesfrom'){
                $('#dynatree_object_restriction_3').fadeIn();
            }else if($('#select_restriction_1_object').val()=='hasvalue'){
                $('#dynatree_object_restriction_4').fadeIn();
            }
            
        }
    });
}
function initDynatreesObject(src){
    $.ajax({
        type: "POST",
        url: src + '/controllers/filters/filters_controller.php',
        data: {
                collection_id: $("#collection_id").val(),
                order: 'name',
                operation: 'restrictionsDynatreeProperties'
            }
    }).done(function (result) {
        var json_propriedades = jQuery.parseJSON(result);
        //dynatree_restriction_1 
        $("#dynatree_object_restriction_1").empty();
        $("#dynatree_object_restriction_1").dynatree({
            selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
            checkbox: true,
            children: json_propriedades,
            onSelect: function (flag, node) {
                     //equivalent properties
                if($('#select_restriction_1_object').val()=='equivalentproperty'){
                    set_values_restrictions(node.data.key,'#object_equivalentproperty_ids');
                } 
            }
        });
    });
    $.ajax({
        type: "POST",
        url: src + '/controllers/filters/filters_controller.php',
        data: {
                collection_id: $("#collection_id").val(),
                order: 'name',
                operation: 'parentObjectDynatreeProperties'
            }
    }).done(function (result) {
        var json_propriedades = jQuery.parseJSON(result);
        //parent
        $("#dynatree_property_object_parent").empty();
        $("#dynatree_property_object_parent").dynatree({
            checkbox: true,
            // Override class name for checkbox icon:
            classNames: {checkbox: "dynatree-radio"},
            selectMode: 1,
            children: json_propriedades,
            onSelect: function (flag, node) {
               
                //equivalent properties
                console.log(node);
                    $('#object_socialdb_property_parent').val(node.data.key);
            }
        });
    });
}
function initDynatreesObjectRestriction(src){
    $.ajax({
        type: "POST",
        url: $('#src').val() + '/controllers/collection/collection_controller.php',
                data: {
                    collection_id: $("#collection_id").val(),
                    operation: 'initDynatreeSingleEdit',
                    hideCheckbox: 'false'
                }
    }).done(function (result) {
        var json_propriedades = jQuery.parseJSON(result);
          //dynatree_restriction_2 
        $("#dynatree_object_restriction_2").empty();
        $("#dynatree_object_restriction_2").dynatree({
            selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
            checkbox: true,
            children: json_propriedades,
            onSelect: function (flag, node) {
                      if($('#select_restriction_1_object').val()=='allvaluesfrom'){
                    set_values_restrictions(node.data.key,'#object_allvaluesfrom_ids');
                }
            }
        });
          //dynatree_restriction_3 
        $("#dynatree_object_restriction_3").empty();
        $("#dynatree_object_restriction_3").dynatree({
            selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
            checkbox: true,
            children: json_propriedades,
            onSelect: function (flag, node) {
                     if($('#select_restriction_1_object').val()=='somevaluesfrom'){
                    set_values_restrictions(node.data.key,'#object_somevaluesfrom_ids');
                     }
                
            }
        });
    });
    
    //dynatree_restriction_4 HAS VALUE
    $("#dynatree_object_restriction_4").empty();
    $("#dynatree_object_restriction_4").dynatree({
         classNames: {checkbox: "dynatree-radio"},
            selectMode: 1,
        selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
        checkbox: true,
        initAjax: {
            url: src + '/controllers/filters/filters_controller.php',
            data: {
                collection_id: $("#collection_id").val(),
                order: 'name',
                operation: 'restrictionsDynatreeIndividues'
            },
            addActiveKey: true
        },
        onLazyRead: function (node) {
            node.appendAjax({
                url: $('#src').val() + '/controllers/collection/collection_controller.php',
                data: {
                    key: node.data.key,
                    collection: $("#collection_id").val(),
                    classCss: node.data.addClass,
                    operation: 'expand_dynatree'
                }
            });
        },
        onSelect: function (flag, node) {
            if ($('#select_restriction_1_object').val() == 'hasvalue') {
                set_values_restrictions(node.data.key, '#object_hasvalue_ids');
            }
        }
    });
}

function set_fields_edit_property_object(elem){
    console.log(elem);
   // toggleSlide('submit_form_property_object','list_properties_object');
    $('#socialdb_property_object_description').val(elem.description);
    if(elem.parent!==0){
        $('#object_no_parent').removeAttr('checked');
        $("#dynatree_property_object_parent").show();
         $("#dynatree_property_object_parent").dynatree("getRoot").visit(function (node) {
                if (node.data.key==elem.parent) {
                    match = node;
                    node.select(true);
                }
        });
    }else{
        $('#object_no_parent').attr('checked','checked');
        $("#dynatree_property_object_parent").hide();
        $("#dynatree_property_object_parent").dynatree("getRoot").visit(function (node) {
                    node.select(false);
                
        });
    }
    //cardinality 
     $("#object_field_1_cardinality").val('');
    if(elem.metas.socialdb_property_cardinalidality&&elem.metas.socialdb_property_cardinalidality!==""){
        $("#select_1_cardinality_object option[value='socialdb_property_cardinalidality']").attr("selected","selected");
        $("#object_field_1_cardinality").val(elem.metas.socialdb_property_cardinalidality);
        $("#button_add_cardinality_object").hide();
        $("#option_cardinality_field_object").hide();
    }else{
        if(elem.metas.socialdb_property_mincardinalidality&&elem.metas.socialdb_property_mincardinalidality!==""){
            $("#select_1_cardinality_object option[value='socialdb_property_mincardinalidality']").attr("selected","selected");
            $("#object_field_1_cardinality").val(elem.metas.socialdb_property_mincardinalidality);
            if(elem.metas.socialdb_property_maxcardinalidality&&elem.metas.socialdb_property_maxcardinalidality!==""){
                    $("#button_add_cardinality_object").show();
                    $("#option_cardinality_field_object").show();
                    $("#select_2_cardinality_object option[value='socialdb_property_maxcardinalidality']").attr("selected","selected");
                    $("#object_field_2_cardinality").val(elem.metas.socialdb_property_maxcardinalidality);
            }
        }else if(elem.metas.socialdb_property_maxcardinalidality&&elem.metas.socialdb_property_maxcardinalidality!==""){
             $("#select_1_cardinality_object option[value='socialdb_property_maxcardinalidality']").attr("selected","selected");
               $("#object_field_1_cardinality").val(elem.metas.socialdb_property_maxcardinalidality);
        }
    }
    //reverses
    if(elem.metas.socialdb_property_object_is_reverse&&elem.metas.socialdb_property_object_is_reverse==='true'){
        list_reverses(elem.metas.socialdb_property_object_reverse);
//         $("#property_object_reverse_ontology option[value='"+elem.metas.socialdb_property_object_reverse+"']").attr("selected","selected");
//         $("#property_object_reverse option[value='"+elem.metas.socialdb_property_object_reverse+"']").attr("selected","selected");
    }
    //functional
    if(elem.metas.socialdb_property_functional&&elem.metas.socialdb_property_functional==='true'){
         $("#property_object_functional").attr('checked','checked');
    }
    //transitive
    if(elem.metas.socialdb_property_transitive&&elem.metas.socialdb_property_transitive==='true'){
         $("#property_object_transitive").attr('checked','checked');
    }
    //simetric
    if(elem.metas.socialdb_property_simetric&&elem.metas.socialdb_property_simetric==='true'){
         $("#property_object_simetric").attr('checked','checked');
    }
    //restrictions (equivalent)
    if(elem.metas.socialdb_property_equivalent&&elem.metas.socialdb_property_equivalent.length>0){
        //$("#select_restriction_1 option[value='equivalentproperty']").attr("selected","selected");
        //$("#dynatree_object_restriction_1").show();
        $("#object_equivalentproperty_ids").val(elem.metas.socialdb_property_equivalent.join(','));
        $("#dynatree_object_restriction_1").dynatree("getRoot").visit(function (node) {
                node.select(false);
        });
        $("#dynatree_object_restriction_1").dynatree("getRoot").visit(function (node) {
                if(elem.metas.socialdb_property_equivalent.indexOf(node.data.key)>-1){
                     node.select(true);
                }
        });
    }
    //restrictions (allvaluesfrom)
    if(elem.metas.socialdb_property_allvaluesfrom&&elem.metas.socialdb_property_allvaluesfrom.length>0){
        $("#object_allvaluesfrom_ids").val(elem.metas.socialdb_property_allvaluesfrom.join(','));
        $("#dynatree_object_restriction_2").dynatree("getRoot").visit(function (node) {
                node.select(false);
        });
        $("#dynatree_object_restriction_2").dynatree("getRoot").visit(function (node) {
                if(elem.metas.socialdb_property_allvaluesfrom.indexOf(node.data.key)>-1){
                     node.select(true);
                }
        });
    }
    //restrictions (somevaluesfrom)
    if(elem.metas.socialdb_property_somevaluesfrom&&elem.metas.socialdb_property_somevaluesfrom.length>0){
      //  $("#dynatree_object_restriction_1").show();
        $("#object_somevaluesfrom_ids").val(elem.metas.socialdb_property_somevaluesfrom.join(','));
        $("#dynatree_object_restriction_3").dynatree("getRoot").visit(function (node) {
                node.select(false);
        });
        $("#dynatree_object_restriction_3").dynatree("getRoot").visit(function (node) {
                if(elem.metas.socialdb_property_somevaluesfrom.indexOf(node.data.key)>-1){
                     node.select(true);
                }
        });
    }
    //restrictions (hasvalue)
    if(elem.metas.socialdb_property_hasvalue&&elem.metas.socialdb_property_hasvalue.length>0){
       // $("#dynatree_object_restriction_1").show();
        $("#object_hasvalue_ids").val(elem.metas.socialdb_property_hasvalue.join(','));
        $("#dynatree_object_restriction_4").dynatree("getRoot").visit(function (node) {
                node.select(false);
        });
        $("#dynatree_object_restriction_4").dynatree("getRoot").visit(function (node) {
                if(elem.metas.socialdb_property_hasvalue.indexOf(node.data.key)>-1){
                     node.select(true);
                }
        });
    }
}

function onselect_relationship(key){
    list_reverses($('#property_object_category_id').val());
}
/************************************************************************************/
function set_values_restrictions(key,seletor){
    var ids = [];
    if($(seletor).val()!==''){
        ids = $(seletor).val().split(',');
        index = ids.indexOf(key);
        if(index>=0){
            ids.splice(index, 1);
        }else{
            ids.push(key);
        }
        $(seletor).val(ids.join(','));
    }else{
        ids.push(key);
        $(seletor).val(ids.join(','));
    }
}

// limpando o formulario de criacao de propriedades
function ontology_clear_forms(){
    //DATA
    $('#socialdb_property_data_description').val('');
    $('#data_no_parent').attr('checked','checked');
    $("#dynatree_property_data_parent").hide();
    $("#dynatree_property_data_parent").dynatree("getRoot").visit(function (node) {
                node.select(false);
    });
    $("#select_1_cardinality option[value='']").attr("selected","selected");
    $("input[name='field_1_cardinality']").val('');
    $("input[name='field_2_cardinality']").val('');
    $("#button_add_cardinality").hide();
    $("#option_cardinality_field").hide();
    
    $("#property_data_functional").removeAttr('checked');
    $("#select_restriction_1 option[value='']").attr("selected","selected");
    //restriction equivalent
    $("#dynatree_data_restriction_1").hide();
    $("#data_equivalentproperty_ids").val('');
    //OBJECT
    $('#socialdb_property_object_description').val('');
    $('#object_no_parent').attr('checked','checked');
    $("#dynatree_property_object_parent").dynatree("getRoot").visit(function (node) {
                node.select(false);
    });
    $("#dynatree_property_object_parent").hide();
    
    $("#select_1_cardinality_object option[value='']").attr("selected","selected");
    $("input[name='field_1_cardinality']").val('');
    $("input[name='field_2_cardinality']").val('');
    $("#button_add_cardinality_object").hide();
        $("#option_cardinality_field_object").hide();
    $('#property_object_category_id').val('');
    $("#property_category_dynatree").dynatree("getRoot").visit(function (node) {
            node.select(false);
    });
     $("#property_object_reverse_ontology option[value='']").attr("selected","selected");
     $("#property_object_reverse option[value='']").attr("selected","selected");
    $("#property_object_functional").removeAttr('checked');
    $("#property_object_transitive").removeAttr('checked');
    $("#property_object_simetric").removeAttr('checked');
    $("#select_restriction_1_object option[value='']").attr("selected","selected");
    $("#dynatree_object_restriction_1").dynatree("getRoot").visit(function (node) {
            node.select(false);
    });
    $("#dynatree_object_restriction_2").dynatree("getRoot").visit(function (node) {
            node.select(false);
    });
    $("#dynatree_object_restriction_3").dynatree("getRoot").visit(function (node) {
            node.select(false);
    });
    $("#dynatree_object_restriction_4").dynatree("getRoot").visit(function (node) {
            node.select(false);
    });
    
    $("#dynatree_object_restriction_1").hide();
    $("#dynatree_object_restriction_2").hide();
    $("#dynatree_object_restriction_3").hide();
    $("#dynatree_object_restriction_4").hide();
    $("#object_equivalentproperty_ids").val('');
    $("#object_allvaluesfrom_ids").val('');
    $("#object_somevaluesfrom_ids").val('');
    $("#object_hasvalue_ids").val('');
    $('#property_object_category_id').val('');
    
}
/******************************************************************************/


/*********************** #adicao/edicao de individuo - validacao de cardinalidade *************************/
// ## OBJECT
//VALIDA A PROPRIEDADE DE OBJETO COMPLETA
function verify_cardinality_property_object_field(seletor,property_id){
    var count = 0;
   
    if((!$("#form_group_"+property_id)||$("#form_group_"+property_id).length==0)
            &&(parseInt($('#property_'+property_id+'_min_cardinality').val())!==0
               ||$('#property_'+property_id+'_max_cardinality').val()!=='*') ){
        $( seletor ).wrap( "<div id='form_group_"+property_id+"'></div>" );
        $("#form_group_"+property_id).wrap( "<div id='field_container_"+property_id+"'></div>" );
        $('<span id="icon_'+property_id+'" class="glyphicon" aria-hidden="true"></span>').insertAfter(seletor);
        $('<span id="status_field_'+property_id+'" class="sr-only"></span>').insertAfter(seletor);
    }
    
    $( seletor+' option:selected' ).each(function( index ) {
            count++;
    });
    
    if(parseInt($('#property_'+property_id+'_min_cardinality').val())===0&&$('#property_'+property_id+'_max_cardinality').val()==='*'){
    
    }else{
        if(count>=parseInt($('#property_'+property_id+'_min_cardinality').val())&&count<=parseInt($('#property_'+property_id+'_max').val())){
            $('#form_group_'+property_id).attr('class','form-group has-success has-feedback') ;
            $('#icon_'+property_id).attr('class','glyphicon glyphicon-ok form-control-feedback') ;
            $('#status_field_'+property_id).html('(success)') ;

            $('#error-'+property_id).hide();
            $('#cardinality-obrigation-'+property_id).show();
            $('#correct-'+property_id).show();
            $('#property_validation_'+property_id).val('true') ; 

        }else{
            $('#form_group_'+property_id).attr('class','form-group has-warning has-feedback') ;
            $('#icon_'+property_id).attr('class','glyphicon glyphicon-warning-sign form-control-feedback') ;
            $('#status_field_'+property_id).html('(warning)') ;

            $('#error-'+property_id).show();
            $('#cardinality-obrigation-'+property_id).show();
            $('#correct-'+property_id).hide();
            $('#property_validation_'+property_id).val('false') ; 
        }
    }
    validate_all_properties();
}
// valida a cardinalidade ao selecionar
Hook.register(
  'tainacan_validate_cardinality_onselect',
  function ( args ) {
      verify_cardinality_property_object_field(args[0],args[1])
  });


//## DATA
//validando a cardinalidade na propriedade de dados EM CADA INPUT
function validation_cardinality_property_data(seletor,property_id,position){
    // se for cardinalidade fiza
    if(parseInt(position)<parseInt($('#property_'+property_id+'_min').val())&&parseInt($('#property_'+property_id+'_max').val())===parseInt($('#property_'+property_id+'_min').val())){
       var has_value= $('#'+seletor).val();
       if(has_value.trim()===''){
          $('#form_group_'+property_id+'_'+position).attr('class','form-group has-warning has-feedback') ;
          $('#icon_'+property_id+'_'+position).attr('class','glyphicon glyphicon-warning-sign form-control-feedback') ;
          $('#status_field_'+property_id+'_'+position).html('(warning)') ;
          $('#is_valid_'+property_id+'_'+position).val('false') ;
          $('#'+seletor).attr('placeholder',$('.obrigation-message-'+property_id).val());
          verify_cardinality_property_data_field(property_id);
       }else{
          $('#form_group_'+property_id+'_'+position).attr('class','form-group has-success has-feedback') ;
          $('#icon_'+property_id+'_'+position).attr('class','glyphicon glyphicon-ok form-control-feedback') ;
          $('#status_field_'+property_id+'_'+position).html('(success)') ;
          $('#is_valid_'+property_id+'_'+position).val('true') ; 
          verify_cardinality_property_data_field(property_id);
       }
   }
   //se for cardinalidade minima for igual ou maior a um
   else if(parseInt($('#property_'+property_id+'_min').val())>0&&parseInt($('#property_'+property_id+'_max').val())>parseInt($('#property_'+property_id+'_min').val())){
        var has_value= $('#'+seletor).val();
        if(has_value.trim()===''){
          $('#form_group_'+property_id+'_'+position).attr('class','form-group has-warning has-feedback') ;
          $('#icon_'+property_id+'_'+position).attr('class','glyphicon glyphicon-warning-sign form-control-feedback') ;
          $('#status_field_'+property_id+'_'+position).html('(warning)') ;
          $('#is_valid_'+property_id+'_'+position).val('false') ;
           $('#'+seletor).attr('placeholder',$('.optional-message-'+property_id).val());
           verify_cardinality_property_data_field(property_id);
       }else{
          $('#form_group_'+property_id+'_'+position).attr('class','form-group has-success has-feedback') ;
          $('#icon_'+property_id+'_'+position).attr('class','glyphicon glyphicon-ok form-control-feedback') ;
          $('#status_field_'+property_id+'_'+position).html('(success)') ;
          $('#is_valid_'+property_id+'_'+position).val('true') ; 
          verify_cardinality_property_data_field(property_id);
       }
   }else{
       $('#is_valid_'+property_id+'_'+position).val('true') ; 
       $('#property_validation_'+property_id).val('true') ; 
   }  
    validate_all_properties();
}
//VALIDA A PROPRIEDADE DE DADOS COMPLETA
function verify_cardinality_property_data_field(property_id){
    var count = 0;
    $( ".is_valid_"+property_id ).each(function( index ) {
        if($( this ).val()==='true'){
            count++;
        }
    });
    if(count>=parseInt($('#property_'+property_id+'_min').val())){
        $('#error-'+property_id).hide();
        $('#cardinality-obrigation-'+property_id).hide();
        $('#correct-'+property_id).show();
        $('#property_validation_'+property_id).val('true') ; 
    }else{
        $('#error-'+property_id).show();
        $('#cardinality-obrigation-'+property_id).show();
        $('#correct-'+property_id).hide();
        $('#property_validation_'+property_id).val('false') ; 
    }
}
//valida todas propriedades
function validate_all_properties(){
    var count = 0;
    $( ".validation_properties_cardinality").each(function( index ) {
        if($( this ).val()==='false'){
            count++;
        }
    });
    //se algum campo nao estiver validado
//     $(".send-button").tooltip({
//            title: 'Complete the form!',
//             placement : 'left',
//             trigger: 'click',
//            delay: 100
//        });
    if(count>0){
        $('.send-button').attr('disabled','disabled');
    }else{
       // $(".send-button").tooltip('destroy');
        $('.send-button').removeAttr('disabled');
    }
}
/*********************** #adicao/edicao de individuos *************************/
//validando o formulario de insercao de item
Hook.register(
  'tainacan_validate_create_item_form',
  function ( args ) {
      var number_of_values = 0; // contador para verificacao de quantidade de valores setados em uma propriedade
      var is_fixed = 0; // se eh um numero fixo de valores
      var minimum_values = 0; // o valor minimo aceitavel
      var maximum_values = 0; // o valor maximo permitido
      if($('#properties_object_ids').val()===undefined){
          Hook.result = {is_validated:true,message:''};
          return false;
      }
      var properties_object_id =  $('#properties_object_ids').val().split(',');
      //valindando as propriedades de objeto
      for(var i = 0;i<properties_object_id.length;i++){ // presorro todas propriedades de dados
        number_of_values = 0; // comeco a contagem
        is_fixed = $('#property_'+properties_object_id[i]+'_is_fixed').val(); // se eh um numero fixo de valores
        minimum_values = parseInt( $('#property_'+properties_object_id[i]+'_min_cardinality').val()); // o valor minimo aceitavel
        maximum_values = parseInt( $('#property_'+properties_object_id[i]+'_max_cardinality').val()); // o valor maximo permitido
        
          $.each(args[0],function(index,object){ // percorro todo o formulario
            if(object.name&&object.name==='socialdb_property_'+properties_object_id[i]+'[]'){ // se
                if(object.value!==''){
                    number_of_values++;
                }
            }
        });
        if(number_of_values<minimum_values){
            Hook.result = {is_validated:false,message:$('#property_'+properties_object_id[i]+'_error_cardinality_message').val()};
            return false;
        }
        if(number_of_values>maximum_values){
            Hook.result = {is_validated:false,message:$('#property_'+properties_object_id[i]+'_error_cardinality_message').val()};
            return false;
        }
      }
      Hook.result = {is_validated:true,message:''};
  }
);
/******************************************************************************/


/*********************** home do item - popriedade de objeto *************************/
//validando a cardinalidade da propriedade de objeto
Hook.register(
  'tainacan_validate_single_save_object_property',
  function ( args ) {
        var number_of_values = 0;
        var minimum_values = parseInt( $('#property_'+args[0]+'_min_cardinality').val()); // o valor minimo aceitavel
        var maximum_values = parseInt( $('#property_'+args[0]+'_max_cardinality').val()); // o valor maximo permitido
        if($("#single_property_value_" + args[0] + "_" + args[1]).val()){
            number_of_values = $("#single_property_value_" + args[0] + "_" + args[1]).val().length;
        }
        if(number_of_values<minimum_values){
            Hook.result = {is_validated:false,message:$('#property_'+args[0]+'_error_cardinality_message').val()};
            return false;
        }
        if(number_of_values>maximum_values){
            Hook.result = {is_validated:false,message:$('#property_'+args[0]+'_error_cardinality_message').val()};
            return false;
        }
         Hook.result = {is_validated:true,message:''};
  }
);
/*********************** home do item - popriedade de dados *************************/
//validando a cardinalidade da propriedade de dados
Hook.register(
  'tainacan_validate_single_save_data_property',
  function ( args ) {
       var number_of_values = 0;
       var minimum_values = parseInt( $('#property_'+args[0]+'_min_cardinality').val()); // o valor minimo aceitavel
       var maximum_values = parseInt( $('#property_'+args[0]+'_max_cardinality').val()); // o valor maximo permitido
      $.each($("input[name='socialdb_property_" + args[0] + "[]']"),function(index,seletor){
            if($(seletor).val().trim()!==''){
                    number_of_values++;
            }
      });
      if(number_of_values<minimum_values){
            Hook.result = {is_validated:false,message:$('#property_'+args[0]+'_error_cardinality_message').val()};
            return false;
        }
        if(number_of_values>maximum_values){
            Hook.result = {is_validated:false,message:$('#property_'+args[0]+'_error_cardinality_message').val()};
            return false;
        }
        Hook.result = {is_validated:true,message:''};
  }
);
//inserindo a cardinalidade da propriedade de dados
Hook.register(
  'tainacan_insert_single_save_data_property',
  function ( args ) {
      var values = [];
      $.each($("input[name='socialdb_property_" + args[0] + "[]']"),function(index,seletor){
            if($(seletor).val().trim()!==''){
                    values.push($(seletor).val().trim());
            }
      });
      //ajax
      $('#modalImportMain').modal('show');//mostro o modal de carregamento
      $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/event/event_controller.php",
                data: {
                    socialdb_event_collection_id: $('#collection_id').val(),
                    operation: 'add_event_property_data_edit_value',
                    socialdb_event_create_date: args[2],
                    socialdb_event_user_id: $('#current_user_id').val(),
                    socialdb_event_property_data_edit_value_object_id: args[1],
                    socialdb_event_property_data_edit_value_property_id: args[0],
                    socialdb_event_property_data_edit_value_attribute_value: values}
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//mostro o modal de carregamento
                verifyPublishedItem(args[1]);
                elem = jQuery.parseJSON(result);
                if(!elem){
                    return false;
                }
                $("#dynatree").dynatree("getTree").reload();
                $("#widget_" + args[0] + "_" + args[1]).hide();
                $("#labels_" + args[0] + "_" + args[1]).fadeIn();
                list_properties_single(args[1]);
                showAlertGeneral(elem.title, elem.msg, elem.type);
            });
  }
);
/******************************* CRIACAO DA COLECAO ********************/
// expande todos os nos
Hook.register(
  'tainacan_oncreate_collection',
  function ( args ) {
  });
/******************************* HOME DA COLECAO/ DYNATREE ********************/
// expande todos os nos
Hook.register(
  'tainacan_oncreate_main_dynatree',
  function ( args ) {
      //args[0].expand();
  });
// ao selecionar uma categoria
Hook.register(
  'tainacan_onselect_dynatree',
  function ( args ) {
    if (typeof show_object_properties == 'function'){
        show_object_properties(); 
    }
    if (typeof show_object_properties_edit == 'function'){
        show_object_properties_edit(); 
    }
    if(args[0].length>0){
        $('.has-selected-class').show();
        $('.none-selected-class').hide();
    }else{
        $('.has-selected-class').hide();
        $('.none-selected-class').show();
    }
  });
  //funcao que mostra o form de adicao
    function show_form_add_item_ontology(){
        var src = $('#src').val();
        $("#menu_object").hide();
        $("#container_socialdb").hide('slow');
        $("#form").hide('slow');
        $("#list").hide('slow');
        show_modal_main();
        $.ajax({
            url: src + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {operation: 'create', collection_id: $("#collection_id").val()}
        }).done(function (result) {
            hide_modal_main();
            $("#form").html(result);
            $('#form').show('slow');
            $('#create_button').hide();
        });
    }
/******************************************************************************/
function import_mapa_cultural(url_base)
{
    if(url_base !== "")
    {
        $('#modalImportMain').modal('show');
        var url_send = $('#src').val() + '/controllers/collection/collection_controller.php?operation=mapa_cultural_form&collection_id='+$('#collection_id').val()+'&type=';
        //Agentes
        $.getJSON(
            url_base + "/api/agent/find",
            {
                '@select': 'name, shortDescription, isVerified, nomeCompleto, dataDeNascimento, emailPublico, telefonePublico, endereco, site, facebook',
                'emailPublico': 'like(*.com*)',
                '@limit': 100
            },
            function (result)
            {
                $.ajax({
                    url: url_send+"agent",
                    type: 'POST',
                    data: {result: result}
                }).success(function (result) {
                    elem = jQuery.parseJSON(result);
                    if (elem.result) {
                        //Espaços
                        $.getJSON(
                            url_base + "/api/space/find",
                            {
                                '@select': 'name, location, public, shortDescription, longDescription, endereco, site, facebook, telefonePublico, telefone1, telefone2',
                                '@limit': 100
                            },
                            function (result)
                            {
                                $.ajax({
                                    url: url_send+"space",
                                    type: 'POST',
                                    data: {result: result}
                                }).success(function (result) {
                                    elem = jQuery.parseJSON(result);
                                    if (elem.result) {
                                        //Eventos
                                        $.getJSON(
                                            url_base + "/api/event/find",
                                            {
                                                '@select': 'name, shortDescription, longDescription, rules, subtitle, preco, site, facebook',
                                                '@limit': 100
                                            },
                                            function (result)
                                            {
                                                $.ajax({
                                                    url: url_send+"event",
                                                    type: 'POST',
                                                    data: {result: result}
                                                }).success(function (result) {
                                                    elem = jQuery.parseJSON(result);
                                                    if (elem.result) {
                                                        //Projetos
                                                        $.getJSON(
                                                            url_base + "/api/project/find",
                                                            {
                                                                '@select': 'name, shortDescription, longDescription, isVerified, site, facebook',
                                                                '@limit': 100
                                                            },
                                                            function (result)
                                                            {
                                                                $.ajax({
                                                                    url: url_send+"project",
                                                                    type: 'POST',
                                                                    data: {result: result}
                                                                }).success(function (result) {
                                                                    elem = jQuery.parseJSON(result);
                                                                    if (elem.result) {
                                                                        //Todas as requisições foram realizadas com sucesso
                                                                        window.location = elem.url;
                                                                    } else {
                                                                        $('#modalImportMain').modal('hide');
                                                                        var message = elem.message;
                                                                        if(!message)
                                                                        {
                                                                            message = 'Houve um erro na importação';
                                                                        }
                                                                        showAlertGeneral('Erro', message, 'error');
                                                                    }
                                                                }).error(function (error) {
                                                                    showAlertGeneral('Erro', 'Houve um erro na importação', 'error');
                                                                });
                                                            }
                                                        );
                                                    } else {
                                                        $('#modalImportMain').modal('hide');
                                                        var message = elem.message;
                                                        if(!message)
                                                        {
                                                            message = 'Houve um erro na importação';
                                                        }
                                                        showAlertGeneral('Erro', message, 'error');
                                                    }
                                                }).error(function (error) {
                                                    showAlertGeneral('Erro', 'Houve um erro na importação', 'error');
                                                });
                                            }
                                        );
                                    } else {
                                        $('#modalImportMain').modal('hide');
                                        var message = elem.message;
                                        if(!message)
                                        {
                                            message = 'Houve um erro na importação';
                                        }
                                        showAlertGeneral('Erro', message, 'error');
                                    }
                                }).error(function (error) {
                                    showAlertGeneral('Erro', 'Houve um erro na importação', 'error');
                                });
                            }
                        );
                    } else {
                        $('#modalImportMain').modal('hide');
                        var message = elem.message;
                        if(!message)
                        {
                            message = 'Houve um erro na importação';
                        }
                        showAlertGeneral('Erro', message, 'error');
                    }
                }).error(function (error) {
                    showAlertGeneral('Erro', 'Houve um erro na importação', 'error');
                });
            }
        );
    }
    else
    {
        showAlertGeneral('Erro', 'URL inválida', 'error');
    }
}

function do_ajax(type, url_base) {

    var select_query, url, parameters, restriction;

    switch (type)
    {
        case "project":
            select_query = 'name, shortDescription, isVerified, site, facebook';
            url = url_base + "/api/project/find";
            break;
        case "event":
            select_query = 'name, shortDescription, rules, subtitle, preco, site, facebook';
            url = url_base + "/api/event/find";
            break;
        case "agent":
            select_query = 'name, shortDescription, isVerified, nomeCompleto, dataDeNascimento, emailPublico, telefonePublico, endereco, site, facebook';
            restriction = 'like(*.com*)';
            url = url_base + "/api/agent/find";
            break;
        case "space":
            select_query = 'name, location, public, shortDescription, site, facebook, telefonePublico, telefone1';
            url = url_base + "/api/space/find";
            break;
    }

    if(type != "agent")
    {
        parameters = {
            '@select': select_query
        }
    }else
    {
        parameters = {
            '@select': select_query,
            'emailPublico': restriction
        }
    }

    $.getJSON(
        url,
        parameters,
        function (result)
        {
            $.ajax({
                url: $('#src').val() + '/controllers/collection/collection_controller.php?operation=mapa_cultural_form&collection_id='+$('#collection_id').val()+'&type='+type,
                type: 'POST',
                async: false,
                data: {result: result}
            }).success(function (result) {
                elem = jQuery.parseJSON(result);
                if (elem.result) {
                    return {"result": true, "url": elem.url};
                } else {
                    $('#modalImportMain').modal('hide');
                    var message = elem.message;
                    if(!message)
                    {
                        message = 'Houve um erro na importação';
                    }
                    showAlertGeneral('Erro', message, 'error');
                }
            }).error(function (error) {
                showAlertGeneral('Erro', 'Houve um erro na importação', 'error');
            });
        }
    );
}