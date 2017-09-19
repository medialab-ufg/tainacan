<script>
    $(function(){

    $('#formYoutubeApi').submit(function (e) {
        var verify =  $( this ).serializeArray();
        if(verify[1].value.trim() === '') {
            showAlertGeneral('<?php _e('Attention','tainacan') ?>', '<?php _e('Please set a valid API KEY','tainacan') ?>', 'info');
            return false;
        }
        e.preventDefault();
        $.ajax({
            url: $("#src").val() + '/controllers/theme_options/theme_options_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            showAlertGeneral(elem.title, elem.msg, elem.type);
        });

    });
    
    $('#formFlickrApi').submit(function (e) {
        var verify =  $( this ).serializeArray();
        if(verify[1].value.trim() === ''){
            showAlertGeneral('<?php _e('Attention','tainacan') ?>', '<?php _e('Please set a valid API KEY','tainacan') ?>', 'info');
            return false;
        }
        e.preventDefault();
        $.ajax({
            url: $("#src").val() + '/controllers/theme_options/theme_options_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            showAlertGeneral(elem.title, elem.msg, elem.type);
        });

    });
    
    $('#formFacebookApi').submit(function (e) {
        var verify =  $( this ).serializeArray();
        if(verify[1].value.trim() === ''||verify[2].value.trim() === ''){
            showAlertGeneral('<?php _e('Attention','tainacan') ?>', '<?php _e('Please set a valid API KEY or API Secret','tainacan') ?>', 'info');
            return false;
        }
        e.preventDefault();
        $.ajax({
            url: $("#src").val() + '/controllers/theme_options/theme_options_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            showAlertGeneral(elem.title, elem.msg, elem.type);
        });

    });
    
    $('#formInstagramApi').submit(function (e) {
        var verify =  $( this ).serializeArray();
        if(verify[1].value.trim() === ''||verify[2].value.trim() === ''){
            showAlertGeneral('<?php _e('Attention','tainacan') ?>', '<?php _e('Please set a valid API KEY or API Secret','tainacan') ?>', 'info');
            return false;
        }
        e.preventDefault();
        $.ajax({
            url: $("#src").val() + '/controllers/theme_options/theme_options_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            showAlertGeneral(elem.title, elem.msg, elem.type);
        });

    });
    
    $('#formVimeoApi').submit(function (e) {
        var verify =  $( this ).serializeArray();
        if(verify[1].value.trim() === ''||verify[2].value.trim() === ''){
            showAlertGeneral('<?php _e('Attention','tainacan') ?>', '<?php _e('Please set a valid API Client ID or API Client Secrets','tainacan') ?>', 'info');
            return false;
        }
        e.preventDefault();
        $.ajax({
            url: $("#src").val() + '/controllers/theme_options/theme_options_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            showAlertGeneral(elem.title, elem.msg, elem.type);
        });

    });
    
    $('#formEmbedApi').submit(function (e) {
         var verify =  $( this ).serializeArray();
        if(verify[1].value.trim() === ''){
            showAlertGeneral('<?php _e('Attention','tainacan') ?>', '<?php _e('Please set a valid API KEY','tainacan') ?>', 'info');
            return false;
        }
        e.preventDefault();
        $.ajax({
            url: $("#src").val() + '/controllers/theme_options/theme_options_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            showAlertGeneral(elem.title, elem.msg, elem.type);
        });

    });
    
    $('#formGoogleApi').submit(function (e) {
        e.preventDefault();
        var verify =  $( this ).serializeArray();
        if(verify[1].value.trim() === '' || verify[2].value.trim() === '' || verify[3].value.trim() === ''){
            showAlertGeneral('<?php _e('Attention','tainacan') ?>', '<?php _e('Please, there are emtpy fields','tainacan') ?>', 'info');
            return false;
        }
        $.ajax({
            url: $("#src").val() + '/controllers/theme_options/theme_options_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            showAlertGeneral(elem.title, elem.msg, elem.type);
        });

    });
    
    $('#formEuropeanaApi').submit(function (e) {
        var verify =  $( this ).serializeArray();
        if(verify[1].value.trim() === ''||verify[2].value.trim() === ''){
            showAlertGeneral('<?php _e('Attention','tainacan') ?>', '<?php _e('Please set a valid API KEY or Private Key','tainacan') ?>', 'info');
            return false;
        }
        e.preventDefault();
        $.ajax({
            url: $("#src").val() + '/controllers/theme_options/theme_options_controller.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false
        }).done(function (result) {
            elem = jQuery.parseJSON(result);
            showAlertGeneral(elem.title, elem.msg, elem.type);
        });

    });


    });
</script>