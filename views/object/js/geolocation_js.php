<script type="text/javascript">
  var use_approx_mode = $("#approx_mode").val();
  var objs = $(".object_id");
  var ids = [],
    lats = [],
    longs = [];   

  var marker_item = [];
  var tot = 0;
  var loaded = 0;
  
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
            marker_item[idx] = [ current_content, current_latitude, current_longitude ];
            lats[idx] = parseFloat(current_latitude);
            longs[idx] = parseFloat(current_longitude);
        }        
    }      
  });
    
    var sorted_lats = lats.sort(function(a,b) { return a - b; } );
    var sorted_longs = longs.sort(function(a,b) { return a - b; } );
    var half_length = parseInt( marker_item.length / 2 );

    function initMap() {     
        try{
            var map = new google.maps.Map(document.getElementById('map'), {
              zoom: half_length, center: new google.maps.LatLng( sorted_lats[half_length] ,sorted_longs[half_length]),
              mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            var infowindow = new google.maps.InfoWindow();
            var marker, i;

            for (i = 0; i < marker_item.length; i++) {
              if(marker_item[i]) {
                marker = new google.maps.Marker({
                  position: new google.maps.LatLng(marker_item[i][1], marker_item[i][2]),
                  map: map
                });

                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                  return function () {
                    infowindow.setContent(marker_item[i][0]);
                    infowindow.open(map, marker);
                  };
                })(marker, i));   
              }
            } // for
        }catch(err){
            console.log(err);
        }
    }
    
    if ( use_approx_mode && use_approx_mode !== "use_approx_mode" ) {
        initMap();
    }
</script>   