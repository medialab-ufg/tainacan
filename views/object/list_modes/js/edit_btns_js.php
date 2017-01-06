<script>
    var visible_popover = false;
    $(function () {
        $('[data-toggle="popover"]').popover()
     });
             
    function triggerPopoverEdit(element,has_checked,id){
        if(has_checked==='false'){
            var options = {
                title:'',
                content: '<button onclick="do_checkout('+id+')" class="btn btn-primary">Checkout</button>',
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
        }else{
            $(element).css('opacity','0.5');
            var options = {
                title:'',
                content: '<button onclick="discard_checkout('+id+')" class="btn btn-primary">Discard Checkout</button>&nbsp;&nbsp;'+
                         '&nbsp;&nbsp;<button onclick="do_checkin('+id+')" class="btn btn-primary">Checkin</button>',
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
            showAlertGeneral('<?php _e('Success!','tainacan') ?>','<?php _e('Checkout enabled!') ?>','success');
            wpquery_filter();
        });
    }
    
    function discard_checkout(id){
        $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {operation: 'check-out', collection_id: $('#collection_id').val(), object_id: id,value:''}
        }).done(function (result) {
            showAlertGeneral('<?php _e('Success!','tainacan') ?>','<?php _e('Checkout disabled!') ?>','success');
            wpquery_filter();
        });
    }
    
    function do_checkin(id){
        show_modal_main();
        $.ajax({
            url: $('#src').val() + '/controllers/object/object_controller.php',
            type: 'POST',
            data: {operation: 'check-in', collection_id: $('#collection_id').val(), object_id: id,motive:'check-in'}
        }).done(function (result) {
             wpquery_filter();
             hide_modal_main();
            showAlertGeneral('<?php _e('Success!','tainacan') ?>','<?php _e('Checkin done!') ?>','success');
            $("#form").html('');
            $('#main_part').hide();
            $('#display_view_main_page').hide();
            $('#loader_collections').hide();
            $('#configuration').html(result).show();
            $('.dropdown-toggle').dropdown();
            $('.nav-tabs').tab();
        });
    }
</script>