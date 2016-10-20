<script type="text/javascript">
    google.charts.load('current', {'packages':['bar','corechart'], 'language':'pt_BR'});

    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var login_qry = '<?php print_r( Log::getUserEvents('login')); ?>';
        var register_qry = '<?php print_r( Log::getUserEvents('register')); ?>';
        var delete_qry = '<?php print_r( Log::getUserEvents('delete_user')); ?>';
        var parsd_login = $.parseJSON(login_qry);
        cl(parsd_login);

        var parsd_reg = $.parseJSON(register_qry);
        cl(parsd_reg);

        var parsd_del = $.parseJSON(delete_qry);
        cl(parsd_del);

        var logins, registers, deletes;
        $(parsd_login).each(function (idx, val) {
            if(idx == 0) {
                logins = val.total_login;
            }
        });
        $(parsd_reg).each(function (idx, val) {
            if(idx == 0) {
                registers = val.total_login;
            }
        });
        $(parsd_del).each(function (idx, val) {
            if(idx == 0) {
                deletes = val.total_login;
            }
        });

        var total_logins = ['Login', logins, 'color: #0c698b' ]; // CSS-style declaration
        var total_registers = ['Registros', registers, 'color: #b87333' ]; //RGB value
        var total_del = ['Excluídos', deletes, 'silver' ]; // English color name

        var data = google.visualization.arrayToDataTable([
            ['Status de usuários', 'qtd ', { role: 'style' }],
            total_del,
            total_logins,
            total_registers,
            ['Banidos', 3, 'silver'],            // English color name
        ]);
        var options = { colors: ['#0c698b'] }; // // width: 900

        // var chart = new google.visualization.BarChart (document.getElementById('chart_div'));
        var chart = new google.charts.Bar(document.getElementById('chart_div'));
        chart.draw(data, options);
    }

    $("#statistics-config").accordion({
        collapsible: true,
        active: 1,
        header: "label",
        animate: 200,
        heightStyle: "content",
        icons: true
    });
</script>