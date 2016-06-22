<script>

/* @name: importVideosYoutube()
 * @description: importa videos de um determinado canal do youtube
 * 
 * @author: saymon
 **/
function importVideosYoutube(object, id, playlist){

    var src = $('#src').val();
    var identifier = $(object).closest("tr").find("td:first").html();
        
    $.ajax({
        url: src+'/controllers/social_network/youtube_controller.php',
        type: 'POST',
        beforeSend: function() {
            $("#loader_videos").show();
            //$('#modalYoutubeImportVideos').modal('show');
        },
        data: { operation: 'getVideosYoutube',
                identifier: identifier,
                playlist: playlist,
                identifierId: id,
                collection_id: $("#collection_id").val()
            },
        success: function(data) {
            //console.log(data);
            if(data) {
                $("#loader_videos").hide();
                //$('#myModal').modal("hide");
                //alert("canais importados com sucesso");
                showAlertGeneral('<?php _e('Import','tainacan'); ?>', '<?php _e('successfully imported videos','tainacan'); ?>', 'success');
                listTableYoutube();
            }
            else {
                $("#loader_videos").hide();
                showAlertGeneral('<?php _e('Import','tainacan'); ?>', '<?php _e('Nonexistent User | It has no public videos | Playlist does not belong to this channel','tainacan'); ?>', 'info');
            }
        }    
    });// fim da importaçõa de videos do youtube */
}



/* @name: importVideosVimeo()
 * @description: importa videos de um determinado canal do youtube
 * 
 * @author: saymon
 **/
function importVideosVimeo(object){

    var src = $('#src').val();
    var identifier = $(object).closest("tr").find("td:first").html();
        
    $.ajax({
        url: src+'/controllers/social_network/vimeo_controller.php',
        type: 'POST',
        beforeSend: function() {
            $("#loader_videos").show();  
        },
        data: { operation: 'getVideosVimeo',
                identifier: identifier,
                collection_id: $("#collection_id").val()
            },
        success: function(data) {
            //console.log(data);
            if(data) {
                $("#loader_videos").hide();
                showAlertGeneral('socialDB import', 'videos importaos com sucesso', 'success');
                alert("canais importados com sucesso");
                
            }
            else {
                $("#loader_videos").hide();
                showAlertGeneral('socialDB import', 'canal de usuário inexistente ou não possui videos públicas', 'info');
            }
        }    
    });// fim da importaçõa de videos do youtube */
}



/* @name: importPhotosFlickr()
 * @description: importa fotos de um determinado perfil
 * do flickr
 * 
 * @author: saymon
 **/
function importPhotosFlickr(object, id){

    var src = $('#src').val();
    var identifier = $(object).closest("tr").find("td:first").html();
        
    $.ajax({
        url: src+'/controllers/social_network/flickr_controller.php',
        type: 'POST',
        beforeSend: function() {
            $("#loader_videos").show();
            //$('#modalYoutubeImportVideos').modal('show');
        },
        data: { operation: 'getPhotosFlickr',
                identifier: identifier,
                identifierId: id,
                collection_id: $("#collection_id").val()
            },
        success: function(data) {
            //console.log(data);
            if(data) {
                $("#loader_videos").hide();
                showAlertGeneral('socialDB import', 'fotos importadas com sucesso', 'success');
                listTableFlickr();
            }
            else {
                $("#loader_videos").hide();
                showAlertGeneral('socialDB import', 'perfil de usuário inexistente ou não possui fotos públicas', 'info');
            }
        }    
    });// fim da importaçõa de videos do youtube */
}

/* @name: importPhotosFacebook()
 * @description: importa fotos de um determinado perfil
 * do flickr
 * 
 * @author: saymon
 **/
function importPhotosFacebook(object, id){

    var src = $('#src').val();
    var identifier = $(object).closest("tr").find("td:first").html();
        
    $.ajax({
        url: src+'/controllers/social_network/facebook_controller.php',
        type: 'POST',
        beforeSend: function() {
            $("#loader_videos").show();
            //$('#modalYoutubeImportVideos').modal('show');
        },
        data: { operation: 'getAccessToken',
                identifier: identifier,
                identifierId: id,
                collection_id: $("#collection_id").val()
            },
        success: function(data) {
            //console.log(data);
            if(data) {
                $("#loader_videos").hide();
                showAlertGeneral('socialDB import', 'fotos importadas com sucesso', 'success');
                listTableFacebook();
            }
            else {
                $("#loader_videos").hide();
                showAlertGeneral('socialDB import', 'perfil de usuário inexistente ou não possui fotos públicas', 'info');
            }
        }    
    });// fim da importaçõa de fotos do youtube */
}

/* @name: importPhotosInstagram()
 * @description: importa fotos de um determinado perfil
 * do instagram
 * 
 * @author: saymon
 **/
function importPhotosInstagram(object, id){

    var src = $('#src').val();
    var identifier = $(object).closest("tr").find("td:first").html();
        
    $.ajax({
        url: src+'/controllers/social_network/instagram_controller.php',
        type: 'POST',
        beforeSend: function() {
            $("#loader_videos").show();
            //$('#modalYoutubeImportVideos').modal('show');
        },
        data: { operation: 'getPhotosInstagram',
                identifier: identifier,
                identifierId: id,
                collection_id: $("#collection_id").val()
            },
        success: function(data) {
            //console.log(data);
            if(data) {
                $("#loader_videos").hide();
                showAlertGeneral('socialDB import', 'fotos importadas com sucesso', 'success');
                listTableInstagram();
            }
            else {
                $("#loader_videos").hide();
                showAlertGeneral('socialDB import', 'perfil de usuário inexistente ou não possui fotos públicas', 'info');
            }
        }    
    });// fim da importaçõa de fotos do youtube */
}



</script>
