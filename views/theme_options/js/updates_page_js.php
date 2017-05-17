<script>
   
/**************************************** API TAINACAN ************************/
function testLinkAPI(){
    $.ajax({
        type: "GET",
        url: $('#url_api').val()+'/wp-json',
        dataType: 'json'
    }).error(function(){
        showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('URL unformmated or service unavailable!', 'tainacan') ?>', 'error');
    }).done(function (result) {
        if(result){
             showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('Connection established!', 'tainacan') ?>', 'success');
        }else{
             showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('URL unformmated or service unavailable!', 'tainacan') ?>', 'error');
        }
    });
}

function confirmationAPI(){
}
</script>
