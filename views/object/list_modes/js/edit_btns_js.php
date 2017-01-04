<script>
    var visible_popover = false;
    $(function () {
        $('[data-toggle="popover"]').popover()
     });
             
    function triggerPopoverEdit(element){
        console.log(element);
        var options = {
            title:'',
            content: '<button class="btn btn-primary">Check in</button>',
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
    
    function dissmissPopoverEdi(){
        
    }

</script>