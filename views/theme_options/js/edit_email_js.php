<script>
    $(function () {
        var src = $('#src').val();
        showCKEditor();

        $('#submit_form_edit_welcome_email').submit(function (e) {
            $("#welcome_email_content").val(CKEDITOR.instances.editor.getData());
            e.preventDefault();
            $.ajax({
                url: $("#src").val() + '/controllers/theme_options/theme_options_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                elem = jQuery.parseJSON(result);
                showAlertGeneral(elem.title, elem.msg, elem.type);
                showWelcomeEmail(src);
            });
        });
    });
</script>
