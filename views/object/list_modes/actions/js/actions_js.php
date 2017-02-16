<script type="text/javascript">
    $(function() {
        var path = $("#src").val() + '/controllers/object/object_controller.php';
        var _col_id = $("#collection_id").val();

        $('.ac-duplicate-item').on('click', function() {
            var item_id = $(this).parents().find('.open_item_actions').first().attr('id').replace('action-', '');
            var duplicate_op = $(this).attr('data-op');
            var op = 'duplicate_item_' + duplicate_op + '_collection';
            var send_data = { object_id: item_id, operation: op };

            if("other" == duplicate_op) {
                send_data.collection_id = _col_id;
                show_duplicate_item(item_id);
                var current_item = $.trim($("#object_" + item_id + " .item-display-title").text());
                var dup_text = '<?php _t("Duplicate ", 1); ?>' + current_item + '<?php _t(" at another collection",1)?>';
                $("#modal_duplicate_object" + item_id + " .modal-title").text( dup_text );
                $("#modal_duplicate_object" + item_id + " br").remove();
                $("#modal_duplicate_object" + item_id + " input[type=radio]").hide().get(1).click();
                $("#modal_duplicate_object" + item_id + " label").hide();
                $("#modal_duplicate_object" + item_id + " label.other_collection").show().text('<?php _t("Search collection",1); ?>');
            } else if("same" == duplicate_op) {
                $('#modalImportMain').modal('show');
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

        $('.ac-create-version').on('click', function() {
            var item_id = $(this).parents().find('.open_item_actions').first().attr('id').replace('action-', '');
            var current_item = $.trim($("#object_" + item_id + " .item-display-title").text());
            var modal_text = '<?php _t("Create new version of ", 1); ?>' + current_item;

            $('#modal_duplicate_object' + item_id).modal('show').find('br').remove();
            $("#modal_duplicate_object" + item_id + " .modal-title").text( modal_text );
            $("#modal_duplicate_object" + item_id + " input[type=radio]").hide().get(2).click();
            $("#modal_duplicate_object" + item_id + " label").hide();
            $("#modal_duplicate_object" + item_id + " label.version").show().text('<?php _t("Versioning",1); ?>');
        });

        $('a.ac-item-versions').on('click', function() {
            var item_id = $(this).parents().find('.open_item_actions').first().attr('id').replace('action-', '');
            $.ajax({
                type: 'POST', url: path,
                data: {operation: 'show_item_versions', object_id: item_id, collection_id: _col_id}
            }).done(function(r) {
                $('#main_part').hide();
                $('#tainacan-breadcrumbs').hide();
                $('#configuration').html(r).show();
            });
        });

        $('a.ac-comment-item').on('click', function() {
            var item_id = $(this).parents().find('.open_item_actions').first().attr('id').replace('action-', '');
            $.ajax({
                type: 'POST', url: path,
                data: {collection_id: $('#collection_id').val(), operation: 'list_comments', object_id: item_id}
            }).done(function(r){
                $("#comment_item"+item_id + ' .modal-body').html(r);
                $("#comment_item"+item_id).modal('show');
            });

        });

        $('a.ac-open-file').on('click', function() {
            var item_id = $(this).parents().find('.open_item_actions').first().attr('id').replace('action-', '');
            $.ajax({
                url: path, type: 'POST',
                data: { operation: 'press_item', object_id: item_id, collection_id: $('#collection_id').val() }
            }).done(function(r){
                var itm = $.parseJSON(r);

                if(itm) {
                    var pressPDF = new jsPDF('p','pt');
                    var baseX = 20;
                    var lMargin = baseX; // 15 left margin in mm
                    var rMargin = baseX; // 15 right margin in mm
                    var pdfInMM = 550;

                    pressPDF.setFont("helvetica");
                    pressPDF.setFontSize(10);

                    /* First items to display */
                    var header_cols = [
                        {title: 'Título', dataKey: 'title'},
                        {title: 'Autor', dataKey: 'author'},
                        {title: 'Criado em', dataKey: 'date'}
                    ];
                    var header_rows = [{title: itm.title, author: itm.author, date: itm.data_c}];
                    pressPDF.autoTable(header_cols, header_rows, { theme: 'plain', styles: {cellPadding: 0}, columnStyles: {}, margin: {top: baseX} } );

                    var paragraph = itm.desc;
                    var lines = pressPDF.splitTextToSize(paragraph, (pdfInMM-lMargin-rMargin));
                    pressPDF.text(lMargin*2, 80, lines);
                    
                    pressPDF.save('press.pdf');
                }

            });
        });

    });
</script>