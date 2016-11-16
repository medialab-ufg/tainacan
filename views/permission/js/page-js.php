<script>
    $(function(){
       $("#list-profiles").accordion({
            active: false,
            collapsible: true,
            header: "h2",
            heightStyle: "content"
        });
    });
    
    function back_main_list() {
        $('#form').hide();
        $("#tainacan-breadcrumbs").hide();
        $('#configuration').hide();
        $('#main_part').show();
        $('#display_view_main_page').show();
    }
</script>    
