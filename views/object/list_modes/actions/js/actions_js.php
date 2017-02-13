<script type="text/javascript">
    $(function() {
        $('.ac-duplicateTC-item').on('click', function() {
            $('#modalImportMain').modal('show');
            var item_id = $(this).parents().find('.open_item_actions').first().attr('id').replace('action-', '');
            var path = $("#src").val() + '/controllers/object/object_controller.php';
            $.ajax({
                type: 'POST', url: path,
                data: { object_id: item_id, operation: 'duplicate_item_same_collection' }
            }).done(function(r){
                $('#main_part').hide();                
                $('#configuration').html(r).show();
                $('#modalImportMain').modal('hide');
            });
        });
    });
</script>