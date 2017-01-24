<input type="hidden" id="route_blog" name="route_blog" value="<?php echo str_replace($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'], '', get_bloginfo('url')) ?>/"> <!-- utilizado na busca -->
<input type="hidden" id="goToLogin" name="goToLogin" value="<?php
    if (get_query_var('log-in')) {
        echo trim(get_query_var('log-in'));
    }
    ?>">
<script>
    var previousRoute;
    /** executa a funcao que verifica se existe a necessidade de executar
        alguma rota **/
    execute_route();
    /***************** adicionando as rotas do tainacan ***********************/
    
    //pagina central da colecao
    $.router.add( $('#route_blog').val()+':collection', function(data) {
    });
    //pagina do item
    $.router.add( $('#route_blog').val()+':collection/:item', function(data) {
        showSingleObjectByName(data.item, $('#src').val())
    });
    //pagina de login
    $.router.add( $('#route_blog').val()+':login/', function(data) {
        showLoginScreen($('#src').val());
    });
    /**************************************************************************/
    
    /**
     * verifica se existe alguma rota a ser executada
     * @returns {undefined}
     */
    function execute_route(){
        
        if ($('#object_page').val() !== '') {
            collection = $('#slug_collection').val();
            routerGo(collection+'/'+$('#object_page').val())
        }else if($('#goToLogin').val()!==''){
            showLoginScreen($('#src').val());
        }
    }
    
    /**
     * funcao que redireciona para a pagina
     * @param {type} page
     * @returns {undefined}
     */
    function routerGo(page){
         saveRoute();
         console.log($('#route_blog').val()+page);
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
