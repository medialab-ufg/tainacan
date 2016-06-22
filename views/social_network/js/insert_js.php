<script>

/* @name: insertIdentifierYoutube()
 * @description: atrela aos botões dos formulários que contem as tabelas
 * de identificadores de canais do youtube eventos de clicks que dispararão uma requisição ajax
 * para inserir no banco o valor da input #youtube_identifier_input
 * 
 * @author: saymon
 **/

function insertIdentifierYoutube() {
    
    $('#btn_identifiers_youtube').on('click', function(event){
        //pega o valor da input text #youtube_identifier_input
        var inputIdentifierYoutube = $('#youtube_identifier_input').val().trim();
        var inputPlaylistYoutube = $('#youtube_playlist_identifier_input').val().trim();
        var collectionId = $('#collection_id').val();
        
        if (inputIdentifierYoutube) {
            
            var src = $('#src').val();
    
            $.ajax({
                url: src+'/controllers/social_network/youtube_controller.php',
                type: 'POST',
                data: {operation: 'InsertIdentifierYoutube',
                       identifier: inputIdentifierYoutube, 
                       playlist: inputPlaylistYoutube, 
                       collectionId: collectionId },
                success: function(response) {
                    //se a gravação no banco foi realizado, a tabela é incrementada
                    if (response == true) {
                        listTableYoutube();
                    }
                    else {
                        showAlertGeneral('<?php _e('Error','tainacan'); ?>','<?php _e('Identifier unsaved','tainacan'); ?>','error');
                    }
                }
            });
            $('#youtube_identifier_input').val('');
            $('#youtube_playlist_identifier_input').val('');
        }
        else {
            showAlertGeneral('<?php _e('Error','tainacan'); ?>','<?php _e('Necessary to inform Youtube channel identifier','tainacan'); ?>','error');
        }
        event.stopImmediatePropagation();
    });// fim da inclusão de identificador youtube
}


/* @name: insertIdentifierVimeo()
 * @description: atrela aos botões dos formulários que contem as tabelas
 * de identificadores de canais do vimeo eventos de clicks que dispararão uma requisição ajax
 * para inserir no banco o valor da input #youtube_identifier_input
 * 
 * @author: saymon
 **/

function insertIdentifierVimeo() {
    
    $('#btn_identifiers_vimeo').on('click', function(event){
        //pega o valor da input text #youtube_identifier_input
        var inputIdentifierVimeo = $('#vimeo_identifier_input').val().trim();
        var collectionId = $('#collection_id').val();
        
        if (inputIdentifierVimeo) {
            
            var src = $('#src').val();
    
            $.ajax({
                url: src+'/controllers/social_network/vimeo_controller.php',
                type: 'POST',
                data: {operation: 'insertIdentifierVimeo',
                       identifier: inputIdentifierVimeo, 
                       collectionId: collectionId },
                success: function(response) {
                    //se a gravação no banco foi realizado, a tabela é incrementada
                    if (response == true ) {
                        listTableVimeo();
                    }
                    else {
                        alert('Identificador não salvo');
                    }
    
                }
                
            });
            $('#vimeo_identifier_input').val('');
        }
        else {
            
            alert('Necessário informar identificador de canal Vimeo');
        }
        event.stopImmediatePropagation();
    });// fim da inclusão de identificador vimeo
}


/* @name: insertIdentifierFlickr()
 * @description: atrela aos botões dos formulários que contem as tabelas
 * de identificadores de nomes de usuário de perfis do flickr
 * eventos de clicks que dispararão uma requisição ajax
 * para inserir no banco o valor da input #flickr_identifier_input
 * 
 * @author: saymon
 **/

function insertIdentifierFlickr() {
    
    $('#btn_identifiers_flickr').on('click', function(event){
        //pega o valor da input text #youtube_identifier_input
        var inputIdentifierFlickr = $('#flickr_identifier_input').val().trim();
        var collectionId = $('#collection_id').val();
        
        if (inputIdentifierFlickr) {
            
            var src = $('#src').val();
    
            $.ajax({
                url: src+'/controllers/social_network/flickr_controller.php',
                type: 'POST',
                data: {operation: 'insertIdentifierFlickr',
                       identifier: inputIdentifierFlickr, 
                       collectionId: collectionId },
                success: function(response) {
                    //se a gravação no banco foi realizado, a tabela é incrementada
                    if (response == true) {
                        listTableFlickr();
                    }
                    else {
                        alert('Identificador não salvo');
                    }
                }
            });
            $('#flickr_identifier_input').val('');
        }
        else {
            alert('Necessário informar identificador de usuário Flickr');
        }
        event.stopImmediatePropagation();
    });// fim da inclusão de identificador youtube
}


/* @name: insertIdentifierFacebook()
 * @description: atrela aos botões dos formulários que contem as tabelas
 * de identificadores de nomes de usuário de perfis do flickr
 * eventos de clicks que dispararão uma requisição ajax
 * para inserir no banco o valor da input #flickr_identifier_input
 * 
 * @author: saymon
 **/

function insertIdentifierFacebook() {
    
    $('#btn_identifiers_facebook').on('click', function(event){
        //pega o valor da input text #youtube_identifier_input
        var inputIdentifierFacebook = $('#facebook_identifier_input').val().trim();
        var collectionId = $('#collection_id').val();
        
        if (inputIdentifierFacebook) {
            
            var src = $('#src').val();
    
            $.ajax({
                url: src+'/controllers/social_network/facebook_controller.php',
                type: 'POST',
                data: {operation: 'insertIdentifierFacebook',
                       identifier: inputIdentifierFacebook, 
                       collectionId: collectionId },
                success: function(response) {
                    //se a gravação no banco foi realizado, a tabela é incrementada
                    if (response == true) {
                        listTableFacebook();
                    }
                    else {
                        alert('Identificador não salvo');
                    }
                }
            });
            $('#facebook_identifier_input').val('');
        }
        else {
            alert('Necessário informar identificador de canal Facebook');
        }
        event.stopImmediatePropagation();
    });// fim da inclusão de identificador youtube
}

/* @name: insertIdentifierInstagram()
 * @description: atrela aos botões dos formulários que contem as tabelas
 * de identificadores de nomes de usuário de perfis do flickr
 * eventos de clicks que dispararão uma requisição ajax
 * para inserir no banco o valor da input #instagram_identifier_input
 * 
 * @author: saymon
 **/

function insertIdentifierInstagram() {
    
    $('#btn_identifiers_instagram').on('click', function(event){
        //pega o valor da input text #youtube_identifier_input
        var inputIdentifierInstagram = $('#instagram_identifier_input').val().trim();
        var collectionId = $('#collection_id').val();
        
        if (inputIdentifierInstagram) {
            
            var src = $('#src').val();
    
            $.ajax({
                url: src+'/controllers/social_network/instagram_controller.php',
                type: 'POST',
                data: {operation: 'insertIdentifierInstagram',
                       identifier: inputIdentifierInstagram, 
                       collectionId: collectionId },
                success: function(response) {
                    //se a gravação no banco foi realizado, a tabela é incrementada
                    if (response == true) {
                        listTableInstagram();
                    }
                    else {
                        alert('Identificador não salvo');
                    }
                }
            });
            $('#instagram_identifier_input').val('');
        }
        else {
            alert('Necessário informar identificador de usuário Instagram');
        }
        event.stopImmediatePropagation();
    });// fim da inclusão de identificador youtube
}

</script>