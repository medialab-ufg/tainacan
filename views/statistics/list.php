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

    <!-- Barra lateral esquerda :ui-widget-header-->
    <div class="col-md-3">
        <!-- Periodo -->
        <div id="config-filt" class="statistics-config form-group">
            <label class="title-pipe">
                <span class="prepend-filter-label glyphicon-chevron-down blue glyphicon sec-color" style="color: #79a6ce !important;"></span>
                <?php _e('Period'); ?>
            </label>
             <div class="date-range-filter period-config">
                <div> 
                    <h6> <?php _e('From: '); ?></h6>
                    <input type="text" class="input_date input-size form-control" value="" placeholder="dd/mm/aaaa" id="from_period" name="from_period">
                </div>
                <div> 
                    <h6><?php _e('Until: '); ?></h6>
                    <input type="text" class="input_date input-size form-control" value="" placeholder="dd/mm/aaaa" id="to_period" name="to_period"> <br />
                </div>
                <!-- Dynatree filter-->
                <div>
                    <input type="radio" id="days" value="days" name="optradio"> <?php _e('Days'); ?>
                </div>
                <div>
                    <input type="radio" id="weeks" value="weeks" name="optradio"> <?php _e('Weeks'); ?>
                </div>
                <div>
                    <input type="radio" id="months" value="months" name="optradio" checked> <?php _e('Months'); ?>
                </div>
                <div>
                    <input type="radio" id="nofilter" name="optradio" value="nofilter" disabled> <?php _e('No filter'); ?>
                </div>

                <!--<div id="report-filters"></div>-->
            </div>

            
        </div>

        <div id="config-repo" class="statistics-config form-group">
            <label for="object_tags" class="title-pipe">
                <span class="prepend-filter-label glyphicon-chevron-down blue glyphicon sec-color" style="color: #79a6ce !important;"></span>
                <?php _e('Report type'); ?>
            </label>
            <!-- Dynatree report type -->
            <div id="report-type-stat"></div>
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
                     <!--Period 
                    <div class="col-md-5" style="width: auto">
                      <span class="config-title"><?php _e('Filters: '); ?></span>
                      <label class="label-from"><?php _e('from: ')?></label><input type="text" class="inputPeriod" value="" placeholder="<?php _e('from: dd/mm/aaaa')?>" id="from_period" name="from_period">
                      <label class="label-until"><?php _e('until: ')?></label><input type="text" class="inputPeriod"  value="" placeholder="<?php _e('until: dd/mm/aaaa')?>" id="to_period" name="to_period">
                    </div>-->
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

            <div id="defaultchart_div" style="width: 100%; height: 350px;"></div> <!--Div that will hold the pie chart-->
            <div class="hide" id="piechart_div" style="width: 100%; height: 350px;"></div>
            <div class="hide" id="barchart_div" style="width: 100%; height: 350px;"></div>
            <div class="hide" id="curvelinechart_div" style="width: 100%; height: 350px;"></div>

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
                  <tr class="headers"> <th class="curr-parent"> <?php _e('Status'); ?> </th> </tr>
                  <tr class="content"> <td class="curr-filter"> <?php _e('Total:'); ?> </td> </tr>
                </tbody>
            </table>
        </div>

        <div class="col-md-12 no-padding" style="background: #e3e3c7; margin-top: 10px; text-align: center">
            <?php include_once "inc/pdf.php"; ?>
        </div>
    </div>

    <div class="temp-set" style="display: none"></div>

</div>