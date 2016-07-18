<script type="text/javascript">
    $( '#formUserRegister' ).submit( function( e ) {
        $.ajax( {
            url: $('#src').val() + '/controllers/user/user_controller.php',
            type: 'POST',
            data: new FormData( this ),
            processData: false,
            contentType: false
        } ).done(function( result ) {
            elem =jQuery.parseJSON(result);
            if(elem.login === 1){
                window.location = elem.url;
            } else {
                showAlertGeneral(elem.title, elem.msg, elem.type);
            }
        });
       e.preventDefault();
    });

    $('a.more-options-register').click(function(e) {
        e.preventDefault();

        if( $(this).hasClass('less-options') ) {
            $(this).text('<?php _e("More options", "tainacan"); ?>');
            $(this).removeClass('less-options');
        } else {
            $(this).addClass('less-options');
            $(this).text('<?php _e("Less options", "tainacan"); ?>');
        }

        $('.expanded-register').toggle();
    });
</script>