<script type="text/javascript">
    google.charts.load('current', {'packages':['bar','corechart'], 'language':'pt_BR'});

    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var str_log = '<?php print_r( Log::getUserEvents()); ?>';
        var psd = $.parseJSON(str_log);
        var logins;
        $(psd).each(function (idx, val) {
            if(idx == 0) {
                logins = val.total_login;
            }
        });

        var total_logins = ['Login', logins, 'color: #0c698b' ]; // CSS-style declaration

        var data = google.visualization.arrayToDataTable([
            ['Status de usuários', 'total users of ', { role: 'style' }],
            total_logins,
            ['Registros', 80.94, '#b87333'],            // RGB value
            ['Banidos', 10.49, 'silver'],            // English color name
            ['Excluídos', 29.30, 'gold']
        ]);
        var options = {  }; // // width: 900

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