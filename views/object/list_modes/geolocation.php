<?php
$_GOOGLE_API_KEY = "AIzaSyBZXPZcDMGeT-CDugrsYWn6D0PQSnq_odg";
?>

<script src="http://maps.googleapis.com/maps/api/js?key=<?php echo $_GOOGLE_API_KEY; ?>&sensor=false&callback=initMap" async defer></script>

<div id="map" style="height: 500px; width: 100%;">
</div>
<script type="text/javascript">
  var locations = [
    ['CALDAS NOVAS', -17.72472, -48.61, 2],
    ['gyn town', -16.6667,-49.2667, 1]
  ];

  function initMap() {

    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 8,
      center: new google.maps.LatLng(-16.6667,-49.2667),
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