<script>
    $(function () {
        var src = $('#src').val();

        $('#submit_help_cc').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: $("#src").val() + '/controllers/object/object_controller.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false
            }).done(function (result) {
                $("#modalHelpCC").modal('hide');
                elem = jQuery.parseJSON(result);
                if(elem.id && elem.id != ''){
                    $('#radio' + elem.id).attr("checked", "checked");
                }
                showAlertGeneral(elem.title, elem.msg, elem.type);
            });
        });
        
        if($('.hide_license')&&$('.hide_license').val()==='true'){
            $('#list_licenses_items').hide();
            $('#core_validation_license').val('true');
        }
    });
  
</script>
