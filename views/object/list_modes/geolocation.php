<div class="geolocation-view-container" <?php if ($collection_list_mode != "geolocation"): ?> style="display: none;" <?php endif ?> >
    <div id="map" style="height: 500px; width: 100%;"></div>

    <div id="map_filtered" style="height: 500px; width: 100%; display: none"></div>

  <div class="not-configured" style="display: none">
      <h5> <?php _t('This collection is not configured to use geolocation mode.', 1); ?> </h5>
  </div>
    <div class="error-map" style="display: none">
        <h5> <?php _t('No items have geographic location data in this listing.', 1); ?> </h5>
    </div>
</div>

<div id="approximated"></div>