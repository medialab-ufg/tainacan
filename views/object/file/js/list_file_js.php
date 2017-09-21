<script>
function showSlideShow( item_index ) {
    $("#modalSlideShow").modal('show');

    var slider_opts = {
        dots: true,
        infinite: true,
        fade: true,
        adaptiveHeight: true,
        arrows: true,
        speed: 500,
        cssEase: 'linear',
        initialSlide: item_index
    };
    
    setTimeout(function () {
        $('#carousel-attachment').slick(slider_opts);
    }, 500);
}
</script>
