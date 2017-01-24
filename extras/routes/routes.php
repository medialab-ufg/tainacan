<script>
    var previousRoute;
    
    
    $.router.add( $('#route_blog').val()+':collection', function(data) {
    });
    //pagina do item
    $.router.add( $('#route_blog').val()+':collection/:item', function(data) {
        showSingleObjectByName(data.item, $('#src').val())
    });
    
    
    function routerGo(page){
         saveRoute();
         $.router.go($('#route_blog').val()+page, 'My cool item');
    }
    
    function saveRoute(){
        previousRoute = window.location.pathname;
    }
    
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
