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

</script>
