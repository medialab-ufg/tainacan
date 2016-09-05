<script>
    $(function () {
        //botao voltar do browser
        /*if (window.history && window.history.pushState) {

            $(window).on('popstate', function () {
                var hashLocation = location.hash;
                var hashSplit = hashLocation.split("#!/");
                var hashName = hashSplit[1];

                if (hashName !== '') {
                    var hash = window.location.hash;
                    if (hash === '') {
                        backToMainPageSingleItem();
                    }
                }
            });
            window.history.pushState('forward', null, './#forward');
        }*/
        var stateObj = {foo: "bar"};
        $('#form').html('');
        $('#object_page').val($('#single_name').val());
        history.replaceState(stateObj, "page 2", $('#socialdb_permalink_object').val());
    });
    
    function delete_version(version_id, title, text){
        swal({
            title: title,
            text: text,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                show_modal_main();
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/object/object_controller.php",
                    data: {
                        operation: 'delete_version',
                        version_id: version_id
                    }
                }).done(function (result) {
                    hide_modal_main();

                });
            }
        });
    }
    
    function restore_version(active_id, version_id, title, text){
        swal({
            title: title,
            text: text,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-danger',
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function (isConfirm) {
            if (isConfirm) {
                show_modal_main();
                $.ajax({
                    type: "POST",
                    url: $('#src').val() + "/controllers/object/object_controller.php",
                    data: {
                        operation: 'restore_version',
                        active_id: active_id,
                        version_id: version_id
                    }
                }).done(function (result) {
                    hide_modal_main();

                });
            }
        });
    }

</script>
