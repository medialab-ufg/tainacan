<?php 
include_once('js/dashboard_js.php');
?>
<!-- DASHBOARD -->
<div class="container-fluid">
  <div class"row">
    
    <div id="dash-buscas-frequentes" class="bgc-widget col-md-4 table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th class="text-left">Buscas Frequentes</th>
            <th class="text-right">
              <a href="javascript:void(0)" id="refresh-buscas-frequentes" class="glyphicon glyphicon-refresh"></a>
            </th>
          </tr>
        </thead>
        <tbody id="tbody-buscas-frequentes">
          <!-- Conteúdo dinâmico, vindo de dashboard_js.php -->
        </tbody>
      </table>
    </div>
  
  </div>
</div>