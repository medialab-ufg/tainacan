<?php ?>
<script>
    $(function () {
        if ($('#open_wizard').val() == 'true') {
            $('#btn_back_collection').hide();
            $('#submit_configuration').hide();
            $('#personalize_design').hide();
        }
        else {
            $('#MyWizard').hide();
            $('#design_create_opt').hide();
            $('#save_and_next').hide();
            $('#personalize_design').show();
        }
        //Chama a biblioteca colorpicker e aplica na background_color
        $('.colorpicker_socialdb').colorpicker();

        //Submissao do formulario de edicao de design
        var src = $('#src').val();
        $('#submit_form_edit_design_collection').submit(function (e) {
            e.preventDefault();
            $('#modalImportMain').modal('show');//mostra o modal de carregamento
            $.ajax({
                url: src + '/controllers/design/design_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                $('#modalImportMain').modal('hide');//esconde o modal de carregamento
                $('.dropdown-toggle').dropdown();
                elem = jQuery.parseJSON(result);
                if (elem.success === 'true') {
                    $("#alert_success_categories").toggle();
                } else {
                    $("#alert_error_categories").toggle();
                }
                showHeaderCollection($('#src').val());
            });
            e.preventDefault();
        });
    });

    function showPersonalizeDesign() {
        $("#show_design_link").hide('slow');
        $("#hide_design_link").show('slow');
        $(".categories_menu").show('slow');
    }

    function hidePersonalizeDesign() {
        $("#personalize_design").hide('slow');
        $("#hide_design_link").hide('slow');
        $("#show_design_link").show('slow');
    }

    function nextStep() {
        showAlertGeneral('<?php _e('Success!','tainacan'); ?>', '<?php _e('Your collection is configured.','tainacan'); ?>', 'success');
        setTimeout(function () {
            $('#open_wizard').val('false');
            window.location.replace('<?php get_the_permalink($collection_post->ID); ?>' + '?');
        }, 2000);
    }
</script>
