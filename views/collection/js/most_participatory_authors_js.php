<script>
    $(function(){
        $('#search-users-autocomplete').keyup(function(){
            $.ajax({
                type: "POST",
                url: $('#src').val() + "/controllers/user/user_controller.php",
                data: {
                    operation: 'search-colaborators', 
                    search: $(this).val(),
                    collection_id: $('#collection_id').val()}
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                if(elem){
                    $('#contact-list').html('');
                    $.each(elem,function(index,value){
                         $('#contact-list').append('<li class="list-group-item" style="padding: 2px;line-height: 1.2em;text-indent: 0px;" >'+
                         '<div class="col-xs-12 col-sm-3">'+value.avatar+'</div>'+
                         '<div class="col-xs-12 col-sm-9">'+
                         '<span style="font-size: 10pt;"><b>'+value.display_name+'</b></span><br>'+
                         '<span style="font-size: 8pt;"><?php _e('Last visited','tainacan') ?>: <?= date('d/m/y') ?></span><br>'+
                         '<span style="font-size: 10pt;"><b>'+value.num_posts+' <?php _e('Colaborations','tainacan') ?></b></span>'+
                         '</div>'+
                         '<div class="clearfix"></div>'+
                         '</li>');
                    });
                }
                
            });
        });
    });
</script>