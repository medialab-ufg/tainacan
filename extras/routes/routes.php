<?php
$collection_route = get_post(get_option('collection_root_id'));
?>

<input type="hidden" id="route_blog" name="route_blog" value="<?php echo str_replace($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'], '', get_bloginfo('url')) ?>/"> <!-- utilizado na busca -->
<input type="hidden" id="goToLogin" name="goToLogin" value="<?php
    if (get_query_var('log-in')) {
        echo trim(get_query_var('log-in'));
    }
    ?>">
<!-- Paginas da colecao -->
<input type="hidden" id="goToAddItem" name="goToAddItem" value="<?php
    if (get_query_var('add-item') && get_query_var('collection')) {
         echo trim(get_query_var('add-item'));
    }
    ?>">
<input type="hidden" id="goToEditObject" name="goToEditObject" value="<?php
    if (get_query_var('edit-item') && get_query_var('collection') && get_query_var('item')) {
        echo get_post_by_name(trim(get_query_var('item')),OBJECT,'socialdb_object')->ID;
    }
    ?>">
<input type="hidden" id="goToAdvancedSearch" name="goToAdvancedSearch" value="<?php
    if ((get_query_var('advancedSearch') && get_query_var('collection') == $collection_route->post_name ) && get_query_var('advancedSearch')) {
        echo trim(get_query_var('advancedSearch'));
    }
    ?>">
<input type="hidden" id="goToCollectionMetadata" name="goToCollectionMetadata" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') !== $collection_route->post_name ) && get_query_var('metadata')) {
        echo trim(get_query_var('metadata'));
    }
    ?>">
<input type="hidden" id="goToCollectionConfiguration" name="goToCollectionConfiguration" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') !==$collection_route->post_name ) && get_query_var('configuration')) {
        echo trim(get_query_var('configuration'));
    }
    ?>">
<input type="hidden" id="goToCollectionLayout" name="goToCollectionLayout" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') !== $collection_route->post_name ) && get_query_var('layout')) {
        echo trim(get_query_var('layout'));
    }
    ?>">
<input type="hidden" id="goToCollectionEvents" name="goToCollectionEvents" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') !== $collection_route->post_name ) && get_query_var('events')) {
        echo trim(get_query_var('events'));
    }
    ?>">
<input type="hidden" id="goToCollectionTags" name="goToCollectionTags" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') !== $collection_route->post_name ) && get_query_var('tags')) {
        echo trim(get_query_var('tags'));
    }
    ?>">
<input type="hidden" id="goToCollectionSocial" name="goToCollectionSocial" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') !== $collection_route->post_name ) && get_query_var('social')) {
        echo trim(get_query_var('social'));
    }
    ?>">
<input type="hidden" id="goToCollectionImport" name="goToCollectionImport" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') !== $collection_route->post_name ) && get_query_var('import')) {
        echo trim(get_query_var('import'));
    }
    ?>">
<input type="hidden" id="goToCollectionLicenses" name="goToCollectionLicenses" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') !== $collection_route->post_name ) && get_query_var('licenses')) {
        echo trim(get_query_var('licenses'));
    }
    ?>">
<input type="hidden" id="goToCollectionExport" name="goToCollectionExport" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') !==$collection_route->post_name ) && get_query_var('export')) {
        echo trim(get_query_var('export'));
    }
    ?>">
<input type="hidden" id="goToCollectionStatistics" name="goToCollectionStatistics" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') !== $collection_route->post_name ) && get_query_var('statistics')) {
        echo trim(get_query_var('statistics'));
    }
    ?>">
<!------------------------ Paginas do repositorio ------------------------------>
<input type="hidden" id="goToRepositoryMetadata" name="goToRepositoryMetadata" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') === $collection_route->post_name ) && get_query_var('metadata')) {
        echo trim(get_query_var('metadata'));
    }
    ?>">
