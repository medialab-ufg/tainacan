<?php $_GOOGLE_API_KEY = "AIzaSyBZXPZcDMGeT-CDugrsYWn6D0PQSnq_odg"; ?>
<script src="http://maps.googleapis.com/maps/api/js?key=<?php echo $_GOOGLE_API_KEY; ?>&sensor=false&callback=initMap" defer></script>

<div class="geolocation-view-container" <?php if ($collection_list_mode != "geolocation"): ?> style="display: none;" <?php endif ?> >
  <div id="map" style="height: 500px; width: 100%;"></div>
  <div class="not-configured" style="display: none">
      <h5>
        <?php _e('This collection is not configured to use geolocation mode.', 'tainacan'); ?>    
      </h5>      
  </div>
</div>
