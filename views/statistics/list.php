<?php
include_once(dirname(__FILE__).'/../../helpers/view_helper.php');
include_once(dirname(__FILE__).'/../../helpers/log/log_helper.php');
include_once(dirname(__FILE__).'/../../models/log/log_model.php');
include_once('js/list_js.php');
 
$_log_helper = new LogHelper();
?>
<div class="col-md-12 statistics-container no-padding">

    <?php $_log_helper->render_statistic_menu() ?>

    <div id="statistics-config" class="col-md-3 ui-widget-header no-padding">

        <div class="form-group period-config">
            <label class="title-pipe"> <?php i18n_str('Period',true); ?> </label>
            <div class="date-range-filter">
                <p>
                    <span> <?php _e('From','tainacan') ?> </span>
                    <input size="7" type="text" class="input_date form-control" value="" placeholder="dd/mm/aaaa" id="from_period" name="from_period">
                </p>
                <p>
                    <span> <?php _e('until','tainacan') ?> </span>
                    <input type="text" class="input_date form-control" size="7" value="" placeholder="dd/mm/aaaa" id="to_period" name="to_period"> <br />
                </p>
            </div>
        </div>
        <div class="form-group">
            <label for="object_tags" class="title-pipe"> <?php i18n_str('Report type',true); ?> </label>
            <div id="report_type_stat"></div>
        </div>
    </div>



    <div id="charts-display" class="col-md-9">
        <div class="chart-header btn-group col-md-12">
            <?php $_log_helper->render_config_title(__('Repository Statistics', 'tainacan')); ?>
            <div class="user-config-control col-md-12 no-padding">
                <div class="col-md-4 pull-left no-padding">
                    <span class="config-title"><?php i18n_str('Filters:',true); ?></span>
                    <span class="current-chart"><?php i18n_str('User Stats',true); ?></span>
                </div>

                <div class="col-md-2 pull-right no-padding">
                    <span class="config-title"><?php i18n_str('Mode:',true); ?></span>

                    <button data-toggle="dropdown" class="btn btn-default" id="statChartType" type="button">
                        <img src="<?php echo $_log_helper->getChartsType()[0]['img']; ?>" alt="<?php echo $_log_helper->getChartsType()[0]['className']; ?>">
                    </button>

                    <ul class="dropdown-menu" aria-labelledby="statChartType" class="statChartType">
                        <?php foreach ($_log_helper->getChartsType() as $chart): ?>
                            <li>
                                <a href="javascript:void(0)" class="change-mode" data-chart="<?php echo $chart['className'] ?>">
                                    <img src="<?php echo $chart['img'] ?>" />
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                </div>

                <?php /*
                <span class="config-title"><?php i18n_str('Orientation:',true); ?></span>
                <button class="btn btn-default"> <?php i18n_str('Download',true); ?> <span class="caret"></span></button>
                */ ?>
            </div>
        </div>

        <div id="charts-container" class="col-md-12">
            <div id="chart_div"></div> <!--Div that will hold the pie chart-->
            <div id="piechart_div" class="hide"></div>
            <div id="barchart_div" class="hide"></div>
        </div>
        
        <div id="charts-resume" class="col-md-12">
            <table>
                <tbody>
                <tr class="headers"> <th class="curr-parent"> <?php i18n_str('Status:',true); ?> </th> </tr>
                <tr class="content"> <td class="curr-filter"> <?php i18n_str('Users:',true); ?> </td> </tr>
                </tbody>
            </table>

        </div>
    </div>

    <div class="temp-set" style="display: none"></div>

</div>