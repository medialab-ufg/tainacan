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

function get_users_page(src, op) {
    var send_ctrl = 'user/user_controller.php';
    var send_url = src + '/controllers/' + send_ctrl;
    var send_data = { operation: op };

    $.ajax({ type: 'POST', url: send_url, data: send_data })
        .done(function(res){
            resetHomeStyleSettingsLibrary();
            $('#tainacan-breadcrumbs').hide();
            $('#users_div').html(res).show();
        })
}

function resetHomeStyleSettingsLibrary() {
    //cl('Entering _resetHomeStyleSettings');
    $('.ibram-home-container').hide();

    if( $('body').hasClass('page-template-page-statistics') ) {
        $("#tainacan-stats").hide();
        $("#configuration").css('margin-top', '30px');
    } else {
        var $_main = '#main_part';
        $("#configuration").css('margin-top', '0px');
        if( $($_main).hasClass('home') ) {
            $($_main).show().css('padding-bottom', '0%');
            $('#display_view_main_page').hide();
            $('body.home').css('background', 'white');
            $("#searchBoxIndex").hide();
            $('.repository-sharings').css('display', 'block');

        } else {
            $("#collection_post").css('margin-top', '0').hide();
            $("#main_part_collection").show();
            $('.collection_header').hide();
            $($_main).hide();
        }
    }

    var ibram_active = $('.ibram_menu_active').val();
    if( ibram_active && ibram_active == true.toString() ) {
        $('#collection_post').show();
    }
}