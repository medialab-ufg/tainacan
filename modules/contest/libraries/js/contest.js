/**
 * Scripts do modulo de debates do tainacan
 * 
 *  #1 Funcoes a serem executadas no inicio do modulo
 *  #2 Abre o modal de criacao de argumento e de pergunta
 * 
 * 
 */
//############## #1 Funcoes a serem executadas no inicio do modulo ############# 
$(window).load(function () {
});
//############################################################################## 

//############## #2 Abre o modal de criacao de argumento e de pergunta############# 
function contest_show_modal_create_argument(){
    $('#modalCreateArgument').modal('show');
}
function contest_show_modal_create_question(){
    $('#modalCreateQuestion').modal('show');
}
//############## #3 Abre o modal de criacao de argumento e de pergunta############# 
/**
 * 
 * @param {type} src
 * @returns {undefined}
 */
function init_contest_item_page(src,collection_id,item_id){  
    $.ajax({
            type: "POST",
            url: src + "/controllers/item/item_controller.php",
            data: {collection_id: collection_id, operation: 'show-item', object_id: item_id}
        }).done(function (result) {
            $('#configuration').html(result).fadeIn();
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
}