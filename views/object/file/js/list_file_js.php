<script>
function showSlideShow( item_index ) {
    $("#modalSlideShow").modal('show');

    $('#carousel-attachment').slick({
        dots: true,
        infinite: true,
        speed: 500,
        fade: true,
        cssEase: 'linear',
        adaptiveHeight: true,
        initialSlide: item_index
    });
}
</script>
