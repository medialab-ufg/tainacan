<script>

    change_breadcrumbs_title('<?php _e('Social Networks','tainacan') ?>');

    /* @name: listTableYoutube()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais salvos no banco
     * 
     * @author: saymon
     **/
    function listTableYoutube() {

        var src = $('#src').val();
        var collectionId = $('#collection_id').val();

        $("#btn_identifiers_youtube_update").hide();
        $("#btn_identifiers_youtube_cancel").hide();
        $("#loader_videos").hide();

        $.ajax({
            url: src + '/controllers/social_network/youtube_controller.php',
            type: 'POST',
            data: {operation: 'listIdentifiersYoutube',
                collectionId: collectionId
            },
            success: function (data) {
                if (data) {
                    var jsonObject = jQuery.parseJSON(data);
                    if (jsonObject && jsonObject != null) {
                        $("#table_youtube_identifiers").html('');
                        $.each(jsonObject.identifier, function (id, object) {
                            if (object.playlist == '') {
                                object.updateClick = "<a href='#' onclick='updateVideosYoutube(this," + object.id + ",\"" + object.playlist + "\",\"" + object.lastId + "\")'><span class='glyphicon glyphicon-save'></span></a>";
                                object.playlist = '<?php _e('Get all videos, no playlist specified.', 'tainacan'); ?>';
                            }
                            else
                            {
                                object.updateClick = "<a href='#' ><span style='opacity:0.4' class='glyphicon glyphicon-save'></span></a>";
                            }
                            if (object.importStatus == 0) {
                                $("#table_youtube_identifiers").append("<tr><td>" + object.name + "</td>" +
                                        "<td>" + object.playlist + "</td>" +
                                        "<td><a href='#' onclick='editIdentifierYoutube(" + object.id + "," + collectionId + ",this,\"" + object.playlist + "\")'><span class='glyphicon glyphicon-pencil'></span></a></td>" +
                                        "<td><a href='#' onclick='deleteIdentifierYoutube(" + object.id + "," + collectionId + " )'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                        "<td><a href='#' onclick='importVideosYoutube(this," + object.id + ",\"" + object.playlist + "\")'><span class='glyphicon glyphicon-arrow-down'></span></a></td>" +
                                        "<td><a href='#' ><span style='opacity:0.4' class='glyphicon glyphicon-save'></span></a></td></tr>");
                            }
                            else {
                                $("#table_youtube_identifiers").append("<tr><td>" + object.name + "</td>" +
                                        "<td>" + object.playlist + "</td>" +
                                        "<td><a href='#' ><span style='opacity:0.4' class='glyphicon glyphicon-pencil'></span></a></td>" +
                                        "<td><a href='#' onclick='deleteIdentifierYoutube(" + object.id + "," + collectionId + " )'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                        "<td><a href='#' ><span style='opacity:0.4' class='glyphicon glyphicon-arrow-down'> " + object.lastUpdate + "/" + object.earlierUpdate + "</span></a></td>" +
                                        "<td>" + object.updateClick + "</td></tr>");
                            }
                        });
                        $("#table_youtube_identifiers").show();
                    }
                } // caso o controller retorne false
            }
        });// fim da inclusão de identificador youtube 
    }


    /* @name: listTableVimeo()
     * @description: cria dinamicamente uma tabela contendo
     * os identificadores de canais vimeo salvos no banco
     * 
     * @author: saymon
     **/

    function listTableVimeo() {

        var src = $('#src').val();
        var collectionId = $('#collection_id').val();

        $("#btn_identifiers_vimeo_update").hide();
        $("#btn_identifiers_vimeo_cancel").hide();
        $("#loader_videos").hide();

        $.ajax({
            url: src + '/controllers/social_network/vimeo_controller.php',
            type: 'POST',
            data: {operation: 'listIdentifiersVimeo',
                collectionId: collectionId
            },
            success: function (data) {
                if (data) {
                    var jsonObject = jQuery.parseJSON(data);
                    if (jsonObject && jsonObject != null) {
                        $("#table_vimeo_identifiers").html('');
                        $.each(jsonObject.identifier, function (id, object) {
                            if (object.importStatus == 0) {
                                $("#table_vimeo_identifiers").append("<tr><td>" + object.name + "</td>" +
                                        "<td><a href='#' onclick='editIdentifierVimeo(" + object.id + "," + collectionId + ",this )'><span class='glyphicon glyphicon-pencil'></span></a></td>" +
                                        "<td><a href='#' onclick='deleteIdentifierVimeo(" + object.id + "," + collectionId + " )'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                        "<td><a href='#' onclick='importVideosVimeo(this)'><span class='glyphicon glyphicon-arrow-down'></span></a></td>" +
                                        "<td><a href='#' onclick='updateVideosVimeo(this)'><span class='glyphicon glyphicon-save'></span></a></td></tr>");
                            }
                            else {
                                $("#table_youtube_identifiers").append("<tr><td>" + object.name + "</td>" +
                                        "<td><a href='#' onclick='editIdentifierVimeo(" + object.id + "," + collectionId + ",this )'><span class='glyphicon glyphicon-pencil'></span></a></td>" +
                                        "<td><a href='#' onclick='deleteIdentifierVimeo(" + object.id + "," + collectionId + " )'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                        "<td><a href='#' ><span style='opacity:0.4' class='glyphicon glyphicon-arrow-down'> Última atualização - " + object.lastUpdate + "</span></a></td>" +
                                        "<td><a href='#' onclick='updateVideosVimeo(this)'><span class='glyphicon glyphicon-save'></span></a></td></tr>");
                            }
                        });
                        $("#table_vimeo_identifiers").show();
                    }
                } // caso o controller retorne false
            }
        });// fim da inclusão de identificador youtube */
    }



    /* @name: listTableFlickr()
     * @description: cria dinamicamente uma tabela contendo
     * os nomes de usuários de perfis do flickr
     * 
     * @author: saymon
     **/
    function listTableFlickr() {

        var src = $('#src').val();
        var collectionId = $('#collection_id').val();

        $("#btn_identifiers_flickr_update").hide();
        $("#btn_identifiers_flickr_cancel").hide();
        $("#loader_videos").hide();

        $.ajax({
            url: src + '/controllers/social_network/flickr_controller.php',
            type: 'POST',
            data: {operation: 'listIdentifiersFlickr',
                collectionId: collectionId
            },
            success: function (data) {
                if (data) {
                    var jsonObject = jQuery.parseJSON(data);
                    if (jsonObject && jsonObject != null) {
                        $("#table_flickr_identifiers").html('');
                        $.each(jsonObject.identifier, function (id, object) {
                            if (object.importStatus == 0) {
                                $("#table_flickr_identifiers").append("<tr><td>" + object.name + "</td>" +
                                        "<td><a href='#' onclick='editIdentifierFlickr(" + object.id + "," + collectionId + ",this )'><span class='glyphicon glyphicon-pencil'></span></a></td>" +
                                        "<td><a href='#' onclick='deleteIdentifierFlickr(" + object.id + "," + collectionId + " )'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                        "<td><a href='#' onclick='importPhotosFlickr(this," + object.id + ")'><span class='glyphicon glyphicon-arrow-down'></span></a></td>" +
                                        "<td><a href='#' ><span style='opacity:0.4' class='glyphicon glyphicon-save'></span></a></td></tr>");
                            }
                            else {
                                $("#table_flickr_identifiers").append("<tr><td>" + object.name + "</td>" +
                                        "<td><a href='#' ><span style='opacity:0.4' class='glyphicon glyphicon-pencil'></span></a></td>" +
                                        "<td><a href='#' onclick='deleteIdentifierFlickr(" + object.id + "," + collectionId + " )'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                        "<td><a href='#' ><span style='opacity:0.4' class='glyphicon glyphicon-arrow-down'> " + object.lastUpdate + "</span></a></td>" +
                                        "<td><a href='#' onclick='updatePhotosFlickr(this," + object.id + ")'><span class='glyphicon glyphicon-save'></span></a></td></tr>");
                            }
                        });
                        $("#table_flickr_identifiers").show();
                    }
                } // caso o controller retorne false
            }
        });// fim da inclusão de identificador youtube 
    }


    /* @name: listTableFacebook()
     * @description: cria dinamicamente uma tabela contendo
     * os nomes de usuários de perfis do facebook
     * 
     * @author: saymon
     **/
    function listTableFacebook() {

        var src = $('#src').val();
        var collectionId = $('#collection_id').val();

        $("#btn_identifiers_facebook_update").hide();
        $("#btn_identifiers_facebook_cancel").hide();
        $("#loader_videos").hide();

        $.ajax({
            url: src + '/controllers/social_network/facebook_controller.php',
            type: 'POST',
            data: {operation: 'listIdentifiersFacebook',
                collectionId: collectionId
            },
            success: function (data) {
                if (data) {
                    var jsonObject = jQuery.parseJSON(data);
                    if (jsonObject && jsonObject != null) {
                        $("#table_facebook_identifiers").html('');
                        $.each(jsonObject.identifier, function (id, object) {
                            if (object.importStatus == 0) {
                                $("#table_facebook_identifiers").append("<tr><td>" + object.name + "</td>" +
                                        "<td><a href='#' onclick='editIdentifierFacebook(" + object.id + "," + collectionId + ",this )'><span class='glyphicon glyphicon-pencil'></span></a></td>" +
                                        "<td><a href='#' onclick='deleteIdentifierFacebook(" + object.id + "," + collectionId + " )'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                        "<td><a href='#' onclick='importPhotosFacebook(this," + object.id + ")'><span class='glyphicon glyphicon-arrow-down'></span></a></td>" +
                                        "<td><a href='#' ><span style='opacity:0.4' class='glyphicon glyphicon-save'></span></a></td></tr>");
                            }
                            else {
                                $("#table_facebook_identifiers").append("<tr><td>" + object.name + "</td>" +
                                        "<td><a href='#' onclick='editIdentifierFacebook(" + object.id + "," + collectionId + ",this )'><span class='glyphicon glyphicon-pencil'></span></a></td>" +
                                        "<td><a href='#' onclick='deleteIdentifierFacebook(" + object.id + "," + collectionId + " )'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                        "<td><a href='#' ><span style='opacity:0.4' class='glyphicon glyphicon-arrow-down'> " + object.lastUpdate + "</span></a></td>" +
                                        "<td><a href='#' onclick='updateVideosFacebook(this," + object.id + ")'><span class='glyphicon glyphicon-save'></span></a></td></tr>");
                            }
                        });
                        $("#table_facebook_identifiers").show();
                    }
                } // caso o controller retorne false
            }
        });// fim da listagem de identificadores facebook
    }


    /* @name: listTableInstagram()
     * @description: cria dinamicamente uma tabela contendo
     * os nomes de usuários de perfis do instagram
     * 
     * @author: saymon
     **/
    function listTableInstagram() {

        var src = $('#src').val();
        var collectionId = $('#collection_id').val();

        $("#btn_identifiers_instagram_update").hide();
        $("#btn_identifiers_instagram_cancel").hide();
        $("#loader_videos").hide();

        $.ajax({
            url: src + '/controllers/social_network/instagram_controller.php',
            type: 'POST',
            data: {operation: 'listIdentifiersInstagram',
                collectionId: collectionId
            },
            success: function (data) {
                if (data) {
                    var jsonObject = jQuery.parseJSON(data);
                    if (jsonObject && jsonObject != null) {
                        $("#table_instagram_identifiers").html('');
                        $.each(jsonObject.identifier, function (id, object) {
                            if (object.importStatus == 0) {
                                $("#table_instagram_identifiers").append("<tr><td>" + object.name + "</td>" +
                                        "<td><a href='#' onclick='editIdentifierInstagram(" + object.id + "," + collectionId + ",this )'><span class='glyphicon glyphicon-pencil'></span></a></td>" +
                                        "<td><a href='#' onclick='deleteIdentifierInstagram(" + object.id + "," + collectionId + " )'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                        "<td><a href='" + src + "/controllers/social_network/instagram_controller.php?collection_id=" + collectionId + "&operation=getPhotosInstagram"
                                        + "&identifier=" + object.name + "&post_id=" + object.id + "'><span class='glyphicon glyphicon-arrow-down'></span></a></td>" +
                                        "<td><a href='#' ><span style='opacity:0.4' class='glyphicon glyphicon-save'></span></a></td></tr>");
                            }
                            else {
                                $("#table_instagram_identifiers").append("<tr><td>" + object.name + "</td>" +
                                        "<td><a href='#' ><span style='opacity:0.4' class='glyphicon glyphicon-pencil'></span></a></td>" +
                                        "<td><a href='#' onclick='deleteIdentifierInstagram(" + object.id + "," + collectionId + " )'><span class='glyphicon glyphicon-trash'></span></a></td>" +
                                        "<td><a href='#' ><span style='opacity:0.4' class='glyphicon glyphicon-arrow-down'> " + object.lastUpdate + "</span></a></td>" +
                                        //"<td><a href='#' onclick='updateVideosInstagram(this," + object.id + ",\"" + object.lastId + "\")'><span class='glyphicon glyphicon-save'></span></a></td></tr>");
                                        "<td><a href='" + src + "/controllers/social_network/instagram_controller.php?collection_id=" + collectionId + "&operation=getPhotosInstagram&real_op=updatePhotosInstagram"
                                        + "&identifier=" + object.name + "&post_id=" + object.id + "&lastDate=" + object.lastUpdate + "'><span class='glyphicon glyphicon-save'></span></a></td></tr>");
                            }
                        });
                        $("#table_instagram_identifiers").show();
                    }
                } // caso o controller retorne false
            }
        });// fim da listagem de identificadores instagram
    }

    function edit_mapping(social) {
        var src = $('#src').val();
        $.ajax({
            url: src + '/controllers/social_network/social_mapping_controller.php',
            type: 'POST',
            data: {operation: 'show_mapping', collection_id: $("#collection_id").val(), 'social_network': social}
        }).done(function (result) {
            $('#list_social_network').hide();
            $('#edit_mapping').html(result).show();
        });
    }

    $(document).ready(function () {
        //insertIdentifierYoutube();
        //insertIdentifierInstagram();
        //insertIdentifierFlickr();
        //insertIdentifierFacebook();
        //listTableFacebook();
        //listTableInstagram();
        listTableFlickr();
        listTableYoutube();
    });

</script>