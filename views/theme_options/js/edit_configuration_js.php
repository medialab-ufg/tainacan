<script>
    $(function () {
        $('body').addClass('repository-config-page');
        var src = $('#src').val();
        change_breadcrumbs_title('<?php _e('Repository Configuration','tainacan') ?>');
        showCKEditor();
        autocomplete_collection_templates();
        init_dynatree_collection_template();
        $('#submit_form_edit_repository_configuration').submit(function (e) {
            $("#repository_content").val(CKEDITOR.instances.editor.getData());
            e.preventDefault();
            $.ajax({
                url: src + '/controllers/theme_options/theme_options_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                var elem = jQuery.parseJSON(result);
                if( elem.type && elem.type === "success" ) {
                    showAlertGeneral(elem.title, elem.msg, elem.type);
                    var redir_url = $("#site_url").val();
                    window.location = redir_url;
                }
            });
        });
        var cropOpts = {
            uploadUrl: src + '/views/collection/upload_file.php',
            cropUrl: src + '/views/collection/crop_file.php',
            imgEyecandy: true,
            imgEyecandyOpacity: 0.1,
            modal: true,
            loaderHtml: '<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div>'
        };

        cropOpts.onAfterImgCrop = function() {
            var repo_config = this.id;
            var croppd_imgs = $("img.croppedImg").length;
            var img = $("img.croppedImg").get( (croppd_imgs - 1) ) ;
            var img_url = $(img).attr("src");
            var data = { operation: 'set_repository_img', collection_id: $("#collection_id").val(),
                img_url: img_url, img_title: getCroppedFileName(img_url), type: repo_config };
            var path = src + '/controllers/collection/collection_controller.php';
            $.ajax({url: path, type: 'POST', data: data});
        };

        var logo  = new Croppic("logo_crop", cropOpts);
        var cover = new Croppic("cover_crop", cropOpts);

        function getCroppedFileName(st) {
            if(st && (typeof st === "string")) {
                var fileName = st.split("/").reverse()[0];
                var fileExt = fileName.substr(fileName.lastIndexOf('.'));
                fileName = fileName.replace(fileExt,"");

                return fileName;
            } else {
                return st;
            }
        }
    });

    function autocomplete_collection_templates() {
        var src = $('#src').val();
        $("#collection_template").autocomplete({
            source: src + '/controllers/collection/collection_controller.php?operation=get_collections_json',
            messages: {
                noResults: '',
                results: function () {
                }
            },
            minLength: 2,
            select: function (event, ui) {
                event.preventDefault();
                $("#collection_template").val(ui.item.label);
                swal({
                    title: '<?php _e('Attention!','tainacan') ?>',
                    text: '<?php _e('Add the collection','tainacan') ?>'+' '+ui.item.label+' '+'<?php _e('as a template','tainacan') ?>',
                    type: "info",
                    showCancelButton: true,
                    confirmButtonClass: 'btn-info',
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function (isConfirm) {
                    if (isConfirm) {
                       $('#modalImportMain').modal('show');//mostro o modal de carregamento
                        $.ajax({
                            type: "POST",
                            url: $('#src').val() + "/controllers/collection/collection_controller.php",
                            data: {
                                operation: 'add_collection_template',
                                collection_id: ui.item.value
                               }
                        }).done(function (result) {
                            $('#modalImportMain').modal('hide');//escondo o modal de carregamento
                            elem_first = jQuery.parseJSON(result);
                            if(elem_first.d){
                                get_collections_template(src);
                                list_templates();
                            }
                        });
                    }
                });
            }
        });
    }
    
    function init_dynatree_collection_template(){
        $("#dynatree-collection-templates").dynatree({
            selectionVisible: true, // Make sure, selected nodes are visible (expanded).
            checkbox: true,
            initAjax: {
                url: $('#src').val() + '/controllers/collection/collection_controller.php',
                data: {
                    collection_id: $("#collection_id").val(),
                    operation: 'initDynatreeCollectionTemplates'
                },
                addActiveKey: true
            },
            autoFocus: false, // Evita que o Dynatree faça Scroll para si mesmo quando iniciar
            onSelect: function (flag, node) {
                if(node.bSelected&&node.childList){
                    $.each(node.childList,function(index,node){
                        node.select(true);
                    });
                } else if(node.childList){
                    $.each(node.childList,function(index,node){
                        node.select(false);
                    });
                }
                if(node.data.key!=='false'){
                    toggleHabilitateTemplate(node.data.key,node.data.type);
                }
            }
        });
    }
    
    function toggleHabilitateTemplate(key,type) {
        $.ajax({
            type: "POST",
            url: $('#src').val() + "/controllers/collection/collection_controller.php",
            data: {
                operation: 'habilitate-collection-templates',
                key:key,
                type:type
               }
        }).done(function (result) {
            list_templates();
        });
    }

    $(document).on("click", "#removeThumbnail", function(){

        swal({
                title: "<?php _e('Are you sure?', 'tainacan')?>",
                text: "<?php _e('Do you really want to remove thumbnail?', 'tainacan')?>",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                showCancelButton: true,
                confirmButtonText: '<?php _e("Yes", "tainacan"); ?>',
                cancelButtonText: '<?php _e("No", "tainacan"); ?>'
            },
            function (willDelete) {
                if (willDelete) {
                    let button = $(this);
                    $(button).attr("disabled", "disabled");
                    $("#remove").hide();
                    $("#removing").show();

                    $("#modalImportMain").modal('show');

                    $.ajax({
                        url: $(src).val() + '/controllers/theme_options/theme_options_controller.php',
                        type: 'POST',
                        data: {operation: 'removeThumbnail', post_id: $("#collection_id").val()}
                    }).done(function(){
                        $("#thumbImg").hide();
                        $("#removeThumbnail").hide();
                        $("#imageEditor").show();
                        $("#showEditThumbnail").hide();
                        $("#modalImportMain").modal('hide');

                        swal(
                            '<?php _e('Removed', 'tainacan'); ?>',
                            "<?php _e('Thumbnail was removed', 'tainacan'); ?>",
                            'success'
                        );
                    });


                }
            }
        );
    });

    $(document).on("click", "#removeCover", function(){

        swal({
                title: "<?php _e('Are you sure?', 'tainacan')?>",
                text: "<?php _e('Do you really want to remove cover?', 'tainacan')?>",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                showCancelButton: true,
                confirmButtonText: '<?php _e("Yes", "tainacan"); ?>',
                cancelButtonText: '<?php _e("No", "tainacan"); ?>'
            },
            function (willDelete) {
                if (willDelete) {
                    let button = $(this);
                    $(button).attr("disabled", "disabled");
                    $("#removeCover").hide();
                    $("#removingCover").show();
                    $("#removeTextCover").text($(button).attr('data-loading-text')+"...");
                    $("#modalImportMain").modal('show');

                    $.ajax({
                        url: $(src).val() + '/controllers/theme_options/theme_options_controller.php',
                        type: 'POST',
                        data: {operation: 'removeCover', post_id: $("#collection_id").val()}
                    }).done(function(){
                        $("#coverImg").hide();
                        $("#removeCover").hide();
                        $("#showEditCover").hide();
                        show_edit_cover();
                        $("#modalImportMain").modal('hide');
                        swal(
                            '<?php _e('Removed', 'tainacan'); ?>',
                            "<?php _e('Cover was removed', 'tainacan'); ?>",
                            'success'
                        );
                    });


                }
            }
        );
    });

    function show_edit_cover() {
        $("#edit_cover_container").show();
    }

    function show_edit_thumbnail() {
        $("#imageEditor").show();
    }
</script>