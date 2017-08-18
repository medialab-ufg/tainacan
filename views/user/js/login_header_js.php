<script> 
$(function() {     
    $('#LoginForm').submit( function(e) {    
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
    
    
    $('#open_myModalForgotPasswordHeader').click(function (e) {
        //$('#myModalForgotPasswordHeader').modal('show');
        $("#forgot_password").toggleClass("hide_elem");
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


$("#login-box").hover(function() {
    $('#login-out').removeClass('login-outer-container');
    $('#login-in' ).removeClass();
});

</script>