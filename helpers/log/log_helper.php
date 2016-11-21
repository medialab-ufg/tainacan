<?php

class LogHelper extends ViewHelper {
    
    public function getChartsType() {
        return [ [ 'className' => 'chart_div', 'img' => $this->getChartImg("chart_column") ],
            [ 'className' => 'piechart_div', 'img' => $this->getChartImg("chart_pie") ],
            [ 'className' => 'barchart_div', 'img' => $this->getChartImg("chart_line") ] ];
    }

    private function getChartImg($fileName) {
        return get_stylesheet_directory_uri() . '/libraries/images/chart/' . $fileName . '.png';
    }
}