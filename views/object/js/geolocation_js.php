<script type="text/javascript">          
  var objs = $(".object_id");
  
  var ids = [];
  var locations = [],
          lats = [];
  $(objs).each(function(idx, el) {      
      ids[idx] = $(el).val();  
      
      var id = $(el).val();
      var lat =  $("#object_" + id + " .latitude").val();
      var long = $("#object_" + id + " .longitude").val();
      var title = $.trim( $("#object_" + id + " .item-display-title").text() );      
      
      locations[idx] = [title, lat, long];
      lats[idx] = parseFloat(lat);
  });  
  
  
  // var b = locations;
  cl( lats );
  cl( lats.sort(function(a,b) { return a -b; } ) );
  
  var quantos_que_eh = lats.length;
  
  console.log(quantos_que_eh);
    
    function initMap() {

    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 4,
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
  
  /*
  $.each( $('.cards-view-container'), function(idx, el) {
      cl( $(this).attr("id") );      
  });
  */
  
  // cl( $(".post_id").val() );
  
    function get_item_coordinates() {
        $.ajax({
            
                                               
        }).done(function(r){
            cl(r);
        });
    }
    // get_item_coordinates();
</script>   