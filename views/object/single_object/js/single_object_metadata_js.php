<script>
    $(function () {
        $("#main_part_collection").hide();
        var is_item_home = $("#configuration .item-breadcrumbs").siblings().first().is("#single_object_id");
        if( is_item_home ) {
            $("#configuration").css('margin-top', 50);
        }

        $('img').bind('contextmenu', function (e) {
            return false;
        });

        var is_col_header_visible = $(".collection_header").is(":visible");
        if (!is_col_header_visible) {
            //$('.header-navbar').css("margin-bottom", 0);
            $('body').css('background-color', '#f2f2f2');
        }

        //botao voltar do browser
        /*
        if (window.history && window.history.pushState) {
            previousRoute = window.location.pathname;
            window.history.pushState('forward', null, $('#route_blog').val()+$('#slug_collection').val()+'/'+$('#single_name').val());
        }
        */
        $('#form').html('');
    });

    function list_files_single(id) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/object/object_controller.php",
            data: {collection_id: $('#collection_id').val(), operation: 'show_files', object_id: id}
        }).done(function (result) {
            $('#single_list_files_' + id).html(result);
        });
    }
</script>
