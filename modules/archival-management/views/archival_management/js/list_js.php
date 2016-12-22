<script> 
$(function(){ 
   $("#dynatree_export_plan").dynatree({
            checkbox: true,
            // Override class name for checkbox icon:
            classNames: {checkbox: "dynatree-radio"},
            selectMode: 1,
            selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
            initAjax: {
                url: $('#src').val() + '/controllers/category/category_controller.php',
                data: {
                    collection_id: $("#collection_id").val(),
                    operation: 'initDynatreeTerms'
                }
                , addActiveKey: true
            },
            onLazyRead: function (node) {
                node.appendAjax({
                    url: $('#src').val() + '/controllers/category/category_controller.php',
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
             onCreate: function (node, span) {
                $("#dynatree_export_plan").dynatree("getRoot").visit(function(node){
                   node.select(false);
                });
            },
            onSelect: function (flag, node) {
                 $("#category_id_archival_management").val(node.data.key);
            }
        });
        
        setTimeout(function(){
            $("#dynatree_export_plan").dynatree("getRoot").visit(function(node){
                if(node.data.key === $("#category_id_archival_management").val() ){
                    node.select(true);
                }
            }); 
         }, 3000);
});
/**
 * funcao que redireciona para forcar o downlload do plano de classificacao de uma hierarquia
 */
function export_classification_plan(){
    $('#list_items_to_eliminate').hide();
    $('#list_items_to_transfer').hide();
    if($("#category_id_archival_management").val()!==''){
        $('#modaldynatreeExportPlan').modal('hide');
        var get = '?operation=export_classification_plan&category_id='+$("#category_id_archival_management").val();
        window.location = $('#src').val() + '/modules/archival-management/controllers/archival_management/archival_management_controller.php'+get;
    }else{
         $('#modaldynatreeExportPlan').modal('hide');
        showAlertGeneral('<?php _e('Attention','taincan') ?>', '<?php _e('Select a category first','tainacan') ?>', 'info');
    }
    
}
/**
 * funcao que redireciona para forcar o downlload da tabela de temporariedade de uma categoria
 */
function export_table_of_temporality(){
     $('#list_items_to_eliminate').hide();
     $('#list_items_to_transfer').hide();
    if($("#category_id_archival_management").val()!==''){
        $('#modaldynatreeExportPlan').modal('hide');
        var get = '?operation=export_table_of_temporality&category_id='+$("#category_id_archival_management").val();
        window.location = $('#src').val() + '/modules/archival-management/controllers/archival_management/archival_management_controller.php'+get;
    }else{
         $('#modaldynatreeExportPlan').modal('hide');
        showAlertGeneral('<?php _e('Attention','taincan') ?>', '<?php _e('Select a category first','tainacan') ?>', 'info');
    }
    
}
/**
 * funcao que mostra a listagem de itemspara transferencia
 */
 function list_export_items(){
     $('#list_items_to_transfer').fadeIn();
     $('#list_items_to_eliminate').hide();
     
     $.ajax({
            type: "POST",
            url: $('#src').val() + "/modules/archival-management/controllers/archival_management/archival_management_controller.php",
            data: {operation:'list_items_to_export',category_id:$('#category_id_archival_management').val(),collection_id:$('#collection_id').val()}
     }).done(function( result ) {
             elem =jQuery.parseJSON(result);
             $('#tbody_items_to_transfer').html('');
             if(elem.length>0){
                 $('#table_items_to_transfer').show();
                 $('#alert_items_to_transfer').hide();
                $.each(elem,function( index, value ) {
                   var string_expired = '';
                   var expired_id = Math.floor(value.expiration/12);
                   if(expired_id>0){
                       string_expired = expired_id+' <?php _e('Year(s)','tainacan') ?>';
                   }
                   //months
                   if(value.expiration%12>0){
                        string_expired += ' and '+value.expiration%12+' <?php _e('Months','tainacan') ?>';
                   }
                    string_expired += ' <?php _e('ago','tainacan'); ?>'
                   $('#tbody_items_to_transfer').
                           append('<tr><td>'+value.name+'</td><td>'+value.date+'</td><td>'+value.current_phase_time+'</td><td>'+string_expired+'</td><td><a style="cursor:pointer;" onclick="edit_object_archival_management('+value.id+')"><span class="glyphicon glyphicon-pencil" ></span></a></td>');
               });
            }else{
                $('#table_items_to_transfer').hide();
                $('#alert_items_to_transfer').show();
            }
    });
 }
 /**
 * funcao que mostra a listagem de items para recolhimento ou exclusao
 */
 function list_export_items_elimination(){
     $('#list_items_to_eliminate').fadeIn();
     $('#list_items_to_transfer').hide();
     
     $.ajax({
            type: "POST",
            url: $('#src').val() + "/modules/archival-management/controllers/archival_management/archival_management_controller.php",
            data: {operation:'list_items_to_eliminate',category_id:$('#category_id_archival_management').val(),collection_id:$('#collection_id').val()}
     }).done(function( result ) {
             elem =jQuery.parseJSON(result);
             $('#tbody_items_to_elimination').html('');
             if(elem.length>0){
                $('#table_items_to_eliminate').show();
                $('#alert_items_to_eliminate').hide();
                $.each(elem,function( index, value ) {
                   var string_expired = '';
                   var expired_id = Math.floor(value.expiration/12);
                   if(expired_id>0){
                       string_expired = expired_id+' <?php _e('Year(s)','tainacan') ?>';
                   }
                   //months
                   if(value.expiration%12>0){
                        string_expired += ' and '+value.expiration%12+' <?php _e('Months','tainacan') ?>';
                   }
                    string_expired += ' <?php _e('ago','tainacan'); ?>';
                   $('#tbody_items_to_elimination').
                           append('<tr><td>'+value.name+'</td><td>'+value.date+'</td><td>'+value.intermediate_phase_time+'</td><td>'+string_expired+'</td><td><a style="cursor:pointer;" onclick="edit_object_archival_management('+value.id+')" ><span class="glyphicon glyphicon-pencil" ></span></a></td>');
               });
             }else{
               $('#table_items_to_eliminate').hide();
               $('#alert_items_to_eliminate').show();
            }
    });
 }
// editando objeto
    function edit_object_archival_management(object_id) {
        $('#modalImportMain').modal('show');//mostro o modal de carregamento
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'edit', object_id: object_id}
        }).done(function (result) {
            $("#configuration").hide();
            $("#container_socialdb").hide();
            $('#modalImportMain').modal('hide');//escondo o modal de carregamento
            $("#container_socialdb").hide('slow');
            $("#form").hide();
            $("#form").html(result);
            $('#form').show('slow');
            $("#main_part").fadeIn();
            $('#create_button').hide();
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
//mostrando o dynatree
function showDynatreeField(){
    $("#showDynatreeField").hide();
    $("#hideDynatreeField").show();
    $("#dynatree_export_plan").slideDown();
}
function hideDynatreeField(){
    $("#showDynatreeField").show();
    $("#hideDynatreeField").hide();
    $("#dynatree_export_plan").slideUp();
}
</script>
            