<script type="text/javascript">

$(function () {
  fetchDashData("repo_searches");
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

// Início - Refresh buscas frequentes
$("#refresh-buscas-frequentes").on("click", function buscFreqRefresh() {
  $('#tbody-buscas-frequentes').html('');
  
  refreshSpin("#refresh-buscas-frequentes");
  
  setTimeout(function() {
    fetchDashData("repo_searches");
  }, 900);
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

function fetchDashData(action) {
  $.ajax({
    url: $(".stat_path").val() + '/controllers/log/log_controller.php', type: 'POST',
    data: { operation: 'user_events', parent: "Collections", event: action, from: "", to: "", collec_id: null, filter: "nofilter" }
  }).done(function(resp) {

  var rJSON = JSON.parse(resp);

  if( (rJSON.stat_object == null) || rJSON.stat_object.length == 0) {
    $('#tbody-buscas-frequentes').html('<h5 class="text-center"> <span class="glyphicon glyphicon-align-center glyphicon-exclamation-sign"></span> No data</h5>');
  } else {
      setTimeout(function() {
        renderTBodyBFreq(rJSON.stat_object);
      }, 100);
    }
  });
}
  
</script>