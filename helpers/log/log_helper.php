<?php

class LogHelper extends ViewHelper {
    
    public function getChartsType() {
        return [ [ 'className' => 'defaultchart_div', 'img' => $this->getChartImg("chart_column") ],
            [ 'className' => 'piechart_div', 'img' => $this->getChartImg("chart_pie") ],
            [ 'className' => 'barchart_div', 'img' => $this->getChartImg("bar_chart") ],
            [ 'className' => 'curvelinechart_div', 'img' => $this->getChartImg("chart_line")] ];
    }
    
    public function getReportTypes() {
        return [ 'users' => _t('Users'),
            'items' => _t('Items'),
            'collections' => _t('Collections'),
            'comments' => _t('Comments'),
            'categories' => _t('Categories'),
            'tags' => _t('Tags'),
            'imports' => _t('Import / Export'),
            'admin' => _t('Administration') ];
    }
    
    public function getDownloadTypes() {
        $_downloads = [ 'pdf' => _t('PDF'), 'csv' => _t('CSV'), 'xls' => _t('XLS') ];
        foreach ($_downloads as $_mime => $_type) {
            echo '<li><a href="javascript:void(0)" class="dl-'.$_mime.'">'. $_type .'</a><li>';
        }
    }
    
    public function renderChartsDropdown() {
        foreach( $this->getChartsType() as $chart) {
            echo '<li class="'. $chart['className'] . '">';
            echo '<a href="javascript:void(0)" class="change-mode" data-chart="'. $chart['className'] .'">';
            echo '<img src="'. $chart['img'] . '" /> </a></li>';
        }
    }

    private function getChartImg($fileName) {
        return get_stylesheet_directory_uri() . '/libraries/images/chart/' . $fileName . '.png';
    }

    public function renderPDFHeader() {
        $logo_id = get_option('socialdb_logo');
        ?>
      <div class="col-md-12 pdf-footer">
          <div class="topo row">
              <div class="col-md-6">
                  <?php echo $this->renderRepositoryLogo($logo_id, 'Tainacan'); ?>
              </div>
              <div class="col-md-6 pull-right"></div>
          </div>
          <div class="dados row">
              <div class="col-md-6">
                  <strong> <?php _e("Research: ", "tainacan"); ?> </strong>
              </div>
              <div class="col-md-6 pull-right">
                  <strong> <?php _e("Consulted period: ", "tainacan"); ?> </strong>
              </div>
          </div>
      </div>
        <?php
    }

    public function renderPDFFooter() {

        $user_data = get_userdata(get_current_user_id());
        if ( is_object( $user_data->data ) ) {
            echo "<strong>" . _t("Name:"). "</strong> " . $user_data->data->user_nicename . "<br />";
            echo "<strong>" . _t("User:"). "</strong> " . $user_data->data->display_name . "<br />";
            echo "<strong>" . _t("email:"). "</strong> " . $user_data->data->user_email . "<br />";
        }
    }
}