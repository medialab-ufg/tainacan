<script type="text/javascript">

$(function () {
  fetchDashData("repo_searches");
  // Outros
});

// Início - Refresh buscas frequentes
$("#refresh-buscas-frequentes").on("click", function buscFreqRefresh() {
  fetchDashData("repo_searches");
});

function renderTBodyBFreq(rJson) {

  $('#tbody-buscas-frequentes').html('');

  for (var key in rJson) {
     $('#tbody-buscas-frequentes').append(
        '<tr>'+ 
          '<td class="text-left">'+ rJson[key][0] +'</td>'+ 
          '<td class="text-right">'+ rJson[key][1] +'</td>'+
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
  console.log(resp)
  var rJson = JSON.parse(resp);

  if( (rJson.stat_object == null) || rJson.stat_object.length == 0) {
    $('#tbody-buscas-frequentes').html('<h5 class="text-center"> <span class="glyphicon glyphicon-align-center glyphicon-exclamation-sign"></span> No data</h5>');
  } else {
      setTimeout(function() {
        renderTBodyBFreq(rJson.stat_object);
      }, 300);
    }
  });
}
  
</script>