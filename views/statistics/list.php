<?php
include_once(dirname(__FILE__).'/../../helpers/view_helper.php');
include_once(dirname(__FILE__).'/../../models/log/log_model.php');
include_once('js/list_js.php');
 
$view_helper = new ViewHelper();
?>
<div class="col-md-12 statistics-container no-padding">

    <?php $view_helper->render_statistic_menu() ?>

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
            <?php $view_helper->render_config_title(__('Repository Statistics', 'tainacan')); ?>
            <div class="user-config-control col-md-12 no-padding">
                <div class="col-md-4 pull-left no-padding">
                    <span class="config-title"><?php i18n_str('Filters:',true); ?></span>
                    <span class="current-chart"><?php i18n_str('User Stats',true); ?></span>
                </div>
                <div class="col-md-4 pull-right no-padding">
                    <span class="config-title"><?php i18n_str('Mode:',true); ?></span>
                    <a href="javascript:void(0)" class="change-mode">
                        <img src="https://google-developers.appspot.com/chart/interactive/images/chart_pie.png" alt="">
                    </a>
                    <?php
                        $_line_img = '<img src="https://google-developers.appspot.com/chart/interactive/images/chart_line.png" alt="">';
                        $_column_img = '<img src="https://google-developers.appspot.com/chart/interactive/images/chart_column.png" alt="">';
                        $_pie_img = '<img src="https://google-developers.appspot.com/chart/interactive/images/chart_pie.png" alt="">';
                    ?>
                    <select class="selectpicker" name="select-chart-mode" id="">
                        ye
                        <option data-content="<?php echo $_line_img; ?>" value=""> do </option>
                        <option data-content="<?php echo $_column_img; ?>" value=""> doee </option>
                        <option data-content="<?php echo $_pie_img; ?>" value=""> dsasao </option>
                    </select>

                </div>
                <?php /*
                <span class="config-title"><?php i18n_str('Orientation:',true); ?></span>
                <button class="btn btn-default"> <?php i18n_str('Download',true); ?> <span class="caret"></span></button>
                */ ?>
            </div>
        </div>

        <div id="charts-container" class="col-md-12">
            <div id="chart_div"></div> <!--Div that will hold the pie chart-->
            <div id="piechart_div" style="display: none"></div>
            <div id="barchart_div"></div>
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