<input type="hidden" id="goToRepositoryConfiguration" name="goToRepositoryConfiguration" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') === $collection_route->post_name ) && get_query_var('configuration')) {
        echo trim(get_query_var('configuration'));
    }
    ?>">
<input type="hidden" id="goToRepositoryCategories" name="goToRepositoryCategories" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') === $collection_route->post_name ) && get_query_var('categories')) {
        echo trim(get_query_var('categories'));
    }
    ?>">
<input type="hidden" id="goToRepositoryEvents" name="goToRepositoryEvents" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') === $collection_route->post_name ) && get_query_var('events')) {
        echo trim(get_query_var('events'));
    }
    ?>">
<input type="hidden" id="goToRepositoryTool" name="goToRepositoryTool" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') === $collection_route->post_name ) && get_query_var('tools')) {
        echo trim(get_query_var('tools'));
    }
    ?>">
<input type="hidden" id="goToRepositorySocial" name="goToRepositorySocial" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') === $collection_route->post_name ) && get_query_var('social')) {
        echo trim(get_query_var('social'));
    }
    ?>">
<input type="hidden" id="goToRepositoryImport" name="goToRepositoryImport" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') === $collection_route->post_name ) && get_query_var('import')) {
        echo trim(get_query_var('import'));
    }
    ?>">
<input type="hidden" id="goToRepositoryLicenses" name="goToRepositoryLicenses" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') === $collection_route->post_name ) && get_query_var('licenses')) {
        echo trim(get_query_var('licenses'));
    }
    ?>">
<input type="hidden" id="goToRepositoryExport" name="goToRepositoryExport" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') === $collection_route->post_name ) && get_query_var('export')) {
        echo trim(get_query_var('export'));
    }
    ?>">
<input type="hidden" id="goToRepositoryEmail" name="goToRepositoryEmail" value="<?php
    if ((get_query_var('collection') && get_query_var('collection') === $collection_route->post_name ) && get_query_var('email')) {
        echo trim(get_query_var('email'));
    }
    ?>">
