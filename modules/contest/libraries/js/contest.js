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