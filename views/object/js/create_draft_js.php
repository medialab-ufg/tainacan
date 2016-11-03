<script>
    /**
     * funcao que salva o rascunho do item
     * @returns {undefined}
     */
    function createDraft() {
        $('#submit_form .auto-save').change(function () {
            var verify =  $('#submit_form').serialize();
            $("#object_content").val(CKEDITOR.instances.object_editor.getData()); 
            var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function(node) {
                        return node.data.key;
            });
            $('#object_classifications').val(selKeys.join(", ")); 
            $.ajax( {
              url: $('#src').val()+'/controllers/object/object_draft_controller.php',
              type: 'POST',
              data:  verify
            } ).done(function( result ) {
                elem_first =jQuery.parseJSON(result);    
                var string = '<?php _e('Automatically saved in','tainacan') ?> '+elem_first.date
                 +' of ' + elem_first.hour;
                $('#draft-text').text(string); 
            }); 
        });
        
        $('#submit_form_edit_object .auto-save').change(function () {
            var verify =  $('#submit_form_edit_object').serialize();
            $("#object_content_edit").val(CKEDITOR.instances.object_editor.getData()); 
            var selKeys = $.map($("#dynatree").dynatree("getSelectedNodes"), function(node) {
                        return node.data.key;
            });
            $('#object_classifications').val(selKeys.join(", ")); 
            $.ajax( {
              url: $('#src').val()+'/controllers/object/object_draft_controller.php',
              type: 'POST',
              data:  verify
            } ).done(function( result ) {
                elem_first =jQuery.parseJSON(result);    
                var string = '<?php _e('Automatically saved in','tainacan') ?> '+elem_first.date
                 +' of ' + elem_first.hour;
                $('#draft-text').text(string); 
            }); 
        });
    }
    setTimeout(function() { createDraft(); }, 8000);
    
    
    function back_main_list_discard(id) {
        $('#form').hide();
        $("#tainacan-breadcrumbs").hide();
        $('#configuration').hide();
        $('#main_part').show();
        $('#display_view_main_page').show();
        $("#container_three_columns").removeClass('white-background');
        $('#menu_object').show();
        $.ajax( {
            url: $('#src').val()+'/controllers/object/object_controller.php',
            type: 'POST',
            data: {operation: 'delete_temporary_object',ID:id}
        } ).done(function( result ) {
            // $('html, body').animate({
             //   scrollTop: parseInt($("#wpadminbar").offset().top)
           // }, 900);  
        });
    }
</script>
