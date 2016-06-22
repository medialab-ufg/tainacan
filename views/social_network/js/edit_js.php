<script>

    /* @name: editIdentifier()
     * @parameters: id, null, object
     * @description: edita no banco o identificador de um dado canal
     * 
     * @author: saymon
     **/

    function editIdentifierYoutube(id, collection, object, playlist) {
        var currentId = id;
        $("#youtube_identifier_input").focus(function (event) {
            $("#btn_identifiers_youtube").hide();
            $("#btn_identifiers_youtube_update").show();
            $("#btn_identifiers_youtube_cancel").show();
            $("#btn_identifiers_youtube_update").one("click", function () {
                saveEditedIdentifierYoutube(currentId);

            });
            $("#btn_identifiers_youtube_cancel").on("click", function () {
                cancelEditIdentifierYoutube();
            });
        });

        var identifier = $(object).closest("tr").find("td:first").html();
        $("#youtube_identifier_input").val(identifier).focus();
        $("#youtube_identifier_input").off('focus');
        if (playlist == '<?php _e('Get all videos, no playlist specified.','tainacan') ?>') {
            playlist = '';
        }
        $("#youtube_playlist_identifier_input").val(playlist);
        event.stopImmediatePropagation();

    }// fim de editIdentifier()

    /* @name: cancelEditIdentifier()
     * @parameters: 
     * @description: cancela a edição do identificador de um dado canal listado na
     * tabela
     * 
     * @author: saymon
     **/


    function cancelEditIdentifierYoutube() {
        $("#btn_identifiers_youtube_cancel").hide();
        $("#btn_identifiers_youtube_update").hide();
        $("#btn_identifiers_youtube").show();
        $("#youtube_identifier_input").val('');
        $("#youtube_identifier_input").off('focus');
    }

    /* @name: saveEditedIdentifier()
     * @parameters: valId (id de um canal listado na tabela de identificadores de
     * canais youtube)
     * @description: salva a edição do identificador de um dado canal listado na
     * tabela e no banco
     * 
     * @author: saymon
     **/


    function saveEditedIdentifierYoutube(valId) {
        var src = $('#src').val();
        var newIdentifier = $("#youtube_identifier_input").val().trim();
        var newPlaylist = $("#youtube_playlist_identifier_input").val().trim();
        $.ajax({
            url: src + '/controllers/social_network/youtube_controller.php',
            type: 'POST',
            data: {operation: 'editIdentifierYoutube',
                identifier: valId,
                new_identifier: newIdentifier,
                new_playlist: newPlaylist},
            success: function (response) {
                //se a gravação no banco foi realizado, a tabela é incrementada
                if (response) {
                    listTableYoutube();
                }
                else {
                    showAlertGeneral('<?php _e('Error','tainacan'); ?>', '<?php _e('Identifier unsaved','tainacan'); ?>', 'error');
                }
            }
        });

        $("#btn_identifiers_youtube").show();
        $("#youtube_identifier_input").val('');
        $("#youtube_playlist_identifier_input").val('');
        $("#btn_identifiers_youtube_cancel").hide();
        $("#btn_identifiers_youtube_update").hide();
        //$("#youtube_identifier_input").off('focus');
    }// fim do evento de edição de identificador 





    /* @name: editIdentifierVimeo()
     * @parameters: id, null, object
     * @description: edita no banco o identificador de um dado canal
     * 
     * @author: saymon
     **/

    function editIdentifierVimeo(id, collection, object) {
        var currentId = id;
        $("#vimeo_identifier_input").focus(function (event) {
            $("#btn_identifiers_vimeo").hide();
            $("#btn_identifiers_vimeo_update").show();
            $("#btn_identifiers_vimeo_cancel").show();
            $("#btn_identifiers_vimeo_update").one("click", function () {
                saveEditedIdentifierVimeo(currentId);

            });
            $("#btn_identifiers_vimeo_cancel").on("click", function () {
                cancelEditIdentifierVimeo();
            });
        });

        var identifier = $(object).closest("tr").find("td:first").html();
        $("#vimeo_identifier_input").val(identifier).focus();
        $("#vimeo_identifier_input").off('focus');
        event.stopImmediatePropagation();

    }// fim de editIdentifier()

    /* @name: cancelEditIdentifier()
     * @parameters: 
     * @description: cancela a edição do identificador de um dado canal listado na
     * tabela
     * 
     * @author: saymon
     **/


    function cancelEditIdentifierVimeo() {
        $("#btn_identifiers_vimeo_cancel").hide();
        $("#btn_identifiers_vimeo_update").hide();
        $("#btn_identifiers_vimeo").show();
        $("#vimeo_identifier_input").val('');
        $("#vimeo_identifier_input").off('focus');
    }

    /* @name: saveEditedIdentifierVimeo()
     * @parameters: valId (id de um canal listado na tabela de identificadores de
     * canais youtube)
     * @description: salva a edição do identificador de um dado canal listado na
     * tabela e no banco
     * 
     * @author: saymon
     **/


    function saveEditedIdentifierVimeo(valId) {
        var src = $('#src').val();
        var newIdentifier = $("#vimeo_identifier_input").val().trim();

        $.ajax({
            url: src + '/controllers/social_network/vimeo_controller.php',
            type: 'POST',
            data: {operation: 'editIdentifierVimeo',
                identifier: valId,
                new_identifier: newIdentifier},
            success: function (response) {
                //se a gravação no banco foi realizado, a tabela é incrementada
                if (response) {
                    listTable();
                }
                else {
                    showAlertGeneral('<?php _e('Error','tainacan'); ?>', '<?php _e('Identifier unsaved','tainacan'); ?>', 'error');
                }
            }
        });

        $("#btn_identifiers_vimeo").show();
        $("#vimeo_identifier_input").val('');
        $("#btn_identifiers_vimeo_cancel").hide();
        $("#btn_identifiers_vimeo_update").hide();
        //$("#youtube_identifier_input").off('focus');
    }// fim do evento de edição de identificador 



    /* @name: editIdentifierFlickr()
     * @parameters: id, null, object
     * @description: edita no banco o identificador de um dado canal
     * 
     * @author: saymon
     **/

    function editIdentifierFlickr(id, collection, object) {
        var currentId = id;
        $("#flickr_identifier_input").focus(function (event) {
            $("#btn_identifiers_flickr").hide();
            $("#btn_identifiers_flickr_update").show();
            $("#btn_identifiers_flickr_cancel").show();
            $("#btn_identifiers_flickr_update").one("click", function () {
                saveEditedIdentifierFlickr(currentId);

            });
            $("#btn_identifiers_flickr_cancel").on("click", function () {
                cancelEditIdentifierFlickr();
            });
        });

        var identifier = $(object).closest("tr").find("td:first").html();
        $("#flickr_identifier_input").val(identifier).focus();
        $("#flickr_identifier_input").off('focus');
        event.stopImmediatePropagation();

    }// fim de editIdentifierFlickr()

    /* @name: cancelEditIdentifierFlickr()
     * @parameters: 
     * @description: cancela a edição do identificador de um dado canal listado na
     * tabela
     * 
     * @author: saymon
     **/


    function cancelEditIdentifierFlickr() {
        $("#btn_identifiers_flickr_cancel").hide();
        $("#btn_identifiers_flickr_update").hide();
        $("#btn_identifiers_flickr").show();
        $("#flickr_identifier_input").val('');
        $("#flickr_identifier_input").off('focus');
    }

    /* @name: saveEditedIdentifierFlickr()
     * @parameters: valId (id de um canal listado na tabela de identificadores de
     * canais youtube)
     * @description: salva a edição do identificador de um dado canal listado na
     * tabela e no banco
     * 
     * @author: saymon
     **/


    function saveEditedIdentifierFlickr(valId) {
        var src = $('#src').val();
        var newIdentifier = $("#flickr_identifier_input").val().trim();

        $.ajax({
            url: src + '/controllers/social_network/flickr_controller.php',
            type: 'POST',
            data: {operation: 'editIdentifierFlickr',
                identifier: valId,
                new_identifier: newIdentifier},
            success: function (response) {
                //se a gravação no banco foi realizado, a tabela é incrementada
                if (response) {
                    listTableFlickr();
                }
                else {
                    showAlertGeneral('<?php _e('Error','tainacan'); ?>', '<?php _e('Identifier unsaved','tainacan'); ?>', 'error');
                }
            }
        });

        $("#btn_identifiers_flickr").show();
        $("#flickr_identifier_input").val('');
        $("#btn_identifiers_flickr_cancel").hide();
        $("#btn_identifiers_flickr_update").hide();
        //$("#youtube_identifier_input").off('focus');
    }// fim do evento de edição de nomes de usuário do flickr 



    /* @name: editIdentifierFacebook()
     * @parameters: id, null, object
     * @description: edita no banco o identificador de um dado canal
     * 
     * @author: saymon
     **/
    function editIdentifierFacebook(id, collection, object) {
        var currentId = id;
        $("#facebook_identifier_input").focus(function (event) {
            $("#btn_identifiers_facebook").hide();
            $("#btn_identifiers_facebook_update").show();
            $("#btn_identifiers_facebook_cancel").show();
            $("#btn_identifiers_facebook_update").one("click", function () {
                saveEditedIdentifierFacebook(currentId);

            });
            $("#btn_identifiers_facebook_cancel").on("click", function () {
                cancelEditIdentifierFacebook();
            });
        });

        var identifier = $(object).closest("tr").find("td:first").html();
        $("#facebook_identifier_input").val(identifier).focus();
        $("#facebook_identifier_input").off('focus');
        event.stopImmediatePropagation();

    }// fim de editIdentifierFacebook()

    /* @name: cancelEditIdentifierFacebook()
     * @parameters: 
     * @description: cancela a edição do identificador de um dado canal listado na
     * tabela
     * 
     * @author: saymon
     **/


    function cancelEditIdentifierFacebook() {
        $("#btn_identifiers_facebook_cancel").hide();
        $("#btn_identifiers_facebook_update").hide();
        $("#btn_identifiers_facebook").show();
        $("#facebook_identifier_input").val('');
        $("#facebook_identifier_input").off('focus');
    }

    /* @name: saveEditedIdentifierFacebook()
     * @parameters: valId (id de um canal listado na tabela de identificadores de
     * canais youtube)
     * @description: salva a edição do identificador de um dado canal listado na
     * tabela e no banco
     * 
     * @author: saymon
     **/
    function saveEditedIdentifierFacebook(valId) {
        var src = $('#src').val();
        var newIdentifier = $("#facebook_identifier_input").val().trim();

        $.ajax({
            url: src + '/controllers/social_network/facebook_controller.php',
            type: 'POST',
            data: {operation: 'editIdentifierFacebook',
                identifier: valId,
                new_identifier: newIdentifier},
            success: function (response) {
                //se a gravação no banco foi realizado, a tabela é incrementada
                if (response) {
                    listTableFacebook();
                }
                else {
                    showAlertGeneral('<?php _e('Error','tainacan'); ?>', '<?php _e('Identifier unsaved','tainacan'); ?>', 'error');
                }
            }
        });

        $("#btn_identifiers_facebook").show();
        $("#facebook_identifier_input").val('');
        $("#btn_identifiers_facebook_cancel").hide();
        $("#btn_identifiers_facebook_update").hide();
        //$("#youtube_identifier_input").off('focus');
    }// fim do evento de edição de nomes de usuário do facebook 


    /* @name: editIdentifierFacebook()
     * @parameters: id, null, object
     * @description: edita no banco o identificador de um dado canal
     * 
     * @author: saymon
     **/
    function editIdentifierInstagram(id, collection, object) {
        var currentId = id;
        $("#instagram_identifier_input").focus(function (event) {
            $("#btn_identifiers_instagram").hide();
            $("#btn_identifiers_instagram_update").show();
            $("#btn_identifiers_instagram_cancel").show();
            $("#btn_identifiers_instagram_update").one("click", function () {
                saveEditedIdentifierInstagram(currentId);

            });
            $("#btn_identifiers_instagram_cancel").on("click", function () {
                cancelEditIdentifierInstagram();
            });
        });

        var identifier = $(object).closest("tr").find("td:first").html();
        $("#instagram_identifier_input").val(identifier).focus();
        $("#instagram_identifier_input").off('focus');
        event.stopImmediatePropagation();

    }// fim de editIdentifierInstagram()

    /* @name: cancelEditIdentifierInstagram()
     * @parameters: 
     * @description: cancela a edição do identificador de um dado canal listado na
     * tabela
     * 
     * @author: saymon
     **/


    function cancelEditIdentifierInstagram() {
        $("#btn_identifiers_instagram_cancel").hide();
        $("#btn_identifiers_instagram_update").hide();
        $("#btn_identifiers_instagram").show();
        $("#instagram_identifier_input").val('');
        $("#instagram_identifier_input").off('focus');
    }

    /* @name: saveEditedIdentifierInstagram()
     * @parameters: valId (id de um canal listado na tabela de identificadores de
     * canais youtube)
     * @description: salva a edição do identificador de um dado canal listado na
     * tabela e no banco
     * 
     * @author: saymon
     **/
    function saveEditedIdentifierInstagram(valId) {
        var src = $('#src').val();
        var newIdentifier = $("#instagram_identifier_input").val().trim();

        $.ajax({
            url: src + '/controllers/social_network/instagram_controller.php',
            type: 'POST',
            data: {operation: 'editIdentifierInstagram',
                identifier: valId,
                new_identifier: newIdentifier},
            success: function (response) {
                //se a gravação no banco foi realizado, a tabela é incrementada
                if (response) {
                    listTableInstagram();
                }
                else {
                    showAlertGeneral('<?php _e('Error','tainacan'); ?>', '<?php _e('Identifier unsaved','tainacan'); ?>', 'error');
                }
            }
        });

        $("#btn_identifiers_instagram").show();
        $("#instagram_identifier_input").val('');
        $("#btn_identifiers_instagram_cancel").hide();
        $("#btn_identifiers_instagram_update").hide();
        //$("#youtube_identifier_input").off('focus');
    }// fim do evento de edição de nomes de usuário do instagram 


</script>