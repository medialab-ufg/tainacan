<script type="text/javascript">
    google.charts.load('current', {'packages':['bar','corechart'], 'language':'pt_BR'});

    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

        /*
         var data = new google.visualization.DataTable();
         data.addColumn('string', 'Topping');
         data.addColumn('number', 'Pedacinho!');
         data.addRows([
         ['Login', 80],
         ['Registros', 84],
         ['Banidos', 50],
         ['Excluídos', 60]
         ]);

         var options = {'title':'Status do usuário',
         'width': 500,
         'height':300,
         is3D: true,
         colors: ['#0c698b']
         };
         */

        /*
        var data = google.visualization.arrayToDataTable([
            ['Year', 'Sales', 'Expenses', 'Profit'],
            ['2014', 1000, 400, 200],
            ['2015', 1170, 460, 250],
            ['2016', 660, 1120, 300],
            ['2017', 1030, 540, 350]
        ]);
         var options = {
         chart: {
         title: 'Company Performance',
         subtitle: 'Sales, Expenses, and Profit: 2014-2017',
         }
         };
        */
        /*
        var data = new google.visualization.DataTable();
        data.addColumn('timeofday', 'Time of ddddddd');
        data.addColumn('number', '< ?php _e("Repository Statistics", "tainacan"); ?>');
        data.addColumn('number', 'Energy Level');
        data.addRows([
            [{v: [8, 0, 0], f: '8 am'}, 1, .25],
            [{v: [9, 0, 0], f: '9 am'}, 2, .5],
            [{v: [10, 0, 0], f:'10 am'}, 3, 1],
            [{v: [11, 0, 0], f: '11 am'}, 4, 2.25],
            [{v: [12, 0, 0], f: '12 pm'}, 5, 2.25],
            [{v: [13, 0, 0], f: '1 pm'}, 6, 3],
            [{v: [14, 0, 0], f: '2 pm'}, 7, 4],
            [{v: [15, 0, 0], f: '3 pm'}, 8, 5.25],
            [{v: [16, 0, 0], f: '4 pm'}, 9, 7.5],
            [{v: [17, 0, 0], f: '5 pm'}, 10, 10],
        ]);
        var options = {
            title: '< ? php _e("Repository Statistics", "tainacan"); ?>',
            width: 800,
            hAxis: {
                title: 'Time of Day',
                format: 'h:mm a',
                viewWindow: {
                    min: [7, 30, 0],
                    max: [17, 30, 0]
                }
            },
            vAxis: {
                title: 'Rating (scale of 1-10)'
            }
        };
        */

        var str_log = '<?php echo json_encode( Log::get_user_events()[0] ); ?>';
        var psd = $.parseJSON(str_log);
        // cl(psd.COUNT(id));

        var data = google.visualization.arrayToDataTable([
            ['Status de usuários', 'total users of ', { role: 'style' }],
            ['Login', 71.45, 'color: #0c698b' ], // CSS-style declaration
            ['Registros', 80.94, '#b87333'],            // RGB value
            ['Banidos', 10.49, 'silver'],            // English color name
            ['Excluídos', 29.30, 'gold']
        ]);
        var options = {
            // width: 900
        };

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