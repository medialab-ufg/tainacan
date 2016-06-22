<script>
    /*
    $(function () {
        //total de items
        $.ajax({
            url: $("#src").val() + '/controllers/home/home_controller.php',
            type: 'POST',
            data: {operation: 'total_collections'}
        }).done(function (result) {
            var elem = JSON.parse(result);
            //$("#loader_collections").hide('slow');
            $("#total_collections").val(elem.size);
        });
        //autoscroll
        $(window).scroll(function () {
            if ($(window).scrollTop() == $(document).height() - $(window).height()
                    && parseInt($("#max_collection_showed").val()) <= parseInt($("#total_collections").val())) {
                $("#loader_collections").show();
                // $("#display_view_main_page").hide();
                $("#max_collection_showed").val(parseInt($("#max_collection_showed").val()) + 6);
                $.ajax({
                    url: $("#src").val() + '/controllers/home/home_controller.php',
                    type: 'POST',
                    data: {operation: 'display_populars', max_collection_showed: $("#max_collection_showed").val()}
                }).done(function (result) {
                    $("#loader_collections").hide('slow');
                    $("#append_popular").append(result);
                    //$('html, body').animate({
                    // scrollTop: parseInt($(".blocos").offset().top)
                    //}, 1000);
                });
                 $.ajax({
                    url: $("#src").val() + '/controllers/home/home_controller.php',
                    type: 'POST',
                    data: {operation: 'display_recents', max_collection_showed: $("#max_collection_showed").val()}
                }).done(function (result) {
                    $("#loader_collections").hide('slow');
                    $("#append_recents").append(result);
                    //$('html, body').animate({
                    // scrollTop: parseInt($(".blocos").offset().top)
                    //}, 1000);
                });
            }
        });

    });
    */
</script>
