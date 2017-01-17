<script type="text/javascript">
  var use_approx_mode = $("#approx_mode").val();
  var objs = $(".object_id");
  var ids = [],
    lats = [],
    longs = [];   

  var marker_item = [];
  var tot = 0;
  var loaded = 0;

  var lat_long_count = 0;
  $(objs).each(function(idx, el) {
      var current_id = $(el).val();
      var current_content   = "<div class='col-md-12'>"+ $.trim( $("#object_" + current_id ).html() )+ "</div>";      

    if ( use_approx_mode && use_approx_mode === "use_approx_mode" ) {
        var current_location  = $("#object_" + current_id + " .location").val();

        if(current_location) {
            tot++;            
            var search_url = "http://maps.google.com/maps/api/geocode/json?address=" + current_location + "&sensor=false";
            $.getJSON(search_url, function(data) {
                $.each( data.results, function( key, val ) {
                    var lt = val.geometry.location.lat;
                    var lng = val.geometry.location.lng;
                                        
                    if (lt && lng) {
                        marker_item[idx] = [ current_content, lt, lng ];
                        lats[idx] = parseFloat(lt);
                        longs[idx] = parseFloat(lng);                        
                        
                        loaded++;
                        if(loaded === tot) {
                            initMap(); 
                        }                        
                    }
              });
            });
            
        }
        
        if(tot < 1) {
            $('.geolocation-view-container #map').hide();
            $('.geolocation-view-container .not-configured').show();
        }
        
    } else {
        var current_latitude  = $("#object_" + current_id + " .latitude").val();
        var current_longitude = $("#object_" + current_id + " .longitude").val();
      
        if(current_latitude && current_longitude) {
            /*
            marker_item[idx] = [ current_content, current_latitude, current_longitude ];
            lats[idx] = parseFloat(current_latitude);
            longs[idx] = parseFloat(current_longitude);
            */
            marker_item[lat_long_count] = [ current_content, current_latitude, current_longitude ];
            lats[lat_long_count] = parseFloat(current_latitude);
            longs[lat_long_count] = parseFloat(current_longitude);

            lat_long_count++;
        }        
    }      
  });
    
  var sorted_lats = lats.sort(function(a,b) { return a - b; } );
  var sorted_longs = longs.sort(function(a,b) { return a - b; } );
  var total_map_markers = marker_item.length;
  var half_length = parseInt( total_map_markers / 2 );

  if( 0 < total_map_markers && total_map_markers < 8 ) {
      half_length = total_map_markers+1;
  } else if ( half_length > 18 ) {
      half_length = 4;
  }

  function getAverageCoord(coord_arr, size) {
      var total_sum = 0.0;
      for(var i = 0; i < size; i++) {
          total_sum += parseFloat(coord_arr[i]);
      }

      return total_sum / size;
  }

  var medium_coords = {
      lat: getAverageCoord(sorted_lats, sorted_lats.length),
      long: getAverageCoord(sorted_longs, sorted_longs.length)
  };

  var ctr = new google.maps.LatLng( medium_coords.lat, medium_coords.long);

  function initMap() {
      if( total_map_markers > 0 ) {
          try {
              var map = new google.maps.Map(document.getElementById('map'), {
                  zoom: half_length, center: ctr, mapTypeId: google.maps.MapTypeId.ROADMAP
              });

              var infowindow = new google.maps.InfoWindow();
              var marker, i;
              var bounds = new google.maps.LatLngBounds();

              for (i = 0; i < total_map_markers; i++) {
                  if(marker_item[i]) {
                      marker = new google.maps.Marker({
                          position: new google.maps.LatLng(marker_item[i][1], marker_item[i][2]),
                          map: map
                      });

                      var loc = new google.maps.LatLng(marker.position.lat(), marker.position.lng());
                      bounds.extend(loc);

                      google.maps.event.addListener(marker, 'click', (function (marker, i) {
                          return function () {
                              infowindow.setContent(marker_item[i][0]);
                              infowindow.open(map, marker);
                          };
                      })(marker, i));
                  }
              } // for

              // Better auto zoom and auto center
              map.fitBounds(bounds);
              map.panToBounds(bounds);

              $("#center_pagination").hide();

          } catch(err) {
              console.log(err);
          }
      } else {
          if(tot < 1) {
              $('.geolocation-view-container #map').hide();
              $('.geolocation-view-container .error-map').show();
          }
      }

    }
    
    if ( use_approx_mode && use_approx_mode !== "use_approx_mode" ) {
        initMap();
    }
</script>   