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
            show_modal_main();
            $.ajax({
                type: 'POST', url: path,
                data: {operation: 'show_item_versions', object_id: item_id, collection_id: _col_id}
            }).done(function(r) {
                hide_modal_main();
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
                // cl(itm);
                if(itm) {
                    var pressPDF = new jsPDF('p','pt');
                    var baseX = 20;
                    var lMargin = baseX; // 15 left margin in mm
                    var rMargin = baseX; // 15 right margin in mm
                    var pdfInMM = 560;
                    var line_dims = { startX: 28, startY: 75, length: 540, thickness: 1 };

                    pressPDF.setFont("helvetica");
                    pressPDF.setFontSize(9.5);

                    var logo = $('img.tainacan-logo-cor').get(0);
                    var projectLogo = new Image();
                    projectLogo.src = $(logo).attr("src");
                    var logo_settings = { width: (projectLogo.naturalWidth * 0.48), height: (projectLogo.naturalHeight * 0.48) };
                    pressPDF.addImage(projectLogo, 'PNG', line_dims.startX + 15, line_dims.startY - 45, logo_settings.width, logo_settings.height);
                    pressPDF.rect(line_dims.startX, line_dims.startY, line_dims.length, line_dims.thickness, 'F');
                    pressPDF.rect(line_dims.startX, line_dims.startY + 50, line_dims.length, line_dims.thickness, 'F');

                    pressPDF.setFontSize(8);
                    var formatted_date = "Consultado em " + getTodayFormatted();
                    pressPDF.text(formatted_date, 400, line_dims.startY - 5); // Consultado em

                    var create_txt = $(".item-creation strong").first().text();
                    var item_date = $("#object_" + item_id + " .item-creation").text().replace(create_txt, "");

                    var dist_from_top = line_dims.startY + 20;
                    pressPDF.setFontType('bold');
                    pressPDF.setFontSize(12);
                    pressPDF.text( itm.title, (line_dims.startX + 15), dist_from_top ); // Item title

                    pressPDF.setFontSize(9.5);
                    pressPDF.text( $(".item-author strong").first().text(), (line_dims.startX + 15), dist_from_top + 20); // Author
                    pressPDF.setFontType('normal');
                    pressPDF.text( itm.author, (line_dims.startX + 70), dist_from_top + 20);

                    var author_width = pressPDF.getTextDimensions(itm.author).w;
                    pressPDF.text(' em ' + item_date, (line_dims.startX + 70) + author_width, dist_from_top + 20);

                    var item_desc = itm.desc;
                    var desc_yDist = 140;
                    var desc_xDist = lMargin + baseX;
                    var desc_max_width = (pdfInMM-lMargin-rMargin);
                    if(itm.tmb) {
                        lMargin = 80;
                        pdfInMM = 490;
                        var thumb_ext = itm.tmb.type.ext;

                        if(thumb_ext == "jpg" || thumb_ext == "jpeg") {
                            thumb_ext = "JPEG";
                        } else {
                            thumb_ext = "PNG";
                        }
                        var item_thumb = new Image();
                        item_thumb.src = itm.tmb.url;
                        pressPDF.addImage(item_thumb, thumb_ext, baseX*2, desc_yDist, 80, 80);

                        desc_xDist = lMargin + (3*baseX);
                        desc_max_width = 410;
                    }

                    var descricao = pressPDF.splitTextToSize(item_desc, desc_max_width);
                    pressPDF.text(desc_xDist, desc_yDist+10, descricao);

                    var extra_yDist = 0;
                    if(item_desc) {
                      if(itm.breaks && itm.breaks > 0) {
                        extra_yDist = itm.breaks * 20;
                      }
                    }

                    var desc_height = Math.round(Math.round(pressPDF.getTextDimensions(descricao).h) * 1.5);
                    if(item_desc) {
                        var base_count = desc_yDist + desc_height + (baseX*2) + extra_yDist;
                    } else {
                        if(itm.tmb) {
                            var base_count = desc_yDist + 80;
                        } else {
                            var base_count = desc_yDist;
                        }
                    }

                    for( idx in itm.inf ) {
                        if(itm.inf[idx].value) {
                            if(itm.inf[idx].meta) {
                                pressPDF.setFontStyle('bold');
                                var p = base_count + 40;
                                pressPDF.text( itm.inf[idx].meta, baseX*2, p);
                                var f = p + 15;
                                pressPDF.setFontStyle('normal');
                                pressPDF.text( itm.inf[idx].value, baseX*2, f);

                                base_count = p;
                            }
                        }
                    }

                    pressPDF.save( itm.output + '.pdf');
                }

            });
        });

    });
</script>
