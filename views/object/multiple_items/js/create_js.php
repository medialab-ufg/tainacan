<script>
    $(function () {
        // #1 - breadcrumbs para localizacao da pagina
        $("#tainacan-breadcrumbs").show();
        $("#tainacan-breadcrumbs .current-config").text('<?php _e('Add new item - Send local file','tainacan') ?>');
        // 2# - Dropzone dos arquivos
        var myDropzone = new Dropzone("#dropzone_multiple_items", {
            maxFilesize: parseInt('<?php echo file_upload_max_size(); ?>') / 1024 / 1024,
            accept: function(file, done) {
                    if (file.type === ".exe") {
                        done("Error! Files of this type are not accepted");
                    }
                    else { done(); }
            },
            init: function () {
                thisDropzone = this;
                this.on("removedfile", function (file) {
                    //    if (!file.serverId) { return; } // The file hasn't been uploaded
                    $.get($('#src').val() + '/controllers/object/object_controller.php?operation=delete_file&object_id=<?php echo $object_id ?>&file_name=' + file.id, function (data) {
                        if (data.trim() === 'false') {
                           // showAlertGeneral('<?php _e("Atention!", 'tainacan') ?>', '<?php _e("An error ocurred, File already removed or corrupted!", 'tainacan') ?>', 'error');
                        } else {
                           // showAlertGeneral('<?php _e("Success", 'tainacan') ?>', '<?php _e("File removed!", 'tainacan') ?>', 'success');
                        }
                    }); // Send the file id along
                });
                //ao terminar o uplaod dos itens
                this.on("queuecomplete", function (file) {
                    $('.extract-img-exif').show();
                    $('#click_editor_items_button').show().focus();
//                        $.get($('#src').val()+'/controllers/object/object_controller.php?collection_id='+$('#collection_id').val()+'&operation=editor_items&object_id='+<?php echo $object_id ?>, function (data) {
//                            try {
//                                //var jsonObject = JSON.parse(data);
//                                if(data!=0){
//                                    $("#uploading").slideUp();
//                                    $('#editor_items').html(data);
//                                }else{
//                                    showAlertGeneral('<?php _e("Atention!", 'tainacan') ?>', '<?php _e("File is too big or Uploaded, however, not supported by wordpress, please select valid files!", 'tainacan') ?>', 'error');
//                                }
//                            }
//                            catch (e)
//                            {
//                                // handle error 
//                            }
//                        });
                });
                $.get($('#src').val() + '/controllers/object/object_controller.php?operation=list_files&object_id=' + $("#object_id_add").val(), function (data) {
                    try {
                        //var jsonObject = JSON.parse(data);
                        $.each(data, function (key, value) {
                            if (value.name !== undefined && value.name !== 0) {
                                var mockFile = {name: value.name, size: value.size,id:value.ID};
                                thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                            }
                        });
                    }
                    catch (e)
                    {
                        // handle error 
                    }
                });
                this.on("success", function (file, message) {
                          file.id = message.trim();
                });
            },
            url: $('#src').val() + '/controllers/object/object_controller.php?operation=save_file&object_id=' +<?php echo $object_id ?>,
            addRemoveLinks: true

        });
         $('#container-fluid-configuration').css('background-color','#f1f2f2');
    });
    function back_main_list() {
     swal({
            title: '<?php _e('Attention!','tainacan') ?>',
            text: '<?php _e('You did not finish your action. Are you sure to leave this page?','tainacan') ?>',
            type: "error",
            showCancelButton: true,
            cancelButtonText: '<?php _e('Cancel','tainacan') ?>',
            confirmButtonClass: 'btn-success',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $('#form').hide();
                $("#tainacan-breadcrumbs").hide();
                $('#configuration').hide();
                $('#main_part').show();
                $('#display_view_main_page').show();
                $("#container_socialdb").show('fast');
                $.ajax({
                    url: $('#src').val() + '/controllers/object/object_controller.php',
                    type: 'POST',
                    data: {operation: 'delete_temporary_object', ID: '<?php echo $object_id ?>'}
                }).done(function (result) {
                    $('#main_part').show();
                    $('#collection_post').show();
                    $('#configuration').slideDown().hide();
                });
            }
        });
    }
    
    function edit_items_uploaded() {
        var do_extract = $('input[name="extract_exif"]').prop("checked");
        show_modal_main();
        $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {
                extract_exif: do_extract,
                operation: 'editor_items',
                collection_id: $('#collection_id').val(),
                object_id: '<?php echo $object_id ?>'}
        }).done(function (data) {
            hide_modal_main();
            if (data != 0) {
                $("#upload_container").hide();
                $('#editor_items').html(data).css('display', 'block');
            } else {
                showAlertGeneral('<?php _e("Attention!", 'tainacan') ?>', '<?php _e("File is too big or Uploaded, however, not supported by wordpress, please select valid files!", 'tainacan') ?>', 'error');
            }
        });
    }

    function upload_more_files() {
        swal({
            title: '<?php _e("Attention!", 'tainacan') ?>',
            text: '<?php _e("You did not finish your action. Are you sure to leave this page?", 'tainacan') ?>',
            type: "warning",
            cancelButtonText: '<?php _e("Cancel", 'tainacan') ?>',
            showCancelButton: true,
            confirmButtonClass: 'btn-success',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $("#editor_items").slideDown();
                $("#editor_items").hide();
                $('#upload_container').show();
                $("#tainacan-breadcrumbs .current-config").text('<?php _e('Add new item - Send local file','tainacan') ?>');
            }
        });
    }
</script>
