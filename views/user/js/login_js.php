<script> 
$(function() {     
    
    $('#LoginFormPage').submit( function(e) {    
       show_modal_main(); 
       $.ajax({
              url: $('#src_login').val()+'/controllers/user/user_controller.php',
              type: 'POST', data: new FormData( this ),
              processData: false, contentType: false
            }).done(function( result ) {
                elem =jQuery.parseJSON(result); 
                if(elem.login === 1) {
                    window.location = elem.url;
                } else {
                    hide_modal_main();
                    showAlertGeneral(elem.title, elem.msg, elem.type);
                }
                    
            }); 
        e.preventDefault();
    });
    
    $('#open_myModalForgotPassword').click(function (e) {
        $('#myModalForgotPassword').modal('show');
    });
    
    $( '#formUserForgotPassword' ).submit( function( e ) {
       
       $.ajax( {
              url: $('#src_login').val()+'/controllers/user/user_controller.php',
              type: 'POST',
              data: new FormData( this ),
              processData: false,
              contentType: false
            } ).done(function( result ) {
                    elem =jQuery.parseJSON(result); 
                    showAlertGeneral(elem.title, elem.msg, elem.type);
                    if(elem.type == 'success'){
                        $('#myModalForgotPassword').modal('hide');
                    }
                    $('#user_login_forgot').val('');
            }); 
            e.preventDefault();
    });
    
    $( '#formUserForgotPasswordHeader' ).submit( function( e ) {
       
       $.ajax( {
              url: $('#src_login').val()+'/controllers/user/user_controller.php',
              type: 'POST',
              data: new FormData( this ),
              processData: false,
              contentType: false
            } ).done(function( result ) {
                    elem =jQuery.parseJSON(result); 
                    showAlertGeneral(elem.title, elem.msg, elem.type);
                    if(elem.type == 'success'){
                        $('#myModalForgotPassword').modal('hide');
                    }
                    $('#user_login_forgot').val('');
            }); 
            e.preventDefault();
    });
   
});



</script>