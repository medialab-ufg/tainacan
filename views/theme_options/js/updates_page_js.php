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
    swal({
        title: '<?php _e('Attention!','tainacan') ?>',
        text: '<?php _e('This operation is irreversible, are you sure?','tainacan') ?>',
        type: "info",
        showCancelButton: true,
        confirmButtonClass: 'btn-primary',
        closeOnConfirm: true,
        closeOnCancel: true
    },
    function (isConfirm) {
        if (isConfirm) {
            show_modal_main();
            $.ajax({
                type: "POST",
                url:$('#src').val() + "/controllers/theme_options/synchronize_controller.php",
                data: {
                        api_user: $('#api_user').val(),
                        api_key: $('#api_key').val(),
                        operation: 'start',
                        api_url:  $('#url_api').val()+'/wp-json'
                },
                dataType: 'json'
            }).error(function(result){
                hide_modal_main();
                console.log(result);
                showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('URL unformmated or service unavailable!', 'tainacan') ?>', 'error');
            }).done(function (result) {
                hide_modal_main();
                if(result){
                     showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('Operation successfully!', 'tainacan') ?>', 'success');
                }else{
                     showAlertGeneral('<?php _e('Attention', 'tainacan') ?>', '<?php _e('URL unformmated or service unavailable!', 'tainacan') ?>', 'error');
                }
            });
        }
    });
}
</script>
