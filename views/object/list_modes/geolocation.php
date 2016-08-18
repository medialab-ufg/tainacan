<div class="geolocation-view-container" <?php if ($collection_list_mode != "geolocation"): ?> style="display: none;" <?php endif ?> >
  <div id="map" style="height: 500px; width: 100%;"></div>
  <div class="not-configured" style="display: none">
      <h5>
        <?php _e('This collection is not configured to use geolocation mode.', 'tainacan'); ?>    
      </h5>      
  </div>
</div>

<div id="approximated"></div>

<script>
  $.getJSON( "http://maps.google.com/maps/api/geocode/json?address=Goiania&sensor=false", function( data ) {
    var items = [];
    $.each( data.results, function( key, val ) {
      items.push( "<li id='" + key + "'>" + val + "</li>" );
    });

    $( "<ul/>", { "class": "google-map-list", html: items.join( "" ) }).appendTo( "#approximated" );
  });
</script>