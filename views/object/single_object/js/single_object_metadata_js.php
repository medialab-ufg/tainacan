<script>
    $(function () {
        $("#main_part_collection").hide();
        var is_item_home = $("#configuration .item-breadcrumbs").siblings().first().is("#single_object_id");
        if( is_item_home ) {
            $("#configuration").css('margin-top', 50);
        }

        change_breadcrumbs_title('<?php _e('Import', 'tainacan') ?>');

        $('img').bind('contextmenu', function (e) {
            return false;
        });

        var is_col_header_visible = $(".collection_header").is(":visible");
        if (!is_col_header_visible) {
            //$('.header-navbar').css("margin-bottom", 0);
            $('body').css('background-color', '#f2f2f2');
        }

        //botao voltar do browser
        if (window.history && window.history.pushState) {
            previousRoute = window.location.pathname;
            window.history.pushState('forward', null, $('#route_blog').val()+$('#slug_collection').val()+'/'+$('#single_name').val());
            //
        }
        var stateObj = {foo: "bar"};
        $('#form').html('');
//        $('#object_page').val($('#single_name').val());
//        history.replaceState(stateObj, "page 2", $('#socialdb_permalink_object').val());

//        var myPopoverObject = $('#iframebuttonObject').data('popover');
//        $('#iframebuttonObject').popover('hide');
//        myPopoverObject.options.html = true;
//        //<iframe width="560" height="315" src="https://www.youtube.com/embed/CGyEd0aKWZE" frameborder="0" allowfullscreen></iframe>
//        myPopoverObject.options.content = $('#socialdb_permalink_object').val();
        // form thumbnail
        $('#formThumbnail').submit(function (e) {
            e.preventDefault();
            $('#single_modal_thumbnail').modal('hide');
            $('#modalImportMain').modal('show');//mostro o modal de carregamento

            $.ajax({
                url: $('#src').val() + "/controllers/object/objectsingle_controller.php",
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).success(function (result) {
                elem = jQuery.parseJSON(result);
                if (elem.attachment_id) {
                    insert_fixed_metadata($('#single_object_id').val(), 'thumbnail', elem.attachment_id);
                } else {
                    $('#modalImportMain').modal('hide');//mostro o modal de carregamento
                }
            });
        });
        //carrego as licensas ativas
        $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {operation: 'show_collection_licenses', object_id: $('#single_object_id').val(), collection_id: $("#collection_id").val()}
        }).done(function (result) {
            $('#event_license').html(result);
        });
    });


    /*
     * Increments item's collection view count
     * @author Rodrigo Guimarães
     * */
    function increment_collection_view_count(collection_id) {
        $.ajax({
            url: $('#src').val() + "/controllers/object/objectsingle_controller.php",
            data: {collection_id: collection_id, operation: 'increment_collection_count'}
        });
    }

</script>
