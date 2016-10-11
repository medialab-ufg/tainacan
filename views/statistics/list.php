<?php
include_once(dirname(__FILE__).'/../../helpers/view_helper.php');

$view_helper = new ViewHelper();
?>
<script type="text/javascript">

    // Load the Visualization API and the corechart package.
    google.charts.load('current', {'packages':['corechart'], 'language':'pt_BR'});

    // Set a callback to run when the Google Visualization API is loaded.
    google.charts.setOnLoadCallback(drawChart);

    // Callback that creates and populates a data table,
    // instantiates the pie chart, passes in the data and
    // draws it.
    function drawChart() {

        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', 'Slices');
        data.addRows([
            ['Mushrooms', 3],
            ['Onions', 1],
            ['Olives', 1],
            ['Zucchini', 1],
            ['Pepperoni', 2]
        ]);

        // Set chart options
        var options = {'title':'How Much Pizza I Ate Last Night',
            'width': 500,
            'height':300,
            is3D: true
        };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
</script>

<div class="col-md-12 config-temp-box">

    <?php $view_helper->render_statistic_menu('config') ?>

    <div id="preset-filters" class="col-md-3 preset-filters ui-widget-header no-padding">
        <div class="btn-group">
        </div>
    </div>

    <div class="col-md-9 ui-widget-content metadata-actions" style="padding-right: 0;">

        <div class="add-property-btn btn-group col-md-12" style="background: white">
            <?php $view_helper->render_config_title(__('Repository Statistics', 'tainacan')); ?>
        </div>

        <div style="float: left; margin-top: 20px">
            <!--Div that will hold the pie chart-->
            <div id="chart_div"></div>
        </div>



    </div>

</div>
