<script>
    $(function () {
        var src = $('#src').val();
        change_breadcrumbs_title('<?php _e('Repository Configuration','tainacan') ?>');
        showCKEditor();
        //list_templates();
        autocomplete_collection_templates();
        init_dynatree_collection_template();
        $('#submit_form_edit_repository_configuration').submit(function (e) {
            $("#repository_content").val(CKEDITOR.instances.editor.getData());
            e.preventDefault();
            $.ajax({
                url: $("#src").val() + '/controllers/theme_options/theme_options_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                if(elem.reload&&elem.reload===true){
                    window.location = '<?php echo site_url(); ?>'
                }
                showAlertGeneral(elem.title, elem.msg, elem.type);
                showRepositoryConfiguration(src);
                get_collections_template($('#src').val());  
            });
        });
    });
    
    
    function autocomplete_collection_templates() {
        $("#collection_template").autocomplete({
            source: $('#src').val() + '/controllers/collection/collection_controller.php?operation=get_collections_json',
            messages: {
                noResults: '',
                results: function () {
                }
            },
            minLength: 2,
            select: function (event, ui) {
                event.preventDefault();
                $("#collection_template").val(ui.item.label);
                swal({
                    title: '<?php _e('Attention!','tainacan') ?>',
                    text: '<?php _e('Add the collection','tainacan') ?>'+' '+ui.item.label+' '+'<?php _e('as a template','tainacan') ?>',
                    type: "info",
                    showCancelButton: true,
                    confirmButtonClass: 'btn-info',
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function (isConfirm) {
                    if (isConfirm) {
                       $('#modalImportMain').modal('show');//mostro o modal de carregamento
                        $.ajax({
                            type: "POST",
                            url: $('#src').val() + "/controllers/collection/collection_controller.php",
                            data: {
                                operation: 'add_collection_template',
                                collection_id: ui.item.value
                               }
                        }).done(function (result) {
                            $('#modalImportMain').modal('hide');//escondo o modal de carregamento
                            elem_first = jQuery.parseJSON(result);
                            if(elem_first.d){
                                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                                get_collections_template($('#src').val()); 
                                list_templates();
                            }
                        });
                    }
                });
            }
        });
    }
    
     function clear_collection_template(e) {
     var id = $(e).attr('id');
           swal({
                title: '<?php _e('Attention!','tainacan') ?>',
                text: '<?php _e('Removing the template','tainacan') ?>'+' '+$('#'+id+' option:selected').text()+'?',
                type: "info",
                showCancelButton: true,
                confirmButtonClass: 'btn-info',
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function (isConfirm) {
                if (isConfirm) {
                   $('#modalImportMain').modal('show');//mostro o modal de carregamento
                    $.ajax({
                        type: "POST",
                        url: $('#src').val() + "/controllers/collection/collection_controller.php",
                        data: {
                            operation: 'delete_collection_template',
                            collection_id:  $('#'+id+' option:selected').val()
                           }
                    }).done(function (result) {
                        $('#modalImportMain').modal('hide');//escondo o modal de carregamento
                        elem_first = jQuery.parseJSON(result);
                        if(elem_first.result){
                            //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                            get_collections_template($('#src').val());  
                            list_templates();
                        }
                    });
                }else{
                    list_templates();
                }
            });       
    
    }
    
    function init_dynatree_collection_template(){
         $("#dynatree-collection-templates").dynatree({
            selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
            checkbox: true,
            initAjax: {
                url: $('#src').val() + '/controllers/collection/collection_controller.php',
                data: {
                    collection_id: $("#collection_id").val(),
                    operation: 'initDynatreeCollectionTemplates'
                },
                addActiveKey: true
            },
            onSelect: function (flag, node) {
                console.log(node)
                if(node.bSelected&&node.childList){
                    $.each(node.childList,function(index,node){
                        node.select(true);
                    });
                }else if(node.childList){
                    $.each(node.childList,function(index,node){
                        node.select(false);
                    });
                }
                if(node.data.key!=='false'){
                    toggleHabilitateTemplate(node.data.key,node.data.type);
                }
            }
        });
    }
    
    function toggleHabilitateTemplate(key,type){
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/collection/collection_controller.php",
            data: {
                operation: 'habilitate-collection-templates',
                key:key,
                type:type
               }
        }).done(function (result) {
            
        });
    }
    /*
    function list_templates() {
         $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/collection/collection_controller.php",
            data: {
                operation: 'list-collection-templates',
                is_json:true
               }
        }).done(function (result) {
            $('#collection_templates').html('');
            elem_first = jQuery.parseJSON(result);
            if(elem_first&&elem_first.length>0){
                $.each(elem_first,function(index,value){
                     $('#collection_templates').append('<option selected="selected" value="'+value.directory+'">'+value.title+'</option>');
                });
                $('#show_collection_empty').show();
            }else{
                $('#show_collection_empty').hide();
            }
        });
    }
    */
    // desabilita as colecaoes vazia no repositorio
//    function disable_empty_collection(selector){
//        selector = $(selector);
//        console.log(selector.prop( "checked" ));
//        $.ajax({
//                url: $("#src").val() + '/controllers/theme_options/theme_options_controller.php',
//                type: 'POST',
//                data: {
//                    operation:'disable_empty_collection';
//                    situ
//                }
//        }).done(function (result) {
//            elem = jQuery.parseJSON(result);
//            showAlertGeneral(elem.title, elem.msg, elem.type);
//            showRepositoryConfiguration(src);
//        });
//    }
</script>
