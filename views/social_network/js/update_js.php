<script>

    /* @name: updateVideosYoutube()
     * @parameters: id, collection
     * @description: importar os videos de um dado canal publicados após a primeira
     * importação ou a última atualização
     * 
     * @author: saymon
     **/

    function updateVideosYoutube(object, id, playlist, lastId) {

        var src = $('#src').val();
        var identifier = $(object).closest("tr").find("td:first").html();
        var data = $(object).closest("tr").find("td:last").prev().find("span:first").html();
        $.ajax({
            url: src + '/controllers/social_network/youtube_controller.php',
            type: 'POST',
            beforeSend: function () {
                $("#loader_videos").show();
            },
            data: {operation: 'updateVideosYoutube',
                identifier: identifier,
                identifierId: id,
                playlist: playlist,
                lastId: lastId,
                data: data,
                collection_id: $("#collection_id").val()
            },
            success: function (response) {
                //se a gravação no banco foi realizado, a tabela é incrementada
                $("#loader_videos").hide();
                listTableYoutube();
                if (response == true) {
                    showAlertGeneral('<?php _e('Success','tainacan'); ?>', '<?php _e('New videos imported successfully.','tainacan'); ?>', 'success');
                }
                else {
                    showAlertGeneral('<?php _e('Info','tainacan'); ?>', '<?php _e('No new videos added since the last import.','tainacan'); ?>', 'info');
                }
            }
        });
    }// fim de updateVideosYoutube()



    /* @name: updateVideosVimeo()
     * @parameters: id, collection
     * @description: importar os videos de um dado canal publicados após a primeira
     * importação ou a última atualização
     * 
     * @author: saymon
     **/

    function updateVideosVimeo(id, collection) {

        var src = $('#src').val();
        //var date = '';

        $.ajax({
            url: src + '/controllers/social_network/vimeo_controller.php',
            type: 'POST',
            data: {operation: 'updateVideosVimeo',
                identifier: id,
                collection_id: collection
            },
            success: function (response) {
                //se a gravação no banco foi realizado, a tabela é incrementada
                if (response == true) {
                    alert("Novos videos importados com sucesso");
                    listTable();
                }
                else {
                    alert("Nenhum video novo adicionado");
                }
            }
        });
    }// fim de updateVideosVimeo()



    /* @name: updatePhotosFlickr()
     * @parameters: id, collection
     * @description: importar os videos de um dado canal publicados após a primeira
     * importação ou a última atualização
     * 
     * @author: saymon
     **/
    function updatePhotosFlickr(object, id) {

        var src = $('#src').val();
        var identifier = $(object).closest("tr").find("td:first").html();
        var data = $(object).closest("tr").find("td:last").prev().find("span:first").html();
        $.ajax({
            url: src + '/controllers/social_network/flickr_controller.php',
            type: 'POST',
            beforeSend: function () {
                $("#loader_videos").show();
            },
            data: {operation: 'updatePhotosFlickr',
                identifier: identifier,
                identifierId: id,
                data: data,
                collection_id: $("#collection_id").val()
            },
            success: function (response) {
                //se a gravação no banco foi realizado, a tabela é incrementada
                if (response == true) {
                    //alert("Novos videos importados com sucesso");
                    $("#loader_videos").hide();
                    showAlertGeneral('<?php _e('Success','tainacan'); ?>', '<?php _e('Photos imported successfully.','tainacan'); ?>', 'success');
                    listTableFlcikr();
                }
                else {
                    showAlertGeneral('<?php _e('Info','tainacan'); ?>', '<?php _e('No new image added since the last import.','tainacan'); ?>', 'info');
                    //alert("Nenhum video novo adicionado desde a última importação");
                    $("#loader_videos").hide();
                    listTableFlickr();
                }
            }
        });
    }// fim de updatePhotosFlickr()


    /* @name: updatePhotosFacebook()
     * @parameters: id, collection
     * @description: importar os videos de um dado canal publicados após a primeira
     * importação ou a última atualização
     * 
     * @author: saymon
     **/
    function updatePhotosFacebook(object, id) {

        var src = $('#src').val();
        var identifier = $(object).closest("tr").find("td:first").html();
        var data = $(object).closest("tr").find("td:last").prev().find("span:first").html();
        $.ajax({
            url: src + '/controllers/social_network/facebook_controller.php',
            type: 'POST',
            beforeSend: function () {
                $("#loader_videos").show();
            },
            data: {operation: 'updatePhotosFacebook',
                identifier: identifier,
                identifierId: id,
                data: data,
                collection_id: $("#collection_id").val()
            },
            success: function (response) {
                //se a gravação no banco foi realizado, a tabela é incrementada
                if (response == true) {
                    //alert("Novos videos importados com sucesso");
                    $("#loader_videos").hide();
                    showAlertGeneral('socialDB import', 'videos importados com sucesso', 'success');
                    listTableFlcikr();
                }
                else {
                    //alert("Nenhum video novo adicionado desde a última importação");
                    $("#loader_videos").hide();
                    listTableFacebook();
                }
            }
        });
    }// fim de updatePhotosFlickr()


    /* @name: updateVideosInstagram()
     * @parameters: id, collection
     * @description: importar as imagens e videos de um dado instagram publicados após a primeira
     * importação ou a última atualização
     * 
     * @author: Marcus
     **/
    function updateVideosInstagram(object, id, lastId) {
        
        var src = $('#src').val();
        var identifier = $(object).closest("tr").find("td:first").html();
        $.ajax({
            url: src + '/controllers/social_network/instagram_controller.php',
            type: 'POST',
            beforeSend: function () {
                $("#loader_videos").show();
            },
            data: {operation: 'getPhotosInstagram',
                real_op: 'updatePhotosInstagram',
                identifier: identifier,
                post_id: id,
                lastId: lastId,
                collection_id: $("#collection_id").val()
            },
            success: function (response) {
                //se a gravação no banco foi realizado, a tabela é incrementada
                $("#loader_videos").hide();
                listTableInstagram();
                if (response == true) {
                    showAlertGeneral('socialDB import', 'videos importados com sucesso', 'success');
                }
                else {
                    showAlertGeneral('socialDB import', 'Nenhum video novo adicionado desde a última importação', 'info');

                }
            }
        });
    }// fim de updateVideosInstagram()

</script>
