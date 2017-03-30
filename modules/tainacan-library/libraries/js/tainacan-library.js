function showModalImportMarc() {
    $("#modalImportMarc").modal("show");
}

function createMarcItem()
{
    var text = $("#textmarc").val();
    if(text.length > 0)
    {
        send_ajax(text);
    }else
    {
        var file = document.getElementById("inputmarc");

        var fr = new FileReader();
        fr.readAsText(file.files[0]);
        fr.onload = function(e)
        {
            send_ajax(fr.result);
        }
    }
}

function send_ajax($marc)
{
    var url_to_send = $('#src').val() + '/controllers/collection/collection_controller.php?operation=import_marc';
    $.ajax({
        url: url_to_send,
        type: 'POST',
        data: {marc: $marc, collection_id: $("#collection_id").val()},
        beforeSend: function () {
            $("#modalImportMarc").modal("hide");
            $("#modalImportLoading").modal("show");
            $('#progressbarmapas').remove();
        },
        success: function (elem) {
            elem = JSON.parse(elem);
            if(elem.result)
            {
                window.location = elem.url;
            }
        }
    });
}

function save_mapping_marc(){
    $("#mapping_marc").submit(function (event) {
        event.preventDefault();

        $('#modalImportLoading').modal('show');
        $('#progressbarmapas').remove();
        var formData = new FormData(this);
        $.ajax({
            url: $('#src').val() + '/controllers/collection/collection_controller.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (r) {
                /*for(var pair of formData.entries()) {
                    console.log(pair[0]+ ', '+ pair[1]);
                }*/
                var elem = JSON.parse(r);
                if(elem.result)
                {
                    window.location = elem.url;
                }
            }
        });
    });
}
