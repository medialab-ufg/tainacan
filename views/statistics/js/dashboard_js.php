<script type="text/javascript">
google.charts.load('current', {'packages':['bar', 'line', 'corechart']}); //bar
//google.charts.setOnLoadCallback(drawGChart);

$(function () {
  fetchDashData("repo_searches", "Collections", "nofilter-dash");
  fetchDashData("profile", "Users", "nofilter");
  // Outros
});

// Animações
function refreshSpin(idBtnRefresh) {
    var animateClass = "gly-spin";
    
    $(idBtnRefresh).addClass(animateClass);

    setTimeout( function() {
      $(idBtnRefresh).removeClass( animateClass );
    }, 1000 );
}
//

// Início - Refresh usuário por perfil
$("#refresh-perfis-usuario").on("click", function profUserRefresh() {
  $("#gChart-perfis-usuario").html('');

  refreshSpin("#refresh-perfis-usuario");

  setTimeout(function() {
    fetchDashData("profile", "Users", "nofilter");
  }, 700);
});
// Fim - Refresh usuário por perfil

// Início - Refresh eventos
$("#refresh-eventos").on("click", function eventsRefresh() {
  $("#tbody-eventos").html('');

  refreshSpin("#refresh-eventos");

  setTimeout(function() {
    //fetchDashData();
  }, 700);
});
// Fim - Refresh eventos

// Início - Refresh buscas frequentes
$("#refresh-buscas-frequentes").on("click", function buscFreqRefresh() {
  $('#tbody-buscas-frequentes').html('');
  
  refreshSpin("#refresh-buscas-frequentes");
  
  setTimeout(function() {
    fetchDashData("repo_searches", "Collections", "nofilter-dash");
  }, 700);
});

function renderTBodyBFreq(rJSON) {
  for (var key in rJSON) {
     $('#tbody-buscas-frequentes').append(
        '<tr>'+ 
          '<td class="text-left">'+ rJSON[key][0] +'</td>'+ 
          '<td class="text-right">'+ rJSON[key][1] +'</td>'+
        '</tr>'
      );
  }
 
}
// Fim - Refresh buscas frequentes

function fetchDashData(action, parent, filter) {
  $.ajax({
    url: $(".stat_path").val() + '/controllers/log/log_controller.php', type: 'POST',
    data: { operation: 'user_events', parent: parent, event: action, from: "", to: "", collec_id: null, filter: filter }
  }).done(function(resp) {
    var rJSON = JSON.parse(resp);

    if( (rJSON.stat_object == null) || rJSON.stat_object.length == 0) {
      switch (action) {
        case "repo_searches":
          $('#tbody-buscas-frequentes').html('<h5 class="text-center"> <span class="glyphicon glyphicon-align-center glyphicon-exclamation-sign"></span> No data</h5>');
          break;
        case "profile":
          $('#gChart-perfis-usuario').html('<h5 class="text-center"> <span class="glyphicon glyphicon-align-center glyphicon-exclamation-sign"></span> No data</h5>');;
        break;
        default:
          break;
      }
    } else {
      switch (action) {
        case "repo_searches":
          setTimeout(function() {
            renderTBodyBFreq(rJSON.stat_object);
          }, 300);
          break;
        case "profile":
          setTimeout(function() {
            drawGChart("column", rJSON);
          }, 300);
          break;
        default:
          break;
      }
    }
  });
}

// Google Charts
function drawGChart(gChartType, dataJobj) {
  var gChartDataTable = new google.visualization.DataTable();

  if(gChartType == 'column'){
    gChartDataTable.addColumn('string', 'Perfis');
    gChartDataTable.addColumn('number', 'total');

    var objStats = dataJobj.stat_object;
    var objColumn = dataJobj.columns.events;

    for(var key in objStats){
      gChartDataTable.addRow([objColumn[key], objStats[key][1]]);
    }

    var columnGChart = new google.charts.Bar(document.getElementById('gChart-perfis-usuario'));
    
    var options = {
      legend: {position: 'none'},
    };

    columnGChart.draw(gChartDataTable, google.charts.Bar.convertOptions(options));
  }

}

</script>