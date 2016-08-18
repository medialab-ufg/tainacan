<script type="text/javascript">
  var use_approx_mode = $("#approx_mode").val();
  var objs = $(".object_id");
  var ids = [],
    locations = [],
    lats = [],
    longs = [];   
    
    cl($(objs));
    
  // use metadata with approximated location   
  if(use_approx_mode == "use_approx_mode") {
      $(objs).each(function(idx, el) {      
        var id = $(el).val();
        ids[idx] = id;    

        var location = $("#object_" + id + " .location").val();
        var title = "<div class='col-md-12'>"+$.trim( $("#object_" + id ).html() )+"</div>";

        if(location) {
            cl("Me vê aqui as coordenadas de " + location + " ... ");    
            var search_url = "http://maps.google.com/maps/api/geocode/json?address=" + location + "&sensor=false";
            $.getJSON(search_url, function(data) {
                $.each( data.results, function( key, val ) {
                    var lt = val.geometry.location.lat;
                    var lng = val.geometry.location.lng;
                //items.push( "<li id='" + key + "'> Lat: " + lt + " ; Long:" + lng +" </li>" );
                cl('Ok ... tua latitude eh ' + lt  + ' e tua longitude eh ' + lng);
              });
            });        
        } else {
            // cl(id);    
        }

          if(lat && long) {
            locations[idx] = [title, lat, long];
            lats[idx] = parseFloat(lat);
            longs[idx] = parseFloat(long);
          }
      });
      
  } else {
      $(objs).each(function(idx, el) {      
        var id = $(el).val();
        ids[idx] = id;    

        var lat =  $("#object_" + id + " .latitude").val();
        var long = $("#object_" + id + " .longitude").val();
        var title = "<div class='col-md-12'>"+$.trim( $("#object_" + id ).html() )+"</div>";

          if(lat && long) {
            locations[idx] = [title, lat, long];
            lats[idx] = parseFloat(lat);
            longs[idx] = parseFloat(long);
          }
      });
    // use metadata configured for latitude and longitude
    var sorted_lats = lats.sort(function(a,b) { return a - b; } );
    var sorted_longs = longs.sort(function(a,b) { return a - b; } );
    var half_length = parseInt( locations.length / 2 );

      function initMap() {
        //document.getElementById('map').style.display = "block";
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: half_length,
          center: new google.maps.LatLng( sorted_lats[half_length] ,sorted_longs[half_length]),
          mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var infowindow = new google.maps.InfoWindow();
        var marker, i;

        for (i = 0; i < locations.length; i++) {
          if(locations[i]) {
            marker = new google.maps.Marker({
              position: new google.maps.LatLng(locations[i][1], locations[i][2]),
              map: map
            });

            google.maps.event.addListener(marker, 'click', (function (marker, i) {
              return function () {
                infowindow.setContent(locations[i][0]);
                infowindow.open(map, marker);
              };
            })(marker, i));   
          }
        } // for
    }

    initMap();
  } 

  
  
  /*
  if(lats && lats.length < 1 ) {
      $('.geolocation-view-container #map').hide();
      $('.geolocation-view-container .not-configured').show();
  } else {    
  */
    
  // }
  
</script>   