<script type="text/javascript">
    var previousRoute;
    /********** cadastro de rotas ********/
     //pagina central da colecao
    $.router.add( $('#route_blog').val()+':collection', function(data) {
        console.log('rota only collection');
        if(data.collection == '<?php echo __('advanced-search','tainacan') ?>'){
            showAdvancedSearch($('#src').val());
        }else{
            backToMainPage(null, true);
        }
    });
    //pagina do item
    $.router.add( $('#route_blog').val()+':collection/:item', function(data) {
        console.log('rota item',data);
        if(data.collection == 'admin'){
            <?php if (current_user_can('manage_options')): ?>
                if(data.item=='<?php echo __('metadata','tainacan') ?>'){
                    showPropertiesRepository($('#src').val());
                }else if(data.item=='<?php echo __('configuration','tainacan') ?>'){
                    showCollectionConfiguration($('#src').val());
                }else if(data.item=='<?php echo __('email','tainacan') ?>'){
                    showWelcomeEmail($('#src').val());
                }else if(data.item=='<?php echo __('categories','tainacan') ?>'){
                    showCategoriesConfiguration($('#src').val());
                }else if(data.item=='<?php echo __('social','tainacan') ?>'){
                     showAPIConfiguration($('#src').val());
                }else if(data.item=='<?php echo __('licenses','tainacan') ?>'){
                    showLicensesRepository($('#src').val());
                }else if(data.item=='<?php echo __('import','tainacan') ?>'){
                    showImportFull($('#src').val());
                }else if(data.item=='<?php echo __('export','tainacan') ?>'){
                    showExportFull($('#src').val());
                }else if(data.item=='<?php echo __('tools','tainacan') ?>'){
                    showTools($('#src').val());
                }else if(data.item=='<?php echo __('events','tainacan') ?>'){
                    showEventsRepository($('#src').val());
                }
            <?php endif; ?>
        }else{
            //console.log(previousRoute ,window.location.pathname);
            if(previousRoute === window.location.pathname){
                $.router.go($('#route_blog').val()+$('#slug_collection').val());
            }else{
                showSingleObjectByName(data.item, $('#src').val())
            }

        }

    });

     //pagina do item
    $.router.add( $('#route_blog').val()+':collection/:item/:operation', function(data) {
        <?php if ((verify_collection_moderators(get_the_ID(), get_current_user_id()) || current_user_can('manage_options')) && get_post_type(get_the_ID()) == 'socialdb_collection'): ?>
        if(data.item == 'admin'){
                if(data.operation=='<?php echo __('metadata','tainacan') ?>'){
                    showPropertiesAndFilters($('#src').val());
                }else if(data.operation=='<?php echo __('configuration','tainacan') ?>'){
                    showRepositoryConfiguration($('#src').val());
                }else if(data.operation=='<?php echo __('layout','tainacan') ?>'){
                    showLayout($('#src').val());
                }else if(data.operation=='<?php echo __('tags','tainacan') ?>'){
                    showCollectionTags($('#src').val());
                }else if(data.operation=='<?php echo __('social','tainacan') ?>'){
                    showSocialConfiguration($('#src').val());
                }else if(data.operation=='<?php echo __('licenses','tainacan') ?>'){
                    showLicensesConfiguration($('#src').val());
                }else if(data.operation=='<?php echo __('import','tainacan') ?>'){
                    showImport($('#src').val());
                }else if(data.operation=='<?php echo __('export','tainacan') ?>'){
                    showExport($('#src').val());
                }else if(data.operation=='<?php echo __('statistics','tainacan') ?>'){
                    showStatistics($('#src').val());
                }else if(data.operation=='<?php echo __('events','tainacan') ?>'){
                    showEvents($('#src').val());
                }
        }
        <?php endif; ?>
    });

    $(function(){
         execute_route();
    });
    /**************************************************************************/

    /**
     * verifica se existe alguma rota a ser executada
     * @returns {undefined}
     */
    function execute_route() {
         $.router.reset();
         //console.log('edit item',$('#goToAddItem').val());
        if ($('#object_page').val() !== '') {
            collection = $('#slug_collection').val();
            if(collection) {
                showSingleObjectByName($('#object_page').val() , $('#src').val())
            }
        } else if($('#goToEditObject').val()!==''){
               route_edit_object_item($('#goToEditObject').val())
        }else if($('#goToAddItem').val()!==''){
            $('#configuration').html(
            '<div style="margin-left:1%;padding-left:15px;min-height:500px;padding-top:80px;" class="col-md-12 menu_left_loader">'+
                '<center>'+
                       '<img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">'+
                       '<h4><?php _e('Loading metadata...', 'tainacan') ?></h4>'+
                '</center>'+
             '</div>').show();
            $("#tainacan-breadcrumbs").show();
            $("#tainacan-breadcrumbs .current-config").show().text('> <?php  echo __('New item','tainacan') ?>');
            console.log($("#tainacan-breadcrumbs .current-config"));
            $('#main_part').hide();
            $('#collection_post').show();
            $('#display_view_main_page').hide();
            $('#loader_collections').hide();
                createItemPage($('#src').val());
        }else if($('#goToLogin').val()!==''){
            showLoginScreen($('#src').val());
        }else if($('#goToAdvancedSearch').val()!==''){
             showAdvancedSearch($('#src').val());
        }
        // REPOSITORIO ROTAS
        <?php if (current_user_can('manage_options')): ?>
            else if($('#goToRepositoryMetadata').val()!==''){
                showPropertiesRepository($('#src').val());
            }else if($('#goToRepositoryConfiguration').val()!==''){
                showRepositoryConfiguration($('#src').val());
            }else if($('#goToRepositoryCategories').val()!==''){
                console.log('categories redirect');
                showCategoriesConfiguration($('#src').val());
            }else if($('#goToRepositoryEvents').val()!==''){
                showEventsRepository($('#src').val());
            }else if($('#goToRepositoryTool').val()!==''){
                showTools($('#src').val());
            }else if($('#goToRepositorySocial').val()!==''){
                showAPIConfiguration($('#src').val());
            }else if($('#goToRepositoryImport').val()!==''){
                showImportFull($('#src').val());
            }else if($('#goToRepositoryLicenses').val()!==''){
                showLicensesRepository($('#src').val());
            }else if($('#goToRepositoryExport').val()!==''){
                showExportFull($('#src').val());
            }else if($('#goToRepositoryEmail').val()!==''){
                showWelcomeEmail($('#src').val());
            }
        <?php endif; ?>
        //COLECAO ROTAS
        <?php if ((verify_collection_moderators(get_the_ID(), get_current_user_id()) || current_user_can('manage_options')) && get_post_type(get_the_ID()) == 'socialdb_collection'): ?>
        else if($('#goToCollectionMetadata').val()!==''){
            console.log('redirect');
            showPropertiesAndFilters($('#src').val());
        }else if($('#goToCollectionConfiguration').val()!==''){
            showCollectionConfiguration($('#src').val());
        }else if($('#goToCollectionLayout').val()!==''){
            showLayout($('#src').val());
        }else if($('#goToCollectionEvents').val()!==''){
            showEvents($('#src').val());
        }else if($('#goToCollectionTags').val()!==''){
            showCollectionTags($('#src').val());
        }else if($('#goToCollectionSocial').val()!==''){
            showSocialConfiguration($('#src').val());
        }else if($('#goToCollectionLicenses').val()!==''){
            showLicensesConfiguration($('#src').val());
        }else if($('#goToCollectionImport').val()!==''){
            showImport($('#src').val());
        }else if($('#goToCollectionExport').val()!==''){
            showExport($('#src').val());
        }else if($('#goToCollectionStatistics').val()!==''){
            showStatistics($('#src').val());
        }
        <?php endif; ?>
    }


    function updateStatePage(state){
        //url amigavel
        if (window.history && window.history.pushState) {

            $(window).on('popstate', function () {
                var hashLocation = location.hash;
                var hashSplit = hashLocation.split("#!/");
                var hashName = hashSplit[1];

                if (hashName !== '') {
                    var hash = window.location.hash;
                    if (hash === '') {
                       // backRoute();
                    }
                }
            });
            window.history.pushState('forward', null, $('#route_blog').val()+'/'+state);
            //
        }
    }
    /**
     * atualiza a url do admin da colecao
     * @param {type} state
     * @returns {undefined}     */
    function updateStateCollection(state){
        if(state=='configuration'){
            state = '<?php _e('configuration','tainacan') ?>';
        }else if(state=='layout'){
            state = '<?php _e('layout','tainacan') ?>';
        }else if(state=='tags'){
            state = '<?php _e('tags','tainacan') ?>';
        }else if(state=='events'){
            state = '<?php _e('events','tainacan') ?>';
        }else if(state=='social'){
            state = '<?php _e('social','tainacan') ?>';
        }else if(state=='licenses'){
            state = '<?php _e('licenses','tainacan') ?>';
        }else if(state=='import'){
            state = '<?php _e('import','tainacan') ?>';
        }else if(state=='export'){
            state = '<?php _e('export','tainacan') ?>';
        }else if(state=='statistics'){
            state = '<?php _e('statistics','tainacan') ?>';
        }else if(state=='metadata'){
            state = '<?php _e('metadata','tainacan') ?>';
        }


        //url amigavel
        if (window.history && window.history.pushState) {

            $(window).on('popstate', function () {
                var hashLocation = location.hash;
                var hashSplit = hashLocation.split("#!/");
                var hashName = hashSplit[1];

                if (hashName !== '') {
                    var hash = window.location.hash;
                    if (hash === '') {
                       // backRoute();
                    }
                }
            });
            window.history.pushState('forward', null, $('#route_blog').val()+$('#slug_collection').val()+'/admin/'+state);
            //
        }
    }

    /**
     * atualiza a url do admin do repositorio
     * @param {type} state
     * @returns {undefined}     */
    function updateStateRepositorio(state){
        if(state=='configuration'){
            state = '<?php _e('configuration','tainacan') ?>';
        }else if(state=='tools'){
            state = '<?php _e('tools','tainacan') ?>';
        }else if(state=='categories'){
            state = '<?php _e('categories','tainacan') ?>';
        }else if(state=='events'){
            state = '<?php _e('events','tainacan') ?>';
        }else if(state=='social'){
            state = '<?php _e('social','tainacan') ?>';
        }else if(state=='licenses'){
            state = '<?php _e('licenses','tainacan') ?>';
        }else if(state=='import'){
            state = '<?php _e('import','tainacan') ?>';
        }else if(state=='export'){
            state = '<?php _e('export','tainacan') ?>';
        }else if(state=='statistics'){
            state = '<?php _e('statistics','tainacan') ?>';
        }else if(state=='metadata'){
            state = '<?php _e('metadata','tainacan') ?>';
        }


        //url amigavel
        if (window.history && window.history.pushState) {

            $(window).on('popstate', function () {
                var hashLocation = location.hash;
                var hashSplit = hashLocation.split("#!/");
                var hashName = hashSplit[1];

                if (hashName !== '') {
                    var hash = window.location.hash;
                    if (hash === '') {
                       // backRoute();
                    }
                    if($('#route_blog').val()===window.location.pathname){
                        window.location = $('#route_blog').val();
                    }
                }
            });
            window.history.pushState('forward', null, $('#route_blog').val()+'admin/'+state);
            //
        }
    }
    /**
     * funcao que redireciona para a pagina
     * @param {type} page
     * @returns {undefined}
     */
    function routerGo(page){
         saveRoute();
         if(page){
             $.router.go($('#route_blog').val()+page, 'My cool item');
         }else{
             window.location = $('#route_blog').val();
         }

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
        if(collection) {
            restoreHeader();
            previousRoute = $('#route_blog').val()+collection+'/';
           $.router.go($('#route_blog').val()+collection+'/', 'My cool item');
        }else
           window.location = $('#route_blog').val();
    }

    function route_edit_object_item(object_id) {
        $('#configuration').html(
        '<div style="margin-left:1%;padding-left:15px;min-height:500px;padding-top:80px;" class="col-md-12 menu_left_loader">'+
            '<center>'+
                   '<img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">'+
                   '<h4><?php _e('Loading metadata...', 'tainacan') ?></h4>'+
            '</center>'+
         '</div>').show();
        $("#tainacan-breadcrumbs").show();
        $("#tainacan-breadcrumbs .current-config").text('> <?php  echo __('Edit item','tainacan') ?>');
        $('#main_part').hide();
        $('#collection_post').show();
        $('#collection_post .headers_container').hide();
        $('#display_view_main_page').hide();
        $('#loader_collections').hide();
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'edit-item', item_id: object_id}
        }).done(function (result) {
            hide_modal_main();
            if(result.trim().indexOf('checkout@')>=0){
                $('.modal').modal('hide');//mostro o modal de carregamento
                var arrayN = result.trim().split('@');
                showAlertGeneral('<?php _e('Attention!','tainacan') ?>','<?php _e('Item blocked by user ') ?>'+arrayN[1]+' <?php _e('at','tainacan') ?> '+arrayN[2],'info');
            }else{
                $("#form").html('');
                $('#main_part').hide();
                $('#display_view_main_page').hide();
                $('#loader_collections').hide();
                $('#configuration').html(result).show();
                $('.dropdown-toggle').dropdown();
                $('.nav-tabs').tab();
            }
        });
    }
</script>
