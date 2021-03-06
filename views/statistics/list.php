<?php
include_once(dirname(__FILE__).'/../../helpers/view_helper.php');
include_once(dirname(__FILE__).'/../../helpers/log/log_helper.php');
include_once(dirname(__FILE__).'/../../models/log/log_model.php');
include_once('./../../helpers/object/object_helper.php');

$_log_helper = new LogHelper();

include_once('inc/i18n_strs.php'); 
include_once('js/list_js.php');
?>
<div class="container-fluid padd-r">
    <div class="row statistics-container">

        <?php $_log_helper->render_statistic_menu(); ?>

        <div id="dashb" class="hidden"></div>
        <!-- Barra lateral esquerda :ui-widget-header-->
        <div id="dynatree-estatisticas" class="col-md-3">
            <!-- Periodo -->
            <div id="config-filt" class="statistics-config form-group">
                <label class="title-pipe">
                    <span class="prepend-filter-label glyphicon-chevron-down blue glyphicon sec-color" style="color: #79a6ce !important;"></span>
                    <?php _e('Period'); ?>
                </label>
                <div class="date-range-filter period-config">
                    <div> 
                        <h6> <?php _e('From: ', 'tainacan'); ?></h6>
                        <input type="text" class="input-size form-control" value="" id="from_period" name="from_period">
                    </div>
                    <div> 
                        <h6><?php _e('Until: ', 'tainacan'); ?></h6>
                        <input type="text" class="input-size form-control" value="" id="to_period" name="to_period"> <br />
                    </div>
                    <!-- Dynatree filter-->
                    <div>
                        <input type="radio" id="days" value="days" name="optradio"> <?php _e('Days', 'tainacan'); ?>
                    </div>
                    <div>
                        <input type="radio" id="weeks" value="weeks" name="optradio"> <?php _e('Weeks', 'tainacan'); ?>
                    </div>
                    <div>
                        <input type="radio" id="months" value="months" name="optradio" checked> <?php _e('Months', 'tainacan'); ?>
                    </div>
                    <div>
                        <input type="radio" id="nofilter" name="optradio" value="nofilter"> <?php _e('No temporal scale', 'tainacan'); ?>
                    </div>
                </div>
            </div>

            <div id="config-repo" class="statistics-config form-group">
                <label for="object_tags" class="title-pipe">
                    <span class="prepend-filter-label glyphicon-chevron-down blue glyphicon sec-color" style="color: #79a6ce !important;"></span>
                    <?php _e('Report type', 'tainacan'); ?>
                </label>
                <!-- Dynatree report type -->
                <div id="report-type-stat"></div>
            </div>
        </div>

        <!-- Chart display -->
        <div id="charts-display" class="col-md-9">

            <div class="chart-header row">
                <?php $_log_helper->render_config_title(_t('Repository statistics')); ?>
            
                <!-- Cabeçalho dos gráficos -->
                <div class="user-config-control col-md-12">
                    
                    <!-- Início botão Download -->
                    <div class="pull-right">
                        <div class="col-md-12">
                            <button class="btn btn-default" data-toggle="dropdown" type="button" id="downloadStat">
                                <span class="config-title"> <?php _t('Download ', true); ?> </span> <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu downloadStat" aria-labelledby="downloadStat">
                                <?php $_log_helper->getDownloadTypes(); ?>
                            </ul>
                        </div>
                    </div>
                    <!-- Fim botão Download -->

                    <!-- Início botão tipo de gráfico -->
                    <div class="col-md-3 pull-right">
                        <span class="config-title"><?php _t('Mode: ',1); ?></span>
                        <button type="button" data-toggle="dropdown" class="btn btn-default" id="statChartType">
                            <img src="<?php echo $_log_helper->getChartsType()[0]['img']; ?>" alt="<?php echo $_log_helper->getChartsType()[0]['className']; ?>"> 
                        </button>
                        <ul class="dropdown-menu statChartType" aria-labelledby="statChartType">
                            <?php $_log_helper->renderChartsDropdown(); ?>
                        </ul>
                    </div>
                    <!-- Fim botão tipo de gráfico -->
                </div>
            </div>

            <!-- Chart container -->
            <div id="charts-container" class="">
                <div id="defaultchart_div" style="width: 100%; height: 500px;"></div>
                <div class="hide" id="piechart_div" style="width: 100%; height: 500px;"></div>
                <div class="hide" id="barchart_div" style="width: 100%; height: 500px;"></div>
                <div class="hide" id="curvelinechart_div" style="width: 100%; height: 500px;"></div>

                <!-- Mensagem 'não há dados' -->
                <div id="no_chart_data" class="hide text-center">
                    <h4> <?php _t('There is no data for this report or report filter!', 1); ?> </h4>
                </div>

                <!-- Mensagem 'Altere o tipo de gráfico ' -->
                <div id="change-chart-msg" class="hide text-center">
                    <h4> <?php _e('Change the chart type!', 'tainacan'); ?> </h4>
                </div>
                <!-- Tabela de exibição dos detalhes dos valores -->
                <div id="values-details" class="hide table-responsive">
                    <button onclick="javascript:closeDetail()" type="button" class="close" title="Close"> <span aria-hidden="true">&times;</span> </button>    
                    <table id="table-detail" class="table table-hover">
                        <thead id="thead-detail">
                            <tr class="headers">
                                <!-- Conteúdo dinâmico, vindo de list_js.php -->
                            </tr>
                        </thead>
                        <tbody id="tbody-detail">
                            <!-- Conteúdo dinâmico, vindo de list_js.php -->
                        </tbody>
                    </table>
                </div>

                <input type="hidden" value="default" class="selected_chart_type" />
                <input type="hidden" class="current_parent_report" value="" />
                <input type="hidden" class="get_collection_stats" value="<?php echo $collec_id; ?>" />
            </div>

            <!-- Chart resume -->
            <div id="charts-resume" class="table-responsive"> 
                <table class="table table-hover">
                    <thead>
                        <tr class="headers"> 
                            <th class="curr-parent"> <?php _e('Status', 'tainacan'); ?> </th> 
                        </tr>
                        <tr>
                            <th class="more-detail-message"> <?php _e('Click on values to see more details', 'tainacan') ?> <span class='glyphicon glyphicon-info-sign'></span> </th>
                        </tr>
                    </thead>
                    <tbody id="tbody-d">
                        <tr class="content"> 
                            <td class="curr-filter"></td> 
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-md-12 no-padding" style="background: #e3e3c7; margin-top: 10px; text-align: center; display: none;">
                <?php include_once "inc/pdf.php"; ?>
            </div>
        </div>

        <div class="temp-set" style="display: none"></div>

    </div>
</div>