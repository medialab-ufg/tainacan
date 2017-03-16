/**
 * Scripts do modulo de debates do tainacan
 * 
 *  #1 Funcoes a serem executadas no inicio do modulo
 *  #2 Abre o modal de criacao de argumento e de pergunta
 *  #3 Abrir os campos para depositar os argumentos
 * 
 */
//############## #1 Funcoes a serem executadas no inicio do modulo ############# 
$(window).load(function () {
});
function showItemObject(object_id, src) {
    $.ajax({
        url: src + '/controllers/object/object_controller.php',
        type: 'POST',
        data: {operation: 'list_single_object', object_id: object_id, collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#configuration').html(result).show();
    });
}
function hide_all_modals(){
    $('.modal').modal('hide');
}
//############################################################################## 

//############## #2 Abre o modal de criacao de argumento e de pergunta############# 
function contest_show_modal_create_argument(){
    $('#modalCreateArgument').modal('show');
}
function contest_show_modal_create_question(){
    $('#modalCreateQuestion').modal('show');
}
//############## #3 Abrir os campos para depositar os argumentos #############
function open_positive_argument(id){
    $('#positive-argument-'+id).fadeIn();
    $('#negative-argument-'+id).fadeOut();
}

function open_negative_argument(id){
    $('#positive-argument-'+id).fadeOut();
    $('#negative-argument-'+id).fadeIn();
}
//############## #3 Abrir os campos para visualizar propriedades #############
function open_properties_argument(id){
    show_modal_main();
    $.ajax({
        type: "POST",
        url: $('#src').val() + "/modules/contest/controllers/item/item_controller.php",
        data: {collection_id: $('#collection_id').val(), operation: 'list_properties_item', object_id: id}
    }).done(function (result) {
        hide_modal_main();
        $('#open_properties_argument_'+ id).hide();
        $('#hide_properties_argument_'+ id).show();
        $('#properties-argument-' + id).html(result).fadeIn();
    });
}
function hide_properties_argument(id){
    $('#open_properties_argument_'+ id).show();
    $('#hide_properties_argument_'+ id).hide();
    $('#properties-argument-' + id).fadeOut();
}
