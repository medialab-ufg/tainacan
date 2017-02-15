<?php
/**
 * Loads variables used along different collection's visualization's modes
 */
$viewHelper = new ViewHelper();
$objHelper = new ObjectHelper();

$countLine = 0;
$collection_list_mode = $collection_data['collection_metas']['socialdb_collection_list_mode'];
$_slideshow_time = $collection_data['collection_metas']['socialdb_collection_slideshow_time'];
$use_approx_mode = $collection_data['collection_metas']['socialdb_collection_use_prox_mode'];
$geo_loc = $collection_data['collection_metas']['socialdb_collection_location_meta'];

if( !$collection_list_mode ) {
    $collection_list_mode = "cards";
}

if("geolocation" === $collection_list_mode && is_null($geo_coordinates) && ($is_filtered_page || $pagid)) {
    $geo_coordinates["lat"] = get_post_meta($col_id, "socialdb_collection_latitude_meta", true);
    $geo_coordinates["long"] = get_post_meta($col_id, "socialdb_collection_longitude_meta", true);

    echo '<input type="hidden" id="filtered_collection" value="true" />';
}
$_fxd_meta = [
    'title' =>  $viewHelper->terms_fixed['title']->name, 
    'thumb' => $viewHelper->terms_fixed['thumbnail']->name
];
$numberItems = ceil($loop->found_posts / 10);
?>

<!-- TAINACAN: hidden utilizados para execucao de processos desta view (list.php)  -->
<input type="hidden" id="keyword_pagination" name="keyword_pagination" value="<?php if (isset($keyword)) echo $keyword; ?>" />
<input type="hidden" id="sorted_form" name="sorted_form" value="<?php echo $sorted_by; ?>" />
<input type="hidden" id="default-viewMode" value="<?php echo $collection_list_mode; ?>">
<input type="hidden" id="temp-viewMode" value="<?php echo $collection_list_mode; ?>">
<input type="hidden" id="slideshow-time" value="<?php echo $_slideshow_time; ?>">
<input type="hidden" id="set-lat" value="<?php echo $geo_coordinates["lat"]; ?>">
<input type="hidden" id="set-long" value="<?php echo $geo_coordinates["long"]; ?>">
<input type="hidden" id="approx_mode" value="<?php echo $use_approx_mode; ?>">
<input type="hidden" id="approx_location" value="<?php echo $geo_loc; ?>">
<input type="hidden" id="repo_fixed_title" value="<?php echo $_fxd_meta['title']; ?>">
<input type="hidden" id="repo_fixed_thumb" value="<?php echo $_fxd_meta['thumb']; ?>">
<input type="hidden" id="original_post_count" value="<?php echo $loop->post_count; ?>" />
<input type="hidden" id="pagination_current_page" value="<?php echo $pagid; ?>" />
