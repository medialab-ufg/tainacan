<script type="text/javascript">
    $('a.more-options-register').click(function(e) {
        e.preventDefault();

        if( $(this).hasClass('less-options') ) {
            $(this).text('<?php _e("More options", "tainacan"); ?>');
            $(this).removeClass('less-options');
        } else {
            $(this).addClass('less-options');
            $(this).text('<?php _e("Less options", "tainacan"); ?>');
        }

        $('.expanded-register').toggle();
    });
</script>