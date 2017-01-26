<input type="hidden" id="route_blog" name="route_blog" value="<?php echo str_replace($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'], '', get_bloginfo('url')) ?>/"> <!-- utilizado na busca -->
<input type="hidden" id="goToLogin" name="goToLogin" value="<?php
    if (get_query_var('log-in')) {
        echo trim(get_query_var('log-in'));
    }
    ?>">
<input type="hidden" id="goToCollectionMetadata" name="goToCollectionMetadata" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') !== 'tainacan-collections' ) && get_query_var('metadata')) {
        echo trim(get_query_var('metadata'));
    }
    ?>">
<input type="hidden" id="goToRepositoryMetadata" name="goToRepositoryMetadata" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') === 'tainacan-collections' ) && get_query_var('metadata')) {
        echo trim(get_query_var('metadata'));
    }
    ?>">
<script>
    var previousRoute;
    /** executa a funcao que verifica se existe a necessidade de executar
        alguma rota **/
     //pagina central da colecao
    $.router.add( $('#route_blog').val()+':collection', function(data) {
        console.log('rota only collection');
        backToMainPage(); 
    });
    //pagina do item
    $.router.add( $('#route_blog').val()+':collection/:item', function(data) {
        console.log('rota item',data);
        if(data.collection == 'admin'){
            if(data.item=='<?php echo __('metadata','tainacan') ?>'){
                
            }
        }else{
             console.log(previousRoute ,window.location.pathname);
            if(previousRoute === window.location.pathname){
                $.router.go($('#route_blog').val()+$('#slug_collection').val());
            }else{
                showSingleObjectByName(data.item, $('#src').val())
            }
            
        }
        
    });
    
    $(function(){
         execute_route();
    });
    /**************************************************************************/
    
    /**
     * verifica se existe alguma rota a ser executada
     * @returns {undefined}
     */
    function execute_route(){
         $.router.reset();
        if ($('#object_page').val() !== '') {
            collection = $('#slug_collection').val();
            showSingleObjectByName($('#object_page').val() , $('#src').val())
        }else if($('#goToLogin').val()!==''){
            showLoginScreen($('#src').val());
        }else if($('#goToCollectionMetadata').val()!==''){
            console.log('redirect');
            showPropertiesAndFilters($('#src').val());
        }else if($('#goToRepositoryMetadata').val()!==''){
            showPropertiesRepository($('#src').val());
        }
    }
    
    /**
     * funcao que redireciona para a pagina
     * @param {type} page
     * @returns {undefined}
     */
    function routerGo(page){
         saveRoute();
         $.router.go($('#route_blog').val()+page, 'My cool item');
    }
    
    /**
     * salva a rota atual
     */
    function saveRoute(){
        previousRoute = window.location.pathname;
    }
    
    /**
     * retorna para a pagina anterior
     * @param {optional} collection
     * @returns {undefined}
     */
    function backRoute(collection){
        console.log(collection,previousRoute,window.location.pathname);
        if( previousRoute == window.location.pathname || !previousRoute){
            if(collection)
               $.router.go($('#route_blog').val()+collection+'/', 'My cool item');
           else
               window.redirect = window.location.pathname
        }else{
            $.router.go(previousRoute, 'My cool item');
        }
    }
</script>    
