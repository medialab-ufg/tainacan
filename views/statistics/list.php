<?php
include_once(dirname(__FILE__).'/../../helpers/view_helper.php');
include_once(dirname(__FILE__).'/../../models/log/log_model.php');
include_once('js/list_js.php');
 
$view_helper = new ViewHelper();
?>
<div class="col-md-12 statistics-container">
    
    <?php $view_helper->render_statistic_menu('config') ?>

    <div id="statistics-config" class="col-md-3 ui-widget-header no-padding"
         style="background-color: white; border: 3px solid #E8E8E8; margin-left: -15px;">

        <div class="form-group period-config">
            <label for="object_tags" class="title-pipe"> <?php i18n_str('Period',true); ?> </label>
            <div class="date-range-filter">
                <p>
                    <span> <?php _e('From','tainacan') ?> </span>
                    <input size="7" type="text" class="input_date form-control" value="" placeholder="dd/mm/aaaa" id="facet_1" name="facet_1">    
                </p>
                <p>
                    <span> <?php _e('until','tainacan') ?> </span>
                    <input type="text" class="input_date form-control" size="7" value="" placeholder="dd/mm/aaaa" id="facet_2" name="facet_2"> <br />    
                </p>
            </div>
        </div>
        <div class="form-group">

        <style type="text/css">
            #report_type_stat .dynatree-icon {
                display: none;
            }
        </style>

            <label for="object_tags" class="title-pipe"> <?php i18n_str('Report type',true); ?> </label>

            <div id="report_type_stat"></div>

            <div>
                <ul style="padding-left: 0">
                    <span class="caret"></span> Usuários
                <li style="margin-left: 13px; margin-top: 5px;">
                    <input type="radio" value="Status"> Status
                    <br />
                    <small style="font-size: 10px; color: #929497; padding-left: 17px"> logins / registros / banidos / excluídos </small>
                </li>
                <li style="margin-left: 13px;">
                    <input type="radio" value="Status"> Itens
                    <br />
                    <small style="font-size: 10px; color: #929497; padding-left: 17px"> criaram / editaram / apagaram /
                        visualizaram / baixaram </small>
                </li>
                <li style="margin-left: 13px;">
                    <input type="radio" value="Status"> Perfil
                    <br />
                    <small style="font-size: 10px; color: #929497; padding-left: 17px"> Pessoas que aderiram a um perfil </small>
                </li>
                <li style="margin-left: 13px;">
                    <input type="radio" value="Status"> Mensagens
                    <br />
                    <small style="font-size: 10px; color: #929497; padding-left: 17px"> enviaram / receberam / excluíram </small>
                </li>
                <li style="margin-left: 13px;">
                    <input type="radio" value="Status"> Categorias
                    <br />
                    <small style="font-size: 10px; color: #929497; padding-left: 17px"> criaram / editaram / apagaram
                        / visualizaram / baixaram </small>
                </li>
                <li style="margin-left: 13px;">
                    <input type="radio" value="Status"> Coleção
                    <br />
                    <small style="font-size: 10px; color: #929497; padding-left: 17px"> criaram / editaram / apagaram
                        / visualizaram </small>
                </li>
                </ul>
                <span class="caret"></span> Itens <br />
                <span class="caret"></span> Coleção <br />
                <span class="caret"></span> Comentários <br />
                <span class="caret"></span> Categorias <br />
                <span class="caret"></span> Tags <br />
                <span class="caret"></span> Mensagens privadas <br />
                <span class="caret"></span> Importar / Exportar <br />
                <span class="caret"></span> Administração <br />
                <span class="caret"></span> Eventos <br />
            </div>
        </div>
    </div>

    <div class="col-md-9">

        <div class="chart-header btn-group col-md-12" style="background-color: white; border: 3px solid #E8E8E8">
            <?php $view_helper->render_config_title(__('Repository Statistics', 'tainacan')); ?>
            <div class="user-config-control col-md-12 no-padding">
                <div class="col-md-4 pull-left">
                    <span class="config-title"><?php i18n_str('Filters:',true); ?></span>
                    <span class="current-chart"><?php i18n_str('User Stats',true); ?></span>
                </div>

                <span class="config-title"><?php i18n_str('Orientation:',true); ?></span>
                <span class="config-title"><?php i18n_str('Mode:',true); ?></span>
                <button class="btn btn-default"> <?php i18n_str('Download',true); ?> <span class="caret"></span></button>
            </div>
        </div>

        <div id="charts-container" class="col-md-12">
            <div id="chart_div"></div> <!--Div that will hold the pie chart-->
        </div>

    </div>

</div>