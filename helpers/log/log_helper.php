<?php

class LogHelper extends ViewHelper {
    
    public function getChartsType() {
        return [ [ 'className' => 'chart_div', 'img' => $this->getChartImg("chart_column") ],
            [ 'className' => 'piechart_div', 'img' => $this->getChartImg("chart_pie") ],
            [ 'className' => 'barchart_div', 'img' => $this->getChartImg("chart_line") ] ];
    }
    
    public function getReportTypes() {
        return [ 'users' => i18n_str('Users'),
                 'items' => i18n_str('Items'),
                 'collections' => i18n_str('Collections'),
                 'comments' => i18n_str('Comments'),
                 'categories' => i18n_str('Categories'),
                 'tags' => i18n_str('Tags'),
                 'imports' => i18n_str('Import / Export'),
                 'admin' => i18n_str('Administration') ];
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
            echo "<strong> Name: </strong>" . $user_data->data->user_nicename . "<br />";
            echo "<strong> User: </strong>" . $user_data->data->display_name . "<br />";
            echo "<strong> e-mail: </strong>" . $user_data->data->user_email . "<br />";

        }
    }
}