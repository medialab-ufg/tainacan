<?php
include_once(dirname(__FILE__).'/../../helpers/view_helper.php');
include_once(dirname(__FILE__).'/../../helpers/log/log_helper.php');
include_once(dirname(__FILE__).'/../../models/log/log_model.php');
$_log_helper = new LogHelper();

include_once('inc/i18n_strs.php'); 
include_once('js/list_js.php');
?>
<div class="col-md-12 statistics-container">

    <?php $_log_helper->render_statistic_menu(); ?>

    <!-- Barra lateral esquerda -->
    <div id="statistics-config" class="col-md-3 ui-widget-header">

        <!-- Periodo -->
<!--        <div class="form-group period-config">-->
<!--            <label class="title-pipe">-->
<!--                <span class="prepend-filter-label glyphicon-chevron-down blue glyphicon sec-color" style="color: #79a6ce !important;"></span>-->
<!--                --><?php //_t('Period',true); ?>
<!--            </label>-->
<!--            <div class="date-range-filter">-->
<!--                <p> <span> --><?php //_t('From',1) ?><!-- </span>-->
<!--                    <input size="7" type="text" class="input_date form-control" value="" placeholder="dd/mm/aaaa" id="from_period" name="from_period">-->
<!--                </p>-->
<!--                <p> <span> --><?php //_t('until',1) ?><!-- </span>-->
<!--                    <input type="text" class="input_date form-control" size="7" value="" placeholder="dd/mm/aaaa" id="to_period" name="to_period"> <br />-->
<!--                </p>-->
<!--            </div>-->
<!--        </div>-->

        <div class="form-group">
            <label for="object_tags" class="title-pipe">
                <span class="prepend-filter-label glyphicon-chevron-down blue glyphicon sec-color" style="color: #79a6ce !important;"></span>
                <?php _t('Report type',true); ?>
            </label>
            <div id="report_type_stat"></div>
        </div>
    </div>

    <!-- Chart display -->
    <div id="charts-display" class="col-md-9">

        <div class="chart-header btn-group col-md-12">
            <!-- Download -->
            <div class="col-md-1 pull-right no-padding" style="width: auto;">
                <button class="btn btn-default" data-toggle="dropdown" type="button" id="downloadStat">
                    <?php _t('Download: ',true); ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu downloadStat" aria-labelledby="downloadStat">
                    <?php $_log_helper->getDownloadTypes(); ?>
                </ul>
            </div>

            <?php $_log_helper->render_config_title(_t('Repository statistics')); ?>
            
            <div class="user-config-control col-md-12 no-padding">
                <!-- Filters -->
                <div class="col-md-10 pull-left no-padding">
                    <span class="config-title"><?php _t('Filters:',1); ?></span>

                    <!-- Period -->
                    <style>
                        .inputPeriod{
                            font-size: 14x;
                            background-color: #fff;
                            border: 1px solid #ccc;
                            border-radius: 4px;
                            box-shadow: inset 0 1px 1px rgba(0,0,0, .075);
                            transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
                        }
                    </style>
                    <input size="14" type="text" class="input_date inputPeriod" value="" placeholder="from: dd/mm/aaaa" id="from_period" name="from_period">
                    <input type="text" class="input_date inputPeriod" size="14" value="" placeholder="until: dd/mm/aaaa" id="to_period" name="to_period">

                    <!--  <span class="current-chart">--><?php //_t('User Stats',1); ?><!--</span> -->
                </div>
            </div>
        </div>

        <!-- Chart container -->
        <div id="charts-container" class="col-md-12" style="text-align: center">
            <!-- Chart type -->
            <style>
                .chartChanger{
                    z-index: 1;
                    width: auto;
                }
            </style>
            <div class="col-md-2 pull-right no-padding chartChanger">
<!--                <span class="config-title">--><?php //_t('Mode:',1); ?><!--</span>-->
                <button data-toggle="dropdown" class="btn btn-default" id="statChartType" type="button">
                    <img src="<?php echo $_log_helper->getChartsType()[0]['img']; ?>" alt="<?php echo $_log_helper->getChartsType()[0]['className']; ?>">
                </button>

                <ul class="dropdown-menu statChartType" aria-labelledby="statChartType">
                    <?php $_log_helper->renderChartsDropdown(); ?>
                </ul>
            </div>
            <div id="defaultchart_div"></div> <!--Div that will hold the pie chart-->
            <div class="hide" id="piechart_div" style="width: 650px; height: 300px;"></div>
            <div class="hide" id="barchart_div"></div>
            <div class="hide" id="curvelinechart_div"></div>

            <div id="no_chart_data" class="hide">
                <h3> <?php _t('There is no data yet for this report!', 1); ?> </h3>
            </div>

            <input type="hidden" value="default" class="selected_chart_type" />
            <input type="hidden" class="current_parent_report" value="" />
            <input type="hidden" class="get_collection_stats" value="<?php echo $collec_id; ?>" />
        </div>

        <!-- Chart resume -->
        <div id="charts-resume" class="col-md-12">
            <table>
                <tbody>
                <tr class="headers"> <th class="curr-parent"> <?php _t('Status',1); ?> </th> </tr>
                <tr class="content"> <td class="curr-filter"> <?php _t('Users:',1); ?> </td> </tr>
                </tbody>
            </table>
        </div>

        <div class="col-md-12 no-padding" style="background: #e3e3c7; margin-top: 10px; text-align: center">
            <?php include_once "inc/pdf.php"; ?>
        </div>
    </div>

    <div class="temp-set" style="display: none"></div>

</div>