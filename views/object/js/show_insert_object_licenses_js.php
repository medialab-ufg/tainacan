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
            var property_license_id = $('#property_license_id').val();
            $('#meta-item-'+property_license_id).hide();
            $('.list_licenses_items').hide();
            if($('#list_licenses_items').length>0){
                $('#list_licenses_items').remove();
            }
            $('#core_validation_license').val('true');
        }else{
            $('#core_validation_license').val('true');
        }
        
        $('input:radio[name="object_license"]').change(function() {
            $('#core_validation_license').val('true');
            validate_all_fields();
            set_field_valid('license','core_validation_license')
        });
    });
  
</script>
