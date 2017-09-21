
<input type="hidden" name="single_object_id" id="single_object_id" value="<?php echo $object->ID; ?>" >
<input type="hidden" id="single_name" name="item_single_name" value="<?php echo $object->post_name; ?>" />
<input type="hidden" id="socialdb_permalink_object" name="socialdb_permalink_object" value="<?php echo get_the_permalink($collection_id) . '?item=' . $object->post_name; ?>" />
<ol class="breadcrumb item-breadcrumbs">
     <li> <a href="<?php echo site_url(); ?>"> Home </a> </li>
    <li> <a href="#" onclick="backToMainPageSingleItem()"> <?php echo get_post($collection_id)->post_title; ?> </a> </li>
    <li class="active"> <?php echo $object->post_title; ?> </li>

    <button data-title="<?php printf(__("URL of %s", "tainacan"), $object->post_title); ?>" id="iframebuttonObject" data-container="body"
            class="btn bt-default content-back pull-right" data-toggle="popoverObject" data-placement="left" data-content="">
        <span class="glyphicon glyphicon-link"></span>
    </button>
</ol>