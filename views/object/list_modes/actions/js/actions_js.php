<script type="text/javascript">
    $(function() {
        $('.ac-duplicate-item').on('click', function() {
            // $('#modalImportMain').modal('show');
            var path = $("#src").val() + '/controllers/object/object_controller.php';
            var item_id = $(this).parents().find('.open_item_actions').first().attr('id').replace('action-', '');
            var duplicate_op = $(this).attr('data-op');
            var op = 'duplicate_item_' + duplicate_op + '_collection';
            var send_data = { object_id: item_id, operation: op };

            if("other" == duplicate_op) {
                send_data.collection_id = $("#collection_id").val();
                show_duplicate_item(item_id);
                var current_item = $.trim($("#object_" + item_id + " .item-display-title").text());
                var dup_text = '<?php _t("Duplicate ", 1); ?>' + current_item + '<?php _t(" at another collection",1)?>';
                cl(dup_text);
                $("#modal_duplicate_object" + item_id + " .modal-title").text( dup_text );
                $("#modal_duplicate_object" + item_id + " input[type=radio]").get(1).click();
                $("#modal_duplicate_object" + item_id + " input[type=radio]").hide();
                $("#modal_duplicate_object" + item_id + " label").hide();
                $("#modal_duplicate_object" + item_id + " label.other_collection").show().text('<?php _t("Search collection",1); ?>');
            } else if("same" == duplicate_op) {
                $.ajax({
                    type: 'POST', url: path,
                    data: send_data
                }).done(function(r){
                    $('#main_part').hide();
                    $('#configuration').html(r).show();
                    $('#modalImportMain').modal('hide');
                });
            }
        });
    });
</script>