<script> 

/**
 * Author: Eduardo Humberto
 */

$(function(){  
    var src = $('#src').val();
      $('#collection_ranking_id').val( $('#collection_id').val());
    $( '#submit_form_ranking' ).submit( function( e ) {
        e.preventDefault();
         $('#modalImportMain').modal('show');//mostra o modal de carregamento
         $.ajax( {
              url: src+'/controllers/ranking/ranking_controller.php',
              type: 'POST',
              data: new FormData( this ),
              processData: false,
              contentType: false
        }).done(function( result ) {
                $('#modalImportMain').modal('hide');//esconde o modal de carregamento
               elem =jQuery.parseJSON(result);                   
                 if(elem.success==='true'){
                     $("#alert_success").hide();
                    $("#alert_error").hide();
                       $("#alert_error").hide();
                     $("#alert_success").show();
                     backToListRanking();
                 }else{
                    $("#alert_success").hide();
                    $("#alert_error").hide();
                    $("#alert_success").hide();
                    $("#alert_error").show();
                 }
        }); 
        e.preventDefault();
    });
   
});

    
    function backToListRanking(){
     $.ajax({         
        url: $('#src').val() + '/controllers/ranking/ranking_controller.php',
        type: 'POST',
        data: {operation: 'list_data', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $('#configuration').html(result);
        $('#configuration').show();
    });
}


</script> 