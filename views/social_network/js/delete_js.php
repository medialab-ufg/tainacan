<script>

    /* @name: deleteIdentifierYoutube()
     * @parameters: id, collection
     * @description: deleta do banco o identificador de um dado canal
     * 
     * @author: saymon
     **/

    function deleteIdentifierYoutube(id, collection) {

        var src = $('#src').val();

        swal({
            title: '<?php _e('Attention!','tainacan'); ?>',
            text: '<?php _e('Are you sure?','tainacan'); ?>',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: src + '/controllers/social_network/youtube_controller.php',
                    type: 'POST',
                    data: {operation: 'deleteIdentifierYoutube',
                        identifier: id,
                        collection_id: collection},
                    success: function (response) {
                        //se a gravação no banco foi realizado, a tabela é incrementada
                        if (response == true) {
                            showAlertGeneral('<?php _e('Success','tainacan'); ?>', '<?php _e('Successfully excluded identifier.','tainacan'); ?>', 'success');
                            listTableYoutube();
                        }
                        else {
                            showAlertGeneral('<?php _e('Error','tainacan'); ?>', '<?php _e('Identifier not excluded','tainacan'); ?>', 'error');
                        }
                    }
                });
            }
        });


    }// fim de deleteIdentifier()



    /* @name: deleteIdentifierVimeo()
     * @parameters: id, collection
     * @description: deleta do banco o identificador de um dado canal
     * vimeo
     * @author: saymon
     **/

    function deleteIdentifierVimeo(id, collection) {

        var src = $('#src').val();

        swal({
            title: '<?php _e('Attention!','tainacan'); ?>',
            text: '<?php _e('Are you sure?','tainacan'); ?>',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: src + '/controllers/social_network/vimeo_controller.php',
                    type: 'POST',
                    data: {operation: 'deleteIdentifierVimeo',
                        identifier: id,
                        collection_id: collection},
                    success: function (response) {
                        //se a gravação no banco foi realizado, a tabela é incrementada
                        if (response == true) {
                            showAlertGeneral('<?php _e('Success','tainacan'); ?>', '<?php _e('Successfully excluded identifier.','tainacan'); ?>', 'success');
                            listTableVimeo();
                        }
                        else {
                            showAlertGeneral('<?php _e('Error','tainacan'); ?>', '<?php _e('Identifier not excluded','tainacan'); ?>', 'error');
                        }
                    }
                });
            }
        });


    }// fim de deleteIdentifier()



    /* @name: deleteIdentifierFlickr()
     * @parameters: id, collection
     * @description: deleta do banco o nome de um usuário de um dado 
     * perfil do flickr
     * 
     * @author: saymon
     **/

    function deleteIdentifierFlickr(id, collection) {

        var src = $('#src').val();

        swal({
            title: '<?php _e('Attention!','tainacan'); ?>',
            text: '<?php _e('Are you sure?','tainacan'); ?>',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: src + '/controllers/social_network/flickr_controller.php',
                    type: 'POST',
                    data: {operation: 'deleteIdentifierFlickr',
                        identifier: id,
                        collection_id: collection},
                    success: function (response) {
                        //se a gravação no banco foi realizado, a tabela é incrementada
                        if (response == true) {
                            showAlertGeneral('<?php _e('Success','tainacan'); ?>', '<?php _e('Successfully excluded identifier.','tainacan'); ?>', 'success');
                            listTableFlickr();
                        }
                        else {
                            showAlertGeneral('<?php _e('Error','tainacan'); ?>', '<?php _e('Identifier not excluded','tainacan'); ?>', 'error');
                        }
                    }
                });
            }
        });


    }// fim de deleteIdentifier()


    /* @name: deleteIdentifierFacebook()
     * @parameters: id, collection
     * @description: deleta do banco o nome de um usuário de um dado 
     * perfil do flickr
     * 
     * @author: saymon
     **/

    function deleteIdentifierFacebook(id, collection) {

        var src = $('#src').val();

        swal({
            title: '<?php _e('Attention!','tainacan'); ?>',
            text: '<?php _e('Are you sure?','tainacan'); ?>',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: src + '/controllers/social_network/facebook_controller.php',
                    type: 'POST',
                    data: {operation: 'deleteIdentifierFacebook',
                        identifier: id,
                        collection_id: collection},
                    success: function (response) {
                        //se a gravação no banco foi realizado, a tabela é incrementada
                        if (response == true) {
                            showAlertGeneral('<?php _e('Success','tainacan'); ?>', '<?php _e('Successfully excluded identifier.','tainacan'); ?>', 'success');
                            listTableFacebook();
                        }
                        else {
                            showAlertGeneral('<?php _e('Error','tainacan'); ?>', '<?php _e('Identifier not excluded','tainacan'); ?>', 'error');
                        }
                    }
                });
            }
        });


    }// fim de deleteIdentifierFacebook()


    /* @name: deleteIdentifierFacebook()
     * @parameters: id, collection
     * @description: deleta do banco o nome de um usuário de um dado 
     * perfil do flickr
     * 
     * @author: saymon
     **/

    function deleteIdentifierInstagram(id, collection) {

        var src = $('#src').val();

        swal({
            title: '<?php _e('Attention!','tainacan'); ?>',
            text: '<?php _e('Are you sure?','tainacan'); ?>',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: src + '/controllers/social_network/instagram_controller.php',
                    type: 'POST',
                    data: {operation: 'deleteIdentifierInstagram',
                        identifier: id,
                        collection_id: collection},
                    success: function (response) {
                        //se a gravação no banco foi realizado, a tabela é incrementada
                        if (response == true) {
                            showAlertGeneral('<?php _e('Success','tainacan'); ?>', '<?php _e('Successfully excluded identifier.','tainacan'); ?>', 'success');
                            listTableInstagram();
                        }
                        else {
                            showAlertGeneral('<?php _e('Error','tainacan'); ?>', '<?php _e('Identifier not excluded','tainacan'); ?>', 'error');
                        }
                    }
                });
            }
        });


    }// fim de deleteIdentifierFacebook()



</script>