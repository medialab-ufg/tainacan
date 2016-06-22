$(window).load(function () {
//silence is gold
});
/*
 *  funcao que abre a view de gerenciamento de arquivos
 */
function showArchivalManagement(src) {
    console.log(src);
    $.ajax({
        url: src + '/controllers/archival_management/archival_management_controller.php',
        type: 'POST',
        data: {operation: 'list', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $('#configuration').html(result);
        $('#configuration').show();
    });
}

