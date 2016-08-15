<script type="text/javascript">          
  var objs = $(".object_id");
  var ids = [],
    locations = [],
    lats = [],
    longs = [];

  $(objs).each(function(idx, el) {      
      ids[idx] = $(el).val();  
      
      var id = $(el).val();
      var lat =  $("#object_" + id + " .latitude").val();
      var long = $("#object_" + id + " .longitude").val();
      var title = "<h5>" + $.trim( $("#object_" + id + " .item-display-title").html() ) + "</h5>" +
        "<p>" + $.trim( $("#object_" + id + " .item-description").text() ) + "</p>";

      if(lat && long) {
        locations[idx] = [title, lat, long];
        lats[idx] = parseFloat(lat);
        longs[idx] = parseFloat(long);
      }
  });

  var sorted_lats = lats.sort(function(a,b) { return a - b; } );
  var sorted_longs = longs.sort(function(a,b) { return a - b; } );
  var half_length = parseInt( locations.length / 2 );

    function initMap() {
      document.getElementById('map').style.display = "block";
      var map = new google.maps.Map(document.getElementById('map'), {
        zoom: half_length,
        center: new google.maps.LatLng( sorted_lats[half_length] ,sorted_longs[half_length]),
        mapTypeId: google.maps.MapTypeId.ROADMAP
      });

    var infowindow = new google.maps.InfoWindow();
    var marker, i;

    for (i = 0; i < locations.length; i++) {
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map
      });

      google.maps.event.addListener(marker, 'click', (function (marker, i) {
        return function () {
          infowindow.setContent(locations[i][0]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
  }
</script>   