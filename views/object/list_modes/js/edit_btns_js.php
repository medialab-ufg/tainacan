<script>
    var visible_popover = false;
    $(function () {
        $('[data-toggle="popover"]').popover()
     });
             
    function triggerPopoverEdit(element,has_checked,id){
        if(has_checked==='false'){
            var options = {
                title:'',
                content: '<button onclick="do_checkout('+id+')" class="btn btn-primary">Check out</button>',
                html:true,
                placement:'left',
                trigger:'focus'
            };
            if(!visible_popover){
                $(element).popover(options);
                $(element).popover('show');
                visible_popover = true;
            }else{
                $(element).popover('hide');
                visible_popover = false;
            }
        }
    }
    
    function do_checkout(id){
        $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {operation: 'check-out', collection_id: $('#collection_id').val(), object_id: id}
        }).done(function (result) {
            // $('html, body').animate({
            //   scrollTop: parseInt($("#wpadminbar").offset().top)
            // }, 900);  
        });
    }
</script>